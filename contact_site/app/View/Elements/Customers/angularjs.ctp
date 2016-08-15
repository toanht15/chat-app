<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']),
    userList = <?php echo json_encode($responderList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>,
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
        company: 2
      },
      init: function(){
        $("#sendMessage").keydown(function(e){
          if ( e.keyCode === 13 ) {
            if ( !(e.shiftKey || e.ctrlKey) ) {
              chatApi.pushMessage();
            }
          }
        })
        .blur(function(e){
          chatApi.observeType.end();
        })
        .focus(function(e){
          chatApi.observeType.start();
        });

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
            if ( sendMessage.value === "" ) {
              chatApi.observeType.prevMessage = "";
              chatApi.observeType.send(false);
            }
            else if ( sendMessage.value !== chatApi.observeType.prevMessage ) {
              chatApi.observeType.prevMessage = sendMessage.value;
              chatApi.observeType.send(true);

              if ( chatApi.observeType.timeoutTimer ) {
                clearTimeout(chatApi.observeType.timeoutTimer);
              }
              chatApi.observeType.timeoutTimer = setTimeout(function(){
                chatApi.observeType.prevMessage = sendMessage.value;
                chatApi.observeType.send(false);
              }, 5000);
            }
          }, 300);
        },
        end: function(){ // フォーカスを外した時に即時入力中をやめる場合は有効にする
          // clearTimeout(chatApi.observeType.timer);
          // chatApi.observeType.send(false);
        },
        send: function(status){
          if ( chatApi.observeType.status !== status ) {
            emit('sendTypeCond', {
              type: chatApi.observeType.cnst.company, // company
              tabId: chatApi.tabId,
              status: status
            });
            chatApi.observeType.status = status;
          }
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
        emit('getAutoChatMessages', {userId: obj.userId, tabId: obj.tabId});
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

  sincloApp.controller('MainCtrl', ['$scope', 'angularSocket', function($scope, socket) {
    $scope.searchText = "";
    $scope.chatMessage = "";
    $scope.oprCnt = 0; // 待機中のオペレーター人数
    $scope.oprWaitCnt = 0; // 総オペレーター人数
    $scope.labelHideList = {
      accessId : false,
      ipAddress : false,
      ua : false,
      time : false,
      stayTime : false,
      page : false,
      title : false,
      referrer : false
    };
    $scope.monitorList = {};
    $scope.messageList = [];
    $scope.chatList = [];
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

    $scope.ua = function(str){
      return userAgentChk.init(str);
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
            var message = "現在 " + userList[monitor.chat] + "さん が対応中です。<br><br>";
            message += "対応者を切り替えますか？";
            modalOpen.call(window, message, 'p-confirm', 'メッセージ');
            popupEvent.closePopup = function(){
                $scope.ngChatApi.connect();
                popupEvent.close();
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
        $scope.customerMainClass = "";
        $scope.detailId = "";
        $scope.messageList = [];
        chatApi.userId = "";
        $("#chatTalk").children().remove();
        $("#customer_list tr.on").removeClass('on');
        setTimeout(function(){
          $("#customer_sub").css("display", "");
        }, 200);

        if ( chatApi.tabId !== tabId ) {
          window.setTimeout(function(){
            $scope.showDetail(tabId);
          }, 300);
        }
        chatApi.tabId = "";
      }
      else {
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
          $("#customer_sub").css("display", "block");
        }, 400);
      }
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
      connect: function(obj){
        chatApi.connection(obj);
      },
      disConnect: function(tabId){
        emit("chatEnd", {tabId: tabId, userId: myUserId});
      },
      notification: function(tabId, accessId, chatMessage){
        var nInstance = new Notification(
          '【' + accessId + '】新着チャットが届きました',
          {
            body: chatMessage,
            icon: "<?=C_PATH_NODE_FILE_SERVER?>/img/mark.png"
        });
        nInstance.onclick = function(){
          window.focus(); // 現在のタブにフォーカスを当てる
          if ( chatApi.tabId !== tabId ) {
            $scope.showDetail(tabId); // 詳細を開く
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
            $scope.messageList.push(obj.messages[key]);
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
        if ( 'widget' in obj ) { $scope.monitorList[obj.tabId].widget = obj.widget; }
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
      }
      if ( obj.tabId === chatApi.tabId ) {
        var chat = {
          sort: Number(obj.created),
          userId: obj.userId,
          type: "start"
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
          userId: obj.userId,
          type: "end"
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
          elm = document.getElementById('sendMessage'), cn;

      if ( !(obj.tabId in $scope.monitorList) ) return false;
      if ( obj.ret ) {
        if (obj.messageType === chatApi.messageType.customer) {
          cn = "sinclo_re";
        }
        else if (obj.messageType === chatApi.messageType.company) {
          cn = "sinclo_se";
          elm.value = "";
        }
        // 対象のタブを開いている場合
        if ( obj.tabId === chatApi.tabId ){
          var chat = JSON.parse(JSON.stringify(obj));
          chat.sort = Number(obj.sort);
          $scope.messageList.push(chat);
          scDown(); // チャットのスクロール
        }

        // 以降、受信時のみの処理
        if (obj.messageType !== chatApi.messageType.customer) return false;

        // 着信音を鳴らす
        chatApi.call();

        // 未読数加算（自分が対応していないとき）
        $scope.monitorList[obj.tabId].chatUnreadCnt++;
        $scope.monitorList[obj.tabId].chatUnreadId = obj.chatId;
        $scope.ngChatApi.notification(obj.tabId, $scope.monitorList[obj.tabId].accessId, obj.chatMessage);

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

    // チャット入力中ステータスの受信
    socket.on('receiveTypeCond', function(d){
      var obj = JSON.parse(d),
          typeMessage = document.getElementById('typeing_message'),
          li = document.createElement('li');

      if ( typeMessage && obj.status === false ) {
        typeMessage.parentNode.removeChild(typeMessage);
      }
      else if (obj.status !== false) {
        if ( typeMessage ) {
          typeMessage.textContent = obj.message;
        }
        else {
          li.innerHTML = obj.message;
          li.className = "sinclo_re";
          li.id = "typeing_message";
          $('#chatTalk').append(li);

        }
      }
      scDown();
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


  }]);

  sincloApp.directive('ngCreateMessage', function(){
    return {
      restrict: 'E',
      link: function(scope, elem, attr) {
        var cn = "";
        var li = document.createElement('li');
        var content = "";

        if ( !("type" in scope.chat) ) {
          // 消費者からのメッセージの場合
          if (scope.chat.messageType === chatApi.messageType.customer) {
            cn = "sinclo_re";
            li.className = cn;
            content = createMessage(scope.chat.message, {radio: false});
          }
          // 企業側か、オートメッセージの場合
          else if (scope.chat.messageType === chatApi.messageType.company && Number(scope.chat.userId) in userList) {
            cn = "sinclo_se";
            content = "<span class='cName'>" + userList[Number(scope.chat.userId)] + "さん</span>";
            content += createMessage(scope.chat.message);
          }
          else  {
            cn = "sinclo_auto";
            content = "<span class='cName'>自動応答</span>";
            content += createMessage(scope.chat.message);
          }

        }
        else {
          cn = "sinclo_etc";
          var userName = "オペレーター";
          if ( "userId" in scope.chat ) {
            userName = userList[Number(scope.chat.userId)];
          }
          if ( scope.chat.type === "start" ) {
            content = "－　" + userName + "が入室しました　－";
          }
          if ( scope.chat.type === "end" ) {
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

}());
</script>
