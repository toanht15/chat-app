<?php echo $this->element('TChatbotScenario/angularjs'); ?>

<div ng-app="sincloApp" ng-controller="MainController as main" ng-cloak>
  <div id="tchatbotscenario_form">
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
            <a ng-repeat="(key, item) in main.actionList" ng-click="main.addItem(key)" class="greenBtn btn-shadow">{{item.label}}</a>
          </div>
        </div>
        <ul id="tchatbotscenario_form_action_body" class="p20x">
        </ul>
      </section>
      <section id="tchatbotscenario_form_preview" class="p10x">
          <h3 class="tchatbotscenario_form_subtitle">プレビュー</h3>
          <section>
            <h4>１．テキスト発言</h4>
            <div>
              <li>資料請求ですね</li>
            </div>
          </section>
      </section>
    <?= $this->Form->end(); ?>
  </div>

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
