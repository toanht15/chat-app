<script type="text/javascript">
  <!--
  'use strict';
  var socket, userId, tabId, iframe, windowSize, windowClose, connectToken, url, emit, resizeApi, iframeLocation, arg = new Object;

  (function(){
    // -----------------------------------------------------------------------------
    //  関数
    // -----------------------------------------------------------------------------

    arg = <?php echo json_encode($query, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
    emit = function(ev, d){
      var obj = {};
      if ( typeof(d) !== "object" ) {
        obj = JSON.parse(d);
      }
      else {
        obj = d;
      }
      obj.siteKey = "<?=$siteKey?>";
      var data = JSON.stringify(obj);
      socket.emit(ev, data);
    };

    windowClose = function(){
      emit('requestSyncStop', {type: 1, tabId: tabId, connectToken: arg.connectToken});
      AssistAgentSDK.endSupport();
      window.close();
      return false;
    };

// TODO 消費者と画面サイズを合わせるためのコードと、
// 企業側がリサイズ行為を行った際に修正するためのコードの
// 切り分けを考える
    resizeApi = {
      timer: null,
      adResizeChk: function(e){ // 管理者が行ったリサイズ
        resizeApi.resizeBy();
      },
      resizeBy: function(){
        var w = windowSize.width - window.outerWidth;
        var h = windowSize.height - window.outerHeight;
        if ( this.timer ) {
          clearTimeout(this.timer);
        }
        if ( w === 0 && h === 0 ) return false;
        this.timer = setTimeout(function(){
          window.resizeBy(Number(w), Number(h));
        },300);
      },
      cuResize: function(wsInfo){
        // 現在のウィンドウサイズを保存しておく
        sessionStorage.setItem('window', JSON.stringify({
          'width':Number(wsInfo.width),
          'height':Number(wsInfo.height)
        }));
        resizeApi.change();
      },
      toolsWidth: 100,
      frameSize: {
        width: window.outerWidth - window.innerWidth + 100,
        height: window.outerHeight - window.innerHeight
      },
      change: function () {
        var wsInfo = JSON.parse(sessionStorage.getItem('window'));

        // ウィンドウ枠幅
        if ( !('width' in this.frameSize) || !('height' in this.frameSize)  ) {
          this.frameSize = {
            width: window.outerWidth - window.innerWidth + this.toolsWidth,
            height: window.outerHeight - window.innerHeight
          };
        }

        var cal = 1; // 縮尺
        var frame = {width:null, height:null}; // iframeサイズ
        var comScreen = {width:(screen.availWidth - this.frameSize.width), height:(screen.availHeight - this.frameSize.height)};
        var ratio = {
          w: wsInfo.width / comScreen.width,
          h: wsInfo.height / comScreen.height
        };
        if ( ratio.w > 1 || ratio.h > 1 ) {
          if (ratio.w > ratio.h) {
            cal = Math.ceil((comScreen.width / wsInfo.width)*100)/100;
          }
          else {
            cal = Math.ceil((comScreen.height / wsInfo.height)*100)/100;
          }
          frame.height = wsInfo.height * cal;
          frame.width = wsInfo.width * cal;
        }
        else {
          frame = wsInfo;
        }

        iframe.width = wsInfo.width;
        iframe.height = wsInfo.height;
        iframe.style.transform = "scale(" + cal + ")";

        var wswidth = frame.width + this.frameSize.width;
        var wsheight = frame.height + this.frameSize.height;

        var winY = window.screenY, winX = window.screenX;
        if ((screen.availHeight-window.screenY - wsheight) < 0) {
          winY = screen.availHeight - wsheight;
        }
        if ((screen.availWidth-window.screenX - wswidth) < 0) {
          winX = screen.availWidth - wswidth;
        }

        try {
          windowSize = {'width': wswidth, 'height': wsheight};
          window.moveTo(winX, winY);
          window.resizeTo(wswidth, wsheight);
        }
        catch(e) {
          console.log("error resize.", e);
        }
      }
    };

    window.focus();

    iframeLocation = {
      sessionName: 'location',
      list: [],
      position: 0,
      status: null,
      forward: function(){
        if ( this.position < (this.list.length - 1) ) {
          this.status = "forward";
          this.position++;
          iframe.src = iframeLocation.list[this.position];
          this.send(this.status, this.position);
        }
      },
      back: function(){
        if ( this.position > 0 ) {
          this.status = "back";
          this.position--;
          iframe.src = iframeLocation.list[this.position];
          this.send(this.status, this.position);
        }
      },
      send:  function(s, p){
        emit('syncLocationOfFrame', {
          tabId: tabId,
          status: s,
          position: p
        });
        this.setBtnColor();
      },
      syncLocationOfFrame: function(d){
        var obj = JSON.parse(d);
        this.status = obj.status;
        this.position = obj.position;
        this.setBtnColor();
      },
      setBtnColor: function(){
        if ( this.position === 0 ) {
          $("#prevBtn:not(.unlight)").addClass('unlight');
        }
        else {
          $("#prevBtn.unlight").removeClass('unlight');
        }

        if ( this.position < (this.list.length - 1) ) {
          $("#nextBtn.unlight").removeClass('unlight');
        }
        else {
          $("#nextBtn:not(.unlight)").addClass('unlight');
        }
      },
      get: function(){
        var location = JSON.parse(sessionStorage.getItem(this.sessionName));
        this.status = location.status;
        this.list = location.list;
        this.position = location.position;
        if ( !this.list[this.position] ) {
          this.position = ( this.list.length > 0 ) ? this.list.length - 1 : 0;
        }
        this.setBtnColor();
      },
      save: function(){
        sessionStorage.setItem(this.sessionName, JSON.stringify({
          status: iframeLocation.status,
          list: iframeLocation.list,
          position: iframeLocation.position
        }));
      }
    };

  })();

  window.onload = function(){

    // スクロール禁止
    $(window).scroll(function(e) {
      $(this).scrollTop(0);
      $(this).scrollLeft(0);
    });

    // WebSocketサーバに接続
    socket = io.connect("<?=C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT?>");
    var first = true;


    // WebSocketサーバ接続イベント
    socket.on('connect', function(){
      userId = arg.userId;
      tabId = arg.id;

      emit('connectFrame', {
        tabId: tabId,
        connectToken: arg.connectToken,
        responderId: "<?= $muserId?>"
      });
    });

    socket.on('retTabInfo', function(d){
      var obj = JSON.parse(d);
      // 別の作業をしている場合
      if ( Number(obj.status) === <?=C_WIDGET_TAB_STATUS_CODE_DISABLE?> ) {
        document.getElementById('tabStatusMessage').style.display = "block";
      }
      // タブがアクティブの場合
      else {
        document.getElementById('tabStatusMessage').style.display = "none";
      }
    });

    socket.on('resUrlChecker', function(d){
      var obj = JSON.parse(d);
      setTimeout(function(){
        // 戻る & 進む以外でのアクションの場合
        if ( iframeLocation.status !== 'back' && iframeLocation.status !== 'forward') {
          // Positionが移動履歴とかみ合わない場合、上書きする
          if ( ((iframeLocation.list.length - 1) !== iframeLocation.position) ) {
            iframeLocation.list = iframeLocation.list.splice(0, iframeLocation.position + 1);
          }
          // Positionが移動履歴と一致しない場合、書き込む
          if ( iframeLocation.list[iframeLocation.list.length - 1] !== obj.url ) {
            iframeLocation.list.push(obj.url);
          }
          iframeLocation.position = iframeLocation.list.length - 1;
          iframeLocation.setBtnColor();
        }

        iframeLocation.status = null;
        iframeLocation.save();
      }, 500);
    });

    socket.on('syncLocationOfFrame', function(d){
      iframeLocation.syncLocationOfFrame(d);
    });

    socket.on('syncResponce', function(data){
      var obj = JSON.parse(data);
      resizeApi.cuResize(obj.windowSize);
    });

    socket.on('syncEvStart', function(){
      if ( document.getElementById('loadingImg') ) {
        loadingImg.parentNode.removeChild(loadingImg);
      }
    });

    // ページ移動が行われるタイミング
    socket.on('syncStart', function(d){
      var obj = JSON.parse(d), str, re, array;

      // 現在のURLを保存しておく
      str = obj.url;
      re = new RegExp("[?|&]{1}\sincloData=", "g");
      array = re.exec(str);

      var saveUrl = obj.url;
      if ( (array !== null) && ('index' in array) ) {
        saveUrl = str.substring(0, array.index);
      }
      sessionStorage.setItem('url', saveUrl);

      var accessList = sessionStorage.getItem('accessList');
    });

    socket.on('syncStop', function(d){
      var obj = JSON.parse(d);
      if ('message' in obj) {
        // ポップアップが表示されていれば、続行しない
        if ( $("#popup").is(".popup-on") ) {
          return false;
        }
        modalOpen.call(window, obj.message, 'p-alert', 'メッセージ');
        popupEvent.closeNoPopup = function(){
          popupEvent.close();
          window.open('about:blank', '_self').close();
          window.close();
        };
      }
      else {
        window.open('about:blank', '_self').close();
        window.close();
      }
    });

    socket.on('unsetUser', function(d){
      var obj = JSON.parse(d);
      if ( obj.tabId !== tabId ) return false;
      modalOpen.call(window, '切断を検知しました。再接続をしますか？', 'p-confirm', 'メッセージ');
      popupEvent.closePopup = function(){
        emit('syncReconnectConfirm', {to: tabId});
        popupEvent.close();
      };
      popupEvent.closeNoPopup = function(){
        popupEvent.close();
        window.open('about:blank', '_self').close();
        window.close();
      };
    });
  };
  // -->
</script>

<section ng-app="sincloApp" ng-controller="MainController">

  <ul id="sync_tools">
    <li id="controlBtn" class="unlight">
      <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_remote.png" width="40" height="40" alt=""></span>
      <p>遠隔操作</p>
    </li>
    <li id="penBtn">
      <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_pen.png" width="40" height="40" alt=""></span>
      <p>ペン</p>
    </li>
    <li id="pointBtn">
      <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_point.png" width="40" height="40" alt=""></span>
      <p>ポイント</p>
    </li>
    <li onclick="location.reload(true); return false;">
      <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_reconnect.png" width="40" height="40" alt=""></span>
      <p>再接続</p>
    </li>
    <?php if(isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]): ?>
      <li ng-click="openDocumentList()">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_document.png" width="40" height="40" alt=""></span>
        <p>資料共有</p>
      </li>
    <?php endif; ?>
    <li onclick="windowClose()">
      <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_disconnect.png" width="40" height="40" alt=""></span>
      <p>終了</p>
    </li>
  </ul>
  <div id="customer_flame">
    <div id="remoteScreenViewContainer">
      <div id="remoteScreenView"></div>
    </div>
    <div id="formContainer" style="display: none;"></div>
  </div>
  <div id="tabStatusMessage">別の作業をしています</div>
  <?php echo $this->element('Customers/laDocumentLists') ?>
  <div id="ang-popup">
    <div id="ang-base">
      <div id="ang-popup-background"></div>
      <div id="ang-popup-frame">
        <div id="ang-popup-content" class="document_list">
          <div id="title_area">資料一覧</div>
          <div id="search_area">
            <?=$this->Form->input('name', ['label' => 'フィルター：', 'ng-model' => 'searchName']);?>
            <!-- <ng-multi-selector></ng-multi-selector> -->
          </div>
          <div id="list_area">
            <ol>
              <li ng-repeat="document in searchFunc(documentList)" ng-click="shareDocument(document)">
                <div class="document_image">
                  <img ng-src="{{::document.thumnail}}" ng-class="setDocThumnailStyle(document)">
                </div>
                <div class="document_content">
                  <h3>{{::document.name}}</h3>
                  <ng-over-view docid="{{::document.id}}" text="{{::document.overview}}" ></ng-over-view>
                  <ul><li ng-repeat="tagId in document.tags">{{::tagList[tagId]}}</li></ul>
                </div>
              </li>
            </ol>
          </div>
          <div id="btn_area">
            <a class="btn-shadow greenBtn" ng-click="closeDocumentList()" href="javascript:void(0)">閉じる</a>
          </div>
        </div>
      </div>
      <div id="ang-ballons">
      </div>
    </div>
  </div>
