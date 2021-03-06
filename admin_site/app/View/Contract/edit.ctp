<?= $this->Html->script(C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT."/socket.io/socket.io.js"); ?>
<?= $this->element('Contract/inputCommonScript'); ?>
<?= $this->element('Contract/editScript'); ?>
<div id='contract_idx'>
  <div id='contract_add_title'>
    <div class="fLeft"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></div>
    <h1>企業設定更新</h1>
  </div>

  <div id='contract_form' class="p20x">
    <?= $this->Form->create('Contract', array('type' => 'post', 'url' => '/Contract/edit/'.$companyId)); ?>
      <div class="form01">
        <section>
          <?= $this->Form->input('id', array('type' => 'hidden')); ?>
          <ul class='formArea'>
            <?= $this->element('Contract/entry'); ?>
            <!-- /* jsファイル作成 */ -->
            <section>
            <?=$this->Form->hidden('id')?>
              <div class="button">
                <?= $this->Html->link('一覧',['controller'=>'Contract', 'action' => 'index'],['escape' => false, 'id' => 'searchRefine','class' => 'normal_btn']); ?>
                <?= $this->Html->link('更新', 'javascript:void(0)', ['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'saveEdit()']); ?>
                <?= $this->Html->link('削除', 'javascript:void(0)', ['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => "remoteDeleteCompany('$companyId','$companyKey')"]); ?>
              </div>
            </section>
          </ul>
        </section>
      </div>
    <?= $this->Form->end(); ?>
  </div>
</div>