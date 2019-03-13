<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/19
 * Time: 11:46
 */
?>
<?php echo $this->element('TChatbotDiagrams/nodeCreator'); ?>
<?php echo $this->element('WidgetSimulator/simulatorService'); ?>
<div>
  <ul>
    <li>
      <label>チャットツリー名称<span class="questionBalloon"><icon class="commontooltip questionBtn" data-text="チャットツリーに名称を設定します。">?</icon></span></label>
      <?= $this->ngForm->input('TChatbotDiagram.name', array(
        'type' => 'text',
        'placeholder' => 'チャットツリー名称を入力',
        'maxlength' => 50,
        'class' => 'w100',
        'label' => false
      )) ?>
      <?php if (!empty($errors['name'])) echo "<span class='error-message'>" . h($errors['name'][0]) . "</span>"; ?>
    </li>
    <!-- メッセージ間隔 -->
    <li>
      <label>メッセージ間隔<span class="questionBalloon"><icon class="commontooltip questionBtn" data-text="各メッセージを送信する間隔（秒数）を設定します。">?</icon></span></label>
      <?= $this->ngForm->input('TChatbotDiagram.messageIntervalTimeSec', array(
        'type' => 'number',
        'max' => 99,
        'min' => 0,
        'ng-model' => 'messageIntervalTimeSec',
        'after' => '秒',
        'label' => false,
      )) ?>
      <?php if (!empty($errors['messageIntervalTimeSec'])) echo "<li class='error-message'>" . h($errors['messageIntervalTimeSec'][0]) . "</li>"; ?>
    </li>

  </ul>
</div>

<!-- フッター -->
<!-- 超暫定対応なので戻るとか保存とかも実装しておく必要がある -->
<section>
  <?= $this->Form->hidden('TChatbotDiagram.id') ?>
  <?= $this->Form->hidden('TChatbotDiagram.activity') ?>
  <?=$this->ngForm->input('widgetSettings', ['type' => 'hidden','value' => json_encode($this->data['widgetSettings'])])?>
  <div id="tchatbotscenario_actions" class="fotterBtnArea">
    <?=$this->Html->link('戻る','/TChatbotDiagrams/index/page:', ['class'=>'whiteBtn btn-shadow'])?>
    <a id="submitBtn" href="javascript:void(0)" class="greenBtn btn-shadow">保存</a>
    <?php
    $class = "";
    if ( empty($this->data['TChatbotScenario']['id']) ) {
      $class = "redBtn vHidden";
    } else
      if (count($this->data['callerInfo']['TAutoMessage']) >= 1 || count($this->data['callerInfo']['TChatbotScenario']) >= 1) {
        $class = "disOffgrayBtn disabled commontooltip";
      } else {
        $class = "redBtn";
      }
    ?>
    <?= $this->Html->link(
      '削除',
      'javascript:void(0)',
      array('escape' => false,
        'class' => 'btn-shadow redBtn',
        'id' => 'tchatbotscenario_edit_remove_btn',
      )) ?>
  </div>
  <!-- シミュレーター -->
  <div ng-controller="DialogController as dialog" ng-cloak>
    <?php echo $this->element('WidgetSimulator/simulatorService'); ?>
    <?= $this->element('TChatbotDiagrams/simulator'); ?>
  </div>
</section>
