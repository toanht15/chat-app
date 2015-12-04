<script type="text/javascript">
'use strict';

(function(){
	var sincloApp = angular.module('sincloApp', ['ngSanitize']);
	sincloApp.controller('MainCtrl', function($scope) {
		$scope.ua = function(str){
			return userAgentChk.init(str);
		};

		function _numPad(str){
			return ("0" + str).slice(-2);
		}

		$scope.calcTime = function(startTime, endTime){
			var end = new Date(endTime),
				req, hour, min, sec,
				start = new Date(startTime);
			if ( isNaN(start.getTime()) || isNaN(end.getTime()) ) return "-";
			req = parseInt((end.getTime() - start.getTime()) / 1000);
			hour = parseInt(req / 3600);
			min = parseInt((req / 60) % 60);
			sec = req % 60;
			return _numPad(hour) + ":" + _numPad(min) + ":" + _numPad(sec); // 表示を更新
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
}());

</script>