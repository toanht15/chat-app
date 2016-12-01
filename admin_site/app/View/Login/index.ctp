<?php echo $this->element('Login/script'); ?>
<div id="login_idx_bg"></div>
<div id="login_idx">
  <div id="content-area">
    <div class="user_add_title">
      SIGN IN
    </div>
    <ul class="formArea">
      <li>
        <?= $this->Form->create('MAdministrator'); ?>
          <?= $this->Form->input('mail_address', ['label' => false, 'placeholder' => 'メールアドレス']); ?>
          <?= $this->Form->input('password', ['label' => false, 'placeholder' => 'パスワード']); ?>
        <?= $this->Form->end(); ?>
      </li>
      <li>
        <?= $this->Form->input('Go →', ['label'=> false,'type' => 'button', 'class' => 'loginForm','onClick' => 'loginBtn()']); ?>
      </li>
    </ul>
  </div>
</div>
