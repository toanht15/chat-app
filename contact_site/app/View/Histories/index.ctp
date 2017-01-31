<?= $this->element('Customers/userAgentCheck') ?>
<?= $this->element('Histories/angularjs') ?>
<?= $this->element('Histories/script') ?>

<div id='history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainCtrl">

  <div id='history_title'>
    <div class="fLeft"><?= $this->Html->image('history_g.png', array('alt' => '履歴一覧', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
      <h1>履歴一覧</h1>
      <?= $this->Html->link(
        'ＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false, 'class'=>'skyBlueBtn btn-shadow', 'id' => 'outputCSV'));
      ?>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>


      <?= $this->Html->link(
        'チャットＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false, 'class'=>'skyBlueBtn btn-shadow', 'id' => 'outputChat'));
      ?>
    <?php endif; ?>
  </div>

  <div id='history_menu' class="p20trl">
    <div id="paging" class="fRight">
      <?=
          $this->Paginator->prev(
          $this->Html->image('paging.png', array('alt' => '前のページへ', 'width'=>25, 'height'=>25)),
          array('escape' => false, 'class' => 'btn-shadow greenBtn tr180'),
          null,
          array('class' => 'grayBtn tr180')
        );
      ?>
      <span style="width: auto!important;padding: 10px 0 0;"> <?= $this->Paginator->counter('{:page} / {:pages}'); ?> </span>
      <?=
          $this->Paginator->next(
          $this->Html->image('paging.png', array('alt' => '次のページへ', 'width'=>25, 'height'=>25)),
          array('escape' => false, 'class' => 'btn-shadow greenBtn'),
          null,
          array('escape' => false, 'class' => 'grayBtn')
        );
      ?>
    </div>

    <?= $this->Html->link(
      '高度な条件',
      'javascript:void(0)',
      array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchRefine','onclick' => 'openSearchRefine()'));
    ?>
    <?= $this->Html->link(
      '検索',
      'javascript:void(0)',
      array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchInfo','onclick' => 'searchCustomerInfo()'));
    ?>
    <?= $this->Html->link(
      '条件クリア',
      'javascript:void(0)',
      array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'sessionClear','onclick' => 'sessionClear()'));
    ?>
    <span id="searchPeriod">検索期間</span>
    <?php //検索をした時の表示
      if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
        <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['period']) ?> : <?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
    <?php } ?>
    <?php //セッションをクリアしたときの表示
      if(empty($data['History']['start_day'])&&empty($data['History ']['finish_day'])) { ?>
        <span id ='mainDatePeriod' name = 'datefilter' class='date'>全期間 : <?= h($itemList['start']) ?>-<?= h($itemList['finish']) ?></span>
    <?php } ?>

    <div class= 'seach_menu'>
      <label class='searchConditions'>検索条件</label>
      <ul>
        <span class="dammy">　</span>
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
        <?php if(!empty($data['THistoryChatLog']['responsible_name'])) { ?>
          <li>
            <label>担当者</label>
            <span class="value"><?= h($data['THistoryChatLog']['responsible_name']) ?></span>
          </li>
        <?php } ?>
        <?php if(isset($data['THistoryChatLog']['achievement_flg']) && $data['THistoryChatLog']['achievement_flg'] !== "" ) { ?>
          <li>
            <label>成果</label>
            <span class="value"><?= $achievementType[h($data['THistoryChatLog']['achievement_flg'])] ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['THistoryChatLog']['message'])) { ?>
          <li>
            <label>ﾁｬｯﾄ内容</label>
            <span class="value"><?= h($data['THistoryChatLog']['message']) ?></span>
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
        <label for="g_chat" class="pointer <?=$class?>">
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
