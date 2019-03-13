<script type="text/javascript">
  'use strict';

  sincloApp.factory('ScenarioSimulatorService', ['$rootScope', '$timeout', 'LocalStorageService', function($rootScope, $timeout, LocalStorageService) {
    var self = this;
    var setActionList = [];
    var widget = null;
    self.inputTypeList = <?php echo json_encode($chatbotScenarioInputType,
      JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
    // next hearing action
    $rootScope.$on('nextHearingAction', function() {
      self.setActionList[self.actionStep].hearings[self.hearingIndex].skipped = true;
      self.hearingIndex++;
      var actionDetail = self.setActionList[self.actionStep];
      if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
          !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
        self.hearingIndex = 0;
        self.disableHearingInput(self.actionStep);
        self.actionStep++;
      }
      self.doAction();
    });

    // シミュレーションで受け付けた受信メッセージ
    $rootScope.$on('receiveVistorMessage', function(event, message, prefix) {
      // 対応するアクションがない場合は何もしない
      if (typeof self.setActionList[self.actionStep] === 'undefined') {
        return;
      }

      if (self.setActionList[self.actionStep].actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
        var actionDetail = self.setActionList[self.actionStep];

        if (self.hearingIndex < actionDetail.hearings.length) {
          // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
          var inputType = actionDetail.hearings[self.hearingIndex].inputType;
          var regex = new RegExp(self.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, '$1'));
          var isMatched = message.split(/\r\n|\n/).every(function(string) {
            return string.length >= 1 ? regex.test(string) : true;
          });
          if (isMatched) {
            // 変数の格納
            var storageParam = [];
            LocalStorageService.setItem('chatbotVariables', [
              {
                key: actionDetail.hearings[self.hearingIndex].variableName,
                value: message
              }]);
            // 次のアクション
            self.hearingIndex++;
            if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
                !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
              self.hearingIndex = 0;
              self.disableHearingInput(self.actionStep);
              self.actionStep++;
            }
          } else {
            // 入力エラー
            self.hearingInputResult = false;
          }
        } else if (actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length) &&
            self.replaceVariable(actionDetail.cancel) === message) {
          // 最初から入力し直し
          self.hearingIndex = 0;
        } else {
          // 次のアクション
          self.hearingIndex++;
          if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
              !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
            self.hearingIndex = 0;
            self.disableHearingInput(self.actionStep);
            self.actionStep++;
          }
        }
        self.doAction();
      } else if (self.setActionList[self.actionStep].actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
        // 選択肢
        var storageParam = [];
        LocalStorageService.setItem('chatbotVariables', [
          {
            key: self.setActionList[self.actionStep].selection.variableName,
            value: message
          }]);
        self.actionStep++;
        self.doAction();
      } else if (self.setActionList[self.actionStep].actionType == <?= C_SCENARIO_ACTION_RECEIVE_FILE ?>) {
        self.actionStep++;
        self.doAction();
      } else if (self.setActionList[self.actionStep].actionType == <?= C_SCENARIO_ACTION_BULK_HEARING ?>) {
        chatBotTyping();
        $.post("<?=$this->Html->url(['controller' => 'CompanyData', 'action' => 'parseSignature'])?>",
            JSON.stringify({
              'accessToken': 'x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK',
              'targetText': message
            }), null, 'json').done(function(result) {
          setTimeout(function() {
            $rootScope.$broadcast('addReForm', {
              prefix: 'action' + self.actionStep + '_bulk-hearing',
              isConfirm: true,
              bulkHearings: self.setActionList[self.actionStep].multipleHearings,
              resultData: result
            });
            $rootScope.$broadcast('switchSimulatorChatTextArea', false);
            chatBotTypingRemove();
          }, parseInt(self.setActionList[self.actionStep].messageIntervalTimeSec, 10) * 1000);
        });
      }
    });

    $rootScope.$on('pressFormOK', function(event, message) {
      $('#chatTalk > div:last-child').fadeOut('fast').promise().then(function() {
        var saveValue = [];
        Object.keys(message).forEach(function(elm) {
          saveValue.push({
            key: elm,
            value: message[elm].value
          });
        });
        $rootScope.$broadcast('addReForm', {
          prefix: 'action' + self.actionStep + '_bulk-hearing',
          isConfirm: false,
          bulkHearings: self.setActionList[self.actionStep].multipleHearings,
          resultData: {data: message}
        });
        LocalStorageService.setItem('chatbotVariables', saveValue);
        self.actionStep++;
        self.doAction();
      });
    });

    $rootScope.$on('setRestoreStatus', function(event, actionIndex, hearingIndex, status) {
      self.setActionList[actionIndex].hearings[hearingIndex].canRestore = status;
    });

    self.addVisitorHearingMessage = function(message) {
      var actionDetail = self.setActionList[self.actionStep];

      if (self.hearingIndex < actionDetail.hearings.length) {
        var uiType = actionDetail.hearings[self.hearingIndex].uiType;

        if (uiType === '1' || uiType === '2') {
          // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
          var inputType = actionDetail.hearings[self.hearingIndex].inputType;
          var regex = new RegExp(self.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, '$1'));
          var isMatched = message.split(/\r\n|\n/).every(function(string) {
            return string.length >= 1 ? regex.test(string) : true;
          });
          if (isMatched) {
            LocalStorageService.setItem('chatbotVariables', [
              {
                key: actionDetail.hearings[self.hearingIndex].variableName,
                value: message
              }]);
            // 次のアクション
            self.hearingIndex++;
            if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
                !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
              self.hearingIndex = 0;
              self.actionStep++;
            }
          } else {
            // 入力エラー
            self.hearingInputResult = false;
          }
        } else {
          LocalStorageService.setItem('chatbotVariables', [
            {
              key: actionDetail.hearings[self.hearingIndex].variableName,
              value: message
            }]);
          // 次のアクション
          self.hearingIndex++;
          if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
              !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
            self.hearingIndex = 0;
            self.disableHearingInput(self.actionStep);
            self.actionStep++;
          }
        }
      } else if (actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length) &&
          self.replaceVariable(actionDetail.cancel) === message) {
        angular.forEach(actionDetail.hearings, function(hearing) {
          hearing.canRestore = true;
        });
        // 最初から入力し直し
        self.hearingIndex = 0;
      } else {
        // 次のアクション
        self.hearingIndex++;
        if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
            !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
          self.hearingIndex = 0;
          self.disableHearingInput(self.actionStep);
          self.actionStep++;
        }
      }
      self.doAction();
    };

    // handle hearing re-select
    self.reSelectionHearing = function(message, actionStep, hearingIndex) {
      self.hearingIndex = hearingIndex;
      self.actionStep = actionStep;
      var actionDetail = self.setActionList[actionStep];
      var uiType = actionDetail.hearings[hearingIndex].uiType;
      // テキストタイプ
      if (uiType === '1' || uiType === '2') {
        // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
        var inputType = actionDetail.hearings[hearingIndex].inputType;
        var regex = new RegExp(self.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, '$1'));
        var isMatched = message.split(/\r\n|\n/).every(function(string) {
          return string.length >= 1 ? regex.test(string) : true;
        });
        if (isMatched) {
          // 変数の格納
          LocalStorageService.setItem('chatbotVariables', [
            {
              key: actionDetail.hearings[hearingIndex].variableName,
              value: message
            }]);
          // 次のアクション
          self.hearingIndex++;
          if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
              !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
            self.hearingIndex = 0;
            self.disableHearingInput(self.actionStep);
            self.actionStep++;
          }
        } else {
          // 入力エラー
          self.hearingInputResult = false;
        }
      } else {
        // 変数の格納
        LocalStorageService.setItem('chatbotVariables', [
          {
            key: actionDetail.hearings[hearingIndex].variableName,
            value: message
          }]);
        // 次のアクション
        self.hearingIndex++;
        if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
            !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
          self.hearingIndex = 0;
          self.disableHearingInput(self.actionStep);
          self.actionStep++;
        }
      }

      self.doAction();
    };

    // シミュレーションの終了(ダイアログ非表示)
    self.closeSimulator = function() {
      self.actionStop();
      $('#tchatbotscenario_simulator_wrapper').hide();
    };

    // アクションの開始
    self.actionInit = function() {
      self.actionStep = 0;
      self.hearingIndex = 0;
      self.sendFileIndex = 0;
      self.firstActionFlg = true;
      self.actionTimer;
      self.hearingInputResult = true;

      // シミュレーション上のメッセージをクリアする
      $rootScope.$broadcast('removeMessage');
      self.doAction();
    };

    $rootScope.$watch('actionStep', function() {
      self.widget.setCurrentActionStep(self.actionStep);
    });

    $rootScope.$watch('hearingIndex', function() {
      self.widget.setCurrentHearingIndex(self.hearingIndex);
    });

    // アクションの停止
    self.actionStop = function() {
      $timeout.cancel(self.simulatorTimer);
    };

    // アクションのクリア(アクションを最初から実行し直す)
    self.actionClear = function() {
      self.actionStop();
      self.actionInit();
      self.setActionList = self.actionListOrigin;
    };

    /**
     * アクションの実行
     * @param String setTime 基本設定のメッセージ間隔に関わらず、メッセージ間隔を指定
     */
    self.receiveFileEventListener = null;
    self.firstActionFlg = true;
    self.doAction = function(setTime) {
      if (typeof self.setActionList[self.actionStep] !== 'undefined' &&
          typeof self.setActionList[self.actionStep].actionType !== 'undefined') {
        var actionDetail = self.setActionList[self.actionStep];
        // メッセージ間隔
        var time = parseInt(actionDetail.messageIntervalTimeSec, 10) * 1000;
        console.log(self.actionStep);
        var branchOnConditon = false;

        //条件分岐の場合は複雑な時間指定が必要になるので括りだしておく
        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>) {
          branchOnConditon = true;
          var value = LocalStorageService.getItem('chatbotVariables', actionDetail.referenceVariable);
          for (var i = 0; i < actionDetail.conditionList.length; i++) {
            if (self.isMatch(value, actionDetail.conditionList[i])) {
              if (actionDetail.conditionList[i].actionType == '1') {
                if (self.actionStep !== 0) {
                  chatBotTyping();
                } else {
                  time = 0;
                }
                break;
              }
            } else if (actionDetail.elseEnabled) {
              if (actionDetail.elseAction.actionType == '1') {
                if (self.actionStep !== 0) {
                  chatBotTyping();
                } else {
                  time = 0;
                }
                break;
              }
            }
            time = setTime || '0';
          }
        }

        if (!branchOnConditon) {
          if (time == 0 || !!setTime ||
              (self.actionStep === 0 && self.hearingIndex === 0 && self.firstActionFlg) ||
              actionDetail.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?> ||
              actionDetail.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?> ||
              actionDetail.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?> ||
              actionDetail.actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?> ||
              actionDetail.actionType == <?= C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION ?> ||
              actionDetail.actionType == <?= C_SCENARIO_ACTION_LEAD_REGISTER ?> ||
              actionDetail.actionType == <?= C_SCENARIO_ACTION_CONTROL_VARIABLE ?>) {
            time = setTime || '0';
            self.firstActionFlg = false;
          } else {
            chatBotTyping();
          }
        }

        if (actionDetail.actionType == <?= C_SCENARIO_ACTION_BULK_HEARING ?> ) {
          chatBotTypingRemove();
          time = 850;
        }

        $timeout.cancel(self.actionTimer);
        self.actionTimer = $timeout(function() {
          if (actionDetail.actionType == <?= C_SCENARIO_ACTION_TEXT ?>) {
            // テキスト発言
            chatBotTypingRemove();
            $rootScope.$broadcast('addReMessage', self.replaceVariable(actionDetail.message),
                'action' + self.actionStep);
            $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
            self.actionStep++;
            self.doAction();
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
            // ヒアリング
            self.doHearingAction(actionDetail);
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
            // 選択肢
            var messageList = [self.replaceVariable(actionDetail.message)];
            angular.forEach(actionDetail.selection.options, function(option) {
              messageList.push('[] ' + option);
            });
            $rootScope.$broadcast('addReMessage', self.replaceVariable(messageList.join('\n')),
                'action' + self.actionStep);
            $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
            // メール送信
            $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
            self.actionStep++;
            self.doAction();
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?>) {
            self.getScenarioDetail(actionDetail.scenarioId, actionDetail.executeNextAction);
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
            self.callExternalApi(actionDetail);
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_SEND_FILE ?>) {
            // ファイル送信
            chatBotTypingRemove();
            if (self.sendFileIndex == 0 && !!actionDetail.message) {
              $rootScope.$broadcast('addReMessage', self.replaceVariable(actionDetail.message),
                  'action' + self.actionStep);
              self.sendFileIndex++;
              self.doAction();
            } else {
              $rootScope.$broadcast('addReFileMessage', actionDetail.file);
              self.sendFileIndex = 0;
              self.actionStep++;
              self.doAction();
            }
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?>) {
            // 属性値取得
            self.actionStep++;
            self.doAction();
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION ?>) {
            // 訪問ユーザ登録
            self.actionStep++;
            self.doAction();
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_RECEIVE_FILE ?>) {
            // ファイル受信
            chatBotTypingRemove();
            if (actionDetail.dropAreaMessage) {
              $rootScope.$broadcast('addSeReceiveFileUI', actionDetail.dropAreaMessage, actionDetail.cancelEnabled,
                  actionDetail.cancelLabel, actionDetail.receiveFileType, actionDetail.extendedReceiveFileExtensions);
              $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
              if (self.receiveFileEventListener) {
                self.receiveFileEventListener();
              }
              self.receiveFileEventListener = $rootScope.$on('onErrorSelectFile', function() {
                var message = actionDetail.errorMessage;
                $rootScope.$broadcast('addReErrorMessage', self.replaceVariable(message), 'action' + self.actionStep);
              });
            }
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>) {
            // 条件分岐
            chatBotTypingRemove();
            $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
            var value = LocalStorageService.getItem('chatbotVariables', actionDetail.referenceVariable);
            for (var i = 0; i < actionDetail.conditionList.length; i++) {
              if (self.isMatch(value, actionDetail.conditionList[i])) {
                self.doBranchOnCondAction(actionDetail.conditionList[i]);
                if (actionDetail.conditionList[i] == '1') {
                  chatBotTyping();
                }
                return;
              }
            }
            // どの条件にもマッチしなかった場合
            if (actionDetail.elseEnabled) {
              self.doBranchOnCondAction(actionDetail.elseAction);
            } else {
              self.actionStep++;
              self.doAction();
            }
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_BULK_HEARING ?>) {
            chatBotTypingRemove();
            self.doBulkHearingAction(actionDetail);
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_LEAD_REGISTER ?>) {
            self.actionStep++;
            self.doAction();
          } else if (actionDetail.actionType == <?= C_SCENARIO_ACTION_CONTROL_VARIABLE ?>) {
            self.doControlVariable(actionDetail);
          }
        }, time);
      } else {
        setTimeout(chatBotTypingRemove, 801);
        self.actionStop();
      }
    };

    self.isMatch = function(targetValue, condition) {
      switch (Number(condition.matchValueType)) {
        case 1: // いずれかを含む場合
          return self.matchCaseInclude(targetValue, self.splitMatchValue(condition.matchValue), condition.matchValuePattern);
        case 2: // いずれも含まない場合
          return self.matchCaseExclude(targetValue, self.splitMatchValue(condition.matchValue), condition.matchValuePattern);
        default:
          return false;
      }
    };

    self.doBranchOnCondAction = function(condition, callback) {
      switch (Number(condition.actionType)) {
        case 1:
          $rootScope.$broadcast('addReMessage', self.replaceVariable(condition.action.message),
              'action' + self.actionStep);
          self.actionStep++;
          self.doAction();
          break;
        case 2:
          // シナリオ呼び出し
          var targetScenarioId = condition.action.callScenarioId;
          console.log('targetScenarioId : %s', targetScenarioId);
          if (targetScenarioId === 'self') {
            var activity = {};
            activity.scenarios = self.actionListOrigin;
            self.setActionList = self.setCalledScenario(activity, condition.action.executeNextAction == 1);
            self.doAction();
          } else {
            self.getScenarioDetail(targetScenarioId, condition.action.executeNextAction == 1);
          }
          break;
        case 3:
          self.actionStop();
          // シナリオ終了
          break;
        case 4:
          self.actionStep++;
          self.doAction();
          // 何もしない（次のアクションへ）
          break;
      }
    };

    self.splitMatchValue = function(val) {
      var splitedArray = [];
      val.split('"').forEach(function(currentValue, index, array) {
        if (array.length > 1) {
          if (index !== 0 && index % 2 === 1) {
            // 偶数個：そのまま文字列で扱う
            if (currentValue !== '') {
              splitedArray.push(currentValue);
            }
          } else {
            if (currentValue) {
              var trimValue = currentValue.trim(),
                  splitValue = trimValue.replace(/　/g, ' ').split(' ');
              splitedArray = splitedArray.concat($.grep(splitValue, function(e) {
                return e !== '';
              }));
            }
          }
        } else {
          var trimValue = currentValue.trim(),
              splitValue = trimValue.replace(/　/g, ' ').split(' ');
          splitedArray = splitedArray.concat($.grep(splitValue, function(e) {
            return e !== '';
          }));
        }
      });
      return splitedArray;
    };

    self.matchCaseInclude = function(val, words, pattern) {
      console.log('_matchCaseInclude : %s <=> %s', words, val);
      var result = false;
      for (var i = 0; i < words.length; i++) {
        if (words[i] === '') {
          continue;
        }
        var word = words[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
        var preg;
        if (!pattern || pattern === '1') {
          // 完全一致
          preg = new RegExp('^' + word + '$');
        } else {
          // 部分一致
          preg = new RegExp(word);
        }

        result = preg.test(val);

        if (result) { // いずれかを含む
          break;
        }
      }
      return result;
    };

    self.matchCaseExclude = function(val, words, pattern) {
      for (var i = 0; i < words.length; i++) {
        if (words[i] === '') {
          if (words.length > 1 && i === words.length - 1) {
            break;
          }
          continue;
        } else {
          var word = words[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
          var preg;
          if (!pattern || pattern === '1') {
            // 完全一致
            preg = new RegExp('^' + word + '$');
          } else {
            // 部分一致
            preg = new RegExp(word);
          }
          var exclusionResult = preg.test(val);
          if (exclusionResult) {
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
    self.doHearingAction = function(actionDetail) {
      chatBotTypingRemove();
      if (!self.hearingInputResult) {
        // エラーメッセージ
        // var message = actionDetail.errorMessage;
        var errorMessage = actionDetail.hearings[self.hearingIndex].errorMessage;
        $rootScope.$broadcast('addReMessage', self.replaceVariable(errorMessage), 'action' + self.actionStep);
        $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
        self.hearingInputResult = true;
        self.doAction();
      } else if (self.hearingIndex < actionDetail.hearings.length) {
        var hearingDetail = actionDetail.hearings[self.hearingIndex];
        // 質問する
        var message = hearingDetail.message;
        var oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
        var isRestore = false;
        if (typeof hearingDetail.canRestore !== 'undefined' && hearingDetail.canRestore === false) {
          isRestore = false;
        } else {
          isRestore = actionDetail.restore && oldValue ? true : false;
        }

        // テキスト一形　＆　テキスト複数行
        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_ONE_ROW_TEXT ?>) {
          $rootScope.$broadcast('addReMessage', self.replaceVariable(message),
              'action' + self.actionStep + '_hearing' + self.hearingIndex);
          if (isRestore) {
            $('#miniSincloChatMessage').val(oldValue);
          }
          $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType,
              hearingDetail.required);
          $rootScope.$broadcast('allowInputLF', false, hearingDetail.inputType);
          var strInputRule = self.inputTypeList[hearingDetail.inputType].inputRule;
          $rootScope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, '$1'));
          $rootScope.$broadcast('enableHearingInputFlg');
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_MULTIPLE_ROW_TEXT ?>) {
          $rootScope.$broadcast('addReMessage', self.replaceVariable(message),
              'action' + self.actionStep + '_hearing' + self.hearingIndex);
          if (isRestore) {
            $('#sincloChatMessage').val(oldValue);
          }
          $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType,
              hearingDetail.required);
          $rootScope.$broadcast('allowSendMessageByShiftEnter', true, hearingDetail.inputType);
          var strInputRule = self.inputTypeList[hearingDetail.inputType].inputRule;
          $rootScope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, '$1'));
          $rootScope.$broadcast('enableHearingInputFlg');
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_RADIO_BUTTON ?>) {
          var data = {};
          data.options = hearingDetail.settings.options;
          data.design = hearingDetail.settings.customDesign;
          data.settings = hearingDetail.settings;
          data.prefix = 'action' + self.actionStep + '_hearing' + self.hearingIndex;
          data.message = self.replaceVariable(message);
          data.isRestore = isRestore;
          data.oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
          data.textColor = self.widget.settings.re_background_color;
          data.backgroundColor = self.widget.settings.re_text_color;

          $rootScope.$broadcast('addReRadio', data);
          $rootScope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType,
              hearingDetail.required);
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_PULLDOWN ?>) {
          var data = {};
          data.options = hearingDetail.settings.options;
          data.design = hearingDetail.settings.customDesign;
          data.prefix = 'action' + self.actionStep + '_hearing' + self.hearingIndex;
          data.message = self.replaceVariable(message);
          data.isRestore = isRestore;
          data.oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
          data.textColor = self.widget.settings.re_background_color;
          data.backgroundColor = self.widget.settings.re_text_color;

          $rootScope.$broadcast('addRePulldown', data);
          $rootScope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType,
              hearingDetail.required);
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_BUTTON ?>) {
          var data = {};
          data.settings = hearingDetail.settings;
          data.options = hearingDetail.settings.options;
          data.prefix = 'action' + self.actionStep + '_hearing' + self.hearingIndex;
          data.message = self.replaceVariable(message);
          data.isRestore = isRestore;
          data.oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);

          $rootScope.$broadcast('addReButton', data);
          $rootScope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType,
              hearingDetail.required);
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_CALENDAR ?>) {
          var data = {};
          data.settings = hearingDetail.settings;
          data.design = hearingDetail.settings.customDesign;
          data.prefix = 'action' + self.actionStep + '_hearing' + self.hearingIndex;
          data.message = self.replaceVariable(message);
          data.isRestore = isRestore;
          data.oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
          data.textColor = self.widget.settings.re_background_color;
          data.backgroundColor = self.widget.settings.re_text_color;

          $rootScope.$broadcast('addReCalendar', data);
          $rootScope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType,
              hearingDetail.required);
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_CAROUSEL ?>) {
          var data = {};
          data.images = hearingDetail.settings.images;
          data.design = hearingDetail.settings.customDesign;
          data.settings = hearingDetail.settings;
          data.prefix = 'action' + self.actionStep + '_hearing' + self.hearingIndex;
          data.message = self.replaceVariable(message);
          data.isRestore = isRestore;
          data.oldValue = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
          data.textColor = self.widget.settings.re_background_color;
          data.backgroundColor = self.widget.settings.re_text_color;

          $rootScope.$broadcast('addReCarousel', data);
          $rootScope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType,
              hearingDetail.required);
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_BUTTON_UI ?>) {
          var data       = {};
          data.settings  = hearingDetail.settings;
          data.options   = hearingDetail.settings.options;
          data.prefix    = 'action' + self.actionStep + '_hearing' + self.hearingIndex;
          data.message   = self.replaceVariable(message);
          data.isRestore = isRestore;
          data.oldValue  = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
          data.textColor = self.widget.settings.re_background_color;
          data.backgroundColor = self.widget.settings.re_text_color;

          $rootScope.$broadcast('addReButtonUI', data);
          $rootScope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType,
              hearingDetail.required);
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_CHECKBOX ?>) {
          var data       = {};
          data.settings  = hearingDetail.settings;
          data.options   = hearingDetail.settings.options;
          data.prefix    = 'action' + self.actionStep + '_hearing' + self.hearingIndex;
          data.message   = self.replaceVariable(message);
          data.isRestore = isRestore;
          data.oldValue  = LocalStorageService.getItem('chatbotVariables', hearingDetail.variableName);
          data.textColor = self.widget.settings.re_background_color;
          data.backgroundColor = self.widget.settings.re_text_color;

          $rootScope.$broadcast('addReCheckbox', data);
          $rootScope.$broadcast('switchSimulatorChatTextArea', !hearingDetail.required, hearingDetail.uiType,
              hearingDetail.required);
        }
        $rootScope.$emit('setRestoreStatus', self.actionStep, self.hearingIndex, true);
      } else if (actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length)) {
        // 確認メッセージ
        var messageList = [actionDetail.confirmMessage, '[] ' + actionDetail.success, '[] ' + actionDetail.cancel];
        var message = messageList.filter(function(string) {
          return string !== '';
        }).join('\n');

        $rootScope.$broadcast('addReMessage', self.replaceVariable(message), 'action' + self.actionStep + '_confirm');
        // 設定したOK/NG以外が入力されないよう、自由入力エリアを非表示とする
        $rootScope.$broadcast('switchSimulatorChatTextArea', false);
      } else {
        // disable radio, pulldown, underline text
        self.disableHearingInput(self.actionStep);
        // 次のアクションへ移行する
        self.hearingIndex = 0;
        self.actionStep++;
        self.doAction();
      }
    };

    self.doBulkHearingAction = function(actionDetail) {
      if (actionDetail.multipleHearings) {
        $rootScope.$broadcast('allowInputLF', true, '1');
        $rootScope.$broadcast('switchSimulatorChatTextArea', true);
        $rootScope.$broadcast('disableHearingInputFlg');
      }
    };

    /**
     * メッセージ内の変数を、ローカルストレージ内のデータと置き換える
     * @param String message 変数を含む文字列
     * @return String        置換後の文字列
     */
    self.replaceVariable = function(message) {
      message = message ? message : '';
      return message.replace(/{{(.+?)\}}/g, function(param) {
        var name = param.replace(/^{{(.+)}}$/, '$1');
        return LocalStorageService.getItem('chatbotVariables', name) || name;
      });
    };

    /**
     * メッセージ内の変数を、ローカルストレージ内のデータと置き換える、が、ない場合は空文字列を返す
     * @param String message 変数を含む文字列
     * @return String        置換後の文字列
     */
    self.replaceVariableWithEmpty = function(message) {
      message = message ? message : '';
      return message.replace(/{{(.+?)\}}/g, function(param) {
        var name = param.replace(/^{{(.+)}}$/, '$1');
        return LocalStorageService.getItem('chatbotVariables', name) || '';
      });
    };

    /**
     * メッセージ内の変数を、ローカルストレージ内のデータと置き換え、数字にする
     * @param String message 変数を含む文字列
     * @return String        置換後の文字列（数値）
     */
    self.replaceIntegerVariable = function(message) {
      message = message ? message : '';
      return message.replace(/{{(.+?)\}}/g, function(param) {
        var name = param.replace(/^{{(.+)}}$/, '$1');
        return Number(self.toHalfWidth(LocalStorageService.getItem('chatbotVariables', name))) || Number(name);
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
          var scenarios = self.setCalledScenario(activity, isNext);
          self.setActionList = scenarios;
        } catch (e) {
          self.actionStep++;
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        // エラー情報を出力する
        console.warn('failed get scenario detail');
        console.error(errorThrown);

        self.actionStep++;
      }).always(function() {
        // アクションを実行する
        self.doAction();
      });
    };

    self.setCalledScenario = function(activity, isNext) {
      var scenarios = {};
      var idx = 0;
      angular.forEach(self.setActionList, function(scenario, key) {
        if (key == self.actionStep) {
          for (var exKey in activity.scenarios) {
            scenarios[idx++] = activity.scenarios[exKey];
          }
        } else if (isNext == 1 || key <= self.actionStep) {
          scenarios[idx++] = self.setActionList[key];
        }
      });
      return scenarios;
    };

    /**
     * 計算・変数操作のアクション実行
     * @param Object actionDetail アクション詳細
     */
    this.doControlVariable = function(actionDetail) {
      actionDetail['calcRules'].forEach(function(calcRule) {
        try {
          var result = calcRule.formula;
          if (Number(calcRule.calcType) === <?= C_SCENARIO_CONTROL_INTEGER ?>) {
            result = self.toHalfWidth(self.replaceIntegerVariable(result));
            result = Number(eval(result));
            result = self.roundResult(result, calcRule.significantDigits, calcRule.rulesForRounding);
            if (isNaN(result)) {
              throw new Error('Not a Number');
            }
          } else {
            result = self.adjustString(self.replaceVariableWithEmpty(result));
          }
          LocalStorageService.setItem('chatbotVariables', [
            {
              key: calcRule.variableName,
              value: String(result)
            }]);
        } catch (e) {
          console.log(e);
          LocalStorageService.setItem('chatbotVariables', [
            {
              key: calcRule.variableName,
              value: '計算エラー'
            }]);
        }
      });
      self.actionStep++;
      self.doAction();
    };

    this.roundResult = function(value, digits, roundRule) {
      var index = Math.pow(10, digits - 1);
      // 1桁目指定の場合は整数部だけ取り出して計算
      if (Number(digits) === 0) {
        if (value > 0) {
          value = Math.floor(value);
        } else {
          value = Math.ceil(value);
        }
      }
      switch (Number(roundRule)) {
        case 1:
          //四捨五入の場合
          value = Math.round(value * index) / index;
          break;
        case 2:
          //切り捨ての場合
          value = Math.floor(value * index) / index;
          break;
        case 3:
          //切り上げの場合
          value = Math.ceil(value * index) / index;
          break;
        default:
          //デフォルトは四捨五入
          value = Math.round(value * index) / index;
      }

      return value;
    };
    this.adjustString = function(formula) {
      if (formula.indexOf('&') != -1) {
        var itemArray = formula.split('&');
        formula = '';
        itemArray.forEach(function(item) {
          formula += item;
        });
      }
      return formula;
    };

    /**
     *  全角⇒半角のキャスト
     *
     */
    this.toHalfWidth = function(formula) {
      var halfWidth = formula.replace(/[！-～]/g,
          function(tmpStr) {
            return String.fromCharCode(tmpStr.charCodeAt(0) - 0xFEE0);
          }
      );
      return halfWidth.replace(/”/g, '"').
          replace(/’/g, '\'').
          replace(/￥/g, '\\').
          replace(/　/g, ' ').
          replace(/～/g, '~');
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
          return {'name': self.replaceVariable(param.name), 'value': self.replaceVariable(param.value)};
        });
      }
      var sendData = {
        'url': encodeURI(self.replaceVariable(actionDetail.url)),
        'methodType': actionDetail.methodType,
        'requestHeaders': requestHeaders,
        'requestBody': self.replaceVariable(actionDetail.requestBody),
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
        self.actionStep++;
        self.doAction();
      });
    };

    // handle next button click
    $(document).on('click', '.nextBtn', function() {
      var numbers = $(this).attr('id').match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];

      var variable = self.setActionList[actionStep].hearings[hearingIndex].variableName;
      var message = LocalStorageService.getItem('chatbotVariables', variable);
      $rootScope.$broadcast('addSeMessage', self.replaceVariable(message),
          'action' + actionStep + '_hearing' + self.hearingIndex);
      $(this).hide();

      self.hearingIndex++;
      var actionDetail = self.setActionList[actionStep];
      if (typeof actionDetail.hearings[self.hearingIndex] === 'undefined' &&
          !(actionDetail.isConfirm === '1' && (self.hearingIndex === actionDetail.hearings.length))) {
        self.hearingIndex = 0;
        self.disableHearingInput(self.actionStep);
        self.actionStep++;
      }

      self.doAction();
    });

    // disable input after hearing finish
    this.disableHearingInput = function(actionIndex) {
      $rootScope.$broadcast('switchSimulatorChatTextArea', false);
      $('#sincloBox input[name*="action' + actionIndex + '"]').prop('disabled', true);
      $('#sincloBox select[id*="action' + actionIndex + '"]').prop('disabled', true);
      $('#sincloBox [id^="action' + actionIndex + '"][id*="underline"]').
          find('.sinclo-text-line').
          removeClass('underlineText');
      $('#sincloBox [id^="action' + actionIndex + '"][id*="calendar"]').addClass('disabledArea');
      $('#sincloBox [id^="action' + actionIndex + '"][id*="carousel"]').addClass('disabledArea');
      $('#sincloBox [id^="action' + actionIndex + '"] .sinclo-button').prop('disabled', true).css('background-color', '#DADADA');
      $('#sincloBox [id^="action' + actionIndex + '"] .sinclo-button-ui').prop('disabled', true).css('background-color', '#DADADA');
      $('#sincloBox [id^="action' + actionIndex + '"][id*="sinclo-checkbox"]').addClass('disabledArea');
      $('#sincloBox [id^="action' + actionIndex + '"][id$="next"]').hide();
      $rootScope.$broadcast('disableHearingInputFlg');
    };

    this.handleReselectionInput = function(message, actionStep, hearingIndex) {
      var variable = self.setActionList[actionStep].hearings[hearingIndex].variableName;
      var isRestore = self.setActionList[actionStep].restore;
      var item = LocalStorageService.getItem('chatbotVariables', variable);
      var skipped = self.setActionList[actionStep].hearings[hearingIndex].skipped;
      if (isRestore) {
        if (!item && !skipped) {
          // first time input
          $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
          self.addVisitorHearingMessage(message);
        } else if ((!item && skipped) ||
            (item && (self.setActionList[actionStep].hearings[hearingIndex].uiType === '7' || self.setActionList[actionStep].hearings[hearingIndex].uiType === '8' || self.setActionList[actionStep].hearings[hearingIndex].uiType === '9' || item !== message))) {
          $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
          $('#action' + actionStep + '_hearing' + hearingIndex + '_question').parent().nextAll('div').remove();
          self.reSelectionHearing(message, actionStep, hearingIndex);
        } else if (self.setActionList[actionStep].hearings[hearingIndex].uiType === '6') {
          $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
          $('#action' + actionStep + '_hearing' + hearingIndex + '_question').parent().nextAll('div').remove();
          self.reSelectionHearing(message, actionStep, hearingIndex);
        }

        if (self.setActionList[actionStep].hearings[hearingIndex].uiType === '9') {
          $rootScope.$broadcast('addCheckboxMessage', self.replaceVariable(message),
              'action' + actionStep + '_hearing' + self.hearingIndex, self.setActionList[actionStep].hearings[hearingIndex].settings.checkboxSeparator);
        } else {
          $rootScope.$broadcast('addSeMessage', self.replaceVariable(message),
              'action' + actionStep + '_hearing' + self.hearingIndex);
        }

      } else {
        if (!item && !skipped) {
          // first time input
          self.addVisitorHearingMessage(message);
        } else {
          $('#action' + actionStep + '_hearing' + hearingIndex + '_question').parent().nextAll('div').remove();
          self.reSelectionHearing(message, actionStep, hearingIndex);
        }
        if (self.setActionList[actionStep].hearings[hearingIndex].uiType === '9') {
          $rootScope.$broadcast('addCheckboxMessage', self.replaceVariable(message),
              'action' + actionStep + '_hearing' + self.hearingIndex,  self.setActionList[actionStep].hearings[hearingIndex].settings.checkboxSeparator);
        } else {
          $rootScope.$broadcast('addSeMessage', self.replaceVariable(message),
              'action' + actionStep + '_hearing' + self.hearingIndex);
        }
      }
    };

    // handle radio button click
    $(document).on('change', '#chatTalk input[type="radio"]', function() {
      var prefix = $(this).attr('id').replace(/-sinclo-radio[0-9a-z-]+$/i, '');
      var message = $(this).val().replace(/^\s/, '');
      var isConfirm = prefix.indexOf('confirm') !== -1 ? true : false;
      var name = $(this).attr('name');

      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      if (isConfirm) {
        // confirm message
        self.addVisitorHearingMessage(message);
        $rootScope.$broadcast('addSeMessage', self.replaceVariable(message),
            'action' + actionStep + '_hearing_confirm');
        $('input[name=' + name + '][type="radio"]').prop('disabled', true);
        // ラジオボタンを非活性にする
        self.disableHearingInput(self.actionStep);
        $('[id^="action' + actionStep + '_hearing"][id$="_question"]').removeAttr('id');
      } else {
        self.handleReselectionInput(message, actionStep, hearingIndex);
      }
    });

    // プルダウンの選択
    $(document).on('change', '#chatTalk select', function() {
      var prefix = $(this).attr('id').replace(/-sinclo-pulldown[0-9a-z-]+$/i, '');
      var message = $(this).val().replace(/^\s/, '');

      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];

      if (message !== '選択してください') {
        self.handleReselectionInput(message, actionStep, hearingIndex);
      } else {
        $(this).parents('.sinclo_re').find('.nextBtn').hide();
      }
    });

    $(document).on('click', '#chatTalk .carousel-container .thumbnail', function() {
      var prefix = $(this).attr('id');
      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      var imageIndex = numbers[2];
      var message = self.setActionList[actionStep].hearings[hearingIndex].settings.images[imageIndex].answer;
      self.handleReselectionInput(message, actionStep, hearingIndex);
    });

    // カレンダーの選択
    $(document).on('change', '#chatTalk .flatpickr-input', function() {
      var prefix = $(this).attr('id').replace(/-sinclo-datepicker[0-9a-z-]+$/i, '');
      var message = $(this).val().replace(/^\s/, '');

      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      self.handleReselectionInput(message, actionStep, hearingIndex);
    });

    // ボタンの選択
    $(document).on('click', '#chatTalk .sinclo-button', function() {
      $(this).parents('div.sinclo-button-wrap').find('.sinclo-button').removeClass('selected');
      $(this).addClass('selected');
      var prefix = $(this).parents('div.sinclo-button-wrap').attr('id').replace(/-sinclo-button[0-9a-z-]+$/i, '');
      var message = $(this).text().replace(/^\s/, '');

      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      self.handleReselectionInput(message, actionStep, hearingIndex);
    });

    $(document).on('click', '#chatTalk .sinclo-button-ui', function() {
      $(this).parent('div').find('.sinclo-button-ui').removeClass('selected');
      $(this).addClass('selected');
      var prefix = $(this).parents('div').attr('id').replace(/-sinclo-button[0-9a-z-]+$/i, '');
      var message = $(this).text().replace(/^\s/, '');

      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      self.handleReselectionInput(message, actionStep, hearingIndex);
    });
    // button ui
    $(document).on('click', '#chatTalk .sinclo-button-ui', function() {
      $(this).parent('div').find('.sinclo-button-ui').removeClass('selected');
      $(this).addClass('selected');
      var prefix = $(this).parents('div').attr('id').replace(/-sinclo-button[0-9a-z-]+$/i, '');
      var message = $(this).text().replace(/^\s/, '');

      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      self.handleReselectionInput(message, actionStep, hearingIndex);
    });

    $(document).on('click', '#chatTalk .checkbox-submit-btn', function() {
      $(this).addClass('disabledArea');
      var prefix = $(this).parents('div').attr('id').replace(/-sinclo-checkbox[0-9a-z-]+$/i, '');
      var message = [];
      $(this).parent('div').find('input:checked').each(function(e) {
        message.push($(this).val());
      });

      var separator = ',';
      switch (Number($(this).parents('div').attr('data-separator'))) {
        case 1:
          separator = ',';
          break;
        case 2:
          separator = '/';
          break;
        case 3:
          separator = '|';
          break;
        default:
          separator = ',';
          break;
      }

      message = message.join(separator);
      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      self.handleReselectionInput(message, actionStep, hearingIndex);
    });

    $(document).on('change', '#chatTalk input[type="checkbox"]', function() {
      if ($(this).is('checked')) {
        $(this).prop('checked', false);
      }

      if ($(this).parent().parent().find('input:checked').length > 0) {
        $(this).parent().parent().find('.checkbox-submit-btn').removeClass('disabledArea');
      } else {
        $(this).parent().parent().find('.checkbox-submit-btn').addClass('disabledArea')
      }
    });

    $(document).on('click', '#chatTalk .checkbox-submit-btn', function() {
      $(this).addClass('disabledArea');
      var prefix = $(this).parents('div').attr('id').replace(/-sinclo-checkbox[0-9a-z-]+$/i, '');
      var message = [];
      $(this).parent('div').find('input:checked').each(function(e) {
        message.push($(this).val());
      });

      var separator = ',';
      switch (Number($(this).parents('div').attr('data-separator'))) {
        case 1:
          separator = ',';
          break;
        case 2:
          separator = '/';
          break;
        case 3:
          separator = '|';
          break;
        default:
          separator = ',';
          break;
      }

      message = message.join(separator);
      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      self.handleReselectionInput(message, actionStep, hearingIndex);
    });

    $(document).on('change', '#chatTalk input[type="checkbox"]', function() {
      if ($(this).is('checked')) {
        $(this).prop('checked', false);
      }

      if ($(this).parent().parent().find('input:checked').length > 0) {
        $(this).parent().parent().find('.checkbox-submit-btn').removeClass('disabledArea');
      } else {
        $(this).parent().parent().find('.checkbox-submit-btn').addClass('disabledArea')
      }
    });

    // re-input text type
    $(document).on('click', '#chatTalk .underlineText', function() {
      var prefix = $(this).parents('.liBoxRight, .liRight').attr('id');
      var numbers = prefix.match(/\d+/g).map(Number);
      var actionStep = numbers[0];
      var hearingIndex = numbers[1];
      self.actionStep = actionStep;
      self.hearingIndex = hearingIndex;
      var actionDetail = self.setActionList[actionStep];
      var hearingDetail = self.setActionList[actionStep].hearings[hearingIndex];

      var variable = self.setActionList[actionStep].hearings[hearingIndex].variableName;
      var value = LocalStorageService.getItem('chatbotVariables', variable);
      $('#action' + actionStep + '_hearing' + hearingIndex + '_question').parent().nextAll('div').remove();

      if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_ONE_ROW_TEXT ?>) {
        $('#miniSincloChatMessage').val(value);
        $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType,
            hearingDetail.required);
        $rootScope.$broadcast('allowInputLF', false, hearingDetail.inputType);
        var strInputRule = self.inputTypeList[hearingDetail.inputType].inputRule;
        $rootScope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, '$1'));
      }

      if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_MULTIPLE_ROW_TEXT ?>) {
        $('#sincloChatMessage').val(value);
        $rootScope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType,
            hearingDetail.required);
        $rootScope.$broadcast('allowSendMessageByShiftEnter', true, hearingDetail.inputType);
        var strInputRule = self.inputTypeList[hearingDetail.inputType].inputRule;
        $rootScope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, '$1'));
      }
    });

    return self;
  }]);
</script>
