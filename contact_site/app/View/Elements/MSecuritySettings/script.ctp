<script type="text/javascript">
var topPosition = 0;
var beforeSelected = 0;
$(function(){
  function onIpFilterEnableSettingChange(){
    if ( $("#MSecuritySettingsIpFilterEnabled0").prop("checked") ) { // 同時対応数上限を利用する場合
      $("#ip_white_filter_settings_area").slideUp("fast");
      $("#ip_black_filter_settings_area").slideUp("fast");
      beforeSelected = 0;
    }
    else if($("#MSecuritySettingsIpFilterEnabled1").prop("checked")) { // 同時対応数上限を利用しない場合
      if(beforeSelected === 0) {
        $("#ip_white_filter_settings_area").slideDown("fast");
        $("#ip_black_filter_settings_area").hide();
      } else {
        $("#ip_black_filter_settings_area").hide();
        $("#ip_white_filter_settings_area").fadeIn();
      }
      beforeSelected = 1;
    }
    else if($("#MSecuritySettingsIpFilterEnabled2").prop("checked")) { // 同時対応数上限を利用しない場合
      if(beforeSelected === 0) {
        $("#ip_black_filter_settings_area").slideDown("fast");
        $("#ip_white_filter_settings_area").hide();
      } else {
        $("#ip_white_filter_settings_area").hide();
        $("#ip_black_filter_settings_area").fadeIn();
      }
      beforeSelected = 2;
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