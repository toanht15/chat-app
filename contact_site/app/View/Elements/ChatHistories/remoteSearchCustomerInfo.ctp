<script type="text/javascript">
popupEvent.closePopup = function(){
  popupEvent.close();
  loading.load.start();
  if ($("#g_chat").prop("checked")) {
    document.getElementById('historySearch').action = "ChatHistories?isChat=true";
  }
  else {
    document.getElementById('historySearch').action = "ChatHistories?isChat=false";
  }

  $('#historySearch').submit();
};

</script>
  <?=  $this->Form->create('History',['id' => 'historySearch', 'type' => 'post','url' => ['controller' => 'Histories','action' => 'index']]); ?>
  <ul>
    <?= $this->Form->hidden('start_day',['label'=> false,'div' => false]); ?>
    <?= $this->Form->hidden('finish_day',['label'=> false,'div' => false]); ?>
    <?= $this->Form->hidden('period',['label'=> false,'div' => false]); ?>
    <li>
      <p><span>種別</span></p>
      <span><?= $this->Form->input('chat_type',['label'=>false,'empty' => ' ', 'options' => $chatType,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>ipアドレス</span></p>
      <span><?= $this->Form->input('ip_address',['label'=>false,'div' => false]) ?></span>
    </li>
    <?php
    for($i = 0; $i < count($customerInformationList); $i++) {
      if(strcmp($customerInformationList[$i]['input_type'], "2") === 0) {
        echo '<li class="auto-height">';
        echo '  <p><span>'.$customerInformationList[$i]['item_name'].'</span></p>';
      } else {
        echo '<li>';
        echo '  <p><span>'.$customerInformationList[$i]['item_name'].'</span></p>';
      }
      echo '  <span>'.$this->htmlEx->visitorSearchInput($customerInformationList[$i], true, false, $data).'</span>';
      echo '</li>';
    }
    ?>
    <li>
      <p><span>キャンペーン</span></p>
      <span><?= $this->Form->input('campaign',['label'=>false,'empty' => ' ', 'options' => $campaign,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>チャット送信ページ</span></p>
      <span><?= $this->Form->input('THistoryChatLog.send_chat_page',['label'=>false,'div' => false]) ?></span>
    </li>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <li>
        <p><span>チャット担当者</span></p>
        <span><?= $this->Form->input('THistoryChatLog.responsible_name',['label'=>false,'div' => false]) ?></span>
      </li>
      <li>
        <p><span>成果</span></p>
        <span><label><?= $this->Form->input('THistoryChatLog.achievement_flg',['type' => 'select', 'empty' => ' ', 'options' => array_merge($achievementType, array("3" => '途中離脱')), 'legend' => false, 'separator' => '</label><br><label>', 'label'=>false,'div' => false]) ?></label></span>
      </li>
      <li>
        <p><span>チャット内容</span></p>
        <span><?= $this->Form->input('THistoryChatLog.message',['label'=>false,'div' => false]) ?></span>
      </li>
    <?php endif; ?>
  </ul>
<?= $this->Form->end(); ?>