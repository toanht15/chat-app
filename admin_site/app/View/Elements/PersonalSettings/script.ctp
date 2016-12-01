<script type="text/javascript">
  //個人設定パスワード欄
  $(function(){
    var passwordElm = $("[type='password']");
    var editCheck = document.getElementById('MAdministratorEditPassword');
    var pwArea = $('#set_password_area span');
    editCheck.addEventListener('click', function(e){
      if ( e.target.checked ) {
        passwordElm.prop('disabled', '');
        pwArea.addClass('require');
      }
      else {
        passwordElm.prop('disabled', 'disabled');
        pwArea.removeClass('require');
      }
    });
  });

  function saveAct(){
    $('#MAdministratorIndexForm').submit();
  }
</script>