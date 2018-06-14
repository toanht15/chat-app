<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_TEXT ?>" class="set_action_item_body action_text">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label>発言内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットに発言させたいテキストメッセージを設定します。">?</icon></span></label></span>
      <div>
        <resize-textarea name="message" ng-model="setItem.message" cols="48" rows="1" placeholder="メッセージを入力してください" ng-required="true" data-maxRow="10"></resize-textarea>
      </div>
    </li>
  </ul>
</div>