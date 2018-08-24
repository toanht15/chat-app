<?= $this->element('Statistics/datepicker') ?>
<?= $this->element('Statistics/script') ?>

<div id="sincloApp">

  <div id='statistic_menu' class="p20x">
    <!-- /* 対象期間選択エリア */ -->
    <condition-bar>
      <left-parts>
        <span id = "searchPeriod">対象期間：</span>
        <?= $this->Form->create('Statistics'); ?>

          <?= $this->Form->input('dateType', array('type'=>'select','name' => 'dateFormat','onChange' => 'timeChange()',
          'div'=>false, 'style' => 'vertical-align:middle;','label'=>false,'options'=>array('月別'=>'月別','日別'=>'日別','時別'=>'時別'), 'selected' => $date)); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'monthlyName','id' => 'monthlyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeYear, 'selected' => $type,'style' => 'display:none;vertical-align:middle','empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'daylyName','id' => 'daylyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeDate,
          'style' => 'display:none;vertical-align:middle;','selected' => $type,'empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'text','name' => 'datefilter','id' => 'hourlyForm',
          'div'=>false, 'label'=>false,'style' => 'width:11em;cursor:pointer;display:none','value' => substr($type,0,10),'placeholder' => date("Y/m/d "),'autocomplete'=>'off')); ?>
          <b id = 'triangle'></b>

        <?= $this->Form->end(); ?>
      </left-parts>
      <right-parts>
        <a href="#" id="outputCSV" class="btn-shadow blueBtn">CSV出力</a>
      </right-parts>
      </condition-bar>
      <!-- /* 対象期間選択エリア */ -->
    </div><!-- #statistic_menu -->

    <div id='statistics_content' class="p20x" style="visibility:hidden;">
      <div id='chatRequestTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>サイト訪問者がチャットを利用（発言／選択肢を選択／リンククリック）した件数（※初回のみカウント）</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatResponseTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>有人チャットリクエストに対してオペレータが入室した件数（※初回入室のみカウント）</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatAutomaticResponseTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>オートリプライまたはシナリオが利用された件数（※初回のみカウント）</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatDenialTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>有人チャットリクエストに対してSorryメッセージを返却した件数（※初回のみカウント）</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatEffectivenessTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>成果が「有効」として登録された件数</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatCVTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>成果が「CV」として登録された件数</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatRequestAverageTimeTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>サイト訪問者がサイトアクセスしてから初回メッセージを送信するまでの平均時間</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatConsumerWaitAverageTimeTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>初回有人チャットリクエストを受信してからオペレータが入室するまでの平均時間</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatResponseAverageTimeTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>初回有人チャットリクエストを受信してからオペレータが初回メッセージを送信するまでの平均時間</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatResponseRateTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>有人チャット応対率：チャット応対件数／有人チャットリクエスト件数</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatAutomaticResponseRateTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>自動返信応対件数／チャットリクエスト件数</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatEffectivenessResponseRateTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>チャット有効件数／有人チャットリクエスト件数</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatLinkClickTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>サイト訪問者がリンクをクリックした件数（※複数回リンクをクリックした場合、クリックした件数分カウント）</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatRequestAbandonTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>有人チャットリクエストに対してオペレータが入室せず放棄した件数（※初回のみカウント）</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='chatRequestMannedTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>有人チャットリクエストの対象となる件数（※初回のみカウント）</span></li>
          </ul>
        </icon-annotation>
      </div>

    <!-- /* テーブル表示エリア */ -->

    <table id="statistics_table" class="display" cellspacing="0" width = "100%">
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
            <?php if(is_int($data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)]);?></td>
            <?php }
            else { ?>
              <td><?php echo $data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
            <?php } ?>
          <?php } ?>
           <td><?php echo number_format($data['accessDatas']['allAccessNumberData']) ?></td>
      </tr>
      <tr>
        <td class = 'tooltip'>ウィジェット表示件数</td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['widgetDatas']['allWidgetNumberData']) ?></td>
      </tr>
      <tr>
        <td id="chatRequestLabel" class = 'tooltip' >チャットリクエスト件数
          <div class="questionBalloon questionBalloonPosition11">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
          <?php }
            else { ?>
              <td><?php echo ($data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['requestDatas']['allRequestNumberData']) ?></td>
      </tr>
      <tr>
        <td id = 'chatAutomaticResponseLabel' class = 'tooltip'>自動返信応対件数
          <div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
          <?php }
            else { ?>
              <td><?php echo ($data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
          <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['automaticResponseData']['allAutomaticResponseNumberData']) ?></td>
      </tr>
      <tr>
        <td id = 'chatLinkClickTooltip' class = 'tooltip'>リンククリック件数
          <div class="questionBalloon questionBalloonPosition8s">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['linkDatas']['linkNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['linkDatas']['linkNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['linkDatas']['linkNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['linkDatas']['allLinkNumberData']) ?></td>
      </tr>
      <tr>
        <td id = 'chatRequestMannedTooltip' class = 'tooltip'>有人チャットリクエスト件数
          <div class="questionBalloon questionBalloonPosition8s">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <td><?php
          if($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)] !== "" && $data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)] !== ""
            && $data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)] !== "") {
            echo number_format($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)]+$data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)]+$data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
        <?php } } ?>
        <td><?php echo number_format($data['responseDatas']['allResponseNumberData']+$data['abandonRequestDatas']['allAbandonRequestNumberData']+$data['coherentDatas']['allDenialNumberData']) ?></td>
      </tr>
      <tr>
        <td id = 'chatResponseLabel'  class = 'tooltip'>チャット応対件数
          <div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
          <?php }
            else { ?>
              <td><?php echo ($data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['responseDatas']['allResponseNumberData']) ?></td>
      </tr>
      <tr>
        <td id = 'chatRequestAbandonTooltip' class = 'tooltip'>チャット放棄件数
          <div class="questionBalloon questionBalloonPosition8s">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['abandonRequestDatas']['allAbandonRequestNumberData']) ?></td>
      </tr>
      <tr>
        <td id = 'chatDenialLabel' class = 'tooltip'>チャット拒否件数
          <div class="questionBalloon questionBalloonPosition8s">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
          <?php }
            else { ?>
              <td><?php echo ($data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['coherentDatas']['allDenialNumberData']) ?></td>
      </tr>
      <tr>
        <td id = 'chatEffectivenessLabel' class = 'tooltip'>チャット有効件数
          <div class="questionBalloon questionBalloonPosition8s">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['coherentDatas']['effectivenessNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['coherentDatas']['effectivenessNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
          <?php }
            else { ?>
               <td><?php echo ($data['coherentDatas']['effectivenessNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['coherentDatas']['allEffectivenessNumberData']) ?></td>
      </tr>
      <tr>
        <td id = 'chatCVLabel' class = 'tooltip'>チャットCV件数
          <div class="questionBalloon questionBalloonPosition8s">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php for ($i = $start; $i <= $end; $i++) { ?>
          <?php if(is_int($data['coherentDatas']['cvNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
            <td><?php echo number_format($data['coherentDatas']['cvNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
          <?php }
          else { ?>
            <td><?php echo ($data['coherentDatas']['cvNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
          <?php } ?>
        <?php } ?>
        <td><?php echo number_format($data['coherentDatas']['allCVNumberData']) ?></td>
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
        <td id = 'chatAutomaticResponseRateLabel' class = 'tooltip'>自動返信応対率
          <div class="questionBalloon questionBalloonPosition7">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php
          for ($i = $start; $i <= $end; $i++) {
            if(is_numeric($data['automaticResponseData']['automaticResponseRate'][$type.'-'.sprintf("%02d",$i)])) {
              $checkData = ' %';
            }
            else {
              $checkData = '';
            } ?>
          <td><?php echo $data['automaticResponseData']['automaticResponseRate'][$type.'-'.sprintf("%02d",$i)].$checkData ?></td>
        <?php } ?>
        <?php
          if(is_numeric($data['automaticResponseData']['allAutomaticResponseRate'])) {
            $checkData = ' %';
          }
          else {
            $checkData = '';
          }
        ?>
      <td><?php echo $data['automaticResponseData']['allAutomaticResponseRate'].$checkData ?></td>
      </tr>
      <tr>
        <td id = 'chatResponseRateLabel' class = 'tooltip'>有人チャット応対率
          <div class="questionBalloon questionBalloonPosition7">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
          <?php
            for ($i = $start; $i <= $end; $i++) {
              if(is_numeric($data['responseDatas']['responseRate'][$type.'-'.sprintf("%02d",$i)])) {
                $checkData = ' %';
              }
              else {
                $checkData = '';
              } ?>
            <td><?php echo $data['responseDatas']['responseRate'][$type.'-'.sprintf("%02d",$i)].$checkData ?></td>
          <?php } ?>
        <?php
          if(is_numeric($data['responseDatas']['allResponseRate'])) {
            $checkData = ' %';
          }
          else {
            $checkData = '';
          }
        ?>
        <td><?php echo $data['responseDatas']['allResponseRate'].$checkData ?></td>
      </tr>
      <tr>
        <td id = 'chatEffectivenessResponseRateLabel' class = 'tooltip'>チャット有効率
          <div class="questionBalloon questionBalloonPosition7">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
        <?php
          for ($i = $start; $i <= $end; $i++) {
            if(is_numeric($data['responseDatas']['effectivenessRate'][$type.'-'.sprintf("%02d",$i)])) {
              $checkData = ' %';
            }
            else {
              $checkData = '';
            } ?>
          <td><?php echo $data['responseDatas']['effectivenessRate'][$type.'-'.sprintf("%02d",$i)].$checkData ?></td>
        <?php } ?>
        <?php
          if(is_numeric($data['responseDatas']['allEffectivenessRate'])) {
            $checkData = ' %';
          }
          else {
            $checkData = '';
          }
        ?>
      <td><?php echo $data['responseDatas']['allEffectivenessRate'].$checkData ?></td>
      </tr>

      <?php }


      else if($date == '時別') { ?>
        <tr>
          <td class = 'tooltip'>合計アクセス件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['accessDatas']['accessNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['accessDatas']['allAccessNumberData']) ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>ウィジェット表示件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['widgetDatas']['widgetNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['widgetDatas']['allWidgetNumberData']) ?></td>
        </tr>
        <tr>
        <td id="chatRequestLabel" class = 'tooltip'>チャットリクエスト件数
          <div class="questionBalloon questionBalloonPosition11">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['requestDatas']['requestNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['requestDatas']['allRequestNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseLabel' class = 'tooltip'>自動返信応対件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo number_format($data['automaticResponseData']['automaticResponseNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['automaticResponseData']['allAutomaticResponseNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatLinkClickTooltip' class = 'tooltip'>リンククリック件数
            <div class="questionBalloon questionBalloonPosition8s">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['linkDatas']['linkNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['linkDatas']['allLinkNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestMannedTooltip" class = 'tooltip'>有人チャットリクエスト件数
            <div class="questionBalloon questionBalloonPosition11">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['responseDatas']['responseNumberData'][sprintf("%02d",$i).':00']+$data['abandonRequestDatas']['abandonRequestNumberData'][sprintf("%02d",$i).':00']+$data['coherentDatas']['denialNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['responseDatas']['allResponseNumberData']+$data['abandonRequestDatas']['allAbandonRequestNumberData']+$data['coherentDatas']['allDenialNumberData']) ?></td>
        </tr>
        <tr>
        <td id = 'chatResponseLabel'  class = 'tooltip'>チャット応対件数
          <div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div>
        </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo number_format($data['responseDatas']['responseNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['responseDatas']['allResponseNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestAbandonTooltip" class = 'tooltip'>チャット放棄件数
            <div class="questionBalloon questionBalloonPosition11">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['abandonRequestDatas']['abandonRequestNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['abandonRequestDatas']['allAbandonRequestNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatDenialLabel' class = 'tooltip'>チャット拒否件数
            <div class="questionBalloon questionBalloonPosition8s">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['coherentDatas']['denialNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['coherentDatas']['allDenialNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessLabel' class = 'tooltip'>チャット有効件数
            <div class="questionBalloon questionBalloonPosition8s">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatCVLabel' class = 'tooltip'>チャットCV件数
            <div class="questionBalloon questionBalloonPosition8s">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['cvNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allCVNumberData'] ?></td>
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
          <td id = 'chatAutomaticResponseRateLabel' class = 'tooltip'>自動返信応対率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php
            for ($i = $start; $i <= $end; $i++) {
              if(is_numeric($data['automaticResponseData']['automaticResponseRate'][sprintf("%02d",$i).':00'])) {
                $checkData = ' %';
              }
              else {
                $checkData = '';
              } ?>
            <td><?php echo $data['automaticResponseData']['automaticResponseRate'][sprintf("%02d",$i).':00'].$checkData ?></td>
          <?php } ?>
          <?php
            if(is_numeric($data['automaticResponseData']['allAutomaticResponseRate'])) {
              $checkData = ' %';
            }
            else {
              $checkData = '';
            }
          ?>
        <td><?php echo $data['automaticResponseData']['allAutomaticResponseRate'].$checkData ?></td>
          </tr>
        <tr>
          <td id = 'chatResponseRateLabel' class = 'tooltip'>有人チャット応対率
             <div class="questionBalloon questionBalloonPosition7">
               <icon class="questionBtn">？</icon>
             </div>
          </td>
          <?php
            for ($i = $start; $i <= $end; $i++) {
              if(is_numeric($data['responseDatas']['responseRate'][sprintf("%02d",$i).':00'])) {
                $checkData = ' %';
              }
              else {
                $checkData = '';
              } ?>
            <td><?php echo $data['responseDatas']['responseRate'][sprintf("%02d",$i).':00'].$checkData ?></td>
          <?php } ?>
        <?php
          if(is_numeric($data['responseDatas']['allResponseRate'])) {
            $checkData = ' %';
          }
          else {
            $checkData = '';
          }
        ?>
        <td><?php echo $data['responseDatas']['allResponseRate'].$checkData ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessResponseRateLabel' class = 'tooltip'>チャット有効率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php
            for ($i = $start; $i <= $end; $i++) {
              if(is_numeric($data['responseDatas']['effectivenessRate'][sprintf("%02d",$i).':00'])) {
                $checkData = ' %';
              }
              else {
                $checkData = '';
              } ?>
            <td><?php echo $data['responseDatas']['effectivenessRate'][sprintf("%02d",$i).':00'].$checkData ?></td>
          <?php } ?>
          <?php
            if(is_numeric($data['responseDatas']['allEffectivenessRate'])) {
              $checkData = ' %';
            }
            else {
              $checkData = '';
            }
          ?>
          <td><?php echo $data['responseDatas']['allEffectivenessRate'].$checkData ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <?=$this->Form->create('statistics', ['action' => 'forChat']);?>
      <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
  </div>
</div>

