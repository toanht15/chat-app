<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate', 'ui.sortable']);

sincloApp.controller('MainController', ['$scope', '$timeout', 'SimulatorService', 'LocalStorageService', function($scope, $timeout, SimulatorService, LocalStorageService) {
  //thisを変数にいれておく
  var self = this;

  $scope.actionList = <?php echo json_encode($chatbotScenarioActionList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.changeFlg = false;

  $scope.focusActionIndex = null;
  // current action
  $scope.previousAction = null;
  $scope.currentAction = null;

  // calendar japanese custom
  $scope.japaneseCalendar = {
      dateFormat: "Y/m/d",
      locale: {
          firstDayOfWeek: 0,
          weekdays: {
              shorthand: ["日", "月", "火", "水", "木", "金", "土"],
              longhand: ["日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日"],
          },
          months: {
              shorthand: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
              longhand: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
          },
      },
  };


  // アクション設定の取得・初期化
  $scope.actionListOrigin = [];
  $scope.setActionList = [];
  $scope.targetDeleteFileIds = [];
  var setActivity = <?= !empty($this->data['TChatbotScenario']['activity']) ? json_encode($this->data['TChatbotScenario']['activity'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : "{}"; ?>;
  if (typeof setActivity  === "string") {
    var jsonData = JSON.parse(setActivity);
    var setActionListTmp = jsonData.scenarios;
    for (var key in setActionListTmp) {
      if (setActionListTmp.hasOwnProperty(key)) {
        if(setActionListTmp[key].actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
          if(!setActionListTmp[key].requestHeaders || setActionListTmp[key].requestHeaders.length === 0) {
            setActionListTmp[key].requestHeaders = [
              {
                name: "",
                value: ""
              }
            ]
          }
          if(!setActionListTmp[key].responseBodyMaps || setActionListTmp[key].responseBodyMaps.length === 0) {
            setActionListTmp[key].responseBodyMaps = [
              {
                sourceKey: "",
                variableName: ""
              }
            ]
          }
        }
        $scope.setActionList.push(setActionListTmp[key]);
      }
    }

    // 削除対象のファイルID一覧の取得
    if (typeof jsonData.targetDeleteFileIds !== 'undefined' && jsonData.targetDeleteFileIds.length >= 1) {
      $scope.targetDeleteFileIds = jsonData.targetDeleteFileIds;
    }
  }

  // 登録済みシナリオ一覧
  var scenarioJsonList = JSON.parse(document.getElementById('TChatbotScenarioScenarioList').value);
  this.scenarioList = [];
  for (var key in scenarioJsonList) {
    if (scenarioJsonList.hasOwnProperty(key)) {
      this.scenarioList.push({'id': scenarioJsonList[key].TChatbotScenario.id, 'name': scenarioJsonList[key].TChatbotScenario.name});
    }
  }

  // 登録済みシナリオ一覧（条件分岐用）
  var scenarioJsonListForBranchOnCond = JSON.parse(document.getElementById('TChatbotScenarioScenarioListForBranchOnCond').value);
  this.scenarioListForBranchOnCond = [];
  for (var key in scenarioJsonListForBranchOnCond) {
    if (scenarioJsonListForBranchOnCond.hasOwnProperty(key)) {
      this.scenarioListForBranchOnCond.push({'id': scenarioJsonListForBranchOnCond[key].TChatbotScenario.id, 'name': scenarioJsonListForBranchOnCond[key].TChatbotScenario.name});
    }
  }

  $scope.inputTypeList = <?php echo json_encode($chatbotScenarioInputType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.inputAttributeList = <?php echo json_encode($chatbotScenarioAttributeType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.sendMailTypeList = <?php echo json_encode($chatbotScenarioSendMailType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.externalType = <?php echo json_encode($chatbotScenarioExternalType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.apiMethodType = <?php echo json_encode($chatbotScenarioApiMethodType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.apiResponseType = <?php echo json_encode($chatbotScenarioApiResponseType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.receiveFileTypeList = <?php echo json_encode($chatbotScenarioReceiveFileTypeList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.matchValueTypeList = <?php echo json_encode($chatbotScenarioBranchOnConditionMatchValueType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.processActionTypeList = <?php echo json_encode($chatbotScenarioBranchOnConditionActionType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.processElseActionTypeList = <?php echo json_encode($chatbotScenarioBranchOnConditionElseActionType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.widget = SimulatorService;
  $scope.widget.settings = getWidgetSettings();

  // 一時保存データのキー生成
  var scenarioId = document.getElementById('TChatbotScenarioId').value || 'tmp';
  $scope.storageKey = 'scenario_' + scenarioId;

  /**
   * angularのExpressionを文字列のまま表示する
   */
  $scope.showExpression = function(str) {
    return '{{' + str + '}}';
  }

  $scope.showCSSSelectorTooltip = function() {
    return 'ウィジェットを表示している画面上から取得する値をCSSのセレクタと同様の記入方法で設定します。<br><br>例１）以下のHTMLで「田中太郎」を取得したい場合<br>【設定値】<span style="color:#4edcdc">#user_name</span><br>【HTMLの例】<br><div style="color:#4edcdc">&lt;span id=&quot;user_name&quot;&gt;田中太郎&lt;/span&gt;</div><br>例２）以下のHTMLで「田中太郎」を取得したい場合<br>【設定値】<span style="color:#4edcdc">#nav-tools .nav-line-1</span><span style="color:#ff8000">　※ID属性とクラス名の間に要半角スペース</span><br>【HTMLの例】<br><div style="color:#4edcdc">&lt;div id=&quot;nav-tools&quot;&gt;<br> 　　(中略)<br>　&lt;span class=&quot;nav-line-1&quot;&gt;田中太郎&lt;/span&gt;<br>　&lt;span class=&quot;nav-line-2&quot;&gt;リスト&lt;span class=&quot;nav-icon&quot;&gt;&lt;/span&gt;&lt;/span&gt;<br> 　　(中略)<br>&lt;/div&gt;</div>';
  }

  // 設定一覧の並び替えオプション
  $scope.sortableOptions = {
    axis: "y",
    tolerance: "pointer",
    containment: "parent",
    handle: '.handle',
    cursor: 'move',
    helper: 'clone',
    revert: 100,
    beforeStop: function(event, ui) {
      // cloneした要素にチェック状態が奪われるため、再度設定し直す
      $.each($(ui.helper).find('input:radio:checked'), function() {
        var name = $(this).prop('name');
        var value = $(this).prop('value');
        $(ui.item).find('input:radio[name="' + name + '"][value="' + value + '"]').prop('checked', true);
      });
    },
    stop: function(event, ui) {
      $scope.$apply();

      // 並び替え後、変数のチェックを行う
      var elms = document.querySelectorAll('li.set_action_item');
      $scope.setActionList.forEach(function(actionItem, index) {
        actionValidationCheck(elms[index], $scope.setActionList, actionItem);
      });
    }
  };

  // 設定一覧の並び替えオプション
  $scope.sortableOptionsHearing = {
    axis: "y",
    tolerance: "pointer",
    containment: "parent",
    handle: '.handleOption',
    cursor: 'move',
    helper: 'clone',
    revert: 100,
    beforeStop: function (event, ui) {
      // cloneした要素にチェック状態が奪われるため、再度設定し直す
      $.each($(ui.helper).find('input:radio:checked'), function () {
        var name = $(this).prop('name');
        var value = $(this).prop('value');
        $(ui.item).find('input:radio[name="' + name + '"][value="' + value + '"]').prop('checked', true);
      });
    },
    stop: function(event, ui) {
      $scope.$apply();
    }
  };

  // メッセージ間隔は同一の設定を各アクションに設定しているため、状態に応じて取得先を変更する
  $scope.messageIntervalTimeSec = "<?= !empty($this->data['TChatbotScenario']['messageIntervalTimeSec']) ? $this->data['TChatbotScenario']['messageIntervalTimeSec'] : '' ?>"
    || (typeof $scope.setActionList[0] !== 'undefined' ? $scope.setActionList[0].messageIntervalTimeSec : '')
    || $scope.actionList[1].default.messageIntervalTimeSec;

  // アクションの追加
  this.addItem = function(actionType, isAppendAtLast = false) {
    if (actionType in $scope.actionList) {
      var item = $scope.actionList[actionType];
      item.actionType = actionType.toString();
      if (actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
        item.default.hearings[0] = this.setDefaultColorHearing(item.default.hearings[0]);
      }
      if (isAppendAtLast) {
        $scope.setActionList.push(angular.copy(angular.merge(item, item.default)));
      } else {
        $scope.setActionList.splice($scope.focusActionIndex + 1, 0, angular.copy(angular.merge(item, item.default)));
      }

      // 表示位置調整
      $timeout(function() {
        var previewBox = $('#tchatbotscenario_form_preview_body');
        var actionBox = $('#tchatbotscenario_form_action_body');

        var time = 500;
        if (isAppendAtLast) {
          actionBox.stop().animate({
            scrollTop: actionBox.scrollTop() + actionBox[0].scrollHeight
          }, time);
          previewBox.stop().animate({
            scrollTop: previewBox.scrollTop() + previewBox[0].scrollHeight
          }, time);
          // フォーカス移動
          $('#tchatbotscenario_form_action_body .set_action_item:last-of-type').focus();
        } else {
          var index = $scope.focusActionIndex + 1;
          var target = $('#action' + index + '_setting');
          var targetY = target.position().top - actionBox.position().top;
          actionBox.stop().animate({
            scrollTop: actionBox.scrollTop() + targetY
          }, time);
          // フォーカス移動
          $('#tchatbotscenario_form_action_body .set_action_item:eq(' + index + ')').focus();
        }
      }, 0);
    }
  };

  this.setDefaultColorHearing = function (target) {
    target.settings.customDesign.textColor = $scope.widget.settings.description_text_color;
    target.settings.customDesign.borderColor = $scope.widget.settings.main_color;
    target.settings.customDesign.headerBackgroundColor = $scope.widget.settings.main_color;
    target.settings.customDesign.calendarTextColor = $scope.widget.settings.description_text_color;
    target.settings.customDesign.sundayColor = $scope.widget.settings.description_text_color;
    target.settings.customDesign.saturdayColor = $scope.widget.settings.description_text_color;
    target.settings.customDesign.headerWeekdayBackgroundColor = this.getRawColor($scope.widget.settings.main_color);

    return target;
  };

  this.setFocusActionIndex = function (actionIndex) {
    $scope.focusActionIndex = actionIndex;
    // $('.set_action_item').blur();
    // $('#action' + actionIndex + '_setting').css('border', '2px solid #C3D69B');
  };

  this.showOptionMenu = function (actionType) {
    // if not focus or focusing at last item -> append after last item
    if ($scope.focusActionIndex === null || ($scope.focusActionIndex + 1) === $scope.setActionList.length) {
        this.addItem(actionType, true);
        return;
    }
    // show option menu
    $('#action' + $scope.focusActionIndex + '_setting').focus();
    $scope.previousAction = $scope.currentAction;
    $scope.currentAction = actionType;
    if ($scope.previousAction !== $scope.currentAction) {
      $("#actionMenu" + $scope.previousAction).fadeOut('fast');
    }
    if ($("#actionMenu" + actionType).is(":visible")) {
      $("#actionMenu" + actionType).fadeOut('fast');
    } else {
      $("#actionMenu" + actionType).fadeIn('fast');
    }
  };

  // アクションの削除
  this.removeItem = function(setActionId) {
    var actionDetail = angular.copy($scope.setActionList[setActionId]);
    if (typeof actionDetail.tChatbotScenarioSendFileId !== 'undefined' && actionDetail.tChatbotScenarioSendFileId !== null ) {
      $scope.targetDeleteFileIds.push(actionDetail.tChatbotScenarioSendFileId);
      LocalStorageService.setItem($scope.storageKey, [{key: 'targetDeleteFileIds', value: $scope.targetDeleteFileIds}]);
    }
    $scope.setActionList.splice(setActionId, 1);
    $scope.focusActionIndex = null;

    // 変更のあるアクション内に変数名を含む場合、アクションの変数チェックを行う
    $timeout(function() {
      var variables = searchObj(actionDetail, /^variableName$/);
      if (variables.length >= 1) {
        var elms = document.querySelectorAll('li.set_action_item');
        for (var listIndex = setActionId; listIndex < elms.length; listIndex++) {
          actionValidationCheck(elms[listIndex], $scope.setActionList, $scope.setActionList[listIndex]);
        }
      }
    }, 0);
  };

  this.revertCalendarColor = function (actionIndex, hearingIndex, customDesignIndex) {
    switch (customDesignIndex) {
      case 'headerBackgroundColor':
        var defaultColor = $scope.widget.settings.main_color;
        break;
      case 'headerTextColor':
        var defaultColor = '#FFFFFF';
        break;
      case 'headerWeekdayBackgroundColor':
        var defaultColor = this.getRawColor($scope.widget.settings.main_color);
        break;
      case 'borderColor':
        var defaultColor = $scope.widget.settings.main_color;
        break;
      case 'calendarBackgroundColor':
        var defaultColor = '#FFFFFF';
        break;
      case 'calendarTextColor':
        var defaultColor = $scope.widget.settings.message_box_text_color;
        break;
      case 'saturdayColor':
        var defaultColor = $scope.widget.settings.message_box_text_color;
        break;
      case 'sundayColor':
        var defaultColor = $scope.widget.settings.message_box_text_color;
        break;
    }

    $scope.setActionList[actionIndex].hearings[hearingIndex].settings.customDesign[customDesignIndex] = defaultColor;
    $('#action' + actionIndex + '_option' + hearingIndex + '_' + customDesignIndex).css('background-color', defaultColor);
    jscolor.installByClassName("jscolor");
  };

  this.revertPulldownColor = function (actionIndex, hearingIndex, customDesignIndex) {
    switch (customDesignIndex) {
      case 'backgroundColor':
        var defaultColor = '#FFFFFF';
        break;
      case 'textColor':
        var defaultColor = $scope.widget.settings.message_box_text_color;
        break;
      case 'borderColor':
        var defaultColor = $scope.widget.settings.main_color;
        break;
    }
    $scope.setActionList[actionIndex].hearings[hearingIndex].settings.customDesign[customDesignIndex] = defaultColor;
    $('#action' + actionIndex + '_pulldown' + hearingIndex + '_' + customDesignIndex).css('background-color', defaultColor);
    jscolor.installByClassName("jscolor");
  };

  // アクションの追加・削除を検知する
  $scope.watchActionList = [];
  $scope.$watchCollection('setActionList', function(newObject, oldObject) {

    // 編集されたことを検知する
    if (!$scope.changeFlg && newObject !== oldObject) {
      $scope.changeFlg = true;
    }

    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      angular.forEach($scope.setActionList, $scope.watchSetActionList);
    });
  });

  // 各アクション内の変更を検知し、プレビューのメッセージを表示更新する
  $scope.watchSetActionList = function(action, index) {
    // watchの破棄
    if (typeof $scope.watchActionList[index] !== 'undefined') {
      $scope.watchActionList[index]();
    }

    $scope.watchActionList[index] = $scope.$watch('setActionList[' + index + ']', function(newObject, oldObject) {
      if (typeof newObject === 'undefined') return;
      // 編集されたことを検知する
      if (!$scope.changeFlg && newObject !== oldObject) {
        $scope.changeFlg = true;
      }

      // 変更のあるアクション内に変数名を含む場合、アクションの変数チェックを行う
      var variables = searchObj(newObject, /^variableName$/);
      if (variables.length >= 1) {
        var elms = document.querySelectorAll('li.set_action_item');
        for (var listIndex = index + 1; listIndex < elms.length; listIndex++) {
          actionValidationCheck(elms[listIndex], $scope.setActionList, $scope.setActionList[listIndex]);
        }
      }

      // プレビューに要素がない場合、以降の処理は実行しない
      if (document.getElementById('action' + index + '_preview') === null) {
        return;
      }

      // 送信メッセージ
      if (typeof newObject.message !== 'undefined' && newObject.message !== '' && typeof newObject.selection === 'undefined') {
        document.getElementById('action' + index + '_message').innerHTML = $scope.widget.createMessage(newObject.message);
      }
      // エラーメッセージ
      if (typeof newObject.errorMessage !== 'undefined' && newObject.errorMessage !== '') {
        document.getElementById('action' + index + '_error_message').innerHTML = $scope.widget.createMessage(newObject.errorMessage);
      }

        // hearings
      if (typeof newObject.message !== 'undefied' && typeof newObject.hearings !== 'undefined') {
        angular.forEach(newObject.hearings, function (hearing, hearingIndex) {
          // pulldown customize
          if (hearing.uiType === '4') {
            $timeout(function () {
              $scope.$apply();
            }).then(function () {
              if (hearing.settings.pulldownCustomDesign) {
                jscolor.installByClassName('jscolor');
              }
              var selectionTarget = $('#action' + index + '_selection' + hearingIndex);
              selectionTarget.css('border-color', hearing.settings.customDesign.borderColor);
              selectionTarget.css('background-color', hearing.settings.customDesign.backgroundColor);
              selectionTarget.css('color', hearing.settings.customDesign.textColor);
              $('#action' + index + '_selection' + hearingIndex + '_option').css('color', hearing.settings.customDesign.textColor);
            });
          }
          // calendar
          if (hearing.uiType === '5') {
            var calendar_options = {
              dateFormat: "Y/m/d",
              minDate: 'today',
              inline: 'true',
              disable: [],
              enable: [],
              locale: {
                firstDayOfWeek: 0
              },
            };
            // set language for calendar
            if (hearing.settings.language == 1) {
              // japanese
              calendar_options.locale = $scope.japaneseCalendar.locale;
            } else {
              // english
              calendar_options.locale = {
                firstDayOfWeek: 0
              }
            }

            // set min date
            if (hearing.settings.isEnableAfterDate) {
              calendar_options.minDate = new Date().fp_incr(hearing.settings.enableAfterDate);
            } else {
              calendar_options.minDate = hearing.settings.disablePastDate ? 'today' : '';
            }
            // set disable date
            if (hearing.settings.isDisableDayOfWeek) {
              var disableWeekDays = [];
              angular.forEach(hearing.settings.dayOfWeekSetting, function (item, key) {
                if (item) {
                  disableWeekDays.push(key);
                }
              });

              calendar_options.disable = [
                function (date) {
                  return disableWeekDays.indexOf(date.getDay()) !== -1;
                },
              ];
            } else {
              calendar_options.disable = [];
            }

            if (hearing.settings.isSetSpecificDate) {
              if (hearing.settings.setSpecificDateType == 1) {
                var disableLength = calendar_options.disable.length;
                angular.forEach(hearing.settings.specificDateData, function (item, key) {
                  calendar_options.disable[key + disableLength] = item;
                });
              }

              if (hearing.settings.setSpecificDateType == 2) {
                angular.forEach(hearing.settings.specificDateData, function (item, key) {
                  calendar_options.enable[key] = item;
                });
              }
            } else {
              hearing.settings.specificDateData = [""];
            }

            $timeout(function () {
              $scope.$apply();
            }).then(function () {
              // add first picker for first input
              if (hearing.settings.setSpecificDateType) {
                var datepickerTarget = $('#action' + index + '_option' + hearingIndex + '_datepicker0');
                if (!datepickerTarget.hasClass('flatpickr-input')) {
                  datepickerTarget.flatpickr($scope.japaneseCalendar);
                }

                var targetElmList = $('.action' + index + '_option' + hearingIndex);
                self.controllListView(<?= C_SCENARIO_ACTION_HEARING ?>, targetElmList, targetElmList);
              }

              if (hearing.settings.isCustomDesign) {
                jscolor.installByClassName("jscolor");
              }

              var calendarTarget = $('#action' + index + '_calendar' + hearingIndex);
              // add datepicker for preview
              var datepickerId = 'action' + index + '_datepicker' + hearingIndex;
              calendarTarget.html('<input id="' + datepickerId + '" type="hidden">');
              $('#' + datepickerId).flatpickr(calendar_options);
              $('#' + datepickerId).hide();
              var firstDayOfWeek = calendarTarget.find('.flatpickr-weekday');
              firstDayOfWeek[0].innerText = hearing.settings.language == 1 ? '日' : 'Sun';

              hearing.selectedTextColor = self.getContrastColor(hearing.settings.customDesign.headerBackgroundColor);
              hearing.weekdayTextColor = self.getContrastColor(hearing.settings.customDesign.headerWeekdayBackgroundColor);

              // binding color to preview
              // calendar text color
              var calendarTextColorTarget = calendarTarget.find('.flatpickr-calendar .flatpickr-day');
              calendarTextColorTarget.each(function () {
                if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
                  $(this).css('color', hearing.settings.customDesign.calendarTextColor);
                }
              });
              // sunday color
              // calendarTarget.find('.flatpickr-weekdaycontainer span:nth-child(7n+1)').css('color', hearing.settings.customDesign.sundayColor);
              var sundayTarget = calendarTarget.find('.dayContainer span:nth-child(7n+1)');
              sundayTarget.each(function () {
                if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
                  $(this).css('color', hearing.settings.customDesign.sundayColor);
                }
              });
              // saturday color
              // calendarTarget.find('.flatpickr-weekdaycontainer span:nth-child(7n+7)').css('color', hearing.settings.customDesign.saturdayColor);
              var saturdayTarget = calendarTarget.find('.dayContainer span:nth-child(7n+7)');
              saturdayTarget.each(function () {
                if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
                  $(this).css('color', hearing.settings.customDesign.saturdayColor);
                }
              });

              // change color when change month
              calendarTarget.find('.flatpickr-calendar .flatpickr-months').on('mousedown', function () {
                self.customCalendarTextColor(calendarTarget, hearing.settings.customDesign);
              });
              // keep color when click on date
              $('#action' + index + '_datepicker' + hearingIndex).on('change', function () {
                self.customCalendarTextColor(calendarTarget, hearing.settings.customDesign);
              });

            });
          }
        });
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
      // ファイル受信のファイル形式
      if (oldObject.receiveFileType === "1" && typeof newObject.receiveFileType !== 'undefined' && newObject.receiveFileType === "2") {
        $scope.showExtendedConfigurationWarningPopup(newObject);
      }
      // 条件分岐のアクション「テキスト発言」
      // 送信メッセージ
      if ( newObject.actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?> && newObject.conditionList.length > 0) {
        angular.forEach(newObject.conditionList, function(condition, conditionIndex){
          if(condition.actionType == "1" && document.getElementById('action' + index + "-" + conditionIndex + '_message')) {
            document.getElementById('action' + index + "-" + conditionIndex + '_message').innerHTML = $scope.widget.createMessage(condition.action.message);
          } else if(condition.actionType != "1" && condition.action.message) {
            condition.action.message = "";
          } else if(condition.actionType != "2" && condition.action.callScenarioId) {
            delete condition.action.callScenarioId;
          }
        });
        // 上記を満たさなかった場合：送信メッセージ
        if (newObject.elseEnabled && newObject.elseAction.actionType == '1' && newObject.elseAction.action.message) {
          document.getElementById('action' + index + '_else-message').innerHTML = $scope.widget.createMessage(newObject.elseAction.action.message);
        }
      }
    }, true);
  };

  this.customCalendarTextColor = function (calendarTarget, design) {
    var calendarTextColorTarget = calendarTarget.find('.flatpickr-calendar .flatpickr-day');
    calendarTextColorTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.calendarTextColor);
      }
    });

    var sundayTarget = calendarTarget.find('.dayContainer span:nth-child(7n + 1)');
    sundayTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.sundayColor);
      }
    });

    var saturdayTarget = calendarTarget.find('.dayContainer span:nth-child(7n+7)');
    saturdayTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.saturdayColor);
      }
    });

    calendarTarget.find('.flatpickr-calendar .dayContainer').css('background-color', design.calendarBackgroundColor);
  };

  this.hexToRgb = function(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
      r: parseInt(result[1], 16),
      g: parseInt(result[2], 16),
      b: parseInt(result[3], 16)
    } : null;
  };

  this.getContrastColor = function (hex) {
    var rgb = this.hexToRgb(hex);
    var brightness;
    brightness = (rgb.r * 299) + (rgb.g * 587) + (rgb.b * 114);
    brightness = brightness / 255000;
    // values range from 0 to 1
    // anything greater than 0.5 should be bright enough for dark text
    return brightness >= 0.5 ? 'black' : 'white';
  };

  // remove opacity from hex color
  this.getRawColor = function (hex) {
    var code = hex.substr(1), r,g,b;
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

    return ('#' + codeR + codeG + codeB).toUpperCase();
  };

  this.changeCalendarHeaderColor = function (actionIndex, hearingIndex, index) {
    if (index === 'headerBackgroundColor') {
      var color = this.getRawColor($scope.setActionList[actionIndex].hearings[hearingIndex].settings.customDesign[index]);
      $scope.setActionList[actionIndex].hearings[hearingIndex].settings.customDesign["headerWeekdayBackgroundColor"] = color;
      $('#action' + actionIndex + '_option' + hearingIndex + '_headerWeekdayBackgroundColor').css('background-color', color);
    }
  };

  $scope.autoResizeTextArea = function () {
    var maxRow = 4;                       // 表示可能な最大行数
    var fontSize = 13;           // 行数計算のため、templateにて設定したフォントサイズを取得
    var borderSize = 2; // 行数計算のため、templateにて設定したボーダーサイズを取得(上下/左右)
    var paddingSize = 5;     // 表示高さの計算のため、templateにて設定したテキストエリア内の余白を取得(上下/左右)
    var lineHeight = 1.5;       // 表示高さの計算のため、templateにて設定した行の高さを取得
    var elm = $('#bulk_textarea');

    // テキストエリアの要素のサイズから、borderとpaddingを引いて文字入力可能なサイズを取得する
    var areaWidth = elm[0].getBoundingClientRect().width - borderSize - paddingSize;

    // フォントサイズとテキストエリアのサイズを基に、行数を計算する
    var textRow = 1;
    elm[0].value.split('\n').forEach(function (string) {
      var stringWidth = string.length * fontSize;
      textRow += Math.max(Math.ceil(stringWidth / areaWidth), 1);
    });

    // 表示する行数に応じて、テキストエリアの高さを調整する
    if (textRow > maxRow) {
      elm.height((maxRow * (fontSize * lineHeight)) + paddingSize);
      elm.css('overflow', 'auto');
    }
    else {
      elm.height(textRow * (fontSize * lineHeight));
      elm.css('overflow', 'hidden');
    }
  };

  this.showBulkSelectionPopup = function (actionIndex, hearingIndex, uiType) {
    if (uiType === '3' || uiType === '4') {
      // ラジオボタン、プルダウン
      var options = $scope.setActionList[actionIndex].hearings[hearingIndex].settings.options;
      var title = '選択肢を一括登録する';
      var description = '選択肢として登録する内容を改行して設定してください。 ';
      var placeholder = '男性&#10;女性'
    }

    if (uiType === '5') {
      //　カレンダー
      var setSpecificDateType = $scope.setActionList[actionIndex].hearings[hearingIndex].settings.setSpecificDateType;
      var options = $scope.setActionList[actionIndex].hearings[hearingIndex].settings.specificDateData;
      var title = '日付を一括登録する';
      var placeholder = '2019/01/01&#10;2019/01/02';
      if (setSpecificDateType == 1) {
        var description = '選択できなくする日付を改行して設定してください。（yyyy/mm/dd形式）。 ';
      } else {
        var description = '選択できなくする日付を改行して設定してください。（yyyy/mm/dd形式）。';
      }
    }

    var convertedOptions = "";
    angular.forEach(options, function (option, optionKey) {
      convertedOptions = convertedOptions + option + '\n';
    });

    var html = '<div class="select-option-one-time-popup">\n' +
      '    <p style="margin-top: -10px; width: 350px;">' + description + '</p>\n' +
      '\n' +
      '    <textarea name=""  id="bulk_textarea" style="overflow: hidden; resize: none; font-size: 13px;" cols="48" rows="3" placeholder="' + placeholder + '">' + convertedOptions + '</textarea>\n' +
      '</div>';

    modalOpen.call(window, html, 'p-hearing-settings', title, 'moment');
    $('#bulk_textarea').bind('input', function () {
      $scope.autoResizeTextArea();
      popupEvent.resize();
    });
    $(window).on('resize', function () {
      $scope.autoResizeTextArea();
      popupEvent.resize();
    });


    popupEvent.convert = function () {
      var inputOptions = $('#bulk_textarea').val();
      var convertedInputOptions = inputOptions.split('\n');

      if (uiType === '3' || uiType === '4') {
        $scope.setActionList[actionIndex].hearings[hearingIndex].settings.options = [];
        angular.forEach(convertedInputOptions, function (option, optionKey) {
          if (option) {
            $scope.setActionList[actionIndex].hearings[hearingIndex].settings.options.push(option);
          }
        });
      }

      if (uiType === '5') {
        $scope.setActionList[actionIndex].hearings[hearingIndex].settings.specificDateData = [];
        angular.forEach(convertedInputOptions, function (option, optionKey) {
          if (option) {
            $scope.setActionList[actionIndex].hearings[hearingIndex].settings.specificDateData.push(option);
          }
        });
      }

      popupEvent.close();
      $timeout(function () {
        $scope.$apply();
        var targetElmList = $('.action' + actionIndex + '_option' + hearingIndex);
        self.controllListView($scope.setActionList[actionIndex].actionType, targetElmList, targetElmList);
        if (uiType === '5') {
          // add datepicker for new input
          angular.forEach(targetElmList, function (targetElm, index) {
            var el = $(targetElm).find('input');
            if (!el.hasClass('flatpickr-input')) {
              el.flatpickr($scope.japaneseCalendar);
            }
          });
        }
      });
    }

  };

  $scope.showExtendedConfigurationWarningPopup = function(obj) {
    modalOpen.call(window, "１．受信したファイルによるウィルス感染などのリスクはお客様の責任にて十分ご理解の上ご利用ください。<br>２．業務に必要なファイル形式のみを指定するようにしてください。<br>３．特に圧縮ファイルを許可する場合は、解凍時に意図しないファイルが含まれる恐れがありますので<br>　　十分に注意の上ご利用ください。", 'p-chatbot-use-extended-setting', '必ず確認してください');
    popupEvent.agree = function() {
      popupEvent.close();
    }
    popupEvent.closeNoPopup = function () {
      obj.receiveFileType = "1";
      popupEvent.close();
      $timeout(function(){
        $scope.$apply();
      });
    }
  };

  /**
   * ファイルアップロード
   * @param String  actionStep  アクション番号
   * @param File    fileObj     Fileオブジェクト
   * @param Blob    loadFile    Blobオブジェクト
   */
  $scope.uploadFile = function(actionStep, fileObj, loadFile) {
    var fd = new FormData();
    var blob = new Blob([loadFile], {type: fileObj.type});
    fd.append("file", blob, fileObj.name);

    var targetElm = document.querySelector('#action' + actionStep + '_setting .selectFileArea');
    var actionDetail = $scope.setActionList[actionStep];
    targetElm.querySelector('li:first-child').style.display = 'none';
    targetElm.querySelector('.uploadProgress').classList.remove('hide');

    $.ajax({
      url  : "<?= $this->Html->url('/TChatbotScenario/remoteUploadFile') ?>",
      type : "POST",
      data : fd,
      cache       : false,
      contentType : false,
      processData : false,
      dataType    : "json",
      xhr: function() {
        var XHR = $.ajaxSettings.xhr();
        if(XHR.upload){
          XHR.upload.addEventListener('progress',function(e){
            var progress = parseInt(e.loaded/e.total*10000)/100;
            targetElm.querySelector('.uploadProgressRate').style.width = progress + '%';
            $scope.$apply();
          }, false);
        }
        return XHR;
      }
    })
    .done(function(data, textStatus, jqXHR){
      // 更新前のファイルIDを、削除リストに追加する
      if (typeof actionDetail.tChatbotScenarioSendFileId !== 'undefined' && actionDetail.tChatbotScenarioSendFileId !== null) {
        $scope.targetDeleteFileIds.push(actionDetail.tChatbotScenarioSendFileId);
      }
      // アップロードしたファイル情報で更新する
      actionDetail = angular.merge(actionDetail, data.save_data);

      // アップロードしたファイルも削除候補として追加し、localStorageを更新する(一時保存しなかった場合に削除されるようにするため)
      $scope.targetDeleteFileIds.push(actionDetail.tChatbotScenarioSendFileId);
      LocalStorageService.setItem($scope.storageKey, [{key: 'targetDeleteFileIds', value: $scope.targetDeleteFileIds}]);
    })
    .fail(function(jqXHR, textStatus, errorThrown){
      alert("fail");
    })
    .always(function() {
      targetElm.querySelector('li:first-child').style.display = '';
      targetElm.querySelector('.uploadProgress').classList.add('hide');
      $scope.$apply();
    });
  };

  // シミュレーターの起動
  this.openSimulator = function() {
    $scope.actionListOrigin = $scope.setActionList;
    $scope.$broadcast('openSimulator', this.createJsonData(true));
    // シミュレータ起動時、強制的に自由入力エリアを有効の状態で表示する
    $scope.$broadcast('switchSimulatorChatTextArea', true);
  };

  // シナリオ設定の一時保存
  this.saveTemporary = function() {
    $scope.changeFlg = false;
    LocalStorageService.setData($scope.storageKey, this.createJsonData(false));
  };

  // シナリオ設定の保存
  this.saveAct = function(e) {
    if($('#submitBtn').hasClass('disabled')) return false;
    // localStorageから一時保存データを削除する
    LocalStorageService.remove($scope.storageKey);

    $scope.changeFlg = false;
    $('#TChatbotScenarioActivity').val(this.createJsonData(true));
    submitAct();
  };

  // シナリオ設定の削除
  this.removeAct = function(lastPage){
    // アラート表示を行わないように、フラグを戻す
    $scope.changeFlg = false;

    modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'シナリオ設定', 'moment');
    popupEvent.closePopup = function(){

      // ファイルIDの削除リストを取得
      $scope.setActionList.forEach(function(action) {
        if (action.actionType == <?= C_SCENARIO_ACTION_SEND_FILE ?>) {
          $scope.targetDeleteFileIds.push(action.tChatbotScenarioSendFileId);
        }
      });

      $.ajax({
        type: 'post',
        data: {
          id: document.getElementById('TChatbotScenarioId').value,
          targetDeleteFileIds: JSON.stringify($scope.targetDeleteFileIds)
        },
        cache: false,
        url: "<?= $this->Html->url('/TChatbotScenario/remoteDelete') ?>",
        success: function(){
          LocalStorageService.remove($scope.storageKey);

          // 一覧ページへ遷移する
          var url = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
          location.href = url + "/page:" + lastPage;
        }
      });
    };
  };

  /**
   * ヒアリング、選択肢、メール送信のリスト追加
   * （選択肢・メール送信ではリストの末尾に、ヒアリングではリストの任意の箇所に追加する）
   * @param String  actionStep  アクション番号
   * @param Integer listIndex   ボタン押下されたリスト番号
   */
  this.addActionItemList = function($event, listIndex) {
    var targetActionId = $($event.target).parents('.set_action_item')[0].id;
    var targetClassName = $($event.target).parents('.itemListGroup')[0].className;
    var actionStep = targetActionId.replace(/action([0-9]+)_setting/, '$1');
    var actionType = $scope.setActionList[actionStep].actionType;

    if (actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
      var src = $scope.actionList[actionType].default.hearings[0];
      src = this.setDefaultColorHearing(src);
      var target = $scope.setActionList[actionStep].hearings;
      src.inputType = src.inputType.toString();
      src.uiType = src.uiType.toString();
      target.splice(listIndex+1, 0, angular.copy(src));
      this.controllHearingSettingView(actionStep);

    } else if (actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
      var src = $scope.actionList[actionType].default.selection.options[0];
      var target = $scope.setActionList[actionStep].selection.options;
      target.splice(listIndex+1, 0, angular.copy(src));
      this.controllSelectOptionSetting(actionStep);

    } else if (actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
      var target = $scope.setActionList[actionStep].toAddress;
      if (target.length < 5) {
        target.push(angular.copy(''));
        this.controllMailSetting(actionStep);
      }

    } else if (actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?>) {
      var src = $scope.actionList[actionType].default.getAttributes[0];
      var target = $scope.setActionList[actionStep].getAttributes;
      src.type = src.type.toString();
      target.splice(listIndex+1, 0, angular.copy(src));
      this.controllAttributeSettingView(actionStep);

    } else if (actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
      if (/externalApiRequestHeader/.test(targetClassName)) {
        var src = $scope.actionList[actionType].default.requestHeaders[0];
        var target = $scope.setActionList[actionStep].requestHeaders;
      } else {
        var src = $scope.actionList[actionType].default.responseBodyMaps[0];
        var target = $scope.setActionList[actionStep].responseBodyMaps;
      }
      target.push(angular.copy(src));
      this.controllExternalApiSetting(actionStep);
    } else if (actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>) {
      var src = $scope.actionList[actionType].default.conditionList[0];
      var target = $scope.setActionList[actionStep].conditionList;
      target.splice(listIndex+1, 0, angular.copy(src));
      this.controllBranchOnConditionSettingView(actionStep);
    } else if (actionType == <?= C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION ?>) {
      var src = $scope.actionList[actionType].default.addCustomerInformations[0];
      var target = $scope.setActionList[actionStep].addCustomerInformations;
      target.splice(listIndex+1, 0, angular.copy(src));
      this.controllAddCustomerInformationView(actionStep);

    } else if (actionType == <?= C_SCENARIO_ACTION_BULK_HEARING ?>) {
      var src = $scope.actionList[actionType].default.multipleHearings[0];
      var target = $scope.setActionList[actionStep].multipleHearings;
      target.splice(listIndex+1, 0, angular.copy(src));
      this.controllBulkHearings(actionStep);
    }
  };

  this.addHearingOption = function ($event, optionType, optionIndex, listIndex) {
    var targetActionId = $($event.target).parents('.set_action_item')[0].id;
    var actionStep = targetActionId.replace(/action([0-9]+)_setting/, '$1');
    var actionType = $scope.setActionList[actionStep].actionType;
    if (optionType === '3' || optionType === '4') {
      // ラジオボタン、プルダウン
      var src = $scope.actionList[actionType].default.hearings[0].settings.options;
      var target = $scope.setActionList[actionStep].hearings[listIndex].settings.options;
    } else {
      // カレンダー
      var src = $scope.actionList[actionType].default.hearings[0].settings.specificDateData;
      var target = $scope.setActionList[actionStep].hearings[listIndex].settings.specificDateData;
    }

    target.splice(optionIndex + 1, 0, angular.copy(src));
    // 表示更新
    $timeout(function () {
      $scope.$apply();
    }).then(function () {
      var targetElmList = $('.action' + actionStep + '_option' + listIndex);
      self.controllListView(actionType, targetElmList, target);
      if (optionType === '5') {
        // add datepicker for new input
        angular.forEach(targetElmList, function (targetElm, index) {
          var el = $(targetElm).find('input');
          if (!el.hasClass('flatpickr-input')) {
            el.flatpickr($scope.japaneseCalendar);
          }
        });
      }
    });
  };

  this.removeHearingOption = function ($event, optionType, optionIndex, listIndex) {
    var targetActionId = $($event.target).parents('.set_action_item')[0].id;
    var actionStep = targetActionId.replace(/action([0-9]+)_setting/, '$1');
    var actionType = $scope.setActionList[actionStep].actionType;
    // var target = $scope.setActionList[actionStep].hearings[listIndex].settings.options;
    if (optionType === '3' || optionType === '4') {
      // ラジオボタン、プルダウン
      var target = $scope.setActionList[actionStep].hearings[listIndex].settings.options;
    } else {
      // カレンダー
        var target = $scope.setActionList[actionStep].hearings[listIndex].settings.specificDateData;
    }
    target.splice(optionIndex, 1);
    // 表示更新
    $timeout(function () {
      $scope.$apply();
    }).then(function () {
      var targetElmList = $('.action' + actionStep + '_option' + listIndex);
      self.controllListView(actionType, targetElmList, target);
    });
  };

  // ヒアリング、選択肢、メール送信のリスト削除
  this.removeActionItemList = function($event, listIndex) {
    var targetActionId = $($event.target).parents('.set_action_item')[0].id;
    var targetClassName = $($event.target).parents('.itemListGroup')[0].className;
    var actionStep = targetActionId.replace(/action([0-9]+)_setting/, '$1');
    var actionType = $scope.setActionList[actionStep].actionType;

    var targetObjList = "";
    var selector = "";
    var limitNum = 0;

    if (actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
      targetObjList = $scope.setActionList[actionStep].hearings;
      selector = '#action' + actionStep + '_setting .itemListGroup';
    } else if (actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
      targetObjList = $scope.setActionList[actionStep].selection.options;
      selector = '#action' + actionStep + '_setting .itemListGroup li';
    } else if (actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
      targetObjList = $scope.setActionList[actionStep].toAddress;
      selector = '#action' + actionStep + '_setting .itemListGroup li';
      limitNum = 5;
    } else if (actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?>) {
      targetObjList = $scope.setActionList[actionStep].getAttributes;
      selector = '#action' + actionStep + '_setting .itemListGroup';
    } else if (actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
      if (/externalApiRequestHeader/.test(targetClassName)) {
        targetObjList = $scope.setActionList[actionStep].requestHeaders;
        selector = '#action' + actionStep + '_setting .itemListGroup.externalApiRequestHeader tr';
      } else {
        targetObjList = $scope.setActionList[actionStep].responseBodyMaps;
        selector = '#action' + actionStep + '_setting .itemListGroup.externalApiResponseBody tr';
      }
    } else if (actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>) {
      targetObjList = $scope.setActionList[actionStep].conditionList;
      selector = '#action' + actionStep + '_setting .itemListGroup';
    } else if (actionType == <?= C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION ?>) {
      targetObjList = $scope.setActionList[actionStep].addCustomerInformations;
      selector = '#action' + actionStep + '_setting .itemListGroup';
    }

    if (targetObjList !== "" && selector !== "") {
      targetObjList.splice(listIndex, 1);

      // 表示更新
      $timeout(function() {
        $scope.$apply();
      }).then(function() {
        self.controllListView(actionType, $(selector), targetObjList, limitNum)
      });
    }
  };

  /**
   * ファイル送信設定の設定状態確認
   * @param  Object  $action アクション詳細
   * @return Boolean
   */
  this.isFileSet = function(action) {
    return !!action.tChatbotScenarioSendFileId && !!action.file && !!action.file.download_url && !!action.file.file_size;
  }

  /**
   * ファイル選択ダイアログの起動
   */
  this.selectFile = function($event) {
    var targetActionId = $($event.target).parents('.set_action_item')[0].id;
    var fileElm = document.querySelector('#' + targetActionId + ' .fileElm');

    if (fileElm) {
      // ファイルピッカー呼び出し
      fileElm.click();
    }
  };

  /**
   * ファイル削除
   */
  this.removeFile = function($event, actionId) {
    // ファイルIDの削除リストへ追加
    $scope.targetDeleteFileIds.push($scope.setActionList[actionId].tChatbotScenarioSendFileId);

    $scope.setActionList[actionId].tChatbotScenarioSendFileId = null;
    $scope.setActionList[actionId].file = null;

    // localStorageに一時保存を行う
    LocalStorageService.setItem($scope.storageKey, [{key: 'targetDeleteFileIds', value:$scope.targetDeleteFileIds}]);
  }

  /**
   * jsonデータを作る
   * @param Boolean isCheckValidation バリデーションチェックの実行フラグ
   */
  this.createJsonData = function(isCheckValidation) {
    var index = 0;
    var activity = {};
    activity.chatbotType = "1"; // 現在、複数タイプ存在しないため、固定で1を設定する
    activity.scenarios = {};

    angular.forEach($scope.setActionList, function(originalAction) {
      var action = angular.copy(originalAction);
      action.messageIntervalTimeSec = $scope.messageIntervalTimeSec;

      // 表示用のデータをオブジェクトから削除する
      delete action.label;
      delete action.tooltip;
      delete action.default;
      delete action.$valid;

      // 保存可能なデータに変換する
      if (isCheckValidation) {
        switch(parseInt(action.actionType, 10)) {
          case <?= C_SCENARIO_ACTION_TEXT ?>:
            action = self.trimDataText(action);
            break;
          case <?= C_SCENARIO_ACTION_HEARING ?>:
            action = self.trimDataHearing(action);
            break;
          case <?= C_SCENARIO_ACTION_SELECT_OPTION ?>:
            action = self.trimDataSelectOption(action);
            break;
          case <?= C_SCENARIO_ACTION_SEND_MAIL ?>:
            action = self.trimDataSendMail(action);
            break;
          case <?= C_SCENARIO_ACTION_CALL_SCENARIO ?>:
            action = self.trimDataCallScenario(action);
            break;
          case <?= C_SCENARIO_ACTION_EXTERNAL_API ?>:
            action = self.trimDataExternalApi(action);
            break;
          case <?= C_SCENARIO_ACTION_SEND_FILE ?>:
            action = self.trimDataSendFile(action);
            break;
        }
      }
      if (action !== null) {
        activity.scenarios[index++] = action;
      };
    });

    // ファイルIDの削除リストを追加する
    if (typeof $scope.targetDeleteFileIds !== 'undefined' && $scope.targetDeleteFileIds.length >= 1) {
      activity.targetDeleteFileIds = $scope.targetDeleteFileIds;
    }

    return JSON.stringify(activity);
  };

  /**
   * テキスト発言のバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataText = function(action) {
    if (typeof action.message == 'undefined' || action.message == '') {
      return null;
    }
    return action;
  };

  /**
   * ヒアリングのバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataHearing = function(action) {
    if (typeof action.hearings === 'undefined' || typeof action.hearings.length < 1 ||
      (action.isConfirm && (typeof action.confirmMessage === 'undefined' || action.confirmMessage === '' || typeof action.success === 'undefined' || action.success === '' || typeof action.cancel === 'undefined' || action.cancel === ''))
    ) {
      return null;
    }

    var hearings = [];
    angular.forEach(action.hearings, function(item, index) {
      if (typeof item.variableName !== 'undefined' && item.variableName !== '' && typeof item.message !== 'undefined' && item.message !== '') {
        // item.inputLFType = item.inputLFType == 1 ? '1' : '2';
        hearings.push(item);
      }
    });
    if (hearings.length < 1) return null;
    action.hearings = hearings;

    action.isConfirm = action.isConfirm ? '1' : '2';
    action.cv = action.cv ? '1' : '2';
    return action;
  };

  /**
   * 選択肢のバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataSelectOption = function(action) {
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
  };

  /**
   * メール送信のバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataSendMail = function(action) {
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
  };

  /**
   * シナリオ呼び出しのバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataCallScenario = function(action) {
    if (typeof action.scenarioId == 'undefined' || action.scenarioId === '' || action.scenarioId === null) {
      return null;
    }
    action.executeNextAction = action.executeNextAction ? '1' : '2';
    return action;
  }

  /**
   * 外部連携のバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataExternalApi = function(action) {
    // 連携タイプがAPI連携の場合
    if (action.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_API?>){
      if (typeof action.url == 'undefined' || action.url == '') {
        return null;
      }
      // メソッド種別によって送信するリクエスト情報を整理する
      if (action.methodType == <?= C_SCENARIO_METHOD_TYPE_POST ?>) {
        var requestHeaders = [];
        angular.forEach(action.requestHeaders, function(item, index) {
          if (typeof item.name !== 'undefined' && item.name !== '' && typeof item.value !== 'undefined' && item.value !== '') {
            requestHeaders.push(item);
          }
        });
        action.requestHeaders = requestHeaders;
      } else {
        action.requestHeaders =$scope.actionList[<?= C_SCENARIO_ACTION_EXTERNAL_API ?>].default.requestHeaders;
        action.requestBody = '';
      }

      var responseBodys = [];
      angular.forEach(action.responseBodyMaps, function(item, index) {
        if (typeof item.variableName !== 'undefined' && item.variableName !== '' && typeof item.sourceKey !== 'undefined' && item.sourceKey !== '') {
          responseBodys.push(item);
        }
      });
      action.responseBodyMaps = responseBodys;
    } else
    if (action.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_SCRIPT ?>){
      // 連携タイプがスクリプトの場合
      if(typeof action.externalScript == 'undefined' || action.externalScript == ''){
        return null;
      }
    }
    return action;
  }

  /**
   * ファイル送信のバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataSendFile = function(action) {
    if (!self.isFileSet(action)) {
      return null;
    }
    return action;
  }

  this.controllHearingSettingView = function(actionStep) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup');
      var targetObjList = $scope.setActionList[actionStep].hearings;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList)
    });
  };

  this.controllSelectOptionSetting = function(actionStep) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionStep].selection.options;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList);
    });
  };

  this.controllMailSetting = function(actionStep) {
    $timeout(function(){
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup li');
      var targetObjList = $scope.setActionList[actionStep].toAddress;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList, 5);
    });
  };

  this.controllAttributeSettingView = function(actionStep) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup');
      var targetObjList = $scope.setActionList[actionStep].getAttributes;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList)
    });
  };

  this.controllExternalApiSetting = function( actionStep) {
    $timeout(function(){
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup.externalApiRequestHeader tr');
      var targetObjList = $scope.setActionList[actionStep].requestHeaders;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList);

      targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup.externalApiResponseBody tr');
      targetObjList = $scope.setActionList[actionStep].responseBodyMaps;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList);
    });
  };

  this.controllBranchOnConditionSettingView = function(actionStep) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup');
      var targetObjList = $scope.setActionList[actionStep].conditionList;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList, 5)
    });
  };

  this.controllAddCustomerInformationView = function(actionStep) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup');
      var targetObjList = $scope.setActionList[actionStep].addCustomerInformations;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList)
    });
  };

  this.controllBulkHearings = function(actionStep) {
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup');
      var targetObjList = $scope.setActionList[actionStep].multipleHearings;
      self.controllListView($scope.setActionList[actionStep].actionType, targetElmList, targetObjList)
    });
  };

  /**
   * 選択肢、ヒアリング、メール送信,属性値取得のリストに対して、追加・削除ボタンの表示状態を更新する
   * @param String  actionType      アクション種別
   * @param Object  targetElmList   対象のリスト要素(jQueryオブジェクト)
   * @param Object  targetObjList   対象のリストオブジェクト
   * @param Integer limitNum        リストの表示制限がある場合に、制限数を設定する(ない場合、リストの表示数は無制限となる)
   */
  this.controllListView = function(actionType, targetElmList, targetObjList, limitNum) {
    if (typeof limitNum === 'undefined') {
      limitNum = 0;
    }

    var elmNum = targetElmList.length;
    var objNum = targetObjList.length;

    angular.forEach(targetElmList, function(targetElm, index) {
      if (elmNum == 1 && index == 0) {
        // リストが一件のみの場合、追加ボタンのみ表示する
        $(targetElm).find('.btnBlock .disOffgreenBtn').show();
        $(targetElm).find('.btnBlock .deleteBtn,span.removeArea .deleteBtn').hide();
      } else if (actionType == <?= C_SCENARIO_ACTION_HEARING ?> || actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>
        || actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?>) {
        // リストが複数件ある場合、ヒアリング・選択肢・属性値アクションは、追加・削除ボタンを表示する
        $(targetElm).find('.btnBlock .disOffgreenBtn').show();
        $(targetElm).find('.btnBlock .deleteBtn,span.removeArea .deleteBtn').show();
      } else if (index == elmNum -1 && index != limitNum-1) {
        // リストの最後の一件の場合、追加・削除ボタンを表示する
        $(targetElm).find('.btnBlock .disOffgreenBtn').show();
        $(targetElm).find('.btnBlock .deleteBtn,span.removeArea .deleteBtn').show();
      } else {
        // 削除ボタンのみ表示する
        $(targetElm).find('.btnBlock .disOffgreenBtn').hide();
        $(targetElm).find('.btnBlock .deleteBtn,span.removeArea .deleteBtn').show();
      }
    });
  };

  this.handleChangeUitype = function (actionType, actionIndex, hearingIndex, uiType) {
    // set defautl color from widget setting
    if (uiType === '5' || uiType === '4') {
      $scope.setActionList[actionIndex].hearings[hearingIndex] = this.setDefaultColorHearing($scope.setActionList[actionIndex].hearings[hearingIndex]);
    }

    // controll selection view of radio and pulldown
    if (uiType === '3' || uiType == '4') {
      $timeout(function () {
        $scope.$apply();
      }).then(function () {
        var target = $('.action' + actionIndex + '_option' + hearingIndex);
        self.controllListView(actionType, target, target);
      }, 0);
    }
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

  angular.element(window).on('load', function(e) {

    var storageData = LocalStorageService.getData($scope.storageKey);
    if (typeof storageData !== 'undefined' && storageData !== null) {
      if (typeof storageData.targetDeleteFileIds !== 'undefined' && storageData.targetDeleteFileIds.length >= 1) {
        $scope.targetDeleteFileIds = storageData.targetDeleteFileIds;
      }

      if (typeof storageData.scenarios !== 'undefined' && storageData.scenarios !== null && storageData.scenarios !== '') {
        if (window.confirm('一時保存されたシナリオがあります。\nデータを復旧しますか？')) {
          // シナリオ設定の一時保存データの復旧
          $scope.setActionList = [];
          angular.forEach(storageData.scenarios, function(action) {
            $scope.setActionList.push(action);
          });

          $scope.$apply();
          $scope.changeFlg = false;
        } else {
          // ファイルIDの削除リストを取得
          angular.forEach(storageData.scenarios, function(action) {
            if (action.actionType == <?= C_SCENARIO_ACTION_SEND_FILE ?>) {
              $scope.targetDeleteFileIds.push(action.tChatbotScenarioSendFileId);
            }
          });

          // ファイルIDの削除リストが存在する場合、現在のシナリオ設定で一時保存データを上書きする
          if (typeof $scope.targetDeleteFileIds !== 'undefined' && $scope.targetDeleteFileIds.length >= 1) {
            LocalStorageService.setItem($scope.storageKey, [{key: 'targetDeleteFileIds', value: $scope.targetDeleteFileIds}]);
            LocalStorageService.removeItem($scope.storageKey, 'scenarios');
          } else {
            LocalStorageService.remove($scope.storageKey);
          }
        }
      }
    }

    // ファイル選択
    $(document).on('change', '.fileElm', function(e) {
      var targetActionId = $(e.target).parents('.set_action_item').attr('id');
      var actionStep = targetActionId.replace(/action([0-9]+)_setting/, '$1');

      var fileObj = this.files.item(0)
      var fileReader = new FileReader();

      // ファイルの内容は FileReader で読み込む
      fileReader.onload = function(event) {
        if (!fileObj.name) {
          return;
        }
        var loadData = event.target.result;
        $scope.uploadFile(actionStep, fileObj, loadData);
      };
      fileReader.readAsArrayBuffer(fileObj);
    });

    // 変更を行っている場合、アラート表示を行う
    $(window).on('beforeunload', function(e) {
      if($scope.changeFlg) {
        return '行った変更が保存されていない可能性があります。';
      }
    });
  });
}])
.controller('DialogController', ['$scope', '$timeout', 'SimulatorService', 'LocalStorageService', function($scope, $timeout, SimulatorService, LocalStorageService) {
  //thisを変数にいれておく
  var self = this;
  $scope.setActionList = [];
  $scope.inputTypeList = <?php echo json_encode($chatbotScenarioInputType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;

  $scope.widget = SimulatorService;

  /**
   * シミュレーションの起動(ダイアログ表示)
   * @param Object activity 実行可能なシナリオ
   */
  $scope.$on('openSimulator', function(event, activity) {
    var scenarios = JSON.parse(activity).scenarios;
    $scope.setActionList = scenarios;
    var defaultHeight = 101;
    if(document.getElementById("maximum_description") != null){
      defaultHeight += 40;
    }
    $('#tchatbotscenario_simulator_wrapper').show();
    $timeout(function() {
      $scope.$apply();
    }).then(function() {
      $('#simulator_popup').css({
        width: $('#sincloBox').outerWidth() + 28 + 'px',
        height: $('#sincloBox').outerHeight() + defaultHeight + 'px'
      });
      $scope.actionInit();
    }, 0);
  });

  $(document).on('onWidgetSizeChanged', function(e){
    var defaultHeight = 101;
    if(document.getElementById("maximum_description") != null){
      defaultHeight += 40;
    }
    $('#simulator_popup').css({
      width: $('#sincloBox').outerWidth() + 28 + 'px',
      height: $('#sincloBox').outerHeight() + defaultHeight + 'px'
    });
  });

  // シミュレーションで受け付けた受信メッセージ
  $scope.$on('receiveVistorMessage', function(event, message, prefix) {
    // 対応するアクションがない場合は何もしない
    if (typeof $scope.setActionList[$scope.actionStep] === 'undefined') {
      return;
    }

    if ($scope.setActionList[$scope.actionStep].actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
      var actionDetail = $scope.setActionList[$scope.actionStep];

      if ($scope.hearingIndex < actionDetail.hearings.length) {
        // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
        var inputType = actionDetail.hearings[$scope.hearingIndex].inputType;
        var regex = new RegExp($scope.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, "$1"));
        var isMatched = message.split(/\r\n|\n/).every(function(string) {
          return string.length >= 1 ? regex.test(string) : true;
        });
        if (isMatched) {
          // 変数の格納
          var storageParam = [];
          LocalStorageService.setItem('chatbotVariables', [{key: actionDetail.hearings[$scope.hearingIndex].variableName, value: message}]);
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
      if (actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length) && $scope.replaceVariable(actionDetail.cancel) === message) {
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
      var storageParam = [];
      LocalStorageService.setItem('chatbotVariables', [{key: $scope.setActionList[$scope.actionStep].selection.variableName, value: message}]);
      $scope.actionStep++;
      $scope.doAction();
    } else
    if ($scope.setActionList[$scope.actionStep].actionType == <?= C_SCENARIO_ACTION_RECEIVE_FILE ?>) {
      $scope.actionStep++;
      $scope.doAction();
    }
  });

  $scope.addVisitorHearingMessage = function (message) {
    var actionDetail = $scope.setActionList[$scope.actionStep];

    if ($scope.hearingIndex < actionDetail.hearings.length) {
      var uiType = actionDetail.hearings[$scope.hearingIndex].uiType;

      if (uiType === '1' || uiType === '2') {
        // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
        var inputType = actionDetail.hearings[$scope.hearingIndex].inputType;
        var regex = new RegExp($scope.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, "$1"));
        var isMatched = message.split(/\r\n|\n/).every(function(string) {
          return string.length >= 1 ? regex.test(string) : true;
        });
        if (isMatched) {
          LocalStorageService.setItem('chatbotVariables', [{key: actionDetail.hearings[$scope.hearingIndex].variableName, value: message}]);
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
      } else {
        LocalStorageService.setItem('chatbotVariables', [{key: actionDetail.hearings[$scope.hearingIndex].variableName, value: message}]);
        // 次のアクション
        $scope.hearingIndex++;
        if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
          !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
          $scope.hearingIndex = 0;
          $scope.actionStep++;
        }
      }
    } else
    if (actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length) && $scope.replaceVariable(actionDetail.cancel) === message) {
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
  };

  $scope.reSelectionHearing = function (message, actionStep, hearingIndex) {
    $scope.hearingIndex = hearingIndex;
    $scope.actionStep = actionStep;
    var actionDetail = $scope.setActionList[actionStep];
    var uiType = actionDetail.hearings[hearingIndex].uiType;
    // テキストタイプ
    if (uiType === '1' || uiType === '2') {
      // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
      var inputType = actionDetail.hearings[hearingIndex].inputType;
      var regex = new RegExp($scope.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, "$1"));
      var isMatched = message.split(/\r\n|\n/).every(function (string) {
        return string.length >= 1 ? regex.test(string) : true;
      });
      if (isMatched) {
        // 変数の格納
        LocalStorageService.setItem('chatbotVariables', [{
          key: actionDetail.hearings[hearingIndex].variableName,
          value: message
        }]);
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
    } else {
      // 変数の格納
      LocalStorageService.setItem('chatbotVariables', [{
        key: actionDetail.hearings[hearingIndex].variableName,
        value: message
      }]);
      // 次のアクション
      $scope.hearingIndex++;
      if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
        !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
        $scope.hearingIndex = 0;
        $scope.actionStep++;
      }
    }


    $scope.doAction();
  };

  // シミュレーションの終了(ダイアログ非表示)
  $scope.closeSimulator = function() {
    $scope.actionStop();
    $('#tchatbotscenario_simulator_wrapper').hide();
  };

  // アクションの開始
  $scope.actionInit = function () {
    $scope.actionStep = 0;
    $scope.hearingIndex = 0;
    $scope.sendFileIndex = 0;
    $scope.firstActionFlg = true;
    $scope.actionTimer;
    $scope.hearingInputResult = true;

    // シミュレーション上のメッセージをクリアする
    $scope.$broadcast('removeMessage');
    $scope.doAction();
  }

  $scope.$watch('actionStep', function () {
    $scope.widget.setCurrentActionStep($scope.actionStep);
  });

  $scope.$watch('hearingIndex', function () {
    $scope.widget.setCurrentHearingIndex($scope.hearingIndex);
  });

  // アクションの停止
  $scope.actionStop = function() {
    $timeout.cancel($scope.simulatorTimer);
  }

  // アクションのクリア(アクションを最初から実行し直す)
  $scope.actionClear = function() {
    $scope.actionStop();
    $scope.actionInit();
    $scope.setActionList = $scope.actionListOrigin;
  };

  /**
   * アクションの実行
   * @param String setTime 基本設定のメッセージ間隔に関わらず、メッセージ間隔を指定
   */
  $scope.receiveFileEventListener = null;
  $scope.firstActionFlg = true;
  $scope.doAction = function(setTime) {
    if (typeof $scope.setActionList[$scope.actionStep] !== 'undefined' && typeof $scope.setActionList[$scope.actionStep].actionType !== 'undefined') {
      var actionDetail = $scope.setActionList[$scope.actionStep];
      // メッセージ間隔
      var time =  actionDetail.messageIntervalTimeSec;
      var branchOnConditon = false;

      //条件分岐の場合は複雑な時間指定が必要になるので括りだしておく
      if(actionDetail.actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>){
        branchOnConditon = true;
        var value = LocalStorageService.getItem('chatbotVariables', actionDetail.referenceVariable);
        for(var i=0; i<actionDetail.conditionList.length; i++) {
          if($scope.isMatch(value, actionDetail.conditionList[i])) {
            if(actionDetail.conditionList[i].actionType =='1'){
              chatBotTyping();
              break;
            }
          }else if(actionDetail.elseEnabled){
            if(actionDetail.elseAction.actionType == '1'){
              chatBotTyping();
              break;
            }
          }
          time = setTime || '0';
        }
      }

      if(!branchOnConditon){
        if (time == 0 || !!setTime || ($scope.actionStep === 0 && $scope.hearingIndex === 0 && $scope.firstActionFlg) || actionDetail.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?> || actionDetail.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?> || actionDetail.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?> || actionDetail.actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?> || actionDetail.actionType == <?= C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION ?>) {
          time = setTime || '0';
          $scope.firstActionFlg = false;
        }else{
          chatBotTyping();
        }
      }

      $timeout.cancel($scope.actionTimer);
      $scope.actionTimer = $timeout(function() {
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_TEXT ?>) {
          // テキスト発言
          $scope.$broadcast('addReMessage', $scope.replaceVariable(actionDetail.message), 'action' + $scope.actionStep);
          $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
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
          $scope.$broadcast('addReMessage', $scope.replaceVariable(messageList.join('\n')), 'action' + $scope.actionStep);
          $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
          // メール送信
          $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
          $scope.actionStep++;
          $scope.doAction();
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?>) {
          self.getScenarioDetail(actionDetail.scenarioId, actionDetail.executeNextAction);
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
          self.callExternalApi(actionDetail);
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_SEND_FILE ?>) {
          // ファイル送信
          if ($scope.sendFileIndex == 0 && !!actionDetail.message) {
            $scope.$broadcast('addReMessage', $scope.replaceVariable(actionDetail.message), 'action' + $scope.actionStep);
            $scope.sendFileIndex++;
            $scope.doAction();
          } else {
            $scope.$broadcast('addReFileMessage', actionDetail.file);
            $scope.sendFileIndex = 0;
            $scope.actionStep++;
            $scope.doAction();
          }
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?>) {
          $scope.actionStep++;
          $scope.doAction();
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION ?>) {
          $scope.actionStep++;
          $scope.doAction();
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_RECEIVE_FILE ?>) {
          if(actionDetail.dropAreaMessage) {
            $scope.$broadcast('addSeReceiveFileUI', actionDetail.dropAreaMessage, actionDetail.cancelEnabled, actionDetail.cancelLabel, actionDetail.receiveFileType, actionDetail.extendedReceiveFileExtensions);
            $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
            if($scope.receiveFileEventListener) {
              $scope.receiveFileEventListener();
            }
            $scope.receiveFileEventListener = $scope.$on('onErrorSelectFile', function(){
              var message = actionDetail.errorMessage;
              $scope.$broadcast('addReErrorMessage', $scope.replaceVariable(message), 'action' + $scope.actionStep);
            });
          }
        } else
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>) {
          // 指定の変数を取得
          chatBotTypingRemove();
          var value = LocalStorageService.getItem('chatbotVariables', actionDetail.referenceVariable);
          for(var i=0; i<actionDetail.conditionList.length; i++) {
            if($scope.isMatch(value, actionDetail.conditionList[i])) {
              $scope.doBranchOnCondAction(actionDetail.conditionList[i]);
              if(actionDetail.conditionList[i] =='1'){
                chatBotTyping();
              }
              return;
            }
          }
          // どの条件にもマッチしなかった場合
          if(actionDetail.elseEnabled) {
            $scope.doBranchOnCondAction(actionDetail.elseAction);
          }else{
            $scope.actionStep++;
            $scope.doAction();
          }
        }
        chatBotTypingRemove();
      }, parseInt(time, 10) * 1000);
    } else {
      setTimeout(chatBotTypingRemove,801);
      $scope.actionStop();
    }
  }

  $scope.isMatch = function(targetValue, condition) {
    switch(Number(condition.matchValueType)) {
      case 1: // いずれかを含む場合
        return $scope.matchCaseInclude(targetValue, $scope.splitMatchValue(condition.matchValue));
      case 2: // いずれも含まない場合
        return $scope.matchCaseExclude(targetValue, $scope.splitMatchValue(condition.matchValue));
      default:
        return false;
    }
  };

  $scope.doBranchOnCondAction = function(condition, callback) {
    switch(Number(condition.actionType)) {
      case 1:
        $scope.$broadcast('addReMessage', $scope.replaceVariable(condition.action.message), 'action' + $scope.actionStep);
        $scope.actionStep++;
        $scope.doAction();
        break;
      case 2:
        // シナリオ呼び出し
        var targetScenarioId = condition.action.callScenarioId;
        console.log("targetScenarioId : %s",targetScenarioId);
        if(targetScenarioId === "self") {
          var activity = {};
          activity.scenarios = $scope.actionListOrigin;
          $scope.setActionList = $scope.setCalledScenario(activity, condition.action.executeNextAction == 1);
          $scope.doAction();
        } else {
          self.getScenarioDetail(targetScenarioId, condition.action.executeNextAction == 1);
        }
        break;
      case 3:
        $scope.actionStop();
        // シナリオ終了
        break;
      case 4:
        $scope.actionStep++;
        $scope.doAction();
        // 何もしない（次のアクションへ）
        break;
    }
  };

  $scope.splitMatchValue = function(val) {
    var splitedArray = [];
    val.split('"').forEach(function(currentValue, index, array){
      if(array.length > 1) {
        if(index !== 0 && index % 2 === 1) {
          // 偶数個：そのまま文字列で扱う
          if(currentValue !== "") {
            splitedArray.push(currentValue);
          }
        } else {
          if(currentValue) {
            var trimValue = currentValue.trim(),
              splitValue = trimValue.replace(/　/g, " ").split(" ");
            splitedArray = splitedArray.concat($.grep(splitValue, function(e){return e !== "";}));
          }
        }
      } else {
        var trimValue = currentValue.trim(),
          splitValue = trimValue.replace(/　/g, " ").split(" ");
        splitedArray = splitedArray.concat($.grep(splitValue, function(e){return e !== "";}));
      }
    });
    return splitedArray;
  };

  $scope.matchCaseInclude = function(val, words) {
    console.log("_matchCaseInclude : %s <=> %s",words, val);
    var result = false;
    for(var i=0; i < words.length; i++) {
      if(words[i] === "") {
        continue;
      }

      var word = words[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var preg = new RegExp(word);
      result = preg.test(val);

      if(result) { // いずれかを含む
        break;
      }
    }
    return result;
  };

  $scope.matchCaseExclude = function(val, words) {
    for(var i=0; i < words.length; i++) {
      if(words[i] === "") {
        if (words.length > 1 && i === words.length - 1) {
          break;
        }
        continue;
      } else {
        var word = words[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
        var preg = new RegExp(word);
        var exclusionResult = preg.test(val);
        if(exclusionResult) {
          // 含んでいる場合はNG
          return false;
        }
      }
    }
    //最後まで含んでいなかったらOK
    return true;
  };

  /**
   * ヒアリングアクションの実行
   * @param Object actionDetail アクションの詳細
   */
  $scope.doHearingAction = function(actionDetail) {
    if (!$scope.hearingInputResult) {
      // エラーメッセージ
      // var message = actionDetail.errorMessage;
      var errorMessage = actionDetail.hearings[$scope.hearingIndex].errorMessage;
      $scope.$broadcast('addReMessage', $scope.replaceVariable(errorMessage), 'action' + $scope.actionStep);
      $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
      $scope.hearingInputResult = true;
      $scope.doAction();
    } else
    if ($scope.hearingIndex < actionDetail.hearings.length) {
      var hearingDetail = actionDetail.hearings[$scope.hearingIndex];
      // 質問する
      var message = hearingDetail.message;
      var oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
      var isRestore = actionDetail.restore && oldValue ? true : false;

      // テキスト一形　＆　テキスト複数行
      if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_ONE_ROW_TEXT ?>) {
        $scope.$broadcast('addReMessage', $scope.replaceVariable(message), 'action' + $scope.actionStep + '_hearing' + $scope.hearingIndex);
        if (isRestore) {
          $('#miniSincloChatMessage').val(oldValue);
        }
        $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType, hearingDetail.required);
        $scope.$broadcast('allowInputLF', false, hearingDetail.inputType);
        var strInputRule =$scope.inputTypeList[hearingDetail.inputType].inputRule;
        $scope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, "$1"));
      }

      if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_MULTIPLE_ROW_TEXT ?>) {
        $scope.$broadcast('addReMessage', $scope.replaceVariable(message), 'action' + $scope.actionStep + '_hearing' + $scope.hearingIndex);
        if (isRestore) {
          $('#sincloChatMessage').val(oldValue);
        }
        $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType, hearingDetail.required);
        $scope.$broadcast('allowSendMessageByShiftEnter', true, hearingDetail.inputType);
        var strInputRule =$scope.inputTypeList[hearingDetail.inputType].inputRule;
        $scope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, "$1"));
      }

      if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_RADIO_BUTTON ?>) {
        var data = {};
        data.options = hearingDetail.settings.options;
        data.design = hearingDetail.settings.customDesign;
        data.prefix = 'action' + $scope.actionStep + '_hearing' + $scope.hearingIndex;
        data.message = $scope.replaceVariable(message);
        data.isRestore = isRestore;
        data.oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
        data.textColor = $scope.widget.settings.re_background_color;
        data.backgroundColor = $scope.widget.settings.re_text_color;

        $scope.$broadcast('addReRadio', data);
        $scope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType, hearingDetail.required);
      }

      if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_PULLDOWN ?>) {
        var data = {};
        data.options = hearingDetail.settings.options;
        data.design = hearingDetail.settings.customDesign;
        data.prefix = 'action' + $scope.actionStep + '_hearing' + $scope.hearingIndex;
        data.message = $scope.replaceVariable(message);
        data.isRestore = isRestore;
        data.oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
        data.textColor = $scope.widget.settings.re_background_color;
        data.backgroundColor = $scope.widget.settings.re_text_color;

        $scope.$broadcast('addRePulldown', data);
        $scope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType, hearingDetail.required);
      }

      if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_CALENDAR ?>) {
        var data = {};
        data.settings = hearingDetail.settings;
        data.design = hearingDetail.settings.customDesign;
        data.prefix = 'action' + $scope.actionStep + '_hearing' + $scope.hearingIndex;
        data.message = $scope.replaceVariable(message);
        data.isRestore = isRestore;
        data.oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
        data.textColor = $scope.widget.settings.re_background_color;
        data.backgroundColor = $scope.widget.settings.re_text_color;

        $scope.$broadcast('addReCalendar', data);
        $scope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType, hearingDetail.required);
      }
    } else
    if (actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length)) {
      // 確認メッセージ
      var messageList = [actionDetail.confirmMessage, '[] ' + actionDetail.success, '[] ' + actionDetail.cancel];
      var message = messageList.filter( function(string) {
        return string !== '';
      }).join('\n');

      $scope.$broadcast('addReMessage', $scope.replaceVariable(message), 'action' + $scope.actionStep + '_confirm');
      // 設定したOK/NG以外が入力されないよう、自由入力エリアを非表示とする
      $scope.$broadcast('switchSimulatorChatTextArea', false);
    } else {
      // 次のアクションへ移行する
      $scope.hearingIndex = 0;
      $scope.actionStep++;
      $scope.doAction();
    }
  };

  /**
   * メッセージ内の変数を、ローカルストレージ内のデータと置き換える
   * @param String message 変数を含む文字列
   * @return String        置換後の文字列
   */
  $scope.replaceVariable = function(message) {
    message = message ? message : '';
    return message.replace(/{{(.+?)\}}/g, function(param) {
      var name = param.replace(/^{{(.+)}}$/, '$1');
      return LocalStorageService.getItem('chatbotVariables', name) || name;
    });
  };

  /**
   * 呼び出し先のシナリオ詳細を取得する
   * @param String scenarioId 呼び出し先シナリオID
   * @param String isNext     呼び出したシナリオ終了後、次のアクションを続けるか
   */
  this.getScenarioDetail = function(scenarioId, isNext) {
    $.ajax({
      url: "<?= $this->Html->url('/TChatbotScenario/remoteGetActionDetail') ?>",
      type: 'post',
      dataType: 'json',
      data: {
        id: scenarioId
      },
      cache: false,
      timeout: 10000
    }).done(function(data) {
      console.info('successed get scenario detail.');
      try {
        var activity = JSON.parse(data['TChatbotScenario']['activity']);
        // 取得したシナリオのアクション情報を、setActionList内に詰める
        var scenarios = $scope.setCalledScenario(activity, isNext);
        $scope.setActionList = scenarios;
      } catch(e) {
        $scope.actionStep++;
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      // エラー情報を出力する
      console.warn('failed get scenario detail');
      console.error(errorThrown);

      $scope.actionStep++;
    }).always(function() {
      // アクションを実行する
      $scope.doAction();
    });
  };

  $scope.setCalledScenario = function(activity, isNext) {
    var scenarios = {};
    var idx = 0;
    angular.forEach($scope.setActionList, function(scenario, key) {
      if (key == $scope.actionStep) {
        for (var exKey in activity.scenarios) {
          scenarios[idx++] = activity.scenarios[exKey];
        }
      } else
      if (isNext == 1 || key <= $scope.actionStep) {
        scenarios[idx++] = $scope.setActionList[key];
      }
    });
    return scenarios;
  };

  /**
   * 外部システム連携のAPI実行(Controllerを呼び出す)
   * @param Object actionDetail アクション詳細
   */
  this.callExternalApi = function(actionDetail) {
    // パラメーターの設定
    var requestHeaders = [];
    if (typeof actionDetail.requestHeaders !== 'undefined') {
      requestHeaders = actionDetail.requestHeaders.map(function(param) {
        return {'name': $scope.replaceVariable(param.name), 'value': $scope.replaceVariable(param.value)};
      });
    }
    var sendData = {
      'url': encodeURI($scope.replaceVariable(actionDetail.url)),
      'methodType': actionDetail.methodType,
      'requestHeaders': requestHeaders,
      'requestBody': $scope.replaceVariable(actionDetail.requestBody),
      'responseType': actionDetail.responseType,
      'responseBodyMaps': actionDetail.responseBodyMaps
    };

    $.ajax({
      url: "<?= $this->Html->url('/Notification/callExternalApi') ?>",
      type: 'post',
      dataType: 'json',
      data: {
        apiParams: JSON.stringify(sendData)
      },
      cache: false,
      timeout: 10000
    }).done(function(data) {
      console.info('successed calling external api.');
      var storageParam = [];
      data.result.forEach(function(param) {
        storageParam.push({key: param.variableName, value: param.value});
      });
      LocalStorageService.setItem('chatbotVariables', storageParam);
    }).fail(function(error) {
      console.error('failed calling external api.', error.statusText);
    }).always(function() {
      $scope.actionStep++;
      $scope.doAction();
    });
  };

  // handle when click on skip button
  $(document).on('click', '.sincloChatSkipBtn', function () {
    $('#action' + $scope.actionStep + '_hearing' + $scope.hearingIndex + '_question').remove();
    $scope.hearingIndex++;
    $scope.doAction();
  });

  // handle when click on next button
  $(document).on('click', '.nextBtn', function () {
    var numbers = $(this).attr('id').match(/\d+/g).map(Number);
    var actionStep = numbers[0];
    var hearingIndex = numbers[1];

    var variable = $scope.setActionList[actionStep].hearings[hearingIndex].variableName;
    var message = LocalStorageService.getItem('chatbotVariables', variable);
    $scope.$broadcast('addSeMessage', $scope.replaceVariable(message), 'action' + actionStep + '_hearing' + $scope.hearingIndex);
    $(this).hide();

    $scope.hearingIndex++;
    $scope.doAction();
  });

  $(document).on('click', '#chatTalk input[type="radio"]', function() {
    var prefix = $(this).attr('id').replace(/-sinclo-radio[0-9a-z-]+$/i, '');
    var message = $(this).val().replace(/^\s/, '');
    var isConfirm = prefix.indexOf('confirm') !== -1 ? true : false;
    var name = $(this).attr('name');

    var numbers = prefix.match(/\d+/g).map(Number);
    var actionStep = numbers[0];
    var hearingIndex = numbers[1];

    if (isConfirm) {
      // confirm message
      $scope.addVisitorHearingMessage(message);
      $scope.$broadcast('addSeMessage', $scope.replaceVariable(message), 'action' + actionStep + '_hearing_confirm');
      // ラジオボタンを非活性にする
      $('input[name=' + name + '][type="radio"]').prop('disabled', true);
    } else {
      // radio type
      var variable = $scope.setActionList[numbers[0]].hearings[numbers[1]].variableName;
      var item = LocalStorageService.getItem('chatbotVariables', variable);
      $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
      if (item && item != message) {
        $('#action' + actionStep + '_hearing' + hearingIndex + '_question').nextAll('div').remove();
        $scope.reSelectionHearing(message, numbers[0], numbers[1]);
      } else {
        $scope.addVisitorHearingMessage(message);
      }
      $scope.$broadcast('addSeMessage', $scope.replaceVariable(message), 'action' + actionStep + '_hearing' + $scope.hearingIndex);
    }
  });

  // プルダウンの選択
  $(document).on('change', '#chatTalk select', function () {
    var prefix = $(this).attr('id').replace(/-sinclo-pulldown[0-9a-z-]+$/i, '');
    var message = $(this).val().replace(/^\s/, '');

    var numbers = prefix.match(/\d+/g).map(Number);
    var actionStep = numbers[0];
    var hearingIndex = numbers[1];

    if (message !== '選択してください') {
      var variable = $scope.setActionList[numbers[0]].hearings[numbers[1]].variableName;
      var item = LocalStorageService.getItem('chatbotVariables', variable);
      $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
      if (item && item != message) {
        $('#action' + actionStep + '_hearing' + hearingIndex + '_question').nextAll('div').remove();
        $scope.reSelectionHearing(message, numbers[0], numbers[1]);
      } else {
        $scope.addVisitorHearingMessage(message);
      }
      $scope.$broadcast('addSeMessage', $scope.replaceVariable(message), 'action' + actionStep + '_hearing' + $scope.hearingIndex);
    }
  });

  // カレンダーの選択
  $(document).on('change', '#chatTalk .flatpickr-input', function () {
    var prefix = $(this).attr('id').replace(/-sinclo-datepicker[0-9a-z-]+$/i, '');
    var message = $(this).val().replace(/^\s/, '');

    var numbers = prefix.match(/\d+/g).map(Number);
    var actionStep = numbers[0];
    var hearingIndex = numbers[1];

    var variable = $scope.setActionList[numbers[0]].hearings[numbers[1]].variableName;
    var item = LocalStorageService.getItem('chatbotVariables', variable);
    $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
    if (item && item != message) {
      $('#action' + actionStep + '_hearing' + hearingIndex + '_question').nextAll('div').remove();
      $scope.reSelectionHearing(message, numbers[0], numbers[1]);
    } else {
      $scope.addVisitorHearingMessage(message);
    }
    $scope.$broadcast('addSeMessage', $scope.replaceVariable(message), 'action' + actionStep + '_hearing' + $scope.hearingIndex);
  });

  // re-input text type
  $(document).on('click', '#chatTalk .underlineText', function () {
    var prefix = $(this).parents('.liBoxRight, .liRight').attr('id');
    var numbers = prefix.match(/\d+/g).map(Number);
    var actionStep = numbers[0];
    var hearingIndex = numbers[1];
    $scope.actionStep = actionStep;
    $scope.hearingIndex = hearingIndex;
    var actionDetail = $scope.setActionList[actionStep];
    var hearingDetail = $scope.setActionList[actionStep].hearings[hearingIndex];

    var variable = $scope.setActionList[actionStep].hearings[hearingIndex].variableName;
    var value = LocalStorageService.getItem('chatbotVariables', variable);
    $('#action' + actionStep + '_hearing' + hearingIndex + '_question').nextAll('div').remove();

    if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_ONE_ROW_TEXT ?>) {
      $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType, hearingDetail.required);
      $scope.$broadcast('allowInputLF', false, hearingDetail.inputType);
      var strInputRule = $scope.inputTypeList[hearingDetail.inputType].inputRule;
      $scope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, "$1"));
      $('#miniSincloChatMessage').val(value);
    }

    if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_MULTIPLE_ROW_TEXT ?>) {
      $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType, hearingDetail.required);
      $scope.$broadcast('allowSendMessageByShiftEnter', true, hearingDetail.inputType);
      var strInputRule = $scope.inputTypeList[hearingDetail.inputType].inputRule;
      $scope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, "$1"));
      $('#sincloChatMessage').val(value);
    }
  });
}])
.directive('resizeTextarea', function() {
  return {
    restrict: 'E',
    replace: true,
    template: '<textarea style="font-size: 13px; border-width: 1px; padding: 5px; line-height: 1.5;"></textarea>',
    link: function(scope, element, attrs) {
      var maxRow = element[0].dataset.maxRow || 10;                       // 表示可能な最大行数
      var fontSize = parseFloat(element[0].style.fontSize, 10);           // 行数計算のため、templateにて設定したフォントサイズを取得
      var borderSize = parseFloat(element[0].style.borderWidth, 10) * 2;  // 行数計算のため、templateにて設定したボーダーサイズを取得(上下/左右)
      var paddingSize = parseFloat(element[0].style.padding, 10) * 2;     // 表示高さの計算のため、templateにて設定したテキストエリア内の余白を取得(上下/左右)
      var lineHeight = parseFloat(element[0].style.lineHeight, 10);       // 表示高さの計算のため、templateにて設定した行の高さを取得
      var elm = angular.element(element[0]);

      function autoResize() {
        // テキストエリアの要素のサイズから、borderとpaddingを引いて文字入力可能なサイズを取得する
        var areaWidth = elm[0].getBoundingClientRect().width - borderSize - paddingSize;

        // フォントサイズとテキストエリアのサイズを基に、行数を計算する
        var textRow = 0;
        elm[0].value.split('\n').forEach(function(string) {
          var stringWidth = string.length * fontSize;
          textRow += Math.max(Math.ceil(stringWidth/areaWidth), 1);
        });

        // 表示する行数に応じて、テキストエリアの高さを調整する
        if (textRow > maxRow) {
          elm[0].style.height = (maxRow * (fontSize*lineHeight)) + paddingSize + 'px';
          elm[0].style.overflow = 'auto';
        } else {
          elm[0].style.height = (textRow * (fontSize*lineHeight)) + paddingSize + 'px';
          elm[0].style.overflow = 'hidden';
        }
      }

      autoResize();
      scope.$watch(attrs.ngModel, autoResize);
      $(window).on('load', autoResize);
      $(window).on('resize', autoResize);
      elm[0].addEventListener('input', autoResize);
    }
  };
})
.directive('validateAction', function() {
  return {
    restrict: 'A',
    required: 'ngModel',
    link: function(scope, element, attrs) {
      var elm = angular.element(element[0]);

      scope.$watch(attrs.ngModel, function(actionItem) {
        actionValidationCheck(elm[0], scope.$parent.setActionList, actionItem);
      }, true);
    }
  };
});

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
  $('#TChatbotScenarioEntryForm').submit();
}

