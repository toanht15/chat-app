var express = require('express');
var router = express.Router();

var database = require('../database');
var api = require('../config');
var uuid = require('node-uuid');
var request = require('request');
var common = require('./common');
var list = require('./company_list');
var LandscapeAPI = require('./landscape');
var CogmoAttendAPICaller = require('./cogmo_attend');
var ChatLogTimeManager = require('./chat_log_time_manager');
var CommonUtil = require('./class/util/common_utility');
var HistoryManager = require('./class/manager/history_manager');
var CustomerInfoManager = require('./class/manager/customer_info_manager');

var DBConnector = require('./class/util/db_connector_util');
// log4js
var log4js = require('log4js'); // log4jsモジュール読み込み

log4js.configure('./log4js_setting.json'); // 設定ファイル読み込み

var reqlogger = log4js.getLogger('request'); // リクエスト用のロガー取得
var errlogger = log4js.getLogger('error'); // エラー用のロガー取得
var deblogger = log4js.getLogger('debug'); // デバッグ用のロガー取得
var scenarioLogger = log4js.getLogger('traceScenario');

//サーバインスタンス作成
var io = require('socket.io')(process.env.WS_PORT);
var SharedData = require('./shared_data');

// SharedData.sincloCoreオブジェクトからセッションIDを取得する関数
function getSessionId(siteKey, tabId, key) {
  if ((siteKey in SharedData.sincloCore) &&
      (tabId in SharedData.sincloCore[siteKey]) &&
      (key in SharedData.sincloCore[siteKey][tabId])) {
    return SharedData.sincloCore[siteKey][tabId][key];
  }
}

// SharedData.sincloCoreオブジェクトからチャットで関連するセッションID群を取得する関数
function getChatSessionIds(siteKey, sincloSessionId, key) {
  if ((siteKey in SharedData.sincloCore) &&
      (sincloSessionId in SharedData.sincloCore[siteKey]) &&
      (key in SharedData.sincloCore[siteKey][sincloSessionId])) {
    return SharedData.sincloCore[siteKey][sincloSessionId][key];
  }
}

function timeUpdate(historyId, obj, time, callback) {
  var insertStayData = {
    t_histories_id: historyId,
    title: ('title' in obj) ? obj.title : '',
    url: ('url' in obj) ? obj.url : '',
    stay_time: '',
    created: time,
    modified: time
  };

  DBConnector.getPool().query(
      'SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;',
      historyId,
      function(err, rows) {
        if (err !== null && err !== '') {
          callback(false);
          return false;
        } // DB接続断対応
        if (CommonUtil.isset(rows) && CommonUtil.isset(rows[0])) {
          // UPDATE
          var stayTime = CommonUtil.getDiffTime(rows[0].created, time);
          DBConnector.getPool().query(
              'UPDATE t_history_stay_logs SET stay_time = ? WHERE id = ?',
              [stayTime, rows[0].id],
              function(error, results, fields) {
              }
          );
        } else {
          rows[0] = {url: null};
        }
        DBConnector.getPool().query(
            'UPDATE t_histories SET out_date = ?, modified = ? WHERE id = ?',
            [time, time, historyId],
            function(error, results, fields) {
            }
        );

        if (insertStayData.url === '' || insertStayData.url === rows[0].url) {
          callback(rows[0].id);
          return true;
        }
        DBConnector.getPool().
            query('INSERT INTO t_history_stay_logs SET ?', insertStayData,
            function(error, results, fields) {
              callback(results.insertId);
              return true;
            }
        );
      }
  );
}

function makeToken() {
  var n = 20,
      str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQESTUVWXYZ1234567890',
      strLen = str.length,
      token = '';
  for (var i = 0; i < n; i++) {
    token += str[Math.floor(Math.random() * strLen)];
  }
  return token;
};

function getCompanyList(forceReload) {
  DBConnector.getPool().query('select * from m_companies where del_flg = 0;',
      function(err, rows) {
        if (err !== null && err !== '') return false; // DB接続断対応
        var key = Object.keys(rows);
        for (var i = 0; key.length > i; i++) {
          var row = rows[key[i]];
          companyList[row.company_key] = row.id;
          list.laSessionCounter.setMaxCount(row.company_key,
              row.la_limit_users);
          list.functionManager.set(row.company_key, row.core_settings);
          if (!(row.company_key in list.customerList)) {
            console.log('new list.customerList : ' + row.company_key);
            list.customerList[row.company_key] = {};
          }
          if (initialized && !(row.company_key in common.companySettings)) {
            console.log('LOAD NEW COMPANY SETTINGS : ' + row.company_key);
            common.reloadSettings(row.company_key);
          }
        }
        initialized = true;
        if (forceReload) {
          common.reloadSettings();
        }
      });
}

list.getCompanyList();

function syncStopCtrl(siteKey, tabId, unsetFlg) {
  var keys = [
    'connectToken', 'syncSessionId', 'syncHostSessionId', 'sdHistoryId',
    'syncFrameSessionId', 'shareWindowId', 'shareWindowFlg'
  ];
  // 画面同期の記録
  var sdHistoryId = getSessionId(siteKey, tabId, 'sdHistoryId');
  if (sdHistoryId) {
    var now = CommonUtil.formatDateParse();
    db.timeUpdateToDisplayShare(now, sdHistoryId);
  }

  if (unsetFlg) { // unsetTarget
    var sessionId = getSessionId(siteKey, tabId, 'sessionId');
    clearTimeout(SharedData.sincloCore[siteKey][tabId].timeoutTimer);
    delete SharedData.sincloCore[siteKey][tabId];
    delete connectList[sessionId];
    return false;
  }

  for (var i = 0; keys.length > i; i++) {
    if (getSessionId(siteKey, tabId, keys[i])) {
      delete SharedData.sincloCore[siteKey][tabId][keys[i]];
    }
  }

  for (var key in list.customerList[siteKey]) {
    if (tabId.indexOf(list.customerList[siteKey][key]['tabId']) === 0) {
      for (var i = 0; keys.length > i; i++) {
        delete list.customerList[siteKey][key][keys[i]];
      }
      break;
    }
  }
}

function coBrowseStopCtrl(siteKey, tabId, unsetFlg) {
  var keys = [
    'coBrowseConnectToken',
    'shareCoBrowseFlg',
    'syncHostSessionId',
    'laShortCode',
    'responderId',
    'coBrowseParentSessionId'
  ];
  // 画面同期の記録 FIXME
  /*
  var sdHistoryId = getSessionId(siteKey, tabId, "sdHistoryId");
  if ( sdHistoryId ) {
    var now = CommonUtil.formatDateParse();
    db.timeUpdateToDisplayShare(now, sdHistoryId);
  }
  */

  if (unsetFlg) { // unsetTarget
    var sessionId = getSessionId(siteKey, tabId, 'sessionId');
    clearTimeout(SharedData.sincloCore[siteKey][tabId].timeoutTimer);
    delete SharedData.sincloCore[siteKey][tabId];
    delete connectList[sessionId];
    return false;
  }

  for (var i = 0; keys.length > i; i++) {
    if (getSessionId(siteKey, tabId, keys[i])) {
      delete SharedData.sincloCore[siteKey][tabId][keys[i]];
    }
  }

  for (var key in list.customerList[siteKey]) {
    if (tabId.indexOf(list.customerList[siteKey][key]['tabId']) === 0) {
      for (var i = 0; keys.length > i; i++) {
        delete list.customerList[siteKey][key][keys[i]];
      }
      break;
    }
  }
  list.laSessionCounter.countDown(siteKey, tabId);
}

function getOperatorCnt(siteKey) {
  var cnt = 0;
  if (CommonUtil.isset(SharedData.activeOperator[siteKey])) {
    var key = Object.keys(SharedData.activeOperator[siteKey]);
    cnt = key.length;
  }
  return cnt;
}

function getConnectInfo(o) {
  var connectToken = getSessionId(o.siteKey, o.tabId, 'connectToken');
  var coBrowseConnectToken = getSessionId(o.siteKey, o.tabId,
      'coBrowseConnectToken');
  var responderId = getSessionId(o.siteKey, o.tabId, 'responderId');
  var chatUserId = getSessionId(o.siteKey, o.tabId, 'chat');
  if (CommonUtil.isset(responderId) && CommonUtil.isset(connectToken)) {
    o.responderId = responderId;
    o.connectToken = connectToken;
  } else {
    delete o['connectToken'];
    if (!CommonUtil.isset(coBrowseConnectToken)) {
      delete o['responderId'];
    }
  }
  if (CommonUtil.isset(responderId) && CommonUtil.isset(coBrowseConnectToken)) {
    o.responderId = responderId;
    o.coBrowseConnectToken = coBrowseConnectToken;
  } else {
    delete o['coBrowseConnectToken'];
    if (!CommonUtil.isset(connectToken)) {
      delete o['responderId'];
    }
  }
  if (CommonUtil.isset(chatUserId)) {
    o.chat = chatUserId;
  } else {
    o.chat = null;
  }
  var docShareId = getSessionId(o.siteKey, o.tabId, 'docShareId');
  if (CommonUtil.isset(docShareId)) {
    o.docShareId = docShareId;
  } else {
    delete o['docShareId'];
  }
  var sincloSessionId = getSessionId(o.siteKey, o.tabId, 'sincloSessionId');
  if (CommonUtil.isset(sincloSessionId)) {
    o.sincloSessionId = sincloSessionId;
  } else {
    o.sincloSessionId = null;
  }
  var orgName = getSessionId(o.siteKey, o.tabId, 'orgName');
  if (CommonUtil.isset(orgName)) {
    o.orgName = orgName;
  } else {
    o.orgName = '';
  }
  var lbcCode = getSessionId(o.siteKey, o.tabId, 'lbcCode');
  if (CommonUtil.isset(lbcCode)) {
    o.lbcCode = lbcCode;
  } else {
    o.lbcCode = '';
  }
  return o;
}

// IPアドレスの取得
function getIp(socket) {
  var ip = '0.0.0.0';
  if (CommonUtil.isset(socket.handshake.headers['x-forwarded-for'])) {
    ip = socket.handshake.headers['x-forwarded-for'];
  }
  return ip;
}

function getMessageTypeBySenarioActionType(type) {
  var result = 3;
  switch (Number(type)) {
    case 1:
    case 10:
      result = 21;
      break;
    case 2:
      result = 22;
      break;
    case 3:
      result = 23;
      break;
    case 7:
      result = 27;
      break;
  }
  return result;
}

function getMessageTypeByUiType(type) {
  var result = 22;
  switch (Number(type)) {
    case 1:
      result = 22;
      break;
    case 2:
      result = 22;
      break;
    case 3:
      result = 55;
      break;
    case 4:
      result = 41;
      break;
    case 5:
      result = 42;
      break;
    case 6:
      result = 45;
      break;
    case 7:
      result = 46;
      break;
    case 8:
      result = 49;
      break;
    case 9:
      result = 52;
      break;
    default:
      result = 22;
  }
  return result;
}

// Landscapeの企業情報取得
//requestをrequire
var http = require('http');
http.globalAgent.maxSockets = 1000;

function getCompanyInfoFromApi(obj, ip, callback) {
  if (list.functionManager.isEnabled(obj.siteKey,
      list.functionManager.keyList.refCompanyData)) {
    var api = new LandscapeAPI('json', 'utf8');
    api.getFrom(ip, callback);
  } else {
    deblogger.debug('refCompanyData is false. siteKey : ' + obj.siteKey);
    callback({});
  }
}

function parseSignature(src, ip, callback) {
  //ヘッダーを定義
  var headers = {
    'Content-Type': 'application/json'
  };

  //オプションを定義
  var options = {
    host: process.env.PARSE_SIGNATURE_API_HOST,
    port: process.env.PARSE_SIGNATURE_API_PORT,
    path: process.env.PARSE_SIGNATURE_API_PATH,
    method: 'POST',
    headers: headers,
    json: true,
    agent: false
  };

  if (process.env.DB_HOST === 'localhost') {
    options.rejectUnauthorized = false;
  }

  //リクエスト送信
  var req = http.request(options, function(response) {
    if (response.statusCode === 200) {
      response.setEncoding('utf8');
      response.on('data', callback);
      return;
    } else {
      console.log('企業詳細情報取得時にエラーが返却されました。 errorCode : ' + response.statusCode);
      callback(false);
      return;
    }
  });

  req.on('error', function(error) {
    console.log('企業詳細情報取得時にHTTPレベルのエラーが発生しました。 message : ' + error.message);
    callback(false);
    return;
  });

  req.write(JSON.stringify({
    'accessToken': 'x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK',
    'targetText': src,
    'ip': ip
  }));
  req.end();
}

function sendMail(autoMessageId, lastChatLogId, callback) {
  //ヘッダーを定義
  var headers = {
    'Content-Type': 'application/json'
  };

  //オプションを定義
  var options = {
    host: process.env.SEND_AUTO_MESSAGE_MAIL_API_HOST,
    port: process.env.SEND_AUTO_MESSAGE_MAIL_API_PORT,
    path: process.env.SEND_AUTO_MESSAGE_MAIL_API_PATH,
    method: 'POST',
    headers: headers,
    json: true,
    agent: false
  };

  if (process.env.DB_HOST === 'localhost') {
    options.rejectUnauthorized = false;
  }

  //リクエスト送信
  var req = http.request(options, function(response) {
    if (response.statusCode === 200) {
      response.setEncoding('utf8');
      response.on('data', callback);
      return;
    } else {
      console.log(
          'トリガー設定メール通知時にエラーが返却されました。 errorCode : ' + response.statusCode);
      callback(false);
      return;
    }
  });

  req.on('error', function(error) {
    console.log(
        'トリガー設定メール通知時にHTTPレベルのエラーが発生しました。 message : ' + error.message);
    callback(false);
    return;
  });

  req.write(JSON.stringify({
    'accessToken': 'x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK',
    'autoMessageId': autoMessageId,
    'lastChatLogId': lastChatLogId
  }));
  req.end();
}

function sendSenarioMail(obj, callback) {
  //ヘッダーを定義
  var headers = {
    'Content-Type': 'application/json'
  };

  //オプションを定義
  var options = {
    host: process.env.SEND_SCENARIO_MESSAGE_MAIL_API_HOST,
    port: process.env.SEND_SCENARIO_MESSAGE_MAIL_API_PORT,
    path: process.env.SEND_SCENARIO_MESSAGE_MAIL_API_PATH,
    method: 'POST',
    headers: headers,
    json: true,
    agent: false
  };

  if (process.env.DB_HOST === 'localhost') {
    options.rejectUnauthorized = false;
  }

  //リクエスト送信
  var req = http.request(options, function(response) {
    if (response.statusCode === 200) {
      response.setEncoding('utf8');
      response.on('data', callback);
      return;
    } else {
      console.log('シナリオメール送信時にエラーが返却されました。 errorCode : ' + response.statusCode);
      callback(false);
      return;
    }
  });

  req.on('error', function(error) {
    console.log('シナリオメール送信時にHTTPレベルのエラーが発生しました。 message : ' + error.message);
    callback(false);
    return;
  });

  var historyId = CommonUtil.isset(
      getSessionId(obj.siteKey, obj.tabId, 'historyId')) ?
      getSessionId(obj.siteKey, obj.tabId, 'historyId') :
      obj.historyId;
  req.write(JSON.stringify({
    'accessToken': 'x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK',
    'userHistoryId': historyId,
    'mailType': obj.mailType,
    'transmissionId': obj.transmissionId,
    'templateId': obj.templateId,
    'withDownloadURL': obj.withDownloadURL,
    'variables': obj.variables
  }));
  req.end();
}

// emit用
var emit = {
  roomKey: {
    client: 'cl001',
    company: 'cm001',
    frame: 'cf001'
  },
  _convert: function(d) {
    if (typeof (d) === 'object') {
      return JSON.stringify(d);
    } else {
      return d;
    }
  },
  toMine: function(ev, d, s) { // 送り主に返信（そのタブのみ）
    var obj = this._convert(d);
    return s.emit(ev, obj);
  },
  toUser: function(ev, d, sId) { // 対象ユーザーに送信(sId = the session id)
    var obj = this._convert(d);
    if (!CommonUtil.isset(sId)) return false;
    if (!CommonUtil.isset(io.sockets.connected[sId])) return false;
    return io.sockets.connected[sId].emit(ev, obj);
  },
  toSameUser: function(ev, d, siteKey, sessionId) { // 対象ユーザーに送信(sId = the session id)
    var obj = this._convert(d);
    var sessionIds = getChatSessionIds(siteKey, sessionId, 'sessionIds');
    if (!CommonUtil.isset(sessionIds)) return false;
    var result = false;
    for (var index in sessionIds) {
      if (!CommonUtil.isset(io.sockets.connected[sessionIds[index]])) continue;
      io.sockets.connected[sessionIds[index]].emit(ev, obj);
    }
  },
  toClient: function(ev, d, rName) { // 対象企業を閲覧中のユーザーに送信(rName = the room's name)
    var obj = this._convert(d);
    if (!CommonUtil.isset(rName)) return false;
    return io.sockets.in(rName + this.roomKey.client).emit(ev, obj);
  },
  toCompany: function(ev, d, rName) { // 対象企業にのみ送信(rName = the room's name)
    var obj = this._convert(d);
    if (!CommonUtil.isset(rName)) return false;
    return io.sockets.in(rName + this.roomKey.company).emit(ev, obj);
  }
};

// socket再接続
function sincloReconnect(socket) {
  emit.toMine('sincloReconnect', {}, socket);
}

//応対件数カウント
function getConversationCountUser(visitors_id, callback) {
  DBConnector.getPool().query(
      'SELECT conversation_count FROM t_conversation_count WHERE visitors_id = ?',
      [visitors_id], function(err, results) {
        if (CommonUtil.isset(err)) {
          console.log(
              'RECORD SElECT ERROR: t_conversation_count(conversation_count):' +
              err);
          callback(null);
          return;
        } else {
          callback(results);
        }
      });
}

//待機中オペレータ情報登録
function addChatActiveUser(t_history_chat_logs_id, m_users_id, siteKey) {
  DBConnector.getPool().query(
      'INSERT INTO t_history_chat_active_users(t_history_chat_logs_id,m_companies_id,m_users_id,created) VALUES(?,?,?,?)',
      [
        t_history_chat_logs_id,
        list.companyList[siteKey],
        m_users_id,
        new Date()],
      function(err, results) {
        if (CommonUtil.isset(err)) {
          console.log(
              'RECORD INSERT ERROR: t_history_chat_active_users:' + err);
        }
      });
}

var callback = function(err, results) {
  if (CommonUtil.isset(err)) {
    console.log(
        'RECORD SElECT ERROR: t_conversation_count(conversation_count):' + err);
    return false;
  }
  callback();
};

var db = {
  addDisplayShareHistory: function(responderId, obj) {
    if (CommonUtil.isset(obj.siteKey)) {
      var tabId = (obj.subWindow) ?
          CommonUtil.trimFrame(obj.to) :
          CommonUtil.trimFrame(obj.tabId);
      if (!(tabId in SharedData.sincloCore[obj.siteKey])) return false;
      if (!(SharedData.sincloCore[obj.siteKey][tabId].hasOwnProperty(
          'historyId'))) return false;
      var historyId = getSessionId(obj.siteKey, tabId, 'historyId');
      var sdHistoryId = getSessionId(obj.siteKey, tabId, 'sdHistoryId');
      var now = CommonUtil.formatDateParse();
      // 登録処理
      if (!sdHistoryId) {
        //insert
        var insertData = {
          t_histories_id: historyId,
          m_users_id: responderId,
          start_time: now,
          finish_time: now
        };
        DBConnector.getPool().
            query('INSERT INTO t_history_share_displays SET ?', insertData,
            function(error, results, fields) {
              if (error !== null && error !== '') return false; // DB接続断対応
              SharedData.sincloCore[obj.siteKey][tabId].sdHistoryId = results.insertId;
            }
        );
      }
      // 更新処理
      else {
        this.timeUpdateToDisplayShare(now, sdHistoryId);
      }

    }
  },
  timeUpdateToDisplayShare: function(now, sdHistoryId) {
    // 渡されたIDをもとに検索
    DBConnector.getPool().
        query('SELECT * FROM t_history_share_displays WHERE id = ?;',
        [sdHistoryId], function(err, rows) {
          if (err !== null && err !== '') return false; // DB接続断対応
          // データが見つかった場合
          if (CommonUtil.isset(rows) && CommonUtil.isset(rows[0])) {
            // アップデートする
            DBConnector.getPool().query(
                'UPDATE t_history_share_displays SET finish_time = ? WHERE id = ?',
                [now, sdHistoryId],
                function(error, results, fields) {
                }
            );
          }
        });
  },
  addHistory: function(obj, s) {
    if (CommonUtil.isset(obj.tabId) && CommonUtil.isset(obj.siteKey)) {
      if (!CommonUtil.isset(list.companyList[obj.siteKey]) ||
          obj.subWindow) return false;
      var siteId = list.companyList[obj.siteKey];
      DBConnector.getPool().query(
          'SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;',
          [siteId, obj.sincloSessionId || obj.tabId, obj.userId],
          function(err, rows) {
            if (err !== null && err !== '') return false; // DB接続断対応
            var now = CommonUtil.formatDateParse();

            if (CommonUtil.isset(rows) && CommonUtil.isset(rows[0])) {
              if (CommonUtil.isset(obj.sincloSessionId)) {
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].historyId = rows[0].id;
              }
              SharedData.sincloCore[obj.siteKey][obj.tabId].historyId = rows[0].id;
              timeUpdate(rows[0].id, obj, now, function(stayLogsId) {
                obj.historyId = rows[0].id;
                obj.stayLogsId = stayLogsId;
                emit.toMine('setHistoryId', obj, s);
              });
            } else {
              //insert
              var insertData = {
                m_companies_id: siteId,
                visitors_id: obj.userId,
                tab_id: obj.sincloSessionId || obj.tabId,
                ip_address: obj.ipAddress,
                user_agent: obj.userAgent,
                access_date: CommonUtil.formatDateParse(obj.time),
                referrer_url: obj.referrer,
                created: now,
                modified: now
              };

              DBConnector.getPool().
                  query('INSERT INTO t_histories SET ?', insertData,
                  function(error, results, fields) {
                    if (error && (error !== null && error !== '')) return false; // DB接続断対応
                    var historyId = results.insertId;
                    if (CommonUtil.isset(obj.sincloSessionId)) {
                      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].historyId = historyId;
                    }
                    SharedData.sincloCore[obj.siteKey][obj.tabId].historyId = historyId;
                    timeUpdate(historyId, obj, now, function(stayLogsId) {
                      obj.historyId = historyId;
                      obj.stayLogsId = stayLogsId;
                      emit.toMine('setHistoryId', obj, s);
                    });
                  }
              );
            }
          });
    }
  },
  /**
   * 顧客情報を更新する
   * @param obj
   * @param s
   */
  upsertCustomerInfo: function(obj, s, callback) {
    if (CommonUtil.isset(obj.customVariables)) {
      var customVariables = obj.customVariables;
      var found = false;
      var customerInfo = (CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) &&
          CommonUtil.isset(
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo)) ?
          SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo :
          {};
      if (Object.keys(customVariables).length !== 0) {
        Object.keys(customVariables).forEach(function(elm, index, arr) {
          if (CommonUtil.isset(customVariables[elm]) && customVariables[elm] !==
              customerInfo[elm]) {
            found = true;
          }
        });
      }
      if (found) {
        DBConnector.getPool().query(
            'SELECT * from m_customers where m_companies_id = ? AND visitors_id = ? order by id desc',
            [list.companyList[obj.siteKey], obj.userId], function(err, row) {
              if (err !== null && err !== '') {
                callback(false);
                return false;
              } // DB接続断対応

              var currentData = {};
              if (CommonUtil.isset(row) && CommonUtil.isset(row[0])) {
                currentData = JSON.parse(row[0].informations);
                Object.keys(customVariables).forEach(function(key, idx, array) {
                  if (CommonUtil.isset(customVariables[key]) &&
                      customVariables[key] !==
                      '') {
                    currentData[key] = customVariables[key];
                  }
                });
                DBConnector.getPool().query(
                    'UPDATE m_customers set informations = ? where id = ? ',
                    [JSON.stringify(currentData), row[0].id],
                    function(err, result) {
                      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo = currentData;
                      if (callback) callback({
                        userId: obj.userId,
                        data: JSON.stringify(currentData)
                      });
                    });
              } else {
                DBConnector.getPool().query(
                    'INSERT INTO m_customers VALUES (NULL, ?, ?, ?, now(), 0, NULL, NULL, NULL, NULL)',
                    [
                      list.companyList[obj.siteKey],
                      obj.userId,
                      JSON.stringify(customVariables)], function(err, result) {
                      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo = customVariables;
                      if (callback) callback({
                        userId: obj.userId,
                        data: JSON.stringify(customVariables)
                      });
                    });
              }
            });
      } else {
        callback(false);
      }
    } else {
      callback(false);
    }
  },

  /*  リード情報を登録する
   *  TODO 適切なエラーハンドリング
   */

  addLeadInformation: function(obj) {
    DBConnector.getPool().query(
        'INSERT INTO t_lead_lists VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, now())', [
          list.companyList[obj.siteKey],
          Number(obj.leadSettingsId),
          Number(obj.scenarioId),
          JSON.stringify(obj.dataSet),
          obj.landingUrl,
          obj.executeUrl,
          obj.userAgent],
        function(err, result) {
          if (CommonUtil.isset(err)) {
            console.log('ERROR DETECTED!!!' + err);
          }
        });
  }
};

var console = {
  date: function() {
    var d = new Date();
    return d.getFullYear() + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' +
        ('0' + d.getDate()).slice(-2) + ' ' + ('0' + d.getHours()).slice(-2) +
        ':' + ('0' + d.getMinutes()).slice(-2) + ':' +
        ('0' + d.getSeconds()).slice(-2);
  },
  log: function(a, b) {
    var label = null, data = null, d = this.date();
    switch (typeof b) {
      case 'object':
        var data = '【' + a + '|' + d + '】 ' + JSON.stringify(b, null, '\t');
        break;
      case 'string':
        var data = '【' + a + '|' + d + '】 ' + b;
        break;
      default:
        var data = '【' + d + '】 ' + JSON.stringify(a, null, '\t');
    }
    reqlogger.info(data);
  }
};

