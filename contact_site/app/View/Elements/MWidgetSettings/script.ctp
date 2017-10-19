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

sincloApp.controller('WidgetCtrl', function($scope){
    $scope.main_image = "<?=$this->formEx->val($this->data['MWidgetSetting'], 'main_image')?>";

    $scope.showWidgetType = 1; // デフォルト表示するウィジェット
    $scope.openFlg = true;

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
        document.getElementById("switch_widget").value = num;
      }
      $scope.openFlg = true;
    }

    //バナーから通常の表示に戻るときの処理
    $scope.bannerSwitchWidget = function(){
      var lastSwitchWidget = Number(document.getElementById("switch_widget").value);
      sincloBox.style.display = 'block';
      $scope.switchWidget(lastSwitchWidget);
      $scope.openFlg = false;
      return;
    }

    $scope.showChooseImg = function(){
      return $scope.mainImageToggle == '1';
    }

    $scope.showcloseButtonMode = function(){
      if($scope.closeButtonSettingToggle == '2' && $scope.mainImageToggle != '4'){
        $("#closeButtonMode").show();
      }
      else{
        $("#closeButtonMode").hide();
      }
      return;
    }

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
      var oneByteCount = 0;
      var towByteCount = 0;

      if(text.length === 0) {
        $('#sincloBanner').css("width","38px");
        return;
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
      var bannerWidth = (oneByteCount * 8) + (towByteCount * 12.7) + 40;
      $('#sincloBanner').css("width", bannerWidth + "px");

      var targetSpan = $('#bannertext').get(0);

      if(targetSpan) {
        console.log(targetSpan.offsetWidth);
        bannerWidth = targetSpan.offsetWidth + 40;
        $('#sincloBanner').css("width", bannerWidth + "px");
      }
    }

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
        defColor = "rgba(" + parseInt(r,16) + ", " + parseInt(g,16) + ", " + parseInt(b,16) + ", 0.1)";
      }
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

    $scope.inputInitToggle = function(item){
      return (item) ? 1 : 2;
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
        document.getElementById("switch_widget").value = $scope.showWidgetType;
      }
      return res;
    };

//     //旧・シンプル表示
//     $scope.spHeaderLightToggle = function(){
//      return ( $scope.showWidgetType === 3 && $scope.sp_header_light_flg === '<?=C_SELECT_CAN?>' );
//     };

    $scope.showGallary = function(){
      $.ajax({
        type: 'post',
        data: {
          color: $scope.main_color,
        },
        cache: false,
        dataType: 'html',
        url: "<?= $this->Html->url('/MWidgetSettings/remoteShowGallary') ?>",
        success: function(html){
          modalOpen.call(window, html, 'p-show-gallary', 'ギャラリー', 'moment');
          popupEvent.customizeBtn = function(name){
            $scope.main_image = "<?=$gallaryPath?>" + name;
            $("#MWidgetSettingUploadImage").val("");
            $scope.$apply();
            popupEvent.close();
          };
        }
      });
    };

    //ウィジェットサイズがクリックされた時の動作
    $scope.clickWidgetSizeTypeToggle = function(siz){
      var settingTitle = document.getElementById('MWidgetSettingTitle');
      var settingSubTitle = document.getElementById('MWidgetSettingSubTitle');
      var settingDescription = document.getElementById('MWidgetSettingDescription');
      var titleLength = 12;
      var subTitleLength = 15;
      var descriptionLength = 15;
      switch (siz) {
       //大きさによってトップタイトル、企業名、説明文のmaxlengthを可変とする
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
          titleLength = 19;
          subTitleLength = 24;
          descriptionLength = 24;
          break;
      }
      settingTitle.maxLength = titleLength;
//       if(settingTitle.value.length > titleLength){
//         $scope.title = settingTitle.value.substring(0, titleLength);
//       }
      settingSubTitle.maxLength = subTitleLength;
//       if(settingSubTitle.value.length > subTitleLength){
//         $scope.sub_title = settingSubTitle.value.substring(0, subTitleLength);
//       }
      settingDescription.maxLength = descriptionLength;
//       if(settingDescription.value.length > descriptionLength){
//         $scope.description = settingDescription.value.substring(0, descriptionLength);
//       }
    }

    //最小化時のデザインがクリックされた時の動作
    $scope.clickMinimizedDesignToggle = function(tag){
      if($scope.showWidgetType !== tag){
        $scope.switchWidget(tag);
      }
      $scope.openFlg = false;
    }

    $scope.settingShowTimeRadioButtonEnable = function(jq) {
      jq.prop('disabled',false).parent().css('color','');
      jq.next().css('color','');
    }

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

    angular.element(window).on('load',function(e){
      $('[name="data[MWidgetSetting][show_timing]"]:checked').trigger('change');
    });

    angular.element('#MWidgetSettingUploadImage').change(function(e){
        var files = e.target.files;
        if ( window.URL && files.length > 0 ) {
            var file = files[files.length-1];
            // 2MB以下である
            if (file.size > 2000000) {
                $("#MWidgetSettingUploadImage").val("");
                return false;
            }
            // jpeg/jpg/png
            var reg = new  RegExp(/image\/(png|jpeg|jpg)/i);
            if ( !reg.exec(file.type) ) {
                $("#MWidgetSettingUploadImage").val("");
                return false;
            }
            var url = window.URL.createObjectURL(file);
            $scope.main_image = url;
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

    $scope.$watch('chat_message_copy', function(){
      // 代入される値の型にバラつきがあるので文字列で統一させる
      $scope.chat_message_copy = Boolean(Number($scope.chat_message_copy));
      $("#MWidgetSettingChatMessageCopy").prop("checked", $scope.chat_message_copy);
    });

    angular.element(window).on("focus", ".showSp", function(e){
        $scope.switchWidget(3);
    });

    angular.element(window).on("focus", ".showHeader", function(e){
        if ( $scope.showWidgetType === 1 ) return false;
//        if ( $scope.showWidgetType === 3 ) {
//           if ( !$scope.spHeaderLightToggle() ) return false;
//         }
//        $scope.switchWidget(1);
        $scope.$apply();
    });
    angular.element(window).on("focus", ".showChat", function(e){
        $scope.widget.showTab = "chat";
//         if ( $scope.spHeaderLightToggle() ) {
//           $scope.switchWidget(1);
//         }
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
      return {'openFlg': $scope.openFlg, 'showWidgetType': $scope.showWidgetType, 'widgetSizeType': $scope.widgetSizeTypeToggle, 'chat_radio_behavior': $scope.chat_radio_behavior, 'chat_trigger': $scope.chat_trigger, 'show_name': $scope.show_name, 'widget.showTab': $scope.widget.showTab};
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
        $('#widgetShowTab').val($scope.widget.showTab);
        $('#MWidgetSettingMainImage').val($scope.main_image);
        $('#MWidgetSettingIndexForm').submit();
    }

    angular.element(window).on("click", ".widgetOpener", function(){
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

</script>
