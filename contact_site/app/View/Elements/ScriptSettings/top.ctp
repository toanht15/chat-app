<header id = "top">
<div class="inner">
<?php
  if($plan == 'chat'){
    $name = "お問い合わせ";
  }
  else if($plan == 'sharing'){
    $name = "フォーム用タグ";
  }
?>
<h1 id="logo"><a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage',$company_key))?>"><?php if(!defined('APP_MODE_OEM') || !APP_MODE_OEM): ?><?= $this->Html->image('logo.png', array('alt' => "Sample Company")) ?><?php endif; ?></a></h1>
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

<div id="contents">

<div class="inner">

<div id="main">

<?php
  if($plan == 'chat'){
?>

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

</section>

<?php
  }
  else if($plan == 'sharing'){
?>
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
<?php } ?>
</div>
<!--/main-->

<?= $this->element('ScriptSettings/subMenu'); ?>

</div>
<!--/inner-->

</div>
<!--/contents-->

<?= $this->element('ScriptSettings/footer'); ?>
