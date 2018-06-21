<?php /* ヒアリング */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_HEARING ?>" class="set_action_item_body action_hearing" ng-init="main.controllHearingSettingView(setActionId)">
  <ul>
    <li>
      <div class='grid-container grid-container-header'>
        <div class='area-name'>変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の回答内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{{showExpression('名前')}}様からのお問い合わせを受付いたしました。" data-tooltip-width='300'>?</icon></span></div>
        <div class='area-type'>タイプ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者が入力した回答が適切か、整合性チェックを行うことができます。<br>入力内容が不適切だった場合（整合性チェックNGだった場合）は、「入力エラー時の返信メッセージ」に設定されたメッセージを自動送信後、再度ヒアリングを実施します。<br><br>＜タイプ＞<br>@text　　　　：制限なし<br>@number　　：数字のみ<br>@email　　　：メールアドレス形式のみ<br>@tel_number：数字とハイフンのみ" data-tooltip-width='300'>?</icon></span></div>
        <div class='area-message'>質問内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。" data-tooltip-width='285'>?</icon></span></div>
      </div>
      <div class='grid-container grid-container-body itemListGroup' ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
        <div class='area-name'><input type="text" ng-model="hearingItem.variableName"></div>
        <div class='area-type'>
          <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType.toString()" ng-options="index as type.label for (index, type) in inputTypeList"></select>
        </div>
        <div class='area-message'><resize-textarea ng-model="hearingItem.message" rows="1" data-maxRow="10"></resize-textarea></div>
        <div class='area-btn'>
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
          </div>
        </div>
        <div class='area-detail'>
          <span>
            <label class="pointer"><input type="radio" ng-model="hearingItem.inputLFType" ng-value="<?= C_SCENARIO_INPUT_LF_TYPE_DISALLOW ?>">改行不可<span class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者の複数行入力を規制します。<br>（改行できなくする）" data-tooltip-width='220'>?</icon></span></label>
            <label class="pointer"><input type="radio" ng-model="hearingItem.inputLFType" ng-value="<?= C_SCENARIO_INPUT_LF_TYPE_ALLOW ?>">改行可<span class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者の複数行入力を許可します。<br>（改行を許可）" data-tooltip-width='220'>?</icon></span></label>
          </span>
        </div>
        <hr class="separator"/>
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em"><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者の発言内容がタイプに当てはまらなかった場合（整合性チェックエラーの場合）に自動返信するメッセージを設定します。" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <resize-textarea name="errorMessage" ng-model="setItem.errorMessage" cols="48" rows="1" placeholder="入力エラー時の返信メッセージを入力してください" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.isConfirm" ng-init="setItem.isConfirm = setItem.isConfirm == 1">入力内容の確認を行う<span class="questionBalloon"><icon class="questionBtn" data-tooltip="質問内容を全て聞き終えた後に、サイト訪問者に確認メッセージを送ることが出来ます。" data-tooltip-width='300'>?</icon></span></label>
      <ul ng-if="setItem.isConfirm == true" class="indentDown">
        <li class="styleFlexbox">
          <span class="fb9em"><label>確認内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="確認メッセージとして送信するメッセージを設定します。<br><br>＜設定例＞<br>お名前　　　　：{{showExpression('名前')}}<br>電話番号　　　：{{showExpression('電話番号')}}<br>メールアドレス：{{showExpression('メールアドレス')}}<br>でよろしいでしょうか？" data-tooltip-width='300'>?</icon></span></label></span>
          <div>
            <resize-textarea name="confirmMessage" ng-model="setItem.confirmMessage" cols="48" rows="1" placeholder="確認内容のメッセージを入力してください" data-maxRow="10"></resize-textarea>
          </div>
        </li>
        <li class="styleFlexbox">
          <span class="fb9em"><label>選択肢（OK）<span class="questionBalloon"><icon class="questionBtn" data-tooltip="OK（次のアクションを実行）の場合の選択肢の名称を設定します。">?</icon></span></label></span>
          <div>
            <input type="text" name="success" ng-model="setItem.success">
          </div>
        </li>
        <li class="styleFlexbox">
          <span class="fb9em"><label>選択肢（NG）<span class="questionBalloon"><icon class="questionBtn" data-tooltip="NG（再入力）の場合の選択肢の名称を設定します。">?</icon></span></label></span>
          <div>
            <input type="text" name="cancel" ng-model="setItem.cancel">
          </div>
        </li>
      </ul>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.cv" ng-init="setItem.cv = setItem.cv == 1">成果にCVとして登録する<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャット履歴の「成果」に「途中離脱」または「CV」として自動登録します。<br><br>【途中離脱】ヒアリング途中で終了した場合<br>【CV】全項目のヒアリングが完了した場合（入力内容の確認を行う場合は「OK」が選択された場合）" data-tooltip-width='300'>?</icon></span></label>
    </li>
  </ul>
</div>