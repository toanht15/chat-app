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
      <span class="fb9em"><label>送信ファイル<span class="questionBalloon"><icon class="questionBtn" data-tooltip="送信させたいファイルを設定します。">?</icon></span></label></span>
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