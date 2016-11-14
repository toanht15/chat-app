<script type="text/javascript">
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

<div id='personal_idx'>
  <div id='personal_add_title'>
    <div class="fLeft"><i class="fa fa-user fa-2x" aria-hidden="true"></i></div>
    <h1>アカウント設定</h1>
  </div>

  <div id='personal_form' class="p20x">

    <?= $this->element('PersonalSettings/entry'); ?>

  </div>
</div>
