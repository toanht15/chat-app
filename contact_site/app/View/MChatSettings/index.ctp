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

function inSettingToggle(){
  if ( $("#MChatSettingInFlg1").prop("checked") ) { // 同時対応数上限を利用する場合
    $("#in_content").slideDown("fast");
  }
  else { // 同時対応数上限を利用しない場合
    $("#in_content").slideUp("fast");
  }
}

// 元に戻す処理
function reloadAct(){
  window.location.reload();
}

function addOption(type,sorryMessageName){
    sendMessage = document.getElementById(sorryMessageName);
    //バリデーション
    if($('#'+sorryMessageName).val().length < 300) {
      $('#'+sorryMessageName).closest('li').find('.validation').hide();
    }
    else {
      $('#'+sorryMessageName).closest('li').find('.validation').show();
    }
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

  if(<?= $in_flg ?> == 2) {
    $('#in_content').css('display','none');
  }

  // 同時対応数上限のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MChatSetting][sc_flg]"]', scSettingToggle);
  scSettingToggle(); // 初回のみ

  // チャット呼出中メッセージのON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MChatSetting][in_flg]"]', inSettingToggle);
  inSettingToggle(); // 初回のみ
  // ツールチップの表示制御
  indicateTooltip();

  //バリデーションチェック
  checkValidate();
});

//初回メッセージ項目削除
function removeItem(number) {
  var length = $('.line').length;
  $('#unit'+number).remove();
  if(length == 5) {
    $("#add5").css('display', 'block');
  }
  //削除した下の行を全て一つ上げる
  for(i=number+1; i<=length;i++) {
    document.getElementById('unit' + i).id = 'unit' + (i-1);
    document.getElementById('notification' + i).id = 'notification' + (i-1);
    document.getElementById('MChatSettingSeconds' + i).name = 'data[MChatSetting][seconds'+ (i-1) +']';
    document.getElementById('MChatSettingSeconds' + i).id = 'MChatSettingSeconds' + (i-1);
    document.getElementById('MChatSettingInitialNotificationMessage' + i).name = 'data[MChatSetting][initial_notification_message'+ (i-1) +']';
    document.getElementById('MChatSettingInitialNotificationMessage' + i).id = 'MChatSettingInitialNotificationMessage' + (i-1);
    $("#remove"+i).attr('onclick',"removeItem("+(i-1)+")");
    document.getElementById('remove' + i).id = 'remove' + (i-1);
    $("#add"+i).attr('onclick',"addItem("+(i)+")");
    document.getElementById('add' + i).id = 'add' + (i-1);
    $("#summarized"+i+" #choise").attr('onclick',"addOption(1,'MChatSettingInitialNotificationMessage"+(i-1)+"')");
    $("#summarized"+i+" #secondSpeechLabel").attr('onclick',"addOption(4,'MChatSettingInitialNotificationMessage"+(i-1)+"')");
    $("#summarized"+i+" #thirdSpeechLabel").attr('onclick',"addOption(3,'MChatSettingInitialNotificationMessage"+(i-1)+"')");
    $("#summarized"+i+" #lastSpeechLabel").attr('onclick',"addOption(2,'MChatSettingInitialNotificationMessage"+(i-1)+"')");
    document.getElementById('summarized' + i).id = 'summarized' + (i-1);
  }
  //チャット呼び出し中メッセージが1つしかない場合は削除ボタンを表示しない
  if(length == 2) {
    $("#remove1").css('display', 'none');
  }
}

