<?= $this->element('TDocuments/script'); ?>
<script type = text/javascript>
// 拡縮率をキー押下で変更できないようにする
var pdfjsCNST = function(){
  return {
    FIRST_PAGE: "最初のページ",
    LAST_PAGE: "最後のページ",
  };
};

var slideJsApi = {
  cnst: new pdfjsCNST(),
  filePath: "",
  currentPage: 1,
  currentScale: 1,
  loadedPage: 0,
  maxPage: 1,
  zoomInTimer: null,
  zoomInTimeTerm: 500,
  pagingTimer: null,
  pagingTimeTerm: 500,
  init: function(){
    this.cngPage();
    this.resetZoomType();// 拡大率を設定

    var canvas = document.getElementById('document_canvas');

    // マウス位置
    var mouseTimer = null;
    window.addEventListener('mousemove', function(e){
      if ( mouseTimer ) return false;
      mouseTimer = setTimeout(function(){
        mouseTimer = null;
      }, 15);
    });

    // キープレス
    window.addEventListener('keydown',function(e){
      if ( e.keyCode === 37 || e.keyCode === 38 ) {
        slideJsApi.prevPage();
      }
      else if ( e.keyCode === 39 || e.keyCode === 40 ) {
        slideJsApi.nextPage();
      }
    });

    // Ctrl + ホイール
    window.addEventListener('wheel', function(e){
      // 資料選択画面が表示されているときは無効化
      if ( document.getElementById('ang-popup').classList.item("show") !== null ) {
        return true;
      }
      if ( e.ctrlKey ) {
        e.preventDefault();
        clearTimeout(slideJsApi.zoomInTimer);
        // 拡大
        if ( e.deltaY < 0 ) {
          slideJsApi.zoomIn(0.1);
        }
        // 縮小
        else {
          slideJsApi.zoomOut(0.1);
        }
      }
      else {
        var canvas = document.querySelector('#slide_' + slideJsApi.currentPage);

        // 前のページへ
        if ( e.deltaY < 0 ) {
          if ( canvas.scrollTop !== 0 ) return false;
          if (e.preventDefault) { e.preventDefault(); }
          slideJsApi.prevPage();
        }
        // 次のページへ
        else {
          if ( (canvas.scrollHeight - canvas.clientHeight) !== canvas.scrollTop ) return false;
          if (e.preventDefault) { e.preventDefault(); }
          slideJsApi.nextPage();
        }
      }
    });

    // ウィンドウリサイズ
    var resizeTimer = null;
    /*window.addEventListener('resize', function(){
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function(){
        resizeTimer = null;
        var size = JSON.parse(sessionStorage.getItem('windowSize'));
        if ( size !== null && size.hasOwnProperty('width') && size.hasOwnProperty('height') && (size.width !== window.outerWidth || size.height !== window.outerHeight) ) {

          var winY = window.screenY, winX = window.screenX;
          if ((screen.availHeight-window.screenY - size.height) < 0) {
            winY = screen.availHeight - size.height;
          }
          if ((screen.availWidth-window.screenX - (size.width - 100)) < 0) {
            winX = screen.availWidth - (size.width - 100);
          }

          window.resizeTo(size.width, size.height);
          window.moveTo(winX, winY);
        }
        slideJsApi.pageRender();
        slideJsApi.render();
      }, 500);
    });*/

  },
  scrollTimer: null,
  setScrollTimer: null,
  setScrollFlg: false,
  scrollFunc: function(e){
    if ( slideJsApi.setScrollFlg ) return false;
    clearTimeout(this.scrollTimer);
    if ( document.getElementById('ang-popup').classList.item("show") === null ) {
      slideJsApi.scrollTimer = setTimeout(function(){
        clearTimeout(slideJsApi.scrollTimer);
        slideJsApi.scrollTimer = null;
        var page = document.getElementById("slide_" + slideJsApi.currentPage);
      }, 100);
    }
  },
  prevPage: function(){
    if ( slideJsApi.currentPage < 2 ) return slideJsApi.notificate('FIRST_PAGE');
    clearTimeout(this.pagingTimer);
    this.pagingTimer = setTimeout(function(){
      clearTimeout(slideJsApi.pagingTimer);
      slideJsApi.currentPage--;
      slideJsApi.sendCtrlAction('page');
      slideJsApi.cngPage();
    }, slideJsApi.pagingTimeTerm);
  },
  nextPage: function(){
    console.log('nextPage');
    if ( slideJsApi.currentPage >= slideJsApi.maxPage ) return slideJsApi.notificate('LAST_PAGE');
    clearTimeout(this.pagingTimer);
    this.pagingTimer = setTimeout(function(){
      clearTimeout(slideJsApi.pagingTimer);
      slideJsApi.currentPage++;
      slideJsApi.sendCtrlAction('page');
      slideJsApi.cngPage();
    }, slideJsApi.pagingTimeTerm);
  },
  toggleManuScript: function(){
    console.log('これはなんだ');
    var type = sessionStorage.getItem('manuscript');
    if ( type === "none" ) {
      type = 'block';
      document.getElementById('scriptToggleBtn').classList.add('on');
      if ( slideJsApi.manuscript.hasOwnProperty(Number(slideJsApi.currentPage)) && slideJsApi.manuscript[slideJsApi.currentPage] !== "" ) {
      }
    }
    else {
      type = 'none';
      document.getElementById('scriptToggleBtn').classList.remove('on');
    }
    $("#manuscriptArea").css({ 'display': type });
    sessionStorage.setItem('manuscript', type);
  },
  cngPage: function(){
    var script = "", type = sessionStorage.getItem('manuscript');
    console.log('ああああああああああああ');
    console.log(slideJsApi.manuscript);
    if ( type === "block" && slideJsApi.manuscript.hasOwnProperty(Number(slideJsApi.currentPage)) && slideJsApi.manuscript[slideJsApi.currentPage] !== "" ) {
      $("#manuscriptArea").css({ 'display': type });
      document.getElementById('scriptToggleBtn').classList.add('on');
    }
    else {
      $("#manuscriptArea").css({'display': 'none'});
    }
    document.getElementById('manuscript').textContent = slideJsApi.manuscript[slideJsApi.currentPage];
    slideJsApi.readPage();
    slideJsApi.pageRender();
  },
  cngScaleTimer: null,
  cngScale: function(){
    clearTimeout(slideJsApi.cngScaleTimer);
    slideJsApi.cngScaleTimer = setTimeout(function(){
      clearTimeout(slideJsApi.cngScaleTimer);
      var type = document.getElementById('scaleType').value;
      if ( type && !isNaN(Number(type)) ) {
        slideJsApi.zoom(type);
      }
    }, slideJsApi.zoomInTimeTerm);
  },
  zoom: function(num){
    clearTimeout(this.zoomInTimer);
    this.zoomInTimer = setTimeout(function(){
      clearTimeout(slideJsApi.zoomInTimer);
      slideJsApi.currentScale = num;
      slideJsApi.render();
      slideJsApi.sendCtrlAction('scale');
    }, slideJsApi.zoomInTimeTerm);
  },
  zoomIn: function(num){
    if ( slideJsApi.currentScale >= 4 ) return false;

    clearTimeout(this.zoomInTimer);
    this.zoomInTimer = setTimeout(function(){
      clearTimeout(slideJsApi.zoomInTimer);
        slideJsApi.currentScale = Math.ceil( (Number(slideJsApi.currentScale) + Number(num)) * 100 ) / 100;
      if ( slideJsApi.currentScale > 4 ) {
        slideJsApi.currentScale = 4;
      }
      slideJsApi.sendCtrlAction('scale');
      slideJsApi.render();
      slideJsApi.resetZoomType();
    }, slideJsApi.zoomInTimeTerm);
  },
  zoomOut: function(num){
    console.log('zoomout');
    if ( slideJsApi.currentScale <= 0 ) return false;

    clearTimeout(this.zoomInTimer);
    this.zoomInTimer = setTimeout(function(){
      clearTimeout(slideJsApi.zoomInTimer);
        slideJsApi.currentScale = Math.ceil( (Number(slideJsApi.currentScale) - Number(num)) * 100 ) / 100;
      if ( slideJsApi.currentScale <= num ) {
        slideJsApi.currentScale = num;
      }
      slideJsApi.sendCtrlAction('scale');
      slideJsApi.render();
      slideJsApi.resetZoomType();
    }, slideJsApi.zoomInTimeTerm);
  },
  resetZoomType: function(){
    console.log('zoom');
    var scaleType = document.getElementById('scaleType');
    for (var i = 0; i < scaleType.children.length; i++) {
      scaleType[i].selected = false;
    }
    if ( document.querySelector("#scaleType option[value='" + Number(slideJsApi.currentScale) + "']") ) {
      document.querySelector("#scaleType option[value='" + Number(slideJsApi.currentScale) + "']").selected = true;
    }
    else {
      scaleType[0].selected = true;
    }
  },
  sendCtrlAction: function(key){
    var data = {to: 'customer'};
    data[key] = ( key === "page" ) ? slideJsApi.currentPage : slideJsApi.currentScale ;
    sessionStorage.setItem(key, data[key]); // セッションに格納
  },
  setWindowSize: function(wsInfo){
    console.log('popupsize');
    var cal = 1; // 縮尺

    var frame = {width:null, height:null}; // iframeサイズ
    // 描画有効サイズ
    var enableScreen = {width:(screen.availWidth - frameSize.width), height:(screen.availHeight - frameSize.height)};
    var ratio = {
      w: wsInfo.width / enableScreen.width,
      h: wsInfo.height / enableScreen.height
    };
    if ( ratio.w > 1 || ratio.h > 1 ) {
      if (ratio.w > ratio.h) {
        cal = Math.ceil((enableScreen.width / wsInfo.width)*100)/100;
      }
      else {
        cal = Math.ceil((enableScreen.height / wsInfo.height)*100)/100;
      }
      frame.height = wsInfo.height * cal;
      frame.width = wsInfo.width * cal;
    }
    else {
      frame = wsInfo;
    }

    var wswidth = frame.width + frameSize.width;
    var wsheight = frame.height + frameSize.height;

    var winY = window.screenY, winX = window.screenX;
    if ((screen.availHeight-window.screenY - wsheight) < 0) {
      winY = screen.availHeight - wsheight;
    }
    if ((screen.availWidth-window.screenX - wswidth) < 0) {
      winX = screen.availWidth - wswidth;
    }

    try {
      windowSize = {'width': wswidth, 'height': wsheight};
      sessionStorage.setItem('windowSize', JSON.stringify(windowSize));
      var scale = wsInfo.width/frame.width;
      sessionStorage.setItem('windowScale', scale);
      windowScale = scale;

      window.moveTo(winX, winY);
      window.resizeTo(wswidth, wsheight);
      slideJsApi.render();
      slideJsApi.pageRender();
    }
    catch(e) {
      console.log("error resize.", e);
    }
  },
  pageRender: function(){
    console.log('pageRender');
    slideJsApi.scrollTimer = null;
    var canvas = document.getElementById('document_canvas');
    var frameWidth = $("slideFrame").prop('offsetWidth');
    if ( isNumber(frameWidth) ) {
      canvas.style.left = -frameWidth * (slideJsApi.currentPage - 1) + "px";
    }
    sessionStorage.setItem('page', slideJsApi.currentPage); // セッションに格納
    $('#pages').text(slideJsApi.currentPage + "/ " + slideJsApi.maxPage);
  },
  render: function(){
    console.log('render');
    var canvas = document.querySelector('slideframe');
    console.log('画像');
    console.log(canvas);
    var frameWidth = $("slideFrame").prop('clientWidth');
    var frameHeight = $("slideFrame").prop('clientHeight');
    /* サイズ調整処理 */
    $(".slide img").css("width", (canvas.clientWidth - 20) * 0.75 + "pt")
                   .css("height", (canvas.clientHeight - 20) * 0.6 + "pt");
    $(".slide").css("width",  canvas.clientWidth + "px").css("height", canvas.clientHeight + "px");
    $(".slide img").css("zoom", slideJsApi.currentScale);
  },
  renderTimer: null,
  notificate: function(code){
    if ( this.cnst.hasOwnProperty(code) ) {
      console.log(this.cnst[code]);
    }
  },
  makePage: function(){
    console.log('makePage');
    var docCanvas = document.getElementById('document_canvas');
    console.log('docCanvas');
    console.log(docCanvas);
    // 現在の表示ページから作っていく
    for(var i = 1; this.maxPage >= i; i++){
      var slide = document.createElement('div');
      slide.id = "slide_" + i;
      slide.classList.add("slide");
      slide.addEventListener('scroll', function(){
        slideJsApi.scrollFunc();
      });
      docCanvas.appendChild(slide);
      console.log('ここここ');

    }
    slideJsApi.render();
  },
  readPage: function(){
    console.log('readPage');
    function setImage(page){
      var img = document.createElement('img');
      img.src = slideJsApi.filePath + "_" + Number(page) + '.svg';
      var slide = document.getElementById('slide_' + page);

      slide.appendChild(img);
    }

    // 初回のページ読み込みで、表示ページが１ページ目以上の場合
    if ( this.loadedPage === 0 && this.currentPage > 1 ) {
      var prevNode = null;
      setImage(this.currentPage);

      // 現在の表示ページから作っていく
      for(var i = this.currentPage - 1; i > 0; i--){
        setImage(i);
      }
      this.loadedPage = this.currentPage;
    }
    else {
      this.loadedPage++;

      if ( !document.querySelector('#slide_' + this.loadedPage) || document.querySelector('#slide_' + this.loadedPage + ' img') ) return false;
      setImage(this.loadedPage); // ページを追加
    }
    slideJsApi.render();

  },
  readFile: function(doc, callback){
    console.log('readFile');
    this.filePath = "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/svg_"?>" + doc.file_name.replace(/\.pdf$/, "");
    console.log(this.filePath);
    sessionStorage.setItem('doc', JSON.stringify(doc));
    this.doc = doc;
    console.log(doc);
    // ダウンロードファイルの設定
    //document.getElementById('downloadFilePath').href = "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>" + doc.file_name;
    this.currentPage = (sessionStorage.getItem('page') !== null) ? Number(sessionStorage.getItem('page')) : 1;
    this.currentScale = (sessionStorage.getItem('scale') !== null) ? Number(sessionStorage.getItem('scale')) : 1;
    if ( sessionStorage.getItem('manuscript') === null ) { sessionStorage.setItem('manuscript', 'block') }
    this.manuscript = JSON.parse(doc.manuscript);
    this.loadedPage = 0;
    var settings = JSON.parse(doc.settings);
    this.maxPage = settings.pages;

    var limitPage = (this.currentPage + 3 > this.maxPage) ? this.maxPage : this.currentPage + 3 ;

    var divCanvas = document.createElement("div");
    divCanvas.id = "document_canvas";
    $("slideframe #document_canvas").remove();
    $("slideframe").append(divCanvas);
    this.makePage(); // 初期スライドを作成
    //this.init();

    var readPageTimer = setInterval(function(){
      slideJsApi.readPage();
      if ( limitPage < slideJsApi.loadedPage ) {
        clearInterval(readPageTimer);
        slideJsApi.pageRender();
        slideJsApi.render();
        callback(false);
      }
    }, 1000);
  }
};

