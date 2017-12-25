<script type = "text/javascript">

//はいをクリック
popupEventOverlap.closePopup = function(){
  var data=JSON.parse('<?php echo  $data; ?>');
  //履歴削除処理
  var url = "<?= $this->Html->url('/ChatHistories/remoteDeleteChatSentence') ?>";
  $.ajax({
    type: 'post',
    cache: false,
    data: {
      id:data.id,
      historyId:data.historyId,
      message:data.message
    },
    url: url,
    success: function(historyId){
      location.href = "<?= $this->Html->url(['controller' => 'ChatHistories', 'action' => 'index']) ?>?id="+ historyId;
    }
  });
}
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