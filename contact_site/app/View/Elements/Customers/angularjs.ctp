<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']),
    userList = <?php echo json_encode($responderList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>,
    campaignList = <?= $campaignList ?>,
    widget = <?= $widgetSettings ?>, contract = <?= json_encode($coreSettings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
    modalFunc, myUserId = <?= h($muserId)?>, chatApi, cameraApi, entryWordApi;

(function(){

  // -----------------------------------------------------------------------------
  //  関数
  // -----------------------------------------------------------------------------
  function emit(ev, d, callback){
    var obj = {};
    if ( typeof(d) !== "object" ) {
      obj = JSON.parse(d);
    }
    else {
      obj = d;
    }
    obj.siteKey = "<?=$siteKey?>";
    var status = $('#operatorStatus').data('status');
    var data = JSON.stringify(obj);
    socket.emit(ev, data, callback);
  }

  modalFunc = {
    option: {
      url: "",
      tabId: "",
      width: "",
      height: ""
    },
    elm: function(){
      return document.getElemenetById('modalCtrl');
    },
    func: function(option){
      window.open(option.url,
                  option.tabId,
                  "width=" + option.width + ",height=" + option.height +
                  ",dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no,scrollbars=no"
      );
      return false;
    },
    set: function(){
      modalFunc.func(this.option);
    }
  };

  chatApi = {
      connect: false,
      tabId: null,
      sincloSessionId: null,
      userId: null,
      token: null,
      getMessageToken: null,
      shownErrorTabIdList: {},
      messageType: {
        customer: 1,
        company: 2,
        auto: 3,
        sorry: 4,
        autoSpeech: 5,
        sendFile: 6,
        notification: 7,
        linkClick: 8,
        start: 998,
        end: 999,
        scenario: {
          customer: {
            hearing: 12,
            selection: 13,
            sendFile: 19,
            answerBulkHearing: 30,
            noModBulkHearing: 31,
            modifyBulkHearing: 32,
            radio: 33,
            pulldown: 34,
            calendar: 35,
            carousel: 43,
            button: 47,
            reInputText: 36,
            reInputRadio: 37,
            reInputPulldown: 38,
            reInputCalendar: 39,
            reInputCarousel: 44,
            reInputButton: 48,
            buttonUI: 50,
            reInputButtonUI: 51,
            checkbox: 53,
            reInputCheckbox: 54,
            cancel: 90
          },
          message: {
            text: 21,
            hearing: 22,
            selection: 23,
            receiveFile: 27,
            returnBulkHearing: 40,
            pulldown: 41,
            calendar: 42,
            carousel: 45,
            button: 46,
            buttonUI: 49,
            checkbox: 52,
            radio: 55,
          }
        },
        cogmo: {
          message: 81,
          feedback: 82
        },
        diagram: {
          message: {
            branch: 300,
            text: 302
          },
          customer: {
            branch: 301,
            operator: 303
          }
        }
      },
      init: function(sendPattern){
        this.sound = document.getElementById('sinclo-sound');
        if ( this.sound ) {
            this.sound.volume = 0.3;
        }
      },
      prevValue: "",
      observeType: {
        timer: null,
        timeoutTimer: null,
        prevMessage: "",
        status: false,
        cnst: {
          company: 1,
          client: 2
        },
        start: function(){
          var sendMessage = document.getElementById('sendMessage');

          // 300ミリ秒ごとに入力値をチェック
          chatApi.observeType.timer = setInterval(function(){
            // 空になった時
            if ( sendMessage.value === "" ) {
              if ( chatApi.observeType.status !== false || chatApi.observeType.prevMessage !== "" ) {
                chatApi.observeType.prevMessage = "";
                chatApi.observeType.send(false);
              }
            }
            // 内容が変わった場合
            else if ( sendMessage.value !== chatApi.observeType.prevMessage ) {
              // 現在のメッセージを保存
              chatApi.observeType.prevMessage = sendMessage.value;
              // メッセージに変化が合った為、
              chatApi.observeType.send(true);

              // タイマーリセット
              if ( chatApi.observeType.timeoutTimer ) {
                clearTimeout(chatApi.observeType.timeoutTimer);
              }
              // ５秒のタイマーセット
              chatApi.observeType.timeoutTimer = setTimeout(function(){
                // 現在のメッセージを保存
                chatApi.observeType.prevMessage = sendMessage.value;
                // 入力が行われず、５秒たっているので、false
                chatApi.observeType.send(false);
              }, 5000);
            }
          }, 300);
        },
        end: function(){
          clearInterval(chatApi.observeType.timer);
          chatApi.observeType.send(false);
        },
        send: function(status){
          chatApi.observeType.emit(chatApi.tabId, chatApi.sincloSessionId, status);
          chatApi.observeType.status = status;
        },
        prevStatus: false,
        emit: function(tabId, sincloSessionId, status){
          if ( tabId === "" ) return false;
          var sendToCustomer = true;
          if ( this.prevStatus === status ) {sendToCustomer = false};
          this.prevStatus = status;
          var value = "";
          if ( document.getElementById('sendMessage') !== undefined ) {
            value = document.getElementById('sendMessage').value;
          }
          emit('sendTypeCond', {
            type: chatApi.observeType.cnst.company, // company
            tabId: tabId,
            sincloSessionId: sincloSessionId,
            sendToCustomer: sendToCustomer,
            message: value,
            status: status
          });
        }
      },
      sound: null,
      call: function(){
        if (this.sound) {
            this.sound.play();
        }
      },
      connection: function(){
        if ( isset(this.tabId) && isset(this.userId) && isset(this.sincloSessionId)) {
          emit("chatStart", {tabId: this.tabId, userId: myUserId, sincloSessionId: this.sincloSessionId});
        }
      },
      getMessage: function(obj){
        // オートメッセージの取得
        this.getMessageToken = makeToken();
        emit('getAutoChatMessages', {userId: obj.userId, mUserId: myUserId, tabId: obj.tabId, sincloSessionId: obj.sincloSessionId, chatToken: this.getMessageToken});
      },
      openFileUploadDialog: function() {

      },
      addOption: function(type){
        var sendMessage = document.getElementById('sendMessage');
        var focusPosition = $('#sendMessage').get(0).selectionStart;
        switch(type){
          case 1:
          if (sendMessage.value.length == 0) {
            sendMessage.value += "[] ";
          }
          else {
            sendMessage.value = sendMessage.value.substr(0, focusPosition) + "\n" + "[] " + sendMessage.value.substr(focusPosition,sendMessage.value.length);
          }
          sendMessage.focus();
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', focusPosition+4);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, focusPosition+4);
          }
        }
      },
      pushMessageFlg: false,
      pushMessage: function() {
        if ( this.pushMessageFlg ) return false;
        this.pushMessageFlg = true;
        var elm = document.getElementById('sendMessage');
        var noFlg = 0;
        var req = new RegExp(/^\s*$/);
        if ( isset(elm.value) && !req.test(elm.value) ) {
          emit('sendChat', {
            token: this.token,
            tabId: chatApi.tabId,
            sincloSessionId: chatApi.sincloSessionId,
            userId: this.userId,
            chatMessage:elm.value,
            mUserId: myUserId,
            messageType: chatApi.messageType.company,
            messageRequestFlg: noFlg
          });
          elm.value = "";
          chatApi.observeType.send(chatApi.tabId, chatApi.observeType.status);
        }
        this.pushMessageFlg = false;
      },
      errorChatStart: function(){
        $(".errorMsg").remove();
        var span = document.createElement("span");
        span.classList.add('errorMsg');
        span.textContent = "処理に失敗しました。再読み込みしてください。";
        $("#sendMessageArea").append(span);
        this.shownErrorTabIdList[this.tabId] = this.tabId;
      },
      clearErrorChatStart: function() {
        $(".errorMsg").remove();
      },
      isShownErrorChatStart: function(tabId) {
        return typeof(this.shownErrorTabIdList[tabId]) !== 'undefined';
      },
      isReadMessage: function(monitor){
        // フォーカスが入っているもののみ
        if (!$("#sendMessage").is(":focus")) return false;
        // メッセージを既読にする
        if ( isset(monitor.chatUnreadCnt) && monitor.chatUnreadCnt > 0 ) {
          emit('isReadChatMessage', {
            tabId: monitor.tabId,
            sincloSessionId: monitor.sincloSessionId,
            chatId: monitor.chatUnreadId
          });
        }
      },
      forceReadMessage: function(monitor){
        // メッセージを既読にする
        emit('isReadChatMessage', {
          tabId: monitor.tabId,
          sincloSessionId: monitor.sincloSessionId,
          chatId: monitor.chatUnreadId
        });
      }
  };

  cameraApi = {
    connect: function(monitor){
      var tabInfo = JSON.stringify({
        userId: monitor.userId,
        tabId: monitor.tabId
      });
      window.open(
        "<?= $this->Html->url(['controller' => 'Customers', 'action' => 'monitor']) ?>?tabInfo=" + encodeURIComponent(tabInfo),
        "monitor_" + monitor.userId,
        "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
      );
      return false;
    },
    disConnect: function(){

    }
  };

  // API
  entryWordApi = {
    scSize: null,
    init: function(){
      var top = $("#wordList").offset().top;
      this.scSize = {
        max: top + document.getElementById('wordList').clientHeight,
        min: top
      };
      document.getElementById('wordList').scrollTop = 0;
    },
    push: function(str){
        var input = document.getElementById('sendMessage');
        input.value += str; // 文字列を追記
    },
    prev: function(){
        var input = document.getElementById('sendMessage');
        input.focus(); // テキストエリアにフォーカスを当てる
        this.close(); // 選択エリアを非表示に
    },
    close: function(){
        var list = document.getElementById('wordListArea');
        if ( list ) {
            list.style.display = "none";
        }
    },
    scTimer: null,
    scroll: function(now){
        var now_t = now.offset().top;
        var now_b = now_t + now.prop("offsetHeight");
        var sc = $("#wordList").offset().top - $("#wordList > li:first-child").offset().top;
        if ( this.scSize.max < now_b ) { // 下に
          var sc = sc + now.prop("offsetHeight");
          $("#wordList").animate({scrollTop: sc}, 0);
        }
        if ( this.scSize.min > now_t ) { // 上に
          var sc = now_t - $("#wordList > li:first-child").offset().top;
          $("#wordList").animate({scrollTop: sc}, 0);
        }
    }
  };

  function makeDateTime(dParse){
    var d = new Date(Number(dParse)),
        date = d.getFullYear() + '/' + ( '0' + (d.getMonth() + 1) ).slice(-2) + '/' + ('0' + d.getDate()).slice(-2),
        time = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    return date + " " + time;
  }

  /**
   * チャットのスクロール
   */
  var scDownTimer = null;
  function scDown(){
    if ( scDownTimer ) {
      clearTimeout(scDownTimer);
    }
    scDownTimer = setTimeout(function(){
      var chatTalk = document.getElementById('chatTalk');
      $('#chatTalk').animate({
        scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
      }, 300);

    }, 500);
  }

  function scDownImmediate() {
    var chatTalk = document.getElementById('chatTalk');
    $('#chatTalk').animate({
      scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
    }, 300);
  }

  var isNotifyOpened = false;
  function notify(message) {
    var target = $('chat-receiver');
    target.find('#receiveMessage').html(message);
    target.css('display', 'block');
    var targetHeight = target.outerHeight();
    target.css('top',$('#chatTalk').outerHeight() - targetHeight);
    // 指定した高さになるまで、1文字ずつ消去していく
    target.css('display', 'none');
    target.css('display', 'block');
    while((message.length > 0) && (target.find('#receiveMessage').outerHeight() >= targetHeight)) {
      message = message.substr(0, message.length - 1);
      target.find('#receiveMessage').html(message + '...');
    }
    if(!isNotifyOpened) {
      target.css('display', 'none');
      isNotifyOpened = true;
      target.show('fast').off('click').on('click', function (e) {
        isNotifyOpened = false;
        e.stopImmediatePropagation();
        scDownImmediate();
        $(this).hide('fast');
      });
    }
    // スクロールが表示判定とならないところまで来たら消す
    $('#chatTalk').on('scroll', function(e){
      if(!isShowChatReceiver()) {
        isNotifyOpened = false;
        target.hide('fast');
      }
    });
  }

  function isShowChatReceiver() {
    var target = $('#chatTalk');
    return target.find('message-list').height() - target.height() - target.scrollTop() >= 55;
  }

  // http://weathercook.hatenadiary.jp/entry/2013/12/02/062136
  sincloApp.factory('angularSocket', function ($rootScope, $timeout) {
    if(socket) {
      socket.open();
    }
    return {
      on: function (eventName, callback) {
        if ( !window.hasOwnProperty('socket') ) return false;
        socket.on(eventName, function () {
          var args = arguments;
            $rootScope.$apply(function () {
              $timeout(function(){
                callback.apply(socket, args);
              });
            });
        });
      },
      emit: function (eventName, d, callback) {
        var obj = {};
        if ( !window.hasOwnProperty('socket') ) return false;
        if ( typeof(d) !== "object" ) {
          obj = JSON.parse(d);
        }
        else {
          obj = d;
        }
        obj.siteKey = "<?=$siteKey?>";
        var data = JSON.stringify(obj);
        socket.emit(eventName, data, function () {
          var args = arguments;
          $rootScope.$apply(function () {
            if (callback) {
              callback.apply(socket, args);
            }
          });
        });
      }
    };
  });

  sincloApp.controller('MainController', ['$scope', 'angularSocket', '$timeout', function($scope, socket, $timeout) {
    $scope.searchText = "";
    $scope.chatMessage = "";
    $scope.oprCnt = 0; // 待機中のオペレーター人数
    $scope.oprWaitCnt = 0; // 総オペレーター人数
    $scope.labelHideList = <?php echo json_encode($labelHideList) ?>;
    $scope.monitorList = {};
    $scope.tmpMonitorList = [];
    $scope.requestedCustomerList = [];
    $scope.customerList = {};
    $scope.messageList = [];
    $scope.chatOpList = [];
    $scope.chatList = [];
    $scope.displayCustomerInfoSettingMap = <?php echo json_encode($customerInfoDisplaySettingMap, JSON_UNESCAPED_UNICODE) ?>;
    $scope.typingMessageSe = "";
    $scope.typingMessageRe = {};
    $scope.uploadProgress = 0;
    $scope.scInfo = { remain: 0 };
    $scope.activeOperatorList = {};
    $scope.onlineOperatorList = {};
    $scope.chatLogList = []; // 詳細情報のチャットログリスト
    $scope.chatLogMessageList = []; // 詳細情報のチャットログメッセージリスト
    $scope.operatorList = <?= json_encode($userList) ?>;
    $scope.operatorListSortMode = 'status';
    $scope.operatorListSortOrder = 'desc';
    $scope.pollingModeIntervalTimer = null;
    $scope.chatReceived = false;
    $scope.firstLoadMonitorList = true;

    /* 資料検索 */
    $scope.tagList = {};
    $scope.documentList = {};
    /* 資料検索 */

    /* 定数 */
    $scope.jsConst = {
      tabInfo: <?php echo json_encode($tabStatusList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>,  // タブ状態の定数
      tabInfoStr: <?php echo json_encode($tabStatusStrList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>, // タブ状態の定数
      tabInfoNotificationMessage: <?php echo json_encode($tabStatusNotificationMessageList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?> // タブ状態の通知用メッセージ
    };

    $scope.beforeInputValue = '';
    $scope.searchResult = [];
    $scope.search = function(array, forceResult){
      var isHideRealTimeMonitor = (contract.hideRealtimeMonitor);
      var result = {}, targetField;
      targetField = ( Number($scope.fillterTypeId) === 2 ) ? 'ipAddress' : 'accessId';
      if(isHideRealTimeMonitor) {
        if (forceResult) {
          $scope.searchResult = array;
          result = array;
        } else if ($scope.searchText.length >= 4 && $scope.searchText !== $scope.beforeInputValue) {
          $scope.searchProcess($scope.searchText, $scope.fillterTypeId);
          if(!contract.monitorPollingMode) {
            $scope.monitorList = [];
            $scope.chatList = [];
            result = [];
          } else {
            result = array;
          }
        } else if ($scope.searchText.length >= 4 && $scope.searchResult.length > 0) {
          result = $scope.monitorList;
        } else if($scope.searchText.length < 4) {
          if (!contract.monitorPollingMode || !contract.enableRealtimeMonitor) {
            $scope.searchResult = [];
            if ($scope.monitorList.length > 0) {
              $scope.monitorList = [];
            }
            if ($scope.chatList > 0) {
              $scope.chatList = [];
            }
            result = [];
          } else {
            result = array;
          }
        }
      } else if (!contract.enableRealtimeMonitor) {
        if (forceResult) {
          $scope.searchResult = array;
          result = array;
        } else if ($scope.searchText.length >= 4 && $scope.searchText !== $scope.beforeInputValue) {
          $scope.searchProcess($scope.searchText, $scope.fillterTypeId);
          result = array;
        } else if ($scope.searchText.length >= 4 && $scope.searchResult.length > 0) {
          result = $scope.monitorList;
        } else if ($scope.searchText.length < 4) {
          var targetArray = [];
          Object.keys($scope.monitorList).forEach(function(elm, idx, arr) {
            if (!$scope.monitorList[elm].isSearchTarget) {
              targetArray.push($scope.monitorList[elm]);
            }
          });
          result = targetArray;
        }
      } else {
        if ( $scope.searchText ) {
          if(isHideRealTimeMonitor && $scope.searchText.length < 3 ) {
            // 何もしない
          } else {
            angular.forEach(array, function(value, key) {
              if ( value[targetField].indexOf($scope.searchText) === 0) {
                result[key] = value;
              }
            });
          }
        } else if(isHideRealTimeMonitor && !contract.monitorPollingMode) {
          // 検索状態じゃない場合で通常時リアルタイムモニタ非表示であれば非表示にする
        } else {
          result = array;
        }
      }
      $scope.beforeInputValue = $scope.searchText;
      return result;
    };

    // 検索用
    $scope.searchProcess = function(term, filterType) {
      emit('searchCustomer', {
        term: term,
        filterType: filterType
      });
    };

    socket.on('searchCustomerResult', function(result){
      var obj = JSON.parse(result);
      $scope.search(obj, true);
      if(obj && obj.length > 0) {
        obj.forEach(function(targetObj) {
          targetObj.isSearchTarget = true;
          pushToList(targetObj);
          if ('chat' in targetObj && String(targetObj.chat) === "<?=$muserId?>") {
            pushToChatList(targetObj.tabId);
          }
        });
      }
    });

    $scope.$watch('searchWord', function(n,o){
      if ( n !== o ) {
        $scope.entryWord = 0;
      }
    });

    $scope.searchTextPlaceholder = function(){
      return (Number($scope.fillterTypeId) === 1) ? "ID" : "訪問ユーザ";
    };

    $scope.openSetting = function(){
      $.ajax({
        type: 'GET',
        url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteCreateSetting')) ?>",
        data: {
          labelHideList: JSON.stringify($scope.labelHideList)
        },
        dataType: 'html',
        cache: false,
        success: function(html){
          modalOpen.call(window, html, 'p-cus-menu', '表示項目の設定');

          popupEvent.closePopup = function(){
            var retList = {};
            $('#labelHideList option').each(function(e){
              retList[this.value] = this.selected;
            });

            $.ajax({
              type: 'GET',
              url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteSaveSetting')) ?>",
              data: {
                labelHideList: JSON.stringify(retList)
              },
              dataType: 'json',
              success: function(json){
                $scope.updateList(retList);
                modalClose();
              }
            });
          };
        }
      });
    };

    $scope.nn = function(tabId){
      var res = 1;
      if($scope.monitorList[tabId]) {
        var num = $scope.monitorList[tabId].stayCount;
        if (angular.isNumber(num) && Number(num) > 0) {
          res = num;
        }
        $scope.monitorList[tabId].stayCount = res;
      }
      return res;
    };

    $scope.updateList = function(retList){
      $scope.labelHideList = retList;
      $scope.$apply();
    };

    $scope.os = function(str){
      return userAgentChk.os(str);
    };

    $scope.browser = function(str){
      return userAgentChk.browser(str);
    };

    $scope.ua = function(str){
      return userAgentChk.pre(str);
    };

    $scope.ip = function(m){
      var showData = [];
      if(contract.refCompanyData && 'orgName' in m && m.orgName !== '' && ($scope.isViewable() || !$scope.isML(m))) {
        showData.push('(' + m.ipAddress + ')'); // IPアドレス
      } else {
        m.lbcCode = "";
        m.orgName = "";
        showData.push(m.ipAddress); // IPアドレス
      }
      return showData.join("\n");
    };

    $scope.isViewable = function() {
      return <?= var_export($viewableMLCompanyInfo, TRUE) ?>;
    }

    $scope.isML = function(m) {
       return ((m.hasOwnProperty('lbcCode') && m.lbcCode === '10102363864'));
    }

    $scope.ui = function(m){
      var showData = [];
      if ( $scope.customerList.hasOwnProperty(m.userId) && isset($scope.customerList[m.userId]) ) {
        var c = $scope.customerList[m.userId];
        try {
          Object.keys($scope.displayCustomerInfoSettingMap).forEach(function (elm, index, array) {
            if(!$scope.displayCustomerInfoSettingMap[elm]) return;
            if ( (elm in c) && c[elm].length > 0 ) {
              showData.push(c[elm]);
            }
          });
        } catch(e) {

        }
      }
      return showData.join("\n");
    };

    $scope.windowOpen = function(tabId, accessId){
      var message = "アクセスID【" + accessId + "】のユーザーに接続しますか？<br><br>";
      var ua = $scope.monitorList[tabId].userAgent.toLowerCase();
      var smartphone = (ua.indexOf('iphone') > 0 || ua.indexOf('ipod') > 0 || ua.indexOf('android') > 0);
      var popupClass = "p-cus-connection";
      message += "<span style='color: #FF7B7B'><?=Configure::read('message.const.chatStartConfirm')?></span>";
      modalOpen.call(window, message, popupClass, 'メッセージ');
       popupEvent.closePopup = function(type){
          sessionStorage.clear();
          popupEvent.close();
          connectToken = makeToken();
          socket.emit('requestWindowSync', {
            tabId: tabId,
            type: type,
            connectToken: connectToken
          });
          // モニター開始時にビデオ表示
          // TODO: ビデオ表示可能な条件をつける。（オプションでビデオチャット可能で、かつユーザーがカメラONにしているとき）
        <?php if (isset($coreSettings[C_COMPANY_USE_VIDEO_CHAT]) && $coreSettings[C_COMPANY_USE_VIDEO_CHAT]) : ?>
          // socket.emit('confirmVideochatStart', {toTabId: tabId, connectToken: connectToken, receiverID: connectToken+'_vc'});
        <?php endif; ?>
       };
    };

    /**************************************************************
     *  資料共有　ここから
     * ************************************************************/

    $scope.docShareId = null;
    $scope.createTimer=null;
    $scope.documentOpen = function(tabId, accessId){
      $scope.docShareId = null;
      $.ajax({
        type: 'GET',
        url: '<?=$this->Html->url(["controller" => "Customers", "action" => "remoteOpenDocumentLists"])?>',
        dataType: 'json',
        success: function(json) {
          $scope.docShareId = tabId;
          $scope.searchName = "";
          $("#ang-popup").addClass("show");
          var contHeight = $('#ang-popup-content').height();
          $('#ang-popup-frame').css('height', contHeight);
          $scope.message = "アクセスID【" + accessId + "】のユーザーと資料共有を開始します。\n共有する資料を選択してください。";
          $scope.tagList = ( json.hasOwnProperty('tagList') ) ? JSON.parse(json.tagList) : {};
          $scope.documentList = ( json.hasOwnProperty('documentList') ) ? JSON.parse(json.documentList) : {};
          $scope.$apply();
        }
      });
    };

    $scope.setDocThumnailStyle = function(doc) {
      var matrix = "";
      if ( doc.hasOwnProperty('settings') ) {
        var settings = JSON.parse(doc.settings);
        if ( settings.hasOwnProperty('rotation') && isNumber(settings.rotation) ) {
          matrix = "rotate" + settings.rotation;
        }
      }
      return matrix;
    };

    $scope.selectList = {};
    $scope.searchName = "";
    $scope.docSearchFunc = function(documentList){
      var targetTagNum = Object.keys($scope.selectList).length;
      if (Object.keys(documentList).length === 0) return {};

      function check(elem, index, array){
        var flg = true;
        if ( elem.tag !== "" && elem.tag !== null ) {
          elem.tags = $scope.jParse(elem.tag);
        }
        if ( $scope.searchName === "" && targetTagNum === 0 ) {
          return elem;
        }

        if ( $scope.searchName !== "" && (elem.name + elem.overview).indexOf($scope.searchName) < 0 ) {
          flg = false;
        }

        if ( flg && targetTagNum > 0 ) {
          var selectList = Object.keys($scope.selectList);
          flg = true;
          for ( var i = 0; selectList.length > i; i++ ) {
            if ( elem.tags.indexOf(Number(selectList[i])) === -1 ) {
              flg = false;
            }
          }
        }

        return ( flg ) ? elem : false;

      }

      return documentList.filter(check);
    };

    /**
     * [shareDocument description]
     * @param  {object} doc documentInfo
     * @return {void}     open new Window.
     */
    $scope.shareDocument = function(doc) {
      $scope.closeDocumentList();
      clearInterval($scope.createTimer);
      $scope.tabId = $scope.docShareId;
      $("#popup-bg").css("background-color","rgba(0, 0, 0, 0.0)");
      $('#afs-popup').show();
      $("#afs-popup").addClass("show");
      $('#afs-popup-frame').css('height', $('#popup-frame').height());
      this.notFirstTime = true;
      $scope.message = "お客様に共有の許可を求めています。";
      $scope.title = "共有申請中";
      $scope.createTimer = setInterval(function () {
        if ($scope.title.length > 7) {
          $scope.title = "共有申請中";
          $scope.$apply();
        }
        else {
          $scope.title　+= '・';
          $scope.$apply();
        }
      }, 500);

      var settings = JSON.parse(doc.settings);
      var rotation = (settings.hasOwnProperty('rotation')) ? settings.rotation : 0;
      socket.emit('docShareConnect', {
        id: doc.id,
        from: 'company',
        responderId: '<?=$userInfo["id"]?>',
        directory: "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>",
        fileName: doc.file_name,
        pagenation_flg: doc.pagenation_flg,
        pages: settings.pages,
        rotation: rotation,
        download_flg: doc.download_flg,
        tabId: $scope.docShareId,
        popup:'true'
      });
    };

    $scope.closeDocumentList = function() {
      $("#ang-popup").removeClass("show");
    };

    /**************************************************************
     *  資料共有　ここまで
     * ************************************************************/

    /**************************************************************
     *　画面共有（LA）
     * ************************************************************/
    $scope.coBrowseOpen = function(tabId, accessId){
        var message = "アクセスID【" + accessId + "】のユーザーに接続しますか？<br><br>";
        var ua = $scope.monitorList[tabId].userAgent.toLowerCase();
        var smartphone = (ua.indexOf('iphone') > 0 || ua.indexOf('ipod') > 0 || ua.indexOf('android') > 0);
        var popupClass = "p-cus-connection";
        message += "<span style='color: #FF7B7B'><?=Configure::read('message.const.chatStartConfirm')?></span>";
        modalOpen.call(window, message, popupClass, 'メッセージ');
        popupEvent.closePopup = function(type){
            coBrowseConnectToken = makeToken();
            socket.emit('requestCoBrowseOpen', {
                tabId: tabId,
                type: type,
                coBrowseConnectToken: coBrowseConnectToken
            });
        };
    };




    $scope.confirmSharingWindowOpen = function(tabId, accessId) {
      /*START:ポップアップブロックの判定処理*/
      //○○共有を使えないのであれば判定自体を行わない
      var popupblock;
      var dummywindow = window.open('','','width = 1,height = 1,top = 20000,left = 20000');
      dummywindow.close();
      var judgewindow = window.open('','','width = 1,height = 1,top = 20000,left = 20000');
      if(judgewindow == null){
        popupblock = true;
        message="ポップアップがブロックされているため、共有機能が利用できません。<br>"
               +"<br>共有機能を利用される場合は<span class='red_font'>ポップアップの設定を「許可」に変更</span>して下さい。"
               +"<br><br><a target='_blank' href='https://info.sinclo.jp/manual/ポップアップブロック解除方法/'>設定方法はこちら</a>";
        modalOpen.call(window, message,"p-cus-block-popup",'ポップアップの設定', 'fade', '');
      }else{
        popupblock = false;
        judgewindow.close();
      }
      if(popupblock){
        return;
      }
      /*FINISH:ポップアップブロックの判定処理*/

      var message = "アクセスID【" + accessId + "】のユーザーと共有するモードを選択してください。<br><br>";
      var ua = $scope.monitorList[tabId].userAgent.toLowerCase();
      var smartphone = (ua.indexOf('iphone') > 0 || ua.indexOf('ipod') > 0 || ua.indexOf('android') > 0);
      var popupClass = "p-cus-select-sharing-mode";
      message += "<span style='color: #FF7B7B'><?=Configure::read('message.const.chatStartConfirm')?></span>";
      modalOpen.call(window, message, popupClass, 'メッセージ', null, $scope.monitorList[tabId].userAgent);
      popupEvent.closePopup = function(type) {
        switch(type) {
          case 1: // ブラウジング共有
            sessionStorage.clear();
            $('.popup-on').addClass('popup-off').removeClass('popup-on');
            connectToken = makeToken();
            socket.emit('requestWindowSync', {
              tabId: tabId,
              type: type,
              connectToken: connectToken
            });
            $scope.sharingApplicationOpen(tabId, accessId);
            break;
          case 2: // 画面キャプチャ共有
            coBrowseConnectToken = makeToken();
            $('.popup-on').addClass('popup-off').removeClass('popup-on');
            socket.emit('requestCoBrowseOpen', {
              tabId: tabId,
              type: type,
              coBrowseConnectToken: coBrowseConnectToken
            });
            $scope.sharingApplicationOpen(tabId, accessId);
            break;
          case 3: // 資料共有
            modalClose();
            $scope.documentOpen(tabId, accessId);
            break;
          default:
            break;
        }
      };
    };

    $scope.closeSharingApplication = function(tabId) {
      $("#afs-popup").hide();
      $("#cs-popup").addClass("show");
      var contHeight = $('#cs-popup-content').height();
      $('#cs-popup-frame').css('height', contHeight);
      document.getElementById('cs-popup-frame').style.top = (window.innerHeight) + "px";
      $('#cs-popup-frame').animate(
          {
            top: 0
          },
          500,
          function () {
            $('body').css('overflow', 'auto');
          }
        );
      socket.emit('cancelSharing', {
        tabId: tabId
      });
    };

    $scope.closeCanselSharingApplication = function() {
      $("#cs-popup").removeClass("show");
      $("#afs-popup").hide();
    };

    notFirstTime: null,
    $scope.closeSharingRejection = function() {
      $("#rsh-popup").removeClass("show");
      $("#afs-popup").hide();
    };

    $scope.sharingApplicationOpen = function(tabId, accessId){
      clearInterval($scope.createTimer);
      $scope.tabId = tabId;
      $scope.accessId = accessId;
      $("#popup-bg").css("background-color","rgba(0, 0, 0, 0.0)");
      $('#afs-popup').show();
      $("#afs-popup").addClass("show");
      $('#afs-popup-frame').css('height', $('#popup-frame').height());
      this.notFirstTime = true;
      $scope.message = "お客様に共有の許可を求めています。";
      $scope.title = "共有申請中";
      $scope.createTimer = setInterval(function () {
        if ($scope.title.length > 7) {
          $scope.title = "共有申請中";
          $scope.$apply();
        }
        else {
          $scope.title　+= '・';
          $scope.$apply();
        }
      }, 500);
      $scope.$apply();
    };

    // 成果表示チェック
    $scope.showAchievement = function (){
      return ( isset($scope.detailId) && (typeof($scope.detail) === "object" && Object.keys($scope.detail).length > 0) && $scope.chatOpList.indexOf(myUserId) > -1 );
    };

    // 成果登録
    $scope.changeAchievement = function (){
      if ( isset($scope.detailId) && (typeof($scope.detail) === "object" && Object.keys($scope.detail).length > 0) && $scope.chatOpList.indexOf(myUserId) > -1 ) {
        var monitor = angular.copy($scope.monitorList[$scope.detailId]);
        var chatList = angular.copy($scope.messageList);
        var chatId = null;
        for (var i = chatList.length - 1; i >= 0; i--) {
          if ( Number(chatList[i].messageType) === chatApi.messageType.start ) {
            if ( chatList[i].hasOwnProperty('id') && Number(chatList[i].userId) === Number(myUserId) ) {
              $.ajax({
                type: 'GET',
                cache: false,
                url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteChangeAchievement')) ?>",
                data: {
                  chatId: chatList[i].id,
                  userId: myUserId,
                  value: $scope.achievement,
                },
                dataType: 'json',
                success: function(json){
                }
              });
              break;
            }
          }
        }
      }
    };

    $scope.openCompanyDetailInfo = function(monitor){
      if(!contract.refCompanyData) return false;
      var retList = {};
      $.ajax({
        type: 'POST',
        cache: false,
        url: "<?= $this->Html->url(array('controller' => 'CompanyData', 'action' => 'getDetailInfo')) ?>",
        data: JSON.stringify({
          accessToken: "<?=$token?>",
          lbc: monitor.lbcCode,
          format: 'popupElement'
        }),
        dataType: 'html',
        success: function(html){
          modalOpen.call(window, html, 'p-cus-company-detail', '企業詳細情報');
        }
      });
    };

    $scope.showOperatorPresence = function(event) {
      // デフォルトの挙動は抑止する
      event.preventDefault();
      // ポップアップを閉じる
      if ( $scope.presenceMainClass !== "" ) {
        $("#operator_presence_pop").css("display", "none");
            $scope.presenceMainClass = "";
      }
      // ポップアップを開く
      else {
        $scope.presenceMainClass = "showPresence";
        setPositionOfPresenceView(); // ポップアップの位置調整
        setTimeout(function(){
          $("#operator_presence_pop").css("display", "block");
          $('#presenceTableWrap').css('height', $('#presenceViewheader').height() + $('#presenceViewBodyScroll').height() + 1 + "px");
          setHeaderSizeOfPresenceView();
        }, 10);
      }
    };

    $scope.closeOperatorPresence = function() {
      $("#operator_presence_pop").css("display", "none");
      $scope.presenceMainClass = "";
    }

    $scope.changeDisplaySortMode = function() {
      if($scope.operatorListSortMode !== 'displayName') {
        $scope.operatorListSortMode = 'displayName';
        $scope.operatorListSortOrder = 'asc';
      } else {
        if($scope.operatorListSortOrder === 'asc') {
          $scope.operatorListSortOrder = 'desc';
        } else {
          $scope.operatorListSortOrder = 'asc';
        }
      }
    }

    $scope.changeStatusSortMode = function() {
      if($scope.operatorListSortMode !== 'status') {
        $scope.operatorListSortMode = 'status';
        $scope.operatorListSortOrder = 'desc';
      } else {
        if($scope.operatorListSortOrder === 'asc') {
          $scope.operatorListSortOrder = 'desc';
        } else {
          $scope.operatorListSortOrder = 'asc';
        }
      }
    }

    $scope.customerMainClass = "";
    $scope.presenceMainClass = "";

    $scope.refreshUserPresences = function() {
      if($('#presenceView').length !== 0) {
        Object.keys($scope.operatorList).forEach(function(key){
          delete $scope.operatorList[key].status;
        });

        if($scope.onlineOperatorList) {
          Object.keys($scope.onlineOperatorList).forEach(function(key){
            if(!$scope.operatorList[key]) {
              return;
            }
            if($scope.activeOperatorList[key]) {
              $scope.operatorList[key].status = 1;
            } else {
              $scope.operatorList[key].status = 0;
            }
            $('#offline'+key).css('display','none');
          });
        }
      }
    }

    $scope.openHistory = function(monitor){
        var retList = {};
        $.ajax({
          type: 'GET',
          cache: false,
          url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteGetStayLogs')) ?>",
          data: {
            visitorsId: monitor.userId,
            tabId: monitor.sincloSessionId
          },
          dataType: 'html',
          success: function(html){
            modalOpen.call(window, html, 'p-cus-history', 'ページ移動履歴');
          }
        });
    };

    $scope.chatOptionDisabled = function(detailId){
      if (!isset(detailId) ) return false;
      return ( typeof($scope.detail) === "object" && $scope.detail.hasOwnProperty('chat') && Number($scope.detail.chat) === Number(myUserId));
    };

    $scope.objCnt = function(list){
      if ( angular.isUndefined(list) ) return 0;
      var ret = Object.keys(list);
      return ret.length;
    };

    $scope.confirmFlg = false;
    $scope.sendMessageConnectConfirm = function(detailId){
        var monitor = $scope.monitorList[detailId], message = "";
        if ( $scope.confirmFlg || !isset(monitor)) return false;
        $scope.confirmFlg = true;

        // 対応者切替の場合
        if ( (monitor.chat in userList) && monitor.chat !== myUserId ) {
          message = "現在 " + userList[monitor.chat] + "さん が対応中です。<br><br>";
          message += "対応者を切り替えますか？";
        }
        else if ( (monitor.chat in userList) && monitor.chat === myUserId ) {
            // 既読にする
            chatApi.isReadMessage($scope.monitorList[detailId]);
            $scope.confirmFlg = false;
            return false;
        }

      <?php if ( $widgetCheck ) { ?>
        if ( String($('#changeOpStatus').data('status')) !== "<?=C_OPERATOR_ACTIVE?>" ) {
          if ( message !== "" ) {
            message += "<br><br>※ 入室すると、ステータスが『待機中』となります。";
          }
          else {
            message = "入室すると、ステータスが『待機中』となります。<br>入室しますか？";
          }
        }
      <?php } ?>

        if ( message === "" ) {
          $scope.ngChatApi.connect();
          $scope.confirmFlg = false;
          return true;
        }
        else {
          $("#sendMessage").blur();
          $scope.achievement = "";
          modalOpen.call(window, message, 'p-confirm', 'メッセージ');
          popupEvent.closeNoPopup = function(){
            $scope.confirmFlg = false;
            $scope.chatPsFlg = true; // PlaceHolderの表示
            popupEvent.close();
            return true;
          };
          popupEvent.closePopup = function(){
              popupEvent.close();
              $("#sendMessage").focus();
              $scope.ngChatApi.connect();
              $("#sendMessage").val("").focus();
                  //離席中→待機中に変化する場合にチャット通知設定の状態を見る
                  if($('#changeOpStatus').data('status') == 0){

                    <?php if(isset($coreSettings[C_COMPANY_USE_CHAT]) && $coreSettings[C_COMPANY_USE_CHAT]): ?>

                      //通知設定が許可されていない場合は警告を出す
                    var userAgent = window.navigator.userAgent.toLowerCase();
                    if(document.cookie.indexOf('block_notify=true') == -1){
                      if(userAgent.indexOf('msie') != -1 || userAgent.indexOf('trident') != -1){
                        message = "通知の表示がブロックされているため、新着チャットを<br>受信した際にデスクトップ通知が表示されません。<br>"
                                + "<br>チャット受信時の通知を受け取りたい場合は<br><span class='red_font'>通知の設定を「許可」に変更</span>してください。"
                                + "<br><br><a target='_blank' href='https://info.sinclo.jp/manual/デスクトップ通知のブロック解除方法/'>設定方法はこちら</a>"
                                + "<br><br><label for='block_notify_chat'><input type='checkbox' id='block_notify_chat' onclick='notify_cookie()'>"
                                + "今後、このメッセージを表示しない</label>";
                        setTimeout(function(){
                          modalOpen.call(window, message,"p-cus-block-notify",'通知の表示', 'fade', '');
                        },505);
                      }else if(Notification.permission !== "granted"){
                        message = "通知の表示がブロックされているため、新着チャットを<br>受信した際にデスクトップ通知が表示されません。<br>"
                                + "<br>チャット受信時の通知を受け取りたい場合は<br><span class='red_font'>通知の設定を「許可」に変更</span>してください。"
                                + "<br><br><a target='_blank' href='https://info.sinclo.jp/manual/デスクトップ通知のブロック解除方法/'>設定方法はこちら</a>"
                                + "<br><br><label for='block_notify_chat'><input type='checkbox' id='block_notify_chat' onclick='notify_cookie()'>"
                                + "今後、このメッセージを表示しない</label>";
                        setTimeout(function(){
                          modalOpen.call(window, message,"p-cus-block-notify",'通知の表示', 'fade', '');
                        },505);
                      }
                    }

                    <?php endif; ?>
                  }
              if ( String($('#changeOpStatus').data('status')) !== "<?=C_OPERATOR_ACTIVE?>" ) {
                chgOpStatus(); // 在席ステータスにする
              }
              $scope.confirmFlg = false;
              return true;
          };
        }

    };

    $scope.confirmDisConnect = function(tabId, sincloSessionId){
      modalOpen.call(window, 'チャットを終了してもよろしいでしょうか？', 'p-cus-detail', '操作確認');
      // チャットを終了する
      popupEvent.closePopup = function(){
        $scope.ngChatApi.disConnect(tabId, sincloSessionId); // チャットを終了する
        popupEvent.close(); // モーダルを閉じる
      };
    };

    $scope.showDetail = function(tabId, sincloSessionId){
      $("#sendMessage").attr('value', '');
      // エラーはとりあえず消す
      chatApi.clearErrorChatStart();
      if(chatApi.isShownErrorChatStart(tabId)) {
        // 既にエラー表示済みであれば再表示
        chatApi.errorChatStart();
      }
      // ポップアップを閉じる
      if ( $scope.customerMainClass !== "" ) {
        $("#customer_sub_pop").css("display", "none");
        $scope.customerMainClass = "";
        $scope.detailId = "";
        $scope.sincloSessionId = "";
        if ( contract.chat ) {
          $scope.typingMessageSe = "";
          $scope.achievement = "";
          $scope.messageList = [];
          chatApi.userId = "";
          chatApi.observeType.emit(chatApi.tabId, chatApi.sincloSessionId, false);
          $("#chatTalk message-list").children().remove();
        }

        $("#customer_list tr.on").removeClass('on');

        if ( chatApi.tabId !== tabId ) {
          window.setTimeout(function(){
            $scope.showDetail(tabId, sincloSessionId);
          }, 300);
        }
        chatApi.tabId = "";
        chatApi.sincloSessionId = "";
        // ポップアップを開く
      }
      else {
        setPositionOfPopup(); // ポップアップの位置調整
        $scope.customerMainClass = "showDetail";
        $scope.detailId = tabId;
        $scope.sincloSessionId = sincloSessionId;
        chatApi.tabId = tabId;
        chatApi.sincloSessionId = sincloSessionId;
        // チャット契約の場合
        if ( contract.chat ) {
          chatApi.token = makeToken(); // トークンを発行
          // チャットメッセージ取得
          $scope.getChatMessage(tabId); // チャットメッセージを取得
          chatApi.userId = $scope.monitorList[tabId].userId;
          $("#monitor_" + tabId).addClass('on'); // 対象のレコードにクラスを付ける
          // チャットエリアに非表示用のクラスを付ける
          $scope.checkChatArea();
          // 過去履歴
          $scope.chatLogList = [];
          $scope.chatLogMessageList = [];
          angular.element("#showChatTab > li").removeClass("on");
          $("#showChatTab > li[data-type='currentChat']").addClass("on");
          $("#chatContent > section").removeClass("on");
          $("#chatContent > #currentChat").addClass("on");
        }
        setTimeout(function(){
          $("#customer_sub_pop").css("display", "block");
        }, 10);
      }
    };

    // チャットメッセージを取得する
    $scope.getChatMessage = function(tabId){
      var data = $scope.monitorList[tabId];
      chatApi.getMessage(data); // Nodeサーバーより最新のチャットメッセージを取得
      // 新着チャットチェック
      // 3秒後にチェック
      setTimeout(function() {
        // タブを開いている、表示チャットが０件
        if ( $scope.detailId === tabId && $scope.messageList.length === 0 ) {
          // HTTPサーバーより最新のチャットメッセージを取得
          $.ajax({
            type: "GET",
            url: "<?=$this->Html->url(['controller' => 'Customers', 'action' => 'remoteGetHistoriesId'])?>",
            data: {tabId: data.sincloSessionId},
            dataType: "json",
            success: function(ret){
              if ( isset(ret) ) return $scope.getOldChat(ret, false);
            }
          });
        }
      }, 5000);
    };

    $scope.checkChatArea = function(){
      $scope.chatAreaShowFlg = true;
      //if ( !(isset($scope.detailId) && $scope.monitorList.hasOwnProperty($scope.detailId)) ) return false;
      $("#chatContent").addClass("connectChat");
      // チャット対応上限が有効かつ、自身が担当していない場合
      if ( "<?=$scFlg?>" === "<?=C_SC_ENABLED?>" && !(isset($scope.monitorList[$scope.detailId])) || !(isset($scope.monitorList[$scope.detailId].chat) && $scope.monitorList[$scope.detailId].chat === myUserId) ) {
<?php if($widgetCheck){ /* 在席・離席管理の場合 */ ?>
        // 在席中かつ、上限に達している場合
        if ( String($('#changeOpStatus').data('status')) === "<?=C_OPERATOR_ACTIVE?>" && Number($scope.scInfo.remain) < 1 ) {
          $scope.chatAreaShowFlg = false; // ウィジェットを隠す
        }
<?php } else { ?>
        // 上限に達している場合
        if ( Number($scope.scInfo.remain) < 1 ) {
          $scope.chatAreaShowFlg = false; // ウィジェットを隠す
        }
<?php } ?>
        $("#chatContent").removeClass("connectChat");
      }
    };

    $scope.changeSetting = function(type){
      var settings = {
        type: type,
        value : ''
      };
      settings.value = $scope.settings[type];
      $.ajax({
        type: 'post',
        data: settings, // type:1 => type, type:2 => type, id
        dataType: 'json',
        cache: false,
        url: "<?= $this->Html->url('/Customers/remoteChageSetting') ?>",
        success: function(json){
        }
      });
    };

    $scope.openDetailFlg = false;
    $scope.openDetail = function(){
      if ( angular.element('#customer_detail').is(".close") ) {
        var height = $("#customer_detail ul").height() + 90;
        angular.element('#customer_detail').removeClass("close").addClass("open").css('height', height);
        $scope.openDetailFlg = true;
      }
      else {
        angular.element('#customer_detail').removeClass("open").addClass("close");
        $scope.openDetailFlg = false;
      }
    }

    $scope.getCustomerInfoFromMonitor = function(m){
      $scope.customerList[m.userId] = m.customerInfo;
      $scope.requestedCustomerList.push(m.userId);
    };

    // 顧客の詳細情報を取得する
    $scope.getCustomerInfo = function(userId, callback){
      $timeout(function(){
        if($scope.customerList[userId]){
          callback($scope.customerList[userId]);
        } else {
          callback({});
        }
      });
    };

    $scope.getCustomerInfoFromApi = function(userId, callback) {
      $.ajax({
        type: "POST",
        url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'remoteGetCusInfo'])?>",
        data: {
          v:  userId
        },
        dataType: "json",
        success: function(json){
          var ret = {};
          if ( typeof(json) !== "string" ) {
            ret = json;
          }
          callback(ret);
        }
      });
    };

    // 顧客の詳細情報を取得する
    $scope.getOldChat = function(historyId, oldFlg, event){
      if(event !== undefined) {
        //過去のチャットを選択したとき
        $('.pastChatShowBold').css('cssText', 'background-color: #fff');
        $(event.currentTarget).css('cssText', 'background-color: rgb(235, 246, 249)!important');
      }
      $scope.chatLogMessageList = [];
      $.ajax({
        type: "GET",
        url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'remoteGetOldChat'])?>",
        data: {
          historyId:  historyId
        },
        dataType: "json",
        success: function(json){
          if ( oldFlg ) { // 過去チャットの場合
            angular.element("message-list-descript").attr("class", "off");
            $scope.chatLogMessageList = json;
            $scope.$apply();
          }
          else {
            $scope.messageList = json;
          }
        }
      });
    };

    /* タブ状態を文字列で返す */
    $scope.tabStatusStr = function (tabId){
      var n = ($scope.monitorList.hasOwnProperty(tabId)) ? $scope.monitorList[tabId].status : <?=C_WIDGET_TAB_STATUS_CODE_OUT?>;
      return $scope.jsConst.tabInfoStr[n];
    };

    /* タブ状態の通知用メッセージを返す */
    $scope.tabStatusNotificationMessage = function (tabId){
      var n = ($scope.monitorList.hasOwnProperty(tabId)) ? $scope.monitorList[tabId].status : <?=C_WIDGET_TAB_STATUS_CODE_OUT?>;
      changeNotificationStyleByTabStatus(n);
      return $scope.jsConst.tabInfoNotificationMessage[n];
    };

    var changeNotificationStyleByTabStatus = function(status) {
      var target = $('chat-notificate');
      switch(status) {
        case 1: // ウィジェットが開いている状態
          target.css('display','none');
          break;
        case 2: // ウィジェットが閉じている状態
          target.css('display','block');
          target.css('background-color','rgba(246, 171, 0, 0.75)');
          break;
        case 3: // ウィジェットが非表示
          target.css('display','block');
          target.css('background-color','rgba(137, 137, 137, 0.75)');
          break;
        case 4: // ウィンドウ非アクティブ
          target.css('display','block');
          target.css('background-color','rgba(181, 181, 182, 0.75)');
          break;
        case 5: // ページ離脱
          target.css('display','block');
          target.css('background-color','rgba(223, 131, 131, 0.5)');
          break;
        default:
          break;
      }
    }

    /* キャンペーン情報を取得する */
    $scope.getCampaign = function (prev){
      var str = "";
      if (!prev ||
          !(prev.hasOwnProperty('length') && angular.isDefined(prev[0]) && prev[0].hasOwnProperty('url'))) return '';
      var url = prev[0].url;
      if ( !angular.isDefined(url) ) return "";
      angular.forEach(campaignList, function(name, parameter){
        var position = url.indexOf(parameter);
        if ( position > 0 ) {
          if ( str !== "" ) {
            str += "\n";
          }
          str += name;
        }
      });
      return str;
    }

    $scope.checkToIP = function (ip){
      var targetIps = <?php echo json_encode($excludeList['ips'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
      for( var n in targetIps ){
        if ( targetIps[n].indexOf('/') > -1 ) {
          var req = cidr2regex(targetIps[n]);
          if ( ip.match(req) ) {
            return false;
          }
        }
        else {
          if ( ip === targetIps[n] ) {
            return false;
          }
        }
      }
      return true;
    };

    /* パラメーターを取り除く */
    var targetParams = <?php echo json_encode(array_flip($excludeList['params']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
    $scope.trimToURL = function (url){
      if ( typeof(url) !== 'string' ) return "";
      return trimToURL(targetParams, url);
    };

    // 【チャット】テキストの構築
    $scope.createTextOfMessage = function(chat, message, opt) {
      var strings = message.split(/\n|<br>/g);
      var isSmartphone = false;
      var custom = "";
      var radioName = "sinclo-radio" + Object.keys(chat).length;
      var option = ( typeof(opt) !== 'object' ) ? { radio: true } : opt;
      var widgetSize = '4'; //リアルタイムモニタ詳細画面
      for (var i = 0; strings.length > i; i++) {
        if(strings[i].match(/(<div |<\/div>)/)) {
          try {
            custom += strings[i].replace(/<[^>]*>/g, '');
          } catch(e) {}
          continue;
        }
        var str = escape_html(strings[i]);
        // ラジオボタン
        var radio = str.indexOf('[]');
        var selectedRadio = str.indexOf('[*]');
        if ( option.radio && (radio > -1 || selectedRadio > -1) ) {
          var val = '';
          if (radio > -1) {
            val = str.slice(radio + 2);
          }
          if (selectedRadio > -1) {
            val = str.slice(selectedRadio + 3);
          }
          str = "<input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + val + "' disabled=''>";
          str += "<label class='pointer' for='" + radioName + "-" + i + "'>" + val + "</label>";
        }
        if(chat.messageType === 1) {
          widgetSize = '5';
        }
        //リンク、電話番号,img
        console.log(widgetSize);
        str = replaceVariable(str,isSmartphone,widgetSize);
        custom += str + "\n";

      }
      return custom;
    };

    $scope.createTextOfCheckbox = function(message) {
      var checkboxData = JSON.parse(message);
      var array = checkboxData.message.split(checkboxData.separator);
      var html = '<ul style="margin: auto; height: auto !important; border: none; background-color: transparent; overflow-y: hidden !important;">';
      angular.forEach(array, function(item) {
        html += '<li style="list-style-type: disc; border: none; background-color: transparent; margin: 5px 0 0 15px; padding: 0;">' + item + '</li>';
      });
      html += '</ul>';
      html = replaceVariable(html,false,4);

      return html;
    };

    $scope.createTextOfSendFile = function(chat, url, name, size, extension, isExpired, message) {
      var thumbnail = "";
      if (extension.match(/(jpeg|jpg|gif|png)$/i) != null && !isExpired) {
        thumbnail = "<img src='" + url + "' class='sendFileThumbnail' width='64' height='64'>";
      } else {
        thumbnail = "<i class='fa " + selectFontIconClassFromExtension(extension) + " fa-4x sendFileThumbnail' aria-hidden='true'></i>";
      }

      var cName = (message) ? "シナリオメッセージ（ファイル送信）" : "ファイル送信";
      var content = "<span class='cName'>" + cName + (isExpired ? "（ダウンロード有効期限切れ）" : "") + "</span>";
      if(message && message !== "") {
        content += "<span class='sendFileMessage'>" + message + "</span>";
      }
      content    += "<div class='sendFileContent'>";
      content    += "  <div class='sendFileThumbnailArea'>" + thumbnail + "</div>";
      content    += "  <div class='sendFileMetaArea'>";
      content    += "    <span class='data sendFileName'>" + name + "</span>";
      content    += "    <span class='data sendFileSize'>" + formatBytes(size,2) + "</span>";
      content    += "  </div>";
      content    += "</div>";

      return content;
    };

    $scope.createTextOfReceivedFile = function(comment, downloadUrl, extension) {
      var divElm = document.createElement('div');
      divElm.style.textAlign = "right";
      var thumbnail = "";
      if (extension.match(/(jpeg|jpg|gif|png)$/i) != null) {
        thumbnail = "<img src='" + downloadUrl + "?thumb' class='sendFileThumbnail' style='max-width: 200px; max-height: 140px'>";
      } else {
        thumbnail = "<i class='fal " + selectFontIconClassFromExtension(extension) + " fa-4x sendFileThumbnail' aria-hidden='true'></i>";
      }
      divElm.innerHTML =
        "    <div class=\"receiveFileContent\" style='line-height:0; margin-bottom:0;'>" +
        "      <div class=\"selectFileArea\" style='margin-bottom:0;'>" +
        "        <p class=\"preview\" style='margin:0; text-align: center;'>" + thumbnail + "</p>" +
        "        <p class=\"commentLabel\" style='margin:0;'>＜コメント＞</p>" +
        "        <p class=\"commentarea\">" + comment + "</p>" +
        "      </div>" +
        "    </div>";

      return divElm.innerHTML;
    };

    function selectFontIconClassFromExtension(ext) {
      var selectedClass = "",
        icons = {
          image:      'fa-file-image',
          pdf:        'fa-file-pdf',
          word:       'fa-file-word',
          powerpoint: 'fa-file-powerpoint',
          excel:      'fa-file-excel',
          audio:      'fa-file-audio',
          video:      'fa-file-video',
          zip:        'fa-file-zip',
          code:       'fa-file-code',
          text:       'fa-file-text',
          file:       'fa-file'
        },
        extensions = {
          gif: icons.image,
          jpeg: icons.image,
          jpg: icons.image,
          png: icons.image,
          pdf: icons.pdf,
          doc: icons.word,
          docx: icons.word,
          ppt: icons.powerpoint,
          pptx: icons.powerpoint,
          xls: icons.excel,
          xlsx: icons.excel,
          aac: icons.audio,
          mp3: icons.audio,
          ogg: icons.audio,
          avi: icons.video,
          flv: icons.video,
          mkv: icons.video,
          mp4: icons.video,
          gz: icons.zip,
          zip: icons.zip,
          css: icons.code,
          html: icons.code,
          js: icons.code,
          txt: icons.text,
          csv: icons.csv,
          file: icons.file
        };
      if(isset(extensions[ext])) {
        selectedClass = extensions[ext]
      } else {
        selectedClass = extensions['file'];
      }
      return selectedClass;
    }

    // 【チャット】チャット枠の構築
    $scope.createMessage = function(elem, chat){
      var cn = "";
      var div = document.createElement('div');
      var li = document.createElement('li');
      var content = "";
      console.log(chat);

      var type = Number(chat.messageType);
      var message = chat.message;
      var userId = Number(chat.userId);
      // 消費者からのメッセージの場合
      if ( type === chatApi.messageType.customer) {
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        li.className = cn;
        content = $scope.createTextOfMessage(chat, message, {radio: false});
      }
      // オートメッセージの場合
      else if ( type === chatApi.messageType.company) {
        cn = "sinclo_se";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        var chatName = widget.subTitle;
        if ( Number(widget.showName) === <?=C_WIDGET_SHOW_NAME?> ) {
          chatName = userList[Number(userId)];
        }

        content = $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.auto || type === chatApi.messageType.sorry ) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>自動応答</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.autoSpeech || type === chatApi.messageType.notification) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>自動返信</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.sendFile ) {
        // ファイル送信はmessageがJSONなのでparseする
        message = JSON.parse(message);
        cn = "sinclo_se";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
//        var chatName = widget.subTitle;
//        if ( Number(widget.showName) === <?//=C_WIDGET_SHOW_NAME?>// ) {
//          chatName = userList[Number(userId)];
//        }
        var isExpired = Math.floor((new Date()).getTime() / 1000) >=  (Date.parse( message.expired.replace( /-/g, '/') ) / 1000);
        content = $scope.createTextOfSendFile(chat, message.downloadUrl, message.fileName, message.fileSize, message.extension, isExpired);
        if(!isExpired) {
          li.style.cursor = "pointer";
          li.addEventListener("click", function(event){window.open(message.downloadUrl)});
        }
      }// 消費者からのメッセージの場合
      else if ( type === chatApi.messageType.scenario.customer.hearing || type === chatApi.messageType.scenario.customer.radio
        || type === chatApi.messageType.scenario.customer.pulldown || type === chatApi.messageType.scenario.customer.calendar
        || type === chatApi.messageType.scenario.customer.carousel || type === chatApi.messageType.scenario.customer.button
        || type === chatApi.messageType.scenario.customer.buttonUI
        || type === chatApi.messageType.scenario.customer.reInputText || type === chatApi.messageType.scenario.customer.reInputRadio
        || type === chatApi.messageType.scenario.customer.reInputPulldown || type === chatApi.messageType.scenario.customer.reInputCalendar
        || type === chatApi.messageType.scenario.customer.reInputCarousel || type === chatApi.messageType.scenario.customer.reInputButton
        || type === chatApi.messageType.scenario.customer.reInputButtonUI || type === chatApi.messageType.scenario.customer.reInputCheckbox
        || type === chatApi.messageType.diagram.customer.branch) {
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        li.className = cn;
        content = $scope.createTextOfMessage(chat, message, {radio: false});
      } else if ( type === chatApi.messageType.scenario.customer.checkbox) {
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        li.className = cn;
        if (isJSON(message)) {
          content = $scope.createTextOfCheckbox(message);
        } else {
          content = $scope.createTextOfMessage(chat, message, {radio: false});
        };
      } else if ( type === chatApi.messageType.scenario.customer.selection) {
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        li.className = cn;
        content = $scope.createTextOfMessage(chat, message, {radio: false});
      }
      else if ( type === chatApi.messageType.scenario.message.text ) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>シナリオメッセージ(テキスト発言)</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.scenario.message.hearing ) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>シナリオメッセージ(ヒアリング)</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.scenario.message.pulldown
          || type === chatApi.messageType.scenario.message.calendar
          || type === chatApi.messageType.scenario.message.carousel
          || type === chatApi.messageType.scenario.message.button
          || type === chatApi.messageType.scenario.message.buttonUI
          || type === chatApi.messageType.scenario.message.checkbox
          || type === chatApi.messageType.scenario.message.radio) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>シナリオメッセージ(ヒアリング)</span>";
        var json = JSON.parse(message);
        if(!json.message) {
          switch (type) {
            case chatApi.messageType.scenario.message.pulldown:
              content += '（プルダウン質問内容なし）';
              break;
            case chatApi.messageType.scenario.message.calendar:
              content += '（カレンダー質問内容なし）';
              break;
            case chatApi.messageType.scenario.message.carousel:
              content += '（カルーセル質問内容なし）';
              break;
            case chatApi.messageType.scenario.message.buttonUI:
              content += '（ボタン質問内容なし）';
              break;
            case chatApi.messageType.scenario.message.checkbox:
              content += '（チェックボックス質問内容なし）';
              break;
            case chatApi.messageType.scenario.message.radio:
              content += '（ラジオボタン質問内容なし）';
              break;
            default:
              content += '（質問内容なし）';
              break;
          }
        } else {
          content += $scope.createTextOfMessage(chat, json.message);
        }
        var radioContent = "";
        if(type === chatApi.messageType.scenario.message.radio) {
          for(var i=0; i < json.settings.options.length; i++) {
            if(i === (json.settings.options.length - 1)) {
              radioContent += '[] ' + json.settings.options[i];
            } else {
              radioContent += '[] ' + json.settings.options[i] + "\n";
            }
          }
          content += $scope.createTextOfMessage(chat, radioContent);
        }
      }
      else if ( type === chatApi.messageType.scenario.message.selection ) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>シナリオメッセージ(選択肢)</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.scenario.message.receiveFile ) {
        // ファイル送信はmessageがJSONなのでparseする
        message = JSON.parse(message);
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
//        var chatName = widget.subTitle;
//        if ( Number(widget.showName) === <?//=C_WIDGET_SHOW_NAME?>// ) {
//          chatName = userList[Number(userId)];
//        }
        var isExpired = Math.floor((new Date()).getTime() / 1000) >=  (Date.parse( message.expired.replace( /-/g, '/') ) / 1000);
        content = $scope.createTextOfSendFile(chat, message.downloadUrl, message.fileName, message.fileSize, message.extension, isExpired, message.message);
        if(!isExpired) {
          li.style.cursor = "pointer";
          li.addEventListener("click", function(event){window.open(message.downloadUrl)});
        }
      } else if ( type === chatApi.messageType.scenario.customer.sendFile ) {
        if(isJSON(message)) {
          // ファイル送信はmessageがJSONなのでparseする
          message = JSON.parse(message);
          cn = "sinclo_re";
          div.style.textAlign = 'left';
          div.style.height = 'auto';
          div.style.padding = '0';
          //        var chatName = widget.subTitle;
          //        if ( Number(widget.showName) === <?//=C_WIDGET_SHOW_NAME?>// ) {
          //          chatName = userList[Number(userId)];
          //        }
          content = $scope.createTextOfReceivedFile(message.comment, message.downloadUrl, message.extension);
          if (!isExpired) {
            li.style.cursor = "pointer";
            li.style.lineHeight = 0;
            li.addEventListener("click", function (event) {
              window.open(message.downloadUrl)
            });
          }
        } else {
          cn = "sinclo_re";
          div.style.textAlign = 'left';
          div.style.height = 'auto';
          div.style.padding = '0';
          content += $scope.createTextOfMessage(chat, message);
        }
      } else if ( type === chatApi.messageType.linkClick ) {
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        li.className = cn;
        content = $scope.createTextOfMessage(chat, message);
        console.log(content);
        var linkTabReg = RegExp(/<a ([\s\S]*?)>([\s\S]*?)<\/a>/);
        var linkTab = content.match(linkTabReg);
        console.log(linkTab);
        content = '（「'+linkTab[0]+'」をクリック）';
      }
      else if ( Number(type) === chatApi.messageType.scenario.customer.answerBulkHearing ) {
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>シナリオメッセージ(一括ヒアリング回答)</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( Number(type) === chatApi.messageType.scenario.customer.noModBulkHearing ) {
        // 未修正ログは表示しない
        return;
      }
      else if ( Number(type) === chatApi.messageType.scenario.customer.modifyBulkHearing ) {
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>シナリオメッセージ(一括ヒアリング内容修正)</span>";
        content += $scope.createBulkHearingKeyValue(chat, message);
      }
      else if ( Number(type) === chatApi.messageType.scenario.message.returnBulkHearing ) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>シナリオメッセージ(一括ヒアリング解析結果)</span>";
        content += $scope.createBulkHearingAnalyseData(chat, message);
      }
      else if ( Number(type) === chatApi.messageType.scenario.customer.cancel ) {
        // 何もしない
        return;
      }
      else if ( Number(type) === chatApi.messageType.cogmo.message ) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>自動応答</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( Number(type) === chatApi.messageType.cogmo.feedback ) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>自動応答</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( Number(type) === chatApi.messageType.diagram.message.branch ) {
        var obj = isJSON(message) ? JSON.parse(message) : message;
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>チャットツリーメッセージ(分岐)</span>";
        content += $scope.createTextOfMessage(chat, obj.message);
      }
      else if ( Number(type) === chatApi.messageType.diagram.message.text ) {
        var obj = isJSON(message) ? JSON.parse(message) : message;
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>チャットツリーメッセージ(テキスト発言)</span>";
        content += $scope.createTextOfMessage(chat, obj.message);
      }
      else if ( Number(type) === chatApi.messageType.diagram.customer.operator ) {
        // 未修正ログは表示しない
        return;
      }
      else  {
        cn = "sinclo_etc";
        var userName = "オペレーター";
        if ( Number(widget.showName) === <?=C_WIDGET_SHOW_NAME?> && userList.hasOwnProperty(Number(userId)) ) {
          userName = userList[Number(userId)];
        }
        if ( type === chatApi.messageType.start ) {
          content = "－　" + userName + "が入室しました　－";
        }
        if ( type === chatApi.messageType.end ) {
          content = "－　" + userName + "が退室しました　－";
        }
      }
      li.className = cn;
      li.innerHTML = content;
      div.appendChild(li);
      $(elem).append(div);
    };

    $scope.createBulkHearingAnalyseData = function(chat, data) {
      var resultVal = "";
      try {
        var obj = JSON.parse(data);
        Object.keys(obj.target).forEach(function(elm, idx, arr){
          resultVal += obj.target[elm].label + "：" + (obj.message[obj.target[elm].inputType] ? obj.message[obj.target[elm].inputType] : "（なし）") + "\n";
        });
      } catch(e) {
        console.log(e.message);
      }
      return resultVal;
    };

    $scope.createBulkHearingKeyValue = function(chat, data) {
      var resultVal = "";
      try {
        var obj = JSON.parse(data);
        Object.keys(obj).forEach(function(elm, idx, arr){
          resultVal += obj[elm].label + '：' + obj[elm].value + "\n";
        });
      } catch(e) {
        console.log("createBulkHearingKeyValue : " + e.message);
      }
      return resultVal;
    };

    // 【チャット】クラス名のジャッジ
    $scope.judgeChatClass = function(type){
      var cn = "sinclo_etc";
      if (Number(type) === chatApi.messageType.customer) {
        cn = "sinclo_re";
      }
      else if (Number(type) === chatApi.messageType.company) {
        cn = "sinclo_se";
      }
      else if (Number(type) === chatApi.messageType.auto || Number(type) === chatApi.messageType.autoSpeech) {
        cn = "sinclo_auto";
      }
      return cn;
    };




    $scope.chgOpStatus = function(){
      var opState = $('#changeOpStatus'),
        status = opState.data('status');
      //START:チャット通知設定のブロック状況
      <?php if(isset($coreSettings[C_COMPANY_USE_CHAT]) && $coreSettings[C_COMPANY_USE_CHAT]): ?>
      //以降、表示しないを選択していない場合は通知設定警告を出す


        //離席中→待機中に変化する場合にチャット通知設定の状態を見る
        if(status == 0){



            //通知設定が許可されていない場合は警告を出す
            var userAgent = window.navigator.userAgent.toLowerCase();
            if(document.cookie.indexOf('block_notify=true') == -1){
              if(userAgent.indexOf('msie') != -1 || userAgent.indexOf('trident') != -1){
                message = "通知の表示がブロックされているため、新着チャットを<br>受信した際にデスクトップ通知が表示されません。<br>"
                        + "<br>チャット受信時の通知を受け取りたい場合は<br><span class='red_font'>通知の設定を「許可」に変更</span>してください。"
                        + "<br><br><a target='_blank' href='https://info.sinclo.jp/manual/デスクトップ通知のブロック解除方法/'>設定方法はこちら</a>"
                        + "<br><br><label for='block_notify_chat'><input type='checkbox' id='block_notify_chat' onclick='notify_cookie()'>"
                        + "今後、このメッセージを表示しない</label>";
                setTimeout(function(){
                  modalOpen.call(window, message,"p-cus-block-notify",'通知の表示', 'fade', '');
                },5);
              }else if(Notification.permission !== "granted"){
                message = "通知の表示がブロックされているため、新着チャットを<br>受信した際にデスクトップ通知が表示されません。<br>"
                        + "<br>チャット受信時の通知を受け取りたい場合は<br><span class='red_font'>通知の設定を「許可」に変更</span>してください。"
                        + "<br><br><a target='_blank' href='https://info.sinclo.jp/manual/デスクトップ通知のブロック解除方法/'>設定方法はこちら</a>"
                        + "<br><br><label for='block_notify_chat'><input type='checkbox' id='block_notify_chat' onclick='notify_cookie()'>"
                        + "今後、このメッセージを表示しない</label>";
                setTimeout(function(){
                  modalOpen.call(window, message,"p-cus-block-notify",'通知の表示', 'fade', '');
                },5);
              }
            }
        }
      <?php endif;?>
      //FINISH:チャット通知設定がのブロック状況




      // 在席⇒退席でチャット中の場合
      if ( String(status) === "<?=C_OPERATOR_ACTIVE?>" && $scope.chatList.length > 0 ) {
        // チャットを退室
        var message = "現在チャット対応中です。<br>強制退室しますか？";
        modalOpen.call(window, message, 'p-confirm', 'メッセージ');
        popupEvent.closeNoPopup = function(){
          popupEvent.close();
        };
        popupEvent.closePopup = function(){
          for( var i = 0; $scope.chatList.length > i; i++ ){
            $scope.ngChatApi.disConnect($scope.chatList[i]);
          }
          chgOpStatus();
          popupEvent.close();
        };
      }
      // その他
      else {
          chgOpStatus();
      }
    };

    $scope.isUndefined = function(value) {
      return angular.isUndefined(value);
    }

    $scope.isset = function(value){
      var result;
      if ( angular.isUndefined(value) ) {
        result = false;
      }
      if ( angular.isNumber(value) && value > 0 ) {
        result = true;
      }
      else {
        result = false;
      }
      return result;
    };

    $scope.ngChatApi = {
      init: function(){
        $("#sendMessage").keydown(function(e){
          if ( e.keyCode === 13 ) {
            if ( e.ctrlKey ) return false;
            if ( !$scope.chatOptionDisabled($scope.detailId) ) {
              chatApi.errorChatStart();
              return false;
            }
            if ( $scope.settings.sendPattarn && !e.shiftKey ) {
              chatApi.pushMessage();
            }
            if ( !$scope.settings.sendPattarn && e.shiftKey ) {
              chatApi.pushMessage();
            }
          }
        })
        .focus(function(e){
          chatApi.observeType.start();
          // フォーカスが当たった時にPlaceholderを消す（Edge対応）
          this.placeholder='';
          $scope.chatPsFlg = false;
        })
        .click(function(e){
          // クリックされた時にPlaceholderを消す
          this.placeholder='';
          $scope.chatPsFlg = false;
        })
        .blur(function(e){
          // フォーカスが外れたら時にPlaceholderを表示（Edge対応）
          var sendPattarnStr = ( $scope.settings.sendPattarn ) ? "Shift + Enter": "Enter";
          chatApi.observeType.end();
          this.placeholder="ここにメッセージ入力してください。\n・" + sendPattarnStr + "で改行されます\n・下矢印キー(↓)で定型文が開きます<?php if(isset($coreSettings[C_COMPANY_USE_SEND_FILE]) && $coreSettings[C_COMPANY_USE_SEND_FILE]): ?>\n・ここにファイルをドロップするとファイルを送信できます<?php endif;?>";
          $scope.chatPsFlg = true;

        });
        chatApi.init();
      },
      connect: function(obj){
        chatApi.connection(obj);
      },
      disConnect: function(tabId, sincloSessionId){
        $("#sendMessage").val("").blur();
        emit("chatEnd", {tabId: tabId, userId: myUserId, sincloSessionId: sincloSessionId});
      },
      notification: function(monitor){
        // 他のオペレーターが対応中の場合
        if ( monitor.hasOwnProperty('chat') && $scope.isset(monitor.chat) && String(monitor.chat) !== "<?=$muserId?>" ) return false;

        <?php if(isset($scNum)): /* チャット応対上限 */ ?>
          // チャット応対上限に達している場合は、通知音とデスクトップ通知を出さない
          if ( !(monitor.hasOwnProperty('chat') && String(monitor.chat) === "<?=$muserId?>") && $scope.scInfo.remain < 1 ) return false;
        <?php endif; ?>

        // 着信音を鳴らす
        chatApi.call();

        // 詳細を開いてる且つ、企業がアクティブタブの場合は、デスクトップ通知を出さない
        if ( angular.isDefined($scope.detailId) && $scope.detailId !== "" && document.hasFocus() ) {
          return false;
        }

        if (!('Notification' in window)) return false;
        var m = monitor;
        function getBody(){
          var options = {
              body: "",
              icon: "<?=C_PATH_NODE_FILE_SERVER?>/img/mark.png"
          };

          var settings = <?=$notificationList?>;

          for(var key in settings){
            var target = "", opt = settings[key];
            // タイトル
            if (m.title && Number(opt.type) === Number(<?=C_NOTIFICATION_TYPE_TITLE?>)) {
              target = m.title;
            }
            // URL
            else if (m.url && Number(opt.type) === Number(<?=C_NOTIFICATION_TYPE_URL?>)) {
              target = m.url;
            }
            if (target !== '' && target.indexOf(opt.keyword) >= 0) {
              options.body = opt.name;
              options.icon = "/img/<?=C_PATH_NOTIFICATION_IMG_DIR?>"+opt.image;
            }
          }

          return options;
        }
        var nInstance = new Notification('【' + monitor.accessId + '】新着チャットが届きました', getBody());
        nInstance.onclick = function(){
          window.focus(); // 現在のタブにフォーカスを当てる
          if ( chatApi.tabId !== monitor.tabId ) {
            $scope.showDetail(monitor.tabId, monitor.sincloSessionId); // 詳細を開く
          }
          else {
            scDown();
          }
          nInstance.close();
        };
      }
    };

    <?php if(isset($coreSettings[C_COMPANY_USE_SEND_FILE]) && $coreSettings[C_COMPANY_USE_SEND_FILE]): ?>
    // ===========
    // ファイル送信
    // ===========
    $scope.fileUploader = {
      isDisable: false,
      dragging: false,
      dragArea: $("#sub_contents"),
      droppable: $("#fileUploadDropArea").css('display', 'none'),
      selectFileBtn: $('#selectFileBtn'),
      selectInput: $('#selectFileInput'),
      fileObj: null,
      loadData: null,
      allowExtensions: <?= json_encode($allowExtensions); ?>,

      init: function() {
        if(window.FileReader) {
          this._addDragAndDropEvents();
        } else {
          this.isDisable = true;
        }
        this._addSelectFileEvents();
      },
      _addDragAndDropEvents: function() {
        this.dragArea.on("dragenter", this._enterEvent);
        this.dragArea.on("dragover", this._overEvent);
        this.dragArea.on("dragleave", this._leaveEvent);
        this.dragArea.on("drop", function(){ $scope.fileUploader.droppable.css('display', 'none'); event.preventDefault(); event.stopPropagation(); return false;});
        this.droppable.on("drop", this._handleDroppedFile);
      },
      _addSelectFileEvents: function() {
        this.selectFileBtn.on('click', function(event){
          $scope.fileUploader.selectInput.trigger('click');
        });
        this.selectInput.on("click", function(event){
          $scope.fileUploader._hideInvalidError();
          $(this).val(null);
        }).on("change",function(event){
          if($scope.fileUploader.selectInput[0].files[0]) {
            $scope.fileUploader.fileObj = $scope.fileUploader.selectInput[0].files[0];
            // ファイルの内容は FileReader で読み込みます.
            var fileReader = new FileReader();
            fileReader.onload = function (event) {
              if(!$scope.fileUploader._validExtension($scope.fileUploader.fileObj.name)) {
                $scope.fileUploader._showInvalidError();
                return;
              }
              // event.target.result に読み込んだファイルの内容が入っています.
              // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
              $scope.fileUploader.loadData = event.target.result;
              $scope.fileUploader._showConfirmDialog("【" + $scope.fileUploader.fileObj.name + "】をアップロードします。<br>よろしいですか？");
            };
            fileReader.readAsArrayBuffer($scope.fileUploader.fileObj);
          }
        });
      },
      _enterEvent: function(event) {
        $scope.fileUploader.dragging = true;
        $scope.fileUploader._cancelEvent(event);
        return false;
      },
      _overEvent: function(event) {
        $scope.fileUploader.dragging = false;
        $scope.fileUploader.droppable.css('display', 'flex');
        $scope.fileUploader._cancelEvent(event);
        return false;
      },
      _leaveEvent: function(event) {
        if($scope.fileUploader.dragging) {
          $scope.fileUploader.dragging = false;
        } else {
          $scope.fileUploader.droppable.css('display', 'none');
        }
        $scope.fileUploader._cancelEvent(event);
        return false;
      },
      _handleDroppedFile: function(event) {
        $scope.fileUploader.droppable.css('display', 'none');
        $scope.fileUploader._hideInvalidError();
        // ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
        $scope.fileUploader.fileObj = event.originalEvent.dataTransfer.files[0];

        // ファイルの内容は FileReader で読み込みます.
        var fileReader = new FileReader();
        fileReader.onload = function(event) {
          if(!$scope.fileUploader._validExtension($scope.fileUploader.fileObj.name)) {
            $scope.fileUploader._showInvalidError();
            return;
          }
          // event.target.result に読み込んだファイルの内容が入っています.
          // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
          $scope.fileUploader.loadData = event.target.result;
          $scope.fileUploader._showConfirmDialog("<p style='text-align:center;'><span style='line-height: 2em;'>【" + $scope.fileUploader.fileObj.name + "】をアップロードします。<br>よろしいですか？</span></p>");
        }
        fileReader.readAsArrayBuffer($scope.fileUploader.fileObj);

        // デフォルトの処理をキャンセルします.
        $scope.fileUploader._cancelEvent(event);
        return false;
      },
      _cancelEvent: function(e) {
        e.preventDefault();
        e.stopPropagation();
      },
      _validExtension: function(filename) {
        var split = filename.split(".");
        var targetExtension = split[split.length-1];
        var regex = new RegExp(this.allowExtensions.join("|"), 'i');
        return regex.test(targetExtension);
      },
      _showInvalidError: function() {
        var span = document.createElement("span");
        span.classList.add('errorMsg');
        span.textContent = "指定のファイルは送信を許可されていません。";
        $("#sendMessageArea").append(span);
      },
      _hideInvalidError: function() {
        $('#sendMessageArea').find('span.errorMsg').remove();
      },
      _showConfirmDialog: function(message) {
        modalOpen.call(window, message, 'p-cus-file-upload', '確認', 'moment');
        popupEvent.closePopup = function() {
          $scope.uploadFile($scope.fileUploader.fileObj, $scope.fileUploader.loadData);
          popupEvent.close();
        };
      }
    };

    $scope.fileUploader.init();
    // =====================
    // ファイル送信（ここまで）
    // =====================
    <?php endif; ?>

    $scope.ngCameraApi = {
      connect: function(obj){
        cameraApi.connect(obj);
      },
      disConnect: function(obj){
        cameraApi.disConnect(obj);
      }
    };

    // ファイル送信
    $scope.uploadFile = function(fileObj, loadFile) {
      var fd = new FormData();
      var blob = new Blob([loadFile], {type: fileObj.type});
      var target = $('upload-notification');
      fd.append("targetUserId", chatApi.userId);
      fd.append("file", blob, fileObj.name);

      $.ajax({
        url  : "<?= $this->Html->url('/File/upload') ?>",
        type : "POST",
        data : fd,
        cache       : false,
        contentType : false,
        processData : false,
        dataType    : "json",
        xhr : function(){
          var XHR = $.ajaxSettings.xhr();
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
          return XHR;
        }
      })
      .done(function(data, textStatus, jqXHR){
        console.log(JSON.stringify(data));
        target.css('display', 'none');
        $('#uploadMessage').css('display', 'block');
        $('#processingMessage').css('display', 'none');
        $scope.uploadProgress = 0;
        $scope.$apply();
        var noFlg = 0;
        emit('sendChat', {
          token: chatApi.token,
          tabId: chatApi.tabId,
          sincloSessionId: chatApi.sincloSessionId,
          userId: chatApi.userId,
          chatMessage: JSON.stringify(data),
          mUserId: myUserId,
          messageType: chatApi.messageType.sendFile,
          messageRequestFlg: noFlg
        });
      })
      .fail(function(jqXHR, textStatus, errorThrown){
        alert("fail");
        $('#uploadMessage').css('display', 'block');
        $('#processingMessage').css('display', 'none');
        $scope.uploadProgress = 0;
        $scope.$apply();
      });
      var targetHeight = target.outerHeight();
      target.css('display', 'block');
      target.css('top',$('#chatTalk').outerHeight() - targetHeight);
    };

    function updateProgress(evt) {
      console.log(evt.loaded / evt.total);
    }

    $scope.$watch('monitorList', function(){
      if ( angular.isDefined($scope.detailId) && !($scope.detailId in $scope.monitorList) ) {
        $scope.showDetail($scope.detailId, $scope.sincloSessionId);
      }
    });

    function pushToList(obj){

      if(contract.monitorPollingMode && $scope.monitorList[obj.tabId]
         && ((!( 'referrer' in obj) || $scope.monitorList[obj.tabId].ref === $scope.trimToURL(obj.referrer))
          && (!( 'connectToken' in obj) || $scope.monitorList[obj.tabId].connectToken === obj.connectToken)
          && (!( 'responderId' in obj) || $scope.monitorList[obj.tabId].responderId === obj.responderId)
          && (!( 'coBrowseConnectToken' in obj) || $scope.monitorList[obj.tabId].coBrowseConnectToken === obj.coBrowseConnectToken)
          && (!( 'docShareId' in obj) || $scope.monitorList[obj.tabId].docShareId === obj.docShareId)
          && (!( 'sincloSessionId' in obj) || $scope.monitorList[obj.tabId].sincloSessionId === obj.sincloSessionId)
          && (!( 'title' in obj) || $scope.monitorList[obj.tabId].title === obj.title)
          && (!( 'status' in obj) || $scope.monitorList[obj.tabId].status === obj.status)
        )
      ) {
        return;
      }

      if(!obj.sincloSessionId) return;

      if ( 'ipAddress' in obj && 'ipAddress' in obj) {
        if (!$scope.checkToIP(obj.ipAddress)) return false;
      }

      if(obj.customerInfo) {
        $scope.getCustomerInfoFromMonitor(obj);
      }

      $scope.monitorList[obj.tabId] = obj;

      if ( 'referrer' in obj && 'referrer' in obj) {
        var url = $scope.trimToURL(obj.referrer);
        //メッセージが50文字以上の場合3点リーダー表示
        if(url.length > 50) {
          url = url.substr(0,50)　+ '...';
        }
        $scope.monitorList[obj.tabId].processedRef = url;
        $scope.monitorList[obj.tabId].ref = $scope.trimToURL(obj.referrer);
      }

      if ( 'connectToken' in obj && 'responderId' in obj) {
        $scope.monitorList[obj.tabId].connectToken = obj.connectToken;
        $scope.monitorList[obj.tabId].responderId = obj.responderId; // ここいる？
      }

      if ( 'coBrowseConnectToken' in obj && 'responderId' in obj) {
        $scope.monitorList[obj.tabId].coBrowseConnectToken = obj.coBrowseConnectToken;
        $scope.monitorList[obj.tabId].responderId = obj.responderId; // ここいる？
      }

      if ( 'docShareId' in obj ) {
        $scope.monitorList[obj.tabId].docShare = true;
        $scope.monitorList[obj.tabId].responderId = obj.docShareId;
      }

      if ( 'sincloSessionId' in obj ) {
        $scope.monitorList[obj.tabId].sincloSessionId = obj.sincloSessionId;
      }

      Object.keys($scope.monitorList).forEach(function(key, index, array){
        if($scope.monitorList[key].sincloSessionId === obj.sincloSessionId) {
          $scope.monitorList[key].prev = obj.prev;
        }
      });

    }

    var changeStatusTimer = null;
    $scope.reload = function(){
      clearTimeout(changeStatusTimer);
      changeStatusTimer = window.setTimeout(function(){
      // 在席ステータスチェック
<?php if($widgetCheck): ?>
        var opState = $('#changeOpStatus'),
            status = opState.data('status');

        // 退席中でチャット中の場合
        if ( String(status) !== "<?=C_OPERATOR_ACTIVE?>" && $scope.chatList.length > 0 ) {
          chgOpStatus();
        }
<?php endif; ?>

        // 待機・離席チェック
        if ( Number($scope.oprCnt) === 0 && Number($scope.oprWaitCnt) === 0 ) {
          <?php if(strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0): ?>
          socket.disconnect(); socket.connect();
          <?php endif; ?>
        }

      }, 1000);
    };

    function pushToChatList(tabId){
      if ( $scope.chatList.indexOf(tabId) < 0 ) {
        $scope.reload();
        $scope.chatList.push(tabId);
      }
    }

    socket.on('getAccessInfo', function (data) {
      var obj = JSON.parse(data);
<?php if($widgetCheck): ?>
      $scope.oprCnt = obj.onlineUserCnt;
      if ( Number(obj.userId) === Number(myUserId) ) {
        if ( String(obj.status) == "<?=C_OPERATOR_ACTIVE?>") {
          chgOpStatusView("<?=C_OPERATOR_ACTIVE?>");
          // 待機中の際に「待機中０人以下」になるのを防ぐ
          $scope.oprCnt = ( $scope.oprCnt < 1 ) ? 1 : $scope.oprCnt;
        }
        else {
          chgOpStatusView("<?=C_OPERATOR_PASSIVE?>");
          // 離席中の際に「待機中０人以下」になるのを防ぐ
          $scope.oprCnt = ( $scope.oprCnt < 1 ) ? 0 : $scope.oprCnt;
        }
      }
      $scope.activeOperatorList = obj.activeOperatorList;
      $scope.onlineOperatorList = obj.onlineOperatorList;
      $scope.refreshUserPresences();
<?php endif; ?>
<?php if ( $coreSettings[C_COMPANY_USE_CHAT] && strcmp(intval($scFlg), C_SC_ENABLED) === 0 ) :  ?>
      // チャット対応上限を設定
      if ( obj.hasOwnProperty('scNum') && Number("<?=$muserId?>") === Number(obj.userId) ) {
        $scope.scInfo.remain = Number(obj.scNum);

        if ( obj.hasOwnProperty('scInfo') && obj.scInfo.hasOwnProperty(obj.userId) ) {
          $scope.scInfo.remain -= Number(obj.scInfo[Number(obj.userId)]);
        }

      }
<?php endif; ?>
      $scope.oprWaitCnt = ( obj.userCnt < 1 ) ? <?php echo strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0 ? 1 : 0; ?> : obj.userCnt;

      $scope.reload(); // 整っているか確認

      $scope.setReceiveAccessInfoTrigger();
    });

    $scope.setReceiveAccessInfoTrigger = function() {
      if(contract.monitorPollingMode) {
        if(window.loading.load && contract.monitorPollingMode && $scope.firstLoadMonitorList) {
          window.loading.load.start();
        }
        if($scope.pollingModeIntervalTimer) {
          clearTimeout($scope.pollingModeIntervalTimer);
        }
        var pollingIntervalMSec = <?= C_REALTIME_MONITOR_POLLING_MODE_INTERVAL_MSEC ?>;
        $scope.pollingModeIntervalTimer = setTimeout(function(e){
          emit('getCustomerList',{}, function(obj){
            <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && strcmp(intval($scFlg), C_SC_ENABLED) === 0 ) :  ?>
            // チャット対応上限を設定
            if ( obj.hasOwnProperty('scInfo') && obj.scInfo.hasOwnProperty(<?=$muserId?>) ) {
              $scope.scInfo.remain = (isNumber(<?=$scNum?>)) ? Number(<?=$scNum?>) : 0 ;
              $scope.scInfo.remain -= Number(obj.scInfo[<?=$muserId?>]);
            }
            else {
              $scope.scInfo.remain = 0;
            }
            <?php endif; ?>
          });
        }, pollingIntervalMSec);
      }
    };

    socket.on('outCompanyUser', function (data) {
      var obj = JSON.parse(data);
      $scope.oprWaitCnt = obj.userCnt;
      delete $scope.onlineOperatorList[obj.userId];
      delete $scope.activeOperatorList[obj.userId];
      $scope.refreshUserPresences();
    });

    socket.on('receiveAccessInfo', function (data) {
      var obj = JSON.parse(data);
      if(contract.monitorPollingMode) {
        Object.keys(obj).forEach(function (element, index, array) {
          if (index === 0) return; // サイト訪問者の総数を格納しているため無視
          $scope.tmpMonitorList[obj[index].tabId] = obj[index];
        });
      } else {
        if (!contract.hideRealtimeMonitor) {
          setTimeout(function(){
            $scope.$apply(function(){
              obj.forEach(function(elm, index, arr) {
                if (index === 0) return; // サイト訪問者の総数を格納しているため無視
                pushToList(elm);
                if ('chat' in elm && String(elm.chat) === "<?=$muserId?>") {
                  pushToChatList(elm.tabId);
                }
              });
            });
          }, 100);
        }
      }
    });

    /**
     * monitorPollingMode: trueのときに使用する
     */
    socket.on('beginOfCustomerList', function(){
      $scope.tmpMonitorList = {};
    });

    /**
     * monitorPollingMode: trueのときに使用する
     */
    socket.on('endOfCustomerList', function(){
      var tmpMonitorListArray = Object.keys($scope.tmpMonitorList).map(function(e){
        return $scope.tmpMonitorList[e];
      });
      tmpMonitorListArray.forEach(function (elm, index, arr) {
        pushToList(elm);
        if ('chat' in elm && String(elm.chat) === "<?=$muserId?>") {
          pushToChatList(elm.tabId);
        }
      });
      Object.keys($scope.monitorList).forEach(function(elm, index, arr){
        if ($scope.tmpMonitorList[elm] ||
            ((contract.hideRealtimeMonitor || !contract.enableRealtimeMonitor) && $scope.searchText.length === 4)) {
          return;
        } else {
          delete $scope.monitorList[elm];
        }
      });
      if(window.loading.load && contract.monitorPollingMode && $scope.firstLoadMonitorList) {
        window.loading.load.finish();
        $scope.firstLoadMonitorList = false;
      }
      $scope.setReceiveAccessInfoTrigger();
    });

    socket.on('resAutoChatMessages', function(d){
        var obj = JSON.parse(d);

        if(obj.chatToken !== chatApi.getMessageToken) return false;

        if ( ('historyId' in obj) ) {
          socket.emit('getChatMessage', {
            siteKey: obj.siteKey,
            tabId: obj.tabId,
            sincloSessionId: obj.sincloSessionId,
            getMessageToken: chatApi.getMessageToken
          });
          for (var key in obj.messages) {
            var chat = obj.messages[key];
            chat.sort = Number(key);
            $scope.messageList.push(chat);
            scDown();
          }
        }

    });

    socket.on('resAutoChatMessage', function(d){
        var obj = JSON.parse(d);
        if (obj.tabId === chatApi.tabId ) {
            var chat = obj;
            chat.sort = Number(chat.sort);
            $scope.messageList.push(obj);
            scDown();
        }
    });

    socket.on('sendCustomerInfo', function (data) {
      var obj = JSON.parse(data);
      pushToList(obj);
    });

    socket.on('customerInfoUpdated', function (data) {
      var obj = JSON.parse(data);
      $scope.customerList[obj.userId] = JSON.parse(obj.data);
      $scope.$apply();
    });


    /* タブ状態受け渡し */
    // タブ状態を取得
    socket.on('retTabInfo', function (d) {
      var obj = JSON.parse(d);
      if ( !(obj.tabId in $scope.monitorList) ) return false;
      $scope.monitorList[obj.tabId].status = obj.status;
      // ウィジェット表示タイミングがページアクセスと同時に表示される訳ではないため、タブステータスの送信と共にステータスを送信する。
      $scope.monitorList[obj.tabId].widget = obj.widget;
    });

    $scope.setName = function(uId){
      if ( String(uId) === "<?=$muserId?>" ) {
        return "あなた";
      }
      else {
        return userList[uId] + "さん";
      }
    };

    socket.on('syncNewInfo', function (data) {
        var obj = JSON.parse(data);

        var tabId = ( obj.subWindow ) ? obj.to : obj.tabId;
        if (angular.isDefined(tabId) && tabId.length > 0 && tabId.indexOf('_frame') > -1) {
          tabId = tabId.substr(0, tabId.indexOf('_frame'));
        }
        // 消費者
        if ($scope.monitorList.hasOwnProperty(tabId)) {
          if ('widget' in obj) {
            $scope.monitorList[tabId].widget = obj.widget;
            if (chatApi.tabId === tabId) {
              chatApi.observeType.emit(chatApi.tabId, chatApi.sincloSessionId, chatApi.observeType.status);
            }
          }
          if ('sincloSessionId' in obj) {
            $scope.monitorList[tabId].sincloSessionId = obj.sincloSessionId;
          }
          if ('connectToken' in obj) {
            $scope.monitorList[tabId].connectToken = obj.connectToken;
          }
          if ('coBrowseConnectToken' in obj) {
            $scope.monitorList[tabId].coBrowseConnectToken = obj.coBrowseConnectToken;
          }
          if ('prev' in obj) {
            $scope.monitorList[tabId].prev = obj.prev;
          }
          if ('title' in obj) {
            $scope.monitorList[tabId].title = obj.title;
          }
          if ('url' in obj) {
            $scope.monitorList[tabId].url = obj.url;
          }
          if ('responderId' in obj) {
            $scope.monitorList[tabId].responderId = obj.responderId;
          }
          if (($scope.isViewable() || !$scope.isML(obj)) && 'orgName' in obj) {
            $scope.monitorList[tabId].orgName = obj.orgName;
          }
          if (($scope.isViewable() || !$scope.isML(obj)) && 'lbcCode' in obj) {
            $scope.monitorList[tabId].lbcCode = obj.lbcCode;
          }
          if('customerInfo' in obj) {
            $scope.customerList[obj.userId] = obj.customerInfo;
            $scope.$apply();
          }
        }
    });

    socket.on('windowSyncInfo', function (data) {
      // 担当しているユーザーかチェック
      var obj = JSON.parse(data), url;
      if (connectToken !== obj.connectToken) return false;
      connectToken = null; // リセット
      url  = "<?= $this->Html->url(array('controller'=>'Customers', 'action'=>'frame')) ?>?type=" + _access_type_host;
      url += "&url=" + encodeURIComponent(obj.url) + "&userId=" + obj.userId;
      url += "&connectToken=" + obj.connectToken + "&id=" + obj.tabId;
      url += "&width=" + obj.windowSize.width + "&height=" + obj.windowSize.height;
      modalFunc.set.call({
        option: {
          url: url,
          tabId: obj.tabId,
          width: 300,
          height: 300
        }
      });
      $('#afs-popup').hide();
    });

  socket.on('docShare', function (data) {
    var obj = JSON.parse(data);
    if(obj && obj.responderId && Number(obj.responderId) === Number(<?=$userInfo["id"]?>)) {
      if($scope.docShareId !== null) {
        var shareId = $scope.docShareId;
      }
      else {
        var shareId = obj.tabId;
      }
      window.open(
        "<?= $this->Html->url(['controller' => 'Customers', 'action' => 'docFrame']) ?>?tabInfo=" + encodeURIComponent(shareId) + "&docId=" + obj.id,
        "doc_monitor_" + shareId,
        "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
      );
      $('#afs-popup').hide();
    }
  });

    socket.on('requestCoBrowseAllowed', function (data) {
      sessionStorage.clear();
      popupEvent.close();
    });

    socket.on('coBrowseSessionLimit', function (data) {
      modalOpen.call(window, '画面キャプチャ共有の同時利用数上限に達しています。', "p-alert", 'お知らせ', 'moment');
    });

    socket.on('beginToCoBrowse', function (data) {
      // 担当しているユーザーかチェック
      var obj = JSON.parse(data), url;
      if (coBrowseConnectToken !== obj.coBrowseConnectToken) return false;

      var url = "<?= $this->Html->url(array('controller'=>'Customers', 'action'=>'laFrame')) ?>?k=begin";
      url += "&userId=" + obj.userId;
      url += "&connectToken=" + obj.coBrowseConnectToken + "&id=" + obj.tabId;
      modalFunc.set.call({
        option: {
          url: url,
          tabId: obj.tabId,
          width: 1028,
          height: 680
        }
      });
      $('#afs-popup').hide();
    });

    socket.on('cngOpStatus', function(data){
      var obj = JSON.parse(data);
      var opState = $('#changeOpStatus');
      opState.data('status', obj.status);
    });

    socket.on('connectConfirm', function(data){
      var obj = JSON.parse(data);
    });

    socket.on('syncEvStart', function(data){
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined && angular.isDefined($scope.monitorList[obj.tabId])) {
        $scope.monitorList[obj.tabId].connectToken = obj.connectToken;
      }
    });

    socket.on('syncStop', function(data){
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined && angular.isDefined($scope.monitorList[obj.tabId])) {
        $scope.monitorList[obj.tabId].connectToken = "";
      }
    });

    socket.on('stopCoBrowse', function(data){
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined && angular.isDefined($scope.monitorList[obj.tabId])) {
        $scope.monitorList[obj.tabId].coBrowseConnectToken = "";
      }
    });

    socket.on('docShareConnect', function(data){ // 資料共有開始
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined && angular.isDefined($scope.monitorList[obj.tabId])) {
        $scope.monitorList[obj.tabId].docShare = true;
        $scope.monitorList[obj.tabId].responderId = obj.responderId;
      }
    });

    socket.on('docDisconnect', function(data){ // 資料共有終了
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined && angular.isDefined($scope.monitorList[obj.tabId])) {
        $scope.monitorList[obj.tabId].docShare = "";
        if ( angular.isUndefined($scope.monitorList[obj.tabId].connectToken) ) {
          $scope.monitorList[obj.tabId].responderId = "";
        }
      }
    });

    socket.on('sharingApplicationRejection', function(data){ // 画面共有拒否
      var obj = JSON.parse(data);
      //画面キャプチャ共有の場合
      var isSyncBrowser = false;
      if(isset(obj.coBrowseConnectToken)) {
        isSyncBrowser = true;
        if (coBrowseConnectToken !== obj.coBrowseConnectToken) return false;
      }
      //画面共有の場合
      if(isset(obj.connectToken)) {
        isSyncBrowser = true;
        if (connectToken !== obj.connectToken) return false;
      }
      //資料共有の場合
      if(!isSyncBrowser && (obj === null || obj === undefined || obj.responderId === null || Number(obj.responderId) !== Number(<?=$userInfo["id"]?>))) {
        return false;
      }

      $("#afs-popup").hide();
      $("#rsh-popup").addClass("show");
      var contHeight = $('#rsh-popup-content').height();
      $('#rsh-popup-frame').css('height', contHeight);
      $scope.$apply();
      $('#rsh-popup-content').jrumble({
        x: 15, //横の揺れ幅を設定
        y: 0, //縦の揺れ幅を設定
        rotation: 0 //回転角度の幅を設定
      });
      $('#rsh-popup-content').trigger('startRumble');
      setTimeout(function(){
        $('#rsh-popup-content').trigger('stopRumble');
      },250);
    });

    socket.on('activeOpCnt', function(data){
      var obj = JSON.parse(data);
      $scope.oprCnt = obj.count;
      if ( obj.userId === myUserId ) {
        if ( obj.active ) {
          chgOpStatusView("<?=C_OPERATOR_ACTIVE?>");
        }
        else {
          chgOpStatusView("<?=C_OPERATOR_PASSIVE?>");
        }
      }
      if ( obj.active ) {
        $scope.activeOperatorList[obj.userId] = "active";
      }
      else {
        delete $scope.activeOperatorList[obj.userId];
      }
      $scope.refreshUserPresences();

      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && strcmp(intval($scFlg), C_SC_ENABLED) === 0 ) :  ?>
            // チャット対応上限を設定
            if ( obj.hasOwnProperty('scInfo') && obj.scInfo.hasOwnProperty(<?=$muserId?>) ) {
              $scope.scInfo.remain = (isNumber(<?=$scNum?>)) ? Number(<?=$scNum?>) : 0 ;
              $scope.scInfo.remain -= Number(obj.scInfo[<?=$muserId?>]);
            }
            else {
              $scope.scInfo.remain = 0;
            }
      <?php endif; ?>
    });

    socket.on('unsetUser', function(data){
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined ) {
        if ( obj.accessType !== _access_type_host ) {
          delete $scope.monitorList[obj.tabId];
          $scope.chatList = $scope.chatList.filter(function(v){
            return (v !== this.t);
          }, {t: obj.tabId});

          <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && strcmp(intval($scFlg), C_SC_ENABLED) === 0 ) :  ?>
                // チャット対応上限を設定
                if ( obj.hasOwnProperty('scInfo') && obj.scInfo.hasOwnProperty(<?=$muserId?>) ) {
                  $scope.scInfo.remain = (isNumber(<?=$scNum?>)) ? Number(<?=$scNum?>) : 0 ;
                  $scope.scInfo.remain -= Number(obj.scInfo[<?=$muserId?>]);
                }
                else {
                  $scope.scInfo.remain = 0;
                }
          <?php endif; ?>

        }
        else {
          socket.emit("requestSyncStop", obj);
        }
      }
    });

    socket.on('disconnect', function(data) {
      $scope.monitorList = {};
    });

    // =======================================
    //   チャット関連受信ここから
    // =======================================

    // チャット接続結果
    socket.on("chatStartResult", function(d){
      var obj = JSON.parse(d);
      var prev = angular.copy($scope.monitorList[obj.tabId].chat);
      $scope.monitorList[obj.tabId].chat = obj.userId;

      if (Number(obj.messageType) === 998) {
        $scope.chatOpList.push(obj.userId);
        if ( obj.userId === myUserId ) {
          $scope.achievement = obj.achievementFlg;
        }
      }


      if ( obj.userId === myUserId && obj.ret ) {
        pushToChatList(obj.tabId);
        // 既読にする
        chatApi.isReadMessage($scope.monitorList[obj.tabId]);
      }
      else {
        $scope.chatList = $scope.chatList.filter(function(v){
          return (v !== this.t);
        }, {t: obj.sincloSessionId});

        // 前回の担当が自分だった場合
        if ( prev === myUserId && obj.ret ) {
          $("#sendMessage").val("").blur();
        }
      }
      if ( obj.tabId === chatApi.tabId ) {
        var chat = {
          id: obj.id,
          achievementFlg: "",
          messageReadFlg: "",
          created: obj.created,
          sort: Number(obj.created),
          messageType: Number(obj.messageType),
          userId: obj.userId
        };
        $scope.messageList.push(chat);
        scDown(); // チャットのスクロール
      }
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && strcmp(intval($scFlg), C_SC_ENABLED) === 0 ) :  ?>
            // チャット対応上限を設定
            if ( obj.hasOwnProperty('scInfo') && obj.scInfo.hasOwnProperty(<?=$muserId?>) ) {
              $scope.scInfo.remain = (isNumber(<?=$scNum?>)) ? Number(<?=$scNum?>) : 0 ;
              $scope.scInfo.remain -= Number(obj.scInfo[<?=$muserId?>]);
            }
            else {
              $scope.scInfo.remain = 0;
            }
      <?php endif; ?>
    });

    // チャット接続終了
    socket.on("chatEndResult", function(d){
      var obj = JSON.parse(d);
      if ( 'tabId' in obj && obj.tabId in $scope.monitorList && 'chat' in $scope.monitorList[obj.tabId] ) {
        $scope.chatList = $scope.chatList.filter(function(v){
          return (v !== this.t);
        }, {t: obj.tabId});
        $scope.monitorList[obj.tabId].chat = null;
      } else if('tabId' in obj && isset($scope.detail) && $scope.detailId === obj.tabId) {
        $scope.chatList = $scope.chatList.filter(function(v){
          return (v !== this.t);
        }, {t: obj.tabId});
        $scope.detail.chat = null;
      }
      if ( obj.tabId === chatApi.tabId ) {
        var chat = {
          sort: Number(obj.created),
          messageType: Number(obj.messageType),
          userId: obj.userId
        };
        $scope.messageList.push(chat);
        scDown(); // チャットのスクロール
      }
<?php if ( $coreSettings[C_COMPANY_USE_CHAT] && strcmp(intval($scFlg), C_SC_ENABLED) === 0 ) :  ?>
      $scope.scInfo.remain = (isNumber(<?=$scNum?>)) ? Number(<?=$scNum?>) : 0 ;
      if ( obj.hasOwnProperty('scInfo') && obj.scInfo.hasOwnProperty(<?=$muserId?>) ) {
        $scope.scInfo.remain -= Number(obj.scInfo[<?=$muserId?>]);
      }
      $scope.checkChatArea(); // チャットエリアの表示/非表示
<?php endif; ?>


    });

    // チャットメッセージ群の受信
    socket.on("chatMessageData", function(d){
      var obj = JSON.parse(d); $scope.achievement = "", $scope.chatOpList = [];

      if(obj.getMessageToken !== chatApi.getMessageToken) return;

      Object.keys(obj.chat.messages).forEach(function(key, idx, arr) {
        if (key.indexOf('_') !== -1) return;
        var chat = {};
        if ( typeof(obj.chat.messages[key]) === "object" ) {
          chat = obj.chat.messages[key];
        }
        else {
          chat.text = obj.chat.messages[key];
        }
        if (Number(chat.messageType) === 998) {
          $scope.chatOpList.push(chat.userId);
          if ( chat.userId === myUserId ) {
            $scope.achievement = String(chat.achievementFlg);
          }
        }
        chat.sort = Number(key);
        var exists = false;
        $scope.messageList.some(function(obj){
          if(obj.id === chat.id) {
            exists = true;
            return true;
          }
        });
        if(!exists) {
          $scope.messageList.push(chat);
          scDown(); // チャットのスクロール
        }
      });

      if ( $scope.monitorList[obj.tabId].chat === myUserId ) {
        // 既読にする(ok)
        chatApi.isReadMessage($scope.monitorList[obj.tabId]);
      }
    });
    // チャットメッセージ送信結果
    socket.on("sendChatResult", function(d){
      var obj = JSON.parse(d),
          elm = document.getElementById('sendMessage');
      if(obj.customerInfo && obj.notifyToCompany) {
        if(!(obj.tabId in $scope.monitorList)) {
          pushToList(obj.customerInfo);
          if ('chat' in obj && String(obj.chat) === "<?=$muserId?>") {
            pushToChatList(obj.tabId);
          }
        }
      }
      if ( !(obj.tabId in $scope.monitorList) ) return false;
      if ( obj.ret ) {
        // 対象のタブを開いている場合
        if ( obj.sincloSessionId === chatApi.sincloSessionId ){
          var chat = JSON.parse(JSON.stringify(obj));
          chat.sort = Number(obj.sort);
          $scope.messageList.push(chat);
          // 通知表示可能で、サイト訪問者からのメッセージだったら
          if(isShowChatReceiver() && obj.messageType === 1) {
            notify(obj.message); // 通知を出す
          } else {
            scDown(); // チャットのスクロール
          }
        }
        if (Number(obj.messageType) === chatApi.messageType.company || Number(obj.messageType) === chatApi.messageType.sorry) {
          // 入力したメッセージを削除
          if ( obj.tabId === chatApi.tabId && obj.userId === myUserId ) {
            elm.value = "";
          }
          // 以降、受信時のみの処理
          return false;
        }
        // Sorryメッセージが送られている場合は通知しない
        if (obj.hasOwnProperty('opFlg') && !obj.opFlg) {
          $scope.monitorList[obj.tabId].chatUnreadCnt = 0;
          $scope.monitorList[obj.tabId].chatUnreadId = null;
          return false;
        }

        // 未読数加算（自分が対応していないとき）
        if((obj.hasOwnProperty('notifyToCompany') && obj.notifyToCompany)) {
          $scope.monitorList[obj.tabId].chatUnreadCnt++;
          $scope.monitorList[obj.tabId].chatUnreadId = obj.chatId;
          $scope.ngChatApi.notification($scope.monitorList[obj.tabId]);
        }

        // 既読にする(対象のタブを開いている、且つ自分が対応しており、フォーカスが当たっているとき)
        if ( obj.tabId === chatApi.tabId && $scope.monitorList[obj.tabId].chat === myUserId && $("#sendMessage").is(":focus") ) {
            chatApi.isReadMessage($scope.monitorList[obj.tabId]);
        }

      }
      else {
        alert('メッセージの送信に失敗しました。');
      }
    });

    // チャット情報取得関数
    socket.on('sendChatInfo', function(d){
      var obj = JSON.parse(d);
      if ( !(obj.tabId in $scope.monitorList) ) return false;
      if ('chatUnreadId' in obj) {
        $scope.monitorList[obj.tabId].chatUnreadId  = obj.chatUnreadId;
      }
      if ('chatUnreadCnt' in obj) {
        $scope.monitorList[obj.tabId].chatUnreadCnt = obj.chatUnreadCnt;
      }
    });

    // チャットメッセージ既読処理結果関数
    socket.on('retReadChatMessage', function(d){
      var obj = JSON.parse(d);
      Object.keys($scope.monitorList).forEach(function(key) {
        if ($scope.monitorList[key].sincloSessionId === obj.sincloSessionId) {
          $scope.monitorList[key].chatUnreadId = null;
          $scope.monitorList[key].chatUnreadCnt = 0;
        }
      });
    });

    // チャット入力中ステータスの要求リクエスト
    socket.on('reqTypingMessage', function(d){
      var obj = JSON.parse(d);
      if (
        $scope.monitorList[obj.tabId].chat === myUserId && // 自身が対応中
        obj.tabId === chatApi.tabId && // 詳細画面を開いている
        document.getElementById('sendMessage').value !== ""  // メッセージが入力されている
      ) {
        emit('retTypingMessage', {
          type: chatApi.observeType.cnst.company, // company
          to: obj.from,
          tabId: chatApi.tabId,
          message: document.getElementById('sendMessage').value,
          status: $scope.isset(chatApi.observeType.timer)
        });
      }
    });

    // チャット入力中ステータスの要求リクエストの応答
    socket.on('resTypingMessage', function(d){
      var obj = JSON.parse(d);

      // 対象のタブを開いていないとき
      if ( obj.tabId !== chatApi.tabId ) return false;

      if ( Number(obj.type) === chatApi.observeType.cnst.company ) {
          $scope.typingMessageSe = obj.message;
      }
    });

    // チャット入力中ステータスの受信
    socket.on('receiveTypeCond', function(d){
      var obj = JSON.parse(d);
      // 対象のタブを開いていないとき
      if ( obj.tabId !== chatApi.tabId && obj.sincloSessionId !== chatApi.sincloSessionId ) return false;

      if ( obj.status === false && Number(obj.type) !== chatApi.observeType.cnst.company ) {
        obj.message = "";
      }

      // 企業側がメッセージ入力中
      if ( Number(obj.type) === chatApi.observeType.cnst.company ) {
        // チャット対応者が自身であるが、メッセージが異なる場合
        if (
         ("chat" in $scope.monitorList[obj.tabId]) &&
         $scope.monitorList[obj.tabId].chat === myUserId &&
         obj.message !== document.getElementById('sendMessage').value
        ) {
          chatApi.observeType.emit(chatApi.tabId, chatApi.sincloSessionId, status);
        }
        else {
          $scope.typingMessageSe = obj.message;
        }

      }
      // 消費者側がメッセージ入力中
      else {
        $scope.typingMessageRe[obj.sincloSessionId] = obj.message;
      }
      if(!isShowChatReceiver()) {
        scDown();
      }
    });

    // =======================================
    //   チャット関連受信ここまで
    // =======================================

    // =======================================
    // 　ビデオチャット関連ここから
    // =======================================
    // ビデオチャット開始許可通知
    socket.on('videochatConfirmOK', function(d){
      // 担当しているユーザー かチェック
      var obj = JSON.parse(d), url;
      var sincloData = {
        from: obj.receiverID, // 管理者側はtabIdが無いのでリアルモニタ画面のsocket.idで代用
        to: obj.tabId,
      }
      url  = "<?php echo C_PATH_NODE_FILE_SERVER ?>/webcam.html?h=true&sincloData=" + encodeURIComponent(JSON.stringify(sincloData));

      modalFunc.set.call({
        option: {
          url:  url, // FIXME
          tabId: obj.receiverID,
          width: 300,
          height: 300
        }
      });
    });


    // =======================================
    // 　入力補助関連ここから
    // =======================================
    $scope.entryWordList = <?php echo json_encode($dictionaryList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
    // フィルター
    $scope.entryWordSearch = function(list){
      var array = [];
      var keys = Object.keys(list);
      angular.forEach(list, function(item){
        for(var key in item) {
          if (item[key].label.indexOf($scope.searchWord) === 0 || angular.isUndefined($scope.searchWord) ) {
            array.push(item[key]);
          }
        }
      });
      return array;
    };

    function getEntryWordSearch(list){
      var array = [];
      var keys = Object.keys(list);
      angular.forEach(list, function(item){
        if (item.label.indexOf($scope.searchWord) === 0 || angular.isUndefined($scope.searchWord) ) {
          array.push(item);
        }
      });
      return array;
    };

    function dictionarySet(name){
        $.ajax({
        type: 'post',
        dataType: 'html',
        cache: false,
        data: name,
        url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'resCategoryDictionaryEdit'])?>",
        success: function(html){
         }
        });
      }

    // テキストエリア
    $("#sendMessage").on("keydown", function(e) {
      //定型文ポップアップの表示処理
      //佐藤作業中
      if ( e.keyCode === 40 ) {  //↓キー押下時
        var target = e.target;
        var html = null;
        if ( target.selectionStart === target.selectionEnd
          && target.selectionStart === target.value.length ) {
          $.ajax({
            type: 'post',
            dataType: 'html',
            cache: false,
            url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'openCategoryDictionaryEdit'])?>",
            success: function(html){
              modalOpen.call(window, html, 'p-category-dictionary-edit', '定型文選択', 'moment');
              $("#wordSearchCond").focus();
              $scope.entryWord = 0;
              $scope.searchWord = "";
              $scope.$apply();
              entryWordApi.init();
              var positionTop = 0;
              var wy = $('#sub_contents.ui-draggable.ui-draggable-handle').offset().top;
              var wx = $('#sub_contents.ui-draggable.ui-draggable-handle').offset().left;   // ウインドウの左上座標
              $("#popup #popup-frame-base #popup-frame.p-category-dictionary-edit").css({
                  top:(wy + 50) + "px",
                  left:(wx + 80) + "px"
              });

              //全てのタブの要素を取得
              var allTabList = document.querySelectorAll('[id^="ui-id-"]');
              for (var i = 0; i < allTabList.length; i++) {
                //全角を2、半角を1バイトとして長さを取得
                var length = countLength(allTabList[i].text);
                //全角バイトを超えていなかったらタブの長さを固定
                if(length < 12){
                  allTabList[i].style.width = '100px';
                  allTabList[i].style.textAlign = 'center';
                }
              }
              //タブの高さごとの配列を取得
              var tobTopList = getTabTopList(allTabList);
              //タブが一行だったら上下ボタンは非表示
              if(tobTopList.length == 1){
                document.getElementById("category_select_button").style.display="none";
              }

              function countLength(str) {
                var r = 0;
                for (var i = 0; i < str.length; i++) {
                    var c = str.charCodeAt(i);
                    // Shift_JIS: 0x0 ～ 0x80, 0xa0 , 0xa1 ～ 0xdf , 0xfd ～ 0xff
                    // Unicode : 0x0 ～ 0x80, 0xf8f0, 0xff61 ～ 0xff9f, 0xf8f1 ～ 0xf8f3
                    if ( (c >= 0x0 && c < 0x81) || (c == 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
                        r += 1;
                    } else {
                        r += 2;
                    }
                }
                return r;
              }

              $('#popup-frame').draggable({
                handle: "#popup-title",
                stop: function() {
                  // スクロールを使用するか
                  var subCon = document.getElementById('popup-frame');
                  // 詳細画面が表示されている場合
                  if (document.getElementById('customer_sub_pop').style.display === "block") {
                    /* position-top */
                    if ($("#popup-frame").css("top").indexOf('px') < 0) return false;
                    var subConTop = Number($("#popup-frame").css("top").replace("px", ""));

                    // ポップアップが画面外（上）に潜った場合の対処
                    var calc = subConTop - 60;
                    if (calc < 0) {
                      subCon.style.top = "60px";
                    }

                    // ポップアップが画面外（下）に潜った場合の対処
                    var subHeader = document.getElementById('popup-title'); // モーダル内のヘッダー
                    var calc = window.innerHeight - (subConTop + Number(subHeader.offsetHeight));
                    if (calc < 0) {
                      subCon.style.top = window.innerHeight - Number(subHeader.offsetHeight) + "px";
                    }

                    /* position-left */
                    if ($("#popup-frame").css("left").indexOf('px') < 0) return false;

                    var subConLeft = Number($("#popup-frame").css("left").replace("px", ""));
                    // ポップアップが画面外（左）に潜った場合の対処
                    if (subConLeft < 0) {
                      subCon.style.left = "0";
                    }

                    // ポップアップが画面外（右）に潜った場合の対処
                    var sideBar = document.getElementById('sidebar-main');
                    var widthArea = window.innerWidth - Number(sideBar.offsetWidth); // 有効横幅
                    if ((widthArea - subConLeft) < 50) {
                      subCon.style.left = widthArea - 80 + "px";
                    }
                  }
                }
              });

              //カテゴリースクロール対応
              //上スクロール
               $("#category_up_btn").on('click', function(e){
                //タブリスト取得
                var allTabList = document.querySelectorAll('[id^="ui-id-"]');
                //タブの高さごとの配列を取得
                var tobTopList = getTabTopList(allTabList);
                //現在選択されているタブインデックス
                var select_tab_index = Number(document.getElementById("select_tab_index").value);
                //現在選択されている行のインデックス
                var select_line_index = Number(document.getElementById("select_line_index").value);
                if(select_line_index != 0){
                  //上スライドされたら必ず下ボタンはグリーンになる
                  document.getElementById("category_down_btn").className="btn-shadow greenBtn commontooltip";
                  //上;
                  select_line_index = (select_line_index - 1);
                  //その行の先頭のIDを取得
                  var id = tobTopList[select_line_index][0];
                  //そのIDまでスクロール
                  document.getElementById(id).scrollIntoView(false);
                  //変更された行IDを保持
                  document.getElementById("select_line_index").value = select_line_index;
                  if(select_line_index == 0){
                    //上限に到達したら上ボタンをグレーにする
                    document.getElementById("category_up_btn").className="btn-shadow grayBtn commontooltip";
                  }
                }
              });

              //下スクロール
               $("#category_down_btn").on('click', function(e){
                //タブリスト取得
                var allTabList = document.querySelectorAll('[id^="ui-id-"]');
                //タブの高さごとの配列を取得
                var tobTopList = getTabTopList(allTabList);
                //現在選択されているタブインデックス
                var select_tab_index = Number(document.getElementById("select_tab_index").value);
                //現在選択されている行のインデックス
                var select_line_index = Number(document.getElementById("select_line_index").value);
                if(select_line_index != (tobTopList.length - 1)){
                  //下スライドされたら必ず上ボタンはグリーンになる
                  document.getElementById("category_up_btn").className="btn-shadow greenBtn commontooltip";
                  //下;
                  select_line_index = (select_line_index + 1);
                  //その行の先頭のIDを取得
                  var id = tobTopList[select_line_index][0];
                  //そのIDまでスクロール
                  document.getElementById(id).scrollIntoView(false);
                  //変更された行IDを保持
                  document.getElementById("select_line_index").value = select_line_index;
                  if(select_line_index == (tobTopList.length - 1)){
                    //下限に到達したら下ボタンをグレーにする
                    document.getElementById("category_down_btn").className="btn-shadow grayBtn commontooltip";
                  }
                }
              });

              //タブの高さごとの配列を取得
              function getTabTopList(allTabList){
                var tobTopList = [];
                var topAllay = [];
                for (var i = 0; i < allTabList.length; i++) {
                  var id = allTabList[i].id;
                  topAllay.push($("#"+id).offset().top);
                }
                var nawtop = topAllay[0];
                var tabAllay = [];
                for (var i = 0; i < topAllay.length; i++) {
                  tabAllay.push(allTabList[i].id);
                  if(nawtop == topAllay[(i + 1)]){
                    var linechange = 0;
                  }
                  else{
                    var linechange = 1;
                    nawtop = topAllay[(i + 1)];
                  }
                  if(linechange == 1 || (topAllay.length - 1) == i){
                    tobTopList.push(tabAllay);
                    tabAllay = [];
                  }
                }
                return tobTopList;
              }

              //閉じるボタンが押された時
              $("#popupCloseBtn").on('click', function(e){
                closeCategoryDictionary();
              });

              //ポップアップ全体監視(ポップアップの何処かにクリックが当たると)
              $("#popup-content").on('click', function(e){
                //検索テクストエリアにフォーカス
                $("#wordSearchCond").focus();
              });

              $('#wordSearchCond').on('keydown', function(e) {
                    if(e.keyCode === 38 || e.keyCode === 40) {
                        e.preventDefault();
                    }
              });

              var userAgent = window.navigator.userAgent.toLowerCase();
              var IMEOnEntering = false;
              $("#popup-content").on("keydown", function(e){
                if(e.keyCode === 229) {
                  IMEOnEntering = true;
                } else if(userAgent.indexOf('gecko')) {
                  // FireFox用の処理（このタイミングで日本語入力かどうかわからないため一応ON）
                  // 後述のkeypressイベントで判断する
                  console.log("FF IME ON");
                  IMEOnEntering = true;
                } else {
                  IMEOnEntering = false;
                }
              });

              $("#popup-content").on("keypress", function(e){
                // FireFoxでkeypress時にkeyCodeが0の場合IME入力ではない
                if(userAgent.indexOf('gecko') && (e.keyCode === 0 || e.keyCode === 13)) {
                  console.log("FF IME off");
                  IMEOnEntering = false;
                }
              });

              //ポップアップ全体監視(ポップアップのどこかにフォーカスがある状態でキーを押下すると)
              $("#popup-content").on('keyup', function(e){
                var search_word = document.getElementById("wordSearchCond").value;
                //検索文字列があるか
                if(search_word){
                  //検索モード
                  document.getElementById("mode_flg").value = 1;
                }
                else{
                  //通常モード
                  document.getElementById("mode_flg").value = 0;
                  //入力が無くなったら通常モード
                  document.getElementById("serect_tab_mode").style.display="";
                  document.getElementById("word_search_mode").style.display="none";
                  document.getElementById("wordSearchChk").value = "";
                }
                if(document.getElementById("mode_flg").value == 0){
                  var keytime = document.getElementById("keytime").value;
                  //二重操作防止
                  if(keytime != e.timeStamp){
                    document.getElementById("keytime").value = e.timeStamp;
                    //検索モードだったら無視する
                    if(document.getElementById("mode_flg").value == 0){
                      //キー押下時の処理
                      var select_tab_index = document.getElementById("select_tab_index").value;
                      //現在選択されているタブのワードリストを取得する
                      var selectTabWordList = $scope.entryWordList[select_tab_index];
                      //ワードリストが空の時は以下処理は実行しない
                      if(selectTabWordList.length > 0){
                        //現在[dictionarySelected~]クラスがついている行のidnameを取得してidだけを抽出する
                        var selected = document.querySelector('[id^="item"].dictionarySelected'+select_tab_index);
                        var selected_id = selected.id;
                        selected_id = Number(selected_id.substr(4));
                        //idから現在選択されているキーを取得する
                        for(var key in selectTabWordList) {
                          if(selected_id === selectTabWordList[key]["id"]){
                            var selected_key = Number(key);
                          }
                        }
                      }
                      if ( e.keyCode === 13 ) { // Enter
                        if(!IMEOnEntering) {
                          var list = getEntryWordSearch($scope.entryWordList[select_tab_index]);
                          if (list.length > 0) {
                            entryWordApi.push(list[selected_key].label);
                          }
                          entryWordApi.prev();
                          //ポップアップを閉じる
                          closeCategoryDictionary();
                        }
                        return false;
                      }
                      if ( e.keyCode === 38 ) { // 上キー
                        if ( selected_key > 0 ) {
                          selected_key = selected_key - 1;
                          var prev = $("#item" + selectTabWordList[selected_key]["id"]);
                          if (prev.prop('id')){
                            //もともとあったセレクトクラスを除外
                            var selectedClassName  = document.getElementById(selected.id).className;
                            document.getElementById("item" + selectTabWordList[selected_key]["id"]).className = selectedClassName;
                            document.getElementById(selected.id).className = "dictionaryWord ng-binding ng-scope";
                            //スクロール判定のために次の次のリストが存在するかどうかを判定する
                            if ( selected_key > 0 ) {
                              var nextnext = selected_key - 1;
                              var t = $("#item" + selectTabWordList[nextnext]["id"]).offset().top; // ターゲットの位置取得
                              var c = $("#wordList"+select_tab_index).offset().top; // 基準となるulの位置取得
                              if(c > t){
                                //新しくセレクトされた要素までスクロール
                                document.getElementById("item" + selectTabWordList[selected_key]["id"]).scrollIntoView(true);
                              }
                            }
                            else{
                              //新しくセレクトされた要素までスクロール
                              document.getElementById("item" + selectTabWordList[selected_key]["id"]).scrollIntoView(true);
                            }
                          }
                        }
                        else {
                          //ポップアップを閉じる
                          closeCategoryDictionary(); // 元の操作に戻る
                          return false;
                        }
                      }
                      if ( e.keyCode === 27 ) { // ESCキー
                        //ポップアップを閉じる
                        closeCategoryDictionary(); // 元の操作に戻る
                        return false;
                      }
                      if ( e.keyCode === 40 ) { // 下
                        if ( getEntryWordSearch($scope.entryWordList[select_tab_index]).length > (selected_key + 1) ) {
                          selected_key = selected_key + 1;
                          var next = $("#item" + selectTabWordList[selected_key]["id"]);
                          if (next.prop('id')){
                            //もともとあったセレクトクラスを除外
                            var selectedClassName  = document.getElementById(selected.id).className;
                            document.getElementById("item" + selectTabWordList[selected_key]["id"]).className = selectedClassName;
                            document.getElementById(selected.id).className = "dictionaryWord ng-binding ng-scope";
                            //スクロール判定のために次の次のリストが存在するかどうかを判定する
                            if ( getEntryWordSearch($scope.entryWordList[select_tab_index]).length > (selected_key + 1) ) {
                              var nextnext = selected_key + 1;
                              var t = $("#item" + selectTabWordList[nextnext]["id"]).offset().top; // ターゲットの位置取得
                              var c = $("#wordList"+select_tab_index).offset().top; // 基準となるulの位置取得
                              var h = $("#wordList"+select_tab_index).height(); //基準となるuiの縦幅
                              if(c + h < t){
                                //新しくセレクトされた要素までスクロール
                                document.getElementById("item" + selectTabWordList[selected_key]["id"]).scrollIntoView(false);
                              }
                            }
                            else{
                              //新しくセレクトされた要素までスクロール
                              document.getElementById("item" + selectTabWordList[selected_key]["id"]).scrollIntoView(false);
                            }
                          }
                        }
                        return false;
                      }
                      if ( e.keyCode === 37 ) { // 左
                        select_tab_index = Number(select_tab_index);
                        if ( select_tab_index > 0 ) {
                          select_tab_index--;
                          $( "#categoryTabs" ).tabs({ active: select_tab_index });
                          var allTabList = document.querySelectorAll('[id^="ui-id-"]');
                          var id = allTabList[select_tab_index].id;
                          //id = Number(id.substr(10));
                          //タブの高さごとの配列を取得
                          var tobTopList = getTabTopList(allTabList);
                          //タブの高さリストから該当IDを検索
                          for(var key in tobTopList) {
                            for(var i_key in tobTopList[key]) {
                              if(tobTopList[key][i_key] == id){
                                var c_key = key;
                              }
                            }
                          }
                          //現在選択されている行のインデックス
                          var select_line_index = Number(document.getElementById("select_line_index").value);
                          if(Number(c_key) != select_line_index){
                            //現在選択されている行インデックスを変更
                            document.getElementById("select_line_index").value = c_key;
                            //上スライドされたら必ず下ボタンはグリーンになる
                            document.getElementById("category_down_btn").className="btn-shadow greenBtn commontooltip";
                            if(c_key == 0){
                              //上限に到達したら上ボタンをグレーにする
                              document.getElementById("category_up_btn").className="btn-shadow grayBtn commontooltip";
                            }
                          }
                          allTabList[select_tab_index].scrollIntoView(false);
                          //新しくセレクトされた要素までスクロール
                          document.getElementById("select_tab_index").value = select_tab_index;
                        }
                        return false;
                      }
                      if ( e.keyCode === 39 ) { // 右
                        select_tab_index = Number(select_tab_index);
                        if ( $scope.entryWordList.length > (select_tab_index + 1) ) {
                          select_tab_index++;
                          $( "#categoryTabs" ).tabs({ active: select_tab_index });
                          var allTabList = document.querySelectorAll('[id^="ui-id-"]');
                          var id = allTabList[select_tab_index].id;
                          //id = Number(id.substr(10));
                          //タブの高さごとの配列を取得
                          var tobTopList = getTabTopList(allTabList);
                          //タブの高さリストから該当IDを検索
                          for(var key in tobTopList) {
                            for(var i_key in tobTopList[key]) {
                              if(tobTopList[key][i_key] == id){
                                var c_key = key;
                              }
                            }
                          }
                          //現在選択されている行のインデックス
                          var select_line_index = Number(document.getElementById("select_line_index").value);
                          if(Number(c_key) != select_line_index){
                            //現在選択されている行インデックスを変更
                            document.getElementById("select_line_index").value = c_key;
                            //下スライドされたら必ず上ボタンはグリーンになる
                            document.getElementById("category_up_btn").className="btn-shadow greenBtn commontooltip";
                            if(c_key == (tobTopList.length - 1)){
                              //下限に到達したら下ボタンをグレーにする
                              document.getElementById("category_down_btn").className="btn-shadow grayBtn commontooltip";
                            }
                          }
                          allTabList[select_tab_index].scrollIntoView(false);
                          document.getElementById("select_tab_index").value = select_tab_index;
                        }
                        return false;
                      }
                      entryWordApi.sc = 0;
                    }
                  }
                }
                else{
                  //上下エンターキーの判定
                  if((!IMEOnEntering && e.keyCode === 13)||(e.keyCode === 38)||(e.keyCode === 40)){
                    var searchkeytime = document.getElementById("searchkeytime").value;
                    //二重操作防止
                    if(searchkeytime != e.timeStamp){
                      document.getElementById("searchkeytime").value = e.timeStamp;
                      //検索結果に対する操作
                      var search_word = document.getElementById("wordSearchCond").value;
                      //検索文字列があるか
                      if(search_word){
                        var searchItemList = document.querySelectorAll('[id^="searchItem"]');
                        var res = [];
                        for (var i = 0; i < searchItemList.length; i++) {
                          if(searchItemList[i].style.display != "none"){
                            res.push(searchItemList[i]);
                          }
                        }
                        //現在表示されているリストが存在すれば
                        if(res){
                          //現在dictionarySearchSelectedクラスを持っているIDを取得する
                          var selected = document.querySelector('[id^="searchItem"].dictionarySearchSelected');
                          var selected_id = selected.id;
                          var selected_key = '';
                          for (var i = 0; i < res.length; i++) {
                            if(res[i].id == selected_id){
                              selected_key = i;
                            }
                          }
                          //selected_id = Number(selected_id.substr(10));
                          //各ボタンの判定
                          if (e.keyCode === 13) { // Enter
                            if(!IMEOnEntering) {
                              var id = Number(selected_id.substr(10));
                              //$scope.entryWordListの中のどこに該当するか特定する
                              var wordlist = $scope.entryWordList;
                              for (var key in wordlist) {
                                for (var v_key in wordlist[key]) {
                                  if (wordlist[key][v_key]["id"] == id) {
                                    var select_tab_index = key;
                                    var select_index = v_key;
                                  }
                                }
                              }
                              var list = getEntryWordSearch($scope.entryWordList[select_tab_index]);
                              closeCategoryDictionary();
                              entryWordApi.push(list[select_index].label);
                            }
                            return false;
                          }
                          if (e.keyCode === 38) { // 上キー
                            if ( selected_key > 0 ) {
                              selected_key = selected_key - 1;
                              //もともとあったセレクトクラスを除外
                              var selectedClassName  = document.getElementById(selected_id).className;
                              document.getElementById(res[selected_key].id).className = selectedClassName;
                              document.getElementById(selected_id).className = "dictionaryWord ng-binding ng-scope";
                              //スクロール判定のために次の次のリストが存在するかどうかを判定する
                              if ( selected_key > 0 ) {
                                var nextnext = selected_key - 1;
                                var t = $("#" + res[nextnext].id).offset().top; // ターゲットの位置取得
                                var c = $("#allWordList").offset().top; // 基準となるulの位置取得
                                if(c > t){
                                  //新しくセレクトされた要素までスクロール
                                  document.getElementById(res[selected_key].id).scrollIntoView(true);
                                }
                              }
                              else{
                                //新しくセレクトされた要素までスクロール
                                document.getElementById(res[selected_key].id).scrollIntoView(true);
                              }
                            } else {
                              closeCategoryDictionary();
                            }
                            return false;
                          }
                          if (e.keyCode === 40) { // 下キー
                            if ( res.length > (selected_key + 1) ) {
                              selected_key = selected_key + 1;
                              var next = res[selected_key].id;
                              //もともとあったセレクトクラスを除外
                              var selectedClassName  = document.getElementById(selected_id).className;
                              document.getElementById(res[selected_key].id).className = selectedClassName;
                              document.getElementById(selected_id).className = "dictionaryWord ng-binding ng-scope";
                              //スクロール判定のために次の次のリストが存在するかどうかを判定する
                              if ( res.length > (selected_key + 1) ) {
                                var nextnext = selected_key + 1;
                                var t = $("#" + res[nextnext].id).offset().top; // ターゲットの位置取得
                                var c = $("#allWordList").offset().top; // 基準となるulの位置取得
                                var h = $("#allWordList").height(); //基準となるuiの縦幅
                                if(c + h < t){
                                  //新しくセレクトされた要素までスクロール
                                  document.getElementById(res[selected_key].id).scrollIntoView(false);
                                }
                              }
                              else{
                                //新しくセレクトされた要素までスクロール
                                document.getElementById(res[selected_key].id).scrollIntoView(false);
                              }
                            }
                            return false;
                          }
                        }
                      }
                    }
                  }
                  else{
                    //一旦全て非表示
                    document.getElementById("categoryTabs-ALL").style.display="none";
                    document.getElementById("allWordList").style.display="none";
                    var searchItemList = document.querySelectorAll('[id^="searchItem"]');
                    for (var i = 0; i < searchItemList.length; i++) {
                      searchItemList[i].style.display="none";
                    }
                    onWordSearchCond(e);
                    return false;
                  }
                }
              });


              $("#wordSearchCond")
              .on('click', function(e){
                //検索モードのみ
                if(document.getElementById("mode_flg").value == 1){
                  var $input = $(this),
                  oldValue = $input.val();
                  if (oldValue == "") return;
                  setTimeout(function(){
                    //一旦全て非表示
                    document.getElementById("categoryTabs-ALL").style.display="none";
                    document.getElementById("allWordList").style.display="none";
                    var searchItemList = document.querySelectorAll('[id^="searchItem"]');
                    for (var i = 0; i < searchItemList.length; i++) {
                      searchItemList[i].style.display="none";
                    }
                    onWordSearchCond(e);
                  },1);
                }
              })
              .on('onchange', function(e){
                //検索モードのみ
                if(document.getElementById("mode_flg").value == 1){
                  //一旦全て非表示
                  document.getElementById("categoryTabs-ALL").style.display="none";
                  document.getElementById("allWordList").style.display="none";
                  var searchItemList = document.querySelectorAll('[id^="searchItem"]');
                  for (var i = 0; i < searchItemList.length; i++) {
                    searchItemList[i].style.display="none";
                  }
                  onWordSearchCond(e);
                }
              });

              //行クリック
              $("[id ^= wordList]").on('click', function(e){
                //カテゴリ
                var select_tab_index = document.getElementById("select_tab_index").value;
                var list = getEntryWordSearch($scope.entryWordList[select_tab_index]);
                if ( list.length > 0 ) {
                  closeCategoryDictionary();
                  entryWordApi.push(list[$(e.target).index()].label);
                  //entryWordApi.prev();
                }
                return false;
              });

              //検索モード行クリック
              $("#allWordList").on('click', function(e){
                //カテゴリ
                var id = e.target.id;
                if(id != 0){
                  id = Number(id.substr(10));
                  //$scope.entryWordListの中のどこに該当するか特定する
                  var wordlist = $scope.entryWordList;
                  for(var key in wordlist) {
                    for(var v_key in wordlist[key]) {
                      if(wordlist[key][v_key]["id"] == id){
                        var select_tab_index = key;
                        var select_index = v_key;
                      }
                    }
                  }
                  var list = getEntryWordSearch($scope.entryWordList[select_tab_index]);
                  closeCategoryDictionary();
                  entryWordApi.push(list[select_index].label);
                  }
                  return false;
              });

              //ポップアップを閉じるときの共通動作
              function closeCategoryDictionary(){
                modalClose();
                document.getElementById("popup-bg").className="";
                document.getElementById("popup-title").className="";
                document.getElementById("popup-main").className="";
                document.getElementById("popup-frame").className="p-cus-detail";
                $(".popup-frame").css('height', '100%');
                $("#sendMessage").focus();
              }

              //検索モード
              function onWordSearchCond(e){
                var search_word = document.getElementById("wordSearchCond").value;
                if((document.getElementById("mode_flg").value == 1) && (search_word != document.getElementById("wordSearchChk").value)){
                  //選択行の削除
                  var selected = document.querySelector('[id^="searchItem"].dictionarySearchSelected');
                  if(selected){
                    var selected_id = selected.id;
                    document.getElementById(selected_id).className = "dictionaryWord ng-binding ng-scope";
                  }
                }
                if(search_word){
                  //一文字でも入力があったら検索モード
                  document.getElementById("mode_flg").value = 1;
                  document.getElementById("serect_tab_mode").style.display="none";
                  document.getElementById("word_search_mode").style.display="";
                  var array = $scope.entryWordList;
                  var res = [];
                  for(var key in array) {
                    for(var v_key in array[key]) {
                      var text = array[key][v_key];
                      var test = text["label"];
                      if ( text["label"].indexOf(search_word) != -1) {
                        //strにhogeを含む場合の処理
                        res.push(text["id"]);
                      }
                    }
                  }
                  //検索に何かヒットしたら
                  if(res){
                    document.getElementById("categoryTabs-ALL").style.display="";
                    document.getElementById("allWordList").style.display="";
                    for(var r_key in res) {
                      //対応する行を表示
                      document.getElementById("searchItem"+res[r_key]).style.display="";
                      //検索にヒットしたリストの一番先頭にdictionarySearchSelectedクラスを付与する
                      if(r_key == 0 && (document.getElementById("wordSearchChk").value != search_word)){
                        document.getElementById("searchItem"+res[r_key]).className = "dictionaryWord ng-binding ng-scope dictionarySearchSelected";
                      }
                    }
                    //検索にヒットしたらwordSearchChkに検索文字列を格納しておく
                    document.getElementById("wordSearchChk").value = search_word;
                    //searchItem
                  }
                }
                else{
                  //入力が無くなったら通常モード
                  document.getElementById("mode_flg").value = 0;
                  document.getElementById("serect_tab_mode").style.display="";
                  document.getElementById("word_search_mode").style.display="none";
                  document.getElementById("wordSearchChk").value = "";
                }
              }
            }
          });

/* 旧定型文処理 */
//           var testarea_offset = $(this).offset();
//           var testarea_size = {
//             height: this.offsetHeight,
//             width: this.offsetWidth
//           };
//           var area = document.getElementById('wordListArea');
//           area.style.display = "block";
//           $("#wordSearchCond").focus();
//           $scope.entryWord = 0;
//           $scope.searchWord = "";
//           $scope.$apply();
//           entryWordApi.init();
//           return false;
/* 旧定型文処理 */
        }
      }
    });

