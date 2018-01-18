<script type="text/javascript">
'use strict';
  var historySearchConditions = <?php echo json_encode($data);?>;
  var mCustomerInfoList = <?php echo json_encode($mCustomerList);?>;
  var sincloApp = angular.module('sincloApp', ['ngSanitize']);
  sincloApp.controller('MainController', ['$scope', '$timeout', function($scope, $timeout) {
    var userList = <?php echo json_encode($responderList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
    $scope.ua = function(str){
      return userAgentChk.pre(str);
    };

    $(document).ready(function(){
      $scope.messageList = [];
      $.ajax({
        type: "GET",
        url: "<?=$this->Html->url(['controller'=>'ChatHistories', 'action' => 'getOldChat'])?>",
        data: {
          historyId: '<?=$historyId?>'
        },
        dataType: "json",
        success: function(json){
          angular.element("message-list-descript").attr("class", "off");
          $scope.messageList = json;
          $scope.$apply();
          addTooltipEvent();
        }
      });
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
          url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'remoteGetChatList')) ?>",
          cache: false,
          data: {
            userId: $('#visitorsId').text()
          },
          dataType: 'json',
          success: function(json){
            $scope.chatLogList = json;
            angular.element("message-list-descript").attr("class", "on");
            $("#oldChat").css('height', $("#oldChat").css('height') - 80);
            $scope.$apply();
            if(1024 < window.parent.screen.width && window.parent.screen.width < 1367) {
              $("#oldChatList *").css("fontSize", "7px");
            }
            else if(window.parent.screen.width <= 1024) {
              $("#oldChatList *").css("fontSize", "4px");
            }
            else {
              $("#oldChatList *").css("fontSize", "13px");
            }
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

    $scope.setDetailMode = function(mode) {
      $scope.switchDetailMode = mode;
    }

    $scope.judgeShowChatContent = function() {
      return $scope.fillterTypeId === 1 || $scope.switchDetailMode === 1;
    };

    $scope.judgeShowCustomerContent = function() {
      return $scope.fillterTypeId === 1 || $scope.switchDetailMode === 2;
    };

    // 顧客の詳細情報を取得する
    $scope.getOldChat = function(historyId, oldFlg){
      $scope.chatLogMessageList = [];
      $scope.messageList = [];
      $timeout(function(){
        $scope.$apply();
      }).then(function(){
        $.ajax({
          type: "GET",
          url: "<?=$this->Html->url(['controller'=>'ChatHistories', 'action' => 'getOldChat'])?>",
          data: {
            historyId:  historyId
          },
          dataType: "json",
          success: function(json){
            if ( oldFlg ) { // 過去チャットの場合
              angular.element("message-list-descript").attr("class", "off");
              $scope.chatLogMessageList = json;
              $scope.$apply();
              addTooltipEvent();
            }
            else {
              console.time('処理時間10：');
              $scope.messageList = json;

              $scope.chatLogList = [];
              $scope.chatLogMessageList = [];
              //$scope.$apply();
              angular.element("message-list-descript").attr("class", "off");
              console.timeEnd('処理時間10：');
              $.ajax({
                type: 'GET',
                url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'remoteGetChatList')) ?>",
                cache: false,
                data: {
                  userId: $('#visitorsId').text()
                },
                dataType: 'json',
                success: function(json){
                  $scope.chatLogList = json;
                  angular.element("message-list-descript").attr("class", "on");
                  $scope.$apply();
                  addTooltipEvent();
                }
              });
            }
          }
        });
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


    $scope.ip = function(ip, issetCompanyName){
      var showData = [];
      if(issetCompanyName) {
        showData.push('(' + ip + ')'); // IPアドレス
      } else {
        showData.push(ip); // IPアドレス
      }
      return showData.join("\n");
    };

    // 【チャット】テキストの構築
    $scope.createTextOfMessage = function(chat, message, opt) {
      var strings = message.split('\n');
      var custom = "";
      var linkReg = RegExp(/http(s)?:\/\/[!-~.a-z]*/);
      var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
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
          // 電話番号（スマホのみリンク化）
          var tel = str.match(telnoTagReg);
          if( tel !== null ) {
            var telno = tel[1];
            // ただの文字列にする
            var span = "<span class='telno'>" + telno + "</span>";
            str = str.replace(tel[0], span);
          }
          custom += str + "\n";
        }
      return custom;
    };

    // 【チャット】チャット枠の構築
    $scope.createMessage = function(elem, chat){
      var chatApi = {
        connect: false,
        tabId: null,
        sincloSessionId: null,
        userId: null,
        token: null,
        getMessageToken: null,
        messageType: {
          customer: 1,
          company: 2,
          auto: 3,
          sorry: 4,
          autoSpeech: 5,
          sendFile: 6,
          start: 98,
          end: 99,
        }
      }
      var cn = "";
      var div = document.createElement('div');
      var li = document.createElement('li');
      var content = "";
      var type = Number(chat.messageType);
      var message = chat.message;
      var userId = Number(chat.userId);
      var fontSize;
      var timeFontSize;
      var dataBaloon;
      var coreSettings = "<?= $coreSettings[C_COMPANY_USE_HISTORY_DELETE] ?>";
      //横並びの場合
      if(<?= $screenFlg ?> == 1) {
        dataBaloon = 89;
      }
      //縦並びの場合
      if(<?= $screenFlg ?> == 2) {
        dataBaloon = 45;
      }
      if(1024 < window.parent.screen.width && window.parent.screen.width < 1367) {
        fontSize = '7px';
        timeFontSize = '6px';
      }
      else if(window.parent.screen.width <= 1024) {
        fontSize = '4px';
        timeFontSize = '3px';
      }
      else {
        fontSize = '13px';
        timeFontSize = '12px';
      }
      // 消費者からのメッセージの場合
      if ( type === chatApi.messageType.customer) {
        var created = chat.created.replace(" ","%");
        var forDeletionMessage = chat.message.replace(/\r?\n?\s+/g,"");
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        div.style.borderBottom = '1px solid #bfbfbf';
        div.style.marginTop = '6px';
        li.className = cn;
        if(chat.delete_flg == 1) {
          var deleteUser = userList[Number(chat.deleted_user_id)];
          content = "<span class='cName' style = 'color:#bdbdbd !important;font-size:"+fontSize+"'>ゲスト(" + Number($('#visitorsId').text()) + ")</span>";
          content += "<span class='cTime' style = 'color:#bdbdbd !important; font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          content +=  "<span class='cChat' style = 'color:#bdbdbd; font-size:"+fontSize+"'>(このメッセージは"+chat.deleted+"に"+deleteUser+"さんによって削除されました。)</span>";
        }
        else {
          content = "<span class='cName' style = 'color:#333333 !important; font-size:"+fontSize+"'>ゲスト(" + Number($('#visitorsId').text()) + ")</span>";
          content += "<span class='cTime' style = 'font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          if(chat.permissionLevel == 1 && coreSettings == 1) {
            content += '<img src= /img/close_b.png alt=履歴削除 onclick = openChatDeleteDialog('+chat.id+','+chat.t_histories_id+',"'+forDeletionMessage+'","'+created+'") width=21 height=21 style="cursor:pointer; float:right; color: #fff !important; padding:2px !important; margin-right: auto;">'
          }
          else if(chat.permissionLevel == 1 && coreSettings == "") {
            content += '<img src= /img/close_b.png alt=履歴削除 class = \"commontooltip disabled deleteChat\" data-text= \"こちらの機能はスタンダードプラン<br>からご利用いただけます。\" data-balloon-position = \"'+dataBaloon+'\"  width=21 height=21 style="cursor:pointer; float:right; color: #fff !important; padding:2px !important; margin-right: auto;">'
          }
          content +=  "<span class='cChat' style = 'font-size:"+fontSize+"'>"+$scope.createTextOfMessage(chat, message, {radio: false})+"</span>";
        }
      }
      // オートメッセージの場合
      else if ( type === chatApi.messageType.company) {
        var created = chat.created.replace(" ","%");
        var forDeletionMessage = chat.message.replace(/\r?\n?\s+/g,"");
        if(message.indexOf('<') > -1){
          forDeletionMessage = forDeletionMessage.replace(/</g, '&lt;');
        }
        if(message.indexOf('>') > -1) {
          forDeletionMessage = forDeletionMessage.replace(/>/g, '&gt;');
        }
        cn = "sinclo_se";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        div.style.borderBottom = '1px solid #bfbfbf';
        div.style.marginTop = '6px';
       // var chatName = widget.subTitle;
        if ( chat.userId !== null ) {
          var chatName = userList[Number(chat.userId)];
        }
        if(chat.delete_flg == 1) {
          var deleteUser = userList[Number(chat.deleted_user_id)];
          content = "<span class='cName' style = 'color:#bdbdbd !important; font-size:"+fontSize+"'>"+ chatName +"</span>";
          content += "<span class='cTime' style = 'color:#bdbdbd !important; font-size:"+timeFontSize+"''>"+chat.created+"</span>";
          content +=  "<span class='cChat' style = 'color:#bdbdbd; font-size:"+fontSize+"'>(このメッセージは"+chat.deleted+"に"+deleteUser+"さんによって削除されました。)</span>";
        }
        else {
          content = "<span class='cName' style = 'font-size:"+fontSize+"'>" + chatName + "</span>";
          content += "<span class='cTime' style = 'font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          if(chat.permissionLevel == 1 && coreSettings == 1) {
            content += '<img src= /img/close_b.png alt=履歴削除 width=21 height=21 onclick = openChatDeleteDialog('+chat.id+','+chat.t_histories_id+',"'+forDeletionMessage+'","'+created+'") style="cursor:pointer; float:right; color: #C9C9C9 !important; padding:2px !important; margin-right: auto;">'
          }
          else if(chat.permissionLevel == 1 && coreSettings == "") {
            content += '<img src= /img/close_b.png alt=履歴削除 class = \"commontooltip disabled deleteChat\" data-text= \"こちらの機能はスタンダードプラン<br>からご利用いただけます。\" data-balloon-position = \"'+dataBaloon+'\"  width=21 height=21 style="cursor:pointer; float:right; color: #C9C9C9 !important; padding:2px !important; margin-right: auto;">'
          }
          content += "<span class='cChat' style = 'font-size:"+fontSize+"'>"+$scope.createTextOfMessage(chat, message)+"</span>";
        }
      }
      else if ( type === chatApi.messageType.auto || type === chatApi.messageType.sorry) {
        cn = "sinclo_auto";
        var created = chat.created.replace(" ","%");
        var forDeletionMessage = chat.message.replace(/\r?\n?\s+/g,"");
        if(message.indexOf('<') > -1){
          forDeletionMessage = forDeletionMessage.replace(/</g, '&lt;');
        }
        if(message.indexOf('>') > -1) {
          forDeletionMessage = forDeletionMessage.replace(/>/g, '&gt;');
        }
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        div.style.borderBottom = '1px solid #bfbfbf';
        div.style.marginTop = '6px';
        if(chat.delete_flg == 1) {
          var deleteUser = userList[Number(chat.deleted_user_id)];
          content = "<span class='cName' style = 'color:#bdbdbd !important; font-size:"+fontSize+"'>自動応答(" + Number($('#visitorsId').text()) + ")</span>";
          content += "<span class='cTime' style = 'color:#bdbdbd !important;font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          content +=  "<span class='cChat' style = 'color:#bdbdbd; font-size:"+fontSize+"'>(このメッセージは"+chat.deleted+"に"+deleteUser+"さんによって削除されました。)</span>";
        }
        else {
          content = "<span class='cName' style = 'font-size:"+fontSize+"'>自動応答</span>";
          content += "<span class='cTime' style = 'font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          if(chat.permissionLevel == 1 && coreSettings == 1) {
            content += '<img src= /img/close_b.png alt=履歴削除  width=21 height=21 onclick = openChatDeleteDialog('+chat.id+','+chat.t_histories_id+',"'+forDeletionMessage+'","'+created+'") style="cursor:pointer; float:right; color: #C9C9C9 !important; padding:2px !important; margin-right: auto;">';
          }
          else if(chat.permissionLevel == 1 && coreSettings == "") {
            content += '<img src= /img/close_b.png alt=履歴削除  width=21 height=21 class = \"commontooltip disabled deleteChat\" data-text= \"こちらの機能はスタンダードプラン<br>からご利用いただけます。\" data-balloon-position = \"'+dataBaloon+'\" style="cursor:pointer; float:right; color: #C9C9C9 !important; padding:2px !important; margin-right: auto;">';
          }
          content += "<span class='cChat' style = 'font-size:"+fontSize+"'>"+$scope.createTextOfMessage(chat, message)+"</span>";
        }
      }
      else if ( type === chatApi.messageType.autoSpeech ) {
        cn = "sinclo_auto";
        var created = chat.created.replace(" ","%");
        var forDeletionMessage = chat.message.replace(/\r?\n?\s+/g,"");
        if(message.indexOf('<') > -1){
          forDeletionMessage = forDeletionMessage.replace(/</g, '&lt;');
        }
        if(message.indexOf('>') > -1) {
          forDeletionMessage = forDeletionMessage.replace(/>/g, '&gt;');
        }
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        div.style.borderBottom = '1px solid #bfbfbf';
        div.style.marginTop = '6px';
        if(chat.delete_flg == 1) {
          var deleteUser = userList[Number(chat.deleted_user_id)];
          content = "<span class='cName' style = 'color:#bdbdbd !important; font-size:"+fontSize+"'>自動返信(" + Number($('#visitorsId').text()) + ")</span>";
          content += "<span class='cTime' style = 'color:#bdbdbd !important; font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          content +=  "<span class='cChat' style = 'color:#bdbdbd; font-size:"+fontSize+"'>(このメッセージは"+chat.deleted+"に"+deleteUser+"さんによって削除されました。)</span>";
        }
        else {
          content = "<span class='cName' style = 'font-size:"+fontSize+"'>自動返信</span>";
          content += "<span class='cTime' style = 'font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          if(chat.permissionLevel == 1 && coreSettings == 1) {
            content += '<img src= /img/close_b.png alt=履歴削除 width=21 height=21 onclick = openChatDeleteDialog('+chat.id+','+chat.t_histories_id+',"'+forDeletionMessage+'","'+created+'") style="cursor:pointer; float:right; color: #C9C9C9 !important; padding:2px !important; margin-right: auto;">'
          }
          else if(chat.permissionLevel == 1 && coreSettings == "") {
            content += '<img src= /img/close_b.png alt=履歴削除 class = \"commontooltip disabled deleteChat\" data-text= \"こちらの機能はスタンダードプラン<br>からご利用いただけます。\"　data-balloon-position = \"'+dataBaloon+'\"  width=21 height=21 style="cursor:pointer; float:right; color: #C9C9C9 !important; padding:2px !important; margin-right: auto;">'
          }
          content += "<span class='cChat' style = 'font-size:"+fontSize+"'>"+$scope.createTextOfMessage(chat, message)+"</span>";
        }
      } else if ( type === chatApi.messageType.sendFile ) {
        cn = "sinclo_se";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        div.style.borderBottom = '1px solid #bfbfbf';
        div.style.marginTop = '6px';
        var created = chat.created.replace(" ","%");
//        var chatName = widget.subTitle;
//        if ( Number(widget.showName) === <?//=C_WIDGET_SHOW_NAME?>// ) {
//          chatName = userList[Number(userId)];
//        }
        if(chat.delete_flg == 1) {
          var deleteUser = userList[Number(chat.deleted_user_id)];
          content = "<span class='cName' style = 'color:#bdbdbd !important; font-size:"+fontSize+"'>ファイル送信"+ (isExpired ? "（ダウンロード有効期限切れ）" : "") + "</span>";
          content += "<span class='cTime' style = 'color:#bdbdbd !important; font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          content +=  "<span class='cChat' style = 'color:#bdbdbd; font-size:"+fontSize+"'>(このメッセージは"+chat.deleted+"に"+deleteUser+"さんによって削除されました。)</span>";
        }
        else {
          // ファイル送信はmessageがJSONなのでparseする
          message = JSON.parse(message);
          var forDeletionMessage = message.fileName.replace(/\r?\n?\s+/g,"");
          content = "<span class='cName' style = 'font-size:"+fontSize+"'>ファイル送信" + (isExpired ? "（ダウンロード有効期限切れ）" : "") + "</span>";
          content += "<span class='cTime' style = 'font-size:"+timeFontSize+"'>"+chat.created+"</span>";
          if(chat.permissionLevel == 1 && coreSettings == 1) {
            content += '<img src= /img/close_b.png alt=履歴削除 width=21 height=21 onclick = openChatDeleteDialog('+chat.id+','+chat.t_histories_id+',"'+forDeletionMessage+'","'+created+'") style="cursor:pointer; float:right; color: #C9C9C9 !important; padding:2px !important; margin-right: auto;">'
          }
          else if(chat.permissionLevel == 1 && coreSettings == "") {
            content += '<img src= /img/close_b.png alt=履歴削除 class = \"commontooltip disabled deleteChat\" data-text= \"こちらの機能はスタンダードプラン<br>からご利用いただけます。\" data-balloon-position = \"'+dataBaloon+'\"  width=21 height=21 style="cursor:pointer; float:right; color: #C9C9C9 !important; padding:2px !important; margin-right: auto;">'
          }

          var isExpired = Math.floor((new Date()).getTime() / 1000) >=  (Date.parse( message.expired.replace( /-/g, '/') ) / 1000);
          content += $scope.createTextOfSendFile(chat, message.downloadUrl, message.fileName, message.fileSize, message.extension, isExpired);
        }
      } else  {
        cn = "sinclo_etc";
        div.style.borderBottom = '1px solid #bfbfbf';
        div.style.marginTop = '6px';
        var userName = "オペレーター";
        if ( chat.userId !== null ) {
          userName = userList[Number(chat.userId)];
        }
        if ( type === chatApi.messageType.start ) {
          content = "<span style = 'color:#9bbb59 !important; font-size:"+fontSize+"'>－　" + userName + "が入室しました　－</span>";
          content += "<span class='cTime' style = 'font-size:"+timeFontSize+"'>"+chat.created+"</span>";
        }
        if ( type === chatApi.messageType.end ) {
          content = "<span style = 'color:#9bbb59 !important; font-size:"+fontSize+"'>－　" + userName + "が退室しました　－</span>";
          content += "<span class='cTime' style = 'font-size:"+timeFontSize+"'>"+chat.created+"</span>";
        }
      }
      li.className = cn;
      li.innerHTML = content;
      div.appendChild(li);
      $(elem).append(div);
    };

    $scope.createTextOfSendFile = function(chat, url, name, size, extension, isExpired) {
      var thumbnail = "";
      if (extension.match(/(jpeg|jpg|gif|png)$/) != null && !isExpired) {
        thumbnail = "<img src='" + url + "' class='sendFileThumbnail' width='64' height='64'>";
      } else {
        thumbnail = "<i class='fa " + selectFontIconClassFromExtension(extension) + " fa-4x sendFileThumbnail' aria-hidden='true'></i>";
      }

      //var content = "<span class='cName'>ファイル送信" + (isExpired ? "（ダウンロード有効期限切れ）" : "") + "</span>";
      var content    = "<div class='sendFileContent'>";
      content    += "  <div class='sendFileThumbnailArea'>" + thumbnail + "</div>";
      content    += "  <div class='sendFileMetaArea'>";
      content    += "    <span class='data sendFileName'>" + name + "</span>";
      content    += "    <span class='data sendFileSize'>" + formatBytes(size,2) + "</span>";
      content    += "  </div>";
      content    += "</div>";

      return content;
    };

    function selectFontIconClassFromExtension(ext) {
      var selectedClass = "",
        icons = {
          image:      'fa-file-image-o',
          pdf:        'fa-file-pdf-o',
          word:       'fa-file-word-o',
          powerpoint: 'fa-file-powerpoint-o',
          excel:      'fa-file-excel-o',
          audio:      'fa-file-audio-o',
          video:      'fa-file-video-o',
          zip:        'fa-file-zip-o',
          code:       'fa-file-code-o',
          text:       'fa-file-text-o',
          file:       'fa-file-o'
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
      if(extensions[ext]) {
        selectedClass = extensions[ext]
      } else {
        selectedClass = extensions['file'];
      }
      return selectedClass;
    }

    $scope.ui = function(ip, id){
      var showData = [];

      if ( mCustomerInfoList.hasOwnProperty(id) && mCustomerInfoList[id] !== "" && mCustomerInfoList[id] != null && mCustomerInfoList[id] !== undefined ) {
        var c = JSON.parse(mCustomerInfoList[id]);
        if ( ('company' in c) && c.company.length > 0 ) {
          showData.push(c.company); // 会社名
        }
        if ( ('name' in c) && c.name.length > 0 ) {
          showData.push(c.name); // 名前
        }
      }
      return showData.join("\n");
    };

  <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
    angular.element('label[for="g_chat"]').on('change', function(e){
      var url = "<?=$this->Html->url(['controller' => 'ChatHistories', 'action'=>'index'])?>?isChat=" + e.target.checked;
      location.href = url;
    });
  <?php endif; ?>

      /* パラメーターを取り除く */
      var targetParams = <?php echo json_encode(array_flip($excludeList['params']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
      $scope.trimToURL = function (url,type){
        if ( typeof(url) !== 'string' ) return "";
        //表示するURLの場合
        if(type == 2) {
          //メッセージが30文字以上の場合3点リーダー表示
          if(url.length > 30) {
            url = url.substr(0,30)　+ '...';
          }
        };
        return trimToURL(targetParams, url);
      };
  }]);


  sincloApp.directive('ngCreateMessage', [function(){
    return {
      restrict: 'E',
      link: function(scope, elem, attr) {
        scope.createMessage(elem, scope.chat);

      }
    };
  }]);

  sincloApp.directive('ngShowDetail', function(){
    return {
      restrict: 'E',
      scope: {
        visitorId: '='
      },
      template: '<a href="javascript:void(0)" ng-click="showDetail(historyId)" class="detailBtn blueBtn btn-shadow">詳細</a>',
      link: function(scope, elem, attr) {
        scope.historyId = attr['id'];
        scope.showDetail = function(id){
          $.ajax({
            type: 'GET',
            url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'getCustomerInfo')) ?>",
            data: {
              historyId: id
            },
            dataType: 'html',
            success: function(html){
              modalOpen.call(window, html, 'p-history-cus', '顧客情報');
            }
          });
        };
      }
    }
  });

