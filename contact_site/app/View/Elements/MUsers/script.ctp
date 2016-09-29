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
                cache: false,
		url: "<?= $this->Html->url('/MUsers/remoteOpenEntryForm') ?>",
		success: function(html){
			modalOpen.call(window, html, 'p-muser-entry', 'ユーザー情報', 'moment');
		}
	});
}
function openConfirmDialog(id){
	modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'ユーザー情報', 'moment');
	popupEvent.closePopup = function(){
		$.ajax({
			type: 'post',
                        cache: false,
			data: {
				id: id
			},
			url: "<?= $this->Html->url('/MUsers/remoteDeleteUser') ?>",
			success: function(){
				location.href = "<?= $this->Html->url('/MUsers/index') ?>";
			}
		});
	};

}
</script>
