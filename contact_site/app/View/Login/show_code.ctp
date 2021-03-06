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
  <div id="content-area-wide">
      <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 139, 'height' => 36, 'style'=>'margin: 5px 0 0 10px; display: block'))?>
      <div id = "content-area-title" style = "margin-top: 30px;font-weight: bold;font-size: 21px;">パスワード再設定用のメールを送信しました</div>
      <div class="description_area">
        <p style="margin-top:16px"><b><?=$mailAddress?></b> にメールを送信しました。</p>
        <div id="code-title">認証コード</div>
        <div id="authentication-code"><?=$authenticationCode?></div>
        <p class="m_bottom" style="line-height: 2.5;">メール本文に記載されたＵＲＬにアクセスし、<span class="red">上記の認証コードを入力してください。</span><br>
           <span class="red">※認証コードの有効期限は24時間となっております</span></p>
    </div>
  </div>
</div>