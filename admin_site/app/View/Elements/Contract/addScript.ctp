<script type = "text/javascript">

var check = false;

function saveAct(){
  //document.getElementById('ContractAddForm').submit();
  if(check == false) {
    check = true;
    $.ajax({
      type: "POST",
      url: $('#ContractAddForm').attr('action'),
      data: $('#ContractAddForm').serialize()
    }).done(function(data){
      socket.emit('settingReload', JSON.stringify({type:1, forceReload: true, siteKey: "master"}));
      setTimeout(function(){
        location.href = "<?= $this->Html->url('/Contract/index') ?>"
      },1000);
    }).fail(function(data){
      var obj = JSON.parse(data.responseText);
      alert(obj.message);
      check = false;
    });
  }
}

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

$(function(){
  $('#ContractSameAsApplication').on('change', function(){
    if($(this).is(':checked')) {
      $('#MAgreementsAdministratorDepartment').val($('#MAgreementsApplicationDepartment').val());
      $('#MAgreementsAdministratorPosition').val($('#MAgreementsApplicationPosition').val());
      $('#MAgreementsAdministratorName').val($('#MAgreementsApplicationName').val());
      $('#MAgreementsAdministratorMailAddress').val($('#MAgreementsApplicationMailAddress').val());
    } else {
      $('#MAgreementsAdministratorDepartment').val("");
      $('#MAgreementsAdministratorPosition').val("");
      $('#MAgreementsAdministratorName').val("");
      $('#MAgreementsAdministratorMailAddress').val("");
    }
  });
  $('#MCompanyOptionsLaCoBrowse').on('change', function(){
    if($(this).is(':checked')) {
      $('#MCompanyLaLimitUsers').prop('disabled', false).css('background-color', "#FFF");
      $('#laLimitUsers').show();
    } else {
      $('#MCompanyLaLimitUsers').prop('disabled', true).css('background-color', "#999");
      $('#laLimitUsers').hide();
    }
  });
  if($('#MCompanyOptionsLaCoBrowse').is(':checked')) {
    $('#MCompanyLaLimitUsers').prop('disabled', false).css('background-color', "#FFF");
    $('#laLimitUsers').show();
  } else {
    $('#MCompanyLaLimitUsers').prop('disabled', true).css('background-color', "#999");
    $('#laLimitUsers').hide();
  }
});
</script>