<script type="text/javascript">

  //特定項目を選択した際に、追加メニュー分の高さを確保する
  //特定項目から選択が外れた場合は、その分の高さを削減する
  $(function () {
  var mX;
  var mY;
  document.body.addEventListener("mousemove", function(e){
    //マウスの座標を取得する
    mX = e.pageX;  //X座標
    mY = e.pageY;  //Y座標
  });
  var save_minheight;
  var save_minwidth;
  var click_save = false;
  addTooltipEvent();
  <?php if(isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]){ ?>
  //項目名が会社名のものを編集するときはcompany_name_existのidが存在する
  if($('#company_name_exist').length){
    $('#TCustomerInformationSettingItemName').blur(function(){
      //保存ボタンの位置を取得する
        save_minheight = $("#popup-button > .textBtn").offset().top;
        save_minwidth = $("#popup-button > .textBtn").offset().left;


      //X軸とY軸で判定を行い
      //保存ボタンが押された状態での項目名変更であればフラグを立てておく
      if(save_minheight < mY && mY < Number(save_minheight)+25){
        if(save_minwidth < mX && mX < Number(save_minwidth)+75){
          click_save = true;
        }
      }

    if($('#TCustomerInformationSettingItemName')[0].value != "会社名"){
      message = "項目名を変更すると企業情報付与された<br>会社名の検索ができなくなります。<br>"
              + "<br><b>本当に項目名を変更しますか？</b>";
      modalOpenOverlap.call(window, message, 'p-tcustomerinformation-alert', '必ず確認してください', 'moment');
        $('#popupCloseOverlapBtn').on('click', function(){
          $('#TCustomerInformationSettingItemName')[0].value = "会社名";
        });

        popupEventOverlap.cancelPopup = (function(){
          $('#TCustomerInformationSettingItemName')[0].value = "会社名";
          change_save = false;
          popupEventOverlap.close();
        });

        popupEventOverlap.closeNoPopupOverlap = (function(){
          popupEventOverlap.close();
          if(click_save){
            popupEvent.closePopup();
          }
        });
      }
    });
  }
    <?php } ?>




    $('#TCustomerInformationSettingItemName').keydown(function(event){
      var str = event.keyCode;
      if(str == 13){
        return false;
      }
    });



    //各種変数の設定
    var popupframe = $('#popup-frame');
    var popupbutton = $('#popup-button');

    var labelposition = $("#pulldown_label")
    $("#TCustomerInformationSettingInputOption").height(8);
    var heightsize = 18;


  //入力量に応じてプルダウンのテキスト入力エリアが拡大する処理
    var column_size = 1;
    enter_flg = false;



    var pulldown_Id = "#TCustomerInformationSettingInputOption";
    $("#TCustomerInformationSettingInputOption").on("input",function(e){
      scroll_flg = false;
      var pulldown_array = $(e.target).val().split(/\r\n|\r|\n/);
      var column_size = pulldown_array.length;
      //ポップアップサイズを取得し、もしブラウザの縦幅以上だったらスクロール表示させる
      if(column_size>4){
        $(e.target).css('overflow-y','scroll');
        $(e.target).height(20*4);
      }else{
        $(e.target).css('overflow-y','hidden');
        $(e.target).height(20*column_size);
      }



      $(e.target).css('overflow-x','hidden');
      for(var i = 0; i < column_size; i++){
        if($("#widther").text(pulldown_array[i]).get(0).offsetWidth>260){
          scroll_flg = true;
        }
      }
      if(scroll_flg){
        $(e.target).css({'overflow-x':'scroll','padding-bottom':'19px'});
        if($(e.target).css('overflow-y')=='scroll'){
          $(e.target).height(20*column_size +18);
          $(e.target).css({'padding-bottom':'4px'});
        }
      }else{
        $(e.target).css({'overflow-x':'hidden','padding-bottom':'4px'});
      }
      $("#widther").empty();
      popupEvent.resize();
    });



    /******既に入力値があった場合にそのサイズに合わせる処理******
     *入力がn行あったら、そのn行全てを表示させておく必要がある。*
    */
    var string_size = $("#length_counter").attr('class');
    var column_size = $("#column_counter").attr('class');
      if(column_size>3){
        $("#TCustomerInformationSettingInputOption").css('overflow-y','scroll');
        $("#TCustomerInformationSettingInputOption").height(20*4 + 3);
      }else{
        $("#TCustomerInformationSettingInputOption").css('overflow-y','hidden');
        if(string_size === "true"){
          $("#TCustomerInformationSettingInputOption").height(20*column_size +23);
        }else{
          $("#TCustomerInformationSettingInputOption").height(20*column_size +8);
        }
      }



    var selectflag = 0;
    //エディット時に、既にプルダウンが選ばれていた場合の処理
    if(document.getElementById('TCustomerInformationSettingInputType').value == 3){
      $('#SelectListWrap').css('display','');
      popupEvent.resize();
      selectflag = 1;
    }


    //エディット時に、既にカスタム変数チェックボックスが選択されている場合
    if(document.getElementById('TCustomerInformationSettingSyncCustomVariableFlg').checked){
        $('#CustomVariableWrap').css('display','');
        popupEvent.resize();
    }

    $('#TCustomerInformationSettingSyncCustomVariableFlg').on('click', function(e){
        if($(this).prop('checked')) {
          $('#CustomVariableWrap').css('display','');
        }else{
          $('#CustomVariableWrap').css('display','none');
        }
        popupEvent.resize();
      });

    $('#SelectListForm').change(function(e){
      if(document.getElementById('TCustomerInformationSettingInputType').value == 3){
        $('#SelectListWrap').css('display','');
        selectflag = 1;
      }else{
        $('#SelectListWrap').css('display','none');
        if(selectflag == 1){
          selectflag = 0;
        }
      }
      popupEvent.resize();
    });
    });

  //保存ボタン押下時処理
  popupEvent.closePopup = function(){
    var customerinformationsettingId = document.getElementById('TCustomerInformationSettingId').value;
    var item_name = document.getElementById('TCustomerInformationSettingItemName').value;
    var input_type = Number(document.getElementById('TCustomerInformationSettingInputType').value);
    if(input_type == 3){
      if(!(document.getElementById('TCustomerInformationSettingInputOption').value)){
        loading.load.finish();
        exit();
      }else{
        var input_option = document.getElementById('TCustomerInformationSettingInputOption').value;
      }
    }
    var show_realtime_monitor_flg = 0;
    var show_send_mail_flg = 0;
    var sync_custom_variable_flg = 0;
    var t_custom_variables_id = 0;
    if($("#TCustomerInformationSettingShowRealtimeMonitorFlg").prop('checked')){
        show_realtime_monitor_flg = 1;
    }
    if($("#TCustomerInformationSettingShowSendMailFlg").prop('checked')){
        show_send_mail_flg = 1;
    }

    if($("#TCustomerInformationSettingSyncCustomVariableFlg").prop('checked')){
        var sync_custom_variable_flg = 1;
        t_custom_variables_id = document.getElementById('TCustomerInformationSettingTCustomVariablesId').value;
    }
    var comment = document.getElementById('TCustomerInformationSettingComment').value;
    //非同期通信処理
    loading.load.start();
    $.ajax({
      type: "post",
      url: "<?=$this->Html->url('/TCustomerInformationSettings/remoteSaveEntryForm')?>",
      data: {
        customerinformationsettingId: customerinformationsettingId,
        item_name: item_name,
        input_type: input_type,
        input_option: input_option,
        show_realtime_monitor_flg: show_realtime_monitor_flg,
        show_send_mail_flg: show_send_mail_flg,
        sync_custom_variable_flg: sync_custom_variable_flg,
        t_custom_variables_id: t_custom_variables_id,
        input_option: input_option,
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
          loading.load.finish();
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
<span id="widther" style="visibility:hidden;position:absolute;white-space:nowrap;"></span>
<?= $this->Form->create('TCustomerInformationSetting', ['action' => 'add']);?>
<div id = "column_counter" class="<?php
//一覧表示のチェックボックスにチェックが入っているかの判別
$uncheckedflg = true;
if(isset($this->request->data['TCustomerInformationSetting'])){
  if($this->request->data['TCustomerInformationSetting']['show_realtime_monitor_flg']){
    $uncheckedflg = false;
  }
  //input_optionの行数取得(改行コード検索)
  $pulldown_str = $this->request->data['TCustomerInformationSetting']['input_option'];
  echo substr_count($pulldown_str,"\n");
}
?>"></div>
<div id = "length_counter" class="<?php
if(isset($this->request->data['TCustomerInformationSetting'])){
  $max_str_length = 0;
  $str_array = explode("\n", $pulldown_str);
  foreach($str_array as $str_value){
    if($max_str_length < mb_strwidth($str_value)){
      $max_str_length = mb_strwidth($str_value);
    }
  }
  if($max_str_length > 39){
    echo "true";
  }else{
    echo "false";
  }
}
?>"></div>
<?php if(isset($this->request->data['TCustomerInformationSetting'])){
  if(strcmp($this->request->data['TCustomerInformationSetting']['item_name'],"会社名") == 0){
    $itemdiv = "<div id='company_name_exist'></div>";
    echo $itemdiv;
  }
}
?>
  <div class="form01">
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <div>
      <span>
        <label class="require">
          項目名
        </label>
        <div class="questionBallon">
          <icon class="questionBtn commontooltip" data-text="項目名を入力します。<br>（会社名、名前など）">?</icon>
        </div>
        <?= $this->Form->input('item_name', ['placeholder' => '項目名', 'div' => false, 'label' => false, 'maxlength' => 100,'style' => 'margin-left: 15px;']) ?>
      </span>
    </div>
    <div id="SelectListForm" style="margin-bottom: 8px">
      <span>
        <label class="require">
          タイプ
        </label>
        <div class="questionBallon">
          <icon class="questionBtn commontooltip" data-text="テキストボックス、テキストエリア、<br>プルダウンから選択可能です。">?</icon>
        </div>
        <?= $this->Form->input('input_type',
        ['type' => 'select',
        'options' => [
          '1' => 'テキストボックス',
          '2' => 'テキストエリア',
          '3' => 'プルダウン'
        ],
        'div' => false,
        'label' => false,
        'maxlength' => 100,
        'style' => 'margin-left: 15px;font-size: 1em',
        ]) ?>
      </span>
    </div>
    <div id="SelectListWrap" style="display: none; margin:0px!important">
      <span style="padding-left: 97px">
        <label id="pulldown_label" class="require">
          プルダウンリスト
        </label>
      <?= $this->Form->textarea('input_option', ['div' => false, 'label' => false, 'maxlength' => 300]) ?>
      </span>
      <p style="font-size: 10px; margin: 0px; padding-left: 219px">※リスト表示する内容を改行して複数入力してください</p>
    </div>
    <div>
    <!-- 一覧表示のチェックが幾つ付いているかのカウント -->
    <?php
    $count = 0;
    foreach($FlgList as $value){
      if($value){
        $count = $count+1;
      }
    }
    ?>
      <span id="BanedType1Label">
        <label class="forcheckbox <?php if((($count >= 3)&&$uncheckedflg))echo "grayfont commontooltip"?>" data-text="一覧表示に登録できるのは3つまでです。" for="TCustomerInformationSettingShowRealtimeMonitorFlg">
          <?= $this->Form->input('show_realtime_monitor_flg',['type' => 'checkbox', 'div' => false, 'label' => "", 'disabled' => (($count >= 3)&&$uncheckedflg)]) ?>
          この項目をリアルタイムモニターや履歴の一覧に表示する
        </label>
        <div class="questionBallon">
          <icon class="questionBtn commontooltip" data-text="リアルタイムモニタやチャット履歴の一覧画面に表示させる場合にチェックをしてください。（一覧に表示できる項目は最大で３つまでとなります。）">?</icon>
        </div>
      </span>
    </div>
    <div>
      <span>
        <label class="forcheckbox" for="TCustomerInformationSettingShowSendMailFlg">
          <?= $this->Form->input('show_send_mail_flg',['type' => 'checkbox', 'div' => false, 'label' => ""])?>
          メール送信時にメール本文に記載する
        </label>
        <div class="questionBallon">
          <icon class="questionBtn commontooltip" data-text="メール本文にこの項目を記載する場合にチェックをしてください。">?</icon>
        </div>
      </span>
    </div>
    <div>
      <span id="BanedType2Label">
        <label class="forcheckbox <?php if(empty($variableList))echo "grayfont commontooltip"?>" data-text="カスタム変数の値が登録されていません。" for="TCustomerInformationSettingSyncCustomVariableFlg">
          <?= $this->Form->input('sync_custom_variable_flg',['type' => 'checkbox', 'div' => false, 'label' => "", 'disabled' => empty($variableList)])?>
          カスタム変数の値を自動的に登録する
        </label>
        <div class="questionBallon">
          <icon class="questionBtn commontooltip" data-text="ページから取得した値（ログインユーザー名など）を自動で訪問ユーザ情報に登録することが可能です、本機能を利用する場合は事前にカスタム変数の設定をしてください。">?</icon>
        </div>
      </span>
    </div>
    <div id="CustomVariableWrap" style="display: none">
      <span style="margin-left:19px">
        <label class="require">
          カスタム変数
        </label>
        <div class="questionBallon">
          <icon class="questionBtn commontooltip" data-text="登録したいカスタム変数を選択してください。">?</icon>
        </div>
        <?= $this->Form->input('t_custom_variables_id',
        ['type' => 'select',
        'options' => $variableList,
        'div' => false,
        'label' => false,
        'maxlength' => 100,
        'style' => 'margin-left: 15px;font-size: 1em;padding-right:50px;max-width:380px;'
        ]) ?>
      </span>
    </div>
    <div>
      <span style="margin-top: 8px;">コメント</span>
      <?= $this->Form->textarea('comment', ['placeholder' => 'コメント', 'div' => false, 'label' => false, 'maxlength' => 300]) ?>
    </div>
  </div>
<?= $this->Form->end(); ?>