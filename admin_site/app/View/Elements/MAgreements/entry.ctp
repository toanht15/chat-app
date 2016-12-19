<!-- /* 申込日 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>申込日</label></span></div>
  <?= $this->Form->input('application_day', array('div' => false, 'label' => false,'type'=> 'text', 'maxlength' => 50)) ?>
  <?php if (!empty($agreementerrors['application_day'])) echo "<li class='error-message'>" . h($agreementerrors['application_day'][0]) . "</li>"; ?>
</li>
<!-- /* 会社名 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>会社名</label></span></div>
  <?= $this->Form->input('company_name', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
   <?php if (!empty($errors['company_name'])) echo "<li class='error-message'>" . h($errors['company_name'][0]) . "</li>"; ?>
</li>
<!-- /* サイトキー */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>サイトキー</label></span></div>
  <?= $this->Form->input('company_key', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
  <?php if (!empty($errors['company_key'])) echo "<li class='error-message'>" . h($errors['company_key'][0]) . "</li>"; ?>
</li>
<!-- /* テスト利用 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>テスト利用</label></span></div>
  <?= $this->Form->checkbox('trial_flg') ?>
</li>
<!-- 契約プラン -->
<li>
  <span class="require"><label>契約プラン</label></span>
  <?php $plans=array('1'=>'フルプラン','2'=>'チャットプラン','3'=>'画面共有プラン'); ?>
  <?= $this->Form->input('m_contact_types_id', array('type' => 'select', 'options' => $plans,'default' => 1,'label'=>false, 'div' => ['id' => 'planListArea'])) ?>
</li>
<!-- /* 契約ID数 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>契約ID数</label></span></div>
  <?= $this->Form->input('limit_users', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
  <?php if (!empty($errors['limit_users'])) echo "<li class='error-message'>" . h($errors['limit_users'][0]) . "</li>"; ?>
</li>
<!-- /* 契約開始日 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>契約開始日</label></span></div>
  <?= $this->Form->input('agreement_start_day', array('div' => false, 'label' => false, 'maxlength' => 50,'type' => 'text')) ?>
  <?php if (!empty($agreementerrors['agreement_start_day'])) echo "<li class='error-message'>" . h($agreementerrors['agreement_start_day'][0]) . "</li>"; ?>
</li>
<!-- /* 契約終了日 */ -->
<?php if ($this->params->action == 'add') { ?>
  <li>
    <?= $this->Form->input('agreement_end_day', array('type' => 'hidden','div' => false, 'label' => false, 'maxlength' => 50)) ?>
  </li>
<?php } ?>
<?php if ($this->params->action == 'edit') { ?>
  <li>
    <div class="labelArea fLeft"><span><label>契約終了日</label></span></div>
    <?= $this->Form->input('agreement_end_day', array('div' => false, 'label' => false, 'maxlength' => 50,'type' => 'text')) ?>
  </li>
<?php } ?>
<!-- /* 申し込み情報部署名 */ -->
<li>
  <div class="labelArea fLeft"><span><label>申し込み情報</label></span></div>
  <?= $this->Form->input('application_department', array('div' => false, 'label' => '部署名', 'maxlength' => 50)) ?>
</li>
<!-- /* 申し込み情報名前 */ -->
<li>
  <div class="labelArea fLeft"><span><label></label></span></div>
  <?= $this->Form->input('application_name', array('div' => false, 'label' => '名前', 'maxlength' => 50)) ?>
</li>
<!-- /* 管理者情報部署名 */ -->
<li>
  <div class="labelArea fLeft"><span><label>管理者情報</label></span></div>
  <?= $this->Form->input('administrator_department', array('div' => false, 'label' => '部署名', 'maxlength' => 50)) ?>
</li>
<!-- /* 管理者情報役職名 */ -->
<li>
  <div class="labelArea fLeft"><span><label></label></span></div>
  <?= $this->Form->input('administrator_position', array('div' => false, 'label' => '役職名', 'maxlength' => 50)) ?>
</li>
<!-- /* 管理者情報名前 */ -->
<li>
  <div class="labelArea fLeft"><span><label></label></span></div>
  <?= $this->Form->input('administrator_name', array('div' => false, 'label' => '名前', 'maxlength' => 50)) ?>
</li>
<!-- /* 設置サイト名 */ -->
<li>
  <div class="labelArea fLeft"><span><label>設置サイト名</label></span></div>
  <?= $this->Form->input('installation_site_name', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
</li>
<!-- /* 設置サイトURL */ -->
<li>
  <div class="labelArea fLeft"><span><label>設置サイトURL</label></span></div>
  <?= $this->Form->input('installation_url', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
</li>
<!-- /* メールアドレス */ -->
<?php if ($this->params->action == 'add') { ?>
<li>
  <div class="labelArea fLeft"><span class="require"><label>スーパー管理者</label></span></div>
  <?= $this->Form->input('mail_address', array('div' => false, 'label' => 'メールアドレス', 'maxlength' => 50)) ?>
   <?php if (!empty($errors['mail_address'])) echo "<li class='error-message'>" . h($errors['mail_address'][0]) . "</li>"; ?>
  </li>
<?php } ?>

<?php if ($this->params->action == 'edit') { ?>
  <li>
    <div class="labelArea fLeft"><span class="require"><label>スーパー管理者</label></span></div>
    <?= $this->Form->input('mail_address', array('div' => false, 'label' => 'メールアドレス', 'maxlength' => 50)) ?>
    <?php if (!empty($userErrors['mail_address'])) echo "<li class='error-message'>" . h($userErrors['mail_address'][0]) . "</li>"; ?>
  </li>
<?php } ?>

  <?php if ($this->params->action == 'edit') { ?>
  <li>
    <?= $this->Form->input('m_companies_id', array('type' => 'hidden','div' => false)) ?>
    <?= $this->Form->input('m_users_id', array('type' => 'hidden','div' => false)) ?>
  </li>
<?php } ?>
<!-- /* パスワード自動生成 */ -->
<li>
  <div class="labelArea fLeft"><span　class="require"><label></label></span></div>
  <?= $this->Form->input('admin_password', array('div' => false, 'label' => 'パスワード', 'maxlength' => 50)) ?>
  <?= $this->Form->input('hash_password', array('type' => 'hidden', 'div' => false, 'label' => 'パスワード', 'maxlength' => 50)) ?>
  <?= $this->Html->link('自動生成','javascript:void(0)',array('escape' => false, 'id' => 'createPassword','class' => 'action_btn','onclick' => 'createPassword()'));?>
  <?php if (!empty($agreementerrors['admin_password'])) echo "<li class='error-message'>" . h($agreementerrors['admin_password'][0]) . "</li>"; ?>
</li>
<!-- /* 電話番号 */ -->
<li>
  <div class="labelArea fLeft"><span><label>電話番号</label></span></div>
  <?= $this->Form->input('telephone_number', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
</li>

<!-- /* 備考 */ -->
<li>
  <span><label>備考</label></span>
  <?=$this->Form->input('note', ['type'=>'textarea','label' => false,'div' => false,'maxlength'=>300,'cols' => 40,'rows' => 10])?>
</li>

<?=$this->Form->hidden('id')?>