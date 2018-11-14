<script>
  $(function() {
    $('#lead_select').change(function () {
      var val = $(this).val();
      var target = $('#outputCSV');
      if(val === "none"){
        target.removeClass("skyBlueBtn");
        target.addClass("grayBtn");
        target.addClass("disabled");
      } else {
        target.addClass("skyBlueBtn");
        target.removeClass("grayBtn");
        target.removeClass("disabled");
      }
    });
  });
</script>
<div style="overflow: hidden; margin-left: 30px">
  <div style="display: flex; margin-top: 30px">
    <span style="display: flex; width: 150px; align-items:center;">検索期間：</span>
    <select>
      <option selected>過去一週間：2018/10/25-2018/10/31</option>
    </select>
  </div>
  <div style="display: flex; margin-top: 30px">
    <span style="display: flex; width: 150px; align-items:center;">リードリスト名：</span>
    <select id="lead_select">
      <option value="none" selected>リードリストを選択してください</option>
      <option>お客様情報①</option>
      <option>お客様情報②</option>
      <option>（すべてのリスト）</option>
    </select>
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