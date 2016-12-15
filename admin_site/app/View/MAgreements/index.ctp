<?= $this->element('MAgreements/addScript'); ?>

<div id='agreementList_idx'>
  <div id='agreementList_add_title'>
    <div class="fLeft"><i class="fa fa-home fa-2x" aria-hidden="true"></i></div>
    <h1>契約状況</h1>
  </div>
  <?= $this->Html->link('登録',['controller'=>'MAgreements', 'action' => 'add'],['escape' => false, 'id' => 'searchRefine','class' => 'action_btn']); ?>
  <div id='agreementList_list' class="p20trl">
    <table>
      <thead>
        <tr>
          <th style="width:17em;">会社名</th>
          <th style="width:17em;">キー</th>
          <th style="width:17em;">プラン</th>
          <th style="width:15em;">ID数</th>
          <th style="width:17em;">メールアドレス</th>
          <th style="width:17em;">パスワード</th>
          <th width="1%"></th>
        </tr>
      </thead>
      <?php foreach((array)$companyList as $key => $val): ?>
        <?php
          $id = "";
          if ($val['MAgreement']['id']) {
            $id = $val['MAgreement']['id'];
          }
          $companyId = $val['MCompany']['id'];
          $userId = $val['MUser']['id'];
          $companyKey = $val['MCompany']['company_key'];
        ?>
        <tbody>
        <?php if(h($val['MCompany']['trial_flg']) == 0) { ?>
          <tr ondblclick= "location.href = '<?=$this->Html->url(array('controller' => 'MAgreements', 'action' => 'edit',$val['MAgreement']['id']))?>';">
            <td><?=h($val['MCompany']['company_name'])?></td>
            <td><?=h($val['MCompany']['company_key'])?></td>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 1){ ?>
              <td>フルプラン</td>
            <?php } ?>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 2){ ?>
              <td>チャットプラン</td>
            <?php } ?>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 3){ ?>
              <td>画面共有プラン</td>
            <?php } ?>
            <td><?=h($val['MUser']['companyId'])?>/<?=h($val['MCompany']['limit_users'])?></td>
            <td><?=h($val['MUser']['mail_address'])?></td>
            <td><?=h($val['MAgreement']['admin_password'])?></td>
            <td>
            <i class="fa fa-times fa-2x" aria-hidden="true" a href="javascript:void(0)" id="delete" onclick="remoteDeleteCompany('<?=$id?>','<?=$companyId?>','<?=$userId?>','<?=$companyKey?>')"></i>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    <?php endforeach; ?>
  </table>
  </div>
</div>
