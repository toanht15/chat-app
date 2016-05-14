<?php echo $this->element('TAutoMessages/script'); ?>
<?php echo $this->element('TAutoMessages/templates'); ?>
<?php echo $this->element('TAutoMessages/angularjs'); ?>

<?=$this->Form->create('TAutoMessages', ['action' => 'entry'])?>
<?php $this->Form->inputDefaults(['label'=>false, 'div' => false, 'error' => false, 'legend' => false ]);?>
<div class="form01" ng-app="sincloApp" ng-controller="MainCtrl" ng-cloak>
	<section>
		<h3>１．基本設定</h3>
		<ul class="settingList pl30">
			<!-- 名称 -->
			<li>
				<span class="require"><label>名称</label></span>
				<?= $this->Form->input('name', [
					'type' => 'text',
					'placeholder' => '名称',
					'maxlength' => 12
				]) ?>
			</li>
			<?php if ($this->Form->isFieldError('name')) echo $this->Form->error('name', null, ['wrap' => 'li']); ?>
			<!-- 名称 -->

			<!-- トリガー -->
			<li>
				<span class="require"><label>トリガー</label></span>
				<?= $this->Form->input('trigger', array('type' => 'select', 'options' => $outMessageTriggerType, 'default' => C_AUTO_TRIGGER_TYPE_BODYLOAD)) ?>
			</li>
			<!-- トリガー -->
		</ul>
	</section>

	<section class="section2" >
		<h3>２．条件詳細設定</h3>
		<ul class="settingList pl30">
			<!-- 条件設定 -->
			<li>
				<span class="require"><label>条件設定</label></span>
				<?= $this->ngForm->input('max_show_time', [
					'type' => 'radio',
					'options' => $outMessageIfType,
					'separator' => '&nbsp',
					'error' => false
				],[
					'entity' => 'max_show_time',
					'default' => C_COINCIDENT,
				]); ?>
			</li>
			<?php if ($this->Form->isFieldError('name')) echo $this->Form->error('name', null, ['wrap' => 'li']); ?>
			<!-- 条件設定 -->

			<!-- トリガー -->
			<li id="tautomessages_triggers" class="bt0">
				<div id="setTriggerList" class="pl30">
					<ul>
					</ul>
				</div>
				<div id="triggerList">
					<ul>
						<li ng-repeat="(key, item) in tmpList" ng-click="addItem(key)">{{item.label}}</li>
					</ul>
				</div>
			</li>
			<!-- トリガー -->
		</ul>
	</section>

	<h3>３．実行設定</h3>
	<section>
		<ul class="settingList pl30">
			<!-- アクション -->
			<li>
				<span class="require"><label>アクション</label></span>
				<?= $this->Form->input('action', array('type' => 'select', 'options' => $outMessageActionType, 'default' => C_AUTO_ACTION_TYPE_SENDMESSAGE)) ?>
			</li>
			<!-- アクション -->

			<!-- メッセージ -->
			<li class="pl30 bt0">
					<span class="require"><label>メッセージ</label></span>
					<?= $this->Form->textarea('messages') ?>
			</li>
			<!-- メッセージ -->

			<!-- 状態 -->
			<li>
				<span class="require"><label>状態</label></span>
				<?= $this->Form->input('available_flg', [
					'type' => 'radio',
					'options' => $outMessageAvailableType,
					'default' => C_STATUS_AVAILABLE,
					'separator' => '&nbsp',
					'error' => false
				]); ?>
			</li>
			<?php if ($this->Form->isFieldError('name')) echo $this->Form->error('name', null, ['wrap' => 'li']); ?>
			<!-- 状態 -->
		</ul>
	</section>

	<section>
		<div id="tautomessages_actions">
			<a href="javascript:void(0)" onclick="loading.ev(saveAct)" class="greenBtn btn-shadow">保存</a>
		</div>
	</section>

</div>

<?=$this->Form->end();?>
