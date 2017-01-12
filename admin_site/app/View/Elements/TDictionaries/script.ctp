<script type="text/javascript">
'use strict';
//登録ボタンから
function openConfirm(){
  openEntryDialog({type: 1});
}

//更新ボタンから
function openEditDialog(id){
  openEntryDialog({type: 2, id: id});
}

function openEntryDialog(setting){
  var type = setting.type;
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, type:2 => type, id
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/TDictionaries/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-tdictionary-entry', '簡易入力メッセージ');
    }
  });
}

//checkbox押したとき
function toggleSort(){
  if ( $(".sortable").is(".move") ) {
    $(".sortable").removeClass("move").sortable("disable");
    $("#sortMessage").text("");
    $("#sortToggleBtn").removeClass("redBtn").addClass("greenBtn");
    var list = getSort();
    $.ajax({
      type: "POST",
      url: "<?= $this->Html->url(['controller' => 'TDictionaries', 'action' => 'remoteSaveSort']) ?>",
      data: {
        list : list
      },
      dataType: "html",
      success: function(){
        location.href = location.href;
      }
    });
  }
  else {
    $(".sortable").addClass("move").sortable("enable");
    $("#sortMessage").text("　(！) 並び順修正中（保存する際は再びチェックボックスをクリックしてください）");
    $("#sortToggleBtn").removeClass("greenBtn").addClass("redBtn");
  }
}

//一覧画面削除機能
function removeAct(id){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', '簡易入力メッセージ');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id:id
      },
      cache: false,
      url: "<?= $this->Html->url('/TDictionaries/remoteDelete') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TDictionaries/index') ?>";
      }
    });
  };
}

var getSort = function(){
  var list = [];
  $(".sortable tr").each(function(e){
    list.push($(this).data('id'));
  });
  return JSON.parse(JSON.stringify(list));
};

$(function(){
  $(".sortable").sortable();
  $(".sortable").sortable("disable");
});

</script>