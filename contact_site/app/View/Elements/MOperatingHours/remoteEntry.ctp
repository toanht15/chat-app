<script type="text/javascript">
//チェックボックス休業日をチェックした際
function holidayCheck(){
  if (document.getElementById("MOperatingHourInfoHoliday").checked) {
    $(".form-control").prop("disabled", true);
    $('.disOffgreenBtn').css('pointer-events','none'); //追加ボタン制御
    $('.deleteBtn').css('pointer-events','none'); //削除ボタン制御
  }
  else {
    $(".form-control").prop("disabled", false);
    $('.disOffgreenBtn').css('pointer-events','auto'); //追加ボタン制御解除
    $('.deleteBtn').css('pointer-events','auto'); //削除ボタン制御解除
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
    $('.disOffgreenBtn').css('pointer-events','none'); //追加ボタン制御
    $('.deleteBtn').css('pointer-events','none'); //削除ボタン制御
  }
}
//条件設定が平日/休日の場合
else{
  if(jsonData.weekly.<?= $date ?>[0].start == "" && jsonData.weekly.<?= $date ?>[0].end == "") {
    document.getElementById("MOperatingHourInfoHoliday").checked = true;
    $(".form-control").prop("disabled", true);
  }
}
var error = 0;

popupEvent.closePopup = function(){
  //formの数
  var length = $('.timeData').length;

  //空チェック
  if (document.getElementById("MOperatingHourInfoHoliday").checked == false) {
    for (i = 0; i < length; i++) {
      if((document.getElementsByName("startTime" + i)[0].value == "" || document.getElementsByName("endTime" + i)[0].value == "")) {
        document.getElementById('error').style.display = "block";
        $('#error').text("条件を設定してください");
        error = error + 1;
        if(error == 1) {
          document.getElementById('popup-frame').style.height = (parseInt($('#popup-frame').css('height'),10) + 15) + 'px';
        }
        return;
      }
    }
  }

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
      for(i=0; i<5; i++) {
        if(document.getElementById('form' + i) != null){
          if(i == 0) {
            timeInfo = timeInfo + document.getElementsByName("startTime" + i)[0].value + '-' + document.getElementsByName("endTime" + i)[0].value;
          }
          else {
            timeInfo = timeInfo + "　/　"　+ document.getElementsByName("startTime" + i)[0].value + '-' + document.getElementsByName("endTime" + i)[0].value;
          }
          jsonData.everyday.<?= $date ?>.push({start: document.getElementsByName("startTime" + i)[0].value, end: document.getElementsByName("endTime" + i)[0].value});
          for(i2=0; i2<check.length;i2++) {
            jsonData.everyday[check[i2]].push({start: document.getElementsByName("startTime" + i)[0].value, end: document.getElementsByName("endTime" + i)[0].value});
            document.getElementById(check[i2] + 'day').style.color = "#595959";
          }
        }
      }
      document.getElementById('<?= $date ?>' + 'day').style.color = "#595959";
    }
    //休業日のチェックボックスにチェックがついている場合
    else {
      timeInfo = "休み";
      jsonData.everyday.<?= $date ?>.push({start: "", end: ""});
      document.getElementById('<?= $date ?>' + 'day').style.color = "#d99694";
      for(i=0; i<check.length;i++) {
        jsonData.everyday[check[i]].push({start: "", end: ""});
        document.getElementById(check[i] + 'day').style.color = "#d99694";
      }
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
      for(i=0; i<5; i++) {
        if(document.getElementById('form' + i) != null){
          if(i == 0) {
            timeInfo = timeInfo + document.getElementsByName("startTime" + i)[0].value + '-' + document.getElementsByName("endTime" + i)[0].value;
          }
          else {
            timeInfo = timeInfo + "　/　"　+ document.getElementsByName("startTime" + i)[0].value + '-' + document.getElementsByName("endTime" + i)[0].value;
          }
          jsonData.weekly.<?= $date ?>.push({start: document.getElementsByName("startTime" + i)[0].value, end: document.getElementsByName("endTime" + i)[0].value});
          for(i2=0; i2<check.length;i2++) {
            jsonData.weekly[check[i2]].push({start: document.getElementsByName("startTime" + i)[0].value, end: document.getElementsByName("endTime" + i)[0].value});
            document.getElementById(check[i2] + 'day').style.color = "#595959";
          }
        }
      }
      document.getElementById('<?= $date ?>' + 'day').style.color = "#595959";
    }
    //休業日のチェックボックスにチェックがついている場合
    else {
      timeInfo = "休み";
      jsonData.weekly.<?= $date ?>.push({start: "", end: ""});
      document.getElementById('<?= $date ?>' + 'day').style.color = "#d99694";
      for(i=0; i<check.length;i++) {
        jsonData.weekly[check[i]].push({start: "", end: ""});
        document.getElementById(check[i] + 'day').style.color = "#d99694";
      }
    }
  }

  jsonData = JSON.stringify(jsonData);
  //一覧画面のフォーム情報に変更した情報を記入
  document.getElementById("MOperatingHourOutputData").value = jsonData;
  //一覧画面に変更した情報を記入
  var td = document.getElementById(day);
  td.innerHTML = timeInfo;
  //チェックボックスでチェックを入れた曜日も同じように変更
  for(i=0; i<check.length;i++) {
    console.log(check[i]);
    td = document.getElementById(check[i]);
    td.innerHTML = timeInfo;
  }
  popupEvent.close();
};

