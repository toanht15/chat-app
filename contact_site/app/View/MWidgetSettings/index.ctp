<?= $this->Html->script('jscolor.min.js'); ?>
<div id='m_widget_setting_idx' class="card-shadow">

  <div id='m_widget_setting_title'>
    <div class="fLeft"><?= $this->Html->image('chat_g.png', array('alt' => '個人設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>ウィジェット設定</h1>
  </div>

  <div id='m_widget_setting_form' class="p20x" ng-app="sincloApp" ng-controller="WidgetCtrl">
    <?= $this->element('MWidgetSettings/script'); ?>
    <div id="m_widget_setting_entry">
      <?= $this->element('MWidgetSettings/entry'); ?>
    </div><!-- /m_widget_setting_entry -->
    <div id="m_widget_simulator">
      <?= $this->element('MWidgetSettings/simulator'); ?>
    </div><!-- /m_widget_simulator -->
  </div><!-- /m_widget_setting_form -->

</div><!-- /m_widget_setting_idx -->
