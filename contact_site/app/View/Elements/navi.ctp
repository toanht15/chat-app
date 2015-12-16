<?php
$monitorSelected = "";
$historySelected = "";
$settingSelected = "";
switch ($this->name) {
	case 'Customers':
		$monitorSelected = "selected";
		break;
	case 'Histories':
		$historySelected = "selected";
		break;
	case 'MUsers':
		$settingSelected = "selected";
		break;
};
?>
<!-- /* 上部カラーバー(ここから) */ -->
<div id="color-bar" class="card-shadow">
	<div id="color-bar-right" style="float:right">
		<div class="fLeft"><span><?= h($userInfo['user_name']) ?></span></div>
		<div class="fRight" id="logout" onclick='location.href = "/Login/logout"'><span>ログアウト</span></div>
	</div>
</div>
<!-- /* 上部カラーバー(ここまで) */ -->

<!-- /* システムアイコン（ここから） */ -->
<div id="sys-icon" class="card-shadow"><?= $this->Html->image('icon.png', array('alt' => 'アイコン', 'width' => 50, 'height' => 50, 'style'=>'margin: 0 auto; display: block; opacity: 0.7'))?></div>
<!-- /* システムアイコン（ここまで） */ -->

<!-- /* サイドバー１（ここから） */ -->
<div id="sidebar-main" class="card-shadow">
	<div>
		<div class="icon <?=$monitorSelected?>">
			<?= $this->Html->image('monitor.png', array('alt' => 'モニター', 'width' => 30, 'height' => 30, 'url' => array('controller' => 'Customers', 'action' => 'index'))) ?>
			<p>モニター</p>
		</div>
		<div class="icon <?=$historySelected?>">
			<?= $this->Html->image('history.png', array('alt' => '履歴', 'width' => 30, 'height' => 30, 'url' => array('controller' => 'Histories', 'action' => 'index'))) ?>
			<p>履歴</p>
		</div>
		<div class="icon <?=$settingSelected?>" id="setting-icon">
			<?= $this->Html->image('setting.png', array('alt' => '設定', 'width' => 30, 'height' => 30, 'url' => array('controller' => 'Customers', 'action' => 'index'))) ?>
			<p>設定</p>
		</div>
	</div>
</div>
<!-- /* サイドバー１（ここまで） */ -->

<!-- /* サイドバー２（ここから） */ -->
<div id="sidebar-sub" class="card-shadow">
	<div>
		<div class="icon">
			<?= $this->Html->image('monitor.png', array('alt' => '個人設定', 'width' => 30, 'height' => 30, 'url' => array('controller' => 'MUsers', 'action' => 'index'))) ?>
			<p>個人設定</p>
		</div>
		<div class="icon">
			<?= $this->Html->image('company.png', array('alt' => '企業設定', 'width' => 30, 'height' => 30, 'url' => array('controller' => 'Customers', 'action' => 'index'))) ?>
			<p>企業設定</p>
		</div>
		<div class="icon">
			<?= $this->Html->image('monitors.png', array('alt' => '個人設定', 'width' => 30, 'height' => 30, 'url' => array('controller' => 'MUsers', 'action' => 'index'))) ?>
			<p>ユーザーマスタ</p>
		</div>
		<div class="icon">
			<?= $this->Html->image('script.png', array('alt' => 'コード・デモ', 'width' => 30, 'height' => 30, 'url' => array('controller' => 'ScriptSettings', 'action' => 'index'))) ?>
			<p>コード・デモ</p>
		</div>
		<div class="icon">
			<?= $this->Html->image('chat.png', array('alt' => 'ウィジェット', 'width' => 30, 'height' => 30, 'url' => array('controller' => 'Customers', 'action' => 'index'))) ?>
			<p>ウィジェット</p>
		</div>
	</div>
</div>
<!-- /* サイドバー２（ここまで） */ -->
<script type="text/javascript">
	$("#setting-icon").toggle(
		function(){
			$("#sidebar-sub").addClass('open');
		},
		function(){
			$("#sidebar-sub").removeClass('open');
		}
	);
</script>