<?php
//
$scHiddenClass = "";
if ( !(!empty($this->data['MChatSetting']['sc_flg']) && strcmp($this->data['MChatSetting']['sc_flg'],C_SC_ENABLED) === 0) ) {
  $scHiddenClass = "sc_hidden";
}
?>
<script type="text/javascript">
var check = false;
var SorryMessageData;
// 同時対応数上限のON/OFF
function scSettingToggle(){
  //対応上限数のsorryメッセージデータ
  if(check == false) {
    check  = true;
    SorryMessageData = $("#MChatSettingWatingCallSorryMessage").val();
  }
  if ( $("#MChatSettingScFlg1").prop("checked") ) { // 同時対応数上限を利用する場合
    $("#sc_content dl").removeClass("sc_hidden"); // ユーザーリストを表示
    $("#sc_content input").prop("disabled", false); // ユーザーリストの数字項目をenabled
    $("#MChatSettingWatingCallSorryMessage").prop("disabled", false); // 対応上限数のsorryメッセージをenabled
    $("#MChatSettingWatingCallSorryMessage").val(SorryMessageData);　// 対応上限数のsorryメッセージを入れる
    $('.settingWatingCallChoice').css('pointer-events','auto'); //追加ボタン制御解除
    $('.settingWatingCallPhone').css('pointer-events','auto'); //追加ボタン制御解除
    $('#wating_call').css('color','#595959'); // 対応上限数のsorryメッセージの文字色を変更
  }
  else { // 同時対応数上限を利用しない場合
    $("#sc_content dl").addClass("sc_hidden"); // ユーザーリストを非表示
    $("#MChatSettingWatingCallSorryMessage").val(""); // 対応上限数のsorryメッセージを空にする
    $("#sc_content input").prop("disabled", true); // ユーザーリストの数字項目をdisabled
    $("#MChatSettingWatingCallSorryMessage").prop("disabled", true); // 対応上限数のsorryメッセージをdisabled
    $('.settingWatingCallChoice').css('pointer-events','none'); //追加ボタン制御
    $(".settingWatingCallPhone").css('pointer-events','none'); //追加ボタン制御
    $('#wating_call').css('color','rgb(204, 204, 204)'); // 対応上限数のsorryメッセージの文字色を変更
  }
}

// 保存処理
function saveAct(){
  document.getElementById('MChatSettingIndexForm').submit();
}

// 元に戻す処理
function reloadAct(){
  window.location.reload();
}

function addOption(type,sorryMessageName){
    sendMessage = document.getElementById(sorryMessageName);
    //変数追加
    addVariable(type,sendMessage);
}

//スクロール位置把握
var topPosition = 0;
window.onload = function() {
  document.querySelector('#content').onscroll = function() {
    topPosition = this.scrollTop;
  };
};

