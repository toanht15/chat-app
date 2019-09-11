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
if( (strcmp($this->name, 'Login') !== 0 && strcmp($this->action, 'baseForAnotherWindow') !== 0
  && strcmp($this->action, 'loadingHtml') !== 0 && strcmp($this->name, 'ScriptSettings') !== 0) ||
  (strcmp($this->name, 'ScriptSettings') === 0 && strcmp($this->action, 'index') === 0)) {
  $naviElm = $this->element('navi');
  $contentStyle = "position: absolute; top: 60px; left: 80px; right: 0; bottom: 0";
}
if(strcmp($this->action, 'baseForAnotherWindow') == 0) {
  $contentStyle = "position: absolute; top: 30px; left: 0px; right: 0; bottom: 0";?>
  <div id="anotherWindow_color-bar" class="card-shadow">
    <ul id="anotherWindow_color-bar-right" class="tCenter">
    <?php if($date == 'eachOperatorDaily') { ?>
      <li class="tCenter"><p>時間別サマリ</p></li>
    <?php }
    if($date == 'eachOperatorMonthly') { ?>
      <li class="tCenter"><p>日別サマリ</p></li>
    <?php }
    if($date == 'eachOperatorYearly') { ?>
      <li class="tCenter"><p>月別サマリ</p></li>
    <?php } ?>
    </ul>
</div>
<?php }

?>

