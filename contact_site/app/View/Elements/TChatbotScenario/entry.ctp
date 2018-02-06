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
    <ul id="tchatbotscenario_form_action_body">
      <!-- アクション設定一覧 -->
      <li ng-repeat="(setActionId, setItem) in setActionList" id="action{{setActionId}}_setting" class="set_action_item">
        <h4><a href="#action{{setActionId}}_preview">{{setActionId + 1}}．{{actionList[setItem.actionType].label}} <i class="error" ng-if="!setItem.$valid"></i></a></h4>
        <?= $this->element('TChatbotScenario/templates'); ?>
        <a class="btn-shadow redBtn closeBtn" ng-click="main.removeItem(setActionId)"><?= $this->Html->image('close.png', array('alt' => '削除する', 'width' => 20, 'height' => 20, 'style' => 'margin: 0 auto')) ?></a>
      </li>
      <li class="error-message" ng-if="setActionList.length <= 0">アクションを上のリストから選択し、設定してください</li>
      <!-- Tooltip -->
      <div id='hearingVariableNameTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>チャットボットから投げかけた質問の回答を保存し、｛｛変数名｝｝としてメッセージ内で利用することができるようになります</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='hearingVariableTypeTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>サイト訪問者が入力した回答が適切か、整合性チェックを行うことができるようになります</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='hearingErrorMessageTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>サイト訪問者が入力した回答が不正な内容の場合に、返信するメッセージになります</span></li>
          </ul>
        </icon-annotation>
      </div>
      <div id='hearingSelectVariableNameTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>チャットボットから投げかけた質問の回答を保存し、｛｛変数名｝｝としてメッセージ内で利用することができるようになります</span></li>
          </ul>
        </icon-annotation>
      </div>
    </ul>
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
        $class = "vHidden";
      }
      ?>
        <a href="javascript:void(0)" onclick="removeAct(<?= $lastPage?>)" class="redBtn btn-shadow <?=$class?>">削除</a>
    </div>
  </section>

  <div ng-controller="DialogController as dialog" ng-cloack>
    <?= $this->element('TChatbotScenario/simulator'); ?>
  </div>
</div>
