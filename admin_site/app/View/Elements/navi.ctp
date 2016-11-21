
<?php
$topSelected = "";
$agreementLists = "";
$settingSelected = "";
$personalSettingsSelected="";

switch ($this->name) {
  case 'Tops':
    $topSelected = "on";
  break;
  case 'AgreementLists':
    $agreementLists = "on";
  break;
  case 'MAdministrators':
    $settingSelected = "on";
  break;
  case 'PersonalSettings':
    $personalSettingsSelected = "on";
  break;
};
?>
<div id="sidebar">
  <div id="logo" ><?=$this->Html->image('sinclo_square_logo.png', ['width'=>54, 'height'=>48])?></div>
  <nav>
    <ul>
      <li class="link nav-group <?=$topSelected?>" onclick="location.href = 'http://sincloadmin.localhost/tops';"><i class="fa fa-home fa-lg" aria-hidden="true"></i> トップ</li>
      <li class="nav-group <?=$agreementLists?>"><i class="fa fa-building fa-lg" aria-hidden="true"></i> 契約管理</li>
      <li class="link <?=$agreementLists?>" onclick="location.href = 'http://sincloadmin.localhost/AgreementLists';">契約一覧</li>
      <li class="nav-group <?=$settingSelected?>"><i class="fa fa-cog fa-lg" aria-hidden="true"></i> 設定</li>
      <li class="link <?=$settingSelected?>" onclick="location.href = 'http://sincloadmin.localhost/MAdministrators';">アカウント設定</a></li>
      <li>テンプレート設定</li>
      <li class="nav-group <?=$personalSettingsSelected?>"><i class="fa fa-user fa-lg" aria-hidden="true"></i> 個人設定</li>
      <li class="link <?=$personalSettingsSelected?>" onclick="location.href = 'http://sincloadmin.localhost/PersonalSettings';">個人設定</a></li>
    </ul>
  </nav>
  <a href="/login/logout">ログアウト</a>
</div>
