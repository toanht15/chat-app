var Promise = require('es6-promise').Promise;
// mysql
var mysql = require('mysql'),
    pool = mysql.createPool({
      host: process.env.DB_HOST || 'localhost',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASS || 'password',
      database: process.env.DB_NAME || 'sinclo_db'
    });

//サーバインスタンス作成
var http = require('http'),
    server = http.createServer(function (req, res) {
        res.writeHead(200, {'Content-Type':'text/html'});
        res.end('server connected');
    }),
    io = require('socket.io').listen(server), access,
    connect;
    server.listen(9090);//9090番ポートで起動

// 待機中ユーザー
var activeOperator = {};

// 機能仕様状況
var sincloCore = {};

// 暗号化ロジック
var crypto = require('crypto');
    crypto_func = {
    type: 'aes192',
    key: null,
    init: function(){
      var str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890",
      t = "";
      for(var i = 0; i < 30; i++){
        t += str[Math.floor(Math.random()*str.length)];
      }
      this.key = t;
    },
    main: function(str){
      var cipher = crypto.createCipher(this.type, key);
      cipher.update(str, 'utf8', 'hex');
      return cipher.final('hex');
    }
};
crypto_func.init();

// ユーザーIDの新規作成
function makeUserId(){
  var d = new Date();
  return d.getFullYear() + ("0" + (d.getMonth() + 1)).slice(-2) + ("0" + d.getDate()).slice(-2) + d.getHours() + d.getMinutes() + d.getSeconds() + Math.floor(Math.random() * 1000);
}

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
  toUser: function(ev, d, sId){ // 対象ユーザーに送信(sId = the session id)
    var obj = this._convert(d);
    if ( !isset(io.sockets.connected[sId]) ) return false;
    return io.sockets.connected[sId].emit(ev, obj);
  },
  toClient: function(ev, d, rName) { // 対象企業を閲覧中のユーザーに送信(rName = the room's name)
    var obj = this._convert(d);
    return io.sockets.in(rName+this.roomKey.client).emit(ev, obj);
  },
  toCompany: function(ev, d, rName) { // 対象企業にのみ送信(rName = the room's name)
    var obj = this._convert(d);
    return io.sockets.in(rName+this.roomKey.company).emit(ev, obj);
  }
};

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
    title: obj.title,
    url: obj.url,
    stay_time: "",
    created: time,
    modified: time
  };
  pool.query('SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;', [historyId, obj.url],
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

      if ( obj.url === rows[0].url ) return false;
      pool.query("INSERT INTO t_history_stay_logs SET ?", insertStayData,
        function (error,results,fields){
        }
      );
    }
  );
}

access = {
  setInterbalTime: 3000,
  setTime: 5000,
  list: {},
  ev: function (obj){
    if (obj.siteKey) {
      siteKey = obj.siteKey;
    }
    else {
      return false;
    }
    var tabId = (obj.tabId) ? obj.tabId : obj.from,
        sendTabId = (obj.to) ? obj.to : obj.tabId,
        url = (obj.url) ? obj.url : "",
        connectToken = (isset(obj.connectToken)) ? true : false;

    if ( !isset(this.list[siteKey]) || (isset(this.list[siteKey]) && !isset(this.list[siteKey][tabId])) ) {
      this.start(obj);
    }
    this.clear(siteKey, tabId);
    this.list[siteKey][tabId].confirmIntervalId = setInterval(
      function(){
        emit.toUser('connectConfirm', obj, sincloCore[obj.siteKey][obj.tabId].sessionId);
        emit.toCompany('connectConfirm', obj, obj.siteKey);
      },
      access.setInterbalTime
    );

    this.list[siteKey][tabId].closeIntervalId = setTimeout(function (){
      if ( obj.subWindow ) {
        sincloCore[obj.siteKey][obj.to].syncSessionId = null;
        sincloCore[obj.siteKey][obj.to].syncHostSessionId = null;
        emit.toUser('syncStop', {siteKey: obj.siteKey, tabId: sendTabId, connectToken: obj.connectToken}, sincloCore[obj.siteKey][obj.to].sessionId);
        access.delete(obj.siteKey, tabId);
        if ( !isset(companyList[obj.siteKey]) || !isset(obj.tabId) || !isset(obj.userId)) return false;
        if ( !isset(companyList[obj.siteKey]) || obj.subWindow ) return false;
        var siteId = companyList[obj.siteKey];
        pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [siteId, obj.tabId, obj.userId], function(err, rows){
          if ( isset(rows) && isset(rows[0]) ) {
            var now = formatDateParse();
            timeUpdate(rows[0], obj, now);
          }
        });
      }
      else {

        if ( obj.connectToken ) {
          emit.toUser('unsetUser', {siteKey: obj.siteKey, tabId: tabId}, sincloCore[obj.siteKey][obj.tabId].syncFrameSessionId);
        }
        emit.toCompany('unsetUser', {siteKey: obj.siteKey, tabId: tabId}, obj.siteKey);
        access.delete(obj.siteKey, tabId);
        if ( !isset(companyList[obj.siteKey]) || !isset(obj.tabId) || !isset(obj.userId)) return false;
        if ( !isset(companyList[obj.siteKey]) || obj.subWindow ) return false;
        var siteId = companyList[obj.siteKey];
        pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;', [siteId, obj.tabId, obj.userId], function(err, rows){
          if ( isset(rows) && isset(rows[0]) ) {
            var now = formatDateParse();
            timeUpdate(rows[0], obj, now);
          }
        });
      }

    }, access.setTime);
  },
  start: function(obj){
    if ( !isset(this.list[obj.siteKey]) ) {
      this.list[obj.siteKey] = {};
    }
    if ( isset(this.list[obj.siteKey][obj.tabId]) ) {
      this.update(obj);
    }
    else {
      this.list[obj.siteKey][obj.tabId] = {
        closeIntervalId : "",
        confirmIntervalId: ""
      };
    }
    db.addHistory(obj);
    this.ev(obj);
  },
  update: function(obj){
    this.ev(obj);
  },
  clear: function(siteKey, tabId){
    if ( !isset(access.list[siteKey]) || (isset(access.list[siteKey]) && !isset(access.list[siteKey][tabId])) ) return false;
    var info = access.list[siteKey][tabId];
    if ( isset(info.confirmIntervalId) ) {
      clearInterval(info.confirmIntervalId);
    }
    if ( isset(info.closeIntervalId) ) {
      clearTimeout(info.closeIntervalId);
    }
  },
  delete: function(siteKey, tabId){
    this.clear(siteKey, tabId);
    delete access.list[siteKey][tabId];
    delete sincloCore[siteKey][tabId];
  }
};

