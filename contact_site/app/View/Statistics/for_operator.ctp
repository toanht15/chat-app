<div id='statistic_idx' class="card-shadow">

  <div id='statistic_title'>
    <div class="fLeft"><?= $this->Html->image('graph_g.png', array('alt' => 'チャット統計', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>オペレータ統計</h1>
  </div>

  <?php echo $this->element('Statistics/baseForOperator'); ?>
</div>

