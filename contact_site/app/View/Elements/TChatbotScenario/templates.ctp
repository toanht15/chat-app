<?php /* テキスト発言 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_TEXT ?>" class="set_action_item_body action_text">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label>発言内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットに発言させたいテキストメッセージを設定します。">?</icon></span></label></span>
      <div>
        <resize-textarea name="message" ng-model="setItem.message" cols="48" rows="1" placeholder="メッセージを入力してください" ng-required="true" data-maxRow="10"></resize-textarea>
      </div>
    </li>
  </ul>
</div>

<?php /* ヒアリング */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_HEARING ?>" class="set_action_item_body action_hearing" ng-init="main.controllHearingSettingView(setActionId)">
  <ul>
    <li>
      <div class='grid-container grid-container-header'>
        <div class='area-name'>変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の回答内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{&thinsp;{名前}&thinsp;}様からのお問い合わせを受付いたしました。" data-tooltip-width='300'>?</icon></span></div>
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
          <span class="fb9em"><label>確認内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="確認メッセージとして送信するメッセージを設定します。<br><br>＜設定例＞<br>お名前　　　　：{&thinsp;{名前}&thinsp;}<br>電話番号　　　：{&thinsp;{電話番号}&thinsp;}<br>メールアドレス：{&thinsp;{メールアドレス}&thinsp;}<br>でよろしいでしょうか？" data-tooltip-width='300'>?</icon></span></label></span>
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

<?php /* 選択肢 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>" class="set_action_item_body action_select_option" ng-init="main.controllSelectOptionSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label class="hearingSelectVariableNameLabel">変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の選択した内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.selection.variableName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb7em"><label>質問内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お客様の性別を選択して下さい。" data-tooltip-width='290'>?</icon></span></label></span>
      <div>
        <resize-textarea name="message" ng-model="setItem.message" cols="48" rows="1" placeholder="質問内容のメッセージを入力してください" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li>
      <ul class="itemListGroup">
        <li ng-repeat="(listId, optionItem) in setItem.selection.options track by $index" class="styleFlexbox" ng-init="options = setItem.selection.options">
          <span class="fb7em"><label>選択肢 {{listId+1}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="回答の選択肢を設定します。<br><br>例）選択肢１：男性<br>　　選択肢２：女性" data-tooltip-width='155'>?</icon></span></label></span>
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
      <span class="fb13em"><label>送信先メールアドレス<span class="questionBalloon"><icon class="questionBtn" data-tooltip="送信先のメールアドレスを設定します。<br>（変数の利用も可能です）" data-tooltip-width='210'>?</icon></span></label></span>
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
      <span class="fb13em"><label>メールタイトル<span class="questionBalloon"><icon class="questionBtn" data-tooltip="メールタイトルを設定します。<br>（変数の利用も可能です）" data-tooltip-width='165'>?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.subject">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>差出人名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="差出人名を設定します。<br>（変数の利用も可能です）" data-tooltip-width='145'>?</icon></span></label></span>
      <div>
        <input type="text" ng-model="setItem.fromName">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>メール本文タイプ</label></span>
      <div>
        <label ng-repeat="(key, item) in sendMailTypeList" class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_mail_type" value="{{key}}" ng-model="setItem.mailType">{{item.label}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="{{item.tooltip}}" data-tooltip-width='240'>?</icon></span></label>
        <resize-textarea ng-if="setItem.mailType == <?= C_SCENARIO_MAIL_TYPE_CUSTOMIZE ?>" ng-model="setItem.template" cols="48" rows="1" placeholder="メール本文を入力してください" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <label class="fb13em pointer p05tb"><input type="checkbox" ng-model="setItem.sendWithDownloadURL" ng-init="setItem.sendWithDownloadURL">添付ファイルがある場合、ダウンロードURLをメール本文に記載する</label>
  </ul>
</div>

<?php /* シナリオ呼び出し */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_CALL_SCENARIO ?>" class="set_action_item_body action_call_scenario">
  <ul>
    <li class="styleFlexbox">
      <span class="fb7em"><label>シナリオ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したいシナリオを設定し、アクションの途中で登録済みのシナリオを実行することができます。" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <select ng-model="setItem.scenarioId" ng-init="setItem.scenarioId" ng-options="item.id as item.name for item in main.scenarioList">
          <option value="">シナリオを選択してください</option>
        </select>
      </div>
    </li>
    <li class="styleFlexbox">
      <label class="pointer"><input type="checkbox" ng-model="setItem.executeNextAction" ng-init="setItem.executeNextAction = setItem.executeNextAction == 1">終了後、このシナリオに戻る<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したシナリオの終了後、このアクションの続きを実行するか設定できます。" data-tooltip-width='300'>?</icon></span></label>
    </li>
  </ul>
