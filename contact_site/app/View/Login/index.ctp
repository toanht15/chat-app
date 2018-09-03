<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 232, 'height' => 59, 'style'=>'margin: 30px auto 10px auto; display: block'))?>
        <div class="form_area">
            <?= $this->element('Login/entry') ?>
            <?= $this->Html->link('パスワードをお忘れの場合','javascript:void(0)', ['id' => 'resetPasswordBtn']) ?>
        </div>
    </div>
</div>
