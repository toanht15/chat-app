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
    var fileObj = null;
    var loadData = null;

    // File API が使用できない場合は諦めます.
    if(!window.FileReader) {
      $("#fileDropArea").css('display', 'none');
    }

    // イベントをキャンセルするハンドラです.
    var cancelEvent = function(event) {
      event.preventDefault();
      event.stopPropagation();
      return false;
    }

    // dragenter, dragover イベントのデフォルト処理をキャンセルします.
    droppable.on("dragenter", cancelEvent);
    droppable.on("dragover", cancelEvent);

    // ドロップ時のイベントハンドラを設定します.
    var handleDroppedFile = function(event) {
      // ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
      fileObj = event.originalEvent.dataTransfer.files[0];

      // ファイルの内容は FileReader で読み込みます.
      var fileReader = new FileReader();
      fileReader.onload = function(event) {
        // event.target.result に読み込んだファイルの内容が入っています.
        // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
        $('#fileUploadConfirmArea').html("【"+fileObj.name+"】をアップロードします。<br>よろしいですか？");
        loadData = event.target.result;
        toggleViewArea();

      }
      fileReader.readAsArrayBuffer(fileObj);

      // デフォルトの処理をキャンセルします.
      cancelEvent(event);
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
    <div id="fileDropArea">
      <?= $this->Html->image('file.png', array('alt' => 'CakePHP', 'width' => '250', 'height' => '250')); ?>
      <span>送信するファイルをここにドロップしてください</span>
    </div>
  </div>
  <div id="fileUploadConfirmArea" style="display:none;">
  </div>
</div>