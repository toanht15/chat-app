<script type="text/javascript">
document.body.onload = function(){
	var allCheckElm = document.getElementById('allCheck');
	   allCheckElm.addEventListener('click', function(e){
	  $('input[name="selectTab"]').prop('checked', this.checked);
	 });
};

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