//接続確立時の処理
io.sockets.on('connection', function(socket) {

  // チャット用
  var chatApi = {
    cnst: {
      observeType: {
        company: 1,
        customer: 2,
        auto: 3,
        sorry: 4,
        autoSpeech: 5,
        notification: 7,
        start: 998,
        end: 999
      },
      requestFlg: {
        noFlg: 0
      },
      inFlg: {
        flg: 1
      }
    },
    set: function(d, noReturnSelfMessage) { // メッセージが渡されてきたとき
      if (!getSessionId(d.siteKey, d.tabId, 'sessionId')) {
        sincloReconnect(socket);
        return false;
      }
      // 履歴idか(入退室以外に)メッセージがない
      if (!getSessionId(d.siteKey, d.sincloSessionId, 'historyId') ||
          (!CommonUtil.isset(d.chatMessage) &&
              !(chatApi.cnst.observeType.start && d.messageType ||
                  chatApi.cnst.observeType.end && d.messageType))) {
        // エラーを渡す
        return emit.toUser('sendChatResult', {
          ret: false,
          messageType: d.messageType,
          tabId: d.tabId,
          siteKey: d.siteKey
        }, d.siteKey);
      }
      // チャットidがある
      else {
        // DBへ書き込む
        this.commit(d, noReturnSelfMessage);
      }
    },
    get: function(obj) { // 最初にデータを取得するとき
      var chatData = {historyId: null, messages: []};
      var historyId = getSessionId(obj.siteKey, obj.tabId, 'historyId');
      if (historyId) {
        chatData.historyId = historyId;

        var sql = 'SELECT';
        sql += ' chat.id, chat.id as chatId, chat.message, chat.message_type as messageType, chat.message_distinction as messageDistinction,chat.achievement_flg as achievementFlg,chat.delete_flg as deleteFlg, chat.visitors_id as visitorsId,chat.m_users_id as userId, mu.display_name as userName, chat.message_read_flg as messageReadFlg,chat.notice_flg as noticeFlg, chat.hide_flg, chat.created ';
        sql += 'FROM t_history_chat_logs AS chat ';
        sql += 'LEFT JOIN m_users AS mu ON ( mu.id = chat.m_users_id ) ';
        sql += 'WHERE t_histories_id = ? AND chat.hide_flg = 0 ORDER BY created';

        DBConnector.getPool().
            query(sql, [chatData.historyId], function(err, rows) {
          if (err !== null && err !== '') return false; // DB接続断対応
          var messages = (CommonUtil.isset(rows)) ? rows : [];
          var setList = {};
          for (var i = 0; i < messages.length; i++) {
            var chatMessageDate = messages[i].created;
            chatMessageDate = new Date(chatMessageDate);
            messages[i].sort = CommonUtil.fullDateTime(chatMessageDate);
            // if ( ('userName' in messages[i]) && obj.showName !== 1 ) {
            //   delete messages[i].userName;
            // }
            setList[CommonUtil.fullDateTime(chatMessageDate)] = messages[i];
          }
          var autoMessages = [];
          if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
              'autoMessages' in
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
            var autoMessageObj = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages;
            try {
              Object.keys(autoMessageObj).
                  forEach(function(automessageKey, index, array) {
                    autoMessages.push(autoMessageObj[automessageKey]);
                  });
            } catch (e) {

            }
          }
          for (var j = 0; j < autoMessages.length; j++) {
            var autoMessageDate = autoMessages[j].created;
            autoMessageDate = new Date(autoMessageDate);
            // if ( ('userName' in autoMessages[i]) && obj.showName !== 1 ) {
            //   delete autoMessages[i].userName;
            // }
            setList[CommonUtil.fullDateTime(autoMessageDate) +
            '_'] = autoMessages[j];
          }
          var scenarioMessages = [];
          if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
              'scenario' in
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
            var scenariosObj = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].scenario;
            Object.keys(scenariosObj).forEach(function(scenarioId, index, arr) {
              var scenarioObj = scenariosObj[Number(scenarioId)];
              Object.keys(scenarioObj).
                  forEach(function(sequenceId, index2, arr2) {
                    var sequenceObj = scenarioObj[sequenceId];
                    Object.keys(sequenceObj).
                        forEach(function(categoryId, idx, array) {
                          scenarioMessages.push(sequenceObj[categoryId]);
                        });
                  });
            });
          }
          for (var k = 0; k < scenarioMessages.length; k++) {
            var scenarioDate = scenarioMessages[k].created;
            scenarioDate = new Date(scenarioDate);
            setList[CommonUtil.fullDateTime(scenarioDate) +
            '_'] = scenarioMessages[k];
          }
          var diagram = [];
          if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
              'diagram' in
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
            var diagramArray = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram;
            try {
              diagram = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram;
            } catch (e) {

            }
          }
          for (var l = 0; l < diagram.length; l++) {
            var diagramDate = diagram[l].created;
            diagramDate = new Date(diagramDate);
            // if ( ('userName' in autoMessages[i]) && obj.showName !== 1 ) {
            //   delete autoMessages[i].userName;
            // }
            setList[CommonUtil.fullDateTime(diagramDate) + '_'] = diagram[l];
          }
          chatData.messages = CommonUtil.objectSort(setList);
          obj.chat = chatData;
          console.log(chatData);
          emit.toMine('chatMessageData', obj, socket);
        });
      } else {
        obj.chat = chatData;
        emit.toMine('chatMessageData', obj, socket);
      }
    },
    commit: function(d, noReturnSelfMessage) { // DBに書き込むとき
      var insertData = {
        t_histories_id: SharedData.sincloCore[d.siteKey][d.sincloSessionId].historyId,
        m_companies_id: list.companyList[d.siteKey],
        visitors_id: d.userId,
        m_users_id: d.mUserId,
        message: (d.isDiagramMessage) ? d.chatMessage.message : d.chatMessage,
        message_type: d.messageType,
        message_distinction: d.messageDistinction,
        message_request_flg: d.messageRequestFlg,
        notice_flg: 0,
        achievement_flg: d.achievementFlg
      };

      DBConnector.getPool().query(
          'SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;',
          insertData.t_histories_id,
          function(err, rows) {
            if (err !== null && err !== '') return false; // DB接続断対応
            if (rows && rows[0]) {
              insertData.t_history_stay_logs_id = CommonUtil.isset(
                  d.stayLogsId) ?
                  d.stayLogsId :
                  rows[0].id;
            }
            insertData.created = (('created' in d)) ?
                new Date(d.created) :
                new Date();

            // オートメッセージとシナリオとチャットツリーの場合は既読
            if (Number(insertData.message_type === 3) ||
                Number(insertData.message_type === 22) ||
                Number(insertData.message_type === 40) ||
                Number(insertData.message_type === 23)) {
              insertData.message_read_flg = 1;
              insertData.message_request_flg = chatApi.cnst.requestFlg.noFlg;
              insertData.message_distinction = d.messageDistinction;
            } else if (((Number(insertData.message_type) === 1 ||
                Number(insertData.message_type) === 303) &&
                d.hasOwnProperty('notifyToCompany') && !d.notifyToCompany) ||
                Number(insertData.message_type) === 12 ||
                Number(insertData.message_type) === 13) {
              // サイト訪問者からのチャットで通知しない場合は既読にする
              insertData.message_read_flg = 1;
              insertData.message_distinction = d.messageDistinction;
            }

            DBConnector.getPool().
                query('INSERT INTO t_history_chat_logs SET ?', insertData,
                function(error, results) {
                  chatApi._handleInsertData(error, results, d,
                      noReturnSelfMessage, insertData);
                });
          }
      );
    },
    _handleInsertData: function(
        error, results, d, noReturnSelfMessage, insertData) {
      try {
        if (d.messageType === 1
            || d.messageType === 12
            || d.messageType === 13
            || d.messageType === 19
            || (d.messageType >= 30 && d.messageType <= 39)
            || d.messageType === 43
            || d.messageType === 44
            || d.messageType === 47
            || d.messageType === 48
            || d.messageType === 50
            || d.messageType === 51
            || d.messageType === 53
            || d.messageType === 54) {
          // DEBUG: サイト訪問者側メッセージのログ出力
          console.log('customer message', {
            siteKey: d.siteKey,
            ssId: d.sincloSessionId,
            tabId: d.tabId,
            message: d.chatMessage,
            type: d.messageType
          });
        }
      } catch (e) {

      }
      if (!CommonUtil.isset(error)) {
        let messageTimeType = 1;
        if (Number(insertData.message_request_flg) === 1) {
          messageTimeType = 2;
        } else if (d.messageType === 1 || d.messageType === 8) {
          messageTimeType = 2;
        }
        ChatLogTimeManager.saveTime(results.insertId, insertData.t_histories_id,
            messageTimeType, insertData.created);
        if (!CommonUtil.isset(
            SharedData.sincloCore[d.siteKey][d.tabId].sessionId)) return false;
        var sendData = {
          tabId: d.tabId,
          chatId: results.insertId,
          messageType: d.messageType,
          created: insertData.created,
          ret: true,
          chatMessage: d.chatMessage,
          siteKey: d.siteKey,
          matchAutoSpeech: !d.notifyToCompany,
          isScenarioMessage: d.isScenarioMessage,
          hideMessage: noReturnSelfMessage ? noReturnSelfMessage : false,
          shownMessage: CommonUtil.isset(d.shownMessage) ?
              d.shownMessage :
              false
        };

        // 担当者のいない消費者からのメッセージの場合
        if ((d.messageType === 1 &&
            !getChatSessionIds(d.siteKey, d.sincloSessionId, 'chat'))
            || (d.messageType === 303 && d.notifyToCompany &&
                !getChatSessionIds(d.siteKey, d.sincloSessionId, 'chat'))) {
          // 応対可能かチェック(対応できるのであれば trueが返る)
          chatApi.sendCheck(d, function(err, ret) {
            sendData.opFlg = ret.opFlg;

            //チャット呼出中メッセージ利用するの場合
            //チャット呼出中メッセージの場合
            if (ret.message == null && ret.in_flg == chatApi.cnst.inFlg.flg
                && d.notifyToCompany && d.initialNotification == true) {
              sendData.notification = true;
              sendData.mUserId = d.mUserId;
              sendData.messageDistinction = d.messageDistinction;
              sendData.userId = d.userId;
            }
            // 書き込みが成功したら顧客側に結果を返す
            var sincloSessionId = SharedData.sincloCore[d.siteKey][d.tabId].sincloSessionId;
            sendData.sincloSessionId = sincloSessionId;
            emit.toSameUser('sendChatResult', sendData, d.siteKey,
                sincloSessionId);
            // 保持していたオートメッセージを空にする
            SharedData.sincloCore[d.siteKey][sincloSessionId].autoMessages;
            if (d.sendMailFlg) {
              sendMail(d.autoMessageId, results.insertId, function() {
                console.log('send mail');
              });
            }
            if (Number(insertData.message_type) === 3) return false;
            // 書き込みが成功したら企業側に結果を返す
            var sendChatData = {
              tabId: d.tabId,
              sincloSessionId: sincloSessionId,
              opFlg: sendData.opFlg,
              chatId: results.insertId,
              sort: CommonUtil.fullDateTime(insertData.created),
              created: insertData.created,
              userId: insertData.m_users_id,
              messageType: d.messageType,
              ret: true,
              message: d.chatMessage,
              siteKey: d.siteKey,
              notifyToCompany: d.isScenarioMessage ?
                  !d.isScenarioMessage :
                  d.notifyToCompany
            };
            if (list.functionManager.isEnabled(d.siteKey,
                list.functionManager.keyList.monitorPollingMode)
                || !list.functionManager.isEnabled(d.siteKey,
                    list.functionManager.keyList.enableRealtimeMonitor)) {
              var sId = SharedData.sincloCore[d.siteKey][d.tabId].sessionId;
              Object.keys(list.customerList[d.siteKey]).forEach(function(key) {
                if (key.indexOf(sId) >= 0) {
                  sendChatData.customerInfo = list.customerList[d.siteKey][key];
                  chatApi.getUnreadCnt(sendChatData.customerInfo,
                      function(ret) {
                        sendChatData.customerInfo['chatUnreadId'] = ret.chatUnreadId;
                        sendChatData.customerInfo['chatUnreadCnt'] = ret.chatUnreadCnt ?
                            ret.chatUnreadCnt :
                            0;
                        emit.toCompany('sendChatResult', sendChatData,
                            d.siteKey);
                      });
                }
              });
            } else {
              emit.toCompany('sendChatResult', sendChatData, d.siteKey);
            }

            //通知された場合
            if (ret.opFlg === true && d.notifyToCompany) {
              ChatLogTimeManager.saveTime(results.insertId,
                  insertData.t_histories_id, 3, insertData.created);
              if ((d.messageType === 1 || d.messageType === 303) &&
                  insertData.message_read_flg != 1) {
                if (!CommonUtil.isset(
                    SharedData.sincloCore[d.siteKey][d.tabId].chatUnreadId)) {
                  SharedData.sincloCore[d.siteKey][d.tabId].chatUnreadId = results.insertId;
                }
                SharedData.sincloCore[d.siteKey][d.tabId].chatUnreadCnt++;
              }
              DBConnector.getPool().query(
                  'UPDATE t_history_chat_logs SET notice_flg = 1 WHERE t_histories_id = ? AND message_type = 1 AND id = ?;',
                  [
                    SharedData.sincloCore[d.siteKey][d.tabId].historyId,
                    results.insertId],
                  function(err, ret, fields) {
                  }
              );
              return false;
            }

            // 応対不可だった場合、既読にする
            historyId = SharedData.sincloCore[d.siteKey][d.tabId].historyId;
            DBConnector.getPool().query(
                'UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND message_type = 1 AND id <= ?;',
                [historyId, results.insertId], function(err, ret, fields) {

                }
            );

            // 自動応対メッセージではなく、Sorryメッセージがある場合は送る
            if (ret.message !== '' &&
                (!d.hasOwnProperty('isAutoSpeech') || !d.isAutoSpeech)) {
              // Sorryメッセージを送る
              var obj = d;
              obj.chatMessage = ret.message;
              obj.messageType = chatApi.cnst.observeType.sorry;
              obj.messageRequestFlg = chatApi.cnst.requestFlg.noFlg;
              obj.isDiagramMessage = false;
              chatApi.set(obj);
            }
          });
        } else {
          // 書き込みが成功したら顧客側に結果を返す
          var sincloSessionId = SharedData.sincloCore[d.siteKey][d.tabId].sincloSessionId;
          sendData.sincloSessionId = sincloSessionId;
          if (!noReturnSelfMessage) {
            emit.toSameUser('sendChatResult', sendData, d.siteKey,
                sincloSessionId);
          }
          if (d.sendMailFlg) {
            sendMail(d.autoMessageId, results.insertId, function() {
              console.log('send mail');
            });
          }
          if (Number(insertData.message_type) === 3) return false;

          // 書き込みが成功したら企業側に結果を返す
          var sendChatData = {
            tabId: d.tabId,
            sincloSessionId: sincloSessionId,
            chatId: results.insertId,
            sort: CommonUtil.fullDateTime(insertData.created),
            created: insertData.created,
            userId: insertData.m_users_id,
            messageType: d.messageType,
            ret: true,
            message: (d.isDiagramMessage) ?
                d.chatMessage.message :
                d.chatMessage,
            siteKey: d.siteKey,
            notifyToCompany: d.notifyToCompany
          };

          if (list.functionManager.isEnabled(d.siteKey,
              list.functionManager.keyList.monitorPollingMode)) {
            var sId = SharedData.sincloCore[d.siteKey][d.tabId].sessionId;
            Object.keys(list.customerList[d.siteKey]).forEach(function(key) {
              if (key.indexOf(sId) >= 0) {
                sendChatData.customerInfo = list.customerList[d.siteKey][key];
              }
            });
          }

          emit.toCompany('sendChatResult', sendChatData, d.siteKey);
          if (d.messageType === 1) {
            SharedData.sincloCore[d.siteKey][d.tabId].chatUnreadCnt++;
          }
        }

        if (d.isDiagramMessage) {
          var obj = d.chatMessage;
          var sql = 'INSERT INTO t_history_diagram_logs VALUES (null,?,?,?,?,?,0,now(),NULL,NULL)';
          DBConnector.getPool().query(sql, [
            list.companyList[d.siteKey],
            results.insertId,
            obj.did,
            obj.sourceNodeId,
            obj.nextNodeId], (err, result) => {

          });
        }

        //オペレータリクエスト件数
        //リクエストチャットか確認
        if (d.messageRequestFlg == 1) {
          var companyId = list.companyList[d.siteKey];
          var getUserInfo = 'SELECT chat.sc_flg as sc_flg, widget.display_type FROM m_companies AS comp LEFT JOIN m_widget_settings AS widget ON ( comp.id = widget.m_companies_id ) LEFT JOIN m_chat_settings AS chat ON ( chat.m_companies_id = widget.m_companies_id ) WHERE comp.id = ?;';
          DBConnector.getPool().
              query(getUserInfo, [companyId], function(err, result) {
            //ウィジェットが常に表示する場合
            if (result[0].display_type == 1) {
              //対応数上限設定ある場合
              if (Number(result[0].sc_flg) == 1) {
                //オペレータがいる場合
                if (CommonUtil.isset(SharedData.scList[d.siteKey])
                    && CommonUtil.isset(SharedData.scList[d.siteKey].user)
                    && Object.keys(SharedData.scList[d.siteKey].user)
                    && Object.keys(SharedData.scList[d.siteKey].user).length !==
                    0) {
                  for (key in Object.keys(SharedData.scList[d.siteKey].user)) {
                    var userId = Object.keys(
                        SharedData.scList[d.siteKey].user)[key];
                    //対応数がMAX人数か確認
                    if (SharedData.scList[d.siteKey].user[userId] >
                        SharedData.scList[d.siteKey].cnt[userId]) {
                      addChatActiveUser(results.insertId, userId, d.siteKey);
                    }
                  }
                }
              }
              //対応数上限設定していない場合
              else {
                //オペレータがいる場合
                if (CommonUtil.isset(SharedData.company.info[d.siteKey])
                    && Object.keys(SharedData.company.info[d.siteKey])
                    &&
                    Object.keys(SharedData.company.info[d.siteKey]).length !==
                    0) {
                  for (key in Object.keys(SharedData.company.info[d.siteKey])) {
                    var userId = Object.keys(
                        SharedData.company.info[d.siteKey])[key];
                    addChatActiveUser(results.insertId, userId, d.siteKey);
                  }
                }
              }
            }

            //オペレータが待機中のみ表示し、待機中オペレータがいる場合場合
            else if (result[0].display_type == 2 &&
                Object.keys(SharedData.activeOperator[d.siteKey]) &&
                Object.keys(SharedData.activeOperator[d.siteKey]).length !==
                0) {
              //対応数上限設定ある場合
              if (Number(result[0].sc_flg) == 1) {
                for (key in Object.keys(SharedData.activeOperator[d.siteKey])) {
                  userId = Object.keys(
                      SharedData.activeOperator[d.siteKey])[key];
                  //対応数がMAX人数か確認
                  if (SharedData.scList[d.siteKey].user[userId] >
                      SharedData.scList[d.siteKey].cnt[userId]) {
                    addChatActiveUser(results.insertId, userId, d.siteKey);
                  }
                }
              }
              //対応数上限を設定していない場合
              else {
                for (key in Object.keys(SharedData.activeOperator[d.siteKey])) {
                  userId = Object.keys(
                      SharedData.activeOperator[d.siteKey])[key];
                  addChatActiveUser(results.insertId, userId, d.siteKey);
                }
              }
            }
          });
        }
      } else {
        // 書き込みが失敗したらエラーを渡す
        return emit.toUser('sendChatResult', {
          tabId: d.tabId,
          messageType: d.messageType,
          ret: false,
          siteKey: d.siteKey
        }, d.siteKey);
      }
    },
    notifyCommit: function(name, d) { // DBに書き込むとき

      var insertData = {
        t_histories_id: getSessionId(d.siteKey, d.tabId, 'historyId'),
        m_companies_id: list.companyList[d.siteKey],
        visitors_id: d.visitorsId,
        m_users_id: d.userId,
        message: d.chatMessage,
        message_type: d.messageType,
        message_distinction: d.messageDistinction,
        message_request_flg: chatApi.cnst.requestFlg.noFlg,
        message_read_flg: 1,
        notice_flg: 0,
        created: new Date(d.created)
      };

      DBConnector.getPool().query(
          'SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;',
          insertData.t_histories_id,
          function(err, rows) {
            if (err !== null && err !== '') return false; // DB接続断対応
            if (rows && rows[0]) {
              insertData.t_history_stay_logs_id = rows[0].id;
            }
            DBConnector.getPool().
                query('INSERT INTO t_history_chat_logs SET ?', insertData,
                function(error, results, fields) {
                  if (CommonUtil.isset(error)) {
                    d.error = error;
                    d.ret = false;
                    console.log(
                        'RECORD INSERT ERROR: notifyCommit-func:' + error);
                  }
                  if (results.hasOwnProperty('insertId')) {
                    let messageTimeType = 1;
                    if (Number(insertData.message_request_flg) === 1) {
                      messageTimeType = 2;
                    } else if (d.messageType === 1 || d.messageType === 8) {
                      messageTimeType = 2;
                    }
                    ChatLogTimeManager.saveTime(results.insertId,
                        insertData.t_histories_id, messageTimeType,
                        insertData.created);
                    d.id = results.insertId;
                    d.created = CommonUtil.fullDateTime(d.created);
                  }
                  emit.toCompany(name, d, d.siteKey);
                });
          }
      );
    },
    sendUnreadCnt: function(evName, obj, toUserFlg) {
      var sql, ret = {
            tabId: obj.tabId,
            sincloSessionId: obj.sincloSessionId,
            chatUnreadId: null,
            chatUnreadCnt: 0
          },
          sincloSessionId = obj.sincloSessionId,
          siteId = list.companyList[obj.siteKey];
      sql = ' SELECT chat.id AS chatId, his.visitors_id, his.tab_id, chat.message FROM t_histories AS his';
      sql += ' INNER JOIN t_history_chat_logs AS chat ON ( his.id = chat.t_histories_id )';
      sql += ' WHERE his.tab_id = ? AND his.m_companies_id = ? AND chat.message_type = 1';
      sql += '   AND chat.m_users_id IS NULL AND chat.message_read_flg != 1 ORDER BY chat.id desc';
      DBConnector.getPool().
          query(sql, [sincloSessionId, siteId], function(err, rows) {
        if (err !== null && err !== '') return false; // DB接続断対応
        if (!CommonUtil.isset(err) &&
            (rows.length > 0 && CommonUtil.isset(rows[0].chatId))) {
          ret.chatUnreadId = rows[0].chatId;
          if (CommonUtil.isset(SharedData.sincloCore[obj.siteKey])
              && CommonUtil.isset(SharedData.sincloCore[obj.siteKey][obj.tabId])
              && CommonUtil.isset(
                  SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt)) {
            if (rows.length > 0 &&
                SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt ===
                0) {
              ret.chatUnreadCnt = rows.length;
            } else {
              ret.chatUnreadCnt = SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt;
            }
            console.log('set unreadCnt ' + obj.tabId + ' ' + ret.chatUnreadCnt);
            SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt = ret.chatUnreadCnt;
          } else {
            ret.chatUnreadCnt = rows.length;
          }
        }
        emit.toCompany(evName, ret, obj.siteKey);
        if (toUserFlg) {
          emit.toMine(evName, ret, socket);
        }
      });
    },
    getUnreadCnt: function(obj, callback) {
      var ret = {
        tabId: obj.tabId,
        sincloSessionId: obj.sincloSessionId,
        chatUnreadId: null,
        chatUnreadCnt: 0
      };
      ret.chatUnreadId = (CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.tabId]) && CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadId)) ?
          SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadId :
          null;
      ret.chatUnreadCnt = (CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.tabId]) && CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt)) ?
          SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt :
          null;
      console.log('getUnreadCnt:: chatUnreadId => ' + ret.chatUnreadId +
          ', chatUnreadCnt => ' + ret.chatUnreadCnt);
      callback(ret);
    },
    calcScNum: function(obj, userId) { /* SharedData.sincloCoreから対象ユーザーのチャット対応状態を算出 */
      var scNum = 0;
      var sincloSessionIds = [];
      if (!SharedData.sincloCore.hasOwnProperty(obj.siteKey)) return scNum;
      var tabIds = Object.keys(SharedData.sincloCore[obj.siteKey]);
      for (var i = 0; i < tabIds.length; i++) {
        if (tabIds[i] && tabIds[i].indexOf('_') >= 0) {
          var tabData = SharedData.sincloCore[obj.siteKey][tabIds[i]];
          if (tabData.hasOwnProperty('chat') &&
              CommonUtil.isNumber(tabData.chat)) {
            // 同一のsincloSessionIdを保有するユーザーは同時応対数１とする
            if (Number(tabData.chat) === Number(userId) &&
                sincloSessionIds.indexOf(tabData.sincloSessionId) === -1) {
              scNum++;
              sincloSessionIds.push(tabData.sincloSessionId);
            }
          }
        }
      }
      return scNum;
    },
    sendCheckTimerList: {},
    widgetCheck: function(d, callback) {
      return this.scCheck(1, d, callback);
    }, // ウィジェット表示チェック
    sendCheck: function(d, callback) {
      return this.scCheck(2, d, callback);
    }, // Sorryメッセージ送信チェック
    dayMap: {
      0: 'sun',
      1: 'mon',
      2: 'tue',
      3: 'wed',
      4: 'thu',
      5: 'fri',
      6: 'sat'
    },
    scCheck: function(type, d, callback) {
      var companyId = list.companyList[d.siteKey],
          siteKey = d.siteKey,
          ret = false,
          message = null,
          now = new Date(),
          nowDay = now.getDay(),
          timeData = [],
          publicHolidayData = null,
          active_flg = '',
          check = '',
          outside_hours_sorry_message = '',
          wating_call_sorry_message = '',
          no_standby_sorry_message = '',
          dateParse = Date.parse(now),
          date = now.getFullYear() + '/' +
              ('0' + (now.getMonth() + 1)).slice(-2) + '/' +
              ('0' + (now.getDate())).slice(-2) + ' ';

      if (CommonUtil.isKeyExists(common.operationHourSettings, siteKey)) {
        for (var i = 0; i <
        common.operationHourSettings[siteKey].length; i++) {
          dayType = JSON.parse(common.operationHourSettings[siteKey][i].type);
          //営業時間設定の条件が「毎日」の場合
          if (dayType == 1) {
            day = this.dayMap[nowDay];
            timeData = JSON.parse(
                common.operationHourSettings[siteKey][i].time_settings).everyday[day];
            publicHolidayData = JSON.parse(
                common.operationHourSettings[siteKey][i].time_settings).everyday['pub'];
          }
          //営業時間設定の条件が「平日・週末」の場合
          else {
            var dayType = 'week';
            if (nowDay == 1 || nowDay == 2 || nowDay == 3 || nowDay == 4 ||
                nowDay == 5) {
              dayType = 'week';
            } else {
              dayType = 'weekend';
            }
            timeData = JSON.parse(
                common.operationHourSettings[siteKey][i].time_settings).weekly[dayType];
            publicHolidayData = JSON.parse(
                common.operationHourSettings[siteKey][i].time_settings).weekly['weekpub'];
          }
          active_flg = JSON.parse(
              common.operationHourSettings[siteKey][i].active_flg);
        }
      }

      if (common.chatSettings[siteKey].sorry_message == '') {
        //営業時間外sorryメッセージ
        outside_hours_sorry_message = common.chatSettings[siteKey].outside_hours_sorry_message;
        //待ち呼sorryメッセージ
        wating_call_sorry_message = common.chatSettings[siteKey].wating_call_sorry_message;
        //待機なしsorryメッセージ
        no_standby_sorry_message = common.chatSettings[siteKey].no_standby_sorry_message;
      } else {
        //営業時間外sorryメッセージ
        outside_hours_sorry_message = common.chatSettings[siteKey].sorry_message;
        //待ち呼sorryメッセージ
        wating_call_sorry_message = common.chatSettings[siteKey].sorry_message;
        //待機なしsorryメッセージ
        no_standby_sorry_message = common.chatSettings[siteKey].sorry_message;
      }

      // ウィジェットが非表示の場合
      if (type == 1 &&
          common.widgetSettings[siteKey].display_type === 3) {
        return callback(true,
            {opFlg: false, message: no_standby_sorry_message});
      } else if (type == 2 &&
          common.widgetSettings[siteKey].display_type === 3) {
        //営業時間を利用する場合
        if (active_flg == 1) {
          for (var i2 = 0; i2 <
          common.publicHolidaySettingsArray.length; i2++) {
            //祝日の場合
            if ((now.getFullYear() + '/' + '0' +
                (now.getMonth() + 1)).slice(-2) + '/' +
                ('0' + (now.getDate())).slice(-2) ==
                common.publicHolidaySettingsArray[i2].year + '/' +
                common.publicHolidaySettingsArray[i2].month + '/' +
                common.publicHolidaySettingsArray[i2].day) {
              //祝日の営業時間設定が「休み」でない場合
              if (publicHolidayData[0].start != '' &&
                  publicHolidayData[0].end != '') {
                for (var i = 0; i < publicHolidayData.length; i++) {
                  var endTime = publicHolidayData[i].end;
                  // 営業時間の終了時刻が24:00の場合
                  if (publicHolidayData[i].end == '24:00') {
                    endTime = '23:59:59';
                  }
                  // 営業時間内の場合
                  if (Date.parse(
                      new Date(date + publicHolidayData[i].start)) <=
                      dateParse && dateParse <
                      Date.parse(new Date(date + endTime))) {
                    return callback(true,
                        {opFlg: false, message: no_standby_sorry_message});
                    break;
                  }
                }
              }
              //営業時間外の場合
              return callback(true,
                  {opFlg: false, message: outside_hours_sorry_message});
            }
          }
          // 祝日でない場合、営業時間設定が「休み」でない場合
          if (timeData[0].start != '' && timeData[0].end != '') {
            for (var i = 0; i < timeData.length; i++) {
              var endTime = timeData[i].end;
              // 営業時間の終了時刻が024:00の場合
              if (timeData[i].end == '24:00') {
                endTime = '23:59:59';
              }
              // 営業時間内の場合
              if (Date.parse(new Date(date + timeData[i].start)) <=
                  dateParse && dateParse <
                  Date.parse(new Date(date + endTime))) {
                check = true;
                return callback(true,
                    {opFlg: false, message: no_standby_sorry_message});
                break;
              }
            }
            //営業時間外の場合
            if (check != true) {
              return callback(true,
                  {opFlg: false, message: outside_hours_sorry_message});
            }
          }
        }
        //営業時間を利用しない場合
        else {
          return callback(true,
              {opFlg: false, message: no_standby_sorry_message});
        }
      }

      // ウィジェット表示のジャッジの場合、常に表示は必ずtrue
      if (type === 1 &&
          common.widgetSettings[siteKey].display_type === 1) {
        return callback(true,
            {opFlg: true, message: no_standby_sorry_message});
      }
      // ウィジェット表示のジャッジの場合、営業時間内のみ表示するの場合、営業時間内の場合はtrue
      if (type === 1 &&
          common.widgetSettings[siteKey].display_type === 4 &&
          active_flg == 1) {
        // 祝日の場合
        for (var i2 = 0; i2 <
        common.publicHolidaySettingsArray.length; i2++) {
          if (now.getFullYear() + '/' +
              ('0' + (now.getMonth() + 1)).slice(-2) + '/' +
              ('0' + (now.getDate())).slice(-2) ==
              common.publicHolidaySettingsArray[i2].year + '/' +
              common.publicHolidaySettingsArray[i2].month + '/' +
              common.publicHolidaySettingsArray[i2].day) {
            if (publicHolidayData[0].start != '' &&
                publicHolidayData[0].end != '') {
              for (var i = 0; i < publicHolidayData.length; i++) {
                var endTime = publicHolidayData[i].end;
                // 営業時間の終了時刻が24:00の場合
                if (publicHolidayData[i].end == '24:00') {
                  endTime = '23:59:59';
                }
                if (Date.parse(
                    new Date(date + publicHolidayData[i].start)) <=
                    dateParse && dateParse <
                    Date.parse(new Date(date + endTime))) {
                  return callback(true,
                      {opFlg: true, message: no_standby_sorry_message});
                  break;
                }
              }
            }
            return callback(true,
                {opFlg: false, message: outside_hours_sorry_message});
          }
        }
        // 祝日でない場合、営業時間設定が「休み」でない場合
        if (timeData[0].start != '' && timeData[0].end != '') {
          for (var i = 0; i < timeData.length; i++) {
            var endTime = timeData[i].end;
            // 営業時間の終了時刻が24:00の場合
            if (timeData[i].end == '24:00') {
              endTime = '23:59:59';
            }
            // 営業時間内の場合
            if (Date.parse(new Date(date + timeData[i].start)) <=
                dateParse && dateParse <
                Date.parse(new Date(date + endTime))) {
              return callback(true,
                  {opFlg: true, message: no_standby_sorry_message});
              break;
            }
          }
        }
        return callback(true,
            {opFlg: false, message: outside_hours_sorry_message});
      }

      // チャット上限数を設定していない場合
      if (Number(common.chatSettings[siteKey].sc_flg) === 2) {
        // オペレーターが待機している場合
        if (type === 1 &&
            (Number(
                common.widgetSettings[siteKey].display_type) ===
                2 &&
                getOperatorCnt(siteKey) > 0)
        ) {
          return callback(true,
              {opFlg: true, message: outside_hours_sorry_message});
        }
        // 営業時間設定を利用している場合
        if (active_flg === 1) {
          //祝日の場合
          for (var i2 = 0; i2 <
          common.publicHolidaySettingsArray.length; i2++) {
            if ((now.getFullYear() + '/' + '0' +
                (now.getMonth() + 1)).slice(-2) + '/' +
                ('0' + (now.getDate())).slice(-2) ==
                common.publicHolidaySettingsArray[i2].year + '/' +
                common.publicHolidaySettingsArray[i2].month + '/' +
                common.publicHolidaySettingsArray[i2].day) {
              check = true;
              //祝日の営業時間設定が「休み」でない場合
              if (publicHolidayData[0].start != '' &&
                  publicHolidayData[0].end != '') {
                for (var i = 0; i < publicHolidayData.length; i++) {
                  var endTime = publicHolidayData[i].end;
                  // 営業時間の終了時刻が24:00の場合
                  if (publicHolidayData[i].end == '24:00') {
                    endTime = '23:59:59';
                  }
                  //営業時間内の場合
                  if (Date.parse(
                      new Date(date + publicHolidayData[i].start)) <=
                      dateParse && dateParse < Date.parse(
                          new Date(date + publicHolidayData[i].end))) {
                    // オペレータが待機している場合
                    if ((Number(
                        common.widgetSettings[siteKey].display_type) ===
                        2 &&
                        getOperatorCnt(siteKey) > 0) ||
                        (Number(
                            common.widgetSettings[siteKey].display_type) ===
                            1 &&
                            getOperatorCnt(siteKey) > 0) ||
                        (Number(
                            common.widgetSettings[siteKey].display_type) ===
                            4 &&
                            getOperatorCnt(siteKey) > 0)
                    ) {
                      return callback(true, {
                        opFlg: true,
                        message: null,
                        in_flg: common.chatSettings[siteKey].in_flg
                      });
                    }
                    //オペレータが待機していない場合
                    else {
                      ret = false;
                      message = no_standby_sorry_message;
                    }
                  }
                  //営業時間外の場合
                  else {
                    ret = false;
                    message = outside_hours_sorry_message;
                  }
                }
              }
              //祝日の営業時間設定が「休み」の場合
              else {
                ret = false;
                message = outside_hours_sorry_message;
              }
            }
          }

          //祝日でない場合
          if (check != true) {
            //営業時間設定が「休み」の場合
            if (timeData[0].start == '' && timeData[0].end == '') {
              ret = false;
              message = outside_hours_sorry_message;
            }
            //営業時間設定が「休み」でない場合
            else {
              for (var i = 0; i < timeData.length; i++) {
                var endTime = timeData[i].end;
                // 営業時間の終了時刻が24:00の場合
                if (timeData[i].end == '24:00') {
                  endTime = '23:59:59';
                }
                //営業時間内
                if (Date.parse(new Date(date + timeData[i].start)) <=
                    dateParse && dateParse <
                    Date.parse(new Date(date + endTime))) {
                  check = true;
                  //オペレータが待機している場合
                  if ((Number(
                      common.widgetSettings[siteKey].display_type) ===
                      2 &&
                      getOperatorCnt(siteKey) > 0) ||
                      (Number(
                          common.widgetSettings[siteKey].display_type) ===
                          1 &&
                          getOperatorCnt(siteKey) > 0) ||
                      (Number(
                          common.widgetSettings[siteKey].display_type) ===
                          4 &&
                          getOperatorCnt(siteKey) > 0)
                  ) {
                    return callback(true, {
                      opFlg: true,
                      message: null,
                      in_flg: Number(common.chatSettings[siteKey].in_flg)
                    });
                  }
                  //オペレータが待機していない場合
                  else {
                    ret = false;
                    message = no_standby_sorry_message;
                  }
                }
              }
              // 営業時間外の場合
              if (check != true) {
                ret = false;
                message = outside_hours_sorry_message;
              }
            }
          }
        }
        //営業時間設定を利用していない場合
        else {
          //オペレータが待機している場合
          if ((Number(
              common.widgetSettings[siteKey].display_type) ===
              2 && getOperatorCnt(siteKey) > 0) ||
              (Number(
                  common.widgetSettings[siteKey].display_type) ===
                  1 && getOperatorCnt(siteKey) > 0) ||
              (Number(
                  common.widgetSettings[siteKey].display_type) ===
                  4 && getOperatorCnt(siteKey) > 0)
          ) {
            return callback(true,
                {
                  opFlg: true,
                  message: null,
                  in_flg: common.chatSettings[siteKey].in_flg
                });
          }
          //オペレータが待機していない場合
          else {
            ret = false;
            message = no_standby_sorry_message;
          }
        }
      }


      // チャット上限数を設定している場合
      else if (Number(common.chatSettings[siteKey].sc_flg) === 1) {
        if (type === 1 &&
            Number(
                common.widgetSettings[siteKey].display_type) ===
            2 &&
            SharedData.scList.hasOwnProperty(siteKey)) {
          var userIds = Object.keys(SharedData.scList[siteKey].user);
          if (userIds.length !== 0) {
            for (var i = 0; i < userIds.length; i++) {
              if (Number(SharedData.scList[siteKey].user[userIds[i]]) ===
                  Number(SharedData.scList[siteKey].cnt[userIds[i]])) continue;
              return callback(true,
                  {opFlg: true, message: outside_hours_sorry_message});
            }
          }
        }
        //営業時間設定を利用している場合
        if (active_flg === 1) {
          for (var i2 = 0; i2 <
          common.publicHolidaySettingsArray.length; i2++) {
            //祝日の場合
            if ((now.getFullYear() + '/' + '0' +
                (now.getMonth() + 1)).slice(-2) + '/' +
                ('0' + (now.getDate())).slice(-2) ==
                common.publicHolidaySettingsArray[i2].year + '/' +
                common.publicHolidaySettingsArray[i2].month + '/' +
                common.publicHolidaySettingsArray[i2].day) {
              check = true;
              //祝日の営業時間設定が「休み」でない場合
              if (publicHolidayData[0].start != '' &&
                  publicHolidayData[0].end != '') {
                for (var i = 0; i < publicHolidayData.length; i++) {
                  var endTime = publicHolidayData[i].end;
                  // 営業時間の終了時刻が24:00の場合
                  if (publicHolidayData[i].end == '24:00') {
                    endTime = '23:59:59';
                  }
                  //営業時間内の場合
                  if (Date.parse(
                      new Date(date + publicHolidayData[i].start)) <=
                      dateParse && dateParse <
                      Date.parse(new Date(date + endTime))) {
                    //オペレータが待機している場合
                    if ((Number(
                        common.widgetSettings[siteKey].display_type) ===
                        2 &&
                        getOperatorCnt(siteKey) > 0) ||
                        (Number(
                            common.widgetSettings[siteKey].display_type) ===
                            1 &&
                            getOperatorCnt(siteKey) > 0) ||
                        (Number(
                            common.widgetSettings[siteKey].display_type) ===
                            4 &&
                            getOperatorCnt(siteKey) > 0)
                    ) {
                      // チャット上限数をみる
                      if (SharedData.scList.hasOwnProperty(siteKey)) {
                        var userIds = Object.keys(
                            SharedData.scList[siteKey].user);
                        if (userIds.length !== 0) {
                          for (var i3 = 0; i3 < userIds.length; i3++) {
                            if (Number(
                                SharedData.scList[siteKey].user[userIds[i]]) ===
                                Number(
                                    SharedData.scList[siteKey].cnt[userIds[i]])) continue;
                            return callback(true, {
                              opFlg: true,
                              message: null,
                              in_flg: common.chatSettings[siteKey].in_flg
                            });
                          }
                          //上限数を超えている場合
                          if (ret != true) {
                            ret = false;
                            message = wating_call_sorry_message;
                          }
                        }
                      }
                    }
                    //待機中のオペレータがいない場合
                    else {
                      ret = false;
                      message = no_standby_sorry_message;
                    }
                  }
                  //営業時間外の場合
                  else {
                    ret = false;
                    message = outside_hours_sorry_message;
                  }
                }
              }
              //休みの設定にしているとき
              else {
                ret = false;
                message = outside_hours_sorry_message;
              }
            }
          }

          //祝日でない場合
          if (check != true) {
            //営業時間設定が「休み」の場合
            if (timeData[0].start === '' && timeData[0].end === '') {
              ret = false;
              message = outside_hours_sorry_message;
            }
            //営業時間設定が「休み」でない場合
            else {
              for (var i = 0; i < timeData.length; i++) {
                var endTime = timeData[i].end;
                // 営業時間の終了時刻が24:00の場合
                if (timeData[i].end == '24:00') {
                  endTime = '23:59:59';
                }
                //営業時間内の場合
                if (Date.parse(new Date(date + timeData[i].start)) <=
                    dateParse && dateParse <
                    Date.parse(new Date(date + endTime))) {
                  check = true;
                  //オペレータが待機している場合
                  if ((Number(
                      common.widgetSettings[siteKey].display_type) ===
                      2 &&
                      getOperatorCnt(siteKey) > 0) ||
                      (Number(
                          common.widgetSettings[siteKey].display_type) ===
                          1 &&
                          getOperatorCnt(siteKey) > 0) ||
                      (Number(
                          common.widgetSettings[siteKey].display_type) ===
                          4 &&
                          getOperatorCnt(siteKey) > 0)
                  ) {
                    // チャット上限数をみる
                    if (SharedData.scList.hasOwnProperty(siteKey)) {
                      var userIds = Object.keys(
                          SharedData.scList[siteKey].user);
                      if (userIds.length !== 0) {
                        for (var i2 = 0; i2 < userIds.length; i2++) {
                          if (Number(
                              SharedData.scList[siteKey].user[userIds[i]]) ===
                              Number(
                                  SharedData.scList[siteKey].cnt[userIds[i]])) continue;
                          return callback(true, {
                            opFlg: true,
                            message: null,
                            in_flg: Number(common.chatSettings[siteKey].in_flg)
                          });
                        }
                        //上限数を超えている場合
                        if (ret != true) {
                          ret = false;
                          message = wating_call_sorry_message;
                        }
                      }
                    }
                  }
                  //待機中のオペレータがいない場合
                  else {
                    ret = false;
                    message = no_standby_sorry_message;
                  }
                }
              }
              // 営業時間外の場合
              if (check != true) {
                ret = false;
                message = outside_hours_sorry_message;
              }
            }
          }
        }
        //営業時間設定を利用しない場合
        else {
          //オペレータが待機している場合
          if ((Number(
              common.widgetSettings[siteKey].display_type) ===
              2 && getOperatorCnt(siteKey) > 0) ||
              (Number(
                  common.widgetSettings[siteKey].display_type) ===
                  1 && getOperatorCnt(siteKey) > 0) ||
              (Number(
                  common.widgetSettings[siteKey].display_type) ===
                  4 && getOperatorCnt(siteKey) > 0)
          ) {
            // チャット上限数をみる
            if (SharedData.scList.hasOwnProperty(siteKey)) {
              var userIds = Object.keys(SharedData.scList[siteKey].user);
              if (userIds.length !== 0) {
                for (var i = 0; i < userIds.length; i++) {
                  if (Number(SharedData.scList[siteKey].user[userIds[i]]) ===
                      Number(
                          SharedData.scList[siteKey].cnt[userIds[i]])) continue;
                  return callback(true,
                      {
                        opFlg: true,
                        message: null,
                        in_flg: Number(common.chatSettings[siteKey].in_flg)
                      });
                }
                //上限数を超えている場合
                if (ret != true) {
                  ret = false;
                  message = wating_call_sorry_message;
                }
              }
            }
          }
          //待機中のオペレータがいない場合
          else {
            ret = false;
            message = no_standby_sorry_message;
          }
        }
      }
      return callback(true, {opFlg: ret, message: message});
    }
  };

  // 顧客情報取得
  var customerApi = {
    getInformations: function(visitorId, siteKey, callback) {
      DBConnector.getPool().query(
          'SELECT informations FROM m_customers WHERE m_companies_id = ? AND visitors_id = ? order by id desc LIMIT 1;',
          [list.companyList[siteKey], visitorId], function(err, row) {
            if (err !== null && err !== '') callback(null); // DB接続断対応
            if (CommonUtil.isset(row) && CommonUtil.isset(row[0]) &&
                CommonUtil.isset(row[0].informations)) {
              callback(JSON.parse(row[0].informations));
            } else {
              callback(null);
            }
          });
    }
  };

  // 接続時
  socket.on('connected', function(r) {
    var res = JSON.parse(r),
        send = {},
        type = '',
        siteKey = '',
        data = res.data,
        newTabId = '';
    send = data;

    if (res.type !== 'admin') {
      if (res.tabId &&
          (CommonUtil.isKeyExists(SharedData.sincloCore, 'siteKey.tabId') &&
              !CommonUtil.isKeyExists(SharedData.sincloCore,
                  'siteKey.tabId.timeoutTimer'))) {
        // 別タブを開いたときに情報がコピーされてしまっている状態
        console.log(
            'tabId is duplicate. change firstConnection flg ' + res.tabId);
        data.firstConnection = true;
      }
      if (res.tabId &&
          CommonUtil.isKeyExists(SharedData.sincloCore,
              'siteKey.tabId.timeoutTimer')) {
        var currentSincloSessionId = SharedData.sincloCore[res.siteKey][res.tabId].sincloSessionId;
        if (currentSincloSessionId) {
          var oldSessionId = SharedData.sincloCore[res.siteKey][res.tabId].sessionId;
          var sincloSession = SharedData.sincloCore[res.siteKey][currentSincloSessionId];
          if (CommonUtil.isset(sincloSession)) {
            var sessionIds = sincloSession.sessionIds;
            delete sessionIds[oldSessionId];
            if (currentSincloSessionId !== res.sincloSessionId &&
                Object.keys(sessionIds).length === 0) {
              delete SharedData.sincloCore[res.siteKey][currentSincloSessionId];
            }
          } else {
            console.log('currentSincloSession : ' + currentSincloSessionId +
                ' is null.');
          }
        }
      }

      if (data.userId === undefined || data.userId === '' || data.userId ===
          null) {
        send.userId = CommonUtil.makeUserId();
      }
      if (data.forceFirstConnect || (!data.inactiveReconnect &&
          ((res.sincloSessionId === undefined || res.sincloSessionId === '' ||
              res.sincloSessionId === null)
              || !(res.siteKey in SharedData.sincloCore)
              || !(res.sincloSessionId in SharedData.sincloCore[res.siteKey])
              ||
              SharedData.sincloCore[res.siteKey][res.sincloSessionId].sessionIds ===
              undefined))) {
        send.sincloSessionId = uuid.v4();
        send.sincloSessionIdIsNew = true;
        data.firstConnection = true;
      } else {
        send.sincloSessionIdIsNew = false;
      }
      if (data.firstConnection || data.accessId === undefined ||
          data.accessId === '' || data.accessId === null) {
        send.accessId = CommonUtil.makeAccessId();
      }
      if (res.token !== undefined) {
        send.token = res.token;
      }
      // ページ表示開始時間
      var d = new Date();
      send.pagetime = Date.parse(d);

      // アクセス開始フラグ
      if (data.firstConnection) {
        send.time = send.pagetime;
      }

      send.ipAddress = getIp(socket);

    }
    // 企業キーが取得できなければスルー
    if (res.siteKey) {

      if (res.type === 'admin') {
        socket.join(res.siteKey + emit.roomKey.company);
        var cnt = [], opKeys = [];

        if ('userId' in data && data.authority !== 99) {
          /* 企業ユーザーの管理 */
          SharedData.company.user[socket.id] = {
            userId: data.userId,
            siteKey: res.siteKey
          };
          if (!CommonUtil.isKeyExists(SharedData.company,
              'info.' + res.siteKey)) {
            SharedData.company.info[res.siteKey] = {};
            SharedData.company.timeout[res.siteKey] = {};
          }
          if (!CommonUtil.isKeyExists(SharedData.company,
              'info.' + res.siteKey + '.' + data.userId)) {
            SharedData.company.info[res.siteKey][data.userId] = {};
          }
          if (CommonUtil.isKeyExists(SharedData.company,
              'timeout.' + res.siteKey + '.' + data.userId)) {
            clearTimeout(SharedData.company.timeout[res.siteKey][data.userId]);
          }
          SharedData.company.info[res.siteKey][data.userId][socket.id] = null;
          /* 企業ユーザーの管理 */

          /* 待機中ユーザーの管理 */
          if (!(res.siteKey in SharedData.activeOperator)) {
            SharedData.activeOperator[res.siteKey] = {};
          }
          // 待機中の場合
          if (('status' in data) && String(data.status) === '1') {
            SharedData.activeOperator[res.siteKey][data.userId] = socket.id;
          }
          // 待機中でない場合
          else {
            if (SharedData.activeOperator[res.siteKey].hasOwnProperty(
                data.userId)) {
              delete SharedData.activeOperator[res.siteKey][data.userId];
            }
            if (SharedData.scList.hasOwnProperty(res.siteKey) &&
                SharedData.scList[res.siteKey].cnt.hasOwnProperty(
                    data.userId)) {
              delete SharedData.scList[res.siteKey].cnt[data.userId];
              delete SharedData.scList[res.siteKey].user[data.userId];
            }
            data.status = 0;
          }
          // 自身の初期ステータスを送る
          emit.toUser('cngOpStatus', {status: data.status},
              SharedData.activeOperator[res.siteKey][data.userId]);
          /* 待機中ユーザーの管理 */

          /* 同時対応数の管理 */
          if (data.hasOwnProperty('scNum')) {
            // ウィジェット表示設定を常に表示、もしくは待機中のみ表示の場合（かつ待機中の場合）
            if (data.opFlg === false ||
                (data.opFlg === true && data.hasOwnProperty('status') &&
                    Number(data.status) === 1)) {
              if (!SharedData.scList.hasOwnProperty(res.siteKey)) {
                SharedData.scList[res.siteKey] = {user: {}, cnt: {}};
              }
              SharedData.scList[res.siteKey].user[data.userId] = data.scNum;
              SharedData.scList[res.siteKey].cnt[data.userId] = chatApi.calcScNum(
                  res,
                  data.userId);
              console.log('【' + res.siteKey + '】新規：対応上限数設定 ' +
                  SharedData.scList[res.siteKey].cnt[data.userId] + ' / ' +
                  SharedData.scList[res.siteKey].user[data.userId] +
                  ' userId : ' +
                  data.userId);

              data.scInfo = SharedData.scList[res.siteKey].cnt;
            }
          } else {
            // チャット対応上限の設定が無効化されている場合はオブジェクトを削除する
            if (SharedData.scList.hasOwnProperty(res.siteKey)) {
              var getChatSettingSQL = 'SELECT sc_flg FROM m_chat_settings WHERE m_companies_id = ?';
              DBConnector.getPool().
                  query(getChatSettingSQL, [list.companyList[res.siteKey]],
                  function(err, rows) {
                    if (err !== null && err !== '') return false; // DB接続断対応
                    if (rows && rows[0] && rows[0].sc_flg === 1) {
                      delete SharedData.scList[res.siteKey];
                    }
                  });
            }
          }
          /* 同時対応数の管理 */
        }
        if (res.siteKey in SharedData.company.info) {
          cnt = Object.keys(SharedData.company.info[res.siteKey]);
        }

        if (res.siteKey in SharedData.activeOperator) {
          opKeys = Object.keys(SharedData.activeOperator[res.siteKey]);
        }

        data.userCnt = cnt.length;
        data.onlineUserCnt = opKeys.length;

        data.activeOperatorList = SharedData.activeOperator[res.siteKey];
        data.onlineOperatorList = SharedData.company.info[res.siteKey];

        // 企業側に情報提供
        emit.toCompany('getAccessInfo', data, res.siteKey);
        // 消費者にアクセス情報要求
        //emit.toClient('getAccessInfo', send, res.siteKey);
        processReceiveAccessInfo(res.siteKey, socket);
      } else {
        chatApi.widgetCheck(res, function(err, ret) {
          send.activeOperatorCnt = getOperatorCnt(res.siteKey);
          send.widget = ret.opFlg;
          send.opFlg = true;
          send.inactiveReconnect = Boolean(data.inactiveReconnect);
          if (ret.opFlg === false) {
            send.opFlg = false;
            if (res.hasOwnProperty('tabId') &&
                CommonUtil.isset(
                    getSessionId(res.siteKey, res.tabId, 'chat'))) {
              send.opFlg = true;
            }
          }
          socket.join(res.siteKey + emit.roomKey.client);
          if (newTabId !== '') {
            send.newTabId = newTabId;
          }
          emit.toMine('accessInfo', send, socket);

        });
      }
    }
  });

  socket.on('getCustomerList', function(data, ack) {
    var obj = JSON.parse(data);
    if (CommonUtil.isset(obj.siteKey)) {
      emit.toMine('beginOfCustomerList', {}, socket);
      processReceiveAccessInfo(obj.siteKey, socket);
    }
    if (ack) {
      ack({
        scInfo: (SharedData.scList.hasOwnProperty(obj.siteKey)) ?
            SharedData.scList[obj.siteKey].cnt :
            {}
      });
    }
  });

  function processReceiveAccessInfo(siteKey, socket) {
    var counter = 0;
    var totalCounter = 0;
    var chunkSize = 100;
    var keyLength = 0;
    if (CommonUtil.isset(list.customerList[siteKey])) {
      keyLength = Object.keys(list.customerList[siteKey]).length;
    }
    var arr = [keyLength];
    if (CommonUtil.isset(list.customerList[siteKey])) {
      Object.keys(list.customerList[siteKey]).forEach(function(key) {
        var splitedKey = key.split('@@');
        if (splitedKey.length === 2 && CommonUtil.isset(splitedKey[1])) {
          var targetSocketId = splitedKey[1];
          if (!io.sockets.connected[targetSocketId] ||
              !CommonUtil.isset(
                  list.customerList[siteKey][key].sincloSessionId)) {
            var targetTabId = list.customerList[siteKey][key].tabId;
            console.log('【' + siteKey + '】 list.customerList key : ' + key +
                ' client is not exist. deleting. targetTabId : ' + targetTabId);
            if (targetTabId && targetTabId !== '') {
              emit.toCompany('unsetUser',
                  {siteKey: siteKey, tabId: targetTabId}, siteKey);
            }
            delete list.customerList[siteKey][key];
            if (totalCounter === keyLength - 1) {
              emit.toMine('receiveAccessInfo', arr, socket);
              arr = [keyLength];
            }
            totalCounter++;
            return;
          }
        }
        var val = getConnectInfo(list.customerList[siteKey][key]);
        if (val.time) {
          val.term = CommonUtil.timeCalculator(val);
        }

        var afterGetCustomerInformations = function() {
          if (list.functionManager.isEnabled(siteKey,
              list.functionManager.keyList.chat)) {
            chatApi.getUnreadCnt(val, function(ret) {
              val['chatUnreadId'] = ret.chatUnreadId;
              val['chatUnreadCnt'] = ret.chatUnreadCnt ? ret.chatUnreadCnt : 0;
              if (!list.functionManager.isEnabled(siteKey,
                  list.functionManager.keyList.enableRealtimeMonitor) && (
                  (CommonUtil.isset(val['chatUnreadCnt']) &&
                      val['chatUnreadCnt'] === 0) &&
                  (!CommonUtil.isset(val['responderId']) &&
                      !CommonUtil.isset(val['chat'])))) {
                // 何もしない
              } else if ((list.functionManager.isEnabled(siteKey,
                  list.functionManager.keyList.hideRealtimeMonitor)
                  && list.functionManager.isEnabled(siteKey,
                      list.functionManager.keyList.monitorPollingMode))
                  && (CommonUtil.isset(val['chatUnreadCnt']) &&
                      val['chatUnreadCnt'] === 0 &&
                      !CommonUtil.isset(val['responderId']) &&
                      !CommonUtil.isset(val['chat']))) {
                // 何もしない
              } else {
                arr.push(val);
                counter++;
              }
              if (counter === chunkSize) {
                emit.toMine('receiveAccessInfo', arr, socket);
                counter = 0;
                arr = [keyLength];
              }
              if (totalCounter === keyLength - 1) {
                emit.toMine('receiveAccessInfo', arr, socket);
                if (list.functionManager.isEnabled(siteKey,
                    list.functionManager.keyList.monitorPollingMode)) {
                  emit.toMine('endOfCustomerList', {}, socket);
                }
                arr = [keyLength];
              }
              totalCounter++;
            });
          } else {
            if (!list.functionManager.isEnabled(siteKey,
                list.functionManager.keyList.enableRealtimeMonitor) && (
                (CommonUtil.isset(val['chatUnreadCnt']) &&
                    val['chatUnreadCnt'] === 0) ||
                (!CommonUtil.isset(val['responderId']) &&
                    !CommonUtil.isset(val['chat'])))
                ||
                (list.functionManager.isEnabled(siteKey,
                    list.functionManager.keyList.hideRealtimeMonitor)
                    && list.functionManager.isEnabled(siteKey,
                        list.functionManager.keyList.monitorPollingMode))
                && (CommonUtil.isset(val['chatUnreadCnt']) &&
                    val['chatUnreadCnt'] === 0 &&
                    !CommonUtil.isset(val['responderId']) &&
                    !CommonUtil.isset(val['chat']))) {
              // 何もしない
            } else {
              arr.push(val);
              counter++;
            }
            if (counter === chunkSize) {
              emit.toMine('receiveAccessInfo', arr, socket);
              counter = 0;
              arr = [keyLength];
            }
            if (totalCounter === keyLength - 1) {
              emit.toMine('receiveAccessInfo', arr, socket);
              if (list.functionManager.isEnabled(siteKey,
                  list.functionManager.keyList.monitorPollingMode)) {
                emit.toMine('endOfCustomerList', {}, socket);
              }
              arr = [keyLength];
            }
            totalCounter++;
          }
        };

        var sincloSessionId = getSessionId(val.siteKey, val.tabId,
            'sincloSessionId');
        if (CommonUtil.isset(
            SharedData.sincloCore[val.siteKey][sincloSessionId]) &&
            CommonUtil.isset(
                SharedData.sincloCore[val.siteKey][sincloSessionId].customerInfo)) {
          val.customerInfo = SharedData.sincloCore[val.siteKey][sincloSessionId].customerInfo;
          afterGetCustomerInformations();
        } else {
          customerApi.getInformations(val.userId, val.siteKey,
              function(information) {
                try {
                  SharedData.sincloCore[val.siteKey][sincloSessionId].customerInfo = information;
                } catch (e) {
                }
                val.customerInfo = information;
                afterGetCustomerInformations();
              });
        }
      });
    }
    if (!CommonUtil.isset(list.customerList[siteKey]) ||
        Object.keys(list.customerList[siteKey]).length === 0) {
      emit.toMine('receiveAccessInfo', arr, socket);
      if (list.functionManager.isEnabled(siteKey,
          list.functionManager.keyList.monitorPollingMode)) {
        emit.toMine('endOfCustomerList', {}, socket);
      }
    }
  }

  socket.on('connectedForSync', function(data) {
    // ページ表示開始時間
    var d = new Date();
    // 待機オペレーター人数
    var obj = JSON.parse(data);
    var actOpCnt = getOperatorCnt(obj.siteKey);

    chatApi.widgetCheck(obj, function(err, ret) {
      var opFlg = true;
      if (ret.opFlg === false) {
        opFlg = false;
        if (obj.hasOwnProperty('tabId') &&
            CommonUtil.isset(getSessionId(obj.siteKey, obj.tabId, 'chat'))) {
          opFlg = true;
        }
      }
      emit.toMine('retConnectedForSync', {
        pagetime: Date.parse(d),
        opFlg: opFlg,
        activeOperatorCnt: actOpCnt
      }, socket);
    });

  });

  socket.on('customerInfo', function(data) {
    var obj = JSON.parse(data);
    obj.term = CommonUtil.timeCalculator(obj);
    if (getSessionId(obj.siteKey, obj.tabId, 'chat')) {
      obj.chat = getSessionId(obj.siteKey, obj.tabId, 'chat');
    }

    obj = getConnectInfo(obj);

    // IPアドレスの取得
    if (!(('ipAddress' in obj) && CommonUtil.isset(obj.ipAddress))) {
      obj.ipAddress = getIp(socket);
    }

    var afterGetInformationProcess = function() {
      if (list.functionManager.isEnabled(obj.siteKey,
          list.functionManager.keyList.hideRealtimeMonitor) ||
          list.functionManager.isEnabled(obj.siteKey,
              list.functionManager.keyList.monitorPollingMode)) {
      } else {
        emit.toCompany('sendCustomerInfo', obj, obj.siteKey);
      }
      list.customerList[obj.siteKey][obj.accessId + '_' + obj.ipAddress + '@@' +
      socket.id] = obj;
      if ((('contract' in obj) && ('chat' in obj.contract) &&
          obj.contract.chat === false) ||
          list.functionManager.isEnabled(obj.siteKey,
              list.functionManager.keyList.monitorPollingMode)) return false;
      chatApi.sendUnreadCnt('sendChatInfo', obj, false);

      if (CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.sincloSessionId])) {
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo = obj.customerInfo;
      }
    };

    if (CommonUtil.isset(SharedData.company.info[obj.siteKey]) &&
        Object.keys(SharedData.company.info[obj.siteKey]).length > 0) {
      customerApi.getInformations(obj.userId, obj.siteKey,
          function(information) {
            obj.customerInfo = information;
            afterGetInformationProcess();
          });
    } else {
      afterGetInformationProcess();
    }
  });

  socket.on('getCustomerInfo', function(data) {
    var obj = JSON.parse(data);
    emit.toClient('confirmCustomerInfo', obj, obj.siteKey);
  });

  socket.on('searchCustomer', function(data) {
    var obj = JSON.parse(data);
    var term = obj.term;
    var result = [];
    if ('siteKey' in obj) {
      var keys = Object.keys(list.customerList[obj.siteKey]);
      if (keys && keys.length > 0) {
        keys.forEach(function(key) {
          var splitedKey = key.split('_');
          var targetTerm = obj.filterType === 1 ? splitedKey[0] : splitedKey[1];
          if (targetTerm) {
            if (targetTerm.indexOf(term) === 0) { // 前方一致検索
              var mergedObject = CommonUtil.extend(
                  list.customerList[obj.siteKey][key],
                  SharedData.sincloCore[obj.siteKey][list.customerList[obj.siteKey][key]['tabId']]);
              if (mergedObject.time) {
                mergedObject.term = CommonUtil.timeCalculator(mergedObject);
              }
              result.push(mergedObject);
            }
          }
        });
      }
    }
    emit.toMine('searchCustomerResult', result, socket);
    // チャット未読
    for (var i = 0; i < result.length; i++) {
      if (('contract' in result[i]) && ('chat' in result[i].contract) &&
          result[i].contract.chat === false) break;
      chatApi.sendUnreadCnt('sendChatInfo', result[i], false);
    }
  });

  socket.on('connectSuccessForClient', function(data) {
    var obj = JSON.parse(data);
    // SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
  });

  socket.on('link', function(data) {
    if (!list.functionManager.isEnabled(data.siteKey,
        list.functionManager.keyList.enableRealtimeMonitor)
        && CommonUtil.isset(SharedData.sincloCore[data.siteKey][data.tabId])
        && !CommonUtil.isset(
            SharedData.sincloCore[data.siteKey][data.tabId].historyId)) {
      let historyManager = new HistoryManager();
      let target = SharedData.sincloCore[data.siteKey][data.tabId];
      obj = Object.assign(data, target);
      historyManager.addHistory(obj).then((result) => {
        emit.toSameUser('setHistoryId', result, obj.siteKey,
            obj.sincloSessionId);
        SharedData.sincloCore[obj.siteKey][obj.tabId]['historyId'] = result.historyId;
        SharedData.sincloCore[obj.siteKey][obj.tabId]['stayLogsId'] = result.stayLogsId;
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]['historyId'] = result.historyId;
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]['stayLogsId'] = result.stayLogsId;
        processAddLink(result);
      });
    } else {
      processAddLink(data);
    }
  });

  function processAddLink(data) {
    var d = new Date();
    DBConnector.getPool().query(
        'INSERT INTO t_history_link_count_logs (m_companies_id,t_histories_id,t_history_stay_logs_id,link_url,created) VALUES(?,?,?,?,?)',
        [
          list.companyList[data.siteKey],
          data.historyId,
          data.stayLogsId,
          data.link,
          d.getFullYear() + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' +
          ('0' + d.getDate()).slice(-2) + ' ' + ('0' + d.getHours()).slice(-2) +
          ':' + ('0' + d.getMinutes()).slice(-2) + ':' +
          ('0' + d.getSeconds()).slice(-2)], function(error, res) {
          if (CommonUtil.isset(error)) {
            console.log(
                'RECORD INSERT ERROR: t_history_widget_close_counts:' + error);
          }
        });
    var ret = {
      siteKey: data.siteKey,
      tabId: data.tabId,
      sincloSessionId: data.sincloSessionId,
      userId: data.userId,
      mUserId: null,
      chatMessage: data.link,
      messageType: 8,
      created: new Date(),
      messageDistinction: 1,
      messageRequestFlg: data.messageRequestFlg,
      sendMailFlg: 0,
      autoMessageId: null
    };
    chatApi.set(ret);
  }



  socket.on('connectSuccess', function(data, ack) {
    var obj = JSON.parse(data);
    if (!CommonUtil.isset(SharedData.sincloCore[obj.siteKey])) {
      SharedData.sincloCore[obj.siteKey] = {};
    }
    if (!CommonUtil.isset(SharedData.sincloCore[obj.siteKey][obj.tabId])) {
      SharedData.sincloCore[obj.siteKey][obj.tabId] = {
        sincloSessionId: null,
        sessionId: null,
        subWindow: false,
        chatUnreadCnt: 0
      };
    }
    if (CommonUtil.isset(obj.sincloSessionId) &&
        !CommonUtil.isset(
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId])) {
      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId] = {
        sessionIds: {},
        autoMessages: {},
        scenario: {},
        diagram: []
      };
    }
    if ('timeoutTimer' in SharedData.sincloCore[obj.siteKey][obj.tabId]) {
      clearTimeout(SharedData.sincloCore[obj.siteKey][obj.tabId].timeoutTimer);
      SharedData.sincloCore[obj.siteKey][obj.tabId].timeoutTimer = null;
    }

    var oldSessionId = SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId;
    if (oldSessionId && CommonUtil.isset(obj.sincloSessionId)) {
      var sessionIds = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].sessionIds;
      console.log('delete id : ' + oldSessionId);
      delete sessionIds[oldSessionId];
      console.log('remains : ' + Object.keys(sessionIds).length);
      Object.keys(sessionIds).forEach(function(key) {
        if (!CommonUtil.isset(io.sockets.connected[key])) {
          console.log('delete not exist sessionId : ' + key);
          delete sessionIds[key];
          console.log('remains : ' + Object.keys(sessionIds).length);
          var keys = Object.keys(list.customerList[obj.siteKey]);
          if (keys && keys.length > 0) {
            keys.forEach(function(customerListId) {
              if (customerListId.indexOf(key) >= 0) {
                console.log(
                    'delete not exist list.customerList : ' + customerListId);
                delete list.customerListId[info.siteKey][customerListId];
              }
            });
          }
        }
      });
    }

    SharedData.connectList[socket.id] = {
      siteKey: obj.siteKey,
      tabId: obj.tabId,
      userId: null,
      sincloSessionId: obj.sincloSessionId
    };
    SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
    if (CommonUtil.isset(obj.sincloSessionId)) {
      SharedData.sincloCore[obj.siteKey][obj.tabId].sincloSessionId = obj.sincloSessionId;
      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].sessionIds[socket.id] = socket.id;
    }
    if (CommonUtil.isset(obj.tmpAutoMessages)) {
      try {
        Object.keys(obj.tmpAutoMessages).
            forEach(function(automessageKey, index, array) {
              if (typeof (obj.tmpAutoMessages[automessageKey]['created']) ===
                  'string') {
                obj.tmpAutoMessages[automessageKey]['created'] = new Date(
                    obj.tmpAutoMessages[automessageKey]['created']);
              }
              if (CommonUtil.isset(
                  SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) &&
                  !CommonUtil.isset(
                      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages)) {
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages = {};
              }
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages[automessageKey] = obj.tmpAutoMessages[automessageKey];
            });
      } catch (e) {

      }
    }
    if (CommonUtil.isset(obj.tmpDiagramMessages)) {
      try {
        Object.keys(obj.tmpDiagramMessages).
            forEach(function(diagramKey, index, array) {
              if (typeof (obj.tmpDiagramMessages[diagramKey]['created']) ===
                  'string') {
                obj.tmpDiagramMessages[diagramKey]['created'] = new Date(
                    obj.tmpDiagramMessages[diagramKey]['created']);
              }
              if (CommonUtil.isset(
                  SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) &&
                  !CommonUtil.isset(
                      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram)) {
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram = [];
              }
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram.push(
                  obj.tmpDiagramMessages[diagramKey]);
            });
      } catch (e) {

      }
    }
    if (obj.subWindow) {
      SharedData.sincloCore[obj.siteKey][obj.tabId].toTabId = obj.to;
      SharedData.sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
      SharedData.sincloCore[obj.siteKey][obj.tabId].subWindow = true;
      if (!getSessionId(obj.siteKey, obj.tabId, 'responderId')) {
        SharedData.sincloCore[obj.siteKey][obj.tabId].responderId = getSessionId(
            obj.siteKey, obj.to, 'responderId');
      }
      db.addDisplayShareHistory(
          SharedData.sincloCore[obj.siteKey][obj.tabId].responderId,
          obj, socket); // 登録
      obj.responderId = getSessionId(obj.siteKey, obj.to, 'responderId');
      if (getSessionId(obj.siteKey, obj.to, 'syncSessionId')) {
        SharedData.sincloCore[obj.siteKey][obj.to].syncSessionId = socket.id; // 同期先配列に、セッションIDを格納
      }
    } else if (!getSessionId(obj.siteKey, obj.tabId, 'parentTabId')) {
      SharedData.connectList[socket.id] = {
        siteKey: obj.siteKey,
        tabId: obj.tabId,
        userId: obj.userId,
        sincloSessionId: obj.sincloSessionId
      };
      if (('reconnect' in obj) && obj.reconnect) {
        socket.join(obj.siteKey + emit.roomKey.client);

        // sessionIdが消えてる可能性があるため、対応ユーザーIDを再セット
        if (getSessionId(obj.siteKey, obj.to, 'responderId')) {
          SharedData.sincloCore[obj.siteKey][obj.tabId].responderId = getSessionId(
              obj.siteKey, obj.to, 'responderId');
          obj.responderId = getSessionId(obj.siteKey, obj.to, 'responderId');
        }
      }

      // 履歴作成
      db.addHistory(obj, socket);
      if (!CommonUtil.isKeyExists(SharedData.sincloCore,
          obj.siteKey + '.' + obj.tabId + '.prev')) {
        SharedData.sincloCore[obj.siteKey][obj.tabId].prev = [];
      }
      SharedData.sincloCore[obj.siteKey][obj.tabId].prev.push({
        url: obj.url,
        title: obj.title,
        accessTime: CommonUtil.formatDateParse()
      });
      // カスタム情報自動登録
      db.upsertCustomerInfo(obj, socket, function(result) {
        // IPアドレスの取得
        if (!(('ipAddress' in obj) && CommonUtil.isset(obj.ipAddress))) {
          obj.ipAddress = getIp(socket);
        }

        //FIXME 企業別機能設定（企業情報連携）
        getCompanyInfoFromApi(obj, obj.ipAddress, function(data) {
          try {
            if (data) {
              var response = data;
              obj.orgName = response.orgName;
              obj.lbcCode = response.lbcCode;
              SharedData.sincloCore[obj.siteKey][obj.tabId].orgName = obj.orgName;
              SharedData.sincloCore[obj.siteKey][obj.tabId].lbcCode = obj.lbcCode;
              if (CommonUtil.isset(
                  list.customerList[obj.siteKey][obj.accessId + '_' +
                  obj.ipAddress +
                  '_' + socket.id])) {
                list.customerList[obj.siteKey][obj.accessId + '_' +
                obj.ipAddress +
                '_' + socket.id]['orgName'] = obj.orgName;
                list.customerList[obj.siteKey][obj.accessId + '_' +
                obj.ipAddress +
                '_' + socket.id]['lbcCode'] = obj.lbcCode;
              }
            }
          } catch (e) {
            console.log(
                'getCompanyInfoFromApiのcallbackでエラー : ' + data + ' message : ' +
                e.message);
          }
          if (!list.functionManager.isEnabled(obj.siteKey,
              list.functionManager.keyList.monitorPollingMode)) {
            emit.toCompany('syncNewInfo', obj, obj.siteKey);
          }
        });

        if (ack) {
          ack(data);
        }
      });
    }
  });
  // ウィジェットが生成されたことを企業側に通知する
  socket.on('syncReady', function(data) {
    var obj = JSON.parse(data);
    if (!list.functionManager.isEnabled(obj.siteKey,
        list.functionManager.keyList.monitorPollingMode)
        && list.functionManager.isEnabled(obj.siteKey,
            list.functionManager.keyList.enableRealtimeMonitor)) {
      emit.toCompany('syncNewInfo', obj, obj.siteKey);
    }
  });
  // アクティブ状態を送る
  socket.on('sendTabInfo', function(d) {
    var obj = JSON.parse(d);
    if (!list.functionManager.isEnabled(obj.siteKey,
        list.functionManager.keyList.monitorPollingMode)) {
      emit.toCompany('retTabInfo', d, obj.siteKey);
    }

    // 画面同期中は同期フレーム本体に送る
    if (('connectToken' in obj) && CommonUtil.isset(obj.connectToken)) {
      emit.toUser('retTabInfo', obj,
          getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
    }
    for (var key in list.customerList[obj.siteKey]) {
      if (CommonUtil.isset(obj.tabId) &&
          obj.tabId.indexOf(list.customerList[obj.siteKey][key]['tabId']) ===
          0) {
        list.customerList[obj.siteKey][key]['status'] = obj.status;
        break;
      }
    }
  });

  //閉じるボタンクリック数追加
  socket.on('addClickCloseCounts', function(data) {
    var obj = JSON.parse(data);
    DBConnector.getPool().query(
        'SELECT widget_close_count from t_history_widget_close_counts where m_companies_id = ? AND year = ? AND month = ? AND day = ? AND hour = ?',
        [list.companyList[obj.siteKey], obj.year, obj.month, obj.day, obj.hour],
        function(err, results) {
          if (results.length == 0) {
            DBConnector.getPool().query(
                'INSERT INTO t_history_widget_close_counts (m_companies_id,year,month,day,hour,widget_close_count) VALUES(?,?,?,?,?,?)',
                [
                  list.companyList[obj.siteKey],
                  obj.year,
                  obj.month,
                  obj.day,
                  obj.hour,
                  1], function(error, res) {
                  if (CommonUtil.isset(error)) {
                    console.log(
                        'RECORD INSERT ERROR: t_history_widget_close_counts:' +
                        error);
                  }
                });
          } else {
            DBConnector.getPool().query(
                'UPDATE t_history_widget_close_counts SET widget_close_count = ? WHERE m_companies_id = ? AND year = ? AND month = ? AND day = ? AND hour = ?',
                [
                  results[0].widget_close_count + 1,
                  list.companyList[obj.siteKey],
                  obj.year,
                  obj.month,
                  obj.day,
                  obj.hour], function(error, res) {
                  if (CommonUtil.isset(error)) {
                    console.log(
                        'RECORD UPDATE ERROR: t_conversation_count(conversation_count):' +
                        error);
                    return false;
                  }
                });
          }
          if (CommonUtil.isset(err)) {
            console.log(
                'RECORD SELECT ERROR: t_history_widget_close_counts:' + err);
          }
        });
  });

  //ウィジェットを最小化した回数追加
  socket.on('addClickMinimizeCounts', function(data) {
    var obj = JSON.parse(data);
    DBConnector.getPool().query(
        'SELECT widget_minimize_count from t_history_widget_minimize_counts where m_companies_id = ? AND year = ? AND month = ? AND day = ? AND hour = ?',
        [list.companyList[obj.siteKey], obj.year, obj.month, obj.day, obj.hour],
        function(err, results) {
          if (results.length == 0) {
            DBConnector.getPool().query(
                'INSERT INTO t_history_widget_minimize_counts (m_companies_id,year,month,day,hour,widget_minimize_count) VALUES(?,?,?,?,?,?)',
                [
                  list.companyList[obj.siteKey],
                  obj.year,
                  obj.month,
                  obj.day,
                  obj.hour,
                  1], function(error, res) {
                  if (CommonUtil.isset(error)) {
                    console.log(
                        'RECORD INSERT ERROR: t_history_widget_minimize_counts:' +
                        error);
                  }
                });
          } else {
            DBConnector.getPool().query(
                'UPDATE t_history_widget_minimize_counts SET widget_minimize_count = ? WHERE m_companies_id = ? AND year = ? AND month = ? AND day = ? AND hour = ?',
                [
                  results[0].widget_minimize_count + 1,
                  list.companyList[obj.siteKey],
                  obj.year,
                  obj.month,
                  obj.day,
                  obj.hour], function(error, res) {
                  if (CommonUtil.isset(error)) {
                    console.log(
                        'RECORD UPDATE ERROR: t_conversation_count(conversation_count):' +
                        error);
                    return false;
                  }
                });
          }
          if (CommonUtil.isset(err)) {
            console.log(
                'RECORD SELECT ERROR: t_history_widget_minimize_counts:' + err);
          }
        });
  });

  //ウィジェットを開いた事を通知する
  //現在はウィジェット表示ログを書き込むのみで、クライアント側はretTabInfoにwidgetの情報を付与してハンドリングしている
  socket.on('sendWidgetShown', function(d) {
    var obj = JSON.parse(d);
    //ウィジェット件数登録処理
    if (obj.widget === true) {
      DBConnector.getPool().
          query('SELECT * FROM t_history_widget_displays WHERE tab_id = ?',
          [obj.sincloSessionId || obj.tabId], function(err, results) {
            if (CommonUtil.isset(err)) {
              console.log(
                  'RECORD SElECT ERROR: t_history_widget_displays(tab_id):' +
                  err);
              return false;
            }
            //ウィジェットが初めて表示された場合
            if (Object.keys(results).length === 0) {
              //tabId登録
              DBConnector.getPool().query(
                  'INSERT INTO t_history_widget_displays(m_companies_id,tab_id,created) VALUES(?,?,?)',
                  [
                    list.companyList[obj.siteKey],
                    obj.sincloSessionId || obj.tabId,
                    new Date()], function(err, results) {
                    if (CommonUtil.isset(err)) {
                      console.log(
                          'RECORD INSERT ERROR: t_history_widget_displays(tab_id):' +
                          err);
                      return false;
                    }
                    if (!list.functionManager.isEnabled(obj.siteKey,
                        list.functionManager.keyList.enableRealtimeMonitor)
                        && CommonUtil.isset(
                            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId])
                        && !CommonUtil.isset(
                            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].historyId)) {
                      let historyManager = new HistoryManager();
                      historyManager.incrementWidgetCount(
                          list.companyList[obj.siteKey],
                          CommonUtil.formatDateParse());
                    }
                  });
            }
          });
    }
  });

  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  socket.on('sendAccessInfo', function(data) {
    var obj = JSON.parse(data);
    if (('contract' in obj) && ('hideRealtimeMonitor' in obj.contract) &&
        obj.contract.hideRealtimeMonitor === true) return false;
    obj.term = CommonUtil.timeCalculator(obj);

    obj = getConnectInfo(obj);

    if (getSessionId(obj.siteKey, obj.tabId, 'chat')) {
      obj.chat = getSessionId(obj.siteKey, obj.tabId, 'chat');
    }

    // IPアドレスの取得
    if (!(('ipAddress' in obj) && CommonUtil.isset(obj.ipAddress))) {
      obj.ipAddress = getIp(socket);
    }
    chatApi.getUnreadCnt(obj, function(ret) {
      obj['chatUnreadId'] = ret.chatUnreadId;
      obj['chatUnreadCnt'] = ret.chatUnreadCnt ? ret.chatUnreadCnt : 0;
      // TODO ここを要求したユーザのみに送るようにする
      // FIXME 初回表示時リアルタイムモニタ表示なしの場合はこのデータを送らないようにする
      emit.toCompany('receiveAccessInfo', obj, obj.siteKey);
      if (('contract' in obj) && ('chat' in obj.contract) &&
          obj.contract.chat === false) return false;
      chatApi.sendUnreadCnt('sendChatInfo', obj, false);
    });
  });
  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  // [管理]モニタリング開始
  socket.on('requestWindowSync', function(data) {
    var obj = JSON.parse(data);
    // 外部接続可能
    if (Number(obj.type) === 2) {
      // 同形ウィンドウを作成するための情報取得依頼
      if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) return false;
      SharedData.sincloCore[obj.siteKey][obj.tabId].shareWindowFlg = true;
      SharedData.sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
      emit.toUser('startWindowSync', data,
          getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
    }
    // 今まで通り
    else {
      // 同形ウィンドウを作成するための情報取得依頼
      if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) return false;
      SharedData.sincloCore[obj.siteKey][obj.tabId].shareWindowFlg = false;
      SharedData.sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
      SharedData.sincloCore[obj.siteKey][obj.tabId].syncSessionId = null;
      SharedData.sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
      emit.toUser('getWindowInfo', data,
          getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
    }

  });

  // 同形ウィンドウを作成するための情報受け取り
  socket.on('sendWindowInfo', function(data) {
    var obj = JSON.parse(data);
    // 同形ウィンドウを作成するための情報渡し
    emit.toUser('windowSyncInfo', obj,
        getSessionId(obj.siteKey, obj.tabId, 'syncHostSessionId'));
  });

  // 同形ウィンドウを作成するための情報受け取り(資料共有)
  socket.on('docShare', function(data) {
    var obj = JSON.parse(data);
    // 同形ウィンドウを作成するための情報渡し
    emit.toCompany('docShare', obj, obj.siteKey);
  });

  // iframe用（接続直後と企業側リロード時）
  socket.on('connectFrame', function(data) {
    var obj = JSON.parse(data);
    if (obj.siteKey) {
      // 外部接続フレームの場合
      var parentTabId = getSessionId(obj.siteKey, obj.tabId, 'parentTabId');
      if (parentTabId) {
        socket.join(obj.siteKey + emit.roomKey.frame);
        if (('responderId' in obj) && ('connectToken' in obj)) {
          SharedData.sincloCore[obj.siteKey][parentTabId].responderId = obj.responderId; // 対応ユーザーID
          SharedData.sincloCore[obj.siteKey][parentTabId].connectToken = obj.connectToken; // 接続トークン
        }
      } else {
        socket.join(obj.siteKey + emit.roomKey.client);
      }
    }
    if ((obj.tabId in SharedData.sincloCore[obj.siteKey])) {
      SharedData.sincloCore[obj.siteKey][obj.tabId].syncFrameSessionId = socket.id; // フレームのセッションID
      if (('responderId' in obj) && ('connectToken' in obj)) {
        SharedData.sincloCore[obj.siteKey][obj.tabId].responderId = obj.responderId; // 対応ユーザーID
        SharedData.sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken; // 接続トークン
      }
      if (getSessionId(obj.siteKey, obj.tabId, 'connectToken')) {
        obj.connectToken = getSessionId(obj.siteKey, obj.tabId, 'connectToken'); // 接続トークンを企業側へ
      }
      if (getSessionId(obj.siteKey, obj.tabId, 'coBrowseConnectToken')) {
        obj.coBrowseConnectToken = getSessionId(obj.siteKey, obj.tabId,
            'coBrowseConnectToken'); // LiveAssist用接続トークンを企業側へ
      }

      emit.toCompany('syncNewInfo', obj, obj.siteKey);
    } else {
      emit.toMine('syncStop', data, socket);
    }
  });

