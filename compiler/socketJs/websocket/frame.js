// -----------------------------------------------------------------------------
//  websocket通信
// -----------------------------------------------------------------------------

// 接続時
socket.on("connect", function(){
  var data = {
    userId: null,
    accessId: null,
    userAgent:window.navigator.userAgent,
    title: common.title(),
    url: location.href
  };

  // cookieに対応させる
  if (Number(userInfo.accessType) === Number(cnst.access_type.guest)) {
    data.userId = userInfo.getUserId();
    data.accessId = userInfo.getAccessId();
    // TODO リアルタイムの仕組み再検討
    common.emit('connected', {type: 'user', data: data});
  }

}); // socket-on: connect

// 接続直後（ユーザＩＤ、アクセスコード発番等）
socket.on("accessInfo", function(d) {
  var obj = common.jParse(d);
  if ( obj.token !== common.token ) return false;

  if ( !check.isset(userInfo.userId) && check.isset(obj.userId) ) {
    userInfo.set(cnst.info_type.user, obj.userId);
  }
  if ( check.isset(obj.accessId) ) {
    userInfo.set(cnst.info_type.access, obj.accessId);
    $('#websocketInfo').remove();
    $('body').append('<div id="websocketInfo" style="position: fixed; top: 20%; right: 0; border: 1px solid #6DC0D2; padding: 5px; background-color: #A5EFFF; border-radius: 10px;">アクセスコード:' + localStorage.accessId + '</div>');
  }
  if ( userInfo.accessType === Number(cnst.access_type.guest) ) {
    userInfo.set(cnst.info_type.ip, obj.ipAddress);
  }
}); // socket-on: accessInfo

// 情報送信
socket.on("getAccessInfo", function(d) {
  var obj = common.jParse(d);
  if ( userInfo.accessType !== Number(cnst.access_type.guest) ) return false;

  common.emit('sendAccessInfo', {
    userId: userInfo.userId,
    accessId: userInfo.accessId,
    ipAddress: userInfo.ipAddress,
    userAgent: userInfo.userAgent,
    title: common.title(),
    url: browserInfo.url,
    receiveAccessInfoToken: obj.token
  });
}); // socket-on: getAccessInfo

// 画面共有
socket.on('getWindowInfo', function(d){
  var obj = common.jParse(d);
  if ( obj.userId !== userInfo.userId ) return false;

  // TODO 通信時に使おう。
  userInfo.connectToken = obj.connectToken;

  // TODO 一時間経過後とかでもテストする
  common.emit('sendWindowInfo', {
    userId: userInfo.userId,
    connectToken: userInfo.connectToken,
    // 解像度
    screen: browserInfo.windowScreen(),
    // ブラウザのサイズ
    windowSize: browserInfo.windowSize(),
    // スクロール位置の取得
    scrollPosition: browserInfo.windowScroll(),
    // 現在のページ
    url: browserInfo.url
  });
}); // socket-on: getWindowInfo

// スクロール位置のセット
socket.on('windowSyncInfo', function (d) {
  var obj = common.jParse(d);
  // 担当しているユーザーかチェック
  if ( obj.userId !== userInfo.userId ) return false;
  if ( Number(userInfo.accessType) !== Number(cnst.access_type.host) ) return false;

  browserInfo.set.scroll(obj.scrollPosition);
}); // socket-on: windowSyncInfo

// 同期情報の収集
socket.on('syncStart', function(d){
  var obj = common.jParse(d);
  if ( obj.userId !== userInfo.userId ) return false;
  if ( Number(userInfo.accessType) !== Number(cnst.access_type.guest) ) return false;
  // フォーム情報収集
  var inputInfo = [];
  $('input').each(function(){
    inputInfo.push(this.value);
  });
   var textareaInfo = [];
  $('textarea').each(function(){
    textareaInfo.push(this.value);
  });

  common.emit('getSyncInfo', {
    userId: userInfo.userId,
    inputInfo: inputInfo,
    textareaInfo: textareaInfo,
    // スクロール位置の取得
    scrollPosition: browserInfo.windowScroll()
  });

}); // socket-on: syncStart