$(document).ready(function() {
  // ツールチップの表示制御（ヘルプ）
  $(document).off('mouseenter','.questionBtn').on('mouseenter','.questionBtn', function(event){
    /**拡大率によって表示が崩れないよう、拡大率を取得し、表示の調整*********/
    var per_expand = window.innerHeight/974;
    if(per_expand < 1){
      per_expand = 1;
    }
      $('.explainTooltip').find('ul').css('max-width', 448*per_expand +'px');
    /**********************************************************************/

    var targetObj = $('.explainTooltip');
    targetObj.find('icon-annotation .detail').html($(this).data('tooltip'));
    targetObj.find('icon-annotation').css('display','block');
    var targetWidth = Number(targetObj.find('ul').css('width').replace('px',''));
    var targetHeight = Number(targetObj.find('ul').css('height').replace('px',''));
    targetObj.css({
      top: $(this).offset().top - 45 + 15*per_expand + 'px',
      left: $(this).offset().left - targetWidth*0.65 + 'px'
    });
    //画面の拡大率を取得(どのような状況でもしっかり処理を行えるよう

    console.log($(this));
    //画面よりも下にヘルプが行ってしまう場合の処理
    var contentposition = Number(targetObj.css('top').replace('px','')) + targetHeight + 175;
    if(contentposition > window.innerHeight){
      targetObj.css({
        top: $(this).offset().top - 60 - 30*per_expand + 'px',
      });
      //ヘルプを上に出しても下に出してもオーバーしてしまう場合
      if(Number(targetObj.css('top').replace('px','')) - targetHeight + targetObj.outerHeight() < $('#color-bar').outerHeight()){
        targetObj.css({
          left:$(this).offset().left - (targetWidth*1.2) + 5 + 'px',
          top :$('#content').outerHeight()/2 - targetHeight/2  + 'px'
        });
      }else{
        targetObj.find('ul').css('top','auto');
        targetObj.find('ul').css('bottom','0px');
      }
    }
    //画面よりも左にヘルプが行ってしまう場合の処理
    if(targetObj.css('left').replace("px","") < 50){
      targetObj.css('left','30px');
    }




    // 表示サイズ調整
    var targetWidth = $(this).data('tooltip-width');
    if (!!targetWidth) {
      targetObj.find('icon-annotation').css('width', targetWidth + 'px');
    } else {
      targetObj.find('icon-annotation').css('width', '18em');
    }
  }).off('mouseleave','.questionBtn').on('mouseleave','.questionBtn', function(event){
    $('.explainTooltip').find('icon-annotation').css('display','none');
    $('.explainTooltip').find('ul').css('top','0px');
    $('.explainTooltip').find('ul').css('bottom','auto');
  });

  // ツールチップの表示制御（エラーメッセージ）
  $(document).off('mouseenter','.errorBtn').on('mouseenter','.errorBtn', function(event){
    var targetObj = $('.errorBalloon');

    var messages = this.dataset.tooltip.split('\n');
    messages.map(function(message) {
      var newElm = document.createElement('p');
      newElm.textContent = message;
      targetObj.first('.detail').append(newElm);
    });

    targetObj.css({
      top: ($(this).offset().top - targetObj.outerHeight() - 70) + 'px',
      left:($(this).offset().left - 79) + 'px',
      display: 'block'
    });
  }).off('mouseleave','.errorBtn').on('mouseleave','.errorBtn', function(event) {
    var targetObj = $('.errorBalloon');
    targetObj.css('display', 'none');
    targetObj.first('.detail').empty();
  });

  // 設定側のスクロールに応じて、プレビュー側をスクロールさせる
  var time = 500;
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
  $(document).on('click', '#tchatbotscenario_form_preview_body > section', function() {
    var box = $('#tchatbotscenario_form_action_body');
    var settingId = $(this).attr('id').replace(/preview$/, 'setting');
    var target = $('#' + settingId);
    var targetY = target.position().top - box.position().top;

    box.stop().animate({
      scrollTop: box.scrollTop() + targetY
    }, time);
    target.closest('.set_action_item ').focus();
  });

  //フォーカスされたアクションに応じて、関連するプレビューを強調表示する
  $(document).on('focus', '.set_action_item', function () {
    $('.set_action_item').blur();
    var previewId = $(this).attr('id').replace(/setting$/, 'preview');
    $('.actionTitle').removeClass('active');
    $('#' + previewId + ' .actionTitle').addClass('active');
    $('.closeBtn').css('display', 'none');
    $('.set_action_item h4').css('background-color', '#DADADA');
    $(this).find('.closeBtn').css('display', 'block');
    $(this).find('h4').css('background-color', '#C3D69B');
    $(this).css('border', '2px solid #C3D69B');
  }).on('blur', '.set_action_item', function () {
    var previewId = $(this).attr('id').replace(/setting$/, 'preview');
    $('.set_action_item').css('border', '2px solid #a9aaa4');
    $('.set_action_item h4').css('background-color', '#DADADA');
    $('.closeBtn').show();
    $('#' + previewId + ' .actionTitle').removeClass('active');
  }).on('focusout', '.set_action_item', function () {
    var previewId = $(this).attr('id').replace(/setting$/, 'preview');
    $('.set_action_item').css('border', '2px solid #a9aaa4');
    $('.set_action_item h4').css('background-color', '#DADADA');
    $('.closeBtn').show();
    $('#' + previewId + ' .actionTitle').removeClass('active');
  });

  $(document).on('click', '.set_action_item', function () {
    $(this).focus();
  });

  // 各アクションのキーイベントに応じて、プレビューのスクロール位置を調整する
  $(document).on('keydown, keyup', '.set_action_item input, .set_action_item textarea', function() {
    var previewId = $(this).parents('.set_action_item').attr('id').replace(/setting$/, 'preview');
    var box = $('#tchatbotscenario_form_preview_body');
    var target = $('#' + previewId);
    var targetY = target.position().top - box.position().top;

    box.stop().animate({
      scrollTop: box.scrollTop() + targetY
    }, time);
  });

  // アクション設定の高さ調整
  var actionWrapperHeight = document.getElementById('tchatbotscenario_form_action').offsetHeight;
  var actionHeaderHeight = document.getElementById('tchatbotscenario_form_action_header').offsetHeight;
  $('#tchatbotscenario_form_action_body').css({height: actionWrapperHeight - actionHeaderHeight + 'px'});

  $(window).resize(function() {
    var actionWrapperHeight = document.getElementById('tchatbotscenario_form_action').offsetHeight;
    var actionHeaderHeight = document.getElementById('tchatbotscenario_form_action_header').offsetHeight;
    $('#tchatbotscenario_form_action_body').css({height: actionWrapperHeight - actionHeaderHeight + 'px'});
  });
});

