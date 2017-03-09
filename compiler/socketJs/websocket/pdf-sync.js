var pdfjsCNST, slideJsApi, frameSize, scrollFlg;
(function(){
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

  function isNumber(n){
    return RegExp(/^(\+|\-)?\d+(.\d+)?$/).test(n);
  }

  slideJsApi = {
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
      this.resetZoomType();// 拡大率を設定
      this.pageRender();
      var canvas = document.getElementById('document_canvas');

      emit("docSendAction", {
        to: 'company',
        scroll: {
          top: canvas.scrollTop,
          left: canvas.scrollLeft
        },
        page: slideJsApi.currentPage,
        scale: slideJsApi.currentScale
      });

      // マウス位置
      var mouseTimer = null;
      window.addEventListener('mousemove', function(e){
        if ( mouseTimer ) return false;
        mouseTimer = setTimeout(function(){
          mouseTimer = null;
          emit("docSendAction", {
            to: 'company',
            mouse: {
              x: e.clientX,
              y: e.clientY
            }
          });
        }, 15);
      });

      // ウィンドウリサイズ
      var resizeTimer = null;
      window.addEventListener('resize', function(){
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function(){
          clearTimeout(resizeTimer);
          resizeTimer = null;
          slideJsApi.sendPositionAction();
          slideJsApi.render();
          slideJsApi.pageRender();
        }, 300);
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
          clearTimeout(slideJsApi.pagingTimer);
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
    },
    scrollTimer: null,
    setScrollTimer: null,
    setScrollFlg: false,
    scrollFunc: function(e){
      if ( slideJsApi.setScrollFlg ) return false;
      clearTimeout(this.scrollTimer);
      slideJsApi.scrollTimer = setTimeout(function(){
        clearTimeout(slideJsApi.scrollTimer);
        slideJsApi.scrollTimer = null;
        var page = document.getElementById("slide_" + slideJsApi.currentPage);
        emit("docSendAction", {
          to: 'company',
          page: slideJsApi.currentPage,
          scroll: {
            top: page.scrollTop / (page.scrollHeight - page.clientHeight),
            left: page.scrollLeft / (page.scrollWidth - page.clientWidth)
          }
        });
      }, 100);
    },
    prevPage: function(){
      if ( this.currentPage < 2 ) return this.notificate('FIRST_PAGE');
      clearTimeout(this.pagingTimer);
      this.pagingTimer = setTimeout(function(){
        clearTimeout(slideJsApi.pagingTimer);
        slideJsApi.currentPage--;
        slideJsApi.cngPage();
      }, slideJsApi.pagingTimeTerm);
    },
    nextPage: function(){
      if ( this.currentPage >= slideJsApi.maxPage ) return this.notificate('LAST_PAGE');
      clearTimeout(this.pagingTimer);
      this.pagingTimer = setTimeout(function(){
        clearTimeout(slideJsApi.pagingTimer);
        slideJsApi.currentPage++;
        slideJsApi.cngPage();
      }, slideJsApi.pagingTimeTerm);
    },
    cngPage: function(){
      slideJsApi.readPage();
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
        clearTimeout(this.zoomInTimer);
        slideJsApi.currentScale = num;
        slideJsApi.sendCtrlAction("scale");
        slideJsApi.render();
      }, slideJsApi.zoomInTimeTerm);
    },
    zoomIn: function(num){
      if ( this.currentScale >= 4 ) return false;

      clearTimeout(this.zoomInTimer);
      this.zoomInTimer = setTimeout(function(){
        clearTimeout(slideJsApi.zoomInTimer);
        slideJsApi.currentScale = Math.ceil( (Number(slideJsApi.currentScale) + Number(num)) * 100 ) / 100;
        if ( slideJsApi.currentScale > 4 ) {
          slideJsApi.currentScale = 4;
        }
        slideJsApi.sendCtrlAction("scale");
        slideJsApi.render();
        slideJsApi.resetZoomType();
      }, slideJsApi.zoomInTimeTerm);
    },
    zoomOut: function(num){
      if ( this.currentScale <= 0 ) return false;

      clearTimeout(this.zoomInTimer);
      this.zoomInTimer = setTimeout(function(){
        clearTimeout(slideJsApi.zoomInTimer);
        slideJsApi.currentScale = Math.ceil( (Number(slideJsApi.currentScale) - Number(num)) * 100 ) / 100;
        if ( slideJsApi.currentScale <= num ) {
          slideJsApi.currentScale = num;
        }
        slideJsApi.sendCtrlAction("scale");
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
      data[key] = ( key === "page" ) ? slideJsApi.currentPage : slideJsApi.currentScale ;
      sessionStorage.setItem(key, data[key]); // セッションに格納
      emit("docSendAction", data);
    },
    pageRender: function(){
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
      var frameWidth = $("slideFrame").prop('clientWidth');
      var frameHeight = $("slideFrame").prop('clientHeight');
      /* サイズ調整処理 */
      $(".slide img").css("width", (canvas.clientWidth - 20) * 0.75 + "pt")
                     .css("height", (canvas.clientHeight - 20) * 0.75 + "pt");
      $(".slide").css("width",  canvas.clientWidth + "px").css("height", canvas.clientHeight + "px");
      $(".slide img").css("zoom", slideJsApi.currentScale);
    },
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
    readFile: function(doc){
      doc.url = doc.directory + doc.fileName;
      this.filePath = doc.directory + "svg_" + doc.fileName.replace(/\.pdf$/, "");
      sessionStorage.setItem('doc', JSON.stringify(doc));
      this.doc = doc;
      this.currentPage = (sessionStorage.getItem('page') !== null) ? Number(sessionStorage.getItem('page')) : 1;
      this.currentScale = (sessionStorage.getItem('scale') !== null) ? Number(sessionStorage.getItem('scale')) : 1;
      this.loadedPage = 0;
      this.maxPage = doc.pages;

      var limitPage = (this.currentPage + 3 > this.maxPage) ? this.maxPage : this.currentPage + 3 ;

      var divCanvas = document.createElement("div");
      divCanvas.id = "document_canvas";
      $("slideframe #document_canvas").remove();
      $("slideframe").append(divCanvas);

      $("#pages").remove();
      if ( Number(doc.pagenation_flg) === 1 ) {
        var s = document.createElement("span");
        s.id = "pages";
        document.getElementById('pageNumTag').appendChild(s);
      }

      // ダウンロードファイルの設定
      document.getElementById('downloadBtn').style.display = "none";
      document.getElementById('downloadFilePath').href = "javascript:void(0)";
      if ( Number(doc.download_flg) === 1 ) {
        document.getElementById('downloadBtn').style.display = "";
        document.getElementById('downloadFilePath').href = doc.url;
      }

      this.makePage(); // 初期スライドを作成
      this.init();
      var readPageTimer = setInterval(function(){
        slideJsApi.readPage();
        if ( limitPage < slideJsApi.loadedPage ) {
          clearInterval(readPageTimer);
          slideJsApi.pageRender();
          slideJsApi.render();
        }
      }, 1000);
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
      pages: params.pages,
      directory: params.directory,
      fileName: params.fileName,
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
        slideJsApi.sendPositionAction(); // サイズを企業側へ送る
      }, 500);
    }

    slideJsApi.readFile(doc);

  });

  // 資料変更
  st.on("changeDocument", function(d){
    var obj = JSON.parse(d);
    sessionStorage.setItem('page', 1);
    sessionStorage.setItem('scale', 1);
    slideJsApi.readFile(obj);
  });

  // 同期イベント
  st.on('docSendAction', function(d){
    var obj = JSON.parse(d), cursor;
    if ( obj.hasOwnProperty('scroll') ) {
      slideJsApi.setScrollFlg = true;
      clearTimeout(slideJsApi.setScrollTimer);
      var page = document.getElementById("slide_" + obj.page);
      $('#slide_' + obj.page).animate({
        scrollTop: (page.scrollHeight - page.clientHeight) * obj.scroll.top,
        scrollLeft: (page.scrollWidth - page.clientWidth) * obj.scroll.left,
      }, {
        duration: 100,
        easing: 'swing',
        complete: function(){
          slideJsApi.setScrollTimer = setTimeout(function(){
            slideJsApi.setScrollFlg = false;
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
      slideJsApi.currentScale = obj.scale;
      sessionStorage.setItem('scale', slideJsApi.currentScale); // セッションに格納
      slideJsApi.resetZoomType();
    }
    if ( obj.hasOwnProperty('page') ) {
      if ( slideJsApi.currentPage < obj.page ) {
        slideJsApi.readPage();
      }
      slideJsApi.currentPage = obj.page;
      slideJsApi.pageRender();
    }
    else {
      slideJsApi.render();
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
