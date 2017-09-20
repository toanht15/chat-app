<script type="text/javascript">
document.body.onload = function(){
	// 全選択用チェックボックス
	var allCheckElm = document.getElementById('allCheck');
	allCheckElm.addEventListener('click', setAllCheck); // 全選択

	// チェックボックス群
	var checkBoxList = document.querySelectorAll('[id^="selectTab"]');
	for (var i = 0; i < checkBoxList.length; i++) {
		checkBoxList[i].addEventListener('change', actBtnShow); // 有効無効ボタンの表示切り替え
	}

	// 「条件」の「設定」ラベル
	var targetBalloonList = document.querySelectorAll('.conditionValueLabel');
	for (var i = 0; i < targetBalloonList.length; i++) {
		targetBalloonList[i].addEventListener('mouseenter', balloonApi.show('cond')); // 設定した条件リストのポップアップ表示
		targetBalloonList[i].addEventListener('mouseleave', balloonApi.hide); // 設定した条件リストのポップアップ非表示
	}

	// 「アクション」の「内容」ラベル
	var targetBalloonList = document.querySelectorAll('.actionValueLabel');
	for (var i = 0; i < targetBalloonList.length; i++) {
		targetBalloonList[i].addEventListener('mouseenter', balloonApi.show('act')); // 設定したアクション内容のポップアップ表示
		targetBalloonList[i].addEventListener('mouseleave', balloonApi.hide); // 設定したアクション内容のポップアップ非表示
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

// 全選択用チェックボックスのコントロール
var allCheckCtrl = function(){
	console.log('ここチェック');
	// 全て選択されている場合
	if ( $('input[name="selectTab"]:not(:checked)').length === 0 ) {
		$('input[name="allCheck"]').prop('checked', true);
	}
	else {
		$('input[name="allCheck"]').prop('checked', false);
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
	allCheckCtrl();
};

// 行クリックでチェックする
var isCheck = function(e){
	var id = getData(this.parentElement, 'id');
	if (id !== undefined) {
		var target = $("#selectTab" + id);
		if (target.prop('checked')) {
			target.prop('checked', false);
		}
		else {
			target.prop('checked', true);
		}
	}
	actBtnShow();
};

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

// 有効/無効処理のリクエスト
var sendActiveRequest = function(data){
	$.ajax({
		type: 'GET',
		url: '/TAutoMessages/changeStatus',
		cache: false,
		data: data,
		dataType: 'html',
		success: function(html){
			location.href = "/TAutoMessages/index"
		}
	});
};

// 有効/無効処理
function toActive(flg){
	var list = document.querySelectorAll('input[name="selectTab"]:checked');
	var selectedList = [];
	for (var i = 0; i < list.length; i++){
		selectedList.push(Number(list[i].value));
	}
	sendActiveRequest({
		status: flg,
		targetList: selectedList
	});
}

// 有効/無効処理
function isActive(flg, id){
	var selectedList = [];
	selectedList.push(Number(id));
	sendActiveRequest({
		status: flg,
		targetList: selectedList
	});
}

function removeAct(no, id){
	modalOpen.call(window, "No." + no + " を削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
	popupEvent.closePopup = function(){
		$.ajax({
			type: 'post',
			data: {
				id: id
			},
			cache: false,
			url: "/TAutoMessages/remoteDelete",
			success: function(){
				location.href = "/TAutoMessages/index";
			}
		});
	};
}
</script>
