<?php
$headerNo = 1;
?>
  <?= $this->Form->create('MWidgetSetting', ['type' => 'post', 'url' => ['controller' => 'MWidgetSettings', 'action' => 'index'],  'enctype'=>'multipart/form-data']); ?>
    <div class="form01">
      <!-- /* 基本情報 */ -->
      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．表示設定</h3>
      <section>
        <?= $this->Form->input('id', ['type' => 'hidden']); ?>
        <ul class="settingList">
          <!-- 表示するタイミング -->
          <li>
            <span class="require"><label>表示するタイミング</label></span>
            <div>
              <?php $maxShowWidgetTimingTagBySite = $this->ngForm->input('max_show_timing_site', [
                  'type' => 'number',
                  'div' => false,
                  'label' => false,
                  'ng-disabled' => 'showTiming !== "'.C_WIDGET_SHOW_TIMING_SITE.'"',
                  'string-to-number' => '', // @see http://qiita.com/amagurik2/items/b64b0a005a60b6eb225b
                  'before' => 'サイト訪問から',
                  'after' => '秒後に表示する',
                  'maxlength' => 4,
                  'style' => 'width:6em',
                  'max' => 3600,
                  'min' => 0,
                  'error' => false
              ],[
                  'entity' => 'MWidgetSetting.max_show_timing_site'
              ]); ?>
              <?php $maxShowWidgetTimingTagByPage = $this->ngForm->input('max_show_timing_page', [
                  'type' => 'number',
                  'div' => false,
                  'label' => false,
                  'ng-disabled' => 'showTiming !== "'.C_WIDGET_SHOW_TIMING_PAGE.'"',
                  'string-to-number' => '', // @see http://qiita.com/amagurik2/items/b64b0a005a60b6eb225b
                  'before' => 'ページ訪問から',
                  'after' => '秒後に表示する',
                  'maxlength' => 4,
                  'style' => 'width:6em',
                  'max' => 3600,
                  'min' => 0,
                  'error' => false
              ],[
                  'entity' => 'MWidgetSetting.max_show_timing_page'
              ]); ?>
              <div ng-init="showTiming='<?=h(empty($this->formEx->val($this->data['MWidgetSetting'], 'show_timing')) ? C_WIDGET_SHOW_TIMING_IMMEDIATELY : $this->formEx->val($this->data['MWidgetSetting'], 'show_timing'))?>'">
                <label class="pointer" for="showTiming<?=C_WIDGET_SHOW_TIMING_SITE?>"><input type="radio" name="data[MWidgetSetting][show_timing]" ng-model="showTiming" id="showTiming<?=C_WIDGET_SHOW_TIMING_SITE?>" value="<?=C_WIDGET_SHOW_TIMING_SITE?>"><?=$maxShowWidgetTimingTagBySite?></label><br>
                <label class="pointer" for="showTiming<?=C_WIDGET_SHOW_TIMING_PAGE?>"><input type="radio" name="data[MWidgetSetting][show_timing]" ng-model="showTiming" id="showTiming<?=C_WIDGET_SHOW_TIMING_PAGE?>" value="<?=C_WIDGET_SHOW_TIMING_PAGE?>" ><?=$maxShowWidgetTimingTagByPage?></label><br>
                <?php if(isset($coreSettings[C_COMPANY_USE_CHAT]) && $coreSettings[C_COMPANY_USE_CHAT]): ?>
                <label class="pointer" for="showTiming<?=C_WIDGET_SHOW_TIMING_RECV_1ST_AUTO_MES?>"><input type="radio" name="data[MWidgetSetting][show_timing]" ng-model="showTiming" id="showTiming<?=C_WIDGET_SHOW_TIMING_RECV_1ST_AUTO_MES?>" value="<?=C_WIDGET_SHOW_TIMING_RECV_1ST_AUTO_MES?>" >初回オートメッセージ受信時に表示する</label><br>
                <?php endif; ?>
                <label class="pointer" for="showTiming<?=C_WIDGET_SHOW_TIMING_IMMEDIATELY?>"><input type="radio" name="data[MWidgetSetting][show_timing]" ng-model="showTiming" id="showTiming<?=C_WIDGET_SHOW_TIMING_IMMEDIATELY?>" value="<?=C_WIDGET_SHOW_TIMING_IMMEDIATELY?>">すぐに表示する</label>
              </div>
            </div>
          </li>
          <?php if ( $this->Form->isFieldError('show_timing') ) echo $this->Form->error('show_timing', null, ['wrap' => 'li']); ?>
          <?php if ( $this->Form->isFieldError('max_show_timing_site') ) echo $this->Form->error('max_show_timing_site', null, ['wrap' => 'li']); ?>
          <?php if ( $this->Form->isFieldError('max_show_timing_page') ) echo $this->Form->error('max_show_timing_page', null, ['wrap' => 'li']); ?>
          <!-- 表示設定 -->
          <li>
            <span class="require"><label>表示する条件</label></span>
            <pre><label class="pointer"><?= $this->Form->input('display_type', ['type' => 'radio',  'options' => $widgetDisplayType, 'legend' => false, 'separator' => '</label><br><label class="pointer">', 'label' => false, 'error' => false, 'div' => false]) ?></label>
            </pre>
          </li>
          <?php if ( $this->Form->isFieldError('display_type') ) echo $this->Form->error('display_type', null, ['wrap' => 'li']); ?>
          <!-- 表示設定 -->
          <!-- 最大化時間設定 -->
          <li>
            <span><label>最大化する条件</label></span>
            <div>
              <?php $maxShowTimeTagBySite = $this->ngForm->input('max_show_time', [
                'type' => 'number',
                'div' => false,
                'label' => false,
                'ng-disabled' => 'showTime !== "'.C_WIDGET_AUTO_OPEN_TYPE_SITE.'"',
                'string-to-number' => '', // @see http://qiita.com/amagurik2/items/b64b0a005a60b6eb225b
                'before' => 'サイト訪問から',
                'after' => '秒後に自動で最大化する',
                'maxlength' => 4,
                'style' => 'width:6em',
                'max' => 3600,
                'min' => 0,
                'error' => false
              ],[
                'entity' => 'MWidgetSetting.max_show_time'
              ]); ?>
              <?php $maxShowTimeTagByPage = $this->ngForm->input('max_show_time_page', [
                'type' => 'number',
                'div' => false,
                'label' => false,
                'ng-disabled' => 'showTime !== "'.C_WIDGET_AUTO_OPEN_TYPE_PAGE.'"',
                'string-to-number' => '', // @see http://qiita.com/amagurik2/items/b64b0a005a60b6eb225b
                'before' => 'ページ訪問から',
                'after' => '秒後に自動で最大化する',
                'maxlength' => 4,
                'style' => 'width:6em',
                'max' => 3600,
                'min' => 0,
                'error' => false
              ],[
                'entity' => 'MWidgetSetting.max_show_time_page'
              ]); ?>
              <div ng-init="showTime='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_time'))?>'">
                <label class="pointer padding" for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_ON?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_ON?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_ON?>">自動で最大化する</label><br>
                <label class="pointer padding" for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>" ><?=$maxShowTimeTagBySite?></label><br>
                <label class="pointer padding" for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>" ><?=$maxShowTimeTagByPage?></label><br>
                <label class="pointer padding" for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_OFF?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_OFF?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_OFF?>">最大化しない</label>
              </div>
            </div>
          </li>
          <?php if ( $this->Form->isFieldError('show_time') ) echo $this->Form->error('show_time', null, ['wrap' => 'li']); ?>
          <?php if ( $this->Form->isFieldError('max_show_time') ) echo $this->Form->error('max_show_time', null, ['wrap' => 'li']); ?>
          <?php if ( $this->Form->isFieldError('max_show_time_page') ) echo $this->Form->error('max_show_time_page', null, ['wrap' => 'li']); ?>
          <!-- 最大化時間設定 -->
          <!-- 表示位置 -->
          <li>
            <span class="require"><label>表示位置</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('show_position', [
                'type' => 'radio',
                'options' => $widgetPositionType,
                'legend' => false,
                'separator' => '</label><br><label class="pointer">',
                'div' => false,
                'label' => false,
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.show_position'
              ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('show_position') ) echo $this->Form->error('show_position', null, ['wrap' => 'li']); ?>
          <!-- 表示位置 -->
        </ul>
      </section>

      <!-- /* ウィジェットの文言設定 */ -->
      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．ウィジェットデザイン</h3>
      <section>
        <ul class="settingList">

          <!-- ウィジットサイズ -->
          <li>
            <span class='require'><label>ウィジェットサイズ</label></span>
            <div ng-init="widgetSizeTypeToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'widget_size_type'))?>'">
              <label class="pointer choose" for="widgetSizeType1"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" id="widgetSizeType1" class="showHeader" value="1" >小</label><br>
              <label class="pointer choose" for="widgetSizeType2"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" id="widgetSizeType2" class="showHeader" value="2" >中</label><br>
              <label class="pointer choose" for="widgetSizeType3"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" id="widgetSizeType3" class="showHeader" value="3" >大</label><br>
            </div>
          </li>
          <!-- ウィジットサイズ -->

          <!-- ウィジェットタイトル -->
          <li>
            <span class="require"><label>トップタイトル</label></span>
            <?= $this->ngForm->input('title', [
              'type' => 'text',
              'placeholder' => 'トップタイトル',
              'div' => false,
              'label' => false,
              'maxlength' => 12,
              'error' => false
            ],[
              'entity' => 'MWidgetSetting.title'
            ]) ?>
          </li>
          <?php if ($this->Form->isFieldError('title')) echo $this->Form->error('title', null, ['wrap' => 'li']); ?>
          <!-- ウィジェットタイトル -->

          <!-- 企業名 -->
          <li>
            <span class='require'><label>企業名</label></span>
            <?php
            $subTitleOpt = [
                'type' => 'text',
                'placeholder' => '企業名',
                'div' => false,
                'style' => 'margin:10px 0 10px 20px;',
                'label' => false,
                'class' => 'showHeader',
                'required' => false,
                'maxlength' => 15,
                'error' => false
            ];
            if(($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])) && !$coreSettings[C_COMPANY_USE_CHAT]) {
              $subTitleOpt['ng-disabled'] = 'subTitleToggle == "2"';
            }
            $subTitle = $this->ngForm->input('sub_title', $subTitleOpt, [
              'entity' => 'MWidgetSetting.sub_title'
            ]);
            ?>
            <div ng-init="subTitleToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_subtitle'))?>'">
              <label class="pointer" for="showSubtitle1"><input type="radio" name="data[MWidgetSetting][show_subtitle]" ng-model="subTitleToggle" id="showSubtitle1" class="showHeader" value="1" >企業名を表示する</label><br>
              <label class="pointer" for="showSubtitle2"><input type="radio" name="data[MWidgetSetting][show_subtitle]" ng-model="subTitleToggle" id="showSubtitle2" class="showHeader" value="2" >企業名を表示しない</label><br>
              <?=$subTitle?>
            </div>
          </li>
          <?php if ($this->Form->isFieldError('sub_title')) echo $this->Form->error('sub_title', null, ['wrap' => 'li']); ?>
          <!-- 企業名 -->

          <!-- 説明文 -->
          <li>
            <span class='require'><label>説明文</label></span>
            <?php $description = $this->ngForm->input('description', [
              'type' => 'text',
              'placeholder' => '説明文',
              'ng-disabled' => 'descriptionToggle == "2"',
              'style' => 'margin:10px 0 10px 20px;',
              'div' => false,
              'class' => 'showHeader',
              'label' => false,
              'maxlength' => 15,
              'error' => false
            ],
            [
              'entity' => 'MWidgetSetting.description'
            ]) ?>
            <div ng-init="descriptionToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_description'))?>'">
              <label class="pointer" for="showDescription1"><input type="radio" class="showHeader" name="data[MWidgetSetting][show_description]" ng-model="descriptionToggle" id="showDescription1" value="1" >説明文を表示する</label><br><?=$description?><br>
              <label class="pointer" for="showDescription2"><input type="radio" class="showHeader" name="data[MWidgetSetting][show_description]" ng-model="descriptionToggle" id="showDescription2" value="2" >説明文を表示しない</label>
            </div>
          </li>
          <?php if ($this->Form->isFieldError('description')) echo $this->Form->error('description', null, ['wrap' => 'li']); ?>
          <!-- 説明文 -->

          <!-- メインカラー -->
          <li>
            <span class="require"><label>カラー</label></span>
            <div style="display: flex; flex-direction: column">
              <label>メイン</label>
              <?= $this->ngForm->input('main_color', [
                'type' => 'text',
                'placeholder' => 'メインカラー',
                'div' => false,
                'class' => 'jscolor {hash:true}',
                'label' => false,
                'maxlength' => 7,
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.main_color'
              ]) ?><br>
              <label>文字</label>
              <?= $this->ngForm->input('string_color', [
                'type' => 'text',
                'placeholder' => 'フォントカラー',
                'div' => false,
                'class' => 'jscolor {hash:true}',
                'label' => false,
                'maxlength' => 7,
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.string_color'
              ]) ?>
            </div>
          </li>
          <?php if ($this->Form->isFieldError('main_color')) echo $this->Form->error('main_color', null, ['wrap' => 'li']); ?>
          <?php if ($this->Form->isFieldError('string_color')) echo $this->Form->error('string_color', null, ['wrap' => 'li']); ?>
          <!-- メインカラー -->

          <!-- 画像の設定 -->
          <li>
            <span ng-class="{require: mainImageToggle=='1'}"><label>画像の設定</label></span>
            <div ng-init="mainImageToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_main_image'))?>'">
              <?= $this->Form->hidden('main_image') ?>
              <label class="pointer" for="showMainImage1"><input type="radio" name="data[MWidgetSetting][show_main_image]" ng-model="mainImageToggle" id="showMainImage1" value="1" >画像を表示する</label><br>
              <div id="imageSelectBtns" ng-class="{chooseImg: showChooseImg()}">

                <div id="picDiv">
                  <img ng-src="{{main_image}}" err-src="<?=C_PATH_WIDGET_GALLERY_IMG?>chat_sample_picture.png" ng-style="{'background-color': main_color}" width="62" height="70" alt="チャットに設定している画像">
                </div>
                <div id="picChooseDiv">
                  <div class="greenBtn btn-shadow" ng-click="showGallary()">ギャラリーから選択</div>
                  <div class="greenBtn btn-shadow" id="fileTagWrap"><?php echo $this->Form->file('uploadImage'); ?>画像をアップロード</div>

                </div>
              </div>
              <?php if ($this->Form->isFieldError('main_image')) echo $this->Form->error('main_image', null, ['ng-if'=>'mainImageToggle=="1"']); ?>
              <?php if ($this->Form->isFieldError('uploadImage')) echo $this->Form->error('uploadImage', null, ['ng-if'=>'mainImageToggle=="1"']); ?>
              <label class="pointer" for="showMainImage2"><input type="radio" name="data[MWidgetSetting][show_main_image]" ng-model="mainImageToggle" id="showMainImage2" value="2" >画像を表示しない</label>
            </div>
          </li>
          <!-- 画像の設定 -->

          <!-- 角の丸み -->
          <li>
          <span class="require"><label>角の丸み</label></span>
          <?= $this->ngForm->input('radius_ratio', [
            'type' => 'range',
            'step' => 1,
            'div' => false,
            'label' => false,
            'max' => 15,
            'min' => 1,
            'error' => false
          ],[
            'entity' => 'MWidgetSetting.radius_ratio'
          ]) ?>
          </li>
          <?php if ( $this->Form->isFieldError('radius_ratio') ) echo $this->Form->error('radius_ratio', null, ['wrap' => 'li']); ?>
          <!-- 角の丸み -->

          <!-- 背景の影 -->
          <li>
          <span class="require"><label>背景の影</label></span>
          <?= $this->ngForm->input('box_shadow', [
            'type' => 'range',
            'step' => 1,
            'div' => false,
            'label' => false,
            'max' => 10,
            'min' => 0,
            'error' => false
          ],[
            'entity' => 'MWidgetSetting.box_shadow'
          ]) ?>
          </li>
          <?php if ( $this->Form->isFieldError('box_shadow') ) echo $this->Form->error('box_shadow', null, ['wrap' => 'li']); ?>
          <!-- 背景の影 -->
        </ul>
      </section>

      <?php if($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])): ?>
      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．最小化／閉じる</h3>
      <section>
        <ul class="settingList">
          <!-- 最小化時のデザイン -->
          <li>
            <span class="require"><label>最小化時のデザイン</label></span>
            <div ng-init="minimizedDesignToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'minimize_design_type'))?>'">
              <label class="pointer choose" for="minimizedDesign1"><input type="radio" name="data[MWidgetSetting][minimize_design_type]" ng-model="minimizedDesignToggle" id="minimizedDesign1" class="showHeader" value="1" >シンプル表示しない</label><br>
              <label class="pointer choose" for="minimizedDesign2"><input type="radio" name="data[MWidgetSetting][minimize_design_type]" ng-model="minimizedDesignToggle" id="minimizedDesign2" class="showHeader" value="2" >スマホのみシンプル表示する</label><br>
              <label class="pointer choose" for="minimizedDesign3"><input type="radio" name="data[MWidgetSetting][minimize_design_type]" ng-model="minimizedDesignToggle" id="minimizedDesign3" class="showHeader" value="3" >すべての端末でシンプル表示する</label><br>
            </div>
          </li>
          <!-- 最小化時のデザイン -->
          <!-- 閉じるボタン -->
          <li>
            <span class="require"><label>閉じるボタン</label></span>
            <div ng-init="closeButtonSettingToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'close_button_setting'))?>'">
              <label class="pointer choose" for="closeButtonSetting1"><input type="radio" name="data[MWidgetSetting][close_button_setting]" ng-model="closeButtonSettingToggle" id="closeButtonSetting1" class="showHeader" value="1" >無効にする</label><br>
              <label class="pointer choose" for="closeButtonSetting2"><input type="radio" name="data[MWidgetSetting][close_button_setting]" ng-model="closeButtonSettingToggle" id="closeButtonSetting2" class="showHeader" value="2" >有効にする</label><br>
              <div id="closeButtonMode" ng-class="{chooseImg: showcloseButtonMode()}" style="padding: 10px 0 0 0;">
                <div ng-init="closeButtonModeTypeToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'close_button_mode_type'))?>'">
                  <label class="pointer choose" for="closeButtonModeType1" style="margin:10px 0 10px 20px;"><input type="radio" name="data[MWidgetSetting][close_button_mode_type]" ng-model="closeButtonModeTypeToggle" id="closeButtonModeType1" class="showHeader" value="1" ng-click="switchWidget(4)">小さなバナー表示</label><br>
                  <?= $this->ngForm->input('bannertext', [
                    'type' => 'text',
                    'placeholder' => 'バナーテキスト',
                    'ng-disabled' => 'closeButtonModeTypeToggle == "2"',
                    'style' => 'margin:10px 0 10px 40px;',
                    'div' => false,
                    'label' => false,
                    'maxlength' => 15,
                    'error' => false,
                    'ng-focus' => 'switchWidget(4)'
                  ],[
                    'entity' => 'MWidgetSetting.bannertext'
                  ]) ?><br>
                  <label class="pointer choose" for="closeButtonModeType2" style="margin:10px 0 10px 20px;"><input type="radio" name="data[MWidgetSetting][close_button_mode_type]" ng-model="closeButtonModeTypeToggle" id="closeButtonModeType2" class="showHeader" value="2" ng-click="switchWidget(4)">非表示</label><br>
                </div>
              </div>
            </div>
          </li>
          <!-- 閉じるボタン -->
        </ul>
      </section>
      <?php endif; ?>

      <?php if($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．チャット設定</h3>
      <section>
        <ul class="settingList">
          <!-- ラジオボタン操作時の動作種別 -->
          <li>
            <span class="require"><label>ラジオボタン選択動作</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('chat_radio_behavior', [
                'type' => 'radio',
                'options' => $widgetRadioBtnBehaviorType,
                'legend' => false,
                'separator' => '</label><br><label class="pointer">',
                'class' => 'showChat',
                'div' => false,
                'label' => false,
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.chat_radio_behavior'
              ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('chat_radio_behavior') ) echo $this->Form->error('chat_radio_behavior', null, ['wrap' => 'li']); ?>
          <!-- ラジオボタン操作時の動作種別 -->

          <!-- 消費者側送信アクション -->
          <li>
            <span class="require"><label>消費者側送信アクション</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('chat_trigger', [
                'type' => 'radio',
                'options' => $widgetSendActType,
                'legend' => false,
                'separator' => '</label><br><label class="pointer">',
                'class' => 'showChat',
                'div' => false,
                'label' => false,
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.chat_trigger'
              ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('chat_trigger') ) echo $this->Form->error('chat_trigger', null, ['wrap' => 'li']); ?>
          <!-- 消費者側送信アクション -->
          <!-- 担当者表示 -->
          <li>
            <span class="require"><label>担当者表示</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('show_name', [
                'type' => 'radio',
                'options' => $widgetShowNameType,
                'legend' => false,
                'separator' => '</label><br><label class="pointer">',
                'class' => 'showChat',
                'div' => false,
                'label' => false,
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.show_name'
              ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('show_name') ) echo $this->Form->error('show_name', null, ['wrap' => 'li']); ?>
          <!-- 担当者表示 -->
          <!-- 吹き出しデザイン -->
          <li>
            <span class="require"><label>吹き出しデザイン</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('chat_message_design_type', [
                  'type' => 'radio',
                  'options' => $chatMessageDesignType,
                  'legend' => false,
                  'separator' => '</label><br><label class="pointer">',
                  'class' => 'chatMessageDesignType',
                  'div' => false,
                  'label' => false,
                  'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.chat_message_design_type'
                  ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('chat_message_design_type') ) echo $this->Form->error('chat_message_design_type', null, ['wrap' => 'li']); ?>
          <!-- 吹き出しデザイン -->
          <!-- メッセージ表示時アニメーション -->
          <li>
            <span class="require"><label>メッセージ表示時アニメーション</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('chat_message_with_animation', [
                  'type' => 'checkbox',
                  'legend' => false,
                  'ng-checked' => 'chat_message_with_animation === "'.C_CHECK_ON.'"',
                  'div' => false,
                  'class' => 'chatMessageWithAnimation',
                  'label' => "アニメーションを有効にする",
                  'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.chat_message_with_animation'
                  ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('chat_message_with_animation') ) echo $this->Form->error('chat_message_with_animation', null, ['wrap' => 'li']); ?>
          <!-- メッセージ表示時アニメーション -->
        </ul>
      </section>

      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．スマートフォン個別設定</h3>
      <section>
        <ul class="settingList">
          <!-- ウィジェットの表示   -->
          <li>
            <span class="require"><label>ウィジェットの表示</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('sp_show_flg', [
                'type' => 'radio',
                'options' => $normalChoices,
                'legend' => false,
                'separator' => '</label><br><label class="pointer">',
                'class' => 'showSp',
                'div' => false,
                'label' => false,
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.sp_show_flg'
              ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('sp_show_flg') ) echo $this->Form->error('sp_show_flg', null, ['wrap' => 'li']); ?>
          <!-- ウィジェットの表示   -->

          <!-- シンプル表示 -->
          <li>
            <span class="require"><label>最大時のシンプル表示</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('sp_header_light_flg', [
                'type' => 'radio',
                'options' => $normalChoices,
                'ng-disabled' => 'sp_show_flg !== "'.C_SELECT_CAN.'"',
                'legend' => false,
                'separator' => '</label><br><label class="pointer">',
                'class' => 'showSp',
                'div' => false,
                'label' => false,
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.sp_header_light_flg'
              ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('sp_header_light_flg') ) echo $this->Form->error('sp_header_light_flg', null, ['wrap' => 'li']); ?>
          <!-- シンプル表示 -->

          <!-- 自動最大化の制御 -->
          <li>
            <span class="require"><label>自動最大化の制御</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('sp_auto_open_flg', [
                'type' => 'checkbox',
                'ng-disabled' => 'sp_show_flg !== "'.C_SELECT_CAN.'"',
                'legend' => false,
                'ng-checked' => 'sp_auto_open_flg === "'.C_CHECK_ON.'"',
                'separator' => '</label><br><label class="pointer">',
                'div' => false,
                'class' => 'showSp',
                'label' => "常に最大化しない",
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.sp_auto_open_flg'
              ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('sp_auto_open_flg') ) echo $this->Form->error('sp_auto_open_flg', null, ['wrap' => 'li']); ?>
          <!-- 自動最大化の制御 -->
        </ul>
      </section>
      <?php endif; ?>

      <?php if($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])): ?>
      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．電話ウィンドウ設定</h3>
      <section>
        <ul class="settingList">
          <!-- お問い合わせ先 -->
          <li>
            <span class="require"><label>お問い合わせ先</label></span>
            <?= $this->ngForm->input('tel', [
              'type' => 'text',
              'placeholder' => 'お問い合わせ先電話番号',
              'div' => false,
              'class' => 'showTel',
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


          <!-- 受付時間 -->
          <li>
            <span class="require"><label>受付時間</label></span>
            <?php $subTitle = $this->ngForm->input('time_text', [
              'type' => 'text',
              'placeholder' => '（例）9：00-18：00',
              'ng-disabled' => 'timeTextToggle == "2"',
              'div' => false,
              'style' => 'margin:10px 0 10px 20px;',
              'label' => false,
              'class' => 'showTel',
              'maxlength' => 15,
              'error' => false
            ],[
              'entity' => 'MWidgetSetting.time_text'
            ]) ?>
            <div ng-init="timeTextToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'display_time_flg'))?>'">
              <label class="pointer" for="showTimeText1"><input type="radio" name="data[MWidgetSetting][display_time_flg]" ng-model="timeTextToggle" class="showTel" id="showTimeText1" value="1" >受付時間を表示する</label><br><?=$subTitle?><br>
              <label class="pointer" for="showTimeText2"><input type="radio" name="data[MWidgetSetting][display_time_flg]" ng-model="timeTextToggle" class="showTel" id="showTimeText2" value="2" >受付時間を表示しない</label>
            </div>
          </li>
          <?php if ($this->Form->isFieldError('time_text')) echo $this->Form->error('time_text', null, ['wrap' => 'li']); ?>
          <!-- 受付時間 -->

          <!-- ウィジェット本文 -->
          <li>
            <span><label>ウィジェット本文</label></span>
            <?= $this->ngForm->input('content', [
              'type' => 'textarea',
              'placeholder' => '本文を１００文字以内で設定してください',
              'class' => 'showTel',
              'div' => false,
              'cols' => 40,
              'label' => false,
              'error' => false
            ],
            [
              'entity' => 'MWidgetSetting.content'
            ]) ?>
          </li>
          <?php if ($this->Form->isFieldError('content') ) echo $this->Form->error('content', null, ['wrap' => 'li']); ?>
          <!-- ウィジェット本文 -->
        </ul>
      </section>
      <?php endif; ?>

    </div>
    <?= $this->ngForm->input('widget.showTab', ['type' => 'hidden'], ['entity' => 'widget.showTab']) ?>
  <?= $this->Form->end(); ?>

  <!-- /* 操作 */ -->
  <section>
    <div id="m_widget_setting_action" class="fotterBtnArea">
      <?= $this->Html->link('更新', 'javascript:void(0)', ['ng-click' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
    </div>
  </section>