//時間追加
function addLine(event){
  document.getElementById('popup-frame').style.height = (parseInt($('#popup-frame').css('height'),10) + 41) + 'px';
  number = Number(event.target.id.substr(event.target.id.indexOf("_")+1));
  var marginLeft;
  var display;
  if(number < 3) {
    document.getElementById('add_' + number).style.display = "none";
    marginLeft = "272px";
    display = "block";
    if(number != 0) {
      document.getElementById('delete_' + number).style.marginLeft = "245px";
    }
  }
  else if(number == 3 ) {
    document.getElementById('add_' + number).style.display = "none";
    document.getElementById('delete_' + number).style.marginLeft = "245px";
    marginLeft = "245px";
    display = "none";
  }
  $(
    '<li id = form' + (number+1) + '>' +
      '<span class = "form timeData">' +
      '<span class= "input-group clockpicker bt0">' +
      '<input type= text value = "" id = "startForm'+(number+1)+'" class="form-control" name= startTime' + (number+1) + '>' +
      '</span>' +
      '<span class="bt0">' +
      '<span>' +
      ' ～ ' +
      '</span>' +
      '</span>' +
      '<span class="input-group clockpicker bt0">' +
      '<input type="text" value = "" id = "endForm'+(number+1)+'" class="form-control" name= endTime' + (number+1) + '>' +
      '<a>' +
      '<img src= /img/add.png alt=登録 class="btn-shadow disOffgreenBtn" width=25 height=25 onclick=addLine(event) style="padding:2px !important; display: '+display+'; margin-left: 245px; margin-top: -25px;" id=add_'+(number+1)+'>' +
      '</a>' +
      '<a>' +
      '<img src= /img/dustbox.png alt=削除 class="btn-shadow redBtn deleteBtn" width=25 height=25 onclick="deleteLine(event)" style="padding:2px !important; display: block; margin-left:'+ marginLeft+'; margin-top: -25px;" id=delete_'+(number+1)+'>' +
      '</a>' +
      '</span>' +
      '</li>'
  )
  .appendTo('.allForm');
  $('.clockpicker').clockpicker({
    donetext:'設定',
    placement: 'orignal',
    align: 'originalTime'
  });
}

