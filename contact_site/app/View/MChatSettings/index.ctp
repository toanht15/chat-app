<?php
//
$scHiddenClass = "";
if ( !(!empty($this->data['MChatSetting']['sc_flg']) && strcmp($this->data['MChatSetting']['sc_flg'],C_SC_ENABLED) === 0) ) {
  $scHiddenClass = "sc_hidden";
  $this->log('送られてくるデータ',LOG_DEBUG);
  $this->log($this->data,LOG_DEBUG);
}
?>
<script type="text/javascript">
// 同時対応数上限のON/OFF
function scSettingToggle(){
  if ( $("#MChatSettingScFlg1").prop("checked") ) { // 同時対応数上限を利用する場合
    $("#sc_content dl").removeClass("sc_hidden"); // ユーザーリストを表示
    $("#sc_content input").prop("disabled", false); // ユーザーリストの数字項目をenabled
    $("#MChatSettingWatingCallSorryMessage").prop("disabled", false); // 対応上限数のsorryメッセージをenabled
    document.getElementById("MChatSettingWatingCallSorryMessage").innerHTML = "<?= $this->data['MChatSetting']['wating_call_sorry_message'] ?>";
    $('#wating_call').css('color','#595959'); // 対応上限数のsorryメッセージの文字色を変更
  }
  else { // 同時対応数上限を利用しない場合
    $("#sc_content dl").addClass("sc_hidden"); // ユーザーリストを非表示
    $("#sc_content input").prop("disabled", true); // ユーザーリストの数字項目をdisabled
    $("#MChatSettingWatingCallSorryMessage").prop("disabled", true); // 対応上限数のsorryメッセージをdisabled
    $("#MChatSettingWatingCallSorryMessage").text("");
    $('#wating_call').css('color','rgb(204, 204, 204)'); // 対応上限数のsorryメッセージの文字色を変更
  }
}

// 保存処理
function saveAct(){
  document.getElementById('MChatSettingIndexForm').submit();
}

$(document).ready(function(){
  if(<?= $operatingHourData ?> == 1) {
    $("#MChatSettingOutsideHoursSorryMessage").prop("disabled", false); // 対応上限数のsorryメッセージをenabled
    $('#outside_hours').css('color','#595959'); // 対応上限数のsorryメッセージの文字色を変更
  }
  if(<?= $operatingHourData ?> == 2) {
    $("#MChatSettingOutsideHoursSorryMessage").prop("disabled", true); // 対応上限数のsorryメッセージをdisabled
    $('#outside_hours').css('color','rgb(204, 204, 204)'); // 対応上限数のsorryメッセージの文字色を変更
  }

  // 同時対応数上限のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MChatSetting][sc_flg]"]', scSettingToggle);
  scSettingToggle(); // 初回のみ
});