/**
 * アクションのバリデーションとエラーメッセージの設定
 * @param  Node   element       チェック対象のアクションの要素(エラー表示を行う)
 * @param  Array  setActionList シナリオ設定のアクション一覧
 * @param  Object actionItem    チェック対象のオブジェクト
 * @return Void
 */
function actionValidationCheck(element, setActionList, actionItem) {
  var messageList = [];
  var storedVariableList = <?php echo json_encode($storedVariableList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;

  if (actionItem.actionType == <?= C_SCENARIO_ACTION_TEXT ?>) {
    if (!actionItem.message) {
      messageList.push('発言内容が未入力です');
    }
    if (actionItem.message) {
      if(actionItem.message.length > 4000){
        messageList.push('発言内容は4000文字以内で入力してください');
      }
    }

  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
    var invalidVariables = actionItem.hearings.some(function(obj) {
      return !obj.variableName || !obj.message;
    });
    if (invalidVariables) {
      messageList.push('変数名と質問内容が未入力です');
    }

    // valid date error mesage
    var hasBlankErrMess = false;
    var hasInvalidLengthErrMess = false;
    angular.forEach(actionItem.hearings, function (item, itemKey) {
      if ((item.uiType == 1 || item.uiType == 2) && item.inputType != 1) {
        if (!item.errorMessage) {
          hasBlankErrMess = true;
        }

        if (item.errorMessage && item.errorMessage.length > 4000) {
          hasInvalidLengthErrMess = true;
        }
      }
    });
    if (hasBlankErrMess) {
      messageList.push('入力エラー時の返信メッセージが未入力です');
    }

    if (hasInvalidLengthErrMess) {
      messageList.push('入力エラー時の返信メッセージは4000文字以内で入力してください');
    }


    // if (!actionItem.errorMessage) {
    //   messageList.push('入力エラー時の返信メッセージが未入力です');
    // }

    // if (actionItem.errorMessage && actionItem.errorMessage.length > 4000) {
    //     messageList.push('入力エラー時の返信メッセージは4000文字以内で入力してください');
    // }

    if (actionItem.isConfirm) {
      if (!actionItem.confirmMessage) {
        messageList.push('確認内容のメッセージが未入力です');
      }
      if (!actionItem.success) {
        messageList.push('選択肢（OK）が未入力です');
      }
      if (!actionItem.cancel) {
        messageList.push('選択肢（NG）が未入力です');
      }
    }

  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
    if (!actionItem.selection.variableName) {
      messageList.push('変数名が未入力です');
    }

    if (!actionItem.message) {
      messageList.push('質問内容が未入力です');
    }

    if (actionItem.message  && actionItem.message.length > 4000) {
        messageList.push('質問内容は4000文字以内で入力してください');
    }

    var validOptions = actionItem.selection.options.some(function(obj) {
      return !!obj;
    });
    if (!validOptions) {
      messageList.push('選択肢が未入力です');
    }

  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
    var validAddress = actionItem.toAddress.some(function(obj) {
      return !!obj;
    });
    if (!validAddress) {
      messageList.push('送信先メールアドレスが未入力です');
    }

    if (!actionItem.fromName) {
      messageList.push('差出人名が未入力です');
    }

    if (!actionItem.subject) {
      messageList.push('メールタイトルが未入力です');
    }

    if (actionItem.mailType == <?= C_SCENARIO_MAIL_TYPE_CUSTOMIZE ?> && !actionItem.template) {
      messageList.push('メール本文が未入力です');
    }

  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?>) {
    if (actionItem.scenarioId === '' || actionItem.scenarioId === null) {
      messageList.push('シナリオを選択してください');
    }

  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?>) {
    actionItem.getAttributes.some(function(elm){
      var found = false;
      if (!elm.variableName) {
        found = true;
        messageList.push('変数名が未入力です');
      }
      if (!elm.attributeValue) {
        found = true;
        messageList.push('CSSセレクタが未入力です');
      }
      if(found) {
        return true;
      }
    });
  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
    if (!actionItem.url && actionItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_API ?>) {
      messageList.push('連携先URLが未入力です');
    }else if(!actionItem.externalScript && actionItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_SCRIPT ?>){
      messageList.push('スクリプトが未入力です');
    }else if(actionItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_SCRIPT ?> && searchStr(actionItem.externalScript,/<.*script.*>/)){
      messageList.push('scriptタグの設定は不要です');
    }
  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_SEND_FILE ?>) {
    if (!actionItem.tChatbotScenarioSendFileId || !actionItem.file || !actionItem.file.download_url || !actionItem.file.file_size) {
      messageList.push('ファイルが未選択です');
    }
    if (!actionItem.message) {
      messageList.push('発言内容が未入力です');
    }else if(actionItem.message.length > 4000){
      messageList.push('発言内容は4000文字以内で入力してください');
    }
  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_RECEIVE_FILE ?>) {
    if(!actionItem.dropAreaMessage) {
      messageList.push("見出し文が未入力です");
    }
    if (Number(actionItem.receiveFileType) === 2 && !actionItem.extendedReceiveFileExtensions) {
      messageList.push('拡張設定を選択した場合は拡張子の指定が必須です');
    }
    if (!actionItem.errorMessage) {
      messageList.push('ファイルエラー時の返信メッセージが未入力です');
    }else if(actionItem.errorMessage.length > 4000){
      messageList.push('ファイルエラー時の返信メッセージは4000文字以内で入力してください');
    }
    if (actionItem.cancelEnabled && !actionItem.cancelLabel) {
      messageList.push('キャンセルできるようにする場合は名称の入力が必須です');
    }
  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>) {
    if (!actionItem.referenceVariable) {
      messageList.push('参照する変数名が未入力です');
    }

    var validMatchValues = actionItem.conditionList.some(function(obj) {
      return !!obj.matchValue;
    });

    if (!validMatchValues) {
      messageList.push('条件が未入力です');
    }

    actionItem.conditionList.some(function(elm){
      if(Number(elm.actionType) === 1 && !elm.action.message) {
        messageList.push('アクションのメッセージが未入力です');
        return true;
      }else if(elm.action.message.length > 4000){
        messageList.push('アクションのメッセージは4000文字以内で入力してください');
      }
    });

    actionItem.conditionList.some(function(elm){
      if(Number(elm.actionType) === 2 && (!elm.action.callScenarioId || elm.action.callScenarioId === "")) {
        messageList.push('呼出先のシナリオを選択して下さい');
        return true;
      }
    });

    if(actionItem.elseEnabled) {
      if(Number(actionItem.elseAction.actionType) === 1 && !actionItem.elseAction.action.message) {
        messageList.push('アクションのメッセージが未入力です');
      }else if(actionItem.elseAction.action.message.length > 4000){
        messageList.push('アクションのメッセージは4000文字以内で入力してください');
      }
      if(Number(actionItem.elseAction.actionType) === 2 && (!actionItem.elseAction.action.callScenarioId || actionItem.elseAction.action.callScenarioId === "")) {
        messageList.push('呼出先のシナリオを選択して下さい');
      }
    }

  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION ?>) {
    actionItem.addCustomerInformations.some(function(elm){
      var found = false;
      if (!elm.variableName) {
        found = true;
        messageList.push('変数名が未入力です');
      }
      if (!elm.targetId) {
        found = true;
        messageList.push('訪問ユーザ情報の項目が未指定です');
      }
      if(found) {
        return true;
      }
    });
  }

  // 使用されている変数名を抽出する
  var setMessages = searchObj(actionItem, /^(?!\$).+$|^variableName$/i);
  var usedVariableList = setMessages.map(function(string) {
    return string.replace(/\n/mg, ' ');
  }).join(' ').match(/{{[^}]+}}/g);

  if (usedVariableList !== null && usedVariableList.length >= 1) {

    // 自分自身以前に設定されたアクションのみチェックする
    setActionList.every(function(action) {
      // 設定されている変数名を抽出する
      var definedVariableList = searchObj(action, /^variableName$/);
      definedVariableList = $.unique($.merge(definedVariableList, storedVariableList));
      // 使用していない変数名を取り出す
      usedVariableList = usedVariableList.filter(function(usedVariable) {
        var variableName = usedVariable.replace(/{{([^}]+)}}/, '$1');
        var isUsing = definedVariableList.some(function(string) {
          return string === variableName;
        });
        return !isUsing;
      });

      return actionItem !== action;
    });

    // 重複を排除して、エラーメッセージを追加する
    usedVariableList.filter(function(value, index, arr) {
      return arr.indexOf(value) == index;
    }).forEach(function(string) {
      var variableName = string.replace(/{{([^}]+)}}/, '$1');
      messageList.push('変数名 "' + variableName + '" が設定されていません');
    });
  }

  actionItem.$valid = messageList.length <= 0;

  // エラーメッセージをツールチップに設定
  if (!actionItem.$valid) {
    $('#submitBtn').removeClass('greenBtn').addClass('disOffgrayBtn disabled').prop('disabled', true);
    setTimeout(function() {
      var target = element.querySelector('h4 > a > i');
      target.dataset.tooltip = messageList.map(function(message) {
        return '● ' + message;
      }).join('\n');
    }, 0);
  } else {
    $('#submitBtn').removeClass('disOffgrayBtn disabled').addClass('greenBtn').prop('disabled', false);
  }
}

