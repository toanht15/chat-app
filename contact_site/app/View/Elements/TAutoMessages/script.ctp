<script type="text/javascript">
document.body.onload = function(){
	// 全選択用チェックボックス
	var allCheckElm = document.getElementById('allCheck');
	allCheckElm.addEventListener('click', setAllCheck); // 全選択
	allCheckElm.addEventListener('change', actBtnShow); // 全選択

	// チェックボックス群
	var checkBoxList = document.querySelectorAll('[id^="selectTab"]');
	for (var i = 0; i < checkBoxList.length; i++) {
		checkBoxList[i].addEventListener('change', actBtnShow); // 更新画面への遷移
	}

	// チェックボックスが入っていないtdタグ群
	var clickTargetTds = document.querySelectorAll('td:not(.noClick)');
	for (var i = 0; i < clickTargetTds.length; i++) {
		clickTargetTds[i].addEventListener('click', toEditPage); // 更新画面への遷移
	}

	// 「条件」の「設定」ラベル
	var targetBalloonList = document.querySelectorAll('.conditionValueLabel');
	for (var i = 0; i < targetBalloonList.length; i++) {
		targetBalloonList[i].addEventListener('mouseenter', balloonApi.show('cond')); // 設定した条件リストのポップアップ表示
		targetBalloonList[i].addEventListener('mouseleave', balloonApi.hide); // 設定した条件リストのポップアップ非表示
	}

	// 「条件」の「設定」ラベル
	var targetBalloonList = document.querySelectorAll('.actionValueLabel');
	for (var i = 0; i < targetBalloonList.length; i++) {
		targetBalloonList[i].addEventListener('mouseenter', balloonApi.show('act')); // 設定した条件リストのポップアップ表示
		targetBalloonList[i].addEventListener('mouseleave', balloonApi.hide); // 設定した条件リストのポップアップ非表示
	}
};

// 全選択
var setAllCheck = function() {
	$('input[name="selectTab"]').prop('checked', this.checked);
	if ( this.checked ) {
		$(".actCtrlBtn").css('display', 'block');
	}
	else {
		$(".actCtrlBtn").css('display', 'none');
	}
}

// 有効/無効ボタンの表示/非表示
var actBtnShow = function(){
	// 選択中の場合
	if ( $('input[name="selectTab"]').is(":checked") ) {
		$(".actCtrlBtn").css('display', 'block');
	}
	else {
		$(".actCtrlBtn").css('display', 'none');
		$('#allCheck').prop('checked', false);
	}
};

// 更新画面への遷移
var toEditPage = function(){
	if ('id' in this.parentElement.dataset) {
		location.href = "<?=$this->Html->url(['controller'=>'TAutoMessages', 'action'=>'edit'])?>/" + this.parentElement.dataset['id'];
	}
}

// 設定した条件リストのポップアップ表示
var balloonApi = {
	flg: false,
	show: function(type) {
		return function (e) {
			balloonApi.flg = true;
			var id = getData(this.parentElement.parentElement, 'id');
			if (id) {
				var elm = $(this);
				var offset = elm.offset();

				$("[id='balloon_" + type + "_" +id+"']").animate({
					top: offset.top + elm.prop("offsetHeight") + 3,
					left: offset.left + 3
				}, {
					duration: "first",
					complete: function(){
						$("[id^='balloon_']").hide();
						if (balloonApi.flg) {
							$(this).show();
						}
					}
				});
			}
		}
	},
	hide: function(e){
		balloonApi.flg = false;
		$("[id^='balloon_']").hide();
	}
};

// 有効/無効処理
var toActive = function(flg){
	var list = document.querySelectorAll('input[name="selectTab"]:checked');
	var selectedList = [];
	for (var i = 0; i < list.length; i++){
		selectedList.push(Number(list[i].value));
	}
	var data = {
		status: flg,
		targetList: selectedList
	};

	$.ajax({
		type: 'GET',
		url: '/TAutoMessages/changeStatus',
		cache: false,
		data: data,
		dataType: 'html',
		success: function(html){
			location.href = "/TAutoMessages/index"
		},
		error: function(){
		}
	});
};
</script>
