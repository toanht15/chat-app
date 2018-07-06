<script type="text/javascript">
var changeFlg = false;
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
  //ページ遷移の際にアラート出す
  changeFlg = true;
  if(changeFlg == true) {
    window.addEventListener('beforeunload', onBeforeunloadHandler, false);
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
  //ページ遷移の際にアラート出す
  changeFlg = true;
  if(changeFlg == true) {
    window.addEventListener('beforeunload', onBeforeunloadHandler, false);
  }
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
  //ページ遷移の際にアラート出す
  changeFlg = true;
  if(changeFlg == true) {
    window.addEventListener('beforeunload', onBeforeunloadHandler, false);
  }
}

var onBeforeunloadHandler = function(e) {
  e.returnValue = 'まだ保存されておりません。離脱してもよろしいでしょうか';
};

$(document).ready(function(){
  // 営業時間設定のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MOperatingHour][active_flg]"]', activeSettingToggle);
  $(document).on('change', '[name="data[MOperatingHour][type]"]', entryChange1);
  if(<?= $widgetData ?> == 4　|| '<?= $check ?>' == 'included') {
    $("#MOperatingHourActiveFlg2").prop("disabled", true);
  }
});
// 保存処理
function saveAct(){
  changeFlg = false;
  if(changeFlg == false) {
    window.removeEventListener('beforeunload', onBeforeunloadHandler, false);
  }
  //loading画像
  loading.load.start();
  document.getElementById('MOperatingHourIndexForm').submit();
}

// 元に戻す処理
function reloadAct(){
  changeFlg = false;
  if(changeFlg == false) {
    window.removeEventListener('beforeunload', onBeforeunloadHandler, false);
  }
  window.location.reload();
}

</script>