(function(){

  window.openHistoryById = function(id){
    $.ajax({
      type: 'GET',
      url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'remoteGetStayLogs')) ?>",
      data: {
        historyId: id
      },
      dataType: 'html',
      success: function(html){
        modalOpen.call(window, html, 'p-history-logs', 'ページ移動履歴');
      }
    });
  };

  <?php if(isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]): ?>
  window.openCompanyDetailInfo = function(lbc){
    var retList = {};
    $.ajax({
      type: 'POST',
      cache: false,
      url: "<?= $this->Html->url(array('controller' => 'CompanyData', 'action' => 'getDetailInfo')) ?>",
      data: JSON.stringify({
        accessToken: "<?=$token?>",
        lbc: lbc,
        format: 'popupElement'
      }),
      dataType: 'html',
      success: function(html){
        modalOpen.call(window, html, 'p-cus-company-detail', '企業詳細情報');
      }
    });
  };
  <?php endif; ?>

  <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
    window.openChatById = function(id){
      $.ajax({
        type: 'GET',
        url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'getChatLogs')) ?>",
        cache: false,
        data: {
          historyId: id
        },
        dataType: 'html',
        success: function(html){
        }
      });
    };
  <?php endif; ?>

}());

$(document).ready(function(){

  // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70) + 'px',
      left: $(this).offset().left - 65 + 'px'
    });
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });

  var outputCSVBtn = document.getElementById('outputCSV');
  outputCSVBtn.addEventListener('click', function(){
    console.log('CSV原因探索1');
    if($(outputCSVBtn).hasClass('disabled')) return false;
    var thead = document.querySelector('#list_body thead');
    var tbody = document.querySelector('#list_body tbody');
    var data = [];
    // CSVに不要な列が追加されたら空をセット
<?php if ($coreSettings[C_COMPANY_USE_CHAT]) { ?>
    var label = ["type","date","","ip","useragent","campaign","referrer","pageCnt","visitTime","achievement","status", "user"];
<?php } else { ?>
    var label = ["date","","ip","useragent","campaign","referrer","pageCnt","visitTime"];
<?php } ?>

    var noCsvData = {};

    for (var a = 0; a < thead.children[0].children.length; a++) {
      var th = thead.children[0].children[a];
      if ( th.className.match(/noOutCsv/) !== null ) {
        noCsvData[a] = "";
      }
    }
    console.log('CSV原因探索2');
    for(var i = 0; i < tbody.children.length; i++){
      var tr = tbody.children[i];
      var tdList = tr.children;
      var row = {};
      for(var u = 0; u < tdList.length; u++){
        if (!(u in noCsvData)) {
                  var td = tdList[u];
                  if ( td.children.length === 0 ) {
                    row[label[u]] = td.textContent;
                  }
                  else {
                    row[label[u]] = td.children[0].textContent;
                  }
                  if ( u === (label.length - 1) ) {
                    data.push(row);
                  }

        }
      }
    }
    document.getElementById('HistoryOutputData').value = JSON.stringify(data);
    document.getElementById('HistoryIndexForm').action = '<?=$this->Html->url(["controller"=>"ChatHistories", "action" => "outputCSVOfHistory"])?>';
    console.log('CSV原因探索2');
    document.getElementById('HistoryIndexForm').submit();
  });

