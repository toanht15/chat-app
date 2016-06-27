<script type="text/javascript">
    popupEvent.closePopup = function(){
        var dictionaryId = document.getElementById('TDictionaryId').value;
        var word = document.getElementById('TDictionaryWord').value;
        var type = document.getElementById('TDictionaryType').value;
        $.ajax({
            type: "post",
            url: "<?=$this->Html->url('/TDictionaries/remoteSaveEntryForm')?>",
            data: {
                dictionaryId: dictionaryId,
                word: word,
                type: type
            },
            cache: false,
            dataType: "JSON",
            success: function(data){
                var keys = Object.keys(data), num = 0;
                $(".error-message").remove();

                if ( keys.length === 0 ) {
                    location.href = "<?=$this->Html->url(array('controller' => 'TDictionaries', 'action' => 'index'))?>";
                    return false;
                }
                for (var i = 0; i < keys.length; i++) {
                    if ( data[keys[i]].length > 0 ) {
                        var target = $("[name='data[TDictionary][" + keys[i] + "]']");
                        for (var u = 0; u < data[keys[i]].length; u++) {
                            target.after("<p class='error-message hide'>" + data[keys[i]][u] + "</p>");
                            num ++;
                        }
                    }
                }
                if ( num > 0 ) {
                    var newHeight = $("#popup-content").height() + (num * 15);
                    $("#popup-frame").animate({
                        height: newHeight + "px"
                    }, {
                        duration: 500,
                        complete: function(){
                            $(".error-message.hide").removeClass("hide");
                            $(this).css("overflow", "");
                        }
                    });
                }
            }
        });
    };
    function addOption(type){
      var textArea = document.getElementById('TDictionaryWord');
      switch(type){
          case 1:
          if (textArea.value.length > 0) {
              textArea.value += "\n";
          }
          textArea.value += "[] ";
          textArea.focus();
      }
    }
</script>
<?= $this->Form->create('TDictionary', array('action' => 'add')); ?>
    <div class="form01">
        <?= $this->Form->input('id', array('type' => 'hidden')); ?>
        <div>
            <div id="inputStr">
              <label class="require">入力文字</label>
              <span class="greenBtn btn-shadow" onclick="addOption(1)">選択肢を追加する</span>
            </div>
            <?= $this->Form->textarea('word', array('placeholder' => '入力文字', 'div' => false, 'label' => false, 'maxlength' => 200)) ?>
        </div>
        <div>
            <label class="require">使用範囲</label>
            <?= $this->Form->input('type', array('type' => 'select', 'options' => $dictionaryTypeList, 'empty' => '-- 使用範囲を選択してください --', 'div' => false, 'label' => false)) ?>
        </div>
    </div>
<?= $this->Form->end(); ?>
