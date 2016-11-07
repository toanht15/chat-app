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

  window.addEventListener('resize', resizeApi.adResizeChk, false);
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
    if ( sessionStorage.getItem(iframeLocation.sessionName) && sessionStorage.getItem('window') ) {
      iframeLocation.get();
      var ws = JSON.parse(sessionStorage.getItem('window'));
    }
    else {
      iframeLocation.list = [arg.url];
      iframeLocation.position = 0;
      iframeLocation.save();

      var ws = {'width':arg.width, 'height':arg.height};
    }

    url = iframeLocation.list[iframeLocation.position];
    iframeLocation.setBtnColor();

    var content = document.getElementById('customer_flame');
    var html  = "<iframe src='' style='transform-origin: 0 0' width='300' height='300'></iframe>";

    content.innerHTML = html;
    iframe = document.getElementsByTagName('iframe')[0];
    iframe.sandbox = "allow-scripts allow-top-navigation allow-forms allow-same-origin allow-modals";

    if ( url.match(/\?/) ) {
      url += "&";
    }
    else {
      url += "?";
    }

    var data = {
      type:2,
      shareType: arg.type,
      responderId: "<?= $muserId?>",
      userId: userId,
      sendTabId: tabId,
      connectToken: arg.connectToken,
      first: true
    };

    iframe.src = url + "sincloData=" + encodeURIComponent(JSON.stringify(data));
    resizeApi.cuResize(ws);
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

<ul id="sync_tools">
  <li id="prevBtn" class="unlight" onclick="iframeLocation.back(); return false;">
    <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_back.png" width="40" height="40" alt=""></span>
    <p>戻る</p>
  </li>
  <li id="nextBtn" class="unlight" onclick="iframeLocation.forward(); return false;">
    <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_next.png" width="40" height="40" alt=""></span>
    <p>進む</p>
  </li>
  <li onclick="location.reload(true); return false;">
    <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_reconnect.png" width="40" height="40" alt=""></span>
    <p>再接続</p>
  </li>
  <li onclick="windowClose()">
    <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_disconnect.png" width="40" height="40" alt=""></span>
    <p>終了</p>
  </li>
</ul>
<div id="customer_flame">
</div>
<div id="tabStatusMessage">別の作業をしています</div>