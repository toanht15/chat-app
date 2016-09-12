<?= $this->element('MChatNotifications/script') ?>

<div id='chat_notifications_idx' class="card-shadow">

<div id='chat_notifications_add_title'>
  <div class="fLeft"><?= $this->Html->image('notification_g.png', array('alt' => 'チャット通知設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>チャット通知設定<span id="sortMessage"></span></h1>
</div>

<div id='chat_notifications_menu' class="p20trl">
  <div class="fLeft" >
    <?= $this->Html->link(
                $this->Html->image(
                  'add.png',
                  array(
                    'alt' => '登録',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                ['controller'=>'MChatNotifications', 'action' => 'add'],
                array(
                  'class' => 'btn-shadow greenBtn',
                  'escape' => false
                )
    ); ?>
  </div>
</div>

<div id='chat_notifications_list' class="p20x">
  <table>
    <thead>
      <tr>
        <th>対象ページ</th>
        <th>通知アイコン</th>
        <th>通知名</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody class="sortable">
    <?php foreach((array)$settingList as $key => $val): ?>
      <tr>
        <td width="20%" class="tLeft"><?=$val['MChatNotification']['keyword']?></td>
        <td width="20%" class="tCenter"><?=$this->Html->image("notification/".$val['MChatNotification']['image'], ['width'=>50, 'height'=>50])?></td>
        <td width="40%" class="tLeft"><?=$val['MChatNotification']['name']?></td>
        <td width="20%" class="tCenter ctrlBtnArea">
          <?php
            echo $this->Html->link(
              $this->Html->image(
                'edit.png',
                array(
                  'alt' => '更新',
                  'width' => 30,
                  'height' => 30,
                )
              ),
              ['controller'=>'MChatNotifications', 'action' => 'edit', $val['MChatNotification']['id']],
              array(
                'class' => 'btn-shadow greenBtn fLeft',
                'escape' => false
              )
            );
          ?>
          <?php
            echo $this->Html->link(
                $this->Html->image(
                  'trash.png',
                  array(
                    'alt' => '削除',
                    'width' => 30,
                    'height' => 30
                  )
                ),
                'javascript:void(0)',
                array(
                  'class' => 'btn-shadow redBtn fRight',
                  'onclick' => 'openConfirmDialog('.$val['MChatNotification']['id'].')',
                  'escape' => false
                )
            );
          ?>

        </td>
      </tr>
    <?php endforeach; ?>
    <?php if ( count($settingList) === 0 ) :?>
      <td class="tCenter" colspan="4">チャット通知設定が設定されていません</td>
    <?php endif; ?>
    </tbody>
  </table>
</div>

</div>