</div>

<?php /* 属性値取得 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_GET_ATTRIBUTE ?>" class="set_action_item_body action_hearing" ng-init="main.controllAttributeSettingView(setActionId)">
  <ul>
    <li>
      <div class='grid-container grid-container-header'>
        <div class='area-name'>変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の回答内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{&thinsp;{名前}&thinsp;}様からのお問い合わせを受付いたしました。" data-tooltip-width='300'>?</icon></span></div>
        <div class='area-message'>CSSセレクタ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。" data-tooltip-width='285'>?</icon></span></div>
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


<?php /* 外部システム連携 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>" class="set_action_item_body action_external_api_connection" ng-init="main.controllExternalApiSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb13em"><label>連携先URL<span class="questionBalloon"><icon class="questionBtn" data-tooltip="HTTP(S)で通信するリクエスト対象の外部連携先のURLです。<br>（変数の利用も可能です）" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <input ng-model="setItem.url" type="text" placeholder="外部連携先のURLを入力してください">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>メソッド種別<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先と通信する際のリクエスト方法の種類です。<br><br>＜メソッド種別＞<br>GET ：送信したいデータをURLに付加して、サーバーへ送ります。<br>POST：送信したいデータをリクエストヘッダーとリクエストボディに設定して、サーバーへ送ります。" data-tooltip-width='300'>?</icon></span></label></span>
      <span>
        <label ng-repeat="(key, item) in apiMethodType" class="pointer"><input type="radio" ng-model="setItem.methodType" ng-value="key">{{item}}</label>
      </span>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>リクエストヘッダー<span class="questionBalloon"><icon class="questionBtn" data-tooltip="リクエストについての情報や属性を設定できます。<br>外部連携先のサーバーにリクエストを送る際、リクエストヘッダーとして送信されます。" data-tooltip-width='300'>?</icon></span></label></span>
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
      <span class="fb13em"><label>リクエストボディ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーにリクエストを送る際、リクエストボディとして送信されます。" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <resize-textarea ng-model="setItem.requestBody" cols="48" rows="1" placeholder="リクエストボディを設定してください" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>レスポンスタイプ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーから受け取るデータの形式です。">?</icon></span></label></span>
      <span>
        <label ng-repeat="(key, item) in apiResponseType" class="pointer"><input type="radio" ng-model="setItem.responseType" ng-value="key">{{item}}</label>
      </span>
    </li>
    <li class="styleFlexbox">
      <span class="fb13em"><label>レスポンスボディ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーから受け取ったデータを、シナリオで利用できるように変数に保存します。" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <table cellspacing="5">
          <thead>
            <tr>
              <th class="item_name">変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名に受け取ったデータの内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。" data-tooltip-width='300'>?</icon></span></th>
              <th class="item_value">変換元キー名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="受け取ったデータから、キー名を設定して変数に保存するデータを指定できます。" data-tooltip-width='300'>?</icon></span></th>
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

<?php /* ファイル送信 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_SEND_FILE ?>" class="set_action_item_body action_send_file">
  <ul>
    <li class="styleFlexbox">
      <span class="fb9em"><label>発言内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットに発言させたいテキストメッセージを設定します。">?</icon></span></label></span>
      <div>
        <resize-textarea name="message" ng-model="setItem.message" cols="48" rows="1" placeholder="メッセージを入力してください" ng-required="true" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb9em"><label>送信ファイル<span class="questionBalloon"><icon class="questionBtn" data-tooltip="送信させたいファイルを設定します。" data-tooltip-width='200'>?</icon></span></label></span>
      <ul class="selectFileArea">
        <li ng-if="!main.isFileSet(setItem)">
          <span>ファイルが選択されていません</span>
        </li>
        <li ng-if="main.isFileSet(setItem)" class="styleFlexbox">
          <div class="fb5em fileImage">
            <img ng-if="widget.isImage(setItem.file.extension)" ng-src="{{setItem.file.download_url}}" width="64" height="64">
            <i ng-if="!widget.isImage(setItem.file.extension)" ng-class="widget.selectIconClassFromExtension(setItem.file.extension)" class="fa fa-4x" aria-hidden="true"></i>
          </div>
          <div class="fileDetail"><span>{{setItem.file.file_name}}</span><span>{{setItem.file.file_size}}</span></div>
        </li>
        <li class="uploadProgress hide">
          <div class="uploadProgressArea"><span>アップロード中 ...</span><div class="uploadProgressRate"><span>アップロード中 ...</span></div></div>
        </li>
        <li>
          <input type="file" class="hide fileElm"><span class="greenBtn btn-shadow" ng-click="main.selectFile($event)">ファイル選択</span><span class="btn-shadow" ng-class="{disOffgrayBtn: !setItem.file, redBtn: !!setItem.file}" ng-click="!!setItem.file && main.removeFile($event, setActionId)">ファイル削除</span>
        </li>
      </ul>
    </li>
  </ul>
</div>

<?php /* ファイル受信 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_RECEIVE_FILE ?>" class="set_action_item_body action_send_file">
  <ul>
    <li class="styleFlexbox">
      <span class="fb11em"><label>発言内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットボットに発言させたいテキストメッセージを設定します。">?</icon></span></label></span>
      <div>
        <resize-textarea name="dropAreaMessage" ng-model="setItem.dropAreaMessage" cols="48" rows="1" placeholder="メッセージを入力してください" ng-required="true" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em"><label>ファイル形式</label></span>
      <div>
        <label ng-repeat="(key, item) in receiveFileTypeList" class="styleBlock pointer"><input type="radio" name="action_{{setActionId}}_receive_file_type" value="{{key}}" ng-model="setItem.receiveFileType">{{item.label}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="{{item.tooltip}}" data-tooltip-width='240'>?</icon></span><p class="radio-annotation"><s>{{item.annotation}}</s></p></label>
        <input type="text" name="extendedReceiveFileExtensions" ng-model="setItem.extendedReceiveFileExtensions" ng-if="setItem.receiveFileType == 2">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em" style="white-space:normal;"><label>ファイルエラー時の<br>返信メッセージ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="ファイル選択時にエラーだった場合のメッセージを設定します。">?</icon></span></label></span>
      <div style="display:flex; align-items: center;">
        <resize-textarea name="errorMessage" ng-model="setItem.errorMessage" cols="48" rows="1" placeholder="メッセージを入力してください" ng-required="true" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.cancelEnabled" ng-init="setItem.isConfirm = setItem.isConfirm == 1">キャンセルできるようにする<span class="questionBalloon"><icon class="questionBtn" data-tooltip="質問内容を全て聞き終えた後に、サイト訪問者に確認メッセージを送ることが出来ます。" data-tooltip-width='300'>?</icon></span></label>
      <ul ng-if="setItem.cancelEnabled == true" class="indentDown">
        <li class="styleFlexbox">
          <span class="fb9em"><label>名称<span class="questionBalloon"><icon class="questionBtn" data-tooltip="確認メッセージとして送信するメッセージを設定します。<br><br>＜設定例＞<br>お名前　　　　：{&thinsp;{名前}&thinsp;}<br>電話番号　　　：{&thinsp;{電話番号}&thinsp;}<br>メールアドレス：{&thinsp;{メールアドレス}&thinsp;}<br>でよろしいでしょうか？" data-tooltip-width='300'>?</icon></span></label></span>
          <div>
            <input type="text" name="cancelLabel" ng-model="setItem.cancelLabel">
          </div>
        </li>
      </ul>
    </li>
  </ul>
</div>

<?php /* 条件分岐 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_BRANCH_ON_CONDITION ?>" class="set_action_item_body action_branch_on_condition" ng-init="main.controllBranchOnConditionSettingView(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb13em"><label>参照する変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="送信先のメールアドレスを設定します。<br>（変数の利用も可能です）" data-tooltip-width='210'>?</icon></span></label></span>
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
          <span class="fb13em indentDown"><label>変数の値が</label></span>
          <input type="text" ng-model="condition.matchValue">
          <select class="m10r10l" ng-model="condition.matchValueType" ng-init="condition.matchValueType = condition.matchValueType.toString()" ng-options="index as type.label for (index, type) in matchValueTypeList"></select>
        </li>
        <li class="styleFlexbox m10b">
          <span class="fb13em indentDown"><label></label></span>
          <s>※複数の値を設定する場合はスペースで区切ってください。</s>
        </li>
        <li class="styleFlexbox">
          <div class="fb13em indentDown">実行するアクション</div>
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
            <label class="executeNextActionCheck pointer"><input type="checkbox" ng-model="condition.action.executeNextAction" ng-init="condition.action.executeNextAction = condition.action.executeNextAction == 1">終了後、このシナリオに戻る<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したシナリオの終了後、このアクションの続きを実行するか設定できます。" data-tooltip-width='300'>?</icon></span></label>
          </div>
          <div class="conditionAction" ng-if="condition.actionType == 3 || condition.actionType == 4"></div>
        </li>
      </ul>
      <div class='area-btn'>
        <div class="btnBlock">
          <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a>
        </div>
      </div>
    </li>
    <li>
      <div>
        <hr class="separator"/>
        <label class="fb13em pointer p05tb"><input type="checkbox" ng-model="setItem.elseEnabled" ng-init="setItem.elseEnabled = false">上記を満たさない場合に実行するアクション<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャット履歴の「成果」に「途中離脱」または「CV」として自動登録します。<br><br>【途中離脱】ヒアリング途中で終了した場合<br>【CV】全項目のヒアリングが完了した場合（入力内容の確認を行う場合は「OK」が選択された場合）" data-tooltip-width='300'>?</icon></span></label>
        <ul class="condition else" ng-if="setItem.elseEnabled == true">
          <li class="styleFlexbox">
            <div class="conditionTypeSelect">
              <select class="m10r" ng-model="setItem.elseAction.actionType" ng-init="setItem.elseAction.actionType = setItem.elseAction.actionType.toString()" ng-options="index as type.label for (index, type) in processActionTypeList"></select>
            </div>
            <div class="conditionAction elseCondition" ng-if="setItem.elseAction.actionType == 1">
              <resize-textarea ng-model="setItem.elseAction.action.message" placeholder="メッセージを入力してください"></resize-textarea>
            </div>
            <div class="conditionAction elseCondition" ng-if="setItem.elseAction.actionType == 2">
              <select ng-model="setItem.elseAction.action.callScenarioId" ng-if="setItem.elseAction.actionType == 2" ng-init="setItem.elseAction.action.callScenarioId" ng-options="item.id as item.name for item in main.scenarioList">
                <option value="">シナリオを選択してください</option>
                <option value="self">このシナリオ</option>
              </select>
              <label class="executeNextActionCheck pointer"><input type="checkbox" ng-model="setItem.elseAction.action.executeNextAction" ng-init="setItem.elseAction.action.executeNextAction = setItem.elseAction.action.executeNextAction == 1">終了後、このシナリオに戻る<span class="questionBalloon"><icon class="questionBtn" data-tooltip="呼び出したシナリオの終了後、このアクションの続きを実行するか設定できます。" data-tooltip-width='300'>?</icon></span></label>
            </div>
            <div class="conditionAction elseCondition" ng-if="condition.actionType == 3 || condition.actionType == 4"></div>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</div>
