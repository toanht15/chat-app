
<?php
$topSelected = "";
$agreements = "";
$settingSelected = "";
$templateSelected = "";
$madministratorsLists="";
$tdictionariesLists="";
$personalSettingsSelected="";
$tdictionaresSelected="";

switch ($this->name) {
  case 'Tops':
    $topSelected = "on";
  break;
  case 'MAgreements':
    $agreements = "on";
  break;
  case 'TDictionaries':
    $templateSelected = "on";
    if($this->name == 'TDictionaries') {
      $tdictionariesLists = "on";
    }
  break;
  case 'MAdministrators':
  if($this->name == 'MAdministrators') {
    $madministratorsLists = "on";
  }
  case 'PersonalSettings':
    $settingSelected = "on";
    if($this->name == 'PersonalSettings') {
    $personalSettingsSelected = "on";
  }
  break;
};

?>
<div id="sidebar">
  <div id="logo" ><?=$this->Html->image('sinclo_square_logo.png', ['width'=>54, 'height'=>48])?></div>
  <nav>
    <ul>
      <li class="link nav-group <?=$topSelected?>" onclick= "location.href = '<?=$this->Html->url(array('controller' => 'Tops', 'action' => 'index'))?>';"><i class="fa fa-home fa-lg" aria-hidden="true"></i>ホーム</li>
      <li class="nav-group <?=$agreements?>"><i class="fa fa-building fa-lg" aria-hidden="true"></i>システム</li>
      <li class="link <?=$agreements?>" onclick= "location.href = '<?=$this->Html->url(array('controller' => 'MAgreements', 'action' => 'index'))?>';">契約管理</li>
      <li>メール配信</li>
      <li class="nav-group <?=$templateSelected?>"><i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>テンプレート設定</li>
      <li class="first-group">ウィジェット設定</li>
      <li>オートメッセージ設定</li>
      <li class="link <?=$tdictionariesLists?>" onclick= "location.href = '<?=$this->Html->url(array('controller' => 'TDictionaries', 'action' => 'index'))?>';">簡易入力メッセージ設定</a></li>
      <li>設置ファイル設定</li>
      <li class="nav-group <?=$settingSelected?>"><i class="fa fa-cog fa-lg" aria-hidden="true"></i>設定</li>
      <li class="link <?=$madministratorsLists?>" onclick= "location.href = '<?=$this->Html->url(array('controller' => 'MAdministrators', 'action' => 'index'))?>';">アカウント管理</a></li>
      <li class="link <?=$personalSettingsSelected?>" onclick= "location.href = '<?=$this->Html->url(array('controller' => 'PersonalSettings', 'action' => 'index'))?>';">個人設定</a></li>
    </ul>
  </nav>
  <ul>
    <li class="logout" onclick= "location.href = '<?=$this->Html->url(array('controller' => 'Login', 'action' => 'logout'))?>';"><i class="fa fa-sign-out fa-lg" aria-hidden="true"></i>ログアウト</li>
  </ul>
</div>
