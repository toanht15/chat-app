<script type="text/javascript">
function entryChange1(){
  radio = document.getElementsByName('data[MOperatingHour][type]');
  //営業時間設定の条件が「毎日」か「平日・週末」のどちらか確認
  if(radio[0].checked) {
    document.getElementById('secondTable').style.display = "none";
    document.getElementById('firstTable').style.display = "";
    document.getElementById('moperating_hours_table').style.height = "49em";
  }else if(radio[1].checked) {
    document.getElementById('firstTable').style.display = "none";
    document.getElementById('secondTable').style.display = "";
    document.getElementById('moperating_hours_table').style.height = "24em";
  }
}


//営業時間モーダル
function openAddDialog(dayOfWeek,timeData){
  jsonData = document.getElementsByName('data[MOperatingHour][outputData]')[0].value;
  radio = document.getElementsByName('data[MOperatingHour][type]');
  //営業時間設定の条件が「毎日」か「平日・週末」のどちらか確認
  if(radio[0].checked) {
    var dayType = 1;
  }
  else {
    var dayType = 2;
  }
  openEntryDialog({day: dayOfWeek, timeData:timeData,title:"営業時間登録",jsonData:jsonData,dayType:dayType});
}

function openEntryDialog(setting){
  $.ajax({
    type: 'post',
    data: setting,
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/MOperatingHours/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-operatinghours-entry', setting.title, 'moment');
    }
  });
}

function activeSettingToggle(){
  if ( $("#MOperatingHourActiveFlg1").prop("checked") ) {
    // 営業時間設定を利用する場合
    $("#detail_content dl").removeClass("detail_hidden");
    $("#detail_content input").prop("disabled", false);
  }
  else {
    // 営業時間設定を利用しない場合
    $("#detail_content dl").addClass("detail_hidden");
    $("#detail_content input").prop("disabled", true);
  }
}

$(document).ready(function(){
  // 営業時間設定のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MOperatingHour][active_flg]"]', activeSettingToggle);
  activeSettingToggle(); // 初回のみ
  if(<?= $widgetData ?> == 4　|| '<?= $check ?>' == 'included') {
    $("#MOperatingHourActiveFlg2").prop("disabled", true);
  }
});
// 保存処理
function saveAct(){
  document.getElementById('MOperatingHourIndexForm').submit();
}

</script>