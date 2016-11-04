<script type="text/javascript">
function MUserFormButton(){
  console.log('eeee');
 document.getElementById('MUserIndexForm').submit();
}
</script>
<div>
<?= $this->Form->create('Login', array('id' => 'MUserIndexForm')); ?>
<?= $this->Form->input('mail_address', array('label' => false, 'placeholder' => 'Mail Address')); ?>
<?= $this->Form->input('password', array('label' => false, 'placeholder' => 'Password')); ?>
<?= $this->Form->end(); ?>
<?= $this->Form->input('Sign In', array('label'=> false,'type' => 'button',  'class'=>'add-submit','onClick' => 'MUserFormButton()')); ?>
</div>
