<?php echo $this->element('Customers/userAgentCheck') ?>
<?php echo $this->element('Histories/angularjs') ?>
<?php echo $this->element('Histories/script') ?>

<div id='history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainCtrl">

  <div id='history_title'>
    <div class="fLeft"><?= $this->Html->image('history_g.png', array('alt' => '履歴一覧', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
      <h1>履歴一覧</h1>
      <?php echo $this->Html->link(
        'ＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false, 'class'=>'skyBlueBtn btn-shadow', 'id' => 'outputCSV'));
      ?>
      <?php echo $this->Html->link(
        'チャットＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false, 'class'=>'skyBlueBtn btn-shadow', 'id' => 'outputChat'));
      ?>
  </div>

  <div id='history_menu' class="p20trl">
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

    <?php echo $this->Html->link(
      '絞り込み検索',
      'javascript:void(0)',
      array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchRefine','onclick' => 'openSearchRefine()'));
    ?>
      <span><?= $this->Form->input('datefilter',['label'=> false,'div' => false,'id' => 'mainDatePeriod','name'=> 'datefilter']); ?></span>
    <div class='seach_menu'>
      <label class='searchConditions'>検索条件</label>
      <ul>
        <?php if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
        <li>
          <label>日付</label>
          <span class="value"><?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
        </li>
        <?php } ?>
        <?php if(!empty($data['History']['ip_address'])) { ?>
          <li>
            <label>IPｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['History']['ip_address']) ?></span>
          </li>
        <?php } ?>　
        <?php if(!empty($data['History']['company_name'])) { ?>
          <li>
            <label>会社名</label>
            <span class="value"><?= h($data['History']['company_name']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['customer_name'])) { ?>
          <li>
            <label class="label">名前</label>
            <span class="value"><?= h($data['History']['customer_name']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['telephone_number'])) { ?>
          <li>
            <label>電話番号</label>
            <span class="value"><?= h($data['History']['telephone_number']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['mail_address'])) { ?>
          <li>
            <label>ﾒｰﾙｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['History']['mail_address']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['responsible_name'])) { ?>
          <li>
            <label>担当者</label>
            <span class="value"><?= h($data['History']['responsible_name']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['message'])) { ?>
          <li>
            <label>ﾁｬｯﾄ内容</label>
            <span class="value"><?= h($data['History']['message']) ?></span>
          </li>
        <?php } ?>
      </ul>
    </div>
    <!-- 検索窓 -->
    <div class='fLeft'>
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
      <?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => '/Histories']); ?>
    </div>
  </div>

  <div id='history_list' class="p20x">
    <?=$this->element('Histories/list')?>
    <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>
  </div>
</div>
