<script type = "text/javascript">

  function saveAct(){
    //document.getElementById('ContractAddForm').submit();
    $.ajax({
      type: "POST",
      url: $('#ContractAddForm').attr('action'),
      data: $('#ContractAddForm').serialize()
    }).done(function(data){
      socket.emit('settingReload', JSON.stringify({type:1, siteKey: "master"}));
      location.href = "<?= $this->Html->url('/Contract/index') ?>"
    }).fail(function(data){

    });
  }

</script>