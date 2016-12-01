<script type="text/javascript">
//契約・使用表示
function ChangeTab(tabname) {
  // 全部消す
  document.getElementById('tab1').style.display = 'none';
  document.getElementById('tab2').style.display = 'none';
  // 指定箇所のみ表示
  if(tabname){
    document.getElementById(tabname).style.display = 'table-row-group';
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
});
</script>