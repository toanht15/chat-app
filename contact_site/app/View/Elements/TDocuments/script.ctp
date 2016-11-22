<script type="text/javascript">

function removeAct(id){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
              id:id
            },
      cache: false,
      url: "<?= $this->Html->url('/TDocuments/remoteDelete') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TDocuments/index') ?>";
      }
    });
  };
}

function tagAdd(){
  var tag = $('#TDocumentEntryForm [name=new_tag]').val();
  $('#MDocumentTagAddForm [name=name]').val(tag);
  document.getElementById('MDocumentTagAddForm').submit();
}

function saveAct(){
 document.getElementById('TDocumentEntryForm').submit();
}

function removeActEdit(){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id:document.getElementById('TDocumentId').value
      },
      cache: false,
      url: "<?= $this->Html->url('/TDocuments/remoteDelete') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/TDocuments/index') ?>";
      }
    });
  };
}

$(function(){
  $('#labelHideList').multiSelect({
  });
});

</script>
