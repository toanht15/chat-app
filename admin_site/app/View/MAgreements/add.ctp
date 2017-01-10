<?= $this->element('MAgreements/addScript'); ?>

<div id='agreement_idx'>
  <div id='agreement_add_title'>
    <div class="fLeft"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></div>
    <h1>契約登録</h1>
  </div>
  <div id='agreement_form' class="p20x">
    <?= $this->Form->create('MAgreement', array('type' => 'post')); ?>
      <div class="form01">
        <section>
          <?= $this->Form->input('id', array('type' => 'hidden')); ?>
          <ul class='formArea'>
            <?= $this->element('MAgreements/entry'); ?>
            <!-- /* jsファイル作成 */ -->
            <section>
              <div class="button">
                <?= $this->Html->link('一覧',['controller'=>'MAgreements', 'action' => 'index'],['escape' => false, 'id' => 'searchRefine','class' => 'normal_btn']); ?>
                <?= $this->Html->link('登録', 'javascript:void(0)', ['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'saveAct()']); ?>
              </div>
            </section>
          </ul>
        </section>
      </div>
    <?= $this->Form->end(); ?>
  </div>
</div>
