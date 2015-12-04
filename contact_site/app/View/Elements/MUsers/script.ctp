<script type="text/javascript">
function openAddDialog(){
	openEntryDialog(1);
}
function openEntryDialog(type){
	$.ajax({
		type: 'post',
		data: {
			type: type
		},
		dataType: 'html',
		url: "<?= $this->Html->url('/MUsers/remoteOpenEntryForm') ?>",
		success: function(html){
			modalOpen.call(window, html, 'p-muser-entry', 'ユーザー情報');
		},
		error: function(){

		}
	});
}
</script>