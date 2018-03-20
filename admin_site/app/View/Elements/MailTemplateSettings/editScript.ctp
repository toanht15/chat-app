<script type = "text/javascript">

  function saveEdit(){
    //document.getElementById('ContractAddForm').submit();
    $.ajax({
      type: "POST",
      url: $('#MailTemplateSettingsEditForm').attr('action'),
      data: $('#MailTemplateSettingsEditForm').serialize()
    }).done(function(data){
      socket.emit('settingReload', JSON.stringify({type:1, siteKey: "master"}));
      setTimeout(function(){
        location.href = "<?= $this->Html->url('/MailTemplateSettings/index') ?>"
      },1000);
    }).fail(function(data){

    });
  }

  //一覧画面削除機能
  function remoteDeleteCompany(id){
    modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', '削除確認');
    popupEvent.closePopup = function(){
      $.ajax({
        type: 'post',
        data: {
          id: id
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
      $(this)[0]['id'] == 'MJobMailTemplateAgreementFlg1' || $(this)[0]['id'] == 'MJobMailTemplateAgreementFlg2') {
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
  });

  //初期表示
  if(<?= $value ?> == 3 || <?= $value ?> == 4) {
    $(".daysAfter").css('display', 'block');
    $(".delete_btn").css('display', 'block');
  }

  else {
    $(".daysAfter").css('display', 'none');
  }
});

</script>