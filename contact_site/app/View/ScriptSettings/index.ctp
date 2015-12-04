<?php // echo $this->element('Customers/script') ?>

<div id='script_setting_idx' class="card-shadow">

<div id='script_setting_title'>
	<div class="fLeft"><?= $this->Html->image('script_g.png', array('alt' => 'コード設置・デモ画面', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto', 'url' => array('controller' => 'Customers', 'action' => 'index'))) ?></div>
	<div style="padding: 6px 35px;">コード設置・デモ画面</div>
</div>
<div id='script_setting_content' class="p20x">
	<?= $this->Html->link('デモ画面へ', array('controller' => 'ScriptSettings', 'action' => 'demopage'), array('target' => '_demo')) ?>
</div>

</div>