<?php /* 計算・変数操作 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_CONTROL_VARIABLE ?>" class="set_action_item_body action_control_variable"
     ng-init="main.controllHearingSettingView(setActionId)">
  <ul>
    <li>
      <div class='grid-container-control-variable grid-container-header'>
        <div class='area-name'>変数名<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の回答内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{{showExpression('名前')}}様からのお問い合わせを受付いたしました。">?</icon></span>
        </div>
        <div class='area-type'>計算タイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip="計算を行う形式を指定します。<br>＜タイプ＞<br>数値　 　 ：四則演算による数字の計算が可能です。<br>文字列　 ：&を用いた文字列同士の結合が可能です。"
                                                                      data-tooltip-width="30em">?</icon></span>
        </div>
        <div class='area-message'>計算式<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip="計算式を入力します。計算式の結果が変数に代入されます。<br>※演算記号は半角で入力して下さい。">?</icon></span>
        </div>

      </div>
      <ul class="input-one-row hearing-input-type"
          ng-model="setItem.calcRules">
        <li class='grid-container-control-variable grid-container-body itemListGroup'
            ng-repeat="(formulaId, item) in setItem.calcRules track by $index">
          <div class='area-name'><input type="text" ng-model="item.variableName"></div>
          <div class='area-type'>
            <select name="hearing-input-option" ng-model="item.calcType"
                    ng-change="">
              <option value="1">数値</option>
              <option value="2">文字列</option>
            </select>
          </div>
          <div class='area-message'>
            <resize-textarea maxlength="4000" ng-model="item.formula" rows="1"
            data-maxRow="10"></resize-textarea>
            <s ng-if="item.calcType == 1">※ “+“（足す）,“-“（引く）,“*“（掛ける）,“/“（割る）,“()“（カッコ）を利用した四則演算が可能です。</s>
            <s ng-if="item.calcType == 2">※ “&“を用いた文字列の結合や文字列の代入が可能です。</s>
          </div>
          <div class='area-btn btnBlock'>
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn hearingBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, formulaId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn hearingBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, formulaId)')) ?></a>
          </div>
          <hr ng-if="!$last" class="separator">
      </li>
    </li>
  </ul>
</div>





