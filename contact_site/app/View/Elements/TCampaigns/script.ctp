<script type="text/javascript">
function openAddDialog(){
  openEntryDialog({type: 1});
}
function openEditDialog(id){
	console.log(id);
  openEntryDialog({type: 2, id: id});
}
function openEntryDialog(setting){
  var type = setting.type;
  //console.log(setting.id);
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, type:2 => type, id
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/TCampaigns/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-tcampaign-entry', 'キャンペーン情報', 'moment');
    }
  });
}
function openConfirmDialog(id){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'キャンペーン情報', 'moment');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        id: id
      },
      url: "<?= $this->Html->url('/TCampaigns/remoteDeleteUser') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TCampaigns/index') ?>";
      }
    });
  };
}
</script>
