'use strict';
var socket, // socket.io
    cnst, // 定数
    common, // 共通関数
    storage, // データ保存領域
    check, // チェック関数
    userInfo, // ユーザー情報
    browserInfo, // ブラウザ情報
    syncEvent, // 画面同期関連関数
    popup, // ポップアップ関連関数
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
      var params = location.href.split('?'), param, i, kv;
      if ( params[1] !== undefined && params[1].match(/sincloData/)) {
        param=params[1].split('sincloData=');
        if ( param[1] ) {
          this.tmpParams = JSON.parse(decodeURIComponent(param[1]));
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
    widgetTemplate: function(){
      var widget = window.info.widget;
      var maincolor = ( window.info.site.maincolor !== undefined ) ? window.info.site.maincolor : "#ABCD05";
      var css   = '#sincloBox * {';
          css  += '  box-sizing: border-box;';
          css  += '  background-color: rgba(0,0,0,0);';
          css  += '  border: none;';
          css  += '  border-radius: 0;';
          css  += '  padding: 0;';
          css  += '  margin: 0;';
          css  += '  line-height: 1;';
          css  += '  color: #6B6B6B;';
          css  += '}';
          css  += '#sincloBox pre, #sincloBox span {';
          css  += '  font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana;';
          css  += '}';
          css  += '#sincloBox a:hover { color: ' + maincolor + ' }';

      var html  = '<div id="sincloBox" style="box-sizing: border-box; position: fixed; height: 45px; bottom: -11px; right: 5px; border: 1.5px solid rgb(232, 231, 224); border-radius: 10px; z-index: 999998; width: 250px; overflow: hidden; background-color: rgb(255, 255, 255);">';
          html += '  <style>' + css + '</style>';
          html += '  <img onclick="sinclo.operatorInfo.ev()" style="position: absolute; top: 11.5px; right: 10px; z-index: 0;" src=" ' + window.info.site.files + '/img/yajirushi.png" height="12" width="16.5">';
          html += '  <div onclick="sinclo.operatorInfo.ev()" style="cursor: pointer; background-color: ' + maincolor + '; width: 100%; height: 35px; background-image: url( ' + window.info.site.files + '/img/call.png); background-repeat: no-repeat; background-position: 15px, 0; background-size: 4.5%; color: #FFF;">';
          html += '    <pre style="color: #FFF; text-align: center; font-size: 15px; padding: 10px; margin:  0;">' + widget.title + '</pre>'
          html += '  </div>';
          // 受付時間を表示しない
          if ( widget.display_time_flg === 0 ) {
            html += '    <div style="height: 45px; margin: 15px 10px;">';
            html += '      <span style="display: block; width: 45px; height: 45px; float: left; background-color: ' + maincolor + '; border-radius: 25px; padding: 2px;"><img width="16.5" height="30" src=" ' + window.info.site.files + '/img/call.png" style="margin: 5px 12px"></span>';
            html += '      <pre style="font-weight: bold; color: ' + maincolor + '; margin: 0 auto;font-size: 20px; text-align: center;padding: 10px 0px 0px;height: 45px;">' + widget.tel + '</pre>';
            html += '    </div>';
          }
          else {
            html += '<div style="height: 50px;margin: 15px 10px">';
            html +=   '<span style="display: block; width: 50px; height: 50px; float: left; background-color: ' + maincolor + '; border-radius: 25px; padding: 3px;"><img width="19.5" height="33" src=" ' + window.info.site.files + '/img/call.png" style="margin: 6px 12px"></span>';
            html +=   '<pre style="font-weight: bold; color: ' + maincolor + '; margin: 0 auto; font-size: 18px; text-align: center; padding: 5px 0 0; height: 30px">' + widget.tel + '</pre>';
            html +=   '<pre style="font-weight: bold; color: ' + maincolor + '; margin: 0 auto; font-size: 10px; text-align: center; padding: 0 0 5px; height: 20px">受付時間： ' + widget.time_text + '</pre>';
            html += '</div>';
          }
          html += '    <pre style="display: block; word-wrap: break-word; font-size: 11px; text-align: center; margin: auto; line-height:1.5; color: #6B6B6B; width: 20em;">' + widget.content + '</pre>';
          html += '    <span style="display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border: 1px solid rgb(188, 188, 188); border-radius: 15px">' + userInfo.accessId + '</span>';
          html += '    <p style="padding: 5px 0; text-align: center; border-top: 1px solid #DBDBDB;color: #A1A1A1!important; height: 20px; font-size: 11px;">Powered by <a target="sinclo" href="http://medialink-ml.co.jp/index.html">sinclo</a></p>';
          html += '</div>';
      return html;
    },
    makeAccessIdTag: function(){
      if ( !check.browser() ) return false;
      emit('getWidgetInfo', {});
    },
    load: {
      id: "loadingImg",
      flg: false,
      timer: null,
      start:  function(){
        window.clearTimeout(this.timer);
        var div = document.createElement('div');
        div.id = this.id;
        div.style.cssText = "position: fixed; top: 0; left: 0; bottom: 0; right: 0; background-color: rgba(255,255,255); z-index: 99999";
        document.body.appendChild(div);
        this.flg = true; // 一度接続済みというフラグを持たせる
        this.timer = window.setTimeout(function(){
          common.load.finish();
        }, 8000);
      },
      finish: function(){
        window.clearTimeout(this.timer);
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
        if (!check.isset(common.tmpParams) && !check.isset(storage.s.get('params'))) {
          ret = true;
        }
      }
      var ua  =navigator.userAgent.toLowerCase();
      // Botとスマートフォンは弾く
      if ( ua.indexOf('iphone') > 0 || ua.indexOf('ipod') > 0 || ua.indexOf('android') > 0 || ua.indexOf('bot') > 0 ) {
        ret = false;
      }
      if ( ua.match(/msie\ [1-9]\./g) ) {
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
    firstUrl: function(){
      if ( location.href.match('/sincloData\=/') ) {
        return true;
      }
      else {
        return false;
      }
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
        emit('connectSuccess', {confirm: false});
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
        delete userInfo['sendTabId'];
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
    href: location.href,
    prevList: [],
    // TODO 画面同期時セットするようにする
    scrollSize: function (){ // 全体のスクロール幅
      return {
        x: document.body.scrollWidth - window.innerWidth,
        y: document.body.scrollHeight - window.innerHeight
      }
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
    resetPrevList: function(){
      var prevList = [];
      prevList.push(this.href);
      this.prevList = prevList;
      storage.s.set('prevList', JSON.stringify(this.prevList));
    },
    setPrevList: function(){
      var prevList = [];
      if ( check.isset(storage.s.get('prevList')) ) {
        prevList = JSON.parse(storage.s.get('prevList'));
      }
      prevList.push(this.href);
      this.prevList = prevList;
      storage.s.set('prevList', JSON.stringify(this.prevList));
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
        var scrollSize = browserInfo.scrollSize();

        document.body.scrollLeft = scrollSize.x * obj.x;
        document.body.scrollTop  = scrollSize.y * obj.y;
        document.documentElement.scrollLeft = scrollSize.x * obj.x;
        document.documentElement.scrollTop  = scrollSize.y * obj.y;
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
        type: "hashchange",
        ev: function(e){
          if ( socket === undefined ) return false;
          browserInfo.href = location.href;
          emit('reqUrlChecker', {});
        }
      }
    ],
    pcResize: function(e){
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
    },
    tabletResize: function(e){
      var size = {
        width: window.innerWidth,
        height: window.innerHeight
      };
      var scroll = browserInfo.windowScroll();

      emit('syncBrowserInfo', {
        accessType: userInfo.accessType,
        // ブラウザのサイズ
        windowSize: size,
        scrollPosition: scroll
      });
    },
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
        tabId: userInfo.tabId,
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
      if ((ua.indexOf("windows") != -1 && ua.indexOf("touch") != -1)
          ||  ua.indexOf("ipad") != -1
          || (ua.indexOf("android") != -1 && ua.indexOf("mobile") == -1)
          || (ua.indexOf("firefox") != -1 && ua.indexOf("tablet") != -1)
          ||  ua.indexOf("kindle") != -1
          ||  ua.indexOf("silk") != -1
          ||  ua.indexOf("playbook") != -1)
      {
        window.addEventListener("orientationchange", syncEvent.tabletResize, false);
      }
      else {
        window.addEventListener("resize", syncEvent.pcResize, false);
      }
    },
    disabledSubmit: function(e) {
      if ( userInfo.accessType !== cnst.access_type.host ) {
        emit('requestSyncStop', {message: "お客様がsubmitボタンをクリックしましたので、\n画面共有を終了します。"});
      }
      else {
        emit('requestSyncStop', {});
        e.preventDefault();
        return false;
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

      // resizeCall
      this.resizeCall(window.navigator.userAgent.toLowerCase(), eventFlg);

      // 要素に対してのイベント操作
      var els = document.getElementsByTagName('input');
      this.ctrlElmEventListener(eventFlg, els, "focus", syncEvent.focusCall);
        // checkbox, radioボタンのイベント操作
        this.ctrlElmEventListener(eventFlg, els, "change", syncEvent.changeCall);
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

      // フォーム制御
      if ( document.forms.length > 0 ) {
        if ( ('form' in info.dataset) && String(info.dataset.form) === "1" ) {
            this.ctrlElmEventListener(eventFlg, scEls, "submit", syncEvent.disabledSubmit);
        }
      }

    },
    start: function(e){ syncEvent.change(true); },
    stop: function(e){ syncEvent.change(false); }
  };

  popup = {
      set: function(title, content){
          popup.remove();
          var maincolor = ( window.info.site.maincolor !== undefined ) ? window.info.site.maincolor : "#ABCD05";
          var hovercolor = ( window.info.site.hovercolor !== undefined ) ? window.info.site.hovercolor : "#9CB90E";
          var html = '';
          html += '<div id="sincloPopup" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999999999999;">';
          html += '  <style>';
          html += '    #sincloPopupFrame {';
          html += '        border: 1px solid #999;';
          html += '        padding: 0.5em 1em;';
          html += '        width: 30em;';
          html += '        height: 10em;';
          html += '        background-color: #EDEDED;';
          html += '        color: #3C3C3C;';
          html += '        margin: auto;';
          html += '        position: absolute;';
          html += '        top: 0;';
          html += '        left: 0;';
          html += '        right: 0;';
          html += '        bottom: 0;';
          html += '        box-shadow: 0 2px 2px rgb(188, 188, 188);';
          html += '        border-radius: 3px;';
          html += '        box-sizing: border-box;';
          html += '    }';
          html += '    sinclo-h3 {';
          html += '        font-weight: bold;';
          html += '        display: block;';
          html += '        font-size: 1.2em;';
          html += '        margin: 0.4em 0;';
          html += '    }';
          html += '    sinclo-div {';
          html += '        display: block;';
          html += '    }';
          html += '    sinclo-content {';
          html += '        display: block;';
          html += '        font-size: 1em;';
          html += '        margin: 0.5em 0;';
          html += '    }';
          html += '    #sincloPopMain {';
          html += '        height: 6em;';
          html += '    }';
          html += '    #sincloPopAct {';
          html += '        width: 100%;';
          html += '        height: 2em;';
          html += '        text-align: center;';
          html += '        padding: 0.5em 0;';
          html += '    }';
          html += '    #sincloPopAct sinclo-a {';
          html += '        background-color: #FFF;';
          html += '        padding: 5px 10px;';
          html += '        text-decoration: none;';
          html += '        color: #666;';
          html += '        border-radius: 5px;';
          html += '        border: 1px solid #BEBEBE;';
          html += '        margin: 10px;';
          html += '        font-size: 1em;';
          html += '        box-shadow: 0 0 1px rgba(75, 75, 75, 0.3);';
          html += '    }';
          html += '    #sincloPopAct sinclo-a:hover {';
          html += '        cursor: pointer;';
          html += '    }';
          html += '    #sincloPopAct sinclo-a:hover,  #sincloPopAct sinclo-a:focus {';
          html += '        outline: none;';
          html += '    }';
          html += '    #sincloPopAct sinclo-a#sincloPopupOk {';
          html += '       background-color: ' + maincolor;
          html += '    }';
          html += '    #sincloPopAct sinclo-a#sincloPopupOk:hover, #sincloPopAct sinclo-a#sincloPopupOk:focus {';
          html += '       background-color: ' + hovercolor;
          html += '    }';
          html += '    #sincloPopAct sinclo-a#sincloPopupNg {';
          html += '       background-color: #FFF';
          html += '    }';
          html += '    #sincloPopAct sinclo-a#sincloPopupNg:hover, #sincloPopAct sinclo-a#sincloPopupNg:focus {';
          html += '       background-color: ##DCDCDC';
          html += '    }';
          html += '  </style>';
          html += '  <sinclo-div id="sincloPopupFrame">';
          html += '    <sinclo-div id="sincloPopMain">';
          html += '      <sinclo-h3>' + title + ':</sinclo-h3><sinclo-content>' + content + '</sinclo-content>';
          html += '    </sinclo-div>';
          html += '    <sinclo-div id="sincloPopAct">';
          html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupOk" onclick="popup.ok()">許可する</sinclo-a>';
          html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupNg" onclick="popup.no()">許可しない</sinclo-a>';
          html += '    </sinclo-div>';
          html += '  </sinclo-div>';
          html += '</sinclo-div>';

          $("body").append(html);
      },
      remove: function(){
          var elm = document.getElementById('sincloPopup');
          if (elm) {
            elm.parentNode.removeChild(elm);
          }
      },
      ok: function(){ return true; },
      no: function(){ this.remove() }
  };

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

    socket.on('syncStop', function(d){
      sinclo.syncStop(d);
    }); // socket-on: syncStop

    socket.on('disconnect', function(data) {
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox ) {
        sincloBox.parentNode.removeChild(sincloBox);
      }
      popup.remove();
    });
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
  data.subWindow = false;
  data.tabId = userInfo.tabId;
  data.prevList = browserInfo.prevList;
  data.accessType = userInfo.accessType;
  data.connectToken = userInfo.get(cnst.info_type.connect);
  if ( check.isset(storage.s.get('params')) || userInfo.accessType === cnst.access_type.host ) {
    data.subWindow = true;
    data.responderId = common.params.responderId;
  }
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
