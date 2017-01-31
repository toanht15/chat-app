<script type="text/javascript">
popupEvent.closePopup = function(){
  "use strict;"
//var userList = <?php echo json_encode($responderList);?>;
//var data = $.parseJSON(jsonstr);

console.log(userList.company);

  //全期間の場合
  /*if (!$("#day_search").prop('checked')) {
    $("#dateperiod").prop("disabled", true);
    $('##ip').val("");
    $('##HistoryFinishDay').val("");
  }*/
  if ($("#g_chat").prop("checked")) {
    document.getElementById('historySearch').action = "Histories?isChat=true";
  }
  else {
    document.getElementById('historySearch').action = "Histories?isChat=false";
  }

  userList.ip = $('#HistoryIpAddress').val();
  userList.company = $('#HistoryCompanyName').val();
  userList.customer = $('#HistoryCustomerName').val();
  userList.telephone = $('#HistoryTelephoneNumber').val();
  userList.mail = $('#HistoryMailAddress').val();
  userList.responsible = $('#THistoryChatLogResponsibleName').val();
  userList.message = $('#THistoryChatLogMessage').val();
  console.log(userList.ip);
  /*var period_day = $('.active').text();
  //カスタム検索の場合
  if(period_day.match(/[^0-9]/) == null){
    $('#HistoryPeriod').val("");
  }
  //それ以外の検索の場合
  else{
    $('#HistoryPeriod').val(period_day);
  }*/
  $.ajax({
    type: 'post',
    data: {
      "data[History][ip_address]": $('#HistoryIpAddress').val(),
      "data[History][company_name]": $('#HistoryCompanyName').val(),
      "data[History][customer_name]": $('#HistoryCustomerName').val(),
      "data[History][telephone_number]": $('#HistoryTelephoneNumber').val(),
      "data[History][mail_address]": $('#HistoryMailAddress').val(),
      "data[THistoryChatLog][responsible_name]": $('#THistoryChatLogResponsibleName').val(),
      "data[THistoryChatLog][message]": $('#THistoryChatLogMessage').val(),
          },
    dataType: 'text',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'aiueo']) ?>",
    success: function(data){
    //return popupEvent.close();
            //location.href = location.href;
    }
  });
  return popupEvent.close();
  //return popupEvent.close();
  //$('#historySearch').submit();
};

popupEvent.customizeBtn = function(){
  //セッションクリア
  location.href = "<?=$this->Html->url(array('controller' => 'Histories', 'action' => 'clearSession'))?>";
};

</script>
<?php echo $this->element('Histories/angularjs') ?>
<?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => ['controller' => 'Histories','action' => 'index']]); ?>
  <ul>
  <?php /*
    <li>
      <p><span>日付</span></p>
      <?php
        $extinguish = '';
        $checked = 'checked';
        //全期間の場合
        if(!isset($this->data['datefilter'])) {
          $extinguish = 'extinguish';
          $checked = '';
        }
      ?>
      <span><?= $this->Form->input('datefilter',['label'=> false,'class'=> $extinguish,'div' => false,'id' => 'dateperiod','name'=> 'datefilter']); ?><label class="pointer"><input type="checkbox" id="day_search" <?= $checked ?>><span>指定する</span></label>
    </li>
    */?>
    <?= $this->Form->hidden('start_day',['label'=> false,'div' => false]); ?>
    <?= $this->Form->hidden('finish_day',['label'=> false,'div' => false]); ?>
    <?= $this->Form->hidden('period',['label'=> false,'div' => false]); ?>
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
<?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
    <li>
      <p><span>チャット担当者</span></p>
      <span><?= $this->Form->input('THistoryChatLog.responsible_name',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>成果</span></p>
      <span><label><?= $this->Form->input('THistoryChatLog.achievement_flg',['type' => 'select', 'empty' => ' - ', 'options' => $achievementType, 'legend' => false, 'separator' => '</label><br><label>', 'label'=>false,'div' => false]) ?></label></span>
    </li>
    <li>
      <p><span>チャット内容</span></p>
      <span><?= $this->Form->input('THistoryChatLog.message',['label'=>false,'div' => false]) ?></span>
    </li>
<?php endif; ?>
  </ul>
<?= $this->Form->end(); ?>