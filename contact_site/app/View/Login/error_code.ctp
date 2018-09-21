<div id="login_idx_bg"></div>
<div id="login_idx">
    <div id="content-area">
        <?= $this->element('Login/script') ?>
        <?= $this->Html->image('sinclo_logo.png', ['alt' => 'アイコン', 'id' => 'logo_center'])?>
        <div class="form_area">
          <p style="font-size:40px; margin: 0px; line-height:1em;font-weight: bold">Error</p>
          <p style="margin: 16px 0 20px 0;font-size: 13px; line-height:1.6em;">以下の原因が考えられます<br>
          ・ＵＲＬが正しく入力されていない<br>
          ・ＵＲＬの有効期限が切れている</p>
          <?php echo $this->Html->link('ログイン画面に戻る','Login/index', ['id' => 'MUserFormButton','class' => 'm_bottom']);?>
        </div>
    </div>
</div>
