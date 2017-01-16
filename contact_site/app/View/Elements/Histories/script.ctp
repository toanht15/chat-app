<script type="text/javascript">
function openSearchRefine(){
  var importantDate = $('#mainDatePeriod').val();
      var d = new Date($('#mainDatePeriod').data('daterangepicker').startDate);
      var startDate = d.getFullYear() + '/' + (d.getMonth() + 1) + '/' + d.getDate();
      var d2 = new Date($('#mainDatePeriod').data('daterangepicker').endDate);
      var endDate = d2.getFullYear() + '/' + (d2.getMonth() + 1) + '/' + d2.getDate();

  $.ajax({
    type: 'post',
    dataType: 'html',
    data: {
            startday: startDate,
            finishday: endDate
          },
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'remoteOpenEntryForm']) ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '絞り込み検索', 'moment');
    }
  });
}

//view側の検索ボタン
function searchRefine(){
  //モーダルの検索ボタンと被らないようにする
  if ( !$("#popup.popup-on #popup-frame ").is(".p-thistory-entry") ) {
    //前の検索条件を日程以外全て引き継ぐ
    var start = $('#start').val();
    var end = $('#end').val();
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
}
</script>
