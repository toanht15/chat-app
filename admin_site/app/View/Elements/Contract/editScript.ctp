<script type = "text/javascript">

  function saveEdit(){
    //document.getElementById('ContractAddForm').submit();
    $.ajax({
      type: "POST",
      url: $('#ContractEditForm').attr('action'),
      data: $('#ContractEditForm').serialize()
    }).done(function(data){
      socket.emit('settingReload', JSON.stringify({type:1, siteKey: "master"}));
      setTimeout(function(){
        location.href = "<?= $this->Html->url('/Contract/index') ?>"
      },1000);
    }).fail(function(data){

    });
  }

  //一覧画面削除機能
  function remoteDeleteCompany(companyId,companyKey){
    modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', '削除確認');
    popupEvent.closePopup = function(){
      $.ajax({
        type: 'post',
        data: {
          id: companyId,
          companyKey:companyKey
        },
        cache: false,
        url: "<?= $this->Html->url(['controller' => 'Contract', 'action' => 'deleteCompany']) ?>",
        success: function(){
          location.href = "<?= $this->Html->url(['controller' => 'Contract', 'action' => 'index']) ?>";
        }
      });
    };
  }

</script>