<script>
  function radioButtonChangeHandler(self) {
    if($(self)[0]['id'] == 'MailTemplateSettingsTimeToSendMail3' || $(self)[0]['id'] == 'MailTemplateSettingsTimeToSendMail4' ||
      $(self)[0]['id'] == 'MJobMailTemplateSendMailMlFlg0' || $(self)[0]['id'] == 'MJobMailTemplateSendMailMlFlg1' ||
      ($(self)[0]['id'] == 'MJobMailTemplateAgreementFlg1' && ($('#MailTemplateSettingsTimeToSendMail3').is(':checked') || $('#MailTemplateSettingsTimeToSendMail4').is(':checked')))
      || ($(self)[0]['id'] == 'MJobMailTemplateAgreementFlg2' && ($('#MailTemplateSettingsTimeToSendMail3').is(':checked') || $('#MailTemplateSettingsTimeToSendMail4').is(':checked')))) {
      $(".daysAfter").css('display', 'block');
      $(".daysAfter.sendTarget").css('display', 'flex');
      if($(self)[0]['id'] == 'MailTemplateSettingsTimeToSendMail3') {
        $("#value").text("何日後");
      }
      if($(self)[0]['id'] == 'MailTemplateSettingsTimeToSendMail4') {
        $("#value").text("何日前");
      }
      $('#MJobMailTemplateMailTypeCd, #MSystemMailTemplateMailTypeCd').attr('name', 'data[MJobMailTemplate][mail_type_cd]');
      $('#MJobMailTemplateSender, #MSystemMailTemplateSender').attr('name', 'data[MJobMailTemplate][sender]');
      $('#MJobMailTemplateSubject, #MSystemMailTemplateSubject').attr('name', 'data[MJobMailTemplate][subject]');
      $('#MJobMailTemplateMailBody, #MSystemMailTemplateMailBody').attr('name', 'data[MJobMailTemplate][mail_body]');
    }
    else {
      $(".daysAfter").css('display', 'none');
      $(".daysAfter.sendTarget").css('display', 'none');
      $('#MJobMailTemplateMailTypeCd, #MSystemMailTemplateMailTypeCd').attr('name', 'data[MSystemMailTemplate][mail_type_cd]');
      $('#MJobMailTemplateSender, #MSystemMailTemplateSender').attr('name', 'data[MSystemMailTemplate][sender]');
      $('#MJobMailTemplateSubject, #MSystemMailTemplateSubject').attr('name', 'data[MSystemMailTemplate][subject]');
      $('#MJobMailTemplateMailBody, #MSystemMailTemplateMailBody').attr('name', 'data[MSystemMailTemplate][mail_body]');
    }
    ///変数メールアドレス・パスワード
    if(($(self)[0]['id'] == 'MJobMailTemplateAgreementFlg1' && $('#MailTemplateSettingsTimeToSendMail1').is(':checked')) ||
      ($(self)[0]['id'] == 'MJobMailTemplateAgreementFlg2' && $('#MailTemplateSettingsTimeToSendMail1').is(':checked'))
      || $(self)[0]['id'] == 'MailTemplateSettingsTimeToSendMail1') {
      $(".initialVariable").css('display', '');
    }
    else {
      $(".initialVariable").css('display', 'none');
    }
  }

  $(window).on('load', function(){
    $('input[type="radio"]').change(function(e) {
      radioButtonChangeHandler(this);
    });
    radioButtonChangeHandler($('input[type="radio"]:checked').get(0));
  });
</script>