var companyList = {};
pool.query('select * from m_companies;', function(err, rows){
  var key = Object.keys(rows);
  for ( var i = 0; key.length > i; i++ ) {
    var row = rows[key[i]];
    companyList[row.company_key] = row.id;
  }
});

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
        if ( isset(rows) && isset(rows[0]) ) {
          sincloCore[obj.siteKey][obj.tabId].historyId = rows[0].id;
          timeUpdate(rows[0], obj, now);
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

//接続確立時の処理
connect = io.sockets.on('connection', function (socket) {
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
      // アクセス開始フラグ
      if ( data.firstConnection ) {
        var d = new Date();
        send.time = Date.parse(d);
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
        socket.join(res.siteKey + emit.roomKey.client);
        emit.toUser('accessInfo', send, socket.id);
      }

    }
  });

  socket.on("getWidgetInfo", function (data) {
    var obj = JSON.parse(data);
    pool.query('SELECT * FROM m_widget_settings WHERE m_companies_id = ? ORDER BY id DESC LIMIT 1;', [companyList[obj.siteKey]], function(err, rows){
      var cnt = 0;
      if ( isset(activeOperator[obj.siteKey]) ) {
        var key = Object.keys(activeOperator[obj.siteKey]);
        cnt = key.length;
      }
      if ( isset(rows) && isset(rows[0]) ) {
        obj.widget = {
          display_type: rows[0].display_type,
          title: rows[0].title,
          tel: rows[0].tel,
          content: rows[0].content.replace(/\r\n/g, '<br>'),
          time_text: rows[0].time_text,
          display_time_flg: rows[0].display_time_flg,
          active_operator_cnt: cnt
        };
        emit.toUser('setWidgetInfo', obj, socket.id);
      }
      else {
        emit.toUser('setWidgetInfo', obj, socket.id);
      }
    });
  });

  socket.on("customerInfo", function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);
    emit.toCompany("sendCustomerInfo", obj, obj.siteKey);
  });

  socket.on("getCustomerInfo", function(data) {
    var obj = JSON.parse(data);
    emit.toClient('confirmCustomerInfo', obj, obj.siteKey);
  });

  socket.on("connectSuccess", function (data) {
    var obj = JSON.parse(data);

    // 初回
    if ( !obj.confirm ) {
      if ( !isset(sincloCore[obj.siteKey]) ) {
        sincloCore[obj.siteKey] = {};
      }
      if ( !isset(sincloCore[obj.siteKey][obj.tabId]) ) {
        sincloCore[obj.siteKey][obj.tabId] = {sessionId: null};
      }

      sincloCore[obj.siteKey][obj.tabId].sessionId = socket.id;

      if ( obj.subWindow ) {
        sincloCore[obj.siteKey][obj.to]['syncSessionId'] = socket.id; // 同期先配列に、セッションIDを格納
      }
      else {
      }

      access.start(obj);
    }
    // 継続
    else {

      if ( isset(sincloCore[obj.siteKey][obj.tabId]['sessionId']) ) {
        emit.toUser('connectInfo', obj, sincloCore[obj.siteKey][obj.tabId].sessionId);
      }
      else {
      }
      emit.toCompany('connectInfo', obj, obj.siteKey);
      access.update(obj);
    }

  });

  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  socket.on("sendAccessInfo", function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);
    emit.toCompany("receiveAccessInfo", obj, obj.siteKey);
  });
  // -----------------------------------------------------------------------
  //  モニタリング通信接続前
  // -----------------------------------------------------------------------
  // [管理]モニタリング開始
  socket.on('requestWindowSync', function (data) {
    var obj = JSON.parse(data);
    // 同形ウィンドウを作成するための情報取得依頼
    if ( !isset(sincloCore[obj.siteKey][obj.tabId]['sessionId']) ) return false;
    sincloCore[obj.siteKey][obj.tabId].syncSessionId = null;
    sincloCore[obj.siteKey][obj.tabId].syncHostSessionId = socket.id; // 企業画面側のセッションID
    emit.toUser('getWindowInfo', data, sincloCore[obj.siteKey][obj.tabId].sessionId);
  });
  // 同形ウィンドウを作成するための情報受け取り
  socket.on('sendWindowInfo', function (data) {
    var obj = JSON.parse(data);
    // 同形ウィンドウを作成するための情報渡し
    if ( !isset(sincloCore[obj.siteKey][obj.tabId]['syncHostSessionId']) ) return false;
    emit.toUser('windowSyncInfo', data, sincloCore[obj.siteKey][obj.tabId].syncHostSessionId);
  });
  // iframe用
  socket.on('connectFrame', function (data) {
    var obj = JSON.parse(data);
    if ( obj.siteKey ) {
      socket.join(obj.siteKey + emit.roomKey.client);
    }
    sincloCore[obj.siteKey][obj.tabId].syncFrameSessionId = socket.id; // フレームのセッションID
  });
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
    var obj = JSON.parse(data);
    // 企業キーが取得できなければスルー
    if ( obj.siteKey ) {
      socket.join(obj.siteKey + emit.roomKey.client);
      data.siteKey = obj.siteKey;
    }
    var tabId = ( obj.accessType !== 1 ) ? obj.to : obj.tabId;
    emit.toUser('syncStart', data, sincloCore[obj.siteKey][tabId].sessionId);
    emit.toUser('syncStart', data, sincloCore[obj.siteKey][tabId].syncSessionId);
  });

  // 初期同期依頼
  socket.on('getSyncInfo', function (data) {
    var obj = JSON.parse(data);
    emit.toUser('syncElement', data, sincloCore[obj.siteKey][obj.tabId].syncSessionId);
  });

  // 初期同期処理完了
  socket.on('syncCompleate', function (data) {
    var obj = JSON.parse(data);
    // マウス・スクロールバーの位置監視
    emit.toUser('syncEvStart', data, sincloCore[obj.siteKey][obj.to].sessionId);
    emit.toUser('syncEvStart', data, sincloCore[obj.siteKey][obj.to].syncSessionId);
  });

  socket.on('requestSyncStop', function (data) {
    var obj = JSON.parse(data);
    if ( isset(obj.connectToken) ) {
      emit.toUser('syncStop', data, sincloCore[obj.siteKey][obj.tabId].sessionId);
    }
    else {
      emit.toCompany('unsetUser', data, obj.siteKey);
      access.clear(obj.siteKey, obj.tabId);
    }
  });

  socket.on('syncBrowserInfo', function (data) {
    var obj = JSON.parse(data);
    if ( isset(obj.windowSize) && isset(sincloCore[obj.siteKey][obj.tabId]['syncFrameSessionId'])) {
      emit.toUser('syncResponce', data, sincloCore[obj.siteKey][obj.tabId].syncFrameSessionId);
    }
    else {
      emit.toUser('syncResponce', data, sincloCore[obj.siteKey][obj.to].sessionId);
    }
  });

  socket.on('syncChangeEv', function (data) {
    var obj = JSON.parse(data);
      emit.toUser('syncResponceEv', data, sincloCore[obj.siteKey][obj.to].sessionId);
  });

  socket.on('sendConfirmConnect', function (data) {
    var obj = JSON.parse(data);
    if ( obj.accessType === 1 ) { // to host
      emit.toUser('getConnectInfo', data, sincloCore[obj.siteKey][obj.tabId].syncSessionId);
    }
    else {
      emit.toUser('getConnectInfo', data, sincloCore[obj.siteKey][obj.tabId].sessionId);
    }
  });

  socket.on('sendConnectInfo', function (data) {
    var obj = JSON.parse(data);
    obj.term = timeCalculator(obj);
    emit.toUser('receiveConnect', data, sincloCore[obj.siteKey][obj.tabId].sessionId);
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
    if ( isset(sincloCore[obj.siteKey]) && isset(sincloCore[obj.siteKey][obj.to]) && isset(sincloCore[obj.siteKey][obj.to]['sessionId']) ) {
      emit.toUser('resUrlChecker', data, sincloCore[obj.siteKey][obj.to].sessionId);
    }
  });

  socket.on('userOut', function (data) {
    var obj = JSON.parse(data);
    if ( !isset(obj.connectToken) ) {
      emit.toCompany('unsetUser', data, obj.siteKey);
      access.clear(obj.siteKey, obj.tabId);
    }
  });
});
