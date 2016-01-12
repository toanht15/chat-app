<script type="text/javascript">
<!--
// -----------------------------------------------------------------------------
//  定数
// -----------------------------------------------------------------------------
var _access_type_guest = 1, _access_type_host = 2, userAgentChk,
    socket = io.connect("http://socket.localhost:9090"),
    connectToken = null, receiveAccessInfoToken = null;

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

 function isset(a){
    return ( a !== undefined && a !== null && a !== "" );
  }

  // TODO ここをもとに、顧客側の存在確認コードも変える
  var sendRegularlyRequest = {
    time: 1500,
    id: null,
    ev: function(status){
      if ( status === <?=C_OPERATOR_ACTIVE?> ) {
        emit('sendOperatorStatus', {userId: <?=$muserId?>, active: true});
      }
      else {
        emit('sendOperatorStatus', {userId: <?=$muserId?>, active: false});
      }
    },
    start: function(){
      var opState = $('#operatorStatus');
      window.clearInterval(this.id);
      this.id = window.setInterval(function(){
        sendRegularlyRequest.ev(opState.data('status'));
      }, sendRegularlyRequest.time);
    },
    end: function(){
      window.clearInterval(this.id);
      emit('sendOperatorStatus', {userId: <?=$muserId?>, active: false});
    }
  };

  $(window).bind('focus', function(){
    sendRegularlyRequest.start();
  })
  .bind('blur', function(){
    sendRegularlyRequest.end();
  });

  $('window').bind('beforeunload', function(){
    sendRegularlyRequest.end();
  });

  socket.on("connect", function() {
    receiveAccessInfoToken = makeToken();
    sendRegularlyRequest.start();
    var data = {type: 'admin', data: {token: receiveAccessInfoToken}};
    emit('connected', data);
  });

})();
// -->
</script>
