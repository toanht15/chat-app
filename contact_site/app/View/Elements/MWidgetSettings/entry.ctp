<?php
$editFlg = true;
if ( !empty($this->data['MUser']['edit_password']) ) {
  $editFlg = false;
}
?>
<?= $this->Form->create('MUser', array('type' => 'post', 'url' => array('controller' => 'MWidgetSettings', 'action' => 'index'))); ?>
	<div class="form01">
		<!-- /* 基本情報 */ -->
		<section>
			<?= $this->Form->input('id', array('type' => 'hidden')); ?>
			<ul>
				<li>
					<div class="labelArea fLeft"><span><label>ウィジェットの表示</label></span></div>
					<?= $this->Form->input('display_status', array('type' => 'radio', 'fieldset' => false, 'separator' => '&nbsp;', 'legend' => false, 'options' => ['する', 'しない'], 'label' => false, 'error' => false)) ?>
				</li>
				<?php if ( $this->Form->isFieldError('user_name') ) echo $this->Form->error('user_name', null, array('wrap' => 'li')); ?>
				<li>
					<div class="labelArea fLeft"><span><label>対応できるオペレーターが居ないときのウィジェットの表示</label></span></div>
					<?= $this->Form->input('display_status', array('type' => 'radio', 'fieldset' => false, 'separator' => '&nbsp;', 'legend' => false, 'options' => ['する', 'しない'], 'label' => false, 'error' => false)) ?>
				</li>
				<?php if ( $this->Form->isFieldError('display_name') ) echo $this->Form->error('display_name', null, array('wrap' => 'li')); ?>
			</ul>
		</section>

		<!-- /* ウィジェットの文言設定 */ -->
		<section>
			<div id="set_widget_detail_area">
				<div id="widget_detail_area">
					<ul>
						<li>
							<div class="labelArea fLeft"><span><label>ウィジェットタイトル</label></span></div>
							<?= $this->Form->input('current_password', array('placeholder' => 'ウィジェットタイトル', 'div' => false, 'label' => false, 'maxlength' => 12, 'error' => false)) ?>
						</li>
						<?php if ($this->Form->isFieldError('current_password')) echo $this->Form->error('current_password', null, array('wrap' => 'li')); ?>
						<li>
							<div class="labelArea fLeft"><span><label>お問い合わせ先電話番号</label></span></div>
							<?= $this->Form->input('new_password', array('placeholder' => 'お問い合わせ先電話番号', 'div' => false, 'label' => false, 'maxlength' => 12, 'error' => false)) ?>
						</li>
						<?php if ($this->Form->isFieldError('new_password') ) echo $this->Form->error('new_password', null, array('wrap' => 'li')); ?>
						<li>
							<div class="labelArea fLeft"><span><label>受付時間指定</label></span></div>
							<?= $this->Form->input('display_status', array('type' => 'radio', 'fieldset' => false, 'separator' => '&nbsp;', 'legend' => false, 'options' => ['する', 'しない'], 'label' => false, 'error' => false)) ?>
						</li>
						<li>
							<div class="labelArea fLeft"><span><label>受付時間の表記</label></span></div>
							<?= $this->Form->input('confirm_password', array('placeholder' => 'confirm password', 'div' => false, 'label' => false, 'maxlength' => 12, 'error' => false)) ?>
						</li>
						<li>
							<div class="labelArea fLeft"><span><label>ウィジェット本文</label></span></div>
							<?= $this->Form->input('confirm_password', array('type' => 'textarea', 'placeholder' => '本文', 'div' => false, 'label' => false, 'maxlength' => 12, 'error' => false)) ?>
						</li>
						<?php if ($this->Form->isFieldError('confirm_password') ) echo $this->Form->error('confirm_password', null, array('wrap' => 'li')); ?>
					</ul>
				</div>

				<div id="sample_widget_area">
					aaa
				</div>
			</div>

		</section>

		<!-- /* 操作 */ -->
		<section>
			<div id="m_widget_setting_action">
				<?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
			</div>
		</section>

	</div>
<?= $this->Form->end(); ?>
