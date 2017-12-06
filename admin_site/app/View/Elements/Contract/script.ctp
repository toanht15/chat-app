<script type = "text/javascript">
  $(function(){
    var popWinObj;
    $('.loginLink').on("click",function(){
      var mailAddress = $(this).parents('tr').find('.adminId').text();
      var pass = $(this).parents('tr').find('.adminPass').text();
      $('#postjump').remove();
      if( (popWinObj) && (!popWinObj.closed) ){
        popWinObj.close();
      }
      popWinObj = window.open("about:blank","sincloViewWindow");
      var html = '<form method="post" action="https://sinclo.jp/Login/login" id="postjump" target="sincloViewWindow" style="display: none;">';
      html += '<input type="hidden" name="data[MUser][mail_address]" value="' + mailAddress + '" id="MUserMailAddress">';
      html += '<input type="hidden" name="data[MUser][password]" value="' + pass + '" id="MUserPassword">';
      html += '</form>';
      $("body").append(html);
      $('#postjump').submit();
      $('#postjump').remove();
    });
  });
</script>