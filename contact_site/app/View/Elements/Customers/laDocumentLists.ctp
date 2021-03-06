<script type="text/javascript">
<?= $this->element('TDocuments/loadScreen'); ?>

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
  maxPage: 1,
  rotation: 0,
  zoomInTimer: null,
  zoomInTimeTerm: 500,
  pagingTimer: null,
  pagingTimeTerm: 500,
  mouseTimer: null,
  resizeTimer: null,
  init: function(){
    this.cngPage();
    this.resetZoomType();// 拡大率を設定
    this.resizeTimer = null;
    this.mouseTimer = null;
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
        emit("docSendAction", {
          to: 'customer',
          page: slideJsApi.currentPage,
          scroll: {
            top: page.scrollTop / (page.scrollHeight - page.clientHeight),
            left: page.scrollLeft / (page.scrollWidth - page.clientWidth)
          }
        });
      }, 100);
    }
  },
  prevPage: function(){
    if ( slideJsApi.currentPage < 2 ) return slideJsApi.notificate('FIRST_PAGE');
    clearTimeout(this.pagingTimer);
    this.pagingTimer = setTimeout(function(){
      clearTimeout(slideJsApi.pagingTimer);
      slideJsApi.currentPage--;
      slideJsApi.cngPage();
    }, slideJsApi.pagingTimeTerm);
  },
  nextPage: function(){
    if ( slideJsApi.currentPage >= slideJsApi.maxPage ) return slideJsApi.notificate('LAST_PAGE');
    clearTimeout(this.pagingTimer);
    this.pagingTimer = setTimeout(function(){
      clearTimeout(slideJsApi.pagingTimer);
      slideJsApi.currentPage++;
      slideJsApi.cngPage();
    }, slideJsApi.pagingTimeTerm);
  },
  toggleManuScript: function(){
    var type = sessionStorage.getItem('manuscript');
    if ( type === "none" ) {
      type = 'block';
      document.getElementById('scriptToggleBtn').classList.add('on');
      if ( slideJsApi.manuscript.hasOwnProperty(Number(slideJsApi.currentPage)) && slideJsApi.manuscript[slideJsApi.currentPage] !== "" ) {
        $("#manuscriptArea").css({ 'display': type });
      }
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
    if ( type === "block" && slideJsApi.manuscript.hasOwnProperty(Number(slideJsApi.currentPage)) && slideJsApi.manuscript[slideJsApi.currentPage] !== "" ) {
      $("#manuscriptArea").css({ 'display': type });
      document.getElementById('scriptToggleBtn').classList.add('on');
    }
    else {
      $("#manuscriptArea").css({'display': 'none'});
    }
    document.getElementById('manuscript').textContent = slideJsApi.manuscript[slideJsApi.currentPage];
    $("img-frame.show").removeClass('show');
    $("#slide_page_" + slideJsApi.currentPage + " img-frame").addClass('show');
    slideJsApi.pageRender();
    slideJsApi.sendCtrlAction('page');
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
      slideJsApi.renderAllPage();
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
      slideJsApi.renderAllPage();
      slideJsApi.resetZoomType();
    }, slideJsApi.zoomInTimeTerm);
  },
  zoomOut: function(num){
    if ( slideJsApi.currentScale <= 0 ) return false;

    clearTimeout(this.zoomInTimer);
    this.zoomInTimer = setTimeout(function(){
      clearTimeout(slideJsApi.zoomInTimer);
        slideJsApi.currentScale = Math.ceil( (Number(slideJsApi.currentScale) - Number(num)) * 100 ) / 100;
      if ( slideJsApi.currentScale <= num ) {
        slideJsApi.currentScale = num;
      }
      slideJsApi.sendCtrlAction('scale');
      slideJsApi.renderAllPage();
      slideJsApi.resetZoomType();
    }, slideJsApi.zoomInTimeTerm);
  },
  resetZoomType: function(){
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
    emit("docSendAction", data);
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
      slideJsApi.render();
      slideJsApi.pageRender();
    }
    catch(e) {
      console.log("error resize.", e);
    }
  },
  pageRender: function(){ // ウィンドウのサイズ、ページが変わった時に必要な処理
    slideJsApi.scrollTimer = null;
    var canvas = document.getElementById('document_canvas');
    var frameWidth = $("slideFrame").prop('offsetWidth');
    sessionStorage.setItem('page', slideJsApi.currentPage); // セッションに格納

    if ( isNumber(frameWidth) ) {
      canvas.style.left = -frameWidth * (slideJsApi.currentPage - 1) + "px";
    }
    $('#pages').text(slideJsApi.currentPage + "/ " + slideJsApi.maxPage);
  },
  render: function(){
    var canvas = document.querySelector('slideframe');
    $(".slide").css("width",  canvas.clientWidth + "px").css("height", canvas.clientHeight + "px");

    var docCanvas = document.getElementById('document_canvas');
    docCanvas.style.width = this.maxPage * canvas.clientWidth + "px";
  },
  renderTimer: null,
  notificate: function(code){
    if ( this.cnst.hasOwnProperty(code) ) {
      console.log(this.cnst[code]);
    }
  },
  renderAllPage: function(){
    for( var i = 1; i <= this.maxPage; i++ ){
      this.renderPage(i);
    }
  },
  renderPage: function(page){ // ページのリサイズ、回転の処理（１ページずつ）
    var canvas = document.querySelector('slideframe'),
        pageImg = document.querySelector("#slide_" + page + " img"),
        wScale = 0, hScale = 0, scale = 0, pWidth = 0, pHeight = 0,
        cWidth = canvas.clientWidth,
        cHeight = canvas.clientHeight,
        matrix;

    if ( pageImg === null ) return false;
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
      var setWidth = pWidth * scale * slideJsApi.currentScale;
      var setHeight = pHeight * scale * slideJsApi.currentScale;
      var x = 0, y= 0;
      if ( Number(slideJsApi.rotation) === 90 || Number(slideJsApi.rotation) === 270 ) {
        x = (setHeight - setWidth)/2;
        if ( setHeight < cWidth ) {
          x += (cWidth - setHeight)/2;
        }
        y = (setWidth - setHeight)/2;
        if ( setWidth < cHeight ) {
          y += (cHeight - setWidth)/2;
        }
      }
      else {
        if ( setWidth < cWidth ) {
          x += (cWidth - setWidth)/2;
        }
        if ( setHeight < cHeight ) {
          y += (cHeight - setHeight)/2;
        }
      }

      switch (Number(slideJsApi.rotation)) {
        case 90:
          matrix = "matrix( 0, 1, -1, 0, " + x + ", " + y + ")";
          break;
        case 180:
          matrix = "matrix(1, 0, 0, -1, " + x + ", " + y + ")";
          break;
        case 270:
          matrix = "matrix( 0, -1, 1, 0, " + x + ", " + y + ")";
          break;
        default:
          matrix = "matrix( 1, 0, 0, 1, " + x + ", " + y + ")";
          break;
      }

      pageImg.style.width = setWidth + "px";
      pageImg.style.height = setHeight + "px";
      pageImg.style.transform = matrix;
    }, 10);

    setTimeout(function(){
      $('slideFrame').css("opacity", 1);
    }, 100);

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
      var slide = document.getElementById('slide_' + page);
      img.src = slideJsApi.filePath + "_" + Number(page) + '.svg';
      slide.appendChild(img);
      img.onload = function(){
        slideJsApi.renderPage(page);
      }
    }

    // 表示ページが１ページ目以上の場合
    if ( this.currentPage > 1 ) {
      var prevNode = null;
      setImage(this.currentPage);

      // 現在のページ以降のページを作る
      for(var i = this.currentPage + 1; i <= this.maxPage; i++){
        setImage(i);
      }

      setTimeout(function(){
        // 現在の表示ページから作っていく
        for(var i = slideJsApi.currentPage - 1; i > 0; i--){
          setImage(i);
        }
      }, 100);
    }
    else {
      // 現在のページ以降のページを作る
      for(var i = 1; i <= slideJsApi.maxPage; i++){
        setImage(i);
      }

    }

  },
  readFile: function(doc){
    $('slideFrame ').css("opacity", 0);
    this.filePath = "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/svg_"?>" + doc.file_name.replace(/\.pdf$/, "");
    sessionStorage.setItem('doc', JSON.stringify(doc));
    this.doc = doc;
    // ダウンロードファイルの設定
    document.getElementById('downloadFilePath').href = "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>" + doc.file_name;
    this.currentPage = (sessionStorage.getItem('page') !== null) ? Number(sessionStorage.getItem('page')) : 1;
    this.currentScale = (sessionStorage.getItem('scale') !== null) ? Number(sessionStorage.getItem('scale')) : 1;
    if ( sessionStorage.getItem('manuscript') === null ) { sessionStorage.setItem('manuscript', 'block') }
    this.manuscript = JSON.parse(doc.manuscript);
    var settings = JSON.parse(doc.settings);
    this.maxPage = settings.pages;
    this.rotation = (settings.hasOwnProperty('rotation')) ? settings.rotation : "";

    var divCanvas = document.createElement("div");
    divCanvas.id = "document_canvas";
    $("slideframe #document_canvas").remove();
    $("slideframe").append(divCanvas);

    this.makePage(); // 初期スライドを作成
    this.makeList(); // 目次作成
    this.init();

    this.readPage(); // ページ読み込み
    this.pageRender();
    this.render();
  },
  makeList: function(){
    var target = document.getElementById('slideList');
    target.innerHTML = "";

    // 現在の表示ページから作っていく
    for(var i = 1; this.maxPage >= i; i++){
      var slide = document.createElement('div');
      slide.id = 'slide_page_' + i;
      var frame = document.createElement('img-frame');
      var img = document.createElement('img');
      img.src = slideJsApi.filePath + "_" + Number(i) + '.svg';
      img.onerror="this.src='<?=C_PATH_WIDGET_GALLERY_IMG?>chat_sample_picture.png'";
      img.classList.add("rotate" + this.rotation);
      frame.appendChild(img);
      slide.appendChild(frame);
      target.appendChild(slide);
      $('#slide_page_' + i).attr('data-page', i);
      if ( i === this.maxPage ) {
        // 目次を作成する
        $('#slideList').slick({
          infinite: false,
          speed: 300,
          slidesToShow: 8,
          slidesToScroll: 8,
          prevArrow: '<a href="" id="prevArrow"></a>',
          nextArrow: '<a href="" id="nextArrow"></a>',
          responsive: [
            {
              breakpoint: 1900,
              settings: {
                slidesToShow: 8,
                slidesToScroll: 8,
              }
            },
            {
              breakpoint: 1700,
              settings: {
                slidesToShow: 7,
                slidesToScroll: 7,
              }
            },
            {
              breakpoint: 1500,
              settings: {
                slidesToShow: 6,
                slidesToScroll: 6,
              }
            },
            {
              breakpoint: 1300,
              settings: {
                slidesToShow: 5,
                slidesToScroll: 5
              }
            },
            {
              breakpoint: 1100,
              settings: {
                slidesToShow: 4,
                slidesToScroll: 4
              }
            },
            {
              breakpoint: 900,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 3
              }
            },
            {
              breakpoint: 700,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 2
              }
            },
            {
              breakpoint: 500,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
          ]
        });

      }
    }
  }
};

