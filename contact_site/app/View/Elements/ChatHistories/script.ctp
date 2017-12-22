<script type="text/javascript">
var changeFlg = false;
//モーダル画面
function openSearchRefine(){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'ChatHistories', 'action' => 'remoteSearchCustomerInfo']) ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '高度な検索', 'moment');
    }
  });
}

//セッションクリア(条件クリア)
function sessionClear(){
  location.href = "<?=$this->Html->url(array('controller' => 'ChatHistories', 'action' => 'portionClearSession'))?>";
}

//ユーザー情報登録
function customerInfoSave(historyId) {
  var dataList = {},
  customerId = document.getElementById('customerId').value;
  visitorsId = $('#visitorsId').text();

  $(".infoData").each(function(i){
    if(i <= 4) {
      dataList[$(this).data('key')] = this.value;
    }
  });
  $.ajax({
    type: 'GET',
    url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'remoteSaveCustomerInfo')) ?>",
    data: {
      customerId: customerId,
      historyId: historyId,
      visitorsId: visitorsId,
      saveData: dataList
    },
    dataType: 'json',
    success: function(historyId){
      location.href = "<?= $this->Html->url(['controller' => 'ChatHistories', 'action' => 'index']) ?>?id="+ historyId;
    }
  });
}


document.body.onload = function(){

  // チェックボックス群
  var checkBoxList = document.querySelectorAll('[id^="selectTab"]');
  for (var i = 0; i < checkBoxList.length; i++) {
    checkBoxList[i].addEventListener('change', actBtnShow); // 有効無効ボタンの表示切り替え
  }
};

// 有効/無効ボタンの表示/非表示
var actBtnShow = function(){
  // 選択中の場合
  if ( $('input[name="selectTab"]').is(":checked") ) {
    var list = document.querySelectorAll('input[name^="selectTab"]:checked');
    var url = "/ChatHistories/outputCSVOfChat";
    for (var i = 0; i < list.length; i++){
      url = url + "/" + Number(list[i].value);
    }
    $("#outputCsv a").attr("href", url);

    //一つでもチェックが入ったら
    //コピーボタン有効
    document.getElementById("history_csv_btn").className="btn-shadow disOffgreenBtn";
    //document.getElementById("history_csv_btn").addEventListener('click', openCopyDialog, false);
    //削除ボタン有効
    document.getElementById("history_dustbox_btn").className="btn-shadow disOffredBtn";
    document.getElementById("history_dustbox_btn").addEventListener('click', openDeleteDialog, false);
  }
  else {
    //一つもチェックが無かったら
    //コピーボタン無効
    document.getElementById("history_csv_btn").className="btn-shadow disOffgrayBtn";
    //document.getElementById("tautomessages_copy_btn").removeEventListener('click', openCopyDialog, false);
    //削除ボタン無効
    document.getElementById("history_dustbox_btn").className="btn-shadow disOffgrayBtn";
    document.getElementById("history_dustbox_btn").removeEventListener('click', openDeleteDialog, false);;
  }
};

//履歴削除モーダル画面
function openDeleteDialog(){
  //チェックボックスのチェック状態の取得
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
  }

  $.ajax({
    type: 'post',
    dataType: 'html',
    data: {
      selectedList:selectedList
    },
    cache: false,
    url: "<?= $this->Html->url('/ChatHistories/openChatEntryDelete') ?>",
    success: function(html){
      modalOpenOverlap.call(window, html, 'p-history-del', 'チャット履歴の削除', 'moment');
    }
  });
};

//選択したチャット履歴CSV出力
function selectedOutputCSV(){
  //チェックボックスのチェック状態の取得
  var list = document.querySelectorAll('input[name^="selectTab"]:checked');
  var selectedList = [];
  document.getElementById("allCheck").checked = false;
  for (var i = 0; i < list.length; i++){
    selectedList.push(Number(list[i].value));
    document.getElementById("selectTab"+Number(list[i].value)).checked = false;
  }
};

//選択したチャット履歴削除
function selectedOutputCSV(){
  document.getElementById("selectDeleteChat").checked = false;
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


//ユーザー情報表示変更
function openChatById(id) {
  $.ajax({
    type: 'GET',
    url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'remoteGetCustomerInfo')) ?>",
    data: {
      historyId: id
    },
    dataType: 'html',
    success: function(html){
      var customerData = JSON.parse(html);
      document.getElementById("visitorsId").innerHTML= customerData.THistory.visitors_id;LandscapeData
      document.getElementById("ipAddress").innerHTML= customerData.THistory.ip_address;
      document.getElementById("Landscape").innerHTML= customerData.LandscapeData.org_name;
      $("#LandscapeData a").attr('onclick',"openCompanyDetailInfo("+customerData.LandscapeData.lbc_code+")");
      document.getElementById("visitCounts").innerHTML= customerData.THistoryCount.cnt;
      document.getElementById("platform").innerHTML= userAgentChk.pre(customerData.THistory.user_agent);
      document.getElementById("landingPage").innerHTML= customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.title;
      $("#landing a").attr("href", customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.url);
      document.getElementById("chatSendingPage").innerHTML= customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.title;
      $("#chatSending a").attr("href", customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.url);
      document.getElementById("separationPage").innerHTML= customerData.tHistoryChatLastPageData.LastSpeechSendPage.title;
      $("#separation a").attr("href", customerData.tHistoryChatLastPageData.LastSpeechSendPage.url);
      $("#customerInfo").attr('onclick',"customerInfoSave("+customerData.THistory.id+")");
      document.getElementById("pageCount").innerHTML= customerData.pageCount[0].count;
      $("#moveHistory").attr('onclick',"openHistoryById("+customerData.THistory.id+")");
      document.getElementById("ng-customer-company").value = customerData.informations.company;
      document.getElementById("ng-customer-name").value = customerData.informations.name;
      document.getElementById("ng-customer-tel").value = customerData.informations.tel;
      document.getElementById("ng-customer-mail").value = customerData.informations.mail;
      document.getElementById("ng-customer-memo").value = customerData.informations.memo;
      document.getElementById('customerId').value = customerData.MCustomer.id;
    }
  });
}
// Change the selector if needed
var  table = $('.scroll');
var  bodyCells = table.find('tbody tr:first').children();
var  colWidth;

