'use strict';
var socket, // socket.io
    cnst, // 定数
    common, // 共通関数
    storage, // データ保存領域
    check, // チェック関数
    userInfo, // ユーザー情報
    browserInfo, // ブラウザ情報
    syncEvent, // 画面同期関連関数
    sinclo, // リアルタイム通信補助関数
    sincloJquery = $.noConflict(true);

(function($){
  cnst = {
    access_type: {
      guest: 1,
      host: 2
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
      prev: 9
    }
  };

  common = {
    n: 20,
    str: "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890",
    token: null,
    cursorTag: null,
    params : {},
    tmpParams : {},
    getParams: function(){
      // パラメータの取得
      var params = location.href.split('?'), pair, i, kv;
      if ( params[1] !== undefined ) {
        pair=params[1].split('&');
        for(i=0; pair[i]; i++) {
          kv = pair[i].split('=');
          if ( kv[0] !== "first" ) {
            this.tmpParams[kv[0]]=kv[1];
          }
        }
      }
    },
    saveParams: function(){
      this.params = this.tmpParams;
      storage.s.set('params', JSON.stringify(this.params));
    },
    setParams: function(){
      this.params = common.jParse(storage.s.get('params'));
    },
    unsetParams: function(){
      storage.s.unset('params');
    },
    title: function(){
      return ( document.getElementsByTagName('title')[0] ) ? document.getElementsByTagName('title')[0].text : "";
    },
    makeToken: function(){
      var t = "";
      for(var i=0; i<this.n; i++){
        t += this.str[Math.floor(Math.random()*this.str.length)];
      }
      return t;
    },
    token_add: function(){
      this.token = this.makeToken();
      return this.token;
    },
    jParse: function(d){
      if ( d === undefined ) return d;
      return JSON.parse(d);
    },
    sincloBoxHeight: 270,
    createWidget: function(){
      var chat = this.chatWidgetTemplate();
      var call = this.widgetTemplate();
      var css = '';

      css += '<style>';
      css += '#sincloContents *{';
      css += '  box-sizing: border-box;';
      css += '  background-color: rgba(0,0,0,0);';
      css += '  border: none;';
      css += '  border-radius: 0;';
      css += '  padding: 0;';
      css += '  margin: 0;';
      css += '  line-height: 1;';
      css += '  color: #6B6B6B;';
      css += '  font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana;';
      css += '}';
      css += '</style>';

      return "<section id='sincloContents'>" + css + chat + call + "</section>";
    },
    widgetTemplate: function(){
      var widget = window.info.widget,
          call  = { html: "", css: "" };
      call.css  += '#sincloBox{';
      call.css  += '  box-sizing: border-box; position: fixed; height: 45px; bottom: -11px; right: 5px; border: 1.5px solid rgb(232, 231, 224); border-radius: 10px; z-index: 999998; width: 250px; overflow: hidden; background-color: rgb(255, 255, 255);';
      call.css  += '}';
      call.css  += '#sincloBox a:hover { color: #ABCD05 }';

      call.html += '<div id="sincloBox" data-flg=false class="sincloBoxes">';
      call.html += '  <style>' + call.css + '</style>';
      call.html += '  <img onclick="sinclo.operatorInfo.ev(\'sincloBox\')" style="position: absolute; top: 11.5px; right: 10px; z-index: 0;" src=" ' + window.info.site.files + '/img/yajirushi.png" height="12" width="16.5">';
      call.html += '  <div onclick="sinclo.operatorInfo.ev(\'sincloBox\')" style="background-color: #ABCD05; width: 100%; height: 35px; background-image: url( ' + window.info.site.files + '/img/call.png); background-repeat: no-repeat; background-position: 15px, 0; background-size: 4.5%; color: #FFF;">';
      call.html += '    <pre style="color: #FFF; text-align: center; font-size: 15px; padding: 10px; margin:  0;">' + widget.title + '</pre>'
      call.html += '  </div>';
      // 受付時間を表示しない
      if ( widget.display_time_flg === 0 ) {
        call.html += '    <div style="background-image: url( ' + window.info.site.files + '/img/call_circle.png); background-repeat: no-repeat; background-position: 5px, 0px;height: 45px; margin: 15px 10px;background-size: 45px auto, 45px auto;padding-left: 45px;">';
        call.html += '      <pre style="font-weight: bold; color: #ABCD05; margin: 0 auto;font-size: 20px; text-align: center;padding: 10px 0px 0px;height: 45px;">' + widget.tel + '</pre>';
        call.html += '    </div>';
      }
      else {
        call.html += '    <div style="background-image: url( ' + window.info.site.files + '/img/call_circle.png); background-repeat: no-repeat; background-position: 5px, 0; height: 50px; margin: 15px 10px; background-size: 55px auto, 55px auto; padding-left: 55px;">';
        call.html += '      <pre style="font-weight: bold; color: #ABCD05; margin: 0 auto; font-size: 18px; text-align: center; padding: 5px 0 0; height: 30px">' + widget.tel + '</pre>';
        call.html += '      <pre style="font-weight: bold; color: #ABCD05; margin: 0 auto; font-size: 10px; text-align: center; padding: 0 0 5px; height: 20px">受付時間： ' + widget.time_text + '</pre>';
        call.html += '    </div>';
      }
      call.html += '    <pre style="display: block; word-wrap: break-word; font-size: 11px; text-align: center; margin: auto; line-height:1.5; color: #6B6B6B; width: 20em;">' + widget.content + '</pre>';
      call.html += '    <span style="display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border: 1px solid rgb(188, 188, 188); border-radius: 15px">' + userInfo.accessId + '</span>';
      call.html += '    <p style="padding: 5px 0; text-align: center; border-top: 1px solid #DBDBDB;color: #A1A1A1!important; height: 20px; font-size: 11px;">Powered by <a target="sinclo" href="http://medialink-ml.co.jp/index.html">sinclo</a></p>';
      call.html += '</div>';
      return call.html;
    },
    chatWidgetTemplate: function(){
      var widget = window.info.widget,
          chat = {
            html: "",
            css: ""
      };

      chat.css  += '#sincloChatBox {';
      chat.css  += '  box-sizing: border-box; position: fixed; height: 45px;  bottom: -11px; right: 270px; border: 1.5px solid rgb(232, 231, 224); border-radius: 10px; z-index: 999998; width: 250px; overflow: hidden; background-color: rgb(255, 255, 255);';
      chat.css  += '}';
      chat.css  += '#sincloChatBox ul { width: 100%; height: 200px; padding: 5px;background-color: #FDFDFD; list-style-type: none; overflow-y: scroll; overflow-x: hidden;}';
      chat.css  += '#sincloChatBox li { margin: 5px 0;padding: 5px; font-size: 12px; box-shadow: 0 0 1px rgba(0,0,0,0.5); color: #8A8A8A; white-space: pre; }';
      chat.css  += '#sincloChatBox li.sinclo_se { border-radius: 5px 5px 0; margin-left: 10px; background-color: #FFF; }';
      chat.css  += '#sincloChatBox li.sinclo_re { margin-right: 10px; border-radius: 5px 5px 5px 0; background-color: #F1F5C8; }';
      chat.css  += '#sinclo_sendbtn{ position: absolute; bottom: 3px; right: 3px; background-color: #FF7B7B; border-radius: 15px; padding: 2px; font-size: 20px; color: #FFF; opacity: 0.3 }';
      chat.css  += '#sinclo_sendbtn:hover{ opacity: 1!important; cursor: pointer; transition: opacity 500ms linear; }';
      chat.css  += '#sincloChatBox textarea { padding: 5px; resize: none; width: 230px; height: 50px; border: 1px solid #E4E4E4; border-radius: 5px; background-color: rgb(253, 253, 253)}';
      chat.css  += '#sincloChatBox textarea:focus{ outline: none; border-color: #CDDC39!important }';
      chat.css  += '#sincloChatBox a:hover { color: #ABCD05 }';

      chat.html += '<div id="sincloChatBox" data-flg=false class="sincloBoxes">';
      chat.html += '  <style>' + chat.css + '</style>';
      chat.html += '  <img onclick="sinclo.operatorInfo.ev(\'sincloChatBox\')" style="position: absolute; top: 11.5px; right: 10px; z-index: 0;" src=" ' + window.info.site.files + '/img/yajirushi.png" height="12" width="16.5">';
      chat.html += '  <div onclick="sinclo.operatorInfo.ev(\'sincloChatBox\')" style="background-color: #ABCD05; width: 100%; height: 35px; background-image: url( ' + window.info.site.files + '/img/chat.png); background-repeat: no-repeat; background-position: 15px, 0; background-size: 9%; color: #FFF;">';
      chat.html += '    <pre style="color: #FFF; text-align: center; font-size: 15px; padding: 10px; margin:  0;">チャット</pre>'
      chat.html += '  </div>';
      chat.html += '  <ul id="chatTalk"></ul>';
      chat.html += '  <div style="border-top: 1px solid #DEDEDE; height: 70px; padding: 10px; position: relative;">';
      chat.html += '    <textarea name="sincloChat" id="sincloChatMessage" />';
      chat.html += '    <span id="sinclo_sendbtn" onclick="sinclo.chatApi.push()">＋</span>';
      chat.html += '  </div>';
      chat.html += '  <p style="padding: 5px 0; text-align: center; border-top: 1px solid #DBDBDB;color: #A1A1A1!important; height: 20px; font-size: 11px;">Powered by <a target="sinclo" href="http://medialink-ml.co.jp/index.html">sinclo</a></p>';
      chat.html += '</div>';

      return chat.html;
    },
    makeAccessIdTag: function(){
      if ( !check.browser() ) return false;
      emit('getWidgetInfo', {});
    },
    load: {
      id: "loadingImg",
      flg: false,
      start:  function(){
        var div = document.createElement('div');
        div.id = this.id;
        div.style.cssText = "position: fixed; top: 0; left: 0; bottom: 0; right: 0; background-color: rgba(255,255,255); z-index: 99999";
        document.body.appendChild(div);
        this.flg = true; // 一度接続済みというフラグを持たせる
      },
      finish: function(){
        if ( document.getElementById(this.id) ) {
          var target = document.getElementById(this.id);
          target.parentNode.removeChild(target);
          if ( document.getElementById(this.id) ) {
            this.finish();
          }
        }
      }
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

  check = {
    browser: function(){
      var ret = false;
      // 消費者のみ、ローカルストレージとセッションストレージが使用できる環境のみ
      if (window.localStorage && window.sessionStorage) {
        if (!check.isset(common.tmpParams) && !check.isset(sessionStorage.params)) {
          ret = true;
        }
      }
      var ua  =navigator.userAgent.toLowerCase();
      // Botとスマートフォンは弾く
      if ( ua.indexOf('iphone') > 0 || ua.indexOf('ipod') > 0 || ua.indexOf('android') > 0 || ua.indexOf('bot') > 0 ) {
        ret = false;
      }
      return ret;
    },
    isset: function(a){
      if ( a === null || a === '' || a === undefined ) {
         return false;
      }
      if ( typeof a === "object" ) {
        var keys = Object.keys(a);
        return ( Object.keys(a).length !== 0 );
      }
      return true;
    },
    ref: function(){
      var reg = new RegExp("^http(s)?:\/\/([A-z]+.)?" + location.hostname + "\/"),
          ref = document.referrer;
      return ref.match(reg);
    }
  };

  userInfo = {
    accessType: cnst.access_type.guest,
    connectToken: "",
    tabId: null,
    userId: null,
    accessId: null,
    ipAddress: null,
    userAgent: window.navigator.userAgent,
    init: function(){
      // ストレージの内容をオブジェクトに格納
      this.globalSet();
      // ストレージにリファラーのセット
      this.setPrevpage();

      common.getParams();
      if ( check.isset(storage.s.get('params')) ) {
        common.setParams();
      }
      else {
        if ( Number(common.tmpParams.type) === Number(cnst.access_type.host) ) {
          userInfo.userId = common.tmpParams.userId;
          userInfo.setTabId();
          common.tmpParams.tabId = userInfo.tabId;
          common.saveParams();
        }
      }
      // 複製したウィンドウの場合
      if ( Number(common.params.type) === Number(cnst.access_type.host) ) {
        userInfo.accessType = cnst.access_type.host;
        userInfo.sendTabId = common.params.sendTabId;
        userInfo.tabId = common.params.tabId;
        userInfo.setConnect(common.params.connectToken);
        emit('connectSuccess', {
          confirm: false,
          subWindow: true
        });
      }

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
        // TODO minify
        userInfo.unsetConnect();
      }
    },
    getCode: function(type){
      switch(type) {
        case cnst.info_type.user:
          return "userId";
          break;
        case cnst.info_type.access:
          return "accessId";
          break;
        case cnst.info_type.ip:
          return "ipAddress";
          break;
        case cnst.info_type.time:
          return "time";
          break;
        case cnst.info_type.page:
          return "page";
          break;
        case cnst.info_type.referrer:
          return "referrer";
          break;
        case cnst.info_type.connect:
          return "connectToken";
          break;
        case cnst.info_type.tab:
          return "tabId";
          break;
        case cnst.info_type.prev:
          return "prev";
          break;
      }
    },
    set: function(type, val, session){
      var code = this.getCode(type);
      if ( !session ) {
        storage.l.set(code, val);
      }
      else {
        storage.s.set(code, val);
      }
      userInfo[code] = val;
    },
    get: function(type){
      var code = this.getCode(type);
      if (check.isset(storage.l.get(code))) {
        return storage.l.get(code);
      }
      else if (check.isset(storage.s.get(code))) {
        return storage.s.get(code);
      }
    },
    unset: function(type){
      var code = this.getCode(type);
      userInfo[code] = null;
      if (check.isset(storage.l.get(code))) {
        storage.l.unset(code);
      }
      if (check.isset(storage.s.unset(code))) {
        storage.s.unset(code);
      }
    },
    globalSet: function () {
      var array = Object.keys(cnst.info_type);
      for ( var i in array ) {
        var code = this.getCode(cnst.info_type[array[i]]);
        if (check.isset(storage.l.get(code))) {
          userInfo[code] = storage.l.get(code);
        }
        else if (check.isset(storage.s.get(code))) {
          userInfo[code] = storage.s.get(code);
        }
      }
    },
    strageReset: function () {
      var array = [
        cnst.info_type.access,
        cnst.info_type.ip,
        cnst.info_type.time,
        cnst.info_type.page,
        cnst.info_type.referrer,
        cnst.info_type.connect,
        cnst.info_type.tab,
        cnst.info_type.prev
      ];
      for ( var i in array ) { userInfo.unset(array[i]) }
    },
    getUserId: function(){
      return this.get(cnst.info_type.user);
    },
    getTabId: function(){
      return this.get(cnst.info_type.tab);
    },
    getAccessId: function(){
      return this.get(cnst.info_type.access);
    },
    getIp: function(){
      return this.get(cnst.info_type.ip);
    },
    getTime: function(){
      return this.get(cnst.info_type.time);
    },
    getPage: function(){
      return this.get(cnst.info_type.page);
    },
    getConnect: function(){
      return this.get(cnst.info_type.connect);
    },
    setPage: function(){
      var p = this.get(cnst.info_type.page),
      n = ( isNaN(p) ) ? 1 : Number(p) + 1;
      this.set(cnst.info_type.page, n, true);
      return n;
    },
    setReferrer: function(){
      var code = this.getCode(cnst.info_type.referrer);
      userInfo.referrer = storage.s.get(code);
      // IE8対応コード
      if ( !check.isset(userInfo.referrer) ) {
        if ( check.isset(document.referrer) ) {
          storage.s.set(code, document.referrer);
        }
      }
    },
    setPrevpage: function(){
      var code = this.getCode(cnst.info_type.prev);
      userInfo.prev = common.jParse(storage.s.get(code));
      if ( !check.isset(userInfo.prev) ) {
        userInfo.prev = [];
      }
      // IE8対応コード
      if ( userInfo.prev.length === 0 || location.href !== userInfo.prev[userInfo.prev.length - 1].url ) {
        if ( userInfo.prev.length > 0 ) {
          browserInfo.referrer = userInfo.prev[userInfo.prev.length - 1].url;
        }
        userInfo.prev.push({url: location.href, title: common.title()});
        storage.s.set(code, JSON.stringify(userInfo.prev));
        userInfo.setPage();
      }
    },
    setConnect: function(val){
      this.set(cnst.info_type.connect, val, true);
    },
    setTabId: function(){
      var val = userInfo.userId + "_" + common.makeToken();
      this.set(cnst.info_type.tab, val, true);
    },
    unsetAccessId: function(){
      return this.unset(cnst.info_type.access);
    },
    unsetConnect: function(){
      return this.unset(cnst.info_type.connect);
    },
    getSendList: function() {
        return {
        ipAddress: this.getIp(),
        time: this.getTime(),
        page: this.getPage(),
        prev: this.prev,
        referrer: this.referrer,
        userAgent: window.navigator.userAgent,
        service: check.browser()
      };
    }
  };

  browserInfo = {
    referrer: "",
    href: location.href.replace(location.search, ""),
    prevList: [],
    sc: function(){
      return 'BackCompat' === document.compatMode ? document.body : document.documentElement
    },
    resetPrevList: function(){
      var prevList = [];
      prevList.push(this.href);
      this.prevList = prevList;
      storage.s.set('prevList', JSON.stringify(this.prevList));
    },
    setPrevList: function(){
      var prevList = [];
      if ( check.isset(sessionStorage.prevList) ) {
        prevList = JSON.parse(sessionStorage.prevList);
      }
      prevList.push(this.href);
      this.prevList = prevList;
      sessionStorage.prevList = JSON.stringify(this.prevList);
    },
    windowScroll: function (){
      return {
        x: window.pageXOffset || this.sc().scrollLeft,
        y: window.pageYOffset || this.sc().scrollTop
      };
    },
    windowScreen: function(){
      if ( userInfo.accessType !== cnst.access_type.host ) {
        return {
          height: window.parent.screen.height,
          width: window.parent.screen.width
        };
      }
      else {
        return {
          height: null,
          width: null
        }
      }
    },
    windowSize : function(){
      return {
        height: window.innerHeight,
        width: window.innerWidth
      }
    },
    interval: Math.floor(1000 / 60 * 10),
    set: {
      scroll: function(obj){
        document.body.scrollLeft = obj.x;
        document.body.scrollTop  = obj.y;
        document.documentElement.scrollLeft = obj.x;
        document.documentElement.scrollTop  = obj.y;
      }
    }
  };

  syncEvent = {
    resizeTimer: false,
    evList: [
      {
        type: "mousemove",
        ev: function(e){
          emit('syncBrowserInfo', {
            accessType: userInfo.accessType,
            mousePoint: {x: e.clientX, y: e.clientY}
          });
        }
      },
      {
        type: "scroll",
        ev: function(e){
          if ( socket === undefined ) return false;
          // 排他処理
          if ( "body" === syncEvent.receiveEvInfo.nodeName && "scroll" === syncEvent.receiveEvInfo.type ) return false;
          // スクロール用
          emit('syncBrowserInfo', {
            accessType: userInfo.accessType,
            mousePoint: {x: e.clientX, y: e.clientY},
            scrollPosition: browserInfo.windowScroll()
          });
        }
      },
      {
        type: "resize",
        ev: function(e){
          if (syncEvent.resizeTimer !== false) {
            clearTimeout(syncEvent.resizeTimer);
          }
          syncEvent.resizeTimer = setTimeout(function () {
            emit('syncBrowserInfo', {
              accessType: userInfo.accessType,
              // ブラウザのサイズ
              windowSize: browserInfo.windowSize(),
              mousePoint: {x: e.clientX, y: e.clientY},
              scrollPosition: browserInfo.windowScroll()
            });
            // do something ...
          }, browserInfo.interval);
        }
      }
    ],
    ctrlEventListener: function(eventFlg, evList){ // ウィンドウに対してのイベント操作

      var attachFlg = false;
      if ( eventFlg ) {
        var evListener = window.addEventListener;
        if ( !window.addEventListener ) {
          evListener = window.attachEvent;
          attachFlg = true;
        }
      }
      else {
        var evListener = window.removeEventListener;
        if ( !window.removeEventListener ) {
          evListener = window.detachEvent;
          attachFlg = true;
        }
      }
      for ( var i in evList ) {
        // ウィンドウリサイズは消費者の状態のみ反映
        if ( evList[i].type === "resize" && Number(userInfo.accessType) !== Number(cnst.access_type.guest) ) continue;
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
          index = $(String(nodeName)).index(this);
      if ( nodeName !== "input" && nodeName !== "textarea" && nodeName !== "select" ) return false;
      // 排他処理
      if ( nodeName === String(syncEvent.receiveEvInfo.nodeName) &&  Number(index) === Number(syncEvent.receiveEvInfo.idx) ) return false;
      emit('syncChangeEv', {
        tabId: userInfo.tabId,
        userId: userInfo.userId,
        accessType: userInfo.accessType,
        nodeName: nodeName,
        type: e.type,
        idx: index,
        value: this.value
      });
    },
    focusCall: function(e){
      this.addEventListener('keyup', syncEvent.changeCall, false);
      this.addEventListener('change', syncEvent.changeCall, false);
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
          tabId: userInfo.tabId,
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

      // 要素に対してのイベント操作
      var els = document.getElementsByTagName('input');
      this.ctrlElmEventListener(eventFlg, els, "focus", syncEvent.focusCall);
      var els = document.getElementsByTagName('textarea');
      this.ctrlElmEventListener(eventFlg, els, "focus", syncEvent.focusCall);

      var $textarea = document.getElementsByTagName("textarea")[0];
      if ( $textarea !== undefined ) {
        var bHeight, bWidth; // ここが要素ごとになるように・・・
        $textarea.addEventListener('mousemove', function(e){
            if ( bHeight && bWidth && ( bHeight !== this.style.height || bWidth !== this.style.width)) {
          }
            bHeight = this.style.height;
            bWidth = this.style.width;
        }, false);
      }

      // プルダウンに対してのイベント操作
      var els = document.getElementsByTagName("select");
      this.ctrlElmEventListener(eventFlg, els, "change", syncEvent.changeCall);


      // 要素スクロール
      var scEls = [];
      var els = document.getElementsByTagName("ul");
      for ( var i in els ) {
        var cHeight = els[i].clientHeight;
        var sHeight = els[i].scrollHeight;
        if ( (sHeight-cHeight)>0 ) {
          scEls.push(els[i]);
        }
      }
      var els = document.getElementsByTagName("textarea");
      for ( var i in els ) {
        var cHeight = els[i].clientHeight;
        var sHeight = els[i].scrollHeight;
        if ( (sHeight-cHeight)>0 ) {
          scEls.push(els[i]);
        }
      }
      var els = document.getElementsByTagName("div");
      for ( var i in els ) {
        var cHeight = els[i].clientHeight;
        var sHeight = els[i].scrollHeight;
        if ( (sHeight-cHeight)>0 ) {
          scEls.push(els[i]);
        }
      }
      var els = document.getElementsByTagName("dl");
      for ( var i in els ) {
        var cHeight = els[i].clientHeight;
        var sHeight = els[i].scrollHeight;
        if ( (sHeight-cHeight)>0 ) {
          scEls.push(els[i]);
        }
      }
      this.ctrlElmEventListener(eventFlg, scEls, "scroll", syncEvent.elmScrollCall);

    },
    start: function(e){ syncEvent.change(true); },
    stop: function(e){ syncEvent.change(false); }
  };

  var windowBeforeUnload = function(e) {
    var subWindow = true;
    if ( !check.isset(sessionStorage.params) && userInfo.accessType !== cnst.access_type.host ) {
      subWindow = false;
    }

    emit('connectSuccess', {
      confirm: true,
      widget: window.info.widgetDisplay,
      subWindow: subWindow,
      connectToken: userInfo.connectToken
    });
  };

  // イベントリスナーに対応している
  if(window.addEventListener){
    // アンロードされる直前に実行されるイベント
    window.addEventListener("beforeunload" , windowBeforeUnload);
  // アタッチイベントに対応している
  }else if(window.attachEvent){
    // アンロードされる直前に実行されるイベント
    window.attachEvent("onbeforeunload" , windowBeforeUnload);
  }
  else{
    // アンロードされる直前に実行されるイベント
    window.onbeforeunload = windowBeforeUnload;
  }

  var init = function(){
    socket = io.connect(info.site.socket, {port: 9090, rememberTransport : false});
    // 接続時
    socket.on("connect", function(){
      sinclo.connect();
    }); // socket-on: connect

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("accessInfo", function(d){
      sinclo.accessInfo(d);
    }); // socket-on: accessInfo

    // 通信確認
    socket.on("connectConfirm", function(d){
      sinclo.connectConfirm(d);
    }); // socket-on: accessInfo

    // 情報送信
    socket.on("getAccessInfo", function(d) {
      sinclo.getAccessInfo(d);
    }); // socket-on: getAccessInfo

    // 情報送信
    socket.on("confirmCustomerInfo", function(d) {
      sinclo.confirmCustomerInfo(d);
    }); // socket-on: confirmCustomerInfo

    // 接続確認
    socket.on('getConnectInfo', function(d){
      sinclo.getConnectInfo(d);
    }); // socket-on: getConnectInfo

    // 画面共有
    socket.on('getWindowInfo', function(d){
      sinclo.getWindowInfo(d);
    }); // socket-on: getWindowInfo

    // スクロール位置のセット
    socket.on('windowSyncInfo', function (d) {
      sinclo.windowSyncInfo(d);
    }); // socket-on: windowSyncInfo

    // 同期情報の収集
    socket.on('syncStart', function(d){
      sinclo.syncStart(d);
    }); // socket-on: syncStart

    // 消費者画面の情報を反映
    socket.on('syncElement', function(d){
      sinclo.syncElement(d);
    }); // socket-on: syncElement

    // イベント監視
    socket.on('syncEvStart', function(d){
      sinclo.syncEvStart(d);
    }); // socket-on: syncEvStart

    // イベント結果適用
    socket.on('syncResponce', function (d) {
      sinclo.syncResponce(d);
    }); // socket-on: syncResponce

    socket.on('syncResponceEv', function (d) {
      sinclo.syncResponceEv(d);
    }); // socket-on: syncResponceEv

    socket.on('userDissconnection', function (d) {
      sinclo.userDissconnectionEv(d);
    });

    // 継続接続
    socket.on('syncContinue', function (d) {
      sinclo.syncContinue(d);
    });

    socket.on('setWidgetInfo', function (d) {
      sinclo.setWidgetInfo(d);
    }); // socket-on: setWidgetInfo

    socket.on('receiveConnect', function (d) {
      sinclo.receiveConnectEv(d);
    }); // socket-on: receiveConnectEV

    socket.on('resUrlChecker', function (d) {
      sinclo.resUrlChecker(d);
    }); // socket-on: receiveConnectEV

    // チャット初期データ
    socket.on('chatMessageData', function (d) {
      sinclo.chatMessageData(d);
    }); // socket-on: receiveConnectEV

    // 新着チャット
    socket.on('sendChatResult', function (d) {
      sinclo.sendChatResult(d);
    }); // socket-on: receiveConnectEV

    socket.on('syncStop', function(d){
      sinclo.syncStop(d);
    }); // socket-on: syncStop
  };

  var timer = window.setInterval(function(){
    if ( io !== "" && sinclo !== "" ) {
      window.clearInterval(timer);
      init();
    }
  }, 200);

}(sincloJquery));

function emit(evName, data){
  data.userId = userInfo.userId;
  data.accessId = userInfo.accessId;
  data.token = common.token_add();
  data.title = common.title();
  data.siteKey = info.site.key;
  data.url= browserInfo.href;
  data.tabId = userInfo.tabId;
  data.prevList = browserInfo.prevList;
  data.accessType = userInfo.accessType;
  data.connectToken = userInfo.get(cnst.info_type.connect);
  if ( evName == "sendWindowInfo" ) {
    data.connectToken = userInfo.connectToken;
  }
  if ( check.isset(userInfo.sendTabId) ) {
    data.from = userInfo.tabId;
    data.to = userInfo.sendTabId;
    data.prevList = browserInfo.prevList;
  }
  socket.emit(evName, JSON.stringify(data));
}

function now(){
  var d = new Date();
  return "【" + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() + "】";
}
