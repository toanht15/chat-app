<?= $this->element('Statistics/datepicker') ?>
<?= $this->element('Statistics/script') ?>

<div id='statistic_idx'>

  <div id="sincloApp">
    <div id='statisticAnotherWindow_title'>
    <?php if (!empty($errorMessage)) echo "<li class='error-message'>" . h($errorMessage) . "</li>";
    else { ?>

    <condition-bar>
  <?php if(empty($item)) { ?>
    <h1><?= $data['users'][0]['m_users']['display_name'] ?></h1>
      <right-parts>
        <a href="#" id="outputPrivateOperatorCSV" class="btn-shadow blueBtn">CSV出力</a>
      </right-parts>
    </condition-bar>
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
    <?php } ?>
      <right-parts>
        <a href="#" id="outputEachItemOperatorCSV" class="btn-shadow blueBtn">CSV出力</a>
      </right-parts>
    </condition-bar>
    <?php } ?>
  </div>

    <div id='statistics_content' class="p20trl" style="visibility:hidden;">
      <div id='opChatRequestTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>サイト訪問者がチャットを送信した件数(※初回メッセージのみカウント)</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='opChatResponseTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>チャットリクエストに対してオペレータが入室した件数（※初回入室のみカウント）</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='opChatEffectivenessTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>成果が「有効」として登録された件数</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='opChatConsumerWaitAverageTimeTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>サイト訪問者の初回メッセージを受信してから、オペレータがチャットに入室するまでの平均時間</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='opChatResponseAverageTimeTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>サイト訪問者の初回メッセージを受信してから、オペレータが初回メッセージを送信するまでの平均時間</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='opChatEffectivenessResponseRateTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>チャット有効件数／チャットリクエスト件数</span></li>
          </ul>
        </icon-annotation>
      </div>

    <!-- /* テーブル表示エリア */ -->

    <table id="statistics_table" class="display" cellspacing="0" width = "100%">
      <thead>
      <!-- /* 月別の場合 */ -->
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
      <!-- /* 日別の場合 */ -->
        <?php if($date == 'eachOperatorMonthly') {
          $start = 1;
          $end = $daylyEndDate; ?>
          <tr>
            <th class="thMinWidth">統計項目 / 日別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th class="thMinWidthDayly"><?= $i.'日' ?></th>
            <?php } ?>
            <th class="thMinWidthDayly">合計・平均</th>
          </tr>
        <?php } ?>
      <!-- /* 時別の場合 */ -->
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
      //各項目ごと画面の場合
      if(!empty($item)) {
        //項目がログイン件数の場合
        if($item == 'ログイン件数') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['display_name'] ?></td>
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
        //項目がリクエスト件数の場合
        else if($item == 'リクエスト件数') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['display_name'] ?></td>
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
      //項目が応対件数の場合
      else if($item == '応対件数') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['display_name'] ?></td>
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
      //項目が有効件数の場合
      else if($item == '有効件数') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['display_name'] ?></td>
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
      //項目が平均消費者待機時間の場合
      else if($item == '平均消費者待機時間') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo ($v['avgEnteringRommTimeNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php } ?>
          <td><?php echo ($v['allAvgEnteringRommTimeData']) ?></td>
        </tr>
        <?php } ?>
      <?php }
      //項目が平均応答時間の場合
      else if($item == '平均応答時間') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo ($v['responseAvgTimeNumber'][$days.sprintf("%02d",$i).$seconds]) ?></td>
          <?php } ?>
          <td><?php echo ($v['allResponseAvgTimeNumber']) ?></td>
        </tr>
        <?php } ?>
      <?php }
      //項目が有効率の場合
      else if($item == '有効率') {
      ?>
        <?php foreach($data['users'] as $k => $v) { ?>
        <tr>
          <td><?= $v['display_name'] ?></td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_numeric($v['effectivenessRate'][$days.sprintf("%02d",$i).$seconds])) {
              $checkData = ' %';
            }
            else {
              $checkData = '';
            } ?>
            <td><?php echo ($v['effectivenessRate'][$days.sprintf("%02d",$i).$seconds].$checkData) ?></td>
          <?php }
            if(is_numeric($v['allEffectivenessRate'])) {
              $checkData = ' %';
            }
            else {
              $checkData = '';
            }
              ?>
          <td><?php echo ($v['allEffectivenessRate'].$checkData) ?></td>
        </tr>
        <?php }
        }?>

      </tbody>
    </table>
    <div id = 'action_btn_area'>
      <condition-bar>
        <a href="#" id="closeWindow" class="btn-shadow whiteBtn">閉じる</a>
      </condition-bar>
    </div>
      <?php }
      else if($date == 'eachOperatorDaily') {
        $days ='';
        $seconds = ':00';
      }
      else if($date == 'eachOperatorYearly' or $date == 'eachOperatorMonthly') {
        $days = $type.'-';
        $seconds = '';
      }
      //オペレータ個人画面の場合
      if(empty($item)) {  ?>
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
          <td id="opChatRequestLabel" class = 'tooltip'>チャットリクエスト件数
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
          <td id = 'opChatResponseLabel'  class = 'tooltip'>チャット応対件数
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
          <td id = 'opChatEffectivenessLabel' class = 'tooltip'>チャット有効件数
            <div class="questionBalloon questionBalloonPosition8s">
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
          <td id = 'opChatConsumerWaitAverageTimeLabel' class = 'tooltip'>平均消費者待機時間
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
          <td id = 'opChatResponseAverageTimeLabel' class = 'tooltip'>平均応対時間
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
          <td id = 'opChatEffectivenessResponseRateLabel' class = 'tooltip'>チャット有効率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) {
            if(is_numeric($data['effectivenessRate'][$days.sprintf("%02d",$i).$seconds])) {
              $checkData = ' %';
            }
            else {
              $checkData = '';
            } ?>
            <td><?php echo $data['effectivenessRate'][$days.sprintf("%02d",$i).$seconds].$checkData ?></td>
          <?php }
          if(is_numeric($data['allEffectivenessRate'])) {
            $checkData = ' %';
          }
          else {
            $checkData = '';
          } ?>
          <td><?php echo $data['allEffectivenessRate'].$checkData ?></td>
        </tr>
      </tbody>
    </table>
    <div id = 'action_btn_area'>
      <condition-bar>
        <a href="#" id="closeWindow" class="btn-shadow whiteBtn">閉じる</a>
      </condition-bar>
    </div>
    <?php }
    }?>
    <?=$this->Form->create('statistics', ['action' => 'forChat']);?>
      <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
    </div>
  </div>
</div>

