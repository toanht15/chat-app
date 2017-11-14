<script type="text/javascript">
// function openConfirmDialog(id){
//   modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'チャット通知設定', 'moment');
//   popupEvent.closePopup = function(){
//     $.ajax({
//       type: 'post',
//         cache: false,
//       data: {
//         id: id
//       },
//      url: "<?= $this->Html->url('/MChatNotifications/remoteDelete') ?>",
//       success: function(){
//        location.href = "<?= $this->Html->url('/MChatNotifications/index') ?>";
//       }
//     });
//   };
// }

//通知設定の削除
function openConfirmDialog(){
  //チェックボックスのチェック状態の取得
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'チャット通知設定', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/MChatNotifications/remoteDelete') ?>",
      success: function(){
        $(".p-dictionary-del #popup-button a").prop("disabled", true);
        var url = "<?= $this->Html->url('/MChatNotifications/index') ?>";
        location.href = url;
      },
      error: function() {
        //debugger;
        console.log('error');
        var url = "<?= $this->Html->url('/MChatNotifications/index') ?>";
        location.href = url;
      }
    });
  });
}

//一度だけ実行
var toExecutableOnce = function(f){
  var called = false, result = undefined;
  return function(){
      if(!called){
          result = f.apply(this, arguments);
          called = true;
      }
      return result;
  };
};

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

document.body.onload = function(){
  // 全選択用チェックボックス
  var allCheckElm = document.querySelectorAll('[id^="allCheck"]');
  for (var i = 0; i < allCheckElm.length; i++) {
    allCheckElm[i].addEventListener('click', setAllCheck); // 全選択
  }

  // チェックボックス群
  var checkBoxList = document.querySelectorAll('[id^="selectTab"]');
  for (var i = 0; i < checkBoxList.length; i++) {
    checkBoxList[i].addEventListener('change', actBtnShow); // 有効無効ボタンの表示切り替え
  }

  // チェックボックスが入っていないtdタグ群
  var clickTargetTds = document.querySelectorAll('td:not(.noClick)');
  for (var i = 0; i < clickTargetTds.length; i++) {
    clickTargetTds[i].addEventListener('click', isCheck); // 対象にチェックを付ける
  }
};

//全選択
var setAllCheck = function() {
  $('input[name^="selectTab"]').prop('checked', this.checked);
  if ( this.checked ) {
    $(".actCtrlBtn").css('display', 'block');
  }
  else {
    $(".actCtrlBtn").css('display', 'none');
  }
  actBtnShow();
}

//全選択用チェックボックスのコントロール
var allCheckCtrl = function(){
  // 全て選択されている場合
  if ( $('input[name="selectTab"]:not(:checked)').length === 0 ) {
    $('input[name="allCheck"]').prop('checked', true);
  }
  else {
    $('input[name="allCheck"]').prop('checked', false);
  }
}

//有効/無効ボタンの表示/非表示
var actBtnShow = function(){
  // 選択中の場合
  if ( $('input[name="selectTab"]').is(":checked") ) {
    //一つでもチェックが入ったら
    //削除ボタン有効
    document.getElementById("chat_notifications_dustbox_btn").className="btn-shadow disOffredBtn";
    document.getElementById("chat_notifications_dustbox_btn").addEventListener('click', openConfirmDialog, false);
  }
  else {
    //一つもチェックが無かったら
    //削除ボタン無効
    document.getElementById("chat_notifications_dustbox_btn").className="btn-shadow disOffgrayBtn";
    document.getElementById("chat_notifications_dustbox_btn").removeEventListener('click', openConfirmDialog, false);
    $('#allCheck').prop('checked', false);
  }
  allCheckCtrl();
};

//行クリックでチェックする
var isCheck = function(){
  allCheckCtrl();
  actBtnShow();
};
</script>