//初回メッセージ項目追加
function addItem(number) {
  var length = $('.line').length;
  //チャット呼び出し中メッセージが複数ある場合は削除ボタンを表示する
  if(length == 1) {
    $("#remove1").css('display', 'block');
  }
  if(length < 5) {
    for(i=length;i>=number;i--) {
      document.getElementById('unit' + i).id = 'unit' + (i+1);
      document.getElementById('notification' + i).id = 'notification' + (i+1);
      document.getElementById('MChatSettingSeconds' + i).name = 'data[MChatSetting][seconds'+ (i+1) +']';
      document.getElementById('MChatSettingSeconds' + i).id = 'MChatSettingSeconds' + (i+1);
      document.getElementById('MChatSettingInitialNotificationMessage' + i).name = 'data[MChatSetting][initial_notification_message'+ (i+1) +']';
      document.getElementById('MChatSettingInitialNotificationMessage' + i).id = 'MChatSettingInitialNotificationMessage' + (i+1);
      $("#remove"+i).attr('onclick',"removeItem("+(i+1)+")");
      document.getElementById('remove' + i).id = 'remove' + (i+1);
      $("#add"+i).attr('onclick',"addItem("+(i+2)+")");
      document.getElementById('add' + i).id = 'add' + (i+1);
      $("#summarized"+i+" #choice").attr('onclick',"addOption(1,'MChatSettingInitialNotificationMessage"+(i+1)+"')");
      $("#summarized"+i+" #secondSpeechLabel").attr('onclick',"addOption(4,'MChatSettingInitialNotificationMessage"+(i+1)+"')");
      $("#summarized"+i+" #thirdSpeechLabel").attr('onclick',"addOption(3,'MChatSettingInitialNotificationMessage"+(i+1)+"')");
      $("#summarized"+i+" #lastSpeechLabel").attr('onclick',"addOption(2,'MChatSettingInitialNotificationMessage"+(i+1)+"')");
      document.getElementById('summarized' + i).id = 'summarized' + (i+1);
    }
    var
        content    = "<div id = unit"+number+">"
        content    += "<li style = 'padding: 0 0 19px 0; width:50em;' id = 'notification"+number+"' class = 'line'>";
        content    += "  <h4 style = 'background-color: #ECF4DA;margin: 0;font-weight:bold;'>";
        content    += "  <span class='removeArea' style = 'width: 2em;float: left;text-align: center;padding: 9px 0.75em;height: 34px;'>";
        content    += "    <i onclick = 'removeItem("+number+")' id = 'remove"+number+"' class = 'remove' style = 'cursor:pointer; border: 1px solid #878787;background-color: #FFFFFF;background-size: 12px;background-repeat: no-repeat;width: 16px;height: 16px;border-radius: 15px;display: block;background-position: 1px;'></i></span>";
        content    += "    <span style = 'display: block;margin-left: 2.5em;padding: 9px 9px 9px 0.25em;height: 34px;' class='labelArea ng-binding''>チャット呼出中メッセージ<i style = 'float: right;background-color: #FF8E9E;width: 15px;height: 15px;cursor:pointer;' class='error ng-scope validation'></i></span>";
        content    += "  </h4>";
        content    += "<div>";
        content    += "<input name='data[MChatSetting][seconds"+number+"]' min = '0' value = '0' style='width: 5.5em;margin-left: 2em;margin-top: 14px;padding: 5px 10px;border: none;border-bottom: 1px solid #909090;' type='number' id='MChatSettingSeconds"+number+"'/>秒後";
        content    += "  </div>";
        content    += "  <span style = 'display:flex;'>";
        content    += "     <textarea name='data[MChatSetting][initial_notification_message"+number+"]' class = 'notificationTextarea' id='MChatSettingInitialNotificationMessage"+number+"'></textarea>";
        content    += "    <span id = 'summarized"+number+"' style = 'margin-left:10px;'>";
        content    += "    <span class='greenBtn btn-shadow actBtn choiseButton' onclick=\"addOption(1,'MChatSettingInitialNotificationMessage"+number+"')\" id = 'choice'>選択肢を追加する</span>";
        content    += "    <span class='greenBtn btn-shadow actBtn phoneButton' onclick=\"addOption(2,'MChatSettingInitialNotificationMessage"+number+"')\" id = 'lastSpeechLabel'>電話番号を追加する<div class = 'questionBalloon questionBalloonPosition13'><icon class = 'questionBtn'>?</icon></div></span>";
        content    += "    <span class='greenBtn btn-shadow actBtn linkMovingButton' onclick=\"addOption(3,'MChatSettingInitialNotificationMessage"+number+"')\" id = 'thirdSpeechLabel'>リンク（ページ遷移）<div class = 'questionBalloon questionBalloonPosition15'><icon class = 'questionBtn'>?</icon></div></span>";
        content    += "    <span class='greenBtn btn-shadow actBtn linkNewTabButton' onclick=\"addOption(4,'MChatSettingInitialNotificationMessage"+number+"')\" id = 'secondSpeechLabel'>リンク（新規ページ）<div class = 'questionBalloon questionBalloonPosition14'><icon class = 'questionBtn'>?</icon></div></span>";
        content    += "  </span>";
        content    += "</span>";
        content    += "<div>";
        content    += "<hr class='separator' style = 'margin-top:1em'>";
        content    += "  <div>";
        content    += " <img onclick = 'addItem("+(number+1)+")' id = 'add"+number+"' src='/img/add.png' alt='登録' class='btn-shadow disOffgreenBtn' width='25' height='25' style='padding: 2px !important; display: block;margin-left: 1.9em;transform: scale(0.8);'>";
        content    += "  </div>";
        content    += "  </div>";
        content    += "</li>";
        content    += "<div class='balloon' style='top: 10px; left: 840px; display:none;position: absolute;top: 0;left: 58em;background-color: #FF8E9E;z-index: 5;box-shadow: 0 0 2px rgba(0, 0, 0, 0.3);''><div class='balloonContent' style ='position: relative;width: 30em;min-height: 5em;padding: 0 1em;'><p style = 'margin: 0;padding: 0;margin-top: 5px;color:#FFF'>● チャット呼出中メッセージは３００文字以内で設定してください</p></div></div>";
        content    += "</div>";
      $('#unit'+(number-1)).after(content);
      if(length == 4) {
        $("#add5").css('display', 'none');
      }
  }
  // ツールチップの表示制御
  indicateTooltip();
  //バリデーションチェック
  checkValidate();

}

