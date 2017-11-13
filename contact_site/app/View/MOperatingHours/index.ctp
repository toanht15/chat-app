<?= $this->element('MOperatingHours/script') ?>

<div id='moperating_hours_idx' class="card-shadow">
  <?php if(!$coreSettings[C_COMPANY_USE_OPERATING_HOUR]): ?>
    <div id="modal" style="display: table; position: absolute; top:15px; left:15px; width: calc(100% - 30px); height: calc(100% - 30px); z-index: 5; background-color: rgba(0, 0, 0, 0.8);">
      <p style="font-size: 15px; color: #FFF; display: table-cell; vertical-align: middle; text-align: center;">こちらの機能はスタンダードプランからご利用いただけます。</p>
    </div>
  <?php endif; ?>

  <div id='moperating_hours_add_title'>
    <h1>営業時間設定<span id="sortMessage"></span></h1>
  </div>
  <div id='moperating_hours_form' class="p20x">
  <?= $this->Form->create('MOperatingHour', ['type' => 'post','name' => 'operatingHours', 'url' => ['controller' => 'MOperatingHours', 'action' => 'index', '']]); ?>
    <div class ="content">
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

      <div id="detail_content">
        <dl>
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
              $timeData = json_decode($operatingHourData['MOperatingHour']['time_settings'])->everyday;
              $timeData2 = json_decode($operatingHourData['MOperatingHour']['time_settings'])->weekly;
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
                    foreach($timeData->{$v2} as $v) {
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
                foreach($timeData2->{$v2} as $v) {
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
<!-- /* 操作 */ -->
  <div id="m_widget_setting_action" class="fotterBtnArea">
   <?= $this->Form->end(); ?>
    <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
  </div>
</div>