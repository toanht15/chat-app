<?php
$settingSelected = "";

switch ($this->name) {
  case 'MAdministrators':
    $settingSelected = "on";
  break;
};
?>
<div id="sidebar">
  <div id="logo" ><?=$this->Html->image('sinclo_square_logo.png', ['width'=>54, 'height'=>48])?></div>
  <nav>
    <ul>
      <li class="nav-group"><i class="fa fa-home fa-lg" aria-hidden="true"></i> トップ</li>
      <li class="nav-group"><i class="fa fa-building fa-lg" aria-hidden="true"></i> 契約管理</li>
      <li>契約一覧</li>
      <li class="nav-group <?=$settingSelected?>"><i class="fa fa-cog fa-lg" aria-hidden="true"></i> 設定</li>
      <li class="<?=$settingSelected?>"><a href="/MAdministrators" style="text-decoration: none;">アカウント設定</a></li>
      <li>テンプレート設定</li>
      <li class="nav-group"><i class="fa fa-user fa-lg" aria-hidden="true"></i> 個人設定</li>
      <li>個人設定</li>
    </ul>
  </nav>
  <a href="/login/logout">ログアウト</a>
</div>
