<script type = "text/javascript">
function saveAct(){
  //document.getElementById('ContractAddForm').submit();
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
$(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail4' ||
$(function(){
  $('input[type="radio"]').change(function(e) {
    if($(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail3' || $(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail4' ||
      $(this)[0]['id'] == 'MailTemplateSettingsToSendMediaLink0' || $(this)[0]['id'] == 'MailTemplateSettingsToSendMediaLink1') {
      $(".daysAfter").css('display', 'block');
      if($(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail3') {
        $("#value").text("何日後");
      }
      if($(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail4') {
        $("#value").text("何日前");
      }
      $('#MailTemplateSettingsAddForm')[0][2]['name'] = "data[MJobMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsAddForm')[0][11]['name'] = "data[MJobMailTemplate][sender]";
      $('#MailTemplateSettingsAddForm')[0][12]['name'] = "data[MJobMailTemplate][subject]";
      $('#MailTemplateSettingsAddForm')[0][13]['name'] = "data[MJobMailTemplate][mail_body]";
    }
    else {
      $(".daysAfter").css('display', 'none');
      $('#MailTemplateSettingsAddForm')[0][2]['name'] = "data[MSystemMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsAddForm')[0][11]['name'] = "data[MJobMailTemplate][sender]";
      $('#MailTemplateSettingsAddForm')[0][12]['name'] = "data[MSystemMailTemplate][subject]";
      $('#MailTemplateSettingsAddForm')[0][13]['name'] = "data[MSystemMailTemplate][mail_body]";
    }
  });
});
</script>