<script type="text/javascript">
<!--
'use strict';
var socket, userId, tabId, iframe, windowSize, connectToken, url, emit, resizeApi, arg = new Object;

(function(){
  // -----------------------------------------------------------------------------
  //  関数
  // -----------------------------------------------------------------------------

  var pair=location.search.substring(1).split('&');
  for(var i=0;pair[i];i++) {
      var kv = pair[i].split('=');
      arg[kv[0]]=kv[1];
  }

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
    frameSize: {
      width: window.outerWidth - window.innerWidth,
      height: window.outerHeight - window.innerHeight
    },
    change: function () {
      var wsInfo = JSON.parse(sessionStorage.getItem('window'));

      if ( !('width' in this.frameSize) || !('height' in this.frameSize)  ) {
        this.frameSize = {
          width: window.outerWidth - window.innerWidth,
          height: window.outerHeight - window.innerHeight
        };
      }

      var cal = 1;
      var frame = {width:null, height:null};
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
      try {
        windowSize = {'width': wswidth, 'height': wsheight};
        window.resizeTo(wswidth, wsheight);
      }
      catch(e) {
        console.log("error resize.", e);
      }
    }
  };

  window.addEventListener('resize', resizeApi.adResizeChk, false);

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
    if ( sessionStorage.getItem('url') && sessionStorage.getItem('window') ) {
      url = sessionStorage.getItem('url');
      var ws = JSON.parse(sessionStorage.getItem('window'));
    }
    else {
      url = decodeURIComponent(arg.url);

      var ws = {'width':arg.width, 'height':arg.height};
    }

    var content = document.getElementById('customer_flame');
    var html  = "<iframe src='' style='transform-origin: 0 0' ";
        html += "        width='300' height='300' sandbox=\"allow-scripts allow-top-navigation allow-forms allow-same-origin allow-modals\"></iframe>";

    content.innerHTML = html;
    iframe = document.getElementsByTagName('iframe')[0];

    if ( url.match(/\?/) ) {
      url += "&";
    }
    else {
      url += "?";
    }

    var data = {
      type:2,
      responderId: "<?= $muserId?>",
      userId: userId,
      sendTabId: tabId,
      connectToken: arg.connectToken,
      first: true
    };

    iframe.src = url + "sincloData=" + encodeURIComponent(JSON.stringify(data));
    resizeApi.cuResize(ws);
    emit('connectFrame', {tabId: tabId});
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

    if ( (array !== null) && ('index' in array) ) {
      sessionStorage.setItem('url', str.substring(0, array.index));
    }
    else {
      sessionStorage.setItem('url', obj.url);
    }
  });

  socket.on('syncStop', function(d){
    var obj = JSON.parse(d);
    if ('message' in obj) {
      modalOpen.call(window, obj.message, 'p-alert', 'メッセージ');
      popupEvent.closeNoPopup = function(){
        popupEvent.close();
        window.open('about:blank', '_self').close();
        window.close();
      };
    }
  });

  socket.on('unsetUser', function(d){
    var obj = JSON.parse(d);
    if ( obj.tabId !== tabId ) return false;
      modalOpen.call(window, '再接続しますか？', 'p-confirm', 'メッセージ');
      popupEvent.closePopup = function(){
        location.reload();
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

<div id="customer_flame">

</div>
