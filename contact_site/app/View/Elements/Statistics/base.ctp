<?= $this->element('Statistics/datepicker') ?>
<?= $this->element('Statistics/script') ?>

<div id="sincloApp">

  <div id='statistic_menu' class="p20trl">
    <!-- /* 対象期間選択エリア */ -->
    <condition-bar>
      <left-parts>
        <span id = "searchPeriod">対象期間：</span>
        <?= $this->Form->create(); ?>

          <?= $this->Form->input('dateType', array('type'=>'select','name' => 'dateFormat','onChange' => 'functionName()',
          'div'=>false, 'style' => 'vertical-align:middle;','label'=>false,'options'=>array('月別'=>'月別','日別'=>'日別','時別'=>'時別'), 'selected' => $date)); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'selectName2','id' => 'monthlyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeYear, 'selected' => $type,'style' => 'display:none;vertical-align:middle','empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'selectName3','id' => 'daylyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeDate,
          'style' => 'display:none;vertical-align:middle;','selected' => $type,'empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'text','name' => 'datefilter','id' => 'hourlyForm',
          'div'=>false, 'label'=>false,'options'=>array(substr($type,0,10) => substr($type,0,10)),
          'style' => 'width:10em;cursor:pointer;display:none','value' => substr($type,0,10),'placeholder' => '選択してください')); ?>

        <?= $this->Form->end(); ?>
      </left-parts>
      <right-parts>
        <a href="#" id="outputCSV" class="btn-shadow blueBtn">CSV出力</a>
      </right-parts>
      </condition-bar>
      <!-- /* 対象期間選択エリア */ -->
    </div><!-- #statistic_menu -->

    <div id='statistics_content' class="p20trl" style="visibility:hidden;">

    <!-- /* テーブル表示エリア */ -->

    <table id="statistics-table" class="display" cellspacing="0" width = "100%">
      <thead>
        <?php if($date == '月別') {
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
        <?php if($date == '日別') {
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
        <?php if($date == '時別') {
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
    <?php if($date == '日別' or $date == '月別') { ?>
      <tr>
        <td class = 'tooltip'>合計アクセス件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
           <td><?php echo $data['accessDatas']['allAccessNumberData'] ?></td>
      </tr>
      <tr>
        <td class = 'tooltip'>ウィジェット表示件数</td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['widgetDatas']['allWidgetNumberData'] ?></td>
      </tr>
      <tr>
        <td id="chatRequestLabel" class = 'tooltip'>チャットリクエスト件数
          <div class="questionBalloon questionBalloonPosition11">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['requestDatas']['allRequestNumberData'] ?></td>
      </tr>
      <tr>
        <td id = 'chatResponseLabel'  class = 'tooltip'>チャット応対件数
          <div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
              <td><?php echo $data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['responseDatas']['allResponseNumberData'] ?></td>
      </tr>
      <tr>
        <td id = 'chatAutomaticResponseLabel' class = 'tooltip'>自動返信応対件数
          <div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
              <td><?php echo $data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['automaticResponseData']['allAutomaticResponseNumberData'] ?></td>
      </tr>
      <tr>
        <td id = 'chatDenialLabel' class = 'tooltip'>チャット拒否件数
          <div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['coherentDatas']['allDenialNumberData'] ?></td>
      </tr>
      <tr>
        <td id = 'chatEffectivenessLabel' class = 'tooltip'>チャット有効件数
          <div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['coherentDatas']['effectivenessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['coherentDatas']['allEffectivenessNumberData'] ?></td>
      </tr>
      <tr>
        <td id = 'chatRequestAverageTimeLabel' class = 'tooltip'>平均チャットリクエスト時間
          <div class="questionBalloon questionBalloonPosition13">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['avgRequestTimeDatas']['requestAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['avgRequestTimeDatas']['allRequestAvgTimeData'] ?></td>
      </tr>
      <tr>
        <td id ='chatConsumerWaitAverageTimeLabel' class = 'tooltip'>平均消費者待機時間
          <div class="questionBalloon questionBalloonPosition9">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['consumerWatingAvgTimeDatas']['allConsumerWatingAvgTimeData'] ?></td>
      </tr>
      <tr>
        <td id ='chatResponseAverageTimeLabel' class = 'tooltip'>平均応答時間
          <div class="questionBalloon questionBalloonPosition6">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['responseAvgTimeData']['responseAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['responseAvgTimeData']['allResponseAvgTimeData'] ?></td>
      </tr>
      <tr>
        <td id = 'chatResponseRateLabel' class = 'tooltip'>チャット応対率
          <div class="questionBalloon questionBalloonPosition7">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['responseDatas']['responseRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['responseDatas']['allResponseRate'] ?></td>
      </tr>
      <tr>
        <td id = 'chatAutomaticResponseRateLabel' class = 'tooltip'>自動返信応対率
          <div class="questionBalloon questionBalloonPosition7">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['automaticResponseData']['automaticResponseRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['automaticResponseData']['allAutomaticResponseRate'] ?></td>
      </tr>
      <tr>
        <td id = 'chatEffectivenessResponseRateLabel' class = 'tooltip'>チャット有効率
          <div class="questionBalloon questionBalloonPosition7">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php echo $data['coherentDatas']['effectivenessRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
        <?php } ?>
        <td><?php echo $data['coherentDatas']['allEffectivenessRate'] ?></td>
      </tr>

      <?php }
      else if($date == '時別') { ?>
        <tr>
          <td class = 'tooltip'>合計アクセス件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['accessDatas']['accessNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['accessDatas']['allAccessNumberData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>ウィジェット表示件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['widgetDatas']['widgetNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['widgetDatas']['allWidgetNumberData'] ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'tooltip'>チャットリクエスト件数
            <div class="questionBalloon questionBalloonPosition11">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['requestDatas']['requestNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['requestDatas']['allRequestNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatResponseLabel'  class = 'tooltip'>チャット応対件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['responseDatas']['responseNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseLabel' class = 'tooltip'>自動返信応対件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['automaticResponseData']['automaticResponseNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatDenialLabel' class = 'tooltip'>チャット拒否件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['denialNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allDenialNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessLabel' class = 'tooltip'>チャット有効件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatRequestAverageTimeLabel' class = 'tooltip'>平均チャットリクエスト時間
            <div class="questionBalloon questionBalloonPosition13">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['avgRequestTimeDatas']['requestAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['avgRequestTimeDatas']['allRequestAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id ='chatConsumerWaitAverageTimeLabel' class = 'tooltip'>平均消費者待機時間
            <div class="questionBalloon questionBalloonPosition9">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['consumerWatingAvgTimeDatas']['allConsumerWatingAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id ='chatResponseAverageTimeLabel' class = 'tooltip'>平均応答時間
            <div class="questionBalloon questionBalloonPosition6">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseAvgTimeData']['responseAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseAvgTimeData']['allResponseAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatResponseRateLabel' class = 'tooltip'>チャット応対率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseDatas']['responseRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseRate'] ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseRateLabel' class = 'tooltip'>自動返信応対率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['automaticResponseData']['automaticResponseRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseRate'] ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessResponseRateLabel' class = 'tooltip'>チャット有効率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessRate'] ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <?=$this->Form->create('statistics', ['action' => 'forChat']);?>
      <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
  </div>
</div>

