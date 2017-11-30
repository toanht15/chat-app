<?php echo $this->element('TAutoMessages/angularjs'); ?>

<?php $this->Form->inputDefaults(['label'=>false, 'div' => false, 'error' => false, 'legend' => false ]);?>
<div class="form01" ng-app="sincloApp" ng-controller="MainController as main" ng-cloak>
  <section>
    <h3>１．基本設定</h3>
    <ul class="settingList pl30">
      <!-- 名称 -->
      <li>
        <span class="require"><label>名称</label></span>
        <?= $this->Form->input('name', [
          'type' => 'text',
          'placeholder' => '名称',
          'maxlength' => 50
        ]) ?>
      <?php if (!empty($errors['name'])) echo "<li class='error-message'>" . h($errors['name'][0]) . "</li>"; ?>
      <input type="hidden" name="lastPage" value="<?= $lastPage?>">
      </li>
      <!-- 名称 -->

      <!-- トリガー -->
      <li>
        <span class="require"><label>トリガー</label></span>
        <?= $this->Form->input('trigger', array('type' => 'select', 'options' => $outMessageTriggerType, 'default' => C_AUTO_TRIGGER_TYPE_BODYLOAD)) ?>
      </li>
      <!-- トリガー -->
    </ul>
  </section>

  <section class="section2" >
    <h3>２．条件詳細設定</h3>
    <ul class="settingList pl30">
      <!-- 条件設定 -->
      <li>
        <span class="require"><label>条件設定</label></span>
        <label class="pointer"><?= $this->ngForm->input('main.condition_type', [
          'type' => 'radio',
          'options' => $outMessageIfType,
          'separator' => '</label><label class="pointer">',
          'error' => false
        ],[
          'entity' => 'conditionType',
          'default' => (!empty($this->data['TAutoMessage']['condition_type'])) ? $this->data['TAutoMessage']['condition_type'] : C_COINCIDENT,
        ]); ?></label>
      </li>
      <?php if (!empty($errors['condition_type'])) echo "<li class='error-message'>" . h($errors['condition_type'][0]) . "</li>"; ?>
      <!-- 条件設定 -->

      <!-- トリガー -->
      <li id="tautomessages_triggers" class="bt0">
        <!-- セット済みトリガー -->
        <div id="setTriggerList" class="pl30 setClockPicker">
          <ul>
            <items ng-repeat="(itemType, list) in main.setItemList" ng-if="main.keys(main.setItemList)!==0">
              <li ng-repeat="(itemId, setItem) in list track by $index" class="triggerItem selected" id="triggerItem_{{$id}}">
                <ng-form name="itemForm" ng-err-cnt novalidate>
                  <h4>
                    <span class="removeArea"><i class="remove" ng-click="main.removeItem(itemType, itemId)"></i></span>
                    <span class="labelArea" ng-click="main.openList('#triggerItem_' + $id)">{{main.tmpList[itemType].label}}<i class="error" ng-if="!itemForm.$valid" ng-showonhover="{{itemType}}"></i></span>
                  </h4>
                  <div>
                    <?php echo $this->element('TAutoMessages/templates'); ?>
                  </div>
                </ng-form>
              </li>
            </items>
            <li class='error-message' ng-if="main.keys(main.setItemList)===0">条件を右のリストから選択し、設定してください</li>
          </ul>
          <div class="balloon"><div class="balloonContent"></div></div>
        </div>
        <!-- セット済みトリガー -->
        <!-- トリガーリスト -->
        <div id="triggerList">
          <span id="pushImg"></span>
          <div>
            <ul>
              <li ng-repeat="(key, item) in main.tmpList" ng-class="{disableLi:main.checkDisabled(key)}" ng-click="main.addItem(key)">{{item.label}}</li>
            </ul>
          </div>
        </div>
        <!-- トリガーリスト -->
      </li>
      <!-- トリガー -->
      <?php
      if (!empty($errors['triggers'])) {
        foreach((array)$errors['triggers'] as $val) {
          echo "<li class='error-message' style='padding: 0 0 0 30px'>" . h($val) . "</li>";
        }
      }
      ?>

    </ul>
    <?=$this->ngForm->input('activity', ['type'=>'hidden'])?>
  </section>

  <h3>３．実行設定</h3>
  <section>
    <ul class="settingList pl30">
      <!-- アクション -->
      <li>
        <span class="require"><label>アクション</label></span>
        <?= $this->Form->input('action_type', array('type' => 'select', 'options' => $outMessageActionType, 'default' => C_AUTO_ACTION_TYPE_SENDMESSAGE)) ?>
      </li>
      <!-- アクション -->

      <!-- ウィジェット -->
      <li class="bt0">
        <span class="require"><label>ウィジェット</label></span>
        <label class="pointer"><?= $this->ngForm->input('main.widget_open', [
          'type' => 'radio',
          'options' => $outMessageWidgetOpenType,
          'separator' => '</label><label class="pointer">',
          'error' => false
        ], [
          'entity' => 'widget_open',
          'default' => (!empty($this->data['TAutoMessage']['widget_open'])) ? $this->data['TAutoMessage']['widget_open'] : C_AUTO_WIDGET_TYPE_CLOSE,
        ]); ?></label>
      </li>
      <!-- ウィジェット -->

      <!-- メッセージ -->
      <li class="pl30 bt0">
          <span>
            <label class="require">メッセージ</label>
            <span class="greenBtn btn-shadow actBtn" onclick="addOption(1)">選択肢を追加する</span>
            <span class="greenBtn btn-shadow actBtn" onclick="addOption(2)" id = "lastSpeechLabel">電話番号を追加する<div class = "questionBalloon questionBalloonPosition13"><icon class = "questionBtn">?</icon></div></span>
          </span>
          <?=$this->ngForm->input('action', ['type'=>'textarea', 'maxlength'=>300],['entiry'=>'action'])?>
          <?php if (!empty($errors['action'])) echo "<pre class='error-message'>" . h($errors['action'][0]) . "</pre>"; ?>

      </li>
      <!-- メッセージ -->

      <!-- 自由入力エリア -->
      <li class="bt0">
        <span class="require"><label>自由入力エリア</label></span>
        <label class="pointer"><?= $this->ngForm->input('main.chat_textarea', [
          'type' => 'radio',
          'options' => $outMessageTextarea,
          'separator' => '</label><br><label style="display:inline-block;"'.($coreSettings[C_COMPANY_USE_FREE_INPUT] ? '' : '"color: #CCCCCC;" class="commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。" data-balloon-position="20.5"').'>',
          'error' => false,
          'disabled' => !$coreSettings[C_COMPANY_USE_FREE_INPUT],
        ], [
          'entity' => 'chat_textarea',
          'default' => (!empty($this->data['TAutoMessage']['chat_textarea'])) ? $this->data['TAutoMessage']['chat_textarea'] : C_AUTO_WIDGET_TEXTAREA_OPEN,
        ]); ?></label>
      </li>
      <!-- 自由入力エリア -->

      <!-- cv -->
      <li class="bt0">
        <span class="require"><label>成果にCVとして登録する</label></span>
        <label style="cursor:pointer;" <?php echo ($coreSettings[C_COMPANY_USE_CV]) ? '' : 'class=commontooltip';?>
        <?php echo ($coreSettings[C_COMPANY_USE_CV]) ? '' : 'data-text=こちらの機能はスタンダードプランからご利用いただけます。';?>
        <?php echo ($coreSettings[C_COMPANY_USE_CV]) ? '' : 'data-balloon-position=40.5';?>
        <?php echo ($coreSettings[C_COMPANY_USE_CV]) ? '' : 'operatingHours=widgetHoursPage';?>>
        <?= $this->ngForm->input('main.cv', [
          'type' => 'radio',
          'options' => $outMessageCvType,
          'separator' => '</label><label class="pointer">',
          'error' => false,
          'disabled' => !$coreSettings[C_COMPANY_USE_CV],
        ], [
          'entity' => 'cv',
          'default' => (!empty($this->data['TAutoMessage']['cv'])) ? $this->data['TAutoMessage']['cv'] : C_AUTO_CV_DISABLED,
        ]); ?></label>
      </li>
      <!-- cv -->

      <!-- 状態 -->
      <li>
        <span class="require"><label>状態</label></span>
        <label class="pointer"><?= $this->Form->input('active_flg', [
          'type' => 'radio',
          'options' => $outMessageAvailableType,
          'default' => C_STATUS_AVAILABLE,
          'separator' => '</label>&nbsp;<label class="pointer">',
          'error' => false
        ]); ?></label>
      </li>
      <!-- 状態 -->
      <div id='lastSpeechTooltip' class="explainTooltip">
        <icon-annotation>
          <ul>
            <li><span>このボタンを押すと挿入される＜telno＞タグの間に電話番号を記入すると、スマホの場合にタップで発信できるようになります</span></li>
          </ul>
        </icon-annotation>
      </div>
    </ul>
  </section>

  <section>
    <?=$this->Form->hidden('id')?>
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

</div>
