(function($){
  // -----------------------------------------------------------------------------
  //  websocket通信
  // -----------------------------------------------------------------------------

  sinclo = {
    syncTimeout: "",
    operatorInfo: {
      header: null,
      ev: function() {
        var height = 0;
        var sincloBox = document.getElementById('sincloBox');
        var flg = sincloBox.getAttribute('data-openflg');
        var elm = $('#sincloBox');
        if ( String(flg) === "false" ) {
          var height = 0;
          height += $("#sincloBox #widgetHeader").outerHeight(true);
          if ( $("#sincloBox").children().is("#navigation") ) {
            height += $("#sincloBox > #navigation").outerHeight(true);
            var tab = $("#navigation li.selected").data('tab');
            height += $("#" + tab + "Tab").outerHeight(true);
          }
          else {
            height += $("[id$='Tab']").outerHeight(true);
          }
          height += $("#sincloBox > #fotter").outerHeight(true);
          sincloBox.setAttribute('data-openflg', true);
          if ( window.info.contract.chat ) {
            sinclo.chatApi.showUnreadCnt();
            sinclo.chatApi.scDown();
          }
        }
        else {
          height = this.header.offsetHeight;
          sincloBox.setAttribute('data-openflg', false);
        }
        elm.animate({
          height: height + "px"
        }, 'first');
      }
    },
    connect: function(){
      // 新規アクセスの場合
      if ( !check.isset(userInfo.getTabId()) ) {
        userInfo.firstConnection = true;
        window.opener = null;
        userInfo.strageReset();
        userInfo.setReferrer();
        userInfo.setStayCount();
      }
      userInfo.init();

      var emitData = {
          referrer: userInfo.referrer,
          time: userInfo.getTime(),
          page: userInfo.getPage(),
          firstConnection: userInfo.firstConnection,
          userAgent: window.navigator.userAgent,
          service: check.browser(),
          prevList: userInfo.prevList
        };

      // モニタリング中であればスルー
      if ( check.isset(userInfo.connectToken) ) {
        common.load.start();
        if ( Number(userInfo.accessType) === Number(cnst.access_type.guest) ) {
          emitData.connectToken = userInfo.connectToken;
          userInfo.syncInfo.get();
          emit('connectSuccess', {prevList: userInfo.prevList, prev: userInfo.prev});
          emit('connectedForSync', {});

          // チャットの契約をしている場合はウィジェット表示
          if ( window.info.contract.chat ) {
            common.makeAccessIdTag();
          }
        }

        if ( check.isset(common.tmpParams) ) {
          browserInfo.resetPrevList();
          emit('requestSyncStart', {
            accessType: common.params.type
          });
        }
        emit('reqUrlChecker', {});

        browserInfo.setPrevList();

        if ( !check.isset(common.tmpParams) ) {
          emit('connectContinue', {
            connectToken: userInfo.connectToken,
            accessType: common.params.type,
            receiverID: userInfo.vc_receiverID
          });

          var vcInfo = common.getVcInfo();
          if(typeof vcInfo !== 'undefined') {
            vcPopup.set(vcInfo.toTabId, vcInfo.receiverID);
          }

          window.clearTimeout(this.syncTimeout);
          this.syncTimeout = window.setTimeout(function(){
            emit('requestSyncStop', emitData);
            userInfo.syncInfo.unset();
          }, 5000);

        }
        return false;
      }
      emit('connected', {
        type: 'user',
        data: emitData
      });
    },
    retConnectedForSync: function (d) {
      var obj = common.jParse(d);
      if ( ('pagetime' in obj) ) {
        userInfo.pageTime = obj['pagetime'];
      }
    },
    accessInfo: function(d){
      var obj = common.jParse(d);
      if ( obj.token !== common.token ) return false;

      if ( ('activeOperatorCnt' in obj) ) {
        window.info.activeOperatorCnt = obj['activeOperatorCnt'];
      }
      if ( ('pagetime' in obj) ) {
        userInfo.pageTime = obj['pagetime'];
      }

      if ( check.isset(obj.accessId) && !check.isset(obj.connectToken)) {
        userInfo.set(cnst.info_type.access, obj.accessId, true);

        var setWidgetFnc = function(){
          if ( window.info.widget === undefined ) {
            setTimeout(setWidgetFnc, 500);
          }
          else {
            common.makeAccessIdTag();
          }
        };

        setWidgetFnc();

      }

      if ( obj.firstConnection ) {
        if ( !check.isset(userInfo.userId) && check.isset(obj.userId) ) {
          userInfo.set(cnst.info_type.user, obj.userId);
        }

        if ( userInfo.accessType === Number(cnst.access_type.guest) ) {
          userInfo.set(cnst.info_type.ip, obj.ipAddress);
          userInfo.set(cnst.info_type.time, obj.time, true);
        }
        userInfo.setTabId();
      }

      obj['prev'] = userInfo.prev;

      emit('customerInfo', obj);
      emit('connectSuccess', {
        confirm: false,
        widget: window.info.widgetDisplay,
        prevList: userInfo.prevList,
        userAgent: window.navigator.userAgent,
        time: userInfo.time,
        ipAddress: userInfo.getIp(),
        referrer: userInfo.referrer
      });
    },
    setHistoryId: function(){
        var createStartTimer,
            createStart = function(){
                var sincloBox = document.getElementById('sincloBox');
                var widgetOpen = storage.s.get('widgetOpen');
                sincloBox.style.display = "block";
                sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
                // ウィジェット表示
                if (!widgetOpen) {
                  if ( (('maxShowTime' in window.info.widget) && String(window.info.widget.maxShowTime).match(/^[0-9]{1,2}$/) !== null) && ('showTime' in window.info.widget) && String(window.info.widget.showTime) === "1" ) {
                    window.setTimeout(function(){
                      var flg = sincloBox.getAttribute('data-openflg');
                      if ( String(flg) === "false" ) {
                        storage.s.set('widgetOpen', true);
                        sinclo.operatorInfo.ev();
                      }
                    }, Number(window.info.widget.maxShowTime) * 1000);
                  }
                  else {
                      storage.s.set('widgetOpen', true);
                  }
                }
                if ( window.info.contract.chat ) {
                    // チャット情報読み込み
                    sinclo.chatApi.init();
                }
        };

        createStartTimer = window.setInterval(function(){
          if (window.info.widgetDisplay && !sinclo.trigger.flg) {
            window.clearInterval(createStartTimer);
            createStart();
          }
        }, 500);

    },
    getAccessInfo: function(d) { // guest only
      var obj = common.jParse(d);
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var emitData = userInfo.getSendList();
      emitData.receiveAccessInfoToken = obj.token;
      emitData.widget = window.info.widgetDisplay;
      emit('sendAccessInfo', emitData);
    },
    confirmCustomerInfo: function(d) {
      var obj = common.jParse(d);
      if ( userInfo.tabId !== obj.tabId ) return false;
      if ( userInfo.accessType !== cnst.access_type.guest ) return false;
      var emitData = userInfo.getSendList();
      emitData.receiveAccessInfoToken = obj.token;
      emit('customerInfo', emitData);
    },
    getConnectInfo: function(d) {
      var obj = common.jParse(d);
      if ( userInfo.tabId !== obj.tabId ) return false;
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      if ( userInfo.connectToken !== obj.connectToken ) return false;
      emit('sendConnectInfo', {
        accessType: userInfo.accessType,
        tabId: userInfo.getTabId(),
        userId: userInfo.userId,
        accessId: userInfo.accessId,
        connectToken: userInfo.connectToken
      });
    },
    getWindowInfo: function(obj) {
      if ( obj.tabId !== userInfo.tabId ) return false;
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      var title = location.host + 'の内容';
      var content = location.host + 'が閲覧ページへのアクセスを求めています。<br>許可しますか';
      popup.ok = function(){
        userInfo.connectToken = obj.connectToken;
        browserInfo.resetPrevList();

        emit('sendWindowInfo', {
          userId: userInfo.userId,
          tabId: userInfo.tabId,
          connectToken: userInfo.connectToken,
          // 解像度
          screen: browserInfo.windowScreen(),
          // ブラウザのサイズ
          windowSize: browserInfo.windowSize(),
          // スクロール位置の取得
          scrollPosition: browserInfo.windowScroll()
        });

/*        vcPopup.set(userInfo.tabId, userInfo.vc_receiverID);

        // sendWindowInfoとほぼ同時にメッセージを送信してしまうと
        // 企業側がFireFoxの場合windowを開くタイミングでapplyができないためウェイトを挟む
        setTimeout(function(){
          emit('videochatConfirmOK', {
            userId: userInfo.userId,
            fromTabId: userInfo.tabId,
            fromConnectToken: userInfo.connectToken,
            receiverID: userInfo.vc_receiverID
          });
        }, 300);
        // 開始したタイミングでビデオチャット情報をセッションストレージに保存
        common.saveVcInfo();*/
        this.remove();
      };
      popup.set(title, content);
    },
    windowSyncInfo: function(d) {
      var obj = common.jParse(d);
      browserInfo.set.scroll(obj.scrollPosition);
    },
    syncStart: function(d) {
      var obj = common.jParse(d);
      if ( Number(userInfo.accessType) === Number(cnst.access_type.host) ) {
        window.clearTimeout(this.syncTimeout);
        return false;
      }
      var sincloBox = document.getElementById('sincloBox');
      // チャット未契約のときはウィジェットを非表示
      if (sincloBox && !window.info.contract.chat) {
        sincloBox.style.display = "none";
      }
      common.load.start();
      userInfo.setConnect(obj.connectToken);
      if ( !check.isset(userInfo.sendTabId) ) {
        userInfo.sendTabId = obj.from;
        userInfo.syncInfo.set();
      }
      else {
       userInfo.syncInfo.get();
      }
      // フォーム情報収集
      var inputInfo = [];
      $('input').each(function(){
        inputInfo.push(this.value);
      });
      var checkboxInfo = [];
      $('input[type="checkbox"]').each(function(){
        checkboxInfo.push(this.checked);
      });
      var radioInfo = [];
      $('input[type="radio"]').each(function(){
        radioInfo.push(this.checked);
      });
       var textareaInfo = [];
      $('textarea').each(function(){
        textareaInfo.push(this.value);
      });
       var selectInfo = [];
      $('select').each(function(){
        selectInfo.push(this.value);
      });

      emit('getSyncInfo', {
        userId: userInfo.userId,
        connectToken: userInfo.connectToken,
        inputInfo: inputInfo,
        checkboxInfo: checkboxInfo,
        radioInfo: radioInfo,
        textareaInfo: textareaInfo,
        selectInfo: selectInfo,
        // スクロール位置の取得
        scrollPosition: browserInfo.windowScroll()
      });
    },
    syncElement: function(d){
      var obj = common.jParse(d);
      var scrollSize = browserInfo.scrollSize();
      window.clearTimeout(this.syncTimeout);
      $("html, body").animate(
        {
          scrollLeft: scrollSize.x * obj.scrollPosition.x,
          scrollTop: scrollSize.y * obj.scrollPosition.y
        },
        {
            duration: 'first',
            easing: 'swing',
            complete: function(){
              for ( var i in obj.inputInfo ) {
                var n = Number(i);
                $('input').eq(n).val(obj.inputInfo[n]);
              }
              for ( var i in obj.checkboxInfo ) {
                var n = Number(i);
                $('input[type="checkbox"]').eq(n).prop("checked", obj.checkboxInfo[n]);
              }
              for ( var i in obj.radioInfo ) {
                var n = Number(i);
                $('input[type="radio"]').eq(n).prop("checked", obj.radioInfo[n]);
              }
              for ( var i in obj.textareaInfo ) {
                var n = Number(i);
                $('textarea').eq(n).val(obj.textareaInfo[n]);
              }
              for ( var i in obj.selectInfo ) {
                var n = Number(i);
                $('select').eq(n).val(obj.selectInfo[n]);
              }
              emit('syncCompleate', {
                userId: userInfo.userId,
                accessType: userInfo.accessType
              });
            }
        }
      );
    },
    syncEvStart: function(d){
      var obj = common.jParse(d);
      if ( obj.to !== userInfo.tabId && obj.from !== userInfo.tabId ) return false;
      syncEvent.start(true);
      window.clearTimeout(sinclo.syncTimeout);
      common.load.finish();
    },
    receiveScTimer: false,
    syncResponce: function(d){
      var obj = common.jParse(d), cursor = common.cursorTag;
      // 画面共有用トークンでの認証に変更する？
      if ( obj.to !== userInfo.tabId ) return false;
      if ( Number(obj.accessType) === Number(userInfo.accessType) ) return false;
      // カーソルを作成していなければ作成する
      if ( !document.getElementById('cursorImg') ) {
        $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mousePoint.x + '; left:' + obj.mousePoint.y + '; z-index:999999"><img width="50px" src="' + window.info.site.files + '/img/pointer.png"></div>');
        cursor = common.cursorTag = document.getElementById("cursorImg");
      }
      else {
        // スクロール位置
        if ( check.isset(obj.scrollPosition) ) {
          syncEvent.receiveEvInfo.type = "scroll";
          syncEvent.receiveEvInfo.nodeName = "body";
          if (this.receiveScTimer) {
            clearTimeout(this.receiveScTimer);
          }

          browserInfo.set.scroll(obj.scrollPosition);

          // TODO まだ微調整が必要
          this.receiveScTimer = setTimeout(function(){
            syncEvent.receiveEvInfo = { nodeName: null, type: null };
          }, browserInfo.interval);
        }
      }
      // カーソル位置
      if ( check.isset(obj.mousePoint)) {
        cursor.style.left = obj.mousePoint.x + "px";
        cursor.style.top  = obj.mousePoint.y + "px";
      }

    },
    syncResponceEv: function (d) {
      var obj = common.jParse(d), elm;
      if ( obj.to !== userInfo.tabId ) return false;
      if ( obj.accessType === userInfo.accessType ) return false;
      elm = $(String(obj.nodeName)).eq(Number(obj.idx));
      syncEvent.receiveEvInfo.type = obj.type;
      syncEvent.receiveEvInfo.nodeName = String(obj.nodeName);
      syncEvent.receiveEvInfo.idx = Number(obj.idx);
      switch (obj.type) {
        case "change":
          if ( String(obj.nodeType) === "radio" || String(obj.nodeType) === "checkbox" ) {
            elm.prop('checked', obj.checked);
          }
        case "keyup":
          elm.val(obj.value);
          break;
        case "scroll":
          var elm = $(obj.nodeName).eq(Number(obj.idx));
          if ( elm.length > 0 ) {
            var scrollBarSize = {
                  height: elm[0].scrollHeight - elm[0].clientHeight,
                  width: elm[0].scrollWidth - elm[0].clientWidth
                };
                elm.stop(false, false).scrollTop(scrollBarSize.height * Number(obj.value.topRatio));
                elm.stop(false, false).scrollLeft(scrollBarSize.width * Number(obj.value.leftRatio));
          }
      };
      syncEvent.receiveEvInfo = { nodeName: null, type: null };
    },
    receiveConnectEv: function(d){
      var obj = JSON.parse(d);
      if ( obj.to !== userInfo.tabId ) return false;
    },
    userDissconnectionEv: function(d){
      var obj = JSON.parse(d);
      if ( obj.connectToken !== userInfo.connectToken ) return false;
      if ( obj.to !== userInfo.tabId ) return false;
      emit('sendConfirmConnect', obj);
    },
    syncContinue:function (d) {
      var obj = JSON.parse(d);
      if ( obj.connectToken !== userInfo.connectToken ) return false;
      if ( obj.to !== userInfo.tabId ) return false;
      emit('requestSyncStart', obj);
    },
    resUrlChecker: function(d){
      var obj = JSON.parse(d);
      if ( obj.url !== browserInfo.href ) {
        location.href = obj.url;
      }
      else {
        emit('requestSyncStart', {
          accessType: common.params.type
        });
      }
    },
    chatStartResult: function(d){
      var obj = JSON.parse(d);
      this.chatApi.online = true;
      storage.s.set('chatAct', true); // オートメッセージを表示しない
      sinclo.chatApi.createNotifyMessage("オペレーターが入室しました");
    },
    chatEndResult: function(d){
      var obj = JSON.parse(d);
      this.chatApi.online = false;
      storage.s.set('chatAct', false); // オートメッセージを表示してもいい
      sinclo.chatApi.createNotifyMessage("オペレーターが退室しました");
    },
    chatMessageData:function(d){
      var obj = JSON.parse(d);
      if ( obj.token !== common.token ) return false;
      this.chatApi.historyId = obj.chat.historyId;
      var keys = Object.keys(obj.chat.messages);
      for (var key in obj.chat.messages) {
        if ( !obj.chat.messages.hasOwnProperty(key) ) return false;
        var chat = obj.chat.messages[key];
        if ( typeof(chat) === "object" ) {
          var cn = (Number(chat.messageType) === 1) ? "sinclo_se" : "sinclo_re";
          if (Number(chat.messageReadFlg) === 0 && chat.messageType === sinclo.chatApi.messageType.company) {
              this.chatApi.unread++;
          }
          this.chatApi.createMessage(cn, chat.message);
        }
        else {
          if ( chat === "start" ) {
            this.chatApi.online = true;
            this.chatApi.createNotifyMessage("オペレーターが入室しました");
          }
          if ( chat === "end" ) {
            this.chatApi.online = false;
            this.chatApi.createNotifyMessage("オペレーターが退室しました");
          }
        }
      }
      if ( !this.chatApi.online && !sinclo.trigger.flg ) {
        // オートメッセージ読み込み
        sinclo.trigger.init();
      }
      // 未読数
      sinclo.chatApi.showUnreadCnt();
    },
    sendChatResult: function(d){
      var obj = JSON.parse(d);
      if ( obj.tabId !== userInfo.tabId ) return false;
      var elm = document.getElementById('sincloChatMessage'), cn;
      if ( obj.ret ) {
        if (obj.messageType === sinclo.chatApi.messageType.company) {
          cn = "sinclo_re";
          sinclo.chatApi.call();
        }
        else if (obj.messageType === sinclo.chatApi.messageType.customer) {
          cn = "sinclo_se";
          elm.value = "";
        }
        if (obj.messageType === sinclo.chatApi.messageType.auto) {
          return false;
        }
        this.chatApi.createMessageUnread(cn, obj.chatMessage);
        // オートメッセージの内容をDBに保存し、オブジェクトから削除する
        if (!sinclo.chatApi.saveFlg) {
          emit("sendAutoChat", {messageList: sinclo.chatApi.autoMessages});
          sinclo.chatApi.autoMessages = [];
          sinclo.chatApi.saveFlg = true;
        }
      }
      else {
        alert('メッセージの送信に失敗しました。');
      }
    },
    sendReqAutoChatMessages: function(d){
      // 自動メッセージの情報を渡す（保存の為）
      var obj = common.jParse(d);
      emit("sendAutoChatMessages", {messages: sinclo.chatApi.autoMessages, sendTo: obj.sendTo});
    },
    resAutoChatMessage: function(d){
        var obj = JSON.parse(d);

        sinclo.chatApi.autoMessages.push({
            chatId:obj.chatId,
            message: obj.message,
            created: obj.created
        });
    },
    confirmVideochatStart: function(obj) {
      // ビデオチャット開始に必要な情報をオペレータ側から受信し、セットする
      if ( obj.toTabId !== userInfo.tabId ) return false;
      if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;
      userInfo.vc_receiverID = obj.receiverID;
      userInfo.vc_toTabId = obj.toTabId;
      common.setVcInfo({receiverID: obj.receiverID, toTabId: obj.toTabId});
    },
    syncStop: function(d){
      var obj = common.jParse(d);
      syncEvent.stop(false);
      window.clearTimeout(sinclo.syncTimeout);

      userInfo.syncInfo.unset();
      if (!document.getElementById('sincloBox')) {
        common.makeAccessIdTag();
      }

      var timer = setInterval(function(){
        if (window.info.widgetDisplay === false) {
          clearInterval(timer);
          return false;
        }
        var sincloBox = document.getElementById('sincloBox');
        // チャット未契約のときはウィジェットを非表示
        if (sincloBox && !window.info.contract.chat) {
          sincloBox.style.display = "block";
          sincloBox.style.height = sinclo.operatorInfo.header.offsetHeight + "px";
          sincloBox.setAttribute('data-openflg', false);
          clearInterval(timer);
        }

      }, 500);
    },
    chatApi: {
        saveFlg: false,
        online: false, // 現在の対応状況
        historyId: null,
        unread: 0,
        messageType: {
            customer: 1,
            company: 2,
            auto: 3
        },
        autoMessages: [],
        init: function(){
            $("#sincloChatMessage").on("keydown", function(e){
                if ( e.keyCode === 13 ) {
                    if ( !e.shiftKey && !e.ctrlKey ) {
                        sinclo.chatApi.push();
                    }
                }
            });

            this.sound = document.getElementById('sinclo-sound');
            if ( this.sound ) {
                this.sound.volume = 0.3;
            }

            $(document).on("change", "input[name^='sinclo-radio']", function(e){
                sinclo.chatApi.send(e.target.value);
            });

            emit('getChatMessage', {});
        },
        createNotifyMessage: function(val){
            var chatTalk = document.getElementById('chatTalk');
            var li = document.createElement('li');
            chatTalk.appendChild(li);
            li.className = "sinclo_etc";
            li.innerHTML = "－　" + val + "　－";
            this.scDown();
        },
        createMessage: function(cs, val){
            var chatTalk = document.getElementById('chatTalk');
            var li = document.createElement('li');
            chatTalk.appendChild(li);
            var strings = val.split('\n');
            var radioCnt = 1;
            var linkReg = RegExp(/http(s)?:\/\/[!-~.a-z]*/);
            var radioName = "sinclo-radio" + chatTalk.children.length;
            var content = "";

            if ( cs === "sinclo_re" ) {
              content = "<span class='cName'>" + window.info.widget.subTitle + "</span>";
            }
            for (var i = 0; strings.length > i; i++) {
                var str = strings[i];

                if ( cs === "sinclo_re" ) {
                    // ラジオボタン
                    var radio = str.indexOf('[]');
                    if ( radio > -1 ) {
                        var val = str.slice(radio+2);
                        str = "<input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + val + "'>";
                        str += "<label for='" + radioName + "-" + i + "'>" + val + "</label>";
                    }
                }
                // リンク
                var link = str.match(linkReg);
                if ( link !== null ) {
                    var url = link[0];
                    var a = "<a href='" + url + "' target='_blank'>" + url + "</a>";
                    str = str.replace(url, a);
                }
                content += str + "\n";

            }
            li.className = cs;
            li.innerHTML = content;
            this.scDown();
        },
        createMessageUnread: function(cs, val){
            if ( cs === "sinclo_re" ) {
                sinclo.chatApi.unread++;
                sinclo.chatApi.showUnreadCnt();
            }
            sinclo.chatApi.createMessage(cs, val);
        },
        scDownTimer: null,
        scDown: function(){
            if ( this.scDownTimer ) {
              clearTimeout(this.scDownTimer);
            }
            this.scDownTimer = setTimeout(function(){
              var chatTalk = document.getElementById('chatTalk');
              $('#chatTalk').animate({
                scrollTop: chatTalk.scrollHeight - chatTalk.clientHeight
              }, 300);
            }, 500);
        },
        push: function(){
            var elm = document.getElementById('sincloChatMessage');
            if ( check.isset(elm.value) ) {
                this.send(elm.value);
            }
        },
        send: function(value){
            storage.s.set('chatAct', true); // オートメッセージを表示しない

            setTimeout(function(){
              emit('sendChat', {
                  historyId: sinclo.chatApi.historyId,
                  chatMessage:value,
                  mUserId: null,
                  messageType: sinclo.chatApi.messageType.customer
              });
            }, 100);

        },
        sound: null,
        call: function(){
            // デスクトップ通知用
            if ( this.sound ) {
                this.sound.play();
            }
        },
        showUnreadCnt: function(){
            var elmId = "sincloChatUnread",
                unreadIcon = document.getElementById(elmId);
            var sincloBox = document.getElementById('sincloBox');
            var flg = sincloBox.getAttribute('data-openflg');
            if ( unreadIcon ) {
                unreadIcon.parentNode.removeChild(unreadIcon);
            }
            if ( Number(sinclo.chatApi.unread) > 0 ) {
                if ($("#chatTab").css("display") !== "none" && String(flg) === "true") {
                    emit("isReadFromCustomer", {});
                    sinclo.chatApi.unread = 0;
                    return false;
                }

                var em = document.createElement('em');
                em.id = elmId;
                em.textContent = sinclo.chatApi.unread;
                var mainImg = document.getElementById('mainImage');
                var titleElm = document.getElementById('widgetTitle');
                if ( mainImg ) {
                    mainImg.appendChild(em);
                }
                else if (titleElm) {
                    titleElm.appendChild(em);
                }
            }
        }
    },
    trigger: {
        flg: false,
        nowSaving: false,
        init: function(){
            if ( !('messages' in window.info) || (('messages' in window.info) && typeof(window.info.messages) !== "object" ) ) return false;
            this.flg = true;
            var messages = window.info.messages;
            // 設定ごと
            for( var i = 0; messages.length > i; i++ ){
                // AND
                if ( Number(messages[i]['activity']['conditionType']) === 1 ) {
                    this.setAndSetting(i, messages[i]['activity'], function(key, ret){
                        var message = messages[key];
                        if (typeof(ret) === 'number') {
                            setTimeout(function(){
                                sinclo.trigger.setAction(message['id'], message['action_type'], message['activity']);
                            }, ret);
                        }
                    });
                }
                // OR
                else {
                    this.setOrSetting(i, messages[i]['activity'], function(key, ret){
                        var message = messages[key];
                        if (typeof(ret) === 'number') {
                            setTimeout(function(){
                                sinclo.trigger.setAction(message['id'], message['action_type'], message['activity']);
                            }, ret);
                        }
                    });
                }
            }
        },
        /**
         * return 即時実行(0)、タイマー実行(ミリ秒)、非実行(null)
         */
        setAndSetting: function(key, setting, callback) {
            var keys = Object.keys(setting['conditions']);
            var ret = 0;
            for(var i = 0; keys.length > i; i++){

                var conditions = setting['conditions'][keys[i]];
                var last = (keys.length === Number(i+1)) ? true : false;
                switch(Number(keys[i])) {
                    case 1: // 滞在時間
                        this.judge.stayTime(conditions[0], function(err, timer){
                            if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                ret = Number(timer);
                            }
                            if (err) ret = null;
                        });
                        break;
                    case 2: // 訪問回数
                        this.judge.stayCount(conditions[0], function(err, timer){
                            if (err) ret = null;
                        });
                        break;
                    case 3: // ページ
                        this.judge.page(conditions[0], function(err, timer){
                            if (err) ret = null;
                        });
                        break;
                    case 4: // 曜日・時間
                        this.judge.dayTime(conditions[0], function(err, timer){
                            if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                ret = Number(timer);
                            }
                            if (err) ret = null;
                        });
                        break;
                    case 5: // リファラー
                        this.judge.referrer(conditions[0], function(err, timer){
                            if (err) ret = null;
                        });
                        break;
                    case 6: // 検索ワード
                        this.judge.searchWord(conditions[0], function(err, timer){
                            if (err) ret = null;
                        });
                        break;
                    default:
                        ret = null;
                        break;
                }
                if (ret === null) break;
            }
            callback(key, ret);
        },
        /**
         * return 即時実行(0)、タイマー実行(ミリ秒)、非実行(null)
         */
        setOrSetting: function(key, setting, callback) {
            var keys = Object.keys(setting['conditions']);
            var ret = null;
            for(var i = 0; keys.length > i; i++){
                var conditions = setting['conditions'][keys[i]];
                var last = (keys.length === Number(i+1)) ? true : false;
                switch(Number(keys[i])) {
                    case 1: // 滞在時間
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.stayTime(conditions[u], function(err, timer){
                              if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                  ret = Number(timer);
                              }
                          });
                        }
                        break;
                    case 2: // 訪問回数
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.stayCount(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 3: // ページ
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.page(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 4: // 曜日・時間
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.dayTime(conditions[u], function(err, timer){
                              if ( !err && (typeof(timer) === "number" && ret <= timer) ) {
                                  ret = Number(timer);
                              }
                          });
                        }
                        break;
                    case 5: // リファラー
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.referrer(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    case 6: // 検索ワード
                        for (var u = 0; u < conditions.length; u++) {
                          this.judge.searchWord(conditions[u], function(err, timer){
                              if ( !err ) {
                                  ret = 0;
                              }
                          });
                        }
                        break;
                    default:
                        break;
                }
            }

            callback(key, ret);
        },
        setAutoMessage: function(id, cond){
            var data = {
                chatId:id,
                message:cond.message
            };

            if ( sinclo.chatApi.saveFlg ) {
                // オートメッセージの内容をDBに保存し、オブジェクトから削除する
                emit("sendAutoChat", {messageList: [data]});
            }
            else {
                emit('sendAutoChatMessage', data);
            }
        },
        setAction: function(id, type, cond){
            // TODO 今のところはメッセージ送信のみ、拡張予定
            var chatActFlg = storage.s.get('chatAct');
            if ( !check.isset(chatActFlg) ) {
              chatActFlg = "false";
            }

            if ( String(type) === "1" && ('message' in cond) && (String(chatActFlg) === "false") ) {
                sinclo.chatApi.createMessageUnread("sinclo_re", cond.message);
                var prev = sinclo.chatApi.autoMessages;

                var setAutoMessageTimer = setInterval(function(){
                    var date = common.fullDateTime();
                    if ( prev.length === 0 || (prev.length > 0 && prev[prev.length - 1].created !== date) ) {
                        clearInterval(setAutoMessageTimer);
                        sinclo.trigger.setAutoMessage(id, cond);
                    }
                }, 1);

            }
        },
        common: {
            /**
             * @params int type 比較種別
             * @params int a 基準値
             * @params int b 比較対象
             * @return bool
             */
            numMatch: function(type, a, b) {
                switch(Number(type)) {
                    case 1: // 一致
                      if (Number(a) ===  Number(b) ) return true;
                      break;
                    case 2: // 以上
                      if (Number(a) >= Number(b) ) return true;
                      break;
                    case 3: // 未満
                      if (Number(a) < Number(b) ) return true;
                      break;
                }
                return false;
            },
            /**
             * @params int type 比較種別
             * @params int a キーワード
             * @params int b マッチ対象
             * @return bool
             */
            pregMatch: function(type, a, b) {
                var preg = "";
                switch(Number(type)) {
                    case 1: // 一致
                      preg = new RegExp("^" + a + "$");
                      return preg.test(b);
                      break;
                    case 2: // 部分一致
                      preg = new RegExp(a);
                      return preg.test(b);
                      break;
                    case 3: // 不一致
                      preg = new RegExp("^" + a + "$");
                      return !preg.test(b);
                      break;
                }
                return false;
            }
        },
        judge: {
            stayTime: function(cond, callback){
                if (!('stayTimeCheckType' in cond) || !('stayTimeType' in cond) || !('stayTimeRange' in cond )) return callback(true, null);
                var time = 0;
                switch(Number(cond.stayTimeType)) {
                    case 1: // 秒
                      time = Number(cond.stayTimeRange) * 1000;
                      break;
                    case 2: // 分
                      time = Number(cond.stayTimeRange) * 1000 * 60;
                      break;
                    case 3: // 時
                      time = Number(cond.stayTimeRange) * 1000 * 60 * 60;
                      break;
                }

                // ページ
                if ( Number(cond.stayTimeCheckType) === 1 ) {
                    callback(false, time);
                }
                // サイト
                else {
                    var term = (Number(userInfo.pageTime) - Number(userInfo.time));
                    if ( term <= time ) {
                        callback(false, (time-term));
                    }
                    else {
                        callback(true, null);
                    }
                }

            },
            stayCount: function(cond, callback){
                if (!('visitCntCond' in cond) || !('visitCnt' in cond )) return callback(true, null);
                if (sinclo.trigger.common.numMatch(cond.visitCntCond, userInfo.getStayCount(), cond.visitCnt)) {
                    callback(false, 0);
                }
                else {
                    callback(true, null);
                }
            },
            page: function(cond, callback){
                if (!('keyword' in cond) || !('targetName' in cond ) || !('stayPageCond' in cond )) return callback(true, null);
                var target = ( Number(cond.targetName) === 1 ) ? common.title() : location.href ;
                if (sinclo.trigger.common.pregMatch(cond.stayPageCond, cond.keyword, target)) {
                    callback(false, 0);
                }
                else {
                    callback(true, null);
                }
            },
            dayTime: function(cond, callback){
                if (!('day' in cond) || !('timeSetting' in cond)) return callback(true, null );
                if (Number(cond.timeSetting) === 1 && (!('startTime' in cond) || !('endTime' in cond))) return callback(true, null);
                // DBに保存している文字列から、JSのgetDay関数に対応する数値を返す関数
                function translateDay(str){
                    var day = {'sun':0, 'mon':1, 'tue':2, 'wed':3, 'thu':4, 'fri':5, 'sat':6};
                    return (str in day) ? day[str] : null;
                }
                function checkTime(time){
                    var reg = new RegExp(/^(0{0,1}[0-9]{1}|1[0-9]{1}|2[0-3]{1}):([0-5]{1}[0-9]{1})$/);
                    return reg.test(time);
                }
                function makeDate(date){
                    var d = new Date(date);
                    return Date.parse(d);
                }
                var d = new Date(), date, dateParse, nowDay, nextDay, keys, dayList = [];
                // 今日の曜日
                nowDay = d.getDay();
                // 明日の曜日
                nextDay = Math.abs((nowDay + 1 > 6) ? 0 : nowDay + 1);
                // 今日の日付
                date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate() + " ";
                dateParse = Date.parse(d);
                keys = Object.keys(cond.day);
                for(var i = 0; keys.length > i; i++){
                  if (!cond.day[keys[i]]) {
                    if ((keys.length - 1) === i) return callback(true, null); // 最終行だった場合はfalse
                    continue;
                  }
                  // 曜日が取得できなければContinue.
                  var day = translateDay(keys[i]); // 曜日を取得
                  if (day === null) {
                    if ((keys.length - 1) === i) return callback(true, null); // 最終行だった場合はfalse
                    continue;
                  }
                  // 曜日が今日若しくは明日ではない場合はContinue
                  if (day !== nowDay && day !== nextDay) {

                    if ((keys.length - 1) === i) return callback(true, null); // 最終行だった場合はfalse
                    continue;
                  }
                  // 曜日が今日で時間指定なしの場合は即時表示
                  if (day === nowDay && Number(cond.timeSetting) === 2) {
                    return callback(false, 0);
                  }
                  // 時間指定ありで、開始・終了時間が取得できない場合は終了
                  if (!checkTime(cond.startTime) || !checkTime(cond.endTime)) return callback(true, null);
                  var startDate = makeDate(date + cond.startTime);
                  var endDate = makeDate(date + cond.endTime);
                  // 今日で開始中の場合
                  if (day === nowDay && startDate <= dateParse && dateParse < endDate ) {
                    return callback(false, 0); // 即時表示
                  }
                  // 今日で開始前の場合
                  else if (day === nowDay && startDate > dateParse && dateParse < endDate ) {
                    return callback(false, (startDate-dateParse) ); // 開始時間に表示されるようにタイマーセット
                  }
                  // 次回の場合
                  else if ( day === nextDay) {
                    var nextDate = startDate + 24*60*60*1000;
                    return callback(false, (nextDate-dateParse)); // 開始時間に表示されるようにタイマーセット
                  }
                  else {
                    return callback(true, null);
                  }
                }
            },
            referrer: function(cond, callback){
                if (!('keyword' in cond) || !('referrerCond' in cond )) return callback(true, null );
                if ( userInfo.referrer === "" ) return callback(true, null );
                if (sinclo.trigger.common.pregMatch(cond.referrerCond, cond.keyword, userInfo.referrer)) {
                    callback(false, 0);
                }
                else {
                    callback(true, null);
                }
            },
            searchWord: function(cond, callback){
                if (!('keyword' in cond) || !('searchCond' in cond )) return callback( true, null );
                if ( userInfo.searchKeyword === null && Number(cond.searchCond) !== 3 ) return callback( true, null );
                if ( userInfo.searchKeyword === null && Number(cond.searchCond) === 3 ) return callback( false, 0 );
                if (sinclo.trigger.common.pregMatch(cond.searchCond, cond.keyword, userInfo.searchKeyword)) {
                    callback(false, 0);
                }
                else {
                    callback(true, null);
                }
            }
        }
    }
  };

  sincloVideo = {
    open: function(obj){
      window.open(
        "https://ap1.sinclo.jp/index.html?userId=" + userInfo.userId,
        "monitor_" + userInfo.userId,
        "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
      );

      return false;
    }
  };

}(sincloJquery));