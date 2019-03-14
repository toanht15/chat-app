<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/19
 * Time: 10:37
 */

/**
 *
 */
?>
<script type="text/javascript">

$(document).ready(function(){

  /* Define all Check Element */
  var allCheckElm = $('#allCheck'),
      columnCheckElm = $('#TChatbotDiagram_list input[name="selectTab"]'),
      copyBtn = $('#TChatbotDiagram_copy_btn'),
      deleteBtn = $('#TChatbotDiagram_dustbox_btn'),
      addBtn = $('#TChatbotDiagram_add_btn'),
      sortBtn = $(".sortable");

  /* Add sortable event */
  sortBtn.sortable({
    axis: "y",
    tolerance: "pointer",
    containment: "parent",
    cursor: "move",
    cancel: ".sortable .cancel",
    revert: 100,
    helper: function(event, ui) {
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    }
  });
  sortBtn.sortable("disable");

  //新規追加・編集イベントの登録
  addBtn.on('click', function(){
    openDiagramPage();
  });

  $('.diagram_column').on('click', function(){
    openDiagramPage($(this).data('id'));
  });

  function openDiagramPage(id) {
    if(!document.getElementById("sort").checked) {
      location.href = createUrl(id);
    }
    else {
      return false;
    }
  }

  function createUrl(id) {
    var index = Number("<?= $this->Paginator->params()["page"] ?>");
    var url = "<?= $this->Html->url('/TChatbotDiagrams/add') ?>";
    if(!!id) {
      url = url + "/" + id
    }
    return url + "?lastpage=" + index;
  }

  //選択
  allCheckElm.on('click', function(){
    columnCheckElm.prop('checked', this.checked);
    actBtnShow();
  });

  columnCheckElm.on('click', function(){
    isCheckedAll();
    actBtnShow();
  });

  function isCheckedAll(){
    var allChecked = true;
    columnCheckElm.each(function(){
      if(!this.checked) {
        allChecked = false;
        return false;
      }
    });
    allCheckElm.prop('checked', allChecked);
  }

  function actBtnShow(){
    columnCheckElm.each(function(){
      if(this.checked) {
        turnOnBtn();
        return false;
      }
      turnOffBtn();
    });
  }

  function turnOnBtn(){
    copyBtn.removeClass("disOffgrayBtn");
    copyBtn.addClass("disOffgreenBtn");
    deleteBtn.removeClass("disOffgrayBtn");
    deleteBtn.addClass("disOffredBtn");
  }

  function turnOffBtn(){
    copyBtn.removeClass("disOffgreenBtn");
    copyBtn.addClass("disOffgrayBtn");
    deleteBtn.removeClass("disOffredBtn");
    deleteBtn.addClass("disOffgrayBtn");
  }
  //コピー
  copyBtn.on('click', function(){
    if(copyBtn.hasClass("disOffgrayBtn")) return;
    var selectedList = getSelectedList();
    modalOpen.call(window, "コピーします、よろしいですか？", 'p-confirm', 'チャットツリー設定', 'moment');
    popupEvent.closePopup = function(){
      $.ajax({
        type: 'post',
        cache: false,
        data: {
          selectedList: selectedList
        },
        url: "<?= $this->Html->url('/TChatbotDiagrams/remoteCopyEntryForm') ?>",
        success: function(){
          //現在のページ番号
          var index = Number("<?= $this->Paginator->params()["page"] ?>");
          var url = "<?= $this->Html->url('/TChatbotDiagrams/index') ?>";
          location.href = url + "/page:" + index;
        },
        error: function() {
          console.log('error');
          location.href = "<?= $this->Html->url('/TChatbotDiagrams/index') ?>";
        }
      });
    };
  });

  //削除
  deleteBtn.on('click', function(){
    if(deleteBtn.hasClass("disOffgrayBtn")) return;
    var selectedList = getSelectedList();
    modalOpen.call(window, "削除します、よろしいですか？<br><br>（呼び出し元が設定されているチャットツリーは削除できません）", 'p-confirm', 'チャットツリー設定', 'moment');
    popupEvent.closePopup = function(){
      $.ajax({
        type: 'post',
        cache: false,
        data: {
          selectedList: selectedList,
        },
        url: "<?= $this->Html->url('/TChatbotDiagrams/chkRemoteDelete') ?>",
        success: function(data){
          // ページ再読み込み
          var url = "<?= $this->Html->url('/TChatbotDiagrams/index') ?>";
          location.href = url + "/page:" + index;
        },
        error: function() {
          console.log('error');
          var url = "<?= $this->Html->url('/TChatbotDiagrams/index') ?>";
          location.href = url + "/page:" + 1;
        }
      });
    };
  });


  var getSelectedList = function(){
    var list = $('#TChatbotDiagram_list input[name^="selectTab"]:checked');
    var selectedList = [];
    list.each(function(idx){
      selectedList.push(Number(list[idx].value));
    });
    return selectedList;
  };

  //並び替え
  $("#sort").on('click', function(){
    if(!this.checked) {
      confirmSort();
    } else {
      columnCheckElm.prop('checked', false);
      actBtnShow();
      isCheckedAll();
      //ソータブル付与
      sortBtn.addClass("move").sortable("enable");
      //並び替え中の文言表示
      $("#sortText").css("display","none");
      $("#sortTextMessage").css("display","");
      //チェックボックスそれぞれにdisabledを付与
      allCheckElm.prop("disabled", true);
      columnCheckElm.prop("disabled", true);
      addBtn.removeClass("disOffgreenBtn");
      addBtn.addClass("disOffgrayBtn");
      $("table tbody.sortable tr td").css('cursor', 'move');
      $("table tbody.sortable tr td a").css('cursor', 'move');
    }
  });

  //ソート順を保存
  var confirmSort = function(){
    modalOpen.call(window, "編集内容を保存します。<br/><br/>よろしいですか？<br/>", 'p-sort-save-confirm', 'チャットツリー設定並び替えの保存', 'moment');
    popupEvent.saveClicked = function(){
      saveToggleSort();
    };
    popupEvent.cancelClicked = function(){
      location.href = "<?= $this->Html->url('/TChatbotDiagrams/index') ?>";
    };
    $(".p-sort-save-confirm #popupCloseBtn").click(function(){
      $("#sort").prop('checked', true);
    });
  };

  var saveToggleSort = function(){
    var idList = getArrayForSort("id");
    var sortNoList = getArrayForSort("sort");
    $.ajax({
      type: "POST",
      url: "<?= $this->Html->url(['controller' => 'TChatbotScenario', 'action' => 'remoteSaveSort']) ?>",
      data: {
        list: idList,
        sortNoList: sortNoList
      },
      dataType: "html",
      success: function(){
        var index = Number("<?= $this->Paginator->params()["page"] ?>");
        var url = "<?= $this->Html->url('/TChatbotDiagrams/index') ?>";
        location.href = url + "/page:" + index;
      }
    });
  };

  //並び順を取得
  var getArrayForSort = function(param){
    var list = [];
    var diagramList = $("#TChatbotDiagram_list .sortable tr");
    for(var i = 0; i < diagramList.length; i++) {
      list.push($(diagramList[i]).data(param));
    }
    return list;
  }
});
</script>
