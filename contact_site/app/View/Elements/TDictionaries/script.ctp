
<script type="text/javascript">
//定型文新規追加
function openAddDialog(tabid){
//定型文並べ替えチェックボックスもしくはカテゴリ並べ替えチェックボックスが入っているときはリンク無効とする
  var index = document.getElementById("select_tab_index").value;
  if (!document.getElementById("sort" + index).checked
       && !document.getElementById("tabsort").checked) {
    openEntryDialog({type: 1, tabid: tabid, tabindex:index});
  }
  else{
    return false;
  }
}

//定型文編集
function openEditDialog(id,tabid){
  //定型文並べ替えチェックボックスもしくはカテゴリ並べ替えチェックボックスが入っているときはリンク無効とする
  var index = document.getElementById("select_tab_index").value;
  if (!document.getElementById("sort" + index).checked
       && !document.getElementById("tabsort").checked) {
    openEntryDialog({type: 2, id: id, tabid: tabid, tabindex:index});
  }
  else{
    return false;
  }
}

//定型文コピー処理
function openCopyDialog(){
  index = document.getElementById("select_tab_index").value;
  var list = document.querySelectorAll('input[name^="selectTab'+index+'"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  var select_tab_index = document.getElementById("select_tab_index").value;
  openEntryEditDialog({
    type: 3,
    selectedList: selectedList,
    select_tab_index: select_tab_index
  });
}

//定型文移動処理
function openMoveDialog(){
  index = document.getElementById("select_tab_index").value;
  var list = document.querySelectorAll('input[name^="selectTab'+index+'"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  var select_tab_index = document.getElementById("select_tab_index").value;
  openEntryEditDialog({
    type: 4,
    selectedList: selectedList,
    select_tab_index: select_tab_index
  });
}

//定型文新規追加/編集ダイアログ表示
function openEntryDialog(setting){
  var type = setting.type;
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, tabid  type:2 => type, id, tabid
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/TDictionaries/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-tdictionary-entry', '定型文メッセージ情報', 'moment');
    }
  });
}

//openEntryEditDialog
//定型文コピー/移動・カテゴリ更新/削除ダイアログ表示
function openEntryEditDialog(setting){
  var type = setting.type;
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, type:2 => type, id
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/TDictionaries/openEntryEdit') ?>",
    success: function(html){
//      var selectedCategory = document.getElementById("TDictionaryType").value;
      if(type == 1){
        modalOpen.call(window, html, 'p-category-edit', 'カテゴリ名の変更', 'moment');
      }
      if(type == 2){
        modalOpen.call(window, html, 'p-category-del', 'カテゴリの削除', 'moment');
      }
      if(type == 3){
        modalOpen.call(window, html, 'p-copy', 'コピー', 'moment');
      }
      if(type == 4){
        modalOpen.call(window, html, 'p-move', '移動', 'moment');
      }
    }
  });
}

//定型文の削除
function openConfirmDialog(){
  //チェックボックスのチェック状態の取得
  index = document.getElementById("select_tab_index").value;
  var list = document.querySelectorAll('input[name^="selectTab'+index+'"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }
  var select_tab_index = document.getElementById("select_tab_index").value;
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', '定型文メッセージ情報', 'moment');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
            cache: false,
      data: {
        selectedList: selectedList,
        select_tab_index: select_tab_index
      },
      url: "<?= $this->Html->url('/TDictionaries/remoteDeleteUser') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TDictionaries/index') ?>";
      }
    });
  };
}

