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
    <div class="fLeft" >
      <div class="btnSet">
        <span style="display:inline; padding: 0;">
          <a href="<?= $this->Html->url('/MChatNotifications/add') ?>" style="text-decoration: none;">
            <?= $this->Html->image('add.png', array(
                'alt' => '登録',
                'id'=>'chat_notifications_add_btn',
                'class' => 'btn-shadow disOffgreenBtn commontooltip',
                'data-text' => '新規追加',
                'data-balloon-position' => '36',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
        <span style="display:inline; padding: 0;">
          <a>
            <?= $this->Html->image('dustbox.png', array(
                'alt' => '削除',
                'id'=>'chat_notifications_dustbox_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => '削除する',
                'data-balloon-position' => '35',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
      </div>
    </div>
  </div>

  <div id='chat_notifications_list' class="p20x">
    <table>
      <thead>
      <tr>
<!-- UI/UX統合対応start -->
        <th width=" 5%">
          <input type="checkbox" name="allCheck" id="allCheck" >
          <label for="allCheck"></label>
        </th>
<!-- UI/UX統合対応end -->
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
        <tr class="pointer" onclick="jumpTo(<?="'".$this->Html->url(['controller'=>'MChatNotifications', 'action' => 'edit', h($val['MChatNotification']['id'])])."'";?>)">
<!-- UI/UX統合対応start -->
          <td class="tCenter" onclick="event.stopPropagation();">
            <input type="checkbox" name="selectTab" id="selectTab<?=$key?>" value="<?=$val['MChatNotification']['id']?>">
            <label for="selectTab<?=$key?>"></label>
          </td>
<!-- UI/UX統合対応end -->
          <td width="20%" class="tLeft"><?=h($val['MChatNotification']['keyword'])?></td>
          <td width="20%" class="tCenter"><?=$this->Html->image("notification/".$val['MChatNotification']['image'], ['width'=>30, 'height'=>30])?></td>
          <td class="tLeft"><?=h($val['MChatNotification']['name'])?></td>
<!--
          <td class="tCenter ctrlBtnArea">
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
                'class' => 'btn-shadow redBtn blockCenter',
                'onclick' => 'event.stopPropagation(); openConfirmDialog('.h($val['MChatNotification']['id']).')',
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
