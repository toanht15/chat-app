<div style="overflow: hidden; margin-left: 30px">
  <div style="display: flex; margin-top: 30px">
    <span style="display: flex; width: 150px; align-items:center;">検索期間：</span>
    <form>
      <input type="hidden" id ='mainDatePeriod' name = 'datefilter'>過去一週間 : 2018/11/20-2018/11/26</input>
    </form>
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