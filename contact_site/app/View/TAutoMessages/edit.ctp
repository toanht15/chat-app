<div id='tautomessages_idx' class="card-shadow">

<div id='tautomessages_add_title'>
	<div class="fLeft"><?= $this->Html->image('auto_message_g.png', array('alt' => 'オートメッセージ設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>オートメッセージ設定更新</h1>
</div>

<div id='tautomessages_form' class="p20x">
  <?=$this->Form->create('TAutoMessage', ['url'=>'/TAutoMessages/edit', 'novalidate' => true, 'id'=>'TAutoMessageEntryForm', 'name'=>'TAutoMessageEntryForm'])?>
    <?= $this->element('TAutoMessages/entry'); ?>
  <?=$this->Form->end();?>
</div><!-- /m_widget_setting_form -->

</div>
