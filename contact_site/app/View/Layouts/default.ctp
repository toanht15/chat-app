<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$naviElm = "";
$contentStyle = "";
if( strcmp($this->name, 'Login') !== 0 ) {
	$naviElm = $this->element('navi');
	$contentStyle = "position: absolute; top: 60px; left: 60px; right: 0; bottom: 0";
}

?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		// TODO 後程検討
		// キャッシュ無効
		echo $this->Html->meta(array('http-equiv' => "Pragma", 'content' => "no-cache"));
		echo $this->Html->meta(array('http-equiv' => "Cache-Control", 'content' => "no-cache"));
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		echo $this->Html->css("style.css");
		echo $this->Html->css("modal.css");
		echo $this->Html->css("multi-select.css");
		echo $this->Html->script("//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js");
		if (strcmp($this->name, "Customers") === 0) {
			echo $this->Html->script("//socket.localhost:9090/socket.io/socket.io.js");
		}
		echo $this->Html->script("jquery.multi-select.js");
		echo $this->Html->script("//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js");
		echo $this->Html->script("//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-sanitize.js");
		echo $this->element("common-js");
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<?php if( strcmp($this->name, 'Login') !== 0 ): ?>
				<?= $this->element('navi') ?>
			<?php endif ;?>
		</div>
		<div id="content" style="<?=$contentStyle?>">
			<?= $this->element('popup') ?>
			<?php echo $this->Flash->render(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
		</div>
	</div>

<?php if ( Configure::read("debug") != 0 ): ?>
	<section>
		<div id="debug-area-toggle">Debug</div>
		<div id="debug-area">
			<?php echo $this->element('sql_dump'); ?>
		</div>
		<script type="text/javascript">
			$("#debug-area-toggle").on('click', function(){
				$("#debug-area").animate(
					{opacity: "toggle"},
					"slow"
				);
			});
		</script>
	</section>
<?php endif; ?>

</body>
</html>
