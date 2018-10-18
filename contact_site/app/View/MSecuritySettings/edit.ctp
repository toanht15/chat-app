<?php echo $this->element('MSecuritySettings/script'); ?>
<div id='msecuritysettings_idx' class="card-shadow">

  <div id='msecuritysettings_title'>
    <div class="fLeft"><i class="fal fa-shield-alt fa-2x"></i></div>
    <h1>セキュリティ設定</h1>
  </div>

  <div id='msecuritysettings_content' class="p20x">
    <?= $this->Form->create('MSecuritySettings', ['type' => 'post', 'url' => ['controller' => 'MSecuritySettings', 'action' => 'edit', '']]); ?>
    <?= $this->Form->input('MSecuritySettings.id', ['type' => 'hidden']); ?>
    <section>
      <h3>ログイン時IP制御設定</h3>
      <div class="content">
        <h2 id="contentExplain">指定した接続元IPアドレス以外からの<?php if(!defined('APP_MODE_OEM') || !APP_MODE_OEM): ?>sinclo<?php endif; ?>管理画面の使用を制限します。</h2>
        <ul>
          <li>
            <div id='ip_filter_enable_select_area'>
              <label style="display:inline-block;">
                <?php
                $isFirst = true;
                foreach($typeSelect as $value => $label) {
                  if($value==1){$help = "指定した接続元IPアドレス以外からのsinclo管理画面の使用を制限します。";
                  }else{$help = "指定した接続元IPアドレスからのsinclo管理画面の使用を制限します。";
                  }
                  if($isFirst) {
                    echo '<label style="display:inline-block"><input type="radio" name="data[MSecuritySettings][ip_filter_enabled]" id="MSecuritySettingsIpFilterEnabled'.$value.'" value="'.$value.'" class="pointer" '.(!(isset($coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]) && $coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]) ? 'disabled="disabled"' : '').(strcmp($this->request->data['MSecuritySettings']['ip_filter_enabled'], $value) === 0 ? ' checked="checked"' : '').' >'.$label.'</label><br>';
                    $isFirst = false;
                  } else {
                    echo '<label style="display:inline-block"'.(isset($coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]) && $coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER] ? '' : ' style="color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="15.5"').'><input type="radio" name="data[MSecuritySettings][ip_filter_enabled]" id="MSecuritySettingsIpFilterEnabled'.$value.'" value="'.$value.'" class="pointer" '.(!(isset($coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]) && $coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]) ? 'disabled="disabled"' : '').(strcmp($this->request->data['MSecuritySettings']['ip_filter_enabled'], $value) === 0 ? ' checked="checked"' : '').'>'.$label.'</label><div class = "questionBalloon"><icon class = "questionBtn commontooltip" data-text='.$help.'>?</icon></div><br>';
                  }
                }
                ?>
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
        <div id="ip_white_filter_settings_area" style="display:none">
        <pre>
接続を許可する接続元IPアドレスを指定します。

なお、CIDRを用いたIPアドレスの範囲指定も可能です。

例：「192.192.192.0/24」と入力した場合、192.192.192.0～192.192.192.255が一括で許可されます。

複数指定する場合は改行して入力してください。
        </pre>
          <?= $this->Form->textarea('MSecuritySettings.ip_filter_whitelist',[
            'class' => 'ip-filter-list-area',
            'cols' => 55,
            'rows' => 15,
            'error' => false
          ]);?>
          <?php if (!empty($errors['allow_extensions'])) echo "<li class='error-message'>" . h($errors['allow_extensions'][0]) . "</li>"; ?>
          <?php if (!empty($errors['ips'])) echo "<li class='error-message'>" . h($errors['ips'][0]) . "</li>"; ?>
        </div>
        <div id="ip_black_filter_settings_area" style="display:none">
        <pre>
接続を制限（拒否）する接続元IPアドレスを指定します。

なお、CIDRを用いたIPアドレスの範囲指定も可能です。

例：「192.192.192.0/24」と入力した場合、192.192.192.0～192.192.192.255が一括で制限されます。

複数指定する場合は改行して入力してください。
        </pre>
          <?= $this->Form->textarea('MSecuritySettings.ip_filter_blacklist',[
            'class' => 'ip-filter-list-area',
            'cols' => 55,
            'rows' => 15,
            'error' => false
          ]);?>
          <?php if (!empty($errors['allow_extensions'])) echo "<li class='error-message'>" . h($errors['allow_extensions'][0]) . "</li>"; ?>
          <?php if (!empty($errors['ips'])) echo "<li class='error-message'>" . h($errors['ips'][0]) . "</li>"; ?>
        </div>
      </div>
    </section>
    <?php $this->Form->end(); ?>
  </div>
  <div id="msecuritysettings_action" class="fotterBtnArea">
    <?php if($coreSettings[C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER]): ?>
    <?= $this->Html->link('元に戻す', 'javascript:void(0)', ['id' => 'reloadBtn','class' => 'whiteBtn btn-shadow']) ?>
    <?= $this->Html->link('更新', 'javascript:void(0)', ['id' => 'updateBtn', 'class' => 'greenBtn btn-shadow']) ?>
    <?= $this->Html->link('dummy', 'javascript:void(0)', ['onclick' => '', 'class' => 'whiteBtn btn-shadow', 'style' => 'visibility: hidden;']) ?>
    <?php endif; ?>
  </div>
</div>
