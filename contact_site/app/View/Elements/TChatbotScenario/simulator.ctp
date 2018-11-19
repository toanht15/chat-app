<div id="tchatbotscenario_simulator_wrapper" style="display: none;">
  <div id="simulator_popup">
    <div id="simulator_popup_header">
      <h2>シミュレーター</h2>
      <div>
        <a class="btn-shadow redBtn closeBtn closeSimulator" ng-click="closeSimulator()"><?= $this->Html->image('close.png', array('alt' => '閉じる', 'width' => 20, 'height' => 20, 'style' => 'margin: 0 auto')) ?></a>
      </div>
    </div>
    <div id="simulator_popup_body">
      <p id="maximum_description" ng-if="widget.settings['widget_size_type'] == 4" style="margin-top:0px;color:#F57E7E">表示されているウィジェットのサイズは実際のサイズ（最大）ではありません。実際のサイズは ＜<?= $this->Html->link('デモサイト', array('controller' => 'ScriptSettings', 'action' => 'testpage',$companyKey), array('target' => '_demo', 'class' => 'underL' ,'style' => 'color:#F57E7E')) ?>＞ から確認してください。</p>
      <?= $this->element('WidgetSimulator/simulator', ['isTabDisplay' => false, 'canVisitorSendMessage' => true]); ?>
    </div>
    <div id="simulator_popup_footer">
      <a class="textBtn greenBtn btn-shadow" ng-click="actionClear()">クリア</a>
      <a class="textBtn greenBtn btn-shadow" ng-click="closeSimulator()">閉じる</a>
    </div>
  </div>
</div>