</section>
<?php
  echo $this->Html->script("https://sdk005.live-assist.jp/assistserver/sdk/web/shared/js/thirdparty/i18next-1.7.4.min.js");
  echo $this->Html->script("https://sdk005.live-assist.jp/gateway/adapter.js");
  echo $this->Html->script("https://sdk005.live-assist.jp/gateway/csdk-phone.js");
  echo $this->Html->script("https://sdk005.live-assist.jp/gateway/csdk-common.js");
  echo $this->Html->script("https://sdk005.live-assist.jp/assistserver/sdk/web/shared/js/assist-aed.js");
  echo $this->Html->script("https://sdk005.live-assist.jp/assistserver/sdk/web/shared/js/document-packet.js");
  echo $this->Html->script("https://sdk005.live-assist.jp/assistserver/sdk/web/shared/js/shared-windows.js");
  echo $this->Html->script("https://sdk005.live-assist.jp/assistserver/sdk/web/agent/js/assist-console.js");
  echo $this->Html->script("liveassist/assist-console-callmanager.js");
?>
<script type="text/javascript">
  <!--
  'use strict';
  $(function() {
    var val = getUrlVars();

    /**
     * URL解析して、クエリ文字列を返す
     * @returns {Array} クエリ文字列
     */
    function getUrlVars()
    {
      var vars = [], max = 0, hash = "", array = "";
      var url = window.location.search;

      //?を取り除くため、1から始める。複数のクエリ文字列に対応するため、&で区切る
      hash  = url.slice(1).split('&');
      max = hash.length;
      for (var i = 0; i < max; i++) {
        array = hash[i].split('=');    //keyと値に分割。
        vars.push(array[0]);    //末尾にクエリ文字列のkeyを挿入。
        vars[array[0]] = array[1];    //先ほど確保したkeyに、値を代入。
      }

      return vars;
    }

    var StorageUtil = {
      key:{
        storageKey:  "laSession",
        sessionId: "sessionId",
        correlationId: "correlationId",
        shortcode: "shortcode"
      },

      _getAll: function() {
        var json = localStorage.getItem(this.key);
        return json ? JSON.parse(json) : {};
      },

      _save: function(obj) {
        localStorage.setItem(this.key, JSON.stringify(obj));
      },

      _get: function(key) {
        var obj = this._getAll();
        return obj[key];
      },

      _set: function(key, val) {
        var obj = this._getAll();
        obj[key] = val;
        this._save(obj);
      },

      setSessionId: function(sessionId) {
        this._set(this.key.sessionId,sessionId);
      },

      setCorrelationId: function(cid) {
        this._set(this.key.correlationId, cid);
      },

      setShortcode: function(shortcode) {
        return this._set(this.key.shortcode, shortcode);
      },

      getSessionId: function() {
        return this._get(this.key.sessionId);
      },

      getCorrelationId: function() {
        return this._get(this.key.correlationId);
      },

      getShortcode: function() {
        return this._get(this.key.shortcode);
      }
    };



    // UI設定
    var remoteView = document.getElementById("remoteScreenView");
    var remoteViewContainer = document.getElementById("remoteScreenViewContainer");
    var formContainer = document.getElementById("formContainer");

    var assistServerSession = {};
    var config = {autoanswer : 'true', agentName: 'Bob' };

    function init() {
      setUI();
      initializeConfiguration();
      setConfiguration();
      setAssistAgentCallbacks();
      getAgentSessionInfo().then(function(){console.log('getSessionID OK');});
    }

    function setUI() {
      AssistAgentSDK.setRemoteView(remoteView);
    }

    function initializeConfiguration() {
      config.autoanswer = 'true';
      config.agentName = 'Bob';
      config.username = 'agent';
      config.password = 'password';
      config.url = "https://sdk005.live-assist.jp";
      config.additionalAttribute = {
        "AED2.metadata": {
          "role": "agent",
          "permissions": {
            "viewable": ["banking", "claims", "default"],
            "interactive": ["claims", "default"]
          },
          "agent-data-1": "agent-value-1",
          "agent-data-2": "agent-value-2"
        }
      };
    }

    function setConfiguration() {
      AssistAED.setConfig(config);
    }

    function setAssistAgentCallbacks() {
      AssistAgentSDK.setFormCallBack(function(formElement) {
        if (formElement) {
          formContainer.appendChild(formElement);
        }
      });

      AssistAgentSDK.setScreenShareActiveCallback(function() {
        debug("setScreenShareActive");
      });

      AssistAgentSDK.setRemoteViewCallBack(function (x, y) {
        console.log("AssistAgentSDK.setRemoteViewCallBack("+x+","+y+")");
        var containerHeight = remoteViewContainer.offsetHeight;
        var containerWidth = remoteViewContainer.offsetWidth;
        var containerAspect = containerHeight / containerWidth;
        var remoteAspect = y / x;

        var height;
        var width;

        if (containerHeight == 0 || containerWidth == 0)
        {
          return;
        }
//        if (containerAspect < remoteAspect) {
//          // Container aspect is taller than the remote view aspect
//          height = Math.min(y, containerHeight);
//          width = height * (x / y);
//        } else {
//          // Container aspect is wider than (or the same as) the remote view aspect
//          width = Math.min(x, containerWidth);
//          height = width * (y / x);
//        }
//        remoteView.style.height = height + "px";
//        remoteView.style.width = width + "px";
        remoteView.style.height = y + "px";
        remoteView.style.width = x + "px";
        window.resizeTo(x+100,y+50);
      });
    }

    function getCorrelationId (shortcode) {
      var d = new $.Deferred();
      var request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState == 4) {
          if (request.status == 200) {
            var cid = JSON.parse(request.responseText).cid;
            StorageUtil.setCorrelationId(cid);
            d.resolve(cid);
          } else {
            d.reject(request.status);
          }
        }
      };
      request.open("GET", "https://sdk005.live-assist.jp/assistserver/shortcode/agent?appkey=" + shortcode, true);
      request.send();
      return d.promise();
    }

    function getAgentSessionInfo() {
      var d = new $.Deferred();
      var request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState == 4) {
          if (request.status == 200) {
            assistServerSession = JSON.parse(request.responseText);
            StorageUtil.setSessionId(assistServerSession.token);
            d.resolve();
          } else {
            d.reject();
          }
        }
      };
      request.open("POST", "https://sdk005.live-assist.jp/assistserver/agent", true);
      request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      request.send("username=" + config.username + "&password="
        + config.password + "&type=create&targetServer=" + "aHR0cHM6Ly9zZGswMDUubGl2ZS1hc3Npc3QuanA6NDQz" //FIXME
        + "&name=" + config.agentName + "&text=" + config.agentText);
      return d.promise();
    }

    $('#startScreenShare').on('click', function (){
      AssistAgentSDK.requestScreenShare();
    });

    $('#controlBtn').on('click', function(){
      try {
        AssistAgentSDK.controlSelected();
        $(this).addClass('unlight');
        $('#penBtn').removeClass('unlight');
        $('#pointBtn').removeClass('unlight');
      } catch (e) {
        if (e instanceof AssistAgentSDK.OffAssistPagesException) {
          alert('Page not Live Assist enabled');
        }
      }
    });

    $('#penBtn').on('click', function(){
      try {
        AssistAgentSDK.drawSelected();
        $(this).addClass('unlight');
        $('#controlBtn').removeClass('unlight');
        $('#pointBtn').removeClass('unlight');
      } catch (e) {
        if (e instanceof AssistAgentSDK.OffAssistPagesException) {
          alert('Page not Live Assist enabled');
        }
      }
    });

    $('#pointBtn').on('click', function(){
      try {
        AssistAgentSDK.spotlightSelected();
        $(this).addClass('unlight');
        $('#controlBtn').removeClass('unlight');
        $('#penBtn').removeClass('unlight');
      } catch (e) {
        if (e instanceof AssistAgentSDK.OffAssistPagesException) {
          alert('Page not Live Assist enabled');
        }
      }
    });

    // マウスホイール拡張
    var mousewheelevent = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';
    $(remoteView).on(mousewheelevent,function(e){
      var num = parseInt($('.wheel').text());
      e.preventDefault();
      var delta = e.originalEvent.deltaY ? -(e.originalEvent.deltaY) : e.originalEvent.wheelDelta ? e.originalEvent.wheelDelta : -(e.originalEvent.detail);
      if (delta < 0){
        console.log("wheel up");
        $('.scrollbar.top').trigger('click');
      } else {
        console.log("wheel down");
        $('.scrollbar.bottom').trigger('click');
      }
    });

    init();
    if(val['k']) {
      StorageUtil.setShortcode(val['k']);
      getCorrelationId(val['k']).then(function(cid){
        AssistAgentSDK.startSupport({
          correlationId: cid,
          sessionToken: StorageUtil.getSessionId(),
          url: "https://sdk005.live-assist.jp",
          additionalAttribute: config.additionalAttribute
        })
      }).then(AssistAgentSDK.requestScreenShare());
    }
  });
  // -->
</script>
