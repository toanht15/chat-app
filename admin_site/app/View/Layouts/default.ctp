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
    echo $this->Html->css('popup');
    echo $this->Html->css('font-awesome.min');
  ?>
</head>
<body>
  <div id="sidebar">
    <div id="logo" ><?=$this->Html->image('sinclo_square_logo.png', ['width'=>54, 'height'=>48])?></div>
    <nav>
      <ul>
        <li class="nav-group"><i class="fa fa-home fa-lg" aria-hidden="true"></i> トップ</li>
        <li class="nav-group on"><i class="fa fa-building fa-lg" aria-hidden="true"></i> 契約管理</li>
        <li>契約一覧</li>
        <li class="nav-group"><i class="fa fa-cog fa-lg" aria-hidden="true"></i> 設定</li>
        <li>アカウント設定</li>
        <li>テンプレート設定</li>
        <li class="nav-group"><i class="fa fa-user fa-lg" aria-hidden="true"></i> 個人設定</li>
        <li>個人設定</li>
      </ul>
    </nav>
    <a href="javascript:void(0)">ログアウト</a>
  </div>
  <div id="content">
    <?= $this->element('popup') ?>
    <?php echo $this->Flash->render(); ?>

    <?php echo $this->fetch('content'); ?>
  </div>
</body>
</html>
