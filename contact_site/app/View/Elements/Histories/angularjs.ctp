<script type="text/javascript">
'use strict';

(function(){
	var sincloApp = angular.module('sincloApp', ['ngSanitize']);
	sincloApp.controller('MainCtrl', function($scope) {
		$scope.ua = function(str){
			return userAgentChk.init(str);
		};
	});

	window.openHistoryById = function(id){
		var retList = {};
			$.ajax({
				type: 'GET',
				url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetStayLogs')) ?>",
				data: {
					historyId: id
				},
				dataType: 'html',
				success: function(html){
					modalOpen.call(window, html, 'p-history-logs', 'ページ移動履歴');
				},
				error: function(){
				}
			});
	};

	window.openChatById = function(id){
		var retList = {};
			$.ajax({
				type: 'GET',
				url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetChatLogs')) ?>",
				data: {
					historyId: id
				},
				dataType: 'html',
				success: function(html){
					modalOpen.call(window, html, 'p-chat-logs', 'チャット履歴');
				},
				error: function(){
				}
			});
	};
}());

</script>
