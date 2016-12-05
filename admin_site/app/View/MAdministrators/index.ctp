<?= $this->element('MAdministrators/script'); ?>

<div id='madmin_idx'>
  <div id='madmin_add_title'>
    <div class="fLeft"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></div>
    <h1>アカウント設定</h1>
  </div>

  <?= $this->Html->link('登録','javascript:void(0)',['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'openConfirm()']);?>
  <div id='madmin_list' style = 'padding: 5px 20px 20px 0px;'>
    <table>
      <thead>
        <tr>
          <th style="width:30em;">名前</th>
          <th>メールアドレス </th>
          <th width="1%"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach((array)$userList as $key => $val): ?>
        <?php
            $id = "";
            if ($val['MAdministrator']['id']) {
              $id = $val['MAdministrator']['id'];
            }
          ?>
          <tr ondblclick = <?= 'openEditDialog('.$val['MAdministrator']['id'].')'?>>
            <td><?=h($val['MAdministrator']['user_name'])?></td>
            <td><?=h($val['MAdministrator']['mail_address'])?></td>
            <td>
            <i class="fa fa-times fa-2x" aria-hidden="true" a href="javascript:void(0)" id="delete" onclick="remoteDeleteUser('<?=$id?>')"></i>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>