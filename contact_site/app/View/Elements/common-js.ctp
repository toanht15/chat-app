<script type="text/javascript">
  function chgOpStatus(stat){
    $.ajax({
      type: 'GET',
      url: '<?=$this->Html->url(array("action" => "remoteChangeOperatorStatus"))?>',
      data: {
        status: stat
      },
      dataType: 'json',
      cache: false,
      success: function(json){
        var opState = $('#operatorStatus');
        if (json.status == "<?=C_OPERATOR_ACTIVE?>") {
          opState.data('status', <?=C_OPERATOR_ACTIVE?>);
          opState.children('img').prop('src', '/img/op.png').prop('alt', '在籍中');
        }
        else {
          opState.data('status', <?=C_OPERATOR_PASSIVE?>);
          opState.children('img').prop('src', '/img/n_op.png').prop('alt', '退席');
        }
      },
      error: function(){
      }
    });
  }


</script>
