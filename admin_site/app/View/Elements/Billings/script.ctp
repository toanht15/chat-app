<script type="text/javascript">
  $(function() {
    $('#csvExport').on('click', function(e) {
      e.stopPropagation();
      var form = document.createElement('form');
      form.action = '/Billings/exportCV';
      form.method = 'post';

      var q = document.createElement('input');
      q.value = $('#BillingsTargetDate').val();
      q.name = 'targetDate';

      form.appendChild(q);
      document.body.appendChild(form);
      form.submit();
      return false;
    });
  });
</script>