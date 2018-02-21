<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area-edit">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 231, 'height' => 59, 'style'=>'margin: 30px auto 0 auto; display: block'))?>
        <div id = "content-area-title" style = "margin-top: 30px;font-weight:bold;">初期パスワードを変更してください</div>
        <div class="edit_form_area">
          <?php if ($notSupportBrowser) { ?>
            <pre style="font-size: 13px">
            <b>対応していないブラウザです。
            下記を参考に、ブラウザを選定してください。</b>

            <b>対応ブラウザ</b>
            - Google Chrome 49.0以上
            - Mozila FireFox 45.0以上
            - Internet Explorer 10.0以上
            </pre>
            <?php
            } else {
            echo $this->Form->create('MUser', array('type' => 'post', 'url' => array('controller' => 'Login', 'action' => 'editPassword')));
            echo $this->Form->input('new_password', array('label' => array('text' => '新しいパスワード','class' => 'control-label'), 'placeholder' => 'new_password','type' => 'password'));
            echo $this->Form->input('confirm_password', array('label' => array('text' => '新しいパスワード(確認用)','class' => 'control-label'), 'placeholder' => 'confirm_password', 'id' => 'MUserPasswordInput','type' => 'password'));;
            echo $this->Form->hidden('id');
            echo $this->Form->hidden('mail_address');
            echo $this->Form->hidden('m_companies_id');
            echo $this->Html->link('更新','javascript:void(0)', array('onclick' => 'saveAct()','id' => 'MUserFormButton'));
            echo $this->Form->end();
            }
            ?>

        </div>
    </div>
    <?php $this->Html->link('パスワードを忘れた方はこちら', 'javascript:void(0)', array('style'=>'display: block; height: 30px; padding: 5px; font-size: 13px; color: #E7EFF5;')) ?>
</div>