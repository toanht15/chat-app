<script type="text/javascript">
  popupEvent.closePopup = function(){
    var data=JSON.parse('<?php echo  $data; ?>');
    var type = data.type;
    var dstoken = document.getElementById('dstoken').value;
    switch (type){
    case '1':
      //カテゴリ編集処理
      var url = "<?= $this->Html->url('/TDictionaries/remoteCategoryEdit') ?>";
      var name = document.getElementById("edit_category_value").value;
      var selectedCategory = "";
      break;
    case '2':
      //カテゴリ削除処理
      var url = "<?= $this->Html->url('/TDictionaries/remoteCategoryDelete') ?>";
      var name = "";
      var selectedCategory = "";
      break;
    case '3':
      //定型文コピー処理
      var url = "<?= $this->Html->url('/TDictionaries/remoteCopyEntryForm') ?>";
      var name = "";
      if("<?= $stint_flg ?>" == "1"){
        var selectedCategory = document.getElementById("TDictionaryType").value;
      }
      else{
        var selectedCategory = "<?= $id ?>";
      }
      break;
    case '4':
      //定型文移動処理
      var url = "<?= $this->Html->url('/TDictionaries/remoteMoveEntryForm') ?>";
      var name = "";
      var selectedCategory = document.getElementById("TDictionaryType").value;
      break;
    }
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        data: data,
        name: name,
        dstoken: dstoken,
        selectedCategory: selectedCategory
      },
      url: url,
      success: function(){
        if(type == '3' || type == '4'){
          location.href = "<?=$this->Html->url(array('controller' => 'TDictionaries', 'action' => 'index', 'tabindex' => $tabindex))?>";
        }
        else{
          location.href = "<?= $this->Html->url('/TDictionaries/index') ?>";
        }
      }
    });
  }


  $( function() {
    var type = "<?= $type ?>";
    if(type == 1){
      document.getElementById("edit_category_value").value = "<?= h($name) ?>";
    }
    $("input[type=search]").keypress(function(ev) {
        if ((ev.which && ev.which === 13) ||
            (ev.keyCode && ev.keyCode === 13)) {
          return false;
        } else {
          return true;
        }
    });
  });
</script>
<?= $this->Form->create('TDictionary'); ?>
<div class="form01">
  <input type="hidden" id="dstoken" name="dstoken" value="<?=$dstoken?>">
  <?= $this->Form->input('id', array('type' => 'hidden')); ?>
  <?php if($type == '1'){?>
  <!-- カテゴリ更新 -->
    <div style="text-align:center;">
      <br/>
      <span>カテゴリ名：　</span>
      <span>
        <input type="text" name="dummy" style="display:none;">
        <input type="text" class="" size="25" id="edit_category_value" value="" >
      </span>
      <br/>
      <br/>
    </div>
  <!-- カテゴリ更新 -->
  <?php }?>
  <?php if($type == '2'){?>
  <!-- カテゴリ削除 -->
    <br/>
    <br/>
    <div style="text-align:center;">
      カテゴリを削除すると<font style="color:rgb(192, 0, 0)">カテゴリ内の定型文も全て削除</font>されます。<br>
    </div>
    <br/>
    <br/>
  <!-- カテゴリ削除 -->
  <?php }?>
  <?php if($type == '3'){?>
  <!-- 定型文コピー -->
    <div style="text-align:center;">
    <font>
      選択された定型文をコピーします。<br/><br/>
    </font>
    <?php if($stint_flg == '1'){ ?>
      <font>
        コピー先を選択してください<br/><br/>
      </font>
      <?= $this->Form->input('type', array('type' => 'select', 'options' => $names, 'div' => false, 'label' => false)) ?>
      <br/>
      <br/>
    <?php }?>
    </div>
  <!-- 定型文コピー -->
  <?php }?>
  <?php if($type== '4'){?>
  <!-- 定型文移動 -->
    <div style="text-align:center;">
    <font>
      選択された定型文を移動します。<br/><br/>
    </font>
    <font>
      移動先を選択してください<br/><br/>
    </font>
    <?= $this->Form->input('type', array('type' => 'select', 'options' => $names, 'div' => false, 'label' => false)) ?>
    <br/>
    <br/>
    </div>
  <!-- 定型文移動 -->
  <?php }?>
</div>
<?= $this->Form->end(); ?>