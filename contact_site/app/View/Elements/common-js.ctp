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
  }
  function isNumber(n){
    return RegExp(/^(\+|\-)?\d+(.\d+)?$/).test(n);
  }

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

  function isJSON (arg) {
    arg = (typeof arg === "function") ? arg() : arg;
    if (typeof arg  !== "string") {
      return false;
    }
    try {
      arg = (!JSON) ? eval("(" + arg + ")") : JSON.parse(arg);
      return true;
    } catch (e) {
      return false;
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

  // バイト表示をKB, MB, GBに変更する
  // https://stackoverflow.com/questions/15900485/correct-way-to-convert-size-in-bytes-to-kb-mb-gb-in-javascript
  function formatBytes(a, b) {
    if (0 == a) return "0 Bytes";
    var c = 1024, d = b || 2, e = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"],
      f = Math.floor(Math.log(a) / Math.log(c));
    return parseFloat((a / Math.pow(c, f)).toFixed(d)) + " " + e[f]
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
    <?php if ( APP_MODE_DEV === false ) { // 本番 ?>
    if(!jqxhr.hasOwnProperty('status') || !(jqxhr.status === 409 || (jqxhr.status === 0 && excep === 'abort'))) { // 409エラーは定型文管理画面のエラーハンドリングで利用しているためリダイレクトさせない
      location.href = "<?=$this->Html->url(['controller' => 'Login', 'action' => 'loginCheck'])?>" + "?error=" + encodeURIComponent(JSON.stringify(log));
    }
    <?php } else { // テスト ?>
      console.dir(log);
    <?php } ?>
  }

  var tooltipEventTimer = null;
  function addTooltipEvent() {
    $(".commontooltip").off('mouseenter').off('mouseleave');
    tooltipEventTimer = setTimeout(function(){
      // 共通ツールチップの配置（内容はdata-textで指定する）
      $(".commontooltip").each(function(index){
        // サイズ調整用
        var debug = $(this).attr('data-debug');
        var self = this;
        this.addEventListener('mouseenter', function(e){

          // クラスにcommontooltipを含まないとき、処理を実行しない(radio, checkbox, selectで二重表示されるため)
          if (!/commontooltip/.test(e.target.className) || $('.tooltips').length === 1) {
            return;
          }

          var $this = $(self);
          var text = $this.attr('data-text');
          var $tooltip = $('<div class="tooltips">'+text+'</div>');
          var topWeight = 1;

          $('body').append($tooltip);

          //ツールチップを付ける対象が画面上のどの位置にあるのかを取得
          var offset = $this.offset();

          //ツールチップを付ける対象のサイズを取得
          var size = {
            width: $this.outerWidth(),
            height: $this.outerHeight()
          };

          // ツールチップのサイズ
          var ttSize = {
            width: $tooltip.width(),
            height: $tooltip.outerHeight()
          };

          //leftCoordinate=>左からの位置を格納する変数
          var leftCoordinate = (offset.left + size.width / 2 - ttSize.width / 2) - 12;

          //画面端40px余白を持たせたサイズを、ツールチップのサイズが右側に超過してしまった場合(幅を短くする)
          var overcount = 1;
          while((leftCoordinate + ttSize.width + 40) > $(window).outerWidth()){
            ttSize.width /= 2;
            var topWeight = 1.7;
          }

          //画面端40px余白を持たせたサイズを、ツールチップのサイズが左側に超過してしまった場合(位置を右にずらす)
          while((leftCoordinate- 40) < 0){
            leftCoordinate += overcount*20
            overcount += 1;
          }

          var topCoordinate = offset.top + size.height + 12;
          var ttElement = $(this).attr('class');

          //ボタン系、またはリアルタイムモニタのオペレータのツールチップの場合はボタンの上部に表示するやつ。
          if(ttElement.indexOf('btn-shadow') !== -1 || ttElement.indexOf('ttposition_top') !== -1){
            topCoordinate = offset.top - ttSize.height*topWeight -10;
          }

          if((topCoordinate + ttSize.height + 40) > $(window).outerHeight()){
            topCoordinate = offset.top -ttSize.height - 30;
          }else{
          }
          $tooltip.css({
            top: topCoordinate,
            left: leftCoordinate
          });
        }, true); //radioボタンのdisableに対応するためuseCaptureを利用


        if(!debug) {
          this.addEventListener('mouseleave',function(e){
            // クラスにcommontooltipを含まないとき、処理を実行しない(radio, checkbox, selectで二重表示されるため)
            if (!/commontooltip/.test(e.target.className)) {
              return;
            }

            $('body').find(".tooltips").remove();
          }, true); //radioボタンのdisableに対応するためuseCaptureを利用
        }
      });
    },1);
  }


  $(document).ready(function(){
    /* Angularの描画 */

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

    addTooltipEvent();

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

  function jumpTo(url) {
    location.href = url;
  }

  $.ajaxSetup({
    cache: false,
  });

  $(document).ajaxError(function(event, jqxhr, settings, exception){
    ajaxTimeout(event, jqxhr, settings, exception);
  });


</script>
