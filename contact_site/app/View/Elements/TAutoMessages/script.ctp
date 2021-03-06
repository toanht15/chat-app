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

  var targetBalloonList = document.querySelectorAll('.actionValueScenarioLabel');
  for (var i = 0; i < targetBalloonList.length; i++) {
    targetBalloonList[i].addEventListener('mouseenter', balloonApi.show('act')); // 設定したアクション内容のポップアップ表示
    targetBalloonList[i].addEventListener('mouseleave', balloonApi.hide); // 設定したアクション内容のポップアップ非表示
  }

  var targetBalloonList = document.querySelectorAll('.actionValueMessageLabel');
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
  var copyBtn = document.getElementById("tautomessages_copy_btn");
  var checkBtn = document.getElementById("tautomessages_check_btn");
  var inactiveBtn = document.getElementById("tautomessages_inactive_btn");
  var dustboxBtn = document.getElementById("tautomessages_dustbox_btn");
  // 選択中の場合
  if ( $('input[name="selectTab"]').is(":checked") ) {
    //一つでもチェックが入ったら
    //コピーボタン有効
    copyBtn.classList.remove('disOffgrayBtn');
    copyBtn.classList.add('disOffgreenBtn');
    copyBtn.addEventListener('click', openCopyDialog, false);
    //有効にするボタン有効
    checkBtn.classList.remove('disOffgrayBtn');
    checkBtn.classList.add('disOffgreenBtn');
    //無効にするボタン有効
    inactiveBtn.classList.remove('disOffgrayBtn');
    inactiveBtn.classList.add('disOffredBtn');
    //削除ボタン有効
    dustboxBtn.classList.remove('disOffgrayBtn');
    dustboxBtn.classList.add('disOffredBtn');
    dustboxBtn.addEventListener('click', openConfirmDialog, false);
  }
  else {
    //一つもチェックが無かったら
    //コピーボタン無効
    copyBtn.classList.remove('disOffgreenBtn');
    copyBtn.classList.add('disOffgrayBtn');
    copyBtn.removeEventListener('click', openCopyDialog, false);
    //有効にするボタン無効
    checkBtn.classList.remove('disOffgreenBtn');
    checkBtn.classList.add('disOffgrayBtn');
    //無効にするボタン無効
    inactiveBtn.classList.remove('disOffredBtn');
    inactiveBtn.classList.add('disOffgrayBtn');
    //削除ボタン無効
    dustboxBtn.classList.remove('disOffredBtn');
    dustboxBtn.classList.add('disOffgrayBtn');
    dustboxBtn.removeEventListener('click', openConfirmDialog, false);
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
        $("[id='balloon_" + type + "_" +id+"']").css("display:block");
          var balloon_height = $("[id='balloon_" + type + "_" +id+"']").outerHeight();
          var balloon_width = $("[id='balloon_" + type + "_" +id+"']").outerWidth();
        $("[id='balloon_" + type + "_" +id+"']").css("display:none");

        $("[id='balloon_" + type + "_" +id+"']").animate({
          top: offset.top + elm.prop("offsetHeight") + 3,
          left: offset.left + 3
        }, {
          duration: 10,
          complete: function(){
            $("[id^='balloon_']").hide();
            if (balloonApi.flg) {
              $(this).show();
              //表示されたので、位置を取得して下部が見切れるようであれば位置修正
              var balloon_top =  $("[id='balloon_" + type + "_" +id+"']").offset().top;
              var balloon_left = $("[id='balloon_" + type + "_" +id+"']").offset().left;
              var label_height = 0;
              switch(type){
              case "act":
                label_height = $(".actionValueLabel").outerHeight(true);
                label_height = $(".actionValueMessageLabel").outerHeight(true);
                label_height = $(".actionValueScenarioLabel").outerHeight(true);
                break;
              case "cond":
                label_height = $(".conditionValueLabel").outerHeight(true);
                break;
              }
              if(balloon_top + balloon_height > window.innerHeight - 20){
                var reset_top = balloon_top - (label_height + balloon_height);
                $("[id='balloon_" + type + "_" +id+"']").offset({top:reset_top});
              }

              //画面上部にも見切れてしまうようであれば、ボタン左に表示
              if($("[id='balloon_" + type + "_" +id+"']").offset().top < $("#color-bar").outerHeight() + 20){
                var reset_left = balloon_left - balloon_width - 6;


                $("[id='balloon_" + type + "_" +id+"']").offset({top:(window.innerHeight- balloon_height)/2,left:reset_left});
              }


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
  loading.load.start();
  $.ajax({
    type: 'POST',
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
//  modalOpen.call(window, "No." + no + " を削除します、よろしいですか？", 'p-confirm', 'トリガー設定', 'moment');
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

//トリガー設定の削除
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
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'トリガー設定（条件設定）', 'moment');
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
  modalOpen.call(window, "コピーします、よろしいですか？", 'p-confirm', 'トリガー設定（条件設定）', 'moment');
  popupEvent.closePopup = toExecutableOnce(function(){
    loading.load.start();
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
  //トリガー設定並べ替えチェックボックスが入っているときはリンク無効とする
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
  //トリガー設定並べ替えチェックボックスが入っているときはリンク無効とする
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

//トリガー設定のソートモード
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
    //トリガー設定登録ボタン押下不可
    var addBtn = document.getElementById('tautomessages_add_btn');
    addBtn.classList.remove('disOffgreenBtn');
    addBtn.classList.add('disOffgrayBtn');
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

//トリガー設定のソート順を保存
var confirmSort = function(){
  modalOpen.call(window, "編集内容を保存します。<br/><br/>よろしいですか？<br/>", 'p-sort-save-confirm', 'トリガー設定並び替えの保存', 'moment');
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

//トリガー設定ソートを保存
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

//トリガー設定のソート順を取得
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

var fileObj = null;
var loadData = null;
var openSelectFile = function() {
  var target = $('#selectFileInput');
  target.on("click", function(event){
    $(this).val(null);
  }).on("change",function(event){
    if(target[0].files[0]) {
      fileObj = target[0].files[0];
      // ファイルの内容は FileReader で読み込みます.
      var fileReader = new FileReader();
      fileReader.onload = function (event) {
        var split = fileObj.name.split(".");
        var targetExtension = split[split.length-1];
        if(targetExtension === "xlsm" || targetExtension === "xlsx") {
          // event.target.result に読み込んだファイルの内容が入っています.
          // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
          loadData = event.target.result;
          _showConfirmDialog("<div style='text-align:center'><p>指定されたファイル【" + fileObj.name + "】をインポートします。</p>" +
              "<br><p style='color:red; margin-top: 0'>現在の設定内容は削除され、指定されたファイルの内容に置き換わります。</p>" +
              "<p style='color:red'>（全件洗い替え）\n</p>" +
              "<p>※インポート後に元に戻すことはできません。</p>" +
              "<p>※現在の設定内容をエクスポートしておくことを推奨します。</p>" +
              "<br>インポートしてよろしいですか？</div>");
        } else {
          _showConfirmDialog("<div class='confirm'>指定されたファイル【" + fileObj.name + "】は対応していません。</div>");
          $('#popupCloseBtn').css('display', 'block');
          $('#uploadCloseBtn').css('display', 'block');
          $('#uploadExcelBtn').css('display', 'none');
          $('#uploadCancelBtn').css('display', 'none');
          $('#popup-button').css('display', 'block');
          popupEvent.resize();
          loadData = null;
          fileObj = null;
        }
      };
      fileReader.readAsArrayBuffer(fileObj);
    }
  });
  $('#selectFileInput').trigger('click');
};

var _showConfirmDialog = function(message) {
  modalOpen.call(window, message, 'p-auto-importexcel-upload', 'インポート確認', 'moment');
  popupEvent.uploadBtnClicked = function() {
    $('#popupCloseBtn').css('display', 'none');
    uploadFile(fileObj, loadData);
  };
};

var uploadFile = function(fileObj, loadFile) {
  var fd = new FormData();
  var blob = new Blob([loadFile], {type: fileObj.type});
  var index = Number("<?= $this->Paginator->params()["page"] ?>");
  fd.append("type", 'speechContent');
  fd.append("lastPage", index);
  fd.append("file", blob, fileObj.name);

  $('#popup-title').html('インポート処理中');
  $('#popup-main').html('<div class="confirm">インポート中（0％）</div>');
  $('#popup-button').css('display', 'none');
  popupEvent.resize();

  $.ajax({
    url  : "<?= $this->Html->url('/TAutoMessages/bulkImport') ?>",
    type : "POST",
    data : fd,
    cache       : false,
    contentType : false,
    processData : false,
    dataType    : "json",
    xhr : function(){
      var XHR = $.ajaxSettings.xhr();
      if(XHR.upload){
        XHR.upload.addEventListener('progress',function(e){
          var uploadProgress = parseInt(e.loaded/e.total*10000)/100;
          $('#popup-main').html('<div class="confirm">インポート中（' + uploadProgress + '％）</div>');
          if(uploadProgress === 100) {
            $('#popup-main').html('<div class="confirm">インポート処理中です。しばらくお待ち下さい。</div>');
          }
        }, false);
      }
      return XHR;
    }
  })
  .done(function(data, textStatus, jqXHR){
    console.log(JSON.stringify(data));
    if(data.success) {
      $('#popup-main').html('<div class="confirm">インポートが完了しました。<br>ページを再読み込みします。</div>');
      $('#popup-button').css('display', '');
      $('#uploadExcelBtn').css('display', 'none');
      $('#uploadCancelBtn').css('display', 'none');
      $('#reloadBtn').css('display', '').on('click', function(e){
        $(this).css('display','none');
        $('#popup-main').html('<div class="confirm">再読み込み中です</div>');
        popupEvent.resize();
        location.href = "<?= $this->Html->url(['controller'=>'TAutoMessages', 'action' => 'index/page:']) ?>" + data.showPageNum;
      });
      popupEvent.resize();
    } else {
      var html = '<p id="importErrorMessage">インポート中にエラーが発生したのでインポートをキャンセルします。<br>以下のエラー内容を確認してください。</p>';
      html += '<div id="errorListScroll">';
      html += '  <div id="errorList">';
      if(typeof(data.errorMessages) === 'object') {
        if(data.errorMessages.hasOwnProperty('type')) {
          html += '<p class="error-row"><span class="error-content">' + data.errorMessages.message + '</span></p>'
        } else {
          Object.keys(data.errorMessages).forEach(function(key){
            Object.keys(data.errorMessages[key]).forEach(function(column) {
              for(var i = 0; i < data.errorMessages[key][column].length; i++) {
                html += '<p class="error-row"><span class="error-matrix">【' + key + ' 行目' + column + ' 列】</span><span class="error-content">' + data.errorMessages[key][column][i] + '</span></p>'
              }
            });
          });
        }
      }
      html += '  </div>';
      html += '</div>';
      $('#popupCloseBtn').css('display', 'block');
      $('#uploadExcelBtn').css('display', 'none');
      $('#uploadCancelBtn').css('display', 'none');
      $('#uploadCloseBtn').css('display', 'block');
      $('#popup-button').css('display', 'block');
      $('#popup-main').html(html);
      popupEvent.resize();
    }
  })
  .fail(function(jqXHR, textStatus, errorThrown){
    alert("fail");
  });
}

$(document).ready(function(){

  <?php if(isset($coreSettings[C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE]) && $coreSettings[C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE]): ?>
  var fadeOutLayerMenu = function() {
    $("#autoMessageLayerMenu").fadeOut("fast");
  };

  var fadeInLayerMenu = function() {
    $("#autoMessageLayerMenu").fadeIn("fast");
  };

  $('#importExcelBtn').on('click', function(e) {
    e.stopPropagation();
    var menu = document.getElementById("autoMessageLayerMenu").style.display;
    if(menu == "block"){
      fadeOutLayerMenu();
    }
    else{
      fadeInLayerMenu();
    }
  });

  $(document).on('click', fadeOutLayerMenu);
  <?php endif; ?>
});
</script>
