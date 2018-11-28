<?=$this->element('TLeadLists/script')?>
<?php if(!$coreSettings[C_COMPANY_USE_CHATBOT_SCENARIO]): ?>
  <div id="modal" style="display: table; position: absolute; top:15px; left:15px; width: calc(100% - 30px); height: calc(100% - 30px); z-index: 4; background-color: rgba(0, 0, 0, 0.8);">
    <p style="font-size: 15px; color: #FFF; display: table-cell; vertical-align: middle; text-align: center;">こちらの機能はシナリオオプションに加入することでご利用いただけます。</p>
  </div>
<?php endif; ?>
<div id='lead_list_idx' class="card-shadow">
  <div id='lead_title'>
    <div class="fLeft"><i class="fal fa-file-alt fa-2x"></i></div>
      <h1>リードリスト出力</h1>
  </div>
  <div id="lead_list_body">
    <?=$this->element('TLeadLists/detail')?>
  </div>
</div>
