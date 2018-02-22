<?php echo $this->element('TChatbotScenario/angularjs'); ?>
<?php echo $this->element('TChatbotScenario/localStorageService'); ?>
<?php echo $this->element('WidgetSimulator/simulatorService'); ?>

<div ng-app="sincloApp" ng-controller="MainController as main" ng-cloak style="height: 100%;" id="tchatbotscenario_form">
  <?php $this->Form->inputDefaults(['label'=>false, 'div' => false, 'error' => false, 'legend' => false ]);?>
  <input type="hidden" name="data[TChatbotScenario][activity]" id="TChatbotScenarioActivity">
  <input type="hidden" name="lastPage" value="<?= $lastPage?>">
  <?=$this->Form->input('widgetSettings', ['type' => 'hidden','value' => json_encode($this->data['widgetSettings'])])?>

  <section id="tchatbotscenario_form_basic_settings" class="p10x">
    <h3 class="tchatbotscenario_form_subtitle">基本設定</h3>
    <ul>
      <!-- シナリオ名称 -->
      <li>
        <span class="require"><label>シナリオ名称</label></span>
        <?= $this->ngForm->input('name', [
          'type' => 'text',
          'placeholder' => 'シナリオ名称を入力',
          'maxlength' => 50
        ]) ?>
        <?php if (!empty($errors['name'])) echo "<span class='error-message'>" . h($errors['name'][0]) . "</span>"; ?>
      </li>
      <!-- メッセージ間隔 -->
      <li>
        <span class="require"><label>メッセージ間隔</label></span>
        <?= $this->ngForm->input('messageIntervalTimeSec', [
          'type' => 'text',
          'class' => 'tRight',
          'maxlength' => 3,
          'ng-model' => 'messageIntervalTimeSec',
          'after' => '秒'
        ]) ?>
          <?php if (!empty($errors['messageIntervalTimeSec'])) echo "<li class='error-message'>" . h($errors['messageIntervalTimeSec'][0]) . "</li>"; ?>
      </li>
    </ul>
  </section>
  <section id="tchatbotscenario_form_action">
    <div id="tchatbotscenario_form_action_header" class="p10x">
      <h3>アクションを追加する</h3>
      <div id="tchatbotscenario_form_action_menulist">
        <!-- アクション追加ボタン -->
        <a ng-repeat="(key, item) in actionList" ng-click="main.addItem(key)" class="greenBtn btn-shadow">{{item.label}}</a>
      </div>
    </div>
    <ul ui-sortable="sortableOptions" ng-model="setActionList" id="tchatbotscenario_form_action_body" class="sortable">
      <!-- アクション設定一覧 -->
      <li ng-repeat="(setActionId, setItem) in setActionList" ng-model="setItem" id="action{{setActionId}}_setting" class="set_action_item" validate-action>
        <h4 class="handle"><a href="#action{{setActionId}}_preview">{{setActionId + 1}}．{{actionList[setItem.actionType].label}} <i class="error" ng-if="!setItem.$valid"></i></a></h4>
        <?= $this->element('TChatbotScenario/templates'); ?>
        <a class="btn-shadow redBtn closeBtn" ng-click="main.removeItem(setActionId)"><?= $this->Html->image('close.png', array('alt' => '削除する', 'width' => 20, 'height' => 20, 'style' => 'margin: 0 auto')) ?></a>
      </li>
      <li class="error-message" ng-if="setActionList.length <= 0">アクションを上のリストから選択し、設定してください</li>
      <!-- Tooltip -->
    </ul>
    <div class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail"></span></li>
        </ul>
      </icon-annotation>
    </div>
  </section>
  <section id="tchatbotscenario_form_preview" ng-class="{middleSize: widget.settings['widget_size_type'] == '2', largeSize: widget.settings['widget_size_type'] == '3'}">
      <div class="p10x">
        <div id="start_simulator_button">
          <span class="btn-shadow blueBtn" ng-click="main.openSimulator()">シミュレーターを起動</span>
        </div>
        <h3 class="tchatbotscenario_form_subtitle">プレビュー</h3>
      </div>
      <div id="tchatbotscenario_form_preview_body">
        <?= $this->element('TChatbotScenario/preview'); ?>
      </div>
  </section>

  <!-- フッター -->
  <section>
    <?=$this->Form->hidden('id')?>
    <div id="tchatbotscenario_actions" class="fotterBtnArea">
      <?=$this->Html->link('戻る','/TChatbotScenario/index/page:'.$lastPage, ['class'=>'whiteBtn btn-shadow'])?>
      <a href="javascript:void(0)" ng-click="main.saveAct()" class="greenBtn btn-shadow">保存</a>
      <?php
      $class = "";
      if ( empty($this->data['TChatbotScenario']['id']) ) {
        $class = "redBtn vHidden";
      } else
      if (count($this->data['TAutoMessage']) >= 1) {
        $class = "disOffgrayBtn disabled commontooltip";
      } else {
        $class = "redBtn";
      }
      ?>
      <?= $this->Html->link(
        '削除',
        'javascript:void(0)',
        array('escape' => false,
        'class' => 'btn-shadow ' . $class,
        'id' => 'tchatbotscenario_edit_remove_btn',
        'onclick' => strpos($class, 'disabled') === false ? 'removeAct(' . $lastPage . ')' : '',
        'disabled' => strpos($class, 'disabled') !== false,
        'data-text' => strpos($class, 'disabled') !== false ? '呼び出し元が設定されているため、<br>削除できません' : '',
        'data-balloon-position' => '50',
        'data-balloon-width' =>  strpos($class, 'disOffgrayBtn') !== false ? '216' : ''
      )) ?>
    </div>
  </section>

  <div ng-controller="DialogController as dialog" ng-cloack>
    <?= $this->element('TChatbotScenario/simulator'); ?>
  </div>
</div>
