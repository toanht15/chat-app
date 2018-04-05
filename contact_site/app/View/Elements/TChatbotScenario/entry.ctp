<?php echo $this->element('TChatbotScenario/angularjs'); ?>
<?php echo $this->element('TChatbotScenario/localStorageService'); ?>
<?php echo $this->element('WidgetSimulator/simulatorService'); ?>

<div ng-app="sincloApp" ng-controller="MainController as main" ng-cloak style="height: 100%;" id="tchatbotscenario_form">
  <?php $this->Form->inputDefaults(['label'=>false, 'div' => false, 'error' => false, 'legend' => false ]);?>
  <input type="hidden" name="data[TChatbotScenario][activity]" id="TChatbotScenarioActivity">
  <input type="hidden" name="lastPage" value="<?= $lastPage?>">
  <?=$this->Form->input('widgetSettings', ['type' => 'hidden','value' => json_encode($this->data['widgetSettings'])])?>
  <?=$this->Form->input('scenarioList', ['type' => 'hidden','value' => json_encode($this->data['scenarioList'])])?>

  <section id="tchatbotscenario_form_basic_settings" class="p10x">
    <h3 class="tchatbotscenario_form_subtitle">基本設定</h3>
    <ul>
      <!-- シナリオ名称 -->
      <li>
        <label>シナリオ名称<span class="questionBalloon"><icon class="questionBtn" data-tooltip="シナリオに名称を設定します。" data-tooltip-width='160'>?</icon></span></label>
        <?= $this->ngForm->input('name', [
          'type' => 'text',
          'placeholder' => 'シナリオ名称を入力',
          'maxlength' => 50,
          'class' => 'w100'
        ]) ?>
        <?php if (!empty($errors['name'])) echo "<span class='error-message'>" . h($errors['name'][0]) . "</span>"; ?>
      </li>
      <!-- メッセージ間隔 -->
      <li>
        <span><label>メッセージ間隔<span class="questionBalloon"><icon class="questionBtn" data-tooltip="各メッセージを送信する間隔（秒数）を設定します。">?</icon></span></label></span>
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
        <!-- テキスト発言 -->
        <a ng-click="main.addItem(<?= C_SCENARIO_ACTION_TEXT ?>)" class="greenBtn btn-shadow commontooltip" data-text="チャットボットに発言させたいテキストメッセージを設定できるアクションです。">テキスト発言</a>
        <!-- ヒアリング -->
        <a ng-click="main.addItem(<?= C_SCENARIO_ACTION_HEARING ?>)" class="greenBtn btn-shadow commontooltip" data-text="チャットボットから投げかけたい質問（ヒアリング項目）を設定し、サイト訪問者からのテキスト入力を受け付けるアクションです。ヒアリング項目は複数設定することが可能です。">ヒアリング</a>
        <!-- 選択肢 -->
        <a ng-click="main.addItem(<?= C_SCENARIO_ACTION_SELECT_OPTION ?>)" class="greenBtn btn-shadow commontooltip" data-text="チャットボットに発言させたい選択式（択一式）メッセージを設定できるアクションです。">選択肢</a>
        <!-- メール送信 -->
        <a ng-click="main.addItem(<?= C_SCENARIO_ACTION_SEND_MAIL ?>)" class="greenBtn btn-shadow commontooltip" data-text="メールを送信するアクションです。宛先、差出人名、メールタイトル、メール本文を自由に設定することが可能です。">メール送信</a>
        <!-- シナリオ呼び出し -->
        <a ng-click="main.addItem(<?= C_SCENARIO_ACTION_CALL_SCENARIO ?>)" class="greenBtn btn-shadow commontooltip" data-text="呼び出したいシナリオを設定し、アクションの途中で登録済みのシナリオを実行することができるアクションです。">シナリオ呼び出し</a>
        <!-- 外部システム連携 -->
        <a ng-click="main.addItem(<?= C_SCENARIO_ACTION_EXTERNAL_API ?>)" class="greenBtn btn-shadow commontooltip" data-text="連携したい外部システムの設定を行い、アクションの途中で任意のAPIを実行することができるアクションです。">外部システム連携</a>
        <!-- ファイル送信 -->
        <a ng-click="main.addItem(<?= C_SCENARIO_ACTION_SEND_FILE ?>)" class="greenBtn btn-shadow commontooltip" data-text="送信したいファイルを設定できるアクションです。">ファイル送信</a>
      </div>
    </div>
    <ul ui-sortable="sortableOptions" ng-model="setActionList" id="tchatbotscenario_form_action_body" class="sortable">
      <!-- アクション設定一覧 -->
      <li ng-repeat="(setActionId, setItem) in setActionList" ng-model="setItem" id="action{{setActionId}}_setting" class="set_action_item" validate-action>
        <h4 class="handle"><a href="#action{{setActionId}}_preview">{{setActionId + 1}}．{{actionList[setItem.actionType].label}} <i class="error errorBtn" ng-if="!setItem.$valid"></i></a></h4>
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
      <a href="javascript:void(0)" ng-click="main.saveTemporary()" class="greenBtn btn-shadow">一時保存</a>
      <a href="javascript:void(0)" ng-click="main.saveAct()" class="greenBtn btn-shadow">保存</a>
      <?php
      $class = "";
      if ( empty($this->data['TChatbotScenario']['id']) ) {
        $class = "redBtn vHidden";
      } else
      if (count($this->data['callerInfo']['TAutoMessage']) >= 1 || count($this->data['callerInfo']['TChatbotScenario']) >= 1) {
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
        'ng-click' => strpos($class, 'disabled') === false ? 'main.removeAct(' . $lastPage . ')' : '',
        'disabled' => strpos($class, 'disabled') !== false,
        'data-text' => strpos($class, 'disabled') !== false ? '呼び出し元が設定されているため、<br>削除できません' : '',
        'data-balloon-position' => '50',
        'data-balloon-width' =>  strpos($class, 'disOffgrayBtn') !== false ? '216' : ''
      )) ?>
    </div>
  </section>

  <!-- エラーメッセージのツールチップ -->
  <div class="errorBalloon">
    <span class="detail"></span>
  </div>

  <!-- シミュレーター -->
  <div ng-controller="DialogController as dialog" ng-cloack>
    <?= $this->element('TChatbotScenario/simulator'); ?>
  </div>
</div>