// FIX ME !!!!!!!
  // 継続接続(両用)
  socket.on('connectContinue', function(data) {
    var obj = JSON.parse(data);

    // 企業キーが取得できなければスルー
    if (obj.siteKey) {

      // 外部接続フレームの場合
      if (getSessionId(obj.siteKey, obj.tabId, 'parentTabId')) {
        socket.join(obj.siteKey + emit.roomKey.frame);
      } else {
        socket.join(obj.siteKey + emit.roomKey.client);
      }

      data.siteKey = obj.siteKey;
      emit.toUser('syncContinue', data, obj.to);
    }
  });

  // -----------------------------------------------------------------------
  //  モニタリング通信接続後
  // -----------------------------------------------------------------------
  // iframe作成通知(admin -> target)
  socket.on('requestSyncStart', function(data) {
    var obj = JSON.parse(data), tabId;
    // 企業キーが取得できなければスルー
    if (obj.siteKey) {
      // 外部接続フレームの場合
      if (getSessionId(obj.siteKey, obj.tabId, 'parentTabId')) {
        socket.join(obj.siteKey + emit.roomKey.frame);
      } else {
        socket.join(obj.siteKey + emit.roomKey.client);
      }

      data.siteKey = obj.siteKey;
    }
    if (obj.accessType !== 1) {
      tabId = obj.to; // host
      if ((obj.to in SharedData.sincloCore[obj.siteKey])) {
        SharedData.sincloCore[obj.siteKey][obj.to].syncSessionId = socket.id;
      }
    } else {
      tabId = obj.tabId; // guest
    }

    emit.toUser('syncStart', data,
        getSessionId(obj.siteKey, tabId, 'sessionId'));
    emit.toUser('syncStart', data,
        getSessionId(obj.siteKey, tabId, 'syncSessionId'));
    emit.toUser('syncStart', data,
        getSessionId(obj.siteKey, tabId, 'syncFrameSessionId'));
  });

  // 初期同期依頼
  socket.on('getSyncInfo', function(data) {
    var obj = JSON.parse(data);
    emit.toUser('syncElement', data,
        getSessionId(obj.siteKey, obj.tabId, 'syncSessionId'));
  });

  // 初期同期処理完了
  socket.on('syncCompleate', function(data) {
    var obj = JSON.parse(data);
    emit.toUser('syncEvStart', data,
        getSessionId(obj.siteKey, obj.to, 'sessionId'));
    emit.toUser('syncEvStart', data,
        getSessionId(obj.siteKey, obj.to, 'syncSessionId'));
  });

  /**
   * サブミットを使った、画面同期停止
   * */
  socket.on('requestSyncStopForSubmit', function(data) {
    var obj = JSON.parse(data);
    if (CommonUtil.isset(obj.connectToken)) {
      emit.toUser('syncStop', obj,
          getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
      emit.toUser('syncStopForSubmit', obj,
          getSessionId(obj.siteKey, obj.tabId, 'shareWindowId'));
      // 企業一括
      emit.toCompany('syncStop', data, obj.siteKey); // リアルタイムモニタを更新する為
    }
  });

  /**
   * 企業フレーム:1, 消費者フレーム:2,企業インライン:3, 消費者インライン:4
   * */
  socket.on('requestSyncStop', function(data) {
    var obj = JSON.parse(data);
    if (CommonUtil.isset(obj.connectToken)) {
      var parentId = false;
      obj.message = '切断を検知しました。';
      // 企業フレーム
      switch (Number(obj.type)) {
        case 1: // 企業フレーム
        case 2: // 消費者フレーム
          if (Number(obj.type) === 1) { // Was angry JS Lint
            parentId = obj.tabId.replace('_frame', '');
          }
          if ('parentId' in obj) {
            parentId = obj.parentId;
          }

          if (getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg')) {
            // 企業フレーム
            emit.toUser('syncStop', obj,
                getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg'));
            // 企業インライン
            // emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.tabId, 'syncSessionId'));
            // 消費者フレーム
            emit.toUser('syncStop', obj,
                getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
            // 消費者インライン
            emit.toUser('syncStop', obj,
                getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
            syncStopCtrl(obj.siteKey, obj.tabId, true);
          } else {
            syncStopCtrl(obj.siteKey, obj.tabId);
          }
          break;
        case 3: // 企業インライン
        case 4: // 消費者インライン
          // 相手先インラインフレーム
          emit.toUser('syncStop', obj,
              getSessionId(obj.siteKey, obj.to, 'sessionId'));
          // 消費者インライン
          break;
      }

      // 消費者親タブ
      var compData = obj;
      if (parentId) {
        emit.toUser('syncStop', data,
            getSessionId(obj.siteKey, parentId, 'sessionId')); // 消費者の親フレーム
        syncStopCtrl(obj.siteKey, parentId);
        compData.tabId = parentId;
      }

      // 企業一括
      emit.toCompany('syncStop', compData, obj.siteKey); // リアルタイムモニタを更新する為
    } else {
      emit.toCompany('unsetUser', data, obj.siteKey);
    }
  });

  /* ウィンドウリサイズとマウス位置 */
  socket.on('syncBrowserInfoFrame', function(data) {
    var obj = JSON.parse(data);

    if (CommonUtil.isset(obj.windowSize)) {
      emit.toUser('syncResponce', data,
          getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
    } else {

      if (getSessionId(obj.siteKey, obj.to, 'parentTabId')) {
        // 外部接続の場合（企業 -> 消費者）
        emit.toUser('syncResponce', data,
            getSessionId(obj.siteKey, obj.to, 'shareWindowId'));
      } else {
        if ('to' in obj) {
          emit.toUser('syncResponce', data,
              getSessionId(obj.siteKey, obj.to, 'sessionId'));
        } else {
          // 外部接続の場合（消費者 -> 企業）
          emit.toUser('syncResponce', data,
              getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
        }
      }
    }
  });

  /* スクロール位置 */
  socket.on('syncScrollInfo', function(data) {
    var obj = JSON.parse(data);
    emit.toUser('syncResponce', data,
        getSessionId(obj.siteKey, obj.to, 'sessionId'));
  });

  socket.on('syncBrowserInfo', function(data) {
    var obj = JSON.parse(data);
    if (CommonUtil.isset(obj.windowSize)) {
      emit.toUser('syncResponce', data,
          getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
    } else {
      emit.toUser('syncResponce', data,
          getSessionId(obj.siteKey, obj.to, 'sessionId'));
    }
  });

  socket.on('syncChangeEv', function(data) {
    var obj = JSON.parse(data);
    emit.toUser('syncResponceEv', data,
        getSessionId(obj.siteKey, obj.to, 'sessionId'));
  });

  socket.on('syncReconnectConfirm', function(data) {
    var obj = JSON.parse(data), timer, i = 1;
    timer = setInterval(function() {
      var sessionId = getSessionId(obj.siteKey, obj.to, 'sessionId');
      if (sessionId && SharedData.connectList[sessionId]) {
        emit.toUser('syncStart', data,
            getSessionId(obj.siteKey, obj.to, 'sessionId'));
        emit.toUser('syncStart', data,
            getSessionId(obj.siteKey, obj.to, 'syncSessionId'));
        clearInterval(timer);
        // sessionIdが消えてる可能性があるため、企業側フレームのsocket.idを再セット
        SharedData.sincloCore[obj.siteKey][obj.to].syncFrameSessionId = socket.id;
      }
      if (i === 5) {
        clearInterval(timer);
        emit.toUser('syncStop', {message: '接続できませんでした。'}, socket.id);
      }
      i++;
    }, 1000);
  });

  socket.on('reqSyncBrowserCtrl', function(data) {
    var obj = JSON.parse(data);
    emit.toUser('syncBrowserCtrl', data,
        getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
  });

  socket.on('sendOperatorStatus', function(data) {
    var obj = JSON.parse(data),
        sendData = {
          siteKey: obj.siteKey,
          userId: obj.userId,
          active: obj.active
        };
    if (!CommonUtil.isset(SharedData.activeOperator[obj.siteKey])) {
      SharedData.activeOperator[obj.siteKey] = {};
    }
    // 在席中
    if (obj.active) {
      if (!CommonUtil.isset(
          SharedData.activeOperator[obj.siteKey][obj.userId])) {
        SharedData.activeOperator[obj.siteKey][obj.userId] = socket.id;
        console.log(
            '【' + obj.siteKey + '】オペレータ 離席 => 待機 userId : ' + obj.userId);
        if (obj.hasOwnProperty('scNum')) {
          if (!SharedData.scList.hasOwnProperty(obj.siteKey)) {
            SharedData.scList[obj.siteKey] = {user: {}, cnt: {}};
          }
          SharedData.scList[obj.siteKey].user[obj.userId] = obj.scNum;
          SharedData.scList[obj.siteKey].cnt[obj.userId] = chatApi.calcScNum(
              obj,
              obj.userId);
          console.log('【' + obj.siteKey + '】対応上限数設定 ' +
              SharedData.scList[obj.siteKey].cnt[obj.userId] + ' / ' +
              SharedData.scList[obj.siteKey].user[obj.userId] + ' userId : ' +
              obj.userId);
        }
      }
    }
    // 退席中
    else {
      if (CommonUtil.isset(
          SharedData.activeOperator[obj.siteKey][obj.userId])) {
        delete SharedData.activeOperator[obj.siteKey][obj.userId];
        console.log(
            '【' + obj.siteKey + '】オペレータ 待機 => 離席 userId : ' + obj.userId);
      }
      if (SharedData.scList.hasOwnProperty(obj.siteKey) &&
          SharedData.scList[obj.siteKey].cnt.hasOwnProperty(obj.userId)) {
        delete SharedData.scList[obj.siteKey].cnt[obj.userId];
        delete SharedData.scList[obj.siteKey].user[obj.userId];
      }
    }

    if (SharedData.scList.hasOwnProperty(obj.siteKey)) {
      sendData.scInfo = SharedData.scList[obj.siteKey].cnt;
    }
    var keys = Object.keys(SharedData.activeOperator[obj.siteKey]);
    sendData.count = keys.length;
    emit.toCompany('activeOpCnt', sendData, obj.siteKey);
  });

  socket.on('sendOtherTabURL', function(d) {
    var obj = JSON.parse(d), tabId;

    // インラインフレームに送る
    emit.toUser('receiveOtherTabURL', obj,
        getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
  });

  socket.on('reqUrlChecker', function(d) {
    var obj = JSON.parse(d), shareWindowId, toTabId;
    emit.toUser('resUrlChecker', d,
        getSessionId(obj.siteKey, obj.to, 'sessionId'));
    // 外部接続中であれば、ページ遷移履歴として遷移先をフレームに送る(再接続の場合を除く)
    shareWindowId = getSessionId(obj.siteKey, obj.to, 'shareWindowId');
    if (shareWindowId && !obj.reconnectFlg) {
      emit.toUser('resUrlChecker', d, shareWindowId);
    }
    // 企業側フレームへも送る(再接続の場合を除く)
    toTabId = getSessionId(obj.siteKey, obj.tabId, 'toTabId') || obj.tabId;
    if (toTabId === obj.tabId && !obj.reconnectFlg) {
      emit.toUser('resUrlChecker', d,
          getSessionId(obj.siteKey, toTabId, 'syncFrameSessionId'));
    }
  });

  socket.on('syncLocationOfFrame', function(d) {
    var obj = JSON.parse(d), shareWindowId, companyFrameId;
    // 外部接続中であれば、ページ遷移履歴として遷移先をフレームに送る
    shareWindowId = getSessionId(obj.siteKey, obj.tabId, 'shareWindowId');
    if (shareWindowId) {
      emit.toUser('syncLocationOfFrame', d, shareWindowId);
    }
    // 企業側フレームへも送る
    companyFrameId = getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId');
    if (companyFrameId) {
      emit.toUser('syncLocationOfFrame', d, companyFrameId);
    }
  });

  // -----------------------------------------------------------------------
  //  チャット関連
  // -----------------------------------------------------------------------

  // 一括：チャットデータ取得
  socket.on('getChatMessage', function(d) {
    var obj = JSON.parse(d);

    chatApi.get(obj);
  });

  // 都度：チャットデータ取得(オートメッセージのみ)
  socket.on('sendAutoChatMessage', function(d) {
    var obj = JSON.parse(d);
    var chat = JSON.parse(JSON.stringify(obj));
    chat.messageType = obj.isAutoSpeech ?
        chatApi.cnst.observeType.autoSpeech :
        chatApi.cnst.observeType.auto;
    chat.created = new Date();
    chat.sort = CommonUtil.fullDateTime(chat.created);

    var sincloSession = SharedData.sincloCore[chat.siteKey][chat.sincloSessionId];
    if (CommonUtil.isset(sincloSession) &&
        CommonUtil.isKeyExists(sincloSession, 'autoMessages')) {
      SharedData.sincloCore[chat.siteKey][chat.sincloSessionId].autoMessages[chat.chatId] = chat;
    } else {
      console.log(
          'sendAutoChatMessage::sincloSession : ' + chat.sincloSessionId +
          'is null.');
      return false;
    }
    if (!list.functionManager.isEnabled(chat.siteKey,
        list.functionManager.keyList.monitorPollingMode)
        && list.functionManager.isEnabled(chat.siteKey,
            list.functionManager.keyList.enableRealtimeMonitor)) {
      emit.toCompany('resAutoChatMessage', chat, chat.siteKey);
    }
    emit.toSameUser('resAutoChatMessage', chat, chat.siteKey,
        chat.sincloSessionId);
  });

  // 一括：チャットデータ取得(オートメッセージのみ)
  socket.on('getAutoChatMessages', function(d) {
    var obj = JSON.parse(d);
    if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) return false;
    var sId = getSessionId(obj.siteKey, obj.tabId, 'sessionId');
    var sincloSessionId = getSessionId(obj.siteKey, obj.tabId,
        'sincloSessionId');
    obj.messageType = chatApi.cnst.observeType.auto;
    obj.sendTo = socket.id;
    obj.sincloSessionId = sincloSessionId;
    emit.toUser('sendReqAutoChatMessages', obj, sId);

    // ユーザーがチャット中の場合
    if (getSessionId(obj.siteKey, obj.tabId, 'chatSessionId')) {
      var sessionId = getSessionId(obj.siteKey, obj.tabId, 'chatSessionId');
      emit.toUser('reqTypingMessage',
          {
            siteKey: obj.siteKey,
            from: obj.mUserId,
            tabId: obj.tabId,
            sincloSessionId: sincloSessionId
          },
          sessionId);
    }
  });

  // 一括：チャットデータ取得(オートメッセージのみ)
  socket.on('sendAutoChatMessages', function(d) {
    var obj = JSON.parse(d);

    var setList = {};
    for (var i = 0; i < obj.messages.length; i++) {
      if (!CommonUtil.isset(obj.messages[i]) ||
          !CommonUtil.isset(obj.messages[i].created)) continue;
      var created = new Date(obj.messages[i].created);
      obj.messages[i].messageType = chatApi.cnst.observeType.auto;
      setList[CommonUtil.fullDateTime(Date.parse(created))] = obj.messages[i];
    }
    if (CommonUtil.isset(obj.scenarios)) {
      for (var i = 0; i < obj.scenarios.length; i++) {
        if (!CommonUtil.isset(obj.scenarios[i]) ||
            !CommonUtil.isset(obj.scenarios[i].created)) continue;
        var created = new Date(obj.scenarios[i].created);
        setList[CommonUtil.fullDateTime(
            Date.parse(created))] = obj.scenarios[i];
      }
    }
    var ret = {};
    ret.messages = CommonUtil.objectSort(setList);
    ret.chatToken = obj.chatToken;
    ret.tabId = obj.tabId;
    ret.historyId = getSessionId(obj.siteKey, obj.tabId, 'historyId');
    ret.sincloSessionId = getSessionId(obj.siteKey, obj.tabId,
        'sincloSessionId');
    emit.toUser('resAutoChatMessages', ret, obj.sendTo);
  });

  // シナリオの状態を同期させる
  socket.on('syncScenarioData', function(d) {
    var obj = JSON.parse(d);
    var dataSet = {
      tabId: obj.tabId,
      sincloSessionId: obj.sincloSessionId,
      param: obj.detail,
      scenarioSeq: obj.scenarioSeq,
      hearingSeq: obj.hearingSeq,
      otherInformation: obj.otherInformation
    };
    try {
      emit.toSameUser('syncScenarioDataResult', dataSet, obj.siteKey,
          obj.sessionID);
    } catch (e) {
      console.log('ERROR DETECTED!! >> ' + e);
    }
  });

  // チャット開始
  socket.on('chatStart', function(d) {
    var obj = JSON.parse(d), date = new Date(),
        now = CommonUtil.fullDateTime(date),
        type = chatApi.cnst.observeType.start;
    var logToken = makeToken();
    // TODO 履歴IDチェック
    try {
      if (CommonUtil.isset(SharedData.sincloCore[obj.siteKey]) &&
          CommonUtil.isset(SharedData.sincloCore[obj.siteKey][obj.tabId])) {
        SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadId = null;
        SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt = 0;
        console.log('reset chatUnreadCnt');
      }
      Object.keys(SharedData.sincloCore[obj.siteKey]).some(function(key) {
        if (SharedData.sincloCore[obj.siteKey][key].sincloSessionId ===
            obj.sincloSessionId) {
          SharedData.sincloCore[obj.siteKey][key].chatUnreadId = null;
          SharedData.sincloCore[obj.siteKey][key].chatUnreadCnt = 0;
          return true;
        }
      });
    } catch (e) {
    }

    console.log('chatStart-0: [' + logToken +
        '] >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');
    console.log('chatStart-1: [' + logToken + '] ' + d);
    if (SharedData.sincloCore[obj.siteKey][obj.tabId] === null) {

      DBConnector.getPool().query(
          'SELECT mu.settings FROM m_users as mu WHERE mu.id = ? AND mu.del_flg != 1 AND mu.m_companies_id = ?',
          [obj.userId, list.companyList[obj.siteKey]], function(err, result) {
            if (err !== null && err !== '') return false;
            var settingObj = '';
            var profileIcon = '';
            try {
              if (CommonUtil.isset(result[0]['settings'])) {
                settingObj = result[0]['settings'];
                profileIcon = JSON.parse(settingObj)['profileIcon'];
              }
            } catch (e) {
            }

            emit.toMine('chatStartResult', {
              ret: false,
              siteKey: obj.siteKey,
              profileIcon: profileIcon,
              userId: SharedData.sincloCore[obj.siteKey][obj.tabId].chat,
              sincloSessionId: obj.sincloSessionId
            }, socket);
          });

      var userId = (getSessionId(obj.siteKey, obj.tabId, 'chat')) ?
          SharedData.sincloCore[obj.siteKey][obj.tabId].chat :
          'undefined userId.';
      console.log('chatStart-2: [' + logToken + '] ' + JSON.stringify({
        ret: false,
        siteKey: obj.siteKey,
        userId: userId
      }));
    } else {
      var sendData = {
        ret: true,
        messageType: type,
        tabId: obj.tabId,
        siteKey: obj.siteKey,
        userId: obj.userId,
        hide: false,
        created: now
      };
      var scInfo = '';

      SharedData.sincloCore[obj.siteKey][obj.tabId].chat = obj.userId;
      if (!CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].chat)) {
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].chat = {};
      }
      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].chat[obj.tabId] = obj.userId;
      SharedData.sincloCore[obj.siteKey][obj.tabId].chatSessionId = socket.id;
      // サイトとして初チャット開始
      if (!(obj.siteKey in SharedData.c_connectList)) {
        SharedData.c_connectList[obj.siteKey] = {};
      }
      // タブに対して初チャット開始
      if (!(obj.tabId in SharedData.c_connectList[obj.siteKey])) {
        SharedData.c_connectList[obj.siteKey][obj.tabId] = {};
      }
      // タブに対して複数回目のチャット開始
      else {
        var keys = Object.keys(
            SharedData.c_connectList[obj.siteKey][obj.tabId]);
        // 横取り（最後のSharedData.c_connectListが"start"でない）
        if (SharedData.c_connectList[obj.siteKey][obj.tabId][keys[keys.length -
        1]].type === 'start') {

          /* チャット対応上限の処理（対応人数減算の処理） */
          if (SharedData.scList.hasOwnProperty(obj.siteKey)) {
            var userId = SharedData.c_connectList[obj.siteKey][obj.tabId][keys[keys.length -
            1]].userId;
            if (SharedData.scList[obj.siteKey].cnt.hasOwnProperty(userId)) {
              SharedData.scList[obj.siteKey].cnt[userId]--; // 対応人数を減算する
              console.log('【' + obj.siteKey + '】chatStart::対応上限数設定 ' +
                  SharedData.scList[obj.siteKey].cnt[userId] + ' / ' +
                  SharedData.scList[obj.siteKey].user[userId] + ' userId : ' +
                  userId);
            }
          }
          /* チャット対応上限の処理（対応人数減算の処理） */

          sendData.hide = true;
        }
      }

      DBConnector.getPool().query(
          'SELECT mu.id, mu.display_name, wid.style_settings FROM m_users as mu LEFT JOIN m_widget_settings AS wid ON ( wid.m_companies_id = mu.m_companies_id ) WHERE mu.id = ? AND mu.del_flg != 1 AND wid.del_flg != 1 AND wid.m_companies_id = ?',
          [obj.userId, list.companyList[obj.siteKey]], function(err, rows) {
            if (err !== null && err !== '') return false; // DB接続断対応

            sendData.userName = 'オペレーター';

            var logData3 = (typeof (rows) === 'object') ?
                JSON.stringify(rows) :
                'typeof: ' + typeof (rows);
            console.log('chatStart-3: [' + logToken + '] ' + logData3);
            if (rows && rows[0]) {
              var settings = JSON.parse(rows[0].style_settings);
              // 表示名をウィジェットで表示する場合
              var userName = rows[0].display_name;
              sendData.userName = userName;

              SharedData.c_connectList[obj.siteKey][obj.tabId][now] = {
                messageType: type,
                type: 'start',
                userName: userName,
                userId: obj.userId
              };

              DBConnector.getPool().query(
                  'SELECT mu.settings FROM m_users as mu WHERE mu.id = ? AND mu.del_flg != 1 AND mu.m_companies_id = ?',
                  [obj.userId, list.companyList[obj.siteKey]],
                  function(err, result) {
                    if (err !== null && err !== '') return false;
                    var settingObj = '';
                    var profileIcon = '';
                    try {
                      if (CommonUtil.isset(result[0]['settings'])) {
                        settingObj = result[0]['settings'];
                        profileIcon = JSON.parse(settingObj)['profileIcon'];
                      }
                    } catch (e) {
                    }

                    sendData.profileIcon = profileIcon;
                    emit.toSameUser('chatStartResult', sendData, obj.siteKey,
                        obj.sincloSessionId);
                  });

              //emit.toUser("chatStartResult", sendData, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));

              /* チャット対応上限の処理（対応人数加算の処理） */
              if (SharedData.scList.hasOwnProperty(obj.siteKey) &&
                  SharedData.scList[obj.siteKey].cnt.hasOwnProperty(
                      obj.userId)) {
                SharedData.scList[obj.siteKey].cnt[obj.userId]++; // 対応人数を加算する
                scInfo = SharedData.scList[obj.siteKey].cnt;
                console.log('【' + obj.siteKey + '】chatStart::対応上限数設定 ' +
                    SharedData.scList[obj.siteKey].cnt[obj.userId] + ' / ' +
                    SharedData.scList[obj.siteKey].user[obj.userId] +
                    ' userId : ' +
                    obj.userId);
              }
              /* チャット対応上限の処理（対応人数加算の処理） */

              // DBに書き込み
              var visitorId = SharedData.connectList[SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId].userId ?
                  SharedData.connectList[SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId].userId :
                  '';

              //応対数検索、登録
              getConversationCountUser(visitorId, function(results) {
                if (results !== null) {
                  //カウント数が取れなかったとき
                  if (Object.keys(results) && Object.keys(results).length ===
                      0) {
                    obj.messageDistinction = 1;
                    //visitors_id,カウント数一件を登録
                    DBConnector.getPool().query(
                        'INSERT INTO t_conversation_count(visitors_id,conversation_count) VALUES(?,?)',
                        [visitorId, 1], function(err, result) {
                          if (CommonUtil.isset(err)) {
                            console.log(
                                'RECORD INSERT ERROR: t_convertsation_count(visitors_id,conversation_count):' +
                                err);
                            return false;
                          }
                        });
                  }
                  //カウント数が取れたとき
                  else {
                    obj.messageDistinction = results[0].conversation_count;
                  }

                  var insertData = {
                    ret: true,
                    hide: sendData.hide,
                    scInfo: scInfo,
                    siteKey: obj.siteKey,
                    tabId: obj.tabId,
                    sincloSessionId: obj.sincloSessionId,
                    visitorsId: visitorId,
                    userId: obj.userId,
                    chatMessage: '入室',
                    messageType: 998,
                    messageDistinction: obj.messageDistinction,
                    userName: userName,
                    created: date
                  };

                  var logData4 = (typeof (insertData) === 'object') ?
                      JSON.stringify(insertData) :
                      'typeof: ' + typeof (insertData);
                  console.log('chatStart-4: [' + logToken + '] ' + logData4);
                  chatApi.notifyCommit('chatStartResult', insertData);
                }
              });
            }
            try {
              var logData5 = (SharedData.sincloCore.hasOwnProperty(
                  obj.siteKey) &&
                  typeof (SharedData.sincloCore[obj.siteKey]) === 'object') ?
                  JSON.stringify(SharedData.sincloCore[obj.siteKey]) :
                  'typeof: ' + typeof (SharedData.sincloCore[obj.siteKey]);
              console.log('chatStart-5: [' + logToken + '] ' +
                  JSON.stringify(SharedData.sincloCore[obj.siteKey]));
              console.log('chatStart-6: [' + logToken +
                  '] <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
            } catch (e) {
            }
          });
    }
  });

  // チャット終了
  socket.on('chatEnd', function(d) {
    var obj = JSON.parse(d), date = new Date(), now = CommonUtil.fullDateTime(),
        type = chatApi.cnst.observeType.end;

    var keys = [];
    var userName = '';
    if (CommonUtil.isset(SharedData.c_connectList[obj.siteKey][obj.tabId])) {
      keys = Object.keys(SharedData.c_connectList[obj.siteKey][obj.tabId]);
      userName = SharedData.c_connectList[obj.siteKey][obj.tabId][keys[keys.length -
      1]].user;
      SharedData.c_connectList[obj.siteKey][obj.tabId][now] = {
        type: 'end',
        userName: userName,
        userId: obj.userId,
        messageType: type
      };
    }

    /* チャット対応上限の処理（対応人数減算の処理） */
    if (SharedData.scList.hasOwnProperty(obj.siteKey) &&
        SharedData.scList[obj.siteKey].cnt.hasOwnProperty(obj.userId)) {
      if (SharedData.scList[obj.siteKey].cnt[obj.userId] > 0) {
        SharedData.scList[obj.siteKey].cnt[obj.userId]--; // 対応人数を減算する

      } else {
        SharedData.scList[obj.siteKey].cnt[obj.userId] = 0;
      }
      console.log('【' + obj.siteKey + '】chatEnd::対応上限数設定 ' +
          SharedData.scList[obj.siteKey].cnt[obj.userId] + ' / ' +
          SharedData.scList[obj.siteKey].user[obj.userId] + ' userId : ' +
          obj.userId);
    }
    /* チャット対応上限の処理（対応人数減算の処理） */
    scInfo = (SharedData.scList.hasOwnProperty(obj.siteKey)) ?
        SharedData.scList[obj.siteKey].cnt :
        {};

    if (CommonUtil.isset(SharedData.sincloCore[obj.siteKey][obj.tabId])
        && CommonUtil.isset(SharedData.sincloCore[obj.siteKey][obj.tabId].chat)
        && CommonUtil.isset(
            SharedData.connectList[SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId])
        && CommonUtil.isset(
            SharedData.connectList[SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId].userId)) {
      SharedData.sincloCore[obj.siteKey][obj.tabId].chat = null;
      SharedData.sincloCore[obj.siteKey][obj.tabId].chatSessionId = null;
      if (CommonUtil.isset(SharedData.sincloCore[obj.siteKey]) &&
          CommonUtil.isset(
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) &&
          CommonUtil.isset(
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].chat)) {
        delete SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].chat[obj.tabId];
        if (Object.keys(
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].chat).length ===
            0) {
          delete SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].chat;
        }
      }

      //emit.toUser("chatEndResult", {ret: true, messageType: type}, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
      emit.toSameUser('chatEndResult', {ret: true, messageType: type},
          obj.siteKey, obj.sincloSessionId);
      // DBに書き込み
      var visitorId = SharedData.connectList[SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId].userId ?
          SharedData.connectList[SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId].userId :
          '';

      //応対数検索、登録
      getConversationCountUser(visitorId, function(results) {
        if (results !== null) {
          //カウントが取れたとき
          if (Object.keys(results) && Object.keys(results).length !== 0) {
            obj.messageDistinction = results[0].conversation_count;
            //カウント数一件追加
            DBConnector.getPool().query(
                'UPDATE t_conversation_count SET conversation_count = ? WHERE visitors_id = ?',
                [results[0].conversation_count + 1, visitorId],
                function(err, result) {
                  if (CommonUtil.isset(err)) {
                    console.log(
                        'RECORD UPDATE ERROR: t_conversation_count(conversation_count):' +
                        err);
                    return false;
                  }
                });
          }

          var insertData = {
            ret: true,
            scInfo: scInfo,
            siteKey: obj.siteKey,
            tabId: obj.tabId,
            sincloSessionId: obj.sincloSessionId,
            visitorsId: visitorId,
            userId: obj.userId,
            chatMessage: '退室',
            messageType: type,
            messageDistinction: obj.messageDistinction,
            userName: userName,
            created: date
          };
          chatApi.notifyCommit('chatEndResult', insertData);
        }
      });
    } else {
      // 既にいないユーザーの退室
      var notifyData = {
        ret: true,
        scInfo: scInfo,
        siteKey: obj.siteKey,
        tabId: obj.tabId,
        sincloSessionId: obj.sincloSessionId,
        visitorsId: '',
        userId: obj.userId,
        chatMessage: '退室',
        messageType: type,
        messageDistinction: obj.messageDistinction,
        userName: userName,
        created: now
      };
      emit.toCompany('chatEndResult', notifyData, obj.siteKey);
    }
  });

  function handleCogmoAttendAPI(obj, isInitialized) {
    let apiCaller = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].apiCaller;
    let isFeedback = apiCaller.isFeedbackMessage();
    let isExitOnConversation = apiCaller.isExitOnConversation();
    let isMessageButton = obj.chatMessage.indexOf('button_') !== -1;
    let customerSendData = {};
    let customerMessageInsertResult = {};
    apiCaller.saveCustomerMessage(
        SharedData.sincloCore[obj.siteKey][obj.tabId].historyId,
        obj.stayLogsId,
        list.companyList[obj.siteKey],
        obj.userId,
        obj.chatMessage.replace('button_', '').replace('\n', ''),
        obj.messageDistinction,
        obj.created).then((resultData) => {
      ChatLogTimeManager.saveTime(resultData.insertId,
          SharedData.sincloCore[obj.siteKey][obj.tabId].historyId, 2,
          obj.created);
      customerMessageInsertResult = resultData;
      customerSendData = {
        tabId: obj.tabId,
        sincloSessionId: obj.sincloSessionId,
        chatId: resultData.insertId,
        messageType: 1,
        created: resultData.created,
        sort: CommonUtil.fullDateTime(resultData.created),
        ret: true,
        chatMessage: resultData.message.replace('button_', '').
            replace('\n', ''),
        message: resultData.message.replace('button_', '').replace('\n', ''),
        siteKey: obj.siteKey,
        matchAutoSpeech: true,
        isScenarioMessage: false
      };
      if (isInitialized) {
        emit.toSameUser('sendChatResult', customerSendData, obj.siteKey,
            obj.sincloSessionId);
        emit.toCompany('sendChatResult', customerSendData, obj.siteKey);
      }
      if (isFeedback && !isExitOnConversation) {
        if (resultData.message.indexOf('はい') !== -1) {
          return apiCaller.sendTo(apiCaller.messageType.FEEDBACK_YES, null);
        } else if (resultData.message.indexOf('いいえ') !== -1) {
          return apiCaller.sendTo(apiCaller.messageType.FEEDBACK_NO, null);
        }
      } else if (isMessageButton) {
        return apiCaller.sendTo(apiCaller.messageType.PUSH_BUTTON,
            customerSendData.chatMessage);
      } else {
        return apiCaller.sendTo(apiCaller.messageType.TEXT,
            customerSendData.chatMessage);
      }
    }).then((text) => {
      if (Array.isArray(text)) {
        for (let i = 0; i < text.length; i++) {
          setTimeout(() => {
            apiCaller.saveMessage(
                SharedData.sincloCore[obj.siteKey][obj.tabId].historyId,
                obj.stayLogsId,
                list.companyList[obj.siteKey],
                obj.userId,
                text[i],
                obj.messageDistinction,
                obj.created).then((resultData) => {
              let sendData = {
                tabId: obj.tabId,
                sincloSessionId: obj.sincloSessionId,
                chatId: resultData.insertId,
                messageType: 81,
                created: resultData.created,
                sort: CommonUtil.fullDateTime(resultData.created),
                ret: true,
                chatMessage: resultData.message,
                message: resultData.message,
                siteKey: obj.siteKey,
                matchAutoSpeech: true,
                isScenarioMessage: false,
                isFeedbackMsg: (SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].apiCaller.isFeedbackMessage() &&
                    i === text.length - 1),
                isExitOnConversation: SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].apiCaller.isExitOnConversation()
              };
              emit.toSameUser('sendChatResult', sendData, obj.siteKey,
                  obj.sincloSessionId);
              emit.toCompany('sendChatResult', sendData, obj.siteKey);
            });
          }, i * 100);
        }
      }
      if (apiCaller.isSwitchingOperator()) {
        obj.notifyToCompany = true;
        obj.matchAutoSpeech = false;
        obj.isScenarioMessage = false;
        obj.initialNotification = true;
        //リクエストメッセージの場合
        if (obj.messageRequestFlg == 1) {
          //消費者が初回メッセージを送る前にオペレータが入室した場合
          DBConnector.getPool().query(
              'SELECT id FROM t_history_chat_logs WHERE visitors_id = ? and t_histories_id = ? and message_distinction = ? and message_type = 998',
              [obj.userId, obj.historyId, obj.messageDistinction],
              function(err, results) {
                if (Object.keys(results) && Object.keys(result).length !== 0) {
                  obj.messageRequestFlg = 0;
                }
                chatApi._handleInsertData(null, customerMessageInsertResult,
                    obj, true, customerSendData);
              });
        } else {
          chatApi._handleInsertData(null, customerMessageInsertResult, obj,
              true, customerSendData);
        }
        if (ack) ack();
      } else {
        // 有人に切り替えないのであれば何もしない
      }
    }, function(err) {
      console.log('COGMO ATTEND CALLBACK REJECT : ' + err);
      let errorDatetime = new Date();
      let sendData = {
        tabId: obj.tabId,
        sincloSessionId: obj.sincloSessionId,
        chatId: null,
        messageType: 81,
        created: CommonUtil.fullDateTime(errorDatetime),
        sort: CommonUtil.fullDateTime(errorDatetime),
        ret: true,
        chatMessage: 'システムエラーです。もう一度、メッセージを入力してください。',
        message: 'システムエラーです。もう一度、メッセージを入力してください。',
        siteKey: obj.siteKey,
        matchAutoSpeech: true,
        isScenarioMessage: false,
        isFeedbackMsg: false,
        isExitOnConversation: true
      };
      emit.toSameUser('sendChatResult', sendData, obj.siteKey,
          obj.sincloSessionId);
      emit.toCompany('sendChatResult', sendData, obj.siteKey);
    });
  }

  //新着チャット
  socket.on('sendChat', function(d, ack) {
    var obj = JSON.parse(d);
    if (obj.messageType !== 2
        && !list.functionManager.isEnabled(obj.siteKey,
            list.functionManager.keyList.enableRealtimeMonitor)
        && CommonUtil.isset(
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId])
        && !CommonUtil.isset(
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].historyId)) {
      let historyManager = new HistoryManager();
      let customerInfoManager = new CustomerInfoManager();
      let target = SharedData.sincloCore[obj.siteKey][obj.tabId];
      obj = Object.assign(obj, target);
      historyManager.addHistory(obj).then((result) => {
        emit.toSameUser('setHistoryId', result, obj.siteKey,
            obj.sincloSessionId);
        SharedData.sincloCore[obj.siteKey][obj.tabId]['historyId'] = result.historyId;
        SharedData.sincloCore[obj.siteKey][obj.tabId]['stayLogsId'] = result.stayLogsId;
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]['historyId'] = result.historyId;
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]['stayLogsId'] = result.stayLogsId;
        processSendChat(result, ack);
      });
    } else {
      processSendChat(obj, ack);
    }
  });

  var processSendChat = function(obj, ack) {
    //応対件数検索、登録
    getConversationCountUser(obj.userId, function(results) {
      if (results !== null) {
        //カウント数が取れなかった場合
        if (Object.keys(results) && Object.keys(results).length == 0) {
          obj.messageDistinction = 1;
        }
        //カウント数が取れた場合
        else {
          obj.messageDistinction = results[0].conversation_count;
        }
        if (list.functionManager.isEnabled(obj.siteKey,
            list.functionManager.keyList.useCogmoAttendApi)
            && !SharedData.sincloCore[obj.siteKey][obj.tabId].chat
            &&
            (!CommonUtil.isset(
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].apiCaller) ||
                !SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].apiCaller.isSwitchingOperator())) {
          if (!CommonUtil.isset(
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].apiCaller)) {
            // list.functionManager.isEnabledでCogmoAttendAPIのsystemUUIDが返却される
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].apiCaller = new CogmoAttendAPICaller(
                list.functionManager.isEnabled(obj.siteKey,
                    list.functionManager.keyList.useCogmoAttendApi));
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].apiCaller.init(
                SharedData.sincloCore[obj.siteKey][obj.tabId].historyId, obj,
                CommonUtil.fullDateTime(), emit).then(() => {
              handleCogmoAttendAPI(obj, false);
            });
          } else {
            handleCogmoAttendAPI(obj, true);
          }
        } else {
          //リクエストメッセージの場合
          if (obj.messageRequestFlg == 1) {
            //消費者が初回メッセージを送る前にオペレータが入室した場合
            DBConnector.getPool().query(
                'SELECT id FROM t_history_chat_logs WHERE visitors_id = ? and t_histories_id = ? and message_distinction = ? and message_type = 998',
                [obj.userId, obj.historyId, obj.messageDistinction],
                function(err, result) {
                  if (Object.keys(results) && Object.keys(result).length !==
                      0) {
                    obj.messageRequestFlg = 0;
                  }
                  chatApi.set(obj);
                });
          } else {
            chatApi.set(obj);
          }
          if (ack) ack();
        }
      }
    });
  };

  //新着チャット
  socket.on('sendFile', function(d, ack) {
    var obj = JSON.parse(d);
    //応対件数検索、登録
    getConversationCountUser(obj.userId, function(results) {
      if (results !== null) {
        //カウント数が取れなかった場合
        if (Object.keys(results) && Object.keys(results).length == 0) {
          obj.messageDistinction = 1;
        }
        //カウント数が取れた場合
        else {
          obj.messageDistinction = results[0].conversation_count;
        }
        //リクエストメッセージの場合
        if (obj.messageRequestFlg == 1) {
          //消費者が初回メッセージを送る前にオペレータが入室した場合
          DBConnector.getPool().query(
              'SELECT id FROM t_history_chat_logs WHERE visitors_id = ? and t_histories_id = ? and message_distinction = ? and message_type = 998',
              [obj.userId, obj.historyId, obj.messageDistinction],
              function(err, result) {
                if (Object.keys(results) && Object.keys(result).length !== 0) {
                  obj.messageRequestFlg = 0;
                }
                chatApi.set(obj);
              });
        } else {
          chatApi.set(obj);
        }
      }
    });
    if (ack) ack();
  });

  // オートチャット
  socket.on('sendAutoChat', function(d) {
    var obj = JSON.parse(d);
    var getConversationCallback = function(results) {
      var messageDistinction;
      if (results !== null) {
        //カウント数が取れなかったとき
        if (Object.keys(results) && Object.keys(results).length === 0) {
          messageDistinction = 1;
        }
        //カウント数が取れたとき
        else {
          messageDistinction = results[0].conversation_count;
        }
        var loop = function(err, rows) {
          if (!err && (rows && rows[0])) {
            var activity = JSON.parse(rows[0].activity);
            if (activity.cv == 1) {
              var ret = {
                siteKey: obj.siteKey,
                tabId: obj.tabId,
                sincloSessionId: obj.sincloSessionId,
                userId: obj.userId,
                mUserId: null,
                chatMessage: activity.message,
                messageType: rows[0].auto_message_type,
                created: rows[0].inputed ? rows[0].inputed : new Date(),
                messageDistinction: messageDistinction,
                achievementFlg: 0,
                sendMailFlg: rows[0].send_mail_flg,
                autoMessageId: rows[0].id
              };
            } else {
              var ret = {
                siteKey: obj.siteKey,
                tabId: obj.tabId,
                sincloSessionId: obj.sincloSessionId,
                userId: obj.userId,
                mUserId: null,
                chatMessage: activity.message,
                messageType: rows[0].auto_message_type,
                created: rows[0].inputed ? rows[0].inputed : new Date(),
                messageDistinction: messageDistinction,
                sendMailFlg: rows[0].send_mail_flg,
                autoMessageId: rows[0].id
              };
            }
            chatApi.set(ret);
          }
        };
        for (var i = 0; obj.messageList.length > i; i++) {
          var message = obj.messageList[i];
          DBConnector.getPool().query(
              'SELECT *, ? as inputed, ? as auto_message_type FROM t_auto_messages WHERE id = ?  AND m_companies_id = ? AND del_flg = 0 AND active_flg = 0 AND action_type = 1',
              [
                message.created,
                message.isAutoSpeech ?
                    chatApi.cnst.observeType.autoSpeech :
                    chatApi.cnst.observeType.auto,
                message.chatId,
                list.companyList[obj.siteKey]], loop);
          var sincloSession = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId];
          if (CommonUtil.isset(sincloSession) && (message.chatId in
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages)) {
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages[message.chatId]['applied'] = true;
          } else if (CommonUtil.isset(sincloSession) &&
              CommonUtil.isset(sincloSession.autoMessages)) {
            message.created = new Date();
            message.sort = CommonUtil.fullDateTime(message.created);
            message.applied = true;
            message.messageType = chatApi.cnst.observeType.auto;
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages[message.chatId] = message;
          }
        }
      }
    };
    //応対数検索、登録
    if (!list.functionManager.isEnabled(obj.siteKey,
        list.functionManager.keyList.enableRealtimeMonitor)
        && !CommonUtil.isset(
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].historyId)) {
      let historyManager = new HistoryManager();
      let target = SharedData.sincloCore[obj.siteKey][obj.tabId];
      obj = Object.assign(obj, target);
      historyManager.addHistory(obj).then((result) => {
        emit.toMine('setHistoryId', result, socket);
        SharedData.sincloCore[obj.siteKey][obj.tabId]['historyId'] = result.historyId;
        SharedData.sincloCore[obj.siteKey][obj.tabId]['stayLogsId'] = result.stayLogsId;
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]['historyId'] = result.historyId;
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]['stayLogsId'] = result.stayLogsId;
        getConversationCountUser(obj.userId, getConversationCallback);
      });
    } else {
      getConversationCountUser(obj.userId, getConversationCallback);
    }
  });

  // チャット呼出中メッセージ
  socket.on('sendInitialNotificationChat', function(d) {
    var obj = JSON.parse(d);
    var ret = {
      siteKey: obj.messageList.siteKey,
      tabId: obj.messageList.tabId,
      sincloSessionId: obj.messageList.sincloSessionId,
      chatMessage: obj.messageList.chatMessage,
      messageType: obj.messageList.messageType,
      messageDistinction: obj.messageList.messageDistinction,
      chatId: obj.messageList.chatId,
      mUserId: obj.messageList.mUserId,
      userId: obj.messageList.userId
    };
    chatApi.set(ret);
  });

  // 既読操作
  socket.on('isReadChatMessage', function(d) {
    var obj = JSON.parse(d);
    try {
      if (CommonUtil.isset(SharedData.sincloCore[obj.siteKey]) &&
          CommonUtil.isset(SharedData.sincloCore[obj.siteKey][obj.tabId])) {
        SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadId = null;
        SharedData.sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt = 0;
        console.log('reset chatUnreadCnt');
      }
      Object.keys(SharedData.sincloCore[obj.siteKey]).some(function(key) {
        if (SharedData.sincloCore[obj.siteKey][key].sincloSessionId ===
            obj.sincloSessionId) {
          SharedData.sincloCore[obj.siteKey][key].chatUnreadId = null;
          SharedData.sincloCore[obj.siteKey][key].chatUnreadCnt = 0;
          return true;
        }
      });
    } catch (e) {
    }
    if (CommonUtil.isset(
        SharedData.sincloCore[obj.siteKey][obj.tabId].historyId)) {
      obj.historyId = SharedData.sincloCore[obj.siteKey][obj.tabId].historyId;
      DBConnector.getPool().query(
          'UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND message_type = 1 AND id <= ?;',
          [obj.historyId, obj.chatId], function(err, ret, fields) {
            chatApi.sendUnreadCnt('retReadChatMessage', obj, true);
          }
      );
    }
  });

  // 既読操作（消費者）
  socket.on('isReadFromCustomer', function(d) {
    var obj = JSON.parse(d);
    if (getSessionId(obj.siteKey, obj.tabId, 'historyId')) {
      obj.historyId = SharedData.sincloCore[obj.siteKey][obj.tabId].historyId;
      DBConnector.getPool().query(
          'UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND message_type != 1;',
          [obj.historyId], function(err, ret, fields) {
            emit.toSameUser('retReadFromCustomer', {
              tabId: obj.tabId,
              sincloSessionId: obj.sincloSessionId
            }, obj.siteKey, obj.sincloSessionId);
          }
      );
    }
  });

  // 入力ステータスを送信
  socket.on('sendTypeCond', function(d) {
    var obj = JSON.parse(d);
    // 存在チェック
    if (!((obj.siteKey in SharedData.sincloCore) &&
        (obj.tabId in SharedData.sincloCore[obj.siteKey]))) {
      return false;
    }

    // 送り主が企業の場合
    if (obj.type === chatApi.cnst.observeType.company) {
      SharedData.sincloCore[obj.siteKey][obj.tabId].chatSessionId = socket.id; // 入力中ユーザーのsocketIdをセットする

      // 企業へ送る
      emit.toCompany('receiveTypeCond', d, obj.siteKey);
      // 消費者へ送る
      delete obj.message;
      if (('sendToCustomer' in obj) && String(obj.sendToCustomer) ===
          'false') return false;
      emit.toSameUser('receiveTypeCond', obj, obj.siteKey,
          getSessionId(obj.siteKey, obj.tabId, 'sincloSessionId'));
    }
    // 送り主が消費者の場合
    else {
      // 企業へ送る
      emit.toCompany('receiveTypeCond', d, obj.siteKey);
    }
  });

  socket.on('retTypingMessage', function(d) {
    var obj = JSON.parse(d);
    // 送り先がセットされている
    if (('to' in obj) && (obj.siteKey in SharedData.company.info) &&
        (obj.to in SharedData.company.info[obj.siteKey])) {
      var toKeys = Object.keys(
          SharedData.company.info[obj.siteKey][obj.to]).length;
      if (toKeys > 0) {
        for (var sessionId in SharedData.company.info[obj.siteKey][obj.to]) {
          emit.toUser('resTypingMessage', d, sessionId);
        }
      }
    }
  });

  // ============================================
  //  シナリオイベントハンドラ
  // ============================================
  socket.on('getScenario', function(data, ack) {
    var obj = JSON.parse(data);
    var result = {};
    DBConnector.getPool().query(
        'select activity from t_chatbot_scenarios where m_companies_id = ? and id = ?;',
        [list.companyList[obj.siteKey], obj.scenarioId],
        function(err, row) {
          if (err !== null && err !== '') {
            if (ack) {
              ack(result);
            } else {
              emit.toMine('resGetSenario', result, socket);
            }
            return;
          }
          if (row.length !== 0) {
            result = JSON.parse(row[0].activity);
          }
          if (ack) {
            ack({id: obj.scenarioId, activity: result});
          } else {
            emit.toMine('resGetScenario',
                {id: obj.scenarioId, activity: result}, socket);
          }
        });
  });

  socket.on('processSendMail', function(data, ack) {
    var obj = JSON.parse(data);
    sendSenarioMail(obj, function(result) {
      ack(result);
    });
  });

  socket.on('storeScenarioMessage', function(data, ack) {
    var obj = JSON.parse(data);
    //応対数検索、登録
    getConversationCountUser(obj.userId, function(results) {
      var messageDistinction;
      if (results !== null) {
        //カウント数が取れなかったとき
        if (Object.keys(results) && Object.keys(results).length === 0) {
          messageDistinction = 1;
        }
        //カウント数が取れたとき
        else {
          messageDistinction = results[0].conversation_count;
        }
        obj.messages.forEach(function(elm, index, arr) {
          if (!CommonUtil.isset(elm.created)) {
            elm.created = new Date();
            elm.sort = CommonUtil.fullDateTime(elm.created);
          }
          elm.applied = true;
          var sincloSession = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId];
          if (CommonUtil.isset(sincloSession) &&
              CommonUtil.isset(sincloSession.scenario)) {
            if (!CommonUtil.isset(
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId])) {
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId] = {};
            }
            if (!CommonUtil.isset(
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId][elm.sequenceNum])) {
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId][elm.sequenceNum] = {};
            }
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId][elm.sequenceNum][elm.categoryNum] = elm;
          }
          // get message type by hearing uitype
          if (typeof elm.uiType !== 'undefined') {
            var messageType = getMessageTypeByUiType(elm.uiType);
          } else {
            var messageType = getMessageTypeBySenarioActionType(elm.type);
          }

          var ret = {
            siteKey: obj.siteKey,
            tabId: obj.tabId,
            sincloSessionId: obj.sincloSessionId,
            userId: obj.userId,
            mUserId: null,
            chatMessage: elm.message,
            messageType: messageType,
            created: elm.created,
            sort: elm.sort,
            messageDistinction: messageDistinction,
            achievementFlg: elm.requireCv ? -1 : null
          };
          chatApi.set(ret);
        });
      }
      ack();
    });
  });

  socket.on('addLastMessageToCV', function(d) {
    var obj = JSON.parse(d);
    DBConnector.getPool().query(
        'select * from t_history_chat_logs where m_companies_id = ? and t_histories_id = ? and ((message_type = 12) OR (message_type = 22) OR (30 <= message_type and message_type <= 32) OR (message_type = 40)) order by created desc limit 0,1;',
        [list.companyList[obj.siteKey], obj.historyId],
        function(err, row) {
          if (err !== null && err !== '') {
            console.log('UPDATE lastMessage to cv is failed. historyId : ' +
                obj.historyId);
            return;
          }
          if (row.length !== 0) {
            DBConnector.getPool().query(
                'update t_history_chat_logs set achievement_flg=0 where id = ?',
                [row[0].id], function() {

                });
          }
        });
  });

  // 都度：チャットデータ取得(オートメッセージのみ)
  socket.on('sendScenarioMessage', function(d, ack) {
    var obj = JSON.parse(d);
    var scenario = JSON.parse(JSON.stringify(obj));
    scenario.created = new Date();
    scenario.sort = CommonUtil.fullDateTime(scenario.created);

    var sincloSession = SharedData.sincloCore[scenario.siteKey][scenario.sincloSessionId];
    if (CommonUtil.isset(sincloSession) &&
        CommonUtil.isset(sincloSession.scenario)) {
      if (!CommonUtil.isset(
          SharedData.sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId])) {
        SharedData.sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId] = {};
      }
      if (!CommonUtil.isset(
          SharedData.sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId][scenario.sequenceNum])) {
        SharedData.sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId][scenario.sequenceNum] = {};
      }
      SharedData.sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId][scenario.sequenceNum][scenario.categoryNum] = scenario;
    } else {
      console.log(
          'sendScenarioMessage::sincloSession : ' + scenario.sincloSessionId +
          'is null.');
      return false;
    }
    if (!list.functionManager.isEnabled(scenario.siteKey,
        list.functionManager.keyList.monitorPollingMode)) {
      emit.toCompany('resScenarioMessage', scenario, scenario.siteKey);
    }
    emit.toSameUser('resScenarioMessage', scenario, scenario.siteKey,
        scenario.sincloSessionId);
    ack({data: scenario});
  });

  socket.on('hideScenarioMessages', function(data, ack) {
    var obj = JSON.parse(data);
    var minChatId = Number.MAX_VALUE;
    for (var i = 0; i < obj.hideMessages.length; i++) {
      var targetChatId = Number(obj.hideMessages[i]);
      if (targetChatId !== 0 && minChatId > targetChatId) {
        minChatId = targetChatId;
      }
    }
    DBConnector.getPool().query(
        'update t_history_chat_logs set hide_flg=1 where id >= ? and m_companies_id = ? and visitors_id = ?;',
        [minChatId, list.companyList[obj.siteKey], obj.userId],
        function(err, result) {

        });
  });

  socket.on('callExternalApi', function(data, ack) {
    var obj = JSON.parse(data);
    DBConnector.getPool().query(
        'select * from t_external_api_connections where m_companies_id = ? and id = ? limit 0,1;',
        [list.companyList[obj.siteKey], obj.externalApiConnectionId],
        function(err, row) {
          if (err !== null && err !== '') {
            console.log(
                'callExternalApi select is failed. externalApiConnectionId : ' +
                obj.externalApiConnectionId);
            return;
          }
          if (row.length !== 0) {
            var reqSettings = row[0];

            var url = replaceVariable(obj.variables, reqSettings.url);
            var reqBody = replaceVariable(obj.variables,
                reqSettings.request_body);

            var headers = {
              'Content-Type': 'application/x-www-form-urlencoded'
            };

            //オプションを定義
            var options = {
              url: 'http://' + process.env.GET_CD_API_HOST + ':' +
                  process.env.GET_CD_API_PORT + '/Notification/callExternalApi',
              headers: headers,
              json: true,
              form: {
                apiParams: JSON.stringify({
                  url: url,
                  requestHeaders: JSON.parse(reqSettings.request_headers),
                  methodType: reqSettings.method_type,
                  requestBody: reqBody,
                  responseBodyMaps: JSON.parse(reqSettings.response_body_maps)
                })
              }
            };

            if (process.env.DB_HOST === 'localhost') {
              options.rejectUnauthorized = false;
            }

            //リクエスト送信
            request.post(options, function(error, response, body) {
              if (error) {
                console.log(
                    '外部API実行時にHTTPレベルのエラーが発生しました。 message : ' + error.message);
                ack({
                  success: false
                });
                return;
              }
              if (response.statusCode === 200) {
                response.setEncoding('utf8');
                if (body.success) {
                  ack(body.result);
                } else {
                  ack({});
                }
                return;
              } else {
                console.log(
                    '外部API実行時にエラーが返却されました。 errorCode : ' + response.statusCode);
                ack({
                  success: false
                });
                return;
              }
            });
          }
        });
  });

  socket.on('getScenarioDownloadInfo', function(data, ack) {
    var obj = JSON.parse(data);
    DBConnector.getPool().query(
        'select * from t_chatbot_scenario_send_files where m_companies_id = ? and id = ? limit 0,1;',
        [list.companyList[obj.siteKey], obj.sendFileId],
        function(err, row) {
          if (err !== null && err !== '') {
            console.log(
                'getScenarioDownloadInfo select is failed. sendFileId : ' +
                obj.sendFileId);
            return;
          }

          if (row.length !== 0) {
            var targetFileInfo = row[0];
            var deleted = Boolean(Number(targetFileInfo.del_flg));
            var sendData = {
              success: true
            };
            var today = new Date();
            if (!deleted) {
              sendData = {
                success: true,
                downloadUrl: targetFileInfo.download_url,
                fileName: targetFileInfo.file_name,
                fileSize: targetFileInfo.file_size,
                expired: CommonUtil.formatDateParse(
                    today.setDate(today.getDate() + 1))
              };
            } else {
              sendData = {
                success: true,
                url: '',
                name: '',
                size: '',
                deleted: true
              };
            }
            ack(sendData);
          }
        });
  });

  socket.on('saveCustomerInfoValue', function(data) {
    var obj = JSON.parse(data);
    var targetValues = obj.targetValues ? obj.targetValues : [];
    var ids = [];
    var objForMerge = {};
    var saveValue = {};
    for (var i = 0; i < targetValues.length; i++) {
      ids.push(targetValues[i].id);
      objForMerge[targetValues[i].id] = targetValues[i].value;
    }

    DBConnector.getPool().query(
        'select id, m_companies_id, item_name, delete_flg from t_customer_information_settings where m_companies_id = ? and id in (?);',
        [list.companyList[obj.siteKey], ids], function(err, rows) {
          if (err !== null && err !== '') return false;
          if (rows.length > 0) {
            for (var j = 0; j < rows.length; j++) {
              saveValue[rows[j]['item_name']] = objForMerge[rows[j].id];
            }
            // カスタム情報自動登録
            db.upsertCustomerInfo({
              siteKey: obj.siteKey,
              userId: obj.userId,
              customVariables: saveValue
            }, socket, function(resultData) {
              if (CommonUtil.isset(resultData)) {
                emit.toCompany('customerInfoUpdated', resultData, obj.siteKey);
              }
            });
          }
        });
  });

  var replaceVariable = function(variables, message) {
    return message.replace(/{{(.+?)\}}/g, function(param) {
      var name = param.replace(/^{{(.+)}}$/, '$1');
      return variables[name] || name;
    });
  };

  socket.on('beginBulkHearing', function(data) {
    var obj = JSON.parse(data),
        historyId = CommonUtil.isset(
            getSessionId(obj.siteKey, obj.tabId, 'historyId')) ?
            getSessionId(obj.siteKey, obj.tabId, 'historyId') :
            obj.historyId;
    getConversationCountUser(obj.userId, function(results) {
      var messageDistinction;
      if (results !== null) {
        //カウント数が取れなかったとき
        if (Object.keys(results) && Object.keys(results).length === 0) {
          messageDistinction = 1;
        }
        //カウント数が取れたとき
        else {
          messageDistinction = results[0].conversation_count;
        }

        console.log(historyId);
        console.log(messageDistinction);
        DBConnector.getPool().query(
            'UPDATE t_history_chat_logs set achievement_flg = -1 where t_histories_id = ? and message_distinction = ? and message_type = 21;',
            [historyId, messageDistinction],
            function(err, row) {
            }
        );
      }
    });
  });

  // leadRegister
  socket.on('saveLeadList', function(data) {
    var obj = JSON.parse(data);
    var targetId = Number(obj.leadSettingsId);
    db.addLeadInformation(obj);
  });

  /*  Check variable (empty or space only)
   *  @param variableIndex (Variables)
   *  @return boolean (check result)
   */
  var isOkVariableForLeadList = function(target) {
    if (CommonUtil.isset(target)) {
      if (!target.match(/^\s$/)) {
        return true;
      }
    }
    return false;
  };

  socket.on('sendParseSignature', function(data, ack) {
    var obj = JSON.parse(data);
    parseSignature(obj.targetText, obj.ip, function(result) {
      var resultObj = JSON.parse(result);
      var storeData = {
        message: resultObj.data,
        target: obj.targetVariable
      };
      obj.chatMessage = JSON.stringify(storeData);
      obj.messageType = 40;
      obj.achievementFlg = obj.requireCv ? -1 : null;
      chatApi.set(obj);
      ack(obj);
    });
  });

  socket.on('traceScenarioInfo', function(data) {
    function joinMessage(message, data) {
      if (typeof (data) === 'object') {
        data = JSON.stringify(data);
      }
      return '[' + obj.siteKey + '][' + obj.sincloSessionId + '] message => ' +
          message + ' data => ' + data;
    }

    /*
     * {
     *   "type": "d" or "i" or "w" or "e"
     *   "message":
     *   "data":
     */
    var obj = JSON.parse(data);
    switch (obj.type) {
      case 'd':
        scenarioLogger.debug(joinMessage(obj.message, obj.data));
        break;
      case 'i':
        scenarioLogger.info(joinMessage(obj.message, obj.data));
        break;
      case 'w':
        scenarioLogger.warn(joinMessage(obj.message, obj.data));
        break;
      case 'e':
        scenarioLogger.error(joinMessage(obj.message, obj.data));
        break;
      default:
        scenarioLogger.info(joinMessage(obj.message, obj.data));
    }
  });

  // ============================================
  // チャットツリーイベントハンドラ
  // ============================================
  socket.on('getChatDiagram', function(data, ack) {
    var obj = JSON.parse(data);
    var result = {};
    DBConnector.getPool().query(
        'select activity from t_chatbot_diagrams where m_companies_id = ? and id = ?;',
        [list.companyList[obj.siteKey], obj.diagramId],
        function(err, row) {
          if (err !== null && err !== '') {
            if (ack) {
              ack(result);
            } else {
              emit.toMine('resGetChatDiagram', result, socket);
            }
            return;
          }
          if (row.length !== 0) {
            result = JSON.parse(row[0].activity);
            // そのままのデータだとクライアント側の処理のパフォーマンスが悪いため
            // UUIDでデータを参照できるよう加工する
            var sendObj = {};
            for (let i = 0; i < result.cells.length; i++) {
              let cell = result.cells[i];
              sendObj[cell['id']] = {
                id: cell['id'],
                parent: cell['parent'],
                type: cell['type'],
                embeds: cell['embeds'],
                attrs: cell['attrs']
              };
            }
          }
          if (ack) {
            ack({id: obj.scenarioId, activity: sendObj});
          } else {
            emit.toMine('resGetChatDiagram',
                {id: obj.diagramId, activity: sendObj}, socket);
          }
        });
  });

  socket.on('sendDiagramMessage', function(d, ack) {
    var obj = JSON.parse(d);
    var diagramData = obj;
    diagramData.created = new Date();
    diagramData.sort = CommonUtil.fullDateTime(diagramData.created);
    diagramData.shownMessage = true;

    var sincloSession = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId];
    if (CommonUtil.isset(sincloSession)) {
      if (!CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram)) {
        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram = [];
      }
      SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram.push(
          diagramData);
    } else {
      console.log(
          'sendDiagramMessage::sincloSession : ' + diagramData.sincloSessionId +
          'is null.');
      return false;
    }
    if (!list.functionManager.isEnabled(obj.siteKey,
        list.functionManager.keyList.monitorPollingMode)
        || list.functionManager.isEnabled(obj.siteKey,
            list.functionManager.keyList.enableRealtimeMonitor)) {
      emit.toCompany('resDiagramMessage', diagramData, obj.siteKey);
    }
    emit.toSameUser('resDiagramMessage', diagramData, obj.siteKey,
        obj.sincloSessionId);
    ack({data: diagramData});
  });

  socket.on('storeDiagramMessage', function(data, ack) {
    var obj = JSON.parse(data);
    //応対数検索、登録
    getConversationCountUser(obj.userId, function(results) {
      var messageDistinction;
      if (results !== null) {
        //カウント数が取れなかったとき
        if (Object.keys(results) && Object.keys(results).length === 0) {
          messageDistinction = 1;
        }
        //カウント数が取れたとき
        else {
          messageDistinction = results[0].conversation_count;
        }

        let messages = obj.message;
        if (CommonUtil.isset(SharedData.sincloCore[obj.siteKey])
            && CommonUtil.isset(
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId])
            && CommonUtil.isset(
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram)) {
          messages = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram.concat(
              messages);
          SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram = [];
        }
        messages.forEach(function(elm, index, arr) {
          if (!CommonUtil.isset(elm.created)) {
            elm.created = new Date();
            elm.sort = CommonUtil.fullDateTime(elm.created);
          }
          elm.applied = true;

          var ret = {
            siteKey: obj.siteKey,
            tabId: obj.tabId,
            userId: obj.userId,
            sincloSessionId: obj.sincloSessionId,
            mUserId: null,
            chatMessage: JSON.stringify(elm.message),
            messageType: elm.messageType,
            created: elm.created,
            sort: elm.sort,
            messageDistinction: messageDistinction,
            achievementFlg: elm.requireCv ? -1 : null,
            shownMessage: CommonUtil.isset(elm.shownMessage) ?
                elm.shownMessage :
                false
          };
          chatApi.set(ret);
        });
      }
      ack();
    });
  });

  socket.on('addLastMessageToConversionFlg', function(d) {
    var obj = JSON.parse(d);
    DBConnector.getPool().query(
        'select * from t_history_chat_logs where m_companies_id = ? and t_histories_id = ? order by created desc limit 0,1;',
        [list.companyList[obj.siteKey], obj.historyId],
        function(err, row) {
          if (err !== null && err !== '') {
            console.log('UPDATE lastMessage to cv is failed. historyId : ' +
                obj.historyId);
            return;
          }
          if (row.length !== 0) {
            DBConnector.getPool().query(
                'update t_history_chat_logs set achievement_flg=0 where id = ?',
                [row[0].id], function() {

                });
          }
        });
  });

  // ============================================
  //  画面キャプチャ共有イベントハンドラ
  // ============================================

  /**
   * オペレータから画面キャプチャ共有のリクエストを送信する
   */
  socket.on('requestCoBrowseOpen', function(data) {
    console.log('requestCoBrowseOpen >>> ' + data);
    var obj = JSON.parse(data);
    if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) return false;
    if (list.laSessionCounter.isLimit(obj.siteKey)) {
      emit.toMine('coBrowseSessionLimit', data, socket);
      return;
    }
    SharedData.sincloCore[obj.siteKey][obj.tabId].shareCoBrowseFlg = true;
    SharedData.sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
    emit.toUser('startCoBrowseOpen', data,
        getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
    emit.toMine('requestCoBrowseAllowed', data, socket);
    // 今まで通り
  });

  /**
   * サイト訪問者側で画面キャプチャ共有のリクエストを許可したときに送信する
   */
  socket.on('beginToCoBrowse', function(data) {
    console.log('beginToCoBrowse >>> ' + data);
    var obj = JSON.parse(data);
    emit.toCompany('beginToCoBrowse', data, obj.siteKey);
  });

  /**
   * オペレータ側の準備が完了し、サイト訪問者側もLiveAssistとのセッションを確立した後に送信する
   */
  socket.on('readyToCoBrowse', function(data) {
    console.log('readyToCoBrowse >>> ' + data);
    var obj = JSON.parse(data);
    SharedData.sincloCore[obj.siteKey][obj.tabId].laShortCode = obj.shortcode;
    SharedData.sincloCore[obj.siteKey][obj.tabId].coBrowseConnectToken = obj.coBrowseConnectToken;
    emit.toUser('readyToCoBrowse', data,
        getSessionId(obj.siteKey, obj.tabId, 'coBrowseParentSessionId'));
    emit.toCompany('syncNewInfo', data, obj.siteKey);
    list.laSessionCounter.countUp(obj.siteKey, obj.tabId);
  });

  /**
   * オペレータ側の準備が完了し、サイト訪問者側がLiveAssistとのセッションの確立に失敗した後に送信する
   */
  socket.on('coBrowseFailed', function(data) {
    console.log('coBrowseFailed >>> ' + data);
    var obj = JSON.parse(data);
    emit.toUser('coBrowseFailed', data,
        getSessionId(obj.siteKey, obj.tabId, 'coBrowseParentSessionId'));
  });

  /**
   * オペレータの画面キャプチャ共有時の子ウィンドウがオープンし、LiveAssistとのセッションを確立したときに送信する
   */
  socket.on('assistAgentIsReady', function(data) {
    console.log('assistAgentIsReady >>> ' + data);
    var obj = JSON.parse(data);
    if (obj.to in SharedData.sincloCore[obj.siteKey]) {
      console.log('OBJ : ' + JSON.stringify(data));
      SharedData.sincloCore[obj.siteKey][obj.to]['responderId'] = obj.responderId; // 対応ユーザーID
      SharedData.sincloCore[obj.siteKey][obj.to]['coBrowseParentSessionId'] = socket.id; // 企業側のsocket.id
      emit.toUser('assistAgentIsReady', data,
          getSessionId(obj.siteKey, obj.to, 'sessionId'));
    } else {
      console.log(
          'assistAgentIsReady >>> Tab ID : ' + obj.to + ' is NOT found.');
      //FIXME  画面共有セッションは切れていない可能性もあるのでハンドリング方法を考える
    }
  });

  //画面共有許可しない
  socket.on('sharingRejection', function(d) {
    var obj = JSON.parse(d);
    var targetId = obj.tabId.replace('_frame', '');
    emit.toCompany('sharingApplicationRejection', obj, obj.siteKey);
  });

  //画面共有キャンセル
  socket.on('cancelSharing', function(d) {
    var obj = JSON.parse(d);
    emit.toUser('cancelSharingApplication', d,
        getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
  });

  socket.on('coBrowseReconnectConfirm', function(data) {
    var obj = JSON.parse(data), timer, i = 1;
    timer = setInterval(function() {
      var sessionId = getSessionId(obj.siteKey, obj.to, 'sessionId');
      if (sessionId && SharedData.connectList[sessionId]) {
        emit.toUser('assistAgentIsReady', data,
            getSessionId(obj.siteKey, obj.to, 'sessionId'));
        clearInterval(timer);
        // sessionIdが消えてる可能性があるため、企業側フレームのsocket.idを再セット
        SharedData.sincloCore[obj.siteKey][obj.to].coBrowseParentSessionId = socket.id;
      }
      if (i === 5) {
        clearInterval(timer);
        emit.toUser('stopCoBrowse', {message: '接続できませんでした。'}, socket.id);
      }
      i++;
    }, 1000);
  });

  /**
   *
   * 企業フレーム:1, 消費者フレーム:2,企業インライン:3, 消費者インライン:4
   */
  socket.on('requestStopCoBrowse', function(data) {
    console.log('requestStopCoBrowse >>> ' + data);
    var obj = JSON.parse(data);
    if (CommonUtil.isset(obj.coBrowseConnectToken)) {
      var parentId = false;
      obj.message = '切断を検知しました。';
      // 企業フレーム
      switch (Number(obj.type)) {
        case 1: // 企業フレーム
        case 2: // 消費者フレーム
          if (Number(obj.type) === 1) { // Was angry JS Lint
            parentId = obj.tabId.replace('_frame', '');
          }
          if ('parentId' in obj) {
            parentId = obj.parentId;
          }

          if (getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg')) {
            // 企業フレーム
            emit.toUser('stopCoBrowse', obj,
                getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg'));
            // 企業インライン
            // emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.tabId, 'syncSessionId'));
            // 消費者フレーム
            emit.toUser('stopCoBrowse', obj,
                getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
            // 消費者インライン
            emit.toUser('stopCoBrowse', obj,
                getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
            coBrowseStopCtrl(obj.siteKey, obj.tabId, true);
          } else {
            coBrowseStopCtrl(obj.siteKey, obj.tabId);
          }
          break;
        case 3: // 企業インライン
        case 4: // 消費者インライン
          // 相手先インラインフレーム
          emit.toUser('stopCoBrowse', obj,
              getSessionId(obj.siteKey, obj.to, 'coBrowseParentSessionId'));
          // 消費者インライン
          break;
      }

      // 消費者親タブ
      var compData = obj;
      if (parentId) {
        emit.toUser('stopCoBrowse', data,
            getSessionId(obj.siteKey, parentId, 'sessionId')); // 消費者の親フレーム
        coBrowseStopCtrl(obj.siteKey, parentId);
        compData.tabId = parentId;
      }

      // 企業一括
      emit.toCompany('stopCoBrowse', compData, obj.siteKey); // リアルタイムモニタを更新する為
    } else {
      emit.toCompany('unsetUser', data, obj.siteKey);
    }
  });

  // -----------------------------------------------------------------------
  // ビデオチャット関連
  // ビデオチャットで利用している各値はプレフィックス（vc_）をつけている。
  // -----------------------------------------------------------------------
  socket.on('sendMessage', function(d) {
    var obj = JSON.parse(d);
    var host = (obj.host !== 'true') ? 'host' : 'guest';
    emit.toUser('receiveMessage', d, SharedData.vc_connectList[obj.fromto.to]);
  });

  socket.on('userOut', function(data) {
    var obj = JSON.parse(data);
    if (!CommonUtil.isset(obj.connectToken)) {
      emit.toCompany('unsetUser', data, obj.siteKey);
    }
  });

  socket.on('settingReload', function(data, ack) {
    var obj = JSON.parse(data), targetKey;
    if (CommonUtil.isset(obj.type) && String(obj.siteKey) === 'master') {
      switch (obj.type) {
        case 1: // get new company ( sample: socket.emit('settingReload', JSON.stringify({type:1, siteKey: "master"})); )
          console.log('before', list.companyList);
          list.getCompanyList(obj.forceReload);
          console.log('after', list.companyList);
          break;
        case 2: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:2, targetKey: "demo", siteKey: "master"})); )
          console.log('before', list.companyList);
          if (CommonUtil.isset(obj.targetKey) &&
              CommonUtil.isset(list.companyList[obj.targetKey])) {
            delete list.companyList[obj.targetKey];
          }
          console.log('after', list.companyList);
          break;
        case 3: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:3, targetKey: "demo", siteKey: "master"})); )
          console.log('connectList', SharedData.connectList);
          console.log('SharedData.doc_connectList', SharedData.doc_connectList);
          if (('targetKey' in obj) &&
              (obj.targetKey in SharedData.sincloCore)) {
            console.log('--------------------------------' + obj.targetKey +
                '--------------------------------');
            console.log('SharedData.sincloCore',
                SharedData.sincloCore[obj.targetKey]);
            console.log(
                '-------------------------------------------------------------------------');
          } else {
            var keys = Object.keys(SharedData.sincloCore);
            for (var i = 0; i < keys.length; i++) {
              targetKey = keys[i];
              console.log('--------------------------------' + targetKey +
                  '--------------------------------');
              console.log('SharedData.sincloCore',
                  SharedData.sincloCore[targetKey]);
            }
            console.log(
                '-------------------------------------------------------------------------');
          }
          break;
        case 4: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:4, siteKey: "master", targetSiteKey: "master"})); )
          if (!('targetSiteKey' in obj)) return false;
          targetKey = obj.targetSiteKey;
          if (targetKey in SharedData.company.info) {
            var activeList = [];
            if (targetKey in SharedData.activeOperator) {
              activeList = SharedData.activeOperator[targetKey];
            }
            console.log('info', SharedData.company.info[targetKey]);
            emit.toMine('consoleLogInfo',
                {u: SharedData.company.info[targetKey], a: activeList}, socket);
          }
          break;
        case 5: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:5, siteKey: "master", str: ">>>>>>>>>>>>>>>>>>>>>"})); )
          var str = '>>>>>>>>>>>>>>>>>>>>>';
          if (('str' in obj)) {
            str = obj.str;
          }
          console.log(str);
          break;
        case 6: // reset LiveAssist current count socket.emit('settingReload', JSON.stringify({type:6, targetKey: "demo", siteKey: "master"}));
          console.log('settingReload >>> reset LiveAssist current count');
          list.laSessionCounter.initializeCurrentCount(obj.targetKey);
          break;
        case 7: // view all Obj socket.emit('settingReload', JSON.stringify({type:7, targetKey: "demo", siteKey: "master"}));
          console.log(
              'getAllObj --------------------------------------------------');
          console.log(
              'SharedData.sincloCore : ' +
              JSON.stringify(SharedData.sincloCore[obj.targetKey]));
          console.log('connectList : ' + JSON.stringify(connectList));
          console.log('SharedData.c_connectList : ' +
              JSON.stringify(SharedData.c_connectList));
          console.log('SharedData.doc_connectList : ' +
              JSON.stringify(SharedData.doc_connectList));
          console.log(
              'list.customerList : ' +
              JSON.stringify(list.customerList[obj.targetKey]));
          console.log(
              'End --------------------------------------------------------');
          break;
        case 8: // confirm tabId user is exists? socket.emit('settingReload', JSON.stringify({type:8, accessId:"", ipAddress:"", targetKey: "demo", siteKey: "master"}));
          var keys = CommonUtil.isset(list.customerList[obj.targetKey]) ?
              Object.keys(list.customerList[obj.targetKey]) :
              [];
          var targetObj = null;
          var targetKey = '';
          if (keys && keys.length > 0) {
            keys.some(function(key) {
              if (key.indexOf(obj.accessId + '_' + obj.ipAddress) >= 0) {
                targetKey = key;
                targetObj = list.customerList[obj.targetKey][key];
                return true;
              }
            });
          }
          var targetSocketId = '';
          if (CommonUtil.isset(targetObj) && CommonUtil.isset(targetKey)) {
            var splited = targetKey.split('/#');
            targetSocketId = '/#' + splited[1];
          }
          var sendData = {
            inCustomerList: CommonUtil.isset(targetObj),
            sincloCoreExists: CommonUtil.isset(
                SharedData.sincloCore[obj.targetKey]),
            sincloCoreDataExists: CommonUtil.isset(
                SharedData.sincloCore[obj.targetKey][(CommonUtil.isset(
                    targetObj) ?
                    (CommonUtil.isset(targetObj['tabId']) ?
                        targetObj['tabId'] :
                        '') :
                    '')]),
            connectListExists: CommonUtil.isset(targetObj) &&
                CommonUtil.isset(connectList),
            inConnectList: CommonUtil.isset(targetObj) &&
                CommonUtil.isset(connectList) &&
                (Object.keys(connectList).indexOf(targetSocketId) !== -1),
            customerListExists: CommonUtil.isset(list.customerList) &&
                CommonUtil.isset(list.customerList[obj.targetKey]),
            socketIdIsFound: CommonUtil.isset(targetSocketId),
            socketConnected: CommonUtil.isset(targetSocketId) &&
                CommonUtil.isset(io.sockets.connected[targetSocketId]),
            inSocketsList: CommonUtil.isset(targetSocketId) &&
                CommonUtil.isset(io.sockets.sockets) &&
                (Object.keys(io.sockets.sockets).indexOf(targetSocketId) !== 1)
          };
          console.log('send data : ' + JSON.stringify(sendData));
          emit.toMine('userExistsResult', sendData, socket);
          if (CommonUtil.isset(targetObj) &&
              CommonUtil.isset(targetObj['tabId'])) {
            emit.toUser('checkExists', {
              siteKey: obj.targetKey,
              tabId: targetObj['tabId'],
              returnTo: socket.id
            }, getSessionId(obj.targetKey, targetObj['tabId'], 'sessionId'));
          } else {
            console.log('checkExists NOT sent.');
          }
          break;
        case 9: // view all Obj count socket.emit('settingReload', JSON.stringify({type:79 targetKey: "demo", siteKey: "master"}));
          console.log(
              'getAllObj count --------------------------------------------------');
          console.log(
              'SharedData.sincloCore : ' +
              Object.keys(SharedData.sincloCore[obj.targetKey]).length);
          console.log('connectList : ' + Object.keys(connectList).length);
          console.log('c_connectList : ' +
              Object.keys(SharedData.c_connectList).length);
          console.log(
              'SharedData.doc_connectList : ' +
              Object.keys(SharedData.doc_connectList).length);
          console.log('list.customerList : ' +
              Object.keys(list.customerList[obj.targetKey]).length);
          console.log(
              'End --------------------------------------------------------');
          break;
        case 10: // view all Obj count socket.emit('settingReload', JSON.stringify({type:79 targetKey: "demo", siteKey: "master"}));
          console.log(
              'getSclist --------------------------------------------------');
          console.log(
              'scList : ' + JSON.stringify(SharedData.scList[obj.targetKey]));
          console.log(
              'End --------------------------------------------------------');
          break;
        case 11: // view  socket.emit('settingReload', JSON.stringify({type:11, targetKey: "demo", targetKey: "master"}));
          var targetKey = obj.targetKey;
          console.log(
              'getCommonMemoryList --------------------------------------------------');
          console.log('common.companySettings : ' +
              JSON.stringify(common.companySettings[targetKey]));
          console.log('common.widgetSettings : ' +
              JSON.stringify(common.widgetSettings[targetKey]));
          console.log('common.autoMessageSettings : ' +
              JSON.stringify(common.autoMessageSettings[targetKey]));
          console.log('common.operationHourSettings : ' +
              JSON.stringify(common.operationHourSettings[targetKey]));
          console.log(
              'End --------------------------------------------------------');
          break;
        case 12: // update all DB-setting  socket.emit('settingReload', JSON.stringify({type:12, targetKey: "demo", targetKey: "master"}));
          var targetKey = obj.targetKey;
          console.log(
              'BEGIN RELOAD DB-settings --------------------------------------------------');
          console.log('TARGET : ' + targetKey);
          common.reloadSettings(targetKey);
          console.log(
              'END   RELOAD DB-settings --------------------------------------------------');
          break;
        default:
          break;
      }
    }
    if (ack) {
      ack();
    }
  });

  socket.on('resCheckExists', function(data) {
    var obj = JSON.parse(data);
    if (CommonUtil.isset(obj['returnTo'])) {
      emit.toUser('resCheckExists', obj, obj['returnTo']);
    } else {
      console.log('resCheckExists NOT sent');
    }
  });

  // [消費者：外部接続フレーム] 外部接続フレームからの初期message
  socket.on('connectFromSyncInit', function(d) {
    var obj = JSON.parse(d);
    socket.join(obj.siteKey + emit.roomKey.frame);
    SharedData.sincloCore[obj.siteKey][obj.parentId].shareWindowId = socket.id;
    if (getSessionId(obj.siteKey, obj.tabId, 'sessionId')) {
      SharedData.sincloCore[obj.siteKey][obj.tabId].shareWindowId = socket.id;
    } else {
      SharedData.sincloCore[obj.siteKey][obj.tabId] = {
        sessionId: null,
        parentId: null,
        connectToken: null,
        syncFrameSessionId: getSessionId(obj.siteKey, obj.parentId,
            'syncFrameSessionId'),
        shareWindowId: socket.id,
        shareWindowFlg: true
      };
    }
  });

  // [消費者：外部接続フレーム] 同形ウィンドウを作成するための情報受け取り
  socket.on('sendWindowInfoFromFrame', function(data) {
    var obj = JSON.parse(data);
    if ('connectToken' in obj) {
      SharedData.sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
    }
    // 同形ウィンドウを作成するための情報渡し
    emit.toUser('windowSyncInfo', obj,
        getSessionId(obj.siteKey, obj.parentId, 'syncHostSessionId'));
  });

  // iFrame内からの初期message
  socket.on('startSyncToFrame', function(d) {
    var obj = JSON.parse(d);
    socket.join(obj.siteKey + emit.roomKey.frame);
    if (!getSessionId(obj.siteKey, obj.tabId, 'shareWindowId') &&
        obj.tabId.match(/\_frame$/)) {
      SharedData.sincloCore[obj.siteKey][obj.tabId] = {
        sessionId: null,
        parentId: null,
        connectToken: null,
        syncFrameSessionId: getSessionId(obj.siteKey,
            obj.tabId.replace('_frame', ''), 'syncFrameSessionId'),
        shareWindowId: getSessionId(obj.siteKey,
            obj.tabId.replace('_frame', ''), 'shareWindowId'),
        shareWindowFlg: true
      };
    }
    SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
    SharedData.sincloCore[obj.siteKey][obj.tabId].parentTabId = obj.parentId;
    var hostFrameId = getSessionId(obj.siteKey, obj.parentId,
        'syncFrameSessionId');
    if (hostFrameId) {
      SharedData.sincloCore[obj.siteKey][obj.tabId].syncFrameSessionId = hostFrameId;
    }
    SharedData.sincloCore[obj.siteKey][obj.parentId].syncSessionId = socket.id;
    emit.toUser('connectFromSync', d,
        getSessionId(obj.siteKey, obj.tabId, 'shareWindowId'));
  });

  // 資料共有開始(企業から)
  socket.on('docShareConnect', function(d) {
    var obj = JSON.parse(d);
    if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) {
      // TODO 接続失敗
      return false;
    }
    if (!(obj.tabId in SharedData.doc_connectList)) {
      SharedData.doc_connectList[obj.tabId] = {};
    }
    SharedData.doc_connectList[obj.tabId][obj.from] = socket.id;
    SharedData.doc_connectList.socketId[socket.id] = {
      type: obj.from,
      tabId: obj.tabId,
      siteKey: obj.siteKey
    };
    if (obj.from === 'company') {
      // 通知先を変える
      var sessionId = (String(
          getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg')) !== 'true') ?
          getSessionId(obj.siteKey, obj.tabId, 'sessionId') :
          getSessionId(obj.siteKey, obj.tabId, 'syncSessionId');

      // 資料共有中ユーザーをセットする
      var targetId = (getSessionId(obj.siteKey, obj.tabId, 'parentTabId')) ?
          getSessionId(obj.siteKey, obj.tabId, 'parentTabId') :
          obj.tabId;
      SharedData.sincloCore[obj.siteKey][targetId].docShareId = obj.responderId; // 資料共有中ユーザーをセットする
      if (obj.popup === 'true') {
        console.log('popup');
        // 消費者側に確認ポップアップを表示する
        emit.toUser('docShareConnect', d, sessionId);
      } else {
        obj.tabId = targetId;
        emit.toCompany('docShareConnect', obj, obj.siteKey); // リアルタイムモニタへ通知
      }
    }
  });

  // 資料共有のキャンセル
  socket.on('docShareCancel', function(d) {
    var obj = JSON.parse(d);
    var targetId = obj.tabId.replace('_frame', '');
    emit.toCompany('sharingApplicationRejection', obj, obj.siteKey);
    emit.toUser('docDisconnect', obj,
        SharedData.doc_connectList[targetId]['company']);
  });

  // 資料共有再接続
  socket.on('docShareReConnect', function(d) {
    var obj = JSON.parse(d);
    if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) {
      // TODO 再接続失敗
      return false;
    }
    if (!(obj.tabId in SharedData.doc_connectList)) {
      SharedData.doc_connectList[obj.tabId] = {};
    }
    SharedData.doc_connectList[obj.tabId][obj.from] = socket.id;
    SharedData.doc_connectList.socketId[socket.id] = {
      type: obj.from,
      tabId: obj.tabId,
      siteKey: obj.siteKey
    };
  });

  // 共有する資料を変更
  socket.on('changeDocument', function(d) {
    var obj = JSON.parse(d);
    emit.toUser('changeDocument', d,
        SharedData.doc_connectList[obj.tabId].customer);
  });

  // 共有する資料を変更
  socket.on('compReadFile', function(d) {
    var obj = JSON.parse(d);
    emit.toUser('compReadFile', d,
        SharedData.doc_connectList[obj.tabId].company);
  });

  socket.on('docShareConnectToCustomer', function(d) {
    var obj = JSON.parse(d);
    if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) {
      // TODO 接続失敗
      return false;
    }
    if (!(obj.tabId in SharedData.doc_connectList)) {
      SharedData.doc_connectList[obj.tabId] = {};
    }
    SharedData.doc_connectList[obj.tabId][obj.from] = socket.id;
    SharedData.doc_connectList.socketId[socket.id] = {
      type: obj.from,
      tabId: obj.tabId,
      siteKey: obj.siteKey
    };

    emit.toUser('docShareConnect', d,
        getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
  });

  socket.on('docSendAction', function(d) {
    var obj = JSON.parse(d);
    if ((obj.tabId in SharedData.doc_connectList) &&
        (obj.to in SharedData.doc_connectList[obj.tabId])) {
      emit.toUser('docSendAction', d,
          SharedData.doc_connectList[obj.tabId][obj.to]);
    }
  });

  // ユーザーのアウトを感知
  socket.on('disconnect', function() {
    console.log('【' + socket.id + '】ON DISCONNECT');
    var info = {};
    // 資料共有の場合
    if (CommonUtil.isKeyExists(SharedData.doc_connectList, 'socketId') &&
        SharedData.doc_connectList.socketId.hasOwnProperty(socket.id)) {
      info = SharedData.doc_connectList.socketId[socket.id]; // tabId, type(company or customer)
      var partner = (info.type === 'company') ? 'customer' : 'company';
      SharedData.doc_connectList.timeout[socket.id] = setTimeout(function() { // 3 minutes
        delete SharedData.doc_connectList.socketId[socket.id];
        if (SharedData.doc_connectList.hasOwnProperty(info.tabId)) {
          var doc = SharedData.doc_connectList[info.tabId];
          if (doc[info.type] === socket.id) { // 最後のsocket.idと一致したら切断扱い
            // 相手が接続中の場合
            if (doc.hasOwnProperty(partner)) {
              // 相手の資料共有画面を閉じる
              emit.toUser('docDisconnect', info, doc[partner]);
              delete SharedData.doc_connectList[info.tabId][info.type];
            }
            // 相手の接続が切れている時
            else {
              delete SharedData.doc_connectList[info.tabId];
              var tabId = (getSessionId(info.siteKey, info.tabId + '_frame',
                  'connectToken')) ? info.tabId + '_frame' : info.tabId;
              /* 資料共有中ユーザーを削除する */
              if (getSessionId(info.siteKey, tabId, 'docShareId')) {
                delete SharedData.sincloCore[info.siteKey][tabId].docShareId;
              }
              // 画面共有を行っている場合
              if (getSessionId(info.siteKey, tabId, 'connectToken')) {
                // 企業に知らせる
                emit.toUser('docDisconnect', info,
                    getSessionId(info.siteKey, tabId, 'syncSessionId'));
                // 消費者に知らせる
                emit.toUser('docDisconnect', info,
                    getSessionId(info.siteKey, tabId, 'sessionId'));
              }
              // 資料共有のみの場合
              else {
                // 相手画面に知らせる
                emit.toUser('docDisconnect', info,
                    getSessionId(info.siteKey, info.tabId, 'sessionId'));
              }
              // 企業側画面に知らせる
              emit.toCompany('docDisconnect', info, info.siteKey);
            }
          }
        }
        delete SharedData.doc_connectList.timeout[socket.id];
      }, 5000);
      return false;
    }
    // リアルタイムモニタからのアクセスの場合
    if (socket.id in SharedData.company.user) {
      // ユーザーの情報を削除
      var userInfo = SharedData.company.user[socket.id];
      delete SharedData.company.user[socket.id];

      // socket.id情報を削除
      delete SharedData.company.info[userInfo.siteKey][userInfo.userId][socket.id];

      if (!(userInfo.userId in SharedData.company.timeout[userInfo.siteKey])) {
        SharedData.company.timeout[userInfo.siteKey][userInfo.userId] = '';
      }

      // チャット中ユーザーが居たら、入力終了フラグを送る
      for (var tabId in SharedData.sincloCore[userInfo.siteKey]) {
        var tab = SharedData.sincloCore[userInfo.siteKey][tabId];

        if (('chatSessionId' in tab) && CommonUtil.isset(tab.chatSessionId) &&
            tab.chatSessionId === socket.id) {
          // 企業へ送る
          emit.toCompany('receiveTypeCond',
              {status: false, type: 1, tabId: tabId, message: ''},
              userInfo.siteKey);
          // 消費者へ送る
          emit.toSameUser('receiveTypeCond', {
            status: false,
            type: 1,
            tabId: tabId,
            message: ''
          }, userInfo.siteKey, tab.sincloSessionId);
        }

      }

      SharedData.company.timeout[userInfo.siteKey][userInfo.userId] = setTimeout(
          function() {
            var keys = {};
            // 同一ユーザーが完全にログアウトした場合はユーザーのオブジェクトごと削除
            if ((userInfo.siteKey in SharedData.company.info) &&
                (userInfo.userId in
                    SharedData.company.info[userInfo.siteKey])) {
              keys = Object.keys(
                  SharedData.company.info[userInfo.siteKey][userInfo.userId]);
            }
            if (keys.length === 0) {
              delete SharedData.company.info[userInfo.siteKey][userInfo.userId];
              delete SharedData.company.timeout[userInfo.siteKey][userInfo.userId];

              // チャット対応上限のリセット
              if (SharedData.scList.hasOwnProperty(userInfo.siteKey) &&
                  SharedData.scList[userInfo.siteKey].cnt.hasOwnProperty(
                      userInfo.userId)) {
                console.log('【' + userInfo.siteKey + '】disconnect::対応上限数設定 ' +
                    SharedData.scList[userInfo.siteKey].cnt[userInfo.userId] +
                    ' / ' +
                    SharedData.scList[userInfo.siteKey].user[userInfo.userId] +
                    ' userId : ' + userInfo.userId);
                delete SharedData.scList[userInfo.siteKey].cnt[userInfo.userId];
                delete SharedData.scList[userInfo.siteKey].user[userInfo.userId];
              }

              // 新しいユーザーの人数を送る
              var cnt = Object.keys(SharedData.company.info[userInfo.siteKey]);
              emit.toCompany('outCompanyUser', {
                siteKey: userInfo.siteKey,
                userCnt: cnt.length,
                userId: userInfo.userId
              }, userInfo.siteKey);

              // 受付中オペレータの情報削除
              if ((userInfo.siteKey in SharedData.activeOperator) &&
                  (userInfo.userId in
                      SharedData.activeOperator[userInfo.siteKey])) {
                delete SharedData.activeOperator[userInfo.siteKey][userInfo.userId];
              }
              var opKeys = [];
              if (userInfo.siteKey in SharedData.activeOperator) {
                opKeys = Object.keys(
                    SharedData.activeOperator[userInfo.siteKey]);
              }
              var sendData = {
                siteKey: userInfo.siteKey,
                count: opKeys.length,
                scInfo: SharedData.scList
              };

              if (SharedData.scList.hasOwnProperty(userInfo.siteKey)) {
                sendData.scInfo = SharedData.scList[userInfo.siteKey].cnt;
              }
              emit.toCompany('activeOpCnt', sendData, userInfo.siteKey);

            }
          }, 5000);
      return false;
    }
    // タグ入りページからのアクセスの場合
    //if ( !(socket.id in SharedData.connectList) ) return false;
    info = SharedData.connectList[socket.id];
    if (info && getSessionId(info.siteKey, info.tabId, 'sessionId')) {
      var core = SharedData.sincloCore[info.siteKey][info.tabId];
      var siteId = list.companyList[info.siteKey];
      var timeout = ('connectToken' in core) ? 10000 : 5000;
      // 消費者側の履歴更新
      if (!('subWindow' in core) || ('subWindow' in core) && !core.subWindow &&
          !core.shareWindowFlg) {
        // 履歴の更新
        var sincloSessionId = SharedData.sincloCore[info.siteKey][info.tabId].sincloSessionId;
        DBConnector.getPool().query(
            'SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;',
            [siteId, sincloSessionId, info.userId], function(err, rows) {
              if (err !== null && err !== '') return false; // DB接続断対応

              if (CommonUtil.isset(rows) && CommonUtil.isset(rows[0])) {
                var now = CommonUtil.formatDateParse();
                timeUpdate(rows[0].id, {}, now, function() {
                });
              }
            });

      }

      if ('timeoutTimer' in SharedData.sincloCore[info.siteKey][info.tabId]) {
        clearTimeout(
            SharedData.sincloCore[info.siteKey][info.tabId].timeoutTimer);
      }

      var sincloSessionId = SharedData.sincloCore[info.siteKey][info.tabId].sincloSessionId;
      SharedData.sincloCore[info.siteKey][info.tabId].timeoutTimer = setTimeout(
          function() {
            var historyId = SharedData.sincloCore[info.siteKey][info.tabId].historyId;
            // SharedData.sincloCoreから情報削除
            delete SharedData.sincloCore[info.siteKey][info.tabId];
            // SharedData.c_connectListから情報削除
            if ((info.siteKey in SharedData.c_connectList) &&
                (info.tabId in SharedData.c_connectList[info.siteKey])) {
              delete SharedData.c_connectList[info.siteKey][info.tabId];
            }
            if (core.subWindow) {
              // 企業側
              if (('toTabId' in core) &&
                  getSessionId(info.siteKey, core.toTabId, 'sessionId')) {
                if (('connectToken' in core) &&
                    getSessionId(info.siteKey, core.toTabId, 'connectToken') &&
                    core.connectToken !==
                    getSessionId(info.siteKey, core.toTabId,
                        'connectToken')) return false;
                emit.toUser('syncStop', {
                  siteKey: info.siteKey,
                  tabId: core.toTabId,
                  connectToken: core.connectToken
                }, getSessionId(info.siteKey, core.toTabId, 'sessionId'));
                if (getSessionId(info.siteKey, core.toTabId, 'parentTabId')) {
                  var parentTabId = getSessionId(info.siteKey, core.toTabId,
                      'parentTabId');
                  emit.toUser('syncStop', {
                    siteKey: info.siteKey,
                    tabId: core.toTabId,
                    connectToken: core.connectToken
                  }, getSessionId(info.siteKey, parentTabId, 'sessionId'));
                  emit.toCompany('syncStop',
                      {siteKey: info.siteKey, tabId: parentTabId},
                      info.siteKey);
                  syncStopCtrl(info.siteKey, parentTabId);
                } else {
                  emit.toCompany('syncStop',
                      {siteKey: info.siteKey, tabId: core.toTabId},
                      info.siteKey);
                }
                syncStopCtrl(info.siteKey, core.toTabId);
              }
              // toがタイムアウトしている時
              else {
                if ('connectTab' in core) {
                  emit.toUser('syncStop', {
                    siteKey: info.siteKey,
                    tabId: core.connectTab,
                    connectToken: core.connectToken
                  }, getSessionId(info.siteKey, core.connectTab, 'sessionId'));
                  emit.toCompany('syncStop',
                      {siteKey: info.siteKey, tabId: core.connectTab},
                      info.siteKey);
                  syncStopCtrl(info.siteKey, core.connectTab);
                } else {
                  emit.toCompany('syncStop',
                      {siteKey: info.siteKey, tabId: core.toTabId},
                      info.siteKey);
                  syncStopCtrl(info.siteKey, core.toTabId);
                }
              }
            }
            // 消費者側
            else {
              // 応対数再計算
              if (('chat' in core) &&
                  SharedData.scList.hasOwnProperty(info.siteKey) &&
                  SharedData.scList[info.siteKey].cnt.hasOwnProperty(
                      core.chat)) {
                SharedData.scList[info.siteKey].cnt[core.chat] = chatApi.calcScNum(
                    info,
                    core.chat);
                console.log('【' + info.siteKey + '】cus::disconnect::対応上限数設定 ' +
                    SharedData.scList[info.siteKey].cnt[core.chat] + ' / ' +
                    SharedData.scList[info.siteKey].user[core.chat] +
                    ' userId : ' +
                    core.chat);
              }
              if ('syncFrameSessionId' in core) {
                emit.toUser('unsetUser',
                    {siteKey: info.siteKey, tabId: info.tabId},
                    core.syncFrameSessionId);
                syncStopCtrl(info.siteKey, info.tabId);
              }

              if ('coBrowseConnectToken' in core) {
                emit.toUser('unsetUser',
                    {siteKey: info.siteKey, tabId: info.tabId},
                    core.coBrowseParentSessionId);
                coBrowseStopCtrl(info.siteKey, info.tabId);
              }

              if (('syncSessionId' in core) &&
                  (core.syncSessionId in SharedData.connectList) &&
                  ('tabId' in SharedData.connectList[core.syncSessionId])) {
                var tabId = SharedData.connectList[core.syncSessionId].tabId;
                if (('parentTabId' in core) &&
                    getSessionId(info.siteKey, tabId, 'subWindow')) {
                  SharedData.sincloCore[info.siteKey][tabId].connectTab = core.parentTabId;
                }
              }

              var sendData = {siteKey: info.siteKey, tabId: info.tabId};

              if (SharedData.scList.hasOwnProperty(info.siteKey)) {
                sendData.scInfo = SharedData.scList[info.siteKey].cnt;
              }
              if (!list.functionManager.isEnabled(info.siteKey,
                  list.functionManager.keyList.enableRealtimeMonitor)) {
                if (CommonUtil.isKeyExists(SharedData.sincloCore,
                    info.siteKey + '.' + info.sincloSessionId + '.historyId')) {
                  emit.toCompany('unsetUser', sendData, info.siteKey);
                }
              } else if (!list.functionManager.isEnabled(info.siteKey,
                  list.functionManager.keyList.monitorPollingMode)) {
                emit.toCompany('unsetUser', sendData, info.siteKey);
              }

              if (sincloSessionId) {
                if (CommonUtil.isset(
                    SharedData.sincloCore[info.siteKey][sincloSessionId])
                    && CommonUtil.isKeyExists(SharedData.sincloCore,
                        info.siteKey + '.' + sincloSessionId + '.sessionIds')) {
                  var sessionIds = SharedData.sincloCore[info.siteKey][sincloSessionId].sessionIds;
                  delete sessionIds[socket.id];
                  if (Object.keys(sessionIds).length === 0) {
                    delete SharedData.sincloCore[info.siteKey][sincloSessionId];
                  }
                }
              }
            }
          }, timeout);
      // SharedData.connectListから削除
      delete SharedData.connectList[socket.id];

      if (sincloSessionId) {
        var sincloSession = SharedData.sincloCore[info.siteKey][sincloSessionId];
        if (CommonUtil.isset(sincloSession) &&
            CommonUtil.isset(sincloSession.sessionIds) &&
            Object.keys(sincloSession.sessionIds).length > 0) {
          delete sincloSession.sessionIds[socket.id];
        } else {
          console.log('sincloSession : ' + sincloSessionId + ' is null.');
        }
      }

      var keys = Object.keys(list.customerList[info.siteKey]);
      if (keys && keys.length > 0) {
        keys.forEach(function(key) {
          if (key.indexOf(socket.id) >= 0) {
            console.log(
                'delete list.customerList[' + info.siteKey + '][' + key + ']');
            delete list.customerList[info.siteKey][key];
          }
        });
      }
    } else {
      if (info &&
          CommonUtil.isset(SharedData.sincloCore[info.siteKey][info.tabId])) {
        console.log('SESSION ID IS NULL info : ' + JSON.stringify(info) +
            ' info.siteKey : ' + info.siteKey + ' info.tabId ' + info.tabId +
            ' sincloSessionId ' +
            SharedData.sincloCore[info.siteKey][info.tabId].sincloSessionId);
        if (CommonUtil.isset(info.siteKey) && CommonUtil.isset(info.tabId)) {
          var sincloSessionId = SharedData.sincloCore[info.siteKey][info.tabId].sincloSessionId;
          // SharedData.sincloCoreから情報削除
          delete SharedData.sincloCore[info.siteKey][info.tabId];
          if (CommonUtil.isset(
              SharedData.sincloCore[info.siteKey][sincloSessionId]) &&
              CommonUtil.isset(
                  SharedData.sincloCore[info.siteKey][sincloSessionId].sessionIds)) {
            var sessionIds = SharedData.sincloCore[info.siteKey][sincloSessionId].sessionIds;
            Object.keys(sessionIds).forEach(function(key) {
              if (!CommonUtil.isset(io.sockets.connected[key])) {
                console.log('delete not exist sessionId : ' + key);
                delete sessionIds[key];
                console.log('remains : ' + Object.keys(sessionIds).length);
                if (Object.keys(sessionIds).length === 0) {
                  delete SharedData.sincloCore[info.siteKey][sincloSessionId];
                }
              }
            });
          } else {
            console.log('sessionIds is null');
            delete SharedData.connectList[socket.id];
            var keys = Object.keys(list.customerList[info.siteKey]);
            if (keys && keys.length > 0) {
              keys.forEach(function(key) {
                if (key.indexOf(socket.id) >= 0) {
                  delete list.customerList[info.siteKey][key];
                }
              });
            }
          }
        }
      } else {
        console.log('SharedData.sincloCore[info.siteKey][info.tabId] is null');
        if (info) {
          var keys = Object.keys(list.customerList[info.siteKey]);
          if (keys && keys.length > 0) {
            keys.forEach(function(key) {
              if (key.indexOf(socket.id) >= 0) {
                console.log(
                    'delete customer sitekey : ' + info.siteKey + ' key : ' +
                    key);
                var customer = list.customerList[info.siteKey][key];
                delete list.customerList[info.siteKey][key];
                if (CommonUtil.isset(customer) && customer.sincloSessionId) {
                  var sincloSessionId = customer.sincloSessionId;
                  if (CommonUtil.isset(
                      SharedData.sincloCore[info.siteKey][sincloSessionId])
                      && CommonUtil.isset(
                          SharedData.sincloCore[info.siteKey][sincloSessionId]['sessionIds'])) {
                    console.log(
                        'target sincloSessionId > ' + sincloSessionId + ' : ' +
                        JSON.stringify(
                            SharedData.sincloCore[info.siteKey][sincloSessionId]));
                    var sessionIds = SharedData.sincloCore[info.siteKey][sincloSessionId].sessionIds;
                    delete sessionIds[socket.id];
                    if (Object.keys(sessionIds).length === 0) {
                      console.log(
                          'delete sincloSessionId > ' + sincloSessionId);
                      delete SharedData.sincloCore[info.siteKey][sincloSessionId];
                    }
                  }
                }
                delete list.customerList[info.siteKey][key];
              }
            });
          }
          delete SharedData.connectList[socket.id];
        } else {
          console.log('info is null socket.id : ' + socket.id);
          delete SharedData.connectList[socket.id];
        }
      }
    }
  });
});
