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
    <?php //モーダル画面の検索
    if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
      <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?>　　</span>
    <?php } ?>
    <?php //view側の検索
    if(!empty($data['start_day'])||!empty($data['finish_day'])) { ?>
      <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['start_day']) ?>-<?= h($data['finish_day']) ?>　　</span>
    <?php } ?>
    <?php //全期間の検索
    if(empty($data['History']['start_day'])&&empty($data['History ']['finish_day'])
    &&empty($data['start_day'])&&empty($data['finish_day'])) { ?>
      <span id ='mainDatePeriod' name = 'datefilter'>全期間</span>
    <?php } ?>
    <span id="startDay"></span>
    <span id="finishDay"></span>
    <?php //モーダル画面の検索ワード
      if(!empty($data['History']['ip_address'])||!empty($data['History']['company_name'])
      ||!empty($data['History']['customer_name'])||!empty($data['History']['telephone_number'])
      ||!empty($data['History']['mail_address'])||!empty($data['History']['responsible_name'])
      ||!empty($data['History']['message'])) {
    ?>
      <span id="ip"><?= h($data['History']['ip_address']) ?></span>
      <span id="company"><?= h($data['History']['company_name']) ?></span>
      <span id="customer"><?= h($data['History']['customer_name']) ?></span>
      <span id="telephone"><?= h($data['History']['telephone_number']) ?></span>
      <span id="mail"><?= h($data['History']['mail_address']) ?></span>
      <span id="responsible"><?= h($data['History']['responsible_name']) ?></span>
      <span id="message"><?= h($data['History']['message']) ?></span>
    <?php } ?>
    <?php //view側の検索ワード
      if(!empty($data['ip_address'])||!empty($data['company_name'])
      ||!empty($data['customer_name'])||!empty($data['telephone_number'])
      ||!empty($data['mail_address'])||!empty($data['responsible_name'])
      ||!empty($data['message'])) {
    ?>
      <span id="ip"><?= h($data['ip_address']) ?></span>
      <span id="company"><?= h($data['company_name']) ?></span>
      <span id="customer"><?= h($data['customer_name']) ?></span>
      <span id="telephone"><?= h($data['telephone_number']) ?></span>
      <span id="mail"><?= h($data['mail_address']) ?></span>
      <span id="responsible"><?= h($data['responsible_name']) ?></span>
      <span id="message"><?= h($data['message']) ?></span>
    <?php } ?>

    <div class='seach_menu'>
      <label class='searchConditions'>検索条件</label>
      <ul>
        <span class="dammy">　</span>
        <?php if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
          <li>
            <label>日付</label>
            <span class="value"><?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
          </li>
        <?php } ?>
        <?php //view側で検索した場合
        if(!empty($data['start_day'])||!empty($data['finish_day'])) { ?>
          <li>
            <label>日付</label>
            <span class="value"><?= h($data['start_day']) ?>-<?= h($data['finish_day']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['ip_address'])) { ?>
          <li>
            <label>IPｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['History']['ip_address']) ?></span>
          </li>
        <?php } ?>
        <?php //view側で検索した場合
         if(!empty($data['ip_address'])) { ?>
          <li>
            <label>IPｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['ip_address']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['company_name'])) { ?>
          <li>
            <label>会社名</label>
            <span class="value"><?= h($data['History']['company_name']) ?></span>
          </li>
        <?php } ?>
        <?php //view側で検索した場合
        if(!empty($data['company_name'])) { ?>
          <li>
            <label>会社名</label>
            <span class="value"><?= h($data['company_name']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['customer_name'])) { ?>
          <li>
            <label class="label">名前</label>
            <span class="value"><?= h($data['History']['customer_name']) ?></span>
          </li>
        <?php } ?>
        <?php //view側で検索した場合
        if(!empty($data['customer_name'])) { ?>
          <li>
            <label>名前</label>
            <span class="value"><?= h($data['customer_name']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['telephone_number'])) { ?>
          <li>
            <label>電話番号</label>
            <span class="value"><?= h($data['History']['telephone_number']) ?></span>
          </li>
        <?php } ?>
        <?php //view側で検索した場合
        if(!empty($data['telephone_number'])) { ?>
          <li>
            <label>電話番号</label>
            <span class="value"><?= h($data['telephone_number']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['mail_address'])) { ?>
          <li>
            <label>ﾒｰﾙｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['History']['mail_address']) ?></span>
          </li>
        <?php } ?>
        <?php //view側で検索した場合
        if(!empty($data['mail_address'])) { ?>
          <li>
            <label>ﾒｰﾙｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['mail_address']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['responsible_name'])) { ?>
          <li>
            <label>担当者</label>
            <span class="value"><?= h($data['History']['responsible_name']) ?></span>
          </li>
        <?php } ?>
        <?php //view側で検索した場合
        if(!empty($data['responsible_name'])) { ?>
          <li>
            <label>ﾒｰﾙｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['responsible_name']) ?></span>
          </li>
        <?php } ?>
        <?php if(!empty($data['History']['message'])) { ?>
          <li>
            <label>ﾁｬｯﾄ内容</label>
            <span class="value"><?= h($data['History']['message']) ?></span>
          </li>
        <?php } ?>
        <?php //view側で検索した場合
        if(!empty($data['message'])) { ?>
          <li>
            <label>ﾒｰﾙｱﾄﾞﾚｽ</label>
            <span class="value"><?= h($data['message']) ?></span>
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
