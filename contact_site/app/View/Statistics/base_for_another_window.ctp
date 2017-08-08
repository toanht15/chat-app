<?= $this->element('Statistics/datepicker') ?>
<?= $this->element('Statistics/script') ?>
<div id='statistic_idx' class="card-shadow">

  <div id='statistic_title'>
  <?php if(empty($item)) { ?>
    <h1><?= $data['users'][0]['m_users']['display_name'] ?></h1>
  <?php }
  else {
    if($item == 'ログイン件数') { ?>
      <h1>ログイン件数</h1>
    <?php }
    if($item == 'リクエスト件数') { ?>
      <h1>チャットリクエスト件数</h1>
    <?php }
    if($item == '応対件数') { ?>
      <h1>チャット応対件数</h1>
    <?php }
    if($item == '有効件数') { ?>
      <h1>チャット有効件数</h1>
    <?php }
    if($item == '平均消費者待機時間') { ?>
      <h1>平均消費者待機時間</h1>
    <?php }
    if($item == '平均応答時間') { ?>
      <h1>平均応答時間</h1>
    <?php }
    if($item == '有効率') { ?>
      <h1>チャット有効率</h1>
    <?php }
    } ?>

  </div>

<div id="sincloApp">

    <div id='statistics_content' class="p20trl" style="visibility:hidden;">
      <div id='chatRequestTooltip' class="explainTooltip">

      </div>

    <!-- /* テーブル表示エリア */ -->

    <table id="statistics_table" class="display" cellspacing="0" width = "100%">
      <thead>
        <?php if($date == 'eachOperatorYearly') {
          $start = 1;
          $end = 12; ?>
          <tr>
            <th class="thMinWidth">統計項目 / 月別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th class="thMinWidthDayly"><?= $i.'月' ?></th>
            <?php } ?>
            <th class="thMinWidthDayly">合計・平均</th>
          </tr>
        <?php } ?>
        <?php if($date == 'eachOperatorMonthly') {
          $start = 1;
          $end = $daylyEndDate;
        ?>
          <tr>
            <th class="thMinWidth">統計項目 / 日別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th class="thMinWidthDayly"><?= $i.'日' ?></th>
            <?php } ?>
            <th class="thMinWidthDayly">合計・平均</th>
          </tr>
        <?php } ?>
        <?php if($date == 'eachOperatorDaily') {
          $start = 0;
          $end = 23; ?>
          <tr>
            <th class="thMinWidth">統計項目 / 時別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th class = "thMinWidthTimely"><?= sprintf("%02d",$i).'-'.sprintf("%02d",$i+1) ?></th>
            <?php } ?>
            <th class="thMinWidthDayly">合計・平均</th>
          </tr>
        <?php } ?>
      </thead>
    <tbody>
      <?php
        if($date == 'eachOperatorDaily') {
          $days ='';
          $seconds = ':00';
        }
        else if($date == 'eachOperatorYearly' or $date == 'eachOperatorMonthly') {
          $days = $type.'-';
          $seconds = '';
        }
        if(!empty($item)) {
        if($item == 'ログイン件数') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['m_users']['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_int($v['loginNumber'][$days.sprintf("%02d",$i).$seconds]) == 'true') { ?>
              <td><?php echo number_format($v['loginNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($v['loginNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            } ?>
          <td><?php echo number_format($v['allLoginNumber']) ?></td>
        </tr>
        <?php } ?>
      <?php }
        else if($item == 'リクエスト件数') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['m_users']['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_int($v['requestNumber'][$days.sprintf("%02d",$i).$seconds]) == 'true') { ?>
              <td><?php echo number_format($v['requestNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($v['requestNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
              <?php }
              } ?>
          <td><?php echo number_format($v['allRequestNumber']) ?></td>
        </tr>
        <?php } ?>
      <?php }
      else if($item == '応対件数') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['m_users']['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_int($v['responseNumber'][$days.sprintf("%02d",$i).$seconds]) == 'true') { ?>
              <td><?php echo number_format($v['responseNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($v['responseNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
              <?php }
              } ?>
          <td><?php echo number_format($v['allResponseNumber']) ?></td>
        </tr>
        <?php } ?>
      <?php }

      else if($item == '有効件数') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['m_users']['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_int($v['effectivenessNumber'][$days.sprintf("%02d",$i).$seconds]) == 'true') { ?>
              <td><?php echo number_format($v['effectivenessNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($v['effectivenessNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            } ?>
          <td><?php echo number_format($v['allEffectivenessNumber']) ?></td>
        </tr>
        <?php } ?>
      <?php }
      else if($item == '平均消費者待機時間') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['m_users']['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo ($v['avgEnteringRommTimeNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php } ?>
          <td><?php echo ($v['allAvgEnteringRommTimeData']) ?></td>
        </tr>
        <?php } ?>
      <?php }
      else if($item == '平均応答時間') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['m_users']['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo ($v['responseAvgTimeNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php } ?>
          <td><?php echo ($v['allResponseAvgTimeNumber']) ?></td>
        </tr>
        <?php } ?>
      <?php }
      else if($item == '有効率') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['m_users']['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo ($v['effectivenessRate'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php } ?>
          <td><?php echo ($v['allEffectivenessRate']) ?></td>
        </tr>
        <?php }
      }?>

      <?php }
        else if($date == 'eachOperatorDaily') {
          $days ='';
          $seconds = ':00';
        }
        else if($date == 'eachOperatorYearly' or $date == 'eachOperatorMonthly') {
          $days = $type.'-';
          $seconds = '';
        }
        if(empty($item)) {
        ?>
      <tr>
          <td class = 'tooltip'>ログイン件数</td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_int($data['loginNumberData'][$days.sprintf("%02d",$i).$seconds]) == 'true') { ?>
              <td><?php echo number_format($data['loginNumberData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['loginNumberData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php }
          } ?>
          <td><?php echo number_format($data['allLoginNumberData']) ?></td>
        </tr>
        <tr>
        <td id="chatRequestLabel" class = 'tooltip'>チャットリクエスト件数
          <div class="questionBalloon questionBalloonPosition11">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_int($data['requestNumberData'][$days.sprintf("%02d",$i).$seconds]) == 'true') { ?>
              <td><?php echo number_format($data['requestNumberData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['requestNumberData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php }
          } ?>
          <td><?php echo number_format($data['allRequestNumberData']) ?></td>
        </tr>
        <tr>
        <td id = 'chatResponseLabel'  class = 'tooltip'>チャット応対件数
          <div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_int($data['responseNumberData'][$days.sprintf("%02d",$i).$seconds]) == 'true') { ?>
              <td><?php echo number_format($data['responseNumberData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['responseNumberData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php }
          }?>
          <td><?php echo number_format($data['allResponseNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseLabel' class = 'tooltip'>チャット有効件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_int($data['effectivenessNumberData'][$days.sprintf("%02d",$i).$seconds]) == 'true') { ?>
              <td><?php echo number_format($data['effectivenessNumberData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php }
          else { ?>
            <td><?php echo ($data['effectivenessNumberData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php }
          } ?>
          <td><?php echo number_format($data['allEffectivenessNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatDenialLabel' class = 'tooltip'>平均消費者待機時間
            <div class="questionBalloon questionBalloonPosition9">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo ($data['avgEnteringRommTimeData'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php } ?>
          <td><?php echo ($data['allAvgEnteringRommTimeData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessLabel' class = 'tooltip'>平均応対時間
            <div class="questionBalloon questionBalloonPosition6">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseAvgTimeData'][$days.sprintf("%02d",$i).$seconds] ?></td>
          <?php } ?>
          <td><?php echo $data['allResponseAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatRequestAverageTimeLabel' class = 'tooltip'>チャット有効率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['effectivenessRate'][$days.sprintf("%02d",$i).$seconds] ?></td>
          <?php } ?>
          <td><?php echo $data['allEffectivenessRate'] ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?=$this->Form->create('statistics', ['action' => 'forChat']);?>
      <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
  </div>
</div>
</div>

