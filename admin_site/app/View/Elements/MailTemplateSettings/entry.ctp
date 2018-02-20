<?php
  $isTrial = false;
  if(!empty($data['MCompany']['trial_flg'])) {
    $isTrial = boolval($data['MCompany']['trial_flg']);
  }
?>
<!-- /* 名称 */ -->
<li>
  <div class="labelArea fLeft"><span><label>名称</label></span></div>
  <?= $this->Form->hidden('MJobMailTemplate.id', array('div' => false, 'type' => 'text','label' => false, 'maxlength' => 50)) ?>
  <?= $this->Form->input('MJobMailTemplate.mail_type_cd', array('div' => false, 'type' => 'text','label' => false, 'maxlength' => 50)) ?>
</li>
<!-- /* 何日後 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>何日後</label></span></div>
  <?= $this->Form->input('MJobMailTemplate.days_after', array('div' => false,'options' => range(0,365), 'label' => false, 'maxlength' => 50,'type' => 'select')) ?>
</li>
<!-- /* 何時 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>何時</label></span></div>
  <?= $this->Form->input('MJobMailTemplate.time', array('div' => false, 'label' => false,'options' => range(0,24), 'maxlength' => 50,'type' => 'select')) ?>
</li>
<!-- /* メールタイトル */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>メールタイトル</label></span></div>
  <?= $this->Form->input('MJobMailTemplate.subject', array('div' => false, 'label' => false,'type' => 'textarea')) ?>
  <?php if (!empty($agreementerrors['MJobMailTemplate.subject'])) echo "<li class='error-message'>" . h($agreementerrors['MJobMailTemplate.subject'][0]) . "</li>"; ?>
</li>
<!-- /* メール本文 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>メール本文</label></span></div>
  <?= $this->Form->input('MJobMailTemplate.mail_body', array('div' => false, 'label' => false,'type' => 'textarea')) ?>
</li>