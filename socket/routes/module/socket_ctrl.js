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
      connect, displayShere;
      server.listen(9090);//9090番ポートで起動

  // 待機中ユーザー
  var activeOperator = {};

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

  function emit(ev, d){
    var obj = {};
    if ( typeof(d) !== "object" ) {
      obj = JSON.parse(d);
    }
    else {
      obj = d;
    }
    if ( ev !== "connectConfirm" && ev !== "connectInfo" && ev !== "syncResponce") {
      console.log('emit : ' + ev);
    }
    connect.to(obj.siteKey).emit(ev, JSON.stringify(obj));
  }

  function now(){
    var d = new Date();
    return "【" + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() + "】";
  }

  function isset(a){
    return ( a !== undefined && a !== null && a !== "" );
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
        pool.query("UPDATE t_histories SET out_date = ?, modified = ? WHERE id = ?",
          [time, time, historyId],
          function (error,results,fields){
            console.log('Updated histories : error', error);
          }
        );

        if ( obj.url === rows[0].url ) return false;
        pool.query("INSERT INTO t_history_stay_logs SET ?", insertStayData,
          function (error,results,fields){
            console.log('Updated stay log : error', error);
          }
        );
      }
    );
  }

  displayShere = {
    list: {},
    set: function(obj){
      if ( !isset(this.list[obj.siteKey]) ) {
        this.list[obj.siteKey] = {};
      }
      if ( !isset(this.list[obj.siteKey][obj.connectToken]) ) {
        this.list[obj.siteKey][obj.connectToken] = {};
      }
      this.list[obj.siteKey][obj.connectToken].url = obj.url;
    },
    get: function(obj){
      if ( !isset(this.list[obj.siteKey]) ) {
        this.list[obj.siteKey] = {};
      }
      if ( !isset(this.list[obj.siteKey][obj.connectToken]) ) {
        this.list[obj.siteKey][obj.connectToken] = {};
      }
      return this.list[obj.siteKey][obj.connectToken].url;
    }
  };

  access = {
    setInterbalTime: 3000,
    setTime: 5000,
    list: {},
    ev: function (obj){
      if (obj.siteKey) {
        siteKey = obj.siteKey;
      }
      else {
        console.log('siteKey-undefined');
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
          emit('connectConfirm', obj);
        },
        access.setInterbalTime
      );

      this.list[siteKey][tabId].closeIntervalId = setTimeout(function (){
        if ( obj.subWindow ) {
          emit('syncStop', {siteKey: siteKey, tabId: sendTabId, connectToken: obj.connectToken});
          access.delete(siteKey, tabId);
          if ( !isset(companyList[obj.siteKey]) || !isset(obj.tabId) || !isset(obj.userId)) return false;
          if ( !isset(companyList[obj.siteKey]) || obj.subWindow ) return false;
          var siteId = companyList[obj.siteKey];
          pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND tmp_customers_id = ? ORDER BY id DESC LIMIT 1;', [siteId, obj.tabId, obj.userId], function(err, rows){
            if ( isset(rows) && isset(rows[0]) ) {
              var now = formatDateParse();
              timeUpdate(rows[0], obj, now);
            }
          });
        }
        else {
          emit('unsetUser', {siteKey: siteKey, tabId: tabId});
          access.delete(siteKey, tabId);
          if ( !isset(companyList[obj.siteKey]) || !isset(obj.tabId) || !isset(obj.userId)) return false;
          if ( !isset(companyList[obj.siteKey]) || obj.subWindow ) return false;
          var siteId = companyList[obj.siteKey];
          pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND tmp_customers_id = ? ORDER BY id DESC LIMIT 1;', [siteId, obj.tabId, obj.userId], function(err, rows){
            if ( isset(rows) && isset(rows[0]) ) {
              var now = formatDateParse();
              timeUpdate(rows[0], obj, now);
            }
          });
        }

      }, access.setTime);
    },
    start: function(obj){
        console.log('----------------------------');
        console.log(obj);
        console.log('----------------------------');
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
        pool.query('SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND tmp_customers_id = ? ORDER BY id DESC LIMIT 1;', [siteId, obj.tabId, obj.userId], function(err, rows){
          var now = formatDateParse();
          var insertStayData = {
            title: obj.title,
            url: obj.url,
            stay_time: "",
            created: now,
            modified: now
          };
          if ( isset(rows) && isset(rows[0]) ) {
            timeUpdate(rows[0], obj, now);
          }
          else {
            //insert
            var insertData = {
              m_companies_id: siteId,
              tmp_customers_id: obj.userId,
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
            type = ( res.type === 'admin' ) ? 'getAccessInfo' : 'accessInfo';
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
          socket.join(res.siteKey);
          send.siteKey = res.siteKey;
          var siteId = companyList[res.siteKey];
          var cnt = 0;
          if ( isset(activeOperator[res.siteKey]) ) {
            var key = Object.keys(activeOperator[res.siteKey]);
            cnt = key.length;
          }
          pool.query('SELECT * FROM m_widget_settings WHERE m_companies_id = ? ORDER BY id DESC LIMIT 1;', [siteId], function(err, rows){
            if ( isset(rows) && isset(rows[0]) ) {
              send.widget = {
                display_type: rows[0].display_type,
                title: rows[0].title,
                tel: rows[0].tel,
                content: rows[0].content.replace(/\r\n/g, '<br>'),
                time_text: rows[0].time_text,
                display_time_flg: rows[0].display_time_flg,
                active_operator_cnt: cnt
              };
              emit(type, send);
            }
            else {
              emit(type, send);
            }
          });
        }
      });

      socket.on("connectSuccess", function (data) {
        // console.log('send : connectSuccess' );
        var obj = JSON.parse(data);

        // 初回
        if ( !obj.confirm ) {
          access.start(obj);
        }
        // 継続
        else {
          access.update(obj);
          emit('connectInfo', obj);
        }
      });

      socket.on("customerInfo", function (data) {
        console.log('send : customerInfo' );
        var obj = JSON.parse(data);
        emit('sendCustomerInfo', obj);
      });

      socket.on("getCustomerInfo", function(data) {
        var obj = JSON.parse(data);
        emit('confirmCustomerInfo', obj);
      });

      // -----------------------------------------------------------------------
      //  モニタリング通信接続前
      // -----------------------------------------------------------------------
      socket.on("sendAccessInfo", function (data) {
        console.log('send : sendAccessInfo' );
        emit("receiveAccessInfo", data);
      });
      // -----------------------------------------------------------------------
      //  モニタリング通信接続前
      // -----------------------------------------------------------------------
      // [管理]モニタリング開始
      socket.on('requestWindowSync', function (data) {
        console.log('send : requestWindowSync');
        // 同形ウィンドウを作成するための情報取得依頼
        emit('getWindowInfo', data);
      });
      // 同形ウィンドウを作成するための情報受け取り
      socket.on('sendWindowInfo', function (data) {
        console.log('send : sendWindowInfo' );
        // 同形ウィンドウを作成するための情報渡し
        emit('windowSyncInfo', data);
      });
      // iframe用
      socket.on('connectFrame', function (data) {
        console.log('send : connectFrame' );
        var obj = JSON.parse(data);
        if ( obj.siteKey ) {
          socket.join(obj.siteKey);
        }
        console.log('connectFrame----------------- > ', obj);
      });
      // 継続接続(両用)
      socket.on('connectContinue', function (data) {
        console.log('send : connectContinue');
        var obj = JSON.parse(data);

        // URLを保存
        displayShere.set(obj);
        // 企業キーが取得できなければスルー
        if ( obj.siteKey ) {
          socket.join(obj.siteKey);
          data.siteKey = obj.siteKey;
          emit('syncContinue', data);
        }
      });

      // -----------------------------------------------------------------------
      //  モニタリング通信接続後
      // -----------------------------------------------------------------------
      // iframe作成通知(admin -> target)
      socket.on('requestSyncStart', function (data) {
        console.log('send : requestSyncStart' );
        var obj = JSON.parse(data);
        // 企業キーが取得できなければスルー
        if ( obj.siteKey ) {
          socket.join(obj.siteKey);
          data.siteKey = obj.siteKey;
        }
        emit('syncStart', data);
      });

      // 初期同期依頼
      socket.on('getSyncInfo', function (data) {
        console.log('send : getSyncInfo');
        emit('syncElement', data);
      });

      // 初期同期処理完了
      socket.on('syncCompleate', function (data) {
        console.log('send : syncCompleate');
        // マウス・スクロールバーの位置監視
        emit('syncEvStart', data);
      });

      socket.on('requestSyncStop', function (data) {
        console.log('send : requestSyncStop');
        var obj = JSON.parse(data);
        if ( isset(obj.connectToken) ) {
          emit('syncStop', data);
        }
        else {
          emit('unsetUser', data);
          access.clear(obj.siteKey, obj.tabId);
        }
      });

      socket.on('syncBrowserInfo', function (data) {
       // console.log('send : syncBrowserInfo');
        emit('syncResponce', data);
      });

      socket.on('syncChangeEv', function (data) {
        console.log('send : syncChangeEv' );
        emit('syncResponceEv', data);
      });

      socket.on('sendConfirmConnect', function (data) {
          console.log('send : sendConfirmConnect' );
          emit('getConnectInfo', data);
      });

      socket.on('sendConnectInfo', function (data) {
        console.log('send : sendConnectInfo');
        emit('receiveConnect', data);
      });

      socket.on('checkUrl', function (data) {
        var obj = JSON.parse(data);
        var url = displayShere.get(obj);
        console.log('send : checkUrl(' + url + ')');
        // 最新のURLと照合し、合っていなければリダイレクト
        if ( url !== obj.url ) {
          var newObj = obj;
          newObj.url = url;
          emit('redirect', newObj);
        }
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
        emit('activeOpCnt', {
          siteKey: obj.siteKey,
          count: keys.length
        });
      })

      socket.on('userOut', function (data) {
        console.log('send : userOut');
        var obj = JSON.parse(data);
        if ( !isset(obj.connectToken) ) {
          emit('unsetUser', data);
          access.clear(obj.siteKey, obj.tabId);
        }
      });

socket.on('debug', function(){
  pool.query('select * from t_histories', function(err, rows){
    return console.log(rows);
  });

});

  });
// }
