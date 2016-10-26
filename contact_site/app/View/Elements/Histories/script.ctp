<script type="text/javascript">
function openSearchRefine(){
  console.log('入ってる');
  $.ajax({
    type: 'post',
    //data: setting,
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/Histories/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '検索絞り込み', 'moment');
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
