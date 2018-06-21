<script type="text/javascript">

  $(function () {
  //ツールチップの表示制御
  var topPosition = 0;
    $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
      var parentTdId = $(this).parent().attr('id');
      var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
      targetObj.find('icon-annotation').css('display','block');
      //位置取得はjQueryだとうまく動作しないことがあるらしく、javascriptでoffsetを取得する
      targetObj.css({
        top: ($(this).get(0).offsetTop - targetObj.find('ul').outerHeight() - 32 + topPosition) + 'px',
        left: $(this).get(0).offsetLeft - 6 + 'px'
      });
    });

    $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
      var parentTdId = $(this).parent().attr('id');
      var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
      targetObj.find('icon-annotation').css('display','none');;
    });
  });


  popupEvent.closePopup = function(){
    var customvariableId = document.getElementById('TCustomVariableId').value;
    var variable_name = document.getElementById('TCustomVariableVariableName').value;
    var attribute_value = document.getElementById('TCustomVariableAttributeValue').value;
    var comment = document.getElementById('TCustomVariableComment').value;

    //非同期通信処理
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

<!-- 表示されるフォーム画面 -->
<?= $this->Form->create('TCustomVariable', array('action' => 'add')); ?>
  <div class="form01">
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <div>
      <span>
        <label class="require">
          変数名
        </label>
        <div class="questionBallon" id="filterType1Label">
          <icon class="questionBtn">?</icon>
        </div>
        <?= $this->Form->input('variable_name', array('placeholder' => '変数名', 'div' => false, 'label' => false, 'maxlength' => 100)) ?>
      </span>
    </div>
    <div>
      <span>
        <label class="require">
          CSSセレクタ
        </label>
        <div class="questionBallon" id="filterType2Label">
          <icon class="questionBtn">?</icon>
        </div>
        <?= $this->Form->input('attribute_value', array('placeholder' => 'CSSセレクタ', 'div' => false, 'label' => false, 'maxlength' => 100)) ?>
      </span>
    </div>
    <div>
      <span style="margin-top: 8px;">コメント</span>
      <?= $this->Form->textarea('comment', array('placeholder' => 'コメント', 'div' => false, 'label' => false, 'maxlength' => 300,'style' => 'margin-top: 8px; padding: 10px;')) ?>
    </div>
    <div id="filterType1Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail">変数名を設定します。<br>ここで設定した変数名に取得したデータの内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{&thinsp;{変数名}&thinsp;}と指定することで利用することが可能です。</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id="filterType2Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail">ウィジェットを表示している画面上から取得する値をCSSのセレクタと同様の記入方法で設定します。<br><br>例）<br>・要素のIDが"user_name"の値を取得する場合：#user_name<br>・IDが"user_info"の要素内の"name"というクラスを持つ要素の値を取得する場合：#user_info .name</span></li>
        </ul>
      </icon-annotation>
    </div>
  </div>
<?= $this->Form->end(); ?>
