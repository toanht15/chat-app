<script type="text/javascript">
popupEvent.closePopup = function(){
  if (!$("#day_search").prop('checked')) {
    $("#dateperiod").prop("disabled", false);
    //$('#HistoryStartDay').val("");
    //$('#HistoryFinishDay').val("");
  }
  if ($("#g_chat").prop("checked")) {
    document.getElementById('historySearch').action = "Histories?isChat=true";
  }
  else {
    document.getElementById('historySearch').action = "Histories?isChat=false";
  }
  var start = $('#HistoryStartDay').val();
  var end = $('#HistoryFinishDay').val();
  var date = start　+ ' - ' + end + '　　';
   $('#mainDatePeriod').text(date);
   console.log(date);
  $('#historySearch').submit();
  $(window).load(function () {
   $('#mainDatePeriod').text(start);
});
 //$('#mainDatePeriod').text(date);
};

popupEvent.customizeBtn = function(){
  location.href = "<?=$this->Html->url(array('controller' => 'Histories', 'action' => 'clearSession'))?>";
};

</script>
<?php echo $this->element('Histories/angularjs') ?>
<?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => ['controller' => 'Histories','action' => 'index']]); ?>
  <ul>
    <li>
      <p><span>日付</span></p>
      <?php
        $extinguish = '';
        $checked = 'checked';
        if(!isset($this->data['datefilter'])) {
          $extinguish = 'extinguish';
          $checked = '';
        }
      ?>
      <span><?= $this->Form->input('datefilter',['label'=> false,'div' => false,'id' => 'dateperiod','name'=> 'datefilter']); ?><label><input type="checkbox" id="day_search" <?= $checked ?>><span>指定しない</span></label>
    </li>
    <?= $this->Form->hidden('start_day',['label'=> false,'div' => false]); ?>
    <?= $this->Form->hidden('finish_day',['label'=> false,'div' => false]); ?>
    <li>
      <p><span>ipアドレス</span></p>
      <span><?= $this->Form->input('ip_address',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>会社名</span></p>
      <span><?= $this->Form->input('company_name',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
    <p><span>名前</span></p>
      <span><?= $this->Form->input('customer_name',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>電話番号</span></p>
      <span><?= $this->Form->input('telephone_number',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>メールアドレス</span></p>
      <span><?= $this->Form->input('mail_address',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>担当者</span></p>
      <span><?= $this->Form->input('responsible_name',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>チャット内容</span></p>
      <span><?= $this->Form->input('message',['label'=>false,'div' => false]) ?></span>
    </li>
  </ul>
<?= $this->Form->end(); ?>