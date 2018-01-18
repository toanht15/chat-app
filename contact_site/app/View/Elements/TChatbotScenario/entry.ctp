<?php echo $this->element('TChatbotScenario/angularjs'); ?>

<div ng-app="sincloApp" ng-controller="MainController as main" ng-cloak style="height: 100%;">
  <?=$this->Form->create('TChatbotScenario', ['url'=>['controller' =>'TChatbotScenario', 'action'=>'add'], 'novalidate' => true, 'id'=>'TChatbotScenarioEntryForm', 'name'=>'TChatbotScenarioEntryForm'])?>
    <?php $this->Form->inputDefaults(['label'=>false, 'div' => false, 'error' => false, 'legend' => false ]);?>
    <section id="tchatbotscenario_form_basic_settings" class="p10x">
      <h3 class="tchatbotscenario_form_subtitle">基本設定</h3>
      <ul>
        <!-- シナリオ名称 -->
        <li>
          <span class="require"><label>シナリオ名称</label></span>
          <?= $this->Form->input('name', [
            'type' => 'text',
            'placeholder' => 'シナリオ名称',
            'maxlength' => 50
          ]) ?>
        </li>
        <!-- メッセージ間隔 -->
        <li>
          <span class="require"><label>メッセージ間隔</label></span>
          <?= $this->Form->input('messageIntervalTimeSec', [
            'type' => 'text',
            'placeholder' => 'メッセージ間隔',
            'maxlength' => 50
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
        <li ng-repeat="(setActionId, setItem) in main.setActionList" class="set_action_item">
          <h4>{{setActionId + 1}}．{{setItem.label}}</h4>
          <?= $this->element('TChatbotScenario/templates'); ?>
          <a class="closeBtn redBtn" ng-click="main.removeItem(setActionId)"><?= $this->Html->image('close.png', array('alt' => '削除する', 'width' => 20, 'height' => 20, 'style' => 'margin: 0 auto')) ?></a>
        </li>
      </ul>
    </section>
    <section id="tchatbotscenario_form_preview">
        <div class="p10x">
          <h3 class="tchatbotscenario_form_subtitle">プレビュー</h3>
        </div>
        <div id="tchatbotscenario_form_preview_body" class="p10x">
          <section ng-repeat="(setActionId, setItem) in main.setActionList">
            <h4>{{setActionId + 1}}．{{setItem.label}}</h4>
            <ul>
              <div>
                <li ng-if="setItem.message" class="preview_talk">{{setItem.message}}</li>
              </div>
              <div>
                <li ng-if="setItem.errorMessage" class="preview_talk">{{setItem.errorMessage}}</li>
              </div>
              <div>
                <li ng-if="setItem.isConfirm && (setItem.confirmMessage || setItem.success || setItem.cancel)" class="preview_talk preview_talk_confirm">
                  {{setItem.confirmMessage}}
                  <ul>
                    <li ng-if="setItem.success"><input type="radio" disabled>{{setItem.success}}</li>
                    <li ng-if="setItem.cancel"><input type="radio" disabled>{{setItem.cancel}}</li>
                  </ul>
                </li>
              </div>
            </ul>
          </section>
        </div>
    </section>
  <?=$this->Form->end();?>

  <!-- フッター -->
  <section>
    <?=$this->Form->hidden('id')?>
    <?=$this->Form->hidden('m_mail_transmission_settings_id')?>
    <?=$this->Form->hidden('m_mail_template_id')?>
    <div id="tautomessages_actions" class="fotterBtnArea">
      <?=$this->Html->link('戻る','/TAutoMessages/index/page:'.$lastPage, ['class'=>'whiteBtn btn-shadow'])?>
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
