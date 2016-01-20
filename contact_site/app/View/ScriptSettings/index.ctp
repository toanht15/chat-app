<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.3/clipboard.min.js"></script>
<script>
  $(function () {
    // クリップボードにコピーする
    var clipboard = new Clipboard('#copyBtn');
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
    <div class="fLeft"><?= $this->Html->image('script_g.png', array('alt' => 'コード設置・デモ画面', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto', 'url' => array('controller' => 'Customers', 'action' => 'index'))) ?></div>
    <div style="padding: 6px 35px;">コード設置・デモ画面</div>
</div>
<div id='script_setting_content' class="p20x">
    <ul>
        <li>
            <h2>設置用タグ</h2>
            <p>
              <span id="copyBtn" data-clipboard-target="#fileName"><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
              <span id="copyArea"><?=$this->Form->input('fileName', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
            </p>
        </li>
        <li>
            <h2>デモ画面</h2>
            <?= $this->Html->link('デモ画面へ', array('controller' => 'ScriptSettings', 'action' => 'testpage'), array('target' => '_demo')) ?>
        </li>
    </ul>
</div>

</div>
