<script type="text/javascript">
  var sheets = document.styleSheets,
      sheet = sheets[sheets.length - 1];

  if (sheet.insertRule)
  {
    sheet.insertRule('.daterangepicker.opensleft:before {right:234px}', sheet.cssRules.length);
    sheet.insertRule('.daterangepicker.opensleft:after {right:235px}', sheet.cssRules.length);
  }
//モーダル画面
function openSearchRefine(){
  var sheets = document.styleSheets,
      sheet = sheets[sheets.length - 1];

  if (sheet.insertRule)
  {
    sheet.insertRule('.daterangepicker.opensleft:before {right:9px}', sheet.cssRules.length);
    sheet.insertRule('.daterangepicker.opensleft:after {right:10px}', sheet.cssRules.length);
  }
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'remoteOpenEntryForm']) ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '絞り込み検索', 'moment');
    }
  });
}
</script>
