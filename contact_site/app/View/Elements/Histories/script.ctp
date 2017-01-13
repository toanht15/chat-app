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

function openSearchRefine2(){
  if ( !$("#popup.popup-on #popup-frame").is(".p-thistory-entry") ) {
  var start = $('#start').val();
  var end = $('#end').val();
  var date = start　+ ' - ' + end + '　　';
    $.ajax({
      type: "POST",
      url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>",
      data: {
        start_day : start,
        finish_day : end
      },
      dataType: "html",
      success: function(){
       location.href = window.location;
      }
    });
  }
}
</script>
