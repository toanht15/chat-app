<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 232, 'height' => 59, 'style'=>'margin: 30px auto 10px auto; display: block'))?>
        <div class="form_area">
            <?= $this->element('Login/entry') ?>
            <?= $this->Html->link('パスワードをお忘れの場合',['action' => 'resetPassword' ], array('style'=>'display: flex; justify-content:start; vertical height: 13px; margin-top: 10px; margin-bottom: 20px; font-size: 13px;color:#A0BDD2;')) ?>
        </div>
    </div>
</div>
