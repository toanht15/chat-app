<?php
//
$fileTypeAreaHiddenClass = "";
if ( !(!empty($this->data['MFileTransferSetting']['type']) && strcmp($this->data['MFileTransferSetting']['type'],C_FILE_TRANSFER_SETTING_TYPE_EXTEND) === 0) ) {
  $fileTypeAreaHiddenClass = "hidden";
}
?>
<?php echo $this->element('MSecuritySettings/script'); ?>
<div id='msecuritysettings_idx' class="card-shadow">

  <div id='msecuritysettings_title'>
    <div class="fLeft"><?= $this->Html->image('file_transfer_setting_top.png', array('alt' => 'ファイル送信設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>セキュリティ設定</h1>
  </div>

  <div id='msecuritysettings_content' class="p20x">
    <?= $this->Form->create('MFileTransferSetting', ['type' => 'post', 'url' => ['controller' => 'MFileTransferSetting', 'action' => 'edit', '']]); ?>
    <?= $this->Form->input('MFileTransferSetting.id', ['type' => 'hidden']); ?>
    <section>
      <h3>ログイン時IP制御設定</h3>
      <ul>
        <li>
          <div id='ip_filter_enable_select_area'>
            <label style="display:inline-block;"<?=($coreSettings[C_COMPANY_USE_SEND_FILE] ? '' : ' style="color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="13" data-content-position-left="-240"') ?>>
              <?php
              $settings = [
                'type' => 'radio',
                'options' => $typeSelect,
                'default' => 0, //FIXME 定数化
                'legend' => false,
                'separator' => '</label><br><label style="display:inline-block;"'.($coreSettings[C_COMPANY_USE_SEND_FILE] ? '' : ' style="color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="9" data-content-position-left="-130"').'>',
                'label' => false,
                'div' => false,
                'error' => false,
                'disabled' => !$coreSettings[C_COMPANY_USE_SEND_FILE],
                'class' => 'pointer'
              ];
              echo $this->Form->input('MSecuritySettings.ip_filter_enabled',$settings);
              ?>
            </label>
          </div>
          <?php if (!empty($errors['type'])) echo "<li class='error-message'>" . h($errors['type'][0]) . "</li>"; ?>
        </li>
      </ul>
      <div id="ip_filter_settings_area" class="<?=$fileTypeAreaHiddenClass?>">
      <pre>
・ホワイトリストまたはブラックリストによる指定が可能です。（いずれかの設定のみ可能）
・CIDRを用いたIPアドレスの範囲指定も可能です。
    例：「192.192.192.0/24」と入力した場合、192.192.192.0～192.192.192.255が一括で除外されます。
・複数指定する場合は改行して入力してください。</pre>
        <p>ホワイトリスト設定</p>
        <s>登録されているアカウントは以下IPのみログインが可能です。</s>
        <?= $this->Form->textarea('MSecuritySettings.ip_filter_whitelist',[
            'class' => 'ip-filter-list-area',
            'error' => false
        ]);?>
        <p>ブラックリスト設定</p>
        <s>登録されているアカウントは以下IPのログインを無効とします。</s>
        <?= $this->Form->textarea('MSecuritySettings.ip_filter_blacklist',[
            'class' => 'ip-filter-list-area',
            'error' => false
        ]);?>
        <?php if (!empty($errors['allow_extensions'])) echo "<li class='error-message'>" . h($errors['allow_extensions'][0]) . "</li>"; ?>
      </div>
    </section>
    <?php $this->Form->end(); ?>
  </div>
  <div id="mtransfersetting_action" class="fotterBtnArea">
    <?php if($coreSettings[C_COMPANY_USE_SEND_FILE]): ?>
    <?= $this->Html->link('元に戻す', 'javascript:void(0)', ['id' => 'reloadBtn','class' => 'whiteBtn btn-shadow']) ?>
    <?= $this->Html->link('更新', 'javascript:void(0)', ['id' => 'updateBtn', 'class' => 'greenBtn btn-shadow']) ?>
    <?= $this->Html->link('dummy', 'javascript:void(0)', ['onclick' => '', 'class' => 'whiteBtn btn-shadow', 'style' => 'visibility: hidden;']) ?>
    <?php endif; ?>
  </div>
</div>
