<?php
App::uses('CompanyExpireChecker', 'Vendor/util');
?>
<?= $this->element('Billings/script'); ?>

<div id='billing_cv_idx'>
    <div id='billing_cv_title'>
        <div class="fLeft"><i class="fa fa-money fa-2x" aria-hidden="true"></i></div>
        <h1>CV請求額一覧</h1>
    </div>
  <?= $this->Form->create('Billings', array(
      'type' => 'post',
      'url' => '/Billings/cv'
  )); ?>
  <?= $this->Form->input('targetDate', array(
      'type' => 'select',
      'options' => $targetDateList,
      'selected' => $targetDate,
      'div' => array('class' => 'targetDateWrap'),
      'label' => '対象期間：',
      'onchange' => "submit(this.form)"
  )); ?>
  <?= $this->Form->end(); ?>
    <div id="paging">
        <a id="csvExport" href="#" class="action_btn">CSV出力</a>
      <?php
      echo $this->Paginator->prev(
          $this->Html->image('paging.png', array('alt' => '前のページへ', 'width' => 25, 'height' => 25)),
          array('escape' => false, 'class' => 'btn-shadow navyBtn tr180'),
          null,
          array('class' => 'grayBtn tr180')
      );
      ?>
        <span style="width: auto!important;padding: 10px 0 0;"> <?php echo $this->Paginator->counter('{:page} / {:pages}'); ?> </span>
      <?php
      echo $this->Paginator->next(
          $this->Html->image('paging.png', array('alt' => '次のページへ', 'width' => 25, 'height' => 25)),
          array('escape' => false, 'class' => 'btn-shadow navyBtn'),
          null,
          array('escape' => false, 'class' => 'grayBtn')
      );
      ?>
    </div>
    <div id='billing_cv_list' class="p20trl">
        <table>
            <thead>
            <tr>
                <th style="width:10%;">顧客番号</th>
                <th style="width:30%;">会社名</th>
                <th style="width:20%;">キー</th>
                <th style="width:10%;">CV単価</th>
                <th style="width:10%;">CV件数</th>
                <th style="width:20%;">請求額</th>
            </tr>
            </thead>
          <?php
            $total_cv_value = 0;
            $total_cv = 0;
            $total_cv_amount = 0;
          ?>
          <?php foreach ((array)$companyList as $key => $val): ?>
            <?php
            $companyId = $val['MCompany']['id'];
            $companyKey = $val['MCompany']['company_key'];
            ?>
              <tbody>
              <tr <?php
              switch (intval($val['MCompany']['trial_flg'])) {
                case 0:
                  if (CompanyExpireChecker::isExpireAgreementDay($val['MAgreement']['agreement_end_day'])) {
                    // 契約期間切れの場合
                    echo 'style="background-color: #ccc;"';
                  } else {
                    if (CompanyExpireChecker::isWarningApplicationDay($val['MAgreement']['agreement_end_day'])) {
                      // 契約期間切れからn日前の場合
                      echo 'style="background-color: #FFFF99;"';
                    }
                  }
                  break;
                case 1:
                  if (CompanyExpireChecker::isExpireTrialDay($val['MAgreement']['trial_end_day'])) {
                    echo 'style="background-color: #ccc;"';
                  }
                  break;
              }
              ?> >
                  <td><?= h($val['MAgreement']['customer_number']) ?></td>
                  <td><a href="#" class="loginLink"><?= h($val['MCompany']['company_name']) ?></a></td>
                  <td><?= h($val['MCompany']['company_key']) ?></td>
                  <td><?= number_format($val['MAgreement']['cv_value']) ?></td>
                  <td class="adminId" style="display:none"><?= h($val['AdminUser']['mail_address']) ?></td>
                  <td class="adminPass" style="display:none"><?= h($val['MAgreement']['admin_password']) ?></td>
                  <td><?= !empty($val['CVCount']['cv']) ? $val['CVCount']['cv'] : 0 ?></td>
                  <td><?= !empty($val['CVCount']['cv']) ? number_format(intval($val['CVCount']['cv']) * intval($val['MAgreement']['cv_value'])) : 0 ?></td>
              </tr>
              </tbody>
            <?php
              $total_cv_value = $total_cv_value + $val['MAgreement']['cv_value'];
              $total_cv = $total_cv + $val['CVCount']['cv'];
              $total_cv_amount = $total_cv_amount + intval($val['CVCount']['cv']) * intval($val['MAgreement']['cv_value']);
            ?>
          <?php endforeach; ?>
              <tfoot>
                <tr>
                  <th colspan="3"><div style="text-align : left">合計</div></th>
                  <th><div style="text-align : left"><?php echo $total_cv_value; ?></div></th>
                  <th><div style="text-align : left"><?php echo $total_cv; ?></div></th>
                  <th><div style="text-align : left"><?php echo $total_cv_amount; ?></div></th>
                </tr>
              </tfoot>
        </table>
    </div>
</div>
