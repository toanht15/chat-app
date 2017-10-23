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
              <label class="pointer choose" for="widgetSizeType1"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" ng-click="clickWidgetSizeTypeToggle(1)" id="widgetSizeType1" class="showHeader" value="1" >小</label><br>
              <label class="pointer choose" for="widgetSizeType2"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" ng-click="clickWidgetSizeTypeToggle(2)" id="widgetSizeType2" class="showHeader" value="2" >中</label><br>
              <label class="pointer choose" for="widgetSizeType3"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" ng-click="clickWidgetSizeTypeToggle(3)" id="widgetSizeType3" class="showHeader" value="3" >大</label><br>
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
              'maxlength' => $titleLength_maxlength,
              'error' => false,
              'ng-maxlength' => "false"
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
                'maxlength' => $subTitleLength_maxlength,
                'error' => false,
                'ng-maxlength' => "false"
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
              <?=$subTitle?><br>
              <label class="pointer" for="showSubtitle2"><input type="radio" name="data[MWidgetSetting][show_subtitle]" ng-model="subTitleToggle" id="showSubtitle2" class="showHeader" value="2" >企業名を表示しない</label>
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
              'maxlength' => $descriptionLength_maxlength,
              'error' => false,
              'ng-maxlength' => "false"
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

          <!-- カラー -->
          <li>
            <span class="require"><label>カラー</label></span>
            <div style="display: flex; flex-direction: column; padding: 0 0 0 5px;">
              <!-- 基本設定色start -->
              <?php if($coreSettings[C_COMPANY_USE_CHAT]){ ?>
              <div style="height: 240px;">
              <?php }else{?>
              <div style="height: 100px;">
              <?php }?>
              <!-- 1.メインカラー -->
              <span style="height: 20px;"><label>メインカラー</label><?= $this->ngForm->input('main_color', [
                'type' => 'text',
                'placeholder' => 'メインカラー',
                'ng-change' => "changeMainColor()",
                'div' => false,
                'class' => 'jscolor {hash:true}',
                'label' => false,
                'maxlength' => 7,
                'style' => "width: 120px; position: relative; left: 75px !important; margin: -5px 0 0 0;",
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.main_color'
              ]) ?></span><br>
              <!-- 2.タイトル文字色 -->
              <span style="height: 20px;"><label>タイトル文字色</label><?= $this->ngForm->input('string_color', [
                'type' => 'text',
                'placeholder' => 'タイトルバー文字色',
                'ng-change' => "changeStringColor()",
                'div' => false,
                'class' => 'jscolor {hash:true}',
                'label' => false,
                'maxlength' => 7,
                'style' => 'width: 120px; position: relative; top: -23px; left: 142px;',
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.string_color'
              ]) ?></span><br>
              <!-- 3.吹き出し文字色 -->
