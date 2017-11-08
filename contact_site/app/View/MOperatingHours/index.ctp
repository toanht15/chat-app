<script type="text/javascript">
radio = document.getElementsByName('data[MOperatingHour][type]');
function entryChange1(){
  console.log('動かないわよ');
  console.log(radio);
  radio = document.getElementsByName('data[MOperatingHour][type]');
  if(radio[0].checked) {
    //フォーム
    document.getElementById('uuu').style.display = "none";
    document.getElementById('ooo').style.display = "";
    document.getElementById('qqqqq').style.height = "49em";
  }else if(radio[1].checked) {
    document.getElementById('ooo').style.display = "none";
    document.getElementById('uuu').style.display = "";
    document.getElementById('qqqqq').style.height = "25em";
  }
}


//営業時間モーダル
function openAddDialog(dayOfWeek,timeData){
  jsonData = document.getElementsByName('data[MOperatingHour][outputData][]]')[0].value;
  radio = document.getElementsByName('data[MOperatingHour][type]');
  if(radio[0].checked) {
    var dayType = 1;
  }
  else {
    var dayType = 2;
  }
  //定型文並べ替えチェックボックスもしくはカテゴリ並べ替えチェックボックスが入っているときはリンク無効とする
  openEntryDialog({day: dayOfWeek, timeData:timeData,title:"営業時間登録",jsonData:jsonData,dayType:dayType});
}

//定型文新規追加/編集ダイアログ表示
function openEntryDialog(setting){
  console.log(setting);
  var type = setting.type;
  $.ajax({
    type: 'post',
    data: setting, // type:1 => type, tabid  type:2 => type, id, tabid
    dataType: 'html',
    cache: false,
    url: "<?= $this->Html->url('/MOperatingHours/remoteOpenEntryForm') ?>",
    success: function(html){
      modalOpen.call(window, html, 'p-operatinghours-entry', setting.title, 'moment');
    }
  });
}

function scSettingToggle(){
  if ( $("#MOperatingHourActiveFlg1").prop("checked") ) { // 営業時間設定を利用する場合
    $("#sc_content dl").removeClass("sc_hidden"); // ユーザーリストを表示
    $("#tcampaigns_list table").removeClass("sc_hidden");
    $("#sc_content input").prop("disabled", false); // ユーザーリストの数字項目をenabled
  }
  else { // 営業時間設定を利用しない場合
    $("#sc_content dl").addClass("sc_hidden"); // ユーザーリストを非表示
    $("#tcampaigns_list table").addClass("sc_hidden"); // ユーザーリストを非表示
    $("#sc_content input").prop("disabled", true); // ユーザーリストの数字項目をdisabled
  }
}

