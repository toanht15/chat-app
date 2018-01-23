<script type="text/javascript">
var topPosition = 0;
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

  // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().attr('id');
    console.log(parentTdId);
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    console.log(targetObj);
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70 + topPosition) + 'px',
      left: $(this).offset().left - 65 + 'px'
    });
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });

  onIpFilterEnableSettingChange();

})
</script>