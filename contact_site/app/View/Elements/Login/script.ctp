<script type="text/javascript">
  var elmEv;
  (function(){
    elmEv = {
      submit: {
        func: function () {
          if (typeof MUserIndexForm !== 'undefined'){
            MUserIndexForm.submit();
          }else if (typeof MUserResetPasswordForm !== 'undefined'){
            MUserResetPasswordForm.submit();
          }
        }
      }
    };
  })();
  $(document).ready(function(){
    //エラーが表示されてるときは入力ボックス下の余白を取り除く処理
    if($('.error-message')[0]){
      $('#TResetPasswordInformationMailAddress').css('margin-bottom',0);
      $('input.form-error').css('margin-bottom',0);
      $('#content-area').css('padding','10px 31px 0 31px');
    }
  });
  window.onload = function(){
    //IEだった場合はボタンのカーソルを変更する処理
    var userAgent = window.navigator.userAgent.toLowerCase();
    if( userAgent.match(/(msie|MSIE)/) || userAgent.match(/(T|t)rident/) ) {
      $('#MUserFormButton').css('cursor','pointer');
    }

    if (typeof MUserFormButton !== 'undefined'){
      MUserFormButton.addEventListener('click', elmEv.submit.func);
    }
    $('#MUserPasswordInput').on('keyup', function(e){
      if(e.keyCode === 13) {
        elmEv.submit.func();
      }
    });
    if(<?=strcmp($this->action,"resetPassword")?>){
    // History API が使えるブラウザかどうかをチェック
      if( window.history && window.history.pushState ){
        //. ブラウザ履歴に１つ追加
        history.pushState( "nohb", null, "" );
        $(window).on( "popstate", function(event){
          //. このページで「戻る」を実行
          if( !event.originalEvent.state ){
            //. もう一度履歴を操作して終了
            history.pushState( "nohb", null, "" );
            return;
          }
        });
      }
    }
  };
  //初期パスワード変更
  var isProcess = false;
  function saveAct(){
    if(!isProcess) {
      isProcess = true;
      $('#MUserEditPasswordForm').submit();
    }
  }
</script>
