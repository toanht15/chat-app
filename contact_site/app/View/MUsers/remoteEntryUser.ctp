<script type="text/javascript">
<?php $this->request->data['MUser']['user_name'] = htmlspecialchars($this->request->data['MUser']['user_name'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['display_name'] = htmlspecialchars($this->request->data['MUser']['display_name'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['mail_address'] = htmlspecialchars($this->request->data['MUser']['mail_address'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['password'] = htmlspecialchars($this->request->data['MUser']['password'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['permission_level'] = htmlspecialchars($this->request->data['MUser']['permission_level'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['profile_icon'] = htmlspecialchars(json_decode($this->request->data['MUser']['settings'], true)['profileIcon'], ENT_QUOTES, 'UTF-8');?>
<?php
  if( empty($this->request->data['MUser']['memo']) ){
    if( !empty($this->request->data['MUser']['user_name']) ) {
      $this->request->data['MUser']['memo'] = $this->request->data['MUser']['user_name'];
    }
  } else {
    $this->request->data['MUser']['memo'] = htmlspecialchars($this->request->data['MUser']['memo'], ENT_QUOTES, 'UTF-8');
  }
?>
  var checkIconIsDefault = function() {
    var icon = $('.hover-changer')[0];
    return icon.tagName === "I";
  };
  var disableSetDefaultBtn = function() {
    var defaultBtn = document.getElementById("setToDefault");
    defaultBtn.classList.remove("greenBtn");
    defaultBtn.classList.add("disOffgrayBtn");
  };
  var ableSetDefaultBtn = function() {
    var defaultBtn = document.getElementById("setToDefault");
    defaultBtn.classList.remove("disOffgrayBtn");
    defaultBtn.classList.add("greenBtn");
  };

  <?php if($coreSettings[C_COMPANY_USE_ICON_SETTINGS]): ?>
  $(function(){
    if( checkIconIsDefault() ) {
      disableSetDefaultBtn();
    }
  });
  <?php endif; ?>

  var confirmToDefault = function() {
    if( checkIconIsDefault() ) {
      //デフォルトアイコンの場合はなにもさせない。
      return;
    }
    message = "現在設定されているアイコンをデフォルトアイコンに戻します。<br>よろしいですか？<br>";
    modalOpenOverlap.call(window, message, 'p-seticontodefault-alert', '確認してください', 'moment');
    initPopupOverlapEvent();

  };

  var initPopupOverlapEvent = function() {
    popupEventOverlap.closePopup = function(){
      var icon = $('.hover-changer')[0];
      if(icon.tagName === "IMG") {
        changeIconToDefault( icon );
      }
      popupEventOverlap.closeNoPopupOverlap();
    };
  };

  var changeIconToDefault = function( icon ) {
    if( icon.parentNode ) {
      icon.parentNode.removeChild( icon );
    }
    var iconComponent = $('.profile_icon_register > div')[0];
    var defaultElm = document.createElement("i");
    defaultElm.classList.add("fa-user","fal","hover-changer");
    defaultElm.style.color = "<?=$iconFontColor ?>";
    defaultElm.style.backgroundColor = "<?=$iconMainColor ?>";
    iconComponent.appendChild(defaultElm);
  };

  var changeProfileIcon = function(e) {
    var files = e.target.files;
    if ( window.URL && files.length > 0 ) {
      var file = files[files.length-1];

      // jpeg/jpg/png
      var reg = new  RegExp(/image\/(png|jpeg|jpg)/i);
      if ( !reg.exec(file.type) ) {
        $("#MUserUploadProfileIcon").val("");
        return false;
      }
      var url = window.URL.createObjectURL(file);
      target = changeIconPath(url, file.name);
      openTrimmingDialog(function(){
        beforeTrimmingInit(url, $('.hover-changer'));
        trimmingInit(null,$('#TrimmingInfo'), 1, "profile_icon");
      });
    }
  };

  $('.hover-changer').click(function(){
    $('#MUserUploadProfileIcon').click();
  });

  $('#MUserUploadProfileIcon').change(function(e){
    changeProfileIcon(e);
    ableSetDefaultBtn();
  });

  var  changeIconPath = function(path, fileName){
    var currentIcon = document.querySelector('.hover-changer');
    var newIcon = document.createElement("img");
    var parentElm = currentIcon.parentNode;
    if( currentIcon.parentNode ) {
      currentIcon.parentNode.removeChild( currentIcon );
    }
    newIcon.src = path;
    newIcon.classList.add("hover-changer");
    parentElm.appendChild(newIcon);
    var iconData = document.getElementById('MUserProfileIcon');
    iconData.value = fileName;
    return newIcon;
  };


  function openTrimmingDialog(callback){
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'MWidgetSettings', 'action' => 'remoteTimmingInfo']) ?>",
      success: function(html){
        modalOpenOverlap.call(window, html, 'p-profile-icon-trimming', 'トリミング', 'moment');
        callback();
      }
    });
  }



popupEvent.closePopup = function(){
        var form = $('#MUserAddForm').get(0);
        var page = Number("<?=$page?>");
        var formData = new FormData( form );
        var userId = document.getElementById('MUserId').value;
        var userName = document.getElementById('MUserUserName').value;
        var displayName = document.getElementById('MUserDisplayName').value;
        var mailAddress = document.getElementById('MUserMailAddress').value;
        var password = document.getElementById('MUserNewPassword').value;
        var permissionLevel = document.getElementById('MUserPermissionLevel').value;
        var accessToken = "<?=$token?>";
        formData.append("accessToken", accessToken);
        // 氏名が無い場合は、表示名を氏名に代入する（氏名は表示以外どこにも使われていない為）
        if( !userName ) {
          userName = displayName;
        }
        $.ajax({
            type: "post",
            url: "<?=$this->Html->url('/MUsers/remoteSaveEntryForm')?>",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            dataType: "JSON",
            success: function(data){
                var keys = Object.keys(data), num = 0, popup = $("#popup-frame");
                $(".error-message").remove();

                if ( keys.length === 0 ) {
                    var url = "<?= $this->Html->url('/MUsers/index') ?>";
                    location.href = url + "/page:" + page;
                    return false;
                }
                for (var i = 0; i < keys.length; i++) {
                    if ( data[keys[i]].length > 0 ) {
                        var target = $("[name='data[MUser][" + keys[i] + "]']");
                        for (var u = 0; u < data[keys[i]].length; u++) {
                            target.after("<p class='error-message hide'>" + data[keys[i]][u] + "</p>");
                            num ++;
                        }
                    }
                }
                if ( num > 0 ) {
                    var newHeight = popup.height() + (num * 15);
                    popup.animate({
                        height: newHeight + "px"
                    }, {
                        duration: 500,
                        complete: function(){
                            popupEvent.resize();
                            $(".error-message.hide").removeClass("hide");
                            $(this).css("overflow", "");
                        }
                    });
                }
            },
            error: function(data) {
              var url = "<?= $this->Html->url('/MUsers/index') ?>";
              location.href = url + "/page:" + page;
            }
        });
    };
</script>
<?= $this->Form->create('MUser', array('action' => 'add')); ?>
    <div class="form01" style="display:flex; flex-direction: column;">
        <?= $this->Form->input('id', array('type' => 'hidden')); ?>
        <?= $this->Form->input('other', array('type' => 'hidden')); ?>
        <?= $this->Form->input('user_name', array('type' => 'hidden')) ?>
        <?php if($coreSettings[C_COMPANY_USE_ICON_SETTINGS]): ?>
        <div class="profile_icon_register" style="display:flex; flex-direction: column; align-items: center;" >
          <div>
            <?= $this->Form->input('profile_icon', ['type' => 'hidden']); ?>
            <?php if (empty($this->request->data['MUser']['profile_icon'])) { ?>
              <i class="fa-user fal hover-changer" style="color:<?=$iconFontColor ?> ; background-color: <?=$iconMainColor ?>;" ></i>
            <?php } else { ?>
              <img class="hover-changer" src="<?=$this->request->data['MUser']['profile_icon']?>" >
            <?php }?>
          </div>
          <div id="profile_register_btn" style="width: 100px">
            <div class="greenBtn btn-shadow icon_register" style="height: 25px; display: flex; justify-content: center; align-items: center; position: relative; overflow: hidden;"><?php echo $this->Form->file('uploadProfileIcon'); ?>写真を変更する</div>
            <div id="setToDefault" class="greenBtn btn-shadow icon_register" onclick="confirmToDefault()" style="height: 25px; display: flex; justify-content: center; align-items: center;">標準に戻す</div>
            <input type="hidden" name="data[Trimming][info]" ng-model="trimmingInfo" id="TrimmingInfo" class="ng-pristine ng-untouched ng-valid">
          </div>
        </div>
        <?php endif; ?>
        <div class = "grid_item">
          <div class="input_label"><span class="require"><label>表示名</label></span></div>
          <?= $this->Form->input('display_name', array('placeholder' => '表示名', 'div' => false, 'label' => false, 'maxlength' => 10, 'error' => false,'class' => 'inputItems')) ?>
        </div>
        <div class = "grid_item">
          <div class="input_label"><span class="require"><label>メールアドレス</label></span></div>
          <?= $this->Form->input('mail_address', array('placeholder' => 'メールアドレス', 'div' => false, 'label' => false, 'maxlength' => 200, 'error' => false, 'class' => 'inputItems')) ?>
        </div>
        <div class = "grid_item">
<?php
$pwReq = "";
if ( empty($this->params->data['MUser']['id']) ) {
$pwReq = 'class="require"';
}

?>
         <div class="input_label"><span class="require"><label>パスワード</label></span></div>
          <?= $this->Form->input('new_password', array('type' => 'password', 'placeholder' => 'パスワード', 'div' => false, 'label' => false, 'maxlength' => 12, 'autocomplete' => 'off')) ?>
       </div>
       <div class = "grid_item">
        <div class="input_label"><span class="require"><label>権限</label></span></div>
        <?= $this->Form->input('permission_level', array('type' => 'select', 'options' => $authorityList, 'empty' => '-- 権限を選択してください --', 'div' => false, 'label' => false)) ?>
      </div>
      <div class = "grid_item">
        <div class="input_label profile_memo_label_div"><label>メモ</label></div>
        <?= $this->Form->input('memo', array('type' => 'textarea', 'placehodler' => 'メモ', 'div' => false, 'label' => false, 'class' => 'profile_memo_area')) ?>
      </div>
    </div>
<?= $this->Form->end(); ?>
