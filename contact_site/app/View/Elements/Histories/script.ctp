<script type="text/javascript">
//モーダル画面
function openSearchRefine(){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'remoteSearchCustomerInfo']) ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '高度な条件', 'moment');
    }
  });
}

//検索
function searchCustomerInfo(){
  $('<form/>', {action: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>", method: 'post'})
  .append($('<input/>', {type: 'hidden', name: "data[History][start_day]", value: userList.start}))
  .append($('<input/>', {type: 'hidden', name: "data[History][company_start_day]", value: userList.companyStartDay}))
  .append($('<input/>', {type: 'hidden', name: "data[History][period]", value:　userList.period}))
  .append($('<input/>', {type: 'hidden', name: "data[History][finish_day]", value: userList.finish}))
  .append($('<input/>', {type: 'hidden', name: "data[History][ip_address]", value: userList.ip}))
  .append($('<input/>', {type: 'hidden', name: "data[History][company_name]", value: userList.company}))
  .append($('<input/>', {type: 'hidden', name: "data[History][customer_name]", value: userList.customer}))
  .append($('<input/>', {type: 'hidden', name: "data[History][telephone_number]", value: userList.telephone}))
  .append($('<input/>', {type: 'hidden', name: "data[History][mail_address]", value: userList.mail}))
  .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][responsible_name]", value: userList.responsible}))
  .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][achievement_flg]", value: $('#achievement').text()}))
  .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][message]", value: userList.message}))
  .appendTo(document.body)
  .submit()
}

//セッションクリア
function sessionClear(){
  location.href = "<?=$this->Html->url(array('controller' => 'Histories', 'action' => 'clearSession'))?>";
}
</script>
