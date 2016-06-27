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
    c_connectList = {}, // socketIDをキーとしたチャット管理
    vc_connectList = {}, // tabId: socketID
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
}

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

function objectSort(object) {
  //戻り値用新オブジェクト生成
  var sorted = {};
  //キーだけ格納し，ソートするための配列生成
  var array = [];
  //for in文を使用してオブジェクトのキーだけ配列に格納
  for (key in object) {
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

// emit用
var emit = {
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
          sincloCore[obj.siteKey][obj.tabId]['historyId'] = rows[0].id;
          timeUpdate(rows[0].id, obj, now);
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
              emit.toMine('setHistoryId', obj, s);
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

  // チャット用
  var chatApi = {
    set: function(d){ // メッセージが渡されてきたとき
      // 履歴idかメッセージがない
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
    get: function(obj){ // 最初にデータを取得するとき
        var chatData = {historyId: null, messages: []};
        var historyId = getSessionId(obj.siteKey, obj.tabId, 'historyId');
        if ( historyId ) {
            chatData.historyId = historyId;
            pool.query('SELECT id, message, message_type as messageType, m_users_id as userId,  message_read_flg as messageReadFlg, created FROM t_history_chat_logs WHERE t_histories_id = ? ORDER BY created;', [chatData.historyId], function(err, rows){
              var messages = ( isset(rows) ) ? rows : [];
              var setList = {};
              if ((obj.siteKey in c_connectList) && (obj.tabId in c_connectList[obj.siteKey])) {
                setList = c_connectList[obj.siteKey][obj.tabId];
              }
              for (var i = 0; i < messages.length; i++) {
                var date = Date.parse(messages[i].created);
                setList[date + "_" + i] = messages[i];
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
            insertData['t_history_stay_logs_id'] = rows[0].id;
          }
          insertData['created'] = (('created' in d)) ? d.created : formatDateParse();
          // オートメッセージの場合は既読
          if (Number(insertData['message_type'] === 3) ) {
              insertData['message_read_flg'] = 1;
          }

          pool.query('INSERT INTO t_history_chat_logs SET ?', insertData, function(error,results,fields){
            if ( !isset(error) ) {
              if ( !isset(sincloCore[d.siteKey][d.tabId]['sessionId'])) return false;
              var sId = sincloCore[d.siteKey][d.tabId].sessionId;
              // 書き込みが成功したら顧客側に結果を返す
              emit.toUser('sendChatResult', {tabId: d.tabId, chatId: results.insertId, messageType: d.messageType, ret: true, chatMessage: d.chatMessage, siteKey: d.siteKey}, sId);
              if (Number(insertData['message_type']) === 3) return false;
              // 書き込みが成功したら企業側に結果を返す
              emit.toCompany('sendChatResult', {tabId: d.tabId, chatId: results.insertId, userId: insertData.m_users_id, messageType: d.messageType, ret: true, chatMessage: d.chatMessage, siteKey: d.siteKey}, d.siteKey);
            }
            else {
              // 書き込みが失敗したらエラーを渡す
              return emit.toUser('sendChatResult', {tabId: d.tabId, messageType: d.messageType, ret: false, siteKey: d.siteKey}, d.siteKey);
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
        if ( 'userId' in data ) {
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
            var cnt = Object.keys(company.info[res.siteKey]);

            if ( ('status' in data) && String(data.status) === '1' ) {
              activeOperator[res.siteKey][data.userId] = data.status;
            }
            else {
              data.status =  0;
            }

            var opKeys = [];
            if ( res.siteKey in activeOperator ) {
              opKeys = Object.keys(activeOperator[res.siteKey]);
            }

            data.userCnt = cnt.length;
            data.onlineUserCnt = opKeys.length;
        }
        // 消費者にアクセス情報要求
        emit.toClient('getAccessInfo', send, res.siteKey);
        // 企業側に情報提供
        emit.toCompany('getAccessInfo', data, res.siteKey);
      }
      else {
        var cnt = 0;
        if ( isset(activeOperator[res.siteKey]) ) {
          var key = Object.keys(activeOperator[res.siteKey]);
          cnt = key.length;
        }
        send['activeOperatorCnt'] = cnt
        socket.join(res.siteKey + emit.roomKey.client);
        emit.toMine('accessInfo', send, socket);
      }

    }
  });

  socket.on("customerInfo", function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);
    if ( getSessionId(obj.siteKey, obj.tabId, 'chat') ) {
      obj.chat = getSessionId(obj.siteKey, obj.tabId, 'chat');
    }

    emit.toCompany("sendCustomerInfo", obj, obj.siteKey);
    chatApi.sendUnreadCnt("sendChatInfo", obj, false);
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
      db.addHistory(obj, socket);
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
    chatApi.sendUnreadCnt("sendChatInfo", obj, false);
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
      emit.toMine('syncStop', data, socket);
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
      userId: obj.userId,
      active: obj.active,
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

  // 一括：チャットデータ取得
  socket.on("getChatMessage", function(d){
    var obj = JSON.parse(d);
    chatApi.get(obj);
  });

  // 都度：チャットデータ取得(オートメッセージのみ)
  socket.on("sendAutoChatMessage", function(d){
    var obj = JSON.parse(d);
    emit.toCompany('resAutoChatMessage', obj, obj.siteKey);
  });

  // 一括：チャットデータ取得(オートメッセージのみ)
  socket.on("getAutoChatMessages", function(d){
    var obj = JSON.parse(d);
    if (!getSessionId(obj.siteKey, obj.tabId, 'sessionId')) return false;
    var sId = getSessionId(obj.siteKey, obj.tabId, 'sessionId');
    obj.sendTo = socket.id;
    emit.toUser('sendReqAutoChatMessages', obj, sId);
  });

  // 一括：チャットデータ取得(オートメッセージのみ)
  socket.on("sendAutoChatMessages", function(d){
    var obj = JSON.parse(d);

    var setList = {};
    for (var i = 0; i < obj.messages.length; i++) {
      var date = Date.parse(obj.messages[i].created);
      setList[date + "_" + i] = obj.messages[i];
    }
    var ret = {};
        ret['messages'] = objectSort(setList);
        ret['chatToken'] = obj['chatToken'];
        ret['tabId'] = obj['tabId'];
        ret['historyId'] = getSessionId(obj.siteKey, obj.tabId, 'historyId');
    emit.toUser('resAutoChatMessages', ret, obj.sendTo);
  });

  // チャット開始
  socket.on("chatStart", function(d){
    var obj = JSON.parse(d), now = new Date();
    if ( sincloCore[obj.siteKey][obj.tabId] === null ) {
      emit.toMine("chatStartResult", {ret: false, siteKey: obj.siteKey, userId: sincloCore[obj.siteKey][obj.tabId].chat}, socket);
    }
    else {
      emit.toCompany("chatStartResult", {ret: true, tabId: obj.tabId, siteKey: obj.siteKey, userId: obj.userId}, obj.siteKey);
      sincloCore[obj.siteKey][obj.tabId]['chat'] = obj.userId;
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
        if ( c_connectList[obj.siteKey][obj.tabId][keys[keys.length - 1]] === "start" ) {
          return false;
        }
      }
      emit.toUser("chatStartResult", {ret: true}, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
      c_connectList[obj.siteKey][obj.tabId][Date.parse(now)] = "start";
    }
  });

  // チャット終了
  socket.on("chatEnd", function(d){
    var obj = JSON.parse(d), now = new Date();
    c_connectList[obj.siteKey][obj.tabId][Date.parse(now)] = "end";
    if ( isset(sincloCore[obj.siteKey]) && isset(sincloCore[obj.siteKey][obj.tabId].chat) ) {
      sincloCore[obj.siteKey][obj.tabId].chat = null;
      emit.toCompany("chatEndResult", {ret: true, tabId: obj.tabId, siteKey: obj.siteKey, userId: obj.userId}, obj.siteKey);
      emit.toUser("chatEndResult", {ret: true}, getSessionId(obj.siteKey, obj.tabId, 'sessionId'));
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
    for (var i = 0; obj.messageList.length > i; i++) {
        var message = obj.messageList[i];
        pool.query("SELECT * FROM t_auto_messages WHERE id = ?  AND m_companies_id = ? AND del_flg = 0 AND active_flg = 0 AND action_type = 1", [message.chatId, companyList[obj.siteKey]], function(err, rows){
            if ( !err && (rows && rows[0]) ) {
                var activity = JSON.parse(rows[0].activity);
                var ret = {
                    siteKey: obj.siteKey,
                    tabId: obj.tabId,
                    userId: obj.userId,
                    mUserId: null,
                    chatMessage: activity.message,
                    messageType: 3,
                    created: message.created
                };

                chatApi.set(ret);
            }
        });
    }

  });

  // 既読操作
  socket.on("isReadChatMessage", function(d){
    var obj = JSON.parse(d);
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
    if ( isset(sincloCore[obj.siteKey][obj.tabId].historyId) ) {
      obj.historyId = sincloCore[obj.siteKey][obj.tabId].historyId;
      pool.query("UPDATE t_history_chat_logs SET message_read_flg = 1 WHERE t_histories_id = ? AND message_type != 1;",
        [obj.historyId], function(err, ret, fields){
        }
      );
    }
  });

  // -----------------------------------------------------------------------
  // ビデオチャット関連
  // ビデオチャットで利用している各値はプレフィックス（vc_）をつけている。
  // -----------------------------------------------------------------------
  socket.on('confirmVideochatStart', function (data) {
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
    vc_connectList[obj.from] = socket.id;
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
        case 3: // del company ( sample: socket.emit('settingReload', JSON.stringify({type:3, targetKey: "demo", siteKey: "master"})); )
          console.log('connectList', connectList);
          if ( ('targetKey' in obj) && (obj.targetKey in sincloCore) ) {
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

      company.timeout[userInfo.siteKey][userInfo.userId] = setTimeout(function(){
        // 同一ユーザーが完全にログアウトした場合はユーザーのオブジェクトごと削除
        var keys = Object.keys(company.info[userInfo.siteKey][userInfo.userId]);
        if ( keys.length === 0 ) {
          delete company.info[userInfo.siteKey][userInfo.userId];

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
            timeUpdate(rows[0].id, {}, now);
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
        // c_connectListから情報削除
        if ( (info.siteKey in sincloCore) && (info.tabId in sincloCore[info.siteKey]) ) {
          delete c_connectList[info.siteKey][info.tabId];
        }
        if ( core.subWindow ) {
          // 企業側
          if ( 'toTabId' in core ) {
            emit.toUser('syncStop', {siteKey: info.siteKey, tabId: core.toTabId, connectToken: core.connectToken}, getSessionId(info.siteKey, core.toTabId, 'sessionId'));
            emit.toCompany('syncStop', {siteKey: info.siteKey, tabId: core.toTabId}, info.siteKey);
          }
          syncStopCtrl(info.siteKey, info.tabId);
        }
        else {
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