</script>
<div id='m_chat_settings_idx' class="card-shadow">

  <div id='m_chat_settings_add_title'>
      <div class="fLeft">
        <?= $this->Html->image('chat_setting_g.png', array('alt' => 'チャット基本設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?>
      </div>
      <h1>チャット基本設定</h1>
  </div>
  <div id='m_chat_settings_form' class="p20x">
    <?= $this->Form->create('MChatSetting', ['type' => 'post', 'url' => ['controller' => 'MChatSettings', 'action' => 'index', '']]); ?>
      <section>
        <h3>１．同時対応数上限</h3>
        <div class ="content">
          <div>
            <label style="display:inline-block;" <?php echo $coreSettings[C_COMPANY_USE_CHAT_LIMITER] ? '' : 'style="color: #CCCCCC;" '?>>
              <?php
                $settings = [
                  'type' => 'radio',
                  'options' => $scFlgOpt,
                  'default' => C_SC_DISABLED,
                  'legend' => false,
                  'separator' => '</label><br><label style="display:inline-block;"'.($coreSettings[C_COMPANY_USE_CHAT_LIMITER] ? '' : ' style="color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="34.5"').'>',
                  'label' => false,
                  'div' => false,
                  'disabled' => !$coreSettings[C_COMPANY_USE_CHAT_LIMITER],
                  'class' => 'pointer'
                ];
                echo $this->Form->input('MChatSetting.sc_flg',$settings);
              ?>
            </label>
            <?php
            // radioボタンがdisabledの場合POSTで値が送信されないため、hiddenで送信すべき値を補填する
            if(!$coreSettings[C_COMPANY_USE_CHAT_LIMITER]):
              ?>
              <input type="hidden" name="data[MChatSetting][sc_flg]" value="2"/>
            <?php endif; ?>
          </div>
          <div id="sc_content">
            <dl class="<?=$scHiddenClass?>">
              <dt>基本<dt-detail>（※ ユーザー作成時に自動で割り振られる上限数です。）</dt-detail></dt>
                <dd>
                  <span>同時対応上限数</span>
                  <?=$this->Form->input('sc_default_num', ['type' => 'number', 'min' => 0, 'max' => 99, 'label' => false, 'div' => false, 'error' => false])?>
                </dd>
                <?php if ( $this->Form->isFieldError('sc_default_num') ) echo $this->Form->error('sc_default_num', null, ['wrap' => 'p']); ?>
              <dt>個別</dt>
              <div>
                <?php foreach( $mUserList as $val ){ ?>
                  <?php
                    $settings = json_decode($val['MUser']['settings']);
                    $sc_num = ( !empty($settings->sc_num) ) ? $settings->sc_num : 0;
                    if ( !(isset($this->data['MChatSetting']['sc_flg']) && $this->data['MChatSetting']['sc_flg']) ) {
                      $sc_num = "";
                    }
                  ?>
                  <dd>
                    <span><?=h($val['MUser']['display_name'])?></span>
                    <?=$this->Form->input('MUser.'.$val['MUser']['id'].'.sc_num', ['type' => 'number', 'default' => $sc_num, 'min' => 0, 'max' => 99, 'label' => false, 'div' => false, 'error' => false])?>
                  </dd>
                  <?php if ( $this->Form->isFieldError('MUser.'.$val['MUser']['id'].'.sc_num') ) echo $this->Form->error('MUser.'.$val['MUser']['id'].'.sc_num', null, ['wrap' => 'p']); ?>
                <?php } ?>
              </div>
            </dl>
          </div>
        </div>
      </section>
      <section>
        <h3 class="require">２．Sorryメッセージ</h3>
        <div class="content">
          <pre style = "padding: 0 0 15px 0;">このメッセージは下記の場合に自動送信されます</pre>
          <li style = "padding: 0 0 15px 0;">
          <pre id = "outside_hours">(1)営業時間外にチャットが受信された場合</pre>
          <?=$this->Form->textarea('outside_hours_sorry_message')?>
          <?php if ( $this->Form->isFieldError('outside_hours_sorry_message') ) echo $this->Form->error('outside_hours_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
          </li>
          <li style = "padding: 0 0 15px 0;">
        <pre id = "wating_call">(2)対応上限数を超えてのチャットが受信された場合</pre>
          <?=$this->Form->textarea('wating_call_sorry_message')?>
          <?php if ( $this->Form->isFieldError('wating_call_sorry_message') ) echo $this->Form->error('wating_call_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
          </li>
          <li style = "padding: 0 0 15px 0;">
         <pre id = "no_standby">(3)在席オペレーターが居ない場合にチャットが受信された場合</pre>
          <?=$this->Form->textarea('no_standby_sorry_message')?>
          <?php if ( $this->Form->isFieldError('no_standby_sorry_message') ) echo $this->Form->error('no_standby_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
        </li>
        </div>
      </section>
      <?=$this->Form->input('MChatSetting.id', ['type' => 'hidden'])?>

    <?= $this->Form->end(); ?>
    <div id="m_widget_setting_action" class="fotterBtnArea">
    <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
  </div>
  </div>


