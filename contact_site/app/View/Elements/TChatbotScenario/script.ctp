<script type="text/javascript">
document.body.onload = function(){
  $(document).ready(function(){
    $(".sortable").sortable({
      axis: "y",
      tolerance: "pointer",
      containment: "parent",
      cursor: 'move',
      cancel: '.sortable .cancel',
      revert: 100
    });
    $(".sortable").sortable("disable");
  });

  // 全選択用チェックボックス
  var allCheckElm = document.getElementById('allCheck');
  allCheckElm.addEventListener('click', setAllCheck); // 全選択

  // チェックボックス群
  var checkBoxList = document.querySelectorAll('#tchatbotscenario_list [id^="selectTab"]');
  angular.forEach(checkBoxList, function(elm) {
    elm.addEventListener('change', actBtnShow); // 有効無効ボタンの表示切り替え
  });
};

// リストの全選択・選択解除
var setAllCheck = function() {
  $('#tchatbotscenario_list input[name="selectTab"]').prop('checked', this.checked);
  document.querySelector('#tchatbotscenario_list input[name="allCheck"]').checked = this.checked;
  actBtnShow();
}

// 全選択用チェックボックスのコントロール
var allCheckCtrl = function(){
  // 全て選択されている場合
  var allCheck = document.querySelector('#tchatbotscenario_list input[name="allCheck"]');
  if (!!document.querySelector('#tchatbotscenario_list input[name="selectTab"]')) {
    allCheck.checked = !document.querySelector('#tchatbotscenario_list input[name="selectTab"]:not(:checked)');
  }
}

// コピー・削除ボタンの有効・無効切り替え
var actBtnShow = function(){
  var copyBtn = document.getElementById("tchatbotscenario_copy_btn");
  var dustBtn = document.getElementById("tchatbotscenario_dustbox_btn");

  if (!!document.querySelector('#tchatbotscenario_list input[name="selectTab"]:checked')) {
    // 一つでもチェックが入ったら、コピー・削除ボタンを有効にする
    copyBtn.classList.remove('disOffgrayBtn');
    copyBtn.classList.add('disOffgreenBtn');
    copyBtn.addEventListener('click', openCopyDialog, false);
    dustBtn.classList.remove('disOffgrayBtn');
    dustBtn.classList.add('disOffredBtn');
    dustBtn.addEventListener('click', openConfirmDialog, false);
  }
  else {
    // 一つもチェックが無かったら、コピー・削除ボタンを無効にする
    copyBtn.classList.remove('disOffgreenBtn');
    copyBtn.classList.add('disOffgrayBtn');
    copyBtn.removeEventListener('click', openCopyDialog, false);
    dustBtn.classList.remove('disOffredBtn');
    dustBtn.classList.add('disOffgrayBtn');
    dustBtn.removeEventListener('click', openConfirmDialog, false);
  }
  allCheckCtrl();
};

//シナリオ設定の削除
function openConfirmDialog(){
  //チェックボックスのチェック状態の取得
  var list = Array.prototype.slice.call(document.querySelectorAll('#tchatbotscenario_list input[name^="selectTab"]:checked'), 0);
  var selectedList = list.map(function(elm) {
    return Number(elm.value);
  });
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
  modalOpen.call(window, "削除します、よろしいですか？<br><br>（呼び出し元が設定されているシナリオは削除できません）", 'p-confirm', 'シナリオ設定', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/TChatbotScenario/chkRemoteDelete') ?>",
      success: function(){
        $(".p-dictionary-del #popup-button a").prop("disabled", true);
        var url = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
        location.href = url + "/page:" + index;
      },
      error: function() {
        //debugger;
        console.log('error');
        TabIndex = document.getElementById("select_tab_index").value;
        var url = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
        location.href = url + "/page:" + 1;
      }
    });
  });
}

