<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/compatibility.min.js"); ?>

<script type="text/javascript">
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
          if ( (canvas.scrollHeight - canvas.clientHeight) !== canvas.scrollTop ) return false;
          if (e.preventDefault) { e.preventDefault(); }
          slideJsApi.nextPage();
        }
      }
    });

    // ウィンドウリサイズ
    var resizeTimer = null;
    window.addEventListener('resize', function(){
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
    });

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
      slideJsApi.sendCtrlAction('page');
      slideJsApi.cngPage();
    }, slideJsApi.pagingTimeTerm);
  },
  nextPage: function(){
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
    if ( type === "block" && slideJsApi.manuscript.hasOwnProperty(Number(slideJsApi.currentPage)) && slideJsApi.manuscript[slideJsApi.currentPage] !== "" ) {
      $("#manuscriptArea").css({ 'display': type });
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
  pageRender: function(){
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
    var canvas = document.querySelector('slideframe');
    var frameWidth = $("slideFrame").prop('clientWidth');
    var frameHeight = $("slideFrame").prop('clientHeight');
    /* サイズ調整処理 */
    $(".slide img").css("width", (canvas.clientWidth - 20) * 0.75 + "pt")
                   .css("height", (canvas.clientHeight - 20) * 0.75 + "pt");
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
    this.filePath = "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/svg_"?>" + doc.file_name.replace(/\.pdf$/, "");
    sessionStorage.setItem('doc', JSON.stringify(doc));
    this.doc = doc;
    // ダウンロードファイルの設定
    document.getElementById('downloadFilePath').href = "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>" + doc.file_name;
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
    this.init();

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

// 拡縮率をキー押下で変更できないようにする
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
  $scope.openDocumentList = function() {
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
    slideJsApi.readFile(doc, function(err) {
      if (err) return false;
      var settings = JSON.parse(doc.settings);
      emit("changeDocument", {
        directory: "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>",
        fileName: doc.file_name,
        pages: settings.pages,
        pagenation_flg: doc.pagenation_flg,
        download_flg: doc.download_flg
      });

    });

    $scope.closeDocumentList();
  };

  $scope.closeDocumentList = function() {
    $("#ang-popup").removeClass("show");
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