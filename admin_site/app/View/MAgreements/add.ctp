<script type = "text/javascript">

function saveFile(){
  location.href = "<?=$this->Html->url(array('controller' => 'MAgreements', 'action' => 'add'))?>";
}

//パスワード自動生成
function saveAct(){
  var key = $('#MAgreementCompanyKey').val();
  $('#MAgreementMailAddress').val(key+"@ml.jp");
  $("#MAgreementMailAddress").prop("disabled", false);
  if ($('#MAgreementTrialFlg').prop('checked')) {
    var day = $('MAgreementUseStart').val();
    var d = new Date($('#MAgreementUseStart').val());
    var endDate = d.getFullYear() + '/' + (d.getMonth() + 1) + '/' + (d.getDate() + 14);
    $('#MAgreementUseEnd').val(endDate);
    console.log($('#MAgreementUseEnd').val());
  }
  else {
    var day = $('MAgreementUseStart').val();
    var d = new Date($('#MAgreementUseStart').val());
    var endDate = (d.getFullYear() + 1) + '/' + (d.getMonth() + 1) + '/' + (d.getDate() - 1);
    $('#MAgreementUseEnd').val(endDate);
    console.log($('#MAgreementUseEnd').val());
  }
  document.getElementById('MAgreementAddForm').submit();
}



//パスワード自動生成
function createPassword(){
  var str = random();
  $('#MAgreementAdminPassword').val(str);
}

//パスワード初期値自動生成
function　passwordLoad() {
  var str = random();
  $('#MAgreementAdminPassword').val(str);
}

window.onload = passwordLoad;

$(function(){
  $("#MAgreementMailAddress").prop("disabled", true);
});
</script>

<div id='agreement_idx'>
  <div id='agreement_add_title'>
    <div class="fLeft"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></div>
    <h1>契約登録</h1>
  </div>


<?= $this->Form->create('MAgreement', array('type' => 'post')); ?>
  <div class="form01">
    <section>
      <?= $this->Form->input('id', array('type' => 'hidden')); ?>
      <ul class='formArea'>
      <!-- /* 申込日 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>申込日</label></span></div>
          <?= $this->Form->input('application_day', array('type' => 'date','dateFormat' => 'YMD','monthNames' => false,'div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* 会社名 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>会社名</label></span></div>
          <?= $this->Form->input('company_name', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
           <?php if (!empty($errors['company_name'])) echo "<li class='error-message'>" . h($errors['company_name'][0]) . "</li>"; ?>
        </li>
        <!-- /* サイトキー */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>サイトキー</label></span></div>
          <?= $this->Form->input('company_key', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
          <?php if (!empty($errors['company_key'])) echo "<li class='error-message'>" . h($errors['company_key'][0]) . "</li>"; ?>
        </li>
        <!-- /* テスト利用 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>テスト利用</label></span></div>
            <?= $this->Form->checkbox('trial_flg') ?>
        </li>
        <!-- 契約プラン -->
      <li>
        <span class="require"><label>契約プラン</label></span>
        <?php $plans=array('1'=>'フルプラン','2'=>'チャットプラン','3'=>'画面共有プラン'); ?>
        <?= $this->Form->input('m_contact_types_id', array('type' => 'select', 'options' => $plans, 'default' => 1,'label'=>false)) ?>
      </li>
        <!-- /* 契約ID数 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>契約ID数</label></span></div>
          <?= $this->Form->input('limit_users', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
           <?php if (!empty($errors['limit_users'])) echo "<li class='error-message'>" . h($errors['limit_users'][0]) . "</li>"; ?>
        </li>
        <!-- /* 契約開始日 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>契約開始日</label></span></div>
          <?= $this->Form->input('agreement_start_day', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* 契約終了日 */ -->
        <li>
          <?= $this->Form->input('agreement_end_day', array('type' => 'hidden','div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* 申し込み情報部署名 */ -->
        <li>
          <div class="labelArea fLeft"><span><label>申し込み情報</label></span></div>
          <?= $this->Form->input('application_department', array('div' => false, 'label' => '部署名', 'maxlength' => 50)) ?>
        </li>
        <!-- /* 申し込み情報名前 */ -->
        <li>
          <div class="labelArea fLeft"><span><label></label></span></div>
          <?= $this->Form->input('application_name', array('div' => false, 'label' => '名前', 'maxlength' => 50)) ?>
        </li>
        <!-- /* 管理者情報部署名 */ -->
        <li>
          <div class="labelArea fLeft"><span><label>管理者情報</label></span></div>
          <?= $this->Form->input('administrator_department', array('div' => false, 'label' => '部署名', 'maxlength' => 50)) ?>
        </li>
        <!-- /* 管理者情報役職名 */ -->
        <li>
          <div class="labelArea fLeft"><span><label></label></span></div>
          <?= $this->Form->input('application_position', array('div' => false, 'label' => '役職名', 'maxlength' => 50)) ?>
        </li>
        <!-- /* 管理者情報名前 */ -->
        <li>
          <div class="labelArea fLeft"><span><label></label></span></div>
          <?= $this->Form->input('application_name', array('div' => false, 'label' => '名前', 'maxlength' => 50)) ?>
        </li>
        <!-- /* 設置サイト名 */ -->
        <li>
          <div class="labelArea fLeft"><span><label>設置サイト名</label></span></div>
          <?= $this->Form->input('installation_site_name', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* 設置サイトURL */ -->
        <li>
          <div class="labelArea fLeft"><span><label>設置サイトURL</label></span></div>
          <?= $this->Form->input('installation_url', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* メールアドレス */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>スーパー管理者</label></span></div>
          <?= $this->Form->input('mail_address', array('div' => false, 'label' => 'メールアドレス', 'maxlength' => 50)) ?>
           <?php if (!empty($errors['mail_address'])) echo "<li class='error-message'>" . h($errors['mail_address'][0]) . "</li>"; ?>
          </li>
          <!-- /* パスワード自動生成 */ -->
          <li>
           <div class="labelArea fLeft"><span　class="require"><label>パスワード</label></span></div>
           <?= $this->Form->input('admin_password', array('type' => 'textbox', 'div' => false, 'label' => false, 'maxlength' => 50)) ?>
          <?= $this->Html->link('自動生成','javascript:void(0)',array('escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'createPassword()'));?>
          <?php if (!empty($errors['admin_password'])) echo "<li class='error-message'>" . h($errors['admin_password'][0]) . "</li>"; ?>
        </li>
        <!-- /* 電話番号 */ -->
        <li>
          <div class="labelArea fLeft"><span><label>電話番号</label></span></div>
          <?= $this->Form->input('telephone_number', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>

        <!-- /* 備考 */ -->
      <li>
        <span><label>備考</label></span>
        <?=$this->Form->input('note', ['type'=>'textarea','label' => false,'div' => false,'maxlength'=>300,'cols' => 25,'rows' => 5])?>
      </li>
        <!-- /* jsファイル作成 */ -->
        <li>
          <section>
            <div id="agreement-button">
              <?= $this->Html->link('一覧',['controller'=>'MAgreements', 'action' => 'index'],['escape' => false, 'id' => 'searchRefine','class' => 'normal_btn']); ?>
              <?= $this->Html->link('登録', 'javascript:void(0)', ['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'saveAct()']); ?>
            </div>
          </section>
        </li>
      </ul>
    </section>
  </div>
<?= $this->Form->end(); ?>

</div>