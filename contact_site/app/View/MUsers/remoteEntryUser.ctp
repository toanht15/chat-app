<script type="text/javascript">
	closePopup = function(){
		var userId = document.getElementById('MUserId').value;
		var userName = document.getElementById('MUserUserName').value;
		var displayName = document.getElementById('MUserDisplayName').value;
		var mailAddress = document.getElementById('MUserMailAddress').value;
		var password = document.getElementById('MUserNewPassword').value;
		var permissionLevel = document.getElementById('MUserPermissionLevel').value;
		$.ajax({
			type: "post",
			url: "<?=$this->Html->url('/MUsers/remoteSaveEntryForm')?>",
			data: {
				userId: userId,
				userName: userName,
				displayName: displayName,
				mailAddress: mailAddress,
				password: password,
				permissionLevel: permissionLevel
			},
			dataType: "JSON",
			success: function(data){
				var keys = Object.keys(data), num = 0, popup = $("#popup");
				popup.removeAttr('style');
				$(".error-message").remove();

				if ( keys.length === 0 ) {
					location.href = "<?=$this->Html->url(array('controller' => 'MUsers', 'action' => 'index'))?>";
					return false;
				}
				for (var i = 0; i < keys.length; i++) {
					if ( data[keys[i]].length > 0 ) {
						var target = $("input[name='data[MUser][" + keys[i] + "]']");
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
			}
		});
	};
</script>
<?= $this->Form->create('MUser', array('action' => 'add')); ?>
	<div class="form01">
		<?= $this->Form->input('id', array('type' => 'hidden')); ?>
		<div>
			<?= $this->Html->image('monitor_g.png', array('alt' => '氏名', 'width' => 30, 'height' => 30)) ?>
			<?= $this->Form->input('user_name', array('placeholder' => 'user_name', 'div' => false, 'label' => false, 'maxlength' => 50)) ?>
		</div>
		<div>
			<?= $this->Html->image('headphone.png', array('alt' => '表示名', 'width' => 30, 'height' => 30)) ?>
			<?= $this->Form->input('display_name', array('placeholder' => 'display_name', 'div' => false, 'label' => false, 'maxlength' => 10)) ?>
		</div>
		<div>
			<?= $this->Html->image('mail_g.png', array('alt' => 'メールアドレス', 'width' => 30, 'height' => 30)) ?>
			<?= $this->Form->input('mail_address', array('placeholder' => 'mail_address', 'div' => false, 'label' => false, 'maxlength' => 200)) ?>
		</div>
		<div>
			<input type="text" style="display: none">
			<input type="password" style="display: none">
			<?= $this->Html->image('password.png', array('alt' => 'パスワード', 'width' => 30, 'height' => 30)) ?>
			<?= $this->Form->input('new_password', array('type' => 'password', 'placeholder' => 'password', 'div' => false, 'label' => false, 'maxlength' => 12)) ?>
		</div>
		<div>
			<?= $this->Html->image('permission.png', array('alt' => '権限', 'width' => 30, 'height' => 30)) ?>
			<?= $this->Form->input('permission_level', array('type' => 'select', 'options' => $authorityList, 'empty' => '-- Choose Permission --', 'div' => false, 'label' => false)) ?>
		</div>
	</div>
<?= $this->Form->end(); ?>