<div id='m_widget_setting_idx' class="card-shadow">

<div id='m_widget_setting_title'>
  <div class="fLeft"><?= $this->Html->image('setting_g.png', array('alt' => '個人設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>ウィジェット設定</h1>
</div>

<div id='m_widget_setting_form' class="p20x">
  <?= $this->element('MWidgetSettings/script'); ?>
  <?= $this->element('MWidgetSettings/entry'); ?>
</div><!-- /m_widget_setting_menu -->

</div><!-- /m_widget_setting_idx -->
