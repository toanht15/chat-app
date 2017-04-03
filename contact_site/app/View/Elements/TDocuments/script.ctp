<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/compatibility.min.js"); ?>

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
    render: function(){
      var canvas = document.querySelector('slideframe');
      /* サイズ調整処理 */
      $(".slide img").css("width", (canvas.clientWidth - 20) * 0.75 + "pt")
                     .css("height", (canvas.clientHeight - 20) * 0.75 + "pt");
      $(".slide").css("width",  canvas.clientWidth + "px").css("height", canvas.clientHeight + "px");
      $(".slide img").css("transform", "scale(" + slideJsApi.currentScale + ")");
      var docCanvas = document.getElementById('document_canvas');
      docCanvas.style.width = this.maxPage * canvas.clientWidth + "px";
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

      this.loadedPage++;

      if ( !document.querySelector('#slide_' + this.loadedPage) || document.querySelector('#slide_' + this.loadedPage + ' img') ) return false;
      setImage(this.loadedPage); // ページを追加
      slideJsApi.render();
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
      $pages = 1;
      $settings = (array)json_decode($this->data['TDocument']['settings']);
      $pages = (isset($settings['pages'])) ? $settings['pages'] : 1;
      $filePath = C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/svg_".pathinfo(h($this->data['TDocument']['file_name']), PATHINFO_FILENAME);
    ?>

    slideJsApi.init("<?=$filePath?>", "<?=$pages?>");
    <?php endif; ?>
  });
})();

</script>
