(function($){
  // -----------------------------------------------------------------------------
  //  websocket通信
  // -----------------------------------------------------------------------------

  sinclo = {
    syncTimeout: "",
    operatorInfo: {
      header: null,
      ev: function() {
        var height = 0;
        var sincloBox = document.getElementById('sincloBox');
        var flg = sincloBox.getAttribute('data-openflg');
        var elm = $('#sincloBox');
        if ( String(flg) === "false" ) {
          var height = 0;
          height += $("#sincloBox #widgetHeader").outerHeight(true);
          if ( $("#sincloBox").children().is("#navigation") ) {
            height += $("#sincloBox > #navigation").outerHeight(true);
            var tab = $("#navigation li.selected").data('tab');
            height += $("#" + tab + "Tab").outerHeight(true);
          }
          else {
            height += $("[id$='Tab']").outerHeight(true);
          }
          height += $("#sincloBox > #fotter").outerHeight(true);
          sincloBox.setAttribute('data-openflg', true);
        }
        else {
          height = this.header.offsetHeight;
          sincloBox.setAttribute('data-openflg', false);
        }
        elm.animate({
          height: height + "px"
        }, 'first');
      }
    },
    connect: function(){
      // 新規アクセスの場合
      if ( !check.isset(userInfo.getTabId()) ) {
        userInfo.firstConnection = true;
        window.opener = null;
        userInfo.strageReset();
        userInfo.setReferrer();
        userInfo.setStayCount();
      }
      userInfo.init();

      var emitData = {
          referrer: userInfo.referrer,
          time: userInfo.getTime(),
          page: userInfo.getPage(),
          firstConnection: userInfo.firstConnection,
          userAgent: window.navigator.userAgent,
          service: check.browser(),
          prevList: userInfo.prevList
        };

      // モニタリング中であればスルー
      if ( check.isset(userInfo.connectToken) ) {
        common.load.start();
        if ( Number(userInfo.accessType) === Number(cnst.access_type.guest) ) {
          emitData.connectToken = userInfo.connectToken;
          userInfo.syncInfo.get();
          emit('connectSuccess', {prevList: userInfo.prevList});
        }

        if ( check.isset(common.tmpParams) ) {
          browserInfo.resetPrevList();
          emit('requestSyncStart', {
            accessType: common.params.type
          });
        }
        else {
          emit('reqUrlChecker', {});
        }

        emit('reqUrlChecker', {});

        browserInfo.setPrevList();

        if ( !check.isset(common.tmpParams) ) {
          emit('connectContinue', {
            connectToken: userInfo.connectToken,
            accessType: common.params.type,
          });

          window.clearTimeout(this.syncTimeout);
          this.syncTimeout = window.setTimeout(function(){
            emit('requestSyncStop', emitData);
            emit('connected', {type: 'user',data: emitData});
            userInfo.syncInfo.unset();
          }, 5000);

        }
        return false;
      }
      emit('connected', {
        type: 'user',
        data: emitData
      });
    },

    accessInfo: function(d){
      var obj = common.jParse(d);
      if ( obj.token !== common.token ) return false;

      if ( ('activeOperatorCnt' in obj) ) {
        window.info.activeOperatorCnt = obj['activeOperatorCnt'];
      }

      if ( ('pagetime' in obj) ) {
        userInfo.pageTime = obj['pagetime'];
      }

      if ( check.isset(obj.accessId) && !check.isset(obj.connectToken)) {
        userInfo.set(cnst.info_type.access, obj.accessId, true);

        var setWidgetFnc = function(){
          if ( window.info.widget === undefined ) {
            setTimeout(setWidgetFnc, 500);
          }
          else {
            common.makeAccessIdTag();
          }
        };

        setWidgetFnc();

      }

      if ( obj.firstConnection ) {
        if ( !check.isset(userInfo.userId) && check.isset(obj.userId) ) {
          userInfo.set(cnst.info_type.user, obj.userId);
        }

        if ( userInfo.accessType === Number(cnst.access_type.guest) ) {
          userInfo.set(cnst.info_type.ip, obj.ipAddress);
          userInfo.set(cnst.info_type.time, obj.time, true);
        }
        userInfo.setTabId();
      }

      emit('customerInfo', obj);
      emit('connectSuccess', {
        confirm: false,
        widget: window.info.widgetDisplay,
        prevList: userInfo.prevList,
        userAgent: window.navigator.userAgent,
        time: userInfo.time,
        ipAddress: userInfo.getIp(),
        referrer: userInfo.referrer
      });
    },
    setHistoryId: function(d){
        var createStartTimer,
            createStart = function(){
                var sincloBox = document.getElementById('sincloBox');
                sincloBox.style.display = "block";
                sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
                // ウィジェット表示
                if ( ('maxShowTime' in window.info.widget) && String(window.info.widget.maxShowTime).match(/^[0-9]{1,2}$/) !== null ) {
                  var widgetOpen = storage.s.get('widgetOpen');
                  if (!widgetOpen) {
                    window.setTimeout(function(){
                      var flg = sincloBox.getAttribute('data-openflg');
                      if ( String(flg) === "false" ) {
                        storage.s.set('widgetOpen', true);
                        sinclo.operatorInfo.ev();
                      }
                    }, Number(window.info.widget.maxShowTime) * 1000);
                  }
                }
                if ( window.info.contract.chat ) {
                    // チャット情報読み込み
                    sinclo.chatApi.init();
                    // オートメッセージ読み込み
                    sinclo.trigger.init();
                }
        };

        createStartTimer = window.setInterval(function(){
          if (window.info.widgetDisplay) {
            window.clearInterval(createStartTimer);
            createStart();
          }
        }, 500);

    },
    getAccessInfo: function(d) { // guest only
      var obj = common.jParse(d);
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var emitData = userInfo.getSendList();
      emitData.receiveAccessInfoToken = obj.token;
      emitData.widget = window.info.widgetDisplay;
      emit('sendAccessInfo', emitData);
    },
    confirmCustomerInfo: function(d) {
      var obj = common.jParse(d);
      if ( userInfo.tabId !== obj.tabId ) return false;
      if ( userInfo.accessType !== cnst.access_type.guest ) return false;
      var emitData = userInfo.getSendList();
      emitData.receiveAccessInfoToken = obj.token;
      emit('customerInfo', emitData);
    },
    getConnectInfo: function(d) {
      var obj = common.jParse(d);
      if ( userInfo.tabId !== obj.tabId ) return false;
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      if ( userInfo.connectToken !== obj.connectToken ) return false;
      emit('sendConnectInfo', {
        accessType: userInfo.accessType,
        tabId: userInfo.getTabId(),
        userId: userInfo.userId,
        accessId: userInfo.accessId,
        connectToken: userInfo.connectToken
      });
    },
    getWindowInfo: function(d) {
      var obj = common.jParse(d);
      if ( obj.tabId !== userInfo.tabId ) return false;
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var title = location.host + 'の内容';
      var content = location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function(){
        userInfo.connectToken = obj.connectToken;
        browserInfo.resetPrevList();

        emit('sendWindowInfo', {
          userId: userInfo.userId,
          tabId: userInfo.tabId,
          connectToken: userInfo.connectToken,
          // 解像度
          screen: browserInfo.windowScreen(),
          // ブラウザのサイズ
          windowSize: browserInfo.windowSize(),
          // スクロール位置の取得
          scrollPosition: browserInfo.windowScroll()
        });
        this.remove();
      };
      popup.set(title, content);
    },
    windowSyncInfo: function(d) {
      var obj = common.jParse(d);
      browserInfo.set.scroll(obj.scrollPosition);
    },
    syncStart: function(d) {
      var obj = common.jParse(d);
      if ( Number(userInfo.accessType) === Number(cnst.access_type.host) ) {
        window.clearTimeout(this.syncTimeout);
        return false;
      }
      common.load.start();
      userInfo.setConnect(obj.connectToken);
      if ( !check.isset(userInfo.sendTabId) ) {
        userInfo.sendTabId = obj.from;
        userInfo.syncInfo.set();
      }
      else {
       userInfo.syncInfo.get();
      }
      // フォーム情報収集
      var inputInfo = [];
      $('input').each(function(){
        inputInfo.push(this.value);
      });
      var checkboxInfo = [];
      $('input[type="checkbox"]').each(function(){
        checkboxInfo.push(this.checked);
      });
      var radioInfo = [];
      $('input[type="radio"]').each(function(){
        radioInfo.push(this.checked);
      });
       var textareaInfo = [];
      $('textarea').each(function(){
        textareaInfo.push(this.value);
      });
       var selectInfo = [];
      $('select').each(function(){
        selectInfo.push(this.value);
      });
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox ) {
        sincloBox.parentNode.removeChild(sincloBox);
      }

      emit('getSyncInfo', {
        userId: userInfo.userId,
        connectToken: userInfo.connectToken,
        inputInfo: inputInfo,
        checkboxInfo: checkboxInfo,
        radioInfo: radioInfo,
        textareaInfo: textareaInfo,
        selectInfo: selectInfo,
        // スクロール位置の取得
        scrollPosition: browserInfo.windowScroll()
      });
    },
    syncElement: function(d){
      var obj = common.jParse(d);
      var scrollSize = browserInfo.scrollSize();
      window.clearTimeout(this.syncTimeout);
      $("html, body").animate(
        {
          scrollLeft: scrollSize.x * obj.scrollPosition.x,
          scrollTop: scrollSize.y * obj.scrollPosition.y
        },
        {
            duration: 'first',
            easing: 'swing',
            complete: function(){
              for ( var i in obj.inputInfo ) {
                var n = Number(i);
                $('input').eq(n).val(obj.inputInfo[n]);
              }
              for ( var i in obj.checkboxInfo ) {
                var n = Number(i);
                $('input[type="checkbox"]').eq(n).prop("checked", obj.checkboxInfo[n]);
              }
              for ( var i in obj.radioInfo ) {
                var n = Number(i);
                $('input[type="radio"]').eq(n).prop("checked", obj.radioInfo[n]);
              }
              for ( var i in obj.textareaInfo ) {
                var n = Number(i);
                $('textarea').eq(n).val(obj.textareaInfo[n]);
              }
              for ( var i in obj.selectInfo ) {
                var n = Number(i);
                $('select').eq(n).val(obj.selectInfo[n]);
              }
              emit('syncCompleate', {
                userId: userInfo.userId,
                accessType: userInfo.accessType
              });
            }
        }
      );
    },
    syncEvStart: function(d){
      var obj = common.jParse(d);
      if ( obj.to !== userInfo.tabId && obj.from !== userInfo.tabId ) return false;
      syncEvent.start(true);
      window.clearTimeout(sinclo.syncTimeout);
      common.load.finish();
    },
    receiveScTimer: false,
    syncResponce: function(d){
      var obj = common.jParse(d), cursor = common.cursorTag;
      // 画面共有用トークンでの認証に変更する？
      if ( obj.to !== userInfo.tabId ) return false;
      if ( Number(obj.accessType) === Number(userInfo.accessType) ) return false;
      // カーソルを作成していなければ作成する
      if ( !document.getElementById('cursorImg') ) {
        $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mousePoint.x + '; left:' + obj.mousePoint.y + '; z-index:999999"><img width="50px" src="' + window.info.site.files + '/img/pointer.png"></div>');
        cursor = common.cursorTag = document.getElementById("cursorImg");
      }
      else {
        // スクロール位置
        if ( check.isset(obj.scrollPosition) ) {
          syncEvent.receiveEvInfo.type = "scroll";
          syncEvent.receiveEvInfo.nodeName = "body";
          if (this.receiveScTimer) {
            clearTimeout(this.receiveScTimer);
          }

          browserInfo.set.scroll(obj.scrollPosition);

          // TODO まだ微調整が必要
          this.receiveScTimer = setTimeout(function(){
            syncEvent.receiveEvInfo = { nodeName: null, type: null };
          }, browserInfo.interval);
        }
      }
      // カーソル位置
      if ( check.isset(obj.mousePoint)) {
        cursor.style.left = obj.mousePoint.x + "px";
        cursor.style.top  = obj.mousePoint.y + "px";
      }

    },
    syncResponceEv: function (d) {
      var obj = common.jParse(d), elm;
      if ( obj.to !== userInfo.tabId ) return false;
      if ( obj.accessType === userInfo.accessType ) return false;
      elm = $(String(obj.nodeName)).eq(Number(obj.idx));
      syncEvent.receiveEvInfo.type = obj.type;
      syncEvent.receiveEvInfo.nodeName = String(obj.nodeName);
      syncEvent.receiveEvInfo.idx = Number(obj.idx);
      switch (obj.type) {
        case "change":
          if ( String(obj.nodeType) === "radio" || String(obj.nodeType) === "checkbox" ) {
            elm.prop('checked', obj.checked);
          }
        case "keyup":
          elm.val(obj.value);
          break;
        case "scroll":
          var elm = $(obj.nodeName).eq(Number(obj.idx));
          if ( elm.length > 0 ) {
            var scrollBarSize = {
                  height: elm[0].scrollHeight - elm[0].clientHeight,
                  width: elm[0].scrollWidth - elm[0].clientWidth
                };
                elm.stop(false, false).scrollTop(scrollBarSize.height * Number(obj.value.topRatio));
                elm.stop(false, false).scrollLeft(scrollBarSize.width * Number(obj.value.leftRatio));
          }
      };
      syncEvent.receiveEvInfo = { nodeName: null, type: null };
    },
    receiveConnectEv: function(d){
      var obj = JSON.parse(d);
      if ( obj.to !== userInfo.tabId ) return false;
    },
    userDissconnectionEv: function(d){
      var obj = JSON.parse(d);
      if ( obj.connectToken !== userInfo.connectToken ) return false;
      if ( obj.to !== userInfo.tabId ) return false;
      emit('sendConfirmConnect', obj);
    },
    syncContinue:function (d) {
      var obj = JSON.parse(d);
      if ( obj.connectToken !== userInfo.connectToken ) return false;
      if ( obj.to !== userInfo.tabId ) return false;
      emit('requestSyncStart', obj);
    },
    resUrlChecker: function(d){
      var obj = JSON.parse(d);
      if ( obj.url !== browserInfo.href ) {
        common.load.start();
        location.href = obj.url;
      }
      else {
        emit('requestSyncStart', {
          accessType: common.params.type
        });
      }
    },
    chatMessageData:function(d){
      var obj = JSON.parse(d);
      if ( obj.token !== common.token ) return false;
      this.chatApi.historyId = obj.chat.historyId;
      for (var i = 0; i < obj.chat.messages.length; i++) {
        var chat = obj.chat.messages[i],
            cn = (chat.messageType == 1) ? "sinclo_se" : "sinclo_re";
        this.chatApi.createMessage(cn, chat.message);
      }
    },
    sendChatResult: function(d){
      var obj = JSON.parse(d);
      if ( obj.tabId !== userInfo.tabId ) return false;
      var elm = document.getElementById('sincloChatMessage'), cn;
      if ( obj.ret ) {
        if (obj.messageType === sinclo.chatApi.messageType.customer) {
          cn = "sinclo_se";
          elm.value = "";
        }
        else {
          cn = "sinclo_re";
        }
        this.chatApi.createMessage(cn, obj.chatMessage);
        // オートメッセージの内容をDBに保存し、オブジェクトから削除する
        emit("sendAutoChat", {messageList: sinclo.chatApi.autoMessages});
        sinclo.chatApi.autoMessages = [];
      }
      else {
        alert('メッセージの送信に失敗しました。');
      }
    },
    sendReqAutoChatMessage: function(d){
      // 自動メッセージの情報を渡す（保存の為）
      var obj = common.jParse(d);
      emit("sendAutoChatMessage", {messages: sinclo.chatApi.autoMessages, chatToken: obj.chatToken});
    },
    syncStop: function(d){
      var obj = common.jParse(d);
      syncEvent.stop(false);
      userInfo.syncInfo.unset();
      common.makeAccessIdTag();
    },
    chatApi: {
      historyId: null,
      messageType: {
        customer: 1,
        company: 2,
        auto: 3
      },
      autoMessages: [],
      init: function(){
        $("#sincloChatMessage").on("keydown", function(e){
          if ( e.keyCode === 13 ) {
            if ( !e.shiftKey && !e.ctrlKey ) {
              sinclo.chatApi.push();
            }
          }
        });

        emit('getChatMessage', {});
      },
      createMessage: function(cs, val){
        var chatTalk = document.getElementById('chatTalk');
        var li = document.createElement('li');
        li.className = cs;
        li.textContent = val;
        chatTalk.appendChild(li);
        $('#chatTalk').animate({
          scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
        }, 100);
      },
      push: function(){
        var elm = document.getElementById('sincloChatMessage');
        if ( check.isset(elm.value) ) {
          emit('sendChat', {
            historyId: sinclo.chatApi.historyId,
            chatMessage:elm.value,
            mUserId: null,
            messageType: sinclo.chatApi.messageType.customer
          });
        }
      }
    },
    trigger: {
        timerList: {},
        init: function(){
            if ( !('messages' in window.info) || (('messages' in window.info) && typeof(window.info.messages) !== "object" ) ) return false;
            var messages = window.info.messages;
            // 設定ごと
            for( var i = 0; messages.length > i; i++ ){
                var ret = false;
                // AND
                if ( Number(messages[i]['activity']['conditionType']) === 1 ) {
                    ret = this.setAndSetting(messages[i]['activity']);
                }
                // OR
                else {
                    ret = this.setOrSetting(messages[i]['activity']);
                }
                if ( typeof(ret) === "number" ) {
                    var message = messages[i];
                    setTimeout(function(){
                        sinclo.trigger.setAction(message['id'], message['action_type'], message['activity']);
                    }, ret);
                }
            }
        },
        /**
         * return 即時実行(0)、タイマー実行(ミリ秒)、非実行(null)
         */
        setAndSetting: function(setting) {
            var keys = Object.keys(setting['conditions']);
            var ret = 0;
            for(var i = 0; keys.length > i; i++){
                var conditions = setting['conditions'][keys[i]];
                var last = (keys.length === Number(i+1)) ? true : false;
                var timer = 0;
                switch(Number(keys[i])) {
                    case 1: // 滞在時間
                      timer = this.judge.stayTime(conditions[0]);
                      if ( ret < timer ) {
                        ret = Number(timer);
                      }
                      break;
                    case 2: // 訪問回数
                      timer = (!this.judge.stayCount(conditions[0]));
                      if (typeof(timer) === "number" && typeof(ret) !== "number") {
                        ret = 0;
                      }
                      break;
                    case 3: // ページ
                      timer = (!this.judge.page(conditions[0]));
                      if (typeof(timer) === "number" && typeof(ret) !== "number") {
                        ret = 0;
                      }
                      break;
                    case 4: // 曜日・時間
                      timer = this.judge.dayTime(conditions[0]);
                      if (typeof(timer) !== "number") return null;
                      if ( ret < timer ) {
                        ret = Number(timer);
                      }
                      break;
                    case 5: // リファラー
                      timer = (!this.judge.referrer(conditions[0]));
                      if (typeof(timer) === "number" && typeof(ret) !== "number") {
                        ret = 0;
                      }
                      break;
                    case 6: // 検索ワード
                      timer = (!this.judge.searchWord(conditions[0]));
                      if (typeof(timer) === "number" && typeof(ret) !== "number") {
                        ret = 0;
                      }
                      break;
                    default:
                      ret = null;
                      break;
                }
            }
            return ret;
        },
        /**
         * return 即時実行(0)、タイマー実行(ミリ秒)、非実行(null)
         */
        setOrSetting: function(setting) {
            var keys = Object.keys(setting['conditions']);
            var ret = null;
            for(var i = 0; keys.length > i; i++){
                var conditions = setting['conditions'][keys[i]];
                var last = (keys.length === Number(i+1)) ? true : false;
                var timer = 0;
                switch(Number(keys[i])) {
                    case 1: // 滞在時間
                      for (var u = 0; u < conditions.length; u++) {
                        timer = this.judge.stayTime(conditions[u]);
                        if ( ret < timer ) {
                          ret = Number(timer);
                        }
                      }
                      break;
                    case 2: // 訪問回数
                      for (var u = 0; u < conditions.length; u++) {
                        timer = this.judge.stayCount(conditions[u]);
                        if (typeof(timer) === "number" && typeof(ret) !== "number") {
                          ret = 0;
                        }
                      }
                      break;
                    case 3: // ページ
                      for (var u = 0; u < conditions.length; u++) {
                        timer = this.judge.page(conditions[u]);
                        if (typeof(timer) === "number" && typeof(ret) !== "number") {
                          ret = 0;
                        }
                      }
                      break;
                    case 4: // 曜日・時間
                      for (var u = 0; u < conditions.length; u++) {
                        timer = this.judge.dayTime(conditions[u]);
                        if (typeof(timer) === "number"){
                          if ( typeof(ret) !== "number" || (typeof(ret) === "number" && ret < timer) ) {
                            ret = Number(timer);
                          }
                        }
                      }
                      break;
                    case 5: // リファラー
                      for (var u = 0; u < conditions.length; u++) {
                        timer = this.judge.referrer(conditions[u]);
                        if (typeof(timer) === "number" && typeof(ret) !== "number") {
                          ret = 0;
                        }
                      }
                      break;
                    case 6: // 検索ワード
                      for (var u = 0; u < conditions.length; u++) {
                        timer = this.judge.searchWord(conditions[u]);
                        if (typeof(timer) === "number" && typeof(ret) !== "number") {
                          ret = 0;
                        }
                      }
                      break;
                    default:
                      break;
                }
            }
            return ret;
        },
        setAction: function(id, type, cond){
            // TODO 今のところはメッセージ送信のみ、拡張予定
            if ( String(type) === "1" && ('message' in cond)) {
                sinclo.chatApi.createMessage("sinclo_re", cond.message);
                sinclo.chatApi.autoMessages.push({
                  chatId:id,
                  chatMessage:cond.message,
                  created: common.formatDateParse()
                });
            }
        },
        common: {
            /**
             * @params int type 比較種別
             * @params int a 基準値
             * @params int b 比較対象
             * @return bool
             */
            numMatch: function(type, a, b) {
                switch(Number(type)) {
                    case 1: // 一致
                      if (Number(a) ===  Number(b) ) return true;
                      break;
                    case 2: // 以上
                      if (Number(a) <= Number(b) ) return true;
                      break;
                    case 3: // 未満
                      if (Number(a) > Number(b) ) return true;
                      break;
                }
                return false;
            },
            /**
             * @params int type 比較種別
             * @params int a キーワード
             * @params int b マッチ対象
             * @return bool
             */
            pregMatch: function(type, a, b) {
                var preg = "";
                switch(Number(type)) {
                    case 1: // 一致
                      preg = new RegExp("^" + a + "$");
                      return preg.test(b);
                      break;
                    case 2: // 部分一致
                      preg = new RegExp(a);
                      return preg.test(b);
                      break;
                    case 3: // 不一致
                      preg = new RegExp("^" + a + "$");
                      return !preg.test(b);
                      break;
                }
                return false;
            }
        },
        judge: {
            stayTime: function(cond){
                if (!('stayTimeType' in cond) || !('stayTimeRange' in cond )) return null;
                var time = 0;
                switch(Number(cond.stayTimeType)) {
                    case 1: // 秒
                      time = Number(cond.stayTimeRange) * 1000;
                      break;
                    case 2: // 分
                      time = Number(cond.stayTimeRange) * 1000 * 60;
                      break;
                    case 3: // 時
                      time = Number(cond.stayTimeRange) * 1000 * 60 * 60;
                      break;
                }
                var term = (Number(userInfo.pageTime) - Number(userInfo.time));
                if ( term < time ) {
                    return time-term;
                }
                else {
                	return 0;
                }

            },
            stayCount: function(cond){
                if (!('visitCntCond' in cond) || !('visitCnt' in cond )) return null;
                return (sinclo.trigger.common.numMatch(cond.visitCntCond, userInfo.getStayCount(), cond.visitCnt)) ? 0 : null;
            },
            page: function(cond){
                if (!('keyword' in cond) || !('targetName' in cond ) || !('stayPageCond' in cond )) return null;
                var target = ( Number(cond.targetName) === 1 ) ? common.title() : location.href ;
                return (sinclo.trigger.common.pregMatch(cond.stayPageCond, cond.keyword, target)) ? 0 : null;
            },
            dayTime: function(cond){
                if (!('day' in cond) || !('timeSetting' in cond)) return null;
                if (Number(cond.timeSetting) === 1 && (!('startTime' in cond) || !('endTime' in cond))) return null;
                // DBに保存している文字列から、JSのgetDay関数に対応する数値を返す関数
                function translateDay(str){
                    var day = {'sun':0, 'mon':1, 'tue':2, 'thu':3, 'wed':4, 'fri':5, 'sat':6};
                    return (str in day) ? day[str] : null;
                }
                function checkTime(time){
                    var reg = new RegExp(/^(0{0,1}[0-9]{1}|1[0-9]{1}|2[0-3]{1}):([0-5]{1}[0-9]{1})$/);
                    return reg.test(time);
                }
                function makeDate(date){
                    var d = new Date(date);
                    return Date.parse(d);
                }
                var d = new Date(), date, dateParse, nowDay, nextDay, keys, dayList = [];
                // 今日の曜日
                nowDay = d.getDay();
                // 明日の曜日
                nextDay = Math.abs((nowDay + 1 > 6) ? 0 : nowDay + 1);
                // 今日の日付
                date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate() + " ";
                dateParse = Date.parse(d);
                keys = Object.keys(cond.day);
                for(var i = 0; keys.length > i; i++){
                  if (!cond.day[keys[i]]) continue;
                  var day = translateDay(keys[i]);
                  if (day === null) continue;
                  // 曜日が今日若しくは明日ではない場合
                  if (day !== nowDay && day !== nextDay) continue;
                  // 時間指定なしの場合
                  if (Number(cond.timeSetting) === 2) return 0;
                  if (!checkTime(cond.startTime) || !checkTime(cond.endTime)) return null;
                  var startDate = makeDate(date + cond.startTime);
                  var endDate = makeDate(date + cond.endTime);

                  // 今日で開始中の場合
                  if (day === nowDay && startDate <= dateParse && dateParse < endDate ) {
                    return 0;
                  }
                  // 今日で開始前の場合
                  else if (day === nowDay && startDate > dateParse && dateParse < endDate ) {
                    return startDate-dateParse;
                  }
                  // 次回の場合
                  else {
                    var nextDate = startDate + 24*60*60*1000;
                    return nextDate-dateParse;
                  }
                }
            	return null;
            },
            referrer: function(cond){
                if (!('keyword' in cond) || !('referrerCond' in cond )) return null;
                if ( userInfo.referrer === "" ) return null;
                return (sinclo.trigger.common.pregMatch(cond.referrerCond, cond.keyword, userInfo.referrer)) ? 0 : null;
            },
            searchWord: function(cond){
                if (!('keyword' in cond) || !('searchCond' in cond )) return null;
                if ( userInfo.searchKeyword === null && Number(cond.searchCond) !== 3 ) return null;
                if ( userInfo.searchKeyword === null && Number(cond.searchCond) === 3 ) return true;
                return (sinclo.trigger.common.pregMatch(cond.searchCond, cond.keyword, userInfo.searchKeyword)) ? 0 : null;
            }
        }
    }
  };

}(sincloJquery));