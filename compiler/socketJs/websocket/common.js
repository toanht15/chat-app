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
    createWidget: function(){
      var widget = window.info.widget, displaySet = "";
      var css = this.widgetCssTemplate(widget),
          header = this.widgetHeaderTemplate(widget),
          navi = this.widgetNaviTemplate(widget),
          chat = this.chatWidgetTemplate(widget),
          call = this.widgetTemplate(widget),
          fotter = '<p id="fotter">Powered by <a target="sinclo" href="http://medialink-ml.co.jp/index.html">sinclo</a></p>';

      if ( widget.contract.chat && widget.contract.synclo ) {
        displaySet += navi + chat + call;
      }
      else {
        if ( widget.contract.chat ) {
          displaySet += chat;
        }
        if ( widget.contract.synclo ) {
          displaySet += call;
        }
      }
      return "<sinclo id='sincloBox' data-flg='false' >" + css + header + displaySet + fotter + "</sinclo>";
    },
    widgetCssTemplate: function(widget){
      var html = "", faintColor = widget.mainColor;
      if ( faintColor.indexOf("#") >= 0 ) {
        var code = faintColor.substr(1), r,g,b;
        if (code.length === 3) {
          r = String(code.substr(0,1)) + String(code.substr(0,1));
          g = String(code.substr(1,1)) + String(code.substr(1,1));
          b = String(code.substr(2)) + String(code.substr(2));
        }
        else {
          r = String(code.substr(0,2));
          g = String(code.substr(2,2));
          b = String(code.substr(4));
        }
        faintColor = "rgba(" + parseInt(r,16) + ", " + parseInt(g,16) + ", " + parseInt(b,16) + ", 0.1)";
      }

      // 表示位置
      var showPosition = "";
      switch ( widget.showPosition ) {
        case 1: // 右下
          showPosition = "bottom: 0; right: 10px;";
          break;
        case 2:
          showPosition = "bottom: 0; left: 10px;";
          break;
      }

      html += '  <style>';
      html += '      #sincloBox { position: fixed; ' + showPosition + ' z-index: 999998; background-color: rgba(0,0,0,0); display: block; width: 270px; }';
      html += '      #sincloBox * { box-sizing: border-box; font-size: 12px; font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana;}';
      html += '      #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }';
      html += '      #sincloBox section { width: 270px; border: 1px solid #E8E7E0; background-color: #FFF; border-top: none;}';
      html += '      #sincloBox p#widgetTitle { cursor:pointer; border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px 0 0; border: 1px solid ' + widget.mainColor + '; border-bottom:none; background-color: ' + widget.mainColor + ';text-align: left; font-size: 14px;padding: 7px; padding-left: 77px; margin: 0;color: #FFF; height: 32px }';
      html += '      #sincloBox p#widgetSubTitle { background-color: #FFF; margin: 0; padding: 7px 0; text-align: left; border-width: 0 1px 0 1px; border-color: #E8E7E0; border-style: solid; padding-left: 77px; font-weight: bold; color: ' + widget.mainColor + '; height: 29px }';
      html += '      #sincloBox p#widgetDescription { background-color: #FFF; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: #E8E7E0; border-style: solid; padding-left: 77px; height: 23px; color: #8A8A8A; }';
      html += '      #sincloBox section { display: inline-block; width: 270px; border: 1px solid #E8E7E0; border-top: none; }';
      html += '      #sincloBox section#navigation { border-width: 0 1px; height: 40px; position: relative; display: block; }';
      html += '      #sincloBox section#navigation ul { margin: 0 0 0 -1px; display: table; padding: 0; position: absolute; top: 0; left: 0; height: 40px; width: 270px }';
      html += '      #sincloBox section#navigation ul li { position: relative; overflow: hidden; cursor: pointer; color: #8A8A8A; width: 50%; text-align: center; display: table-cell; padding: 10px 0; border-left: 1px solid #E8E7E0; height: 40px }';
      html += '      #sincloBox section#navigation ul li:last-child { border-right: 1px solid #E8E7E0; }';
      html += '      #sincloBox section#navigation ul li.selected { background-color: #FFFFFF; }';
      html += '      #sincloBox section#navigation ul li:not(.selected) { border-bottom: 1px solid #E8E7E0 }';
      html += '      #sincloBox section#chatTab { padding-top: 5px;  }';
      // チャットも画面同期も使用する際にはデフォルトで画面同期ウィジェットの表示をnoneにする
      if ( widget.contract.chat && widget.contract.synclo ) {
        html += '      #sincloBox section#callTab { display: none; }';
      }
      html += '      #sincloBox ul#chatTalk { width: 100%; height: 250px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}';
      html += '      #sincloBox ul#chatTalk li { margin: 5px 0; padding: 5px; font-size: 12px; border: 1px solid #C9C9C9; color: #595959; white-space: pre; color: #8A8A8A; }';
      html += '      #sincloBox ul#chatTalk li.sinclo_se { border-radius: 5px 5px 0; margin-left: 10px; background-color: #FFF; }';
      html += '      #sincloBox ul#chatTalk li.sinclo_re { margin-right: 10px; border-radius: 5px 5px 5px 0; background-color:' + faintColor + ' }';
      html += '      #sincloBox section#chatTab textarea { padding: 5px; resize: none; width: 100%; height: 50px; border: 1px solid #E4E4E4; border-radius: 5px; color: #8A8A8A; }';
      html += '      #sincloBox section#navigation ul li.selected::after{ content: " "; border-bottom: 2px solid ' + widget.mainColor + '; position: absolute; bottom: 0px; left: 5px; right: 5px;}';
      html += '      #sincloBox #fotter { padding: 5px 0;text-align: center;border: 1px solid #E8E7E0;color: #A1A1A1!important; background-color: #FFF; font-size: 11px;margin: 0;border-top: none; }';
      html += '  </style>';

      return html;
    },
    widgetHeaderTemplate: function(widget){
      var html = "";
      // 画像
      html += '  <span style="position: absolute; top: 7px; left: 7px;"><img src="' + window.info.site.files + '/img/chat_sample_picture.png" width="62" height="70" alt="チャット画像"></span>';
      html += '  <div id="widgetHeader" >';
      // タイトル
      html += '    <p id="widgetTitle" onclick="sinclo.operatorInfo.ev()">' + widget.title + '</p>';
      // サブタイトル
      html += '    <p id="widgetSubTitle">' + widget.subTitle + '</p>';
      // 説明文
      html += '    <p id="widgetDescription">' + widget.description + '</p>';
      html += '  </div>';
      return html;
    },
    widgetNaviTemplate: function(widget){
      var html = "";
      html += '  <section id="navigation">';
      html += '    <ul>';
      html += '        <li data-tab="chat" class="widgetCtrl selected">チャットでの受付</li>';
      html += '        <li data-tab="call" class="widgetCtrl" >電話での受付</li>';
      html += '    </ul>';
      html += '  </section>';
      return html;
    },
    widgetTemplate: function(widget){
      var html = "";
      html += '<section id="callTab">';
      html += '    <div style="height: 50px;margin: 15px 25px">';
      // アイコン
      html += '    <span style="display: block; width: 50px; height: 50px; float: left; background-color: ' + widget.mainColor + '; border-radius: 25px; padding: 3px;"><img width="19.5" height="33" src="' + window.info.site.files + '/img/call.png" style="margin: 6px 12px"></span>';
      // 受付電話番号
      html += '    <pre id="telNumber" style="font-weight: bold; color: ' + widget.mainColor + '; margin: 0 auto; font-size: 18px; text-align: center; padding: 5px 0px 0px; height: 30px">' + widget.tel + '</pre>';
      // 受付時間
      html += '    <pre ng-if="display_time_flg == \'1\'" style="font-weight: bold; color: ' + widget.mainColor + '; margin: 0 auto; font-size: 10px; text-align: center; padding: 0 0 5px; height: 20px">受付時間： ' + widget.time_text + '</pre>';
      html += '    </div>';
      // テキスト
      html += '    <pre style="display: block; word-wrap: break-word; font-size: 11px; text-align: center; margin: auto; line-height: 1.5; color: #6B6B6B; width: 20em;">' + widget.content + '</pre>';
      html += '    <span style="display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border: 1px solid rgb(188, 188, 188); border-radius: 15px">' + userInfo.accessId + '</span>';
      html += '</section>';
      return html;
    },
    chatWidgetTemplate: function(widget){
      var html = "";
      html += '  <section id="chatTab">';
      html += '    <ul id="chatTalk"></ul>';
      html += '    <div style="border-top: 1px solid #E8E7E0; padding: 10px;">';
      html += '    <textarea name="sincloChat" id="sincloChatMessage" maxlength="300" placeholder="メッセージ入力後、Enterで送信"></textarea>';
      html += '    </div>';
      html += '  </section>';
      return html;
    },
    makeAccessIdTag: function(){
      if ( !check.browser() ) return false;
      if ( !('widget' in window.info) ) return false;
      window.info.widgetDisplay = false; // デフォルト表示しない
      // ウィジェットを常に表示する
      if ( !('display_type' in window.info.widget) && window.info.widget.display_type === 1 ) {
        window.info.widgetDisplay = true;
      }
      // オペレーターの数に応じて表示する
      else if ( window.info.widget.display_type === 2 ) {
        if ( window.info.active_operator_cnt > 0 ) {
          window.info.widgetDisplay = true;
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

      common.load.finish();
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox ) {
        sincloBox.parentNode.removeChild(sincloBox);
      }
      if ( userInfo.accessType !== cnst.access_type.host ) {
      var html = common.createWidget();
        $('body').append(html);
        common.sincloBoxHeight = $("#sincloBox").outerHeight(true);
        $("#sincloBox").outerHeight(85);

        $(".widgetCtrl").click(function(){
            var target = $(".widgetCtrl.selected"), clickTab = $(this).data('tab');
            target.removeClass("selected");
            common.sincloBoxHeight = $("#sincloBox").outerHeight(true);
            $("#sincloBox").attr("style", "");

            if ( clickTab === "call" ) {
              $("#callTab").css('display', 'inline-block');
              $("#chatTab").hide();
            }
            else {
              $("#callTab").hide();
              $("#chatTab").css('display', 'inline-block');
            }
            $(this).addClass("selected");
        });
        // チャット情報読み込み
        sinclo.chatApi.init();
        if ( ('maxShowTime' in window.info.widget) && String(window.info.widget.maxShowTime).match(/^[0-9]{1,2}$/) !== null ) {
          var maxShowTime = Number(window.info.widget.maxShowTime) * 1000;
          var widgetOpen = storage.s.get('widgetOpen');
          if (!widgetOpen) {
            window.setTimeout(function(){
              if ( !sinclo.operatorInfo.flg ) {
                storage.s.set('widgetOpen', true);
                sinclo.operatorInfo.flg = true;
                $("#sincloBox").animate({
                  'height':  (common.sincloBoxHeight) + 'px'
                }, 'first');
              }
            }, maxShowTime);
          }
        }
        emit('syncReady', {widget: window.info.widgetDisplay});
      }

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
      // トークン初期化
      common.token_add();
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
        chatCnt: document.getElementsByClassName('sinclo_se').length,
        chatUnread: {id: null, cnt: 0},
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
      if (
          ( (ua.indexOf("windows") != -1 && ua.indexOf("touch") != -1)
            ||  ua.indexOf("ipad") != -1
            || (ua.indexOf("android") != -1 && ua.indexOf("mobile") == -1)
            || (ua.indexOf("firefox") != -1 && ua.indexOf("tablet") != -1)
            ||  ua.indexOf("kindle") != -1
            ||  ua.indexOf("silk") != -1
            ||  ua.indexOf("playbook") != -1
          )
          && 'orientationchange' in window
        )
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
          var maincolor = ( window.info.widget.mainColor !== undefined ) ? window.info.widget.mainColor : "#ABCD05";
          var hovercolor = ( window.info.site.hovercolor !== undefined ) ? window.info.site.hovercolor : "#9CB90E";
          var html = '';
          html += '<div id="sincloPopup" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999999999999;">';
          html += '  <style>';
          html += '    #sincloPopupFrame {';
          html += '        border: 0.15em solid #ABABAB;';
          html += '        width: 30em;';
          html += '        opacity: 0;';
          html += '        background-color: #EDEDED;';
          html += '        color: #3C3C3C;';
          html += '        margin: auto;';
          html += '        position: absolute;';
          html += '        top: 0;';
          html += '        left: 0;';
          html += '        right: 0;';
          html += '        bottom: 0;';
          html += '        box-shadow: 0 35px 42px rgba(141, 141, 141, 0.8);';
          html += '        border-radius: 5px;';
          html += '        box-sizing: border-box;';
          html += '    }';
          html += '    #sincloPopBar {';
          html += '        height: 1.85em;';
          html += '        background: linear-gradient(#EDEDED, #D2D2D2);';
          html += '        border-bottom: 0.15em solid #989898;';
          html += '        border-radius: 5px 5px 0 0;';
          html += '    }';
          html += '    #sincloLogo {';
          html += '        padding: 1em;';
          html += '    }';
          html += '    #sincloMessage {';
          html += '        padding-right: 1em;';
          html += '    }';
          html += '    sinclo-h3 {';
          html += '        font-weight: bold;';
          html += '        display: block;';
          html += '        font-size: 1.2em;';
          html += '        height: 1.2em;';
          html += '        margin: 0.4em 0;';
          html += '    }';
          html += '    sinclo-div {';
          html += '        display: block;';
          html += '    }';
          html += '    sinclo-content {';
          html += '        display: block;';
          html += '        font-size: 0.9em;';
          html += '        margin: 0.5em 0;';
          html += '        line-height: 2em;';
          html += '    }';
          html += '    #sincloPopMain {';
          html += '        display: table;';
          html += '    }';
          html += '    sinclo-div#sincloPopMain sinclo-div {';
          html += '        display: table-cell;';
          html += '        vertical-align: top;';
          html += '    }';
          html += '    #sincloPopAct {';
          html += '        width: 100%;';
          html += '        height: 2em;';
          html += '        text-align: center;';
          html += '        padding: 0.5em 0;';
          html += '        box-sizing: content-box;';
          html += '    }';
          html += '    #sincloPopAct sinclo-a {';
          html += '        background-color: #FFF;';
          html += '        padding: 5px 10px;';
          html += '        text-decoration: none;';
          html += '        border-radius: 5px;';
          html += '        border: 1px solid #959595;';
          html += '        margin: 10px;';
          html += '        font-size: 1em;';
          html += '        box-shadow: 0 0 2px rgba(75, 75, 75, 0.3);';
          html += '        font-weight: bold;';
          html += '    }';
          html += '    #sincloPopAct sinclo-a:hover {';
          html += '        cursor: pointer;';
          html += '    }';
          html += '    #sincloPopAct sinclo-a:hover,  #sincloPopAct sinclo-a:focus {';
          html += '        outline: none;';
          html += '    }';
          html += '    #sincloPopAct sinclo-a#sincloPopupOk {';
          html += '       background: linear-gradient(to top, #D5FAFF, #80BEEA, #D5FAFF);';
          html += '    }';
          html += '    #sincloPopAct sinclo-a#sincloPopupNg {';
          html += '       background-color: #FFF';
          html += '    }';
          html += '    #sincloPopAct sinclo-a#sincloPopupNg:hover, #sincloPopAct sinclo-a#sincloPopupNg:focus {';
          html += '       background-color: ##DCDCDC';
          html += '    }';
          html += '  </style>';
          html += '  <sinclo-div id="sincloPopupFrame">';
          html += '    <sinclo-div id="sincloPopBar">';
          html += '    </sinclo-div>';
          html += '    <sinclo-div id="sincloPopMain">';
          html += '      <sinclo-div id="sincloLogo"><img src="' + info.site.files + '/img/mark.png" width="60" height="60"></sinclo-div>';
          html += '      <sinclo-div id="sincloMessage">';
          html += '          <sinclo-h3>' + title + ':</sinclo-h3><sinclo-content>' + content + '</sinclo-content>';
          html += '      </sinclo-div>';
          html += '    </sinclo-div>';
          html += '    <sinclo-div id="sincloPopAct">';
          html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupOk" onclick="popup.ok()">許可する</sinclo-a>';
          html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupNg" onclick="popup.no()">許可しない</sinclo-a>';
          html += '    </sinclo-div>';
          html += '  </sinclo-div>';
          html += '</sinclo-div>';

          $("body").append(html);

          var height = 0;
          $("#sincloPopupFrame > sinclo-div").each(function(e){
            height += this.offsetHeight;
          });

          $("#sincloPopupFrame").height(height).css("opacity", 1);
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

    socket.on('setInitInfo', function (d) {
      sinclo.setInitInfo(d);
    }); // socket-on: setInitInfo

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

    socket.on('disconnect', function(data) {
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox ) {
        sincloBox.parentNode.removeChild(sincloBox);
      }
      popup.remove();
    });

        $.ajax({
    	    type: 'get',
            url: window.info.site.files + "/settings/",
            data: {
                'sitekey': window.info.site.key
            },
            dataType: "json",
            success: function(json){
                window.info.widget = json.widget;
                window.info.messages = json.messages;
                window.info.contract = json.contract;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                  $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
                  $("#textStatus").html("textStatus : " + textStatus);
                  $("#errorThrown").html("errorThrown : " + errorThrown.message);
            }
        });
  };

  var timer = window.setInterval(function(){
    if ( io !== "" && sinclo !== "" ) {
      window.clearInterval(timer);
      init();
    }
  }, 200);

}(sincloJquery));

function f_url(url){
  var re = /(\?|&)?sincloData=/;
  var num =  url.search(re);
  if ( num < 0 ) {
    return browserInfo.href;
  }
  return url.substr(0,num);
}

function emit(evName, data){
  data.userId = userInfo.userId;
  data.accessId = userInfo.accessId;
  data.token = common.token;
  data.title = common.title();
  data.siteKey = info.site.key;
  data.url= f_url(browserInfo.href);
  data.subWindow = false;
  data.tabId = userInfo.tabId;
  data.prevList = browserInfo.prevList;
  data.accessType = userInfo.accessType;
  data.chat = null;
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
