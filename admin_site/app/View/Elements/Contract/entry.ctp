<!-- /* 会社名 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>会社名</label></span></div>
  <?= $this->Form->input('MCompany.company_name', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
   <?php if (!empty($errors['company_name'])) echo "<li class='error-message'>" . h($errors['company_name'][0]) . "</li>"; ?>
</li>
<!-- /* サイトキー サイトキーは登録日（MD5）のハッシュ値 */ -->
<!-- /* テスト利用 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>テスト利用</label></span></div>
  <?= $this->Form->checkbox('MCompany.trial_flg') ?>
</li>
<!-- 契約プラン -->
<li>
  <span class="require"><label>契約プラン</label></span>
  <?php $plans=array('1'=>'プレミアムプラン','4'=>'チャットベーシックプラン','2'=>'チャットスタンダードプラン','3'=>'画面共有プラン'); ?>
  <?= $this->Form->input('MCompany.m_contact_types_id', array('type' => 'select', 'options' => $plans,'default' => 1,'label'=>false, 'div' => ['id' => 'planListArea'])) ?>
</li>
<!-- /* 契約ID数 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>契約ID数</label></span></div>
  <?= $this->Form->input('MCompany.limit_users', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
  <?php if (!empty($errors['limit_users'])) echo "<li class='error-message'>" . h($errors['limit_users'][0]) . "</li>"; ?>
</li>
<!-- /* 初期管理者情報 */ -->
<?php if ($this->params->action == 'add'): ?>
<li>
  <div class="labelArea fLeft"><span class="require"><label>初期管理者情報</label></span></div>
</li>
<!-- /* 管理者名 */ -->
<li>
  <div class="labelArea fLeft"><span><label></label></span></div>
  <?= $this->Form->input('user_name', array('div' => false, 'label' => '管理者名', 'maxlength' => 10)) ?>
</li>
<!-- /* 表示名 */ -->
<li>
  <div class="labelArea fLeft"><span><label></label></span></div>
  <?= $this->Form->input('user_display_name', array('div' => false, 'label' => '表示名', 'maxlength' => 50)) ?>
</li>
<!-- /* メールアドレス */ -->
<li>
  <div class="labelArea fLeft"><span><label></label></span></div>
  <?= $this->Form->input('user_mail_address', array('div' => false, 'label' => 'メールアドレス', 'maxlength' => 50)) ?>
</li>
<!-- /* パスワード TODO: 自動発行してメールで通知したい */ -->
<li>
  <div class="labelArea fLeft"><span><label></label></span></div>
  <?= $this->Form->input('user_password', array('div' => false, 'type' => 'password', 'label' => 'パスワード', 'maxlength' => 50)) ?>
</li>
<!-- /* パスワード（確認用） TODO: 自動発行してメールで通知したい */ -->
<li>
  <div class="labelArea fLeft"><span><label></label></span></div>
  <?= $this->Form->input('user_confirm_password', array('div' => false, 'type' => 'password', 'label' => 'パスワードの確認', 'maxlength' => 50)) ?>
</li>
<?php endif; ?>