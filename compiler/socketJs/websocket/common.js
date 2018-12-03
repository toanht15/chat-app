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
  uploadFileSelectorModal, // ファイル受信用
  sinclo, // リアルタイム通信補助関数
  sincloVideo; // ビデオ通信補助関数

(function (jquery) {
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
      none: 3,
      disable: 4
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
    sync_type: {inner: 1, outer: 2}
  };

  common = {
    n: 20,
    str: "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890",
    token: null,
    cursorTag: null,
    params: {},
    tmpParams: {},
    vcInfo: {}, // ビデオチャット用のセッション情報
    getParams: function () {
      // パラメータの取得
      var params = location.href.split('?'), param, i, kv;
      if ( params[1] !== undefined && params[1].match(/sincloData/) ) {
        param = params[1].split('sincloData=');
        if ( param[1] ) {
          this.tmpParams = JSON.parse(decodeURIComponent(param[1]));
        }
      }
    },
    fullDateTime: function (parse) {
      function _numPad(str) {
        return ("0" + str).slice(-2);
      }

      var d = (check.isset(parse)) ? new Date(Number(parse)) : new Date();
      return d.getFullYear() + "-" + _numPad(d.getMonth() + 1) + "-" + _numPad(d.getDate()) + " " + _numPad(d.getHours()) + ":" + _numPad(d.getMinutes()) + ":" + _numPad(d.getSeconds()) + "." + Number(String(d.getMilliseconds()).slice(0, 2));
    },
    formatDateParse: function (parse) {
      function _numPad(str) {
        return ("0" + str).slice(-2);
      }

      var d = (check.isset(parse)) ? new Date(Number(parse)) : new Date();
      return d.getFullYear() + "/" + _numPad(d.getMonth() + 1) + "/" + _numPad(d.getDate()) + " " + _numPad(d.getHours()) + ":" + _numPad(d.getMinutes()) + ":" + _numPad(d.getSeconds());
    },
    saveParams: function () {
      this.params = this.tmpParams;
      storage.s.set('params', JSON.stringify(this.params));
    },
    setParams: function () {
      this.params = common.jParse(storage.s.get('params'));
    },
    unsetParams: function () {
      storage.s.unset('params');
    },
    // ==========
    // ビデオ用情報保存
    // ==========
    saveVcInfo: function () {
      storage.s.set('vcInfo', JSON.stringify(this.vcInfo));
    },
    getVcInfo: function () {
      return JSON.parse(storage.s.get('vcInfo')) || undefined;
    },
    setVcInfo: function (obj) {
      this.vcInfo = obj;
    },
    unsetVcInfo: function () {
      storage.s.unset('vcInfo');
    },
    // ==========
    title: function () {
      return (document.getElementsByTagName('title')[0]) ? document.getElementsByTagName('title')[0].text : "";
    },
    makeToken: function () {
      var t = "";
      for ( var i = 0; i < this.n; i++ ) {
        t += this.str[Math.floor(Math.random() * this.str.length)];
      }
      return t;
    },
    token_add: function () {
      this.token = this.makeToken();
      return this.token;
    },
    jParse: function (d) {
      if ( d === undefined ) return d;
      return JSON.parse(d);
    },
    // バイト表示をKB, MB, GBに変更する
    // https://stackoverflow.com/questions/15900485/correct-way-to-convert-size-in-bytes-to-kb-mb-gb-in-javascript
    formatBytes: function (a, b) {
      if ( 0 == a ) return "0 Bytes";
      var c = 1024, d = b || 2, e = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"],
        f = Math.floor(Math.log(a) / Math.log(c));
      return parseFloat((a / Math.pow(c, f)).toFixed(d)) + " " + e[f]
    },
    createWidget: function () {
      var widget = window.sincloInfo.widget, displaySet = "";
      var css = this.widgetCssTemplate(widget),
        header = this.widgetHeaderTemplate(widget),
        //プレミアムプランであってもナビゲションを非表示にする
        //navi = this.widgetNaviTemplate(widget),
        navi = "",
        chat = this.chatWidgetTemplate(widget),
        call = this.widgetTemplate(widget),
        fotter = (check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.widget.hideFotter) && window.sincloInfo.custom.widget.hideFotter) ? '' : '<p id="fotter">Powered by <a target="sinclo" href="https://sinclo.medialink-ml.co.jp/lp/?utm_medium=web-widget&utm_campaign=widget-referral">sinclo</a></p>';
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
      if ( Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1 ) {
        //バナーも同時に生成したsinclo
        var sincloBanner = this.sincloBannerTemplate(widget);
        return "<sinclo id='sincloBox' >" + css + "<div id='sincloWidgetBox'>" + header + displaySet + fotter + "</div><div id='sincloBannerBox' style='display: none;'>" + sincloBanner + "</div></sinclo>";
      }
      else {
        //今までと同じ通常のsinclo
        return "<sinclo id='sincloBox' >" + css + "<div id='sincloWidgetBox'>" + header + displaySet + fotter + "</div></sinclo>";
      }
    },
    //サイズを返す関数
    getColorList: function (widget) {
      var widget = window.sincloInfo.widget;
      //通常設定か高度設定か判定 colorSettingType
      if ( Number(widget.colorSettingType) === 0 ) {
        //企業側吹き出し背景色は現在設定されているメインカラーから算出する
        var main_color = widget.mainColor;
        var code = main_color.substr(1), r, g, b;
        if ( code.length === 3 ) {
          r = String(code.substr(0, 1)) + String(code.substr(0, 1));
          g = String(code.substr(1, 1)) + String(code.substr(1, 1));
          b = String(code.substr(2)) + String(code.substr(2));
        }
        else {
          r = String(code.substr(0, 2));
          g = String(code.substr(2, 2));
          b = String(code.substr(4));
        }

        var balloonR = String(Math.floor(255 - (255 - parseInt(r, 16)) * 0.1));
        var balloonG = String(Math.floor(255 - (255 - parseInt(g, 16)) * 0.1));
        var balloonB = String(Math.floor(255 - (255 - parseInt(b, 16)) * 0.1));
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
      else {
        //高度な設定
        if ( widget.reBorderColor === undefined || widget.reBorderColor === 'none' ) {
          var reBorderColor = "#E8E7E0"; //念のため
          var reBorderNone = 1;
        }
        else {
          var reBorderColor = widget.reBorderColor;
          var reBorderNone = 0;
        }
        if ( widget.seBorderColor === undefined || widget.seBorderColor === 'none' ) {
          var seBorderColor = "#E8E7E0"; //念のため
          var seBorderNone = 1;
        }
        else {
          var seBorderColor = widget.seBorderColor;
          var seBorderNone = 0;
        }
        if ( widget.messageBoxBorderColor === undefined || widget.messageBoxBorderColor === 'none' ) {
          var messageBoxBorderColor = "#E8E7E0"; //念のため
          var messageBoxBorderNone = 1;
        }
        else {
          var messageBoxBorderColor = widget.messageBoxBorderColor;
          var messageBoxBorderNone = 0;
        }
        if ( widget.widgetBorderColor === undefined || widget.widgetBorderColor === 'none' ) {
          var widgetBorderColor = "#E8E7E0"; //念のため
          var widgetBorderNone = 1;
        }
        else {
          var widgetBorderColor = widget.widgetBorderColor;
          var widgetBorderNone = 0;
        }
        if ( widget.widgetInsideBorderColor === undefined || widget.widgetInsideBorderColor === 'none' ) {
          var widgetInsideBorderColor = "#E8E7E0"; //念のため
          var widgetInsideBorderNone = 1;
        }
        else {
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
    getSizeType: function (sizeTypeID) {
      switch ( sizeTypeID ) {
        case 1: //小
          var sizeArray = {
            boxHeight: 447,
            boxWidth: 285,
            widgetTitlePadding: 7,
            widgetTitleHeight: 32,
            widgetTitleTop: 6,
            widgetSubTitleHeight: 24,
            widgetDescriptionHeight: 15,
            navigationHeight: 40,
            chatTalkHeight: 194,
            classFlexBoxRowHeight: 75,
            sincloAccessInfoHeight: 26.5,
            fotterHeight: 26.5,
            telContentHeight: 125,
            paddingBottom: 30.8,
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
            widgetSubTitleHeight: 24,
            widgetDescriptionHeight: 15,
            navigationHeight: 40,
            chatTalkHeight: 284,
            classFlexBoxRowHeight: 75,
            sincloAccessInfoHeight: 26.5,
            fotterHeight: 26.5,
            telContentHeight: 214.5,
            paddingBottom: 45.6,
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
            widgetSubTitleHeight: 24,
            widgetDescriptionHeight: 15,
            navigationHeight: 40,
            chatTalkHeight: 374,
            classFlexBoxRowHeight: 75,
            sincloAccessInfoHeight: 26.5,
            fotterHeight: 26.5,
            telContentHeight: 305,
            paddingBottom: 60,
            d11font: 12,
            d12font: 13,
            d13font: 14,
            d14font: 15,
            d18font: 19,
            d20font: 21,
            d25font: 26
          };
          break;
        case 4: //最大、だが幅以外の値はwidgethandlerで計算する（要素表示、現状設定の兼ね合い）
          var sizeArray = {
            boxHeight: 632,
            boxWidth: $(window).width(),
            widgetTitlePadding: 7,
            widgetTitleHeight: 32,
            widgetTitleTop: 6,
            widgetSubTitleHeight: 24,
            widgetDescriptionHeight: 15,
            navigationHeight: 40,
            chatTalkHeight: 100,
            classFlexBoxRowHeight: 75,
            sincloAccessInfoHeight: 26.5,
            fotterHeight: 26.5,
            telContentHeight: 305,
            paddingBottom: 60,
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
            widgetSubTitleHeight: 24,
            widgetDescriptionHeight: 15,
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
    getAbridgementType: function () {
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
      switch ( widget.minimizeDesignType ) {
        case 1: //シンプル表示しない
          if ( spHeaderLightFlg === 1 ) {
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
          else {
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
          if ( spHeaderLightFlg === 1 ) {
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
          else {
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
          if ( spHeaderLightFlg === 1 ) {
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
          else {
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
          if ( spHeaderLightFlg === 1 ) {
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
          else {
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
      if ( smartphone ) {
        //スマホだったら縦か横かを判定
        if ( common.isPortrait() ) {
          //縦
          var MinRes = abridgementType['spMinRes'];
          var MaxRes = abridgementType['spMaxRes'];
        }
        else {
          //横
          var MinRes = true;
          var MaxRes = true;
        }
      }
      else {
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
    _headerContentsSettings: {
      _processSettings: function () {
        if ( check.smartphone() ) {
          $("#widgetTitle").addClass("spview");
          $("#widgetSubTitle").addClass("spview");
          $("#widgetDescription").addClass("spview");
        }
        this._deciedPosition("#widgetTitle", Number(sincloInfo.widget.widget_title_top_type));
        this._deciedPosition("#widgetSubTitle", Number(sincloInfo.widget.widget_title_name_type));
        this._deciedPosition("#widgetDescription", Number(sincloInfo.widget.widget_title_explain_type));
        this._setContentsView(Number(this._countContents()));
      },
      _deciedPosition: function (selector, position) {
        if ( position === 1 ) {
          $(selector).addClass("leftPosition");
        } else if ( position === 2 ) {
          $(selector).addClass("centerPosition");
        }
      },
      _countContents: function () {
        if ( Number(sincloInfo.widget.showDescription) === 1 && Number(sincloInfo.widget.showSubtitle) === 1 ) {
          return 2;
        } else if ( Number(sincloInfo.widget.showDescription) === 1 || Number(sincloInfo.widget.showSubtitle) === 1 ) {
          $("#widgetSubTitle").addClass("oneContent");
          $("#widgetDescription").addClass("oneContent");
          return 1;
        } else {
          $("#widgetSubTitle").addClass("noContent");
          $("#widgetDescription").addClass("noContent");
          return 0;
        }
      },
      _setContentsView: function (contents) {
        //サブタイトルまたは説明文のどちらかが存在する場合のみ
        if ( contents === 1 ) {
          if ( Number(sincloInfo.widget.showDescription) === 2 ) {
            $("#widgetDescription").hide();
            $("#widgetDescription").remove();
            return;
          }
          if ( Number(sincloInfo.widget.showSubtitle) === 2 ) {
            $("#widgetSubTitle").hide();
            $("#widgetSubTitle").remove();
            return;
          }
        }
      }
    },
    indicateSimpleNoImage: function () {
      //画像を表示しない場合
      $("#widgetTitle").addClass("noImage");
      $("#widgetSubTitle").addClass("noImage");
      $("#widgetDescription").addClass("noImage");
      common._headerContentsSettings._processSettings();
    },
    indicateSimpleImage: function () {
      //画像を表示する場合
      common._headerContentsSettings._processSettings();
    },
    //ヘッダ表示（通常表示）
    abridgementTypeShow: function () {
      $("#mainImage").show();
      $("#widgetSubTitle").show();
      $("#widgetDescription").show();
      $("#widgetTitle").removeClass("noImage");
      $("#widgetSubTitle").removeClass("noImage");
      $("#widgetDescription").removeClass("noImage");
      if ( check.smartphone() ) {
        if ( common.isPortrait() ) {
          if ( !$('#sincloBox p#widgetTitle').hasClass("notSimple") ) {
            $('#sincloBox p#widgetTitle').addClass("notSimple");
          }
        }
        else {
          //横
          $('#widgetTitle').css('text-align', 'center');
        }
      }
      else {
        if ( !$('#sincloBox p#widgetTitle').hasClass("notSimple") ) {
          $('#sincloBox p#widgetTitle').addClass("notSimple");
        }
      }
      if ( (Number(window.sincloInfo.widget.showMainImage) === 2 || window.sincloInfo.widget.mainImage === "") ) {
        $('#sincloBox p#widgetTitle').addClass("noImage");
      }
    },
    //ヘッダ非表示（シンプル表示）
    abridgementTypehide: function () {
      $("#mainImage").hide();
      $("#widgetSubTitle").hide();
      $("#widgetDescription").hide();
      //false/true:通常（PC）/スマホ
      if ( check.smartphone() ) {
        //スマホ時
        //スマホだったら縦か横かを判定
        if ( common.isPortrait() ) {
          //縦
          if ( $('#sincloBox p#widgetTitle').hasClass("notSimple") ) {
            $('#sincloBox p#widgetTitle').removeClass("notSimple");
          }
        }
        else {
          //横
          $('#widgetTitle').css('text-align', 'center');
        }
      }
      else {
        //PC時
        if ( $('#sincloBox p#widgetTitle').hasClass("notSimple") ) {
          $('#sincloBox p#widgetTitle').removeClass("notSimple");
        }
      }
      if ( (Number(window.sincloInfo.widget.showMainImage) === 2 || window.sincloInfo.widget.mainImage === "") ) {
        $('#sincloBox p#widgetTitle').addClass("noImage");
      }
    },
    //最大化時ボタン表示
    whenMaximizedBtnShow: function () {
      $("#minimizeBtn").show();
      $("#closeBtn").hide();
    },
    //最小化時ボタン表示
    whenMinimizedBtnShow: function () {
      var widget = window.sincloInfo.widget, displaySet = "";
      if ( Number(widget.closeButtonSetting) === 1 ) {
        //閉じるボタン無効
        $("#minimizeBtn").hide();
        $("#closeBtn").hide();
      }
      else {
        //閉じるボタン有効
        $("#minimizeBtn").hide();
        var smartphone = check.smartphone();
        if ( smartphone ) {
          //スマホ時
          //スマホだったら縦か横かを判定
          if ( common.isPortrait() ) {
            //縦
            $("#closeBtn").show();
          }
          else {
            $("#closeBtn").hide();
          }
        }
        else {
          $("#closeBtn").show();
        }
      }
    },
    toRGBCode: function (colorCode) {
      if ( colorCode.indexOf("#") >= 0 ) {
        var code = colorCode.substr(1), r, g, b;
        if ( code.length === 3 ) {
          r = String(code.substr(0, 1)) + String(code.substr(0, 1));
          g = String(code.substr(1, 1)) + String(code.substr(1, 1));
          b = String(code.substr(2)) + String(code.substr(2));
        }
        else {
          r = String(code.substr(0, 2));
          g = String(code.substr(2, 2));
          b = String(code.substr(4));
        }
        return "rgb(" + parseInt(r, 16) + ", " + parseInt(g, 16) + ", " + parseInt(b, 16) + ")";
      }
    },
    defaultOrientation: true,
    isPortrait: function () {
      var o = (window.orientation % 180 == 0);
      return (o && common.defaultOrientation) || !(o || common.defaultOrientation);
    },
    widgetCssTemplate: function (widget) {
      // システムで出力するテキストのカラー
      var systemTextColor = "#666666";
      // チャットのフォントカラー
      var chatContentTextColor = "#333333";

      //サイズを取得
      var sizeList = this.getSizeType(widget.widgetSizeType);

      //カラーリストの取得
      var colorList = this.getColorList(widget);

      var html = "", faintColor = widget.mainColor, balloonInnerColor = faintColor = widget.mainColor,
        highlightColor = widget.mainColor;
      if ( faintColor.indexOf("#") >= 0 ) {
        var code = faintColor.substr(1), r, g, b;
        if ( code.length === 3 ) {
          r = String(code.substr(0, 1)) + String(code.substr(0, 1));
          g = String(code.substr(1, 1)) + String(code.substr(1, 1));
          b = String(code.substr(2)) + String(code.substr(2));
        }
        else {
          r = String(code.substr(0, 2));
          g = String(code.substr(2, 2));
          b = String(code.substr(4));
        }
        faintColor = "rgba(" + parseInt(r, 16) + ", " + parseInt(g, 16) + ", " + parseInt(b, 16) + ", 0.1)";
        var balloonR = String(Math.floor(255 - (255 - parseInt(r, 16)) * 0.1));
        var balloonG = String(Math.floor(255 - (255 - parseInt(g, 16)) * 0.1));
        var balloonB = String(Math.floor(255 - (255 - parseInt(b, 16)) * 0.1));
        balloonInnerColor = 'rgb(' + balloonR + ', ' + balloonG + ', ' + balloonB + ');';
        var highlightColorR = String(Math.floor(255 - (255 - parseInt(r, 16)) * 0.7));
        var highlightColorG = String(Math.floor(255 - (255 - parseInt(g, 16)) * 0.7));
        var highlightColorB = String(Math.floor(255 - (255 - parseInt(b, 16)) * 0.7));
        highlightColor = 'rgb(' + highlightColorR + ', ' + highlightColorG + ', ' + highlightColorB + ');';
      }

      // 表示位置
      var widgetHorizontalPosition = "10px";
      var widgetVerticalPosition = "0px";
      if ( !check.smartphone() ) {
        widgetHorizontalPosition = (window.sincloInfo.custom && window.sincloInfo.custom.widget && window.sincloInfo.custom.widget.horizontalPosition) ? window.sincloInfo.custom.widget.horizontalPosition : "10px";
        widgetVerticalPosition = (window.sincloInfo.custom && window.sincloInfo.custom.widget && window.sincloInfo.custom.widget.verticalPosition) ? window.sincloInfo.custom.widget.verticalPosition : "0px";
      }
      var showPosition = "", chatPosition = {se: "", re: ""};
      switch ( Number(widget.showPosition) ) {
        case 1: // 右下
          showPosition = "bottom: " + widgetVerticalPosition + "; right: " + widgetHorizontalPosition + ";";
          chatPosition = {
            se: {
              mg: "margin-left: 10px;",
              color: colorList['seTextColor'],
              textSize: widget.seTextSize,
              backgroundColor: colorList['seBackgroundColor'],
              borderColor: colorList['seBorderColor']
            },
            re: {
              mg: "margin-right: 10px;",
              color: colorList['reTextColor'],
              textSize: widget.reTextSize,
              backgroundColor: colorList['reBackgroundColor'],
              borderColor: colorList['reBorderColor']
            },
          };
          if ( Number(sincloInfo.widget.widgetSizeType) === 4 && !check.smartphone() ) {
            showPosition = "bottom: 0; right: 0;"
          }
          break;
        case 2: // 左下
          showPosition = "bottom: " + widgetVerticalPosition + "; left: " + widgetHorizontalPosition + ";";
          chatPosition = {
            se: {
              mg: "margin-left: 10px;",
              color: colorList['seTextColor'],
              textSize: widget.seTextSize,
              backgroundColor: colorList['seBackgroundColor'],
              borderColor: colorList['seBorderColor']
            },
            re: {
              mg: "margin-right: 10px;",
              color: colorList['reTextColor'],
              textSize: widget.reTextSize,
              backgroundColor: colorList['reBackgroundColor'],
              borderColor: colorList['reBorderColor']
            },
          };
          if ( Number(sincloInfo.widget.widgetSizeType) === 4 && !check.smartphone() ) {
            showPosition = "bottom: 0; left: 0;"
          }
          break;
      }

      switch ( Number(widget.chatMessageDesignType) ) {
        case 1: //BOX型
          if(Number(widget.widgetSizeType) == 1) {
            chatPosition.se.mg = "margin-left: 37.5px; margin-right: 10px;  border-bottom-right-radius: 0;";
            chatPosition.re.mg = "margin-left: 10px; margin-right: 17.5px; border-bottom-left-radius: 0;";
          }
          if(Number(widget.widgetSizeType) == 2) {
            chatPosition.se.mg = "margin-left: 45px; margin-right: 10px;  border-bottom-right-radius: 0;";
            chatPosition.re.mg = "margin-left: 10px; margin-right: 21px; border-bottom-left-radius: 0;";
          }
          if(Number(widget.widgetSizeType) == 3 || Number(widget.widgetSizeType) == 4) {
            chatPosition.se.mg = "margin-left: 52.7px; margin-right: 10px;  border-bottom-right-radius: 0;";
            chatPosition.re.mg = "margin-left: 10px; margin-right: 24.6px; border-bottom-left-radius: 0;";
          }
          break;
        case 2: //吹き出し型
          if(Number(widget.widgetSizeType) == 1) {
            chatPosition.se.mg = "margin-left: 37.5px;";
            chatPosition.re.mg = "margin-right: 17.5px;";
          }
          if(Number(widget.widgetSizeType) == 2) {
            chatPosition.se.mg = "margin-left: 45px;";
            chatPosition.re.mg = "margin-right: 21px;";
          }
          if(Number(widget.widgetSizeType) == 3 || Number(widget.widgetSizeType) == 4) {
            chatPosition.se.mg = "margin-left: 52.7px;";
            chatPosition.re.mg = "margin-right: 24.6px;";
          }
          break;
        default: //BOX型
          if(Number(widget.widgetSizeType) == 1) {
            chatPosition.se.mg = "margin-left: 37.5px; margin-right: 10px;  border-bottom-right-radius: 0;";
            chatPosition.re.mg = "margin-left: 10px; margin-right: 17.5px; border-bottom-left-radius: 0;";
          }
          if(Number(widget.widgetSizeType) == 2) {
            chatPosition.se.mg = "margin-left: 45px; margin-right: 10px;  border-bottom-right-radius: 0;";
            chatPosition.re.mg = "margin-left: 10px; margin-right: 21px; border-bottom-left-radius: 0;";
          }
          if(Number(widget.widgetSizeType) == 3 || Number(widget.widgetSizeType) == 4) {
            chatPosition.se.mg = "margin-left: 52.7px; margin-right: 10px;  border-bottom-right-radius: 0;";
            chatPosition.re.mg = "margin-left: 10px; margin-right: 24.6px; border-bottom-left-radius: 0;";
          }
          break;
      }

      if(window.sincloInfo.widget.isSendMessagePositionLeft) {
        var tmp = JSON.stringify(chatPosition.se);
        chatPosition.se = chatPosition.re;
        chatPosition.re = JSON.parse(tmp);
      }

      // 基本設定
      var widgetWidth = 285, ratio = 1;
      // ユーザーエージェント
      var ua = navigator.userAgent.toLowerCase();

      html += '  <style>';

      /* 共通スタイル */
      html += '      @media print{ sinclo { display:none!important; } }';
      //アイコンフォント用
      html += '      @font-face { font-family: "FA5P"; font-style: normal; font-weight: 300; src: url("' + sincloInfo.site.files + '/webfonts/fa-light-300.eot"); src: url("' + sincloInfo.site.files + '/webfonts/fa-light-300.eot?#iefix") format("embedded-opentype"), url("' + sincloInfo.site.files + '/webfonts/fa-light-300.woff2") format("woff2"), url("' + sincloInfo.site.files + '/webfonts/fa-light-300.woff") format("woff"), url("' + sincloInfo.site.files + '/webfonts/fa-light-300.ttf") format("truetype"), url("' + sincloInfo.site.files + '/webfonts/fa-light-300.svg#fontawesome") format("svg"); }';
      html += '      @font-face { font-family: SincloFont; font-style: normal; font-weight: 900; src: url("' + sincloInfo.site.files + '/webfonts/fa-solid-900.eot"); src: url("' + sincloInfo.site.files + '/webfonts/fa-solid-900.eot?#iefix") format("embedded-opentype"), url("' + sincloInfo.site.files + '/webfonts/fa-solid-900.woff2") format("woff2"), url("' + sincloInfo.site.files + '/webfonts/fa-solid-900.woff") format("woff"), url("' + sincloInfo.site.files + '/webfonts/fa-solid-900.ttf") format("truetype"), url("' + sincloInfo.site.files + '/webfonts/fa-solid-900.svg#fontawesome") format("svg"); }';
      html += '      #sincloBox .sinclo-fal { font-family: "FA5P"; display: inline-block; font-style: normal; font-weight: 300; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }';
      html += '      #sincloBox .sinclo-fal.fa-4x { font-size: 4em; }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file:before { content: "\\f15b" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-image:before { content: "\\f1c5" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-pdf:before { content: "\\f1c1" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-word:before { content: "\\f1c2" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-powerpoint:before { content: "\\f1c4" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-excel:before { content: "\\f1c3" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-audio:before { content: "\\f1c7" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-video:before { content: "\\f1c8" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-zip:before { content: "\\f1c6" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-code:before { content: "\\f1c9" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-file-text:before { content: "\\f15c" }';
      html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-cloud-upload:before { content: "\\f0ee" }';
      html += '      #sincloBox .sinclo-fal.fa-comments:before { content: "\\f086" }';
      html += '      #sincloBox .sinclo-fal.fa-phone:before { content: "\\f095" }';
      html += '      #sincloBox .sinclo-fal.fa-robot:before { content: "\\f544" }';
      html += '      #sincloBox .sinclo-fal.fa-comment-alt-lines:before { content: "\\f4a6" }';
      html += '      #sincloBox .sinclo-fal.fa-comments-alt:before { content: "\\f4b6" }';
      html += '      #sincloBox .sinclo-fal.fa-comment-lines:before { content: "\\f4b0" }';
      //アイコンフォント用
      /* http://meyerweb.com/eric/tools/css/reset/
         v2.0 | 20110126
         License: none (public domain)
         ※ ここの値は気軽に編集しないこと
      */
      html += '      #sincloBox div, #sincloBox span, #sincloBox applet, #sincloBox object, #sincloBox iframe, #sincloBox h1, #sincloBox h2, #sincloBox h3, #sincloBox h4, #sincloBox h5, #sincloBox h6, #sincloBox p, #sincloBox blockquote, #sincloBox pre, #sincloBox a, #sincloBox abbr, #sincloBox acronym, #sincloBox address, #sincloBox big, #sincloBox cite, #sincloBox code, #sincloBox del, #sincloBox dfn, #sincloBox em, #sincloBox img, #sincloBox ins, #sincloBox kbd, #sincloBox q, #sincloBox s, #sincloBox samp, #sincloBox small, #sincloBox strike, #sincloBox strong, #sincloBox sub, #sincloBox sup, #sincloBox tt, #sincloBox var, #sincloBox b, #sincloBox u, #sincloBox i, #sincloBox center, #sincloBox dl, #sincloBox dt, #sincloBox dd, #sincloBox ol, #sincloBox ul, #sincloBox li, #sincloBox fieldset, #sincloBox form, #sincloBox label, #sincloBox legend, #sincloBox table, #sincloBox caption, #sincloBox tbody, #sincloBox tfoot, #sincloBox thead, #sincloBox tr, #sincloBox th, #sincloBox td, #sincloBox article, #sincloBox aside, #sincloBox canvas, #sincloBox details, #sincloBox embed, #sincloBox figure, #sincloBox figcaption, #sincloBox footer, #sincloBox header, #sincloBox hgroup, #sincloBox menu, #sincloBox nav, #sincloBox output, #sincloBox ruby, #sincloBox section, #sincloBox summary, #sincloBox time, #sincloBox mark, #sincloBox audio, #sincloBox video' +
        ' { ' +
        '  font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica,Helvetica Neue,Arial,Verdana;' +
        '  font-weight: normal;' +
        '  font-variant: normal;' +
        '  position: static;' +
        '  top: auto;' +
        '  right: auto;' +
        '  bottom: auto;' +
        '  left: auto;' +
        '  float: none;' +
        '  box-sizing: border-box;' +
        '  width: auto;' +
        '  min-width: 0;' +
        '  max-width: none;' +
        '  height: auto;' +
        '  min-height: 0;' +
        '  max-height: none;' +
        '  margin: 0;' +
        '  padding: 0;' +
        '  text-align: start;' +
        '  vertical-align: baseline;' +
        '  text-decoration: none;' +
        '  text-indent: 0;' +
        '  letter-spacing: normal;' +
        '  word-spacing: normal;' +
        '  color: #333;' + // sinclo-particular value
        '  border: 0;' +
        '  background: initial;' +
        '  box-shadow: none;' +
        '  text-shadow: none;' +
        '  -webkit-font-smoothing: subpixel-antialiased;' +
        '  direction: ltr;' +
        ' }';
      /* HTML5 display-role reset for older browsers */
      html += '      #sincloBox article, #sincloBox aside, #sincloBox details, #sincloBox figcaption, #sincloBox figure, #sincloBox footer, #sincloBox header, #sincloBox hgroup, #sincloBox menu, #sincloBox nav, #sincloBox section { display: block; }';
      html += '      #sincloBox ol, #sincloBox ul { list-style: none; }';
      html += '      #sincloBox blockquote, #sincloBox q { quotes: none; }';
      html += '      #sincloBox blockquote:before, #sincloBox blockquote:after, #sincloBox q:before, #sincloBox q:after { content: \'\'; content: none; }';
      html += '      #sincloBox table { border-collapse: collapse; border-spacing: 0; }';
      //END OF reset-css
      html += "      #sincloBox { display: none; position: fixed; " + showPosition + " z-index: 999998; background-color: rgba(0,0,0,0); }";
      html += '      #sincloBox * { color: #333333; line-height: 1.3; box-sizing: border-box; font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana; letter-spacing: initial; }';
      html += '      #sincloBox *:before, #sincloBox *:after { box-sizing: content-box; }';
      html += '      #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }';
      html += '      #sincloBox .sinclo-hide { display:none!important; }';
      //html += '      #sincloBox a { color: #8a8a8a }';
      html += '      #sincloBox .sinclo_se a { color: '+ chatPosition.se.color +' }';
      html += '      #sincloBox .sinclo_re a { color: '+ chatPosition.re.color +' }';
      html += '      #sincloBox ul#chatTalk li.sinclo_re span.telno { color: ' + chatPosition.re.color + '; }';
      html += '      #sincloBox ul#chatTalk li.sinclo_re span.link { color: ' + chatPosition.re.color + '; }';
      html += '      #sincloBox ul#chatTalk li.sinclo_re .smallSizeImg { max-width: 165px; max-height: 120px; display:block;}';
      html += '      #sincloBox ul#chatTalk li.sinclo_re .middleSizeImg { max-width: 215px; max-height: 188px; display:block;}';
      html += '      #sincloBox ul#chatTalk li.sinclo_re .largeSizeImg { max-width: 265px; max-height: 285px; display:block;}';
      html += '      #sincloBox sinclo-div {display: block; }';
      html += '      #sincloBox label { display: inline; max-width: 100%; margin-bottom: 0; font-weight: normal;}';
      html += '      #sincloBox sinclo-div#widgetHeader { cursor:pointer; position: relative;}';
      html += '      #sincloBox #titleWrap { position: relative; }';
      html += '      #sincloBox input { text-align: left; }';
      html += '      #sincloBox sinclo-div#widgetHeader:after { content: " "; position: absolute; bottom: 0; left: 0; right: 0; z-index: -1; background-color: #FFF; }';
      html += '      #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }';
      html += '      #sincloBox span#mainImage { cursor:pointer; z-index: 2; position: absolute; }';
      html += '      #sincloBox span#mainImage img { background-color: ' + colorList['mainColor'] + ' }';
      html += '      #sincloBox span#mainImage i.normal { color: ' + colorList['stringColor'] + '; background-color: ' + colorList['mainColor'] + '; }';
      html += '      #sincloBox span#mainImage i.fa-robot { padding-bottom: 3px; }';
      html += '      #sincloBox p#widgetTitle { position:relative; cursor:pointer; border: 1px solid ' + colorList['mainColor'] + '; border-bottom:none; background-color: ' + colorList['mainColor'] + ';text-align: center; margin: 0; color: ' + colorList['stringColor'] + ' ;white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}';
      html += '      #sincloBox p#widgetTitle #sincloChatUnread { position: absolute; top: 0; left: 0; color: #FFF; font-style: normal; text-align: center; font-weight: bold; background-color: #FF5C5C; }';
      html += '      #sincloBox div#minimizeBtn { display: none; cursor: pointer; background-image: url("' + window.sincloInfo.site.files + '/img/widget/minimize.png"); background-position-y: 0px; position: absolute; top: calc(50% - 10px); right: 6px; bottom: 6px; content: " "; width: 20px; height: 20px; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }';
      //＋ボタンと×ボタンは閉じるボタン設定によってポジションが異なるため別々に記載。なお、IDは同一とする
      if ( Number(widget.closeButtonSetting) === 1 ) {
        //閉じるボタン無効
        html += '      #sincloBox div#closeBtn { display: none; cursor: pointer; background-image: url("' + window.sincloInfo.site.files + '/img/widget/close.png"); background-position-y: -1.5px; position: absolute; top: calc(50% - 9px); right: 6px; content: " "; width: 18px; height: 18px; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }';
      }
      else {
        //閉じるボタン有効
        html += '      #sincloBox div#closeBtn { display: none; cursor: pointer; background-image: url("' + window.sincloInfo.site.files + '/img/widget/close.png"); background-position-y: -1.5px; position: absolute; top: calc(50% - 8px); right: 6px; content: " "; width: 18px; height: 18px; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }';
      }

      html += '      #sincloBox div#sincloWidgetBox { position: relative; top: 0px; }';
      html += '      #sincloBox div#sincloWidgetBox > section { background-color: #FFF; border-top: none; }';
      html += '      #sincloBox ul#chatTalk li a, #sincloBox #fotter a {  text-decoration: underline; }';
      html += '      #sincloBox section { display: none; padding: 0!important; top:0px!important; }';
      html += '      #sincloBox .flexBox { position: relative; display: -ms-flexbox; display: -webkit-flex; display: flex; -ms-flex-direction: column; -webkit-flex-direction: column; flex-direction: column }';
      if ( widget.chatMessageCopy === 1 ) {
        html += '      #sincloBox .flexBoxRow { display: -ms-flexbox; display: -webkit-flex; display: flex; -ms-flex-direction: row; -webkit-flex-direction: row; flex-direction: row; user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;}';
      }
      else {
        html += '      #sincloBox .flexBoxRow { display: -ms-flexbox; display: -webkit-flex; display: flex; -ms-flex-direction: row; -webkit-flex-direction: row; flex-direction: row; }';
      }
      // チャットを使用する際
      if ( window.sincloInfo.contract.chat ) {
        html += '      @keyframes rightEffect { 0% { transform :translate3d(20px, 0px, 0px); opacity :0; } 70% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }';
        html += '      @keyframes leftEffect { 0% { transform :translate3d(-20px, 0px, 0px) scale(0.8); opacity :0; } 69% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }';
        html += '      @keyframes fadeIn { 0% { opacity :0; } 100% { opacity :1; } }';
        html += '      @keyframes noneRightEffect { 0% { opacity :0; } 70% {} 100% { opacity :1; } }';
        html += '      @keyframes noneLeftEffect { 0% { opacity :0; } 69% {} 100% { opacity :1; } }';
        html += '      #sincloBox #mainImage em { position: absolute; background-image: url("' + window.sincloInfo.site.files + '/img/chat-bg.png");background-size: contain;background-repeat: no-repeat; color: #FFF; font-style: normal; text-align: center; font-weight: bold }';
        // ファイルフォントアイコン-----------
        html += '      #sincloBox ul#chatTalk li .sinclo-fal.fa-4x { font-size:4em; }';
        // ------------ファイルフォントアイコン
        html += '      #sincloBox ul#chatTalk li span.sendFileMessage { color: '+ chatPosition.re.color +' }';
        html += '      #sincloBox ul#chatTalk li div.sendFileContent { display: table; table-layout:fixed; width: 100%; height: 64px; white-space: pre-line; margin-bottom: 0; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileContent * { color: '+ chatPosition.re.color +' }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea { display: table-cell; width: 64px; height: 64px; border: 1px solid #D9D9D9; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileMetaArea { display:table-cell; vertical-align: middle; margin-left: 10px; margin-bottom: 0px; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea .sendFileThumbnail { text-align: center; vertical-align: middle; display: inline-block; width: 100%; height: auto; margin-left: 0; margin-bottom: 0px; margin-right: auto; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileThumbnailArea:before { content: ""; height: 100%; vertical-align: middle; width: 0px; display: inline-block; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileMetaArea .data { margin-left: 1em; margin-bottom: 5px; display: block; }';
        html += '      #sincloBox ul#chatTalk li div.sendFileMetaArea .data.sendFileSize { margin-bottom: 0px; }';

        if ( widget.chatMessageCopy === 1 ) {
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
        html += '      #sincloBox ul sinclo-chat { clear: both!important; }';
        html += '      #sincloBox ul sinclo-typing { padding-bottom: ' + sizeList['paddingBottom'] + 'px; display: block; }';
        html += '      #sincloBox ul#chatTalk li { text-align: left!important; word-wrap: break-word; word-break: break-all; white-space: pre-wrap!important; background-color: transparent; white-space: pre; color: ' + chatContentTextColor + '; font-weight: normal!important; }';
        html += '      #sincloBox ul#chatTalk li.sinclo_se span.sinclo-text-line { display: inline-block; color:'+ chatPosition.se.color + '; font-size: '+ chatPosition.se.textSize +'px; }';
        html += '      #sincloBox ul#chatTalk li.sinclo_re span.sinclo-text-line { display: inline-block; color:'+ chatPosition.re.color + '; font-size: '+ chatPosition.re.textSize +'px; }';
        if (widget.chatMessageDesignType === 2) {
          var leftMessageClass = 'sinclo_re';
          var rightMessageClass = 'sinclo_se';
          if (window.sincloInfo.widget.isSendMessagePositionLeft) {
            leftMessageClass = 'sinclo_se';
            rightMessageClass = 'sinclo_re';
          }
          // 吹き出し型
          html += '      #sincloBox ul#chatTalk li { line-height: 1.4; padding: 10px 15px; border-radius: 12px !important;}';
          html += '      #sincloBox ul#chatTalk li.' + rightMessageClass + ' { ' + chatPosition.se.mg + 'background-color: '+ chatPosition.se.backgroundColor +'; display: inline-block; position: relative; margin-right: 15px; border-bottom-right-radius: 0px!important; color:'+ chatPosition.se.color + '; font-size: '+ chatPosition.se.textSize +'px; }';
          html += '      #sincloBox ul#chatTalk li.' + rightMessageClass + ':before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; border: 5px solid transparent; border-left: 5px solid '+ chatPosition.se.backgroundColor +'; border-bottom: 5px solid '+ chatPosition.se.backgroundColor +'; z-index: 2; }';
          html += '      #sincloBox ul#chatTalk li.' + rightMessageClass + ':after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; z-index: 1 }';
          if(colorList['seBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.' + rightMessageClass + ':after {border-left: 5px solid '+ chatPosition.se.borderColor +'; border-bottom: 5px solid '+ chatPosition.se.borderColor +'; }';
          }
          html += '      #sincloBox ul#chatTalk li.' + leftMessageClass + ' { ' + chatPosition.re.mg + 'background-color:' + chatPosition.re.backgroundColor + '; display: inline-block; position: relative; margin-left: 10px; padding-right: 15px; border-bottom-left-radius: 0px!important; color:'+ chatPosition.re.color +'; font-size: '+ chatPosition.re.textSize +'px; }';
          html += '      #sincloBox ul#chatTalk li.' + leftMessageClass + ':before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; border: 5px solid transparent; border-right: 5px solid ' + chatPosition.re.backgroundColor + '; border-bottom: 5px solid ' + chatPosition.re.backgroundColor + '; z-index: 2; }';
          html += '      #sincloBox ul#chatTalk li.' + leftMessageClass + ':after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; border: 5px solid transparent; z-index: 1; }';
          if(colorList['reBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.' + leftMessageClass + ':after {border-right: 5px solid '+ chatPosition.re.borderColor +'; border-bottom: 5px solid '+ chatPosition.re.borderColor +'; }';
          }
        } else {
          // BOX型
          html += '      #sincloBox ul#chatTalk li.sinclo_se { ' + chatPosition.se.mg + 'background-color: '+ chatPosition.se.backgroundColor + '; color:' + chatPosition.se.color + '; font-size: '+ chatPosition.se.textSize +'px; display: inline-block; position: relative;}';
          html += '      #sincloBox ul#chatTalk li.sinclo_re { ' + chatPosition.re.mg + 'background-color:' + chatPosition.re.backgroundColor + '; color:' + chatPosition.re.color + '; font-size: '+ chatPosition.re.textSize +'px; display: inline-block; position: relative;}';
        }
        if ( widget.chatMessageWithAnimation === 1 ) {
          html += '      #sincloBox ul#chatTalk li.effect_right { -webkit-animation-name:rightEffect; animation-name:rightEffect; -webkit-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; animation-fill-mode:both; -webkit-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; animation-delay:0.6s; }';
          html += '      #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; animation-fill-mode:both; -webkit-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; animation-delay:0.6s; }';
        } else {
          html += '      #sincloBox ul#chatTalk li.effect_right { -webkit-animation-name:noneRightEffect; animation-name:noneRightEffect; -webkit-animation-duration:1ms; animation-duration:1ms; -webkit-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; animation-fill-mode:both; opacity:0; -webkit-animation-delay:0.7s; animation-delay:0.7s; }';
          html += '      #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:noneLeftEffect; animation-name:noneLeftEffect; -webkit-animation-duration:1ms; animation-duration:1ms; -webkit-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; animation-fill-mode:both; opacity:0; -webkit-animation-delay:0.7s; animation-delay:0.7s; }';

        }
        html += '      #sincloBox ul#chatTalk li#sinclo_typeing_message { position: relative; color: #d5d5d5; border: none; text-align: center; }';
        html += '      #sincloBox ul#chatTalk li#sinclo_typeing_message span { position: absolute; top: 0; bottom: 0; left: 50%; display: block; }';
        html += '      #sincloBox ul#chatTalk li span.cName { display: block; color: ' + colorList['cNameTextColor'] + '; font-weight: bold; }';
        html += '      #sincloBox ul#chatTalk li sinclo-radio { display: inline-block; margin-top: ' + widget.btwButtonMargin + 'px; } ';
        html += '      #sincloBox ul#chatTalk li span.sinclo-text-line + sinclo-radio { margin-top: ' + widget.lineButtonMargin + 'px; } ';
        html += '      #sincloBox ul#chatTalk li sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; } ';
        html += '      #sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; margin: 0; padding: 0 0 0 ' + (Number(chatPosition.re.textSize) + 7) + 'px; color:' + chatPosition.re.color + '; min-height: 12px; font-size: ' + chatPosition.re.textSize + 'px; } ';
        html += '      #sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: ' + Math.ceil((Number(chatPosition.re.textSize)/2)) + 'px; left: 0px; margin-top: -' + Math.ceil((Number(chatPosition.re.textSize)/2)) + 'px; width: ' + (Number(chatPosition.re.textSize)) + 'px; height: ' + (Number(chatPosition.re.textSize)) + 'px; border: 0.5px solid #999; border-radius: 50%; background-color: #FFF; } ';
        html += '      #sincloBox ul#chatTalk li sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: ' + Math.ceil((Number(chatPosition.re.textSize)/2)) + 'px; left: ' + ((chatPosition.re.textSize/2 - ((chatPosition.re.textSize-6)/2))+1) + 'px; margin-top: -' + (Math.round(chatPosition.re.textSize/2)-4) + 'px; width: ' + (Number(chatPosition.re.textSize)-7) + 'px; height: ' + (Number(chatPosition.re.textSize)-7) + 'px; background: ' + colorList['mainColor'] + '; border-radius: 50%; } ';
        html += '      #sincloBox ul#chatTalk sinclo-chat-receiver { cursor: pointer; display: none; position: absolute; left: 0; right: 0; width: 100%; height: 1.5em; background-color: rgba(0, 0, 0, 0.45); vertical-align: middle; word-wrap: break-word; z-index: 2; } ';
        html += '      #sincloBox ul#chatTalk sinclo-chat-receiver:before {content: ""; display: inline-block; border: 6px solid transparent; position: absolute; top: 50%; margin-top: -3px; left: 5px; height: 8px; border-top: 8px solid white; border-bottom: 0; }';
        html += '      #sincloBox ul#chatTalk sinclo-chat-receiver #receiveMessage { overflow: hidden; font-size: 10px; height: 100%; line-height: 2em; padding-left: 2em; color: #FFF; }';
        html += '      #sincloBox section#chatTab sinclo-div:not(#flexBoxWrap) { border-top: 1px solid ' + colorList['widgetInsideBorderColor'] + '; background-color: ' + colorList['chatMessageBackgroundColor'] + ';}';

        /* ヒアリング */
        html += '#sincloBox ul#chatTalk li.sinclo_se.cancelable span.sinclo-text-line { text-decoration: underline; cursor: pointer; }';
        html += '#sincloBox ul#chatTalk li.sinclo_se.skip_input {display: none}';

        /* ファイル受信  */
        var previewFileClasses = '#sincloBox #chatTalk li.sinclo_re';
        var uploadFileClasses = '#sincloBox #chatTalk li.sinclo_se';
        if(window.sincloInfo.widget.isSendMessagePositionLeft) {
          previewFileClasses = '#sincloBox #chatTalk li.sinclo_se';
          uploadFileClasses = '#sincloBox #chatTalk li.sinclo_re';
        }

        if(Number(widget.widgetSizeType) == 1) {
          html += previewFileClasses + '.recv_file_left, ' + uploadFileClasses + '.recv_file_right { display: block; padding: 10px!important; line-height: 0; }';
          html += previewFileClasses + '.uploaded { padding: 10px!important; line-height: 0; }';
          html += uploadFileClasses + ' div.receiveFileContent div.selectFileArea p.drop-area-message { margin: 9px 0 6.5px; line-height: 20px; }';
          html += uploadFileClasses + ' div.receiveFileContent div.selectFileArea p.drop-area-button { display: flex; justify-content: center; line-height: 0px; margin: 6.5px 0 9px; }';
        } else if (check.smartphone() || Number(widget.widgetSizeType) == 2) {
          html += previewFileClasses + '.recv_file_left, ' + uploadFileClasses + '.recv_file_right { display: block; padding: 12px!important; line-height: 0; }';
          html += previewFileClasses + '.uploaded { padding: 12px!important; line-height: 0; }';
          html += uploadFileClasses + '  div.receiveFileContent div.selectFileArea p.drop-area-message { margin: 13px 0 6.5px; line-height: 24px; }';
          html += uploadFileClasses + '  div.receiveFileContent div.selectFileArea p.drop-area-button { display: flex; justify-content: center; line-height: 0px; margin: 6.5px 0 13px; }';
        } else if(Number(widget.widgetSizeType) == 3) {
          html += previewFileClasses + '.recv_file_left, ' + uploadFileClasses + '.recv_file_right { display: block; padding: 14px!important; line-height: 0; }';
          html += previewFileClasses + '.uploaded { padding: 14px!important; line-height: 0; }';
          html += uploadFileClasses + ' div.receiveFileContent div.selectFileArea p.drop-area-message { margin: 13px 0 6.5px; line-height: 24px; }';
          html += uploadFileClasses + ' div.receiveFileContent div.selectFileArea p.drop-area-button { display: flex; justify-content: center; line-height: 0px; margin: 6.5px 0 13px; }';
        }

        html += uploadFileClasses + ' div.receiveFileContent { position: relative; line-height: 0; border: 1px dashed ' + chatPosition.re.color + '; }';
        html += uploadFileClasses + ' div.receiveFileContent div.selectFileArea { line-height: 0; }';
        html += uploadFileClasses + ' div.receiveFileContent div.selectFileArea p { margin: 6.5px 0; text-align: center; color:' + chatPosition.re.color + '; font-weight: bold; }';
        html += uploadFileClasses + ' div.receiveFileContent a.select-file-button { display:inline-block; width:75%; padding: 5px 35px; border-radius: 0; text-decoration: none; cursor: pointer; text-align: center; background-color: ' + chatPosition.re.color + '!important; color: ' + chatPosition.re.backgroundColor + '; font-weight: normal; }';
        html += uploadFileClasses + ' div.receiveFileContent a.select-file-button:hover { opacity: .8; }';
        html += uploadFileClasses + ' div.cancelReceiveFileArea { margin-top: 5px; }';
        html += uploadFileClasses + ' div.cancelReceiveFileArea a { font-size: ' + (chatPosition.re.textSize - 1) + 'px; cursor: pointer; text-decoration: underline; }';
        html += uploadFileClasses + ' div.receiveFileContent div.selectFileArea p.drop-area-icon i { line-height: 1; font-size: 3em; color: ' + chatPosition.re.textSize + '; }';
        html += previewFileClasses + ' div.receiveFileContent { position: relative; line-height: 0; background-color: #FFF; padding: 5px; }';
        html += previewFileClasses + ' div.receiveFileContent div.selectFileArea { line-height: 3px; }';
        html += previewFileClasses + ' div.receiveFileContent div.selectFileArea p.preview { text-align: center; }';
        html += previewFileClasses + ' div.receiveFileContent div.selectFileArea p.preview img.small { max-width: 165px; max-height: 120px; }';
        html += previewFileClasses + ' div.receiveFileContent div.selectFileArea p.preview img.middle { max-width: 215px; max-height: 188px; }';
        html += previewFileClasses + ' div.receiveFileContent div.selectFileArea p.preview img.large { max-width: 265px; max-height: 285px; }';
        html += previewFileClasses + ' div.receiveFileContent div.selectFileArea p.commentarea { text-align: center; width: 100%; }';
        html += previewFileClasses + ' div.receiveFileContent div.selectFileArea p.commentarea textarea { display: block; margin: 0 auto; border-radius: 0px; width: 97%; height: 40px; resize: none; }';
        html += previewFileClasses + ' div.receiveFileContent div.selectFileArea p.commentarea textarea:focus { outline: none!important; border-color: ' + colorList['chatSendBtnBackgroundColor'] + '!important;}'
        html += previewFileClasses + ' div.receiveFileContent div.actionButtonWrap { display: flex; justify-content: space-between; width: 97%; margin: 0 auto; font-size: 0px; }';
        html += previewFileClasses + ' div.receiveFileContent div.actionButtonWrap a:hover { opacity: .8; }';
        html += previewFileClasses + ' div.receiveFileContent div.actionButtonWrap a.cancel-file-button { display: block; margin-right: 2px; width: 49%; height: auto; padding: 5px 10px; border-radius: 0; text-decoration: none; cursor: pointer; margin: 0 auto; text-align: center; background-color: #7F7F7F!important; color: #FFF; font-weight: normal; word-break: keep-all; }';
        html += previewFileClasses + ' div.receiveFileContent div.actionButtonWrap a.send-file-button { display: block; margin-left: 2px; width: 49%; height: auto; padding: 5px 10px; border-radius: 0; text-decoration: none; cursor: pointer; margin: 0 auto; text-align: center; background-color: ' + colorList['chatSendBtnBackgroundColor'] + '; color: ' + colorList['chatSendBtnTextColor'] + '; font-weight: normal; word-break: keep-all; }';
        html += '#sincloBox #chatTalk li div div.loadingPopup { display: flex; flex-flow: column nowrap; justify-content: center; align-items: center; text-align:center; vertical-align: middle; color: #FFF; background-color: rgba(0, 0, 0, 0.7); position: absolute; top:0; right: 0; bottom: 0; left: 0; }';
        html += '#sincloBox #chatTalk li div div.loadingPopup.hide { display: none; }';
        html += '#sincloBox #chatTalk li div div.loadingPopup i { font-size: 6em; text-align:center; color: #FFF; }';
        html += '#sincloBox #chatTalk li div div.loadingPopup p.progressMessage { text-align:center; color: #FFF; }';
        html += '#sincloBox #chatTalk li div div.loadingPopup i.load { -webkit-animation: spin 1.5s linear infinite; -moz-animation: spin 1.5s linear infinite; -ms-animation: spin 1.5s linear infinite; -o-animation: spin 1.5s linear infinite; animation: spin 1.5s linear infinite; }';
        html += '@-webkit-keyframes spin { 0% {-webkit-transform: rotate(0deg);} 100% {-webkit-transform: rotate(360deg);} }';
        html += '@-moz-keyframes spin { 0% {-moz-transform: rotate(0deg);} 100% {-moz-transform: rotate(360deg);} }';
        html += '@-ms-keyframes spin { 0% {-ms-transform: rotate(0deg);} 100% {-ms-transform: rotate(360deg);} }';
        html += '@-o-keyframes spin { 0% {-o-transform: rotate(0deg);} 100% {-o-transform: rotate(360deg);} }';
        html += '@keyframes spin { 0% {transform: rotate(0deg);} 100% {transform: rotate(360deg);} }';

        // 一括ヒアリング
        html += '#sincloBox #chatTalk li.sinclo_re.sinclo_form { display: block; padding: 10px 15px 15px 15px; line-height: 0; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formContentArea { margin: 0; line-height: 0; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formContentArea p.formMessage { color: ' + chatPosition.re.color + '; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formArea { background-color: #FFF; line-height: 0; padding: 10px 10px 0 10px; margin-top: 10px; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formElement { line-height: 0; color: #333; display: flex; justify-content: stretch; flex-flow: column nowrap; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formElement.withMB { margin-bottom: 8px; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formElement label.formLabel { margin-bottom: 3px; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formElement label.formLabel span.require { color: #7F0000 }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formElement input.formInput { padding: 5px; border: 1px solid ' + chatPosition.re.backgroundColor + '; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formArea p.formOKButtonArea { margin-top: 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center;  }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formArea p.formOKButtonArea span.formOKButton { width: 100px; height: 30px; cursor: pointer; display: inline-flex; justify-content: center; align-items: center; border-radius: 12px; background-color: ' + colorList['chatSendBtnBackgroundColor'] + '; color: ' + colorList['chatSendBtnTextColor'] + ';  }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formArea p.formOKButtonArea span.formOKButton:hover { opacity: 0.8; }';
        html += '#sincloBox #chatTalk li.sinclo_re div.formArea p.formOKButtonArea span.formOKButton.disabled { opacity: 0.38; cursor: auto; }';

        html += '#sincloBox #chatTalk li.sinclo_se.sinclo_form { display: block; padding: 15px; line-height: 0; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formContentArea { margin: 0; line-height: 0; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formArea { background-color: #FFF; line-height: 0; padding: 10px 10px 0 10px; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formElement { color: #333; display: flex; justify-content: stretch; flex-flow: column nowrap; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formElement.withMB { margin-bottom: 8px; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formElement label.formLabel { margin-bottom: 3px; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formElement label.formLabel span.require { color: #7F0000 }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formElement input.formInput { padding: 5px; border: 1px solid ' + chatPosition.se.backgroundColor + '; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formArea p.formOKButtonArea { margin-top: 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center;  }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formArea p.formOKButtonArea span.formOKButton { width: 100px; height: 30px; cursor: pointer; display: inline-flex; justify-content: center; align-items: center; border-radius: 12px; background-color: ' + colorList['chatSendBtnBackgroundColor'] + '; color: ' + colorList['chatSendBtnTextColor'] + '; opacity:0.38;  }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formArea p.formOKButtonArea span.formOKButton.disabled { opacity: 0.38; cursor: auto; }';

        html += '#sincloBox #chatTalk li.sinclo_se.sinclo_form { display: block; padding: 10px 15px; line-height: 0; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formSubmitArea { background-color: transparent; line-height: 0; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formSubmitArea div.formElement { display: grid; display: -ms-grid; grid-template-columns: 36% max-content 1fr; -ms-grid-columns: 36% max-content 1fr; color: #333; line-height: 0; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formSubmitArea div.formElement.withMB { margin-bottom: 7px; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formSubmitArea div.formElement span { display: inline-block; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formSubmitArea div.formElement span.formLabel { grid-column: 1/2; grid-row: 1/2; -ms-grid-column: 1; -ms-grid-row: 1; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formSubmitArea div.formElement span.formLabel span.require { color: #7F0000 }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formSubmitArea div.formElement span.formLabelSeparator { margin: 0 3px; grid-column: 2/3; grid-row: 1/2; -ms-grid-column: 2; -ms-grid-row: 1; }';
        html += '#sincloBox #chatTalk li.sinclo_se div.formSubmitArea div.formElement span.formValue { grid-column: 3/4; grid-row: 1/2; -ms-grid-column: 3; -ms-grid-row: 1; }';

        /* Cogmo */
        html += '#sincloBox #chatTalk li.sinclo_re p.sincloButtonWrap { cursor: pointer; background-color: ' + chatPosition.re.color + '; text-align: center; padding: 10px; margin: 5px 0px;}';
        html += '#sincloBox #chatTalk li.sinclo_re p.sincloButtonWrap:hover { opacity: 0.8 }';
        html += '#sincloBox #chatTalk li.sinclo_re p.sincloButtonWrap span.sincloButton { color: ' + chatPosition.re.backgroundColor + '; font-size: ' + chatPosition.re.textSize + 'px;}';
        html += '#sincloBox #chatTalk li.sinclo_re.withButton { line-height: 0; }';

        html += '#sincloBox #chatTalk li.sinclo_re select {cursor: pointer;}';
        /* flatpickr カスタム値の方が強いため基本important指定 */
        html += '#sincloBox #chatTalk li.sinclo_re div.flatpickr-calendar.disable { pointer-events: none!important; opacity: 0.5!important; }';

        if ( colorList['widgetInsideBorderNone'] === 1 ) {
          html += '      #sincloBox section#chatTab sinclo-div:not(#flexBoxWrap) { border-top: none!important;}';
        }
        if ( widget.chatInitShowTextarea === 2 ) {
          html += '    #sincloBox section#chatTab sinclo-div#flexBoxWrap { display: none; }';
        }
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage, #sincloBox section#chatTab sinclo-div #miniSincloChatMessage { display: block; height: 100%; min-height: 100%!important; margin: 0; width: 80%; resize: none; color: ' + colorList['messageBoxTextColor'] + '!important; background-color: ' + colorList['messageBoxBackgroundColor'] + '; font-size: ' + widget.messageBoxTextSize + 'px;}';
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage:disabled, #sincloBox section#chatTab sinclo-div #miniSincloChatMessage:disabled { background-color: ' + colorList['messageBoxBackgroundColor'] + '; opacity: 1; }';
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage:focus, #sincloBox section#chatTab sinclo-div #miniSincloChatMessage:focus { outline: none; border-color: ' + colorList['mainColor'] + '!important }';
        if ( colorList['messageBoxBorderNone'] === 0 ) {
          html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage, #sincloBox section#chatTab sinclo-div #miniSincloChatMessage { border-right-color: ' + colorList['chatSendBtnBackgroundColor'] + '!important; }';
          html += '      #sincloBox section#chatTab sinclo-div #sincloChatMessage:focus, #sincloBox section#chatTab sinclo-div #miniSincloChatMessage:focus { border-color: ' + colorList['mainColor'] + '!important }';
        }
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatSendBtn, #sincloBox section#chatTab sinclo-div #miniSincloChatSendBtn { display: flex;justify-content:center; align-items: center; height: 100%; width: 20%; text-decoration: none; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0; text-align: center; background-color: ' + colorList['chatSendBtnBackgroundColor'] + '; color: ' + colorList['chatSendBtnTextColor'] + '; font-weight: bold;}';
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatSendBtn span, #sincloBox section#chatTab sinclo-div #miniSincloChatSendBtn span {color: ' + colorList['chatSendBtnTextColor'] + '; }';
        if ( (window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document) || (check.isset(widget.showAccessId) && widget.showAccessId === 1)) ) {
          if ( widget.chatMessageCopy === 1 ) {
            html += '      #sincloBox section#chatTab #sincloAccessInfo { height: ' + sizeList['sincloAccessInfoHeight'] + 'px; text-align: left; padding-left: 0.5em; padding-top: 5px; padding-bottom: 5px; font-size: 0.9em; border-top: 1px solid ' + colorList['widgetInsideBorderColor'] + '; user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none; }';
          }
          else {
            html += '      #sincloBox section#chatTab #sincloAccessInfo { height: ' + sizeList['sincloAccessInfoHeight'] + 'px; text-align: left; padding-left: 0.5em; padding-top: 5px; padding-bottom: 5px; font-size: 0.9em; border-top: 1px solid ' + colorList['widgetInsideBorderColor'] + ' }';
          }
          if ( colorList['widgetInsideBorderNone'] === 1 ) {
            html += '      #sincloBox section#chatTab #sincloAccessInfo { border-top: none!important; }';
          }
        }
      }
      html += '      #sincloBox section#navigation { position: relative; display: block; top: 0px!important; background: #FFF!important; }';
      html += '      #sincloBox section#navigation ul { display: table; padding: 0; position: absolute; top: 0; left: 0; }';
      if ( widget.chatMessageCopy === 1 ) {
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
      if ( Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1 ) {
        html += '      #sincloBox div#sincloBannerBox { bottom:0px; right:0px; background-color: rgb(255, 255, 255); border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px;}';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner:hover { opacity:0.75 !important}';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner .sinclo-fal { display: inline-block; font-family: SincloFont ; font-style: normal; font-weight: normal; line-height: 1; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; cursor: pointer; }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner .sinclo-fal.fa-comment:before { content: "\\f075" }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner.sincloBanner { position: relative; z-index: 1; height: 42px; width : -webkit-fit-content !important; width : -moz-fit-content !important; width : fit-content !important; background-color: ' + colorList['mainColor'] + '; box-shadow: 0px 0px ' + widget.boxShadow + 'px ' + widget.boxShadow + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px; color: ' + colorList['stringColor'] + '; margin: auto; filter:alpha(opacity=90); -moz-opacity: 0.9; opacity: 0.9; cursor: pointer; }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner .sincloBannerText{ display: flex; justify-content: center; align-items: center; height: 100%; width: auto!important; margin: 0 5px; }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner.sincloBanner i{ color: ' + widget.stringColor + '; }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner.sincloBanner .sinclo-comment{ transform: scale( 1 , 1.4 ); font-size: 17.5px; padding: 0 2px 0 10px; cursor: pointer; }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner.sincloBanner .sinclo-comment-notext{ transform: scale( 1 , 1.4 ); font-size: 17.5px; padding: 0 2px 0 13px; cursor: pointer; }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner #bannerIcon { width: 24px; height: 24px; opacity: 1; margin: 0px 5px; }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner.sincloBanner .bannertext{ color: ' + colorList['stringColor'] + '; font-size: 12.5px; cursor: pointer; vertical-align: middle; margin-right: 5px; }';
        html += '      #sincloBox div#sincloBannerBox #sincloBanner.sincloBanner .notext{ cursor: pointer; }';
      }

      html += common.injectCalendarCSS();

      /* iPhone/iPod/Androidの場合 */
      if ( check.smartphone() ) {
        // TODO 関数化
        if ( widget.spMaximizeSizeType === 2 ) {
          widgetWidth = $(window).width();
        } else {
          widgetWidth = $(window).width() - 20;
        }
        ratio = widgetWidth * (1 / 285);

        html += '#sincloBox { -webkit-transition: 100ms linear 0ms;  transition: opacity 100ms linear 0ms; }';
        html += '#sincloBox section#chatTab sinclo-div:not(#flexBoxWrap) { position: relative }';
        html += '#sincloBox section#chatTab sinclo-chat-alert { position: absolute; background-color: rgba(0,0,0,0.6); box-shadow: 0px 0px 4px 4px rgba(0,0,0,0.1); color: #FFF; text-align: center; }';
        html += '#sincloBox ul sinclo-typing { padding-bottom: ' + (30.8 * ratio) + 'px; display: block; }';
        if ( sinclo.chatApi.sendErrCatchFlg ) {
          html += '#sincloBox section#chatTab sinclo-chat-alert { display:block }';
        }
        else {
          html += '#sincloBox section#chatTab sinclo-chat-alert { display:none }';
        }

        html += '#sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.commentarea textarea { font-size: 16px; }';

        /* 縦の場合 */
        if ( this.isPortrait() ) {
          if ( widget.spMaximizeSizeType === 2 ) {
            html += '#sincloBox { width: ' + ($(window).width()) + 'px; right: 0px; left: 0px; }';
          } else {
            html += '#sincloBox { width: ' + widgetWidth + 'px; }';
          }
          html += '#sincloBox div#sincloWidgetBox { box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px 0 0;}';
          html += '#sincloBox * { font-size: ' + (12 * ratio) + 'px; }';
          html += '#sincloBox section { width: ' + widgetWidth + 'px }';
          html += '#sincloBox section#navigation ul { width: ' + widgetWidth + 'px }';
          html += '#sincloBox span#mainImage { top: ' + (7 * ratio) + 'px; left: ' + (8 * ratio) + 'px; }';
          html += '#sincloBox sinclo-div#widgetHeader:after { top: ' + (35 * ratio) + 'px }';
          html += '#sincloBox p#widgetTitle { border-radius: ' + (widget.radiusRatio * ratio) + 'px ' + (widget.radiusRatio * ratio) + 'px 0 0; border: ' + (1 * ratio) + 'px solid ' + colorList['mainColor'] + '; font-size: ' + (14 * ratio) + 'px; padding: ' + (7 * ratio) + 'px ' + (30 * ratio) + 'px; height: ' + (32 * ratio) + 'px; }';
          if ( widget.widgetSizeType !== 1 ) {
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
          else {
            //閉じるボタン有効
            /*
            html += '#sincloBox div#addBtn { display: none; top: ' + (6 * ratio) + 'px; right: ' + (25 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (20 * ratio) + 'px; height: ' + (20 * ratio) + 'px; z-index: 2; }';
*/
            html += '#sincloBox div#closeBtn { display: none; top: ' + (7 * ratio) + 'px; right: ' + (10 * ratio) + 'px; bottom: ' + (6 * ratio) + 'px; width: ' + (18 * ratio) + 'px; height: ' + (18 * ratio) + 'px; z-index: 2; }';
          }
          html += '      #sincloBox span#mainImage i {display: flex; justify-content: center; align-items: center; width: 80px; height: 70px; font-size: calc(43px * ((3 * 15 + 36) / 81)); border: 1px solid; }';
          html += '      #sincloBox p#widgetTitle { border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px 0 0; font-size: ' + (14 * ratio) + 'px; padding: ' + (7 * ratio) + 'px 0px ' + (7 * ratio) + 'px 0px; height: ' + (32 * ratio) + 'px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}';
          html += '      #sincloBox p#widgetTitle.leftPosition { text-align: left; padding-left: ' + (70 * ratio) + 'px;}';
          html += '      #sincloBox p#widgetTitle.leftPosition.noImage { padding-left: ' + (15 * ratio) + 'px;}';
          html += '      #sincloBox p#widgetTitle.centerPosition { padding-right: ' + (30 * ratio) + 'px; padding-left: calc(2.5em + 38px);}';
          html += '      #sincloBox p#widgetTitle.centerPosition.noImage { padding-left: 0px; padding-right: 0px; }';

          html += '      #sincloBox p#widgetSubTitle { background-color: ' + colorList['headerBackgroundColor'] + '; border-color: ' + colorList['widgetBorderColor'] + '; font-weight: bold; color: ' + colorList['subTitleTextColor'] + '; border-style: solid; text-align: left; margin: 0; padding: ' + (7 * ratio) + 'px 0; border-width: 0 ' + (1 * ratio) + 'px 0 ' + (1 * ratio) + 'px; padding-left: ' + (74 * ratio) + 'px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}';
          html += '      #sincloBox p#widgetSubTitle.leftPosition { padding-left: ' + (70 * ratio) + 'px;}';
          html += '      #sincloBox p#widgetSubTitle.leftPosition.noImage { padding-left: ' + (15 * ratio) + 'px;}';
          html += '      #sincloBox p#widgetSubTitle.centerPosition { text-align: center; padding-right: ' + (30 * ratio) + 'px; padding-left: calc(2.5em + 38px * ' + ratio + ');}';
          html += '      #sincloBox p#widgetSubTitle.centerPosition.noImage { padding-left: 0px; padding-right: 0px;}';
          html += '      #sincloBox p#widgetSubTitle.oneContent { line-height: calc((1em + 9px)*2); padding-bottom: ' + (3.5 * ratio) + 'px; padding-top: ' + (4.5 * ratio) + 'px; border-bottom: 1px ' + colorList['widgetBorderColor'] + ' solid;}';
          if ( colorList['widgetInsideBorderNone'] === 1 ) {
            html += '      #sincloBox p#widgetSubTitle.oneContent{ border:none;}';
          }

          html += '      #sincloBox p#widgetDescription.leftPosition { padding-left: ' + (70 * ratio) + 'px;}';
          html += '      #sincloBox p#widgetDescription.leftPosition.noImage { padding-left: ' + (15 * ratio) + 'px;}';
          html += '      #sincloBox p#widgetDescription.centerPosition { text-align: center; padding-right: ' + (30 * ratio) + 'px; padding-left: calc(2.5em + 38px * ' + ratio + ');}';
          html += '      #sincloBox p#widgetDescription.centerPosition.noImage { padding-left: 0px; padding-right: 0px;}';
          html += '      #sincloBox p#widgetDescription.oneContent { line-height: calc((1em + 9px)*2); padding-bottom: ' + (3.5 * ratio) + 'px; padding-top: ' + (4.5 * ratio) + 'px;}';
          if ( widget.widgetSizeType !== 1 ) {
            html += '#sincloBox p#widgetSubTitle { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }';
          }
          if ( colorList['widgetBorderNone'] === 1 ) {
            html += '#sincloBox p#widgetSubTitle { border:none; }';
          }
          html += '#sincloBox p#widgetDescription { background-color: ' + colorList['headerBackgroundColor'] + '; margin: 0; padding-bottom: ' + (7 * ratio) + 'px; border-width: 0 ' + (1 * ratio) + 'px ' + (1 * ratio) + 'px ' + (1 * ratio) + 'px; padding-left: ' + (74 * ratio) + 'px; text-align: left; border-color: ' + colorList['widgetBorderColor'] + '; border-style: solid; color: ' + colorList['descriptionTextColor'] + '; border-bottom-color:' + colorList['widgetInsideBorderColor'] + '; }';
          if ( widget.widgetSizeType !== 1 ) {
            html += '#sincloBox p#widgetDescription { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }';
          }
          if ( colorList['widgetBorderNone'] === 1 ) {
            html += '#sincloBox p#widgetDescription { border-left:none; border-right:none;}';
          }
          html += '#sincloBox section { border: ' + (1 * ratio) + 'px solid ' + colorList['widgetBorderColor'] + '; border-top: none; border-bottom: ' + (1 * ratio) + 'px solid ' + colorList['widgetInsideBorderColor'] + ';}';
          if ( colorList['widgetBorderNone'] === 1 ) {
            html += '#sincloBox section { border-top: none; border-left:none; border-right:none; }'
          }
          if ( colorList['widgetInsideBorderNone'] === 1 ) {
            html += '#sincloBox p#widgetDescription { border-bottom:none!important;}';
            html += '#sincloBox section { border-bottom:none!important;}';
          }
          // 画像がセットされている場合のスタイル
          html += '#sincloBox p#widgetTitle.notSimple { padding-left: ' + (70 * ratio) + 'px; }';
          // 画像がセットされていない場合のスタイル
          html += '#sincloBox p#widgetTitle.noImage { padding-left: ' + (30 * ratio) + 'px; }';
          html += '#sincloBox #mainImage em { top: -' + (10 * ratio) + 'px; right: -' + (10 * ratio) + 'px; width: ' + (25 * ratio) + 'px; height: ' + (20 * ratio) + 'px; font-size: ' + (11 * ratio) + 'px; padding: ' + (1 * ratio) + 'px; }';
          if ( widget.spMaximizeSizeType === 2 ) {
            html += '#sincloBox ul#chatTalk { padding: ' + (5 * ratio) + 'px; padding-top:0px; height: ' + (194 * ratio) + 'px; background-color: ' + colorList['chatTalkBackgroundColor'] + ' }';
          } else {
            html += '#sincloBox ul#chatTalk { padding: ' + (5 * ratio) + 'px; padding-top:0px; height: ' + (194 * ratio) + 'px; background-color: ' + colorList['chatTalkBackgroundColor'] + ' }';
          }
          html += '#sincloBox ul#chatTalk li { border-radius: ' + (12 * ratio) + 'px; margin: ' + (5 * ratio) + 'px 0; padding: ' + (10 * ratio) + 'px ' + (10 * ratio) + 'px; font-size: ' + (12 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk li.sinclo_se { font-size: ' + (12 * ratio) + 'px; margin-left: ' + (widgetWidth / 342.5) * 45 + 'px;}';
          html += '#sincloBox ul#chatTalk li.sinclo_re { font-size: ' + (12 * ratio) + 'px; margin-right: ' + (widgetWidth / 342.5) * 21 + 'px;}';
          html += '#sincloBox ul#chatTalk li.sinclo_se span.sinclo-text-line { font-size: ' + (12 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk li.sinclo_re span.sinclo-text-line { font-size: ' + (12 * ratio) + 'px; }';
          html += '#sincloBox ul#chatTalk li div.sendFileThumbnailArea { display: table-cell; width: ' + (64 * ratio) + 'px; height: ' + (64 * ratio) + 'px; border: 1px solid #D9D9D9; }';
          if(colorList['seBorderNone'] === 0){
            html += '#sincloBox ul#chatTalk li.sinclo_se { border: ' + (1 * ratio) + 'px solid '+ chatPosition.se.borderColor +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '#sincloBox ul#chatTalk li.sinclo_re { border: ' + (1 * ratio) + 'px solid '+ chatPosition.re.borderColor +'; }';
          }
          html += '#sincloBox ul#chatTalk li sinclo-radio { margin-top: ' + (Number(widget.btwButtonMargin) * ratio) + 'px; display: inline-block; } ';
          html += '#sincloBox ul#chatTalk li span.sinclo-text-line + sinclo-radio { margin-top: ' + (Number(widget.lineButtonMargin) * ratio) + 'px; } ';
          if ( widget.chatMessageDesignType === 2 ) {
            // 吹き出し型
            html += '#sincloBox ul#chatTalk li { line-height: 1.4; padding: ' + (10 * ratio) + 'px ' + (15 * ratio) + 'px !important; border-radius: ' + (12 * ratio) + 'px !important;}';
          }
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] { margin-right: 0.5em } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio * { webkit-transform: scale(' + (1 * ratio) + '); transform: scale(' + (1 * ratio) + '); moz-transform: scale(' + (1 * ratio) + '); } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label { display: inline; padding-left: 1.5em; font-size: ' + (12 * ratio) + 'px; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: ' + (11 * ratio) / 2 + 'px; margin-top: -' + (11 * ratio) / 2 + 'px; left: ' + (0 * ratio) + 'px; width: ' + (11 * ratio) + 'px; height: ' + (11 * ratio) + 'px; border: 0.5px solid ' + chatContentTextColor + '; border-radius: 50%; background-color: #FFF; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: ' + (11 * ratio) / 2 + 'px; left: ' + (11 * ratio) / 4 + 'px; margin-top: -' + (11 * ratio) / 4 + 'px; width: ' + Math.ceil(((11 * ratio) / 2)) + 'px; height: ' + Math.ceil(((11 * ratio) / 2)) + 'px; background: ' + colorList['mainColor'] + '; border-radius: 50%; } ';
          html += '#sincloBox ul#chatTalk li span.cName { font-size: ' + (13 * ratio) + 'px; margin: 0 0 ' + (5 * ratio) + 'px 0; }';
          if(colorList['seBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_se:after {border-left: 5px solid '+ chatPosition.se.borderColor +'; border-bottom: 5px solid '+ chatPosition.se.borderColor +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_re:after {border-right: 5px solid '+ chatPosition.re.borderColor +'; border-bottom: 5px solid '+ chatPosition.re.borderColor +'; }';
          }
          html += '#sincloBox ul#chatTalk sinclo-chat-receiver #receiveMessage { font-size: ' + (10 * ratio) + 'px; }';
          html += '#sincloBox section#chatTab sinclo-div:not(#flexBoxWrap) { height: ' + (60 * ratio) + 'px; padding: ' + (5 * ratio) + 'px; }';
          if ( colorList['widgetInsideBorderNone'] === 0 ) {
            html += '#sincloBox section#chatTab sinclo-div:not(#flexBoxWrap) { border-top: ' + (1 * ratio) + 'px solid ' + colorList['widgetInsideBorderColor'] + '; }';
          }
          html += '#sincloBox section#chatTab #sincloChatMessage, #sincloBox section#chatTab #miniSincloChatMessage { font-size: 17px! important; padding: ' + (5 * ratio) + 'px;  }';
          if ( colorList['messageBoxBorderNone'] === 0 ) {
            html += '#sincloBox section#chatTab #sincloChatMessage, #sincloBox section#chatTab #miniSincloChatMessage { border-radius: ' + (5 * ratio) + 'px 0 0 ' + (5 * ratio) + 'px!important; border: ' + (1 * ratio) + 'px solid ' + colorList['messageBoxBorderColor'] + '!important; }';
          }
          else {
            html += '#sincloBox section#chatTab #sincloChatMessage, #sincloBox section#chatTab #miniSincloChatMessage { border: none!important; }';
          }

          html += '      #sincloBox ul#chatTalk li.sinclo_etc { border: none; text-align: center!important; margin: 0 auto; font-weight: bold; font-size:14px); }';

          // 一括ヒアリング
          html += '#sincloBox #chatTalk li.sinclo_re.sinclo_form { padding: ' + (10 * ratio) + 'px ' + (15 * ratio) + 'px ' + (15 * ratio) + 'px ' + (15 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_re div.formArea { padding: ' + (10 * ratio) + 'px ' + (10 * ratio) + 'px 0 ' + (10 * ratio) + 'px; margin-top: ' + (10 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_re div.formElement.withMB { margin-bottom: ' + (8 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_re div.formElement label.formLabel { margin-bottom: ' + (3 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_re div.formElement input.formInput { padding: ' + (5 * ratio) + 'px; border: ' + (1 * ratio) + 'px solid ' + chatPosition.re.backgroundColor + '; }';
          html += '#sincloBox #chatTalk li.sinclo_re div.formArea p.formOKButtonArea { margin-top: ' + (20 * ratio) + 'px; margin-bottom: ' + (20 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_re div.formArea p.formOKButtonArea span.formOKButton { width: ' + (100 * ratio) + 'px; height: ' + (30 * ratio) + 'px; border-radius: ' + (12 * ratio) + 'px; }';

          html += '#sincloBox #chatTalk li.sinclo_se.sinclo_form { padding: ' + (15 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_se div.formContentArea { margin: 0; line-height: 0; }';
          html += '#sincloBox #chatTalk li.sinclo_se div.formArea { padding: ' + (10 * ratio) + 'px ' + (10 * ratio) + 'px 0 ' + (10 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_se div.formElement.withMB { margin-bottom: ' + (8 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_se div.formElement label.formLabel { margin-bottom: ' + (3 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_se div.formElement input.formInput { padding: ' + (5 * ratio) + 'px; border: ' + (1 * ratio) + 'px solid ' + chatPosition.se.backgroundColor + '; }';
          html += '#sincloBox #chatTalk li.sinclo_se div.formArea p.formOKButtonArea { margin-top: ' + (20 * ratio) + 'px; margin-bottom: ' + (20 * ratio) + 'px; }';
          html += '#sincloBox #chatTalk li.sinclo_se div.formArea p.formOKButtonArea span.formOKButton.disabled { opacity: 0.38; }';

          html += '#sincloBox section#chatTab sinclo-div#miniFlexBoxHeight { height: ' + (48 * ratio) + 'px;  }';
          html += '#sincloBox section#chatTab #sincloChatSendBtn, #sincloBox section#chatTab #miniSincloChatSendBtn { padding:  ' + (16.5 * ratio) + 'px 0; border: ' + ratio + 'px solid ' + colorList['chatSendBtnBackgroundColor'] + '; }';
          html += '#sincloBox section#chatTab a#miniSincloChatSendBtn { padding: ' + (8 * ratio) + 'px 0;  }';
          html += '#sincloBox section#chatTab sinclo-div #sincloChatSendBtn, #sincloBox section#chatTab sinclo-div #miniSincloChatSendBtn { font-size: ' + (13 * ratio) + 'px;}';
          html += '#sincloBox section#chatTab sinclo-chat-alert { left: 10%; right: 10%; bottom: 50%; border-radius: ' + (5 * ratio) + 'px; color: #FFF; padding: ' + (10 * ratio) + 'px 0; }';
          html += '#sincloBox section#navigation { border-width: 0 ' + (1 * ratio) + 'px; height: ' + (40 * ratio) + 'px; }';
          html += '#sincloBox section#navigation ul { margin: 0 0 0 -' + (1 * ratio) + 'px; height: ' + (40 * ratio) + 'px; }';
          html += '#sincloBox section#navigation ul li { padding: ' + (10 * ratio) + 'px 0; border-left: ' + (1 * ratio) + 'px solid #E8E7E0; height: ' + (40 * ratio) + 'px;  }';
          html += '#sincloBox section#navigation ul li:last-child { border-right: ' + (1 * ratio) + 'px solid ' + colorList['widgetBorderColor'] + '; }';
          if ( colorList['widgetBorderNone'] === 1 ) {
            html += '#sincloBox section#navigation ul li:last-child { border-right:none; }';
          }
          html += '#sincloBox section#navigation ul li:not(.selected) { border-bottom: ' + (1 * ratio) + 'px solid ' + colorList['widgetInsideBorderColor'] + '; }';
          if ( colorList['widgetInsideBorderNone'] === 1 ) {
            html += '#sincloBox section#navigation ul li:not(.selected) { border-bottom: none!important;}';
          }
          html += '#sincloBox section#navigation ul li.selected::after { border-bottom: ' + (2 * ratio) + 'px solid ' + colorList['mainColor'] + '; left: ' + (5 * ratio) + 'px; }';
          html += '#sincloBox #fotter { padding: ' + (5 * ratio) + 'px 0; border: ' + (1 * ratio) + 'px solid ' + colorList['widgetBorderColor'] + '; font-size: ' + (11 * ratio) + 'px; border-top: none;}';
          if ( colorList['widgetBorderNone'] === 1 ) {
            html += '#sincloBox #fotter { border:none; }';
          }
          html += '#sincloBox section#navigation ul li::before { margin-right: ' + (5 * ratio) + 'px; width: ' + (18 * ratio) + 'px; height: ' + (18 * ratio) + 'px; }';
          /* Cogmo */
          html += '#sincloBox #chatTalk li.sinclo_re p.sincloButtonWrap { padding: ' + (10 * ratio) + 'px; border-radius: ' + (12 * ratio) + 'px; margin: ' + (5 * ratio) + 'px 0px; }';
          html += '#sincloBox #chatTalk li.sinclo_re p.sincloButtonWrap:hover { opacity: 0.8 }';
          html += '#sincloBox #chatTalk li.sinclo_re p.sincloButtonWrap span.sincloButton { font-size: ' + (12 * ratio) + 'px;}';
          //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
          if ( Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1 ) {
            html += '      #sincloBox div#sincloBannerBox { bottom:0px; right:0px; background: initial;}';
            if ( window.sincloInfo.widget.spBannerPosition ) {
              //スマホ隠しパラメータの存在チェック
              var horizontalPosition = (5 * ratio) + "px";
              if ( typeof(widget.spBannerHorizontalPosition) === "string" && widget.spBannerHorizontalPosition.indexOf("px") !== -1 ) {
                horizontalPosition = parseInt(widget.spBannerHorizontalPosition) * ratio + "px";
              } else if ( typeof(widget.spBannerHorizontal) === "string" ) {
                horizontalPosition = widget.spBannerHorizontalPosition;
              }
              var verticalPositionFromBottom = (5 * ratio) + "px";
              if ( typeof(widget.spBannerVerticalPositionFromBottom) === "string" && widget.spBannerVerticalPositionFromBottom.indexOf("px") !== -1 ) {
                verticalPositionFromBottom = parseInt(widget.spBannerVerticalPositionFromBottom) * ratio + "px";
              } else if ( typeof(widget.spBannerVerticalPositionFromBottom) === "string" ) {
                verticalPositionFromBottom = widget.spBannerVerticalPositionFromBottom;
              }
              switch ( Number(window.sincloInfo.widget.spBannerPosition) ) {
                case 1://右下
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; right: ' + horizontalPosition + '; bottom: ' + verticalPositionFromBottom + '; height: ' + (30 * ratio) + 'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .bannertext{ color: ' + widget.stringColor + '; font-size: ' + (10 * ratio) + 'px; vertical-align: middle; margin-right: ' + (5 * ratio) + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner div#sincloBannerText #bannerIcon { width: ' + Math.ceil(21 * ratio) + 'px; height: ' + Math.ceil(21 * ratio) + 'px; opacity: 1; margin: 0px ' + (5 * ratio) + 'px; }';
                  break;
                case 2://左下
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; left: ' + horizontalPosition + '; bottom: ' + verticalPositionFromBottom + '; height: ' + (30 * ratio) + 'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .bannertext{ color: ' + widget.stringColor + '; font-size: ' + (10 * ratio) + 'px; vertical-align: middle; margin-right: ' + (5 * ratio) + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner div#sincloBannerText #bannerIcon { width: ' + Math.ceil(21 * ratio) + 'px; height: ' + Math.ceil(21 * ratio) + 'px; opacity: 1; margin: 0px ' + (5 * ratio) + 'px; }';
                  break;
                case 3://右中央
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; right:0; top: ' + widget.spBannerVerticalPositionFromTop + '; transform: translateY(-50%); height: auto; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius:' + widget.radiusRatio * ratio + 'px 0 0 ' + widget.radiusRatio * ratio + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .bannertext { margin-left: 0px; margin-right: 0; color: ' + widget.stringColor + '; font-size: ' + (10 * ratio) + 'px; vertical-align: middle; -webkit-writing-mode: vertical-rl; -ms-writing-mode: tb-rl; writing-mode: vertical-rl; letter-spacing: 1px;}';
                  html += '      #sincloBox div#sincloBannerBox #sincloBanner .sincloBannerText{ flex-flow: column nowrap; margin: ' + Math.ceil(10 * ratio) + 'px ' + Math.ceil(5 * ratio) + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner div#sincloBannerText #bannerIcon { width: ' + Math.ceil(10 * ratio) + 'px; height: ' + Math.ceil(10 * ratio) + 'px; opacity: 1; margin-bottom: 5px; }';
                  break;
                case 4://左中央
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; left:0; top: ' + widget.spBannerVerticalPositionFromTop + '; transform: translateY(-50%); height: auto; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius:0 ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px 0; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .bannertext { margin-right: 0px; margin-left: 0; color: ' + widget.stringColor + '; font-size: ' + (10 * ratio) + 'px; vertical-align: middle; -webkit-writing-mode: vertical-rl; -ms-writing-mode: tb-rl; writing-mode: vertical-rl; letter-spacing: 1px;}';
                  html += '      #sincloBox div#sincloBannerBox #sincloBanner .sincloBannerText{ flex-flow: column nowrap; margin: ' + Math.ceil(10 * ratio) + 'px ' + Math.ceil(5 * ratio) + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner div#sincloBannerText #bannerIcon { width: ' + Math.ceil(10 * ratio) + 'px; height: ' + Math.ceil(10 * ratio) + 'px; opacity: 1; margin-bottom: 5px; }';
                  break;
                default://右下
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; right: ' + (5 * ratio) + 'px; bottom: ' + (5 * ratio) + 'px; height: ' + (30 * ratio) + 'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .bannertext{ color: ' + widget.stringColor + '; font-size: ' + (10 * ratio) + 'px; vertical-align: middle; margin-right: ' + (5 * ratio) + 'px; }';
                  html += '      #sincloBox div#sincloBannerBox div#sincloBanner div#sincloBannerText #bannerIcon { width: ' + Math.ceil(21 * ratio) + 'px; height: ' + Math.ceil(21 * ratio) + 'px; opacity: 1; margin: 0px ' + (5 * ratio) + 'px; }';
                  break;
              }
            } else {
              //スマホ隠しパラメータが存在しない場合
              if ( Number(window.sincloInfo.widget.showPosition) === 1 ) {
                //右下
                html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; right: ' + (5 * ratio) + 'px; bottom: ' + (5 * ratio) + 'px; height: ' + (30 * ratio) + 'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
              } else {
                //左下
                html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; left: ' + (5 * ratio) + 'px; bottom: ' + (5 * ratio) + 'px; height: ' + (30 * ratio) + 'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
              }
            }
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .sinclo-comment{ font-size: ' + (17.5 * ratio) + 'px; padding: 0 ' + (2 * ratio) + 'px 0 ' + (10 * ratio) + 'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .sinclo-comment-notext{ font-size: ' + (17.5 * ratio) + 'px; padding: 0 ' + (2 * ratio) + 'px 0 ' + (13 * ratio) + 'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .notext{ cursor: pointer; }';
          }
        }
        /* 横の場合 */
        else {
          var chatAreaHeight = window.innerHeight * (document.body.clientWidth / window.innerWidth);
          var hRatio = chatAreaHeight * 0.07;
          html += '#sincloBox { left:0; right:0; bottom: 0; }';
          html += '#sincloBox div#sincloWidgetBox { box-shadow: 0px 0px ' + widget.boxShadow + 'px ' + widget.boxShadow + 'px rgba(0,0,0,0.1);}';
          html += '#sincloBox * { font-size: ' + hRatio + 'px }';
          if ( widget.chatMessageCopy === 1 ) {
            html += '#sincloBox p#widgetTitle { border-radius: 0; border-top-width: 0.1em; height: 2em; padding: 0.35em 2em 0; font-size: 1.2em;  user-select: none; -moz-user-select: none; -webkit-user-select: none; -ms-user-select: none; }';
          }
          else {
            html += '#sincloBox p#widgetTitle { border-radius: 0; border-top-width: 0.1em; height: 2em; padding: 0.35em 2em 0; font-size: 1.2em;}';
          }
          if ( widget.widgetSizeType !== 1 ) {
            html += '#sincloBox p#widgetTitle { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }';
          }
          html += '#sincloBox section { width: 100% }';
          html += '#sincloBox section#chatTab ul { height: ' + (chatAreaHeight - (6.5 * hRatio)) + 'px }';
          html += '#sincloBox div#minimizeBtn { width: 1.5em; height: 1.5em; top: 0.4em; bottom: 0; right: 0.7em; }';
          html += '#sincloBox section#chatTab sinclo-div #sincloChatSendBtn, #sincloBox section#chatTab sinclo-div #miniSincloChatSendBtn { font-size: ' + (13 * ratio) + 'px;}';
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
          html += '#sincloBox ul#chatTalk { padding: 0.3em; padding-top:0px; background-color: ' + colorList['chatTalkBackgroundColor'] + '}';
          html += '#sincloBox ul#chatTalk li { font-size: 0.8em; border-radius: 0.72em; margin: 0.3em 0; padding: ' + (10 * (ratio / 2)) + 'px ' + (15 * (ratio / 2)) + 'px; }';
          html += '#sincloBox ul#chatTalk li div.sendFileThumbnailArea { display: table-cell; width: ' + (64 * ratio) + 'px; height: ' + (64 * ratio) + 'px; border: 1px solid #D9D9D9; }';
          html += '#sincloBox ul#chatTalk li.sinclo_se { font-size: 0.8em; margin-left: ' + (widgetWidth / 342.5) * 45 + 'px;}';
          html += '#sincloBox ul#chatTalk li.sinclo_re { font-size: 0.8em; margin-right: ' + (widgetWidth / 342.5) * 21 + 'px;}';
          html += '#sincloBox ul#chatTalk li.sinclo_se span.sinclo-text-line { font-size: 0.8em; }';
          html += '#sincloBox ul#chatTalk li.sinclo_re span.sinclo-text-line { font-size: 0.8em; }';
          if(colorList['seBorderNone'] === 0){
            html += '#sincloBox ul#chatTalk li.sinclo_se { border: ' + (1 * ratio) + 'px solid '+ chatPosition.se.borderColor +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '#sincloBox ul#chatTalk li.sinclo_re { border: ' + (1 * ratio) + 'px solid '+ chatPosition.re.borderColor +'; }';
          }
          html += '#sincloBox ul#chatTalk li.sinclo_etc { border: none; text-align: center!important; margin: 0 auto; font-weight: bold; font-size:14px); }';

          html += '#sincloBox ul#chatTalk li sinclo-radio { margin: 0 0 -1em 0.5em; display: inline-block; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] { margin-right: 0.5em } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"], #sincloBox ul#chatTalk li sinclo-radio label { webkit-transform: scale(1.3); transform: scale(1.3); moz-transform: scale(1.3); } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label { display: inline; padding-left: 1em; font-size: 0.8em; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: 10px; margin-top: -10px; left: -5px; width: 20px; height: 20px; border: 0.5px solid ' + chatContentTextColor + '; border-radius: 50%; } ';
          html += '#sincloBox ul#chatTalk li sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: 10px; left: 0px; margin-top: -5px; width: 11px; height: 11px; background: ' + colorList['mainColor'] + '; border-radius: 50%; } ';
          html += '#sincloBox ul#chatTalk li label, #sincloBox ul#chatTalk li span, #sincloBox ul#chatTalk li a { font-size: 1em; }';
          html += '#sincloBox ul#chatTalk li span.cName { margin: 0 0 0.3em 0 }';
          if ( widget.chatMessageDesignType === 2 ) {
            // 吹き出し型
            // html += '#sincloBox ul#chatTalk li.sinclo_se:before { bottom: 0.35em; left: calc(100% - 3px); }';
            // html += '#sincloBox ul#chatTalk li.sinclo_se:after { bottom: 0.4em; }';
            // html += '#sincloBox ul#chatTalk li.sinclo_re:before { bottom: 0.35em; left: -18px; }';
            // html += '#sincloBox ul#chatTalk li.sinclo_re:after { bottom: 0.4em; }';
          }
          if(colorList['seBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_se:after {border-left: 5px solid '+ chatPosition.se.borderColor +'; border-bottom: 5px solid '+ chatPosition.se.borderColor +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_re:after {border-right: 5px solid '+ chatPosition.re.borderColor +'; border-bottom: 5px solid '+ chatPosition.re.borderColor +'; }';
          }
          html += '#sincloBox ul#chatTalk sinclo-chat-receiver #receiveMessage { font-size: 0.8em; }';
          html += '#sincloBox section#chatTab sinclo-div:not(#flexBoxWrap) { height: 4em; padding: 0.5em; }';

          if ( hRatio > 16 ) {
            html += '#sincloBox #sincloChatMessage, #sincloBox #miniSincloChatMessage { height: 100%;  min-height: 100%!important; border-radius: 5px 0 0 5px!important; }';
          }
          else {
            html += '#sincloBox #sincloChatMessage, #sincloBox #miniSincloChatMessage { height: 100%;  min-height: 100%!important; border-radius: 5px 0 0 5px!important; font-size: 1.5em }';
          }
          html += '      #sincloBox section#chatTab #sincloChatSendBtn, #sincloBox section#chatTab #miniSincloChatSendBtn { padding: 0.6em 0; border: 1px solid ' + colorList['chatSendBtnBackgroundColor'] + '; }';
          html += '      #sincloBox section#chatTab sinclo-chat-alert { left: 10%; right: 10%; bottom: 50%; border-radius: 5px; color: #FFF; text-align: center; padding: 0.25em 0; }';
          if ( colorList['messageBoxBorderNone'] === 0 ) {
            html += '#sincloBox section#chatTab #sincloChatMessage, #sincloBox section#chatTab #miniSincloChatMessage { border-radius: ' + (5 * ratio) + 'px 0 0 ' + (5 * ratio) + 'px!important; border: ' + (1 * ratio) + 'px solid ' + colorList['messageBoxBorderColor'] + '!important; }';
          }
          else {
            html += '#sincloBox section#chatTab #sincloChatMessage, #sincloBox section#chatTab #miniSincloChatMessage { border: none!important; }';
          }
          html += '#sincloBox section#navigation ul { width: 100% }';
          html += 'sinclo span#mainImage, sinclo #widgetSubTitle, sinclo #widgetDescription, sinclo #navigation, sinclo #navigation * { display:none!important; height: 0!important }';
          html += '#sincloBox #fotter { display: none; height: 0!important }';
          //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
          /*
          if(Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1){
            html += '      #sincloBox div#sincloBannerBox { bottom:0px; right:0px; background: initial;}';
            if(Number(window.sincloInfo.widget.showPosition) === 1) {
              html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; right: ' + (5 * ratio) + 'px; bottom: ' + (5 * ratio) + 'px; height: '+ (18.75 * ratio) +'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
            } else {
              html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; left: ' + (5 * ratio) + 'px; bottom: ' + (5 * ratio) + 'px; height: '+ (18.75 * ratio) +'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
            }
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .sinclo-comment{ font-size: '+ (10.9375 * ratio) +'px; padding: 0 '+ (1.25 * ratio) +'px 0 '+ (6.25 * ratio) +'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .sinclo-comment-notext{ font-size: ' + (10.9375 * ratio) + 'px; padding: 0 '+ (1.15 * ratio) +'px 0 '+ (8.125 * ratio) +'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner #bannerIcon { width: ' + Math.ceil(13.125 * ratio) + 'px; height: ' + Math.ceil(13.125 * ratio) + 'px; opacity: 1; margin: 0px ' + (3.125 * ratio) + 'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .bannertext{ color: '+ widget.stringColor +'; font-size: '+ (6.25 * ratio) +'px; vertical-align: middle; margin-right: ' + (3.125 * ratio) + 'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .notext{ cursor: pointer; }';
          }
          */
          //閉じるボタン設定が有効かつバナー表示設定になっているかどうか
          if ( Number(widget.closeButtonSetting) === 2 && Number(widget.closeButtonModeType) === 1 ) {
            ratio = 1.263157894736842;
            html += '      #sincloBox div#sincloBannerBox { bottom:0px; right:0px; background: initial;}';
            if ( Number(window.sincloInfo.widget.showPosition) === 1 ) {
              html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; right: ' + (5 * ratio) + 'px; bottom: ' + (5 * ratio) + 'px; height: ' + (30 * ratio) + 'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
            } else {
              html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner { position: fixed; left: ' + (5 * ratio) + 'px; bottom: ' + (5 * ratio) + 'px; height: ' + (30 * ratio) + 'px; box-shadow: 0px 0px ' + widget.boxShadow * ratio + 'px ' + widget.boxShadow * ratio + 'px rgba(0,0,0,0.1); border-radius: ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px ' + widget.radiusRatio * ratio + 'px; }';
            }
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .sinclo-comment{ font-size: ' + (17.5 * ratio) + 'px; padding: 0 ' + (2 * ratio) + 'px 0 ' + (10 * ratio) + 'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .sinclo-comment-notext{ font-size: ' + (17.5 * ratio) + 'px; padding: 0 ' + (2 * ratio) + 'px 0 ' + (13 * ratio) + 'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner #bannerIcon { width: ' + Math.ceil(21 * ratio) + 'px; height: ' + Math.ceil(21 * ratio) + 'px; opacity: 1; margin: 0px ' + (5 * ratio) + 'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .bannertext{ color: ' + widget.stringColor + '; font-size: ' + (10 * ratio) + 'px; vertical-align: middle; margin-right: ' + (5 * ratio) + 'px; }';
            html += '      #sincloBox div#sincloBannerBox div#sincloBanner.sincloBanner .notext{ cursor: pointer; }';
          }
        }
      }
      /* PC版 */
      else {
//        html += "      #sincloBox { width: " + widgetWidth + "px }";
        html += "      #sincloBox { overflow: hidden; }";
        html += "      #sincloBox div#sincloWidgetBox { width: " + sizeList['boxWidth'] + "px; box-shadow: 0px 0px " + widget.boxShadow + "px " + widget.boxShadow + "px rgba(0,0,0,0.1); border-radius: " + widget.radiusRatio + "px " + widget.radiusRatio + "px 0 0; background-color: rgb(255, 255, 255);}";
        html += '      #sincloBox * { line-height: 1.4; font-size: ' + sizeList['d12font'] + 'px; }';
        html += '      #sincloBox a:hover { color: ' + colorList['mainColor'] + '; }';

        html += '      #sincloBox sinclo-div#widgetHeader:hover { opacity: 0.75; }';
        html += '      #sincloBox sinclo-div#widgetHeader:after { top: 32px }';
//        html += "      #sincloBox section { width: " + widgetWidth + "px }";
//        html += "      #sincloBox section#navigation ul { width: " + widgetWidth + "px }";
        html += "      #sincloBox section { width: " + sizeList['boxWidth'] + "px }";
        html += "      #sincloBox section#navigation ul { width: " + sizeList['boxWidth'] + "px }";
        html += '      #sincloBox span#mainImage { top: 7px; left: 8px }';
        html += '      #sincloBox span#mainImage i {display: flex; justify-content: center; align-items: center; width: 80px; height: 70px; font-size: calc(43px * ((3 * ' + widget.headerTextSize + ' + 36) / 81)); border: 1px solid; }';

        html += '      #sincloBox p#widgetTitle { border-radius: ' + widget.radiusRatio + 'px ' + widget.radiusRatio + 'px 0 0; font-size: ' + widget.headerTextSize + 'px; padding: 7px 0px 7px 0px; height: auto; line-height: ' + widget.headerTextSize + 'px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}';
        html += '      #sincloBox p#widgetTitle.leftPosition { text-align: left; padding-left: calc(2.5em + 41px);}';
        html += '      #sincloBox p#widgetTitle.leftPosition.noImage { padding-left: 15px;}';
        html += '      #sincloBox p#widgetTitle.centerPosition { padding-right: 25px; padding-left: calc(2.5em + 33px);}';
        html += '      #sincloBox p#widgetTitle.centerPosition.noImage { padding-left: 0px; padding-right: 0px; }';
        html += '      #sincloBox p#widgetTitle #sincloChatUnread { width: 25px; height: 25px; font-size: ' + (widget.headerTextSize - 1) + 'px; border-radius: 15px; margin: 2.5px 6px; padding: 3px; }';
        html += '      #sincloBox p#widgetTitle:after { background-position-y: 3px; top: ' + sizeList['widgetTitleTop'] + 'px; right: 10px; bottom: 6px; width: 20px; height: 20px; }';

        html += '      #sincloBox p#widgetSubTitle { background-color: ' + colorList['headerBackgroundColor'] + '; margin: 0; text-align: left; border-width: 0 1px 0 1px; padding-top: 3px; padding-bottom: 3px; border-color: ' + colorList['widgetBorderColor'] + '; border-style: solid; font-weight: bold; color: ' + colorList['subTitleTextColor'] + '; height: auto; line-height:calc(1em + 9px); font-size: ' + (Number(widget.headerTextSize) - 2) + 'px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}';
        html += '      #sincloBox p#widgetSubTitle.leftPosition { padding-left: calc(2.5em + 46px)}';
        html += '      #sincloBox p#widgetSubTitle.leftPosition.noImage { padding-left: 15px;}';
        html += '      #sincloBox p#widgetSubTitle.centerPosition { text-align: center; padding-right: 25px; padding-left: calc(2.5em + 38px);}';
        html += '      #sincloBox p#widgetSubTitle.centerPosition.noImage { padding-left: 0px; padding-right: 0px;}';
        html += '      #sincloBox p#widgetSubTitle.oneContent { line-height: calc((1em + 9px)*2); border-bottom: 1px ' + colorList['widgetBorderColor'] + ' solid;}';
        if ( colorList['widgetInsideBorderNone'] === 1 ) {
          html += '      #sincloBox p#widgetSubTitle.oneContent{ border:none;}';
        }

        html += '      #sincloBox p#widgetDescription.leftPosition { padding-left: calc(2.5em + 46px)}';
        html += '      #sincloBox p#widgetDescription.leftPosition.noImage { padding-left: 15px;}';
        html += '      #sincloBox p#widgetDescription.centerPosition { text-align: center; padding-right: 25px; padding-left: calc(2.5em + 38px);}';
        html += '      #sincloBox p#widgetDescription.centerPosition.noImage { padding-left: 0px; padding-right: 0px;}';
        html += '      #sincloBox p#widgetDescription.oneContent { line-height: calc((1em + 9px)*2); padding-top: 3px; padding-bottom: 3px;}';
        if ( colorList['widgetBorderNone'] === 1 ) {
          html += '#sincloBox p#widgetSubTitle { border:none; }';
        }

        html += '      #sincloBox p#widgetDescription { background-color: ' + colorList['headerBackgroundColor'] + '; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: ' + colorList['widgetBorderColor'] + '; border-style: solid; height: auto; line-height:calc(1em + 2px); color: ' + colorList['descriptionTextColor'] + '; border-bottom-color:' + colorList['widgetInsideBorderColor'] + '; font-size: ' + (Number(widget.headerTextSize) - 2) + 'px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;;}';
        if ( colorList['widgetBorderNone'] === 1 ) {
          html += '#sincloBox p#widgetDescription { border-left:none; border-right:none;}';
        }
        html += '      #sincloBox section { background-color: #FFF; border: 1px solid ' + colorList['widgetBorderColor'] + '; border-top: none; border-bottom: 1px solid ' + colorList['widgetInsideBorderColor'] + '; }';
        if ( colorList['widgetBorderNone'] === 1 ) {
          html += '      #sincloBox section { border-top: none; border-left:none; border-right:none; }';
        }
        if ( colorList['widgetInsideBorderNone'] === 1 ) {
          html += '#sincloBox p#widgetDescription { border-bottom:none!important;}';
          html += '      #sincloBox section { border-bottom: none!important; }';
        }

        html += '      #sincloBox ul#chatTalk li.sinclo_etc { border: none; text-align: center!important; margin: 0 auto; font-weight: bold; font-size:calc(' + widget.reTextSize + 'px*0.92); }';
        html += '      #sincloBox section#chatTab sinclo-div #sincloChatSendBtn, #sincloBox section#chatTab sinclo-div #miniSincloChatSendBtn { font-size: ' + widget.chatSendBtnTextSize + 'px;}';

        // チャットを使用する際
        if ( window.sincloInfo.contract.chat ) {
          html += '      #sincloBox #mainImage em { top: -10px; right: -10px; width: 25px; height: 20px; font-size: ' + sizeList['d11font'] + 'px; padding: 1px; }';
          html += '      #sincloBox ul#chatTalk { height: ' + sizeList['chatTalkHeight'] + 'px; padding: 0px 5px 0px 5px; background-color: ' + colorList['chatTalkBackgroundColor'] + ' }';
          html += '      #sincloBox ul#chatTalk li { border-radius: 12px; margin: 10px 0 0 0; padding: 10px 15px; }';
          if(colorList['seBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_se { border: 1px solid '+ chatPosition.se.borderColor +'; }';
          }
          if(colorList['reBorderNone'] === 0){
            html += '      #sincloBox ul#chatTalk li.sinclo_re { border: 1px solid '+ chatPosition.re.borderColor +'; }';
          }
          html += '      #sincloBox ul#chatTalk li span.cName { font-size: ' + (Number(widget.reTextSize)) + 'px; margin: 0 0 5px 0 }';
          html += '      #sincloBox section#chatTab sinclo-div:not(#flexBoxWrap) { height: ' + sizeList['classFlexBoxRowHeight'] + 'px!important; padding: 5px }';
          html += '      #sincloBox section#chatTab sinclo-div#miniFlexBoxHeight { height: 48px!important; padding: 5px }';
          html += '      #sincloBox section#chatTab sinclo-chat-alert { display: none; position: absolute; background-color: rgba(0,0,0,0.6); box-shadow: 0px 0px 4px 4px rgba(0,0,0,0.1); color: #FFF; text-align: center; cursor: pointer; }';
          html += '      #sincloBox section#chatTab sinclo-chat-alert { left: 25%; bottom: 50%; width: 50%; height:64px; border-radius: 5px; line-height: 47px; color: #FFF; padding: 10px 0; }';
          html += '      #sincloBox section#chatTab #sincloChatMessage, #sincloBox section#chatTab #miniSincloChatMessage { color: ' + chatContentTextColor + '!important; padding: 5px; height: 100%; min-height: 100%!important; }';
          if ( colorList['messageBoxBorderNone'] === 0 ) {
            html += '      #sincloBox section#chatTab #sincloChatMessage, #sincloBox section#chatTab #miniSincloChatMessage { border: 1px solid ' + colorList['messageBoxBorderColor'] + '!important; border-radius: 5px 0 0 5px!important; }';
          }
          else {
            html += '      #sincloBox section#chatTab #sincloChatMessage, #sincloBox section#chatTab #miniSincloChatMessage { border: none!important;}';
          }
          html += '      #sincloBox section#chatTab #sincloChatSendBtn, #sincloBox section#chatTab #miniSincloChatSendBtn { padding: 20px 0; height: 100%; border: 1px solid ' + colorList['chatSendBtnBackgroundColor'] + '; }';
          html += '      #sincloBox section#chatTab #miniSincloChatSendBtn { padding: 8px 0; height: 100%; border: 1px solid ' + colorList['chatSendBtnBackgroundColor'] + '; }';
        }
        // 画面同期を使用する際
        if ( window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document) ) {
          html += '      #sincloBox section#callTab { background-color: ' + colorList['chatTalkBackgroundColor'] + '!important; }';
          html += '      #sincloBox section#callTab #telNumber { overflow: hidden; color: ' + colorList['mainColor'] + '; font-weight: bold; margin: 0 auto; text-align: center; border: none!important; overflow: visible!important; }';
          html += '      #sincloBox section#callTab #telIcon { color: ' + colorList['mainColor'] + '; display: block; width: 50px; height: 50px; float: left; background-color: #3EA3DE; border-radius: 25px; padding: 3px }';
          html += '      #sincloBox section#callTab #telContent { display: block; overflow-y: auto; overflow-x: hidden; height:' + sizeList['telContentHeight'] + 'px; max-height: ' + sizeList['telContentHeight'] + 'px }';
          if ( window.sincloInfo.contract.chat ) {
            html += '      #sincloBox section#callTab #telContent .tblBlock {  text-align: center;  margin: 0 auto;  width: 240px;  display: table; align-content: center;  height: 119px!important;  justify-content: center; }';
          }
          else {
            html += '      #sincloBox section#callTab #telContent .tblBlock { text-align: center; margin: 0 auto; width: 240px; display: table; align-content: center; justify-content: center; overflow-x: hidden; overflow-y: auto }';
          }
          html += '      #sincloBox section#callTab #telContent span { word-wrap: break-word ;word-break: break-all; font-size: ' + sizeList['d11font'] + 'px; line-height: 1.5!important; color: #6B6B6B; white-space: pre-wrap; max-height: 119px; display: table-cell; vertical-align: middle; text-align: center }';
          if ( window.sincloInfo.contract.chat ) {
            html += '      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 20px auto 21px; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: ' + sizeList['d25font'] + 'px; font-weight: bold; text-align: center; border-radius: 15px } ';
          } else {
            html += '      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: ' + sizeList['d25font'] + 'px; font-weight: bold; text-align: center; border-radius: 15px } ';
          }
        }
        html += '      #sincloBox section#navigation { border-width: 0 1px; height: ' + sizeList['navigationHeight'] + 'px; }';
        html += '      #sincloBox section#navigation ul { margin: 0 0 0 -1px; height: ' + sizeList['navigationHeight'] + 'px;}';
        html += '      #sincloBox section#navigation ul li { width: 50%; padding: 10px 0; border-left: 1px solid ' + colorList['widgetBorderColor'] + '; height: ' + sizeList['navigationHeight'] + 'px }';
        if ( colorList['widgetBorderNone'] === 1 ) {
          html += '      #sincloBox section#navigation ul li { border-left:none; }';
        }
        html += '      #sincloBox section#navigation ul li:last-child { border-right: 1px solid ' + colorList['widgetBorderColor'] + '; border-left: 1px solid ' + colorList['widgetInsideBorderColor'] + '; }';
        if ( colorList['widgetBorderNone'] === 1 ) {
          html += '      #sincloBox section#navigation ul li:last-child { border-right:none; }';
        }
        if ( colorList['widgetInsideBorderNone'] === 1 ) {
          html += '      #sincloBox section#navigation ul li:last-child { border-left: none!important; }';
        }
        html += '      #sincloBox section#navigation ul li:not(.selected) { border-bottom: 1px solid ' + colorList['widgetInsideBorderColor'] + ' }';
        if ( colorList['widgetInsideBorderNone'] === 1 ) {
          html += '      #sincloBox section#navigation ul li:not(.selected) { border-bottom: none!important; }';
        }
        html += '      #sincloBox section#navigation ul li.selected::after{ border-bottom: 2px solid ' + colorList['mainColor'] + '; }';
        html += '      #sincloBox #fotter { height: ' + sizeList['fotterHeight'] + 'px; padding: 5px 0; border: 1px solid ' + colorList['widgetBorderColor'] + '; font-size: ' + sizeList['d11font'] + 'px; border-top: none; }';
        if ( colorList['widgetBorderNone'] === 1 ) {
          html += '      #sincloBox #fotter { border:none }';
        }
        html += '      #sincloBox section#navigation ul li::before{ margin-right: 5px; width: 18px; height: 18px; }';

      }


      html += '  </style>';

      return html;
    },
    injectCalendarCSS: function () {
      return "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar {\n" +
        "  background: transparent;\n" +
        "  opacity: 0;\n" +
        "  display: none;\n" +
        "  text-align: center;\n" +
        "  visibility: hidden;\n" +
        "  padding: 0;\n" +
        "  -webkit-animation: none;\n" +
        "  animation: none;\n" +
        "  direction: ltr;\n" +
        "  border: 0;\n" +
        "  font-size: 14px;\n" +
        "  line-height: 24px;\n" +
        "  border-radius: 5px;\n" +
        "  position: absolute;\n" +
        "  width: 307.875px;\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "  -ms-touch-action: manipulation;\n" +
        "  touch-action: manipulation;\n" +
        "  background: #fff;\n" +
        "  -webkit-box-shadow: 1px 0 0 #e6e6e6, -1px 0 0 #e6e6e6, 0 1px 0 #e6e6e6, 0 -1px 0 #e6e6e6, 0 3px 13px rgba(0, 0, 0, 0.08);\n" +
        "  box-shadow: 1px 0 0 #e6e6e6, -1px 0 0 #e6e6e6, 0 1px 0 #e6e6e6, 0 -1px 0 #e6e6e6, 0 3px 13px rgba(0, 0, 0, 0.08);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.open,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.inline {\n" +
        "  opacity: 1;\n" +
        "  max-height: 640px;\n" +
        "  visibility: visible;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.open {\n" +
        "  display: inline-block;\n" +
        "  z-index: 99999;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.animate.open {\n" +
        "  -webkit-animation: fpFadeInDown 300ms cubic-bezier(0.23, 1, 0.32, 1);\n" +
        "  animation: fpFadeInDown 300ms cubic-bezier(0.23, 1, 0.32, 1);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.inline {\n" +
        "  display: block;\n" +
        "  position: relative;\n" +
        "  top: 2px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.static {\n" +
        "  position: absolute;\n" +
        "  top: calc(100% + 2px);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.static.open {\n" +
        "  z-index: 999;\n" +
        "  display: block;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.multiMonth .flatpickr-days .dayContainer:nth-child(n+1) .flatpickr-day.inRange:nth-child(7n+7) {\n" +
        "  -webkit-box-shadow: none !important;\n" +
        "  box-shadow: none !important;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.multiMonth .flatpickr-days .dayContainer:nth-child(n+2) .flatpickr-day.inRange:nth-child(7n+1) {\n" +
        "  -webkit-box-shadow: -2px 0 0 #e6e6e6, 5px 0 0 #e6e6e6;\n" +
        "  box-shadow: -2px 0 0 #e6e6e6, 5px 0 0 #e6e6e6;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .hasWeeks .dayContainer,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .hasTime .dayContainer {\n" +
        "  border-bottom: 0;\n" +
        "  border-bottom-right-radius: 0;\n" +
        "  border-bottom-left-radius: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .hasWeeks .dayContainer {\n" +
        "  border-left: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.showTimeInput.hasTime .flatpickr-time {\n" +
        "  height: 40px;\n" +
        "  border-top: 1px solid #e6e6e6;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.noCalendar.hasTime .flatpickr-time {\n" +
        "  height: auto;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar:before,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar:after {\n" +
        "  position: absolute;\n" +
        "  display: block;\n" +
        "  pointer-events: none;\n" +
        "  content: \"\";\n" +
        "  height: 0;\n" +
        "  width: 0;\n" +
        "  left: 22px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.rightMost:before,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.rightMost:after {\n" +
        "  left: auto;\n" +
        "  right: 22px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar:before {\n" +
        "  border-width: 5px;\n" +
        "  margin: 0 -5px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar:after {\n" +
        "  border-width: 4px;\n" +
        "  margin: 0 -4px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.arrowTop:before,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.arrowTop:after {\n" +
        "  bottom: 100%;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.arrowTop:before {\n" +
        "  border-bottom-color: #e6e6e6;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.arrowTop:after {\n" +
        "  border-bottom-color: #fff;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.arrowBottom:before,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.arrowBottom:after {\n" +
        "  top: 100%;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.arrowBottom:before {\n" +
        "  border-top-color: #e6e6e6;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar.arrowBottom:after {\n" +
        "  border-top-color: #fff;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar:focus {\n" +
        "  outline: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-wrapper {\n" +
        "  position: relative;\n" +
        "  display: inline-block;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months {\n" +
        "  display: -webkit-box;\n" +
        "  display: -webkit-flex;\n" +
        "  display: -ms-flexbox;\n" +
        "  display: flex;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-month {\n" +
        "  background: transparent;\n" +
        "  color: rgba(0, 0, 0, 0.9);\n" +
        "  fill: rgba(0, 0, 0, 0.9);\n" +
        "  height: 28px;\n" +
        "  line-height: 1;\n" +
        "  text-align: center;\n" +
        "  position: relative;\n" +
        "  -webkit-user-select: none;\n" +
        "  -moz-user-select: none;\n" +
        "  -ms-user-select: none;\n" +
        "  user-select: none;\n" +
        "  overflow: hidden;\n" +
        "  -webkit-box-flex: 1;\n" +
        "  -webkit-flex: 1;\n" +
        "  -ms-flex: 1;\n" +
        "  flex: 1;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month {\n" +
        "  text-decoration: none;\n" +
        "  cursor: pointer;\n" +
        "  position: absolute;\n" +
        "  top: 0;\n" +
        "  line-height: 16px;\n" +
        "  height: 28px;\n" +
        "  padding: 10px;\n" +
        "  z-index: 3;\n" +
        "  color: rgba(0, 0, 0, 0.9);\n" +
        "  fill: rgba(0, 0, 0, 0.9);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month.disabled,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month.disabled {\n" +
        "  display: none;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month i,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month i {\n" +
        "  position: relative;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month.flatpickr-prev-month,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month.flatpickr-prev-month {\n" +
        "  left: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month.flatpickr-next-month,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month.flatpickr-next-month {\n" +
        "  right: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month:hover {\n" +
        "  color: #959ea9;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month:hover svg,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month:hover svg {\n" +
        "  fill: #f64747;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month svg,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month svg {\n" +
        "  width: 14px;\n" +
        "  height: 14px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-prev-month svg path,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-months .flatpickr-next-month svg path {\n" +
        "  -webkit-transition: fill 0.1s;\n" +
        "  transition: fill 0.1s;\n" +
        "  fill: inherit;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper {\n" +
        "  position: relative;\n" +
        "  height: auto;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper input,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span {\n" +
        "  display: inline-block;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper input {\n" +
        "  width: 100%;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper input::-ms-clear {\n" +
        "  display: none;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span {\n" +
        "  position: absolute;\n" +
        "  right: 0;\n" +
        "  width: 14px;\n" +
        "  padding: 0 4px 0 2px;\n" +
        "  height: 50%;\n" +
        "  line-height: 50%;\n" +
        "  opacity: 0;\n" +
        "  cursor: pointer;\n" +
        "  border: 1px solid rgba(57, 57, 57, 0.15);\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span:hover {\n" +
        "  background: rgba(0, 0, 0, 0.1);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span:active {\n" +
        "  background: rgba(0, 0, 0, 0.2);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span:after {\n" +
        "  display: block;\n" +
        "  content: \"\";\n" +
        "  position: absolute;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span.arrowUp {\n" +
        "  top: 0;\n" +
        "  border-bottom: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span.arrowUp:after {\n" +
        "  border-left: 4px solid transparent;\n" +
        "  border-right: 4px solid transparent;\n" +
        "  border-bottom: 4px solid rgba(57, 57, 57, 0.6);\n" +
        "  top: 26%;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span.arrowDown {\n" +
        "  top: 50%;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span.arrowDown:after {\n" +
        "  border-left: 4px solid transparent;\n" +
        "  border-right: 4px solid transparent;\n" +
        "  border-top: 4px solid rgba(57, 57, 57, 0.6);\n" +
        "  top: 40%;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span svg {\n" +
        "  width: inherit;\n" +
        "  height: auto;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper span svg path {\n" +
        "  fill: rgba(0, 0, 0, 0.5);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper:hover {\n" +
        "  background: rgba(0, 0, 0, 0.05);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .numInputWrapper:hover span {\n" +
        "  opacity: 1;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month {\n" +
        "  font-size: 135%;\n" +
        "  line-height: inherit;\n" +
        "  font-weight: 300;\n" +
        "  color: inherit;\n" +
        "  position: absolute;\n" +
        "  width: 75%;\n" +
        "  left: 12.5%;\n" +
        "  padding: 6.16px 0 0 0;\n" +
        "  line-height: 1;\n" +
        "  height: 28px;\n" +
        "  display: inline-block;\n" +
        "  text-align: center;\n" +
        "  -webkit-transform: translate3d(0, 0, 0);\n" +
        "  transform: translate3d(0, 0, 0);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month span.cur-month {\n" +
        "  font-family: inherit;\n" +
        "  font-weight: 700;\n" +
        "  color: inherit;\n" +
        "  display: inline-block;\n" +
        "  margin-left: 0.5ch;\n" +
        "  padding: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month span.cur-month:hover {\n" +
        "  background: rgba(0, 0, 0, 0.05);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month .numInputWrapper {\n" +
        "  width: 6ch;\n" +
        "  width: 7ch\\0;\n" +
        "  display: inline-block;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month .numInputWrapper span.arrowUp:after {\n" +
        "  border-bottom-color: rgba(0, 0, 0, 0.9);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month .numInputWrapper span.arrowDown:after {\n" +
        "  border-top-color: rgba(0, 0, 0, 0.9);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month input.cur-year {\n" +
        "  background: transparent;\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "  color: inherit;\n" +
        "  cursor: text;\n" +
        "  padding: 0 0 0 0.5ch;\n" +
        "  margin: 0;\n" +
        "  display: inline-block;\n" +
        "  font-size: inherit;\n" +
        "  font-family: inherit;\n" +
        "  font-weight: 300;\n" +
        "  line-height: inherit;\n" +
        "  height: auto;\n" +
        "  border: 0;\n" +
        "  border-radius: 0;\n" +
        "  vertical-align: initial;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month input.cur-year:focus {\n" +
        "  outline: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month input.cur-year[disabled],\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-current-month input.cur-year[disabled]:hover {\n" +
        "  font-size: 100%;\n" +
        "  color: rgba(0, 0, 0, 0.5);\n" +
        "  background: transparent;\n" +
        "  pointer-events: none;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-weekdays {\n" +
        "  background: transparent;\n" +
        "  text-align: center;\n" +
        "  overflow: hidden;\n" +
        "  width: 100%;\n" +
        "  display: -webkit-box;\n" +
        "  display: -webkit-flex;\n" +
        "  display: -ms-flexbox;\n" +
        "  display: flex;\n" +
        "  -webkit-box-align: center;\n" +
        "  -webkit-align-items: center;\n" +
        "  -ms-flex-align: center;\n" +
        "  align-items: center;\n" +
        "  height: 28px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-weekdays .flatpickr-weekdaycontainer {\n" +
        "  display: -webkit-box;\n" +
        "  display: -webkit-flex;\n" +
        "  display: -ms-flexbox;\n" +
        "  display: flex;\n" +
        "  -webkit-box-flex: 1;\n" +
        "  -webkit-flex: 1;\n" +
        "  -ms-flex: 1;\n" +
        "  flex: 1;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re span.flatpickr-weekday {\n" +
        "  cursor: default;\n" +
        "  font-size: 90%;\n" +
        "  background: transparent;\n" +
        "  color: rgba(0, 0, 0, 0.54);\n" +
        "  line-height: 1;\n" +
        "  margin: 0;\n" +
        "  text-align: center;\n" +
        "  display: block;\n" +
        "  -webkit-box-flex: 1;\n" +
        "  -webkit-flex: 1;\n" +
        "  -ms-flex: 1;\n" +
        "  flex: 1;\n" +
        "  font-weight: bolder;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .dayContainer,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-weeks {\n" +
        "  padding: 1px 0 0 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-days {\n" +
        "  position: relative;\n" +
        "  overflow: hidden;\n" +
        "  display: -webkit-box;\n" +
        "  display: -webkit-flex;\n" +
        "  display: -ms-flexbox;\n" +
        "  display: flex;\n" +
        "  -webkit-box-align: start;\n" +
        "  -webkit-align-items: flex-start;\n" +
        "  -ms-flex-align: start;\n" +
        "  align-items: flex-start;\n" +
        "  width: 307.875px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-days:focus {\n" +
        "  outline: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .dayContainer {\n" +
        "  padding: 0;\n" +
        "  outline: 0;\n" +
        "  text-align: left;\n" +
        "  width: 307.875px;\n" +
        "  min-width: 307.875px;\n" +
        "  max-width: 307.875px;\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "  display: inline-block;\n" +
        "  display: -ms-flexbox;\n" +
        "  display: -webkit-box;\n" +
        "  display: -webkit-flex;\n" +
        "  display: flex;\n" +
        "  -webkit-flex-wrap: wrap;\n" +
        "  flex-wrap: wrap;\n" +
        "  -ms-flex-wrap: wrap;\n" +
        "  -ms-flex-pack: justify;\n" +
        "  -webkit-justify-content: space-around;\n" +
        "  justify-content: space-around;\n" +
        "  -webkit-transform: translate3d(0, 0, 0);\n" +
        "  transform: translate3d(0, 0, 0);\n" +
        "  opacity: 1;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .dayContainer + .dayContainer {\n" +
        "  -webkit-box-shadow: -1px 0 0 #e6e6e6;\n" +
        "  box-shadow: -1px 0 0 #e6e6e6;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day {\n" +
        "  background: none;\n" +
        "  border: 1px solid transparent;\n" +
        "  border-radius: 150px;\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "  color: #393939;\n" +
        "  cursor: pointer;\n" +
        "  font-weight: 400;\n" +
        "  width: 14.2857143%;\n" +
        "  -webkit-flex-basis: 14.2857143%;\n" +
        "  -ms-flex-preferred-size: 14.2857143%;\n" +
        "  flex-basis: 14.2857143%;\n" +
        "  max-width: 39px;\n" +
        "  height: 39px;\n" +
        "  line-height: 39px;\n" +
        "  margin: 0;\n" +
        "  display: inline-block;\n" +
        "  position: relative;\n" +
        "  -webkit-box-pack: center;\n" +
        "  -webkit-justify-content: center;\n" +
        "  -ms-flex-pack: center;\n" +
        "  justify-content: center;\n" +
        "  text-align: center;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.prevMonthDay.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.nextMonthDay.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.today.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.prevMonthDay.today.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.nextMonthDay.today.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.prevMonthDay:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.nextMonthDay:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day:focus,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.prevMonthDay:focus,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.nextMonthDay:focus {\n" +
        "  cursor: pointer;\n" +
        "  outline: 0;\n" +
        "  background: #e6e6e6;\n" +
        "  border-color: #e6e6e6;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.today {\n" +
        "  border-color: #959ea9;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.today:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.today:focus {\n" +
        "  border-color: #959ea9;\n" +
        "  background: #959ea9;\n" +
        "  color: #fff;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange.inRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected:focus,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange:focus,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange:focus,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected.prevMonthDay,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange.prevMonthDay,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange.prevMonthDay,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected.nextMonthDay,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange.nextMonthDay,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange.nextMonthDay {\n" +
        "  -webkit-box-shadow: none;\n" +
        "  box-shadow: none;\n" +
        "  color: #fff;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected.startRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange.startRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange.startRange {\n" +
        "  border-radius: 50px 0 0 50px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected.endRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange.endRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange.endRange {\n" +
        "  border-radius: 0 50px 50px 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected.startRange + .endRange:not(:nth-child(7n+1)),\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange.startRange + .endRange:not(:nth-child(7n+1)),\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange.startRange + .endRange:not(:nth-child(7n+1)) {\n" +
        "  -webkit-box-shadow: -10px 0 0 #569ff7;\n" +
        "  box-shadow: -10px 0 0 #569ff7;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.selected.startRange.endRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.startRange.startRange.endRange,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.endRange.startRange.endRange {\n" +
        "  border-radius: 50px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.inRange {\n" +
        "  border-radius: 0;\n" +
        "  -webkit-box-shadow: -5px 0 0 #e6e6e6, 5px 0 0 #e6e6e6;\n" +
        "  box-shadow: -5px 0 0 #e6e6e6, 5px 0 0 #e6e6e6;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.disabled,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.disabled:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.prevMonthDay,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.nextMonthDay,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.notAllowed,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.notAllowed.prevMonthDay,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.notAllowed.nextMonthDay {\n" +
        "  color: rgba(57, 57, 57, 0.3);\n" +
        "  background: transparent;\n" +
        "  border-color: transparent;\n" +
        "  cursor: default;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.disabled,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.disabled:hover {\n" +
        "  cursor: not-allowed;\n" +
        "  color: rgba(57, 57, 57, 0.1);\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.week.selected {\n" +
        "  border-radius: 0;\n" +
        "  -webkit-box-shadow: -5px 0 0 #569ff7, 5px 0 0 #569ff7;\n" +
        "  box-shadow: -5px 0 0 #569ff7, 5px 0 0 #569ff7;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-day.hidden {\n" +
        "  visibility: hidden;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .rangeMode .flatpickr-day {\n" +
        "  margin-top: 1px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-weekwrapper {\n" +
        "  display: inline-block;\n" +
        "  float: left;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-weekwrapper .flatpickr-weeks {\n" +
        "  padding: 0 12px;\n" +
        "  -webkit-box-shadow: 1px 0 0 #e6e6e6;\n" +
        "  box-shadow: 1px 0 0 #e6e6e6;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-weekwrapper .flatpickr-weekday {\n" +
        "  float: none;\n" +
        "  width: 100%;\n" +
        "  line-height: 28px;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-weekwrapper span.flatpickr-day,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-weekwrapper span.flatpickr-day:hover {\n" +
        "  display: block;\n" +
        "  width: 100%;\n" +
        "  max-width: none;\n" +
        "  color: rgba(57, 57, 57, 0.3);\n" +
        "  background: transparent;\n" +
        "  cursor: default;\n" +
        "  border: none;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-innerContainer {\n" +
        "  display: block;\n" +
        "  display: -webkit-box;\n" +
        "  display: -webkit-flex;\n" +
        "  display: -ms-flexbox;\n" +
        "  display: flex;\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "  overflow: hidden;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-rContainer {\n" +
        "  display: inline-block;\n" +
        "  padding: 0;\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time {\n" +
        "  text-align: center;\n" +
        "  outline: 0;\n" +
        "  display: block;\n" +
        "  height: 0;\n" +
        "  line-height: 40px;\n" +
        "  max-height: 40px;\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "  overflow: hidden;\n" +
        "  display: -webkit-box;\n" +
        "  display: -webkit-flex;\n" +
        "  display: -ms-flexbox;\n" +
        "  display: flex;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time:after {\n" +
        "  content: \"\";\n" +
        "  display: table;\n" +
        "  clear: both;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time .numInputWrapper {\n" +
        "  -webkit-box-flex: 1;\n" +
        "  -webkit-flex: 1;\n" +
        "  -ms-flex: 1;\n" +
        "  flex: 1;\n" +
        "  width: 40%;\n" +
        "  height: 40px;\n" +
        "  float: left;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time .numInputWrapper span.arrowUp:after {\n" +
        "  border-bottom-color: #393939;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time .numInputWrapper span.arrowDown:after {\n" +
        "  border-top-color: #393939;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time.hasSeconds .numInputWrapper {\n" +
        "  width: 26%;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time.time24hr .numInputWrapper {\n" +
        "  width: 49%;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time input {\n" +
        "  background: transparent;\n" +
        "  -webkit-box-shadow: none;\n" +
        "  box-shadow: none;\n" +
        "  border: 0;\n" +
        "  border-radius: 0;\n" +
        "  text-align: center;\n" +
        "  margin: 0;\n" +
        "  padding: 0;\n" +
        "  height: inherit;\n" +
        "  line-height: inherit;\n" +
        "  color: #393939;\n" +
        "  font-size: 14px;\n" +
        "  position: relative;\n" +
        "  -webkit-box-sizing: border-box;\n" +
        "  box-sizing: border-box;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time input.flatpickr-hour {\n" +
        "  font-weight: bold;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time input.flatpickr-minute,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time input.flatpickr-second {\n" +
        "  font-weight: 400;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time input:focus {\n" +
        "  outline: 0;\n" +
        "  border: 0;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time .flatpickr-time-separator,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time .flatpickr-am-pm {\n" +
        "  height: inherit;\n" +
        "  display: inline-block;\n" +
        "  float: left;\n" +
        "  line-height: inherit;\n" +
        "  color: #393939;\n" +
        "  font-weight: bold;\n" +
        "  width: 2%;\n" +
        "  -webkit-user-select: none;\n" +
        "  -moz-user-select: none;\n" +
        "  -ms-user-select: none;\n" +
        "  user-select: none;\n" +
        "  -webkit-align-self: center;\n" +
        "  -ms-flex-item-align: center;\n" +
        "  align-self: center;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time .flatpickr-am-pm {\n" +
        "  outline: 0;\n" +
        "  width: 18%;\n" +
        "  cursor: pointer;\n" +
        "  text-align: center;\n" +
        "  font-weight: 400;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time input:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time .flatpickr-am-pm:hover,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time input:focus,\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-time .flatpickr-am-pm:focus {\n" +
        "  background: #f3f3f3;\n" +
        "}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-input[readonly] {\n" +
        "  cursor: pointer;\n" +
        "}\n" +
        "@-webkit-keyframes fpFadeInDown {\n" +
        "  from {\n" +
        "    opacity: 0;\n" +
        "    -webkit-transform: translate3d(0, -20px, 0);\n" +
        "    transform: translate3d(0, -20px, 0);\n" +
        "  }\n" +
        "  to {\n" +
        "    opacity: 1;\n" +
        "    -webkit-transform: translate3d(0, 0, 0);\n" +
        "    transform: translate3d(0, 0, 0);\n" +
        "  }\n" +
        "}\n" +
        "@keyframes fpFadeInDown {\n" +
        "  from {\n" +
        "    opacity: 0;\n" +
        "    -webkit-transform: translate3d(0, -20px, 0);\n" +
        "    transform: translate3d(0, -20px, 0);\n" +
        "  }\n" +
        "  to {\n" +
        "    opacity: 1;\n" +
        "    -webkit-transform: translate3d(0, 0, 0);\n" +
        "    transform: translate3d(0, 0, 0);\n" +
        "  }\n" +
        "}" + "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar {\n" +
        "  width: 210px;\n" +
        "  height: 252px;\n" +
        "  border-radius: 0;\n" +
        "  box-shadow: none;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-current-month {\n" +
        "  font-size: 14px;\n" +
        "  padding-top: 8px;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-weekdays {\n" +
        "  width: 206px;\n" +
        "  height: 24px;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-weekdaycontainer {\n" +
        "  padding-top: 4px;\n" +
        "  padding-right: 2px;\n" +
        "  height: 21px;\n" +
        "  white-space: normal;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-weekdaycontainer .flatpickr-weekday {\n" +
        "  font-size: 11px;\n" +
        "  line-height: 10px;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .dayContainer {\n" +
        "  max-width: 206px;\n" +
        "  min-width: 200px;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-months {\n" +
        "  height: 32px;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-months .flatpickr-prev-month {\n" +
        "  height: 24px;\n" +
        "  padding: 7px;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-months .flatpickr-next-month {\n" +
        "  height: 24px;\n" +
        "  padding: 7px;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-months .flatpickr-current-month .numInputWrapper {\n" +
        "  display: none;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .flatpickr-months .flatpickr-current-month input.cur-year {\n" +
        "  display: none;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .dayContainer .flatpickr-day.disabled {\n" +
        "  color: rgba(57, 57, 57, 0.18);\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .dayContainer .flatpickr-day {\n" +
        "  height: 32px;\n" +
        "  line-height: 32px;\n" +
        "  border-radius: 0;\n" +
        "  font-weight: bolder;\n" +
        "  flex-basis: 29.42px;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .dayContainer .flatpickr-day.today {\n" +
        "  border: none;\n" +
        "}\n" +
        "\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .dayContainer span:nth-child(7n+7) {\n" +
        "  border-right: none;}\n" +
        "#sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .dayContainer .flatpickr-day.today:after { content: \"\";position: absolute;top: 0px;left: 0px;width: 26px;height: 29px;display: inline-block;}\n" +
        "@media screen and (-ms-high-contrast: active), screen and (-ms-high-contrast: none) {\n" +
        "  #sincloBox ul#chatTalk li.sinclo_re .flatpickr-calendar .dayContainer .flatpickr-day {flex-basis: 28.42px;}\n" +
        "  #sincloBox ul#chatTalk li.sinclo_re  .flatpickr-calendar .dayContainer .flatpickr-day.today:after { content: \"\";position: absolute;top: 0px;left: 0px;width: 27px;height: 29px;display: inline-block;}\n" +
        "}";
    },
    //バナーを生成する関数
    sincloBannerTemplate: function (widget) {
      var widgetWidth = $(window).width() - 20;
      var ratio = widgetWidth * (1 / 285);
      if ( check.smartphone() ) {
        if ( common.isPortrait() ) {
          //縦
          var paddingpx = 'padding: ' + 10 * ratio + 'px 0';
        }
        else {
          //横
          var paddingpx = 'padding: 20px 0';
        }
      }
      else {
        var paddingpx = 'padding: ' + 10 + 'px 0';
      }
      var html = "";
      html += '  <div id="sincloBanner" class="sincloBanner" onclick="sinclo.operatorInfo.clickBanner()">';
      html += '    <div id="sincloBannerText" class="sincloBannerText">';
      html += '      <svg version="1.1" id="bannerIcon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">\n' +
        '<style type="text/css">\n' +
        '\t.st0{fill:' + sincloInfo.widget.stringColor + ';}\n' +
        '</style>\n' +
        '<g>\n' +
        '\t<path class="st0" d="M257.135,19.179C103.967,19.179,0,97.273,0,218.763c0,74.744,31.075,134.641,91.108,173.176\n' +
        '\t\tc4.004,2.572,8.728,2.962,6.955,10.365c-7.16,29.935-19.608,83.276-19.608,83.276c-0.527,2.26,0.321,4.618,2.162,6.03\n' +
        '\t\tc1.84,1.402,4.334,1.607,6.38,0.507c0,0,87.864-52.066,99.583-58.573c27.333-15.625,50.878-18.654,68.558-18.654\n' +
        '\t\tC376.619,414.89,512,366.282,512,217.458C512,102.036,418.974,19.179,257.135,19.179z" style="fill: ' + common.toRGBCode(sincloInfo.widget.stringColor) + ';"></path>\n' +
        '</g>\n' +
        '</svg>';
      var bannertext = "";
      if ( check.smartphone() && typeof(widget.spBannerText) === "string" ) {
        //スマホかつ隠しパラメータ設定済みの場合
        bannertext = check.escape_html(widget.spBannerText);
      } else {
        bannertext = check.escape_html(widget.bannertext);
      }
      html += '      <span class="' + (bannertext.length !== 0 ? 'bannertext' : 'notext') + '">' + bannertext + '</span>';
      html += '    </div>';
      html += '  </div>';
      return html;
    },
    widgetHeaderTemplate: function (widget) {
      var html = "", chatAndTitleOnly = false;
      // チャットとタイトルバーのみ表示するフラグ
//      if ( check.smartphone() && ( window.screen.availHeight < window.screen.availWidth || (widget.hasOwnProperty('spHeaderLightFlg') && Number(widget.spHeaderLightFlg) === 1) ) ) {
//        chatAndTitleOnly = true;
//      }
      // 画像
      if ( !chatAndTitleOnly && (Number(widget.showMainImage) === 1 || widget.mainImage !== "") ) {
        var ratio = 1;
        if ( check.smartphone() ) {
          ratio = ($(window).width() - 20) * (1 / 285);
        }
        html += '  <span id="mainImage" onclick="sinclo.operatorInfo.toggle()">';
        if ( check.smartphone() ) {
          if ( widget.mainImage.match(/^fa/) !== null ) {
            html += '    <i class="sinclo-fal ' + widget.mainImage + '" style="width:calc(' + (62 * ratio) + 'px* ((3 * 14 + 36) / 81))!important; height:calc(' + (70 * ratio) + 'px* ((3 * 14 + 36) / 81))!important;" alt="チャット画像"></i>';
          } else {
            html += '    <img src="' + widget.mainImage + '" style="width:calc(' + (62 * ratio) + 'px* ((3 * 14 + 36) / 81))!important; height:calc(' + (70 * ratio) + 'px* ((3 * 14 + 36) / 81))!important;" alt="チャット画像">';
          }
        } else {
          if ( widget.mainImage.match(/^fa/) !== null ) {
            html += '    <i class="sinclo-fal ' + widget.mainImage + '" style="width:calc(' + (62 * ratio) + 'px* ((3 * ' + widget.headerTextSize + ' + 36) / 81))!important; height:calc(' + (70 * ratio) + 'px* ((3 * ' + widget.headerTextSize + ' + 36) / 81))!important;" alt="チャット画像"></i>';
          } else {
            html += '    <img src="' + widget.mainImage + '" style="width:calc(' + (62 * ratio) + 'px* ((3 * ' + widget.headerTextSize + ' + 36) / 81))!important; height:calc(' + (70 * ratio) + 'px* ((3 * ' + widget.headerTextSize + ' + 36) / 81))!important;" alt="チャット画像">';
          }
        }
        html += '  </span>';
      }
      html += '  <sinclo-div id="widgetHeader" class="notSelect" onclick="sinclo.operatorInfo.toggle()">';
      html += '  <sinclo-div id="titleWrap">';
      // タイトル
      html += '    <p id="widgetTitle">' + check.escape_html(widget.title) + '</p>';
      //ボタン差し替え対応
      html += '    <div id="minimizeBtn"></div>';
      html += '    <div id="closeBtn" onclick="sinclo.operatorInfo.closeBtn()"></div>';
      html += '  </sinclo-div>';
      var subTitle = (widget.subTitle === undefined && Number(widget.showSubtitle) === 1) ? "" : widget.subTitle;
      var description = (widget.description === undefined) ? "" : widget.description;
      if ( !chatAndTitleOnly && (Number(widget.showMainImage) === 1 || Number(widget.showSubtitle) === 1 || Number(widget.showDescription) === 1) ) {
        // サブタイトル
        if ( Number(widget.showSubtitle) === 1 && (widget.subTitle).length !== 0 ) {
          html += '    <p id="widgetSubTitle">' + check.escape_html(subTitle) + '</p>';
        }
        else {
          html += '    <p id="widgetSubTitle">&thinsp;</p>';
        }

        // 説明文
        if ( Number(widget.showDescription) === 1 && (widget.description).length !== 0 ) {
          html += '    <p id="widgetDescription">' + check.escape_html(description) + '</p>';
        }
        else {
          html += '    <p id="widgetDescription">&thinsp;</p>';
        }
      }

      html += '  </sinclo-div>';
      return html;
    },
    widgetNaviTemplate: function (widget) {
      var html = "";
      html += '  <section id="navigation" class="notSelect">';
      html += '    <ul>';
      html += '        <li data-tab="chat" class="widgetCtrl selected">チャットでの受付</li>';
      html += '        <li data-tab="call" class="widgetCtrl" >電話での受付</li>';
      html += '    </ul>';
      html += '  </section>';
      return html;
    },
    widgetTemplate: function (widget) {

      //サイズを取得
      var sizeList = this.getSizeType(widget.widgetSizeType);

      //カラーリストの取得
      var colorList = this.getColorList(widget);

      var html = "";

      // 電話・チャットプランの場合
      if ( window.sincloInfo.contract.chat && (window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document)) && !check.smartphone() ) {
        html += '<section id="callTab">';
      }
      // 電話のみプランの場合
      else {
        html += '<section id="callTab" class="flexBox">';
      }

      html += '    <sinclo-div style="height: 50px;margin: 15px 25px">';
      // アイコン
      html += '    <span style="display: block; width: 50px; height: 50px; float: left; background-color: ' + colorList['mainColor'] + '; border-radius: 25px; padding: 3px;"><img width="19.5" height="33" src="' + window.sincloInfo.site.files + '/img/call.png" style="width: 19.5px; height: 33px; margin: 6px 12px"></span>';
      // 受付電話番号
      if ( Number(widget.display_time_flg) === 1 ) {
        html += '    <pre id="telNumber" style="font-size: ' + sizeList['d18font'] + 'px; padding: 5px 0px 0px; height: 30px">' + check.escape_html(widget.tel) + '</pre>';
      }
      else {
        html += '    <pre id="telNumber" style="font-size: ' + sizeList['d20font'] + 'px; padding: 10px 0px 0px; height: 45px;">' + check.escape_html(widget.tel) + '</pre>';
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
    showVideoChatView: function (fromID, toID) {
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
    chatWidgetTemplate: function (widget) {
      var html = "", placeholder, spFlg = check.smartphone();
      // ボタンのみの場合
      placeholder = sinclo.chatApi.getPlaceholderMessage();
      html += '  <section id="chatTab" class="flexBox">';
      html += '    <ul id="chatTalk"><sinclo-chat></sinclo-chat><sinclo-typing></sinclo-typing><sinclo-chat-receiver><span id="receiveMessage">テストメッセージです</span></sinclo-chat-receiver><sinclo-loading></sinclo-loading></sinclo-loading></ul>';
      html += '    <sinclo-chat-alert>通信が切断されました。<br>こちらをタップすると再接続します。</sinclo-chat-alert>';
      html += '    <sinclo-div id="flexBoxWrap">';
      html += '      <sinclo-div class="flexBoxRow" id = "flexBoxHeight">';
      html += '        <textarea name="sincloChat" id="sincloChatMessage" maxlength="1000" placeholder=" ' + placeholder + ' "></textarea>';
      html += '        <a id="sincloChatSendBtn" class="notSelect" onclick="sinclo.chatApi.push()">送信</a>';
      html += '      </sinclo-div>';
      html += '      <sinclo-div class="flexBoxRow sinclo-hide" id = "miniFlexBoxHeight">';
      html += '        <input type="text" name="miniSincloChat" id="miniSincloChatMessage" maxlength="1000" placeholder=" ' + placeholder + ' "></input>';
      html += '        <a id="miniSincloChatSendBtn" class="notSelect" onclick="sinclo.chatApi.push()">送信</a>';
      html += '      </sinclo-div>';
      html += '    </sinclo-div>';
      if ( !check.smartphone() && (window.sincloInfo.contract.synclo || (window.sincloInfo.contract.hasOwnProperty('document') && window.sincloInfo.contract.document) || (check.isset(widget.showAccessId) && widget.showAccessId === 1)) ) {
        html += '    <span id="sincloAccessInfo">ウェブ接客コード：' + userInfo.accessId + '</span>';
      }
      html += '    <audio id="sinclo-sound">';
      html += '      <source src="' + window.sincloInfo.site.files + '/sounds/decision.mp3" type="audio/mp3">';
      html += '    </audio>';
      html += '  </section>';
      return html;
    },
    judgeShowWidget: function () {
      window.sincloInfo.widgetDisplay = null; // デフォルト表示しない
      // チャット契約、画面同期契約、画面同期中であれば表示
      if ( window.sincloInfo.contract.chat || window.sincloInfo.contract.synclo ) {
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
      // 常に表示する設定の値があれば必ず表示する
      if ( check.isset(window.sincloInfo.dataset) && (check.isset(window.sincloInfo.dataset.showAlways) && window.sincloInfo.dataset.showAlways === "1") ) {
        window.sincloInfo.widgetDisplay = true;
      }
      // 同期対象とするが、ウィジェットは表示しない
      if (check.isset(window.sincloInfo.dataset) && (check.isset(window.sincloInfo.dataset.hide) && window.sincloInfo.dataset.hide === "1")) {
        window.sincloInfo.widgetDisplay = false;
      }

      // ウィジェット表示タイミング
      var beforeJudgeDisplayFlg = window.sincloInfo.widgetDisplay;
      if ( window.sincloInfo.widgetDisplay ) {
        switch ( window.sincloInfo.widget.showTiming ) {
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

      if ( beforeJudgeDisplayFlg && window.sincloInfo.widgetDisplay ) {
        return window.sincloInfo.widgetDisplay;
      } else if ( beforeJudgeDisplayFlg && !window.sincloInfo.widgetDisplay ) {
        // 常に表示以外はウィジェットUI作成処理を継続させるためにtrueを返す
        // (window.sincloInfo.widgetDisplayのフラグは後続処理で利用する)
        return true;
      } else if ( !beforeJudgeDisplayFlg ) {
        // そもそも表示しない設定
        // FIXME 条件が冗長
        return window.sincloInfo.widgetDisplay;
      }
    },
    makeAccessIdTag: function () {
      if ( !check.browser() ) return false;
      if ( !('widget' in window.sincloInfo) ) return false;
      if ( !this.judgeShowWidget() ) {
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

        $("#sincloBox .widgetCtrl").click(function () {
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
        common.widgetHandler._handleResizeEvent();
        if ( window.sincloInfo.contract.chat && check.smartphone() ) {
          // 初期の画面向き
          if ( window.screen.availHeight < window.screen.availWidth ) {
            sincloBox.setAttribute('data-screen', 'horizontal'); // 横向き
          }
          else {
            sincloBox.setAttribute('data-screen', 'vertical'); // 縦向き
          }

          // スクロールをした時の処理群
          window.addEventListener('scroll', sinclo.operatorInfo.widgetHide);

          // 画面を回転ときは、向きによってスタイルを変える
          window.addEventListener('orientationchange', function () {
            //バナー表示だった
            var bannerAct = storage.l.get('bannerAct');
            if ( bannerAct === "true" ) {
              //強制的にバナー表示とする
              $("#sincloBannerBox").hide();
//                $("#sincloBox").css("height","");
//                sinclo.operatorInfo.onBanner();
            }
            sinclo.operatorInfo.reCreateWidget();
          });
          // サイズが変わった時は、サイズ感を変える
          window.addEventListener('resize', function (e) {
            if ( e ) e.stopPropagation();
            if ( common.isPortrait() || document.activeElement.id === "sincloChatMessage" ) return false; // 横向きの場合のみ使用
            sinclo.operatorInfo.reCreateWidget();
          });
        }
      }
    },
    toRGBAcolor: function (colorCode, opacity) {
      if ( colorCode.indexOf("#") >= 0 ) {
        var code = colorCode.substr(1), r, g, b;
        if ( code.length === 3 ) {
          r = String(code.substr(0, 1)) + String(code.substr(0, 1));
          g = String(code.substr(1, 1)) + String(code.substr(1, 1));
          b = String(code.substr(2)) + String(code.substr(2));
        }
        else {
          r = String(code.substr(0, 2));
          g = String(code.substr(2, 2));
          b = String(code.substr(4));
        }
        colorCode = "rgba(" + parseInt(r, 16) + ", " + parseInt(g, 16) + ", " + parseInt(b, 16) + ", " + opacity + ")";
      }
      return colorCode;
    },
    reconnectManual: function () {
      if ( socket ) {
        if ( socket.disconnected ) {
          socket.open();
          return true;
        } else {
          socket.close();
          socket.open();
          return true;
        }
      } else {
        // socketオブジェクトが無いためページを再読込
        storage.s.set('chatAct', false);
        return location.href = location.href;
      }
    },
    widgetHandler: {
      _currentWindowHeight: $(window).height(),
      // 通常呼び出し時はfalse or 引数指定なし（undefined）で呼び出す
      show: function (reCreateWidget) {
        console.log("widgetHandler::show");
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
        var bannerAct = storage.l.get('bannerAct');
        storage.s.set('bannerAct', bannerAct);
        //非表示の状態だった
        var closeAct = storage.s.get('closeAct');
        if ( bannerAct === "true" ) {
          //強制的にバナー表示とする
          $("#sincloBox").css("height", "");
          sinclo.operatorInfo.onBanner();
          //バナー表示フラグ設定をクリア
//          storage.s.unset("bannerAct");
        }
        if ( closeAct === "true" ) {
          //強制的に非表示とする
          //チャットを閉じる
          $("#sincloWidgetBox").hide();
          //非表示フラグ設定をクリア
//          storage.s.unset("closeAct");
        }
        if ( (common.widgetHandler.isShown() || window.sincloInfo.widgetDisplay)
          && sincloBox && (sincloBox.style.display === 'none' || sincloBox.style.display === '') ) {
          console.log('でろでろでろでろでろでろ');
          sincloBox.style.display = "block";
          //一旦非表示
          //ヘッダ非表示（シンプル表示）
          common.abridgementTypehide();
          common.widgetHandler.saveShownFlg();
          common.widgetHandler.stopToWatchResizeEvent();
          common.widgetHandler.beginToWatchResizeEvent();
          common.widgetHandler.beginToWatchTabletResize();
          // テキストエリアの表示非表示
          if ( !storage.l.get("textareaOpend") || storage.l.get("textareaOpend") === "open" ) {
            sinclo.displayTextarea();
          } else if ( storage.l.get("textareaOpend") === "close" ) {
            sinclo.hideTextarea();
          }
          var dataOpenflg = sinclo.widget.condifiton.get();
          //最小化時と最大化時の状態を取得
          var abridgementType = common.getAbridgementType();
          //ウィジェットの再生成処理呼び出しでなければ最小化表示設定で呼び出す
          if ( !reCreateWidget && dataOpenflg === "false" ) {
            sinclo.widget.condifiton.set(false, true);
            sinclo.chatApi.unlockPageScroll();
            //ログ書き込み用にメッセージ送信
            emit("sendWidgetShown", {widget: true});
            //最小化
            if ( abridgementType['MinRes'] ) {
              //ヘッダ非表示（シンプル表示）
              common.abridgementTypehide();
            }
            else {
              //ヘッダ表示（通常表示）
              common.abridgementTypeShow();
            }
            if ( bannerAct !== "true" ) {
              sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
            }
            //最小化時ボタン表示
            common.whenMinimizedBtnShow();
            common.widgetHandler._handleResizeEvent();
            // このタイミングでの最大化実行条件
            // １：PCの場合、ウィジェット最大化処理がウィジェット非表示時に実行されていた場合
            // ２：スマホの場合、ウィジェット最大化する設定が有効で、ウィジェット最大化処理がウィジェット非表示時に実行されていた場合
            if ( (!(check.smartphone() && sincloInfo.widget.hasOwnProperty('spAutoOpenFlg') && Number(sincloInfo.widget.spAutoOpenFlg) === 1) && storage.s.get('preWidgetOpened') === "true")
              || (!check.smartphone() && storage.s.get('preWidgetOpened') === "true") ) {
              //すでに最大化処理が呼び出されていたら最大化表示する
              sinclo.operatorInfo.ev();
              //最大化
              if ( abridgementType['MaxRes'] ) {
                //ヘッダ非表示（シンプル表示）
                common.abridgementTypehide();
              }
              else {
                //ヘッダ表示（通常表示）
                common.abridgementTypeShow();
              }
              //最大化時ボタン表示
              common.whenMaximizedBtnShow();
              if ( !check.smartphone() ) {
                common.widgetHandler._handleResizeEvent();
              } else {
                sinclo.adjustSpWidgetSize();
              }
            }
          }
          else {
            if ( dataOpenflg === "false" ) {
              console.log("saisyouka");
              //最小化
              if ( abridgementType['MinRes'] ) {
                //ヘッダ非表示（シンプル表示）
                common.abridgementTypehide();
              }
              else {
                //ヘッダ表示（通常表示）
                common.abridgementTypeShow();
              }
              //最小化時ボタン表示
              common.whenMinimizedBtnShow();
              sinclo.chatApi.unlockPageScroll();
              if ( !check.smartphone() ) {
                common.widgetHandler._handleResizeEvent();
              } else {
                sinclo.adjustSpWidgetSize();
              }
            }
            else {
              console.log("saidaika");
              //最大化
              if ( abridgementType['MaxRes'] ) {
                //ヘッダ非表示（シンプル表示）
                common.abridgementTypehide();
              }
              else {
                //ヘッダ表示（通常表示）
                common.abridgementTypeShow();
              }
              //最大化時ボタン表示
              common.whenMaximizedBtnShow();
              sinclo.chatApi.lockPageScroll();
              if ( !check.smartphone() ) {
                common.widgetHandler._handleResizeEvent();
              } else {
                sinclo.adjustSpWidgetSize();
              }
            }
          }
          //画像がない時のタイトル位置
          if ( $('#mainImage').css('display') === 'none' || $('#mainImage').css('display') === undefined ) {
            common.indicateSimpleNoImage();
          }
          //画像がある時のタイトル位置
          else if ( $('#mainImage').css('display') === 'block' || $('#mainImage').css('display') === 'inline' ) {
            common.indicateSimpleImage();
          }
        }
      },
      hide: function () {
        sincloBox.style.display = "none";
      },
      saveShownFlg: function () {
        storage.s.set("widgetShown", "true");
        storage.s.set("widgetShowTimingType", window.sincloInfo.widget.showTiming);
      },
      clearShownFlg: function () {
        storage.s.unset("widgetShown");
        storage.s.unset("widgetShowTimingType");
      },
      isShown: function () {
        return storage.s.get("widgetShown") === "true";
      },
      //サイト/ページ訪問時の設定
      getRemainingTimeMsec: function () {
        var remainingTime = 0;
        switch ( window.sincloInfo.widget.showTiming ) {
          case 1: //サイト
            remainingTime = this._calcRemainingShowTimingSiteTimeMsec();
            break;
          case 2: //ページ
            remainingTime = window.sincloInfo.widget.maxShowTimingPage * 1000;
            break;
        }
        return remainingTime;
      },
      resetMessageAreaState: function () {
        if ( Number(sincloInfo.widget.chatInitShowTextarea) === 1 ) {
          storage.l.set('textareaOpend', 'open');
        } else {
          storage.l.set('textareaOpend', 'close');
        }
      },
      _calcRemainingShowTimingSiteTimeMsec: function () {
        var siteAccessTimeMsec = (Number(userInfo.pageTime) - Number(userInfo.time)),
          showIntervalMsec = window.sincloInfo.widget.maxShowTimingSite * 1000;
        console.log("siteAccessTimeMsec " + siteAccessTimeMsec);
        console.log("showIntervalMsec" + showIntervalMsec);
        console.log("_calcRemainingShowTimingSiteTimeMsec: " + (siteAccessTimeMsec <= showIntervalMsec ? showIntervalMsec - siteAccessTimeMsec : 0));
        return siteAccessTimeMsec <= showIntervalMsec ? showIntervalMsec - siteAccessTimeMsec : 0;
      },
      beginToWatchTabletResize: function () {
        if ( check.smartphone() ) {
          return;
        }
        //タブレットの拡大縮小を取得する（スマホは対象外）
        $(window).on('touchstart', function () {
          console.log('タブレット画面サイズ監視開始');
        });

        $(window).on('touchend', function () {
          console.log('タブレット画面サイズ監視終了');
        });
      },
      beginToWatchResizeEvent: function () {
        if ( !check.smartphone() ) {
          console.log("widgetHandler::beginToWatchResizeEvent");
          $(window).on('resize.change_widget_size', common.widgetHandler._handleResizeEvent);
          // いったんリサイズ処理を走らせる
          common.widgetHandler._handleResizeEvent();
        }
      },
      stopToWatchResizeEvent: function () {
        if ( !check.smartphone() ) {
          console.log("widgetHandler::stopToWatchResizeEvent");
          $(window).off('resize.change_widget_size', common.widgetHandler._handleResizeEvent);
        }
      },
      _maximumReverseAnimation: function () {
        if ( check.smartphone() ) {
          return;
        }
        console.log('「最大」設定時に最小化するアニメーションです');
        $('#sincloWidgetBox').animate({
          width: "400px"
        });
      },
      _setFooterSize: function () {
        if ( check.isset(window.sincloInfo.custom) && check.isset(window.sincloInfo.custom.widget) && check.isset(window.sincloInfo.custom.widget.hideFotter) && window.sincloInfo.custom.widget.hideFotter ) {
          return 0;
        } else {
          return 26;
        }

      },
      _maximumAnimation: function () {
        if ( check.smartphone() ) {
          return;
        }
        console.log('「最大」設定時に最大化するアニメーションです');
        var footerSize = this._setFooterSize();
        var offset = $('#widgetHeader').outerHeight() + $('#flexBoxWrap').outerHeight() + $('#sincloAccessInfo').outerHeight() + footerSize;
        $('#chatTalk').css('height', $(window).height() - offset);
        $('#sincloWidgetBox').animate({
          width: $(window).width() + "px"
        }, 400);
      },
      _widgetFitForWindow: function () {
        console.log('<><><><><><><><><>最大設定!!!!<><><><><><><><><><>');
        //他のウィジェットサイズタイプとは大きく違うため、別の関数を用意しました。
        var footerSize = this._setFooterSize();
        var offset = $('#widgetHeader').outerHeight() + $('#flexBoxWrap').outerHeight() + $('#sincloAccessInfo').outerHeight() + footerSize;
        $('#chatTalk').css('height', $(window).height() - offset);
        if ( $('#minimizeBtn').is(':hidden') ) {
          //最大化時以外は横幅400px
          $('#sincloWidgetBox').css('width', "400px");
          return;
        }
        $('#sincloWidgetBox').css('width', $(window).width() + "px");
        $('#chatTab').css('width', "100%");
      },
      _handleResizeEvent: function () {
        console.log("<><><><><><><><><>widgetHandler::_handleResizeEvent");
        if ( storage.s.get('widgetMaximized') === "true" ) {
          $('#sincloBox').css('height', 'auto');
        }
        if ( Number(sincloInfo.widget.widgetSizeType) === 4 && !check.smartphone() ) {
          common.widgetHandler._widgetFitForWindow();
          return;
        }
        var windowHeight = $(window).innerHeight(),
          minCurrentWidgetHeight = common.widgetHandler._getMinWidgetHeight(),
          currentWidgetHeight = $('#sincloWidgetBox').height(),
          maxCurrentWidgetHeight = common.widgetHandler._getMaxWidgetHeight(),
          changeTarget = ($('#chatTab').length > 0) ? $('#chatTalk') : $('#telContent'),
          delta = windowHeight - common.widgetHandler._currentWindowHeight;
        if ( windowHeight * 0.85 > maxCurrentWidgetHeight ) {
          changeTarget.height(common.widgetHandler._getMaxChatTalkHeight());
          return;
        }
        if ( windowHeight * 0.85 < currentWidgetHeight && delta === 0 ) {
          delta = (windowHeight * 0.85) - currentWidgetHeight;
        }

        // 変更後サイズ
        var afterWidgetHeight = $('#sincloWidgetBox').height() + delta;
        if ( delta > 0 && afterWidgetHeight > maxCurrentWidgetHeight ) {
          console.log('<><><><><><><><><>1 %s %s %s', delta, afterWidgetHeight, maxCurrentWidgetHeight);
          changeTarget.height(common.widgetHandler._getMaxChatTalkHeight());
        } else if ( delta < 0 && afterWidgetHeight < minCurrentWidgetHeight ) {
          console.log('<><><><><><><><><>2 %s %s %s', delta, afterWidgetHeight, minCurrentWidgetHeight);
          changeTarget.height(common.widgetHandler._getMinChatTalkHeight());
        } else if ( (delta < 0 && windowHeight * 0.85 < currentWidgetHeight) || (delta > 0 && windowHeight * 0.85 >= afterWidgetHeight) ) {
          console.log('<><><><><><><><><>3 %s %s %s %s', delta, windowHeight, currentWidgetHeight, afterWidgetHeight);
          changeTarget.height(changeTarget.height() + delta);
        }
        common.widgetHandler._currentWindowHeight = windowHeight;
        $('#sincloWidgetBox').offset({top: $('#sincloBox').offset().top});
      },
      _getMaxWidgetHeight: function () {
        var offset = common.widgetHandler._getMessageAreaOffset();
        switch ( Number(sincloInfo.widget.widgetSizeType) ) {
          case 1:
            return 405 - offset;
          case 2:
            return 496 - offset;
          case 3:
            return 596 - offset;
          default:
            return 496 - offset;
        }
      },
      _getMinWidgetHeight: function () {
        var offset = common.widgetHandler._getMessageAreaOffset();
        switch ( Number(sincloInfo.widget.widgetSizeType) ) {
          case 1:
            return 318 - offset;
          case 2:
            return 364 - offset;
          case 3:
            return 409 - offset;
          default:
            return 364 - offset;
        }
      },
      _getMaxChatTalkHeight: function () {
        var offset = common.widgetHandler._getMessageAreaOffset(true);
        if ( $('#chatTab').length > 0 ) {
          switch ( Number(sincloInfo.widget.widgetSizeType) ) {
            case 1:
              // 小
              return 194 + offset;
            case 2:
              return 284 + offset;
            case 3:
              return 374 + offset;
            default:
              return 284 + offset;
          }
        } else {
          // シェアリング
          switch ( Number(sincloInfo.widget.widgetSizeType) ) {
            case 1:
              // 小
              return 125;
            case 2:
              return 214.5;
            case 3:
              return 305;
            default:
              return 284;
          }
        }
      },
      _getMinChatTalkHeight: function () {
        var offset = common.widgetHandler._getMessageAreaOffset(true);
        if ( $('#chatTab').length > 0 ) {
          switch ( Number(sincloInfo.widget.widgetSizeType) ) {
            case 1:
              // 小
              return 97 + offset;
            case 2:
              return 142 + offset;
            case 3:
              return 187 + offset;
            default:
              return 142 + offset;
          }
        } else {
          // シェアリング
          switch ( Number(sincloInfo.widget.widgetSizeType) ) {
            case 1:
              // 小
              return 32;
            case 2:
              return 76;
            case 3:
              return 121;
            default:
              return 76;
          }
        }
      },
      _getMessageAreaOffset: function (forChatTalkOffset) {
        var invisibleUIOffset = 0;
        if ( !forChatTalkOffset ) {
          if ( !$('#sincloAccessInfo').is(':visible') ) {
            invisibleUIOffset += 26.5;
          }
          if ( !$('#sincloWidgetBox #footer').is(':visible') ) {
            invisibleUIOffset += 26.5;
          }
          if ( !$('#widgetSubTitle').is(':visible') && !$('#widgetDescription').is(':visible') ) {
            invisibleUIOffset += 53;
          }
        }
        if ( !$('#flexBoxWrap').is(':visible') ) {
          // 非表示
          if ( forChatTalkOffset ) {
            return 75;
          } else {
            return 0 + invisibleUIOffset;
          }
        } else if ( $('#flexBoxHeight').is(':visible') ) {
          return 0 + invisibleUIOffset;
        } else if ( $('#miniFlexBoxHeight').is(':visible') ) {
          if ( forChatTalkOffset ) {
            return 27 + invisibleUIOffset;
          } else {
            return 0 + invisibleUIOffset;
          }
        } else {
          // とりあえず表示されている状態
          return 0 + invisibleUIOffset;
        }
      },
      openWidget: function() {
        storage.s.set('closeAct', false);
        $('#sincloBox').show().css('height', 'auto');
        $('#sincloWidgetBox').show();
        common.whenMaximizedBtnShow();
        common.widgetHandler._handleResizeEvent();
      }
    },
    load: {
      id: "loadingImg",
      flg: false,
      timer: null,
      loadingHtml: function () {
        var html = "";
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
      start: function () {
        window.clearTimeout(this.timer);
        var div = document.createElement('div');
        div.id = this.id;
        div.style.cssText = "position: fixed; top: 0; left: 0; bottom: 0; right: 0; background-color: rgba(68,68,68,0.7); z-index: 99999";
        var html = this.loadingHtml();
        div.innerHTML = html;
        document.body.appendChild(div);
        this.flg = true; // 一度接続済みというフラグを持たせる
        this.timer = window.setTimeout(function () {
          common.load.finish();
        }, 5000);
      },
      finish: function () {
        window.clearTimeout(this.timer);
        if ( document.getElementById(this.id) ) {
          var target = document.getElementById(this.id);
          target.parentNode.removeChild(target);
          if ( document.getElementById(this.id) ) {
            this.finish();
          }
        }
      }
    },
    chatBotTypingDelayTimer: null,
    firstTimeChatBotTyping: true,
    chatBotTypingCall: function (obj) {
      console.log("sinclo.scenarioApi._bulkHearing.isInMode() %s", sinclo.scenarioApi._bulkHearing.isInMode());
      if ( !common.chatBotTypingDelayTimer || obj.messageType === sinclo.chatApi.messageType.sorry ) {
        common.chatBotTypingDelayTimer = setTimeout(function () {
          common.chatBotTyping(obj);
          common.chatBotTypingDelayTimer = null;
        }, 850)
      }
    },
    chatBotTypingTimerClear: function () {
      console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>chatBotTypingTimerClear');
      clearTimeout(common.chatBotTypingDelayTimer);
      common.chatBotTypingDelayTimer = null;
    },
    chatBotTyping: function (obj) {
      console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>waitAnimationFuncStart');
      //予期せぬエラーを回避するため、ローディングの重複表示を避ける
      if ( $(".botNowDiv").length > 0 ) {
        return;
      }
      //チャットが発言内容によるオートメッセージ・シナリオ発動
      //シナリオ中のヒアリング・ファイル受信・選択肢である場合
      //ウェイトアニメーションを表示するという処理
      console.log(obj);
      if ( obj == null ) {
        return;
      } else if ( obj.forceWaitAnimation ) {
      } else if ( obj.messageType === sinclo.chatApi.messageType.customer ) {
        if ( !obj.matchAutoSpeech ) {
          return;
        } else if ( obj.isScenarioMessage ) {
          return;
        }
      }else if(obj.messageType === sinclo.chatApi.messageType.autoSpeech
             ||obj.messageType === sinclo.chatApi.messageType.auto
             ||obj.messageType === sinclo.chatApi.messageType.company
             ||obj.messageType === sinclo.chatApi.messageType.end
             ||obj.messageType === sinclo.chatApi.messageType.notification
             ||obj.messageType === sinclo.chatApi.messageType.start
             ||obj.messageType === sinclo.chatApi.messageType.cogmo.message
             ||obj.messageType === sinclo.chatApi.messageType.cogmo.feedback){
        return;
      } else if ( obj.messageType === sinclo.chatApi.messageType.scenario.message.hearing
        || obj.messageType === sinclo.chatApi.messageType.scenario.message.pulldown
        || obj.messageType === sinclo.chatApi.messageType.scenario.message.calendar
        || obj.messageType === sinclo.chatApi.messageType.scenario.message.selection ) {
        return;
      } else if ( obj.messageType === sinclo.chatApi.messageType.scenario.message.text
        || obj.messageType === sinclo.chatApi.messageType.scenario.message.receiveFile
        || obj.messageType === sinclo.chatApi.messageType.scenario.customer.sendFile) {
        if ( !sinclo.scenarioApi.isProcessing() ) {
          return;
        }
      }
      var widget = window.sincloInfo.widget;
      var sizeList = common.getSizeType(widget.widgetSizeType);
      var fontSize = widget.reTextSize;
      var waitHeight = 1.4 * fontSize + 20;
      var waitWidth = waitHeight * 2;
      var waitPadding = waitWidth * 0.172;
      var loadDotSize = fontSize * 0.8;
      var heightWeight = widget.widgetSizeType - 3;
      var html = "";
      html += "<div class='botNowDiv'>";
      //ウィジェットサイズが小で余白がない場合のみ、特殊なクラスを設ける
      if ( !check.smartphone() && widget.widgetSizeType === 1 && $('#chatTalk').get(0).offsetHeight < $('#chatTalk').get(0).scrollHeight ) {
        html += "<li class='effect_left_wait botDotOnlyTyping'>";
        html += "  <div class='reload_only_dot_left'></div>";
        html += "  <div class='reload_only_dot_center'></div>";
        html += "  <div class='reload_only_dot_right'></div>";
      } else {
        //スマホかウィジェットサイズが大以上の場合
        if ( check.smartphone() || widget.widgetSizeType === 3 || widget.widgetSizeType === 4 ) {
          html += "<li class='effect_left_wait botNowTypingLarge'>";
          //ウィジェットサイズが中の場合
        } else if ( widget.widgetSizeType === 2 ) {
          html += "<li class='effect_left_wait botNowTypingMedium'>";
          //ウィジェットサイズが小の場合
        } else if ( widget.widgetSizeType === 1 ) {
          html += "<li class='effect_left_wait botNowTypingSmall'>";
        }
        html += "    <div class='reload_dot_left'></div>";
        html += "    <div class='reload_dot_center'></div>";
        html += "    <div class='reload_dot_right'></div>";
      }
      html += "  </li>";
      html += "</div>";

        var css  = "";
            //ドットのサイズは共通
            css += "#sincloBox ul#chatTalk div[class^='reload']{";
            css += "  min-width:"+loadDotSize+"px;width:"+loadDotSize+"px;min-height:"+loadDotSize+"px;height:"+loadDotSize+"px;border-radius:100%;";
            css += "}";
            //吹き出しがある場合はテキストカラーを採用
            css += "#sincloBox ul#chatTalk div[class^='reload_dot']{";
            if(window.sincloInfo.widget.isSendMessagePositionLeft) {
              css += "  background-color:"+widget.seTextColor+";";
            } else {
              css += "  background-color:"+widget.reTextColor+";";
            }
            css += "}";
            //吹き出しがない場合はメインカラー、または吹き出し背景色を採用
            if(widget.mainColor == "#FFFFFF"){
              css += "#sincloBox ul#chatTalk div[class^='reload_only_dot']{";
              if(window.sincloInfo.widget.isSendMessagePositionLeft) {
                css += "  background-color:"+widget.seBackgroundColor+";";
              } else {
                css += "  background-color:"+widget.reBackgroundColor+";";
              }
              css += "}";
            }else{
              css += "#sincloBox ul#chatTalk div[class^='reload_only_dot']{";
              css += "  background-color:"+widget.mainColor+";";
              css += "}";
            }
            css += "#sincloBox ul#chatTalk div[class$='left']{";
            css += "  animation:dotScale 1.0s ease-in-out -0.32s infinite both";
            css += "}";
            css += "#sincloBox ul#chatTalk div[class$='center']{";
            css += "  animation:dotScale 1.0s ease-in-out -0.16s infinite both";
            css += "}";
            css += "#sincloBox ul#chatTalk div[class$='right']{";
            css += "  animation:dotScale 1.0s ease-in-out 0s infinite both";
            css += "}";
            if(widget.chatMessageWithAnimation === 1){
              if(window.sincloInfo.widget.isSendMessagePositionLeft) {
                css += "#sincloBox ul#chatTalk div.botNowDiv { text-align: right; }";
                css += "#sincloBox ul#chatTalk li.effect_left_wait { -webkit-animation-name:rightEffect; animation-name:rightEffect; -webkit-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; animation-fill-mode:both; -webkit-transform-origin:left bottom; transform-origin:left bottom; opacity:0;}";
              } else {
                css += "#sincloBox ul#chatTalk li.effect_left_wait { -webkit-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; animation-fill-mode:both; -webkit-transform-origin:left bottom; transform-origin:left bottom; opacity:0;}";
              }
            }else{
              if(window.sincloInfo.widget.isSendMessagePositionLeft) {
                css += '#sincloBox ul#chatTalk li.effect_left_wait { -webkit-animation-name:noneRightEffect; animation-name:noneRightEffect; -webkit-animation-duration:1ms; animation-duration:1ms; -webkit-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; animation-fill-mode:both; opacity:0;}';
              } else {
                css += '#sincloBox ul#chatTalk li.effect_left_wait { -webkit-animation-name:noneLeftEffect; animation-name:noneLeftEffect; -webkit-animation-duration:1ms; animation-duration:1ms; -webkit-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; animation-fill-mode:both; opacity:0;}';
              }
            }
            //吹き出しの大きさをウィジェットタイプで変える
            //基準(共通)の設定
            css += "#sincloBox ul#chatTalk li[class*='botNowTyping']{";
            css += "  display:inline-flex;justify-content:space-around;align-items:center;border-radius:12px!important;";
            css += "  width:"+waitWidth+"px;padding:0 "+waitPadding+"px;margin-left: 10px;";
            if(window.sincloInfo.widget.isSendMessagePositionLeft) {
              css += "  background-color:"+widget.seBackgroundColor+";";
            } else {
              css += "  background-color:"+widget.reBackgroundColor+";";
            }
            css += "}";
            //小(余白あり)の場合
            css += "#sincloBox ul#chatTalk li.botNowTypingSmall{";
            css += "  height:"+(waitHeight-8)+"px;"
            css += "}";
            //中の場合
            css += "#sincloBox ul#chatTalk li.botNowTypingMedium{";
            css += "  height:"+(waitHeight-8)+"px;"
            css += "}";
            //大の場合
            css += "#sincloBox ul#chatTalk li.botNowTypingLarge{";
            css += "  height:"+waitHeight+"px;"
            css += "}";
            //小(余白なし)の場合は、色と大きさが変化する
            css += "#sincloBox ul#chatTalk li.botDotOnlyTyping{";
            css += "  display:flex;justify-content:space-around;align-items:center;border-radius:12px!important;";
            css += "  width:"+waitWidth+"px;height:"+(waitHeight-21)+"px;;padding:0 "+waitPadding+"px;margin-left: 10px;";
            css += "  background-color:"+widget.chatTalkBackgroundColor+";";
            css += "}";
            //共通アニメーションの設定
            css += "@keyframes dotScale{";
            css += "   0%,100%{transform: scale(0.4);opacity:0.3}";
            css += "  30%,70%{opacity:0.7}";
            css += "  50%{transform: scale(1);opacity:1.0}";
            css += "}";

      //一回も呼び出されていなかった場合のみCSSを追加する
      if ( common.firstTimeChatBotTyping ) {
        $("#sincloBox > style").append(css);
        common.firstTimeChatBotTyping = false;
      }
      $("sinclo-chat").append(html);
      console.log('waitAnimationAdded!');
      return;
    },
    chatBotTypingRemove: function () {
      //ウェイトアニメーションが存在しない場合はリターンする
      //エラーを防ぐため
      if ( $(".botNowDiv").length == 0 ) {
        return;
      }
      $('div.botNowDiv').remove();
    },
    reloadWidget: function () {
      var widget = window.sincloInfo.widget;
      var sizeList = common.getSizeType(widget.widgetSizeType);

      if ( check.smartphone() ) {
        var widgetWidth;
        if ( sincloInfo.widget.spMaximizeSizeType === 2 ) {
          widgetWidth = $(window).width();
        } else {
          widgetWidth = $(window).width() - 20;
        }
        var coverWidth = widgetWidth;
      } else {
        var coverWidth = parseInt(sizeList.boxWidth);
      }
      var coverHeight = $('#chatTalk').outerHeight() + $('#flexBoxHeight').outerHeight();
      var loadPadding = Number(widget.widgetSizeType) * 29 + 61;

      var html = "";
      html += "<div class='reloadCover'>";
      html += "  <div class='reload_dot_left'></div>";
      html += "  <div class='reload_dot_center'></div>";
      html += "  <div class='reload_dot_right'></div>";
      html += "</div>";

      var css  = "";
          css += "#sincloBox .reloadCover div[class^='reload_dot']{";
          css += "  width:18px;height:18px;border-radius:100%;";
          if(widget.mainColor == widget.chatTalkBackgroundColor){
            if(window.sincloInfo.widget.isSendMessagePositionLeft) {
              css += "  background-color:"+widget.seBackgroundColor+";";
            } else {
              css += "  background-color:"+widget.reBackgroundColor+";";
            }
          }else{
            css += "  background-color:"+widget.mainColor+";";
          }
          css += "}";
          css += "#sincloBox .reloadCover div[class$='left']{";
          css += "  animation:dotScale 1.4s ease-in-out -0.32s infinite both";
          css += "}";
          css += "#sincloBox .reloadCover div[class$='center']{";
          css += "  animation:dotScale 1.4s ease-in-out -0.16s infinite both";
          css += "}";
          css += "#sincloBox .reloadCover div[class$='right']{";
          css += "  animation:dotScale 1.4s ease-in-out 0s infinite both";
          css += "}";
          css += "#sincloBox div.reloadCover{";
          css += "  position:absolute;z-index:1000;display:flex;justify-content:space-around;align-items:center;";
          css += "  background-color:"+widget.chatTalkBackgroundColor+";";
          css += "  width:"+coverWidth+"px;height:"+coverHeight+"px;padding:0 "+loadPadding+"px;"
          css += "}";
          css += "@keyframes dotScale{";
          css += "   0%,80%,100%{transform: scale(0);}";
          css += "  40%{transform: scale(1);}";
          css += "}";
      $("#sincloBox > style").append(css);
      $("#chatTab").prepend(html);
      return;
    },
    reloadWidgetRemove: function () {
      $("div.reloadCover").remove();
    },
    waitDelayTimer: function () {
      return 20;
    },
    stringReplaceProcessForGA: function (link) {
      console.log('GA連携用に電話番号とメールアドレスの修正を行います');
      /*href属性値のみ取得*/
      var sliceStart = link.indexOf('"') + 1;
      var sliceEnd = link.indexOf('"', sliceStart);
      link = link.slice(sliceStart, sliceEnd);
      link = link.replace("mailto:", "");
      link = link.replace("tel:", "");
      return link;
    }
  };

  storage = {
    s: {
      get: function (name) {
        return sessionStorage.getItem(name);
      },
      set: function (name, val) {
        sessionStorage.setItem(name, val);
      },
      unset: function (name) {
        sessionStorage.removeItem(name);
      }
    },
    l: {
      get: function (name) {
        return localStorage.getItem(name);
      },
      set: function (name, val) {
        localStorage.setItem(name, val);
      },
      unset: function (name) {
        localStorage.removeItem(name);
      }
    },
    c: {
      prefix: '___',
      get: function (name) {
        var cookies = document.cookie;
        var cookieItem = cookies.split(";");
        var cookieValue = "";

        for ( var i = 0; i < cookieItem.length; i++ ) {
          var elem = cookieItem[i].split("=");
          if ( elem[0].trim() === this.prefix + name ) {
            cookieValue = decodeURIComponent(elem[1]);
          } else {
            continue;
          }
        }
        return cookieValue;
      },
      set: function (name, val) {
        document.cookie = this.prefix + name + '=' + encodeURIComponent(val) + '; path=/';
      },
      unset: function (name) {
        localStorage.removeItem(name);
      }
    }
  };

  check = {
    browser: function () {
      var ret = false;
      // 消費者のみ、ローカルストレージとセッションストレージが使用できる環境のみ
      if ( window.localStorage && window.sessionStorage ) {
        if ( !check.isset(common.tmpParams) && !check.isset(storage.s.get('params')) ) {
          ret = true;
        }
      }
      var ua = navigator.userAgent.toLowerCase();
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
    smartphone: function () {
      var ua = navigator.userAgent.toLowerCase();
      // iPhone/iPod/Androidのみ有効のロジック
      return (ua.indexOf('iphone') > 0 || ua.indexOf('ipod') > 0 || ua.indexOf('android') > 0);
    },
    android: function () {
      var ua = navigator.userAgent.toLowerCase();
      return ua.indexOf('android') > 0;
    },
    isset: function (a) {
      if ( a === null || a === '' || a === undefined || String(a) === "null" || String(a) === "undefined" ) {
        return false;
      }
      if ( typeof a === "object" ) {
        var keys = Object.keys(a);
        return (Object.keys(a).length !== 0);
      }
      return true;
    },
    isJSON: function (arg) {
      arg = (typeof arg === "function") ? arg() : arg;
      if ( typeof arg !== "string" ) {
        return false;
      }
      try {
        arg = (!JSON) ? eval("(" + arg + ")") : JSON.parse(arg);
        return true;
      } catch (e) {
        return false;
      }
    },
    isIE: function () {
      var userAgent = window.navigator.userAgent;

      if ( (userAgent.indexOf('MSIE') > -1) || (userAgent.indexOf('Trident/') > -1) ) {
        return true;
      }
      return false;
    },
    // エスケープ用
    // http://qiita.com/saekis/items/c2b41cd8940923863791
    escape_html: function (string) {
      if ( typeof string !== 'string' ) {
        return string;
      }
      var str = string.replace(/(<br>|<br \/>)/gi, '\n');
      str = str.replace(/[&'`"<>]/g, function (match) {
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
    firstUrl: function () {
      if ( location.href.match('/sincloData\=/') ) {
        return true;
      }
      else {
        return false;
      }
    },
    ref: function () {
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
    customVariables: {},
    init: function () {
      // トークン初期化
      common.token_add();
      // ストレージの内容をオブジェクトに格納
      this.globalSet();
      // ストレージにリファラーのセット
      this.setPrevpage();

      common.getParams();

      userInfo.setCustomVariables();

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
      set: function () {
        storage.s.set(this.code, JSON.stringify({
          sendTabId: userInfo.sendTabId
        }));
      },
      get: function () {
        var syncInfo = common.jParse(storage.s.get(this.code));
        if ( check.isset(syncInfo) && check.isset(syncInfo.sendTabId) ) {
          userInfo.sendTabId = syncInfo.sendTabId;
        }

      },
      unset: function () {
        storage.s.unset(this.code);
        delete userInfo.sendTabId;
        // TODO minify
        userInfo.unsetConnect();
      }
    },
    getCode: function (type) {
      switch ( type ) {
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
    set: function (type, val, session) {
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
    get: function (type) {
      var code = this.getCode(type);
      if ( check.isset(storage.l.get(code)) ) {
        return storage.l.get(code);
      }
      else if ( check.isset(storage.s.get(code)) ) {
        return storage.s.get(code);
      } else if ( check.isset(storage.c.get(code)) ) {
        return storage.c.get(code);
      }
    },
    unset: function (type) {
      var code = this.getCode(type);
      userInfo[code] = null;
      if ( check.isset(storage.l.get(code)) ) {
        storage.l.unset(code);
      }
      if ( check.isset(storage.s.unset(code)) ) {
        storage.s.unset(code);
      }
    },
    globalSet: function () {
      var array = Object.keys(cnst.info_type);
      for ( var i in array ) {
        var code = this.getCode(cnst.info_type[array[i]]);
        if ( check.isset(storage.l.get(code)) ) {
          userInfo[code] = storage.l.get(code);
        }
        else if ( check.isset(storage.s.get(code)) ) {
          userInfo[code] = storage.s.get(code);
        } else if ( check.isset(storage.c.get(code)) ) {
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
      for ( var i in array ) {
        userInfo.unset(array[i]);
      }
    },
    getUserId: function () {
      return this.get(cnst.info_type.user);
    },
    getTabId: function () {
      return this.get(cnst.info_type.tab);
    },
    getAccessId: function () {
      return this.get(cnst.info_type.access);
    },
    getIp: function () {
      return this.get(cnst.info_type.ip);
    },
    getTime: function () {
      return this.get(cnst.info_type.time);
    },
    getConnect: function () {
      return this.get(cnst.info_type.connect);
    },
    getStayCount: function () {
      var code = this.getCode(cnst.info_type.staycount);
      return Number(storage.l.get(code));
    },
    setStayCount: function () {
      var code = this.getCode(cnst.info_type.staycount),
        cnt = Number(storage.l.get(code)) + 1;
      storage.l.set(code, cnt);
    },
    setReferrer: function () {
      var code = this.getCode(cnst.info_type.referrer);

      // IE8対応コード
      if ( userInfo.referrer === null ) {
        if ( check.isset(document.referrer) ) {
          storage.s.set(code, document.referrer);
        }
      }
    },
    setPrevpage: function (reset) {
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
    writePrevToLocalStorage: function () {
      var code = this.getCode(cnst.info_type.prev);
      var prev = [];
      if ( typeof(userInfo.oldSincloSessionId) !== 'undefined' ) {
        console.log("oldSincloSessionId is found. : " + userInfo.oldSincloSessionId + " overwrite.");
        prev = common.jParse(storage.s.get(code));
        storage.l.set(code, JSON.stringify(prev));
      } else {
        prev = common.jParse(storage.l.get(code));
        if ( !check.isset(prev) ) {
          prev = [];
        }
        // IE8対応コード
        if ( prev.length === 0 || location.href !== prev[prev.length - 1].url ) {
          prev.push({url: location.href, title: common.title()});
          storage.l.set(code, JSON.stringify(prev));
        }
      }
      return prev;
    },
    setConnect: function (val) {
      this.set(cnst.info_type.connect, val, true);
    },
    setTabId: function () {
      var val = userInfo.userId + "_" + common.makeToken();
      this.set(cnst.info_type.tab, val, true);
    },
    changeTabId: function (tabId) {
      console.log("CHANGE TAB ID");
      this.set(cnst.info_type.tab, tabId, true);
    },
    unsetAccessId: function () {
      return this.unset(cnst.info_type.access);
    },
    unsetConnect: function () {
      return this.unset(cnst.info_type.connect);
    },
    getSendList: function () {
      var code = this.getCode(cnst.info_type.prev);
      var prev = common.jParse(storage.l.get(code));
      console.log("<><><><><><><><><><> getSendList <><><>><<><><><><><><><><><><><>");
      return {
        ipAddress: this.getIp(),
        time: this.getTime(),
        prev: prev ? prev : this.prev,
        stayCount: this.getStayCount(),
        referrer: this.referrer,
        userAgent: window.navigator.userAgent,
        chatCnt: document.getElementsByClassName('sinclo_se').length,
        chatUnread: {id: null, cnt: 0},
        service: check.browser(),
        widget: window.sincloInfo.widgetDisplay,
        customVariables: window.userInfo.customVariables
      };
    },
    setCustomVariables: function () {
      if ( sincloInfo.customVariable.length > 0 ) {
        try {
          for ( var i = 0; i < sincloInfo.customVariable.length; i++ ) {
            var getValue = userInfo._getText($(sincloInfo.customVariable[i].attribute_value));
            if ( getValue ) {
              userInfo.customVariables[sincloInfo.customVariable[i].item_name] = getValue.trim();
            }
          }
        } catch (e) {

        }
      }
    },
    _getText: function (jqObject) {
      if ( jqObject.text() !== "" ) {
        return jqObject.text();
      } else if ( jqObject.val() !== "" ) {
        return jqObject.val();
      } else {
        return "";
      }
    }
  };

  browserInfo = {
    connectFlg: false,
    referrer: "",
    href: location.href,
    prevList: [],
    // TODO 画面同期時セットするようにする
    scrollSize: function () { // 全体のスクロール幅
      return {
        x: document.body.scrollWidth - window.innerWidth,
        y: document.body.scrollHeight - window.innerHeight
      };
    },
    // TODO 画面同期時セットするようにする
    sc: function () { // スクロール量を取得する先
      if ( document.body.scrollTop > document.documentElement.scrollTop || document.body.scrollLeft > document.documentElement.scrollLeft ) {
        return document.body;
      }
      else {
        return document.documentElement;
      }
    },
    resetPrevList: function () {
      var prevList = [];
      prevList.push(this.href);
      this.prevList = prevList;
      storage.s.set('prevList', JSON.stringify(this.prevList));
    },
    setPrevList: function () {
      var prevList = [];
      if ( check.isset(storage.s.get('prevList')) ) {
        prevList = JSON.parse(storage.s.get('prevList'));
      }
      prevList.push(this.href);
      this.prevList = prevList;
      storage.s.set('prevList', JSON.stringify(this.prevList));
    },
    windowScroll: function () {
      var customDoc = browserInfo.sc();
      var scrollSize = browserInfo.scrollSize();
      var x = (customDoc.scrollLeft / scrollSize.x);
      var y = (customDoc.scrollTop / scrollSize.y);
      return {
        x: (isNaN(x)) ? 0 : x,
        y: (isNaN(y)) ? 0 : y
      };
    },
    windowScreen: function () {
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
    windowSize: function () {
      return {
        height: window.innerHeight,
        width: window.innerWidth
      };
    },
    interval: Math.floor(1000 / 60 * 10),
    set: {
      scroll: function (obj) {
        var scrollSize = browserInfo.scrollSize();

        document.body.scrollLeft = scrollSize.x * obj.x;
        document.body.scrollTop = scrollSize.y * obj.y;
        document.documentElement.scrollLeft = scrollSize.x * obj.x;
        document.documentElement.scrollTop = scrollSize.y * obj.y;
      }
    },
    getActiveWindow: function () {
      var tabFlg = document.hasFocus(), widgetFlg = false, tabStatus, sincloBox;
      //バナー表示かどうか　bannerAct === "true"だったらバナー表示
      var bannerAct = storage.l.get('bannerAct');
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
        if ( !sincloBox || (sincloBox && sincloBox.style.display !== "block") ) {
          tabStatus = cnst.tab_type.none;
        }
        //バナー表示中
        if ( bannerAct === "true" ) {
          tabStatus = cnst.tab_type.none;
        }
        //閉じるボタンによる非表示中
        if ( closeAct === "true" ) {
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
        timer: null,
        ev: function (e) {
          if ( e ) e.stopPropagation();
          if ( this.timer ) {
            return false;
          }
          this.timer = setTimeout(function () {
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
        ev: function (e) {
          if ( e ) e.stopPropagation();
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
        ev: function (e) {
          if ( e ) e.stopPropagation();
          if ( socket === undefined ) return false;
          browserInfo.href = location.href;
          emit('reqUrlChecker', {});
        }
      }
    ],
    pcResize: function (e) {
      if ( e ) e.stopPropagation();
      if ( syncEvent.resizeTimer !== false ) {
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
    tabletResize: function (e) {
      if ( e ) e.stopPropagation();
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
    ctrlEventListener: function (eventFlg, evList) { // ウィンドウに対してのイベント操作

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
        var evName = (attachFlg) ? "on" + String(evList[Number(i)].type) : String(evList[Number(i)].type);
        var event = evList[Number(i)].ev;
        evListener(evName, event);
      }
    },
    ctrlElmEventListener: function (eventFlg, els, type, ev) {
      var evName, attachFlg = true;
      if ( eventFlg ) {
        if ( window.addEventListener ) attachFlg = false;
      }
      else {
        if ( window.removeEventListener ) attachFlg = false;
      }
      evName = (attachFlg) ? "on" + String(type) : type;
      for ( var i = 0; i < els.length; i++ ) {
        if ( eventFlg && attachFlg ) {
          els[i].attachEvent(evName, ev, false);
        }
        else if ( eventFlg && !attachFlg ) {
          els[i].addEventListener(evName, ev, false);
        }
        else if ( !eventFlg && attachFlg ) {
          els[i].detachEvent(evName, ev, false);
        }
        else if ( !eventFlg && !attachFlg ) {
          els[i].removeEventListener(evName, ev, false);
        }
      }
    },
    changeCall: function (e) {
      var nodeName = e.target.nodeName.toLowerCase(),
        checked = false,
        index = $(String(nodeName)).index(this);
      if ( nodeName !== "input" && nodeName !== "textarea" && nodeName !== "select" ) return false;
      if ( e.target.type === "radio" || e.target.type === "checkbox" ) {
        checked = e.target.checked;
      }
      // 排他処理
      if ( nodeName === String(syncEvent.receiveEvInfo.nodeName) && Number(index) === Number(syncEvent.receiveEvInfo.idx) ) return false;
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
    focusCall: function (e) {
      this.addEventListener('keyup', syncEvent.changeCall, false);
      this.addEventListener('change', syncEvent.changeCall, false);
    },
    resizeCall: function (ua, eventFlg) {
      if ( !eventFlg ) {
        window.removeEventListener("resize", syncEvent.pcResize);
        window.removeEventListener("orientationchange", syncEvent.tabletResize);
        return false;
      }
      // ウィンドウリサイズは消費者の状態のみ反映
      if ( Number(userInfo.accessType) !== Number(cnst.access_type.guest) ) return false;
      if (
        ((ua.indexOf("windows") != -1 && ua.indexOf("touch") != -1) ||
          ua.indexOf("ipad") != -1 ||
          (ua.indexOf("android") != -1 && ua.indexOf("mobile") == -1) ||
          (ua.indexOf("firefox") != -1 && ua.indexOf("tablet") != -1) ||
          ua.indexOf("kindle") != -1 ||
          ua.indexOf("silk") != -1 ||
          ua.indexOf("playbook") != -1
        ) &&
        'orientationchange' in window
      ) {
        window.addEventListener("orientationchange", syncEvent.tabletResize, false);
      }
      else {
        window.addEventListener("resize", syncEvent.pcResize, false);
      }
    },
    elmScrollCallTimers: {},
    elmScrollCall: function (e) {
      e.stopPropagation();
      var nodeName = e.target.nodeName.toLowerCase(),
        index = $(String(nodeName)).index(this);

      // 排他処理
      if ( nodeName === String(syncEvent.receiveEvInfo.nodeName) && Number(index) === Number(syncEvent.receiveEvInfo.idx) ) return false;
      var elem = document.getElementsByTagName(nodeName)[Number(index)],
        scrollBarSize = {
          height: elem.scrollHeight - elem.clientHeight,
          width: elem.scrollWidth - elem.clientWidth
        };


      if ( check.isset(syncEvent.elmScrollCallTimers[nodeName + '_' + index]) ) {
        clearTimeout(syncEvent.elmScrollCallTimers[nodeName + '_' + index]);
      }
      syncEvent.elmScrollCallTimers[nodeName + '_' + index] = setTimeout(function () {

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
    receiveEvInfo: {nodeName: null, type: null},
    change: function (eventFlg) {
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
        $textarea.addEventListener('mousemove', function (e) {
          if ( e ) e.stopPropagation();
          if ( bHeight && bWidth && (bHeight !== this.style.height || bWidth !== this.style.width) ) {
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
        if ( (sHeight - cHeight) > 0 ) {
          scEls.push(els[i]);
        }
      }
      els = document.getElementsByTagName("textarea");
      for ( i in els ) {
        cHeight = els[i].clientHeight;
        sHeight = els[i].scrollHeight;
        if ( (sHeight - cHeight) > 0 ) {
          scEls.push(els[i]);
        }
      }
      els = document.getElementsByTagName("div");
      for ( i in els ) {
        cHeight = els[i].clientHeight;
        sHeight = els[i].scrollHeight;
        if ( (sHeight - cHeight) > 0 ) {
          scEls.push(els[i]);
        }
      }
      els = document.getElementsByTagName("dl");
      for ( i in els ) {
        cHeight = els[i].clientHeight;
        sHeight = els[i].scrollHeight;
        if ( (sHeight - cHeight) > 0 ) {
          scEls.push(els[i]);
        }
      }
      this.ctrlElmEventListener(eventFlg, scEls, "scroll", syncEvent.elmScrollCall);
      if ( ('form' in window.sincloInfo.dataset) && window.sincloInfo.dataset.form ) {
        // フォーム制御
        $(document).submit(function (e) {
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
    start: function (e) {
      syncEvent.change(true);
    },
    stop: function (e) {
      syncEvent.change(false);
    }
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
    getAction: function (type) {
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
    set: function (title, content, type) {
      if ( check.isset(type) === false ) {
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
      $("#sincloPopupFrame > sinclo-div").each(function (e) {
        height += this.offsetHeight;
      });

      $("#sincloPopupFrame").height(height).css("opacity", 1);
    },
    remove: function () {
      var elm = document.getElementById('sincloPopup');
      if ( elm ) {
        elm.parentNode.removeChild(elm);
      }
    },
    ok: function () {
      return true;
    },
    no: function () {
      this.remove();
    }
  };

  vcPopup = {
    dragging: false,
    set: function (fromID, toID) {
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
      $("#sincloVcPopupFrame > sinclo-div").each(function (e) {
        height += this.offsetHeight;
      });

      $("#sincloVcPopupFrame").height(height).css("opacity", 1);
      $("#sincloVcPopupFrame *").on('mousedown', vcPopup.dragOn);
      $("#sincloVcPopupFrame *").on('mouseup', vcPopup.dragOff);
      $("#sincloVcPopupFrame *").on('mousemove', vcPopup.drag);
    },
    remove: function () {
      var elm = document.getElementById('sincloVcPopup');
      if ( elm ) {
        elm.parentNode.removeChild(elm);
      }
    },
    ok: function () {
      return true;
    },
    no: function () {
      this.remove();
    },
    // ドラッグ用プロパティ・メソッド群
    startDragX: 0,
    startDragY: 0,
    dragOn: function (e) {
      if ( e ) e.stopPropagation();
      vcPopup.dragging = true;
      vcPopup.startDragX = e.screenX;
      vcPopup.startDragY = e.screenY;
    },
    dragOff: function (e) {
      if ( e ) e.stopPropagation();
      vcPopup.dragging = false;
    },
    drag: function (e) {
      if ( e ) e.stopPropagation();
      if ( !vcPopup.dragging ) return;
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

  uploadFileSelectorModal = {
    dragging: false,
    set: function (fromID, toID) {
      var html = '';
      var sincloData = {
        from: fromID,
        to: toID,
      };
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
      $("#sincloVcPopupFrame > sinclo-div").each(function (e) {
        height += this.offsetHeight;
      });

      $("#sincloVcPopupFrame").height(height).css("opacity", 1);
      $("#sincloVcPopupFrame *").on('mousedown', vcPopup.dragOn);
      $("#sincloVcPopupFrame *").on('mouseup', vcPopup.dragOff);
      $("#sincloVcPopupFrame *").on('mousemove', vcPopup.drag);
    },
    remove: function () {
      var elm = document.getElementById('sincloVcPopup');
      if ( elm ) {
        elm.parentNode.removeChild(elm);
      }
    },
    ok: function () {
      return true;
    },
    no: function () {
      this.remove();
    }
  };

  var showTimer = null;
  var focusTargetType = ["text", "search", "tel", "url", "email", "number", "password", "datetime"];
  var init = function () {
    window.addEventListener('load', function () {
      if ( 'orientation' in window ) {
        var o1 = (window.innerWidth < window.innerHeight);
        var o2 = (window.orientation % 180 == 0);
        common.defaultOrientation = (o1 && o2) || !(o1 || o2);
        console.log("common.defaultOrientation = %s", common.defaultOrientation);
      }
    }, false);
    if ( check.smartphone() ) {
      $('textarea:not(#sincloChatMessage), input:not(#miniSincloChatMessage)').on('DOMFocusIn', function (e) {
        console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>> DOMFocusIn');
        if ( !event.target ) {
          return;
        }
        if ( event.target.nodeName.toLowerCase() === 'textarea' || focusTargetType.indexOf(e.target.type) >= 0 ) {
          console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>> HIDE WIDGET');
          if ( showTimer ) {
            clearTimeout(showTimer);
            showTimer = null;
          }
          common.widgetHandler.hide();
          storage.s.set('closeAct', true);
        }
      }).on('DOMFocusOut', function (e) {
        console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>> DOMFocusOut');
        if ( !showTimer ) {
          showTimer = setTimeout(function () {
            console.log('>>>>>>>>>>>>>>>>>>>>>>>>>>>> SHOW WIDGET');
            storage.s.set('closeAct', false);
            common.widgetHandler.show();
          }, 100);
        }
      });
    }
    var tabStateTimer = null;
    // ウィジェット最大化設定をクリア
    storage.s.unset("preWidgetOpened");
    if ( window.sincloInfo.widget.showTiming !== Number(storage.s.get("widgetShowTimingType")) ) {
      // SessionStorageで保存している表示タイミング設定と違う場合はクリアする
      console.log("Storage widgetShowTimingType is different. clearing... before: " + Number(storage.s.get("widgetShowTimingType")) + " after: " + window.sincloInfo.widget.showTiming);
      common.widgetHandler.clearShownFlg();
    }
    if ( window.sincloInfo.widget.showTiming === 2 ) {
      // 表示タイミングがページごとの場合は初期処理でフラグをクリアする
      console.log("Widget Show flg clear.");
      common.widgetHandler.clearShownFlg();
    }
    socket = io.connect(sincloInfo.site.socket, {port: 9090, rememberTransport: false});

    // 接続時
    socket.on("connect", function () {
      // ウィジェットがある状態での再接続があった場合
      var sincloBox = document.getElementById('sincloBox');
      if ( sincloBox && userInfo.accessType === Number(cnst.access_type.guest) ) {
        sinclo.trigger.flg = true;
        var emitData = userInfo.getSendList();
        emitData.widget = window.sincloInfo.widgetDisplay;
        var tmpAutoMessages = sinclo.chatApi.autoMessages.get(true);
        emit('connectSuccess', {
          confirm: false,
          reconnect: true,
          tmpAutoMessages: tmpAutoMessages,
          widget: window.sincloInfo.widgetDisplay
        }, function (ev) {
          emit('customerInfo', emitData);
        });
        common.widgetHandler.show();
      }
      else {
        sinclo.trigger.flg = false;
        sinclo.connect();
      }

      if ( userInfo.accessType === Number(cnst.access_type.host) || String(userInfo.gFrame) === "true" ) return false;
      // 定期的にタブのアクティブ状態を送る
      var tabState = browserInfo.getActiveWindow();
      if ( tabStateTimer ) {
        clearInterval(tabStateTimer);
      }
      tabStateTimer = setInterval(function () {
        var newState = browserInfo.getActiveWindow();
        if ( document.getElementById('sincloBox') !== null && tabState !== newState ) {
          tabState = newState;
          emit('sendTabInfo', {status: tabState, widget: window.sincloInfo.widgetDisplay});
        }
      }, 700);
    }); // socket-on: connect

    socket.on("changeTabId", function (d) {
      var obj = common.jParse(d);
      userInfo.tabId = obj.newTabId;
      userInfo.changeTabId(obj.newTabId);
    });

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("retConnectedForSync", function (d) {
      sinclo.retConnectedForSync(d);
    }); // socket-on: retConnectedForSync

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("accessInfo", function (d) {
      sinclo.accessInfo(d);
    }); // socket-on: accessInfo

    // 接続直後（ユーザＩＤ、アクセスコード発番等）
    socket.on("syncUserInfo", function (d) {
      sinclo.syncUserInfo(d);
    }); // socket-on: accessInfo

    // 履歴ID割り振り後
    socket.on("setHistoryId", function (d) {
      sinclo.setHistoryId(d);
    }); // socket-on: getAccessInfo

    // 情報送信
    socket.on("getAccessInfo", function (d) {
      sinclo.getAccessInfo(d);
    }); // socket-on: getAccessInfo

    // 情報送信
    socket.on("confirmCustomerInfo", function (d) {
      sinclo.confirmCustomerInfo(d);
    }); // socket-on: confirmCustomerInfo

    // 画面共有
    socket.on('getWindowInfo', function (d) {
      var obj = common.jParse(d);
      sinclo.getWindowInfo(obj);
    }); // socket-on: getWindowInfo

    // 画面共有キャンセル
    socket.on('cancelSharingApplication', function (d) {
      var obj = common.jParse(d);
      sinclo.cancelSharingApplication(obj);
    });

    // 画面共有(LiveAssist)
    socket.on('startCoBrowseOpen', function (d) {
      var obj = common.jParse(d);
      sinclo.startCoBrowseOpen(obj);
    }); // socket-on: getWindowInfo

    // 画面共有準備完了
    socket.on('assistAgentIsReady', function (d) {
      var obj = common.jParse(d);
      sinclo.assistAgentIsReady(obj);
    });

    // 画面共有(iframeバージョン)
    socket.on('startWindowSync', function (d) {
      var obj = common.jParse(d);
      sinclo.startWindowSync(obj);
    }); // socket-on: startWindowSync

    // スクロール位置のセット
    socket.on('windowSyncInfo', function (d) {
      sinclo.windowSyncInfo(d);
    }); // socket-on: windowSyncInfo

    // 同期情報の収集
    socket.on('syncStart', function (d) {
      sinclo.syncStart(d);
    }); // socket-on: syncStart

    // 消費者画面の情報を反映
    socket.on('syncElement', function (d) {
      sinclo.syncElement(d);
    }); // socket-on: syncElement

    // イベント監視
    socket.on('syncEvStart', function (d) {
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

    // オートメッセージ
    socket.on('resScenarioMessage', function (d) {
      sinclo.resScenarioMessage(d);
    }); // socket-on: resScenarioMessage

    // 新着チャット
    socket.on('sendChatResult', function (d) {
      sinclo.sendChatResult(d);
    }); // socket-on: sendChatResult

    // リンク
    socket.on('clickLink', function (d) {
      sinclo.clickLink(d);
    });

    // 新着チャット
    socket.on('resGetScenario', function (d) {
      var obj = common.jParse(d);
      sinclo.scenarioApi.init(obj.id, obj.activity.scenarios);
      sinclo.scenarioApi.begin();
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
    socket.on('confirmVideochatStart', function (d) {
      var obj = common.jParse(d);
      sinclo.confirmVideochatStart(obj);
    }); // socket-on: confirmVideochatStart

    // 資料共有依頼受信
    socket.on('docShareConnect', function (d) {
      var obj = common.jParse(d);
      sinclo.docShareConnect(obj);
    }); // socket-on: confirmVideochatStart

    // 資料共有終了通知
    socket.on('docDisconnect', function () {
      sinclo.docDisconnect();
    }); // socket-on: confirmVideochatStart

    socket.on('syncStop', function (d) {
      sinclo.syncStop(d);
    }); // socket-on: syncStop

    socket.on('stopCoBrowse', function (d) {
      sinclo.stopCoBrowse(d);
    }); // socket-on: stopCoBrowse

    socket.on('sincloReconnect', function (d) { // socket再接続
      socket.disconnect();
      socket.connect();
    }); // socket-on: sincloReconnect

    socket.on('disconnect', function (data) {
      if ( !sinclo.chatApi.inactiveCloseFlg ) {
        var sincloBox = document.getElementById('sincloBox');
        if ( sincloBox ) {
          common.widgetHandler.hide();
          if ( window.sincloInfo.contract.chat && document.getElementsByTagName("sinclo-chat")[0] ) {
            sinclo.chatApi.clearChatMessages();
          }
        }
      }
      popup.remove();
    });

    // 存在チェックレスポンス用
    socket.on("checkExists", function (d) {
      var obj = common.jParse(d);
      emit('resCheckExists', obj);
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
    success: function (json) {
      if ( String(json.status) === "true" ) {
        if ( check.smartphone() && json.widget.hasOwnProperty('spShowFlg') && Number(json.widget.spShowFlg) === 2 ) {
          clearTimeout(timer);
          return false;
        }
        window.sincloInfo.widget = json.widget;
        window.sincloInfo.messages = json.messages;
        window.sincloInfo.contract = json.contract;
        window.sincloInfo.chat = json.chat;
        window.sincloInfo.customVariable = json.customVariable;
      }
      else {
        clearTimeout(timer);
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
      $("#textStatus").html("textStatus : " + textStatus);
      $("#errorThrown").html("errorThrown : " + errorThrown.message);
    }
  });

  var timer = window.setInterval(function () {
    if ( io !== "" && sinclo !== "" && window.sincloInfo.contract !== undefined ) {
      window.clearInterval(timer);
      init();
    }
  }, 200);

}(sincloJquery));

function f_url(url) {
  var re = /(\?|&)?sincloData=/;
  var num = url.search(re);
  if ( num < 0 ) {
    return browserInfo.href;
  }
  return url.substr(0, num);
}

function emit(evName, data, callback) {

  /* ここから：イベント名指定なし */
  data.siteKey = sincloInfo.site.key; // サイトの識別キー
  if ( check.isset(userInfo.sendTabId) ) {
    data.to = userInfo.sendTabId; // 送信先ID
  }
  /* ここまで：イベント名指定なし */
  /* ここから：イベント名指定あり */
  if ( evName === "customerInfo" || evName === "sendAccessInfo" ) {
    data.accessId = userInfo.accessId;
    data.userId = userInfo.userId;
    data.status = browserInfo.getActiveWindow();
    data.sincloSessionId = userInfo.sincloSessionId;
  }
  if ( evName === "connected" || evName === "getChatMessage" ) {
    data.token = common.token;
  }
  if ( evName === "connectSuccess" ) {
    data.widget = window.sincloInfo.widgetDisplay;
    data.accessId = userInfo.accessId;
    data.customVariables = userInfo.customVariables;
  }
  if ( evName === "customerInfo" || evName === "sendAccessInfo" ) {
    data.contract = window.sincloInfo.contract;
  }
  if ( evName === "syncReady" || evName === "connectSuccess" || evName === "customerInfo" || evName === "sendAccessInfo" ) {
    data.subWindow = false;
    if ( check.isset(storage.s.get('params')) || userInfo.accessType === cnst.access_type.host ) {
      data.responderId = common.params.responderId;
      data.subWindow = true;
    }
  }
  if ( evName === "syncReady" || evName === "connectSuccess" || evName === "sendAccessInfo" || evName === "customerInfo" ) {
    data.title = common.title();
  }
  if ( evName === "connectSuccess" || evName === "sendWindowInfo" || evName === "sendAutoChat" || evName === "sendChat" ||
    evName === "storeScenarioMessage" || evName === "saveCustomerInfoValue" || evName === "beginBulkHearing" || evName === "sendParseSignature"
    || evName === "hideScenarioMessages" ) {
    data.userId = userInfo.userId;
  }
  if ( evName === "connectSuccess" || evName === "sendWindowInfo" || evName === "sendAutoChatMessages" ||
    evName === "getChatMessage" || evName === "sendChat" || evName === "sendAutoChatMessage"
  ) {
    data.chat = null;
  }
  if ( evName === "syncBrowserInfo" || evName === "syncChangeEv" || evName === "requestSyncStop" ||
    evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendConfirmConnect"
  ) {
    data.accessType = userInfo.accessType;
  }
  if ( evName === "syncReady" || evName === "connectSuccess" || evName === "reqUrlChecker" || evName === "customerInfo" ||
    evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendAccessInfo" || evName === "sendWindowInfo"
  ) {
    data.url = f_url(browserInfo.href);
  }
  // connectToken
  if ( evName === "syncReady" || evName === "connectSuccess" || evName === "requestSyncStop" || evName === "customerInfo" || evName === "sendTabInfo" ||
    evName === "requestSyncStart" || evName === "connectContinue" || evName === "sendAccessInfo" || evName === "sendConfirmConnect"
  ) {
    data.connectToken = userInfo.get(cnst.info_type.connect);
  }
  if ( evName == "sendWindowInfo" || evName == "requestSyncStopForSubmit" || evName == "startSyncToFrame" ) {
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
    socket.emit(evName, JSON.stringify(data));
    return false;
  }
  /* ここまで：イベント名指定あり */
  var timer = setInterval(function () {
    if ( userInfo.tabId !== "" ) {
      clearInterval(timer);
      data.tabId = userInfo.tabId; // タブの識別ID
      data.sincloSessionId = userInfo.sincloSessionId;
      console.log("EMIT : " + evName + " data : " + JSON.stringify(data));
      socket.emit(evName, JSON.stringify(data), callback);
    }
  }, 100);
}

function now() {
  var d = new Date();
  return "【" + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() + "】";
}

function link(word, link, eventLabel) {
  /*リンクをクリックした場合は必ずこの関数を呼び出す
  * ga連携のアクションがここで起きるため、リンク・電話番号・メールのどれであるかを引数として渡したい
  */
  console.log("ga連携します");
  console.log("押されたやつのテキストは" + word + "値は" + link + "イベントラベルは" + eventLabel + "です");
  if ( eventLabel === "clickMail" ) {
    console.log('これはメールです。もし画像リンクなら文字列を修正します');
    if ( word.match(/mailto\s*:/) ) {
      console.log('画像なので文字列を修正します');
      word = word.replace(/mailto\s*:/g, "");
    }
  }
  else if ( eventLabel === "clickTelno" ) {
    console.log('これは電話です。もし画像リンクなら文字列を修正します');
    if ( word.match(/tel/) ) {
      console.log('画像なので文字列を修正します');
      word = word.replace(/tel\s*:/g, "");
    }
  }
  link = "<a " + link.replace(/\$nbsp;/g, " ") + ">" + word + "</a>";
  var data = sinclo.chatApi;
  data.link = link;
  data.siteKey = sincloInfo.site.key;
  data.tabId = userInfo.tabId;
  data.userId = userInfo.userId;
  if ( storage.s.get('requestFlg') === 'true' ) {
    data.messageRequestFlg = 0;
  }
  else {
    data.messageRequestFlg = 1;
    storage.s.set('requestFlg', true);
  }
  if ( typeof ga == "function" ) {
    if ( eventLabel === "clickLink" ) {
      //リンククリック時に登録する値は今までと変わりないようにする
      ga('send', 'event', 'sinclo', eventLabel, link, 1);
    } else {
      //メール及び電話の時は登録する文字列を修正して登録する
      link = common.stringReplaceProcessForGA(link);
      ga('send', 'event', 'sinclo', eventLabel, link, 1);
    }
  }
  socket.emit('link', data);
}

// get type
var myTag = document.querySelector("script[src$='/client/" + sincloInfo.site.key + ".js']");
if ( myTag.getAttribute('data-hide') ) {
  sincloInfo.dataset.hide = myTag.getAttribute('data-hide');
}
if ( myTag.getAttribute('data-form') ) {
  sincloInfo.dataset.form = myTag.getAttribute('data-form');
}
if ( myTag.getAttribute('data-show-always') ) {
  // オペレータ存在条件や営業時間設定に依存せずtrueであれば表示
  sincloInfo.dataset.showAlways = myTag.getAttribute('data-show-always');
}

