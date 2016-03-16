(function($){
  // -----------------------------------------------------------------------------
  //  websocket通信
  // -----------------------------------------------------------------------------

  sinclo = {
    syncTimeout: "",
    operatorInfo: {
      ev: function(contentId) {
        var height = "45px";
        var elm = $('#' + contentId);
        var flg = elm.data('flg');
        if ( !flg ) {
          height = common[contentId + 'Height'] + "px";
        }
        elm.animate({
          height: height
        }, 'first')
        .data('flg', !flg);
      }
    },
    connect: function(){
      var firstConnection = false;
      // 新規アクセスの場合
      if ( !check.isset(userInfo.getTabId()) || check.isset(window.opener) ) {
        firstConnection = true;
        userInfo.strageReset();
        userInfo.setReferrer();
      }
      userInfo.init();
      var emitData = {
          referrer: userInfo.referrer,
          time: userInfo.getTime(),
          page: userInfo.getPage(),
          firstConnection: firstConnection,
          userAgent: window.navigator.userAgent,
          service: check.browser(),
          prev: userInfo.prev
        };

      // モニタリング中であればスルー
      if ( check.isset(userInfo.connectToken) ) {
        common.load.start();
        if ( Number(userInfo.accessType) !== Number(cnst.access_type.guest) ) {
          // emit('requestSyncStart', {});
        }
        else {
          emitData.connectToken = userInfo.connectToken;
          userInfo.syncInfo.get();
          emit('connectSuccess', {
            confirm: false,
            widget: window.info.widgetDisplay,
            subWindow: false,
            prev: userInfo.prev,
            userAgent: window.navigator.userAgent,
            time: userInfo.time,
            ipAddress: userInfo.getIp(),
            referrer: userInfo.referrer
          });
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

        browserInfo.setPrevList();

        if ( !check.isset(common.tmpParams) ) {
          emit('connectContinue', {
            connectToken: userInfo.connectToken,
            accessType: common.params.type,
          });

          window.clearTimeout(this.syncTimeout);
          this.syncTimeout = window.setTimeout(function(){
            userInfo.syncInfo.unset();
            emit('requestSyncStop', emitData);
            emit('connected', {type: 'user',data: emitData});
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
      if ( check.isset(obj.accessId) && !check.isset(obj.connectToken)) {
        userInfo.set(cnst.info_type.access, obj.accessId, true);
        common.makeAccessIdTag(userInfo.accessId);
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
        subWindow: false,
        prev: userInfo.prev,
        userAgent: window.navigator.userAgent,
        time: userInfo.time,
        ipAddress: userInfo.getIp(),
        referrer: userInfo.referrer
      });
    },
    connectConfirm: function(d) {
      var obj = common.jParse(d), subWindow = false;
      if ( userInfo.tabId !== obj.tabId ) return false;
      if ( userInfo.accessType === Number(cnst.access_type.host) ) {
        subWindow = true;
      }

      emit('connectSuccess', {
        confirm: true,
        widget: window.info.widgetDisplay,
        subWindow: subWindow,
        prev: userInfo.prev
      });
    },
    getAccessInfo: function(d) { // guest only
      var obj = common.jParse(d);
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var emitData = userInfo.getSendList();
      emitData.receiveAccessInfoToken = obj.token;
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
      if (window.confirm(location.host + ' が閲覧ページへのアクセスを求めています。\n許可しますか？')) {
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
      }
    },
    windowSyncInfo: function(d) {
      var obj = common.jParse(d);
      // 担当しているユーザーかチェック
      if ( obj.to !== userInfo.tabId ) return false;
      if ( Number(userInfo.accessType) !== Number(cnst.access_type.host) ) return false;
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
       var textareaInfo = [];
      $('textarea').each(function(){
        textareaInfo.push(this.value);
      });
       var selectInfo = [];
      $('select').each(function(){
        selectInfo.push(this.value);
      });
      var sincloContents = document.getElementById('sincloContents');
      if ( sincloContents ) {
        sincloContents.parentNode.removeChild(sincloContents);
      }

      emit('getSyncInfo', {
        userId: userInfo.userId,
        connectToken: userInfo.connectToken,
        inputInfo: inputInfo,
        textareaInfo: textareaInfo,
        selectInfo: selectInfo,
        // スクロール位置の取得
        scrollPosition: browserInfo.windowScroll()
      });
    },
    syncElement: function(d){
      var obj = common.jParse(d);
      window.clearTimeout(this.syncTimeout);

      $("body").animate(
        {
          scrollLeft:obj.scrollPosition.x,
          scrollTop:obj.scrollPosition.y
        },
        {
            duration: 'first',
            easing: 'swing',
            complete: function(){
              for ( var i in obj.inputInfo ) {
                var n = Number(i);
                $('input').eq(n).val(obj.inputInfo[n]);
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

          browserInfo.set.scroll(obj.scrollPosition);

          // TODO まだ微調整が必要
          setTimeout(function(){
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
    setWidgetInfo: function(d){
      var obj = JSON.parse(d);
      if ( obj.token !== common.token ) return false;
      if ( check.isset(obj.widget) && obj.widget.display_type === 1 ) {
        window.info.widgetDisplay = true;
        window.info.widget = obj.widget;
      }
      else if ( check.isset(obj.widget) && obj.widget.display_type === 2 ) {
        if ( obj.widget.active_operator_cnt > 0 ) {
          window.info.widgetDisplay = true;
          window.info.widget = obj.widget;
        }
      }
      else {
        window.info.widgetDisplay = false;
      }

      common.load.finish();
      var sincloContents = document.getElementById('sincloContents');
      if ( sincloContents ) {
        sincloContents.parentNode.removeChild(sincloContents);
      }
      if ( !check.isset(window.info.widget) ) return false;

      var html = common.createWidget();
      if ( !check.isset(sessionStorage.params) ) {
        $('body').append(html);
        common.sincloBoxHeight = 15;
        common.sincloChatBoxHeight = 15;
        $("#sincloBox").children().each(function(){
          if ( this.tagName !== "STYLE" && this.tagName !== "IMG" ) {
            common.sincloBoxHeight = common.sincloBoxHeight + $(this).outerHeight(true);
          }
        });

        $("#sincloChatBox").children().each(function(){
          if ( this.tagName !== "STYLE" && this.tagName !== "IMG" ) {
            common.sincloChatBoxHeight = common.sincloChatBoxHeight + $(this).outerHeight(true);
          }
        });
        // チャット情報読み込み
        sinclo.chatApi.init();

        window.setTimeout(function(){
          var elm = $('#sincloBox');
          if ( elm.data('flg') === false ) {
            elm.data('flg', true).animate({
              'height':  common.sincloBoxHeight + 'px'
            }, 'first');
          }
        }, 3000);
      }

    },
    resUrlChecker: function(d){
      var obj = JSON.parse(d);
      if ( obj.connectToken !== userInfo.connectToken ) return false;
      if ( obj.accessType === userInfo.accessType ) return false;
      if ( obj.to === userInfo.tabId ) {
        if ( obj.url !== browserInfo.href ) {
          common.load.start();
          location.href = obj.url;
        }
        else {
          emit('requestSyncStart', {
            accessType: common.params.type
          });
        }
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
      }
      else {
        alert('メッセージの送信に失敗しました。');
      }
    },
    syncStop: function(d){
      var obj = common.jParse(d);
      if ( obj.connectToken !== userInfo.connectToken ) return false;
      if ( obj.tabId !== userInfo.tabId ) return false;
      if (check.isset(common.tmpParams) || check.isset(sessionStorage.params)) return false;
      syncEvent.stop(false);
      userInfo.syncInfo.unset();
      common.makeAccessIdTag(userInfo.accessId);
    },
    chatApi: {
      historyId: null,
      messageType: {
        customer: 1,
        company: 2
      },
      init: function(){
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
    }
  };

}(sincloJquery));