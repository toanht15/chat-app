<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/19
 * Time: 10:33
 */
?>
<?php echo $this->element('TChatbotDiagrams/angularjs'); ?>
<?php echo $this->element('TChatbotDiagrams/preview'); ?>
<?php echo $this->Html->script('jscolor.min.js'); ?>
<div id="t_chatbot_diagrams_idx" class="card-shadow entry-wrapper" ng-app="sincloApp"
     ng-controller="DiagramController as main" ng-cloak>
  <div id="t_chatbot_diagramas_add_title">
    <div class="fLeft"><i class="fal fa-sitemap fa-rotate-270 fa-2x"></i></div>
    <h1>チャットツリー設定登録</h1>
  </div>
  <div id="t_chatbot_diagrams_header">
    <div id="t_chatbot_diagrams_entry">
      <?= $this->Form->create('TChatbotDiagrams', ['url' => ['controller' => 'TChatbotDiagrams', 'action' => 'save'], 'novalidate' => true, 'id' => 'TChatbotDiagramsEntryForm', 'name' => 'TChatbotDiagramsEntryForm']) ?>
      <?= $this->element('TChatbotDiagrams/entry'); ?>
      <?= $this->Form->end(); ?>
    </div>
    <div id="diagrams_simulator_btn">
      <span class="btn-shadow blueBtn" ng-click="main.openSimulator()">シミュレーターを起動</span>
    </div>
  </div>
  <div id="canvas_scale_controller">
    <input type="range" name="scale_slide_bar" value="5" min="3" max="7" step="0.5"/>
  </div>
  <div id="t_chatbot_diagrams_body">
    <?= $this->element('TChatbotDiagrams/editor'); ?>
  </div>
</div>
