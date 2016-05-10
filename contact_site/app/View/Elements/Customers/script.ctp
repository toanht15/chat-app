<script type="text/javascript">
<!--
// -----------------------------------------------------------------------------
//  定数
// -----------------------------------------------------------------------------
var _access_type_guest = 1, _access_type_host = 2, userAgentChk, notificationStatus = false,
    socket = io.connect("<?=C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT?>"),
    connectToken = null, receiveAccessInfoToken = null, isset, myUserId = <?= h($muserId)?>;

(function(){
  // -----------------------------------------------------------------------------
  //  関数
  // -----------------------------------------------------------------------------
  function emit(ev, d){
    var obj = {};
    if ( typeof(d) !== "object" ) {
      obj = JSON.parse(d);
    }
    else {
      obj = d;
    }
    obj.siteKey = "<?=$siteKey?>";
    var status = $('#operatorStatus').data('status');
    var data = JSON.stringify(obj);
    socket.emit(ev, data);
  }

  function parse (data) {
    return JSON.parse(data);
  }

  var makeToken = function(){
    var n = 20,
        str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQESTUVWXYZ1234567890",
        strLen = str.length,
        token = "";
        for(var i=0; i<n; i++){
          token += str[Math.floor(Math.random()*strLen)];
        }
        return token;
  }

 isset = function(a){
    return ( a !== undefined && a !== null && a !== "" );
  };

  // TODO ここをもとに、顧客側の存在確認コードも変える
  var sendRegularlyRequest = {
    time: 1500,
    id: null,
    ev: function(){
      window.clearTimeout(this.id);
      this.id = window.setTimeout(function(){
        var opState = $('#operatorStatus');

        if ( opState.data('status') === <?=C_OPERATOR_ACTIVE?> ) {
          emit('sendOperatorStatus', {userId: myUserId, active: true});
        }
        else {
          emit('sendOperatorStatus', {userId: myUserId, active: false});
        }
        sendRegularlyRequest.ev();
      }, sendRegularlyRequest.time);

    },
    start: function(){
      sendRegularlyRequest.ev();
    },
    end: function(){
      window.clearInterval(this.id);
      emit('sendOperatorStatus', {userId: myUserId, active: false});
    }
  };

  // http://qiita.com/kidatti/items/10a6a033ed0b84619d81
  // デスクトップ通知が利用できる場合
  var Notification = window.Notification || window.mozNotification || window.webkitNotification;
  if (Notification) {

    // Permissionの確認
    if (Notification.permission === 'granted') {
      // 許可されている場合はNotificationで通知
      notificationStatus = true;

    }
    else if (Notification.permission === 'denied') {
      notificationStatus = false;
    }
    else if (Notification.permission === 'default') {

      // 許可が取れていない場合はNotificationの許可を取る
      Notification.requestPermission(function(result) {
        if (result === 'granted') {
          notificationStatus = true;
        }
      });
    }
  }

  $(window).bind('beforeunload', function(){
<?php if ( $widgetCheck ): ?>
    sendRegularlyRequest.end();
<?php endif; ?>
  });
  socket.on("connect", function() {
    receiveAccessInfoToken = makeToken();
<?php if ( $widgetCheck ): ?>
    sendRegularlyRequest.start();
<?php endif; ?>
    var data = {type: 'admin', data: {token: receiveAccessInfoToken}};
    emit('connected', data);
  });

})();

$(document).ready(function(){
  chatApi.init();
});

// -->
</script>
