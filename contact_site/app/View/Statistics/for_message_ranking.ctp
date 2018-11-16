<div id='statistic_idx' class="card-shadow">
  <?php if(!$coreSettings[C_COMPANY_USE_STATISTICS]): ?>
    <div id="modal" style="display: table; position: absolute; top:15px; left:15px; width: calc(100% - 30px); height: calc(100% - 30px); z-index: 4; background-color: rgba(0, 0, 0, 0.8);">
      <p style="font-size: 15px; color: #FFF; display: table-cell; vertical-align: middle; text-align: center;">こちらの機能はスタンダードプランからご利用いただけます。</p>
    </div>
  <?php endif; ?>
  <div id='statistic_title'>
    <div class="fLeft"><i class="fal fa-chart-line fa-2x"></i></div>
    <h1>選択式メッセージランキング</h1>
  </div>

  <?php echo $this->element('Statistics/baseForMessageRanking'); ?>
</div>