/**
 * オブジェクト内のプロパティを検索
 * （オブジェクトのキーが正規表現にマッチした、すべての値を返す）
 * @param  Object obj   検索対象のオブジェクト
 * @param  RegExp regex 正規表現
 * @return Array        検索結果
 */
function searchObj (obj, regex) {
  var resultList = [];
  for (var key in obj) {
    if (typeof obj[key] === 'object') {
      resultList = resultList.concat(searchObj(obj[key], regex));
    }

    if (typeof obj[key] === 'string' && obj[key].length >= 1 && regex.test(key)) {
      resultList.push(obj[key]);
    }
  }
  return resultList;
}

/**
 * 文字列の正規表現を行う
 * @param  String str   検索対象の文字列
 * @param  RegExp regex 正規表現
 * @return boolean      検索結果(あり、か、なし)
 **/
function searchStr (str, regex) {
  var result = false;
  if(str.match(regex)){
    result = true;
  }
  return result;
}

// check when click on other area of action menu
$(document).mouseup(function (e) {
  // hide previous option menu when click new action
  // hide dropdown when click on other area
  if (!$(e.target).closest('.actionMenu a').length) {
    $('.actionMenuOption').fadeOut('fast');
  }

  if (!$(e.target).closest('.set_action_item').length && !$(e.target).closest('.actionMenu').length && !$(e.target).closest('#tchatbotscenario_form_preview_body > section').length) {
    angular.element($('#tchatbotscenario_form_action_menulist')).scope().focusActionIndex = null;
    $('.set_action_item').blur();
  }
});
</script>
