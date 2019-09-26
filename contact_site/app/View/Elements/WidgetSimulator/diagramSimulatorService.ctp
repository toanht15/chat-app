<script type="text/javascript">
  'use strict';

  sincloApp.factory('DiagramSimulatorService', ['$rootScope', '$timeout', function($rootScope, $timeout) {
    var self = this;

    self.actionListOrigin = null;
    self.beginNodeId = '';
    self.currentNodeId = '';
    self.receiveFileEventListener = null;
    self.firstActionFlg = true;
    self.callFirst = true;

    self.actionInit = function() {
      self.beginNodeId = '';
      self.currentNodeId = '';

      Object.keys(self.setActionList).some(function(uuid, index, arr){
        if(self.setActionList[uuid].type !== 'devs.Model') return false;
        var node = self.setActionList[uuid];
        if(node.attrs.nodeBasicInfo.nodeType === 'start') {
          self.beginNodeId = node.id;
          self.currentNodeId = node.attrs.nodeBasicInfo.nextNodeId;
          return true;
        }
      });
      // シミュレーション上のメッセージをクリアする
      $rootScope.$broadcast('removeMessage');
    };

    /**
     * アクションの実行
     * @param String setTime 基本設定のメッセージ間隔に関わらず、メッセージ間隔を指定
     */
    self.doAction = function() {
      if (true) {
        // メッセージ間隔
        var actionNode = self.findNodeById(self.currentNodeId);

        if(!actionNode) {
          chatbotTypingRemove();
          return;
        }

        var time = 2;
        if(self.callFirst
            || actionNode.attrs.nodeBasicInfo.nodeType === 'scenario'
            || actionNode.attrs.nodeBasicInfo.nodeType === 'jump'
            || actionNode.attrs.nodeBasicInfo.nodeType === 'link'
            || actionNode.attrs.nodeBasicInfo.nodeType === 'operator'
            || actionNode.attrs.nodeBasicInfo.nodeType === 'cv') {
          time = 0;
          self.callFirst = false;
        }

        chatBotTyping();

        $timeout.cancel(self.actionTimer);
        self.actionTimer = $timeout(function() {
          switch(actionNode.attrs.nodeBasicInfo.nodeType) {
            case 'branch': // 分岐
              self.doBranchAction(actionNode);
              break;
            case 'text': // テキスト発言
              self.doTextAction(actionNode);
              break;
            case 'scenario': // シナリオ呼び出し
              self.doCallScenarioAction(actionNode);
              break;
            case 'jump': // ジャンプ
              var nextNode = self.findNodeById(actionNode.attrs.actionParam.targetId);
              self.currentNodeId = nextNode.id;
              self.doAction();
              break;
            case 'link': // リンク
              var nextNode = self.findNodeById(actionNode.attrs.nodeBasicInfo.nextNodeId);
              self.currentNodeId = nextNode.id;
              self.doAction();
              break;
            case 'operator': // オペレータ呼び出し
              clearChatbotTypingTimer();
              chatbotTypingRemove();
              break;
            case 'cv': //CVポイント
              var nextNode = self.findNodeById(actionNode.attrs.nodeBasicInfo.nextNodeId);
              self.currentNodeId = nextNode.id;
              self.doAction();
              break;
          }
        }, time * 1000);
      } else {
        setTimeout(chatBotTypingRemove, 801);
        self.actionStop();
      }
    };

    // アクションの停止
    self.actionStop = function() {
      $timeout.cancel(self.actionTimer);
    };

    self.findNodeById = function(nodeId) {
      return self.setActionList[nodeId];
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

    /**
     * ヒアリングアクションの実行
     * @param Object actionDetail アクションの詳細
     */
    self.doBranchAction = function(node) {
      var nodeId = node.id;
      var buttonType = node.attrs.actionParam.btnType;
      var message = node.attrs.actionParam.text;
      var selections = self.getBranchSelection(node);
      var labels = self.getBranchLabels(node, Object.keys(selections));
      var customDesign = node.attrs.actionParam.customizeDesign;
      $rootScope.$broadcast('addReDiagramBranchMessage', nodeId, buttonType, message, selections, labels, customDesign);
    };

    self.doTextAction = function(node) {
      clearChatbotTypingTimer();
      chatBotTypingRemove();
      var nodeId = node.id;
      var messages = node.attrs.actionParam.text;
      var nextNodeId = node.attrs.nodeBasicInfo.nextNodeId;
      var intervalSec = 2;
      $rootScope.$broadcast('addReDiagramTextMessage', nodeId, messages, nextNodeId, intervalSec);
    };

    self.doCallScenarioAction = function(node) {
      <?php if($coreSettings[C_COMPANY_USE_CHATBOT_SCENARIO]): ?>
      $.ajax({
        url: "<?= $this->Html->url('/TChatbotScenario/remoteGetActionDetail') ?>",
        type: 'post',
        dataType: 'json',
        data: {
          id: node.attrs.actionParam.scenarioId
        },
        cache: false,
        timeout: 10000
      }).done(function(data) {
        console.info('successed get scenario detail.');
        var activity = JSON.parse(data['TChatbotScenario']['activity']);
        $rootScope.$broadcast('receiveScenario', activity);
        if(node.attrs.actionParam.callbackToDiagram) {
          self.afterScenarioNextNodeId = node.attrs.nodeBasicInfo.nextNodeId;
          self.finishProcessListener = $rootScope.$on('finishScenarioProcess', self.handleEndScenarioProcess);
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        // エラー情報を出力する
        console.warn('failed get scenario detail');
        console.error(errorThrown);
      });
      <?php else: ?>
      clearChatbotTypingTimer();
      chatBotTypingRemove();
      <?php endif; ?>
    };

    self.handleEndScenarioProcess = function (event) {
      if(self.afterScenarioNextNodeId) {
        if(self.finishProcessListener) {
          self.finishProcessListener();
        }
        var nextNode = self.findNodeById(self.afterScenarioNextNodeId);
        self.afterScenarioNextNodeId = null;
        self.currentNodeId = nextNode.id;
        self.doAction();
      }
    };

    $rootScope.$on('finishAddTextMessage', function(event, nextNodeId){
      var nextNode = self.findNodeById(nextNodeId);
      self.currentNodeId = nextNode.id;
      self.doAction();
    });

    self.getBranchSelection = function(node) {
      var itemIds = node.embeds;
      var map = {};
      var baseData = self.setActionList;
      for (var i = 0; i < itemIds.length; i++) {
        var targetNode = baseData[itemIds[i]];
        if(targetNode['type'] !== 'devs.Model' && targetNode['type'] !== 'basic.Rect') continue;
        console.log('baseData.id:%s itemId:%s baseData.attrs.nodeBasicInfo.nodeType: %s', targetNode['id'], itemIds[i], targetNode['attrs']['nodeBasicInfo']['nodeType']);
        if ((targetNode['attrs']['nodeBasicInfo']['nodeType'] === 'childPortNode'
            && targetNode['attrs']['nodeBasicInfo']['nextNodeId']
            && targetNode['attrs']['nodeBasicInfo']['nextNodeId'] !== '')
            || targetNode['attrs']['nodeBasicInfo']['nodeType'] === 'childTextNode') {
          map[itemIds[i]] = targetNode['attrs']['nodeBasicInfo']['nextNodeId'];
        }
      }
      return map;
    };

    self.getBranchLabels = function(node, idKeys) {
      var labels = node.attrs.actionParam.selection;
      var map = {};
      var baseData = self.setActionList;
      for (var i = 0; i < idKeys.length; i++) {
        if(idKeys[i] === undefined) break;
        var targetNode = baseData[idKeys[i]];
        if ((targetNode['attrs']['nodeBasicInfo']['nodeType'] === 'childPortNode' || targetNode['attrs']['nodeBasicInfo']['nodeType'] === 'childTextNode')
            && targetNode['attrs']['nodeBasicInfo']['tooltip']) {
          for(var j=0; j < labels.length; j++) {
            if(labels[j].uuid === targetNode.id || labels[j].value === targetNode['attrs']['nodeBasicInfo']['tooltip']) {
              labels[j]['uuid'] = idKeys[i];
              map[j] = labels[j];
              break;
            }
          }
        }
      }
      return map;
    };

    self.handleDiagramReselectionInput = function(message, type, nodeId) {
      $rootScope.$broadcast('addSeMessage', message,
          'anwer_' + type * '_' + nodeId);
    };

    self.hideTextarea = function() {
      $rootScope.$broadcast('switchSimulatorChatTextArea', false);
    };

    // handle radio button click
    $(document).on('change', '#chatTalk input[type="radio"]', function(e) {
      var prefix = $(this).attr('id').replace(/-sinclo-radio[0-9a-z-]+$/i, '');
      var message = $(this).val().replace(/^\s/, '');
      if($(e.target).data('nid') && $(e.target).data('nextNid')) {
        self.handleDiagramReselectionInput(message, 'branch', $(e.target).data('nid'));
        var nextNode = self.findNodeById($(e.target).data('nextNid'));
        self.currentNodeId = nextNode.id;
        self.doAction();
      }
    });

    $(document).on('click', '#chatTalk .sinclo-button-ui', function(e) {
      $(this).parent('div').find('.sinclo-button-ui').removeClass('selected');
      $(this).addClass('selected');
      var prefix = $(this).parents('div').attr('id').replace(/-sinclo-button[0-9a-z-]+$/i, '');
      var message = $(this).text().replace(/^\s/, '');

      if($(e.target).data('nid') && $(e.target).data('nextNid')) {
        self.handleDiagramReselectionInput(message, 'branch', $(e.target).data('nid'));
        var nextNode = self.findNodeById($(e.target).data('nextNid'));
        self.currentNodeId = nextNode.id;
        self.doAction();
      }
    });

    return self;
  }]);
</script>
