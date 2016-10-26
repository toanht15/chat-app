<script type="text/javascript">
  popupEvent.closePopup = function(){
    console.log('remote入ってる');
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
      url: "<?=$this->Html->url('/Histories/remoteSearchEntryForm')?>",
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
                console.log('大成功');
                 $('#historySearch').submit();
                 //$('input[name="start_day"]').val(start_day);
      }
    });
  };

popupEvent.customizeBtn = function(){
  location.href = "<?=$this->Html->url(array('controller' => 'Histories', 'action' => 'index'))?>";
     $.ajax({
      type: "post",
      url: "<?=$this->Html->url('/Histories/remoteClearEntryForm')?>",
      cache: false,
    });
  };

</script>
<?php echo $this->element('Histories/angularjs') ?>

    <?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => '/Histories']); ?>
        <?= $this->Form->input('datefilter',['label'=> false,'div' => false,'id' => 'dateperiod','name'=> 'datefilter']); ?>
        <div>
          <?= $this->Form->input('start_day',['label'=> false,'div' => false,'name'=> 'start_day','placeholder' => '開始日','value'=>$this->data[start_day]]); ?>
          <?= $this->Form->input('finish_day',['label'=> false,'div' => false,'name' => 'finish_day','placeholder' => '終了日','value'=>$this->data[finish_day]]); ?>
          <?= $this->Form->input('ip_address',['label'=>false,'div' => false,'placeholder' => 'ipアドレス','value'=>$this->data[ip_address]]) ?>
          <?= $this->Form->input('company_name',['label'=>false,'div' => false,'placeholder' => '会社名','value'=>$this->data[company_name]]) ?>
          <?= $this->Form->input('customer_name',['label'=>false,'div' => false,'placeholder' => '顧客名','value'=>$this->data[customer_name]]) ?>
          <?= $this->Form->input('telephone_number',['label'=>false,'div' => false,'placeholder' => '電話番号','value'=>$this->data[telephone_number]]) ?>
          <?= $this->Form->input('mail_address',['label'=>false,'div' => false,'placeholder' => 'メールアドレス','value'=>$this->data[mail_address]]) ?>
      <?= $this->Form->end(); ?>
