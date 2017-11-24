<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('Customers/userAgentCheck') ?>
<?php echo $this->element('Customers/script') ?>
<?php echo $this->element('Customers/angularjs') ?>

<section id='customer_idx' class="{{customerMainClass}}" ng-app="sincloApp" ng-controller="MainController">

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
else if ( !$coreSettings[C_COMPANY_USE_SYNCLO] && !$coreSettings[C_COMPANY_USE_CHAT] && (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])  ) {
  $cName = "docOnly";
}
?>

    <div id='customer_sub_pop' data-contract='<?=$cName?>' ng-show="detailId" ng-cloak><?php echo $this->element('Customers/detail') ?></div>

    <div id='customer_tab' ng-cloak>
      <ul>
        <li ng-repeat="cInfo in chatList" ng-click="showDetail(monitorList[cInfo].tabId, monitorList[cInfo].sincloSessionId)" ng-class="{selected: cInfo == detailId}">{{monitorList[cInfo].accessId}}</li>
      </ul>
    </div>
</section>



