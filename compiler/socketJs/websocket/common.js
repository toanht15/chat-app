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

(function(jquery){
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
      gFrame: 11,
      sendTabId: 12,
      parentId: 13,
      sincloSessionId: 14
    },
    sync_type: { inner: 1, outer: 2 }
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
    // バイト表示をKB, MB, GBに変更する
    // https://stackoverflow.com/questions/15900485/correct-way-to-convert-size-in-bytes-to-kb-mb-gb-in-javascript
    formatBytes : function(a,b){if(0==a)return"0 Bytes";var c=1024,d=b||2,e=["Bytes","KB","MB","GB","TB","PB","EB","ZB","YB"],f=Math.floor(Math.log(a)/Math.log(c));return parseFloat((a/Math.pow(c,f)).toFixed(d))+" "+e[f]},
    createWidget: function(){
      var widget = window.sincloInfo.widget, displaySet = "";
      var css = this.widgetCssTemplate(widget),
          header = this.widgetHeaderTemplate(widget),
          //プレミアムプランであってもナビゲションを非表示にする
          //navi = this.widgetNaviTemplate(widget),
          navi = "",
          chat = this.chatWidgetTemplate(widget),
          call = this.widgetTemplate(widget),
          fotter = '<p id="fotter">Powered by <a target="sinclo" href="https://sinclo.medialink-ml.co.jp/lp/?utm_medium=web-widget&utm_campaign=widget-referral">sinclo</a></p>';
      // フルプランのPCの場合
      if ( window.sincloInfo.contract.chat && (window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document)) && !check.smartphone() ) {
        displaySet += navi + chat + call;
      }
      // フルプランのSPの場合はチャットのみ表示
      else if ( window.sincloInfo.contract.chat && (window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document)) && check.smartphone() ) {
        displaySet += chat;
      }
      else {
        // チャットのみ契約の場合
        if ( window.sincloInfo.contract.chat ) {
          displaySet += chat;
        }
        // 画面同期のみ契約の場合
        if ( window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document) ) {
          displaySet += call;
        }
      }
      //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
      if(Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1){
        //バナーも同時に生成したsinclo
        var sincloBanner = this.sincloBannerTemplate(widget);
        return "<sinclo id='sincloBox' >" + css + "<div id='sincloWidgetBox'>" + header + displaySet + fotter + "</div><div id='sincloBannerBox' style='display: none;'>"+ sincloBanner +"</div></sinclo>";
      }
      else{
        //今までと同じ通常のsinclo
        return "<sinclo id='sincloBox' >" + css + "<div id='sincloWidgetBox'>" + header + displaySet + fotter + "</div></sinclo>";
      }
    },
    //サイズを返す関数
    getColorList: function(widget){
//      var widget = window.sincloInfo.widget;
      //通常設定か高度設定か判定 colorSettingType
      if(Number(widget.colorSettingType) === 0){
        //企業側吹き出し背景色は現在設定されているメインカラーから算出する
        var main_color = widget.mainColor;
        var code = main_color.substr(1), r,g,b;
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

        var balloonR = String(Math.floor(255 - (255 - parseInt(r,16)) * 0.1));
        var balloonG = String(Math.floor(255 - (255 - parseInt(g,16)) * 0.1));
        var balloonB = String(Math.floor(255 - (255 - parseInt(b,16)) * 0.1));
        var codeR = parseInt(balloonR).toString(16);
        var codeG = parseInt(balloonG).toString(16);
        var codeB = parseInt(balloonB).toString(16);
        var reBackgroundColor = ('#' + codeR + codeG + codeB).toUpperCase();
        //通常設定
        var portioneArray = {
          //1.メインカラー
          mainColor: widget.mainColor,
          //2.タイトル文字色
          stringColor: widget.stringColor,
          //3.吹き出し文字色
          messageTextColor: widget.messageTextColor,
          //4.その他文字色
          otherTextColor: widget.otherTextColor,
          //5.ウィジェット枠線色
          widgetBorderColor: widget.widgetBorderColor,
          //6.吹き出し枠線色
          chatTalkBorderColor: widget.chatTalkBorderColor,
          //ヘッダー背景色
          headerBackgroundColor: "#FFFFFF",
          //7.企業名文字色 ※基本設定 1.メインカラーを使用
          subTitleTextColor: widget.mainColor,
          //8.説明文文字色 ※基本設定 4.その他文字色を使用
          descriptionTextColor: widget.otherTextColor,
          //9.チャットエリア背景色 ※デフォルトカラー白に設定
          chatTalkBackgroundColor: "#FFFFFF",
          //10.企業名担当者名文字色 ※基本設定 1.メインカラーを使用
          cNameTextColor: widget.mainColor,
          //11.企業側吹き出し文字色 ※基本設定 3.吹き出し文字色を使用
          reTextColor: widget.messageTextColor,
          //12.企業側吹き出し背景色 ※渡された基本設定 1.メインカラーを薄くする
          reBackgroundColor: reBackgroundColor,
          //13.企業側吹き出し枠線色 ※基本設定 6.吹き出し枠線色を使用
          reBorderColor: widget.chatTalkBorderColor,
          //14.企業側吹き出し枠線なし ※通常設定の時は必ず0
          reBorderNone: 0,
          //15.訪問者側吹き出し文字色 ※基本設定 3.吹き出し文字色を使用
          seTextColor: widget.messageTextColor,
          //16.訪問者側吹き出し背景色 ※デフォルトカラー白に設定
          seBackgroundColor: "#FFFFFF",
          //17.訪問者側吹き出し枠線色 ※基本設定 6.吹き出し枠線色を使用
          seBorderColor: widget.chatTalkBorderColor,
          //18.訪問者側吹き出し枠線色 ※通常設定の時は必ず0
          seBorderNone: 0,
          //19.メッセージエリア背景色 ※デフォルトカラー白に設定
          chatMessageBackgroundColor: "#FFFFFF",
          //20.メッセージBOX文字色 ※基本設定 4.その他文字色を使用
          messageBoxTextColor: widget.otherTextColor,
          //21.メッセージBOX背景色 ※デフォルトカラー白に設定
          messageBoxBackgroundColor: "#FFFFFF",
          //22.メッセージBOX枠線色 ※基本設定 6.吹き出し枠線色を使用
          messageBoxBorderColor: widget.chatTalkBorderColor,
          //23.メッセージBOX枠線なし ※通常設定の時は必ず0
          messageBoxBorderNone: 0,
          //24.送信ボタン文字色 ※基本設定 2.タイトル文字色を使用
          chatSendBtnTextColor: widget.stringColor,
          //25.送信ボタン背景色 ※基本設定 1.メインカラーを使用
          chatSendBtnBackgroundColor: widget.mainColor,
          //26.ウィジット内枠線色 ※基本設定ウィジェット枠線色を使用
          widgetInsideBorderColor: widget.widgetBorderColor,
          //27.ウィジット内枠線なし ※通常設定の時は必ず0
          widgetInsideBorderNone: 0,
          //28.ウィジット外枠線なし ※通常設定の時は必ず0
          widgetBorderNone: 0
        };
      }
      else{
        //高度な設定
        if(widget.reBorderColor === undefined || widget.reBorderColor === 'none'){
          var reBorderColor = "#E8E7E0"; //念のため
          var reBorderNone = 1;
        }
        else{
          var reBorderColor = widget.reBorderColor;
          var reBorderNone = 0;
        }
        if(widget.seBorderColor === undefined || widget.seBorderColor === 'none'){
          var seBorderColor = "#E8E7E0"; //念のため
          var seBorderNone = 1;
        }
        else{
          var seBorderColor = widget.seBorderColor;
          var seBorderNone = 0;
        }
        if(widget.messageBoxBorderColor === undefined || widget.messageBoxBorderColor === 'none'){
          var messageBoxBorderColor = "#E8E7E0"; //念のため
          var messageBoxBorderNone = 1;
        }
        else{
          var messageBoxBorderColor = widget.messageBoxBorderColor;
          var messageBoxBorderNone = 0;
        }
        if(widget.widgetBorderColor === undefined || widget.widgetBorderColor === 'none'){
          var widgetBorderColor = "#E8E7E0"; //念のため
          var widgetBorderNone = 1;
        }
        else{
          var widgetBorderColor = widget.widgetBorderColor;
          var widgetBorderNone = 0;
        }
        if(widget.widgetInsideBorderColor === undefined || widget.widgetInsideBorderColor === 'none'){
          var widgetInsideBorderColor = "#E8E7E0"; //念のため
          var widgetInsideBorderNone = 1;
        }
        else{
          var widgetInsideBorderColor = widget.widgetInsideBorderColor;
          var widgetInsideBorderNone = 0;
        }
        var portioneArray = {
          //1.メインカラー
          mainColor: widget.mainColor,
          //2.タイトル文字色
          stringColor: widget.stringColor,
          //3.吹き出し文字色
          messageTextColor: widget.messageTextColor,
          //4.その他文字色
          otherTextColor: widget.otherTextColor,
          //5.ウィジェット枠線色
          widgetBorderColor: widget.widgetBorderColor,
          //6.吹き出し枠線色
          chatTalkBorderColor: widget.chatTalkBorderColor,
          //ヘッダー背景色
          headerBackgroundColor: widget.headerBackgroundColor,
          //7.企業名文字色
          subTitleTextColor: widget.subTitleTextColor,
          //8.説明文文字色
          descriptionTextColor: widget.descriptionTextColor,
          //9.チャットエリア背景色
          chatTalkBackgroundColor: widget.chatTalkBackgroundColor,
          //10.企業名担当者名文字色
          cNameTextColor: widget.cNameTextColor,
          //11.企業側吹き出し文字色
          reTextColor: widget.reTextColor,
          //12.企業側吹き出し背景色
          reBackgroundColor: widget.reBackgroundColor,
          //13.企業側吹き出し枠線色
          reBorderColor: reBorderColor,
          //14.企業側吹き出し枠線なし
          reBorderNone: reBorderNone,
          //15.訪問者側吹き出し文字色
          seTextColor: widget.seTextColor,
          //16.訪問者側吹き出し背景色
          seBackgroundColor: widget.seBackgroundColor,
          //17.訪問者側吹き出し枠線色
          seBorderColor: seBorderColor,
          //18.訪問者側吹き出し枠線なし
          seBorderNone: seBorderNone,
          //19.メッセージエリア背景色
          chatMessageBackgroundColor: widget.chatMessageBackgroundColor,
          //20.メッセージBOX文字色
          messageBoxTextColor: widget.messageBoxTextColor,
          //21.メッセージBOX背景色
          messageBoxBackgroundColor: widget.messageBoxBackgroundColor,
          //22.メッセージBOX枠線色
          messageBoxBorderColor: messageBoxBorderColor,
          //23.メッセージBOX枠線なし
          messageBoxBorderNone: messageBoxBorderNone,
          //24.送信ボタン文字色
          chatSendBtnTextColor: widget.chatSendBtnTextColor,
          //25.送信ボタン背景色
          chatSendBtnBackgroundColor: widget.chatSendBtnBackgroundColor,
          //26.ウィジット内枠線色
          widgetInsideBorderColor: widgetInsideBorderColor,
          //27.ウィジット内枠線なし
          widgetInsideBorderNone: widgetInsideBorderNone,
          //28.ウィジット外枠線なし
          widgetBorderNone: widgetBorderNone
        };
      }
      return portioneArray;
    },
    //サイズを返す関数
    getSizeType: function(sizeTypeID){
      switch (sizeTypeID) {
        case 1: //小
          var sizeArray = {
            boxHeight: 447,
            boxWidth: 285,
            widgetTitlePadding: 7,
            widgetTitleHeight: 32,
            widgetTitleTop: 6,
            widgetSubTitleHeight: 29,
            widgetDescriptionHeight: 23,
            navigationHeight: 40,
            chatTalkHeight: 194,
            classFlexBoxRowHeight: 75,
            sincloAccessInfoHeight: 26.5,
            fotterHeight: 26.5,
            telContentHeight: 125,
            d11font: 11,
            d12font: 12,
            d13font: 13,
            d14font: 14,
            d18font: 18,
            d20font: 20,
            d25font: 25
          };
          break;
        case 2: //中
          var sizeArray = {
            boxHeight: 540.5,
            boxWidth: 342.5,
            widgetTitlePadding: 7,
            widgetTitleHeight: 32,
            widgetTitleTop: 6,
            widgetSubTitleHeight: 29,
            widgetDescriptionHeight: 23,
            navigationHeight: 40,
            chatTalkHeight: 284,
            classFlexBoxRowHeight: 75,
            sincloAccessInfoHeight: 26.5,
            fotterHeight: 26.5,
            telContentHeight: 214.5,
            d11font: 12,
            d12font: 13,
            d13font: 14,
            d14font: 15,
            d18font: 19,
            d20font: 21,
            d25font: 26
          };
          break;
        case 3: //大
          var sizeArray = {
            boxHeight: 632,
            boxWidth: 400,
            widgetTitlePadding: 7,
            widgetTitleHeight: 32,
            widgetTitleTop: 6,
            widgetSubTitleHeight: 29,
            widgetDescriptionHeight: 23,
            navigationHeight: 40,
            chatTalkHeight: 374,
            classFlexBoxRowHeight: 75,
            sincloAccessInfoHeight: 26.5,
            fotterHeight: 26.5,
            telContentHeight: 305,
            d11font: 12,
            d12font: 13,
            d13font: 14,
            d14font: 15,
            d18font: 19,
            d20font: 21,
            d25font: 26
          };
          break;
        default: //該当しないタイプが来たら小
          var sizeArray = {
            boxHeight: 447,
            boxWidth: 285,
            widgetTitlePadding: 7,
            widgetTitleHeight: 32,
            widgetTitleTop: 6,
            widgetSubTitleHeight: 29,
            widgetDescriptionHeight: 23,
            navigationHeight: 40,
            chatTalkHeight: 194,
            classFlexBoxRowHeight: 75,
            sincloAccessInfoHeight: 26.5,
            fotterHeight: 26.5,
            telContentHeight: 125,
            d11font: 11,
            d12font: 12,
            d13font: 13,
            d14font: 14,
            d18font: 18,
            d20font: 20,
            d25font: 25
          };
          break;
      }
      return sizeArray;
    },
    //最小化時と最大化時の状態を取得する関数
    getAbridgementType: function() {
      var widget = window.sincloInfo.widget;
      //1/2/3:シンプル表示しない/スマホのみシンプル表示する/すべての端末でシンプル表示する
      var minimizeDesignType = Number(widget.minimizeDesignType);
      //最大時のシンプル表示(スマホ) 1/2:する/しない
      var spHeaderLightFlg = Number(widget.spHeaderLightFlg);
      //最大化時、最小化時シンプル表示するしない true/false:する/しない
      var pcMaxRes = false;
      var pcMinRes = false;
      var spMaxRes = false;
      var spMinRes = false;
      switch (widget.minimizeDesignType) {
      case 1: //シンプル表示しない
        if(spHeaderLightFlg === 1){
          //最大時のシンプル表示(スマホ)する
          //pc最小化中
          pcMinRes = false;
          //pc最大化中
          pcMaxRes = false;
          //sp最小化中
          spMinRes = false;
          //sp最大化中
          spMaxRes = true;
        }
        else{
          //最大時のシンプル表示(スマホ)しない
          //pc最小化中
          pcMinRes = false;
          //pc最大化中
          pcMaxRes = false;
          //sp最小化中
          spMinRes = false;
          //sp最大化中
          spMaxRes = false;
        }
        break;
      case 2: //スマホのみシンプル表示する
        if(spHeaderLightFlg === 1){
          //最大時のシンプル表示(スマホ)する
          //pc最小化中
          pcMinRes = false;
          //pc最大化中
          pcMaxRes = false;
          //sp最小化中
          spMinRes = true;
          //sp最大化中
          spMaxRes = true;
        }
        else{
          //最大時のシンプル表示(スマホ)しない
          //pc最小化中
          pcMinRes = false;
          //pc最大化中
          pcMaxRes = false;
          //sp最小化中
          spMinRes = true;
          //sp最大化中
          spMaxRes = false;
        }
        break;
      case 3: //すべての端末でシンプル表示する
        if(spHeaderLightFlg === 1){
          //最大時のシンプル表示(スマホ)する
          //pc最小化中
          pcMinRes = true;
          //pc最大化中
          pcMaxRes = false;
          //sp最小化中
          spMinRes = true;
          //sp最大化中
          spMaxRes = true;
        }
        else{
          //最大時のシンプル表示(スマホ)しない
          //pc最小化中
          pcMinRes = true;
          //pc最大化中
          pcMaxRes = false;
          //sp最小化中
          spMinRes = true;
          //sp最大化中
          spMaxRes = false;
        }
        break;
      default ://該当しない場合はシンプル表示しない
        if(spHeaderLightFlg === 1){
          //最大時のシンプル表示(スマホ)する
          //pc最小化中
          pcMinRes = false;
          //pc最大化中
          pcMaxRes = false;
          //sp最小化中
          spMinRes = false;
          //sp最大化中
          spMaxRes = true;
        }
        else{
          //最大時のシンプル表示(スマホ)しない
          //pc最小化中
          pcMinRes = false;
          //pc最大化中
          pcMaxRes = false;
          //sp最小化中
          spMinRes = false;
          //sp最大化中
          spMaxRes = false;
        }
        break;
      }
      var abridgementType = {
          pcMinRes: pcMinRes,
          pcMaxRes: pcMaxRes,
          spMinRes: spMinRes,
          spMaxRes: spMaxRes
      };
      //false/true:通常（PC）/スマホ
      var smartphone = check.smartphone();
      if(smartphone){
        //スマホだったら縦か横かを判定
        if($(window).height() > $(window).width()){
          //縦
          var MinRes = abridgementType['spMinRes'];
          var MaxRes = abridgementType['spMaxRes'];
        }
        else{
          //横
          var MinRes = true;
          var MaxRes = true;
        }
      }
      else{
        //PC
        var MinRes = abridgementType['pcMinRes'];
        var MaxRes = abridgementType['pcMaxRes'];
      }
      var res = {
          MinRes: MinRes,
          MaxRes: MaxRes
      }
      return res;
    },
    //ヘッダ表示（通常表示）
    abridgementTypeShow: function() {
      $("#mainImage").show();
      $("#widgetSubTitle").show();
      $("#widgetDescription").show();
      var smartphone = check.smartphone();
      if(smartphone){
        if( $(window).height() > $(window).width() ){
          if(!$('#sincloBox p#widgetTitle').hasClass("notSimple")){
            $('#sincloBox p#widgetTitle').addClass("notSimple");
          }
        }
        else{
          //横
          $('#widgetTitle').css('text-align', 'center');
        }
      }
      else{
        if(!$('#sincloBox p#widgetTitle').hasClass("notSimple")){
          $('#sincloBox p#widgetTitle').addClass("notSimple");
        }
      }
      if((Number(window.sincloInfo.widget.showMainImage) === 2 || window.sincloInfo.widget.mainImage === "")) {
        $('#sincloBox p#widgetTitle').addClass("noImage");
      }
    },
    //ヘッダ非表示（シンプル表示）
    abridgementTypehide: function() {
      $("#mainImage").hide();
      $("#widgetSubTitle").hide();
      $("#widgetDescription").hide();
      //false/true:通常（PC）/スマホ
      var smartphone = check.smartphone();
      if(smartphone){
        //スマホ時
        //スマホだったら縦か横かを判定
        if($(window).height() > $(window).width()){
          //縦
          if($('#sincloBox p#widgetTitle').hasClass("notSimple")){
            $('#sincloBox p#widgetTitle').removeClass("notSimple");
          }
        }
        else{
          //横
          $('#widgetTitle').css('text-align', 'center');
        }
      }
      else{
        //PC時
        if ($('#sincloBox p#widgetTitle').hasClass("notSimple")) {
          $('#sincloBox p#widgetTitle').removeClass("notSimple");
        }
      }
      if((Number(window.sincloInfo.widget.showMainImage) === 2 || window.sincloInfo.widget.mainImage === "")) {
        $('#sincloBox p#widgetTitle').addClass("noImage");
      }
    },
    //最大化時ボタン表示
    whenMaximizedBtnShow: function() {
      $("#minimizeBtn").show();
      //$("#addBtn").hide();
      $("#closeBtn").hide();
    },
    //最小化時ボタン表示
    whenMinimizedBtnShow: function() {
      var widget = window.sincloInfo.widget, displaySet = "";
      if ( Number(widget.closeButtonSetting) === 1 ) {
        //閉じるボタン無効
        $("#minimizeBtn").hide();
        //$("#addBtn").show();
        $("#closeBtn").hide();
      }
      else{
        //閉じるボタン有効
        $("#minimizeBtn").hide();
        //$("#addBtn").show();
        var smartphone = check.smartphone();
        if(smartphone){
          //スマホ時
          //スマホだったら縦か横かを判定
          if($(window).height() > $(window).width()){
            //縦
            $("#closeBtn").show();
          }
          else{
            $("#closeBtn").hide();
          }
        }
        else{
          $("#closeBtn").show();
        }
      }
    },
    widgetCssTemplate: function(widget){
      // システムで出力するテキストのカラー
      var systemTextColor = "#666666";
      // チャットのフォントカラー
      var chatContentTextColor = "#333333";

      //サイズを取得
      var sizeList = this.getSizeType(widget.widgetSizeType);

      //カラーリストの取得
      var colorList = this.getColorList(widget);

      var html = "", faintColor = widget.mainColor, balloonInnerColor = faintColor = widget.mainColor;
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
        var balloonR = String(Math.floor(255 - (255 - parseInt(r,16)) * 0.1));
        var balloonG = String(Math.floor(255 - (255 - parseInt(g,16)) * 0.1));
        var balloonB = String(Math.floor(255 - (255 - parseInt(b,16)) * 0.1));
        balloonInnerColor = 'rgb(' + balloonR  + ', ' +  balloonG  + ', ' +  balloonB + ');';
      }

      // 表示位置
      var showPosition = "", chatPosition = {se: "", re: ""};
      switch ( Number(widget.showPosition) ) {
        case 1: // 右下
          showPosition = "bottom: 0; right: 10px;";
          chatPosition = {
            se: "margin-left: 10px;",
            re: "margin-right: 10px;"
          };
          break;
        case 2: // 左下
          showPosition = "bottom: 0; left: 10px;";
          chatPosition = {
            se: "margin-left: 10px;",
            re: "margin-right: 10px;"
          };
          break;
      }

      switch ( Number(widget.chatMessageDesignType) ) {
        case 1: //BOX型
          chatPosition.se += " border-bottom-right-radius: 0;";
          chatPosition.re += " border-bottom-left-radius: 0;";
          break;
        case 2: //吹き出し型
          // 何もしない
          break;
        default: //BOX型
          chatPosition.se += " border-bottom-right-radius: 0;";
          chatPosition.re += " border-bottom-left-radius: 0;";
          break;
      }

      // 基本設定
      var widgetWidth = 285, ratio = 1;
      // ユーザーエージェント
      var ua  =  navigator.userAgent.toLowerCase();

      html += '  <style>';

      /* 共通スタイル */
      html += '      @media print{ sinclo { display:none!important; } }';
      //アイコンフォント用
      html += '      @font-face { font-family: "SincloFont"; src: url("https://netdna.bootstrapcdn.com/font-awesome/4.7.0/fonts/fontawesome-webfont.eot?v=4.7.0"); src: url("https://netdna.bootstrapcdn.com/font-awesome/4.7.0/fonts/fontawesome-webfont.eot?#iefix&v=4.7.0") format("embedded-opentype"), url("https://netdna.bootstrapcdn.com/font-awesome/4.7.0/fonts/fontawesome-webfont.woff?v=4.7.0") format("woff"), url("https://netdna.bootstrapcdn.com/font-awesome/4.7.0/fonts/fontawesome-webfont.ttf?v=4.7.0") format("truetype"), url("https://netdna.bootstrapcdn.com/font-awesome/4.7.0/fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular") format("svg"); font-weight: normal; font-style: normal }';
      html += '      #sincloBox .sinclo-fa { display: inline-block; font-family: SincloFont ; font-style: normal; font-weight: normal; line-height: 1; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; cursor: pointer; }';
      //アイコンフォント用
      html += '      #sincloBox { display: none; position: fixed; ' + showPosition + ' z-index: 999998; background-color: rgba(0,0,0,0);}';
      html += '      #sincloBox * { color: #333333; line-height: 1.3; box-sizing: border-box; font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana; letter-spacing: initial;}';
      html += '      #sincloBox *:before, #sincloBox *:after { box-sizing: content-box; }';
      html += '      #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }';
      //html += '      #sincloBox a { color: #8a8a8a }';
      html += '      #sincloBox .sinclo_se a { color: '+ colorList['seTextColor'] +' }';
      html += '      #sincloBox .sinclo_re a { color: '+ colorList['reTextColor'] +' }';
      html += '      #sincloBox ul#chatTalk li.sinclo_re span.telno { color: ' + colorList['reTextColor'] + '; }';
      html += '      #sincloBox sinclo-div {display: block; }';
      html += '      #sincloBox label { display: inline; max-width: 100%; margin-bottom: 0; font-weight: normal;}';
      html += '      #sincloBox a:hover { color: ' + colorList['mainColor'] + '; }';
      html += '      #sincloBox sinclo-div#widgetHeader { cursor:pointer; position: relative;}';
      html += '      #sincloBox sinclo-div#widgetHeader:after { content: " "; position: absolute; bottom: 0; left: 0; right: 0; z-index: -1; background-color: #FFF; }';
      html += '      #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }';
      html += '      #sincloBox span#mainImage { cursor:pointer; z-index: 2; position: absolute; }';
      html += '      #sincloBox span#mainImage img { background-color: ' + colorList['mainColor'] + ' }';
      html += '      #sincloBox p#widgetTitle { position:relative; cursor:pointer; border-bottom:none; background-color: ' + colorList['mainColor'] + ';text-align: center; margin: 0; color: ' + colorList['stringColor'] + ' }';
      html += '      #sincloBox p#widgetTitle #sincloChatUnread { position: absolute; top: 0; left: 0; color: #FFF; font-style: normal; text-align: center; font-weight: bold; background-color: #FF5C5C; }';