// Adjust the width of thead cells when window resizes
$(window).resize(function() {
    // Get the tbody columns width array
    colWidth = bodyCells.map(function() {
        return $(this).width();
    }).get();

    // Set the width of thead columns
    table.find('thead tr').children().each(function(i, v) {
      $(v).width(colWidth[i]);
    });
}).resize(); // Trigger resize handler
//var splitterObj = null;


$(function(){
  $(document).ready(function(){
    //横並びの場合
    if(<?= $screenFlg ?> == 1) {
      $("#chatContent").css('height', window.innerHeight - 210);
    }
    //縦並びの場合
    if(<?= $screenFlg ?> == 2) {
      $("#chatContent").css('height', window.innerHeight - 495);
    }
    $("#pastChatTalk").css('height', window.innerHeight - 390);
    $("#history_list_side").css('height', window.innerHeight - 150);
  });


  // 全選択用チェックボックス
  var allCheckElm = document.getElementById('allCheck');
  allCheckElm.addEventListener('click', setAllCheck); // 全選択

  //リサイズ処理
  $(window).resize(function() {;
  $("#history_list_side").css('height', window.innerHeight - 150);
  //横並びの場合
  if(<?= $screenFlg ?> == 1) {
    $("#chatContent").css('height', window.innerHeight - 210);
  }
  //縦並びの場合
  if(<?= $screenFlg ?> == 2) {
    $("#chatContent").css('height', window.innerHeight - 495);
  }
  //$("#chatContent").css('max-height', '62em');
  $("#pastChatTalk").css('height', window.innerHeight - 410);
  $("#pastChatTalk").css('max-height', '32.3em');
  });

  //縦並びをクリックした場合
  $(document).on('click', '.vertical', function(){
    splitterObj.destroy();
    splitterObj = null;
    splitterObj = $("#history_list_side").height(800).split({
      "orientation": "horizontal",
      //"limit": 50,
      "position": "40%"
    });
    splitterObj.refresh();
    document.getElementById('history_body_side').style.width = "100%";
    document.getElementById('chatTable').style.width = "100%";
    document.getElementById('detail').style.width = "100%";
    $("#chatContent").css('height', window.innerHeight - 495);
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/ChatHistories/changeScreen') ?>",
      success: function(html){
        //modalOpenOverlap.call(window, html, 'p-history-del', '履歴の削除', 'moment');
      }
    });
 });

  //横並びをクリックした場合
  $(document).on('click', '.side', function(){
    splitterObj.destroy();
    splitterObj = null;
    splitterObj = $("#history_list_side").height(800).split({
      "orientation": "vertical",
      "limit": 50,
      "position": "45%"
    });
    splitterObj.refresh();
    document.getElementById('history_body_side').style.height = "100%";
    document.getElementById('detail').style.height = "100%";
    $("#chatContent").css('height', window.innerHeight - 210);
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/ChatHistories/changeScreen') ?>",
      success: function(html){
        //modalOpenOverlap.call(window, html, 'p-history-del', '履歴の削除', 'moment');
      }
    });
 });

    //横並びの場合
    if(<?= $screenFlg ?> == 1) {
      var splitterObj = $("#history_list_side").height(800).split({
        "orientation": "vertical",
        //"limit": 500,
        "position": "45%"
      });
    }

    //縦並びの場合
    if(<?= $screenFlg ?> == 2) {
      splitterObj = $("#history_list_side").height(800).split({
        "orientation": "horizontal",
        "limit": 50,
        "position": "40%"
      });
    }
});

var onBeforeunloadHandler = function(e) {
  e.returnValue = 'まだ保存されておりません。離脱してもよろしいでしょうか';
};

// 元に戻す処理
function reloadAct(){
  changeFlg = false;
  if(changeFlg == false) {
    window.removeEventListener('beforeunload', onBeforeunloadHandler, false);
  }
  window.location.reload();
}

//履歴チャット削除モーダル画面
function openChatDeleteDialog(id,historyId,message,created){
  $.ajax({
    type: 'post',
    dataType: 'html',
    data: {
      id:id,
      historyId:historyId,
      message:message,
      created:created
    },
    cache: false,
    url: "<?= $this->Html->url('/ChatHistories/openChatSentenceEntryDelete') ?>",
    success: function(html){
      modalOpenOverlap.call(window, html, 'p-history-del', '履歴の削除', 'moment');
    }
  });
}
</script>
