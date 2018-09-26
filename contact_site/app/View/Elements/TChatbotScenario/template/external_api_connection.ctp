<?php /* 外部システム連携 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_EXTERNAL_API ?>" class="set_action_item_body action_external_api_connection" ng-init="main.controllExternalApiSetting(setActionId)">
  <ul>
    <li class="styleFlexbox">
      <span class="fb13em"><label>連携タイプ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携の種類です。<br><br>＜連携タイプ＞<br>API連携 ：外部APIとの連携を行います。<br>スクリプト：スクリプト（JavaScript）を実行します。" data-tooltip-width='300'>?</icon></span></label></span>
      <span>
        <label ng-repeat="(key, item) in externalType" class="pointer"><input type="radio" style="outline:0 ;" ng-model="setItem.externalType" ng-value="key">{{item}}</label>
      </span>
    </li>
    <li class="styleFlexbox" ng-if="setItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_API ?>">
      <span class="fb13em"><label>連携先URL<span class="questionBalloon"><icon class="questionBtn" data-tooltip="HTTP(S)で通信するリクエスト対象の外部連携先のURLです。<br>（変数の利用も可能です）" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <input ng-model="setItem.url" type="text" placeholder="外部連携先のURLを入力してください">
      </div>
    </li>
    <li class="styleFlexbox" ng-if="setItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_API ?>">
      <span class="fb13em"><label>メソッド種別<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先と通信する際のリクエスト方法の種類です。<br><br>＜メソッド種別＞<br>GET ：送信したいデータをURLに付加して、サーバーへ送ります。<br>POST：送信したいデータをリクエストヘッダーとリクエストボディに設定して、サーバーへ送ります。" data-tooltip-width='300'>?</icon></span></label></span>
      <span>
        <label ng-repeat="(key, item) in apiMethodType" class="pointer"><input type="radio" ng-model="setItem.methodType" ng-value="key">{{item}}</label>
      </span>
    </li>
    <li class="styleFlexbox" ng-if="setItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_API ?>">
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
    <li class="styleFlexbox" ng-if="setItem.methodType == <?= C_SCENARIO_METHOD_TYPE_POST ?> && setItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_API ?>">
      <span class="fb13em"><label>リクエストボディ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーにリクエストを送る際、リクエストボディとして送信されます。" data-tooltip-width='300'>?</icon></span></label></span>
      <div>
        <resize-textarea ng-model="setItem.requestBody" cols="48" rows="1" placeholder="リクエストボディを設定してください" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li class="styleFlexbox" ng-if="setItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_API ?>">
      <span class="fb13em"><label>レスポンスタイプ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーから受け取るデータの形式です。">?</icon></span></label></span>
      <span>
        <label ng-repeat="(key, item) in apiResponseType" class="pointer"><input type="radio" ng-model="setItem.responseType" ng-value="key">{{item}}</label>
      </span>
    </li>
    <li class="styleFlexbox" ng-if="setItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_API ?>">
      <span class="fb13em"><label>レスポンスボディ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="外部連携先のサーバーから取得したデータを、シナリオで利用できるように変数に保存します。">?</icon></span></label></span>
      <div>
        <table cellspacing="5">
          <thead>
          <tr>
            <th class="item_name">変数名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="変数名を設定します。<br>ここで設定した変数名に取得したデータの内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。">?</icon></span></th>
            <th class="item_value">変換元キー名<span class="questionBalloon"><icon class="questionBtn" data-tooltip="取得したデータから、キー名を設定して変数に保存するデータを指定できます。">?</icon></span></th>
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
    <li class="styleFlexbox" ng-if="setItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_SCRIPT ?>">
      <span class="fb7em"><label>スクリプト<span class="questionBalloon"><icon class="questionBtn" data-tooltip="実行したいスクリプト（JavaScript）を設定します。<br>スクリプト内で変数の利用も可能です。<br>スクリプト内で変数を利用する場合は{{showExpression('変数名')}}<br>をダブルクオーテーションで括る必要があります。<br>（例：&quot;{{showExpression('変数名')}}&quot;）<br><br>※先頭行および最終行のscriptタグの設定は不要です。">?</icon></span></label></span>
      <div>
        <textarea style="font-size: 13px; border-width: 1px; padding: 5px; margin-left: 72px; line-height: 1.5; overflow: auto; width: calc(100% - 157px); resize: vertical;" name="externalScript" maxlength="4000" ng-model="setItem.externalScript" cols="48" rows="9" placeholder="/* Google広告トラッキング */
  var img;
  google_conversion_id = 'YOUR_CONVERSION_ID';
  google_conversion_label = 'YOUR_CONVERSION_LABEL';
  google_conversion_value = 0;
  img = new Image(1, 1);
  img.id = 'conversionCaller'+google_conversion_id;
  img.src = 'https://www.googleadservices.com/pagead/conversion/'+google_conversion_id+'/?label='google_conversion_label+'&script=0';
  document.body.appendChild(img);" ng-required="true" required="required" class="ng-invalid ng-invalid-required ng-valid-maxlength ng-touched"></textarea>
      </div>
    </li>
    <li class="styleFlexbox align_center" ng-if="setItem.externalType == <?= C_SCENARIO_EXTERNAL_TYPE_SCRIPT ?>">
      <i class="icon fal fa-lightbulb-on" style="margin:0 5px 0 156px; font-size:16px;"></i><a href="https://info.sinclo.jp/manual/google-ads-conversion-tracking/" target="_blank" id = "help_link">Google広告のコンバージョンをトラッキングするスクリプトの記述例</a>
    </li>
  </ul>
</div>