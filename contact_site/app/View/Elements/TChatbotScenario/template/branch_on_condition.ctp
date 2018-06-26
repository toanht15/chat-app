<?php /* 条件分岐 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>" class="set_action_item_body action_branch_on_condition" ng-init="main.controllBranchOnConditionSettingView(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb15em"><label class="require">参照する変数名</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="条件の判定に利用する変数名を設定します。<br>（変数名を{{showExpression('変数名')}}と{で括る必要はありません）">?</icon></span></span>
      <div>
        <input type="text" ng-model="setItem.referenceVariable">
      </div>
    </li>
    <li class="styleFlexbox direction-column itemListGroup" ng-repeat="(listId, condition) in setItem.conditionList track by $index">
      <div>
        <h5 class="condition-separator">
          <span class="removeArea"><i class="remove deleteBtn" ng-click="main.removeActionItemList($event, listId)"></i></span>
          <span class="labelArea">条件{{$index+1}}</span>
        </h5>
      </div>
      <ul class="condition">
        <li class="styleFlexbox">
          <span class="fb15em indentDown"><label class="require">変数の値が</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="「参照する変数名」に指定した変数の値を設定します。複数の値を設定する場合はスペースで区切ってください。">?</icon></span></span>
          <input type="text" ng-model="condition.matchValue">
          <select class="m10r10l" ng-model="condition.matchValueType" ng-init="condition.matchValueType = condition.matchValueType.toString()" ng-options="index as type.label for (index, type) in matchValueTypeList"></select>
        </li>
        <li class="styleFlexbox m10b">
          <span class="fb15em indentDown"><label></label></span>
          <div class="styleFlexbox direction-column">
            <s>※複数の値を設定する場合はスペースで区切ってください。</s>
            <s>※スペース（空白）を含む値を設定する場合はキーワード全体を半角のダブルクォーテーション（"）で囲んでください。</s>
            <s>（例："山田 花子"）</s>
          </div>
        </li>
        <li class="styleFlexbox">
          <div class="fb15em indentDown"><label class="require">実行するアクション</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="条件を満たした場合に「テキスト発言」「シナリオ呼出」「シナリオを終了」「次のアクションへ」のいずれかの処理を行うことができます。">?</icon></span></div>
          <div class="conditionTypeSelect">
            <select class="m10r" ng-model="condition.actionType" ng-init="condition.actionType = condition.actionType.toString()" ng-options="index as type.label for (index, type) in processActionTypeList"></select>
          </div>
          <div class="conditionAction" ng-if="condition.actionType == 1">
            <resize-textarea ng-model="condition.action.message" placeholder="メッセージを入力してください"></resize-textarea>
          </div>
          <div class="conditionAction" ng-if="condition.actionType == 2">
            <select ng-model="condition.action.callScenarioId" ng-init="condition.action.callScenarioId" ng-options="item.id as item.name for item in main.scenarioListForBranchOnCond">
              <option value="">シナリオを選択してください</option>
              <option value="self">このシナリオ</option>
            </select>
            <label class="executeNextActionCheck pointer"><input type="checkbox" ng-model="condition.action.executeNextAction" ng-init="condition.action.executeNextAction = condition.action.executeNextAction == 1">終了後、このシナリオに戻る<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したシナリオの終了後、このアクションの続きを実行するか設定できます。">?</icon></span></label>
          </div>
          <div class="conditionAction" ng-if="condition.actionType == 3 || condition.actionType == 4"></div>
        </li>
      </ul>
      <div class='area-btn'>
        <hr class="separator pb6p" ng-if="$last && listId != '4'"/>
        <div class="btnBlock">
          <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a>
        </div>
      </div>
    </li>
    <li>
      <div>
        <hr class="separator"/>
        <label class="fb13em pointer p05tb"><input type="checkbox" ng-model="setItem.elseEnabled" ng-init="setItem.elseEnabled == true">上記を満たさない場合に実行するアクション<span class="questionBalloon"><icon class="questionBtn" data-tooltip="設定した条件のいずれにも該当しない場合に実行するアクションを設定します。（本設定を行わない場合、設定された条件に該当しない場合は次のアクションに進みます。）">?</icon></span></label>
        <ul class="condition else" ng-if="setItem.elseEnabled == true">
          <li class="styleFlexbox">
            <div class="conditionTypeSelect">
              <select class="m10r" ng-model="setItem.elseAction.actionType" ng-init="setItem.elseAction.actionType = setItem.elseAction.actionType.toString()" ng-options="index as type.label for (index, type) in processElseActionTypeList"></select>
            </div>
            <div class="conditionAction elseCondition" ng-if="setItem.elseAction.actionType == 1">
              <resize-textarea ng-model="setItem.elseAction.action.message" placeholder="メッセージを入力してください"></resize-textarea>
            </div>
            <div class="conditionAction elseCondition" ng-if="setItem.elseAction.actionType == 2">
              <select ng-model="setItem.elseAction.action.callScenarioId" ng-if="setItem.elseAction.actionType == 2" ng-init="setItem.elseAction.action.callScenarioId" ng-options="item.id as item.name for item in main.scenarioList">
                <option value="">シナリオを選択してください</option>
                <option value="self">このシナリオ</option>
              </select>
              <label class="executeNextActionCheck pointer"><input type="checkbox" ng-model="setItem.elseAction.action.executeNextAction" ng-init="setItem.elseAction.action.executeNextAction = setItem.elseAction.action.executeNextAction == 1">終了後、このシナリオに戻る<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したシナリオの終了後、このアクションの続きを実行するか設定できます。">?</icon></span></label>
            </div>
            <div class="conditionAction elseCondition" ng-if="condition.actionType == 3 || condition.actionType == 4"></div>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</div>