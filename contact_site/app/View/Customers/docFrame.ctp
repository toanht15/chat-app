<?=$this->Html->script("//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js");?>

<script type="text/javascript">
<!--
'use strict';

var socket, emit, tabId = '<?=$tabInfo?>', windowSize, url, emit, pdfjsApi, frameSize;

<?php echo $this->element('Customers/documentLists') ?>

(function(){

  // WebSocketサーバに接続
  socket = io.connect("<?=C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT?>");

  // -----------------------------------------------------------------------------
  //  関数
  // -----------------------------------------------------------------------------

  emit = function(ev, d){
    var obj = {};
    if ( typeof(d) !== "object" ) {
      obj = JSON.parse(d);
    }
    else {
      obj = d;
    }
    obj.siteKey = "<?=$siteKey?>";
    obj.tabId = tabId;
    var data = JSON.stringify(obj);
    socket.emit(ev, data);
  };

  var pdfjsCNST = function(){
    return {
      FIRST_PAGE: "最初のページ",
      LAST_PAGE: "最後のページ",
    };
  };


  PDFJS.workerSrc = "<?=C_PATH_NODE_FILE_SERVER?>/websocket/pdf.worker.min.js";

    pdfjsApi = {
    cnst: new pdfjsCNST(),
    pdf: null,
    pdfUrl: null,
    currentPage: 1,
    currentScale: 1,
    init: function(){
      this.showpage();
      var canvas = document.getElementById('document_canvas');

      // マウス位置
      window.addEventListener('mousemove', function(e){
        var canvas = document.getElementById('document_canvas');
        emit("docSendAction", {
          to: 'customer',
          mouse: {
            x: e.clientX,
            y: e.clientY
          }
        });
      });

      // キープレス
      window.addEventListener('keydown',function(e){
        if ( e.keyCode === 37 || e.keyCode === 38 ) {
          pdfjsApi.prevPage();
        }
        else if ( e.keyCode === 39 || e.keyCode === 40 ) {
          pdfjsApi.nextPage();
        }
      });
      // Ctrl + ホイール
      window.addEventListener('wheel', function(e){
        if ( e.ctrlKey ) {
          if (e.preventDefault) {
            e.preventDefault();
          }
          // 拡大
          if ( e.deltaY < 0 ) {
            pdfjsApi.zoomIn(0.1);
          }
          // 縮小
          else {
            pdfjsApi.zoomOut(0.1);
          }
          return false;
        }
      });

      // スクロール位置
      $("#document_canvas").stop().on('scroll', function(e){
        emit("docSendAction", {
          to: 'customer',
          scroll: {
            top: e.target.scrollTop,
            left: e.target.scrollLeft
          }
        });
      });
    },
    prevPage: function(){
      if ( this.currentPage < 2 ) return this.notificate('FIRST_PAGE');
      this.currentPage--;
      this.showpage();
      pdfjsApi.sendCtrlAction();
    },
    nextPage: function(){
      if ( this.currentPage >= this.pdf.pdfInfo.numPages ) return this.notificate('LAST_PAGE');
      this.currentPage++;
      pdfjsApi.sendCtrlAction();
      this.showpage();
    },
    cngScale: function(){
      var type = document.getElementById('scaleType').value;
      if ( type && !isNaN(Number(type)) ) {
        this.zoom(type);
      }
    },
    zoom: function(num){
      this.currentScale = num;
      pdfjsApi.sendCtrlAction();
      this.showpage();
    },
    zoomIn: function(num){
      if ( this.currentScale >= 4 ) return false;
      this.currentScale+=num;
      if ( this.currentScale > 4 ) {
        this.currentScale = 4;
      }
      pdfjsApi.sendCtrlAction();
      this.showpage();
    },
    zoomOut: function(num){
      if ( this.currentScale <= 0 ) return false;
      this.currentScale-=num;
      if ( this.currentScale <= num ) {
        this.currentScale = num;
      }
      pdfjsApi.sendCtrlAction();
      this.showpage();
    },
    sendCtrlAction: function(){
      var canvas = document.getElementById('document_canvas');
      emit("docSendAction", {
        to: 'customer',
        page: pdfjsApi.currentPage,
        scale: pdfjsApi.currentScale
      });
    },
    showpage: function(){
      // Asynchronous download PDF
      PDFJS.getDocument(pdfjsApi.pdfUrl)
        .then(function(pdf) {
          pdfjsApi.pdf = pdf;
          return pdf.getPage(pdfjsApi.currentPage);
        })
        .then(function(page) {
          var canvasFrame = document.getElementById('document_canvas');
          // Get canvas#the-canvas
          if ( !pdfjsApi.canvas ) {
            pdfjsApi.canvas = document.createElement('canvas');
            pdfjsApi.canvas.setAttribute('id', 'the-canvas');
            $(canvasFrame).html(pdfjsApi.canvas);
            pdfjsApi.context = pdfjsApi.canvas.getContext('2d');
          }

          pdfjsApi.page = page;
          pdfjsApi.render();
        });
    },
    render: function(){
      var canvasFrame = document.getElementById('document_canvas');

      function fitWindow(page) {
        var viewport = page.getViewport(1);
        var widthScale = canvasFrame.clientWidth/viewport.width;
        var heightScale = canvasFrame.clientHeight/viewport.height;
        var scale = ( widthScale > heightScale ) ? heightScale : widthScale;
        return page.getViewport(scale * pdfjsApi.currentScale);
      }
      var page = pdfjsApi.page;
      // Fetch canvas' 2d context
      var viewport = fitWindow(page);
      // Set dimensions to Canvas
      pdfjsApi.canvas.height = viewport.height;
      pdfjsApi.canvas.width = viewport.width;
      // Set Margin
      var calc = ((window.innerHeight - 40 - viewport.height) > 0) ? (window.innerHeight - 40 - viewport.height)/2 : 0;
      canvasFrame.style.paddingTop = String(calc) + "px";

      setTimeout(function(){
        // Render PDF page
        page.render({
          canvasContext: pdfjsApi.canvas.getContext('2d'),
          viewport: viewport
        }).then(function(){
            document.getElementById('pages').textContent = pdfjsApi.currentPage + "/ " + pdfjsApi.pdf.pdfInfo.numPages;
            pdfjsApi.canvas.style.opacity = 1;
        });
      }, 0);
    },
    notificate: function(code){
      if ( this.cnst.hasOwnProperty(code) ) {
        console.log(this.cnst[code]);
      }
    }
  };


  var xhr = new XMLHttpRequest();
  xhr.open('GET', "https://s3-ap-northeast-1.amazonaws.com/medialink.sinclo.jp/medialink/%E3%83%86%E3%82%B9%E3%83%88PDF.pdf", true);
  xhr.responseType = 'arraybuffer';
  xhr.onload = function(e) {
    if (this.status == 200) {
      // Note: .response instead of .responseText
      var blob = new Blob([this.response], {type: 'application/pdf'});
      pdfjsApi.pdfUrl = URL.createObjectURL(blob);
      pdfjsApi.init();
    }
  };
  xhr.send();

  window.focus();
})();

