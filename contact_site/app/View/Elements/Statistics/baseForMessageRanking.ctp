<?= $this->element('Statistics/datepicker') ?>
<?= $this->element('Statistics/script') ?>

<div id="sincloApp">

  <div id='statistic_menu' class="p20x">
    <!-- /* 対象期間選択エリア */ -->
    <condition-bar>
      <left-parts>
        <span id = "searchPeriod">対象期間：</span>
        <?= $this->Form->create('Statistics'); ?>

        <?= $this->Form->input('dateType', array('type'=>'select','name' => 'dateFormat','onChange' => 'timeChangeForMessageRanking()',
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
        <a href="#" id="outputMessageRankingCSV" class="btn-shadow blueBtn">CSV出力</a>
      </right-parts>
    </condition-bar>
    <!-- /* 対象期間選択エリア */ -->
  </div><!-- #statistic_menu -->

  <div id='statistics_content' class="p20x" style="visibility:hidden;">
    <!-- /* テーブル表示エリア */ -->

    <table id="statistics_table" class="display" cellspacing="0" width="100%">
      <thead>
      <?php if($date == '月別') {
        $start = 1;
        $end = 12; ?>
        <tr>
          <th class="thMinWidthMessageRanking">メッセージ</th>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <th class="thMinWidthDaylyMessageRanking" style="padding-left: 13px; padding-right: 13px"><?= $i.'月' ?></th>
          <?php } ?>
          <th class="thMinWidthDaylyMessageRanking" style="padding-left: 16px; padding-right: 16px">合計</th>
        </tr>
      <?php } ?>
      <?php if($date == '日別') {
        $start = 1;
        $end = $daylyEndDate; ?>
        <tr>
          <th class="thMinWidthMessageRanking">メッセージ</th>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <th class="thMinWidthDaylyMessageRanking" style="padding-left: 5px; padding-right: 5px"><?= $i.'日' ?></th>
          <?php } ?>
          <th class="thMinWidthDaylyMessageRanking">合計</th>
        </tr>
      <?php } ?>
      <?php if($date == '時別') {
        $start = 0;
        $end = 23; ?>
        <tr>
          <th class="thMinWidthMessageRanking">メッセージ</th>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <th class = "thMinWidthDaylyMessageRanking" style="padding-left: 5px; padding-right: 5px"><?= sprintf("%02d",$i).'-'.sprintf("%02d",$i+1) ?></th>
          <?php } ?>
          <th class="thMinWidthDaylyMessageRanking">合計</th>
        </tr>
      <?php } ?>
      </thead>
      <tbody>
      <?php if($date == '日別' or $date == '月別' or $date == '時別') { ?>
        <?php foreach ($messageData['data'] as $message => $data): ?>
          <tr>
            <td id="chatRequestLabel" class="autoMessage tooltip"><?php echo $message ?></td>
            <?php for ($i = $start; $i <= $end; $i++): ?>
              <?php if (isset($data[$i])): ?>
                <td><?php echo $data[$i] ?></td>
              <?php else: ?>
                <td>0</td>
            <?php endif; ?>
            <?php endfor; ?>
            <td><?php echo isset($data['sum']) ? $data['sum'] : '0'; ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td id="chatRequestLabel" class="autoMessage tooltip">合計</td>
          <?php for ($i = $start; $i <= $end; $i++): ?>
            <?php if (isset($messageData['sum'][$i])): ?>
              <td><?php echo $messageData['sum'][$i] ?></td>
            <?php else: ?>
              <td>0</td>
            <?php endif; ?>
          <?php endfor; ?>
          <td><?php echo isset($messageData['sum']['sum']) ? $messageData['sum']['sum'] : '0'; ?></td>
        </tr>
      <?php }


      else if($date == '時別dd') { ?>
        <tr>
          <td class = 'autoMessage tooltip'>資料請求</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['accessDatas']['accessNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['accessDatas']['allAccessNumberData']) ?></td>
        </tr>
        <tr>
          <td class = 'autoMessage tooltip'>導入事例が知りたい</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['widgetDatas']['widgetNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['widgetDatas']['allWidgetNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip'>sinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['requestDatas']['requestNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['requestDatas']['allRequestNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseLabel' class = 'autoMessage tooltip'>無料トライアルに申し込む
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['automaticResponseData']['automaticResponseNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['automaticResponseData']['allAutomaticResponseNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatLinkClickTooltip' class = 'autoMessage tooltip'>sinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['linkDatas']['linkNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['linkDatas']['allLinkNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >合計</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['linkDatas']['linkNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['linkDatas']['allLinkNumberData']) ?></td>
        </tr>

      <?php } ?>
      </tbody>
    </table>
    <?=$this->Form->create('statistics', ['action' => 'forMessageRanking']);?>
    <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
  </div>
</div>

