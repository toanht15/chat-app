<script type = "text/javascript">

//はいをクリック
popupEventOverlap.closePopup = function(){
  var data=JSON.parse('<?php echo  $data; ?>');
  //履歴削除処理
  var url = "<?= $this->Html->url('/ChatHistories/remoteDeleteChat') ?>";
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
        url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'remoteGetChatLogs')) ?>",
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
</script>
<div class="form01">
  <!--　履歴削除  -->
    <br/>
    <div style="text-align:center;">「選択されたチャット履歴を削除します。<br/><br/>
     <span style ="font-size: 1.1em;color: rgb(192, 0, 0);font-weight: bold;">一度削除されたチャット履歴は元に戻りません。</span><br/><br/>削除してよろしいですか？</font><br>
    </div>
    <br/>
  <!-- 履歴削除 -->
</div>