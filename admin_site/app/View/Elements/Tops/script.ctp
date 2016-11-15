<script type="text/javascript">
function ChangeTab(tabname) {
  // 全部消す
  document.getElementById('tab1').style.display = 'none';
  document.getElementById('tab2').style.display = 'none';
  // 指定箇所のみ表示
  if(tabname){
    document.getElementById(tabname).style.display = 'table-row-group';
  }
}

$(function(){
  $('#agreement').click(function(){
    $(this).addClass("on");
    $('#trial').removeClass("on");
  });
  $('#trial').click(function(){
    $(this).addClass("on");
    $('#agreement').removeClass("on");
  });
});
</script>