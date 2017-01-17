<script type="text/javascript">
//モーダル画面
function openSearchRefine(){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'remoteOpenEntryForm']) ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '絞り込み検索', 'moment');
    }
  });
}

//view側の検索ボタン
function searchRefine(){
  $('#mainDatePeriod').on('apply.daterangepicker', function(ev, picker) {
  $('#startDay').text(picker.startDate.format('YYYY/MM/DD'));
  $('#finishDay').text(picker.endDate.format('YYYY/MM/DD'));
  //モーダルの検索ボタンと被らないようにする
  if ( !$("#popup.popup-on #popup-frame ").is(".p-thistory-entry") ) {
    //前の検索条件を日程以外全て引き継ぐ
    var start = $('#startDay').text();
    var end = $('#finishDay').text();
    var ip = $('#ip').text();
    var company = $('#company').text();
    var customer = $('#customer').text();
    var telephone = $('#telephone').text();
    var mail = $('#mail').text();
    var responsible = $('#responsible').text();
    var message = $('#message').text();
    var date = start　+ ' - ' + end + '　　';
    $.ajax({
      type: "POST",
      url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>",
      data: {
        start_day : start,
        finish_day : end,
        ip_address : ip,
        company_name : company,
        customer_name : customer,
        telephone_number : telephone,
        mail_address : mail,
        responsible_name : responsible,
        message : message
      },
      dataType: "html",
      success: function(){
        location.href = window.location;
      }
    });
  }
    });
}
</script>
