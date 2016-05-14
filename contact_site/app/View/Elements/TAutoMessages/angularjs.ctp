<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', []);
sincloApp.controller('MainCtrl', function($scope) {
	$scope.setItemList = {};
	$scope.tmpList = {};

	angular.forEach(<?php echo json_encode($outMessageTriggerList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>, function(v, k){
		$scope.tmpList[k] = v;
		if ( $("#tmp_" + v.key) !== undefined ) {
			$scope.tmpList[k].html = Handlebars.compile($("#tmp_" + v.key).html());
		}
	});

	var inputTarget = $("#setTriggerList > ul");

	$scope.addItem = function(tmpId){
		var template = null;
		var ifType = (String($scope.max_show_time) === "<?=C_COINCIDENT?>") ? "and" : "or";
		if ( tmpId in $scope.tmpList ) {
			template = $scope.tmpList[tmpId].html;
			if ( !(tmpId in $scope.setItemList) ) {
				$scope.setItemList[tmpId] = [];
			}
			else if (tmpId in $scope.setItemList && $scope.setItemList[tmpId].length >= $scope.tmpList[tmpId].createLimit[ifType]) {
				return false;
			}
			inputTarget.append(template());
			$scope.setItemList[tmpId].push({});
			openList();
			if ( String(tmpId) === "<?=C_AUTO_TRIGGER_DAY_TIME?>" ) {
				$(".clockpicker").clockpicker({
					autoclose: true,
				});
			}
		}


	};

	// $(document).ready(function(){
	// 	$("#triggerList li").click( function(e){
	// 	});
	// });
});
</script>
