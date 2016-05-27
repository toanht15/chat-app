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
var io = require('socket.io')(9090),
    activeOperator = {}, // 待機中オペレーター
    sincloCore = {}, // socketIDの管理
    connectList = {}, // socketIDをキーとした管理
    emit = {}; // Emit

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
}

function timeUpdate(history, obj, time){
  var historyId = history.id;
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

function formatDateParse(parse){
  var d = ( isset(parse) ) ? new Date(Number(parse)) : new Date();
  return d.getFullYear() + "/" + _numPad(d.getMonth() + 1) + "/" + _numPad(d.getDate()) + " " + _numPad(d.getHours()) + ":" + _numPad(d.getMinutes()) + ":" + _numPad(d.getSeconds());
}

function syncStopCtrl(siteKey, tabId){
  var keys = ['connectToken', 'syncSessionId', 'syncHostSessionId', 'syncFrameSessionId'];

  for (var i; keys.length > i; i++) {
    if ( getSessionId(siteKey, tabId, keys[i]) ) {
      delete sincloCore[siteKey][tabId][keys[i]];
    }
  }
}

var db = {
  addHistory: function(obj) {
    if ( isset(obj.tabId) && isset(obj.siteKey) ) {
      if ( !isset(companyList[obj.siteKey]) || obj.subWindow ) return false;
      var siteId = companyList[obj.siteKey];
      pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [siteId, obj.tabId, obj.userId], function(err, rows){
        var now = formatDateParse();
        var insertStayData = {
          title: obj.title,
          url: obj.url,
          stay_time: "",
          created: now,
          modified: now
        };
        if ( !(obj.tabId in sincloCore[obj.siteKey]) ) {
          sincloCore[obj.siteKey][obj.tabId] = {};
        }

        if ( isset(rows) && isset(rows[0]) ) {
          sincloCore[obj.siteKey][obj.tabId]['historyId'] = rows[0].id;
          timeUpdate(rows[0], obj, now);
          emit.toMine('setHistoryId', obj);
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
              if ( isset(error) ) return false;
              insertStayData['t_histories_id'] = results.insertId;
              sincloCore[obj.siteKey][obj.tabId].historyId = results.insertId;
              emit.toMine('setHistoryId', obj);
              pool.query("INSERT INTO t_history_stay_logs SET ?", insertStayData,
                function (error,results,fields){
                }
              );
            }
          );
        };
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

  // emit用
  emit = {
    roomKey: {
      client: 'cl001',
      company: 'cm001'
    },
    _convert: function(d){
      if ( typeof(d) === "object" ) {
        return JSON.stringify(d);
      }
      else {
        return d;
      }
    },
    toMine: function(ev, d){ // 送り主に返信
      var obj = this._convert(d);
      return socket.emit(ev, obj);
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

  // チャット用
  var chatApi = {
    set: function(d){ // メッセージが渡されてきたとき
      // // 履歴idかメッセージがない
      if ( !getSessionId(d.siteKey, d.tabId, 'historyId') || !isset(d.chatMessage) ) {
        // エラーを渡す
        return emit.toUser('sendChatResult', {ret: false, messageType: d.messageType, tabId: d.tabId, siteKey: d.siteKey}, d.siteKey);
      }
      // チャットidがある
      else {
        // DBへ書き込む
        this.commit(d);
      }

    },
    get: function(obj, sId){ // 最初にデータを取得するとき
      pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [companyList[obj.siteKey], obj.tabId, obj.userId], function(err, rows){
        var chatData = {historyId: null, messages: []};
        if ( isset(rows) && isset(rows[0]) ) {
          chatData.historyId = rows[0].id;
          pool.query('SELECT id, message, message_type as messageType FROM t_history_chat_logs WHERE t_histories_id = ? AND message_type != 3;', [chatData.historyId], function(err, rows){
            chatData.messages = ( isset(rows) ) ? rows : [];
            obj.chat = chatData;
            emit.toUser('chatMessageData', obj, sId);
          });
        }
        else {
          obj.chat = chatData;
          emit.toUser('chatMessageData', obj, sId);
        }
      });
    },
    commit: function(d){ // DBに書き込むとき

      var insertData = {
        t_histories_id: sincloCore[d.siteKey][d.tabId].historyId,
        visitors_id: d.userId,
        m_users_id: d.mUserId,
        message: d.chatMessage,
        message_type: d.messageType,
        created: formatDateParse()
      };

      pool.query('INSERT INTO t_history_chat_logs SET ?', insertData, function(error,results,fields){
        if ( !isset(error) ) {
          if ( !isset(sincloCore[d.siteKey][d.tabId]['sessionId'])) return false;
          var sId = sincloCore[d.siteKey][d.tabId].sessionId;
          // 書き込みが成功したら顧客側に結果を返す
          emit.toUser('sendChatResult', {tabId: d.tabId, chatId: results.insertId, messageType: d.messageType, ret: true, chatMessage: d.chatMessage, siteKey: d.siteKey}, sId);

          // 書き込みが成功したら企業側に結果を返す
          emit.toCompany('sendChatResult', {tabId: d.tabId, chatId: results.insertId, messageType: d.messageType, ret: true, chatMessage: d.chatMessage, siteKey: d.siteKey}, d.siteKey);

        }
        else {
          // 書き込みが失敗したらエラーを渡す
          return emit.toUser('sendChatResult', {tabId: d.tabId, messageType: d.messageType, ret: false, siteKey: d.siteKey}, d.siteKey);
        }
      });

    },
    sendUnreadCnt: function(evName, obj){
      var sql, ret = {}, tabId = obj.tabId, siteId = companyList[obj.siteKey];

      sql  = " SELECT chat.id AS chatId, his.visitors_id, his.tab_id, chat.message FROM t_histories AS his";
      sql += " INNER JOIN t_history_chat_logs AS chat ON ( his.id = chat.t_histories_id )";
      sql += " WHERE his.tab_id = ? AND his.m_companies_id = ? AND chat.message_type = 1";
      sql += "   AND chat.m_users_id IS NULL AND chat.message_read_flg != 1 ORDER BY chat.id desc";

      pool.query(sql, [tabId, siteId], function(err, rows){
        if ( !isset(err) && (rows.length > 0 && isset(rows[0].chatId))) {
          ret.chatUnreadId = rows[0].chatId;
          ret.chatUnreadCnt = rows.length;
          emit.toCompny(evName, obj, obj.siteKey);
        }
        else {
          if ( isset(err) ) {
          }
          ret.chatUnreadId = null;
          ret.chatUnreadCnt = 0;
          emit.toCompany(evName, obj, obj.siteKey);
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

      if ( isset(socket.handshake.headers['x-forwarded-for']) ) {
        send.ipAddress = socket.handshake.headers['x-forwarded-for'];
      }
      else {
        send.ipAddress = "0.0.0.0";
      }

    }
    // 企業キーが取得できなければスルー
    if ( res.siteKey ) {

      if ( res.type === 'admin' ) {
        socket.join(res.siteKey + emit.roomKey.company);
        emit.toClient('getAccessInfo', send, res.siteKey);
      }
      else {
        var cnt = 0;
        if ( isset(activeOperator[res.siteKey]) ) {
          var key = Object.keys(activeOperator[res.siteKey]);
          cnt = key.length;
        }
        send['activeOperatorCnt'] = cnt
        socket.join(res.siteKey + emit.roomKey.client);
        emit.toMine('accessInfo', send);
      }

    }
  });

  socket.on("customerInfo", function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);
    emit.toCompany("sendCustomerInfo", obj, obj.siteKey);
    chatApi.sendUnreadCnt("sendChatInfo", obj);
  });

  socket.on("getCustomerInfo", function(data) {
    var obj = JSON.parse(data);
    emit.toClient('confirmCustomerInfo', obj, obj.siteKey);
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
      clearTimeout(sincloCore[obj.siteKey][obj.tabId]['timeoutTimer']);
      sincloCore[obj.siteKey][obj.tabId]['timeoutTimer'] = null;
    }

    connectList[socket.id] = {siteKey: obj.siteKey, tabId: obj.tabId, userId: null};
    sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;
    if ( obj.subWindow ) {
      sincloCore[obj.siteKey][obj.tabId]['toTabId'] = obj.to;
      sincloCore[obj.siteKey][obj.tabId].subWindow = true;
      obj['responderId'] = getSessionId(obj.siteKey, obj.to, 'responderId');
      if ( getSessionId(obj.siteKey, obj.to, 'syncSessionId') ) {
        sincloCore[obj.siteKey][obj.to]['syncSessionId'] = socket.id; // 同期先配列に、セッションIDを格納
      }
    }
    else {
      connectList[socket.id] = {siteKey: obj.siteKey, tabId: obj.tabId, userId: obj.userId};
      // 履歴作成
      db.addHistory(obj);
      emit.toCompany('syncNewInfo', obj, obj.siteKey);
    }
  });
  // ウィジェットが生成されたことを企業側に通知する
  socket.on("syncReady", function(data){
    var obj = JSON.parse(data);
    emit.toCompany('syncNewInfo', obj, obj.siteKey);
  });

  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  socket.on("sendAccessInfo", function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);
    if ( getSessionId(obj.siteKey, obj.tabId, 'responderId') ) {
      obj.responderId = getSessionId(obj.siteKey, obj.tabId, 'responderId');
    }
    if ( getSessionId(obj.siteKey, obj.tabId, 'chat') ) {
      obj.chat = getSessionId(obj.siteKey, obj.tabId, 'chat');
    }
    // TODO ここを要求したユーザのみに送るようにする
    emit.toCompany("receiveAccessInfo", obj, obj.siteKey);
    chatApi.sendUnreadCnt("sendChatInfo", obj);
  });
  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  // [管理]モニタリング開始
  socket.on('requestWindowSync', function (data) {
    var obj = JSON.parse(data);
    // 同形ウィンドウを作成するための情報取得依頼
    if ( !getSessionId(obj.siteKey, obj.tabId, 'sessionId') ) return false;
    sincloCore[obj.siteKey][obj.tabId].connectToken = obj.connectToken;
    sincloCore[obj.siteKey][obj.tabId].syncSessionId = null;
    sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
    emit.toUser('getWindowInfo', data, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));

  });
  // 同形ウィンドウを作成するための情報受け取り
  socket.on('sendWindowInfo', function (data) {
    var obj = JSON.parse(data);
    // 同形ウィンドウを作成するための情報渡し
    emit.toUser('windowSyncInfo', data, getSessionId(obj.siteKey, obj.tabId, 'syncHostSessionId'));
  });
  // iframe用（接続直後と企業側リロード時）
  socket.on('connectFrame', function (data) {
    var obj = JSON.parse(data);
    if ( obj.siteKey ) {
      socket.join(obj.siteKey + emit.roomKey.client);
    }
    if ( (obj.tabId in sincloCore[obj.siteKey]) ) {
      sincloCore[obj.siteKey][obj.tabId]['syncFrameSessionId'] = socket.id; // フレームのセッションID
      if ( 'responderId' in obj ) {
        sincloCore[obj.siteKey][obj.tabId]['responderId'] = obj.responderId; // 対応ユーザーID
      }
      if ( getSessionId(obj.siteKey, obj.tabId, 'connectToken') ) {
        obj.connectToken = getSessionId(obj.siteKey, obj.tabId, 'connectToken'); // 接続トークンを企業側へ
      }


      emit.toCompany('syncNewInfo', obj, obj.siteKey);
    }
    else {
      emit.toMine('syncStop', data);
    }
  });

