<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area-wide">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 139, 'height' => 36, 'style'=>'margin: 5px 0 0 10px; display: block'))?>
        <div id = "content-area-title" style = "margin-top: 30px;font-weight: bold;font-size: 21px;">パスワードの再設定</div>
        <div class="form_area">
          <p>アカウントに登録されているメールアドレスを入力してください。</p>
          <?php
          echo $this->Form->create('TResetPasswordInformation', array('type' => 'post', 'url' => array('controller' => 'Login', 'action' => 'resetPassword')));
          $mail_address = "";
          if( !empty($_COOKIE['CakeCookie']['mail_address'])){
            $mail_address = $_COOKIE['CakeCookie']['mail_address'];
          }
          echo $this->Form->input('mail_address', ['label' => false,'placeholder' => 'Mail Address','required' => false,'value' => $mail_address]);
          echo $this->Form->end(['label' => '送信', 'id' => 'MUserFormButton','style' =>'cursor:pointer']);
          ?>
        </div>
    </div>
<!-- </div> -->