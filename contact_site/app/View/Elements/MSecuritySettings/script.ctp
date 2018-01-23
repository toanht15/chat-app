<script type="text/javascript">
$(function(){
  function onIpFilterEnableSettingChange(){
    if ( $("#MSecuritySettingsIpFilterEnabled0").prop("checked") ) { // 同時対応数上限を利用する場合
      $("#ip_white_filter_settings_area").addClass("hidden");
      $("#ip_black_filter_settings_area").addClass("hidden");
    }
    else if($("#MSecuritySettingsIpFilterEnabled1").prop("checked")) { // 同時対応数上限を利用しない場合
      $("#ip_white_filter_settings_area").removeClass("hidden");
      $("#ip_black_filter_settings_area").addClass("hidden");
    }
    else if($("#MSecuritySettingsIpFilterEnabled2").prop("checked")) { // 同時対応数上限を利用しない場合
      $("#ip_white_filter_settings_area").addClass("hidden");
      $("#ip_black_filter_settings_area").removeClass("hidden");
    }
  }

  function saveAct(){
    document.getElementById('MSecuritySettingsEditForm').submit();
  }

  function reloadAct(){
    return location.href = location.href;
  }

  $("#reloadBtn").on("click", reloadAct);
  $("#updateBtn").on("click", saveAct);

  // ログイン時IP制御設定のラジオボタン変更時のイベントハンドラ
  $(document).on('change', '[name="data[MSecuritySettings][ip_filter_enabled]"]', onIpFilterEnableSettingChange);
  onIpFilterEnableSettingChange();

})
</script>