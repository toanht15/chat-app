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
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    echo $this->Html->css('style');
    echo $this->Html->script("//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js");
    echo $this->Html->script("http://code.jquery.com/jquery-1.8.3.js");
   	echo $this->Html->script("http://code.jquery.com/ui/1.10.0/jquery-ui.js");
    echo $this->Html->script("common.js");
    echo $this->Html->css('popup');
    echo $this->Html->css('font-awesome.min');
  ?>
</head>
<body>
  <?php if(strcmp($this->name,'Login') !== 0): ?>
    <?= $this->element('navi'); ?>
  <?php endif ;?>
  <div id="content">
    <?= $this->element('popup') ?>
    <?php echo $this->Flash->render(); ?>

    <?php echo $this->fetch('content'); ?>
  </div>
</body>
</html>
