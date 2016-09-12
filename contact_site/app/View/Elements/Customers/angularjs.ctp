<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']),
    userList = <?php echo json_encode($responderList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>,
    widget = <?= $widgetSettings ?>,
    modalFunc, myUserId = <?= h($muserId)?>, chatApi, cameraApi, entryWordApi;

(function(){

  // -----------------------------------------------------------------------------
  //  関数
  // -----------------------------------------------------------------------------
  function emit(ev, d){
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
    socket.emit(ev, data);
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
      userId: null,
      token: null,
      messageType: {
        customer: 1,
        company: 2,
        auto: 3,
        start: 98,
        end: 99,
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
            chatApi.observeType.emit(chatApi.tabId, false);
        },
        send: function(status){
          chatApi.observeType.emit(chatApi.tabId, status);
          chatApi.observeType.status = status;
        },
        emit: function(tabId, status){
          if ( tabId === "" ) return false;
          emit('sendTypeCond', {
            type: chatApi.observeType.cnst.company, // company
            tabId: tabId,
            message: document.getElementById('sendMessage').value,
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
        if ( isset(this.tabId) && isset(this.userId) ) {
          emit("chatStart", {tabId: this.tabId, userId: myUserId});
        }
      },
      getMessage: function(obj){
        // オートメッセージの取得
        emit('getAutoChatMessages', {userId: obj.userId, mUserId: myUserId, tabId: obj.tabId});
      },
      addOption: function(type){
        var sendMessage = document.getElementById('sendMessage');
        switch(type){
            case 1:
            if (sendMessage.value.length > 0) {
                sendMessage.value += "\n";
            }
            sendMessage.value += "[] ";
            sendMessage.focus();
        }
      },
      pushMessage: function() {
        var elm = document.getElementById('sendMessage');
        if ( isset(elm.value) ) {
          emit('sendChat', {
            token: this.token,
            tabId: chatApi.tabId,
            userId: this.userId,
            chatMessage:elm.value,
            mUserId: myUserId,
            messageType: chatApi.messageType.company
          });
        }
      },
      isReadMessage: function(monitor){
        // フォーカスが入っているもののみ
        if (!$("#sendMessage").is(":focus")) return false;
        // メッセージを既読にする
        if ( isset(monitor.chatUnreadCnt) && monitor.chatUnreadCnt > 0 ) {
          emit('isReadChatMessage', {
            tabId: monitor.tabId,
            chatId: monitor.chatUnreadId
          });
        }
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

  // http://weathercook.hatenadiary.jp/entry/2013/12/02/062136
  sincloApp.factory('angularSocket', function ($rootScope) {
    return {
      on: function (eventName, callback) {
        socket.on(eventName, function () {
          var args = arguments;
          $rootScope.$apply(function () {
            callback.apply(socket, args);
          });
        });
      },
      emit: function (eventName, d, callback) {
        var obj = {};
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
        })
      }
    };
  });

  sincloApp.controller('MainCtrl', ['$scope', 'angularSocket', '$timeout', function($scope, socket, $timeout) {
    $scope.searchText = "";
    $scope.chatMessage = "";
    $scope.oprCnt = 0; // 待機中のオペレーター人数
    $scope.oprWaitCnt = 0; // 総オペレーター人数
    $scope.labelHideList = {
      accessId : false,
      ipAddress : false,
      ua : false,
      time : false,
      stayCount : false,
      stayTime : false,
      page : false,
      title : false,
      referrer : false
    };
    $scope.monitorList = {};
    $scope.customerList = {};
    $scope.messageList = [];
    $scope.chatList = [];
    $scope.typingMessageSe = "";
    $scope.typingMessageRe = {};

    $scope.search = function(array){
      var result = {};
      if ( $scope.searchText ) {
        angular.forEach(array, function(value, key) {
          if ( value.accessId.indexOf($scope.searchText) === 0) {
            result[key] = value;
          }
        });
      }
      else {
        result = array;
      }
      return result;
    };

    $scope.$watch('searchWord', function(n,o){
      if ( n !== o ) {
        $scope.entryWord = 0;
      }
    });

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
              dataType: 'html',
              success: function(html){
                $scope.updateList(retList);
                modalClose();
              }
            });
          };
        }
      });
    };

    $scope.updateList = function(retList){
      $scope.labelHideList = retList;
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

    $scope.ui = function(m){
      var showData = [];

      if ( (m.userId in $scope.customerList) && Object.keys($scope.customerList[m.userId]).length > 0 ) {
        var c = $scope.customerList[m.userId];
        if ( ('company' in c) && c.company.length > 0 ) {
          showData.push(c.company); // 会社名
        }
        if ( ('name' in c) && c.name.length > 0 ) {
          showData.push(c.name); // 名前
        }
      }
      // 顧客情報未登録の場合
      if ( showData.length === 0 ) {
        showData.push(m.ipAddress); // IPアドレス
      }
      return showData.join("\n");
    };

    var makeToken = function(){
      var n = 20,
          str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQESTUVWXYZ1234567890",
          strLen = str.length,
          token = "";
      for(var i=0; i<n; i++){
        token += str[Math.floor(Math.random()*strLen)];
      }
      return token;
    };

    $scope.windowOpen = function(tabId, accessId){
      var message = "アクセスID【" + accessId + "】のユーザーに接続しますか？<br><br>";
      message += "<span style='color: #FF7B7B'><?=Configure::read('message.const.chatStartConfirm')?></span>";
      modalOpen.call(window, message, 'p-confirm', 'メッセージ');
       popupEvent.closePopup = function(){
          sessionStorage.clear();
          popupEvent.close();
          connectToken = makeToken();
          socket.emit('requestWindowSync', {
            tabId: tabId,
            connectToken: connectToken
          });
          // モニター開始時にビデオ表示
          // TODO: ビデオ表示可能な条件をつける。（オプションでビデオチャット可能で、かつユーザーがカメラONにしているとき）
        <?php if (isset($coreSettings[C_COMPANY_USE_VIDEO_CHAT]) && $coreSettings[C_COMPANY_USE_VIDEO_CHAT]) : ?>
          socket.emit('confirmVideochatStart', {toTabId: tabId, connectToken: connectToken, receiverID: connectToken+'_vc'});
        <?php endif; ?>
       };
    };

    $scope.openHistory = function(monitor){
        var retList = {};
        $.ajax({
          type: 'GET',
          cache: false,
          url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteGetStayLogs')) ?>",
          data: {
            visitorsId: monitor.userId,
            tabId: monitor.tabId
          },
          dataType: 'html',
          success: function(html){
            modalOpen.call(window, html, 'p-cus-history', 'ページ移動履歴');
          }
        });
    };

    $scope.chatOptionDisabled = function(detailId){
        if (!isset(detailId)) return false;
        return ( isset($scope.monitorList[detailId].chat) && Number($scope.monitorList[detailId].chat) === Number(myUserId));
    };

    $scope.objCnt = function(list){
      if ( angular.isUndefined(list) ) return 0;
      var ret = Object.keys(list);
      return ret.length;
    };

    $scope.customerMainClass = "";

    $scope.confirmFlg = false;
    $scope.sendMessageConnectConfirm = function(detailId){
        var monitor = $scope.monitorList[detailId];
        if ( !(monitor.chat in userList) ) {
            $scope.ngChatApi.connect();
            return true;
        }
        else if ( (monitor.chat in userList) && monitor.chat === myUserId ) {
            // 既読にする
            chatApi.isReadMessage($scope.monitorList[detailId]);
        }
        else {
            if ( $scope.confirmFlg ) return false;
            $("#sendMessage").blur();
            var message = "現在 " + userList[monitor.chat] + "さん が対応中です。<br><br>";
            message += "対応者を切り替えますか？";
            modalOpen.call(window, message, 'p-confirm', 'メッセージ');
            popupEvent.closePopup = function(){
                $scope.confirmFlg = true;
                $scope.ngChatApi.connect();
                $("#sendMessage").val("").focus();
                popupEvent.close();
                $scope.confirmFlg = false;
                return true;
            };

        }


    };

    $scope.confirmDisConnect = function(tabId){
      modalOpen.call(window, 'チャットを終了してもよろしいでしょうか？', 'p-cus-detail', '操作確認');
      // チャットを終了する
      popupEvent.closePopup = function(){
        $scope.ngChatApi.disConnect(tabId); // チャットを終了する
        $scope.showDetail(tabId);
        popupEvent.close(); // モーダルを閉じる
      };
      // 最小化する
      popupEvent.customizeBtn = function(){
        $scope.showDetail(tabId); // 詳細を閉じる
        popupEvent.close(); // モーダルを閉じる
      };

    };

    $scope.showDetail = function(tabId){
      $("#sendMessage").attr('value', '');
      if ( $scope.customerMainClass !== "" ) {
        $("#customer_sub_pop").css("display", "");
        $scope.customerMainClass = "";
        $scope.detailId = "";
        $scope.typingMessageSe = "";
        $scope.messageList = [];
        chatApi.userId = "";
        chatApi.observeType.emit(chatApi.tabId, false);
        $("#chatTalk message-list").children().remove();
        $("#customer_list tr.on").removeClass('on');

        if ( chatApi.tabId !== tabId ) {
          window.setTimeout(function(){
            $scope.showDetail(tabId);
          }, 300);
        }
        chatApi.tabId = "";
      }
      else {
        setPositionOfPopup();
        $scope.customerMainClass = "showDetail";
        chatApi.token = makeToken();
        $scope.detailId = tabId;
        chatApi.getMessage($scope.monitorList[tabId]);
        chatApi.userId = $scope.monitorList[tabId].userId;
        chatApi.tabId = tabId;
        $("#monitor_" + tabId).addClass('on');
        if ( isset($scope.monitorList[tabId].chat) && $scope.monitorList[tabId].chat === myUserId ) {
          $("#chatContent").addClass("connectChat");
        }
        else {
          $("#chatContent").removeClass("connectChat");
        }
        setTimeout(function(){
          $("#customer_sub_pop").css("display", "block");
        }, 400);
      }
    };

    // プレースホルダーの動的変更
    $scope.chatPsFlg = true;
    $scope.chatPs = function(){
      var sendPattarnStr = ( $scope.settings.sendPattarn ) ? "Shift + Enter": "Enter";
      if ( $scope.chatPsFlg ) {
        return "ここにメッセージ入力してください。\n・" + sendPattarnStr + "で改行されます\n・下矢印キー(↓)で簡易入力が開きます";
      }
      else {
        return "";
      }
    }

    // ボタン名ーの動的変更
    $scope.chatSendBtnName = function(){
      return ( $scope.settings.sendPattarn ) ? "送信（Enter）": "送信（Shift+Enter）";
    }

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
      $scope.getCustomerInfo(m.userId, function(ret){
        $scope.customerList[m.userId] = ret;
      });
    };

    // 顧客の詳細情報を取得する
    $scope.getCustomerInfo = function(userId, callback){
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
          $scope.chatPsFlg = false;
        })
        .blur(function(e){
          // フォーカスが当たった時にPlaceholderを消す（Edge対応）
          $scope.chatPsFlg = true;
        });
        chatApi.init();
      },
      connect: function(obj){
        chatApi.connection(obj);
      },
      disConnect: function(tabId){
        emit("chatEnd", {tabId: tabId, userId: myUserId});
      },
      notification: function(monitor){
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
            if (Number(opt.type) === Number(<?=C_NOTIFICATION_TYPE_TITLE?>)) {
              target = m.title;
            }
            // URL
            else if (Number(opt.type) === Number(<?=C_NOTIFICATION_TYPE_URL?>)) {
              target = m.url;
            }
            if (target.indexOf(opt.keyword) > 0) {
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
            $scope.showDetail(monitor.tabId); // 詳細を開く
          }
          nInstance.close();
        };
      }
    };

    $scope.ngCameraApi = {
      connect: function(obj){
        cameraApi.connect(obj);
      },
      disConnect: function(obj){
        cameraApi.disConnect(obj);
      }
    };

    $scope.$watch('monitorList', function(){
      if ( angular.isDefined($scope.detailId) && !($scope.detailId in $scope.monitorList) ) {
        $scope.showDetail($scope.detailId);
      }
    });

    function pushToList(obj){
      $scope.monitorList[obj.tabId] = obj;

      $scope.getCustomerInfoFromMonitor(obj);

      if ( 'connectToken' in obj && 'responderId' in obj) {
        $scope.monitorList[obj.tabId].connectToken = obj.connectToken;
      }
    }

    function pushToChatList(tabId){
      if ( $scope.chatList.length === 0 ) {
          $scope.chatList.push(tabId);
      }
      else {
        if ( $scope.chatList.indexOf(tabId) < 0 ) {
            $scope.chatList.push(tabId);
        }
      }
    }

    socket.on('getAccessInfo', function (data) {
      var obj = JSON.parse(data);
<?php if($widgetCheck): ?>
      if ( Number(obj.userId) === Number(myUserId) ) {
        if ( String(obj.status) == "<?=C_OPERATOR_ACTIVE?>") {
          chgOpStatusView("<?=C_OPERATOR_ACTIVE?>");
        }
        else {
          chgOpStatusView("<?=C_OPERATOR_PASSIVE?>");
        }
      }
      $scope.oprCnt = obj.onlineUserCnt;
<?php endif; ?>
      $scope.oprWaitCnt = obj.userCnt;
    });

    socket.on('outCompanyUser', function (data) {
      var obj = JSON.parse(data);
      $scope.oprWaitCnt = obj.userCnt;
    });

    socket.on('receiveAccessInfo', function (data) {
      var obj = JSON.parse(data);
      if ( receiveAccessInfoToken !== obj.receiveAccessInfoToken ) return false;
      pushToList(obj);
      if ( 'chat' in obj && String(obj.chat) === "<?=$muserId?>" ) {
        pushToChatList(obj.tabId);
      }
    });

    socket.on('resAutoChatMessages', function(d){
        var obj = JSON.parse(d);

        if ( ('historyId' in obj) ) {
          socket.emit('getChatMessage', {
            siteKey: obj.siteKey,
            tabId: obj.tabId
          });
          for (var key in obj.messages) {
            var chat = obj.messages[key];
            chat.sort = Number(key);
            $scope.messageList.push(chat);
          }
        }

    });

    socket.on('resAutoChatMessage', function(d){
        var obj = JSON.parse(d);
        if (obj.tabId === chatApi.tabId ) {
            var chat = obj;
            chat.sort = Number(chat.sort);
            $scope.messageList.push(obj);
        }
    });

    socket.on('sendCustomerInfo', function (data) {
      var obj = JSON.parse(data);
      pushToList(obj);
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

      // 消費者
      if ( angular.isDefined($scope.monitorList[obj.tabId]) ) {
        if ( 'widget' in obj ) {
          $scope.monitorList[obj.tabId].widget = obj.widget;
          if ( chatApi.tabId === obj.tabId ) {
            chatApi.observeType.emit(chatApi.tabId, chatApi.observeType.status);

          }
        }
        if ( 'connectToken' in obj ) { $scope.monitorList[obj.tabId].connectToken = obj.connectToken; }
        if ( 'prev' in obj ) { $scope.monitorList[obj.tabId].prev = obj.prev; }
        if ( 'title' in obj ) { $scope.monitorList[obj.tabId].title = obj.title; }
        if ( 'url' in obj ) { $scope.monitorList[obj.tabId].url = obj.url; }
        if ( 'responderId' in obj ) { $scope.monitorList[obj.tabId].responderId = obj.responderId; }
      }

      var tabId = ( obj.subWindow ) ? obj.to : obj.tabId;
      if ( 'connectToken' in obj && 'responderId' in obj) {
        $scope.monitorList[tabId].connectToken = obj.connectToken;
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
    });

    socket.on('unsetUser', function(data){
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined ) {
        if ( obj.accessType !== _access_type_host ) {
          delete $scope.monitorList[obj.tabId];
          $scope.chatList = $scope.chatList.filter(function(v){
            return (v !== this.t)
          }, {t: obj.tabId});
          if ( obj.tabId === chatApi.tabId ){
            $scope.showDetail(obj.tabId);
            chatApi.tabId = null;
          }
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

      if ( obj.userId === myUserId && obj.ret ) {
        pushToChatList(obj.tabId);
        $("#sendMessage").focus();
        // 既読にする
        chatApi.isReadMessage($scope.monitorList[obj.tabId]);
      }
      else {
        $scope.chatList = $scope.chatList.filter(function(v){
          return (v !== this.t)
        }, {t: obj.tabId});

        // 前回の担当が自分だった場合
        if ( prev === myUserId && obj.ret ) {
          $("#sendMessage").val("").blur();
        }
      }
      if ( obj.tabId === chatApi.tabId ) {
        var chat = {
          sort: Number(obj.created),
          messageType: Number(obj.messageType),
          userId: obj.userId
        };
        $scope.messageList.push(chat);
      }
    });

    // チャット接続終了
    socket.on("chatEndResult", function(d){
      var obj = JSON.parse(d);

      if ( 'tabId' in obj && obj.tabId in $scope.monitorList && 'chat' in $scope.monitorList[obj.tabId] ) {
        $scope.chatList = $scope.chatList.filter(function(v){
          return (v !== this.t)
        }, {t: obj.tabId});
        $scope.monitorList[obj.tabId].chat = null;
      }
      if ( obj.tabId === chatApi.tabId ) {
        var chat = {
          sort: Number(obj.created),
          userId: obj.userId
        };
        $scope.messageList.push(chat);
      }

    });

    // チャットメッセージ群の受信
    socket.on("chatMessageData", function(d){
      var obj = JSON.parse(d);
      for (var key in obj.chat.messages) {
        var chat = {};
        if ( typeof(obj.chat.messages[key]) === "object" ) {
          chat = obj.chat.messages[key];
        }
        else {
          chat.text = obj.chat.messages[key];
        }
        chat.sort = Number(key);
        $scope.messageList.push(chat);
      }
      if ( $scope.monitorList[obj.tabId].chat === myUserId ) {
        // 既読にする(ok)
        chatApi.isReadMessage($scope.monitorList[obj.tabId]);
      }
    });
    // チャットメッセージ送信結果
    socket.on("sendChatResult", function(d){
      var obj = JSON.parse(d),
          elm = document.getElementById('sendMessage');

      if ( !(obj.tabId in $scope.monitorList) ) return false;
      if ( obj.ret ) {

        // 対象のタブを開いている場合
        if ( obj.tabId === chatApi.tabId ){
          var chat = JSON.parse(JSON.stringify(obj));
          chat.sort = Number(obj.sort);
          $scope.messageList.push(chat);
          scDown(); // チャットのスクロール
        }

        if (Number(obj.messageType) === chatApi.messageType.company) {
          // 入力したメッセージを削除
          if ( obj.tabId === chatApi.tabId && obj.userId === myUserId ) {
            elm.value = "";
          }
          // 以降、受信時のみの処理
          return false;
        }

        // 着信音を鳴らす
        chatApi.call();

        // 未読数加算（自分が対応していないとき）
        $scope.monitorList[obj.tabId].chatUnreadCnt++;
        $scope.monitorList[obj.tabId].chatUnreadId = obj.chatId;
        $scope.ngChatApi.notification($scope.monitorList[obj.tabId]);

        // 既読にする(対象のタブを開いている、且つ自分が対応しており、フォーカスが当たっているとき)
        if (  obj.tabId === chatApi.tabId && $scope.monitorList[obj.tabId].chat === myUserId && $("#sendMessage").is(":focus") ) {
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
      $scope.monitorList[obj.tabId].chatUnreadId  = obj.chatUnreadId;
      $scope.monitorList[obj.tabId].chatUnreadCnt = obj.chatUnreadCnt;
    });

    // チャットメッセージ既読処理結果関数
    socket.on('retReadChatMessage', function(d){
      var obj = JSON.parse(d);
      $scope.monitorList[obj.tabId].chatUnreadId = null;
      $scope.monitorList[obj.tabId].chatUnreadCnt = 0;
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
          to: obj.tabId,
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
      if ( obj.tabId !== chatApi.tabId ) return false;

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
          chatApi.observeType.emit(chatApi.tabId, status);
        }
        else {
          $scope.typingMessageSe = obj.message;
        }

      }
      // 消費者側がメッセージ入力中
      else {
        $scope.typingMessageRe[obj.tabId] = obj.message;
      }
      var chatTalk = document.getElementById('chatTalk');
      $('#chatTalk').animate({
        scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
      }, 100);
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
        if (item.label.indexOf($scope.searchWord) === 0 || angular.isUndefined($scope.searchWord) ) {
          array.push(item);
        }
      });
      return array;
    };

    // テキストエリア
    $("#sendMessage").on("keydown", function(e) {
      if ( e.keyCode === 40 ) {
        var target = e.target;
        if ( target.selectionStart === target.selectionEnd
          && target.selectionStart === target.value.length ) {
          var testarea_offset = $(this).offset();
          var testarea_size = {
            height: this.offsetHeight,
            width: this.offsetWidth
          };
          var area = document.getElementById('wordListArea');
          area.style.display = "block";
          $("#wordSearchCond").focus();
          $scope.entryWord = 0;
          $scope.searchWord = "";
          $scope.$apply();
          entryWordApi.init();

          return false;
        }
      }
    });

    /* 【ここから】ワードリスト内 */

    // ワードリストエリア
    $("#wordListArea").on('click', function(e){
      e.stopPropagation();
    });

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

    $(document).ready(function(){
      $scope.ngChatApi.init();
    });

    // ポップアップをセンターに表示
    function setPositionOfPopup(){
      var subContent = document.getElementById("sub_contents");
      subContent.style.left = ((window.innerWidth-$("#sidebar-main").outerWidth()) - $("#sub_contents").outerWidth())/2 + "px";
      subContent.style.top = "100px";
    }

  }]);

  sincloApp.directive('ngCreateMessage', function(){
    return {
      restrict: 'E',
      link: function(scope, elem, attr) {
        var cn = "";
        var li = document.createElement('li');
        var content = "";

        // 消費者からのメッセージの場合
        if (scope.chat.messageType === chatApi.messageType.customer) {
          cn = "sinclo_re";
          li.className = cn;
          content = createMessage(scope.chat.message, {radio: false});
        }
        // オートメッセージの場合
        else if (scope.chat.messageType === chatApi.messageType.company) {
          cn = "sinclo_se";
          var chatName = widget.subTitle;
          if ( Number(widget.showName) === <?=C_WIDGET_SHOW_NAME?> ) {
            chatName = userList[Number(scope.chat.userId)];
          }
          content = "<span class='cName'>" + chatName + "</span>";
          content += createMessage(scope.chat.message);
        }
        else if (scope.chat.messageType === chatApi.messageType.auto) {
          cn = "sinclo_auto";
          content = "<span class='cName'>自動応答</span>";
          content += createMessage(scope.chat.message);
        }
        else  {
          cn = "sinclo_etc";
          var userName = "オペレーター";
          if ( Number(widget.showName) === <?=C_WIDGET_SHOW_NAME?> && "userId" in scope.chat ) {
            userName = userList[Number(scope.chat.userId)];
          }
          if ( scope.chat.messageType === chatApi.messageType.start ) {
            content = "－　" + userName + "が入室しました　－";
          }
          if ( scope.chat.messageType === chatApi.messageType.end ) {
            content = "－　" + userName + "が退室しました　－";
          }
        }
        li.className = cn;
        li.innerHTML = content;
        $(elem).append(li);

        scDown();

        function createMessage(message, opt){
          var strings = message.split('\n');
          var custom = "";
          var linkReg = RegExp(/http(s)?:\/\/[!-~.a-z]*/);
          var radioName = "sinclo-radio" + Object.keys(scope.chat).length;
          var option = ( typeof(opt) !== 'object' ) ? { radio: true } : opt;
          for (var i = 0; strings.length > i; i++) {
              var str = strings[i];
              // ラジオボタン
              var radio = str.indexOf('[]');
              if ( option.radio && radio > -1 ) {
                  var val = str.slice(radio+2);
                  str = "<input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + val + "' disabled=''>";
                  str += "<label for='" + radioName + "-" + i + "'>" + val + "</label>";
              }
              // リンク
              var link = str.match(linkReg);
              if ( link !== null ) {
                  var url = link[0];
                  var a = "<a href='" + url + "' target='_blank'>"  + url + "</a>";
                  str = str.replace(url, a);
              }
              custom += str + "\n";

          }
          return custom;
        }
      }
    };
  });

  // 参考 http://stackoverflow.com/questions/14478106/angularjs-sorting-by-property
  sincloApp.filter('orderObjectBy', function(){
   return function(input, atr) {
      if (!angular.isObject(input)) return input;
      var array = [];
      for(var objectKey in input) {
          array.push(input[objectKey]);
      }
      var sortAsc = (atr.match(/^-{1}/) === null);
      var attribute = (sortAsc) ? atr : atr.substr(1);
      array.sort(function(a, b){
          a = (isNaN(parseInt(a[attribute]))) ? 0 : parseInt(a[attribute]);
          b = (isNaN(parseInt(b[attribute]))) ? 0 : parseInt(b[attribute]);
          if (sortAsc) {
            return a - b;
          }
          else {
            return b - a;
          }
      });
      return array;
   }
  });

  function _numPad(str){
    return ("0" + str).slice(-2);
  }

  sincloApp.filter('customDate', function(){
    return function(input) {
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
        scope.stayTime = scope.monitor.term;

        function countUp(){
          scope.monitor.term = Number(scope.monitor.term) + 1;
          var hour = parseInt(scope.monitor.term / 3600),
              min = parseInt((scope.monitor.term / 60) % 60),
              sec = scope.monitor.term % 60;
          scope.stayTime = _numPad(hour) + ":" + _numPad(min) + ":" + _numPad(sec); // 表示を更新
          $timeout(function(e){
            countUp();
          }, 1000); // 1秒ごとに実行
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

          timer[idx] = setTimeout(function(){
            var headerElem = document.getElementById("list_header");
            var col = document.querySelectorAll("#list_header tr th")[idx];
            headerElem.style.width = tblWidth;
            col.style.width = width;
          }, 5);
        }

        var width = [];
        setInterval(function(){
          var ths = document.querySelectorAll("#list_body th");
          var tblStyle = window.getComputedStyle(elems[0], null);
          angular.forEach(ths, function(elem){
            var style = window.getComputedStyle(elem, null);
            if ( (elem.cellIndex in width) ) {
              if ( width[elem.cellIndex] !== style.width ) {
                colResize(tblStyle.width, elem.cellIndex, style.width);
                width[elem.cellIndex] = style.width;
              }
            }
            else {
              width[elem.cellIndex] = style.width;
              colResize(tblStyle.width, elem.cellIndex, style.width);
            }
          });
        }, 5);
      }
    }
  });

  sincloApp.directive('ngCustomer', function(){
    return {
      restrict: 'A',
      link: function(scope, elems, attrs, ctrl){
        scope.saveCusInfo = function(key, value){
          if ( !(key in scope.customData) ) return false;
          var ret = true;
          // 新規記入でない場合
          if ( (key in scope.customPrevData) ) {
            // 変わっていない場合
            if ( scope.customData[key] === scope.customPrevData[key] ) {
              ret = false;
            }
          }
          if ( ret ) {
            var data = {
                v: scope.monitorList[scope.detailId].userId,
                i: value
              };
            $.ajax({
              type: "POST",
              url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'remoteSaveCusInfo'])?>",
              data: data,
              dataType: "json",
              success: function(json){
                if ( json ) {
                  scope.customPrevData = angular.copy(value);
                  scope.customerList[scope.monitorList[scope.detailId].userId] = angular.copy(value);
                }
              }
            });
          }
        }
        scope.$watch('detailId', function(){
          scope.detail = {};
          scope.customData = {};
          scope.customPrevData = {};
          if ( scope.detailId !== "" && (scope.detailId in scope.monitorList) ) {
            scope.detail = scope.monitorList[scope.detailId];
            scope.getCustomerInfo(scope.monitorList[scope.detailId].userId, function(ret){
              scope.customData = ret;
              scope.customPrevData = angular.copy(ret);
              scope.customerList[scope.monitorList[scope.detailId].userId] = angular.copy(scope.customData);
            });
          }
        });
      }
    }
  });

}());
</script>
