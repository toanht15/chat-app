<div id="testpage_bg">
    <style>body { overflow: auto!important; }</style>
<?php switch($layoutNumber){ ?>
<?php   case 1: ?>
  <?php
    echo $this->element('ScriptSettings/top');
  ?>
    <?php echo $this->Html->script($fileName, ['data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 2: ?>
  <?= $this->element('ScriptSettings/plan'); ?>
    <?php echo $this->Html->script($fileName, ['data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 3: ?>
  <?= $this->element('ScriptSettings/flow'); ?>
     <?php echo $this->Html->script($fileName, ['data-form' => 1, 'data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 4: ?>
  <?= $this->element('ScriptSettings/contact'); ?>
     <?php echo $this->Html->script($fileName, ['data-form' => 1, 'data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 5: ?>
  <?= $this->element('ScriptSettings/company'); ?>
     <?php echo $this->Html->script($fileName, ['data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 6: ?>
  <?= $this->element('ScriptSettings/works'); ?>
     <?php echo $this->Html->script($fileName, ['data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 7: ?>
  <?= $this->element('ScriptSettings/staff'); ?>
     <?php echo $this->Html->script($fileName, ['data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 8: ?>
  <?= $this->element('ScriptSettings/link'); ?>
     <?php echo $this->Html->script($fileName, ['data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 9: ?>
  <?= $this->element('ScriptSettings/faq'); ?>
     <?php echo $this->Html->script($fileName, ['data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 10: ?>
  <?= $this->element('ScriptSettings/campaign'); ?>
     <?php echo $this->Html->script($fileName, ['data-show-always' => 1]); ?>
<?php     break; ?>
<?php   case 11: ?>
  <?= $this->element('ScriptSettings/confirm'); ?>
<?php     break; ?>
<?php   default; ?>
<?php } ?>

</div>
