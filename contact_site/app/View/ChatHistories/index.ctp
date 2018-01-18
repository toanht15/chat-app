<?= $this->element('Customers/userAgentCheck') ?>
<?= $this->element('ChatHistories/angularjs') ?>
<?= $this->element('ChatHistories/script') ?>
<div id='chat_history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainController" >
  <div id='history_title'>
    <div class="fLeft"><?= $this->Html->image('history_g.png', array('alt' => '履歴一覧', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
      <h1>履歴一覧</h1>
      <?= $this->Html->link(
        '履歴一覧ＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false,
              'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? " skyBlueBtn commontooltip" : " grayBtn commontooltip disabled"),
              'id' => 'outputCSV',
              'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
              'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "検索条件に該当するチャット履歴<br>一覧をCSV出力します。" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
              'data-balloon-position' => '75'
          ));
      ?>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
      <?= $this->Html->link(
        'チャット内容ＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false,
              'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? " skyBlueBtn commontooltip" : " grayBtn commontooltip disabled"),
              'id' => 'outputChat',
              'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
              'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "検索条件に該当するチャット内容を<br>すべてCSV出力します。" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
        ));
      ?>
    <?php endif; ?>
  </div>
  <div id = "history_list_side" style = "height:calc(100vh - 142px);">
    <div id = "history_body_side" style = "width:100%; padding:0 20px; display:none;">
      <?=$this->element('ChatHistories/list')?>
      <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>
    </div>
  </div>