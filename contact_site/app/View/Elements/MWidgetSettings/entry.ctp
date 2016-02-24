<?= $this->Form->create('MWidgetSetting', ['type' => 'post', 'url' => ['controller' => 'MWidgetSettings', 'action' => 'index']]); ?>
  <div class="form01">
    <!-- /* 基本情報 */ -->
    <section>
      <?= $this->Form->input('id', ['type' => 'hidden']); ?>
      <ul>
        <li>
          <div class="labelArea fLeft"><span class="require"><label>表示設定</label></span></div>
          <?= $this->Form->input('display_type', ['type' => 'select', 'options' => $widgetDisplayType, 'label' => false, 'error' => false, 'div' => false]) ?>
        </li>
        <?php if ( $this->Form->isFieldError('display_type') ) echo $this->Form->error('display_type', null, ['wrap' => 'li']); ?>
      </ul>
    </section>

    <!-- /* ウィジェットの文言設定 */ -->
    <section ng-app="sincloApp" ng-controller="WidgetCtrl">
      <div id="set_widget_detail_area">
        <h2>ウィジェット詳細設定</h2>
        <div id="widget_detail_area">
          <ul>

          <!-- ウィジェットタイトル -->
            <li>
              <div class="labelArea fLeft"><span class="require"><label>タイトル</label></span></div>
              <?= $this->Form->input('title', [
                  'type' => 'text',
                  'placeholder' => 'ウィジェットタイトル',
                  'ng-model' => 'title',
                  'ng-init' => 'title="' . h($this->data['MWidgetSetting']['title']) . '";',
                  'div' => false,
                  'label' => false,
                  'maxlength' => 12,
                  'error' => false
              ]) ?>
            </li>
            <?php if ($this->Form->isFieldError('title')) echo $this->Form->error('title', null, ['wrap' => 'li']); ?>
          <!-- ウィジェットタイトル -->

          <!-- お問い合わせ先 -->
            <li>
              <div class="labelArea fLeft"><span class="require"><label>お問い合わせ先</label></span></div>
              <?= $this->ngForm->input('tel', [
                  'type' => 'text',
                  'placeholder' => 'お問い合わせ先電話番号',
                  'div' => false,
                  'label' => false,
                  'maxlength' => 13,
                  'error' => false
              ],
              [
                  'entity' => 'MWidgetSetting.tel'
              ]
              ) ?>
            </li>
            <?php if ($this->Form->isFieldError('tel') ) echo $this->Form->error('tel', null, ['wrap' => 'li']); ?>
          <!-- お問い合わせ先 -->

          <!-- 受付時間指定 -->
            <li>
              <div class="labelArea fLeft"><span class="require"><label>受付時間指定</label></span></div>
              <?= $this->ngForm->input('display_time_flg', [
                  'type' => 'radio',
                  'fieldset' => false,
                  'separator' => '&nbsp;',
                  'legend' => false,
                  'options' => [
                      'しない',
                      'する'
                  ],
                  'label' => false,
                  'error' => false
              ],
              [
                  'entity' => 'MWidgetSetting.display_time_flg',
                  'change' => 'isDisplayTime()'
              ]) ?>
            </li>
            <?php if ($this->Form->isFieldError('display_time_flg') ) echo $this->Form->error('display_time_flg', null, ['wrap' => 'li']); ?>
          <!-- 受付時間指定 -->

          <!-- 受付時間の表記 -->
<?php
// ウィジェットに受付時間を表記しない場合は、受付時間を更新対象外にする。
$isDisableTimeText = false;
if ( isset($this->data['MWidgetSetting']['display_time_flg']) && intval($this->data['MWidgetSetting']['display_time_flg']) !== 1 ) {
  $isDisableTimeText = true;
}
$isRequired = "";
if ( !$isDisableTimeText ) {
  $isRequired = "class='require'";
}
?>
            <li>
              <div class="labelArea fLeft"><span <?=$isRequired?> id="timeTextLabel"><label>受付時間の表記</label></span></div>
              <?= $this->ngForm->input('time_text', [
                  'type' => 'text',
                  'placeholder' => '受付時間の表記',
                  'div' => false,
                  'label' => false,
                  'maxlength' => 15,
                  'disabled' => $isDisableTimeText,
                  'error' => false
              ],
              [
                  'entity' => 'MWidgetSetting.time_text'
              ]) ?>
            </li>
            <?php if ($this->Form->isFieldError('time_text') ) echo $this->Form->error('time_text', null, ['wrap' => 'li']); ?>
          <!-- 受付時間の表記 -->

          <!-- ウィジェット本文 -->
            <li>
              <div class="labelArea fLeft"><span><label>ウィジェット本文</label></span></div>
              <?= $this->ngForm->input('content', [
                  'type' => 'textarea',
                  'placeholder' => '本文',
                  'div' => false,
                  'label' => false,
                  'maxlength' => 100,
                  'error' => false
              ],
              [
                  'entity' => 'MWidgetSetting.content'
              ]) ?>
            </li>
            <?php if ($this->Form->isFieldError('content') ) echo $this->Form->error('content', null, ['wrap' => 'li']); ?>
          <!-- ウィジェット本文 -->

          </ul>
        </div>

        <div id="sample_widget_area">
          <div id="sincloBox" style="position: relative; border: 1.5px solid rgb(232, 231, 224); border-radius: 10px 10px 0 0; z-index: 1; width: 250px; overflow: hidden; background-color: rgb(255, 255, 255);">
            <img style="position: absolute; top: 11.5px; right: 10px; z-index: 0;" src="https://ws1.sinclo.jp/img/yajirushi.png" height="12" width="16.5">
            <div style="background-color: #ABCD05; width: 100%; height: 35px; background-image: url(https://ws1.sinclo.jp/img/call.png); background-repeat: no-repeat; background-position: 15px, 0; background-size: 4.5%; color: #FFF;">
              <!-- タイトル -->
              <pre style="color: #FFF; text-align: center; font-size: 15px; padding: 10px; margin:  0;">{{title}}</pre>
              <!-- タイトル -->
            </div>

            <div id="receiptArea" style="background-image: url(https://ws1.sinclo.jp/img/call_circle.png); background-repeat: no-repeat; background-position: 5px, 0px; height: 50px; margin: 15px 10px; background-size: 55px auto, 55px auto; padding-left: 55px;">

              <!-- 受付電話番号 -->
              <pre id="telNumber" style="font-weight: bold; color: #ABCD05; margin: 0 auto; font-size: 18px; text-align: center; padding: 5px 0px 0px; height: 30px">{{tel}}</pre>
              <!-- 受付電話番号 -->

              <!-- 受付時間 -->
              <pre ng-if="display_time_flg == '1'" style="font-weight: bold; color: #ABCD05; margin: 0 auto; font-size: 10px; text-align: center; padding: 0 0 5px; height: 20px">受付時間： {{time_text}}</pre>
              <!-- 受付時間 -->

            </div>

            <!-- テキスト -->
            <pre style="display: block; word-wrap: break-word; font-size: 11px; text-align: center; margin: auto; line-height: 1.5; color: #6B6B6B; width: 20em;">{{content}}</pre>
            <!-- テキスト -->

            <span style="display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border: 1px solid rgb(188, 188, 188); border-radius: 15px">
              ●●●●
            </span>

            <p >Powered by <a target="sinclo" href="http://medialink-ml.co.jp/index.html">sinclo</a></p>

          </div>
        </div>
      </div>

    </section>

    <!-- /* 操作 */ -->
    <section>
      <div id="m_widget_setting_action">
        <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
      </div>
    </section>

  </div>
<?= $this->Form->end(); ?>
