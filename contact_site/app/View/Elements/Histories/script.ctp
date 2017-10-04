<script type="text/javascript">
//モーダル画面
function openSearchRefine(){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'remoteSearchCustomerInfo']) ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '高度な検索', 'moment');
    }
  });
}

//セッションクリア(条件クリア)
function sessionClear(){
  location.href = "<?=$this->Html->url(array('controller' => 'Histories', 'action' => 'portionClearSession'))?>";
}

//履歴削除モーダル画面
function openDeleteDialog(id,historyId,message,created){
  console.log('入ってる');
  $.ajax({
    type: 'post',
    dataType: 'html',
    data: {
      id:id,
      historyId:historyId,
      message:message,
      created:created
    },
    cache: false,
    url: "<?= $this->Html->url('/Histories/openEntryDelete') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-history-del', '履歴の削除', 'moment');
    }
  });
}
</script>
