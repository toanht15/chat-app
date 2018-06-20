<div id='chat_notifications_idx' class="card-shadow">

<div id='chat_notifications_add_title'>
  <div class="fLeft"><i class="fal fa-info-circle fa-2x"></i></div>
  <h1>チャット通知情報</h1>
</div>
<div id='chat_notifications_entry' class="p20x">
  <?= $this->Form->create('MChatNotification', ['type' => 'post', 'url' => ['controller' => 'MChatNotifications', 'action' => 'add'],  'enctype'=>'multipart/form-data']); ?>
    <?=$this->element('MChatNotifications/entry'); ?>
  <?=$this->Form->end();?>
</div>

</div>
