<script type="text/javascript">
$(document).ready(function(){
  $("#setTriggerList li").click(function(){
    var target = $(this);
    if (!target.is(".selected")) {
      $("#setTriggerList .selected").css('height', 34 + "px").removeClass("selected");
      $(this).css('height',$(this).children("div").prop("offsetHeight") + 34 + "px").addClass("selected");
    }
    else {
      $("#setTriggerList .selected").css('height', 34 + "px").removeClass("selected");
    }
  });

  function saveAct(){
    $('#MUserIndexForm').submit();
  }
});
</script>
