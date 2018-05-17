<header>
<div class="inner">
<h1 id="logo"><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2',$company_key))?>"><?= $this->Html->image('logo.png', array('alt' => "Sample Company")) ?></a></h1>
<p id="tel">TEL:0120-000-000<span>AM9:00〜PM6:00　水曜定休</span></p>
</div>
<!--/inner-->
</header>

<div id="menu-box">
<!--PC用（481px以上端末）メニュー-->
<nav id="menubar">
<ul>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage',$company_key))?>">目次<span>Home</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2',$company_key))?>">ウィジェット非表示タグ<span>Widget</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3',$company_key))?>">フォーム用タグ<span>Form</span></a></li>
</ul>
</nav>
<!--スマホ用（480px以下端末）メニュー-->
<nav id="menubar-s">
<ul>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage',$company_key))?>">目次<span>Home</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2',$company_key))?>">ウィジェット非表示タグ<span>Widget</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3',$company_key))?>">フォーム用タグ<span>Form</span></a></li>
</ul>
</nav>
</div>
<!--/menubox-->