/*var doc = <?=json_encode($docData['TDocument'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_FORCE_OBJECT )?>;
console.log(doc)
var firstFlg = true;
if ( sessionStorage.getItem("doc") !== null ) {
  firstFlg = false;
  doc = JSON.parse(sessionStorage.getItem("doc"));
}

slideJsApi.readFile(doc, function(err){
  if (err) return false;
  if (firstFlg) {
    var settings = JSON.parse(doc.settings);
  }
});*/

$(document).on("keydown", "#scaleType", function(e){ return false; });
var sincloApp = angular.module('sincloApp', []);
sincloApp.controller('MainCtrl', function($scope){
  $scope.documentList = [];
  $scope.tagList = {};
  $scope.searchName = "";
  $scope.selectList = {};
  $scope.searchFunc = function(documentList){
    var targetTagNum = Object.keys($scope.selectList).length;

    function check(elem, index, array){
      var flg = true;
      if ( elem.tag !== "" && elem.tag !== null ) {
        elem.tags = $scope.jParse(elem.tag);
      }
      if ( $scope.searchName === "" && targetTagNum === 0 ) {
        return elem;
      }

      if ( $scope.searchName !== "" && (elem.name + elem.overview).indexOf($scope.searchName) < 0 ) {
        flg = false;
      }

      if ( flg && targetTagNum > 0 ) {
        var selectList = Object.keys($scope.selectList);
        flg = true;
        for ( var i = 0; selectList.length > i; i++ ) {
          if ( elem.tags.indexOf(Number(selectList[i])) === -1 ) {
            flg = false;
          }
        }
      }

      return ( flg ) ? elem : false;

    }

    return documentList.filter(check);
  };

  /**
   * openDocumentList
   *  ドキュメントリストの取得
   * @return void(0)
   */
  $scope.openDocumentList3 = function(id) {
    console.log('aaaa');
    $.ajax({
      type: 'post',
      data: {
        id:id
      },
      url: '<?=$this->Html->url(["controller" => "TDocuments", "action" => "remoteOpenDocumentLists"])?>',
      dataType: 'json',
      success: function(json) {
        console.log('json');
        console.log(id);
        console.log(json);
        console.log(JSON.parse(json.documentList));
        console.log(JSON.parse(json.documentList)[0]['file_name']);
        console.log(JSON.parse(json.documentList)[0]['settings']);
        doc = JSON.parse(json.documentList)[0];
        $("#ang-popup3").addClass("show");
        $scope.searchName = "";
        var contHeight = $('#ang-popup-content3').height();
        $('#ang-popup-frame3').css('height', contHeight);
        $scope.tagList = ( json.hasOwnProperty('tagList') ) ? JSON.parse(json.tagList) : {};
        $scope.documentList = ( json.hasOwnProperty('documentList') ) ? JSON.parse(json.documentList) : {};
        $scope.$apply();
        slideJsApi.readFile(doc, function(err) {
          if (err) return false;
          var settings = JSON.parse(doc.settings);
        });
      }
    });
  };

  $scope.openDocumentList2 = function() {
    console.log('NO.2');
    $.ajax({
      type: 'GET',
      url: '<?=$this->Html->url(["controller" => "Customers", "action" => "remoteOpenDocumentLists"])?>',
      dataType: 'json',
      success: function(json) {
        console.log('ワッハッハ');
        doc = JSON.parse(json.documentList)[0];
        $("#ang-popup2").addClass("show");
        $scope.searchName = "";
        var contHeight = $('#ang-popup-content2').height();
        $('#ang-popup-frame2').css('height', contHeight);
        $scope.tagList = ( json.hasOwnProperty('tagList') ) ? JSON.parse(json.tagList) : {};
        $scope.documentList = ( json.hasOwnProperty('documentList') ) ? JSON.parse(json.documentList) : {};
        $scope.$apply();
      }
    });
  };
  /**
   * [shareDocument description]
   * @param  {object} doc documentInfo
   * @return {void}     open new Window.
   */
  $scope.shareDocument = function(doc) {
    var targetTabId = tabId.replace("_frame", "");
    sessionStorage.removeItem('doc');
    window.open(
      "<?= $this->Html->url(['controller' => 'Customers', 'action' => 'docFrame']) ?>?tabInfo=" + encodeURIComponent(targetTabId) + "&docId=" + doc.id,
      "doc_monitor_" + targetTabId,
      "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
    );
    $scope.closeDocumentList();
  };

  /**
   * [changeDocument description]
   * @param  {object} doc document's info
   * @return {void}     send new docURL
   */
    $scope.changeDocument = function(doc){
    console.log('change');
    console.log(doc);
    sessionStorage.setItem('page', 1);
    sessionStorage.setItem('scale', 1);
    slideJsApi.readFile(doc, function(err) {
      if (err) return false;
      var settings = JSON.parse(doc.settings);
    });
    $scope.closeDocumentList2();
  };

  $scope.closeDocumentList = function() {
    $("#ang-popup3").removeClass("show");
  };


  $scope.closeDocumentList2 = function() {
    $("#ang-popup2").removeClass("show");
  };

  /*angular.element(document).on("click", function(evt){
    if ( evt.target.getAttribute('data-elem-type') !== 'selector' ) {
      var e = document.querySelector('ng-multi-selector');
      if ( e.classList.contains('show') ) {
        e.classList.remove('show');
      }
    }
  });*/


});