<!--
              <span style="height: 20px;"><label>吹き出し文字色</label><?= $this->ngForm->input('message_text_color', [
                'type' => 'text',
                'placeholder' => '吹き出し文字色',
                'ng-change' => "changeMessageTextColor()",
                'div' => false,
                'class' => 'jscolor {hash:true}',
                'label' => false,
                'maxlength' => 7,
                'style' => "width: 120px; position: relative; left: 63px !important; margin: -5px 0 0 0;",
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.message_text_color'
              ]) ?></span><br>
 -->
              <!-- 4.その他文字色 -->
              <span style="height: 20px;"><label>ヘッダー文字色</label><?= $this->ngForm->input('other_text_color', [
                'type' => 'text',
                'placeholder' => 'その他文字色',
                'ng-change' => "changeOtherTextColor()",
                'div' => false,
                'class' => 'jscolor {hash:true}',
                'label' => false,
                'maxlength' => 7,
                'style' => "width: 120px; position: relative; left: 63px !important; margin: -5px 0 0 0;",
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.other_text_color'
              ]) ?></span><br>
              <?php if($coreSettings[C_COMPANY_USE_CHAT]){?>
              <div>
                <hr class="separator">
              <?php }else{?>
              <div style="display: none">
              <?php }?>
                <!-- 12.企業側吹き出し背景色 -->
                <span style="height: 35px;"><label>企業側吹き出し背景色　　</label><?= $this->ngForm->input('re_background_color', [
                  'type' => 'text',
                  'placeholder' => '企業側吹き出し背景色',
                  'div' => false,
                  'class' => 'jscolor {hash:true}',
                  'label' => false,
                  'maxlength' => 7,
                  'error' => false,
                  'style' => 'width: 120px; position: relative; left: 142px; top: -22px;'
                ],
                [
                  'entity' => 'MWidgetSetting.re_background_color'
                ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('re_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
                <!-- 11.企業側吹き出し文字色 -->
                <span style="height: 35px;"><label>企業側吹き出し文字色　　</label><?= $this->ngForm->input('re_text_color', [
                  'type' => 'text',
                  'placeholder' => '企業側吹き出し文字色',
                  'div' => false,
                  'class' => 'jscolor {hash:true}',
                  'label' => false,
                  'maxlength' => 7,
                  'error' => false,
                  'style' => 'width: 120px; position: relative; left: 142px; top: -22px;'
                ],
                [
                  'entity' => 'MWidgetSetting.re_text_color'
                ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('re_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
                <hr class="separator">
                <!-- 16.訪問者側吹き出し背景色 -->
                <span style="height: 35px;"><label>訪問者側吹き出し背景色</label><?= $this->ngForm->input('se_background_color', [
                  'type' => 'text',
                  'placeholder' => '訪問者側吹き出し背景色',
                  'div' => false,
                  'class' => 'jscolor {hash:true}',
                  'label' => false,
                  'maxlength' => 7,
                  'error' => false,
                  'style' => 'width: 120px; position: relative; left: 142px; top: -22px;'
                ],
                [
                  'entity' => 'MWidgetSetting.se_background_color'
                ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('se_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
                <!-- 15.訪問者側吹き出し文字色 -->
                <span style="height: 35px;"><label>訪問者側吹き出し文字色</label><?= $this->ngForm->input('se_text_color', [
                  'type' => 'text',
                  'placeholder' => '訪問者側吹き出し文字色',
                  'div' => false,
                  'class' => 'jscolor {hash:true}',
                  'label' => false,
                  'maxlength' => 7,
                  'error' => false,
                  'style' => 'width: 120px; position: relative; left: 142px; top: -22px;'
                ],
                [
                  'entity' => 'MWidgetSetting.se_text_color'
                ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('se_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
              </div>
              <!-- 5.ウィジェット枠線色 -->
<!--
              <span style="height: 20px;"><label>ウィジェット枠線色</label><?= $this->ngForm->input('widget_border_color', [
                'type' => 'text',
                'placeholder' => 'ウィジェット枠線色',
                'ng-change' => "changeWidgetBorderColor()",
                'div' => false,
                'class' => 'jscolor {hash:true}',
                'label' => false,
                'maxlength' => 7,
                'style' => 'width: 120px; position: relative; top: -23px; left: 142px;',
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.widget_border_color'
              ]) ?></span><br>
 -->
              <!-- 6.吹き出し枠線色 -->
<!--
              <span style="height: 20px;"><label>吹き出し枠線色</label><?= $this->ngForm->input('chat_talk_border_color', [
                'type' => 'text',
                'placeholder' => '吹き出し枠線色',
                'ng-change' => "changeChatTalkBorderColor()",
                'div' => false,
                'class' => 'jscolor {hash:true}',
                'label' => false,
                'maxlength' => 7,
                'style' => "width: 120px; position: relative; left: 63px !important; margin: -5px 0 0 0;",
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.chat_talk_border_color'
              ]) ?></span><br>
 -->
              </div>
              <!-- 基本設定色end -->
              <!-- 0.通常設定・高度設定 -->
              <!-- 高度な設定を行う行わないを制御するチェックボックス -->
              <pre style="margin-top: 30px; margin-left: -3px;"><hr class="separator" style="margin: 5px 0 5px 0;"><label class="pointer"><?= $this->ngForm->input('color_setting_type', [
                'type' => 'checkbox',
                'legend' => false,
                'ng-checked' => 'color_setting_type === "'.COLOR_SETTING_TYPE_ON.'"',
                'div' => false,
                'label' => "高度な設定を行う",
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.color_setting_type'
              ]) ?></label></pre>

              <!-- 高度な設定を行う制御 start-->
              <div id="color_setting_details" ng-class="{chooseImg: showColorSettingDetails()}" style="margin:display: none;">
                <!-- ヘッダー部start -->
                <div style=" background-color: #ECF4DA; cursor: pointer; border-color: #C3D69B; border-style: solid; border-width: 1px 0 1px 0; font-weight: bold; padding: 5px 0 5px 10px; width: 396px !important;">ヘッダー部</div><br>
                <div style=" position: relative; top: -10px; left: 10px;">
                <!-- 7.企業名文字色 -->
                <span style="height: 35px;"><label>企業名文字色</label><?= $this->ngForm->input('sub_title_text_color', [
                  'type' => 'text',
                  'placeholder' => '企業名担当者名文字色',
                  'div' => false,
                  'class' => 'jscolor {hash:true}',
                  'label' => false,
                  'maxlength' => 7,
                  'error' => false,
                  'style' => 'width: 120px; position: relative; left: 70px;'
                ],
                [
                  'entity' => 'MWidgetSetting.sub_title_text_color'
                ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('sub_title_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -28px; left: 285px;" >標準に戻す</span></span>
                <!-- 8.説明文文字色 -->
                <span style="height: 35px;"><label>説明文文字色</label><?= $this->ngForm->input('description_text_color', [
                  'type' => 'text',
                  'placeholder' => '企業側吹き出し文字色',
                  'div' => false,
                  'class' => 'jscolor {hash:true}',
                  'label' => false,
                  'maxlength' => 7,
                  'error' => false,
                  'style' => 'width: 120px; position: relative; left: 70px;'
                ],
                [
                  'entity' => 'MWidgetSetting.description_text_color'
                ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('description_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -28px; left: 285px;" >標準に戻す</span></span>
                </div>
                <!-- ヘッダー部end -->

                <?php if($coreSettings[C_COMPANY_USE_CHAT]){ ?>
                  <div>
                <?php }else{?>
                  <div style="display: none">
                <?php }?>
                  <!-- チャットエリア部start -->
                  <div style=" background-color: #ECF4DA; cursor: pointer; border-color: #C3D69B; border-style: solid; border-width: 1px 0 1px 0; font-weight: bold; padding: 5px 0 5px 10px; width: 396px !important;">チャットエリア部</div><br>
                  <div style=" position: relative; top: 0px; left: 10px;">
                  <!-- 9.チャットエリア背景色 -->
                  <span style="height: 35px;"><label>チャットエリア背景色　　</label><?= $this->ngForm->input('chat_talk_background_color', [
                    'type' => 'text',
                    'placeholder' => '企業側吹き出し背景色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.chat_talk_background_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('chat_talk_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <hr class="separator indent">
                  <!-- 10.企業名担当者名文字色 -->
                  <span style="height: 35px;"><label>企業名／担当者名文字色</label><?= $this->ngForm->input('c_name_text_color', [
                    'type' => 'text',
                    'placeholder' => '企業名担当者名文字色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.c_name_text_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('c_name_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <!-- 11.企業側吹き出し文字色 -->
  <!--
                  <span style="height: 35px;"><label>企業側吹き出し文字色　　</label><?= $this->ngForm->input('re_text_color', [
                    'type' => 'text',
                    'placeholder' => '企業側吹き出し文字色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.re_text_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('re_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
   -->
                  <!-- 12.企業側吹き出し背景色 -->
  <!--
                  <span style="height: 35px;"><label>企業側吹き出し背景色　　</label><?= $this->ngForm->input('re_background_color', [
                    'type' => 'text',
                    'placeholder' => '企業側吹き出し背景色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.re_background_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('re_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
   -->
                  <!-- 13.企業側吹き出し枠線色 -->
                  <span style="height: 35px;"><label>企業側吹き出し枠線色</label>
                  <?php if($re_border_color_flg){?>
                    <?= $this->ngForm->input('re_border_color', [
                      'type' => 'text',
                      'ng-click' => "changeReBorderColor()",
                      'placeholder' => '企業側吹き出し枠線色',
                      'div' => false,
                      'class' => ('jscolor {hash:true}'),
                      'label' => false,
                      'maxlength' => 7,
                      'error' => false,
                      'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                    ],
                    [
                      'entity' => 'MWidgetSetting.re_border_color'
                    ]) ?>
                  <?php }else{?>
                    <?= $this->ngForm->input('re_border_color', [
                      'type' => 'text',
                      'ng-click' => "changeReBorderColor()",
                      'placeholder' => '企業側吹き出し枠線色',
                      'div' => false,
                      'label' => false,
                      'maxlength' => 7,
                      'error' => false,
                      'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                    ],
                    [
                      'entity' => 'MWidgetSetting.re_border_color'
                    ]) ?>
                  <?php }?>
                  <span class="greenBtn btn-shadow" ng-click="returnStandardColor('re_border_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <!-- 14.企業側吹き出し枠線なし -->
                  <pre><label class="pointer" style="position: relative; left: 141px; top: -5px;"><?= $this->ngForm->input('re_border_none', [
                    'type' => 'checkbox',
                    'legend' => false,
                    'ng-checked' => 're_border_color === "なし"',
                    'div' => false,
                    'label' => "枠線なしにする",
                    'error' => false
                  ],
                  [
                    'entity' => 'MWidgetSetting.re_border_none'
                  ]) ?></label></pre>
                  <hr class="separator indent">
                  <!-- 15.訪問者側吹き出し文字色 -->
  <!--
                  <span style="height: 35px;"><label>訪問者側吹き出し文字色</label><?= $this->ngForm->input('se_text_color', [
                    'type' => 'text',
                    'placeholder' => '訪問者側吹き出し文字色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.se_text_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('se_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
   -->
                  <!-- 16.訪問者側吹き出し背景色 -->
  <!--
                  <span style="height: 35px;"><label>訪問者側吹き出し背景色</label><?= $this->ngForm->input('se_background_color', [
                    'type' => 'text',
                    'placeholder' => '訪問者側吹き出し背景色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.se_background_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('se_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
   -->
                  <!-- 17.訪問者側吹き出し枠線色 -->
                  <span style="height: 35px;"><label>訪問者側吹き出し枠線色</label>
                  <?php if($se_border_color_flg){?>
                    <?= $this->ngForm->input('se_border_color', [
                      'type' => 'text',
                      'ng-click' => "changeSeBorderColor()",
                      'placeholder' => '訪問者側吹き出し枠線色',
                      'div' => false,
                      'class' => 'jscolor {hash:true}',
                      'label' => false,
                      'maxlength' => 7,
                      'error' => false,
                      'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                    ],
                    [
                      'entity' => 'MWidgetSetting.se_border_color'
                    ]) ?>
                  <?php }else{?>
                    <?= $this->ngForm->input('se_border_color', [
                      'type' => 'text',
                      'ng-click' => "changeSeBorderColor()",
                      'placeholder' => '訪問者側吹き出し枠線色',
                      'div' => false,
                      'label' => false,
                      'maxlength' => 7,
                      'error' => false,
                      'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                    ],
                    [
                      'entity' => 'MWidgetSetting.se_border_color'
                    ]) ?>
                  <?php }?>
                  <span class="greenBtn btn-shadow" ng-click="returnStandardColor('se_border_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <!-- 18.訪問者側吹き出し枠線なし -->
                  <pre><label class="pointer" style="position: relative; left: 141px; top: -5px;"><?= $this->ngForm->input('se_border_none', [
                    'type' => 'checkbox',
                    'legend' => false,
                    'ng-checked' => 'se_border_color === "なし"',
                    'div' => false,
                    'label' => "枠線なしにする",
                    'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.se_border_none'
                  ]) ?></label></pre>

                  </div>
                  <!-- チャットエリア部end -->

                  <!-- メッセージエリア部start -->
                  <div style=" background-color: #ECF4DA; cursor: pointer; border-color: #C3D69B; border-style: solid; border-width: 1px 0 1px 0; font-weight: bold; padding: 5px 0 5px 10px; width: 396px !important;">メッセージエリア部</div>
                  <div style=" position: relative; top: 15px; left: 10px;">
                  <!-- 19.メッセージエリア背景色 -->
                  <span style="height: 35px;"><label>メッセージエリア背景色</label><?= $this->ngForm->input('chat_message_background_color', [
                    'type' => 'text',
                    'placeholder' => 'メッセージエリア背景色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.chat_message_background_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('chat_message_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <hr class="separator indent">
                  <!-- 21.メッセージBOX背景色 -->
                  <span style="height: 35px;"><label>メッセージBOX背景色　</label><?= $this->ngForm->input('message_box_background_color', [
                    'type' => 'text',
                    'placeholder' => 'メッセージBOX背景色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.message_box_background_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('message_box_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <!-- 20.メッセージBOX文字色 -->
                  <span style="height: 35px;"><label>メッセージBOX文字色　</label><?= $this->ngForm->input('message_box_text_color', [
                    'type' => 'text',
                    'placeholder' => 'メッセージBOX文字色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.message_box_text_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('message_box_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <!-- 22.メッセージBOX枠線色 -->
                  <span style="height: 35px;"><label>メッセージBOX枠線色</label>
                  <?php if($message_box_border_color_flg){?>
                    <?= $this->ngForm->input('message_box_border_color', [
                      'type' => 'text',
                      'ng-click' => "changeMessageBoxBorderColor()",
                      'placeholder' => 'メッセージBOX枠線色',
                      'div' => false,
                      'class' => 'jscolor {hash:true}',
                      'label' => false,
                      'maxlength' => 7,
                      'error' => false,
                      'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                    ],
                    [
                      'entity' => 'MWidgetSetting.message_box_border_color'
                    ]) ?>
                  <?php }else{?>
                    <?= $this->ngForm->input('message_box_border_color', [
                      'type' => 'text',
                      'ng-click' => "changeMessageBoxBorderColor()",
                      'placeholder' => 'メッセージBOX枠線色',
                      'div' => false,
                      'label' => false,
                      'maxlength' => 7,
                      'error' => false,
                      'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                    ],
                    [
                      'entity' => 'MWidgetSetting.message_box_border_color'
                    ]) ?>
                  <?php }?>
                  <span class="greenBtn btn-shadow" ng-click="returnStandardColor('message_box_border_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <!-- 23.メッセージBOX枠線なし -->
                  <pre><label class="pointer" style="position: relative; left: 141px; top: -5px;"><?= $this->ngForm->input('message_box_border_none', [
                    'type' => 'checkbox',
                    'legend' => false,
                    'ng-checked' => 'message_box_border_color === "なし"',
                    'div' => false,
                    'label' => "枠線なしにする",
                    'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.message_box_border_none'
                  ]) ?></label></pre>
                  <hr class="separator indent">
                  <!-- 25.送信ボタン背景色 -->
                  <span style="height: 35px;"><label>送信ボタン背景色</label><?= $this->ngForm->input('chat_send_btn_background_color', [
                    'type' => 'text',
                    'placeholder' => '送信ボタン背景色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.chat_send_btn_background_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('chat_send_btn_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  <!-- 24.送信ボタン文字色 -->
                  <span style="height: 35px;"><label>送信ボタン文字色</label><?= $this->ngForm->input('chat_send_btn_text_color', [
                    'type' => 'text',
                    'placeholder' => '送信ボタン文字色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.chat_send_btn_text_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('chat_send_btn_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                  </div>
                  <!-- メッセージエリア部end -->
                </div>
                <!-- その他部start -->
                <div style=" background-color: #ECF4DA; cursor: pointer; border-color: #C3D69B; border-style: solid; border-width: 1px 0 1px 0; font-weight: bold; padding: 5px 0 5px 10px; width: 396px !important; position: relative;top: 20px;">その他</div>
                <div style=" position: relative; top: 30px; left: 10px;">
                <!-- 5.ウィジェット枠線色 -->
                <span style="height: 35px;"><label>ウィジェット外枠線色</label>
                <?php if($widget_border_color_flg){?>
                  <?= $this->ngForm->input('widget_border_color', [
                    'type' => 'text',
                    'ng-click' => "changeWidgetBorderColor()",
                    'placeholder' => 'ウィジェット外枠線色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.widget_border_color'
                  ]) ?>
                <?php }else{?>
                  <?= $this->ngForm->input('widget_border_color', [
                    'type' => 'text',
                    'ng-click' => "changeWidgetBorderColor()",
                    'placeholder' => 'ウィジェット外枠線色',
                    'div' => false,
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.widget_border_color'
                  ]) ?>
                <?php }?>
                <span class="greenBtn btn-shadow" ng-click="returnStandardColor('widget_border_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px ; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                <!-- 28.ウィジット外枠線なし -->
                <pre><label class="pointer" style="position: relative; left: 141px; top: -5px;"><?= $this->ngForm->input('widget_outside_border_none', [
                  'type' => 'checkbox',
                  'legend' => false,
                  'ng-checked' => 'widget_border_color === "なし"',
                  'div' => false,
                  'label' => "枠線なしにする",
                  'error' => false
              ],
                [
                  'entity' => 'MWidgetSetting.widget_outside_border_none'
                ]) ?></label></pre>
                <!-- 26.ウィジット内枠線色 -->
                <span style="height: 60px;"><label>ウィジェット内枠線色</label>
                <?php if($widget_inside_border_color_flg){?>
                  <?= $this->ngForm->input('widget_inside_border_color', [
                    'type' => 'text',
                    'ng-click' => "changeWidgetInsideBorderColor()",
                    'placeholder' => 'ウィジェット内枠線色',
                    'div' => false,
                    'class' => 'jscolor {hash:true}',
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.widget_inside_border_color'
                  ]) ?>
                <?php }else{?>
                  <?= $this->ngForm->input('widget_inside_border_color', [
                    'type' => 'text',
                    'ng-click' => "changeWidgetInsideBorderColor()",
                    'placeholder' => 'ウィジェット内枠線色',
                    'div' => false,
                    'label' => false,
                    'maxlength' => 7,
                    'error' => false,
                    'style' => 'width: 120px; position: relative; left: 140px; top: -22px;'
                  ],
                  [
                    'entity' => 'MWidgetSetting.widget_inside_border_color'
                  ]) ?>
                <?php }?>
                <span class="greenBtn btn-shadow" ng-click="returnStandardColor('widget_inside_border_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px ; font-size: 0.9em; position: relative; top: -50px; left: 285px;" >標準に戻す</span></span>
                <!-- 27.ウィジット内枠線なし -->
                <pre><label class="pointer" style="position: relative; left: 141px; top: -30px;"><?= $this->ngForm->input('widget_inside_border_none', [
                  'type' => 'checkbox',
                  'legend' => false,
                  'ng-checked' => 'widget_inside_border_color === "なし"',
                  'div' => false,
                  'label' => "枠線なしにする",
                  'error' => false
              ],
                [
                  'entity' => 'MWidgetSetting.widget_inside_border_none'
                ]) ?></label></pre>
                <!-- その他部end -->
                </div>
              </div>

          <!-- 高度な設定を行う制御 end-->
            </div>
          </li>
          <?php if ($this->Form->isFieldError('main_color')) echo $this->Form->error('main_color', null, ['wrap' => 'li']); ?>
          <?php if ($this->Form->isFieldError('string_color')) echo $this->Form->error('string_color', null, ['wrap' => 'li']); ?>
          <?php if ($this->Form->isFieldError('message_text_color')) echo $this->Form->error('message_text_color', null, ['wrap' => 'li']); ?>
          <?php if ($this->Form->isFieldError('other_text_color')) echo $this->Form->error('other_text_color', null, ['wrap' => 'li']); ?>
          <?php if ($this->Form->isFieldError('widget_border_color')) echo $this->Form->error('widget_border_color', null, ['wrap' => 'li']); ?>
          <?php if ($this->Form->isFieldError('chat_talk_border_color')) echo $this->Form->error('chat_talk_border_color', null, ['wrap' => 'li']); ?>
          <?php if ( $this->Form->isFieldError('color_setting_type') ) echo $this->Form->error('color_setting_type', null, ['wrap' => 'li']); ?>
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

      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．最小化／閉じる</h3>
      <section>
        <ul class="settingList">
          <!-- 最小化時のデザイン -->
          <li>
            <span class="require"><label>最小化時のデザイン</label></span>
            <div ng-init="minimizedDesignToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'minimize_design_type'))?>'">
              <label class="pointer choose" for="minimizedDesign1"><input type="radio" name="data[MWidgetSetting][minimize_design_type]" ng-model="minimizedDesignToggle" id="minimizedDesign1" ng-click="clickMinimizedDesignToggle(1)" class="showHeader" value="1" >シンプル表示しない</label><br>
              <label class="pointer choose" for="minimizedDesign2"><input type="radio" name="data[MWidgetSetting][minimize_design_type]" ng-model="minimizedDesignToggle" id="minimizedDesign2" ng-click="clickMinimizedDesignToggle(3)" class="showHeader" value="2" >スマホのみシンプル表示する</label><br>
              <label class="pointer choose" for="minimizedDesign3"><input type="radio" name="data[MWidgetSetting][minimize_design_type]" ng-model="minimizedDesignToggle" id="minimizedDesign3" ng-click="clickMinimizedDesignToggle(1)" class="showHeader" value="3" >すべての端末でシンプル表示する</label><br>
            </div>
          </li>
          <!-- 最小化時のデザイン -->
          <!-- 閉じるボタン -->
          <li>
            <span class="require"><label>閉じるボタン</label></span>
            <div ng-init="closeButtonSettingToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'close_button_setting'))?>'">
              <label class="pointer choose" for="closeButtonSetting1"><input type="radio" name="data[MWidgetSetting][close_button_setting]" ng-model="closeButtonSettingToggle" ng-click="clickMinimizedDesignToggle(1)" id="closeButtonSetting1" class="showHeader" value="1" >無効にする</label><br>
              <label class="pointer choose" for="closeButtonSetting2"><input type="radio" name="data[MWidgetSetting][close_button_setting]" ng-model="closeButtonSettingToggle" ng-click="clickMinimizedDesignToggle(4)" id="closeButtonSetting2" class="showHeader" value="2" >有効にする</label><br>
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
                    'ng-focus' => 'switchWidget(4)',
                    'ng-maxlength' => "false"
                  ],[
                    'entity' => 'MWidgetSetting.bannertext'
                  ]) ?><br>
                  <label class="pointer choose" for="closeButtonModeType2" style="margin:10px 0 10px 20px;"><input type="radio" name="data[MWidgetSetting][close_button_mode_type]" ng-model="closeButtonModeTypeToggle" id="closeButtonModeType2" class="showHeader" value="2" ng-click="switchWidget(4)">非表示<br><s style="margin:0px 0px 0px 3.2em; display: inline-block;">※再アクセス時までウィジェットが表示されなくなります</s></label>
                </div>
              </div>
            </div>
          </li>
          <!-- 閉じるボタン -->
        </ul>
      </section>

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
          <!-- チャット本文コピー -->
          <li>
            <span class="require"><label>チャット本文コピー</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('chat_message_copy', [
                  'type' => 'checkbox',
                  'legend' => false,
                  'class' => 'chatMessageCopy',
                  'ng-checked' => 'chat_message_copy === "'.C_WIDGET_CHAT_MESSAGE_CANT_COPY.'"',
                  'label' => 'サイト訪問者によるチャット本文のコピーを出来なくする',
                  'div' => false,
                  'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.chat_message_copy'
                  ])
                   ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('chat_message_copy') ) echo $this->Form->error('chat_message_copy', null, ['wrap' => 'li']); ?>
          <!-- チャット本文コピー -->
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
      <?= $this->Html->link('元に戻す', 'javascript:window.location.reload()', ['class' => 'whiteBtn btn-shadow']) ?>
      <?= $this->Html->link('更新', 'javascript:void(0)', ['ng-click' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
      <?= $this->Html->link('dummy', 'javascript:void(0)', ['ng-click' => '', 'class' => 'whiteBtn btn-shadow', 'style' => 'visibility: hidden;']) ?>
    </div>
  </section>
