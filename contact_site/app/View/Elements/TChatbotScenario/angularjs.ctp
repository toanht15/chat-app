<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate']);

sincloApp.factory('SharedSetActionListService', function() {
  return [];
})
.controller('MainController', ['$scope', '$timeout', 'SharedSetActionListService', 'SimulatorService', function($scope, $timeout, SharedSetActionListService, SimulatorService) {
  //thisを変数にいれておく
  var self = this;

  this.actionList = <?php echo json_encode($chatbotScenarioActionList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.setActionList = SharedSetActionListService;

  $scope.widget = SimulatorService;
  $scope.widget.settings = getWidgetSettings();

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
        document.getElementById('action' + index + '_message').innerHTML = $scope.widget.createMessage(newObject.message);
      }
      // エラーメッセージ
      if(typeof newObject.errorMessage !== 'undefined' && newObject.errorMessage !== '') {
        document.getElementById('action' + index + '_error_message').innerHTML = $scope.widget.createMessage(newObject.errorMessage);
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
        document.getElementById('action' + index + '_confirm_message').innerHTML = $scope.widget.createMessage(message);
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
        document.getElementById('action' + index + '_message').innerHTML = $scope.widget.createMessage(message || '');
      }
    }, true);
  };

  // シミュレーターの起動
  this.openSimulator = function() {
    var contentElm = $('#content');
    var targetElm = $('#tchatbotscenario_simulator_wrapper');
    targetElm.css({
      top:  '50px',
      left: ($(contentElm).outerWidth() - $(targetElm).outerWidth()) / 2 + 'px'
    });
    targetElm.show();
  };

  this.saveAct = function() {
    console.log('=== call func saveAct ===');
    // TODO: 保存ボタン押下時の処理
    submitAct();
  };

  // jsonデータ作る（保存時にも使えるように）
  this.createJsonData = function() {
    var setActionList = [];

    // setActionList の内容をチェックする
    // TODO:シミュレーション表示可能なものがない場合、エラーメッセージを出すなど
    angular.forEach($scope.setActionList, function(originalAction, index) {
      var action = angular.copy(originalAction);
      action.messageIntervalTimeSec = $scope.messageIntervalTimeSec;
      delete action.label;
      delete action.default;

      switch(parseInt(action.actionType, 10)) {
        case <?= C_SCENARIO_ACTION_TEXT ?>:
          action = adjustDataOftext(action);
          break;
        case <?= C_SCENARIO_ACTION_HEARING ?>:
          action = adjustDataOfHearing(action);
          break;
        case <?= C_SCENARIO_ACTION_SELECT_OPTION ?>:
          action = adjustDataOfSelectOption(action);
          break;
        case <?= C_SCENARIO_ACTION_SEND_MAIL ?>:
          action = adjustDataOfSendMail(action);
          break;
        default:
          break;
      }

      if(action !== null) {
        setActionList.push(action);
      };
    });

    if(setActionList.length < 1) {
      // TODO: エラー処理？
    }
    return setActionList;
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
    $scope.setActionList[actionIndex].toAddress = [''];

    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionIndex + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionIndex].toAddress;
      self.controllListView(targetElmList, targetObjList, 5)
    });
  };

  this.controllMailSetting = function(actionIndex) {
    var targetElmList = $('#action' + actionIndex + '_setting').find('.itemListGroup li');
    $timeout(function(){
      $scope.$apply();
    }).then(function() {
      targetElmList = $('#action' + actionIndex + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionIndex].toAddress;
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
      var target = $scope.setActionList[actionIndex].toAddress;
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
      targetObjList = $scope.setActionList[actionIndex].toAddress;
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
}])
.controller('SimulatorController', ['$scope', 'SharedSetActionListService', 'SimulatorService', function($scope, SharedSetActionListService, SimulatorService) {
  //thisを変数にいれておく
  var self = this;
  $scope.setActionList = SharedSetActionListService;

  $scope.widget = SimulatorService;
  $scope.widget.settings = getWidgetSettings();

  this.close = function() {
    $('#tchatbotscenario_simulator_wrapper').hide();
  }

  this.clear = function() {
    console.log("=== call func SimulatorController::clear ===");
  }
}]);

function getWidgetSettings() {
  var json = JSON.parse(document.getElementById('TChatbotScenarioWidgetSettings').value);
  var widgetSettings = [];
  for (var item in json) {
    widgetSettings[item] = json[item];
  }
  widgetSettings.show_name = <?=C_WIDGET_SHOW_COMP?>; // 表示名を企業名に固定する
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
      left: $(this).offset().left - 70 + 'px'
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

// テキスト発言のバリデーションチェック
function adjustDataOftext(action) {
  if (typeof action.message == 'undefined' || action.message == '') {
    return null;
  }
  return action;
}

// ヒアリングのバリデーションチェック
function adjustDataOfHearing(action) {
  if (typeof action.hearings === 'undefined' || typeof action.hearings.length < 1 ||
    typeof action.errorMessage === 'undefined' || action.errorMessage === '' ||
    (action.isConfirm && (typeof action.confirmMessage === 'undefined' || action.confirmMessage === '' || typeof action.success === 'undefined' || action.success === '' || typeof action.cancel === 'undefined' || action.cancel === ''))
  ) {
    return null;
  }

  var hearings = [];
  angular.forEach(action.hearings, function(item, index) {
    if (typeof item.variableName !== 'undefined' && item.variableName !== '' && typeof item.message !== 'undefined' && item.message !== '') {
      hearings.push(item);
    }
  });
  if(hearings.length < 1) return null;
  action.hearings = hearings;
  return action;
}

// 選択肢のバリデーションチェック
function adjustDataOfSelectOption(action) {
  if (typeof action.message === 'undefined' || action.message === '' ||
    typeof action.selection === 'undefined' || action.selection.options.length < 1 ||
    typeof action.selection.variableName === 'undefined' || action.selection.variableName === ''
  ) {
    return null;
  }

  var options = [];
  angular.forEach(action.selection.options, function(item, index) {
    if (item !== '') {
      options.push(item);
    }
  });
  if(options.length < 1) return null;
  action.options = options;
  return action;
}

// メール送信のバリデーションチェック
function adjustDataOfSendMail(action) {
  if (typeof action.subject === 'undefined' || action.subject === '' ||
    typeof action.fromName === 'undefined' || action.fromName === '' ||
    typeof action.toAddress === 'undefined' || action.toAddress < 1 ||
    (action.mailType == 3 && (typeof action.template === 'undefined' || action.template === ''))
  ) {
    return null;
  }

  var toAddress = [];
  angular.forEach(action.toAddress, function(item, index) {
    if (item !== '') {
      toAddress.push(item);
    }
  });
  if(toAddress.length < 1) return null;
  action.toAddress = toAddress;
  return action;
}
</script>
