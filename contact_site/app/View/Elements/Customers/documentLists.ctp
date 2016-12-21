<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/compatibility.min.js"); ?>
<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/pdf.min.js"); ?>

<script type="text/javascript">

PDFJS.workerSrc = "<?=C_PATH_NODE_FILE_SERVER?>/websocket/pdf.worker.min.js";


var pdfjsCNST = function(){
  return {
    FIRST_PAGE: "最初のページ",
    LAST_PAGE: "最後のページ",
  };
};

var pdfjsApi = {
  cnst: new pdfjsCNST(),
  pdf: null,
  pdfUrl: null,
  currentPage: 1,
  currentScale: 1,
  renderFlg: false,
  zoomInTimer: null,
  zoomInTimeTerm: 200,
  pagingTimer: null,
  pagingTimeTerm: 200,
  init: function(){
    this.cngPage();
    this.showpage();
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
      }, 10);
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
        e.preventDefault();
        if (e.preventDefault) { e.preventDefault(); }
        // 拡大
        if ( e.deltaY < 0 ) {
          pdfjsApi.zoomIn(0.1);
        }
        // 縮小
        else {
          pdfjsApi.zoomOut(0.1);
        }
      }
      else {
        var canvas = document.getElementById('document_canvas');
        // 前のページへ
        if ( e.deltaY < 0 ) {
          if ( canvas.scrollTop !== 0 ) return false;
          if (e.preventDefault) { e.preventDefault(); }
          pdfjsApi.prevPage();
        }
        // 次のページへ
        else {
          if ( (canvas.scrollHeight - canvas.clientHeight) !== canvas.scrollTop ) return false;
          if (e.preventDefault) { e.preventDefault(); }
          pdfjsApi.nextPage();
        }
      }
    });

    // ウィンドウリサイズ
    var resizeTimer = null;
    window.addEventListener('resize', function(){
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function(){
        clearTimeout(resizeTimer);
        resizeTimer = null;
        var size = JSON.parse(sessionStorage.getItem('windowSize'));
        if ( size !== null && size.hasOwnProperty('width') && size.hasOwnProperty('height') && (size.width !== window.outerWidth || size.height !== window.outerHeight) ) {
          window.resizeTo(size.width, size.height);
        }
        pdfjsApi.render();
      }, 300);
    });

    // スクロール位置
    pdfjsApi.scrollTimer = null;
    canvas.addEventListener('scroll', this.scrollFunc);
  },
  scrollTimer: null,
  scrollFunc: function(e){
    if ( pdfjsApi.scrollTimer !== null ) return false;
    pdfjsApi.scrollTimer = setTimeout(function(){
      clearTimeout(pdfjsApi.scrollTimer);
      pdfjsApi.scrollTimer = null;
      emit("docSendAction", {
        to: 'customer',
        scroll: {
          top: e.target.scrollTop,
          left: e.target.scrollLeft
        }
      });
    }, 100);
  },
  prevPage: function(){
    if ( pdfjsApi.renderFlg ) return false;

    clearTimeout(this.pagingTimer);
    this.pagingTimer = setTimeout(function(){
      clearTimeout(pdfjsApi.pagingTimer);
      if ( pdfjsApi.currentPage < 2 ) return pdfjsApi.notificate('FIRST_PAGE');
      pdfjsApi.renderFlg = true;
      pdfjsApi.currentPage--;
      pdfjsApi.cngPage();
      pdfjsApi.pageRender();
      pdfjsApi.sendCtrlAction();
    }, pdfjsApi.pagingTimeTerm);
  },
  nextPage: function(){
    if ( pdfjsApi.renderFlg ) return false;

    clearTimeout(this.pagingTimer);
    this.pagingTimer = setTimeout(function(){
      clearTimeout(pdfjsApi.pagingTimer);
      if ( pdfjsApi.currentPage >= pdfjsApi.pdf.pdfInfo.numPages ) return pdfjsApi.notificate('LAST_PAGE');
      pdfjsApi.renderFlg = true;
      pdfjsApi.currentPage++;
      pdfjsApi.sendCtrlAction();
      pdfjsApi.cngPage();
      pdfjsApi.pageRender();
    }, pdfjsApi.pagingTimeTerm);
  },
  toggleManuScript: function(){
    if ( document.getElementById('manuscript').textContent !== "" ) {
      $("#manuscriptArea").toggle();
      sessionStorage.setItem('manuscript', $("#manuscriptArea").css('display'));
    }
    if ( document.getElementById('manuscriptArea').style.display === "none" ) {
      document.getElementById('scriptToggleBtn').classList.remove('on');
    }
    else {
      document.getElementById('scriptToggleBtn').classList.add('on');
    }

  },
  cngPage: function(){
    var script = "", type = sessionStorage.getItem('manuscript');
    if ( pdfjsApi.manuscript.hasOwnProperty(Number(pdfjsApi.currentPage)) ) {
      script = pdfjsApi.manuscript[pdfjsApi.currentPage];
      $("#manuscriptArea").css({ 'display': type });
    }
    else {
      $("#manuscriptArea").css({'display': 'none'});
    }
    if ( document.getElementById('manuscriptArea').style.display === "none" ) {
      document.getElementById('scriptToggleBtn').classList.remove('on');
    }
    else {
      document.getElementById('scriptToggleBtn').classList.add('on');
    }
    document.getElementById('manuscript').textContent = script;
  },
  cngScale: function(){
    var type = document.getElementById('scaleType').value;
    if ( type && !isNaN(Number(type)) ) {
      this.zoom(type);
    }
  },
  zoom: function(num){
    clearTimeout(this.zoomInTimer);
    this.zoomInTimer = setTimeout(function(){
      clearTimeout(pdfjsApi.zoomInTimer);
      pdfjsApi.currentScale = num;
      pdfjsApi.sendCtrlAction();
      pdfjsApi.render();
    }, pdfjsApi.zoomInTimeTerm);
  },
  zoomIn: function(num){
    clearTimeout(this.zoomInTimer);
    this.zoomInTimer = setTimeout(function(){
      clearTimeout(pdfjsApi.zoomInTimer);
      if ( pdfjsApi.currentScale >= 4 ) return false;
        pdfjsApi.currentScale = Number(pdfjsApi.currentScale) + Number(num);
      if ( pdfjsApi.currentScale > 4 ) {
        pdfjsApi.currentScale = 4;
      }
      pdfjsApi.sendCtrlAction();
      pdfjsApi.render();
    }, pdfjsApi.zoomInTimeTerm);
  },
  zoomOut: function(num){
    clearTimeout(this.zoomInTimer);
    this.zoomInTimer = setTimeout(function(){
      clearTimeout(pdfjsApi.zoomInTimer);
      if ( pdfjsApi.currentScale <= 0 ) return false;
        pdfjsApi.currentScale = Number(pdfjsApi.currentScale) - Number(num);
      if ( pdfjsApi.currentScale <= num ) {
        pdfjsApi.currentScale = num;
      }
      pdfjsApi.sendCtrlAction();
      pdfjsApi.render();
    }, pdfjsApi.zoomInTimeTerm);
  },
  sendCtrlAction: function(){
    var canvas = document.getElementById('document_canvas');
    emit("docSendAction", {
      to: 'customer',
      page: pdfjsApi.currentPage,
      scale: pdfjsApi.currentScale
    });
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
      var scale = wsInfo.width/frame.width;
      sessionStorage.setItem('windowScale', scale);
      windowScale = scale;

      window.moveTo(winX, winY);
      window.resizeTo(wswidth, wsheight);
    }
    catch(e) {
      console.log("error resize.", e);
    }
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
        canvasFrame.scrollTop = 0;
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
  pageRender: function(){
    pdfjsApi.pdf.getPage(pdfjsApi.currentPage)
      .then(function(page){
        var canvasFrame = document.getElementById('document_canvas');
        canvasFrame.scrollTop = 0;
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

    sessionStorage.setItem('page', pdfjsApi.currentPage);
    sessionStorage.setItem('scale', pdfjsApi.currentScale);

    function fitWindow(page) {
      var viewport = page.getViewport(1);
      var widthScale = canvasFrame.clientWidth/viewport.width;
      var heightScale = canvasFrame.clientHeight/viewport.height;
      var scale = ( widthScale > heightScale ) ? heightScale : widthScale;
      return page.getViewport(scale * pdfjsApi.currentScale);
    }

    if ( pdfjsApi.page === undefined ) {
      return false;
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

    // Render PDF page
    page.render({
      canvasContext: pdfjsApi.canvas.getContext('2d'),
      viewport: viewport
    }).then(function(){
      document.getElementById('pages').textContent = pdfjsApi.currentPage + "/ " + pdfjsApi.pdf.pdfInfo.numPages;
      pdfjsApi.canvas.style.opacity = 1;
      pdfjsApi.renderFlg = false;
    });
  },
  renderTimer: null,
  notificate: function(code){
    if ( this.cnst.hasOwnProperty(code) ) {
      console.log(this.cnst[code]);
    }
  },
  readFile: function(doc){
    var file = "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>" + doc.file_name;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', file, true);
    xhr.responseType = 'arraybuffer';
    xhr.onload = function(e) {
      if (this.status == 200) {
        sessionStorage.setItem('doc', JSON.stringify(doc));
        pdfjsApi.doc = doc;
        pdfjsApi.pdfUrl = new Uint8Array(this.response);
        pdfjsApi.currentPage = (sessionStorage.getItem('page') !== null) ? Number(sessionStorage.getItem('page')) : 1;
        pdfjsApi.currentScale = (sessionStorage.getItem('scale') !== null) ? Number(sessionStorage.getItem('scale')) : 1;
        pdfjsApi.manuscript = JSON.parse(doc.manuscript);
        pdfjsApi.init();
        document.getElementById('downloadFilePath').href = file;
      }
    };
    xhr.send();
  }
};

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
    pdfjsApi.readFile(doc);
    emit("changeDocument", {
      url: "<?=C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/"?>" + doc.file_name,
      pagenation_flg: doc.pagenation_flg,
      download_flg: doc.download_flg
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