<script type = "text/javascript">

  function saveEdit(){
    //document.getElementById('ContractAddForm').submit();
    $.ajax({
      type: "POST",
      url: $('#ContractEditForm').attr('action'),
      data: $('#ContractEditForm').serialize()
    }).done(function(data){
      socket.emit('settingReload', JSON.stringify({type:1, siteKey: "master"}));
      location.href = "<?= $this->Html->url('/Contract/index') ?>"
    }).fail(function(data){

    });
  }

</script>