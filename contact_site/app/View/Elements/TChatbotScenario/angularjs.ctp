<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate']);

sincloApp.controller('MainController', function($scope) {
  //thisを変数にいれておく
  var self = this;

  this.actionList = <?php echo json_encode($chatbotScenarioActionList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;

  // アクションの追加
  this.addItem = function(id) {
    if (id in this.actionList) {
      var parentElement = document.getElementById('tchatbotscenario_form_action_body');
      var targetElement = document.querySelector(`#tchatbotscenario_action_templates .tchatbotscenario_form_action_template_${id}`);
      parentElement.appendChild(targetElement.cloneNode(true));
    }
  };

  // アクションの削除
  this.removeItem = function() {
    console.log(this);
  }

  $scope.onclick = function(e) {
    console.log('=== onclick ===');
    console.log(e);
  }
});

$(document).ready(function() {
  $('.closeBtn').on('click', function(ev) {
    console.log($(this));
  });
});

</script>
