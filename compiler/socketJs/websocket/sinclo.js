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
          sincloBox.setAttribute('data-openflg', true);

          if ( check.smartphone() && window.info.contract.chat && (window.screen.availHeight < window.screen.availWidth) ) {
            height = window.innerHeight * (document.body.clientWidth / window.innerWidth);
          }
          else {
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
          }
          if ( window.info.contract.chat ) {
            sinclo.chatApi.showUnreadCnt();
            sinclo.chatApi.scDown();
          }
        }
        else {
          height = this.header.offsetHeight;
          sincloBox.setAttribute('data-openflg', false);
        }
        elm.animate({
          height: height + "px"
        }, 'first');
      },
      widgetHide: function() {
        var sincloBox = document.getElementById('sincloBox');
        if ( !sincloBox ) return false;
        var openflg = sincloBox.getAttribute('data-openflg');

        var height = document.getElementById('widgetTitle').clientHeight;
        if ( height === 0 ) {
          height = 60;
        }
        var enableArea = browserInfo.scrollSize().y - height;
        if ( enableArea < window.scrollY && String(openflg) === "false" ) {
          sincloBox.style.opacity = 0;
        }
        else {
          sincloBox.style.opacity = 1;
        }
        setTimeout(function(){
          if ( Number(sincloBox.style.opacity) === 0 ) {
            sincloBox.style.display = "none";
          }
          else {
            sincloBox.style.display = "block";
          }
        }, 500);
      },
      reCreateWidgetMessage: "",
      reCreateWidgetTimer: null,
      reCreateWidget: function(){
        if (!check.smartphone()) return false; // 念のため
        if ( sinclo.operatorInfo.reCreateWidgetTimer ) {
          clearTimeout(sinclo.operatorInfo.reCreateWidgetTimer);
        }
        var sincloBox = document.getElementById('sincloBox');

        var screen = ( window.screen.availHeight < window.screen.availWidth ) ? 'horizontal' : 'vertical';
        var current = document.activeElement;
        if ( current.id === "sincloChatMessage" && screen === sincloBox.getAttribute('data-screen') ) {
          setTimeout(function(){
            sinclo.operatorInfo.reCreateWidget();
          }, 500);
          return false;
        }
        var openFlg = sincloBox.getAttribute('data-openflg');

        if ( sincloBox ) {
          sincloBox.style.display = "none";
        }


        sinclo.operatorInfo.reCreateWidgetTimer = setTimeout(function(){
          var html = common.createWidget();
          var chatTalk = $("sinclo-chat").children();
          sinclo.operatorInfo.reCreateWidgetMessage = document.getElementById('sincloChatMessage').value;

          $("#sincloBox").remove();
          $("body").append(html);
          $("sinclo-chat").append(chatTalk);
          var sincloBox = document.getElementById('sincloBox');
          document.getElementById('sincloChatMessage').value = sinclo.operatorInfo.reCreateWidgetMessage;
          sincloBox.style.display = "block";
          sincloBox.style.opacity = 0;
          sinclo.operatorInfo.header = document.getElementById('widgetHeader');
          sincloBox.setAttribute('data-openflg', openFlg);
          sinclo.operatorInfo.widgetHide();

          if ( String(openFlg) === "true" ) {
            sincloBox.setAttribute('data-openflg', true);

            if ( window.screen.availHeight < window.screen.availWidth ) {
              sincloBox.style.height = document.documentElement.clientHeight + "px";
            }
            else {
              var height = $("#widgetHeader").outerHeight(true);
              height += $("#chatTab").outerHeight(true);
              sincloBox.style.height = height;
            }
          }
          else {
            sincloBox.setAttribute('data-openflg', false);
            sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
          }

          sincloBox.setAttribute('data-screen', screen); // 画面の向きを制御

          sinclo.chatApi.showUnreadCnt();
          sinclo.chatApi.scDown();

        }, 500);
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
          firstConnection: userInfo.firstConnection,
          userAgent: window.navigator.userAgent,
          service: check.browser(),
          prevList: userInfo.prevList
        };

      // チャットの契約をしている場合
      if ( window.info.contract.chat ) {
        sinclo.chatApi.observeType.emit(false, "");
      }

      // モニタリング中であればスルー
      if ( check.isset(userInfo.connectToken) ) {
        common.load.start();
        if ( Number(userInfo.accessType) === Number(cnst.access_type.guest) ) {
          emitData.connectToken = userInfo.connectToken;
          userInfo.syncInfo.get();
          common.judgeShowWidget();
          emit('connectSuccess', {prevList: userInfo.prevList, prev: userInfo.prev});
          emit('connectedForSync', {});

          // チャットの契約をしている場合はウィジェット表示
          if ( window.info.contract.chat ) {
            common.makeAccessIdTag();
          }
        }

        if ( check.isset(common.tmpParams) ) {
          browserInfo.resetPrevList();
          emit('requestSyncStart', {
            accessType: common.params.type
          });
        }
        emit('reqUrlChecker', {});

        browserInfo.setPrevList();

        if ( !check.isset(common.tmpParams) ) {
          emit('connectContinue', {
            connectToken: userInfo.connectToken,
            accessType: common.params.type,
            receiverID: userInfo.vc_receiverID
          });

          var vcInfo = common.getVcInfo();
          if(typeof vcInfo !== 'undefined') {
            vcPopup.set(vcInfo.toTabId, vcInfo.receiverID);
          }

          window.clearTimeout(this.syncTimeout);
          this.syncTimeout = window.setTimeout(function(){
            emit('requestSyncStop', emitData);
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
    retConnectedForSync: function (d) {
      var obj = common.jParse(d);
      if ( ('pagetime' in obj) ) {
        userInfo.pageTime = obj['pagetime'];
      }
      if ( ('activeOperatorCnt' in obj) ) {
        window.info.activeOperatorCnt = obj['activeOperatorCnt'];
      }
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

      obj['prev'] = userInfo.prev;
      obj.stayCount = userInfo.getStayCount();

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
    setHistoryId: function(){
        var createStartTimer,
            createStart = function(){
                var sincloBox = document.getElementById('sincloBox');
                var widgetOpen = storage.s.get('widgetOpen');
                if ( window.info.contract.chat && check.smartphone() ) {
                  sincloBox.style.display = "block";
                  sincloBox.style.opacity = 0;
                  sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
                  sinclo.operatorInfo.widgetHide();
                }
                else {
                  sincloBox.style.display = "block";
                  sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
                }
                // ウィジェット表示
                if ( !(('showTime' in window.info.widget) && ('maxShowTime' in window.info.widget) && String(window.info.widget.maxShowTime).match(/^[0-9]{1,2}$/) !== null) ) return false;
                var showTime = String(window.info.widget.showTime);
                var maxShowTime = Number(window.info.widget.maxShowTime) * 1000;
                if ( showTime === "2" ) return false; // 常に最大化しない
                if ( showTime === "1" ) { // サイト訪問後
                  if (widgetOpen) return false;
                }
                // 常に最大化する、ページ訪問時（showTime === 3,4）
                window.setTimeout(function(){
                  var flg = sincloBox.getAttribute('data-openflg');
                  if ( String(flg) === "false" ) {
                    storage.s.set('widgetOpen', true);
                    sinclo.operatorInfo.ev();
                  }
                }, maxShowTime);


                if ( window.info.contract.chat ) {
                    // チャット情報読み込み
                    sinclo.chatApi.init();
                }
        };

        createStartTimer = window.setInterval(function(){
          if (window.info.widgetDisplay && !sinclo.trigger.flg) {
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
      emitData.stayCount = userInfo.getStayCount();
      emit('sendAccessInfo', emitData);
    },
    confirmCustomerInfo: function(d) {
      var obj = common.jParse(d);
      if ( userInfo.tabId !== obj.tabId ) return false;
      if ( userInfo.accessType !== cnst.access_type.guest ) return false;
      var emitData = userInfo.getSendList();
      emitData.receiveAccessInfoToken = obj.token;
      emitData.stayCount = userInfo.getStayCount();
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
    getWindowInfo: function(obj) {
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

/*        vcPopup.set(userInfo.tabId, userInfo.vc_receiverID);

        // sendWindowInfoとほぼ同時にメッセージを送信してしまうと
        // 企業側がFireFoxの場合windowを開くタイミングでapplyができないためウェイトを挟む
        setTimeout(function(){
          emit('videochatConfirmOK', {
            userId: userInfo.userId,
            fromTabId: userInfo.tabId,
            fromConnectToken: userInfo.connectToken,
            receiverID: userInfo.vc_receiverID
          });
        }, 300);
        // 開始したタイミングでビデオチャット情報をセッションストレージに保存
        common.saveVcInfo();*/
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
      var sincloBox = document.getElementById('sincloBox');
      // チャット未契約のときはウィジェットを非表示
      if (sincloBox && !window.info.contract.chat) {
        sincloBox.style.display = "none";
      }
      common.load.start();
      userInfo.setConnect(obj.connectToken);
      if ( !check.isset(userInfo.sendTabId) ) {
        userInfo.sendTabId = obj.tabId;
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
      emit('sendTabInfo', { status: browserInfo.getActiveWindow() });
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
      if ( obj.to !== userInfo.tabId && obj.tabId !== userInfo.tabId ) return false;
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
        location.href = obj.url;
      }
      else {
        emit('requestSyncStart', {
          accessType: common.params.type
        });
      }
    },
    chatStartResult: function(d){
      var obj = JSON.parse(d), opUser;
      this.chatApi.online = true;
      storage.s.set('chatAct', true); // オートメッセージを表示しない

      if ( info.widget.showName === 1 ) {
        sinclo.chatApi.opUser = obj.userName;
        opUser = obj.userName;
      }
      else if ( info.widget.showName === 2 && String(obj.hide) === "true" ) {
        return false;
      }

      if ( check.isset(opUser) === false ) {
        opUser = "オペレーター";
      }
      check.escape_html(opUser); // エスケープ

      sinclo.chatApi.createNotifyMessage(opUser + "が入室しました");
    },
    chatEndResult: function(d){
      var obj = JSON.parse(d);
      this.chatApi.online = false;
      storage.s.set('chatAct', false); // オートメッセージを表示してもいい
      var opUser = sinclo.chatApi.opUser;
      if ( check.isset(opUser) === false ) {
        opUser = "オペレーター";
      }
      check.escape_html(opUser); // エスケープ
      sinclo.chatApi.createNotifyMessage(opUser + "が退室しました");
      sinclo.chatApi.opUser = "";
    },
    chatMessageData:function(d){
      var obj = JSON.parse(d);
      if ( obj.token !== common.token ) return false;
      this.chatApi.historyId = obj.chat.historyId;
      var keys = Object.keys(obj.chat.messages);
      for (var key in obj.chat.messages) {
        if ( !obj.chat.messages.hasOwnProperty(key) ) return false;
        var chat = obj.chat.messages[key], userName;
        if ( Number(chat.messageType) < 90 ) {
          var cn = (Number(chat.messageType) === 1) ? "sinclo_se" : "sinclo_re";
          if (Number(chat.messageReadFlg) === 0 && chat.messageType === sinclo.chatApi.messageType.company) {
              this.chatApi.unread++;
          }

          // オートメッセージか、企業からのメッセージで表示名を使用しない場合
          if ( Number(chat.messageType) === 3 || (Number(chat.messageType) === 2 && window.info.widget.showName !== 1) ) {
            userName = window.info.widget.subTitle;
          }
          else if ( Number(chat.messageType) === 2 ) {
            userName = chat.userName;
          }
          this.chatApi.createMessage(cn, chat.message, userName);
        }
        else {
          if ( ('userName' in obj.chat.messages[key]) ) {
            sinclo.chatApi.opUser = obj.chat.messages[key].userName;
          }
          // 途中で設定が変更されたときの対策
          if ( info.widget.showName !== 1 ) {
            sinclo.chatApi.opUser = "";
          }
          var opUser = sinclo.chatApi.opUser;

          if ( sinclo.chatApi.opUser === "" ) {
            opUser = "オペレーター";
          }
          check.escape_html(opUser); // エスケープ

          if ( Number(chat.messageType) === sinclo.chatApi.messageType.start ) {
            this.chatApi.online = true;
            this.chatApi.createNotifyMessage(opUser + "が入室しました");
          }
          if ( Number(chat.messageType) === sinclo.chatApi.messageType.end ) {
            this.chatApi.online = false;
            this.chatApi.createNotifyMessage(opUser + "が退室しました");
            sinclo.chatApi.opUser = "";
          }
        }
      }
      if ( !this.chatApi.online && !sinclo.trigger.flg ) {
        // オートメッセージ読み込み
        sinclo.trigger.init();
      }
      // 未読数
      sinclo.chatApi.showUnreadCnt();
    },
    sendChatResult: function(d){
      var obj = JSON.parse(d);
      if ( obj.tabId !== userInfo.tabId ) return false;
      var elm = document.getElementById('sincloChatMessage'), cn, userName = "";

      if ( obj.ret ) {
        // スマートフォンの場合はメッセージ送信時に、到達確認タイマーをリセットする
        if ( sinclo.chatApi.sendErrCatchTimer !== null ) {
          clearTimeout(sinclo.chatApi.sendErrCatchTimer);
        }

        if (obj.messageType === sinclo.chatApi.messageType.company) {
          cn = "sinclo_re";
          sinclo.chatApi.call();
          userName = sinclo.chatApi.opUser;
        }
        else if (obj.messageType === sinclo.chatApi.messageType.customer) {
          cn = "sinclo_se";
          elm.value = "";
        }
        if (obj.messageType === sinclo.chatApi.messageType.auto) {
          return false;
        }
        this.chatApi.createMessageUnread(cn, obj.chatMessage, userName);
        // オートメッセージの内容をDBに保存し、オブジェクトから削除する
        if (!sinclo.chatApi.saveFlg) {
          emit("sendAutoChat", {messageList: sinclo.chatApi.autoMessages});
          sinclo.chatApi.autoMessages = [];
          sinclo.chatApi.saveFlg = true;
        }
      }
      else {
        alert('メッセージの送信に失敗しました。');
      }
    },
    sendReqAutoChatMessages: function(d){
      // 自動メッセージの情報を渡す（保存の為）
      var obj = common.jParse(d);
      emit("sendAutoChatMessages", {messages: sinclo.chatApi.autoMessages, sendTo: obj.sendTo});
      var value = "";
      if (window.info.widgetDisplay) {
        value = document.getElementById('sincloChatMessage').value;
      }
      // 入力中のステータスを送る
      sinclo.chatApi.observeType.emit(sinclo.chatApi.observeType.status, value);
    },
    resAutoChatMessage: function(d){
        var obj = JSON.parse(d);

        sinclo.chatApi.autoMessages.push({
            chatId:obj.chatId,
            message: obj.message,
            created: obj.created
        });
    },
    confirmVideochatStart: function(obj) {
      // ビデオチャット開始に必要な情報をオペレータ側から受信し、セットする
      if ( obj.toTabId !== userInfo.tabId ) return false;
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      userInfo.vc_receiverID = obj.receiverID;
      userInfo.vc_toTabId = obj.toTabId;
      common.setVcInfo({receiverID: obj.receiverID, toTabId: obj.toTabId});
    },
    syncStop: function(d){
      var obj = common.jParse(d);
      syncEvent.stop(false);
      window.clearTimeout(sinclo.syncTimeout);

      userInfo.syncInfo.unset();
      if (!document.getElementById('sincloBox')) {
        common.makeAccessIdTag();
      }

      // 終了通知
      var title = location.host + 'の内容';
      var content = location.host + 'との画面共有を終了しました';
      popup.ok = function(){
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);

      var timer = setInterval(function(){
        if (window.info.widgetDisplay === false) {
          clearInterval(timer);
          return false;
        }
        var sincloBox = document.getElementById('sincloBox');
        // チャット未契約のときはウィジェットを非表示
        if (sincloBox && !window.info.contract.chat) {
          sincloBox.style.display = "block";
          sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
          sincloBox.setAttribute('data-openflg', false);
          clearInterval(timer);
        }

      }, 500);
    },
    chatApi: {
        saveFlg: false,
        online: false, // 現在の対応状況
        historyId: null,
        unread: 0,
        opUser: "",
        messageType: {
            customer: 1,
            company: 2,
            auto: 3,
            start: 98,
            end: 99
        },
        autoMessages: [],
        init: function(){
            if ( window.info.contract.chat ) {
                if ( !( 'chatTrigger' in window.info.widget && window.info.widget.chatTrigger === 2) ) {
                    $(document).on("keydown", "#sincloChatMessage", function(e){
                        if ( (e.which && e.which === 13) || (e.keyCode && e.keyCode === 13) ) {
                            if ( !e.shiftKey && !e.ctrlKey ) {
                                sinclo.chatApi.push();
                            }
                        }
                    });
                }
                $(document).on("focus", "#sincloChatMessage", function(e){
                  sinclo.chatApi.observeType.start();
                });
            }

            this.sound = document.getElementById('sinclo-sound');
            if ( this.sound ) {
                this.sound.volume = 0.3;
            }

            $(document)
              .on('focus', "#sincloChatMessage",function(e){
                var message = document.getElementById('sincloChatMessage');
                message.placeholder = "";
              })
              .on('blur', "#sincloChatMessage",function(e){
                var message = document.getElementById('sincloChatMessage');
                message.placeholder = "メッセージを入力してください";
                if ( !( 'chatTrigger' in window.info.widget && window.info.widget.chatTrigger === 2) ) {
                  if ( check.smartphone() ) {
                    message.placeholder += "（改行で送信）";
                  }
                  else {
                    message.placeholder += "（Shift+Enterで改行/Enterで送信）";
                  }
                }
              })
              .on("change", "input[name^='sinclo-radio']", function(e){
                sinclo.chatApi.send(e.target.value);
              });

            emit('getChatMessage', {showName: info.widget.showName});
        },
        createNotifyMessage: function(val){
            var chatList = document.getElementsByTagName('sinclo-chat')[0];
            var li = document.createElement('li');
            chatList.appendChild(li);
            li.className = "sinclo_etc";
            li.innerHTML = "－ " + check.escape_html(val) + " －";
            this.scDown();
        },
        createTypingTimer: null,
        createTypingMessage: function(d){
            var obj = JSON.parse(d),
                opUser = sinclo.chatApi.opUser,
                chatType = document.getElementsByTagName('sinclo-typing')[0],
                typeMessage = document.getElementById('sinclo_typeing_message'),
                li = document.createElement('li'),
                span = document.createElement('span');

            var calcMergin = function(opUser){
              var margin = (opUser.length + 4)/2;
              span.style.marginLeft = "-" + margin + "em";
            };

            if ( obj.status === false ) {
              if ( typeMessage ) {
                typeMessage.parentNode.removeChild(typeMessage);
              }
              clearInterval(this.createTypingTimer);
              return false;
            }

            if ( check.isset(opUser) === false ) {
              opUser = "オペレーター";
            }

            opUser = check.escape_html(opUser); // エスケープ

            if ( !typeMessage ) {
              li.appendChild(span);
              chatType.appendChild(li);
              li.id = "sinclo_typeing_message";
              span.textContent = opUser + "が入力中";
              calcMergin(opUser);
            }

            this.createTypingTimer = setInterval(function(){
              calcMergin(opUser);

              if (span.textContent.length > opUser.length + 6 ) {
                span.textContent = opUser + "が入力中";
              }
              else {
                span.textContent += ".";
              }
            }, 500);
            var chatTalk = document.getElementById('chatTalk');
            $('#chatTalk').animate({
              scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
            }, 300);
        },
        createMessage: function(cs, val, cName){
            var chatList = document.getElementsByTagName('sinclo-chat')[0];
            var li = document.createElement('li');
            chatList.appendChild(li);
            var strings = val.split('\n');
            var radioCnt = 1;
            var linkReg = RegExp(/http(s)?:\/\/[!-~.a-z]*/);
            var radioName = "sinclo-radio" + chatList.children.length;
            var content = "";

            if ( check.isset(cName) === false ) {
              cName = window.info.widget.subTitle;
            }
            check.escape_html(cName); // エスケープ

            if ( cs === "sinclo_re" ) {
              content = "<span class='cName'>" + cName + "</span>";
            }
            for (var i = 0; strings.length > i; i++) {
                var str = check.escape_html(strings[i]);

                if ( cs === "sinclo_re" ) {
                    // ラジオボタン
                    var radio = str.indexOf('[]');
                    if ( radio > -1 ) {
                        var val = str.slice(radio+2);
                        str = "<sinclo-radio><input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + val + "'>";
                        str += "<label for='" + radioName + "-" + i + "'>" + val + "</label></sinclo-radio>";
                    }
                }
                // リンク
                var link = str.match(linkReg);
                if ( link !== null ) {
                    var url = link[0];
                    var a = "<a href='" + url + "' target='_blank'>" + url + "</a>";
                    str = str.replace(url, a);
                }
                content += str + "\n";

            }
            li.className = cs;
            li.innerHTML = content;
            this.scDown();
        },
        createMessageUnread: function(cs, val, name){
            if ( cs === "sinclo_re" ) {
                sinclo.chatApi.unread++;
                sinclo.chatApi.showUnreadCnt();
            }
            sinclo.chatApi.createMessage(cs, val, name);
        },
        scDownTimer: null,
        scDown: function(){
          if ( this.scDownTimer ) {
            clearTimeout(this.scDownTimer);
          }
          this.scDownTimer = setTimeout(function(){
            var chatTalk = document.getElementById('chatTalk');
            $('#chatTalk').animate({
              scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
            }, 300);
          }, 500);
        },
        push: function(){
            sinclo.operatorInfo.reCreateWidgetMessage = ""; // 送信したら空にする

            var elm = document.getElementById('sincloChatMessage');
            if ( check.isset(elm.value) ) {
                this.send(elm.value);
            }
        },
        send: function(value){
            storage.s.set('chatAct', true); // オートメッセージを表示しない

            // タイマーが仕掛けられていたらリセット
            if ( this.sendErrCatchTimer !== null ) {
              clearTimeout(this.sendErrCatchTimer);
            }

            setTimeout(function(){
              emit('sendChat', {
                  historyId: sinclo.chatApi.historyId,
                  chatMessage:value,
                  mUserId: null,
                  messageType: sinclo.chatApi.messageType.customer
              });
            }, 100);

            // スマートフォンの場合、タイマーをセット。（メッセージ送信に失敗した場合にリロードを促す）
            if ( check.smartphone() ) {
              this.sendErrCatch();
            }

        },
        observeType: { // 入力中監視処理
          timer: null,
          prevMessage: "",
          status: false,
          start: function(){ // タイピング監視処理
            var sendMessage = document.getElementById('sincloChatMessage');
            if ( this.timer !== null ) {
              clearInterval(this.timer);
            }
            // 300ミリ秒ごとに入力値をチェック
            this.timer = setInterval(function(){
              if ( sendMessage.value === "" ) {
                sinclo.chatApi.observeType.prevMessage = "";
                sinclo.chatApi.observeType.send(false, sendMessage.value);
              }
              else if ( sendMessage.value !== sinclo.chatApi.observeType.prevMessage ) {
                sinclo.chatApi.observeType.prevMessage = sendMessage.value;
                sinclo.chatApi.observeType.send(true, sendMessage.value);
              }
            }, 300);
          },
          send: function(status, message){ // 状態の逐一送信処理
            if ( sinclo.chatApi.observeType.status !== status || (status === true && message !== "")  ) {
              sinclo.chatApi.observeType.emit(status, message);
              sinclo.chatApi.observeType.status = status;
            }
          },
          emit: function(status, message){ // 状態の送信処理
            emit('sendTypeCond', { type: 2, status: status, message: message });
          }
        },
        sendErrCatchFlg: false,
        sendErrCatchTimer: null,
        sendErrCatch: function(){
          if ( this.sendErrCatchTimer !== null ) {
            clearTimeout(this.sendErrCatchTimer);
          }
          this.sendErrCatchTimer = setTimeout(function(){
            $("sinclo-chat-alert").css('display', 'block');
            sinclo.chatApi.sendErrCatchFlg = true;
          }, 5000);
        },
        sound: null,
        call: function(){
            // デスクトップ通知用
            if ( this.sound ) {
                this.sound.play();
            }
        },
        showUnreadCnt: function(){
            var elmId = "sincloChatUnread",
                unreadIcon = document.getElementById(elmId);
            var sincloBox = document.getElementById('sincloBox');
            var flg = sincloBox.getAttribute('data-openflg');
            if ( unreadIcon ) {
                unreadIcon.parentNode.removeChild(unreadIcon);
            }
            if ( Number(sinclo.chatApi.unread) > 0 ) {
                if ($("#chatTab").css("display") !== "none" && String(flg) === "true") {
                    emit("isReadFromCustomer", {});
                    sinclo.chatApi.unread = 0;
                    return false;
                }

                var em = document.createElement('em');
                em.id = elmId;
                em.textContent = sinclo.chatApi.unread;
                var mainImg = document.getElementById('mainImage');
                var titleElm = document.getElementById('widgetTitle');
                if ( mainImg ) {
                    mainImg.appendChild(em);
                }
                else if (titleElm) {
                    titleElm.appendChild(em);
                }
            }
        }
    },
    trigger: {
        flg: false,
        nowSaving: false,
        init: function(){
            if ( !('messages' in window.info) || (('messages' in window.info) && typeof(window.info.messages) !== "object" ) ) return false;
            this.flg = true;
            var messages = window.info.messages;
            // 設定ごと
            for( var i = 0; messages.length > i; i++ ){
                // AND
                if ( Number(messages[i]['activity']['conditionType']) === 1 ) {
                    this.setAndSetting(i, messages[i]['activity'], function(key, ret){
                        var message = messages[key];
                        if (typeof(ret) === 'number') {
                            setTimeout(function(){
                                sinclo.trigger.setAction(message['id'], message['action_type'], message['activity']);
                            }, ret);
                        }
                    });
                }
                // OR
                else {
                    this.setOrSetting(i, messages[i]['activity'], function(key, ret){
                        var message = messages[key];
                        if (typeof(ret) === 'number') {
                            setTimeout(function(){
                                sinclo.trigger.setAction(message['id'], message['action_type'], message['activity']);
                            }, ret);
                        }
                    });
                }
            }
        },
        /**
         * return 即時実行(0)、タイマー実行(ミリ秒)、非実行(null)
         */
        setAndSetting: function(key, setting, callback) {
            var keys = Object.keys(setting['conditions']);
            var ret = 0;
            for(var i = 0; keys.length > i; i++){

                var conditions = setting['conditions'][keys[i]];
                var last = (keys.length === Number(i+1)) ? true : false;
                switch(Number(keys[i])) {
                    case 1: // 滞在時間
                        this.judge.stayTime(conditions[0], function(err, timer){
                            if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                ret = Number(timer);
                            }
                            if (err) ret = null;
                        });
                        break;
                    case 2: // 訪問回数
                        this.judge.stayCount(conditions[0], function(err, timer){
                            if (err) ret = null;
                        });
                        break;
                    case 3: // ページ
                        this.judge.page(conditions[0], function(err, timer){
                            if (err) ret = null;
                        });
                        break;
                    case 4: // 曜日・時間
                        this.judge.dayTime(conditions[0], function(err, timer){
                            if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                ret = Number(timer);
                            }
                            if (err) ret = null;
                        });
                        break;
                    case 5: // リファラー
                        this.judge.referrer(conditions[0], function(err, timer){
                            if (err) ret = null;
                        });
                        break;
                    case 6: // 検索ワード
                        this.judge.searchWord(conditions[0], function(err, timer){
                            if (err) ret = null;
                        });
                        break;
                    default:
                        ret = null;
                        break;
                }
                if (ret === null) break;
            }
            callback(key, ret);
        },
        /**
         * return 即時実行(0)、タイマー実行(ミリ秒)、非実行(null)
         */
        setOrSetting: function(key, setting, callback) {
            var keys = Object.keys(setting['conditions']);
            var ret = null;
            for(var i = 0; keys.length > i; i++){
                var conditions = setting['conditions'][keys[i]];
                var last = (keys.length === Number(i+1)) ? true : false;
                switch(Number(keys[i])) {
                    case 1: // 滞在時間
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.stayTime(conditions[u], function(err, timer){
                              if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                  ret = Number(timer);
                              }
                          });
                        }
                        break;
                    case 2: // 訪問回数
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.stayCount(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 3: // ページ
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.page(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 4: // 曜日・時間
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.dayTime(conditions[u], function(err, timer){
                              if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                  ret = Number(timer);
                              }
                          });
                        }
                        break;
                    case 5: // リファラー
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.referrer(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 6: // 検索ワード
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.searchWord(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    default:
                        break;
                }
            }

            callback(key, ret);
        },
        setAutoMessage: function(id, cond){
            var data = {
                chatId:id,
                message:cond.message
            };

            if ( sinclo.chatApi.saveFlg ) {
                // オートメッセージの内容をDBに保存し、オブジェクトから削除する
                emit("sendAutoChat", {messageList: [data]});
            }
            else {
                emit('sendAutoChatMessage', data);
            }
        },
        setAction: function(id, type, cond){
            // TODO 今のところはメッセージ送信のみ、拡張予定
            var chatActFlg = storage.s.get('chatAct');
            if ( !check.isset(chatActFlg) ) {
              chatActFlg = "false";
            }

            if ( String(type) === "1" && ('message' in cond) && (String(chatActFlg) === "false") ) {
                sinclo.chatApi.createMessageUnread("sinclo_re", cond.message, info.widget.subTitle);
                var prev = sinclo.chatApi.autoMessages;

                var setAutoMessageTimer = setInterval(function(){
                    var date = common.fullDateTime();
                    if ( prev.length === 0 || (prev.length > 0 && prev[prev.length - 1].created !== date) ) {
                        clearInterval(setAutoMessageTimer);
                        sinclo.trigger.setAutoMessage(id, cond);
                        // 自動最大化
                        if ( !('widgetOpen' in cond) ) return false;
                        var flg = sincloBox.getAttribute('data-openflg');
                        if ( Number(cond.widgetOpen) === 1 && String(flg) === "false" ) {
                          sinclo.operatorInfo.ev();
                        }
                    }
                }, 1);

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
                      if (Number(a) >= Number(b) ) return true;
                      break;
                    case 3: // 未満
                      if (Number(a) < Number(b) ) return true;
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
            stayTime: function(cond, callback){
                if (!('stayTimeCheckType' in cond) || !('stayTimeType' in cond) || !('stayTimeRange' in cond )) return callback(true, null);
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

                // ページ
                if ( Number(cond.stayTimeCheckType) === 1 ) {
                    callback(false, time);
                }
                // サイト
                else {
                    var term = (Number(userInfo.pageTime) - Number(userInfo.time));
                    if ( term <= time ) {
                        callback(false, (time-term));
                    }
                    else {
                        callback(true, null);
                    }
                }

            },
            stayCount: function(cond, callback){
                if (!('visitCntCond' in cond) || !('visitCnt' in cond )) return callback(true, null);
                if (sinclo.trigger.common.numMatch(cond.visitCntCond, userInfo.getStayCount(), cond.visitCnt)) {
                    callback(false, 0);
                }
                else {
                    callback(true, null);
                }
            },
            page: function(cond, callback){
                if (!('keyword' in cond) || !('targetName' in cond ) || !('stayPageCond' in cond )) return callback(true, null);
                var target = ( Number(cond.targetName) === 1 ) ? common.title() : location.href ;
                if (sinclo.trigger.common.pregMatch(cond.stayPageCond, cond.keyword, target)) {
                    callback(false, 0);
                }
                else {
                    callback(true, null);
                }
            },
            dayTime: function(cond, callback){
                if (!('day' in cond) || !('timeSetting' in cond)) return callback(true, null );
                if (Number(cond.timeSetting) === 1 && (!('startTime' in cond) || !('endTime' in cond))) return callback(true, null);
                // DBに保存している文字列から、JSのgetDay関数に対応する数値を返す関数
                function translateDay(str){
                    var day = {'sun':0, 'mon':1, 'tue':2, 'wed':3, 'thu':4, 'fri':5, 'sat':6};
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
                  if (!cond.day[keys[i]]) {
                    if ((keys.length - 1) === i) return callback(true, null); // 最終行だった場合はfalse
                    continue;
                  }
                  // 曜日が取得できなければContinue.
                  var day = translateDay(keys[i]); // 曜日を取得
                  if (day === null) {
                    if ((keys.length - 1) === i) return callback(true, null); // 最終行だった場合はfalse
                    continue;
                  }
                  // 曜日が今日若しくは明日ではない場合はContinue
                  if (day !== nowDay && day !== nextDay) {

                    if ((keys.length - 1) === i) return callback(true, null); // 最終行だった場合はfalse
                    continue;
                  }
                  // 曜日が今日で時間指定なしの場合は即時表示
                  if (day === nowDay && Number(cond.timeSetting) === 2) {
                    return callback(false, 0);
                  }
                  // 時間指定ありで、開始・終了時間が取得できない場合は終了
                  if (!checkTime(cond.startTime) || !checkTime(cond.endTime)) return callback(true, null);
                  var startDate = makeDate(date + cond.startTime);
                  var endDate = makeDate(date + cond.endTime);
                  // 今日で開始中の場合
                  if (day === nowDay && startDate <= dateParse && dateParse < endDate ) {
                    return callback(false, 0); // 即時表示
                  }
                  // 今日で開始前の場合
                  else if (day === nowDay && startDate > dateParse && dateParse < endDate ) {
                    return callback(false, (startDate-dateParse) ); // 開始時間に表示されるようにタイマーセット
                  }
                  // 次回の場合
                  else if ( day === nextDay) {
                    var nextDate = startDate + 24*60*60*1000;
                    return callback(false, (nextDate-dateParse)); // 開始時間に表示されるようにタイマーセット
                  }
                  else {
                    return callback(true, null);
                  }
                }
            },
            referrer: function(cond, callback){
                if (!('keyword' in cond) || !('referrerCond' in cond )) return callback(true, null );
                if ( userInfo.referrer === "" ) return callback(true, null );
                if (sinclo.trigger.common.pregMatch(cond.referrerCond, cond.keyword, userInfo.referrer)) {
                    callback(false, 0);
                }
                else {
                    callback(true, null);
                }
            },
            searchWord: function(cond, callback){
                if (!('keyword' in cond) || !('searchCond' in cond )) return callback( true, null );
                if ( userInfo.searchKeyword === null && Number(cond.searchCond) !== 3 ) return callback( true, null );
                if ( userInfo.searchKeyword === null && Number(cond.searchCond) === 3 ) return callback( false, 0 );
                if (sinclo.trigger.common.pregMatch(cond.searchCond, cond.keyword, userInfo.searchKeyword)) {
                    callback(false, 0);
                }
                else {
                    callback(true, null);
                }
            }
        }
    }
  };

  sincloVideo = {
    open: function(obj){
      window.open(
        "https://ap1.sinclo.jp/index.html?userId=" + userInfo.userId,
        "monitor_" + userInfo.userId,
        "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
      );

      return false;
    }
  };

}(sincloJquery));
