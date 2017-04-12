<script type="text/javascript">
<?= $this->element('TDocuments/loadScreen'); ?>
<?php if ( $this->action !== "index" ) : ?>
function handleFileSelect(evt) {
  $("slideframe").html('<div id="document_canvas"></div>');
  var files = evt.target.files; // FileList object
  if (files.length === 0) {
    if ( slideJsApi.filePath !== "" ) slideJsApi.init(slideJsApi.filePath, slideJsApi.maxPage);
    return false;
  }

  var file = files[0];
  if (file.name.indexOf('.pdf') < 0) {
    return false;
  }
  var reader = new FileReader();
  reader.onload = (function(theFile) {
    return function(e) {
      var slideTemp = document.createElement('div');
      slideTemp.id = "slide_temp";
      slideTemp.classList.add('slide');
      var slideSample = document.createElement('div');
      var fileTitle = document.createElement('span');
      fileTitle.textContent = file.name;
      slideSample.appendChild(fileTitle);
      slideTemp.appendChild(slideSample);
      var target = document.getElementById('document_canvas');
      target.appendChild(slideTemp);
    };
  })(file);
  // Read in the image file as a data URL.
  reader.readAsDataURL(file);
}

if (window.File && window.FileReader && window.FileList && window.Blob) {
  $(document).on('change', '#TDocumentFiles', function(e){
    handleFileSelect(e);
  })
}

var onBeforeunloadHandler = function(e) {
  e.returnValue = 'まだ保存されておりません。離脱してもよろしいでしょうか';
};

$(document).ready(function(){
  // イベントを登録
  window.addEventListener('beforeunload', onBeforeunloadHandler, false);
});

<?php endif; ?>


//タグ追加
function tagAdd(){
  var tag = $('#TDocumentNewTag').val();
  $('#MDocumentTagName').val(tag);
  document.getElementById('MDocumentTagAddForm').submit();
}

//保存機能
function saveAct(){
  loading.load.start();
  // ページ離脱防止解除
  window.removeEventListener('beforeunload', onBeforeunloadHandler, false);

  if ( slideJsApi.hasOwnProperty('manuscript') ) {
    document.getElementById('TDocumentManuscript').value = JSON.stringify(slideJsApi.manuscript);
  }
  document.getElementById('TDocumentEntryForm').submit();
  setTimeout(function(){
    $("a").addClass("disableBtn").prop("onclick", "").click(
      function(e){
        e.preventDefault();e.stopImmediatePropagation();return false;
      }
    );
  }, 100);
}

//一覧画面削除機能
function removeAct(id){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', '資料設定', 'moment');
  popupEvent.closePopup = function(){
    loading.load.start();
    $.ajax({
      type: 'post',
      data: {
        id:id
      },
      cache: false,
      url: "<?= $this->Html->url('/TDocuments/remoteDelete') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TDocuments/index') ?>";
      }
    });
  };
}

//登録・更新画面削除機能
function removeActEdit(){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', '資料設定', 'moment');
  popupEvent.closePopup = function(){
    loading.load.start();
    $.ajax({
      type: 'post',
      data: {
        id:document.getElementById('TDocumentId').value
      },
      cache: false,
      url: "<?= $this->Html->url('/TDocuments/remoteDelete') ?>",
      success: function(){
        // ページ離脱防止解除
        window.removeEventListener('beforeunload', onBeforeunloadHandler, false);
        location.href = "<?= $this->Html->url('/TDocuments/index') ?>";
      }
    });
  };
}

//タグリスト表示
$(function(){
  $('#tagList').multiSelect({});
});

<?php
$manuscript = (!empty($this->data['TDocument']['manuscript'])) ? $this->data['TDocument']['manuscript'] : '{}';
?>

var slideJsApi,slideJsApi2,slideJsCNST;

