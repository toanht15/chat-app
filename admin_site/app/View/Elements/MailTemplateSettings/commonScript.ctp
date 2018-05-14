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
      $('#MailTemplateSettingsAddForm, #MailTemplateSettingsEditForm')[0][2]['name'] = "data[MJobMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsAddForm, #MailTemplateSettingsEditForm')[0][13]['name'] = "data[MJobMailTemplate][sender]";
      $('#MailTemplateSettingsAddForm, #MailTemplateSettingsEditForm')[0][14]['name'] = "data[MJobMailTemplate][subject]";
      $('#MailTemplateSettingsAddForm, #MailTemplateSettingsEditForm')[0][15]['name'] = "data[MJobMailTemplate][mail_body]";
    }
    else {
      $(".daysAfter").css('display', 'none');
      $(".daysAfter.sendTarget").css('display', 'none');
      $('#MailTemplateSettingsAddForm, #MailTemplateSettingsEditForm')[0][2]['name'] = "data[MSystemMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsAddForm, #MailTemplateSettingsEditForm')[0][13]['name'] = "data[MSystemMailTemplate][sender]";
      $('#MailTemplateSettingsAddForm, #MailTemplateSettingsEditForm')[0][14]['name'] = "data[MSystemMailTemplate][subject]";
      $('#MailTemplateSettingsAddForm, #MailTemplateSettingsEditForm')[0][15]['name'] = "data[MSystemMailTemplate][mail_body]";
    }
    ///変数メールアドレス・パスワード
    if(($(self)[0]['id'] == 'MJobMailTemplateAgreementFlg1' && document.form.elements[6].checked == true) ||
      ($(self)[0]['id'] == 'MJobMailTemplateAgreementFlg2' && document.form.elements[6].checked == true) || $(self)[0]['id'] == 'MailTemplateSettingsTimeToSendMail1') {
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