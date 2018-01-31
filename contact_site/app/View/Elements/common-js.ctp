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
  function formatBytes(a,b){if(0==a)return"0 Bytes";var c=1024,d=b||2,e=["Bytes","KB","MB","GB","TB","PB","EB","ZB","YB"],f=Math.floor(Math.log(a)/Math.log(c));return parseFloat((a/Math.pow(c,f)).toFixed(d))+" "+e[f]};

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
    if(!jqxhr.hasOwnProperty('status') || jqxhr.status !== 409) { // 409エラーは定型文管理画面のエラーハンドリングで利用しているためリダイレクトさせない
      location.href = "<?=$this->Html->url(['controller' => 'Login', 'action' => 'loginCheck'])?>" + "?error=" + encodeURIComponent(JSON.stringify(log));
    }
    <?php } else { // テスト ?>
      console.dir(log);
    <?php } ?>
  }

  function addTooltipEvent() {
    setTimeout(function(){
      // 共通ツールチップの配置（内容はdata-textで指定する）
      $(".commontooltip").each(function(index){
        // サイズ調整用
        var debug = $(this).attr('data-debug');
        $(this).off('mouseenter').off('mouseleave');
        var self = this;
        this.addEventListener('mouseenter', function(e){

          var $this = $(self);
          var text = $this.attr('data-text');
          var baloonPosition = $this.attr('data-balloon-position'); // 吹き出しの＜の部分
          var baloonWidth = $this.attr('data-balloon-width'); // 吹き出しの＜の部分
          var textCenter = $this.attr('data-text-center'); // 吹き出しの＜の部分
          var contentPositionLeft = $this.attr('data-content-position-left') ? Number($this.attr('data-content-position-left')) : 0;
          var noleft = $this.attr('noleft');
          var operatingHours = $this.attr('operatingHours');
          var $tooltip = $('<div class="tooltips">'+text+'</div>');
          //行数をカウント
          var id = $this.attr('id');
          var brcount = (text.split("<br>")).length;
          if(operatingHours == 'operatingHoursPage') {
            var toppx = 69;
          }
          else if(operatingHours == 'widgetHoursPage') {
            var toppx = 54;
          }
          else {
            var toppx = 39;
          }
          if(brcount > 1){
            toppx = toppx+((brcount-1)*15);
          }

          if(baloonWidth) {
            $tooltip.css('width', baloonWidth + 'px');
          }

          if(textCenter) {
            $tooltip.css('text-align', 'center');
          }

          $('body').append($tooltip);// 要素の表示位置
          var offset = $this.offset();

          // 要素のサイズ
          var size = {
            width: $this.outerWidth(),
            height: $this.outerHeight()
          };

          // ツールチップのサイズ
          var ttSize = {
            width: $tooltip.outerWidth(),
            height: $tooltip.outerHeight()
          };

          var leftCoordinate = (offset.left + size.width / 2 - ttSize.width / 2) + contentPositionLeft;
          var isOverWidth = (leftCoordinate + ttSize.width + 40) > $(window).outerWidth();
          if(isOverWidth) {
            leftCoordinate = (offset.left + size.width)  - ttSize.width;
            baloonPosition = 75;
          }

          // 要素の上に横中央で配置
          if(! noleft){
            $tooltip.css({
              top: offset.top - ttSize.height - 12, // 三角部分の高さ
              left: leftCoordinate
            });
          }
          else{
            $tooltip.css({
              top: offset.top - ttSize.height - 12 // 三角部分の高さ
            });
          }
          if(baloonPosition) {
            $tooltip.append('<style>.tooltips:after{left:' + baloonPosition + '%!important; top:' + toppx + 'px!important;}</style>');
            $tooltip.attr("id","tooltip_"+id)
          }
        }, true); //radioボタンのdisableに対応するためuseCaptureを利用

        if(!debug) {
          this.addEventListener('mouseleave',function(e){
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
