<?= $this->element('Tops/script'); ?>

<div id='top_idx'>
  <div id='top_add_title'>
    <div class="fLeft"><i class="fa fa-home fa-2x" aria-hidden="true"></i></div>
    <h1>契約状況</h1>
  </div>
  <div id='agreement_button'>
    <?= $this->Html->link('契約','#tab1',['escape' => false,'class' => 'action_btn agreement on','id' => 'agreement_tag','style'=> 'width:6em','onclick' => "ChangeTab('tab1')"]);?>
    <?= $this->Html->link('試用','#tab2',['escape' => false,'class' => 'normal_btn trial','id' => 'trial_tag','style'=> 'width:6em','onclick' => "ChangeTab('tab2')"]);?>
  </div>

  <div id='top_list' class="p20trl">
    <table>
      <thead>
        <tr>
          <th style="width:25em;">会社名</th>
          <th style="width:25em;">キー</th>
          <th style="width:25em;">プラン</th>
          <th style="width:25em;">ID数</th>
        </tr>
      </thead>
      <tbody class='tab1'>
        <?php /* 契約中企業一覧 初期表示は非表示 */ ?>
        <?php foreach((array)$companyList as $key => $val): ?>
          <?php if(intval($val['MCompany']['trial_flg']) === 0) { ?>
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
              <td><?=h($val['MUser']['user_account'])?>/<?=h($val['MCompany']['limit_users'])?></td>
            </tr>
          <?php } ?>
        <?php endforeach; ?>
      </tbody>
      <?php /* トライアル中企業一覧 初期表示は非表示 */ ?>
      <tbody class='tab2' style="display:none">
        <?php foreach((array)$companyList as $key => $val): ?>
          <?php if(intval($val['MCompany']['trial_flg']) === 1) { ?>
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
            <td><?=h($val['MCompany']['limit_users'])?></td>
          </tr>
          <?php } ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
