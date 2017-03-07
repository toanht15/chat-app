<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/pdf.min.js"); ?>
<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/compatibility.min.js"); ?>

<script type="text/javascript">
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
      pdfjsApi.pdfUrl = e.target.result;
      pdfjsApi.init();
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
  document.getElementById('TDocumentManuscript').value = JSON.stringify(pdfjsApi.manuscript);
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

//タグリスト表示
$(function(){
  $('#tagList').multiSelect({});
});

<?php
$manuscript = (!empty($this->data['TDocument']['manuscript'])) ? $this->data['TDocument']['manuscript'] : '{}';
?>

var pdfjsApi, pdfjsCNST;

(function(){
  pdfjsCNST = function(){
    return {
      FIRST_PAGE: "最初のページ",
      LAST_PAGE: "最後のページ",
    };
  };

  PDFJS.workerSrc = "<?=C_PATH_NODE_FILE_SERVER?>/websocket/pdf.worker.min.js";

  pdfjsApi = {
    cnst: new pdfjsCNST(),
    pdf: null,
    pdfUrl: null,
    currentPage: 1,
    currentScale: 1,
    rotation: 0,
    manuscript: <?=$manuscript?>,
    init: function(){
      pdfjsApi.currentPage = 1;
      // 原稿
      var textarea = document.getElementById('pages-text');
      if ( pdfjsApi.currentPage in pdfjsApi.manuscript ) {
        textarea.value = pdfjsApi.manuscript[pdfjsApi.currentPage];
      }
      this.showpage();

      textarea.addEventListener('blur',function(e){
        if ( pdfjsApi.manuscript.hasOwnProperty(pdfjsApi.currentPage) && pdfjsApi.manuscript[pdfjsApi.currentPage] === e.target.value ) {
          return false; // 変わっていない
        }
        if ( !pdfjsApi.manuscript.hasOwnProperty(pdfjsApi.currentPage) && e.target.value === "" ) {
          return false; // 書いていない
        }
        pdfjsApi.manuscript[pdfjsApi.currentPage] = e.target.value;
      });
    },
    setManuscript: function(){
      document.getElementById('pages-text').value = ( pdfjsApi.manuscript.hasOwnProperty(pdfjsApi.currentPage) ) ? pdfjsApi.manuscript[pdfjsApi.currentPage] : "";
    },
    prevPage: function(){
      if ( this.currentPage < 2 ) return this.notificate('FIRST_PAGE');
      this.currentPage--;
      this.setManuscript();
      this.showpage();
    },
    nextPage: function(){
      if ( this.currentPage >= this.pdf.pdfInfo.numPages ) return this.notificate('LAST_PAGE');
      this.currentPage++;
      this.setManuscript();
      this.showpage();
    },
    rotate: function(){
      setTimeout(function(){
        pdfjsApi.rotation += 90;
        if ( pdfjsApi.rotation === 360 ) {
          pdfjsApi.rotation = 0;
        }
        pdfjsApi.showpage();
      }, 0);
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

      function fitWindow(page, rotation) {
        var viewport = page.getViewport(1), widthScale, heightScale;
        if ( Number(rotate) === 90 || Number(rotate) === 270 ) {
          heightScale = canvasFrame.clientHeight/viewport.width;
          widthScale = canvasFrame.clientWidth/viewport.height;
        }
        else {
          widthScale = canvasFrame.clientWidth/viewport.width;
          heightScale = canvasFrame.clientHeight/viewport.height;
        }
        var scale = ( widthScale > heightScale ) ? heightScale : widthScale;
        return page.getViewport(scale * pdfjsApi.currentScale, rotation);
      }

      if ( pdfjsApi.page === undefined ) {
        return false;
      }
      var page = pdfjsApi.page;
      var rotate = pdfjsApi.rotation;

      // Fetch canvas' 2d context
      var viewport = fitWindow(page, rotate);
      // Set dimensions to Canvas

      pdfjsApi.canvas.height = viewport.height;
      pdfjsApi.canvas.width = viewport.width;
      // Set Margin
      var calc = ((canvasFrame.clientHeight - 40 - viewport.height) > 0) ? (canvasFrame.clientHeight - 40 - viewport.height)/2 : 0;
      canvasFrame.style.paddingTop = String(calc) + "px";

      setTimeout(function(){
        // Render PDF page
        page.render({
          canvasContext: pdfjsApi.canvas.getContext('2d'),
          viewport: viewport
        }).then(function(){
          $('.pages').text("（" + pdfjsApi.currentPage + "/ " + pdfjsApi.pdf.pdfInfo.numPages + "）");
          pdfjsApi.canvas.style.opacity = 1;
        });
      }, 0);
    },
    notificate: function(code){
      if ( this.cnst.hasOwnProperty(code) ) {
        console.log(this.cnst[code]);
      }
    }
  };

  <?php if ( !empty($this->data['TDocument']['file_name']) ):
    $filePath = C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/".h($this->data['TDocument']['file_name']);
  ?>
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?=$filePath?>', true);
  xhr.responseType = 'arraybuffer';
  xhr.onload = function(e) {
    if (this.status == 200) {
      pdfjsApi.pdfUrl = new Uint8Array(this.response);
      pdfjsApi.init();
    }
  };
  xhr.send();
  <?php endif; ?>
})();

function remoteOpenPreview(){
  console.log('jajaja');
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
</script>
