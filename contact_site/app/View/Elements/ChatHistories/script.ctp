<script type="text/javascript">
var changeFlg = false;
var changeScreenMode = "";
var customerInfoSettings = <?php echo json_encode($customerInformationList, JSON_UNESCAPED_UNICODE) ?>;
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
  var dataList = {};
  customerId = document.getElementById('customerId').value;
  visitorsId = $('#visitorsId').text();

  $(".infoData").each(function(i){
    dataList[$(this).data('key')] = this.value;
  });
  $.ajax({
    type: 'GET',
    url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'saveCustomerInfo')) ?>",
    data: {
      customerId: customerId,
      historyId: historyId,
      visitorsId: visitorsId,
      saveData: dataList
    },
    dataType: 'json',
    success: function(data){
      location.href = "<?= $this->Html->url(['controller' => 'ChatHistories', 'action' => 'index']) ?>?id="+ historyId+'&edit';
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

  // 選択中の場合
  if ( $('input[name="selectTab"]').is(":checked") ) {
    var screenMode = <?= $screenFlg ?>;
    if(changeScreenMode != "" && screenMode != changeScreenMode) {
      screenMode = changeScreenMode;
    }
    $("#btnSet").css('display', 'block');
    var list = document.querySelectorAll('input[name^="selectTab"]:checked');
    var url = "/ChatHistories/outputCSVOfChat";
    for (var i = 0; i < list.length; i++){
      url = url + "/" + Number(list[i].value);
    }
    $("#outputCsv a").attr("href", url);
    //一つでもチェックが入ったら
    //CSVボタン有効
    if(authorityCsv == 1) {
      document.getElementById("history_csv_btn").classList.remove("disOffgrayBtn");
      document.getElementById("history_csv_btn").classList.add("disOffgreenBtn");
    }
    //document.getElementById("history_csv_btn").addEventListener('click', openCopyDialog, false);
    //削除ボタン有効
    if(authorityDelete == 1 && document.getElementById("history_dustbox_btn") != null ) {
      document.getElementById("history_dustbox_btn").classList.remove("disOffgrayBtn");
      document.getElementById("history_dustbox_btn").classList.add("disOffredBtn");
      document.getElementById("history_dustbox_btn").addEventListener('click', openDeleteDialog, false);
    }
    if(authorityCsv == 1 || authorityDelete == 1) {
      SetListHeight(screenMode);
    }
  }
  else {
    var screenMode = <?= $screenFlg ?>;
    if(changeScreenMode != "" && screenMode != changeScreenMode) {
      screenMode = changeScreenMode;
    }
    //一つもチェックが無かったら
    //CSVボタン無効
    if(authorityCsv == 1) {
      document.getElementById("history_csv_btn").classList.remove("disOffgreenBtn");
      document.getElementById("history_csv_btn").classList.add("disOffgrayBtn");
    }
    //document.getElementById("tautomessages_copy_btn").removeEventListener('click', openCopyDialog, false);
    //削除ボタン無効
    if(authorityDelete == 1 && document.getElementById("history_dustbox_btn") != null) {
      document.getElementById("history_dustbox_btn").classList.remove("disOffredBtn");
      document.getElementById("history_dustbox_btn").classList.add("disOffgrayBtn");
      document.getElementById("history_dustbox_btn").removeEventListener('click', openDeleteDialog, false);
    }
    if(authorityCsv == 1 || authorityDelete == 1) {
      $("#btnSet").css('display', 'none');
      SetListHeight(screenMode);
    }
  }
  if(authorityDelete == "" ) {
    return false;
  }
  if(authorityCsv == "") {
    $("#disabled_history_csv_btn").click(function(){
      return false;
    })
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

// Change the selector if needed
var  table = $('.scroll');
var  bodyCells = table.find('tbody tr:first').children();
var  colWidth;



/****************************/
/**一番最初に呼ばれる関数群**/
/****************************/

$(function(){
  $("#btnSet").css('display');
  $("#disabled_history_csv_btn").click(function(){
    return false;
  });
  $("#info").css('max-width','30%');

  var calcHeaderHeight = function() {
    return $('#history_menu').outerHeight() + $('div.btnSet').outerHeight() + $('label[for="g_chat"]').outerHeight() + $('.dataTables_scrollHead').outerHeight();
  };
  var tableObj = null;
  $(window).on('load', function() {
    document.getElementById("history_body_side").style.display = "block";
    document.getElementById("detail").style.display = "block";
    $(".info").css('width',$("#info").outerWidth());

    $.extend( $.fn.dataTable.defaults, {
      language: { url: "/lib/datatables/Japanese.JSON" }
    });

    tableObj = $("#chatTable").DataTable({
      searching: false,
      scroller:true,
      responsive:true,
      scrollX: false,
      scrollY: true,
      scrollCollapse: false,
      paging: false,
      info: false,
      ordering: false,
      columnDefs: [
        { width: 120, targets: 0 }
      ]
    });
    tableObj.on('draw', function(){
      if(<?= $screenFlg ?> == 1) {
        $(".info").css('width',$("#info").outerWidth());
        $(".dataTables_scrollHeadInner").css('width',$(".dataTables_scrollHead").outerWidth() - 17);
        $("#chatHistory").css('height','100%');
      }
      if(<?= $screenFlg ?> == 2) {
        $(".dataTables_scrollBody").css('height',$("#history_body_side").outerHeight() - 130);
        $("#chatHistory").css('height','100%');
      }
      SetListHeight(<?= $screenFlg ?> );
    });
  });

  //選択したチャット履歴CSV出力
  $('#history_csv_btn').click(function(){
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
    if(document.getElementById("history_dustbox_btn") != null) {
      document.getElementById("history_dustbox_btn").classList.remove("disOffredBtn");
      document.getElementById("history_dustbox_btn").classList.add("disOffgrayBtn");
    }
    document.getElementById("history_dustbox_btn").classList.remove("disOffgreenBtn");
    document.getElementById("history_dustbox_btn").classList.add("disOffgrayBtn");
    $("#btnSet").css('display', 'none');
    SetListHeight(<?= $screenFlg ?> );
  })

  // 全選択用チェックボックス
  var allCheckElm = document.getElementById('allCheck');
  allCheckElm.addEventListener('click', setAllCheck); // 全選択

  //リサイズ処理
  var screenMode = <?= $screenFlg ?>;

  $(window).resize(function() {
    $("#history_list_side").css('height', window.innerHeight - 135);
    //横並びの場合
    if(screenMode == 1) {
      splitterObj.refresh();
      document.getElementById('history_body_side').style.width = $('#history_body_side').outerWidth() + 'px';
      document.getElementById('history_body_side').style.height = $('#history_list_side').outerHeight() + 'px';
      $("#chatContent").css('height', $("#detail").outerHeight() - 105);
      $("#customerInfoScrollArea").css('height', $("#detail").outerHeight() - 39);
      $("#chatHistory").css('height','100%');
      $("#info").css({'width':'50px','word-break':'break-all'});
    }
    //縦並びの場合
    if(screenMode == 2) {
      splitterObj.refresh();
      document.getElementById('history_body_side').style.width = $('#history_list_side').outerWidth() + 'px';
      $("#chatContent").css('height', $("#detail").outerHeight() - 65);
      $("#chatHistory").css('height','100%');
      $("#customerInfoScrollArea").css('height',$("#detail").outerHeight());
    }
    tableObj.columns.adjust();
    SetListHeight(screenMode);
    $('#content').css('overflow-y','hidden');
  });

  //縦並びをクリックした場合
  $(document).on('click', '.vertical', function(){
    if(screenMode == 2){
      return;
    }
    splitterObj.destroy();
    splitterObj = null;
    splitterObj = $("#history_list_side").split({
      "orientation": "horizontal",
      "position": "40%",
      onDrag: function(ev) {
        tableObj.columns.adjust();
      }
    });
    splitterObj.refresh();
    document.getElementById('history_body_side').style.width = $('#history_list_side').outerWidth() + 'px';
    document.getElementById('chatTable').style.width = $('#history_body_side').outerWidth() + 'px';
    document.getElementById('detail').style.width = "100%";
    document.getElementById('verticalToggleMenu').style.display = "none";
    document.getElementById('info').style.display = "none";
    document.getElementById('kind').style.display = "";
    document.getElementById('firstTimeReceivingLabel').style.display = "";
    document.getElementById('ip').style.display = "";
    document.getElementById('visitor').style.display = "";
    document.getElementById('responsible').style.display = "";
    $("#info").css('width','0px');
    $(".eachInfo").css('display','none');
    $(".eachKind").css('display','');
    $(".eachFirstSpeechTime").css('display','');
    $(".eachIpAddress").css('display','');
    $(".eachVisitor").css('display','');
    $(".responsible").css('display','');
    $("#chatContent").css('height', $("#detail").outerHeight() - 65);
    $("#customerInfoScrollArea").css('height',$("#detail").outerHeight());
    $("#chatHistory").css('height','100%');
    $(".trHeight").css('height','50px');
    $(".deleteChat").attr('data-balloon-position',45);

    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/ChatHistories/changeScreen') ?>",
      success: function(html){
      }
    });
    tableObj.columns.adjust();
    screenMode = 2;
    changeScreenMode = 2;
    SetListHeight(screenMode);
 });

  //横並びをクリックした場合
  $(document).on('click', '.side', function(){
    if(screenMode == 1){
        return;
      }
    splitterObj.destroy();
    splitterObj = null;
    splitterObj = $("#history_list_side").split({
      "orientation": "vertical",
      "limit": 50,
      "position": "60%",
      onDrag: function(ev) {
        tableObj.columns.adjust();
      }
    });
    splitterObj.refresh();
    document.getElementById('history_body_side').style.width = $('#history_body_side').outerWidth() + 'px';
    document.getElementById('history_body_side').style.height = $('#history_list_side').outerHeight() + 'px';
    document.getElementById('detail').style.height = "100%";
    document.getElementById('verticalToggleMenu').style.display = "block";
    document.getElementById('info').style.display = "";
    document.getElementById('kind').style.display = "none";
    document.getElementById('firstTimeReceivingLabel').style.display = "none";
    document.getElementById('ip').style.display = "none";
    document.getElementById('visitor').style.display = "none";
    document.getElementById('responsible').style.display = "none";
    $(".eachInfo").css('display','');
    $(".eachKind").css('display','none');
    $(".eachFirstSpeechTime").css('display','none');
    $(".eachIpAddress").css('display','none');
    $(".eachVisitor").css('display','none');
    $(".responsible").css('display','none');
    $(".trHeight").css('height','72px');
    $("#chatContent").css('height', $("#detail").outerHeight() - 105);
    $("#customerInfoScrollArea").css('height', $("#detail").outerHeight() - 39);
    $("#chatHistory").css('height','100%');
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/ChatHistories/changeScreen') ?>",
      success: function(html){
      }
    });
    tableObj.columns.adjust();
    $(".info").css('width',$("#info").outerWidth());
    screenMode = 1;
    changeScreenMode = 1;
    SetListHeight(screenMode);
 });


    //横並びの場合
    if(<?= $screenFlg ?> == 1) {
      var splitterObj = $("#history_list_side").split({
        "orientation": "vertical",
        "position": "60%",
        onDrag: function(ev) {
          tableObj.columns.adjust();
        }});



      document.getElementById('detail').style.height = "100%";
      document.getElementById('verticalToggleMenu').style.display = "block";
      $("#chatContent").css('height', $("#detail").outerHeight() - 105);
      $("#customerInfoScrollArea").css('height', $("#detail").outerHeight() - 39);
      $("#chatHistory").css('height','100%');
      $(".trHeight").css('height','72px');
      SetListHeight(<?= $screenFlg ?> );
    }
    //縦並びの場合
    if(<?= $screenFlg ?> == 2) {
      var splitterObj = $("#history_list_side").split({
        "orientation": "horizontal",
        "position": "40%",
        onDrag: function(ev) {
          tableObj.columns.adjust();
        }
      });
      document.getElementById('history_body_side').style.width = $('#history_body_side').outerWidth() + 'px';
      document.getElementById('chatTable').style.width = $('#history_body_side').outerWidth() - 40 + 'px';
      document.getElementById('detail').style.width = "100%";
      document.getElementById('verticalToggleMenu').style.display = "none";
      document.getElementById('info').style.display = "none";
      document.getElementById('kind').style.display = "";
      document.getElementById('firstTimeReceivingLabel').style.display = "";
      document.getElementById('ip').style.display = "";
      document.getElementById('visitor').style.display = "";
      document.getElementById('responsible').style.display = "";
      $(".eachInfo").css('display','none');
      $(".eachKind").css('display','');
      $(".eachFirstSpeechTime").css('display','');
      $(".eachIpAddress").css('display','');
      $(".eachVisitor").css('display','');
      $(".responsible").css('display','');
      $("#chatContent").css('height', $("#detail").outerHeight() - 65);
      $("#customerInfoScrollArea").css('height',$("#detail").outerHeight());
      $("#chatHistory").css('height','100%');
      $(".trHeight").css('height','50px');
      SetListHeight(<?= $screenFlg ?> );
    }

    setTimeout(function(){
      // 初期表示時にテーブルのヘッダとボディがズレることがあるのでタイミングをずらして再描画
      if(tableObj && tableObj.columns) {
        tableObj.columns.adjust();
      }
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
  location.href = "<?= $this->Html->url(['controller' => 'ChatHistories', 'action' => 'index']) ?>?id="+ historyId+'&edit';
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

var jqxhr;
//ユーザー情報表示変更
function openChatById(id) {
  clearChatAndPersonalInfo();
  if (jqxhr) {
    jqxhr.abort();
  }
  jqxhr = $.ajax({
    type: 'GET',
    url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'getCustomerInfo')) ?>",
    data: {
      historyId: id
    },
    dataType: 'html',
    success: function(html){
      var customerData = JSON.parse(html);
      document.getElementById("visitorsId").innerHTML= customerData.THistory.visitors_id;
      document.getElementById("ipAddress").innerHTML= "("+customerData.THistory.ip_address+")";
      if(customerData.LandscapeData != null) {
        document.getElementById("Landscape").innerHTML= customerData.LandscapeData.org_name;
        $("#LandscapeData a").attr('onclick',"openCompanyDetailInfo("+customerData.LandscapeData.lbc_code+")");
      }
      /* 必ず治す！！ */
      document.getElementById("visitCounts").innerHTML= customerData.THistoryCount.cnt + "回";
      document.getElementById("platform").innerHTML= userAgentChk.pre(customerData.THistory.user_agent);
      document.getElementById("campaignParam").innerHTML= customerData.campaignParam;
      if(customerData.THistory.referrer_url !== null) {
        document.getElementById("referrerUrl").innerHTML = customerData.THistory.referrer_url;
        $("#referrer a").attr("href", customerData.THistory.referrer_url);
      }
      else {
        document.getElementById("referrerUrl").innerHTML= "";
      }
      if(customerData.landingData !== null) {
        document.getElementById("landingPage").innerHTML= customerData.landingData.title;
        $("#landing a").attr("href", customerData.landingData.url);
      }
      else {
        document.getElementById("landingPage").innerHTML= "";
      }
      if(customerData.tHistoryChatSendingPageData !== null) {
        document.getElementById("chatSendingPage").innerHTML= customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.title;
        $("#chatSending a").attr("href", customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.url);
      }
      else {
        document.getElementById("chatSendingPage").innerHTML= "";
      }
      if(customerData.tHistoryChatLastPageData !== null) {
        document.getElementById("separationPage").innerHTML= customerData.tHistoryChatLastPageData.title;
        $("#separation a").attr("href", customerData.tHistoryChatLastPageData.url);
      }
      else {
        document.getElementById("separationPage").innerHTML= "";
      }
      $("#customerInfo").attr('onclick',"customerInfoSave("+customerData.THistory.id+")");
      $("#restore").attr('onclick',"reloadAct("+customerData.THistory.id+")");
      document.getElementById("pageCount").innerHTML= customerData.pageCount[0].count;
      $("#moveHistory").attr('onclick',"openHistoryById("+customerData.THistory.id+")");

      for(var i=0; i < customerInfoSettings.length; i++) {
        if(customerData.informations && customerData.informations[customerInfoSettings[i].item_name]) {
          $('#ng-customer-custom-'+customerInfoSettings[i].id).get(0).value = customerData.informations[customerInfoSettings[i].item_name];
        } else {
          $('#ng-customer-custom-'+customerInfoSettings[i].id).get(0).value = "";
        }
      }

      if(customerData.MCustomer && customerData.MCustomer.id) {
        document.getElementById('customerId').value = customerData.MCustomer.id;
      }
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
  document.getElementById("referrerUrl").innerHTML= "";
  document.getElementById("landingPage").innerHTML= "";
  document.getElementById("chatSendingPage").innerHTML= "";
  document.getElementById("separationPage").innerHTML= "";
  document.getElementById("pageCount").innerHTML= "";
  $('#rightContens .detailForm').find('li').each(function(index, element) {
    $(elem).find('input,textarea').val("");
  });
  document.getElementById('customerId').value= "";
}

function SetListHeight(type){
  console.log(type);
  //リストの高さを計算するための変数群を初期化
  var List_offsetHeight = 0;
  if(type==1){
    var btnHeight = 0;
    var menuHeight = 0;
    // history_menuはpaddingを高さに含めない
    // 3はスクロールバーを表示させないようにするマージン
    var adjustHeight = $("#history_menu").height() + $(".fLeft").outerHeight() + $(".dataTables_scrollHead").outerHeight() - 3;
    //CSV出力、削除ボタンが表示されている場合、高さを取得
    if($("#btnSet").css('display') != "none"){
      btnHeight = parseInt($("#btnSet").css('height'));
    }
    //検索条件が表示されている場合、高さを取得
    if($(".seach_menu")[0] != null){
      menuHeight = parseInt($(".seach_menu").css('height')) + 13;
    }
    //スクロール変化対象のリスト以外の高さを取得し計算する。
    List_offsetHeight = $("#history_body_side").outerHeight() - (adjustHeight + btnHeight + menuHeight);
  }else if(type==2){
    var adjustHeight = $('#chatHistory').offset().top - $('#history_body_side').offset().top + 1; // +1はサイズ調整用マージン
    List_offsetHeight = $("#history_body_side").outerHeight() - adjustHeight;
  }
  $(".dataTables_scrollBody").css({'height':List_offsetHeight});
}
</script>