function saveAct() {
  var length = $('.line').length;
  var setList = {};
  for (var i = 0; i < length; i++){
    setList[i] = {'seconds': $('#MChatSettingSeconds'+(i+1)).val(),'message': $('#MChatSettingInitialNotificationMessage'+(i+1)).val()};
  }
  $('#MChatSettingInitialNotificationMessage').val(JSON.stringify(setList));
  document.getElementById('MChatSettingIndexForm').submit();
}

function indicateTooltip() {
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
}

function checkValidate() {
  var balloon = $("div.balloon");
  $('.validation').bind('mouseenter', function(e) {
      if($(this).val() == "" || $(this).val().length > 300) {
        var top = $(this).closest("li").prop('offsetTop');
        var left = $(this).closest("li").prop('offsetLeft');
        var width = $(this).closest("li").prop('offsetWidth');
        balloon.css({
            "top": top + 10,
            "left": width + left
        }).show();
      }
  });
  $('.validation').bind('mouseleave', function() {
      balloon.hide();
  });

  $(document).on('keyup', '.notificationTextarea', function(e) {
    if($(this).val() !== "" && $(this).val().length < 300) {
      $(this).closest('li').find('.validation').hide();
    }
    else {
      $(this).closest('li').find('.validation').show();
    }
  });
}

