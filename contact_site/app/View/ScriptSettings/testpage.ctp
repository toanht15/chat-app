<div id="testpage_bg">
    <style>body { overflow: auto!important; }</style>

<?php switch($layoutNumber){ ?>
<?php   case 1: ?>
    <div id="testpage_idx">
      <div id="title">
        <span class="bold">目次</span>：
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2'))?>"><span class="underL">ウィジェット非表示タグ</span></a>：
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3'))?>"><span class="underL">フォーム用タグ</span></a>
      </div>
      <div id="detail">
        <p>モニタリングでは以下のことが可能です</p>
        <ul>
          <li>ページ同期（ページ遷移にも対応）</li>
          <li>スクロールの共有</li>
          <li>顧客から企業へのウィンドウサイズの反映</li>
          <li>マウス位置の共有</li>
          <li>フォームの入力内容の共有</li>
        </ul>
      </div>
      <div class="form01">
        <pre class="p15l">他ページにて上記動作を試してみてください。</pre>
      </div>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
      <p>12345678</p>
    </div>
    <?php echo $this->Html->script($fileName); ?>

<?php     break; ?>
<?php   case 2: ?>
    <div id="testpage_idx">
      <div id="title">
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage'))?>"><span class="underL">目次</span></a>：
        <span class="bold">ウィジェット非表示タグ</span>：
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3'))?>"><span class="underL">フォーム用タグ</span></a>
      </div>
      <div id="detail">
        <p>ウィジェット非表示タグの特徴</p>
        <span>画面同期を行う対象のページにしつつ、<br>ウィジェットの表示をさせないためのタグです。</span>
      </div>
    </div>
    <?php echo $this->Html->script($fileName, ['data-hide' => 1]); ?>
<?php     break; ?>
<?php   case 3: ?>

    <div id="testpage_idx">
      <div id="title">
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage'))?>"><span class="underL">目次</span></a>：
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2'))?>"><span class="underL">ウィジェット非表示タグ</span></a>：
        <span class="bold">フォーム用タグ</span>
      </div>
      <div id="detail">
        <p>フォーム用タグの特徴</p>
        <span>画面同期時に入力フォームがあるページに設置します。<br>設置すると、サブミット処理が行われたタイミングで自動的に画面同期を終了します。</span>
      </div>
      <div class="form01">
  <?= $this->Form->create('ScriptSettings', ['action' => 'confirm']) ?>
        <ul>
          <li class="lb">
            テキストエリアの入力内容が共有できます。
          </li>
          <li>
            <?= $this->Form->input('name', ['placeholder' => "名前", 'label' => false, 'div' => false, 'value' => '']) ?>
          </li>
          <li class="lb">
            <pre><span>ラジオボタンやチェックボックスの選択も反映されます。</span></pre>
          </li>
          <li>
            <p>性別：
              <label class="pointer"><?= $this->Form->radio('sexes', $optList['sexes'], ['legend' => false, 'separator' => '</label><label class="pointer">', 'value' => '' ]) ?></label>
            </p>
          </li>
          <style type="text/css">
          .cb_parent{ display: table!important }
          .cb_parent span { display: table-row!important }
          .cb_parent span label { margin: 5px 10px }
          </style>
          <li class="cb_parent">
            <p>どの製品をお使いですか？：</p>
            <span style="margin-top:10px">
              <label class="pointer">
              <?php echo $this->Form->input('product', [
                           'type' => 'select',
                           'multiple'=> 'checkbox',
                           'separator' => '</label><br><label class="pointer">',
                           'options' => $optList['products'],
                           'div' => false,
                           'value' => '',
                           'label' => false,
              ]); ?>
              </label>
            </span>
          </li>
          <li>
            <?= $this->Form->input('old', ['type' => "number", 'min' => "0", 'label' => false, 'placeholder'=>"年齢", 'value' => '']); ?>
          </li>
          <li>
            <?= $this->Form->input('office', ['placeholder'=>"会社", 'label' => false, 'value' => '']); ?>
          </li>
          <li class="lb">
            同じく、プルダウンの選択も反映されます。
          </li>
          <li>
            <?=$this->Form->input('work', [
              'type' => 'select',
              'multiple' => false,
              'value' => '',
              'label' => "職種",
              'options' => $optList['works']
            ]);?>
          </li>
          <li>
            <?= $this->Form->input('favorite', ['label' => false, 'placeholder'=>"趣味", 'value' => '']); ?>
          </li>
          <li>
            <?= $this->Form->textarea('other', ['label' => false, 'placeholder'=>"その他", 'value' => '']); ?>
          </li>
          <li>
            <input type="submit" value="send!!" style="margin: 0 auto">
          </li>
        </ul>
  <?= $this->Form->end(); ?>
      </div>
    </div>
    <?php echo $this->Html->script($fileName, ['data-form' => 1]); ?>
<?php     break; ?>
<?php   case 4: ?>
    <div id="testpage_idx">
      <div id="title">
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage'))?>"><span>目次</span></a>：
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage2'))?>"><span>ウィジェット非表示タグ</span></a>：
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage3'))?>"><span>フォーム用タグ</span></a>
      </div>
      <div id="detail" style="margin:15px 10px">
        <span>フォーム内容が表示されます。<br>このページは、同期対象外の為、専用タグを設置しておりません。<br><br>フォームの入力以降のページには専用タグを設置しないようにお願いします。</span>

        <ul>
          <li><label>名前</label>：<?php if ( isset($data['name']) ) { echo h($data['name']); } ?></li>
          <li><label>性別</label>：<?php if ( isset($data['sexes']) && isset($optList['sexes'][$data['sexes']]) ) { echo h($optList['sexes'][$data['sexes']]); } ?></li>
          <li><label>製品</label>：
            <?php
              if ( !empty($data['product']) ) {
                foreach((array)$data['product'] as $key => $val) {
                  if ( $key > 0 ) { echo " / "; }
                  if ( isset($optList['products'][$val]) ) { echo h($optList['products'][$val]); }
                }
              }
            ?>
          </li>
          <li><label>年齢</label>：<?php if ( isset($data['old']) ) { echo h($data['old']); } ?></li>
          <li><label>会社</label>：<?php if ( isset($data['office']) ) { echo h($data['office']); } ?></li>
          <li><label>職種</label>：<?php if ( isset($data['work']) && isset($optList['works'][$data['work']]) ) { echo $optList['works'][$data['work']]; } ?></li>
          <li><label>趣味</label>：<?php if ( isset($data['favorite']) ) { echo h($data['favorite']); } ?></li>
          <li><label>その他</label>：<?php if ( isset($data['other']) ) { echo h($data['other']); } ?></li>
        </ul>
      </div>
    </div>

<?php     break; ?>
<?php   default; ?>
<?php } ?>

</div>
