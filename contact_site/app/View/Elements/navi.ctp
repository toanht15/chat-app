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
    case 'PersonalSettings':
    case 'MWidgetSettings':
        $settingSelected = "selected";
        break;
};
?>
<!-- /* 上部カラーバー(ここから) */ -->
<div id="color-bar" class="card-shadow">
    <ul id="color-bar-left" class="fLeft">
<?php
/*
 * リアルタイムモニタ画面にて、ウィジェットの表示方法を「オペレーターが待機中の時のみ表示する」に
 * している場合にのみ表示します。
 */
 ?>
<?php if ( !empty($monitorSelected) && $widgetCheck ) : ?>
<?php

if ( $opStatus ) {
  echo "<li id='operatorStatus' class='opWait'><span>待機中</span>";
}
else {
  echo "<li id='operatorStatus' class='opStop'><span>離席中</span>";
}
?>
        </li>
        <li id="changeOpStatus" onclick="chgOpStatus()" data-status="<?=$opStatus?>">
<?php
if ( $opStatus ) {
  echo "<p>離席中にする</p>";
}
else {
  echo "<p>待機中にする</p>";
}
?>
        </li>
<?php endif; ?>
    </ul>
    <ul id="color-bar-right" class="fRight">
        <li class="fLeft"><p><?= h($userInfo['user_name']) ?></p></li>
        <li class="fRight" id="logout" onclick='location.href = "/Login/logout"'><p>ログアウト</p></li>
    </ul>
</div>
<!-- /* 上部カラーバー(ここまで) */ -->

<!-- /* システムアイコン（ここから） */ -->
<div id="sys-icon" class="card-shadow"><?= $this->Html->image('sinclo_square_logo.png', array('alt' => 'アイコン', 'width' => 54, 'height' => 48, 'style'=>'margin: 6px 3px; display: block'))?></div>
<!-- /* システムアイコン（ここまで） */ -->

<!-- /* サイドバー１（ここから） */ -->
<div id="sidebar-main" class="card-shadow">
    <div>
        <div class="icon <?=$monitorSelected?>">
            <?= $this->htmlEx->naviLink('モニター', 'monitor.png', ['href' => ['controller' => 'Customers', 'action' => 'index']]) ?>
        </div>
        <div class="icon <?=$historySelected?>">
            <?= $this->htmlEx->naviLink('履歴', 'history.png', ['href' => ['controller' => 'Histories', 'action' => 'index']]) ?>
        </div>
        <div class="icon <?=$settingSelected?>" id="setting-icon">
            <?= $this->htmlEx->naviLink('設定', 'setting.png') ?>
        </div>
    </div>
</div>
<!-- /* サイドバー１（ここまで） */ -->

<!-- /* サイドバー２（ここから） */ -->
<div id="sidebar-sub" class="card-shadow">
    <div>
        <div class="icon">
            <?= $this->htmlEx->naviLink('個人設定', 'monitor.png', ['href' => ['controller' => 'PersonalSettings', 'action' => 'index']]) ?>
        </div>
    <?php if (strcmp($userInfo['permission_level'], C_AUTHORITY_ADMIN) === 0): ?>
        <div class="icon" style="display:none">
            <?= $this->htmlEx->naviLink('企業設定', 'company.png', ['href' => ['controller' => 'Customers', 'action' => 'index']]) ?>
        </div>
        <div class="icon">
            <?= $this->htmlEx->naviLink('ユーザーマスタ', 'monitors.png', ['href' => ['controller' => 'MUsers', 'action' => 'index']]) ?>
        </div>
    <?php endif; ?>
        <div class="icon">
            <?= $this->htmlEx->naviLink('コード・デモ', 'script.png', ['href' => ['controller' => 'ScriptSettings', 'action' => 'index']]) ?>
        </div>
    <?php if (strcmp($userInfo['permission_level'], C_AUTHORITY_ADMIN) === 0): ?>
        <div class="icon">
            <?= $this->htmlEx->naviLink('ウィジェット', 'chat.png', ['href' => ['controller' => 'MWidgetSettings', 'action' => 'index']]) ?>
        </div>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
            <div class="icon">
                <?= $this->htmlEx->naviLink('メッセージ', 'auto_message.png', ['href' => ['controller' => 'TAutoMessages', 'action' => 'index']]) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
        <div class="icon">
            <?= $this->htmlEx->naviLink('簡易入力', 'dictionary.png', ['href' => ['controller' => 'TDictionaries', 'action' => 'index']]) ?>
        </div>
    <?php endif; ?>
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