//時間削除
function deleteLine(event){
  document.getElementById('popup-frame').style.height = (parseInt($('#popup-frame').css('height'),10) - 41) + 'px';
  number = Number(event.target.id.substr(event.target.id.indexOf("_")+1));
  document.getElementById('form' + number).remove();
  var length = $('.timeData').length;
  //削除した下の行を全て一つ上げる
  for(i=number + 1; i<=length;i++) {
    document.getElementById('form' + i).id = 'form' + (i-1);
    document.getElementById('add_' + i).id = 'add_' + (i-1);
    document.getElementById('delete_' + i).id = 'delete_' + (i-1);
    document.getElementById('startForm' + i).name = 'startTime' + (i-1);
    document.getElementById('startForm' + i).id = 'startForm' + (i-1);
    document.getElementById('endForm' + i).name = 'endTime' + (i-1);
    document.getElementById('endForm' + i).id = 'endForm' + (i-1);
  }
  //一番下を削除した場合
  if(number == length) {
    document.getElementById('add_' + (number-1)).style.display = "block";
    if(number != 1) {
      document.getElementById('delete_' + (number-1)).style.marginLeft = "272px";
    }
  }
  //一番下ではないところを削除した場合
  else {
    document.getElementById('add_' + (length-1)).style.display = "block";
    document.getElementById('delete_' + (length-1)).style.marginLeft = "272px";
  }
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
  <span class = "allForm">
  <?php
  $number = count($timeData->{$date}) -1;
  foreach($timeData->{$date} as $key => $v) { ?>
    <li id = <?= "form".$key ?>>
      <?php if($key === 0) { ?>
        <label style = "font-weight:bold;">営業時間</label>
      <?php }
      if($key === 0) { ?>
        <span class = "firstForm timeData">
      <?php }
      else { ?>
        <span class = "form timeData">
      <?php }
      if($key === 0 && empty($v->start) && empty($v->end)) {
        $v->start = "";
        $v->end = "";
      }  ?>

      <span class="input-group clockpicker bt0">
        <?php if($key === 0 && empty($v->start) && empty($v->end)) { ?>
          <input type="text" value = "" class="form-control" name=<?= "startTime".$key ?> id=<?= "startForm".$key ?>>
        <?php } else { ?>
          <input type="text" value = <?= $v->start; ?>  class="form-control" name=<?= "startTime".$key ?> id=<?= "startForm".$key ?>>
        <?php } ?>
      </span>
      <span class="bt0"><span>～</span></span>
      <span class="input-group clockpicker bt0">
        <?php if($key === 0 && empty($v->start) && empty($v->end)) { ?>
          <input type="text" value = "" class="form-control" name=<?= "endTime".$key ?> id=<?= "endForm".$key ?>>
        <?php }
        else { ?>
          <input type="text" value = <?= $v->end; ?> class="form-control" name=<?= "endTime".$key ?> id=<?= "endForm".$key ?>>
        <?php }
         if($key == $number && $key != 4) {
            $display = 'block';
          }
          else {
            $display = 'none';
          }
          ?>
          <a>
            <?= $this->Html->image('add.png', array(
              'alt' => '登録',
              'class' => 'btn-shadow disOffgreenBtn',
              'width' => 25,
              'height' => 25,
              'id' => 'add_'.$key,
              'onclick' => 'addLine(event)',
              'style' => 'padding:2px !important; display: '.$display.'; margin-left: 245px; margin-top: -25px;'
            )) ?>
          </a>
         <?php
         if(($key != 0 && $key != $number) || $key == 4) {
            $marginLeft = '245px';
            $check = 'true';
          }
          else if($key != 0) {
            $marginLeft = '272px';
            $check = 'true';
          }
          if($check == 'true') { ?>
            <a>
            <?= $this->Html->image('dustbox.png', array(
              'alt' => '削除',
              'class' => 'btn-shadow redBtn deleteBtn',
              'data-balloon-position' => '35',
              'width' => 25,
              'height' => 25,
              'id' => 'delete_'.$key,
              'onclick' => 'deleteLine(event)',
              'style' => 'padding:2px !important; display: block; margin-left: '.$marginLeft.'; margin-top: -25px;')) ?>
            </a>
          <?php } ?>
      </span>
    </li>
    <script type="text/javascript">
      $('.clockpicker').clockpicker({
        donetext:'設定',
        placement: 'orignal',
        align: 'originalTime'
      });
    </script>
  <?php } ?>
  </span>
  <li>
  <div id = "error"></div>
  </li>
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
      if($v == 'weekpub') {
        $v2 = '祝日';
      }
      if($v != 'week' && $v != 'weekend' && $v != 'weekpub' && mb_substr($dayOfWeek, 0, 1) != $v2) { ?>
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
        else if(($v == 'week' || $v == 'weekend' || $v == 'weekpub') && $dayOfWeek != $v2) {?>
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
