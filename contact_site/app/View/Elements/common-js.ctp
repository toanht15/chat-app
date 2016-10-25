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

  // エスケープ用
  // http://qiita.com/saekis/items/c2b41cd8940923863791
  function escape_html (string) {
    if(typeof string !== 'string') {
      return string;
    }
    return string.replace(/[&'`"<>]/g, function(match) {
      return {
        '&': '&amp;',
        "'": '&#x27;',
        '`': '&#x60;',
        '"': '&quot;',
        '<': '&lt;',
        '>': '&gt;',
      }[match]
    });
  }

  function ajaxTimeout(){
    modalOpen.call(window, "タイムアウトしました", 'p-alert', 'アラート');
    popupEvent.closeNoPopup = function(){
      location.href = "<?=$this->Html->url(['controller' => 'Login', 'action'=>'logout'])?>";
    };
  }

  /* Angularの描画 */
  !function(){
    var bootTimer = null;
    if ( 'angular' in window ) {
      // 500ミリ秒後、描画が正常に行われていなかった場合
      bootTimer = setInterval(function(){
        if ( angular.element('*[ng-cloak]').length > 0 ) {
          // 描画し直す
          angular.bootstrap(document, ['sincloApp']);
          // 再接続
          socket.disconnect();
          socket.connect();
        }
        else {
          clearInterval(bootTimer);
          bootTimer = null;
        }
      }, 500);
    }
  }();

  $(document).ready(function(){
    /* フッター設置ボタンの位置調整 */
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
