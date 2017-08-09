<script type = "text/javascript">
function saveAct(){
  document.getElementById('ContractAddForm').submit();
}

//パスワード自動生成
function createPassword(){
  var str = random();
  $('#MAgreementAdminPassword').val(str);
}

//パスワード初期値自動生成
function　passwordLoad() {
  var str = random();
  $('#MAgreementAdminPassword').val(str);
}

window.onload = passwordLoad;

$(function(){
  $("#MAgreementMailAddress").prop("disabled", true);
});

//削除処理
function remoteDeleteCompany(id,companyId,userId,companyKey){
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'アカウント設定');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id:id,
        companyId:companyId,
        userId:userId,
        companyKey:companyKey
      },
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'MAgreements', 'action' => 'remoteDeleteCompany']) ?>",
      success: function(){
        location.href = "<?= $this->Html->url(['controller' => 'MAgreements', 'action' => 'index']) ?>";
      }
    });
  };
}
</script>