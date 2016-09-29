<script type="text/javascript">
function openAddDialog(){
  openEntryDialog({type: 1});
}
function openEditDialog(id){
  openEntryDialog({type: 2, id: id});
}
function openEntryDialog(setting){
  var type = setting.type;
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, type:2 => type, id
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/Campaigns/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-campaign-entry', 'キャンペーン情報', 'moment');
    }
  });
}
function openConfirmDialog(id){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'キャンペーン情報', 'moment');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
            cache: false,
      data: {
        id: id
      },
      url: "<?= $this->Html->url('/TDictionaries/remoteDeleteUser') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TDictionaries/index') ?>";
      }
    });
  };
}

function toggleSort(){
  if ( $(".sortable").is(".move") ) {
    $(".sortable").removeClass("move").sortable("disable");
    $("#sortMessage").text("");
    $("#sortToggleBtn").removeClass("redBtn").addClass("greenBtn");
    var list = getSort();
    $.ajax({
      type: "POST",
      url: "<?= $this->Html->url(['controller' => 'TDictionaries', 'action' => 'remoteSaveSort']) ?>",
      data: {
        list : list
      },
      dataType: "html",
      success: function(){
        location.href = location.href;
      }
    });
  }
  else {
    $(".sortable").addClass("move").sortable("enable");
    $("#sortMessage").text("　(！) 並び順修正中（保存する際は再びチェックボックスをクリックしてください）");
    $("#sortToggleBtn").removeClass("greenBtn").addClass("redBtn");
  }
}

var getSort = function(){
  var list = [];
  $(".sortable tr").each(function(e){
    list.push($(this).data('id'));
  });
  return JSON.parse(JSON.stringify(list));
};

$(document).ready(function(){

$(".sortable").sortable({
  axis: "y",
  tolerance: "pointer",
  containment: "parent",
  revert: 100
});
$(".sortable").sortable("disable");

});
</script>
