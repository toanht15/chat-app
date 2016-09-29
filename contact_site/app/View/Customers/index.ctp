<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('Customers/userAgentCheck') ?>
<?php echo $this->element('Customers/script') ?>
<?php echo $this->element('Customers/angularjs') ?>

<section id='customer_idx' class="{{customerMainClass}}" ng-app="sincloApp" ng-controller="MainCtrl">

    <div id='customer_main' class="card-shadow" ng-cloak>
        <?php echo $this->element('Customers/monitor') ?>
    </div>

<?php
$cName = "full";
if ( !$coreSettings[C_COMPANY_USE_SYNCLO] && $coreSettings[C_COMPANY_USE_CHAT] ) {
  $cName = "chatOnly";
}
else if ( $coreSettings[C_COMPANY_USE_SYNCLO] && !$coreSettings[C_COMPANY_USE_CHAT] ) {
  $cName = "syncOnly";
}
?>

    <div id='customer_sub_pop' data-contract='<?=$cName?>' ng-show="detailId" ng-cloak><?php echo $this->element('Customers/detail') ?></div>
    <script type="text/javascript">
    (function(){
      angular.element(document).ready(function () {
        // 500ミリ秒後、描画が正常に行われていなかった場合
        setTimeout(function(){
          if ( angular.element('#customer_tab[ng-cloak]').length > 0 ) {
            // 描画し直す
            angular.bootstrap(document, ['sincloApp']);
          }
        }, 500);
      });
    }());
    </script>

    <div id='customer_tab' ng-cloak>
      <ul>
        <li ng-repeat="cInfo in chatList" ng-click="showDetail(monitorList[cInfo].tabId)" ng-class="{selected: cInfo == detailId}">{{monitorList[cInfo].accessId}}</li>
      </ul>
    </div>
</section>



