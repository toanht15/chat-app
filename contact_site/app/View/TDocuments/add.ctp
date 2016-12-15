<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TDocuments/script'); ?>

<div id='tdocument_idx' class="card-shadow">
  <div id='tdocument_add_title'>
    <div class="fLeft"><?= $this->Html->image('auto_message_g.png', array('alt' => '資料設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>資料設定</h1>
  </div>
  <div id='tdocument_form' class="p20x">
    <!-- 登録フォーム -->
    <?=$this->Form->create('TDocument', ['id'=>'TDocumentEntryForm', 'type' => 'file'])?>
      <?php echo $this->element('TDocuments/entry'); ?>
    <?=$this->Form->end();?>
    <!-- タグ登録フォーム -->
    <?=$this->Form->create('MDocumentTag', ['url'=>['controller' =>'TDocuments', 'action'=>'addTag']]) ?>
      <?= $this->Form->input('name', ['type' => 'hidden']) ?>
    <?=$this->Form->end();?>
  </div>
</div>