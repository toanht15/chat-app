<script type="text/javascript">
    balloonMessages = {
      1: "選択肢を追加します",
      2: "企業名を代入する文字列を挿入します",
      3: "表示名（担当者名）を代入する文字列を挿入します"
    };
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

    var CaretPosition = 0;
    $('#TDictionaryWord').on('blur', function(e){
      CaretPosition = this.selectionStart;
    });
    function addOption(type){
      var textArea = document.getElementById('TDictionaryWord');
      var newCaretPosition = 0;
      switch(type){
          case 1:
            if (textArea.value.length > 0) {
                textArea.value += "\n";
            }
            textArea.value += "[] ";
            newCaretPosition = textArea.value.length;
            break;
          case 2:
            var str = "{!company}";
            textArea.value = inputString(textArea.value, str);
            newCaretPosition = CaretPosition + str.length;
            break;
          case 3:
            var str = "{!user}";
            textArea.value = inputString(textArea.value, str);
            newCaretPosition = CaretPosition + str.length;
            break;
      }
      textArea.focus();
      textArea.setSelectionRange(newCaretPosition, newCaretPosition);
    }
    function inputString(val, string){
      var startVal = val.slice(0,CaretPosition);
      var endVal = val.slice(CaretPosition);
      return startVal + string + endVal;
    }
    $('menu span').on("mouseover", function(){
      $('.balloon').children('span').text(balloonMessages[$(this).data('type')]);
      $('.balloon').css('top', ($(this).offset().top - $(this).outerHeight() - 10) + "px")
                   .css('display', "block")
                   .css('left', $(this).offset().left + "px");
    })
    .on("mouseout", function(){
      $('.balloon').css('display', "none");
    });
</script>
<?= $this->Form->create('TDictionary', array('action' => 'add')); ?>
    <div class="form01">
        <?= $this->Form->input('id', array('type' => 'hidden')); ?>
        <div>
            <div id="inputStr">
              <label class="require">入力文字</label>
              <menu class="w100">
                <span class="btn-shadow" data-type="1" onclick="addOption(1)">選択肢</span>
                <span class="btn-shadow" data-type="2" onclick="addOption(2)">企業名</span>
                <span class="btn-shadow" data-type="3" onclick="addOption(3)">表示名</span>
              </menu>
              <div class="balloon"><span></span></div>
            </div>
            <?= $this->Form->textarea('word', array('placeholder' => '入力文字', 'div' => false, 'label' => false, 'maxlength' => 200)) ?>
        </div>
        <div>
            <label class="require">使用範囲</label>
            <?= $this->Form->input('type', array('type' => 'select', 'options' => $dictionaryTypeList, 'empty' => '-- 使用範囲を選択してください --', 'div' => false, 'label' => false)) ?>
        </div>
    </div>
<?= $this->Form->end(); ?>
