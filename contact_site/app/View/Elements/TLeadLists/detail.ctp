<div style="overflow: hidden; margin-left: 30px">
  <div style="display: flex; margin-top: 30px">
    <span style="display: flex; width: 150px; align-items:center;">検索期間：</span>
    <select>
      <option selected>過去一週間：2018/10/31-2018/11/06</option>
    </select>
  </div>
  <div style="display: flex; margin-top: 30px">
    <span style="display: flex; width: 150px; align-items:center;">リードリスト名：</span>
    <select>
      <option selected>リードリストを選択してください</option>
      <option>お客様情報①</option>
      <option>お客様情報②</option>
    </select>
  </div>
  <div>
    <?= $this->Html->link(
      'ＣＳＶ出力',
      'javascript:void(0)',
      array('escape' => false,
        'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? " skyBlueBtn commontooltip" : " grayBtn commontooltip disabled"),
        'id' => 'outputCSV',
        'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
        'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "検索条件に該当するチャット履歴一覧をCSV出力します。" : "こちらの機能はスタンダードプランからご利用いただけます。",
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