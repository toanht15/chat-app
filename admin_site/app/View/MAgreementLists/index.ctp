<script type = "text/javascript">


function remoteDeleteCompany(id,m_companies_id,m_users_id){
  console.log(id);
  console.log(m_companies_id);
  console.log(m_users_id);
  modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'アカウント設定');
  popupEvent.closePopup = function(){
    $.ajax({
      type: 'post',
      data: {
        id:id,
        m_companies_id:m_companies_id,
        m_users_id:m_users_id
      },
      cache: false,
      url: "<?= $this->Html->url('/MAgreementLists/remoteDeleteCompany') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/MAgreementLists/index') ?>";
      }
    });
  };
}

</script>

<div id='agreementList_idx'>
  <div id='agreementList_add_title'>
    <div class="fLeft"><i class="fa fa-home fa-2x" aria-hidden="true"></i></div>
    <h1>契約状況</h1>
  </div>
  <?= $this->Html->link('登録',['controller'=>'MAgreementLists', 'action' => 'add'],['escape' => false, 'id' => 'searchRefine','class' => 'action_btn']); ?>
<div id='agreementList_list' class="p20trl">
  <table>
    <thead>
      <tr>
        <th style="width:25em;">会社名</th>
        <th style="width:25em;">キー</th>
        <th style="width:25em;">プラン</th>
        <th style="width:25em;">ID数</th>
        <th width="1%"></th>
      </tr>
    </thead>
    <?php foreach((array)$companyList as $key => $val): ?>
      <?php
            $id = "";
            if ($val['MAgreementList']['id']) {
              $id = $val['MAgreementList']['id'];
            }
            $m_companies_id = $val['MCompany']['id'];
            $m_users_id = $val['MUser']['id'];
          ?>
      <tbody>
        <?php if(h($val['MCompany']['trial_flg']) == 0) { ?>
          <tr ondblclick= "location.href = '<?=$this->Html->url(array('controller' => 'MAgreementLists', 'action' => 'edit',$val['MAgreementList']['id']))?>';">
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
            <td><?=h($val['MUser']['companyId'])?>/<?=h($val['MCompany']['limit_users'])?></td>
            <td>
            <i class="fa fa-times fa-2x" aria-hidden="true" a href="javascript:void(0)" id="delete" onclick="remoteDeleteCompany('<?=$id?>','<?=$m_companies_id?>','<?=$m_users_id?>')"></i>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    <?php endforeach; ?>
  </table>
  </div>
</div>
