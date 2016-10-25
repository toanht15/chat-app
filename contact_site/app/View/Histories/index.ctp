  <?php echo $this->element('Customers/userAgentCheck') ?>
  <?php echo $this->element('Histories/angularjs') ?>

  <div id='history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainCtrl">

  <div id='history_title'>
    <div class="fLeft"><?= $this->Html->image('history_g.png', array('alt' => '履歴一覧', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>履歴一覧</h1>
    <?php echo $this->Html->link(
        '検索絞り込み',
        'javascript:void(0)',
        array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchRefine'));
    ?>
    <?php echo $this->Html->link(
        'ＣＳＶ出力',
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
      <?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => '/Histories']); ?>
        <?= $this->Form->input('datefilter',['label'=> false,'div' => false,'id' => 'dateperiod','name'=> 'datefilter']); ?>
        <div>
          <?= $this->Form->input('start_day',['label'=> false,'div' => false,'name'=> 'start_day','placeholder' => '開始日']); ?>
          <?= $this->Form->input('finish_day',['label'=> false,'div' => false,'name' => 'finish_day','placeholder' => '終了日']);?>
          <?= $this->Form->input('ip_address',['label'=>false,'div' => false,'placeholder' => 'ipアドレス']) ?>
        </div>
        <div>
          <?= $this->Form->input('company_name',['label'=>false,'div' => false,'placeholder' => '会社名']) ?>
          <?= $this->Form->input('customer_name',['label'=>false,'div' => false,'placeholder' => '顧客名']) ?>
          <?= $this->Form->input('telephone_number',['label'=>false,'div' => false,'placeholder' => '電話番号']) ?>
          <?= $this->Form->input('mail_address',['label'=>false,'div' => false,'placeholder' => 'メールアドレス']) ?>
        </div>
        <div>
          <?= $this->Form->button('検索',['label'=>false,'div' => false,'onclick' => 'searchRefine()']) ?>
        </div>
      <?= $this->Form->end(); ?>
    </div>
  <script type="text/javascript">


  </script>
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
