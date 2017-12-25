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

function customerInfoSave() {
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
    url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteSaveCustomerInfo')) ?>",
    data: {
      customerId: customerId,
      visitorsId: visitorsId,
      saveData: dataList
    },
    dataType: 'json',
    success: function(ret){
      location.href = location.href;
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
    console.log('ここには入ってる');
    var list = document.querySelectorAll('input[name^="selectTab"]:checked');
    var url = "/ChatHistories/outputCSVOfChat";
    for (var i = 0; i < list.length; i++){
      url = url + "/" + Number(list[i].value);
    }
    console.log('url');
    console.log(url);
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
    url: "<?= $this->Html->url('/ChatHistories/outputCSVOfChat') ?>",
    success: function(html){
      //modalOpenOverlap.call(window, html, 'p-history-del', 'チャット履歴の削除', 'moment');
      console.log('成功');

      //window.location.reload();
     /*var xhr = new XMLHttpRequest();
      xhr.open('GET', '/download', true);
      xhr.responseType = 'arraybuffer';
      xhr.onload = function(e) {
          var blob = new Blob([this.response]);
          var url = window.URL || window.webkitURL;
          var blobURL = url.createObjectURL(blob);

          var a = document.createElement('a');
          a.download = "hoge2.csv";
          a.href = blobURL;
          a.click();
      };

      xhr.send();*/

    }
  });
};


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
      console.log('顧客データ');
      console.log(customerData);
      document.getElementById("visitorsId").innerHTML= customerData.THistory.visitors_id;LandscapeData
      document.getElementById("ipAddress").innerHTML= customerData.THistory.ip_address;
      document.getElementById("Landscape").innerHTML= customerData.LandscapeData.org_name;
      $("#LandscapeData a").attr('onclick',"openCompanyDetailInfo("+customerData.LandscapeData.lbc_code+")");
      //$("#LandscapeData a").attr("onclick", new Function(openCompanyDetailInfo(customerData.LandscapeData.lbc_code)));
      console.log(document.getElementById('LandscapeData'));
      document.getElementById("visitCounts").innerHTML= customerData.THistoryCount.cnt;
      document.getElementById("landingPage").innerHTML= customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.title;
      $("#landing a").attr("href", customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.url);
      document.getElementById("chatSendingPage").innerHTML= customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.title;
      $("#chatSending a").attr("href", customerData.tHistoryChatSendingPageData.FirstSpeechSendPage.url);
      document.getElementById("separationPage").innerHTML= customerData.tHistoryChatLastPageData.LastSpeechSendPage.title;
      $("#separation a").attr("href", customerData.tHistoryChatLastPageData.LastSpeechSendPage.url);
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

console.log('table');
console.log(table);

