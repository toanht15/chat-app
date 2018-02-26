<script type="text/javascript">
  var elmEv;
  (function(){
    elmEv = {
      submit: {
        func: function () {
          if (typeof MUserIndexForm === 'undefined') return false;
          MUserIndexForm.submit();
        }
      }
    };
    // 自身のページを履歴に追加
    location.hash = "#loginPage";
    // ページ戻り時にも自身のページを履歴に追加
    $(window).on("hashchange", function(){
      location.hash = "#loginPage";
    });
  })();
  window.onload = function(){
    if (typeof MUserFormButton === 'undefined') return false;
    MUserFormButton.addEventListener('click', elmEv.submit.func);
    $('#MUserPasswordInput').on('keyup', function(e){
      if(e.keyCode === 13) {
        elmEv.submit.func();
      }
    });
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
