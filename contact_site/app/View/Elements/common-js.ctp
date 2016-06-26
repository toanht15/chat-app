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
      }
    });
  }

  function ajaxTimeout(){
	modalOpen.call(window, "タイムアウトしました", 'p-alert', 'アラート');
	popupEvent.closeNoPopup = function(){
		location.href = "<?=$this->Html->url(['controller' => 'Login', 'action'=>'logout'])?>";
	};

  }

  $.ajaxSetup({
    cache: false,
    error: function(XMLHttpRequest, textStatus, errorThrown){
      if ( textStatus ) {
        ajaxTimeout();
      }
    }
  });


</script>