//シナリオコピー処理
function openCopyDialog(){
  var list = Array.prototype.slice.call(document.querySelectorAll('#tchatbotscenario_list input[name^="selectTab"]:checked'), 0);
  var selectedList = list.map(function(elm) {
    return Number(elm.value);
  });

  modalOpen.call(window, "コピーします、よろしいですか？", 'p-confirm', 'シナリオ設定設定', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/TChatbotScenario/remoteCopyEntryForm') ?>",
      success: function(){
        //現在のページ番号
        var index = Number("<?= $this->Paginator->params()["page"] ?>");
        var url = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
        location.href = url + "/page:" + index;
      },
      error: function() {
        console.log('error');
        location.href = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
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

//シナリオ新規追加
function openAdd(){
  //並べ替えチェックボックスがONのときはリンク無効とする
  if (!document.getElementById("sort").checked) {
    //現在のページ番号
    var index = Number("<?= $this->Paginator->params()["page"] ?>");
    var url = "<?= $this->Html->url('/TChatbotScenario/add') ?>";
    location.href = url + "?lastpage="+index;
  }
  else{
    return false;
  }
}

//シナリオ編集
function openEdit(id){
  //シナリオ設定並べ替えチェックボックスが入っているときはリンク無効とする
  if (!document.getElementById("sort").checked) {
    //現在のページ番号
    var index = Number("<?= $this->Paginator->params()["page"] ?>");
    var url = "<?= $this->Html->url('/TChatbotScenario/edit') ?>";
    location.href = url + "/" + id + "?lastpage="+index;
  }
  else{
    return false;
  }
}

//シナリオ設定のソートモード
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
    //資料設定ソートモードメッセージ表示
    document.getElementById("sortText").style.display="none";
    document.getElementById("sortTextMessage").style.display="";

    //各ボタン及び動作をモード中は動かなくする
    //シナリオ設定登録ボタン押下不可
    var addBtn = document.getElementById('tchatbotscenario_add_btn');
    addBtn.classList.remove('disOffgreenBtn');
    addBtn.classList.add('disOffgrayBtn');
    //全て選択チェックボックス選択不可
    document.getElementById('allCheck').disabled = "disabled";
    //項目チェックボックス選択不可
    var checkBoxList = document.querySelectorAll('[id^="selectTab"]');
    angular.forEach(checkBoxList, function(elm) {
      elm.disabled = "disabled";
    })
    $("table tbody.sortable tr td").css('cursor', 'move');
    $("table tbody.sortable tr td a").css('cursor', 'move');
  }
}

//シナリオ設定のソート順を保存
var confirmSort = function(){
  modalOpen.call(window, "編集内容を保存します。<br/><br/>よろしいですか？<br/>", 'p-sort-save-confirm', 'シナリオ設定並び替えの保存', 'moment');
  popupEvent.saveClicked = function(){
    saveToggleSort();
  }
  popupEvent.cancelClicked = function(){
    var url = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
    location.href = url;
  }
  $(".p-sort-save-confirm #popupCloseBtn").click(function(){
    $("#sort").prop('checked', true);
  });
};

//シナリオ設定ソートを保存
var saveToggleSort = toExecutableOnce(function(){
  var list = getSort();
  var sortNolist = getSortNo();
  $.ajax({
    type: "POST",
    url: "<?= $this->Html->url(['controller' => 'TChatbotScenario', 'action' => 'remoteSaveSort']) ?>",
    data: {
      list : list,
      sortNolist: sortNolist
    },
    dataType: "html",
    success: function(){
      //現在のページ番号
      var index = Number("<?= $this->Paginator->params()["page"] ?>");
      var url = "<?= $this->Html->url('/TChatbotScenario/index') ?>";
      location.href = url + "/page:" + index;
    }
  });
});

// ソートタブの準備
var getSort = function(){
  var scenarioList = Array.prototype.slice.call(document.querySelectorAll("#tchatbotscenario_list .sortable tr"), 0);
  var sortlist = scenarioList.map(function(elm) {
    return elm.dataset.id;
  }).filter(function(elm) {
    return elm;
  });
  return JSON.parse(JSON.stringify(sortlist));
};

var getSortNo = function(){
  var scenarioList = Array.prototype.slice.call(document.querySelectorAll("#tchatbotscenario_list .sortable tr"), 0);
  var sortlist = scenarioList.map(function(elm) {
    return elm.dataset.sort;
  }).filter(function(elm) {
    return elm;
  });
  return JSON.parse(JSON.stringify(sortlist));
};

</script>
