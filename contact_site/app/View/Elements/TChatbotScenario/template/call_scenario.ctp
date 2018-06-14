<?php /* シナリオ呼び出し */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?>" class="set_action_item_body action_call_scenario">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label>シナリオ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したいシナリオを設定し、アクションの途中で登録済みのシナリオを実行することができます。" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <select ng-model="setItem.scenarioId" ng-init="setItem.scenarioId" ng-options="item.id as item.name for item in main.scenarioList">
          <option value="">シナリオを選択してください</option>
        </select>
      </div>
    </li>
    <li class="styleFlexbox">
      <label class="pointer"><input type="checkbox" ng-model="setItem.executeNextAction" ng-init="setItem.executeNextAction = setItem.executeNextAction == 1">終了後、このシナリオに戻る<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したシナリオの終了後、このアクションの続きを実行するか設定できます。" data-tooltip-width='300'>?</icon></span></label>
    </li>
  </ul>
</div>