<div id="tchatbotscenario_simulator_wrapper" style="display: none;">
  <div id="simulator_popup">
    <div id="simulator_popup_header">
      <h2>シミュレーター</h2>
      <div>
        <a class="btn-shadow redBtn closeBtn closeSimulator" ng-click="closeSimulator()"><?= $this->Html->image('close.png', array('alt' => '閉じる', 'width' => 20, 'height' => 20, 'style' => 'margin: 0 auto')) ?></a>
      </div>
    </div>
    <div id="simulator_popup_body">
      <?= $this->element('WidgetSimulator/simulator', ['isTabDisplay' => false, 'isVisitorSendMessage' => true]); ?>
    </div>
    <div id="simulator_popup_footer">
      <a class="textBtn greenBtn btn-shadow" ng-click="actionClear()">クリア</a>
      <a class="textBtn greenBtn btn-shadow" ng-click="closeSimulator()">閉じる</a>
    </div>
  </div>
</div>
