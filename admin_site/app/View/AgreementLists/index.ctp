<?= $this->element('Tops/script'); ?>

<div id='agreementList_idx'>
  <div id='agreementList_add_title'>
    <div class="fLeft"><i class="fa fa-home fa-2x" aria-hidden="true"></i></div>
    <h1>契約状況</h1>
  </div>
  <?php echo $this->Html->link(
    '登録',
    'javascript:void(0)',
    array('escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'openConfirm()'));
  ?>

  <table>
    <thead>
      <tr>
        <th style="width:30em;">会社名</th>
        <th style="width:12em;">キー</th>
        <th style="width:12em;">プラン</th>
        <th>ID数</th>
      </tr>
    </thead>
    <?php foreach((array)$userList as $key => $val): ?>
      <tbody>
        <?php if(h($val['MCompany']['trial_flg']) == 0) { ?>
          <tr>
            <td><?=h($val['MCompany']['company_name'])?></td>
            <td><?=h($val['MCompany']['company_key'])?></td>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 1){ ?>
              <td>フルプラン</td>
            <?php } ?>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 2){ ?>
              <td>画像共有のみプラン</td>
            <?php } ?>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 3){ ?>
              <td>チャットのみプラン</td>
            <?php } ?>
            <td><?=h($val['MUser']['del_flg'])?>/<?=h($val['MCompany']['limit_users'])?></td>
          </tr>
        <?php } ?>
      </tbody>
    <?php endforeach; ?>
  </table>
</div>
