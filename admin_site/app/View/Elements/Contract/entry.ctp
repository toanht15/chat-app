<?php
  $isTrial = false;
  if(!empty($data['MCompany']['trial_flg'])) {
    $isTrial = boolval($data['MCompany']['trial_flg']);
  }
?>
  <li><h2>企業情報</h2></li>
  <!-- /* 会社名 */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>会社名</label></span></div>
    <?= $this->Form->input('MCompany.company_name', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
     <?php if (!empty($errors['company_name'])) echo "<li class='error-message'>" . h($errors['company_name'][0]) . "</li>"; ?>
  </li>
  <!-- /* ビジネスモデル */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>ビジネスモデル</label></span></div>
    <?= $this->Form->input('MAgreements.business_model', array('type' => 'radio','legend' => false,'options' => $businessModel, 'label' => 'BtoB','default' => false,'value' => 1)) ?>
  </li>
  <!-- /* 導入を検討しているサイトURL */ -->
  <li>
    <div class="labelArea fLeft"><span><label>サイトURL</label></span></div>
    <?= $this->Form->input('MAgreements.installation_url', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 2048)) ?>
  </li>

  <li><h2>申込者情報</h2></li>
  <!-- /* 部署名 */ -->
  <li>
    <div class="labelArea fLeft"><span><label>部署名</label></span></div>
    <?= $this->Form->input('MAgreements.application_department', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
  </li>
  <!-- /* 役職 */ -->
  <li>
    <div class="labelArea fLeft"><span><label>役職</label></span></div>
    <?= $this->Form->input('MAgreements.application_position', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
  </li>
  <!-- /* お名前 */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>お名前</label></span></div>
    <?= $this->Form->input('MAgreements.application_name', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
    <?php if (!empty($errors['application_name'])) echo "<li class='error-message'>" . h($errors['application_name'][0]) . "</li>"; ?>
  </li>
  <!-- /* メールアドレス */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>メールアドレス</label></span></div>
    <?= $this->Form->input('MAgreements.application_mail_address', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
  </li>

  <!-- /* 電話番号 */ -->
  <li>
    <div class="labelArea fLeft"><span><label>電話番号</label></span></div>
    <?= $this->Form->input('MAgreements.telephone_number', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
  </li>

  <li>
    <h2>担当者情報</h2>
    <?php if ($this->params->action == 'add'): ?>
    <?= $this->Form->input('same_as_application', array('type' => 'checkbox', 'div' => false, 'label' => '申込者情報と同じ')) ?>
    <?php endif; ?>
  </li>
  <!-- /* 部署名 */ -->
  <li>
    <div class="labelArea fLeft"><span><label>部署名</label></span></div>
    <?= $this->Form->input('MAgreements.administrator_department', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
  </li>
  <!-- /* 役職 */ -->
  <li>
    <div class="labelArea fLeft"><span><label>役職</label></span></div>
    <?= $this->Form->input('MAgreements.administrator_position', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
  </li>
  <!-- /* お名前 */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>お名前</label></span></div>
    <?= $this->Form->input('MAgreements.administrator_name', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
    <?php if (!empty($errors['application_name'])) echo "<li class='error-message'>" . h($errors['application_name'][0]) . "</li>"; ?>
  </li>
  <!-- /* メールアドレス */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>メールアドレス</label></span></div>
    <?= $this->Form->input('MAgreements.administrator_mail_address', array('div' => false, 'label' => false, 'class' => 'text-input', 'maxlength' => 50)) ?>
    <!-- /* 初期管理者情報 */ -->
    <?php if ($this->params->action == 'add'): ?>
      <span>※ 初期登録ユーザーのメールアドレスとして利用されます。</span>
    <?php endif; ?>
  </li>

  <!-- /* サイトキー サイトキーは登録日（MD5）のハッシュ値 */ -->
  <!-- /* テスト利用 */ -->
  <li><h2>プラン設定</h2></li>
  <li>
    <div class="labelArea fLeft"><span class="require"><label>テスト利用</label></span></div>
    <?= $this->Form->checkbox('MCompany.trial_flg') ?>
  </li>
  <!-- 契約プラン -->
  <li>
    <span class="require"><label>契約プラン</label></span>
    <?php $plans=array('1'=>'プレミアムプラン','4'=>'チャットベーシックプラン','2'=>'チャットスタンダードプラン','3'=>'シェアリングプラン'); ?>
    <?= $this->Form->input('MCompany.m_contact_types_id', array('type' => 'select', 'options' => $plans,'default' => 1,'label'=>false, 'div' => ['id' => 'planListArea'])) ?>
  </li>
  <!-- オプション -->
  <li style="display:flex">
    <span><label>オプション</label></span>
    <div>
    <?= $this->Form->input('MCompany.options.refCompanyData', array('type' => 'checkbox', 'default' => false, 'label'=>'企業情報付与', 'div' => ['id' => 'refCompanyDataOptionArea'])) ?>
    <?= $this->Form->input('MCompany.options.chatbotScenario', array('type' => 'checkbox', 'default' => false, 'label'=>'シナリオ設定', 'div' => ['id' => 'chatbotScenario'])) ?>
    <?= $this->Form->input('MCompany.options.laCoBrowse', array('type' => 'checkbox', 'default' => false, 'label'=>'画面キャプチャ共有', 'div' => ['id' => 'laCoBrowse'])) ?>
    <?= $this->Form->input('MCompany.la_limit_users', array('type' => 'number', 'default' => 0, 'label'=>'最大セッション数：', 'div' => ['id' => 'laLimitUsers'])) ?>
    </div>
  </li>
  <!-- /* 契約ID数 */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>契約ID数</label></span></div>
    <?= $this->Form->input('MCompany.limit_users', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
    <?php if (!empty($errors['limit_users'])) echo "<li class='error-message'>" . h($errors['limit_users'][0]) . "</li>"; ?>
  </li>
  <li><h2>期間設定</h2></li>
  <!-- /* トライアル開始日 */ -->
  <li>
    <div class="labelArea fLeft"><span><label>トライアル開始日</label></span></div>
    <?= $this->Form->input('MAgreements.trial_start_day', array('div' => false, 'label' => false, 'maxlength' => 50,'type' => 'text','class' => $isTrial ? '' : 'disabled','readonly' => $isTrial)) ?>
    <?php if (!empty($agreementerrors['MAgreements.trial_start_day'])) echo "<li class='error-message'>" . h($agreementerrors['MAgreements.trial_start_day'][0]) . "</li>"; ?>
  </li>
  <!-- /* トライアル終了日 */ -->
  <li>
    <div class="labelArea fLeft"><span><label>トライアル終了日</label></span></div>
    <?= $this->Form->input('MAgreements.trial_end_day', array('div' => false, 'label' => false, 'maxlength' => 50,'type' => 'text','class' =>  $isTrial ? '' : 'disabled','readonly' => $isTrial)) ?>
    <?php if (!empty($agreementerrors['MAgreements.trial_end_day'])) echo "<li class='error-message'>" . h($agreementerrors['MAgreements.trial_end_day'][0]) . "</li>"; ?>
  </li>
  <!-- /* 契約開始日 */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>契約開始日</label></span></div>
    <?= $this->Form->input('MAgreements.agreement_start_day', array('div' => false, 'label' => false, 'maxlength' => 50,'type' => 'text','class' =>  $isTrial ? 'disabled' : '', 'readonly' => !$isTrial)) ?>
    <?php if (!empty($agreementerrors['MAgreements.agreement_start_day'])) echo "<li class='error-message'>" . h($agreementerrors['MAgreements.agreement_start_day'][0]) . "</li>"; ?>
  </li>
  <!-- /* 契約開始日 */ -->
  <li>
    <div class="labelArea fLeft"><span class="require"><label>契約終了日</label></span></div>
    <?= $this->Form->input('MAgreements.agreement_end_day', array('div' => false, 'label' => false, 'maxlength' => 50,'type' => 'text','class' =>  $isTrial ? 'disabled' : '','readonly' => !$isTrial)) ?>
    <?php if (!empty($agreementerrors['MAgreements.agreement_end_day'])) echo "<li class='error-message'>" . h($agreementerrors['MAgreements.agreement_end_day'][0]) . "</li>"; ?>
  </li>
  <!-- /* メモ */ -->
  <li>
    <div class="labelArea fLeft"><span><label>メモ</label></span></div>
    <?= $this->Form->input('MAgreements.memo', array('div' => false, 'label' => false, 'maxlength' => 20000, 'type' => 'textarea', 'cols' => 75, 'rows' => 15)) ?>
    <?php if (!empty($agreementerrors['MAgreements.memo'])) echo "<li class='error-message'>" . h($agreementerrors['MAgreements.memo'][0]) . "</li>"; ?>
  </li>