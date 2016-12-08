<script type = "text/javascript">

function saveFile(){
  location.href = "<?=$this->Html->url(array('controller' => 'Agreements', 'action' => 'add'))?>";
}

//パスワード自動生成
function saveAct(){
  var key = $('#AgreementSiteKey').val();
  $('#mailaddress').val(key+"@ml.jp");
  if ($('#AgreementTestUse').prop('checked')) {
    var day = $('AgreementUseStart').val();
    var d = new Date($('#AgreementUseStart').val());
    var endDate = d.getFullYear() + '/' + (d.getMonth() + 1) + '/' + (d.getDate() + 14);
    $('#AgreementUseEnd').val(endDate);
    console.log($('#AgreementUseEnd').val());
  }
  else {
    var day = $('AgreementUseStart').val();
    var d = new Date($('#AgreementUseStart').val());
    var endDate = (d.getFullYear() + 1) + '/' + (d.getMonth() + 1) + '/' + (d.getDate() - 1);
    $('#AgreementUseEnd').val(endDate);
    console.log($('#AgreementUseEnd').val());
  }
}



//パスワード自動生成
function createPassword(){
  var str = random();
  $('#AgreementPassword').val(str);
}

//パスワード初期値自動生成
function　passwordLoad() {
  var str = random();
  $('#AgreementPassword').val(str);
}

window.onload = passwordLoad;

</script>

<div id='agreement_idx'>
  <div id='agreement_add_title'>
    <div class="fLeft"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></div>
    <h1>契約登録</h1>
  </div>


<?= $this->Form->create('Agreement', array('type' => 'post', 'url' => array('controller' => 'Agreements', 'action' => 'index'))); ?>
  <div class="form01">
    <section>
      <?= $this->Form->input('id', array('type' => 'hidden')); ?>
      <ul class='formArea'>
      <!-- /* 申込日 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>申込日</label></span></div>
          <?= $this->Form->input('applicationDay', array('type' => 'date','dateFormat' => 'YMD','monthNames' => false,'div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* 会社名 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>会社名</label></span></div>
          <?= $this->Form->input('companyName', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* サイトキー */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>サイトキー</label></span></div>
          <?= $this->Form->input('siteKey', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* テスト利用 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>テスト利用</label></span></div>
            <?= $this->Form->checkbox('testUse') ?>
        </li>
        <!-- 契約プラン -->
      <li>
        <span class="require"><label>契約プラン</label></span>
        <?php $plans=array('1'=>'フルプラン','2'=>'チャットプラン','3'=>'画面共有プラン'); ?>
        <?= $this->Form->input('agreementPlan', array('type' => 'select', 'options' => $plans, 'default' => 1,'label'=>false)) ?>
      </li>
        <!-- /* 契約ID数 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>契約ID数</label></span></div>
          <?= $this->Form->input('agreementId', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* 契約開始日 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>契約開始日</label></span></div>
          <?= $this->Form->input('useStart', array('div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* 契約終了日 */ -->
        <li>
          <?= $this->Form->input('useEnd', array('type' => 'hidden','div' => false, 'label' => false, 'maxlength' => 50)) ?>
        </li>
        <!-- /* メールアドレス */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>スーパー管理者</label></span></div>
          <?= $this->Form->input('agreementPlan', array('div' => false, 'label' => 'メールアドレス', 'maxlength' => 50,'id' => 'mailaddress')) ?>
          </li>
          <!-- /* パスワード自動生成 */ -->
          <li>
           <div class="labelArea fLeft"><span><label></label></span></div>
           <?= $this->Form->input('password', array('type' => 'textbox', 'div' => false, 'label' => 'パスワード', 'maxlength' => 50)) ?>
          <?= $this->Html->link('自動生成','javascript:void(0)',array('escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'createPassword()'));?>
        </li>
        <!-- /* 備考 */ -->
      <li>
        <span class="require"><label>備考</label></span>
        <?=$this->Form->input('remarks', ['type'=>'textarea','label' => false,'div' => false,'maxlength'=>300,'cols' => 25,'rows' => 5])?>
      </li>
        <!-- /* jsファイル作成 */ -->
        <li>
          <section>
            <div id="agreement-button">
              <?= $this->Html->link('一覧',['controller'=>'AgreementLists', 'action' => 'index'],['escape' => false, 'id' => 'searchRefine','class' => 'normal_btn']); ?>
              <?= $this->Html->link('登録', 'javascript:void(0)', ['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'saveAct()']); ?>
            </div>
          </section>
        </li>
      </ul>
    </section>
  </div>
<?= $this->Form->end(); ?>

</div>