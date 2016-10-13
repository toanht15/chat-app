<?php
$headerNo = 1;
?>
<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('MShareDisplaySettings/script'); ?>

<div id='MShareDisplaySettings_idx' class="card-shadow">

  <div id='MShareDisplaySettings_add_title'>
    <div class="fLeft"><?= $this->Html->image('dictionary_g.png', array('alt' => 'キャンペーン管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
      <h1>表示除外設定<span id="sortMessage"></span></h1>
    </div>
    <div id='script_setting_content' class="p20x">
    <h2><?= mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．除外パラメータ設定</h2>
    <section>
      <pre>リアルタイムモニタや履歴にて表示不要なURLパラメータ名を指定します。&#10;複数指定する場合は改行して入力してください。</pre>
    </section>

    <?= $this->Form->create('MShareDisplaySetting', ['type' => 'post', 'url' => ['controller' => 'MShareDisplaySettings', 'action' => 'index']]); ?>
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <?= $this->Form->input('exclude_params', [
      'type' => 'textarea',
      'class' => 'ExclusionContent',
      'div' => false,
      'cols' => 55,
      'rows' => 18,
      'label' => false,
      'error' => false
    ]) ?>

    <div id='script_setting_content' class="p20x">
    <h2><?= mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．除外IPアドレス設定</h2>
    <section>
      <pre>リアルタイムモニタや履歴にて表示不要なIPアドレスを指定します。&#10;なお、CIDRを用いたIPアドレスの範囲指定も可能です。&#10; 例：「192.192.192.0/24」と入力した場合、192.192.192.0～192.192.192.255が一括で除外されます。</pre>
    </section>

    <?= $this->Form->create('MShareDisplaySetting', ['type' => 'post', 'url' => ['controller' => 'MShareDisplaySettings', 'action' => 'index'] ]); ?>
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <?= $this->Form->input('exclude_ips', [
      'type' => 'textarea',
      'class' => 'ExclusionContent',
      'div' => false,
      'cols' => 55,
      'rows' => 18,
      'label' => false,
      'error' => false
    ]) ?>
  </div>
</div>
<?= $this->Form->end(); ?>

<!-- /* 操作 */ -->
  <section>
    <div id="m_widget_setting_action" class="fotterBtnArea">
      <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
    </div>
  </section>

