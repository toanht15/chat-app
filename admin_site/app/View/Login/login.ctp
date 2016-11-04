<script type="text/javascript">
function MUserFormButton(){
 document.getElementById('MAdministratorLoginForm').submit();
}
</script>
<div>
<?= $this->Form->create('MAdministrator'); ?>
<?= $this->Form->input('mail_address', array('label' => false, 'placeholder' => 'Mail Address')); ?>
<?= $this->Form->input('password', array('label' => false, 'placeholder' => 'Password')); ?>
<?= $this->Form->end(); ?>
<?= $this->Form->input('Sign In', array('label'=> false,'type' => 'button',  'class'=>'add-submit','onClick' => 'MUserFormButton()')); ?>
</div>
