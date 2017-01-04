var pdfjsCNST, pdfjsApi, frameSize, scrollFlg;
(function(){
  PDFJS.workerSrc = site.files + "/websocket/pdf.worker.min.js";
  pdfjsCNST = function(){
    return {
      FIRST_PAGE: "最初のページ",
      LAST_PAGE: "最後のページ",
    };
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

  pdfjsApi = {
    cnst: new pdfjsCNST(),
    pdf: null,
    pdfUrl: null,
    currentPage: 1,
    currentScale: 1,
    renderFlg: false,
    zoomInTimer: null,
    zoomInTimeTerm: 500,
    pagingTimer: null,
    pagingTimeTerm: 500,
    init: function(){
      this.showpage();
      this.resetZoomType();
      var canvas = document.getElementById('document_canvas');

      emit("docSendAction", {
        to: 'company',
        scroll: {
          top: canvas.scrollTop,
          left: canvas.scrollLeft
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
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function(){
          clearTimeout(resizeTimer);
          resizeTimer = null;
          pdfjsApi.sendPositionAction();
          pdfjsApi.render();
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
          e.preventDefault();
          clearTimeout(pdfjsApi.zoomInTimer);
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
          clearTimeout(pdfjsApi.pagingTimer);
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
      // スクロール位置
      pdfjsApi.scrollTimer = null;
      canvas.addEventListener('scroll', this.scrollFunc);
    },
    scrollTimer: null,
    scrollFunc: function(e){
      if ( pdfjsApi.scrollTimer !== null ) return false;
      clearTimeout(this.scrollTimer);
      pdfjsApi.scrollTimer = setTimeout(function(){
        clearTimeout(pdfjsApi.scrollTimer);
        pdfjsApi.scrollTimer = null;
        emit("docSendAction", {
          to: 'company',
          scroll: {
            top: e.target.scrollTop,
            left: e.target.scrollLeft
          }
        });
      }, 100);
    },
    prevPage: function(){
      if ( this.renderFlg ) return false;
      if ( this.currentPage < 2 ) return this.notificate('FIRST_PAGE');
      clearTimeout(this.pagingTimer);
      this.pagingTimer = setTimeout(function(){
        clearTimeout(pdfjsApi.pagingTimer);
        pdfjsApi.renderFlg = true;
        pdfjsApi.currentPage--;
        pdfjsApi.pageRender();
        pdfjsApi.sendCtrlAction('page');
      }, pdfjsApi.pagingTimeTerm);
    },
    nextPage: function(){
      if ( this.renderFlg ) return false;
      if ( this.currentPage >= this.pdf.pdfInfo.numPages ) return this.notificate('LAST_PAGE');
      clearTimeout(this.pagingTimer);
      this.pagingTimer = setTimeout(function(){
        clearTimeout(pdfjsApi.pagingTimer);
        pdfjsApi.renderFlg = true;
        pdfjsApi.currentPage++;
        pdfjsApi.pageRender();
        pdfjsApi.sendCtrlAction('page');
      }, pdfjsApi.pagingTimeTerm);
    },
    cngScaleTimer: null,
    cngScale: function(){
      clearTimeout(pdfjsApi.cngScaleTimer);
      pdfjsApi.cngScaleTimer = setTimeout(function(){
        clearTimeout(pdfjsApi.cngScaleTimer);
        var type = document.getElementById('scaleType').value;
        if ( type && !isNaN(Number(type)) ) {
          pdfjsApi.zoom(type);
        }
      }, pdfjsApi.zoomInTimeTerm);
    },
    zoom: function(num){
      clearTimeout(this.zoomInTimer);
      this.zoomInTimer = setTimeout(function(){
        clearTimeout(this.zoomInTimer);
        pdfjsApi.currentScale = num;
        pdfjsApi.sendCtrlAction("scale");
        pdfjsApi.render();
      }, pdfjsApi.zoomInTimeTerm);
    },
    zoomIn: function(num){
      if ( this.currentScale >= 4 ) return false;

      clearTimeout(this.zoomInTimer);
      this.zoomInTimer = setTimeout(function(){
        clearTimeout(pdfjsApi.zoomInTimer);
        pdfjsApi.currentScale = Math.ceil( (Number(pdfjsApi.currentScale) + Number(num)) * 100 ) / 100;
        if ( pdfjsApi.currentScale > 4 ) {
          pdfjsApi.currentScale = 4;
        }
        pdfjsApi.sendCtrlAction("scale");
        pdfjsApi.render();
        pdfjsApi.resetZoomType();
      }, pdfjsApi.zoomInTimeTerm);
    },
    zoomOut: function(num){
      if ( this.currentScale <= 0 ) return false;

      clearTimeout(this.zoomInTimer);
      this.zoomInTimer = setTimeout(function(){
        clearTimeout(pdfjsApi.zoomInTimer);
        pdfjsApi.currentScale = Math.ceil( (Number(pdfjsApi.currentScale) - Number(num)) * 100 ) / 100;
        if ( pdfjsApi.currentScale <= num ) {
          pdfjsApi.currentScale = num;
        }
        pdfjsApi.sendCtrlAction("scale");
        pdfjsApi.render();
        pdfjsApi.resetZoomType();
      }, pdfjsApi.zoomInTimeTerm);
    },
    resetZoomType: function(){
      var scaleType = document.getElementById('scaleType');

      for (var i = 0; i < scaleType.children.length; i++) {
        scaleType[i].selected = false;
      }
      if ( document.querySelector("#scaleType option[value='" + Number(pdfjsApi.currentScale) + "']") ) {
        document.querySelector("#scaleType option[value='" + Number(pdfjsApi.currentScale) + "']").selected = true;
      }
      else {
        scaleType[0].selected = true;
      }
    },
    sendPositionAction: function(){
      emit("docSendAction", {
        to: 'company',
        offset: {
          width: window.innerWidth,
          height: window.innerHeight
        }
      });
    },
    sendCtrlAction: function(key){
      var data = {to: 'company'};
      data[key] = ( key === "page" ) ? pdfjsApi.currentPage : pdfjsApi.currentScale ;
      emit("docSendAction", data);
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
        if ( document.getElementById('pages') ) {
          document.getElementById('pages').textContent = pdfjsApi.currentPage + "/ " + pdfjsApi.pdf.pdfInfo.numPages;
        }
        pdfjsApi.renderFlg = false;
        pdfjsApi.canvas.style.opacity = 1;
      });
    },
    notificate: function(code){
      if ( this.cnst.hasOwnProperty(code) ) {
        console.log(this.cnst[code]);
      }
    },
    readFile: function(doc){
      var xhr = new XMLHttpRequest();
      xhr.open('GET', doc.url, true);
      xhr.responseType = 'arraybuffer';
      xhr.onload = function(e) {
        if (this.status == 200) {
          sessionStorage.setItem('doc', JSON.stringify(doc));
          pdfjsApi.doc = doc;
          $("#pages").remove();
          if ( Number(doc.pagenation_flg) === 1 ) {
            var s = document.createElement("span");
            s.id = "pages";
            document.getElementById('pageNumTag').appendChild(s);
          }
          document.getElementById('downloadBtn').style.display = "none";
          document.getElementById('downloadFilePath').href = "javascript:void(0)";
          if ( Number(doc.download_flg) === 1 ) {
            document.getElementById('downloadBtn').style.display = "";
            document.getElementById('downloadFilePath').href = doc.url;
          }
          pdfjsApi.pdfUrl = new Uint8Array(this.response);
          pdfjsApi.currentPage = (sessionStorage.getItem('page') !== null) ? Number(sessionStorage.getItem('page')) : 1;
          pdfjsApi.currentScale = (sessionStorage.getItem('scale') !== null) ? Number(sessionStorage.getItem('scale')) : 1;
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
    var doc = {
      url: params.url,
      pagenation_flg: params.pagenation_flg,
      download_flg: params.download_flg
    };

    emit('docShareConnect', {from: 'customer'}); // 資料共有開始
    frameSize = {
      height: window.outerHeight - window.innerHeight,
      width: window.outerWidth - window.innerWidth
    };

    if ( sessionStorage.getItem("doc") !== null ) {
      doc = JSON.parse(sessionStorage.getItem("doc"));
    }
    else { // 初回表示
      window.resizeTo(screen.availWidth, screen.availHeight);
      window.moveTo(0,0);
      setTimeout(function(){
        pdfjsApi.sendPositionAction(); // サイズを企業側へ送る
      }, 500);
    }

    pdfjsApi.readFile(doc);

  });

  // 資料変更
  st.on("changeDocument", function(d){
    var obj = JSON.parse(d);
    sessionStorage.setItem('page', 1);
    sessionStorage.setItem('scale', 1);
    pdfjsApi.readFile(obj);
  });

  // 同期イベント
  var scAddEventTimer = null;
  st.on('docSendAction', function(d){
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
    if ( obj.hasOwnProperty('scale') ) {
      pdfjsApi.currentScale = obj.scale;
      pdfjsApi.resetZoomType();
    }
    if ( obj.hasOwnProperty('page') ) {
      pdfjsApi.currentPage = obj.page;
      pdfjsApi.pageRender();
    }
    else {
      pdfjsApi.render();
    }
  });

  st.on('docDisconnect', function(d){
    window.close();
    return false;
  });

  window.focus();

// -->
})();

// 拡縮率をキー押下で変更できないようにする
$(document).on("keydown", "#scaleType", function(e){ return false; });
