<div id='tautomessages_idx' class="card-shadow">

<div id='tautomessages_add_title'>
	<div class="fLeft"><i class="fal fa-comments fa-2x"></i></div>
	<h1>トリガー設定（条件設定）更新</h1>
</div>

<div id='tautomessages_form' class="p20x">
  <?=$this->Form->create('TAutoMessage', ['url'=>'/TAutoMessages/edit', 'novalidate' => true, 'id'=>'TAutoMessageEntryForm', 'name'=>'TAutoMessageEntryForm'])?>
    <?= $this->element('TAutoMessages/entry'); ?>
  <?=$this->Form->end();?>
</div><!-- /m_widget_setting_form -->

</div>
