<?php
$gallaryPath = C_PATH_NODE_FILE_SERVER.'/img/widget/';
?>
<style>
	<?php foreach((array)$cssStyle as $key => $val): ?>
		<?php printf("%s { %s }", $key, $this->Html->style($val)); ?>
	<?php endforeach; ?>
</style>
<script type="text/javascript">
var imageList = document.querySelectorAll('#gallaryImage li');
var clickEvnt = function(){
    popupEvent.customizeBtn(this.getAttribute("data-name"));
    popupEvent.close();
}
for(var i = 0; imageList.length > i; i++) {
  imageList[i].addEventListener("click", clickEvnt);
}
</script>
<ul id="gallaryImage">
  <li data-name="op01.jpg">
    <img src="<?=$gallaryPath?>op01.jpg" alt="オペレータ１" width="62" height="70">
  </li>
  <li data-name="op02.jpg">
    <img src="<?=$gallaryPath?>op02.jpg" alt="オペレータ２" width="62" height="70">
  </li>
  <li data-name="op03.jpg">
    <img src="<?=$gallaryPath?>op03.jpg" alt="オペレータ３" width="62" height="70">
  </li>
  <li class="icon-view" data-name="fa-comments normal">
    <i class="icon fal fa-comments bgOn"/>
  </li>
  <li class="icon-view" data-name="fa-phone normal">
    <i class="icon fal fa-phone bgOn"/>
  </li>
  <li class="icon-view" data-name="fa-robot normal">
    <i class="icon fal fa-robot bgOn"/>
  </li>
  <li class="icon-view" data-name="fa-comments invert">
    <i class="icon fal fa-comments bgOff"/>
  </li>
  <li class="icon-view" data-name="fa-phone invert">
    <i class="icon fal fa-phone bgOff"/>
  </li>
  <li class="icon-view" data-name="fa-robot invert">
    <i class="icon fal fa-robot bgOff"/>
  </li>
</ul>
