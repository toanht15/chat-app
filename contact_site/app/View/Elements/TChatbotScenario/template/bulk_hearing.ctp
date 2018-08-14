<?php /* メール送信 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_BULK_HEARING ?>" class="set_action_item_body action_send_mail" ng-init="main.controllBulkHearings(setActionId)">
  <ul>
    <li class="styleFlexbox direction-column itemListGroup" ng-repeat="(listId, condition) in setItem.multipleHearing track by $index">
      <input type="hidden" ng-model="condition.variableName"/>
      <input type="hidden" ng-model="condition.inputType"/>
      <input type="hidden" ng-model="condition.label"/>
      <input type="hidden" ng-model="condition.required"/>
    </li>
  </ul>
</div>