/* #451 定型文カテゴリ対応 start */
//カテゴリのソートモード（タブ）
function tabSort(){
  //チェックモードタブを判定
  var index = document.getElementById("select_tab_index").value;
  if (!document.getElementById("tabsort").checked) {
    document.getElementById("tabSortMessage").style.display="none";
    document.getElementById("tabsort_btn").style.display="none";
    //カテゴリ名入力欄変更可
    document.getElementById("input_category_value").disabled = "";
    //定型文登録ボタン押下可
    document.getElementById('tdictionaries_add_btn'+index).className="btn-shadow disOffgreenBtn";
    //定型文の並べ替えチェックボックスチェック可
    document.getElementById('sort'+index).disabled = "";
    //カテゴリメニューボタン押下可
    document.getElementById('tdictionaries_manu_btn'+index).className="btn-shadow disOffgreenBtn";
    //全て選択チェックボックス選択可
    document.getElementById('allCheck'+index).disabled = "";
    //項目チェックボックス選択可
    var checkBoxList = document.querySelectorAll('[id^="selectTab'+index+'"]');
    for (var i = 0; i < checkBoxList.length; i++) {
      checkBoxList[i].disabled = "";
    }
    $(".ui-tabs-nav").addClass("move").sortable("disable");
  }
  else {
    document.getElementById("tabSortMessage").style.display="";
    document.getElementById("tabsort_btn").style.display="";
    //各ボタン及び動作をモード中は動かなくする
    //カテゴリ名入力欄変更不可
    document.getElementById("input_category_value").disabled = "disabled";
    //定型文登録ボタン押下不可
    document.getElementById('tdictionaries_add_btn'+index).className="btn-shadow disOffgrayBtn";
    //定型文の並べ替えチェックボックスチェック不可
    document.getElementById('sort'+index).disabled = "disabled";
    //カテゴリメニューボタン押下不可
    document.getElementById('tdictionaries_manu_btn'+index).className="btn-shadow disOffgrayBtn";
    //全て選択チェックボックス選択不可
    document.getElementById('allCheck'+index).disabled = "disabled";
    //項目チェックボックス選択不可
    var checkBoxList = document.querySelectorAll('[id^="selectTab'+index+'"]');
    for (var i = 0; i < checkBoxList.length; i++) {
      checkBoxList[i].disabled = "disabled";
    }
    //ソートモードon
    $(".ui-tabs-nav").addClass("move").sortable("enable");
  }
}

//カテゴリのソートを保存
function saveTabSort(){
  var list = getTabSort();
  $.ajax({
    type: "POST",
    url: "<?= $this->Html->url(['controller' => 'TDictionaries', 'action' => 'remoteSaveTabSort']) ?>",
    data: {
      list : list
    },
    dataType: "html",
    success: function(){
      location.href = location.href;
    }
  });
}

//タブのソート順を取得
var getTabSort = function(){
  var list = [];
  $(".soteTabs ul li a").each(function(e){
    list.push($(this).data('id'));
  });
  return JSON.parse(JSON.stringify(list));
};

/* #451 定型文カテゴリ対応 end */

