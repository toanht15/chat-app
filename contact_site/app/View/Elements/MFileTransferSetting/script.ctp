<script type="text/javascript">
$(function(){
  function onFileTypeToggled(){
    if ( $("#MFileTransferSettingType1").prop("checked") ) { // 同時対応数上限を利用する場合
      $("#extension_setting_area").addClass("hidden");
    }
    else { // 同時対応数上限を利用しない場合
      $("#extension_setting_area").removeClass("hidden");
    }
  }

  function saveAct(){
    document.getElementById('MFileTransferSettingIndexForm').submit();
  }

  function reloadAct(){
    return location.href = location.href;
  }

  $("#reloadBtn").on("click", reloadAct);
  $("#updateBtn").on("click", saveAct);

  // 同時対応数上限のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MFileTransferSetting][type]"]', onFileTypeToggled);
  onFileTypeToggled(); // 初回のみ

})
</script>