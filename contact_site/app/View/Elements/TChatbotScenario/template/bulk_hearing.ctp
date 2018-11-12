<?php /* メール送信 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_BULK_HEARING ?>" class="set_action_item_body action_bulk_hearing" ng-init="main.controllBulkHearings(setActionId)">
  <ul>
    <div class='grid-container-bulk-hearing grid-container-header'>
      <div class='area-require'>必須<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip="必須項目とする場合はチェックを付けます。（スキップ可能とする場合はチェックを外します）">?</icon></span>
      </div>
      <div class='area-name'>属性<span class="questionBalloon"><icon class="questionBtn"
                                                                    data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の回答内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{{showExpression('名前')}}様からのお問い合わせを受付いたしました。">?</icon></span>
      </div>
      <div class='area-type'>ラベル名<span class="questionBalloon"><icon class="questionBtn"
                                                                    data-tooltip="ヒアリングの回答を入力する形式を指定します。<br>＜タイプ＞<br>テキスト(1行)　 　 ：フリーテキスト入力（改行不可）<br>テキスト(複数行)　 ：フリーテキスト入力（改行可）<br>ラジオボタン　　　：ラジオボタン形式の択一選択<br>プルダウン　　　　：プルダウン形式の択一選択<br>カレンダー　　　　：カレンダーから日付を選択"
                                                                    data-tooltip-width="30em">?</icon></span>
      </div>
      <div class='area-variable'>変数名<span class="questionBalloon"><icon class="questionBtn"
                                                                        data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。">?</icon></span>
      </div>

    </div>
    <li class="direction-column itemListGroup grid-container-bulk-hearing grid-container-body" ng-repeat="(listId, condition) in setItem.multipleHearings track by $index">
      <div class="area-require">
        <label class="require-checkbox">
          <input type="checkbox" ng-model="condition.required">
          <span class="checkmark"></span>
        </label>
      </div>
      <div class='area-type'>
        <select name="hearing-input-option" ng-model="condition.inputType">
          <option value="1">会社名</option>
          <option value="2">名前</option>
          <option value="3">郵便番号</option>
          <option value="4">住所</option>
          <option value="5">部署名</option>
          <option value="6">役職</option>
          <option value="7">電話番号</option>
          <option value="8">FAX番号</option>
          <option value="9">携帯番号</option>
          <option value="10">メールアドレス</option>
        </select>
      </div>
      <div class='area-name'><input type="text" ng-model="condition.label"></div>
      <div class='area-variable'><input type="text" ng-model="condition.variableName"></div>
      <div class='area-btn'>
        <div class="btnBlock">
          <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn hearingBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn hearingBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
        </div>
      </div>
      <hr ng-if="!$last" class="separator">
    </li>
  </ul>
</div>
