(function(jquery){
  // -----------------------------------------------------------------------------
  //   websocket通信
  // -----------------------------------------------------------------------------

  var $ = jquery;
  sinclo = {
    widget: {
      condifiton: {
        get: function(){
          var sincloBox = document.getElementById('sincloBox');
          if(storage.s.get("widgetMaximized") !== null && sincloBox.getAttribute('data-openflg') !== storage.s.get("widgetMaximized")) {
            sincloBox.setAttribute('data-openflg', storage.s.get("widgetMaximized"));
            return storage.s.get("widgetMaximized");
          } else {
            return sincloBox.getAttribute('data-openflg');
          }
        },
        set: function(flg, overwrite){
          var sincloBox = document.getElementById('sincloBox');
          sincloBox.setAttribute('data-openflg', flg);
          if(overwrite || storage.s.get("widgetMaximized") === null) {
            storage.s.set("widgetMaximized", flg);
          }
        }
      }
    },
    syncTimeout: "",
    operatorInfo: {
      header: null,
      toggle: function(){
        this.ev();
//        sincloBox.setAttribute('data-openflg', false);
        var flg = sinclo.widget.condifiton.get();
        //ウィジェットを開いた回数
        if(String(flg) === "true" && typeof ga == "function"){
          ga('send', 'event', 'sinclo', 'クリック', location.href);
        }
      },
      ev: function() {
        if(!common.widgetHandler.isShown()) {
          console.log("非表示状態のため動作させない");
          return;
        }
        var height = 0;
        var sincloBox = document.getElementById('sincloBox');
        var flg = sinclo.widget.condifiton.get();
        var elm = $('#sincloBox');
        //実際にバナーではないか
        var bannerAct = storage.s.get('bannerAct');
        if(bannerAct !== "true"){
          //アニメーションさせる
          //最小化時と最大化時の状態を取得
          var abridgementType = common.getAbridgementType()
          if ( String(flg) === "false" ) {
            //最大化時ボタン表示
            common.whenMaximizedBtnShow();
            //最大化
            if(abridgementType['MaxRes']){
              //ヘッダ非表示（シンプル表示）
              common.abridgementTypehide();
            }
            else{
              //ヘッダ表示（通常表示）
              common.abridgementTypeShow();
            }
            sinclo.widget.condifiton.set(true, true);
            if ( check.smartphone() && window.sincloInfo.contract.chat && (window.screen.availHeight < window.screen.availWidth) ) {
              //スマホ横
              height = window.innerHeight * (document.body.clientWidth / window.innerWidth);
            }
            else {
              height += $("#sincloWidgetBox #widgetHeader").outerHeight(true);
              if ( $("#sincloWidgetBox").children().is("#navigation") ) {
              //if(check.smartphone()) {
                height += $("#sincloWidgetBox > #navigation").outerHeight(true);
                var tab = $("#sincloWidgetBox #navigation li.selected").data('tab');
                height += $("#sincloWidgetBox #" + tab + "Tab").outerHeight(true);
              }
              else {
                height += $("#sincloWidgetBox [id$='Tab']").outerHeight(true);
              }
              height += $("#sincloWidgetBox > #fotter").outerHeight(true);
            }
            if ( window.sincloInfo.contract.chat ) {
              sinclo.chatApi.showUnreadCnt();
              sinclo.chatApi.scDown();
            }
          }
          else {
            //最小化時ボタン表示
            common.whenMinimizedBtnShow();
            //最小化
            if(abridgementType['MinRes']){
              //ヘッダ非表示（シンプル表示）
              common.abridgementTypehide();
            }
            else{
              //ヘッダ表示（通常表示）
              common.abridgementTypeShow();
            }
            height = this.header.offsetHeight;
            sinclo.widget.condifiton.set(false, true);
          }
            elm.animate({
              height: height + "px"
            }, 'first');
        }
        else{
          //バナー表示時の位置を設定
          $("#sincloBox").css("height","");
          sinclo.operatorInfo.bannerBottomLeftRight();
        }
      },
      //閉じるボタンがクリックされた時の挙動
      closeBtn: function(){
        //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
        if(Number(window.sincloInfo.widget.closeButtonSetting) === 2 && Number(window.sincloInfo.widget.closeButtonModeType) === 1){
          //バナー表示にする
          sinclo.operatorInfo.onBanner();
        }
        else{
          //非表示状態になった
          storage.s.set('closeAct', true);
          //チャットを閉じる
          $("#sincloBox").hide();
        }
      },
      //バナー表示時の位置を設定
      bannerBottomLeftRight: function() {
        if ( check.smartphone() ) {
          //スマホだったら縦か横かを判定

          var text = check.escape_html(window.sincloInfo.widget.bannertext);
          var oneByteCount = 0;
          var towByteCount = 0;
          for (var i=0; i<text.length; i++){
            var n = escape(text.charAt(i));
            if (n.length < 4){
              oneByteCount++;
            }
            else{
              towByteCount++;
            }
          }
          if($(window).height() > $(window).width()){
            var widgetWidth = $(window).width() - 20;
            var ratio = widgetWidth * (1/285);
            var bannerBasicSize = (63 * ratio);
            var fontSize = (12.5 * ratio);
            var bannerSize = bannerBasicSize + (oneByteCount * (fontSize * (1/2))) + (towByteCount * fontSize)
            //縦
            var bottom = (10 * ratio) + "px" ;
            var leftRight = (-(widgetWidth * (1/2)) + (bannerSize * (1/2))) + "px" ;
          }
          else{
            var ratio = 1.9;
            var widgetWidth = $(window).width() * ratio;
            var bannerBasicSize = (63 * ratio);
            var fontSize = (12.5 * ratio);
            var bannerSize = bannerBasicSize + (oneByteCount * (fontSize * (1/2))) + (towByteCount * fontSize)
            //横
            var bottom = "2em";
            var leftRight = (-(widgetWidth * (1/2)) + ((bannerSize * (1/2)) * ratio) - (12 * ratio) ) + "px" ;
            //var leftRight = (-(widgetWidth * (1/2))) + "px" ;
          }
        }
        else{
          //pc
          var bottom = "20px";
          var leftRight = "20px";
        }
        $("#sincloBox").css("bottom",bottom);
        switch ( Number(window.sincloInfo.widget.showPosition) ) {
          case 1: // 右下
            //right: 10px;
            $("#sincloBox").css("right",leftRight);
            break;
          case 2: // 左下
            //left: 10px;
            $("#sincloBox").css("left",leftRight);
            break;
        }
      },
      //バナー表示にする
      onBanner: function() {
        //バナー表示
        $("#sincloWidgetBox").hide();
        $("#sincloBox").css("outline","none");
        $("#sincloBox").css("height","");
        //バナー表示時の位置を設定
        sinclo.operatorInfo.bannerBottomLeftRight();
        //バナー表示状態になった
        storage.s.set('bannerAct', true);
        $("#sincloBannerBox").show();
      },
      //バナーがクリックされた時の挙動
      clickBanner: function() {
        //バナー非表示状態になった
        storage.s.set('bannerAct', false);
        $("#sincloWidgetBox").show();
        $("#sincloBannerBox").hide();
        $("#sincloBox").css("bottom","0");
        //スマホかつ横かを判定
        if (check.smartphone()) {
          $("#sincloBox").css("right","");
          $("#sincloBox").css("left","");
        }
        else{
          switch ( Number(window.sincloInfo.widget.showPosition) ) {
          case 1: // 右下
            //right: 10px;
            $("#sincloBox").css("right","10px");
            break;
          case 2: // 左下
            //left: 10px;
            $("#sincloBox").css("left","10px");
            break;
          }
        }
        //最小化時ボタン表示
        common.whenMinimizedBtnShow();
        sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
        sinclo.operatorInfo.ev();
      },
      widgetHide: function(e) {
        if(e) e.stopPropagation();
        var sincloBox = document.getElementById('sincloBox');
        if ( !sincloBox ) return false;
        var openflg = sinclo.widget.condifiton.get();

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
            common.widgetHandler.show();
          }
        }, 500);
      },
      reCreateWidgetMessage: "",
      reCreateWidgetTimer: null,
      reCreateWidget: function(e){
        if(e) e.stopPropagation();
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
        var openFlg = sinclo.widget.condifiton.get();

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
          sincloBox.style.opacity = 0;
          sinclo.operatorInfo.header = document.getElementById('widgetHeader');
          sinclo.widget.condifiton.set(openFlg, true);
          common.widgetHandler.show(true);
          sinclo.operatorInfo.widgetHide();

          if ( String(openFlg) === "true" ) {
            sinclo.widget.condifiton.set(true, true);

            if ( window.screen.availHeight < window.screen.availWidth ) {
              sincloBox.style.height = document.documentElement.clientHeight + "px";
            }
            else {
              var height = $("#sincloBox #widgetHeader").outerHeight(true);
              height += $("#sincloBox #chatTab").outerHeight(true);
              sincloBox.style.height = height;
            }
          }
          else {
            //ここでもしバナーだったら二段階表示防止のために以下の処理を避ける
            var bannerAct = storage.s.get('bannerAct');
            if(bannerAct !== "true"){
              sinclo.widget.condifiton.set(false, true);
              sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
            }
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
        userInfo.gFrame = false;
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
      if ( window.sincloInfo.contract.chat && !(userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest)) ) {
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
          if ( window.sincloInfo.contract.chat && !(userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest)) ) {
            common.makeAccessIdTag();
          }
        }

        if ( check.isset(common.tmpParams) && Number(userInfo.accessType) === Number(cnst.access_type.host) ) {
          browserInfo.resetPrevList();
          emit('requestSyncStart', {
            accessType: common.params.type
          });
        }

        emit('reqUrlChecker', {reconnectFlg: browserInfo.connectFlg});

        // connectフラグ
        browserInfo.connectFlg = true;


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
      // LiveAssistのSDKがあれば、このタイミングでイベントハンドラをセットしておき
      if(typeof(AssistSDK) !== 'undefined') {
        console.log("Assist SDK found. initSDKCallbacks");
        laUtil.initSDKCallbacks();
        if(storage.l.get('assist-localstorage-config') && !storage.s.get('assist-session-config')) {
          console.log("LA localstorage found. but sessionstorage not found. deleting");
          storage.l.unset('assist-localstorage-config');
        }
      }

      // connectフラグ
      browserInfo.connectFlg = true;

      if(check.isset(userInfo.accessId)) {
        emitData.accessId = userInfo.accessId;
      }

      emit('connected', {
        type: 'user',
        data: emitData
      });
    },
    retConnectedForSync: function (d) {
      var obj = common.jParse(d);
      if ( ('pagetime' in obj) ) {
        userInfo.pageTime = obj.pagetime;
      }
      if ( obj.hasOwnProperty('activeOperatorCnt') ) {
        window.sincloInfo.activeOperatorCnt = obj.activeOperatorCnt;
      }
      if ( obj.hasOwnProperty('opFlg') ) {
        window.sincloInfo.opFlg = obj.opFlg;
      }
    },
    accessInfo: function(d){
      var obj = common.jParse(d);
      if ( obj.token !== common.token ) return false;
      if ( obj.hasOwnProperty('activeOperatorCnt') ) {
        window.sincloInfo.activeOperatorCnt = obj.activeOperatorCnt;
      }
      if ( obj.hasOwnProperty('opFlg') ) {
        window.sincloInfo.opFlg = obj.opFlg;
      }
      if ( obj.hasOwnProperty('pagetime') ) {
        userInfo.pageTime = obj.pagetime;
      }

      if ( check.isset(obj.accessId) && !check.isset(obj.connectToken)) {
        userInfo.set(cnst.info_type.access, obj.accessId, true);

        var setWidgetFnc = function(){
          if ( window.sincloInfo.widget === undefined ) {
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

      if ( obj.sincloSessionIdIsNew || (!check.isset(userInfo.sincloSessionId) && check.isset(obj.sincloSessionId)) ) {
        if(obj.sincloSessionIdIsNew) console.log("sincloSessionIdIsNew");
        userInfo.oldSincloSessionId = userInfo.sincloSessionId ? userInfo.sincloSessionId : "";
        userInfo.set(cnst.info_type.sincloSessionId, obj.sincloSessionId, "sincloSessionId");
      }

      obj.prev = userInfo.writePrevToLocalStorage();
      obj.stayCount = userInfo.getStayCount();
      if ( (userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest)) === false ) {
        emit('customerInfo', obj);
      }
      emit('connectSuccess', {
        confirm: false,
        widget: window.sincloInfo.widgetDisplay,
        prevList: userInfo.prevList,
        userAgent: window.navigator.userAgent,
        time: userInfo.time,
        ipAddress: userInfo.getIp(),
        referrer: userInfo.referrer
      });

      // customEvent
      if(document.createEvent) {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent('sinclo:connected', true, true);
        document.dispatchEvent(evt);
      } else {
        var evt = document.createEventObject();
        document.fireEvent('sinclo:connected', evt);
      }
    },
    setHistoryId: function(){
        var createStartTimer,
            createStart = function(){
                console.log("create start");
                var sincloBox = document.getElementById('sincloBox');
                if ( window.sincloInfo.contract.chat && check.smartphone() ) {
                  common.widgetHandler.show();
                  sincloBox.style.opacity = 0;
                  sinclo.operatorInfo.widgetHide();
                }
                else {
                  common.widgetHandler.show();
                }
                // ウィジェット表示
                sinclo.chatApi.widgetOpen();

                if ( window.sincloInfo.contract.chat ) {
                    // チャット情報読み込み
                    sinclo.chatApi.init();
            }
        };

        if ( document.getElementById('sincloBox') === null ) return false;

        createStartTimer = window.setInterval(function(){
          if (window.sincloInfo.widget.showTiming !== 4 || (window.sincloInfo.widgetDisplay && !sinclo.trigger.flg)) {
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
      emitData.widget = window.sincloInfo.widgetDisplay;
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
    createShareWindow: function(obj) { // 外部接続
      if ( obj.tabId !== userInfo.tabId ) return false;
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var title = location.host + 'の内容';
      var content = location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function(){
        browserInfo.resetPrevList();
        userInfo.setConnect(obj.connectToken);

        var size = browserInfo.windowSize();
        var params = {
          data: {
            url: location.href,
            userId: userInfo.userId,
            tabId: userInfo.tabId,
            connectToken: obj.connectToken
          },
          site: window.sincloInfo.site
        };
        var url = window.sincloInfo.site.files + "/frame/" + encodeURIComponent(JSON.stringify(params));

        window.open(url, "_blank", "width=" + size.width + ", height=" + size.height + ", resizable=no,scrollbars=yes,status=no");
      };
      popup.set(title, content);
    },
    getWindowInfo: function(obj) {
      if ( obj.tabId !== userInfo.tabId ) return false;
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var title = location.host + 'の内容';
      var content = location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function(){
        var sincloBox = document.getElementById("sincloBox");
        if( sincloBox ){
          sincloBox.style.display = "none";
        }

        userInfo.connectToken = obj.connectToken;
        browserInfo.resetPrevList();
        userInfo.setConnect(obj.connectToken);

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
    startWindowSync: function(obj) {
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var title = location.host + 'の内容';
      var content = location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function(){
        userInfo.connectToken = obj.connectToken;
        browserInfo.resetPrevList();
        var params = {
          site: window.sincloInfo.site,
          data: {
            userId: userInfo.userId,
            tabId: userInfo.tabId,
            url: location.href,
            screen: browserInfo.windowScreen(), // 解像度
            connectToken: userInfo.connectToken
          }
        };
        var filePath = window.sincloInfo.site.files.replace("https://", "http://");
        var url = filePath + "/frame/" + encodeURIComponent(JSON.stringify(params));
        window.open(url,
                    "sinclo",
                    "width=" + screen.availWidth * (2/3) + ",height=" + screen.availHeight * (2/3) +
                    ",dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no,scrollbars=no"
        );

        // emit('sendWindowInfo', {
        //   userId: userInfo.userId,
        //   tabId: userInfo.tabId,
        //   connectToken: userInfo.connectToken,
        //   // 解像度
        //   screen: browserInfo.windowScreen(),
        //   // ブラウザのサイズ
        //   windowSize: browserInfo.windowSize()
        // });
        this.remove();
      };
      popup.set(title, content);
    },
    startCoBrowseOpen : function(obj) {
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var title = location.host + 'の内容';
      var content = location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function() {
        var sincloBox = document.getElementById("sincloBox");
        if( sincloBox ){
          sincloBox.style.display = "none";
        }
        userInfo.coBrowseConnectToken = obj.coBrowseConnectToken;
        storage.s.set('coBrowseConnectToken', obj.coBrowseConnectToken);
        var params = {
          userId: userInfo.userId,
          tabId: userInfo.tabId,
          coBrowseConnectToken: userInfo.coBrowseConnectToken
        };
        emit('beginToCoBrowse', params);
        this.remove();
      }
      popup.set(title, content);
    },
    assistAgentIsReady: function(d) {
      console.log('assistAgentIsReady');
      var operatorId = d.responderId;
      var _self = this;
      laUtil.setOnErrorCallback(function(error){
        _self.coBrowseErrorProcess();
      });
      laUtil.setOnAgentLeftCallback(function(){
        if ( !check.isset(userInfo.coBrowseConnectToken) ) return false;

        storage.s.unset("coBrowseConnectToken");
        if (!document.getElementById('sincloBox')) {
          common.makeAccessIdTag();
          if ( window.sincloInfo.contract.chat ) {
            // チャット情報読み込み
            sinclo.chatApi.init();
          }
        }

        // 終了通知
        var title = location.host + 'の内容';
        var content = location.host + 'との画面共有を終了しました';
        popup.ok = function(){
          laUtil.disconnect();
          this.remove();
        };
        popup.set(title, content, popup.const.action.alert);

        var timer = setInterval(function(){
          if (window.sincloInfo.widgetDisplay === false) {
            clearInterval(timer);
            laUtil.disconnect();
            return false;
          }
          var sincloBox = document.getElementById('sincloBox');
          // チャット未契約のときはウィジェットを非表示
          if (sincloBox && (window.sincloInfo.contract.chat || window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document)) ) {
            common.widgetHandler.show();
            sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
            sinclo.widget.condifiton.set(false, true);
            clearInterval(timer);
          }
        }, 500);
      });
      laUtil.initAndStart(operatorId).then(function() {
        // shortcode取得
        var shortcode = laUtil.shortcode;
        if (shortcode !== undefined && shortcode !== null && shortcode !== "") {
          console.log("Fire readyToCoBrowse");
          var params = {
            userId: userInfo.userId,
            tabId: userInfo.tabId,
            responderId: operatorId,
            url: location.href,
            coBrowseConnectToken: userInfo.coBrowseConnectToken,
            shortcode: laUtil.shortcode
          };
          emit('readyToCoBrowse', params);
        }
      }).fail(function(e){
        _self.coBrowseErrorProcess();
      });
    },
    coBrowseErrorProcess: function() {
      // 終了通知
      emit('coBrowseFailed', {
        userId: userInfo.userId,
        tabId: userInfo.tabId,
        connectToken: userInfo.coBrowseConnectToken
      });
      var title = location.host + 'の内容';
      var content = '接続時にエラーが発生しました。<br>再度お試しください。';
      popup.ok = function(){
        laUtil.disconnect();
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);
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
      // チャット未契約か、外部接続のときはウィジェットを非表示
      if (sincloBox && (!window.sincloInfo.contract.chat || (userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest)) )) {
        sincloBox.style.display = "none";
      }
      if (!(userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest))) {
        common.load.start();
      }
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
      if (String(userInfo.gFrame) === "true") return false;
      emit('sendTabInfo', { status: browserInfo.getActiveWindow(), widget: window.sincloInfo.widgetDisplay });
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
              var i, n;
              for ( i in obj.inputInfo ) {
                n = Number(i);
                $('input').eq(n).val(obj.inputInfo[n]);
              }
              for ( i in obj.checkboxInfo ) {
                n = Number(i);
                $('input[type="checkbox"]').eq(n).prop("checked", obj.checkboxInfo[n]);
              }
              for ( i in obj.radioInfo ) {
                n = Number(i);
                $('input[type="radio"]').eq(n).prop("checked", obj.radioInfo[n]);
              }
              for ( i in obj.textareaInfo ) {
                n = Number(i);
                $('textarea').eq(n).val(obj.textareaInfo[n]);
              }
              for ( i in obj.selectInfo ) {
                n = Number(i);
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
      if ( check.isset(obj.to) && obj.to !== userInfo.tabId ) return false;
      if ( Number(obj.accessType) === Number(userInfo.accessType) ) return false;
      // カーソルを作成していなければ作成する
      if ( !document.getElementById('cursorImg') ) {
        $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mousePoint.x + '; left:' + obj.mousePoint.y + '; z-index:999999"><img width="50px" src="' + window.sincloInfo.site.files + '/img/pointer.png"></div>');
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
          elm.val(obj.value);
          break;
        case "keyup":
          elm.val(obj.value);
          break;
        case "scroll":
          elm = $(obj.nodeName).eq(Number(obj.idx));
          if ( elm.length > 0 ) {
            var scrollBarSize = {
                  height: elm[0].scrollHeight - elm[0].clientHeight,
                  width: elm[0].scrollWidth - elm[0].clientWidth
                };
                elm.stop(false, false).scrollTop(scrollBarSize.height * Number(obj.value.topRatio));
                elm.stop(false, false).scrollLeft(scrollBarSize.width * Number(obj.value.leftRatio));
          }
          break;
      }
      syncEvent.receiveEvInfo = { nodeName: null, type: null };
    },
    syncBrowserCtrl: function(d){
      var obj = JSON.parse(d);

      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      if ( !obj.state ) return false;

      // 進む
      if ( Number(obj.state) > 0 ) {
        history.forward();
      }
      // 戻る
      else if ( Number(obj.state) < 0 ) {
        history.back();
      }
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
      storage.s.set('operatorEntered', true); // オペレータが入室した

      if ( sincloInfo.widget.showName === 1 ) {
        sinclo.chatApi.opUser = obj.userName;
        opUser = obj.userName;
      }
      else if ( sincloInfo.widget.showName === 2 && String(obj.hide) === "true" ) {
        return false;
      }

      if ( check.isset(opUser) === false ) {
        opUser = "オペレーター";
      }
      check.escape_html(opUser); // エスケープ

      sinclo.chatApi.createNotifyMessage(opUser + "が入室しました");
      // チャットの契約をしている場合
      if ( window.sincloInfo.contract.chat ) {
        //OPが入室した数
        //入室数についてはタブでカウントする
        if(typeof ga == "function" && obj.tabId === userInfo.tabId){
          ga('send', 'event', 'sinclo', 'チャット対応', sinclo.chatApi.opUser);
        }
      }
    },
    chatEndResult: function(d){
      var obj = JSON.parse(d);
      this.chatApi.online = false;
      storage.s.set('operatorEntered', false); // オペレータが退室した
      storage.s.set('chatAct', false); // オートメッセージを表示してもいい
      var opUser = sinclo.chatApi.opUser;
      if ( check.isset(opUser) === false ) {
        opUser = "オペレーター";
      }
      check.escape_html(opUser); // エスケープ
      sinclo.chatApi.createNotifyMessage(opUser + "が退室しました");
      //退室した後に同じ消費者からメッセージが来た場合、それもGAのイベントとしてカウントするため
      sessionStorage.removeItem('chatEmit');
    },
    chatMessageData:function(d){
      console.log("chatMessgeData");
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

          // オートメッセージは格納しとく
          if (Number(chat.messageType) === 3 && 'chatId' in chat) {
            console.log("push " + chat.chatId);

            this.chatApi.autoMessages.push(chat.chatId, {
              chatId: chat.chatId,
              message: chat.message,
              created: chat.created,
              applied: chat.applied ? chat.applied : false
            });
          }

          // オートメッセージか、Sorryメッセージ、企業からのメッセージで表示名を使用しない場合
          if ( Number(chat.messageType) === 3 || Number(chat.messageType) === 4 || (Number(chat.messageType) === 2 && window.sincloInfo.widget.showName !== 1) ) {
            userName = window.sincloInfo.widget.subTitle;
          }
          else if ( Number(chat.messageType) === 2 ) {
            userName = chat.userName;
          }
          this.chatApi.createMessage(cn, chat.message, userName);
          this.chatApi.scDown();
        }
        else {
          if ( ('userName' in obj.chat.messages[key]) ) {
            sinclo.chatApi.opUser = obj.chat.messages[key].userName;
          }
          // 途中で設定が変更されたときの対策
          if ( sincloInfo.widget.showName !== 1 ) {
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
      if ( obj.sincloSessionId !== userInfo.sincloSessionId && obj.tabId !== userInfo.tabId ) return false;
      var elm = document.getElementById('sincloChatMessage'), cn, userName = "";
      if ( obj.ret ) {
        if(obj.messageType === sinclo.chatApi.messageType.customer && storage.s.get('chatAct') !== "true" && !obj.matchAutoSpeech) {
          // 別タブで送信した自分のメッセージを受けたのでチャット応対中とする
          console.log("self message received. set chatAct = true");
          storage.s.set('chatAct', true);
        }
        if(obj.messageType === sinclo.chatApi.messageType.customer && storage.s.get('chatEmit') !== "true") {
          // 別タブで送信した自分のメッセージを受けたのでチャット送信状態とする
          console.log("self message received. set chatEmit = true");
          storage.s.set('chatEmit', true) ;
        }
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
        if (obj.messageType === sinclo.chatApi.messageType.auto || obj.messageType === sinclo.chatApi.messageType.autoSpeech) {
          if(obj.tabId === userInfo.tabId) {
            this.chatApi.scDown();
            return false;
          } else if(obj.messageType === sinclo.chatApi.messageType.autoSpeech) {
            // 別タブで送信された自動返信は表示する
            cn = "sinclo_re";
          } else {
            // 別タブで送信されたオートメッセージは何もしない
            return false;
          }
        }

        if (obj.messageType === sinclo.chatApi.messageType.sorry) {
          cn = "sinclo_re";
          sinclo.chatApi.call();
          this.chatApi.createMessage(cn, obj.chatMessage, sincloInfo.widget.subTitle);
          if(this.chatApi.isShowChatReceiver() && Number(obj.messageType) === sinclo.chatApi.messageType.company) {
            this.chatApi.notify(obj.chatMessage);
          } else {
            this.chatApi.scDown();
          }
          // チャットの契約をしている場合
          if ( window.sincloInfo.contract.chat ) {
            //sorryメッセージを出した数
            //sorryメッセージ受信数はメッセージを送信した対象のタブでカウントする
            if(typeof ga == "function" && obj.tabId !== userInfo.tabId){
              ga('send', 'event', 'sinclo', 'チャット拒否', location.href);
            }
          }
          return false;
        }
        this.chatApi.createMessageUnread(cn, obj.chatMessage, userName);
        if(this.chatApi.isShowChatReceiver() && Number(obj.messageType) === sinclo.chatApi.messageType.company) {
          this.chatApi.notify(obj.chatMessage);
        } else {
          this.chatApi.scDown();
        }
        //sinclo.trigger.fireChatEnterEvent(obj.chatMessage);
        // オートメッセージの内容をDBに保存し、オブジェクトから削除する
        if (!sinclo.chatApi.saveFlg && obj.tabId === userInfo.tabId) {
          console.log("EMIT sendAutoChat");
          emit("sendAutoChat", {messageList: sinclo.chatApi.autoMessages.getByArray()});
          sinclo.chatApi.autoMessages.unset();
          sinclo.chatApi.saveFlg = true;
        } else if(obj.tabId !== userInfo.tabId) {
          // メインのオートメッセージだけ保存してサブのオートメッセージは保存しない
          console.log("unset automessages")
          sinclo.chatApi.autoMessages.unset();
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
      emit("sendAutoChatMessages", {messages: sinclo.chatApi.autoMessages.getByArray(), sendTo: obj.sendTo});
      var value = "";
      if (window.sincloInfo.widgetDisplay) {
        value = document.getElementById('sincloChatMessage').value;
      }
      // 入力中のステータスを送る
      sinclo.chatApi.observeType.emit(sinclo.chatApi.observeType.status, value);
    },
    resAutoChatMessage: function(d){
        console.log("resAutoChatMessage : " + JSON.stringify(d));
        var obj = JSON.parse(d);
        if(!sinclo.chatApi.autoMessages.exists(obj.chatId)) {
          sinclo.chatApi.createMessage("sinclo_re", obj.message, sincloInfo.widget.subTitle);
        }
        sinclo.chatApi.autoMessages.push(obj.chatId, {
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
    docShareConnect: function(obj){
      sessionStorage.removeItem('doc');

      // 終了通知
      var title = location.host + 'の内容';
      var content = location.host + 'が資料共有を求めています。<br>許可しますか';
      popup.ok = function(){
        var size = browserInfo.windowSize();
        var params = {
          data: obj,
          site: window.sincloInfo.site
        };
        var url = window.sincloInfo.site.files + "/docFrame/" + encodeURIComponent(JSON.stringify(params));

        window.open(url, "_blank", "width=" + size.width + ", height=" + size.height + ", resizable=yes,scrollbars=yes,status=no");
        this.remove();
      };
      popup.no = function(){
        emit('docShareCancel', obj);
        this.remove();
      };
      popup.set(title, content);

    },
    docDisconnect: function(){
      // 終了通知
      var title = location.host + 'の内容';
      var content = location.host + 'との資料共有を終了しました';
      popup.ok = function(){
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);
    },
    syncStop: function(d){
      var obj = common.jParse(d);
      syncEvent.stop(false);
      if ( (userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest)) ) {
        window.parent.close();
        return false;
      }
      if ( !check.isset(userInfo.connectToken) ) return false;

      window.clearTimeout(sinclo.syncTimeout);

      userInfo.syncInfo.unset();
      if (!document.getElementById('sincloBox')) {
        common.makeAccessIdTag();
        if ( window.sincloInfo.contract.chat ) {
          // チャット情報読み込み
          sinclo.chatApi.init();
        }
      }

      // 終了通知
      var title = location.host + 'の内容';
      var content = location.host + 'との画面共有を終了しました';
      popup.ok = function(){
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);

      var timer = setInterval(function(){
        if (window.sincloInfo.widgetDisplay === false) {
          clearInterval(timer);
          return false;
        }
        var sincloBox = document.getElementById('sincloBox');
        // チャット未契約のときはウィジェットを非表示
        if (sincloBox && (window.sincloInfo.contract.chat || window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document)) ) {
          common.widgetHandler.show();
          sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
          sinclo.widget.condifiton.set(false, true);
          clearInterval(timer);
        }
      }, 500);
    },
    stopCoBrowse: function(d){
      var obj = common.jParse(d);
      if ( (userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest)) ) {
        window.parent.close();
        return false;
      }
      if ( !check.isset(userInfo.coBrowseConnectToken) ) return false;

      storage.s.unset("coBrowseConnectToken");
      userInfo.coBrowseConnectToken = "";
      if (!document.getElementById('sincloBox')) {
        common.makeAccessIdTag();
        if ( window.sincloInfo.contract.chat ) {
          // チャット情報読み込み
          sinclo.chatApi.init();
        }
      }

      // 終了通知
      var title = location.host + 'の内容';
      var content = location.host + 'との画面共有を終了しました';
      popup.ok = function(){
        laUtil.disconnect();
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);
      laUtil.disconnect();
      var timer = setInterval(function(){
        if (window.sincloInfo.widgetDisplay === false) {
          clearInterval(timer);
          laUtil.disconnect();
          return false;
        }
        var sincloBox = document.getElementById('sincloBox');
        // チャット未契約のときはウィジェットを非表示
        if (sincloBox && (window.sincloInfo.contract.chat || window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document)) ) {
          common.widgetHandler.show();
          sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
          sinclo.widget.condifiton.set(false, true);
          clearInterval(timer);
        }
      }, 500);
    },
    syncApi: {
      init : function(type){
        if ( type === cnst.sync_type.outer ) {
          sinclo.syncApi.func = sinclo.syncApi._func.outer;
        }
        else {
          sinclo.syncApi.func = sinclo.syncApi._func.inner;
        }
      },
      func: {
        formSync: null,
        mouseSync: null,
        scrollSync: null,
        resizeSync: null,
        pageSync: null,
      },
      _func: {
        inner: {
          formSync: {
            send: function(){
            },
            receive: function(){
            }
          },
          mouseSync: {
            send: function(){
            },
            receive: function(){
            }
          },
          scrollSync: {
            send: function(){
            },
            receive: function(){
            }
          },
          resizeSync: {
            send: function(){
            },
            receive: function(){
            }
          },
          pageSync: {
            send: function(){
            },
            receive: function(){
            }
          },
        },
        outer: {
          formSync: {
            send: function(){
            },
            receive: function(){
            }
          },
          mouseSync: {
            send: function(){
            },
            receive: function(){
            }
          },

        }
      }
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
            sorry: 4,
            autoSpeech: 5,
            start: 98,
            end: 99
        },
        autoMessages: {
          push: function(id, obj) {
            
            var list = this.get(true);
            if(!this.exists(id)) {
              list[id] = obj;
              storage.s.set('amsg', JSON.stringify(list));
              return true;
            } else if(!('created' in list[id])) {
              console.log("OVERWRITE OBJECT ID: " + id + "B : " + JSON.stringify(list[id]) + "A : " + JSON.stringify(obj));
              list[id] = obj;
              storage.s.set('amsg', JSON.stringify(list));
              return true;
            }
            return false;
          },
          get: function(allData) {
            var json = storage.s.get('amsg');
            var returnData = {};
            if(json) {
              var array = JSON.parse(json);
              Object.keys(array).forEach(function(id, index, ar) {
                if(allData || !array[id].deleted) {
                  returnData[id] = array[id];
                }
              });
            }
            return returnData;
          },
          getByArray: function(allData) {
            var json = storage.s.get('amsg');
            var returnData = [];
            if(json) {
              var array = JSON.parse(json);
              Object.keys(array).forEach(function(id, index, ar) {
                if(allData || !array[id].applied) {
                  returnData.push(array[id]);
                }
              });
            }
            return returnData;
          },
          exists: function(chatId) {
            var list = this.get(true);
            console.log(">>>>>>>>>>>>>>> " + JSON.stringify(list));
            return chatId in list;
          },
          unset: function() {
            // 論理的にフラグを付ける
            var list = this.get();
            Object.keys(list).forEach(function(id, index, arr) {
              
              list[id]['applied'] = true;
            });
            storage.s.set('amsg', JSON.stringify(list));
          }
        },
        init: function(){
            if ( window.sincloInfo.contract.chat ) {
                if ( !( 'chatTrigger' in window.sincloInfo.widget && window.sincloInfo.widget.chatTrigger === 2) ) {
                  // チャットメッセージ入力欄でのキーイベント系はすべてバブリングしない
                  $("#sincloChatMessage").on("keyup keypress",function(e){
                    if(e) e.stopImmediatePropagation();
                  });
                  $('#sincloChatMessage').on("keydown", function(e){
                      if(e) e.stopImmediatePropagation();
                      if ( (e.which && e.which === 13) || (e.keyCode && e.keyCode === 13) ) {
                          if ( !e.shiftKey && !e.ctrlKey ) {
                              sinclo.chatApi.push();
                          }
                      }
                  });
                  // キーイベント系はすべてバブリングしない
                  $(document).on("keydown keyup keypress", "#sincloChatMessage", function(e){
                    if(e) e.stopImmediatePropagation();
                  });
                }
                $(document).on("focus", "#sincloChatMessage", function(e){
                  if(e) e.stopPropagation();
                  sinclo.chatApi.observeType.start();
                });
            }

            this.sound = document.getElementById('sinclo-sound');
            if ( this.sound ) {
                this.sound.volume = 0.3;
            }

            // 複数回イベントが登録されるケースがあるためいったんOFFにする
            $(document).off('click', "input[name^='sinclo-radio']");

            $(document)
              .on('focus', "#sincloChatMessage",function(e){
                if(e) e.stopPropagation();
                var message = document.getElementById('sincloChatMessage');
                message.placeholder = "";
              })
              .on('blur', "#sincloChatMessage",function(e){
                if(e) e.stopPropagation();
                var message = document.getElementById('sincloChatMessage');
                message.placeholder = "メッセージを入力してください";
                if ( !( 'chatTrigger' in window.sincloInfo.widget && window.sincloInfo.widget.chatTrigger === 2) ) {
                  if ( check.smartphone() ) {
                    message.placeholder += "（改行で送信）";
                  }
                  else {
                    message.placeholder += "\n（Shift+Enterで改行/Enterで送信）";
                  }
                }
              })
              .on("click", "input[name^='sinclo-radio']", function(e){
                if(e) e.stopPropagation();
                if ( !(window.sincloInfo.widget.hasOwnProperty('chatRadioBehavior') && window.sincloInfo.widget.chatRadioBehavior === 2) ) {
                  sinclo.chatApi.send(e.target.value.trim());
                }
                else {
                  var message = document.getElementById('sincloChatMessage');
                  if ( check.isset(message.value) ) {
                    message.value += "\n";
                  }
                  message.value += e.target.value.trim();
                }
              });

            emit('getChatMessage', {showName: sincloInfo.widget.showName});
        },
        widgetOpen: function(){
          console.log("chatApi.widgetOpen start");
          this.beforeWidgetOpen();
          var widgetOpen = storage.s.get('widgetOpen');
          if ( !(('showTime' in window.sincloInfo.widget) && ('maxShowTime' in window.sincloInfo.widget) && String(window.sincloInfo.widget.maxShowTime).match(/^[0-9]{1,2}$/) !== null) ) return false;
          var showTime = String(window.sincloInfo.widget.showTime);
          var maxShowTime = Number(window.sincloInfo.widget.maxShowTime) * 1000;
          if ( showTime === "2" ) return false; // 常に最大化しない
          if ( showTime === "1" ) { // サイト訪問後
            if (widgetOpen) return false;
          }
          // 常に最大化する、ページ訪問時（showTime === 3,4）
          window.setTimeout(function(){
            console.log("ウィジェット最大化条件発動");
            var flg = sinclo.widget.condifiton.get();
            if ( String(flg) === "false" ) {
              storage.s.set('widgetOpen', true);
              if(!common.widgetHandler.isShown()) {
                storage.s.set('preWidgetOpened', true);
              }
              if ( !(check.smartphone() && sincloInfo.widget.hasOwnProperty('spAutoOpenFlg') && Number(sincloInfo.widget.spAutoOpenFlg) === 1) ) {
                sinclo.operatorInfo.ev();
              }
            }
          }, maxShowTime);
        },
        beforeWidgetOpen: function() {
          // ウィジェット表示タイミングが「ページ訪問時」の場合はタイマーをセットする
          if(window.sincloInfo.widget.showTiming === 1 || window.sincloInfo.widget.showTiming === 2) {
            console.log("訪問後表示処理実行");
            window.setTimeout(function() {
              console.log("訪問後表示");
              window.sincloInfo.widgetDisplay = true;
              common.widgetHandler.show();
            },common.widgetHandler.getRemainingTimeMsec());
          }
        },
        createNotifyMessage: function(val){
            var chatList = document.getElementsByTagName('sinclo-chat')[0];
            var div = document.createElement('div');
            var li = document.createElement('li');
            div.appendChild(li);
            chatList.appendChild(div);
            li.className = "sinclo_etc";
            li.innerHTML = "－ " + check.escape_html(val) + " －";
            this.scDown();
        },
        createTypingTimer: null,
        createTypingMessage: function(d) {
          var obj = JSON.parse(d),
            opUser = sinclo.chatApi.opUser,
            chatType = document.getElementsByTagName('sinclo-typing')[0],
            typeMessage = document.getElementById('sinclo_typeing_message'),
            li = document.createElement('li'),
            span = document.createElement('span');

          var calcMergin = function (opUser) {
            var margin = (opUser.length + 4) / 2;
            span.style.marginLeft = "-" + margin + "em";
          };

          if (obj.status === false) {
            if (typeMessage) {
              typeMessage.parentNode.removeChild(typeMessage);
            }
            clearInterval(this.createTypingTimer);
            return false;
          }

          if (check.isset(opUser) === false) {
            opUser = "オペレーター";
          }

          opUser = check.escape_html(opUser); // エスケープ

          if (!typeMessage) {
            li.appendChild(span);
            chatType.appendChild(li);
            li.id = "sinclo_typeing_message";
            span.textContent = opUser + "が入力中";
            calcMergin(opUser);
          }

          this.createTypingTimer = setInterval(function () {
            calcMergin(opUser);

            if (span.textContent.length > opUser.length + 6) {
              span.textContent = opUser + "が入力中";
            }
            else {
              span.textContent += ".";
            }
          }, 500);
          if (!this.isShowChatReceiver()) {
            var chatTalk = document.getElementById('chatTalk');
            $('#sincloBox #chatTalk').animate({
              scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
            }, 300);
          }
        },
        createMessage: function(cs, val, cName){
            var chatList = document.getElementsByTagName('sinclo-chat')[0];
            var div = document.createElement('div');
            var li = document.createElement('li');
            div.appendChild(li);
            chatList.appendChild(div);
            var strings = val.split('\n');
            var radioCnt = 1;
            var linkReg = RegExp(/http(s)?:\/\/[!-~.a-z]*/);
            var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
            var radioName = "sinclo-radio" + chatList.children.length;
            var content = "";
            if ( check.isset(cName) === false ) {
              cName = window.sincloInfo.widget.subTitle;
            }
            check.escape_html(cName); // エスケープ

            if ( cs === "sinclo_re" ) {
              div.style.textAlign = "left";
              content = "<span class='cName'>" + cName + "</span>";
            } else if (cs === "sinclo_se") {
              div.style.textAlign = "right";
            }
            for (var i = 0; strings.length > i; i++) {
                var str = check.escape_html(strings[i]);

                if ( cs === "sinclo_re" ) {
                    // ラジオボタン
                    var radio = str.indexOf('[]');
                    if ( radio > -1 ) {
                        var name = str.slice(radio+2);
                        str = "<sinclo-radio><input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + name + "'>";
                        str += "<label for='" + radioName + "-" + i + "'>" + name + "</label></sinclo-radio>";
                    }
                }
                // リンク
                var link = str.match(linkReg);
                if ( link !== null ) {
                    var url = link[0];
                    var a = "<a href='" + url + "' target='_blank'>" + url + "</a>";
                    str = str.replace(url, a);
                }
                // 電話番号（スマホのみリンク化）
                var tel = str.match(telnoTagReg);
                if( tel !== null ) {
                  var telno = tel[1];
                  if(check.smartphone()) {
                    // リンクとして有効化
                    var a = "<a href='tel:" + telno + "'>" + telno + "</a>";
                    str = str.replace(tel[0], a);
                  } else {
                    // ただの文字列にする
                    var span = "<span class='telno'>" + telno + "</span>";
                    str = str.replace(tel[0], span);
                  }
                }
                content += str + "\n";
            }

            if ( cs === "sinclo_re" ) {
              cs += ' effect_left';
            } else if (cs === "sinclo_se") {
              cs += ' effect_right';
            }

            li.className = cs;
            li.innerHTML = content;
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
            $('#sincloBox #chatTalk').animate({
              scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
          }, 300);
          }, 500);
        },
        scDownImmediate: function(){
          var chatTalk = document.getElementById('chatTalk');
          $('#sincloBox #chatTalk').animate({
            scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
          }, 300);
        },
        isNotifyOpened: false,
        notify: function(message) {
          var self = this;
          var target = $('sinclo-chat-receiver');
          target.find('#receiveMessage').html(message);
          target.css('display', 'block');
          var targetHeight = target.outerHeight();
          target.css('top',($('#sincloBox #chatTalk').position().top + $('#sincloBox #chatTalk').outerHeight()) - targetHeight);
          // 指定した高さになるまで、1文字ずつ消去していく
          target.css('display', 'none');
          target.css('display', 'block');
          var isShrinkMessage = false;
          while((message.length > 0) && (target.find('#receiveMessage').outerHeight() >= targetHeight)) {
            isShrinkMessage = true;
            message = message.substr(0, message.length - 1);
            target.find('#receiveMessage').html(message + '...');
          }
          if(isShrinkMessage) {
            message = message.substr(0, message.length - 1);
            target.find('#receiveMessage').html(message + '...');
          }
          if(!this.isNotifyOpened) {
            target.css('display', 'none');
            this.isNotifyOpened = true;
            target.show('fast').off('click').on('click', function (e) {
              self.isNotifyOpened = false;
              e.stopImmediatePropagation();
              self.scDown();
              $(this).hide();
            });
          }
          // スクロールが表示判定とならないところまで来たら消す
          $('#sincloBox #chatTalk').on('scroll', function(e){
            if(!self.isShowChatReceiver()) {
              self.isNotifyOpened = false;
              target.hide('fast');
            }
          });
        },
        isShowChatReceiver: function() {
          var target = $('#sincloBox #chatTalk');
          var allHeight = 0;
          target.find('sinclo-chat').find('li').each(function(index){
            allHeight += $(this).outerHeight();
          });
          console.log('allHeight: ' + allHeight);
          return allHeight - target.height() - target.scrollTop() >= 55;
        },
        pushFlg: false,
        push: function(){
          if (this.pushFlg) return false;
          this.pushFlg = true;
          sinclo.operatorInfo.reCreateWidgetMessage = ""; // 送信したら空にする

          var elm = document.getElementById('sincloChatMessage');
          var req = new RegExp(/^\s*$/);

          if ( check.isset(elm.value) && !req.test(elm.value) ) {
            this.send(elm.value);
            elm.value = "";
          }
          this.pushFlg = false;
        },
        send: function(value){
          // 自動返信の処理中でなければ
          if(!sinclo.trigger.processing) {
            storage.s.set('chatAct', true); // オートメッセージを表示しない
          }

            // タイマーが仕掛けられていたらリセット
            if ( this.sendErrCatchTimer !== null ) {
              clearTimeout(this.sendErrCatchTimer);
            }

            // チャットの契約をしている場合
            if ( window.sincloInfo.contract.chat ) {
              var firstChatEmit = storage.s.get('chatEmit');
              //チャットリクエスト件数でない
              var noFlg = 0;
              //チャットリクエスト件数である
              var flg = 1;
              var messageRequestFlg = noFlg;

              //サイト訪問者がチャット送信した初回のタイミング
              if ( !check.isset(firstChatEmit) ) {
                if(typeof ga == "function"){
                  ga('send', 'event', 'sinclo', 'チャット送信', location.href);
                }
                messageRequestFlg = flg;
              }

              sinclo.trigger.judge.matchAllSpeechContent(value, function(result){
                if(result && (!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false")) {
                  storage.s.set('chatAct', false); // オートメッセージを表示しない
                }

                setTimeout(function(){
                  emit('sendChat', {
                    historyId: sinclo.chatApi.historyId,
                    chatMessage:value,
                    mUserId: null,
                    messageType: sinclo.chatApi.messageType.customer,
                    messageRequestFlg: messageRequestFlg,
                    isAutoSpeech : result,
                    notifyToCompany: !result
                  });
                }, 100);
              });

              storage.s.set('chatEmit', true) ;
            }

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
            emit('sendTypeCond', { type: 2, status: status, message: message, sincloSessionId: userInfo.sincloSessionId });
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
        //未読数表示
        showUnreadCnt: function(){
            var elmId = "sincloChatUnread",
                unreadIcon = document.getElementById(elmId);
            var sincloBox = document.getElementById('sincloBox');
            var flg = sinclo.widget.condifiton.get();
            //unreadIconがあればエレメントを削除
            if ( unreadIcon ) {
                unreadIcon.parentNode.removeChild(unreadIcon);
            }
            if ( Number(sinclo.chatApi.unread) > 0 ) {
                if ($("#sincloBox #chatTab").css("display") !== "none" && String(flg) === "true") {
                    emit("isReadFromCustomer", {});
                    sinclo.chatApi.unread = 0;
                    return false;
                }

                var em = document.createElement('em');
                em.id = elmId;
                em.textContent = sinclo.chatApi.unread;
                var mainImg = document.getElementById('mainImage');
                var titleElm = document.getElementById('widgetTitle');
                //最小化時と最大化時の状態を取得
                var abridgementType = common.getAbridgementType()
                //未読表示位置がシンプル設定か否かによって異なる
                if (! abridgementType['MinRes'] ) {
                    //通常時
                    mainImg.appendChild(em);
                }
                else {
                    //シンプルデザイン時
                    titleElm.appendChild(em);
                }
//                if ( mainImg ) {
//                  //通常時
//                  mainImg.appendChild(em);
//                }
//                else if ( titleElm ) {
//                    //シンプルデザイン時
//                    titleElm.appendChild(em);
//                }
            }
        },
        KEY_TRIGGERED_AUTO_SPEECH: "triggeredAutoSpeech",
        _getAutoSpeechTriggeredList: function () {
          return storage.s.get(this.KEY_TRIGGERED_AUTO_SPEECH) ? JSON.parse(storage.s.get(this.KEY_TRIGGERED_AUTO_SPEECH)) : [];
        },
        // 発動した発言内容を保存
        saveAutoSpeechTriggered: function(triggerType, id) {
          console.log("saveAutoSpeechTriggered triggerType : " + triggerType + " id : " + id);
          if(triggerType === "1") {
            // 発動条件が１回のみ有効であればidを保持する
            var array = this._getAutoSpeechTriggeredList();
            if(array.indexOf(id) < 0) {
              // 登録済みでなければ追加する
              array.push(id);
              storage.s.set(this.KEY_TRIGGERED_AUTO_SPEECH, JSON.stringify(array));
            }
          } else {
            console.log("triggerType = 2");
          }
        },
        // 発動した発言内容を保存
        triggeredAutoSpeechExists: function(id) {
          var array = this._getAutoSpeechTriggeredList();
          return array.indexOf(id) >= 0;
        }
    },
    trigger: {
        flg: false,
        nowSaving: false,
        timerTriggeredList: {},
        orTriggeredId: [],
        processing: false,
        init: function(){
          console.log("sinclo.trigger.init");
            if ( !('messages' in window.sincloInfo) || (('messages' in window.sincloInfo) && typeof(window.sincloInfo.messages) !== "object" ) ) return false;
            this.flg = true;
            var messages = window.sincloInfo.messages;
            console.log("MESSAGES : " + JSON.stringify(messages));
            var andFunc = function(conditionKey, condition, key, ret){
                if(conditionKey === 7) {
                  // 自動返信のトリガーの場合は処理中フラグを立てる
                  sinclo.trigger.processing = true;
                }
                console.log("AND FUNC key: " + key + " ret: " + ret);
                var message = messages[key];
                if (typeof(ret) === 'number') {
                    setTimeout(function(){
                      sinclo.trigger.setAction(message.id, message.action_type, message.activity);
                      sinclo.trigger.processing = false;
                      // if(conditionKey === 7) {
                      //   // 自動返信実行後はチャット中のフラグを立てる
                      //   storage.s.set('chatAct','true');
                      // }
                    }, ret);
                } else if(ret && typeof(ret) === 'object') {
                    sinclo.trigger.timerTriggeredList[message.id] = false;
                    setTimeout(function(){
                        sinclo.trigger.processing = false;
                        console.log("AUTO MESSAGE TIMER TRIGGERED");
                        sinclo.trigger.timerTriggeredList[message.id] = true;
                    }, ret.delay);
                }
            };
            var orFunc = function(conditionKey, condition, key, ret){
                var message = messages[key];
                if(conditionKey === 7) {
                  // 自動返信のトリガーの場合は処理中フラグを立てる
                  sinclo.trigger.processing = true;
                }
                if (typeof(ret) === 'number') {
                    setTimeout(function() {
                      console.log("orFunc::setTimeout message : " + JSON.stringify(message) + "conditionKey : " + conditionKey + " condition : " + JSON.stringify(condition));

                      // ・OR条件における発言内容発動条件
                      // ・発言内容が先に発動した場合 => 後続で発動した条件は無視する
                      // ・その他条件が先に発動した場合 => 発言内容が１回きりの場合、無視する
                      // 　　　　　　　　　　　　　　　 => 発言内容が何度でもの場合、発動する
                      var isAutoSpeechTrigger = conditionKey && condition && conditionKey === 7;
                      var autoSpeechTriggerManyTimes = false;
                      if(isAutoSpeechTrigger && condition.speechTriggerCond === "1") {
                        autoSpeechTriggerManyTimes = false;
                      } else if(isAutoSpeechTrigger && condition.speechTriggerCond === "2") {
                        autoSpeechTriggerManyTimes = true;
                      }

                      if(!autoSpeechTriggerManyTimes && sinclo.trigger.orTriggeredId.indexOf(message.id) >= 0) {
                        console.log("OR id: " + message.id + " was triggered. ignoreing");
                        return;
                      }

                      console.log("OR id: " + message.id + " is triggered.");

                      if(sinclo.trigger.orTriggeredId.indexOf(message.id) === -1) {
                        sinclo.trigger.orTriggeredId.push(message.id);
                      }

                      if(!isAutoSpeechTrigger && Object.keys(message.activity.conditions).indexOf("7") >= 0) {
                        console.log("orFunc saveAutoSpeechTriggered");
                        //ここに入るオートメッセージは他の条件で発動するため、発言内容条件で動作しないようフラグを立てる
                        var autoSpeechCondition = message.activity.conditions["7"][0];
                        console.log("autoSpeechCondition : " + JSON.stringify(autoSpeechCondition));
                        if(autoSpeechCondition) {
                          sinclo.chatApi.saveAutoSpeechTriggered(autoSpeechCondition.speechTriggerCond, message.id);
                        }
                      }
                      sinclo.trigger.setAction(message.id, message.action_type, message.activity);
                      // if(conditionKey === 7) {
                      //   // 自動返信実行後はチャット中のフラグを立てる
                      //   storage.s.set('chatAct','true');
                      // }
                      sinclo.trigger.processing = false;
                    }, ret);
                }
            };
            // 設定ごと
            for( var i = 0; messages.length > i; i++ ){
                // AND
                if ( Number(messages[i].activity.conditionType) === 1 ) {
                    this.setAndSetting(i, messages[i].activity, andFunc);
                }
                // OR
                else {
                    this.setOrSetting(i, messages[i].activity, orFunc);
                }
            }
        },
        /**
         * return 即時実行(0)、タイマー実行(ミリ秒)、非実行(null)
         */
        setAndSetting: function(key, setting, callback) {
          console.log("setAndSettings key : " + key + " setting: " + setting);
            var keys = Object.keys(setting.conditions);
            // 発言内容条件を一番最後にする
            var arrayForSort = ["1","2","3","4","5","6","8","9","10","7"];
            var tmpKey = [];
            arrayForSort.forEach(function(element, index, array ){
                if(keys.indexOf(element) >= 0) {
                  tmpKey.push(element);
                }
            });
            console.log("tmpKey : " + JSON.stringify(tmpKey));
            keys = tmpKey;
            var ret = 0;
            for(var i = 0; keys.length > i; i++){

                var conditions = setting.conditions[keys[i]];
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
                    case 7: // 発言内容
                        if(ret !== null) { // その他の設定で無効の場合は何もしない
                          // あとで実行する関数のため、第三引数は値渡しで対応する必要がある
                          var cloneCondition = JSON.parse(JSON.stringify(conditions[0]));
                          this.judge.setMatchSpeechContent(1, window.sincloInfo.messages[key].id, cloneCondition,function(err, timer){
                            console.log("【AND】setMatchSpeechContent triggered!! : " + JSON.stringify(cloneCondition));
                            if (err) {
                              ret = null;
                              return;
                            }
                            sinclo.chatApi.saveAutoSpeechTriggered(cloneCondition.speechTriggerCond, window.sincloInfo.messages[key].id);
                            ret = Number(cloneCondition.triggerTimeSec) * 1000;
                            callback(7, cloneCondition, key, ret);
                          });
                          ret = {
                            delay: ret
                          }
                        }
                        break;
                  case 8: // 最初に訪れたページ
                    this.judge.pageOfFirst(conditions[0], function(err, timer){
                      if (err) ret = null;
                    });
                    break;
                  case 9: // 前のページ
                    this.judge.pageOfPrevious(conditions[0], function(err, timer){
                      if (err) ret = null;
                    });
                    break;
                  case 10: // 営業時間
                    this.judge.operating_hours(conditions[0], function(err, timer){
                        if (err) ret = null;
                    });
                  break;
                  default:
                    console.error("automessage condition is not defined : " + Number(keys[i]));
                    ret = null;
                    break;
                }
                if (ret === null) break;
            }
            callback(null, null, key, ret);
        },
        /**
         * return 即時実行(0)、タイマー実行(ミリ秒)、非実行(null)
         */
        setOrSetting: function(key, setting, callback) {
          console.log("setOrSetting key : " + key + " setting : " + JSON.stringify(setting));
            var keys = Object.keys(setting.conditions);
            var ret = null;
            for(var i = 0; keys.length > i; i++){
                var conditions = setting.conditions[keys[i]], u;
                var last = (keys.length === Number(i+1)) ? true : false;
                switch(Number(keys[i])) {
                    case 1: // 滞在時間
                        for (u = 0; u < conditions.length; u++) {
                          this.judge.stayTime(conditions[u], function(err, timer){
                              if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                  ret = Number(timer);
                              }
                          });
                        }
                        break;
                    case 2: // 訪問回数
                        for (u = 0; u < conditions.length; u++) {
                          this.judge.stayCount(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 3: // ページ
                        for (u = 0; u < conditions.length; u++) {
                          this.judge.page(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 4: // 曜日・時間
                        for (u = 0; u < conditions.length; u++) {
                          this.judge.dayTime(conditions[u], function(err, timer){
                              if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                  ret = Number(timer);
                              }
                          });
                        }
                        break;
                    case 5: // リファラー
                        for (u = 0; u < conditions.length; u++) {
                          this.judge.referrer(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 6: // 検索ワード
                        for (u = 0; u < conditions.length; u++) {
                          this.judge.searchWord(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 7: // 発言内容
                      for (u = 0; u < conditions.length; u++) {
                        console.log("DEBUG : conditions => " + JSON.stringify(conditions));
                        var condition = JSON.parse(JSON.stringify(conditions[u])); // 参照先が変わってもいいように値渡し

                        this.judge.setMatchSpeechContent(2, window.sincloInfo.messages[key].id, condition, function (err, timer) {
                          console.log("【OR】setMatchSpeechContent triggered!! : " + JSON.stringify(condition));
                          if (err) {
                            return;
                          }
                          sinclo.chatApi.saveAutoSpeechTriggered(condition.speechTriggerCond, window.sincloInfo.messages[key].id);
                          ret = Number(condition.triggerTimeSec) * 1000;
                          callback(7, condition, key, ret);
                        });
                      }
                      break;
                    case 8: // 最初に訪れたページ
                      for (u = 0; u < conditions.length; u++) {
                        this.judge.pageOfFirst(conditions[u], function (err, timer) {
                          if (!err) {
                            ret = 0;
                          }
                        });
                      }
                      break;
                    case 9: // 前のページ
                      for (u = 0; u < conditions.length; u++) {
                        this.judge.pageOfPrevious(conditions[u], function (err, timer) {
                          if (!err) {
                            ret = 0;
                          }
                        });
                      }
                      break;
                    case 10: // 営業時間設定
                      for (u = 0; u < conditions.length; u++) {
                        this.judge.operating_hours(conditions[u], function(err, timer){
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

            callback(null, null, key, ret);
        },
        setAutoMessage: function(id, cond){
            if(sincloInfo.widget.showTiming === 3) {
              console.log("オートメッセージ表示処理発動");
              // 初回オートメッセージ表示時にフラグを立てる
              sincloInfo.widgetDisplay = true;
              common.widgetHandler.show();
            }

            // 発言内容によるオートメッセージかチェックする
            var isSpeechContent = false;
            for(var key in cond.conditions) {
              console.log("DEBUG => key : " + key);
              if(key === "7") { // FIXME マジックナンバー
                isSpeechContent = true;
              }
            }

            console.log("IS SPEECH CONTENT : " + isSpeechContent);

            var data = {
                chatId:id,
                message:cond.message,
                isAutoSpeech: isSpeechContent
            };

            
            if(!sinclo.chatApi.autoMessages.exists(data.chatId) && !isSpeechContent) {
              //resAutoMessagesで表示判定をするためにidをkeyとして空Objectを入れる
              sinclo.chatApi.autoMessages.push(data.chatId, {});
            }

            if ( sinclo.chatApi.saveFlg ) {
                // オートメッセージの内容をDBに保存し、オブジェクトから削除する
              console.log("EMIT sendAutoChat::setAutoMessage");
                emit("sendAutoChat", {messageList: [data]});
            }
            else {
              console.log("EMIT sendAutoChatMessage::setAutoMessage");
                emit('sendAutoChatMessage', data);
            }
        },
        setAction: function(id, type, cond){
            console.log("setAction id : " + id + " type : " + type + " cond : " + JSON.stringify(cond));
            // TODO 今のところはメッセージ送信のみ、拡張予定
            var chatActFlg = storage.s.get('chatAct');
            console.log("chatActFlg : " + chatActFlg);
            if ( !check.isset(chatActFlg) ) {
              chatActFlg = "false";
            }

            if ( String(type) === "1" && ('message' in cond) && (String(chatActFlg) === "false") ) {
              
                if(sinclo.chatApi.autoMessages.exists(id)){
                  console.log("exists id : " + id);
                  return;
                }
                sinclo.chatApi.createMessageUnread("sinclo_re", cond.message, sincloInfo.widget.subTitle);
                sinclo.chatApi.scDown();
                var prev = sinclo.chatApi.autoMessages.getByArray();

                var setAutoMessageTimer = setInterval(function(){
                  console.log("監視中");
                    var date = common.fullDateTime();
                    if ( prev.length === 0 || (prev.length > 0 && prev[prev.length - 1].created !== date) ) {
                      clearInterval(setAutoMessageTimer);
                      sinclo.trigger.setAutoMessage(id, cond);
                      // 自動最大化
                      if ( !('widgetOpen' in cond) || (check.smartphone() && sincloInfo.widget.hasOwnProperty('spAutoOpenFlg') && Number(sincloInfo.widget.spAutoOpenFlg) === 1) ) return false;
                      var flg = sinclo.widget.condifiton.get();
                      console.log("自動最大化設定");
                      if(Number(cond.widgetOpen) === 1 && !common.widgetHandler.isShown()) {
                        console.log("オートメッセージによる最大化フラグセット");
                        storage.s.set('preWidgetOpened', true);
                      } else if ( Number(cond.widgetOpen) === 1 && String(flg) === "false" ) {
                        console.log("オートメッセージ最大化処理");
                        sinclo.operatorInfo.ev();
                      }
                    }
                }, 1);

            }
        },
        fireChatEnterEvent: function(msg) {
          $(this).trigger('chatEntered',msg);
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
              console.log("pregMatch type: " + type + " a: " + a + " b: " + b);
                var result = false;
                var preg = "";
                switch(Number(type)) {
                    case 1: // 一致
                      preg = new RegExp("^" + a + "$");
                      result = preg.test(b);
                      break;
                    case 2: // 部分一致
                      preg = new RegExp(a);
                      result = preg.test(b);
                      break;
                    case 3: // 不一致
                      preg = new RegExp("^" + a + "$");
                      result = !preg.test(b);
                      break;
                }
                console.log("result : " + result);
                return result;
            }
        },
        judge: {
            speechContentRegEx: [],
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
            },
            setMatchSpeechContent: function(conditionType, id, cond, callback) {
              if (!('speechContent' in cond) || !('speechContentCond' in cond )) return false;
              this.speechContentRegEx.push({
                id:  id,
                type: cond.speechContentCond,
                text: cond.speechContent,
                delay: cond.triggerTimeSec,
                conditionType: conditionType,
                callback: callback
              });
            },
            matchAllSpeechContent: function(msg, callback) {
              // FIXME マッチした処理が２回以上の場合、チャット送信処理も２回以上処理される
              var matched = false;
              if((!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false") && this.speechContentRegEx.length > 0) {
                for (var index in this.speechContentRegEx) {
                  if(sinclo.chatApi.triggeredAutoSpeechExists(this.speechContentRegEx[index].id)) {
                    console.log("triggeredAutoSpeechExists. Ignored. id : " + this.speechContentRegEx[index].id);
                    continue;
                  }
                  if(sinclo.trigger.timerTriggeredList.hasOwnProperty(this.speechContentRegEx[index].id)
                    && !sinclo.trigger.timerTriggeredList[this.speechContentRegEx[index].id]) {
                    console.log("timer is not triggered. Ignored. id : " + this.speechContentRegEx[index].id);
                    continue;
                  }
                  console.log("matching judge + " + this.speechContentRegEx[index]);
                  if(sinclo.trigger.common.pregMatch(this.speechContentRegEx[index].type, this.speechContentRegEx[index].text, msg)) {
                    this.speechContentRegEx[index].callback(false, this.speechContentRegEx[index].delay);
                    matched = true;
                  }
                }
                callback(matched);
              } else {
                //発言内容設定が無いのでそのままtrueを返す
                callback(matched);
              }
            },
            pageOfFirst: function(cond, callback){
              if (!('keyword' in cond) || !('targetName' in cond ) || !('stayPageCond' in cond )) return callback(true, null);
              var target = ( Number(cond.targetName) === 1 ) ? userInfo.prev[0].title : userInfo.prev[0].url;
              if (sinclo.trigger.common.pregMatch(cond.stayPageCond, cond.keyword, target)) {
                callback(false, 0);
              }
              else {
                callback(true, null);
              }
            },
            pageOfPrevious: function(cond, callback){
              if (!('keyword' in cond) || !('targetName' in cond ) || !('stayPageCond' in cond )) return callback(true, null);
              var previousLength = userInfo.prev.length-2;
              if(previousLength < 0) {
                // 前のページ情報が存在しないため実行しない
                callback(true, null);
                return;
              }
              var target = ( Number(cond.targetName) === 1 ) ? userInfo.prev[previousLength].title : userInfo.prev[previousLength].url;
              if (sinclo.trigger.common.pregMatch(cond.stayPageCond, cond.keyword, target)) {
                callback(false, 0);
              }
              else {
                callback(true, null);
              }
            },
            operating_hours: function(cond, callback){
              if (!('operatingHoursTime' in cond)) return callback(true, null);
              var check = "";
              var now = cond.now;
              var nowDay = cond.nowDay;
              var dateParse = cond.dateParse;
              var date = cond.date;
              var today = cond.today;
              //営業時間設定の条件が「毎日」の場合
              if(cond.type == 1) {
                var day = { 0:'sun', 1:'mon', 2:'tue', 3:'wed', 4:'thu', 5:'fri', 6:'sat'};
                day = day[nowDay];
                timeData = cond.everyday[day];
                publicHolidayData = cond.everyday['pub'];
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
                timeData = cond.weekly[day];
                publicHolidayData = cond.weekly['weekpub'];
              }
              publicHoliday = cond.publicHoliday;

              //営業時間設定を利用している場合
              if(cond.operatingHoursTime == 1) {
                //祝日の場合
                for(var i2=0; i2<publicHoliday.length; i2++) {
                  if(today == publicHoliday[i2].month + publicHoliday[i2].day) {
                    check = true;
                    if(publicHolidayData[0].start != null && publicHolidayData[0].end != null) {
                      for(var i=0; i<publicHolidayData.length; i++){
                        if( Date.parse(new Date(date + publicHolidayData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + publicHolidayData[i].end)) ) {
                          callback(false, 0);
                          return;
                        }
                      }
                    }
                  }
                }
                if(check != true) {
                  //営業時間設定を「休み」に設定している場合
                  if( timeData[0].start === null && timeData[0].end === null) {
                    callback(true, null);
                    return;
                  }
                  else {
                    for(var i=0; i<timeData.length; i++){
                      if( Date.parse(new Date(date + timeData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + timeData[i].end)) ) {
                        callback(false, 0);
                        return;
                      }
                      else {
                        callback(true, null);
                        return;
                      }
                    }
                  }
                }
              }
              //営業時間設定を利用していない場合
              else if(cond.operatingHoursTime == 2) {
                //祝日の場合
                for(var i2=0; i2<publicHoliday.length; i2++) {
                  if(today == publicHoliday[i2].month + publicHoliday[i2].day) {
                    check = true;
                    if(publicHolidayData[0].start != null && publicHolidayData[0].end != null) {
                      for(var i=0; i<publicHolidayData.length; i++){
                        if( Date.parse(new Date(date + publicHolidayData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + publicHolidayData[i].end)) ) {
                          callback(true, null);
                          return;
                        }
                      }
                    }
                  }
                }
                if(check != true) {
                  //営業時間設定を「休み」に設定している場合
                  if(timeData[0].start === null && timeData[0].end === null)  {
                    callback(false, 0);
                    return;
                  }
                  else {
                    for(var i=0; i<timeData.length; i++){
                      if( Date.parse(new Date(date + timeData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + timeData[i].end)) ) {
                        callback(true, null);
                        return;
                      }
                      else {
                        callback(false, 0);
                        return;
                      }
                    }
                  }
                }
              }
          }
        }
    },
    // 外部連携API
    api: {
      getAccessId : function() {
        var value = "";
        if(userInfo && userInfo.accessId) {
          value = userInfo.accessId;
        }
        return value;
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
