<?php echo $this->element('Customers/userAgentCheck') ?>
<?php echo $this->element('Customers/script') ?>
<?php echo $this->element('Customers/angularjs') ?>

<section id='customer_idx' class="{{customerMainClass}}" ng-app="sincloApp" ng-controller="MainCtrl" ng-cloak>

    <div id='customer_main' class="card-shadow">
        <?php echo $this->element('Customers/monitor') ?>
    </div>

    <div id='customer_sub' ng-cloak><?php echo $this->element('Customers/detail') ?></div>

    <div id='customer_tab' ng-cloak>
      <ul>
        <li ng-repeat="cInfo in chatList" ng-click="showDetail(monitorList[cInfo].tabId)" ng-class="{selected: cInfo == detailId}">{{monitorList[cInfo].accessId}}</li>
      </ul>
    </div>
</section>