</script>
<?php
  $params = $this->Paginator->params();
  $prevCnt = ($params['page'] - 1);
?>
<section id="document_share" ng-app="sincloApp" ng-controller="MainCtrl">
<div id='tdocument_idx' class="card-shadow">
  <div id='tdocument_add_title'>
    <div class="fLeft"><?= $this->Html->image('document_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>資料設定<span id="sortMessage"></span></h1>
  </div>

  <div id='tdocument_menu' class="p20trl">
    <div class="fLeft" >
      <?= $this->Html->image('add.png', ['url' => ['controller'=>'TDocuments', 'action' => 'add'], 'alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30]) ?>
    </div>
  </div>

  <div id='tdocument_list' class="p20x">

    <table>
      <thead>
        <tr>
          <th width="5%">No</th>
          <th width="15%">資料</th>
          <th width="25%">資料名</th>
          <th width="40%">概要</th>
          <th width="15%">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php
          foreach((array)$documentList as $key => $val):
          $id = "";
          if ($val['TDocument']['id']) {
            $id = $val['TDocument']['id'];
          }
          $no = $prevCnt + h($key+1);
        ?>
        <tr data-id="<?=h($id)?>">
          <td class="tCenter"><?=$no?></td>
          <td class="tCenter">
            <div class = "document_image">
              <?= $this->Html->image(C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/".C_PREFIX_DOCUMENT.pathinfo(h($val['TDocument']['file_name']), PATHINFO_FILENAME).".jpg", ["width" => 210, "height" => 180,"ng-click"=>"openDocumentList3($id)"]);?>
            </div>
          </td>
          <td class="tCenter"><?=h($val['TDocument']['name'])?></td>
          <td class="tCenter"><?=h($val['TDocument']['overview'])?></td>
          <!-- <td class="tCenter"><span><?=implode("</span>、<span>",$val['TDocument']['tag'])?></span></td> -->
          <td class="p10x noClick lineCtrl">
            <div>
              <a href="<?=$this->Html->url(['controller'=>'TDocuments', 'action'=>'edit', $id])?>" class="btn-shadow greenBtn fLeft"><img src="/img/edit.png" alt="更新" width="30" height="30"></a>
              <a href="javascript:void(0)" class="btn-shadow redBtn m10r10l fRight" onclick="removeAct('<?=$id?>')"><img src="/img/trash.png" alt="削除" width="30" height="30"></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if ( count($documentList) === 0 ) :?>
          <td class="tCenter" colspan="5">保存された資料がありません</td>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

  <div id="ang-popup3">
    <div id="ang-base3">
      <div id="ang-popup-background3"></div>
      <div id="ang-popup-frame3">
        <div id="ang-popup-content3" class="document_list">
            <!-- /* サイドバー */ -->
  <ul id="document_share_tools">
    <li-bottom2>
      <li ng-click="openDocumentList2()">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_document.png" width="40" height="40" alt=""></span>
        <p>資料切り替え</p>
      </li>
      <li ng-click="closeDocumentList()">
        <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_disconnect.png" width="40" height="40" alt=""></span>
        <p>閉じる</p>
      </li>
    </li-bottom2>
  </ul>
  <!-- /* サイドバー */ -->
  <!-- /* ツールバー */ -->
  <ul id="document_ctrl_tools">
    <li-left>
      <li class="showDescriptionBottom" data-description="前のページへ" onclick="slideJsApi.prevPage(); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_back.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="次のページへ" onclick="slideJsApi.nextPage(); return false;">
        <span class="btn" ng-class="{{manuscriptType}}" ><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_next.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="原稿の表示/非表示" onclick="slideJsApi.toggleManuScript(); return false;">
        <span id="scriptToggleBtn" class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_talkscript.png" width="30" height="30" alt=""></span>
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
        <select name="scale_type" id="scaleType" onchange="slideJsApi.cngScale(); return false;">
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
      <li class="showDescriptionBottom" data-description="拡大する" onclick="slideJsApi.zoomIn(0.25); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_plus.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="縮小する" onclick="slideJsApi.zoomOut(0.25); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_minus.png" width="30" height="30" alt=""></span>
      </li>
    </li-right>
  </ul>

  <ul>
    <li-bottom3>
      <slideFrame>
        <div id="document_canvas"></div>
        </slideFrame>
    </li-bottom3>
  </ul>

  <!-- /* ツールバー */ -->
  <div id="manuscriptArea" style="display:none;">
    <span id="manuscript"></span>
    <span id="manuscriptCloseBtn" onclick="slideJsApi.toggleManuScript(); return false;"></span>
  </div>

<div id="ang-popup2">
    <div id="ang-base2">
      <div id="ang-popup-background2"></div>
      <div id="ang-popup-frame2">
        <div id="ang-popup-content2" class="document_list">
          <div id="title_area2">資料一覧</div>
          <div id="search_area2">
            <?=$this->Form->input('name', ['label' => 'フィルター：', 'ng-model' => 'searchName']);?>
            <!-- <ng-multi-selector></ng-multi-selector> -->
          </div>
          <div id="list_area2">
            <ol>
              <li ng-repeat="document in searchFunc(documentList)" ng-click="changeDocument(document)">
                <div class="document_image">
                  <img ng-src="{{::document.thumnail}}" style="width:5em;height:4em">
                </div>
                <div class="document_content">
                  <h3>{{::document.name}}</h3>
                  <ng-over-view docid="{{::document.id}}" text="{{::document.overview}}" ></ng-over-view>
                  <ul><li ng-repeat="tagId in document.tags">{{::tagList[tagId]}}</li></ul>
                </div>
              </li>
            </ol>
          </div>
          <div id="btn_area2">
            <a class="btn-shadow greenBtn" ng-click="closeDocumentList2()" href="javascript:void(0)">閉じる</a>
          </div>
        </div>
      </div>
      <div id="ang-ballons2">
      </div>
    </div>
  </div>

  </section>