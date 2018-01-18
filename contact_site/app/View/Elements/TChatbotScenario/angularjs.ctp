<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate']);

sincloApp.controller('MainController', function($scope) {
  //thisを変数にいれておく
  var self = this;

  this.actionList = <?php echo json_encode($chatbotScenarioActionList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
  this.setActionList = [];

  // アクションの追加
  this.addItem = function(actionType) {
    if (actionType in this.actionList) {
      var item = this.actionList[actionType];
      item.actionType = actionType;
      this.setActionList.push(angular.copy(item));
    }
  };

  // アクションの削除
  this.removeItem = function(setActionId) {
    this.setActionList.splice(setActionId, 1);
  }
});

</script>
