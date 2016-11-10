<script type="text/javascript">
function muserFormButton(){
 document.getElementById('MAdministratorLoginForm').submit();
}
</script>
<div id="login_idx_bg"></div>
<div id="login_idx">
  <div id="content-area">
    <div class="user_add_title">
      SIGN IN
    </div>
    <ul class="formArea">
      <li>
        <?= $this->Form->create('MAdministrator'); ?>
        <?= $this->Form->input('mail_address', array('label' => false, 'placeholder' => 'メールアドレス')); ?>
        <?= $this->Form->input('password', array('label' => false, 'placeholder' => 'パスワード')); ?>
        <?= $this->Form->end(); ?>
      </li>
      <li>
        <?= $this->Form->input('Go →', array('label'=> false,'type' => 'button', 'class' => 'loginForm','onClick' => 'muserFormButton()')); ?>
      </li>
    </ul>
  </div>
</div>
