<?= $this->element('Customers/userAgentCheck') ?>
<?= $this->element('Histories/angularjs') ?>
<?= $this->element('Histories/script') ?>
<div id='history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainController">

  <div id='history_title'>
    <div class="fLeft"><i class="fal fa-history fa-2x"></i></div>
      <h1>アクセス履歴</h1>
      <?= $this->Html->link(
        'ＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false,
              'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? " skyBlueBtn" : " grayBtn disabled commontooltip"),
              'id' => 'outputCSV',
              'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
              'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
              'data-balloon-position' => '75'
          ));
      ?>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>


      <?= $this->Html->link(
        'チャットＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false,
              'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? " skyBlueBtn" : " grayBtn disabled commontooltip"),
              'id' => 'outputChat',
              'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
              'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
        ));
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
      '高度な検索',
      'javascript:void(0)',
      array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchRefine','onclick' => 'openSearchRefine()'));
    ?>
    <span id="searchPeriod">検索期間</span>
    <?php
      //検索条件表示：非表示
      $noseach_menu = '';
      $seach_menu = 'seach_menu';
    ?>
    <?php //検索をした時の表示
      if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
        <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['period']) ?> : <?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
    <?php } ?>
    <?php //セッションをクリアしたときの表示(履歴一覧ボタンを押下した時)
      if(empty($data['History']['start_day'])&&empty($data['History ']['finish_day'])) { ?>
        <span id ='mainDatePeriod' name = 'datefilter' class='date'>過去一ヵ月間 : <?= h($historySearchConditions['start_day']) ?>-<?= h($historySearchConditions['finish_day']) ?></span>
    <?php } ?>
    <?php
        if(
          empty($data['History']['ip_address'])&&empty($data['CustomData'])&&empty($data['THistoryChatLog']['responsible_name'])
          &&($data['THistoryChatLog']['achievement_flg'] === "")
          &&empty($data['THistoryChatLog']['message'])){
          $noseach_menu = 'noseach_menu';
          $seach_menu='　';
        }
    ?>

    <div class=<?= $seach_menu; ?> id=<?= $noseach_menu ?>>
      <label class='searchConditions'>検索条件</label>
      <ul ng-non-bindable>
        <?php if(!empty($data['History']['ip_address'])) { ?>
          <li>
            <label>IPｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['History']['ip_address']) ?></span>
          </li>
        <?php } ?>
        <?php
      if(!empty($data['CustomData'])) {
        foreach($data['CustomData'] as $key => $value) {
          if(!empty($value)) { ?>
          <li>
            <label><?= $key ?></label>
            <span class="value"><?= h($value) ?></span>
          </li>
        <?php } } } ?>
        <?php if(!empty($data['THistoryChatLog']['responsible_name'])) { ?>
          <li>
            <label>担当者</label>
            <span class="value"><?= h($data['THistoryChatLog']['responsible_name']) ?></span>
          </li>
        <?php } ?>
        <?php if(isset($data['THistoryChatLog']['achievement_flg']) && ($data['THistoryChatLog']['achievement_flg'] !== "" || $data['THistoryChatLog']['achievement_flg'] === 0)) { ?>
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

        <?= $this->Html->link(
          '条件クリア',
          'javascript:void(0)',
          ['escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'sessionClear','onclick' => 'sessionClear();loading.load.start();']);
        ?>
      </ul>
    </div>
    <!-- 検索窓 -->
    <div class='fLeft'>
      <?php
        if ($coreSettings[C_COMPANY_USE_CHAT]) :
        $checked = "";
        $class = "checked";
        if (strcmp($groupByChatChecked, 'false') !== 0) {
          $class = "";
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
