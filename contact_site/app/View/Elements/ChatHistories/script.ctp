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

function changeSizeOfTbl(){
  // リアルタイムモニタの高さを指定
  //$("#list_body").height($(window).height() - $("#history_list").offset().top - 60);
  //$("#list_body").height($(window).height() - $("#history_list").offset().top - 60);
  $('#history_list').height($('#history_list').height() - 30);
  //$('#list_body').height($('#list_body').height() + 30);
}

$(document).ready(function(){
  changeSizeOfTbl();
});

$(window).resize(function(){
  changeSizeOfTbl();
});

$("#detail").resize(function(){
  console.log('まっさっか');
});

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
    console.log('ここまで入ってることを確認');
    document.getElementById("history_dustbox_btn").addEventListener('click', openDeleteDialog, false);
    console.log('終了');
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


function openCopyDialog() {
  console.log('eeeeeeeeeeee');
}
//履歴削除モーダル画面
function openDeleteDialog(){
  console.log('ここまで入ってることを確認aaaaaaaaaaaaa');
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

function aaa() {
  console.log('わっしょい');
  //document.getElementById('history_list2').style.display = "none";
};

function bbb() {
  console.log('ちぇえええええっく');
  //document.getElementById('history_list2').style.display = "";
  //document.getElementById('history_menu').style.display = "none";
  //document.getElementById('history_list').style.display = "none";
  /*document.getElementById('historyBody').style.left = "";
  document.getElementById('check').style.left = Number($('#check').css('left').slice(0,-2)) + 94   + 'px';
  document.getElementById('historyBody').style.width = Number($('#historyBody').css('width').slice(0,-2)) + 62 + 'px';
  document.getElementById('detail').style.width = Number($('#detail').css('width').slice(0,-2)) - 62 + 'px';*/
};

var splitterObj = null;
$(function(){

/*$("#history_list2").splitter({
  "orientation": "horizontal",
  "limit": 605,
  "barwidth": 8,
});


document.getElementById('historyBody').style.left = "";
document.getElementById('check').style.left = Number($('#check').css('left').slice(0,-2)) + 94   + 'px';
document.getElementById('historyBody').style.width = Number($('#historyBody').css('width').slice(0,-2)) + 62 + 'px';
document.getElementById('detail').style.width = Number($('#detail').css('width').slice(0,-2)) - 62 + 'px';*/

$('#ccc').click(function(){
    console.log('ああああああ');
    //$("#history_list2").unbind( "splitter");
    splitterObj.release();
     //$("#history_list2").remove();

document.getElementById('history_list2').style.display = "none";
document.getElementById('history_menu').style.display = "";
document.getElementById('history_list').style.display = "";
splitterObj = $("#list_body").splitter({
  "orientation": "vertical",
  "limit": 110
});

});

$('#eeee').click(function(){
    console.log('うううううううううううううう');
    splitterObj.release();

  /*$("#history_list2").splitter({
    "orientation": "horizontal",
    "limit": 605,
    "barwidth": 8,
  });*/

});

console.log('再度はないよね？');
/*$("#list_body").splitter({
  "orientation": "vertical",
  "limit": 110,
  "keepLeft": false
});

document.getElementById('check').style.marginLeft = '-35px';
document.getElementById('check').style.width = '101.6%';
document.getElementById('check').style.backgroundColor = '#7f7f7f';
document.getElementById('check').style.height = '6px';*/
splitterObj = $("#history_list2").splitter({
  "orientation": "horizontal",
  "limit": 605,
  "barwidth": 8,
});


document.getElementById('historyBody').style.left = "";
document.getElementById('check').style.left = Number($('#check').css('left').slice(0,-2)) + 94   + 'px';
document.getElementById('historyBody').style.width = Number($('#historyBody').css('width').slice(0,-2)) + 62 + 'px';
document.getElementById('detail2').style.width = Number($('#detail2').css('width').slice(0,-2)) - 62 + 'px';
});
</script>
