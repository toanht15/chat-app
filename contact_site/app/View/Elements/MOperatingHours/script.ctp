<script type="text/javascript">

function entryChange1(){
  radio = document.getElementsByName('data[MOperatingHour][type]');
  //営業時間設定の条件が「毎日」か「平日・週末」のどちらか確認
  if(radio[0].checked) {
    document.getElementById('secondTable').style.display = "none";
    document.getElementById('firstTable').style.display = "";
  }else if(radio[1].checked) {
    document.getElementById('firstTable').style.display = "none";
    document.getElementById('secondTable').style.display = "";
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
    $("#moperating_hours_list table").removeClass("detail_hidden");
    $("#detail_content input").prop("disabled", false);
  }
  else {
    // 営業時間設定を利用しない場合
    $("#detail_content dl").addClass("detail_hidden");
    $("#moperating_hours_list table").addClass("detail_hidden");
    $("#detail_content input").prop("disabled", true);
  }
}

$(document).ready(function(){
  // 営業時間設定のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MOperatingHour][active_flg]"]', activeSettingToggle);
  activeSettingToggle(); // 初回のみ
});
// 保存処理
function saveAct(){
  setting = {title: "営業時間設定エラー"};
  //ウィジェット設定が「営業時間のみ表示する」で、「利用しない」を選択して更新ボタンを押した場合
  if(<?= $widgetData ?> == 4 && $("#MOperatingHourActiveFlg1").prop("checked") == false) {
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/MOperatingHours/remoteOpenError') ?>",
      success: function(html){
        modalOpen.call(window, html, 'p-operatinghours-error', setting.title, 'moment');
      }
    });
  }
  else {
    document.getElementById('MOperatingHourIndexForm').submit();
  }
}

</script>