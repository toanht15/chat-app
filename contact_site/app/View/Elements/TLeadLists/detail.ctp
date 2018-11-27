<div style="overflow: hidden; margin-left: 30px">
  <div style="display: flex; margin-top: 30px">
    <span style="display: flex; width: 150px; align-items:center;">検索期間：</span>
    <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['period']) ?> : <?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
  </div>
  <div style="display: flex; margin-top: 30px">
    <span style="display: flex; width: 150px; align-items:center;">リードリスト名：</span>
    <form id="listForm" method="post">
      <?php
      echo $this->Form->input('selectList', [
        'default' => 'none',
        'type' => 'select',
        'options' => $leadList,
        'div' => false,
        'label' => false
      ]);
      ?>
      <input type="hidden" id="startDateForm" name="data[startDate]" value="">
      <input type="hidden" id="endDateForm" name="data[endDate]" value="">
    </form>
  </div>
  <div>
    <?= $this->Html->link(
      'ＣＳＶ出力',
      'javascript:void(0)',
      array('escape' => false,
        'class'=>'btn-shadow grayBtn commontooltip disabled',
        'id' => 'outputCSV',
        'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
        'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "選択したリードリストをCSV出力します。（すべてのリスト）を選択した場合、各リードリストのCSVファイルをまとめzip形式で出力します。" : "こちらの機能はスタンダードプランからご利用いただけます。",
        'style' => [
          'display: flex;',
          'justify-content: center;',
          'align-items: center;',
          'width: 100px;',
          'height: 30px;',
          'margin: 30px 0px 30px;'
        ]
      ));
    ?>
  </div>
</div>
