<header>
<div class="inner">
<?php
  if($plan == 'chat'){
    $name = "お問い合わせ";
  }
  else if($plan == 'sharing'){
    $name = "フォーム用タグ";
  }
?>
<h1 id="logo"><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage',$company_key))?>"><?= $this->Html->image('logo.png', array('alt' => "Sample Company")) ?></a></h1>
<p id="tel">TEL:0120-000-000<span>AM9:00〜PM6:00　水曜定休</span></p>
</div>
<!--/inner-->
</header>
<div id="menu-box">
<!--PC用（481px以上端末）メニュー-->
<nav id="menubar">
<ul>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage',$company_key))?>">ホーム<span>Home</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2',$company_key))?>">プラン・費用<span>Plan</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3',$company_key))?>">制作の流れ<span>Flow</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage4',$company_key))?>"><?= $name ?><span>Form</span></a></li>
</ul>
</nav>
<!--スマホ用（480px以下端末）メニュー-->
<nav id="menubar-s">
<ul>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage',$company_key))?>">ホーム<span>Home</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2',$company_key))?>">プラン・費用<span>Plan</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3',$company_key))?>">制作の流れ<span>Flow</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage4',$company_key))?>"><?= $name ?><span>Form</span></a></li>
</ul>
</nav>
</div>
<!--/menubox-->