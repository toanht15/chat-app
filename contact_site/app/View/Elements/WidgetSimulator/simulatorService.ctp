<script type="text/javascript">
'use strict';

sincloApp.factory('SimulatorService', function() {

  return {
    _settings: {},
    _showWidgetType: 1,
    _openFlg: true,
    _showTab: 'chat',
    _sincloChatMessagefocusFlg: true,
    set settings(obj) {
      this._settings = obj;
    },
    get settings() {
      return this._settings;
    },
    get showWidgetType() {
      return this._showWidgetType;
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
    get se_border_none() {
      return this._settings['se_border_color'] === 'なし';
    },
    get message_box_border_none() {
      return this._settings['message_box_border_color'] === 'なし';
    },
    get header_text_size() {
      return Number(this._settings['header_text_size']);
    },
    get se_text_size() {
      return Number(this._settings['se_text_size']);
    },
    get re_text_size() {
      return Number(this._settings['re_text_size']);
    },
    // 表示タブ
    set showTab(val) {
      this._showTab = val;
    },
    get showTab() {
      return this._showTab;
    },
    set sincloChatMessagefocusFlg(bool) {
      this._sincloChatMessagefocusFlg = bool;
    },
    switchWidget: function(num) {
      this.showWidgetType = num;
      this.sincloChatMessagefocusFlg = true;
      var sincloBox = document.getElementById("sincloBox");

      if ( Number(num) === 3 ) { // ｽﾏｰﾄﾌｫﾝ（縦）の表示
        this.showTab = 'chat'; // 強制でチャットにする
      }

      if ( Number(num) !== 2 ) { // ｽﾏｰﾄﾌｫﾝ（横）以外は最大化する
        if(sincloBox){
          if(sincloBox.style.display == 'none'){
            sincloBox.style.display = 'block';
          }
        }
        /* ウィジェットが最小化されていたら最大化する */
        if ( !this.openFlg ) { // 最小化されている場合
          var main = document.getElementById("miniTarget");  // 非表示対象エリア
          var height = 0;
          if(main){
            for(var i = 0; main.children.length > i; i++){ // 非表示エリアのサイズを計測する
              if ( Number(num) === 3 && main.children[i].id === 'navigation' ) continue; // SPの場合はナビゲーションは基本表示しない
              height += main.children[i].offsetHeight;
            }
            main.style.height = height + "px";
          }
        }
      }
      if( Number(num) !== 4 ){
        if(coreSettingsChat){
          document.getElementById("switch_widget").value = num;
        }
      }
      this.openFlg = true;

      setTimeout(function(){
        $scope.createMessage();
        $scope.toggleChatTextareaView($scope.main.chat_textarea);
      },0);
    },
    // 関数
    getSeBackgroundColor: function(){
      var defColor = "#FFFFFF";
      //仕様変更、常に高度な設定が当たっている状態とする
      defColor = this._settings.se_background_color;
//       if($scope.color_setting_type === '1'){
//         defColor = $scope.se_background_color;
//       }
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
//       if(self.widgetSettings..color_setting_type === '1'){
//         if(chk === 're'){
//           defColor = self.widgetSettings..re_border_color;
//         }
//         else{
//           defColor = self.widgetSettings..se_border_color;
//         }
//       }
//       else{
//         defColor = self.widgetSettings.chat_talk_border_color;
//       }
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
        if(this.showWidgetType === 1){
          //通常（PC）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this.openFlg){
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
            if(!this.openFlg){
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
            if(!this.openFlg){
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
            if(!this.openFlg){
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
        if(this.showWidgetType === 1){
          //通常（PC）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this.openFlg){
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
            if(!this.openFlg){
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
            if(!this.openFlg){
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
            if(!this.openFlg){
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
        if(this.showWidgetType === 1){
          //通常（PC）
          if(this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!this.openFlg){
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
            if(!this.openFlg){
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
            if(!this.openFlg){
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
            if(!this.openFlg){
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
      if(this.openFlg){
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
        var coreSettingsChat = "<?= $coreSettings[C_COMPANY_USE_CHAT]?>";
        if(coreSettingsChat){
          document.getElementById("switch_widget").value = this.showWidgetType;
        }
      }
      return res;
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
      var messageIndex = $('#chatTalk > div:not([style*="display: none;"])').length;

      var strings = val.split('\n');
      var radioCnt = 1;
      var linkReg = RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/);
      var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
      var htmlTagReg = RegExp(/<\/?("[^"]*"|'[^']*'|[^'">])*>/g)
      var radioName = prefix + "sinclo-radio" + messageIndex;
      var content = "";

      for (var i = 0; strings.length > i; i++) {
        var str = escape_html(strings[i]);

        // リンク
        var link = str.match(linkReg);
        if ( link !== null ) {
            var url = link[0];
            var a = "<a href='" + url + "' target='_blank'>" + url + "</a>";
            str = str.replace(url, a);
        }
        // ラジオボタン
        var radio = str.indexOf('[]');
        if ( radio > -1 ) {
            var value = str.slice(radio+2);
            var name = value.replace(htmlTagReg, '');
            str = "<span class='sinclo-radio'><input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + name + "'>";
            str += "<label for='" + radioName + "-" + i + "'>" + value + "</label></span>";
        }
        // 電話番号（スマホのみリンク化）
        var tel = str.match(telnoTagReg);
        if( tel !== null ) {
          var telno = tel[1];
          // if(isSmartphone) {
          //   // リンクとして有効化
          //   var a = "<a href='tel:" + telno + "'>" + telno + "</a>";
          //   str = str.replace(tel[0], a);
          // } else {
            // ただの文字列にする
            var span = "<span class='telno'>" + telno + "</span>";
            str = str.replace(tel[0], span);
          // }
        }
        content += str + "\n";
      }

      return content;
    },
    /**
     * ファイル拡張子から、画像か判別する
     * @param String extension ファイル拡張子
     */
    isImage: function(extension) {
      return /jpeg|jpg|gif|png/.test(extension);
    },
    /**
     * ファイルタイプ別ごとに、font-awesome用のクラスを出し分ける
     * @param String extension ファイルの拡張子
     */
    selectIconClassFromExtension: function(extension) {
      var selectedClass = "";
      var icons = {
        image:      'fa-file-image-o',
        pdf:        'fa-file-pdf-o',
        word:       'fa-file-word-o',
        powerpoint: 'fa-file-powerpoint-o',
        excel:      'fa-file-excel-o',
        audio:      'fa-file-audio-o',
        video:      'fa-file-video-o',
        zip:        'fa-file-zip-o',
        code:       'fa-file-code-o',
        text:       'fa-file-text-o',
        file:       'fa-file-o'
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
