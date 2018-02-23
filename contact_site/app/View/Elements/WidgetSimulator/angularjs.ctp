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
  // 自由入力エリアへの、改行入力の許可状態
  $scope.allowInputLF = true;

  /**
   * addReMessage
   * 企業側メッセージの追加
   * @param String message 追加するメッセージ
   */
  $scope.$on('addReMessage', function(event, message, prefix) {
    $scope.addMessage('re', message, prefix);
  });

  /**
   * addSeMessage
   * サイト訪問者側メッセージの追加
   * @param String message 追加するメッセージ
   */
  $scope.$on('addSeMessage', function(event, message) {
    console.log("=== SimulatorController::addSeMessage ===");
  });

  /**
   * removeMessage
   * メッセージの消去
   */
  $scope.$on('removeMessage', function(event) {
    document.querySelector('#sincloChatMessage').value = '';
    var elms = $('#chatTalk div:nth-child(n+3)');
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

    // 自由入力エリアの改行許可状態を戻す
    $scope.allowInputLF = true;

    // placeholder を戻す
    document.querySelector('#sincloChatMessage').placeholder = $scope.defaultPlaceholder;

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

    // 高さ調整
    $timeout(function() {
      var target = $('#chatTalk');
      var time = 500;
      target.stop().animate({
        scrollTop: target.get(0).scrollHeight - target.outerHeight()
      }, time);
    }, 0);
  }

  /**
   * setPlaceholder
   * プレースホルダの設定
   * （サイト訪問者のメッセージ送信後に、プレースホルダの内容を戻す）
   * @param String message プレースホルダに設定するメッセージ
   */
  $scope.$on('setPlaceholder', function(event, message) {
    var elm = document.querySelector('#sincloChatMessage');
    $scope.defaultPlaceholder = elm.placeholder;
    elm.placeholder = message;
  });

  /**
   * switchChatTextAreaDisplay
   * シミュレーションの自由入力エリアの表示状態を切り替える
   * @param Boolean status 自由入力エリアの表示状態(true: 表示, false: 非表示）
   */
  $scope.$on('switchSimulatorChatTextArea', function(event, status) {
    $scope.isTextAreaOpen = status;
  });

  /**
   * allowInputLF
   * 自由入力エリアの改行入力の許可状態を切り替える
   * （サイト訪問者のメッセージ送信後に、状態を戻す）
   * @param Boolean status 改行入力の許可状態(true: 表示, false: 非表示)
   */
  $scope.$on('allowInputLF', function(event, status) {
    $scope.allowInputLF = status == '1' ? true : false;
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

    // 高さ調整
    var target = $('#chatTalk');
    var time = 500;
    target.stop().animate({
      scrollTop: target.get(0).scrollHeight - target.outerHeight()
    }, time);
  });

  // 自由入力エリアのキーイベント
  $(document).on('keypress', '#sincloChatMessage', function(e) {
    // Enterキー
    if (e.which === 13 && !e.shiftKey) {
      // メッセージ送信、かつEnterキー押下で消費者側送信アクションが有効
      if ($scope.canVisitorSendMessage && $scope.simulatorSettings.settings['chat_trigger'] == <?= C_WIDGET_RADIO_CLICK_SEND ?>) {
        $scope.visitorSendMessage();
        return false;
      }
    }
    if (e.which === 13) {
      // 改行が許可されていない
      if (!$scope.allowInputLF) {
        return false;
      }
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
}]);

</script>
