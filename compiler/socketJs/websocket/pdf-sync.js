(function($){
  var pdfjsCNST, pdfjsApi;

  pdfjsCNST = function(){
    return {
      FIRST_PAGE: "最初のページ",
      LAST_PAGE: "最後のページ",
    };
  };

  pdfjsApi = {
    cnst: new pdfjsCNST(),
    pdf: null,
    pdfUrl: site.files + "/files/test.pdf",
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
    sendAction: function(){
      var canvas = document.getElementById('document_canvas');
      emit("docSendAction", {
        scroll: {
          top: pdfjsApi.scrollTop,
          left: pdfjsApi.scrollLeft
        },
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

  var st = io.connect(site.socket, {port: 9090, rememberTransport : false});

  var emit = function(key, data){
    data.siteKey = site.key;
    data.parentId = userInfo.parentId;
    data.tabId = userInfo.tabId;
    data.userId = params.userId;
    st.emit(key, JSON.stringify(data));
  };

  st.on("connect", function(d){
console.log(d);
  });


  pdfjsApi.init();
  window.focus();

// -->
})(sincloJquery);