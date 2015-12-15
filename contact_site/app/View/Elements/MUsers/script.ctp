<script type="text/javascript">
function openAddDialog(){
	openEntryDialog({type: 1});
}
function openEditDialog(id){
	openEntryDialog({type: 2, id: id});
}
function openEntryDialog(setting){
	var type = setting.type;
	$.ajax({
		type: 'post',
		data: setting, // type:1 => type, type:2 => type, id
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