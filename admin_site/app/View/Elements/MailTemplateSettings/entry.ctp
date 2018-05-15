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
<!-- オプション -->
<li class="daysAfter sendTarget" style="display:flex">
  <span><label>送信対象</label></span>
  <div>
    <?= $this->Form->input('MJobMailTemplate.send_mail_application_user_flg', array('type' => 'checkbox', 'default' => false, 'label'=>'申込者に送る', 'div' => ['id' => 'sendApplicationUserFlg'])) ?>
    <?= $this->Form->input('MJobMailTemplate.send_mail_administrator_user_flg', array('type' => 'checkbox', 'default' => false, 'label'=>'管理者に送る', 'div' => ['id' => 'sendAdministratorUserFlg'])) ?>
    <?= $this->Form->input('MJobMailTemplate.send_mail_sinclo_all_users_flg', array('type' => 'checkbox', 'default' => false, 'label'=>'sincloのユーザー一覧にいる全員に送る', 'div' => ['id' => 'sendSincloAllUserFlg'])) ?>
  </div>
</li>
<!-- /* メディアリンクにメールを飛ばす */ -->
<li class = 'daysAfter' style = "display:none;">
 <div class="labelArea fLeft"><span class="require"><label>MLにもメール送信<br>する</label></span></div>
  <?= $this->Form->input('MJobMailTemplate.send_mail_ml_flg', array('type' => 'radio','legend' => false,'options' => $sendingMailML, 'label' => false,'default' => C_SEND_MAIL_ML)) ?>
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
  <?= $this->Form->input($model.'.mail_body', array('div' => false, 'label' => false,'type' => 'textarea','style' => 'width: 334px; height: 603px;')) ?>
  <div style = "margin-left:30px;">
    <table>
      <thead style="background-color: #596d8f; color: #FFF">
        <tr><td>変数名</td><td>説明</td></tr>
      </thead>
      <tbody>
        <tr><td>##COMPANY_NAME##</td><td>会社名</td></tr>
        <tr class="initialVariable"><td>##PASSWORD##</td><td>パスワード</td></tr>
        <tr><td>##BUSINESS_MODEL##</td><td>ビジネスモデル</td></tr>
        <tr><td>##DEPARTMENT##</td><td>申込者：部署</td></tr>
        <tr><td>##POSITION##</td><td>申込者：役職</td></tr>
        <tr><td>##USER_NAME##</td><td>申込者：お名前<br>（sincloのユーザー送信時は各人の「氏名」）</td></tr>
        <tr><td>##MAIL_ADDRESS##</td><td>申込者：メールアドレス<br>（sincloのユーザー送信時は各人の「メールアドレス」）</td></tr>
        <tr><td>##ADMIN_DEPARTMENT##</td><td>管理者：部署</td></tr>
        <tr><td>##ADMIN_POSITION##</td><td>管理者：役職</td></tr>
        <tr><td>##ADMIN_USER_NAME##</td><td>管理者：お名前</td></tr>
        <tr><td>##ADMIN_MAIL_ADDRESS##</td><td>管理者：メールアドレス</td></tr>
        <tr><td>##PHONE_NUMBER##</td><td>電話番号</td></tr>
        <tr><td>##URL##</td><td>サイトURL</td></tr>
        <tr><td>##PLAN_NAME##</td><td>プラン名</td></tr>
        <tr><td>##BEGIN_DATE##</td><td>開始日</td></tr>
        <tr><td>##END_DATE##</td><td>終了日</td></tr>
        <tr><td>##USABLE_USER_COUNT##</td><td>利用可能ID数</td></tr>
        <tr><td>##OPTION_COMPANY_INFO##</td><td>オプション：企業情報付与</td></tr>
        <tr><td>##OPTION_SCENALIO##</td><td>オプション：チャットボットシナリオ</td></tr>
        <tr><td>##OPTION_CAPTURE##</td><td>オプション：画面キャプチャ共有</td></tr>
      </tbody>
    </table>
  </div>
</li>