<?php
$editFlg = true;
if ( !empty($this->data['MAdministrator']['edit_password']) ) {
  $editFlg = false;
}
?>
<?= $this->Form->create('MAdministrator', array('type' => 'post', 'url' => array('controller' => 'PersonalSettings', 'action' => 'index'))); ?>
  <div class="form01">
    <!-- /* 基本情報 */ -->
    <section>
      <?= $this->Form->input('id', array('type' => 'hidden')); ?>
      <ul class='formArea'>
        <li>
          <div class="labelArea fLeft"><span class="require"><label>氏名</label></span></div>
          <?= $this->Form->input('user_name', array('placeholder' => 'user_name', 'div' => false, 'label' => false, 'maxlength' => 50, 'error' => false)) ?>
        </li>
        <?php if ( $this->Form->isFieldError('user_name') ) echo $this->Form->error('user_name', null, array('wrap' => 'li')); ?>
        <li>
          <div class="labelArea fLeft"><span class="require"><label>メールアドレス</label></span></div>
          <?= $this->Form->input('mail_address', array('placeholder' => 'mail_address', 'div' => false, 'label' => false, 'maxlength' => 200, 'error' => false)) ?>
        </li>
        <?php if ( $this->Form->isFieldError('mail_address') ) echo $this->Form->error('mail_address', null, array('wrap' => 'li')); ?>
      </ul>
    </section>

    <!-- /* パスワード変更 */ -->
    <section>
      <!-- /* autocomplete対策 */ -->
      <input type="text" style="display: none">
      <input type="password" style="display: none">
      <!-- /* autocomplete対策 */ -->

      <div class="labelArea m40l fLeft"><span><label>パスワードを変更する</label></span></div>
      <?= $this->Form->input('edit_password', array('type' => 'checkbox', 'class' => false, 'label' => false, 'div' => false)); ?>

      <div id="set_password_area">
        <ul class='formArea'>
          <li>
            <div class="labelArea fLeft"><span><label>現在のパスワード</label></span></div>
            <?= $this->Form->input('current_password', array('type' => 'password', 'disabled' => $editFlg, 'placeholder' => 'current password', 'div' => false, 'label' => false, 'maxlength' => 12, 'error' => false)) ?>
          </li>
          <?php if ($this->Form->isFieldError('current_password')) echo $this->Form->error('current_password', null, array('wrap' => 'li')); ?>
          <li>
            <div class="labelArea fLeft"><span><label>新しいパスワード</label></span></div>
            <?= $this->Form->input('new_password', array('type' => 'password', 'disabled' => $editFlg, 'placeholder' => 'new password', 'div' => false, 'label' => false, 'maxlength' => 12, 'error' => false)) ?>
          </li>
          <?php if ($this->Form->isFieldError('new_password') ) echo $this->Form->error('new_password', null, array('wrap' => 'li')); ?>
          <li>
            <div class="labelArea fLeft"><span><label>新しいパスワード（確認用）</label></span></div>
            <?= $this->Form->input('confirm_password', array('type' => 'password', 'disabled' => $editFlg, 'placeholder' => 'confirm password', 'div' => false, 'label' => false, 'maxlength' => 12, 'error' => false)) ?>
          </li>
          <?php if ($this->Form->isFieldError('confirm_password') ) echo $this->Form->error('confirm_password', null, array('wrap' => 'li')); ?>
        </ul>
      </div>
    </section>

    <!-- /* 操作 */ -->
    <section>
      <div id="personal_action">
        <?php echo $this->Html->link('更新','javascript:void(0)',array('escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'saveAct()'));?>
      </div>
    </section>

  </div>
<?= $this->Form->end(); ?>