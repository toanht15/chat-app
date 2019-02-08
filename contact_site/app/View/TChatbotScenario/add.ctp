<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->Html->script("angular.sortable.js"); ?>
<?= $this->Html->script('jscolor.min.js'); ?>
<div id='tchatbotscenario_idx' class="card-shadow entry-wrapper">

<div id='tchatbotscenario_add_title'>
	<div class="fLeft"><i class="fal fa-code-merge fa-2x"></i></div>
	<h1>シナリオ設定登録</h1>
</div>

<div id='tchatbotscenario_entry'>
  <?=$this->Form->create('TChatbotScenario', ['url'=>['controller' =>'TChatbotScenario', 'action'=>'add'], 'novalidate' => true, 'id'=>'TChatbotScenarioEntryForm', 'name'=>'TChatbotScenarioEntryForm'])?>
    <?= $this->element('TChatbotScenario/entry'); ?>
  <?=$this->Form->end();?>
</div><!-- /tchatbotscenario_form -->

</div>
<script>
  FontAwesomeConfig = { searchPseudoElements: true };
</script>