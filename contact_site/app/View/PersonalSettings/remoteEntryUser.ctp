<script type="text/javascript">
  <?php $this->request->data['MUser']['user_name'] = htmlspecialchars($this->request->data['MUser']['user_name'],
    ENT_QUOTES, 'UTF-8');?>
  <?php $this->request->data['MUser']['display_name'] = htmlspecialchars($this->request->data['MUser']['display_name'],
    ENT_QUOTES, 'UTF-8');?>
  <?php $this->request->data['MUser']['profile_icon'] = htmlspecialchars(json_decode($this->request->data['MUser']['settings'],
    true)['profileIcon'], ENT_QUOTES, 'UTF-8');?>
  <?php
  if (empty($this->request->data['MUser']['memo'])) {
    if (!empty($this->request->data['MUser']['user_name'])) {
      $this->request->data['MUser']['memo'] = $this->request->data['MUser']['user_name'];
    }
  } else {
    $this->request->data['MUser']['memo'] = htmlspecialchars($this->request->data['MUser']['memo'], ENT_QUOTES,
      'UTF-8');
  }
  ?>
  var checkIconIsDefault = function() {
    var icon = $('.profile_icon_selector')[0];
    return icon.tagName === 'I';
  };

  var disableSetDefaultBtn = function() {
    var defaultBtn = document.getElementById('setToDefault');
    defaultBtn.classList.remove('greenBtn');
    defaultBtn.classList.add('disOffgrayBtn');
  };
  var ableSetDefaultBtn = function() {
    var defaultBtn = document.getElementById('setToDefault');
    defaultBtn.classList.remove('disOffgrayBtn');
    defaultBtn.classList.add('greenBtn');
  };

  var initHoverClickEvent = function() {
    $('.hover-changer').off("click");
    $('.hover-changer').click(function(){
      $('#MUserUploadProfileIcon').click();
    });
  };

  $(function() {
    var passwordElm = $('[type=\'password\']');
    var editCheck = document.getElementById('MUserEditPassword');
    var pwArea = $('#set_password_area span');
    editCheck.addEventListener('click', function(e) {
      if (e.target.checked) {
        passwordElm.prop('disabled', '');
        pwArea.addClass('require');
      } else {
        passwordElm.prop('disabled', 'disabled');
        pwArea.removeClass('require');
      }
    });

    //何かしらアイコンをどうにかする必要がある
    <?php if($coreSettings[C_COMPANY_USE_ICON_SETTINGS]): ?>
    if (checkIconIsDefault()) {
      disableSetDefaultBtn();
    }
    initHoverClickEvent();
    <?php endif; ?>
  });

  var confirmToDefault = function() {
    if (checkIconIsDefault()) {
      //デフォルトアイコンの場合はなにもさせない。
      return;
    }
    message = '現在設定されているアイコンをデフォルトアイコンに戻します。<br>よろしいですか？<br>';
    modalOpenOverlap.call(window, message, 'p-seticontodefault-alert', '確認してください', 'moment');
    initPopupOverlapEvent();

  };

  var initPopupOverlapEvent = function() {
    popupEventOverlap.closePopup = function() {
      var icon = $('.profile_icon_selector')[0];
      if (icon.tagName === 'IMG') {
        changeIconToDefault(icon);
      }
      popupEventOverlap.closeNoPopupOverlap();
    };
  };

  var changeIconToDefault = function(icon) {
    if (icon.parentNode) {
      icon.parentNode.removeChild(icon);
    }
    $('#MUserUploadProfileIcon').val('');
    $('#TrimmingProfileIconInfo').val('');
    $('#MUserProfileIcon').val('');
    var iconComponent = $('.profile_icon_register > div')[0];
    var defaultElm = document.createElement("i");
    // IE対策でわざと分離
    defaultElm.classList.add("fa-user");
    defaultElm.classList.add("fal");
    defaultElm.classList.add("profile_icon_selector");
    defaultElm.style.color = "<?=$iconFontColor ?>";
    defaultElm.style.backgroundColor = "<?=$iconMainColor ?>";
    iconComponent.appendChild(defaultElm);
    disableSetDefaultBtn();
    initHoverClickEvent();
  };

  var changeProfileIcon = function(e) {
    var files = e.target.files;
    if (window.URL && files.length > 0) {
      var file = files[files.length - 1];

      // jpeg/jpg/png
      var reg = new RegExp(/image\/(png|jpeg|jpg)/i);
      if (!reg.exec(file.type)) {
        $('#MUserUploadProfileIcon').val('');
        return false;
      }
      var url = window.URL.createObjectURL(file);
      target = changeIconPath(url, file.name);
      openTrimmingDialog(function() {
        beforeTrimmingInit(url, $('.profile_icon_selector'));
        trimmingInit(null, $('#TrimmingProfileIconInfo'), 1, 'profile_icon');
      });
    }
  };

  $('#MUserUploadProfileIcon').change(function(e) {
    changeProfileIcon(e);
    ableSetDefaultBtn();
  });

  var changeIconPath = function(path, fileName) {
    var currentIcon = document.querySelector('.hover-changer').children[1];
    var newIcon = document.createElement('img');
    var parentElm = currentIcon.parentNode;
    if (currentIcon.parentNode) {
      currentIcon.parentNode.removeChild(currentIcon);
    }
    newIcon.src = path;
    newIcon.classList.add('profile_icon_selector');
    parentElm.appendChild(newIcon);
    var iconData = document.getElementById('MUserProfileIcon');
    iconData.value = fileName;
    return newIcon;
  };

  function openTrimmingDialog(callback) {
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'MWidgetSettings', 'action' => 'remoteTimmingInfo']) ?>",
      success: function(html) {
        modalOpenOverlap.call(window, html, 'p-profile-icon-trimming', 'トリミング', 'moment');
        callback();
      },
    });
  }

  popupEvent.closePopup = function() {
    var form = $('#MUserRemoteOpenEntryFormForm').get(0);
    var formData = new FormData(form);
    var accessToken = "<?=$token?>";
    formData.append('accessToken', accessToken);
    $.ajax({
      type: 'post',
      url: "<?=$this->Html->url('/PersonalSettings/remoteSaveEntryForm')?>",
      dataType: 'JSON',
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      success: function(data) {
        var keys = Object.keys(data), num = 0, popup = $('#popup-frame');
        $('.error-message').remove();
        console.log(keys.length);
        if (keys.length === 0) {
          location.href = location.href;
          return false;
        }
        for (var i = 0; i < keys.length; i++) {
          if (data[keys[i]].length > 0) {
            var target = $('[name=\'data[MUser][' + keys[i] + ']\']');
            for (var u = 0; u < data[keys[i]].length; u++) {
              target.after('<p class=\'error-message hide\'>' + data[keys[i]][u] + '</p>');
              num++;
            }
          }
        }
        if (num > 0) {
          var newHeight = popup.height() + (num * 15);
          popup.animate({
            height: newHeight + 'px',
          }, {
            duration: 500,
            complete: function() {
              $('.error-message.hide').removeClass('hide');
              $(this).css('overflow', '');
              popupEvent.resize();
            },
          });
        }
      },
    });
  };

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
<?= $this->Form->create('MUser', array(
  'type' => 'post',
  'url' => array('controller' => 'PersonalSettings', 'action' => 'index'),
  'name' => 'MUserIndexForm'
)); ?>
<div class="form01">
  <!-- /* 基本情報 */ -->
  <section>
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <?= $this->Form->input('user_name', array('type' => 'hidden')); ?>
    <?php if ($coreSettings[C_COMPANY_USE_ICON_SETTINGS]): ?>
      <div class="profile_icon_register">
        <div class="hover-changer">
          <?= $this->Form->input('profile_icon', ['type' => 'hidden']); ?>
          <?php if (empty($this->request->data['MUser']['profile_icon'])) { ?>
            <i class="fa-user fal profile_icon_selector"
               style="color:<?= $iconFontColor ?> ; background-color: <?= $iconMainColor ?>;"></i>
          <?php } else { ?>
            <img class="profile_icon_selector" src="<?= $this->request->data['MUser']['profile_icon'] ?>">
          <?php } ?>
        </div>
        <div id="profile_register_btn">
          <div class="greenBtn btn-shadow icon_register"><?php echo $this->Form->file('uploadProfileIcon'); ?>写真を変更する
          </div>
          <div id="setToDefault" class="greenBtn btn-shadow icon_register" onclick="confirmToDefault()">標準に戻す</div>
          <input type="hidden" name="data[Trimming][profileIconInfo]" ng-model="trimmingProfileIconInfo"
                 id="TrimmingProfileIconInfo" class="ng-pristine ng-untouched ng-valid">
        </div>
      </div>
    <?php endif; ?>
    <div class="item">
      <div class="labelArea fLeft"><span class="require"><label>表示名</label></span></div>
      <?= $this->Form->input('display_name', array(
        'placeholder' => '表示名',
        'div' => false,
        'label' => false,
        'maxlength' => 10,
        'error' => false,
        'class' => 'inputItems'
      )) ?>
    </div>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT] && !empty($mChatSetting['MChatSetting']) && strcmp($mChatSetting['MChatSetting']['sc_flg'],
      C_SC_ENABLED) === 0) : ?>
    <div class="item">
      <?php else : ?>
      <div style="display:none;">
        <?php endif; ?>
        <div class="labelArea fLeft"><span><label>チャット同時対応数</label></span></div>
        <div id="upperLimit"><?php
          echo (!empty($settings['sc_num'])) ? $settings['sc_num'] : 0 ?></div>
        <?= $this->Form->hidden('settings', array('error' => false)) ?>
        <?php if ($this->Form->isFieldError('settings')) {
          echo $this->Form->error('settings', null, array('wrap' => 'li'));
        } ?>
      </div>
      <div class="item">
        <div class="labelArea fLeft"><span class="require"><label>メールアドレス</label></span></div>
        <?= $this->Form->input('mail_address', array(
          'placeholder' => 'メールアドレス',
          'div' => false,
          'label' => false,
          'maxlength' => 200,
          'error' => false,
          'class' => 'inputItems'
        )) ?>
      </div>
      <div class="item">
        <div class="labelArea fLeft align-start"><label>メモ</label></div>
        <?= $this->Form->input('memo', array(
          'type' => 'textarea',
          'placeholder' => 'メモ',
          'div' => false,
          'label' => false,
          'maxlength' => 200,
          'error' => false,
          'class' => 'profile_memo_area'
        )) ?>
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
        <?= $this->Form->input('edit_password', array(
          'type' => 'checkbox',
          'class' => 'pointer',
          'label' => false,
          'div' => false,
          'style' => 'margin-left:0px'
        )); ?>
        <span>パスワードを変更する</span>
      </label>
    </div>
    <div id="set_password_area">
      <li>
        <div class="labelAreaPassword fLeft"><span><label>現在のパスワード</label></span></div>
        <?= $this->Form->input('current_password', array(
          'type' => 'password',
          'disabled' => $editFlg,
          'placeholder' => 'current password',
          'div' => false,
          'label' => false,
          'maxlength' => 12,
          'error' => false
        )) ?>
      </li>
      <?php if ($this->Form->isFieldError('current_password')) {
        echo $this->Form->error('current_password', null, array('wrap' => 'li'));
      } ?>
      <li>
        <div class="labelAreaPassword fLeft"><span><label>新しいパスワード</label></span></div>
        <?= $this->Form->input('new_password', array(
          'type' => 'password',
          'disabled' => $editFlg,
          'placeholder' => 'new password',
          'div' => false,
          'label' => false,
          'maxlength' => 12,
          'error' => false
        )) ?>
      </li>
      <?php if ($this->Form->isFieldError('new_password')) {
        echo $this->Form->error('new_password', null, array('wrap' => 'li'));
      } ?>
      <li>
        <div class="labelAreaPassword fLeft"><span><label>新しいパスワード（確認用）</label></span></div>
        <?= $this->Form->input('confirm_password', array(
          'type' => 'password',
          'disabled' => $editFlg,
          'placeholder' => 'confirm password',
          'div' => false,
          'label' => false,
          'maxlength' => 12,
          'error' => false
        )) ?>
      </li>
      <?php if ($this->Form->isFieldError('confirm_password')) {
        echo $this->Form->error('confirm_password', null, array('wrap' => 'li'));
      } ?>
    </div>
  </section>
</div>
<?= $this->Form->end(); ?>