$(document).ready(function(){
  // 同時対応数上限のON/OFFの切り替わりを監視
  $(document).on('change', '[name="data[MOperatingHour][active_flg]"]', scSettingToggle);
  scSettingToggle(); // 初回のみ
});
// 保存処理
function saveAct(){
  setting = {title: "営業時間設定エラー"};
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
<?php
      $this->log('画面側だよ',LOG_DEBUG);
      $timeData = json_decode($operatingHourData['MOperatingHour']['time_settings'])->everyday;
      $timeData2 = json_decode($operatingHourData['MOperatingHour']['time_settings'])->weekly;
?>
<div id='display_exclusions_idx' class="card-shadow">
<?php if(!$coreSettings[C_COMPANY_USE_OPERATING_HOUR]): ?>
  <div id="modal" style="display: table; position: absolute; top:15px; left:15px; width: calc(100% - 30px); height: calc(100% - 30px); z-index: 5; background-color: rgba(0, 0, 0, 0.8);">
    <p style="font-size: 15px; color: #FFF; display: table-cell; vertical-align: middle; text-align: center;">こちらの機能はスタンダードプランからご利用いただけます。</p>
  </div>
<?php endif; ?>
  <div id='display_exclusions_add_title'>
    <h1>営業時間設定<span id="sortMessage"></span></h1>
  </div>
  <?= $this->Form->create('MOperatingHour', ['type' => 'post','name' => 'operatingHours', 'url' => ['controller' => 'MOperatingHours', 'action' => 'index', '']]); ?>
    <div class ="content" style = "margin: 2em 0 2em 2em;">
      <div>
        <label style="display:inline-block;" <?php echo $coreSettings[C_COMPANY_USE_OPERATING_HOUR] ? '' : 'style="color: #CCCCCC;" '?>>
          <?php
            $settings = [
              'type' => 'radio',
              'options' => $scFlgOpt,
              'default' => C_ACTIVE_ENABLED,
              'legend' => false,
              'separator' => '</label><br><label style="display:inline-block;"'.($coreSettings[C_COMPANY_USE_OPERATING_HOUR] ? '' : ' style="color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="34.5"').'>',
              'label' => false,
              'div' => false,
              'disabled' => !$coreSettings[C_COMPANY_USE_OPERATING_HOUR],
              'class' => 'pointer'
            ];
            echo $this->Form->input('active_flg',$settings);
          ?>
        </label>
        <?php
        // radioボタンがdisabledの場合POSTで値が送信されないため、hiddenで送信すべき値を補填する
        if(!$coreSettings[C_COMPANY_USE_OPERATING_HOUR]):
          ?>
          <input type="hidden" name="data[OperatingHour][active_flg]" value="2"/>
        <?php endif; ?>
      </div>
      <div id="sc_content" style = "margin-left: 2em;">
        <dl id = "qqqqq" style = "overflow: hidden; -webkit-transition: height 200ms linear; transition: height 200ms linear; height: 49em;">
          <dt style = "border-left: 5px solid #a2a2a2; padding: 0.25em 0.5em; font-weight: bold; background-color: #eee; width: 30em;">条件設定<dt-detail></dt-detail></dt>
            <dd>
              <li style = "padding-top: 36px; padding-left: 26px;">
                <label class="pointer"><?=  $this->Form->input('type', array('type' => 'radio', 'default' => '1','onclick' => 'entryChange1();', 'name' => 'data[MOperatingHour][type]', 'label' => false, 'legend' => false,'options' => array('1' => '毎日', '2' => '平日/週末'))); ?></label>
              </li>
            </dd>
            <?php
            echo $this->Form->hidden('outputData][]',array('value' => $operatingHourData['MOperatingHour']['time_settings']));
            ?>
            <?php if ( $this->Form->isFieldError('sc_default_num') ) echo $this->Form->error('sc_default_num', null, ['wrap' => 'p']); ?>
          <dt style = "border-left: 5px solid #a2a2a2; padding: 0.25em 0.5em; font-weight: bold; background-color: #eee; width: 30em;">時間設定</dt>
          <div id='display_exclusions_form' style = "padding: 20px 20px 20px 0px;">

            <div id='tcampaigns_list' style = 'padding: 5px 20px 20px 20px;'>
              <?php
              if($operatingHourData['MOperatingHour']['type'] == 1) {
                $type = $operatingHourData['MOperatingHour']['type'];
              $this->log('あーはやくしてくれ1',LOG_DEBUG); ?>
              <table class = "aaa" id = "ooo">
              <?php } else {
              $this->log('あーはやくしてくれ',LOG_DEBUG); ?>
              <table class = "aaa" id = "ooo" style = "display:none;">
              <?php } ?>
                <thead>
                </thead>
                <tbody class="sortable">
                  <tr>
                  <?php
                      $monday = "";
                      foreach($timeData->{'mon'} as $v) {
                        if(empty($monday)) {
                          if(empty($v->start) && empty($v->end)) {
                            $monday = "休み";
                          }
                          else {
                            $monday = $v->start.'-'.$v->end;
                          }
                        }
                        else {
                          $monday = $monday."　".$v->start.'-'.$v->end;
                        }
                      }
                    ?>
                    <?php if($monday == "休み") { ?>
                      <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "monday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold; " >月曜日</span></td>
                    <?php }
                    else { ?>
                      <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "monday" style = "color: #111;display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold; " >月曜日</span></td>
                    <?php } ?>
                    <td id = "mon" style = "font-weight:bold !important; padding-left: 18px; padding-top:3px;">
                    <?php
                      echo $monday; ?></td>
                    <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('mon','$monday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
        <?php
            $tuesday = "";
            foreach($timeData->{'tue'} as $v) {
              if(empty($tuesday)) {
                if(empty($v->start) && empty($v->end)) {
                  $tuesday = "休み";
                }
                else {
                  $tuesday = $v->start.'-'.$v->end;
                }
              }
              else {
                $tuesday = $tuesday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($tuesday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "tueday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">火曜日</span></td>
          <?php }
            else { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "tueday" style = "color: #111;display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">火曜日</span></td>
          <?php } ?>
          <td  id = "tue" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $tuesday; ?></td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('tue','$tuesday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
        <?php
            $wednesday = "";
            foreach($timeData->{'wed'} as $v) {
              if(empty($wednesday)) {
                if(empty($v->start) && empty($v->end)) {
                  $wednesday = "休み";
                }
                else {
                  $wednesday = $v->start.'-'.$v->end;
                }
              }
              else {
                $wednesday = $wednesday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($wednesday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "wedday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">水曜日</span></td>
          <?php }
            else { ?>
            <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "wedday" style = "color: #111;display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">水曜日</span></td>
          <?php } ?>
          <td id = "wed" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $wednesday; ?></td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('wed','$wednesday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
        <?php
            $thursday = "";
            foreach($timeData->{'thu'} as $v) {
              if(empty($thursday)) {
                if(empty($v->start) && empty($v->end)) {
                  $thursday = "休み";
                }
                else {
                  $thursday = $v->start.'-'.$v->end;
                }
              }
              else {
                $thursday = $thursday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($thursday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "thuday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">木曜日</span></td>
          <?php }
            else { ?>
            <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "thuday" style = "color: #111;display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">木曜日</span></td>
          <?php } ?>
          <td id = "thu" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $thursday; ?></td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('thu','$thursday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
          <?php
            $friday = "";
            foreach($timeData->{'fri'} as $v) {
              if(empty($friday)) {
                if(empty($v->start) && empty($v->end)) {
                  $friday = "休み";
                }
                else {
                  $friday = $v->start.'-'.$v->end;
                }
              }
              else {
                $friday = $friday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($friday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "friday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">金曜日</span></td>
          <?php }
            else { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "friday" style = "color: #111;display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">金曜日</span></td>
          <?php } ?>
          <td  id = "fri" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $friday; ?></td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('fri','$friday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
          <?php
            $saturday = "";
            foreach($timeData->{'sat'} as $v) {
              if(empty($saturday)) {
                if(empty($v->start) && empty($v->end)) {
                  $saturday = "休み";
                }
                else {
                  $saturday = $v->start.'-'.$v->end;
                }
              }
              else {
                $saturday = $saturday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($saturday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "satday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">土曜日</span></td>
          <?php }
            else { ?>
            <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "satday" style = "color: #111; display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">土曜日</span></td>
          <?php } ?>
          <td id = "sat" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $saturday; ?></td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('sat','$saturday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
          <?php
            $sunday = "";
            foreach($timeData->{'sun'} as $v) {
              if(empty($sunday)) {
                if(empty($v->start) && empty($v->end)) {
                  $sunday = "休み";
                }
                else {
                  $sunday = $v->start.'-'.$v->end;
                }
              }
              else {
                $sunday = $sunday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($sunday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "sunday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">日曜日</span></td>
          <?php }
            else { ?>
            <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "sunday" style = "color: #111; display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">日曜日</span></td>
          <?php } ?>
          <td id = "sun" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $sunday; ?></td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('sun','$sunday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
        <?php
            $publicHoliday = "";
            foreach($timeData->{'pub'} as $v) {
              if(empty($publicHoliday)) {
                if(empty($v->start) && empty($v->end)) {
                  $publicHoliday = "休み";
                }
                else {
                  $publicHoliday = $v->start.'-'.$v->end;
                }
              }
              else {
                $publicHoliday = $publicHoliday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($publicHoliday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "pubday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">祝日</span></td>
          <?php }
            else { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "publicHoliday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">祝日</span></td>
          <?php } ?>
          <td id = "pub" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $publicHoliday; ?></td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('pub','$publicHoliday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php
    if($operatingHourData['MOperatingHour']['type'] == 2) { ?>
    <table class = "bbb" id = "uuu">
    <?php
    } else { ?>
    <table class = "bbb" id = "uuu" style = "display:none;">
    <?php } ?>
      <thead>
      </thead>
      <tbody class="sortable">
        <tr>
        <?php
            $weekday = "";
            $this->log('おかしいいやろおおお',LOG_DEBUG);
            $this->log($timeData2->{'week'},LOG_DEBUG);
            foreach($timeData2->{'week'} as $v) {
              if(empty($weekday)) {
                if(empty($v->start) && empty($v->end)) {
                  $weekday = "休み";
                }
                else {
                  $weekday = $v->start.'-'.$v->end;
                }
              }
              else {
                $weekday = $weekday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($weekday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "weekday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold; " >平日</span></td>
          <?php }
            else { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "weekday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold; " >平日</span></td>
          <?php } ?>
          <td id = "week" style = "font-weight:bold !important; padding-left: 18px; padding-top:3px;">
          <?php
            echo $weekday; ?>
          </td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('week','$weekday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
        <?php
            $weekendday = "";
            $this->log('timedataaaaaa',LOG_DEBUG);
            $this->log($timeData,LOG_DEBUG);
            foreach($timeData2->{'weekend'} as $v) {
              if(empty($weekendday)) {
                if(empty($v->start) && empty($v->end)) {
                  $weekendday = "休み";
                }
                else {
                  $weekendday = $v->start.'-'.$v->end;
                }
              }
              else {
                $weekendday = $weekendday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($weekendday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "weekendday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">週末</span></td>
          <?php }
            else { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "weekendday" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">週末</span></td>
          <?php } ?>
          <td id = "weekend" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $weekendday; ?>
            </td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('weekend','$weekendday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
        <tr>
        <?php
            $publicHoliday = "";
            foreach($timeData2->{'pub2'} as $v) {
              if(empty($publicHoliday)) {
                if(empty($v->start) && empty($v->end)) {
                  $publicHoliday = "休み";
                }
                else {
                  $publicHoliday = $v->start.'-'.$v->end;
                }
              }
              else {
                $publicHoliday = $publicHoliday."　".$v->start.'-'.$v->end;
              }
            }
          ?>
          <?php if($publicHoliday == "休み") { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green eee" id = "pub2day" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">祝日</span></td>
          <?php }
            else { ?>
          <td class="tCenter" style = "width:5em; height:50px !important;"><span class = "green" id = "pub2day" style = "display: inline-block;width: 50px;height: 24px;line-height: 24px; text-align: center; font-weight:bold;">祝日</span></td>
          <?php } ?>
          <td id = "pub2" style = "font-weight:bold !important; padding-left: 18px;">
          <?php
            echo $publicHoliday; ?></td>
          <td>
          <?php echo $this->Html->link(
                $this->Html->image(
                  'edit.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow greenBtn blockCenter',
                  'style' => 'width: 35px; height: 35px; padding: 2px;',
                  'onclick' => "openAddDialog('pub2','$publicHoliday');",
                  'escape' => false
                )
              );
          ?>
          </td>
        </tr>
      </tbody>
    </table>
    </dl>
  </div>
  </div>
</li>
<!-- /* 操作 */ -->
    <div id="m_widget_setting_action" class="fotterBtnArea">
     <?= $this->Form->end(); ?>
      <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
    </div>
</div>
</div>
</div>