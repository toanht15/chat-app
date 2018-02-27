<script type="text/javascript">
function openAddDialog(){
  //現在のページ番号
  var index = Number("<?= $this->Paginator->params()["page"] ?>");
  openEntryDialog({type: 1, index: index});
}
function openEditDialog(id){
  //現在のページ番号
  var index = Number("<?= $this->Paginator->params()["page"] ?>");
  openEntryDialog({type: 2, id: id, index: index});
}
function openEntryDialog(setting){
  var type = setting.type;
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, type:2 => type, id
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/MUsers/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-muser-entry', 'ユーザー情報', 'moment');
    },
    error: function(html) {
      console.log('error');
    }
  });
}
// function openConfirmDialog(id){
//   modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'ユーザー情報', 'moment');
//   popupEvent.closePopup = function(){
//     $.ajax({
//       type: 'post',
//       cache: false,
//       data: {
//         id: id
//       },
//      url: "<?= $this->Html->url('/MUsers/remoteDeleteUser') ?>",
//       success: function(){
//        location.href = "<?= $this->Html->url('/MUsers/index') ?>";
//       }
//     });
//   };
// }

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
  var dustboxBtn = document.getElementById("m_users_dustbox_btn");
  // 選択中の場合
  if ( $('input[name="selectTab"]').is(":checked") ) {
    //一つでもチェックが入ったら
    //削除ボタン有効
    dustboxBtn.classList.remove('disOffgrayBtn');
    dustboxBtn.classList.add('disOffredBtn');
    dustboxBtn.addEventListener('click', openConfirmDialog, false);
  }
  else {
    //一つもチェックが無かったら
    //削除ボタン無効
    dustboxBtn.classList.remove('disOffredBtn');
    dustboxBtn.classList.add('disOffgrayBtn');
    dustboxBtn.removeEventListener('click', openConfirmDialog, false);
    $('#allCheck').prop('checked', false);
  }
  allCheckCtrl();
};

//行クリックでチェックする
var isCheck = function(){
  allCheckCtrl();
  actBtnShow();
};

//ユーザの削除
function openConfirmDialog(){
  //チェックボックスのチェック状態の取得
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  //現在のページ番号
  var index = Number("<?= $this->Paginator->params()["page"] ?>");
  //現在表示しているレコードの数
  var current = Number("<?= $this->Paginator->params()["current"] ?>");
  //削除されるレコードの数
  var countList = Number(list.length);
  //現在表示されているレコードより多く削除されかつページ番号が2以上だったらページを一つ戻す
  if(countList >= current && index > 1){
    index = index - 1;
  }
  //modalOpen.call(window, "選択された定型文を削除します。<br/><br/>よろしいですか？<br/>", 'p-dictionary-del', '削除', 'moment');
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'ユーザー情報', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList,
        index: index
      },
      url: "<?= $this->Html->url('/MUsers/remoteDeleteUser') ?>",
      success: function(){
        $(".p-dictionary-del #popup-button a").prop("disabled", true);
        var url = "<?= $this->Html->url('/MUsers/index') ?>";
        location.href = url + "/page:" + index;
      },
      error: function() {
        //debugger;
        console.log('error');
        TabIndex = document.getElementById("select_tab_index").value;
        var url = "<?= $this->Html->url('/MUsers/index') ?>";
        location.href = url + "/page:" + 1;
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
</script>