/*
// マウス位置
slideJsApi.mouseTimer = null;
window.addEventListener('mousemove', function(e){
  if ( slideJsApi.mouseTimer ) return false;
  slideJsApi.mouseTimer = setTimeout(function(){
    slideJsApi.mouseTimer = null;
    emit("docSendAction", {
      to: 'customer',
      mouse: {
        x: e.clientX * windowScale,
        y: e.clientY * windowScale
      }
    });
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
      if ( (canvas.scrollHeight - canvas.clientHeight - canvas.scrollTop) > 1 ) return false;
      if (e.preventDefault) { e.preventDefault(); }
      slideJsApi.nextPage();
    }
  }
});

// 特定のページへ移動
$(document).on('click', 'img-frame', function(){
  var page = $(this).parents('.slick-slide').data('page');
  $("#pageListToggleBtn").trigger('click');
  if ( page !== undefined ) {
    slideJsApi.currentPage = Number(page);
    clearTimeout(slideJsApi.pagingTimer);
    slideJsApi.pagingTimer = setTimeout(function(){
      clearTimeout(slideJsApi.pagingTimer);
      slideJsApi.sendCtrlAction('page');
      slideJsApi.cngPage();
    }, slideJsApi.pagingTimeTerm);
  }


});

// ウィンドウリサイズ
window.addEventListener('resize', function(){
  $('slideFrame').css("opacity", 0);
  clearTimeout(slideJsApi.resizeTimer);
  slideJsApi.resizeTimer = setTimeout(function(){
    slideJsApi.resizeTimer = null;
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
    slideJsApi.renderAllPage();
    slideJsApi.render();
  }, 500);
});

// 拡縮率をキー押下で変更できないようにする
$(document).on("keydown", "#scaleType", function(e){ return false; });
*/

