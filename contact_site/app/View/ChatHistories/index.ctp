<?= $this->element('Customers/userAgentCheck') ?>
<?= $this->element('ChatHistories/angularjs') ?>
<?= $this->element('ChatHistories/script') ?>
<div id='chat_history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainController" >
  <div id='history_title'>
    <div class="fLeft"><i class="fal fa-history fa-2x"></i></div>
      <h1>チャット履歴</h1>
      <?= $this->Html->link(
        '履歴一覧ＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false,
              'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? " skyBlueBtn commontooltip" : " grayBtn commontooltip disabled"),
              'id' => 'outputCSV',
              'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
              'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "検索条件に該当するチャット履歴一覧をCSV出力します。" : "こちらの機能はスタンダードプランからご利用いただけます。",
              'data-balloon-position' => '50'
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
              'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "検索条件に該当するチャット内容をすべてCSV出力します。" : "こちらの機能はスタンダードプランからご利用いただけます。",
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