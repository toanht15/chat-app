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
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$this->layout = "normal";
?>
<div id="login_idx_bg"></div>
<div id="login_idx">
  <div id="content-area">
    <?= $this->Html->image('sinclo_logo.png', array('alt' => 'アイコン', 'width' => 231, 'height' => 59, 'style'=>'margin: 30px auto 0 auto; display: block'))?>
    <div class="form_area" style="vertical-align: middle;">
      <h1 style="font-size: 5em;"><?php echo $this->response->statusCode(); ?></h1>
      <h2><?php echo $message; ?></h2>
    </div>
  </div>
  <?php $this->Html->link('パスワードを忘れた方はこちら', 'javascript:void(0)', array('style'=>'display: block; height: 30px; padding: 5px; font-size: 13px; color: #E7EFF5;')) ?>
</div>
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
?>
