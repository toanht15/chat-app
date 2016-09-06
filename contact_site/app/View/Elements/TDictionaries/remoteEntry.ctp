<script type="text/javascript">
    balloonMessages = {
      1: "選択肢を追加します",
      2: "企業名を代入する文字列を挿入します",
      3: "表示名を代入する文字列を挿入します"
    };
    popupEvent.closePopup = function(){
        var dictionaryId = document.getElementById('TDictionaryId').value;
        var word = document.getElementById('TDictionaryWord').value;
        var sort = document.getElementById('TDictionarySort').value;
        var type = document.getElementById('TDictionaryType').value;
        $.ajax({
            type: "post",
            url: "<?=$this->Html->url('/TDictionaries/remoteSaveEntryForm')?>",
            data: {
                dictionaryId: dictionaryId,
                word: word,
                sort: sort,
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
            break;
          case 2:
            textArea.value += "{!company}";
            break;
          case 3:
            textArea.value += "{!user}";
            break;
      }
      textArea.focus();
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
              <div class="balloon"><span>ほげほげ</span></div>
            </div>
            <?= $this->Form->textarea('word', array('placeholder' => '入力文字', 'div' => false, 'label' => false, 'maxlength' => 200)) ?>
        </div>
        <div>
            <label class="require">使用範囲</label>
            <?= $this->Form->input('type', array('type' => 'select', 'options' => $dictionaryTypeList, 'empty' => '-- 使用範囲を選択してください --', 'div' => false, 'label' => false)) ?>
        </div>
        <div>
            <label>表示順</label>
            <?= $this->Form->input('sort', array('type' => 'number', 'div' => false, 'class' => 'tRight', 'label' => false)) ?>
        </div>
    </div>
<?= $this->Form->end(); ?>