<?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>

  var outputChatCSVBtn = document.getElementById('outputChat');
  outputChatCSVBtn.addEventListener('click', function(){
    if($(outputChatCSVBtn).hasClass('disabled')) return false;
    var thead = document.querySelector('#list_body thead');
    var tbody = document.querySelector('#list_body tbody');
    var data = [];
    // CSVに不要な列が追加されたら空をセット
    var label = ["date","","ip","useragent","campaign","sourcePage","pageCnt","visitTime","status","","user"];
    var noCsvData = {};

    for (var a = 0; a < thead.children[0].children.length; a++) {
      var th = thead.children[0].children[a];
      if ( th.className.match(/noOutCsv/) !== null ) {
        noCsvData[a] = "";
      }
    }

    for(var i = 0; i < tbody.children.length; i++){
      var tr = tbody.children[i];
      var tdList = tr.children;
      var row = {};
      for(var u = 0; u < tdList.length; u++){
        var td = tdList[u];
        if (!(u in noCsvData)) {
          if ( td.children.length === 0 ) {
            row[label[u]] = td.textContent;
          }
          else {
            row[label[u]] = td.children[0].textContent;
          }
          if ( u === (label.length - 1) ) {
            data.push(row);
          }
        }
        else {
          var id = $(td.children[0]).data('id');
          if ( id !== null && id !== undefined ) {
            row['id'] = id;
          }
        }
      }
    }
    document.getElementById('HistoryOutputData').value = JSON.stringify(data);
    document.getElementById('HistoryIndexForm').action = '<?=$this->Html->url(["controller"=>"ChatHistories", "action" => "outputCSVOfChatHistory"])?>';
    document.getElementById('HistoryIndexForm').submit();
  });

