<?php /* メール送信 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>" class="set_action_item_body action_send_mail" ng-init="main.controllMailSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb13em"><label>送信先メールアドレス<span class="questionBalloon"><icon class="questionBtn" data-tooltip="送信先のメールアドレスを設定します。<br>（変数の利用も可能です）" data-tooltip-width='210'>?</icon></span></label></span>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, addressItem) in setItem.toAddress track by $index">
          <input type="text" ng-model="setItem.toAddress[listId]" ng-init="setItem.toAddress[listId] = setItem.toAddress[listId]" default="">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
          </div>
        </li>
      </ul>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>メールタイトル<span class="questionBalloon"><icon class="questionBtn" data-tooltip="メールタイトルを設定します。<br>（変数の利用も可能です）" data-tooltip-width='165'>?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.subject">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>差出人名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="差出人名を設定します。<br>（変数の利用も可能です）" data-tooltip-width='145'>?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.fromName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>メール本文タイプ</label></span>
      <div>
        <label ng-repeat="(key, item) in sendMailTypeList" class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_mail_type" value="{{key}}" ng-model="setItem.mailType">{{item.label}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="{{item.tooltip}}" data-tooltip-width='240'>?</icon></span></label>
        <resize-textarea ng-if="setItem.mailType == <?= C_SCENARIO_MAIL_TYPE_CUSTOMIZE ?>" ng-model="setItem.template" cols="48" rows="1" placeholder="メール本文を入力してください" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <label class="fb13em pointer p05tb"><input type="checkbox" ng-model="setItem.sendWithDownloadURL" ng-init="setItem.sendWithDownloadURL">添付ファイルがある場合、ダウンロードURLをメール本文に記載する</label>
  </ul>
</div>