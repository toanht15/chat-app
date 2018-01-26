<?php /* テキスト発言 | C_SCENARIO_ACTION_TEXT */ ?>
<div ng-if="setItem.actionType == 1" class="set_action_item_body action_text">
  <ul>
    <li class="styleFlexbox">
      <span><label>発言内容</label></span>
      <div>
        <textarea name="message" ng-model="setItem.message" cols="48" rows="4" placeholder="メッセージを入力してください" required></textarea>
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
            <th class="hearingVariableNameLabel">変数名<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></th>
            <th class="hearingVariableTypeLabel">タイプ<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></th>
            <th class="hearingVariableQuestionLabel">質問内容</th>
            <th class="hearginVariableBtnGroupLabel"></th>
          </tr>
        </thead>
        <tbody class="itemListGroup">
          <tr ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
            <td><input type="text" ng-model="hearingItem.variableName" class="frame"></td>
            <td>
              <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType" class="frame">
                <option value="1">@text</option>
                <option value="2">@number</option>
                <option value="3">@email</option>
                <option value="4">@tel_number</option>
              </select>
            </td>
            <td class="message"><input type="text" ng-model="hearingItem.message" class="frame"></td>
            <td class="btnBlock">
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList(setActionId, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList(setActionId, listId)')) ?></a>
            </td>
          </tr>
        </tbody>
      </table>
    </li>
    <li class="styleFlexbox">
      <span><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></label></span>
      <div>
        <textarea name="errorMessage" ng-model="setItem.errorMessage" cols="48" rows="4" placeholder="入力エラー時の返信メッセージを入力してください"></textarea>
      </div>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.isConfirm" ng-init="setItem.isConfirm = setItem.isConfirm == 1">入力内容の確認を行う</label>
      <ul ng-if="setItem.isConfirm == true" class="indentDown">
        <li class="styleFlexbox">
          <span><label>確認内容</label></span>
          <div>
            <textarea name="confirmMessage" ng-model="setItem.confirmMessage" cols="48" rows="4" placeholder="確認内容のメッセージを入力してください"></textarea>
          </div>
        </li>
        <li class="styleFlexbox">
          <span><label>選択肢（OK）<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></label></span>
          <div>
            <input type="text" name="success" ng-model="setItem.success">
          </div>
        </li>
        <li class="styleFlexbox">
          <span><label>選択肢（NG）<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></label></span>
          <div>
            <input type="text" name="cancel" ng-model="setItem.cancel">
          </div>
        </li>
      </ul>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.cv" ng-init="setItem.cv = setItem.cv == 1">成果にCVとして登録する</label>
      <div ng-if="setItem.cv == true" class="indentDown">
        <label class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_cv_condition" value="1" ng-model="setItem.cvCondition">一部の項目でも正常に入力されたらCVとして登録する</label>
        <label class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_cv_condition" value="2" ng-model="setItem.cvCondition">すべての項目が正常に入力された場合のみCVとして登録する</label>
        <label class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_cv_condition" value="3" ng-model="setItem.cvCondition" ng-disabled="!setItem.isConfirm">入力確認にて選択肢（OK）が選択された場合のみCVとして登録する</label>
      </div>
    </li>
  </ul>
</div>

<?php /* 選択肢 | C_SCENARIO_ACTION_SELECT_OPTION */ ?>
<div ng-if="setItem.actionType == 3" class="set_action_item_body action_select_option" ng-init="main.controllSelectOptionSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span><label class="hearingSelectVariableNameLabel">変数名<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></label></span>
      <div>
        <input type="text" ng-model="setItem.selection.variableName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span><label>質問内容</label></span>
      <div>
        <textarea name="message" ng-model="setItem.message" cols="48" rows="4" placeholder="質問内容のメッセージを入力してください"></textarea>
      </div>
    </li>
    <li>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, optionItem) in setItem.selection.options track by $index" class="styleFlexbox" ng-init="options = setItem.selection.options">
          <span><label>選択肢 {{listId+1}}</label></span>
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
      <span><label>送信先メールアドレス</label></span>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, addressItem) in setItem.toAddress track by $index">
          <input type="text" ng-model="setItem.toAddress[listId]">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList(setActionId, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList(setActionId, listId)')) ?></a>
          </div>
        </li>
      </ul>
    </li>
    <li class="styleFlexbox">
      <span><label>メールタイトル</label></span>
      <div>
        <input type="text" ng-model="setItem.subject">
      </div>
    </li>
    <li class="styleFlexbox">
      <span><label>差出人名</label></span>
      <div>
        <input type="text" ng-model="setItem.fromName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span><label>メール本文タイプ</label></span>
      <div>
        <label class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_mail_type" value="1" ng-model="setItem.mailType" ng-init="setItem.mailType = setItem.default.mailType">メール内容をすべてメールする</label>
        <label class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_mail_type" value="2" ng-model="setItem.mailType" ng-init="setItem.mailType = setItem.default.mailType">変数の値のみメールする</label>
        <label class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_mail_type" value="3" ng-model="setItem.mailType" ng-init="setItem.mailType = setItem.default.mailType">メール本文をカスタマイズする</label>
        <textarea ng-if="setItem.mailType == 3" ng-model="setItem.template" cols="48" rows="4" placeholder="メール本文を入力してください"></textarea>
      </div>
    </li>
  </ul>
</div>
