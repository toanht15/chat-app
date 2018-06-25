<?php /* 訪問ユーザ登録 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION ?>" class="set_action_item_body action_hearing" ng-init="main.controllAddCustomerInformationView(setActionId)">
  <ul>
    <li>
      <div class='grid-container short grid-container-header'>
        <div class='area-name'><label class="require">変数名</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名を利用し、訪問ユーザ情報の項目に取得したデータの内容が保存されます。">?</icon></span></div>
        <div class='area-selector short'><label class="require">訪問ユーザ情報の項目</label><span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数を利用し、訪問ユーザ情報として登録したい項目を設定します。">?</icon></span></div>
      </div>
      <div  class='styleFlexbox' ng-repeat="(listId, addCustomerInformation) in setItem.addCustomerInformations track by $index">
        <div class='grid-container short grid-container-body itemListGroup'>
          <div class='area-name'><input type="text" ng-model="addCustomerInformation.variableName"></div>
          <div class='area-selector short'>
            <select ng-model="addCustomerInformation.targetId">
              <option value="">選択してください</option>
            <?php
              for($i = 0; $i < count($chatbotScenarioAddCustomerInformationList); $i++) {
                echo "<option value='".$chatbotScenarioAddCustomerInformationList[$i]['TCustomerInformationSetting']['id']."'>".$chatbotScenarioAddCustomerInformationList[$i]['TCustomerInformationSetting']['item_name']."</option>";
              }
            ?>
            </select>
          </div>
          <div class='area-btn short'>
            <div class="btnBlock">
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
            </div>
          </div>
          <hr class="separator short" ng-if="!$last"/>
        </div>
      </div>
    </li>
  </ul>
</div>