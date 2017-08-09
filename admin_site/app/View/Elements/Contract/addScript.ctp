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
  var inputDisabled = function(jqObj) {
    jqObj.prop("readonly", true).addClass("disabled").prev('div').find('span').first().removeClass('require');
  }

  var inputEnabled = function(jqObj) {
    jqObj.prop("readonly", false).removeClass("disabled").prev('div').find('span').first().addClass('require')
  }

  $('#MCompanyTrialFlg').on('change', function(event){
    var checked = $(this).prop("checked");
    if(checked) {
      inputEnabled($('#MAgreementsTrialStartDay'));
      inputEnabled($('#MAgreementsTrialEndDay'));
      inputDisabled($('#MAgreementsAgreementStartDay'));
      inputDisabled($('#MAgreementsAgreementEndDay'));
    } else {
      inputDisabled($('#MAgreementsTrialStartDay'));
      inputDisabled($('#MAgreementsTrialEndDay'));
      inputEnabled($('#MAgreementsAgreementStartDay'));
      inputEnabled($('#MAgreementsAgreementEndDay'));
    }
  });
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