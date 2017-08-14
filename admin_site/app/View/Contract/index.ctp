<?= $this->element('Contract/script'); ?>

<div id='agreementList_idx'>
  <div id='agreementList_add_title'>
    <div class="fLeft"><i class="fa fa-home fa-2x" aria-hidden="true"></i></div>
    <h1>企業一覧</h1>
  </div>
  <?= $this->Html->link('登録',['controller'=>'Contract', 'action' => 'add'],['escape' => false, 'id' => 'searchRefine','class' => 'action_btn']); ?>
  <div id='agreementList_list' class="p20trl">
    <table>
      <thead>
        <tr>
          <th style="width:17em;">会社名</th>
          <th style="width:17em;">キー</th>
          <th style="width:17em;">プラン</th>
          <th style="width:15em;">ID数 / 最大ID数</th>
          <th style="width:8em;">ML用アカウント</th>
          <th style="width:8em;">パスワード</th>
          <th style="width:14em;">トライアル / 本契約</th>
          <th style="width:8em;">開始日</th>
          <th style="width:8em;">終了日</th>
          <th style="width:8em;">登録日時</th>
        </tr>
      </thead>
      <?php foreach((array)$companyList as $key => $val): ?>
        <?php
          $companyId = $val['MCompany']['id'];
          $userId = $val['MUser']['id'];
          $companyKey = $val['MCompany']['company_key'];
        ?>
        <tbody>
          <tr ondoubleclick= "location.href = '<?=$this->Html->url(array('controller' => 'Contract', 'action' => 'edit', $val['MCompany']['id']))?>';">
            <td><?=h($val['MCompany']['company_name'])?></td>
            <td><?=h($val['MCompany']['company_key'])?></td>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 1){ ?>
              <td>プレミアムプラン</td>
            <?php } ?>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 2){ ?>
              <td>スタンダードプラン</td>
            <?php } ?>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 3){ ?>
              <td>シェアリングプラン</td>
            <?php } ?>
            <?php if(h($val['MCompany']['m_contact_types_id']) == 4){ ?>
              <td>ベーシックプラン</td>
            <?php } ?>
            <td><?= h($val['MUser']['user_account'])?> / <?=h($val['MCompany']['limit_users'])?></td>
            <td><?= h($val['MCompany']['company_key'].C_MAGREEMENT_MAIL_ADDRESS) ?></td>
            <td><?= h($val['MAgreement']['admin_password']) ?></td>
            <td><?= intval($val['MCompany']['trial_flg']) === 1 ? "トライアル" : "本契約" ?></td>
            <td><?= intval($val['MCompany']['trial_flg']) === 1 ?  h($val['MAgreement']['trial_start_day']) : h($val['MAgreement']['agreement_start_day']) ?></td>
            <td><?= intval($val['MCompany']['trial_flg']) === 1 ?  h($val['MAgreement']['trial_end_day']) : h($val['MAgreement']['agreement_end_day']) ?></td>
            <td><?= !empty($val['MAgreement']['application_day']) ? h($val['MAgreement']['application_day']) : h(date('Y-m-d', strtotime($val['MCompany']['created']))); ?></td>
          </tr>
      </tbody>
    <?php endforeach; ?>
  </table>
  </div>
</div>
