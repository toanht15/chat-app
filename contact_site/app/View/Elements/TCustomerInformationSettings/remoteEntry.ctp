<script type="text/javascript">
  //保存時の処理を行う必要があるので内部開発をするときに随時変更していく

  //特定項目を選択した際に、追加メニュー分の高さを確保する
  //特定項目から選択が外れた場合は、その分の高さを削減する
  $(function () {
    $('#CustomVariableForm').on('click', function(e){
      if($(this).prop('checked')) {
        $('#CustomVariableWrap').css('display','');
        var popup = $('#popup-frame');
        popup.height(popup.height()+40);
      }else{
        $('#CustomVariableWrap').css('display','none');
        var popup = $('#popup-frame');
        popup.height(popup.height()-40);
      }
    });
    var selectflag = 0;
    $('#SelectListForm').change(function(e){
      if(document.getElementById('TCustomerInformationSettingタイプ名').value == 2){
        $('#SelectListWrap').css('display','');
        var popup = $('#popup-frame');
        popup.height(popup.height()+57);
        selectflag = 1;
      }else{
        $('#SelectListWrap').css('display','none');
        if(selectflag == 1){
            var popup = $('#popup-frame');
            popup.height(popup.height()-57);
            selectflag = 0;
        }
      }
    });
    //ツールチップの表示制御
    var topPosition = 0;
    $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
      console.log('aiueo');
      var parentTdId = $(this).parent().attr('id');
      console.log(parentTdId);
      var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
      console.log(targetObj);
      targetObj.find('icon-annotation').css('display','block');
      //jQueryだとうまく動作しないことがあるらしく、javascriptでoffsetを取得する
      targetObj.css({
        top: ($(this).get(0).offsetTop - targetObj.find('ul').outerHeight() - 32 + topPosition) + 'px',
        left: $(this).get(0).offsetLeft - 6 + 'px'
      });
    });

    $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
      var parentTdId = $(this).parent().attr('id');
      var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
      targetObj.find('icon-annotation').css('display','none');
    });
  });

  popupEvent.closePopup = function(){
    var customvariableId = document.getElementById('TCustomerInformationSettingId').value;
    var variable_name = document.getElementById('TCustomerInformationSettingVariableName').value;
    var attribute_value = document.getElementById('TCustomerInformationSettingAttributeValue').value;
    var comment = document.getElementById('TCustomerInformationSettingComment').value;

    //非同期通信処理
    $.ajax({
      type: "post",
      url: "<?=$this->Html->url('/TCustomerInformationSettings/remoteSaveEntryForm')?>",
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
          location.href = "<?=$this->Html->url(array('controller' => 'TCustomerInformationSettings', 'action' => 'index'))?>";
          return false;
        }
        for (var i = 0; i < keys.length; i++) {
          if ( data[keys[i]].length > 0 ) {
            var target = $("[name='data[TCustomerInformationSetting][" + keys[i] + "]']");
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
<?= $this->Form->create('TCustomerInformationSetting', ['action' => 'add']); ?>
  <div class="form01">
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <div>
      <span>
        <label class="require">
          項目名
        </label>
        <div class="questionBallon" id="filterType1Label">
          <icon class="questionBtn">?</icon>
        </div>
        <?= $this->Form->input('項目名', ['div' => false, 'label' => false, 'maxlength' => 100,'style' => 'margin-left: 15px;']) ?>
      </span>
    </div>
    <div id="SelectListForm" style="margin-bottom: 8px">
      <span>
        <label class="require">
          タイプ
        </label>
        <div class="questionBallon" id="filterType2Label">
          <icon class="questionBtn">?</icon>
        </div>
        <?= $this->Form->input('タイプ名',
        ['type' => 'select',
        'options' => ['テキストボックス','テキストエリア','プルダウン'],
        'div' => false,
        'label' => false,
        'maxlength' => 100,
        'style' => 'margin-left: 15px;font-size: 1em',
        ]) ?>
      </span>
    </div>
    <div id="SelectListWrap" style="display: none; margin:0px!important">
      <span style="padding-left: 110px">
        <label class="require" style="vertical-align: 7px;">
          プルダウンリスト
        </label>
      <?= $this->Form->textarea('プルダウンリスト', ['placeholder' => '', 'div' => false, 'label' => false, 'maxlength' => 300,'style' =>
        'box-sizing: content-box; height: 1.2em; border-radius: 5px!important; margin-top: 8px; padding: 1px;']) ?>
      </span>
      <p style="font-size: 10px; margin: 0px; padding-left: 217px">※リスト表示する内容を改行して複数入力してください</p>
    </div>
    <div>
      <span>
        <label for="ViewItemForm" style="cursor:pointer; margin-bottom: 1em">
          <input type="checkbox" id="ViewItemForm" style="position:relative; top:-1px; margin-left:15px"/>
          この項目をリアルタイムモニターや履歴の一覧に表示する
        </label>
        <div class="questionBallon" id="filterType3Label">
          <icon class="questionBtn">?</icon>
        </div>
      </span>
    </div>
    <div>
      <span>
        <label for="MailableForm" style="cursor:pointer; margin-bottom: 1em">
          <input type="checkbox" id="MailableForm" style="position:relative; top:-1px; margin-left:15px"/>
          メール送信時にメール本文に記載する
        </label>
        <div class="questionBallon" id="filterType4Label">
          <icon class="questionBtn">?</icon>
        </div>
      </span>
    </div>
    <div>
      <span>
        <label for="CustomVariableForm" style="cursor:pointer; margin-bottom: 1em">
          <input type="checkbox" id="CustomVariableForm" style="position:relative; top:-1px; margin-left:15px"/>
          カスタム変数の値を自動的に登録する
        </label>
        <div class="questionBallon" id="filterType5Label">
          <icon class="questionBtn">?</icon>
        </div>
      </span>
    </div>
    <div id="CustomVariableWrap" style="display: none">
      <span style="padding-left: 34px">
        <label class="require">
          カスタム変数
        </label>
        <div class="questionBallon" id="filterType6Label">
          <icon class="questionBtn">?</icon>
        </div>
        <?php $customvariablelist = array_column($tCustomVariableList, 'TCustomerInformationSetting');?>
        <?php $variablelist = array_column($customvariablelist, 'variable_name');?>
        <?= $this->Form->input('カスタム変数',
        ['type' => 'select',
        'options' => $variablelist,
        'div' => false,
        'label' => false,
        'maxlength' => 100,
        'style' => 'margin-left: 15px;font-size: 1em'
        ]) ?>
      </span>
    </div>
    <div>
      <span style="margin-top: 8px;">コメント</span>
      <?= $this->Form->textarea('コメント変数', ['placeholder' => 'コメント', 'div' => false, 'label' => false, 'maxlength' => 300,'style' => 'margin-top: 8px; padding: 10px;']) ?>
    </div>
    <div id="filterType1Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail">項目名のヘルプです。</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id="filterType2Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail">タイプのヘルプです。</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id="filterType3Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail">CheckBox1のヘルプです。</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id="filterType4Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail">CheckBox2のヘルプです。</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id="filterType5Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail">CheckBox3のヘルプです。</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id="filterType6Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail">カスタム変数のヘルプです。</span></li>
        </ul>
      </icon-annotation>
    </div>
  </div>
<?= $this->Form->end(); ?>
