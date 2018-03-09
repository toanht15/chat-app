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

    $('#showFormTag').on('click', function(e){
      if($(this).prop('checked')) {
        $('#formTagWrap').css('display','');
      } else {
        $('#formTagWrap').css('display','none');
      }
    });
  });
</script>
<?php
$headerNo = 1;
$mainTitle = ( $adminFlg ) ? "コード設置・デモサイト" : "デモサイト" ;
?>
<div id='script_setting_idx' class="card-shadow">

<div id='script_setting_title'>
  <div class="fLeft"><?= $this->Html->image('script_g.png', array('alt' => $mainTitle, 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1><?=$mainTitle?></h1>
</div>
<div id='script_setting_content' class="p20x">
  <?php if ( $adminFlg ): ?>
  <h2><?= mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．コード設置</h2>
  <section>
    <pre>下記いずれか１つのコードをWEBページの&lt;body&gt;タグの最後（&lt;/body&gt;の直前）に埋め込んでください。
※１つのWEBページにsincloのタグ（下記いずれかのタグ）は1つだけ埋め込んでください。</pre>
    <dl>
      <dt>（１）ウィジェット表示タグ</dt>
      <dd>
        <pre>ウィジェットを表示するページに埋め込むタグです。</pre>
        <p>
          <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "'></script>"; ?>
          <span class="copyBtn" data-clipboard-target="#normalTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
          <span class="copyArea"><?=$this->Form->input('normalTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
        </p>
      </dd>
      <dt>（２）ウィジェット非表示タグ</dt>
      <dd>
        <pre>ウィジェットを表示させたくないページに埋め込むタグです。
※ウィジェットは表示させずに、リアルタイムモニタへの表示やアクセス履歴の対象としたいページや、
画面共有の対象としたいページに本タグを埋め込んでください。</pre>
        <p>
          <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-hide='1'></script>"; ?>
          <span class="copyBtn" data-clipboard-target="#hideTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
          <span class="copyArea"><?=$this->Form->input('hideTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
        </p>
      </dd>
      <?php if($coreSettings[C_COMPANY_USE_SYNCLO] || $coreSettings[C_COMPANY_USE_DOCUMENT] || $coreSettings[C_COMPANY_USE_LA_CO_BROWSE]): ?>
      <label for="showFormTag" style="cursor:pointer; margin-bottom: 1em; display:block; font-size: 1.2em"><input type="checkbox" id="showFormTag" style="position:relative; top:-1px"/>フォーム代理入力（画面共有機能）を利用する場合に対象ページに埋め込むタグ</label>
      <div id="formTagWrap" style="display:none;">
      <dt>（３）フォーム代理入力するページ用のタグ（ウィジェット表示）</dt>
        <dd>
          <pre>画面共有を利用してフォームの代理入力を行うページに埋め込むタグです。（ウィジェット表示）
※本タグを埋め込んだページは、画面共有中に企業側からのsubmitボタン操作を無効にします。</pre>
          <p>
            <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-form='1'></script>"; ?>
            <span class="copyBtn" data-clipboard-target="#formTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
            <span class="copyArea"><?=$this->Form->input('formTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
          </p>
        </dd>
        <dt>（４）フォーム代理入力するページ用のタグ（ウィジェット非表示）</dt>
        <dd>
          <pre>画面共有を利用してフォームの代理入力を行うページに埋め込むタグです。（ウィジェット非表示）
※本タグを埋め込んだページは、画面共有中に企業側からのsubmitボタン操作を無効にします。</pre>
          <p>
            <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-hide='1' data-form='1'></script>"; ?>
            <span class="copyBtn" data-clipboard-target="#formHideTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
            <span class="copyArea"><?=$this->Form->input('formHideTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
          </p>
        </dd>
        <pre style="color:red">注意：フォーム系ページの次のページ（submitボタンをクリックした後に遷移する「確認ページ」や「サンクスページ」）にはsincloタグは
　　　埋め込まないでください。
        </pre>
      </div>
    </dl>
    <?php endif; ?>
  </section>
  <?php endif; ?>
  <h2><?= mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．デモサイト</h2>
  <section>
    <pre>下記サイトにてウィジェットの確認やsincloの動作確認、事前トレーニングを行うことができます。</pre>
    【 <?= $this->Html->link('デモサイトへ', array('controller' => 'ScriptSettings', 'action' => 'testpage'), array('target' => '_demo', 'class' => 'underL')) ?> 】
  </section>
</div>

</div>