<?php endif; ?>
  $('#mainDatePeriod').daterangepicker({
    "ranges": {
      '今日': [moment(), moment()],
      '昨日': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      '過去一週間': [moment().subtract(6, 'days'), moment()],
      '過去一ヶ月間': [moment().subtract(30, 'days'), moment()],
      '今月': [moment().startOf('month'), moment().endOf('month')],
      '先月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
      '全期間': [historySearchConditions.History.company_start_day, moment()]
    },
    "locale": {
      "format": "YYYY/MM/DD",
      "separator": " - ",
      "applyLabel": "検索",
      "cancelLabel": "キャンセル",
      "fromLabel": "From",
      "toLabel": "To",
      "customRangeLabel": "カスタム",
      "weekLabel": "W",
      "daysOfWeek": [
        "日",
        "月",
        "火",
        "水",
        "木",
        "金",
        "土"
      ],
      "monthNames": [
        "1月",
        "2月",
        "3月",
        "4月",
        "5月",
        "6月",
        "7月",
        "8月",
        "9月",
        "10月",
        "11月",
        "12月"
      ],
      "firstDay": 1
    },
    "alwaysShowCalendars": true,
    "startDate": historySearchConditions.History.start_day,
    "endDate": historySearchConditions.History.finish_day,
    "opens": "left"
  });

  //キャンセルボタン
  $('.cancelBtn').on('click', function() {
    $('#mainDatePeriod').html(historySearchConditions.History.period + ' : ' + historySearchConditions.History.start_day + '-' + historySearchConditions.History.finish_day);
  });

  //検索期間欄をクリックした場合
  $('#mainDatePeriod').on('click', function() {
    $('#mainDatePeriod').html(historySearchConditions.History.period + ' : ' + historySearchConditions.History.start_day + '-' + historySearchConditions.History.finish_day);
  });

  var number = 1;
  var prevBoldTarget = null;
  var numberLines;
  $('.showBold').on('click', function(e){
    var getTopPosition  = $(".dataTables_scrollBody").scrollTop();
    $('.showBold').each(function(index){
      if((location.search.split("?")[1]) !== undefined && location.search.split("?")[1].match(/id/)) {
        if ((location.search.split("?")[1]).substr(3) == $(this)[0]['id']) {
          $(this).find('td').each(function(index){
            if(index < 12) {
              $(this).css("background-color", "#fff");
              $(this).css("font-weight", "normal");
            }
          });
        }
      }
    });
    if(prevBoldTarget != null) {
      prevBoldTarget.find('td').each(function(index){
        if(index < 12) {
          $(this).css("background-color", "#fff");
          $(this).css("font-weight", "normal");
        }
      });
    }
    $(this).find('td').each(function(index){
      if(index < 12) {
        $(this).css("background-color", "#ebf6f9");
        $(this).css("font-weight", "bold");
      }
    });
    prevBoldTarget = $(this);
  });

  $('.showBold').each(function(index){
    if((location.search.split("?")[1]) !== undefined && location.search.split("?")[1].match(/id/)) {
      if ((location.search.split("?")[1]).substr(3) == $(this)[0]['id']) {
        $(this).find('td').each(function(index){
          if(index < 12) {
            console.time('処理時間4：');
            if(index == 0) {
              prevBoldTarget = $(this).parent('tr');
            }
            $(this).css("background-color", "#ebf6f9");
            $(this).css("font-weight", "bold");
          }
        });
      }
    }
    else {
      if(index == 0) {
        $('.showBold').find('td').each(function(index2){
          if(index2 < 12) {
            if(index2 == 0) {
              prevBoldTarget = $(this).parent('tr');
            }
            $(this).css("background-color", "#ebf6f9");
            $(this).css("font-weight", "bold");
          }
          else {
            return false;
          }
        });
      }
      else {
        return false;
      }
    }
  });

  var id;
  var scrollHeight = 1;
  var focusHeigt;
  $(window).on('keydown', function(e) {
    var check = parseInt($(".dataTables_scrollBody").css('height'));
    if(e.keyCode === 40) { // ↓
      e.preventDefault();
      number = number + 1;
      if(prevBoldTarget.next("tr")[0] != null) {
        prevBoldTarget.find('td').each(function(index){
          if(index < 12) {
            $(this).css("background-color", "#fff");
            $(this).css("font-weight", "normal");
          }
        });
        prevBoldTarget.next("tr").find('td').each(function(index){
          if(index < 12) {
            if(index == 0) {
              id = $(this).parent('tr')[0]['id'];
              prevBoldTarget = $(this).parent('tr');
              focusHeigt = $(this).offset().top;
            }
            $(this).css("background-color", "#ebf6f9");
            $(this).css("font-weight", "bold");
          }
        });
      }

      //チャット情報取得
      var element = document.getElementById("chat_history_idx");
      // jQueryかjqLiteが有効な場合はセレクタを使える
      // var $scope = angular.element('#myElement').scope()
      var $scope = angular.element(element).scope();
      $scope.getOldChat(id, false);
      //ユーザー情報取得
      openChatById(id);

      var row = chatTable.rows.length;
      if(prevBoldTarget.next("tr")[0] != null && parseInt($(".dataTables_scrollBody").css('height')) - (focusHeigt - ($('.dataTables_scroll').offset()['top']+49) + prevBoldTarget.next("tr")[0]['clientHeight']) < 0 ) {
          $(".dataTables_scrollBody").scrollTop($(".dataTables_scrollBody").scrollTop()+prevBoldTarget.next("tr")[0]['clientHeight']);
          if(scrollHeight < (row-number)) {
            scrollHeight = scrollHeight + 1;
          }
          number = number -1;
      }
      else if(prevBoldTarget.next("tr")[0] == null) {
        $(".dataTables_scrollBody").scrollTop($(".dataTables_scrollBody").scrollTop()+prevBoldTarget[0]['clientHeight']);
      }
    }
    if(e.keyCode === 38) { // ↑キーを押したら
      e.preventDefault();
      if(number > 0) {
        number = number - 1;
      }
      if(prevBoldTarget.prev("tr")[0] != null) {
        prevBoldTarget.find('td').each(function(index){
          if(index < 12) {
            if(index == 0) {
              focusHeigt = $(this).offset().top;
            }
            $(this).css("background-color", "#fff");
            $(this).css("font-weight", "normal");
          }
        });
        prevBoldTarget.prev("tr").find('td').each(function(index){
          if(index < 12) {
            if(index == 0) {
              id = $(this).parent('tr')[0]['id'];
              prevBoldTarget = $(this).parent('tr');
            }
            $(this).css("background-color", "#ebf6f9");
            $(this).css("font-weight", "bold");
          }
        });
      }

      //ユーザー情報取得
      openChatById(id);
      //チャット情報取得
      var element = document.getElementById("chat_history_idx");
      // jQueryかjqLiteが有効な場合はセレクタを使える
      // var $scope = angular.element('#myElement').scope()
      var $scope = angular.element(element).scope();
      $scope.getOldChat(id, false);
      if( prevBoldTarget.prev("tr")[0] != null && focusHeigt-($('.dataTables_scroll').offset()['top']+49)-prevBoldTarget.prev("tr")[0]['clientHeight'] < 0) {
        $(".dataTables_scrollBody").scrollTop($(".dataTables_scrollBody").scrollTop()-prevBoldTarget.prev("tr")[0]['clientHeight']);
      }
      else if(prevBoldTarget.prev("tr")[0] == null) {
        $(".dataTables_scrollBody").scrollTop(0);
      }
    }
  });


  //検索ボタン
  $('#mainDatePeriod').on('apply.daterangepicker', function(ev, picker) {
    var search_day  = $('.active').val();
    //開始日
    var startDay =  $("input[name=daterangepicker_start]").val();
    //終了日
    var endDay = $("input[name=daterangepicker_end]").val();
    //今日
    var today = moment();
    today = today.format("YYYY/MM/DD");
    //昨日
    var yesterday = moment().subtract(1, 'days');
    yesterday = yesterday.format("YYYY/MM/DD");
    //過去一週間
    var oneWeekAgo = moment().subtract(6, 'days');
    oneWeekAgo = oneWeekAgo.format("YYYY/MM/DD");
    //過去一か月間
    var oneMonthAgo = moment().subtract(30, 'days');
    oneMonthAgo = oneMonthAgo.format("YYYY/MM/DD");
    //過去一ヵ月間
    var thisMonth = moment().startOf('month');
    thisMonth = thisMonth.format("YYYY/MM/DD");
    //今月の初め
    var thisMonthStart = moment().startOf('month');
    thisMonthStart = thisMonthStart.format("YYYY/MM/DD");
    //今月の終わり
    var thisMonthEnd = moment().endOf('month');
    thisMonthEnd = thisMonthEnd.format("YYYY/MM/DD");
    //先月の初め
    var lastMonthStart = moment().subtract(1, 'month').startOf('month');
    lastMonthStart = lastMonthStart.format("YYYY/MM/DD");
    //先月の終わり
    var lastMonthEnd = moment().subtract(1, 'month').endOf('month');
    lastMonthEnd = lastMonthEnd.format("YYYY/MM/DD");
    //全期間
    var allDay = historySearchConditions.History.company_start_day;

    //今日
    if(startDay  == today && endDay == today){
       search_day  = "今日";
     }
     //昨日
     else if(startDay  == yesterday && endDay == yesterday){
       search_day  = "昨日";
     }
     //過去一週間
     else if(startDay  == oneWeekAgo && endDay == today){
       search_day  = "過去一週間";
     }
     //過去一か月間
     else if(startDay  == oneMonthAgo && endDay == today){
       search_day  = "過去一ヵ月間";
     }
     //今月
     else if(startDay  == thisMonthStart && endDay == thisMonthEnd){
       search_day  = "今月";
     }
     //先月
     else if(startDay  == lastMonthStart && endDay == lastMonthEnd ){
       search_day  = "先月";
     }
     //全期間
     else if(startDay  == allDay && endDay == today){
       search_day  = "全期間";
     }
     //カスタム
     else {
       search_day  = "カスタム";
     }
    historySearchConditions.History.start_day = $("input[name=daterangepicker_start]").val();
    historySearchConditions.History.finish_day = $("input[name=daterangepicker_end]").val();
    historySearchConditions.History.period = search_day;

    $.ajax({
      type: 'post',
      dataType: 'html',
      data:historySearchConditions,
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'ChatHistories', 'action' => 'index']) ?>",
      success: function(html){
        location.href ="<?= $this->Html->url(['controller' => 'ChatHistories', 'action' => 'index']) ?>";
      }
    });
  });
});

</script>
