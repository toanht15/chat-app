<script src="http://cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.1.5/ZeroClipboard.min.js"></script>
<script>
  $(function () {
    // クリップボードにコピーする
    var clipboard = new ZeroClipboard(document.getElementById("copyBtn"));
    clipboard.on('ready', function(){});
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
              <span id="copyBtn" data-clipboard-text="<?=$scriptName?>" ><?=$this->Html->image('clipboard.png', array('alt' =>'コピー', 'width' => 25, 'height' => 25)) ?></span>
              <span id="copyArea"><?=$this->Form->input('fileName', array('type' => 'text', 'value' => $scriptName, 'label' => false, 'div' => false ))?></span>
            </p>
        </li>
        <li>
            <h2>デモ画面</h2>
            <?= $this->Html->link('デモ画面へ', array('controller' => 'ScriptSettings', 'action' => 'demopage'), array('target' => '_demo')) ?>
        </li>
    </ul>
</div>

</div>
