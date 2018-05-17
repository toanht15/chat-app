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
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage4',$company_key))?>">お問い合わせ <span>Form</span></a></li>
</ul>
</nav>
<!--スマホ用（480px以下端末）メニュー-->
<nav id="menubar-s">
<ul>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage',$company_key))?>">ホーム<span>Home</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2',$company_key))?>">プラン・費用<span>Plan</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3',$company_key))?>">制作の流れ<span>Flow</span></a></li>
<li><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage4',$company_key))?>">お問い合わせ<span>Form</span></a></li>
</ul>
</nav>
</div>
<!--/menubox-->

<div id="contents">

<div class="inner">

<div id="main">

<section>

<section id="new">
<h2 id="newinfo_hdr" class="close">更新情報・お知らせ</h2>
<dl id="newinfo">
<dt>20XX/00/00</dt>
<dd>サンプルテキスト。サンプルテキスト。サンプルテキスト。<span class="newicon">NEW</span></dd>
<dt>20XX/00/00</dt>
<dd>サンプルテキスト。サンプルテキスト。サンプルテキスト。</dd>
<dt>20XX/00/00</dt>
<dd>サンプルテキスト。サンプルテキスト。サンプルテキスト。</dd>
<dt>20XX/00/00</dt>
<dd>サンプルテキスト。サンプルテキスト。サンプルテキスト。</dd>
<dt>20XX/00/00</dt>
<dd>サンプルテキスト。サンプルテキスト。サンプルテキスト。</dd>
<dt>20XX/00/00</dt>
<dd>サンプルテキスト。サンプルテキスト。サンプルテキスト。</dd>
<dt>20XX/00/00</dt>
<dd>サンプルテキスト。サンプルテキスト。サンプルテキスト。</dd>
</dl>
</section>

<section>

<h2>サンプルテキスト。サンプルテキスト。サンプルテキスト。</h2>

<h3>サンプルテキスト。サンプルテキスト。サンプルテキスト。</h3>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
<p><span class="color1">■<strong>サンプルテキスト。サンプルテキスト。サンプルテキスト。</strong></span><br>
サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
<p><span class="color1">■<strong>サンプルテキスト。サンプルテキスト。サンプルテキスト。</strong></span><br>
サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>

<h3>サンプルテキスト。サンプルテキスト。サンプルテキスト。</h3>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>

<h3>サンプルテキスト。サンプルテキスト。サンプルテキスト。</h3>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>

</section>

<section>
<h2>サンプルテキスト。サンプルテキスト。サンプルテキスト。</h2>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</section>

</div>
<!--/main-->

<?= $this->element('ScriptSettings/subMenu'); ?>

</div>
<!--/inner-->

</div>
<!--/contents-->

<?= $this->element('ScriptSettings/footer'); ?>