//     function ajaxopenCategoryDictionary(){
//       return $.ajax({
//         type: 'post',
//         dataType: 'html',
//         cache: false,

//         success: function(html){
//           modalOpen.call(window, html, 'p-category-dictionary-edit', '定型文選択', 'moment');
//         }
//       });
//       ajaxopenCategoryDictionary().done(function(result) {
//         var test = result;
//     }).fail(function(result) {
//         var test = result;
//     });
//     }

//     ajaxopenCategoryDictionary().done(function(result) {
//         var test = result;
//     }).fail(function(result) {
//         var test = result;
//     });

    /* 【ここから】ワードリスト内 */

    // ワードリスト絞り込み
    $scope.searchKeydown = function(e){
      if ( e.keyCode === 13 ) { // Enter
        var list = $scope.entryWordSearch($scope.entryWordList);
        if ( list.length > 0 ) {
          entryWordApi.push(list[$scope.entryWord].label);
        }
        entryWordApi.prev();
        return false;
      }
      if ( e.keyCode === 38 ) { // 上キー
        if ( $scope.entryWord > 0 ) {
          $scope.entryWord--;
          var prev = $("#item" + $scope.entryWord);
          if (prev.prop('id')){
            entryWordApi.scroll(prev);
          }
        }
        else {
          entryWordApi.prev(); // 元の操作に戻る
          return false;
        }
      }
      if ( e.keyCode === 27 ) { // ESCキー
        entryWordApi.prev(); // 元の操作に戻る
        return false;
      }
      if ( e.keyCode === 40 ) { // 下
        if ( $scope.entryWordSearch($scope.entryWordList).length > ($scope.entryWord + 1) ) {
          $scope.entryWord++;
          var next = $("#item" + $scope.entryWord);
          if (next.prop('id')){
            entryWordApi.scroll(next);
          }
        }
        return false;
      }
      entryWordApi.sc = 0;

    };

    // ワードリスト（表示用※新定型文から）