// 消費者画面の情報を反映
socket.on('syncElement', function(data){
  var obj = common.jParse(data);
  if ( obj.userId !== userInfo.userId ) return false;
  if ( Number(userInfo.accessType) ==! Number(cnst.access_type.host) ) return false;
  $("body").animate(
    {
      scrollLeft:obj.scrollPosition.x,
      scrollTop:obj.scrollPosition.y
    },
    {
        duration: 'first',
        easing: 'swing',
        complete: function(){
          for ( var i in obj.inputInfo ) {
            var n = Number(i);
            $('input').eq(n).val(obj.inputInfo[n]);
          }
          for ( var i in obj.textareaInfo ) {
            var n = Number(i);
            $('textarea').eq(n).val(obj.textareaInfo[n]);
          }
          common.emit('syncCompleate', {userId: userInfo.userId, accessType: userInfo.accessType});
        }
    }
  );

}); // socket-on: syncElement

// イベント監視
socket.on('syncEvStart', function(d){
  var obj = common.jParse(d);
  if ( obj.userId !== userInfo.userId ) return false;
  if ( obj.accessType === userInfo.accessType ) return false;
  syncEvent.start(true);
}); // socket-on: syncEvStart

// イベント結果適用
socket.on('syncResponce', function (d) {
  var obj = common.jParse(d), cursor = common.cursorTag;
  // 画面共有用トークンでの認証に変更する？
  if ( obj.userId !== userInfo.userId ) return false;
  if ( Number(obj.accessType) === Number(userInfo.accessType) ) return false;
  // カーソルを作成していなければ作成する
  if ( !check.isset(cursor) ) {
    $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mousePoint.x + '; left:' + obj.mousePoint.y + '"><img width="50px" src="http://183.177.237.205:3000/img/pointer.png"></div>');
    cursor = common.cursorTag = document.getElementById("cursorImg");
  }
  // カーソル位置
  if ( check.isset(obj.mousePoint)) {
    cursor.style.left = obj.mousePoint.x + "px";
    cursor.style.top  = obj.mousePoint.y + "px";
  }
  // スクロール位置
  if ( check.isset(obj.scrollPosition) ) {
    syncEvent.receiveEvInfo.type = "scroll";
    syncEvent.receiveEvInfo.nodeName = "body";

    browserInfo.set.scroll(obj.scrollPosition);

    // TODO まだ微調整が必要
    setTimeout(function(){
      syncEvent.receiveEvInfo = { nodeName: null, type: null };
    }, browserInfo.interval);
  }
}); // socket-on: syncResponce

socket.on('syncResponceEv', function (d) {
  var obj = common.jParse(d), elm;
  if ( obj.userId !== userInfo.userId ) return false;
  if ( obj.accessType === userInfo.accessType ) return false;
  elm = $(String(obj.nodeName)).eq(Number(obj.idx));
  syncEvent.receiveEvInfo.type = obj.type;
  syncEvent.receiveEvInfo.nodeName = String(obj.nodeName);
  syncEvent.receiveEvInfo.idx = Number(obj.idx);
  switch (obj.type) {
    case "click":
      elm.trigger(String(obj.type));
      break;
    case "keyup":
      elm.val(obj.value);
      break;
    case "scroll":
      $(obj.nodeName).eq(Number(obj.idx)).stop(false, false).scrollTop(Number(obj.value.top));
      $(obj.nodeName).eq(Number(obj.idx)).stop(false, false).scrollLeft(Number(obj.value.left));
    // console.log('scroll', obj.userId);
    //   document.getElementsByTagName(obj.nodeName)[Number(obj.idx)].scrollTop = Number(obj.value.top);
    //   document.getElementsByTagName(obj.nodeName)[Number(obj.idx)].scrollLeft = Number(obj.value.left);
  };
  syncEvent.receiveEvInfo = { nodeName: null, type: null };

}); // socket-on: syncResponceEv

socket.on('syncStop', function(d){
  var obj = common.jParse(d);
  if ( obj.userId !== userInfo.userId ) return false;
  syncEvent.stop(false);
}); // socket-on: syncStop

$(window).on('beforeunload', function(e) {
  // ログアウト通知
  common.emit('userOut', {userId: userInfo.userId, accessType: userInfo.accessType});
});
