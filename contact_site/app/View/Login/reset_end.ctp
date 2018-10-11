<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', ['alt' => 'アイコン', 'id' => 'logo_center'])?>
        <div class="form_area">
          <p id="reset_ended">パスワードの変更が完了しました</p>
          <?php echo $this->Html->link('ログイン画面に戻る','Login/index', ['id' => 'MUserFormButton','class' => 'm_bottom']);?>
        </div>
    </div>
</div>
