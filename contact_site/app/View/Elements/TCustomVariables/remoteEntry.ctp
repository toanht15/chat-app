<script type="text/javascript">

  $(function () {
  //ツールチップの表示制御
    $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
      var parentTdId = $(this).parent().attr('id');
      var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
      targetObj.find('icon-annotation').css('display','block');
      //位置取得はjQueryだとうまく動作しないことがあるらしく、javascriptでoffsetを取得する
      targetObj.css({
        top: $(this).get(0).offsetTop  - 57  + 'px',
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
          <li><span class="detail" style="width:314px;">変数名を設定します。<br>ここで設定した変数名に取得したデータの内容が保存され、訪問ユーザー情報に自動で付与することが可能です。（別途、訪問ユーザ情報設定画面からの設定も必要です）</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id="filterType2Tooltip" class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail" style="width:550px;">ウィジェットを表示している画面上から取得する値をCSSのセレクタと同様の記入方法で設定します。<br><br>例１）<br>・要素のIDが&quot;user_name&quot;の値を取得する場合：#user_name<br>・IDが&quot;user_info&quot;の要素内の&quot;name&quot;というクラスを持つ要素の値を取得する場合：#user_info .name<br><br>例２）以下のHTMLで「田中太郎さん」を取得したい場合：#nav-tools .nav-line-1<br>&lt;div id=&quot;nav-tools&quot;&gt;<br>　　・<br>　　・<br>　　・<br>  &lt;span class=&quot;nav-line-1&quot;&gt;田中太郎さん&lt;/span&gt;<br>  &lt;span class=&quot;nav-line-2&quot;&gt;リスト&lt;span class=&quot;nav-icon nav-arrow&quot; style=&quot;visibility: visible;&quot;&gt;&lt;/span&gt;<br>　　・<br>　　・<br>　　・<br>&lt;/div&gt;ウィジェットを表示している画面上から取得する値を<br>CSSのセレクタと同様の記入方法で設定します。<br><br>例１）要素のIDが&quot;user_name&quot;の値を取得する場合<br>　=>　#user_name<br>例２）IDが&quot;user_info&quot;の要素内の&quot;name&quot;というクラスを持つ要素の値を取得する場合<br>　=>　#user_info .name<br>例３）以下のHTMLで「田中太郎さん」を取得したい場合<br>　=>　#nav-tools .nav-line-1<br><br><div style="color: #4bacc6">&lt;div id=&quot;nav-tools&quot;&gt;<br>　　・<br>　　・<br>  &lt;span class=&quot;nav-line-1&quot;&gt;田中太郎さん&lt;/span&gt;<br>  &lt;span class=&quot;nav-line-2&quot;&gt;リスト&lt;span class=&quot;nav-icon nav-arrow&quot; style=&quot;visibility: visible;&quot;&gt;&lt;/span&gt;<br>　　・<br>　　・<br>&lt;/div&gt;</div></span></li>
        </ul>
      </icon-annotation>
    </div>
  </div>
<?= $this->Form->end(); ?>
