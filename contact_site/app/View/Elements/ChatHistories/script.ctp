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

var selectCsv = function(){

}

// 有効/無効ボタンの表示/非表示
var actBtnShow = function(){
  var authorityDelete = "<?= $coreSettings[C_COMPANY_USE_HISTORY_DELETE] ?>";
  var authorityCsv = "<?= $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ?>";
  if(authorityDelete == "" ) {
    return false;
  }
  if(authorityCsv == "") {
    $("#disabled_history_csv_btn").click(function(){
      return false;
    })
  }

  // 選択中の場合
  if ( $('input[name="selectTab"]').is(":checked") ) {
    var list = document.querySelectorAll('input[name^="selectTab"]:checked');
    var url = "/ChatHistories/outputCSVOfChat";
    for (var i = 0; i < list.length; i++){
      url = url + "/" + Number(list[i].value);
    }
    $("#outputCsv a").attr("href", url);

    //一つでもチェックが入ったら
    //CSVボタン有効
    if(authorityCsv == 1) {
      document.getElementById("history_csv_btn").className="btn-shadow disOffgreenBtn";
    }
    //document.getElementById("history_csv_btn").addEventListener('click', openCopyDialog, false);
    //削除ボタン有効
    if(authorityDelete == 1) {
      document.getElementById("history_dustbox_btn").className="btn-shadow disOffredBtn";
      document.getElementById("history_dustbox_btn").addEventListener('click', openDeleteDialog, false);
    }
  }
  else {
    //一つもチェックが無かったら
    //CSVボタン無効
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
  clearChatAndPersonalInfo();
  $.ajax({
    type: 'GET',
    url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'remoteGetCustomerInfo')) ?>",
    data: {
      historyId: id
    },
    dataType: 'html',
    success: function(html){
      var customerData = JSON.parse(html);
      document.getElementById("visitorsId").innerHTML= customerData.THistory.visitors_id;
      document.getElementById("ipAddress").innerHTML= "("+customerData.THistory.ip_address+")";
      if(customerData.LandscapeData) {
        document.getElementById("Landscape").innerHTML= customerData.LandscapeData.org_name;
        $("#LandscapeData a").attr('onclick',"openCompanyDetailInfo("+customerData.LandscapeData.lbc_code+")");
      }
      document.getElementById("visitCounts").innerHTML= customerData.THistoryCount.cnt + "回";
      document.getElementById("platform").innerHTML= userAgentChk.pre(customerData.THistory.user_agent);
      document.getElementById("campaignParam").innerHTML= customerData.campaignParam;
      document.getElementById("landingPage").innerHTML= customerData.THistoryStayLog.title;
      $("#landing a").attr("href", customerData.THistoryStayLog.url);
      if(customerData.tHistoryChatSendingPageData !== null) {
        document.getElementById("chatSendingPage").innerHTML= customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.title;
        $("#chatSending a").attr("href", customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.url);
      }
      else {
        document.getElementById("chatSendingPage").innerHTML= "";
      }
      document.getElementById("separationPage").innerHTML= customerData.tHistoryChatLastPageData.title;
      $("#separation a").attr("href", customerData.tHistoryChatLastPageData.url);
      $("#customerInfo").attr('onclick',"customerInfoSave("+customerData.THistory.id+")");
      $("#restore").attr('onclick',"reloadAct("+customerData.THistory.id+")");
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

function clearChatAndPersonalInfo() {
  document.getElementById("visitorsId").innerHTML= "";
  document.getElementById("ipAddress").innerHTML= "";
  if(document.getElementById("Landscape") !== null) {
    document.getElementById("Landscape").innerHTML= "";
  }
  document.getElementById("visitCounts").innerHTML= "";
  document.getElementById("platform").innerHTML= "";
  document.getElementById("campaignParam").innerHTML= "";
  document.getElementById("landingPage").innerHTML= "";
  document.getElementById("chatSendingPage").innerHTML= "";
  document.getElementById("separationPage").innerHTML= "";
  document.getElementById("pageCount").innerHTML= "";
  document.getElementById("ng-customer-company").value= "";
  document.getElementById("ng-customer-name").value= "";
  document.getElementById("ng-customer-tel").value= "";
  document.getElementById("ng-customer-mail").value= "";
  document.getElementById("ng-customer-memo").value= "";
  document.getElementById('customerId').value= "";
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
  $("#disabled_history_csv_btn").click(function(){
    return false;
  });

  var calcHeaderHeight = function() {
    return $('#history_menu').outerHeight() + $('div.btnSet').outerHeight() + $('label[for="g_chat"]').outerHeight() + $('.dataTables_scrollHead').outerHeight();
  };

  var tableObj = null;
  $(window).on('load', function() {
    document.getElementById("history_body_side").style.display = "block";
    document.getElementById("detail").style.display = "block";
    if(1024 < window.parent.screen.width && window.parent.screen.width < 1367) {
      $("#history_list_side *").css("fontSize", "7px");
      $("#leftContents ul.tabStyle li").css("width", "14.5em");
      $("#leftContents ul.tabStyle li.on").css("width", "14.5em");
    }
    else if(window.parent.screen.width <= 1024) {
      $("#history_list_side *").css("fontSize", "4px");
      $("#leftContents ul.tabStyle li").css("width", "13em");
      $("#leftContents ul.tabStyle li.on").css("width", "13em");
    }

    $.extend( $.fn.dataTable.defaults, {
      language: { url: "/lib/datatables/Japanese.JSON" }
    });

    tableObj = $("#chatTable").DataTable({
      searching: false,
      scroller:true,
      responsive:true,
      scrollX: false,
      scrollY: true,
      scrollCollapse: true,
      paging: false,
      info: false,
      ordering: false,
      columnDefs: [
        { width: 120, targets: 0 }
      ]
    });

    tableObj.on('draw', function(){
      $(".dataTables_scrollBody").css('height',$("#history_body_side").outerHeight() - calcHeaderHeight() - 20);
    });
  });

  //選択したチャット履歴CSV出力
  $('#history_csv_btn').click(function(){
    //return false;
    var authorityCsv = "<?= $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ?>";
    if(authorityCsv == "") {
      $("#disabled_history_csv_btn").click(function(){
        return false;
      })
    }
    //チェックボックスのチェック状態の取得
    var list = document.querySelectorAll('input[name^="selectTab"]:checked');
    if(list.length == 0) {
      return false;
    }
    var selectedList = [];
    document.getElementById("allCheck").checked = false;
    for (var i = 0; i < list.length; i++){
      selectedList.push(Number(list[i].value));
      document.getElementById("selectTab"+Number(list[i].value)).checked = false;
    }
    document.getElementById("history_dustbox_btn").className="btn-shadow disOffgrayBtn";
    document.getElementById("history_csv_btn").className="btn-shadow disOffgrayBtn";
  })

  // 全選択用チェックボックス
  var allCheckElm = document.getElementById('allCheck');
  allCheckElm.addEventListener('click', setAllCheck); // 全選択

  //リサイズ処理
  var screenMode = <?= $screenFlg ?>;

  $(window).resize(function() {
    $("#history_list_side").css('height', window.innerHeight - 145);
    //横並びの場合
    if(screenMode == 1) {
      //$("#pastChatTalk").css('height', window.innerHeight - 364);
      document.getElementById('history_body_side').style.width = $('#history_body_side').outerWidth() + 'px';
      document.getElementById('history_body_side').style.height = $('#history_list_side').outerHeight() + 'px';
      $("#chatContent").css('height', $("#detail").outerHeight() - 105);
      $("#customerInfoScrollArea").css('height', $("#detail").outerHeight() - 39);
      $("#chatHistory").css('height',window.innerHeight - 355);
    }
    //縦並びの場合
    if(screenMode == 2) {
      document.getElementById('history_body_side').style.width = $('#history_list_side').outerWidth() + 'px';
      $("#chatContent").css('height', $("#detail").outerHeight() - 65);
      $("#chatHistory").css('height',$("#history_body_side").outerHeight() - 170);
      $("#customerInfoScrollArea").css('height',$("#detail").outerHeight());
      //$("#pastChatTalk").css('height', window.innerHeight - 540);
    }
    $(".dataTables_scrollBody").css('height',$("#history_body_side").outerHeight() - calcHeaderHeight() - 20);
    tableObj.columns.adjust();
  });

  //縦並びをクリックした場合
  $(document).on('click', '.vertical', function(){
    screenMode = 2;
    splitterObj.destroy();
    splitterObj = null;
    splitterObj = $("#history_list_side").split({
      "orientation": "horizontal",
      //"limit": 50,
      "position": "40%",
      onDrag: function(ev) {
        tableObj.columns.adjust();
      }
    });
    splitterObj.refresh();
    document.getElementById('history_body_side').style.width = $('#history_list_side').outerWidth() + 'px';
    document.getElementById('chatTable').style.width = $('#history_body_side').outerWidth() + 'px';
    document.getElementById('detail').style.width = "100%";
    $("#chatContent").css('height', $("#detail").outerHeight() - 105);
    $("#customerInfoScrollArea").css('height',$("#detail").outerHeight());
    $("#chatHistory").css('height',$("#history_body_side").outerHeight() - 170);
    //$("#pastChatTalk").css('height', window.innerHeight - 540);
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/ChatHistories/changeScreen') ?>",
      success: function(html){
        //modalOpenOverlap.call(window, html, 'p-history-del', '履歴の削除', 'moment');
      }
    });
    tableObj.columns.adjust();
    $(".dataTables_scrollBody").css('height',$("#history_body_side").outerHeight() - calcHeaderHeight() - 15);
 });

  //横並びをクリックした場合
  $(document).on('click', '.side', function(){
    screenMode = 1;
    splitterObj.destroy();
    splitterObj = null;
    splitterObj = $("#history_list_side").split({
      "orientation": "vertical",
      "limit": 50,
      "position": "70%",
      onDrag: function(ev) {
        tableObj.columns.adjust();
      }
    });
    splitterObj.refresh();
    document.getElementById('history_body_side').style.width = $('#history_body_side').outerWidth() + 'px';
    document.getElementById('history_body_side').style.height = $('#history_list_side').outerHeight() + 'px';
    document.getElementById('detail').style.height = "100%";

    //$("#pastChatTalk").css('height', window.innerHeight - 364);
    $("#chatContent").css('height', $("#detail").outerHeight() - 105);
    $("#customerInfoScrollArea").css('height', $("#detail").outerHeight() - 39);
    $("#chatHistory").css('height',window.innerHeight - 355);
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/ChatHistories/changeScreen') ?>",
      success: function(html){
        //modalOpenOverlap.call(window, html, 'p-history-del', '履歴の削除', 'moment');
      }
    });
    tableObj.columns.adjust();
    $(".dataTables_scrollBody").css('height',$("#history_body_side").outerHeight() - calcHeaderHeight() - 20);
 });


    //横並びの場合
    if(<?= $screenFlg ?> == 1) {
      var splitterObj = $("#history_list_side").split({
        "orientation": "vertical",
        //"limit": 500,
        "position": "70%",
        onDrag: function(ev) {
          tableObj.columns.adjust();
        }
      });
      //$("#pastChatTalk").css('height', window.innerHeight - 364);
      document.getElementById('detail').style.height = "100%";
      $("#chatContent").css('height', $("#detail").outerHeight() - 105);
      $("#customerInfoScrollArea").css('height', $("#detail").outerHeight() - 39);
      $("#chatHistory").css('height',window.innerHeight - 355);
      $(".dataTables_scrollBody").css('height',$("#history_body_side").outerHeight() - 170);
    }
    //縦並びの場合$this.attr('data-balloon-position');
    if(<?= $screenFlg ?> == 2) {
      splitterObj = $("#history_list_side").split({
        "orientation": "horizontal",
        //"limit": 50,
        "position": "40%",
        onDrag: function(ev) {
          tableObj.columns.adjust();
        }
      });
      document.getElementById('history_body_side').style.width = $('#history_body_side').outerWidth() + 'px';
      document.getElementById('chatTable').style.width = $('#history_body_side').outerWidth() - 40 + 'px';
      document.getElementById('detail').style.width = "100%";
      $("#chatContent").css('height', $("#detail").outerHeight() - 65);
      $("#customerInfoScrollArea").css('height',$("#detail").outerHeight());
    }

    setTimeout(function(){
      // 初期表示時にテーブルのヘッダとボディがズレることがあるのでタイミングをずらして再描画
      tableObj.columns.adjust();
    }, 500);
});

var onBeforeunloadHandler = function(e) {
  e.returnValue = 'まだ保存されておりません。離脱してもよろしいでしょうか';
};

// 元に戻す処理
function reloadAct(historyId){
  changeFlg = false;
  if(changeFlg == false) {
    window.removeEventListener('beforeunload', onBeforeunloadHandler, false);
  }
  location.href = "<?= $this->Html->url(['controller' => 'ChatHistories', 'action' => 'index']) ?>?id="+ historyId
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
