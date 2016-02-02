<script type="text/javascript">
'use strict';

(function(){
var sincloApp = angular.module('sincloApp', ['ngSanitize']),
    modalFunc;

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
        }
        else {
          socket.emit("requestSyncStop", obj);
        }
      }
    });

  }]);

  sincloApp.filter('customDate', function(){
    return function(input) {
      var d = new Date(Number(input)),
          date = d.getFullYear() + '/' + ( '0' + (d.getMonth() + 1) ).slice(-2) + '/' + ('0' + d.getDate()).slice(-2),
          time = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
      return date + " " + time;
    };
  });

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
