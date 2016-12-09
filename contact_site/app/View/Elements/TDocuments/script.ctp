<?= $this->Html->script(C_PATH_NODE_FILE_SERVER."/websocket/pdf.min.js"); ?>

<script type="text/javascript">
//タグ追加
function tagAdd(){
  var tag = $('#TDocumentNewTag').val();
  $('#MDocumentTagName').val(tag);
  document.getElementById('MDocumentTagAddForm').submit();
}

//保存機能
function saveAct(){
 document.getElementById('TDocumentEntryForm').submit();
}

//一覧画面削除機能
function removeAct(id){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
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
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
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
  $('#tagList').multiSelect({
  });
});


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
    init: function(){
      this.showpage();
      // キープレス
      window.addEventListener('keydown',function(e){
        if ( e.keyCode === 37 || e.keyCode === 38 ) {
          pdfjsApi.prevPage();
        }
        else if ( e.keyCode === 39 || e.keyCode === 40 ) {
          pdfjsApi.nextPage();
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
      var calc = ((canvasFrame.clientHeight - 40 - viewport.height) > 0) ? (canvasFrame.clientHeight - 40 - viewport.height)/2 : 0;
      canvasFrame.style.paddingTop = String(calc) + "px";

      setTimeout(function(){
        // Render PDF page
        page.render({
          canvasContext: pdfjsApi.canvas.getContext('2d'),
          viewport: viewport
        }).then(function(){
            $('.pages').text(pdfjsApi.currentPage + "/ " + pdfjsApi.pdf.pdfInfo.numPages);
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
    $filePath = "https://s3-".C_AWS_S3_REGION.".amazonaws.com/".C_AWS_S3_BUCKET."/medialink/".$this->data['TDocument']['file_name'];
  ?>
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '<?=$filePath?>', true);
  xhr.responseType = 'arraybuffer';
  xhr.onload = function(e) {
    if (this.status == 200) {
      // Note: .response instead of .responseText
      var blob = new Blob([this.response], {type: 'application/pdf'});
      pdfjsApi.pdfUrl = URL.createObjectURL(blob);
      pdfjsApi.init();
    }
  };
  xhr.send();
  <?php endif; ?>
})();

</script>
