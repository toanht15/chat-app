<script type="text/javascript">
//定型文新規追加
function openAddDialog(tabid){
//定型文並べ替えチェックボックスもしくはカテゴリ並べ替えチェックボックスが入っているときはリンク無効とする
  var index = document.getElementById("select_tab_index").value;
  if (!document.getElementById("sort" + index).checked
       && !document.getElementById("tabsort").checked) {
    openEntryDialog({type: 1, tabid: tabid, tabindex:index, title:"定型文登録"});
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
    openEntryDialog({type: 2, id: id, tabid: tabid, tabindex:index, title:"定型文更新"});
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
      modalOpen.call(window, html, 'p-tdictionary-entry', setting.title, 'moment');
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
        $("#edit_category_value").select();
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
  modalOpen.call(window, "選択された定型文を削除します。<br/><br/>よろしいですか？<br/>", 'p-dictionary-del', '削除', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    $.ajax({
      type: 'post',
            cache: false,
      data: {
        selectedList: selectedList,
        select_tab_index: select_tab_index
      },
      url: "<?= $this->Html->url('/TDictionaries/remoteDeleteUser') ?>",
      success: function(){
        $(".p-dictionary-del #popup-button a").prop("disabled", true);
        var url = "<?= $this->Html->url('/TDictionaries/index') ?>";
        location.href = url + "/tabindex:" + index;
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

/* #451 定型文カテゴリ対応 start */
//カテゴリのソートモード（タブ）
function tabSort(){
  //チェックモードタブを判定
  var index = document.getElementById("select_tab_index").value;
  if (!document.getElementById("tabsort").checked) {
    confirmTabSort();
  }
  else {
    $('[id^="selectTab"]').prop('checked', false);
    allCheckCtrl();
    actBtnShow();
    document.getElementById("tabsortText").style.display="none";
    document.getElementById("tabSortMessage").style.display="";
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
    $(".ui-tabs-nav").addClass("move").sortable( "option", {
      disabled: false,
      update: function( event, ui ) {
        console.log("update");
        $('.soteTabs').tabs({
          active: ui.item.attr('id').replace('li_','')
        })
      }
    });
    $(".soteTabs ul li").css('cursor', 'move');
    $(".soteTabs ul li a").css('cursor', 'move');
  }
}

//カテゴリのソートを保存
var saveTabSort = toExecutableOnce(function(){
  var list = getTabSort();
  var tabindex = 0;
  $('#tablist').find('li').each(function(index, element){
    if($(element).hasClass('ui-state-active')) {
      tabindex = index;
      return;
    }
  });
  $.ajax({
    type: "POST",
    url: "<?= $this->Html->url(['controller' => 'TDictionaries', 'action' => 'remoteSaveTabSort']) ?>",
    data: {
      list : list
    },
    dataType: "html",
    success: function(){
      var url = "<?= $this->Html->url('/TDictionaries/index') ?>";
      location.href = url + "/tabindex:" + tabindex;
    }
  });
});

//タブのソート順を取得
var getTabSort = function(){
  var list = [];
  $(".soteTabs ul li a").each(function(e){
    list.push($(this).data('id'));
  });
  list = $.grep(list, function(e){return e;});
  return JSON.parse(JSON.stringify(list));
};

//タブのソート順をリセット
var confirmTabSort = function(){
  modalOpen.call(window, "編集内容を保存します。<br/><br/>よろしいですか？<br/>", 'p-tabsort-save-confirm', 'カテゴリ並び替えの保存', 'moment');
  popupEvent.saveClicked = function(){
    saveTabSort();
  }
  popupEvent.cancelClicked = function(){
    location.href = "<?= $this->Html->url('/TDictionaries/index') ?>";
  };
  $(".p-tabsort-save-confirm #popupCloseBtn").click(function(){
    $('#tabsort').prop('checked', true);
  });
};

//定型文のソート順をリセット
var confirmSort = function(){
  var index = document.getElementById("select_tab_index").value;
  modalOpen.call(window, "編集内容を保存します。<br/><br/>よろしいですか？<br/>", 'p-sort-save-confirm', '定型文並び替えの保存', 'moment');
  popupEvent.saveClicked = function(){
    saveToggleSort();
  }
  popupEvent.cancelClicked = function(){
    var url = "<?= $this->Html->url('/TDictionaries/index') ?>";
    location.href = url + "/tabindex:" + index;
  }
  $(".p-sort-save-confirm #popupCloseBtn").click(function(){
    $("#sort" + index).prop('checked', true);
  });
};

/* #451 定型文カテゴリ対応 end */

//定型文のソートモード
function toggleSort(){
  var index = document.getElementById("select_tab_index").value;
  if (!document.getElementById("sort" + index).checked) {
    confirmSort();
  }
  else {
    $('[id^="selectTab"]').prop('checked', false);
    allCheckCtrl();
    actBtnShow();
    //ソートモードon
    $(".sortable").addClass("move").sortable("enable");
    //定型文ソートモードメッセージ＆登録ボタン表示
    document.getElementById("sortText" + index).style.display="none";
    document.getElementById("sortMessage" + index).style.display="";
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
    $("table tbody.sortable tr td").css('cursor', 'move');
    $("table tbody.sortable tr td a").css('cursor', 'move');
  }
}

//定型文ソートを保存
var saveToggleSort = toExecutableOnce(function(){
  var list = getSort();
  $.ajax({
    type: "POST",
    url: "<?= $this->Html->url(['controller' => 'TDictionaries', 'action' => 'remoteSaveSort']) ?>",
    data: {
      list : list
    },
    dataType: "html",
    success: function(){
      var index = document.getElementById("select_tab_index").value;
      var url = "<?= $this->Html->url('/TDictionaries/index') ?>";
      location.href = url + "/tabindex:" + index;
    }
  });
});

//

//定型文のソート順を取得
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

$(".soteTabs").tabs({active: "<?= $tabindex ?>",
  create: function( event, ui ) {
  $('#soteTabs').css('visibility', 'visible');
}
});
$('.ui-tabs-nav').sortable( {
    axis: 'x',
    cursor: 'move'
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
    if(stint_flg != '0'){
      document.getElementById("tdictionaries_move_btn" + select_tab_index).className="btn-shadow disOffgreenBtn";
      document.getElementById("tdictionaries_move_btn" + select_tab_index).addEventListener('click', openMoveDialog, false);
    }
    document.getElementById("tdictionaries_dustbox_btn" + select_tab_index).className="btn-shadow disOffredBtn";
    document.getElementById("tdictionaries_dustbox_btn" + select_tab_index).addEventListener('click', openConfirmDialog, false);
  }
  else {
    document.getElementById("tdictionaries_copy_btn" + select_tab_index).className="btn-shadow disOffgrayBtn";
    document.getElementById("tdictionaries_copy_btn" + select_tab_index).removeEventListener('click', openCopyDialog, false);
    if(stint_flg != '0'){
      document.getElementById("tdictionaries_move_btn" + select_tab_index).className="btn-shadow disOffgrayBtn";
      document.getElementById("tdictionaries_move_btn" + select_tab_index).removeEventListener('click', openMoveDialog, false);
    }
    document.getElementById("tdictionaries_dustbox_btn" + select_tab_index).className="btn-shadow disOffgrayBtn";
    document.getElementById("tdictionaries_dustbox_btn" + select_tab_index).removeEventListener('click', openConfirmDialog, false);
  }
  allCheckCtrl();
};

/* #451 定型文カテゴリ対応 start */
$( function() {
  //インデックスの初期値を挿入暫定的に0
  document.getElementById("select_tab_index").value = "<?= $tabindex ?>";
  document.getElementById("tabBoxborder" + document.getElementById("select_tab_index").value).style.display="";
  //タブが押下された時の処理
  $("#soteTabs").bind('tabsactivate', function(event, ui) {
    var oldid = "ui-id-" + (Number(document.getElementById("select_tab_index").value) + 1);
    //メニューが開いていたらメニューを閉じる
    $("#layerMenu"+ document.getElementById("select_tab_index").value).fadeOut("fast");
    document.getElementById("tabBoxborder" + document.getElementById("select_tab_index").value).style.display="none";
    var index = Number($(ui.newTab).attr('id').replace('li_',''));
    document.getElementById("tabBoxborder" + index).style.display="";
    var stint_flg = document.getElementById("stint_flg").value;
    //もし、タブをクリックされた時定型文並び替えモードだったら並べ替えモードをキャンセル
    if (!document.getElementById("sort" + index).checked) {
      $(".sortable").addClass("move").sortable("disable");
      //定型文ソートモードメッセージ＆登録ボタン非表示
      document.getElementById("sortText" + index).style.display="";
      document.getElementById("sortMessage" + index).style.display="none";
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
      document.getElementById('gray_tdictionaries_manu_btn' + index).className="btn-shadow disOffgrayBtn";
    }
    setWidth(event);
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
    document.getElementById('gray_tdictionaries_manu_btn'+index).className="btn-shadow disOffgrayBtn";
    //カテゴリ並べ替えチェックボックスボタン押下不可
    document.getElementById("tabsort").disabled = "disabled";
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

  //ページ全体を監視して何らかのクリックがあった時
  $(document).on('click touchend', function(event) {
    var index = document.getElementById("select_tab_index").value;
    //メニューオープン以外の動きだったら
    if (!$(event.target).closest('[id^=openMenu]').length) {
      $("#layerMenu"+ index).fadeOut("fast");
    }
  });

//  var currentWidth = $(window).outerWidth();
  $(window).on('load resize', function(e){
    setWidth(e);
  });

  //デフォルトのタブ幅セット
  var allDefaultTabWidth = 0;
  var minimumWidgetSizeList = {};
  function setWidth(e){
    var afterWindowSize = $(this).outerWidth();
    //全てのタブの要素を取得
    var allTabList = document.querySelectorAll('[id^="ui-id-"]');
    for (var i = 0; i < allTabList.length; i++) {
      var tab_w = allTabList[i].clientWidth;
      if(tab_w < 104){
        allTabList[i].style.width = '104px';
        allTabList[i].style.textAlign = 'center';
      }
      else{
        var tab_w = allTabList[i].clientWidth;
        if(allTabList[i].clientWidth > 40){
          var px_str = tab_w+'px'
          allTabList[i].style.width = px_str;
          allTabList[i].style.textAlign = 'center';
        }
      }
      if(e.type === 'load') {
        $(allTabList[i]).data('defaultWidth', allTabList[i].clientWidth);
        allDefaultTabWidth += allTabList[i].clientWidth + 1;
      }
    }

    // タブ表示領域サイズを取得
    var tabDisplayWidth = $('#tablist').outerWidth() - (5 * allTabList.length);
    for (var i = 0; i < allTabList.length; i++) {
      var tabWidth = $(allTabList[i]).data('defaultWidth');
      var ratio = tabWidth / allDefaultTabWidth;

      if (tabDisplayWidth * ratio >  tabWidth) {
        allTabList[i].style.width = tabWidth - 2 + "px";
        if(typeof minimumWidgetSizeList[i] !== 'undefined') {
          delete minimumWidgetSizeList[i];
        }
      } else if((tabDisplayWidth * ratio - (2 * (1.5 * ratio) + (0.1 * Object.keys(minimumWidgetSizeList).length))) > 40) {
        allTabList[i].style.width = (tabDisplayWidth * ratio - (2 * (1.5 * ratio) + (0.1 * Object.keys(minimumWidgetSizeList).length))) + "px";
        if(typeof minimumWidgetSizeList[i] !== 'undefined') {
          delete minimumWidgetSizeList[i];
        }
      } else {
        allTabList[i].style.width = "40px";
        if(typeof minimumWidgetSizeList[i] === 'undefined') {
          minimumWidgetSizeList[i] = true;
        }
      }
    }
  }

  function isEllipsisActive(e) {
    return (e.offsetWidth < e.scrollWidth);
  }

  //タブの高さごとの配列を取得
  function getTabTopList(allTabList){
    var tobTopList = [];
    var topAllay = [];
    for (var i = 0; i < allTabList.length; i++) {
      var id = allTabList[i].id;
      topAllay.push($("#"+id).offset().top);
    }
    var nawtop = topAllay[0];
    var tabAllay = [];
    for (var i = 0; i < topAllay.length; i++) {
      tabAllay.push(allTabList[i].id);
      if(Math.abs(nawtop - topAllay[(i + 1)]) < 25){ // タブが折り返された判定になったら
        var linechange = 0;
      }
      else{
        var linechange = 1;
        nawtop = topAllay[(i + 1)];
      }
      if(linechange == 1 || (topAllay.length - 1) == i){
        tobTopList.push(tabAllay);
        tabAllay = [];
      }
    }
    return tobTopList;
  }

  function countLength(str) {
    var r = 0;
    for (var i = 0; i < str.length; i++) {
        var c = str.charCodeAt(i);
        // Shift_JIS: 0x0 ～ 0x80, 0xa0 , 0xa1 ～ 0xdf , 0xfd ～ 0xff
        // Unicode : 0x0 ～ 0x80, 0xf8f0, 0xff61 ～ 0xff9f, 0xf8f1 ～ 0xf8f3
        if ( (c >= 0x0 && c < 0x81) || (c == 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
            r += 1;
        } else {
            r += 2;
        }
    }
    return r;
  }

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
  //二重サブミット防止
  document.getElementById("input_category_btn").disabled = "disabled";
  saveCategoryEntryDialog({type: 1});
  setTimeout(function() {
    document.getElementById("input_category_btn").disabled = "";
  }, 10000);
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
          location.href = "<?= $this->Html->url('/TDictionaries/index')?>" + '/tabindex:' + (document.querySelectorAll('[id^="ui-id-"]').length);
      }
      });
  }
  else{
    document.getElementById("input_category_btn").disabled = "disabled";
    document.getElementById("input_category_btn").className= "disOffgrayBtn btn-shadow";
  }
}

/* #451 定型文カテゴリ対応 end */
</script>
