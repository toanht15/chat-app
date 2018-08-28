<script type = "text/javascript">
  $(function(){
    //エラー数2つ
    if(<?= $errorNumbers ?> === 2) {
      $('#content-area-edit').css('height',445);
    }
    //エラー数1つ
    if(<?= $errorNumbers ?> === 1){
      $('#content-area-edit').css('height',405);
    }
    if(<?= $errorNumbers ?> === 0){
      $('#content-area-edit').css('height',380);
    }
  });
</script>
<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 116, 'height' => 30, 'style'=>'margin: 5px 0 0 10px; display: block'))?>
        <div id = "content-area-title" style = "margin-top: 30px;font-weight: bold;font-size: 21px;">パスワードの再設定を行います</div>
        <div class="form_area">
          <p>アカウントに登録されているメールアドレスを入力してください。</p>
          <?php
          echo $this->Form->create('MUser', array('type' => 'post', 'url' => array('controller' => 'Login', 'action' => 'resetPassword')));
          echo $this->Form->input('mail_address', ['label' => false,'placeholder' => 'Mail Address']);
          echo $this->Form->hidden('id');
          echo $this->Form->hidden('m_companies_id');
          echo $this->Html->link('送信','javascript:void(0)', ['id' => 'MUserFormButton']);
          echo $this->Form->end();
          ?>
        </div>
    </div>
</div>