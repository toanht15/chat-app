<?= $this->element('ScriptSettings/menuBox'); ?>
<!--/menubox-->

<div id="contents">

<div class="inner">

<div id="main">

<section>

<h2>制作の流れ</h2>

<section class="list">
<figure><?= $this->Html->image('sample_noimg.png', array('alt' => "写真の説明")) ?></figure>
<h4>ステップ１</h4>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</section>

<p class="c"><?= $this->Html->image('arrow.png', array('alt' => "")) ?></p>

<section class="list">
<figure><?= $this->Html->image('sample_noimg.png', array('alt' => "写真の説明")) ?></figure>
<h4>ステップ２</h4>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</section>

<p class="c"><?= $this->Html->image('arrow.png', array('alt' => "")) ?></p>

<section class="list">
<figure><?= $this->Html->image('sample_noimg.png', array('alt' => "写真の説明")) ?></figure>
<h4>ステップ３</h4>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</section>

<?php
  if($plan == 'sharing'){
?>
<p class="c"><?= $this->Html->image('arrow.png', array('alt' => "")) ?></p>

<section class="list">
<figure><?= $this->Html->image('sample_noimg.png', array('alt' => "写真の説明")) ?></figure>
<h4>ステップ４</h4>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</section>

<p class="c"><?= $this->Html->image('arrow.png', array('alt' => "")) ?></p>

<section class="list">
<figure><?= $this->Html->image('sample_noimg.png', array('alt' => "写真の説明")) ?></figure>
<h4>ステップ5</h4>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</section>

<p class="c"><?= $this->Html->image('arrow.png', array('alt' => "")) ?></p>

<section class="list">
<figure><?= $this->Html->image('sample_noimg.png', array('alt' => "写真の説明")) ?></figure>
<h4>ステップ6</h4>
<p>サンプルテキスト。サンプルテキスト。サンプルテキスト。</p>
</section>
<?php } ?>
</section>

</div>
<!--/main-->

<?= $this->element('ScriptSettings/subMenu'); ?>

</div>
<!--/inner-->

</div>
<!--/contents-->

<?= $this->element('ScriptSettings/footer'); ?>