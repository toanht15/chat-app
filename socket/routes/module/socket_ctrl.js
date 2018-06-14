var express = require('express');
var router = express.Router();

var database = require('../database');
var api = require('../api');
var uuid = require('node-uuid');
var request = require('request');
var common = require('./common');

// mysql
var mysql = require('mysql'),
    pool = mysql.createPool({
      host: process.env.DB_HOST || 'localhost',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASS || 'password',
      database: process.env.DB_NAME || 'sinclo_db'
    });

// log4js
var log4js = require('log4js'); // log4jsモジュール読み込み

log4js.configure('./log4js_setting.json'); // 設定ファイル読み込み

var reqlogger = log4js.getLogger('request'); // リクエスト用のロガー取得
var errlogger = log4js.getLogger('error'); // エラー用のロガー取得
var deblogger = log4js.getLogger('debug'); // デバッグ用のロガー取得

//サーバインスタンス作成
var io = require('socket.io')(process.env.WS_PORT),
    activeOperator = {}, // 待機中オペレーター
    sincloCore = {}, // socketIDの管理
    connectList = {}, // socketIDをキーとした管理
    c_connectList = {}, // socketIDをキーとしたチャット管理
    vc_connectList = {}, // tabId: socketID
    doc_connectList = {'socketId': {}, 'timeout': {}}, // tabId: tabId
    // siteKeyをキーとした対応上限管理
    // scList = { 'siteKey': { user: { 'userId': '対応上限人数' },  cnt: { 'userId': '対応中人数' } } };
    scList = {},
    company = {
        info : {}, // siteKeyをキーとした企業側ユーザー人数管理
        user : {}, // socket.idをキーとした企業側ユーザー管理
        timeout : {} // userIdをキーとした企業側ユーザー管理
    },
    customerList = {}; // customerList[siteKey][<accessId>_<socket.id>][<userObj>]

// LiveAssistの同時セッション数管理用クラス
// laSessionCount = { 'siteKey' : {current: '利用中セッション数', max: '指定済みの最大数'} }
var LaSessionCounter = function() {
  var _key_currentCount = 'current';
  var _key_maxCount = 'max';
  var countList = {};
  var _initializeCountList = function(siteKey) {
    countList[siteKey] = {};
    countList[siteKey][_key_currentCount] = [];
    countList[siteKey][_key_maxCount] = 0;
    _printCurrentState(siteKey, '_initializeCountList');
  }
  var _getMaxCount = function(siteKey) {
    return (siteKey in countList && _key_maxCount in countList[siteKey]) ? countList[siteKey][_key_maxCount] : 0;
  };
  var _getCurrentCount = function(siteKey) {
    return (siteKey in countList && _key_currentCount in countList[siteKey]) ? countList[siteKey][_key_currentCount].length : 0;
  };
  var _printCurrentState = function(siteKey, functionName) {
    var current = _getCurrentCount(siteKey);
    var max = _getMaxCount(siteKey);
    console.log("LaSessionCounter::" + functionName + " siteKey:" + siteKey + ' currentSessions:' + current + ' max:' + max);
  };
  return {
    setMaxCount: function(siteKey, maxCount) {
      if(!(siteKey in countList)) {
        _initializeCountList(siteKey);
      }
      countList[siteKey][_key_maxCount] = maxCount;
      _printCurrentState(siteKey, "setMaxCount");
    },
    getMaxCount: function(siteKey) {
      return _getMaxCount(siteKey);
    },
    getCurrentCount: function(siteKey) {
      return _getCurrentCount(siteKey);
    },
    countUp : function(siteKey, tabId) { // サイト訪問者側のIDを入れる
      if(!this.currentCountExists(siteKey)) {
        // まずはゼロ代入
        this.initializeCurrentCount(siteKey);
      }
      if(!this.isLimit(siteKey)) {
        console.log("DEBUG2 : " + JSON.stringify(countList[siteKey]));
        countList[siteKey][_key_currentCount].push(tabId);
      }
      _printCurrentState(siteKey, "countUp");
    },
    countDown : function(siteKey, tabId) { // サイト訪問者側のIDを入れる
      if(countList[siteKey][_key_currentCount].length <= 0) return;
      countList[siteKey][_key_currentCount].some(function(v, i){
        if (v === tabId) countList[siteKey][_key_currentCount].splice(i,1);
      });
      _printCurrentState(siteKey, "countDown");

    },
    initializeCurrentCount : function(siteKey) {
      countList[siteKey][_key_currentCount] = [];
    },
    currentCountExists : function(siteKey) {
      return (siteKey in countList) && (_key_currentCount in countList[siteKey]) && (typeof(countList[siteKey][_key_currentCount] === 'object'));
    },
    isLimit : function(siteKey) {
      var current = this.getCurrentCount(siteKey);
      var max = this.getMaxCount(siteKey);
      var result = (current >= max);
      if(result) {
        _printCurrentState(siteKey, "isLimit");
      }
      return result;
    }
  }
};
var laSessionCounter = new LaSessionCounter();

/**
 * 機能有無管理クラス
 */
var CompanyFunctionManager = function() {
  var _list = {};

  return {
    keyList : {
      chat : 'chat',
      synclo : 'synclo',
      document : 'document',
      videochat : 'videochat',
      laCoBrowse : 'laCoBrowse',
      chatLimitation : 'chatLimitation',
      exportHistory : 'exportHistory',
      deleteHistory : 'deleteHistory',
      statistics : 'statistics',
      dictionaryCategory : 'dictionaryCategory',
      hideRealtimeMonitor : 'hideRealtimeMonitor',
      operatingHour : 'operatingHour',
      refCompanyData : 'refCompanyData',
      freeInput : 'freeInput',
      cv : 'cv',
      autoMessageSendMail : 'autoMessageSendMail',
      sendFile : 'sendFile',
      loginIpFilter : 'loginIpFilter',
      importExcelAutoMessage : 'importExcelAutoMessage',
      operatorPresenceView : 'operatorPresenceView',
      monitorPollingMode : 'monitorPollingMode'
    },
    set : function(companyKey, coreSettings) {
      try {
        if (typeof(coreSettings) === 'string') {
          _list[companyKey] = JSON.parse(coreSettings);
        } else {
          // object
          _list[companyKey] = coreSettings;
        }
      } catch(e) {
        console.log("Error while set functionList companyKey : " + companyKey);
      }
    },
    isEnabled : function(companyKey, funcName) {
      var result = false;
      if(isset(_list[companyKey]) && isset(_list[companyKey][funcName])) {
        result = _list[companyKey][funcName];
      }
      return result;
    }
  }
}

var functionManager = new CompanyFunctionManager();

// ユーザーIDの新規作成
function makeUserId(){
  var d = new Date();
  return d.getFullYear() + ("0" + (d.getMonth() + 1)).slice(-2) + ("0" + d.getDate()).slice(-2) + d.getHours() + d.getMinutes() + d.getSeconds() + Math.floor(Math.random() * 1000);
}

// sincloCoreオブジェクトからセッションIDを取得する関数
function getSessionId(siteKey, tabId, key){
  if ( (siteKey in sincloCore) && (tabId in sincloCore[siteKey]) && (key in sincloCore[siteKey][tabId]) ) {
    return sincloCore[siteKey][tabId][key];
  }
}

// sincloCoreオブジェクトからチャットで関連するセッションID群を取得する関数
function getChatSessionIds(siteKey, sincloSessionId, key){
  if ( (siteKey in sincloCore) && (sincloSessionId in sincloCore[siteKey]) && (key in sincloCore[siteKey][sincloSessionId]) ) {
    return sincloCore[siteKey][sincloSessionId][key];
  }
}

function now(){
  var d = new Date();
  return "【" + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() + "】";
}

function isset(a){
  return ( a !== undefined && a !== null && a !== "" );
}

var timeCalculator = function(obj){
    var now = new Date(),
        start = new Date(Number(obj.time)),
        req = parseInt((now.getTime() - start.getTime()) / 1000);
  return Number(req);
};

function timeUpdate(historyId, obj, time, callback){
  var insertStayData = {
    t_histories_id: historyId,
    title: ('title' in obj) ? obj.title : "",
    url: ('url' in obj) ? obj.url : "",
    stay_time: "",
    created: time,
    modified: time
  };

  pool.query('SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;', historyId,
    function(err, rows){
      if ( err !== null && err !== '' ) {
        callback(false);
        return false;
      } // DB接続断対応
      if ( isset(rows) && isset(rows[0]) ) {
        // UPDATE
        var stayTime = calcTime(rows[0].created, time);
        pool.query("UPDATE t_history_stay_logs SET stay_time = ? WHERE id = ?",
          [stayTime, rows[0].id],
          function (error,results,fields){
          }
        );
      }
      else {
        rows[0] = { url: null };
      }
      pool.query("UPDATE t_histories SET out_date = ?, modified = ? WHERE id = ?",
        [time, time, historyId],
        function (error,results,fields){
        }
      );

      if ( insertStayData.url === '' || insertStayData.url === rows[0].url ) {
        callback(rows[0].id);
        return true;
      }
      pool.query("INSERT INTO t_history_stay_logs SET ?", insertStayData,
        function (error,results,fields) {
          callback(results.insertId);
          return true;
        }
      );
    }
  );
}

function makeToken(){
  var n = 20,
      str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQESTUVWXYZ1234567890",
      strLen = str.length,
      token = "";
  for(var i=0; i<n; i++){
    token += str[Math.floor(Math.random()*strLen)];
  }
  return token;
};

var companyList = {};
var initialized = false;
function getCompanyList(forceReload){
  pool.query('select * from m_companies where del_flg = 0;', function(err, rows){
    if ( err !== null && err !== '' ) return false; // DB接続断対応
    var key = Object.keys(rows);
    for ( var i = 0; key.length > i; i++ ) {
      var row = rows[key[i]];
      companyList[row.company_key] = row.id;
      laSessionCounter.setMaxCount(row.company_key, row.la_limit_users);
      functionManager.set(row.company_key, row.core_settings);
      if(!(row.company_key in customerList)) {
        console.log("new customerList : " + row.company_key);
        customerList[row.company_key] = {};
      }
      if(initialized && !(row.company_key in common.companySettings)) {
        console.log("LOAD NEW COMPANY SETTINGS : " + row.company_key);
        common.reloadSettings(row.company_key);
      }
    }
    initialized = true;
    if(forceReload) {
      common.reloadSettings();
    }
  });
}
getCompanyList();

router.get('/refreshCompanyList', function(req, res, next){
  getCompanyList();
  res.send("OK");
  res.status(200);
});
module.exports = router;

function _numPad(str){
  return ("0" + str).slice(-2);
}

function calcTime(startTime, endTime){
  var end = new Date(endTime),
      start = new Date(startTime);
  if ( isNaN(start.getTime()) || isNaN(end.getTime()) ) return false;
  req = parseInt((end.getTime() - start.getTime()) / 1000);
  hour = parseInt(req / 3600);
  min = parseInt((req / 60) % 60);
  sec = req % 60;
  return _numPad(hour) + ":" + _numPad(min) + ":" + _numPad(sec); // 表示を更新
}

function fullDateTime(parse){
  function _numPad(str){
    return ("0" + str).slice(-2);
  }
  var d = ( isset(parse) ) ? new Date(Number(parse)) : new Date();
  return d.getFullYear() + _numPad(d.getMonth() + 1) + _numPad(d.getDate()) + _numPad(d.getHours()) + _numPad(d.getMinutes()) + _numPad(d.getSeconds()) + _numPad(Number(String(d.getMilliseconds()).slice(0,2)));
}

function formatDateParse(parse){
  var d = ( isset(parse) ) ? new Date(Number(parse)) : new Date();
  return d.getFullYear() + "/" + _numPad(d.getMonth() + 1) + "/" + _numPad(d.getDate()) + " " + _numPad(d.getHours()) + ":" + _numPad(d.getMinutes()) + ":" + _numPad(d.getSeconds());
}

function syncStopCtrl(siteKey, tabId, unsetFlg){
  var keys = [
    'connectToken', 'syncSessionId', 'syncHostSessionId', 'sdHistoryId',
    'syncFrameSessionId', 'shareWindowId', 'shareWindowFlg'
  ];
  // 画面同期の記録
  var sdHistoryId = getSessionId(siteKey, tabId, "sdHistoryId");
  if ( sdHistoryId ) {
    var now = formatDateParse();
    db.timeUpdateToDisplayShare(now, sdHistoryId);
  }

  if ( unsetFlg ) { // unsetTarget
    var sessionId = getSessionId(siteKey, tabId, "sessionId");
    clearTimeout(sincloCore[siteKey][tabId].timeoutTimer);
    delete sincloCore[siteKey][tabId];
    delete connectList[sessionId];
    return false;
  }

  for (var i = 0; keys.length > i; i++) {
    if ( getSessionId(siteKey, tabId, keys[i]) ) {
      delete sincloCore[siteKey][tabId][keys[i]];
    }
  }

  for ( var key in customerList[siteKey] ){
    if(tabId.indexOf(customerList[siteKey][key]['tabId']) === 0) {
      for (var i = 0; keys.length > i; i++) {
        delete customerList[siteKey][key][keys[i]];
      }
      break;
    }
  }
}

function coBrowseStopCtrl(siteKey, tabId, unsetFlg){
  var keys = [
    'coBrowseConnectToken', 'shareCoBrowseFlg', 'syncHostSessionId', 'laShortCode',
    'responderId', 'coBrowseParentSessionId'
  ];
  // 画面同期の記録 FIXME
  /*
  var sdHistoryId = getSessionId(siteKey, tabId, "sdHistoryId");
  if ( sdHistoryId ) {
    var now = formatDateParse();
    db.timeUpdateToDisplayShare(now, sdHistoryId);
  }
  */

  if ( unsetFlg ) { // unsetTarget
    var sessionId = getSessionId(siteKey, tabId, "sessionId");
    clearTimeout(sincloCore[siteKey][tabId].timeoutTimer);
    delete sincloCore[siteKey][tabId];
    delete connectList[sessionId];
    return false;
  }

  for (var i = 0; keys.length > i; i++) {
    if ( getSessionId(siteKey, tabId, keys[i]) ) {
      delete sincloCore[siteKey][tabId][keys[i]];
    }
  }

  for ( var key in customerList[siteKey] ){
    if(tabId.indexOf(customerList[siteKey][key]['tabId']) === 0) {
      for (var i = 0; keys.length > i; i++) {
        delete customerList[siteKey][key][keys[i]];
      }
      break;
    }
  }
  laSessionCounter.countDown(siteKey, tabId);
}

function getOperatorCnt(siteKey) {
  var cnt = 0;
  if ( isset(activeOperator[siteKey]) ) {
    var key = Object.keys(activeOperator[siteKey]);
    cnt = key.length;
  }
  return cnt;
}

function objectSort(object) {
  //戻り値用新オブジェクト生成
  var sorted = {};
  //キーだけ格納し，ソートするための配列生成
  var array = [];
  //for in文を使用してオブジェクトのキーだけ配列に格納
  for (var key in object) {
    //指定された名前のプロパティがオブジェクトにあるかどうかチェック
    if (object.hasOwnProperty(key)) {
      //if条件がtrueならば，配列の最後にキーを追加する
      array.push(key);
    }
  }
  //配列のソート
  array.sort();
  //配列の逆ソート
  //array.reverse();

  //キーが入った配列の長さ分だけfor文を実行
  for (var i = 0; i < array.length; i++) {
    /*戻り値用のオブジェクトに
    新オブジェクト[配列内のキー] ＝ 引数のオブジェクト[配列内のキー]を入れる．
    配列はソート済みなので，ソートされたオブジェクトが出来上がる*/
    sorted[array[i]] = object[array[i]];
  }
  //戻り値にソート済みのオブジェクトを指定
  return sorted;
}

function getConnectInfo(o){
  var connectToken = getSessionId(o.siteKey, o.tabId, 'connectToken');
  var coBrowseConnectToken = getSessionId(o.siteKey, o.tabId, 'coBrowseConnectToken');
  var responderId = getSessionId(o.siteKey, o.tabId, 'responderId');
  var chatUserId = getSessionId(o.siteKey, o.tabId, 'chat');
  if ( isset(responderId) && isset(connectToken) ) {
    o.responderId = responderId;
    o.connectToken = connectToken;
  } else {
    delete o['connectToken'];
    if(!isset(coBrowseConnectToken)) {
      delete o['responderId'];
    }
  }
  if ( isset(responderId) && isset(coBrowseConnectToken) ) {
    o.responderId = responderId;
    o.coBrowseConnectToken = coBrowseConnectToken;
  } else {
    delete o['coBrowseConnectToken'];
    if(!isset(connectToken)) {
      delete o['responderId'];
    }
  }
  if( isset(chatUserId) ) {
    o.chat = chatUserId;
  } else {
    o.chat = null;
  }
  var docShareId = getSessionId(o.siteKey, o.tabId, 'docShareId');
  if ( isset(docShareId) ) {
    o.docShareId = docShareId;
  } else {
    delete o['docShareId'];
  }
  var sincloSessionId = getSessionId(o.siteKey, o.tabId, 'sincloSessionId');
  if ( isset(sincloSessionId) ) {
    o.sincloSessionId = sincloSessionId;
  } else {
    o.sincloSessionId = null;
  }
  var orgName = getSessionId(o.siteKey, o.tabId, 'orgName');
  if ( isset(orgName) ) {
    o.orgName = orgName;
  } else {
    o.orgName = "";
  }
  var lbcCode = getSessionId(o.siteKey, o.tabId, 'lbcCode');
  if ( isset(lbcCode) ) {
    o.lbcCode = lbcCode;
  } else {
    o.lbcCode = "";
  }
  return o;
}

// IPアドレスの取得
function getIp(socket){
  var ip = "0.0.0.0";
  if ( isset(socket.handshake.headers['x-forwarded-for']) ) {
    ip = socket.handshake.headers['x-forwarded-for'];
  }
  return ip;
}

