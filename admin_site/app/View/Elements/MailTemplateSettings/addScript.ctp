<script type = "text/javascript">
function saveAct(){
  //document.getElementById('ContractAddForm').submit();
  $.ajax({
    type: "POST",
    url: $('#MailTemplateSettingsAddForm').attr('action'),
    data: $('#MailTemplateSettingsAddForm').serialize()
  }).done(function(data){
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
$(function(){
  $('input[type="radio"]').change(function(e) {
    if($(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail3' || $(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail4' ||
      $(this)[0]['id'] == 'MJobMailTemplateSendMailMlFlg0' || $(this)[0]['id'] == 'MJobMailTemplateSendMailMlFlg1' ||
      ($(this)[0]['id'] == 'MJobMailTemplateAgreementFlg1' && (document.form.elements[8].checked == true || document.form.elements[9].checked == true))
      || ($(this)[0]['id'] == 'MJobMailTemplateAgreementFlg2' && (document.form.elements[8].checked == true || document.form.elements[9].checked == true))) {
      $(".daysAfter").css('display', 'block');
      if($(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail3') {
        $("#value").text("何日後");
      }
      if($(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail4') {
        $("#value").text("何日前");
      }
      $('#MailTemplateSettingsAddForm')[0][2]['name'] = "data[MJobMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsAddForm')[0][13]['name'] = "data[MJobMailTemplate][sender]";
      $('#MailTemplateSettingsAddForm')[0][14]['name'] = "data[MJobMailTemplate][subject]";
      $('#MailTemplateSettingsAddForm')[0][15]['name'] = "data[MJobMailTemplate][mail_body]";
    }
    else {
      $(".daysAfter").css('display', 'none');
      $('#MailTemplateSettingsAddForm')[0][2]['name'] = "data[MSystemMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsAddForm')[0][13]['name'] = "data[MSystemMailTemplate][sender]";
      $('#MailTemplateSettingsAddForm')[0][14]['name'] = "data[MSystemMailTemplate][subject]";
      $('#MailTemplateSettingsAddForm')[0][15]['name'] = "data[MSystemMailTemplate][mail_body]";
    }
    ///変数メールアドレス・パスワード
    if(($(this)[0]['id'] == 'MJobMailTemplateAgreementFlg1' && document.form.elements[6].checked == true) ||
     ($(this)[0]['id'] == 'MJobMailTemplateAgreementFlg2' && document.form.elements[6].checked == true) || $(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail1') {
      $("#variable").css('display', 'block');
    }
    else {
      $("#variable").css('display', 'none');
    }
  });
});
</script>