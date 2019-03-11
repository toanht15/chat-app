<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/19
 * Time: 11:46
 */
?>
<?php echo $this->element('TChatbotDiagrams/nodeCreator'); ?>
<?php echo $this->element('TChatbotDiagrams/jointScript'); ?>
<?php echo $this->element('TChatbotDiagrams/angularjs');?>
<div>
  <ul>
    <li>
      <label>チャットツリー名称<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャットツリーに名称を設定します。">?</icon></span></label>
      <?= $this->ngForm->input('name', [
        'type' => 'text',
        'placeholder' => 'チャットツリー名称を入力',
        'maxlength' => 50,
        'class' => 'w100',
        'label' => false
      ]) ?>
      <?php if (!empty($errors['name'])) echo "<span class='error-message'>" . h($errors['name'][0]) . "</span>"; ?>
    </li>
    <!-- メッセージ間隔 -->
    <li>
      <label>メッセージ間隔<span class="questionBalloon"><icon class="questionBtn" data-tooltip="各メッセージを送信する間隔（秒数）を設定します。">?</icon></span></label>
      <?= $this->ngForm->input('messageIntervalTimeSec', [
        'type' => 'text',
        'class' => 'tRight',
        'maxlength' => 3,
        'ng-model' => 'messageIntervalTimeSec',
        'after' => '秒',
        'label' => false
      ]) ?>
      <?php if (!empty($errors['messageIntervalTimeSec'])) echo "<li class='error-message'>" . h($errors['messageIntervalTimeSec'][0]) . "</li>"; ?>
    </li>

  </ul>
</div>