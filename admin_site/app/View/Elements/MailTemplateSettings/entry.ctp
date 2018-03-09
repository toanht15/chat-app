<?php
  $isTrial = false;
  if(!empty($data['MCompany']['trial_flg'])) {
    $isTrial = boolval($data['MCompany']['trial_flg']);
  }
  //無料トライアル申込み後or初期パスワード変更後or契約申込み後
  if($value == C_AFTER_TRIAL_APPLICATION || $value == C_AFTER_PASSWORD_CHANGE || $value == C_AFTER_APPLICATIONE) {
    $model = 'MSystemMailTemplate';
  }
  //何日後
  else if($value == C_AFTER_DAYS) {
    $model = 'MJobMailTemplate';
  }
?>
<!-- /* 名称 */ -->
<li>
  <div class="labelArea fLeft"><span><label>名称</label></span></div>
  <?= $this->Form->hidden('MSystemMailTemplate.id', array('div' => false, 'type' => 'text','label' => false, 'maxlength' => 50)) ?>
  <?= $this->Form->input($model.'.mail_type_cd', array('div' => false, 'type' => 'text','label' => false, 'maxlength' => 50)) ?>
</li>
<!-- /* 名称 */ -->
<li>
 <div class="labelArea fLeft"><span><label>いつ</label></span></div>
  <?= $this->Form->input('timeToSendMail', array('type' => 'radio','legend' => false,'options' => $mailRegistration, 'label' => false,'default' => false,'value' => $value)) ?>
</li>
<!-- /* 何日後 */ -->
<li class = 'daysAfter' style = "display:none;">
  <div class="labelArea fLeft"><span class="require"><label id = 'value'>何日後</label></span></div>
  <?= $this->Form->input('MJobMailTemplate.value', array('div' => false,'options' => range(0,365), 'label' => false, 'maxlength' => 50,'type' => 'select')) ?>
</li>
<!-- /* 何時 */ -->
<li class = 'daysAfter' style = "display:none;">
  <div class="labelArea fLeft"><span class="require"><label>何時</label></span></div>
  <?= $this->Form->input('MJobMailTemplate.time', array('div' => false, 'label' => false,'options' => array(9,12,15,19), 'maxlength' => 50,'type' => 'select')) ?>
</li>
<!-- /* メディアリンクにメールを飛ばす */ -->
<li class = 'daysAfter' style = "display:none;">
 <div class="labelArea fLeft"><span class="require"><label>MLにもメール送信<br>する</label></span></div>
  <?= $this->Form->input('ToSendMediaLink', array('type' => 'radio','legend' => false,'options' => $sendingMailML, 'label' => false,'default' => C_SEND_MAIL_ML)) ?>
</li>
<!-- /* メール差出人 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>メール差出人</label></span></div>
  <?= $this->Form->input($model.'.sender', array('div' => false, 'label' => false,'type' => 'textarea','style' => 'width: 332px; height: 15px;')) ?>
  <?php if (!empty($agreementerrors['MSystemMailTemplate.subject'])) echo "<li class='error-message'>" . h($agreementerrors['MSystemMailTemplate.subject'][0]) . "</li>"; ?>
</li>
<!-- /* メールタイトル */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>メールタイトル</label></span></div>
  <?= $this->Form->input($model.'.subject', array('div' => false, 'label' => false,'type' => 'textarea','style' => 'width: 332px; height: 15px;')) ?>
  <?php if (!empty($agreementerrors['MSystemMailTemplate.subject'])) echo "<li class='error-message'>" . h($agreementerrors['MSystemMailTemplate.subject'][0]) . "</li>"; ?>
</li>
<!-- /* メール本文 */ -->
<li>
  <div class="labelArea fLeft"><span class="require"><label>メール本文</label></span></div>
  <?= $this->Form->input($model.'.mail_body', array('div' => false, 'label' => false,'type' => 'textarea','style' => 'width: 334px; height: 286px;')) ?>
</li>