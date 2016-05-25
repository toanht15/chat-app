<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']);

sincloApp.controller('MainCtrl', function($scope) {
	//thisを変数にいれておく
	var self = this;

	var setActivity = <?=( !empty($this->data['TAutoMessage']['activity']) ) ? json_encode($this->data['TAutoMessage']['activity'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : "{}" ?>;

	this.setItemList = {};
	var setItemListTmp = (typeof(setActivity) === "string") ? JSON.parse(setActivity) : setActivity;
	if ( 'conditions' in setItemListTmp ) {
		this.setItemList = setItemListTmp['conditions'];
	}
	this.keys = function(obj){
        if (angular.isObject(obj)) {
            return Object.keys(obj).length;
        }
        else {
            return obj.length;
        }
    };

	this.tmpList = <?php echo json_encode($outMessageTriggerList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;

	$scope.$watch(function(){
	  return self.setItemList;
	});

	// var inputTarget = $("#setTriggerList > ul");

	this.checkDisabled = function(itemId){
		var ifType = (String(this.condition_type) === "<?=C_COINCIDENT?>") ? "and" : "or";

		return (itemId in this.setItemList && this.setItemList[itemId].length >= this.tmpList[itemId].createLimit[ifType]);
	};

	this.addItem = function(tmpId){
		var ifType = (String(this.condition_type) === "<?=C_COINCIDENT?>") ? "and" : "or";

		if ( tmpId in this.tmpList ) {
			if ( !(tmpId in this.setItemList) ) {
				this.setItemList[tmpId] = [];
			}
			else if (tmpId in this.setItemList && this.setItemList[tmpId].length >= this.tmpList[tmpId].createLimit[ifType]) {
				return false;
			}
			this.setItemList[tmpId].push(angular.copy(this.tmpList[tmpId].default));
		}
	};

	this.openList = function(elm){
		var target = null;
		target = $(String(elm));
		if (!target.is(".selected")) {
			$("li.triggerItem.selected").css('height', 34 + "px").removeClass("selected");
			target.css('height', target.children("ng-form").children("div").prop("offsetHeight") + 34 + "px").addClass("selected");
		}
		else {
			$("li.triggerItem.selected").css('height', 34 + "px").removeClass("selected");
		}
	};

	this.requireCheckBox = function(form){
		if (form === undefined) return false;
		var ret = Object.keys(form).filter(function(k) {
			return form[k] == true;
		})[0];
		return ( ret === undefined || ret.length === 0 );
	};

	this.removeItem = function(itemType, itemId){
		if ( itemType in this.setItemList ) {
			if ( itemId in this.setItemList[itemType] ) {
				if ( Object.keys(this.setItemList[itemType]).length === 1 ) {
					delete this.setItemList[itemType];
				}
				else {
					this.setItemList[itemType].splice(itemId, 1);
				}
				angular.bind(this, function() {
					this.setItemList = self.setItemList;
					$scope.$apply();
				})
				$("div.balloon").hide();
			}
		}
	};

	this.saveAct = function(){
		var setList = {
				'conditionType': Number(this.condition_type),
				'conditions': angular.copy(this.setItemList),
				 // TODO 後々動的に
				'message': angular.element("#TAutoMessageAction").val()
		};
		$('#TAutoMessageActivity').val(JSON.stringify(setList));
		submitAct();
	};
});

// http://stackoverflow.com/questions/17035621/what-is-the-angular-way-of-displaying-a-tooltip-lightbox
sincloApp.directive('ngShowonhover',function() {
	return {
		link : function(scope, element, attrs) {
			var balloon = $("div.balloon");

			var itemsTag = element.closest("li");
			element.parent().bind('mouseenter', function(e) {
				if (Object.keys(scope.itemForm.$error).length === 0) { return false; }
				createBalloon(attrs['ngShowonhover'], scope.itemForm);
				var top = itemsTag.prop('offsetTop');
				var left = itemsTag.prop('offsetLeft');
				balloon.css({
					"top": top + 10
				}).show();
			});
			element.parent().bind('mouseleave', function() {
				balloon.hide();
			});

			var createBalloon = function(key, form){
				var messageList = [];
				$("div.balloonContent").children().remove();

				/* 滞在時間 */
				if ( 'stayTimeRange' in form ) {
					if ( 'required' in form.stayTimeRange.$error ) {
						messageList.push("時間が未入力です");
					}
					if ('max' in form.stayTimeRange.$error) {
						messageList.push("時間は０～１００回までの間で指定できます");
					}
				}
				/* 訪問回数 */
				if ( 'visitCnt' in form ) {
					if ('required' in form.visitCnt.$error) {
						messageList.push("訪問回数が未入力です");
					}
					if ('max' in form.visitCnt.$error) {
						messageList.push("訪問回数は０～１００回までの間で指定できます");
					}
				}
				/* ページ・リファラー・検索キーワード */
				if ( 'keyword' in form ) {
					if ('required' in form.keyword.$error) {
						messageList.push("キーワードが未入力です");
					}
				}
				/* 曜日・日時 */
				if ( 'day' in form ) {
					if ('required' in form.day.$error) {
						messageList.push("曜日が未選択です");
					}
				}
				if ( 'startTime' in form ) {
					if ('required' in form.startTime.$error) {
						messageList.push("開始時間が未選択です");
					}
				}
				if ( 'endTime' in form ) {
					if ('required' in form.endTime.$error) {
						messageList.push("開始時間が未選択です");
					}
				}

				for( var i = 0; i <	 messageList.length; i++ ){
					var element = document.createElement("p");
					element.textContent = "● " + messageList[i];
					$("div.balloonContent").append(element);
				}
			};
		}
	};
});

function removeAct(){
	modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定');
	popupEvent.closePopup = function(){
		$.ajax({
			type: 'post',
			data: {
				id: document.getElementById('TAutoMessageId').value
			},
			url: "<?= $this->Html->url('/TAutoMessages/remoteDelete') ?>",
			success: function(){
				location.href = "<?= $this->Html->url('/TAutoMessages/index') ?>";
			},
			error: function(){
				location.href = "<?= $this->Html->url('/TAutoMessages/index') ?>";
			}
		});
	};
}

function submitAct(){
  $('#TAutoMessageEntryForm').submit();
}
</script>
