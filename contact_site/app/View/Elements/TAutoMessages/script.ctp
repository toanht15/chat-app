<script type="text/javascript">
var openList = null;
$(document).ready(function(){

  $(document).on("click", "li.triggerItem h4", function(){
    openList(this);
  });

  openList = function(elm){
    var target = null;
    if ( elm === undefined ) {
      target = $("li.triggerItem:last-child");
    }
    else {
      target = $(elm).parent("li.triggerItem");
    }
    if (!target.is(".selected")) {
      $("li.triggerItem.selected").css('height', 34 + "px").removeClass("selected");
      target.css('height', target.children("div").prop("offsetHeight") + 34 + "px").addClass("selected");
    }
    else {
      $("li.triggerItem.selected").css('height', 34 + "px").removeClass("selected");
    }
  };

  function saveAct(){
    $('#MUserIndexForm').submit();
  }
});
</script>
