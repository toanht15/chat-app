<script type="text/javascript">
'use strict';

sincloApp.controller('SimulatorController', ['$scope', '$timeout', 'SimulatorService', function($scope, $timeout, SimulatorService) {
  //thisを変数にいれておく
  var self = this;
  $scope.simulatorSettings = SimulatorService;

  $scope.isTabDisplay = document.querySelector('[id$="IsTabDisplay"]').value == true;
  $scope.canVisitorSendMessage = document.querySelector('[id$="CanVisitorSendMessage"]').value == true;

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
   *
   */
  $scope.$on('addSeReceiveFileUI', function(event, dropAreaMessage, calcelable, cancelLabel) {
    $scope.addReceiveFileUI(dropAreaMessage, calcelable, cancelLabel);
  });

  /**
   * removeMessage
   * メッセージの消去（コピー元となる非表示要素を残して削除する）
   */
  $scope.$on('removeMessage', function(event) {
    document.querySelector('#sincloChatMessage').value = '';
    document.querySelector('#miniSincloChatMessage').value = '';
    var elms = $('#chatTalk > div:not([style*="display: none;"])');
    angular.forEach(elms, function(elm) {
      document.querySelector('#chatTalk').removeChild(elm);
    });
    $scope.resizeWidgetHeightByWindowHeight();
  });

  /**
   * visitorSendMessage
   * サイト訪問者のメッセージ受信と、呼び出し元アクションへの通知
   */
  $scope.visitorSendMessage = function() {
    var message = $('#sincloChatMessage').val() ? $('#sincloChatMessage').val() : $('#miniSincloChatMessage').val();
    if (typeof message === 'undefined' || message.trim() === '') {
      return;
    }

    // 設定を初期状態に戻す
    $scope.allowInputLF = true;
    $scope.allowSendMessageByShiftEnter = false;
    $scope.inputRule = <?= C_MATCH_INPUT_RULE_ALL ?>;

    $scope.addMessage('se', message);
    $('#sincloChatMessage').val('');
    $('#miniSincloChatMessage').val('');
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
    var tmbImage =  divElm.querySelector('li .sendFileThumbnailArea img.sendFileThumbnail');
    var tmbIcon =  divElm.querySelector('li .sendFileThumbnailArea i.sendFileThumbnail');
    if ($scope.simulatorSettings.isImage(fileObj.extension)) {
      tmbImage.src = fileObj.download_url;
      tmbImage.style.display = "";
      tmbIcon.style.display = "none";
    } else {
      tmbIcon.classList.add($scope.simulatorSettings.selectIconClassFromExtension(fileObj.extension));
      tmbIcon.style.display = "";
      tmbImage.style.display = "none";
    }
    divElm.querySelector('li .sendFileMetaArea .sendFileName').innerHTML = fileObj.file_name;
    divElm.querySelector('li .sendFileMetaArea .sendFileSize').innerHTML = fileObj.file_size;
    divElm.addEventListener('click', function() {
      window.open(fileObj.download_url);
    });

    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $('#chatTalk div:last-child').show();

    self.autoScroll()
  };

  /**
   * addReceiveFileUI
   * シミュレーター上へのファイル受信用UI表示追加
   */
  $scope.addReceiveFileUI = function(dropAreaMessage, cancelEnabled, cancelButtonLabel) {
    // ベースとなる要素をクローン
    var divElm = document.querySelector('#chatTalk div > li.sinclo_re.recv_file_left').parentNode.cloneNode(true);
    var dropAreaMessageElm = divElm.querySelector('li.recv_file_left div.receiveFileContent div.selectFileArea p.drop-area-message');
    var selectFileButtonElm = divElm.querySelector('li.recv_file_left div.receiveFileContent div.selectFileArea p.drop-area-button a');
    dropAreaMessageElm.innerHTML = dropAreaMessage;
    selectFileButtonElm.addEventListener('click', $scope.onClickSelectFileButton);
    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $('#chatTalk div:last-child').show();
    self.autoScroll();
  }

  $scope.onClickSelectFileButton = function(event) {
    var targetInput = $(event.target).parents('div.selectFileArea').find('.receiveFileInput');
    if(targetInput.length === 1) {
      targetInput.off('change');
      var self = this;
      targetInput.val(null);
      targetInput.trigger('click');
      targetInput.on('change', function(e){
        var fileObj = this.files.item(0);
        var fileReader = new FileReader();

        fileReader.onload = function(fileEv) {
          if(!fileObj.name){
            return;
          }
          var loadData = fileEv.target.result;
          $scope.showPreview(self, fileObj, loadData);
        };
        fileReader.readAsArrayBuffer(fileObj);
      });
    }
  };

  $scope.showPreview = function(target, fileObj, loadData) {
    $(target).parents('li.sinclo_re.recv_file_left').parent().hide();

    // ベースとなる要素をクローン
    var divElm = document.querySelector('#chatTalk div > li.sinclo_se.recv_file_right').parentNode.cloneNode(true);
    var imgElm = document.createElement('img');

    var fileReader = new FileReader();
    fileReader.onload = function(e) {
      imgElm.src = this.result;
      divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent p.preview').appendChild(imgElm);
      divElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap a.cancel-file-button').addEventListener('click', function(e){
        document.getElementById('chatTalk').removeChild(divElm);
        $(target).parents('li.sinclo_re.recv_file_left').parent().show();
      });
      divElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap a.send-file-button').addEventListener('click', function(e){
        var comment = divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentarea textarea').value;
        if(!comment) {
          comment = "no comment";
        }
        $scope.uploadFile(divElm, comment, fileObj, loadData);
      });
      // 要素を追加する
      document.getElementById('chatTalk').appendChild(divElm);
      $('#chatTalk div:last-child').show();
      self.autoScroll();
    };
    fileReader.readAsDataURL(fileObj);
  };

  // ファイル送信
  $scope.uploadFile = function(targetDivElm, comment, fileObj, loadFile) {
    var fd = new FormData();
    var blob = new Blob([loadFile], {type: fileObj.type});
    fd.append("k", "<?= $companyKey; ?>");
    fd.append("c", comment)
    fd.append("f", blob, fileObj.name);

    $.ajax({
      url  : "<?= $this->Html->url('/FC/pus') ?>",
      type : "POST",
      data : fd,
      cache       : false,
      contentType : false,
      processData : false,
      dataType    : "json",
      xhr : function(){
        var XHR = $.ajaxSettings.xhr();
        /*
        if(XHR.upload){
          XHR.upload.addEventListener('progress',function(e){
            $scope.uploadProgress = parseInt(e.loaded/e.total*10000)/100;
            console.log($scope.uploadProgress);
            if($scope.uploadProgress === 100) {
              $('#uploadMessage').css('display', 'none');
              $('#processingMessage').css('display', 'block');
            }
            $scope.$apply();
          }, false);
        }
        */
        return XHR;
      }
    })
      .done(function(data, textStatus, jqXHR){
        console.log(JSON.stringify(data));
        var commentLabel = targetDivElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentLabel');
        var commentArea = targetDivElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentarea');
        var actionButtonWrap = targetDivElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap');
        commentArea.innerHTML = "";
        actionButtonWrap.remove();
        var commentElm = document.createElement('p');
        commentLabel.innerHTML = "＜コメント＞";
        commentArea.innerHTML = data.comment;
        $scope.$emit('receiveVistorMessage', "");
      })
      .fail(function(jqXHR, textStatus, errorThrown){
        alert("fail");
      });
  };

  /**
   * setPlaceholder
   * プレースホルダの設定
   * （サイト訪問者のメッセージ送信後に、プレースホルダの内容を戻す）
   * @param String message プレースホルダに設定するメッセージ
   */
  $scope.$on('setPlaceholder', function(event, message) {
    var elm = document.querySelector('#sincloChatMessage');
    var miniElm = document.querySelector('#miniSincloChatMessage');
    $scope.defaultPlaceholder = elm.placeholder;
    elm.placeholder = message;
    miniElm.placeholder = message;
  });

  /**
   * switchChatTextAreaDisplay
   * シミュレーションの自由入力エリアの表示状態を切り替える
   * @param Boolean status 自由入力エリアの表示状態(true: 表示, false: 非表示）
   */
  $scope.$on('switchSimulatorChatTextArea', function(event, status) {
    $scope.isTextAreaOpen = status;
    $timeout(function(){
      $scope.$apply();
    },0);
  });

  /**
   * 自由入力エリアの改行入力の許可状態を一時的に切り替える
   * （allowSendMessageByShiftEnterと同時に設定しないことを前提とする）
   * @param Boolean status 改行入力の許可状態
   */
  $scope.$on('allowInputLF', function(event, status, inputType) {
    console.log("$scope.$on('allowInputLF') inputType: %s", inputType);
    var _inputType = {
      "1": "text",
      "2": "number",
      "3": "email",
      "4": "tel"
    };
    $scope.allowInputLF = status === true;
    if($scope.allowInputLF) {
      $scope.hideMiniMessageArea();
    } else {
      $scope.showMiniMessageArea(_inputType[inputType]);
    }
    self.setPlaceholder('メッセージを入力してください');
  });

  /**
   * 自由入力エリアのメッセージ送信設定を一時的に切り替える
   * （allowInputLFと同時に設定しないことを前提とする）
   * @param Boolean status Shift+Enterでのメッセージ送信の許可状態
   */
  $scope.$on('allowSendMessageByShiftEnter', function(event, status, inputType) {
    console.log("allowSendMessageByShiftEnter");
    var _inputType = {
      "1": "text",
      "2": "number",
      "3": "email",
      "4": "tel"
    };
    $scope.allowSendMessageByShiftEnter = status === true;
    if($scope.allowSendMessageByShiftEnter) {
      $scope.hideMiniMessageArea();
    } else {
      $scope.showMiniMessageArea(_inputType[inputType]);
    }
    self.setPlaceholder('メッセージを入力してください\n（Enterで改行/Shift+Enterで送信）');
    $scope.$apply();
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
   * 改行ありのtextarea入力欄を非表示にし、改行不可のinput[type="*"]を表示する
   */
  $scope.showMiniMessageArea = function(inputType) {
    console.log("showMiniMessageArea");
    if($('#messageBox').is(':visible')) {
      var chatTalkElm = document.getElementById('chatTalk');
      var chatTalkHeight = chatTalkElm.getBoundingClientRect().height;
      document.getElementById('chatTalk').style.height = chatTalkHeight + 27 + "px";
    }
    $('#messageBox').addClass('sinclo-hide');
    $('#miniSincloChatMessage').get(0).type = inputType;
    $('#miniFlexBoxHeight').removeClass('sinclo-hide').find('#miniSincloChatMessage').focus();
    var msgBoxElm = document.getElementById('flexBoxWrap');
    msgBoxElm.dataset.originalHeight = 48;
  };

  /**
   * 改行ありのtextarea入力欄を表示し、改行不可のinput[type="*"]を非表示にする
   */
  $scope.hideMiniMessageArea = function() {
    console.log("hideMiniMessageArea");
    if($('#miniFlexBoxHeight').is(':visible')) {
      var chatTalkElm = document.getElementById('chatTalk');
      var chatTalkHeight = chatTalkElm.getBoundingClientRect().height;
      document.getElementById('chatTalk').style.height = chatTalkHeight - 27 + "px";
    }
    $('#miniFlexBoxHeight').addClass('sinclo-hide');
    $('#miniSincloChatMessage').get(0).type = 'text';
    $('#messageBox').removeClass('sinclo-hide').find('#sincloChatMessage').focus();
    var msgBoxElm = document.getElementById('flexBoxWrap');
    msgBoxElm.dataset.originalHeight = 75;
  };

  /**
   * isTextAreaOpen
   * showWidgetTypeを元に自由入力エリアの表示を切り替える
   */
  $scope.$watch('isTextAreaOpen', function() {
    $scope.setTextAreaOpenToggle();
  });
  $scope.setTextAreaOpenToggle = function() {
    console.log("setTextAreaOpenToggle");
    var msgBoxElm = document.getElementById('flexBoxWrap');
    var chatTalkElm = document.getElementById('chatTalk');
    if (msgBoxElm === null || chatTalkElm === null) {
      return;
    }

    var chatTalkHeight = chatTalkElm.getBoundingClientRect().height;
    var msgBoxHeight = msgBoxElm.getBoundingClientRect().height;

    // ウェブ接客コードが非表示な場合に、発生してしまう差分を埋める
    var offset = $scope.simulatorSettings.showWidgetType === 1 ? 0 : 3;

    console.log("msgBoxElm.dataset.originalHeight : %s",msgBoxElm.dataset.originalHeight);
    if ($scope.isTextAreaOpen) {
      console.log("SHOW");
      msgBoxElm.style.display = "";
      chatTalkElm.style.height = (chatTalkHeight - msgBoxElm.dataset.originalHeight + offset) + "px";
    } else {
      console.log("HIDE");
      msgBoxElm.style.display = "none";
      msgBoxElm.dataset.originalHeight = msgBoxHeight;
      chatTalkElm.style.height = (chatTalkHeight + msgBoxHeight - offset) + "px";
    }

    self.autoScroll();
  };

  //位置調整
  $scope.$watch(function(){
    return {'openFlg': $scope.simulatorSettings.openFlg, 'showWidgetType': $scope.simulatorSettings.showWidgetType, 'widgetSizeType': $scope.simulatorSettings.widgetSizeTypeToggle, 'chat_radio_behavior': $scope.simulatorSettings.settings['chat_radio_behavior'], 'chat_trigger': $scope.simulatorSettings.settings['chat_trigger'], 'show_name': $scope.simulatorSettings.settings['show_name'], 'widget.showTab': $scope.simulatorSettings.showTab};
  },
  function(){
    var main = document.getElementById("miniTarget");
    if ( !main ) return false;
    if ( $scope.simulatorSettings.openFlg ) {
      $timeout(function() {
        $scope.$apply();
      }).then(function() {
        angular.element("#sincloBox").addClass("open");
        var height = 0;
        for(var i = 0; main.children.length > i; i++){
          height += main.children[i].offsetHeight;
        }
        main.style.height = height + "px";

        var msgBox = document.getElementById('flexBoxWrap');
        if (!$scope.isTextAreaOpen && msgBox.style.display !== 'none') {
          $scope.setTextAreaOpenToggle();
        } else
        if ($scope.isTextAreaOpen && msgBox.style.display === 'none') {
          $scope.setTextAreaOpenToggle();
        }
      });
    }
    else {
      angular.element("#sincloBox").removeClass("open");
      main.style.height = "0";
    }
  }, true);

  /**
   * ウィジェットの表示タブ切替
   * @param Integer type 表示タイプ(1:通常, 2:スマートフォン(横), 3:スマートフォン(縦))
   */
  $scope.switchWidget = function(type) {
    var self = this;
    $scope.simulatorSettings.showWidgetType = type;
    var sincloBox = document.getElementById("sincloBox");

    // chatTalkの高さをリセットする(自由入力エリアの表示/非表示切替で設定が追加されるため)
    var chatTalkElm = document.getElementById('chatTalk');
    chatTalkElm.style.height = '';

    if ( Number(type) === 3 ) { // ｽﾏｰﾄﾌｫﾝ（縦）の表示
      $scope.simulatorSettings.showTab = 'chat'; // 強制でチャットにする
      $("#sincloBox ul#chatTalk li.boxType.chat_left").css('margin-right','17.5px');
    }

    if ( Number(type) === 1 ) { // 通常の表示
      $("#sincloBox ul#chatTalk li.boxType.chat_left").css('margin-right','');
    }

    if ( Number(type) !== 2 ) { // ｽﾏｰﾄﾌｫﾝ（横）以外は最大化する
      if(sincloBox){
        if(sincloBox.style.display == 'none'){
          sincloBox.style.display = 'block';
        }
      }
      /* ウィジェットが最小化されていたら最大化する */
      if ( !$scope.simulatorSettings.openFlg ) { // 最小化されている場合
        var main = document.getElementById("miniTarget");  // 非表示対象エリア
        var height = 0;
        if(main){
          for(var i = 0; main.children.length > i; i++){ // 非表示エリアのサイズを計測する
            if ( Number(type) === 3 && main.children[i].id === 'navigation' ) continue; // SPの場合はナビゲーションは基本表示しない
            height += main.children[i].offsetHeight;
          }
          main.style.height = height + "px";
        }
      }
    }
    if( Number(type) !== 4 ){
      if($scope.simulatorSettings.coreSettingsChat){
        document.getElementById("switch_widget").value = type;
      }
    }
    $scope.simulatorSettings.openFlg = true;

    // タブ切替後も、自由入力エリアの表示内容を保持する
    var textareaMessage = document.getElementById('sincloChatMessage').value;
    $timeout(function(){
      document.getElementById('sincloChatMessage').value = textareaMessage;
      // タブ切替の通知
      $scope.$emit('switchWidget', type)
    },0);
  };

  $scope.currentWindowHeight = $(window).height();
  angular.element(window).on('load',function(e){
    $(window).on('resize', function(e){
      if($scope.simulatorSettings.showWidgetType === 1) {
        $scope.resizeWidgetHeightByWindowHeight();
      }
    });
    $scope.resizeWidgetHeightByWindowHeight();
  });

  $scope.resizeWidgetHeightByWindowHeight = function() {
    var windowHeight = $(window).innerHeight(),
      minCurrentWidgetHeight = $scope._getMinWidgetHeight(),
      currentWidgetHeight = $('#titleWrap').height() + $('#descriptionSet').height() + $('#miniTarget').height(),
      maxCurrentWidgetHeight = $scope._getMaxWidgetHeight(),
      delta = windowHeight - $scope.currentWindowHeight;

    if(windowHeight * 0.7 < currentWidgetHeight && delta === 0) {
      delta = (windowHeight * 0.7) - currentWidgetHeight;
    }

    // 変更後サイズ
    var afterWidgetHeight = currentWidgetHeight + delta;
    var changed = false;
    if(delta > 0 && afterWidgetHeight > maxCurrentWidgetHeight) {
      console.log('1 %s', delta);
      changed = true;
      $('#chatTalk').height($scope._getMaxChatTalkHeight());
    } else if(delta < 0 && afterWidgetHeight < minCurrentWidgetHeight) {
      console.log('2-1 %s ', delta, minCurrentWidgetHeight, $scope._getMinChatTalkHeight());
      changed = true;
      $('#chatTalk').height($scope._getMinChatTalkHeight());
      console.log('2-2 %s ', $('#sincloBox').height());
    } else if((delta < 0 && windowHeight * 0.7 < currentWidgetHeight) || (delta > 0 && windowHeight * 0.7 >= afterWidgetHeight)) {
      console.log('3 %s', delta);
      changed = true;
      $('#chatTalk').height($('#chatTalk').height() + delta);
    }
    $scope.currentWindowHeight = windowHeight;

    if(changed) {
      $(document).trigger('onWidgetSizeChanged');
    }
  };

  $scope._getMaxWidgetHeight = function() {
    var offset = $scope._getMessageAreaOffset();
    switch(Number($scope.widgetSizeType)) {
      case 1:
        return 405 - offset;
      case 2:
        return 496 - offset;
      case 3:
        return 596 - offset;
      default:
        return 496 - offset;
    }
  };

  $scope._getMinWidgetHeight = function() {
    var offset = $scope._getMessageAreaOffset();
    switch(Number($scope.widgetSizeType)) {
      case 1:
        return 318 - offset;
      case 2:
        return 364 - offset;
      case 3:
        return 409 - offset;
      default:
        return 364 - offset;
    }
  };

  $scope._getMaxChatTalkHeight = function() {
    var offset = $scope._getMessageAreaOffset(true);
    switch(Number($scope.widgetSizeType)) {
      case 1:
        // 小
        return 194 + offset;
      case 2:
        return 284 + offset;
      case 3:
        return 374 + offset;
      default:
        return 284 + offset;
    }
  };

  $scope._getMinChatTalkHeight = function() {
    var offset = $scope._getMessageAreaOffset(true);
    switch(Number($scope.widgetSizeType)) {
      case 1:
        // 小
        return 97 + offset;
      case 2:
        return 142+ offset;
      case 3:
        return 187 + offset;
      default:
        return 142 + offset;
    }
  };

  $scope._getMessageAreaOffset = function(forChatTalkOffset) {
    var invisibleUIOffset = 0;
    if(!forChatTalkOffset) {
      if(!$('#sincloAccessInfo').is(':visible')) {
        invisibleUIOffset += 26.5;
      }
      invisibleUIOffset +=  53 - $('#descriptionSet').height();
    }
    if(!$('#flexBoxWrap').is(':visible')) {
      // 非表示
      if(forChatTalkOffset) {
        return 75;
      } else {
        return 0 + invisibleUIOffset;
      }
    } else if($('#messageBox').is(':visible')) {
      return 0 + invisibleUIOffset;
    } else if($('#miniFlexBoxHeight').is(':visible')) {
      return 27 + invisibleUIOffset;
    } else {
      // とりあえず表示されている状態
      return 0 + invisibleUIOffset;
    }
  };

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
    var miniElm = document.querySelector('#miniSincloChatMessage');

    if (typeof message === 'undefined' || message == null) {
      elm.placeholder = $scope.placeholder || elm.placeholder;
      miniElm.placeholder = elm.placeholder;
    } else {
      $scope.placeholder = elm.placeholder;
      elm.placeholder = message;
      miniElm.placeholder = elm.placeholder;
    }
  }

  /**
   * 自由入力エリアのキーイベント
   */
  $(document).on('keypress', '#sincloChatMessage,#miniSincloChatMessage', function(e) {
    if (!$scope.allowInputLF && e.key === 'Enter') {
      // ヒアリング：改行不可（Enterキーでメッセージ送信）
      $scope.visitorSendMessage();
      return false;
    } else
    if ($scope.allowSendMessageByShiftEnter && e.key === 'Enter' && e.shiftKey) {
      // ヒアリング：改行可（Shift+Enterキーでメッセージ送信）
      $scope.visitorSendMessage();
      return false;
    }
  });
  /**
   * 自由入力エリアのテキスト入力イベント
   */
  $(document).on('input paste', '#sincloChatMessage,#miniSincloChatMessage', function(e) {
    var targetElm = $(this);
    var inputText = targetElm.val();

    var regex = new RegExp($scope.inputRule);
    var changed = '';
    // 入力された文字列を改行ごとに分割し、設定された正規表現のルールに則っているかチェックする
    var isMatched = inputText.split(/\r\n|\n/).every(function(string) {
      var matchResult = string.match(regex);
      // 入力文字列が適切ではない場合、先頭から適切な文字列のみを取り出して処理を終了する
      if (matchResult === null || matchResult[0] !== matchResult.input) {
        changed += (matchResult === null || matchResult.index !== 0) ? '' : matchResult[0];
        return false;
      }
      changed += string + '\n';
      return true;
    });
    if (!isMatched) {
      targetElm.val(changed);
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
        document.querySelector('#miniSincloChatMessage').value = message;
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
