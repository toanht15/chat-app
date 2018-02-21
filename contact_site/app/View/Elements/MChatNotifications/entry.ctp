<?= $this->element('MChatNotifications/script') ?>

<div class="form01">
  <span class="pre">特定キーワードを含む（部分一致）ページからチャットを受信した際にデスクトップ通知するアイコン画像を選択してください。</span>
  <!-- /* 対象ページ設定 */ -->
  <h3>１．対象ページ設定</h3>
  <section>
  <?= $this->Form->input('id', ['type' => 'hidden']); ?>
  <ul class="settingList">
    <!-- 対象 -->
    <li>
      <span class="require"><label>対象</label></span>
      <pre><label class="pointer"><?= $this->Form->input('type', ['type' => 'radio', 'separator' => '</label>&nbsp;<label class="pointer">', 'options' => $chatNotificationType, 'default' => C_NOTIFICATION_TYPE_TITLE, 'legend' => false, 'separator' => '</label> <label class="pointer">', 'label' => false, 'error' => false, 'div' => false]) ?></label></pre>
    </li>
    <?php if ( $this->Form->isFieldError('type') ) echo $this->Form->error('type', null, ['wrap' => 'li']); ?>
    <!-- 対象 -->
    <!-- キーワード -->
    <li>
      <span class="require"><label>キーワード</label></span>
      <?= $this->Form->input('keyword', [
                  'type' => 'text',
                  'legend' => false,
                  'div' => false,
                  'label' => false,
                  'maxlength' => 100,
                  'placeholder' => 'キーワード',
                  'error' => false
                ]) ?>
    </li>
    <?php if ( $this->Form->isFieldError('keyword') ) echo $this->Form->error('keyword', null, ['wrap' => 'li']); ?>
    <!-- キーワード -->
  </ul>
  </section>
  <!-- /* 対象ページ設定 */ -->

  <!-- /* 通知設定 */ -->
  <h3>２．通知設定</h3>
  <section>
  <?= $this->Form->input('id', ['type' => 'hidden']); ?>
  <ul class="settingList">
    <!-- 画像の設定 -->
    <li>
      <span><label>アイコン画像</label></span>
      <div>
        <?= $this->Form->hidden('main_image') ?>
        <div id="imageSelectBtns" >

          <div id="picDiv">
            <?=$this->Html->image(C_PATH_NOTIFICATION_IMG_DIR.$imagePath, ['err-src'=>C_PATH_NOTIFICATION_IMG_DIR.'popup_icon_light_green.png', 'width'=>70, 'height'=>70, 'alt'=>'デスクトップ通知に設定している画像'])?>
          </div>
          <div id="picChooseDiv">
            <div class="greenBtn btn-shadow" onclick="showGallary()">ギャラリーから選択</div>
            <div class="greenBtn btn-shadow" id="fileTagWrap"><?php echo $this->Form->file('uploadImage'); ?>画像をアップロード</div>

          </div>
        </div>
        <?php if ($this->Form->isFieldError('main_image')) echo $this->Form->error('main_image', null, ['ng-if'=>'mainImageToggle=="1"']); ?>
        <?php if ($this->Form->isFieldError('uploadImage')) echo $this->Form->error('uploadImage', null, ['ng-if'=>'mainImageToggle=="1"']); ?>
      </div>
    </li>
    <!-- 画像の設定 -->
    <!-- 通知名 -->
    <li>
      <span class="require"><label>通知名</label></span>
      <?= $this->Form->input('name', [
        'type' => 'text',
        'placeholder' => '通知名',
        'div' => false,
        'label' => false,
        'maxlength' => 100,
        'error' => false
      ]
      ) ?>
    </li>
    <?php if ($this->Form->isFieldError('name') ) echo $this->Form->error('name', null, ['wrap' => 'li']); ?>
    <!-- 通知名 -->
  </ul>
  </section>
  <!-- /* 通知設定 */ -->

  <!-- /* 操作 */ -->
  <section>
      <div id="action_btn_area">
          <?= $this->Html->link('戻る', ['controller'=>'MChatNotifications', 'action'=>'index'], ['onclick' => 'load.ev(saveAct)', 'class' => 'greenBtn btn-shadow']) ?>
          <?= $this->Html->link('保存', 'javascript:void(0)', ['onclick' => 'load.ev(saveAct)', 'class' => 'greenBtn btn-shadow']) ?>
      </div>
  </section>
  <!-- /* 操作 */ -->
</div>