var sincloApp = angular.module('sincloApp', []);
sincloApp.controller('MainController', function($scope){
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
  $scope.openDocumentList = function() {
    $('#controlBtn').trigger('click');
    // 目次が開いていたら閉じる
    if ($("#pageListToggleBtn").is('.on')){
      $("#pageListToggleBtn").trigger('click');
    }
    $.ajax({
      type: 'GET',
      url: '<?=$this->Html->url(["controller" => "Customers", "action" => "remoteOpenDocumentLists"])?>',
      dataType: 'json',
      success: function(json) {
        $("#ang-popup").addClass("show");
        $scope.searchName = "";
        var contHeight = $('#ang-popup-content').height();
        $('#ang-popup-frame').css('height', contHeight);
        $scope.tagList = ( json.hasOwnProperty('tagList') ) ? JSON.parse(json.tagList) : {};
        $scope.documentList = ( json.hasOwnProperty('documentList') ) ? JSON.parse(json.documentList) : {};
        $scope.$apply();
      }
    });
  };

  $scope.shareDocument = function(doc) {
    $scope.closeDocumentList();
    clearInterval($scope.createTimer);
    $scope.tabId = $scope.docShareId;
    $("#popup-bg").css("background-color","rgba(0, 0, 0, 0.0)");
    $('#afs-popup').show();
    $("#afs-popup").addClass("show");
    $('#afs-popup-frame').css('height', $('#popup-frame').height());
    this.notFirstTime = true;
    $scope.message = "お客様に共有の許可を求めています。";
    $scope.title = "共有申請中";
    $scope.createTimer = setInterval(function () {
      if ($scope.title.length > 7) {
        $scope.title = "共有申請中";
        $scope.$apply();
      }
      else {
        $scope.title　+= '・';
        $scope.$apply();
      }
    }, 500);

    var settings = JSON.parse(doc.settings);
    var rotation = (settings.hasOwnProperty('rotation')) ? settings.rotation : 0;
    emit('docShareConnect', {
      id: doc.id,
      from: 'company',
      responderId: '<?=$userInfo["id"]?>',
      directory: "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>",
      fileName: doc.file_name,
      pagenation_flg: doc.pagenation_flg,
      pages: settings.pages,
      rotation: rotation,
      download_flg: doc.download_flg,
      tabId: tabId.replace("_frame", ""),
      popup:'true'
    });
  };

  $scope.docShare = function(doc){
    var obj = JSON.parse(data);
    if(obj && obj.responderId && Number(obj.responderId) === Number(<?=$userInfo["id"]?>)) {
      window.open(
        "<?= $this->Html->url(['controller' => 'Customers', 'action' => 'docFrame']) ?>?tabInfo=" + encodeURIComponent($scope.docShareId) + "&docId=" + obj.id,
        "doc_monitor_" + $scope.docShareId,
        "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
      );
      $('#afs-popup').hide();
    }
  };

  /**
   * [changeDocument description]
   * @param  {object} doc document's info
   * @return {void}     send new docURL
   */
  $scope.changeDocument = function(doc){
    // 目次リセット
    $('#slideList').slick('unslick');

    sessionStorage.setItem('page', 1);
    sessionStorage.setItem('scale', 1);

    loading.load.start(); // ローディング開始
    slideJsApi.readFile(doc);

    var settings = JSON.parse(doc.settings);
    var rotation = (settings.hasOwnProperty('rotation')) ? settings.rotation : 0;
    emit("changeDocument", {
      directory: "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>",
      fileName: doc.file_name,
      pages: settings.pages,
      rotation: rotation,
      pagenation_flg: doc.pagenation_flg,
      download_flg: doc.download_flg
    });

    $scope.closeDocumentList();
  };

  $scope.closeDocumentList = function() {
    $("#ang-popup").removeClass("show");
  };

  $scope.setDocThumnailStyle = function(doc) {
    var matrix = "";
    if ( doc.hasOwnProperty('settings') ) {
      var settings = JSON.parse(doc.settings);
      if ( settings.hasOwnProperty('rotation') && isNumber(settings.rotation) ) {
        matrix = "rotate" + settings.rotation;
      }
    }
    return matrix;
  };

});

