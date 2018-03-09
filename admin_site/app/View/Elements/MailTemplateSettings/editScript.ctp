<script type = "text/javascript">

function saveEdit(){
  //document.getElementById('ContractAddForm').submit();
  console.log('saveEdit');
  console.log($('#MailTemplateSettingsEditForm').attr('action'));
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
    //何日後
    if($(this).val() == 3 || $(this).val() == 4) {
      $(".daysAfter").css('display', 'block');
      $('#MailTemplateSettingsEditForm')[0][3]['name'] = "data[MJobMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsEditForm')[0][9]['name'] = "data[MJobMailTemplate][subject]";
      $('#MailTemplateSettingsEditForm')[0][10]['name'] = "data[MJobMailTemplate][mail_body]";
      $(this).parents('form').attr('action', '/MailTemplateSettings/edit/<?= $id ?>/3/<?= $value ?>');
    }
    //無料トライアル申込み後or初期パスワード変更後
    else {
      $(".daysAfter").css('display', 'none');
      $('#MailTemplateSettingsEditForm')[0][3]['name'] = "data[MSystemMailTemplate][mail_type_cd]";
      $('#MailTemplateSettingsEditForm')[0][9]['name'] = "data[MSystemMailTemplate][subject]";
      $('#MailTemplateSettingsEditForm')[0][10]['name'] = "data[MSystemMailTemplate][mail_body]";
      //無料トライアル申込み後
      if($(this).val() == 1) {
        $(this).parents('form').attr('action', '/MailTemplateSettings/edit/<?= $id ?>/1/<?= $value ?>');
      }
      //初期パスワード変更後
      else if($(this).val() == 2) {
        $(this).parents('form').attr('action', '/MailTemplateSettings/edit/<?= $id ?>/2/<?= $value ?>');
      }
    }
  });

  //初期表示
  if(<?= $value ?> == 4) {
    $(".daysAfter").css('display', 'block');
    $(".delete_btn").css('display', 'block');
  }

  else {
    $(".daysAfter").css('display', 'none');
  }
});

</script>