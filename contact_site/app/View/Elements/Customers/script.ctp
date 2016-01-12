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
    ev: function(){
      window.clearTimeout(this.id);
      this.id = window.setTimeout(function(){
        var opState = $('#operatorStatus');

        if ( opState.data('status') === <?=C_OPERATOR_ACTIVE?> ) {
          emit('sendOperatorStatus', {userId: <?=$muserId?>, active: true});
        }
        else {
          emit('sendOperatorStatus', {userId: <?=$muserId?>, active: false});
        }
        sendRegularlyRequest.ev();
      }, sendRegularlyRequest.time);

    },
    start: function(){
      sendRegularlyRequest.ev();
    },
    end: function(){
      window.clearInterval(this.id);
      emit('sendOperatorStatus', {userId: <?=$muserId?>, active: false});
    }
  };

  $(window).bind('beforeunload', function(){
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