<!DOCTYPE html>
<html>
<head>
  <?php echo $this->Html->charset(); ?>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if(strcmp($this->name, 'ScriptSettings') == 0 && strcmp($this->action, 'index') !== 0) { ?>
      <title><?php echo $this->fetch('title'); ?></title>
    <?php }
    else { ?>
      <title><?php echo $this->fetch('title'); ?><?php if(!defined('APP_MODE_OEM') || !APP_MODE_OEM): ?> | sinclo<?php endif; ?></title>
    <?php } ?>
  <?php
    echo $this->Html->meta('icon');
    // TODO 後程検討
    // キャッシュ無効
    echo $this->Html->meta(array('http-equiv' => "Pragma", 'content' => "no-cache"));
    echo $this->Html->meta(array('http-equiv' => "Cache-Control", 'content' => "no-cache"));
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    echo $this->Html->css("bootstrap.css");
    echo $this->Html->css("multi-select.css");
    echo $this->Html->css("standalone.css");
    echo $this->Html->css("light.min");
    echo $this->Html->css("solid.min");
    echo $this->Html->css('fontawesome.min');
    echo $this->Html->css('fontawesome-all.min');
    echo $this->Html->css("//cdnjs.cloudflare.com/ajax/libs/cropper/1.0.0/cropper.min.css");
  ?>
  <?php
    if ( strcmp($this->name, 'TAutoMessages') === 0 || strcmp($this->name, 'MOperatingHours') === 0) {
      echo $this->Html->css("clockpicker.css");
    }
    if ( strcmp($this->name, 'ScriptSettings') !== 0 || strcmp($this->action, 'index') === 0) {
      echo $this->Html->css("style.css");
    }
    echo $this->Html->css("modal.css");
    if ( strcmp($this->name, 'Histories') === 0 || strcmp($this->name, 'ChatHistories') === 0 || strcmp($this->name, 'TLeadLists') === 0) {
      echo $this->Html->css("daterangepicker.css");
    }
    if ( strcmp($this->name, 'Statistics') === 0 || strcmp($this->name, 'ChatHistories') === 0 ) {
      echo $this->Html->css("jquery.dataTables.css");
    }
    if(strcmp($this->name, 'MWidgetSettings') === 0 || strcmp($this->name, 'MChatNotifications') === 0) {
      echo $this->Html->css("//cdnjs.cloudflare.com/ajax/libs/cropper/1.0.0/cropper.min.css");
    }
    if ( strcmp($this->name, 'Statistics') === 0) {
      echo $this->Html->css("//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css");
    }
    if ( strcmp($this->name, 'ScriptSettings') === 0 && strcmp($this->action, 'index') !== 0) {
      echo $this->Html->css("demo.css");
    }
    echo $this->Html->script("//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js");
    if (strcmp($this->name, "Customers") === 0) {
      echo $this->Html->script(C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT."/socket.io/socket.io.js");
      echo $this->Html->css('jquery-ui.css');
      echo $this->Html->script("jquery-ui.js");
    }
    echo $this->Html->script("jquery.multi-select.js");
    if ( strcmp($this->name, 'TAutoMessages') === 0 || strcmp($this->name, 'MOperatingHours') === 0) {
      echo $this->Html->css('jquery-ui.css');
      echo $this->Html->script("jquery-ui.js");
      echo $this->Html->script("clockpicker.js");
    }
    echo $this->Html->script("common.js");
    echo $this->Html->script("//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js");
    echo $this->Html->script("//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-sanitize.js");
    echo $this->Html->script("angular.validate.js");
    echo $this->Html->script("cidr2regex.js");
    echo $this->element("common-js");
    echo $this->Html->script("moment.min.js");
    if(strcmp($this->name, 'Customers') === 0) {
      echo $this->Html->script("jquery.jrumble.1.3.js");
    }
    if(strcmp($this->name, 'MWidgetSettings') === 0 || strcmp($this->name, 'MChatNotifications') === 0) {
      //echo $this->Html->script("cropper.min.js");
      //cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js
      echo $this->Html->script("//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js");
      echo $this->Html->script("//cdnjs.cloudflare.com/ajax/libs/cropper/1.0.0/cropper.min.js");
    } else {
      echo $this->Html->script("//cdnjs.cloudflare.com/ajax/libs/cropper/1.0.0/cropper.min.js");
    }
    if ( strcmp($this->name, 'Histories') === 0 || strcmp($this->name, 'ChatHistories') === 0 || strcmp($this->name, 'TLeadLists') === 0) {
      echo $this->Html->script("daterangepicker.js");
    }
    if ( strcmp($this->name, 'TLeadLists') === 0 ) {
      echo $this->Html->script('jquery.binarytransport.js');
    }
    if ( strcmp($this->name, 'TDocuments') === 0 || strcmp($this->name, 'TChatbotScenario') === 0) {
      echo $this->Html->script("//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js");
      echo $this->Html->css("//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css");
    }
    if ( strcmp($this->name, 'Statistics') === 0 || strcmp($this->name, 'ChatHistories') === 0 ) {
      echo $this->Html->script('jquery.dataTables.min.js');
      echo $this->Html->script("dataTables.fixedColumns.min.js");
    }
    if ( strcmp($this->name, 'TDictionaries') === 0 ) {
      echo $this->Html->css('jquery-ui.css');
      echo $this->Html->script("jquery-ui.js");
    }
    if ( strcmp($this->name, 'TCampaigns') === 0 ) {
      echo $this->Html->css('jquery-ui.css');
      echo $this->Html->script("jquery-ui.js");
    }
    if ( strcmp($this->name, 'ChatHistories') === 0 ) {
      echo $this->Html->css('jquery.splitter.css');
      echo $this->Html->script("jquery.splitter.js");
    }
    if ( strcmp($this->name, 'ScriptSettings') === 0 && strcmp($this->action, 'index') !== 0) {
      echo $this->Html->script("openclose.js");
    }
  if (strcmp($this->name, 'TChatbotScenario') === 0) {
    echo $this->Html->css("jquery.atwho.css");
    echo $this->Html->script("jquery.caret.js");
    echo $this->Html->script("jquery.atwho.js");
    echo $this->Html->script("canvas-toBlob.js"); // support toBlob() in IE
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js");
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/angular-slick-carousel/3.1.7/angular-slick.js");
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.min.js");
    echo $this->Html->css("https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.min.css");
    echo $this->Html->script("flatpickr.js");
  }
  if (strcmp($this->name,'TChatbotDiagrams') === 0) {
    echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/jointjs/2.1.0/joint.css"');
    echo $this->Html->css('jquery-ui.css');
    echo $this->Html->css('joint.ui.halo.css');
    echo $this->Html->css('joint.ui.navigator.css');
    echo $this->Html->css('joint.ui.paperScroller.css');
    echo $this->Html->script("https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js");
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/lodash.js/3.10.1/lodash.js");
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.3.3/backbone.js");
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/jointjs/2.1.0/joint.js");
    echo $this->Html->script("joint.ui.halo.js");
    echo $this->Html->script('joint.ui.navigator.js');
    echo $this->Html->script('joint.ui.paperScroller.js');
    // need scenario simulator
    echo $this->Html->css("jquery.atwho.css");
    echo $this->Html->script("jquery.caret.js");
    echo $this->Html->script("jquery.atwho.js");
    echo $this->Html->script("canvas-toBlob.js"); // support toBlob() in IE
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js");
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/angular-slick-carousel/3.1.7/angular-slick.js");
    echo $this->Html->script("https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.min.js");
    echo $this->Html->css("https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.min.css");
    echo $this->Html->script("flatpickr.js");
  }
 ?>

<script type="text/javascript">
  <?php echo $this->element('loadScreen'); ?>
</script>

</head>
<body>
  <div id="container">
    <div id="header">
      <?php if( (strcmp($this->name, 'Login') !== 0 && strcmp($this->action, 'baseForAnotherWindow') !== 0
      && strcmp($this->action, 'loadingHtml') !== 0 && strcmp($this->name, 'ScriptSettings') !== 0) ||
      (strcmp($this->name, 'ScriptSettings') === 0 && strcmp($this->action, 'index') === 0)) : ?>
        <?= $this->element('navi') ?>
      <?php endif ;?>
    </div>
    <div id="content" style="<?=$contentStyle?>">
      <?= $this->element('popupOverlap') ?>
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
