<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/19
 * Time: 10:33
 */
?>
<div id="t_chatbot_diagrams_idx" class="card-shadow entry-wrapper">

  <div id="t_chatbot_diagramas_add_title">
    <div class="fLeft"><i class="fal fa-sitemap fa-rotate-270 fa-2x"></i></div>
    <h1>チャットツリー設定登録</h1>
  </div>
  <div id="t_chatbot_diagrams_header">
    <div id="t_chatbot_diagrams_entry">
      <?= $this->Form->create('TChatbotDiagrams', ['url' => ['controller' => 'TChatbotDiagrams', 'action' => 'add'], 'novalidate' => true, 'id' => 'TChatbotDiagramsEntryForm', 'name' => 'TChatbotDiagramsEntryForm']) ?>
      <?= $this->element('TChatbotDiagrams/entry'); ?>
      <?= $this->Form->end(); ?>
    </div>
    <div id="diagrams_simulator_btn">
      シミュレータを起動
    </div>
  </div>
  <div id="canvas_scale_controller">
    <input type="range" name="scale_slide_bar" value="5" min="3" max="7" step="0.5"/>
  </div>
  <div id="t_chatbot_diagrams_body">
    <?= $this->element('TChatbotDiagrams/editor'); ?>
  </div>
</div>