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
      <span class="fb13em"><label>レスポンスボディ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーから取得したデータを、シナリオで利用できるように変数に保存します。" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <table cellspacing="5">
          <thead>
          <tr>
            <th class="item_name">変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名に取得したデータの内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。" data-tooltip-width='300'>?</icon></span></th>
            <th class="item_value">変換元キー名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="取得したデータから、キー名を設定して変数に保存するデータを指定できます。" data-tooltip-width='300'>?</icon></span></th>
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