// Adjust the width of thead cells when window resizes
$(window).resize(function() {
    // Get the tbody columns width array
    colWidth = bodyCells.map(function() {
        return $(this).width();
    }).get();
    console.log('resiezewidthサイズ');
    console.log(colWidth);

    // Set the width of thead columns
    table.find('thead tr').children().each(function(i, v) {
      console.log('v');
      console.log(v);
      console.log(i);
      $(v).width(colWidth[i]);
    });
}).resize(); // Trigger resize handler
//var splitterObj = null;
$(function(){

  //リサイズ処理
  $(window).resize(function() {;
  $("#history_list_side").css('height', window.innerHeight - 150);
  $("#chatContent").css('height', window.innerHeight - 235);
  $("#chatContent").css('max-height', '47em');
  $("#pastChatTalk").css('height', window.innerHeight - 410);
  $("#pastChatTalk").css('max-height', '32.3em');
  });

  //画面を縦に並べる場合
  /*$(document).on('click', '#vertical', function(){
      $("#check").remove();
      document.getElementById('history_list_side').style.display = "none";
      //var $historyList2 = $('#history_list2').clone();
      var historyListSide = $("#history_list_side").detach();
      //$("#history_list2").remove();
      $("#history_list_vertical").splitter({
        "orientation": "vertical",
        "limit": 110
      });
      historyListSide.appendTo('#chat_history_idx');
      document.getElementById('history_list_vertical').style.display = "";
      document.getElementById('history_body_vertical').style.top = "";
      document.getElementById('history_body_vertical').style.overflow = "hidden";
      document.getElementById('check').style.top = Number($('#check').css('top').slice(0,-2)) + 75 + 'px';
      document.getElementById('check').style.left = 0;
      document.getElementById('detail').style.left = 0;
      document.getElementById('detail').style.width = "100%";
      document.getElementById('detail').style.height = Number($('#detail').css('height').slice(0,-2)) - 85 + 'px';
      $("#history_list_vertical").css('height', window.innerHeight - 150);
  });*/

  $(document).on('click', '#vertical', function(){
    splitterObj.destroy();
    splitterObj = null;
    splitterObj = $("#history_list_side").height(800).split({
      "orientation": "horizontal",
      "limit": 50,
      "position": "40%"
    });
    splitterObj.refresh();
    document.getElementById('history_body_side').style.width = "100%";
    document.getElementById('chatTable').style.width = "100%";
    document.getElementById('detail').style.width = "100%";
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

  $(document).on('click', '#side', function(){
    splitterObj.destroy();
    splitterObj = null;
    splitterObj = $("#history_list_side").height(800).split({
      "orientation": "vertical",
      "limit": 50,
      "position": "35%"
    });
    splitterObj.refresh();
    document.getElementById('history_body_side').style.height = "100%";
    document.getElementById('detail').style.height = "100%";
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


  //画面を横に並べる場合
  /*$(document).on('click', '#side', function(){
    $("#check").remove();
    document.getElementById('history_list_vertical').style.display = "none";
    //var $historyList3 = $('#history_list3').clone();
    var historyListVertical = $("#history_list_vertical").detach();
      $("#history_list_side").splitter({
      "orientation": "horizontal",
      "limit": 625,
      "barwidth": 8,
    });
    historyListVertical.appendTo('#chat_history_idx');
    document.getElementById('history_list_side').style.display = "";
    document.getElementById('history_body_side').style.left = "";
    document.getElementById('detail').style.width = Number($('#detail').css('width').slice(0,-2)) - 10 + 'px';
    document.getElementById('check').style.height = Number($('#check').css('height').slice(0,-2)) -120   + 'px';
    document.getElementById('check').style.left = Number($('#check').css('left').slice(0,-2)) + 57   + 'px';
    document.getElementById('detail').style.width = Number($('#detail').css('width').slice(0,-2)) - 22 + 'px';
    document.getElementById('detail').style.right = "15px";
  });*/

  //初期設定
  /*$("#history_list_side").splitter({
    "orientation": "horizontal",
    "limit": 625,
    "barwidth": 8
  });
  document.getElementById('history_body_side').style.overflow = "hidden";
  document.getElementById('check').style.height = Number($('#check').css('height').slice(0,-2)) - 97 + 'px';
  document.getElementById('history_body_side').style.left = "";
  document.getElementById('detail').style.right = "15px";
  document.getElementById('check').style.left = Number($('#check').css('left').slice(0,-2)) + 134   + 'px';
  document.getElementById('history_body_side').style.width = Number($('#history_body_side').css('width').slice(0,-2)) + 82 + 'px';
  document.getElementById('detail').style.width = Number($('#detail').css('width').slice(0,-2)) - 62 + 'px';
  $("#history_list_side").css('height', window.innerHeight - 150);
  $("#chatContent").css('height', window.innerHeight - 235);
  $("#list_body").css('height', '100%');
});*/

    //横並びの場合
    if(<?= $screenFlg ?> == 1) {
      var splitterObj = $("#history_list_side").height(800).split({
        "orientation": "vertical",
        "limit": 500,
        "position": "35%"
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
