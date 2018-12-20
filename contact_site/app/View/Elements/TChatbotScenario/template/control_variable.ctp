<?php /* 計算・変数操作 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_CONTROL_VARIABLE ?>" class="set_action_item_body action_control_variable"
     ng-init="main.controllHearingSettingView(setActionId)">
  <ul>
    <li>
      <div class='grid-container-control-variable grid-container-header'>
        <div class='area-name'>変数名<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip="変数名を設定します。<br>ここで設定した変数名に計算結果が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}<br>と指定することで利用することが可能です。">?</icon></span>
        </div>
        <div class='area-type'>計算タイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip="計算を行う形式を指定します。<br>＜タイプ＞<br>数値　：四則演算（+,-,*,/,()）や任意の数値を変数に代入することが可能です。<br>文字列：&を用いた文字列結合や任意の文字列を変数に代入することが可能です。"
                                                                      data-tooltip-width="30em">?</icon></span>
        </div>
        <div class='area-message'>計算式<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip='計算式または代入したい値や文字列を入力します。<br>計算式の結果が変数に代入されます。<br>＜利用できる記号＞<br>計算タイプ=数値の場合　："+"（足す）,"-"（引く）,"*"（掛ける）,<br>"/"（割る）,"()"（カッコ）<br>計算タイプ=文字列の場合："&"'>?</icon></span>
        </div>
        <div class='area-message'>有効桁数<span class="questionBalloon"><icon class="questionBtn"
                                                     data-tooltip="変数名を設定します。<br>ここで設定した変数名に計算結果が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}<br>と指定することで利用することが可能です。">?</icon></span>
        </div>
        <div class='area-message'>端数処理<span class="questionBalloon"><icon class="questionBtn"
                                                    data-tooltip="変数名を設定します。<br>ここで設定した変数名に計算結果が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}<br>と指定することで利用することが可能です。">?</icon></span>
        </div>


      </div>
      <ul class="input-one-row hearing-input-type"
          ng-model="setItem.calcRules">
        <li class='grid-container-control-variable grid-container-body itemListGroup'
            ng-repeat="(formulaId, item) in setItem.calcRules track by $index">
          <div class='area-name'><input type="text" ng-model="item.variableName"></div>
          <div class='area-type'>
            <select name="hearing-input-option" ng-model="item.calcType">
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
          <div ng-if="item.calcType == 1">
            <select id="digitList" ng-model="item.significantDigits">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
              <option value="17">17</option>
              <option value="18">18</option>
              <option value="19">19</option>
              <option value="20">20</option>
              <option value="21">21</option>
              <option value="22">22</option>
              <option value="23">23</option>
              <option value="24">24</option>
              <option value="25">25</option>
              <option value="26">26</option>
              <option value="27">27</option>
              <option value="28">28</option>
              <option value="29">29</option>
              <option value="30">30</option>
            </select>
          </div>
          <div ng-if="item.calcType == 1">
            <select ng-model="item.rulesForRounding">
              <option value="1">四捨五入</option>
              <option value="2">切り捨て</option>
              <option value="3">切り上げ</option>
            </select>
          </div>
          <div class='area-btn btnBlock'>
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn hearingBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, formulaId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn hearingBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, formulaId)')) ?></a>
          </div>
          <hr ng-if="!$last" class="separator">
      </li>
    </li>
  </ul>
</div>





