<?php
  foreach($data as $key => $value) {
    $key = $key+1; ?>
    <div id = "unit<?=$key?>">
      <li style = "padding: 0 0 19px 0; width:50em;" id = <?="notification".$key?> class = 'line'>
      <h4 style = "background-color: #ECF4DA;border-color: #C3D69B;border-style: solid;border-width: 1px 0 0 0;margin: 0;font-weight: bold;">
      <span class="removeArea" style = "width: 2em;float: left;text-align: center;padding: 9px 0.75em;height: 34px;">
        <?php if(count($data) == 1) { ?>
           <i onclick = 'removeItem(<?=$key?>)' id = 'remove<?=$key?>' class='remove' style = 'border: 1px solid #878787;background-color: #FFFFFF;background-size: 12px;background-repeat: no-repeat;width: 16px;height: 16px;border-radius: 15px;display: none;background-position: 1px; cursor: pointer;'></i></span>
        <?php }
        else { ?>
            <i onclick = 'removeItem(<?=$key?>)' id = 'remove<?=$key?>' class='remove' style = 'border: 1px solid #878787;background-color: #FFFFFF;background-size: 12px;background-repeat: no-repeat;width: 16px;height: 16px;border-radius: 15px;display: block;background-position: 1px; cursor: pointer;'></i></span>
          <?php } ?>
          <span style = 'display: block;margin-left: 2.5em;padding: 9px 9px 9px 0.25em;height: 34px;' class='labelArea ng-binding''>チャット呼出中メッセージ
          <?php if($value['message'] == '' || mb_strlen($value['message']) > 300) { ?>
            <i style = 'float: right;background-color: #FF8E9E;width: 15px;height: 15px;cursor: pointer;' class='error ng-scope validation'></i>
          <?php }
          else { ?>
            <i style = 'float: right;background-color: #FF8E9E;width: 15px;height: 15px;display:none;cursor: pointer;' class='error ng-scope validation'></i>
          <?php } ?>
            </span>
          </h4>
          <div>
            <?= $this->Form->input('seconds'.$key, array('id' => 'MChatSettingSeconds'.$key, 'min' => 0,'type' => 'number', 'div' => false, 'label' => false, 'style' => 'width: 5.5em;margin-left: 2em;margin-top: 14px;','error' => false)) ?>秒後
          </div>
          <span style = "display:flex;">
            <?=$this->Form->textarea('initial_notification_message'.$key,array('class' => 'notificationTextarea'))?>
            <?php if ( $this->Form->isFieldError('no_standby_sorry_message') ) echo $this->Form->error('no_standby_sorry_message', null, ['wrap' => 'p', 'style' => 'margin: 0;']); ?>
            <span id = "summarized<?=$key?>">
              <span class="greenBtn btn-shadow actBtn choiseButton" onclick="addOption(1,'MChatSettingInitialNotificationMessage<?=$key?>')" id = "choice">選択肢を追加する</span>
              <span class="greenBtn btn-shadow actBtn phoneButton" onclick="addOption(2,'MChatSettingInitialNotificationMessage<?=$key?>')" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon commontooltip" data-text="このボタンを押すと挿入される&lt;  a href  &gt;タグの「ここに電話番号を記載」の個所に電話番号を記入すると、スマホの場合にタップで発信できるようになります"><icon class = "questionBtn">?</icon></div></span>
              <span class="greenBtn btn-shadow actBtn linkMovingButton" onclick="addOption(3,'MChatSettingInitialNotificationMessage<?=$key?>')" id = "thirdSpeechLabel">リンク（ページ遷移）<div class = "questionBalloon commontooltip" data-text="このボタンを押すと挿入される&lt;  a href  &gt;タグの「ここにURLを記載」の個所にURLを記入すると、リンクをクリックした際に新規ページで開きます"><icon class = "questionBtn">?</icon></div></span>
              <span class="greenBtn btn-shadow actBtn linkNewTabButton" onclick="addOption(4,'MChatSettingInitialNotificationMessage<?=$key?>')" id = "secondSpeechLabel">リンク（新規ページ）<div class = "questionBalloon commontooltip" data-text="このボタンを押すと挿入される&lt;  a href  &gt;タグの「ここにURLを記載」の個所にURLを記入すると、リンクをクリックした際にページ遷移します"><icon class = "questionBtn">?</icon></div></span>
            </span>
          </span>
          <div>
          <hr class="separator" style = "margin-top:1em">
          <div>
            <?php
            //「＋」ボタンを表示させない
            if(count($data) == 5) { ?>
              <img onclick = "addItem(<?=$key+1?>)" id = 'add<?=$key?>' src="/img/add.png" alt="登録" class="btn-shadow disOffgreenBtn" width="25" height="25" style="padding: 2px !important; display: none;margin-left: 1.9em;transform: scale(0.8);">
            <?php }
            else { ?>
              <img onclick = "addItem(<?=$key+1?>)" id = 'add<?=$key?>' src="/img/add.png" alt="登録" class="btn-shadow disOffgreenBtn" width="25" height="25" style="padding: 2px !important; display: block;margin-left: 1.9em;transform: scale(0.8);">
            <?php } ?>
          </div>
        </div>
      </li>
    </div>
    <div class='balloon' style='top: 10px; left: 840px; display:none;position: absolute;top: 0;left: 58em;background-color: #FF8E9E;z-index: 5;box-shadow: 0 0 2px rgba(0, 0, 0, 0.3);'><div class='balloonContent' style ='position: relative;width: 30em;min-height: 5em;padding: 0 1em;'><p style = 'margin: 0;padding: 0;margin-top: 5px;color:#FFF'>● チャット呼出中メッセージは３００文字以内で設定してください。</p></div></div>
<?php } ?>
<?=$this->Form->hidden('initial_notification_message')?>