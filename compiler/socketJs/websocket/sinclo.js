(function($){
  // -----------------------------------------------------------------------------
  //  websocket通信
  // -----------------------------------------------------------------------------

  sinclo = {
    syncTimeout: "",
    operatorInfo: {
      flg: false,
      ev: function() {
        var height = "45px";
        if ( !this.flg ) {
          height = (common.sincloBoxHeight + 55) + "px";
        }
        this.flg = !this.flg;
        $("#sincloBox").animate({
          height: height
        }, 'first');
      }
    },
    connect: function(){
      var firstConnection = false;
      // 新規アクセスの場合
      if ( !check.isset(userInfo.getTabId()) ) {
        firstConnection = true;
        window.opener = null;
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
        if ( Number(userInfo.accessType) === Number(cnst.access_type.guest) ) {
          emitData.connectToken = userInfo.connectToken;
          userInfo.syncInfo.get();
          emit('connectSuccess', {prev: userInfo.prev});
        }

        if ( check.isset(common.tmpParams) ) {
          browserInfo.resetPrevList();
          emit('requestSyncStart', {
            accessType: common.params.type
          });
        }

        var re = new RegExp("sincloData\=");
        if ( !re.test(browserInfo.href) ) {
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
            emit('requestSyncStop', emitData);
            emit('connected', {type: 'user',data: emitData});
            userInfo.syncInfo.unset();
          }, 5000);

        }
        return false;
      };
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
        common.makeAccessIdTag();
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
        prev: userInfo.prev,
        userAgent: window.navigator.userAgent,
        time: userInfo.time,
        ipAddress: userInfo.getIp(),
        referrer: userInfo.referrer
      });
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
      window.clearTimeout(this.syncTimeout);

      $("body").animate(
        {
          scrollLeft: browserInfo.scrollSize.x * obj.scrollPosition.x,
          scrollTop: browserInfo.scrollSize.y * obj.scrollPosition.y
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
    setWidgetInfo: function(d){
      var obj = JSON.parse(d);
      window.info.widgetDisplay = false; // デフォルト表示しない
      // ウィジェットを常に表示する
      if ( check.isset(obj.widget) && obj.widget.display_type === 1 ) {
        window.info.widgetDisplay = true;
        window.info.widget = obj.widget;
      }
      // オペレーターの数に応じて表示する
      else if ( check.isset(obj.widget) && obj.widget.display_type === 2 ) {
        if ( obj.widget.active_operator_cnt > 0 ) {
          window.info.widgetDisplay = true;
          window.info.widget = obj.widget;
        }
      }
      if (!window.info.widgetDisplay) {
        return false;
      }
      // 同期対象とするが、ウィジェットは表示しない
      if (check.isset(window.info['dataset']) && (check.isset(window.info.dataset['hide']) && window.info.dataset.hide === "1")) {
        window.info.widgetDisplay = false;
        return false;
      }
      // 画面同期中であれば抑制
      if ( check.isset(userInfo.connectToken) ) {
        return false;
      }
      var html = common.widgetTemplate();
      common.load.finish();
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox ) {
        sincloBox.parentNode.removeChild(sincloBox);
      }
      if ( userInfo.accessType !== cnst.access_type.host ) {
        $('body').append(html);
        common.sincloBoxHeight = 0;
        $("#sincloBox").children().each(function(){
          common.sincloBoxHeight = common.sincloBoxHeight + this.offsetHeight;
        });
        window.setTimeout(function(){
          if ( sinclo.operatorInfo.flg === false ) {
            sinclo.operatorInfo.flg = true;
            $("#sincloBox").animate({
              'height':  (common.sincloBoxHeight + 55) + 'px'
            }, 'first');
          }
        }, 3000);
        emit('syncReady', {widget: window.info.widgetDisplay});
      }

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
    syncStop: function(d){
      var obj = common.jParse(d);
      syncEvent.stop(false);
      userInfo.syncInfo.unset();
      common.makeAccessIdTag(userInfo.accessId);
    }
  };

}(sincloJquery));