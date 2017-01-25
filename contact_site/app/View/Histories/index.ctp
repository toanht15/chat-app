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
      '絞り込み検索',
      'javascript:void(0)',
      array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchRefine','onclick' => 'openSearchRefine()'));
    ?>
    <span id="searchPeriod">検索期間</span>
    <?php //指定範囲のある検索
    if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
      <?php //モーダル画面から検索した場合
      if(isset($data['History']['period'])) {
        if(($data['History']['period']) == '全期間') { ?>
        <span id ='mainDatePeriod' name = 'datefilter' class='date'>全期間</span>
      <?php } else{ ?>
        <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['period']) ?> : <?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
        <?php } ?>
      <?php } ?>
      <?php //view側から検索した場合
      if(isset($data['History']['viewPeriod'])) { ?>
       <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['viewPeriod']) ?> : <?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
      <?php } ?>
    <?php } ?>
    <?php //全期間の検索
    if(empty($data['History']['start_day'])&&empty($data['History ']['finish_day'])) { ?>
      <span id ='mainDatePeriod' name = 'datefilter' class='date'>全期間</span>
    <?php } ?>

    <?php
      //条件クリアをした時や、最初に来て値が入っていない時
      if(empty($data['History']['start_day']) && empty($data['History']['finish_day'])){ ?>
        <span id="startDay"><?=date("Y/m/d") ?></span>
        <span id="finishDay"><?= date("Y/m/d") ?></span>
        <span id="companyStart"><?= h($data['History']['company_start_day']) ?></span>
      <?php }
        //全期間検索の場合
      else if($data['History']['start_day'] == $data['History']['company_start_day'] && $data['History']['finish_day'] == date("Y/m/d")) { ?>
        <span id="startDay"><?=date("Y/m/d") ?></span>
        <span id="finishDay"><?= date("Y/m/d") ?></span>
        <span id="companyStart"><?= h($data['History']['company_start_day']) ?></span>
        <span id="ip"><?= h($data['History']['ip_address']) ?></span>
        <span id="company"><?= h($data['History']['company_name']) ?></span>
        <span id="customer"><?= h($data['History']['customer_name']) ?></span>
        <span id="telephone"><?= h($data['History']['telephone_number']) ?></span>
        <span id="mail"><?= h($data['History']['mail_address']) ?></span>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
          <span id="responsible"><?= h($data['THistoryChatLog']['responsible_name']) ?></span>
          <span id="achievement"><?= h($data['THistoryChatLog']['achievement_flg']) ?></span>
          <span id="message"><?= h($data['THistoryChatLog']['message']) ?></span>
        <?php endif; ?>
       <?php }
       //それ以外の検索の場合
      else { ?>
        <span id="startDay"><?= h($data['History']['start_day']) ?></span>
        <span id="companyStart"><?= h($data['History']['company_start_day']) ?></span>
        <span id="finishDay"><?= h($data['History']['finish_day']) ?></span>
        <span id="ip"><?= h($data['History']['ip_address']) ?></span>
        <span id="company"><?= h($data['History']['company_name']) ?></span>
        <span id="customer"><?= h($data['History']['customer_name']) ?></span>
        <span id="telephone"><?= h($data['History']['telephone_number']) ?></span>
        <span id="mail"><?= h($data['History']['mail_address']) ?></span>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
          <span id="responsible"><?= h($data['THistoryChatLog']['responsible_name']) ?></span>
          <span id="achievement"><?= h($data['THistoryChatLog']['achievement_flg']) ?></span>
          <span id="message"><?= h($data['THistoryChatLog']['message']) ?></span>
        <?php endif; ?>
      <?php } ?>


    <?php
      $none = '';
      $seach_menu = 'seach_menu';
      //全期間の場合
      if(empty($data['History'])&&empty($data['THistoryChatLog'])){
        $none = 'none';
        $seach_menu='　';
      }
      //日程だけ検索の場合
      if(empty($data['History']['ip_address'])&&empty($data['History']['company_name'])
        &&empty($data['History']['customer_name'])&&empty($data['History']['telephone_number'])
        &&empty($data['History']['mail_address'])&&empty($data['THistoryChatLog']['responsible_name'])
        &&empty($data['THistoryChatLog']['achievement_flg'])&&empty($data['THistoryChatLog']['message'])){
        $none = 'none';
        $seach_menu='　';
      }
    ?>
    <div class=<?= $seach_menu; ?> id=<?= $none ?>>
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
