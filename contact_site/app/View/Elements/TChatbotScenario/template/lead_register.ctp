<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_LEAD_REGISTER ?>" class="set_action_item_body action_lead_register" ng-init="main.controllLeadRegister(setActionId)">
  <?=$this->Form->input('leadList', ['type' => 'hidden','value' => json_encode($this->data['leadList1'])])?>
  <ul>
    <li class="styleFlexbox">
      <div class="fb15em"><label class="require">種別</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="新規でリードリストを作成するか、既に作成済みのリードリストから選択するかを選ぶことできます。">?</icon></span></div>
      <div class="conditionTypeSelect" style="flex-grow: 0; margin-right: 3px;">
      <span>
        <label ng-repeat="(key, item) in makeLeadTypeList" class="pointer"><input type="radio" style="outline:0 ;" ng-value="key" ng-model="setItem.makeLeadTypeList" ng-value="key" ng-change="main.controllLeadRegister(setActionId)">{{item}}</label>
      </span>
      </div>
    </li>
    <li class="styleFlexbox">
      <div class="fb15em"><label class="require">リードリスト名</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="リードリスト名を設定します。">?</icon></span></div>
      <div ng-if="setItem.makeLeadTypeList == <?= C_SCENARIO_LEAD_REGIST ?>">
        <input type="text" ng-model="setItem.leadTitleLabel">
      </div>
      <select ng-if="setItem.makeLeadTypeList == <?= C_SCENARIO_LEAD_USE ?>" style="padding: 0px; flex-grow: 0;" ng-model="setItem.tLeadListSettingId" ng-options="item.id as item.name for item in main.leadList" ng-change="main.handleLeadInfo(setItem.tLeadListSettingId, setActionId)">
        <option value="">リストを選択してください</option>
      </select>
    </li>
    <!--新規作成の場合-->
    <li class="styleFlexbox" ng-if="setItem.makeLeadTypeList == <?= C_SCENARIO_LEAD_REGIST ?>">
      <div class="fb15em" style="white-space: nowrap; padding-top: 5px;"><label class="require">登録内容</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="リードリストに登録する項目に対して、それぞれ変数を設定できます">?</icon></span></div>
      <div>
        <div class="grid-container-header">
          <div class='area-name' style="text-align: center;"><label class="require">リードリストの項目</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名で指定された変数の値が、ここで指定されたリードリストの項目に自動で登録されます。">?</icon></span></div>
          <div class='area-name' style="text-align: center;"><label class="require">参照する変数名</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="リード情報として自動登録したい変数名を設定します。<br>（変数名を{{showExpression('変数名')}}と{で括る必要はありません）">?</icon></span></div>
        </div>
        <div class="grid-container-body itemListGroup" ng-repeat="(listId, item) in setItem.leadInformations track by $index">
          <div class='area-name'><input type="text" class="make-box" ng-model="item.leadLabelName"></div>
          <div class='area-name'><input type="text" class="make-box" ng-model="item.leadVariableName"></div>
          <div class='area-btn short'>
            <div class="btnBlock">
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
            </div>
          </div>
        </div>
      </div>
    </li>
    <!--既存リスト使用の場合-->
    <li class="styleFlexbox" ng-if="setItem.makeLeadTypeList == <?= C_SCENARIO_LEAD_USE ?>" >
      <div class="fb15em" style="white-space: nowrap; padding-top: 5px;"><label class="require">登録内容</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="リードリストに登録する項目に対して、それぞれ変数を設定できます">?</icon></span></div>
      <div>
        <div class="grid-container-header">
          <div class='area-name' style="text-align: center;"><label class="require">リードリストの項目</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名で指定された変数の値が、ここで指定されたリードリストの項目に自動で登録されます。">?</icon></span></div>
          <div class='area-name' style="text-align: center;"><label class="require">参照する変数名</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="リード情報として自動登録したい変数名を設定します。<br>（変数名を{{showExpression('変数名')}}と{で括る必要はありません）">?</icon></span></div>
        </div>
        <div class="grid-container-body itemListGroup" ng-repeat="(listId, item) in setItem.leadInformations track by $index">
          <div class='area-name'><input type="text" class="make-box" disabled ng-model="item.leadLabelName"></div>
          <div class='area-name'><input type="text" class="make-box" ng-model="item.leadVariableName"></div>
        </div>
      </div>
    </li>
  </ul>
</div>