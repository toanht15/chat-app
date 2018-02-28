<?php /* テキスト発言 | C_SCENARIO_ACTION_TEXT */ ?>
<div ng-if="setItem.actionType == 1" class="set_action_item_body action_text">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label>発言内容</label></span>
      <div>
        <resize-textarea name="message" ng-model="setItem.message" cols="48" rows="4" placeholder="メッセージを入力してください" ng-required="true"></resize-textarea>
      </div>
    </li>
  </ul>
</div>

<?php /* ヒアリング | C_SCENARIO_ACTION_HEARING */ ?>
<div ng-if="setItem.actionType == 2" class="set_action_item_body action_hearing" ng-init="main.controllHearingSettingView(setActionId)">
  <ul>
    <li>
      <table cellspacing="5">
        <thead>
          <tr>
            <th class="hearingVariableNameLabel">変数名<div class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットから投げかけた質問の回答を保存し、{&thinsp;{変数名}&thinsp;}としてメッセージ内で利用することができるようになります">?</icon></div></th>
            <th class="hearingVariableTypeLabel">タイプ<div class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者が入力した回答が適切か、整合性チェックを行うことができるようになります">?</icon></div></th>
            <th class="hearingVariableAllowLF">改行<div class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者が回答を入力するとき、改行を行うか制御できるようになります">?</icon></div></th>
            <th class="hearingVariableQuestionLabel">質問内容</th>
            <th class="hearginVariableBtnGroupLabel"></th>
          </tr>
        </thead>
        <tbody class="itemListGroup">
          <tr ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
            <td><input type="text" ng-model="hearingItem.variableName" class="frame"></td>
            <td>
              <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType" ng-options="index as type.label for (index, type) in inputTypeList" class="frame"></select>
            </td>
            <td><input type="checkbox" ng-model="hearingItem.allowInputLF" ng-init="hearingItem.allowInputLF = hearingItem.allowInputLF == 1"></td>
            <td class="message"><input type="text" ng-model="hearingItem.message" class="frame"></td>
            <td class="btnBlock">
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList(setActionId, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList(setActionId, listId)')) ?></a>
            </td>
          </tr>
        </tbody>
      </table>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em"><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ<div class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者が入力した回答が不正な内容の場合に、返信するメッセージになります">?</icon></div></label></span>
      <div>
        <resize-textarea name="errorMessage" ng-model="setItem.errorMessage" cols="48" rows="4" placeholder="入力エラー時の返信メッセージを入力してください"></resize-textarea>
      </div>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.isConfirm" ng-init="setItem.isConfirm = setItem.isConfirm == 1">入力内容の確認を行う</label>
      <ul ng-if="setItem.isConfirm == true" class="indentDown">
        <li class="styleFlexbox">
          <span class="fb11em"><label>確認内容</label></span>
          <div>
            <resize-textarea name="confirmMessage" ng-model="setItem.confirmMessage" cols="48" rows="4" placeholder="確認内容のメッセージを入力してください"></resize-textarea>
          </div>
        </li>
        <li class="styleFlexbox">
          <span class="fb11em"><label>選択肢（OK）<div class="questionBalloon"><icon class="questionBtn" data-tooltip="すべての項目が正常に入力されたことを確認する選択肢です">?</icon></div></label></span>
          <div>
            <input type="text" name="success" ng-model="setItem.success">
          </div>
        </li>
        <li class="styleFlexbox">
          <span class="fb11em"><label>選択肢（NG）<div class="questionBalloon"><icon class="questionBtn" data-tooltip="項目が正常に入力されず、ヒアリングを先頭から実施し直す選択肢です">?</icon></div></label></span>
          <div>
            <input type="text" name="cancel" ng-model="setItem.cancel">
          </div>
        </li>
      </ul>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.cv" ng-init="setItem.cv = setItem.cv == 1">成果にCVとして登録する</label>
    </li>
  </ul>
</div>

<?php /* 選択肢 | C_SCENARIO_ACTION_SELECT_OPTION */ ?>
<div ng-if="setItem.actionType == 3" class="set_action_item_body action_select_option" ng-init="main.controllSelectOptionSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label class="hearingSelectVariableNameLabel">変数名<div class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットから投げかけた質問の回答を保存し、{&thinsp;{変数名}&thinsp;}としてメッセージ内で利用することができるようになります">?</icon></div></label></span>
      <div>
        <input type="text" ng-model="setItem.selection.variableName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb7em"><label>質問内容</label></span>
      <div>
        <resize-textarea name="message" ng-model="setItem.message" cols="48" rows="4" placeholder="質問内容のメッセージを入力してください"></resize-textarea>
      </div>
    </li>
    <li>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, optionItem) in setItem.selection.options track by $index" class="styleFlexbox" ng-init="options = setItem.selection.options">
          <span class="fb7em"><label>選択肢 {{listId+1}}</label></span>
          <div>
            <input type="text" ng-model="setItem.selection.options[listId]">
            <div class="btnBlock">
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList(setActionId, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList(setActionId, listId)')) ?></a>
            </div>
          </div>
        </li>
      </ul>
    </li>
  </ul>
</div>

<?php /* メール送信 | C_SCENARIO_ACTION_SEND_MAIL */ ?>
<div ng-if="setItem.actionType == 4" class="set_action_item_body action_send_mail" ng-init="main.initMailSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb11em"><label>送信先メールアドレス</label></span>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, addressItem) in setItem.toAddress track by $index">
          <input type="text" ng-model="setItem.toAddress[listId]" ng-init="setItem.toAddress[listId] = setItem.toAddress[listId]" default="">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList(setActionId, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList(setActionId, listId)')) ?></a>
          </div>
        </li>
      </ul>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em"><label>メールタイトル</label></span>
      <div>
        <input type="text" ng-model="setItem.subject">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em"><label>差出人名</label></span>
      <div>
        <input type="text" ng-model="setItem.fromName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em"><label>メール本文タイプ</label></span>
      <div>
        <label ng-repeat="(key, item) in sendMailTypeList" class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_mail_type" value="{{key}}" ng-model="setItem.mailType" ng-init="setItem.mailType = setItem.mailType" default="setItem.default.mailType">{{item}}</label>
        <resize-textarea ng-if="setItem.mailType == <?= C_SCENARIO_MAIL_TYPE_CUSTOMIZE ?>" ng-model="setItem.template" cols="48" rows="4" placeholder="メール本文を入力してください"></resize-textarea>
      </div>
    </li>
  </ul>
</div>
