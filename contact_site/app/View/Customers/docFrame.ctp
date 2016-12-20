<script type="text/javascript">
<!--
'use strict';

var socket, emit, tabId = '<?=$tabInfo?>', url, emit, pdfjsApi, frameSize, windowScale;
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

  $(document).on("hover", ".showDescriptionBottom",function(e){
    var desc = $(this).data('description');
    if ( desc === undefined ) return false;
    var d = document.getElementById("desc-balloon");
    d.textContent = desc;
    d.style.top = 50 + $(this).offset().top + "px";
    d.style.left = $(this).offset().left + "px";
    d.style.display = "block";
  })
  .on("blur", ".showDescriptionBottom",function(e){
    var d = document.getElementById("desc-balloon");
    d.style.display = "none";
  });
  window.focus();
})();

// WebSocketサーバ接続イベント
socket.on('connect', function(){
  var doc = <?=json_encode($docData['TDocument'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_FORCE_OBJECT )?>;
  if ( sessionStorage.getItem("doc") !== null ) {
    doc = JSON.parse(sessionStorage.getItem("doc"));
    emit('docShareReConnect', {
      from: 'company'
    }); // 資料共有開始
  }
  else {
    emit('docShareConnect', {
      from: 'company',
      responderId: '<?=$userInfo["id"]?>',
      url: "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>" + doc.file_name,
      pagenation_flg: doc.pagenation_flg,
      download_flg: doc.download_flg
    }); // 資料共有開始
  }
  pdfjsApi.readFile(doc);

  frameSize = {
    height: window.outerHeight - window.innerHeight,
    width: window.outerWidth - window.innerWidth
  };

});

window.onload = function(){

  // スクロール禁止
  $(window).scroll(function(e) {
    $(this).scrollTop(0);
    $(this).scrollLeft(0);
  });

  // 同期イベント
  var scAddEventTimer = null;
  windowScale = (sessionStorage.getItem("windowScale")) ? sessionStorage.getItem("windowScale") : 1;
  socket.on('docSendAction', function(d){
    var obj = JSON.parse(d), cursor;
    if ( obj.hasOwnProperty('scroll') ) {
      var canvas = document.getElementById('document_canvas');
      canvas.removeEventListener('scroll', pdfjsApi.scrollFunc);
      clearTimeout(scAddEventTimer);
      $('#document_canvas').animate({
        scrollTop: obj.scroll.top,
        scrollLeft: obj.scroll.left,
      }, {
        duration: 50,
        easing: 'swing',
        complete: function(){
          scAddEventTimer = setTimeout(function(){
            clearTimeout(scAddEventTimer);
            scAddEventTimer= null;
            canvas.addEventListener('scroll', pdfjsApi.scrollFunc);
          }, 300);
        }
      });
      return false;
    }
    if ( obj.hasOwnProperty('offset') ) {
      pdfjsApi.setWindowSize(obj.offset);
    }
    if ( obj.hasOwnProperty('mouse') ) {
      cursor = document.getElementById('cursorImg');
      // カーソルを作成していなければ作成する
      if ( !cursor ) {
        $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mouse.x + '; left:' + obj.mouse.y + '; z-index: 1"><img width="50px" src="<?=C_PATH_NODE_FILE_SERVER?>/img/pointer.png"></div>');
        cursor = document.getElementById("cursorImg");
      }
      cursor.style.left = obj.mouse.x / windowScale + "px";
      cursor.style.top  = obj.mouse.y / windowScale + "px";
      return false;
    }
    if ( obj.hasOwnProperty('scale') ) {
      pdfjsApi.currentScale = obj.scale;
    }
    if ( obj.hasOwnProperty('page') ) {
      pdfjsApi.currentPage = obj.page;
      pdfjsApi.pageRender();
    }
    else {
      pdfjsApi.render();
    }
  });

  socket.on('docDisconnect', function(d){
    window.close();
    return false;
  });

  $("#manuscriptArea").draggable({
    scroll: false,
    cancel: "#document_canvas"
  })
  .css({
    'display': 'block',
    'width': "calc(100% - 150px)",
    'left': "125px",
    'top': "65px"
  });
};


// -->
</script>

<section id="document_share" ng-app="sincloApp" ng-controller="MainCtrl">

  <!-- /* サイドバー */ -->
  <ul id="document_share_tools">
    <li-top>
      <!-- <p>ID:1234</p> -->
      <!-- <li>
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_user.png" width="40" height="40" alt=""></span>
        <p>顧客情報</p>
      </li> -->
    </li-top>
    <li-bottom>
      <li ng-click="openDocumentList()">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_document.png" width="40" height="40" alt=""></span>
        <p>資料切り替え</p>
      </li>
      <li>
        <a id="downloadFilePath" href="">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_download.png" width="40" height="40" alt=""></span>
        <p>ダウンロード</p>
        </a>
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
      <li class="showDescriptionBottom" data-description="前のページへ" onclick="pdfjsApi.prevPage(); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_back.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="次のページへ" onclick="pdfjsApi.nextPage(); return false;">
        <span class="btn" ng-class="{{manuscriptType}}" ><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_next.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="原稿の表示/非表示" onclick="pdfjsApi.toggleManuScript(); return false;">
        <span id="scriptToggleBtn" class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_talkscript.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="ページ再描画" onclick="pdfjsApi.render(); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_reconnect.png" width="30" height="30" alt=""></span>
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
      <li class="showDescriptionBottom" data-description="拡大する" onclick="pdfjsApi.zoomIn(0.25); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_plus.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="縮小する" onclick="pdfjsApi.zoomOut(0.25); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_minus.png" width="30" height="30" alt=""></span>
      </li>
    </li-right>
  </ul>
  <!-- /* ツールバー */ -->
  <div id="manuscriptArea" style="display:none;">
    <span id="manuscript"></span>
    <span id="manuscriptCloseBtn" onclick="pdfjsApi.toggleManuScript(); return false;"></span>
  </div>

  <div id="tabStatusMessage">別の作業をしています</div>

  <div id="document_canvas"></div>
  <?php echo $this->element('Customers/documentLists') ?>

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
              <li ng-repeat="document in searchFunc(documentList)" ng-click="changeDocument(document)">
                <div class="document_image">
                  <img src="{{::document.thumnail}}" style="width:10em;height:7em">
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

  <div id="desc-balloon"></div>
</section>
