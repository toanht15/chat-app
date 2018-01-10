<script type="text/javascript">
$(function(){
  function onIpFilterEnableSettingChange(){
    if ( $("#MSecuritySettingsIpFilterEnabled0").prop("checked") ) { // 同時対応数上限を利用する場合
      $("#ip_filter_settings_area").addClass("hidden");
    }
    else { // 同時対応数上限を利用しない場合
      $("#ip_filter_settings_area").removeClass("hidden");
    }
  }

  function onIpFilterSettingChanged() {
    var whitelistTextarea = $('[name="data[MSecuritySettings][ip_filter_whitelist]"]'),
        blacklistTextarea = $('[name="data[MSecuritySettings][ip_filter_blacklist]"]'),
        whitelistIsSetting = whitelistTextarea.val() !== "",
        blacklistIsSetting = blacklistTextarea.val() !== "";
    if(whitelistIsSetting && blacklistIsSetting) {
      // 異常系
      textareaEnabled(whitelistTextarea);
      textareaEnabled(blacklistTextarea);
    } else if(whitelistIsSetting) {
      textareaEnabled(whitelistTextarea);
      textareaDisabled(blacklistTextarea);
    } else if(blacklistIsSetting) {
      textareaDisabled(whitelistTextarea);
      textareaEnabled(blacklistTextarea);
    } else {
      textareaEnabled(whitelistTextarea);
      textareaEnabled(blacklistTextarea);
    }
  }

  function textareaDisabled(textareaObj) {
    textareaObj.prop('disabled', true).addClass('disabled');
  }

  function textareaEnabled(textareaObj) {
    textareaObj.prop('disabled', false).removeClass('disabled');
  }

  function saveAct(){
    document.getElementById('MSecuritySettingsEditForm').submit();
  }

  function reloadAct(){
    return location.href = location.href;
  }

  $("#reloadBtn").on("click", reloadAct);
  $("#updateBtn").on("click", saveAct);

  // 同時対応数上限のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MSecuritySettings][ip_filter_enabled]"]', onIpFilterEnableSettingChange);
  $(document).on('change', '.ip-filter-list-area', onIpFilterSettingChanged);
  onIpFilterEnableSettingChange(); // 初回のみ
  onIpFilterSettingChanged(); // 初回のみ

})
</script>