<script type = "text/javascript">
//パスワード自動生成
function saveEdit(){
  if ($('#MAgreementTrialFlg').prop('checked')) {
    var day = $('MAgreementAgreementStartDay').val();
    var d = new Date($('#MAgreementAgreementStartDay').val());
    var endDate = d.getFullYear() + '/' + (d.getMonth() + 1) + '/' + (d.getDate() + 14);
    $('#MAgreementAgreementEndDay').val(endDate);
  }
  else {
    var day = $('MAgreementAgreementStartDay').val();
    var d = new Date($('#MAgreementAgreementStartDay').val());
    var endDate = (d.getFullYear() + 1) + '/' + (d.getMonth() + 1) + '/' + (d.getDate() - 1);
    $('#MAgreementAgreementEndDay').val(endDate);
  }
  var password = document.getElementById('MAgreementAdminPassword').value;

  $.ajax({
    type: 'post',
    data: {
      password:password
    },
    cache: false,
    crossDomain: true,
    dataType: 'text',
    url: "<?= $this->Html->url('http://contact.sinclo/MUsers/remoteSaveForm') ?>",
    success: function(data){
      $('#MAgreementHashPassword').val(data);
      document.getElementById('MAgreementEditForm').submit();
    }
  });
}

//一覧画面削除機能
function remoteDeleteCompany(companyId,userId,companyKey){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', '契約設定');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id:document.getElementById('MAgreementId').value,
        companyId:companyId,
        userId:userId,
        companyKey:companyKey
      },
      cache: false,
      url: "<?= $this->Html->url('/MAgreements/remoteDeleteCompany') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/MAgreements/index') ?>";
      }
    });
  };
}

//パスワード自動生成
function createPassword(){
  var str = random();
  $('#MAgreementAdminPassword').val(str);
}

</script>