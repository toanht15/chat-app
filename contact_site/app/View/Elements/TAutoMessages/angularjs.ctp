<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', []);
sincloApp.controller('MainCtrl', function($scope) {
	$scope.setItemList = {};
	$scope.tmpList = {
		"<?=C_AUTO_TRIGGER_STAY_TIME?>": {
		createLimit: {and:1, or:1},
		html: Handlebars.compile($("#tmp_stay_time").html())
		}, // 滞在時間
		"<?=C_AUTO_TRIGGER_VISIT_CNT?>": {
		createLimit: {and:1, or:1},
		html: Handlebars.compile($("#tmp_visit_cnt").html())
		}, // 訪問回数
		"<?=C_AUTO_TRIGGER_STAY_PAGE?>": {
		createLimit: {and:1, or:1},
		html: Handlebars.compile($("#tmp_stay_page").html())
		}, // ページ
		"<?=C_AUTO_TRIGGER_DAY_TIME?>": {
		createLimit: {and:1, or:1},
		html: Handlebars.compile($("#tmp_day_time").html())
		}, // 曜日・時間
		"<?=C_AUTO_TRIGGER_REFERRER?>": {
		createLimit: {and:1, or:1},
		html: Handlebars.compile($("#tmp_referrer").html())
		}, // 参照元URL（リファラー）
		"<?=C_AUTO_TRIGGER_SEARCH_KEY?>": {
		createLimit: {and:1, or:1},
		html: Handlebars.compile($("#tmp_search_keyword").html())
		} // 検索キーワード
	};

	$(document).ready(function(){
		var inputTarget = $("#setTriggerList > ul");
		$("#triggerList li").click( function(e){
		var template = null;
		var ifType = (String($scope.max_show_time) === "<?=C_COINCIDENT?>") ? "and" : "or";
		if ( $(this).data('type') in $scope.tmpList ) {
			var tmpId = $(this).data('type');
			template = $scope.tmpList[tmpId].html;
			if ( !(tmpId in $scope.setItemList) ) {
				$scope.setItemList[tmpId] = [];
			}
			else if (tmpId in $scope.setItemList && $scope.setItemList[tmpId] >= $scope.tmpList[tmpId].createLimit[ifType]) {
				return false;
			}
			inputTarget.append(template());
			$scope.setItemList[tmpId] = [];
console.log($scope.setItemList);
			openList();
			if ( String($(this).data('type')) === "<?=C_AUTO_TRIGGER_DAY_TIME?>" ) {
			$(".clockpicker").clockpicker({
				autoclose: true,
			});
			}
		}
		});
	});
});
</script>
