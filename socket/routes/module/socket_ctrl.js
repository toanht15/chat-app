var database = require('../database');

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
    company = {
        info : {}, // siteKeyをキーとした企業側ユーザー人数管理
        user : {}, // socket.idをキーとした企業側ユーザー管理
        timeout : {} // userIdをキーとした企業側ユーザー管理
    };

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

function timeUpdate(historyId, obj, time){
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

      if ( insertStayData.url === '' || insertStayData.url === rows[0].url ) return false;
      pool.query("INSERT INTO t_history_stay_logs SET ?", insertStayData,
        function (error,results,fields){
        }
      );
    }
  );
}

var companyList = {};
function getCompanyList(){
  pool.query('select * from m_companies;', function(err, rows){
    var key = Object.keys(rows);
    for ( var i = 0; key.length > i; i++ ) {
      var row = rows[key[i]];
      companyList[row.company_key] = row.id;
    }
  });
}
getCompanyList();

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
    'connectToken', 'syncSessionId', 'syncHostSessionId',
    'syncFrameSessionId', 'shareWindowId', 'shareWindowFlg'
  ];
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
  var responderId = getSessionId(o.siteKey, o.tabId, 'responderId');
  if ( isset(responderId) && isset(connectToken) ) {
    o.responderId = responderId;
    o.connectToken = connectToken;
  }
  var docShareId = getSessionId(o.siteKey, o.tabId, 'docShareId');
  if ( isset(docShareId) ) {
    o.docShareId = docShareId;
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
  toMine: function(ev, d, s){ // 送り主に返信
    var obj = this._convert(d);
    return s.emit(ev, obj);
  },
  toUser: function(ev, d, sId){ // 対象ユーザーに送信(sId = the session id)
    var obj = this._convert(d);
    if ( !isset(sId) ) return false;
    if ( !isset(io.sockets.connected[sId]) ) return false;
    return io.sockets.connected[sId].emit(ev, obj);
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
var db = {
  addHistory: function(obj, s) {
    if ( isset(obj.tabId) && isset(obj.siteKey) ) {
      if ( !isset(companyList[obj.siteKey]) || obj.subWindow ) return false;
      var siteId = companyList[obj.siteKey];
      pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [siteId, obj.tabId, obj.userId], function(err, rows){
        var now = formatDateParse();
        if ( !(obj.tabId in sincloCore[obj.siteKey]) ) {
          sincloCore[obj.siteKey][obj.tabId] = {};
        }

        if ( isset(rows) && isset(rows[0]) ) {
          sincloCore[obj.siteKey][obj.tabId].historyId = rows[0].id;
          timeUpdate(rows[0].id, obj, now);
          obj.historyId = rows[0].id;
          emit.toMine('setHistoryId', obj, s);
        }
        else {
          //insert
          var insertData = {
            m_companies_id: siteId,
            visitors_id: obj.userId,
            tab_id: obj.tabId,
            ip_address: obj.ipAddress,
            user_agent: obj.userAgent,
            access_date: formatDateParse(obj.time),
            referrer_url: obj.referrer,
            created: now,
            modified: now
          };

          pool.query("INSERT INTO t_histories SET ?", insertData,
            function (error,results,fields){
              if ( isset(error) ) {
                return false;
              }
              var historyId = results.insertId;
              sincloCore[obj.siteKey][obj.tabId].historyId = historyId;
              timeUpdate(historyId, obj, now);
              obj.historyId = historyId;
              emit.toMine('setHistoryId', obj, s);
            }
          );
        }
      });
    }
  }
};

// var console = {
//   date: function(){
//     var d = new Date();
//     return d.getFullYear() + "/" + ( "0" + (d.getMonth() + 1) ).slice(-2) + "/" + ("0" + d.getDate()).slice(-2) + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);
//   },
//   log: function(a, b){
//     var label = null, data = null, d = this.date();
//     switch(typeof b){
//       case 'object':
//         var data = "【" + a + "|" + d + "】 " + JSON.stringify(b, null, "\t");
//         break;
//       case 'string':
//         var data = "【" + a + "|" + d + "】 " + b;
//         break;
//       default:
//         var data = "【" + d + "】 " + JSON.stringify(a, null, "\t");
//     }
//     reqlogger.info(data);
//   }
// };

//接続確立時の処理
io.sockets.on('connection', function (socket) {

  // チャット用
  var chatApi = {
    cnst: {
      observeType: {
        company: 1,
        customer: 2,
        auto: 3,
        start: 98,
        end: 99,
      }
    },
    set: function(d){ // メッセージが渡されてきたとき
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
                sql += " chat.id, chat.message, chat.message_type as messageType, chat.m_users_id as userId, mu.display_name as userName, chat.message_read_flg as messageReadFlg, chat.created ";
                sql += "FROM t_history_chat_logs AS chat ";
                sql += "LEFT JOIN m_users AS mu ON ( mu.id = chat.m_users_id ) ";
                sql += "WHERE t_histories_id = ? ORDER BY created";

            pool.query(sql, [chatData.historyId], function(err, rows){
              var messages = ( isset(rows) ) ? rows : [];
              var setList = {};
              for (var i = 0; i < messages.length; i++) {
                var date = messages[i].created;
                date = new Date(date);
                if ( ('userName' in messages[i]) && obj.showName !== 1 ) {
                  delete messages[i].userName;
                }
                setList[fullDateTime(messages[i].created)] = messages[i];
              }
              chatData.messages = objectSort(setList);
              obj.chat = chatData;
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
        visitors_id: d.userId,
        m_users_id: d.mUserId,
        message: d.chatMessage,
        message_type: d.messageType
      };

      pool.query('SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;', insertData.t_histories_id,
        function(err, rows){
          if ( rows && rows[0] ) {
            insertData.t_history_stay_logs_id = rows[0].id;
          }
          insertData.created = (('created' in d)) ? new Date(d.created) : new Date();
          // オートメッセージの場合は既読
          if (Number(insertData.message_type === 3) ) {
              insertData.message_read_flg = 1;
          }
          pool.query('INSERT INTO t_history_chat_logs SET ?', insertData, function(error,results,fields){
            if ( !isset(error) ) {
              if ( !isset(sincloCore[d.siteKey][d.tabId].sessionId)) return false;
              var sId = sincloCore[d.siteKey][d.tabId].sessionId;
              // 書き込みが成功したら顧客側に結果を返す
              emit.toUser('sendChatResult', {tabId: d.tabId, chatId: results.insertId, messageType: d.messageType, created: insertData.created, ret: true, chatMessage: d.chatMessage, siteKey: d.siteKey}, sId);
              if (Number(insertData.message_type) === 3) return false;
              // 書き込みが成功したら企業側に結果を返す
              emit.toCompany('sendChatResult', {tabId: d.tabId, chatId: results.insertId, sort: fullDateTime(insertData.created), created: insertData.created, userId: insertData.m_users_id, messageType: d.messageType, ret: true, message: d.chatMessage, siteKey: d.siteKey}, d.siteKey);
            }
            else {
              // 書き込みが失敗したらエラーを渡す
              return emit.toUser('sendChatResult', {tabId: d.tabId, messageType: d.messageType, ret: false, siteKey: d.siteKey}, d.siteKey);
            }
          });
        }
      );

    },
    notifyCommit: function(d){ // DBに書き込むとき

      var insertData = {
        t_histories_id: sincloCore[d.siteKey][d.tabId].historyId,
        visitors_id: d.userId,
        m_users_id: d.mUserId,
        message: d.chatMessage,
        message_type: d.messageType,
        message_read_flg: 1,
        created: new Date(d.created)
      };

      pool.query('SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;', insertData.t_histories_id,
        function(err, rows){
          if ( rows && rows[0] ) {
            insertData.t_history_stay_logs_id = rows[0].id;
          }
          pool.query('INSERT INTO t_history_chat_logs SET ?', insertData, function(error,results,fields){
            if ( isset(error) ) {
              console.log("RECORD INSERT ERROR: notifyCommit-func:" + error);
            }
          });
        }
      );

    },
    sendUnreadCnt: function(evName, obj, toUserFlg){
      var sql, ret = {tabId: obj.tabId, chatUnreadId:null, chatUnreadCnt:0}, tabId = obj.tabId, siteId = companyList[obj.siteKey];

      sql  = " SELECT chat.id AS chatId, his.visitors_id, his.tab_id, chat.message FROM t_histories AS his";
      sql += " INNER JOIN t_history_chat_logs AS chat ON ( his.id = chat.t_histories_id )";
      sql += " WHERE his.tab_id = ? AND his.m_companies_id = ? AND chat.message_type = 1";
      sql += "   AND chat.m_users_id IS NULL AND chat.message_read_flg != 1 ORDER BY chat.id desc";
      pool.query(sql, [tabId, siteId], function(err, rows){
        if ( !isset(err) && (rows.length > 0 && isset(rows[0].chatId))) {
          ret.chatUnreadId = rows[0].chatId;
          ret.chatUnreadCnt = rows.length;
        }
        emit.toCompany(evName, ret, obj.siteKey);
        if ( toUserFlg ) {
          emit.toMine(evName, ret, socket);
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
        data = res.data;
        send = data;
    if ( res.type !== 'admin' ) {
      if ( data.userId === undefined || data.userId === '' || data.userId === null ) {
        send.userId = makeUserId();
      }
      if ( data.accessId === undefined || data.accessId === '' || data.accessId === null ) {
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
            company.user[socket.id] = {
                userId: data.userId,
                siteKey: res.siteKey
            };
            if ( !(res.siteKey in activeOperator) ) {
                activeOperator[res.siteKey] = {};
            }
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

            if ( ('status' in data) && String(data.status) === '1' ) {
              activeOperator[res.siteKey][data.userId] = socket.id;
            }
            else {
              if ( data.userId in activeOperator[res.siteKey] ) {
                delete activeOperator[res.siteKey][data.userId];
              }
              data.status =  0;
            }
              emit.toUser('cngOpStatus', {status: data.status}, activeOperator[res.siteKey][data.userId]);

        }
        if ( res.siteKey in company.info ) {
          cnt = Object.keys(company.info[res.siteKey]);
        }

        if ( res.siteKey in activeOperator ) {
          opKeys = Object.keys(activeOperator[res.siteKey]);
        }

        data.userCnt = cnt.length;
        data.onlineUserCnt = opKeys.length;

        // 消費者にアクセス情報要求
        emit.toClient('getAccessInfo', send, res.siteKey);
        // 企業側に情報提供
        emit.toCompany('getAccessInfo', data, res.siteKey);
      }
      else {
        send.activeOperatorCnt = getOperatorCnt(res.siteKey);
        socket.join(res.siteKey + emit.roomKey.client);
        emit.toMine('accessInfo', send, socket);
      }

    }
  });

  socket.on("connectedForSync", function (data) {
    // ページ表示開始時間
    var d = new Date();
    // 待機オペレーター人数
    var obj = JSON.parse(data);
    var actOpCnt = getOperatorCnt(obj.siteKey);

    emit.toMine("retConnectedForSync", {pagetime: Date.parse(d), activeOperatorCnt: actOpCnt}, socket);
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

    emit.toCompany("sendCustomerInfo", obj, obj.siteKey);
    if ( ('contract' in obj) && ('chat' in obj.contract) && obj.contract.chat === false) return false;
    chatApi.sendUnreadCnt("sendChatInfo", obj, false);
  });

  socket.on("getCustomerInfo", function(data) {
    var obj = JSON.parse(data);
    emit.toClient('confirmCustomerInfo', obj, obj.siteKey);
  });

  socket.on("connectSuccessForClient", function (data) {
    var obj = JSON.parse(data);
    // sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
  });

  socket.on("connectSuccess", function (data) {
    var obj = JSON.parse(data);
    if ( !isset(sincloCore[obj.siteKey]) ) {
      sincloCore[obj.siteKey] = {};
    }
    if ( !isset(sincloCore[obj.siteKey][obj.tabId]) ) {
      sincloCore[obj.siteKey][obj.tabId] = {sessionId: null, subWindow: false};
    }
    if ('timeoutTimer' in sincloCore[obj.siteKey][obj.tabId]) {
      clearTimeout(sincloCore[obj.siteKey][obj.tabId].timeoutTimer);
      sincloCore[obj.siteKey][obj.tabId].timeoutTimer = null;
    }

    connectList[socket.id] = {siteKey: obj.siteKey, tabId: obj.tabId, userId: null};
    sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
    if ( obj.subWindow ) {
      sincloCore[obj.siteKey][obj.tabId].toTabId = obj.to;
      sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
      sincloCore[obj.siteKey][obj.tabId].subWindow = true;
      if ( !getSessionId(obj.siteKey, obj.tabId, 'responderId') ) {
        sincloCore[obj.siteKey][obj.tabId].responderId = getSessionId(obj.siteKey, obj.to, 'responderId');
      }
      obj.responderId = getSessionId(obj.siteKey, obj.to, 'responderId');
      if ( getSessionId(obj.siteKey, obj.to, 'syncSessionId') ) {
        sincloCore[obj.siteKey][obj.to].syncSessionId = socket.id; // 同期先配列に、セッションIDを格納
      }
    }
    else if ( !getSessionId(obj.siteKey, obj.tabId, 'parentTabId') ) {
      connectList[socket.id] = {siteKey: obj.siteKey, tabId: obj.tabId, userId: obj.userId};
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
      emit.toCompany('syncNewInfo', obj, obj.siteKey);
    }
  });
  // ウィジェットが生成されたことを企業側に通知する
  socket.on("syncReady", function(data){
    var obj = JSON.parse(data);
    emit.toCompany('syncNewInfo', obj, obj.siteKey);
  });
  // アクティブ状態を送る
  socket.on("sendTabInfo", function(d){
    var obj = JSON.parse(d);
    emit.toCompany('retTabInfo', d, obj.siteKey);
    // 画面同期中は同期フレーム本体に送る
    if ( ('connectToken' in obj) && isset(obj.connectToken) ) {
      emit.toUser('retTabInfo', obj, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
    }
  });

  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  socket.on("sendAccessInfo", function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);

    obj = getConnectInfo(obj);

    if ( getSessionId(obj.siteKey, obj.tabId, 'chat') ) {
      obj.chat = getSessionId(obj.siteKey, obj.tabId, 'chat');
    }

    // IPアドレスの取得
    if ( !(('ipAddress' in obj) && isset(obj.ipAddress)) ) {
      obj.ipAddress = getIp(socket);
    }

    // TODO ここを要求したユーザのみに送るようにする
    emit.toCompany("receiveAccessInfo", obj, obj.siteKey);
    if ( ('contract' in obj) && ('chat' in obj.contract) && obj.contract.chat === false) return false;
    chatApi.sendUnreadCnt("sendChatInfo", obj, false);
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
    var obj = JSON.parse(data);
    if ( !isset(activeOperator[obj.siteKey]) ) {
      activeOperator[obj.siteKey] = {};
    }
    // 在席中
    if ( obj.active ) {
      if ( !isset(activeOperator[obj.siteKey][obj.userId]) ) {
        activeOperator[obj.siteKey][obj.userId] = socket.id;
      }
    }
    // 退席中
    else {
      if ( isset(activeOperator[obj.siteKey][obj.userId]) ) {
        delete activeOperator[obj.siteKey][obj.userId];
      }
    }

    var keys = Object.keys(activeOperator[obj.siteKey]);
    emit.toCompany('activeOpCnt', {
      siteKey: obj.siteKey,
      userId: obj.userId,
      active: obj.active,
      count: keys.length
    }, obj.siteKey);
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
    chat.messageType = chatApi.cnst.observeType.auto;
    chat.created = new Date();
    chat.sort = fullDateTime(chat.created);
    emit.toCompany('resAutoChatMessage', chat, chat.siteKey);
    emit.toMine('resAutoChatMessage', chat, socket);
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
      var created = new Date(obj.messages[i].created);
      obj.messages[i].messageType = chatApi.cnst.observeType.auto;
      setList[fullDateTime(Date.parse(created))] = obj.messages[i];
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
    if ( sincloCore[obj.siteKey][obj.tabId] === null ) {
      emit.toMine("chatStartResult", {ret: false, siteKey: obj.siteKey, userId: sincloCore[obj.siteKey][obj.tabId].chat}, socket);
    }
    else {
      // var sendData = {ret: true, messageType: type, hide:false, created: now};
      var sendData = {ret: true, messageType: type, tabId: obj.tabId, siteKey: obj.siteKey, userId: obj.userId, hide:false, created: now};
      emit.toCompany("chatStartResult", sendData, obj.siteKey);

      sincloCore[obj.siteKey][obj.tabId].chat = obj.userId;
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
          sendData.hide = true;
        }
      }

      pool.query('SELECT mu.id, mu.display_name, wid.style_settings FROM m_users as mu LEFT JOIN m_widget_settings AS wid ON ( wid.m_companies_id = mu.m_companies_id ) WHERE mu.id = ? AND mu.del_flg != 1 AND wid.del_flg != 1 AND wid.m_companies_id = ?', [obj.userId, companyList[obj.siteKey]], function(err, rows){
        sendData.userName = "オペレーター";
        if ( rows && rows[0] ) {
          var settings = JSON.parse(rows[0].style_settings);
          // 表示名をウィジェットで表示する場合
          var userName = rows[0].display_name;
          if ( isset(settings.showName) && Number(settings.showName) === 1 ) {
            sendData.userName = userName;
          }

          emit.toUser("chatStartResult", sendData, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
          c_connectList[obj.siteKey][obj.tabId][now] = {messageType: type, type:"start", userName: userName, userId: obj.userId};

          // DBに書き込み
          var ids = obj.tabId.split("_");
          var insertData = {
            siteKey: obj.siteKey,
            tabId: obj.tabId,
            t_histories_id: getSessionId(obj.siteKey, obj.tabId, 'historyId'),
            userId: (ids.length > 1) ? ids[0] : "",
            mUserId: obj.userId,
            chatMessage: "入室",
            messageType: type,
            created: date
          };
          chatApi.notifyCommit(insertData);

        }
      });
    }
  });

  // チャット終了
  socket.on("chatEnd", function(d){
    var obj = JSON.parse(d), date = new Date(), now = fullDateTime(), type = chatApi.cnst.observeType.end;
    var keys = Object.keys(c_connectList[obj.siteKey][obj.tabId]);
    var userName = c_connectList[obj.siteKey][obj.tabId][keys[keys.length-1]].user;
    c_connectList[obj.siteKey][obj.tabId][now] = {type:"end", userName: userName, userId: obj.userId, messageType: type};
    if ( isset(sincloCore[obj.siteKey]) && isset(sincloCore[obj.siteKey][obj.tabId].chat) ) {
      sincloCore[obj.siteKey][obj.tabId].chat = null;
      sincloCore[obj.siteKey][obj.tabId].chatSessionId = null;
      emit.toCompany("chatEndResult", {ret: true, messageType: type, created: now, tabId: obj.tabId, siteKey: obj.siteKey, userId: obj.userId}, obj.siteKey);
      emit.toUser("chatEndResult", {ret: true, messageType: type}, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));

      // DBに書き込み
      var ids = obj.tabId.split("_");
      var insertData = {
        siteKey: obj.siteKey,
        tabId: obj.tabId,
        t_histories_id: getSessionId(obj.siteKey, obj.tabId, 'historyId'),
        userId: (ids.length > 1) ? ids[0] : "",
        mUserId: obj.userId,
        chatMessage: "退室",
        messageType: type,
        created: date
      };
      chatApi.notifyCommit(insertData);
    }
  });

  // 新着チャット
  socket.on("sendChat", function(d){
    var obj = JSON.parse(d);
    chatApi.set(obj);
  });

  // オートチャット
  socket.on("sendAutoChat", function(d){
    var obj = JSON.parse(d);
    var loop = function(err, rows){
      if ( !err && (rows && rows[0]) ) {
          var activity = JSON.parse(rows[0].activity);
          var ret = {
              siteKey: obj.siteKey,
              tabId: obj.tabId,
              userId: obj.userId,
              mUserId: null,
              chatMessage: activity.message,
              messageType: 3,
              created: rows[0].inputed
          };
          chatApi.set(ret);
      }
    };
    for (var i = 0; obj.messageList.length > i; i++) {
        var message = obj.messageList[i];
        pool.query("SELECT *, ? as inputed FROM t_auto_messages WHERE id = ?  AND m_companies_id = ? AND del_flg = 0 AND active_flg = 0 AND action_type = 1", [message.created, message.chatId, companyList[obj.siteKey]], loop);
    }

  });

  // 既読操作
  socket.on("isReadChatMessage", function(d){
    var obj = JSON.parse(d);
    // TODO 履歴IDチェック
    if ( isset(sincloCore[obj.siteKey][obj.tabId].historyId) ) {
      obj.historyId = sincloCore[obj.siteKey][obj.tabId].historyId;
      pool.query("UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND message_type = 1 AND id <= ?;",
        [obj.historyId, obj.chatId], function(err, ret, fields){
          chatApi.sendUnreadCnt('retReadChatMessage', obj, true);
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
      emit.toUser('receiveTypeCond', obj, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
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

  socket.on('settingReload', function (data) {
    var obj = JSON.parse(data), targetKey;
    if ( isset(obj.type) && String(obj.siteKey) === "master") {
      switch (obj.type) {
        case 1: // get new company ( sample: socket.emit('settingReload', JSON.stringify({type:1, siteKey: "master"})); )
          console.log('before', companyList);
          getCompanyList();
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
        case 4: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:4, siteKey: "master"})); )
          if ( !((socket.id in company.user) && ('siteKey' in company.user[socket.id])) ) return false;
          targetKey = company.user[socket.id].siteKey;
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
        default:
      }
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
    sessionId = ( obj.to === 'company' ) ? doc_connectList[obj.tabId].company : doc_connectList[obj.tabId].customer;
    emit.toUser('docSendAction', d, sessionId);
  });

  // ユーザーのアウトを感知
  socket.on('disconnect', function () {
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
      }, 3000);
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
          emit.toUser('receiveTypeCond', {status: false, type: 1, tabId: tabId, message: ""}, tab.sessionId);
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

          // 新しいユーザーの人数を送る
          var cnt = Object.keys(company.info[userInfo.siteKey]);
          emit.toCompany('outCompanyUser', {siteKey: userInfo.siteKey, userCnt: cnt.length}, userInfo.siteKey);

          // 受付中オペレータの情報削除
          if ( (userInfo.siteKey in activeOperator) && (userInfo.userId in activeOperator[userInfo.siteKey]) ) {
            delete activeOperator[userInfo.siteKey][userInfo.userId];
          }
          var opKeys = [];
          if ( userInfo.siteKey in activeOperator ) {
            opKeys = Object.keys(activeOperator[userInfo.siteKey]);
          }
          emit.toCompany('activeOpCnt', {siteKey: userInfo.siteKey,count: opKeys.length}, userInfo.siteKey);

        }
      }, 5000);
      return false;
    }
    // タグ入りページからのアクセスの場合
    if ( !(socket.id in connectList) ) return false;
    info = connectList[socket.id];
    if (getSessionId(info.siteKey, info.tabId, 'sessionId')) {
      var core = sincloCore[info.siteKey][info.tabId];
      var siteId = companyList[info.siteKey];
      var timeout = ('connectToken' in core) ? 10000 : 5000;
      // 消費者側の履歴更新
      if ( !('subWindow' in core) || ('subWindow' in core) && !core.subWindow && !core.shareWindowFlg ) {
        // 履歴の更新
        pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [siteId, info.tabId, info.userId], function(err, rows){
          if ( isset(rows) && isset(rows[0]) ) {
            var now = formatDateParse();
            timeUpdate(rows[0].id, {}, now);
          }
        });

      }

      if ( 'timeoutTimer' in sincloCore[info.siteKey][info.tabId] ) {
        clearTimeout(sincloCore[info.siteKey][info.tabId].timeoutTimer);
      }

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
        else {
          // 消費者側
          if ( 'syncFrameSessionId' in core ) {
            emit.toUser('unsetUser', {siteKey: info.siteKey, tabId: info.tabId}, core.syncFrameSessionId);
            syncStopCtrl(info.siteKey, info.tabId);
          }

          if ( ('syncSessionId' in core) && (core.syncSessionId in connectList) && ('tabId' in connectList[core.syncSessionId]) ) {
            var tabId = connectList[core.syncSessionId].tabId;
            if ( ('parentTabId' in core) && getSessionId(info.siteKey, tabId, 'subWindow') ) {
              sincloCore[info.siteKey][tabId].connectTab = core.parentTabId;
            }
          }
          emit.toCompany('unsetUser', {siteKey: info.siteKey, tabId: info.tabId}, info.siteKey);
        }

      }, timeout);
      // connectListから削除
      delete connectList[socket.id];
    }
  });
});
