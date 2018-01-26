<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate']);

sincloApp.controller('MainController', ['$scope', '$timeout', function($scope, $timeout) {
  //thisを変数にいれておく
  var self = this;

  this.actionList = <?php echo json_encode($chatbotScenarioActionList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.setActionList = [];
  $scope.widgetSettings = getWidgetSettings();

  // メッセージ間隔は同一の設定を各アクションに設定しているため、テキスト発言からデフォルト値を取得する
  $scope.messageIntervalTimeSec = this.actionList[1].default.messageIntervalTimeSec;

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
  $scope.watchActionList = [];
  $scope.$watchCollection('setActionList', function() {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      angular.forEach($scope.setActionList, $scope.watchSetActionList);
    });
  });

  // アクション内の変更を検知する
  $scope.watchSetActionList = function(action, index) {
    // watchの破棄
    if(typeof $scope.watchActionList[index] !== 'undefined') {
      $scope.watchActionList[index]();
    }

    $scope.watchActionList[index] = $scope.$watch('setActionList[' + index + ']', function(newObject, oldObject) {
      if(typeof newObject === 'undefined' || newObject == oldObject) return;

      // 送信メッセージ
      if(typeof newObject.message !== 'undefined' && newObject.message !== '' && typeof newObject.selection === 'undefined') {
        document.getElementById('action' + index + '_message').innerHTML = createMessage(newObject.message);
      }
      // エラーメッセージ
      if(typeof newObject.errorMessage !== 'undefined' && newObject.errorMessage !== '') {
        document.getElementById('action' + index + '_error_message').innerHTML = createMessage(newObject.errorMessage);
      }
      // 確認メッセージ
      if(typeof newObject.confirmMessage !== 'undefined' && typeof newObject.success !== 'undefined' && typeof newObject.cancel !== 'undefined') {
        var confirmMessage = newObject.confirmMessage;
        var successMessage = newObject.success !== '' ? '[] ' + newObject.success : '';
        var cancelMessage = newObject.cancel !== '' ? '[] ' + newObject.cancel : '';

        var message = [confirmMessage, successMessage, cancelMessage].filter( function(string) {
          return string !== '';
        }).join('\n');
        if (message == '') return;
        document.getElementById('action' + index + '_confirm_message').innerHTML = createMessage(message);
      }
      // 選択肢
      if(typeof newObject.message !== 'undefied' && typeof newObject.selection !== 'undefined') {
        var messageList = [newObject.message];
        angular.forEach(newObject.selection.options, function(option) {
          if (option == '') return;
          messageList.push('[] ' + option);
        });

        var message = messageList.filter( function(string) {
          return typeof string !== 'undefined' && string !== '';
        }).join('\n');
        if (message == '') return;
        document.getElementById('action' + index + '_message').innerHTML = createMessage(message || '');
      }
    }, true);
  };

  $scope.watchActionItemList = function(itemList) {
      console.log('=== call fnc watchActionItemList ===');
      console.log(itemList);
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

  this.controllHearingSettingView = function(actionIndex) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionIndex + '_setting').find('.itemListGroup tr');
      var targetObjList = $scope.setActionList[actionIndex].hearings;
      self.controllListView(targetElmList, targetObjList)
    });
  };

  this.controllSelectOptionSetting = function(actionIndex) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionIndex + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionIndex].selection.options;
      self.controllListView(targetElmList, targetObjList);
    });
  };

  // TODO: 初期化方法によってはこの処理を消して、 controllMailSetting へ統一する
  this.initMailSetting = function(actionIndex) {
    $scope.setActionList[actionIndex].mailAddresses = [''];

    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionIndex + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionIndex].mailAddresses;
      self.controllListView(targetElmList, targetObjList, 5)
    });
  };

  this.controllMailSetting = function(actionIndex) {
    var targetElmList = $('#action' + actionIndex + '_setting').find('.itemListGroup li');
    $timeout(function(){
      $scope.$apply();
    }).then(function() {
      targetElmList = $('#action' + actionIndex + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionIndex].mailAddresses;
      self.controllListView(targetElmList, targetObjList, 5);
    });
  };

  // ヒアリング、選択肢、メール送信のリスト追加
  this.addActionItemList = function(actionIndex, listIndex) {
    var actionType = $scope.setActionList[actionIndex].actionType;

    if(actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
      var src = $scope.setActionList[actionIndex].default.hearings[0];
      var target = $scope.setActionList[actionIndex].hearings;
      target.push(angular.copy(src));
      this.controllHearingSettingView(actionIndex);

    } else if(actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
      var src = $scope.setActionList[actionIndex].default.selection.options[0];
      var target = $scope.setActionList[actionIndex].selection.options;
      target.push(angular.copy(src));
      this.controllSelectOptionSetting(actionIndex);

    } else if(actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
      var target = $scope.setActionList[actionIndex].mailAddresses;
      if(target.length < 5) {
        target.push(angular.copy(''));
        this.controllMailSetting(actionIndex);
      }
    }
  };

  // ヒアリング、選択肢、メール送信のリスト削除
  this.removeActionItemList = function(actionIndex, listIndex) {
    var actionType = $scope.setActionList[actionIndex].actionType;
    var targetObjList = "";
    var selector = "";
    var limitNum = 0;

    if(actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
      targetObjList = $scope.setActionList[actionIndex].hearings;
      selector = '#action' + actionIndex + '_setting .itemListGroup tr';
    } else if(actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
      targetObjList = $scope.setActionList[actionIndex].selection.options;
      selector = '#action' + actionIndex + '_setting .itemListGroup li';
    } else if(actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
      targetObjList = $scope.setActionList[actionIndex].mailAddresses;
      selector = '#action' + actionIndex + '_setting .itemListGroup li';
      limitNum = 5;
    }

    if(targetObjList !== "" && selector !== "") {
      targetObjList.splice(listIndex, 1);

      // 表示更新
      $timeout(function() {
        $scope.$apply();
      }).then(function() {
        self.controllListView($(selector), targetObjList, limitNum)
      });
    }
  };

  // ヒアリング、選択肢、メール送信のリスト表示の更新処理
  this.controllListView = function(targetElmList, targetObjList, limitNum) {
    if(typeof limitNum === 'undefined') {
      limitNum = 0;
    }
    var elmNum = targetElmList.length;
    var objNum = targetObjList.length;

    angular.forEach(targetElmList, function(targetElm, index) {
      if(index == elmNum-1) {
        $(targetElm).find('.btnBlock .disOffgreenBtn').show();
        if(index == 0) {
          $(targetElm).find('.btnBlock .deleteBtn').hide();
        } else if(index == limitNum-1) {
          $(targetElm).find('.btnBlock .disOffgreenBtn').hide();
        }
      } else {
        $(targetElm).find('.btnBlock .deleteBtn').show();
        $(targetElm).find('.btnBlock .disOffgreenBtn').hide();
      }
    });
  };

  // 選択肢が、プレビュー表示可能かを返す
  this.visibleSelectOptionSetting = function(param) {
    var visible = false;
    if(typeof param.selection !== 'undefined' && typeof param.selection.options !== 'undefined') {
      angular.forEach(param.selection.options, function(option) {
        visible = visible || option != '';
      });
    }
    return visible;
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
  };

  $scope.getSeBackgroundColor = function(){
    var defColor = "#FFFFFF";
    //仕様変更、常に高度な設定が当たっている状態とする
    defColor = $scope.widgetSettings.se_background_color;
//       if($scope.color_setting_type === '1'){
//         defColor = $scope.se_background_color;
//       }
    return defColor;
  };
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
  $(document).off('mouseenter','.questionBtn').on('mouseenter','.questionBtn', function(event){
    var parentClass = $(this).parent().parent().attr('class');
    var targetObj = $("#" + parentClass.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70) + 'px',
      left: $(this).offset().left - 69 + 'px'
    });
  });
  $(document).off('mouseleave','.questionBtn').on('mouseleave','.questionBtn', function(event){
    var parentClass = $(this).parent().parent().attr('class');
    var targetObj = $("#" + parentClass.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });

  // 設定側のスクロールに応じて、プレビュー側をスクロールさせる
  $(document).on('mouseenter', '.set_action_item', function() {
    var id = this.id;
    var selector = '#' + id.split('_')[0] + '_preview';
    var box = $('#tchatbotscenario_form_preview_body');
    var target = $(selector);
    var targetY = target.position().top - box.position().top;

    box.stop().animate({
      scrollTop: box.scrollTop() + targetY
    }, time);
    return false;
  });

  // プレビュー側のタイトルクリックに応じて、設定側をスクロールさせる
  var time = 500;
  $(document).on('click', '#tchatbotscenario_form_preview_body a[href^=#]', function() {
    var box = $('#tchatbotscenario_form_action_body');
    var target = $(this.hash);
    var targetY = target.position().top - box.position().top;

    box.stop().animate({
      scrollTop: box.scrollTop() + targetY - 19
    }, time);

    window.history.pushState(null, null, this.hash);
    return false;
  });
});

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
