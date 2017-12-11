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

$(function(){
$("#list_body").splitter({
  "orientation": "vertical",
  "limit": 110,
  //"keepLeft": false
});

document.getElementById('check').style.marginLeft = '-35px';
document.getElementById('check').style.width = '101.6%';
document.getElementById('check').style.backgroundColor = '#7f7f7f';
document.getElementById('check').style.height = '6px';
console.log('高さ');
//$('#check').height($('#detail').height() + 40);
//console.log(document.getElementById('detail').style.height);
//$('#history_title').enhsplitter({minSize: 0, vertical: false});
});
</script>
