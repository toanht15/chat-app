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
    readPage: function(){
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
