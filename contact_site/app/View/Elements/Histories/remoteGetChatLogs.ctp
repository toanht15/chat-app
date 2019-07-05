<?php echo $this->element("common-js"); ?>
<script type="text/javascript">
$(function(){
  var type = "<?= $alertMessage['type'] ?>";
  var message = "<?= $alertMessage['text'] ?>";
  // type 1:success, 2:error
  var className = "";

  if(type == 1) {
    className = "success";
  }
  if(type == 2) {
    className = "failure";
  }

  $("#modalShortMessage").text(message).attr('style', '').addClass(className);
  $("#modalShortMessage").removeClass('popup-off');
  window.setTimeout(function(){
    $("#modalShortMessage").animate(
        {
            opacity: 0
        },
        500,
        function(){
            window.setTimeout(function(){
                $('#modalShortMessage').prop('class', 'popup-off');
            }, 500);
        }
    );
  }, 1500);
});

</script>

<div id = "modalShortMessage">
</div>
<ul>
<?php $number = -1; ?>
<?php foreach($THistoryChatLog as $key => $val): ?>
  <?php
    $className = "";
    $name = "";
    $message = "";
    $id = "";
    $historyId = "";
    $deleteMessage = "";
    $created = "";
    $deleted = "";
    $deletedUserDisplayName = "";
    $isSendFile = false;
    $isRecieveFile = false;
    $imgTag = false;

    if ( strcmp($val['THistoryChatLog']['message_type'], 1) === 0 ) {
      $className = "sinclo_re";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 2) === 0 && !empty($val['MUser']['display_name'])) {
      $className = "sinclo_se";
      $name = $val['MUser']['display_name'];
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      if(strpos($val['THistoryChatLog']['message'],'<img') !== false){
         $imgTag = true;
      }
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 3) === 0 || strcmp($val['THistoryChatLog']['message_type'], 4) === 0 ) {
      $className = "sinclo_auto";
      $name = "自動応答";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $deleteMessage = str_replace('"', '', $deleteMessage);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      if(strpos($val['THistoryChatLog']['message'],'<img') !== false){
         $imgTag = true;
      }
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 5) === 0 || strcmp($val['THistoryChatLog']['message_type'], 4) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 7) === 0) {
      $className = "sinclo_auto";
      $name = "自動返信";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $deleteMessage = str_replace('"', '', $deleteMessage);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      if(strpos($val['THistoryChatLog']['message'],'<img') !== false){
         $imgTag = true;
      }
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 6) === 0 ) {
      $className = "sinclo_se";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = true;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 8) === 0 ) {
      $className = "sinclo_re";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $deleteMessage = str_replace('"', '', $deleteMessage);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
      $val['THistoryChatLog']['message'] = '（「'.$val['THistoryChatLog']['message'].'」をクリック）';
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 12) === 0 || strcmp($val['THistoryChatLog']['message_type'], 33) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 34) === 0 || strcmp($val['THistoryChatLog']['message_type'], 35) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 36) === 0 || strcmp($val['THistoryChatLog']['message_type'], 37) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 38) === 0 || strcmp($val['THistoryChatLog']['message_type'], 39) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 43) === 0 || strcmp($val['THistoryChatLog']['message_type'], 44) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 47) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 50) === 0 || strcmp($val['THistoryChatLog']['message_type'], 51) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 53) === 0 || strcmp($val['THistoryChatLog']['message_type'], 54) === 0) {
      $className = "sinclo_re";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 13) === 0 ) {
      $className = "sinclo_re";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 19) === 0 ) {
      if(!json_decode($val['THistoryChatLog']['message'])) {
        $className = "sinclo_re";
        $name = "シナリオメッセージ（ファイル受信）";
        $id = $val['THistoryChatLog']['id'];
        $historyId = $val['THistoryChatLog']['t_histories_id'];
        $deleteMessage = $val['THistoryChatLog']['message'];
        $created = $val['THistoryChatLog']['created'];
        $deleted = $val['THistoryChatLog']['deleted'];
        $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
        $isSendFile = false;
        $isRecieveFile = false;
        $number = $number + 1;
        $imgTag = false;
      } else {
        $className = "sinclo_re";
        $name = "シナリオメッセージ（ファイル受信）";
        $id = $val['THistoryChatLog']['id'];
        $historyId = $val['THistoryChatLog']['t_histories_id'];
        $deleteMessage = "＜コメント＞".json_decode($val['THistoryChatLog']['message'])->comment;
        $deleteMessage = preg_replace( '/[\n\r]+/', ' ', $deleteMessage);
        $downloadUrl = json_decode($val['THistoryChatLog']['message'])->downloadUrl;
        $created = $val['THistoryChatLog']['created'];
        $deleted = $val['THistoryChatLog']['deleted'];
        $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
        $isSendFile = false;
        $isRecieveFile = true;
        $number = $number + 1;
        $imgTag = false;
      }
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 21) === 0 ) {
      $className = "sinclo_auto";
      $name = "シナリオメッセージ（テキスト発言）";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      if(strpos($val['THistoryChatLog']['message'],'<img') !== false){
         $imgTag = true;
      }
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 22) === 0 ) {
      $className = "sinclo_auto";
      $name = "シナリオメッセージ（ヒアリング）";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      if(strpos($val['THistoryChatLog']['message'],'<img') !== false){
         $imgTag = true;
      }
    }
    else if (  strcmp($val['THistoryChatLog']['message_type'], 23) === 0  ) {
      $className = "sinclo_auto";
      $name = "シナリオメッセージ（選択肢）";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      if(strpos($val['THistoryChatLog']['message'],'<img') !== false){
         $imgTag = true;
      }
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 27) === 0 ) {
      $className = "sinclo_auto";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = true;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 30) === 0 ) {
      $className = "sinclo_re";
      $name = "シナリオメッセージ(一括ヒアリング回答)";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 31) === 0 ) {
      $className = "sinclo_re";
      $name = "シナリオメッセージ(一括ヒアリング解析結果未修正)";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 32) === 0 ) {
      $className = "sinclo_re";
      $name = "シナリオメッセージ(一括ヒアリング内容修正)";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 40) === 0 ) {
      $className = "sinclo_se";
      $name = 'シナリオメッセージ(一括ヒアリング解析結果)';
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 41) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 42) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 45) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 46) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 49) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 52) === 0
      || strcmp($val['THistoryChatLog']['message_type'], 55) === 0) {
      $className = "sinclo_auto";
      $name = "シナリオメッセージ（ヒアリング）";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 81) === 0 || strcmp($val['THistoryChatLog']['message_type'], 82) === 0 ) {
      $className = "sinclo_auto";
      $name = "自動応答";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $deleteMessage = str_replace('"', '', $deleteMessage);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      if(strpos($val['THistoryChatLog']['message'],'<img') !== false){
        $imgTag = true;
      }
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 300) === 0 ) {
      $className = "sinclo_auto";
      $name = "チャットツリーメッセージ（分岐）";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $json = json_decode($val['THistoryChatLog']['message'], TRUE);
      $deleteMessage = str_replace(PHP_EOL, '', $json['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 301) === 0 ) {
      $className = "sinclo_re";
      $name = "チャットツリーメッセージ（分岐回答）";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $deleteMessage = str_replace(PHP_EOL, '', $val['THistoryChatLog']['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 302) === 0 ) {
      $className = "sinclo_auto";
      $name = "チャットツリーメッセージ（分岐）";
      $id = $val['THistoryChatLog']['id'];
      $historyId = $val['THistoryChatLog']['t_histories_id'];
      $json = json_decode($val['THistoryChatLog']['message'], true);
      $deleteMessage = str_replace(PHP_EOL, '', $json['message']);
      $created = $val['THistoryChatLog']['created'];
      $deleted = $val['THistoryChatLog']['deleted'];
      $deletedUserDisplayName = $val['DeleteMUser']['display_name'];
      $isSendFile = false;
      $isRecieveFile = false;
      $imgTag = false;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 90) === 0 ) {
      // 何も表示しない
      continue;
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 998) === 0 ) {
      $className = "sinclo_etc";
      $message = "- ". $val['MUser']['display_name'] . "が入室しました -";
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 999) === 0 ) {
      $className = "sinclo_etc";
      $message = "- ". $val['MUser']['display_name'] . "が退室しました -";
    }
  ?>
  <?php if ( intval($val['THistoryChatLog']['message_type']) < 90 || intval($val['THistoryChatLog']['message_type']) >= 300) { ?>
    <?php //権限が管理者、削除された履歴の場合
    if(strcmp($val['THistoryChatLog']['delete_flg'], 1) === 0) { ?>
      <li class="<?=$className?>" style = "color:#bdbdbd"><span style = "color:#bdbdbd"><?= $this->Time->format($val['THistoryChatLog']['created'], "%Y/%m/%d %H:%M:%S")?></span><span style = "color:#bdbdbd"><?=h($name)?></span><?=$this->htmlEx->makeChatView("(このメッセージは $deleted に $deletedUserDisplayName さんによって削除されました。)")?></li>
    <?php } //権限が管理者、削除されていない履歴の場合
    else if(strcmp($permissionLevel,1) === 0 && strcmp($val['THistoryChatLog']['delete_flg'], 0) === 0) { ?>
      <!--      handle json deleteMessage-->
      <?php if ($this->App->isJson($deleteMessage)) {
        $deleteMessage = json_decode($deleteMessage, TRUE)['message'];
        $deleteMessage = str_replace(PHP_EOL, '', $deleteMessage);
      } ?>
      <li class="<?=$className?>"><span><?= $this->Time->format($val['THistoryChatLog']['created'], "%Y/%m/%d %H:%M:%S")?></span><?= $this->Html->image('close_b.png', array('class' => ($coreSettings[C_COMPANY_USE_HISTORY_DELETE] ? "" : "commontooltip"),'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_DELETE] ? "" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",'data-balloon-position' => '43.5','alt' => '履歴一覧','width' => 17,'height' => 17,'style' => 'margin-top: -24px; float:right; margin-right:1px; opacity:0.7; cursor:pointer','onclick' => !$coreSettings[C_COMPANY_USE_HISTORY_DELETE] ? "" : 'openDeleteDialog('.$id.','.$historyId.',"'.(intval($val['THistoryChatLog']['message_type']) === 6 ? json_decode($deleteMessage, TRUE)["fileName"] : $deleteMessage).'","'.$created.'")')) ?>
      <span><?=h($name)?></span><?php
        if ($this->App->isJson($val['THistoryChatLog']['message'])) {
          if(intval($val['THistoryChatLog']['message_type']) === 31 || intval($val['THistoryChatLog']['message_type']) === 32 || intval($val['THistoryChatLog']['message_type']) === 40) {
            $json = json_decode($val['THistoryChatLog']['message'], TRUE);
            switch(intval($val['THistoryChatLog']['message_type'])) {
              case 32:
                $message = "";
                foreach($json as $variableName => $object) {
                  $message .= $object['label'].'：'.($object['value'])."\n";
                }
                echo $message;
                break;
              case 40:
                $message = "";
                foreach($json['target'] as $variableName => $object) {
                  $message .= $object['label'].'：'.((!empty($json['message'][$object['inputType']])) ? $json['message'][$object['inputType']] : "（なし）")."\n";
                }
                echo $message;
                break;
            }
          } else if(intval($val['THistoryChatLog']['message_type']) === 41
            || intval($val['THistoryChatLog']['message_type']) === 42
            || intval($val['THistoryChatLog']['message_type']) === 45
            || intval($val['THistoryChatLog']['message_type']) === 46
            || intval($val['THistoryChatLog']['message_type']) === 49
            || intval($val['THistoryChatLog']['message_type']) === 52
            || intval($val['THistoryChatLog']['message_type']) === 53
            || intval($val['THistoryChatLog']['message_type']) === 54
            || intval($val['THistoryChatLog']['message_type']) === 55
            || intval($val['THistoryChatLog']['message_type']) === 300
            || intval($val['THistoryChatLog']['message_type']) === 302) {
            $json = json_decode($val['THistoryChatLog']['message'], TRUE);
            $textOfMessage = '';
            if (!$json['message']) {
              switch (intval($val['THistoryChatLog']['message_type'])) {
                case 41:
                  $textOfMessage = '（プルダウン質問内容なし）';
                  break;
                case 42:
                  $textOfMessage = '（カレンダー質問内容なし）';
                  break;
                case 45:
                  $textOfMessage = '（カルーセル質問内容なし）';
                  break;
                case 46:
                  $textOfMessage = '（コンファーム質問内容なし）';
                  break;
                case 49:
                  $textOfMessage = '（ボタン質問内容なし）';
                  break;
                case 52:
                  $textOfMessage = '（チェックボックス質問内容なし）';
                  break;
                case 55:
                  $textOfMessage = '（ラジオボタン質問内容なし）';
                  break;
                case 300:
                  $textOfMessage = '（チャットツリー質問内容なし）';
                  break;
                case 302:
                  $textOfMessage = '（チャットツリー質問内容なし）';
                  break;
                default:
                  $textOfMessage = '（質問内容なし）';
                  break;
              }
            } else {
              $textOfMessage = $json['message'];
            }
            if(intval($val['THistoryChatLog']['message_type']) === 55) {
              $textOfMessage = $json['message'] . "\n";
              foreach($json['settings']['options'] as $idx => $option) {
                if($idx === (count($json['settings']['options']) - 1)) {
                  $textOfMessage .= '[] '.$option;
                } else {
                  $textOfMessage .= '[] '.$option."\n";
                }
              }
            }

            if (intval($val['THistoryChatLog']['message_type']) === 53 || intval($val['THistoryChatLog']['message_type']) === 54) {
              $json = json_decode($val['THistoryChatLog']['message'], true);
              $chatMessage = $json['message'];
              $separator = $json['separator'];
              $textOfMessage = '';
              $arr = explode($separator, $chatMessage);
              foreach ($arr as $item) {
                $textOfMessage .= '・' . $item . "\n";
              }
              $textOfMessage = trim($textOfMessage);
            }

            echo $this->htmlEx->makeChatView($textOfMessage, $isSendFile, $isRecieveFile, $imgTag);
          }
          else {
            echo $this->htmlEx->makeChatView($val['THistoryChatLog']['message'], $isSendFile, $isRecieveFile, $imgTag);
          }
        } else {
          echo $this->htmlEx->makeChatView($val['THistoryChatLog']['message'], $isSendFile, $isRecieveFile, $imgTag);
        }

      ?></li>
    <?php }
    else { //権限が一般の場合 ?>
      <li class="<?=$className?>"><span><?= $this->Time->format($val['THistoryChatLog']['created'], "%Y/%m/%d %H:%M:%S")?></span><span><?=h($name)?></span><?=$this->htmlEx->makeChatView($val['THistoryChatLog']['message'],$isSendFile,$isRecieveFile,$imgTag)?></li>
  <?php }
  } else { ?>
    <li class="<?=$className?>"><span><?= $this->Time->format($val['THistoryChatLog']['created'], "%Y/%m/%d %H:%M:%S")?></span><?=h($message)?></li>
  <?php } ?>
  <script type="text/javascript">
  $(function(){
    var number = "<?=$number?>";
    var message_type = "<?=$val['THistoryChatLog']['message_type']?>";
    if(message_type == 19 && number !== -1) {
      if($('.recieveFileContent')[number]) {
        $('.recieveFileContent')[number].style.cursor = "pointer";
        $('.recieveFileContent')[number].addEventListener("click", function (event) {
          window.open("<?=$downloadUrl?>")
        });
      }
    }
  });
  </script>
<?php endforeach; ?>
</ul>
<?php echo $this->Html->link(
    "ＣＳＶ出力",
    ['controller'=>'Histories', 'action' => 'outputCSVOfChat', h($val['THistoryChatLog']['t_histories_id'])],
    array('escape' => false, 'class'=>'skyBlueBtn btn-shadow', 'id' => 'popupCloseBtn'));
?>
