<script type="text/javascript">
function openConfirmDialog(id){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'チャット通知設定', 'moment');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
        cache: false,
      data: {
        id: id
      },
      url: "<?= $this->Html->url('/MChatNotifications/remoteDelete') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/MChatNotifications/index') ?>";
      }
    });
  };
}

function showGallary(){
  $.ajax({
    type: 'post',
    cache: false,
    dataType: 'html',
    url: "<?= $this->Html->url('/MChatNotifications/remoteShowGallary') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-show-notification-gallary', 'ギャラリー', 'moment');
      popupEvent.customizeBtn = function(name){
        changeImagePath("/img/<?=C_PATH_NOTIFICATION_IMG_DIR?>" + name, name);
        popupEvent.close();
      };
    }
  });
}

function saveAct(){
  document.forms[0].submit();
}

function changeImagePath(path, fileName){
  var image = document.querySelector('#picDiv img');
  image.src = path;
  var imageData = document.getElementById('MChatNotificationMainImage');
  imageData.value = fileName;
}

$(document).ready(function(){
  $('#MChatNotificationUploadImage').change(function(e){
      var files = e.target.files;
      if ( window.URL && files.length > 0 ) {
          var file = files[files.length-1];
          // 2MB以下である
          if (file.size > 2000000) {
              $("#MChatNotificationUploadImage").val("");
              return false;
          }
          // jpeg/jpg/png
          var reg = new  RegExp(/image\/(png|jpeg|jpg)/i);
          if ( !reg.exec(file.type) ) {
              $("#MChatNotificationUploadImage").val("");
              return false;
          }
          var url = window.URL.createObjectURL(file);
          changeImagePath(url, file.name);
      }
  });
});
</script>
