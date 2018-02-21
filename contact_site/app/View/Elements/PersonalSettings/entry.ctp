<script type="text/javascript">
<?php $this->request->data['MUser']['user_name'] = htmlspecialchars($this->request->data['MUser']['user_name'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['display_name'] = htmlspecialchars($this->request->data['MUser']['display_name'], ENT_QUOTES, 'UTF-8');?>
</script>

<?php
$editFlg = true;
if ( !empty($this->data['MUser']['edit_password']) ) {
  $editFlg = false;
}
$settings = [];
if ( !empty($this->data['MUser']['settings']) ) {
  if(!preg_match('/^(?=.*(<|>|&|\')).*$/',$this->data['MUser']['settings'])) {
    $settings = (array)json_decode($this->data['MUser']['settings']);
  }
}
?>
<?= $this->Form->create('MUser', array('type' => 'post', 'url' => array('controller' => 'PersonalSettings', 'action' => 'index'))); ?>
    <div class="form01">
        <!-- /* 基本情報 */ -->
        <section>
            <?= $this->Form->input('id', array('type' => 'hidden')); ?>
            <ul>
                <li>
                    <div class="labelArea fLeft"><span class="require"><label>氏名</label></span></div>
                    <?= $this->Form->input('user_name', array('placeholder' => 'user_name', 'div' => false, 'label' => false, 'maxlength' => 50, 'error' => false)) ?>
                </li>
                <?php if ( $this->Form->isFieldError('user_name') ) echo $this->Form->error('user_name', null, array('wrap' => 'li')); ?>
                <li>
                    <div class="labelArea fLeft"><span class="require"><label>表示名</label></span></div>
                    <?= $this->Form->input('display_name', array('placeholder' => 'display_name', 'div' => false, 'label' => false, 'maxlength' => 10, 'error' => false)) ?>
                </li>
                <?php if ( $this->Form->isFieldError('display_name') ) echo $this->Form->error('display_name', null, array('wrap' => 'li')); ?>

                <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && !empty($mChatSetting['MChatSetting']) && strcmp($mChatSetting['MChatSetting']['sc_flg'], C_SC_ENABLED) === 0 ) : ?>
                  <li>
                      <div class="labelArea fLeft"><span><label>チャット同時対応数</label></span></div>
                      <span><?php
                      echo ( !empty($settings['sc_num']) ) ? $settings['sc_num'] : 0 ?></span>
                      <?=$this->Form->hidden('settings',array('error' => false))?>
                  </li>
                  <?php if ( $this->Form->isFieldError('settings') ) echo $this->Form->error('settings', null, array('wrap' => 'li')); ?>
                <?php endif; ?>

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
            <?= $this->Form->input('edit_password', array('type' => 'checkbox', 'class' => 'pointer', 'label' => false, 'div' => false)); ?>

            <div id="set_password_area">
                <ul>
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
                <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'load.ev(saveAct)', 'class' => 'greenBtn btn-shadow inlineSaveBtn']) ?>
            </div>
        </section>

    </div>
<?= $this->Form->end(); ?>
