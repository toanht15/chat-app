<script type = "text/javascript">

//はいをクリック
popupEventOverlap.closePopup = function(){
  var data=JSON.parse('<?php echo  $data; ?>');
  //履歴削除処理
  var url = "<?= $this->Html->url('/Histories/remoteDeleteChat') ?>";
  $.ajax({
    type: 'post',
    cache: false,
    data: {
      id:data.id,
      message:data.message
    },
    url: url,
    success: function(xhr){
      //履歴呼び直し
      $.ajax({
        type: 'GET',
        url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetChatLogs')) ?>",
        cache: false,
        data: {
          historyId: data.historyId
        },
        dataType: 'html',
        success: function(html){
          modalOpen.call(window, html, 'p-chat-logs', 'チャット履歴','moment');
          $(".p-chat-logs #popup-main ul").scrollTop(0);
        }
      });
    }
  });
}

//いいえをクリック
/*popupEvent.cancelClicked = function(){
  var data=JSON.parse('<?php echo  $data; ?>');
  //履歴呼び直し
    $.ajax({
      type: 'GET',
      url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetChatLogs')) ?>",
      cache: false,
      data: {
        historyId: data.historyId
      },
      dataType: 'html',
      success: function(html){
        var popupCansel = document.getElementById('popupCanselBtn');
        popupCansel.id = "popupCloseBtn";
        modalOpen.call(window, html, 'p-chat-logs', 'チャット履歴','moment');
        $(".p-chat-logs #popup-main ul").scrollTop(0);
      }
    });
}

//×ボタンをクリック
$("#popupCanselBtn").on('click', function(e){
     var data=JSON.parse('<?php echo  $data; ?>');
  //履歴呼び直し
    $.ajax({
      type: 'GET',
      url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetChatLogs')) ?>",
      cache: false,
      data: {
        historyId: data.historyId
      },
      dataType: 'html',
      success: function(html){
        var popupCansel = document.getElementById('popupCanselBtn');
        popupCansel.id = "popupCloseBtn";
        modalOpen.call(window, html, 'p-chat-logs', 'チャット履歴','moment');
        $(".p-chat-logs #popup-main ul").scrollTop(0);
      }
    });
});*/

</script>
<div class="form01">
  <!--　履歴削除  -->
    <br/>
    <div style="text-align:center;">
      <?= date('Y/m/d H:i:s',strtotime(json_decode($data,true)['created']));?>「<?= json_decode($data,true)['message']; ?>」の履歴を削除します。<br/><br/>
     <span style ="font-size: 1.1em;color: rgb(192, 0, 0);font-weight: bold;">一度削除されたメッセージは元に戻りません。</span><br/><br/>削除してよろしいですか？</font><br>
    </div>
    <br/>
  <!-- 履歴削除 -->
</div>