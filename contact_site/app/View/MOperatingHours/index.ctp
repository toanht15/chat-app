<?php
//
$detailHiddenClass = "";
if ( !(!empty($this->data['MOperatingHour']['active_flg']) && strcmp($this->data['MOperatingHour']['active_flg'],C_ACTIVE_ENABLED) === 0) ) {
  $detailHiddenClass = "detail_hidden";
}
?>
<?= $this->element('MOperatingHours/script') ?>

<div id='moperating_hours_idx' class="card-shadow">

  <div id='moperating_hours_add_title'>
     <div class="fLeft">
        <?= $this->Html->image('operating_hour_g.png', array('alt' => 'チャット基本設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?>
      </div>
    <h1>営業時間設定<span id="sortMessage"></span></h1>
  </div>
  <div id='moperating_hours_form' class="p20x">
  <?= $this->Form->create('MOperatingHour', ['type' => 'post','name' => 'operatingHours', 'url' => ['controller' => 'MOperatingHours', 'action' => 'index', '']]); ?>
    <div class ="content">
      <div>
        <label style="display:inline-block;
        <?php echo (($widgetData == C_WIDGET_DISPLAY_CODE_TIME || $check == 'included') || !$coreSettings[C_COMPANY_USE_OPERATING_HOUR]) ? 'color: #CCCCCC;' : '';?>"
        <?php echo ($widgetData == C_WIDGET_DISPLAY_CODE_TIME || $coreSettings[C_COMPANY_USE_OPERATING_HOUR] || $check == true) ? 'class=commontooltip' : '';?>
        <?php echo ($check == 'included' && $coreSettings[C_COMPANY_USE_OPERATING_HOUR]) ? 'data-text=オートメッセージ設定の「条件設定」に「営業時間設定」が含まれているメッセージがあります' : '';?>
        <?php echo ($widgetData == C_WIDGET_DISPLAY_CODE_TIME && $coreSettings[C_COMPANY_USE_OPERATING_HOUR]) ? 'data-text=ウィジェット設定の「表示する条件」を「営業時間内のみ表示する」から変更してください' : '';?>
        <?php echo (($widgetData == C_WIDGET_DISPLAY_CODE_TIME || $check == 'included') && $coreSettings[C_COMPANY_USE_OPERATING_HOUR]) ? 'data-balloon-position=31.5' : '';?>
        <?php echo (($widgetData == C_WIDGET_DISPLAY_CODE_TIME || $check == 'included') && $coreSettings[C_COMPANY_USE_OPERATING_HOUR]) ? 'operatingHours=operatingHoursPage' : '';?>
        >
          <?php
            $settings = [
              'type' => 'radio',
              'options' => $scFlgOpt,
              'default' => C_ACTIVE_ENABLED,
              'legend' => false,
              'separator' => '</label><br><label style="display:inline-block;"'.($coreSettings[C_COMPANY_USE_OPERATING_HOUR] ? '' : 'style="color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="34.5"').'>',
              'label' => false,
              'div' => false,
              'disabled' => !$coreSettings[C_COMPANY_USE_OPERATING_HOUR],
              'class' => "pointer"
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

      <div id="detail_content">
        <dl class="<?=$detailHiddenClass?>" id = "moperating_hours_table" <?php echo ($this->data['MOperatingHour']['type'] == C_TYPE_EVERY) ? 'style="height:49em"' : 'style="height:24em"';?>>
          <dt>条件設定<dt-detail></dt-detail></dt>
          <dd>
            <li>
              <label class="pointer"><?=  $this->Form->input('type', array('type' => 'radio', 'default' => '1','onclick' => 'entryChange1();', 'name' => 'data[MOperatingHour][type]', 'label' => false, 'legend' => false,'options' => array('1' => '毎日', '2' => '平日/週末'))); ?></label>
            </li>
          </dd>
          <?php
            echo $this->Form->hidden('outputData',array('value' => $operatingHourData['MOperatingHour']['time_settings']));
          ?>
          <dt>時間設定</dt>
          <dd>
            <div id='moperating_hours_list'>
              <?php
              $everydayData = json_decode($operatingHourData['MOperatingHour']['time_settings'])->everyday;
              $weeklyData = json_decode($operatingHourData['MOperatingHour']['time_settings'])->weekly;
              if($operatingHourData['MOperatingHour']['type'] == 1) {
               ?>
              <table class = "everyday" id = "firstTable">
              <?php } else { ?>
              <table class = "everyday" id = "firstTable" style = "display:none;">
              <?php } ?>
                <tbody>
                <?php
                  foreach($days as $v2) {
                    if($v2 == 'mon') {
                      $dayOfWeek = '月曜日';
                    }
                    if($v2 == 'tue') {
                      $dayOfWeek = '火曜日';
                    }
                    if($v2 == 'wed') {
                      $dayOfWeek = '水曜日';
                    }
                    if($v2 == 'thu') {
                      $dayOfWeek = '木曜日';
                    }
                    if($v2 == 'fri') {
                      $dayOfWeek = '金曜日';
                    }
                    if($v2 == 'sat') {
                      $dayOfWeek = '土曜日';
                    }
                    if($v2 == 'sun') {
                      $dayOfWeek = '日曜日';
                    }
                    if($v2 == 'pub') {
                      $dayOfWeek = '祝日';
                    }
                ?>
                  <tr>
                  <?php
                    $day = "";
                    foreach($everydayData->{$v2} as $v) {
                      if(empty($day)) {
                        if(empty($v->start) && empty($v->end)) {
                          $day = "休み";
                        }
                        else {
                          $day = $v->start.'-'.$v->end;
                        }
                      }
                      else {
                        $day = $day."　/　".$v->start.'-'.$v->end;
                      }
                    }
                    if($day == "休み") {
                  ?>
                    <td class="tCenter dayOfWeek"><span class = "green holiday day" id = "<?= $v2.'day' ?>"><?= $dayOfWeek ?></span></td>
                  <?php }
                    else {
                  ?>
                    <td class="tCenter dayOfWeek"><span class = "green day" id = "<?= $v2.'day' ?>"><?= $dayOfWeek ?></span></td>
                     <?php } ?>
                      <td id = "<?= $v2 ?>" class = "time">
                      <?php
                        echo $day; ?>
                      </td>
                      <td>
                      <?php echo $this->Html->link(
                        $this->Html->image(
                          'edit.png',
                          array(
                            'alt' => '編集',
                            'width' => 30,
                            'height' => 30
                          )
                        ),
                        'javascript:void(0)',
                        array(
                          'class' => 'btn-shadow greenBtn fRight',
                          'style' => 'width: 35px; height: 35px; padding: 2px;',
                          'onclick' => "openAddDialog('$v2','$day');",
                          'escape' => false
                        )
                      ); ?>
                      </td>
                    </tr>
                 <?php } ?>
               </tbody>
              </table>

              <?php
              if($operatingHourData['MOperatingHour']['type'] == 2) { ?>
                <table class = "weekly" id = "secondTable">
              <?php
              } else { ?>
                <table class = "weekly" id = "secondTable" style = "display:none;">
              <?php } ?>
              <tbody>
                <?php
                  foreach($weekly as $v2) {
                    if($v2 == 'week') {
                      $dayOfWeek = '平日';
                    }
                    if($v2 == 'weekend') {
                      $dayOfWeek = '週末';
                    }
                    if($v2 == 'weekpub') {
                      $dayOfWeek = '祝日';
                    }
                ?>
                <tr>
                <?php
                  $day = "";
                  foreach($weeklyData   ->{$v2} as $v) {
                    if(empty($day)) {
                      if(empty($v->start) && empty($v->end)) {
                        $day = "休み";
                      }
                      else {
                        $day = $v->start.'-'.$v->end;
                      }
                    }
                    else {
                      $day = $day."　".$v->start.'-'.$v->end;
                    }
                  }
                ?>
                <?php if($day == "休み") { ?>
                  <td class="tCenter dayOfWeek"><span class = "green holiday day" id = "<?= $v2.'day' ?>"><?= $dayOfWeek ?></span></td>
                <?php }
                  else { ?>
                  <td class="tCenter dayOfWeek"><span class = "green day" id = "<?= $v2.'day' ?>"><?= $dayOfWeek ?></span></td>
                <?php } ?>
                  <td id = "<?= $v2 ?>" class = "time">
                  <?php
                  echo $day; ?>
                 </td>
                  <td>
                  <?php echo $this->Html->link(
                    $this->Html->image(
                      'edit.png',
                      array(
                        'alt' => '編集',
                        'width' => 30,
                        'height' => 30
                      )
                    ),
                    'javascript:void(0)',
                    array(
                      'class' => 'btn-shadow greenBtn fRight',
                      'style' => 'width: 35px; height: 35px; padding: 2px;',
                      'onclick' => "openAddDialog('$v2','$day');",
                      'escape' => false
                    )
                  );
                  ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </dd>
    </dl>
  </div>
  </div>
<!-- /* 操作 */ -->
  <?= $this->Form->end(); ?>
  <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow inlineSaveBtn']) ?>
</div>