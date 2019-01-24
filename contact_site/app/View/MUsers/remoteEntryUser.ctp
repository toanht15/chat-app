<script type="text/javascript">
<?php $this->request->data['MUser']['user_name'] = htmlspecialchars($this->request->data['MUser']['user_name'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['display_name'] = htmlspecialchars($this->request->data['MUser']['display_name'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['mail_address'] = htmlspecialchars($this->request->data['MUser']['mail_address'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['password'] = htmlspecialchars($this->request->data['MUser']['password'], ENT_QUOTES, 'UTF-8');?>
<?php $this->request->data['MUser']['permission_level'] = htmlspecialchars($this->request->data['MUser']['permission_level'], ENT_QUOTES, 'UTF-8');?>


    var confirmToDefault = function(){
      message = "現在設定されているアイコンをデフォルトアイコンに戻します。<br>よろしいですか？<br>";
      modalOpenOverlap.call(window, message, 'p-seticontodefault-alert', '確認してください', 'moment');
    };

    popupEvent.closePopup = function(){
        var page = Number("<?=$page?>");
        var userId = document.getElementById('MUserId').value;
        var userName = document.getElementById('MUserUserName').value;
        var displayName = document.getElementById('MUserDisplayName').value;
        var mailAddress = document.getElementById('MUserMailAddress').value;
        var password = document.getElementById('MUserNewPassword').value;
        var permissionLevel = document.getElementById('MUserPermissionLevel').value;
        var accessToken = "<?=$token?>";
        $.ajax({
            type: "post",
            url: "<?=$this->Html->url('/MUsers/remoteSaveEntryForm')?>",
            data: {
                userId: userId,
                userName: userName,
                displayName: displayName,
                mailAddress: mailAddress,
                password: password,
                permissionLevel: permissionLevel,
                accessToken: accessToken
            },
            cache: false,
            dataType: "JSON",
            success: function(data){
                var keys = Object.keys(data), num = 0, popup = $("#popup-frame");
                popup.removeAttr('style');
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
        <div class="profile_icon_register" style="display:flex; flex-direction: column; align-items: center;" >
          <i class="fa-user fal hover-changer" style="width: 53px; height: 53px; display: flex; justify-content: center; align-items: center;background-color: #ABCD05; border-radius: 50%; color: white; font-size: 35px;" ></i>
          <div id="profile_register_btn" style="width: 100px">
            <div class="greenBtn btn-shadow icon_register" style="height: 25px; display: flex; justify-content: center; align-items: center;">写真を変更する</div>
            <div class="greenBtn btn-shadow icon_register" onclick="confirmToDefault()" style="height: 25px; display: flex; justify-content: center; align-items: center;">標準に戻す</div>
            <input type="hidden" name="data[Trimming][info]" ng-model="trimmingInfo" id="TrimmingInfo" class="ng-pristine ng-untouched ng-valid">
          </div>
        </div>
        <div class = "grid_item">
          <div class="input_label"><span class="require"><label>表示名</label></span></div>
          <?= $this->Form->input('display_name', array('placeholder' => 'display_name', 'div' => false, 'label' => false, 'maxlength' => 10, 'error' => false,'class' => 'inputItems')) ?>
        </div>
        <div class = "grid_item">
          <div class="input_label"><span class="require"><label>メールアドレス</label></span></div>
          <?= $this->Form->input('mail_address', array('placeholder' => 'mail_address', 'div' => false, 'label' => false, 'maxlength' => 200, 'error' => false, 'class' => 'inputItems')) ?>
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
    </div>
<?= $this->Form->end(); ?>
