<script type = "text/javascript">
//パスワード自動生成
function saveAct(){
  var key = $('#MAgreementCompanyKey').val();
  $('#MAgreementMailAddress').val(key+"@ml.jp");
  $("#MAgreementMailAddress").prop("disabled", false);
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
      document.getElementById('MAgreementAddForm').submit();
    }
  });
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
      url: "<?= $this->Html->url('/MAgreements/remoteDeleteCompany') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/MAgreements/index') ?>";
      }
    });
  };
}
</script>