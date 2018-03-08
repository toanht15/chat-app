<?php /* テキスト発言 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_TEXT ?>" class="set_action_item_body action_text">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label>発言内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットに発言させたいテキストメッセージを設定します。">?</icon></span></label></span>
      <div>
        <resize-textarea name="message" ng-model="setItem.message" cols="48" rows="4" placeholder="メッセージを入力してください" ng-required="true"></resize-textarea>
      </div>
    </li>
  </ul>
</div>

<?php /* ヒアリング */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_HEARING ?>" class="set_action_item_body action_hearing" ng-init="main.controllHearingSettingView(setActionId)">
  <ul>
    <li>
      <table cellspacing="5">
        <thead>
          <tr>
            <th class="item_name">変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。ここで設定した変数名にサイト訪問者の回答内容が保存されます。変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{&thinsp;{名前}&thinsp;}様からのお問い合わせを受付いたしました。">?</icon></span></th>
            <th class="item_type">タイプ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者が入力した回答が適切か、整合性チェックを行うことができます。入力内容が不適切だった場合（整合性チェックNGだった場合）は、「入力エラー時の返信メッセージ」に設定されたメッセージを自動送信後、再度ヒアリングを実施します。<br>＜タイプ＞<br>@text　　　　：制限なし<br>@number　　：数字のみ<br>@email　　　：メールアドレス形式のみ<br>@tel_number：数字とハイフンのみ">?</icon></span></th>
            <th class="item_message">質問内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。">?</icon></span></th>
            <th class="item_btn_block"></th>
          </tr>
        </thead>
        <tbody class="itemListGroup">
          <tr ng-repeat-start="(listId, hearingItem) in setItem.hearings track by $index">
            <td class="item_name"><input type="text" ng-model="hearingItem.variableName"></td>
            <td class="item_type">
              <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType.toString()" ng-options="index as type.label for (index, type) in inputTypeList"></select>
            </td>
            <td class="item_message" rowspan="2"><textarea ng-model="hearingItem.message" rows="5"></textarea></td>
          </tr>
          <tr ng-repeat-end>
            <td class="item_detail_settings" colspan="2">
              <span>
                <label ng-repeat="(key, item) in inputLFTypeList" class="pointer"><input type="radio" ng-model="hearingItem.inputLFType" ng-value="key">{{item.label}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="{{item.tooltip}}">?</icon></span></label>
              </span>
              <span ng-repeat="(key, item) in inputLFTypeList" ng-if="hearingItem.inputLFType == key" class="pointer">
                <label ng-repeat="(detailKey, detailItem) in item.detail"><input type="radio" ng-model="hearingItem.sendMessageType" ng-value="detailKey">{{detailItem.label}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="{{detailItem.tooltip}}">?</icon></span></label>
              </span>
            </td>
            <td class="item_btn_block">
              <div class="btnBlock">
                <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em"><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者の発言内容がタイプに当てはまらなかった場合（整合性チェックエラーの場合）に自動返信するメッセージを設定します。">?</icon></span></span>
      <div>
        <resize-textarea name="errorMessage" ng-model="setItem.errorMessage" cols="48" rows="4" placeholder="入力エラー時の返信メッセージを入力してください"></resize-textarea>
      </div>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.isConfirm" ng-init="setItem.isConfirm = setItem.isConfirm == 1">入力内容の確認を行う<span class="questionBalloon"><icon class="questionBtn" data-tooltip="質問内容を全て聞き終えた後に、サイト訪問者に確認メッセージを送ることが出来ます。">?</icon></span></label>
      <ul ng-if="setItem.isConfirm == true" class="indentDown">
        <li class="styleFlexbox">
          <span class="fb9em"><label>確認内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="確認メッセージとして送信するメッセージを設定します。<br><br>＜設定例＞<br>お名前　　　　：{&thinsp;{名前}&thinsp;}<br>電話番号　　　：{&thinsp;{電話番号}&thinsp;}<br>メールアドレス：{&thinsp;{メールアドレス}&thinsp;}<br>でよろしいでしょうか？">?</icon></span></label></span>
          <div>
            <resize-textarea name="confirmMessage" ng-model="setItem.confirmMessage" cols="48" rows="4" placeholder="確認内容のメッセージを入力してください"></resize-textarea>
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
      <label class="pointer"><input type="checkbox" ng-model="setItem.cv" ng-init="setItem.cv = setItem.cv == 1">成果にCVとして登録する<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャット履歴の「成果」に「途中離脱」または「CV」として自動登録します。<br>【途中離脱】ヒアリング途中で終了した場合<br>【CV】全項目のヒアリングが完了した場合（入力内容の確認を行う場合は「OK」が選択された場合）">?</icon></span></label>
    </li>
  </ul>
</div>

<?php /* 選択肢 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>" class="set_action_item_body action_select_option" ng-init="main.controllSelectOptionSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label class="hearingSelectVariableNameLabel">変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。ここで設定した変数名にサイト訪問者の選択した内容が保存されます。変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。">?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.selection.variableName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb7em"><label>質問内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お客様の性別を選択して下さい。">?</icon></span></label></span>
      <div>
        <resize-textarea name="message" ng-model="setItem.message" cols="48" rows="4" placeholder="質問内容のメッセージを入力してください"></resize-textarea>
      </div>
    </li>
    <li>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, optionItem) in setItem.selection.options track by $index" class="styleFlexbox" ng-init="options = setItem.selection.options">
          <span class="fb7em"><label>選択肢 {{listId+1}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="回答の選択肢を設定します。<br><br>例）選択肢１：男性<br>　　選択肢２：女性">?</icon></span></label></span>
          <div>
            <input type="text" ng-model="setItem.selection.options[listId]">
            <div class="btnBlock">
              <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
            </div>
          </div>
        </li>
      </ul>
    </li>
  </ul>
</div>

<?php /* メール送信 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_SEND_MAIL ?>" class="set_action_item_body action_send_mail" ng-init="main.controllMailSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb13em"><label>送信先メールアドレス<span class="questionBalloon"><icon class="questionBtn" data-tooltip="送信先のメールアドレスを設定します。（変数の利用も可能です）">?</icon></span></label></span>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, addressItem) in setItem.toAddress track by $index">
          <input type="text" ng-model="setItem.toAddress[listId]" ng-init="setItem.toAddress[listId] = setItem.toAddress[listId]" default="">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
          </div>
        </li>
      </ul>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>メールタイトル<span class="questionBalloon"><icon class="questionBtn" data-tooltip="メールタイトルを設定します。（変数の利用も可能です）">?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.subject">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>差出人名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="差出人名を設定します。（変数の利用も可能です）">?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.fromName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>メール本文タイプ</label></span>
      <div>
        <label ng-repeat="(key, item) in sendMailTypeList" class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_mail_type" value="{{key}}" ng-model="setItem.mailType" ng-init="setItem.mailType = setItem.mailType" default="setItem.default.mailType">{{item.label}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="{{item.tooltip}}">?</icon></span></label>
        <resize-textarea ng-if="setItem.mailType == <?= C_SCENARIO_MAIL_TYPE_CUSTOMIZE ?>" ng-model="setItem.template" cols="48" rows="4" placeholder="メール本文を入力してください"></resize-textarea>
      </div>
    </li>
  </ul>
</div>

<?php /* シナリオ呼び出し */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?>" class="set_action_item_body action_call_scenario">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label>シナリオ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したいシナリオを設定し、アクションの途中で登録済みのシナリオを実行することができます。">?</icon></span></label></span>
      <div>
        <select ng-model="setItem.scenarioId" ng-init="setItem.scenarioId" ng-options="item.key as item.name for item in main.scenarioList">
          <option value="">シナリオを選択してください</option>
        </select>
      </div>
    </li>
  </ul>
</div>

<?php /* 外部システム連携 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>" class="set_action_item_body action_external_api_connection" ng-init="main.controllExternalApiSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb13em"><label>連携先URL<span class="questionBalloon"><icon class="questionBtn" data-tooltip="HTTP(S)で通信するリクエスト対象の外部連携先のURLです。（変数の利用も可能です）">?</icon></span></label></span>
      <div>
        <input ng-model="setItem.url" type="text" placeholder="外部連携先のURLを入力してください">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>メソッド種別<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先と通信する際のリクエスト方法の種類です。<br>＜メソッド種別＞<br>GET ：送信したいデータをURLに付加して、サーバーへ送ります。<br>POST：送信したいデータをリクエストヘッダーとリクエストボディに設定して、サーバーへ送ります。">?</icon></span></label></span>
      <span>
        <label ng-repeat="(key, item) in apiMethodType" class="pointer"><input type="radio" ng-model="setItem.methodType" ng-value="key">{{item}}</label>
      </span>
    </li>
    <li class="styleFlexbox" ng-if="setItem.methodType == <?= C_SCENARIO_METHOD_TYPE_POST ?>">
      <span class="fb13em"><label>リクエストヘッダー<span class="questionBalloon"><icon class="questionBtn" data-tooltip="リクエストについての情報や属性を設定できます。外部連携先のサーバーにリクエストを送る際、リクエストヘッダーとして送信されます。">?</icon></span></label></span>
      <div>
        <table cellspacing="5">
          <thead>
            <tr>
              <th class="item_name">名前<span class="questionBalloon"><icon class="questionBtn" data-tooltip="リクエストヘッダーのフィールド名を設定します。">?</icon></span></th>
              <th class="item_value">値<span class="questionBalloon"><icon class="questionBtn" data-tooltip="リクエストヘッダーの内容を設定します。">?</icon></span></th>
              <th class="item_btn_block"></th>
            </tr>
          </thead>
          <tbody class="externalApiRequestHeader itemListGroup">
            <tr ng-repeat="(listId, headerItem) in setItem.requestHeaders">
              <td class="item_name"><input ng-model="headerItem.name" type="text" class="frame"></td>
              <td class="item_value"><input ng-model="headerItem.value" type="text" class="frame"></td>
              <td class="item_btn_block">
                <div class="btnBlock">
                  <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </li>
    <li class="styleFlexbox" ng-if="setItem.methodType == <?= C_SCENARIO_METHOD_TYPE_POST ?>">
      <span class="fb13em"><label>リクエストボディ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーにリクエストを送る際、リクエストボディとして送信されます。">?</icon></span></label></span>
      <div>
        <resize-textarea ng-model="setItem.requestBody" cols="48" rows="4" placeholder="リクエストボディを設定してください"></resize-textarea>
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>レスポンスタイプ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーから受け取るデータの形式です。">?</icon></span></label></span>
      <span>
        <label ng-repeat="(key, item) in apiResponseType" class="pointer"><input type="radio" ng-model="setItem.responseType" ng-value="key">{{item}}</label>
      </span>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>レスポンスボディ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーから受け取ったデータを、シナリオで利用できるように変数に保存します。">?</icon></span></label></span>
      <div>
        <table cellspacing="5">
          <thead>
            <tr>
              <th class="item_name">変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。ここで設定した変数名に受け取ったデータの内容が保存されます。変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。">?</icon></span></th>
              <th class="item_value">変換元キー名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="受け取ったデータから、キー名を設定して変数に保存するデータを指定できます。">?</icon></span></th>
              <th class="item_btn_block"></th>
            </tr>
          </thead>
          <tbody class="externalApiResponseBody itemListGroup">
            <tr ng-repeat="(listId, bodyItem) in setItem.responseBodyMaps">
              <td class="item_name"><input ng-model="bodyItem.variableName" type="text" class="frame"></td>
              <td class="item_value"><input ng-model="bodyItem.sourceKey" type="text" class="frame"></td>
              <td class="item_btn_block">
                <div class="btnBlock">
                  <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </li>
  </ul>
</div>
