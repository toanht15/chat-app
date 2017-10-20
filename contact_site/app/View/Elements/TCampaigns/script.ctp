<script type="text/javascript">
function openAddDialog(){
  openEntryDialog({type: 1});
}
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
    url: "<?= $this->Html->url('/TCampaigns/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-tcampaign-entry', 'キャンペーン情報', 'moment');
    }
  });
}

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
    //コピーボタン有効
    document.getElementById("tcampaigns_copy_btn").className="btn-shadow disOffgreenBtn";
    document.getElementById("tcampaigns_copy_btn").addEventListener('click', openCopyDialog, false);
    //削除ボタン有効
    document.getElementById("tcampaigns_dustbox_btn").className="btn-shadow disOffredBtn";
    document.getElementById("tcampaigns_dustbox_btn").addEventListener('click', openConfirmDialog, false);
  }
  else {
    //一つもチェックが無かったら
    //コピーボタン無効
    document.getElementById("tcampaigns_copy_btn").className="btn-shadow disOffgrayBtn";
    document.getElementById("tcampaigns_copy_btn").removeEventListener('click', openCopyDialog, false);
    //削除ボタン無効
    document.getElementById("tcampaigns_dustbox_btn").className="btn-shadow disOffgrayBtn";
    document.getElementById("tcampaigns_dustbox_btn").removeEventListener('click', openConfirmDialog, false);
    $('#allCheck').prop('checked', false);
  }
  allCheckCtrl();
};

//行クリックでチェックする
var isCheck = function(){
  allCheckCtrl();
  actBtnShow();
};

//キャンペーンの削除
function openConfirmDialog(){
  //チェックボックスのチェック状態の取得
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'キャンペーン情報', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/TCampaigns/remoteDeleteUser') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TCampaigns/index') ?>";
      },
      error: function() {
        //debugger;
        console.log('error');
        location.href = "<?= $this->Html->url('/TCampaigns/index') ?>";
      }
    });
  });
};

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

//キャンペーンコピー処理
function openCopyDialog(){
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  modalOpen.call(window, "コピーします、よろしいですか？", 'p-confirm', 'キャンペーン情報', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/TCampaigns/remoteCopyEntryForm') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TCampaigns/index') ?>";
      },
      error: function() {
        //debugger;
        console.log('error');
        location.href = "<?= $this->Html->url('/TCampaigns/index') ?>";
      }
    });
  });
}

</script>
