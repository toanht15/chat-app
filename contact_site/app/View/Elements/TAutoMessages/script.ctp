<script type="text/javascript">
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
  var allCheckElm = document.getElementById('allCheck');
  allCheckElm.addEventListener('click', setAllCheck); // 全選択

  // チェックボックス群
  var checkBoxList = document.querySelectorAll('[id^="selectTab"]');
  for (var i = 0; i < checkBoxList.length; i++) {
    checkBoxList[i].addEventListener('change', actBtnShow); // 有効無効ボタンの表示切り替え
  }

  // 「条件」の「設定」ラベル
  var targetBalloonList = document.querySelectorAll('.conditionValueLabel');
  for (var i = 0; i < targetBalloonList.length; i++) {
    targetBalloonList[i].addEventListener('mouseenter', balloonApi.show('cond')); // 設定した条件リストのポップアップ表示
    targetBalloonList[i].addEventListener('mouseleave', balloonApi.hide); // 設定した条件リストのポップアップ非表示
  }

  // 「アクション」の「内容」ラベル
  var targetBalloonList = document.querySelectorAll('.actionValueLabel');
  for (var i = 0; i < targetBalloonList.length; i++) {
    targetBalloonList[i].addEventListener('mouseenter', balloonApi.show('act')); // 設定したアクション内容のポップアップ表示
    targetBalloonList[i].addEventListener('mouseleave', balloonApi.hide); // 設定したアクション内容のポップアップ非表示
  }
};

// 全選択
var setAllCheck = function() {
  $('input[name="selectTab"]').prop('checked', this.checked);
  if ( this.checked ) {
    $(".actCtrlBtn").css('display', 'block');
  }
  else {
    $(".actCtrlBtn").css('display', 'none');
  }
  actBtnShow();
}

// 全選択用チェックボックスのコントロール
var allCheckCtrl = function(){
  // 全て選択されている場合
  if ( $('input[name="selectTab"]:not(:checked)').length === 0 ) {
    $('input[name="allCheck"]').prop('checked', true);
  }
  else {
    $('input[name="allCheck"]').prop('checked', false);
  }
}

// 有効/無効ボタンの表示/非表示
var actBtnShow = function(){
  // 選択中の場合
  if ( $('input[name="selectTab"]').is(":checked") ) {
    //一つでもチェックが入ったら
    //コピーボタン有効
    document.getElementById("tautomessages_copy_btn").className="btn-shadow disOffgreenBtn";
    document.getElementById("tautomessages_copy_btn").addEventListener('click', openCopyDialog, false);
    //有効にするボタン有効
    document.getElementById("tautomessages_check_btn").className="btn-shadow disOffgreenBtn";
    //無効にするボタン有効
    document.getElementById("tautomessages_inactive_btn").className="btn-shadow disOffredBtn";
    //削除ボタン有効
    document.getElementById("tautomessages_dustbox_btn").className="btn-shadow disOffredBtn";
    document.getElementById("tautomessages_dustbox_btn").addEventListener('click', openConfirmDialog, false);
  }
  else {
    //一つもチェックが無かったら
    //コピーボタン無効
    document.getElementById("tautomessages_copy_btn").className="btn-shadow disOffgrayBtn";
    document.getElementById("tautomessages_copy_btn").removeEventListener('click', openCopyDialog, false);
    //有効にするボタン無効
    document.getElementById("tautomessages_check_btn").className="btn-shadow disOffgrayBtn";
    //無効にするボタン無効
    document.getElementById("tautomessages_inactive_btn").className="btn-shadow disOffgrayBtn";
    //削除ボタン無効
    document.getElementById("tautomessages_dustbox_btn").className="btn-shadow disOffgrayBtn";
    document.getElementById("tautomessages_dustbox_btn").removeEventListener('click', openConfirmDialog, false);
    $('#allCheck').prop('checked', false);
  }
  allCheckCtrl();
};

// 行クリックでチェックする
var isCheck = function(e){
  var id = getData(this.parentElement, 'id');
  if (id !== undefined) {
    var target = $("#selectTab" + id);
    if (target.prop('checked')) {
      target.prop('checked', false);
    }
    else {
      target.prop('checked', true);
    }
  }
  actBtnShow();
};

// 設定した条件リストのポップアップ表示
var balloonApi = {
  flg: false,
  show: function(type) {
    return function (e) {
      balloonApi.flg = true;
      var id = getData(this.parentElement.parentElement, 'id');
      if (id) {
        var elm = $(this);
        var offset = elm.offset();

        $("[id='balloon_" + type + "_" +id+"']").animate({
          top: offset.top + elm.prop("offsetHeight") + 3,
          left: offset.left + 3
        }, {
          duration: "first",
          complete: function(){
            $("[id^='balloon_']").hide();
            if (balloonApi.flg) {
              $(this).show();
            }
          }
        });
      }
    }
  },
  hide: function(e){
    balloonApi.flg = false;
    $("[id^='balloon_']").hide();
  }
};

// 有効/無効処理のリクエスト
var sendActiveRequest = function(data){
  $.ajax({
    type: 'GET',
    url: '/TAutoMessages/changeStatus',
    cache: false,
    data: data,
    dataType: 'html',
    success: function(html){
      //現在のページ番号
      var index = Number("<?= $this->Paginator->params()["page"] ?>");
      var url = "<?= $this->Html->url('/TAutoMessages/index') ?>";
      location.href = url + "/page:" + index;
//      location.href = "/TAutoMessages/index"
    }
  });
};