//定型文のソートモード
function toggleSort(){
  var index = document.getElementById("select_tab_index").value;
  if (!document.getElementById("sort" + index).checked) {
    //ソートモードoff
    $(".sortable").addClass("move").sortable("disable");
    //定型文ソートモードメッセージ＆登録ボタン非表示
    document.getElementById("sortMessage" + index).style.display="none";
    document.getElementById("tdictionaries_sort_btn").style.display="none";
    //カテゴリ名入力欄変更可
    document.getElementById("input_category_value").disabled = "";
    //定型文登録ボタン押下可
    document.getElementById('tdictionaries_add_btn'+index).className="btn-shadow disOffgreenBtn";
    //カテゴリの並べ替えチェックボックスチェック可
    document.getElementById('tabsort').disabled = "";
    //カテゴリメニューボタン押下可
    document.getElementById('tdictionaries_manu_btn'+index).className="btn-shadow disOffgreenBtn";
    //全て選択チェックボックス選択可
    document.getElementById('allCheck'+index).disabled = "";
    //項目チェックボックス選択可
    var checkBoxList = document.querySelectorAll('[id^="selectTab'+index+'"]');
    for (var i = 0; i < checkBoxList.length; i++) {
      checkBoxList[i].disabled = "";
    }
  }
  else {
    //ソートモードon
    $(".sortable").addClass("move").sortable("enable");
    //定型文ソートモードメッセージ＆登録ボタン表示
    document.getElementById("sortMessage" + index).style.display="";
    document.getElementById("tdictionaries_sort_btn").style.display="";
    //各ボタン及び動作をモード中は動かなくする
    //カテゴリ名入力欄変更不可
    document.getElementById("input_category_value").disabled = "disabled";
    //定型文登録ボタン押下不可
    document.getElementById('tdictionaries_add_btn'+index).className="btn-shadow disOffgrayBtn";
    //カテゴリの並べ替えチェックボックスチェック不可
    document.getElementById('tabsort').disabled = "disabled";
    //カテゴリメニューボタン押下不可
    document.getElementById('tdictionaries_manu_btn'+index).className="btn-shadow disOffgrayBtn";
    //全て選択チェックボックス選択不可
    document.getElementById('allCheck'+index).disabled = "disabled";
    //項目チェックボックス選択不可
    var checkBoxList = document.querySelectorAll('[id^="selectTab'+index+'"]');
    for (var i = 0; i < checkBoxList.length; i++) {
      checkBoxList[i].disabled = "disabled";
    }
    $('t-link').removeClass('t-link');
  }
}

