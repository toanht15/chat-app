<?= $this->Html->script(C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT."/socket.io/socket.io.js"); ?>
<?= $this->element('Contract/inputCommonScript'); ?>
<?= $this->element('MailTemplateSettings/addScript'); ?>

<div id='contract_idx'>
  <div id='contract_add_title'>
    <div class="fLeft"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></div>
    <h1>メール登録</h1>
  </div>
  <div id='contract_form' class="p20x">
    <?php if(!empty($overLimitMessage)): ?>
    <span style="margin-top: 5em;"><?= $overLimitMessage ?></span>
    <?php else: ?>
    <?= $this->Form->create('MailTemplateSettings', array('type' => 'post', 'url' => '/MailTemplateSettings/add')); ?>
      <div class="form01">
        <section>
          <ul class='formArea'>
            <?= $this->element('MailTemplateSettings/entry'); ?>
            <!-- /* jsファイル作成 */ -->
            <section>
              <div class="button">
                <?= $this->Html->link('一覧',['controller'=>'MailTemplateSettings', 'action' => 'index'],['escape' => false, 'id' => 'searchRefine','class' => 'normal_btn']); ?>
                <?= $this->Html->link('登録', 'javascript:void(0)', ['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'saveAct()']); ?>
              </div>
            </section>
          </ul>
        </section>
      </div>
    <?= $this->Form->end(); ?>
    <?php endif; ?>
  </div>
</div>
