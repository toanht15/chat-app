<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']),
    modalFunc, myUserId = <?= h($muserId)?>, chatApi;

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
                  ",dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
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
      historyId: null,
      messageType: {
        customer: 1,
        company: 2
      },
      connection: function(monitor){
        if ( isset(monitor.tabId) && isset(monitor.userId) && !isset(monitor.chat) ) {
          emit("chatStart", {tabId: monitor.tabId, userId: myUserId});
        }
      },
      disConnect: function(monitor){
        if ( isset(monitor.tabId) && (isset(monitor.userId) && isset(monitor.chat) && myUserId === monitor.chat ) ) {
          emit("chatEnd", {tabId: monitor.tabId});
        }
      },
      getMessage: function(obj){
        $("#sendMessage").attr('disabled', true);
        // チャットの取得
        emit('getChatMessage', {userId: obj.userId, tabId: obj.tabId, token: chatApi.token});
      },
      createMessage: function(cs, val){
        var chatTalk = document.getElementById('chatTalk');
        var li = document.createElement('li');
        li.className = cs;
        li.textContent = val;
        chatTalk.appendChild(li);
        $('#chatTalk').animate({
          scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
        }, 100);
      },
      pushMessage: function() {
        var elm = document.getElementById('sendMessage');
        if ( isset(elm.value) ) {
          emit('sendChat', {
            token: this.token,
            historyId: this.historyId,
            tabId: chatApi.tabId,
            userId: this.userId,
            chatMessage:elm.value,
            mUserId: myUserId,
            messageType: chatApi.messageType.company
          });
        }
      },
      isReadMessage: function(monitor){
        // メッセージを既読にする
        if ( isset(monitor.chatUnread) && isset(monitor.chatUnread['cnt']) && monitor.chatUnread.cnt > 0 ) {
          emit('isReadChatMessage', {
            tabId: this.tabId,
            historyId: this.historyId,
            chatId: monitor.chatUnread.id
          });
        }
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
    $scope.userList = <?=json_encode($muserList)?>;
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
      // $scope.$parent.labelHideList = retList;
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

    $scope.windowOpen = function(tabId){
      connectToken = makeToken();
      socket.emit('requestWindowSync', {tabId: tabId, connectToken: connectToken});
    };

    $scope.openHistory = function(monitor){
        var retList = {};
        $.ajax({
          type: 'GET',
          url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteGetStayLogs')) ?>",
          data: {
            tmpCustomersId: monitor.userId,
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

    $scope.customerMainClass = "";

    $scope.showDetail = function(tabId){
      $("#sendMessage").attr('value', '');
      if ( $scope.customerMainClass !== "" ) {
        $scope.customerMainClass = "";
        $scope.detailData = "";
        chatApi.historyId = "";
        chatApi.userId = "";
        $("#chatTalk").children().remove();
        $("#customer_list tr.on").removeClass('on');
        if ( chatApi.connect ) {
         socket.emit("chatStart", {tabId: chatApi.tabId, userId: myUserId});
        }

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
        $scope.detailData = $scope.monitorList[tabId];
        chatApi.getMessage($scope.detailData);
        chatApi.userId = $scope.detailData.userId;
        chatApi.tabId = tabId;
        $("#monitor_" + tabId).addClass('on');
        chatApi.connect = false;
        if ( isset($scope.detailData.chat) && $scope.detailData.chat === myUserId ) {
          $("#chatContent").addClass("connectChat");
        }
        else {
          $("#chatContent").removeClass("connectChat");
        }
      }
    };

    $scope.ngChatApi = {
      connect: function(obj){
        chatApi.connection(obj);
      },
      disConnect: function(obj){
        chatApi.disConnect(obj);
      }
    };

    function pushToList(obj){
      $scope.monitorList[obj.tabId] = obj;
    }

    socket.on('receiveAccessInfo', function (data) {
      var obj = JSON.parse(data);
      if ( receiveAccessInfoToken !== obj.receiveAccessInfoToken ) return false;
      pushToList(obj);
    });

    socket.on('sendCustomerInfo', function (data) {
      var obj = JSON.parse(data);
      pushToList(obj);
    });

    socket.on('connectInfo', function (data) {
      var obj = JSON.parse(data);
      // 消費者
      if ( obj.subWindow === false ) {
        if ( angular.isDefined($scope.monitorList[obj.tabId]) ) {
          $scope.monitorList[obj.tabId].connectToken = obj.connectToken;
          $scope.monitorList[obj.tabId].title = obj.title;
          $scope.monitorList[obj.tabId].url = obj.url;
          $scope.monitorList[obj.tabId].prev = obj.prev;
          $scope.monitorList[obj.tabId].widget = obj.widget;
          $scope.monitorList[obj.tabId].chat = obj.chat;
        }
        else {
          socket.emit('getCustomerInfo', JSON.stringify({tabId: obj.tabId}));
        }
      }
      else {
        // 接続中
        if ( angular.isDefined($scope.monitorList[obj.to]) ) {
          $scope.monitorList[obj.to].connectToken = obj.connectToken;
        }
      }
    });

    socket.on('windowSyncInfo', function (data) {
      // 担当しているユーザーかチェック
      var obj = JSON.parse(data), url, scscale, scwidth, scheight;
      if (connectToken !== obj.connectToken) return false;
      scscale = screen.availWidth / obj.screen.width;
      var wSpan = window.parent.screen.width - obj.windowSize.width;
      var hSpan = window.parent.screen.height - obj.windowSize.height;
      // サイズが超えてしまう場合
      if ( wSpan < 0 || hSpan < 0 ) {
        // 縮小する
        if ( hSpan > wSpan ) {
          scscale = screen.availHeight / obj.screen.height;
        }
      }
      scwidth = obj.windowSize.width * scscale;
      scheight = obj.windowSize.height * scscale;
      connectToken = null; // リセット
      url  = "<?= $this->Html->url(array('controller'=>'Customers', 'action'=>'frame')) ?>/?userId=" + obj.userId + "&type=" + _access_type_host;
      url += "&url=" + encodeURIComponent(obj.url) + "&userId=" + obj.userId;
      url += "&connectToken=" + obj.connectToken + "&id=" + obj.tabId;
      url += "&width=" + obj.windowSize.width + "&height=" + obj.windowSize.height + "&scale=" + scscale;
      modalFunc.set.call({
        option: {
          url: url,
          tabId: obj.tabId,
          width: scwidth,
          height: scheight
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

    // =======================================
    //   チャット関連受信ここから
    // =======================================

    // チャット接続結果
    socket.on("chatStartResult", function(d){
      var obj = JSON.parse(d);
      if ( obj.userId === myUserId && obj.ret ) {
        chatApi.connect = true;
        // 詳細表示
        if ( obj.tabId !== chatApi.tabId ) {
          $scope.showDetail(obj.tabId);
        }
        else {
          $scope.monitorList[obj.tabId].chat = myUserId;
        }
        // 既読にする
        chatApi.isReadMessage($scope.monitorList[obj.tabId]);
      }
    });

    // チャットメッセージ群の受信
    socket.on("chatMessageData", function(d){
      var obj = JSON.parse(d);
      if ( chatApi.token !== obj.token ) return false;
      if ( isset(obj.chat.historyId) ) {
        chatApi.historyId = obj.chat.historyId;
      }
      for (var i = 0; i < obj.chat.messages.length; i++) {
        var chat = obj.chat.messages[i],
            cn = (chat.messageType === chatApi.messageType.customer) ? "sinclo_re" : "sinclo_se";
        chatApi.createMessage(cn, chat.message);
      }
      if ( $scope.monitorList[obj.tabId].chat === myUserId  ) {
        // 既読にする(ok)
        chatApi.isReadMessage($scope.monitorList[obj.tabId]);
      }
    });
    // チャットメッセージ送信結果
    socket.on("sendChatResult", function(d){
      var obj = JSON.parse(d);
      // 未読数加算（自分以外の誰も対応していないとき）
      if ( isset($scope.monitorList[obj.tabId]) && ($scope.monitorList[obj.tabId].chat === null || $scope.monitorList[obj.tabId].chat === myUserId) ) {
          $scope.monitorList[obj.tabId].chatUnread.cnt++;
          $scope.monitorList[obj.tabId].chatUnread.id = obj.chatId;
          var n = new Notification(obj.chatMessage);
      }
      // 既読にする（自分が対応しているとき）
      if ( isset($scope.monitorList[obj.tabId]) && $scope.monitorList[obj.tabId].chat === myUserId ) {
          // 既読にする
          chatApi.isReadMessage($scope.monitorList[obj.tabId]);
      }

      // 対象のタブを開いていない場合
      if ( obj.tabId !== chatApi.tabId ) return false;

      var elm = document.getElementById('sendMessage'), cn;
      if ( obj.ret ) {
        if (obj.messageType === chatApi.messageType.customer) {
          cn = "sinclo_re";
        }
        else if (obj.messageType === chatApi.messageType.company) {
          cn = "sinclo_se";
          elm.value = "";
        }
        chatApi.createMessage(cn, obj.chatMessage);
      }
      else {
        alert('メッセージの送信に失敗しました。');
      }
    });

    // チャットメッセージ既読処理結果関数
    socket.on('retReadChatMessage', function(d){
      var obj = JSON.parse(d);
      console.log('retReadChatMessage', obj);
      $scope.monitorList[obj.tabId].chatUnread = {id: null, cnt: 0};
    });

    // =======================================
    //   チャット関連受信ここまで
    // =======================================

  }]);

  sincloApp.filter('customDate', function(){
    return function(input) {
      var d = new Date(Number(input)),
          date = d.getFullYear() + '/' + ( '0' + (d.getMonth() + 1) ).slice(-2) + '/' + ('0' + d.getDate()).slice(-2),
          time = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
      return date + " " + time;
    };
  });

  // http://stackoverflow.com/questions/18313576/confirmation-dialog-on-ng-click-angularjs
  sincloApp.directive('ngConfirmClick', [
    function(){
      return {
        priority: -1,
        restrict: 'A',
        link: function(scope, element, attrs){
          element.bind('click', function(e){
            var message = attrs.ngConfirmClick;
            if(message && !confirm(message)){
              e.stopImmediatePropagation();
              e.preventDefault();
            }
          });
        }
      }
    }
  ]);

  sincloApp.directive('calStayTime', ['$timeout', function($timeout){
    return {
      restrict: "A",
      template: "{{stayTime}}",
      link: function(scope, attr, elem) {
        scope.stayTime = scope.monitor.term;

        function _numPad(str){
          return ("0" + str).slice(-2);
        }

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