// 有効/無効処理
function toActive(flg){
  //一つでもチェックボックスに値が入っていたら
  if ( $('input[name="selectTab"]').is(":checked") ) {
    var list = document.querySelectorAll('input[name="selectTab"]:checked');
    var selectedList = [];
    for (var i = 0; i < list.length; i++){
      selectedList.push(Number(list[i].value));
    }
    sendActiveRequest({
      status: flg,
      targetList: selectedList
    });
  }
}

// 有効/無効処理
function isActive(flg, id){
  var selectedList = [];
  selectedList.push(Number(id));
  sendActiveRequest({
    status: flg,
    targetList: selectedList
  });
}

// function removeAct(no, id){
//  modalOpen.call(window, "No." + no + " を削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
//  popupEvent.closePopup = function(){
//    $.ajax({
//      type: 'post',
//      data: {
//        id: id
//      },
//      cache: false,
//      url: "/TAutoMessages/remoteDelete",
//      success: function(){
//        location.href = "/TAutoMessages/index";
//      }
//    });
//  };
// }

//オートメッセージ設定の削除
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
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/TAutoMessages/chkRemoteDelete') ?>",
      success: function(){
        $(".p-dictionary-del #popup-button a").prop("disabled", true);
        var url = "<?= $this->Html->url('/TAutoMessages/index') ?>";
        location.href = url + "/page:" + index;
      },
      error: function() {
        //debugger;
        console.log('error');
        TabIndex = document.getElementById("select_tab_index").value;
        var url = "<?= $this->Html->url('/TAutoMessages/index') ?>";
        location.href = url + "/page:" + 1;
      }
    });
  });
}

//オートメッセージコピー処理
function openCopyDialog(){
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  modalOpen.call(window, "コピーします、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        selectedList: selectedList
      },
      url: "<?= $this->Html->url('/TAutoMessages/remoteCopyEntryForm') ?>",
      success: function(){
        //現在のページ番号
        var index = Number("<?= $this->Paginator->params()["page"] ?>");
        var url = "<?= $this->Html->url('/TAutoMessages/index') ?>";
        location.href = url + "/page:" + index;
      },
      error: function() {
        console.log('error');
        location.href = "<?= $this->Html->url('/TAutoMessages/index') ?>";
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

//オートメッセージ新規追加
function openAdd(){
  //オートメッセージ設定並べ替えチェックボックスが入っているときはリンク無効とする
  if (!document.getElementById("sort").checked) {
    //現在のページ番号
    var index = Number("<?= $this->Paginator->params()["page"] ?>");
    var url = "<?= $this->Html->url('/TAutoMessages/add') ?>";
    location.href = url + "?lastpage="+index;
  }
  else{
    return false;
  }
}

//オートメッセージ編集
function openEdit(id){
  //オートメッセージ設定並べ替えチェックボックスが入っているときはリンク無効とする
  if (!document.getElementById("sort").checked) {
    //現在のページ番号
    var index = Number("<?= $this->Paginator->params()["page"] ?>");
    var url = "<?= $this->Html->url('/TAutoMessages/edit') ?>";
    location.href = url + "/" + id + "?lastpage="+index;
  }
  else{
    return false;
  }
}

//オートメッセージ設定のソートモード
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
    //オートメッセージ設定登録ボタン押下不可
    document.getElementById('tautomessages_add_btn').className="btn-shadow disOffgrayBtn";
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

//オートメッセージ設定のソート順を保存
var confirmSort = function(){
  modalOpen.call(window, "編集内容を保存します。<br/><br/>よろしいですか？<br/>", 'p-sort-save-confirm', 'オートメッセージ設定並び替えの保存', 'moment');
  popupEvent.saveClicked = function(){
    saveToggleSort();
  }
  popupEvent.cancelClicked = function(){
    var url = "<?= $this->Html->url('/TAutoMessages/index') ?>";
    location.href = url;
  }
  $(".p-sort-save-confirm #popupCloseBtn").click(function(){
    $("#sort").prop('checked', true);
  });
};

//オートメッセージ設定ソートを保存
var saveToggleSort = toExecutableOnce(function(){
  var list = getSort();
  var sortNolist = getSortNo();
  $.ajax({
    type: "POST",
    url: "<?= $this->Html->url(['controller' => 'TAutoMessages', 'action' => 'remoteSaveSort']) ?>",
    data: {
      list : list,
      sortNolist: sortNolist
    },
    dataType: "html",
    success: function(){
      //現在のページ番号
      var index = Number("<?= $this->Paginator->params()["page"] ?>");
      var url = "<?= $this->Html->url('/TAutoMessages/index') ?>";
      location.href = url + "/page:" + index;
    }
  });
});

//オートメッセージ設定のソート順を取得
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
  return JSON.parse(JSON.stringify(sortlist));
};

$(document).ready(function(){
  // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    console.log('入った');
    var parentTdId = $(this).parent().parent().attr('id');
    console.log(parentTdId);
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    console.log(targetObj);
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70) + 'px',
      left: $(this).offset().left - 65 + 'px'
    });
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    console.log('離れた');
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });
});
</script>
