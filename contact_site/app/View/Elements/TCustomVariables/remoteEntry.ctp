<script type="text/javascript">
  popupEvent.closePopup = function(){
    var customvariableId = document.getElementById('TCustomVariableId').value;
    var variable_name = document.getElementById('TCustomVariableVariableName').value;
    var attribute_value = document.getElementById('TCustomVariableAttributeValue').value;
    var comment = document.getElementById('TCustomVariableComment').value;
    $.ajax({
      type: "post",
      url: "<?=$this->Html->url('/TCustomVariables/remoteSaveEntryForm')?>",
      data: {
        customvariableId: customvariableId,
        variable_name: variable_name,
        attribute_value: attribute_value,
        comment: comment
      },
      cache: false,
      dataType: "JSON",
      success: function(data){
        var keys = Object.keys(data), num = 0;
        $(".error-message").remove();
        if ( keys.length === 0 ) {
          location.href = "<?=$this->Html->url(array('controller' => 'TCustomVariables', 'action' => 'index'))?>";
          return false;
        }
        for (var i = 0; i < keys.length; i++) {
          if ( data[keys[i]].length > 0 ) {
            var target = $("[name='data[TCustomVariable][" + keys[i] + "]']");
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


</script>
<?= $this->Form->create('TCustomVariable', array('action' => 'add')); ?>
  <div class="form01">
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <div>
      <span>
        <label class="require">
          変数名
        </label>
        <div class="questionBallon" id="filterType1Label">
          <icon class="questionBtn variable_helpBtn">?</icon>
        </div>
        <?= $this->Form->input('variable_name', array('placeholder' => '変数名', 'div' => false, 'label' => false, 'maxlength' => 100,'style' => 'margin-left: 15px;')) ?>
      </span>
    </div>
    <div>
      <span>
        <label class="require">
          CSSセレクタ
        </label>
        <div class="questionBallon" id="filterType2Label">
          <icon class="questionBtn selecter_helpBtn">?</icon>
        </div>
        <?= $this->Form->input('attribute_value', array('placeholder' => 'CSSセレクタ', 'div' => false, 'label' => false, 'maxlength' => 100,'style' => 'margin-left: 15px;')) ?>
      </span>
    </div>
    <div>
      <span style="margin-top: 8px;">コメント</span>
      <?= $this->Form->textarea('comment', array('placeholder' => 'コメント', 'div' => false, 'label' => false, 'maxlength' => 300,'style' => 'margin-top: 8px; padding: 10px;')) ?>
    </div>
    <div class="explainTooltip1">
      <icon-annotation>
        <ul>
          <li><span class="detail">変数名のヘルプです。</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div class="explainTooltip2">
      <icon-annotation>
        <ul>
          <li><span class="detail">CSSセレクタのヘルプです</span></li>
        </ul>
      </icon-annotation>
    </div>
  </div>
<?= $this->Form->end(); ?>
