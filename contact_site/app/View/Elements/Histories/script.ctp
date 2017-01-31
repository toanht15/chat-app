<script type="text/javascript">

'use strict';

var userList = <?php echo json_encode($responderList);?>;
//var data = $.parseJSON(jsonstr);


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

function openSearchRefin2(){

  console.log(userList.ip);

    var search_day = $('.active').val();
    //今日
    var today = moment();
    today = today.format("YYYY/MM/DD");
    //昨日
    var yesterday = moment().subtract(1, 'days');
    yesterday = yesterday.format("YYYY/MM/DD");
    //過去一週間
    var oneWeekAgo = moment().subtract(6, 'days');
    oneWeekAgo = oneWeekAgo.format("YYYY/MM/DD");
    //過去一か月間
    var oneMonthAgo = moment().subtract(30, 'days');
    oneMonthAgo = oneMonthAgo.format("YYYY/MM/DD");
    //過去一ヵ月間
    var thisMonth = moment().startOf('month');
    thisMonth = thisMonth.format("YYYY/MM/DD");
    //今月
    var thisMonth = moment().startOf('month');
    thisMonth = thisMonth.format("YYYY/MM/DD");
    //先月
    var lastMonth = moment().subtract(1, 'month').startOf('month');
    lastMonth = lastMonth.format("YYYY/MM/DD");
    //全期間
    var allDay = $('#companyStart').text();

    //今日
    if(search_day == today){
      search_day = "今日";
    }
    //昨日
    else if(search_day == yesterday){
      search_day = "昨日";
    }
    //過去一週間
    else if(search_day == oneWeekAgo){
      search_day = "過去一週間";
    }
    //過去一か月間
    else if(search_day == oneMonthAgo){
      search_day = "過去一ヵ月間";
    }
    //今月
    else if(search_day == thisMonth){
      search_day = "今月";
    }
    //先月
    else if(search_day == lastMonth){
      search_day = "先月";
    }
    //全期間
    else if(search_day == allDay){
      search_day = "全期間";
    }
    //カスタム
    else {
      search_day = "カスタム";
    }
    console.log(userList.ip);

    if($('#HistoryIpAddress').val() != null || $('#HistoryCompanyName').val() != null || $('#HistoryCustomerName').val() !=  null || $('#HistoryTelephoneNumber').val() != null || $('#HistoryMailAddress').val() != null  ){

      $('<form/>', {action: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>", method: 'post'})
      .append($('<input/>', {type: 'hidden', name: "data[datefilter]", value: $('#startDay').text()+ '-' +$('#finishDay').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][start_day]", value: userList.start}))
      .append($('<input/>', {type: 'hidden', name: "data[History][company_start_day]", value: userList.companyStart}))
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
    else {
       $('<form/>', {action: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>", method: 'post'})
      .append($('<input/>', {type: 'hidden', name: "data[datefilter]", value: $('#startDay').text()+ '-' +$('#finishDay').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][start_day]", value: userList.start}))
      .append($('<input/>', {type: 'hidden', name: "data[History][company_start_day]", value: userList.companyStart}))
      .append($('<input/>', {type: 'hidden', name: "data[History][period]", value:　userList.period}))
      .append($('<input/>', {type: 'hidden', name: "data[History][finish_day]", value: userList.finish}))
      .append($('<input/>', {type: 'hidden', name: "data[History][ip_address]", value:  userList.ip}))
      .append($('<input/>', {type: 'hidden', name: "data[History][company_name]", value: userList.company}))
      .append($('<input/>', {type: 'hidden', name: "data[History][customer_name]", value: userList.customer}))
      .append($('<input/>', {type: 'hidden', name: "data[History][telephone_number]", value: userList.telephone}))
      .append($('<input/>', {type: 'hidden', name: "data[History][mail_address]", value: 　userList.mail}))
      .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][responsible_name]", value: userList.responsible}))
      .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][achievement_flg]", value: $('#achievement').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][message]", value: userList.message}))
      .appendTo(document.body)
      .submit()
    }

    /*else {
      var ip = "";
      var company = "";
      var customer = "";
      var telephone = "";
      var mail = "";
    }
      //form作成
      $('<form/>', {action: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>", method: 'post'})
      .append($('<input/>', {type: 'hidden', name: "data[datefilter]", value: $('#startDay').text()+ '-' +$('#finishDay').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][start_day]", value: $("#HistoryStartDay").val()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][company_start_day]", value: $('#companyStart').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][period]", value:　$("#HistoryPeriod").val()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][finish_day]", value: $("#HistoryFinishDay").val()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][ip_address]", value:  ip}))
      .append($('<input/>', {type: 'hidden', name: "data[History][company_name]", value: company}))
      .append($('<input/>', {type: 'hidden', name: "data[History][customer_name]", value: customer}))
      .append($('<input/>', {type: 'hidden', name: "data[History][telephone_number]", value: telephone}))
      .append($('<input/>', {type: 'hidden', name: "data[History][mail_address]", value: mail}))
      .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][responsible_name]", value: $('#responsible').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][achievement_flg]", value: $('#achievement').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[THistoryChatLog][message]", value: $('#message').text()}))
      .appendTo(document.body)
      .submit()*/
}
</script>
