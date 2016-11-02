<script type="text/javascript">
popupEvent.closePopup = function(){
  if (!$("#day_search").prop('checked')) {
    $("#dateperiod").prop("disabled", true);
    $('input[name="start_day"]').val("");
    $('input[name="finish_day"]').val("");
  }
  if ($("#g_chat").prop("checked")) {
    document.getElementById('historySearch').action = "Histories?isChat=true";
  }
  else {
    document.getElementById('historySearch').action = "Histories?isChat=false";
  }
  $('#historySearch').submit();
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
      <span><?= $this->Form->input('datefilter',['label'=> false,'class'=> $extinguish,'div' => false,'id' => 'dateperiod','name'=> 'datefilter']); ?><label><input type="checkbox" id="day_search" <?= $checked ?>><span>指定する</span></label>
    </li>
    <?= $this->Form->hidden('start_day',['label'=> false,'div' => false,'name'=> 'start_day','value'=>$this->data['start_day']]); ?>
    <?= $this->Form->hidden('finish_day',['label'=> false,'div' => false,'name' => 'finish_day','value'=>$this->data['finish_day']]); ?>
    <li>
      <p><span>ipアドレス</span></p>
      <span><?= $this->Form->input('ip_address',['label'=>false,'div' => false,'name' => 'ip_address','value'=>$this->data[ip_address]]) ?></span>
    </li>
    <li>
      <p><span>会社名</span></p>
      <span><?= $this->Form->input('company_name',['label'=>false,'div' => false,'name' => 'company_name','value'=>$this->data[company_name]]) ?></span>
    </li>
    <li>
    <p><span>名前</span></p>
      <span><?= $this->Form->input('customer_name',['label'=>false,'div' => false,'name' => 'customer_name','value'=>$this->data[customer_name]]) ?></span>
    </li>
    <li>
      <p><span>電話番号</span></p>
      <span><?= $this->Form->input('telephone_number',['label'=>false,'div' => false,'name' => 'telephone_number','value'=>$this->data[telephone_number]]) ?></span>
    </li>
    <li>
      <p><span>メールアドレス</span></p>
      <span><?= $this->Form->input('mail_address',['label'=>false,'div' => false,'name' => 'mail_address','value'=>$this->data[mail_address]]) ?></span>
    </li>
  </ul>
<?= $this->Form->end(); ?>