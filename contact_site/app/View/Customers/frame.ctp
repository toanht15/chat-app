<script type="text/javascript">
<!--
var socket, userId, iframe, connectToken, url, emit, windowResize, arg = new Object;

(function(){
  // -----------------------------------------------------------------------------
  //  関数
  // -----------------------------------------------------------------------------

  pair=location.search.substring(1).split('&');
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

  windowResize = function (ws) {
    iframe.width = ws.width;
    iframe.height = ws.height;

    ws = {'width':iframe.width, 'height':iframe.height};
    // 現在のウィンドウサイズを保存しておく
    sessionStorage.setItem('window', JSON.stringify(ws));

    var outHeightSize = window.outerHeight - window.innerHeight;
    var outWidthSize = window.outerWidth - window.innerWidth;
    wswidth = ws.width * arg.scale + outWidthSize;
    wsheight = ws.height * arg.scale + outHeightSize;
    window.resizeTo(wswidth, wsheight);
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
    if ( sessionStorage.getItem('url') && sessionStorage.getItem('window') ) {
      url = sessionStorage.getItem('url');
      ws = JSON.parse(sessionStorage.getItem('window'));
    }
    else {
      url = decodeURIComponent(arg.url);

      ws = {'width':arg.width, 'height':arg.height};
      // 現在のウィンドウサイズを保存しておく
      sessionStorage.setItem('window', JSON.stringify(ws));
    }

    var content = document.getElementById('customer_flame');
    var html  = "<iframe src='' style='transform:scale(" + Number(arg.scale) + "); transform-origin: 0 0' ";
        html += "        width='" + arg.width + "' height='" + arg.height + "' sandbox=\"allow-scripts allow-top-navigation allow-forms allow-same-origin allow-modals\"></iframe>";

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
      userId: userId,
      sendTabId: tabId,
      connectToken: arg.connectToken,
      first: true
    };

    iframe.src = url + "sincloData=" + encodeURIComponent(JSON.stringify(data));
    windowResize(ws);
    emit('connectFrame', {tabId: tabId});
  });

  socket.on('syncResponce', function(data){
    var obj = JSON.parse(data);
    windowResize(obj.windowSize);
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
    if (('message' in obj) && window.confirm(obj.message)){
      window.open('about:blank', '_self').close();
      window.close();
    }
    else {
      window.open('about:blank', '_self').close();
      window.close();
    }
  });

  socket.on('unsetUser', function(d){
    var obj = JSON.parse(d);
    if ( obj.tabId !== tabId ) return false;
    if (window.confirm('再接続しますか？')){
      location.reload();
    }
    else {
      window.open('about:blank', '_self').close();
      window.close();
    }
  });

};
// -->
</script>

<div id="customer_flame">

</div>
