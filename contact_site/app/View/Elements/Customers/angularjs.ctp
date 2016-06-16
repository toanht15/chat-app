<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']),
    userList = <?php echo json_encode($responderList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>,
    modalFunc, myUserId = <?= h($muserId)?>, chatApi, cameraApi;

(function(){

  $.ajaxSetup({
    cache: false
  });

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
        });
      },
      connection: function(){
        if ( isset(this.tabId) && isset(this.userId) ) {
          emit("chatStart", {tabId: this.tabId, userId: myUserId});
        }
      },
      getMessage: function(obj){
        // オートメッセージの取得
        emit('getAutoChatMessages', {userId: obj.userId, tabId: obj.tabId, chatToken: chatApi.token});
      },
      addOption: function(type){
        var sendMessage = document.getElementById('sendMessage');
        switch(type){
            case 1:
            sendMessage.value += "[] 選択肢\n";
        }
      },
      createMessage: function(cs, val){
        var chatTalk = document.getElementById('chatTalk');
        var li = document.createElement('li');
        var strings = val.split('\n');
        var radioCnt = 1;
        var linkReg = RegExp(/http(s)?:\/\/[!-~.a-z]*/);
        var radioName = "sinclo-radio" + chatTalk.children.length;

        var content = "";
        for (var i = 0; strings.length > i; i++) {
            var str = strings[i];
            // ラジオボタン
            var radio = str.indexOf('[]');
            if ( radio > -1 ) {
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
            content += str + "\n";

        }
        li.className = cs;
        li.innerHTML = content;
        chatTalk.appendChild(li);
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
      notification: function(accessId, chatMessage){
        var nInstance = new Notification(
          '【' + accessId + '】新着チャットが届きました',
          {
            body: chatMessage,
            icon: "/img/sinclo_square_logo.png"
        });
        setTimeout(nInstance.close.bind(nInstance), 3000);
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

  function makeDateTime(dParse){
    var d = new Date(Number(dParse)),
        date = d.getFullYear() + '/' + ( '0' + (d.getMonth() + 1) ).slice(-2) + '/' + ('0' + d.getDate()).slice(-2),
        time = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    return date + " " + time;
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
              },
              error: function(){
              }
            });
          };
        },
        error: function(){
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
      message += "<span style='color: red'><?=Configure::read('message.const.chatStartConfirm')?></span>";
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
          socket.emit('confirmVideochatStart', {toTabId: tabId, connectToken: connectToken, receiverID: connectToken+'_vc'});
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
          },
          error: function(){
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
          var chatTalk = document.getElementById('chatTalk');
          $('#chatTalk').animate({
            scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
          }, 800);
        }, 400);
      }
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
      connect: function(obj){
        chatApi.connection(obj);
      },
      disConnect: function(tabId){
        emit("chatEnd", {tabId: tabId, userId: myUserId});
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

        if ( chatApi.token !== obj.chatToken ) return false;

       if ( ('historyId' in obj) ) {
           $.ajax({
               type: "GET",
               url: "<?=$this->Html->url('/Customers/remoteGetChatInfo')?>",
               data: {
                   historyId: obj.historyId
               },
               cache: false,
               dataType: "json",
               success: function(json){
                   if (json.length === 0) {
                       for (var u = 0; obj.messages.length > u; u++) {
                           chatApi.createMessage("sinclo_se", obj.messages[u].chatMessage);
                       }
                   }
                   else {
                       for (var i = 0; json.length > i; i++) {
                           var data= json[i]['THistoryChatLog'];
                           var cn = (Number(data['message_type']) === Number(chatApi.messageType.customer)) ? "sinclo_re" : "sinclo_se";
                           chatApi.createMessage(cn, data['message']);
                           if ( ((json.length - 1) === i && 'messages' in obj) ) {
                               for (var u = 0; obj.messages.length > u; u++) {
                                   chatApi.createMessage("sinclo_se", obj.messages[u].chatMessage);
                               }
                           }
                       }

                   }
               }
           });
       }

    });

    socket.on('resAutoChatMessage', function(d){
        var obj = JSON.parse(d);
        if (obj.tabId === chatApi.tabId ) {
            chatApi.createMessage("sinclo_auto" , obj.chatMessage);
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
    });

    // チャットメッセージ群の受信
    socket.on("chatMessageData", function(d){
      var obj = JSON.parse(d);
      if ( chatApi.token !== obj.token ) return false;
      for (var i = 0; i < obj.chat.messages.length; i++) {
        var chat = obj.chat.messages[i],
            cn = (chat.messageType === chatApi.messageType.customer) ? "sinclo_re" : "sinclo_se";
        chatApi.createMessage(cn, chat.message);
        $('#chatTalk').prop({scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight});
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
          chatApi.createMessage(cn, obj.chatMessage);
          var chatTalk = document.getElementById('chatTalk');
          $('#chatTalk').animate({
            scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
          }, 100);
        }

        // 以降、受信時のみの処理
        if (obj.messageType !== chatApi.messageType.customer) return false;

        // 未読数加算（自分が対応していないとき）
        $scope.monitorList[obj.tabId].chatUnreadCnt++;
        $scope.monitorList[obj.tabId].chatUnreadId = obj.chatId;
        chatApi.notification($scope.monitorList[obj.tabId].accessId, obj.chatMessage);

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
      url  = "<?php echo C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT ?>/webcam.html?h=true&sincloData=" + encodeURIComponent(JSON.stringify(sincloData));

      modalFunc.set.call({
        option: {
          url:  url, // FIXME
          tabId: obj.receiverID,
          width: 300,
          height: 300
        }
      });
    });

  }]);



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
