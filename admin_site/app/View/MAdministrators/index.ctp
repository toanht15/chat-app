<?php echo $this->element('MAdministrators/script'); ?>

<div id='muser_idx'>
  <div id='muser_add_title'>
    <div class="fLeft"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></div>
    <h1>アカウント設定</h1>
  </div>

  <?php echo $this->Html->link(
    '登録',
    'javascript:void(0)',
    array('escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'openConfirm()'));
  ?>
  <div id='muser_list' style = 'padding: 5px 20px 20px 0px;'>
    <table>
      <thead>
        <tr>
          <th style="width:30em;">名前</th>
          <th>メールアドレス </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach((array)$userList as $key => $val): ?>
          <tr onclick = <?= 'openEditDialog('.$val['MAdministrator']['id'].')'?>>
            <td><?=h($val['MAdministrator']['user_name'])?></td>
            <td><?=h($val['MAdministrator']['mail_address'])?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>