</script>
<div id='m_chat_settings_idx' class="card-shadow">

  <div id='m_chat_settings_add_title'>
      <div class="fLeft">
        <i class="fal fa-cog fa-2x"></i>
      </div>
      <h1>チャット基本設定</h1>
  </div>
  <div id='m_chat_settings_form' class="p20x">
    <?= $this->Form->create('MChatSetting', ['type' => 'post', 'url' => ['controller' => 'MChatSettings', 'action' => 'index', '']]); ?>
      <section>
        <h3>１．同時対応数上限</h3>
        <div class ="content">
          <span class = "pre">オペレータが同時にチャット対応できる上限数を設定することができます。&#10;ここで設定した同時対応数に達したオペレータには新着チャットのデスクトップ通知が表示されなくなります。&#10;また、すべてのオペレータが同時対応数の上限に達している際に新着チャットが送信された場合には、チャット送信者（サイト訪問者）に対してsorryメッセージ（当画面下段にて設定可能）を自動返信します。</span>
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
        <h3>２．チャット呼出中メッセージ</h3>
        <div class="content">
          <span class = "pre">有人チャットを受信後、オペレータが入室するまでの間に任意のメッセージを自動送信することができます。&#10;最初の有人チャットを受信してからオペレータが入室するまでの経過時間により、自動送信するメッセージを複数設定することが可能です。</span>
          <label style="display:inline-block;">
              <?php
                $settings = [
                  'type' => 'radio',
                  'options' => $scFlgOpt,
                  'default' => C_IN_DISABLED,
                  'legend' => false,
                  'separator' => '</label><br><label style="display:inline-block;"'.($coreSettings[C_COMPANY_USE_CHATCALLMESSAGES] ? '' : ' style="color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="34.5"').'>',
                  'label' => false,
                  'div' => false,
                  'disabled' => !$coreSettings[C_COMPANY_USE_CHATCALLMESSAGES],
                  'class' => 'pointer'
                ];
                echo $this->Form->input('MChatSetting.in_flg',$settings);
              ?>
          </label>
          <div id = "in_content" style = "margin-top:1.2em">
            <?= $this->element('MChatSettings/templates'); ?>
          </div>
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
                <span class = "summarized">
                  <span class="greenBtn btn-shadow actBtn choiseButton settingOutsideHoursChoise" onclick="addOption(1,'MChatSettingOutsideHoursSorryMessage')">選択肢を追加する</span>
                  <span class="greenBtn btn-shadow actBtn phoneButton settingOutsideHoursPhone" onclick="addOption(2,'MChatSettingOutsideHoursSorryMessage')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
                  <span class="greenBtn btn-shadow actBtn linkMovingButton settingOutsideHoursPhone" onclick="addOption(3,'MChatSettingOutsideHoursSorryMessage')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon questionBalloonPosition15"><icon class = "questionBtn">?</icon></div></span>
                  <span class="greenBtn btn-shadow actBtn linkNewTabButton settingOutsideHoursPhone" onclick="addOption(4,'MChatSettingOutsideHoursSorryMessage')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon questionBalloonPosition14"><icon class = "questionBtn">?</icon></div></span>
                </span>
              </span>
              <?php if ( $this->Form->isFieldError('outside_hours_sorry_message') ) echo $this->Form->error('outside_hours_sorry_message', null, ['wrap' => 'p', 'style' => 'margin-top: 15px;']); ?>
          </li>
          <li style = "padding: 0 0 15px 0;">
            <pre id = "wating_call">(2)対応上限数を超えてのチャットが受信された場合</pre>
            <span style = "display:flex;">
              <?=$this->Form->textarea('wating_call_sorry_message')?>
              <span class = "summarized">
                <span class="greenBtn btn-shadow actBtn choiseButton settingWatingCallChoice" onclick="addOption(1,'MChatSettingWatingCallSorryMessage')">選択肢を追加する</span>
                <span class="greenBtn btn-shadow actBtn phoneButton settingWatingCallPhone" onclick="addOption(2,'MChatSettingWatingCallSorryMessage')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
                <span class="greenBtn btn-shadow actBtn linkMovingButton settingWatingCallPhone" onclick="addOption(3,'MChatSettingWatingCallSorryMessage')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon questionBalloonPosition15"><icon class = "questionBtn">?</icon></div></span>
                <span class="greenBtn btn-shadow actBtn linkNewTabButton settingWatingCallPhone" onclick="addOption(4,'MChatSettingWatingCallSorryMessage')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon questionBalloonPosition14"><icon class = "questionBtn">?</icon></div></span>
              </span>
            </span>
            <?php if ( $this->Form->isFieldError('wating_call_sorry_message') ) echo $this->Form->error('wating_call_sorry_message', null, ['wrap' => 'p', 'style' => 'margin-top: 15px;']); ?>
          </li>
          <li style = "padding: 0 0 40px 0;">
            <pre id = "no_standby">(3)在席オペレーターが居ない場合にチャットが受信された場合</pre>
            <span style = "display:flex;">
              <?=$this->Form->textarea('no_standby_sorry_message')?>
              <span class = "summarized">
                <span class="greenBtn btn-shadow actBtn choiseButton" onclick="addOption(1,'MChatSettingNoStandbySorryMessage')">選択肢を追加する</span>
                <span class="greenBtn btn-shadow actBtn phoneButton" onclick="addOption(2,'MChatSettingNoStandbySorryMessage')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
                <span class="greenBtn btn-shadow actBtn linkMovingButton" onclick="addOption(3,'MChatSettingNoStandbySorryMessage')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon questionBalloonPosition15"><icon class = "questionBtn">?</icon></div></span>
                <span class="greenBtn btn-shadow actBtn linkNewTabButton" onclick="addOption(4,'MChatSettingNoStandbySorryMessage')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon questionBalloonPosition14"><icon class = "questionBtn">?</icon></div></span>
              </span>
            </span>
            <?php if ( $this->Form->isFieldError('no_standby_sorry_message') ) echo $this->Form->error('no_standby_sorry_message', null, ['wrap' => 'p', 'style' => 'margin-top: 15px;']); ?>
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



