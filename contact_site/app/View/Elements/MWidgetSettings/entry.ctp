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
        <!-- 表示設定 -->
        <li>
          <span class="require"><label>表示する条件</label></span>
          <pre><label><?= $this->Form->input('display_type', ['type' => 'radio',  'options' => $widgetDisplayType, 'legend' => false, 'separator' => '</label><br><label>', 'label' => false, 'error' => false, 'div' => false]) ?></label></pre>
        </li>
        <?php if ( $this->Form->isFieldError('display_type') ) echo $this->Form->error('display_type', null, ['wrap' => 'li']); ?>
        <!-- 表示設定 -->
        <!-- 最大化時間設定 -->
        <li>
          <span><label>最大化する条件</label></span>
          <div>
            <?php $maxShowTimeTagBySite = $this->Form->input('max_show_time', [
              'type' => 'number',
              'div' => false,
              'label' => false,
              'ng-disabled' => 'showTime !== "'.C_WIDGET_AUTO_OPEN_TYPE_SITE.'"',
              'before' => 'サイト訪問後',
              'after' => '秒後に自動で最大化する',
              'maxlength' => 2,
              'style' => 'width:5em',
              'max' => 60,
              'min' => 0,
              'error' => false
            ],[
              'entity' => 'MWidgetSetting.max_show_time'
            ]); ?>
            <?php $maxShowTimeTagByPage = $this->Form->input('max_show_time_page', [
              'type' => 'number',
              'div' => false,
              'label' => false,
              'ng-disabled' => 'showTime !== "'.C_WIDGET_AUTO_OPEN_TYPE_PAGE.'"',
              'before' => 'ページ訪問後',
              'after' => '秒後に自動で最大化する',
              'maxlength' => 2,
              'style' => 'width:5em',
              'max' => 60,
              'min' => 0,
              'error' => false
            ],[
              'entity' => 'MWidgetSetting.max_show_time_page'
            ]); ?>
            <div ng-init="showTime='<?=$this->formEx->val($this->data['MWidgetSetting'], 'show_time')?>'">
              <label for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_ON?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_ON?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_ON?>">常に自動で最大化する</label><br>
              <label for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>" ><?=$maxShowTimeTagBySite?></label><br>
              <label for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>" ><?=$maxShowTimeTagByPage?></label><br>
              <label for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_OFF?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_OFF?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_OFF?>">常に最大化しない</label>
            </div>
          </div>
        </li>
        <?php if ( $this->Form->isFieldError('max_show_time') ) echo $this->Form->error('max_show_time', null, ['wrap' => 'li']); ?>
        <?php if ( $this->Form->isFieldError('max_show_time_page') ) echo $this->Form->error('max_show_time_page', null, ['wrap' => 'li']); ?>
        <!-- 最大化時間設定 -->
        <!-- 表示位置 -->
        <li>
          <span class="require"><label>表示位置</label></span>
          <pre><label><?= $this->ngForm->input('show_position', [
              'type' => 'radio',
              'options' => $widgetPositionType,
              'legend' => false,
              'separator' => '</label><br><label>',
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
      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．ウィジェット詳細設定</h3>
      <section>
      <ul class="settingList">

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
          $subTitleClass = '';
          if($coreSettings[C_COMPANY_USE_CHAT]){
            $subTitleClass = 'showChat';
          }

          $subTitleOpt = [
              'type' => 'text',
              'placeholder' => '企業名',
              'div' => false,
              'style' => 'margin:10px 0 10px 20px;',
              'label' => false,
              'class' => $subTitleClass,
              'required' => false,
              'maxlength' => 15,
              'error' => false
          ];
          if($coreSettings[C_COMPANY_USE_SYNCLO] && !$coreSettings[C_COMPANY_USE_CHAT]) {
            $subTitleOpt['ng-disabled'] = 'subTitleToggle == "2"';
          }
          $subTitle = $this->ngForm->input('sub_title', $subTitleOpt, [
            'entity' => 'MWidgetSetting.sub_title'
          ]);
          ?>
          <div ng-init="subTitleToggle='<?=$this->formEx->val($this->data['MWidgetSetting'], 'show_subtitle')?>'">
            <label for="showSubtitle1"><input type="radio" name="data[MWidgetSetting][show_subtitle]" ng-model="subTitleToggle" id="showSubtitle1" value="1" >企業名を表示する</label><br>
            <label for="showSubtitle2"><input type="radio" name="data[MWidgetSetting][show_subtitle]" ng-model="subTitleToggle" id="showSubtitle2" value="2" >企業名を表示しない</label><br>
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
            'label' => false,
            'maxlength' => 15,
            'error' => false
          ],
          [
            'entity' => 'MWidgetSetting.description'
          ]) ?>
          <div ng-init="descriptionToggle='<?=$this->formEx->val($this->data['MWidgetSetting'], 'show_description')?>'">
            <label for="showDescription1"><input type="radio" name="data[MWidgetSetting][show_description]" ng-model="descriptionToggle" id="showDescription1" value="1" >説明文を表示する</label><br><?=$description?><br>
            <label for="showDescription2"><input type="radio" name="data[MWidgetSetting][show_description]" ng-model="descriptionToggle" id="showDescription2" value="2" >説明文を表示しない</label>
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
          <div ng-init="mainImageToggle='<?=$this->formEx->val($this->data['MWidgetSetting'], 'show_main_image')?>'">
            <?= $this->Form->hidden('main_image') ?>
            <label for="showMainImage1"><input type="radio" name="data[MWidgetSetting][show_main_image]" ng-model="mainImageToggle" id="showMainImage1" value="1" >画像を表示する</label><br>
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
            <label for="showMainImage2"><input type="radio" name="data[MWidgetSetting][show_main_image]" ng-model="mainImageToggle" id="showMainImage2" value="2" >画像を表示しない</label>
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
      </ul>
      </section>

      <?php if($coreSettings[C_COMPANY_USE_SYNCLO]): ?>
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
          <div ng-init="timeTextToggle='<?=$this->formEx->val($this->data['MWidgetSetting'], 'display_time_flg')?>'">
            <label for="showTimeText1"><input type="radio" name="data[MWidgetSetting][display_time_flg]" ng-model="timeTextToggle" class="showTel" id="showTimeText1" value="1" >受付時間を表示する</label><br><?=$subTitle?><br>
            <label for="showTimeText2"><input type="radio" name="data[MWidgetSetting][display_time_flg]" ng-model="timeTextToggle" class="showTel" id="showTimeText2" value="2" >受付時間を表示しない</label>
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

      <?php if($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．チャット設定</h3>
      <section>
      <ul class="settingList">
        <!-- チャット送信アクション -->
        <li>
          <span class="require"><label>消費者側送信アクション</label></span>
          <pre><label><?= $this->ngForm->input('chat_trigger', [
              'type' => 'radio',
              'options' => $widgetSendActType,
              'legend' => false,
              'separator' => '</label><br><label>',
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
        <!-- チャット送信アクション -->
        <!-- 担当者表示 -->
        <li>
          <span class="require"><label>担当者表示</label></span>
          <pre><label><?= $this->ngForm->input('show_name', [
              'type' => 'radio',
              'options' => $widgetShowNameType,
              'legend' => false,
              'separator' => '</label><br><label>',
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
