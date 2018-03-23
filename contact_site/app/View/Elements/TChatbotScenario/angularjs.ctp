<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate', 'ui.sortable']);

sincloApp.controller('MainController', ['$scope', '$timeout', 'SimulatorService', function($scope, $timeout, SimulatorService) {
  //thisを変数にいれておく
  var self = this;

  $scope.actionList = <?php echo json_encode($chatbotScenarioActionList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.changeFlg = false;

  // アクション設定の取得・初期化
  $scope.setActionList = [];
  $scope.targetDeleteFileIds = [];
  var setActivity = <?= !empty($this->data['TChatbotScenario']['activity']) ? json_encode($this->data['TChatbotScenario']['activity'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : "{}" ?>;
  if (typeof setActivity  === "string") {
    var jsonData = JSON.parse(setActivity);
    var setActionListTmp = jsonData.scenarios;
    for (var key in setActionListTmp) {
      if (setActionListTmp.hasOwnProperty(key)) {
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

  $scope.inputTypeList = <?php echo json_encode($chatbotScenarioInputType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.sendMailTypeList = <?php echo json_encode($chatbotScenarioSendMailType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.apiMethodType = <?php echo json_encode($chatbotScenarioApiMethodType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.apiResponseType = <?php echo json_encode($chatbotScenarioApiResponseType, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  $scope.widget = SimulatorService;
  $scope.widget.settings = getWidgetSettings();

  // 一時保存データのキー生成
  var scenarioId = document.getElementById('TChatbotScenarioId').value || 'tmp';
  $scope.storageKey = 'scenario_' + scenarioId;

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
      var elms = event.target.querySelectorAll('li.set_action_item');
      $scope.setActionList.forEach(function(actionItem, index) {
        actionValidationCheck(elms[index], $scope.setActionList, actionItem);
      });
    }
  };

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

      // 表示位置調整
      $timeout(function() {
        var actionBox = $('#tchatbotscenario_form_preview_body');
        var previewBox = $('#tchatbotscenario_form_action_body');

        var time = 500
        actionBox.stop().animate({
          scrollTop: actionBox.scrollTop() + actionBox[0].scrollHeight
        }, time);
        previewBox.stop().animate({
          scrollTop: previewBox.scrollTop() + previewBox[0].scrollHeight
        }, time);

        // フォーカス移動
        var target = $('#tchatbotscenario_form_action_body .set_action_item:last-of-type');
        target.find('input, textarea, select')[0].focus();
      }, 0);
    }
  };

  // アクションの削除
  this.removeItem = function(setActionId) {
    var actionDetail = $scope.setActionList[setActionId];
    if (typeof actionDetail.tChatbotScenarioSendFileId !== 'undefined' || actionDetail.tChatbotScenarioSendFileId !== null ) {
      $scope.targetDeleteFileIds.push(actionDetail.tChatbotScenarioSendFileId);
    }

    $scope.setActionList.splice(setActionId, 1);

    // localStorageに一時保存を行う
    localStorage.setItem($scope.storageKey, self.createJsonData(false));
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

  /**
   * uploadFile ファイルアップロード
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

      // localStorageに一時保存を行う
      localStorage.setItem($scope.storageKey, self.createJsonData(false));
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
    $scope.$broadcast('openSimulator', this.createJsonData(true));
    // シミュレータ起動時、強制的に自由入力エリアを有効の状態で表示する
    $scope.$broadcast('switchSimulatorChatTextArea', true);
  };

  this.saveAct = function() {
    var validatedActivity = this.createJsonData(true);

    // localStorageから一時保存データを削除する
    localStorage.removeItem($scope.storageKey);

    // アラート表示を行わないように、フラグを戻す
    $scope.changeFlg = false;

    $('#TChatbotScenarioActivity').val(validatedActivity);
    submitAct();
  };

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
          // 不要な一時保存データを削除する
          localStorage.removeItem($scope.storageKey);

          // 一覧ページへ遷移する
          var url = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
          location.href = url + "/page:" + lastPage;
        }
      });
    };
  };

  /**
   * addActionItemList
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
      var target = $scope.setActionList[actionStep].hearings;
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
    }
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
      selector = '#action' + actionStep + '_setting .itemListGroup tr:nth-child(2n+2)';
    } else if (actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
      targetObjList = $scope.setActionList[actionStep].selection.options;
      selector = '#action' + actionStep + '_setting .itemListGroup li';
    } else if (actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>) {
      targetObjList = $scope.setActionList[actionStep].toAddress;
      selector = '#action' + actionStep + '_setting .itemListGroup li';
      limitNum = 5;
    } else if (actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
      if (/externalApiRequestHeader/.test(targetClassName)) {
        targetObjList = $scope.setActionList[actionStep].requestHeaders;
        selector = '#action' + actionStep + '_setting .itemListGroup.externalApiRequestHeader tr';
      } else {
        targetObjList = $scope.setActionList[actionStep].responseBodyMaps;
        selector = '#action' + actionStep + '_setting .itemListGroup.externalApiResponseBody tr';
      }
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
  this.removeFile = function($event) {
    var targetActionId = $($event.target).parents('.set_action_item')[0].id;
    var actionStep = targetActionId.replace(/action([0-9]+)_setting/, '$1');

    // ファイルIDの削除リストへ追加
    $scope.targetDeleteFileIds.push($scope.setActionList[actionStep].tChatbotScenarioSendFileId);

    $scope.setActionList[actionStep].tChatbotScenarioSendFileId = null;
    $scope.setActionList[actionStep].file = null;

    // localStorageに一時保存を行う
    localStorage.setItem($scope.storageKey, self.createJsonData(false));
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
    if ($scope.targetDeleteFileIds.length >= 1) {
      activity.targetDeleteFileIds = $scope.targetDeleteFileIds;
    }

    return JSON.stringify(activity);
  };

  /**
   * trimDataText
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
   * trimDataHearing
   * ヒアリングのバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataHearing = function(action) {
    if (typeof action.hearings === 'undefined' || typeof action.hearings.length < 1 ||
      typeof action.errorMessage === 'undefined' || action.errorMessage === '' ||
      (action.isConfirm && (typeof action.confirmMessage === 'undefined' || action.confirmMessage === '' || typeof action.success === 'undefined' || action.success === '' || typeof action.cancel === 'undefined' || action.cancel === ''))
    ) {
      return null;
    }

    var hearings = [];
    angular.forEach(action.hearings, function(item, index) {
      if (typeof item.variableName !== 'undefined' && item.variableName !== '' && typeof item.message !== 'undefined' && item.message !== '') {
        item.inputLFType = item.inputLFType == 1 ? '1' : '2';
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
   * trimDataSelectOption
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
   * trimDataSendMail
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
   * trimDataCallScenario
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
   * trimDataExternalApi
   * 外部システム連携のバリデーションチェックを行い、保存可能なデータを返す
   * @param Object  action  アクションの詳細
   */
  this.trimDataExternalApi = function(action) {
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

    return action;
  }

  /**
   * trimDataSendFile
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
      var targetElmList = $('#action' + actionStep + '_setting').find('.itemListGroup tr:nth-child(2n+2)');
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

  /**
   * controllListView
   * 選択肢、ヒアリング、メール送信のリストに対して、追加・削除ボタンの表示状態を更新する
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
        $(targetElm).find('.btnBlock .deleteBtn').hide();
      } else if (actionType == <?= C_SCENARIO_ACTION_HEARING ?> || actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
        // リストが複数件ある場合、ヒアリング・選択肢アクションは、追加・削除ボタンを表示する
        $(targetElm).find('.btnBlock .disOffgreenBtn').show();
        $(targetElm).find('.btnBlock .deleteBtn').show();
      } else if (index == elmNum -1 && index != limitNum-1) {
        // リストの最後の一件の場合、追加・削除ボタンを表示する
        $(targetElm).find('.btnBlock .disOffgreenBtn').show();
        $(targetElm).find('.btnBlock .deleteBtn').show();
      } else {
        // 削除ボタンのみ表示する
        $(targetElm).find('.btnBlock .disOffgreenBtn').hide();
        $(targetElm).find('.btnBlock .deleteBtn').show();
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

  angular.element(window).on('load', function(e) {
    var storageData = localStorage.getItem($scope.storageKey);
    var jsonData = JSON.parse(storageData);

    if (typeof storageData !== 'undefined' && storageData !== null && storageData !== '') {
      if (window.confirm('一時保存されたシナリオがあります。\nデータを復旧しますか？')) {
        // シナリオ設定の一時保存データの復旧
        $scope.setActionList = [];
        angular.forEach(jsonData.scenarios, function(action) {
          $scope.setActionList.push(action);
        });
        // ファイルIDの削除リストを取得
        if (typeof jsonData.targetDeleteFileIds !== 'undefined' && jsonData.targetDeleteFileIds !== null) {
          $scope.targetDeleteFileIds = jsonData.targetDeleteFileIds;
        }

        $scope.$apply();
      } else {
        // ファイルIDの削除リストを取得
        $scope.targetDeleteFileIds = jsonData.targetDeleteFileIds;
        angular.forEach(jsonData.scenarios, function(action) {
          if (action.actionType == <?= C_SCENARIO_ACTION_SEND_FILE ?>) {
            $scope.targetDeleteFileIds.push(action.tChatbotScenarioSendFileId);
          }
        });

        // ファイルIDの削除リストが存在する場合、現在のシナリオ設定で一時保存データを上書きする
        if ($scope.targetDeleteFileIds.length >= 1) {
          localStorage.setItem($scope.storageKey, self.createJsonData(false));
        } else {
          localStorage.removeItem($scope.storageKey);
        }
      }
    } else {
      // ファイルIDの削除リストが存在する場合、現在のシナリオ設定で一時保存データを上書きする
      if ($scope.targetDeleteFileIds.length >= 1) {
        localStorage.setItem($scope.storageKey, self.createJsonData(false));
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

    // フォームからフォーカスが外れた際、localStorageに一時保存を行う
    $(document).on('focusout', '.set_action_item input, .set_action_item textarea', function() {
      if ($scope.changeFlg) {
        var data = self.createJsonData(false);
        localStorage.setItem($scope.storageKey, data);
      }
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
  $scope.widget.settings = getWidgetSettings();

  /**
   * シミュレーションの起動(ダイアログ表示)
   * @param Object activity 実行可能なシナリオ
   */
  $scope.$on('openSimulator', function(event, activity) {
    var scenarios = JSON.parse(activity).scenarios;
    $scope.setActionList = scenarios;
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
    $scope.doAction();
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

  /**
   * doAction
   * アクションの実行
   * @param String setTime 基本設定のメッセージ間隔に関わらず、メッセージ間隔を指定
   */
  $scope.doAction = function(setTime) {
    if (typeof $scope.setActionList[$scope.actionStep] !== 'undefined' && typeof $scope.setActionList[$scope.actionStep].actionType !== 'undefined') {
      var actionDetail = $scope.setActionList[$scope.actionStep];

      // メッセージ間隔
      var time =  actionDetail.messageIntervalTimeSec;
      if (!!setTime || ($scope.actionStep === 0 && $scope.hearingIndex === 0) || actionDetail.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?> ||  actionDetail.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?> || actionDetail.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
        time = setTime || '0';
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
          $scope.$broadcast('addReFileMessage', actionDetail.file);
          $scope.actionStep++;
          $scope.doAction();
        }
      }, parseInt(time, 10) * 1000);
    } else {
      $scope.actionStop();
    }
  }

  /**
   * ヒアリングアクションの実行
   * @param Object actionDetail アクションの詳細
   */
  $scope.doHearingAction = function(actionDetail) {
    if (!$scope.hearingInputResult) {
      // エラーメッセージ
      var message = actionDetail.errorMessage;
      $scope.$broadcast('addReMessage', $scope.replaceVariable(message), 'action' + $scope.actionStep);
      $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
      $scope.hearingInputResult = true;
      $scope.doAction();
    } else
    if ($scope.hearingIndex < actionDetail.hearings.length) {
      var hearingDetail = actionDetail.hearings[$scope.hearingIndex];
      // 質問する
      var message = hearingDetail.message;
      $scope.$broadcast('addReMessage', $scope.replaceVariable(message), 'action' + $scope.actionStep);
      // 改行設定を元に、シミュレーターの設定変更
      $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1');
      if (hearingDetail.inputLFType == <?= C_SCENARIO_INPUT_LF_TYPE_ALLOW ?>) {
        $scope.$broadcast('allowSendMessageByShiftEnter', true);
      } else {
        $scope.$broadcast('allowInputLF', false);
      }
      var strInputRule =$scope.inputTypeList[hearingDetail.inputType].inputRule;
      $scope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, "$1"));
    } else
    if (actionDetail.isConfirm && ($scope.hearingIndex === actionDetail.hearings.length)) {
      // 確認メッセージ
      var messageList = [actionDetail.confirmMessage, '[] ' + actionDetail.success, '[] ' + actionDetail.cancel];
      var message = messageList.filter( function(string) {
        return string !== '';
      }).join('\n');

      $scope.$broadcast('addReMessage', $scope.replaceVariable(message), 'action' + $scope.actionStep);
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
      return LocalStorageService.getItem(name) || name;
    });
  }

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
        var scenarios = {};
        var idx = 0;
        var activity = JSON.parse(data['TChatbotScenario']['activity']);

        // 取得したシナリオのアクション情報を、setActionList内に詰める
        Object.entries($scope.setActionList).every(function(param, key) {
          if (key == $scope.actionStep) {
            for (var exKey in activity.scenarios) {
              scenarios[idx++] = activity.scenarios[exKey];
            }
            // 取得したシナリオを追加後、フラグを見て続けるか判断する
            if (!isNext || isNext != '1') {
              return false;
            }
          } else {
            scenarios[idx++] = $scope.setActionList[key];
          }
          return true;
        });
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
      data.result.forEach(function(param) {
        LocalStorageService.setItem(param.variableName, param.value);
      });
    }).fail(function(error) {
      console.error('failed calling external api.', error.statusText);
    }).always(function() {
      $scope.actionStep++;
      $scope.doAction();
    });
  };
}])
.directive('resizeTextarea', function() {
  return {
    restrict: 'E',
    replace: true,
    template: '<textarea></textarea>',
    link: function(scope, element, attrs) {
      var elm = angular.element(element[0]);
      var defaultHeight = element[0].scrollHeight;
      var maxHeight = defaultHeight * 4;

      var clearWatch = scope.$watch(attrs.ngModel, function(value) {
        if (typeof value === 'undefined') {
          return;
        }
        var textHeight = elm[0].scrollHeight;
        if (textHeight > defaultHeight) {
          elm[0].style.height = (textHeight > maxHeight ? maxHeight : textHeight) + 'px';
        }
        clearWatch();
      });
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
    var targetObj = $('.explainTooltip');
    targetObj.find('icon-annotation .detail').html($(this).data('tooltip'));
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70) + 'px',
      left: $(this).offset().left - 70 + 'px'
    });
  }).off('mouseleave','.questionBtn').on('mouseleave','.questionBtn', function(event){
    $('.explainTooltip').find('icon-annotation').css('display','none');
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
      left: $(this).offset().left - 70 + 'px',
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
    target.find('input, textarea, select')[0].focus();
  });

  // フォーカスされたアクションに応じて、関連するプレビューを強調表示する
  $(document).on('focusin', '.set_action_item input, .set_action_item textarea', function() {
    var previewId = $(this).parents('.set_action_item').attr('id').replace(/setting$/, 'preview');
    $('.actionTitle').removeClass('active');
    $('#' + previewId + ' .actionTitle').addClass('active');
  }).on('focusout', '.set_action_item input, .set_action_item textarea', function() {
    var previewId = $(this).parents('.set_action_item').attr('id').replace(/setting$/, 'preview');
    $('#' + previewId + ' .actionTitle').removeClass('active');
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

// アクションのバリデーションとエラーメッセージの設定
function actionValidationCheck(element, setActionList, actionItem) {
  var messageList = [];

  if (actionItem.actionType == <?= C_SCENARIO_ACTION_TEXT ?>) {
    if (!actionItem.message) {
      messageList.push('発言内容が未入力です');
    }

  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
    var validVariables = actionItem.hearings.some(function(obj) {
      return !!obj.variableName && !!obj.message;
    });
    if (!validVariables) {
      messageList.push('変数名と質問内容が未入力です')
    }

    if (!actionItem.errorMessage) {
      messageList.push('入力エラー時の返信メッセージが未入力です')
    }

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

    var validOptions = actionItem.selection.options.some(function(obj) {
      return !!obj;
    });
    if (!validOptions) {
      messageList.push('選択肢が未入力です')
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
      messageList.push('メールタイトルが未入力です');
    }

    if (!actionItem.subject) {
      messageList.push('差出人名が未入力です');
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
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>) {
    if (!actionItem.url) {
      messageList.push('連携先URLが未入力です');
    }

    var validResponseBody = actionItem.responseBodyMaps.some(function(obj) {
      return !!obj.variableName && !!obj.sourceKey;
    });
    if (!validResponseBody) {
      messageList.push('レスポンスボディ情報が未入力です')
    }
  } else
  if (actionItem.actionType == <?= C_SCENARIO_ACTION_SEND_FILE ?>) {
    if (!actionItem.tChatbotScenarioSendFileId && !actionItem.file && !actionItem.file.download_url && !actionItem.file.file_size) {
      messageList.push('ファイルが未選択です');
    }
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

    usedVariableList.forEach(function(string) {
      var variableName = string.replace(/{{([^}]+)}}/, '$1');
      messageList.push('変数名 "' + variableName + '" がこのアクションより前に設定されていません');
    });
  }

  actionItem.$valid = messageList.length <= 0;

  // エラーメッセージをツールチップに設定
  if (!actionItem.$valid) {
    setTimeout(function() {
      var target = element.querySelector('h4 > a > i');
      target.dataset.tooltip = messageList.map(function(message) {
        return '● ' + message;
      }).join('\n');
    }, 0);
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

    if (typeof obj[key] === 'string' && regex.test(key)) {
      resultList.push(obj[key]);
    }
  }
  return resultList;
}
</script>
