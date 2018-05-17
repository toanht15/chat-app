<?= $this->element('ScriptSettings/menuBox'); ?>
<!--/menubox-->

<div id="contents">

<div class="inner">

<div id="main">

<section>

<h2>フォーム用タグの特徴</h2>
<section class="list">
<p style = "margin-left:0px;">※画面同期時に入力フォームがあるページに設置します。<br>設置すると、サブミット処理が行われたタイミングで自動的に画面同期を終了します。</p>
</section>
<?= $this->Form->create('ScriptSettings', ['action' => 'confirm/'.$company_key]) ?>
<table class="ta1">
<tr>
<th colspan="2" class="tamidashi">※テキストエリアの入力内容が共有でき、ラジオボタン、プルダウン、チェックボックスの選択も反映されます</th>
</tr>
<tr>
<th>お名前</th>
<td><?= $this->Form->input('name', ['type' => "text", 'class' => "ws", 'size' => 30,'label' => false]); ?></td>
</tr>
<tr>
<th>メールアドレス</th>
<td><?= $this->Form->input('mail', ['type' => "text", 'class' => "ws", 'size' => 30,'label' => false]); ?></td>
</tr>
<tr>
<th>性別</th>
<td><?= $this->Form->radio('sexes', $optList['sexes'], ['legend' => false, 'separator' => '</label><label class="pointer">', 'value' => '' ]) ?></td>
</tr>
<tr>
<tr>
<th>年齢</th>
<td><?= $this->Form->input('old', ['type' => "number", 'min' => "0", 'label' => false, 'value' => '']); ?></td>
</tr>
<tr>
<th>ご住所(都道府県)</th>
<td>
<?= $this->Form->input('prefectures', ['type' => "select",'label' => false,'options' => $option,'selected' => '都道府県選択']); ?>
</td>
</tr>
<tr>
<th>ご住所(市区町村以下)</th>
<td><?= $this->Form->input('address', ['type' => "text", 'class' => "wl", 'size' => 30,'label' => false]); ?></td>
</tr>
<tr>
<th>お問い合わせ項目</th>
<td>
<label>
  <?php echo $this->Form->input('inquiry', [
    'type' => 'select',
    'multiple'=> 'checkbox',
    'separator' => '</label><br><label class="pointer">',
    'options' => $optList['inquiry'],
    'div' => false,
    'value' => '',
    'label' => false,
  ]); ?>
</label>
</td>
</tr>
<tr>
<th>お問い合わせ詳細</th>
<td><?= $this->Form->input('detail', ['type' => "textarea",'cols' => 30, 'rows' => 10, 'class'=> 'wl','label' => false,'div' => false]); ?></td>
</tr>
</table>

<p class="c">
<input type="submit" value="内容を確認する">
</p>
<?= $this->Form->end(); ?>
</section>

</div>
<!--/main-->

<?= $this->element('ScriptSettings/subMenu'); ?>

</div>
<!--/inner-->

</div>
<!--/contents-->

<?= $this->element('ScriptSettings/footer'); ?>