//     $("#wordList")
//       .on('mouseover', function(e){
//       $scope.entryWord = $(e.target).index();
//       $scope.$apply();
//     })
//       .on('click', function(e){
//       var list = $scope.entryWordSearch($scope.entryWordList);
//       if ( list.length > 0 ) {
//         entryWordApi.push(list[$(e.target).index()].label);
//         entryWordApi.prev();
//       }
//       return false;
//     });
    //選択時のイベント
    // ワードリスト（表示用）
    $("#wordList")
      .on('mouseover', function(e){
      $scope.entryWord = $(e.target).index();
      $scope.$apply();
    })
      .on('click', function(e){
      var list = $scope.entryWordSearch($scope.entryWordList);
      if ( list.length > 0 ) {
        entryWordApi.push(list[$(e.target).index()].label);
        entryWordApi.prev();
      }
      return false;
    });

    /* 【ここまで】ワードリスト内 */

    // ドキュメントオブジェクト内
    $(document).on('click', function(e){
      entryWordApi.close();
    });

    // 過去チャットと現行チャット
    $(document).on("click", "#showChatTab > li", function(e){
      var className = $(this).data('type');
      angular.element("#showChatTab > li").removeClass("on");

      if ( className === "oldChat" ) {
        $scope.chatLogList = [];
        $scope.chatLogMessageList = [];
        angular.element("message-list-descript").attr("class", "off");
        $.ajax({
          type: 'GET',
          url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteGetChatList')) ?>",
          cache: false,
          data: {
            userId: $scope.detail.userId,
          },
          dataType: 'json',
          success: function(json){
            $scope.chatLogList = json;
            angular.element("message-list-descript").attr("class", "on");
            $scope.$apply();
          }
        });
      }
      else {
        className = "currentChat";
      }
      $("#showChatTab > li[data-type='" + className + "']").addClass("on");
      $("#chatContent > section").removeClass("on");
      $("#chatContent > #" + className).addClass("on");
    });


    $(document).ready(function(){
      $scope.ngChatApi.init();
    });

    // チャットエリアの監視
    angular.element(document).on("focus", "#sendMessage", function(){
      setTimeout(function(){
        if (!$("#sendMessage").is(":focus")) return false;
        if ( $scope.chatList.indexOf($scope.detailId) < 0 ) {
          chatApi.errorChatStart();
        }
      }, 3000);
    });

    // ポップアップをセンターに表示
    function setPositionOfPopup(){
      var subContent = document.getElementById("sub_contents");
      subContent.style.left = ((window.innerWidth-$("#sidebar-main").outerWidth()) - $("#sub_contents").outerWidth())/2 + "px";
      subContent.style.top = "100px";
    }

    // ポップアップをセンターに表示
    function setPositionOfPresenceView(){
      var subContent = document.getElementById("presenceView"),
          // リンクの位置を取得
          showPopupLinkObj = $('#showOperatorPresenceLink'),
          showPopupLinkOffset = showPopupLinkObj.offset();
      subContent.style.left = showPopupLinkOffset.left + "px";
      subContent.style.top =  showPopupLinkOffset.top + showPopupLinkObj.height() + 10 + "px";
    }

    // ポップアップをセンターに表示
    function setHeaderSizeOfPresenceView(){
      var displayTd = $('#presenceViewBody').find('td.displayName');
      var displayMaxTdWidth = 0;
      displayTd.each(function(index, val){
        if(displayMaxTdWidth < $(val).outerWidth()) displayMaxTdWidth = $(val).outerWidth();
      });
      var displayNameHeaderWidth = displayMaxTdWidth;
      if(!$('#statusHeader').hasClass('mac')) {
        displayNameHeaderWidth += 18;
      }
      var statusTdWidth = displayTd.next('td').outerWidth();
      $('#displayNameHeader').css("width", displayNameHeaderWidth+'px');
      $('td.displayName').css("width", displayMaxTdWidth+'px');
      $('#statusHeader').css("width", statusTdWidth+'px');
    }

    $scope.$watch('monitorList', function(newVal, oldVal){
      console.log("monitorList is changed");
    });
  }]);

  /**************************************************************
   *  資料共有　ここまで
   * ************************************************************/

  sincloApp.directive('ngOverView', function(){
    return {
      restrict: "E",
      scope: {
        text: "@",
        docid: "@"
      },
      template: '<span ng-mouseover="toggleOverView()" ng-mouseleave="toggleOverView()">{{::text}}</span>',
      link: function(scope, elem, attr){
        var ballons = angular.element('#ang-ballons');
        var ballon = document.createElement('div');
        ballon.classList.add("hide");
        ballon.textContent = scope.text;
        ballon.setAttribute('data-id', scope.docid);
        ballons.append(ballon);

        scope.toggleOverView = function(){
          var p = angular.element(elem).offset();
          ballon.style.top = p.top + "px";
          ballon.style.left = p.left + "px";
          ballon.classList.toggle("hide");
        };
      }
    };
  });

  sincloApp.directive('ngMultiSelector', function(){
    return {
      restrict: "E",
      template: '<selected data-elem-type="selector" ng-click="openMultiSelector()">{{selected}}</selected>' +
                '<ul>' +
                '  <li data-elem-type="selector" ng-repeat="(id, name) in tagList" ng-click="changAct(id)" ng-class="{selected: judgeSelect(id)}">{{name}}</li>' +
                '</ul>',
      link: function(scope, elem, attr){
        scope.openMultiSelector = function(){
          var e = angular.element(elem);
          if ( e.hasClass('show') ) {
            e.removeClass('show');
          }
          else {
            e.addClass('show');
          }
        };
        scope.selected = "-";
        scope.changAct = function(id){
          if ( scope.selectList.hasOwnProperty(id) ) {
            delete scope.selectList[id];
          }
          else {
            scope.selectList[id] = true;
          }
          var str = Object.keys(scope.selectList).map(function(item){
            return scope.tagList[item];
          }).join('、');
          scope.selected = ( str === "" ) ? "-" : str;
        };

        scope.judgeSelect = function(id){
          return (scope.selectList.hasOwnProperty(id));
        };

        scope.jParse = function(str){
          return JSON.parse(str);
        };
      }
    };
  });

  /**************************************************************
   *  資料共有　ここまで
   * ************************************************************/

  sincloApp.directive('ngCreateMessage', [function(){
    return {
      restrict: 'E',
      link: function(scope, elem, attr) {
        scope.createMessage(elem, scope.chat);

      }
    };
  }]);

  sincloApp.filter('limitTo', function(){
    return function(input, limit, scope) {
      var returnValue = input.slice(0, limit);
      returnValue.forEach(function(elm, index, array){
        if(!isset(scope.customerList[elm.userId])) {
          if(scope.requestedCustomerList.indexOf(elm.userId) === -1) {
            scope.getCustomerInfo(elm.userId, function(result){
              scope.customerList[elm.userId] = result;
              scope.$apply();
            });
            scope.requestedCustomerList.push(elm.userId);
          }
        } else {
          elm.customerInfo = scope.customerList[elm.userId];
        }
      });
      return returnValue;
    }
  });

  // 参考 http://stackoverflow.com/questions/14478106/angularjs-sorting-by-property
  sincloApp.filter('orderObjectBy', function(){
    return function(input, atr) {
      if (!angular.isObject(input)) return input;
      var array = [];
      for(var objectKey in input) {
        array.push(input[objectKey]);
      }
      var sortAsc = (atr.match(/^-{1}-{2}-{3}/) === null);
      var splitedOrder = atr.split("-").slice(1);
      var attribute1 =  splitedOrder[0];
      var attribute2 =  splitedOrder[1];
      var attribute3 = splitedOrder[2];
      array.sort(function(a, b){
        // 未読あり > 未読ありの対応中 > 対応中　＝　画面共有／資料共有対応中 > 何もない
        var a1 = (isNaN(parseInt(a[attribute1]))) ? 0 : 10000,
            b1 = (isNaN(parseInt(b[attribute1]))) ? 0 : 10000,
            a2 = (isNaN(parseInt(a[attribute2]))) ? 0 : 10,
            b2 = (isNaN(parseInt(b[attribute2]))) ? 0 : 10,
            a3 = (isNaN(parseInt(a[attribute3]))) ? 0 : 10,
            b3 = (isNaN(parseInt(b[attribute3]))) ? 0 : 10,
            calc = Math.abs(a1 - a2 - a3) - Math.abs(b1 - b2 - b3);
        if(calc > 0) {
          return -1;
        } else if (calc < 0) {
          return 1;
        } else if(a1 === 0 && b1 === 0 && a2 === 0 && b2 === 0 && a3 === 0 && b3 === 0) {
          // 各優先順位でステータス（ウィジェットオープン　＞　ウィジェット最小化　＞　ウィジェット非表示　＞　非アクティブ）で並び替え
          var astatus = (isNaN(parseInt(a['status']))) ? 0 : parseInt(a['status']),
              bstatus = (isNaN(parseInt(b['status']))) ? 0 : parseInt(b['status']);
          if(astatus !== bstatus) {
            return astatus - bstatus;
          } else {
            var atime = (isNaN(parseInt(a['time']))) ? 0 : parseInt(a['time']),
                btime = (isNaN(parseInt(b['time']))) ? 0 : parseInt(b['time']);
            return btime - atime;
          }
        } else {
          var atime = (isNaN(parseInt(a['time']))) ? 0 : parseInt(a['time']),
              btime = (isNaN(parseInt(b['time']))) ? 0 : parseInt(b['time']);
          return btime - atime;
        }
      });
      //console.log(JSON.stringify(array));
      return array;
    }
  });

  function _numPad(str){
    return ("0" + str).slice(-2);
  }

  /**
   * オペレータステータス一覧のソート処理
   */
  sincloApp.filter('orderOperatorStatus', function(){
    return function(input, scope) {
      if (!angular.isObject(input)) return input;
      var array = [];
      for(var objectKey in input) {
        array.push(input[objectKey]);
      }
      array.sort(function(a, b){
        var aStatus = a.status >= 0 ? a.status : -1,
            bStatus = b.status >= 0 ? b.status : -1,
          aDisplayName = a.display_name,
          bDisplayName = b.display_name;
        // TODO 正常にソートさせるならよみがなが必要
        if(scope.operatorListSortMode === 'displayName') {
          if(scope.operatorListSortOrder === "asc") {
            return aDisplayName === bDisplayName ? 0 : aDisplayName > bDisplayName ? 1 : -1;
          } else { // desc
            return aDisplayName === bDisplayName ? 0 : aDisplayName > bDisplayName ? -1 : 1;
          }
        } else { // scope.operatorListSortMode === 'status'
          if(scope.operatorListSortOrder === "asc") {
            return aStatus === bStatus ? 0 : aStatus > bStatus ? 1 : -1;
          } else { // desc
            return aStatus === bStatus ? 0 : aStatus > bStatus ? -1 : 1;
          }
        }
      });
      //console.log(JSON.stringify(array));
      return array;
    }
  });

  sincloApp.filter('customDate', function(){
    return function(input) {
      if ( angular.isUndefined(input) ) return "";
      var d = new Date(Number(input)),
          date = d.getFullYear() + '-' + _numPad(d.getMonth() + 1) + '-' + _numPad(d.getDate()),
          time = _numPad(d.getHours()) + ':' + _numPad(d.getMinutes()) + ':' + _numPad(d.getSeconds());
      return date + " " + time;
    };
  });

  sincloApp.directive('calStayTime', ['$timeout', function($timeout){
    return {
      restrict: "A",
      template: "{{stayTime}}",
      link: function(scope, attr, elem) {
        console.log("call calStayTime link function.");
        scope.stayTime = scope.monitor.term;
        var term = 0;

        function countUp(){
          console.log("call calStayTime link function => countUp()");
          // 存在しないユーザーなら、カウントを止める
          if ( !scope.$parent
            || !scope.$parent.hasOwnProperty('monitorList')
            || (scope.$parent.hasOwnProperty('monitorList') && !scope.$parent.monitorList.hasOwnProperty(scope.monitor.tabId))
          ) return false;

          scope.monitor.term = Number(scope.monitor.term) + term;
          var hour = parseInt(scope.monitor.term / 3600),
              min = parseInt((scope.monitor.term / 60) % 60),
              sec = scope.monitor.term % 60;

          if ( scope.monitor.term >= 86400 ) { // 24時間以上
            scope.stayTime = parseInt(scope.monitor.term / 86400) + "日";
            var remainHour = parseInt(hour % 24); // 一日の経過時間を算出
            term = 60 * 60 * 24 - ((remainHour * 60 + min) * 60 + sec); // 一日の残り時間を算出
          }
          else if ( scope.monitor.term < 86400 &&  scope.monitor.term >= 3600 ) { // 1時間以上、24時間未満
            scope.stayTime = parseInt(scope.monitor.term / 3600) + "時間";
            term = 60 * 60 - (min * 60 + sec);
          }
          else if ( scope.monitor.term < 3600 &&  scope.monitor.term >= 60 ) { // 1時間以上、24時間未満
            scope.stayTime = parseInt(scope.monitor.term / 60) + "分";
            term = 60 - sec;
          }
          else {
            scope.stayTime = "0分";
            term = 60;
          }

          $timeout(function(e){
            countUp();
          }, term * 1000); // 60秒ごとに実行
        }
        countUp();
      }
    }
  }]);

  /* 固定ヘッダ */
  sincloApp.directive('fixedHeader', function(){
    return {
      restrict: 'A',
      link: function(scope, elems, attrs, ctrl){
        var timer = [];
        function colResize(tblWidth, idx, width){
          clearTimeout(timer[idx]);
          var headerList = document.querySelectorAll("#list_header tr th");

          timer[idx] = setTimeout(function(){
            $("#list_header").outerWidth(tblWidth);
            $("#list_header tr th").eq(idx).outerWidth(width);
          }, 5);
        }

        var width = [];
        setInterval(function(){
          var ths = document.querySelectorAll("#list_body th");
          var tblStyle = document.querySelectorAll("#list_body thead")[0];
          angular.forEach(ths, function(elem){

            var style = window.getComputedStyle(elem, null);
            if ( (elem.cellIndex in width) ) {
              if ( width[elem.cellIndex] !== elem.offsetWidth ) {
                colResize(tblStyle.offsetWidth, elem.cellIndex, elem.offsetWidth);
                width[elem.cellIndex] = elem.offsetWidth;
              }
            }
            else {
              width[elem.cellIndex] = elem.offsetWidth;
              colResize(tblStyle.offsetWidth, elem.cellIndex, elem.offsetWidth);
            }
          });
        }, 100);
      }
    }
  });

  sincloApp.directive('ngCustomer', function($timeout){
    return {
      restrict: 'A',
      link: function(scope, elems, attrs, ctrl){
        scope.saveCusInfo = function(key, value){
          if ( !(key in scope.customData) ) return false;
          var ret = true;
          // 新規記入でない場合
          if ( scope.customPrevData && (key in scope.customPrevData) ) {
            // 変わっていない場合
            if ( scope.customData[key] === scope.customPrevData[key] ) {
              ret = false;
            }
          }
          if ( ret ) {
            var data = {
              v: scope.detail.userId,
              i: value
            };
            $.ajax({
              type: "POST",
              url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'remoteSaveCusInfo'])?>",
              data: data,
              dataType: "json",
              success: function(json) {
                if ( json ) {
                  scope.customPrevData = angular.copy(value);
                  scope.customerList[scope.detail.userId] = angular.copy(value);
                  $timeout(function () {
                    scope.$apply();
                  }, 100);
                }
              }
            });
          }
        };
        scope.showConnectionBtn = function(){
          if (scope.detail === undefined || scope.detail === {} || !scope.monitorList.hasOwnProperty(scope.detailId)) return false;
          if (!('widget' in scope.detail) || (('widget' in scope.detail) && !scope.detail.widget)) return false;
          if (!('connectToken' in scope.detail)) return true;
          if (('connectToken' in scope.detail) && scope.detail.connectToken === '') {
            return true;
          }
          return false;
        };

        // プレースホルダーの動的変更
        scope.chatPsFlg = true;
        scope.chatPs = function(){
          var sendPattarnStr = ( scope.settings.sendPattarn ) ? "Shift + Enter": "Enter";
          if ( scope.chatPsFlg ) {
            return "ここにメッセージ入力してください。\n・" + sendPattarnStr + "で改行されます\n・下矢印キー(↓)で定型文が開きます<?php if(isset($coreSettings[C_COMPANY_USE_SEND_FILE]) && $coreSettings[C_COMPANY_USE_SEND_FILE]): ?>\n・ここにファイルをドロップするとファイルを送信できます<?php endif;?>";
          }
          else {
            return "";
          }
        }

        // ボタン名ーの動的変更
        scope.chatSendBtnName = function(){
          return ( scope.settings.sendPattarn ) ? "送信（Enter）": "送信（Shift+Enter）";
        }

        scope.$watch(function(){
          if ( angular.isDefined(scope.detailId) && scope.detailId !== "" && (scope.detailId in scope.monitorList) ) {
            if ( scope.monitorList[scope.detailId].hasOwnProperty('chat') ) {
              return scope.monitorList[scope.detailId].chat;
            }
            else {
              return scope.monitorList[scope.detailId];
            }
          }
          else {
            return scope.detailId;
          }
        }, function(){
          // 変わった場合
          console.log("changed");
          if ( !(scope.detail === undefined || scope.detail === {}) && scope.detail.tabId !== scope.detailId ) {
            scope.customData = {};
            scope.customPrevData = {};
            $('option.delete-me').remove();
          }
          if ( angular.isDefined(scope.detailId) && scope.detailId !== "" && (scope.detailId in scope.monitorList) ) {
            scope.detail = angular.copy(scope.monitorList[scope.detailId]);
            scope.getCustomerInfoFromApi(scope.monitorList[scope.detailId].userId, function(ret){
              scope.customData = ret;
              scope.customPrevData = angular.copy(ret);

              if(ret) {
                Object.keys(ret).forEach(function(elm, idx, arr){
                  var targetElm = $('[data-key="' + elm + '"]');
                  if(targetElm && targetElm.is('select') && targetElm.find('option[value="'+ret[elm]+'"]').length === 0) {
                    targetElm.prepend('<option class="delete-me" value="' + ret[elm] + '" selected disabled>' + ret[elm] + '</option>');
                    scope.customData[elm] = ret[elm];
                  }
                });
              }

              if ( angular.isDefined(scope.detailId) && scope.detailId !== "" ) {
                scope.customerList[scope.monitorList[scope.detailId].userId] = angular.copy(scope.customData);
                console.log("scope apply");
                $timeout(function(){
                  scope.$apply();
                },100);
              }
              else {
                console.error('Please Tab Close.');
                return false;
              }
            });
          }
        });
      }
    }
  });
}());
</script>
