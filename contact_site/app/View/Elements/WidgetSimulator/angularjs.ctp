<script type="text/javascript">
'use strict';

sincloApp.controller('SimulatorController', ['$scope', '$timeout', 'SimulatorService', function($scope, $timeout, SimulatorService) {
  //thisを変数にいれておく
  var self = this;
  $scope.simulatorSettings = SimulatorService;

  $scope.isTabDisplay = document.getElementById('TChatbotScenarioIsTabDisplay').value || true;
  $scope.canVisitorSendMessage = document.getElementById('TChatbotScenarioCanVisitorSendMessage').value || false;

  // 自由入力エリアの表示状態
  $scope.isTextAreaOpen = true;
  // 自由入力エリアの、改行入力の許可状態
  $scope.allowInputLF = true;
  // 自由入力エリアの、メッセージ送信の許可状態
  $scope.allowSendMessageByShiftEnter = false;
  // 入力制御
  $scope.inputRule = <?= C_MATCH_INPUT_RULE_ALL ?>;

  /**
   * addReMessage
   * 企業側メッセージの追加
   * @param String message 追加するメッセージ
   * @param String prefix  ラジオボタンに付与するプレフィックス
   */
  $scope.$on('addReMessage', function(event, message, prefix) {
    $scope.addMessage('re', message, prefix);
  });

  /**
   * addSeMessage
   * サイト訪問者側メッセージの追加 TODO: 現在使用されていないため、仮実装状態
   * @param String message 追加するメッセージ
   */
  $scope.$on('addSeMessage', function(event, message) {
    console.log("=== SimulatorController::addSeMessage ===");
  });

  /**
   * addReFileMessage
   * 企業側ファイル送信のメッセージ追加
   * @param Object fileObj 追加するメッセージ
   */
  $scope.$on('addReFileMessage', function(event, fileObj) {
    $scope.addFileMessage('re', fileObj);
  });

  /**
   * removeMessage
   * メッセージの消去（コピー元となる非表示要素を残して削除する）
   */
  $scope.$on('removeMessage', function(event) {
    document.querySelector('#sincloChatMessage').value = '';
    var elms = $('#chatTalk > div:not([style*="display: none;"])');
    angular.forEach(elms, function(elm) {
      document.querySelector('#chatTalk').removeChild(elm);
    });
  });

  /**
   * visitorSendMessage
   * サイト訪問者のメッセージ受信と、呼び出し元アクションへの通知
   */
  $scope.visitorSendMessage = function() {
    var message = $('#sincloChatMessage').val()
    if (typeof message === 'undefined' || message.trim() === '') {
      return;
    }

    // 設定を初期状態に戻す
    $scope.allowInputLF = true;
    $scope.allowSendMessageByShiftEnter = false;
    $scope.inputRule = <?= C_MATCH_INPUT_RULE_ALL ?>;
    self.setPlaceholder();

    $scope.addMessage('se', message);
    $('#sincloChatMessage').val('');
    $scope.$emit('receiveVistorMessage', message)
  };

  /**
   * addMessage
   * シミュレーター上へのメッセージ追加
   * @param String type     企業側(re)・訪問者側(se)のメッセージタイプ
   * @param String message  追加するメッセージ
   * @param String prefix   ラジオボタンに付与するプレフィックス
   */
  $scope.addMessage = function(type, message, prefix) {
    // ベースとなる要素をクローンし、メッセージを挿入する
    if (type === 're') {
      var divElm = document.querySelector('#chatTalk div > li.sinclo_re.chat_left').parentNode.cloneNode(true);
    } else {
      var divElm = document.querySelector('#chatTalk div > li.sinclo_se.chat_right').parentNode.cloneNode(true);
    }
    var formattedMessage = $scope.simulatorSettings.createMessage(message, prefix);
    divElm.querySelector('li .details:not(.cName)').innerHTML = formattedMessage;

    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $('#chatTalk div:last-child').show();

    self.autoScroll();
  };

  /**
   * addFileMessage
   * シミュレーター上へのファイル送信メッセージ追加
   * @param String type     企業側(re)・訪問者側(se)のメッセージタイプ
   * @param Object fileObj  追加するメッセージ
   * @param String prefix   ラジオボタンに付与するプレフィックス
   */
  $scope.addFileMessage = function(type, fileObj) {
    // ベースとなる要素をクローンする
    if (type === 're') {
      var list = document.querySelector('#chatTalk div > li.sinclo_re.file_left');
      var divElm = document.querySelector('#chatTalk div > li.sinclo_re.file_left').parentNode.cloneNode(true);
    } else {
      // 訪問者側からのファイル受信UIは未対応です
    }

    // パラメーターを表示用に設定する
    divElm.querySelector('li .sendFileThumbnailArea .sendFileThumbnail').src = fileObj.uploadUrl; // TODO: downloadUrl に変更すること
    divElm.querySelector('li .sendFileMetaArea .sendFileName').innerHTML = fileObj.fileName;
    divElm.querySelector('li .sendFileMetaArea .sendFileSize').innerHTML = (fileObj.fileSize / 1024).toFixed(2) + 'KB';
    divElm.addEventListener('click', function() {
      window.open(fileObj.uploadUrl); // TODO: downloadUrl に変更すること
    });

    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $('#chatTalk div:last-child').show();

    self.autoScroll()
  };

  /**
   * switchChatTextAreaDisplay
   * シミュレーションの自由入力エリアの表示状態を切り替える
   * @param Boolean status 自由入力エリアの表示状態(true: 表示, false: 非表示）
   */
  $scope.$on('switchSimulatorChatTextArea', function(event, status) {
    $scope.isTextAreaOpen = status;
  });

  /**
   * 自由入力エリアの改行入力の許可状態を一時的に切り替える
   * （allowSendMessageByShiftEnterと同時に設定しないことを前提とする）
   * @param Boolean status 改行入力の許可状態
   */
  $scope.$on('allowInputLF', function(event, status) {
    $scope.allowInputLF = status === true;
    self.setPlaceholder('（Enter/Shift+Enterで送信）');
  });

  /**
   * 自由入力エリアのメッセージ送信設定を一時的に切り替える
   * （allowInputLFと同時に設定しないことを前提とする）
   * @param Boolean status Shift+Enterでのメッセージ送信の許可状態
   */
  $scope.$on('allowSendMessageByShiftEnter', function(event, status) {
    $scope.allowSendMessageByShiftEnter = status === true;
    self.setPlaceholder('（Enterで改行/Shift+Enterで送信）');
  });

  /**
   * setInputRule
   * 入力制限の設定
   * （サイト訪問者のメッセージ送信後に、状態を戻す）
   * @param Boolean rule 設定したい入力制限(正規表現)
   */
  $scope.$on('setInputRule', function(event, rule) {
    $scope.inputRule = rule;
  });

  /**
   * isTextAreaOpen
   * showWidgetTypeを元に自由入力エリアの表示を切り替える
   */
  $scope.$watch('isTextAreaOpen', function() {
    var msgBoxElm = document.getElementById('messageBox');
    var chatTalkElm = document.getElementById('chatTalk');
    if (msgBoxElm === null || chatTalkElm === null) {
      return;
    }

    var chatTalkHeight = 194;
    var messageBoxHeight = 75;

    switch($scope.simulatorSettings.showWidgetType) {
      case 1: // 表示タブ：通常
        // ウィジェットサイズごとにサイズを変更する
        if($scope.simulatorSettings.settings['widget_size_type'] == 2) {
          chatTalkHeight = 284;
        } else if($scope.simulatorSettings.settings['widget_size_type'] == 3) {
          chatTalkHeight = 374;
        }
        // プレミアムプラン以外の場合、高さを調整する
        <?php if ( !$coreSettings[C_COMPANY_USE_SYNCLO] && (!isset($coreSettings[C_COMPANY_USE_DOCUMENT]) || !$coreSettings[C_COMPANY_USE_DOCUMENT]) ): ?>
          messageBoxHeight -= 3;
        <?php endif; ?>
        break;
      case 2:  // 表示タブ：スマートフォン(横)
        chatTalkHeight = 90;
        messageBoxHeight = 62;
        break;
      case 3:  // 表示タブ：スマートフォン(縦)
        chatTalkHeight = 184;
        messageBoxHeight = 72;
        break;
    }

    if ($scope.isTextAreaOpen) {
      msgBoxElm.style.display = "";
      chatTalkElm.style.height = chatTalkHeight + "px";
    } else {
      msgBoxElm.style.display = "none";
      chatTalkElm.style.height = chatTalkHeight + messageBoxHeight + "px";
    }

    self.autoScroll();
  });

  /**
   * メッセージ追加後のスクロールアニメーション
   */
  this.autoScroll = function() {
    $timeout(function() {
      var target = $('#chatTalk');
      var time = 500;
      target.stop().animate({
        scrollTop: target.get(0).scrollHeight - target.outerHeight()
      }, time);
    }, 0);
  }

  /**
   * プレースホルダを設定する
   * @param String message プレースホルダに設定するメッセージ（指定がない場合は変更前に戻す）
   */
  this.setPlaceholder = function(message) {
    var elm = document.querySelector('#sincloChatMessage');

    if (typeof message === 'undefined' || message == null) {
      elm.placeholder = $scope.placeholder || elm.placeholder;
    } else {
      $scope.placeholder = elm.placeholder;
      elm.placeholder = elm.placeholder.replace(/(（.+）$)/, message);
    }
  }

  // 自由入力エリアのキーイベント
  $(document).on('keypress', '#sincloChatMessage', function(e) {
    // ヒアリング：改行不可（Enterキーでメッセージ送信）
    if (!$scope.allowInputLF && e.key === 'Enter') {
      $scope.visitorSendMessage();
      return false;
    } else
    // ヒアリング：改行可（Shift+Enterキーでメッセージ送信）
    if ($scope.allowSendMessageByShiftEnter && e.key === 'Enter' && e.shiftKey) {
      $scope.visitorSendMessage();
      return false;
    }

    // 入力制限
    var regex = new RegExp($scope.inputRule);
    if (!regex.test(e.key)) {
      return false;
    }
  });

  // ラジオボタンの選択
  $(document).on('click', '#chatTalk input[type="radio"]', function() {
    // メッセージ送信が有効な場合
    if ($scope.canVisitorSendMessage) {
      var prefix = $(this).attr('id').replace(/-sinclo-radio[0-9a-z-]+$/i, '');
      var message = $(this).val().replace(/^\s/, '');
      var name = $(this).attr('name');

      // ウィジェット設定とテキストエリアの表示状態により、選択された文字列の処理を変更する
      if ($scope.simulatorSettings.settings['chat_radio_behavior'] == <?= C_WIDGET_RADIO_CLICK_SEND ?> || !$scope.isTextAreaOpen) {
        // 即時送信
        $scope.addMessage('se', message)
        $scope.$emit('receiveVistorMessage', message, prefix)
      } else {
        // テキストエリアへの入力
        document.querySelector('#sincloChatMessage').value = message;
      }

      // ラジオボタンを非活性にする
      $('input[name=' + name + '][type="radio"]').prop('disabled', true);
    }
  });

  // ダウンロード可能な吹き出しの背景色切替
  $(document).on('mouseenter', '#chatTalk .file_left', function(e){
    e.target.style.backgroundColor = $scope.simulatorSettings.makeFaintColor(0.9);
  }).on('mouseleave', '#chatTalk .file_left', function(e){
    e.target.style.backgroundColor = $scope.simulatorSettings.makeFaintColor();
  });
}]);

</script>