//    html += '      #sincloBox p#widgetTitle:after { background-image: url("' + window.sincloInfo.site.files + '/img/widget/yajirushi.png"); content: " "; display: inline-block; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; -webkit-transition: 200ms linear; transition: transform 200ms linear}';
//    html += '      #sincloBox[data-openflg="true"] p#widgetTitle:after { -ms-transform: rotate(0deg); -moz-transform: rotate(0deg); -webkit-transform: rotate(0deg); -o-transform: rotate(0deg); transform: rotate(0deg); }';
//    html += '      #sincloBox[data-openflg="false"] p#widgetTitle:after { -ms-transform: rotate(180deg); -moz-transform: rotate(180deg); -webkit-transform: rotate(180deg); -o-transform: rotate(180deg); transform: rotate(180deg); }';

      html += '      #sincloBox div#minimizeBtn { display: none; cursor: pointer; background-image: url("' + window.sincloInfo.site.files + '/img/widget/minimize.png"); background-position-y: 0px; top: 6px; right: 6px; bottom: 6px; content: " "; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }';
      //＋ボタンと×ボタンは閉じるボタン設定によってポジションが異なるため別々に記載。なお、IDは同一とする
      if ( Number(widget.closeButtonSetting) === 1 ) {
        //閉じるボタン無効
/*
        html += '      #sincloBox div#addBtn { display: none; cursor: pointer; background-image: url("' + window.sincloInfo.site.files + '/img/widget/add.png"); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }';
*/
        html += '      #sincloBox div#closeBtn { display: none; cursor: pointer; background-image: url("' + window.sincloInfo.site.files + '/img/widget/close.png"); background-position-y: -1.5px; top: 7px; right: 6px; bottom: 6px; content: " "; width: 18px; height: 18px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }';
      }
      else{
        //閉じるボタン有効
/*
        html += '      #sincloBox div#addBtn { display: none; cursor: pointer; background-image: url("' + window.sincloInfo.site.files + '/img/widget/add.png"); background-position-y: 0px; top: 6px; right: 25px; bottom: 6px; content: " "; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }';
*/
        html += '      #sincloBox div#closeBtn { display: none; cursor: pointer; background-image: url("' + window.sincloInfo.site.files + '/img/widget/close.png"); background-position-y: -1.5px; top: 7px; right: 6px; bottom: 6px; content: " "; width: 18px; height: 18px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }';
      }

      html += '      #sincloWidgetBox > section { background-color: #FFF; border-top: none; }';
      html += '      #sincloBox ul#chatTalk li a, #sincloBox #fotter a {  text-decoration: underline; }';
      html += '      #sincloBox section { display: none; padding: 0!important; }';
      html += '      #sincloBox .flexBox { position: relative; display: -ms-flexbox; display: -webkit-flex; display: flex; -ms-flex-direction: column; -webkit-flex-direction: column; flex-direction: column }';
      if(widget.chatMessageCopy === 1) {
        html += '      #sincloBox .flexBoxRow { display: -ms-flexbox; display: -webkit-flex; display: flex; -ms-flex-direction: row; -webkit-flex-direction: row; flex-direction: row; user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;}';
      }
      else {
        html += '      #sincloBox .flexBoxRow { display: -ms-flexbox; display: -webkit-flex; display: flex; -ms-flex-direction: row; -webkit-flex-direction: row; flex-direction: row; }';
      }
      // チャットを使用する際
      if ( window.sincloInfo.contract.chat ) {
        html += '      @keyframes rightEffect { 0% { transform :translate3d(20px, 0px, 0px); opacity :0; } 70% { } 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }';
        html += '      @keyframes leftEffect { 0% { transform :translate3d(-20px, 0px, 0px) scale(0.8); opacity :0; } 69% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }';
        html += '      @keyframes fadeIn { 0% { opacity :0; } 100% { opacity :1; } }';
        html += '      #sincloBox #mainImage em { position: absolute; background-image: url("' + window.sincloInfo.site.files + '/img/chat-bg.png");background-size: contain;background-repeat: no-repeat; color: #FFF; font-style: normal; text-align: center; font-weight: bold }';
        // ファイルフォントアイコン-----------
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-4x { font-size:4em; }'
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-o:before { content: "\\f016" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-image-o:before { content: "\\f1c5" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-pdf-o:before { content: "\\f1c1" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-word-o:before { content: "\\f1c2" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-powerpoint-o:before { content: "\\f1c4" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-excel-o:before { content: "\\f1c3" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-audio-o:before { content: "\\f1c7" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-video-o:before { content: "\\f1c8" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-zip-o:before { content: "\\f1c6" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-code-o:before { content: "\\f1c9" }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sinclo-fa.fa-file-text-o:before { content: "\\f0f6" }';
        // ------------ファイルフォントアイコン
        html += '      #sincloBox ul#chatTalk li div.sendFileContent { display: table; table-layout:fixed; width: 100%; height: 64px; white-space: pre-line; margin-bottom: 0; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileContent * { color: '+ colorList['reTextColor'] +' }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea { display: table-cell; width: 64px; height: 64px; border: 1px solid #D9D9D9; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileMetaArea { display:table-cell; vertical-align: middle; margin-left: 10px; margin-bottom: 0px; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sendFileThumbnail { text-align: center; vertical-align: middle; display: inline-block; width: 100%; height: auto; margin-left: 0; margin-bottom: 0px; margin-right: auto; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea:before { content: ""; height: 100%; vertical-align: middle; width: 0px; display: inline-block; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileMetaArea .data { margin-left: 1em; margin-bottom: 5px; display: block; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileMetaArea .data.sendFileSize { margin-bottom: 0px; }';

        if(widget.chatMessageCopy === 1) {
          console.log('1に入っている');
          //　チャット本文コピーできない
          html += '      #sincloBox ul#chatTalk { width: 100%; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0; clear: both!important; user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none; }';
        }
        else {
          console.log(widget.chatMessageCopy);
          console.log('0に入っている');
          //　チャット本文コピーできる
          html += '      #sincloBox ul#chatTalk { width: 100%; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0; clear: both!important; }';
        }
        html += '      #sincloBox ul sinclo-chat { clear: both!important } ';
        html += '      #sincloBox ul#chatTalk li { text-align: left!important; word-wrap: break-word; word-break: break-all; white-space: pre-wrap!important; background-color: transparent; white-space: pre; color: ' + chatContentTextColor + '; font-weight: normal!important; }';
        if (widget.chatMessageDesignType === 2) {
          // 吹き出し型
          html += '      #sincloBox ul#chatTalk li { line-height: 1.4; padding: 5px 15px !important; border-radius: 12px !important;}';
          html += '      #sincloBox ul#chatTalk li.sinclo_se { ' + chatPosition.se + 'background-color: '+ colorList['seBackgroundColor'] +'; display: inline-block; position: relative; margin-right: 15px; border-bottom-right-radius: 0px!important; color:'+ colorList['seTextColor'] +' }';
          html += '      #sincloBox ul#chatTalk li.sinclo_se:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; border: 5px solid transparent; border-left: 5px solid '+ colorList['seBackgroundColor'] +'; border-bottom: 5px solid '+ colorList['seBackgroundColor'] +'; z-index: 2; }';
          html += '      #sincloBox ul#chatTalk li.sinclo_se:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; z-index: 1 }';
          if(colorList['seBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_se:after {border-left: 5px solid '+ colorList['seBorderColor'] +'; border-bottom: 5px solid '+ colorList['seBorderColor'] +'; }';
          }
          html += '      #sincloBox ul#chatTalk li.sinclo_re { ' + chatPosition.re + 'background-color:' + colorList['reBackgroundColor'] + '; display: inline-block; position: relative; margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px!important; color:'+ colorList['reTextColor'] +' }';
          html += '      #sincloBox ul#chatTalk li.sinclo_re:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; border: 5px solid transparent; border-right: 5px solid ' + colorList['reBackgroundColor'] + '; border-bottom: 5px solid ' + colorList['reBackgroundColor'] + '; z-index: 2; }';
          html += '      #sincloBox ul#chatTalk li.sinclo_re:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; border: 5px solid transparent; z-index: 1; }';
          if(colorList['reBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_re:after {border-right: 5px solid '+ colorList['reBorderColor'] +'; border-bottom: 5px solid '+ colorList['reBorderColor'] +'; }';
          }
        } else {
          // BOX型
          html += '      #sincloBox ul#chatTalk li.sinclo_se { ' + chatPosition.se + 'background-color: '+ colorList['seBackgroundColor'] + '; color:' + colorList['seTextColor'] + '; }';
          html += '      #sincloBox ul#chatTalk li.sinclo_re { ' + chatPosition.re + 'background-color:' + colorList['reBackgroundColor'] + '; color:' + colorList['reTextColor'] + '; }';
        }
        if (widget.chatMessageWithAnimation === 1) {
          html += '      #sincloBox ul#chatTalk li.effect_right { -webkit-animation-name:rightEffect; -moz-animation-name:rightEffect; -o-animation-name:rightEffect; -ms-animation-name:rightEffect; animation-name:rightEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }';
          html += '      #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }';
        }
        html += '      #sincloBox ul#chatTalk li#sinclo_typeing_message { position: relative; color: #d5d5d5; border: none; text-align: center; }';
        html += '      #sincloBox ul#chatTalk li#sinclo_typeing_message span { position: absolute; top: 0; bottom: 0; left: 50%; display: block; }';
        html += '      #sincloBox ul#chatTalk li span.cName { display: block; color: ' + colorList['cNameTextColor'] + '; font-weight: bold; }';
        html += '      #sincloBox ul#chatTalk li.sinclo_etc { border: none; text-align: center!important; margin: 0 auto; font-weight: bold }';
        html += '      #sincloBox ul#chatTalk li sinclo-radio { display: block; margin-top: 0.1em; margin-bottom: -1.25em; } ';
        html += '      #sincloBox ul#chatTalk li sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; } ';
        html += '      #sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; padding: 0 0 0 15px; color:'+ colorList['reTextColor'] +' } ';
        html += '      #sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: 1px; left: 0px; width: 11px; height: 11px; border: 1px solid #999; border-radius: 50%; background-color: #FFF; } ';
        html += '      #sincloBox ul#chatTalk li sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: 4px; left: 3px; width: 7px; height: 7px; background: ' + colorList['mainColor'] + '; border-radius: 50%; } ';
        html += '      #sincloBox ul#chatTalk sinclo-chat-receiver { cursor: pointer; display: none; position: absolute; left: 0; right: 0; width: 100%; height: 1.5em; background-color: rgba(0, 0, 0, 0.45); vertical-align: middle; word-wrap: break-word; z-index: 2; } ';
        html += '      #sincloBox ul#chatTalk sinclo-chat-receiver:before {content: ""; display: inline-block; border: 6px solid transparent; position: absolute; top: 50%; margin-top: -3px; left: 5px; height: 8px; border-top: 8px solid white; border-bottom: 0; }';
        html += '      #sincloBox ul#chatTalk sinclo-chat-receiver #receiveMessage { overflow: hidden; font-size: 10px; height: 100%; line-height: 2em; padding-left: 2em; color: #FFF; }';
        html += '      #sincloBox section#chatTab sinclo-div { border-top: 1px solid '+ colorList['widgetInsideBorderColor'] +'; background-color: '+ colorList['chatMessageBackgroundColor'] +';}';
        if(colorList['widgetInsideBorderNone'] === 1){
          html += '      #sincloBox section#chatTab sinclo-div { border-top: none!important;}';
        }
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage { display: block; height: 100%; min-height: 100%!important; margin: 0; width: 80%; resize: none; color: ' + colorList['messageBoxTextColor'] + '!important; background-color: '+ colorList['messageBoxBackgroundColor'] +'; }';
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage:focus { outline: none; border-color: ' + colorList['mainColor'] + '!important }';
        if(colorList['messageBoxBorderNone'] === 0){
          html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage { border-right-color: ' + colorList['chatSendBtnBackgroundColor'] + '!important; }';
          html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage:focus { border-color: ' + colorList['mainColor'] + '!important }';
        }
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatSendBtn { display: block; height: 100%; width: 20%; text-decoration: none; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0; text-align: center; background-color: ' + colorList['chatSendBtnBackgroundColor'] + '; color: ' + colorList['chatSendBtnTextColor'] + '; font-weight: bold; font-size: 1.2em;}';
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatSendBtn span { color: ' + colorList['chatSendBtnTextColor'] + '; }';
        if( window.sincloInfo.contract.sinclo || (window.sincloInfo.contract.hasOwnProperty("document") && window.sincloInfo.contract.document ) ) {
          if(widget.chatMessageCopy === 1) {
            html += '      #sincloBox section#chatTab #sincloAccessInfo { height: '+ sizeList['sincloAccessInfoHeight'] +'px; text-align: left; padding-left: 0.5em; padding-top: 5px; padding-bottom: 5px; font-size: 0.9em; border-top: 1px solid ' + colorList['widgetInsideBorderColor'] + '; user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none; }';
          }
          else {
            html += '      #sincloBox section#chatTab #sincloAccessInfo { height: '+ sizeList['sincloAccessInfoHeight'] +'px; text-align: left; padding-left: 0.5em; padding-top: 5px; padding-bottom: 5px; font-size: 0.9em; border-top: 1px solid ' + colorList['widgetInsideBorderColor'] + ' }';
          }
          if(colorList['widgetInsideBorderNone'] === 1){
            html += '      #sincloBox section#chatTab #sincloAccessInfo { border-top: none!important; }';
          }
        }
      }
      html += '      #sincloBox section#navigation { position: relative; display: block; top: 0px!important; background: #FFF!important; }';
      html += '      #sincloBox section#navigation ul { display: table; padding: 0; position: absolute; top: 0; left: 0; }';
      if(widget.chatMessageCopy === 1) {
        html += '      #sincloBox #fotter { text-align: center; color: #A1A1A1!important; background-color: #FFF; margin: 0;border-top: none; user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;}';
      }
      else {
        html += '      #sincloBox #fotter { text-align: center; color: #A1A1A1!important; background-color: #FFF; margin: 0;border-top: none; }';
      }
      html += '      #sincloBox section#navigation ul li { position: relative; overflow: hidden; cursor: pointer; color: ' + colorList['otherTextColor'] + '; text-align: center; display: table-cell; line-height: inherit!important; }';
      html += '      #sincloBox section#navigation ul li.selected { background-color: ' + colorList['chatTalkBackgroundColor'] + '; }';
      html += '      #sincloBox section#navigation ul li::before{ color: #BCBCBC; content: " "; display: inline-block; position: relative; background-size: contain; vertical-align: middle; background-repeat: no-repeat }';
      html += '      #sincloBox section#navigation ul li.selected::after{ content: " "; position: absolute; bottom: 0px; left: 5px; right: 5px; }';
/*
      html += '      #sincloBox section#navigation ul li[data-tab="call"]::before{ background-image: url("' + window.sincloInfo.site.files + '/img/widget/icon_tel.png"); }';
      html += '      #sincloBox section#navigation ul li[data-tab="chat"]::before{ background-image: url("' + window.sincloInfo.site.files + '/img/widget/icon_chat.png"); }';
*/
      //画像からアイコンフォントに差し替え
      html += '      #sincloBox section#navigation ul li[data-tab="call"]::before{ content: "\\f095"; font-family: SincloFont; font-size: 17px; margin: -5px 7px 0 0; font-weight: bold; }';
      html += '      #sincloBox section#navigation ul li[data-tab="chat"]::before{ content: "\\f075"; font-family: SincloFont; font-size: 17px; margin: -5px 7px 0 0; transform: scale( 1 , 1.1 ); }';

      html += '      #sincloBox section#navigation ul li.selected::before{ color: ' + colorList['mainColor'] + '; }';
      //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
      if(Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1){
        html += '      #sincloBannerBox{ bottom:0px; right:0px; }';
        html += '      #sincloBanner .sinclo-fa { display: inline-block; font-family: SincloFont ; font-style: normal; font-weight: normal; line-height: 1; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; cursor: pointer; }';
        html += '      #sincloBanner .sinclo-fa.fa-comment:before { content: "\\f075" }';
        html += '      #sincloBanner.sincloBanner { position: relative; z-index: 1; height: 42px; width : -webkit-fit-content !important; width : -moz-fit-content !important; width : fit-content !important; background-color: '+ colorList['mainColor'] +'; box-shadow: 0px 0px ' + widget.boxShadow + 'px ' + widget.boxShadow + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px; color: '+ colorList['stringColor'] +'; margin: auto; filter:alpha(opacity=90); -moz-opacity: 0.9; opacity: 0.9; cursor: pointer; }';
        html += '      #sincloBanner.sincloBannerText{ line-height: 42px; height: auto!important; width: auto!important; padding:0; }';
        html += '      #sincloBanner.sincloBanner i{ color: '+ widget.stringColor +'; }';
        html += '      #sincloBanner.sincloBanner .sinclo-comment{ transform: scale( 1 , 1.4 ); font-size: 17.5px; padding: 0 2px 0 10px; cursor: pointer; }';
        html += '      #sincloBanner.sincloBanner .sinclo-comment-notext{ transform: scale( 1 , 1.4 ); font-size: 17.5px; padding: 0 2px 0 13px; cursor: pointer; }';
        html += '      #sincloBanner.sincloBanner .bannertext{ color: '+ colorList['stringColor'] +'; font-size: 12.5px; padding: 0 10px 0 3px; cursor: pointer; }';
        html += '      #sincloBanner.sincloBanner .notext{ padding: 0 7px 0 3px; cursor: pointer; }';
        //スマホだったらpxの書き換え
//        if ( check.smartphone() ) {
//          widgetWidth = $(window).width() - 20;
//          ratio = widgetWidth * (1/285);
//          html += '      #sincloBannerBox{ bottom:0px; right:0px; }';
//          html += '      #sincloBanner.sincloBanner { height: '+ (60 * ratio) +'px; box-shadow: 0px 0px ' + widget.boxShadow + 'px ' + widget.boxShadow + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px; }';
//          html += '      #sincloBanner.sincloBannerText{ line-height: '+ (60 * ratio) +'px; padding:0; }';
//          html += '      #sincloBanner.sincloBanner .sinclo-comment{ font-size: '+ (25 * ratio) +'px; padding: 0 '+ (15 * ratio) +'px 0 '+ (15 * ratio) +'px; }';
//          html += '      #sincloBanner.sincloBanner .bannertext{ font-size: '+ (18 * ratio) +'px; padding: 0 '+ (15 * ratio) +'px 0 0; }';
//        }
      }

// html += '    #sincloBox ul { clear: both; display: flex; flex-direction: column } ';
// html += '    #sincloBox sinclo-chat, #sincloBox sinclo-typing { display: block; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; } ';

      /* iPhone/iPod/Androidの場合 */
      if ( check.smartphone() ) {
        // TODO 関数化
        widgetWidth = $(window).width() - 20;
        ratio = widgetWidth * (1/285);

        html += '#sincloBox { -webkit-transition: 100ms linear 0ms;  transition: opacity 100ms linear 0ms; }';
        html += '#sincloBox section#chatTab sinclo-div { position: relative }';
        html += '#sincloBox section#chatTab sinclo-chat-alert { position: absolute; background-color: rgba(0,0,0,0.6); box-shadow: 0px 0px 4px 4px rgba(0,0,0,0.1); color: #FFF; text-align: center; }';
        if ( sinclo.chatApi.sendErrCatchFlg ) {
          html += '#sincloBox section#chatTab sinclo-chat-alert { display:block }';
        }
        else {
          html += '#sincloBox section#chatTab sinclo-chat-alert { display:none }';
        }

        /* 縦の場合 */
        if ( $(window).height() > $(window).width() ) {
          html += '#sincloBox { width: ' + widgetWidth + 'px; }';
          html += '#sincloWidgetBox { box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px 0 0;}';
          html += '#sincloBox * { font-size: ' + (12 * ratio) + 'px; }';
          html += '#sincloBox section { width: ' + widgetWidth + 'px }';
          html += '#sincloBox section#navigation ul { width: ' + widgetWidth + 'px }';
          html += '#sincloBox span#mainImage { top: ' + (7 * ratio) + 'px; left: ' + (10 * ratio) + 'px; }';
          html += '#sincloBox sinclo-div#widgetHeader:after { top: ' + (32 * ratio) + 'px }';
          html += '#sincloBox p#widgetTitle { border-radius: ' + (widget.radiusRatio * ratio) + 'px ' + (widget.radiusRatio * ratio) + 'px 0 0; border: ' + (1 * ratio) + 'px solid ' + colorList['mainColor'] + '; font-size: ' + (14 * ratio) + 'px; padding: ' + (7 * ratio) + 'px ' + (30 * ratio) + 'px; height: ' + (32 * ratio) + 'px; }';
          if(widget.widgetSizeType !== 1){
            html += '#sincloBox p#widgetTitle { overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}';
          }
          html += '#sincloBox p#widgetTitle #sincloChatUnread { width: ' + (25 * ratio) + 'px; height: ' + (25 * ratio) + 'px; font-size: ' + (13 * ratio) + 'px; border-radius: ' + (15 * ratio) + 'px; margin: ' + (2.5 * ratio) + 'px ' + (6 * ratio) + 'px; padding: ' + (3 * ratio) + 'px; }';
//        html += '#sincloBox p#widgetTitle:after { background-position-y: ' + (3 * ratio) + 'px; top: ' + (6 * ratio) + 'px; right: ' + (10 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (20 * ratio) + 'px; height: ' + (20 * ratio) + 'px; }';
          html += '#sincloBox div#minimizeBtn { display: none; top: ' + (6 * ratio) + 'px; right: ' + (10 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (20 * ratio) + 'px; height: ' + (20 * ratio) + 'px; z-index: 2; }';
          //＋ボタンと×ボタンは閉じるボタン設定によってポジションが異なるため別々に記載。なお、IDは同一とする
          if ( Number(widget.closeButtonSetting) === 1 ) {
            //閉じるボタン無効
            //＋ボタン無効に仕様変更
/*
            html += '#sincloBox div#addBtn { display: none; top: ' + (6 * ratio) + 'px; right: ' + (10 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (20 * ratio) + 'px; height: ' + (20 * ratio) + 'px; z-index: 2; }';
*/
            html += '#sincloBox div#closeBtn { display: none; top: ' + (7 * ratio) + 'px; right: ' + (10 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (18 * ratio) + 'px; height: ' + (18 * ratio) + 'px; z-index: 2; }';
          }
          else{
            //閉じるボタン有効
/*
            html += '#sincloBox div#addBtn { display: none; top: ' + (6 * ratio) + 'px; right: ' + (25 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (20 * ratio) + 'px; height: ' + (20 * ratio) + 'px; z-index: 2; }';
*/
            html += '#sincloBox div#closeBtn { display: none; top: ' + (7 * ratio) + 'px; right: ' + (10 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (18 * ratio) + 'px; height: ' + (18 * ratio) + 'px; z-index: 2; }';
          }
          html += '#sincloBox p#widgetSubTitle { background-color: '+ colorList['headerBackgroundColor'] +'; border-color: '+ colorList['widgetBorderColor'] +'; font-weight: bold; color: ' + colorList['subTitleTextColor'] + '; border-style: solid; text-align: left; margin: 0; padding: ' + (7 * ratio) + 'px 0; border-width: 0 ' + (1 * ratio) + 'px 0 ' + (1 * ratio) + 'px; padding-left: ' + (77 * ratio) + 'px; height: ' + (29 * ratio) + 'px; }';
          if(widget.widgetSizeType !== 1){
            html += '#sincloBox p#widgetSubTitle { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }';
          }
          if(colorList['widgetBorderNone'] === 1){
            html += '#sincloBox p#widgetSubTitle { border:none; }';
          }
          html += '#sincloBox p#widgetDescription { background-color: '+ colorList['headerBackgroundColor'] +'; margin: 0; padding-bottom: ' + (7 * ratio) + 'px; border-width: 0 ' + (1 * ratio) + 'px ' + (1 * ratio) + 'px ' + (1 * ratio) + 'px; padding-left: ' + (77 * ratio) + 'px; height: ' + (23 * ratio) + 'px; text-align: left; border-color: '+ colorList['widgetBorderColor'] +'; border-style: solid; color: ' + colorList['descriptionTextColor'] + '; border-bottom-color:'+ colorList['widgetInsideBorderColor'] +'; }';
          if(widget.widgetSizeType !== 1){
            html += '#sincloBox p#widgetDescription { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }';
          }
          if(colorList['widgetBorderNone'] === 1){
            html += '#sincloBox p#widgetDescription { border-left:none; border-right:none;}';
          }
          html += '#sincloBox section { border: ' + (1 * ratio) + 'px solid '+ colorList['widgetBorderColor'] +'; border-top: none; border-bottom: ' + (1 * ratio) + 'px solid '+ colorList['widgetInsideBorderColor'] +';}';
          if(colorList['widgetBorderNone'] === 1){
            html += '#sincloBox section { border-top: none; border-left:none; border-right:none; }'
          }
          if(colorList['widgetInsideBorderNone'] === 1){
            html += '#sincloBox p#widgetDescription { border-bottom:none!important;}';
            html += '#sincloBox section { border-bottom:none!important;}';
          }
          // 画像がセットされている場合のスタイル
          html += '#sincloBox p#widgetTitle.notSimple { padding-left: ' + (70 * ratio) + 'px; text-indent: 1em; }';
          // 画像がセットされていない場合のスタイル
          html += '#sincloBox p#widgetTitle.noImage { padding-left: ' + (30 * ratio) + 'px; text-indent: 1em; }';
          html += '#sincloBox #mainImage em { top: -' + (10 * ratio) + 'px; right: -' + (10 * ratio) + 'px; width: ' + (25 * ratio) + 'px; height: ' + (20 * ratio) + 'px; font-size: ' + (11 * ratio) + 'px; padding: ' + (1 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk { padding: ' + (5 * ratio) + 'px; height: ' + (194 * ratio) + 'px; background-color: '+ colorList['chatTalkBackgroundColor'] +' }';
          html += '#sincloBox ul#chatTalk li { border-radius: ' + (5 * ratio) + 'px; margin: ' + (5 * ratio) + 'px 0; padding: ' + (5 * ratio) + 'px; font-size: ' + (12 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk li div.sendFileThumbnailArea { display: table-cell; width: ' + (64 * ratio) + 'px; height: ' + (64 * ratio) + 'px; border: 1px solid #D9D9D9; }';
          if(colorList['seBorderNone'] === 0){
            html += '#sincloBox ul#chatTalk li.sinclo_se { border: ' + (1 * ratio) + 'px solid '+ colorList['seBorderColor'] +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '#sincloBox ul#chatTalk li.sinclo_re { border: ' + (1 * ratio) + 'px solid '+ colorList['reBorderColor'] +'; }';
          }
          html += '#sincloBox ul#chatTalk li sinclo-radio { margin: 0.15em 0 -1em 0.5em; display: block; } ';
          if (widget.chatMessageDesignType === 2) {
            // 吹き出し型
            html += '#sincloBox ul#chatTalk li { line-height: 1.4; padding: ' + (5 * ratio) + 'px ' + (15 * ratio) + 'px !important; border-radius: ' + (12 * ratio) + 'px !important;}';
          }
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] { margin-right: 0.5em } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio * { webkit-transform: scale(' + (1 * ratio) + '); transform: scale(' + (1 * ratio) + '); moz-transform: scale(' + (1 * ratio) + '); } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label { display: inline; padding-left: 1.5em; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: '+ (1 * ratio) +'px; left: '+ (0 * ratio) +'px; width: '+ (11 * ratio) +'px; height: '+ (11 * ratio) +'px; border: 1px solid ' + chatContentTextColor + '; border-radius: 50%; background-color: #FFF; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: '+ (4 * ratio) +'px; left: '+ (3 * ratio) +'px; width: '+ (6 * ratio) +'px; height: '+ (6 * ratio) +'px; background: ' + colorList['mainColor'] + '; border-radius: 50%; } ';
          html += '#sincloBox ul#chatTalk li span.cName { font-size: ' + (13 * ratio) + 'px; margin: 0 0 ' + (5 * ratio) + 'px 0; }';
          if (widget.chatMessageDesignType === 2) {
            // 吹き出し型
            // html += '#sincloBox ul#chatTalk li.sinclo_se:before { bottom: 0.35em; left: calc(100% - 2px); }';
            // html += '#sincloBox ul#chatTalk li.sinclo_se:after { bottom: 0.4em; }';
            // html += '#sincloBox ul#chatTalk li.sinclo_re:before { bottom: 0.35em; left: -18px; }';
            // html += '#sincloBox ul#chatTalk li.sinclo_re:after { bottom: 0.4em; }';
          }
          if(colorList['seBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_se:after {border-left: 5px solid '+ colorList['seBorderColor'] +'; border-bottom: 5px solid '+ colorList['seBorderColor'] +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_re:after {border-right: 5px solid '+ colorList['reBorderColor'] +'; border-bottom: 5px solid '+ colorList['reBorderColor'] +'; }';
          }
          html += '#sincloBox ul#chatTalk sinclo-chat-receiver #receiveMessage { font-size: ' + (10 * ratio) + 'px; }';
          html += '#sincloBox section#chatTab sinclo-div { height: ' + (60*ratio) + 'px; padding: ' + (5 * ratio) + 'px; }';
          if(colorList['widgetInsideBorderNone'] === 0){
            html += '#sincloBox section#chatTab sinclo-div { border-top: ' + (1 * ratio) + 'px solid '+ colorList['widgetInsideBorderColor'] +'; }';
          }
          html += '#sincloBox section#chatTab #sincloChatMessage { font-size: 1em; padding: ' + (5 * ratio) + 'px;  }';
          if(colorList['messageBoxBorderNone'] === 0){
            html += '#sincloBox section#chatTab #sincloChatMessage { border-radius: ' + (5 * ratio) +'px 0 0 ' + (5 * ratio) +'px!important; border: ' + (1 * ratio) + 'px solid '+ colorList['messageBoxBorderColor'] +'!important; }';
          }
          else{
            html += '#sincloBox section#chatTab #sincloChatMessage { border: none!important; }';
          }

          html += '#sincloBox section#chatTab #sincloChatSendBtn { padding:  ' + (16.5 * ratio) + 'px 0; border: ' + ratio + 'px solid ' + colorList['chatSendBtnBackgroundColor'] + '; }';
          html += '#sincloBox section#chatTab sinclo-chat-alert { left: 10%; right: 10%; bottom: 50%; border-radius: ' + (5 * ratio) + 'px; color: #FFF; padding: ' + (10 * ratio) + 'px 0; }';
          html += '#sincloBox section#navigation { border-width: 0 ' + (1 * ratio) + 'px; height: ' + (40 * ratio) + 'px; }';
          html += '#sincloBox section#navigation ul { margin: 0 0 0 -' + (1 * ratio) + 'px; height: ' + (40 * ratio) + 'px; }';
          html += '#sincloBox section#navigation ul li { padding: ' + (10 * ratio) + 'px 0; border-left: ' + (1 * ratio) + 'px solid #E8E7E0; height: ' + (40 * ratio) + 'px;  }';
          html += '#sincloBox section#navigation ul li:last-child { border-right: ' + (1 * ratio) + 'px solid '+ colorList['widgetBorderColor'] +'; }';
          if(colorList['widgetBorderNone'] === 1){
            html += '#sincloBox section#navigation ul li:last-child { border-right:none; }';
          }
          html += '#sincloBox section#navigation ul li:not(.selected) { border-bottom: ' + (1 * ratio) + 'px solid '+ colorList['widgetInsideBorderColor'] +'; }';
          if(colorList['widgetInsideBorderNone'] === 1){
            html += '#sincloBox section#navigation ul li:not(.selected) { border-bottom: none!important;}';
          }
          html += '#sincloBox section#navigation ul li.selected::after { border-bottom: ' + (2 * ratio) + 'px solid ' + colorList['mainColor'] + '; left: ' + (5 * ratio) + 'px; }';
          html += '#sincloBox #fotter { padding: ' + (5 * ratio) + 'px 0; border: ' + (1 * ratio) + 'px solid '+ colorList['widgetBorderColor'] +'; font-size: ' + (11 * ratio) + 'px; border-top: none;}';
          if(colorList['widgetBorderNone'] === 1){
            html += '#sincloBox #fotter { border:none; }';
          }
          html += '#sincloBox section#navigation ul li::before { margin-right: ' + (5 * ratio) + 'px; width: ' + (18 * ratio) + 'px; height: ' + (18 * ratio) + 'px; }';
          //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
          if(Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1){
            html += '      #sincloBannerBox{ bottom:0px; right:0px; }';
            html += '      #sincloBanner.sincloBanner { height: '+ (42 * ratio) +'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
            html += '      #sincloBanner.sincloBanner .sinclo-comment{ font-size: '+ (17.5 * ratio) +'px; padding: 0 '+ (2 * ratio) +'px 0 '+ (10 * ratio) +'px; }';
            html += '      #sincloBanner.sincloBanner .sinclo-comment-notext{ font-size: ' + (17.5 * ratio) + 'px; padding: 0 '+ (2 * ratio) +'px 0 '+ (13 * ratio) +'px; }';
            html += '      #sincloBanner.sincloBanner .bannertext{ color: '+ widget.stringColor +'; font-size: '+ (12.5 * ratio) +'px; padding: 0 '+ (10 * ratio) +'px 0 '+ (3 * ratio) +'px; }';
            html += '      #sincloBanner.sincloBanner .notext{ padding: 0 ' + (7 * ratio) + 'px 0 ' + (3 * ratio) + 'px; cursor: pointer; }';
          }
        }
        /* 横の場合 */
        else {
          var chatAreaHeight = window.innerHeight * (document.body.clientWidth / window.innerWidth);
          var hRatio = chatAreaHeight * 0.07;
          html += '#sincloBox { left:0; right:0; bottom: 0; }';
          html += '#sincloWidgetBox { box-shadow: 0px 0px ' + widget.boxShadow + 'px ' + widget.boxShadow  + 'px rgba(0,0,0,0.1);}';
          html += '#sincloBox * { font-size: ' + hRatio + 'px }';
          if(widget.chatMessageCopy === 1) {
            html += '#sincloBox p#widgetTitle { border-radius: 0; border-top-width: 0.1em; height: 2em; padding: 0.35em 2em 0; font-size: 1.2em;  user-select: none; -moz-user-select: none; -webkit-user-select: none; -ms-user-select: none; }';
          }
          else {
            html += '#sincloBox p#widgetTitle { border-radius: 0; border-top-width: 0.1em; height: 2em; padding: 0.35em 2em 0; font-size: 1.2em;}';
          }
          if(widget.widgetSizeType !== 1){
            html += '#sincloBox p#widgetTitle { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }';
          }
          html += '#sincloBox section { width: 100% }';
          html += '#sincloBox section#chatTab ul { height: ' + (chatAreaHeight - (6.5 * hRatio)) + 'px }';
          html += '#sincloBox div#minimizeBtn { width: 1.5em; height: 1.5em; top: 0.4em; bottom: 0; right: 0.7em; }';
          //＋ボタンと×ボタンは閉じるボタン設定によってポジションが異なるため別々に記載。なお、IDは同一とする
          //閉じるボタン無効
          //＋ボタン無効に仕様変更
/*
          html += '#sincloBox div#addBtn { width: 1.5em; height: 1.5em; top: 0.4em; bottom: 0; right: 0.7em; }';
*/
/*
          html += '#sincloBox #widgetTitle:after { width: 1.5em; height: 1.5em; top: 0; bottom: 0; right: 0.25em;}';
*/
          html += '#sincloBox[data-openflg="true"] p#widgetTitle:after { margin-top: 0.5em; }';
          html += '#sincloBox #widgetTitle em { width: 2em; height: 2em; font-size: 0.8em; padding: 0.25em; border-radius: 5em; margin: 0.25em; }';
          html += '#sincloBox ul#chatTalk { padding: 0.3em; background-color: '+ colorList['chatTalkBackgroundColor'] +'}';
          html += '#sincloBox ul#chatTalk li { font-size: 0.8em; border-radius: 0.3em; margin: 0.3em 0; padding: 0.3em; }';
          html += '#sincloBox ul#chatTalk li div.sendFileThumbnailArea { display: table-cell; width: ' + (64 * ratio) + 'px; height: ' + (64 * ratio) + 'px; border: 1px solid #D9D9D9; }';
          if(colorList['seBorderNone'] === 0){
            html += '#sincloBox ul#chatTalk li.sinclo_se { border: ' + (1 * ratio) + 'px solid '+ colorList['seBorderColor'] +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '#sincloBox ul#chatTalk li.sinclo_re { border: ' + (1 * ratio) + 'px solid '+ colorList['reBorderColor'] +'; }';
          }
          html += '#sincloBox ul#chatTalk li sinclo-radio { margin: 0.15em 0 -1em 0.5em; display: inline-block; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] { margin-right: 0.5em } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"], #sincloBox ul#chatTalk li sinclo-radio label { webkit-transform: scale(1.3); transform: scale(1.3); moz-transform: scale(1.3); } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label { display: inline; padding-left: 1em; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: 2px; left: 0px; width: 19px; height: 19px; border: 1px solid ' + chatContentTextColor + '; border-radius: 50%; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: 7px; left: 5px; width: 11px; height: 11px; background: ' + colorList['mainColor'] + '; border-radius: 50%; } ';
          html += '#sincloBox ul#chatTalk li label, #sincloBox ul#chatTalk li span, #sincloBox ul#chatTalk li a { font-size: 1em; }';
          html += '#sincloBox ul#chatTalk li span.cName { margin: 0 0 0.3em 0 }';
          if (widget.chatMessageDesignType === 2) {
            // 吹き出し型
            // html += '#sincloBox ul#chatTalk li.sinclo_se:before { bottom: 0.35em; left: calc(100% - 3px); }';
            // html += '#sincloBox ul#chatTalk li.sinclo_se:after { bottom: 0.4em; }';
            // html += '#sincloBox ul#chatTalk li.sinclo_re:before { bottom: 0.35em; left: -18px; }';
            // html += '#sincloBox ul#chatTalk li.sinclo_re:after { bottom: 0.4em; }';
          }
          if(colorList['seBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_se:after {border-left: 5px solid '+ colorList['seBorderColor'] +'; border-bottom: 5px solid '+ colorList['seBorderColor'] +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_re:after {border-right: 5px solid '+ colorList['reBorderColor'] +'; border-bottom: 5px solid '+ colorList['reBorderColor'] +'; }';
          }
          html += '#sincloBox ul#chatTalk sinclo-chat-receiver #receiveMessage { font-size: 0.8em; }';
          html += '#sincloBox section#chatTab sinclo-div { height: 4em; padding: 0.5em; }';

          if ( hRatio > 16 ) {
            html += '#sincloBox #sincloChatMessage { height: 100%;  min-height: 100%!important; border-radius: 5px 0 0 5px!important; }';
          }
          else {
            html += '#sincloBox #sincloChatMessage { height: 100%;  min-height: 100%!important; border-radius: 5px 0 0 5px!important; font-size: 1.5em }';
          }
          html += '      #sincloBox section#chatTab #sincloChatSendBtn { padding: 0.6em 0; border: 1px solid ' + colorList['chatSendBtnBackgroundColor'] + '; }';
          html += '      #sincloBox section#chatTab sinclo-chat-alert { left: 10%; right: 10%; bottom: 50%; border-radius: 5px; color: #FFF; text-align: center; padding: 0.25em 0; }';
          if(colorList['messageBoxBorderNone'] === 0){
            html += '#sincloBox section#chatTab #sincloChatMessage { border-radius: ' + (5 * ratio) +'px 0 0 ' + (5 * ratio) +'px!important; border: ' + (1 * ratio) + 'px solid '+ colorList['messageBoxBorderColor'] +'!important; }';
          }
          else{
            html += '#sincloBox section#chatTab #sincloChatMessage { border: none!important; }';
          }
          html += '#sincloBox section#navigation ul { width: 100% }';
          html += 'sinclo span#mainImage, sinclo #widgetSubTitle, sinclo #widgetDescription, sinclo #navigation, sinclo #navigation * { display:none!important; height: 0!important }';
          html += '#sincloBox #fotter { display: none; height: 0!important }';
          //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
          if(Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1){
            var ratio = 1.9;
            html += '      #sincloBannerBox{ bottom:0px; right:0px; }';
            html += '      #sincloBanner.sincloBanner { height: '+ (42 * ratio) +'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
            html += '      #sincloBanner.sincloBanner .sinclo-comment{ font-size: '+ (17.5 * ratio) +'px; padding: 0 '+ (2 * ratio) +'px 0 '+ (10 * ratio) +'px; }';
            html += '      #sincloBanner.sincloBanner .sinclo-comment-notext{ font-size: ' + (17.5 * ratio) + 'px; padding: 0 '+ (2 * ratio) +'px 0 '+ (13 * ratio) +'px; }';
            html += '      #sincloBanner.sincloBanner .bannertext{ color: '+ widget.stringColor +'; font-size: '+ (12.5 * ratio) +'px; padding: 0 '+ (10 * ratio) +'px 0 '+ (3 * ratio) +'px; }';
            html += '      #sincloBanner.sincloBanner .notext{ padding: 0 ' + (7 * ratio) + 'px 0 ' + (3 * ratio) + 'px; cursor: pointer; }';
          }
        }
      }
      /* PC版 */
      else {
//        html += "      #sincloBox { width: " + widgetWidth + "px }";
        html += "      #sincloWidgetBox { width: " + sizeList['boxWidth'] + "px; box-shadow: 0px 0px " + widget.boxShadow + "px " + widget.boxShadow + "px rgba(0,0,0,0.1); border-radius: " + widget.radiusRatio + "px " + widget.radiusRatio + "px 0 0;}";
        html += '      #sincloBox * { line-height: 1.4; font-size: '+ sizeList['d12font'] +'px; }';
        html += '      #sincloBox sinclo-div#widgetHeader:after { top: 32px }';
//        html += "      #sincloBox section { width: " + widgetWidth + "px }";
//        html += "      #sincloBox section#navigation ul { width: " + widgetWidth + "px }";
        html += "      #sincloBox section { width: " + sizeList['boxWidth'] + "px }";
        html += "      #sincloBox section#navigation ul { width: " + sizeList['boxWidth'] + "px }";
        html += '      #sincloBox span#mainImage { top: 7px; left: 10px }';
        html += '      #sincloBox p#widgetTitle { border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px 0 0; border: 1px solid ' + colorList['mainColor'] + '; font-size: '+ sizeList['d14font'] +'px;padding: '+ sizeList['widgetTitlePadding'] +'px 30px; height: '+ sizeList['widgetTitleHeight'] +'px }';
        html += '      #sincloBox p#widgetTitle #sincloChatUnread { width: 25px; height: 25px; font-size: '+ sizeList['d13font'] +'px; border-radius: 15px; margin: 2.5px 6px; padding: 3px; }';
        html += '      #sincloBox p#widgetTitle:after { background-position-y: 3px; top: '+ sizeList['widgetTitleTop'] +'px; right: 10px; bottom: 6px; width: 20px; height: 20px; }';
        html += '      #sincloBox p#widgetSubTitle { background-color: '+ colorList['headerBackgroundColor'] +'; margin: 0; padding: 7px 0; text-align: left; border-width: 0 1px 0 1px; border-color: '+ colorList['widgetBorderColor'] +'; border-style: solid; padding-left: 77px; font-weight: bold; color: ' + colorList['subTitleTextColor'] + '; height: '+ sizeList['widgetSubTitleHeight'] +'px }';
        if(colorList['widgetBorderNone'] === 1){
          html += '#sincloBox p#widgetSubTitle { border:none; }';
        }
        html += '      #sincloBox p#widgetDescription { background-color: '+ colorList['headerBackgroundColor'] +'; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: '+ colorList['widgetBorderColor'] +'; border-style: solid; padding-left: 77px; height: '+ sizeList['widgetDescriptionHeight'] +'px; color: ' + colorList['descriptionTextColor'] + '; border-bottom-color:'+ colorList['widgetInsideBorderColor'] +';}';
        if(colorList['widgetBorderNone'] === 1){
          html += '#sincloBox p#widgetDescription { border-left:none; border-right:none;}';
        }
        html += '      #sincloBox section { background-color: #FFF; border: 1px solid '+ colorList['widgetBorderColor'] +'; border-top: none; border-bottom: 1px solid '+ colorList['widgetInsideBorderColor'] +'; }';
        if(colorList['widgetBorderNone'] === 1){
          html += '      #sincloBox section { border-top: none; border-left:none; border-right:none; }';
        }
        if(colorList['widgetInsideBorderNone'] === 1){
          html += '#sincloBox p#widgetDescription { border-bottom:none!important;}';
          html += '      #sincloBox section { border-bottom: none!important; }';
        }
        // 画像がセットされている場合のスタイル
        html += '      #sincloBox p#widgetTitle.notSimple { padding-left: 70px; }';
        // 画像がセットされていない場合のスタイル
        html += '      #sincloBox p#widgetTitle.noImage { padding-left: 30px; }';
        // チャットを使用する際
        if ( window.sincloInfo.contract.chat ) {
          html += '      #sincloBox #mainImage em { top: -10px; right: -10px; width: 25px; height: 20px; font-size: '+ sizeList['d11font'] +'px; padding: 1px; }';
          html += '      #sincloBox ul#chatTalk { height: '+ sizeList['chatTalkHeight'] +'px; padding: 5px; background-color: '+ colorList['chatTalkBackgroundColor'] +' }';
          html += '      #sincloBox ul#chatTalk li { border-radius: 5px; margin: 5px 0 0 0; padding: 8px; font-size: '+ sizeList['d12font'] +'px; }';
          if(colorList['seBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_se { border: 1px solid '+ colorList['seBorderColor'] +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_re { border: 1px solid '+ colorList['reBorderColor'] +'; }';
          }
          html += '      #sincloBox ul#chatTalk li span.cName { font-size: '+ sizeList['d13font'] +'px; margin: 0 0 5px 0 }';
          html += '      #sincloBox section#chatTab sinclo-div { height: '+ sizeList['classFlexBoxRowHeight'] +'px!important; padding: 5px }';
          html += '      #sincloBox section#chatTab sinclo-chat-alert { display: none; position: absolute; background-color: rgba(0,0,0,0.6); box-shadow: 0px 0px 4px 4px rgba(0,0,0,0.1); color: #FFF; text-align: center; cursor: pointer; }';
          html += '      #sincloBox section#chatTab sinclo-chat-alert { left: 25%; bottom: 50%; width: 50%; height:64px; border-radius: 5px; line-height: 47px; color: #FFF; padding: 10px 0; }';
          html += '      #sincloBox section#chatTab #sincloChatMessage { color: ' + chatContentTextColor + '!important; padding: 5px; height: 100%; min-height: 100%!important; }';
          if(colorList['messageBoxBorderNone'] === 0){
            html += '      #sincloBox section#chatTab #sincloChatMessage { border: 1px solid '+ colorList['messageBoxBorderColor'] +'!important; border-radius: 5px 0 0 5px!important; }';
          }
          else{
            html += '      #sincloBox section#chatTab #sincloChatMessage { border: none!important;}';
          }
          html += '      #sincloBox section#chatTab #sincloChatSendBtn { padding: 20px 0; height: 100%; border: 1px solid ' + colorList['chatSendBtnBackgroundColor'] + '; }';

        }
        // 画面同期を使用する際
        if ( window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document) ) {
          html += '      #sincloBox section#callTab { background-color: '+ colorList['chatTalkBackgroundColor'] +'!important; }';
          html += '      #sincloBox section#callTab #telNumber { overflow: hidden; color: ' + colorList['mainColor'] + '; font-weight: bold; margin: 0 auto; text-align: center; border: none!important; overflow: visible!important; }';
          html += '      #sincloBox section#callTab #telIcon { color: ' + colorList['mainColor'] + '; display: block; width: 50px; height: 50px; float: left; background-color: #3EA3DE; border-radius: 25px; padding: 3px }';
          html += '      #sincloBox section#callTab #telContent { display: block; overflow-y: auto; overflow-x: hidden; height:'+ sizeList['telContentHeight'] +'px; max-height: '+ sizeList['telContentHeight'] +'px }';
          if ( window.sincloInfo.contract.chat ) {
            html += '      #sincloBox section#callTab #telContent .tblBlock {  text-align: center;  margin: 0 auto;  width: 240px;  display: table; align-content: center;  height: 119px!important;  justify-content: center; }';
          }
          else {
            html += '      #sincloBox section#callTab #telContent .tblBlock { text-align: center; margin: 0 auto; width: 240px; display: table; align-content: center; justify-content: center; overflow-x: hidden; overflow-y: auto }';
          }
          html += '      #sincloBox section#callTab #telContent span { word-wrap: break-word ;word-break: break-all; font-size: '+ sizeList['d11font'] +'px; line-height: 1.5!important; color: #6B6B6B; white-space: pre-wrap; max-height: 119px; display: table-cell; vertical-align: middle; text-align: center }';
          if ( window.sincloInfo.contract.chat ) {
            html += '      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 20px auto 21px; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: '+ sizeList['d25font'] +'px; font-weight: bold; text-align: center; border-radius: 15px } ';
          } else {
            html += '      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: '+ sizeList['d25font'] +'px; font-weight: bold; text-align: center; border-radius: 15px } ';
          }
        }
        html += '      #sincloBox section#navigation { border-width: 0 1px; height: '+ sizeList['navigationHeight'] +'px; }';
        html += '      #sincloBox section#navigation ul { margin: 0 0 0 -1px; height: '+ sizeList['navigationHeight'] +'px;}';
        html += '      #sincloBox section#navigation ul li { width: 50%; padding: 10px 0; border-left: 1px solid '+ colorList['widgetBorderColor'] +'; height: '+ sizeList['navigationHeight'] +'px }';
        if(colorList['widgetBorderNone'] === 1){
          html += '      #sincloBox section#navigation ul li { border-left:none; }';
        }
        html += '      #sincloBox section#navigation ul li:last-child { border-right: 1px solid '+ colorList['widgetBorderColor'] +'; border-left: 1px solid '+ colorList['widgetInsideBorderColor'] +'; }';
        if(colorList['widgetBorderNone'] === 1){
          html += '      #sincloBox section#navigation ul li:last-child { border-right:none; }';
        }
        if(colorList['widgetInsideBorderNone'] === 1){
          html += '      #sincloBox section#navigation ul li:last-child { border-left: none!important; }';
        }
        html += '      #sincloBox section#navigation ul li:not(.selected) { border-bottom: 1px solid '+ colorList['widgetInsideBorderColor'] +' }';
        if(colorList['widgetInsideBorderNone'] === 1){
          html += '      #sincloBox section#navigation ul li:not(.selected) { border-bottom: none!important; }';
        }
        html += '      #sincloBox section#navigation ul li.selected::after{ border-bottom: 2px solid ' + colorList['mainColor'] + '; }';
        html += '      #sincloBox #fotter { height: '+ sizeList['fotterHeight'] +'px; padding: 5px 0; border: 1px solid '+ colorList['widgetBorderColor'] +'; font-size: '+ sizeList['d11font'] +'px; border-top: none; }';
        if(colorList['widgetBorderNone'] === 1){
          html += '      #sincloBox #fotter { border:none }';
        }
        html += '      #sincloBox section#navigation ul li::before{ margin-right: 5px; width: 18px; height: 18px; }';

      }


      html += '  </style>';

      return html;
    },
    //バナーを生成する関数
    sincloBannerTemplate: function(widget){
      var widgetWidth = $(window).width() - 20;
      var ratio = widgetWidth * (1/285);
      if ( check.smartphone() ) {
        if ( $(window).height() > $(window).width() ) {
          //縦
          var paddingpx = 'padding: '+ 10 * ratio +'px 0';
        }
        else{
          //横
          var paddingpx = 'padding: 20px 0';
        }
      }
      else{
        var paddingpx = 'padding: '+ 10 +'px 0';
      }
      var html = "";
      html += '  <div id="sincloBanner" class="sincloBanner" onclick="sinclo.operatorInfo.clickBanner()">';
      html += '    <div id="sincloBannerText" class="sincloBannerText" style="height: auto!important; width: auto!important; '+ paddingpx +'">';
      html += '      <i id="sinclo-comment" class="sinclo-fa fa-comment sinclo-comment' + (widget.bannertext.length !== 0 ? '' : '-notext') + '"></i><span class="' + (widget.bannertext.length !== 0 ? 'bannertext' : 'notext') + '">'+ check.escape_html(widget.bannertext) +'</span>';
      html += '    </div>';
      html += '  </div>';
      return html;
    },
    widgetHeaderTemplate: function(widget){
      var html = "", chatAndTitleOnly = false;
      // チャットとタイトルバーのみ表示するフラグ
//      if ( check.smartphone() && ( window.screen.availHeight < window.screen.availWidth || (widget.hasOwnProperty('spHeaderLightFlg') && Number(widget.spHeaderLightFlg) === 1) ) ) {
//        chatAndTitleOnly = true;
//      }
      // 画像
      if ( !chatAndTitleOnly && (Number(widget.showMainImage) === 1 || widget.mainImage !== "") ) {
        var ratio = 1;
        if ( check.smartphone() ) {
          ratio = ($(window).width() - 20) * (1/285);
        }
        html += '  <span id="mainImage" onclick="sinclo.operatorInfo.toggle()">';
        html += '    <img src="' + widget.mainImage + '" width="' + (ratio * 62) + '" height="' + (ratio * 70) + '" style="width:' + (ratio * 62) + 'px!important; height:' + (ratio * 70) + 'px!important;" alt="チャット画像">';
        html += '  </span>';
      }
      html += '  <sinclo-div id="widgetHeader" class="notSelect" onclick="sinclo.operatorInfo.toggle()">';
      // タイトル
      html += '    <p id="widgetTitle">' + check.escape_html(widget.title) + '</p>';
      //ボタン差し替え対応
      html += '    <div id="minimizeBtn"></div>';
//      html += '    <div id="addBtn"></div>';
      html += '    <div id="closeBtn" onclick="sinclo.operatorInfo.closeBtn()"></div>';
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

      html += '  </sinclo-div>';
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

      //サイズを取得
      var sizeList = this.getSizeType(widget.widgetSizeType);

      //カラーリストの取得
      var colorList = this.getColorList(widget);

      var html = "";

      // 電話・チャットプランの場合
      if ( window.sincloInfo.contract.chat && (window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document) ) && !check.smartphone() ) {
        html += '<section id="callTab">';
      }
      // 電話のみプランの場合
      else {
        html += '<section id="callTab" class="flexBox">';
      }

      html += '    <sinclo-div style="height: 50px;margin: 15px 25px">';
      // アイコン
      html += '    <span style="display: block; width: 50px; height: 50px; float: left; background-color: ' + colorList['mainColor'] + '; border-radius: 25px; padding: 3px;"><img width="19.5" height="33" src="' + window.sincloInfo.site.files + '/img/call.png" style="margin: 6px 12px"></span>';
      // 受付電話番号
      if ( Number(widget.display_time_flg) === 1 ) {
          html += '    <pre id="telNumber" style="font-size: '+ sizeList['d18font'] +'px; padding: 5px 0px 0px; height: 30px">' + check.escape_html(widget.tel) + '</pre>';
      }
      else {
          html += '    <pre id="telNumber" style="font-size: '+ sizeList['d20font'] +'px; padding: 10px 0px 0px; height: 45px;">' + check.escape_html(widget.tel) + '</pre>';
      }
      // 受付時間
      if ( Number(widget.display_time_flg) === 1 ) {
          html += '    <pre style="font-weight: bold; color: ' + colorList['mainColor'] + '; margin: 0 auto; white-space: pre-line; font-size: 11px; text-align: center; padding: 0 0 5px; height: 20px; border: none!important;  overflow: visible!important;">受付時間： ' + widget.time_text + '</pre>';
      }
      html += '    </sinclo-div>';
      // テキスト
      html += '    <sinclo-div id="telContent"><sinclo-div class="tblBlock"><span>' + check.escape_html(widget.content) + '</span></sinclo-div></sinclo-div>';
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
        iframe.src = sincloInfo.site.webcam_view + "?h=false&sincloData=" + encodeURIComponent(JSON.stringify(sincloData)); // FIXME
        document.body.appendChild(iframe);
    },
    chatWidgetTemplate: function(widget){
      var html = "", placeholder, spFlg = check.smartphone();
      // ボタンのみの場合
      if ( ( 'chatTrigger' in window.sincloInfo.widget && window.sincloInfo.widget.chatTrigger === 2) ) {
        placeholder = "メッセージを入力してください";
      }
      else {
        if ( spFlg ) { // スマートフォンの場合
          placeholder = "メッセージを入力してください（改行で送信）";
        }
        else {
          placeholder = "メッセージを入力してください&#13;&#10;（Shift+Enterで改行/Enterで送信）";
        }
      }
      html += '  <section id="chatTab" class="flexBox">';
      html += '    <ul id="chatTalk"><sinclo-chat></sinclo-chat><sinclo-typing></sinclo-typing><sinclo-chat-receiver><span id="receiveMessage">テストメッセージです</span></sinclo-chat-receiver></ul>';
      html += '    <sinclo-chat-alert>通信が切断されました。<br>こちらをタップすると再接続します。</sinclo-chat-alert>';
      html += '    <sinclo-div class="flexBoxRow" id = "flexBoxHeight">';
      html += '      <textarea name="sincloChat" id="sincloChatMessage" maxlength="300" placeholder=" ' + placeholder + ' "></textarea>';
      html += '      <a id="sincloChatSendBtn" class="notSelect" onclick="sinclo.chatApi.push()">送信</a>';
      html += '    </sinclo-div>';
      if(!check.smartphone() && (window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document))) {
        html += '    <span id="sincloAccessInfo">ウェブ接客コード：' + userInfo.accessId + '</span>';
      }
      html += '    <audio id="sinclo-sound">';
      html += '      <source src="' + window.sincloInfo.site.files + '/sounds/decision.mp3" type="audio/mp3">';
      html += '    </audio>';
      html += '  </section>';
      return html;
    },
    judgeShowWidget: function(){
      window.sincloInfo.widgetDisplay = null; // デフォルト表示しない
      // チャット契約、画面同期契約、画面同期中であれば表示
      if ( window.sincloInfo.contract.chat || window.sincloInfo.contract.synclo) {
        window.sincloInfo.widgetDisplay = true;
      }
      // ウィジェットを常に表示する
      if ( ('display_type' in window.sincloInfo.widget) && window.sincloInfo.widget.display_type === 1 ) {
        window.sincloInfo.widgetDisplay = true;
      }
      // オペレーターの数に応じて表示する
      else if ( ('display_type' in window.sincloInfo.widget) && window.sincloInfo.widget.display_type === 2 ) {
        if ( Number(window.sincloInfo.activeOperatorCnt) > 0 ) {
          window.sincloInfo.widgetDisplay = true;
        }
      }

      // 画面同期中は表示しない
      if ( check.isset(userInfo.connectToken) || check.isset(userInfo.coBrowseConnectToken) ) {
        window.sincloInfo.widgetDisplay = false;
      }

      // 同時対応上限数の設定があり、超えている場合
      if ( window.sincloInfo.hasOwnProperty('opFlg') && window.sincloInfo.opFlg === false ) {
        window.sincloInfo.widgetDisplay = false;
      }
      // 同期対象とするが、ウィジェットは表示しない
      if (check.isset(window.sincloInfo.dataset) && (check.isset(window.sincloInfo.dataset.hide) && window.sincloInfo.dataset.hide === "1")) {
        window.sincloInfo.widgetDisplay = false;
      }

      // ウィジェット表示タイミング
      var beforeJudgeDisplayFlg = window.sincloInfo.widgetDisplay;
      if(window.sincloInfo.widgetDisplay) {
        switch(window.sincloInfo.widget.showTiming) {
          case 1: //サイト訪問後
            window.sincloInfo.widgetDisplay = false;
            break;
          case 2: //ページ訪問後
            window.sincloInfo.widgetDisplay = false;
            break;
          case 3: //初回オートチャット受信時
            window.sincloInfo.widgetDisplay = false;
            break;
          case 4: //常に表示
            //上の表示条件処理に依存する
            break;
        }
      }

      if(beforeJudgeDisplayFlg && window.sincloInfo.widgetDisplay) {
        return window.sincloInfo.widgetDisplay;
      } else if (beforeJudgeDisplayFlg && !window.sincloInfo.widgetDisplay) {
        // 常に表示以外はウィジェットUI作成処理を継続させるためにtrueを返す
        // (window.sincloInfo.widgetDisplayのフラグは後続処理で利用する)
        return true;
      } else if (!beforeJudgeDisplayFlg) {
        // そもそも表示しない設定
        // FIXME 条件が冗長
        return window.sincloInfo.widgetDisplay;
      }
    },
    makeAccessIdTag: function(){
      if ( !check.browser() ) return false;
      if ( !('widget' in window.sincloInfo) ) return false;
      if (!this.judgeShowWidget()) {
        return false;
      }
      common.load.finish();
      var sincloBox = document.getElementById('sincloBox');
      // 非表示にされているだけであれば、再表示
      if ( sincloBox && sincloBox.style.display === "none" ) {
        common.widgetHandler.show();
        // sincloBox.parentNode.removeChild(sincloBox);
      }

      if ( userInfo.accessType !== cnst.access_type.host ) {
          var html = common.createWidget();
          $('body').append(html);
          emit('syncReady', {widget: window.sincloInfo.widgetDisplay});
          sincloBox = document.getElementById('sincloBox');
          sinclo.widget.condifiton.set(false, false);
          sinclo.operatorInfo.header = document.querySelector('#sincloBox #widgetHeader');

          $("#sincloBox .widgetCtrl").click(function(){
              var target = $("#sincloBox .widgetCtrl.selected"), clickTab = $(this).data('tab');
              target.removeClass("selected");
              $("#sincloBox").height("");
              $(this).addClass("selected");

              if ( clickTab === "call" ) {
                $("#sincloBox #chatTab").removeClass('flexBox');
                $("#sincloBox #callTab").addClass('flexBox');
              }
              else {
                $("#sincloBox #callTab").removeClass('flexBox');
                $("#sincloBox #chatTab").addClass('flexBox');
                sinclo.chatApi.showUnreadCnt();
                sinclo.chatApi.scDown();
              }
          });

          if ( window.sincloInfo.contract.chat && check.smartphone() ) {
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
            window.addEventListener('orientationchange', function(){
              //バナー表示だった
              var bannerAct = storage.s.get('bannerAct');
              if(bannerAct === "true"){
                //強制的にバナー表示とする
                $("#sincloBannerBox").hide();
//                $("#sincloBox").css("height","");
//                sinclo.operatorInfo.onBanner();
              }
              sinclo.operatorInfo.reCreateWidget();
            });
            // サイズが変わった時は、サイズ感を変える
            window.addEventListener('resize', function(e){
              if(e) e.stopPropagation();
              if ( $(window).height() > $(window).width() || document.activeElement.id === "sincloChatMessage") return false; // 横向きの場合のみ使用
               sinclo.operatorInfo.reCreateWidget();
            });
          }
      }
    },
    toRGBAcolor: function(colorCode, opacity) {
      if ( colorCode.indexOf("#") >= 0 ) {
        var code = colorCode.substr(1), r,g,b;
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
        colorCode = "rgba(" + parseInt(r,16) + ", " + parseInt(g,16) + ", " + parseInt(b,16) + ", " + opacity + ")";
      }
      return colorCode;
    },
    reconnectManual: function() {
      if(socket) {
        if(socket.disconnected) {
          socket.open();
          return true;
        } else {
          console.log("socket is NOT disconnected");
          return false;
        }
      } else {
        // socketオブジェクトが無いためページを再読込
        storage.s.set('chatAct', false);
        return location.href = location.href;
      }
    },
    widgetHandler: {
      // 通常呼び出し時はfalse or 引数指定なし（undefined）で呼び出す
      show: function(reCreateWidget) {
        /**
         * 表示条件（OR）
         * １：すでに表示されていた場合（common.widgetHandler.isShown()）
         * ２：すでに表示はされていないが、表示判定の結果、表示する場合（window.sincloInfo.widgetDisplay）
         * 表示条件（AND）
         * ３：sincloBoxの要素が存在する
         * ４：sincloBoxの要素のdisplayがnoneである
         */
        //画面遷移前に閉じるボタンが押下されていたか
        //バナー表示だった
        var bannerAct = storage.s.get('bannerAct');
        //非表示の状態だった
        var closeAct = storage.s.get('closeAct');
        if(bannerAct === "true"){
          //強制的にバナー表示とする
          $("#sincloBox").css("height","");
          sinclo.operatorInfo.onBanner();
          //バナー表示フラグ設定をクリア
//          storage.s.unset("bannerAct");
        }
        if(closeAct === "true"){
          //強制的に非表示とする
          //チャットを閉じる
          $("#sincloWidgetBox").hide();
          //非表示フラグ設定をクリア
//          storage.s.unset("closeAct");
        }
        if((common.widgetHandler.isShown() || window.sincloInfo.widgetDisplay)
          && sincloBox && (sincloBox.style.display === 'none' || sincloBox.style.display === '')) {
          console.log('でろでろでろでろでろでろ');
          sincloBox.style.display = "block";
          //一旦非表示
          //ヘッダ非表示（シンプル表示）
          common.abridgementTypehide();
          common.widgetHandler.saveShownFlg();
          var dataOpenflg = sinclo.widget.condifiton.get();
          //最小化時と最大化時の状態を取得
          var abridgementType = common.getAbridgementType();
          //ウィジェットの再生成処理呼び出しでなければ最小化表示設定で呼び出す
          if(!reCreateWidget && dataOpenflg === "false") {
            sinclo.widget.condifiton.set(false, true);
            //ログ書き込み用にメッセージ送信
            emit("sendWidgetShown",{widget:true});
            //最小化
            if(abridgementType['MinRes']){
              //ヘッダ非表示（シンプル表示）
              common.abridgementTypehide();
            }
            else{
              //ヘッダ表示（通常表示）
              common.abridgementTypeShow();
            }
            if(bannerAct !== "true") {
              sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
            }
            //最小化時ボタン表示
            common.whenMinimizedBtnShow();
            // このタイミングでの最大化実行条件
            // １：PCの場合、ウィジェット最大化処理がウィジェット非表示時に実行されていた場合
            // ２：スマホの場合、ウィジェット最大化する設定が有効で、ウィジェット最大化処理がウィジェット非表示時に実行されていた場合
            if((!(check.smartphone() && sincloInfo.widget.hasOwnProperty('spAutoOpenFlg') && Number(sincloInfo.widget.spAutoOpenFlg) === 1) && storage.s.get('preWidgetOpened') === "true")
               || (!check.smartphone() && storage.s.get('preWidgetOpened') === "true")) {
              //すでに最大化処理が呼び出されていたら最大化表示する
              sinclo.operatorInfo.ev();
              //最大化
              if(abridgementType['MaxRes']){
                //ヘッダ非表示（シンプル表示）
                common.abridgementTypehide();
              }
              else{
                //ヘッダ表示（通常表示）
                common.abridgementTypeShow();
              }
              //最大化時ボタン表示
              common.whenMaximizedBtnShow();
            }
          }
          else{
            if(dataOpenflg === "false"){
              console.log("saisyouka");
              //最小化
              if(abridgementType['MinRes']){
                //ヘッダ非表示（シンプル表示）
                common.abridgementTypehide();
              }
              else{
                //ヘッダ表示（通常表示）
                common.abridgementTypeShow();
              }
              //最小化時ボタン表示
              common.whenMinimizedBtnShow();
            }
            else{
              console.log("saidaika");
              //最大化
              if(abridgementType['MaxRes']){
                //ヘッダ非表示（シンプル表示）
                common.abridgementTypehide();
              }
              else{
                //ヘッダ表示（通常表示）
                common.abridgementTypeShow();
              }
              //最大化時ボタン表示
              common.whenMaximizedBtnShow();
            }
          }
        }
      },
      hide: function() {
        sincloBox.style.display = "none";
      },
      saveShownFlg: function() {
        storage.s.set("widgetShown", "true");
        storage.s.set("widgetShowTimingType", window.sincloInfo.widget.showTiming);
      },
      clearShownFlg: function() {
        storage.s.unset("widgetShown");
        storage.s.unset("widgetShowTimingType");
      },
      isShown: function() {
        return storage.s.get("widgetShown") === "true";
      },
      //サイト/ページ訪問時の設定
      getRemainingTimeMsec: function() {
        var remainingTime = 0;
        switch(window.sincloInfo.widget.showTiming) {
          case 1: //サイト
            remainingTime = this._calcRemainingShowTimingSiteTimeMsec();
            break;
          case 2: //ページ
            remainingTime = window.sincloInfo.widget.maxShowTimingPage * 1000;
            break;
        }
        return remainingTime;
      },
      _calcRemainingShowTimingSiteTimeMsec: function() {
        var siteAccessTimeMsec = (Number(userInfo.pageTime) - Number(userInfo.time)),
            showIntervalMsec = window.sincloInfo.widget.maxShowTimingSite * 1000;
        console.log("siteAccessTimeMsec " + siteAccessTimeMsec);
        console.log("showIntervalMsec" + showIntervalMsec);
        console.log("_calcRemainingShowTimingSiteTimeMsec: " + (siteAccessTimeMsec <= showIntervalMsec ? showIntervalMsec - siteAccessTimeMsec : 0));
        return siteAccessTimeMsec <= showIntervalMsec ? showIntervalMsec - siteAccessTimeMsec : 0;
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
    },
    c: {
      prefix: '___',
      get: function(name){
        var cookies = document.cookie;
        var cookieItem = cookies.split(";");
        var cookieValue = "";

        for (var i = 0; i < cookieItem.length; i++) {
          var elem = cookieItem[i].split("=");
          if (elem[0].trim() === this.prefix + name) {
            cookieValue = decodeURIComponent(elem[1]);
          } else {
            continue;
          }
        }
        return cookieValue;
      },
      set: function(name, val){
        document.cookie = this.prefix + name + '=' + encodeURIComponent(val) + '; path=/';
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
      if ( !window.sincloInfo.contract.chat && this.smartphone() ) {
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
      if ( a === null || a === '' || a === undefined || String(a) === "null" || String(a) === "undefined" ) {
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
      var str = string.replace(/(<br>|<br \/>)/gi, '\n');
      str = str.replace(/[&'`"<>]/g, function(match) {
        return {
          '&': '&amp;',
          "'": '&#x27;',
          '`': '&#x60;',
          '"': '&quot;',
          '<': '&lt;',
          '>': '&gt;',
        }[match];
      });
      return str;
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
    gFrame: false, // 外部接続
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
      else {
        // 消費者がフレームの場合
        if ( common.tmpParams.hasOwnProperty('gFrame') && !check.isset(storage.s.get('gFrame')) ) {
          var gFrameCode = userInfo.getCode(cnst.info_type.gFrame);
          storage.s.set(gFrameCode, common.tmpParams.gFrame);
          var connectCode = userInfo.getCode(cnst.info_type.connect);
          storage.s.set(connectCode, common.tmpParams.connectToken);
          var tabIdCode = userInfo.getCode(cnst.info_type.tab);
          storage.s.set(tabIdCode, common.tmpParams.tabId);
          var parentIdCode = userInfo.getCode(cnst.info_type.parentId);
          storage.s.set(parentIdCode, common.tmpParams.parentId);
        }
        if ( check.isset(storage.s.get('gFrame')) && check.isset(storage.s.get('parentId')) ) {
          userInfo.gFrame = storage.s.get('gFrame');
          userInfo.tabId = storage.s.get('tabId');
          userInfo.connectToken = storage.s.get('connectToken');
          userInfo.sendTabId = storage.s.get('sendTabId');
          userInfo.parentId = storage.s.get('parentId');
          emit('startSyncToFrame', {
            parentId: userInfo.parentId,
            tabId: userInfo.tabId
          });
        }
        if ( check.isset(storage.s.get('coBrowseConnectToken')) ) {
          userInfo.coBrowseConnectToken = storage.s.get('coBrowseConnectToken');
        }
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
        delete userInfo.sendTabId;
        // TODO minify
        userInfo.unsetConnect();
      }
    },
    getCode: function(type){
      switch(type) {
        case cnst.info_type.user:
          return "userId";
        case cnst.info_type.access:
          return "accessId";
        case cnst.info_type.ip:
          return "ipAddress";
        case cnst.info_type.time:
          return "time";
        case cnst.info_type.referrer:
          return "referrer";
        case cnst.info_type.connect:
          return "connectToken";
        case cnst.info_type.tab:
          return "tabId";
        case cnst.info_type.prev:
          return "prev";
        case cnst.info_type.staycount:
          return "stayCount";
        case cnst.info_type.gFrame:
          return "gFrame";
        case cnst.info_type.sendTabId:
          return "sendTabId";
        case cnst.info_type.parentId:
          return "parentId";
        case cnst.info_type.sincloSessionId:
          return "sincloSessionId";
      }
    },
    set: function(type, val, session){
      var code = this.getCode(type);
      if ( typeof(session) === 'undefined' ) {
        storage.l.set(code, val);
      } else if ( session === 'sincloSessionId' ) {
        console.log('sincloSessionId is set : ' + val);
        storage.c.set(code, val);
      } else {
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
      } else if (check.isset(storage.c.get(code))) {
        return storage.c.get(code);
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
        } else if (check.isset(storage.c.get(code))) {
          userInfo[code] = storage.c.get(code);
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
        cnst.info_type.gFrame,
        cnst.info_type.parentId
      ];
      for ( var i in array ) { userInfo.unset(array[i]); }
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
    setPrevpage: function(reset){
      var code = this.getCode(cnst.info_type.prev);
      userInfo.prev = common.jParse(storage.s.get(code));
      if ( !check.isset(userInfo.prev) || reset ) {
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
    writePrevToLocalStorage: function(){
      var code = this.getCode(cnst.info_type.prev);
      var prev = [];
      if(typeof(userInfo.oldSincloSessionId) !== 'undefined') {
        console.log("oldSincloSessionId is found. : " + userInfo.oldSincloSessionId + " overwrite.");
        prev = common.jParse(storage.s.get(code));
        storage.l.set(code, JSON.stringify(prev));
      } else {
        prev = common.jParse(storage.l.get(code));
        if (!check.isset(prev)) {
          prev = [];
        }
        // IE8対応コード
        if (prev.length === 0 || location.href !== prev[prev.length - 1].url) {
          prev.push({url: location.href, title: common.title()});
          storage.l.set(code, JSON.stringify(prev));
        }
      }
      return prev;
    },
    setConnect: function(val){
      this.set(cnst.info_type.connect, val, true);
    },
    setTabId: function(){
      var val = userInfo.userId + "_" + common.makeToken();
      this.set(cnst.info_type.tab, val, true);
    },
    changeTabId: function(tabId){
      console.log("CHANGE TAB ID");
      this.set(cnst.info_type.tab, tabId, true);
    },
    unsetAccessId: function(){
      return this.unset(cnst.info_type.access);
    },
    unsetConnect: function(){
      return this.unset(cnst.info_type.connect);
    },
    getSendList: function() {
      var code = this.getCode(cnst.info_type.prev);
      var prev = common.jParse(storage.l.get(code));
      return {
        ipAddress: this.getIp(),
        time: this.getTime(),
        prev: prev,
        stayCount: this.getStayCount(),
        referrer: this.referrer,
        userAgent: window.navigator.userAgent,
        chatCnt: document.getElementsByClassName('sinclo_se').length,
        chatUnread: {id: null, cnt: 0},
        service: check.browser(),
        widget: window.sincloInfo.widgetDisplay
      };
    }
  };

  browserInfo = {
    connectFlg: false,
    referrer: "",
    href: location.href,
    prevList: [],
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
        };
      }
    },
    windowSize : function(){
      return {
        height: window.innerHeight,
        width: window.innerWidth
      };
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
      var tabFlg = document.hasFocus(), widgetFlg = false, tabStatus, sincloBox;
      //バナー表示かどうか　bannerAct === "true"だったらバナー表示
      var bannerAct = storage.s.get('bannerAct');
      //閉じるボタンによる非表示の状態かどうか　closeAct === "true"だったら閉じるボタンによる非表示状態
      var closeAct = storage.s.get('closeAct');
      if ( document.getElementById('sincloBox') ) {
        sincloBox = document.getElementById('sincloBox');
        var tmp = sinclo.widget.condifiton.get();
        if ( String(tmp) === "true" ) {
          widgetFlg = true;
        }
      }
      // タブがアクティブ
      if ( tabFlg ) {
        sinclo.chatApi.clearInactiveTimeout();
        // ウィジェットが開いている
        if ( widgetFlg ) {
          tabStatus = cnst.tab_type.open;
        }
        else {
          tabStatus = cnst.tab_type.close;
        }
        // ウィジェット非表示中
        if ( !sincloBox || ( sincloBox && sincloBox.style.display !== "block" ) ) {
          tabStatus = cnst.tab_type.none;
        }
        //バナー表示中
        if(bannerAct === "true"){
          tabStatus = cnst.tab_type.none;
        }
        //閉じるボタンによる非表示中
        if(closeAct === "true"){
          tabStatus = cnst.tab_type.none;
        }
      }
      else {
        tabStatus = cnst.tab_type.disable;
        sinclo.chatApi.startInactiveTimeout();
      }
      return tabStatus;
    }
  };

  syncEvent = {
    resizeTimer: false,
    evList: [
      {
        type: "mousemove",
        timer : null,
        ev: function(e){
          if(e) e.stopPropagation();
          if ( this.timer ) {
            return false;
          }
          this.timer = setTimeout(function(){
            this.timer = null;
            emit('syncBrowserInfoFrame', {
              accessType: userInfo.accessType,
              mousePoint: {x: e.clientX, y: e.clientY}
            });
          }, 10);
        }
      },
      {
        type: "scroll",
        ev: function(e){
          if(e) e.stopPropagation();
          if ( socket === undefined ) return false;
          if ( "body" === syncEvent.receiveEvInfo.nodeName && "scroll" === syncEvent.receiveEvInfo.type ) return false;
          // スクロール用
          emit('syncScrollInfo', {
            accessType: userInfo.accessType,
            mousePoint: {x: e.clientX, y: e.clientY},
            scrollPosition: browserInfo.windowScroll()
          });
        }
      },
      {
        type: "hashchange",
        ev: function(e){
          if(e) e.stopPropagation();
          if ( socket === undefined ) return false;
          browserInfo.href = location.href;
          emit('reqUrlChecker', {});
        }
      }
    ],
    pcResize: function(e){
      if(e) e.stopPropagation();
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
      if(e) e.stopPropagation();
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

      var els;
      // 要素に対してのイベント操作
      els = document.getElementsByTagName('input');
      this.ctrlElmEventListener(eventFlg, els, "focus", syncEvent.focusCall);
        // checkbox, radioボタンのイベント操作
        this.ctrlElmEventListener(eventFlg, els, "change", syncEvent.changeCall);
      els = document.getElementsByTagName('textarea');
      this.ctrlElmEventListener(eventFlg, els, "focus", syncEvent.focusCall);

      var $textarea = document.getElementsByTagName("textarea")[0];
      if ( $textarea !== undefined ) {
        var bHeight, bWidth; // ここが要素ごとになるように・・・
        $textarea.addEventListener('mousemove', function(e){
          if(e) e.stopPropagation();
            if ( bHeight && bWidth && ( bHeight !== this.style.height || bWidth !== this.style.width)) {
          }
            bHeight = this.style.height;
            bWidth = this.style.width;
        }, false);
      }

      // プルダウンに対してのイベント操作
      els = document.getElementsByTagName("select");
      this.ctrlElmEventListener(eventFlg, els, "change", syncEvent.changeCall);

      // 要素スクロール
      var scEls = [], cHeight, sHeight, i;
      els = document.getElementsByTagName("ul");
      for ( i in els ) {
        cHeight = els[i].clientHeight;
        sHeight = els[i].scrollHeight;
        if ( (sHeight-cHeight)>0 ) {
          scEls.push(els[i]);
        }
      }
      els = document.getElementsByTagName("textarea");
      for ( i in els ) {
        cHeight = els[i].clientHeight;
        sHeight = els[i].scrollHeight;
        if ( (sHeight-cHeight)>0 ) {
          scEls.push(els[i]);
        }
      }
      els = document.getElementsByTagName("div");
      for ( i in els ) {
        cHeight = els[i].clientHeight;
        sHeight = els[i].scrollHeight;
        if ( (sHeight-cHeight)>0 ) {
          scEls.push(els[i]);
        }
      }
      els = document.getElementsByTagName("dl");
      for ( i in els ) {
        cHeight = els[i].clientHeight;
        sHeight = els[i].scrollHeight;
        if ( (sHeight-cHeight)>0 ) {
          scEls.push(els[i]);
        }
      }
      this.ctrlElmEventListener(eventFlg, scEls, "scroll", syncEvent.elmScrollCall);
      if ( ('form' in window.sincloInfo.dataset ) && window.sincloInfo.dataset.form ) {
        // フォーム制御
        $(document).submit(function(e){
          if ( userInfo.accessType !== cnst.access_type.host ) {
            emit('requestSyncStopForSubmit', {message: "お客様がsubmitボタンをクリックしましたので、\n画面共有を終了します。"});
          }
          else {
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
        css += '        display: -ms-flexbox; display: -webkit-flex; display: flex;';
        css += '        min-height: calc(60px + 2em);';
        css += '    }';
        css += '    sinclo-div#sincloPopMain sinclo-div {';
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
          var html = '';
          html += this.getCss();
          html += '  <sinclo-div id="sincloPopupFrame">';
          html += '    <sinclo-div id="sincloPopBar">';
          html += '    </sinclo-div>';
          html += '    <sinclo-div id="sincloPopMain">';
          html += '      <sinclo-div id="sincloLogo"><img src="' + sincloInfo.site.files + '/img/mark.png" width="60" height="60"></sinclo-div>';
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
      no: function(){ this.remove(); }
  };

  vcPopup = {
      dragging: false,
      set: function(fromID, toID){
          vcPopup.remove();
          var html = '';
          var sincloData = {
            from: fromID,
            to: toID,
          };
          var url = sincloInfo.site.webcam_view + "?h=false&sincloData=" + encodeURIComponent(JSON.stringify(sincloData));
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
          html += '      <sinclo-div id="sincloVcLogo"><img src="' + sincloInfo.site.files + '/img/mark.png" width="18" height="18"></sinclo-div>';
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
      no: function(){ this.remove(); },
      // ドラッグ用プロパティ・メソッド群
      startDragX: 0,
      startDragY: 0,
      dragOn: function(e) {
        if(e) e.stopPropagation();
        vcPopup.dragging = true;
        vcPopup.startDragX = e.screenX;
        vcPopup.startDragY = e.screenY;
      },
      dragOff: function(e) {
        if(e) e.stopPropagation();
        vcPopup.dragging = false;
      },
      drag: function(e) {
        if(e) e.stopPropagation();
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
    var tabStateTimer = null;
    // ウィジェット最大化設定をクリア
    storage.s.unset("preWidgetOpened");
    if(window.sincloInfo.widget.showTiming !== Number(storage.s.get("widgetShowTimingType"))) {
      // SessionStorageで保存している表示タイミング設定と違う場合はクリアする
      console.log("Storage widgetShowTimingType is different. clearing... before: " + Number(storage.s.get("widgetShowTimingType")) + " after: " + window.sincloInfo.widget.showTiming);
      common.widgetHandler.clearShownFlg();
    }
    if(window.sincloInfo.widget.showTiming === 2) {
      // 表示タイミングがページごとの場合は初期処理でフラグをクリアする
      console.log("Widget Show flg clear.");
      common.widgetHandler.clearShownFlg();
    }
    socket = io.connect(sincloInfo.site.socket, {port: 9090, rememberTransport : false});

    // 接続時
    socket.on("connect", function(){
      // ウィジェットがある状態での再接続があった場合
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox && userInfo.accessType === Number(cnst.access_type.guest) ) {
        sinclo.trigger.flg = true;
        var emitData = userInfo.getSendList();
        emitData.widget = window.sincloInfo.widgetDisplay;
        emit('customerInfo', emitData);
        emit('connectSuccess', {confirm: false, reconnect: true, widget: window.sincloInfo.widgetDisplay});
        common.widgetHandler.show();
      }
      else {
        sinclo.trigger.flg = false;
        sinclo.connect();
      }

      if ( userInfo.accessType === Number(cnst.access_type.host) || String(userInfo.gFrame) === "true") return false;
      // 定期的にタブのアクティブ状態を送る
      var tabState = browserInfo.getActiveWindow();
      if ( tabStateTimer ) { clearInterval(tabStateTimer); }
      tabStateTimer = setInterval(function(){
        var newState = browserInfo.getActiveWindow();
        if ( document.getElementById('sincloBox') !== null && tabState !== newState ) {
          tabState = newState;
          emit('sendTabInfo', { status: tabState, widget: window.sincloInfo.widgetDisplay });
        }
      }, 700);
    }); // socket-on: connect

    socket.on("changeTabId", function(d){
      var obj = common.jParse(d);
      userInfo.tabId = obj.newTabId;
      userInfo.changeTabId(obj.newTabId);
    });

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("retConnectedForSync", function(d){
      sinclo.retConnectedForSync(d);
    }); // socket-on: retConnectedForSync

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("accessInfo", function(d){
      sinclo.accessInfo(d);
    }); // socket-on: accessInfo

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("syncUserInfo", function(d){
      sinclo.syncUserInfo(d);
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

    // 画面共有
    socket.on('getWindowInfo', function(d){
      var obj = common.jParse(d);
      sinclo.getWindowInfo(obj);
    }); // socket-on: getWindowInfo

    // 画面共有(LiveAssist)
    socket.on('startCoBrowseOpen', function(d){
      var obj = common.jParse(d);
      sinclo.startCoBrowseOpen(obj);
    }); // socket-on: getWindowInfo

    // 画面共有準備完了
    socket.on('assistAgentIsReady', function(d){
      var obj = common.jParse(d);
      sinclo.assistAgentIsReady(obj);
    });

    // 画面共有(iframeバージョン)
    socket.on('startWindowSync', function(d){
      var obj = common.jParse(d);
      sinclo.startWindowSync(obj);
    }); // socket-on: startWindowSync

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

    // ブラウザ「次へ」「前へ」の操作
    socket.on('syncBrowserCtrl', function (d) {
      sinclo.syncBrowserCtrl(d);
    });

    // 継続接続
    socket.on('syncContinue', function (d) {
      sinclo.syncContinue(d);
    });

    socket.on('setInitInfo', function (d) {
      sinclo.setInitInfo(d);
    }); // socket-on: setInitInfo

    // 同期確認
    socket.on('resUrlChecker', function (d) {
      sinclo.resUrlChecker(d);
    }); // socket-on: resUrlChecker

    // 別タブ同期
    socket.on('receiveOtherTabURL', function (d) {
      window.focus();
      sinclo.resUrlChecker(d);
    }); // socket-on: receiveOtherTabURL

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

    // 既読操作後のレスポンス
    socket.on('retReadFromCustomer', function (d) {
      sinclo.chatApi.retReadFromCustomer(d);
    });

    // 画面共有
    socket.on('confirmVideochatStart', function(d){
      var obj = common.jParse(d);
      sinclo.confirmVideochatStart(obj);
    }); // socket-on: confirmVideochatStart

    // 資料共有依頼受信
    socket.on('docShareConnect', function(d){
      var obj = common.jParse(d);
      sinclo.docShareConnect(obj);
    }); // socket-on: confirmVideochatStart

    // 資料共有終了通知
    socket.on('docDisconnect', function(){
      sinclo.docDisconnect();
    }); // socket-on: confirmVideochatStart

    socket.on('syncStop', function(d){
      sinclo.syncStop(d);
    }); // socket-on: syncStop

    socket.on('stopCoBrowse', function(d){
      sinclo.stopCoBrowse(d);
    }); // socket-on: stopCoBrowse

    socket.on('sincloReconnect', function(d){ // socket再接続
      socket.disconnect();
      socket.connect();
    }); // socket-on: sincloReconnect

    socket.on('disconnect', function(data) {
      if(!sinclo.chatApi.inactiveCloseFlg) {
        var sincloBox = document.getElementById('sincloBox');
        if ( sincloBox ) {
          //sincloBox.parentNode.removeChild(sincloBox);
          common.widgetHandler.hide();
        }
      }
      popup.remove();
    });
  };

  $.ajaxSetup({
    cache: false
  });

  if ( check.isset(storage.s.get('params')) ) {
    common.params = common.jParse(storage.s.get('params'));
    userInfo.accessType = common.params.type;
  }
  else {
    common.getParams();
    if ( Number(common.tmpParams.type) === Number(cnst.access_type.host) ) {
      userInfo.accessType = cnst.access_type.host;
    }
  }

  $.ajax({
      type: 'get',
      url: window.sincloInfo.site.files + "/settings/",
      cache: false,
      data: {
          'sitekey': window.sincloInfo.site.key,
          accessType: userInfo.accessType
      },
      dataType: "json",
      success: function(json){
        if ( String(json.status) === "true" ) {
          if ( check.smartphone() && json.widget.hasOwnProperty('spShowFlg') && Number(json.widget.spShowFlg) === 2 ) {
            clearTimeout(timer);
            return false;
          }
          window.sincloInfo.widget = json.widget;
          window.sincloInfo.messages = json.messages;
          window.sincloInfo.contract = json.contract;
        }
        else {
          clearTimeout(timer);
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
          $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
          $("#textStatus").html("textStatus : " + textStatus);
          $("#errorThrown").html("errorThrown : " + errorThrown.message);
      }
  });

  var timer = window.setInterval(function(){
    if ( io !== "" && sinclo !== "" && window.sincloInfo.contract !== undefined ) {
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
  data.siteKey = sincloInfo.site.key; // サイトの識別キー
  if ( check.isset(userInfo.sendTabId) ) {
    data.to = userInfo.sendTabId; // 送信先ID
  }
  /* ここまで：イベント名指定なし */
  /* ここから：イベント名指定あり */
  if (evName === "customerInfo" || evName === "sendAccessInfo") {
    data.accessId = userInfo.accessId;
    data.userId = userInfo.userId;
    data.status = browserInfo.getActiveWindow();
    data.sincloSessionId = userInfo.sincloSessionId;
  }
  if (evName === "connected" || evName === "getChatMessage") {
    data.token = common.token;
  }
  if (evName === "connectSuccess") {
    data.widget = window.sincloInfo.widgetDisplay;
    data.accessId = userInfo.accessId;
  }
  if (evName === "customerInfo" || evName === "sendAccessInfo") {
    data.contract = window.sincloInfo.contract;
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
  if (   evName === "connectSuccess" || evName === "sendWindowInfo" || evName === "sendAutoChatMessages" ||
         evName === "getChatMessage" || evName === "sendChat" || evName === "sendAutoChatMessage"
  ) {
    data.chat = null;
  }
  if (   evName === "syncBrowserInfo" || evName === "syncChangeEv" || evName === "requestSyncStop" ||
         evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendConfirmConnect"
  ) {
    data.accessType = userInfo.accessType;
  }
  if (   evName === "syncReady" || evName === "connectSuccess" || evName === "reqUrlChecker"  || evName === "customerInfo" ||
         evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendAccessInfo" || evName === "sendWindowInfo"
  ) {
    data.url= f_url(browserInfo.href);
  }
  // connectToken
  if (   evName === "syncReady" || evName === "connectSuccess" || evName === "requestSyncStop"  || evName === "customerInfo" || evName === "sendTabInfo" ||
         evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendAccessInfo" || evName === "sendConfirmConnect"
  ) {
    data.connectToken = userInfo.get(cnst.info_type.connect);
  }
  if ( evName == "sendWindowInfo" || evName == "requestSyncStopForSubmit"  || evName == "startSyncToFrame" ) {
    data.connectToken = userInfo.connectToken;
  }
  if ( evName == "requestSyncStop" && userInfo.accessType === cnst.access_type.host ) {
    data.type = 3;
  }
  if ( evName == "requestSyncStop" && userInfo.accessType === cnst.access_type.guest ) {
    data.type = 4;
  }
  if ( evName === "requestSyncStopForSubmit" ) { // ページ離脱直後に送りたいメッセージ
    data.tabId = userInfo.tabId; // タブの識別ID
    socket.emit(evName, JSON.stringify(data)); return false;
  }
  /* ここまで：イベント名指定あり */
  var timer = setInterval(function(){
    if ( userInfo.tabId !== "" ) {
      clearInterval(timer);
      data.tabId = userInfo.tabId; // タブの識別ID
      data.sincloSessionId = userInfo.sincloSessionId;
      console.log("EMIT : " + evName + "data : " + JSON.stringify(data));
      socket.emit(evName, JSON.stringify(data));
    }
  }, 100);
}

function now(){
  var d = new Date();
  return "【" + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() + "】";
}

// get type
var myTag = document.querySelector("script[src$='/client/" + sincloInfo.site.key + ".js']");
if (myTag.getAttribute('data-hide')) {
    sincloInfo.dataset.hide = myTag.getAttribute('data-hide');
}
if (myTag.getAttribute('data-form')) {
    sincloInfo.dataset.form = myTag.getAttribute('data-form');
}
