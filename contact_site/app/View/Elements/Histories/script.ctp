<script type="text/javascript">
function openSearchRefine(){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/Histories/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '検索絞り込み', 'moment');
    }
  });
}
</script>
