<?php
  $isTrial = false;
  if(!empty($data['MCompany']['trial_flg'])) {
    $isTrial = boolval($data['MCompany']['trial_flg']);
  }
  //無料トライアル申込み後or初期パスワード変更後or契約申込み後
  if($value == C_AFTER_APPLICATION || $value == C_AFTER_PASSWORD_CHANGE) {
    $model = 'MSystemMailTemplate';
  }
  //何日後
  else if($value == C_AFTER_DAYS || $value == C_BEFORE_DAYS) {
    $model = 'MJobMailTemplate';
  }
?>
<!-- /* 名称 */ -->
<li>
  <div class="labelArea fLeft"><span><label>名称</label></span></div>
  <?= $this->Form->hidden('MSystemMailTemplate.id', array('div' => false, 'type' => 'text','label' => false, 'maxlength' => 50)) ?>
  <?= $this->Form->input($model.'.mail_type_cd', array('div' => false, 'type' => 'text','label' => false, 'maxlength' => 50)) ?>
</li>
<!-- /* 契約 */ -->
<li>
 <div class="labelArea fLeft"><span><label>契約</label></span></div>
  <?= $this->Form->input('MJobMailTemplate.agreement_flg', array('type' => 'radio','legend' => false,'options' => $agreement, 'label' => false,'default' => false,'value' => $agreementFlg)) ?>
</li>
<!-- /* いつ */ -->
<li>
 <div class="labelArea fLeft"><span><label>いつ</label></span></div>
  <?= $this->Form->input('timeToSendMail', array('type' => 'radio','legend' => false,'options' => $mailRegistration, 'label' => false,'default' => false,'value' => $value)) ?>
</li>
<!-- /* 何日後、何日前 */ -->
<li class = 'daysAfter' style = "display:none;">
  <div class="labelArea fLeft"><span class="require"><label id = 'value'>
  <?php if($value == 3) { ?>
    何日後
  <?php } ?>
  <?php if($value == 4) { ?>
    何日前
  <?php } ?>
  </label></span></div>
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
  <?= $this->Form->input('MJobMailTemplate.send_mail_ml_flg', array('type' => 'radio','legend' => false,'options' => $sendingMailML, 'label' => false,'default' => C_SEND_MAIL_ML)) ?>
</li>
<!-- /* メール差出人 */ -->
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
  <?= $this->Form->input($model.'.mail_body', array('div' => false, 'label' => false,'type' => 'textarea','style' => 'width: 334px; height: 286px;')) ?>
  <div style = "margin-left:30px;">
    ##COMPANY_NAME##：　会社名<br>
    ##USER_NAME##：　名前<br>
    ##MAIL_ADDRESS##：　メールアドレス<br>
    <?php if($value == C_AFTER_APPLICATION) { ?>
      ##PASSWORD##：　パスワード
    <?php } ?>
  </div>
</li>