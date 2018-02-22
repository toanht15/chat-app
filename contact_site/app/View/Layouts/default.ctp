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
if( strcmp($this->name, 'Login') !== 0 && strcmp($this->action, 'baseForAnotherWindow') !== 0
  && strcmp($this->action, 'loadingHtml') !== 0 && strcmp($this->name, 'Trial') !== 0) {
  $naviElm = $this->element('navi');
  $contentStyle = "position: absolute; top: 60px; left: 60px; right: 0; bottom: 0";
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
  <title>
    <?php echo $this->fetch('title'); ?> | sinclo
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
    echo $this->Html->css("bootstrap.css");
    echo $this->Html->css("multi-select.css");
    echo $this->Html->css("standalone.css");
    echo $this->Html->css('font-awesome.min');
  ?>
  <link rel="prefetch" href="/fonts/fontawesome-webfont.eot?v=4.0.3" as="font">
  <link rel="prefetch" href="/fonts/fontawesome-webfont.eot?#iefix&v=4.0.3" as="font">
  <link rel="prefetch" href="/fonts/fontawesome-webfont.woff?v=4.0.3" as="font">
  <link rel="prefetch" href="/fonts/fontawesome-webfont.ttf?v=4.0.3" as="font">
  <link rel="prefetch" href="/fonts/fontawesome-webfont.svg?v=4.0.3#fontawesomeregular" as="font">
  <?php
    if ( strcmp($this->name, 'TAutoMessages') === 0 || strcmp($this->name, 'MOperatingHours') === 0) {
      echo $this->Html->css("clockpicker.css");
    }
    echo $this->Html->css("style.css");
    echo $this->Html->css("modal.css");
    if ( strcmp($this->name, 'Histories') === 0 || strcmp($this->name, 'ChatHistories') === 0) {
      echo $this->Html->css("daterangepicker.css");
    }
    if ( strcmp($this->name, 'Statistics') === 0 || strcmp($this->name, 'ChatHistories') === 0 ) {
      echo $this->Html->css("jquery.dataTables.css");
    }
    if ( strcmp($this->name, 'Statistics') === 0) {
      echo $this->Html->css("//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css");
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
    if ( strcmp($this->name, 'Histories') === 0 || strcmp($this->name, 'ChatHistories') === 0) {
      echo $this->Html->script("daterangepicker.js");
    }
    if ( strcmp($this->name, 'TDocuments') === 0 ) {
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
    if ( strcmp($this->name, 'Trial') === 0 ) {
      echo $this->Html->css('freeTrial.css'); ?>
      <!-- Google Tag Manager -->
      <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-MS5ZND');</script>
      <!-- End Google Tag Manager -->
   <?php } ?>

<script type="text/javascript">
  <?php echo $this->element('loadScreen'); ?>
</script>

</head>
<body>
  <?php if ( strcmp($this->name, 'Trial') === 0 ) { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MS5ZND"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
  <?php } ?>
  <div id="container">
    <div id="header">
      <?php if( strcmp($this->name, 'Login') !== 0 && strcmp($this->action, 'baseForAnotherWindow') !== 0
      && strcmp($this->action, 'loadingHtml') !== 0 && strcmp($this->name, 'Trial') !== 0) : ?>
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
