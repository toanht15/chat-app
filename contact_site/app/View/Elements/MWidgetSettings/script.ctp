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
        /* ウィジェットが最小化されていたら最大化する */
        if ( !$scope.openFlg ) { // 最小化されている場合
          var main = document.getElementById("miniTarget");  // 非表示対象エリア
          var height = 0;
          for(var i = 0; main.children.length > i; i++){ // 非表示エリアのサイズを計測する
            if ( Number(num) === 3 && main.children[i].id === 'navigation' ) continue; // SPの場合はナビゲーションは基本表示しない
            height += main.children[i].offsetHeight;
          }
          main.style.height = height + "px";
        }
      }

      $scope.openFlg = true;
    }

    $scope.showChooseImg = function(){
      return $scope.mainImageToggle == '1';
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

    $scope.spHeaderLightToggle = function(){
      return ( $scope.showWidgetType === 3 && $scope.sp_header_light_flg === '<?=C_SELECT_CAN?>' );
    };

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

    angular.element(window).on("focus", ".showSp", function(e){
        $scope.switchWidget(3);
    });

    angular.element(window).on("focus", ".showHeader", function(e){
        if ( $scope.showWidgetType === 1 ) return false;
        if ( $scope.showWidgetType === 3 ) {
          if ( !$scope.spHeaderLightToggle() ) return false;
        }
        $scope.switchWidget(1);
        $scope.$apply();
    });
    angular.element(window).on("focus", ".showChat", function(e){
        $scope.widget.showTab = "chat";
        if ( $scope.spHeaderLightToggle() ) {
          $scope.switchWidget(1);
        }
        $scope.$apply();
    });

    angular.element(window).on("focus", ".showTel", function(e){
        $scope.widget.showTab = "call";
        $scope.switchWidget(1);
        $scope.$apply();
    });

    $scope.$watch(function(){
      return {'openFlg': $scope.openFlg, 'showWidgetType': $scope.showWidgetType};
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
