<script type="text/javascript">
//契約・使用表示
function ChangeTab(tabname) {
  // 契約アカウント見えないようにする
  var tab1 = document.getElementsByClassName('tab1');
  for(var i = 0; i < tab1.length; i++) {
    tab1[i].style.display = "none";
  }

  //試験用アカウントは見えないようにする
  var tab2 = document.getElementsByClassName('tab2');
  for(var i = 0; i < tab2.length; i++) {
    tab2[i].style.display = "none";
  }
  // 指定箇所のみ表示
  if(tabname){
    var tab = document.getElementsByClassName(tabname);
    for(var i = 0; i < tab.length; i++) {
      tab[i].style.display = "table-row-group";
    }
  }
}

//契約・使用表示
$(function(){
  $('#agreement_tag').click(function(){
    $(this).addClass("on");
    $('#trial_tag').removeClass("on");
  });
  $('#trial_tag').click(function(){
    $(this).addClass("on");
    $('#agreement_tag').removeClass("on");
  });

//試験用アカウント見えなくする
  var tab2 = document.getElementsByClassName('tab2');
  for(var i = 0; i < tab2.length; i++) {
    tab2[i].style.display = "none";
  }
});
</script>