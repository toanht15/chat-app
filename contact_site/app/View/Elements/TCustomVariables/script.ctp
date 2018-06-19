<script type="text/javascript">


function openAddDialog(){
  //並べ替えチェックボックスが入っているときはリンク無効とする
  if (!document.getElementById("sort").checked) {
    openEntryDialog({type: 1});
  }else{
    return false;
  }
}
function openEditDialog(id){
  //並べ替えチェックボックスが入っているときはリンク無効とする
  if (!document.getElementById("sort").checked) {
    openEntryDialog({type: 2, id: id});
  }else{
    return false;
  }
}
function openEntryDialog(setting){
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, type:2 => type, id
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/TCustomVariables/remoteOpenEntryForm') ?>",
  }).done(function(html){
    modalOpen.call(window, html, 'p-tcustomvariables-entry', 'カスタム変数設定', 'moment');
  })
}

document.body.onload = function(){
  //ソートタブの準備
  var getSort = function(){
    var list = [];
    $(".sortable tr").each(function(e){
      list.push($(this).data('id'));
    });
    list = $.grep(list, function(e){return e;});
    return JSON.parse(JSON.stringify(list));
  };
  $(document).ready(function(){
    $(".sortable").sortable({
      axis: "y",
      tolerance: "pointer",
      containment: "parent",
      cursor: 'move',
      revert: 100
    });
    $(".sortable").sortable("disable");
  });

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
    document.getElementById("tcustomvariables_copy_btn").classList.remove("disOffgrayBtn");
    document.getElementById("tcustomvariables_copy_btn").classList.add("disOffgreenBtn");
    document.getElementById("tcustomvariables_copy_btn").addEventListener('click', openCopyDialog, false);
    //削除ボタン有効
    document.getElementById("tcustomvariables_dustbox_btn").classList.remove("disOffgrayBtn");
    document.getElementById("tcustomvariables_dustbox_btn").classList.add("disOffredBtn");
    document.getElementById("tcustomvariables_dustbox_btn").addEventListener('click', openConfirmDialog, false);
  }
  else {
    //一つもチェックが無かったら
    //コピーボタン無効
    document.getElementById("tcustomvariables_copy_btn").classList.remove("disOffgreenBtn");
    document.getElementById("tcustomvariables_copy_btn").classList.add("disOffgrayBtn");
    document.getElementById("tcustomvariables_copy_btn").removeEventListener('click', openCopyDialog, false);
    //削除ボタン無効
    document.getElementById("tcustomvariables_dustbox_btn").classList.remove("disOffredBtn");
    document.getElementById("tcustomvariables_dustbox_btn").classList.add("disOffgrayBtn");
    document.getElementById("tcustomvariables_dustbox_btn").removeEventListener('click', openConfirmDialog, false);
    $('#allCheck').prop('checked', false);
  }
  allCheckCtrl();
};

//行クリックでチェックする
var isCheck = function(){
  allCheckCtrl();
  actBtnShow();
};

//カスタム変数の削除
function openConfirmDialog(){
  //チェックボックスのチェック状態の取得
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  modalOpen.call(window, "削除します、よろしいですか？<br><br>（参照設定されているカスタム変数は削除できません）", 'p-confirm', 'カスタム変数', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/TCustomVariables/remoteDeleteUser') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TCustomVariables/index') ?>";
      },
      error: function(){
        console.log('error');
        location.href = "<?= $this->Html->url('/TCustomVariables/index') ?>";
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

//カスタム変数コピー処理
function openCopyDialog(){
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  modalOpen.call(window, "コピーします、よろしいですか？", 'p-confirm', 'カスタム変数', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/TCustomVariables/remoteCopyEntryForm') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TCustomVariables/index') ?>";
      },
      error: function() {
        //debugger;
        console.log('error');
        location.href = "<?= $this->Html->url('/TCustomVariables/index') ?>";
      }
    });
  });
}

//カスタム変数のソートモード
function toggleSort(){
  if (!document.getElementById("sort").checked) {
    confirmSort();
  }
  else {
    $('[id^="selectTab"]').prop('checked', false);
    allCheckCtrl();
    actBtnShow();
    //ソートモードon
    $(".sortable").addClass("move").sortable("enable");
    //カスタム変数ソートモードメッセージ表示
    document.getElementById("sortText").style.display="none";
    document.getElementById("sortTextMessage").style.display="";

    //各ボタン及び動作をモード中は動かなくする
    //カスタム変数登録ボタン押下不可
    document.getElementById("tcustomvariables_add_btn").classList.remove("disOffgreenBtn");
    document.getElementById("tcustomvariables_add_btn").classList.add("disOffgrayBtn");
    //コピーボタン無効
    document.getElementById("tcustomvariables_copy_btn").classList.remove("disOffgreenBtn");
    document.getElementById("tcustomvariables_copy_btn").classList.add("disOffgrayBtn");
    document.getElementById("tcustomvariables_copy_btn").removeEventListener('click', openCopyDialog, false);
    //全て選択チェックボックス選択不可
    document.getElementById('allCheck').disabled = "disabled";
    //項目チェックボックス選択不可
    var checkBoxList = document.querySelectorAll('[id^="selectTab"]');
    for (var i = 0; i < checkBoxList.length; i++) {
      checkBoxList[i].disabled = "disabled";
    }
    $("table tbody.sortable tr td").css('cursor', 'move');
    $("table tbody.sortable tr td a").css('cursor', 'move');
  }
}

//カスタム変数のソート順を保存
var confirmSort = function(){
  modalOpen.call(window, "編集内容を保存します。<br/><br/>よろしいですか？<br/>", 'p-sort-save-confirm', 'カスタム変数並び替えの保存', 'moment');
  popupEvent.saveClicked = function(){
    saveToggleSort();
  }
  popupEvent.cancelClicked = function(){
    var url = "<?= $this->Html->url('/TCustomVariables/index') ?>";
    location.href = url;
  }
  $(".p-sort-save-confirm #popupCloseBtn").click(function(){
    $("#sort").prop('checked', true);
  });
};

//カスタム変数ソートを保存
var saveToggleSort = toExecutableOnce(function(){
  var list = getSort();
  var sortNolist = getSortNo();
  $.ajax({
    type: "POST",
    url: "<?= $this->Html->url(['controller' => 'TCustomVariables', 'action' => 'remoteSaveSort']) ?>",
    data: {
      list : list,
      sortNolist : sortNolist
    },
    dataType: "html",
    success: function(){
      var url = "<?= $this->Html->url('/TCustomVariables/index') ?>";
      location.href = url;
    }
  });
});

  //カスタム変数のソート順を取得
  var getSort = function(){
    var list = [];
    $(".sortable tr").each(function(e){
      list.push($(this).data('id'));
    });
    list = $.grep(list, function(e){return e;});
    return JSON.parse(JSON.stringify(list));
  };

  var getSortNo = function(){
      var sortlist = [];
      $(".sortable tr").each(function(e){
        sortlist.push($(this).data('sort'));
      });
      sortlist = $.grep(sortlist, function(e){return e;});
      //console.log(sortlist);この時点ではソート順を取得できている(後で消す)
      return JSON.parse(JSON.stringify(sortlist));
  };
</script>
