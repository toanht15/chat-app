<script type="text/javascript">
//モーダル画面
function openSearchRefine(){
  $.ajax({
    type: 'post',
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'remoteSearchCustomerInfo']) ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-thistory-entry', '高度な検索', 'moment');
    }
  });
}

//セッションクリア(条件クリア)
function sessionClear(){
  location.href = "<?=$this->Html->url(array('controller' => 'Histories', 'action' => 'portionClearSession'))?>";
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
    //一つでもチェックが入ったら
    //コピーボタン有効
    document.getElementById("history_csv_btn").className="btn-shadow disOffgreenBtn";
    document.getElementById("history_csv_btn").addEventListener('click', openCopyDialog, false);
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
  $.ajax({
    type: 'post',
    dataType: 'html',
    data: {
      //id:id,
      //historyId:historyId,
      //message:message,
      //created:created
    },
    cache: false,
    url: "<?= $this->Html->url('/ChatHistories/openEntryDelete') ?>",
    success: function(html){
      modalOpenOverlap.call(window, html, 'p-history-del', 'チャット履歴の削除', 'moment');
    }
  });
};


//var splitterObj = null;
$(function(){

  //リサイズ処理
  $(window).resize(function() {
  $("#history_list_vertical").css('height', window.innerHeight - 150);
  $("#history_list_side").css('height', window.innerHeight - 150);
  $("#chatContent").css('height', window.innerHeight - 235);
  });

  //画面を縦に並べる場合
  $(document).on('click', '#vertical', function(){
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
      document.getElementById('check').style.top = Number($('#check').css('top').slice(0,-2)) + 80 + 'px';
      document.getElementById('check').style.left = 0;
      document.getElementById('detail').style.left = 0;
      document.getElementById('detail').style.width = "100%";
      document.getElementById('detail').style.height = Number($('#detail').css('height').slice(0,-2)) - 115 + 'px';
      $("#history_list_vertical").css('height', window.innerHeight - 150);
  });

  //画面を横に並べる場合
  $(document).on('click', '#side', function(){
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
    document.getElementById('detail').style.width = Number($('#detail').css('width').slice(0,-2)) - 10 + 'px';
    document.getElementById('check').style.height = Number($('#check').css('height').slice(0,-2)) -120   + 'px';
    document.getElementById('check').style.left = Number($('#check').css('left').slice(0,-2)) + 22   + 'px';
    document.getElementById('detail').style.width = Number($('#detail').css('width').slice(0,-2)) - 22 + 'px';
    document.getElementById('detail').style.right = "15px";
  });


  //初期設定
  $("#history_list_side").splitter({
    "orientation": "horizontal",
    "limit": 625,
    "barwidth": 8
  });
  document.getElementById('history_body_side').style.overflow = "hidden";
  document.getElementById('check').style.height = Number($('#check').css('height').slice(0,-2)) - 97 + 'px';
  document.getElementById('history_body_side').style.left = "";
  document.getElementById('detail').style.right = "15px";
  document.getElementById('check').style.left = Number($('#check').css('left').slice(0,-2)) + 94   + 'px';
  document.getElementById('history_body_side').style.width = Number($('#history_body_side').css('width').slice(0,-2)) + 82 + 'px';
  document.getElementById('detail').style.width = Number($('#detail').css('width').slice(0,-2)) - 62 + 'px';
  $("#history_list_side").css('height', window.innerHeight - 150);
  $("#chatContent").css('height', window.innerHeight - 235);
  $("#list_body").css('height', '100%');
});
</script>
