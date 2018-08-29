<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 232, 'height' => 59, 'style'=>'margin: 30px auto 10px auto; display: block'))?>
        <div class="form_area">
          <?php
          echo $this->Form->create('TResetPasswordInformation', array('type' => 'post', 'url' => array('controller' => 'Login', 'action' => 'confirmPassword')));
          echo $this->Form->input('new_password', ['label' => false, 'placeholder' => '新しいパスワード', 'required' => false]);
          echo $this->Form->input('confirmpassword', ['label' => false, 'placeholder' => 'パスワードの確認', 'required' => false]);
          echo $this->Form->hidden('authentication_code',['value' => $authentication_code]);
          echo $this->Form->hidden('parameter',['value' => $parameter]);
          echo $this->Form->end(['label' => 'パスワードを変更','id' => 'MUserFormButton','style' => 'cursor:grab;margin-bottom:20px']);
          ?>
        </div>
    </div>
</div>
