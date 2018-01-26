<?php echo $this->element('TChatbotScenario/angularjs'); ?>

<div ng-app="sincloApp" ng-controller="MainController as main" ng-cloak style="height: 100%;">
  <?=$this->Form->create('TChatbotScenario', ['url'=>['controller' =>'TChatbotScenario', 'action'=>'add'], 'novalidate' => true, 'id'=>'TChatbotScenarioEntryForm', 'name'=>'TChatbotScenarioEntryForm'])?>
    <?php $this->Form->inputDefaults(['label'=>false, 'div' => false, 'error' => false, 'legend' => false ]);?>
    <?=$this->Form->input('widgetSettings', ['type' => 'hidden','value' => json_encode($this->data['widgetSettings'])],[
        'entity' => ''
    ])?>

    <section id="tchatbotscenario_form_basic_settings" class="p10x">
      <h3 class="tchatbotscenario_form_subtitle">基本設定</h3>
      <ul>
        <!-- シナリオ名称 -->
        <li>
          <span class="require"><label>シナリオ名称</label></span>
          <?= $this->Form->input('name', [
            'type' => 'text',
            'placeholder' => 'シナリオ名称を入力',
            'maxlength' => 50
          ]) ?>
        </li>
        <!-- メッセージ間隔 -->
        <li>
          <span class="require"><label>メッセージ間隔</label></span>
          <?= $this->ngForm->input('messageIntervalTimeSec', [
            'type' => 'text',
            'class' => 'tRight',
            'maxlength' => 2,
            'ng-model' => 'messageIntervalTimeSec'
          ]) ?>
            秒
        </li>
      </ul>
    </section>
    <section id="tchatbotscenario_form_action">
      <div id="tchatbotscenario_form_action_header" class="p10x">
        <h3>アクションを追加する</h3>
        <div id="tchatbotscenario_form_action_menulist">
          <!-- アクション追加ボタン -->
          <a ng-repeat="(key, item) in main.actionList" ng-click="main.addItem(key)" class="greenBtn btn-shadow">{{item.label}}</a>
        </div>
      </div>
      <ul id="tchatbotscenario_form_action_body" class="p20x">
        <!-- アクション設定一覧 -->
        <li ng-repeat="(setActionId, setItem) in setActionList" id="action{{setActionId}}_setting" class="set_action_item">
          <h4><a href="#action{{setActionId}}_preview">{{setActionId + 1}}．{{setItem.label}}</a></h4>
          <?= $this->element('TChatbotScenario/templates'); ?>
          <a class="btn-shadow redBtn closeBtn" ng-click="main.removeItem(setActionId)"><?= $this->Html->image('close.png', array('alt' => '削除する', 'width' => 20, 'height' => 20, 'style' => 'margin: 0 auto')) ?></a>
        </li>
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
    <section id="tchatbotscenario_form_preview" ng-class="{middleSize: widgetSettings.widget_size_type == '2', largeSize: widgetSettings.widget_size_type == '3'}">
        <div class="p10x">
          <div id="start_simulator_button">
            <span class="btn-shadow blueBtn" ng-click="main.openSimulatorDialog()">シミュレーターを起動</span>
          </div>
          <h3 class="tchatbotscenario_form_subtitle">プレビュー</h3>
        </div>
        <div id="tchatbotscenario_form_preview_body">
          <?= $this->element('TChatbotScenario/preview'); ?>
        </div>
    </section>
  <?=$this->Form->end();?>

  <!-- フッター -->
  <section>
    <?=$this->Form->hidden('id')?>
    <?=$this->Form->hidden('m_mail_transmission_settings_id')?>
    <?=$this->Form->hidden('m_mail_template_id')?>
    <div id="tautomessages_actions" class="fotterBtnArea">
      <?=$this->Html->link('戻る','/TChatbotScenario/index/page:'.$lastPage, ['class'=>'whiteBtn btn-shadow'])?>
      <a href="javascript:void(0)" ng-click="main.saveAct()" class="greenBtn btn-shadow">保存</a>
      <?php
      $class = "";
      if ( empty($this->data['TAutoMessage']['id']) ) {
        $class = "vHidden";
      }
      ?>
        <a href="javascript:void(0)" onclick="removeAct(<?= $lastPage?>)" class="redBtn btn-shadow <?=$class?>">削除</a>
    </div>
  </section>

  <!-- シミュレーター -->

  <!-- テンプレート -->
  <div id="tchatbotscenario_action_templates" style="display: none;">
    <?php echo $this->element('TChatbotScenario/templates'); ?>
  </div>
<div>
