<script type="text/javascript">
  popupEvent.closePopup = function(){
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
      <p><span>日付 <input type="checkbox" id="day_search" 'onclick'></span></p>
      <span><?= $this->Form->input('datefilter',['label'=> false,'div' => false,'id' => 'dateperiod','name'=> 'datefilter']); ?></span>
    </li>
    <?= $this->Form->hidden('start_day',['label'=> false,'div' => false,'name'=> 'start_day','value'=>$this->data['start_day']]); ?>
    <?= $this->Form->hidden('finish_day',['label'=> false,'div' => false,'name' => 'finish_day','value'=>$this->data['finish_day']]); ?>
    <li>
      <p><span>ipアドレス</span></p>
      <span><?= $this->Form->input('ip_address',['label'=>false,'div' => false,'name' => 'ip_address','value'=>h($this->data[ip_address])]) ?></span>
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