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
    vcPopup, // ビデオ表示用ポップアップ関連関数
    sinclo, // リアルタイム通信補助関数
    sincloVideo; // ビデオ通信補助関数

(function($){
  cnst = {
    access_type: {
      guest: 1,
      host: 2
    },
    tab_type: {
      open: 1,
      close: 2,
      none:3
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

  common = {
    n: 20,
    str: "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890",
    token: null,
    cursorTag: null,
    params : {},
    tmpParams : {},
    vcInfo : {}, // ビデオチャット用のセッション情報
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
    fullDateTime: function(parse){
      function _numPad(str){
        return ("0" + str).slice(-2);
      }
      var d = ( check.isset(parse) ) ? new Date(Number(parse)) : new Date();
      return d.getFullYear() + "-" + _numPad(d.getMonth() + 1) + "-" + _numPad(d.getDate()) + " " + _numPad(d.getHours()) + ":" + _numPad(d.getMinutes()) + ":" + _numPad(d.getSeconds()) + "." + Number(String(d.getMilliseconds()).slice(0,2));
    },
    formatDateParse: function(parse){
      function _numPad(str){
        return ("0" + str).slice(-2);
      }
      var d = ( check.isset(parse) ) ? new Date(Number(parse)) : new Date();
      return d.getFullYear() + "/" + _numPad(d.getMonth() + 1) + "/" + _numPad(d.getDate()) + " " + _numPad(d.getHours()) + ":" + _numPad(d.getMinutes()) + ":" + _numPad(d.getSeconds());
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
    // ==========
    // ビデオ用情報保存
    // ==========
    saveVcInfo: function(){
      storage.s.set('vcInfo', JSON.stringify(this.vcInfo));
    },
    getVcInfo: function(){
      return JSON.parse(storage.s.get('vcInfo')) || undefined;
    },
    setVcInfo: function(obj){
      this.vcInfo = obj;
    },
    unsetVcInfo: function(){
      storage.s.unset('vcInfo');
    },
    // ==========
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
    createWidget: function(){
      var widget = window.info.widget, displaySet = "";
      var css = this.widgetCssTemplate(widget),
          header = this.widgetHeaderTemplate(widget),
          navi = this.widgetNaviTemplate(widget),
          chat = this.chatWidgetTemplate(widget),
          call = this.widgetTemplate(widget),
          fotter = '<p id="fotter">Powered by <a target="sinclo" href="http://medialink-ml.co.jp/index.html">sinclo</a></p>';

      // フルプランのPCの場合
      if ( window.info.contract.chat && window.info.contract.synclo && !check.smartphone() ) {
        displaySet += navi + chat + call;
      }
      // フルプランのSPの場合はチャットのみ表示
      else if ( window.info.contract.chat && window.info.contract.synclo && check.smartphone() ) {
        displaySet += chat;
      }
      else {
        // チャットのみ契約の場合
        if ( window.info.contract.chat ) {
          displaySet += chat;
        }
        // 画面同期のみ契約の場合
        if ( window.info.contract.synclo ) {
          displaySet += call;
        }
      }
      return "<sinclo id='sincloBox' >" + css + header + displaySet + fotter + "</sinclo>";
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
      var showPosition = "", chatPosition = {se: "", re: ""};
      switch ( Number(widget.showPosition) ) {
        case 1: // 右下
          showPosition = "bottom: 0; right: 10px;";
          chatPosition = {
            se: "border-bottom-left-radius: 0; margin-right: 10px;",
            re: "border-bottom-right-radius: 0; margin-left: 10px;"
          };
          break;
        case 2:
          showPosition = "bottom: 0; left: 10px;";
          chatPosition = {
            se: "border-bottom-right-radius: 0; margin-left: 10px;",
            re: "border-bottom-left-radius: 0; margin-right: 10px;"
          };
          break;
      }

      // 基本設定
      var widgetWidth = 285, ratio = 1;
      // ユーザーエージェント
      var ua  =  navigator.userAgent.toLowerCase();

      html += '  <style>';

      /* 共通スタイル */
      html += '      @media print{ sinclo { display:none!important; } }';
      html += '      #sincloBox { display: none; position: fixed; ' + showPosition + ' z-index: 999998; background-color: rgba(0,0,0,0); }';
      html += '      #sincloBox * { color: #8A8A8A; line-height: 1.3; box-sizing: border-box; font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana;}';
      html += '      #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }';
      html += '      #sincloBox a { color: #8a8a8a }';
      html += '      #sincloBox a:hover { color: ' + widget.mainColor + '; }';
      html += '      #sincloBox div#widgetHeader { cursor:pointer; position: relative;}';
      html += '      #sincloBox div#widgetHeader:after { content: " "; position: absolute; bottom: 0; left: 0; right: 0; z-index: -1; background-color: #FFF; }';
      html += '      #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }';
      html += '      #sincloBox span#mainImage { cursor:pointer; z-index: 2; position: absolute; }';
      html += '      #sincloBox span#mainImage img { background-color: ' + widget.mainColor + ' }';
      html += '      #sincloBox p#widgetTitle { position:relative; cursor:pointer; border-bottom:none; background-color: ' + widget.mainColor + ';text-align: center; margin: 0; color: ' + widget.stringColor + ' }';
      html += '      #sincloBox p#widgetTitle #sincloChatUnread { position: absolute; top: 0; left: 0; color: #FFF; font-style: normal; text-align: center; font-weight: bold; background-color: #FF5C5C; }';
      html += '      #sincloBox p#widgetTitle:after { background-image: url("' + window.info.site.files + '/img/widget/yajirushi.png"); content: " "; display: inline-block; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; -webkit-transition: 200ms linear; transition: transform 200ms linear}';
      html += '      #sincloBox[data-openflg="true"] p#widgetTitle:after { -ms-transform: rotate(0deg); -moz-transform: rotate(0deg); -webkit-transform: rotate(0deg); -o-transform: rotate(0deg); transform: rotate(0deg); }';
      html += '      #sincloBox[data-openflg="false"] p#widgetTitle:after { -ms-transform: rotate(180deg); -moz-transform: rotate(180deg); -webkit-transform: rotate(180deg); -o-transform: rotate(180deg); transform: rotate(180deg); }';
      html += '      #sincloBox > section { background-color: #FFF; border-top: none; }';
      html += '      #sincloBox ul#chatTalk li a, #sincloBox #fotter a {  text-decoration: underline; }';
      html += '      #sincloBox section { display: none }';
      html += '      #sincloBox .flexBox { display: -ms-flexbox; display: -webkit-flex; display: flex; -ms-flex-direction: column; -webkit-flex-direction: column; flex-direction: column }';
      html += '      #sincloBox .flexBoxRow { display: -ms-flexbox; display: -webkit-flex; display: flex; -ms-flex-direction: row; -webkit-flex-direction: row; flex-direction: row }';

      // チャットを使用する際
      if ( window.info.contract.chat ) {
        html += '      #sincloBox #mainImage em { position: absolute; background-image: url("' + window.info.site.files + '/img/chat-bg.png");background-size: contain;background-repeat: no-repeat; color: #FFF; font-style: normal; text-align: center; font-weight: bold }';
        html += '      #sincloBox ul#chatTalk { width: 100%; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0; clear: both!important; }';
        html += '      #sincloBox ul sinclo-chat { clear: both!important } ';
        html += '      #sincloBox ul#chatTalk li { word-wrap: break-word; word-break: break-word; white-space: pre-wrap!important; background-color: #FFF; white-space: pre; color: #8A8A8A; }';
        html += '      #sincloBox ul#chatTalk li.sinclo_se { ' + chatPosition.se + 'background-color: #FFF; }';
        html += '      #sincloBox ul#chatTalk li.sinclo_re { ' + chatPosition.re + 'background-color:' + faintColor + ' }';
        html += '      #sincloBox ul#chatTalk li#sinclo_typeing_message { position: relative; color: #d5d5d5; border: none; text-align: center; }';
        html += '      #sincloBox ul#chatTalk li#sinclo_typeing_message span { position: absolute; top: 0; bottom: 0; left: 50%; display: block; }';
        html += '      #sincloBox ul#chatTalk li span.cName { display: block; color: ' + widget.mainColor + '; font-weight: bold; }';
        html += '      #sincloBox ul#chatTalk li.sinclo_etc { border: none; text-align: center; margin: 0 auto; font-weight: bold }';
        html += '      #sincloBox section#chatTab div { border-top: 1px solid #E8E7E0; }';
        html += '      #sincloBox section#chatTab div #sincloChatMessage { display: block; height: 100%; margin: 0; width: 80%; resize: none; color: #8A8A8A!important; border-right-color: ' + widget.mainColor + '!important; }';
        html += '      #sincloBox section#chatTab div #sincloChatMessage:focus { outline: none; border-color: ' + widget.mainColor + '!important }';
        html += '      #sincloBox section#chatTab div #sincloChatSendBtn { display: block; height: 100%; width: 20%; text-decoration: none; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0; text-align: center; background-color: ' + widget.mainColor + '; color: ' + widget.stringColor + '; font-weight: bold; font-size: 1.2em;}';
        html += '      #sincloBox section#chatTab div #sincloChatSendBtn span { color: ' + widget.stringColor + '; }';
      }
      html += '      #sincloBox section#navigation { position: relative; display: block; }';
      html += '      #sincloBox section#navigation ul { display: table; padding: 0; position: absolute; top: 0; left: 0; }';
      html += '      #sincloBox section#navigation ul li { position: relative; overflow: hidden; cursor: pointer; color: #8A8A8A; text-align: center; display: table-cell; }';
      html += '      #sincloBox section#navigation ul li.selected { background-color: #FFFFFF; }';
      html += '      #sincloBox #fotter { text-align: center; color: #A1A1A1!important; background-color: #FFF; margin: 0;border-top: none; }';
      html += '      #sincloBox section#navigation ul li::before{ background-color: #BCBCBC; content: " "; display: inline-block; position: relative; background-size: contain; vertical-align: middle; background-repeat: no-repeat }';
      html += '      #sincloBox section#navigation ul li.selected::after{ content: " "; position: absolute; bottom: 0px; }';
      html += '      #sincloBox section#navigation ul li[data-tab="call"]::before{ background-image: url("' + window.info.site.files + '/img/widget/icon_tel.png"); }';
      html += '      #sincloBox section#navigation ul li[data-tab="chat"]::before{ background-image: url("' + window.info.site.files + '/img/widget/icon_chat.png"); }';
      html += '      #sincloBox section#navigation ul li.selected::before{ background-color: ' + widget.mainColor + '; }'

// html += '    #sincloBox ul { clear: both; display: flex; flex-direction: column } ';
// html += '    #sincloBox sinclo-chat, #sincloBox sinclo-typing { display: block; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; } ';

      /* iPhone/iPod/Androidの場合 */
      if ( check.smartphone() ) {
        // TODO 関数化
        widgetWidth = $(window).width() - 20;
        ratio = widgetWidth * (1/285);

        html += '#sincloBox { -webkit-transition: 100ms linear 0ms;  transition: opacity 100ms linear 0ms; }';
        html += '#sincloBox section#chatTab div { position: relative }';
        html += '#sincloBox section#chatTab sinclo-chat-alert { position: absolute; background-color: rgba(0,0,0,0.6); color: #FFF; text-align: center; }';
        if ( sinclo.chatApi.sendErrCatchFlg ) {
          html += '#sincloBox section#chatTab sinclo-chat-alert { display:block }';
        }
        else {
          html += '#sincloBox section#chatTab sinclo-chat-alert { display:none }';
        }

        /* 縦の場合 */
        if ( $(window).height() > $(window).width() ) {
          html += '#sincloBox { width: ' + widgetWidth + 'px }';
          html += '#sincloBox * { font-size: ' + (12 * ratio) + 'px; }';
          html += '#sincloBox section { width: ' + widgetWidth + 'px }';
          html += '#sincloBox section#navigation ul { width: ' + widgetWidth + 'px }';
          html += '#sincloBox span#mainImage { top: ' + (7 * ratio) + 'px; left: ' + (10 * ratio) + 'px; }';
          html += '#sincloBox div#widgetHeader:after { top: ' + (32 * ratio) + 'px }';
          html += '#sincloBox p#widgetTitle { border-radius: ' + (widget.radiusRatio * ratio) + 'px ' + (widget.radiusRatio * ratio) + 'px 0 0; border: ' + (1 * ratio) + 'px solid ' + widget.mainColor + '; font-size: ' + (14 * ratio) + 'px; padding: ' + (7 * ratio) + 'px ' + (30 * ratio) + 'px; height: ' + (32 * ratio) + 'px; }';
          html += '#sincloBox p#widgetTitle #sincloChatUnread { width: ' + (25 * ratio) + 'px; height: ' + (25 * ratio) + 'px; font-size: ' + (13 * ratio) + 'px; border-radius: ' + (15 * ratio) + 'px; margin: ' + (2.5 * ratio) + 'px ' + (6 * ratio) + ' px; padding: ' + (3 * ratio) + 'px; }';
          html += '#sincloBox p#widgetTitle:after { background-position-y: ' + (3 * ratio) + 'px; top: ' + (6 * ratio) + 'px; right: ' + (10 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (20 * ratio) + 'px; height: ' + (20 * ratio) + 'px; }';
          html += '#sincloBox p#widgetSubTitle { background-color: #FFF; border-color: #E8E7E0; font-weight: bold; color: ' + widget.mainColor + '; border-style: solid; text-align: left; margin: 0; padding: ' + (7 * ratio) + 'px 0; border-width: 0 ' + (1 * ratio) + 'px 0 ' + (1 * ratio) + 'px; padding-left: ' + (77 * ratio) + 'px; height: ' + (29 * ratio) + 'px; }';
          html += '#sincloBox p#widgetDescription { background-color: #FFF; margin: 0; padding-bottom: ' + (7 * ratio) + 'px; border-width: 0 ' + (1 * ratio) + 'px ' + (1 * ratio) + 'px ' + (1 * ratio) + 'px; padding-left: ' + (77 * ratio) + 'px; height: ' + (23 * ratio) + 'px; text-align: left; border-color: #E8E7E0; border-style: solid; color: #8A8A8A; }';
          html += '#sincloBox section { border: ' + (1 * ratio) + 'px solid #E8E7E0; border-top: none }';
          // 画像がセットされている場合のスタイル
          if ( String(widget.showMainImage) !== "2" ) {
            html += '#sincloBox p#widgetTitle { padding-left: ' + (70 * ratio) + 'px; }';
          }
          html += '#sincloBox #mainImage em { top: -' + (10 * ratio) + 'px; right: -' + (10 * ratio) + 'px; width: ' + (25 * ratio) + 'px; height: ' + (20 * ratio) + 'px; font-size: ' + (11 * ratio) + 'px; padding: ' + (1 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk { padding: ' + (5 * ratio) + 'px; height: ' + (194 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk li { border-radius: ' + (5 * ratio) + 'px; margin: ' + (5 * ratio) + 'px 0; padding: ' + (5 * ratio) + 'px; font-size: ' + (12 * ratio) + 'px; border: ' + (1 * ratio) + 'px solid #C9C9C9; }';
          html += '#sincloBox ul#chatTalk li sinclo-radio { margin: 0.25em 0 0.25em 0.5em; display: inline-block; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] { margin-right: 0.5em } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio * { webkit-transform: scale(' + (1 * ratio) + '); transform: scale(' + (1 * ratio) + '); moz-transform: scale(' + (1 * ratio) + '); } ';
          html += '#sincloBox ul#chatTalk li.sinclo_se { margin-right: ' + (10 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk li.sinclo_re { margin-left: ' + (10 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk li span.cName { font-size: ' + (13 * ratio) + 'px; margin: 0 0 ' + (5 * ratio) + 'px 0; }';
          html += '#sincloBox section#chatTab div { height: ' + (60*ratio) + 'px; padding: ' + (5 * ratio) + 'px; }';
          html += '#sincloBox section#chatTab #sincloChatMessage { border-radius: ' + (5 * ratio) +'px 0 0 ' + (5 * ratio) +'px!important; font-size: 1em; padding: ' + (5 * ratio) + 'px; border: ' + (1 * ratio) + 'px solid #E4E4E4!important; }';
          html += '#sincloBox section#chatTab #sincloChatSendBtn { padding:  ' + (16.5 * ratio) + 'px 0; border: ' + ratio + 'px solid ' + widget.mainColor + '; }';
          html += '#sincloBox section#chatTab div sinclo-chat-alert { top: ' + (5 * ratio) + 'px; left: ' + (5 * ratio) + 'px; right: ' + (5 * ratio) + 'px; bottom: ' + (5 * ratio) + 'px; border-radius: ' + (5 * ratio) + 'px; color: #FFF; padding: ' + (10 * ratio) + 'px 0; }';
          html += '#sincloBox section#navigation { border-width: 0 ' + (1 * ratio) + 'px; height: ' + (40 * ratio) + 'px; }';
          html += '#sincloBox section#navigation ul { margin: 0 0 0 -' + (1 * ratio) + 'px; height: ' + (40 * ratio) + 'px; }';
          html += '#sincloBox section#navigation ul li { padding: ' + (10 * ratio) + 'px 0; border-left: ' + (1 * ratio) + 'px solid #E8E7E0; height: ' + (40 * ratio) + 'px;  }';
          html += '#sincloBox section#navigation ul li:last-child { border-right: ' + (1 * ratio) + 'px solid #E8E7E0; }';
          html += '#sincloBox section#navigation ul li:not(.selected) { border-bottom: ' + (1 * ratio) + 'px solid #E8E7E0; }';
          html += '#sincloBox section#navigation ul li.selected::after { border-bottom: ' + (2 * ratio) + 'px solid ' + widget.mainColor + '; left: ' + (5 * ratio) + 'px; }';
          html += '#sincloBox #fotter { padding: ' + (5 * ratio) + 'px 0; border: ' + (1 * ratio) + 'px solid #E8E7E0; font-size: ' + (11 * ratio) + 'px; border-top: none;}';
          html += '#sincloBox section#navigation ul li::before { margin-right: ' + (5 * ratio) + 'px; width: ' + (18 * ratio) + 'px; height: ' + (18 * ratio) + 'px; }';
        }
        /* 横の場合 */
        else {
          var chatAreaHeight = window.innerHeight * (document.body.clientWidth / window.innerWidth);
          var hRatio = chatAreaHeight * 0.07;
          html += '#sincloBox { left:0; right:0; bottom: 0; }';
          html += '#sincloBox * { font-size: ' + hRatio + 'px }';
          html += '#sincloBox p#widgetTitle { border-radius: 0; border-top-width: 0.1em; height: 2em; padding: 0.35em; font-size: 1.2em }';
          html += '#sincloBox section { width: 100% }';
          html += '#sincloBox section#chatTab ul { height: ' + (chatAreaHeight - (6.5 * hRatio)) + 'px }';

          html += '#sincloBox #widgetTitle:after { width: 1.5em; height: 1.5em; top: 0; bottom: 0; right: 0.25em;}';
          html += '#sincloBox[data-openflg="true"] p#widgetTitle:after { margin-top: 0.5em; }';
          html += '#sincloBox #widgetTitle em { width: 2em; height: 2em; font-size: 0.8em; padding: 0.25em; border-radius: 5em; margin: 0.25em; }';
          html += '#sincloBox ul#chatTalk { padding: 0.3em; }';
          html += '#sincloBox ul#chatTalk li { font-size: 0.8em; border-radius: 0.3em; margin: 0.3em 0; padding: 0.3em; border: 1px solid #C9C9C9; }';
          html += '#sincloBox ul#chatTalk li sinclo-radio { margin: 0.25em 0 0.25em 0.5em; display: inline-block; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] { margin-right: 0.5em } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"], #sincloBox ul#chatTalk li sinclo-radio label { webkit-transform: scale(1.3); transform: scale(1.3); moz-transform: scale(1.3); } ';
          html += '#sincloBox ul#chatTalk li label, #sincloBox ul#chatTalk li span, #sincloBox ul#chatTalk li a { font-size: 1em; }';
          html += '#sincloBox ul#chatTalk li span.cName { margin: 0 0 0.3em 0 }';
          html += '#sincloBox section#chatTab div { height: 4em; padding: 0.5em; }';

          if ( hRatio > 16 ) {
            html += '#sincloBox #sincloChatMessage { height: 100%; border-radius: 5px 0 0 5px!important; }';
          }
          else {
            html += '#sincloBox #sincloChatMessage { height: 100%; border-radius: 5px 0 0 5px!important; font-size: 1.5em }';
          }
          html += '      #sincloBox section#chatTab #sincloChatSendBtn { padding: 0.6em 0; border: 1px solid ' + widget.mainColor + '; }';
          html += '      #sincloBox section#chatTab div sinclo-chat-alert { top: 0.5em; left: 0.5em; right: 0.5em; bottom: 0.5em; border-radius: 5px; color: #FFF; text-align: center; padding: 0.25em 0; }';

          html += '#sincloBox section#navigation ul { width: 100% }';
          html += 'sinclo span#mainImage, sinclo #widgetSubTitle, sinclo #widgetDescription, sinclo #navigation, sinclo #navigation * { display:none!important; height: 0!important }';
          html += '#sincloBox #fotter { display: none; height: 0!important }';
        }
      }
      /* PC版 */
      else {
        html += "      #sincloBox { width: " + widgetWidth + "px }";
        html += '      #sincloBox * { line-height: 1.4; font-size: 12px; }';
        html += '      #sincloBox div#widgetHeader:after { top: 32px }';
        html += "      #sincloBox section { width: " + widgetWidth + "px }";
        html += "      #sincloBox section#navigation ul { width: " + widgetWidth + "px }";
        html += '      #sincloBox span#mainImage { top: 7px; left: 10px }';
        html += '      #sincloBox p#widgetTitle { border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px 0 0; border: 1px solid ' + widget.mainColor + '; font-size: 14px;padding: 7px 30px; height: 32px }';
        html += '      #sincloBox p#widgetTitle #sincloChatUnread { width: 25px; height: 25px; font-size: 13px; border-radius: 15px; margin: 2.5px 6px; padding: 3px; }';
        html += '      #sincloBox p#widgetTitle:after { background-position-y: 3px; top: 6px; right: 10px; bottom: 6px; width: 20px; height: 20px; }';
        html += '      #sincloBox p#widgetSubTitle { background-color: #FFF; margin: 0; padding: 7px 0; text-align: left; border-width: 0 1px 0 1px; border-color: #E8E7E0; border-style: solid; padding-left: 77px; font-weight: bold; color: ' + widget.mainColor + '; height: 29px }';
        html += '      #sincloBox p#widgetDescription { background-color: #FFF; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: #E8E7E0; border-style: solid; padding-left: 77px; height: 23px; color: #8A8A8A; }';
        html += '      #sincloBox section { background-color: #FFF; border: 1px solid #E8E7E0; border-top: none; }';
        // 画像がセットされている場合のスタイル
        if ( String(widget.showMainImage) !== "2" ) {
          html += '      #sincloBox p#widgetTitle { padding-left: 70px; }';
        }
        // チャットを使用する際
        if ( window.info.contract.chat ) {
          html += '      #sincloBox #mainImage em { top: -10px; right: -10px; width: 25px; height: 20px; font-size: 11px; padding: 1px; }';
          html += '      #sincloBox ul#chatTalk { height: 194px; padding: 5px; }';
          html += '      #sincloBox ul#chatTalk li { border-radius: 5px; margin: 5px 0; padding: 5px; font-size: 12px; border: 1px solid #C9C9C9; }';
          html += '      #sincloBox ul#chatTalk li span.cName { font-size: 13px; margin: 0 0 5px 0 }';
          html += '      #sincloBox section#chatTab div { height: 75px!important; padding: 5px }';
          html += '      #sincloBox section#chatTab #sincloChatMessage { color: #8A8A8A!important; padding: 5px; height: 100%; border: 1px solid #E4E4E4!important; border-radius: 5px 0 0 5px!important; }';
          html += '      #sincloBox section#chatTab #sincloChatSendBtn { padding: 20px 0; height: 100%; border: 1px solid ' + widget.mainColor + '; }';

        }
        // 画面同期を使用する際
        if ( window.info.contract.synclo ) {
          html += '      #sincloBox section#callTab #telNumber { overflow: hidden; color: ' + widget.mainColor + '; font-weight: bold; margin: 0 auto; text-align: center }';
          html += '      #sincloBox section#callTab #telIcon { color: ' + widget.mainColor + '; display: block; width: 50px; height: 50px; float: left; background-color: #3EA3DE; border-radius: 25px; padding: 3px }';
          html += '      #sincloBox section#callTab #telContent { display: block; overflow-y: auto; overflow-x: hidden; max-height: 119px }';
          if ( window.info.contract.chat ) {
            html += '      #sincloBox section#callTab #telContent .tblBlock {  text-align: center;  margin: 0 auto;  width: 240px;  display: table; align-content: center;  height: 119px!important;  justify-content: center; }';
          }
          else {
            html += '      #sincloBox section#callTab #telContent .tblBlock { text-align: center; margin: 0 auto; width: 240px; display: table; align-content: center; justify-content: center; overflow-x: hidden; overflow-y: auto }';
          }
          html += '      #sincloBox section#callTab #telContent span { word-wrap: break-word ;word-break: break-all; font-size: 11px; line-height: 1.5!important; color: #6B6B6B; white-space: pre-wrap; max-height: 119px; display: table-cell; vertical-align: middle; text-align: center }';
          html += '      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border-radius: 15px } ';
        }
        html += '      #sincloBox section#navigation { border-width: 0 1px; height: 40px; }';
        html += '      #sincloBox section#navigation ul { margin: 0 0 0 -1px; height: 40px;}';
        html += '      #sincloBox section#navigation ul li { width: 50%; padding: 10px 0; border-left: 1px solid #E8E7E0; height: 40px }';
        html += '      #sincloBox section#navigation ul li:last-child { border-right: 1px solid #E8E7E0; }';
        html += '      #sincloBox section#navigation ul li:not(.selected) { border-bottom: 1px solid #E8E7E0 }';
        html += '      #sincloBox section#navigation ul li.selected::after{ border-bottom: 2px solid ' + widget.mainColor + '; }';
        html += '      #sincloBox #fotter { padding: 5px 0; border: 1px solid #E8E7E0; font-size: 11px; border-top: none; }';
        html += '      #sincloBox section#navigation ul li::before{ margin-right: 5px; width: 18px; height: 18px; }';

      }


      html += '  </style>';

      return html;
    },
    widgetHeaderTemplate: function(widget){
      var html = "", chatAndTitleOnly = false;
      // チャットとタイトルバーのみ表示するフラグ
      if ( check.smartphone() && ( window.screen.availHeight < window.screen.availWidth ) ) {
        chatAndTitleOnly = true;
      }
      // 画像
      if ( !chatAndTitleOnly && (Number(widget.showMainImage) === 1 || widget.mainImage !== "") ) {
        var ratio = 1;
        if ( check.smartphone() ) {
          ratio = ($(window).width() - 20) * (1/285);
        }
        html += '  <span id="mainImage" onclick="sinclo.operatorInfo.ev()"><img src="' + widget.mainImage + '" width="' + (ratio * 62) + '" height="' + (ratio * 70) + '" alt="チャット画像"></span>';
      }
      html += '  <div id="widgetHeader" class="notSelect" onclick="sinclo.operatorInfo.ev()">';
      // タイトル
      html += '    <p id="widgetTitle">' + check.escape_html(widget.title) + '</p>';
      var subTitle = (widget.subTitle === undefined && Number(widget.showSubtitle) === 1 ) ? "" : widget.subTitle;
      var description = (widget.description === undefined) ? "" : widget.description;
      if ( !chatAndTitleOnly && (Number(widget.showMainImage) === 1 || Number(widget.showSubtitle) === 1 || Number(widget.showDescription) === 1) ) {

        // サブタイトル
        if ( Number(widget.showSubtitle) === 1 ) {
          html += '    <p id="widgetSubTitle">' + check.escape_html(subTitle) + '</p>';
        }
        else {
          html += '    <p id="widgetSubTitle"></p>';
        }

        // 説明文
        html += '    <p id="widgetDescription">' + check.escape_html(description) + '</p>';
      }

      html += '  </div>';
      return html;
    },
    widgetNaviTemplate: function(widget){
      var html = "";
      html += '  <section id="navigation" class="notSelect">';
      html += '    <ul>';
      html += '        <li data-tab="chat" class="widgetCtrl selected">チャットでの受付</li>';
      html += '        <li data-tab="call" class="widgetCtrl" >電話での受付</li>';
      html += '    </ul>';
      html += '  </section>';
      return html;
    },
    widgetTemplate: function(widget){
      var html = "";

      // 電話・チャットプランの場合
      if ( window.info.contract.chat && window.info.contract.synclo && !check.smartphone() ) {
        html += '<section id="callTab">';
      }
      // 電話のみプランの場合
      else {
        html += '<section id="callTab" class="flexBox">';
      }

      html += '    <div style="height: 50px;margin: 15px 25px">';
      // アイコン
      html += '    <span style="display: block; width: 50px; height: 50px; float: left; background-color: ' + widget.mainColor + '; border-radius: 25px; padding: 3px;"><img width="19.5" height="33" src="' + window.info.site.files + '/img/call.png" style="margin: 6px 12px"></span>';
      // 受付電話番号
      if ( Number(widget.display_time_flg) === 1 ) {
          html += '    <pre id="telNumber" style="font-size: 18px; padding: 5px 0px 0px; height: 30px">' + check.escape_html(widget.tel) + '</pre>';
      }
      else {
          html += '    <pre id="telNumber" style="font-size: 20px; padding: 10px 0px 0px; height: 45px;">' + check.escape_html(widget.tel) + '</pre>';
      }
      // 受付時間
      if ( Number(widget.display_time_flg) === 1 ) {
          html += '    <pre style="font-weight: bold; color: ' + widget.mainColor + '; margin: 0 auto; white-space: pre-line; font-size: 11px; text-align: center; padding: 0 0 5px; height: 20px">受付時間： ' + widget.time_text + '</pre>';
      }
      html += '    </div>';
      // テキスト
      html += '    <div id="telContent"><div class="tblBlock"><span>' + check.escape_html(widget.content) + '</span></div></div>';
      html += '    <span id="accessIdArea">' + userInfo.accessId + '</span>';
      html += '</section>';
      return html;
    },
    showVideoChatView: function(fromID, toID) {
        var iframe = document.createElement('iframe');
        iframe.width = 480;
        iframe.height = 400;
        var sincloData = {
          from: fromID,
          to: toID,
        };
        iframe.src = info.site.webcam_view + "?h=false&sincloData=" + encodeURIComponent(JSON.stringify(sincloData)); // FIXME
        document.body.appendChild(iframe);
    },
    chatWidgetTemplate: function(widget){
      var html = "", placeholder, spFlg = check.smartphone();
      // ボタンのみの場合
      if ( ( 'chatTrigger' in window.info.widget && window.info.widget.chatTrigger === 2) ) {
        placeholder = "メッセージを入力してください";
      }
      else {
        if ( spFlg ) { // スマートフォンの場合
          placeholder = "メッセージを入力してください（改行で送信）";
        }
        else {
          placeholder = "メッセージを入力してください（Shift+Enterで改行/Enterで送信）";
        }
      }
      html += '  <section id="chatTab" class="flexBox">';
      html += '    <ul id="chatTalk"><sinclo-chat></sinclo-chat><sinclo-typing></sinclo-typing></ul>';
      html += '    <div class="flexBoxRow">';
      html += '      <textarea name="sincloChat" id="sincloChatMessage" maxlength="300" placeholder=" ' + placeholder + ' "></textarea>';
      html += '      <a id="sincloChatSendBtn" class="notSelect" onclick="sinclo.chatApi.push()">送信</a>';
      if ( spFlg ) { // スマートフォンの場合
        html += '      <sinclo-chat-alert onclick="return location.href = location.href;">通信が切断されました。<br>こちらをタップすると再接続します。</sinclo-chat-alert>';
      }
      html += '    </div>';
      html += '    <audio id="sinclo-sound">';
      html += '      <source src="' + window.info.site.files + '/sounds/decision.mp3" type="audio/mp3">';
      html += '    </audio>';
      html += '  </section>';
      return html;
    },
    judgeShowWidget: function(){
      window.info.widgetDisplay = null; // デフォルト表示しない
      // チャット契約、画面同期中であれば表示
      if ( check.isset(userInfo.connectToken) && window.info.contract.chat ) {
        window.info.widgetDisplay = true;
      }
      // ウィジェットを常に表示する
      if ( ('display_type' in window.info.widget) && window.info.widget.display_type === 1 ) {
        window.info.widgetDisplay = true;
      }
      // オペレーターの数に応じて表示する
      else if ( ('display_type' in window.info.widget) && window.info.widget.display_type === 2 ) {
        if ( Number(window.info.activeOperatorCnt) > 0 ) {
          window.info.widgetDisplay = true;
        }
      }
      // 同期対象とするが、ウィジェットは表示しない
      if (check.isset(window.info['dataset']) && (check.isset(window.info.dataset['hide']) && window.info.dataset.hide === "1")) {
        window.info.widgetDisplay = false;
      }
      return window.info.widgetDisplay;
    },
    makeAccessIdTag: function(){

      if ( !check.browser() ) return false;
      if ( !('widget' in window.info) ) return false;
      if (!this.judgeShowWidget()) {
        return false;
      }
      common.load.finish();
      var sincloBox = document.getElementById('sincloBox');
      // 非表示にされているだけであれば、再表示
      if ( sincloBox && sincloBox.style.display === "none" ) {
        sincloBox.style.display = "block";
        // sincloBox.parentNode.removeChild(sincloBox);
      }

      if ( userInfo.accessType !== cnst.access_type.host ) {
        var html = common.createWidget();
        $('body').append(html);
        emit('syncReady', {widget: window.info.widgetDisplay});
        var sincloBox = document.getElementById('sincloBox');
        sincloBox.setAttribute('data-openflg', false);
        sinclo.operatorInfo.header = document.querySelector('#sincloBox #widgetHeader');

        $(".widgetCtrl").click(function(){
            var target = $(".widgetCtrl.selected"), clickTab = $(this).data('tab');
            target.removeClass("selected");
            $("#sincloBox").height("");
            $(this).addClass("selected");

            if ( clickTab === "call" ) {
              $("#chatTab").removeClass('flexBox');
              $("#callTab").addClass('flexBox');
            }
            else {
              $("#callTab").removeClass('flexBox');
              $("#chatTab").addClass('flexBox');
              sinclo.chatApi.showUnreadCnt();
              sinclo.chatApi.scDown();
            }
        });

        if ( window.info.contract.chat && check.smartphone() ) {
          // 初期の画面向き
            if ( window.screen.availHeight < window.screen.availWidth ) {
              sincloBox.setAttribute('data-screen', 'horizontal'); // 横向き
            }
            else {
              sincloBox.setAttribute('data-screen', 'vertical'); // 縦向き
            }

          // スクロールをした時に、ページ下部であれば透過する
          window.addEventListener('scroll', sinclo.operatorInfo.widgetHide);
          // 画面を回転ときは、向きによってスタイルを変える
          window.addEventListener('orientationchange', sinclo.operatorInfo.reCreateWidget);
          // サイズが変わった時は、サイズ感を変える
          window.addEventListener('resize', function(){
            if ( $(window).height() > $(window).width() || document.activeElement.id === "sincloChatMessage") return false; // 横向きの場合のみ使用
             sinclo.operatorInfo.reCreateWidget();
          });
        }

      }

    },
    load: {
      id: "loadingImg",
      flg: false,
      timer: null,
      loadingHtml: function(){
        var html  = "";
            html += "<style type='text/css'>";
            html += "sinclo-loading-div {";
            html += "  background: none;";
            html += "  position: relative;";
            html += "  width: 200px;";
            html += "  height: 200px;";
            html += "}";
            html += "@-webkit-keyframes sinclo-loading-css {";
            html += "  0% {";
            html += "    opacity: 1;";
            html += "    -ms-transform: scale(1.5);";
            html += "    -moz-transform: scale(1.5);";
            html += "    -webkit-transform: scale(1.5);";
            html += "    -o-transform: scale(1.5);";
            html += "    transform: scale(1.5);";
            html += "  }";
            html += "  100% {";
            html += "    opacity: 0.1;";
            html += "    -ms-transform: scale(1);";
            html += "    -moz-transform: scale(1);";
            html += "    -webkit-transform: scale(1);";
            html += "    -o-transform: scale(1);";
            html += "    transform: scale(1);";
            html += "  }";
            html += "}";
            html += "@-webkit-keyframes sinclo-loading-css {";
            html += "  0% {";
            html += "    opacity: 1;";
            html += "    -ms-transform: scale(1.5);";
            html += "    -moz-transform: scale(1.5);";
            html += "    -webkit-transform: scale(1.5);";
            html += "    -o-transform: scale(1.5);";
            html += "    transform: scale(1.5);";
            html += "  }";
            html += "  100% {";
            html += "    opacity: 0.1;";
            html += "    -ms-transform: scale(1);";
            html += "    -moz-transform: scale(1);";
            html += "    -webkit-transform: scale(1);";
            html += "    -o-transform: scale(1);";
            html += "    transform: scale(1);";
            html += "  }";
            html += "}";
            html += "@-moz-keyframes sinclo-loading-css {";
            html += "  0% {";
            html += "    opacity: 1;";
            html += "    -ms-transform: scale(1.5);";
            html += "    -moz-transform: scale(1.5);";
            html += "    -webkit-transform: scale(1.5);";
            html += "    -o-transform: scale(1.5);";
            html += "    transform: scale(1.5);";
            html += "  }";
            html += "  100% {";
            html += "    opacity: 0.1;";
            html += "    -ms-transform: scale(1);";
            html += "    -moz-transform: scale(1);";
            html += "    -webkit-transform: scale(1);";
            html += "    -o-transform: scale(1);";
            html += "    transform: scale(1);";
            html += "  }";
            html += "}";
            html += "@-ms-keyframes sinclo-loading-css {";
            html += "  0% {";
            html += "    opacity: 1;";
            html += "    -ms-transform: scale(1.5);";
            html += "    -moz-transform: scale(1.5);";
            html += "    -webkit-transform: scale(1.5);";
            html += "    -o-transform: scale(1.5);";
            html += "    transform: scale(1.5);";
            html += "  }";
            html += "  100% {";
            html += "    opacity: 0.1;";
            html += "    -ms-transform: scale(1);";
            html += "    -moz-transform: scale(1);";
            html += "    -webkit-transform: scale(1);";
            html += "    -o-transform: scale(1);";
            html += "    transform: scale(1);";
            html += "  }";
            html += "}";
            html += "@-moz-keyframes sinclo-loading-css {";
            html += "  0% {";
            html += "    opacity: 1;";
            html += "    -ms-transform: scale(1.5);";
            html += "    -moz-transform: scale(1.5);";
            html += "    -webkit-transform: scale(1.5);";
            html += "    -o-transform: scale(1.5);";
            html += "    transform: scale(1.5);";
            html += "  }";
            html += "  100% {";
            html += "    opacity: 0.1;";
            html += "    -ms-transform: scale(1);";
            html += "    -moz-transform: scale(1);";
            html += "    -webkit-transform: scale(1);";
            html += "    -o-transform: scale(1);";
            html += "    transform: scale(1);";
            html += "  }";
            html += "}";
            html += "@-webkit-keyframes sinclo-loading-css {";
            html += "  0% {";
            html += "    opacity: 1;";
            html += "    -ms-transform: scale(1.5);";
            html += "    -moz-transform: scale(1.5);";
            html += "    -webkit-transform: scale(1.5);";
            html += "    -o-transform: scale(1.5);";
            html += "    transform: scale(1.5);";
            html += "  }";
            html += "  100% {";
            html += "    opacity: 0.1;";
            html += "    -ms-transform: scale(1);";
            html += "    -moz-transform: scale(1);";
            html += "    -webkit-transform: scale(1);";
            html += "    -o-transform: scale(1);";
            html += "    transform: scale(1);";
            html += "  }";
            html += "}";
            html += "@-o-keyframes sinclo-loading-css {";
            html += "  0% {";
            html += "    opacity: 1;";
            html += "    -ms-transform: scale(1.5);";
            html += "    -moz-transform: scale(1.5);";
            html += "    -webkit-transform: scale(1.5);";
            html += "    -o-transform: scale(1.5);";
            html += "    transform: scale(1.5);";
            html += "  }";
            html += "  100% {";
            html += "    opacity: 0.1;";
            html += "    -ms-transform: scale(1);";
            html += "    -moz-transform: scale(1);";
            html += "    -webkit-transform: scale(1);";
            html += "    -o-transform: scale(1);";
            html += "    transform: scale(1);";
            html += "  }";
            html += "}";
            html += "@keyframes sinclo-loading-css {";
            html += "  0% {";
            html += "    opacity: 1;";
            html += "    -ms-transform: scale(1.5);";
            html += "    -moz-transform: scale(1.5);";
            html += "    -webkit-transform: scale(1.5);";
            html += "    -o-transform: scale(1.5);";
            html += "    transform: scale(1.5);";
            html += "  }";
            html += "  100% {";
            html += "    opacity: 0.1;";
            html += "    -ms-transform: scale(1);";
            html += "    -moz-transform: scale(1);";
            html += "    -webkit-transform: scale(1);";
            html += "    -o-transform: scale(1);";
            html += "    transform: scale(1);";
            html += "  }";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div {";
            html += "  width: 24px;";
            html += "  height: 24px;";
            html += "  margin-left: 4px;";
            html += "  margin-top: 4px;";
            html += "  position: absolute;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div > sinclo-loading-div-child {";
            html += "  width: 100%;";
            html += "  height: 100%;";
            html += "  border-radius: 15px;";
            html += "  background: #b2d251;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(1) > sinclo-loading-div-child {";
            html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
            html += "  animation: sinclo-loading-css 1s linear infinite;";
            html += "  -ms-animation-delay: 0s;";
            html += "  -moz-animation-delay: 0s;";
            html += "  -webkit-animation-delay: 0s;";
            html += "  -o-animation-delay: 0s;";
            html += "  animation-delay: 0s;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(1) {";
            html += "  -ms-transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
            html += "  -moz-transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
            html += "  -webkit-transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
            html += "  -o-transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
            html += "  transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(2) > sinclo-loading-div-child {";
            html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
            html += "  animation: sinclo-loading-css 1s linear infinite;";
            html += "  -ms-animation-delay: 0.12s;";
            html += "  -moz-animation-delay: 0.12s;";
            html += "  -webkit-animation-delay: 0.12s;";
            html += "  -o-animation-delay: 0.12s;";
            html += "  animation-delay: 0.12s;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(2) {";
            html += "  -ms-transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
            html += "  -moz-transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
            html += "  -webkit-transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
            html += "  -o-transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
            html += "  transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(3) > sinclo-loading-div-child {";
            html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
            html += "  animation: sinclo-loading-css 1s linear infinite;";
            html += "  -ms-animation-delay: 0.25s;";
            html += "  -moz-animation-delay: 0.25s;";
            html += "  -webkit-animation-delay: 0.25s;";
            html += "  -o-animation-delay: 0.25s;";
            html += "  animation-delay: 0.25s;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(3) {";
            html += "  -ms-transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
            html += "  -moz-transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
            html += "  -webkit-transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
            html += "  -o-transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
            html += "  transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(4) > sinclo-loading-div-child {";
            html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
            html += "  animation: sinclo-loading-css 1s linear infinite;";
            html += "  -ms-animation-delay: 0.37s;";
            html += "  -moz-animation-delay: 0.37s;";
            html += "  -webkit-animation-delay: 0.37s;";
            html += "  -o-animation-delay: 0.37s;";
            html += "  animation-delay: 0.37s;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(4) {";
            html += "  -ms-transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
            html += "  -moz-transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
            html += "  -webkit-transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
            html += "  -o-transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
            html += "  transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(5) > sinclo-loading-div-child {";
            html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
            html += "  animation: sinclo-loading-css 1s linear infinite;";
            html += "  -ms-animation-delay: 0.5s;";
            html += "  -moz-animation-delay: 0.5s;";
            html += "  -webkit-animation-delay: 0.5s;";
            html += "  -o-animation-delay: 0.5s;";
            html += "  animation-delay: 0.5s;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(5) {";
            html += "  -ms-transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
            html += "  -moz-transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
            html += "  -webkit-transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
            html += "  -o-transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
            html += "  transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(6) > sinclo-loading-div-child {";
            html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
            html += "  animation: sinclo-loading-css 1s linear infinite;";
            html += "  -ms-animation-delay: 0.62s;";
            html += "  -moz-animation-delay: 0.62s;";
            html += "  -webkit-animation-delay: 0.62s;";
            html += "  -o-animation-delay: 0.62s;";
            html += "  animation-delay: 0.62s;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(6) {";
            html += "  -ms-transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
            html += "  -moz-transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
            html += "  -webkit-transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
            html += "  -o-transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
            html += "  transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(7) > sinclo-loading-div-child {";
            html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
            html += "  animation: sinclo-loading-css 1s linear infinite;";
            html += "  -ms-animation-delay: 0.75s;";
            html += "  -moz-animation-delay: 0.75s;";
            html += "  -webkit-animation-delay: 0.75s;";
            html += "  -o-animation-delay: 0.75s;";
            html += "  animation-delay: 0.75s;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(7) {";
            html += "  -ms-transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
            html += "  -moz-transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
            html += "  -webkit-transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
            html += "  -o-transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
            html += "  transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(8) > sinclo-loading-div-child {";
            html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
            html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
            html += "  animation: sinclo-loading-css 1s linear infinite;";
            html += "  -ms-animation-delay: 0.87s;";
            html += "  -moz-animation-delay: 0.87s;";
            html += "  -webkit-animation-delay: 0.87s;";
            html += "  -o-animation-delay: 0.87s;";
            html += "  animation-delay: 0.87s;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-div:nth-of-type(8) {";
            html += "  -ms-transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
            html += "  -moz-transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
            html += "  -webkit-transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
            html += "  -o-transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
            html += "  transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
            html += "}";
            html += "sinclo-loading-area, sinclo-loading-div, sinclo-loading-div-child {";
            html += "    display: block;";
            html += "}";
            html += "sinclo-loading-area {";
            html += "  position: absolute;";
            html += "  top: 50%;";
            html += "  left: 50%;";
            html += "  width: 150px;";
            html += "  height: 150px;";
            html += "  margin-left: -75px;";
            html += "  margin-top: -75px;";
            html += "}";
            html += ".uil-spin-css > sinclo-loading-span {";
            html += "  font-family: 'メイリオ','ＭＳ Ｐ明朝',細明朝体,serif;";
            html += "  position: absolute;";
            html += "  top: 50%;";
            html += "  left: 0;";
            html += "  right: 0;";
            html += "  color: #b2d251;";
            html += "  font-size: 17px;";
            html += "  text-align: center;";
            html += "  font-weight: bold;";
            html += "  margin-top: -0.5em;";
            html += "}";
            html += "</style>";
            html += "<sinclo-loading-area>";
            html += "  <sinclo-loading-div class='uil-spin-css' style='-webkit-transform:scale(0.8)'>";
            html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
            html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
            html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
            html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
            html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
            html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
            html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
            html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
            html += "    <sinclo-loading-span>Loading...</sinclo-loading-span>";
            html += "  </sinclo-loading-div>";
            html += "</sinclo-loading-area>";
          return html;
      },
      start:  function(){
        window.clearTimeout(this.timer);
        var div = document.createElement('div');
        div.id = this.id;
        div.style.cssText = "position: fixed; top: 0; left: 0; bottom: 0; right: 0; background-color: rgba(68,68,68,0.7); z-index: 99999";
        var  html = this.loadingHtml();
        div.innerHTML = html;
        document.body.appendChild(div);
        this.flg = true; // 一度接続済みというフラグを持たせる
        this.timer = window.setTimeout(function(){
          common.load.finish();
        }, 5000);
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
      // チャット契約なしで、スマートフォンからの閲覧の場合は弾く
      if ( !window.info.contract.chat && this.smartphone() ) {
        ret = false;
      }
      // Botは弾く
      if ( ua.indexOf('bot') > 0 ) {
        ret = false;
      }
      if ( ua.match(/msie\ [1-9]\./g) ) {
        ret = false;
      }
      return ret;
    },
    smartphone: function(){
      var ua = navigator.userAgent.toLowerCase();
      // iPhone/iPod/Androidのみ有効のロジック
      return (ua.indexOf('iphone') > 0 || ua.indexOf('ipod') > 0 || ua.indexOf('android') > 0);
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
    // エスケープ用
    // http://qiita.com/saekis/items/c2b41cd8940923863791
    escape_html: function(string) {
      if(typeof string !== 'string') {
        return string;
      }
      return string.replace(/[&'`"<>]/g, function(match) {
        return {
          '&': '&amp;',
          "'": '&#x27;',
          '`': '&#x60;',
          '"': '&quot;',
          '<': '&lt;',
          '>': '&gt;',
        }[match]
      });
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
    pageTime: null,
    firstConnection: false,
    searchKeyword: null,
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
        else {
          var code = this.getCode(cnst.info_type.referrer);
          userInfo.referrer = storage.s.get(code);
          if ( check.isset(userInfo.referrer) ) {
            var ret = userInfo.referrer.match(/[?&](kw|MT|name|p|q|qt|query|search|word)=([^&]+)/);
            userInfo.searchKeyword = (check.isset(ret) && ret.length === 3) ? ret[2] : null;
          }
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
        case cnst.info_type.staycount:
          return "stayCount";
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
    getConnect: function(){
      return this.get(cnst.info_type.connect);
    },
    getStayCount: function(){
      var code = this.getCode(cnst.info_type.staycount);
      return Number(storage.l.get(code));
    },
    setStayCount: function(){
      var code = this.getCode(cnst.info_type.staycount),
          cnt = Number(storage.l.get(code)) + 1;
      storage.l.set(code, cnt);
    },
    setReferrer: function(){
      var code = this.getCode(cnst.info_type.referrer);

      // IE8対応コード
      if ( userInfo.referrer === null ) {
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
        prev: this.prev,
        referrer: this.referrer,
        userAgent: window.navigator.userAgent,
        chatCnt: document.getElementsByClassName('sinclo_se').length,
        chatUnread: {id: null, cnt: 0},
        service: check.browser(),
        widget: window.info.widgetDisplay
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
    },
    getActiveWindow: function(){
      var tabFlg = document.hasFocus(), widgetFlg = false, tabStatus;
      if ( document.getElementById('sincloBox') ) {
        var tmp = document.getElementById('sincloBox').getAttribute('data-openflg');
        if ( String(tmp) === "true" ) {
          widgetFlg = true;
        }
      }
      // タブがアクティブ
      if ( tabFlg ) {
        // ウィジェットが開いている
        if ( widgetFlg ) {
          tabStatus = cnst.tab_type.open;
        }
        else {
          tabStatus = cnst.tab_type.close;
        }
      }
      else {
        tabStatus = cnst.tab_type.none;
      }
      return tabStatus;
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
      if ( ('form' in window.info.dataset ) && window.info.dataset.form ) {
        // フォーム制御
        $(document).submit(function(e){
          if ( userInfo.accessType !== cnst.access_type.host ) {
            emit('requestSyncStop', {message: "お客様がsubmitボタンをクリックしましたので、\n画面共有を終了します。"});
          }
          else {
            emit('requestSyncStop', {});
            e.preventDefault();
            e.stopPropagation();
            return false;
          }
        });
      }

    },
    start: function(e){ syncEvent.change(true); },
    stop: function(e){ syncEvent.change(false); }
  };

    popup = {
      const: {
        action: {
          alert: 1,
          confirm: 2,
        }
      },
      getCss: function () {
        var css = '';
        css += '<div id="sincloPopup" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999999999999;">';
        css += '  <style>';
        css += '    #sincloPopupFrame {';
        css += '        border: 0.15em solid #ABABAB;';
        css += '        width: 30em;';
        css += '        opacity: 0;';
        css += '        background-color: #EDEDED;';
        css += '        color: #3C3C3C;';
        css += '        margin: auto;';
        css += '        position: absolute;';
        css += '        top: 0;';
        css += '        left: 0;';
        css += '        right: 0;';
        css += '        bottom: 0;';
        css += '        box-shadow: 0 35px 42px rgba(141, 141, 141, 0.8);';
        css += '        border-radius: 5px;';
        css += '        box-sizing: border-box;';
        css += '    }';
        css += '    #sincloPopBar {';
        css += '        height: 1.85em;';
        css += '        background: linear-gradient(#EDEDED, #D2D2D2);';
        css += '        border-bottom: 0.15em solid #989898;';
        css += '        border-radius: 5px 5px 0 0;';
        css += '    }';
        css += '    #sincloLogo {';
        css += '        padding: 1em;';
        css += '    }';
        css += '    #sincloMessage {';
        css += '        padding-right: 1em;';
        css += '    }';
        css += '    sinclo-h3 {';
        css += '        font-weight: bold;';
        css += '        display: block;';
        css += '        font-size: 1.2em;';
        css += '        height: 1.2em;';
        css += '        margin: 0.4em 0;';
        css += '    }';
        css += '    sinclo-div {';
        css += '        display: block;';
        css += '    }';
        css += '    sinclo-content {';
        css += '        display: block;';
        css += '        font-size: 0.9em;';
        css += '        margin: 0.5em 0;';
        css += '        line-height: 2em;';
        css += '    }';
        css += '    #sincloPopMain {';
        css += '        display: table;';
        css += '    }';
        css += '    sinclo-div#sincloPopMain sinclo-div {';
        css += '        display: table-cell;';
        css += '        vertical-align: top;';
        css += '    }';
        css += '    #sincloPopAct {';
        css += '        width: 100%;';
        css += '        height: 2em;';
        css += '        text-align: center;';
        css += '        padding: 0.5em 0;';
        css += '        box-sizing: content-box;';
        css += '    }';
        css += '    #sincloPopAct sinclo-a {';
        css += '        background-color: #FFF;';
        css += '        padding: 5px 10px;';
        css += '        text-decoration: none;';
        css += '        border-radius: 5px;';
        css += '        border: 1px solid #959595;';
        css += '        margin: 10px;';
        css += '        font-size: 1em;';
        css += '        box-shadow: 0 0 2px rgba(75, 75, 75, 0.3);';
        css += '        font-weight: bold;';
        css += '    }';
        css += '    #sincloPopAct sinclo-a:hover {';
        css += '        cursor: pointer;';
        css += '    }';
        css += '    #sincloPopAct sinclo-a:hover,  #sincloPopAct sinclo-a:focus {';
        css += '        outline: none;';
        css += '    }';
        css += '    #sincloPopAct sinclo-a#sincloPopupOk {';
        css += '       background: linear-gradient(to top, #D5FAFF, #80BEEA, #D5FAFF);';
        css += '    }';
        css += '    #sincloPopAct sinclo-a#sincloPopupNg {';
        css += '       background-color: #FFF';
        css += '    }';
        css += '    #sincloPopAct sinclo-a#sincloPopupNg:hover, #sincloPopAct sinclo-a#sincloPopupNg:focus {';
        css += '       background-color: ##DCDCDC';
        css += '    }';
        css += '  </style>';
        return css;
      },
      getAction: function(type){
        var html = "";
        if ( type === popup.const.action.confirm ) {
          html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupOk" onclick="popup.ok()">許可する</sinclo-a>';
          html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupNg" onclick="popup.no()">許可しない</sinclo-a>';
        }
        else {
          html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupOk" onclick="popup.ok()">閉じる</sinclo-a>';
        }
        return html;
      },
      set: function(title, content, type){
          if (check.isset(type) === false) {
            type = popup.const.action.confirm;
          }
          popup.remove();
          var maincolor = ( window.info.widget.mainColor !== undefined ) ? window.info.widget.mainColor : "#ABCD05";
          var hovercolor = ( window.info.site.hovercolor !== undefined ) ? window.info.site.hovercolor : "#9CB90E";
          var html = '';
          html += this.getCss();
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
          html += this.getAction(type);
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

  vcPopup = {
      dragging: false,
      set: function(fromID, toID){
          vcPopup.remove();
          var maincolor = ( window.info.widget.mainColor !== undefined ) ? window.info.widget.mainColor : "#ABCD05";
          var hovercolor = ( window.info.site.hovercolor !== undefined ) ? window.info.site.hovercolor : "#9CB90E";
          var html = '';
          var sincloData = {
            from: fromID,
            to: toID,
          };
          var url = info.site.webcam_view + "?h=false&sincloData=" + encodeURIComponent(JSON.stringify(sincloData));
          html += '  <style>';
          html += '    #sincloVcPopupFrame {';
          html += '        border: 0.15em solid #ABABAB;';
          html += '        width: 27em;';
          html += '        opacity: 0;';
          html += '        background-color: #EDEDED;';
          html += '        color: #3C3C3C;';
          html += '        margin: auto;';
          html += '        position: absolute;';
          html += '        top: 0;';
          html += '        left: 0;';
          html += '        box-shadow: 0 35px 42px rgba(141, 141, 141, 0.8);';
          html += '        border-radius: 5px;';
          html += '        box-sizing: border-box;';
          html += '    }';
          html += '    #sincloVcPopBar {';
          html += '        height: 2.45em;';
          html += '        background: linear-gradient(#EDEDED, #D2D2D2);';
          html += '        border-bottom: 0.15em solid #989898;';
          html += '        border-radius: 5px 5px 0 0;';
          html += '    }';
          html += '    #sincloVcLogo {';
          html += '        padding: 0.5em 1em;';
          html += '    }';
          html += '    #sincloVcMessage {';
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
          html += '    #sincloVcPopMain {';
          html += '        display: table;';
          html += '    }';
          html += '    sinclo-div#sincloVcPopMain sinclo-div {';
          html += '        display: table-cell;';
          html += '        vertical-align: top;';
          html += '    }';
          html += '    #sincloVcPopAct {';
          html += '        width: 100%;';
          html += '        height: 2em;';
          html += '        text-align: center;';
          html += '        padding: 0.5em 0;';
          html += '        box-sizing: content-box;';
          html += '    }';
          html += '    #sincloVcPopAct sinclo-a {';
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
          html += '    #sincloVcPopAct sinclo-a:hover {';
          html += '        cursor: pointer;';
          html += '    }';
          html += '    #sincloVcPopAct sinclo-a:hover,  #sincloVcPopAct sinclo-a:focus {';
          html += '        outline: none;';
          html += '    }';
          html += '    #sincloVcPopAct sinclo-a#sincloPopupOk {';
          html += '       background: linear-gradient(to top, #D5FAFF, #80BEEA, #D5FAFF);';
          html += '    }';
          html += '    #sincloVcPopAct sinclo-a#sincloPopupNg {';
          html += '       background-color: #FFF';
          html += '    }';
          html += '    #sincloVcPopAct sinclo-a#sincloPopupNg:hover, #sincloVcPopAct sinclo-a#sincloPopupNg:focus {';
          html += '       background-color: ##DCDCDC';
          html += '    }';
          html += '  </style>';
          html += '  <sinclo-div id="sincloVcPopupFrame">';
          html += '    <sinclo-div id="sincloVcPopBar">';
          html += '      <sinclo-div id="sincloVcLogo"><img src="' + info.site.files + '/img/mark.png" width="18" height="18"></sinclo-div>';
          html += '    </sinclo-div>';
          html += '    <sinclo-div id="sincloVcPopMain">';
          html += '      <sinclo-div id="sincloVcMessage">';
          html += '          <iframe id="sincloVcView" src="' + url + '" width="320" height="240"/>';
          html += '      </sinclo-div>';
          html += '    </sinclo-div>';
          html += '  </sinclo-div>';

          $("body").append(html);

          var height = 0;
          $("#sincloVcPopupFrame > sinclo-div").each(function(e){
            height += this.offsetHeight;
          });

          $("#sincloVcPopupFrame").height(height).css("opacity", 1);
          $("#sincloVcPopupFrame *").on('mousedown', vcPopup.dragOn);
          $("#sincloVcPopupFrame *").on('mouseup', vcPopup.dragOff);
          $("#sincloVcPopupFrame *").on('mousemove', vcPopup.drag);
      },
      remove: function(){
          var elm = document.getElementById('sincloVcPopup');
          if (elm) {
            elm.parentNode.removeChild(elm);
          }
      },
      ok: function(){ return true; },
      no: function(){ this.remove() },
      // ドラッグ用プロパティ・メソッド群
      startDragX: 0,
      startDragY: 0,
      dragOn: function(e) {
        vcPopup.dragging = true;
        vcPopup.startDragX = e.screenX;
        vcPopup.startDragY = e.screenY;
      },
      dragOff: function() {
        vcPopup.dragging = false;
      },
      drag: function(e) {
        if(!vcPopup.dragging) return;
        e.stopPropagation();
        var deltaX = e.screenX - vcPopup.startDragX;
        var deltaY = e.screenY - vcPopup.startDragY;
        $("#sincloVcPopupFrame").css({
          top: $('#sincloVcPopupFrame').offset().top + deltaY,
          left: $('#sincloVcPopupFrame').offset().left + deltaX
        });
        vcPopup.startDragX = e.screenX;
        vcPopup.startDragY = e.screenY;
        return false;
      }
  };

  var init = function(){
    socket = io.connect(info.site.socket, {port: 9090, rememberTransport : false});
    // 接続時
    socket.on("connect", function(){
      // ウィジェットがある状態での再接続があった場合
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox && userInfo.accessType === Number(cnst.access_type.guest) ) {
        sinclo.trigger.flg = true;
        var emitData = userInfo.getSendList();
        emitData.widget = window.info.widgetDisplay;
        emit('customerInfo', emitData);
        emit('connectSuccess', {confirm: false, reconnect: true, widget: window.info.widgetDisplay});
        sincloBox.style.display = "block";
      }
      else {
        sinclo.trigger.flg = false;
        sinclo.connect();
      }


      if ( sincloBox && userInfo.accessType === Number(cnst.access_type.host) ) return false;
      // 定期的にタブのアクティブ状態を送る
      var tabState = browserInfo.getActiveWindow();
      setInterval(function(){
        var newState = browserInfo.getActiveWindow();
        if ( tabState !== newState ) {
          tabState = newState;
          emit('sendTabInfo', { status: tabState });
        }
      }, 700);
    }); // socket-on: connect

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("retConnectedForSync", function(d){
      sinclo.retConnectedForSync(d);
    }); // socket-on: retConnectedForSync

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("accessInfo", function(d){
      sinclo.accessInfo(d);
    }); // socket-on: accessInfo

    // 履歴ID割り振り後
    socket.on("setHistoryId", function(d) {
      sinclo.setHistoryId(d);
    }); // socket-on: getAccessInfo

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
      var obj = common.jParse(d);
      sinclo.getWindowInfo(obj);
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
    }); // socket-on: resUrlChecker

    // チャット対応開始結果
    socket.on('chatStartResult', function (d) {
      sinclo.chatStartResult(d);
    }); // socket-on: chatStartResult

    // チャット対応終了結果
    socket.on('chatEndResult', function (d) {
      sinclo.chatEndResult(d);
    }); // socket-on: chatEndResult

    // チャット初期データ
    socket.on('chatMessageData', function (d) {
      sinclo.chatMessageData(d);
    }); // socket-on: chatMessageData

    // オートメッセージデータ群
    socket.on('sendReqAutoChatMessages', function (d) {
      sinclo.sendReqAutoChatMessages(d);
    }); // socket-on: sendReqAutoChatMessages

    // オートメッセージ
    socket.on('sendAutoChatMessage', function (d) {
      sinclo.sendAutoChatMessage(d);
    }); // socket-on: sendReqAutoChatMessages

    // オートメッセージ
    socket.on('resAutoChatMessage', function (d) {
      sinclo.resAutoChatMessage(d);
    }); // socket-on: resAutoChatMessage

    // 新着チャット
    socket.on('sendChatResult', function (d) {
      sinclo.sendChatResult(d);
    }); // socket-on: sendChatResult

    // チャット入力状況受信
    socket.on('receiveTypeCond', function (d) {
      sinclo.chatApi.createTypingMessage(d);
    }); // socket-on: receiveTypeCond

    // 画面共有
    socket.on('confirmVideochatStart', function(d){
      var obj = common.jParse(d);
      sinclo.confirmVideochatStart(obj);
    }); // socket-on: confirmVideochatStart

    socket.on('syncStop', function(d){
      sinclo.syncStop(d);
    }); // socket-on: syncStop

    socket.on('disconnect', function(data) {
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox ) {
        // sincloBox.parentNode.removeChild(sincloBox);
        sincloBox.style.display = "none";
      }
      popup.remove();
    });
  };

  $.ajaxSetup({
    cache: false
  });

  $.ajax({
      type: 'get',
      url: window.info.site.files + "/settings/",
      cache: false,
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

  var timer = window.setInterval(function(){
    if ( io !== "" && sinclo !== "" && window.info.contract !== undefined ) {
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
  /* ここから：イベント名指定なし */
  data.siteKey = info.site.key; // サイトの識別キー
  data.tabId = userInfo.tabId; // タブの識別ID
  if ( check.isset(userInfo.sendTabId) ) {
    data.to = userInfo.sendTabId; // 送信先ID
  }
  /* ここまで：イベント名指定なし */
  /* ここから：イベント名指定あり */
  if (evName === "customerInfo" || evName === "sendAccessInfo") {
    data.accessId = userInfo.accessId;
    data.userId = userInfo.userId;
    data.status = browserInfo.getActiveWindow();
  }
  if (evName === "connected" || evName === "getChatMessage") {
    data.token = common.token;
  }
  if (evName === "connectSuccess") {
    data.widget = window.info.widgetDisplay;
  }
  if (evName === "customerInfo" || evName === "sendAccessInfo") {
    data.contract = window.info.contract;
  }
  if (evName === "syncReady" || evName === "connectSuccess" || evName === "customerInfo" || evName === "sendAccessInfo") {
    data.subWindow = false;
    if ( check.isset(storage.s.get('params')) || userInfo.accessType === cnst.access_type.host ) {
      data.responderId = common.params.responderId;
      data.subWindow = true;
    }
  }
  if (evName === "syncReady" || evName === "connectSuccess" || evName === "sendAccessInfo" || evName === "customerInfo") {
    data.title = common.title();
  }
  if (evName === "connectSuccess" || evName === "sendWindowInfo" || evName === "sendAutoChat" || evName === "sendChat") {
    data.userId = userInfo.userId;
  }
  if (   evName === "connectSuccess" || evName === "sendWindowInfo" || evName === "sendAutoChatMessages"
      || evName === "getChatMessage" || evName === "sendChat" || evName === "sendAutoChatMessage"
  ) {
    data.chat = null;
  }
  if (   evName === "syncBrowserInfo" || evName === "syncChangeEv" || evName === "requestSyncStop"
      || evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendConfirmConnect"
  ) {
    data.accessType = userInfo.accessType;
  }
  if (   evName === "syncReady" || evName === "connectSuccess" || evName === "reqUrlChecker"  || evName === "customerInfo"
      || evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendAccessInfo" || evName === "sendWindowInfo"
  ) {
    data.url= f_url(browserInfo.href);
  }
  // connectToken
  if (   evName === "syncReady" || evName === "connectSuccess" || evName === "requestSyncStop"  || evName === "customerInfo" || evName === "sendTabInfo"
      || evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendAccessInfo" || evName === "sendConfirmConnect"
  ) {
    data.connectToken = userInfo.get(cnst.info_type.connect);
  }
  if ( evName == "sendWindowInfo" ) {
    data.connectToken = userInfo.connectToken;
  }
  /* ここまで：イベント名指定あり */
// console.log(evName, data);
  socket.emit(evName, JSON.stringify(data));
}

function now(){
  var d = new Date();
  return "【" + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() + "】";
}

// get type
var myTag = document.querySelector("script[src='" + info.site.files + "/client/" + info.site.key + ".js']");
if (myTag.getAttribute('data-hide')) {
    info.dataset['hide'] = myTag.getAttribute('data-hide');
}
if (myTag.getAttribute('data-form')) {
    info.dataset['form'] = myTag.getAttribute('data-form');
}
