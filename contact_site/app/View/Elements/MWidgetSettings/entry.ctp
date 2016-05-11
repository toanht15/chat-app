	<?= $this->Form->create('MWidgetSetting', ['type' => 'post', 'url' => ['controller' => 'MWidgetSettings', 'action' => 'index']]); ?>
		<div class="form01">
			<!-- /* 基本情報 */ -->
			<h3>１．表示設定</h3>
			<section>
			<?= $this->Form->input('id', ['type' => 'hidden']); ?>
			<ul class="settingList">
				<!-- 表示設定 -->
				<li>
					<span class="require"><label>表示する条件</label></span>
					<pre><?= $this->Form->input('display_type', ['type' => 'radio', 'options' => $widgetDisplayType, 'legend' => false, 'separator' => '<br>', 'label' => false, 'error' => false, 'div' => false]) ?></pre>
				</li>
				<?php if ( $this->Form->isFieldError('display_type') ) echo $this->Form->error('display_type', null, ['wrap' => 'li']); ?>
				<!-- 表示設定 -->
				<!-- 最大化時間設定 -->
				<li>
					<span><label>最大化する条件</label></span>
					<?= $this->Form->input('max_show_time', [
						'type' => 'number',
						'div' => false,
						'label' => false,
						'maxlength' => 2,
						'max' => 60,
						'min' => 0,
						'error' => false
					]);?><span class="numberLabel">秒後に自動で最大化する</span>
				</li>
				<?php if ( $this->Form->isFieldError('max_show_time') ) echo $this->Form->error('max_show_time', null, ['wrap' => 'li']); ?>
				<!-- 最大化時間設定 -->
				<!-- 表示位置 -->
				<li>
					<span class="require"><label>表示位置</label></span>
					<pre><?= $this->Form->input('show_position', [
							'type' => 'radio',
							'options' => $widgetPositionType,
							'legend' => false,
							'separator' => '<br>',
							'div' => false,
							'label' => false,
							'error' => false
						]) ?></pre>
				</li>
				<?php if ( $this->Form->isFieldError('show_position') ) echo $this->Form->error('show_position', null, ['wrap' => 'li']); ?>
				<!-- 表示位置 -->
			</ul>
			</section>

			<!-- /* ウィジェットの文言設定 */ -->
			<h3>２．ウィジェット詳細設定</h3>
			<section>
			<ul class="settingList">

				<!-- ウィジェットタイトル -->
				<li>
					<span class="require"><label>トップタイトル</label></span>
					<?= $this->Form->input('title', [
						'type' => 'text',
						'placeholder' => 'ウィジェットタイトル',
						'ng-model' => 'title',
						'ng-init' => 'title="' . h($this->data['MWidgetSetting']['title']) . '";',
						'div' => false,
						'label' => false,
						'maxlength' => 12,
						'error' => false
					]) ?>
				</li>
				<?php if ($this->Form->isFieldError('title')) echo $this->Form->error('title', null, ['wrap' => 'li']); ?>
				<!-- ウィジェットタイトル -->

				<!-- サブタイトル -->
				<li>
					<span class="require"><label>サブタイトル</label></span>
					<?= $this->Form->input('sub_title', [
						'type' => 'text',
						'placeholder' => 'サブタイトル',
						'ng-model' => 'sub_title',
						'ng-init' => 'sub_title="' . h($this->data['MWidgetSetting']['sub_title']) . '";',
						'div' => false,
						'label' => false,
						'maxlength' => 15,
						'error' => false
					]) ?>
				</li>
				<?php if ($this->Form->isFieldError('sub_title')) echo $this->Form->error('sub_title', null, ['wrap' => 'li']); ?>
				<!-- サブタイトル -->

				<!-- 説明文 -->
				<li>
					<span class="require"><label>説明文</label></span>
					<?= $this->Form->input('description', [
						'type' => 'text',
						'placeholder' => '説明文',
						'ng-model' => 'description',
						'ng-init' => 'description="' . h($this->data['MWidgetSetting']['description']) . '";',
						'div' => false,
						'label' => false,
						'maxlength' => 15,
						'error' => false
					]) ?>
				</li>
				<?php if ($this->Form->isFieldError('description')) echo $this->Form->error('description', null, ['wrap' => 'li']); ?>
				<!-- 説明文 -->

				<!-- メインカラー -->
				<li>
					<span class="require"><label>メインカラー</label></span>
					<?= $this->Form->input('main_color', [
						'type' => 'text',
						'placeholder' => 'メインカラー',
						'ng-model' => 'main_color',
						'ng-init' => 'main_color="'. h($this->data['MWidgetSetting']['main_color']) . '";',
						'div' => false,
						'class' => 'jscolor {hash:true}',
						'label' => false,
						'maxlength' => 7,
						'error' => false
					],
					[
					'entity' => 'MWidgetSetting.main_color'
					]) ?>
				</li>
				<?php if ($this->Form->isFieldError('main_color')) echo $this->Form->error('main_color', null, ['wrap' => 'li']); ?>
				<!-- メインカラー -->

				<!-- 角の丸み -->
				<li>
				<span class="require"><label>角の丸み</label></span>
				<?= $this->ngForm->input('radius_ratio', [
					'type' => 'range',
					'step' => 1,
					'ng-model' => 'radius_ratio',
					'ng-init' => 'radius_ratio="' . h($this->data['MWidgetSetting']['radius_ratio']) . '";',
					'div' => false,
					'label' => false,
					'max' => 15,
					'min' => 1,
					'error' => false
				],[
					'entity' => 'MWidgetSetting.radius_ratio'
				]) ?>
				</li>
				<?php if ( $this->Form->isFieldError('radius_ratio') ) echo $this->Form->error('radius_ratio', null, ['wrap' => 'li']); ?>
				<!-- 角の丸み -->


				<!-- お問い合わせ先 -->
				<li>
					<span class="require"><label>お問い合わせ先</label></span>
					<?= $this->ngForm->input('tel', [
						'type' => 'text',
						'placeholder' => 'お問い合わせ先電話番号',
						'div' => false,
						'label' => false,
						'maxlength' => 13,
						'error' => false
					],
					[
						'entity' => 'MWidgetSetting.tel'
					]
					) ?>
				</li>
				<?php if ($this->Form->isFieldError('tel') ) echo $this->Form->error('tel', null, ['wrap' => 'li']); ?>
				<!-- お問い合わせ先 -->

				<!-- 受付時間指定 -->
				<li>
					<span class="require"><label>受付時間指定</label></span>
					<?= $this->ngForm->input('display_time_flg', [
						'type' => 'radio',
						'fieldset' => false,
						'separator' => '&nbsp;',
						'legend' => false,
						'options' => [
							'しない',
							'する'
						],
						'label' => false,
						'error' => false
					],
					[
						'entity' => 'MWidgetSetting.display_time_flg',
						'change' => 'isDisplayTime()'
					]) ?>
				</li>
				<?php if ($this->Form->isFieldError('display_time_flg') ) echo $this->Form->error('display_time_flg', null, ['wrap' => 'li']); ?>
				<!-- 受付時間指定 -->

				<!-- 受付時間の表記 -->
				<?php
				// ウィジェットに受付時間を表記しない場合は、受付時間を更新対象外にする。
				$isDisableTimeText = false;
				if ( isset($this->data['MWidgetSetting']['display_time_flg']) && intval($this->data['MWidgetSetting']['display_time_flg']) !== 1 ) {
					$isDisableTimeText = true;
				}
				$isRequired = "";
				if ( !$isDisableTimeText ) {
					$isRequired = "class='require'";
				}
				?>
				<li>
					<span <?=$isRequired?> id="timeTextLabel"><label>受付時間の表記</label></span>
					<?= $this->ngForm->input('time_text', [
						'type' => 'text',
						'placeholder' => '受付時間の表記',
						'div' => false,
						'label' => false,
						'maxlength' => 15,
						'disabled' => $isDisableTimeText,
						'error' => false
					],
					[
						'entity' => 'MWidgetSetting.time_text'
					]) ?>
				</li>
				<?php if ($this->Form->isFieldError('time_text') ) echo $this->Form->error('time_text', null, ['wrap' => 'li']); ?>
				<!-- 受付時間の表記 -->

				<!-- ウィジェット本文 -->
				<li>
					<span><label>ウィジェット本文</label></span>
					<?= $this->ngForm->input('content', [
						'type' => 'textarea',
						'placeholder' => '本文',
						'div' => false,
						'label' => false,
						'maxlength' => 100,
						'error' => false
					],
					[
						'entity' => 'MWidgetSetting.content'
					]) ?>
				</li>
				<?php if ($this->Form->isFieldError('content') ) echo $this->Form->error('content', null, ['wrap' => 'li']); ?>
				<!-- ウィジェット本文 -->

			</ul>
			</section>
		</div>
	<?= $this->Form->end(); ?>

	<!-- /* 操作 */ -->
	<section>
		<div id="m_widget_setting_action">
		<?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
		</div>
	</section>
