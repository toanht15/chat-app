<script type="text/javascript">
<!--
'use strict';
// -----------------------------------------------------------------------------
//  定数
// -----------------------------------------------------------------------------
var _access_type_guest = 1, _access_type_host = 2, userAgentChk, notificationStatus = false, socket,
    connectToken = null, receiveAccessInfoToken = null, isset, myUserId = <?= h($muserId)?>;

if ( window.hasOwnProperty('io') ) {
  socket = io.connect("<?=C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT?>");
}


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
  };

  isset = function(a){
    return ( a !== undefined && a !== null && a !== "" );
  };

  // TODO ここをもとに、顧客側の存在確認コードも変える
  window.sendRegularlyRequest = function(status){
    if ( status === undefined ) {
      var opState = $('#changeOpStatus');
      var status = opState.data('status');
    }
    if ( status == <?=C_OPERATOR_ACTIVE?> ) {
      emit('sendOperatorStatus', {userId: myUserId, active: true});
    }
    else {
      emit('sendOperatorStatus', {userId: myUserId, active: false});
    }
  };

    window.chgOpStatusView = function(status){
      var opState = $('#changeOpStatus');
      if (status == "<?=C_OPERATOR_ACTIVE?>") {
        opState.data('status', <?=C_OPERATOR_ACTIVE?>).prop("class", "redBtn btn-shadow").text('離席中にする');
        $('#operatorStatus').prop("class", "opWait").children('span').text('待機中');
      }
      else {
        opState.data('status', <?=C_OPERATOR_PASSIVE?>).prop("class", "blueBtn btn-shadow").text('待機中にする');
        $('#operatorStatus').prop("class", "opStop").children('span').text('離席中');
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

  socket.on("connect", function() {
    receiveAccessInfoToken = makeToken();
    var data = {
        type: 'admin',
        data: {
            token: receiveAccessInfoToken,
            authority: <?=$userInfo['permission_level']?>,
            userId: '<?=$muserId?>'
        }
    };
<?php if ($widgetCheck): ?>
    data.data['status'] = '<?=$opStatus?>';

<?php endif; ?>
    emit('connected', data);
  });

  function changePositionOfPopup(){
    // スクロールを使用するか
    var subCon = document.getElementById('sub_contents');
    // 詳細画面が表示されている場合
    if ( document.getElementById('customer_sub_pop').style.display === "block" ) {

      /* position-top */
      if ( $("#sub_contents").css("top").indexOf('px') < 0 ) return false;
      var subConTop = Number($("#sub_contents").css("top").replace("px", ""));

      // ポップアップが画面外（上）に潜った場合の対処
      var calc = subConTop - 60;
      if ( calc < 0 ) {
        subCon.style.top = "60px";
      }

      // ポップアップが画面外（下）に潜った場合の対処
      var subHeader = document.getElementById('cus_info_header'); // モーダル内のヘッダー
      var calc = window.innerHeight - (subConTop + Number(subHeader.offsetHeight));
      if ( calc < 0 ) {
        subCon.style.top = window.innerHeight - Number(subHeader.offsetHeight) + "px";
      }

      /* position-left */
      if ( $("#sub_contents").css("left").indexOf('px') < 0 ) return false;

      var subConLeft = Number($("#sub_contents").css("left").replace("px", ""));
      // ポップアップが画面外（左）に潜った場合の対処
      if ( subConLeft < 0 ) {
        subCon.style.left = "0";
      }

      // ポップアップが画面外（右）に潜った場合の対処
      var sideBar = document.getElementById('sidebar-main');
      var widthArea = window.innerWidth - Number(sideBar.offsetWidth); // 有効横幅
      if ( (widthArea - subConLeft) < 50 ) {
        subCon.style.left = widthArea - 80 + "px";
      }
    }
  }

  function changeSizeOfTbl(){
    // リアルタイムモニタの高さを指定
    $("#list_body").height($(window).height() - $("#customer_list").offset().top - 60);
  }


  $(document).ready(function(){
    if ($("#customer_list").length === 0) return false;
    if ($("#sub_contents").length === 0) return false;
    if ($("#customer_sub_pop").length === 0) return false;
    changeSizeOfTbl();
    changePositionOfPopup();

    $("#sub_contents").draggable({
      // containment: "#content",
      scroll: false,
      cancel: "#cus_info_contents",
      stop:function(event, ui) {
        changePositionOfPopup();
      }
    });

    $(document).on("click", "#showChatTab > li", function(e){
      var className = $(this).data('type');
      $("#showChatTab > li").removeClass("on");
      if ( className === "oldChat" ) {
        $.ajax({
          type: 'GET',
          url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetChatLogs')) ?>",
          cache: false,
          data: {
            tabId: $scope.detail.tabId,
            userId: $scope.detail.userId,
          },
          dataType: 'html',
          success: function(html){
            modalOpen.call(window, html, 'p-chat-logs', 'チャット履歴');
          }
        });
      }
      else {
        className = "currentChat";
      }
      $("#showChatTab > li[data='" + className + "']").addClass("on");
      $("#chatContent > section").removeClass("on");
      $("#chatContent > #" + className).addClass("on");
    });

  });

  $(window).resize(function(){
    changeSizeOfTbl();
    changePositionOfPopup();
  });

})();


// -->
</script>
