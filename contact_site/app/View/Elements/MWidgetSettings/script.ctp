<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']);

// @see http://qiita.com/amagurik2/items/b64b0a005a60b6eb225b
sincloApp.directive('stringToNumber', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      ngModel.$parsers.push(function(value) {
        return '' + value;
      });
      ngModel.$formatters.push(function(value) {
        return parseFloat(value, 10);
      });
    }
  };
});

sincloApp.controller('WidgetCtrl', function($scope, $timeout){
    var coreSettingsChat = "<?= $coreSettings[C_COMPANY_USE_CHAT]?>";
    $scope.main_image = "<?=$this->formEx->val($this->data['MWidgetSetting'], 'main_image')?>";
    $scope.chatbot_icon = "<?=$this->formEx->val($this->data['MWidgetSetting'], 'chatbot_icon')?>";
    $scope.operator_icon = "<?=$this->formEx->val($this->data['MWidgetSetting'], 'operator_icon')?>";

    $scope.showWidgetType = 1; // デフォルト表示するウィジェット
    $scope.openFlg = true;

    $scope.changeFlg = false;

    $scope.trimmingInfo = "{}";

    $scope.viewSpWidget = true;

    $scope.beforeSpbPosition = 0;

    $scope.hideWidget = function(){
      $scope.resetSpView();
      $scope.switchWidget(4);
    };

    $scope.resetSpView = function(){
      $scope.beforeSpbPosition = 0;
      $scope.viewSpWidget = true;
      $scope.openFlg = true;
    };

    $scope.forceSpCloseWidget = function(){
       $scope.viewSpWidget = false;
       $scope.openFlg = false;
       console.log($scope.sp_banner_position);
    };

    $scope.closeAct = function(){
      //閉じるボタンを押された時の挙動
      console.log($scope.showWidgetType);
      switch($scope.showWidgetType) {
      case 1:
        $scope.switchWidget(4);
        break;
      case 3:
      //スマホ（縦）で押された場合
      if(Number($scope.closeButtonModeTypeToggle) === 2){
      //閉じるボタンで非表示になる場合は、非表示タブを表示する
        $scope.switchWidget(4);
      } else {
      //閉じるボタンで小さなバナーになる場合は、スマートフォン（縦）で表示する
        console.log('スマホ（縦）で小さなバナーを表示する');
        $scope.spViewHandler(3);
      }
        break;
      }
    };

    $scope.spViewHandler = function(type){
      //typeはウィジェットの状態
      //1:最大化／2:最小化／3:小さなバナー
      if(type === 3){
        $scope.viewSpWidget = false;
      } else {
        console.log('スマホ用小さなバナーがクリックされました');
        $scope.resetSpView();
      }
    };

    $scope.spBannerTypeHandler = function(){
      var bannerClass = {};
      switch(Number($scope.sp_banner_position)){
      case 1:
        bannerClass.rightbottom = true;
        break;
      case 2:
        bannerClass.leftbottom = true;
        break;
      case 3:
        bannerClass.rightcenter = true;
        break;
      case 4:
        bannerClass.leftcenter = true;
        break;
      }
      return bannerClass;
    };

    $scope.bannerEditClick = function(type){
      if(type === 1){
        console.log('特に無し');
        $scope.switchWidget(4);
      } else if(type === 2){
        $scope.switchWidget(3);
        $scope.forceSpCloseWidget();
      }
    };

    $scope.setGridDiv = function( type ){
      var settings = {};
      var target;
      var targetToggle;
      switch( type ) {
        case 'bot':
          targetToggle = $scope.chatbotIconToggle;
          target = $scope.chatbot_icon;
          break;
        case 'op':
          targetToggle = $scope.operatorIconToggle;
          target = $scope.operator_icon;
          break;
        default:
          break;
      }
      settings["with_icon"] = (Number( targetToggle ) === 1 && ( $scope.isPictureImage( target ) || $scope.isIconImage( target ) ));
      settings["arrowUp"] = (Number( $scope.chat_message_design_type ) == 1 && Number( $scope.chat_message_arrow_position ) == 1);
      settings["arrowBottom"] = (Number( $scope.chat_message_design_type ) != 1 || Number( $scope.chat_message_arrow_position ) == 2);
      settings = $scope.setWidgetSize( settings );
      return  settings;
    };

    $scope.getIconSettings = function( type ){
      var settings = {};
      var iconImage = $scope.checkIconImage( type );
      settings["icon_border"] = $scope.checkWhiteColor() && $scope.isIconImage(iconImage);
      settings = $scope.setWidgetSize( settings );
      return settings;
    };

    $scope.checkIconImage = function( type ){
      switch( type ) {
        case 'bot':
          return $scope.chatbot_icon;
        case 'op':
          return $scope.operator_icon;
        default:
          break;
      }
    };

    $scope.forceIconOriginal = function() {
      //メイン画像をそのまま利用するアイコン設定の場合は、強制的に「個別に設定」を選択させる
      if ( Number( $scope.chatbotIconType ) === <?= ICON_USE_MAIN_IMAGE ?> ) {
        $scope.chatbotIconType = <?= ICON_USE_ORIGINAL_IMAGE ?>;
        $scope.chatbot_icon = "";
      }
      if ( Number( $scope.operatorIconType ) === <?= ICON_USE_MAIN_IMAGE ?> ) {
        $scope.operatorIconType = <?= ICON_USE_ORIGINAL_IMAGE ?>;
        $scope.operator_icon = "";
      }
    };

    $scope.changeIconToMainImage = function( type ){
      switch( type ) {
        case 'bot':
          $scope.chatbot_icon = $scope.main_image;
          break;
        case 'op':
          $scope.operator_icon = $scope.main_image;
          break;
        default:
          break;
      }
    };

    $scope.changeIconToNoImage = function( type ){
      switch( type ) {
        case 'bot':
          $scope.chatbot_icon = "";
          break;
        case 'op':
          $scope.operator_icon = "";
          break;
        default:
          break;
      }
    };

    $scope.getProfileIconForOperatorIcon = function( type ) {
      if( Number( type ) === <?=ICON_USE_OPERATOR_IMAGE?> ) {
        var profileIcon = $('.header_profile_icon')[0];
        if (profileIcon.tagName === "IMG") {
          $scope.operator_icon = profileIcon.src;
        } else {
          $scope.operator_icon = "fa-user fal";
        }
      }
    };


    $scope.setWidgetSize = function(settings ) {
      if( settings == null ) {
        settings = {}
      }
      var size = "middleSize";
      if($scope.showWidgetType === 1) {
        switch (Number($scope.widgetSizeTypeToggle)) {
          case 1:
            size =  "smallSize";
            break;
          case 2:
            size = "middleSize";
            break;
          case 3:
          case 4:
            size = "largeSize";
            break;
          case 5:
            size = "customSize";
            break;
        }
      }
      settings[size] = true;
      return settings;
    };

    $scope.switchWidget = function(num){
      $scope.showWidgetType = num;
      sincloChatMessagefocusFlg = true;
      var sincloBox = document.getElementById("sincloBox");

      if ( Number(num) === 3 ) { // ｽﾏｰﾄﾌｫﾝ（縦）の表示
        $scope.widget.showTab = 'chat'; // 強制でチャットにする
      }

      if ( Number(num) !== 2 ) { // ｽﾏｰﾄﾌｫﾝ（横）以外は最大化する
        if(sincloBox){
          if(sincloBox.style.display == 'none'){
            sincloBox.style.display = 'block';
          }
        }
        /* ウィジェットが最小化されていたら最大化する */
        if ( !$scope.openFlg ) { // 最小化されている場合
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
      $scope.openFlg = true;
    };

    //バナーから通常の表示に戻るときの処理
    $scope.bannerSwitchWidget = function(){
      var coreSettingsChat = "<?= $coreSettings[C_COMPANY_USE_CHAT]?>";
      if(coreSettingsChat){
        var lastSwitchWidget = Number(document.getElementById("switch_widget").value);
      }
      else{
        var lastSwitchWidget = 1;
      }
      if (lastSwitchWidget === 3) {
      //スマホ縦は個別に値を保持している為、強制的に通常表示に遷移させる
        lastSwitchWidget = 1;
      }

      sincloBox.style.display = 'block';
      $scope.switchWidget(lastSwitchWidget);
      $scope.openFlg = true;
      return;
    };

    $scope.showChooseImg = function(){
      return Number($scope.mainImageToggle) === 1;
    };

    $scope.showChooseChatbotIcon = function(){
      return Number($scope.chatbotIconToggle) === 1;
    };

    $scope.showChooseOperatorIcon = function(){
      return Number($scope.operatorIconToggle) === 1;
    };

    $scope.showcloseButtonMode = function(){
      if(Number($scope.closeButtonSettingToggle) === 2 && Number($scope.mainImageToggle) !== 4){
        $("#closeButtonMode").show();
      }
      else{
        $("#closeButtonMode").hide();
      }
    };

    //小さなバナーの横幅を求める関数
    /*
     * 　もともとバナーの横幅はwidth: fit-content;で値を動的に持たせていたが、IEでこの実装は動作しなかったため
     * 現在の横幅を算出して当てはめる方法にした経緯がある。
     * 　しかし、各ブラウザごとにfontサイズの扱いが異なるため、この実装においても、サファリなどで見た目に差異が
     * 生まれてしまっていた。そのため、ブラウザごとに微調整できるようにし、現在に至る。
     */
    $scope.getBannerWidth = function(){
      $('#sincloBanner').css("width","40px");
      var text = $scope.bannertext;
      var sptext = $scope.sp_banner_text;
      var oneByteCount = 0;
      var towByteCount = 0;

      if((text && text.length === 0 && $scope.showWidgetType === 4)
        || (sptext && sptext.length === 0) && $scope.showWidgetType === 3) {
        $('#sincloBanner').css("width","44px");
        $('#bannertext').css("margin-right", "0px");
        return;
      } else {
        $('#bannertext').css("margin-right", "5px");
      }

      for (var i=0; i<text.length; i++){
        var n = escape(text.charAt(i));
        if (n.length < 4){
          oneByteCount++;
        }
        else{
          towByteCount++;
        }
      }

      //いったん文字数でのサイズ調整を行い、その後spanタグの長さで調整（span内で文字が折り返さないように）
      var bannerWidth = (oneByteCount * 8) + (towByteCount * 12.7) + 50;
      $('#sincloBanner').css("width", bannerWidth + "px");
      var targetSpan = $('#bannertext').get(0);
      //スマホ縦であれば少し違う調整方法を行う
      if(targetSpan) {
        console.log(targetSpan.offsetWidth);
        bannerWidth = targetSpan.offsetWidth + 50;
        $('#sincloBanner').css("width", bannerWidth + "px");
      }
      if(Number($scope.showWidgetType) === 3){
        $scope.ctrlSpBannerWidth();
      }
    };

    $scope.ctrlSpBannerWidth = function() {
      var targetSpan = $('#bannertext').get(0);
      if(Number($scope.beforeSpbPosition) === 3 || Number($scope.beforeSpbPosition) === 4){
        if(targetSpan) {
          console.log('中央からの遷移');
          console.log(targetSpan.offsetHeight);
          var bannerWidth = targetSpan.offsetHeight + 44;
          $('#sincloBanner').css("width", bannerWidth + "px");
        }
      }
    };

    $scope.showColorSettingDetails = function(){
      var chk = document.getElementById('MWidgetSettingColorSettingType').checked;
      //高度な設定を行う行わないを制御するチェックボックス
      if(chk) {
        $('#color_setting_details').show();
        $scope.color_setting_type = '1';
      }
      else{
        $('#color_setting_details').hide();
        $scope.color_setting_type = '0';
      }
      return;
    };

    /* 各基本カラー（高度な設定以外の色）が変更されたら対応する子カラーもその色に変更する start */
    //メインカラー
    $scope.changeMainColor = function(){
      var colorid = $scope.main_color;
      var rgb = $scope.checkRgbColor(colorid);
      //企業名文字色
      $scope.sub_title_text_color = colorid;
      document.getElementById('MWidgetSettingSubTitleTextColor').style.backgroundColor = colorid;
      document.getElementById('MWidgetSettingSubTitleTextColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      //企業名担当者名文字色
      $scope.c_name_text_color = colorid;
      document.getElementById('MWidgetSettingCNameTextColor').style.backgroundColor = colorid;
      document.getElementById('MWidgetSettingCNameTextColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      //送信ボタン背景色
      $scope.chat_send_btn_background_color = colorid;
      document.getElementById('MWidgetSettingChatSendBtnBackgroundColor').style.backgroundColor = colorid;
      document.getElementById('MWidgetSettingChatSendBtnBackgroundColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      //企業側吹き出し背景色※算出
      var main_color = $scope.main_color;
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
      var code = ('#' + codeR + codeG + codeB).toUpperCase();
      $scope.re_background_color = code;
      var rgb = 'rgb(' + balloonR  + ', ' +  balloonG  + ', ' +  balloonB + ')';
      var element = document.getElementById('MWidgetSettingReBackgroundColor');
      element.style.backgroundColor = rgb;
      element.style.color = $scope.checkTxtColor(balloonR,balloonG,balloonB);
      jscolor.installByClassName("jscolor");
    }
    //タイトル文字色
    $scope.changeStringColor = function(){
      //現在設定されているタイトルバー文字色に変更
      var colorid = $scope.string_color;
      //送信ボタン文字色
      $scope.chat_send_btn_text_color = colorid;
      var rgb = $scope.checkRgbColor(colorid);
      var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
      var element = document.getElementById('MWidgetSettingChatSendBtnTextColor');
      element.style.backgroundColor = rgbcode;
      element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      jscolor.installByClassName("jscolor");
    }
    //吹き出し文字色
    $scope.changeMessageTextColor = function(){
      //現在設定されている吹き出し文字色に変更
      //var colorid = $scope.message_text_color;
      var colorid = "<?= MESSAGE_TEXT_COLOR ?>";
      //企業側吹き出し文字色
      $scope.re_text_color = colorid;
      var rgb = $scope.checkRgbColor(colorid);
      var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
      document.getElementById('MWidgetSettingReTextColor').style.backgroundColor = rgbcode;
      document.getElementById('MWidgetSettingReTextColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      //訪問者側吹き出し文字色
      $scope.se_text_color = colorid;
      document.getElementById('MWidgetSettingSeTextColor').style.backgroundColor = rgbcode;
      document.getElementById('MWidgetSettingSeTextColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      jscolor.installByClassName("jscolor");
    }
    //その他文字色
    $scope.changeOtherTextColor = function(){
      //現在設定されているその他文字色に変更
      var colorid = $scope.other_text_color;
      //説明文文字色
      $scope.description_text_color = colorid;
      var rgb = $scope.checkRgbColor(colorid);
      var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
      document.getElementById('MWidgetSettingDescriptionTextColor').style.backgroundColor = rgbcode;
      document.getElementById('MWidgetSettingDescriptionTextColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      //メッセージBOX文字色
      $scope.message_box_text_color = colorid;
      document.getElementById('MWidgetSettingMessageBoxTextColor').style.backgroundColor = rgbcode;
      document.getElementById('MWidgetSettingMessageBoxTextColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      jscolor.installByClassName("jscolor");
    }
    //吹き出し枠線色
    $scope.changeChatTalkBorderColor = function(){
      //現在設定されている吹き出し枠線色に変更
      var colorid = $scope.chat_talk_border_color;
      //企業側吹き出し枠線色
      $scope.re_border_color = colorid;
      var rgb = $scope.checkRgbColor(colorid);
      var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
      document.getElementById('MWidgetSettingReBorderColor').style.backgroundColor = rgbcode;
      document.getElementById('MWidgetSettingReBorderColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      //訪問者側吹き出し枠線色
      $scope.se_border_color = colorid;
      document.getElementById('MWidgetSettingSeBorderColor').style.backgroundColor = rgbcode;
      document.getElementById('MWidgetSettingSeBorderColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      //メッセージBOX枠線色
      $scope.message_box_border_color = colorid;
      document.getElementById('MWidgetSettingMessageBoxBorderColor').style.backgroundColor = rgbcode;
      document.getElementById('MWidgetSettingMessageBoxBorderColor').style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      jscolor.installByClassName("jscolor");
    };
    /* 各基本カラー（高度な設定以外の色）が変更されたら対応する子カラーもその色に変更する end */

    /* 各ボーダー色を変更した時にその色に対応する「枠線なしチェックボックス」が入っていたらチェックを外す start*/
    //企業側吹き出し枠線色
    $scope.changeReBorderColor = function(){
      var chk = document.getElementById('MWidgetSettingReBorderNone').checked;
      if(chk){
        var element = document.getElementById('MWidgetSettingReBorderColor');
        if(!element.classList.contains("jscolor{hash:true}")){
          element.classList.add("jscolor{hash:true}");
          jscolor.installByClassName("jscolor");
        }
        element.jscolor.show();
        //var colorid = $scope.chat_talk_border_color;
        var colorid = "#E8E7E0";
        $scope.re_border_color = colorid;
        var rgb = $scope.checkRgbColor(colorid);
        element.style.backgroundColor = colorid;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
        document.getElementById('MWidgetSettingReBorderNone').checked = false;
        $scope.re_border_none = '';
      }
    };
    //訪問者側吹き出し枠線色
    $scope.changeSeBorderColor = function(){
      var chk = document.getElementById('MWidgetSettingSeBorderNone').checked;
      if(chk){
        var element = document.getElementById('MWidgetSettingSeBorderColor');
        if(!element.classList.contains("jscolor{hash:true}")){
          element.classList.add("jscolor{hash:true}");
          jscolor.installByClassName("jscolor");
        }
        element.jscolor.show();
        var colorid = "#E8E7E0";
        $scope.se_border_color = colorid;
        var rgb = $scope.checkRgbColor(colorid);
        element.style.backgroundColor = colorid;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
        document.getElementById('MWidgetSettingSeBorderNone').checked = false;
        $scope.se_border_none = '';
      }
    };
    //メッセージBOX枠線色
    $scope.changeMessageBoxBorderColor = function(){
      var chk = document.getElementById('MWidgetSettingMessageBoxBorderNone').checked;
      if(chk){
        var element = document.getElementById('MWidgetSettingMessageBoxBorderColor');
        if(!element.classList.contains("jscolor{hash:true}")){
          element.classList.add("jscolor{hash:true}");
          jscolor.installByClassName("jscolor");
        }
        element.jscolor.show();
        var colorid = "<?= CHAT_TALK_BORDER_COLOR ?>";
        $scope.message_box_border_color = colorid;
        var rgb = $scope.checkRgbColor(colorid);
        element.style.backgroundColor = colorid;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
        document.getElementById('MWidgetSettingMessageBoxBorderNone').checked = false;
        $scope.message_box_border_none = false;
      }
    }
    //ウィジェット外枠線色
    $scope.changeWidgetBorderColor = function(){
      var chk = document.getElementById('MWidgetSettingWidgetOutsideBorderNone').checked;
      if(chk){
        var element = document.getElementById('MWidgetSettingWidgetBorderColor');
        if(!element.classList.contains("jscolor{hash:true}")){
          element.classList.add("jscolor{hash:true}");
          jscolor.installByClassName("jscolor");
        }
        element.jscolor.show();
        //var colorid = $scope.widget_border_color;
        var colorid = "<?= WIDGET_BORDER_COLOR ?>";
        $scope.widget_border_color = colorid;
        var rgb = $scope.checkRgbColor(colorid);
        element.style.backgroundColor = colorid;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
        document.getElementById('MWidgetSettingWidgetOutsideBorderNone').checked = false;
        $scope.widget_outside_border_none = false;
      }
    };
    //ウィジェット内枠線色
    $scope.changeWidgetInsideBorderColor = function(){
      var chk = document.getElementById('MWidgetSettingWidgetInsideBorderNone').checked;
      if(chk){
        var element = document.getElementById('MWidgetSettingWidgetInsideBorderColor');
        if(!element.classList.contains("jscolor{hash:true}")){
          element.classList.add("jscolor{hash:true}");
          jscolor.installByClassName("jscolor");
        }
        element.jscolor.show();
        //var colorid = $scope.widget_border_color;
        var colorid = "<?= WIDGET_INSIDE_BORDER_COLOR ?>";
        $scope.widget_inside_border_color = colorid;
        var rgb = $scope.checkRgbColor(colorid);
        element.style.backgroundColor = colorid;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
        document.getElementById('MWidgetSettingWidgetInsideBorderNone').checked = false;
        $scope.widget_inside_border_none = false;
      }
    };
    /* 各ボーダー色を変更した時にその色に対応する「枠線なしチェックボックス」が入っていたらチェックを外す end*/


    //各標準色に戻す
    /*
    *ヘッダ部
    * 7.企業名文字色'sub_title_text_color':現在設定されているメインカラーに変更
    * 8.説明文文字色'description_text_color':現在設定されているその他文字色に変更ok
    *
    *チャットエリア部
    * 9.チャットエリア背景色'chat_talk_background_color':標準カラーに変更ok
    * 10.企業名担当者名文字色'c_name_text_color':現在設定されているメインカラーに変更
    * 11.企業側吹き出し文字色're_text_color':現在設定されている吹き出し文字色に変更ok
    * 12.企業側吹き出し背景色're_background_color':標準カラーに変更ok
    * 13.企業側吹き出し枠線色're_border_color':現在設定されている吹き出し枠線色に変更ok
    * 14.訪問者側吹き出し文字色'se_text_color':現在設定されている吹き出し文字色に変更ok
    * 15.訪問者側吹き出し背景色'se_background_color':標準カラーに変更ok
    * 16.訪問者側吹き出し枠線色'se_border_color':現在設定されている吹き出し枠線色に変更ok
    *
    *メッセージエリア部
    * 17.メッセージエリア背景色'chat_message_background_color':標準カラーに変更ok
    * 18.メッセージBOX文字色'message_box_text_color':現在設定されているその他文字色に変更ok
    * 19.メッセージBOX背景色'message_box_background_color':標準カラーに変更ok
    * 20.メッセージBOX枠線色'message_box_border_color':現在設定されている吹き出し枠線色に変更ok
    * 21.送信ボタン文字色'chat_send_btn_text_color':現在設定されているタイトルバー文字色に変更ok
    * 22.送信ボタン背景色'chat_send_btn_background_color':現在設定されているメインカラーに変更
    *
    *その他
    * 23.ウィジット内枠線色'widget_inside_border_color':現在設定されているウィジェット枠線色に変更ok
    */
    $scope.returnStandardColor = function(id){
      if(id === 'widget_border_color'){
        var colorid = "<?= WIDGET_BORDER_COLOR ?>";
        $scope.widget_border_color = colorid;
        $scope.changeWidgetBorderColor();
        //MWidgetSettingWidgetInsideBorderColor
        var rgb = $scope.checkRgbColor(colorid);
        var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
        var element = document.getElementById('MWidgetSettingWidgetBorderColor');
        element.style.backgroundColor = rgbcode;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      }
      if(id === 'chat_send_btn_text_color'){
        //現在設定されているタイトルバー文字色に変更
        var colorid = $scope.string_color;
        $scope.chat_send_btn_text_color = colorid;
        //MWidgetSettingChatSendBtnTextColor
        var rgb = $scope.checkRgbColor(colorid);
        var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
        var element = document.getElementById('MWidgetSettingChatSendBtnTextColor');
        element.style.backgroundColor = rgbcode;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      }
      if(id === 'widget_inside_border_color'){
        //現在設定されているウィジェット枠線色に変更
        //var colorid = $scope.widget_border_color;
        var colorid = "<?= WIDGET_INSIDE_BORDER_COLOR ?>";
        $scope.widget_inside_border_color = colorid;
        $scope.changeWidgetInsideBorderColor();
        //MWidgetSettingWidgetInsideBorderColor
        var rgb = $scope.checkRgbColor(colorid);
        var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
        var element = document.getElementById('MWidgetSettingWidgetInsideBorderColor');
        element.style.backgroundColor = rgbcode;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      }
      if(id === 're_background_color'){
        //企業側吹き出し背景色は現在設定されているメインカラーから算出する
        var main_color = $scope.main_color;
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
        var code = ('#' + codeR + codeG + codeB).toUpperCase();
        $scope.re_background_color = code;
        var rgb = 'rgb(' + balloonR  + ', ' +  balloonG  + ', ' +  balloonB + ')';
        var element = document.getElementById('MWidgetSettingReBackgroundColor');
        element.style.backgroundColor = rgb;
        element.style.color = $scope.checkTxtColor(balloonR,balloonG,balloonB);

      }
      if(id === 'description_text_color' || id === 'message_box_text_color'){
        //現在設定されているその他文字色に変更
        var colorid = $scope.other_text_color;
        switch (id) {
          case "description_text_color":
            $scope.description_text_color = colorid;
            //MWidgetSettingDescriptionTextColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingDescriptionTextColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
          case "message_box_text_color":
            $scope.message_box_text_color = colorid;
            //MWidgetSettingMessageBoxTextColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingMessageBoxTextColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
        }
      }
      if(id === 're_text_color' || id === 'se_text_color'){
        //現在設定されている吹き出し文字色に変更
//        var colorid = $scope.message_text_color;
        var colorid = "<?= MESSAGE_TEXT_COLOR ?>";

        switch (id) {
          case "re_text_color":
            $scope.re_text_color = colorid;
            //MWidgetSettingReTextColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingReTextColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
          case "se_text_color":
            $scope.se_text_color = colorid;
            //MWidgetSettingSeTextColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingSeTextColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
        }
      }
      if(id === 're_border_color' || id === 'se_border_color' || id === 'message_box_border_color'){
        //現在設定されている吹き出し枠線色に変更
//        var colorid = $scope.chat_talk_border_color;
        var colorid = "#E8E7E0";
        switch (id) {
          case "re_border_color":
              var element = document.getElementById('MWidgetSettingReBorderColor');
              $scope.re_border_none = true;
              element.style.backgroundColor = "#FFFFFF";
              element.style.color = "#909090";
              $scope.re_border_color = "なし"
            break;
          case "se_border_color":
              var element = document.getElementById('MWidgetSettingSeBorderColor');
              $scope.se_border_none = true;
              element.style.backgroundColor = "#FFFFFF";
              element.style.color = "#909090";
              $scope.se_border_color = "なし"
            break;
          case "message_box_border_color":
            colorid = "<?= MESSAGE_BOX_BORDER_COLOR ?>";
            $scope.message_box_border_color = colorid;
            $scope.changeMessageBoxBorderColor();
            //MWidgetSettingMessageBoxBorderColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingMessageBoxBorderColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
        }
      }
      if(id === 'chat_talk_background_color'
         || id === 'se_background_color'
         || id === 'chat_message_background_color'
         || id === 'message_box_background_color'){
        //標準カラーに変更
        switch (id) {
          case "chat_talk_background_color":
            var colorid = '<?=CHAT_TALK_BACKGROUND_COLOR?>';
            $scope.chat_talk_background_color = colorid;
            //MWidgetSettingChatTalkBackgroundColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingChatTalkBackgroundColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
          case "se_background_color":
            //var colorid = '<?=SE_BACKGROUND_COLOR?>';
            var colorid = '#E7E7E7';
            $scope.se_background_color = colorid;
            //MWidgetSettingSeBackgroundColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingSeBackgroundColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
          case "chat_message_background_color":
            var colorid = '<?=CHAT_MESSAGE_BACKGROUND_COLOR?>';
            $scope.chat_message_background_color = colorid;
            //MWidgetSettingChatMessageBackgroundColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingChatMessageBackgroundColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
          case "message_box_background_color":
            var colorid = '<?=MESSAGE_BOX_BACKGROUND_COLOR?>';
            $scope.message_box_background_color = colorid;
            //MWidgetSettingMessageBoxBackgroundColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingMessageBoxBackgroundColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
        }
      }
      if(id === 'sub_title_text_color' || id === 'c_name_text_color' || id === 'chat_send_btn_background_color'){
        //現在設定されているメインカラーに変更
        var colorid = $scope.main_color;
        switch (id) {
          case "sub_title_text_color":
            $scope.sub_title_text_color = colorid;
            //MWidgetSettingSubTitleTextColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingSubTitleTextColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
          case "c_name_text_color":
            $scope.c_name_text_color = colorid;
            //MWidgetSettingCNameTextColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingCNameTextColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
          case "chat_send_btn_background_color":
            $scope.chat_send_btn_background_color = colorid;
            //MWidgetSettingChatSendBtnBackgroundColor
            var rgb = $scope.checkRgbColor(colorid);
            var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
            var element = document.getElementById('MWidgetSettingChatSendBtnBackgroundColor');
            element.style.backgroundColor = rgbcode;
            element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
            break;
        }
      }
      if(id === 'header_background_color') {
        var colorid = '#FFFFFF';
        $scope.header_background_color = colorid;
        //MWidgetSettingSeBackgroundColor
        var rgb = $scope.checkRgbColor(colorid);
        var rgbcode = 'rgb(' + rgb['r']  + ', ' +  rgb['g']  + ', ' +  rgb['b'] + ')';
        var element = document.getElementById('MWidgetSettingHeaderBackgroundColor');
        element.style.backgroundColor = rgbcode;
        element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
      }
      jscolor.installByClassName("jscolor");
    };

    $scope.$watch('chat_trigger', function(){
      if ( Number($scope.chat_trigger) === 1 ) {
        $scope.chat_area_placeholder_pc = "（Shift+Enterで改行/Enterで送信）";
        $scope.chat_area_placeholder_sp = "（改行で送信）";
      }
      else {
        $scope.chat_area_placeholder_pc = "";
        $scope.chat_area_placeholder_sp = "";
      }
    });

    $scope.makeFaintColor = function(){
      var defColor = "#F1F5C8";
      //仕様変更、常に高度な設定が当たっている状態とする
      defColor = $scope.re_background_color;
      return defColor;
    };

    $scope.getTalkBorderColor = function(chk){
      var defColor = "#E8E7E0";
      //仕様変更、常に高度な設定が当たっている状態とする
      if(chk === 're'){
        defColor = $scope.re_border_color;
      }
      else{
        defColor = $scope.se_border_color;
      }
      return defColor;
    };

    $scope.getSeBackgroundColor = function(){
      var defColor = "#FFFFFF";
      //仕様変更、常に高度な設定が当たっている状態とする
      defColor = $scope.se_background_color;
      return defColor;
    };

  $scope.makeBalloonTriangleColor = function(){
    var defColor = "#F1F5C8";
    if ( $scope.main_color.indexOf("#") >= 0 ) {
      var code = $scope.main_color.substr(1), r,g,b;
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
      defColor = 'rgb(' + balloonR  + ', ' +  balloonG  + ', ' +  balloonB + ');';
    }
    return defColor;
  };

  $scope.isIconImage = function(image) {
    if( typeof(image) === "undefined" ) return false;
    return image.match(/^fa/) !== null;
  };

  $scope.isPictureImage = function(image) {
    if( typeof(image) === "undefined" ) return false;
    return image.match(/^http|data|\/\/node/) !== null;
  };

  $scope.getActualChatTalkHeight = function() {
    var offset = (Number($scope.chat_init_show_textarea) === 2) ? 75 : 0;
    var actualHeight = 284;
    switch(Number($scope.widgetSizeTypeToggle)) {
      case 1:
        actualHeight = 194 + offset;
        break;
      case 2:
        actualHeight = 284 + offset;
        break;
      case 3:
      case 4:
        actualHeight = 374 + offset;
        break;
      case 5:
        actualHeight = Number($scope.widget_custom_height) + offset;
        break;
    }
    return actualHeight;
  };

  $scope.resetChatbotIconTypeToMain = function() {
    $scope.chatbotIconType = 1;
    $scope.changeIconToMainImage('bot');
  };

  $scope.resetOperatorIconTypeToMain = function() {
    $scope.operatorIconType = 1;
    $scope.changeIconToMainImage('op');
  };

  $scope.checkWhiteColor = function() {
    return String($scope.main_color) === "#FFFFFF";
  };

  $scope.inputInitToggle = function(item){
    return (item) ? 1 : 2;
  };

  $scope.iconBorderSetting = function(isMainColorWhite) {
    if( isMainColorWhite ) {
      return {
        'border': "1px solid" + $scope.string_color
      };
    } else {
      // メインカラーが白でない場合は何も返却しない
    }
  };

  $scope.getIconColor = function(main_image) {
    var isInvert = main_image.match(/invert$/) !== null;
    if(isInvert) {
      return {
        'background-color': $scope.string_color,
        'color': $scope.main_color
      };
    } else {
      return {
        'background-color': $scope.main_color,
        'color': $scope.string_color
      };
    }
  };
  $scope.getProfileIconForOpIcon = function( type ) {
    if( Number( type ) === <?=ICON_USE_OPERATOR_IMAGE?> ){
      var profileIcon = document.getElementById("headerProfileImg");
      if ( !profileIcon == null) {
        $scope.operator_icon = profileIcon.src
      }
      $scope.operator_icon = "fa-user fal";
    }
  };

    //シンプル表示判定
    /*
    * 最小化時のデザイン
    * $scope.minimizedDesignToggle = 1/2/3:シンプル表示しない/スマホのみシンプル表示する/すべての端末でシンプル表示する
    * $scope.showWidgetType = 1/3:通常/スマホ（縦）
    * 最大時のシンプル表示(スマホ)
    * $scope.sp_header_light_flg = 0/1:しない/する
    * $scope.openFlg = true/false:最大化/最小化
    */
    $scope.spHeaderLightToggle = function(){
      switch ($scope.minimizedDesignToggle) {
      case "1": //シンプル表示しない
        if($scope.showWidgetType === 1){
          //通常（PC）
          if($scope.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
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
            if(!$scope.openFlg){
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
          if($scope.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
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
            if(!$scope.openFlg){
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
        if($scope.showWidgetType === 1){
          //通常（PC）
          if($scope.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
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
            if(!$scope.openFlg){
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
          if($scope.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
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
            if(!$scope.openFlg){
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
        if($scope.showWidgetType === 1){
          //通常（PC）
          if($scope.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
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
            if(!$scope.openFlg){
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
          if($scope.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
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
            if(!$scope.openFlg){
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
      if($scope.openFlg){
        //最大化時
        $("#minimizeBtn").show();
        $("#addBtn").hide();
        $("#closeBtn").hide();
        //画像あるときタイトル位置
        setTimeout(function(){
          if($('#mainImage').is(':visible')) {
            $scope.indicateSimpleImage();
          }
          //画像ないときタイトル位置
          else {
            $scope.indicateSimpleNoImage();
          }
        }, 100);
      }
      else{
        //最小化時
        $("#addBtn").show();
        $("#minimizeBtn").hide();
        if($scope.closeButtonSettingToggle === '2'){
          $("#closeBtn").show();
        }
        else{
          $("#closeBtn").hide();
        }
        var coreSettingsChat = "<?= $coreSettings[C_COMPANY_USE_CHAT]?>";
        if(coreSettingsChat){
          document.getElementById("switch_widget").value = $scope.showWidgetType;
        }
        //画像あるときタイトル位置
        setTimeout(function(){
          if($('#mainImage').is(':visible')) {
            $scope.indicateSimpleImage();
          }
          //画像ないときタイトル位置
          else {
            $scope.indicateSimpleNoImage();
          }
        }, 100);
      }
      return res;
    };

    $scope.indicateSimpleNoImage = function(){
    }

    $scope.indicateSimpleImage = function(){
    }

    $scope.showGallary = function(galleryType){
      $.ajax({
        type: 'post',
        data: {
          color: $scope.main_color,
          string_color: $scope.string_color,
          iconType: galleryType
        },
        cache: false,
        dataType: 'html',
        url: "<?= $this->Html->url('/MWidgetSettings/remoteShowGallary') ?>",
        success: function(html){
          modalOpen.call(window, html, 'p-show-gallary', 'ギャラリー', 'moveup');
          popupEvent.customizeBtn = function(name, galleryType){
            console.log(galleryType);
            var src;
            if(name.match(/^fa/)) {
              src = name;
            } else {
              src = "<?=$gallaryPath?>" + name;
            }

            switch( Number( galleryType ) ) {
              case 1:
                $scope.main_image = src;
                if ( Number($scope.chatbotIconType) === 1 ) {
                  $scope.changeIconToMainImage('bot');
                }
                if ( Number($scope.operatorIconType) === 1) {
                  $scope.changeIconToMainImage('op');
                }
                break;
              case 2:
                $scope.chatbot_icon = src;
                break;
              case 3:
                $scope.operator_icon = src;
                break;
              default:
                $scope.main_image = src;
            }
            $("#MWidgetSettingUploadImage").val("");
            $scope.$apply();
            popupEvent.close();
          };
        }
      });
    };

    //カラーコードのＲＧＢを算出
    $scope.checkRgbColor = function(color){
      var code = color.substr(1), r,g,b;
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
      var balloonR = String(Math.floor(255 - (255 - parseInt(r,16))));
      var balloonG = String(Math.floor(255 - (255 - parseInt(g,16))));
      var balloonB = String(Math.floor(255 - (255 - parseInt(b,16))));
      var res = {
          r: balloonR,
          g: balloonG,
          b: balloonB
      };
      return res;
    };

    //テキストカラーの振り分け
    $scope.checkTxtColor = function(cR,cG,cB){
      // 最高値は 255 なので、約半分の数値 127 を堺目にして白/黒の判別する
      var cY = 0.3*cR + 0.6*cG + 0.1*cB;

      if(cY > 127){
          return "#000000"; // 黒に設定
      }
      return "#FFFFFF"; // 白に設定
    };

    $scope.switchMaxLength = function(size){
      var settingTitle = document.getElementById('MWidgetSettingTitle');
      var settingSubTitle = document.getElementById('MWidgetSettingSubTitle');
      var settingDescription = document.getElementById('MWidgetSettingDescription');
      var titleLength = 12;
      var subTitleLength = 15;
      var descriptionLength = 15;
      switch (size) {
       //大きさによってトップタイトル、企業名、説明文の上限文字列長を可変とする
        case 1: //小
          titleLength = 12;
          subTitleLength = 15;
          descriptionLength = 15;
          break;
        case 2: //中
          titleLength = 16;
          subTitleLength = 20;
          descriptionLength = 20;
          break;
        case 3: //大
        case 4: //最大
        case 5:
          titleLength = 19;
          subTitleLength = 24;
          descriptionLength = 24;
          break;
      }
      settingTitle.maxLength = titleLength;
      settingSubTitle.maxLength = subTitleLength;
      settingDescription.maxLength = descriptionLength;
    };

    $scope.switchMaxTextSize = function(size){
      //テキストサイズの最大値を変更します
      var settingHeaderText = document.getElementById('MWidgetSettingHeaderTextSize');
      var settingReText = document.getElementById('MWidgetSettingReTextSize');
      var settingSeText = document.getElementById('MWidgetSettingSeTextSize');
      var settingSendBtnText = document.getElementById('MWidgetSettingChatSendBtnTextSize');
      var headerTextSize = 0;
      var textSize = 0;
      var sendBtnTextSize = 0;
      switch(size) {
      case 1:
        headerTextSize = 20;
        textSize = 20;
        sendBtnTextSize = 26;
        break;
      case 2:
        headerTextSize = 20;
        textSize = 20;
        sendBtnTextSize = 30;
        break;
      case 3:
        headerTextSize = 20;
        textSize = 20;
        sendBtnTextSize = 36;
        break;
      case 4:
      case 5:
        headerTextSize = 64;
        textSize = 42;
        sendBtnTextSize = 36;
        break;
      }
      settingHeaderText.max = headerTextSize;
      settingReText.max = textSize;
      settingSeText.max = textSize;
      settingSendBtnText.max = sendBtnTextSize;
    };

    //ウィジェットサイズがクリックされた時の動作
    $scope.clickWidgetSizeTypeToggle = function(size){
      $scope.switchMaxLength(size);
      $scope.switchMaxTextSize(size);
      $scope.revertStandardTextSize('header_text_size');
      $scope.revertStandardTextSize('re_text_size');
      $scope.revertStandardTextSize('se_text_size');
      $scope.revertStandardTextSize('chat_send_btn_text_size');
      $scope.revertStandardTextSize('message_box_text_size');
      if($('#chatTalk').length > 0) {
        $('#chatTalk').css('height', '');
      } else {
        $('#telContent').css('height', '');
      }
      $scope.resizeWidgetHeightByWindowHeight();
      setTimeout(function(){
        $('#miniTarget').css('height', 'auto');
      },0);
    };

    $scope.revertStandardTextSize = function(target) {
      var widgetSize = $scope.widgetSizeTypeToggle;
      var size = 0;
      switch(target + widgetSize) {
        case 'header_text_size1':
          size = 14;
          break;
        case 'header_text_size2':
        case 'header_text_size3':
        case 'header_text_size4':
          case 'header_text_size5':
          size = 15;
          break;
        case 're_text_size1':
          size = 12;
          break;
        case 're_text_size2':
        case 're_text_size3':
        case 're_text_size4':
        case 're_text_size5':
          size = 13;
          break;
        case 'se_text_size1':
          size = 12;
          break;
        case 'se_text_size2':
        case 'se_text_size3':
        case 'se_text_size4':
        case 'se_text_size5':
          size = 13;
          break;
        case 'chat_send_btn_text_size1':
          size = 12;
          break;
        case 'chat_send_btn_text_size2':
        case 'chat_send_btn_text_size3':
        case 'chat_send_btn_text_size4':
        case 'chat_send_btn_text_size5':
          size = 13;
          break;
        case 'message_box_text_size1':
          size = 12;
          break;
        case 'message_box_text_size2':
        case 'message_box_text_size3':
        case 'message_box_text_size4':
        case 'message_box_text_size5':
          size = 13;
          break;
      }
      $scope[target] = size;
    };

    $scope.showNormalMaximized = function(){
      $scope.switchWidget(1);
      $scope.openFlg = true;
    };

    //最小化表示する
    $scope.showNormalMinimized = function(){
      $scope.openFlg = false;
    };

    //最小化時のデザインがクリックされた時の動作
    $scope.clickMinimizedDesignToggle = function(tag){
      //スマホバナー状態を解除する
      $scope.viewSpWidget = true;
      if($scope.showWidgetType !== tag){
        $scope.switchWidget(tag);
      }
      //2段階表示の場合であれば、最小化状態を表示させない
      if(tag === 3 && Number($scope.sp_widget_view_pattern) === 3){
        console.log($scope.openFlg);
        return;
      }
      $timeout(function(){
        $scope.openFlg = false;
      },0);
    };

    $scope.settingShowTimeRadioButtonEnable = function(jq) {
      jq.prop('disabled',false).parent().css('color','');
      jq.next().css('color','');
    };

    $scope.settingShowTimeRadioButtonDisable = function(jq) {
      // 選択されていたら「常に最大化しない」設定にする
      if(jq.prop('checked')) {
        jq.prop('checked',false);
        jq.next().prop('disabled',true);
        $('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_OFF?>').prop('checked',true);
      }
      jq.prop('disabled',true).parent().css('color','#ccc');
      jq.next().css('color','#ccc');
    };

    $scope.$watch('widgetDisplayTypeToggle', function(){
      if(String($scope.widgetDisplayTypeToggle) === "3") {
        // 小さなバナー表示
        $scope.closeBtnDisableWhenShowBannerEnable();
      } else {
        $scope.closeBtnEnableWhenShowBannerDisable();
      }
    });

    $scope.$watch('showTiming', function(){
      switch($scope.showTiming) {
        case "1": // サイト訪問後__秒で表示
          $scope.settingShowTimeRadioButtonEnable($('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>'));
          $scope.settingShowTimeRadioButtonDisable($('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>'));
          break;
        case "2": // ページ訪問後__秒で表示
          $scope.settingShowTimeRadioButtonDisable($('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>'));
          $scope.settingShowTimeRadioButtonEnable($('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>'));
          break;
        case "3": // 初回オートメッセージ受信後に表示
          $scope.settingShowTimeRadioButtonDisable($('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>'));
          $scope.settingShowTimeRadioButtonDisable($('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>'));
          break;
        case "4": // すぐに表示
          $scope.settingShowTimeRadioButtonEnable($('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>'));
          $scope.settingShowTimeRadioButtonEnable($('#showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>'));
          break;
      }
    });

    //営業時間設定を利用しないに設定している場合
    if(<?= $operatingHourData ?> == 2) {
      $scope.settingShowTimeRadioButtonDisable($('#MWidgetSettingDisplayType4'));
      $("#MWidgetSettingDisplayType4").parent().addClass("commontooltip");
      $('#MWidgetSettingDisplayType4').parent().attr('data-text', 'こちらの機能は営業時間設定で「利<br>用する」を選択すると、ご利用いただけます。');
      $('#MWidgetSettingDisplayType4').parent().attr('data-balloon-position', '14');
      $('#MWidgetSettingDisplayType4').parent().attr('operatingHours', 'widgetHoursPage');
    }

    // 表示切り替え時のチラ付きを抑えるためにいったん非表示にする
    $scope.currentWindowHeight = $(window).height();
    $('#m_widget_simulator').css('visibility', 'hidden');
    angular.element(window).on('load',function(e){
      $('[name="data[MWidgetSetting][show_timing]"]:checked').trigger('change');
      // formのどこかを変更したらフラグを立てる
      $("form").change(function(e){

        console.log("changed");

        //この処理を、バナーポジションが見えていない間は行わない
        if($('#sincloBanner').is(':visible')){
          $scope.beforeSpbPosition = $scope.sp_banner_position;
        }
        //初期表示タイミングと自動最大化設定の齟齬を無くす
        var MaxShowTimeSite = $("#MWidgetSettingMaxShowTime"),
            MaxShowTimePage = $("#MWidgetSettingMaxShowTimePage");

        if($scope.showTiming == "1" && $scope.showTime == "4"){
          $scope.showTime = "1";
          MaxShowTimeSite.prop('disabled', false);
          MaxShowTimePage.prop('disabled', true);
          $("#showTime1").prop('checked', true);
        } else if($scope.showTiming == "2" && $scope.showTime == "1"){
          $scope.showTime = "4";
          MaxShowTimeSite.prop('disabled', true);
          MaxShowTimePage.prop('disabled', false);
          $("#showTime4").prop('checked', true);
        } else if($scope.showTiming == "3" && ($scope.showTime == "1" || $scope.showTime == "4")){
          $scope.showTime = "5";
          MaxShowTimeSite.prop('disabled', true);
          MaxShowTimePage.prop('disabled', true);
          $("#showTime5").prop('checked', true);
        }
        $scope.changeFlg = true;
      });
      $(window).on('resize', function(e){
        if($scope.showWidgetType === 1) {
          $scope.resizeWidgetHeightByWindowHeight();
        }
      });
      $scope.resizeWidgetHeightByWindowHeight();
      $('#m_widget_simulator').css('visibility', 'visible');

      $(window).on('beforeunload', function(e) {
        if($scope.changeFlg) {
          return '行った変更が保存されない可能性があります。';
        }
      });

      if(Number($scope.closeButtonSettingToggle) !== 2 || Number($scope.closeButtonModeTypeToggle) !== 1) {
        $scope.showTimeBannerSettingDisable();
      }

      if(Number($scope.chat_init_show_textarea) === 2) {
        $scope.resizeChatArea();
      }
    });

    $scope.resizeWidgetHeightByWindowHeight = function() {
      if($('#miniTarget').height() > 0) {
        $('#miniTarget').css('height', 'auto');
      }
      if($('#callTab').length > 0) {
        $('#callTab').css('height', 'auto');
      }
      var windowHeight = $(window).innerHeight(),
          minCurrentWidgetHeight = $scope._getMinWidgetHeight(),
          currentWidgetHeight = $('#titleWrap').height() + $('#descriptionSet').height() + $('#miniTarget').height(),
          maxCurrentWidgetHeight = $scope._getMaxWidgetHeight(),
          changeTarget = ($('#chatTab').length > 0) ? $('#chatTalk') : $('#telContent'),
          delta = windowHeight - $scope.currentWindowHeight;

      if(windowHeight * 0.7 < currentWidgetHeight && delta === 0) {
        delta = (windowHeight * 0.7) - currentWidgetHeight;
      }

      // 変更後サイズ
      var afterWidgetHeight = currentWidgetHeight + delta;
      var changed = false;
      if(delta > 0 && afterWidgetHeight > maxCurrentWidgetHeight) {
        console.log('1 %s', delta);
        changed = true;
        changeTarget.height($scope._getMaxChatTalkHeight());
      } else if(delta < 0 && afterWidgetHeight < minCurrentWidgetHeight) {
        console.log('2-1 %s ', delta, minCurrentWidgetHeight, $scope._getMinChatTalkHeight());
        changed = true;
        changeTarget.height($scope._getMinChatTalkHeight());
        console.log('2-2 %s ', $('#sincloBox').height());
      } else if((delta < 0 && windowHeight * 0.7 < currentWidgetHeight) || (delta > 0 && windowHeight * 0.7 >= afterWidgetHeight)) {
        console.log('3 %s', delta);
        changed = true;
        changeTarget.height(changeTarget.height() + delta);
      }

      $scope.currentWindowHeight = windowHeight;
      if(changed) {
        $(document).trigger('onWidgetSizeChanged');
      }
    };

    $scope._getMaxWidgetHeight = function() {
      var offset = $scope._getMessageAreaOffset();
      switch(Number($scope.widgetSizeTypeToggle)) {
        case 1:
          return 405 - offset;
        case 2:
          return 496 - offset;
        case 3:
        case 4:
          return 596 - offset;
        case 5:
          return $scope.widget_custom_height - offset;
        default:
          return 496 - offset;
      }
    };

    $scope._getMinWidgetHeight = function() {
      var offset = $scope._getMessageAreaOffset();
      switch(Number($scope.widgetSizeTypeToggle)) {
        case 1:
        case 5:
          return 318 - offset;
        case 2:
          return 364 - offset;
        case 3:
        case 4:
          return 409 - offset;
        default:
          return 364 - offset;
      }
    };

    $scope._getMaxChatTalkHeight = function() {
      var offset = $scope._getMessageAreaOffset(true);
      if($('#chatTab').length > 0) {
        switch (Number($scope.widgetSizeTypeToggle)) {
          case 1:
            // 小
            return 194 + offset;
          case 2:
            return 284 + offset;
          case 3:
          case 4:
            return 374 + offset;
          default:
            return 284 + offset;
        }
      } else {
        // シェアリング
        switch (Number($scope.widgetSizeTypeToggle)) {
          case 1:
            // 小
            return 119;
          case 2:
            return 202;
          case 3:
          case 4:
            return 280;
          default:
            return 202;
        }
      }
    };

    $scope._getMinChatTalkHeight = function() {
      var offset = $scope._getMessageAreaOffset(true);
      if($('#chatTab').length > 0) {
        switch (Number($scope.widgetSizeTypeToggle)) {
          case 1:
          case 5:
            // 小orカスタム
            return 97 + offset;
          case 2:
            return 142 + offset;
          case 3:
          case 4:
            return 187 + offset;
          default:
            return 142 + offset;
        }
      } else {
        // シェアリング
        switch (Number($scope.widgetSizeTypeToggle)) {
          case 1:
          case 5:
            // 小orカスタム
            return 32;
          case 2:
            return 76;
          case 3:
          case 4:
            return 121;
          default:
            return 76;
        }
      }
    };

    $scope._getMessageAreaOffset = function(forChatTalkOffset) {
      var invisibleUIOffset = 0;
      if(!forChatTalkOffset) {
        if(!$('#sincloAccessInfo').is(':visible')) {
          invisibleUIOffset += 26.5;
        }
        invisibleUIOffset +=  53 - $('#descriptionSet').height();
      }
      if($('#messageBox').is(':visible')) {
        return 0 + invisibleUIOffset;
      } else if($('#miniFlexBoxHeight').is(':visible')) {
        return 27 + invisibleUIOffset;
      } else {
        // とりあえず表示されている状態
        return 0 + invisibleUIOffset;
      }
    };

    $scope.closeBtnDisableWhenShowBannerEnable = function() {
      $scope.settingShowTimeRadioButtonDisable($('#closeButtonSetting1'));
      $scope.settingShowTimeRadioButtonDisable($('#closeButtonModeType2'));
      $('#closeButtonSetting1').parent().addClass("commontooltip");
      $('#closeButtonSetting1').parent().attr('data-text', '「最大化する条件」が「小さなバナーを表示する」<br>に設定されているため、変更できません。');
      $('#closeButtonSetting1').parent().attr('data-content-position-top', '39');
      $('#closeButtonSetting1').parent().attr('data-balloon-position', '36.5');
      $('#closeButtonSetting1').parent().attr('data-balloon-width', '300');
      $("#closeButtonModeType2").parent().addClass("commontooltip");
      $('#closeButtonModeType2').parent().attr('data-text', '「最大化する条件」が「小さなバナーを表示する」<br>に設定されているため、変更できません。');
      $('#closeButtonModeType2').parent().attr('data-content-position-top', '39');
      $('#closeButtonModeType2').parent().attr('data-balloon-position', '4.5');
      $('#closeButtonModeType2').parent().attr('data-balloon-width', '300');

      addTooltipEvent();
    }

    $scope.closeBtnEnableWhenShowBannerDisable = function() {
      $scope.settingShowTimeRadioButtonEnable($('#closeButtonSetting1'));
      $scope.settingShowTimeRadioButtonEnable($('#closeButtonModeType2'));
      $('#closeButtonSetting1').parent().removeClass("commontooltip");
      $('#closeButtonSetting1').parent().removeAttr('data-text');
      $('#closeButtonSetting1').parent().removeAttr('data-content-position-top');
      $('#closeButtonSetting1').parent().removeAttr('data-balloon-position');
      $("#closeButtonModeType2").parent().removeClass("commontooltip");
      $('#closeButtonModeType2').parent().removeAttr('data-text');
      $('#closeButtonModeType2').parent().removeAttr('data-content-position-top');
      $('#closeButtonModeType2').parent().removeAttr('data-balloon-position');
      addTooltipEvent();
    }

    $scope.showTimeBannerSettingEnable = function() {
      $scope.settingShowTimeRadioButtonEnable($('#displayStyleType3'));
      $("#displayStyleType3").parent().removeClass("commontooltip");
      $('#displayStyleType3').parent().removeAttr('data-text');
      $('#displayStyleType3').parent().removeAttr('data-content-position-top');
      $('#displayStyleType3').parent().removeAttr('data-balloon-position');
      $('#displayStyleType3').parent().removeAttr('data-balloon-width');
      addTooltipEvent();
    }

    $scope.showTimeBannerSettingDisable = function() {
      $scope.settingShowTimeRadioButtonDisable($('#displayStyleType3'));
      $("#displayStyleType3").parent().addClass("commontooltip");
      $('#displayStyleType3').parent().attr('data-text', '「閉じるボタン」を有効にし<br>「小さなバナー表示」を選択するとご利用いただけます。');
      $('#displayStyleType3').parent().attr('data-content-position-top', '39');
      $('#displayStyleType3').parent().attr('data-balloon-position', '27.5');
      $('#displayStyleType3').parent().attr('data-balloon-width', '340');
      addTooltipEvent();
    }

    angular.element('#fileTagWrap').click(function(e){
      $("#MWidgetSettingUploadImage").val("");
    });

    // メイン画像のトリミング
    angular.element('#MWidgetSettingUploadImage').change(function(e){
      var files = e.target.files;
      if ( window.URL && files.length > 0 ) {
        var file = files[files.length-1];
        // jpeg/jpg/png
        var reg = new  RegExp(/image\/(png|jpeg|jpg)/i);
        if ( !reg.exec(file.type) ) {
            $("#MWidgetSettingUploadImage").val("");
            $scope.uploadImageError = "画像はpng,jpeg,jpgのいずれかのみ利用可能です"
        }

        var url = window.URL.createObjectURL(file);

        openTrimmingDialog(function(){
          beforeTrimmingInit(url, $('#trim'));
          trimmingInit($scope, null, 62 / 70, "main_image");
        });
      }
    });

    //チャットボットアイコンのトリミング
    angular.element('#MWidgetSettingUploadBotIcon').change(function(e){
      console.log("ボットアイコンのトリミング");
      var files = e.target.files;
      if ( window.URL && files.length > 0 ) {
        var file = files[files.length-1];
        // jpeg/jpg/png
        var reg = new  RegExp(/image\/(png|jpeg|jpg)/i);
        if ( !reg.exec(file.type) ) {
          $("#MWidgetSettingUploadBotIcon").val("");
          $scope.uploadImageError = "画像はpng,jpeg,jpgのいずれかのみ利用可能です"
        }

        var url = window.URL.createObjectURL(file);

        openTrimmingDialog(function(){
          beforeTrimmingInit(url, $('#trim'));
          trimmingInit($scope, null, 1, "chatbot_icon");
        });
      }
    });

    //オペレーターアイコンのトリミング
    angular.element('#MWidgetSettingUploadOpIcon').change(function(e){
      console.log("オペレーターアイコンのトリミング");
      var files = e.target.files;
      if ( window.URL && files.length > 0 ) {
        var file = files[files.length-1];
        // jpeg/jpg/png
        var reg = new  RegExp(/image\/(png|jpeg|jpg)/i);
        if ( !reg.exec(file.type) ) {
          $("#MWidgetSettingUploadOpIcon").val("");
          $scope.uploadImageError = "画像はpng,jpeg,jpgのいずれかのみ利用可能です"
        }

        var url = window.URL.createObjectURL(file);

        openTrimmingDialog(function(){
          beforeTrimmingInit(url, $('#trim'));
          trimmingInit($scope, null, 1, "operator_icon");
        });
      }
    });

    angular.element('input[name="data[MWidgetSetting][close_button_setting]"]').on('change', function(e){
      switch($('#' + this.id).val()) {
        case "1":
          // 無効
          $scope.showTimeBannerSettingDisable();
          break;
        case "2":
          // 有効
          if($('#closeButtonModeType1').is(':checked')) {
            $scope.showTimeBannerSettingEnable();
          } else {
            $scope.showTimeBannerSettingDisable();
          }
          break;
      }
    });

    angular.element('input[name="data[MWidgetSetting][show_subtitle]"]').on('change', function(e){
      //企業名を表示する場合
      if(e.currentTarget.id == 'showSubtitle1') {
        $('#widgetTitleNameTypeLabel1').css('display','block');
        $('#widgetTitleNameTypeLabel2').css('display','block');
      }
      //企業名を表示しない場合
      if(e.currentTarget.id == 'showSubtitle2') {
        $('#widgetTitleNameTypeLabel1').css('display','none');
        $('#widgetTitleNameTypeLabel2').css('display','none');
      }
    });

    angular.element('input[name="data[MWidgetSetting][show_description]"]').on('change', function(e){
      //説明文を表示する場合
      if(e.currentTarget.id == 'showDescription1') {
        $('#widgetTitleExplainTypeLabel1').css('display','block');
        $('#widgetTitleExplainTypeLabel2').css('display','block');
      }
      //説明文を表示しない場合
      if(e.currentTarget.id == 'showDescription2') {
        $('#widgetTitleExplainTypeLabel1').css('display','none');
        $('#widgetTitleExplainTypeLabel2').css('display','none');
      }
    });

    $scope.$watch('closeButtonModeTypeToggle', function() {
    switch($scope.closeButtonModeTypeToggle) {
      case "1": // 小さなバナー表示
        if(String($scope.closeButtonSettingToggle) === "2") $scope.showTimeBannerSettingEnable();
        break;
      case "2":
        $scope.showTimeBannerSettingDisable();
        break;
    }
  });

  $scope.$watch('chat_init_show_textarea', function() {
    $scope.resizeChatArea();
  });

  $scope.resizeChatArea = function() {
    var offset = (Number($scope.showWidgetType) !== 2) ? 74 : 64;
    switch(Number($scope.chat_init_show_textarea)) {
      case 1:
        $('#chatTalk').height($('#chatTalk').height() - offset);
        break;
      case 2:
        $('#chatTalk').height($('#chatTalk').height() + offset);
        break;
    }
  };

    $('ul.settingList input').on('mousedown', function() {
      if (!$(this).hasClass('ignore-click-event')) {
        $scope.switchWidget(1);
        $scope.$apply();
      }
    });

    angular.element(window).on("click", ".widgetCtrl", function(e){
        var clickTab = $(this).data('tab');
        if ( clickTab === $scope.widget.showTab ) return false;
        $scope.widget.showTab = clickTab;
        $scope.$apply();
    });

    $("#MWidgetSettingChatMessageWithAnimation").on("click", function(e){
      var checked = $(this).prop('checked');
      var targetMessageUI = $('.showAnimationSample');
      //いったん非表示にする
      targetMessageUI.css('visibility', 'hidden');
      if(checked) {
        targetMessageUI.addClass('effect_left');
      } else {
        targetMessageUI.removeClass('effect_left');
      }
      // 設定が適用されたことをプレビューで見やすいようにずらして表示
      setTimeout(function(){
        targetMessageUI.css('visibility', 'visible');
      },600);
    });

    $scope.changeSubtitle = function() {
      if(String ($('#MWidgetSettingSubTitle').val()) === "" && Number($('input[name="data[MWidgetSetting][show_subtitle]"]:checked').val()) === 1) {
        $('#widgetSubTitle').text("　");
      } else {
        $('#widgetSubTitle').text($scope.sub_title);
      }
    }

    $scope.changeDescription = function() {
      if(String ($('#MWidgetSettingDescription').val()) === "" && Number($('input[name="data[MWidgetSetting][show_description]"]:checked').val()) === 1){
        $('#widgetDescription').text("　");
      } else {
        $('#widgetDescription').text($scope.description);
      }
    }



    $scope.$watch('chat_message_copy', function(){
      // 代入される値の型にバラつきがあるので文字列で統一させる
      $scope.chat_message_copy = Boolean(Number($scope.chat_message_copy));
      $("#MWidgetSettingChatMessageCopy").prop("checked", $scope.chat_message_copy);
    });

    //各チェックボックスがクリックされた時の値を明示的に制御する
    $("#MWidgetSettingReBorderNone").on("click", function(e){
      //企業側吹き出し枠線なし
      var chk = document.getElementById('MWidgetSettingReBorderNone').checked;
      var element = document.getElementById('MWidgetSettingReBorderColor');
      if(chk) {
        $scope.re_border_none = true;
        element.style.backgroundColor = "#FFFFFF";
        element.style.color = "#909090";
        $scope.re_border_color = "なし"
      }
      else{
//         $scope.re_border_none = false;
//         //現在設定されている吹き出し枠線色に変更
//         //var colorid = $scope.chat_talk_border_color;
//        var colorid = "<?= CHAT_TALK_BORDER_COLOR ?>";
//         $scope.re_border_color = colorid;
//         var rgb = $scope.checkRgbColor(colorid);
//         element.style.backgroundColor = colorid;
//         element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
        document.getElementById('MWidgetSettingReBorderNone').checked = true;
        $scope.changeReBorderColor();
      }
    });
    $("#MWidgetSettingReBorderNone").on("load", function(e){
      var chk = document.getElementById('MWidgetSettingReBorderNone').checked;
    });
    //load
    $("#MWidgetSettingSeBorderNone").on("click", function(e){
      //訪問者側吹き出し枠線なし
      var chk = document.getElementById('MWidgetSettingSeBorderNone').checked;
      var element = document.getElementById('MWidgetSettingSeBorderColor');
      if(chk) {
        $scope.se_border_none = true;
        element.style.backgroundColor = "#FFFFFF";
        element.style.color = "#909090";
        $scope.se_border_color = "なし"
      }
      else{
//         $scope.se_border_none = false;
//         //現在設定されている吹き出し枠線色に変更
//         //var colorid = $scope.chat_talk_border_color;
//        var colorid = "<?= CHAT_TALK_BORDER_COLOR ?>";
//         $scope.se_border_color = colorid;
//         var rgb = $scope.checkRgbColor(colorid);
//         element.style.backgroundColor = colorid;
//         element.style.color = $scope.checkTxtColor(rgb['r'],rgb['g'],rgb['b']);
        document.getElementById('MWidgetSettingSeBorderNone').checked = true;
        $scope.changeSeBorderColor();
      }
    });
    $("#MWidgetSettingMessageBoxBorderNone").on("click", function(e){
      //メッセージボックス枠線なし
      var chk = document.getElementById('MWidgetSettingMessageBoxBorderNone').checked;
      var element = document.getElementById('MWidgetSettingMessageBoxBorderColor');
      if(chk) {
        $scope.message_box_border_none = true;
        element.style.backgroundColor = "#FFFFFF";
        element.style.color = "#909090";
        $scope.message_box_border_color = "なし"
      }
      else{
//         //現在設定されている吹き出し枠線色に変更
        document.getElementById('MWidgetSettingMessageBoxBorderNone').checked = true;
        $scope.changeMessageBoxBorderColor();
      }
    });

    $("#MWidgetSettingWidgetOutsideBorderNone").on("click", function(e){
      //ウィジェット外枠線なし
      var chk = document.getElementById('MWidgetSettingWidgetOutsideBorderNone').checked;
      var element = document.getElementById('MWidgetSettingWidgetBorderColor');
      if(chk) {
        $scope.widget_outside_border_none = true;
        element.style.backgroundColor = "#FFFFFF";
        element.style.color = "#909090";
        $scope.widget_border_color = "なし"
      }
      else{
        document.getElementById('MWidgetSettingWidgetOutsideBorderNone').checked = true;
        $scope.changeWidgetBorderColor();
      }
    });

    $("#MWidgetSettingWidgetInsideBorderNone").on("click", function(e){
      //ウィジェット内枠線なし
      var chk = document.getElementById('MWidgetSettingWidgetInsideBorderNone').checked;
      var element = document.getElementById('MWidgetSettingWidgetInsideBorderColor');
      if(chk) {
        $scope.widget_inside_border_none = true;
        element.style.backgroundColor = "#FFFFFF";
        element.style.color = "#909090";
        $scope.widget_inside_border_color = "なし"
      }
      else{
        document.getElementById('MWidgetSettingWidgetInsideBorderNone').checked = true;
        $scope.changeWidgetInsideBorderColor();
      }
    });
    //高度な設定を行う行わないを制御するチェックボックス
    $("#MWidgetSettingColorSettingType").on("click", function(e){
      var checked = $(this).prop('checked');
      if(checked) {
        $scope.color_setting_type = '1';
      }
      else{
        $scope.color_setting_type = '0';
      }
    });

    //メッセージBOXにフォーカスが当たった、外れた時の処理
    $("#MWidgetSettingMessageBoxTextColor")
    .focusin(function(e) {
      $("#sincloChatMessage").val("カラーテスト");
    })
    .focusout(function(e) {
      $("#sincloChatMessage").val("");
    });

    angular.element(window).on("focus", ".showSp", function(e){
      //スクロール中非表示と自動で最大化しないの設定値を変更した場合はswitchさせないようにする
      if(e.currentTarget.id === "MWidgetSettingSpScrollViewSetting" || e.currentTarget.id === "MWidgetSettingSpAutoOpenFlg"){
        return;
      }
      $scope.switchWidget(3);
    });

    angular.element(window).on("focus", ".showNormal", function(e){
      $scope.switchWidget(1);
    });

    angular.element(window).on("focus", ".showHeader", function(e){
        if ( $scope.showWidgetType === 1 ) return false;
        $scope.$apply();
    });
    angular.element(window).on("focus", ".showChat", function(e){
        $scope.widget.showTab = "chat";
        $scope.$apply();
    });

    angular.element(window).on("focus", ".showTel", function(e){
        $scope.widget.showTab = "call";
        $scope.switchWidget(1);
        $scope.$apply();
    });

    angular.element(window).on("click", ".widgetCtrl", function(e){
        var clickTab = $(this).data('tab');
        if ( clickTab === $scope.widget.showTab ) return false;
        $scope.widget.showTab = clickTab;
        $scope.$apply();
    });

    //位置調整
    $scope.$watch(function(){
      return {'openFlg': $scope.openFlg, 'showWidgetType': $scope.showWidgetType, 'widgetSizeType': $scope.widgetSizeTypeToggle, 'chat_radio_behavior': $scope.chat_radio_behavior, 'chat_trigger': $scope.chat_trigger, 'show_name': $scope.show_name, 'show_automessage_name': $scope.show_automessage_name, 'widget.showTab': $scope.widget.showTab, 'sp_maximize_size_type': $scope.sp_maximize_size_type};
    },
    function(){
      var main = document.getElementById("miniTarget");
      if ( !main ) return false;
      if ( $scope.openFlg ) {
        setTimeout(function(){
          angular.element("#sincloBox").addClass("open");
          var height = 0;
          for(var i = 0; main.children.length > i; i++){
              height += main.children[i].offsetHeight;
          }
          main.style.height = height + "px";
        }, 0);
      }
      else {
        angular.element("#sincloBox").removeClass("open");
        main.style.height = "0";
      }
    }, true);

    //位置調整
    $scope.$watch(function(){
      return {'widgetSizeType': $scope.widgetSizeTypeToggle};
    },
    function(){
      $scope.switchWidget(1); // 標準に切り替える
    }, true);

    $scope.saveAct = function (){
      // 保存ボタンが押されたらconfirmを出さない
      loading.load.start();
      $scope.changeFlg = false;
        $('#widgetShowTab').val($scope.widget.showTab);
        $('#MWidgetSettingMainImage').val($scope.main_image);
        $('#MWidgetSettingChatbotIcon').val($scope.chatbot_icon);
        $('#MWidgetSettingOperatorIcon').val($scope.operator_icon);
        $('#TrimmingInfo').val($scope.trimmingInfo);
        $('#TrimmingBotIconInfo').val($scope.trimmingBotIconInfo);
        $('#TrimmingOpIconInfo').val($scope.trimmingOpIconInfo);
        $('#MWidgetSettingIndexForm').submit();
    };

    $scope.reloadAct = function (){
      // 元に戻すボタンが押されたらconfirmを出さない
      $scope.changeFlg = false;
      window.location.reload();
    };

    //param : String型 ng-classで付けたい情報を渡す
    //TODO 受け取った情報をカンマで分割してfor文を回したほうがいい
    $scope.viewWidgetSetting = function (param){
      var widgetClasses = {};
      if(typeof param !== 'undefined' && param.indexOf("size") !== -1){
        widgetClasses = setWidgetSizeSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("sp") !== -1){
        widgetClasses = setSmartPhoneSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("toptitle") !== -1){
        widgetClasses = setTitlePositionSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("topname") !== -1){
        widgetClasses = setHeaderNamePositionSetting(widgetClasses);;
      }
      if(typeof param !== 'undefined' && param.indexOf("desc") !== -1){
        widgetClasses = setHeaderDescPositionSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("topimg") !== -1){
        widgetClasses = setHeaderImageSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("outsideborder") !== -1){
        widgetClasses = setOutSideBorderSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("insideborder") !== -1){
        widgetClasses = setInSideBorderSetting(widgetClasses);
      }
      if(typeof param !== 'undefined' && param.indexOf("headercontent") !== -1){
        widgetClasses = setHeaderContentSetting(widgetClasses);
      }
      return widgetClasses;
    };

    /*ng-class用のオブジェクトを設定する関数群--開始--*/


    var setWidgetSizeSetting = function (obj){
      obj.middleSize = judgeSize("middle");
      obj.largeSize = judgeSize("large");
      return obj;
    };

    var setSmartPhoneSetting = function (obj){
      obj.spText = isSmartPhonePortrait();
      obj.sp = isSmartPhonePortrait();
      return obj;
    };

    var setTitlePositionSetting = function(obj){
      if (Number($scope.widget_title_top_type) === 1){
        obj["leftPositionTitle"] = true;
      } else if (Number($scope.widget_title_top_type) === 2){
        obj["centerPositionTitle"] = true;
      }
      return obj;
    };

    var setHeaderNamePositionSetting = function(obj){
      if (Number($scope.subTitleToggle) === 2){
        obj["noCompany"] = true;
      } else if (Number($scope.subTitleToggle) === 1){
        if (Number($scope.widget_title_name_type) === 1){
          obj["leftPosition"] = true;
        } else if (Number($scope.widget_title_name_type) === 2) {
          obj["centerPosition"] = true;
        }
      }
      return obj;
    };

    var setHeaderDescPositionSetting = function(obj){
      if (Number($scope.descriptionToggle) === 2){
        obj["noExplain"] = true;
      } else if (Number($scope.descriptionToggle) === 1){
        if (Number($scope.widget_title_explain_type) === 1){
          obj["leftPosition"] = true;
        } else if (Number($scope.widget_title_explain_type) === 2) {
          obj["centerPosition"] = true;
        }
      }
      return obj;
    };

    var setHeaderImageSetting = function(obj){
      if ( Number($scope.mainImageToggle) === 2
         ||(Number($scope.sp_header_light_flg) === 1 && $scope.openFlg && $('#widgetTitle').hasClass("sp"))
         ||((Number($scope.minimizedDesignToggle) === 2 || Number($scope.minimizedDesignToggle) === 3) && !$scope.openFlg && $('#widgetTitle').hasClass("sp"))
         ||(Number($scope.minimizedDesignToggle) === 3 && !$scope.openFlg && !$('#widgetTitle').hasClass("sp"))
         ){
        obj["NoImage"] = true;
      } else if (Number($scope.mainImageToggle) === 1){
        obj["Image"] = true;
      }
      return obj;
    };

    var setOutSideBorderSetting = function(obj){
      if (Number($scope.widget_outside_border_none === '' || $scope.widget_outside_border_none === false)){
        obj["notNoneWidgetOutsideBorder"] = true;
      } else {

      }
      return obj;
    };

    var setInSideBorderSetting = function(obj){
      if (Number($scope.widget_inside_border_none === '' || $scope.widget_inside_border_none === false)){
        obj["notNone"] = true;
      } else {

      }
      return obj;
    };

    var setHeaderContentSetting = function(obj){
      if(Number($scope.descriptionToggle) === 1 && Number($scope.subTitleToggle) === 1){
        obj["twoContents"] = true;
      } else if (Number($scope.descriptionToggle) === 1 || Number($scope.subTitleToggle) === 1){
        obj["oneContents"] = true;
      } else if (Number($scope.descriptionToggle) === 2 || Number($scope.subTitleToggle) === 2){
        obj["noContents"] = true;
      }
      return obj;
    };

    /*ng-class用のオブジェクトを設定する関数群--終了--*/




    //size : String型 small,middle,large のいずれか
    //現状の設定が渡されたサイズかどうかを判別する
    //return : boolean型
    var judgeSize = function(size){

       //通常表示でない場合は判定させない
      if(Number($scope.showWidgetType !== 1)){
        return false;
      }

      switch(size){
      case "small":
        //現状設定が無いため判別無し
        return true;
      case "middle":
        return Number($scope.widgetSizeTypeToggle) === 2;
      case "large":
        return Number($scope.widgetSizeTypeToggle) === 3 || Number($scope.widgetSizeTypeToggle) === 4;
      default:
        //デフォルトはfalseを返す
        return false;
      }
    };

    var isSmartPhonePortrait = function(){
      return Number($scope.showWidgetType) === 3;
    };

    angular.element(window).on("click", ".widgetOpener", function(){
      if(Number($scope.closeButtonSettingToggle) === 2 && Number($scope.closeButtonModeTypeToggle) === 1){
        //閉じるボタンが無効な場合や、非表示にする場合は2段階オプションの判定をさせない
        if(Number($scope.showWidgetType) === 3 && Number($scope.sp_widget_view_pattern) === 3){
          //2段階表示の時は即座にバナー状態にさせる
          $scope.closeAct();
        }
      }
      var sincloBox = document.getElementById("sincloBox");
      var nextFlg = true;
      if ( $scope.openFlg ) {
        nextFlg = false;
      }
      $scope.openFlg = nextFlg;
      $scope.$apply();
    });
});

sincloApp.directive('errSrc', function(){
  return {
    link: function(scope,elements, attrs) {
      if ( attrs.ngSrc === "" ) {
        attrs.$set('src', attrs.errSrc);
      }
      elements.bind("error", function(){
        if ( attrs.ngSrc != attrs.errSrc ) {
          attrs.$set('src', attrs.errSrc);
        }
      });
    }
  };
});

/* [ #2243 ] IE緊急対応 */
// TODO 仮対応のため正式な対応をする
var sincloChatMessagefocusFlg = true;
$("body").on('focus', '#sincloChatMessage', function(e){
  if ( sincloChatMessagefocusFlg ) {
    e.target.value = "";
    sincloChatMessagefocusFlg = false;
  }
});
/* [ #2243 ] IE緊急対応 */

//モーダル画面
function openTrimmingDialog(callback){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'MWidgetSettings', 'action' => 'remoteTimmingInfo']) ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-widget-image-trimming', 'トリミング', 'moment');
      callback();
    }
  });
}
</script>
