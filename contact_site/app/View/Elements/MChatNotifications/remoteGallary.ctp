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
  <li data-name="popup_icon_red.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_red.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_pink.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_pink.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_orange.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_orange.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_light_blue.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_light_blue.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_blue.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_blue.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_purple.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_purple.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_light_green.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_light_green.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_green.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_green.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_glay.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_glay.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
  <li data-name="popup_icon_black.png">
    <?= $this->Html->image(C_PATH_NOTIFICATION_IMG_DIR."popup_icon_black.png", ['alt'=>'通知画像１', 'width'=>65, 'height'=>65]) ?>
  </li>
</ul>