(function(){
  slideJsCNST = function(){
    return {
      FIRST_PAGE: "最初のページ",
      LAST_PAGE: "最後のページ",
    };
  };

  slideJsApi = {
    cnst: new slideJsCNST(),
    filePath: "",
    currentPage: 1,
    loadedPage: 0,
    maxPage: 1,
    rotation: 0,
    manuscript: <?=$manuscript?>,
    init: function(filePath, page, rotation){
      this.currentPage = 1;
      this.loadedPage = 0;
      this.maxPage = page;
      this.rotation = rotation;
      this.filePath = filePath;
      this.makePage(); // 初期スライドを作成
      var limitPage = (this.currentPage + 3 > this.maxPage) ? this.maxPage : this.currentPage + 3 ;

      var canvas = document.getElementById('document_canvas');
      var readPageTimer = setInterval(function(){
        slideJsApi.readPage();
        if ( limitPage < slideJsApi.loadedPage ) {
          clearInterval(readPageTimer);
        }
      }, 1000);
      slideJsApi.showPage();

      // 原稿
      var textarea = document.getElementById('pages-text');
      if ( this.currentPage in this.manuscript ) {
        textarea.value = this.manuscript[this.currentPage];
      }

      textarea.addEventListener('blur',function(e){
        if ( slideJsApi.manuscript.hasOwnProperty(slideJsApi.currentPage) && slideJsApi.manuscript[slideJsApi.currentPage] === e.target.value ) {
          return false; // 変わっていない
        }
        if ( !slideJsApi.manuscript.hasOwnProperty(slideJsApi.currentPage) && e.target.value === "" ) {
          return false; // 書いていない
        }
        slideJsApi.manuscript[slideJsApi.currentPage] = e.target.value;
      });


    },
    setManuscript: function(){
      document.getElementById('pages-text').value = ( this.manuscript.hasOwnProperty(this.currentPage) ) ? this.manuscript[this.currentPage] : "";
    },
    prevPage: function(){
      if ( this.currentPage < 2 ) return this.notificate('FIRST_PAGE');
      this.currentPage--;
      this.showPage();
    },
    nextPage: function(){
      if ( this.currentPage >= this.maxPage ) return this.notificate('LAST_PAGE');
      this.currentPage++;
      this.showPage();
      this.readPage();
    },
    rotate: function(){
      setTimeout(function(){
        slideJsApi.rotation = Number(slideJsApi.rotation) + 90;
        if ( slideJsApi.rotation === 360 ) {
          slideJsApi.rotation = 0;
        }
        for ( var i = 1; i <= slideJsApi.loadedPage; i++ ) {
          slideJsApi.renderPage(i);
        }
      }, 0);
    },
    showPage: function(){
      var canvas = document.getElementById('document_canvas');
      canvas.style.left = -22.5 * (slideJsApi.currentPage - 1) + "em";
      this.setManuscript();
      $('.pages').text("（" + slideJsApi.currentPage + "/ " + slideJsApi.maxPage + "）");
    },
    render: function(){
      var canvas = document.querySelector('slideframe');
       /* サイズ調整処理 */
      $(".slide").css("width",  canvas.clientWidth + "px").css("height", canvas.clientHeight + "px");
      $(".slide img").css("transform", "scale(" + slideJsApi.currentScale + ")");
      var docCanvas = document.getElementById('document_canvas');
      docCanvas.style.width = this.maxPage * canvas.clientWidth + "px";
    },
    renderPage: function(page){
      var canvas = document.querySelector('slideframe'),
          pageImg = document.querySelector("#slide_" + page + " img"),
          wScale = 0, hScale = 0, scale = 0, pWidth = 0, pHeight = 0,
          cWidth = canvas.clientWidth,
          cHeight = canvas.clientHeight,
          matrix = "matrix( 1, 0, 0, 1, 0, 0)";

      switch (Number(this.rotation)) {
        case 90:
           matrix = "matrix( 0, 1, -1, 0, 0, 0)";
           break;
        case 180:
           matrix = "matrix(1, 0, 0, -1, 0, 0)";
           break;
        case 270:
           matrix = "matrix( 0, -1, 1, 0, 0, 0)";
           break;
      }

      if ( typeof pageImg.naturalWidth !== 'undefined' ) {
        pWidth = pageImg.naturalWidth;
        pHeight = pageImg.naturalHeight;
      }
      if ( typeof pageImg.runtimeStyle !== 'undefined' ) {
        pageImg.style.opacity = 0;
        pageImg.style.width  = "auto";
        pageImg.style.height = "auto";
        setTimeout(function(){
          pWidth = pageImg.clientWidth;
          pHeight = pageImg.clientHeight;
          pageImg.style.opacity = 1;
        }, 10);
      }

      setTimeout(function(){
        wScale = cWidth/pWidth;
        hScale = cHeight/pHeight;
        if ( Number(slideJsApi.rotation) === 90 || Number(slideJsApi.rotation) === 270 ) {
          wScale = cHeight/pWidth;
          hScale = cWidth/pHeight;
        }

        scale = ( wScale < hScale ) ? wScale : hScale;
        pageImg.style.width = pWidth * scale + "px";
        pageImg.style.height = pHeight * scale + "px";
        pageImg.style.transform = matrix;
      }, 10);
    },
    makePage: function(){
      var docCanvas = document.getElementById('document_canvas');
      // 現在の表示ページから作っていく
      for(var i = 1; this.maxPage >= i; i++){
        var slide = document.createElement('div');
        slide.id = "slide_" + i;
        slide.classList.add("slide");
        slide.addEventListener('scroll', function(){
          slideJsApi.scrollFunc();
        });
        docCanvas.appendChild(slide);
      }
      slideJsApi.render();
    },
    readPage: function(){
      function setImage(page){
        var img = document.createElement('img');
        img.src = slideJsApi.filePath + "_" + Number(page) + '.svg';
        var slide = document.getElementById('slide_' + page);

        slide.appendChild(img);
        img.onload = function(){
          slideJsApi.renderPage(page);
        }
      }

      this.loadedPage++;

      if ( !document.querySelector('#slide_' + this.loadedPage) || document.querySelector('#slide_' + this.loadedPage + ' img') ) return false;
      setImage(this.loadedPage); // ページを追加
      slideJsApi.render();
    },
    notificate: function(code){
      if ( this.cnst.hasOwnProperty(code) ) {
        console.log(this.cnst[code]);
      }
    }
  },
  slideJsApi2 = {
    cnst: new slideJsCNST(),
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

      var canvas = document.getElementById('document_canvas2');

      var mousewheelevent = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';
      $(document).on(mousewheelevent,function(e){
        var num = parseInt($('.wheel').text());
        e.preventDefault();
        var delta = e.originalEvent.deltaY ? -(e.originalEvent.deltaY) : e.originalEvent.wheelDelta ? e.originalEvent.wheelDelta : -(e.originalEvent.detail);

        if ( e.ctrlKey ) {
          e.preventDefault();
          clearTimeout(slideJsApi2.zoomInTimer);
          // 拡大
          if ( delta < 0 ) {
            slideJsApi2.zoomIn(0.1);
          }
          // 縮小
          else {
            slideJsApi2.zoomOut(0.1);
          }
        }
        else {
          var canvas = document.querySelector('#slide2_' + slideJsApi2.currentPage);

          // 前のページへ
          if (delta < 0 ) {
            if ( canvas.scrollTop !== 0 ) return false;
            if (e.preventDefault) { e.preventDefault(); }
            slideJsApi2.prevPage();
          }
          // 次のページへ
          else {
            if (e.preventDefault) { e.preventDefault(); }
            slideJsApi2.nextPage();
          }
        }
      });

      // キープレス
      $(window).keyup(function(e){
        if ( e.keyCode === 37 || e.keyCode === 38 ) {
          slideJsApi2.nextPage();
        }
        else if ( e.keyCode === 39 || e.keyCode === 40 ) {
          slideJsApi2.prevPage();
        }
      });
    },
    scrollTimer: null,
    setScrollTimer: null,
    setScrollFlg: false,
    scrollFunc: function(e){
      if ( slideJsApi2.setScrollFlg ) return false;
      clearTimeout(this.scrollTimer);
      if ( document.getElementById('ang-popup3').classList.item("show") === null ) {
        slideJsApi2.scrollTimer = setTimeout(function(){
          clearTimeout(slideJsApi2.scrollTimer);
          slideJsApi2.scrollTimer = null;
          var page = document.getElementById("slide2_" + slideJsApi2.currentPage);
        }, 100);
      }
    },
    prevPage: function(){
      if ( slideJsApi2.currentPage < 2 ) return slideJsApi2.notificate('FIRST_PAGE');
      clearTimeout(this.pagingTimer);
      this.pagingTimer = setTimeout(function(){
        clearTimeout(slideJsApi2.pagingTimer);
        slideJsApi2.currentPage--;
        slideJsApi2.sendCtrlAction('page');
        slideJsApi2.cngPage();
      }, slideJsApi2.pagingTimeTerm);
    },
    nextPage: function(){
      if ( slideJsApi2.currentPage >= slideJsApi2.maxPage ) return slideJsApi2.notificate('LAST_PAGE');
      clearTimeout(this.pagingTimer);
      this.pagingTimer = setTimeout(function(){
        clearTimeout(slideJsApi2.pagingTimer);
        slideJsApi2.currentPage++;
        slideJsApi2.sendCtrlAction('page');
        slideJsApi2.cngPage();
      }, slideJsApi2.pagingTimeTerm);
    },
    toggleManuScript: function(){
      var type = sessionStorage.getItem('manuscript');
      if ( type === "none" ) {
        type = 'block';
        document.getElementById('scriptToggleBtn').classList.add('on');
        if ( slideJsApi2.manuscript.hasOwnProperty(Number(slideJsApi2.currentPage)) && slideJsApi2.manuscript[slideJsApi2.currentPage] !== "" ) {
        }
        $("#manuscriptArea").css({ 'display': type });
      }
      else {
        type = 'none';
        document.getElementById('scriptToggleBtn').classList.remove('on');
        $("#manuscriptArea").css({ 'display': type });
      }
      sessionStorage.setItem('manuscript', type);
    },
    cngPage: function(){
      var script = "", type = sessionStorage.getItem('manuscript');
      if ( type === "block" && slideJsApi2.manuscript.hasOwnProperty(Number(slideJsApi2.currentPage)) && slideJsApi2.manuscript[slideJsApi2.currentPage] !== "" ) {
        $("#manuscriptArea").css({ 'display': type });
        document.getElementById('scriptToggleBtn').classList.add('on');
      }
      else {
        $("#manuscriptArea").css({'display': 'none'});
      }
      document.getElementById('manuscript').textContent = slideJsApi2.manuscript[slideJsApi2.currentPage];
      slideJsApi2.readPage();
      slideJsApi2.pageRender();
    },
    cngScaleTimer: null,
    cngScale: function(){
      clearTimeout(slideJsApi2.cngScaleTimer);
      slideJsApi2.cngScaleTimer = setTimeout(function(){
        clearTimeout(slideJsApi2.cngScaleTimer);
        var type = document.getElementById('scaleType').value;
        if ( type && !isNaN(Number(type)) ) {
          slideJsApi2.zoom(type);
        }
      }, slideJsApi2.zoomInTimeTerm);
    },
    zoom: function(num){
      clearTimeout(this.zoomInTimer);
      this.zoomInTimer = setTimeout(function(){
        clearTimeout(slideJsApi2.zoomInTimer);
        slideJsApi2.currentScale = num;
        slideJsApi2.render();
        slideJsApi2.sendCtrlAction('scale');
      }, slideJsApi2.zoomInTimeTerm);
    },
    zoomIn: function(num){
      if ( slideJsApi2.currentScale >= 4 ) return false;

      clearTimeout(this.zoomInTimer);
      this.zoomInTimer = setTimeout(function(){
        clearTimeout(slideJsApi2.zoomInTimer);
          slideJsApi2.currentScale = Math.ceil( (Number(slideJsApi2.currentScale) + Number(num)) * 100 ) / 100;
        if ( slideJsApi2.currentScale > 4 ) {
          slideJsApi2.currentScale = 4;
        }
        slideJsApi2.sendCtrlAction('scale');
        slideJsApi2.render();
        slideJsApi2.resetZoomType();
      }, slideJsApi2.zoomInTimeTerm);
    },
    zoomOut: function(num){
      if ( slideJsApi2.currentScale <= 0 ) return false;

      clearTimeout(this.zoomInTimer);
      this.zoomInTimer = setTimeout(function(){
        clearTimeout(slideJsApi2.zoomInTimer);
          slideJsApi2.currentScale = Math.ceil( (Number(slideJsApi2.currentScale) - Number(num)) * 100 ) / 100;
        if ( slideJsApi2.currentScale <= num ) {
          slideJsApi2.currentScale = num;
        }
        slideJsApi2.sendCtrlAction('scale');
        slideJsApi2.render();
        slideJsApi2.resetZoomType();
      }, slideJsApi2.zoomInTimeTerm);
    },
    resetZoomType: function(){
      var scaleType = document.getElementById('scaleType');
      for (var i = 0; i < scaleType.children.length; i++) {
        scaleType[i].selected = false;
      }
      if ( document.querySelector("#scaleType option[value='" + Number(slideJsApi2.currentScale) + "']") ) {
        document.querySelector("#scaleType option[value='" + Number(slideJsApi2.currentScale) + "']").selected = true;
      }
      else {
        scaleType[0].selected = true;
      }
    },
    sendCtrlAction: function(key){
      var data = {to: 'customer'};
      data[key] = ( key === "page" ) ? slideJsApi2.currentPage : slideJsApi2.currentScale ;
      sessionStorage.setItem(key, data[key]); // セッションに格納
    },
    setWindowSize: function(wsInfo){
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
        slideJsApi2.render();
        slideJsApi2.pageRender();
      }
      catch(e) {
        console.log("error resize.", e);
      }
    },
    pageRender: function(){
      slideJsApi2.scrollTimer = null;
      var canvas = document.getElementById('document_canvas2');
      var frameWidth = $("slideFrame2").prop('offsetWidth');
      if ( isNumber(frameWidth) ) {
        canvas.style.left = -frameWidth * (slideJsApi2.currentPage - 1) + "px";
      }
      sessionStorage.setItem('page', slideJsApi2.currentPage); // セッションに格納
      $('#pages').text(slideJsApi2.currentPage + "/ " + slideJsApi2.maxPage);
    },
    render: function(){
      var canvas = document.querySelector('slideFrame2');
      /* サイズ調整処理 */
      $(".slide2 img").css("width", (canvas.clientWidth - 20) * 0.65 + "pt")
                     .css("height", (canvas.clientHeight - 20) * 0.65 + "pt");
      $(".slide2").css("width",  canvas.clientWidth + "px").css("height", canvas.clientHeight + "px") ;
      $(".slide2 img").css("transform", "scale(" + slideJsApi2.currentScale + ")");

      var docCanvas = document.getElementById('document_canvas2');
      docCanvas.style.width = this.maxPage * canvas.clientWidth + "px";
    },
    renderTimer: null,
    notificate: function(code){
      if ( this.cnst.hasOwnProperty(code) ) {
        console.log(this.cnst[code]);
      }
    },
    makePage: function(){
      var docCanvas = document.getElementById('document_canvas2');

      // 現在の表示ページから作っていく
      for(var i = 1; this.maxPage >= i; i++){
        var slide = document.createElement('div');
        slide.id = "slide2_" + i;
        slide.classList.add("slide2");
        slide.addEventListener('scroll', function(){
          slideJsApi2.scrollFunc();
        });
        docCanvas.appendChild(slide);
      }
      slideJsApi2.render();
    },
    readPage: function(){
      function setImage(page){
        var img = document.createElement('img');
        img.src = slideJsApi2.filePath + "_" + Number(page) + '.svg';
        var slide2 = document.getElementById('slide2_' + page);
        slide2.appendChild(img);
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

        if ( !document.querySelector('#slide2_' + this.loadedPage) || document.querySelector('#slide2_' + this.loadedPage + ' img') ) return false;
        setImage(this.loadedPage); // ページを追加
      }
      slideJsApi2.render();
    },
    readFile: function(doc,callback){
      this.filePath = "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/svg_"?>" + doc.file_name.replace(/\.pdf$/, "");
      sessionStorage.setItem('doc', JSON.stringify(doc));
      this.doc = doc;
      this.currentPage = (sessionStorage.getItem('page') !== null) ? Number(sessionStorage.getItem('page')) : 1;
      this.currentScale = (sessionStorage.getItem('scale') !== null) ? Number(sessionStorage.getItem('scale')) : 1;
      if ( sessionStorage.getItem('manuscript') === null ) { sessionStorage.setItem('manuscript', 'block') }
      this.manuscript = JSON.parse(doc.manuscript);
      this.loadedPage = 0;
      var settings = JSON.parse(doc.settings);
      this.maxPage = settings.pages;

      var limitPage = (this.currentPage + 3 > this.maxPage) ? this.maxPage : this.currentPage + 3 ;

      divCanvas = document.createElement("div");
      divCanvas.id = "document_canvas2";
      $("slideFrame2 #document_canvas2").remove();
      $("slideFrame2").append(divCanvas);
      this.makePage(); // 初期スライドを作成
      this.init();

      var readPageTimer = setInterval(function(){
        slideJsApi2.readPage();
        if ( limitPage < slideJsApi2.loadedPage ) {
          clearInterval(readPageTimer);
          slideJsApi2.pageRender();
          slideJsApi2.render();
          callback(false);
        }
      }, 1000);
    }
  };

  $(document).ready(function(){
    <?php if ( !empty($this->data['TDocument']['file_name']) ):
      $pages = 1;
      $settings = (array)json_decode($this->data['TDocument']['settings']);
      $pages = (isset($settings['pages'])) ? $settings['pages'] : 1;
      $filePath = C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/svg_".pathinfo(h($this->data['TDocument']['file_name']), PATHINFO_FILENAME);
    ?>

    slideJsApi.init("<?=$filePath?>", "<?=$pages?>");
    <?php endif; ?>
  });
})();

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
    $.ajax({
      type: 'post',
      data: {
        id:id
      },
      url: '<?=$this->Html->url(["controller" => "TDocuments", "action" => "remoteOpenDocumentPreview"])?>',
      dataType: 'json',
      success: function(json) {
        doc = JSON.parse(json.documentPreview)[0]['TDocument'];
        $("#document-preview").addClass("show");
        $scope.searchName = "";
        var contHeight = $('#document-preview-content').height();
        $('#document-preview-frame').css('height', contHeight);
        $scope.tagList = ( json.hasOwnProperty('tagList') ) ? JSON.parse(json.tagList) : {};
        $scope.documentList = ( json.hasOwnProperty('documentList') ) ? JSON.parse(json.documentPreview) : {};
        $scope.$apply();
        slideJsApi2.readFile(doc,function(err) {
          if (err) return false;
          var settings = JSON.parse(doc.settings);
        });
      }
    });
  };

  $scope.openDocumentList2 = function() {
    $.ajax({
      type: 'GET',
      url: '<?=$this->Html->url(["controller" => "Customers", "action" => "remoteOpenDocumentLists"])?>',
      dataType: 'json',
      success: function(json) {
        doc = JSON.parse(json.documentList)[0]['TDocument'];
        $("#switching-preview").addClass("show");
        $scope.searchName = "";
        var contHeight = $('#switching-preview-content').height();
        $('#switching-preview-frame').css('height', contHeight);
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
    sessionStorage.setItem('page', 1);
    sessionStorage.setItem('scale', 1);
    slideJsApi2.readFile(doc, function(err) {
      if (err) return false;
      var settings = JSON.parse(doc.settings);
    });
    $scope.closeDocumentList2();
  };

  $scope.closeDocumentList = function() {
    var scroll_event = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';
    $(document).off(scroll_event);
    $("#document-preview").removeClass("show");
  };

  $scope.closeDocumentList2 = function() {
    $("#switching-preview").removeClass("show");
  };
});

window.onload = function(){
  $("#manuscriptArea").draggable({
    scroll: false,
    containment: "slideframe2",
    cancel: "#document_canvas2"
  })
  .css({
    'display': 'block',
    'position': 'relative',
    'width': "calc(100% - 150px)",
    'left': "125px",
    'top': "4em"
  });
};
</script>
