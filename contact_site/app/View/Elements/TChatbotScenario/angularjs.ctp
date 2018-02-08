<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate']);

sincloApp.controller('MainController', ['$scope', '$timeout', 'SimulatorService', function($scope, $timeout, SimulatorService) {
  //thisを変数にいれておく
  var self = this;

  $scope.actionList = <?php echo json_encode($chatbotScenarioActionList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;

  // アクション設定の取得・初期化
  var setActivity = <?= !empty($this->data['TChatbotScenario']['activity']) ? json_encode($this->data['TChatbotScenario']['activity'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : "{}" ?>;
  var setActionListTmp = (typeof(setActivity) === "string") ? JSON.parse(setActivity) : [];
  $scope.setActionList = setActionListTmp;

  $scope.inputTypeList = <?php echo json_encode($chatbotScenarioInputType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.sendMailTypeList = <?php echo json_encode($chatbotScenarioSendMailType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.widget = SimulatorService;
  $scope.widget.settings = getWidgetSettings();

  // メッセージ間隔は同一の設定を各アクションに設定しているため、状態に応じて取得先を変更する
  $scope.messageIntervalTimeSec = "<?= !empty($this->data['TChatbotScenario']['messageIntervalTimeSec']) ? $this->data['TChatbotScenario']['messageIntervalTimeSec'] : '' ?>"
    || (typeof $scope.setActionList[0] !== 'undefined' ? $scope.setActionList[0].messageIntervalTimeSec : '')
    || $scope.actionList[1].default.messageIntervalTimeSec;

  // アクションの追加
  this.addItem = function(actionType) {
    if (actionType in $scope.actionList) {
      var item = $scope.actionList[actionType];
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
    if (typeof $scope.watchActionList[index] !== 'undefined') {
      $scope.watchActionList[index]();
    }

    $scope.watchActionList[index] = $scope.$watch('setActionList[' + index + ']', function(newObject, oldObject) {
      if (typeof newObject === 'undefined') return;

      // 各アクションのバリデーション
      if (newObject.actionType == <?= C_SCENARIO_ACTION_TEXT ?>) {
        newObject.$valid = !!newObject.message;
      } else
      if (newObject.actionType == <?= C_SCENARIO_ACTION_HEARING ?> ) {
        newObject.$valid = newObject.hearings.some(function(obj) {
          return !!obj.variableName && !!obj.message;
        });
        newObject.$valid = newObject.$valid && !!newObject.errorMessage;
        if (newObject.isConfirm) {
          newObject.$valid = newObject.$valid && !!newObject.confirmMessage && !!newObject.success && !!newObject.cancel;
        }
      } else
      if (newObject.actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
        newObject.$valid = newObject.selection.options.some(function(obj) {
          return !!obj;
        });
        newObject.$valid = newObject.$valid && !!newObject.selection.variableName;
        newObject.$valid = newObject.$valid && !!newObject.message;
      } else
      if (newObject.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
        newObject.$valid = newObject.toAddress.some(function(obj) {
          return !!obj;
        });
        newObject.$valid = newObject.$valid && !!newObject.subject;
        newObject.$valid = newObject.$valid && !!newObject.fromName;
        if (newObject.mailType == <?= C_SCENARIO_MAIL_TYPE_CUSTOMIZE ?>) {
          newObject.$valid = newObject.$valid && !!newObject.template;
        }
      }

      // 送信メッセージ
      if (typeof newObject.message !== 'undefined' && newObject.message !== '' && typeof newObject.selection === 'undefined') {
        document.getElementById('action' + index + '_message').innerHTML = $scope.widget.createMessage(newObject.message);
      }
      // エラーメッセージ
      if (typeof newObject.errorMessage !== 'undefined' && newObject.errorMessage !== '') {
        document.getElementById('action' + index + '_error_message').innerHTML = $scope.widget.createMessage(newObject.errorMessage);
      }
      // 確認メッセージ
      if (typeof newObject.confirmMessage !== 'undefined' && typeof newObject.success !== 'undefined' && typeof newObject.cancel !== 'undefined') {
        var confirmMessage = newObject.confirmMessage;
        var successMessage = newObject.success !== '' ? '[] ' + newObject.success : '';
        var cancelMessage = newObject.cancel !== '' ? '[] ' + newObject.cancel : '';

        var message = [confirmMessage, successMessage, cancelMessage].filter( function(string) {
          return string !== '';
        }).join('\n');
        if (message == '') return;
        document.getElementById('action' + index + '_confirm_message').innerHTML = $scope.widget.createMessage(message, 'preview' + index);
      }
      // 選択肢
      if (typeof newObject.message !== 'undefied' && typeof newObject.selection !== 'undefined') {
        var messageList = [newObject.message];
        angular.forEach(newObject.selection.options, function(option) {
          if (option == '') return;
          messageList.push('[] ' + option);
        });

        var message = messageList.filter( function(string) {
          return typeof string !== 'undefined' && string !== '';
        }).join('\n');
        if (message == '') return;
        document.getElementById('action' + index + '_message').innerHTML = $scope.widget.createMessage(message || '', 'preview' + index);
      }
    }, true);
  };

  // シミュレーターの起動
  this.openSimulator = function() {
    $scope.$broadcast('openSimulator', this.createJsonData());
  };

  this.saveAct = function() {
    $('#TChatbotScenarioActivity').val(this.createJsonData());
    submitAct();
  };

  // jsonデータ作る
  this.createJsonData = function() {
    var setActionList = [];

    // setActionList の内容をチェックする
    angular.forEach($scope.setActionList, function(originalAction, index) {
      var action = angular.copy(originalAction);
      action.messageIntervalTimeSec = $scope.messageIntervalTimeSec;
      delete action.label;
      delete action.default;
      delete action.$valid;

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
      }

      if (action !== null) {
        setActionList.push(action);
      };
    });
    return JSON.stringify(setActionList);
  };

  this.controllHearingSettingView = function(actionStep) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup tr');
      var targetObjList = $scope.setActionList[actionStep].hearings;
      self.controllListView(targetElmList, targetObjList)
    });
  };

  this.controllSelectOptionSetting = function(actionStep) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionStep].selection.options;
      self.controllListView(targetElmList, targetObjList);
    });
  };

  this.initMailSetting = function(actionStep) {
    $scope.setActionList[actionStep].toAddress = $scope.setActionList[actionStep].toAddress || [''];
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionStep].toAddress;
      self.controllListView(targetElmList, targetObjList, 5)
    });
  };

  this.controllMailSetting = function(actionStep) {
    var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup li');
    $timeout(function(){
      $scope.$apply();
    }).then(function() {
      targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionStep].toAddress;
      self.controllListView(targetElmList, targetObjList, 5);
    });
  };

  // ヒアリング、選択肢、メール送信のリスト追加
  this.addActionItemList = function(actionStep, listIndex) {
    var actionType = $scope.setActionList[actionStep].actionType;

    if (actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
      var src = $scope.actionList[actionType].default.hearings[0];
      var target = $scope.setActionList[actionStep].hearings;
      target.push(angular.copy(src));
      this.controllHearingSettingView(actionStep);

    } else if (actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
      var src = $scope.actionList[actionType].default.selection.options[0];
      var target = $scope.setActionList[actionStep].selection.options;
      target.push(angular.copy(src));
      this.controllSelectOptionSetting(actionStep);

    } else if (actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
      var target = $scope.setActionList[actionStep].toAddress;
      if (target.length < 5) {
        target.push(angular.copy(''));
        this.controllMailSetting(actionStep);
      }
    }
  };

  // ヒアリング、選択肢、メール送信のリスト削除
  this.removeActionItemList = function(actionStep, listIndex) {
    var actionType = $scope.setActionList[actionStep].actionType;
    var targetObjList = "";
    var selector = "";
    var limitNum = 0;

    if (actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
      targetObjList = $scope.setActionList[actionStep].hearings;
      selector = '#action' + actionStep + '_setting .itemListGroup tr';
    } else if (actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
      targetObjList = $scope.setActionList[actionStep].selection.options;
      selector = '#action' + actionStep + '_setting .itemListGroup li';
    } else if (actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
      targetObjList = $scope.setActionList[actionStep].toAddress;
      selector = '#action' + actionStep + '_setting .itemListGroup li';
      limitNum = 5;
    }

    if (targetObjList !== "" && selector !== "") {
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
    if (typeof limitNum === 'undefined') {
      limitNum = 0;
    }
    var elmNum = targetElmList.length;
    var objNum = targetObjList.length;

    angular.forEach(targetElmList, function(targetElm, index) {
      if (index == elmNum-1) {
        $(targetElm).find('.btnBlock .disOffgreenBtn').show();
        if (index == 0) {
          $(targetElm).find('.btnBlock .deleteBtn').hide();
        } else if (index == limitNum-1) {
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
    if (typeof param.selection !== 'undefined' && typeof param.selection.options !== 'undefined') {
      angular.forEach(param.selection.options, function(option) {
        visible = visible || option != '';
      });
    }
    return visible;
  };

  $(".sortable").sortable({
    axis: "y",
    tolerance: "pointer",
    containment: "parent",
    handle: '.handle',
    cursor: 'move',
    revert: 100,
    stop: function() {
      // 並び替えの後処理(番号の振り直し、プレビュー更新)
      var elms = Array.prototype.slice.call(document.querySelectorAll('#tchatbotscenario_form_action_body > li'), 0);
      $timeout(function() {
        $scope.$apply();
      }).then(function() {
        $scope.setActionList = elms.map(function(elm) {
          elm.style = '';
          var id = elm.id.replace(/action([0-9]+)_setting/, '$1');
          return $scope.setActionList[id];
        });
      });
    }
  });
}])
.controller('DialogController', ['$scope', '$timeout', 'SimulatorService', 'LocalStorageService', function($scope, $timeout, SimulatorService, LocalStorageService) {
  //thisを変数にいれておく
  var self = this;
  $scope.setActionList = [];
  $scope.inputTypeList = <?php echo json_encode($chatbotScenarioInputType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;

  $scope.widget = SimulatorService;
  $scope.widget.settings = getWidgetSettings();

  // シミュレーションの起動(ダイアログ表示)
  $scope.$on('openSimulator', function(event, setActionList) {
    $scope.setActionList = JSON.parse(setActionList);
    $('#tchatbotscenario_simulator_wrapper').show();
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      $('#simulator_popup').css({
        width: $('#sincloBox').outerWidth() + 28 + 'px',
        height: $('#sincloBox').outerHeight() + 101 + 'px'
      });
      $scope.actionInit();
    }, 0);
  });

  // シミュレーションで受け付けた受信メッセージ
  $scope.$on('receiveVistorMessage', function(event, message, prefix) {
    $scope.actionStep = (typeof prefix !== 'undefined' && prefix !== '') ? parseInt(prefix.replace(/action/, ''), 10) : $scope.actionStep;

    if (typeof $scope.setActionList[$scope.actionStep] === 'undefined') {
      // 対応するアクションがない場合は何もしない
      return;
    }

    if ($scope.setActionList[$scope.actionStep].actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
      var actionDetail = $scope.setActionList[$scope.actionStep];

      if ($scope.hearingIndex < actionDetail.hearings.length) {
        // 入力内容のチェック
        var inputType = actionDetail.hearings[$scope.hearingIndex].inputType
        var regex = new RegExp($scope.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, "$1"));
        if (regex.test(message)) {
          // 変数の格納
          LocalStorageService.setItem(actionDetail.hearings[$scope.hearingIndex].variableName, message);
          // 次のアクション
          $scope.hearingIndex++;
          if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
            !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
              $scope.hearingIndex = 0;
              $scope.actionStep++;
          }
        } else {
          // 入力エラー
          $scope.hearingInputResult = false;
        }
      } else
      if (actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length) && actionDetail.cancel === message) {
        // 最初から入力し直し
        $scope.hearingIndex = 0;
      } else {
        // 次のアクション
        $scope.hearingIndex++;
        if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
          !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
            $scope.hearingIndex = 0;
            $scope.actionStep++;
          }
      }
      $scope.doAction();
    } else
    if ($scope.setActionList[$scope.actionStep].actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
      // 選択肢
      var actionDetail = $scope.setActionList[$scope.actionStep];
      LocalStorageService.setItem(actionDetail.selection.variableName, message);
      $scope.actionStep++;
      $scope.doAction();
    }
  });

  // シミュレーションの終了(ダイアログ非表示)
  $scope.closeSimulator = function() {
    $scope.actionStop();
    $('#tchatbotscenario_simulator_wrapper').hide();
  };

  // アクションの開始
  $scope.actionInit = function() {
    $scope.actionStep = 0;
    $scope.hearingIndex = 0;
    $scope.actionTimer;
    $scope.hearingInputResult = true;

    // シミュレーション上のメッセージをクリアする
    $scope.$broadcast('removeMessage');
    $scope.doAction($scope.setActionList[$scope.actionStep]);
  }

  // アクションの停止
  $scope.actionStop = function() {
    $timeout.cancel($scope.simulatorTimer);
  }

  // アクションのクリア(アクションを最初から実行し直す)
  $scope.actionClear = function() {
    $scope.actionStop();
    $scope.actionInit();
  };

  $scope.doAction = function() {
    if (typeof $scope.setActionList[$scope.actionStep] !== 'undefined' && typeof $scope.setActionList[$scope.actionStep].actionType !== 'undefined') {
      var time = $scope.setActionList[$scope.actionStep].messageIntervalTimeSec;

      $timeout.cancel($scope.actionTimer);
      $scope.actionTimer = $timeout(function() {
        var actionDetail = $scope.setActionList[$scope.actionStep];

        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_TEXT ?>) {
          // テキスト発言
          $scope.$broadcast('addReMessage', $scope.replaceVariable(actionDetail.message), 'action' + $scope.actionStep);
          $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea);
          $scope.actionStep++;
          $scope.doAction();
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
          // ヒアリング
          $scope.doHearingAction(actionDetail);
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
          // 選択肢
          var messageList = [$scope.replaceVariable(actionDetail.message)];
          angular.forEach(actionDetail.selection.options, function(option) {
            messageList.push('[] ' + option);
          });
          $scope.$broadcast('addReMessage', messageList.join('\n'), 'action' + $scope.actionStep);
          $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea);
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
          // メール送信
          $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea);
          $scope.actionStep++;
          $scope.doAction();
        }
      }, parseInt(time, 10) * 1000);
    } else {
      $scope.actionStop();
    }
  }

  $scope.doHearingAction = function(actionDetail) {
    if (!$scope.hearingInputResult) {
      var message = $scope.replaceVariable(actionDetail.errorMessage);
      $scope.$broadcast('addReMessage', message, 'action' + $scope.actionStep);
      $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea);
      $scope.hearingInputResult = true;
      $scope.doAction();
    } else
    if ($scope.hearingIndex < actionDetail.hearings.length) {
      // 質問する
      var message = actionDetail.hearings[$scope.hearingIndex].message;
      $scope.$broadcast('addReMessage', message, 'action' + $scope.actionStep);
      $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea);
    } else
    if (actionDetail.isConfirm && ($scope.hearingIndex === actionDetail.hearings.length)) {
      var messageList = [$scope.replaceVariable(actionDetail.confirmMessage), '[] ' + actionDetail.success, '[] ' + actionDetail.cancel];
      var message = messageList.filter( function(string) {
        return string !== '';
      }).join('\n');

      $scope.$broadcast('addReMessage', message, 'action' + $scope.actionStep);
      $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea);
    } else {
      // 次のアクションへ移行する
      $scope.hearingIndex = 0;
      $scope.actionStep++;
      $scope.doAction();
    }
  };

  $scope.replaceVariable = function(message) {
    return message.replace(/{{(.+?)\}}/g, function(param) {
      var name = param.replace(/^{{(.+)}}$/, '$1');
      return LocalStorageService.getItem(name) || name;
    });
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

function removeAct(lastPage){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'シナリオ設定', 'moment');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id: document.getElementById('TChatbotScenarioId').value
      },
      cache: false,
      url: "<?= $this->Html->url('/TChatbotScenario/remoteDelete') ?>",
      success: function(){
        var url = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
        location.href = url + "/page:" + lastPage;
      }
    });
  };
}

function submitAct() {
  $('#TChatbotScenarioEntryForm').submit();
}

$(document).ready(function() {
  // ツールチップの表示制御
  $(document).off('mouseenter','.questionBtn').on('mouseenter','.questionBtn', function(event){
    var targetObj = $('.explainTooltip');
    targetObj.find('icon-annotation .detail').text($(this).data('tooltip'));
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70) + 'px',
      left: $(this).offset().left - 70 + 'px'
    });
  });
  $(document).off('mouseleave','.questionBtn').on('mouseleave','.questionBtn', function(event){
    $('.explainTooltip').find('icon-annotation').css('display','none');
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
      scrollTop: box.scrollTop() + targetY
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
  if (hearings.length < 1) return null;
  action.hearings = hearings;
  action.isConfirm = action.isConfirm ? '1' : '2';
  action.cv = action.cv ? '1' : '2';
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
  if (options.length < 1) return null;
  action.selection.options = options;
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
  if (toAddress.length < 1) return null;
  action.toAddress = toAddress;
  return action;
}
</script>
