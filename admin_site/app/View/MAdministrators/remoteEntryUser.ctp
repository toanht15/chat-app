<script type="text/javascript">
  popupEvent.closePopup = function(){
    var userId = document.getElementById('MAdministratorId').value;
    var userName = document.getElementById('MAdministratorUserName').value;
    var mailAddress = document.getElementById('MAdministratorMailAddress').value;
    var password = document.getElementById('MAdministratorNewPassword').value;
    $.ajax({
      type: "post",
      url: "<?=$this->Html->url('/MAdministrators/remoteSaveEntryForm')?>",
      data: {
        userId: userId,
        userName: userName,
        mailAddress: mailAddress,
        password: password,
      },
      cache: false,
      dataType: "JSON",
      success: function(data){
      	console.log(('haitteru'));
      	var keys = Object.keys(data), num = 0;
        $(".error-message").remove();
          if (keys.length === 0 ) {
          	console.log('hei!');
            location.href = "<?=$this->Html->url(array('controller' => 'MAdministrators', 'action' => 'index'))?>";
            return false;
          }
          for (var i = 0; i < keys.length; i++) {
            if ( data[keys[i]].length > 0 ) {
            	console.log('ooo');
              var target = $("[name='data[MAdministrator][" + keys[i] + "]']");
              for (var u = 0; u < data[keys[i]].length; u++) {
                target.after("<p class='error-message hide'>" + data[keys[i]][u] + "</p>");
                num ++;
              }
            }
          }
          if ( num > 0 ) {
          	console.log('aaaa');
            var newHeight = $("#popup-content").height() + (num * 15);
            $("#popup-frame").animate({
            height: newHeight + "px"
          },
          {
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

  popupEvent.closeDeletePopup = function(){
    var id = document.getElementById('MAdministratorId').value;
    $.ajax({
      type: 'post',
      cache: false,
      data: {
        id: id
      },
      url: "<?= $this->Html->url('/MAdministrators/remoteDeleteUser') ?>",
      success: function(){
        location.href = "<?= $this->Html->url('/MAdministrators/index') ?>";
      }
    });
  };
</script>
<?=$this->Form->create('MAdministrator', array('action' => 'add')); ?>
  <div class='form01'>
    <?= $this->Form->input('id', array('type' => 'hidden')); ?>
    <ul class="formArea">
      <li>
        <span>
           <label class='require'>名前</label>
          <?= $this->Form->input('user_name', ['div' => false,'label' => false, 'maxlength' => 50]) ?>
        </span>
      </li>
      <li>
        <span>
          <label class='require'>メールアドレス</label>
          <?= $this->Form->input('mail_address', ['div' => false, 'label' => false, 'maxlength' => 200,'autocomplete' => 'email']) ?>
        </span>
      </li>
      <li>
        <input type="text" style="display:block; position: fixed; top: -500px; left: -500px; z-index: 0;">
        <input type="password" style="display:block; position: fixed; top: -500px; left: -500px; z-index: 0;">
        <span>
          <label class='require'>パスワード</label>
          <?= $this->Form->input('new_password', ['type' => 'password','div' => false, 'autocomplete' => 'off','label' => false, 'maxlength' => 12]) ?>
        </span>
      </li>
    </ul>
  </div>
<?= $this->Form->end(); ?>
