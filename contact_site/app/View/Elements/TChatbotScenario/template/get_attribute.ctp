<?php /* 属性値取得 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?>" class="set_action_item_body action_hearing" ng-init="main.controllAttributeSettingView(setActionId)">
  <ul>
    <li>
      <div class='grid-container grid-container-header'>
        <div class='area-name'>変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名に取得したデータの内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。" data-tooltip-width='300'>?</icon></span></div>
        <div class='area-selector'>CSSセレクタ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="{{showCSSSelectorTooltip()}}" data-tooltip-width='450'>?</icon></span></div>
      </div>
      <div  class='styleFlexbox' ng-repeat="(listId, getAttributes) in setItem.getAttributes track by $index">
        <div class='grid-container grid-container-body itemListGroup'>
          <div class='area-name'><input type="text" ng-model="getAttributes.variableName"></div>
          <input type="hidden" ng-model="getAttributes.type" ng-init="getAttributes.type=3" value="3"/>
          <div class='area-selector'><input type = "text" ng-model="getAttributes.attributeValue" rows="1" data-maxRow="10" class = "textarea-message"></input></div>
          <div class='area-btn'>
            <div class="btnBlock">
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
            </div>
          </div>
          <hr class="separator" ng-if="!$last"/>
        </div>
      </div>
    </li>
  </ul>
</div>