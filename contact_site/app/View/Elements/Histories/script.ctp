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

</script>
