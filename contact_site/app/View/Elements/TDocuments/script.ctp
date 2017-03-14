<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/pdf.min.js"); ?>
<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/compatibility.min.js"); ?>

<script type="text/javascript">
<?= $this->element('TDocuments/loadScreen'); ?>
<?php if ( $this->action !== "index" ) : ?>
function handleFileSelect(evt) {
  var files = evt.target.files; // FileList object
  var file = files[0];
  if (file.type !== 'application/pdf') {
    return false;
  }
  var reader = new FileReader();
  reader.onload = (function(theFile) {
    return function(e) {
      // slideJsApi.init();
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
  document.getElementById('TDocumentManuscript').value = JSON.stringify(slideJsApi.manuscript);
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
        location.href = "<?= $this->Html->url('/TDocuments/index') ?>";
      }
    });
  };
}

//資料共有ポップアッププレビュー
function remoteOpenPreview(){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'TDocuments', 'action' => 'openPreview']) ?>",
    success: function(html){
      modalOpen.call(window, html,'p-tdocument-preview','', 'moment');
    }
  });
}

//タグリスト表示
$(function(){
  $('#tagList').multiSelect({});
});

<?php
$manuscript = (!empty($this->data['TDocument']['manuscript'])) ? $this->data['TDocument']['manuscript'] : '{}';
?>

var slideJsApi, slideJsCNST;

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
    init: function(filePath, page){
      this.currentPage = 1;
      this.loadedPage = 0;
      this.maxPage = page;
      this.filePath = filePath;
      var limitPage = (this.currentPage + 3 > this.maxPage) ? this.maxPage : this.currentPage + 3 ;

      var canvas = document.getElementById('document_canvas');
      var readPageTimer = setInterval(function(){
        slideJsApi.readPage();
        slideJsApi.showPage();
        if ( limitPage < slideJsApi.loadedPage ) {
          clearInterval(readPageTimer);
        }
      }, 1000);

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
    init2: function(){
      console.log('hahahha');
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
        this.rotation += 90;
        if ( this.rotation === 360 ) {
          this.rotation = 0;
        }
        slideJsApi.showPage();
      }, 0);
    },
    showPage: function(){
      var canvas = document.getElementById('document_canvas');
      canvas.style.left = -22.5 * (slideJsApi.currentPage - 1) + "em";
      this.setManuscript();
      $('.pages').text("（" + slideJsApi.currentPage + "/ " + slideJsApi.maxPage + "）");
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
      console.log('manuscriptaaaaaaaa');
      console.log((slideJsApi.manuscript));
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
    makePage: function(){
      console.log('makePage');
      var docCanvas = document.getElementById('document_canvas');
      console.log('docCanvas');
      console.log(docCanvas);
      // 現在の表示ページから作っていく
      for(var i = 1; this.maxPage >= i; i++){
        console.log('入ってる');
        var slide = document.createElement('div');
        slide.id = "slide_" + i;
        slide.classList.add("slide");
        slide.addEventListener('scroll', function(){
          slideJsApi.scrollFunc();
        });
        docCanvas.appendChild(slide);
        console.log(docCanvas);
      }
      slideJsApi.render();
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
    readFile: function(doc, callback){
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
      this.init2();

      var readPageTimer = setInterval(function(){
        slideJsApi.readPage2();
        if ( limitPage < slideJsApi.loadedPage ) {
          clearInterval(readPageTimer);
          slideJsApi.pageRender();
          slideJsApi.render();
          callback(false);
        }
      }, 1000);
    },
    readPage: function(){
      console.log('本を読む');
      var docCanvas = document.getElementById('document_canvas'),
          html = "",
          slide = document.createElement('object');

      this.loadedPage++;

      if ( this.loadedPage > this.maxPage ) return false;

      slide.id = "slide_" + slideJsApi.loadedPage;
      slide.classList.add("slide");
      slide.data = slideJsApi.filePath + "_" + Number(slideJsApi.loadedPage) + '.svg';
      slide.type="image/svg+xml";
      slide.style.height="12em";
      slide.style.width="22.5em";
      docCanvas.appendChild(slide);

    },
    readPage2: function(){
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
    notificate: function(code){
      if ( this.cnst.hasOwnProperty(code) ) {
        console.log(this.cnst[code]);
      }
    }
  };

  $(document).ready(function(){
    <?php if ( !empty($this->data['TDocument']['file_name']) ):
      $settings = json_decode($this->data['TDocument']['settings']);
      $filePath = C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/svg_".pathinfo(h($this->data['TDocument']['file_name']), PATHINFO_FILENAME);
    ?>

    slideJsApi.init("<?=$filePath?>", "<?=$settings->pages?>");
    <?php endif; ?>
  });
})();

</script>
