<script type = "text/javascript">

function saveEdit(){
  //document.getElementById('ContractAddForm').submit();
  $.ajax({
    type: "POST",
    url: $('#MailTemplateSettingsEditForm').attr('action'),
    data: $('#MailTemplateSettingsEditForm').serialize()
  }).done(function(data){
    setTimeout(function(){
      location.href = "<?= $this->Html->url('/MailTemplateSettings/index') ?>"
    },1000);
  }).fail(function(data){

  });
}

//一覧画面削除機能
function remoteDeleteCompany(id,when){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', '削除確認');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id: id,
        when: when
      },
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'MailTemplateSettings', 'action' => 'deleteMailInfo']) ?>",
      success: function(){
        location.href = "<?= $this->Html->url(['controller' => 'MailTemplateSettings', 'action' => 'index']) ?>";
      }
    });
  };
}

$(function(){
  //ラジオボタン変更したら
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
      $('#MailTemplateSettingsEditForm')[0][3]['name'] = "data[MJobMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsEditForm')[0][14]['name'] = "data[MJobMailTemplate][sender]";
      $('#MailTemplateSettingsEditForm')[0][15]['name'] = "data[MJobMailTemplate][subject]";
      $('#MailTemplateSettingsEditForm')[0][16]['name'] = "data[MJobMailTemplate][mail_body]";
    }
    else {
      $(".daysAfter").css('display', 'none');
      $('#MailTemplateSettingsEditForm')[0][3]['name'] = "data[MSystemMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsEditForm')[0][14]['name'] = "data[MSystemMailTemplate][sender]";
      $('#MailTemplateSettingsEditForm')[0][15]['name'] = "data[MSystemMailTemplate][subject]";
      $('#MailTemplateSettingsEditForm')[0][16]['name'] = "data[MSystemMailTemplate][mail_body]";
    }
    //変数メールアドレス・パスワード
    if(($(this)[0]['id'] == 'MJobMailTemplateAgreementFlg1' && document.form.elements[6].checked == true) ||
     ($(this)[0]['id'] == 'MJobMailTemplateAgreementFlg2' && document.form.elements[6].checked == true) || $(this)[0]['id'] == 'MailTemplateSettingsTimeToSendMail1') {
      $("#variable").css('display', 'block');
    }
    else {
      $("#variable").css('display', 'none');
    }
  });

  //初期表示
  if(<?= $value ?> == 3 || <?= $value ?> == 4) {
    $(".daysAfter").css('display', 'block');
  }

  else {
    $(".daysAfter").css('display', 'none');
  }
});

</script>