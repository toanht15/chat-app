<div id='statistic_idx' class="card-shadow">
  <?php if(!$coreSettings[C_COMPANY_USE_STATISTICS]): ?>
    <div id="modal" style="display: table; position: absolute; top:15px; left:15px; width: calc(100% - 30px); height: calc(100% - 30px); z-index: 4; background-color: rgba(0, 0, 0, 0.8);">
      <p style="color: #FFF; display: table-cell; vertical-align: middle; text-align: center;">こちらの機能はスタンダードプランからご利用いただけます。</p>
    </div>
  <?php endif; ?>
  <div id='statistic_title'>
    <div class="fLeft"><?= $this->Html->image('graph_g.png', array('alt' => 'チャット統計', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>チャット統計</h1>
  </div>

  <?php echo $this->element('Statistics/base'); ?>
</div>

