<script type = "text/javascript">
function saveAct(){
  //document.getElementById('ContractAddForm').submit();
  console.log('チェック');
  console.log($('#MailTemplateSettingsAddForm').attr('action'));
  $.ajax({
    type: "POST",
    url: $('#MailTemplateSettingsAddForm').attr('action'),
    data: $('#MailTemplateSettingsAddForm').serialize()
  }).done(function(data){
    socket.emit('settingReload', JSON.stringify({type:1, siteKey: "master"}));
    setTimeout(function(){
      location.href = "<?= $this->Html->url('/MailTemplateSettings/index') ?>"
    },1000);
  }).fail(function(data){
    var obj = JSON.parse(data.responseText);
    alert(obj.message);
  });
}

//削除処理
function remoteDeleteCompany(id,companyId,userId,companyKey){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'アカウント設定');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id:id,
        companyId:companyId,
        userId:userId,
        companyKey:companyKey
      },
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'MailTemplateSettings', 'action' => 'remoteDeleteCompany']) ?>",
      success: function(){
        location.href = "<?= $this->Html->url(['controller' => 'MailTemplateSettings', 'action' => 'index']) ?>";
      }
    });
  };
}
</script>