<script type="text/javascript">
  popupEvent.closePopup = function(){
    var data=JSON.parse('<?php echo  $data; ?>');
    var type = data.type;
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
        selectedCategory: selectedCategory
      },
      url: url,
      success: function(){
        location.href = "<?= $this->Html->url('/TDictionaries/index') ?>";
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
  <?= $this->Form->input('id', array('type' => 'hidden')); ?>
  <?php if($type == '1'){?>
  <!-- カテゴリ更新 -->
    <div style="text-align:center;">
      <span>カテゴリ名：</span>
      <span>
        <input type="text" name="dummy" style="display:none;">
        <input type="search" class="" size="35" id="edit_category_value" value="" >
      </span>
    </div>
  <!-- カテゴリ更新 -->
  <?php }?>
  <?php if($type == '2'){?>
  <!-- カテゴリ削除 -->
    <div style="text-align:center;">
      カテゴリを削除すると<font style="color:red">カテゴリ内の定型文も全て削除</font>されます。<br>
    </div>
  <!-- カテゴリ削除 -->
  <?php }?>
  <?php if($type == '3'){?>
  <!-- 定型文コピー -->
    <div style="text-align:center;">
    <font>
      選択された定型文をコピーします。<br>
    </font>
    <?php if($stint_flg == '1'){ ?>
      <font>
        コピー先を選択してください<br>
      </font>
      <?= $this->Form->input('type', array('type' => 'select', 'options' => $names, 'div' => false, 'label' => false)) ?>
    <?php }?>
    </div>
  <!-- 定型文コピー -->
  <?php }?>
  <?php if($type== '4'){?>
  <!-- 定型文移動 -->
    <div style="text-align:center;">
    <font>
      選択された定型文を移動します。<br>
    </font>
    <font>
      移動先を選択してください<br>
    </font>
    <?= $this->Form->input('type', array('type' => 'select', 'options' => $names, 'div' => false, 'label' => false)) ?>
    </div>
  <!-- 定型文移動 -->
  <?php }?>
</div>
<?= $this->Form->end(); ?>
