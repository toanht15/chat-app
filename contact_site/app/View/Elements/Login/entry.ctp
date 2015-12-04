<?= $this->Form->create('MUser', array('url' => '/Login/login', 'id' => 'MUserIndexForm')); ?>
		<?= $this->Form->input('mail_address', array('label' => false, 'placeholder' => 'Mail Address')) ?>
		<?= $this->Form->input('password', array('label' => false, 'placeholder' => 'Password')) ?>
		<?= $this->Html->link('Sign In','javascript:void(0)', array('id' => 'MUserFormButton')) ?>
	<?= $this->Form->end(); ?>