window.onload = function(){

  // スクロール禁止
  $(window).scroll(function(e) {
    $(this).scrollTop(0);
    $(this).scrollLeft(0);
  });

  // WebSocketサーバ接続イベント
  socket.on('connect', function(){
    emit('docShareConnect', {from: 'company', responderId: '<?=$userInfo["id"]?>'}); // 資料共有開始

    frameSize = {
      height: window.outerHeight - window.innerHeight,
      width: window.outerWidth - window.innerWidth
    };

  });

  // 同期イベント
  socket.on('docSendAction', function(d){
    var obj = JSON.parse(d), cursor;
    if ( obj.hasOwnProperty('scroll') ) {
      var canvasFrame = document.getElementById('document_canvas');
      canvasFrame.scrollTop = obj.scroll.top;
      canvasFrame.scrollLeft = obj.scroll.left;
      return false;
    }
    if ( obj.hasOwnProperty('mouse') ) {
      cursor = document.getElementById('cursorImg');
      // カーソルを作成していなければ作成する
      if ( !cursor ) {
        $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mouse.x + '; left:' + obj.mouse.y + '; z-index: 1"><img width="50px" src="<?=C_PATH_NODE_FILE_SERVER?>/img/pointer.png"></div>');
        cursor = document.getElementById("cursorImg");
      }
      cursor.style.left = obj.mouse.x + "px";
      cursor.style.top  = obj.mouse.y + "px";
      return false;
    }
    if ( obj.hasOwnProperty('offset') ) {
      window.resizeTo(frameSize.width + obj.offset.width, frameSize.height + obj.offset.height);
    }
    if ( obj.hasOwnProperty('page') ) {
      pdfjsApi.currentPage = obj.page;
    }
    if ( obj.hasOwnProperty('scale') ) {
      pdfjsApi.currentScale = obj.scale;
    }
    pdfjsApi.showpage();
  });
};


