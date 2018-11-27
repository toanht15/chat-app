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
        <?php foreach ($messageData['data'] as $message => $data): ?>
          <tr>
            <td id="chatRequestLabel" class="autoMessage tooltip"><?php echo $message ?></td>
            <?php for ($i = $start; $i <= $end; $i++): ?>
              <?php if (isset($data[$i])): ?>
                <td><?php echo number_format($data[$i]) ?></td>
              <?php else: ?>
                <td>0</td>
            <?php endif; ?>
            <?php endfor; ?>
            <td><?php echo isset($data['sum']) ? number_format($data['sum']) : '0'; ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td id="chatRequestLabel" class="autoMessage tooltip">合計</td>
          <?php for ($i = $start; $i <= $end; $i++): ?>
            <?php if (isset($messageData['sum'][$i])): ?>
              <td><?php echo number_format($messageData['sum'][$i]) ?></td>
            <?php else: ?>
              <td>0</td>
            <?php endif; ?>
          <?php endfor; ?>
          <td><?php echo isset($messageData['sum']['sum']) ? number_format($messageData['sum']['sum']) : '0'; ?></td>
        </tr>

      </tbody>
    </table>
    <?=$this->Form->create('statistics', ['action' => 'forMessageRanking']);?>
    <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
  </div>
</div>

