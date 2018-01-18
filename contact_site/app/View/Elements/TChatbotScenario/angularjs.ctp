<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate']);

sincloApp.controller('MainController', ['$scope', '$timeout', function($scope, $timeout) {
  //thisを変数にいれておく
  var self = this;

  this.actionList = <?php echo json_encode($chatbotScenarioActionList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.setActionList = [];
  $scope.test = ['a'];
  $scope.widgetSettings = getWidgetSettings();
  console.log(typeof $scope.widgetSettings);
  console.log($scope.widgetSettings);

  // アクションの追加
  this.addItem = function(actionType) {
    if (actionType in this.actionList) {
      var item = this.actionList[actionType];
      item.actionType = actionType;
      $scope.setActionList.push(angular.copy(angular.merge(item, item.default)));
    }
  };

  // アクションの削除
  this.removeItem = function(setActionId) {
    $scope.setActionList.splice(setActionId, 1);
  };


  // アクションの追加・削除を検知する
  $scope.$watchCollection('setActionList', function() {
    $timeout(function() {
      angular.forEach($scope.setActionList, $scope.watchSetActionList);
    }, 10)
  }, true);

  // アクション内の変更を検知する
  $scope.watchSetActionList = function(action, index) {
    $scope.$watchCollection('setActionList[' + index + ']', function(newObject, oldObject) {
      if(typeof newObject === 'undefined' || newObject === oldObject) return;

      // 送信メッセージ
      if(typeof newObject.message !== 'undefined' && newObject.message !== '') {
        document.getElementById('action_' + index + '_message').innerHTML = createMessage(newObject.message);
      }
      // エラーメッセージ
      if(typeof newObject.errorMessage !== 'undefined' && newObject.errorMessage !== '') {
        document.getElementById('action_' + index + '_error_message').innerHTML = createMessage(newObject.errorMessage);
      }
      // 確認メッセージ
      if(typeof newObject.confirmMessage !== 'undefined' || typeof newObject.success !== 'undefined' || typeof newObject.cancel !== 'undefined') {
        var confirmMessage = newObject.confirmMessage;
        var successMessage = newObject.success !== '' ? 'ｓ[] ' + newObject.success : '';
        var cancelMessage = newObject.cancel !== '' ? '[] ' + newObject.cancel : '';

        var message = [confirmMessage, successMessage, cancelMessage].filter( function(string) {
          return string !== '';
        }).join('\n');
        if(message === '') return;
        document.getElementById('action_' + index + '_confirm_message').innerHTML = createMessage(message);
      }
    });
  }

  // シミュレーターの起動
  this.openSimulatorDialog = function() {
    console.log("=== call func openSimulatorDialog ===");
  };

  this.saveAct = function() {
    console.log('=== call func saveAct ===');
    // TODO: 保存ボタン押下時の処理
    submitAct();
  };

  $scope.makeFaintColor = function(){
    var defColor = "#F1F5C8";
    //仕様変更、常に高度な設定が当たっている状態とする
    defColor = $scope.widgetSettings.re_background_color;
//       if($scope.color_setting_type === '1'){
//         defColor = $scope.re_background_color;
//       }
//       else{
//         if ( $scope.main_color.indexOf("#") >= 0 ) {
//           var code = $scope.main_color.substr(1), r,g,b;
//           if (code.length === 3) {
//             r = String(code.substr(0,1)) + String(code.substr(0,1));
//             g = String(code.substr(1,1)) + String(code.substr(1,1));
//             b = String(code.substr(2)) + String(code.substr(2));
//           }
//           else {
//             r = String(code.substr(0,2));
//             g = String(code.substr(2,2));
//             b = String(code.substr(4));
//           }
//           var balloonR = String(Math.floor(255 - (255 - parseInt(r,16)) * 0.1));
//           var balloonG = String(Math.floor(255 - (255 - parseInt(g,16)) * 0.1));
//           var balloonB = String(Math.floor(255 - (255 - parseInt(b,16)) * 0.1));
//           defColor = 'rgb(' + balloonR  + ', ' +  balloonG  + ', ' +  balloonB + ')';
//         }
//       }
    return defColor;
  };

  $scope.getTalkBorderColor = function(chk){
    var defColor = "#E8E7E0";
    //仕様変更、常に高度な設定が当たっている状態とする
    if(chk === 're'){
      defColor = $scope.widgetSettings.re_border_color;
    }
    else{
      defColor = $scope.widgetSettings.se_border_color;
    }
//       if($scope.color_setting_type === '1'){
//         if(chk === 're'){
//           defColor = $scope.re_border_color;
//         }
//         else{
//           defColor = $scope.se_border_color;
//         }
//       }
//       else{
//         defColor = $scope.chat_talk_border_color;
//       }
    return defColor;
  }

  $scope.getSeBackgroundColor = function(){
    var defColor = "#FFFFFF";
    //仕様変更、常に高度な設定が当たっている状態とする
    defColor = $scope.widgetSettings.se_background_color;
//       if($scope.color_setting_type === '1'){
//         defColor = $scope.se_background_color;
//       }
    return defColor;
  }
}]);

function getWidgetSettings() {
  var json = JSON.parse(document.getElementById('TChatbotScenarioWidgetSettings').value);
  var widgetSettings = [];
  for (var item in json) {
    widgetSettings[item] = json[item];
  }

  // 担当者表示は企業名で固定する
  widgetSettings.show_name = <?=C_WIDGET_SHOW_COMP?>;
  return widgetSettings;
}

function submitAct() {
  $('TChatbotScenarioEntryForm').submit();
}

$(document).ready(function() {
  // ツールチップの表示制御
  addTooltipEvent();
});

function addTooltipEvent() {
  $(document).off('mouseenter','.questionBtn').on('mouseenter','.questionBtn', function(event){
    var parentClass = $(this).parent().parent().attr('class');
    var targetObj = $("#" + parentClass.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70) + 'px',
      left: $(this).offset().left - 65 + 'px'
    });
  });
  $(document).off('mouseleave','.questionBtn').on('mouseleave','.questionBtn', function(event){
    var parentClass = $(this).parent().parent().attr('class');
    var targetObj = $("#" + parentClass.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });
}


function createMessage(val) {
  var strings = val.split('\n');
  var radioCnt = 1;
  var linkReg = RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/);
  var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
  var htmlTagReg = RegExp(/<\/?("[^"]*"|'[^']*'|[^'">])*>/g)
  var radioName = "sinclo-radio0";
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
}
</script>