// -->
</script>

<section id="document_share" ng-app="sincloApp" ng-controller="MainCtrl">

  <!-- /* サイドバー */ -->
  <ul id="document_share_tools">
    <li-top>
      <p>ID:1234</p>
      <li id=""onclick="">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_user.png" width="40" height="40" alt=""></span>
        <p>顧客情報</p>
      </li>
    </li-top>
    <li-bottom>
      <li ng-click="openDocumentList()">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_document.png" width="40" height="40" alt=""></span>
        <p>資料切り替え</p>
      </li>
      <li onclick="return false;">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_reconnect.png" width="40" height="40" alt=""></span>
        <p>ダウンロード</p>
      </li>
      <li onclick="window.close(); return false;">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_disconnect.png" width="40" height="40" alt=""></span>
        <p>閉じる</p>
      </li>
    </li-bottom>
  </ul>
  <!-- /* サイドバー */ -->

  <!-- /* ツールバー */ -->
  <ul id="document_ctrl_tools">
    <li-left>
      <li onclick="pdfjsApi.prevPage(); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_back.png" width="30" height="30" alt=""></span>
      </li>
      <li onclick="pdfjsApi.nextPage(); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_next.png" width="30" height="30" alt=""></span>
      </li>
    </li-left>
    <li-center>
      <li>
        <span id="pages"></span>
      </li>
    </li-center>
    <li-right>
      <li id="scaleChoose">
        <label dir="scaleType">拡大率</label>
        <select name="scale_type" id="scaleType" onchange="pdfjsApi.cngScale(); return false;">
          <option value=""   > - </option>
          <option value="0.5"   >50%</option>
          <option value="0.75"  >75%</option>
          <option value="1"     selected>100%</option>
          <option value="1.5"   >150%</option>
          <option value="2"     >200%</option>
          <option value="2.5"   >250%</option>
          <option value="3"     >300%</option>
          <option value="4"     >400%</option>
        </select>
      </li>
      <li onclick="pdfjsApi.zoomIn(0.25); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_plus.png" width="30" height="30" alt=""></span>
      </li>
      <li onclick="pdfjsApi.zoomOut(0.25); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_minus.png" width="30" height="30" alt=""></span>
      </li>
    </li-right>
  </ul>
  <!-- /* ツールバー */ -->

  <div id="tabStatusMessage">別の作業をしています</div>

  <div id="document_canvas"></div>

  <div id="ang-popup">
    <div id="ang-base">
      <div id="ang-popup-background"></div>
      <div id="ang-popup-frame">
        <div id="ang-popup-content" class="document_list">
          <div id="title_area">資料一覧</div>
          <div id="search_area">
            <?=$this->Form->input('name', ['label' => 'フィルター：', 'ng-model' => 'searchName']);?>
            <ng-multi-selector></ng-multi-selector>
          </div>
          <div id="list_area">
            <ol>
              <li ng-repeat="document in searchFunc(documentList)">
                <div class="document_image">
                  <?=$this->Html->image("tab_status_disable.png", ["style"=>"width:10em;height:7em"])?>
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
