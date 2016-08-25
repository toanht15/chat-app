<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.3/clipboard.min.js"></script>
<script>
  $(function () {
  // クリップボードにコピーする
  var clipboard = new Clipboard('.copyBtn');
  clipboard.on('success', function(e) {
    var self = e;
    // コピーしたことが視覚的にわかりやすいように
    // 少しゆとりを持って選択を解除する
    window.setTimeout(function(){
      self.clearSelection();
    }, 300);
  });
  });
</script>
<div id='script_setting_idx' class="card-shadow">

<div id='script_setting_title'>
  <div class="fLeft"><?= $this->Html->image('script_g.png', array('alt' => 'コード設置・デモサイト', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>コード設置・デモサイト</h1>
</div>
<div id='script_setting_content' class="p20x">
  <h2>１．コード設置</h2>
  <section>
    <pre>下記いずれか１つのコードをWEBページの&lt;body&gt;タグの最後（&lt;/body&gt;の直前）に埋め込んでください。
※１つのWEBページにsincloのタグ（下記いずれかのタグ）は1つだけ埋め込んでください。</pre>
    <dl>
      <dt>（１）ウィジェット表示タグ</dt>
      <dd>
        <pre>ウィジェットを表示したい通常のページ（フォーム以外のページ）に埋め込むタグです。</pre>
        <p>
          <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "'></script>"; ?>
          <span class="copyBtn" data-clipboard-target="#normalTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
          <span class="copyArea"><?=$this->Form->input('normalTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
        </p>
      </dd>
      <dt>（２）ウィジェット非表示タグ</dt>
      <dd>
        <pre>ウィジェットは表示させずに画面共有の対象とする通常のページ（フォーム以外のページ）に埋め込むタグです。</pre>
        <p>
          <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-hide='1'></script>"; ?>
          <span class="copyBtn" data-clipboard-target="#hideTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
          <span class="copyArea"><?=$this->Form->input('hideTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
        </p>
      </dd>
      <dt>（３）フォーム用ウィジェット表示タグ</dt>
      <dd>
        <pre>ウィジェットを表示したいフォーム系ページに埋め込むタグです。
※本タグを埋め込んだページは、画面共有中に企業側からのsubmitボタン操作を無効にします。</pre>
        <p>
          <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-form='1'></script>"; ?>
          <span class="copyBtn" data-clipboard-target="#formTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
          <span class="copyArea"><?=$this->Form->input('formTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
        </p>
      </dd>
      <dt>（４）フォーム用ウィジェット非表示タグ</dt>
      <dd>
        <pre>ウィジェットは表示させずに画面共有の対象とするフォーム系ページに埋め込むタグです。
※本タグを埋め込んだページは、画面共有中に企業側からのsubmitボタン操作を無効にします</pre>
        <p>
          <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-hide='1' data-form='1'></script>"; ?>
          <span class="copyBtn" data-clipboard-target="#formHideTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
          <span class="copyArea"><?=$this->Form->input('formHideTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
        </p>
      </dd>
    </dl>
    <pre style="color:red">注意：フォーム系ページの次のページ（submitボタンをクリックした後に遷移する「確認ページ」や「サンクスページ」）にはsincloタグは
　　　埋め込まないでください。
    </pre>
  </section>
  <h2>２．デモサイト</h2>
  <section>
    <pre>下記サイトにてウィジェットの確認やsincloの動作確認、事前トレーニングを行うことができます。</pre>
    【 <?= $this->Html->link('デモサイトへ', array('controller' => 'ScriptSettings', 'action' => 'testpage'), array('target' => '_demo', 'class' => 'underL')) ?> 】
  </section>
</div>

</div>
