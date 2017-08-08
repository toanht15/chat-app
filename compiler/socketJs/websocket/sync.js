var cnst, init, check, userInfo, browserInfo, url, syncEvent, sinclo, common, storage, emit, st, iframe;

!function(jquery){
  'use strict';
  var $ = jquery;
  cnst = {
    access_type: {
      guest: 1,
      host: 2
    },
    tab_type: {
      open: 1,
      close: 2,
      none:3,
      disable:4
    },
    info_type: {
      user: 1,
      access: 2,
      ip: 3,
      time: 4,
      page: 5,
      referrer: 6,
      connect: 7,
      tab: 8,
      prev: 9,
      staycount: 10,
    }
  };

  check = {
    isset: function(a){
      if ( a === null || a === '' || a === undefined ) {
         return false;
      }
      if ( typeof a === "object" ) {
        var keys = Object.keys(a);
        return ( Object.keys(a).length !== 0 );
      }
      return true;
    }
  };

  storage = {
    s: {
      get: function(name){
        return sessionStorage.getItem(name);
      },
      set: function(name, val){
        sessionStorage.setItem(name, val);
      },
      unset: function(name){
        sessionStorage.removeItem(name);
      }
    },
    l: {
      get: function(name){
        return localStorage.getItem(name);
      },
      set: function(name, val){
        localStorage.setItem(name, val);
      },
      unset: function(name){
        localStorage.removeItem(name);
      }
    }
  };

  browserInfo = {
    // TODO 画面同期時セットするようにする
    scrollSize: function (){ // 全体のスクロール幅
      return {
        x: document.body.scrollWidth - window.innerWidth,
        y: document.body.scrollHeight - window.innerHeight
      };
    },
    // TODO 画面同期時セットするようにする
    sc: function(){ // スクロール量を取得する先
      if ( document.body.scrollTop > document.documentElement.scrollTop || document.body.scrollLeft > document.documentElement.scrollLeft ) {
        return document.body;
      }
      else {
        return document.documentElement;
      }
    },
    windowScroll: function (){
      var customDoc = browserInfo.sc();
      var scrollSize = browserInfo.scrollSize();
      var x = (customDoc.scrollLeft / scrollSize.x);
      var y = (customDoc.scrollTop / scrollSize.y);
      return {
        x: (isNaN(x)) ? 0 : x,
        y: (isNaN(y)) ? 0 : y
      };
    },
    set: {
      scroll: function(obj){
        var scrollSize = browserInfo.scrollSize();

        document.body.scrollLeft = scrollSize.x * obj.x;
        document.body.scrollTop  = scrollSize.y * obj.y;
        document.documentElement.scrollLeft = scrollSize.x * obj.x;
        document.documentElement.scrollTop  = scrollSize.y * obj.y;
      }
    },
    getActiveWindow: function(){
      var tabFlg = document.hasFocus(), widgetFlg = false, tabStatus;
      // タブがアクティブ
      if ( tabFlg ) {
        tabStatus = cnst.tab_type.none;
      }
      else {
        tabStatus = cnst.tab_type.disable;
      }
      return tabStatus;

    }
  };

  userInfo = {
    parentId: params.tabId,
    tabId: params.tabId + "_frame",
    accessType: cnst.access_type.guest,
    setConnect: function(){
      return false;
    },
    syncInfo: {
      code: 'syncInfo',
      set: function(){
        storage.s.set(this.code, JSON.stringify({
          sendTabId: userInfo.sendTabId
        }));
      },
      get: function(){
        var syncInfo = common.jParse(storage.s.get(this.code));
        if ( check.isset(syncInfo) && check.isset(syncInfo.sendTabId) ) {
          userInfo.sendTabId = syncInfo.sendTabId;
        }

      },
      unset: function(){
        storage.s.unset(this.code);
        delete userInfo.sendTabId;
        // TODO minify
        userInfo.unsetConnect();
      }
    }
  };

  common = {
    jParse: function(d){
      if ( d === undefined ) return d;
      return JSON.parse(d);
    },
    scrollSize: function (){ // 全体のスクロール幅
      return {
        x: document.body.scrollWidth - window.innerWidth,
        y: document.body.scrollHeight - window.innerHeight
      };
    },
    // TODO 画面同期時セットするようにする
    sc: function(){ // スクロール量を取得する先
      if ( document.body.scrollTop > document.documentElement.scrollTop || document.body.scrollLeft > document.documentElement.scrollLeft ) {
        return document.body;
      }
      else {
        return document.documentElement;
      }
    },
    windowScroll: function (){
      var customDoc = common.sc();
      var scrollSize = common.scrollSize();
      var x = (customDoc.scrollLeft / scrollSize.x);
      var y = (customDoc.scrollTop / scrollSize.y);
      return {
        x: (isNaN(x)) ? 0 : x,
        y: (isNaN(y)) ? 0 : y
      };
    },
    windowScreen: function(){
      return {
        height: window.parent.screen.height,
        width: window.parent.screen.width
      };
    },
    windowSize : function(){
      return {
        height: window.innerHeight,
        width: window.innerWidth - 100
      };
    },
    setUrl: function(url){
      var data = {
        gFrame: true, // access_type.gFrame
        tabId: userInfo.tabId, // access_type.gFrame
        connectToken: params.connectToken, // access_type.gFrame
        parentId: userInfo.parentId, // access_type.gFrame
      };
      if ( url.match(/\?/) ) {
        url += "&";
      }
      else {
        url += "?";
      }

      url += "sincloData=" + encodeURIComponent(JSON.stringify(data));
      return url;
    }
  };

  iframeLocation = {
    sessionName: 'location',
    list: [],
    position: 0,
    status: null,
    forward: function(){
      if ( this.position < (this.list.length - 1) ) {
        this.status = "forward";
        this.position++;
        iframe.src = iframeLocation.list[this.position];
        this.send(this.status, this.position);
      }
    },
    back: function(){
      if ( this.position > 0 ) {
        this.status = "back";
        this.position--;
        iframe.src = iframeLocation.list[this.position];
        this.send(this.status, this.position);
      }
    },
    send:  function(s, p){
      emit('syncLocationOfFrame', {
        status: s,
        position: p
      });
      this.setBtnColor();
    },
    syncLocationOfFrame: function(d){
      var obj = JSON.parse(d);
      iframeLocation.status = obj.status;
      iframeLocation.position = obj.position;
      this.setBtnColor();
    },
    setBtnColor: function(){
      if ( this.position === 0 ) {
        $("#prevBtn:not(.unlight)").addClass('unlight');
      }
      else {
        $("#prevBtn.unlight").removeClass('unlight');
      }

      if ( this.position < (this.list.length - 1) ) {
        $("#nextBtn.unlight").removeClass('unlight');
      }
      else {
        $("#nextBtn:not(.unlight)").addClass('unlight');
      }
    },
    get: function(){
      var location = JSON.parse(sessionStorage.getItem(this.sessionName));
      this.status = location.status;
      this.list = location.list;
      this.position = location.position;
      this.setBtnColor();
    },
    save: function(){
      sessionStorage.setItem(this.sessionName, JSON.stringify({
        status: iframeLocation.status,
        list: iframeLocation.list,
        position: iframeLocation.position
      }));
    }
  };

  syncEvent = {
    resizeTimer: false,
    evList: [
      // {
      //   type: "hashchange",
      //   ev: function(e){
      //     if ( socket === undefined ) return false;
      //     browserInfo.href = location.href;
      //     emit('reqUrlChecker', {});
      //   }
      // }
    ],
    pcResize: function(e){
      if (syncEvent.resizeTimer !== false) {
        clearTimeout(syncEvent.resizeTimer);
      }
      syncEvent.resizeTimer = setTimeout(function () {
        emit('syncBrowserInfoFrame', {
          accessType: userInfo.accessType,
          // ブラウザのサイズ
          windowSize: browserInfo.windowSize(),
          mousePoint: {x: e.clientX, y: e.clientY},
          scrollPosition: browserInfo.windowScroll()
        });
        // do something ...
      }, browserInfo.interval);
    },
    tabletResize: function(e){
      var size = {
        width: window.innerWidth,
        height: window.innerHeight
      };
      var scroll = browserInfo.windowScroll();

      emit('syncBrowserInfoFrame', {
        accessType: userInfo.accessType,
        // ブラウザのサイズ
        windowSize: size,
        scrollPosition: scroll
      });
    },
    ctrlEventListener: function(eventFlg, evList){ // ウィンドウに対してのイベント操作

      var attachFlg = false, evListener;
      if ( eventFlg ) {
        evListener = window.addEventListener;
        if ( !window.addEventListener ) {
          evListener = window.attachEvent;
          attachFlg = true;
        }
      }
      else {
        evListener = window.removeEventListener;
        if ( !window.removeEventListener ) {
          evListener = window.detachEvent;
          attachFlg = true;
        }
      }
      for ( var i in evList ) {
        var evName = ( attachFlg ) ? "on" + String(evList[Number(i)].type) : String(evList[Number(i)].type);
        var event = evList[Number(i)].ev;
        evListener(evName, event);
      }
    },
    ctrlElmEventListener: function(eventFlg, els, type, ev){
      var evName, attachFlg = true;
      if ( eventFlg ) {
        if ( window.addEventListener ) attachFlg = false;
      }
      else {
        if ( window.removeEventListener ) attachFlg = false;
      }
      evName = ( attachFlg ) ? "on" + String(type) : type;
      for(var i = 0; i < els.length; i++) {
        if ( eventFlg && attachFlg) {
          els[i].attachEvent(evName, ev, false);
        }
        else if ( eventFlg && !attachFlg) {
          els[i].addEventListener(evName, ev, false);
        }
        else if ( !eventFlg && attachFlg) {
          els[i].detachEvent(evName, ev, false);
        }
        else if ( !eventFlg && !attachFlg) {
          els[i].removeEventListener(evName, ev, false);
        }
      }
    },
    changeCall: function(e){
      var nodeName = e.target.nodeName.toLowerCase(),
          checked = false,
          index = $(String(nodeName)).index(this);
      if ( nodeName !== "input" && nodeName !== "textarea" && nodeName !== "select" ) return false;
      if ( e.target.type === "radio" || e.target.type === "checkbox" ) {
        checked = e.target.checked;
      }
      // 排他処理
      if ( nodeName === String(syncEvent.receiveEvInfo.nodeName) &&  Number(index) === Number(syncEvent.receiveEvInfo.idx) ) return false;
      emit('syncChangeEv', {
        userId: userInfo.userId,
        accessType: userInfo.accessType,
        nodeName: nodeName,
        type: e.type,
        nodeType: e.target.type,
        checked: checked,
        idx: index,
        value: this.value
      });
    },
    focusCall: function(e){
      this.addEventListener('keyup', syncEvent.changeCall, false);
      this.addEventListener('change', syncEvent.changeCall, false);
    },
    resizeCall: function(ua, eventFlg){
      if ( !eventFlg ) {
        window.removeEventListener("resize", syncEvent.pcResize);
        window.removeEventListener("orientationchange", syncEvent.tabletResize);
        return false;
      }
      // ウィンドウリサイズは消費者の状態のみ反映
      if ( Number(userInfo.accessType) !== Number(cnst.access_type.guest) ) return false;
      if (
          ( (ua.indexOf("windows") != -1 && ua.indexOf("touch") != -1) ||
            ua.indexOf("ipad") != -1 ||
            (ua.indexOf("android") != -1 && ua.indexOf("mobile") == -1) ||
            (ua.indexOf("firefox") != -1 && ua.indexOf("tablet") != -1) ||
            ua.indexOf("kindle") != -1 ||
            ua.indexOf("silk") != -1 ||
            ua.indexOf("playbook") != -1
          ) &&
          'orientationchange' in window
        )
      {
        window.addEventListener("orientationchange", syncEvent.tabletResize, false);
      }
      else {
        window.addEventListener("resize", syncEvent.pcResize, false);
      }
    },
    elmScrollCallTimers: {},
    elmScrollCall: function(e){
      e.stopPropagation();
      var nodeName = e.target.nodeName.toLowerCase(),
          index = $(String(nodeName)).index(this);

      // 排他処理
      if ( nodeName === String(syncEvent.receiveEvInfo.nodeName) &&  Number(index) === Number(syncEvent.receiveEvInfo.idx) ) return false;
      var elem = document.getElementsByTagName(nodeName)[Number(index)],
          scrollBarSize = {
            height: elem.scrollHeight - elem.clientHeight,
            width: elem.scrollWidth - elem.clientWidth
          };


      if (check.isset(syncEvent.elmScrollCallTimers[nodeName+'_'+index])) {
        clearTimeout(syncEvent.elmScrollCallTimers[nodeName+'_'+index]);
      }
      syncEvent.elmScrollCallTimers[nodeName+'_'+index] = setTimeout(function(){

        emit('syncChangeEv', {
          userId: userInfo.userId,
          accessType: userInfo.accessType,
          nodeName: nodeName,
          type: e.type,
          idx: index,
          value: {
            topRatio: elem.scrollTop / scrollBarSize.height,
            leftRatio: elem.scrollLeft / scrollBarSize.width
          }
        });
      }, 1000);

    },
    receiveEvInfo: { nodeName: null, type: null },
    change: function(eventFlg){
      if ( !eventFlg ) {
        var cursorImg = document.getElementById('cursorImg');
        if ( cursorImg ) {
          cursorImg.parentNode.removeChild(cursorImg);
        }
        common.cursorTag = undefined;
      }
      // windowに対してのイベント操作
      this.ctrlEventListener(eventFlg, syncEvent.evList);

      // resizeCall
      this.resizeCall(window.navigator.userAgent.toLowerCase(), eventFlg);

    },
    start: function(e){ syncEvent.change(true); },
    stop: function(e){ syncEvent.change(false); }
  };

  sinclo = {
    syncTimeout: "",
    syncEvStart: function(d){
      var obj = common.jParse(d);
      syncEvent.start(true);
      window.clearTimeout(sinclo.syncTimeout);
    },
    receiveScTimer: false,
    syncResponce: function(d){
      var obj = common.jParse(d), cursor = common.cursorTag;
      // カーソルを作成していなければ作成する
      if ( !document.getElementById('cursorImg') ) {
        $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mousePoint.x + '; left:' + obj.mousePoint.y + '; z-index:999999"><img width="50px" src="' + site.files + '/img/pointer.png"></div>');
        cursor = common.cursorTag = document.getElementById("cursorImg");
      }
      // カーソル位置
      if ( check.isset(obj.mousePoint)) {
        cursor.style.left = (obj.mousePoint.x + 100) + "px";
        cursor.style.top  = obj.mousePoint.y + "px";
      }
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
    },
    docShareConnect: function(d){
      var obj = JSON.parse(d);
      // 終了通知
      var title = location.host + 'の内容';
      var content = location.host + 'が資料共有を求めています。<br>許可しますか';
      popup.ok = function(){
        var size = browserInfo.windowSize();
        var params = {
          data: obj,
          site: window.info.site
        };
        var url = window.info.site.files + "/docFrame/" + encodeURIComponent(JSON.stringify(params));

        window.open(url, "_blank", "width=" + size.width + ", height=" + size.height + ", resizable=yes,scrollbars=yes,status=no");
        this.remove();
      };
      popup.set(title, content);
    },
    resUrlChecker: function(d){
      var obj = JSON.parse(d);
      // 戻る & 進む以外でのアクションの場合
      setTimeout(function(){
        // 戻る & 進む以外でのアクションの場合
        if ( iframeLocation.status !== 'back' && iframeLocation.status !== 'forward') {
          // Positionが移動履歴とかみ合わない場合、上書きする
          if ( ((iframeLocation.list.length - 1) !== iframeLocation.position) ) {
            iframeLocation.list = iframeLocation.list.splice(0, iframeLocation.position + 1);
          }
          // Positionが移動履歴と一致しない場合、書き込む
          if ( iframeLocation.list[iframeLocation.list.length - 1] !== obj.url ) {
            iframeLocation.list.push(obj.url);
          }
          iframeLocation.position = iframeLocation.list.length - 1;
          iframeLocation.setBtnColor();
        }

        iframeLocation.status = null;
        iframeLocation.save();
      }, 500);
    },
    syncStopForSubmit: function(d){
      var obj = JSON.parse(d);
      if ( common.cursorTag ) {
        common.cursorTag.parentNode.removeChild(cursorImg);
      }
      common.cursorTag = undefined;
      // TODO 閉じる？
    },
    syncStop: function(d){
      emit('requestSyncStop', {
        type: 2,
        tabId: userInfo.tabId,
        parentId: userInfo.parentId,
        connectToken: params.connectToken
      });
      window.close();
    }
  };

  init = function(){
    st = io.connect(site.socket, {port: 9090, rememberTransport : false});
    popup.settings.filesPath = site.files;

    emit = function(key, data){
      data.siteKey = site.key;
      data.parentId = userInfo.parentId;
      data.tabId = userInfo.tabId;
      data.userId = params.userId;
      st.emit(key, JSON.stringify(data));
    };

    // 定期的にタブのアクティブ状態を送る
    var tabState = browserInfo.getActiveWindow();
    setInterval(function(){
      var newState = browserInfo.getActiveWindow();
      if ( tabState !== newState ) {
        tabState = newState;
        emit('sendTabInfo', { status: tabState, connectToken: params.connectToken, widget: window.sincloInfo.widgetDisplay });
      }
    }, 700);

    st.on("connect", function(){
      if ( sessionStorage.getItem(iframeLocation.sessionName)) {
        iframeLocation.get();
      }
      else {
        iframeLocation.list = [params.url];
        iframeLocation.position = 0;
        iframeLocation.save();
      }

      url = iframeLocation.list[iframeLocation.position];
      iframeLocation.setBtnColor();

      var frameDiv = document.getElementById('customer_flame');
      iframe = document.createElement('iframe');
      iframe.width = window.innerWidth - 100;
      iframe.height = window.innerHeight;
      iframe.sandbox = "allow-scripts allow-top-navigation allow-forms allow-same-origin allow-modals allow-popups";

      iframe.src = common.setUrl(url);
      frameDiv.appendChild(iframe);

      emit('connectFromSyncInit', {connectToken: params.connectToken});
      // emit('connectFromSync', {});
    }); // socket-on: connect

    // 画面同期情報を送る
    st.on("connectFromSync", function(){
      // emit('connectedFromSync', {});
      emit('sendWindowInfoFromFrame', {
        connectToken: params.connectToken,
        url: params.url,
        // ブラウザのサイズ
        windowSize: common.windowSize(),
      });
    }); // socket-on: connectedFromSync

    st.on("docShareConnect", function(d){
      sinclo.docShareConnect(d);
    }); // socket-on: docShareConnect

    st.on("syncResponce", function(d){
      sinclo.syncResponce(d);
    }); // socket-on: syncResponce

    st.on("syncLocationOfFrame", function(d){
      iframeLocation.syncLocationOfFrame(d);
    }); // socket-on: syncResponce

    st.on("resUrlChecker", function(d){
      sinclo.resUrlChecker(d);
    }); // socket-on: syncResponce

    st.on("syncStopForSubmit", function(d){
      sinclo.syncStopForSubmit(d);
    }); // socket-on: syncResponce

  };

  $(window).on("resize", function(e){
    e.stopPropagation();
    var windowSize = common.windowSize();
    iframe.width = windowSize.width;
    iframe.height = windowSize.height;
    if ( iframe.height < 310 ) {
      window.resizeTo(window.outerWidth, 310 + (window.outerHeight - window.innerHeight));
    }
  });

  var timer = window.setInterval(function(){
    if ( io !== "" ) {
      window.clearInterval(timer);
      init();
    }
  }, 200);

}(sincloJquery);