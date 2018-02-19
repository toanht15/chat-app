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

</script>