<?= $this->Html->script(C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT."/socket.io/socket.io.js"); ?>
<?= $this->element('Contract/inputCommonScript'); ?>
<?= $this->element('MailTemplateSettings/editScript'); ?>
<div id='contract_idx'>
  <div id='contract_add_title'>
    <div class="fLeft"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></div>
    <h1>メール設定更新</h1>
  </div>

  <div id='contract_form' class="p20x">
    <?= $this->Form->create('MailTemplateSettings', array('type' => 'post', 'url' => '/MailTemplateSettings/edit/'.$id.'/'.$value)); ?>
      <div class="form01">
        <section>
          <?= $this->Form->input('id', array('type' => 'hidden')); ?>
          <ul class='formArea'>
            <?= $this->element('MailTemplateSettings/entry'); ?>
            <!-- /* jsファイル作成 */ -->
            <section>
            <?=$this->Form->hidden('id')?>
              <div class="button">
                <?= $this->Html->link('一覧',['controller'=>'MailTemplateSettings', 'action' => 'index'],['escape' => false, 'id' => 'searchRefine','class' => 'normal_btn']); ?>
                <?= $this->Html->link('更新', 'javascript:void(0)', ['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'saveEdit()']); ?>
                <?= $this->Html->link('削除', 'javascript:void(0)', ['escape' => false, 'id' => 'searchRefine','class' => 'action_btn delete_btn','style' => 'display:none;','onclick' => "remoteDeleteCompany('$id','$value')"]); ?>
              </div>
            </section>
          </ul>
        </section>
      </div>
    <?= $this->Form->end(); ?>
  </div>
</div>