sincloApp.directive('ngOverView', function(){
  return {
    restrict: "E",
    scope: {
      text: "@",
      docid: "@"
    },
    template: '<span ng-mouseover="toggleOverView()" ng-mouseleave="toggleOverView()">{{::text}}</span>',
    link: function(scope, elem, attr){
      var ballons = angular.element('#ang-ballons');
      var ballon = document.createElement('div');
      ballon.classList.add("hide");
      ballon.textContent = scope.text;
      ballon.setAttribute('data-id', scope.docid);
      ballons.append(ballon);

      scope.toggleOverView = function(){
        var p = angular.element(elem).offset();
        ballon.style.top = p.top + "px";
        ballon.style.left = p.left + "px";
        ballon.classList.toggle("hide");
      };
    }
  };
});

sincloApp.directive('ngMultiSelector', function(){
  return {
    restrict: "E",
    template: '<selected data-elem-type="selector" ng-click="openMultiSelector()">{{selected}}</selected>' +
              '<ul>' +
              '  <li data-elem-type="selector" ng-repeat="(id, name) in tagList" ng-click="changAct(id)" ng-class="{selected: judgeSelect(id)}">{{name}}</li>' +
              '</ul>',
    link: function(scope, elem, attr){
      scope.openMultiSelector = function(){
        var e = angular.element(elem);
        if ( e.hasClass('show') ) {
          e.removeClass('show');
        }
        else {
          e.addClass('show');
        }
      };
      scope.selected = "-";
      scope.changAct = function(id){
        if ( scope.selectList.hasOwnProperty(id) ) {
          delete scope.selectList[id];
        }
        else {
          scope.selectList[id] = true;
        }
        var str = Object.keys(scope.selectList).map(function(item){
          return scope.tagList[item];
        }).join('、');
        scope.selected = ( str === "" ) ? "-" : str;
      };

      scope.judgeSelect = function(id){
        return (scope.selectList.hasOwnProperty(id));
      };

      scope.jParse = function(str){
        return JSON.parse(str);
      };
    }
  };
});
</script>
