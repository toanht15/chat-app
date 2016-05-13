<?php echo $this->Html->script('handlebars-v4.0.5.js'); ?>
<script id="entry-template" type="text/x-handlebars-template">
  <div class="entry">
    <h1>{{title}}</h1>
    <div class="body">
      {{body}}
    </div>
  </div>
</script>
<script type="text/javascript">
$("#entry-template").load(function(){
  var source   = $("#entry-template").html();
  var template = Handlebars.compile(source);
  console.log(template({
     title: "All about <p> Tags",
     body: "<p>This is a post about &lt;p&gt; tags</p>"
  }));
});
</script>