$(document).ready(function(){
  if(<?= $operatingHourData ?> == 1) {
    $("#MChatSettingOutsideHoursSorryMessage").prop("disabled", false); // 営業時間設定のsorryメッセージをenabled
    $('#outside_hours').css('color','#595959'); // 営業時間設定のsorryメッセージの文字色を変更
    $('.settingOutsideHoursChoise').css('pointer-events','auto'); //追加ボタン制御解除
    $('.settingOutsideHoursPhone').css('pointer-events','auto'); //追加ボタン制御解除
  }
  if(<?= $operatingHourData ?> == 2) {
    $("#MChatSettingOutsideHoursSorryMessage").text(""); // 営業時間設定のsorryメッセージを空にする
    $("#MChatSettingOutsideHoursSorryMessage").prop("disabled", true); // 営業時間設定のsorryメッセージをdisabled
    $('#outside_hours').css('color','rgb(204, 204, 204)'); // 営業時間設定のsorryメッセージの文字色を変更
    $('.settingOutsideHoursChoise').css('pointer-events','none'); //追加ボタン制御
    $('.settingOutsideHoursPhone').css('pointer-events','none'); //追加ボタン制御
  }

  // 同時対応数上限のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MChatSetting][sc_flg]"]', scSettingToggle);
  scSettingToggle(); // 初回のみ

    // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    console.log(parentTdId);
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    console.log(targetObj);
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70 + topPosition) + 'px',
      left: $(this).offset().left - 65 + 'px'
    });
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });
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
        <h3>２．初回通知メッセージ</h3>
        <div class="content">
          <pre>このメッセージはサイト訪問者からのメッセージが通知された初回のみ、N秒後に自動送信されます</pre>
          <label style="display:inline-block;">
              <?php
                $settings = [
                  'type' => 'radio',
                  'options' => $scFlgOpt,
                  'default' => 1,
                  'legend' => false,
                  'separator' => '</label><br><label style="display:inline-block;">',
                  'label' => false,
                  'div' => false,
                  'class' => 'pointer'
                ];
                echo $this->Form->input('MChatSetting.sc_flg2',$settings);
              ?>
          </label>
          <li style = "padding: 0 0 19px 0; border-bottom: 1px solid #C3D69B; width:50em; margin-top:1.2em;">
          <h4 style = "background-color: #ECF4DA;cursor: pointer;border-color: #C3D69B;border-style: solid;border-width: 1px 0 0 0;margin: 0;font-weight: bold;">
          <span class="removeArea" style = "width: 2em;float: left;text-align: center;padding: 9px 0.75em;height: 34px;">
            <i></i></span>
              <span style = "display: block;margin-left: 2.5em;padding: 9px 9px 9px 0.25em;height: 34px;" class="labelArea ng-binding" ng-click="main.openList('#triggerItem_' + $id)">滞在時間<!-- ngIf: !itemForm.$valid --></span>
              </h4>
              <div>
                <input name="data[MWidgetSetting][max_show_timing_site]" ng-disabled="showTiming !== &quot;1&quot;" string-to-number="" maxlength="4" style="width: 3.5em;margin-left: 2em;margin-top: 14px;" max="3600" min="0" ng-model="max_show_timing_site" ng-init="max_show_timing_site=&quot;2&quot;;" type="number" value="2" id="MWidgetSettingMaxShowTimingSite" required="required" class="ng-valid ng-valid-min ng-valid-max ng-valid-required ng-valid-maxlength ng-dirty ng-valid-number ng-touched">秒後
                </div>
              <span style = "display:flex;margin-top: 5px;">
                <?=$this->Form->textarea('no_standby_sorry_message2')?>
                <?php if ( $this->Form->isFieldError('no_standby_sorry_message') ) echo $this->Form->error('no_standby_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
                <span class = "summarized">
                  <span class="greenBtn btn-shadow actBtn choiseButton settingOutsideHoursChoise" onclick="addOption(1,'MChatSettingOutsideHoursSorryMessage')">選択肢を追加する</span>
                  <span class="greenBtn btn-shadow actBtn phoneButton settingOutsideHoursPhone" onclick="addOption(2,'MChatSettingOutsideHoursSorryMessage')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
                  <span class="greenBtn btn-shadow actBtn linkMovingButton settingOutsideHoursPhone" onclick="addOption(3,'MChatSettingOutsideHoursSorryMessage')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon questionBalloonPosition15"><icon class = "questionBtn">?</icon></div></span>
                  <span class="greenBtn btn-shadow actBtn linkNewTabButton settingOutsideHoursPhone" onclick="addOption(4,'MChatSettingOutsideHoursSorryMessage')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon questionBalloonPosition14"><icon class = "questionBtn">?</icon></div></span>
                </span>
              </span>
              <div style = "margin-top:17px;">
              <img src="/img/add.png" alt="登録" class="btn-shadow disOffgreenBtn" width="25" height="25" style="padding: 2px !important; display: block;margin-left: 1.9em;margin-top: 14px;">
            </div>
          </li>
          <li style = "padding: 0 0 19px 0; border-bottom: 1px solid #C3D69B; width:50em;">
          <h4 style = "background-color: #ECF4DA;    cursor: pointer;margin: 0;font-weight: bold;">
          <span class="removeArea" style = "width: 2em;float: left;text-align: center;padding: 9px 0.75em;height: 34px;">
            <i class="remove" style = "border: 1px solid #878787;background-color: #FFFFFF;background-size: 12px;background-repeat: no-repeat;width: 16px;height: 16px;border-radius: 15px;display: block;background-position: 1px;"></i></span>
              <span style = "display: block;margin-left: 2.5em;padding: 9px 9px 9px 0.25em;height: 34px;" class="labelArea ng-binding" ng-click="main.openList('#triggerItem_' + $id)">滞在時間<!-- ngIf: !itemForm.$valid --></span>
              </h4>
              <div>
                <input name="data[MWidgetSetting][max_show_timing_site]" ng-disabled="showTiming !== &quot;1&quot;" string-to-number="" maxlength="4" style="width: 3.5em;margin-left: 2em;margin-top: 14px;" max="3600" min="0" ng-model="max_show_timing_site" ng-init="max_show_timing_site=&quot;2&quot;;" type="number" value="2" id="MWidgetSettingMaxShowTimingSite" required="required" class="ng-valid ng-valid-min ng-valid-max ng-valid-required ng-valid-maxlength ng-dirty ng-valid-number ng-touched">秒後
                </div>
              <span style = "display:flex;margin-top: 5px;">
                <?=$this->Form->textarea('no_standby_sorry_message2')?>
                <?php if ( $this->Form->isFieldError('no_standby_sorry_message') ) echo $this->Form->error('no_standby_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
                <span class = "summarized">
                  <span class="greenBtn btn-shadow actBtn choiseButton settingOutsideHoursChoise" onclick="addOption(1,'MChatSettingOutsideHoursSorryMessage')">選択肢を追加する</span>
                  <span class="greenBtn btn-shadow actBtn phoneButton settingOutsideHoursPhone" onclick="addOption(2,'MChatSettingOutsideHoursSorryMessage')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
                  <span class="greenBtn btn-shadow actBtn linkMovingButton settingOutsideHoursPhone" onclick="addOption(3,'MChatSettingOutsideHoursSorryMessage')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon questionBalloonPosition15"><icon class = "questionBtn">?</icon></div></span>
                  <span class="greenBtn btn-shadow actBtn linkNewTabButton settingOutsideHoursPhone" onclick="addOption(4,'MChatSettingOutsideHoursSorryMessage')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon questionBalloonPosition14"><icon class = "questionBtn">?</icon></div></span>
                </span>
              </span>
              <div style = "margin-top:17px;">
              <img src="/img/add.png" alt="登録" class="btn-shadow disOffgreenBtn" width="25" height="25" style="padding: 2px !important; display: block;margin-left: 1.9em;margin-top: 14px;">
            </div>
          </li>
        </div>
      </section>
      <section>
        <h3 class="require">3．Sorryメッセージ</h3>
        <div class="content">
          <pre style = "padding: 0 0 15px 0;">このメッセージは下記の場合に自動送信されます</pre>
          <li style = "padding: 0 0 15px 0;">
            <pre id = "outside_hours">(1)営業時間外にチャットが受信された場合</pre>
              <span style = "display:flex;">
                <?=$this->Form->textarea('outside_hours_sorry_message')?>
                <?php if ( $this->Form->isFieldError('outside_hours_sorry_message') ) echo $this->Form->error('outside_hours_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
                <span class = "summarized">
                  <span class="greenBtn btn-shadow actBtn choiseButton settingOutsideHoursChoise" onclick="addOption(1,'MChatSettingOutsideHoursSorryMessage')">選択肢を追加する</span>
                  <span class="greenBtn btn-shadow actBtn phoneButton settingOutsideHoursPhone" onclick="addOption(2,'MChatSettingOutsideHoursSorryMessage')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
                  <span class="greenBtn btn-shadow actBtn linkMovingButton settingOutsideHoursPhone" onclick="addOption(3,'MChatSettingOutsideHoursSorryMessage')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon questionBalloonPosition15"><icon class = "questionBtn">?</icon></div></span>
                  <span class="greenBtn btn-shadow actBtn linkNewTabButton settingOutsideHoursPhone" onclick="addOption(4,'MChatSettingOutsideHoursSorryMessage')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon questionBalloonPosition14"><icon class = "questionBtn">?</icon></div></span>
                </span>
              </span>
          </li>
          <li style = "padding: 0 0 15px 0;">
            <pre id = "wating_call">(2)対応上限数を超えてのチャットが受信された場合</pre>
            <span style = "display:flex;">
              <?=$this->Form->textarea('wating_call_sorry_message')?>
              <?php if ( $this->Form->isFieldError('wating_call_sorry_message') ) echo $this->Form->error('wating_call_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
              <span class = "summarized">
                <span class="greenBtn btn-shadow actBtn choiseButton settingWatingCallChoice" onclick="addOption(1,'MChatSettingWatingCallSorryMessage')">選択肢を追加する</span>
                <span class="greenBtn btn-shadow actBtn phoneButton settingWatingCallPhone" onclick="addOption(2,'MChatSettingWatingCallSorryMessage')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
                <span class="greenBtn btn-shadow actBtn linkMovingButton settingWatingCallPhone" onclick="addOption(3,'MChatSettingWatingCallSorryMessage')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon questionBalloonPosition15"><icon class = "questionBtn">?</icon></div></span>
                <span class="greenBtn btn-shadow actBtn linkNewTabButton settingWatingCallPhone" onclick="addOption(4,'MChatSettingWatingCallSorryMessage')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon questionBalloonPosition14"><icon class = "questionBtn">?</icon></div></span>
              </span>
            </span>
          </li>
          <li style = "padding: 0 0 15px 0;">
            <pre id = "no_standby">(3)在席オペレーターが居ない場合にチャットが受信された場合</pre>
            <span style = "display:flex;">
              <?=$this->Form->textarea('no_standby_sorry_message')?>
              <?php if ( $this->Form->isFieldError('no_standby_sorry_message') ) echo $this->Form->error('no_standby_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
              <span class = "summarized">
                <span class="greenBtn btn-shadow actBtn choiseButton" onclick="addOption(1,'MChatSettingNoStandbySorryMessage')">選択肢を追加する</span>
                <span class="greenBtn btn-shadow actBtn phoneButton" onclick="addOption(2,'MChatSettingNoStandbySorryMessage')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
                <span class="greenBtn btn-shadow actBtn linkMovingButton" onclick="addOption(3,'MChatSettingNoStandbySorryMessage')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon questionBalloonPosition15"><icon class = "questionBtn">?</icon></div></span>
                <span class="greenBtn btn-shadow actBtn linkNewTabButton" onclick="addOption(4,'MChatSettingNoStandbySorryMessage')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon questionBalloonPosition14"><icon class = "questionBtn">?</icon></div></span>
              </span>
            </span>
          </li>
        </div>
      </section>
      <?=$this->Form->input('MChatSetting.id', ['type' => 'hidden'])?>

    <?= $this->Form->end(); ?>
    <div id="m_widget_setting_action" class="fotterBtnArea">
      <?= $this->Html->link('元に戻す', 'javascript:void(0)', ['onclick' => 'reloadAct()','class' => 'whiteBtn btn-shadow']) ?>
      <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
      <?= $this->Html->link('dummy', 'javascript:void(0)', ['onclick' => '', 'class' => 'whiteBtn btn-shadow', 'style' => 'visibility: hidden;']) ?>
    </div>
    <div id='lastSpeechTooltip' class="explainTelTooltip">
      <icon-annotation>
        <ul>
          <li><span>このボタンを押すと挿入される＜telno＞タグの間に電話番号を記入すると、スマホの場合にタップで発信できるようになります</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='secondSpeechTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>このボタンを押すと挿入される＜a href＞タグの「ここにURLを記載」の個所にURLを記入すると、リンクをクリックした際に新規ページで開きます</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='thirdSpeechTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>このボタンを押すと挿入される＜a href＞タグの「ここにURLを記載」の個所にURLを記入すると、リンクをクリックした際にページ遷移します</span></li>
        </ul>
      </icon-annotation>
    </div>
  </div>



