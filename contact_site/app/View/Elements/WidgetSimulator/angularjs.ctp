<script type="text/javascript">
'use strict';

sincloApp.controller('SimulatorController', ['$scope', '$timeout', 'SimulatorService', function($scope, $timeout, SimulatorService) {
  //thisを変数にいれておく
  var self = this;
  $scope.simulatorSettings = SimulatorService;

  $scope.isTabDisplay = document.getElementById('TChatbotScenarioIsTabDisplay').value || true;
  $scope.canVisitorSendMessage = document.getElementById('TChatbotScenarioCanVisitorSendMessage').value || false;

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
    var elms = $('#chatTalk div:nth-child(n+3)');
    angular.forEach(elms, function(elm) {
      document.querySelector('#chatTalk').removeChild(elm);
    });
  });

  $scope.visitorSendMessage = function() {
    var message = $('#sincloChatMessage').val()
    if (typeof message === 'undefined' || message.trim() === '') {
      return;
    }

    $scope.addMessage('se', message);
    $('#sincloChatMessage').val('');
    $scope.$emit('receiveVistorMessage', message)
  };

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
   * switchChatTextAreaDisplay
   * シミュレーションの自由入力エリアの表示状態を切り替える
   * @param String isTextAreaOpen 自由入力エリアの表示状態(1: 表示, 2:非表示）
   */
  $scope.$on('switchSimulatorChatTextArea', function(event, isTextAreaOpen) {
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

    if (isTextAreaOpen === '1') {
      document.getElementById('messageBox').style.display = "block";
      document.getElementById('chatTalk').style.height = chatTalkHeight + "px";
    } else {
      document.getElementById('messageBox').style.display = "none";
      document.getElementById('chatTalk').style.height = chatTalkHeight + messageBoxHeight + "px";
    }

    // 高さ調整
    var target = $('#chatTalk');
    var time = 500;
    target.stop().animate({
      scrollTop: target.get(0).scrollHeight - target.outerHeight()
    }, time);
  });

  // ラジオボタンの選択
  $(document).on('click', '#chatTalk input[type="radio"]', function() {
    // メッセージ送信が有効な場合、$emitでイベントを送信する
    if ($scope.canVisitorSendMessage) {
      var prefix = $(this).attr('id').replace(/-sinclo-radio[0-9a-z-]+$/i, '');
      var message = $(this).val().replace(/^\s/, '');
      $scope.addMessage('se', message)
      $scope.$emit('receiveVistorMessage', message, prefix)
    }
  });
}]);

</script>
