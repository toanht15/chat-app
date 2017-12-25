<?php
App::uses('LandscapeCodeMapper', 'Vendor/Util/Landscape');
?>

<script type="text/javascript">
  $(function() {
    function toggleViewArea() {
      var menuAreaDisplay = $('#fileUploadMenuArea').css('display'),
          confirmAreaDisplay = $('#fileUploadConfirmArea').css('display');
      $('#fileUploadMenuArea').css('display', confirmAreaDisplay);
      $('#fileUploadConfirmArea').css('display', menuAreaDisplay);
      // ポップアップのボタン表示エリアの表示制御
      $('#popup-button').css('display', menuAreaDisplay);
      popupEvent.resize();
    }

    var droppable = $("#fileDropArea");
    var selectFileBtn = $('#fileSelectArea');
    var selectInput = $('#selectFileInput');
    var fileObj = null;
    var loadData = null;

    var allowExtensions = <?= json_encode($allowExtensions); ?>;

    // File API が使用できない場合は諦めます.
    if(!window.FileReader) {
      $("#fileDropArea").css('display', 'none');
    }

    // イベントをキャンセルするハンドラです.
    var enterEvent = function(event) {
      event.preventDefault();
      event.stopPropagation();
      return false;
    };

    // イベントをキャンセルするハンドラです.
    var overEvent = function(event) {
      hideInvalidError();
      $(this).addClass("hovering");
      event.preventDefault();
      event.stopPropagation();
      return false;
    };

    // イベントをキャンセルするハンドラです.
    var leaveEvent = function(event) {
      $(this).removeClass("hovering");
      return false;
    }

    var validExtension = function(filename) {
      var split = filename.split(".");
      var targetExtension = split[split.length-1];
      return $.inArray(targetExtension, allowExtensions) >= 0
    };

    showInvalidError = function() {
      $('#fileUploadError').css('display','inline-block');
      popupEvent.resize();
    };

    hideInvalidError = function() {
      $('#fileUploadError').css('display','none');
      popupEvent.resize();
    };

    // dragenter, dragover イベントのデフォルト処理をキャンセルします.
    droppable.on("dragenter", enterEvent);
    droppable.on("dragover", overEvent);
    droppable.on("dragleave", leaveEvent);

    selectFileBtn.on('click', function(event){
      selectInput.trigger('click');
    });

    selectInput.on("click", function(event){
      hideInvalidError();
      $(this).val(null);
    }).on("change",function(event){
      if(selectInput[0].files[0]) {
        fileObj = selectInput[0].files[0];
        // ファイルの内容は FileReader で読み込みます.
        var fileReader = new FileReader();
        fileReader.onload = function (event) {
          if(!validExtension(fileObj.name)) {
            showInvalidError();
            return;
          }
          // event.target.result に読み込んだファイルの内容が入っています.
          // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
          $('#fileUploadConfirmArea').html("【" + fileObj.name + "】をアップロードします。<br>よろしいですか？");
          loadData = event.target.result;
          toggleViewArea();
        };
        fileReader.readAsArrayBuffer(fileObj);
      }
    });

    // ドロップ時のイベントハンドラを設定します.
    var handleDroppedFile = function(event) {
      hideInvalidError();
      // ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
      fileObj = event.originalEvent.dataTransfer.files[0];

      // ファイルの内容は FileReader で読み込みます.
      var fileReader = new FileReader();
      fileReader.onload = function(event) {
        if(!validExtension(fileObj.name)) {
          showInvalidError();
          return;
        }
        // event.target.result に読み込んだファイルの内容が入っています.
        // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
        $('#fileUploadConfirmArea').html("【"+fileObj.name+"】をアップロードします。<br>よろしいですか？");
        loadData = event.target.result;
        toggleViewArea();

      }
      fileReader.readAsArrayBuffer(fileObj);

      // デフォルトの処理をキャンセルします.
      enterEvent(event);
      return false;
    }

    // ドロップ時のイベントハンドラを設定します.
    droppable.bind("drop", handleDroppedFile);

    // override
    popupEvent.closePopup = function() {
      angular.element('#customer_idx').scope().uploadFile(fileObj, loadData);
      popupEvent.close();
    }
  });
</script>
<div id="fileUploadPopupContent">
  <div id="fileUploadMenuArea">
    <div class="upload-select-menu" id="fileDropArea">
      <?= $this->Html->image('file.png', array('alt' => 'CakePHP', 'width' => '250', 'height' => '250')); ?>
      <span>送信するファイルをここにドロップしてください</span>
    </div>
    <div class="upload-select-menu" id="fileSelectArea">
      <span>ダイアログを表示してファイルを選択する</span>
    </div>
    <span class="error-message" id="fileUploadError">指定のファイルは送信を許可されていません。</span>
  </div>
  <div id="fileUploadConfirmArea" style="display:none;">
  </div>
  <input type="file" id="selectFileInput" name="uploadFile" style="display:none "/>
</div>