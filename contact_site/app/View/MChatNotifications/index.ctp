<?= $this->element('MChatNotifications/script') ?>

<div id='chat_notifications_idx' class="card-shadow">

<div id='chat_notifications_add_title'>
  <div class="fLeft"><?= $this->Html->image('notification_g.png', array('alt' => 'チャット通知設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>チャット通知設定<span id="sortMessage"></span></h1>
</div>

<div id='chat_notifications_menu'>
  <span class="pre">サイト閲覧者がどのページからチャット送信したかを直観的に把握できるよう、企業側にチャット受信時に通知するデスクトップ通知のアイコンをカスタマイズすることができます。
  ※ Internet Explorerは <b>"チャット通知"</b> に対応しておりません
  ※ Safariは <b>"通知のアイコン指定"</b> に対応しておりません</span>
  <div>
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
        <th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck" ><label for="allCheck"></label></th>
        <th class="tLeft">対象ページ</th>
        <th class="tLeft">通知アイコン</th>
        <th class="tLeft">通知名</th>
<!--
        <th class="tCenter">操作</th>
 -->
      </tr>
    </thead>
    <tbody class="sortable">
    <?php foreach((array)$settingList as $key => $val): ?>
      <tr>
        <td class="tCenter">
          <input type="checkbox" name="selectTab" id="selectTab<?=$key?>" value="<?=$val['MChatNotification']['id']?>">
          <label for="selectTab<?=$val['MChatNotification']['id']?>"></label>
        </td>
        <td width="20%" class="tLeft">
          <?php
            echo $this->Html->link(
              h($val['MChatNotification']['keyword']),
              ['controller'=>'MChatNotifications', 'action' => 'edit', h($val['MChatNotification']['id'])],
              array(
                'class' => 't-link',
                'escape' => false
            )
            );
          ?>
<!--
          <?=h($val['MChatNotification']['keyword'])?>
 -->
        </td>
        <td width="20%" class="tCenter">
          <?php
            echo $this->Html->link(
              $this->Html->image("notification/".$val['MChatNotification']['image'], ['width'=>30, 'height'=>30]),
              ['controller'=>'MChatNotifications', 'action' => 'edit', h($val['MChatNotification']['id'])],
              array(
                'escape' => false
            )
            );
          ?>
<!--
          <?=$this->Html->image("notification/".$val['MChatNotification']['image'], ['width'=>30, 'height'=>30])?>
 -->
        </td>
        <td class="tLeft">
          <?php
            echo $this->Html->link(
              h($val['MChatNotification']['name']),
              ['controller'=>'MChatNotifications', 'action' => 'edit', h($val['MChatNotification']['id'])],
              array(
                'class' => 't-link',
                'escape' => false
            )
            );
          ?>
<!--
          <?=h($val['MChatNotification']['name'])?>
 -->
        </td>
<!--
        <td class="tCenter ctrlBtnArea">
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
              ['controller'=>'MChatNotifications', 'action' => 'edit', h($val['MChatNotification']['id'])],
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
                  'onclick' => 'openConfirmDialog('.h($val['MChatNotification']['id']).')',
                  'escape' => false
                )
            );
          ?>
        </td>
 -->
      </tr>
    <?php endforeach; ?>
    <?php if ( count($settingList) === 0 ) :?>
      <td class="tCenter" colspan="4">チャット通知設定が設定されていません</td>
    <?php endif; ?>
    </tbody>
  </table>
</div>

</div>
