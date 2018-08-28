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
  window.onload = function(){
    if (typeof MUserFormButton !== 'undefined'){
      MUserFormButton.addEventListener('click', elmEv.submit.func);
    }
    //全部上記のfunc内に処理を書いておけば平和になるのでは?
    $('#MUserPasswordInput').on('keyup', function(e){
      if(e.keyCode === 13) {
        elmEv.submit.func();
      }
    });
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
