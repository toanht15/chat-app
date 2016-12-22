<script type="text/javascript">
  function chgOpStatus(){
    var opState = $('#changeOpStatus'),
        status = opState.data('status');
    $.ajax({
      type: 'GET',
      url: '<?=$this->Html->url(array("action" => "remoteChangeOperatorStatus"))?>',
      data: {
        status: status
      },
      dataType: 'json',
      cache: false,
      success: function(json){

        if ( json.status == "<?=C_OPERATOR_ACTIVE?>" ) {
          chgOpStatusView("<?=C_OPERATOR_ACTIVE?>");
          sendRegularlyRequest("<?=C_OPERATOR_ACTIVE?>");
        }
        else {
          chgOpStatusView("<?=C_OPERATOR_PASSIVE?>");
          sendRegularlyRequest("<?=C_OPERATOR_PASSIVE?>");
        }

      }
    });

  }

  function makeToken(){
    var n = 20,
        str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQESTUVWXYZ1234567890",
        strLen = str.length,
        token = "";
    for(var i=0; i<n; i++){
      token += str[Math.floor(Math.random()*strLen)];
    }
    return token;
  };

  function trimToURL(excludes, url){
    var builder = { search: '', hash: '' }, baseUrl = url;

    if ( !url ) return url;

    if ( typeof(URL) === "function" ) {
      var builder = new URL(url);
      baseUrl = builder.origin + builder.port + builder.pathname;
    }
    // URLインターフェースがないブラウザへの救済処置
    else {
      var tmpURL = url.split('?'), search = '', num = '', hash = '';
      // パラメーターを含んでいる場合
      if ( tmpURL[1] !== undefined ) {
        baseUrl = tmpURL[0]; // ? 以降を変数に格納
        search = tmpURL[1]; // ? 以降を変数に格納
        /* ハッシュの為の処理 */
        num = url.indexOf('#'); // # の位置を確認
        hash = (num > -1) ? url.substr(num) : ""; // # があった場合は、それ以降の文字列を変数に格納
        search = '?' + search.replace(hash, ""); // # 以降の文字列をもとのURLから取り除き、変数に格納
        builder = { // エセURLインターフェースの戻り値作成
          search: search,
          hash: hash,
        };
      }
    }

    if (builder.search) {
      builder.search = "?" + builder.search.substr(1).split('&').filter(function (item) {
          return !excludes.hasOwnProperty(item.split('=', 2)[0]);
      }).join('&');
      builder.search = ( builder.search.search(/\?$/) < 0 ) ? builder.search : ""; // IE対策
      return baseUrl + builder.search + builder.hash; // builder.toString()できるといいなぁ
    }
    else {
      return url;
    }
  }

  // エスケープ用
  // http://qiita.com/saekis/items/c2b41cd8940923863791
  function escape_html (string) {
    if(typeof string !== 'string') {
      return string;
    }
    return string.replace(/[&'`"<>]/g, function(match) {
      return {
        '&': '&amp;',
        "'": '&#x27;',
        '`': '&#x60;',
        '"': '&quot;',
        '<': '&lt;',
        '>': '&gt;',
      }[match]
    });
  }

  function ajaxTimeout(ev, jqxhr, set, excep){
    var log = {
      user: "<?php if ( isset($userInfo['id']) ) { echo $userInfo['id']; } ?>",
      currentUrl: "",
      currentTitle: "",
      errorType: "",
      status: "",
      statusText: excep,
      requestType: "",
      requestUrl: "",
      requestDataType: "",
      requestData: "",
    };

    if ( 'target' in ev ) {
      log.currentUrl    = ( ev.target.hasOwnProperty('URL') ) ? ev.target.URL   : "";
      log.currentTitle  = ( ev.target.hasOwnProperty('title') ) ? ev.target.title : "";
    }
    log.errorType       = ( ev.hasOwnProperty('type') ) ? ev.type      : "";
    log.status          = ( jqxhr.hasOwnProperty('status') ) ? jqxhr.status : "";
    log.requestType     = ( set.hasOwnProperty('type') ) ? set.type     : "";
    log.requestUrl      = ( set.hasOwnProperty('url') ) ? set.url      : "";
    log.requestDataType = ( set.hasOwnProperty('dataType') ) ? set.dataType : "";
    log.requestData     = ( set.hasOwnProperty('data') ) ? set.data     : "";
    location.href = "<?=$this->Html->url(['controller' => 'Login', 'action'=>'loginCheck'])?>" + "?error=" + encodeURIComponent(JSON.stringify(log));
  }

  /* Angularの描画 */
  !function(){
    var bootTimer = null;
    if ( 'angular' in window ) {
      // 500ミリ秒後、描画が正常に行われていなかった場合
      bootTimer = setInterval(function(){
        if ( angular.element('*[ng-cloak]').length > 0 ) {
          // 描画し直す
          angular.bootstrap(document, ['sincloApp']);

          // 接続し直す
          if ( 'socket' in window ) {
            // 再接続
            socket.disconnect();
            socket.connect();
          }
        }
        else {
          clearInterval(bootTimer);
          bootTimer = null;
        }
      }, 500);
    }
  }();

  $(document).ready(function(){
    /* フッター設置ボタンの位置調整 */
    var target = document.getElementsByClassName("fotterBtnArea"), fotterBtnAreaResizeTimer = null;
    if ( target.length === 0 ) return false;
    if ( target.length === 0 ) return false;
      $(window).resize(function(){
        if ( fotterBtnAreaResizeTimer ) return false;
        fotterBtnAreaResizeTimer = setTimeout(function(){
          fotterBtnAreaResizeTimer = null;
          setCtrlArea(target[0].id);
        }, 300);
      });
    setCtrlArea(target[0].id);
  });

  function setCtrlArea(id){
      var cont = document.getElementById('content');
      var bottom = cont.offsetHeight - cont.clientHeight;
      var right = cont.offsetWidth - cont.clientWidth;
      var left = 60;
      $('#' + id).animate({
          bottom: bottom,
          right: right,
          left: left
      }, 100);
  }

  $.ajaxSetup({
    cache: false,
  });

  $(document).ajaxError(function(event, jqxhr, settings, exception){
    ajaxTimeout(event, jqxhr, settings, exception);
  });


</script>
