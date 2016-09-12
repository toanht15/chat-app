<div id='chat_notifications_idx' class="card-shadow">

<div id='chat_notifications_add_title'>
  <div class="fLeft"><?= $this->Html->image('notification_g.png', array('alt' => 'チャット通知設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>チャット通知設定登録<span id="sortMessage"></span></h1>
</div>
<div id='chat_notifications_entry' class="p20x">
  <?= $this->Form->create('MChatNotification', ['type' => 'post', 'url' => ['controller' => 'MChatNotifications', 'action' => 'add'],  'enctype'=>'multipart/form-data']); ?>
    <?=$this->element('MChatNotifications/entry'); ?>
  <?=$this->Form->end();?>
</div>

</div>
