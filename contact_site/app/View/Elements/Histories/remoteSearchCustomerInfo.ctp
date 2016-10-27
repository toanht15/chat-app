<script type="text/javascript">
  popupEvent.closePopup = function(){
    var start_day = document.getElementById('HistoryStartDay').value;
    var finish_day = document.getElementById('HistoryFinishDay').value;
    var ip_address = document.getElementById('HistoryIpAddress').value;
    var company_name = document.getElementById('HistoryCompanyName').value;
    var customer_name = document.getElementById('HistoryCustomerName').value;
    var telephone_number = document.getElementById('HistoryTelephoneNumber').value;
    var mail_address = document.getElementById('HistoryMailAddress').value;
    console.log(start_day);
    $.ajax({
      type: "post",
      url: "<?=$this->Html->url('/Histories/remoteSearchForm')?>",
      data: {
        start_day: start_day,
        finish_day: finish_day,
        ip_address: ip_address,
        company_name: company_name,
        customer_name: customer_name,
        telephone_number: telephone_number,
        mail_address: mail_address,
      },
      cache: false,
      dataType: "json",
      success: function(data){
        $('#historySearch').submit();
      }
    });
  };

popupEvent.customizeBtn = function(){
  location.href = "<?=$this->Html->url(array('controller' => 'Histories', 'action' => 'index'))?>";
  $.ajax({
   type: "post",
   url: "<?=$this->Html->url('/Histories/remoteClearSession')?>",
   cache: false,
  });
};

</script>
<?php echo $this->element('Histories/angularjs') ?>
<?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => '/Histories']); ?>
  <ul>
    <li>
      <p><span>日付</span></p>
      <span><?= $this->Form->input('datefilter',['label'=> false,'div' => false,'id' => 'dateperiod','name'=> 'datefilter']); ?></span>
    </li>
    <?= $this->Form->hidden('start_day',['label'=> false,'div' => false,'name'=> 'start_day','value'=>$this->data['start_day']]); ?>
    <?= $this->Form->hidden('finish_day',['label'=> false,'div' => false,'name' => 'finish_day','value'=>$this->data['finish_day']]); ?>
    <li>
      <p><span>ipアドレス</span></p>
      <span><?= $this->Form->input('ip_address',['label'=>false,'div' => false,'value'=>$this->data[ip_address]]) ?></span>
    </li>
    <li>
      <p><span>会社名</span></p>
      <span><?= $this->Form->input('company_name',['label'=>false,'div' => false,'value'=>$this->data[company_name]]) ?></span>
    </li>
    <li>
    <p><span>名前</span></p>
      <span><?= $this->Form->input('customer_name',['label'=>false,'div' => false,'value'=>$this->data[customer_name]]) ?></span>
    </li>
    <li>
      <p><span>電話番号</span></p>
      <span><?= $this->Form->input('telephone_number',['label'=>false,'div' => false,'value'=>$this->data[telephone_number]]) ?></span>
    </li>
    <li>
      <p><span>メールアドレス</span></p>
      <span><?= $this->Form->input('mail_address',['label'=>false,'div' => false,'value'=>$this->data[mail_address]]) ?></span>
    </li>
  </ul>
<?= $this->Form->end(); ?>