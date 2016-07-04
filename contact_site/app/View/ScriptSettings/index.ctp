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
    <div class="fLeft"><?= $this->Html->image('script_g.png', array('alt' => 'コード設置・デモ画面', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>コード設置・デモ画面</h1>
</div>
<div id='script_setting_content' class="p20x">
    <ul>
        <li>
            <h2>ウィジェット表示タグ</h2>
            <p>
              <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "'></script>"; ?>
              <span class="copyBtn" data-clipboard-target="#normalTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
              <span class="copyArea"><?=$this->Form->input('normalTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
            </p>
        </li>
        <li>
            <h2>ウィジェット非表示タグ</h2>
            <p>
              <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-hide='1'></script>"; ?>
              <span class="copyBtn" data-clipboard-target="#hideTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
              <span class="copyArea"><?=$this->Form->input('hideTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
            </p>
        </li>
        <li>
            <h2>フォーム用表示タグ</h2>
            <p>
              <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-form='1'></script>"; ?>
              <span class="copyBtn" data-clipboard-target="#formTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
              <span class="copyArea"><?=$this->Form->input('formTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
            </p>
        </li>
        <li>
            <h2>フォーム用非表示タグ</h2>
            <p>
              <?php $scriptName = "<script type='text/javascript' src='" . $fileName . "' data-hide='1' data-form='1'></script>"; ?>
              <span class="copyBtn" data-clipboard-target="#formTag"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
              <span class="copyArea"><?=$this->Form->input('formTag', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
            </p>
        </li>
        <li>
            <h2>デモ画面</h2>
            <?= $this->Html->link('デモ画面へ', array('controller' => 'ScriptSettings', 'action' => 'testpage'), array('target' => '_demo', 'class' => 'underL')) ?>
        </li>
    </ul>
</div>

</div>
