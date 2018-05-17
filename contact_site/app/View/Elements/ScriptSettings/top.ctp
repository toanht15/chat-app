<header id = "top">
<div class="inner">
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
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage4',$company_key))?>">フォーム用タグ <span>Form</span></a></li>
</ul>
</nav>
<!--スマホ用（480px以下端末）メニュー-->
<nav id="menubar-s">
<ul>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage',$company_key))?>">ホーム<span>Home</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2',$company_key))?>">プラン・費用<span>Plan</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3',$company_key))?>">制作の流れ<span>Flow</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage4',$company_key))?>">フォーム用タグ<span>Form</span></a></li>
</ul>
</nav>
</div>
<!--/menubox-->

<div id="contents">

<div class="inner">

<div id="main">

<section>

<h2>画面共有では以下のことが可能です</h2>
<section class="list">
<p style = "margin-left:0px;">○ページ同期（ページ遷移にも対応）</p>
<p style = "margin-left:0px;">○スクロールの共有</p>
<p style = "margin-left:0px;">○顧客から企業へのウィンドウサイズの反映</p>
<p style = "margin-left:0px;">○マウス位置の共有</p>
<p style = "margin-left:0px;">○フォームの入力内容の共有</p>
</section>
<br>他ページにて上記動作を試してみてください。

</section>

</div>
<!--/main-->

<?= $this->element('ScriptSettings/subMenu'); ?>

</div>
<!--/inner-->

</div>
<!--/contents-->

<?= $this->element('ScriptSettings/footer'); ?>
