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
        echo $this->Html->script(C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT."/socket.io/socket.io.js");
        echo $this->Html->script("//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js");
        echo $this->Html->script("//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js");
        echo $this->Html->css("//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css");
        echo $this->Html->script("jquery-ui.min.js");
        echo $this->element("common-js");
        echo $this->Html->css("style.css");
        echo $this->Html->css("modal.css");
        if ( strcmp($this->action, "laFrame") === 0 ) {
          echo $this->Html->css(C_LIVEASSIST_SERVER_SDK_FQDN.'/assistserver/sdk/web/agent/css/assist-console.css');
          echo $this->Html->css(C_LIVEASSIST_SERVER_SDK_FQDN.'/assistserver/sdk/web/shared/css/shared-window.css');
        }
    ?>
    <style type="text/css">
        body { margin: 0; overflow: hidden }
        body, iframe { border: none; }
        #loadingImg {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background-color: rgba(0,0,0,0.6);
        }

        #loadingImg img{
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
            width: 100px;
            height: 100px;
            opacity: 0.6;
        }
    </style>
</head>
<body onContextmenu="return false">
  <?= $this->element('popup') ?>
  <?php echo $this->fetch('content'); ?>
</html>

