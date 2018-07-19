<?php /* 選択肢 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>" class="set_action_item_body action_select_option" ng-init="main.controllSelectOptionSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label class="hearingSelectVariableNameLabel">変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の選択した内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。">?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.selection.variableName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb7em"><label>質問内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お客様の性別を選択して下さい。">?</icon></span></label></span>
      <div>
        <resize-textarea name="message" maxlength="4000" ng-model="setItem.message" cols="48" rows="1" placeholder="質問内容のメッセージを入力してください" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, optionItem) in setItem.selection.options track by $index" class="styleFlexbox" ng-init="options = setItem.selection.options">
          <span class="fb7em"><label>選択肢 {{listId+1}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="回答の選択肢を設定します。<br><br>例）選択肢１：男性<br>　　選択肢２：女性">?</icon></span></label></span>
          <div>
            <input type="text" ng-model="setItem.selection.options[listId]">
            <div class="btnBlock">
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
            </div>
          </div>
        </li>
      </ul>
    </li>
  </ul>
</div>