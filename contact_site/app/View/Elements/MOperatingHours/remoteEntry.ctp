<script type="text/javascript">
//チェックボックス休業日をチェックした際
function holidayCheck(){
  if (document.getElementById("MOperatingHourInfoHoliday").checked) {
    $(".form-control").prop("disabled", true);
  }
  else {
    $(".form-control").prop("disabled", false);
  }
}

//ポップアップを開いた際の休業日のチェックボックスの状態
var jsonData = '<?= $jsonData ?>';
jsonData = JSON.parse(jsonData);
radio = document.getElementsByName('data[MOperatingHour][type]');
//条件設定が毎日の場合
if(radio[0].checked) {
  if(jsonData.everyday.<?= $date ?>[0].start == "" && jsonData.everyday.<?= $date ?>[0].end == "") {
    document.getElementById("MOperatingHourInfoHoliday").checked = true;
    $(".form-control").prop("disabled", true);
  }
}
//条件設定が平日/休日の場合
else{
  if(jsonData.weekly.<?= $date ?>[0].start == "" && jsonData.weekly.<?= $date ?>[0].end == "") {
    document.getElementById("MOperatingHourInfoHoliday").checked = true;
    $(".form-control").prop("disabled", true);
  }
}

popupEvent.closePopup = function(){
  var timeInfo = "";
  var day = '<?= $date ?>';
  var check = [];
  for(var i=0; i<document.registrationInfo.elements.length;i++){
    // i番目のチェックボックスがチェックされているかを判定
    if(document.registrationInfo.elements[i].name === 'data[MOperatingHourInfo][day_of_week]' && document.registrationInfo.elements[i].checked) {
      check.push(document.registrationInfo.elements[i].value);
    }
  }

  radio = document.getElementsByName('data[MOperatingHour][type]');
  //条件設定が毎日の場合
  if(radio[0].checked) {
    jsonData.everyday.<?= $date ?> = [];
    for(i=0; i<check.length;i++) {
      jsonData.everyday[check[i]] = [];
    }
    //休業日のチェックボックスにチェックがついていない場合
    if(document.getElementById("MOperatingHourInfoHoliday").checked == false) {
      if(document.getElementById('form0').style.display != "none"){
        timeInfo = timeInfo + document.getElementsByName("startTime0")[0].value + '-' + document.getElementsByName("endTime0")[0].value;
        jsonData.everyday.<?= $date ?>.push({start: document.getElementsByName("startTime0")[0].value, end: document.getElementsByName("endTime0")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.everyday[check[i]].push({start: document.getElementsByName("startTime0")[0].value, end: document.getElementsByName("endTime0")[0].value});
        }
      }
      if(document.getElementById('form1').style.display != "none"){
        timeInfo = timeInfo + "　"　+ document.getElementsByName("startTime1")[0].value + '-' + document.getElementsByName("endTime1")[0].value;
        jsonData.everyday.<?= $date ?>.push({start: document.getElementsByName("startTime1")[0].value, end: document.getElementsByName("endTime1")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.everyday[check[i]].push({start: document.getElementsByName("startTime1")[0].value, end: document.getElementsByName("endTime1")[0].value});
        }
      }
      if(document.getElementById('form2').style.display != "none"){
        timeInfo = timeInfo + "　"　+ document.getElementsByName("startTime2")[0].value + '-' + document.getElementsByName("endTime2")[0].value;
        jsonData.everyday.<?= $date ?>.push({start: document.getElementsByName("startTime2")[0].value, end: document.getElementsByName("endTime2")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.everyday[check[i]].push({start: document.getElementsByName("startTime2")[0].value, end: document.getElementsByName("endTime2")[0].value});
        }
      }
      if(document.getElementById('form3').style.display != "none"){
        timeInfo = timeInfo + "　"　+ document.getElementsByName("startTime3")[0].value + '-' + document.getElementsByName("endTime3")[0].value;
        jsonData.everyday.<?= $date ?>.push({start: document.getElementsByName("startTime3")[0].value, end: document.getElementsByName("endTime3")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.everyday[check[i]].push({start: document.getElementsByName("startTime3")[0].value, end: document.getElementsByName("endTime3")[0].value});
        }
      }
      if(document.getElementById('form4').style.display != "none"){
        timeInfo = timeInfo + "　"　+ document.getElementsByName("startTime4")[0].value + '-' + document.getElementsByName("endTime4")[0].value;
        jsonData.everyday.<?= $date ?>.push({start: document.getElementsByName("startTime4")[0].value, end: document.getElementsByName("endTime4")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.everyday[check[i]].push({start: document.getElementsByName("startTime4")[0].value, end: document.getElementsByName("endTime4")[0].value});
        }
      }
    }
    //休業日のチェックボックスにチェックがついている場合
    else {
      timeInfo = "休み";
      jsonData.everyday.<?= $date ?>.push({start: "", end: ""});
      document.getElementById('<?= $date ?>' + 'day').style.color = "#d99694";
    }
  }
  //条件設定が平日/休日の場合
  else {
    jsonData.weekly.<?= $date ?> = [];
    for(i=0; i<check.length;i++) {
      jsonData.weekly[check[i]] = [];
    }
    //休業日のチェックボックスにチェックがついていない場合
    if(document.getElementById("MOperatingHourInfoHoliday").checked == false) {
      if(document.getElementById('form0').style.display != "none"){
        timeInfo = timeInfo + document.getElementsByName("startTime0")[0].value + '-' + document.getElementsByName("endTime0")[0].value;
        jsonData.weekly.<?= $date ?>.push({start: document.getElementsByName("startTime0")[0].value, end: document.getElementsByName("endTime0")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.weekly[check[i]].push({start: document.getElementsByName("startTime0")[0].value, end: document.getElementsByName("endTime0")[0].value});
        }
      }
      if(document.getElementById('form1').style.display != "none"){
        timeInfo = timeInfo + "　"　+ document.getElementsByName("startTime1")[0].value + '-' + document.getElementsByName("endTime1")[0].value;
        jsonData.weekly.<?= $date ?>.push({start: document.getElementsByName("startTime1")[0].value, end: document.getElementsByName("endTime1")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.weekly[check[i]].push({start: document.getElementsByName("startTime1")[0].value, end: document.getElementsByName("endTime1")[0].value});
        }
      }
      if(document.getElementById('form2').style.display != "none"){
        timeInfo = timeInfo + "　"　+ document.getElementsByName("startTime2")[0].value + '-' + document.getElementsByName("endTime2")[0].value;
        jsonData.weekly.<?= $date ?>.push({start: document.getElementsByName("startTime2")[0].value, end: document.getElementsByName("endTime2")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.weekly[check[i]].push({start: document.getElementsByName("startTime2")[0].value, end: document.getElementsByName("endTime2")[0].value});
        }
      }
      if(document.getElementById('form3').style.display != "none"){
        timeInfo = timeInfo + "　"　+ document.getElementsByName("startTime3")[0].value + '-' + document.getElementsByName("endTime3")[0].value;
        jsonData.weekly.<?= $date ?>.push({start: document.getElementsByName("startTime3")[0].value, end: document.getElementsByName("endTime3")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.weekly[check[i]].push({start: document.getElementsByName("startTime3")[0].value, end: document.getElementsByName("endTime3")[0].value});
        }
      }
      if(document.getElementById('form4').style.display != "none"){
        timeInfo = timeInfo + "　"　+ document.getElementsByName("startTime4")[0].value + '-' + document.getElementsByName("endTime4")[0].value;
        jsonData.weekly.<?= $date ?>.push({start: document.getElementsByName("startTime4")[0].value, end: document.getElementsByName("endTime4")[0].value});
        for(i=0; i<check.length;i++) {
          jsonData.weekly[check[i]].push({start: document.getElementsByName("startTime4")[0].value, end: document.getElementsByName("endTime4")[0].value});
        }
      }
    }
    //休業日のチェックボックスにチェックがついている場合
    else {
      timeInfo = "休み";
      jsonData.weekly.<?= $date ?>.push({start: "", end: ""});
      document.getElementById('<?= $date ?>' + 'day').style.color = "#d99694";
    }
  }

  jsonData = JSON.stringify(jsonData);
  //一覧画面のフォーム情報に変更した情報を記入
  document.getElementById("MOperatingHourOutputData][]").value = jsonData;
  //一覧画面に変更した情報を記入
  var td = document.getElementById(day);
  td.innerHTML = timeInfo;
  //チェックボックスでチェックを入れた曜日も同じように変更
  for(i=0; i<check.length;i++) {
    td = document.getElementById(check[i]);
    td.innerHTML = timeInfo;
  }
  popupEvent.close();
};

//時間追加
function addLine(number){
  document.getElementById('form' + (number +1)).style.display = "";
}

//時間削除
function deleteLine(number){
  document.getElementById('form' + number).style.display = "none";
}
</script>

<?php
if($type == 1) {
  $timeData = json_decode($jsonData)->everyday;
}
else {
  $timeData = json_decode($jsonData)->weekly;
}
?>

<?= $this->Form->create('MOperatingHourInfo', array('action' => 'index','name' => 'registrationInfo')); ?>
<div class="form01 setClockPicker">
  <?= $this->Form->input('id', array('type' => 'hidden')); ?>
  <div>
    <li>
      <label>対象曜日</label>
      <span id = "day"><?= $dayOfWeek; ?></span>
        <?= $this->Form->checkbox('holiday', array('onchange' => 'holidayCheck()','style' => 'margin-left:21px; margin-top:1px; cursor:pointer;')) ?><span id="tabsortText" style = "margin-top:1px;">休業日</span>
    </li>
  </div>
  <?php foreach($timeData->{$date} as $key => $v) { ?>
    <li id = <?= "form".$key ?>>
      <?php if($key === 0) { ?>
        <label style = "font-weight:bold;">営業時間</label>
      <?php }
      if($key === 0) { ?>
        <span class = "firstForm">
      <?php }
      else { ?>
        <span class = "form">
      <?php }
      if($key === 0 && empty($v->start) && empty($v->end)) {
        $v->start = "";
        $v->end = "";
      }  ?>

      <span class="input-group clockpicker bt0">
        <?php if($key === 0 && empty($v->start) && empty($v->end)) { ?>
          <input type="text" value = "" class="form-control" name=<?= "startTime".$key ?>>
        <?php } else { ?>
          <input type="text" value = <?= $v->start; ?>  class="form-control" name=<?= "startTime".$key ?> >
        <?php } ?>
      </span>
      <span class="bt0"><span>～</span></span>
      <span class="input-group clockpicker bt0">
        <?php if($key === 0 && empty($v->start) && empty($v->end)) { ?>
          <input type="text" value = "" class="form-control" name=<?= "endTime".$key ?>>
        <?php }
        else { ?>
          <input type="text" value = <?= $v->end; ?> class="form-control" name=<?= "endTime".$key ?>>
        <?php } ?>
          <a>
            <?= $this->Html->image('add.png', array(
              'alt' => '登録',
              'class' => 'btn-shadow disOffgreenBtn',
              'width' => 22,
              'height' => 22,
              'onclick' => 'addLine('.$key.')',
              'style' => 'padding:2px !important; display: block; margin-left: 245px; margin-top: -25px;'
            )) ?>
          </a>
        <?php if($key != 0) { ?>
            <a>
            <?= $this->Html->image('dustbox.png', array(
              'alt' => '削除',
              'class' => 'btn-shadow redBtn',
              'data-balloon-position' => '35',
              'width' => 22,
              'height' => 22,
              'onclick' => 'deleteLine('.$key.')',
              'style' => 'padding:2px !important; display: block; margin-left: 272px; margin-top: -22px;')) ?>
            </a>
        <?php } ?>
      </span>
    </li>
    <script type="text/javascript">
      $('.clockpicker').clockpicker({
        donetext:'設定',
        placement: 'orignal',
        align: 'original2'
      });
    </script>
  <?php }
  for ($i = $key+1; $i <= 4; $i++) { ?>
    <li id = <?= "form".$i ?> style = "display:none;">
      <span class = "form">
        <span class="input-group clockpicker bt0">
          <input type="text" value = "" class="form-control" name=<?= "startTime".$i ?>>
        </span>
        <span class="bt0"><span>～</span></span>
        <span class="input-group clockpicker bt0">
          <input type="text" value = "" class="form-control" name=<?= "endTime".$i ?>>
            <a>
              <?= $this->Html->image('add.png', array(
                'alt' => '登録',
                'class' => 'btn-shadow disOffgreenBtn',
                'width' => 22,
                'height' => 22,
                'onclick' => 'addLine('.$i.')',
                'style' => 'padding:2px !important; display: block; margin-left: 245px; margin-top: -25px;'
              )) ?>
            </a>
            <a>
              <?= $this->Html->image('dustbox.png', array(
                'alt' => '削除',
                'class' => 'btn-shadow redBtn',
                'data-balloon-position' => '35',
                'width' => 22,
                'height' => 22,
                'onclick' => 'deleteLine('.$i.')',
                'style' => 'padding:2px !important; display: block; margin-left: 272px; margin-top: -22px;'))
              ?>
            </a>
        </span>
      </span>
    </li>
  <?php } ?>
  <li>
    <span id = "top">他の曜日も同様に設定する</span>
  </li>
  <li id = "dayList">
    <?php foreach($days as $key => $v) {
      if($v == 'mon') {
        $v2 = '月';
      }
      if($v == 'tue') {
        $v2 = '火';
      }
      if($v == 'wed') {
        $v2 = '水';
      }
      if($v == 'thu') {
        $v2 = '木';
      }
      if($v == 'fri') {
        $v2 = '金';
      }
      if($v == 'sat') {
        $v2 = '土';
      }
      if($v == 'sun') {
        $v2 = '日';
      }
      if($v == 'pub') {
        $v2 = '祝';
      }
      if($v == 'week') {
        $v2 = '平日';
      }
      if($v == 'weekend') {
        $v2 = '週末';
      }
      if($v == 'pub2') {
        $v2 = '祝日';
      }
      if($v != 'week' && $v != 'weekend' && $v != 'pub2' && mb_substr($dayOfWeek, 0, 1) != $v2) { ?>
        <label class="pointer">
          <?= $this->ngForm->input('day_of_week', [
            'type' => 'checkbox',
            'legend' => false,
            'class' => 'dayOfWeek',
            'label' => $v2,
            'div' => false,
            'value' => $v,
            'error' => false
          ]) ?>
        </label>
      <?php }
        else if(($v == 'week' || $v == 'weekend' || $v == 'pub2') && $dayOfWeek != $v2) { ?>
        <label class="pointer">
          <?= $this->ngForm->input('day_of_week', [
            'type' => 'checkbox',
            'legend' => false,
            'class' => 'dayOfWeek',
            'label' => $v2,
            'div' => false,
            'value' => $v,
            'error' => false
          ]) ?>
        </label>
      <?php  }
          } ?>
  </li>
</div>
<?= $this->Form->end(); ?>