// FIX ME !!!!!!!
  // 継続接続(両用)
  socket.on('connectContinue', function (data) {
    var obj = JSON.parse(data);

    // 企業キーが取得できなければスルー
    if ( obj.siteKey ) {
      socket.join(obj.siteKey + emit.roomKey.client);
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
      socket.join(obj.siteKey + emit.roomKey.client);
      data.siteKey = obj.siteKey;
    }
    if ( obj.accessType !== 1 ) {
      tabId = obj.to; // host
      if ( (obj.to in sincloCore[obj.siteKey]) ) {
        sincloCore[obj.siteKey][obj.to]['syncSessionId'] = socket.id;
      }
    }
    else {
      tabId = obj.tabId; // guest
    }

// FIX ME !!! | Change to 'toMine'
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
    // マウス・スクロールバーの位置監視
    emit.toUser('syncEvStart', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
    emit.toUser('syncEvStart', data, getSessionId(obj.siteKey, obj.to, 'syncSessionId'));
    emit.toUser('syncEvStart', data, sincloCore[obj.siteKey][obj.to].sessionId);
    emit.toUser('syncEvStart', data, sincloCore[obj.siteKey][obj.to].syncSessionId);
  });

  socket.on('requestSyncStop', function (data) {
    var obj = JSON.parse(data);
    if ( isset(obj.connectToken) ) {
      emit.toUser('syncStop', data, getSessionId(obj.siteKey, obj.tabId, 'syncFrameSessionId'));
      syncStopCtrl(obj.siteKey, obj.tabId);
    }
    else {
      emit.toCompany('unsetUser', data, obj.siteKey);
    }
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
      }
      if ( i === 5 ) {
        clearInterval(timer);
        emit.toUser('syncStop', {message: "接続できませんでした。"}, socket.id);
      }
      i++;
    }, 1000);
  });

  socket.on('sendConfirmConnect', function (data) {
    var obj = JSON.parse(data);
    if ( obj.accessType === 1 ) { // to host
      emit.toUser('getConnectInfo', data, getSessionId(obj.siteKey, obj.tabId, 'syncSessionId'));
    }
    else {
      emit.toUser('getConnectInfo', data, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
    }
  });

  socket.on('sendConnectInfo', function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);
    emit.toUser('receiveConnect', data, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
  });

  socket.on('sendOperatorStatus', function(data){
    var obj = JSON.parse(data);
    if ( !isset(activeOperator[obj.siteKey]) ) {
      activeOperator[obj.siteKey] = {};
    }
    // 在席中
    if ( obj.active ) {
      if ( !isset(activeOperator[obj.siteKey][obj.userId]) ) {
        activeOperator[obj.siteKey][obj.userId] = true;
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
      count: keys.length
    }, obj.siteKey);
  })

  socket.on('reqUrlChecker', function (data){
    var obj = JSON.parse(data);
    emit.toUser('resUrlChecker', data, getSessionId(obj.siteKey, obj.to, 'sessionId'));
  });

  // -----------------------------------------------------------------------
  //  チャット関連
  // -----------------------------------------------------------------------

  // チャットデータ取得
  socket.on("getChatMessage", function(d){
    var obj = JSON.parse(d);
    chatApi.get(obj, socket.id);
  });

  // チャット開始
  socket.on("chatStart", function(d){
    var obj = JSON.parse(d);
    if ( sincloCore[obj.siteKey][obj.tabId] === null ) {
      emit.toUser("chatStartResult", {ret: false, siteKey: obj.siteKey, userId: sincloCore[obj.siteKey][obj.tabId].chat}, socket.id);
    }
    else {
      emit.toCompany("chatStartResult", {ret: true, tabId: obj.tabId, siteKey: obj.siteKey, userId: obj.userId}, obj.siteKey);
      sincloCore[obj.siteKey][obj.tabId].chat = obj.userId;
    }
  });

  // チャット終了
  socket.on("chatEnd", function(d){
    var obj = JSON.parse(d);
    if ( isset(sincloCore[obj.siteKey]) && isset(sincloCore[obj.siteKey][obj.tabId].chat) ) {
      sincloCore[obj.siteKey][obj.tabId].chat = null;
      emit.toCompany("chatEndResult", {ret: true, tabId: obj.tabId, siteKey: obj.siteKey, userId: obj.userId}, obj.siteKey);
    }
  });

  // 新着チャット
  socket.on("sendChat", function(d){
    var obj = JSON.parse(d);
    chatApi.set(obj);
  });

  // 新着チャット
  socket.on("clientSendChat", function(d){
    var obj = JSON.parse(d);
    chatApi.set(obj);
  });

  // 既読操作
  socket.on("isReadChatMessage", function(d){
    var obj = JSON.parse(d);
    if ( isset(sincloCore[obj.siteKey][obj.tabId].historyId) ) {
      obj.historyId = sincloCore[obj.siteKey][obj.tabId].historyId;
      pool.query("UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND id <= ?;",
        [obj.historyId, obj.chatId], function(err, ret, fields){
          chatApi.sendUnreadCnt('retReadChatMessage', obj);
        }
      );
    }
  });

  socket.on('userOut', function (data) {
    var obj = JSON.parse(data);
    if ( !isset(obj.connectToken) ) {
      emit.toCompany('unsetUser', data, obj.siteKey);
    }
  });

  socket.on('settingReload', function (data) {
    var obj = JSON.parse(data);
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
        case 3: // del company ( sample: socket.emit('log', JSON.stringify({type:3, targetKey: "demo", siteKey: "master"})); )
          console.log('connectList', connectList);
          if ( 'targetKey' in obj ) {
            console.log("--------------------------------" + obj.targetKey + "--------------------------------");
            console.log('sincloCore', sincloCore[obj.targetKey]);
            console.log("-------------------------------------------------------------------------");
          }
          else {
            var keys = Object.keys(sincloCore);
            for( var i = 0; i < keys.length; i++ ){
              var targetKey = keys[i];
              console.log("--------------------------------" + targetKey + "--------------------------------");
              console.log('sincloCore', sincloCore[targetKey]);
            }
            console.log("-------------------------------------------------------------------------");
          }
          break;
        default:
      }
    }
  });

  // ユーザーのアウトを感知
  socket.on('disconnect', function () {
    // タグ入りページからのアクセスの場合
    if ( !(socket.id in connectList) ) return false;
    var info = connectList[socket.id];

    if (getSessionId(info.siteKey, info.tabId, 'sessionId')) {
      var core = sincloCore[info.siteKey][info.tabId];
      var siteId = companyList[info.siteKey];
      var timeout = ('connectToken' in core) ? 10000 : 5000;
      // 消費者側の履歴更新
      if ( !('subWindow' in core) || ('subWindow' in core) && !core.subWindow ) {
        // 履歴の更新
        pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [siteId, info.tabId, info.userId], function(err, rows){
          if ( isset(rows) && isset(rows[0]) ) {
            var now = formatDateParse();
            timeUpdate(rows[0], {}, now);
          }
        });
      }

      if ( 'timeoutTimer' in sincloCore[info.siteKey][info.tabId] ) {
        clearTimeout(sincloCore[info.siteKey][info.tabId]['timeoutTimer']);
      }

      sincloCore[info.siteKey][info.tabId]['timeoutTimer'] = setTimeout(function(){
        var historyId = sincloCore[info.siteKey][info.tabId].historyId;
        // sincloCoreから情報削除
        delete sincloCore[info.siteKey][info.tabId];
        if ( core.subWindow ) {
          // 企業側
          if ( 'toTabId' in core ) {
            emit.toUser('syncStop', {siteKey: info.siteKey, tabId: core.toTabId, connectToken: core.connectToken}, getSessionId(info.siteKey, core.toTabId, 'sessionId'));
            emit.toCompany('syncStop', {siteKey: info.siteKey, tabId: core.toTabId}, info.siteKey);
          }
          syncStopCtrl(info.siteKey, info.tabId);
        }
        else {
          // チャット使用状況を確認
          pool.query('SELECT * FROM t_history_chat_logs WHERE message_type != 3 AND t_histories_id = ?', [historyId], function(err, rows){
          	// 未使用の場合
            if (!(isset(rows) && isset(rows[0]))) {
              // 自動送信履歴を削除
              pool.query('DELETE FROM t_history_chat_logs WHERE message_type = 3 AND t_histories_id = ?', [historyId]);
            }
          });

          // 消費者側
          if ( 'syncFrameSessionId' in core ) {
            emit.toUser('unsetUser', {siteKey: info.siteKey, tabId: info.tabId}, core.syncFrameSessionId);
            syncStopCtrl(info.siteKey, info.tabId);
          }
          emit.toCompany('unsetUser', {siteKey: info.siteKey, tabId: info.tabId}, info.siteKey);
        }

      }, timeout);
      // connectListから削除
      delete connectList[socket.id];
    }
  });
});
