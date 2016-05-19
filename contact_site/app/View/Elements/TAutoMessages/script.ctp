<script type="text/javascript">
document.body.onload = function(){
	var allCheckElm = document.getElementById('allCheck');
	allCheckElm.addEventListener('click', function(e){
		$('input[name="selectTab"]').prop('checked', this.checked);
	});
	var clickTargetTds = document.querySelectorAll('td:not(.noClick)');
	for (var i = 0; i < clickTargetTds.length; i++) {
		clickTargetTds[i].addEventListener('click', function(e){
			if ('id' in this.parentElement.dataset) {
				this.parentElement.dataset['id'];
				location.href = "<?=$this->Html->url(['controller'=>'TAutoMessages', 'action'=>'edit'])?>/" + this.parentElement.dataset['id'];
			}
		});
	}
};

var toActive = function(flg){
	console.time('timer');

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
