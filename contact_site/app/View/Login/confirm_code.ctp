<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', ['alt' => 'アイコン', 'id' => 'logo_center'])?>
        <div class="form_area">
        <p id="description">認証コードを入力してください</p>
          <?php
          echo $this->Form->create('TResetPasswordInformation', array('type' => 'post', 'url' => array('controller' => 'Login', 'action' => 'confirmCode')));
          echo $this->Form->input('authentication_code', ['label' => false, 'placeholder' => '認証コード', 'required' => false , 'autocomplete' => 'off']);
          echo $this->Form->hidden('parameter',['value' => $parameter]);
          ?>
          <div id="error_code"><?php if(isset($errorMsg)) echo($errorMsg)?></div>
          <?php
          echo $this->Form->end(['label' => 'パスワードの再設定','id' => 'MUserFormButton','style' => 'cursor:pointer']);
          ?>
        </div>
    </div>
</div>
