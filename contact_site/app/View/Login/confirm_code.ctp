<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 232, 'height' => 59, 'style'=>'margin: 30px auto 0 auto; display: block'))?>
        <div class="form_area">
        <p id="description">認証コードを入力してください</p>
          <?php
          echo $this->Form->create('MUser', array('type' => 'post', 'url' => array('controller' => 'Login', 'action' => 'confirmCode')));
          echo $this->Form->input('authentication_code', ['label' => false, 'placeholder' => '認証コード', 'required' => false]);
          echo $this->Form->hidden('parameter',['value' => $_SERVER["REQUEST_URI"]]);
          echo $this->Form->end(['label' => 'パスワードの再設定','id' => 'MUserFormButton','style' => 'cursor:grab']);
          ?>
        </div>
    </div>
</div>
