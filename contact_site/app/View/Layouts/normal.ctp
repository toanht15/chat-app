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
		echo $this->Html->script("//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js");
		echo $this->Html->css("style.css");
	?>
	<style type="text/css">
		body { margin: 0; background-color: #D0D0D0;}
		body, iframe { border: none; }
	</style>
</head>
<body>
  <?php echo $this->fetch('content'); ?>
</body>
</html>

