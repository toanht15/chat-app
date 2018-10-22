<script type="text/javascript">
'use strict';

sincloApp.factory('SimulatorService', function() {

  var self = this;

  this.escape_html = function() {
    if(typeof unescapedString !== 'string') {
      return unescapedString;
    }
    var string = unescapedString.replace(/(<br>|<br \/>)/gi, '\n');
    string = string.replace(/[&'`"<>]/g, function(match) {
      return {
        '&': '&amp;',
        "'": '&#x27;',
        '`': '&#x60;',
        '"': '&quot;',
        '<': '&lt;',
        '>': '&gt;',
      }[match];
    });
    return string;
  };

  return {
    _settings: {},
    _coreSettingsChat: {},
    _showWidgetType: 1,
    _openFlg: true,
    _showTab: 'chat',
    _isTextAreaOpen: true,
    set settings(obj) {
      this._settings = obj;
    },
    get settings() {
      return this._settings;
    },
    set coreSettingsChat(settings) {
      this._coreSettingsChat = settings;
    },
    get coreSettingsChat() {
      return this._coreSettingsChat;
    },
    set showWidgetType(type) {
      this._showWidgetType = type;
    },
    get showWidgetType() {
      return this._showWidgetType;
    },
    set openFlg(status) {
      this._openFlg = status;
    },
    get openFlg() {
      return this._openFlg;
    },
    // パラメータ取得(実際のパラメータと、取得時の名称が異なるもの)
    get widgetSizeTypeToggle() {
      return this._settings['widget_size_type'];
    },
    get subTitleToggle() {
      return this._settings['show_subtitle'];
    },
    get descriptionToggle() {
      return this._settings['show_description'];
    },
    get mainImageToggle() {
      return this._settings['show_main_image'];
    },
    get minimizedDesignToggle() {
      return this._settings['minimize_design_type'];
    },
    get closeButtonSettingToggle() {
      return this._settings['close_button_setting'];
    },
    get closeButtonModeTypeToggle() {
      return this._settings['close_button_mode_type'];
    },
    get timeTextToggle() {
      return this._settings['display_time_flg'];
    },
    get chat_area_placeholder_pc() {
      return Number(this._settings['chat_trigger']) === 1 ? '（Shift+Enterで改行/Enterで送信）' : '';
    },
    get chat_area_placeholder_sp() {
      return Number(this._settings['chat_trigger']) === 1 ? '（改行で送信）' : '';
    },
    /**
     * ウィジェットサイズ
     */
    get isMiddleSize() {
      return this._showWidgetType === 1 && this._settings['widget_size_type'] === '2';
    },
    get isLargeSize() {
      return this._showWidgetType === 1 && this._settings['widget_size_type'] === '3';
    },
    get isMaximumSize(){
      return this._showWidgetType === 1 && this._settings['widget_size_type'] === '4';
    },
    // パラメータ取得(設定の有無)
    get widget_outside_border_none() {
      return this._settings['widget_border_color'] === 'なし';
    },
    get widget_inside_border_none() {
      return this._settings['widget_inside_border_color'] === 'なし';
    },
    get re_border_none() {
      return this._settings['re_border_color'] === 'なし';
    },
    get re_text_size() {
      return Number(this._settings['re_text_size']);
    },
    get radioButtonBeforeTop() {
      return Math.ceil((Number(this.re_text_size)/2));
    },
    get radioButtonAfterTop() {
      return Math.ceil((Number(this.re_text_size)/2));
    },
    get radioButtonAfterLeft() {
      return (this.re_text_size/2 - ((this.re_text_size-6)/2))+1;
    },
    get radioButtonAfterMarginTop() {
      return Math.round(this.re_text_size/2)-4;
    },
    get se_border_none() {
      return this._settings['se_border_color'] === 'なし';
    },
    get message_box_border_none() {
      return this._settings['message_box_border_color'] === 'なし';
    },
    // 表示タブ
    set showTab(val) {
      this._showTab = val;
    },
    get showTab() {
      return this._showTab;
    },
    // 関数
    getSeBackgroundColor: function(){
      var defColor = "#FFFFFF";
      //仕様変更、常に高度な設定が当たっている状態とする
      defColor = this._settings.se_background_color;
      return defColor;
    },
    getTalkBorderColor: function(chk) {
      var defColor = "#E8E7E0";
      //仕様変更、常に高度な設定が当たっている状態とする
      if(chk === 're'){
        defColor = this._settings.re_border_color;
      }
      else{
        defColor = this._settings.se_border_color;
      }
      return defColor;
    },
    /**
     * 吹き出しの背景色設定
     * @param Integer opacity 透明度(省略可)
     * @return String         RGBAカラーコード
     */
    makeFaintColor: function(opacity) {
      opacity = opacity || 1;
      var colorCode = this._settings.re_background_color || "#F1F5C8";

      var red   = parseInt(colorCode.substring(1,3), 16);
      var green = parseInt(colorCode.substring(3,5), 16);
      var blue  = parseInt(colorCode.substring(5,7), 16);
      return 'rgba(' + red + ', ' + green + ', ' + blue + ', ' + opacity + ')';
    },
    //シンプル表示判定
    /*
    * 最小化時のデザイン
    * $scope.minimizedDesignToggle = 1/2/3:シンプル表示しない/スマホのみシンプル表示する/すべての端末でシンプル表示する
    * $scope.showWidgetType = 1/3:通常/スマホ（縦）
    * 最大時のシンプル表示(スマホ)
    * $scope.sp_header_light_flg = 0/1:しない/する
    * $scope.openFlg = true/false:最大化/最小化
    */
    spHeaderLightToggle: function(){
      switch (this.minimizedDesignToggle) {
      case "1": //シンプル表示しない
        if(this._showWidgetType === 1){
          //通常（PC）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this._openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!this._openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        else{
          //スマホ（縦）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this._openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = true;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!this._openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        break;
      case "2": //スマホのみシンプル表示する
        if(this._showWidgetType === 1){
          //通常（PC）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this._openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!this._openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        else{
          //スマホ（縦）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this._openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = true;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!this._openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        break;
      case "3": //すべての端末でシンプル表示する
        if(this._showWidgetType === 1){
          //通常（PC）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this._openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = false;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!this._openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        else{
          //スマホ（縦）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this._openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = true;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!this._openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        break;
      }
      if(this._openFlg){
        //最大化時
        $("#minimizeBtn").show();
        $("#addBtn").hide();
        $("#closeBtn").hide();
      }
      else{
        //最小化時
        $("#addBtn").show();
        $("#minimizeBtn").hide();
        if(this.closeButtonSettingToggle === '2'){
          $("#closeBtn").show();
        }
        else{
          $("#closeBtn").hide();
        }
        if(this._coreSettingsChat){
          document.getElementById("switch_widget").value = this._showWidgetType;
        }
      }
      return res;
    },
    isIconImage: function() {
      return this.settings['main_image'].match(/^fa/) !== null;
    },
    isPictureImage: function() {
      return this.settings['main_image'].match(/^http/) !== null;
    },
    //param : String型 ng-classで付けたい情報を渡す
    //TODO 受け取った情報をカンマで分割してfor文を回したほうがいい
    viewWidgetSetting: function (param){
      var widgetClasses = {};
      if(typeof param !== 'undefined' && param.indexOf("size") !== -1){
        widgetClasses = this.setWidgetSizeSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("sp") !== -1){
        widgetClasses = this.setSmartPhoneSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("toptitle") !== -1){
        widgetClasses = this.setTitlePositionSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("topname") !== -1){
        widgetClasses = this.setHeaderNamePositionSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("desc") !== -1){
        widgetClasses = this.setHeaderDescPositionSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("topimg") !== -1){
        widgetClasses = this.setHeaderImageSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("outsideborder") !== -1){
        widgetClasses = this.setOutSideBorderSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("insideborder") !== -1){
        widgetClasses = this.setInSideBorderSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("headercontent") !== -1){
        widgetClasses = this.setHeaderContentSetting(widgetClasses);
      }
      return widgetClasses;
    },

    /*ng-class用のオブジェクトを設定する関数群--開始--*/


    setWidgetSizeSetting : function (obj){
      obj.middleSize = this.judgeSize("middle");
      obj.largeSize = this.judgeSize("large");
      return obj;
    },

    setSmartPhoneSetting : function (obj){
      obj.spText = this.isSmartPhonePortrait();
      obj.sp = this.isSmartPhonePortrait();
      return obj;
    },

    setTitlePositionSetting : function(obj){
      console.log(this._settings.widget_title_top_type);
      if (Number(this._settings.widget_title_top_type) === 1){
        obj["leftPositionTitle"] = true;
      } else if (Number(this._settings.widget_title_top_type) === 2){
        obj["centerPositionTitle"] = true;
      }
      return obj;
    },

    setHeaderNamePositionSetting : function(obj){
      if (Number(this.subTitleToggle) === 2){
        obj["noCompany"] = true;
      } else if (Number(this.subTitleToggle) === 1){
        if (Number(this._settings.widget_title_name_type) === 1){
          obj["leftPosition"] = true;
        } else if (Number(this._settings.widget_title_name_type) === 2) {
          obj["centerPosition"] = true;
        }
      }
      return obj;
    },

    setHeaderDescPositionSetting : function(obj){
      if (Number(this.descriptionToggle) === 2){
        obj["noExplain"] = true;
      } else if (Number(this.descriptionToggle) === 1){
        if (Number(this._settings.widget_title_explain_type) === 1){
          obj["leftPosition"] = true;
        } else if (Number(this._settings.widget_title_explain_type) === 2) {
          obj["centerPosition"] = true;
        }
      }
      return obj;
    },

    setHeaderImageSetting : function(obj){
      if (Number(this.mainImageToggle) === 1){
        obj["Image"] = true;
      } else if (Number(this.mainImageToggle) === 2){
        obj["NoImage"] = true;
      }
      return obj;
    },

    setOutSideBorderSetting : function(obj){
      if (Number(this.widget_outside_border_none === '' || this.widget_outside_border_none === false)){
        obj["notNoneWidgetOutsideBorder"] = true;
      } else {

      }
      return obj;
    },

    setInSideBorderSetting : function(obj){
      if (Number(this.widget_inside_border_none === '' || this.widget_inside_border_none === false)){
        obj["notNone"] = true;
      } else {

      }
      return obj;
    },

    setHeaderContentSetting : function(obj){
      if(Number(this.descriptionToggle) === 1 && Number(this.subTitleToggle) === 1){
        obj["twoContents"] = true;
      } else if (Number(this.descriptionToggle) === 1 || Number(this.subTitleToggle) === 1){
        obj["oneContents"] = true;
      } else if (Number(this.descriptionToggle) === 2 || Number(this.subTitleToggle) === 2){
        obj["noContents"] = true;
      }
      return obj;
    },

    /*ng-class用のオブジェクトを設定する関数群--終了--*/




    //size : String型 small,middle,large のいずれか
    //現状の設定が渡されたサイズかどうかを判別する
    //return : boolean型
    judgeSize : function(size){

       //通常表示でない場合は判定させない
      if(Number(this.showWidgetType !== 1)){
        return false;
      }

      switch(size){
      case "small":
        //現状設定が無いため判別無し
        return true;
      break;
      case "middle":
        if(Number(this.showWidgetType) === 2){
          return true;
        } else {
          return false;
        }
      break;
      case "large":
        if(Number(this.showWidgetType) === 3 || Number(this.widgetSizeTypeToggle) === 4){
          return true;
        } else {
          return false;
        }
      break;
      default:
        //デフォルトはfalseを返す
        return false;
      }
    },

    isSmartPhonePortrait : function(){
      if(Number(this.showWidgetType) === 3){
        return true;
      } else {
        return false;
      }
    },


    /**
     * 表示用HTMLへの変換
     * @param String val    変換したいメッセージ
     * @param String prefix ラジオボタンに付与するプレフィックス
     * @return String       変換したメッセージ
     */
    createMessage: function(val, prefix) {
      if (val === '') return;
      prefix =  (typeof prefix !== 'undefined' && prefix !== '') ? prefix + '-' : '';
      var isSmartphone = this._showWidgetType != 1;
      var messageIndex = $('#chatTalk > div:not([style*="display: none;"])').length;
      var strings = val.split('\n');
      var radioCnt = 1;
      var htmlTagReg = RegExp(/<\/?("[^"]*"|'[^']*'|[^'">])*>/g)
      var radioName = prefix + "sinclo-radio" + messageIndex;
      var content = "";

      for (var i = 0; strings.length > i; i++) {
        var str = escape_html(strings[i]);
        // ラジオボタン
        var radio = str.indexOf('[]');
        if ( radio > -1 ) {
            var value = str.slice(radio+2).trim();
            var name = value.replace(htmlTagReg, '');
            str = "<span class='sinclo-radio'><input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + name + "'>";
            str += "<label for='" + radioName + "-" + i + "'>" + value + "</label></span>";
        }
        //リンク、電話番号、imgタグ
        str = replaceVariable(str,isSmartphone,this._settings['widget_size_type']);

        if(str.match(/<(".*?"|'.*?'|[^'"])*?>/)) {
          content += "" + str + "\n";
        } else {
          content += "<span class='sinclo-text-line'>" + str + "</span>\n";
        }
      }

      return content;
    },
    /**
     * ファイル拡張子から、画像か判別する
     * @param String extension ファイル拡張子
     */
    isImage: function(extension) {
      return /jpeg|jpg|gif|png/i.test(extension);
    },

    /**
     * ファイルタイプ別ごとに、font-awesome用のクラスを出し分ける
     * @param String extension ファイルの拡張子
     */
    selectIconClassFromExtension: function(extension) {
      var selectedClass = "";
      var icons = {
        image:      'fa-file-image',
        pdf:        'fa-file-pdf',
        word:       'fa-file-word',
        powerpoint: 'fa-file-powerpoint',
        excel:      'fa-file-excel',
        audio:      'fa-file-audio',
        video:      'fa-file-video',
        zip:        'fa-file-zip',
        code:       'fa-file-code',
        text:       'fa-file-text',
        file:       'fa-file'
      };
      var extensions = {
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

      return extensions[extension] || extensions['file'];
    }

  };
});

</script>
