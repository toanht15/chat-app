<script type="text/javascript">
<!--
// -----------------------------------------------------------------------------
//  定数
// -----------------------------------------------------------------------------
var _access_type_guest = 1, _access_type_host = 2, userAgentChk, chatApi,
    socket = io.connect("<?=C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT?>"),
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

  chatApi = {
      tabId: null,
      userId: null,
      token: null,
      historyId: null,
      messageType: {
        customer: 1,
        company: 2
      },
      getMessage: function(obj){
        $("#sendMessage").attr('disabled', true);
        // チャットの取得
        emit('getChatMessage', {userId: obj.userId, tabId: obj.tabId});
      },
      createMessage: function(cs, val){
        var chatTalk = document.getElementById('chatTalk');
        var li = document.createElement('li');
        li.className = cs;
        li.textContent = val;
        chatTalk.appendChild(li);
        $('#chatTalk').animate({
          scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
        }, 100);
      },
      pushMessage: function() {
        var elm = document.getElementById('sendMessage');
        if ( isset(elm.value) ) {
          emit('sendChat', {
            token: this.token,
            historyId: this.historyId,
            tabId: $("#chatTalk").data("tabId"),
            userId: this.userId,
            chatMessage:elm.value,
            mUserId: <?= h($muserId)?>,
            messageType: chatApi.messageType.company
          });
        }
      },
      init: function(){
        // チャットメッセージ群の受信
        socket.on("chatMessageData", function(d){
          var obj = JSON.parse(d);
          if ( this.token !== obj.token ) return false;
          if ( isset(obj.chat.historyId) ) {
            chatApi.historyId = obj.chat.historyId;
            $("#sendMessage").attr('disabled', false);
          }

          for (var i = 0; i < obj.chat.messages.length; i++) {
            var chat = obj.chat.messages[i],
                cn = (chat.messageType === chatApi.messageType.customer) ? "sinclo_re" : "sinclo_se";
            chatApi.createMessage(cn, chat.message);
          }
        });
        // チャットメッセージ送信結果
        socket.on("sendChatResult", function(d){
          var obj = JSON.parse(d);
          var elm = document.getElementById('sendMessage'), cn;
          if ( obj.ret ) {
            if (obj.messageType === chatApi.messageType.customer) {
              cn = "sinclo_re";
            }
            else if (obj.messageType === chatApi.messageType.company) {
              cn = "sinclo_se";
              elm.value = "";
            }
            chatApi.createMessage(cn, obj.chatMessage);
          }
          else {
            alert('メッセージの送信に失敗しました。');
          }
        });
      }
  };

  chatApi.init();


})();

// -->
</script>
