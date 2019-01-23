<?php
$galleryPath = C_PATH_NODE_FILE_SERVER.'/img/widget/';
switch( $iconType ) {
  case "1":
    $imagePrefix = "op";
    $iconPrefix = "fi_main";
    break;
  case "2":
  case "3":
    $imagePrefix = "icon_op";
    $iconPrefix = "fi_icon";
    break;
  default:
    $imagePrefix = "op";
    $iconPrefix = "fi_main";
}

$imagePath = $galleryPath.$imagePrefix;
?>
<style>
	<?php foreach((array)$cssStyle as $key => $val): ?>
		<?php printf("%s { %s }", $key, $this->Html->style($val)); ?>
	<?php endforeach; ?>
</style>
<script type="text/javascript">
var imageList = document.querySelectorAll('#gallaryImage li');
var clickEvnt = function(){
    popupEvent.customizeBtn(this.getAttribute("data-name"), this.getAttribute("data-galleryType"));
    popupEvent.moveType = "moment";
    popupEvent.close();
};
for(var i = 0; imageList.length > i; i++) {
  imageList[i].addEventListener("click", clickEvnt);
}
</script>
<ul id="gallaryImage">
  <li class="<?=$imagePrefix?>" data-name="<?=$imagePrefix?>01.jpg" data-galleryType="<?=$iconType?>">
    <img src="<?=$imagePath?>01.jpg" alt="オペレータ１">
  </li>
  <li class="<?=$imagePrefix?>" data-name="<?=$imagePrefix?>02.jpg" data-galleryType="<?=$iconType?>">
    <img src="<?=$imagePath?>02.jpg" alt="オペレータ２">
  </li>
  <li class="<?=$imagePrefix?>" data-name="<?=$imagePrefix?>03.jpg" data-galleryType="<?=$iconType?>">
    <img src="<?=$imagePath?>03.jpg" alt="オペレータ３">
  </li>
  <li class="icon-view" data-name="fa-comments normal <?=$iconPrefix?>" data-galleryType="<?=$iconType?>">
    <i class="<?=$iconPrefix?> fal fa-comments bgOn"/>
  </li>
  <li class="icon-view" data-name="fa-comments-alt normal <?=$iconPrefix?>" data-galleryType="<?=$iconType?>">
    <i class="<?=$iconPrefix?> fal fa-comments-alt bgOn"/>
  </li>
  <li class="icon-view" data-name="fa-comment-lines normal <?=$iconPrefix?>" data-galleryType="<?=$iconType?>">
    <i class="<?=$iconPrefix?> fal fa-comment-lines bgOn"/>
  </li>
  <li class="icon-view" data-name="fa-comment-alt-lines normal <?=$iconPrefix?>" data-galleryType="<?=$iconType?>">
    <i class="<?=$iconPrefix?> fal fa-comment-alt-lines bgOn"/>
  </li>
  <li class="icon-view" data-name="fa-phone normal <?=$iconPrefix?>" data-galleryType="<?=$iconType?>">
    <i class="<?=$iconPrefix?> fal fa-phone bgOn"/>
  </li>
  <li class="icon-view" data-name="fa-robot normal <?=$iconPrefix?>" data-galleryType="<?=$iconType?>">
    <i class="<?=$iconPrefix?> fal fa-robot bgOn"/>
  </li>
</ul>
