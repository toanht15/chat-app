<?=$this->Html->script("//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js");?>

<script type="text/javascript">
<!--
'use strict';
var socket, tabId = '<?=$tabInfo?>', windowSize, windowClose, url, emit, pdfjsApi;

(function(){
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
    var data = JSON.stringify(obj);
    socket.emit(ev, data);
  };

  windowClose = function(){
    window.close();
    return false;
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
    emit('docShareConnect', {tabId: tabId, responderId: '<?=$userInfo["id"]?>'}); // 資料共有開始
  });

  socket.on('syncResponce', function(data){
    var obj = JSON.parse(data);
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

<?php echo $this->element('Customers/documentLists') ?>

var pdfjsCNST = function(){
  return {
    FIRST_PAGE: "最初のページ",
    LAST_PAGE: "最後のページ",
  };
};


  PDFJS.workerSrc = "http://socket.localhost:8080/websocket/pdf.worker.js";

  pdfjsApi = {
      cnst: new pdfjsCNST(),
      pdf: null,
      pdfUrl: "http://contact.localhost/files/test.pdf",
      currentPage: 1,
      currentScale: 1,
      init: function(){
        this.showpage();

        // ウィンドウリサイズ
        var resizeTimer = null;
        window.addEventListener('resize', function(){
          if ( resizeTimer ) {
            clearTimeout(resizeTimer);
          }
          resizeTimer = setTimeout(function(){
            pdfjsApi.showpage();
          }, 300);
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
      },
      prevPage: function(){
        if ( this.currentPage < 2 ) return this.notificate('FIRST_PAGE');
        this.currentPage--;
        this.showpage();
      },
      nextPage: function(){
        if ( this.currentPage >= this.pdf.pdfInfo.numPages ) return this.notificate('LAST_PAGE');
        this.currentPage++;
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
        this.showpage();
      },
      zoomIn: function(num){
        if ( this.currentScale >= 4 ) return false;
        this.currentScale+=num;
        if ( this.currentScale > 4 ) {
          this.currentScale = 4;
        }
        this.showpage();
      },
      zoomOut: function(num){
        if ( this.currentScale <= 0 ) return false;
        this.currentScale-=num;
        if ( this.currentScale <= num ) {
          this.currentScale = num;
        }
        this.showpage();
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

            function fitWindow(page) {
              var viewport = page.getViewport(1);
              var widthScale = canvasFrame.clientWidth/viewport.width;
              var heightScale = canvasFrame.clientHeight/viewport.height;
              var scale = ( widthScale > heightScale ) ? heightScale : widthScale;
              return page.getViewport(scale * pdfjsApi.currentScale);
            }

            // Get canvas#the-canvas
            var canvas = document.createElement('canvas');
            canvas.setAttribute('id', 'the-canvas');
            $(canvasFrame).html(canvas);
            // Fetch canvas' 2d context
            var context = canvas.getContext('2d');
            var viewport = fitWindow(page);
            // Set dimensions to Canvas
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            // Set Margin
            var calc = ((window.innerHeight - 40 - viewport.height) > 0) ? (window.innerHeight - 40 - viewport.height)/2 : 0;
            canvasFrame.style.paddingTop = String(calc) + "px";
            // Prepare object needed by render method
            var renderContext = {
              canvasContext: context,
              viewport: viewport
            };
            // Render PDF page
            page.render(renderContext)
              .then(function(){
                document.getElementById('pages').textContent = pdfjsApi.currentPage + "/ " + pdfjsApi.pdf.pdfInfo.numPages;
                canvas.style.opacity = 1;
              });
          });
      },
      notificate: function(code){
        if ( this.cnst.hasOwnProperty(code) ) {
          console.log(this.cnst[code]);
        }
      }
    };

    pdfjsApi.init();
    window.focus();

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
      <li onclick="windowClose()">
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
        <select name="scale_type" id="scaleType" onchange="pdfjsApi.cngScale()">
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
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_plus.png" width="30" height="30" alt=""></span>
      </li>
      <li onclick="pdfjsApi.zoomOut(0.25); return false;">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_minus.png" width="30" height="30" alt=""></span>
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
