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

      for(var i=0; i < self.setActionList.cells.length; i++) {
        if(self.setActionList.cells[i].type !== 'devs.Model') continue;
        var node = self.setActionList.cells[i];
        if(node.attrs.nodeBasicInfo.nodeType === 'start') {
          self.beginNodeId = node.id;
          self.currentNodeId = node.attrs.nodeBasicInfo.nextNodeId;
        }
        break;
      }
      // シミュレーション上のメッセージをクリアする
      $rootScope.$broadcast('removeMessage');
    };

    /**
     * アクションの実行
     * @param String setTime 基本設定のメッセージ間隔に関わらず、メッセージ間隔を指定
     */
    self.doAction = function() {
      debugger;
      if (true) {
        // メッセージ間隔
        var actionNode = self.findNodeById(self.currentNodeId);

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
      var targetNode = {};
      Object.keys(self.setActionList.cells).some(function(idx, arrIdx, arr){
        var node = self.setActionList.cells[idx];
        if(node.id.indexOf(nodeId) !== -1) {
          targetNode = node;
          return true;
        }
      });
      return targetNode;
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
      }).fail(function(jqXHR, textStatus, errorThrown) {
        // エラー情報を出力する
        console.warn('failed get scenario detail');
        console.error(errorThrown);
      });
    };

    $rootScope.$on('finishAddTextMessage', function(event, nextNodeId){
      var nextNode = self.findNodeById(nextNodeId);
      self.currentNodeId = nextNode.id;
      self.doAction();
    });

    self.getBranchSelection = function(node) {
      var itemIds = node.embeds;
      var map = {};
      var baseData = self.setActionList.cells;
      for (var i = 0; i < itemIds.length; i++) {
        for (var nodeIndex = 0; nodeIndex <
        baseData.length; nodeIndex++) {
          if(baseData[nodeIndex]['type'] !== 'devs.Model') continue;
          console.log('baseData.id:%s itemId:%s baseData.attrs.nodeBasicInfo.nodeType: %s', baseData[nodeIndex]['id'], itemIds[i], baseData[nodeIndex]['attrs']['nodeBasicInfo']['nodeType']);
          if (baseData[nodeIndex]['id'] === itemIds[i] &&
              baseData[nodeIndex]['attrs']['nodeBasicInfo'] &&
              'childPortNode'.indexOf(baseData[nodeIndex]['attrs']['nodeBasicInfo']['nodeType']) !== -1 &&
              baseData[nodeIndex]['attrs']['nodeBasicInfo']['nextNodeId']) {
            map[itemIds[i]] = baseData[nodeIndex]['attrs']['nodeBasicInfo']['nextNodeId'];
          }
        }
      }
      return map;
    };

    self.getBranchLabels = function(node, idKeys) {
      var labels = node.attrs.actionParam.selection;
      var map = {};
      for (var i = 0; i < labels.length; i++) {
        map[idKeys[i]] = labels[i];
      }
      return map;
    };

    self.handleDiagramReselectionInput = function(message, type, nodeId) {
      $rootScope.$broadcast('addSeMessage', message,
          'anwer_' + type * '_' + nodeId);
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
