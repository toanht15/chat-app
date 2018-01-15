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
    <div class="fLeft"><?= $this->Html->image('security_settings_top.png', array('alt' => 'セキュリティ設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>セキュリティ設定</h1>
  </div>

  <div id='msecuritysettings_content' class="p20x">
    <?= $this->Form->create('MSecuritySettings', ['type' => 'post', 'url' => ['controller' => 'MSecuritySettings', 'action' => 'edit', '']]); ?>
    <?= $this->Form->input('MSecuritySettings.id', ['type' => 'hidden']); ?>
    <section>
      <h3>ログイン時IP制御設定</h3>
      <ul>
        <li>
          <div id='ip_filter_enable_select_area'>
            <label style="display:inline-block;">
              <?php
              $settings = [
                'type' => 'radio',
                'options' => $typeSelect,
                'default' => 0, //FIXME 定数化
                'legend' => false,
                'separator' => '</label><br><label style="display:inline-block;"'.(isset($coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]) && $coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER] ? '' : ' style="color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="34.5"').'>',
                'label' => false,
                'div' => false,
                'error' => false,
                'disabled' => !(isset($coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]) && $coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]),
                'class' => 'pointer'
              ];
              echo $this->Form->input('MSecuritySettings.ip_filter_enabled',$settings);
              ?>
            </label>

            <?php
              // radioボタンがdisabledの場合POSTで値が送信されないため、hiddenで送信すべき値を補填する
              if(!(isset($coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]) && $coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER])):
            ?>
            <input type="hidden" name="data[MSecuritySettings][ip_filter_enabled]" value="0"/>
            <?php endif; ?>
          </div>
          <?php if (!empty($errors['active_flg'])) echo "<li class='error-message'>" . h($errors['active_flg'][0]) . "</li>"; ?>
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
