<script type="text/javascript">
  $(function(){
    var passwordElm = $("[type='password']");
    var editCheck = document.getElementById('MUserEditPassword');
    editCheck.addEventListener('click', function(e){
      if ( e.target.checked ) {
        passwordElm.prop('disabled', '');
      }
      else {
        passwordElm.prop('disabled', 'disabled');
      }
    });
  });

  function saveAct(){
    $('#MUserIndexForm').submit();
  }

</script>

<div id='personal_idx' class="card-shadow">

<div id='personal_title'>
	<div class="fLeft"><?= $this->Html->image('setting_g.png', array('alt' => '個人設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>個人設定</h1>
</div>

<div id='personal_form' class="p20x">

	<?= $this->element('PersonalSettings/entry'); ?>

</div><!-- /personal_menu -->

</div><!-- /personal_idx -->