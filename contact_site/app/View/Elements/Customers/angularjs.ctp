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
        sorry: 4,
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
        prevStatus: false,
        emit: function(tabId, status){
          if ( tabId === "" ) return false;
          var sendToCustomer = true;
          if ( this.prevStatus === status ) {sendToCustomer = false};
          this.prevStatus = status;
          if ( document.getElementById('sendMessage') === undefined ) return false;
          var value = document.getElementById('sendMessage').value;
          emit('sendTypeCond', {
            type: chatApi.observeType.cnst.company, // company
            tabId: tabId,
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
        if ( !window.hasOwnProperty('socket') ) return false;
        socket.on(eventName, function () {
          var args = arguments;
          $rootScope.$apply(function () {
            callback.apply(socket, args);
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

  sincloApp.controller('MainCtrl', ['$scope', 'angularSocket', '$timeout', function($scope, socket, $timeout) {
    $scope.searchText = "";
    $scope.chatMessage = "";
    $scope.oprCnt = 0; // 待機中のオペレーター人数
    $scope.oprWaitCnt = 0; // 総オペレーター人数
    $scope.labelHideList = <?php echo json_encode($labelHideList) ?>;
    $scope.monitorList = {};
    $scope.customerList = {};
    $scope.messageList = [];
    $scope.chatOpList = [];
    $scope.chatList = [];
    $scope.typingMessageSe = "";
    $scope.typingMessageRe = {};
    $scope.scInfo = { remain: 0 };
    $scope.chatLogList = []; // 詳細情報のチャットログリスト
    $scope.chatLogMessageList = []; // 詳細情報のチャットログメッセージリスト
    /* 資料検索 */
    $scope.tagList = {};
    $scope.documentList = {};
    /* 資料検索 */

    /* 定数 */
    $scope.jsConst = {
      tabInfo: <?php echo json_encode($tabStatusList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>,  // タブ状態の定数
      tabInfoStr: <?php echo json_encode($tabStatusStrList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?> // タブ状態の定数

    };

    $scope.search = function(array){
      var result = {}, targetField;
      targetField = ( Number($scope.fillterTypeId) === 2 ) ? 'ipAddress' : 'accessId';
      if ( $scope.searchText ) {
        angular.forEach(array, function(value, key) {
          if ( value[targetField].indexOf($scope.searchText) === 0) {
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
      var res = 1, num = $scope.monitorList[tabId].stayCount;
      if ( angular.isNumber(num) && Number(num) > 0 ) {
        res = num;
      }
      $scope.monitorList[tabId].stayCount = res;
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

    $scope.ui = function(m){
      var showData = [];
      if ( $scope.customerList.hasOwnProperty(m.userId) && isset($scope.customerList[m.userId]) ) {
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
      window.open(
        "<?= $this->Html->url(['controller' => 'Customers', 'action' => 'docFrame']) ?>?tabInfo=" + encodeURIComponent($scope.docShareId) + "&docId=" + doc.id,
        "doc_monitor_" + $scope.docShareId,
        "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
      );
      $scope.closeDocumentList();
    };

    $scope.closeDocumentList = function() {
      $("#ang-popup").removeClass("show");
    };

    /**************************************************************
     *  資料共有　ここまで
     * ************************************************************/

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
      if (!isset(detailId) ) return false;
      return ( typeof($scope.detail) === "object" && $scope.detail.hasOwnProperty('chat') && Number($scope.detail.chat) === Number(myUserId));
    };

    $scope.objCnt = function(list){
      if ( angular.isUndefined(list) ) return 0;
      var ret = Object.keys(list);
      return ret.length;
    };

    $scope.customerMainClass = "";

    $scope.confirmFlg = false;
    $scope.sendMessageConnectConfirm = function(detailId){
        var monitor = $scope.monitorList[detailId], message = "";
        if ( $scope.confirmFlg ) return false;
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
            popupEvent.close();
            return true;
          };
          popupEvent.closePopup = function(){
              $("#sendMessage").focus();
              $scope.ngChatApi.connect();
              $("#sendMessage").val("").focus();
              if ( String($('#changeOpStatus').data('status')) !== "<?=C_OPERATOR_ACTIVE?>" ) {
                chgOpStatus(); // 在席ステータスにする
              }
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
        popupEvent.close(); // モーダルを閉じる
      };
    };

    $scope.showDetail = function(tabId){
      $("#sendMessage").attr('value', '');
      // ポップアップを閉じる
      if ( $scope.customerMainClass !== "" ) {
        $("#customer_sub_pop").css("display", "none");
        $scope.customerMainClass = "";
        $scope.detailId = "";
        if ( contract.chat ) {
          $scope.typingMessageSe = "";
          $scope.achievement = "";
          $scope.messageList = [];
          chatApi.userId = "";
          chatApi.observeType.emit(chatApi.tabId, false);
          $("#chatTalk message-list").children().remove();
        }

        $("#customer_list tr.on").removeClass('on');

        if ( chatApi.tabId !== tabId ) {
          window.setTimeout(function(){
            $scope.showDetail(tabId);
          }, 300);
        }
        chatApi.tabId = "";
      }
      // ポップアップを開く
      else {
        setPositionOfPopup(); // ポップアップの位置調整
        $scope.customerMainClass = "showDetail";
        $scope.detailId = tabId;
        chatApi.tabId = tabId;
        // チャット契約の場合
        if ( contract.chat ) {
          chatApi.token = makeToken(); // トークンを発行
          chatApi.getMessage($scope.monitorList[tabId]); // チャットメッセージを取得
          chatApi.userId = $scope.monitorList[tabId].userId;
          $("#monitor_" + tabId).addClass('on'); // 対象のレコードにクラスを付ける
          $scope.chatAreaShowFlg = true;
          $("#chatContent").addClass("connectChat");
          // チャットエリアに非表示用のクラスを付ける
          // チャット対応上限が有効かつ、自身が担当していない場合
          if ( "<?=$scFlg?>" === "<?=C_SC_ENABLED?>" && !(isset($scope.monitorList[tabId].chat) && $scope.monitorList[tabId].chat === myUserId) ) {
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

    // 顧客の詳細情報を取得する
    $scope.getOldChat = function(historyId){
      $scope.chatLogMessageList = [];
      $.ajax({
        type: "GET",
        url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'remoteGetOldChat'])?>",
        data: {
          historyId:  historyId
        },
        dataType: "json",
        success: function(json){
          angular.element("message-list-descript").attr("class", "off");
          $scope.chatLogMessageList = json;
        }
      });
    };

    /* タブ状態を文字列で返す */
    $scope.tabStatusStr = function (tabId){
      var n = ($scope.monitorList.hasOwnProperty(tabId)) ? $scope.monitorList[tabId].status : <?=C_WIDGET_TAB_STATUS_CODE_OUT?>;
      return $scope.jsConst.tabInfoStr[n];
    }

    /* キャンペーン情報を取得する */
    $scope.getCampaign = function (prev){
      var str = "";
      if ( !(prev.hasOwnProperty('length') && angular.isDefined(prev[0]) && prev[0].hasOwnProperty('url'))  ) return "";
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
        var strings = message.split('\n');
        var custom = "";
        var linkReg = RegExp(/http(s)?:\/\/[!-~.a-z]*/);
        var radioName = "sinclo-radio" + Object.keys(chat).length;
        var option = ( typeof(opt) !== 'object' ) ? { radio: true } : opt;
        for (var i = 0; strings.length > i; i++) {
            var str = escape_html(strings[i]);
            // ラジオボタン
            var radio = str.indexOf('[]');
            if ( option.radio && radio > -1 ) {
                var val = str.slice(radio+2);
                str = "<input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + val + "' disabled=''>";
                str += "<label class='pointer' for='" + radioName + "-" + i + "'>" + val + "</label>";
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
      };

    // 【チャット】チャット枠の構築
    $scope.createMessage = function(elem, chat){
      var cn = "";
      var li = document.createElement('li');
      var content = "";

      var type = Number(chat.messageType);
      var message = chat.message;
      var userId = Number(chat.userId);
      // 消費者からのメッセージの場合
      if ( type === chatApi.messageType.customer) {
        cn = "sinclo_re";
        li.className = cn;
        content = $scope.createTextOfMessage(chat, message, {radio: false});
      }
      // オートメッセージの場合
      else if ( type === chatApi.messageType.company) {
        cn = "sinclo_se";
        var chatName = widget.subTitle;
        if ( Number(widget.showName) === <?=C_WIDGET_SHOW_NAME?> ) {
          chatName = userList[Number(userId)];
        }
        content = "<span class='cName'>" + chatName + "</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.auto || type === chatApi.messageType.sorry) {
        cn = "sinclo_auto";
        content = "<span class='cName'>自動応答</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else  {
        cn = "sinclo_etc";
        var userName = "オペレーター";
        if ( Number(widget.showName) === <?=C_WIDGET_SHOW_NAME?> && userId ) {
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
      $(elem).append(li);
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
      else if (Number(type) === chatApi.messageType.auto) {
        cn = "sinclo_auto";
      }
      return cn;
    };

    $scope.chgOpStatus = function(){
      var opState = $('#changeOpStatus'),
          status = opState.data('status');

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

      <?php if(isset($scNum)): /* チャット応対上限 */ ?>
        // チャット応対上限に達している場合は、通知を出さない
        if ( !(monitor.hasOwnProperty('chat') && String(monitor.chat) === "<?=$muserId?>") && $scope.scInfo.remain < 1) return false;
      <?php endif; ?>
        // 詳細を開いてる且つ、企業がアクティブタブの場合は、通知を出さない
        if ( angular.isDefined($scope.detailId) && $scope.detailId !== "" && document.hasFocus() ) return false;
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
            if (target.indexOf(opt.keyword) >= 0) {
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
          else {
            scDown();
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

      if ( 'ipAddress' in obj && 'ipAddress' in obj) {
        if (!$scope.checkToIP(obj.ipAddress)) return false;
      }

      $scope.monitorList[obj.tabId] = obj;
      $scope.getCustomerInfoFromMonitor(obj);

      if ( 'referrer' in obj && 'referrer' in obj) {
        $scope.monitorList[obj.tabId].ref = $scope.trimToURL(obj.referrer);
      }

      if ( 'connectToken' in obj && 'responderId' in obj) {
        $scope.monitorList[obj.tabId].connectToken = obj.connectToken;
        $scope.monitorList[obj.tabId].responderId = obj.responderId; // ここいる？
      }

      if ( 'docShareId' in obj ) {
        $scope.monitorList[obj.tabId].docShare = true;
        $scope.monitorList[obj.tabId].responderId = obj.docShareId;
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
<?php if ( $coreSettings[C_COMPANY_USE_CHAT] && strcmp(intval($scFlg), C_SC_ENABLED) === 0 ) :  ?>

      // チャット対応上限を設定
      if ( obj.hasOwnProperty('scNum') && Number("<?=$muserId?>") === Number(obj.userId) ) {
        $scope.scInfo.remain = Number(obj.scNum);

        if ( obj.hasOwnProperty('scInfo') && obj.scInfo.hasOwnProperty(obj.userId) ) {
          $scope.scInfo.remain -= Number(obj.scInfo[Number(obj.userId)]);
        }

      }
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
            scDown(); // チャットのスクロール
          }
        }

    });

    socket.on('resAutoChatMessage', function(d){
        var obj = JSON.parse(d);
        if (obj.tabId === chatApi.tabId ) {
            var chat = obj;
            chat.sort = Number(chat.sort);
            $scope.messageList.push(obj);
            scDown(); // チャットのスクロール
        }
    });

    socket.on('sendCustomerInfo', function (data) {
      var obj = JSON.parse(data);
      pushToList(obj);
    });


    /* タブ状態受け渡し */
    // タブ状態を取得
    socket.on('retTabInfo', function (d) {
      var obj = JSON.parse(d);
      if ( !(obj.tabId in $scope.monitorList) ) return false;
      $scope.monitorList[obj.tabId].status = obj.status;
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
      if ( angular.isDefined(tabId) && tabId.length > 0 && tabId.indexOf('_frame') > -1 ) {
        tabId = tabId.substr(0, tabId.indexOf('_frame'));
      }
      // 消費者
      if ( $scope.monitorList.hasOwnProperty(tabId) ) {

        if ( 'widget' in obj ) {
          $scope.monitorList[tabId].widget = obj.widget;
          if ( chatApi.tabId === tabId ) {
            chatApi.observeType.emit(chatApi.tabId, chatApi.observeType.status);

          }
        }
        if ( 'connectToken' in obj ) { $scope.monitorList[tabId].connectToken = obj.connectToken; }
        if ( 'prev' in obj ) { $scope.monitorList[tabId].prev = obj.prev; }
        if ( 'title' in obj ) { $scope.monitorList[tabId].title = obj.title; }
        if ( 'url' in obj ) { $scope.monitorList[tabId].url = obj.url; }
        if ( 'responderId' in obj ) { $scope.monitorList[tabId].responderId = obj.responderId; }
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

    socket.on('docShareConnect', function(data){ // 資料共有開始
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined && angular.isDefined($scope.monitorList[obj.tabId])) {
        $scope.monitorList[obj.tabId].docShare = true;
        $scope.monitorList[obj.tabId].responderId = obj.responderId;
      }
    })

    socket.on('docDisconnect', function(data){ // 資料共有終了
      var obj = JSON.parse(data);
      if ( obj.tabId !== undefined && angular.isDefined($scope.monitorList[obj.tabId])) {
        $scope.monitorList[obj.tabId].docShare = "";
        if ( angular.isUndefined($scope.monitorList[obj.tabId].connectToken) ) {
          $scope.monitorList[obj.tabId].responderId = "";
        }
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

      if ( Number(obj.messageType) === 98 ) {
        $scope.chatOpList.push(obj.userId);
        if ( obj.userId === myUserId ) {
          $scope.achievement = obj.achievementFlg;
        }
      }


      if ( obj.userId === myUserId && obj.ret ) {
        pushToChatList(obj.tabId);
        // $("#sendMessage").focus();
        // 既読にする
        chatApi.isReadMessage($scope.monitorList[obj.tabId]);
      }
      else {
        $scope.chatList = $scope.chatList.filter(function(v){
          return (v !== this.t);
        }, {t: obj.tabId});

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
<?php endif; ?>


    });

    // チャットメッセージ群の受信
    socket.on("chatMessageData", function(d){
      var obj = JSON.parse(d); $scope.achievement = "", $scope.chatOpList = [];

      for (var key in obj.chat.messages) {
        var chat = {};
        if ( typeof(obj.chat.messages[key]) === "object" ) {
          chat = obj.chat.messages[key];
        }
        else {
          chat.text = obj.chat.messages[key];
        }
        if ( Number(chat.messageType) === 98 ) {
          $scope.chatOpList.push(chat.userId);
          if ( chat.userId === myUserId ) {
            $scope.achievement = String(chat.achievementFlg);
          }
        }
        chat.sort = Number(key);
        $scope.messageList.push(chat);
        scDown(); // チャットのスクロール
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

    // ポップアップをセンターに表示
    function setPositionOfPopup(){
      var subContent = document.getElementById("sub_contents");
      subContent.style.left = ((window.innerWidth-$("#sidebar-main").outerWidth()) - $("#sub_contents").outerWidth())/2 + "px";
      subContent.style.top = "100px";
    }

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
                v: scope.detail.userId,
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
                  scope.customerList[scope.detail.userId] = angular.copy(value);
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
            return "ここにメッセージ入力してください。\n・" + sendPattarnStr + "で改行されます\n・下矢印キー(↓)で簡易入力が開きます";
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
          if ( !(scope.detail === undefined || scope.detail === {}) && scope.detail.tabId !== scope.detailId ) {
            scope.customData = {};
            scope.customPrevData = {};
          }
          if ( angular.isDefined(scope.detailId) && scope.detailId !== "" && (scope.detailId in scope.monitorList) ) {
            scope.detail = angular.copy(scope.monitorList[scope.detailId]);
            scope.getCustomerInfo(scope.monitorList[scope.detailId].userId, function(ret){
              scope.customData = ret;
              scope.customPrevData = angular.copy(ret);
              if ( angular.isDefined(scope.detailId) && scope.detailId !== "" ) {
                scope.customerList[scope.monitorList[scope.detailId].userId] = angular.copy(scope.customData);
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
