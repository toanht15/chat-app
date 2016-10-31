<?php echo $this->element('DisplayExclusions/script'); ?>

<div id='display_exclusions_idx' class="card-shadow">

  <div id='display_exclusions_add_title'>
      <div class="fLeft">
        <?= $this->Html->image('exclusion_g.png', array('alt' => 'キャンペーン管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?>
      </div>
      <h1>表示除外設定<span id="sortMessage"></span></h1>
  </div>
  <div id='display_exclusions_form' class="p20x">
    <?= $this->Form->create('MCompany', ['type' => 'post', 'url' => ['controller' => 'DisplayExclusions', 'action' => 'index']]); ?>
      <?= $this->Form->input('id', array('type' => 'hidden')); ?>

      <h3>１．除外パラメータ設定</h3>
      <div class="content">
        <pre>リアルタイムモニタや履歴にて表示不要なURLパラメータ名を指定します。&#10;複数指定する場合は改行して入力してください。</pre>
        <?= $this->Form->input('exclude_params', [
          'type' => 'textarea',
          'div' => true,
          'cols' => 55,
          'rows' => 15,
          'label' => false
        ]) ?>
      </div>

      <h3>２．除外IPアドレス設定</h3>
      <div class="content">
        <pre>リアルタイムモニタや履歴にて表示不要なIPアドレスを指定します。&#10;なお、CIDRを用いたIPアドレスの範囲指定も可能です。&#10; 例：「192.192.192.0/24」と入力した場合、192.192.192.0～192.192.192.255が一括で除外されます。
複数指定する場合は改行して入力してください。
※ 除外対象としたIPアドレスの消費者へは、ウィジェットの表示はされず、履歴も残りません</pre>
        <?= $this->Form->input('exclude_ips', [
          'type' => 'textarea',
          'div' => true,
          'cols' => 55,
          'rows' => 15,
          'label' => false
        ]) ?>
      </div>
    <?= $this->Form->end(); ?>
  </div>

<!-- /* 操作 */ -->
    <div id="m_widget_setting_action" class="fotterBtnArea">
      <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
    </div>

