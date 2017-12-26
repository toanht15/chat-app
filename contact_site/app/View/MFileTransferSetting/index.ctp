<?php
//
$fileTypeAreaHiddenClass = "";
if ( !(!empty($this->data['MFileTransferSetting']['type']) && strcmp($this->data['MFileTransferSetting']['type'],C_FILE_TRANSFER_SETTING_TYPE_EXTEND) === 0) ) {
  $fileTypeAreaHiddenClass = "hidden";
}
?>
<?php echo $this->element('MFileTransferSetting/script'); ?>
<div id='mfiletransfersetting_idx' class="card-shadow">

  <div id='mfiletransfersetting_title'>
    <div class="fLeft"><?= $this->Html->image('file_transfer_setting_top.png', array('alt' => 'ファイル送信設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>ファイル送信設定</h1>
  </div>

  <div id='mfiletransfersetting_content' class="p20x">
    <?= $this->Form->create('MFileTransferSetting', ['type' => 'post', 'url' => ['controller' => 'MFileTransferSetting', 'action' => 'index', '']]); ?>
    <?= $this->Form->input('MFileTransferSetting.id', ['type' => 'hidden']); ?>
    <section>
      <h3>ファイル送信許可設定</h3>
      <ul>
        <li>
          <div id='setting_type_area'>
            <label style="display:inline-block;">
              <?php
              $settings = [
                'type' => 'radio',
                'options' => $typeSelect,
                'default' => C_FILE_TRANSFER_SETTING_TYPE_BASIC,
                'legend' => false,
                'separator' => '</label><br><label style="display:inline-block;">',
                'label' => false,
                'div' => false,
                'error' => false,
                'class' => 'pointer'
              ];
              echo $this->Form->input('MFileTransferSetting.type',$settings);
              ?>
            </label>
          </div>
          <?php if (!empty($errors['type'])) echo "<li class='error-message'>" . h($errors['type'][0]) . "</li>"; ?>
        </li>
      </ul>
      <div id="extension_setting_area" class="<?=$fileTypeAreaHiddenClass?>">
        <s>※ 複数設定する場合は、カンマを使ってファイルの種類を区切ります。</s>
        <?= $this->Form->textarea('MFileTransferSetting.allow_extensions',[
            'placeholder' => 'pdf,ppt,pptx,jpg,png,gif',
            'error' => false
        ]);?>
        <?php if (!empty($errors['allow_extensions'])) echo "<li class='error-message'>" . h($errors['allow_extensions'][0]) . "</li>"; ?>
      </div>
    </section>
    <?php $this->Form->end(); ?>
  </div>
  <div id="mtransfersetting_action" class="fotterBtnArea">
    <?= $this->Html->link('元に戻す', 'javascript:void(0)', ['id' => 'reloadBtn','class' => 'whiteBtn btn-shadow']) ?>
    <?= $this->Html->link('更新', 'javascript:void(0)', ['id' => 'updateBtn', 'class' => 'greenBtn btn-shadow']) ?>
    <?= $this->Html->link('dummy', 'javascript:void(0)', ['onclick' => '', 'class' => 'whiteBtn btn-shadow', 'style' => 'visibility: hidden;']) ?>
  </div>
</div>
