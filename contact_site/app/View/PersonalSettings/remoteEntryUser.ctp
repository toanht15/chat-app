<script type="text/javascript">
  <?php $this->request->data['MUser']['user_name'] = htmlspecialchars($this->request->data['MUser']['user_name'], ENT_QUOTES, 'UTF-8');?>
  <?php $this->request->data['MUser']['display_name'] = htmlspecialchars($this->request->data['MUser']['display_name'], ENT_QUOTES, 'UTF-8');?>
  $(function () {
    var passwordElm = $("[type='password']");
    var editCheck = document.getElementById('MUserEditPassword');
    var pwArea = $('#set_password_area span');
    editCheck.addEventListener('click', function (e) {
      if (e.target.checked) {
        passwordElm.prop('disabled', '');
        pwArea.addClass('require');
      }
      else {
        passwordElm.prop('disabled', 'disabled');
        pwArea.removeClass('require');
      }
    });
  });

  popupEvent.closePopup = function () {
    var id = document.getElementById('MUserId').value;
    var userName = document.getElementById('MUserUserName').value;
    var displayName = document.getElementById('MUserDisplayName').value;
    var settings = document.getElementById('MUserSettings').value;
    var mailAddress = document.getElementById('MUserMailAddress').value;
    var password = document.getElementById('MUserNewPassword').value;
    var edit_password = document.getElementById("MUserEditPassword").checked;
    var current_password = document.getElementById('MUserCurrentPassword').value;
    var new_password = document.getElementById('MUserNewPassword').value;
    var confirm_password = document.getElementById('MUserConfirmPassword').value;
    var accessToken = "<?=$token?>";
    $.ajax({
      type: "post",
      url: "<?=$this->Html->url('/PersonalSettings/remoteSaveEntryForm')?>",
      data: {
        id: id,
        userName: userName,
        displayName: displayName,
        settings: settings,
        mailAddress: mailAddress,
        edit_password: edit_password,
        current_password: current_password,
        new_password: new_password,
        confirm_password: confirm_password,
        accessToken: accessToken
      },
      cache: false,
      dataType: "JSON",
      success: function (data) {
        var keys = Object.keys(data), num = 0, popup = $("#popup-frame");
        $(".error-message").remove();
        console.log(keys.length);
        if (keys.length === 0) {
          location.href = location.href;
          return false;
        }
        for (var i = 0; i < keys.length; i++) {
          if (data[keys[i]].length > 0) {
            var target = $("[name='data[MUser][" + keys[i] + "]']");
            for (var u = 0; u < data[keys[i]].length; u++) {
              target.after("<p class='error-message hide'>" + data[keys[i]][u] + "</p>");
              num++;
            }
          }
        }
        if (num > 0) {
          var newHeight = popup.height() + (num * 15);
          popup.animate({
            height: newHeight + "px"
          }, {
            duration: 500,
            complete: function () {
              $(".error-message.hide").removeClass("hide");
              $(this).css("overflow", "");
            }
          });
        }
      }
    });
  }

</script>
<?php
$editFlg = true;
if (!empty($this->data['MUser']['edit_password'])) {
  $editFlg = false;
}
$settings = [];
if (!empty($this->data['MUser']['settings'])) {
  if (!preg_match('/^(?=.*(<|>|&|\')).*$/', $this->data['MUser']['settings'])) {
    $settings = (array)json_decode($this->data['MUser']['settings']);
  }
}
?>
<!-- 表示されるフォーム画面 -->
<?= $this->Form->create('MUser', array('type' => 'post', 'url' => array('controller' => 'PersonalSettings', 'action' => 'index'), 'name' => 'MUserIndexForm')); ?>
<div class="form01">
  <!-- /* 基本情報 */ -->
  <section>
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <div class="item">
      <div class="labelArea fLeft"><span class="require"><label>氏名</label></span></div>
      <?= $this->Form->input('user_name', array('placeholder' => 'user_name', 'div' => false, 'label' => false, 'maxlength' => 50, 'error' => false, 'class' => 'inputItems')) ?>
    </div>
    <div class="item">
      <div class="labelArea fLeft"><span class="require"><label>表示名</label></span></div>
      <?= $this->Form->input('display_name', array('placeholder' => 'display_name', 'div' => false, 'label' => false, 'maxlength' => 10, 'error' => false, 'class' => 'inputItems')) ?>
    </div>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT] && !empty($mChatSetting['MChatSetting']) && strcmp($mChatSetting['MChatSetting']['sc_flg'], C_SC_ENABLED) === 0) : ?>
    <div class="item">
      <?php else : ?>
      <div style="display:none;">
        <?php endif; ?>
        <div class="labelArea fLeft"><span><label>チャット同時対応数</label></span></div>
        <div id="upperLimit"><?php
          echo (!empty($settings['sc_num'])) ? $settings['sc_num'] : 0 ?></div>
        <?= $this->Form->hidden('settings', array('error' => false)) ?>
        <?php if ($this->Form->isFieldError('settings')) echo $this->Form->error('settings', null, array('wrap' => 'li')); ?>
      </div>
      <div class="item">
        <div class="labelArea fLeft"><span class="require"><label>メールアドレス</label></span></div>
        <?= $this->Form->input('mail_address', array('placeholder' => 'mail_address', 'div' => false, 'label' => false, 'maxlength' => 200, 'error' => false, 'class' => 'inputItems')) ?>
      </div>
  </section>
  <!-- /* パスワード変更 */ -->
  <section>
    <div class="item">
      <!-- /* autocomplete対策 */ -->
      <input type="text" style="display: none">
      <input type="password" style="display: none">
      <!-- /* autocomplete対策 */ -->
      <label class="checkLabelArea">
        <?= $this->Form->input('edit_password', array('type' => 'checkbox', 'class' => 'pointer', 'label' => false, 'div' => false, 'style' => 'margin-left:0px')); ?>
        <span>パスワードを変更する</span>
      </label>
    </div>
    <div id="set_password_area">
      <li>
        <div class="labelAreaPassword fLeft"><span><label>現在のパスワード</label></span></div>
        <?= $this->Form->input('current_password', array('type' => 'password', 'disabled' => $editFlg, 'placeholder' => 'current password', 'div' => false, 'label' => false, 'maxlength' => 24, 'error' => false)) ?>
      </li>
      <?php if ($this->Form->isFieldError('current_password')) echo $this->Form->error('current_password', null, array('wrap' => 'li')); ?>
      <li>
        <div class="labelAreaPassword fLeft"><span><label>新しいパスワード</label></span></div>
        <?= $this->Form->input('new_password', array('type' => 'password', 'disabled' => $editFlg, 'placeholder' => 'new password', 'div' => false, 'label' => false, 'maxlength' => 24, 'error' => false)) ?>
      </li>
      <?php if ($this->Form->isFieldError('new_password')) echo $this->Form->error('new_password', null, array('wrap' => 'li')); ?>
      <li>
        <div class="labelAreaPassword fLeft"><span><label>新しいパスワード（確認用）</label></span></div>
        <?= $this->Form->input('confirm_password', array('type' => 'password', 'disabled' => $editFlg, 'placeholder' => 'confirm password', 'div' => false, 'label' => false, 'maxlength' => 24, 'error' => false)) ?>
      </li>
      <?php if ($this->Form->isFieldError('confirm_password')) echo $this->Form->error('confirm_password', null, array('wrap' => 'li')); ?>
    </div>
  </section>
</div>
<?= $this->Form->end(); ?>