//定型文ソートを保存
function saveToggleSort(){
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

//定型文のソート順を取得
var getSort = function(){
  var list = [];
  $(".sortable tr").each(function(e){
    list.push($(this).data('id'));
  });
  return JSON.parse(JSON.stringify(list));
};

$(document).ready(function(){

$(".sortable").sortable({
  axis: "y",
  tolerance: "pointer",
  containment: "parent",
  revert: 100
});
$(".sortable").sortable("disable");

$("#soteTabs").tabs({active: "<?= $tabindex ?>",});
$('.ui-tabs-nav').sortable( {
    axis: 'x'
} );
$(".ui-tabs-nav").sortable("disable");

});

document.body.onload = function(){
  // 全選択用チェックボックス
  var index = document.getElementById("select_tab_index").value;
  var allCheckElm = document.querySelectorAll('[id^="allCheck"]');
  for (var i = 0; i < allCheckElm.length; i++) {
    allCheckElm[i].addEventListener('click', setAllCheck); // 全選択
  }

  // チェックボックス群
  var checkBoxList = document.querySelectorAll('[id^="selectTab'+index+'"]');
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
  var index = document.getElementById("select_tab_index").value;
  $('input[name^="selectTab' + index + '"]').prop('checked', this.checked);
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
  var index = document.getElementById("select_tab_index").value;
  // 全て選択されている場合
  if ( $('input[name^="selectTab' + index + '"]:not(:checked)').length === 0 ) {
    $('input[id="allCheck' + index + '"]').prop('checked', true);
  }
  else {
    $('input[id="allCheck' + index + '"]').prop('checked', false);
  }
}

//行クリックでチェックする
var isCheck = function(){
  allCheckCtrl();
  actBtnShow();
};

//各ボタンの有効/無効切り替え
var actBtnShow = function(){
  // 選択中の場合
  var stint_flg = document.getElementById("stint_flg").value;
  var select_tab_index = document.getElementById("select_tab_index").value;
  if ( $('input[name^="selectTab'+select_tab_index+'"]').is(":checked") ) {
    document.getElementById("tdictionaries_copy_btn" + select_tab_index).className="btn-shadow disOffgreenBtn";
    document.getElementById("tdictionaries_copy_btn" + select_tab_index).addEventListener('click', openCopyDialog, false);
    if(stint_flg == '0'){
      document.getElementById("tdictionaries_move_btn" + select_tab_index).className="btn-shadow disOffgrayBtn";
      document.getElementById("tdictionaries_move_btn" + select_tab_index).removeEventListener('click', openMoveDialog, false);
    }
    else{
      document.getElementById("tdictionaries_move_btn" + select_tab_index).className="btn-shadow disOffgreenBtn";
      document.getElementById("tdictionaries_move_btn" + select_tab_index).addEventListener('click', openMoveDialog, false);
    }
    document.getElementById("tdictionaries_dustbox_btn" + select_tab_index).className="btn-shadow disOffredBtn";
    document.getElementById("tdictionaries_dustbox_btn" + select_tab_index).addEventListener('click', openConfirmDialog, false);
  }
  else {
    document.getElementById("tdictionaries_copy_btn" + select_tab_index).className="btn-shadow disOffgrayBtn";
    document.getElementById("tdictionaries_copy_btn" + select_tab_index).removeEventListener('click', openCopyDialog, false);
    document.getElementById("tdictionaries_move_btn" + select_tab_index).className="btn-shadow disOffgrayBtn";
    document.getElementById("tdictionaries_move_btn" + select_tab_index).removeEventListener('click', openMoveDialog, false);
    document.getElementById("tdictionaries_dustbox_btn" + select_tab_index).className="btn-shadow disOffgrayBtn";
    document.getElementById("tdictionaries_dustbox_btn" + select_tab_index).removeEventListener('click', openConfirmDialog, false);
  }
  allCheckCtrl();
};

/* #451 定型文カテゴリ対応 start */
$( function() {

  //インデックスの初期値を挿入暫定的に0
  document.getElementById("select_tab_index").value = "<?= $tabindex ?>";
  //タブが押下された時の処理
  $("#soteTabs").bind('tabsactivate', function(event, ui) {
    var index = ui.newTab.index();
    var stint_flg = document.getElementById("stint_flg").value;
    //もし、タブをクリックされた時定型文並び替えモードだったら並べ替えモードをキャンセル
    if (!document.getElementById("sort" + index).checked) {
      $(".sortable").addClass("move").sortable("disable");
      //定型文ソートモードメッセージ＆登録ボタン非表示
      document.getElementById("sortMessage" + index).style.display="none";
      document.getElementById("tdictionaries_sort_btn").style.display="none";
      //カテゴリ名入力欄変更可
      document.getElementById("input_category_value").disabled = "";
      //定型文登録ボタン押下可
      var tdictionariesAddBtnList = document.querySelectorAll('[id^="tdictionaries_add_btn"]');
      for (var i = 0; i < tdictionariesAddBtnList.length; i++) {
        tdictionariesAddBtnList[i].className="btn-shadow disOffgreenBtn";
      }
      //カテゴリの並べ替えチェックボックスチェック可
      document.getElementById('tabsort').disabled = "";
      //カテゴリメニューボタン押下可
      var tdictionariesManuBtnList = document.querySelectorAll('[id^="tdictionaries_manu_btn"]');
      for (var i = 0; i < tdictionariesManuBtnList.length; i++) {
        tdictionariesManuBtnList[i].className="btn-shadow disOffgreenBtn";
      }
      //全て選択チェックボックス選択可
      //document.getElementById('allCheck'+index).disabled = "";
      var allCheckList = document.querySelectorAll('[id^="allCheck"]');
      for (var i = 0; i < allCheckList.length; i++) {
        allCheckList[i].disabled = "";
      }
      //項目チェックボックス選択可
      var checkBoxList = document.querySelectorAll('[id^="selectTab"]');
      for (var i = 0; i < checkBoxList.length; i++) {
        checkBoxList[i].disabled = "";
      }
      //チェックボックスも空に戻す
      $("[id^=sort]").prop("checked", false);
    }
    // クリックされたタブのインデックスをhiddenに持っておく
    document.getElementById("select_tab_index").value = index;
    //タブ並び替えモード中だったら
    if (document.getElementById("tabsort").checked) {
      tabSort();
    }
    //もし定型文リストが存在しなかったら
    if((document.getElementById("dictionary_list_flg"+index).value) == 0){
      //定型文の並べ替えチェックボックスチェック不可
      document.getElementById('sort'+index).disabled = "disabled";
      //全て選択チェックボックス選択不可
      document.getElementById('allCheck'+index).disabled = "disabled";
    }
    else{
      //定型文の並べ替えチェックボックスチェック不可
      document.getElementById('sort'+index).disabled = "";
      //全て選択チェックボックス選択不可
      document.getElementById('allCheck'+index).disabled = "";
    }
    if(stint_flg == '0'){
      document.getElementById('tdictionaries_manu_btn' + index).className="btn-shadow disOffgrayBtn";
    }
  });

  //プラン別対応
  var stint_flg = document.getElementById("stint_flg").value;
  var index = document.getElementById("select_tab_index").value;
  if(stint_flg == '0'){
    //機能制限
    //カテゴリ名入力欄変更不可
    document.getElementById("input_category_value").disabled = "disabled";
    //プランメッセージ表示
    document.getElementById("stintMessage").style.display="";
    //カテゴリメニューボタン押下不可
    document.getElementById('tdictionaries_manu_btn'+index).className="btn-shadow disOffgrayBtn";
  }

  $("[id^=openMenu]").click(function(){
    var stint_flg = document.getElementById("stint_flg").value;
    var index = document.getElementById("select_tab_index").value;
    if(!document.getElementById("sort" + index).checked
          && !document.getElementById("tabsort").checked
          && stint_flg != '0'){
      var menu = document.getElementById("layerMenu"+ index).style.display;
      if(menu == "block"){
        $("#layerMenu"+ index).fadeOut("fast");
      }
      else{
        $("#layerMenu"+ index).fadeIn("fast");
      }
    }
    else{
      return;
    }
  });
});

//テキストボックスに入力があったらボタンを有効化
function inputValue($this){
  if($this.value){
    document.getElementById("input_category_btn").disabled = "";
    document.getElementById("input_category_btn").className= "disOffgreenBtn btn-shadow";
  }
  else{
    document.getElementById("input_category_btn").disabled = "disabled";
    document.getElementById("input_category_btn").className= "disOffgrayBtn btn-shadow";
  }
}

//カテゴリ追加
function saveCategoryAddDialog(){
  saveCategoryEntryDialog({type: 1});
}

//カテゴリ更新
function openCategoryEditDialog(id){
  var index = document.getElementById("select_tab_index").value;
  $("#layerMenu"+ index).fadeOut("fast");
  openEntryEditDialog({type: 1, id: id});
}

//カテゴリ削除
function openCategoryDeleteDialog(id){
  var index = document.getElementById("select_tab_index").value;
  $("#layerMenu"+ index).fadeOut("fast");
  openEntryEditDialog({type: 2, id: id});
}

function saveCategoryEntryDialog(setting){
  var type = setting.type;
  var category_name = document.getElementById('input_category_value').value;
  if(category_name){
    modalOpen.call(window, "カテゴリを登録します、よろしいですか？", 'p-confirm', '定型文メッセージ情報', 'moment');
    popupEvent.closePopup = function(){
      $.ajax({
        type: 'post',
        url: "<?= $this->Html->url('/TDictionaries/remoteSaveCategoryEntryForm') ?>",
        data: {
          setting: setting, // type:1 => type, type:2 => type, id
          category_name: category_name,
        },
        cache: false,
        dataType: "JSON",
        success: function(data){
          location.href = "<?= $this->Html->url('/TDictionaries/index') ?>";
        }
      });
    };
  }
  else{
    document.getElementById("input_category_btn").disabled = "disabled";
    document.getElementById("input_category_btn").className= "disOffgrayBtn btn-shadow";
  }
}

/* #451 定型文カテゴリ対応 end */
</script>
