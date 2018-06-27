<script type="text/javascript">

  $(function () {
    addTooltipEvent();
    //タグを含むツールチップの制御
    $('#ExtraLabel').off("mouseenter").on('mouseenter',function(event){
      targetObj = $('#ExtraTooltip').find('icon-annotation')
      var targetHeight = 486;
      targetObj.css('display','flex');
      //位置取得はjQueryだとうまく動作しないことがあるらしく、javascriptでoffsetを取得する
      targetObj.css({
        top: $(this).get(0).offsetTop - 280 + 'px',
        left: $(this).get(0).offsetLeft - 95 + 'px',
        'position':'relative'
      });
      //画面よりも下にヘルプが行ってしまう場合の処理
      var contentposition = targetHeight + 175;
      console.log(window.innerHeight);
      if(contentposition > window.innerHeight){
        targetObj.css({
          top: $(this).get(0).offsetTop - window.innerHeight*0.7 + 'px',
          left: $(this).get(0).offsetLeft + 20 + 'px'
        });
      }
    });

    $('#ExtraLabel').off("mouseleave").on('mouseleave',function(event){
     $('#ExtraTooltip').find('icon-annotation').css('display','none');
     $('#ExtraTooltip').find('ul').css('top','0px');
     $('#ExtraTooltip').find('ul').css('bottom','auto');
    });
  });


  popupEvent.closePopup = function(){
    var customvariableId = document.getElementById('TCustomVariableId').value;
    var variable_name = document.getElementById('TCustomVariableVariableName').value;
    var attribute_value = document.getElementById('TCustomVariableAttributeValue').value;
    var comment = document.getElementById('TCustomVariableComment').value;

    //非同期通信処理
    loading.load.start();
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
        loading.load.finish();
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

<?php echo $this->Html->script("common.js"); ?>
<!-- 表示されるフォーム画面 -->
<?= $this->Form->create('TCustomVariable', array('action' => 'add')); ?>
  <div class="form01">
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <div>
      <span>
        <label class="require">
          変数名
        </label>
        <div class="questionBallon">
          <icon class="questionBtn commontooltip" data-text="変数名を設定します。ここで設定した変数名に取得したデータの内容が保存され、訪問ユーザー情報に<br>自動で付与することが可能です。（別途、訪問ユーザ情報設定画面からの設定も必要です）">?</icon>
        </div>
        <?= $this->Form->input('variable_name', array('placeholder' => '変数名', 'div' => false, 'label' => false, 'maxlength' => 100)) ?>
      </span>
    </div>
    <div>
      <span>
        <label class="require">
          CSSセレクタ
        </label>
        <div class="questionBallon" id="ExtraLabel">
          <icon class="questionBtn">?</icon>
        </div>
        <?= $this->Form->input('attribute_value', array('placeholder' => 'CSSセレクタ', 'div' => false, 'label' => false, 'maxlength' => 100)) ?>
      </span>
    </div>
    <div>
      <span style="margin-top: 8px;">コメント</span>
      <?= $this->Form->textarea('comment', array('placeholder' => 'コメント', 'div' => false, 'label' => false, 'maxlength' => 300,'style' => 'margin-top: 8px; padding: 10px;')) ?>
    </div>
    <div id="ExtraTooltip" class="explainTooltip">
      <icon-annotation>
        <ul style="max-width:530px;width:510px;">
          <li><span class="detail">ウィジェットを表示している画面上から取得する値をCSSのセレクタと同様の記入方法で設定します。<br><br>例１）以下のHTMLで「田中太郎」を取得したい場合<br>【設定値】<span style="color:#4edcdc">#user_name</span><br>【HTMLの例】<br><div style="color:#4edcdc">&lt;span id=&quot;user_name&quot;&gt;田中太郎&lt;/span&gt;</div><br>例２）以下のHTMLで「田中太郎」を取得したい場合<br>【設定値】<span style="color:#4edcdc">#nav-tools .nav-line-1</span><span style="color:#ff8000">　※ID属性とクラス名の間に要半角スペース</span><br>【HTMLの例】<br><div style="color:#4edcdc">&lt;div id=&quot;nav-tools&quot;&gt;<br> 　　(中略)<br>　&lt;span class=&quot;nav-line-1&quot;&gt;田中太郎&lt;/span&gt;<br>　&lt;span class=&quot;nav-line-2&quot;&gt;リスト&lt;span class=&quot;nav-icon&quot;&gt;&lt;/span&gt;&lt;/span&gt;<br> 　　(中略)<br>&lt;/div&gt;</div></span></li>
        </ul>
      </icon-annotation>
    </div>
  </div>
<?= $this->Form->end(); ?>
