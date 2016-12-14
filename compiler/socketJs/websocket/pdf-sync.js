var pdfjsCNST, pdfjsApi, frameSize, docDownload;
(function($){
  PDFJS.workerSrc = site.files + "/websocket/pdf.worker.min.js";

  pdfjsCNST = function(){
    return {
      FIRST_PAGE: "最初のページ",
      LAST_PAGE: "最後のページ",
    };
  };

  pdfjsApi = {
    cnst: new pdfjsCNST(),
    pdf: null,
    pdfUrl: null,
    currentPage: 1,
    currentScale: 1,
    init: function(){
      this.showpage();
      var canvas = document.getElementById('document_canvas');

      emit("docSendAction", {
        to: 'company',
        scroll: {
          top: canvas.scrollTop,
          left: canvas.scrollLeft
        },
        offset: {
          width: window.innerWidth,
          height: window.innerHeight
        },
        page: pdfjsApi.currentPage,
        scale: pdfjsApi.currentScale
      });

      // マウス位置
      window.addEventListener('mousemove', function(e){
        var canvas = document.getElementById('document_canvas');
        emit("docSendAction", {
          to: 'company',
          mouse: {
            x: e.clientX,
            y: e.clientY
          }
        });
      });

      // ウィンドウリサイズ
      var resizeTimer = null;
      window.addEventListener('resize', function(){
        if ( resizeTimer ) {
          clearTimeout(resizeTimer);
        }
        resizeTimer = setTimeout(function(){
          pdfjsApi.sendPositionAction();
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

      // スクロール位置
      $("#document_canvas").stop().on('scroll', function(e){
        emit("docSendAction", {
          to: 'company',
          scroll: {
            top: e.target.scrollTop,
            left: e.target.scrollLeft
          }
        });
      });
    },
    docDownload: function(){

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
      this.render();
    },
    zoomIn: function(num){
      if ( this.currentScale >= 4 ) return false;
      this.currentScale+=num;
      if ( this.currentScale > 4 ) {
        this.currentScale = 4;
      }
      pdfjsApi.sendCtrlAction();
      pdfjsApi.render();
    },
    zoomOut: function(num){
      if ( this.currentScale <= 0 ) return false;
      this.currentScale-=num;
      if ( this.currentScale <= num ) {
        this.currentScale = num;
      }
      pdfjsApi.sendCtrlAction();
      pdfjsApi.render();
    },
    sendPositionAction: function(){
      var canvas = document.getElementById('document_canvas');
      emit("docSendAction", {
        to: 'company',
        offset: {
          width: window.innerWidth,
          height: window.innerHeight
        }
      });
    },
    sendCtrlAction: function(){
      var canvas = document.getElementById('document_canvas');
      emit("docSendAction", {
        to: 'company',
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
    },
    readFile: function(file){
      var xhr = new XMLHttpRequest();
      xhr.open('GET', file, true);
      xhr.responseType = 'arraybuffer';
      xhr.onload = function(e) {
          if (this.status == 200) {
            sessionStorage.setItem('pdfUrl', file);
            // Note: .response instead of .responseText
            var blob = new Blob([this.response], {type: 'application/pdf'});
            pdfjsApi.pdfUrl = URL.createObjectURL(blob);
            pdfjsApi.currentPage = 1;
            pdfjsApi.currentScale = 1;
            pdfjsApi.init();

          }
      };
      xhr.send();
    }
  };

  var st = io.connect(site.socket, {port: 9090, rememberTransport : false});

  var emit = function(key, data){
    data.siteKey = site.key;
    data.tabId = params.tabId;
    data.userId = params.userId;
    st.emit(key, JSON.stringify(data));
  };

  st.on("connect", function(d){
    var path = params.url;
    if ( sessionStorage.getItem("pdfUrl") !== null ) {
      path = sessionStorage.getItem("pdfUrl");
    }
    pdfjsApi.readFile(path);

    emit('docShareConnect', {from: 'customer'}); // 資料共有開始

    frameSize = {
      height: window.outerHeight - window.innerHeight,
      width: window.outerWidth - window.innerWidth
    };

  });

  // 資料変更
  st.on("changeDocument", function(d){
    var obj = JSON.parse(d);
    pdfjsApi.readFile(obj.file);
  });

  // 同期イベント
  st.on('docSendAction', function(d){
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
        $('body').append('<div id="cursorImg" style="position:fixed; top:' + obj.mouse.x + '; left:' + obj.mouse.y + '; z-index: 1"><img width="50px" src="' + site.files + '/img/pointer.png"></div>');
        cursor = document.getElementById("cursorImg");
      }
      cursor.style.left = obj.mouse.x + "px";
      cursor.style.top  = obj.mouse.y + "px";
      return false;
    }
    if ( obj.hasOwnProperty('page') ) {
      pdfjsApi.currentPage = obj.page;
    }
    if ( obj.hasOwnProperty('scale') ) {
      pdfjsApi.currentScale = obj.scale;
    }
    pdfjsApi.showpage();
  });
  window.focus();

// -->
})(sincloJquery);