function getMessageTypeBySenarioActionType(type) {
  var result = 3;
  switch(Number(type)) {
    case 1:
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

// Landscapeの企業情報取得
//requestをrequire
var http = require('http');
http.globalAgent.maxSockets = 100;
function getCompanyInfoFromApi(ip, callback) {
  //ヘッダーを定義
  var headers = {
    'Content-Type':'application/json'
  };

  //オプションを定義
  var options = {
    host: process.env.GET_CD_API_HOST,
    port: process.env.GET_CD_API_PORT,
    path: process.env.GET_CD_API_PATH,
    method: 'POST',
    headers: headers,
    json: true,
    agent: false
  };

  if(process.env.DB_HOST === 'localhost') {
    options.rejectUnauthorized = false;
  }

  //リクエスト送信
  var req = http.request(options, function (response) {
    if(response.statusCode === 200) {
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

  req.write(JSON.stringify({"accessToken":"x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK", "ipAddress":ip}));
  req.end();
}

function sendMail(autoMessageId, lastChatLogId, callback) {
  //ヘッダーを定義
  var headers = {
    'Content-Type':'application/json'
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

  if(process.env.DB_HOST === 'localhost') {
    options.rejectUnauthorized = false;
  }

  //リクエスト送信
  var req = http.request(options, function (response) {
    if(response.statusCode === 200) {
      response.setEncoding('utf8');
      response.on('data', callback);
      return;
    } else {
      console.log('オートメッセージ設定メール通知時にエラーが返却されました。 errorCode : ' + response.statusCode);
      callback(false);
      return;
    }
  });

  req.on('error', function(error) {
    console.log('オートメッセージ設定メール通知時にHTTPレベルのエラーが発生しました。 message : ' + error.message);
    callback(false);
    return;
  });

  req.write(JSON.stringify({"accessToken":"x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK", "autoMessageId":autoMessageId, "lastChatLogId":lastChatLogId}));
  req.end();
}

function sendSenarioMail(obj, callback) {
  //ヘッダーを定義
  var headers = {
    'Content-Type':'application/json'
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

  if(process.env.DB_HOST === 'localhost') {
    options.rejectUnauthorized = false;
  }

  //リクエスト送信
  var req = http.request(options, function (response) {
    if(response.statusCode === 200) {
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

  var historyId = getSessionId(obj.siteKey, obj.tabId, "historyId");
  req.write(JSON.stringify({
    "accessToken":"x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK",
    "userHistoryId": historyId,
    "mailType": obj.mailType,
    "transmissionId": obj.transmissionId,
    "templateId": obj.templateId,
    "withDownloadURL": obj.withDownloadURL,
    "variables": obj.variables
  }));
  req.end();
}

// Frameの削除
function trimFrame(str){
  return str.replace("_frame", "");
}

// 数値チェック
function isNumber(n){
  return RegExp(/^(\+|\-)?\d+(.\d+)?$/).test(n);
}

// objectのマージ
function extend(obj1, obj2) {
  for (key in obj2) {
    if (obj2.hasOwnProperty(key)) {
      obj1[key] = obj2[key];
    }
  }
  return obj1;
}

// emit用
var emit = {
  roomKey: {
    client: 'cl001',
    company: 'cm001',
    frame: 'cf001',
  },
  _convert: function(d){
    if ( typeof(d) === "object" ) {
      return JSON.stringify(d);
    }
    else {
      return d;
    }
  },
  toMine: function(ev, d, s){ // 送り主に返信（そのタブのみ）
    var obj = this._convert(d);
    return s.emit(ev, obj);
  },
  toUser: function(ev, d, sId){ // 対象ユーザーに送信(sId = the session id)
    var obj = this._convert(d);
    if ( !isset(sId) ) return false;
    if ( !isset(io.sockets.connected[sId]) ) return false;
    return io.sockets.connected[sId].emit(ev, obj);
  },
  toSameUser: function(ev, d, siteKey, sessionId){ // 対象ユーザーに送信(sId = the session id)
    var obj = this._convert(d);
    var sessionIds = getChatSessionIds(siteKey, sessionId, 'sessionIds');
    if ( !isset(sessionIds) ) return false;
    var result = false;
    for (var index in sessionIds) {
      if ( !isset(io.sockets.connected[sessionIds[index]]) ) continue;
      io.sockets.connected[sessionIds[index]].emit(ev, obj);
    }
  },
  toClient: function(ev, d, rName) { // 対象企業を閲覧中のユーザーに送信(rName = the room's name)
    var obj = this._convert(d);
    if ( !isset(rName) ) return false;
    return io.sockets.in(rName+this.roomKey.client).emit(ev, obj);
  },
  toCompany: function(ev, d, rName) { // 対象企業にのみ送信(rName = the room's name)
    var obj = this._convert(d);
    if ( !isset(rName) ) return false;
    return io.sockets.in(rName+this.roomKey.company).emit(ev, obj);
  }
};

// socket再接続
function sincloReconnect(socket){
  emit.toMine("sincloReconnect", {}, socket);
}

//応対件数カウント
function getConversationCountUser(visitors_id,callback) {
  pool.query('SELECT conversation_count FROM t_conversation_count WHERE visitors_id = ?',[visitors_id], function (err, results) {
    if(isset(err)) {
      console.log("RECORD SElECT ERROR: t_conversation_count(conversation_count):" + err);
      callback(null);
      return;
    }
    else {
      callback(results);
    }
  });
}

//待機中オペレータ情報登録
function addChatActiveUser(t_history_chat_logs_id,m_users_id,siteKey) {
  pool.query('INSERT INTO t_history_chat_active_users(t_history_chat_logs_id,m_companies_id,m_users_id,created) VALUES(?,?,?,?)',[t_history_chat_logs_id,companyList[siteKey],m_users_id,new Date()],function(err,results) {
    if(isset(err)) {
      console.log("RECORD INSERT ERROR: t_history_chat_active_users:" + err);
    }
  });
}

var callback = function (err, results) {
    if(isset(err)) {
      console.log("RECORD SElECT ERROR: t_conversation_count(conversation_count):" + err);
      return false;
    }
  callback();
}

var db = {
  addDisplayShareHistory: function(responderId, obj){
    if ( isset(obj.siteKey) ) {
      var tabId = ( obj.subWindow ) ? trimFrame(obj.to) : trimFrame(obj.tabId);
      if ( !(tabId in sincloCore[obj.siteKey])  ) return false;
      if ( !(sincloCore[obj.siteKey][tabId].hasOwnProperty('historyId')) ) return false;
      var historyId = getSessionId(obj.siteKey, tabId, "historyId");
      var sdHistoryId = getSessionId(obj.siteKey, tabId, "sdHistoryId");
      var now = formatDateParse();
      // 登録処理
      if ( !sdHistoryId ) {
        //insert
        var insertData = {
          t_histories_id: historyId,
          m_users_id: responderId,
          start_time: now,
          finish_time: now,
        };
        pool.query("INSERT INTO t_history_share_displays SET ?", insertData,
          function (error,results,fields){
            if ( error !== null && error !== '' ) return false; // DB接続断対応
            sincloCore[obj.siteKey][tabId].sdHistoryId = results.insertId;
          }
        );
      }
      // 更新処理
      else {
        this.timeUpdateToDisplayShare(now, sdHistoryId);
      }

    }
  },
  timeUpdateToDisplayShare: function (now, sdHistoryId) {
    // 渡されたIDをもとに検索
    pool.query('SELECT * FROM t_history_share_displays WHERE id = ?;', [sdHistoryId], function(err, rows){
      if ( err !== null && err !== '' ) return false; // DB接続断対応
      // データが見つかった場合
      if ( isset(rows) && isset(rows[0]) ) {
        // アップデートする
        pool.query("UPDATE t_history_share_displays SET finish_time = ? WHERE id = ?",
          [now, sdHistoryId],
          function (error,results,fields){
          }
        );
      }
    });
  },
  addHistory: function(obj, s) {
    if ( isset(obj.tabId) && isset(obj.siteKey) ) {
      if ( !isset(companyList[obj.siteKey]) || obj.subWindow ) return false;
      var siteId = companyList[obj.siteKey];
      pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [siteId, obj.sincloSessionId || obj.tabId, obj.userId], function(err, rows){
        if ( err !== null && err !== '' ) return false; // DB接続断対応
        var now = formatDateParse();

        if ( isset(rows) && isset(rows[0]) ) {
          if(isset(obj.sincloSessionId)) {
            sincloCore[obj.siteKey][obj.sincloSessionId].historyId = rows[0].id;
          }
          sincloCore[obj.siteKey][obj.tabId].historyId = rows[0].id;
          timeUpdate(rows[0].id, obj, now,function(stayLogsId){
            obj.historyId = rows[0].id;
            obj.stayLogsId = stayLogsId;
            emit.toMine('setHistoryId', obj, s);
          });
        }
        else {
          //insert
          var insertData = {
            m_companies_id: siteId,
            visitors_id: obj.userId,
            tab_id: obj.sincloSessionId || obj.tabId,
            ip_address: obj.ipAddress,
            user_agent: obj.userAgent,
            access_date: formatDateParse(obj.time),
            referrer_url: obj.referrer,
            created: now,
            modified: now
          };

          pool.query("INSERT INTO t_histories SET ?", insertData,
            function (error,results,fields){
              if (error && (error !== null && error !== '')) return false; // DB接続断対応
              var historyId = results.insertId;
              if(isset(obj.sincloSessionId)) {
                sincloCore[obj.siteKey][obj.sincloSessionId].historyId = historyId;
              }
              sincloCore[obj.siteKey][obj.tabId].historyId = historyId;
              timeUpdate(historyId, obj, now, function(stayLogsId){
                obj.historyId = historyId;
                obj.stayLogsId = stayLogsId;
                emit.toMine('setHistoryId', obj, s);
              });
            }
          );
        }
      });
    }
  }
};

var console = {
  date: function(){
    var d = new Date();
    return d.getFullYear() + "/" + ( "0" + (d.getMonth() + 1) ).slice(-2) + "/" + ("0" + d.getDate()).slice(-2) + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);
  },
  log: function(a, b){
    var label = null, data = null, d = this.date();
    switch(typeof b){
      case 'object':
        var data = "【" + a + "|" + d + "】 " + JSON.stringify(b, null, "\t");
        break;
      case 'string':
        var data = "【" + a + "|" + d + "】 " + b;
        break;
      default:
        var data = "【" + d + "】 " + JSON.stringify(a, null, "\t");
    }
    reqlogger.info(data);
  }
};

//接続確立時の処理
io.sockets.on('connection', function (socket) {

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
        start: 98,
        end: 99
      },
      requestFlg: {
        noFlg: 0
      },
      inFlg: {
        flg: 1
      }
    },
    set: function(d){ // メッセージが渡されてきたと
      if ( !getSessionId(d.siteKey, d.tabId, 'sessionId') ) {
        sincloReconnect(socket);
        return false;
      }
      // 履歴idか(入退室以外に)メッセージがない
      if ( !getSessionId(d.siteKey, d.tabId, 'historyId') || (!isset(d.chatMessage) && !(chatApi.cnst.observeType.start && d.messageType || chatApi.cnst.observeType.end && d.messageType)) ) {
        // エラーを渡す
        return emit.toUser('sendChatResult', {ret: false, messageType: d.messageType, tabId: d.tabId, siteKey: d.siteKey}, d.siteKey);
      }
      // チャットidがある
      else {
        // DBへ書き込む
        this.commit(d);
      }
    },
    get: function(obj){ // 最初にデータを取得するとき
        var chatData = {historyId: null, messages: []};
        var historyId = getSessionId(obj.siteKey, obj.tabId, 'historyId');
        if ( historyId ) {
            chatData.historyId = historyId;

            var sql  = "SELECT";
                sql += " chat.id, chat.message, chat.message_type as messageType, chat.message_distinction as messageDistinction,chat.achievement_flg as achievementFlg,chat.delete_flg as deleteFlg, chat.visitors_id as visitorsId,chat.m_users_id as userId, mu.display_name as userName, chat.message_read_flg as messageReadFlg,chat.notice_flg as noticeFlg, chat.created ";
                sql += "FROM t_history_chat_logs AS chat ";
                sql += "LEFT JOIN m_users AS mu ON ( mu.id = chat.m_users_id ) ";
                sql += "WHERE t_histories_id = ? ORDER BY created";

            pool.query(sql, [chatData.historyId], function(err, rows){
              if ( err !== null && err !== '' ) return false; // DB接続断対応
              var messages = ( isset(rows) ) ? rows : [];
              var setList = {};
              for (var i = 0; i < messages.length; i++) {
                var date = messages[i].created;
                date = new Date(date);
                // if ( ('userName' in messages[i]) && obj.showName !== 1 ) {
                //   delete messages[i].userName;
                // }
                setList[fullDateTime(messages[i].created)] = messages[i];
              }
              var autoMessages = [];
              if(obj.sincloSessionId in sincloCore[obj.siteKey] && 'autoMessages' in sincloCore[obj.siteKey][obj.sincloSessionId] ) {
                var autoMessageArray = sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages;
                for(var key in autoMessageArray) {
                  autoMessages.push(autoMessageArray[key]);
                }
              }
              for (var i = 0; i < autoMessages.length; i++) {
                var date = autoMessages[i].created;
                date = new Date(date);
                // if ( ('userName' in autoMessages[i]) && obj.showName !== 1 ) {
                //   delete autoMessages[i].userName;
                // }
                setList[fullDateTime(autoMessages[i].created) + '_'] = autoMessages[i];
              }
              var scenarioMessages = [];
              if(obj.sincloSessionId in sincloCore[obj.siteKey] && 'scenario' in sincloCore[obj.siteKey][obj.sincloSessionId] ) {
                var scenariosObj = sincloCore[obj.siteKey][obj.sincloSessionId].scenario;
                Object.keys(scenariosObj).forEach(function(scenarioId, index, arr){
                  var scenarioObj = scenariosObj[Number(scenarioId)];
                  Object.keys(scenarioObj).forEach(function(sequenceId, index2, arr2){
                    var sequenceObj = scenarioObj[sequenceId];
                    Object.keys(sequenceObj).forEach(function(categoryId, idx, array){
                      scenarioMessages.push(sequenceObj[categoryId]);
                    });
                  });
                });
              }
              for (var i = 0; i < scenarioMessages.length; i++) {
                var date = scenarioMessages[i].created;
                date = new Date(date);
                setList[fullDateTime(date) + "_"] = scenarioMessages[i];
              }
              chatData.messages = objectSort(setList);
              obj.chat = chatData;
              console.log(chatData);
              emit.toMine('chatMessageData', obj, socket);
            });
        }
        else {
            obj.chat = chatData;
            emit.toMine('chatMessageData', obj, socket);
        }
    },
    commit: function(d){ // DBに書き込むとき
      var insertData = {
        t_histories_id: sincloCore[d.siteKey][d.tabId].historyId,
        m_companies_id: companyList[d.siteKey],
        visitors_id: d.userId,
        m_users_id: d.mUserId,
        message: d.chatMessage,
        message_type: d.messageType,
        message_distinction: d.messageDistinction,
        message_request_flg: d.messageRequestFlg,
        achievement_flg: d.achievementFlg
      };

      pool.query('SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;', insertData.t_histories_id,
        function(err, rows){
          if ( err !== null && err !== '' ) return false; // DB接続断対応
          if ( rows && rows[0] ) {
            insertData.t_history_stay_logs_id = isset(d.stayLogsId) ? d.stayLogsId : rows[0].id;
          }
          insertData.created = (('created' in d)) ? new Date(d.created) : new Date();


          // オートメッセージとシナリオの場合は既読
          if (Number(insertData.message_type === 3) || Number(insertData.message_type === 22) || Number(insertData.message_type === 23)) {
            insertData.message_read_flg = 1;
            insertData.message_request_flg = chatApi.cnst.requestFlg.noFlg;
            insertData.message_distinction = d.messageDistinction;
          } else if((Number(insertData.message_type)  === 1 && d.hasOwnProperty('notifyToCompany') && !d.notifyToCompany) || Number(insertData.message_type)  === 12 || Number(insertData.message_type)  === 13) {
          // サイト訪問者からのチャットで通知しない場合は既読にする
            insertData.message_read_flg = 1;
            insertData.message_distinction = d.messageDistinction;
          }

          pool.query('INSERT INTO t_history_chat_logs SET ?', insertData, function(error,results,fields){
            if ( !isset(error) ) {
              if ( !isset(sincloCore[d.siteKey][d.tabId].sessionId)) return false;
              var sendData = {
                tabId: d.tabId,
                chatId: results.insertId,
                messageType: d.messageType,
                created: insertData.created,
                ret: true,
                chatMessage: d.chatMessage,
                siteKey: d.siteKey,
                matchAutoSpeech: !d.notifyToCompany,
                isScenarioMessage: d.isScenarioMessage
              };

              // 担当者のいない消費者からのメッセージの場合
              if ( d.messageType === 1 && !getChatSessionIds(d.siteKey, d.sincloSessionId, 'chat') ) {
                // 自動返信（チャットボット）の対象ではないメッセージが到達し、sorryメッセージ発動用のタイマーがあればクリアする。（重複表示防止）
                if (chatApi.sendCheckTimerList.hasOwnProperty(d.tabId) && (!d.hasOwnProperty('isAutoSpeech') || !d.isAutoSpeech)) {
                  clearTimeout(chatApi.sendCheckTimerList[d.tabId]);
                  chatApi.sendCheckTimerList[d.tabId] = null;
                }

                // 応対可能かチェック(対応できるのであれば trueが返る)
                chatApi.sendCheck(d, function(err, ret){
                  sendData.opFlg = ret.opFlg;
                  //初回通知メッセージ利用するの場合
                  //初回通知メッセージの場合
                  if(ret.message == null && ret.in_flg == chatApi.cnst.inFlg.flg
                    && d.notifyToCompany && d.initialNotification == true) {
                    sendData.notification = true;
                    sendData.mUserId = d.mUserId;
                    sendData.messageDistinction = d.messageDistinction;
                    sendData.userId = d.userId;
                  }
                  // 書き込みが成功したら顧客側に結果を返す
                  //emit.toUser('sendChatResult', sendData, sId);
                  var sincloSessionId = sincloCore[d.siteKey][d.tabId].sincloSessionId;
                  sendData.sincloSessionId = sincloSessionId;
                  emit.toSameUser('sendChatResult', sendData, d.siteKey, sincloSessionId);
                  // 保持していたオートメッセージを空にする
                  sincloCore[d.siteKey][sincloSessionId].autoMessages;
                  if(d.sendMailFlg) {
                    sendMail(d.autoMessageId, results.insertId, function(){
                      console.log("send mail");
                    });
                  }
                  if (Number(insertData.message_type) === 3) return false;
                  // 書き込みが成功したら企業側に結果を返す
                  var sendChatData = {
                    tabId: d.tabId,
                    sincloSessionId: sincloSessionId,
                    opFlg: sendData.opFlg,
                    chatId: results.insertId,
                    sort: fullDateTime(insertData.created),
                    created: insertData.created,
                    userId: insertData.m_users_id,
                    messageType: d.messageType,
                    ret: true,
                    message: d.chatMessage,
                    siteKey: d.siteKey,
                    notifyToCompany: d.isScenarioMessage ? !d.isScenarioMessage : d.notifyToCompany
                  };
                  if(functionManager.isEnabled(d.siteKey, functionManager.keyList.monitorPollingMode)) {
                    var sId = sincloCore[d.siteKey][d.tabId].sessionId;
                    Object.keys(customerList[d.siteKey]).forEach(function(key){
                      if(key.indexOf(sId) >= 0) {
                        sendChatData.customerInfo = customerList[d.siteKey][key];
                        chatApi.getUnreadCnt(sendChatData.customerInfo, function (ret) {
                          sendChatData.customerInfo['chatUnreadId'] = ret.chatUnreadId;
                          sendChatData.customerInfo['chatUnreadCnt'] = ret.chatUnreadCnt ? ret.chatUnreadCnt : 0;
                          emit.toCompany('sendChatResult', sendChatData, d.siteKey);
                        });
                      }
                    });
                  } else {
                    emit.toCompany('sendChatResult', sendChatData, d.siteKey);
                  }

                  if(d.messageType === 1 && insertData.message_read_flg != 1) {
                    sincloCore[d.siteKey][d.tabId].chatUnreadCnt++;
                  }

                  //通知された場合
                  if(ret.opFlg === true && d.notifyToCompany) {
                    pool.query("UPDATE t_history_chat_logs SET notice_flg = 1 WHERE t_histories_id = ? AND message_type = 1 AND id = ?;",
                      [sincloCore[d.siteKey][d.tabId].historyId, results.insertId], function(err, ret, fields){}
                    );
                    return false;
                  }

                  // 応対不可だった場合、既読にする
                  historyId = sincloCore[d.siteKey][d.tabId].historyId;
                  pool.query("UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND message_type = 1 AND id <= ?;",
                    [historyId, results.insertId], function(err, ret, fields){
                      if(d.messageType === 1) {
                        sincloCore[d.siteKey][d.tabId].chatUnreadCnt > 0 ? sincloCore[d.siteKey][d.tabId].chatUnreadCnt-- : 0;
                      }
                    }
                  );

                  // 自動応対メッセージではなく、Sorryメッセージがある場合は送る
                  if ( ret.message !== "" && (!d.hasOwnProperty('isAutoSpeech') || !d.isAutoSpeech)) {
                    chatApi.sendCheckTimerList[d.tabId] = setTimeout(function(){
                      delete chatApi.sendCheckTimerList[d.tabId];
                      // Sorryメッセージを送る
                      var obj = d;
                      obj.chatMessage = ret.message;
                      obj.messageType = chatApi.cnst.observeType.sorry;
                      obj.messageRequestFlg = chatApi.cnst.requestFlg.noFlg;
                      chatApi.set(obj);
                    }, 3000);
                  }
                });
              }
              else {
                // 書き込みが成功したら顧客側に結果を返す
                var sincloSessionId = sincloCore[d.siteKey][d.tabId].sincloSessionId;
                sendData.sincloSessionId = sincloSessionId;
                emit.toSameUser('sendChatResult', sendData, d.siteKey, sincloSessionId);
                if(d.sendMailFlg) {
                  sendMail(d.autoMessageId, results.insertId, function(){
                    console.log("send mail");
                  });
                }
                if (Number(insertData.message_type) === 3) return false;

                // 書き込みが成功したら企業側に結果を返す

                var sendChatData = {
                  tabId: d.tabId,
                  sincloSessionId: sincloSessionId,
                  chatId: results.insertId,
                  sort: fullDateTime(insertData.created),
                  created: insertData.created,
                  userId: insertData.m_users_id,
                  messageType: d.messageType,
                  ret: true,
                  message: d.chatMessage,
                  siteKey: d.siteKey,
                  notifyToCompany: d.isScenarioMessage ? !d.isScenarioMessage : d.notifyToCompany
                };

                if(functionManager.isEnabled(d.siteKey, functionManager.keyList.monitorPollingMode)) {
                  var sId = sincloCore[d.siteKey][d.tabId].sessionId;
                  Object.keys(customerList[d.siteKey]).forEach(function(key){
                    if(key.indexOf(sId) >= 0) {
                      sendChatData.customerInfo = customerList[d.siteKey][key];
                    }
                  });
                }

                emit.toCompany('sendChatResult', sendChatData, d.siteKey);
                if(d.messageType === 1) {
                  sincloCore[d.siteKey][d.tabId].chatUnreadCnt++;
                }
              }

              //オペレータリクエスト件数
              //リクエストチャットか確認
              if(d.messageRequestFlg == 1) {
                var companyId = companyList[d.siteKey];
                var getUserInfo = "SELECT chat.sc_flg as sc_flg, widget.display_type FROM m_companies AS comp LEFT JOIN m_widget_settings AS widget ON ( comp.id = widget.m_companies_id ) LEFT JOIN m_chat_settings AS chat ON ( chat.m_companies_id = widget.m_companies_id ) WHERE comp.id = ?;";
                pool.query(getUserInfo, [companyId], function(err, result){
                  //ウィジェットが常に表示する場合
                  if(result[0].display_type == 1 ){
                    //対応数上限設定ある場合
                    if(Number(result[0].sc_flg) == 1) {
                      //オペレータがいる場合
                      if(isset(scList[d.siteKey])
                        && isset(scList[d.siteKey].user)
                        && Object.keys(scList[d.siteKey].user)
                        && Object.keys(scList[d.siteKey].user).length !== 0) {
                        for (key in Object.keys(scList[d.siteKey].user)) {
                          var userId = Object.keys(scList[d.siteKey].user)[key];
                          //対応数がMAX人数か確認
                          if(scList[d.siteKey].user[userId] > scList[d.siteKey].cnt[userId]) {
                            addChatActiveUser(results.insertId,userId,d.siteKey);
                          }
                        }
                      }
                    }
                    //対応数上限設定していない場合
                    else {
                      //オペレータがいる場合
                      if(isset(company.info[d.siteKey])
                        && Object.keys(company.info[d.siteKey])
                        && Object.keys(company.info[d.siteKey]).length !== 0) {
                        for (key in Object.keys(company.info[d.siteKey])) {
                          var userId = Object.keys(company.info[d.siteKey])[key];
                          addChatActiveUser(results.insertId,userId,d.siteKey);
                        }
                      }
                    }
                  }

                  //オペレータが待機中のみ表示し、待機中オペレータがいる場合場合
                  else if(result[0].display_type == 2 && Object.keys(activeOperator[d.siteKey]) && Object.keys(activeOperator[d.siteKey]).length !== 0)　{
                    //対応数上限設定ある場合
                    if(Number(result[0].sc_flg) == 1) {
                      for (key in Object.keys(activeOperator[d.siteKey])) {
                        userId = Object.keys(activeOperator[d.siteKey])[key];
                        //対応数がMAX人数か確認
                        if(scList[d.siteKey].user[userId] > scList[d.siteKey].cnt[userId]) {
                          addChatActiveUser(results.insertId,userId,d.siteKey);
                        }
                      }
                    }
                    //対応数上限を設定していない場合
                    else{
                      for (key in Object.keys(activeOperator[d.siteKey])) {
                        userId = Object.keys(activeOperator[d.siteKey])[key];
                        addChatActiveUser(results.insertId,userId,d.siteKey);
                      }
                    }
                  }
                });
              }
            }

            else {
              // 書き込みが失敗したらエラーを渡す
              return emit.toUser('sendChatResult', {tabId: d.tabId, messageType: d.messageType, ret: false, siteKey: d.siteKey}, d.siteKey);
            }
          });
        }
      );
    },
    notifyCommit: function(name, d){ // DBに書き込むとき

      var insertData = {
        t_histories_id: getSessionId(d.siteKey, d.tabId, 'historyId'),
        m_companies_id: companyList[d.siteKey],
        visitors_id: d.visitorsId,
        m_users_id: d.userId,
        message: d.chatMessage,
        message_type: d.messageType,
        message_distinction: d.messageDistinction,
        message_request_flg: chatApi.cnst.requestFlg.noFlg,
        message_read_flg: 1,
        created: new Date(d.created)
      };

      pool.query('SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;', insertData.t_histories_id,
        function(err, rows){
          if ( err !== null && err !== '' ) return false; // DB接続断対応
          if ( rows && rows[0] ) {
            insertData.t_history_stay_logs_id = rows[0].id;
          }
          pool.query('INSERT INTO t_history_chat_logs SET ?', insertData, function(error,results,fields){
            if ( isset(error) ) {
              d.error = error;
              d.ret = false;
              console.log("RECORD INSERT ERROR: notifyCommit-func:" + error);
            }
            if ( results.hasOwnProperty('insertId') ) {
              d.id = results.insertId;
              d.created = fullDateTime(d.created);
            }
            emit.toCompany(name, d, d.siteKey);
          });
        }
      );
    },
    sendUnreadCnt: function(evName, obj, toUserFlg){
      var sql, ret = {tabId: obj.tabId, sincloSessionId: obj.sincloSessionId, chatUnreadId:null, chatUnreadCnt:0}, sincloSessionId = obj.sincloSessionId, siteId = companyList[obj.siteKey];
      sql  = " SELECT chat.id AS chatId, his.visitors_id, his.tab_id, chat.message FROM t_histories AS his";
      sql += " INNER JOIN t_history_chat_logs AS chat ON ( his.id = chat.t_histories_id )";
      sql += " WHERE his.tab_id = ? AND his.m_companies_id = ? AND chat.message_type = 1";
      sql += "   AND chat.m_users_id IS NULL AND chat.message_read_flg != 1 ORDER BY chat.id desc";
      pool.query(sql, [sincloSessionId, siteId], function(err, rows){
        if ( err !== null && err !== '' ) return false; // DB接続断対応
        if ( !isset(err) && (rows.length > 0 && isset(rows[0].chatId))) {
          ret.chatUnreadId = rows[0].chatId;
          if(isset(sincloCore[obj.siteKey])
            && isset(sincloCore[obj.siteKey][obj.tabId])
            && isset(sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt)) {
            if (rows.length > 0 && sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt === 0) {
              ret.chatUnreadCnt = rows.length;
            } else {
              ret.chatUnreadCnt = sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt;
            }
            console.log("set unreadCnt " + obj.tabId + " " + ret.chatUnreadCnt);
            sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt = ret.chatUnreadCnt;
          } else {
            ret.chatUnreadCnt = rows.length;
          }
        }
        emit.toCompany(evName, ret, obj.siteKey);
        if ( toUserFlg ) {
          emit.toMine(evName, ret, socket);
        }
      });
    },
    getUnreadCnt: function(obj, callback){
      var sql, ret = {tabId: obj.tabId, sincloSessionId: obj.sincloSessionId, chatUnreadId:null, chatUnreadCnt:0}, sincloSessionId = obj.sincloSessionId, siteId = companyList[obj.siteKey];
      sql  = " SELECT chat.id AS chatId, his.visitors_id, his.tab_id, chat.message FROM t_histories AS his";
      sql += " INNER JOIN t_history_chat_logs AS chat ON ( his.id = chat.t_histories_id )";
      sql += " WHERE his.tab_id = ? AND his.m_companies_id = ? AND chat.message_type = 1";
      sql += "   AND chat.m_users_id IS NULL AND chat.message_read_flg != 1 ORDER BY chat.id desc";
      pool.query(sql, [sincloSessionId, siteId], function(err, rows){
        if ( err !== null && err !== '' ) return false; // DB接続断対応
        if ( !isset(err) && (rows.length > 0 && isset(rows[0].chatId))) {
          ret.chatUnreadId = rows[0].chatId;
          if(isset(sincloCore[obj.siteKey])
            && isset(sincloCore[obj.siteKey][obj.tabId])
            && isset(sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt)) {
            if (rows.length > 0 && sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt === 0) {
              ret.chatUnreadCnt = rows.length;
            } else {
              ret.chatUnreadCnt = sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt;
            }
          } else {
            ret.chatUnreadCnt = rows.length;
          }
        }
        callback(ret);
      });
    },
    calcScNum: function(obj, userId){ /* sincloCoreから対象ユーザーのチャット対応状態を算出 */
      var scNum = 0;
      var sincloSessionIds = [];
      if ( !sincloCore.hasOwnProperty(obj.siteKey) ) return scNum;
      var tabIds = Object.keys(sincloCore[obj.siteKey]);
      for (var i = 0; i < tabIds.length; i++) {
        if(tabIds[i] && tabIds[i].indexOf("_") >= 0) {
          var tabData = sincloCore[obj.siteKey][tabIds[i]];
          if (tabData.hasOwnProperty("chat") && isNumber(tabData.chat)) {
            // 同一のsincloSessionIdを保有するユーザーは同時応対数１とする
            if (Number(tabData.chat) === Number(userId) && sincloSessionIds.indexOf(tabData.sincloSessionId) === -1) {
              scNum++;
              sincloSessionIds.push(tabData.sincloSessionId);
            }
          }
        }
      }
      return scNum;
    },
    sendCheckTimerList: {},
    widgetCheck: function(d, callback){ return this.scCheck(1, d, callback) }, // ウィジェット表示チェック
    sendCheck: function(d, callback){ return this.scCheck(2, d, callback) }, // Sorryメッセージ送信チェック
    scCheck: function(type, d, callback){
      var companyId = companyList[d.siteKey];
      var siteKey = d.siteKey;

      var getUserSQL = "SELECT IFNULL(chat.sc_flg, 2) as sc_flg,in_flg,sorry_message, outside_hours_sorry_message, wating_call_sorry_message, no_standby_sorry_message,initial_notification_message, widget.display_type FROM m_companies AS comp LEFT JOIN m_widget_settings AS widget ON ( comp.id = widget.m_companies_id ) LEFT JOIN m_chat_settings AS chat ON ( chat.m_companies_id = widget.m_companies_id ) WHERE comp.id = ?;";
      pool.query(getUserSQL, [companyId], function(err, rows){
        if ( err !== null && err !== '' ) return false; // DB接続断対応
        var ret = false, message = null;
        now = new Date();
        nowDay = now.getDay();
        var timeData = [];
        var active_flg = "";
        var check = "";
        dateParse = Date.parse(now);
        date = now.getFullYear() + "/" + ("0"+(now.getMonth()+1)).slice(-2) +'/'+ ("0"+(now.getDate())).slice(-2) + " ";
        if(common.operationHourSettings[siteKey] != "") {
          for(var i=0; i<common.operationHourSettings[siteKey].length; i++){
            dayType = JSON.parse(common.operationHourSettings[siteKey][i].type);
            //営業時間設定の条件が「毎日」の場合
            if(dayType == 1) {
              var day = { 0:'sun', 1:'mon', 2:'tue', 3:'wed', 4:'thu', 5:'fri', 6:'sat'};
              day = day[nowDay];
              timeData = JSON.parse(common.operationHourSettings[siteKey][i].time_settings).everyday[day];
              publicHolidayData = JSON.parse(common.operationHourSettings[siteKey][i].time_settings).everyday['pub'];
            }
            //営業時間設定の条件が「平日・週末」の場合
            else {
              var day = { 0:'sun', 1:'mon', 2:'tue', 3:'wed', 4:'thu', 5:'fri', 6:'sat'};
              if(nowDay == 1 || nowDay == 2 || nowDay == 3 || nowDay == 4 || nowDay == 5) {
                var day = 'week';
              }
              else {
                var day = 'weekend';
              }
              timeData = JSON.parse(common.operationHourSettings[siteKey][i].time_settings).weekly[day];
              publicHolidayData = JSON.parse(common.operationHourSettings[siteKey][i].time_settings).weekly['weekpub'];
            }
            active_flg = JSON.parse(common.operationHourSettings[siteKey][i].active_flg);
          }
        }
        if( rows && rows[0] ) {

          if(rows[0].sorry_message == "") {
            //営業時間外sorryメッセージ
            var outside_hours_sorry_message = rows[0].outside_hours_sorry_message;
            //待ち呼sorryメッセージ
            var wating_call_sorry_message = rows[0].wating_call_sorry_message;
            //待機なしsorryメッセージ
            var no_standby_sorry_message = rows[0].no_standby_sorry_message;
          }
          else {
            //営業時間外sorryメッセージ
            var outside_hours_sorry_message = rows[0].sorry_message;
            //待ち呼sorryメッセージ
            var wating_call_sorry_message = rows[0].sorry_message;
            //待機なしsorryメッセージ
            var no_standby_sorry_message = rows[0].sorry_message;
          }

          // ウィジェットが非表示の場合
          if (　type ==1 && rows[0].display_type === 3 ) {
            return callback(true, {opFlg: false, message: no_standby_sorry_message});
          }
          else if( type ==2 && rows[0].display_type === 3) {
            //営業時間を利用する場合
            if(active_flg == 1) {
              for(var i2=0; i2<common.publicHolidaySettingsArray.length; i2++) {
                //祝日の場合
                if( ("0"+(now.getMonth()+1)).slice(-2) +'/'+ ("0"+(now.getDate())).slice(-2) == common.publicHolidaySettingsArray[i2].month +'/'+ common.publicHolidaySettingsArray[i2].day) {
                  //祝日の営業時間設定が「休み」でない場合
                  if(publicHolidayData[0].start != "" && publicHolidayData[0].end != "") {
                    for(var i=0; i<publicHolidayData.length; i++){
                      var endTime = publicHolidayData[i].end;
                      // 営業時間の終了時刻が24:00の場合
                      if(publicHolidayData[i].end == "24:00") {
                        endTime = "23:59:59";
                      }
                      // 営業時間内の場合
                      if( Date.parse(new Date(date + publicHolidayData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + endTime))) {
                        return callback(true, {opFlg: false, message: no_standby_sorry_message});
                        break;
                      }
                    }
                  }
                  //営業時間外の場合
                  return callback(true, {opFlg: false, message: outside_hours_sorry_message});
                }
              }
              // 祝日でない場合、営業時間設定が「休み」でない場合
              if(timeData[0].start != "" && timeData[0].end != "") {
                for(var i=0; i<timeData.length; i++){
                  var endTime = timeData[i].end;
                  // 営業時間の終了時刻が024:00の場合
                  if(timeData[i].end == "24:00") {
                    endTime = "23:59:59";
                  }
                  // 営業時間内の場合
                  if( Date.parse(new Date(date + timeData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + endTime))) {
                    check = true;
                    return callback(true, {opFlg: false, message: no_standby_sorry_message});
                    break;
                  }
                }
                //営業時間外の場合
                if(check != true) {
                  return callback(true, {opFlg: false, message: outside_hours_sorry_message});
                }
              }
            }
            //営業時間を利用しない場合
            else {
              return callback(true, {opFlg: false, message: no_standby_sorry_message});
            }
          }


          // ウィジェット表示のジャッジの場合、常に表示は必ずtrue
          if ( type === 1 && rows[0].display_type === 1 ) {
            return callback(true, {opFlg: true, message: no_standby_sorry_message});
          }
          // ウィジェット表示のジャッジの場合、営業時間内のみ表示するの場合、営業時間内の場合はtrue
          if ( type === 1 && rows[0].display_type === 4 && active_flg == 1) {
            // 祝日の場合
            for(var i2=0; i2<common.publicHolidaySettingsArray.length; i2++) {
              if(("0"+(now.getMonth()+1)).slice(-2) +'/'+ ("0"+(now.getDate())).slice(-2) == common.publicHolidaySettingsArray[i2].month +'/'+ common.publicHolidaySettingsArray[i2].day) {
                if(publicHolidayData[0].start != "" && publicHolidayData[0].end != "") {
                  for(var i=0; i<publicHolidayData.length; i++){
                    var endTime = publicHolidayData[i].end;
                    // 営業時間の終了時刻が24:00の場合
                    if(publicHolidayData[i].end == "24:00") {
                      endTime = "23:59:59";
                    }
                    if( Date.parse(new Date(date + publicHolidayData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + endTime))) {
                      return callback(true, {opFlg: true, message: no_standby_sorry_message});
                      break;
                    }
                  }
                }
                return callback(true, {opFlg: false, message: outside_hours_sorry_message});
              }
            }
            // 祝日でない場合、営業時間設定が「休み」でない場合
            if(timeData[0].start != "" && timeData[0].end != "") {
              for(var i=0; i<timeData.length; i++){
                var endTime = timeData[i].end;
                // 営業時間の終了時刻が24:00の場合
                if(timeData[i].end == "24:00") {
                  endTime = "23:59:59";
                }
                // 営業時間内の場合
                if( Date.parse(new Date(date + timeData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + endTime))) {
                  return callback(true, {opFlg: true, message: no_standby_sorry_message});
                  break;
                }
              }
            }
            return callback(true, {opFlg: false, message: outside_hours_sorry_message});
          }




          // チャット上限数を設定していない場合
          if ( Number(rows[0].sc_flg) === 2 ) {
            // オペレーターが待機している場合
            if ( type === 1 && (rows[0].display_type === 2 && getOperatorCnt(d.siteKey) > 0)
            ) {
              return callback(true, {opFlg: true, message: outside_hours_sorry_message});
            }
            // 営業時間設定を利用している場合
            if (active_flg === 1) {
              //祝日の場合
              for(var i2=0; i2<common.publicHolidaySettingsArray.length; i2++) {
                if(("0"+(now.getMonth()+1)).slice(-2) +'/'+ ("0"+(now.getDate())).slice(-2) == common.publicHolidaySettingsArray[i2].month +'/'+ common.publicHolidaySettingsArray[i2].day) {
                  check = true;
                  //祝日の営業時間設定が「休み」でない場合
                  if(publicHolidayData[0].start != "" && publicHolidayData[0].end != "") {
                    for(var i=0; i<publicHolidayData.length; i++){
                      var endTime = publicHolidayData[i].end;
                      // 営業時間の終了時刻が24:00の場合
                      if(publicHolidayData[i].end == "24:00") {
                        endTime = "23:59:59";
                      }
                      //営業時間内の場合
                      if( Date.parse(new Date(date + publicHolidayData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + publicHolidayData[i].end)) ) {
                        // オペレータが待機している場合
                        if ( (rows[0].display_type === 2 && getOperatorCnt(d.siteKey) > 0) ||
                          (rows[0].display_type === 1 && getOperatorCnt(d.siteKey) > 0) ||
                          (rows[0].display_type === 4 && getOperatorCnt(d.siteKey) > 0)
                        ) {
                          return callback(true, {opFlg: true, message: null, in_flg: rows[0].in_flg});
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
              if(check != true) {
                //営業時間設定が「休み」の場合
                if(timeData[0].start == "" && timeData[0].end == "") {
                  ret = false;
                  message = outside_hours_sorry_message;
                }
                //営業時間設定が「休み」でない場合
                else {
                  for(var i=0; i<timeData.length; i++){
                    var endTime = timeData[i].end;
                    // 営業時間の終了時刻が24:00の場合
                    if(timeData[i].end == "24:00") {
                      endTime = "23:59:59";
                    }
                    //営業時間内
                    if( Date.parse(new Date(date + timeData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + endTime)) ) {
                      check = true;
                      //オペレータが待機している場合
                      if ( (rows[0].display_type === 2 && getOperatorCnt(d.siteKey) > 0) ||
                        (rows[0].display_type === 1 && getOperatorCnt(d.siteKey) > 0) ||
                        (rows[0].display_type === 4 && getOperatorCnt(d.siteKey) > 0)
                      ) {
                          return callback(true, {opFlg: true, message: null, in_flg: rows[0].in_flg});
                      }
                      //オペレータが待機していない場合
                      else {
                        ret = false;
                        message = no_standby_sorry_message;
                      }
                    }
                  }
                  // 営業時間外の場合
                  if(check != true) {
                    ret = false;
                    message = outside_hours_sorry_message;
                  }
                }
              }
            }
            //営業時間設定を利用していない場合
            else {
              //オペレータが待機している場合
              if ( (rows[0].display_type === 2 && getOperatorCnt(d.siteKey) > 0) ||
                (rows[0].display_type === 1 && getOperatorCnt(d.siteKey) > 0) ||
                (rows[0].display_type === 4 && getOperatorCnt(d.siteKey) > 0)
              ) {
                return callback(true, {opFlg: true, message: null, in_flg: rows[0].in_flg});
              }
              //オペレータが待機していない場合
              else {
                ret = false;
                message = no_standby_sorry_message;
              }
            }
          }


          // チャット上限数を設定している場合
          else if ( Number(rows[0].sc_flg) === 1 ) {
            if ( type === 1 && rows[0].display_type === 2 && scList.hasOwnProperty(d.siteKey) ) {
              var userIds = Object.keys(scList[d.siteKey].user);
              if ( userIds.length !== 0 ) {
                for (var i = 0; i < userIds.length; i++) {
                  if ( Number(scList[d.siteKey].user[userIds[i]]) === Number(scList[d.siteKey].cnt[userIds[i]]) ) continue;
                  return callback(true, {opFlg: true, message: outside_hours_sorry_message});
                }
              }
            }
            //営業時間設定を利用している場合
            if (active_flg === 1) {
              for(var i2=0; i2<common.publicHolidaySettingsArray.length; i2++) {
                //祝日の場合
                if(("0"+(now.getMonth()+1)).slice(-2) +'/'+ ("0"+(now.getDate())).slice(-2) == common.publicHolidaySettingsArray[i2].month +'/'+ common.publicHolidaySettingsArray[i2].day) {
                  check = true;
                  //祝日の営業時間設定が「休み」でない場合
                  if(publicHolidayData[0].start　!= "" && publicHolidayData[0].end != "") {
                    for(var i=0; i<publicHolidayData.length; i++){
                      var endTime = publicHolidayData[i].end;
                      // 営業時間の終了時刻が24:00の場合
                      if(publicHolidayData[i].end == "24:00") {
                        endTime = "23:59:59";
                      }
                      //営業時間内の場合
                      if( Date.parse(new Date(date + publicHolidayData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + endTime)) ) {
                        //オペレータが待機している場合
                        if ( (rows[0].display_type === 2 && getOperatorCnt(d.siteKey) > 0) ||
                          (rows[0].display_type === 1 && getOperatorCnt(d.siteKey) > 0) ||
                          (rows[0].display_type === 4 && getOperatorCnt(d.siteKey) > 0)
                        ) {
                          // チャット上限数をみる
                          if ( scList.hasOwnProperty(d.siteKey) ) {
                            var userIds = Object.keys(scList[d.siteKey].user);
                            if ( userIds.length !== 0 ) {
                              for (var i3 = 0; i3 < userIds.length; i3++) {
                                if ( Number(scList[d.siteKey].user[userIds[i]]) === Number(scList[d.siteKey].cnt[userIds[i]]) ) continue;
                                  return callback(true, {opFlg: true, message: null, in_flg: rows[0].in_flg});
                              }
                              //上限数を超えている場合
                              if(ret != true) {
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
              if(check != true) {
                //営業時間設定が「休み」の場合
                if(timeData[0].start === "" && timeData[0].end === "") {
                  ret = false;
                  message = outside_hours_sorry_message;
                }
                //営業時間設定が「休み」でない場合
                else {
                  for(var i=0; i<timeData.length; i++){
                    var endTime = timeData[i].end;
                    // 営業時間の終了時刻が24:00の場合
                    if(timeData[i].end == "24:00") {
                      endTime = "23:59:59";
                    }
                    //営業時間内の場合
                    if( Date.parse(new Date(date + timeData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + endTime)) ) {
                      check = true;
                      //オペレータが待機している場合
                      if ( (rows[0].display_type === 2 && getOperatorCnt(d.siteKey) > 0) ||
                        (rows[0].display_type === 1 && getOperatorCnt(d.siteKey) > 0) ||
                        (rows[0].display_type === 4 && getOperatorCnt(d.siteKey) > 0)
                      ) {
                        // チャット上限数をみる
                        if ( scList.hasOwnProperty(d.siteKey) ) {
                          var userIds = Object.keys(scList[d.siteKey].user);
                          if ( userIds.length !== 0 ) {
                            for (var i2 = 0; i2 < userIds.length; i2++) {
                              if ( Number(scList[d.siteKey].user[userIds[i]]) === Number(scList[d.siteKey].cnt[userIds[i]]) ) continue;
                                return callback(true, {opFlg: true, message: null, in_flg: rows[0].in_flg});
                            }
                            //上限数を超えている場合
                            if(ret != true) {
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
                  if(check != true) {
                    ret = false;
                    message = outside_hours_sorry_message;
                  }
                }
              }
            }
            //営業時間設定を利用しない場合
            else {
              //オペレータが待機している場合
              if ( (rows[0].display_type === 2 && getOperatorCnt(d.siteKey) > 0) ||
                (rows[0].display_type === 1 && getOperatorCnt(d.siteKey) > 0) ||
                (rows[0].display_type === 4 && getOperatorCnt(d.siteKey) > 0)
              ) {
                // チャット上限数をみる
                if ( scList.hasOwnProperty(d.siteKey) ) {
                  var userIds = Object.keys(scList[d.siteKey].user);
                  if ( userIds.length !== 0 ) {
                    for (var i = 0; i < userIds.length; i++) {
                      if ( Number(scList[d.siteKey].user[userIds[i]]) === Number(scList[d.siteKey].cnt[userIds[i]]) ) continue;
                        return callback(true, {opFlg: true, message: null, in_flg: rows[0].in_flg});
                    }
                    //上限数を超えている場合
                    if(ret != true) {
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
        else {
          if ( type === 1 ) { // ウィジェット
            ret = true;
          }
          return callback(false, {ret: ret, message: null});
        }
      });
    },
  };

  // 顧客情報取得
  var customerApi = {
    getInformations: function (visitorId, siteKey, callback) {
      pool.query('SELECT informations FROM m_customers WHERE m_companies_id = ? AND visitors_id = ? LIMIT 1;', [companyList[siteKey], visitorId], function(err, row) {
        if ( err !== null && err !== '' ) callback([]); // DB接続断対応
        if(isset(row) && isset(row[0]) && isset(row[0].informations)) {
          callback(JSON.parse(row[0].informations));
        } else {
          callback([]);
        }
      });
    }
  };

  // 接続時
  socket.on('connected', function (r) {
    var res = JSON.parse(r),
        send = {},
        type = "",
        siteKey = "",
        data = res.data,
        newTabId = "";
        send = data;

    if ( res.type !== 'admin' ) {
      if (res.tabId && (isset(sincloCore[res.siteKey]) && isset(sincloCore[res.siteKey][res.tabId]) && !isset(sincloCore[res.siteKey][res.tabId].timeoutTimer))) {
        // 別タブを開いたときに情報がコピーされてしまっている状態
        console.log("tabId is duplicate. change firstConnection flg " + res.tabId);
        data.firstConnection = true;
      }
      if (res.tabId && (isset(sincloCore[res.siteKey]) && isset(sincloCore[res.siteKey][res.tabId]) && 'timeoutTimer' in sincloCore[res.siteKey][res.tabId]) ) {
        var currentSincloSessionId = sincloCore[res.siteKey][res.tabId].sincloSessionId;
        if(currentSincloSessionId) {
          var oldSessionId = sincloCore[res.siteKey][res.tabId].sessionId;
          var sincloSession = sincloCore[res.siteKey][currentSincloSessionId];
          if(isset(sincloSession)) {
            var sessionIds = sincloSession.sessionIds;
            delete sessionIds[oldSessionId];
            if(currentSincloSessionId !== res.sincloSessionId && Object.keys(sessionIds).length === 0) {
              delete sincloCore[res.siteKey][currentSincloSessionId];
            }
          } else {
            console.log("currentSincloSession : " + currentSincloSessionId + " is null.");
          }
        }
      }

      if ( data.userId === undefined || data.userId === '' || data.userId === null ) {
        send.userId = makeUserId();
      }
      if ( !data.inactiveReconnect && ((res.sincloSessionId === undefined || res.sincloSessionId === '' || res.sincloSessionId === null)
        || !(res.siteKey in sincloCore)
        || !(res.sincloSessionId in sincloCore[res.siteKey])
        || sincloCore[res.siteKey][res.sincloSessionId].sessionIds === undefined)) {
        send.sincloSessionId = uuid.v4();
        send.sincloSessionIdIsNew = true;
        data.firstConnection = true;
      } else {
        send.sincloSessionIdIsNew = false;
      }
      if ( data.firstConnection || data.accessId === undefined || data.accessId === '' || data.accessId === null ) {
        send.accessId = ('000' + Math.floor(Math.random() * 10000)).slice(-4);
      }
      if ( res.token !== undefined ) {
        send.token = res.token;
      }
      // ページ表示開始時間
      var d = new Date();
      send.pagetime = Date.parse(d);

      // アクセス開始フラグ
      if ( data.firstConnection ) {
        send.time = send.pagetime;
      }

      send.ipAddress = getIp(socket);

    }
    // 企業キーが取得できなければスルー
    if ( res.siteKey ) {

      if ( res.type === 'admin' ) {
        socket.join(res.siteKey + emit.roomKey.company);
        var cnt = [], opKeys = [];

        if ( 'userId' in data && data.authority !== 99) {
            /* 企業ユーザーの管理 */
            company.user[socket.id] = {
                userId: data.userId,
                siteKey: res.siteKey
            };
            if ( !(res.siteKey in company.info) ) {
                company.info[res.siteKey] = {};
                company.timeout[res.siteKey] = {};
            }
            if ( !(data.userId in company.info[res.siteKey]) ) {
                company.info[res.siteKey][data.userId] = {};
            }
            if ( data.userId in company.timeout[res.siteKey] ) {
                clearTimeout(company.timeout[res.siteKey][data.userId]);
            }
            company.info[res.siteKey][data.userId][socket.id] = null;
            /* 企業ユーザーの管理 */

            /* 待機中ユーザーの管理 */
            if ( !(res.siteKey in activeOperator) ) {
                activeOperator[res.siteKey] = {};
            }
            // 待機中の場合
            if ( ('status' in data) && String(data.status) === '1' ) {
              activeOperator[res.siteKey][data.userId] = socket.id;
            }
            // 待機中でない場合
            else {
              if ( activeOperator[res.siteKey].hasOwnProperty(data.userId) ) {
                delete activeOperator[res.siteKey][data.userId];
              }
              if ( scList.hasOwnProperty(res.siteKey) && scList[res.siteKey].cnt.hasOwnProperty(data.userId) ) {
                delete scList[res.siteKey].cnt[data.userId];
                delete scList[res.siteKey].user[data.userId];
              }
              data.status = 0;
            }
            // 自身の初期ステータスを送る
            emit.toUser('cngOpStatus', {status: data.status}, activeOperator[res.siteKey][data.userId]);
            /* 待機中ユーザーの管理 */

            /* 同時対応数の管理 */
            if ( data.hasOwnProperty('scNum') ) {
              // ウィジェット表示設定を常に表示、もしくは待機中のみ表示の場合（かつ待機中の場合）
              if ( data.opFlg === false || (data.opFlg === true && data.hasOwnProperty('status') && Number(data.status) === 1) ) {
                if ( !scList.hasOwnProperty(res.siteKey) ) { scList[res.siteKey] = { user: { }, cnt: {} }; }
                scList[res.siteKey].user[data.userId] = data.scNum;
                scList[res.siteKey].cnt[data.userId] = chatApi.calcScNum(res, data.userId);
                console.log("【" + res.siteKey + "】新規：対応上限数設定 " + scList[res.siteKey].cnt[data.userId] + " / " + scList[res.siteKey].user[data.userId] +  " userId : " + data.userId);

                data.scInfo = scList[res.siteKey].cnt;
              }
            }
            else {
              // チャット対応上限の設定が無効化されている場合はオブジェクトを削除する
              if ( scList.hasOwnProperty(res.siteKey) ) {
                var getChatSettingSQL = "SELECT sc_flg FROM m_chat_settings WHERE m_companies_id = ?";
                pool.query(getChatSettingSQL, [companyList[res.siteKey]], function(err, rows){
                  if ( err !== null && err !== '' ) return false; // DB接続断対応
                  if ( rows && rows[0] && rows[0].sc_flg === 1 ) {
                    delete scList[res.siteKey];
                  }
                });
              }
            }
            /* 同時対応数の管理 */
        }
        if ( res.siteKey in company.info ) {
          cnt = Object.keys(company.info[res.siteKey]);
        }

        if ( res.siteKey in activeOperator ) {
          opKeys = Object.keys(activeOperator[res.siteKey]);
        }

        data.userCnt = cnt.length;
        data.onlineUserCnt = opKeys.length;

        data.activeOperatorList = activeOperator[res.siteKey];
        data.onlineOperatorList = company.info[res.siteKey];

        // 企業側に情報提供
        emit.toCompany('getAccessInfo', data, res.siteKey);
        // 消費者にアクセス情報要求
        //emit.toClient('getAccessInfo', send, res.siteKey);
        processReceiveAccessInfo(res.siteKey, socket);
      }
      else {
        chatApi.widgetCheck(res, function(err, ret){
          send.activeOperatorCnt = getOperatorCnt(res.siteKey);
          send.widget = ret.opFlg;
          send.opFlg = true;
          send.inactiveReconnect = Boolean(data.inactiveReconnect);
          if ( ret.opFlg === false ) {
            send.opFlg = false;
            if ( res.hasOwnProperty('tabId') && isset(getSessionId(res.siteKey, res.tabId, 'chat')) ) {
              send.opFlg = true;
            }
          }
          socket.join(res.siteKey + emit.roomKey.client);
          if(newTabId !== "") {
            send.newTabId = newTabId;
          }
          emit.toMine('accessInfo', send, socket);

        });
      }
    }
  });

  socket.on('getCustomerList', function(data, ack){
    var obj = JSON.parse(data);
    if(isset(obj.siteKey)) {
      emit.toMine('beginOfCustomerList', {}, socket);
      processReceiveAccessInfo(obj.siteKey, socket);
    }
    if(ack) {
      ack({
        scInfo: ( scList.hasOwnProperty(obj.siteKey) ) ? scList[obj.siteKey].cnt : {}
      });
    }
  });

  function processReceiveAccessInfo(siteKey, socket) {
    var counter = 0;
    var totalCounter = 0;
    var chunkSize = 100;
    var keyLength = 0;
    if(isset(customerList[siteKey])) {
      keyLength = Object.keys(customerList[siteKey]).length;
    }
    var arr = [keyLength];
    if(isset(customerList[siteKey])) {
      Object.keys(customerList[siteKey]).forEach(function (key) {
        var splitedKey = key.split("/#");
        if (splitedKey.length === 2 && isset(splitedKey[1])) {
          var targetSocketId = "/#" + splitedKey[1]
          if (!io.sockets.connected[targetSocketId]) {
            var targetTabId = customerList[siteKey][key].tabId;
            console.log("【" + siteKey + "】 customerList key : " + key + " client is not exist. deleting. targetTabId : " + targetTabId);
            if (targetTabId && targetTabId !== "") {
              emit.toCompany('unsetUser', {siteKey: siteKey, tabId: targetTabId}, siteKey);
            }
            delete customerList[siteKey][key];

            if (totalCounter === keyLength - 1) {
              emit.toMine("receiveAccessInfo", arr, socket);
              arr = [keyLength];
            }
            totalCounter++;
            return;
          }
        }
        var val = getConnectInfo(customerList[siteKey][key]);
        if (val.time) {
          val.term = timeCalculator(val);
        }
        customerApi.getInformations(val.userId, val.siteKey, function (information) {
          val.customerInfo = information;
          if (functionManager.isEnabled(siteKey, functionManager.keyList.chat)) {
            chatApi.getUnreadCnt(val, function (ret) {
              val['chatUnreadId'] = ret.chatUnreadId;
              val['chatUnreadCnt'] = ret.chatUnreadCnt ? ret.chatUnreadCnt : 0;
              if (functionManager.isEnabled(siteKey, functionManager.keyList.hideRealtimeMonitor)
                && functionManager.isEnabled(siteKey, functionManager.keyList.monitorPollingMode)
                && ((isset(val['chatUnreadCnt']) && val['chatUnreadCnt'] === 0) && !isset(val['responderId']) && !isset(val['chat']))) {
                // 何もしない
              } else {
                arr.push(val);
                counter++;
              }
              if (counter === chunkSize) {
                emit.toMine("receiveAccessInfo", arr, socket);
                counter = 0;
                arr = [keyLength];
              }
              if (totalCounter === keyLength - 1) {
                emit.toMine("receiveAccessInfo", arr, socket);
                if (functionManager.isEnabled(siteKey, functionManager.keyList.monitorPollingMode)) {
                  emit.toMine('endOfCustomerList', {}, socket);
                }
                arr = [keyLength];
              }
              totalCounter++;
            });
          } else {
            if (functionManager.isEnabled(siteKey, functionManager.keyList.hideRealtimeMonitor)
              && functionManager.isEnabled(siteKey, functionManager.keyList.monitorPollingMode)
              && ((!isset(val['chatUnreadCnt']) || val['chatUnreadCnt'] === 0) && !isset(val['responderId']) && !isset(val['chat']))) {
              // 何もしない
            } else {
              arr.push(val);
              counter++;
            }
            if (counter === chunkSize) {
              emit.toMine("receiveAccessInfo", arr, socket);
              counter = 0;
              arr = [keyLength];
            }
            if (totalCounter === keyLength - 1) {
              emit.toMine("receiveAccessInfo", arr, socket);
              if (functionManager.isEnabled(siteKey, functionManager.keyList.monitorPollingMode)) {
                emit.toMine('endOfCustomerList', {}, socket);
              }
              arr = [keyLength];
            }
            totalCounter++;
          }
        });
      });
    }
    if(isset(customerList[siteKey]) && Object.keys(customerList[siteKey]).length === 0) {
      emit.toMine("receiveAccessInfo", arr, socket);
      if(functionManager.isEnabled(siteKey, functionManager.keyList.monitorPollingMode)) {
        emit.toMine('endOfCustomerList', {}, socket);
      }
    }
  }

  socket.on("connectedForSync", function (data) {
    // ページ表示開始時間
    var d = new Date();
    // 待機オペレーター人数
    var obj = JSON.parse(data);
    var actOpCnt = getOperatorCnt(obj.siteKey);

    chatApi.widgetCheck(obj, function(err, ret){
      var opFlg = true;
      if ( ret.opFlg === false ) {
        opFlg = false;
        if ( obj.hasOwnProperty('tabId') && isset(getSessionId(obj.siteKey, obj.tabId, 'chat')) ) {
          opFlg = true;
        }
      }
      emit.toMine("retConnectedForSync", {
        pagetime: Date.parse(d),
        opFlg: opFlg,
        activeOperatorCnt: actOpCnt
      }, socket);
    });

  });

  socket.on("customerInfo", function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);
    if ( getSessionId(obj.siteKey, obj.tabId, 'chat') ) {
      obj.chat = getSessionId(obj.siteKey, obj.tabId, 'chat');
    }

    obj = getConnectInfo(obj);

    // IPアドレスの取得
    if ( !(('ipAddress' in obj) && isset(obj.ipAddress)) ) {
      obj.ipAddress = getIp(socket);
    }

    if( functionManager.isEnabled(obj.siteKey, functionManager.keyList.hideRealtimeMonitor) || functionManager.isEnabled(obj.siteKey, functionManager.keyList.monitorPollingMode)) {

    } else {
      emit.toCompany("sendCustomerInfo", obj, obj.siteKey);
    }

    customerList[obj.siteKey][obj.accessId + '_' + obj.ipAddress + '_' + socket.id] = obj;

    if ( (('contract' in obj) && ('chat' in obj.contract) && obj.contract.chat === false) || functionManager.isEnabled(obj.siteKey, functionManager.keyList.monitorPollingMode)) return false;
    chatApi.sendUnreadCnt("sendChatInfo", obj, false);
  });

  socket.on("getCustomerInfo", function(data) {
    var obj = JSON.parse(data);
    emit.toClient('confirmCustomerInfo', obj, obj.siteKey);
  });

  socket.on("searchCustomer", function(data){
    var obj = JSON.parse(data);
    var term = obj.term;
    var result = [];
    if('siteKey' in obj) {
      var keys = Object.keys(customerList[obj.siteKey]);
      if(keys && keys.length > 0) {
        keys.forEach(function(key) {
          var splitedKey = key.split("_");
          var targetTerm = obj.filterType === 1 ? splitedKey[0] : splitedKey[1];
          if(targetTerm) {
            if(targetTerm.indexOf(term) === 0) { // 前方一致検索
              var mergedObject = extend(customerList[obj.siteKey][key], sincloCore[obj.siteKey][customerList[obj.siteKey][key]['tabId']]);
              if(mergedObject.time) {
                mergedObject.term = timeCalculator(mergedObject);
              }
              result.push(mergedObject);
            }
          }
        });
      }
    }
    emit.toMine('searchCustomerResult', result, socket);
    // チャット未読
    for(var i=0; i < result.length; i++) {
      if ( ('contract' in result[i]) && ('chat' in result[i].contract) && result[i].contract.chat === false) break;
      chatApi.sendUnreadCnt("sendChatInfo", result[i], false);
    }
  });

  socket.on("connectSuccessForClient", function (data) {
    var obj = JSON.parse(data);
    // sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
  });

  socket.on("connectSuccess", function (data, ack) {
    var obj = JSON.parse(data);
    if ( !isset(sincloCore[obj.siteKey]) ) {
      sincloCore[obj.siteKey] = {};
    }
    if ( !isset(sincloCore[obj.siteKey][obj.tabId]) ) {
      sincloCore[obj.siteKey][obj.tabId] = {sincloSessionId: null, sessionId: null, subWindow: false, chatUnreadCnt: 0};
    }
    if ( isset(obj.sincloSessionId) && !isset(sincloCore[obj.siteKey][obj.sincloSessionId]) ) {
      sincloCore[obj.siteKey][obj.sincloSessionId] = {sessionIds: {}, autoMessages: {}, scenario: {}};
    }
    if ('timeoutTimer' in sincloCore[obj.siteKey][obj.tabId]) {
      clearTimeout(sincloCore[obj.siteKey][obj.tabId].timeoutTimer);
      sincloCore[obj.siteKey][obj.tabId].timeoutTimer = null;
    }

    var oldSessionId = sincloCore[obj.siteKey][obj.tabId].sessionId;
    if(oldSessionId && isset(obj.sincloSessionId)) {
      var sessionIds = sincloCore[obj.siteKey][obj.sincloSessionId].sessionIds;
      console.log("delete id : " + oldSessionId);
      delete sessionIds[oldSessionId];
      console.log("remains : " + Object.keys(sessionIds).length);
      Object.keys(sessionIds).forEach(function (key) {
        if (!isset(io.sockets.connected[key])) {
          console.log("delete not exist sessionId : " + key);
          delete sessionIds[key];
          console.log("remains : " + Object.keys(sessionIds).length);
          var keys = Object.keys(customerList[obj.siteKey]);
          if(keys && keys.length > 0) {
            keys.forEach(function(customerListId) {
              if(customerListId.indexOf(key) >= 0) {
                console.log("delete not exist customerList : " + customerListId);
                delete customerList[info.siteKey][customerListId];
              }
            });
          }
        }
      });
    }

    connectList[socket.id] = {siteKey: obj.siteKey, tabId: obj.tabId, userId: null, sincloSessionId: obj.sincloSessionId};
    sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
    if (isset(obj.sincloSessionId)) {
      sincloCore[obj.siteKey][obj.tabId].sincloSessionId = obj.sincloSessionId;
      sincloCore[obj.siteKey][obj.sincloSessionId].sessionIds[socket.id] = socket.id;
    }
    if (isset(obj.tmpAutoMessages)) {
      for(var key in obj.tmpAutoMessages) {
        if(typeof(obj.tmpAutoMessages[key]['created']) === "string") {
          obj.tmpAutoMessages[key]['created'] = new Date(obj.tmpAutoMessages[key]['created']);
        }
        sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages[key] = obj.tmpAutoMessages[key];
      }
    }
    if ( obj.subWindow ) {
      sincloCore[obj.siteKey][obj.tabId].toTabId = obj.to;
      sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
      sincloCore[obj.siteKey][obj.tabId].subWindow = true;
      if ( !getSessionId(obj.siteKey, obj.tabId, 'responderId') ) {
        sincloCore[obj.siteKey][obj.tabId].responderId = getSessionId(obj.siteKey, obj.to, 'responderId');
      }
      db.addDisplayShareHistory(sincloCore[obj.siteKey][obj.tabId].responderId, obj, socket); // 登録
      obj.responderId = getSessionId(obj.siteKey, obj.to, 'responderId');
      if ( getSessionId(obj.siteKey, obj.to, 'syncSessionId') ) {
        sincloCore[obj.siteKey][obj.to].syncSessionId = socket.id; // 同期先配列に、セッションIDを格納
      }
    }
    else if ( !getSessionId(obj.siteKey, obj.tabId, 'parentTabId') ) {
      connectList[socket.id] = {siteKey: obj.siteKey, tabId: obj.tabId, userId: obj.userId, sincloSessionId: obj.sincloSessionId};
      if ( ('reconnect' in obj) && obj.reconnect ) {
        socket.join(obj.siteKey + emit.roomKey.client);

        // sessionIdが消えてる可能性があるため、対応ユーザーIDを再セット
        if ( getSessionId(obj.siteKey, obj.to, 'responderId') ) {
          sincloCore[obj.siteKey][obj.tabId].responderId = getSessionId(obj.siteKey, obj.to, 'responderId');
          obj.responderId = getSessionId(obj.siteKey, obj.to, 'responderId');
        }
      }

      // 履歴作成
      db.addHistory(obj, socket);

      // IPアドレスの取得
      if ( !(('ipAddress' in obj) && isset(obj.ipAddress)) ) {
        obj.ipAddress = getIp(socket);
      }

      //FIXME 企業別機能設定（企業情報連携）
      getCompanyInfoFromApi(obj.ipAddress, function(data){
        if(data) {
          var response = JSON.parse(data);
          obj.orgName = response.data.orgName;
          obj.lbcCode = response.data.lbcCode;
          sincloCore[obj.siteKey][obj.tabId].orgName = obj.orgName;
          sincloCore[obj.siteKey][obj.tabId].lbcCode = obj.lbcCode;
          if(isset(customerList[obj.siteKey][obj.accessId + '_' + obj.ipAddress + '_' + socket.id])) {
            customerList[obj.siteKey][obj.accessId + '_' + obj.ipAddress + '_' + socket.id]['orgName'] = obj.orgName;
            customerList[obj.siteKey][obj.accessId + '_' + obj.ipAddress + '_' + socket.id]['lbcCode'] = obj.lbcCode;
          }
        }
        if(!functionManager.isEnabled(obj.siteKey, functionManager.keyList.monitorPollingMode)) {
          emit.toCompany('syncNewInfo', obj, obj.siteKey);
        }
      });
    }
    if(ack) {
      ack(data);
    }
  });
  // ウィジェットが生成されたことを企業側に通知する
  socket.on("syncReady", function(data){
    var obj = JSON.parse(data);
    if(!functionManager.isEnabled(obj.siteKey, functionManager.keyList.monitorPollingMode)) {
      emit.toCompany('syncNewInfo', obj, obj.siteKey);
    }
  });
  // アクティブ状態を送る
  socket.on("sendTabInfo", function(d){
    var obj = JSON.parse(d);
    if(!functionManager.isEnabled(obj.siteKey, functionManager.keyList.monitorPollingMode)) {
      emit.toCompany('retTabInfo', d, obj.siteKey);
    }

    // 画面同期中は同期フレーム本体に送る
    if ( ('connectToken' in obj) && isset(obj.connectToken) ) {
      emit.toUser('retTabInfo', obj, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
    }
    for ( var key in customerList[obj.siteKey] ){
      if(obj.tabId.indexOf(customerList[obj.siteKey][key]['tabId']) === 0) {
        customerList[obj.siteKey][key]['status'] = obj.status;
        break;
      }
    }
  });
  //ウィジェットを開いた事を通知する
  //現在はウィジェット表示ログを書き込むのみで、クライアント側はretTabInfoにwidgetの情報を付与してハンドリングしている
  socket.on("sendWidgetShown",function(d){
    var obj = JSON.parse(d);
    //ウィジェット件数登録処理
    if(obj.widget === true) {
      pool.query('SELECT * FROM t_history_widget_displays WHERE tab_id = ?',[obj.sincloSessionId || obj.tabId], function (err, results) {
        if(isset(err)) {
          console.log("RECORD SElECT ERROR: t_history_widget_displays(tab_id):" + err);
          return false;
        }
        //ウィジェットが初めて表示された場合
        if (Object.keys(results).length === 0) {
          //tabId登録
          pool.query('INSERT INTO t_history_widget_displays(m_companies_id,tab_id,created) VALUES(?,?,?)',[companyList[obj.siteKey],obj.sincloSessionId || obj.tabId,new Date()],function(err,results) {
            if(isset(err)) {
              console.log("RECORD INSERT ERROR: t_history_widget_displays(tab_id):" + err);
              return false;
            }
          });
        }
      });
    }
  });

  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  socket.on("sendAccessInfo", function (data) {
    var obj = JSON.parse(data);
    if( ('contract' in obj) && ('hideRealtimeMonitor' in obj.contract) && obj.contract.hideRealtimeMonitor === true) return false;
    obj.term = timeCalculator(obj);

    obj = getConnectInfo(obj);

    if ( getSessionId(obj.siteKey, obj.tabId, 'chat') ) {
      obj.chat = getSessionId(obj.siteKey, obj.tabId, 'chat');
    }

    // IPアドレスの取得
    if ( !(('ipAddress' in obj) && isset(obj.ipAddress)) ) {
      obj.ipAddress = getIp(socket);
    }
    chatApi.getUnreadCnt(obj, function (ret) {
      obj['chatUnreadId'] = ret.chatUnreadId;
      obj['chatUnreadCnt'] = ret.chatUnreadCnt ? ret.chatUnreadCnt : 0;
      // TODO ここを要求したユーザのみに送るようにする
      // FIXME 初回表示時リアルタイムモニタ表示なしの場合はこのデータを送らないようにする
      emit.toCompany("receiveAccessInfo", obj, obj.siteKey);
      if (('contract' in obj) && ('chat' in obj.contract) && obj.contract.chat === false) return false;
      chatApi.sendUnreadCnt("sendChatInfo", obj, false);
    });
  });
  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  // [管理]モニタリング開始
  socket.on('requestWindowSync', function (data) {
          var obj = JSON.parse(data);
          // 外部接続可能
          if ( Number(obj.type) === 2 ) {
              // 同形ウィンドウを作成するための情報取得依頼
              if ( !getSessionId(obj.siteKey, obj.tabId, 'sessionId') ) return false;
              sincloCore[obj.siteKey][obj.tabId].shareWindowFlg = true;
              sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
              emit.toUser('startWindowSync', data, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
          }
          // 今まで通り
          else {
              // 同形ウィンドウを作成するための情報取得依頼
              if ( !getSessionId(obj.siteKey, obj.tabId, 'sessionId') ) return false;
              sincloCore[obj.siteKey][obj.tabId].shareWindowFlg = false;
      sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
      sincloCore[obj.siteKey][obj.tabId].syncSessionId = null;
      sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
      emit.toUser('getWindowInfo', data, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
    }

  });

  // 同形ウィンドウを作成するための情報受け取り
  socket.on('sendWindowInfo', function (data) {
    var obj = JSON.parse(data);
    // 同形ウィンドウを作成するための情報渡し
    emit.toUser('windowSyncInfo', obj, getSessionId(obj.siteKey, obj.tabId, 'syncHostSessionId'));
  });

  // iframe用（接続直後と企業側リロード時）
  socket.on('connectFrame', function (data) {
    var obj = JSON.parse(data);
    if ( obj.siteKey ) {
      // 外部接続フレームの場合
      var parentTabId = getSessionId(obj.siteKey, obj.tabId, 'parentTabId');
      if ( parentTabId ) {
        socket.join(obj.siteKey + emit.roomKey.frame);
        if ( ('responderId' in obj) && ('connectToken' in obj) ) {
          sincloCore[obj.siteKey][parentTabId].responderId = obj.responderId; // 対応ユーザーID
          sincloCore[obj.siteKey][parentTabId].connectToken = obj.connectToken; // 接続トークン
        }
      }
      else {
        socket.join(obj.siteKey + emit.roomKey.client);
      }
    }
    if ( (obj.tabId in sincloCore[obj.siteKey]) ) {
      sincloCore[obj.siteKey][obj.tabId].syncFrameSessionId = socket.id; // フレームのセッションID
      if ( ('responderId' in obj) && ('connectToken' in obj) ) {
        sincloCore[obj.siteKey][obj.tabId].responderId = obj.responderId; // 対応ユーザーID
        sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken; // 接続トークン
      }
      if ( getSessionId(obj.siteKey, obj.tabId, 'connectToken') ) {
        obj.connectToken = getSessionId(obj.siteKey, obj.tabId, 'connectToken'); // 接続トークンを企業側へ
      }
      if ( getSessionId(obj.siteKey, obj.tabId, 'coBrowseConnectToken') ) {
        obj.coBrowseConnectToken = getSessionId(obj.siteKey, obj.tabId, 'coBrowseConnectToken'); // LiveAssist用接続トークンを企業側へ
      }

      emit.toCompany('syncNewInfo', obj, obj.siteKey);
    }
    else {
      emit.toMine('syncStop', data, socket);
    }
  });

// FIX ME !!!!!!!
  // 継続接続(両用)
  socket.on('connectContinue', function (data) {
    var obj = JSON.parse(data);

    // 企業キーが取得できなければスルー
    if ( obj.siteKey ) {

      // 外部接続フレームの場合
      if ( getSessionId(obj.siteKey, obj.tabId, 'parentTabId') ) {
        socket.join(obj.siteKey + emit.roomKey.frame);
      }
      else {
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
  socket.on('requestSyncStart', function (data) {
    var obj = JSON.parse(data), tabId;
    // 企業キーが取得できなければスルー
    if ( obj.siteKey ) {
      // 外部接続フレームの場合
      if ( getSessionId(obj.siteKey, obj.tabId, 'parentTabId') ) {
        socket.join(obj.siteKey + emit.roomKey.frame);
      }
      else {
        socket.join(obj.siteKey + emit.roomKey.client);
      }

      data.siteKey = obj.siteKey;
    }
    if ( obj.accessType !== 1 ) {
      tabId = obj.to; // host
      if ( (obj.to in sincloCore[obj.siteKey]) ) {
        sincloCore[obj.siteKey][obj.to].syncSessionId = socket.id;
      }
    }
    else {
      tabId = obj.tabId; // guest
    }

    emit.toUser('syncStart', data, getSessionId(obj.siteKey, tabId, 'sessionId'));
    emit.toUser('syncStart', data, getSessionId(obj.siteKey, tabId, 'syncSessionId'));
    emit.toUser('syncStart', data, getSessionId(obj.siteKey, tabId, 'syncFrameSessionId'));
  });

  // 初期同期依頼
  socket.on('getSyncInfo', function (data) {
    var obj = JSON.parse(data);
    emit.toUser('syncElement', data, getSessionId(obj.siteKey, obj.tabId, 'syncSessionId'));
  });

  // 初期同期処理完了
  socket.on('syncCompleate', function (data) {
    var obj = JSON.parse(data);
    emit.toUser('syncEvStart', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
    emit.toUser('syncEvStart', data, getSessionId(obj.siteKey, obj.to, 'syncSessionId'));
  });

  /**
   * サブミットを使った、画面同期停止
   * */
  socket.on('requestSyncStopForSubmit', function (data) {
    var obj = JSON.parse(data);
    if ( isset(obj.connectToken) ) {
      emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
      emit.toUser('syncStopForSubmit', obj, getSessionId(obj.siteKey, obj.tabId, 'shareWindowId'));
      // 企業一括
      emit.toCompany('syncStop', data, obj.siteKey); // リアルタイムモニタを更新する為
    }
  });

  /**
   * 企業フレーム:1, 消費者フレーム:2,企業インライン:3, 消費者インライン:4
   * */
  socket.on('requestSyncStop', function (data) {
    var obj = JSON.parse(data);
    if ( isset(obj.connectToken) ) {
      var parentId = false;
      obj.message = "切断を検知しました。";
      // 企業フレーム
      switch(Number(obj.type)){
        case 1: // 企業フレーム
        case 2: // 消費者フレーム
          if ( Number(obj.type) === 1 ) { // Was angry JS Lint
            parentId = obj.tabId.replace("_frame", "");
          }
          if ( 'parentId' in obj ) {
            parentId = obj.parentId;
          }

          if ( getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg') ) {
            // 企業フレーム
            emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg'));
            // 企業インライン
            // emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.tabId, 'syncSessionId'));
            // 消費者フレーム
            emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
            // 消費者インライン
            emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
            syncStopCtrl(obj.siteKey, obj.tabId, true);
          }
          else {
            syncStopCtrl(obj.siteKey, obj.tabId);
          }
          break;
        case 3: // 企業インライン
        case 4: // 消費者インライン
          // 相手先インラインフレーム
          emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.to, 'sessionId'));
          // 消費者インライン
          break;
      }

      // 消費者親タブ
      var compData = obj;
      if ( parentId ) {
        emit.toUser('syncStop', data, getSessionId(obj.siteKey, parentId, 'sessionId')); // 消費者の親フレーム
        syncStopCtrl(obj.siteKey, parentId);
        compData.tabId = parentId;
      }

      // 企業一括
      emit.toCompany('syncStop', compData, obj.siteKey); // リアルタイムモニタを更新する為
    }
    else {
      emit.toCompany('unsetUser', data, obj.siteKey);
    }
  });

  /* ウィンドウリサイズとマウス位置 */
  socket.on('syncBrowserInfoFrame', function (data) {
    var obj = JSON.parse(data);

    if ( isset(obj.windowSize) ) {
      emit.toUser('syncResponce', data, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
    }
    else {

      if ( getSessionId(obj.siteKey, obj.to, 'parentTabId') ) {
        // 外部接続の場合（企業 -> 消費者）
        emit.toUser('syncResponce', data, getSessionId(obj.siteKey, obj.to, 'shareWindowId'));
      }
      else {
        if ( 'to' in obj ) {
          emit.toUser('syncResponce', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
        }
        else {
          // 外部接続の場合（消費者 -> 企業）
          emit.toUser('syncResponce', data, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
        }
      }
    }
  });

  /* スクロール位置 */
  socket.on('syncScrollInfo', function (data) {
    var obj = JSON.parse(data);
    emit.toUser('syncResponce', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
  });

  socket.on('syncBrowserInfo', function (data) {
    var obj = JSON.parse(data);
    if ( isset(obj.windowSize) ) {
      emit.toUser('syncResponce', data, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
    }
    else {
      emit.toUser('syncResponce', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
    }
  });

  socket.on('syncChangeEv', function (data) {
    var obj = JSON.parse(data);
      emit.toUser('syncResponceEv', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
  });

  socket.on('syncReconnectConfirm', function (data) {
    var obj = JSON.parse(data), timer, i = 1;
    timer = setInterval(function(){
      var sessionId = getSessionId(obj.siteKey, obj.to, 'sessionId');
      if ( sessionId && connectList[sessionId] ) {
        emit.toUser('syncStart', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
        emit.toUser('syncStart', data, getSessionId(obj.siteKey, obj.to, 'syncSessionId'));
        clearInterval(timer);
        // sessionIdが消えてる可能性があるため、企業側フレームのsocket.idを再セット
        sincloCore[obj.siteKey][obj.to].syncFrameSessionId = socket.id;
      }
      if ( i === 5 ) {
        clearInterval(timer);
        emit.toUser('syncStop', {message: "接続できませんでした。"}, socket.id);
      }
      i++;
    }, 1000);
  });

  socket.on('reqSyncBrowserCtrl', function(data){
    var obj = JSON.parse(data);
    emit.toUser('syncBrowserCtrl', data, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
  });

  socket.on('sendOperatorStatus', function(data){
    var obj = JSON.parse(data),
        sendData = {
          siteKey: obj.siteKey,
          userId: obj.userId,
          active: obj.active
        };
    if ( !isset(activeOperator[obj.siteKey]) ) {
      activeOperator[obj.siteKey] = {};
    }
    // 在席中
    if ( obj.active ) {
      if ( !isset(activeOperator[obj.siteKey][obj.userId]) ) {
        activeOperator[obj.siteKey][obj.userId] = socket.id;
        console.log("【" + obj.siteKey + "】オペレータ 離席 => 待機 userId : " + obj.userId);
        if ( obj.hasOwnProperty('scNum') ) {
          if ( !scList.hasOwnProperty(obj.siteKey) ) { scList[obj.siteKey] = { user: {}, cnt: {} }; }
          scList[obj.siteKey].user[obj.userId] = obj.scNum;
          scList[obj.siteKey].cnt[obj.userId] = chatApi.calcScNum(obj, obj.userId);
          console.log("【" + obj.siteKey + "】対応上限数設定 " + scList[obj.siteKey].cnt[obj.userId] + " / " + scList[obj.siteKey].user[obj.userId] +  " userId : " + obj.userId);
        }
      }
    }
    // 退席中
    else {
      if ( isset(activeOperator[obj.siteKey][obj.userId]) ) {
        delete activeOperator[obj.siteKey][obj.userId];
        console.log("【" + obj.siteKey + "】オペレータ 待機 => 離席 userId : " + obj.userId);
      }
      if ( scList.hasOwnProperty(obj.siteKey) && scList[obj.siteKey].cnt.hasOwnProperty(obj.userId) ) {
        delete scList[obj.siteKey].cnt[obj.userId];
        delete scList[obj.siteKey].user[obj.userId];
      }
    }

    if ( scList.hasOwnProperty(obj.siteKey) ) {
      sendData.scInfo = scList[obj.siteKey].cnt;
    }
    var keys = Object.keys(activeOperator[obj.siteKey]);
    sendData.count = keys.length;
    emit.toCompany('activeOpCnt', sendData, obj.siteKey);
  });

  socket.on('sendOtherTabURL', function (d){
    var obj = JSON.parse(d), tabId;

    // インラインフレームに送る
    emit.toUser('receiveOtherTabURL', obj, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
  });

  socket.on('reqUrlChecker', function (d){
    var obj = JSON.parse(d), shareWindowId, toTabId;
    emit.toUser('resUrlChecker', d, getSessionId(obj.siteKey, obj.to, 'sessionId'));
    // 外部接続中であれば、ページ遷移履歴として遷移先をフレームに送る(再接続の場合を除く)
    shareWindowId = getSessionId(obj.siteKey, obj.to, 'shareWindowId');
    if (shareWindowId && !obj.reconnectFlg) {
      emit.toUser('resUrlChecker', d, shareWindowId);
    }
    // 企業側フレームへも送る(再接続の場合を除く)
    toTabId = getSessionId(obj.siteKey, obj.tabId, 'toTabId') || obj.tabId;
    if (toTabId === obj.tabId  && !obj.reconnectFlg) {
      emit.toUser('resUrlChecker', d, getSessionId(obj.siteKey, toTabId, 'syncFrameSessionId'));
    }
  });

  socket.on('syncLocationOfFrame', function (d){
    var obj = JSON.parse(d), shareWindowId, companyFrameId;
    // 外部接続中であれば、ページ遷移履歴として遷移先をフレームに送る
    shareWindowId = getSessionId(obj.siteKey, obj.tabId, 'shareWindowId');
    if (shareWindowId ) {
      emit.toUser('syncLocationOfFrame', d, shareWindowId);
    }
    // 企業側フレームへも送る
    companyFrameId = getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId');
    if ( companyFrameId ) {
      emit.toUser('syncLocationOfFrame', d, companyFrameId);
    }
  });

  // -----------------------------------------------------------------------
  //  チャット関連
  // -----------------------------------------------------------------------

  // 一括：チャットデータ取得
  socket.on("getChatMessage", function(d){
    var obj = JSON.parse(d);
    chatApi.get(obj);
  });

  // 都度：チャットデータ取得(オートメッセージのみ)
  socket.on("sendAutoChatMessage", function(d){
    var obj = JSON.parse(d);
    var chat = JSON.parse(JSON.stringify(obj));
    chat.messageType = obj.isAutoSpeech ? chatApi.cnst.observeType.autoSpeech : chatApi.cnst.observeType.auto;
    chat.created = new Date();
    chat.sort = fullDateTime(chat.created);

    var sincloSession = sincloCore[chat.siteKey][chat.sincloSessionId];
    if(isset(sincloSession) && isset(sincloSession.autoMessages)) {
      sincloCore[chat.siteKey][chat.sincloSessionId].autoMessages[chat.chatId] = chat;
    } else {
      console.log("sendAutoChatMessage::sincloSession : " + chat.sincloSessionId + "is null.");
      return false;
    }
    if(!functionManager.isEnabled(chat.siteKey, functionManager.keyList.monitorPollingMode)) {
      emit.toCompany('resAutoChatMessage', chat, chat.siteKey);
    }
    emit.toSameUser('resAutoChatMessage', chat, chat.siteKey, chat.sincloSessionId);
  });

  // 一括：チャットデータ取得(オートメッセージのみ)
  socket.on("getAutoChatMessages", function(d){
    var obj = JSON.parse(d);
    if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) return false;
    var sId = getSessionId(obj.siteKey, obj.tabId, 'sessionId');
    obj.messageType = chatApi.cnst.observeType.auto;
    obj.sendTo = socket.id;
    emit.toUser('sendReqAutoChatMessages', obj, sId);

    // ユーザーがチャット中の場合
    if ( getSessionId(obj.siteKey, obj.tabId, 'chatSessionId') ) {
      var sessionId = getSessionId(obj.siteKey, obj.tabId, 'chatSessionId');
      emit.toUser('reqTypingMessage', {siteKey: obj.siteKey, from: obj.mUserId, tabId: obj.tabId }, sessionId);
    }
  });

  // 一括：チャットデータ取得(オートメッセージのみ)
  socket.on("sendAutoChatMessages", function(d){
    var obj = JSON.parse(d);

    var setList = {};
    for (var i = 0; i < obj.messages.length; i++) {
      if(!isset(obj.messages[i]) || !isset(obj.messages[i].created)) continue;
      var created = new Date(obj.messages[i].created);
      obj.messages[i].messageType = chatApi.cnst.observeType.auto;
      setList[fullDateTime(Date.parse(created))] = obj.messages[i];
    }
    if(isset(obj.scenarios)) {
      for (var i = 0; i < obj.scenarios.length; i++) {
        if (!isset(obj.scenarios[i]) || !isset(obj.scenarios[i].created)) continue;
        var created = new Date(obj.scenarios[i].created);
        setList[fullDateTime(Date.parse(created))] = obj.scenarios[i];
      }
    }
    var ret = {};
        ret.messages = objectSort(setList);
        ret.chatToken = obj.chatToken;
        ret.tabId = obj.tabId;
        ret.historyId = getSessionId(obj.siteKey, obj.tabId, 'historyId');
    emit.toUser('resAutoChatMessages', ret, obj.sendTo);
  });

  // チャット開始
  socket.on("chatStart", function(d){
    var obj = JSON.parse(d), date = new Date(), now = fullDateTime(date), type = chatApi.cnst.observeType.start;
var logToken = makeToken();
console.log("chatStart-0: [" + logToken + "] >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>");
console.log("chatStart-1: [" + logToken + "] " + d);
    if ( sincloCore[obj.siteKey][obj.tabId] === null ) {
      emit.toMine("chatStartResult", {ret: false, siteKey: obj.siteKey, userId: sincloCore[obj.siteKey][obj.tabId].chat, sincloSessionId: obj.sincloSessionId}, socket);

      var userId = ( getSessionId(obj.siteKey,obj.tabId,'chat') ) ? sincloCore[obj.siteKey][obj.tabId].chat : "undefined userId.";
console.log("chatStart-2: [" + logToken + "] " + JSON.stringify({ret: false, siteKey: obj.siteKey, userId: userId}));
    }
    else {
      var sendData = {ret: true, messageType: type, tabId: obj.tabId, siteKey: obj.siteKey, userId: obj.userId, hide:false, created: now};
      var scInfo = "";

      sincloCore[obj.siteKey][obj.tabId].chat = obj.userId;
      if(!isset(sincloCore[obj.siteKey][obj.sincloSessionId].chat)) {
        sincloCore[obj.siteKey][obj.sincloSessionId].chat = {};
      }
      sincloCore[obj.siteKey][obj.sincloSessionId].chat[obj.tabId] = obj.userId;
      sincloCore[obj.siteKey][obj.tabId].chatSessionId = socket.id;
      // サイトとして初チャット開始
      if ( !(obj.siteKey in c_connectList) ) {
        c_connectList[obj.siteKey] = {};
      }
      // タブに対して初チャット開始
      if ( !(obj.tabId in c_connectList[obj.siteKey]) ) {
        c_connectList[obj.siteKey][obj.tabId] = {};
      }
      // タブに対して複数回目のチャット開始
      else {
        var keys = Object.keys(c_connectList[obj.siteKey][obj.tabId]);
        // 横取り（最後のc_connectListが"start"でない）
        if ( c_connectList[obj.siteKey][obj.tabId][keys[keys.length - 1]].type === "start" ) {

          /* チャット対応上限の処理（対応人数減算の処理） */
          if ( scList.hasOwnProperty(obj.siteKey) ) {
            var userId = c_connectList[obj.siteKey][obj.tabId][keys[keys.length - 1]].userId;
            if ( scList[obj.siteKey].cnt.hasOwnProperty(userId) ) {
              scList[obj.siteKey].cnt[userId]--; // 対応人数を減算する
              console.log("【" + obj.siteKey + "】chatStart::対応上限数設定 " + scList[obj.siteKey].cnt[userId] + " / " + scList[obj.siteKey].user[userId] +  " userId : " + userId);
            }
          }
          /* チャット対応上限の処理（対応人数減算の処理） */

          sendData.hide = true;
        }
      }

      pool.query('SELECT mu.id, mu.display_name, wid.style_settings FROM m_users as mu LEFT JOIN m_widget_settings AS wid ON ( wid.m_companies_id = mu.m_companies_id ) WHERE mu.id = ? AND mu.del_flg != 1 AND wid.del_flg != 1 AND wid.m_companies_id = ?', [obj.userId, companyList[obj.siteKey]], function(err, rows){
        if ( err !== null && err !== '' ) return false; // DB接続断対応

        sendData.userName = "オペレーター";

        var logData3 = ( typeof(rows) === 'object' ) ? JSON.stringify(rows) : "typeof: " + typeof(rows);
console.log("chatStart-3: [" + logToken + "] " + logData3);
        if ( rows && rows[0] ) {
          var settings = JSON.parse(rows[0].style_settings);
          // 表示名をウィジェットで表示する場合
          var userName = rows[0].display_name;
          sendData.userName = userName;

          c_connectList[obj.siteKey][obj.tabId][now] = {messageType: type, type:"start", userName: userName, userId: obj.userId};
          //emit.toUser("chatStartResult", sendData, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
          emit.toSameUser("chatStartResult", sendData, obj.siteKey, obj.sincloSessionId);

          /* チャット対応上限の処理（対応人数加算の処理） */
          if ( scList.hasOwnProperty(obj.siteKey) && scList[obj.siteKey].cnt.hasOwnProperty(obj.userId) ) {
            scList[obj.siteKey].cnt[obj.userId]++; // 対応人数を加算する
            scInfo = scList[obj.siteKey].cnt;
            console.log("【" + obj.siteKey + "】chatStart::対応上限数設定 " + scList[obj.siteKey].cnt[obj.userId] + " / " + scList[obj.siteKey].user[obj.userId] +  " userId : " + obj.userId);
          }
          /* チャット対応上限の処理（対応人数加算の処理） */

          // DBに書き込み
          var visitorId = connectList[sincloCore[obj.siteKey][obj.tabId].sessionId].userId ? connectList[sincloCore[obj.siteKey][obj.tabId].sessionId].userId : "";

          //応対数検索、登録
          getConversationCountUser(visitorId,function(results) {
            if(results !== null){
              //カウント数が取れなかったとき
              if (Object.keys(results) && Object.keys(results).length === 0) {
                obj.messageDistinction = 1;
                //visitors_id,カウント数一件を登録
                pool.query('INSERT INTO t_conversation_count(visitors_id,conversation_count) VALUES(?,?)',[visitorId, 1],function(err,result) {
                  if(isset(err)){
                    console.log("RECORD INSERT ERROR: t_convertsation_count(visitors_id,conversation_count):" + err);
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
                chatMessage: "入室",
                messageType: 98,
                messageDistinction: obj.messageDistinction,
                userName: userName,
                created: date
              };

              var logData4 = ( typeof(insertData) === 'object' ) ? JSON.stringify(insertData) : "typeof: " + typeof(insertData) ;
console.log("chatStart-4: [" + logToken + "] " + logData4);
            chatApi.notifyCommit("chatStartResult", insertData);
            }
          });
        }
        var logData5 = ( sincloCore.hasOwnProperty(obj.siteKey) && typeof(sincloCore[obj.siteKey]) === 'object' ) ? JSON.stringify(sincloCore[obj.siteKey]) : "typeof: " + typeof(sincloCore[obj.siteKey]) ;
console.log("chatStart-5: [" + logToken + "] " + JSON.stringify(sincloCore[obj.siteKey]));
console.log("chatStart-6: [" + logToken + "] <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<");
      });
    }
  });

  // チャット終了
  socket.on("chatEnd", function(d){
    var obj = JSON.parse(d), date = new Date(), now = fullDateTime(), type = chatApi.cnst.observeType.end;

    var keys = [];
    var userName = "";
    if(isset(c_connectList[obj.siteKey][obj.tabId])) {
      keys = Object.keys(c_connectList[obj.siteKey][obj.tabId]);
      userName = c_connectList[obj.siteKey][obj.tabId][keys[keys.length-1]].user;
      c_connectList[obj.siteKey][obj.tabId][now] = {type:"end", userName: userName, userId: obj.userId, messageType: type};
    }

    /* チャット対応上限の処理（対応人数減算の処理） */
    if ( scList.hasOwnProperty(obj.siteKey) && scList[obj.siteKey].cnt.hasOwnProperty(obj.userId) ) {
      if(scList[obj.siteKey].cnt[obj.userId] > 0) {
        scList[obj.siteKey].cnt[obj.userId]--; // 対応人数を減算する

      } else {
        scList[obj.siteKey].cnt[obj.userId] = 0;
      }
      console.log("【" + obj.siteKey + "】chatEnd::対応上限数設定 " + scList[obj.siteKey].cnt[obj.userId] + " / " + scList[obj.siteKey].user[obj.userId] +  " userId : " + obj.userId);
    }
    /* チャット対応上限の処理（対応人数減算の処理） */
    scInfo = ( scList.hasOwnProperty(obj.siteKey) ) ? scList[obj.siteKey].cnt : {};

    if ( isset(sincloCore[obj.siteKey][obj.tabId] )
      && isset(sincloCore[obj.siteKey][obj.tabId].chat)
      && isset(connectList[sincloCore[obj.siteKey][obj.tabId].sessionId])
      && isset(connectList[sincloCore[obj.siteKey][obj.tabId].sessionId].userId)) {
      sincloCore[obj.siteKey][obj.tabId].chat = null;
      sincloCore[obj.siteKey][obj.tabId].chatSessionId = null;
      if( isset(sincloCore[obj.siteKey]) && isset(sincloCore[obj.siteKey][obj.sincloSessionId]) && isset(sincloCore[obj.siteKey][obj.sincloSessionId].chat) ) {
        delete sincloCore[obj.siteKey][obj.sincloSessionId].chat[obj.tabId];
        if(Object.keys(sincloCore[obj.siteKey][obj.sincloSessionId].chat).length === 0) {
          delete sincloCore[obj.siteKey][obj.sincloSessionId].chat;
        }
      }

      //emit.toUser("chatEndResult", {ret: true, messageType: type}, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
      emit.toSameUser("chatEndResult", {ret: true, messageType: type}, obj.siteKey, obj.sincloSessionId);
      // DBに書き込み
      var visitorId = connectList[sincloCore[obj.siteKey][obj.tabId].sessionId].userId ? connectList[sincloCore[obj.siteKey][obj.tabId].sessionId].userId : "";

      //応対数検索、登録
      getConversationCountUser(visitorId, function(results) {
        if(results !== null){
          //カウントが取れたとき
          if ( Object.keys(results) && Object.keys(results).length !== 0) {
            obj.messageDistinction = results[0].conversation_count;
            //カウント数一件追加
            pool.query('UPDATE t_conversation_count SET conversation_count = ? WHERE visitors_id = ?',[results[0].conversation_count + 1, visitorId],function(err,result) {
              if(isset(err)){
                console.log("RECORD UPDATE ERROR: t_conversation_count(conversation_count):" + err);
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
            chatMessage: "退室",
            messageType: type,
            messageDistinction: obj.messageDistinction,
            userName: userName,
            created: date
          };
          chatApi.notifyCommit("chatEndResult", insertData);
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
        visitorsId: "",
        userId: obj.userId,
        chatMessage: "退室",
        messageType: type,
        messageDistinction: obj.messageDistinction,
        userName: userName,
        created: now
      };
      emit.toCompany("chatEndResult", notifyData, obj.siteKey);
    }
  });

  //新着チャット
  socket.on("sendChat", function(d,ack){
    var obj = JSON.parse(d);
    //応対件数検索、登録
    getConversationCountUser(obj.userId,function(results) {
      if(results !== null) {
        //カウント数が取れなかった場合
        if (Object.keys(results) && Object.keys(results).length == 0) {
          obj.messageDistinction = 1;
        }
        //カウント数が取れた場合
        else {
          obj.messageDistinction = results[0].conversation_count;
        }
        //リクエストメッセージの場合
        if(obj.messageRequestFlg == 1){
          //消費者が初回メッセージを送る前にオペレータが入室した場合
          pool.query('SELECT id FROM t_history_chat_logs WHERE visitors_id = ? and t_histories_id = ? and message_distinction = ? and message_type = 98',[obj.userId,obj.historyId,obj.messageDistinction], function (err, result) {
            if(Object.keys(results) && Object.keys(result).length !== 0) {
              obj.messageRequestFlg = 0;
            }
            chatApi.set(obj);
          });
        }
        else {
          chatApi.set(obj);
        }
      }
    });
    if(ack) ack();
  });

  //新着チャット
  socket.on("sendFile", function(d, ack){
    var obj = JSON.parse(d);
    //応対件数検索、登録
    getConversationCountUser(obj.userId,function(results) {
      if(results !== null) {
        //カウント数が取れなかった場合
        if (Object.keys(results) && Object.keys(results).length == 0) {
          obj.messageDistinction = 1;
        }
        //カウント数が取れた場合
        else {
          obj.messageDistinction = results[0].conversation_count;
        }
        //リクエストメッセージの場合
        if(obj.messageRequestFlg == 1){
          //消費者が初回メッセージを送る前にオペレータが入室した場合
          pool.query('SELECT id FROM t_history_chat_logs WHERE visitors_id = ? and t_histories_id = ? and message_distinction = ? and message_type = 98',[obj.userId,obj.historyId,obj.messageDistinction], function (err, result) {
            if(Object.keys(results) && Object.keys(result).length !== 0) {
              obj.messageRequestFlg = 0;
            }
            chatApi.set(obj);
          });
        }
        else {
          chatApi.set(obj);
        }
      }
    });
    if(ack) ack();
  });

  // オートチャット
  socket.on("sendAutoChat", function(d){
    var obj = JSON.parse(d);
    //応対数検索、登録
    getConversationCountUser(obj.userId,function(results) {
      var messageDistinction;
      if(results !== null){
        //カウント数が取れなかったとき
        if (Object.keys(results) && Object.keys(results).length === 0) {
          messageDistinction = 1;
        }
        //カウント数が取れたとき
        else {
          messageDistinction = results[0].conversation_count;
        }
        var loop = function(err, rows){
          if ( !err && (rows && rows[0]) ) {
              var activity = JSON.parse(rows[0].activity);
              if(activity.cv == 1) {
                var ret = {
                  siteKey: obj.siteKey,
                  tabId: obj.tabId,
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
              }
              else {
                var ret = {
                    siteKey: obj.siteKey,
                    tabId: obj.tabId,
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
          pool.query("SELECT *, ? as inputed, ? as auto_message_type FROM t_auto_messages WHERE id = ?  AND m_companies_id = ? AND del_flg = 0 AND active_flg = 0 AND action_type = 1", [message.created, message.isAutoSpeech ? chatApi.cnst.observeType.autoSpeech : chatApi.cnst.observeType.auto, message.chatId, companyList[obj.siteKey]], loop);
          var sincloSession = sincloCore[obj.siteKey][obj.sincloSessionId];
          if(message.chatId in sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages) {
            sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages[message.chatId]['applied'] = true;
          } else if(isset(sincloSession) && isset(sincloSession.autoMessages)) {
            message.created = new Date();
            message.sort = fullDateTime(message.created);
            message.applied = true;
            message.messageType = chatApi.cnst.observeType.auto;
            sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages[message.chatId] = message;
          }
        }
      }
    });
  });

  // 初回通知メッセージ
  socket.on("sendInitialNotificationChat", function(d){
    var obj = JSON.parse(d);
    var ret = {
      siteKey: obj.messageList.siteKey,
      tabId: obj.messageList.tabId,
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
  socket.on("isReadChatMessage", function(d){
    var obj = JSON.parse(d);
    // TODO 履歴IDチェック
    if ( isset(sincloCore[obj.siteKey][obj.tabId].historyId) ) {
      obj.historyId = sincloCore[obj.siteKey][obj.tabId].historyId;
      pool.query("UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND message_type = 1 AND id <= ?;",
        [obj.historyId, obj.chatId], function(err, ret, fields){
          if(isset(sincloCore[obj.siteKey]) && isset(sincloCore[obj.siteKey][obj.tabId])) {
            sincloCore[obj.siteKey][obj.tabId].chatUnreadCnt = 0;
          }
          chatApi.sendUnreadCnt('retReadChatMessage', obj, true);
          Object.keys(sincloCore[obj.siteKey]).forEach(function(key) {
            if (sincloCore[obj.siteKey][key].sincloSessionId === obj.sincloSessionId) {
              sincloCore[obj.siteKey][key].chatUnreadCnt = 0;
            }
          });
        }
      );
    }
  });

  // 既読操作（消費者）
  socket.on("isReadFromCustomer", function(d){
    var obj = JSON.parse(d);
    if ( getSessionId(obj.siteKey, obj.tabId, 'historyId') ) {
      obj.historyId = sincloCore[obj.siteKey][obj.tabId].historyId;
      pool.query("UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND message_type != 1;",
        [obj.historyId], function(err, ret, fields){
          emit.toSameUser('retReadFromCustomer', {
            tabId: obj.tabId,
            sincloSessionId: obj.sincloSessionId
          }, obj.siteKey, obj.sincloSessionId);
        }
      );
    }
  });

  // 入力ステータスを送信
  socket.on("sendTypeCond", function(d){
    var obj = JSON.parse(d);
    // 存在チェック
    if ( !((obj.siteKey in sincloCore) && (obj.tabId in sincloCore[obj.siteKey])) ) {
      return false;
    }

    // 送り主が企業の場合
    if ( obj.type === chatApi.cnst.observeType.company ) {
      sincloCore[obj.siteKey][obj.tabId].chatSessionId = socket.id; // 入力中ユーザーのsocketIdをセットする

      // 企業へ送る
      emit.toCompany('receiveTypeCond', d, obj.siteKey);
      // 消費者へ送る
      delete obj.message;
      if ( ('sendToCustomer' in obj) && String(obj.sendToCustomer) === "false" ) return false;
      emit.toSameUser('receiveTypeCond', obj, obj.siteKey, getSessionId(obj.siteKey, obj.tabId, 'sincloSessionId'));
    }
    // 送り主が消費者の場合
    else {
      // 企業へ送る
      emit.toCompany('receiveTypeCond', d, obj.siteKey);
    }
  });

  socket.on("retTypingMessage", function(d){
    var obj = JSON.parse(d);
    // 送り先がセットされている
    if ( ('to' in obj) && (obj.siteKey in company.info) && (obj.to in company.info[obj.siteKey]) ) {
      var toKeys = Object.keys(company.info[obj.siteKey][obj.to]).length;
      if ( toKeys > 0 ) {
        for ( var sessionId in company.info[obj.siteKey][obj.to] ) {
          emit.toUser('resTypingMessage', d, sessionId);
        }
      }
    }
  });

  // ============================================
  //  シナリオイベントハンドラ
  // ============================================
  socket.on('getScenario', function(data, ack){
    var obj = JSON.parse(data);
    var result = {};
    pool.query('select activity from t_chatbot_scenarios where m_companies_id = ? and id = ?;', [companyList[obj.siteKey], obj.scenarioId],
      function(err, row){
        if ( err !== null && err !== '' ) {
          if(ack) {
            ack(result);
          } else {
            emit.toMine('resGetSenario', result, socket);
          }
          return;
        }
        if(row.length !== 0) {
          result = JSON.parse(row[0].activity);
        }
        if(ack) {
          ack({id: obj.scenarioId, activity: result});
        } else {
          emit.toMine('resGetScenario', {id: obj.scenarioId, activity: result}, socket);
        }
      });
  });

  socket.on('processSendMail', function(data, ack){
    var obj = JSON.parse(data);
    sendSenarioMail(obj, function(result){
      ack(result);
    });
  });

  socket.on('storeScenarioMessage', function(data, ack){
    var obj = JSON.parse(data);
    //応対数検索、登録
    getConversationCountUser(obj.userId,function(results) {
      var messageDistinction;
      if(results !== null){
        //カウント数が取れなかったとき
        if (Object.keys(results) && Object.keys(results).length === 0) {
          messageDistinction = 1;
        }
        //カウント数が取れたとき
        else {
          messageDistinction = results[0].conversation_count;
        }
        obj.messages.forEach(function(elm, index, arr){
          if(!isset(elm.created)) {
            elm.created = new Date();
            elm.sort = fullDateTime(elm.created);
          }
          elm.applied = true;
          var sincloSession = sincloCore[obj.siteKey][obj.sincloSessionId];
          if(isset(sincloSession) && isset(sincloSession.scenario)) {
            if(!isset(sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId])) {
              sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId] = {};
            }
            if(!isset(sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId][elm.sequenceNum])) {
              sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId][elm.sequenceNum] = {};
            }
            sincloCore[obj.siteKey][obj.sincloSessionId].scenario[elm.scenarioId][elm.sequenceNum][elm.categoryNum] = elm;
          }
          var ret = {
            siteKey: obj.siteKey,
            tabId: obj.tabId,
            userId: obj.userId,
            mUserId: null,
            chatMessage: elm.message,
            messageType: getMessageTypeBySenarioActionType(elm.type),
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

  socket.on("addLastMessageToCV", function(d){
    var obj = JSON.parse(d);
    pool.query('select * from t_history_chat_logs where m_companies_id = ? and t_histories_id = ? and ((message_type = 12) OR (message_type = 22)) order by created desc limit 0,1;', [companyList[obj.siteKey], obj.historyId],
      function(err, row){
        if ( err !== null && err !== '' ) {
          console.log("UPDATE lastMessage to cv is failed. historyId : " + obj.historyId);
          return;
        }
        if(row.length !== 0) {
          pool.query('update t_history_chat_logs set achievement_flg=0 where id = ?', [row[0].id], function(){

          });
        }
      });
  });

  // 都度：チャットデータ取得(オートメッセージのみ)
  socket.on("sendScenarioMessage", function(d, ack){
    var obj = JSON.parse(d);
    var scenario = JSON.parse(JSON.stringify(obj));
    scenario.created = formatDateParse();
    scenario.sort = fullDateTime(new Date(scenario.created));

    var sincloSession = sincloCore[scenario.siteKey][scenario.sincloSessionId];
    if(isset(sincloSession) && isset(sincloSession.scenario)) {
      if(!isset(sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId])) {
        sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId] = {};
      }
      if(!isset(sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId][scenario.sequenceNum])) {
        sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId][scenario.sequenceNum] = {};
      }
      sincloCore[scenario.siteKey][scenario.sincloSessionId].scenario[scenario.scenarioId][scenario.sequenceNum][scenario.categoryNum] = scenario;
    } else {
      console.log("sendScenarioMessage::sincloSession : " + scenario.sincloSessionId + "is null.");
      return false;
    }
    if(!functionManager.isEnabled(scenario.siteKey, functionManager.keyList.monitorPollingMode)) {
      emit.toCompany('resScenarioMessage', scenario, scenario.siteKey);
    }
    emit.toSameUser('resScenarioMessage', scenario, scenario.siteKey, scenario.sincloSessionId);
    ack({data: scenario});
  });

  socket.on("callExternalApi", function (data,ack){
    var obj = JSON.parse(data);
    pool.query('select * from t_external_api_connections where m_companies_id = ? and id = ? limit 0,1;', [companyList[obj.siteKey], obj.externalApiConnectionId],
      function(err, row){
        if ( err !== null && err !== '' ) {
          console.log("callExternalApi select is failed. externalApiConnectionId : " + obj.externalApiConnectionId);
          return;
        }
        if(row.length !== 0) {
          var reqSettings = row[0];

          var url = replaceVariable(obj.variables, reqSettings.url);
          var reqBody = replaceVariable(data.variables, reqSettings.request_body);

          var headers = {
            'Content-Type': 'application/x-www-form-urlencoded'
          };

          //オプションを定義
          var options = {
            url: 'http://' + process.env.GET_CD_API_HOST + ':' + process.env.GET_CD_API_PORT + '/Notification/callExternalApi',
            headers: headers,
            json: true,
            form: {
              apiParams: JSON.stringify({
                url: url,
                requestHeaders: JSON.parse(reqSettings.request_headers),
                methodType: reqSettings.method_type,
                requestBody: reqSettings.request_body,
                responseBodyMaps: JSON.parse(reqSettings.response_body_maps),
              })
            }
          };

          if(process.env.DB_HOST === 'localhost') {
            options.rejectUnauthorized = false;
          }

          //リクエスト送信
          request.post(options, function (error, response, body) {
            if (error) {
              console.log('外部API実行時にHTTPレベルのエラーが発生しました。 message : ' + error.message);
              ack({
                success: false
              });
              return;
            }
            if(response.statusCode === 200) {
              response.setEncoding('utf8');
              if(body.success) {
                ack(body.result);
              } else {
                ack({});
              }
              return;
            } else {
              console.log('外部API実行時にエラーが返却されました。 errorCode : ' + response.statusCode);
              ack({
                success: false
              });
              return;
            }
          });
        }
      });
  });

  socket.on('getScenarioDownloadInfo', function(data, ack){
    var obj = JSON.parse(data);
    pool.query('select * from t_chatbot_scenario_send_files where m_companies_id = ? and id = ? limit 0,1;', [companyList[obj.siteKey], obj.sendFileId],
    function(err, row) {
      if (err !== null && err !== '') {
        console.log("getScenarioDownloadInfo select is failed. sendFileId : " + obj.sendFileId);
        return;
      }

      if (row.length !== 0) {
        var targetFileInfo = row[0];
        var deleted = Boolean(Number(targetFileInfo.del_flg));
        var sendData = {
          success: true,
        };
        var today = new Date();
        if(!deleted){
          sendData = {
            success: true,
            downloadUrl: targetFileInfo.download_url,
            fileName: targetFileInfo.file_name,
            fileSize: targetFileInfo.file_size,
            expired: formatDateParse(today.setDate(today.getDate() + 1))
          };
        } else {
          sendData = {
            success: true,
            url: "",
            name: "",
            size: "",
            deleted: true
          };
        }
        ack(sendData);
      }
    });
  });

  var replaceVariable = function(variables, message) {
    return message.replace(/{{(.+?)\}}/g, function(param) {
      var name = param.replace(/^{{(.+)}}$/, '$1');
      return variables[name] || name;
    });
  }

  // ============================================
  //  画面キャプチャ共有イベントハンドラ
  // ============================================

  /**
   * オペレータから画面キャプチャ共有のリクエストを送信する
   */
  socket.on('requestCoBrowseOpen', function (data) {
    console.log("requestCoBrowseOpen >>> " + data);
    var obj = JSON.parse(data);
    if ( !getSessionId(obj.siteKey, obj.tabId, 'sessionId') ) return false;
    if(laSessionCounter.isLimit(obj.siteKey)) {
      emit.toMine('coBrowseSessionLimit', data, socket);
      return;
    }
    sincloCore[obj.siteKey][obj.tabId].shareCoBrowseFlg = true;
    sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
    emit.toUser('startCoBrowseOpen', data, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
    emit.toMine('requestCoBrowseAllowed', data, socket);
    // 今まで通り
    // else {
    //     // 同形ウィンドウを作成するための情報取得依頼
    //     if ( !getSessionId(obj.siteKey, obj.tabId, 'sessionId') ) return false;
    //     sincloCore[obj.siteKey][obj.tabId].shareWindowFlg = false;
    //     sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
    //     sincloCore[obj.siteKey][obj.tabId].syncSessionId = null;
    //     sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
    //     emit.toUser('getWindowInfo', data, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
    // }
  });

  /**
   * サイト訪問者側で画面キャプチャ共有のリクエストを許可したときに送信する
   */
  socket.on('beginToCoBrowse', function (data) {
    console.log("beginToCoBrowse >>> " + data);
    var obj = JSON.parse(data);
    emit.toCompany('beginToCoBrowse', data, obj.siteKey);
  });

  /**
   * オペレータ側の準備が完了し、サイト訪問者側もLiveAssistとのセッションを確立した後に送信する
   */
  socket.on('readyToCoBrowse', function (data) {
    console.log("readyToCoBrowse >>> " + data);
    var obj = JSON.parse(data);
    sincloCore[obj.siteKey][obj.tabId].laShortCode = obj.shortcode;
    sincloCore[obj.siteKey][obj.tabId].coBrowseConnectToken = obj.coBrowseConnectToken;
    emit.toUser('readyToCoBrowse', data, getSessionId(obj.siteKey, obj.tabId, 'coBrowseParentSessionId'));
    emit.toCompany('syncNewInfo', data, obj.siteKey);
    laSessionCounter.countUp(obj.siteKey, obj.tabId);
  });

  /**
   * オペレータ側の準備が完了し、サイト訪問者側がLiveAssistとのセッションの確立に失敗した後に送信する
   */
  socket.on('coBrowseFailed', function (data) {
    console.log("coBrowseFailed >>> " + data);
    var obj = JSON.parse(data);
    emit.toUser('coBrowseFailed', data, getSessionId(obj.siteKey, obj.tabId, 'coBrowseParentSessionId'));
  });

  /**
   * オペレータの画面キャプチャ共有時の子ウィンドウがオープンし、LiveAssistとのセッションを確立したときに送信する
   */
  socket.on('assistAgentIsReady', function (data) {
    console.log("assistAgentIsReady >>> " + data);
    var obj = JSON.parse(data);
    if (obj.to in sincloCore[obj.siteKey])  {
      console.log("OBJ : " + JSON.stringify(data));
      sincloCore[obj.siteKey][obj.to]['responderId'] = obj.responderId; // 対応ユーザーID
      sincloCore[obj.siteKey][obj.to]['coBrowseParentSessionId'] = socket.id; // 企業側のsocket.id
      emit.toUser('assistAgentIsReady', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
    } else {
      console.log("assistAgentIsReady >>> Tab ID : " + obj.to + " is NOT found.");
      //FIXME  画面共有セッションは切れていない可能性もあるのでハンドリング方法を考える
    }
  });

  socket.on('coBrowseReconnectConfirm', function (data) {
    var obj = JSON.parse(data), timer, i = 1;
    timer = setInterval(function(){
      var sessionId = getSessionId(obj.siteKey, obj.to, 'sessionId');
      if ( sessionId && connectList[sessionId] ) {
        emit.toUser('assistAgentIsReady', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
        clearInterval(timer);
        // sessionIdが消えてる可能性があるため、企業側フレームのsocket.idを再セット
        sincloCore[obj.siteKey][obj.to].coBrowseParentSessionId = socket.id;
      }
      if ( i === 5 ) {
        clearInterval(timer);
        emit.toUser('stopCoBrowse', {message: "接続できませんでした。"}, socket.id);
      }
      i++;
    }, 1000);
  });

  /**
   *
   * 企業フレーム:1, 消費者フレーム:2,企業インライン:3, 消費者インライン:4
   */
  socket.on('requestStopCoBrowse', function (data) {
    console.log("requestStopCoBrowse >>> " + data);
    var obj = JSON.parse(data);
    if ( isset(obj.coBrowseConnectToken) ) {
      var parentId = false;
      obj.message = "切断を検知しました。";
      // 企業フレーム
      switch(Number(obj.type)){
        case 1: // 企業フレーム
        case 2: // 消費者フレーム
          if ( Number(obj.type) === 1 ) { // Was angry JS Lint
            parentId = obj.tabId.replace("_frame", "");
          }
          if ( 'parentId' in obj ) {
            parentId = obj.parentId;
          }

          if ( getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg') ) {
            // 企業フレーム
            emit.toUser('stopCoBrowse', obj, getSessionId(obj.siteKey, obj.tabId, 'shareWindowFlg'));
            // 企業インライン
            // emit.toUser('syncStop', obj, getSessionId(obj.siteKey, obj.tabId, 'syncSessionId'));
            // 消費者フレーム
            emit.toUser('stopCoBrowse', obj, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
            // 消費者インライン
            emit.toUser('stopCoBrowse', obj, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
            coBrowseStopCtrl(obj.siteKey, obj.tabId, true);
          }
          else {
            coBrowseStopCtrl(obj.siteKey, obj.tabId);
          }
          break;
        case 3: // 企業インライン
        case 4: // 消費者インライン
          // 相手先インラインフレーム
          emit.toUser('stopCoBrowse', obj, getSessionId(obj.siteKey, obj.to, 'coBrowseParentSessionId'));
          // 消費者インライン
          break;
      }

      // 消費者親タブ
      var compData = obj;
      if ( parentId ) {
        emit.toUser('stopCoBrowse', data, getSessionId(obj.siteKey, parentId, 'sessionId')); // 消費者の親フレーム
        coBrowseStopCtrl(obj.siteKey, parentId);
        compData.tabId = parentId;
      }

      // 企業一括
      emit.toCompany('stopCoBrowse', compData, obj.siteKey); // リアルタイムモニタを更新する為
    }
    else {
      emit.toCompany('unsetUser', data, obj.siteKey);
    }
  });

  // -----------------------------------------------------------------------
  // ビデオチャット関連
  // ビデオチャットで利用している各値はプレフィックス（vc_）をつけている。
  // -----------------------------------------------------------------------
/*  socket.on('confirmVideochatStart', function (data) {
    var obj = JSON.parse(data);
    // 同形ウィンドウを作成するための情報取得依頼
    if ( !getSessionId(obj.siteKey, obj.toTabId, 'sessionId') ) return false;
    sincloCore[obj.siteKey][obj.toTabId].vc_connectToken = obj.connectToken;
    sincloCore[obj.siteKey][obj.toTabId].vc_syncSessionId = null;
    sincloCore[obj.siteKey][obj.toTabId].vc_syncHostSessionId = socket.id; // 企業画面側のセッションID
    emit.toUser('confirmVideochatStart', data, getSessionId(obj.siteKey, obj.toTabId, 'sessionId'));
  });

  socket.on('videochatConfirmOK', function (data) {
    var obj = JSON.parse(data);
    //obj.connectToken = sincloCore[obj.siteKey][obj.tabId].vc_connectToken;
    emit.toUser('videochatConfirmOK', JSON.stringify(obj), getSessionId(obj.siteKey, obj.tabId, 'vc_syncHostSessionId'));
  });

  socket.on('videoChatConnected', function (data) {
    console.log('VIDEOCHAT CONNECTED : ' + data);
    var obj = JSON.parse(data);
    vc_connectList[obj.tabId] = socket.id;
    if(vc_connectList[obj.to]) {
      console.log('VIDEOCHAT TO FOUND!');
      emit.toUser('videoChatConnected', data, vc_connectList[obj.to]);
    }
  });

  socket.on('askMakeOffer', function (data) {
    console.log('askMakeOffer : ' + data);
    var obj = JSON.parse(data);
    if(vc_connectList[obj.to]) {
      emit.toUser('askMakeOffer', data, vc_connectList[obj.to]);
    }
  });
*/
  socket.on('sendMessage', function(d){
    var obj = JSON.parse(d);
    var host = (obj.host !== "true") ? "host" : "guest";
    emit.toUser('receiveMessage', d, vc_connectList[obj.fromto.to]);
  });

  socket.on('userOut', function (data) {
    var obj = JSON.parse(data);
    if ( !isset(obj.connectToken) ) {
      emit.toCompany('unsetUser', data, obj.siteKey);
    }
  });

  socket.on('settingReload', function (data, ack) {
    var obj = JSON.parse(data), targetKey;
    if ( isset(obj.type) && String(obj.siteKey) === "master") {
      switch (obj.type) {
        case 1: // get new company ( sample: socket.emit('settingReload', JSON.stringify({type:1, siteKey: "master"})); )
          console.log('before', companyList);
          getCompanyList(obj.forceReload);
          console.log('after', companyList);
          break;
        case 2: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:2, targetKey: "demo", siteKey: "master"})); )
          console.log('before', companyList);
          if ( isset(obj.targetKey) && isset(companyList[obj.targetKey]) ) {
            delete companyList[obj.targetKey];
          }
          console.log('after', companyList);
          break;
        case 3: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:3, targetKey: "demo", siteKey: "master"})); )
          console.log('connectList', connectList);
          console.log('doc_connectList', doc_connectList);
          if ( ('targetKey' in obj) && (obj.targetKey in sincloCore) ) {
            console.log("--------------------------------" + obj.targetKey + "--------------------------------");
            console.log('sincloCore', sincloCore[obj.targetKey]);
            console.log("-------------------------------------------------------------------------");
          }
          else {
            var keys = Object.keys(sincloCore);
            for( var i = 0; i < keys.length; i++ ){
              targetKey = keys[i];
              console.log("--------------------------------" + targetKey + "--------------------------------");
              console.log('sincloCore', sincloCore[targetKey]);
            }
            console.log("-------------------------------------------------------------------------");
          }
          break;
        case 4: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:4, siteKey: "master", targetSiteKey: "master"})); )
          if ( !('targetSiteKey' in obj) ) return false;
          targetKey = obj.targetSiteKey;
          if ( targetKey in company.info ) {
            var activeList = [];
            if ( targetKey in activeOperator ) {
              activeList = activeOperator[targetKey];
            }
            console.log('info', company.info[targetKey]);
            emit.toMine('consoleLogInfo', {u: company.info[targetKey], a: activeList}, socket);
          }
          break;
        case 5: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:5, siteKey: "master", str: ">>>>>>>>>>>>>>>>>>>>>"})); )
          var str = ">>>>>>>>>>>>>>>>>>>>>";
          if (('str' in obj)) {
            str = obj.str;
          }
          console.log(str);
          break;
        case 6: // reset LiveAssist current count socket.emit('settingReload', JSON.stringify({type:6, targetKey: "demo", siteKey: "master"}));
          console.log("settingReload >>> reset LiveAssist current count");
          laSessionCounter.initializeCurrentCount(obj.targetKey);
          break;
        case 7: // view all Obj socket.emit('settingReload', JSON.stringify({type:7, targetKey: "demo", siteKey: "master"}));
          console.log("getAllObj --------------------------------------------------");
          console.log("sincloCore : " + JSON.stringify(sincloCore[obj.targetKey]));
          console.log("connectList : " + JSON.stringify(connectList));
          console.log("c_connectList : " + JSON.stringify(c_connectList));
          console.log("doc_connectList : " + JSON.stringify(doc_connectList));
          console.log("customerList : " + JSON.stringify(customerList[obj.targetKey]));
          console.log("End --------------------------------------------------------");
          break;
        case 8: // confirm tabId user is exists? socket.emit('settingReload', JSON.stringify({type:8, accessId:"", ipAddress:"", targetKey: "demo", siteKey: "master"}));
          var keys = isset(customerList[obj.targetKey]) ? Object.keys(customerList[obj.targetKey]) : [];
          var targetObj = null;
          var targetKey = "";
          if(keys && keys.length > 0) {
            keys.some(function(key) {
              if(key.indexOf(obj.accessId + "_" + obj.ipAddress) >= 0) {
                targetKey = key;
                targetObj = customerList[obj.targetKey][key];
                return true;
              }
            });
          }
          var targetSocketId = "";
          if(isset(targetObj) && isset(targetKey)) {
            var splited = targetKey.split("/#");
            targetSocketId = "/#" + splited[1];
          }
          var sendData = {
            inCustomerList: isset(targetObj),
            sincloCoreExists: isset(sincloCore[obj.targetKey]),
            sincloCoreDataExists: isset(sincloCore[obj.targetKey][(isset(targetObj) ? (isset(targetObj['tabId']) ? targetObj['tabId'] : "") : "")]),
            connectListExists: isset(targetObj) && isset(connectList),
            inConnectList: isset(targetObj) && isset(connectList) && (Object.keys(connectList).indexOf(targetSocketId) !== -1),
            customerListExists: isset(customerList) && isset(customerList[obj.targetKey]),
            socketIdIsFound: isset(targetSocketId),
            socketConnected: isset(targetSocketId) && isset(io.sockets.connected[targetSocketId]),
            inSocketsList: isset(targetSocketId) && isset(io.sockets.sockets) && (Object.keys(io.sockets.sockets).indexOf(targetSocketId) !== 1)
          };
          console.log("send data : " + JSON.stringify(sendData));
          emit.toMine('userExistsResult', sendData, socket);
          if(isset(targetObj) && isset(targetObj['tabId'])) {
            emit.toUser('checkExists', {siteKey: obj.targetKey, tabId: targetObj['tabId'], returnTo: socket.id}, getSessionId(obj.targetKey, targetObj['tabId'], 'sessionId'));
          } else {
            console.log("checkExists NOT sent.");
          }
          break;
        case 9: // view all Obj count socket.emit('settingReload', JSON.stringify({type:79 targetKey: "demo", siteKey: "master"}));
          console.log("getAllObj count --------------------------------------------------");
          console.log("sincloCore : " + Object.keys(sincloCore[obj.targetKey]).length);
          console.log("connectList : " + Object.keys(connectList).length);
          console.log("c_connectList : " + Object.keys(c_connectList).length);
          console.log("doc_connectList : " + Object.keys(doc_connectList).length);
          console.log("customerList : " + Object.keys(customerList[obj.targetKey]).length);
          console.log("End --------------------------------------------------------");
          break;
        case 10: // view all Obj count socket.emit('settingReload', JSON.stringify({type:79 targetKey: "demo", siteKey: "master"}));
          console.log("getSclist --------------------------------------------------");
          console.log("scList : " + JSON.stringify(scList[obj.targetKey]));
          console.log("End --------------------------------------------------------");
          break;
        case 11: // view  socket.emit('settingReload', JSON.stringify({type:11, targetKey: "demo", targetKey: "master"}));
          var targetKey = obj.targetKey;
          console.log("getCommonMemoryList --------------------------------------------------");
          console.log("common.companySettings : " + JSON.stringify(common.companySettings[targetKey]));
          console.log("common.widgetSettings : " + JSON.stringify(common.widgetSettings[targetKey]));
          console.log("common.autoMessageSettings : " + JSON.stringify(common.autoMessageSettings[targetKey]));
          console.log("common.operationHourSettings : " + JSON.stringify(common.operationHourSettings[targetKey]));
          console.log("End --------------------------------------------------------");
          break;
        case 12: // update all DB-setting  socket.emit('settingReload', JSON.stringify({type:12, targetKey: "demo", targetKey: "master"}));
          var targetKey = obj.targetKey;
          console.log("BEGIN RELOAD DB-settings --------------------------------------------------");
          console.log("TARGET : " + targetKey);
          common.reloadSettings(targetKey);
          console.log("END   RELOAD DB-settings --------------------------------------------------");
          break;
        default:
          break;
      }
    }
    if(ack) {
      ack();
    }
  });

  socket.on('resCheckExists', function (data) {
    var obj = JSON.parse(data);
    if(isset(obj['returnTo'])) {
      emit.toUser('resCheckExists', obj, obj['returnTo']);
    } else {
      console.log("resCheckExists NOT sent");
    }
  });

  // [消費者：外部接続フレーム] 外部接続フレームからの初期message
  socket.on('connectFromSyncInit', function (d) {
    var obj = JSON.parse(d);
    socket.join(obj.siteKey + emit.roomKey.frame);
    sincloCore[obj.siteKey][obj.parentId].shareWindowId = socket.id;
    if ( getSessionId(obj.siteKey, obj.tabId, "sessionId") ) {
      sincloCore[obj.siteKey][obj.tabId].shareWindowId = socket.id;
    }
    else {
      sincloCore[obj.siteKey][obj.tabId] = {
        sessionId: null,
        parentId: null,
        connectToken: null,
        syncFrameSessionId: getSessionId(obj.siteKey, obj.parentId, "syncFrameSessionId"),
        shareWindowId: socket.id,
        shareWindowFlg: true
      };
    }
  });

  // [消費者：外部接続フレーム] 同形ウィンドウを作成するための情報受け取り
  socket.on('sendWindowInfoFromFrame', function (data) {
    var obj = JSON.parse(data);
    if ( 'connectToken' in obj ) {
      sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
    }
    // 同形ウィンドウを作成するための情報渡し
    emit.toUser('windowSyncInfo', obj, getSessionId(obj.siteKey, obj.parentId, 'syncHostSessionId'));
  });

  // iFrame内からの初期message
  socket.on('startSyncToFrame', function (d) {
    var obj = JSON.parse(d);
    socket.join(obj.siteKey + emit.roomKey.frame);
    if ( !getSessionId(obj.siteKey, obj.tabId, 'shareWindowId') && obj.tabId.match(/\_frame$/) ) {
      sincloCore[obj.siteKey][obj.tabId] = {
        sessionId: null,
        parentId: null,
        connectToken: null,
        syncFrameSessionId: getSessionId(obj.siteKey, obj.tabId.replace("_frame", ""), "syncFrameSessionId"),
        shareWindowId: getSessionId(obj.siteKey, obj.tabId.replace("_frame", ""), "shareWindowId"),
        shareWindowFlg: true
      };
    }
    sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
    sincloCore[obj.siteKey][obj.tabId].parentTabId = obj.parentId;
    var hostFrameId = getSessionId(obj.siteKey, obj.parentId, "syncFrameSessionId");
    if ( hostFrameId ) {
      sincloCore[obj.siteKey][obj.tabId].syncFrameSessionId = hostFrameId;
    }
    sincloCore[obj.siteKey][obj.parentId].syncSessionId = socket.id;
    emit.toUser('connectFromSync', d, getSessionId(obj.siteKey, obj.tabId, "shareWindowId"));
  });

 // 資料共有開始(企業から)
  socket.on('docShareConnect', function(d) {
    var obj = JSON.parse(d);
    if ( !getSessionId(obj.siteKey, obj.tabId, "sessionId") ) {
      // TODO 接続失敗
      return false;
    }
    if ( !(obj.tabId in doc_connectList) ) {
      doc_connectList[obj.tabId] = {};
    }
    doc_connectList[obj.tabId][obj.from] = socket.id;
    doc_connectList.socketId[socket.id] = {type: obj.from, tabId: obj.tabId, siteKey: obj.siteKey};
    if ( obj.from === "company" ) {
      // 通知先を変える
      var sessionId = ( String(getSessionId(obj.siteKey, obj.tabId, "shareWindowFlg")) !== "true" ) ? getSessionId(obj.siteKey, obj.tabId, "sessionId") : getSessionId(obj.siteKey, obj.tabId, "syncSessionId");

      // 資料共有中ユーザーをセットする
      var targetId = (getSessionId(obj.siteKey, obj.tabId, "parentTabId")) ? getSessionId(obj.siteKey, obj.tabId, "parentTabId") : obj.tabId;
      sincloCore[obj.siteKey][targetId].docShareId = obj.responderId; // 資料共有中ユーザーをセットする
      // 消費者側に確認ポップアップを表示する
      emit.toUser('docShareConnect', d, sessionId);
      obj.tabId = targetId;
      emit.toCompany('docShareConnect', obj, obj.siteKey); // リアルタイムモニタへ通知
    }
  });

  // 資料共有のキャンセル
  socket.on('docShareCancel', function(d){
    var obj = JSON.parse(d);
    var targetId = obj.tabId.replace("_frame", "");
    emit.toUser('docDisconnect', obj, doc_connectList[targetId]["company"]);
  });

 // 資料共有再接続
  socket.on('docShareReConnect', function(d) {
    var obj = JSON.parse(d);
    if ( !getSessionId(obj.siteKey, obj.tabId, "sessionId") ) {
      // TODO 再接続失敗
      return false;
    }
    if ( !(obj.tabId in doc_connectList) ) {
      doc_connectList[obj.tabId] = {};
    }
    doc_connectList[obj.tabId][obj.from] = socket.id;
    doc_connectList.socketId[socket.id] = {type: obj.from, tabId: obj.tabId, siteKey: obj.siteKey};
  });

  // 共有する資料を変更
  socket.on('changeDocument', function(d){
    var obj = JSON.parse(d);
    emit.toUser('changeDocument', d, doc_connectList[obj.tabId].customer);
  });

  // 共有する資料を変更
  socket.on('compReadFile', function(d){
    var obj = JSON.parse(d);
    emit.toUser('compReadFile', d, doc_connectList[obj.tabId].company);
  });

  socket.on('docShareConnectToCustomer', function(d) {
    var obj = JSON.parse(d);
    if ( !getSessionId(obj.siteKey, obj.tabId, "sessionId") ) {
      // TODO 接続失敗
      return false;
    }
    if ( !(obj.tabId in doc_connectList) ) {
      doc_connectList[obj.tabId] = {};
    }
    doc_connectList[obj.tabId][obj.from] = socket.id;
    doc_connectList.socketId[socket.id] = {type: obj.from, tabId: obj.tabId, siteKey: obj.siteKey};

    emit.toUser('docShareConnect', d, getSessionId(obj.siteKey, obj.tabId, "sessionId"));
  });

  socket.on('docSendAction', function(d){
    var obj = JSON.parse(d);
    if ( (obj.tabId in doc_connectList) && (obj.to in doc_connectList[obj.tabId]) ) {
      emit.toUser('docSendAction', d, doc_connectList[obj.tabId][obj.to]);
    }
  });

  // ユーザーのアウトを感知
  socket.on('disconnect', function () {
    console.log("【" + socket.id + "】ON DISCONNECT");
    var info = {};
    // 資料共有の場合
    if ( doc_connectList.socketId.hasOwnProperty(socket.id) ) {
      info = doc_connectList.socketId[socket.id]; // tabId, type(company or customer)
      var partner = (info.type === "company") ? "customer" : "company";
      doc_connectList.timeout[socket.id] = setTimeout(function(){ // 3 minutes
        delete doc_connectList.socketId[socket.id];
        if ( doc_connectList.hasOwnProperty(info.tabId) ) {
          var doc = doc_connectList[info.tabId];
          if ( doc[info.type] === socket.id ) { // 最後のsocket.idと一致したら切断扱い
            // 相手が接続中の場合
            if ( doc.hasOwnProperty(partner) ) {
              // 相手の資料共有画面を閉じる
              emit.toUser('docDisconnect', info, doc[partner]);
              delete doc_connectList[info.tabId][info.type];
            }
            // 相手の接続が切れている時
            else {
              delete doc_connectList[info.tabId];
              var tabId = (getSessionId(info.siteKey, info.tabId + "_frame", "connectToken")) ? info.tabId + "_frame" : info.tabId;
              /* 資料共有中ユーザーを削除する */
              if ( getSessionId(info.siteKey, tabId, 'docShareId') ) {
                delete sincloCore[info.siteKey][tabId].docShareId;
              }
              // 画面共有を行っている場合
              if ( getSessionId(info.siteKey, tabId, 'connectToken') ) {
                // 企業に知らせる
                emit.toUser('docDisconnect', info, getSessionId(info.siteKey, tabId, 'syncSessionId'));
                // 消費者に知らせる
                emit.toUser('docDisconnect', info, getSessionId(info.siteKey, tabId, 'sessionId'));
              }
              // 資料共有のみの場合
              else {
                // 相手画面に知らせる
                emit.toUser('docDisconnect', info, getSessionId(info.siteKey, info.tabId, 'sessionId'));
              }
              // 企業側画面に知らせる
              emit.toCompany('docDisconnect', info, info.siteKey);
            }
          }
        }
        delete doc_connectList.timeout[socket.id];
      }, 5000);
      return false;
    }
    // リアルタイムモニタからのアクセスの場合
    if ( socket.id in company.user ) {
      // ユーザーの情報を削除
      var userInfo = company.user[socket.id];
      delete company.user[socket.id];

      // socket.id情報を削除
      delete company.info[userInfo.siteKey][userInfo.userId][socket.id];

      if ( !(userInfo.userId in company.timeout[userInfo.siteKey]) ) {
        company.timeout[userInfo.siteKey][userInfo.userId] = "";
      }

      // チャット中ユーザーが居たら、入力終了フラグを送る
      for ( var tabId in sincloCore[userInfo.siteKey] ) {
        var tab = sincloCore[userInfo.siteKey][tabId];

        if ( ('chatSessionId' in tab) && isset(tab.chatSessionId) && tab.chatSessionId === socket.id ) {
          // 企業へ送る
          emit.toCompany('receiveTypeCond', {status: false, type: 1, tabId: tabId, message: ""}, userInfo.siteKey);
          // 消費者へ送る
          emit.toSameUser('receiveTypeCond', {status: false, type: 1, tabId: tabId, message: ""}, userInfo.siteKey, tab.sincloSessionId);
        }

      }

      company.timeout[userInfo.siteKey][userInfo.userId] = setTimeout(function(){
        var keys = {};
        // 同一ユーザーが完全にログアウトした場合はユーザーのオブジェクトごと削除
        if ( (userInfo.siteKey in company.info) && (userInfo.userId in company.info[userInfo.siteKey]) ) {
          keys = Object.keys(company.info[userInfo.siteKey][userInfo.userId]);
        }
        if ( keys.length === 0 ) {
          delete company.info[userInfo.siteKey][userInfo.userId];
          delete company.timeout[userInfo.siteKey][userInfo.userId];

          // チャット対応上限のリセット
          if ( scList.hasOwnProperty(userInfo.siteKey) && scList[userInfo.siteKey].cnt.hasOwnProperty(userInfo.userId) ) {
            console.log("【" + userInfo.siteKey + "】disconnect::対応上限数設定 " + scList[userInfo.siteKey].cnt[userInfo.userId] + " / " + scList[userInfo.siteKey].user[userInfo.userId] +  " userId : " + userInfo.userId);
            delete scList[userInfo.siteKey].cnt[userInfo.userId];
            delete scList[userInfo.siteKey].user[userInfo.userId];
          }

          // 新しいユーザーの人数を送る
          var cnt = Object.keys(company.info[userInfo.siteKey]);
          emit.toCompany('outCompanyUser', {siteKey: userInfo.siteKey, userCnt: cnt.length, userId: userInfo.userId}, userInfo.siteKey);

          // 受付中オペレータの情報削除
          if ( (userInfo.siteKey in activeOperator) && (userInfo.userId in activeOperator[userInfo.siteKey]) ) {
            delete activeOperator[userInfo.siteKey][userInfo.userId];
          }
          var opKeys = [];
          if ( userInfo.siteKey in activeOperator ) {
            opKeys = Object.keys(activeOperator[userInfo.siteKey]);
          }
          var sendData = {siteKey: userInfo.siteKey,count: opKeys.length, scInfo: scList};

          if ( scList.hasOwnProperty(userInfo.siteKey) ) {
            sendData.scInfo = scList[userInfo.siteKey].cnt;
          }
          emit.toCompany('activeOpCnt', sendData, userInfo.siteKey);

        }
      }, 5000);
      return false;
    }
    // タグ入りページからのアクセスの場合
    //if ( !(socket.id in connectList) ) return false;
    info = connectList[socket.id];
    if (info && getSessionId(info.siteKey, info.tabId, 'sessionId')) {
      var core = sincloCore[info.siteKey][info.tabId];
      var siteId = companyList[info.siteKey];
      var timeout = ('connectToken' in core) ? 10000 : 5000;
      // 消費者側の履歴更新
      if ( !('subWindow' in core) || ('subWindow' in core) && !core.subWindow && !core.shareWindowFlg ) {
        // 履歴の更新
        var sincloSessionId = sincloCore[info.siteKey][info.tabId].sincloSessionId;
        pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [siteId, sincloSessionId, info.userId], function(err, rows){
          if ( err !== null && err !== '' ) return false; // DB接続断対応

          if ( isset(rows) && isset(rows[0]) ) {
            var now = formatDateParse();
            timeUpdate(rows[0].id, {}, now, function(){});
          }
        });

      }

      if ( 'timeoutTimer' in sincloCore[info.siteKey][info.tabId] ) {
        clearTimeout(sincloCore[info.siteKey][info.tabId].timeoutTimer);
      }

      var sincloSessionId = sincloCore[info.siteKey][info.tabId].sincloSessionId;
      sincloCore[info.siteKey][info.tabId].timeoutTimer = setTimeout(function(){
        var historyId = sincloCore[info.siteKey][info.tabId].historyId;
        // sincloCoreから情報削除
        delete sincloCore[info.siteKey][info.tabId];
        // c_connectListから情報削除
        if ( (info.siteKey in c_connectList) && (info.tabId in c_connectList[info.siteKey]) ) {
          delete c_connectList[info.siteKey][info.tabId];
        }
        if ( core.subWindow ) {
          // 企業側
          if ( ('toTabId' in core) && getSessionId(info.siteKey, core.toTabId, 'sessionId') ) {
            if ( ('connectToken' in core) && getSessionId(info.siteKey, core.toTabId, 'connectToken') && core.connectToken !== getSessionId(info.siteKey, core.toTabId, 'connectToken') ) return false;
            emit.toUser('syncStop', {siteKey: info.siteKey, tabId: core.toTabId, connectToken: core.connectToken}, getSessionId(info.siteKey, core.toTabId, 'sessionId'));
            if ( getSessionId(info.siteKey, core.toTabId, 'parentTabId') ) {
              var parentTabId = getSessionId(info.siteKey, core.toTabId, 'parentTabId');
              emit.toUser('syncStop', {siteKey: info.siteKey, tabId: core.toTabId, connectToken: core.connectToken}, getSessionId(info.siteKey, parentTabId, "sessionId"));
              emit.toCompany('syncStop', {siteKey: info.siteKey, tabId: parentTabId}, info.siteKey);
              syncStopCtrl(info.siteKey, parentTabId);
            }
            else {
              emit.toCompany('syncStop', {siteKey: info.siteKey, tabId: core.toTabId}, info.siteKey);
            }
            syncStopCtrl(info.siteKey, core.toTabId);
          }
          // toがタイムアウトしている時
          else {
            if ( 'connectTab' in core ) {
              emit.toUser('syncStop', {siteKey: info.siteKey, tabId: core.connectTab, connectToken: core.connectToken}, getSessionId(info.siteKey, core.connectTab, "sessionId"));
              emit.toCompany('syncStop', {siteKey: info.siteKey, tabId: core.connectTab}, info.siteKey);
              syncStopCtrl(info.siteKey, core.connectTab);
            }
            else {
              emit.toCompany('syncStop', {siteKey: info.siteKey, tabId: core.toTabId}, info.siteKey);
              syncStopCtrl(info.siteKey, core.toTabId);
            }
          }
        }
        // 消費者側
        else {
          // 応対数再計算
          if ( ('chat' in core) && scList.hasOwnProperty(info.siteKey) && scList[info.siteKey].cnt.hasOwnProperty(core.chat) ) {
            scList[info.siteKey].cnt[core.chat] = chatApi.calcScNum(info, core.chat);
            console.log("【" + info.siteKey + "】cus::disconnect::対応上限数設定 " + scList[info.siteKey].cnt[core.chat] + " / " + scList[info.siteKey].user[core.chat] +  " userId : " + core.chat);
          }
          if ( 'syncFrameSessionId' in core ) {
            emit.toUser('unsetUser', {siteKey: info.siteKey, tabId: info.tabId}, core.syncFrameSessionId);
            syncStopCtrl(info.siteKey, info.tabId);
          }

          if ( 'coBrowseConnectToken' in core ) {
            emit.toUser('unsetUser', {siteKey: info.siteKey, tabId: info.tabId}, core.coBrowseParentSessionId);
            coBrowseStopCtrl(info.siteKey, info.tabId);
          }

          if ( ('syncSessionId' in core) && (core.syncSessionId in connectList) && ('tabId' in connectList[core.syncSessionId]) ) {
            var tabId = connectList[core.syncSessionId].tabId;
            if ( ('parentTabId' in core) && getSessionId(info.siteKey, tabId, 'subWindow') ) {
              sincloCore[info.siteKey][tabId].connectTab = core.parentTabId;
            }
          }

          var sendData = {siteKey: info.siteKey, tabId: info.tabId};

          if ( scList.hasOwnProperty(info.siteKey) ) {
            sendData.scInfo = scList[info.siteKey].cnt;
          }
          if(!functionManager.isEnabled(info.siteKey, functionManager.keyList.monitorPollingMode)) {
            emit.toCompany('unsetUser', sendData, info.siteKey);
          }

          if(sincloSessionId) {
            if(isset(sincloCore[info.siteKey][sincloSessionId])
              && isset(sincloCore[info.siteKey][sincloSessionId]['sessionIds'])) {
              var sessionIds = sincloCore[info.siteKey][sincloSessionId].sessionIds;
              delete sessionIds[socket.id];
              if(Object.keys(sessionIds).length === 0) {
                delete sincloCore[info.siteKey][sincloSessionId];
              }
            }
          }
        }
      }, timeout);
      // connectListから削除
      delete connectList[socket.id];

      if(sincloSessionId) {
        var sincloSession = sincloCore[info.siteKey][sincloSessionId];
        if(isset(sincloSession) && isset(sincloSession.sessionIds) && Object.keys(sincloSession.sessionIds).length > 0) {
          delete sincloSession.sessionIds[socket.id];
        } else {
          console.log("sincloSession : " + sincloSessionId + " is null.");
        }
      }

      var keys = Object.keys(customerList[info.siteKey]);
      if(keys && keys.length > 0) {
        keys.forEach(function(key) {
          if(key.indexOf(socket.id) >= 0) {
            console.log("delete customerList[" + info.siteKey + "]["+key+"]");
            delete customerList[info.siteKey][key];
          }
        });
      }
    } else {
      if(info && isset(sincloCore[info.siteKey][info.tabId])) {
        console.log("SESSION ID IS NULL info : " + JSON.stringify(info) + " info.siteKey : " + info.siteKey + " info.tabId " + info.tabId + " sincloSessionId " + sincloCore[info.siteKey][info.tabId].sincloSessionId);
        if(isset(info.siteKey) && isset(info.tabId)) {
          var sincloSessionId = sincloCore[info.siteKey][info.tabId].sincloSessionId;
          // sincloCoreから情報削除
          delete sincloCore[info.siteKey][info.tabId];
          if (isset(sincloCore[info.siteKey][sincloSessionId]) && isset(sincloCore[info.siteKey][sincloSessionId].sessionIds)) {
            var sessionIds = sincloCore[info.siteKey][sincloSessionId].sessionIds;
            Object.keys(sessionIds).forEach(function (key) {
              if (!isset(io.sockets.connected[key])) {
                console.log("delete not exist sessionId : " + key);
                delete sessionIds[key];
                console.log("remains : " + Object.keys(sessionIds).length);
                if (Object.keys(sessionIds).length === 0) {
                  delete sincloCore[info.siteKey][sincloSessionId];
                }
              }
            });
          } else {
            console.log("sessionIds is null");
            delete connectList[socket.id];
            var keys = Object.keys(customerList[info.siteKey]);
            if (keys && keys.length > 0) {
              keys.forEach(function (key) {
                if (key.indexOf(socket.id) >= 0) {
                  delete customerList[info.siteKey][key];
                }
              });
            }
          }
        }
      } else {
        console.log("sincloCore[info.siteKey][info.tabId] is null");
        if(info) {
          var keys = Object.keys(customerList[info.siteKey]);
          if (keys && keys.length > 0) {
            keys.forEach(function (key) {
              if (key.indexOf(socket.id) >= 0) {
                console.log("delete customer sitekey : " + info.siteKey + " key : " + key);
                var customer = customerList[info.siteKey][key];
                delete customerList[info.siteKey][key];
                if(isset(customer) && customer.sincloSessionId) {
                  var sincloSessionId = customer.sincloSessionId;
                  if(isset(sincloCore[info.siteKey][sincloSessionId])
                    && isset(sincloCore[info.siteKey][sincloSessionId]['sessionIds'])) {
                    console.log("target sincloSessionId > " + sincloSessionId + " : " + JSON.stringify(sincloCore[info.siteKey][sincloSessionId]));
                    var sessionIds = sincloCore[info.siteKey][sincloSessionId].sessionIds;
                    delete sessionIds[socket.id];
                    if(Object.keys(sessionIds).length === 0) {
                      console.log("delete sincloSessionId > " + sincloSessionId);
                      delete sincloCore[info.siteKey][sincloSessionId];
                    }
                  }
                }
                delete customerList[info.siteKey][key];
              }
            });
          }
          delete connectList[socket.id];
        } else {
          console.log("info is null socket.id : " + socket.id);
          delete connectList[socket.id];
        }
      }
    }
  });
});
