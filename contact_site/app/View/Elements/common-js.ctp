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
        var opState = $('#changeOpStatus');
        if (json.status == "<?=C_OPERATOR_ACTIVE?>") {
          opState.data('status', <?=C_OPERATOR_ACTIVE?>);
          opState.children('p').text('離席中にする');
          $('#operatorStatus').prop("class", "opStop").children("span").text('待機中');
        }
        else {
          opState.data('status', <?=C_OPERATOR_PASSIVE?>);
          opState.children('p').text('待機中にする');
          $('#operatorStatus').prop("class", "opWait").children("span").text('離席中');
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
