<div id='tchatbotscenario_idx' class="card-shadow">

<div id='tchatbotscenario_add_title'>
	<div class="fLeft"><?= $this->Html->image('scenario_setting_g.png', array('alt' => 'シナリオ設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>シナリオ設定更新</h1>
</div>

<div id='tchatbotscenario_entry'>
  <?=$this->Form->create('TChatbotScenario', ['url'=>['controller' =>'TChatbotScenario', 'action'=>'edit'], 'novalidate' => true, 'id'=>'TChatbotScenarioEntryForm', 'name'=>'TChatbotScenarioEntryForm'])?>
    <?= $this->element('TChatbotScenario/entry'); ?>
  <?=$this->Form->end();?>
</div><!-- /tchatbotscenario_form -->

</div>
