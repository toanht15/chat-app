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

    // 会社名クリックのログイン処理
    var popWinObj;
    $('.loginLink').on('click', function(e) {
      e.preventDefault();
      var mailAddress = $(this).parents('tr').find('.adminId').text();
      var pass = $(this).parents('tr').find('.adminPass').text();
      $('#postjump').remove();
      if ((popWinObj) && (!popWinObj.closed)) {
        popWinObj.close();
      }
      popWinObj = window.open('about:blank', 'sincloViewWindow');
      var html = '<form method="post" action="http://contact.sinclo/Login/login" id="postjump" target="sincloViewWindow" style="display: none;">';
      html += '<input type="hidden" name="data[MUser][mail_address]" value="' + mailAddress +
          '" id="MUserMailAddress">';
      html += '<input type="hidden" name="data[MUser][password]" value="' + pass + '" id="MUserPassword">';
      html += '</form>';
      $('body').append(html);
      $('#postjump').submit();
      $('#postjump').remove();
    });
  });
</script>