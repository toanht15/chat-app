<?= $this->element('ScriptSettings/menuBox'); ?>
<!--/menubox-->

<div id="contents">

<div class="inner">

<div id="main">

<section>

<h2>制作実績（横長ボックス）</h2>

<section class="list">
<a href="#">
<figure><?= $this->Html->image('sample1.jpg', array('alt' => "写真の説明")) ?></figure>
<h4>A様</h4>
<p>http://xxxxxxx.com/<br>
サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</a>
</section>

<section class="list">
<a href="#">
<figure><?= $this->Html->image('sample1.jpg', array('alt' => "写真の説明")) ?></figure>
<h4>B様</h4>
<p>http://xxxxxxx.com/<br>
サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</a>
</section>

</section>

<section>

<h2>制作実績（コンパクトボックス）</h2>

<section class="list compact">
<a href="#">
<figure><?= $this->Html->image('sample1.jpg', array('alt' => "写真の説明")) ?></figure>
<h4>A様</h4>
<p>http://xxxxxxx.com/<br>
サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</a>
</section>

<section class="list compact">
<a href="#">
<figure><?= $this->Html->image('sample1.jpg', array('alt' => "写真の説明")) ?></figure>
<h4>B様</h4>
<p>http://xxxxxxx.com/<br>
サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</a>
</section>

<section class="list compact">
<a href="#">
<figure><?= $this->Html->image('sample1.jpg', array('alt' => "写真の説明")) ?></figure>
<h4>C様</h4>
<p>http://xxxxxxx.com/<br>
沢山の文字を詰め込みすぎると途中で切れてしまいまうのでご注意下さい。沢山の文字を詰め込みすぎると途中で切れてしまいまうのでご注意下さい。沢山の文字を詰め込みすぎると途中で切れてしまいまうのでご注意下さい。</p>
</a>
</section>

</section>

</div>
<!--/main-->

<?= $this->element('ScriptSettings/subMenu'); ?>

</div>
<!--/inner-->

</div>
<!--/contents-->

<?= $this->element('ScriptSettings/footer'); ?>