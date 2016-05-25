<?php
$gallaryPath = C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT.'/img/widget/';
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
	<li data-name="chat.png">
		<img src="<?=$gallaryPath?>chat.png" alt="チャットイラスト" class="bgOn" width="62" height="70">
	</li>
	<li data-name="tel.png">
		<img src="<?=$gallaryPath?>tel.png" alt="テレフォンイラスト" class="bgOn" width="62" height="70">
	</li>
</ul>
