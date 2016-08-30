<?php echo $this->element('Customers/userAgentCheck') ?>
<?php echo $this->element('Histories/angularjs') ?>

<div id='history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainCtrl">

<div id='history_title'>
  <div class="fLeft"><?= $this->Html->image('history_g.png', array('alt' => '履歴一覧', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>履歴一覧</h1>
  <?php echo $this->Html->link(
      $this->Html->image('download.png', array('alt' => 'CSV出力', 'width'=>20, 'height'=>20)),
      'javascript:void(0)',
      array('escape' => false, 'class'=>'skyBlueBtn btn-shadow', 'id' => 'outputCSV'));
  ?>
</div>

<div id='history_menu' class="p20trl">
  <!-- 検索窓 -->
  <div class="fLeft">
    <?php
      if ($coreSettings[C_COMPANY_USE_CHAT]) :
      $checked = "";
      $class = "";
      if (strcmp($groupByChatChecked, 'false') !== 0) {
        $class = "checked";
        $checked = "checked=\"\"";
      }
    ?>
      <label for="g_chat" class="<?=$class?>">
        <input type="checkbox" id="g_chat" name="group_by_chat" <?=$checked?> />
        チャット履歴があるもののみ表示
      </label>
    <?php endif; ?>
    <?=$this->Form->create('History', ['action' => 'index']);?>
      <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
  </div>
  <div id="paging" class="fRight">
    <?php
      echo $this->Paginator->prev(
        $this->Html->image('paging.png', array('alt' => '前のページへ', 'width'=>25, 'height'=>25)),
        array('escape' => false, 'class' => 'btn-shadow greenBtn tr180'),
        null,
        array('class' => 'grayBtn tr180')
      );
    ?>
    <span style="width: auto!important;padding: 10px 0 0;"> <?php echo $this->Paginator->counter('{:page} / {:pages}'); ?> </span>
    <?php
      echo $this->Paginator->next(
        $this->Html->image('paging.png', array('alt' => '次のページへ', 'width'=>25, 'height'=>25)),
        array('escape' => false, 'class' => 'btn-shadow greenBtn'),
        null,
        array('escape' => false, 'class' => 'grayBtn')
      );
    ?>
  </div>
</div>

<div id='history_list' class="p20x">
  <?=$this->element('Histories/list')?>
  <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>
</div>

</div>
