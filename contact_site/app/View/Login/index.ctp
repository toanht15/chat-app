<div id="login_idx_bg"></div>
<div id="login_idx">
	<div id="content-area">
		<?= $this->element('Login/script') ?>
		<?= $this->Html->image('icon.png', array('alt' => 'アイコン', 'width' => 100, 'height' => 100, 'style'=>'margin: 0 auto; display: block; opacity: 0.7'))?>
		<div class="form_area">
			<?= $this->element('Login/entry') ?>
		</div>
	</div>
	<?= $this->Html->link('パスワードを忘れた方はこちら', 'javascript:void(0)', array('style'=>'display: block; height: 30px; padding: 5px; font-size: 13px; color: #E7EFF5;')) ?>
</div>