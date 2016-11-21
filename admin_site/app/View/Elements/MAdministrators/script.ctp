<script type="text/javascript">
'use strict';
function openConfirm(){
  openEntryDialog({type: 1});
}
function openEditDialog(id){
  openEntryDialogUpdate({type: 2, id: id});
}
function openEntryDialog(setting){
  var type = setting.type;
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, type:2 => type, id
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/MAdministrators/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-muser-entry', 'アカウント登録');
    }
  });
}

function openEntryDialogUpdate(setting){
  var type = setting.type;
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, type:2 => type, id
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/MAdministrators/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-muser-update', 'アカウント更新');
    }
  });
}
</script>

