<script type="text/javascript">
  function chgOpStatus(){
    var opState = $('#changeOpStatus'),
        status = opState.data('status');
    $.ajax({
      type: 'GET',
      url: '<?=$this->Html->url(array("action" => "remoteChangeOperatorStatus"))?>',
      data: {
        status: status
      },
      dataType: 'json',
      cache: false,
      success: function(json){

        if ( json.status == "<?=C_OPERATOR_ACTIVE?>" ) {
          chgOpStatusView("<?=C_OPERATOR_ACTIVE?>");
          sendRegularlyRequest("<?=C_OPERATOR_ACTIVE?>");
        }
        else {
          chgOpStatusView("<?=C_OPERATOR_PASSIVE?>");
          sendRegularlyRequest("<?=C_OPERATOR_PASSIVE?>");
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

  /* フッター設置ボタンの位置調整 */
  $(document).ready(function(){
    var target = document.getElementsByClassName("fotterBtnArea");
    if ( target.length === 0 ) return false;
    $(window).resize(function(){
      setCtrlArea(target[0].id);
    });
    setCtrlArea(target[0].id);
  });

  function setCtrlArea(id){
      var cont = document.getElementById('content');
      var bottom = cont.offsetHeight - cont.clientHeight;
      var right = cont.offsetWidth - cont.clientWidth;
      var left = 60;
      $('#' + id).animate({
          bottom: bottom,
          right: right,
          left: left
      }, 100);
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
