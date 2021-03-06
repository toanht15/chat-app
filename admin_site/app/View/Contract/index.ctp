<?php
  App::uses('CompanyExpireChecker', 'Vendor/util');
?>
<?= $this->element('Contract/script'); ?>

<div id='agreementList_idx'>
  <div id='agreementList_add_title'>
    <div class="fLeft"><i class="fa fa-home fa-2x" aria-hidden="true"></i></div>
    <h1>企業一覧</h1>
  </div>
  <?= $this->Html->link('登録',['controller'=>'Contract', 'action' => 'add'],['escape' => false, 'id' => 'searchRefine','class' => 'action_btn']); ?>
  <div id="paging">
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
      $this->Html->image('paging.png', array('alt' => '次のページへ', 'width'=>25, 'height'=>25)),
      array('escape' => false, 'class' => 'btn-shadow navyBtn'),
      null,
      array('escape' => false, 'class' => 'grayBtn')
    );
    ?>
  </div>
  <div id='agreementList_list' class="p20trl">
    <table>
      <thead>
        <tr>
          <th style="width:8em;">顧客番号</th>
          <th style="width:23em;">会社名</th>
          <?php if (ALLOW_SET_CV_VALUE): ?>
              <th style="width:8em;">CV単価</th>
          <?php endif; ?>
          <th style="width:8em;">キー</th>
          <?php if ($isStrongPermission): ?>
              <th style="width:15em;">プラン</th>
              <th style="width:20em;">オプション</th>
              <th style="width:8em;">ID数<br>/ 最大ID数</th>
              <th style="width:12em;">ML用アカウント</th>
              <th style="width:8em;">パスワード</th>
              <th style="width:8em;">トライアル<br>/ 本契約</th>
          <?php endif; ?>
          <th style="width:8em;">開始日</th>
          <th style="width:8em;">終了日</th>
          <th style="width:8em;">登録日</th>
          <th style="width:8em;">更新日</th>
        </tr>
      </thead>
      <?php foreach((array)$companyList as $key => $val): ?>
        <?php
          $companyId = $val['MCompany']['id'];
          $companyKey = $val['MCompany']['company_key'];
        ?>
        <tbody>
          <tr ondblclick= "location.href = '<?=$this->Html->url(array('controller' => 'Contract', 'action' => 'edit', $val['MCompany']['id']))?>';" <?php
            switch(intval($val['MCompany']['trial_flg'])) {
              case 0:
                if(CompanyExpireChecker::isExpireAgreementDay($val['MAgreement']['agreement_end_day'])) {
                  // 契約期間切れの場合
                  echo 'style="background-color: #ccc;"';
                } else if (CompanyExpireChecker::isWarningApplicationDay($val['MAgreement']['agreement_end_day'])) {
                  // 契約期間切れからn日前の場合
                  echo 'style="background-color: #FFFF99;"';
                }
                break;
              case 1:
                if(CompanyExpireChecker::isExpireTrialDay($val['MAgreement']['trial_end_day'])) {
                  echo 'style="background-color: #ccc;"';
                }
                break;
            }
            ?> >
            <td><?=h($val['MAgreement']['customer_number'])?></td>
            <td><a href="#" class="loginLink"><?=h($val['MCompany']['company_name'])?></a></td>
            <?php if (ALLOW_SET_CV_VALUE): ?>
                <td><?= number_format($val['MAgreement']['cv_value']) ?></td>
            <?php endif; ?>
            <td><?=h($val['MCompany']['company_key'])?></td>

            <?php if ($isStrongPermission): ?>
              <?php if(h($val['MCompany']['m_contact_types_id']) == 1){ ?>
                    <td>プレミアムプラン</td>
              <?php } ?>
              <?php if(h($val['MCompany']['m_contact_types_id']) == 2){ ?>
                    <td>チャットプラン</td>
              <?php } ?>
              <?php if(h($val['MCompany']['m_contact_types_id']) == 3){ ?>
                    <td>シェアリングプラン</td>
              <?php } ?>
              <?php if(h($val['MCompany']['m_contact_types_id']) == 4){ ?>
                    <td>ベーシックプラン</td>
              <?php } ?>
                <td><?php
                  $coreSettings = json_decode($val['MCompany']['core_settings'], TRUE);
                  foreach($coreSettings as $coreSetting => $enabled) {
                    switch($coreSetting) {
                      case 'enableRealtimeMonitor':
                        if ($enabled) {
                          echo '<p>【リアルタイムモニタ】</p>';
                        }
                        break;
                      case 'refCompanyData':
                        if($enabled) {
                          echo '<p>【企業情報付与】</p>';
                        }
                        break;
                      case 'laCoBrowse':
                        if($enabled) {
                          echo '<p>【画面キャプチャ連携】<br>（同時セッション：'.$val['MCompany']['la_limit_users'].'）</p>';
                        }
                        break;
                      case 'hideRealtimeMonitor':
                        if($enabled) {
                          echo '<p>【リアルタイムモニタ非表示】</p>';
                        }
                        break;
                      case 'chatbotScenario':
                        if($enabled) {
                          echo '<p>【シナリオ設定】</p>';
                        }
                        break;
                      case 'chatbotTreeEditor':
                        if($enabled) {
                          echo '<p>【チャットツリー設定】</p>';
                        }
                        break;
                    }
                  }
                  ?></td>
                <td><?= h(!empty($val['MUser']['user_account']) ? $val['MUser']['user_account'] : 0)?> / <?=h($val['MCompany']['limit_users'])?></td>
            <?php endif; ?>
              <td class="adminId" <?= ($isStrongPermission) ? '' : 'style="display:none"' ?>><?= h($val['AdminUser']['mail_address']) ?></td>
              <td class="adminPass" <?= ($isStrongPermission) ? '' : 'style="display:none"' ?>><?= h($val['MAgreement']['admin_password']) ?></td>
            <?php if ($isStrongPermission): ?>
            <td><?= intval($val['MCompany']['trial_flg']) === 1 ? "トライアル" : "本契約" ?></td>
            <?php endif; ?>
            <td><?= intval($val['MCompany']['trial_flg']) === 1 ?  h($val['MAgreement']['trial_start_day']) : h($val['MAgreement']['agreement_start_day']) ?></td>
            <td><?= intval($val['MCompany']['trial_flg']) === 1 ?  h($val['MAgreement']['trial_end_day']) : h($val['MAgreement']['agreement_end_day']) ?></td>
            <td><?= !empty($val['MAgreement']['application_day']) ? h($val['MAgreement']['application_day']) : h(date('Y-m-d', strtotime($val['MCompany']['created']))); ?></td>
            <td><?= !empty($val['MAgreement']['modified']) ? h(date('Y-m-d', strtotime($val['MAgreement']['modified']))) : ""; ?></td>
          </tr>
      </tbody>
    <?php endforeach; ?>
  </table>
  </div>
</div>
