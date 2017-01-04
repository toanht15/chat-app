<script type="text/javascript">
'use strict';
function openConfirm(){
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
    url: "<?= $this->Html->url('/MAdministrators/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-muser-entry', 'アカウント設定');
    }
  });
}

//一覧画面削除機能
function remoteDeleteUser(id){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'アカウント設定');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id:id
      },
      cache: false,
      url: "<?= $this->Html->url('/MAdministrators/remoteDeleteUser') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/MAdministrators/index') ?>";
      }
    });
  };
}

  function SaveToFile(FileName, Stream) {
  if (window.navigator.msSaveBlob) {
    window.navigator.msSaveBlob(new Blob([Stream], { type: "text/plain" }), FileName);
  } else {
    console.log('huhuhuhu');
    var a = document.createElement("a");
    a.href = URL.createObjectURL(new Blob([Stream], { type: "text/plain" }));
    //a.target   = '_blank';
    a.download = FileName;
    document.body.appendChild(a) //  Firefox specification
    a.click();
    document.body.removeChild(a) //  Firefox specification
  }
}

function run() {
  var Stream = 'HElloWorld!';
  SaveToFile('test.txt', Stream);
}

</script>

