(function (jquery) {
  // -----------------------------------------------------------------------------
  //   websocket通信
  // -----------------------------------------------------------------------------
  var $ = jquery;
  sinclo = {
    widget: {
      condifiton: {
        get: function () {
          var sincloBox = document.getElementById('sincloBox');
          if (storage.s.get("widgetMaximized") !== null && sincloBox.getAttribute('data-openflg') !== storage.s.get("widgetMaximized")) {
            sincloBox.setAttribute('data-openflg', storage.s.get("widgetMaximized"));
            return storage.s.get("widgetMaximized");
          } else {
            return sincloBox.getAttribute('data-openflg');
          }
        },
        set: function (flg, overwrite) {
          var sincloBox = document.getElementById('sincloBox');
          sincloBox.setAttribute('data-openflg', flg);
          if (overwrite || storage.l.get("widgetMaximized") === null) {
            storage.l.set("widgetMaximized", flg);
            storage.s.set("widgetMaximized", flg);
          }
        }
      },
    },
    sorryMsgTimer: null,
    syncTimeout: "",
    operatorInfo: {
      header: null,
      toggle: function () {
        this.ev();
//        sincloBox.setAttribute('data-openflg', false);
        var flg = sinclo.widget.condifiton.get();
        //ウィジェットを開いた回数
        if (String(flg) === "true" && typeof ga == "function") {
          ga('send', 'event', 'sinclo', 'clickMaximize', location.href, 1);
        }
        if (String(flg) === "false" && typeof ga == "function") {
          ga('send', 'event', 'sinclo', 'clickMinimize', location.href, 1);
          //ウィジェットを最小化した回数追加
          var now = new Date();
          month = ('0' + (now.getMonth() + 1)).slice(-2);
          day = ('0' + now.getDate()).slice(-2);
          hour = ('0' + now.getHours()).slice(-2);
          emit('addClickMinimizeCounts', {
            siteKey: sincloInfo.site.key,
            year: now.getFullYear(),
            month: month,
            day: day,
            hour: hour
          });
        }
      },
      ev: function() {
        sinclo.adjustSpWidgetSize();
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
        //非表示の状態
        var closeAct = storage.s.get('closeAct');
        if (bannerAct !== "true" && closeAct !== "true") {
          //アニメーションさせる
          //最小化時と最大化時の状態を取得
          var abridgementType = common.getAbridgementType();
          if (String(flg) === "false") {
            //最大化時ボタン表示
            common.whenMaximizedBtnShow();
            //最大化
            if (abridgementType['MaxRes']) {
              //ヘッダ非表示（シンプル表示）
              common.abridgementTypehide();
            }
            else {
              //ヘッダ表示（通常表示）
              common.abridgementTypeShow();
            }

            //自由入力エリアが閉まっているか空いているかチェック
            var textareaOpend = storage.l.get('textareaOpend');
            //チャットのテキストエリア表示
            if (textareaOpend == 'close') {
              sinclo.hideTextarea();
            }
            //チャットのテキストエリア非表示
            else {
              sinclo.displayTextarea();
              if(Number(sincloInfo.widget.widgetSizeType) === 4){
                common.widgetHandler._maximumAnimation();
              }
            }

            sinclo.widget.condifiton.set(true, true);
            if (check.smartphone() && window.sincloInfo.contract.chat && (window.screen.availHeight < window.screen.availWidth)) {
              //スマホ横
              height = window.innerHeight * (document.body.clientWidth / window.innerWidth);
            }
            else {
              height += $("#sincloWidgetBox #widgetHeader").outerHeight(true);
              if ($("#sincloWidgetBox").children().is("#navigation")) {
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
            if (window.sincloInfo.contract.chat) {
              sinclo.chatApi.showUnreadCnt();
              sinclo.chatApi.scDown();
            }
            //画像がない時のタイトル位置
            if ($('#mainImage').css('display') === 'none' || $('#mainImage').css('display') === undefined) {
              common.indicateSimpleNoImage();
            }
            //画像がある時のタイトル位置
            else if ($('#mainImage').css('display') === 'block' || $('#mainImage').css('display') === 'inline') {
              common.indicateSimpleImage();
            }
            sinclo.chatApi.lockPageScroll();
          }
          else {
            //最小化時ボタン表示
            common.whenMinimizedBtnShow();
            //最小化
            if (abridgementType['MinRes']) {
              //ヘッダ非表示（シンプル表示）
              common.abridgementTypehide();
            }
            else {
              //ヘッダ表示（通常表示）
              common.abridgementTypeShow();
            }
            //画像がない時のタイトル位置
            if ($('#mainImage').css('display') === 'none' || $('#mainImage').css('display') === undefined) {
              common.indicateSimpleNoImage();
            }
            //画像がある時のタイトル位置
            else if ($('#mainImage').css('display') === 'block' || $('#mainImage').css('display') === 'inline') {
              common.indicateSimpleImage();
            }
            height = this.header.offsetHeight;
            if(Number(window.sincloInfo.widget.widgetSizeType) === 4){
              common.widgetHandler._maximumReverseAnimation();
            }
            sinclo.widget.condifiton.set(false, true);
            sinclo.chatApi.unlockPageScroll();
          }
          if(check.smartphone() && Number(window.sincloInfo.widget.spWidgetViewPattern) === 3 && $('#minimizeBtn').is(':hidden') && Number(window.sincloInfo.widget.closeButtonSetting) === 2 && Number(window.sincloInfo.widget.closeButtonModeType) === 1){
            console.log('<><><><><><><><><><>スマホ用隠しパラメータ、即バナー<><><><><><><><><><><>');
            elm.css('height', height + 'px');
            sinclo.operatorInfo.closeBtn();
          } else {
            elm.animate({
            height: height + "px"
            }, 'first', null, function(){
              console.log('$(\'#sincloBox\').offset().top : %s, $(\'#sincloWidgetBox\').offset().top',$('#sincloBox').offset().top, $('#sincloWidgetBox').offset().top);
              $('#sincloWidgetBox').offset({top: $('#sincloBox').offset().top});
            });
          }
        }
        else if (closeAct !== "true") {
          //バナー表示時の位置を設定
          $("#sincloBox").css("height", "");
          sinclo.operatorInfo.bannerBottomLeftRight();
        }
      },
      //閉じるボタンがクリックされた時の挙動
      closeBtn: function () {
        //閉じるボタンをクリックした回数
        if (typeof ga == "function") {
          ga('send', 'event', 'sinclo', 'clickClose', location.href, 1);
          //閉じるボタンクリック数追加
          var now = new Date();
          month = ('0' + (now.getMonth() + 1)).slice(-2);
          day = ('0' + now.getDate()).slice(-2);
          hour = ('0' + now.getHours()).slice(-2);
          emit('addClickCloseCounts', {
            siteKey: sincloInfo.site.key,
            year: now.getFullYear(),
            month: month,
            day: day,
            hour: hour
          });
        }
        //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
        if (Number(window.sincloInfo.widget.closeButtonSetting) === 2 && Number(window.sincloInfo.widget.closeButtonModeType) === 1) {
          //バナー表示にする
          sinclo.operatorInfo.onBanner();
        }
        else {
          //非表示状態になった
          storage.s.set('closeAct', true);
          //チャットを閉じる
          $("#sincloBox").hide();
        }
      },
      //バナー表示時の位置を設定
      bannerBottomLeftRight: function() {
        if ( !check.smartphone() ) {
          var bannerHorizontalPosition = (window.sincloInfo.custom && window.sincloInfo.custom.widget && window.sincloInfo.custom.widget.bannerHorizontalPosition) ? window.sincloInfo.custom.widget.bannerHorizontalPosition : "20px";
          var bannerVerticalPosition = (window.sincloInfo.custom && window.sincloInfo.custom.widget && window.sincloInfo.custom.widget.bannerVerticalPosition) ? window.sincloInfo.custom.widget.bannerVerticalPosition : "20px";
          //pc
          var bottom = bannerVerticalPosition;
          var leftRight = bannerHorizontalPosition;

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
        }
      },
      //バナー表示にする
      onBanner: function () {
        //バナー表示
        $("#sincloWidgetBox").hide();
        $("#sincloBox").css("outline", "none");
        $("#sincloBox").css("height", "");
        //バナー表示時の位置を設定
        sinclo.operatorInfo.bannerBottomLeftRight();
        //バナー表示状態になった
        storage.l.set('bannerAct', true);
        storage.s.set('bannerAct', true);
        $("#sincloBannerBox").show();
      },
      //バナーがクリックされた時の挙動
      clickBanner: function (showMinimize) {
        //バナー非表示状態になった
        storage.l.set('bannerAct', false);
        storage.s.set('bannerAct', false);
        $("#sincloWidgetBox").show();
        $("#sincloBannerBox").hide();
        $("#sincloBox").css("bottom", "0");
        //スマホかつ横かを判定
        if (check.smartphone()) {
          $("#sincloBox").css("right","");
          $("#sincloBox").css("left","");
          sinclo.adjustSpWidgetSize();
        }
        else{
          var widgetHorizontalPosition = "10px";
          var widgetVerticalPosition = "0px";
          if(!check.smartphone()) {
            widgetHorizontalPosition = (window.sincloInfo.custom && window.sincloInfo.custom.widget && window.sincloInfo.custom.widget.horizontalPosition) ? window.sincloInfo.custom.widget.horizontalPosition : "10px";
            widgetVerticalPosition = (window.sincloInfo.custom && window.sincloInfo.custom.widget && window.sincloInfo.custom.widget.verticalPosition) ? window.sincloInfo.custom.widget.verticalPosition : "0px";
          }
          common.widgetHandler._handleResizeEvent();
          switch ( Number(window.sincloInfo.widget.showPosition) ) {
          case 1: // 右下
            //right: 10px;
            $("#sincloBox").css({
              "right": widgetHorizontalPosition,
              "bottom": widgetVerticalPosition
            });
            break;
          case 2: // 左下
            //left: 10px;
            $("#sincloBox").css({
              "left": widgetHorizontalPosition,
              "bottom": widgetVerticalPosition
            });
            break;
          }
        }
        //最小化時ボタン表示
        common.whenMinimizedBtnShow();
        sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
        if (!showMinimize) {
          sinclo.operatorInfo.ev();
        }
      },
      widgetHideTimer: null,
      nowScrollTimer: null,
      widgetHide: function(e) {


        if(sinclo.operatorInfo.widgetHideTimer) {
          clearTimeout(sinclo.operatorInfo.widgetHideTimer);
          sinclo.operatorInfo.widgetHideTimer = null;
        }

        if(sinclo.operatorInfo.nowScrollTimer) {
          clearTimeout(sinclo.operatorInfo.nowScrollTimer);
          sinclo.operatorInfo.nowScrollTimer = null;
        }

        if(e) e.stopPropagation();
        var sincloBox = document.getElementById('sincloBox');
        if ( !sincloBox ) return false;
        if ( check.android() && storage.s.get('closeAct') === 'true') {
          return false;
        }

        var openflg = sinclo.widget.condifiton.get();

        var height = document.getElementById('widgetTitle').clientHeight;
        if (height === 0) {
          height = 10;
        }
        var enableArea = browserInfo.scrollSize().y - height;

        if (enableArea < window.scrollY && String(openflg) === "false") {
          if(typeof window.sincloInfo.widget.spBannerPosition !== "undefined" &&
            (Number(window.sincloInfo.widget.spBannerPosition) === 3 || Number(window.sincloInfo.widget.spBannerPosition) === 4)){
            //バナーの位置が中央だった場合は下部でもバナー非表示にしない
            sincloBox.style.opacity = 1;
          } else {
            //バナーの位置が右下、左下の場合のみ、ページ下部でバナー非表示にする
            sincloBox.style.opacity = 0;
          }
        }
        else {
          if(typeof window.sincloInfo.widget.spScrollViewSetting !== "undefined" &&
             Number(window.sincloInfo.widget.spScrollViewSetting) === 1 &&
             storage.l.get('widgetMaximized') === "false"){
            //スクロール中はsincloBoxを隠す設定
            console.info("<><><><>スクロール中非表示設定<><><><>");
            sincloBox.style.opacity = 0;
            sinclo.operatorInfo.nowScrollTimer = setTimeout(function() {
              sincloBox.style.opacity = 1;
            },400);
          }
          else {
            sincloBox.style.opacity = 1;
          }
        }

        sinclo.operatorInfo.widgetHideTimer = setTimeout(function(){
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
      reCreateWidget: function (e) {
        if (e) e.stopPropagation();
        if (!check.smartphone()) return false; // 念のため
        if (sinclo.operatorInfo.reCreateWidgetTimer) {
          clearTimeout(sinclo.operatorInfo.reCreateWidgetTimer);
        }
        var sincloBox = document.getElementById('sincloBox');

        var screen = (window.screen.availHeight < window.screen.availWidth) ? 'horizontal' : 'vertical';
        var current = document.activeElement;
        if (current.id === "sincloChatMessage" && screen === sincloBox.getAttribute('data-screen')) {
          setTimeout(function () {
            sinclo.operatorInfo.reCreateWidget();
          }, 500);
          return false;
        }
        var openFlg = sinclo.widget.condifiton.get();

        if (sincloBox) {
          sincloBox.style.display = "none";
        }


        sinclo.operatorInfo.reCreateWidgetTimer = setTimeout(function () {
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
          //sinclo.operatorInfo.widgetHide();

          sinclo.chatApi.targetTextarea = document.getElementById('chatTalk');

          if (String(openFlg) === "true") {
            sinclo.widget.condifiton.set(true, true);

            //自由入力エリアが閉まっているか空いているかチェック
            var textareaOpend = storage.l.get('textareaOpend');
            //チャットのテキストエリア表示
            if (textareaOpend == 'close') {
              sinclo.hideTextarea();
            }
            //チャットのテキストエリア非表示
            else {
              sinclo.displayTextarea();
            }

            if (window.screen.availHeight < window.screen.availWidth) {
              sincloBox.style.height = document.documentElement.clientHeight + "px";
            }
            else {
              var height = $("#sincloBox #widgetHeader").outerHeight(true);
              height += $("#sincloBox #chatTab").outerHeight(true);
              sincloBox.style.height = height;
            }
            if (check.smartphone()) {
              sinclo.chatApi.lockPageScroll();
            }
          }
          else {
            //ここでもしバナーだったら二段階表示防止のために以下の処理を避ける
            var bannerAct = storage.l.get('bannerAct');
            if (bannerAct !== "true") {
              sinclo.widget.condifiton.set(false, true);
              sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
            }
            if (check.smartphone()) {
              sinclo.chatApi.unlockPageScroll();
            }
          }

          sincloBox.setAttribute('data-screen', screen); // 画面の向きを制御

          sinclo.chatApi.showUnreadCnt();
          sinclo.chatApi.scDown();

        }, 500);
      }
    },
    connect: function () {
      // 新規アクセスの場合
      var oldIpAddress = userInfo.getIp();
      if (!check.isset(userInfo.getTabId())) {
        userInfo.firstConnection = true;
        window.opener = null;
        userInfo.strageReset();
        userInfo.setReferrer();
        userInfo.gFrame = false;
        common.widgetHandler.resetMessageAreaState();
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
      if (window.sincloInfo.contract.chat && !(userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest))) {
        sinclo.chatApi.observeType.emit(false, "");
      }
      // モニタリング中であればスルー
      if (check.isset(userInfo.connectToken)) {
        common.load.start();
        if (Number(userInfo.accessType) === Number(cnst.access_type.guest)) {
          emitData.connectToken = userInfo.connectToken;
          userInfo.syncInfo.get();
          common.judgeShowWidget();

          emit('connectSuccess', {prevList: userInfo.prevList, prev: userInfo.prev}, function (ev) {
            emit('customerInfo', userInfo.getSendList());
          });
          emit('connectedForSync', {});

          // チャットの契約をしている場合はウィジェット表示
          if (window.sincloInfo.contract.chat && !(userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest))) {
            common.makeAccessIdTag();
          }
        }

        if (check.isset(common.tmpParams) && Number(userInfo.accessType) === Number(cnst.access_type.host)) {
          browserInfo.resetPrevList();
          emit('requestSyncStart', {
            accessType: common.params.type
          });
        }

        emit('reqUrlChecker', {reconnectFlg: browserInfo.connectFlg});

        // connectフラグ
        browserInfo.connectFlg = true;


        browserInfo.setPrevList();

        if (!check.isset(common.tmpParams)) {
          emit('connectContinue', {
            connectToken: userInfo.connectToken,
            accessType: common.params.type,
            receiverID: userInfo.vc_receiverID
          });

          var vcInfo = common.getVcInfo();
          if (typeof vcInfo !== 'undefined') {
            vcPopup.set(vcInfo.toTabId, vcInfo.receiverID);
          }

          window.clearTimeout(this.syncTimeout);
          this.syncTimeout = window.setTimeout(function () {
            emit('requestSyncStop', emitData);
            userInfo.syncInfo.unset();
          }, 5000);

        }
        return false;
      }
      // LiveAssistのSDKがあれば、このタイミングでイベントハンドラをセットしておき
      if (typeof(AssistSDK) !== 'undefined') {
        console.log("Assist SDK found. initSDKCallbacks");
        laUtil.initSDKCallbacks();
        if (storage.l.get('assist-localstorage-config') && !storage.s.get('assist-session-config')) {
          console.log("LA localstorage found. but sessionstorage not found. deleting");
          storage.l.unset('assist-localstorage-config');
        }
      }

      // connectフラグ
      browserInfo.connectFlg = true;

      if (check.isset(userInfo.accessId)) {
        emitData.accessId = userInfo.accessId;
      }

      if (check.isset(storage.s.get('inactiveTimeout')) && storage.s.get('inactiveTimeout') === "true") {
        // 再接続扱いとする
        emitData.inactiveReconnect = true;
        storage.s.set('inactiveTimeout', false);
      }

      if(!check.isset(oldIpAddress)) {
        console.log("FORCE FIRST CONNECT");
        emitData.forceFirstConnect = true;
      }

      emit('connected', {
        type: 'user',
        data: emitData
      });
    },
    retConnectedForSync: function (d) {
      var obj = common.jParse(d);
      if (('pagetime' in obj)) {
        userInfo.pageTime = obj.pagetime;
      }
      if (obj.hasOwnProperty('activeOperatorCnt')) {
        window.sincloInfo.activeOperatorCnt = obj.activeOperatorCnt;
      }
      if (obj.hasOwnProperty('opFlg')) {
        window.sincloInfo.opFlg = obj.opFlg;
      }
    },
    accessInfo: function (d) {
      var obj = common.jParse(d);
      if (obj.token !== common.token) return false;
      if (obj.hasOwnProperty('activeOperatorCnt')) {
        window.sincloInfo.activeOperatorCnt = obj.activeOperatorCnt;
      }
      if (obj.hasOwnProperty('opFlg')) {
        window.sincloInfo.opFlg = obj.opFlg;
      }
      if (obj.hasOwnProperty('pagetime')) {
        userInfo.pageTime = obj.pagetime;
      }

      if (check.isset(obj.accessId) && !check.isset(obj.connectToken)) {
        userInfo.set(cnst.info_type.access, obj.accessId, true);

        var setWidgetFnc = function () {
          if (window.sincloInfo.widget === undefined) {
            setTimeout(setWidgetFnc, 500);
          }
          else {
            common.makeAccessIdTag();
          }
        };

        setWidgetFnc();

      }

      if (obj.firstConnection) {
        if (!check.isset(userInfo.userId) && check.isset(obj.userId)) {
          userInfo.set(cnst.info_type.user, obj.userId);
        }

        if (userInfo.accessType === Number(cnst.access_type.guest)) {
          userInfo.set(cnst.info_type.ip, obj.ipAddress);
          userInfo.set(cnst.info_type.time, obj.time, true);
        }
        userInfo.setTabId();
      }

      if(obj.sincloSessionIdIsNew) {
        userInfo.setStayCount();
        storage.l.unset('widgetOpen');
        storage.l.unset('widgetMaximized');
      } else {
        var widgetOpened = storage.l.get('widgetOpen');
        var widgetMaximized = storage.l.get('widgetMaximized');
        storage.s.set('widgetOpen', widgetOpened);
        storage.s.set('widgetMaximized', widgetMaximized);
      }

      if (obj.sincloSessionIdIsNew || (!check.isset(userInfo.sincloSessionId) && check.isset(obj.sincloSessionId))) {
        if (obj.sincloSessionIdIsNew) console.log("sincloSessionIdIsNew");
        userInfo.oldSincloSessionId = userInfo.sincloSessionId ? userInfo.sincloSessionId : "";
        userInfo.set(cnst.info_type.sincloSessionId, obj.sincloSessionId, "sincloSessionId");
        common.widgetHandler.resetMessageAreaState();
        storage.l.set('leaveFlg', 'false');
        storage.s.unset('amsg');
        storage.s.unset('chatAct');
        storage.s.unset('chatEmit');
        storage.l.unset('bannerAct');
        sinclo.scenarioApi.reset();
        userInfo.setPrevpage(true);
      }

      obj.prev = userInfo.writePrevToLocalStorage();
      obj.stayCount = userInfo.getStayCount();
      var connectSuccessData = {
        confirm: false,
        widget: window.sincloInfo.widgetDisplay,
        prevList: userInfo.prevList,
        userAgent: window.navigator.userAgent,
        time: userInfo.time,
        ipAddress: userInfo.getIp(),
        referrer: userInfo.referrer
      };

      if (obj.inactiveReconnect) {
        var tmpAutoMessages = sinclo.chatApi.autoMessages.get(true);
        connectSuccessData.tmpAutoMessages = tmpAutoMessages;
      }

      emit('connectSuccess', connectSuccessData, function (ev) {
        if ((userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest)) === false) {
          emit('customerInfo', obj);
        }
      });

      // customEvent
      if (document.createEvent) {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent('sinclo:connected', true, true);
        document.dispatchEvent(evt);
      } else {
        var evt = document.createEventObject();
        document.fireEvent('sinclo:connected', evt);
      }
    },
    setHistoryId: function (d) {
      var obj = common.jParse(d),
        createStartTimer,
        createStart = function () {
          console.log("create start");
          var sincloBox = document.getElementById('sincloBox');
          common.reloadWidget();
          if ( window.sincloInfo.contract.chat && check.smartphone() ) {
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

      if (document.getElementById('sincloBox') === null) return false;
      if (obj.stayLogsId) sinclo.chatApi.stayLogsId = obj.stayLogsId;

      createStartTimer = window.setInterval(function () {
        if (window.sincloInfo.widget.showTiming !== 4 || (window.sincloInfo.widgetDisplay && !sinclo.trigger.flg)) {
          window.clearInterval(createStartTimer);
          createStart();
        } else if (window.sincloInfo.widgetDisplay && (sinclo.trigger.flg && !sinclo.chatApi.inactiveCloseFlg)) {
          // 再接続時はウィジェットが表示されたタイミングでチャット情報を再読み込みする
          window.clearInterval(createStartTimer);
          if (window.sincloInfo.contract.chat) {
            // チャット情報読み込み
            sinclo.chatApi.init();
          }
        }
      }, 500);
    },
    getAccessInfo: function (d) { // guest only
      var obj = common.jParse(d);
      if (userInfo.accessType !== Number(cnst.access_type.guest)) return false;
      var emitData = userInfo.getSendList();
      emitData.receiveAccessInfoToken = obj.token;
      emitData.widget = window.sincloInfo.widgetDisplay;
      emitData.stayCount = userInfo.getStayCount();
      emit('sendAccessInfo', emitData);
    },
    confirmCustomerInfo: function (d) {
      var obj = common.jParse(d);
      if (userInfo.tabId !== obj.tabId) return false;
      if (userInfo.accessType !== cnst.access_type.guest) return false;
      var emitData = userInfo.getSendList();
      emitData.receiveAccessInfoToken = obj.token;
      emitData.stayCount = userInfo.getStayCount();
      emit('customerInfo', emitData);
    },
    createShareWindow: function (obj) { // 外部接続
      if (obj.tabId !== userInfo.tabId) return false;
      if (userInfo.accessType !== Number(cnst.access_type.guest)) return false;
      var title = location.host + 'の内容';
      var content = location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function () {
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
    cancelSharingApplication: function (obj) {
      popup.remove();
    },
    getWindowInfo: function (obj) {
      if (obj.tabId !== userInfo.tabId) return false;
      if (userInfo.accessType !== Number(cnst.access_type.guest)) return false;
      var title = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareBrowse.begin.headerMessage)) ? window.sincloInfo.custom.shareBrowse.begin.headerMessage : location.host + 'の内容';
      var content = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareBrowse.begin.content)) ? window.sincloInfo.custom.shareBrowse.begin.content : location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function () {
        var sincloBox = document.getElementById("sincloBox");
        if (sincloBox) {
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
      popup.no = function () {
        emit('sharingRejection', obj);
        this.remove();
      };
      popup.set(title, content);
    },
    startWindowSync: function (obj) {
      if (userInfo.accessType !== Number(cnst.access_type.guest)) return false;
      var title = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareBrowse.begin.headerMessage)) ? window.sincloInfo.custom.shareBrowse.begin.headerMessage : location.host + 'の内容';
      var content = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareBrowse.begin.content)) ? window.sincloInfo.custom.shareBrowse.begin.content : location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function () {
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
          "width=" + screen.availWidth * (2 / 3) + ",height=" + screen.availHeight * (2 / 3) +
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
    startCoBrowseOpen: function (obj) {
      if (userInfo.accessType !== Number(cnst.access_type.guest)) return false;
      var title = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareCoBrowse.begin.headerMessage)) ? window.sincloInfo.custom.shareCoBrowse.begin.headerMessage : location.host + 'の内容';
      var content = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareCoBrowse.begin.content)) ? window.sincloInfo.custom.shareCoBrowse.begin.content : location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function () {
        var sincloBox = document.getElementById("sincloBox");
        if (sincloBox) {
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
      popup.no = function () {
        emit('sharingRejection', obj);
        this.remove();
      };
      popup.set(title, content);
    },
    assistAgentIsReady: function (d) {
      console.log('assistAgentIsReady');
      var operatorId = d.responderId;
      var _self = this;
      laUtil.setOnErrorCallback(function (error) {
        _self.coBrowseErrorProcess();
      });
      laUtil.setOnAgentLeftCallback(function () {
        if (!check.isset(userInfo.coBrowseConnectToken)) return false;

        storage.s.unset("coBrowseConnectToken");
        if (!document.getElementById('sincloBox')) {
          common.makeAccessIdTag();
          if (window.sincloInfo.contract.chat) {
            // チャット情報読み込み
            sinclo.chatApi.init();
          }
        }

        // 終了通知
        var title = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareCoBrowse.end.headerMessage)) ? window.sincloInfo.custom.shareCoBrowse.end.headerMessage : location.host + 'の内容';
        var content = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareCoBrowse.end.content)) ? window.sincloInfo.custom.shareCoBrowse.end.content : location.host + 'との画面共有を終了しました';
        popup.ok = function () {
          laUtil.disconnect();
          this.remove();
        };
        popup.set(title, content, popup.const.action.alert);

        var timer = setInterval(function () {
          if (window.sincloInfo.widgetDisplay === false) {
            clearInterval(timer);
            laUtil.disconnect();
            return false;
          }
          var sincloBox = document.getElementById('sincloBox');
          // チャット未契約のときはウィジェットを非表示
          if (sincloBox && (window.sincloInfo.contract.chat || window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document))) {
            common.widgetHandler.show();
            sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
            sinclo.widget.condifiton.set(false, true);
            clearInterval(timer);
          }
        }, 500);
      });
      laUtil.initAndStart(operatorId).then(function () {
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
      }).fail(function (e) {
        _self.coBrowseErrorProcess();
      });
    },
    coBrowseErrorProcess: function () {
      // 終了通知
      emit('coBrowseFailed', {
        userId: userInfo.userId,
        tabId: userInfo.tabId,
        connectToken: userInfo.coBrowseConnectToken
      });
      var title = location.host + 'の内容';
      var content = '接続時にエラーが発生しました。<br>再度お試しください。';
      popup.ok = function () {
        laUtil.disconnect();
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);
    },
    windowSyncInfo: function (d) {
      var obj = common.jParse(d);
      browserInfo.set.scroll(obj.scrollPosition);
    },
    syncStart: function (d) {
      var obj = common.jParse(d);
      if (Number(userInfo.accessType) === Number(cnst.access_type.host)) {
        window.clearTimeout(this.syncTimeout);
        return false;
      }
      var sincloBox = document.getElementById('sincloBox');
      // チャット未契約か、外部接続のときはウィジェットを非表示
      if (sincloBox && (!window.sincloInfo.contract.chat || (userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest)))) {
        sincloBox.style.display = "none";
      }
      if (!(userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest))) {
        common.load.start();
      }
      if (!check.isset(userInfo.sendTabId)) {
        userInfo.sendTabId = obj.tabId;
        userInfo.syncInfo.set();
      }
      else {
        userInfo.syncInfo.get();
      }
      // フォーム情報収集
      var inputInfo = [];
      $('input').each(function () {
        inputInfo.push(this.value);
      });
      var checkboxInfo = [];
      $('input[type="checkbox"]').each(function () {
        checkboxInfo.push(this.checked);
      });
      var radioInfo = [];
      $('input[type="radio"]').each(function () {
        radioInfo.push(this.checked);
      });
      var textareaInfo = [];
      $('textarea').each(function () {
        textareaInfo.push(this.value);
      });
      var selectInfo = [];
      $('select').each(function () {
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
      emit('sendTabInfo', {status: browserInfo.getActiveWindow(), widget: window.sincloInfo.widgetDisplay});
    },
    syncElement: function (d) {
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
          complete: function () {
            var i, n;
            for (i in obj.inputInfo) {
              n = Number(i);
              $('input').eq(n).val(obj.inputInfo[n]);
            }
            for (i in obj.checkboxInfo) {
              n = Number(i);
              $('input[type="checkbox"]').eq(n).prop("checked", obj.checkboxInfo[n]);
            }
            for (i in obj.radioInfo) {
              n = Number(i);
              $('input[type="radio"]').eq(n).prop("checked", obj.radioInfo[n]);
            }
            for (i in obj.textareaInfo) {
              n = Number(i);
              $('textarea').eq(n).val(obj.textareaInfo[n]);
            }
            for (i in obj.selectInfo) {
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
    syncEvStart: function (d) {
      var obj = common.jParse(d);
      if (obj.to !== userInfo.tabId && obj.tabId !== userInfo.tabId) return false;
      syncEvent.start(true);
      window.clearTimeout(sinclo.syncTimeout);
      common.load.finish();
    },
    receiveScTimer: false,
    syncResponce: function (d) {
      var obj = common.jParse(d), cursor = common.cursorTag;
      // 画面共有用トークンでの認証に変更する？
      if (check.isset(obj.to) && obj.to !== userInfo.tabId) return false;
      if (Number(obj.accessType) === Number(userInfo.accessType)) return false;
      // カーソルを作成していなければ作成する
      if (!document.getElementById('cursorImg')) {
        $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mousePoint.x + '; left:' + obj.mousePoint.y + '; z-index:999999"><img width="50px" src="' + window.sincloInfo.site.files + '/img/pointer.png"></div>');
        cursor = common.cursorTag = document.getElementById("cursorImg");
      }
      else {
        // スクロール位置
        if (check.isset(obj.scrollPosition)) {
          syncEvent.receiveEvInfo.type = "scroll";
          syncEvent.receiveEvInfo.nodeName = "body";
          if (this.receiveScTimer) {
            clearTimeout(this.receiveScTimer);
          }

          browserInfo.set.scroll(obj.scrollPosition);

          // TODO まだ微調整が必要
          this.receiveScTimer = setTimeout(function () {
            syncEvent.receiveEvInfo = {nodeName: null, type: null};
          }, browserInfo.interval);
        }
      }
      // カーソル位置
      if (check.isset(obj.mousePoint)) {
        cursor.style.left = obj.mousePoint.x + "px";
        cursor.style.top = obj.mousePoint.y + "px";
      }
    },
    syncResponceEv: function (d) {
      var obj = common.jParse(d), elm;
      if (obj.to !== userInfo.tabId) return false;
      if (obj.accessType === userInfo.accessType) return false;
      elm = $(String(obj.nodeName)).eq(Number(obj.idx));
      syncEvent.receiveEvInfo.type = obj.type;
      syncEvent.receiveEvInfo.nodeName = String(obj.nodeName);
      syncEvent.receiveEvInfo.idx = Number(obj.idx);
      switch (obj.type) {
        case "change":
          if (String(obj.nodeType) === "radio" || String(obj.nodeType) === "checkbox") {
            elm.prop('checked', obj.checked);
          }
          elm.val(obj.value);
          break;
        case "keyup":
          elm.val(obj.value);
          break;
        case "scroll":
          elm = $(obj.nodeName).eq(Number(obj.idx));
          if (elm.length > 0) {
            var scrollBarSize = {
              height: elm[0].scrollHeight - elm[0].clientHeight,
              width: elm[0].scrollWidth - elm[0].clientWidth
            };
            elm.stop(false, false).scrollTop(scrollBarSize.height * Number(obj.value.topRatio));
            elm.stop(false, false).scrollLeft(scrollBarSize.width * Number(obj.value.leftRatio));
          }
          break;
      }
      syncEvent.receiveEvInfo = {nodeName: null, type: null};
    },
    syncBrowserCtrl: function (d) {
      var obj = JSON.parse(d);

      if (userInfo.accessType !== Number(cnst.access_type.guest)) return false;
      if (!obj.state) return false;

      // 進む
      if (Number(obj.state) > 0) {
        history.forward();
      }
      // 戻る
      else if (Number(obj.state) < 0) {
        history.back();
      }
    },
    syncContinue: function (d) {
      var obj = JSON.parse(d);
      if (obj.connectToken !== userInfo.connectToken) return false;
      if (obj.to !== userInfo.tabId) return false;
      emit('requestSyncStart', obj);
    },
    resUrlChecker: function (d) {
      var obj = JSON.parse(d);
      if (obj.url !== browserInfo.href) {
        location.href = obj.url;
      }
      else {
        emit('requestSyncStart', {
          accessType: common.params.type
        });
      }
    },
    chatStartResult: function (d) {
      var obj = JSON.parse(d), opUser;
      this.chatApi.online = true;
      storage.s.set('chatAct', true); // オートメッセージを表示しない
      storage.s.set('operatorEntered', true); // オペレータが入室した
      storage.l.set('leaveFlg', 'false'); // オペレータが入室した

      //サイト訪問者側のテキストエリア表示
      sinclo.displayTextarea();
      storage.l.set('textareaOpend', 'open');
      if (sinclo.scenarioApi.isProcessing()) {
        sinclo.chatApi.hideMiniMessageArea();
      }

      switch (sincloInfo.widget.showName) {
        case 1:
          sinclo.chatApi.opUser = obj.userName;
          sinclo.chatApi.opUserName = obj.userName;
          opUser = obj.userName;
          break;
        case 2:
          sinclo.chatApi.opUserName = obj.userName;
          opUser = "";
          break;
      }

      if (check.isset(opUser) === false) {
        opUser = "オペレーター";
      }
      check.escape_html(opUser); // エスケープ

      sinclo.chatApi.createNotifyMessage(opUser + "が入室しました");
      // チャットの契約をしている場合
      if ( window.sincloInfo.contract.chat ) {
        if(storage.s.get('mannedRequestFlg') === 'true') {
          //OPが入室した数
          //入室数についてはタブでカウントする
          if(typeof ga == "function" && obj.tabId === userInfo.tabId){
            ga('send', 'event', 'sinclo', 'manualChat', sinclo.chatApi.opUserName, 1);
          }
        }
      }
    },
    chatEndResult: function (d) {
      var obj = JSON.parse(d);
      this.chatApi.online = false;
      storage.s.set('operatorEntered', false); // オペレータが退室した
      storage.s.set('chatAct', false); // オートメッセージを表示してもいい
      storage.l.set('leaveFlg', 'true'); // オペレータが退室した
      storage.s.set('initialNotification', 'true'); // 初回通知メッセージ
      if (sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi.isScenarioLFDisabled()) {
        sinclo.chatApi.showMiniMessageArea();
      }
      var opUser = sinclo.chatApi.opUser;
      if (check.isset(opUser) === false) {
        opUser = "オペレーター";
      }
      check.escape_html(opUser); // エスケープ
      sinclo.chatApi.createNotifyMessage(opUser + "が退室しました");
      //退室した後に同じ消費者からメッセージが来た場合、それもGAのイベントとしてカウントするため
      sessionStorage.removeItem('chatEmit');
    },
    chatMessageData:function(d){
      console.log("chatMessgeData");
      console.log("DATA : %s", d);
      var obj = JSON.parse(d);
      if (obj.token !== common.token) return false;
      this.chatApi.historyId = obj.chat.historyId;
      var keys = (typeof(obj.chat.messages) === 'object') ? Object.keys(obj.chat.messages) : [];
      var prevMessageBlock = null;
      var firstCheck = true;
      for (var key in obj.chat.messages) {
        if (!obj.chat.messages.hasOwnProperty(key)) return false;
        var chat = obj.chat.messages[key], userName;
        if (Number(chat.messageType) < 90) {
          var cn = "sinclo_re";
          switch(Number(chat.messageType)) {
            case 1:
            case 13:
            case 30:
              cn = "sinclo_se";
              break;
            case 12:
              cn = "cancelable sinclo_se";
              break;
          }

          if (Number(chat.messageReadFlg) === 0 && chat.messageType === sinclo.chatApi.messageType.company) {
            this.chatApi.unread++;
          }

          // オートメッセージは格納しとく
          if (Number(chat.messageType) === 3 && 'chatId' in chat) {
            if (check.isset(window.sincloInfo.messages)) {
              var found = false;
              for (var i=0; i<window.sincloInfo.messages.length; i++) {
                if(Number(window.sincloInfo.messages[i].id) === Number(chat.chatId)) {
                  var conditions = window.sincloInfo.messages[i].activity.conditions;
                  var isAutoSpeechMessage = false;
                  Object.keys(conditions).forEach(function(e,i,a){
                    if(Number(e) === 7) {
                      isAutoSpeechMessage = true;
                    }
                  });
                  if(isAutoSpeechMessage) continue;
                  console.log("push " + chat.chatId);
                  this.chatApi.autoMessages.push(chat.chatId, {
                    chatId: chat.chatId,
                    message: chat.message,
                    created: chat.created,
                    applied: chat.applied ? chat.applied : false
                  });
                  found = true;
                  break;
                }
              }
              if (!found) {
                // オートメッセージ設定で無効 or 削除された
                console.log("delete " + chat.chatId);
                window.sinclo.chatApi.autoMessages.delete(chat.chatId);
                continue;
              }
            }
          }

          // オートメッセージか、Sorryメッセージ、企業からのメッセージで表示名を使用しない場合
          console.log("window.sincloInfo.widget.showName: %s window.sincloInfo.widget.showAutomessageName: %s", window.sincloInfo.widget.showName, window.sincloInfo.widget.showAutomessageName);
          if (Number(chat.messageType) === 3
            || Number(chat.messageType) === 4
            || Number(chat.messageType) === 7
            || Number(chat.messageType) === 21
            || Number(chat.messageType) === 22
            || Number(chat.messageType) === 23
            || Number(chat.messageType) === 27
            || Number(chat.messageType) === 81
            || Number(chat.messageType) === 82) {
            if (check.isset(window.sincloInfo.widget.showAutomessageName) && window.sincloInfo.widget.showAutomessageName === 2) {
              userName = "";
            } else {
              userName = window.sincloInfo.widget.subTitle;
            }
          }
          else if (Number(chat.messageType) === sinclo.chatApi.messageType.company) {
            cn = "sinclo_re";
            sinclo.chatApi.call();
            console.log("sincloInfo.widget.showOpName : %s", sincloInfo.widget.showOpName);
            switch (sincloInfo.widget.showOpName) {
              case 1:
                userName = sinclo.chatApi.opUserName;
                break;
              case 2:
                userName = sincloInfo.widget.subTitle;
                break;
              case 3:
                userName = "";
                break;
              default: // 設定が存在しない場合
                if (sincloInfo.widget.showName === 1) {
                  userName = sinclo.chatApi.opUserName;
                } else {
                  userName = sincloInfo.widget.subTitle;
                }
                break;
            }
            console.log("userName : %s", userName);
          }

          if (sinclo.scenarioApi.isProcessing() && (!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false") && chat.showTextarea && chat.showTextarea === "1") {
            sinclo.displayTextarea();
          } else if (sinclo.scenarioApi.isProcessing() && (!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false") && chat.showTextarea && chat.showTextarea === "2") {
            sinclo.hideTextarea();
          }
          if (key.indexOf('_') >= 0 && 'applied' in chat && chat.applied) continue;
          if (Number(chat.messageType) === 6 || Number(chat.messageType) === 27) {
            // ファイル送信チャット表示
            if (chat.deleteFlg === 0) {
              this.chatApi.createSendFileMessage(JSON.parse(chat.message), userName);
            }
            // ファイル送信チャットが削除されている場合
            else if (chat.deleteFlg === 1) {
              var sendData = {
                message: chat.message,
                name: userName,
                chatId: chat.chatId,
                cn: cn,
              };
              this.chatApi.createMessage(sendData, ((Number(chat.messageType) > 20 && (Number(chat.messageType) < 29))));
            }
          }
          else if(Number(chat.messageType) === 8){
          }
          else if(Number(chat.messageType) === 19) {
            if(check.isJSON(chat.message)) {
              var result = JSON.parse(chat.message);
              this.chatApi.createSentFileMessage(result.comment, result.downloadUrl, result.extension);
            } else {
              var sendData = {
                message: chat.message,
                name: userName,
                chatId: chat.chatId,
                cn: "sinclo_se",
              };
              this.chatApi.createMessage(sendData, ((Number(chat.messageType) > 20 && (Number(chat.messageType) < 29))));
            }
          } else if (Number(chat.messageType) === 40) {
            if(sinclo.scenarioApi.isProcessing() && Object.keys(obj.chat.messages).indexOf(key) === Object.keys(obj.chat.messages).length - 1) {
              var data = JSON.parse(chat.message);
              sinclo.chatApi.createForm(true, data.target, data.message, sinclo.scenarioApi._bulkHearing.handleFormOK);
            } else {
              continue;
            }
          } else if (Number(chat.messageType) === 31 || Number(chat.messageType) === 32) {
            this.chatApi.createFormFromLog(JSON.parse(chat.message));
          } else if (Number(chat.messageType) === 81) {
            this.chatApi.createCogmoAttendBotMessage("sinclo_re", chat.message, userName, false);
          } else if (Number(chat.messageType) === 82) {
            this.chatApi.createCogmoAttendBotMessage("sinclo_re", chat.message, userName, true);
          } else if (Number(chat.messageType) === 41) {
            var pulldown = JSON.parse(chat.message);
            this.chatApi.addPulldown("sinclo_re", pulldown.message, userName, pulldown.settings);
          } else {
            //通知した場合
            if (chat.noticeFlg == 1 && firstCheck == true && sincloInfo.chat.settings.in_flg == 1) {
              var now = new Date();
              var targetDate = new Date(storage.s.get('notificationTime'));
              //現在時刻から通知された時間の差
              var diff = (now.getTime() - targetDate.getTime()) / 1000;
              var data = sincloInfo.chat.settings.initial_notification_message ? JSON.parse(sincloInfo.chat.settings.initial_notification_message) : {};
              for (var i = 0; i < Object.keys(data).length; i++) {
                if(storage.s.get('callingMessageSeconds') < data[i].seconds) {
                  (function(times) {
                    setTimeout(function() {
                      //オペレータが入室していなかった場合
                      if(storage.s.get('operatorEntered') !== 'true' && data[times].message !== "") {
                        var sendData = {
                          message: data[times].message,
                          name: sincloInfo.widget.subTitle,
                          chatId: 0,
                          cn: "sinclo_re",
                        };
                        sinclo.chatApi.createMessageUnread(sendData);
                        sinclo.chatApi.scDown();
                        var sendData = {
                          siteKey: obj.siteKey,
                          tabId: obj.tabId,
                          chatMessage: data[times].message,
                          messageType: sinclo.chatApi.messageType.notification,
                          messageDistinction: chat.messageDistinction,
                          mUserId: chat.userId,
                          userId: chat.visitorsId,
                        }
                        emit("sendInitialNotificationChat", {messageList: sendData});
                      }
                      storage.s.set('callingMessageSeconds',data[times].seconds);
                    },(data[times].seconds-diff)*1000);
                    firstCheck = false;
                  })(i);
                }
              }
            }
            console.log(JSON.stringify(chat, null, 4));
            this.chatApi.createMessage({cn: cn, message: chat.message, name: userName, chatId: chat.chatId}, ((Number(chat.messageType) > 20 && (Number(chat.messageType) < 29))));
          }
          // シナリオ実行中であればラジオボタンを非活性にする。
          if ((Number(chat.messageType) === 22 || Number(chat.messageType) === 23) && chat.message.match(/\[\]/) && prevMessageBlock === null) {
            prevMessageBlock = $('sinclo-chat').find('div:last-child');
          } else if(Number(chat.messageType) === 41) {
            prevMessageBlock = $('sinclo-chat').find('div:last-child');
          } else {
            if (prevMessageBlock !== null) {
              if(prevMessageBlock.find('[type="radio"]').length > 0) {
                var name = prevMessageBlock.find('[type="radio"], select option').attr('name');
                console.log("DISABLE RADIO NAME : " + name);
                var targetLabel = prevMessageBlock.find('label');
                var targetId = "";
                targetLabel.each(function (index, val) {
                  if (val.innerText.trim() === chat.message) {
                    targetId = $(val).attr('for');
                  }
                });
                if (targetId !== "") {
                  $('#' + targetId).prop('checked', true);
                }
              } else if(prevMessageBlock.find('select').length > 0) {
                prevMessageBlock.find('select').val(chat.message);
              }
              prevMessageBlock = null;
            }
          }
          this.chatApi.scDown();
        }
        else {
          var opUser = "";
          if (('userName' in obj.chat.messages[key])) {
            sinclo.chatApi.opUser = obj.chat.messages[key].userName;
          }
          console.log("chatMessageData :: sinclo.chatApi.opUser : %s", sinclo.chatApi.opUser);
          // 途中で設定が変更されたときの対策
          switch (sincloInfo.widget.showName) {
            case 1:
              sinclo.chatApi.opUserName = sinclo.chatApi.opUser;
              opUser = sinclo.chatApi.opUserName;
              break;
            case 2:
              sinclo.chatApi.opUserName = sinclo.chatApi.opUser;
              sinclo.chatApi.opUser = "";
              opUser = "";
              break;
          }

          if (opUser === "") {
            opUser = "オペレーター";
          }
          check.escape_html(opUser); // エスケープ

          if (Number(chat.messageType) === sinclo.chatApi.messageType.start) {
            this.chatApi.online = true;
            this.chatApi.createNotifyMessage(opUser + "が入室しました");
          }
          if (Number(chat.messageType) === sinclo.chatApi.messageType.end) {
            this.chatApi.online = false;
            this.chatApi.createNotifyMessage(opUser + "が退室しました");
            sinclo.chatApi.opUser = "";
          }
        }
      }
      if ((Number(window.sincloInfo.widget.showTiming) === 3 && Object.keys(window.sinclo.chatApi.autoMessages.get(true)).length > 0)) {
        // オートメッセージ発動済みのため表示する
        common.widgetHandler.saveShownFlg();
        window.sincloInfo.widgetDisplay = true;
        common.widgetHandler.show();
        //自由入力エリアが閉まっているか空いているかチェック
        var textareaOpend = storage.l.get('textareaOpend');
        //チャットのテキストエリア表示
        if (textareaOpend == 'close') {
          sinclo.hideTextarea();
        }
        //チャットのテキストエリア非表示
        else {
          sinclo.displayTextarea();
        }
      }
      if (!this.chatApi.online && !sinclo.trigger.flg) {
        // オートメッセージ読み込み
        sinclo.trigger.init();
      }
      if(sinclo.scenarioApi.isProcessing()) {
        if(!keys || ($.isArray(keys) && keys.length === 0)) {
          // シナリオ実行中にも関わらず受け取ったメッセージが空の場合はシナリオで>出力したメッセージが復旧できないためいったん削除する
          console.log('<><><><><><><><> RESTORE SCENARIO DATA <><><><><><><<><><>');
          var scenarioId = sinclo.scenarioApi.get(sinclo.scenarioApi._lKey.scenarioId);
          var scenarioData = sinclo.scenarioApi.get(sinclo.scenarioApi._lKey.scenarios);
          sinclo.scenarioApi.reset();
          sinclo.scenarioApi.init(scenarioId, scenarioData);
          sinclo.scenarioApi.begin();
        } else {
          sinclo.scenarioApi.init(null, null);
          sinclo.scenarioApi.begin();
        }
      }
      // 未読数
      sinclo.chatApi.showUnreadCnt();
    },
    sendChatResult: function (d) {
      console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>sendChatResult>>>");
      var obj = JSON.parse(d);
      common.chatBotTypingRemove();
      if (obj.sincloSessionId !== userInfo.sincloSessionId && obj.tabId !== userInfo.tabId) return false;
      var elm = document.getElementById('sincloChatMessage'), cn, userName = "";
      if (obj.ret) {
        if (obj.messageType === sinclo.chatApi.messageType.customer && storage.s.get('chatAct') !== "true" && !obj.matchAutoSpeech) {
          // 別タブで送信した自分のメッセージを受けたのでチャット応対中とする
          console.log("self message received. set chatAct = true");
          storage.s.set('chatAct', true);
        }
        if (obj.messageType === sinclo.chatApi.messageType.customer && storage.s.get('chatEmit') !== "true") {
          // 別タブで送信した自分のメッセージを受けたのでチャット送信状態とする
          console.log("self message received. set chatEmit = true");
          storage.s.set('chatEmit', true);
        }
        // スマートフォンの場合はメッセージ送信時に、到達確認タイマーをリセットする
        if (sinclo.chatApi.sendErrCatchTimer !== null) {
          clearTimeout(sinclo.chatApi.sendErrCatchTimer);
        }

        if (obj.messageType === sinclo.chatApi.messageType.company) {
          cn = "sinclo_re";
          sinclo.chatApi.call();
          console.log("sendChatResult :: sincloInfo.widget.showOpName : %s", sincloInfo.widget.showOpName);
          switch (sincloInfo.widget.showOpName) {
            case 1:
              userName = sinclo.chatApi.opUserName;
              break;
            case 2:
              userName = sincloInfo.widget.subTitle;
              break;
            case 3:
              userName = "";
              break;
            default: // 設定が存在しない場合
              if (sincloInfo.widget.showName === 1) {
                userName = sinclo.chatApi.opUserName;
              } else {
                userName = sincloInfo.widget.subTitle;
              }
              break;
          }
          console.log("sendChatResult :: userName : %s", userName);
        } else if(obj.messageType === sinclo.chatApi.messageType.scenario.customer.hearing) {
          cn = "cancelable sinclo_se";
          elm.value = "";
        } else if (obj.messageType === sinclo.chatApi.messageType.customer
          || obj.messageType === sinclo.chatApi.messageType.scenario.customer.selection
          || obj.messageType === sinclo.chatApi.messageType.scenario.customer.answerBulkHearing ) {
          cn = "sinclo_se";
          elm.value = "";
        } else if (obj.messageType === sinclo.chatApi.messageType.cogmo.message
          || obj.messageType === sinclo.chatApi.messageType.cogmo.feedback) {
          cn = "sinclo_re";
          if (window.sincloInfo.widget.showAutomessageName === 2) {
            userName = "";
          } else {
            userName = window.sincloInfo.widget.subTitle;
          }
          elm.value = "";
        }

        if (obj.messageType === sinclo.chatApi.messageType.auto || obj.messageType === sinclo.chatApi.messageType.autoSpeech
          || obj.messageType === sinclo.chatApi.messageType.scenario.message.text
          || obj.messageType === sinclo.chatApi.messageType.scenario.message.hearing
          || obj.messageType === sinclo.chatApi.messageType.scenario.message.pulldown
          || obj.messageType === sinclo.chatApi.messageType.scenario.message.selection
          || obj.messageType === sinclo.chatApi.messageType.scenario.message.receiveFile) {
          if (obj.messageType !== sinclo.chatApi.messageType.auto && storage.s.get('requestFlg') === 'true') {
            //自動返信を出した数
            if (typeof ga == "function") {
              ga('send', 'event', 'sinclo', 'autoChat', location.href, 1);
            }
            storage.s.set('requestFlg',false);
          };
          if(obj.tabId === userInfo.tabId) {
            //シナリオ中のみ発動
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ヒアリングの入力無効終了(ｽﾏﾎ)<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
            if(check.smartphone() && sinclo.scenarioApi.isProcessing()){
              var miniTextarea = document.getElementById("miniSincloChatMessage"),
                  textarea = document.getElementById("sincloChatMessage");
              if(textarea){
                textarea.disabled = false;
              }
              if(miniTextarea){
                miniTextarea.disabled = false;
              }
            }
            common.chatBotTypingCall(obj);
            return false;
          } else if (obj.messageType === sinclo.chatApi.messageType.autoSpeech) {
            // 別タブで送信された自動返信は表示する
            cn = "sinclo_re";
            if (window.sincloInfo.widget.showAutomessageName === 2) {
              userName = "";
            } else {
              userName = window.sincloInfo.widget.subTitle;
            }
          } else if (obj.messageType === sinclo.chatApi.messageType.scenario.message.text
            || obj.messageType === sinclo.chatApi.messageType.scenario.message.hearing
            || obj.messageType === sinclo.chatApi.messageType.scenario.message.selection) {
            // 別タブで送信されたシナリオのメッセージは表示する
            cn = "sinclo_re";
            if (window.sincloInfo.widget.showAutomessageName === 2) {
              userName = "";
            } else {
              userName = window.sincloInfo.widget.subTitle;
            }

            if(sinclo.scenarioApi._hearing._isConfirming()) {
              sinclo.scenarioApi._hearing._endInputProcess();
              sinclo.scenarioApi._hearing._showConfirmMessage();
            }
            sinclo.chatApi.createMessage({cn: cn, message: obj.chatMessage, name: userName, chatId: obj.chatId}, true);
            sinclo.chatApi.scDown();
            return false;
          } else if (obj.messageType === sinclo.chatApi.messageType.scenario.message.receiveFile) {
            this.chatApi.createSendFileMessage(JSON.parse(obj.chatMessage), sincloInfo.widget.subTitle);
            this.chatApi.scDown();
            return false;
          } else {
            // 別タブで送信されたオートメッセージは何もしない
            return false;
          }
        }

        if (obj.messageType === sinclo.chatApi.messageType.sendFile || obj.messageType === sinclo.chatApi.messageType.scenario.message.receiveFile) {
          sinclo.chatApi.call();
          this.chatApi.createSendFileMessage(JSON.parse(obj.chatMessage), sincloInfo.widget.subTitle);
          this.chatApi.scDown();
          return false;
        }

        if (obj.messageType === sinclo.chatApi.messageType.scenario.customer.sendFile) {
          sinclo.chatApi.call();
          common.chatBotTypingCall(obj);
          if(check.isJSON(obj.chatMessage)) {
            var result = JSON.parse(obj.chatMessage);
            this.chatApi.createSentFileMessage(result.comment, result.downloadUrl, result.extension);
            if(obj.tabId !== userInfo.tabId) {
              var deleteTarget = $('#sincloBox sinclo-chat li.recv_file_left');
              if($('#sincloBox sinclo-chat li.recv_file_left').length > 0) {
                $('#sincloBox sinclo-chat li.recv_file_left').parent().remove();
              }
            }
          } else {
            cn = "sinclo_se";
            this.chatApi.createMessage({cn: cn, message: obj.chatMessage, name: "", chatId: obj.chatId});
          }
          this.chatApi.scDown();
          return false;
        }

        if (obj.messageType === sinclo.chatApi.messageType.scenario.message.returnBulkHearing) {
          var data = JSON.parse(obj.chatMessage);
          common.chatBotTypingRemove();
          if(sinclo.scenarioApi.isProcessing()) {
            sinclo.chatApi.createForm(true, data.target, data.message, sinclo.scenarioApi._bulkHearing.handleFormOK);
          }
          return false;
        }

        if (obj.messageType === sinclo.chatApi.messageType.scenario.customer.noModBulkHearing
          || obj.messageType === sinclo.chatApi.messageType.scenario.customer.modifyBulkHearing) {
          this.chatApi.createFormFromLog(JSON.parse(obj.chatMessage));
          this.chatApi.scDown();
          setTimeout(function () {
            common.chatBotTyping(obj)
          }, 800);
          if (obj.tabId !== userInfo.tabId) {
            $('ul#chatTalk li.sinclo_re.sinclo_form:last-of-type').remove();
          }
          return false;
        }

        if (obj.messageType === sinclo.chatApi.messageType.cogmo.message
          || obj.messageType === sinclo.chatApi.messageType.cogmo.feedback) {
          var createMessageData = {
            cn: cn,
            message: obj.message,
            name: userName,
            chatId: obj.chatId
          };
          this.chatApi.createMessageUnread(createMessageData, false, true, obj.isFeedbackMsg);
          this.chatApi.scDown(obj);
          return false;
        }

        if (obj.messageType === sinclo.chatApi.messageType.sorry) {
          cn = "sinclo_re";
          if (window.sincloInfo.widget.showAutomessageName === 2) {
            userName = "";
          } else {
            userName = window.sincloInfo.widget.subTitle;
          }
          //Sorryメッセージが複数回呼ばれた場合は、タイマーが重複しないよう削除する
          if(sinclo.sorryMsgTimer){
            clearTimeout(sinclo.sorryMsgTimer);
          }
         common.chatBotTypingCall(obj);
          sinclo.sorryMsgTimer = setTimeout(function(){
            cn = "sinclo_re";
            sinclo.chatApi.call();
            sinclo.chatApi.createMessage({cn: cn, message: obj.chatMessage, name: userName, chatId: obj.chatId});
            if(sinclo.chatApi.isShowChatReceiver() && Number(obj.messageType) === sinclo.chatApi.messageType.company) {
              sinclo.chatApi.notify(obj.chatMessage);
            } else {
              sinclo.chatApi.scDown();
            }
            // チャットの契約をしている場合
            if ( window.sincloInfo.contract.chat ) {
              if(storage.s.get('sorryMessageFlg') !== 'true') {
                if(storage.s.get('mannedRequestFlg') !== 'true') {
                  storage.s.set('mannedRequestFlg',true);
                }
                storage.s.set('sorryMessageFlg',true);
                //sorryメッセージを出した数
                //sorryメッセージ受信数はメッセージを送信した対象のタブでカウントする
                if(typeof ga == "function" && obj.tabId === userInfo.tabId){
                  ga('send', 'event', 'sinclo', 'sorryMsg', location.href, 1);
                }
              }
            }
            sinclo.sorryMsgTimer = null;
            return false;
          },3000);
        }
        //初回通知メッセージを利用している場合
        if (obj.notification === true && obj.tabId === userInfo.tabId) {
          storage.s.set('notificationTime', obj.created);
          var data = sincloInfo.chat.settings.initial_notification_message ? JSON.parse(sincloInfo.chat.settings.initial_notification_message) : {};
          for (var i = 0; i < Object.keys(data).length; i++) {
            (function (times) {
              setTimeout(function () {
                if (storage.s.get('operatorEntered') !== 'true' && data[times].message !== "") {
                  var userName = "";
                  if (window.sincloInfo.widget.showAutomessageName === 2) {
                    userName = "";
                  } else {
                    userName = window.sincloInfo.widget.subTitle;
                  }
                  sinclo.chatApi.createMessageUnread({cn: "sinclo_re", message: data[times].message, name: userName, chatId: obj.chatId});
                  sinclo.chatApi.scDown();
                  var sendData = {
                    siteKey: obj.siteKey,
                    tabId: obj.tabId,
                    chatMessage: data[times].message,
                    messageType: sinclo.chatApi.messageType.notification,
                    messageDistinction: obj.messageDistinction,
                    chatId: obj.chatId,
                    mUserId: obj.mUserId,
                    userId: obj.userId,
                  };
                  emit("sendInitialNotificationChat", {messageList: sendData});
                }
                storage.s.set('callingMessageSeconds',data[times].seconds);
              },data[times].seconds*1000);
            })(i);
          }
        }
        if (obj.messageType == sinclo.chatApi.messageType.notification) {
          return false;
        }
        if(!obj.hideMessage && obj.messageType != sinclo.chatApi.messageType.sorry && obj.messageType != sinclo.chatApi.messageType.linkClick){
          this.chatApi.createMessageUnread({cn: cn, message: obj.chatMessage, name: userName, chatId: obj.chatId});
        }

        if(this.chatApi.isShowChatReceiver() && Number(obj.messageType) === sinclo.chatApi.messageType.company) {
          this.chatApi.notify(obj.chatMessage);
        } else {
          if(obj.tabId === userInfo.tabId && obj.messageType != sinclo.chatApi.messageType.linkClick) {
            this.chatApi.scDown();
            common.chatBotTypingCall(obj);
          }
        }
        //sinclo.trigger.fireChatEnterEvent(obj.chatMessage);
        // オートメッセージの内容をDBに保存し、オブジェクトから削除する
        if (!sinclo.chatApi.saveFlg && obj.tabId === userInfo.tabId) {
          console.log("EMIT sendAutoChat");
          emit("sendAutoChat", {messageList: sinclo.chatApi.autoMessages.getByArray()});
          sinclo.chatApi.autoMessages.unset();
          sinclo.chatApi.saveFlg = true;
        } else if (obj.tabId !== userInfo.tabId) {
          // メインのオートメッセージだけ保存してサブのオートメッセージは保存しない
          console.log("unset automessages")
          sinclo.chatApi.autoMessages.unset();
          sinclo.chatApi.saveFlg = true;
        }
      }
      else {
        alert('メッセージの送信に失敗しました。');
      }
      //通知した際に自由入力エリア表示
      if (obj.opFlg == true && obj.matchAutoSpeech == false) {
        sinclo.displayTextarea();
        storage.l.set('textareaOpend', 'open');
        storage.s.set('initialNotification', 'false');
        if(storage.s.get('mannedRequestFlg') !== 'true') {
          storage.s.set('mannedRequestFlg', true);
        }
      }
    },
    sendReqAutoChatMessages: function (d) {
      // 自動メッセージの情報を渡す（保存の為）
      var obj = common.jParse(d);
      emit("sendAutoChatMessages", {
        messages: sinclo.chatApi.autoMessages.getByArray(),
        scenarios: sinclo.scenarioApi.getStoredMessage(),
        sendTo: obj.sendTo,
        chatToken: obj.chatToken
      });
      var value = "";
      if (window.sincloInfo.widgetDisplay) {
        value = document.getElementById('sincloChatMessage').value;
      }
      // 入力中のステータスを送る
      sinclo.chatApi.observeType.emit(sinclo.chatApi.observeType.status, value);
    },
    resAutoChatMessage: function (d) {
      console.log("resAutoChatMessage : " + JSON.stringify(d));
      var obj = JSON.parse(d);
      if (!sinclo.chatApi.autoMessages.exists(obj.chatId)) {
        var sendData = {
          cn: "sinclo_re",
          message: obj.message,
          name: sincloInfo.widget.subTitle,
          chatId: obj.chatId
        };
        sinclo.chatApi.createMessage(sendData);
      }
      sinclo.chatApi.autoMessages.push(obj.chatId, obj);
    },
    resScenarioMessage: function (d) {
      console.log("resScenarioMessage");
      var obj = JSON.parse(d);
      if (obj.sincloSessionId !== userInfo.sincloSessionId && obj.tabId !== userInfo.tabId) return false;
      var elm = document.getElementById('sincloChatMessage'), cn, userName = "";

      if (obj.messageType === sinclo.chatApi.messageType.auto || obj.messageType === sinclo.chatApi.messageType.autoSpeech
        || obj.messageType === sinclo.chatApi.messageType.scenario.message.text
        || obj.messageType === sinclo.chatApi.messageType.scenario.message.hearing
        || obj.messageType === sinclo.chatApi.messageType.scenario.message.selection
        || obj.messageType === sinclo.chatApi.messageType.scenario.message.receiveFile) {
        if(obj.tabId === userInfo.tabId) {
          common.chatBotTypingCall(obj);
          this.chatApi.scDown();
          return false;
        } else {
          // 別タブで表示したシナリオメッセージは表示する
          cn = "sinclo_re";
        }
      }

      if (obj.messageType === sinclo.chatApi.messageType.scenario.message.receiveFile) {
        this.chatApi.createSendFileMessage(JSON.parse(obj.message), sincloInfo.widget.subTitle);
        this.chatApi.scDown();
        return false;
      }

      this.chatApi.createMessageUnread({cn: cn, message: obj.message, name: sincloInfo.widget.subTitle, chatId: obj.chatId});
      if (this.chatApi.isShowChatReceiver() && Number(obj.messageType) === sinclo.chatApi.messageType.company) {
        this.chatApi.notify(obj.chatMessage);
      } else {
        this.chatApi.scDown();
      }
    },
    confirmVideochatStart: function (obj) {
      // ビデオチャット開始に必要な情報をオペレータ側から受信し、セットする
      if (obj.toTabId !== userInfo.tabId) return false;
      if (userInfo.accessType !== Number(cnst.access_type.guest)) return false;
      userInfo.vc_receiverID = obj.receiverID;
      userInfo.vc_toTabId = obj.toTabId;
      common.setVcInfo({receiverID: obj.receiverID, toTabId: obj.toTabId});
    },
    docShareConnect: function (obj) {
      sessionStorage.removeItem('doc');

      // 終了通知
      var title = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareDocument.begin.headerMessage)) ? window.sincloInfo.custom.shareDocument.begin.headerMessage : location.host + 'の内容';
      var content = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareDocument.begin.content)) ? window.sincloInfo.custom.shareDocument.begin.content : location.host + 'が資料共有を求めています。<br>許可しますか';
      popup.ok = function () {
        var size = browserInfo.windowSize();
        var params = {
          data: obj,
          site: window.sincloInfo.site
        };
        var url = window.sincloInfo.site.files + "/docFrame/" + encodeURIComponent(JSON.stringify(params));

        window.open(url, "_blank", "width=" + size.width + ", height=" + size.height + ", resizable=yes,scrollbars=yes,status=no");
        emit('docShare', {
          id: obj.id,
          responderId: obj.responderId,
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
      popup.no = function () {
        emit('docShareCancel', obj);
        this.remove();
      };
      popup.set(title, content);

    },
    docDisconnect: function () {
      // 終了通知
      var title = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareDocument.end.headerMessage)) ? window.sincloInfo.custom.shareDocument.end.headerMessage : location.host + 'の内容';
      var content = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareDocument.end.content)) ? window.sincloInfo.custom.shareDocument.end.content : location.host + 'との資料共有を終了しました';
      popup.ok = function () {
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);
    },
    syncStop: function (d) {
      var obj = common.jParse(d);
      syncEvent.stop(false);
      if ((userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest))) {
        window.parent.close();
        return false;
      }
      if (!check.isset(userInfo.connectToken)) return false;

      window.clearTimeout(sinclo.syncTimeout);

      userInfo.syncInfo.unset();
      if (!document.getElementById('sincloBox')) {
        common.makeAccessIdTag();
        if (window.sincloInfo.contract.chat) {
          // チャット情報読み込み
          sinclo.chatApi.init();
        }
      }

      // 終了通知
      var title = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareBrowse.end.headerMessage)) ? window.sincloInfo.custom.shareBrowse.end.headerMessage : location.host + 'の内容';
      var content = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareBrowse.end.content)) ? window.sincloInfo.custom.shareBrowse.end.content : location.host + 'との画面共有を終了しました';
      popup.ok = function () {
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);

      var timer = setInterval(function () {
        if (window.sincloInfo.widgetDisplay === false) {
          clearInterval(timer);
          return false;
        }
        var sincloBox = document.getElementById('sincloBox');
        // チャット未契約のときはウィジェットを非表示
        if (sincloBox && (window.sincloInfo.contract.chat || window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document))) {
          common.widgetHandler.show();
          sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
          sinclo.widget.condifiton.set(false, true);
          clearInterval(timer);
        }
      }, 500);
    },
    stopCoBrowse: function (d) {
      var obj = common.jParse(d);
      if ((userInfo.gFrame && Number(userInfo.accessType) === Number(cnst.access_type.guest))) {
        window.parent.close();
        return false;
      }
      if (!check.isset(userInfo.coBrowseConnectToken)) return false;

      storage.s.unset("coBrowseConnectToken");
      userInfo.coBrowseConnectToken = "";
      if (!document.getElementById('sincloBox')) {
        common.makeAccessIdTag();
        if (window.sincloInfo.contract.chat) {
          // チャット情報読み込み
          sinclo.chatApi.init();
        }
      }

      // 終了通知
      var title = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareCoBrowse.end.headerMessage)) ? window.sincloInfo.custom.shareCoBrowse.end.headerMessage : location.host + 'の内容';
      var content = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.shareCoBrowse.end.content)) ? window.sincloInfo.custom.shareCoBrowse.end.content : location.host + 'との画面共有を終了しました';
      popup.ok = function () {
        laUtil.disconnect();
        this.remove();
      };
      popup.set(title, content, popup.const.action.alert);
      laUtil.disconnect();
      var timer = setInterval(function () {
        if (window.sincloInfo.widgetDisplay === false) {
          clearInterval(timer);
          laUtil.disconnect();
          return false;
        }
        var sincloBox = document.getElementById('sincloBox');
        // チャット未契約のときはウィジェットを非表示
        if (sincloBox && (window.sincloInfo.contract.chat || window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document))) {
          common.widgetHandler.show();
          sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
          sinclo.widget.condifiton.set(false, true);
          clearInterval(timer);
        }
      }, 500);
    },
    displayTextareaDelayTimer: null,
    hideTextareaDelayTimer: null,
    firstCallDisplayTextarea: true,
    firstCallHideTextarea: true,
    textareaTimerController: function(){
      var delayTime = 900;
      if(window.sincloInfo.widget.chatMessageWithAnimation === 1){
        delayTime = 1105;
      }
      if(sinclo.displayTextareaDelayTimer) {
        clearTimeout(sinclo.displayTextareaDelayTimer);
        sinclo.displayTextareaDelayTimer = null;
      }
      if(sinclo.hideTextareaDelayTimer) {
        clearTimeout(sinclo.hideTextareaDelayTimer);
        sinclo.hideTextareaDelayTimer = null;
      }
      return delayTime;
    },
    displayTextarea: function(){
      console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>displayTextAreaCalled<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<");
      if(!document.getElementById("flexBoxWrap")) return;
      if((check.isset(window.sincloInfo.custom)
        && check.isset(window.sincloInfo.custom.widget.forceHideMessageArea)
        && window.sincloInfo.custom.widget.forceHideMessageArea)) {
        sinclo.hideTextarea();
        return;
      }
      var delayTime = sinclo.textareaTimerController();
      sinclo.displayTextareaDelayTimer = setTimeout(function(){
      console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>displayTextAreaNow");
      $(window).off('resize', sinclo.displayTextarea).off('resize', sinclo.hideTextarea).on('resize', sinclo.displayTextarea);
      $('#flexBoxWrap').css('display', 'block');
      if(!check.smartphone()){
        common.widgetHandler._handleResizeEvent();
        if(sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi.isScenarioLFDisabled()) {
          $('#miniSincloChatMessage').focus();
        } else {
          $('#sincloChatMessage').focus();
        }
        $('#sincloWidgetBox').offset({top: $('#sincloBox').offset().top});
      }
      //スマホの場合
      if ( check.smartphone() ) {
        sinclo.adjustSpWidgetSize();
      }
      $('#sincloBox #chatTalk').scrollTop(chatTalk.scrollHeight - chatTalk.clientHeight - 2);
      },delayTime);
      if(sinclo.firstCallDisplayTextarea) {
        if ( check.smartphone() ){
          $('#flexBoxWrap').css('display', 'block');
          sinclo.adjustSpWidgetSize();
          $('#sincloBox #chatTalk').scrollTop(chatTalk.scrollHeight - chatTalk.clientHeight - 2);
        }
      }
      sinclo.firstCallDisplayTextarea = false;
    },
    hideTextarea: function(){
      console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>hideTextareaCalled<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<");
      if(!document.getElementById("flexBoxWrap") ) return;
      var delayTime = sinclo.textareaTimerController();
      sinclo.hideTextareaDelayTimer = setTimeout(function(){
        console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>hideTextareaNow");
        $(window).off('resize', sinclo.displayTextarea).off('resize', sinclo.hideTextarea).on('resize', sinclo.hideTextarea);
        if(!check.smartphone() && $('#sincloWidgetBox').is(':visible') && $('#flexBoxWrap').is(':visible')) {
          var isMiniDisplayShow = $('#miniFlexBoxHeight').is(':visible');
          $('#flexBoxWrap').css('display', 'none');
          if(sinclo.scenarioApi.isProcessing() && isMiniDisplayShow) {
            document.getElementById("chatTalk").style.height = chatTalk.clientHeight + 48 + 'px';
          } else {
            document.getElementById("chatTalk").style.height = chatTalk.clientHeight + 75 + 'px';
          }
        }
        //スマホの場合
        if ( check.smartphone() ) {
          $('#flexBoxWrap').css('display', 'none');
          sinclo.adjustSpWidgetSize();
        }
      },delayTime);
      if(sinclo.firstCallHideTextarea) {
        if ( check.smartphone() ) {
          $('#flexBoxWrap').css('display', 'none');
          sinclo.adjustSpWidgetSize();
        }
      }
      sinclo.firstCallHideTextarea = false;
    },
    calcSpWidgetHeight: function(){
      var widgetHeaderHeight = $('#sincloBox #widgetDescription').outerHeight() + $('#sincloBox #widgetSubTitle').outerHeight() + $('#sincloBox #widgetTitle').outerHeight(),
          widgetBodyHeight = 0,
          widgetFotterHeight = $('#sincloBox #fotter').outerHeight();
      if(window.sincloInfo.widget.spHeaderLightFlg === 1){
          widgetHeaderHeight = $('#sincloBox #widgetTitle').outerHeight();
        }
        //自由入力欄があるならば、その高さを取得してチャットウィジェット全体の高さを計算する
        if ($('#flexBoxWrap').is(':visible')) {
          widgetBodyHeight = window.innerHeight - (widgetHeaderHeight + $('#flexBoxWrap').outerHeight() + widgetFotterHeight);
        }else{
          widgetBodyHeight = window.innerHeight - (widgetHeaderHeight + widgetFotterHeight);
        }
        console.log("チャットエリアの高さは" + widgetBodyHeight + "px");
      return widgetBodyHeight;
    },
    adjustSpWidgetSize: function() {
      if ( check.smartphone() ) {
        if ($('#flexBoxWrap').is(':visible')) {
          console.log("<><><><>adjustSpWidgetSizeのdisplaytextareaが作動<><><><>");
          // 縦の場合
          var widgetWidth = 0,
              ratio = 0;
          if (common.isPortrait() && $(window).height() > $(window).width()) {
            widgetWidth = $(window).width();
            ratio = widgetWidth * (1 / 285);
            if (window.sincloInfo.widget.spMaximizeSizeType === 2) {
              var fullHeight = sinclo.calcSpWidgetHeight();
              $("#chatTalk").outerHeight(fullHeight);
              $('#sincloBox ul sinclo-typing').css('padding-bottom', (fullHeight * 0.1604) + 'px');
            //余白ありの場合
            } else {
              widgetWidth = $(window).width() - 20;
              ratio = widgetWidth * (1 / 285);
              var chatTalkHeight = (194 * ratio) + (60 * ratio);
              if($('#flexBoxWrap').is(':visible')){
                chatTalkHeight -= $('#flexBoxWrap').outerHeight();
              }
              document.getElementById("chatTalk").style.height = chatTalkHeight + 'px';
              $('#sincloBox ul sinclo-typing').css('padding-bottom', ((194 * ratio) * 0.1604) + 'px');
            }
          }
          //横の場合
          else {
            if (!check.android()) {
              var chatAreaHeight = window.innerHeight * (document.body.clientWidth / window.innerWidth);
              var hRatio = chatAreaHeight * 0.07;
              document.getElementById("chatTalk").style.height = (chatAreaHeight - (6.5 * hRatio)) + 'px';
            }
          }
        } else {
          if (check.smartphone()) {
            console.log("<><><><>adjustSpWidgetSizeのhidetextareaが作動<><><><>");
            // 縦の場合
            var widgetWidth = 0,
                ratio = 0;
            $('#flexBoxWrap').css('display', 'none');
            if (common.isPortrait() && $(window).height() > $(window).width()) {
              console.log("ratio : " + ratio);

              if (window.sincloInfo.widget.spMaximizeSizeType === 2) {
                var fullHeight = sinclo.calcSpWidgetHeight();
                $("#chatTalk").outerHeight(fullHeight);
              //余白ありの場合
              } else {
                widgetWidth = $(window).width() - 20;
                ratio = widgetWidth * (1 / 285);
                document.getElementById("chatTalk").style.height = (194 * ratio) + (60 * ratio) + 'px';
              }
            }
            //横の場合
            else {
              $('#flexBoxWrap').css('display', 'none');
              var chatAreaHeight = window.innerHeight * (document.body.clientWidth / window.innerWidth);
              var hRatio = chatAreaHeight * 0.07;
              document.getElementById("chatTalk").style.height = (chatAreaHeight - (6.5 * hRatio)) + (hRatio * 4) + 'px';
            }
          }
        }
      }
    },
    syncApi: {
      init: function (type) {
        if (type === cnst.sync_type.outer) {
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
            send: function () {
            },
            receive: function () {
            }
          },
          mouseSync: {
            send: function () {
            },
            receive: function () {
            }
          },
          scrollSync: {
            send: function () {
            },
            receive: function () {
            }
          },
          resizeSync: {
            send: function () {
            },
            receive: function () {
            }
          },
          pageSync: {
            send: function () {
            },
            receive: function () {
            }
          },
        },
        outer: {
          formSync: {
            send: function () {
            },
            receive: function () {
            }
          },
          mouseSync: {
            send: function () {
            },
            receive: function () {
            }
          },

        }
      }
    },
    chatApi: {
        saveFlg: false,
        online: false, // 現在の対応状況
        historyId: null,
        stayLogsId: null,
        unread: 0,
        opUser: "",
        opUserName: "",
        messageType: {
          customer: 1,
          company: 2,
          auto: 3,
          sorry: 4,
          autoSpeech: 5,
          sendFile: 6,
          notification: 7,
          linkClick: 8,
          start: 998,
          end: 999,
          scenario: {
            customer: {
              hearing: 12,
              selection: 13,
              sendFile: 19,
              answerBulkHearing: 30,
              noModBulkHearing: 31,
              modifyBulkHearing: 32
            },
            message: {
              text: 21,
              hearing: 22,
              selection: 23,
              receiveFile: 27,
              returnBulkHearing: 40,
              pulldown: 41,
              calendar: 42
            }
          },
          cogmo: {
            message: 81,
            feedback: 82
          }
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
                if(allData || !array[id].applied) {
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
            var list = this.get(true);
            Object.keys(list).forEach(function(id, index, arr) {
              list[id]['applied'] = true;
            });
            storage.s.set('amsg', JSON.stringify(list));
          },
          delete: function(id) {
            var list = this.get(true);
            delete list[id];
            storage.s.set('amsg', JSON.stringify(list));
          }
        },
        init: function(){
          this.initEvent();

        var textareaOpend = storage.l.get('textareaOpend');
        //チャットのテキストエリア表示
        if (textareaOpend == 'close') {
          sinclo.hideTextarea();
        }
        //チャットのテキストエリア非表示
        else {
          sinclo.displayTextarea();
        }

          emit('getChatMessage', {showName: sincloInfo.widget.showName});
          common.reloadWidgetRemove();
        },
        initEvent: function(){
          if ( window.sincloInfo.contract.chat ) {
            if ( !( 'chatTrigger' in window.sincloInfo.widget && window.sincloInfo.widget.chatTrigger === 2) ) {
              // チャットメッセージ入力欄でのキーイベント系はすべてバブリングしない
              $("#sincloChatMessage").on("keyup keypress",function(e){
                if(e) e.stopImmediatePropagation();
              });
              sinclo.chatApi.addKeyDownEventToSendChat();
              // キーイベント系はすべてバブリングしない
              $(document).on("keydown keyup keypress", "#sincloChatMessage,#miniSincloChatMessage", function(e){
                if(e) e.stopImmediatePropagation();
              });
            }
            $(document).on("focus", "#sincloChatMessage,#miniSincloChatMessage", function(e){
              if(e) e.stopPropagation();
              sinclo.chatApi.observeType.start();
              console.log('エラー');
            });
          }

          if ( window.sincloInfo.contract.chatbotScenario ) {
            sinclo.scenarioApi.addStorageUpdateEvent();
          }

        this.sound = document.getElementById('sinclo-sound');
        if (this.sound) {
          this.sound.volume = 0.3;
        }

        // 複数回イベントが登録されるケースがあるためいったんOFFにする
        $(document).off('click', "input[name^='sinclo-radio']");
          $(document).on('change', "[name^='sinclo-pulldown']", function (e) {
            if(e) e.stopPropagation();
            console.log("sinclo.scenarioApi.isProcessing() : " + sinclo.scenarioApi.isProcessing() + " sinclo.scenarioApi.isWaitingInput() : " + sinclo.scenarioApi.isWaitingInput());
            sinclo.chatApi.send(e.target.value.trim());
          });

          $(document)
            .on('focus', "#sincloChatMessage,#miniSincloChatMessage",function(e){
              if(e) e.stopPropagation();
              sinclo.chatApi.clearPlaceholderMessage();
              console.log('サバ');
              if(check.smartphone()) {
                $(document).one('touchstart', function(e){
                  $(document).trigger('blur');
                });
              }
            })
            .on('blur', "#sincloChatMessage,#miniSincloChatMessage",function(e){
              if(e) e.stopPropagation();
              sinclo.chatApi.setPlaceholderMessage(sinclo.chatApi.getPlaceholderMessage());
            })
            .on("click", "input[name^='sinclo-radio']", function(e){
              if(e) e.stopPropagation();
              console.log("sinclo.scenarioApi.isProcessing() : " + sinclo.scenarioApi.isProcessing() + " sinclo.scenarioApi.isWaitingInput() : " + sinclo.scenarioApi.isWaitingInput());
              if(sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi.isWaitingInput() && (!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false")) {
                var name = $(this).attr('name');
                console.log("DISABLE RADIO NAME : " + name);
                storage.l.set('sinclo_disable_radio',name);
                $('input[name=' + name + '][type="radio"]').prop('disabled', true).parent().css('opacity', 0.5);
              }
              if ( !(window.sincloInfo.widget.hasOwnProperty('chatRadioBehavior') && window.sincloInfo.widget.chatRadioBehavior === 2) ) {
                sinclo.chatApi.send(e.target.value.trim());
              } else {
                var textareaOpend = storage.l.get('textareaOpend');
                //チャットのテキストエリアが閉まっているときは即時送信
                if( textareaOpend == 'close') {
                  sinclo.chatApi.send(e.target.value.trim());
                }
                else {
                  var message = document.getElementById('sincloChatMessage');
                  if ( check.isset(message.value) ) {
                    message.value += "\n";
                  }
                  message.value += e.target.value.trim();
                }
              }
            });
            $("input[name^='sinclo-radio']").each(function(index){
              if(!sinclo.scenarioApi.isProcessing() && $(this).parents('.sinclo-scenario-msg').length !== 0) {
                var selected = false;
                $(this).parents("li.sinclo_re").find('.sinclo-chat-radio').each(function(index){
                  if($(this).is(':checked')) {
                    selected = true;
                  }
                });
                if(selected) {
                  $(this).prop('disabled', true).parent().css('opacity', 0.5);
                } else {
                  $(this).prop('disabled', false);
                }
              } else {
                $(this).prop('disabled', false);
              }
            });
        },
        removeAllEvent: function() {
          if ( window.sincloInfo.contract.chat ) {
            if ( !( 'chatTrigger' in window.sincloInfo.widget && window.sincloInfo.widget.chatTrigger === 2) ) {
              // チャットメッセージ入力欄でのキーイベント系はすべてバブリングしない
              $("#sincloChatMessage,#miniSincloChatMessage").off("keyup keypress");
              $('#sincloChatMessage,#miniSincloChatMessage').off("keydown");
              // キーイベント系はすべてバブリングしない
              $(document).off("keydown keyup keypress", "#sincloChatMessage,#miniSincloChatMessage");
            }
            $(document).off("focus", "#sincloChatMessage,#miniSincloChatMessage");
          }

        this.sound = document.getElementById('sinclo-sound');
        if (this.sound) {
          this.sound.volume = 0.3;
        }

          // 複数回イベントが登録されるケースがあるためいったんOFFにする
          $(document).off('click', "input[name^='sinclo-radio']");
          $(document)
            .off('focus', "#sincloChatMessage,#miniSincloChatMessage")
            .off('blur', "#sincloChatMessage,#miniSincloChatMessage")
            .off("click", "input[name^='sinclo-radio']");
          $("input[name^='sinclo-radio']").prop('disabled', true);
        },
        showMiniMessageArea: function() {
          console.log(">>>>>>>>>>>>>>>>>>>>>showMiniMessageArea");
          if((check.isset(window.sincloInfo.custom)
            && check.isset(window.sincloInfo.custom.widget.forceHideMessageArea)
            && window.sincloInfo.custom.widget.forceHideMessageArea)) {
            return;
          }
          // オペレータ未入室のシナリオのヒアリングモードのみ有効
          if((!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false") && sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi._hearing.isHearingMode()) {
            $('#flexBoxHeight').addClass('sinclo-hide');
            $('#miniFlexBoxHeight').removeClass('sinclo-hide');
            $('#miniSincloChatMessage').attr('type', sinclo.scenarioApi.getInputType());
            if(!check.smartphone()) {
              common.widgetHandler._handleResizeEvent();
              var chatTalk = document.getElementById('chatTalk');
              $('#miniSincloChatMessage').focus();
              $('#sincloWidgetBox').offset({top: $('#sincloBox').offset().top});
            } else {
              sinclo.adjustSpWidgetSize();
            }
          }
        },
        hideMiniMessageArea: function() {
        console.log(">>>>>>>>>>>>>>>>>>>>>hideMiniMessageArea");
        if ((check.isset(window.sincloInfo.custom)
          && check.isset(window.sincloInfo.custom.widget.forceHideMessageArea)
          && window.sincloInfo.custom.widget.forceHideMessageArea)) {
          return;
        }
        // シナリオのヒアリングモードのみ有効
        if (sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi._hearing.isHearingMode()) {
          $('#flexBoxHeight').removeClass('sinclo-hide');
          $('#miniFlexBoxHeight').addClass('sinclo-hide');
          $('#miniSincloChatMessage').attr('type', 'text'); // とりあえずデフォルトに戻す
          if (!check.smartphone()) {
            common.widgetHandler._handleResizeEvent();
            var chatTalk = document.getElementById('chatTalk');
            $('#sincloChatMessage').focus();
            $('#sincloWidgetBox').offset({top: $('#sincloBox').offset().top});
          } else {
            sinclo.adjustSpWidgetSize();
          }
        }
      },
      addKeyDownEventToSendChat: function() {
        // 重複登録防止
        var isScenarioLFDisabled = sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi.isWaitingInput()
          && sinclo.scenarioApi._hearing.isHearingMode() && sinclo.scenarioApi._hearing.isLFModeDisabled();
        $('#sincloChatMessage,#miniSincloChatMessage').off("keydown", sinclo.chatApi.handleKeyDown);
        $('#sincloChatMessage,#miniSincloChatMessage').on("keydown", sinclo.chatApi.handleKeyDown);
      },
      removeKeyDownEventToSendChat: function() {
        // 重複登録防止
        if (!('chatTrigger' in window.sincloInfo.widget) || !(window.sincloInfo.widget.chatTrigger === 2)) {
          $('#sincloChatMessage,#miniSincloChatMessage').off("keydown", sinclo.chatApi.handleKeyDown);
        }
      },
      handleKeyDown: function(e) {
        var isScenarioHearingMode = sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi.isWaitingInput()
          && sinclo.scenarioApi._hearing.isHearingMode(),
          isScenarioLFDisabled = isScenarioHearingMode && sinclo.scenarioApi._hearing.isLFModeDisabled(),
          isScenarioLFEnabled = sinclo.scenarioApi._bulkHearing.isInMode() || isScenarioHearingMode && !sinclo.scenarioApi._hearing.isLFModeDisabled();
        if (isScenarioLFDisabled) {
          if (e) e.stopImmediatePropagation();
          if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
            sinclo.chatApi.push();
          }
        } else if (isScenarioLFEnabled) {
          if (e) e.stopImmediatePropagation();
          if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
            if (e.shiftKey) {
              sinclo.chatApi.push();
            }
          }
        } else {
          if (e) e.stopImmediatePropagation();
          if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
            if (!e.shiftKey && !e.ctrlKey) {
              sinclo.chatApi.push();
            }
          }
        }
      },
      getPlaceholderMessage: function() {
        var msg = "メッセージを入力してください";
        if (sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi.getPlaceholderMessage() !== "") {
          msg = sinclo.scenarioApi.getPlaceholderMessage();
        } else if (!('chatTrigger' in window.sincloInfo.widget && window.sincloInfo.widget.chatTrigger === 2)) {
          if (check.smartphone()) {
            msg += "（改行で送信）";
          }
          else {
            msg += "\n（Shift+Enterで改行/Enterで送信）";
          }
        }
        return msg;
      },
      setPlaceholderMessage: function(msg) {
        var message = document.getElementById('sincloChatMessage');
        if (message) {
          message.placeholder = msg;
        }
        var miniMessage = document.getElementById('miniSincloChatMessage');
        if (miniMessage) {
          miniMessage.placeholder = msg;
        }
      },
      clearPlaceholderMessage: function() {
        var message = document.getElementById('sincloChatMessage');
        if (message) {
          message.placeholder = "";
        }
        var miniMessage = document.getElementById('miniSincloChatMessage');
        if (miniMessage) {
          miniMessage.placeholder = "";
        }
      },
      targetTextarea: null,
      lockPageScroll: function() {
        if (!check.smartphone()) return false;
        if (!sinclo.chatApi.targetTextarea) {
          sinclo.chatApi.targetTextarea = document.getElementById('chatTalk');
        }
        window.addEventListener('touchmove', sinclo.chatApi.lockPageScrollHandler);
        sinclo.chatApi.targetTextarea.addEventListener('scroll', sinclo.chatApi.unlockPageScrollHandler);
      },
      unlockPageScroll: function() {
        if (!check.smartphone()) return false;
        if (!sinclo.chatApi.targetTextarea) {
          sinclo.chatApi.targetTextarea = document.getElementById('chatTalk');
        }
        window.removeEventListener('touchmove', sinclo.chatApi.lockPageScrollHandler);
        sinclo.chatApi.targetTextarea.removeEventListener('scroll', sinclo.chatApi.unlockPageScrollHandler);
      },
      lockPageScrollHandler: function(event) {
        if ($(event.target).parents("#chatTalk").get(0) === sinclo.chatApi.targetTextarea
          && sinclo.chatApi.targetTextarea.scrollTop !== 0
          && sinclo.chatApi.targetTextarea.scrollTop + sinclo.chatApi.targetTextarea.clientHeight < sinclo.chatApi.targetTextarea.scrollHeight) {
          console.log("a");
          event.stopPropagation();
        }
        else {
          console.log("b");
          event.preventDefault();
        }
      },
      unlockPageScrollHandler: function(event) {
        if (sinclo.chatApi.targetTextarea.scrollTop === 0) {
          sinclo.chatApi.targetTextarea.scrollTop = 1;
        }
        else if (sinclo.chatApi.targetTextarea.scrollTop + sinclo.chatApi.targetTextarea.clientHeight === sinclo.chatApi.targetTextarea.scrollHeight) {
          sinclo.chatApi.targetTextarea.scrollTop = sinclo.chatApi.targetTextarea.scrollTop - 1;
        }
      },
      widgetOpen: function() {
        console.log("chatApi.widgetOpen start");
        this.beforeWidgetOpen();
        var widgetOpen = storage.s.get('widgetOpen');
        if (!(('showTime' in window.sincloInfo.widget)
          && ('maxShowTime' in window.sincloInfo.widget)
          && String(window.sincloInfo.widget.maxShowTime).match(/^[0-9]{1,2}$/) !== null)) return false;
        var showTime = String(window.sincloInfo.widget.showTime);
        var displayStyleType = String(window.sincloInfo.widget.displayStyleType);
        var maxShowTime = Number(window.sincloInfo.widget.maxShowTime) * 1000;
        if (check.smartphone()
          && Number(sincloInfo.widget.spAutoOpenFlg) === 1
          && Number(sincloInfo.widget.spWidgetViewPattern) === 3) {
          if (!storage.l.get('bannerAct')) {
            console.log("spWidgetViewPattern 3 show banner");
            //バナー表示にする
            sinclo.operatorInfo.onBanner();
          }
          return;
        }
        switch (displayStyleType) {
          case "1": // 最大化
            if (!widgetOpen) {
              var flg = sinclo.widget.condifiton.get();
              if (String(flg) === "false") {
                console.log("SHOW WIDGET MAXIMIZE");
                storage.l.set('widgetOpen', true);
                storage.s.set('widgetOpen', true);
                if (!common.widgetHandler.isShown()) {
                  storage.s.set('preWidgetOpened', true);
                }
                if (!(check.smartphone() && sincloInfo.widget.hasOwnProperty('spAutoOpenFlg') && Number(sincloInfo.widget.spAutoOpenFlg) === 1)) {
                  sinclo.operatorInfo.ev();
                }
              }
            }
            break;
          case "2": // 最小化
            if (check.smartphone()
              && Number(sincloInfo.widget.spWidgetViewPattern) === 3) {
              if (!storage.l.get('bannerAct')) {
                console.log("widget minimize and spWidgetViewPattern 3 show banner");
                //バナー表示にする
                sinclo.operatorInfo.onBanner();
              }
            }
            break;
          case "3": // バナー表示
            if (!storage.l.get('bannerAct')) {
              console.log("SHOW INITIAL BANNER MODE");
              //バナー表示にする
              sinclo.operatorInfo.onBanner();
            }
            break;
        }

        if (showTime === "5") return false; // 初期表示のままにする
        if (showTime === "1") { // サイト訪問後
          if (widgetOpen) return false;
        }
        // ページ訪問時（showTime === 4）
        window.setTimeout(function () {
          console.log("ウィジェット最大化条件発動");
          var flg = sinclo.widget.condifiton.get();
          if (String(flg) === "false") {
            storage.l.set('widgetOpen', true);
            storage.s.set('widgetOpen', true);
            if (!common.widgetHandler.isShown()) {
              storage.s.set('preWidgetOpened', true);
            }
            if (storage.l.get('bannerAct') === 'true') {
              //バナー表示だった場合最小化状態で表示
              sinclo.operatorInfo.clickBanner(true);
            }
            if (!(check.smartphone() && sincloInfo.widget.hasOwnProperty('spAutoOpenFlg') && Number(sincloInfo.widget.spAutoOpenFlg) === 1)) {
              sinclo.operatorInfo.ev();
            }
          }
        }, maxShowTime);
      },
      beforeWidgetOpen: function () {
        // ウィジェット表示タイミングが「ページ訪問時」の場合はタイマーをセットする
        if (window.sincloInfo.widget.showTiming === 1 || window.sincloInfo.widget.showTiming === 2) {
          console.log("訪問後表示処理実行");
          window.setTimeout(function () {
            console.log("訪問後表示");
            window.sincloInfo.widgetDisplay = true;
            common.widgetHandler.show();

            //自由入力エリアが閉まっているか空いているかチェック
            var textareaOpend = storage.l.get('textareaOpend');
            //チャットのテキストエリア表示
            if (textareaOpend == 'close') {
              sinclo.hideTextarea();
            }
            //チャットのテキストエリア非表示
            else {
              sinclo.displayTextarea();
            }
          }, common.widgetHandler.getRemainingTimeMsec());
        }
      },
      createNotifyMessage: function (val) {
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
      createTypingMessage: function (d) {
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
      createAnchorTag: {
        _regList: {
          imgTagReg: RegExp(/<img ([\s\S]*?)>/),
          linkReg: RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\&\;\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/),
          linkTabReg: RegExp(/<a ([\s\S]*?)>([\s\S]*?)<\/a>/),
          linkButtonTabReg: RegExp(/<a ([\s\S]*?)style=([\s\S]*?)>([\s\S]*?)<\/a>/),
          mailLinkReg: RegExp(/(mailto:[\w\-\.\/\?\=\&\;\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/),
          telLinkReg: RegExp(/(tel:[0-9]{9,})/),
          telnoTagReg: RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/),
          urlTagReg: RegExp(/href="([\s\S]*?)"([\s\S]*?)/)
        },
        _linkWithoutText: function(option,link,unEscapeStr,str,className){
          var url = link[0];
          var img = unEscapeStr.match(this._regList.imgTagReg);
          if(img == null) {
            var a = '<a href="' + url + '" target="_blank">' + url + '</a>';
            var linkTab = a.match(this._regList.linkTabReg);
            var processedLink = linkTab[1].replace(/ /g, "\$nbsp;");
            a = a.replace(linkTab[1],linkTab[1]+" onclick=link('"+linkTab[2]+"','"+processedLink+"','"+option+"')");
            str = str.replace(url, a);
          }
          else {
            var a = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div>";
            str = a;
          }
          return str;
        },
        _linkText: function(option,link,linkTab,unEscapeStr,str,className){
          if(link !== null) {
            var a = linkTab[0];
            console.log(linkTab[0]);
            console.log(option);
            //imgタグ有効化
            var img = unEscapeStr.match(this._regList.imgTagReg);
            if(img == null) {
              //ボタンのCSSを外す
              var linkButtonTab = unEscapeStr.match(this._regList.linkButtonTabReg);
              if(linkButtonTab !== null) {
                var processedLink = linkButtonTab[1].replace(/ /g, "\$nbsp;");
              }
              else {
                var processedLink = linkTab[1].replace(/ /g, "\$nbsp;");
              }
              if(option === "clickTelno"){
                //電話番号の場合はスマホチェックをし、生の電話番号を取得
                var telno = link[0].replace(/[^0-9^\.]/g,"");
                if(check.smartphone()){
                  a = a.replace(linkTab[1],linkTab[1]+"class='sincloTelConversion' onclick=link('"+linkTab[2]+"','"+processedLink+"','"+option+"');sinclo.api.callTelCV('" + telno + "')");
                } else {
                  a = "<span class='link'>"+ linkTab[2] + "</span>";
                }
              }
              else {
                a = a.replace(linkTab[1],linkTab[1]+" onclick=link('"+linkTab[2]+"','"+processedLink+"','"+option+"')");
              }
            }
            else {
              var processedLink = linkTab[1].replace(img[0], "");
              processedLink = processedLink.replace(/ /g, "\$nbsp;");
              imgTag = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div>";
              a = a.replace(img[0], imgTag);
              var url = a.match(this._regList.urlTagReg);
              if(option === "clickTelno"){
                console.log('画像じゃい');
                //電話番号の場合は、生の電話番号を取得
                var telno = link[0].replace(/[^0-9^\.]/g,"");
                if(check.smartphone()){
                  a = a.replace(linkTab[1],linkTab[1]+"class='sincloTelConversion' onclick=link('"+url[1]+"','"+processedLink+"','"+option+"');sinclo.api.callTelCV('" + telno + "')");
                } else {
                  console.log(a);
                  a = a.replace(a,imgTag);
                }
              }
              else {
                a = a.replace(linkTab[1],linkTab[1]+" onclick=link('"+url[1]+"','"+processedLink+"','"+option+"')");
              }
            }
          }
          else {
            // ただの文字列にする
            var a = "<span class='link'>"+ linkTab[2] + "</span>";
          }
        str = unEscapeStr.replace(linkTab[0], a);
        return str;
        }
      },
      /**
       * <code>
       *   obj = {
       *     cn: <class name>,
       *     message: <chat message>,
       *     name: <send use name>,
       *     chatId: <stored message id>
       *   }
       * </code>
       * @param obj
       * @param isScenarioMsg
       */
      createMessage: function (obj, isScenarioMsg) {
        common.chatBotTypingTimerClear();
        common.chatBotTypingRemove();
        var chatList = document.getElementsByTagName('sinclo-chat')[0];
        var div = document.createElement('div');
        var li = document.createElement('li');
        li.dataset.chatId = obj.chatId;
        if (isScenarioMsg) {
          div.classList.add('sinclo-scenario-msg');
        }
        div.appendChild(li);
        chatList.appendChild(div);
        var strings = obj.message.split('\n');
        var radioCnt = 1;
        var radioName = "sinclo-radio" + chatList.children.length;
        var content = "";
        var className;

        if (sincloInfo.widget.widgetSizeType === 1 || check.smartphone()) {
          className = 'smallSizeImg';
        }
        else if (sincloInfo.widget.widgetSizeType === 2) {
          className = 'middleSizeImg';
        }
        else if (sincloInfo.widget.widgetSizeType === 3) {
          className = 'largeSizeImg';
        }

        if (check.isset(obj.name) === false) {
          obj.name = "";
        }
        check.escape_html(obj.name); // エスケープ

        if (obj.cn.indexOf("sinclo_re") !== -1) {
          div.style.textAlign = "left";
          if (obj.name !== "") {
            content = "<span class='cName'>" + obj.name + "</span>";
          }
        } else if (obj.cn.indexOf("sinclo_se") !== -1) {
          div.style.textAlign = "right";
        }
        for (var i = 0; strings.length > i; i++) {
          var str = check.escape_html(strings[i]);
          var unEscapeStr = str
            .replace(/(&lt;)/g, '<')
            .replace(/(&gt;)/g, '>')
            .replace(/(&quot;)/g, '"')
            .replace(/(&#39;)/g, "'")
            .replace(/(&amp;)/g, '&');

                if ( obj.cn.indexOf("sinclo_re") !== -1 ) {
                  // ラジオボタン
                  var radio = str.indexOf('[]');
                  if ( radio > -1 ) {
                      var name = str.slice(radio+2).trim();
                      str = "<sinclo-radio><input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + name + "'>";
                      str += "<label for='" + radioName + "-" + i + "'>" + name + "</label></sinclo-radio>";
                  }

                  // ボタン（CogmoAttend）

                }
                // リンク
                var link = str.match(this.createAnchorTag._regList.linkReg);
                var linkTab = unEscapeStr.match(this.createAnchorTag._regList.linkTabReg);
                var option = "clickLink";
                if ( linkTab !== null) {
                  //aタグが設定されているリンクの場合
                  if(link !== null){
                  //普通のページリンクの場合は初期値
                  }
                  if(str.match(this.createAnchorTag._regList.mailLinkReg) !== null) {
                  //メールリンクの場合
                    option = "clickMail";
                    link = str.match(this.createAnchorTag._regList.mailLinkReg);
                  }
                  if(str.match(this.createAnchorTag._regList.telLinkReg) !== null){
                  //電話リンクの場合
                    option = "clickTelno";
                    link = str.match(this.createAnchorTag._regList.telLinkReg);
                  }
                  str = sinclo.chatApi.createAnchorTag._linkText(option,link,linkTab,unEscapeStr,str,className);
                }
                else {
                  /*aタグが設定されていないリンクの場合
                   *この場合はURLのみしかない為、以下の条件式
                   */
                  if(link !== null){
                    str = sinclo.chatApi.createAnchorTag._linkWithoutText(option,link,unEscapeStr,str,className);
                  }
                }
                // 電話番号（スマホのみリンク化）
                var tel = str.match(this.createAnchorTag._regList.telnoTagReg);
                if( tel !== null ) {
                  var telno = tel[1];
                  if(check.smartphone()) {
                    // リンクとして有効化
                    // GA連携時に必要な情報を作成
                    var exceedLink = 'href="tel:' + telno + '"';
                    console.log(exceedLink);
                    var a = "<a class=\"sincloTelConversion\" onclick=sinclo.api.callTelCV('" + telno + "');link('" + telno + "','" + exceedLink+ "','clickTelno') href='tel:" + telno + "'>" + telno + "</a>";
                    str = str.replace(tel[0], a);
                  } else {
                    // ただの文字列にする
                    var span = "<span class='telno'>" + telno + "</span>";
                    str = str.replace(tel[0], span);
                  }
                }
                if ( obj.cn.indexOf("sinclo_re") !== 1 ) {
                  //imgタグ
                  var imgTagReg = RegExp(/<img ([\s\S]*?)>/);
                  var img = unEscapeStr.match(imgTagReg);
                  if(img !== null && link == null && linkTab == null) {
                    imgTag = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div>";
                    str = unEscapeStr.replace(img[0], imgTag);
                  }
                }
                if(str.match(/<(".*?"|'.*?'|[^'"])*?>/)) {
                  content += "" + str + "\n";
                } else {
                  content += "<span class='sinclo-text-line'>" + str.replace(/^[\n|\r\n|\r]$/g, "") + "</span>\n";
                }
            }

        if (obj.cn.indexOf("sinclo_re") !== -1) {
          obj.cn += ' effect_left';
        } else if (obj.cn.indexOf("sinclo_se") !== -1) {
          obj.cn += ' effect_right';
        }

        li.className = obj.cn;
        li.innerHTML = content;
      },
      createMessageHtml: function (message) {
        var content = "";
        var strings = message.split('\n');
        for (var i = 0; strings.length > i; i++) {
          var str = check.escape_html(strings[i]);
          str = str
            .replace(/(&lt;)/g, '<')
            .replace(/(&gt;)/g, '>')
            .replace(/(&quot;)/g, '"')
            .replace(/(&#39;)/g, "'")
            .replace(/(&amp;)/g, '&');

          if(str.match(/<(".*?"|'.*?'|[^'"])*?>/)) {
            content += "" + str + "\n";
          } else {
            content += "<span class='sinclo-text-line'>" + str.replace(/^[\n|\r\n|\r]$/g, "") + "</span>\n";
          }
        }

        return content;
      },
      addPulldown: function(cs, message, name, settings){
        common.chatBotTypingTimerClear();
        common.chatBotTypingRemove();
        var chatList = document.getElementsByTagName('sinclo-chat')[0];
        var div = document.createElement('div');
        var li = document.createElement('li');
        div.classList.add('sinclo-scenario-msg');
        div.appendChild(li);
        chatList.appendChild(div);

        var messageHtml = sinclo.chatApi.createMessageHtml(message);
        var pulldownHtml = sinclo.chatApi.createPullDownHtml(settings, chatList.children.length);
        div.style.textAlign = "left";
        cs += ' effect_left';

        li.className = cs;
        li.innerHTML = messageHtml + pulldownHtml;
      },
      addCalendar: function (cs, message, settings) {
        common.chatBotTypingTimerClear();
        common.chatBotTypingRemove();
        var chatList = document.getElementsByTagName('sinclo-chat')[0];
        var div = document.createElement('div');
        var li = document.createElement('li');
        div.classList.add('sinclo-scenario-msg');
        div.appendChild(li);
        chatList.appendChild(div);

        var messageHtml = sinclo.chatApi.createMessageHtml(message);
        var calendarHtml = sinclo.chatApi.createCalendarHtml(settings, chatList.children.length);
        // var pulldownHtml = sinclo.chatApi.createPullDownHtml(settings, chatList.children.length);
        div.style.textAlign = "left";
        cs += ' effect_left';

        li.className = cs;
        li.innerHTML = messageHtml + calendarHtml;
        $('#sinclo-datepicker' + chatList.children.length).flatpickr({inline: 'true'});
        // $('#sinclo-datapicker' + chatList.children.length).hide();
      },
      createCalendarHtml: function(settings, index) {
        var html = "";
        html += '<div style="margin-top: 10px" name="sinclo-calendar' + index + '">';
        html += '<input type="text" id="sinclo-datepicker' + index + '">';
        html += '</div>';

        return html;
      },
      createPullDownHtml: function (settings, index) {
        var style = sinclo.chatApi.createPulldownStyle(settings);
        var name = 'sinclo-pulldown' + index;
        var html = "";
        html += '<select name="' + name + '" id="' + name + '" style="' + style + '">';
        html += '<option value="">選択してください</option>';
        settings.options.forEach(function (option, index) {
          html += '<option value="' + option + '">' + option + '</option>';
        });
        html += '</select>';

        return html;
      },
      createPulldownStyle: function (settings) {
        var style = '';
        style += 'margin-top: 10px; height: 30px; ';
        style += 'color: ' + settings.customDesign.textColor + ';';
        style += 'background-color: ' + settings.customDesign.backgroundColor + ';';
        style += 'border: 1px solid ' + settings.customDesign.borderColor + ';';

        return style;
      },
      createCogmoAttendBotMessage: function (cs, val, cName, isFeedbackMsg) {
        common.chatBotTypingRemove();
        var chatList = document.getElementsByTagName('sinclo-chat')[0];
        var div = document.createElement('div');
        var li = document.createElement('li');
        div.appendChild(li);
        chatList.appendChild(div);
        var strings = val.split(/\n|<br>/g);
        var radioCnt = 1;
        var linkReg = RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\&\;\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/);
        var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
        var imgTagReg = RegExp(/<img ([\s\S]*?)>/);
        var radioName = "sinclo-radio" + chatList.children.length;
        var content = "";
        var className;

        if (sincloInfo.widget.widgetSizeType === 1 || check.smartphone()) {
          className = 'smallSizeImg';
        }
        else if (sincloInfo.widget.widgetSizeType === 2) {
          className = 'middleSizeImg';
        }
        else if (sincloInfo.widget.widgetSizeType === 3) {
          className = 'largeSizeImg';
        }

        if (check.isset(cName) === false) {
          cName = "";
        }
        check.escape_html(cName); // エスケープ

        if (cs === "sinclo_re") {
          div.style.textAlign = "left";
          if (cName !== "") {
            content = "<span class='cName'>" + cName + "</span>";
          }
        } else if (cs === "sinclo_se") {
          div.style.textAlign = "right";
        }
        for (var i = 0; strings.length > i; i++) {
          var str = check.escape_html(strings[i]);
          var unEscapeStr = str
            .replace(/(&lt;)/g, '<')
            .replace(/(&gt;)/g, '>')
            .replace(/(&quot;)/g, '"')
            .replace(/(&#39;)/g, "'")
            .replace(/(&amp;)/g, '&');

          if ( cs === "sinclo_re" ) {
            // ラジオボタン
            var radio = str.indexOf('[]');
            if ( radio > -1 ) {
              var name = str.slice(radio+2).trim();
              str = "<sinclo-radio><input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + name + "'>";
              str += "<label for='" + radioName + "-" + i + "'>" + name + "</label></sinclo-radio>";
            }
            // ボタン（CogmoAttend）
            if (str.match(/\[(.*)?]/g)) {
              cs += ' withButton';
              var buttons = str.match(/\[(.*?)]/g);
              var buttonHtml = "";
              for (var j=0; j < buttons.length; j++) {
                str = str.replace(buttons[j], "");
                var buttonStr = buttons[j]  .replace(/[\[\]]/g, '');
                buttonHtml += "<p class='sincloButtonWrap' onclick='sinclo.chatApi.send(\"button_" + buttonStr + "\")'><span class='sincloButton'>" + buttonStr + "</span></p>"
              }
              str = "<span class='sinclo-text-line'>" + str + "</span>";
              str += buttonHtml;
            }
            // フィードバックボタン（CogmoAttend）
            if (isFeedbackMsg) {
              cs += ' withButton';
              str = "<span class='sinclo-text-line'>" + str + "</span>";
              str += "<p class='sincloButtonWrap' onclick='sinclo.chatApi.send(\"button_はい\")'><span class='sincloButton'>はい</span></p>";
              str += "<p class='sincloButtonWrap' onclick='sinclo.chatApi.send(\"button_いいえ\")'><span class='sincloButton'>いいえ</span></p>"
            }
          }
          // リンク
          var link = str.match(linkReg);
          var linkTabReg = RegExp(/<a ([\s\S]*?)>([\s\S]*?)<\/a>/);
          var linkTab = unEscapeStr.match(linkTabReg);
          if ( link !== null || linkTab !== null) {
            if ( linkTab !== null) {
              if(link !== null) {
                var a = linkTab[0];
                //imgタグ有効化
                var img = unEscapeStr.match(imgTagReg);
                if(img == null) {
                  //ボタンのCSSを外す
                  var linkButtonTabReg = RegExp(/<a ([\s\S]*?)style=([\s\S]*?)>([\s\S]*?)<\/a>/);
                  var linkButtonTab = unEscapeStr.match(linkButtonTabReg);
                  if(linkButtonTab !== null) {
                    var processedLink = linkButtonTab[1].replace(/ /g, "\$nbsp;");
                  }
                  else {
                    var processedLink = linkTab[1].replace(/ /g, "\$nbsp;");
                  }
                  a = a.replace(linkTab[1],linkTab[1]+" onclick=link('"+linkTab[2]+"','"+processedLink+"')");
                }
                else {
                  var processedLink = linkTab[1].replace(img[0], "");
                  processedLink = processedLink.replace(/ /g, "\$nbsp;");
                  imgTag = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div>";
                  a = a.replace(img[0], imgTag);
                  var urlTagReg = RegExp(/href="([\s\S]*?)"([\s\S]*?)/);
                  var url = a.match(urlTagReg);
                  a = a.replace(linkTab[1],linkTab[1]+" onclick=link('"+url[1]+"','"+processedLink+"')");
                }
              }
              else {
                // ただの文字列にする
                var a = "<span class='link'>"+ linkTab[2] + "</span>";
              }
              str = unEscapeStr.replace(linkTab[0], a);
            }
            //URLのみのリンクの場合
            else {
              var url = link[0];
              //imgタグ有効化
              var img = unEscapeStr.match(imgTagReg);
              if(img == null) {
                var a = '<a href="' + url + '" target="_blank">' + url + '</a>';
                var linkTabReg = RegExp(/<a ([\s\S]*?)>([\s\S]*?)<\/a>/);
                var linkTab = a.match(linkTabReg);
                processedLink = linkTab[1].replace(/ /g, "\$nbsp;");
                a = a.replace(linkTab[1],linkTab[1]+" onclick=link('"+linkTab[2]+"','"+processedLink+"')");
                str = str.replace(url, a);
              }
              else {
                var imageBlock = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img class='"+className+"' "+img[1]+"></div>";

                str = unEscapeStr.replace(img[0], imageBlock);

              }
            }
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
          if ( cs === "sinclo_re" ) {
            //imgタグ
            var imgTagReg = RegExp(/<img ([\s\S]*?)>/);
            var img = unEscapeStr.match(imgTagReg);
            if(img !== null && link == null && linkTab == null) {
              imgTag = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div>";
              str = unEscapeStr.replace(img[0], imgTag);
            }
          }
          if(str.match(/<(".*?"|'.*?'|[^'"])*?>/)) {
            content += "" + str + "\n";
          } else {
            content += "<span class='sinclo-text-line'>" + str.replace(/^[\n|\r\n|\r]$/g, "") + "</span>\n";
          }
        }

        if (cs.indexOf("sinclo_re") !== -1) {
          cs += ' effect_left';
        } else if (cs.indexOf("sinclo_se") !== -1) {
          cs += ' effect_right';
        }

        li.className = cs;
        li.innerHTML = content;
      },
      createSendFileMessage: function (data, cName) {
        var chatList = document.getElementsByTagName('sinclo-chat')[0];
        var div = document.createElement('div');
        div.style.cursor = "pointer";
        var li = document.createElement('li');
        var thumbnail = "";
        var isExpired = Math.floor((new Date()).getTime() / 1000) >= (Date.parse(data.expired.replace(/-/g, '/')) / 1000);

        div.appendChild(li);
        chatList.appendChild(div);

        if (data.extension.match(/(jpeg|jpg|gif|png)$/i) != null && !isExpired) {
          thumbnail = "<img src='" + data.downloadUrl + "' class='sendFileThumbnail' width='64' height='64'>";
        } else {
          thumbnail = "<i class='sinclo-fal " + this._selectFontIconClassFromExtension(data.extension) + " fa-4x sendFileThumbnail' aria-hidden='true'></i>";
        }

        var content = "<span class='cName'>" + (Number(window.sincloInfo.widget.showAutomessageName) !== 2 ? "ファイルが送信されました" : "") + (isExpired ? "（ダウンロード有効期限切れ）" : "") + "</span>";
        if (check.isset(data.message) && data.message !== "") {
          content += "<span class='sendFileMessage'>" + data.message + "</span>";
        }
        content += "<div class='sendFileContent'>";
        content += "  <div class='sendFileThumbnailArea'>" + thumbnail + "</div>";
        content += "  <div class='sendFileMetaArea'>";
        content += "    <span class='data sendFileName'>" + data.fileName + "</span>";
        content += "    <span class='data sendFileSize'>" + common.formatBytes(data.fileSize, 2) + "</span>";
        content += "  </div>";
        content += "</div>";

        // kari
        var colorList = common.getColorList(window.sincloInfo.widget);
        if (!isExpired) {
          div.addEventListener('click', function () {
            window.open(data.downloadUrl);
          });
          div.addEventListener('mouseenter', function () {
            var changeColor = common.toRGBAcolor(colorList['reBackgroundColor'], 0.9);
            li.style.backgroundColor = changeColor;
          });
          div.addEventListener('mouseleave', function () {
            var changeColor = colorList['reBackgroundColor'];
            li.style.backgroundColor = changeColor;
          });
        }

        li.className = 'sinclo_re effect_left';
        li.innerHTML = content;
      },
      createSelectUploadFileMessage: function (message, cancelable, cancelLabel, extensionType, extendedExtensions) {
        common.chatBotTypingRemove();
        var chatList = document.getElementsByTagName('sinclo-chat')[0];
        var div = document.createElement('div');
        div.style.cursor = "pointer";
        var li = document.createElement('li');
        var thumbnail = "";

        div.appendChild(li);
        chatList.appendChild(div);

        var content = (Number(window.sincloInfo.widget.showAutomessageName) !== 2) ? "<span class='cName'>" + sincloInfo.widget.subTitle + "</span>" : "";
        content += "<div class='receiveFileContent'>";
        content += "  <div class='selectFileArea'>";
        content += "    <p class='drop-area-message'>" + message + "</p>";
        content += "    <p class='drop-area-icon'><i class='sinclo-fal fa-cloud-upload'></i></p>";
        content += "<p>または</p>";
        content += "    <p class='drop-area-button'>";
        content += "<a class='select-file-button'>ファイルを選択</a>";
        content += "    </p>";
        content += "    <input type='file' class='receiveFileInput' name='receiveFileInput' style='display:none'>"
        content += "  </div>";
        content += "  <div class='loadingPopup hide'><i class='sinclo-fal fa-spinner load'></i><p class='progressMessage'>読み込み中です。<br>しばらくお待ち下さい。</p></div>"
        content += "</div>";
        if (cancelable) {
          content += "<div class='cancelReceiveFileArea'>";
          content += "<a>" + cancelLabel + "</a>";
          content += "</div>";
        }

        li.className = 'sinclo_re effect_left recv_file_left';
        li.innerHTML = content;

        if (cancelable) {
          li.querySelector('div.cancelReceiveFileArea a').addEventListener('click', function () {
            chatList.removeChild(div);
            emit('sendChat', {
              historyId: sinclo.chatApi.historyId,
              stayLogsId: sinclo.chatApi.stayLogsId,
              chatMessage: "ファイル送信をキャンセル",
              mUserId: null,
              messageType: 19,
              messageRequestFlg: 0,
              isAutoSpeech: false,
              notifyToCompany: false,
              isScenarioMessage: true
            }, function () {
              $(document).trigger(sinclo.scenarioApi._events.fileUploaded, [true, {
                "canceled": true,
                "message": "ファイル送信をキャンセル"
              }]);
            });
          });
        }

        sinclo.chatApi.fileUploader.init($('#sincloBox'),
          $(li.querySelector('div.receiveFileContent div.selectFileArea')),
          $(li.querySelector('div.receiveFileContent div.selectFileArea p.drop-area-button a.select-file-button')),
          $(li.querySelector('div.receiveFileContent div.selectFileArea input.receiveFileInput')),
          extensionType,
          extendedExtensions);
        this.scDown();
      },
      createSentFileMessage: function (comment, downloadUrl, extension) {
        var divElm = document.createElement('div');
        divElm.style.textAlign = "right";
        var thumbnail = "";
        if (extension.match(/(jpeg|jpg|gif|png)$/i) != null) {
          thumbnail = "<img src='" + downloadUrl + "' class='sendFileThumbnail " + sinclo.chatApi.fileUploader._selectPreviewImgClass() + "'>";
        } else {
          thumbnail = "<i class='sinclo-fal " + this._selectFontIconClassFromExtension(extension) + " fa-4x sendFileThumbnail' aria-hidden='true'></i>";
        }
        divElm.innerHTML = "  <li class=\"sinclo_se effect_right chat_right uploaded details\">" +
          "    <div class=\"receiveFileContent\">" +
          "      <div class=\"selectFileArea\">" +
          "        <p class=\"preview\">" + thumbnail + "</p>" +
          "        <p class=\"commentLabel\">＜コメント＞</p>" +
          "        <p class=\"commentarea\" style='text-align: left;'>" + comment + "</p>" +
          "      </div>" +
          "    </div>" +
          "  </li>";
        // 要素を追加する
        document.getElementById('chatTalk').querySelector('sinclo-chat').appendChild(divElm);
      },
      _selectFontIconClassFromExtension: function (ext) {
        var selectedClass = "",
          icons = {
            image: 'fa-file-image',
            pdf: 'fa-file-pdf',
            word: 'fa-file-word',
            powerpoint: 'fa-file-powerpoint',
            excel: 'fa-file-excel',
            audio: 'fa-file-audio',
            video: 'fa-file-video',
            zip: 'fa-file-zip',
            code: 'fa-file-code',
            text: 'fa-file-text',
            file: 'fa-file'
          },
          extensions = {
            gif: icons.image,
            jpeg: icons.image,
            jpg: icons.image,
            png: icons.image,
            pdf: icons.pdf,
            doc: icons.word,
            docx: icons.word,
            ppt: icons.powerpoint,
            pptx: icons.powerpoint,
            xls: icons.excel,
            xlsx: icons.excel,
            aac: icons.audio,
            mp3: icons.audio,
            ogg: icons.audio,
            avi: icons.video,
            flv: icons.video,
            mkv: icons.video,
            mp4: icons.video,
            gz: icons.zip,
            zip: icons.zip,
            css: icons.code,
            html: icons.code,
            js: icons.code,
            txt: icons.text,
            csv: icons.csv,
            file: icons.file
          };
        if (check.isset(extensions[ext])) {
          selectedClass = extensions[ext]
        } else {
          selectedClass = extensions['file'];
        }
        return selectedClass;
      },
      createForm: function (isConfirm, hearingTarget, resultData, callback) {
        common.chatBotTypingRemove();
        if(sinclo.scenarioApi.isProcessing()) {
          sinclo.hideTextarea();
        }
        var chatList = document.getElementsByTagName('sinclo-chat')[0];
        var div = document.createElement('div');
        var li = document.createElement('li');

        div.appendChild(li);
        chatList.appendChild(div);

        var formElements = "";
        var isEmptyRequire = false;

        var content = "";
        if(isConfirm) {
          hearingTarget.forEach(function(elm, idx, arr){
            if(elm.required && resultData[Number(elm.inputType)].length === 0) {
              isEmptyRequire = true;
            }
            formElements += (arr.length - 1 === idx) ? "    <div class='formElement'>" : "    <div class='formElement withMB'>";
            formElements += "      <label class='formLabel'>" + elm.label + (elm.required ? "<span class='require'>*</span>" : "") + "</label>";
            formElements += "      <input type='text' class='formInput' placeholder='" + elm.label + "を入力してください' data-required='" + elm.required + "' data-input-type='" + elm.inputType + "' data-label-text='" + elm.label + "' name='" + elm.label + "' value='" + resultData[Number(elm.inputType)] + "'/>";
            formElements += "    </div>";
          });

          content +=  (Number(window.sincloInfo.widget.showAutomessageName) !== 2) ? "<span class='cName'>" + sincloInfo.widget.subTitle + "</span>" : "";
          content += "<div class='formContentArea'>";
          content += "  <p class='formMessage'>" + ((isEmptyRequire) ? "必須項目の入力が認識できませんでした。\n*印の項目を入力してください。" : "こちらの内容でよろしいでしょうか？")  + "</p>";
          content += "  <div class='formArea'>";
          content += formElements;
          content += "    <p class='formOKButtonArea'><span class='formOKButton'>OK</span></p>";
          content += "  </div>";
          content += "</div>";
          li.className = 'sinclo_re effect_left sinclo_form';
        } else {
          hearingTarget.forEach(function(elm, idx, arr){
            if(elm.required && resultData[elm.variableName].value.length === 0) {
              isEmptyRequire = true;
            }
            formElements += (arr.length - 1 === idx) ? "    <div class='formElement'>" : "    <div class='formElement withMB'>";
            formElements += "      <label class='formLabel'>" + elm.label + (elm.required ? "<span class='require'>*</span>" : "") + "</label>";
            formElements += "      <input type='text' class='formInput' placeholder='" + elm.label + "を入力してください' data-required='" + elm.required + "' data-label-text='" + elm.label + "' name='" + elm.variableName + "' value='" + resultData[elm.variableName].value + "' readonly/>";
            formElements += "    </div>";
          });

          content += (Number(window.sincloInfo.widget.showAutomessageName) !== 2) ? "<span class='cName'>" + sincloInfo.widget.subTitle + "</span>" : "";
          content += "<div class='formContentArea'>";
          content += "  <div class='formArea'>";
          content += formElements;
          content += "    <p class='formOKButtonArea'><span class='formOKButton disabled'>OK</span></p>";
          content += "  </div>";
          content += "</div>";
          li.className = 'sinclo_se effect_right sinclo_form';
        }
        li.innerHTML = content;
        if(isEmptyRequire) {
          $(li).find('span.formOKButton').addClass('disabled');
        }
        $(li).find('span.formOKButton').on('click', function(e){
          if($(this).hasClass('disabled')) return;
          var returnValue = {};
          var targetArray = $(li).find('.formInput');
          targetArray.each(function(index, element) {
            console.log("CHANGED : %s vs %s", $(element).val(), resultData[$(element).data('input-type')]);
            returnValue[$(element).attr('name')] = {
              label: $(element).data('label-text'),
              value: $(element).val(),
              required: $(element).data('required'),
              changed: $(element).val() !== resultData[Number($(element).data('input-type'))]
            }
          });
          console.log("return Value : %s",JSON.stringify(returnValue));
          callback(returnValue);
        });
        $(li).find('input.formInput').on('input', function(){
          $(li).find('input.formInput').each(function(idx, elem){
            if(hearingTarget[idx].required && $(this).val().length === 0) {
              $(li).find('span.formOKButton').addClass('disabled');
              return false;
            } else if (hearingTarget.length - 1 === idx){
              $(li).find('span.formOKButton').removeClass('disabled');
            }
          });
        });
        this.scDown();
      },
      createFormFromLog: function (data) {
        var chatList = document.getElementsByTagName('sinclo-chat')[0];
        var div = document.createElement('div');
        var li = document.createElement('li');

        div.appendChild(li);
        chatList.appendChild(div);

        var formElements = "";
        var content = "";
        var objKeys = Object.keys(data);
        objKeys.forEach(function(variableName, index, array){
          formElements += (array.length - 1 === index) ? "    <div class='formElement'>" : "    <div class='formElement withMB'>";
          formElements += "      <span class='formLabel'>" + data[variableName].label + (data[variableName].required ? "<span class='require'>*</span>" : "") + "</span>";
          formElements += "      <span class='formLabelSeparator'>：</span>";
          formElements += "      <span class='formValue'>" + (data[variableName].value ? data[variableName].value : "（なし）") + "</span>";
          formElements += "    </div>";
        });

        content += "<div class='formContentArea'>";
        content += "  <div class='formSubmitArea'>";
        content += formElements;
        content += "  </div>";
        content += "</div>";
        li.className = 'sinclo_se effect_right sinclo_form';
        li.innerHTML = content;
      },
      hideForm: function() {
        $('li.sinclo_re.sinclo_form').remove();
      },
      createMessageUnread: function (data, isScenarioMessage, isCogmoAttendBotMessage, isFeedbackMsg) {
        if (data.cn && data.cn.indexOf("sinclo_re") >= 0) {
          sinclo.chatApi.unread++;
          sinclo.chatApi.showUnreadCnt();
        }
        if(isCogmoAttendBotMessage) {
          sinclo.chatApi.createCogmoAttendBotMessage(data.cn, data.message, data.name, isFeedbackMsg);
        } else {
          sinclo.chatApi.createMessage(data, isScenarioMessage);
        }
      },
      createPullDown: function (cs, val, name, settings) {
          // create pulldown
        sinclo.chatApi.createPullDownHtml(cs, val, name, settings);
      },
      clearChatMessages: function () {
        var chatTalk = document.getElementsByTagName("sinclo-chat")[0];
        while (chatTalk.firstChild) chatTalk.removeChild(chatTalk.firstChild);
      },
      scDownTimer: null,
      scDown: function (obj) {
        console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>scDownCalled!!');
        if (this.scDownTimer) {
          clearTimeout(this.scDownTimer);
        }
        //setTimeout(function(){common.chatBotTyping(obj)},800);
        this.scDownTimer = setTimeout(function () {
          console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>scDownNow!!');
          var chatTalk = document.getElementById('chatTalk');
            var receiveLastMessage = $('#chatTalk sinclo-chat div:last-of-type').find('.sinclo_re:last-of-type');
            var chatScrollHeight = chatTalk.scrollHeight;
            var chatClientHeight = chatTalk.clientHeight;
            if(receiveLastMessage.length > 0) {
              var lastMessageHeight = receiveLastMessage.parent().outerHeight();
              var paddingBottom = parseFloat($('#chatTalk sinclo-typing').css('padding-bottom'));
              if(check.smartphone()) {
                ratio = $(window).width() * (1/285);
                paddingBottom = paddingBottom + (10 * ratio);
              }
              if(chatTalk.clientHeight > (lastMessageHeight + paddingBottom)) { // FIXME ウィジェットサイズに合わせた余白で計算すること
                $('#sincloBox #chatTalk').animate({
                  scrollTop: (chatScrollHeight - chatClientHeight - 2)
                }, 300);
              } else {
                //「○○が入力中です」のメッセージが残っていない場合
                if(document.getElementById('sinclo_typeing_message') === null) {
                  $('#sincloBox #chatTalk').animate({
                    scrollTop: (chatScrollHeight - (lastMessageHeight + paddingBottom)) // FIXME ウィジェットサイズに合わせた余白で計算すること
                  }, 300);
                }
                //「○○が入力中です」のメッセージが残っている場合
                else {
                  $('#sincloBox #chatTalk').animate({
                    scrollTop: (chatScrollHeight - (lastMessageHeight + paddingBottom + 25)) // FIXME ウィジェットサイズに合わせた余白で計算すること
                  }, 300);
                }
              }
            } else {
              $('#sincloBox #chatTalk').animate({
                scrollTop: (chatScrollHeight - chatClientHeight - 2)
              }, 300);
            }
          console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>scDownNowEnd!!');
          }, 500);
        },
        scDownImmediate: function(){
          console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>scDownImmediate");
          var chatTalk = document.getElementById('chatTalk');
          var receiveLastMessage = $('#chatTalk sinclo-chat div:last-of-type').find('.sinclo_re:last-of-type');
          if(receiveLastMessage.length > 0) {
            var lastMessageHeight = receiveLastMessage.parent().outerHeight();
            var paddingBottom = parseFloat($('#chatTalk sinclo-typing').css('padding-bottom'));
            var ratio = 1;
            if(check.smartphone()) {
              ratio = $(window).width() * (1/285);
              paddingBottom = paddingBottom + (10 * ratio);
            }
            if(chatTalk.clientHeight > (lastMessageHeight + paddingBottom)) { // FIXME ウィジェットサイズに合わせた余白で計算すること
              $('#sincloBox #chatTalk').scrollTop(chatTalk.scrollHeight - chatTalk.clientHeight - 2);
            } else {
              //「○○が入力中です」のメッセージが残っていない場合
              if(document.getElementById('sinclo_typeing_message') === null) {
                $('#sincloBox #chatTalk').scrollTop(chatTalk.scrollHeight - (lastMessageHeight + paddingBottom * ratio));
              }
              //「○○が入力中です」のメッセージが残っている場合
              else {
                $('#sincloBox #chatTalk').scrollTop(chatTalk.scrollHeight - (lastMessageHeight + paddingBottom * ratio + 25));
              }
            }
          } else {
            $('#sincloBox #chatTalk').scrollTop(chatTalk.scrollHeight - chatTalk.clientHeight - 2);
          }
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
        if ($('#flexBoxHeight').hasClass("sinclo-hide")) {
          elm = document.getElementById('miniSincloChatMessage');
        }
        var req = new RegExp(/^\s*$/);
        if (check.isset(elm.value) && !req.test(elm.value)) {
          this.send(elm.value);
          elm.value = "";
        }
        this.pushFlg = false;
      },
      send: function (value) {
        var messageType = sinclo.chatApi.messageType.customer;
        // 自動返信の処理中でなければ
        if (!sinclo.trigger.processing) {
          storage.s.set('chatAct', true); // オートメッセージを表示しない
        }

        // タイマーが仕掛けられていたらリセット
        if (this.sendErrCatchTimer !== null) {
          clearTimeout(this.sendErrCatchTimer);
        }

        // チャットの契約をしている場合
        if (window.sincloInfo.contract.chat) {
          var firstChatEmit = storage.s.get('chatEmit');
          //チャットリクエスト件数でない
          var noFlg = 0;
          //チャットリクエスト件数である
          var flg = 1;
          var messageRequestFlg = noFlg;

            //サイト訪問者がチャット送信した初回のタイミング
            if ( !check.isset(firstChatEmit) ) {
              if(typeof ga == "function"){
                ga('send', 'event', 'sinclo', 'sendChat', location.href, 1);
              }
              if(storage.s.get('requestFlg') !== 'true') {
                storage.s.set('requestFlg',true);
                messageRequestFlg = flg;
              }
              else {
                messageRequestFlg = noFlg;
              }
            }

          var isScenarioMessage = false;
          console.log("sinclo.scenarioApi.isProcessing() : " + sinclo.scenarioApi.isProcessing() + " sinclo.scenarioApi.isWaitingInput() : " + sinclo.scenarioApi.isWaitingInput())
          if (sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi.isWaitingInput()
            && (!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false")) {
            sinclo.scenarioApi.triggerInputWaitComplete(value);
            messageType = sinclo.scenarioApi.getCustomerMessageType();
            // シナリオ中の返答はオペレータへの通知をしない
            isScenarioMessage = true;
          }

          sinclo.trigger.judge.matchAllSpeechContent(value, function (result) {
            if ((isScenarioMessage || result) && (!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false")) {
              result = true;
              storage.s.set('chatAct', false); // オートメッセージを表示しない
            }

            //初回通知メッセージの場合
            if (storage.s.get('initialNotification') === null || storage.s.get('initialNotification') === 'true') {
              initialNotification = true;
            }
            //初回通知メッセージではない場合
            else if (storage.s.get('initialNotification') === 'false') {
              initialNotification = false;
            }
            else {
              initialNotification = false;
            }
            setTimeout(function () {
              emit('sendChat', {
                historyId: sinclo.chatApi.historyId,
                stayLogsId: sinclo.chatApi.stayLogsId,
                chatMessage: value,
                mUserId: null,
                messageType: messageType,
                messageRequestFlg: messageRequestFlg,
                initialNotification: initialNotification,
                isAutoSpeech: result,
                notifyToCompany: !result,
                isScenarioMessage: isScenarioMessage
              }, function(){
                if (sinclo.scenarioApi.isProcessing() && sinclo.scenarioApi.isWaitingInput()
                  && (!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false")) {
                  setTimeout(function(){
                    sinclo.scenarioApi.triggerInputWaitComplete(value);
                  },100);
                }
              });
            }, 100);
          });

          storage.s.set('chatEmit', true);
        }

        // スマートフォンの場合、タイマーをセット。（メッセージ送信に失敗した場合にリロードを促す）
        if (check.smartphone()) {
          this.sendErrCatch();
        }

      },
      observeType: { // 入力中監視処理
        timer: null,
        prevMessage: "",
        status: false,
        start: function () { // タイピング監視処理
          var sendMessage = document.getElementById('sincloChatMessage');
          if (this.timer !== null) {
            clearInterval(this.timer);
          }
          // 300ミリ秒ごとに入力値をチェック
          this.timer = setInterval(function () {
            if (sendMessage.value === "") {
              sinclo.chatApi.observeType.prevMessage = "";
              sinclo.chatApi.observeType.send(false, sendMessage.value);
            }
            else if (sendMessage.value !== sinclo.chatApi.observeType.prevMessage) {
              sinclo.chatApi.observeType.prevMessage = sendMessage.value;
              sinclo.chatApi.observeType.send(true, sendMessage.value);
            }
          }, 300);
        },
        send: function (status, message) { // 状態の逐一送信処理
          if (sinclo.chatApi.observeType.status !== status || (status === true && message !== "")) {
            sinclo.chatApi.observeType.emit(status, message);
            sinclo.chatApi.observeType.status = status;
          }
        },
        emit: function (status, message) { // 状態の送信処理
          emit('sendTypeCond', {type: 2, status: status, message: message, sincloSessionId: userInfo.sincloSessionId});
        }
      },
      sendErrCatchFlg: false,
      sendErrCatchTimer: null,
      sendErrCatch: function () {
        if (this.sendErrCatchTimer !== null) {
          clearTimeout(this.sendErrCatchTimer);
        }
        this.sendErrCatchTimer = setTimeout(function () {
          $("sinclo-chat-alert").css('display', 'block').html('通信が切断されました。<br>こちらをタップすると再接続します。').on('click', function () {
            var result = common.reconnectManual();
            if (result) {
              $("sinclo-chat-alert").css('display', 'none');
              sinclo.chatApi.initEvent();
              sinclo.chatApi.sendErrCatchFlg = false;
            }
          });
          sinclo.chatApi.sendErrCatchFlg = true;
          sinclo.chatApi.removeAllEvent();
        }, 5000);
      },
      inactiveTimer: null,
      inactiveCloseFlg: false,
      startInactiveTimeout: function () {
        if (!this.inactiveTimer) {
          console.log("start inactive timer");
          this.inactiveTimer = setTimeout(function () {
            if (socket) {
              sinclo.chatApi.inactiveCloseFlg = true;
              storage.s.set('inactiveTimeout', true);
              console.log("close socket");
              socket.close();
            }
            $("sinclo-chat-alert").css('display', 'block').html('クリックして再接続').on('click', function () {
              var result = common.reconnectManual();
              if (result) {
                $("sinclo-chat-alert").css('display', 'none');
                sinclo.chatApi.initEvent();
              }
            });
            sinclo.chatApi.removeAllEvent();
          }, 90 * 60 * 1000);
        }
      },
      clearInactiveTimeout: function () {
        if (this.inactiveTimer) {
          console.log("clear inactive timer");
          clearTimeout(this.inactiveTimer);
          this.inactiveTimer = null;
        }
      },
      sound: null,
      call: function () {
        // デスクトップ通知用
        if (this.sound && !check.smartphone()) {
          this.sound.play();
        }
      },
      //未読数表示
      showUnreadCnt: function () {
        var elmId = "sincloChatUnread",
          unreadIcon = document.getElementById(elmId);
        var sincloBox = document.getElementById('sincloBox');
        var flg = sinclo.widget.condifiton.get();
        //unreadIconがあればエレメントを削除
        if (unreadIcon) {
          unreadIcon.parentNode.removeChild(unreadIcon);
        }
        if (Number(sinclo.chatApi.unread) > 0) {
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
          if (!abridgementType['MinRes']) {
            //通常時
            if (mainImg) mainImg.appendChild(em);
          }
          else {
            //シンプルデザイン時
            if (titleElm) titleElm.appendChild(em);
          }
        }
      },
      retReadFromCustomer: function (d) {
        var obj = JSON.parse(d);
        if (obj.sincloSessionId === userInfo.sincloSessionId) {
          sinclo.chatApi.unread = 0;
          sinclo.chatApi.showUnreadCnt();
        }
      },
      KEY_TRIGGERED_AUTO_SPEECH: "triggeredAutoSpeech",
      _getAutoSpeechTriggeredList: function () {
        return storage.s.get(this.KEY_TRIGGERED_AUTO_SPEECH) ? JSON.parse(storage.s.get(this.KEY_TRIGGERED_AUTO_SPEECH)) : [];
      },
      // 発動した発言内容を保存
      saveAutoSpeechTriggered: function (triggerType, id) {
        console.log("saveAutoSpeechTriggered triggerType : " + triggerType + " id : " + id);
        if (triggerType === "1") {
          // 発動条件が１回のみ有効であればidを保持する
          var array = this._getAutoSpeechTriggeredList();
          if (array.indexOf(id) < 0) {
            // 登録済みでなければ追加する
            array.push(id);
            storage.s.set(this.KEY_TRIGGERED_AUTO_SPEECH, JSON.stringify(array));
          }
        } else {
          console.log("triggerType = 2");
        }
      },
      // 発動した発言内容を保存
      triggeredAutoSpeechExists: function (id) {
        var array = this._getAutoSpeechTriggeredList();
        return array.indexOf(id) >= 0;
      },
      fileUploader: {
        isDisable: false,
        dragging: false,
        dragArea: null,
        droppable: null,
        selectFileBtn: null,
        selectInput: null,
        fileObj: null,
        loadData: null,
        extensionType: null,
        extendedExtensions: null,

        init: function (dragArea, droppable, selectFileButton, selectInput, extensionType, extendedExtensions) {
          this.dragArea = dragArea;
          this.droppable = droppable;
          this.selectFileBtn = selectFileButton;
          this.selectInput = selectInput;
          this.extensionType = extensionType;
          this.extendedExtensions = extendedExtensions;
          if (window.FileReader) {
            this._addDragAndDropEvents();
          } else {
            this.isDisable = true;
          }
          this._addSelectFileEvents();
        },
        _addDragAndDropEvents: function () {
          this.dragArea.on("dragenter", this._enterEvent);
          this.dragArea.on("dragover", this._overEvent);
          this.dragArea.on("dragleave", this._leaveEvent);
          this.dragArea.on("drop", function () {
            event.preventDefault();
            event.stopPropagation();
            return false;
          });
          this.droppable.on("drop", this._handleDroppedFile);
        },
        _addSelectFileEvents: function () {
          this.selectFileBtn.on('click', function (event) {
            sinclo.chatApi.fileUploader.selectInput.trigger('click');
          });
          this.selectInput.on("click", function (event) {
            sinclo.chatApi.fileUploader._hideInvalidError();
            $(this).val(null);
          }).on("change", function (event) {
            if (sinclo.chatApi.fileUploader.selectInput[0].files[0] && sinclo.chatApi.fileUploader.selectInput[0].files.length === 1) {
              var self = this;
              sinclo.chatApi.fileUploader.fileObj = sinclo.chatApi.fileUploader.selectInput[0].files[0];
              sinclo.chatApi.fileUploader._showLoadingPopup($(self).parents('li.sinclo_re'));
              // ファイルの内容は FileReader で読み込みます.
              var fileReader = new FileReader();
              fileReader.onload = function (event) {
                sinclo.chatApi.fileUploader._hideLoadingPopup($(self).parents('li.sinclo_re'));
                if (!sinclo.chatApi.fileUploader._validExtension(sinclo.chatApi.fileUploader.fileObj.name)) {
                  sinclo.chatApi.fileUploader._showInvalidError();
                  return;
                } else {
                  $('#chatTab').find('[class^="sinclo_re delete_e"]').remove();
                }
                // event.target.result に読み込んだファイルの内容が入っています.
                // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
                sinclo.chatApi.fileUploader.loadData = event.target.result;
                sinclo.chatApi.fileUploader._showPreview(self, sinclo.chatApi.fileUploader.fileObj, sinclo.chatApi.fileUploader.loadData);
              };
              fileReader.readAsArrayBuffer(sinclo.chatApi.fileUploader.fileObj);
            } else {
              sinclo.chatApi.fileUploader._showInvalidError();
              return;
            }
          });
        },
        _enterEvent: function (event) {
          sinclo.chatApi.fileUploader.dragging = true;
          sinclo.chatApi.fileUploader._cancelEvent(event);
          return false;
        },
        _overEvent: function (event) {
          sinclo.chatApi.fileUploader.dragging = false;
          sinclo.chatApi.fileUploader.droppable.css('opacity', '0.8');
          sinclo.chatApi.fileUploader._cancelEvent(event);
          return false;
        },
        _leaveEvent: function (event) {
          if (sinclo.chatApi.fileUploader.dragging) {
            sinclo.chatApi.fileUploader.dragging = false;
          } else {
            sinclo.chatApi.fileUploader.droppable.css('opacity', '1.0');
          }
          sinclo.chatApi.fileUploader._cancelEvent(event);
          return false;
        },
        _handleDroppedFile: function (event) {
          sinclo.chatApi.fileUploader._hideInvalidError();
          if (event.originalEvent.dataTransfer.files[0] && event.originalEvent.dataTransfer.files.length === 1) {
            // ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
            sinclo.chatApi.fileUploader.fileObj = event.originalEvent.dataTransfer.files[0];

            var self = this;
            // ファイルの内容は FileReader で読み込みます.
            sinclo.chatApi.fileUploader._showLoadingPopup($(self).parents('li.sinclo_re'));
            var fileReader = new FileReader();
            fileReader.onload = function (event) {
              sinclo.chatApi.fileUploader._hideLoadingPopup($(self).parents('li.sinclo_re'));
              if (!sinclo.chatApi.fileUploader._validExtension(sinclo.chatApi.fileUploader.fileObj.name)) {
                sinclo.chatApi.fileUploader._showInvalidError();
                return;
              } else {
                $('#chatTab').find('[class^="sinclo_re delete_e"]').remove();
              }
              // event.target.result に読み込んだファイルの内容が入っています.
              // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
              sinclo.chatApi.fileUploader.loadData = event.target.result;
              sinclo.chatApi.fileUploader._showPreview(self, sinclo.chatApi.fileUploader.fileObj, sinclo.chatApi.fileUploader.loadData);
            }
            fileReader.readAsArrayBuffer(sinclo.chatApi.fileUploader.fileObj);
          } else {
            sinclo.chatApi.fileUploader._showInvalidError();
          }

          // デフォルトの処理をキャンセルします.
          sinclo.chatApi.fileUploader._cancelEvent(event);
          return false;
        },
        _cancelEvent: function (e) {
          e.preventDefault();
          e.stopPropagation();
        },
        _validExtension: function (filename) {
          var allowExtensions = sinclo.chatApi.fileUploader._getAllowExtension();

          var split = filename.split(".");
          var targetExtension = split[split.length - 1];
          var regex = new RegExp(allowExtensions.join("|"), 'i');
          return regex.test(targetExtension);
          return false;
        },
        _getAllowExtension: function () {
          var base = ["pdf", "pptx", "ppt", "jpg", "jpeg", "png", "gif"];
          switch (Number(sinclo.chatApi.fileUploader.extensionType)) {
            case 1:
              return base;
            case 2:
              var extendSettings = sinclo.chatApi.fileUploader.extendedExtensions;
              return base.concat(extendSettings);
            default:
              return base;
          }
        },
        _showLoadingPopup: function (divElm) {
          $(divElm).find('div.receiveFileContent').find('div.loadingPopup').removeClass('hide');
        },
        _hideLoadingPopup: function (divElm) {
          $(divElm).find('div.receiveFileContent').find('div.loadingPopup').addClass('hide');
        },
        _showInvalidError: function () {
          $(document).trigger(sinclo.scenarioApi._events.fileUploaded, false);
        },
        _hideInvalidError: function () {
          $('#sendMessageArea').find('span.errorMsg').remove();
        },
        _showConfirmDialog: function (message) {
          modalOpen.call(window, message, 'p-cus-file-upload', '確認', 'moment');
          popupEvent.closePopup = function () {
            sinclo.chatApi.uploadFile(sinclo.chatApi.fileUploader.fileObj, sinclo.chatApi.fileUploader.loadData);
            popupEvent.close();
          };
        },
        _showPreview: function (targetElm, fileObj, loadData) {
          sinclo.chatApi.fileUploader._effectScene(false, $(targetElm).parents('li.sinclo_re').parent(), function () {
            var textareaFontSize = 13;
            if (check.smartphone()) {
              // iOSの場合フォントサイズを16px以上にしないとフォーカス時に画面が拡大してしまう
              textareaFontSize = 16;
            }

            var divElm = document.createElement('div');
            divElm.innerHTML = "  <li class=\"sinclo_se effect_right chat_right recv_file_right details\">" +
              "    <div class=\"receiveFileContent\">" +
              "      <div class=\"selectFileArea\">" +
              "        <p class=\"preview\"></p><p class=\"commentLabel\">コメント</p>" +
              "        <p class=\"commentarea\"><textarea maxlength=\"1000\" style=\"font-size: " + textareaFontSize + "px; border-width: 1px; padding: 5px; line-height: 1.5;\"></textarea></p>" +
              "        <div class=\"actionButtonWrap\">" +
              "          <a class=\"cancel-file-button\">選択し直す</a>" +
              "          <a class=\"send-file-button\">送信する</a>" +
              "        </div>" +
              "      </div>" +
              "      <div class='loadingPopup hide'><i class='sinclo-fal fa-spinner load'></i><p class='progressMessage'>アップロード中です。<br>しばらくお待ち下さい。</p></div>" +
              "    </div>" +
              "  </li>";
            divElm.style.textAlign = "right";
            var split = fileObj.name.split(".");
            var targetExtension = split[split.length - 1];

            function afterDesideThumbnail(elm) {
              divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent p.preview').appendChild(elm);
              divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentarea').style.textAlign = 'center';
              $(divElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap a.cancel-file-button')).off('click');
              divElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap a.cancel-file-button').addEventListener('click', function (e) {
                sinclo.chatApi.fileUploader._effectScene(false, $(divElm), function () {
                  document.getElementById('chatTalk').querySelector('sinclo-chat').removeChild(divElm);
                  sinclo.chatApi.fileUploader._effectScene(true, $(targetElm).parents('li.sinclo_re').parent(), function () {
                  });
                });
              });
              $(divElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap a.send-file-button')).off('click');
              divElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap a.send-file-button').addEventListener('click', function (e) {
                var comment = divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentarea textarea').value;
                if (!comment) {
                  comment = "（なし）";
                }
                sinclo.chatApi.fileUploader._showLoadingPopup(divElm);
                sinclo.chatApi.fileUploader._uploadFile(divElm, comment, fileObj, loadData);
              });
              // 要素を追加する
              document.getElementById('chatTalk').querySelector('sinclo-chat').appendChild(divElm);
              sinclo.chatApi.fileUploader._changeResizableTextarea(divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentarea textarea'));
              sinclo.chatApi.scDown();
            }

            if (targetExtension.match(/(jpeg|jpg|gif|png)$/i) != null) {
              var imgElm = document.createElement('img');
              imgElm.classList.add(sinclo.chatApi.fileUploader._selectPreviewImgClass());
              var fileReader = new FileReader();
              fileReader.onload = function (e) {
                imgElm.src = this.result;
                afterDesideThumbnail(imgElm);
              };
              fileReader.readAsDataURL(fileObj);
            } else {
              var iconElm = document.createElement('i');
              iconElm.classList.add('sinclo-fal');
              iconElm.classList.add('fa-4x');
              iconElm.classList.add(sinclo.chatApi._selectFontIconClassFromExtension(targetExtension));
              iconElm.setAttribute("aria-hidden", "true");
              afterDesideThumbnail(iconElm);
            }
          });
        },
        _selectPreviewImgClass: function () {
          var widgetSizeType = check.smartphone() ? 1 : Number(sincloInfo.widget.widgetSizeType);
          switch (widgetSizeType) {
            case 1:
              return 'small';
            case 2:
              return 'middle';
            case 3:
              return 'large';
            default:
              return 'middle';
          }
        },
        _effectScene: function (isBack, jqObj, callback) {
          if (isBack) {
            jqObj.fadeIn('fast', callback);
          } else {
            jqObj.fadeOut('fast', callback);
          }
        },
        _changeResizableTextarea: function (elm) {
          var maxRow = 5;                       // 表示可能な最大行数
          var fontSize = parseFloat(elm.style.fontSize, 10);           // 行数計算のため、templateにて設定したフォントサイズを取得
          var borderSize = parseFloat(elm.style.borderWidth, 10) * 2;  // 行数計算のため、templateにて設定したボーダーサイズを取得(上下/左右)
          var paddingSize = parseFloat(elm.style.padding, 10) * 2;     // 表示高さの計算のため、templateにて設定したテキストエリア内の余白を取得(上下/左右)
          var lineHeight = parseFloat(elm.style.lineHeight, 10);       // 表示高さの計算のため、templateにて設定した行の高さを取得

          function autoResize() {
            console.log("autoResize");
            // テキストエリアの要素のサイズから、borderとpaddingを引いて文字入力可能なサイズを取得する
            var areaWidth = elm.getBoundingClientRect().width - borderSize - paddingSize;

            // フォントサイズとテキストエリアのサイズを基に、行数を計算する
            var textRow = 0;
            elm.value.split('\n').forEach(function (string) {
              var stringWidth = string.length * fontSize;
              textRow += Math.max(Math.ceil(stringWidth / areaWidth), 1);
            });

            // 表示する行数に応じて、テキストエリアの高さを調整する
            if (textRow > maxRow) {
              elm.style.height = (maxRow * (fontSize * lineHeight)) + paddingSize + 'px';
              elm.style.overflow = 'auto';
              sinclo.chatApi.scDown();
            } else {
              elm.style.height = (textRow * (fontSize * lineHeight)) + paddingSize + 'px';
              elm.style.overflow = 'hidden';
              sinclo.chatApi.scDown();
            }
          }

          autoResize();
          elm.addEventListener('input', autoResize);
        },
        _uploadFile: function (targetDivElm, comment, fileObj, loadFile) {
          var fd = new FormData();
          var blob = new Blob([loadFile], {type: fileObj.type});
          fd.append("k", sincloInfo.site.key);
          fd.append("c", comment)
          fd.append("f", blob, fileObj.name);

          $.ajax({
            url: sincloInfo.site.socket + "/FC/pu",
            type: "POST",
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            xhr: function () {
              var XHR = $.ajaxSettings.xhr();
              /*
              if(XHR.upload){
                XHR.upload.addEventListener('progress',function(e){
                  sinclo.chatApi.uploadProgress = parseInt(e.loaded/e.total*10000)/100;
                  console.log(sinclo.chatApi.uploadProgress);
                  if(sinclo.chatApi.uploadProgress === 100) {
                    $('#uploadMessage').css('display', 'none');
                    $('#processingMessage').css('display', 'block');
                  }
                  sinclo.chatApi.$apply();
                }, false);
              }
              */
              return XHR;
            }
          })
            .done(function (data, textStatus, jqXHR) {
              sinclo.chatApi.fileUploader._hideLoadingPopup(targetDivElm);
              sinclo.chatApi.fileUploader._effectScene(false, $(targetDivElm), function () {
                console.log(JSON.stringify(data));
                document.getElementById('chatTalk').querySelector('sinclo-chat').removeChild(targetDivElm);
                emit('sendChat', {
                  historyId: sinclo.chatApi.historyId,
                  stayLogsId: sinclo.chatApi.stayLogsId,
                  chatMessage: JSON.stringify(data),
                  mUserId: null,
                  messageType: 19,
                  messageRequestFlg: 0,
                  isAutoSpeech: false,
                  notifyToCompany: false,
                  isScenarioMessage: true
                }, function () {
                  $(document).trigger(sinclo.scenarioApi._events.fileUploaded, [true, {
                    'downloadUrl': data.downloadUrl,
                    'comment': data.comment
                  }]);
                });
              });
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
              sinclo.chatApi.fileUploader._hideLoadingPopup(targetDivElm);
              sinclo.chatApi.fileUploader._showInvalidError();
            });
        }
      }
    },
    trigger: {
      flg: false,
      nowSaving: false,
      timerTriggeredList: {},
      orTriggeredId: [],
      triggerIds: {},
      processing: false,
      init: function () {
        console.log("sinclo.trigger.init");
        if (!('messages' in window.sincloInfo) || (('messages' in window.sincloInfo) && typeof(window.sincloInfo.messages) !== "object")) return false;
        this.flg = true;
        var messages = window.sincloInfo.messages;
        console.log("MESSAGES : " + JSON.stringify(messages));
        sinclo.trigger.triggerIds = {};
        var andFunc = function (conditionKey, condition, key, ret) {
          if (conditionKey === 7) {
            // 自動返信のトリガーの場合は処理中フラグを立てる
            sinclo.trigger.processing = true;
          }
          console.log("AND FUNC key: " + key + " ret: " + ret);
          var message = messages[key];
          if (typeof(ret) === 'number') {
            if(!sinclo.trigger.triggerIds[ret]) {
              sinclo.trigger.triggerIds[ret] = [];
            }
            if (conditionKey !== 7 ) {
              sinclo.trigger.triggerIds[ret].push(message.id);
            }
            setTimeout(function () {
              console.log(sinclo.trigger.triggerIds);
              //シナリオの場合
              if(message.action_type == 2) {
                if(conditionKey === 7 || sinclo.trigger.triggerIds[ret][0] == message.id){
                  sinclo.trigger.setAction(message.id, message.action_type, message.activity, message.send_mail_flg, message.scenario_id);
                  sinclo.trigger.processing = false;
                  console.log('scenarioStart');
                }
              }
              else {
                sinclo.trigger.setAction(message.id, message.action_type, message.activity, message.send_mail_flg, message.scenario_id);
                sinclo.trigger.processing = false;
              }
              // if(conditionKe大変申し訳ございません。 y === 7) {
              //   // 自動返信実行後はチャット中のフラグを立てる
              //   storage.s.set('chatAct','true');
              // }
            }, ret);
          } else if (ret && typeof(ret) === 'object') {
            sinclo.trigger.timerTriggeredList[message.id] = false;
            setTimeout(function () {
              sinclo.trigger.processing = false;
              console.log("AUTO MESSAGE TIMER TRIGGERED");
              sinclo.trigger.timerTriggeredList[message.id] = true;
            }, ret.delay);
          }
        };
        var orFunc = function (conditionKey, condition, key, ret) {
          var message = messages[key];
          if (conditionKey === 7) {
            // 自動返信のトリガーの場合は処理中フラグを立てる
            sinclo.trigger.processing = true;
          }
          if (typeof(ret) === 'number') {
            if(!sinclo.trigger.triggerIds[ret]) {
              sinclo.trigger.triggerIds[ret] = [];
            }
            if (conditionKey !== 7) {
              sinclo.trigger.triggerIds[ret].push(message.id);
            }
            setTimeout(function () {
              console.log(sinclo.trigger.triggerIds);
              console.log("orFunc::setTimeout message : " + JSON.stringify(message) + "conditionKey : " + conditionKey + " condition : " + JSON.stringify(condition));

              // ・OR条件における発言内容発動条件
              // ・発言内容が先に発動した場合 => 後続で発動した条件は無視する
              // ・その他条件が先に発動した場合 => 発言内容が１回きりの場合、無視する
              // 　　　　　　　　　　　　　　　 => 発言内容が何度でもの場合、発動する
              var isAutoSpeechTrigger = conditionKey && condition && conditionKey === 7;
              var autoSpeechTriggerManyTimes = false;
              if (isAutoSpeechTrigger && condition.speechTriggerCond === "1") {
                autoSpeechTriggerManyTimes = false;
              } else if (isAutoSpeechTrigger && condition.speechTriggerCond === "2") {
                autoSpeechTriggerManyTimes = true;
              }

              if (!autoSpeechTriggerManyTimes && sinclo.trigger.orTriggeredId.indexOf(message.id) >= 0) {
                console.log("OR id: " + message.id + " was triggered. ignoreing");
                return;
              }

              console.log("OR id: " + message.id + " is triggered.");

              if (sinclo.trigger.orTriggeredId.indexOf(message.id) === -1) {
                sinclo.trigger.orTriggeredId.push(message.id);
              }

              if (!isAutoSpeechTrigger && Object.keys(message.activity.conditions).indexOf("7") >= 0) {
                console.log("orFunc saveAutoSpeechTriggered");
                //ここに入るオートメッセージは他の条件で発動するため、発言内容条件で動作しないようフラグを立てる
                var autoSpeechCondition = message.activity.conditions["7"][0];
                console.log("autoSpeechCondition : " + JSON.stringify(autoSpeechCondition));
                if (autoSpeechCondition) {
                  sinclo.chatApi.saveAutoSpeechTriggered(autoSpeechCondition.speechTriggerCond, message.id);
                }
              }
              //シナリオの場合
              if (message.action_type == 2) {
                if (conditionKey === 7 || sinclo.trigger.triggerIds[ret][0] == message.id) {
                  sinclo.trigger.setAction(message.id, message.action_type, message.activity, message.send_mail_flg, message.scenario_id);
                  sinclo.trigger.processing = false;
                  console.log('scenarioStart');
                }
              }
              else {
                sinclo.trigger.setAction(message.id, message.action_type, message.activity, message.send_mail_flg, message.scenario_id);
                sinclo.trigger.processing = false;
              }
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
          var last = (keys.length === Number(i + 1)) ? true : false;
          switch (Number(keys[i])) {
            case 1: // 滞在時間
              this.judge.stayTime(conditions[0], function (err, timer) {
                if (!err && (typeof(timer) === "number" && ret <= timer)) {
                  ret = Number(timer);
                }
                if (err) ret = null;
              });
              break;
            case 2: // 訪問回数
              this.judge.stayCount(conditions[0], function (err, timer) {
                if (err) ret = null;
              });
              break;
            case 3: // ページ
              this.judge.page(conditions[0], function (err, timer) {
                if (err) ret = null;
              });
              break;
            case 4: // 曜日・時間
              this.judge.dayTime(conditions[0], function (err, timer) {
                if (!err && (typeof(timer) === "number" && ret <= timer)) {
                  ret = Number(timer);
                }
                if (err) ret = null;
              });
              break;
            case 5: // リファラー
              this.judge.referrer(conditions[0], function (err, timer) {
                if (err) ret = null;
              });
              break;
            case 6: // 検索ワード
              this.judge.searchWord(conditions[0], function (err, timer) {
                if (err) ret = null;
              });
              break;
            case 7: // 発言内容
              if (ret !== null) { // その他の設定で無効の場合は何もしない
                // あとで実行する関数のため、第三引数は値渡しで対応する必要がある
                var cloneCondition = JSON.parse(JSON.stringify(conditions[0]));
                this.judge.setMatchSpeechContent(1, window.sincloInfo.messages[key].id, cloneCondition, function (err, timer) {
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
              this.judge.pageOfFirst(conditions[0], function (err, timer) {
                if (err) ret = null;
              });
              break;
            case 9: // 前のページ
              this.judge.pageOfPrevious(conditions[0], function (err, timer) {
                if (err) ret = null;
              });
              break;
            case 10: // 営業時間
              this.judge.operating_hours(conditions[0], function (err, timer) {
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
      setOrSetting: function (key, setting, callback) {
        console.log("setOrSetting key : " + key + " setting : " + JSON.stringify(setting));
        var keys = Object.keys(setting.conditions);
        var ret = null;
        for (var i = 0; keys.length > i; i++) {
          var conditions = setting.conditions[keys[i]], u;
          var last = (keys.length === Number(i + 1)) ? true : false;
          switch (Number(keys[i])) {
            case 1: // 滞在時間
              for (u = 0; u < conditions.length; u++) {
                this.judge.stayTime(conditions[u], function (err, timer) {
                  if (!err && (typeof(timer) === "number" && ret <= timer)) {
                    ret = Number(timer);
                  }
                });
              }
              break;
            case 2: // 訪問回数
              for (u = 0; u < conditions.length; u++) {
                this.judge.stayCount(conditions[u], function (err, timer) {
                  if (!err) {
                    ret = 0;
                  }
                });
              }
              break;
            case 3: // ページ
              for (u = 0; u < conditions.length; u++) {
                this.judge.page(conditions[u], function (err, timer) {
                  if (!err) {
                    ret = 0;
                  }
                });
              }
              break;
            case 4: // 曜日・時間
              for (u = 0; u < conditions.length; u++) {
                this.judge.dayTime(conditions[u], function (err, timer) {
                  if (!err && (typeof(timer) === "number" && ret <= timer)) {
                    ret = Number(timer);
                  }
                });
              }
              break;
            case 5: // リファラー
              for (u = 0; u < conditions.length; u++) {
                this.judge.referrer(conditions[u], function (err, timer) {
                  if (!err) {
                    ret = 0;
                  }
                });
              }
              break;
            case 6: // 検索ワード
              for (u = 0; u < conditions.length; u++) {
                this.judge.searchWord(conditions[u], function (err, timer) {
                  if (!err) {
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
                this.judge.operating_hours(conditions[u], function (err, timer) {
                  if (!err) {
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
      setAutoMessage: function (id, cond, sendMail) {
        if (sincloInfo.widget.showTiming === 3) {
          // 初回オートメッセージ表示時にフラグを立てる
          sincloInfo.widgetDisplay = true;
          common.widgetHandler.show();
        }

        //チャットのテキストエリア表示
        if (Number(cond.chatTextarea) === 1 || cond.chatTextarea === undefined || storage.l.get('leaveFlg') == 'true') {
          sinclo.displayTextarea();
          storage.l.set('textareaOpend', 'open');
        }
        //チャットのテキストエリア非表示
        else if (Number(cond.chatTextarea) === 2) {
          sinclo.hideTextarea();
          storage.l.set('textareaOpend', 'close');
        }

        // 発言内容によるオートメッセージかチェックする
        var isSpeechContent = false;
        for (var key in cond.conditions) {
          console.log("DEBUG => key : " + key);
          if (key === "7") { // FIXME マジックナンバー
            isSpeechContent = true;
          }
        }

        console.log("IS SPEECH CONTENT : " + isSpeechContent);

        // 外部連携実装後に外す
        if (sendMail) {
          sinclo.api.callFunction('am', id);
        }
        // 外部連携実装後に外す

        //CVに登録するオートメッセージの場合
        if (cond.cv == 1) {
          var data = {
            chatId: id,
            message: cond.message,
            isAutoSpeech: isSpeechContent,
            achievementFlg: 3,
            sendMailFlg: sendMail
          };
          emit("sendAutoChat", {messageList: sinclo.chatApi.autoMessages.getByArray()});
          sinclo.chatApi.autoMessages.unset();
          sinclo.chatApi.saveFlg = true;
        }
        else if (sendMail) {
          var data = {
            chatId: id,
            message: cond.message,
            isAutoSpeech: isSpeechContent,
            sendMailFlg: sendMail
          };
          emit("sendAutoChat", {messageList: sinclo.chatApi.autoMessages.getByArray()});
          sinclo.chatApi.autoMessages.unset();
          sinclo.chatApi.saveFlg = true;
        } else {
          var data = {
            chatId: id,
            message: cond.message,
            isAutoSpeech: isSpeechContent,
            sendMailFlg: sendMail
          };
        }

        if (!sinclo.chatApi.autoMessages.exists(data.chatId) && !isSpeechContent) {
          //resAutoMessagesで表示判定をするためにidをkeyとして空Objectを入れる
          sinclo.chatApi.autoMessages.push(data.chatId, {});
        }

        if (sinclo.chatApi.saveFlg) {
          // オートメッセージの内容をDBに保存し、オブジェクトから削除する
          console.log("EMIT sendAutoChat::setAutoMessage");
          emit("sendAutoChat", {messageList: [data]});
        }
        else {
          console.log("EMIT sendAutoChatMessage::setAutoMessage");
          if (isSpeechContent) {
          }
          emit('sendAutoChatMessage', data);
        }
      },
      setAction: function (id, type, cond, sendMail, scenarioId) {
        console.log("setAction id : " + id + " type : " + type + " cond : " + JSON.stringify(cond));
        // TODO 今のところはメッセージ送信のみ、拡張予定
        var chatActFlg = storage.s.get('chatAct');
        console.log("chatActFlg : " + chatActFlg);
        if (!check.isset(chatActFlg)) {
          chatActFlg = "false";
        }

        if (String(type) === "1" && ('message' in cond) && (String(chatActFlg) === "false")) {
          if (sinclo.chatApi.autoMessages.exists(id) || sinclo.scenarioApi.isProcessing()) {
            console.log("exists id : " + id + " or scenario is processing");
            return;
          }
          var userName = sincloInfo.widget.subTitle;
          if (window.sincloInfo.widget.showAutomessageName === 2) {
            userName = "";
          }
          var createMessageData = {
            cn: "sinclo_re",
            message: cond.message,
            name: userName,
            chatId: id
          };
          sinclo.chatApi.createMessageUnread(createMessageData);
          sinclo.chatApi.scDown();
          var prev = sinclo.chatApi.autoMessages.getByArray();

          var setAutoMessageTimer = setInterval(function () {
            console.log("監視中");
            var date = common.fullDateTime();
            if (prev.length === 0 || (prev.length > 0 && prev[prev.length - 1].created !== date)) {
              clearInterval(setAutoMessageTimer);
              sinclo.trigger.setAutoMessage(id, cond, sendMail);
              // 自動最大化
              if (!('widgetOpen' in cond) || (check.smartphone() && sincloInfo.widget.hasOwnProperty('spAutoOpenFlg') && Number(sincloInfo.widget.spAutoOpenFlg) === 1)) return false;
              var flg = sinclo.widget.condifiton.get();
              console.log("自動最大化設定");
              if (Number(cond.widgetOpen) === 1 && !common.widgetHandler.isShown()) {
                console.log("オートメッセージによる最大化フラグセット");
                storage.s.set('preWidgetOpened', true);
              } else if (Number(cond.widgetOpen) === 1 && String(flg) === "false") {
                console.log("オートメッセージ最大化処理");
                if (storage.l.get("bannerAct") === "true") {
                  sinclo.operatorInfo.clickBanner(true);
                }
                sinclo.operatorInfo.ev();
              }
            }
          }, 1);
        } else if (String(type) === "2") {
          console.log("SENARIO TRIGGERED!!!!!! " + scenarioId);
          if (scenarioId && !sinclo.scenarioApi.isProcessing()) {
            emit('getScenario', {"scenarioId": scenarioId});
            if (sincloInfo.widget.showTiming === 3) {
              console.log("シナリオ表示処理発動");
              // 初回オートメッセージ表示時にフラグを立てる
              sincloInfo.widgetDisplay = true;
              common.widgetHandler.show();
            }
            var flg = sinclo.widget.condifiton.get();
            if (Number(cond.widgetOpen) === 1 && String(flg) === "false") {
              console.log("シナリオ最大化処理");
              if (storage.l.get("bannerAct") === "true") {
                sinclo.operatorInfo.clickBanner(true);
              }
              sinclo.operatorInfo.ev();
            }
          }
        }
      },
      fireChatEnterEvent: function (msg) {
        $(this).trigger('chatEntered', msg);
      },
      common: {
        /**
         * @params int type 比較種別
         * @params int a 基準値
         * @params int b 比較対象
         * @return bool
         */
        numMatch: function (type, a, b) {
          switch (Number(type)) {
            case 1: // 一致
              if (Number(a) === Number(b)) return true;
              break;
            case 2: // 以上
              if (Number(a) >= Number(b)) return true;
              break;
            case 3: // 未満
              if (Number(a) < Number(b)) return true;
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
        pregMatch: function (type, a, b) {
          console.log("pregMatch type: " + type + " a: " + a + " b: " + b);
          var result = false;
          var preg = "";
          switch (Number(type)) {
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
        },
        pregContainsAndExclsion: function (typeObj, contains, exclusions, val) {
          console.log("pregContainsAndExclsion type: " + JSON.stringify(typeObj) + " contains: " + contains + " exclusions: " + exclusions + " val: " + val);
          var result = true;

          // 含む方
          // var splitedContains = contains.replace(/　/g, " ").split(" ");
          var splitedContains = [];
          contains.split('"').forEach(function (currentValue, index, array) {
            if (array.length > 1) {
              if (index !== 0 && index % 2 === 1) {
                // 偶数個：そのまま文字列で扱う
                if (currentValue !== "") {
                  splitedContains.push(currentValue);
                }
              } else {
                if (currentValue) {
                  var trimValue = currentValue.trim(),
                    splitValue = trimValue.replace(/　/g, " ").split(" ");
                  splitedContains = splitedContains.concat($.grep(splitValue, function (e) {
                    return e !== "";
                  }));
                }
              }
            } else {
              var trimValue = currentValue.trim(),
                splitValue = trimValue.replace(/　/g, " ").split(" ");
              splitedContains = splitedContains.concat($.grep(splitValue, function (e) {
                return e !== "";
              }));
            }
          });
          for (var i = 0; i < splitedContains.length; i++) {
            if (splitedContains[i] === "") {
              result = true;
              continue;
            }
            var preg = "";
            var word = "";
            switch (Number(typeObj.wordType)) {
              case 1: // 完全一致
                // アスタリスクを許容し、それ以外の文字は文字列として扱う
                word = splitedContains[i]
                  .replace(/[-\/\\^$+?.()|[\]{}]/g, '\\$&').replace(/\*/g, ".*");
                preg = new RegExp("^" + word + "$");
                result = preg.test(val);
                break;
              case 2: // 部分一致
                word = splitedContains[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                preg = new RegExp(word);
                result = preg.test(val);
                break;
            }
            if ((result && typeObj.containsType === 2)) { // いずれかを含む
              break;
            } else if ((!result && typeObj.containsType === 1)) { // すべてを含む
              break;
            }
          }

          if (!result) return false; // 含む方と含まない方はAND条件なので、ここでダメならマッチエラーを返す

          // 含まない方
          var splitedExclusions = [];
          exclusions.split('"').forEach(function (currentValue, index, array) {
            if (array.length > 1) {
              if (index !== 0 && index % 2 === 1) {
                // 偶数個：そのまま文字列で扱う
                if (currentValue !== "") {
                  splitedExclusions.push(currentValue);
                }
              } else {
                if (currentValue) {
                  var trimValue = currentValue.trim(),
                    splitValue = trimValue.replace(/　/g, " ").split(" ");
                  splitedExclusions = splitedExclusions.concat($.grep(splitValue, function (e) {
                    return e !== "";
                  }));
                }
              }
            } else {
              var trimValue = currentValue.trim(),
                splitValue = trimValue.replace(/　/g, " ").split(" ");
              splitedExclusions = splitedExclusions.concat($.grep(splitValue, function (e) {
                return e !== "";
              }));
            }
          });
          var exclusionResult = false;
          for (var i = 0; i < splitedExclusions.length; i++) {
            if (splitedExclusions[i] === "") {
              if (splitedExclusions.length > 1 && i === splitedExclusions.length - 1) {
                result = typeObj.exclusionsType === 1 ? false : true;
                break;
              }
              continue;
            } else {
              var preg = "";
              var word = "";
              switch (Number(typeObj.wordType)) {
                case 1: // 完全一致
                  word = splitedExclusions[i]
                  // アスタリスクを許容し、それ以外の文字は文字列として扱う
                    .replace(/[-\/\\^$+?.()|[\]{}]/g, '\\$&').replace(/\*/g, ".*");
                  preg = new RegExp("^" + word + "$");
                  exclusionResult = preg.test(val);
                  break;
                case 2: // 部分一致
                  word = splitedExclusions[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                  preg = new RegExp(word);
                  exclusionResult = preg.test(val);
                  break;
              }
            }

            if (!exclusionResult && typeObj.exclusionsType === 1) { // すべて含む
              // 1つでも含んでいなかったら対象外条件は成立しないので、ウィジェットは出す
              result = true;
              break;
            } else if (exclusionResult && typeObj.exclusionsType === 1 && i === splitedExclusions.length - 1) { // すべて含む
              // 最後まで含んでいる状態であれば対象外条件が成立するので、ウィジェットは出さない
              result = false;
              break;
            } else if (exclusionResult && typeObj.exclusionsType === 2) { // いずれかを含む
              // 1つでも含んでいたら対象外条件が成立するので、ウィジェットは出さない
              result = false;
              break;
            } else if (!exclusionResult && typeObj.exclusionsType === 2 && i === splitedExclusions.length - 1) { // いずれかを含む
              // 最後まで含んでいない状態であれば対象外条件は成立しないので、ウィジェットは出す
              result = true;
              break;
            }
          }

          return result;
        }
      },
      judge: {
        speechContentRegEx: [],
        stayTime: function (cond, callback) {
          if (!('stayTimeCheckType' in cond) || !('stayTimeType' in cond) || !('stayTimeRange' in cond)) return callback(true, null);
          var time = 0;
          switch (Number(cond.stayTimeType)) {
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
          if (Number(cond.stayTimeCheckType) === 1) {
            callback(false, time);
          }
          // サイト
          else {
            var term = (Number(userInfo.pageTime) - Number(userInfo.time));
            if (term <= time) {
              callback(false, (time - term));
            }
            else {
              callback(true, null);
            }
          }

        },
        stayCount: function (cond, callback) {
          if (!('visitCntCond' in cond) || !('visitCnt' in cond)) return callback(true, null);
          if (sinclo.trigger.common.numMatch(cond.visitCntCond, userInfo.getStayCount(), cond.visitCnt)) {
            callback(false, 0);
          }
          else {
            callback(true, null);
          }
        },
        page: function (cond, callback) {
          if ((!'keyword_contains' in cond || !'keyword_contains_type' in cond)
            || (!'keyword_exclusions' in cond || !'keyword_exclusions_type' in cond)
            || !('stayPageCond' in cond)
            || !('targetName' in cond)) return callback(true, null);
          var target = (Number(cond.targetName) === 1) ? common.title() : location.href;
          if (sinclo.trigger.common.pregContainsAndExclsion({
            wordType: Number(cond.stayPageCond),
            containsType: Number(cond.keyword_contains_type),
            exclusionsType: Number(cond.keyword_exclusions_type)
          }, cond.keyword_contains, cond.keyword_exclusions, target)) {
            callback(false, 0);
          }
          else {
            callback(true, null);
          }
        },
        dayTime: function (cond, callback) {
          if (!('day' in cond) || !('timeSetting' in cond)) return callback(true, null);
          if (Number(cond.timeSetting) === 1 && (!('startTime' in cond) || !('endTime' in cond))) return callback(true, null);

          // DBに保存している文字列から、JSのgetDay関数に対応する数値を返す関数
          function translateDay(str) {
            var day = {'sun': 0, 'mon': 1, 'tue': 2, 'wed': 3, 'thu': 4, 'fri': 5, 'sat': 6};
            return (str in day) ? day[str] : null;
          }

          function checkTime(time) {
            var reg = new RegExp(/^(0{0,1}[0-9]{1}|1[0-9]{1}|2[0-3]{1}):([0-5]{1}[0-9]{1})$/);
            return reg.test(time);
          }

          function makeDate(date) {
            var d = new Date(date);
            return Date.parse(d);
          }

          var d = new Date(), date, dateParse, nowDay, nextDay, keys, dayList = [];
          // 今日の曜日
          nowDay = d.getDay();
          // 明日の曜日
          nextDay = Math.abs((nowDay + 1 > 6) ? 0 : nowDay + 1);
          // 今日の日付
          date = d.getFullYear() + "/" + (d.getMonth() + 1) + "/" + d.getDate() + " ";
          dateParse = Date.parse(d);
          keys = Object.keys(cond.day);
          for (var i = 0; keys.length > i; i++) {
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
            if (day === nowDay && startDate <= dateParse && dateParse < endDate) {
              return callback(false, 0); // 即時表示
            }
            // 今日で開始前の場合
            else if (day === nowDay && startDate > dateParse && dateParse < endDate) {
              return callback(false, (startDate - dateParse)); // 開始時間に表示されるようにタイマーセット
            }
            // 次回の場合
            else if (day === nextDay) {
              var nextDate = startDate + 24 * 60 * 60 * 1000;
              return callback(false, (nextDate - dateParse)); // 開始時間に表示されるようにタイマーセット
            }
            else {
              return callback(true, null);
            }
          }
        },
        referrer: function (cond, callback) {
          if ((!'keyword_contains' in cond || !'keyword_contains_type' in cond)
            || (!'keyword_exclusions' in cond || !'keyword_exclusions_type' in cond)
            || !('referrerCond' in cond)) return callback(true, null);
          if (userInfo.referrer === "") return callback(true, null);

          if (sinclo.trigger.common.pregContainsAndExclsion({
            wordType: Number(cond.referrerCond),
            containsType: Number(cond.keyword_contains_type),
            exclusionsType: Number(cond.keyword_exclusions_type)
          }, cond.keyword_contains, cond.keyword_exclusions, userInfo.referrer)) {
            callback(false, 0);
          }
          else {
            callback(true, null);
          }
        },
        searchWord: function (cond, callback) {
          if (!('keyword' in cond) || !('searchCond' in cond)) return callback(true, null);
          if (userInfo.searchKeyword === null && Number(cond.searchCond) !== 3) return callback(true, null);
          if (userInfo.searchKeyword === null && Number(cond.searchCond) === 3) return callback(false, 0);
          if (sinclo.trigger.common.pregMatch(cond.searchCond, cond.keyword, userInfo.searchKeyword)) {
            callback(false, 0);
          }
          else {
            callback(true, null);
          }
        },
        setMatchSpeechContent: function (conditionType, id, cond, callback) {
          if ((!'keyword_contains' in cond || !'keyword_contains_type' in cond)
            || (!'keyword_exclusions' in cond || !'keyword_exclusions_type' in cond)
            || !('speechContentCond' in cond)) return false;
          this.speechContentRegEx.push({
            id: id,
            typeObj: {
              wordType: Number(cond.speechContentCond),
              containsType: Number(cond.keyword_contains_type),
              exclusionsType: Number(cond.keyword_exclusions_type)
            },
            keyword_contains: cond.keyword_contains,
            keyword_exclusions: cond.keyword_exclusions,
            delay: cond.triggerTimeSec,
            conditionType: conditionType,
            callback: callback
          });
        },
        matchAllSpeechContent: function (msg, callback) {
          // FIXME マッチした処理が２回以上の場合、チャット送信処理も２回以上処理される
          var matched = false;
          // チェック処理に入る条件（すべてAND）
          // 1. オペレータが未入室状態
          // 2. シナリオ中ではない
          // 3. シナリオの入力待ち状態ではない
          // 4. マッチ設定が存在する
          console.log("matchAllSpeechContent ::: sinclo.scenarioApi.isProcessing() : " + sinclo.scenarioApi.isProcessing() + " sinclo.scenarioApi.isWaitingInput() : " + sinclo.scenarioApi.isWaitingInput())
          if (
            (!window.sincloInfo.contract.useCogmoAttendApi && !check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false")
            && !sinclo.scenarioApi.isProcessing() && !sinclo.scenarioApi.isWaitingInput() && this.speechContentRegEx.length > 0) {
            for (var index in this.speechContentRegEx) {
              console.log(this.speechContentRegEx[index].id);
              if (sinclo.chatApi.triggeredAutoSpeechExists(this.speechContentRegEx[index].id)) {
                console.log("triggeredAutoSpeechExists. Ignored. id : " + this.speechContentRegEx[index].id);
                continue;
              }
              if (sinclo.trigger.timerTriggeredList.hasOwnProperty(this.speechContentRegEx[index].id)
                && !sinclo.trigger.timerTriggeredList[this.speechContentRegEx[index].id]) {
                console.log("timer is not triggered. Ignored. id : " + this.speechContentRegEx[index].id);
                continue;
              }
              console.log("matching judge + " + this.speechContentRegEx[index]);
              if (sinclo.trigger.common.pregContainsAndExclsion(this.speechContentRegEx[index].typeObj, this.speechContentRegEx[index].keyword_contains, this.speechContentRegEx[index].keyword_exclusions, msg)) {
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
        pageOfFirst: function (cond, callback) {
          if ((!'keyword_contains' in cond || !'keyword_contains_type' in cond)
            || (!'keyword_exclusions' in cond || !'keyword_exclusions_type' in cond)
            || !('stayPageCond' in cond)
            || !('targetName' in cond)) return callback(true, null);
          var target = (Number(cond.targetName) === 1) ? userInfo.prev[0].title : userInfo.prev[0].url;
          if (sinclo.trigger.common.pregContainsAndExclsion({
            wordType: Number(cond.stayPageCond),
            containsType: Number(cond.keyword_contains_type),
            exclusionsType: Number(cond.keyword_exclusions_type)
          }, cond.keyword_contains, cond.keyword_exclusions, target)) {
            callback(false, 0);
          }
          else {
            callback(true, null);
          }
        },
        pageOfPrevious: function (cond, callback) {
          if ((!'keyword_contains' in cond || !'keyword_contains_type' in cond)
            || (!'keyword_exclusions' in cond || !'keyword_exclusions_type' in cond)
            || !('stayPageCond' in cond)
            || !('targetName' in cond)) return callback(true, null);
          var previousLength = userInfo.prev.length - 2;
          if (previousLength < 0) {
            // 前のページ情報が存在しないため実行しない
            callback(true, null);
            return;
          }
          var target = (Number(cond.targetName) === 1) ? userInfo.prev[previousLength].title : userInfo.prev[previousLength].url;
          if (sinclo.trigger.common.pregContainsAndExclsion({
            wordType: Number(cond.stayPageCond),
            containsType: Number(cond.keyword_contains_type),
            exclusionsType: Number(cond.keyword_exclusions_type)
          }, cond.keyword_contains, cond.keyword_exclusions, target)) {
            callback(false, 0);
          }
          else {
            callback(true, null);
          }
        },
        operating_hours: function (cond, callback) {
          if (!('operatingHoursTime' in cond)) return callback(true, null);
          var check = "";
          var checkHour = "";
          var now = cond.now;
          var nowDay = cond.nowDay;
          var dateParse = cond.dateParse;
          var date = cond.date;
          var today = cond.today;
          //営業時間設定の条件が「毎日」の場合
          if (cond.type == 1) {
            var day = {0: 'sun', 1: 'mon', 2: 'tue', 3: 'wed', 4: 'thu', 5: 'fri', 6: 'sat'};
            day = day[nowDay];
            timeData = cond.everyday[day];
            publicHolidayData = cond.everyday['pub'];
          }
          //営業時間設定の条件が「平日・週末」の場合
          else {
            var day = {0: 'sun', 1: 'mon', 2: 'tue', 3: 'wed', 4: 'thu', 5: 'fri', 6: 'sat'};
            if (nowDay == 1 || nowDay == 2 || nowDay == 3 || nowDay == 4 || nowDay == 5) {
              var day = 'week';
            }
            else {
              var day = 'weekend';
            }
            timeData = cond.weekly[day];
            publicHolidayData = cond.weekly['weekpub'];
          }
          publicHoliday = cond.publicHoliday;

          //条件が営業時間内の場合
          if (cond.operatingHoursTime == 1) {
            //祝日の場合
            for (var i2 = 0; i2 < publicHoliday.length; i2++) {
              if (today == publicHoliday[i2].month + '/' + publicHoliday[i2].day) {
                check = true;
                if (publicHolidayData[0].start != "" && publicHolidayData[0].end != "") {
                  for (var i = 0; i < publicHolidayData.length; i++) {
                    //営業時間内の場合
                    if (Date.parse(new Date(date + publicHolidayData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + publicHolidayData[i].end))) {
                      checkHour = true;
                      callback(false, 0);
                      return;
                    }
                  }
                }
                //営業時間設定を「休み」に設定している場合
                else {
                  callback(true, null);
                  return;
                }
              }
            }
            if (check == true && checkHour != true) {
              callback(true, null);
              return;
            }


            //祝日ではない場合
            if (check != true) {
              //営業時間設定を「休み」に設定している場合
              if (timeData[0].start === "" && timeData[0].end === "") {
                callback(true, null);
                return;
              }
              else {
                for (var i = 0; i < timeData.length; i++) {
                  //営業時間内の場合
                  if (Date.parse(new Date(date + timeData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + timeData[i].end))) {
                    checkHour = true;
                    callback(false, 0);
                    return;
                  }
                }
                //営業時間外の場合
                if (checkHour != true) {
                  callback(true, null);
                  return;
                }
              }
            }
          }
          //条件が営業時間外の場合
          else if (cond.operatingHoursTime == 2) {
            //祝日の場合
            for (var i2 = 0; i2 < publicHoliday.length; i2++) {
              if (today == publicHoliday[i2].month + '/' + publicHoliday[i2].day) {
                check = true;
                if (publicHolidayData[0].start != "" && publicHolidayData[0].end != "") {
                  for (var i = 0; i < publicHolidayData.length; i++) {
                    //営業時間内の場合
                    if (Date.parse(new Date(date + publicHolidayData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + publicHolidayData[i].end))) {
                      checkHour = true;
                      callback(true, null);
                      return;
                    }
                  }
                }
                //営業時間設定を「休み」に設定している場合
                else {
                  callback(false, 0);
                  return;
                }
              }
            }
            if (check == true && checkHour != true) {
              callback(false, 0);
              return;
            }

            //祝日でない場合
            if (check != true) {
              //営業時間設定を「休み」に設定している場合
              if (timeData[0].start === "" && timeData[0].end === "") {
                callback(false, 0);
                return;
              }
              else {
                for (var i = 0; i < timeData.length; i++) {
                  if (Date.parse(new Date(date + timeData[i].start)) <= dateParse && dateParse < Date.parse(new Date(date + timeData[i].end))) {
                    checkHour = true;
                    callback(true, null);
                    return;
                  }
                }
                if (checkHour != true) {
                  callback(false, 0);
                  return;
                }
              }
            }
          }
        }
      }
    },
    /**
     * =================================
     * シナリオAPI
     * =================================
     */
    scenarioApi: {
      _validation: {
        "1": '.+',
        "2": '[0-9]+',
        "3": "^(([^<>()\\[\\]\\.,;:\\s@\"]+(\\.[^<>()\\[\\]\\.,;:\\s@\"]+)*)|(\".+\"))@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}])|(([a-zA-Z\\-0-9]+\\.)+[a-zA-Z]{2,}))$",
        "4": '^(0|\\+)(\\d{9,}|[\\d-]{11,})'
      },
      _validChars: {
        "1": '.+',
        "1_lf": '(.|\\r\\n|\\n|\\r)+',
        "2": '[0-9]+',
        "2_lf": '([0-9]|\\r\\n|\\n|\\r)+',
        "3": '[abcdefg.hijklmnopqrstuvwxyz!#$%&\'*/=?^_+-`{|}~0123456789)]+',
        "3_lf": '([abcdefg.hijklmnopqrstuvwxyz!#$%&\'*/=?^_+-`{|}~0123456789]|\\r\\n|\\n|\\r)+',
        "4": '([0-9]|\-)+',
        "4_lf": '([0-9]|\-|\\r\\n|\\n|\\r)+'
      },
      _lKey: {
        beforeTextareaOpened: "s_beforeTextareaOpened",
        scenarioBase: "s_currentdata",
        scenarioId: "s_id",
        processing: "s_processing",
        waitingInput: "s_waiting",
        variables: "scl_s_variables",
        messages: "s_messages",
        allowSave: "s_allowSave",
        scenarios: "s_scenarios",
        scenarioLength: "s_scenarioLength",
        currentScenario: "s_currentScenario",
        currentScenarioSeqNum: "s_currentScenarioSeqNum",
        storedVariableKeys: "s_storedVariableKeys",
        sendCustomerMessageType: "s_sendCustomerMessageType",
        showSequenceSet: "s_showSequenceList",
        scenarioMessageType: "s_scenarioMessageType",
        previousChatMessageLength: "s_prevChatMessageLength",
        stackReturnSettings: "s_stackReturnSettings",
        isSentMail: "s_isSentMail"
      },
      defaultVal: {
        "s_id": 0,
        "s_currentdata": {},
        "s_processing": {},
        "s_waiting": false,
        "s_messages": [],
        "s_allowSave": false,
        "s_scenarios": {},
        "s_scenarioLength": 0,
        "s_currentScenario": 0,
        "s_currentScenarioSeqNum": 0,
        "s_storedVariableKeys": [],
        "s_sendCustomerMessageType": 1,
        "s_showSequenceList": {},
        "s_scenarioMessageType": 3,
        "s_stackReturnSettings": {},
        "s_isSentMail": false
      },
      _isReload: false,
      _events: {
        inputCompleted: "sinclo:scenario:inputComplete",
        fileUploaded: "sinclo:scenario:fileUploaded"
      },
      _actionType: {
        speakText: "1",
        hearing: "2",
        selection: "3",
        mail: "4",
        anotherScenario: "5",
        callExternalApi: "6",
        receiveFile: "7",
        getAttributeValue: "8",
        sendFile: "9",
        branchOnCond: "10",
        addCustomerInformation: "11",
        bulkHearing: "12"
      },
      set: function (key, data) {
        var self = sinclo.scenarioApi;
        var obj = {};
        if(key === self._lKey.variables) {
          storage.l.set(self._lKey.variables, JSON.stringify(data));
        } else {
          obj = self._getBaseObj();
          obj[key] = data;
          self._setBaseObj(obj);
        }
      },
      get: function (key) {
        var self = sinclo.scenarioApi;
        var obj = {};
        if(key === self._lKey.variables) {
          obj = {};
          obj = storage.l.get(self._lKey.variables) ? storage.l.get(self._lKey.variables) : obj;
          if(obj && typeof(obj) === 'string') {
            return JSON.parse(obj);
          } else {
            return obj;
          }
        } else {
          obj = self._getBaseObj();
          return obj[key] ? obj[key] : self.defaultVal[key];
        }
      },
      unset: function (key) {
        var self = sinclo.scenarioApi;
        var obj = self._getBaseObj();
        delete obj[key];
        self._setBaseObj(obj);
      },
      unsetSequenceList: function (sequenceNum) {
        var self = sinclo.scenarioApi;
        var obj = self._getBaseObj();
        delete obj[self._lKey.showSequenceSet][sequenceNum];
        self._setBaseObj(obj);
      },
      reset: function () {
        var self = sinclo.scenarioApi;
        self._unsetBaseObj();
      },
      exists: function () {
        var self = sinclo.scenarioApi;
        var obj = self._getBaseObj();
        return Object.keys(obj).length !== 0;
      },
      init: function (id, scenarioObj) {
        var self = sinclo.scenarioApi;
        self._resetDefaultVal();
        if(self.isProcessing()) {
          self._isReload = true;
        } else {
          self._unsetUploadedFileData();
          self._setBaseObj({});
          self.set(self._lKey.beforeTextareaOpened, storage.l.get('textareaOpend'));
          self.set(self._lKey.scenarioId, id);
          self.set(self._lKey.scenarios, scenarioObj);
          self.set(self._lKey.scenarioLength, Object.keys(scenarioObj).length);
          self.set(self._lKey.currentScenario, scenarioObj["0"]);
          self.set(self._lKey.currentScenarioSeqNum, 0);
          self.set(self._lKey.storedVariableKeys, []);
          self.set(self._lKey.sendCustomerMessageType, 1);
          self.set(self._lKey.allowSave, self._getChatSaveFlg());
          self.set(self._lKey.showSequenceSet, {});
          self.set(self._lKey.previousChatMessageLength, 0);
          self.set(self._lKey.stackReturnSettings, {});
          self.set(self._lKey.isSentMail, false);
          console.log("〜〜〜〜〜〜〜〜〜〜 SET SCENARIO 〜〜〜〜〜〜〜〜〜〜");
          console.log("self.set(self._lKey.scenarioId " + id);
          console.log("self.set(self._lKey.scenarios " + JSON.stringify(scenarioObj));
          console.log("self.set(self._lKey.scenarioLength " + Object.keys(scenarioObj).length);
          console.log("self.set(self._lKey.currentScenario " + JSON.stringify(scenarioObj["0"]));
          console.log("self.set(self._lKey.currentScenarioSeqNum " + 0);
          console.log("self.set(self._lKey.storedVariableKeys " + []);
          console.log("self.set(self._lKey.sendCustomerMessageType " + 1);
          console.log("self.set(self._lKey.allowSave " + self._getChatSaveFlg());
          console.log("self.set(self._lKey.showSequenceSet " + {});
          console.log("self.set(self._lKey.previousChatMessageLength " + 0);
        }
      },
      _getChatSaveFlg: function () {
        return sinclo.chatApi.saveFlg;
      },
      _resetDefaultVal: function () {
        var self = sinclo.scenarioApi;
        self.defaultVal = {
          "s_id": 0,
          "s_currentdata": {},
          "s_processing": {},
          "s_waiting": false,
          "s_variables": {},
          "s_messages": [],
          "s_allowSave": self._getChatSaveFlg(),
          "s_scenarios": {},
          "s_scenarioLength": 0,
          "s_currentScenario": 0,
          "s_currentScenarioSeqNum": 0,
          "s_storedVariableKeys": [],
          "s_sendCustomerMessageType": 1,
          "s_showSequenceList": {},
          "s_scenarioMessageType": 3,
          "s_stackReturnSettings": {},
          "s_isSentMail": false
        };
      },
      begin: function () {
        this.addStorageUpdateEvent();
        this._disablePreviousRadioButton();
        this._saveProcessingState(true);
        this._process();
        this._isReload = false;
      },
      _end: function () {
        // シナリオ終了
        console.log('シナリオ終了時にそもそもウェイトアニメーションを出さない');
        common.chatBotTypingTimerClear();
        var self = sinclo.scenarioApi;
        var beforeTextareaOpened = self.get(self._lKey.beforeTextareaOpened);
        // 元のメッセージ入力欄に戻す
        sinclo.chatApi.hideMiniMessageArea();
        self._saveProcessingState(false);
        sinclo.chatApi.removeAllEvent();
        sinclo.chatApi.initEvent();
        var type = (beforeTextareaOpened === "close") ? "2" : "1";
        self._handleChatTextArea(type);

        self._resetDefaultVal();
        self._enablePreviousRadioButton();
        self._unsetBaseObj();
        self._unsetUploadedFileData();
        self.setPlaceholderMessage(self.getPlaceholderMessage());
      },
      isProcessing: function () {
        var self = sinclo.scenarioApi;
        var result = false;
        var value = self.get(self._lKey.processing);
        if (value !== null && (value === "true" || value === true)) {
          result = true;
        }
        console.log("scenarioApi::isProcessing => " + result);
        return result;
      },
      isWaitingInput: function () {
        var self = sinclo.scenarioApi;
        var result = false;
        var value = this.get(self._lKey.waitingInput);
        if (value !== null && (value === "true" || value === true)) {
          result = true;
        }
        return result;
      },
      isScenarioLFDisabled: function () {
        var self = sinclo.scenarioApi;
        return self.isProcessing()
          && self._hearing.isHearingMode()
          && self._hearing.isLFModeDisabled();
      },
      /**
       * 入力がされたことを通知する
       * @param text 入力時のテキスト
       */
      triggerInputWaitComplete: function (text) {
        $(document).trigger(this._events.inputCompleted, [text]);
      },
      /**
       * 現在実行されているシナリオのメッセージ種別を返却する
       * @returns {Number} メッセージ種別
       */
      geScenarioMessageType: function () {
        var self = sinclo.scenarioApi;
        return self.get(this._lKey.sendCustomerMessageType);
      },
      getPlaceholderMessage: function () {
        var self = sinclo.scenarioApi;
        var msg = "";
        if (self._hearing.isHearingMode()) {
          var currentSeq = self._hearing._getCurrentHearingProcess();
          switch (currentSeq.inputLFType) {
            case "1": // 改行不可
              msg = "メッセージを入力してください";
              break;
            case "2":
              msg = "メッセージを入力してください";
              if (check.smartphone()) {
                // 何も追加しない
              } else {
                msg += "\n（Enterで改行/Shift+Enterで送信）"
              }
              break;
          }
        }
        return msg;
      },
      setPlaceholderMessage: function (msg) {
        // オペレータ入室中は変更しない
        if (msg !== "") {
          sinclo.chatApi.setPlaceholderMessage(msg);
        }
      },
      /**
       * 現在実行されているシナリオに対してサイト訪問者が入力（返答）した場合のメッセージ種別を返却する
       * @returns {*}
       */
      getCustomerMessageType: function () {
        var self = sinclo.scenarioApi;
        return self.get(this._lKey.sendCustomerMessageType);
      },
      getInputType: function () {
        var self = sinclo.scenarioApi;
        return self._hearing.getInputType();
      },
      /**
       * ローカルに保存した表示済みシナリオメッセージを取得する
       * @returns {Array}
       */
      getStoredMessage: function () {
        var self = sinclo.scenarioApi;
        var json = self.get(self._lKey.messages);
        return json ? json : [];
      },
      /**
       * シナリオ設定関連で一元管理しているオブジェクトを取得する
       * @returns {{}}
       * @private
       */
      _getBaseObj: function () {
        var self = sinclo.scenarioApi;
        var json = storage.l.get(self._lKey.scenarioBase);
        return json ? JSON.parse(json) : {};
      },
      _setBaseObj: function (obj) {
        var self = sinclo.scenarioApi;
        storage.l.set(self._lKey.scenarioBase, JSON.stringify(obj));
      },
      _unsetBaseObj: function () {
        var self = sinclo.scenarioApi;
        storage.l.unset(self._lKey.scenarioBase);
      },
      _unsetUploadedFileData: function () {
        var self = sinclo.scenarioApi;
        var data = self.get(self._lKey.variables);
        if(check.isset(data) && check.isset(data[self._sendFile._downloadUrlKey])) {
          delete data[self._sendFile._downloadUrlKey];
          self.set(self._lKey.variables, data);
        }
      },
      /**
       * 表示したシナリオメッセージをローカルに保存する
       * @param messageObj
       * @private
       */
      _saveMessage: function (messageObj) {
        var self = sinclo.scenarioApi;
        var array = self.getStoredMessage();
        array.push(messageObj);
        self.set(self._lKey.messages, array);
      },
      _disablePreviousRadioButton: function () {
        var self = sinclo.scenarioApi;
        var chatMessageBlock = $('sinclo-chat').find('div');
        var length = self.get(self._lKey.previousChatMessageLength);
        if (!self.isProcessing()) {
          // 初期状態
          length = chatMessageBlock.length;
          self.set(self._lKey.previousChatMessageLength, length);
        }
        console.log("current length: " + length);
        for (var i = 0; i < length; i++) {
          var name = $(chatMessageBlock[i]).find('[type="radio"]').attr('name');
          $('input[name=' + name + '][type="radio"]').prop('disabled', true).parent().css('opacity', 0.5);
        }
      },
      _enablePreviousRadioButton: function (length) {
        var self = sinclo.scenarioApi;
        var chatMessageBlock = $('sinclo-chat').find('div:not(.sinclo-scenario-msg)');
        console.log(length);
        if(typeof length === "undefined"){
          length = self.get(self._lKey.previousChatMessageLength);
        }
        for (var i = 0; i < length; i++) {
          var name = $(chatMessageBlock[i]).find('[type="radio"]').attr('name');
          $('input[name=' + name + '][type="radio"]').prop('disabled', false).parent().css('opacity', 1);
        }
      },
      /**
       * 現在セットされているシナリオを実行する
       * @param forceFirst シナリオ内に複数の分岐のあるものの場合、一番最初から実行する
       * @private
       */
      _process: function (forceFirst) {
        var self = sinclo.scenarioApi;
        switch (String(self.get(self._lKey.currentScenario).actionType)) {
          case self._actionType.speakText:
            self._speakText();
            self.set(self._lKey.scenarioMessageType, 21);
            break;
          case self._actionType.hearing:
            self._hearing._init(self, self.get(self._lKey.currentScenario));
            self._hearing._process(forceFirst);
            self.set(self._lKey.sendCustomerMessageType, 12);
            self.set(self._lKey.scenarioMessageType, 22);
            break;
          case self._actionType.selection:
            self._selection._init(self, self.get(self._lKey.currentScenario));
            self._selection._process();
            self.set(self._lKey.sendCustomerMessageType, 13);
            self.set(self._lKey.scenarioMessageType, 23);
            break;
          case self._actionType.mail:
            self._mail._init(self, self.get(self._lKey.currentScenario));
            self._mail._process();
            break;
          case self._actionType.anotherScenario:
            self._anotherScenario._init(self);
            self._anotherScenario._process();
            break;
          case self._actionType.callExternalApi:
            self._callExternalApi._init(self);
            self._callExternalApi._process();
            break;
          //ファイルをお客様が受信
          case self._actionType.receiveFile:
            self._receiveFile._init(self);
            self._receiveFile._process();
            break;
          case self._actionType.getAttributeValue:
            self._getAttributeValue._init(self);
            self._getAttributeValue._process();
            break;
          //ファイルをお客様が送信
          case self._actionType.sendFile:
            self._sendFile._init(self);
            self._sendFile._process();
            break;
          case self._actionType.branchOnCond:
            self.set(self._lKey.scenarioMessageType, 21); // テキスト発言として扱う
            self._branchOnCond._init(self);
            self._branchOnCond._process();
            break;
          case self._actionType.addCustomerInformation:
            self._addCustomerInformation._init(self);
            self._addCustomerInformation._process();
            break;
          case self._actionType.bulkHearing:
            self._bulkHearing._init(self);
            self._bulkHearing._process();
            self.set(self._lKey.sendCustomerMessageType, 30);
            break;
        }
      },
      addStorageUpdateEvent: function() {
        var self = sinclo.scenarioApi;
        window.addEventListener('storage', self._handleStorageUpdateEvent);
      },
      removeStorageUpdateEvent: function() {
        var self = sinclo.scenarioApi;
        window.removeEventListener('storage', self._handleStorageUpdateEvent);
      },
      _handleStorageUpdateEvent: function(event) {
        if(document.hasFocus()){
          return;
        }
        var self = sinclo.scenarioApi;
        console.log(event);
        if(event.key === self._lKey.scenarioBase) {
          if(check.isset(event.oldValue)){
            var oldObj = JSON.parse(event.oldValue);
          } else {
            var oldObj = null;
          }
          if(check.isset(event.newValue)){
            var newObj = JSON.parse(event.newValue);
          } else {
            var newObj = null;
          }
          if(self.isProcessing() && (!oldObj && newObj)
            || (
              oldObj && newObj
              && check.isset(oldObj[self._lKey.currentScenario])
              && check.isset(newObj[self._lKey.currentScenario])
              && JSON.stringify(oldObj[self._lKey.currentScenarioSeqNum]) !== JSON.stringify(newObj[self._lKey.currentScenarioSeqNum])
            )
          ) {
            console.log("<><><><><><><><><><> sequence moved %s => %s <><><><><><><><><><>", oldObj[self._lKey.currentScenarioSeqNum], newObj[self._lKey.currentScenarioSeqNum]);
            setTimeout(function(){
              var action = self.get(self._lKey.currentScenario);
              if(String(action.actionType) === self._actionType.hearing
                || String(action.actionType) === self._actionType.selection
                || String(action.actionType) === self._actionType.bulkHearing
                || String(action.actionType) === self._actionType.sendFile) {
                console.log("<><><><><><><><><><> process %s <><><><><><><><><><>", String(action.actionType));
                self.begin();
              } else {
                console.log("<><><><><><><><><><> NOT process %s <><><><><><><><><><>", String(action.actionType));
                self._handleChatTextArea(self.get(self._lKey.currentScenario).chatTextArea);
              }
            }, 100);
          } else if(self.isProcessing() && (String(self.get(self._lKey.currentScenario).actionType) === self._actionType.hearing) ) {
            setTimeout(function(){
              console.log('ヒアリング中');
              self._hearing._beginValidInputWatcher();
              if(newObj['sh_currentSeq'] !== newObj['sh_length'] && oldObj['sh_currentSeq'] !== newObj['sh_currentSeq']) {
                console.log('hearing sequence num is changed');
                var hearingProcess = self._hearing._getCurrentHearingProcess();
                if(self._hearing._isTheEnd()) {
                  self._hearing._executeConfirm(true);
                } else {
                  self._hearing._execute(hearingProcess, true);
                }
              }
            }, self._getIntervalTimeSec() * 1000);
          } else if((oldObj && newObj && oldObj[self._lKey.currentScenario] && newObj[self._lKey.currentScenario]) && ((oldObj[self._lKey.currentScenario]).actionType === self._actionType.hearing) && ((newObj[self._lKey.currentScenario]).actionType !== self._actionType.hearing)) {
            setTimeout(function(){
              console.log('ヒアリング終了時');
              self._hearing._endValidInputWatcher();
            }, self._getIntervalTimeSec() * 1000);
          } else if(oldObj && !newObj){
            console.log('シナリオ終了時');
            var length = oldObj['s_prevChatMessageLength'];
            self._enablePreviousRadioButton(length);
            self._hearing._endValidInputWatcher();
            var beforeTextareaOpened = oldObj['s_beforeTextareaOpened'];
            var type = (beforeTextareaOpened === "close") ? "2" : "1";
            self._handleChatTextArea(type);
          } else if(typeof(storage.l.get('sinclo_disable_radio')) === "string"){
            console.log("ラジオボタン非活性化");
            $('input[name=' + storage.l.get('sinclo_disable_radio') + '][type="radio"]').prop('disabled', true).parent().css('opacity', 0.5);
            storage.l.unset('sinclo_disable_radio');
          }
        }
      },
      _scenarioSeqIsUpdated: false,
      _isTheFiestScenaroAndSequence: function () {
        var self = sinclo.scenarioApi;
        var result = false;
        // 現在の実行シナリオが「テキスト発言」「選択肢」「メール送信」であればシナリオのシーケンス番号だけを見る
        if (self.get(self._lKey.currentScenario).actionType === self._actionType.speakText
          || self.get(self._lKey.currentScenario).actionType === self._actionType.selection
          || self.get(self._lKey.currentScenario).actionType === self._actionType.mail) {
          result = self.get(self._lKey.currentScenarioSeqNum) === 0;
        } else if (self.get(self._lKey.currentScenario).actionType === self._actionType.hearing) {
          // ヒアリングの場合は一番最初の問いかけかも見る
          result = self.get(self._lKey.currentScenarioSeqNum) === 0 && self._hearing._isTheFirst();
        }
        return result;
      },
      _goToNextScenario: function (isJumpScenario) {
        var self = sinclo.scenarioApi;
        if (!isJumpScenario && Number(self.get(self._lKey.currentScenarioSeqNum)) === Number(self.get(self._lKey.scenarioLength)) - 1) {
          self._end();
          return false;
        }
        if (!isJumpScenario) {
          self.set(self._lKey.currentScenarioSeqNum, Number(self.get(self._lKey.currentScenarioSeqNum)) + 1);
        }
        self.set(self._lKey.currentScenario, self.get(self._lKey.scenarios)[String(self.get(self._lKey.currentScenarioSeqNum))]);
        return true;
      },
      _handleChatTextArea: function (type) {
        switch (type) {
          case "1":
            sinclo.displayTextarea();
            storage.l.set('textareaOpend', 'open');
            break;
          case "2":
            sinclo.hideTextarea();
            storage.l.set('textareaOpend', 'close');
            break;
        }
      },
      _showMessage: function(type, message, categoryNum, showTextArea, callback) {
        console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>_showMessage:type ' + type);
        var self = sinclo.scenarioApi;
        message = self._replaceVariable(message);
        if (!self._isShownMessage(self.get(self._lKey.currentScenarioSeqNum), categoryNum)) {
          var name = (sincloInfo.widget.showAutomessageName === 2 ? "" : sincloInfo.widget.subTitle);
          if(type != self._actionType.hearing && type != self._actionType.selection && type != self._actionType.sendFile){
            common.chatBotTypingCall({forceWaitAnimation:true});
          }
          if(String(categoryNum).indexOf("delete_") >= 0) {
            sinclo.chatApi.createMessageUnread({cn: 'sinclo_re' + categoryNum, message: message, name: name, chatId: 0}, true);
          } else {
            sinclo.chatApi.createMessageUnread({cn: 'sinclo_re', message: message, name: name, chatId: 0}, true);
          }
          self._saveShownMessage(self.get(self._lKey.currentScenarioSeqNum), categoryNum);
          sinclo.chatApi.scDown();
          // ローカルに蓄積しておく
          self._putScenarioMessage(type, message, categoryNum, showTextArea, callback);
        } else {
          callback();
        }
      },
      _showPullDown: function(params, callback) {
        console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> SHOW PULLDOWN <<<<<<<<<<<<<<<<<<<<<<<<<<<');
        var self = sinclo.scenarioApi;
        params.message = self._replaceVariable(params.message);
        if (!self._isShownMessage(self.get(self._lKey.currentScenarioSeqNum), params.categoryNum)) {
          var name = (sincloInfo.widget.showAutomessageName === 2 ? "" : sincloInfo.widget.subTitle);

          sinclo.chatApi.addPulldown('sinclo_re', params.message, name, params.settings);
          self._saveShownMessage(self.get(self._lKey.currentScenarioSeqNum), params.categoryNum);
          sinclo.chatApi.scDown();
          // ローカルに蓄積しておく
          self._putHearingPulldown(params, callback);
        } else {
          callback();
        }
      },
      _showCalendar: function (params, callback) {
        console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> SHOW CALENDAR <<<<<<<<<<<<<<<<<<<<<<<<<<<');
        var self = sinclo.scenarioApi;
        params.message = self._replaceVariable(params.message);
        if (!self._isShownMessage(self.get(self._lKey.currentScenarioSeqNum), params.categoryNum)) {
          sinclo.chatApi.addCalendar('sinclo_re', params.message, params.settings);
          self._saveShownMessage(self.get(self._lKey.currentScenarioSeqNum), params.categoryNum);
          sinclo.chatApi.scDown();
          // ローカルに蓄積しておく
          // self._putHearingPulldown(params, callback);
        } else {
          callback();
        }
      },
      _showFileTypeMessage: function (type, resultDataSet, categoryNum, showTextArea, callback) {
        var self = sinclo.scenarioApi;
        resultDataSet.message = self._replaceVariable(resultDataSet.message);
        if (!self._isShownMessage(self.get(self._lKey.currentScenarioSeqNum), categoryNum)) {
          sinclo.chatApi.unread++;
          sinclo.chatApi.showUnreadCnt();
          sinclo.chatApi.createSendFileMessage(resultDataSet, "auto");
          self._saveShownMessage(self.get(self._lKey.currentScenarioSeqNum), categoryNum);
          sinclo.chatApi.scDown();
          // ローカルに蓄積しておく
          self._putScenarioMessage(type, JSON.stringify(resultDataSet), categoryNum, showTextArea, callback);
        } else {
          callback();
        }
      },
      _saveShownMessage: function (scenarioSeqNum, categoryNum) {
        var self = sinclo.scenarioApi;
        var data = self.get(self._lKey.showSequenceSet),
          data = data ? data : {};
        var arr = data[scenarioSeqNum] ? data[scenarioSeqNum] : [];
        arr.push(categoryNum);
        data[scenarioSeqNum] = arr;
        self.set(self._lKey.showSequenceSet, data);
      },
      _deleteShownMessage: function (scenarioSeqNum, categoryNum) {
        var self = sinclo.scenarioApi;
        var data = self.get(self._lKey.showSequenceSet),
          data = data ? data : {};
        var arr = data[scenarioSeqNum] ? data[scenarioSeqNum] : [];
        delete arr[arr.indexOf(categoryNum)];
        data[scenarioSeqNum] = arr;
        self.set(self._lKey.showSequenceSet, data);
      },
      _isShownMessage: function (scenarioSeqNum, categoryNum) {
        var self = sinclo.scenarioApi;
        var data = self.get(self._lKey.showSequenceSet);
        var arr = data[scenarioSeqNum] ? data[scenarioSeqNum] : [];
        return arr.indexOf(categoryNum) !== -1;
      },
      /**
       * シナリオメッセージをDBに格納する
       * @param array 配列で格納したメッセージ
       * @property {number} type - シナリオメッセージのタイプ
       * @property {string} message - シナリオメッセージ
       * @param {ƒgetfunction} callback - メッセージをDBに格納した後のコールバック関数
       * @private
       */
      _storeMessageToDB: function (array, callback) {
        var self = sinclo.scenarioApi;
        if (!callback) callback = function () {
        };
        emit('storeScenarioMessage', {messages: array}, callback);
      },
      _saveProcessingState: function (isProcessing) {
        var self = sinclo.scenarioApi;
        self.set(self._lKey.processing, isProcessing);
      },
      _saveWaitingInputState: function (isWaitingInput) {
        var self = sinclo.scenarioApi;
        self.set(self._lKey.waitingInput, isWaitingInput);
      },
      _putScenarioMessage: function (type, message, categoryNum, showTextArea, callback) {
        var self = sinclo.scenarioApi,
          storeObj = {
            scenarioId: self.get(self._lKey.scenarioId),
            type: type,
            messageType: self.get(self._lKey.scenarioMessageType),
            sequenceNum: self.get(self._lKey.currentScenarioSeqNum),
            requireCv: self._bulkHearing.isInMode() || (type === self._actionType.hearing && self._hearing._cvIsEnable()),
            categoryNum: categoryNum,
            showTextarea: showTextArea,
            message: message
          };
        if (self._disallowSaveing()) {
          self._pushScenarioMessage(storeObj, function (data) {
            self._saveMessage(data.data);
            callback();
          });
        } else {
          self._storeMessageToDB([storeObj], callback);
        }
      },
      _putHearingPulldown: function (data, callback) {
        var pulldownData = {
          message: data.message,
          settings: {
            options: data.settings.options,
            customDesign: data.settings.customDesign
          }
        };
        var myData = JSON.stringify(pulldownData);
        console.log('TTTTTTTTTTTTTTTTTTTTTTTTTTTTT' + myData);
        var self = sinclo.scenarioApi,
          storeObj = {
            scenarioId: self.get(self._lKey.scenarioId),
            type: data.type,
            uiType: data.uiType,
            messageType: self.get(self._lKey.scenarioMessageType),
            sequenceNum: self.get(self._lKey.currentScenarioSeqNum),
            requireCv: self._bulkHearing.isInMode() || (data.type === self._actionType.hearing && self._hearing._cvIsEnable()),
            categoryNum: data.categoryNum,
            showTextarea: data.showTextArea,
            // message: data.message
            message: myData
          };
        if (self._disallowSaveing()) {
          self._pushScenarioMessage(storeObj, function (item) {
            self._saveMessage(item.data);
            callback();
          });
        } else {
          self._storeMessageToDB([storeObj], callback);
        }
      },
      _handleStoredMessage: function () {
        var self = sinclo.scenarioApi;
        if (self._disallowSaveing()) {
          self._saveStoredMessage(function () {
            self._unsetScenarioMessage();
          });
        }
      },
      _pushScenarioMessage: function (targetObj, callback) {
        emit('sendScenarioMessage', targetObj, callback);
      },
      _saveStoredMessage: function (callback) {
        var self = sinclo.scenarioApi;
        var json = self.get(self._lKey.messages);
        var array = json ? json : [];
        self._storeMessageToDB(array, callback);
      },
      _unsetScenarioMessage: function () {
        var self = sinclo.scenarioApi;
        self.unset(self._lKey.messages);
        self.set(self._lKey.allowSave, true);
      },
      _disallowSaveing: function () {
        var self = sinclo.scenarioApi;
        var flg = self.get(self._lKey.allowSave);
        return flg == null || flg === "false" || flg === false;
      },
      _saveVariable: function (valKey, value) {
        var self = sinclo.scenarioApi;
        // FIXME JSONで突っ込む
        var json = self.get(self._lKey.variables);
        var obj = json;
        obj[valKey] = {};
        obj[valKey].value = value;
        obj[valKey].created = (new Date()).getTime();
        obj[valKey].scId = self.get(self._lKey.scenarioId);
        self.set(self._lKey.variables, obj);
        // メール送信シナリオで利用するためシナリオで保存した変数は配列で保持する
        if (self.get(self._lKey.storedVariableKeys) && self.get(self._lKey.storedVariableKeys).indexOf(valKey) === -1) {
          var arr = self.get(self._lKey.storedVariableKeys);
          arr.push(valKey);
          self.set(self._lKey.storedVariableKeys, arr);
        } else if (!self.get(self._lKey.storedVariableKeys)) {
          self.set(self._lKey.storedVariableKeys, [valKey]);
        }
      },
      _getStoredVariable: function (valKey) {
        var self = sinclo.scenarioApi;
        if(self.get(self._lKey.storedVariableKeys).indexOf(valKey) !== -1) {
          return self._getSavedVariable(valKey);
        } else {
          return valKey;
        }
      },
      _getSavedVariable: function (valKey) {
        var self = sinclo.scenarioApi;
        // FIXME JSONで突っ込む
        var obj = self.get(self._lKey.variables);
        if (!obj) obj = {};
        return (obj[valKey] && obj[valKey].value) ? obj[valKey].value : "";
      },
      _getAllTargetVariables: function () {
        var self = sinclo.scenarioApi;
        var resultSet = {};
        self.get(self._lKey.storedVariableKeys).forEach(function (elm, index, array) {
          if (elm === self._sendFile._downloadUrlKey) {
            // いったん取り出す
            var sendFileArray = JSON.parse(self._getSavedVariable(elm));
            var targetFileArray = [];
            for (var i = 0; i < sendFileArray.length; i++) {
              if (!sendFileArray[i].sent) {
                targetFileArray.push(sendFileArray[i]);
              }
              resultSet[elm] = JSON.stringify(targetFileArray);
            }
          } else {
            resultSet[elm] = self._getSavedVariable(elm);
          }
        });
        return resultSet;
      },
      _getMessage: function () {
        var self = sinclo.scenarioApi;
        return self.get(self._lKey.currentScenario).message;
      },
      _replaceVariable: function (message) {
        var self = sinclo.scenarioApi;
        if (message) {
          return message.replace(/\{\{(.+?)\}\}/g, function (param) {
            var name = param.replace(/^\{\{(.+)\}\}$/, '$1');
            return self._getStoredVariable(name) || name;
          });
        } else {
          return "";
        }
      },
      _getIntervalTimeSec: function () {
        var self = sinclo.scenarioApi;
        return Number(self.get(self._lKey.currentScenario).messageIntervalTimeSec);
      },
      _doing: function (intervalSec, callFunction) {
        var self = sinclo.scenarioApi;
        if(self._isTheFiestScenaroAndSequence()) {
          // 一番最初のシナリオ開始は即時実行
          callFunction();
        } else {
          setTimeout(callFunction, intervalSec * 1000);
        }
      },
      _valid: function (typeStr, val) {
        var self = sinclo.scenarioApi;
        var regex = new RegExp(self._validation[Number(typeStr)]);
        return regex.test(val);
      },
      _speakText: function () {
        // クロージャー用
        var self = sinclo.scenarioApi;
        this._doing(self._getIntervalTimeSec(), function () {
          self._handleChatTextArea(self.get(self._lKey.currentScenario).chatTextArea);
          self._showMessage(self.get(self._lKey.currentScenario).actionType, self._getMessage(), 0, self.get(self._lKey.currentScenario).chatTextArea, function () {
            if (self._goToNextScenario()) {
              self._process();
            }
          });
        });
      },
      _createSelectionMessage: function (headerMessage, selections) {
        var self = sinclo.scenarioApi;
        var messageBlock = self._replaceVariable(headerMessage) + "\n";
        selections.forEach(function (elm, index, arr) {
          messageBlock += "[] " + elm;
          if (index !== arr.length - 1) {
            messageBlock += "\n";
          }
        });
        return messageBlock;
      },
      _waitingInput: function (callback) {
        var self = sinclo.scenarioApi;
        self._unWaitingInput();
        $(document).on(self._events.inputCompleted, function (e, inputVal) {
          callback(inputVal);
        });
        self._saveWaitingInputState(true);
      },
      _unWaitingInput: function () {
        var self = sinclo.scenarioApi;
        $(document).off(self._events.inputCompleted);
        self._saveWaitingInputState(false);
      },
      _mergeScenario: function (result, executableNextAction) {
        var targetScenario = result.activity.scenarios;
        var self = sinclo.scenarioApi;
        var scenarioObj = self.get(self._lKey.scenarios);
        var scenarioSeqNum = self.get(self._lKey.currentScenarioSeqNum);
        var newScenarioObj = {};
        var executeNextAction = executableNextAction;
        var currentIndex = 0;
        Object.keys(scenarioObj).some(function (elm, index) {
          if (index === scenarioSeqNum) {
            Object.keys(targetScenario).forEach(function (elm, index, arr) {
              newScenarioObj[String(currentIndex)] = targetScenario[elm];
              currentIndex++;
            });
            self._saveReturnSettings(currentIndex - 1, executeNextAction, Object.keys(targetScenario).length);
            if (!executeNextAction) {
              return true;
            }
          } else {
            newScenarioObj[String(currentIndex)] = scenarioObj[elm];
            currentIndex++;
          }
        });
        console.dir(newScenarioObj);
        self.set(self._lKey.scenarios, newScenarioObj);
        self.set(self._lKey.scenarioLength, Object.keys(newScenarioObj).length);
        var isSentMail = self.get(self._lKey.isSentMail);
        if(isSentMail || isSentMail === "true") {
          // 別のシナリオを呼び出す時、既にメールを送っている状態であればダウンロード用のURLを送信済みとする
          self._applyAllDataSent();
        }
      },
      _saveReturnSettings: function (lastSequenceNum, isReturn, incrementSeqVal) {
        var self = sinclo.scenarioApi;
        var savedSettings = self.get(self._lKey.stackReturnSettings);
        var newSettings = {};
        Object.keys(savedSettings).forEach(function (elm, idx, key) {
          newSettings[Number(elm) + incrementSeqVal - 1] = savedSettings[elm];
        });
        newSettings[lastSequenceNum] = isReturn;
        self.set(self._lKey.stackReturnSettings, newSettings);
      },
      _getReturnSettingsOnCallerScenario: function (currentSequenceNum) {
        var self = sinclo.scenarioApi;
        var savedSettings = self.get(self._lKey.stackReturnSettings);
        var settings = {
          lastSequenceNum: 0,
          isReturn: false
        };
        Object.keys(savedSettings).some(function (e) {
          if (Number(e) >= currentSequenceNum) {
            settings.lastSequenceNum = Number(e);
            settings.isReturn = savedSettings[e];
            return true;
          }
        });
        return settings;
      },
      /**
       * メール送信したアップロード済み情報をフラグ付けする
       * @private
       */
      _applyAllDataSent: function () {
        var self = sinclo.scenarioApi;
        var data = self._getSavedVariable(self._sendFile._downloadUrlKey);
        var dataObj = [];
        if (check.isJSON(data)) {
          dataObj = JSON.parse(data);
        }
        for (var i = 0; i < dataObj.length; i++) {
          dataObj[i].sent = true;
        }
        self._saveVariable(self._sendFile._downloadUrlKey, JSON.stringify(dataObj));
      },
      _hearing: {
        _parent: null,
        _state: {
          currentSeq: "sh_currentSeq",
          retry: "sh_retry",
          length: "sh_length",
          confirming: "sh_comfirming"
        },
        _cvType: {
          validOnce: "1",
          validAll: "2",
          confirmOK: "3"
        },
        _inputType: {
          "1": "text",
          "2": "number",
          "3": "email",
          "4": "tel"
        },
        _watcher: null,
        isHearingMode: function () {
          var self = sinclo.scenarioApi._hearing;
          if (!self._parent) {
            // initがコールされていないのでヒアリング開始していない
            return false;
          } else {
            return String(self._parent.get(self._parent._lKey.currentScenario).actionType) === "2";
          }
        },
        isLFModeDisabled: function () {
          var self = sinclo.scenarioApi._hearing;
          if (!self._parent) {
            // initがコールされていないのでヒアリング開始していない
            return true;
          } else {
            return String(self._getCurrentHearingProcess().inputLFType) === "1"
              || String(self._getCurrentHearingProcess().uiType) === "1";
          }
        },
        getInputType: function () {
          var self = sinclo.scenarioApi._hearing,
            inputTypeStr = "1";
          if (self.isHearingMode()) {
            inputTypeStr = String(self._getCurrentHearingProcess().inputType);
          }
          return self._inputType[inputTypeStr];
        },
        _easyApi: {
          labelMap: {
            "lbc_office_id": "",
            "lbc_head_office_id": "",
            "pref_code": "都道府県コード",
            "city_code": "市区町村コード",
            "addr": "住所",
            "cname": "企業名",
            "oname": "事業所名",
            "pname": "姓名",
            "pname_kana": "姓名カナ",
            "pname_kana2": "姓名かな",
            "busho": "部署名",
            "yakushoku": "役職名",
            "zip": "郵便番号",
            "tel": "電話番号",
            "fax": "FAX番号",
            "ktai": "携帯番号",
            "chokutsu": "直通番号",
            "daihyo": "代表番号",
            "mail": "メールアドレス",
            "url": "URL",
            "extra": "その他",
            "unknown": "その他",
            "org_addr": "住所",
            "org_zip": "郵便番号",
            "exist_cname": "企業名マスタ存在",
            "exist_addr": "住所マスタ存在",
            "exist_zip": "郵便番号マスタ存在",
            "match_pref_add": "都道府県・住所一致",
            "match_pref_zip": "都道府県・郵便番号一致",
            "match_pref_tel": "都道府県・電話番号一致"
          },
          targetCondition: {
            validOnce: "1",
            validAll: "2"
          },
        },

        _init: function (parent, currentScenario) {
          this._parent = parent;
          this._setCurrentSeq(this._getCurrentSeq());
          if (this._isParseSignatureMode()) {
            this._setLength(1);
          } else {
            this._setLength(this._parent.get(this._parent._lKey.currentScenario).hearings.length);
          }
        },
        _setCurrentSeq: function (val) {
          var self = sinclo.scenarioApi._hearing;
          self._parent.set(self._state.currentSeq, val);
        },
        _getCurrentSeq: function () {
          var self = sinclo.scenarioApi._hearing;
          var json = self._parent.get(self._state.currentSeq);
          var obj = json ? json : 0;
          return obj;
        },
        _setRetryFlg: function () {
          var self = sinclo.scenarioApi._hearing;
          self._parent.set(self._state.retry, true);
        },
        _clearRetryFlg: function () {
          var self = sinclo.scenarioApi._hearing;
          self._parent.set(self._state.retry, false);
        },
        _getRetryFlg: function () {
          var self = sinclo.scenarioApi._hearing;
          var json = self._parent.get(self._state.retry);
          var obj = json ? json : false;
          console.log("scenarioApi::hearing::_getRetryFlg => " + obj);
          return obj;
        },
        _setLength: function (val) {
          var self = sinclo.scenarioApi._hearing;
          self._parent.set(self._state.length, val);
        },
        _getLength: function (val) {
          var self = sinclo.scenarioApi._hearing;
          var json = self._parent.get(self._state.length);
          var obj = json ? json : 0;
          return Number(obj);
        },
        _getValidChars: function (input) {
          var self = sinclo.scenarioApi._hearing;
          var validCharIndex = self.isLFModeDisabled() ? String(self._getCurrentHearingProcess().inputType) : String(self._getCurrentHearingProcess().inputType) + '_lf';
          var match = input.match(self._parent._validChars[validCharIndex]);
          if (match) {
            return match[0];
          } else {
            return "";
          }
        },
        _beginValidInputWatcher: function () {
          var self = sinclo.scenarioApi._hearing;
          if (sinclo.scenarioApi.isScenarioLFDisabled()) {
            sinclo.chatApi.showMiniMessageArea();
          } else {
            sinclo.chatApi.hideMiniMessageArea();
          }
          if (!check.isIE() && !self._watcher) {
            console.log("BEGIN TIMER");
            self._watcher = setInterval(function () {
              if ((!check.isset(storage.s.get('operatorEntered')) || storage.s.get('operatorEntered') === "false")) {
                if (sinclo.scenarioApi.isScenarioLFDisabled()) {
                  $('#miniSincloChatMessage').val(self._getValidChars($('#miniSincloChatMessage').val()));
                } else {
                  $('#sincloChatMessage').val(self._getValidChars($('#sincloChatMessage').val()));
                }
              }
            }, 100);
          }
        },
        _endValidInputWatcher: function () {
          var self = sinclo.scenarioApi._hearing;
          if (!check.isIE() && self._watcher) {
            console.log("END TIMER");
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ヒアリングの入力無効開始(ｽﾏﾎ)<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
            //入力待機が終わったらreadOnly属性をtrueにする
            //要素が存在するか確認してから行うこと
            if(check.smartphone()){
              var miniTextarea = document.getElementById("miniSincloChatMessage"),
                  textarea = document.getElementById("sincloChatMessage");
              if(textarea){
                console.log('>>>>>>>>>>>>>>>>>>>>>>>>>テキストエリアが無効になります<<<<<<<<<<<<<<<<<<<<<<<<');
                textarea.disabled = true;
              }
              if(miniTextarea){
                console.log('>>>>>>>>>>>>>>>>>>>>>>>>>ミニテキストエリアが無効になります<<<<<<<<<<<<<<<<<<<<<<<<');
                miniTextarea.disabled = true;
              }
            }
            clearInterval(self._watcher);
            self._watcher = null;
          }
        },
        _beginCancelHandler: function() {
          var self = sinclo.scenarioApi._hearing;
          $('#sincloBox ul#chatTalk li.sinclo_se.cancelable').on('click', self._handleCancel);
        },
        _handleCancel: function(e) {
          var self = sinclo.scenarioApi._hearing;
          var target = $(this);
          var targetChatId = target.data('chatId');
          var text = target.text();
          console.log('cancelable click %s %s', targetChatId, text);
          var deleteTargetIds = [];
          deleteTargetIds.push(targetChatId);
          target.parents('div').nextAll().each(function(index, value){
            deleteTargetIds.push($(this).find('li').data('chatId'));
            $(this).remove();
          });
          target.remove();
          self._hideMessage(deleteTargetIds);
        },
        _hideMessage: function(messageIds) {
          emit('hideScenarioMessages', {
            hideMessages: messageIds
          });
        },
        _process: function (forceFirst) {
          var self = sinclo.scenarioApi._hearing;
          if (forceFirst) {
            console.log("FORCE RESET hearing process");
            self._setCurrentSeq(0);
            self._parent.unsetSequenceList(self._parent.get(self._parent._lKey.currentScenarioSeqNum))
          }
          self._beginCancelHandler();
          var doHearing = self._getCurrentHearingProcess();
          if (self._isTheEnd()) {
            self._executeConfirm();
          } else {
            self._execute(doHearing);
          }
        },
        _execute: function (hearing, executeSilent) {
          var message = hearing.message;
          // クロージャー用
          var self = sinclo.scenarioApi._hearing;
          //リロード直後のヒアリングは即時実行される
          var intervalTimeSec = self._parent._getIntervalTimeSec();
          if(sinclo.scenarioApi._isReload){
            intervalTimeSec = 0;
            sinclo.scenarioApi._isReload = false;
          }
          self._endInputProcess();
          self._parent._doing(intervalTimeSec, function () {
            self._handleChatTextArea(self._getCurrentHearingProcess().uiType);
            self._parent.setPlaceholderMessage(self._parent.getPlaceholderMessage());
            var afterShowMessageProcess = function () {
              sinclo.chatApi.addKeyDownEventToSendChat();
              self._parent._saveWaitingInputState(true);
              self._parent._waitingInput(function (inputVal) {
                self._endInputProcess();
                self._parent._handleStoredMessage();
                if (self._parent._valid(hearing.inputType, inputVal)) {
                  self._parent._saveVariable(hearing.variableName, inputVal);
                  if (self._goToNext()) {
                    self._process();
                  } else {
                    self._executeConfirm();
                  }
                } else {
                  self._showError();
                }
              });
            };
            if(executeSilent) {
              afterShowMessageProcess();
            } else {
              self._showMessage(self._getCurrentHearingProcess().uiType, message, self._getCurrentHearingProcess().required, self._getCurrentHearingProcess().settings, afterShowMessageProcess);
            }
          });
        },
        _showMessage: function (uiType, message, required, settings, callback) {
          var self = sinclo.scenarioApi._hearing;
          switch(uiType) {
            case "1":
              self._parent._showMessage("2", message, self._getCurrentSeq(), "1", callback);
              break;
            case "2":
              self._parent._showMessage("2", message, self._getCurrentSeq(), "1", callback);
              break;
            case "3": // ラジオボタン
              message += "\n";
              settings.options.forEach(function(elm, index, arr) {
                message += "[] " + elm + "\n";
              });
              self._parent._showMessage("2", message, self._getCurrentSeq(), "2", callback);
              break;
            case "4":
              var params = {
                type: "2",
                uiType: uiType,
                message: message,
                settings: settings,
                categoryNum: self._getCurrentSeq()
              };
              self._parent._showPullDown(params, callback);
              break;
            case "5":
              var params = {
                type: "2",
                uiType: uiType,
                message: message,
                settings: settings,
                categoryNum: self._getCurrentSeq()
              };
              self._parent._showCalendar(params, callback);
              break;
            default:
              //TODO 旧IFを吸収する
          }
        },
        _handleChatTextArea: function (type) {
          var self = sinclo.scenarioApi._hearing;
          switch (type) {
            case "1":
              self._endInputProcess();
              sinclo.displayTextarea();
              self._beginValidInputWatcher();
              storage.l.set('textareaOpend', 'open');
              break;
            case "2":
              self._endInputProcess();
              sinclo.displayTextarea();
              self._beginValidInputWatcher();
              storage.l.set('textareaOpend', 'open');
              break;
            case "3": // ラジオボタン
            case "4": // プルダウン
            case "5": // カレンダー
              sinclo.hideTextarea();
              storage.l.set('textareaOpend', 'close');
              break;
            default:
              // text
              self._endInputProcess();
              sinclo.displayTextarea();
              self._beginValidInputWatcher();
              storage.l.set('textareaOpend', 'open');
              break;
          }
        },
        _endInputProcess: function () {
          var self = sinclo.scenarioApi._hearing;
          sinclo.chatApi.removeKeyDownEventToSendChat();
          self._parent._unWaitingInput();
          self._endValidInputWatcher();
        },
        _executeConfirm: function (executeSilent) {
          var self = sinclo.scenarioApi._hearing;
          //ヒアリングが終わるときはチャットエリアのreadOnlyを解除しておく
          console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ヒアリングの入力無効終了(ｽﾏﾎ)<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
          if(check.smartphone()){
            var miniTextarea = document.getElementById("miniSincloChatMessage"),
                textarea = document.getElementById("sincloChatMessage");
            if(textarea){
              textarea.disabled = false;
            }
            if(miniTextarea){
              miniTextarea.disabled = false;
            }
          }
          if (self._requireConfirm()) {
            self._saveConfirmFlg(true);
            self._showConfirmMessage(executeSilent);
          } else {
            if (self._cvIsEnable()) {
              // 全てOKの場合はCV
              setTimeout(function () {
                emit('addLastMessageToCV', {historyId: sinclo.chatApi.historyId});
              }, 1000);
            }
            if (self._parent._goToNextScenario()) {
              self._setCurrentSeq(0);
              self._parent._process();
            }
          }
        },
        _saveConfirmFlg: function (confirming) {
          var self = sinclo.scenarioApi._hearing;
          self._parent.set(self._state.confirming, confirming);
        },
        _isConfirming: function () {
          var self = sinclo.scenarioApi._hearing;
          if (!self || !self._parent) return false;
          var isConfirming = self._parent.get(self._state.confirming);
          return typeof(isConfirming) !== null && (isConfirming || isConfirming === 'true');
        },
        _requireConfirm: function () {
          var self = sinclo.scenarioApi._hearing;
          return self._parent.get(self._parent._lKey.currentScenario).isConfirm === "1";
        },
        _getCurrentHearingProcess: function () {
          var self = sinclo.scenarioApi._hearing;
          var result = {};
          if(self._parent.get(self._parent._lKey.currentScenario).hearings) {
            var triggerObj = self._parent.get(self._parent._lKey.currentScenario).hearings[self._getCurrentSeq()];
            if (typeof(triggerObj) !== 'undefined') {
              result = triggerObj;
            }
          }
          return result;
        },
        _showError: function () {
          var self = sinclo.scenarioApi._hearing;
          var errorMessage = self._parent.get(self._parent._lKey.currentScenario).errorMessage;
          self._parent._doing(self._parent._getIntervalTimeSec(), function () {
            self._parent._handleChatTextArea(self._parent.get(self._parent._lKey.currentScenario).chatTextArea);
            self._parent._showMessage(self._parent.get(self._parent._lKey.currentScenario).actionType, errorMessage, self._parent.get(self._state.currentSeq) + "e" + common.fullDateTime(), self._parent.get(self._parent._lKey.currentScenario).chatTextArea, function () {
              self._parent._deleteShownMessage(self._parent.get(self._parent._lKey.currentScenarioSeqNum), self._parent.get(self._state.currentSeq));
              self._process();
            });
          });
        },
        _goToNext: function () {
          var self = sinclo.scenarioApi._hearing;
          if (self._isTheEnd()) {
            return false;
            // 終了であればチャット送信エリアを元に戻す
            sinclo.chatApi.hideMiniMessageArea();
          }
          self._setCurrentSeq(self._getCurrentSeq() + 1);
          if (self._isTheEnd()) {
            return false;
            // 終了であればチャット送信エリアを元に戻す
            sinclo.chatApi.hideMiniMessageArea();
          } else {
            return true;
          }
        },
        _isTheEnd: function () {
          var self = sinclo.scenarioApi._hearing;
          return self._getCurrentSeq() === self._getLength();
        },
        _isTheFirst: function () {
          var self = sinclo.scenarioApi._hearing;
          return self._getCurrentSeq() === 0 && !self._getRetryFlg();
        },
        _cvTypeIs: function (type) {
          var self = sinclo.scenarioApi._hearing;
          if (!self._cvIsEnable()) return false;
          return type === String(self._parent.get(self._parent._lKey.currentScenario).cvCondition);
        },
        _cvIsEnable: function () {
          var self = sinclo.scenarioApi._hearing;
          return self._parent.get(self._parent._lKey.currentScenario).cv === "1";
        },
        _isParseSignatureMode: function () {
          var self = sinclo.scenarioApi._hearing;
          return self._parent.get(self._parent._lKey.currentScenario).parseSignatureMode;
        },
        _easyApiRequireAll: function () {
          var self = sinclo.scenarioApi._hearing;
          return self._parent.get(self._parent._lKey.currentScenario).hearingTargetCondition === self._easyApi.targetCondition.validAll;
        },
        _showConfirmMessage: function (executeSilent) {
          var self = sinclo.scenarioApi._hearing;
          var messageBlock = self._parent._createSelectionMessage(self._parent.get(self._parent._lKey.currentScenario).confirmMessage, [self._parent.get(self._parent._lKey.currentScenario).success, self._parent.get(self._parent._lKey.currentScenario).cancel]);
          var handleConfirmMessageFunc = function () {
            self._parent._waitingInput(function (inputVal) {
              self._parent._unWaitingInput();
              self._parent._handleStoredMessage();
              console.log("inputVal : " + inputVal + " self._parent._lKey.currentScenario.success : " + self._parent.get(self._parent._lKey.currentScenario).success + " self._parent._lKey.currentScenario.cancel : " + self._parent.get(self._parent._lKey.currentScenario).cancel);
              self._saveConfirmFlg(false);
              if (inputVal === self._parent.get(self._parent._lKey.currentScenario).success) {
                self._clearRetryFlg();
                if (self._cvIsEnable()) {
                  // OKを押したタイミングでCVを付ける
                  setTimeout(function () {
                    emit('addLastMessageToCV', {historyId: sinclo.chatApi.historyId});
                  }, 1000);
                }
                self._setCurrentSeq(0);
                if (self._parent._goToNextScenario()) {
                  self._parent._process();
                }
              } else if (inputVal === self._parent.get(self._parent._lKey.currentScenario).cancel) {
                self._setRetryFlg();
                self._parent._process(true);
              } else {
                self._showError();
              }
            });
          };
          self._parent._doing(self._parent._getIntervalTimeSec(), function () {
            self._parent._handleChatTextArea("2"); // 確認ダイアログを出すときはOFF固定
            if(executeSilent) {
              handleConfirmMessageFunc();
            } else {
              self._parent._showMessage(self._parent.get(self._parent._lKey.currentScenario).actionType, messageBlock, self._parent.get(self._state.currentSeq) + 1, "2", handleConfirmMessageFunc);
            }
          });
        },
        _createSignatureMessage: function (obj) {
          var self = sinclo.scenarioApi._hearing;
          var message = "";
          Object.keys(obj).forEach(function (elm, index, arr) {
            if (obj[elm] !== "") {
              if (typeof (obj[elm]) === "string") {
                message += self._easyApi.labelMap[elm] + "：" + obj[elm] + "\n";
              } else if (typeof(obj[elm]) === "object") {
                var concatStr = self._easyApi.labelMap[elm] + "：";
                for (var i = 0; i < obj[elm].length; i++) {
                  concatStr += obj[elm][i] + " ";
                }
                message += concatStr + "\n";
              }
            }
          });
          return message;
        }
      },
      _selection: {
        _parent: null,
        _init: function (parent, currentScenario) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._selection;
          var messageBlock = self._parent._createSelectionMessage(self._parent.get(self._parent._lKey.currentScenario).message, self._parent.get(self._parent._lKey.currentScenario).selection.options);
          self._parent._doing(self._parent._getIntervalTimeSec(), function () {
            self._parent._handleChatTextArea(self._parent.get(self._parent._lKey.currentScenario).chatTextArea);
            self._parent._showMessage(self._parent.get(self._parent._lKey.currentScenario).actionType, messageBlock, 0, self._parent.get(self._parent._lKey.currentScenario).chatTextArea, function () {
              self._parent._waitingInput(function (inputVal) {
                self._parent._unWaitingInput();
                self._parent._handleStoredMessage();
                self._parent._saveVariable(self._parent.get(self._parent._lKey.currentScenario).selection.variableName, inputVal);
                if (self._parent._goToNextScenario()) {
                  self._parent._process();
                }
              });
            });
          });
        }
      },
      _mail: {
        _parent: null,
        _init: function (parent, currentScenario) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._mail;
          var targetVariables = self._parent._getAllTargetVariables();
          var sendData = {
            historyId: sinclo.chatApi.historyId,
            mailType: self._parent.get(self._parent._lKey.currentScenario).mailType,
            transmissionId: self._parent.get(self._parent._lKey.currentScenario).mMailTransmissionId,
            templateId: self._parent.get(self._parent._lKey.currentScenario).mMailTemplateId,
            withDownloadURL: self._isNeedToAddDownloadURL(),
            variables: targetVariables
          };

          // 外部連携実装後に外す
          sinclo.api.callFunction('sc', self._parent.get(self._parent._lKey.scenarioId));
          // 外部連携実装後に外す
          emit('processSendMail', sendData, function(ev) {
            self._parent.set(self._parent._lKey.isSentMail, true);
          });
          if (self._parent._goToNextScenario()) {
            self._parent._process();
          }
        },
        _isNeedToAddDownloadURL: function () {
          var self = sinclo.scenarioApi._mail;
          var isNeed = self._parent.get(self._parent._lKey.currentScenario).sendWithDownloadURL;
          return (isNeed) ? isNeed : false;
        }
      },
      _anotherScenario: {
        _parent: null,
        _init: function (parent) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._anotherScenario;
          self._getScenario(function (result) {
            self._parent._mergeScenario(result, self._isExecutableNextAction());
            if (self._parent._goToNextScenario(true)) {
              self._parent._process();
            }
          });
        },
        _getScenario: function (callback) {
          var self = sinclo.scenarioApi._anotherScenario;
          var scenarioId = self._parent.get(self._parent._lKey.currentScenario).tChatbotScenarioId;
          emit('getScenario', {scenarioId: scenarioId}, callback);
        },
        _isExecutableNextAction: function () {
          var self = sinclo.scenarioApi._anotherScenario;
          var result = self._parent.get(self._parent._lKey.currentScenario).executeNextAction;
          return (result && "1".indexOf(result) >= 0);
        }
      },
      _callExternalApi: {
        _parent: null,
        _externalType: {
          useApi: "1",
          useScript: "2"
        },
        _init: function (parent) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._callExternalApi;
          var externalType = self._parent.get(self._parent._lKey.currentScenario).externalType;
          self._parent._doing(0, function () {
            if(String(externalType) === self._externalType.useScript){
              var externalScript = self._parent.get(self._parent._lKey.currentScenario).externalScript;
              self._callScript(self._parent._replaceVariable(externalScript));
              if (self._parent._goToNextScenario()) {
                self._parent._process();
              }
            } else {
              self._callApi(function (response) {
                Object.keys(response).forEach(function (elm, index, arr) {
                  self._parent._saveVariable(response[elm].variableName, response[elm].value);
                });
                if (self._parent._goToNextScenario()) {
                  self._parent._process();
                }
              });
            }
          });
        },
        _callScript: function(externalScript){
          var self = sinclo.scenarioApi._callExternalApi;
          emit('traceScenarioInfo', {
            type: "i",
            message: "call external script. from scenarioID : " + self._parent.get(self._parent._lKey.scenarioId),
            data: externalScript
          });
          try {
            eval(externalScript);
          } catch (e) {
            emit('traceScenarioInfo', {
              type: "w",
              message: "call external script error found. error: " + e.message,
              data: externalScript
            });
            console.log(e.message);
            return;
          }
          emit('traceScenarioInfo', {
            type: "i",
            message: "result is OK.",
            data: externalScript
          });
        },
        _callApi: function (callback) {
          var self = sinclo.scenarioApi._callExternalApi;
          var externalApiConnectionId = self._parent.get(self._parent._lKey.currentScenario).tExternalApiConnectionId;
          emit('callExternalApi', {
            externalApiConnectionId: externalApiConnectionId,
            variables: self._parent._getAllTargetVariables()
          }, function (result) {
            callback(result);
          });
        }
      },
      _receiveFile: { // 管理画面上ではファイル送信
        _parent: null,
        _init: function (parent) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._receiveFile;
          self._parent._doing(self._parent._getIntervalTimeSec(), function () {
            self._parent._handleChatTextArea(self._parent.get(self._parent._lKey.currentScenario).chatTextArea);
            self._getDownloadInfo(function (result) {
              if (result.success) {
                if (result.deleted) {
                  // FIXME
                } else {
                  var splitedFileName = result.fileName.split('.');
                  result.extension = splitedFileName[splitedFileName.length - 1].toLowerCase();
                  result.message = self._parent.get(self._parent._lKey.currentScenario).message;
                  self._parent._showFileTypeMessage(self._parent.get(self._parent._lKey.currentScenario).actionType, result, 0, self._parent.get(self._parent._lKey.currentScenario).chatTextArea, function () {
                    if (self._parent._goToNextScenario()) {
                      self._parent._process();
                    }
                  });
                }
              }
            });
          });
        },
        _getDownloadInfo: function(callback) {
          var self = sinclo.scenarioApi._receiveFile;
          var sendFileId = self._parent.get(self._parent._lKey.currentScenario).tChatbotScenarioSendFileId;
          emit('getScenarioDownloadInfo', {
            sendFileId: sendFileId
          }, function (result) {
            callback(result);
          });
        }
      },
      _getAttributeValue: {
        _parent: null,
        _init: function (parent) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._getAttributeValue;
          self._parent._doing(0, function () {
            self._parent._handleChatTextArea(self._parent.get(self._parent._lKey.currentScenario).chatTextArea);
            self._getValueFromAttribute(function (result) {
              if (self._parent._goToNextScenario()) {
                self._parent._process();
              }
            });
          });
        },
        _getValueFromAttribute: function (callback) {
          var self = sinclo.scenarioApi._getAttributeValue;
          var attributeSettings = self._parent.get(self._parent._lKey.currentScenario).getAttributes;
          for (var i = 0; i < attributeSettings.length; i++) {
            self._parent._saveVariable(attributeSettings[i].variableName, self._getValue(attributeSettings[i].type, attributeSettings[i].attributeValue));
          }
          callback();
        },
        _getValue: function (type, selector) {
          var self = sinclo.scenarioApi._getAttributeValue;
          switch (Number(type)) {
            case 1: // ID
              return self._getText($('#' + selector));
            case 2: // name
              return self._getText($('[name="' + selector + '"]')); // FIXME あやしい
            case 3: // CSS-selector
              return self._getText($(selector));
              break;
          }
        },
        _getText: function (jqObject) {
          if (jqObject.text() !== "") {
            return jqObject.text();
          } else if (jqObject.val() !== "") {
            return jqObject.val();
          } else {
            return "";
          }
        }
      },
      _sendFile: {
        _parent: null,
        _downloadUrlKey: "s_sendfile_data",
        _init: function (parent) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._sendFile;
          self._parent._doing(self._parent._getIntervalTimeSec(), function () {
            self._parent._handleChatTextArea("2");
            var dropAreaMessage = self._parent.get(self._parent._lKey.currentScenario).dropAreaMessage;
            var cancelEnabled = self._parent.get(self._parent._lKey.currentScenario).cancelEnabled;
            var cancelLabel = self._parent.get(self._parent._lKey.currentScenario).cancelLabel;
            var extensionType = self._parent.get(self._parent._lKey.currentScenario).receiveFileType;
            var extendedExtensions = self._parent.get(self._parent._lKey.currentScenario).extendedReceiveFileExtensions.split(',');
            sinclo.chatApi.unread++;
            sinclo.chatApi.showUnreadCnt();
            sinclo.chatApi.createSelectUploadFileMessage(dropAreaMessage, cancelEnabled, cancelLabel, extensionType, extendedExtensions);
            self._waitUserAction(self._handleFileSelect);
          });
        },
        _waitUserAction: function (callback) {
          var self = sinclo.scenarioApi._sendFile;
          $(document).one(self._parent._events.fileUploaded, callback);
        },
        _pushDownloadUrlData: function (obj) {
          var self = sinclo.scenarioApi._sendFile;
          var data = self._parent._getSavedVariable(self._downloadUrlKey);
          var dataObj = [];
          if (check.isJSON(data)) {
            dataObj = JSON.parse(data);
          }
          dataObj.push(obj);
          self._parent._saveVariable(self._downloadUrlKey, JSON.stringify(dataObj));
        },
        _showError: function () {
          var self = sinclo.scenarioApi._sendFile;
          var errorMessage = self._parent.get(self._parent._lKey.currentScenario).errorMessage;
          self._parent._doing(0, function () {
            self._parent._handleChatTextArea(self._parent.get(self._parent._lKey.currentScenario).chatTextArea);
            self._parent._showMessage(self._parent.get(self._parent._lKey.currentScenario).actionType, errorMessage, "delete_e" + (new Date(common.fullDateTime())).getTime(), self._parent.get(self._parent._lKey.currentScenario).chatTextArea, function () {
              self._waitUserAction(self._handleFileSelect);
            });
          });
        },
        _handleFileSelect: function (event, result, data) {
          console.log("FIRE _handleFileSelect :::: %s, $s", result, data);
          var self = sinclo.scenarioApi._sendFile;
          if (result) {
            self._parent._handleStoredMessage();
            if (data) {
              self._pushDownloadUrlData(data);
            }
            if (self._parent._goToNextScenario()) {
              self._parent._process();
            }
          } else {
            self._showError();
          }
        }
      },
      _branchOnCond: {
        _parent: null,
        _init: function (parent) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._branchOnCond;
          // 即時で実行
          self._parent._doing(0, function () {
            self._parent._handleChatTextArea(self._parent.get(self._parent._lKey.currentScenario).chatTextArea);
            var targetValKey = self._parent.get(self._parent._lKey.currentScenario).referenceVariable;
            var conditions = self._parent.get(self._parent._lKey.currentScenario).conditionList;
            for (var i = 0; i < conditions.length; i++) {
              if (self._isMatch(targetValKey, conditions[i])) {
                self._doAction(conditions[i]);
                return;
              }
            }
            if (self._parent.get(self._parent._lKey.currentScenario).elseEnabled) {
              self._doAction(self._parent.get(self._parent._lKey.currentScenario).elseAction);
              return;
            }
            // ここに到達したら次のシナリオへ
            if (self._parent._goToNextScenario()) {
              self._parent._process();
            }
          });
        },
        _isMatch: function (targetValKey, condition) {
          var self = sinclo.scenarioApi._branchOnCond;
          var targetValue = self._parent._getSavedVariable(targetValKey);
          switch (Number(condition.matchValueType)) {
            case 1: // いずれかを含む場合
              return self._matchCaseInclude(targetValue, self._splitMatchValue(condition.matchValue));
            case 2: // いずれも含まない場合
              return self._matchCaseExclude(targetValue, self._splitMatchValue(condition.matchValue));
            default:
              return false;
          }
        },
        _doAction: function (condition, callback) {
          var self = sinclo.scenarioApi._branchOnCond;
          switch (Number(condition.actionType)) {
            case 1:
              // テキスト発言
              self._parent._doing(self._parent._getIntervalTimeSec(), function () {
                self._parent._showMessage(self._parent.get(self._parent._lKey.currentScenario).actionType, condition.action.message, 0, self._parent.get(self._parent._lKey.currentScenario).chatTextArea, function () {
                  if (self._parent._goToNextScenario()) {
                    self._parent._process();
                  }
                });
              });
              break;
            case 2:
              // シナリオ呼び出し
              var targetScenarioId = condition.action.callScenarioId;
              console.log("targetScenarioId : %s", targetScenarioId);
              if (targetScenarioId === "self") {
                targetScenarioId = self._parent.get(self._parent._lKey.scenarioId);
              }
              emit('getScenario', {scenarioId: targetScenarioId}, function (result) {
                var executeNext = condition.action.executeNextAction ? condition.action.executeNextAction : false;
                self._parent._mergeScenario(result, executeNext);
                if (self._parent._goToNextScenario(true)) {
                  self._parent._process();
                }
              });
              break;
            case 3:
              // シナリオ終了
              common.chatBotTypingRemove();
              var currentSequenceNum = Number(self._parent.get(self._parent._lKey.currentScenarioSeqNum));
              var savedReturnSettings = self._parent._getReturnSettingsOnCallerScenario(currentSequenceNum);
              if (savedReturnSettings.isReturn) {
                self._parent.set(self._parent._lKey.currentScenarioSeqNum, savedReturnSettings.lastSequenceNum);
                if (self._parent._goToNextScenario()) {
                  self._parent._process();
                }
              } else {
                self._parent._end();
              }
              break;
            case 4:
              // 何もしない（次のアクションへ）
              if (self._parent._goToNextScenario()) {
                self._parent._process();
              }
              break;
          }
        },
        _splitMatchValue: function (val) {
          var splitedArray = [];
          val.split('"').forEach(function (currentValue, index, array) {
            if (array.length > 1) {
              if (index !== 0 && index % 2 === 1) {
                // 偶数個：そのまま文字列で扱う
                if (currentValue !== "") {
                  splitedArray.push(currentValue);
                }
              } else {
                if (currentValue) {
                  var trimValue = currentValue.trim(),
                    splitValue = trimValue.replace(/　/g, " ").split(" ");
                  splitedArray = splitedArray.concat($.grep(splitValue, function (e) {
                    return e !== "";
                  }));
                }
              }
            } else {
              var trimValue = currentValue.trim(),
                splitValue = trimValue.replace(/　/g, " ").split(" ");
              splitedArray = splitedArray.concat($.grep(splitValue, function (e) {
                return e !== "";
              }));
            }
          });
          return splitedArray;
        },
        _matchCaseInclude: function (val, words) {
          console.log("_matchCaseInclude : %s <=> %s", words, val);
          var result = false;
          for (var i = 0; i < words.length; i++) {
            if (words[i] === "") {
              continue;
            }

            var word = words[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var preg = new RegExp(word);
            result = preg.test(val);

            if (result) { // いずれかを含む
              break;
            }
          }
          return result;
        },
        _matchCaseExclude: function (val, words) {
          for (var i = 0; i < words.length; i++) {
            if (words[i] === "") {
              if (words.length > 1 && i === words.length - 1) {
                break;
              }
              continue;
            } else {
              var word = words[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
              var preg = new RegExp(word);
              exclusionResult = preg.test(val);
              if (exclusionResult) {
                // 含んでいる場合はNG
                return false;
              }
            }
          }
          //最後まで含んでいなかったらOK
          return true;
        }
      },
      _addCustomerInformation: {
        _parent: null,
        _init: function (parent) {
          this._parent = parent;
        },
        _process: function () {
          var self = sinclo.scenarioApi._addCustomerInformation;
          self._parent._doing(0, function () { // 即時実行
            self._parent._handleChatTextArea(self._parent.get(self._parent._lKey.currentScenario).chatTextArea);
            self._getValueAndSend(function (result) {
              if (self._parent._goToNextScenario()) {
                self._parent._process();
              }
            });
          });
        },
        _getValueAndSend: function (callback) {
          var self = sinclo.scenarioApi._addCustomerInformation;
          var customerInformations = self._parent.get(self._parent._lKey.currentScenario).addCustomerInformations;
          var sendArray = [];
          for (var i = 0; i < customerInformations.length; i++) {
            var targetVariableName = customerInformations[i].variableName;
            var targetId = customerInformations[i].targetId;
            var variable = self._parent._getSavedVariable(targetVariableName);
            sendArray.push({
              id: targetId,
              value: variable
            });
          }
          emit('saveCustomerInfoValue', {targetValues: sendArray});
          // 即時で返す
          callback();
        }
      },
        _bulkHearing: { // 一括ヒアリング
        _parent: null,
        _analyseResult: {},
        _state: {
          waitInput: 'sbh_waitInput',
          confirm: 'sbh_confirm'
        },
        _init: function (parent) {
          this._parent = parent;
        },
        handleFormOK: function(resultValue) {
          var self = sinclo.scenarioApi._bulkHearing;
          if(resultValue && Object.keys(resultValue).length > 0) {
            var changed = false;
            var keys = Object.keys(resultValue);
            keys.forEach(function(e, i, a){
              // 保存時は変数名を利用
              self._parent._saveVariable(keys[i], resultValue[keys[i]].value);
              if(!changed) {
                changed = resultValue[keys[i]].changed;
              }
            });
            emit('sendChat', {
              historyId: sinclo.chatApi.historyId,
              stayLogsId: sinclo.chatApi.stayLogsId,
              chatMessage: JSON.stringify(resultValue),
              mUserId: null,
              messageType: changed ? 32 : 31,
              messageRequestFlg: 0,
              isAutoSpeech: false,
              notifyToCompany: false,
              isScenarioMessage: true
            }, function () {
              setTimeout(function () {
                emit('addLastMessageToCV', {historyId: sinclo.chatApi.historyId});
              }, 1000);
              if (self._parent._goToNextScenario()) {
                self._parent._process();
              }
            });
            sinclo.chatApi.hideForm();
          }
        },
        isInMode: function () {
          var self = sinclo.scenarioApi._bulkHearing;
          if (!self._parent) {
            // initがコールされていないのでヒアリング開始していない
            return false;
          } else {
            return String(self._parent.get(self._parent._lKey.currentScenario).actionType) === self._parent._actionType.bulkHearing;
          }
        },
        _process: function () {
          var self = sinclo.scenarioApi._bulkHearing;
          if(!self._isStatusConfirming()) {
            self._parent._doing(0, function () { // 即時実行
              self._parent._handleChatTextArea("1"); // 必ず表示する
              sinclo.chatApi.hideMiniMessageArea(); // 改行可のメッセージエリアにする
              common.chatBotTypingTimerClear();
              common.chatBotTypingRemove();
              setTimeout(function () {
                self._notifyBeginBulkHearing();
              }, 200);
              self._saveWaitingInputState();
              self._parent._waitingInput(function (inputVal) {
                self._parent._unWaitingInput();
                self._analyseInput(inputVal, function (result) {
                  // 描画処理はsendChatResultで実行している
                  self._parent._handleChatTextArea("2");
                  self._saveConfirmState();
                });
              });
            });
          }
        },
        _analyseInput: function (inputVal, callback) {
          var self = sinclo.scenarioApi._bulkHearing;
          emit('sendParseSignature', {
            historyId: sinclo.chatApi.historyId,
            stayLogsId: sinclo.chatApi.stayLogsId,
            targetText: inputVal,
            ip: userInfo.getIp(),
            requireCv: true,
            isAutoSpeech: false,
            notifyToCompany: false,
            isScenarioMessage: true,
            targetVariable: self._parent.get(self._parent._lKey.currentScenario).multipleHearings
          }, callback);
        },
        _notifyBeginBulkHearing: function() {
          var self = sinclo.scenarioApi._bulkHearing;
          emit('beginBulkHearing', {
            historyId: sinclo.chatApi.historyId
          });
        },
        _saveWaitingInputState: function() {
          var self = sinclo.scenarioApi._bulkHearing;
          self._parent.set(self._state.waitInput, true);
        },
        _saveConfirmState: function() {
          var self = sinclo.scenarioApi._bulkHearing;
          self._parent.set(self._state.confirm, true);
        },
        _isStatusConfirming: function() {
          var self = sinclo.scenarioApi._bulkHearing;
          var state = self._parent.get(self._state.confirm);
          return state && ((typeof(state) === "boolean" && state) || (typeof(state) === "string" && state === "true"));
        }
      }
    },
    // 外部連携API
    api: {
      getAccessId: function () {
        var value = "";
        if (userInfo && userInfo.accessId) {
          value = userInfo.accessId;
        }
        return value;
      },
      callFunction: function (type, id) {
        try {
          if (sincloInfo.custom.callFunc
            && typeof sincloInfo.custom.callFunc === 'object'
            && sincloInfo.custom.callFunc.hasOwnProperty(type)
            && typeof sincloInfo.custom.callFunc[type] === 'object') {
            if (sincloInfo.custom.callFunc[type].hasOwnProperty(Number(id)) && typeof sincloInfo.custom.callFunc[type][Number(id)] === 'function') {
              sincloInfo.custom.callFunc[type][id]();
            }
          }
        } catch (e) {
          console.log("api::callFunction Error => %s", e.message);
        }
      },
      callTelCV: function(telNumber) {
        var telNumberStr = 'tel:' + telNumber;
        try {
          if(typeof(window.gtag_report_conversion) === 'function') {
            window.gtag_report_conversion(telNumberStr);
          } else if (check.isset(window.dataLayer)) {
            window.dataLayer.push({'event': telNumberStr});
          }
        } catch(gFuncError) {
          console.log(gFuncError.message);
          emit('traceScenarioInfo', {
            type: "w",
            message: "call google tel-cv function is failed. error : " + gFuncError.message,
            data: telNumberStr
          });
        }
        try {
          if(typeof(window.yahoo_report_conversion) === 'function') {
            window.yahoo_report_conversion(telNumberStr);
          }
        } catch(yFuncError) {
          console.log(yFuncError.message);
          emit('traceScenarioInfo', {
            type: "w",
            message: "call yahoo tel-cv function is failed. error : " + yFuncError.message,
            data: telNumberStr
          });
        }
      }
    }
  };

  sincloVideo = {
    open: function (obj) {
      window.open(
        "https://ap1.sinclo.jp/index.html?userId=" + userInfo.userId,
        "monitor_" + userInfo.userId,
        "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
      );

      return false;
    }
  };

}(sincloJquery));
