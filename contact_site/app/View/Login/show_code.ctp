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
  <div id="content-area">
      <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 116, 'height' => 30, 'style'=>'margin: 5px 0 0 10px; display: block'))?>
      <div id = "content-area-title" style = "margin-top: 30px;font-weight: bold;font-size: 21px;">送信したメールをご確認ください</div>
      <div class="description_area">
        <p>メールアドレスにパスワード再設定用ＵＲＬを送信いたしました。<br>
           送信されたＵＲＬにアクセスし、<span class="red">以下の認証コードを入力してください。</span><br>
           ※認証コードの有効期限は24時間です</p>
      <div id="code-title">認証コード</div>
      <div id="authentication-code"><?=$authenticationCode?></div>
    </div>
  </div>
</div>