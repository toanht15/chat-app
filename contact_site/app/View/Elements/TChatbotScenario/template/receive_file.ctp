<?php /* ファイル受信 */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_RECEIVE_FILE ?>" class="set_action_item_body action_receive_file">
  <ul>
    <li class="styleFlexbox">
      <span class="fb11em"><label>見出し文<span class="questionBalloon"><icon class="questionBtn" data-tooltip="見出しに表示するテキストメッセージを設定します。">?</icon></span></label></span>
      <div>
        <resize-textarea name="dropAreaMessage" ng-model="setItem.dropAreaMessage" cols="48" rows="1" placeholder="メッセージを入力してください" ng-required="true" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em"><label>ファイル形式</label></span>
      <div>
        <label ng-repeat="(key, item) in receiveFileTypeList" class="styleBlock pointer"><input class="selectFileTypeRadio" type="radio" name="action_{{setActionId}}_receive_file_type" value="{{key}}" ng-model="setItem.receiveFileType">{{item.label}}<span class="questionBalloon"><icon class="questionBtn" data-tooltip="{{item.tooltip}}">?</icon></span><p class="radio-annotation"><s>{{item.annotation}}</s></p></label>
        <input type="text" name="extendedReceiveFileExtensions" ng-model="setItem.extendedReceiveFileExtensions" ng-if="setItem.receiveFileType == 2">
      </div>
    </li>
    <li class="styleFlexbox">
      <span class="fb11em" style="white-space:normal;"><label>ファイルエラー時の<br>返信メッセージ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="指定外のファイル形式のファイルが選択された場合や複数のファイルが選択された場合にチャットボットに発言させるテキストメッセージを設定します。">?</icon></span></label></span>
      <div style="display:flex; align-items: center;">
        <resize-textarea name="errorMessage" ng-model="setItem.errorMessage" cols="48" rows="1" placeholder="メッセージを入力してください" ng-required="true" data-maxRow="10"></resize-textarea>
      </div>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.cancelEnabled" ng-init="setItem.isConfirm = setItem.isConfirm == 0">キャンセルできるようにする<span class="questionBalloon"><icon class="questionBtn" data-tooltip="ファイル送信をキャンセルできるようにする（ファイル送信を行わず次のアクションに進むことを許容する）場合、チェックをONにしてください。">?</icon></span></label>

      <ul ng-if="setItem.cancelEnabled == true" class="indentDown">
        <li class="styleFlexbox">
          <span class="fb9em"><label>名称<span class="questionBalloon"><icon class="questionBtn" data-tooltip="キャンセルボタンの表示テキストを設定します。">?</icon></span></label></span>
          <div>
            <input type="text" name="cancelLabel" ng-model="setItem.cancelLabel">
          </div>
        </li>
      </ul>
    </li>
  </ul>
</div>