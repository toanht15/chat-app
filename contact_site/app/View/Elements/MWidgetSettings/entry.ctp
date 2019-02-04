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
            <pre><label class="pointer"><?= $this->Form->input('display_type', ['type' => 'radio',  'options' => $widgetDisplayType, 'legend' => false, 'separator' => '</label><br><label class="pointer">', 'label' => false, 'error' => false, 'div' => false]) ?></label>
            </pre>
          </li>
          <?php if ( $this->Form->isFieldError('display_type') ) echo $this->Form->error('display_type', null, ['wrap' => 'li']); ?>
          <!-- 初期表示時のスタイル -->
          <li>
            <span class="require"><label>初期表示時のスタイル</label></span>
            <div ng-init="widgetDisplayTypeToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'display_style_type'))?>'">
              <label class="pointer choose" for="displayStyleType1"><input type="radio" name="data[MWidgetSetting][display_style_type]" ng-model="widgetDisplayTypeToggle" ng-click="showNormalMaximized()" id="displayStyleType1" class="showHeader" value="1" ><?= $widgetDisplayStyleType[1] ?></label><br>
              <label class="pointer choose ignore-click-event" for="displayStyleType2"><input type="radio" name="data[MWidgetSetting][display_style_type]" ng-model="widgetDisplayTypeToggle" ng-click="showNormalMinimized()" id="displayStyleType2" class="showHeader" value="2" ><?= $widgetDisplayStyleType[2] ?></label><br>
              <label class="pointer choose" for="displayStyleType3"><input type="radio" name="data[MWidgetSetting][display_style_type]" ng-model="widgetDisplayTypeToggle" ng-click="switchWidget(4)" id="displayStyleType3" class="showHeader" value="3" ><?= $widgetDisplayStyleType[3] ?></label><br>
            </div>
          </li>
          <!-- 表示するタイミング -->
          <li>
            <span class="require"><label>初期表示するタイミング</label></span>
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
          <!-- 最大化時間設定 -->
          <li>
            <span><label>自動で最大化する条件</label></span>
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
                <label class="pointer padding" for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_SITE?>" ><?=$maxShowTimeTagBySite?></label><br>
                <label class="pointer padding" for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_PAGE?>" ><?=$maxShowTimeTagByPage?></label><br>
                <label class="pointer padding" for="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_NONE?>"><input type="radio" name="data[MWidgetSetting][show_time]" ng-model="showTime" id="showTime<?=C_WIDGET_AUTO_OPEN_TYPE_NONE?>" value="<?=C_WIDGET_AUTO_OPEN_TYPE_NONE?>">自動で最大化しない</label>
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
          <!-- Web接客コード表示 -->
          <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && !$coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
          <li>
            <span class="require"><label>ウェブ接客コード表示</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('show_access_id', [
                  'type' => 'radio',
                  'options' => $widgetShowAccessId,
                  'legend' => false,
                  'separator' => '</label><br><label class="pointer">',
                  'div' => false,
                  'label' => false,
                  'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.show_access_id'
                  ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('show_access_id') ) echo $this->Form->error('show_access_id', null, ['wrap' => 'li']); ?>
          <?php else :?>
          <?= $this->ngForm->input('show_access_id', [
              'type' => 'hidden',
              'legend' => false,
              'div' => false,
              'label' => false,
              'error' => false
            ]) ?>
          <?php endif; ?>
          <!-- Web接客コード表示 -->
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
              <?php if($coreSettings[C_COMPANY_USE_CHAT]): ?>
              <label class="pointer choose" for="widgetSizeType4"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" ng-click="clickWidgetSizeTypeToggle(4)" id="widgetSizeType4" class="showHeader" value="4" >最大</label><br>
              <?php endif; ?>
              <div style="display:grid; grid-template-columns:80px 1fr;">
                <div>
                  <?php if($coreSettings[C_COMPANY_USE_CUSTOM_WIDGET_SIZE]): ?>
                 <label class="pointer choose" for="widgetSizeType5"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" ng-click="clickWidgetSizeTypeToggle(5)" id="widgetSizeType5" class="showHeader" value="5" >カスタム</label><br>
                  <?php else: ?>
                  <label class="pointer choose commontooltip" for="widgetSizeType5" style="color: rgb(204, 204, 204);" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。"><input type="radio" name="data[MWidgetSetting][widget_size_type]" ng-model="widgetSizeTypeToggle" id="MWidgetSettingChatInitShowTextarea2" id="widgetSizeType5" class="showHeader" value="" disabled>カスタム</label>
                  <?php endif; ?>
                </div>
                <div ng-show="widgetSizeTypeToggle == 5" style="margin-left: 10px"  >
                  <span style="display: flex; width: 19em;">
                    <label style=" flex-basis:154px; ">ウィジェット横幅</label>
                    <?= $this->ngForm->input('widget_custom_width', [
                      'type' => 'number',
                      'class' => 'showNormal',
                      'min' => '285',
                      'div' => false,
                      'label' => false,
                      'maxlength' => 7,
                      'style' => "width: 100px; padding-left: 20px !important; margin: -5px 0 0 0;",
                      'error' => false,
                      'string-to-number' => true,
                      'ng-max' => "false"
                    ],
                      [
                        'entity' => 'MWidgetSetting.widget_custom_width'
                      ])
                    ?><span style="display:inline-block; width:auto; padding-top: 0px; align-self: flex-start;">px</span>
                  </span>
                  <span style="display: flex; width: 19em;">
                    <label style=" flex-basis:154px; ">ウィジェット高さ</label>
                    <?= $this->ngForm->input('widget_custom_height', [
                      'type' => 'number',
                      'class' => 'showNormal',
                      'min' => '194',
                      'div' => false,
                      'label' => false,
                      'maxlength' => 7,
                      'style' => "width: 100px; padding-left: 20px !important; margin: -5px 0 0 0;",
                      'error' => false,
                      'string-to-number' => true,
                      'ng-max' => "false",
                      'ng-change' => "resetElementCustomHeight()"
                    ],
                      [
                        'entity' => 'MWidgetSetting.widget_custom_height'
                      ])
                    ?><span style="display:inline-block; width:auto; padding-top: 0px; align-self: flex-start;">px</span>
                  </span>
                </div>
              </div>
              <s style="display: inline-block; margin-top: 5px; padding-left: 16px; text-indent: -1em;">※指定したウィジェットの縦幅よりもウィンドウサイズの縦幅が小さい場合、<br>
                ウィジェットの縦幅は自動的に縮小されます（レスポンシブ対応）</s>
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
            <div ng-init="widget_title_top_type ='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'widget_title_top_type'))?>'" style = "margin-top: 37px; margin-left: -275px;">
              <label class="pointer choose" for="widgetTitleTopType1"><input type="radio" name="data[MWidgetSetting][widget_title_top_type]" ng-model="widget_title_top_type" id="widgetTitleTopType1" class="showHeader" value="1" >左寄せ</label><br>
              <label class="pointer choose" for="widgetTitleTopType2"><input type="radio" name="data[MWidgetSetting][widget_title_top_type]" ng-model="widget_title_top_type" id="widgetTitleTopType2" class="showHeader" value="2">中央寄せ</label><br>
            </div>
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
                'ng-maxlength' => "false",
                'ng-change' => "changeSubtitle()"
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
              <?=$subTitle?>
              <div ng-init="widget_title_name_type ='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'widget_title_name_type'))?>'" style = "margin-top: 0px; margin-left: 20px;" id = "widgetTitleNameType">
                <?php
                  if($this->data['MWidgetSetting']['show_subtitle'] == 1) {
                    $display = 'block';
                  }
                  else if($this->data['MWidgetSetting']['show_subtitle'] == 2) {
                    $display = 'none';
                  }
                ?>
                <label  class="pointer choose" for="widgetTitleNameType1" id = "widgetTitleNameTypeLabel1" style = "display:<?=$display?>"><input type="radio" name="data[MWidgetSetting][widget_title_name_type]" ng-model="widget_title_name_type" id="widgetTitleNameType1" class="showHeader" value="1" >左寄せ<br></label>
                <label  class="pointer choose" for="widgetTitleNameType2" id = "widgetTitleNameTypeLabel2" style = "display:<?=$display?>"><input type="radio" name="data[MWidgetSetting][widget_title_name_type]" ng-model="widget_title_name_type" id="widgetTitleNameType2" class="showHeader" value="2">中央寄せ<br></label>
              </div>
            <br>
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
              'ng-maxlength' => "false",
              'ng-change' => "changeDescription()"
            ],
            [
              'entity' => 'MWidgetSetting.description'
            ]) ?>
            <div ng-init="descriptionToggle ='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_description'))?>'">
              <label class="pointer" for="showDescription1"><input type="radio" class="showHeader" name="data[MWidgetSetting][show_description]" ng-model="descriptionToggle" id="showDescription1" value="1" ng-change="changeDescription()" >説明文を表示する</label><br><?=$description?>
                <div ng-init="widget_title_explain_type ='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'widget_title_explain_type'))?>'" style = "margin-top: 0px; margin-left: 20px;" id = "widgetTitleExplainType">
                  <?php
                  if($this->data['MWidgetSetting']['show_description'] == 1) {
                    $display = 'block';
                  }
                  else if($this->data['MWidgetSetting']['show_description'] == 2) {
                    $display = 'none';
                  }
                  ?>
                  <label class="pointer choose" for="widgetTitleExplainType1" id = "widgetTitleExplainTypeLabel1" style = "display:<?=$display?>"><input type="radio" name="data[MWidgetSetting][widget_title_explain_type]" ng-model="widget_title_explain_type" id="widgetTitleExplainType1" class="showHeader" value="1" >左寄せ<br></label>
                  <label class="pointer choose" for="widgetTitleExplainType2" id = "widgetTitleExplainTypeLabel2" style = "display:<?=$display?>"><input type="radio" name="data[MWidgetSetting][widget_title_explain_type]" ng-model="widget_title_explain_type" id="widgetTitleExplainType2" class="showHeader" value="2">中央寄せ<br></label>
                </div>
              <br>
              <label class="pointer" for="showDescription2"><input type="radio" class="showHeader" name="data[MWidgetSetting][show_description]" ng-model="descriptionToggle" id="showDescription2" value="2" ng-change="changeDescription()" >説明文を表示しない</label>
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
                'class' => 'jscolor {hash:true} ignore-click-event',
                'label' => false,
                'maxlength' => 7,
                'style' => "width: 120px; position: relative; left: 88px !important; margin: -5px 0 0 0;",
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
                'class' => 'jscolor {hash:true} ignore-click-event',
                'label' => false,
                'maxlength' => 7,
                'style' => 'width: 120px; position: relative; top: -23px; left: 155px;',
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
                'style' => "width: 120px; position: relative; left: 75px !important; margin: -5px 0 0 0;",
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
                'style' => "width: 120px; position: relative; left: 75px !important; margin: -5px 0 0 0;",
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.other_text_color'
              ]) ?></span><br>
              <span style="display: flex; height: 20px; width: 32em; justify-content: space-between; align-items: center;">
                <label style=" flex-basis:154px; ">ヘッダー文字サイズ</label>
                <?= $this->ngForm->input('header_text_size', [
                'type' => 'number',
                'class' => 'showNormal',
                'min' => '12',
                'max' => $max_header_fontsize,
                'div' => false,
                'label' => false,
                'maxlength' => 7,
                'style' => "width: 100px; padding-left: 20px !important; margin: -5px 0 0 0;",
                'error' => false,
                'string-to-number' => true,
                'ng-max' => "false"
              ],
              [
                'entity' => 'MWidgetSetting.header_text_size'
              ]) ?><span style="display:inline-block; width:auto; padding-top: 0px; align-self: flex-start;">px</span><span class="greenBtn btn-shadow" ng-click="revertStandardTextSize('header_text_size')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; left: 11px;" >標準に戻す</span></span><br>
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
                  'style' => 'width: 120px; position: relative; left: 155px; top: -22px;'
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
                  'style' => 'width: 120px; position: relative; left: 155px; top: -22px;'
                ],
                [
                  'entity' => 'MWidgetSetting.re_text_color'
                ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('re_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
                <span style="display: flex; height: 20px; width: 32em; justify-content: space-between; align-items: center;">
                <label style=" flex-basis:154px; ">企業側吹き出し文字サイズ</label>
                  <?= $this->ngForm->input('re_text_size', [
                    'type' => 'number',
                    'class' => 'showNormal',
                    'min' => '10',
                    'max' => $max_fontsize,
                    'placeholder' => '',
                    'div' => false,
                    'label' => false,
                    'maxlength' => 7,
                    'style' => "width: 100px; padding-left: 20px !important; margin: -5px 0 0 0;",
                    'error' => false,
                    'string-to-number' => true,
                    'ng-max' => "false"
                  ],
                    [
                      'entity' => 'MWidgetSetting.re_text_size'
                    ]) ?><span style="display:inline-block; width:auto; padding-top: 0px; align-self: flex-start;">px</span><span class="greenBtn btn-shadow" ng-click="revertStandardTextSize('re_text_size')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; left: 11px;" >標準に戻す</span></span><br>
                <?php /* 隠しパラメータ */ ?>
                <?= $this->ngForm->input('line_button_margin', [
                    'type' => 'hidden',
                    'div' => false,
                    'label' => false,
                    'string-to-number' => true,
                    'default' => 2.6
                  ],
                  [
                    'entity' => 'MWidgetSetting.line_button_margin'
                  ]) ?>
                <?= $this->ngForm->input('btw_button_margin', [
                    'type' => 'hidden',
                    'div' => false,
                    'label' => false,
                    'string-to-number' => true,
                    'default' => 2.6
                  ],
                  [
                    'entity' => 'MWidgetSetting.btw_button_margin'
                  ]) ?>
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
                  'style' => 'width: 120px; position: relative; left: 155px; top: -22px;'
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
                  'style' => 'width: 120px; position: relative; left: 155px; top: -22px;'
                ],
                [
                  'entity' => 'MWidgetSetting.se_text_color'
                ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('se_text_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -50px; left: 295px;" >標準に戻す</span></span>
                <span style="display: flex; height: 20px; width: 32em; justify-content: space-between; align-items: center;">
                <label style=" flex-basis:154px; ">訪問者吹き出し文字サイズ</label>
                  <?= $this->ngForm->input('se_text_size', [
                    'type' => 'number',
                    'class' => 'showNormal',
                    'min' => '10',
                    'max' => $max_fontsize,
                    'placeholder' => '',
                    'div' => false,
                    'label' => false,
                    'maxlength' => 7,
                    'style' => "width: 100px; padding-left: 20px !important; margin: -5px 0 0 0;",
                    'error' => false,
                    'string-to-number' => true,
                    'ng-max' => "false"
                  ],
                    [
                      'entity' => 'MWidgetSetting.se_text_size'
                    ]) ?><span style="display:inline-block; width:auto; padding-top: 0px; align-self: flex-start;">px</span><span class="greenBtn btn-shadow" ng-click="revertStandardTextSize('se_text_size')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; left: 11px;" >標準に戻す</span></span><br>
              </div>
              </div>
              <!-- 基本設定色end -->
              <!-- 0.通常設定・高度設定 -->
              <!-- 高度な設定を行う行わないを制御するチェックボックス -->
              <pre style="margin-top: <?php echo $coreSettings[C_COMPANY_USE_CHAT] ? "136" : "38"?>px; margin-left: -3px; margin-bottom: 5px;"><hr class="separator" style="margin: 5px 0 5px 0;"><label class="pointer"><?= $this->ngForm->input('color_setting_type', [
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
                <span style="height: 35px;"><label>背景色</label><?= $this->ngForm->input('header_background_color', [
                      'type' => 'text',
                      'placeholder' => '背景色',
                      'div' => false,
                      'class' => 'jscolor {hash:true}',
                      'label' => false,
                      'maxlength' => 7,
                      'error' => false,
                      'style' => 'width: 120px; position: relative; left: 106px;'
                  ],
                  [
                      'entity' => 'MWidgetSetting.header_background_color'
                  ]) ?><span class="greenBtn btn-shadow" ng-click="returnStandardColor('header_background_color')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; top: -28px; left: 285px;" >標準に戻す</span></span>
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
                  <!-- .メッセージBOX文字サイズ -->
                  <span style="display: flex; height: 20px; width: 32em; justify-content: flex-start; align-items: center; margin-bottom: 15px;">
                    <label>メッセージBOX文字サイズ</label>
                    <?= $this->ngForm->input('message_box_text_size', [
                      'type' => 'number',
                      'class' => 'showNormal',
                      'min' => '10',
                      'max' => '36',
                      'placeholder' => '',
                      'div' => false,
                      'label' => false,
                      'maxlength' => 7,
                      'style' => "width: 102px;",
                      'error' => false,
                      'string-to-number' => true
                    ],
                    [
                      'entity' => 'MWidgetSetting.message_box_text_size'
                    ]) ?><span style="display:inline-block; width:auto; padding-top: 0px; align-self: flex-start; margin-left: 5px">px</span><span class="greenBtn btn-shadow" ng-click="revertStandardTextSize('message_box_text_size')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; left: 11px; margin-left: 9px;" >標準に戻す</span></span>
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
                  <!-- .送信ボタン文字サイズ -->
                  <span style="display: flex; height: 20px; width: 32em; justify-content: flex-start; align-items: center; margin-bottom: 15px;">
                    <label>送信ボタン文字サイズ</label>
                    <?= $this->ngForm->input('chat_send_btn_text_size', [
                      'type' => 'number',
                      'class' => 'showNormal',
                      'min' => '10',
                      'max' => $max_send_btn_fontsize,
                      'div' => false,
                      'label' => false,
                      'maxlength' => 7,
                      'style' => "width: 102px;margin-left: 25px",
                      'error' => false,
                      'string-to-number' => true,
                      'ng-max' => "false"
                    ],
                    [
                      'entity' => 'MWidgetSetting.chat_send_btn_text_size'
                    ]) ?><span style="display:inline-block; width:auto; padding-top: 0px; align-self: flex-start;margin-left:5px;">px</span><span class="greenBtn btn-shadow" ng-click="revertStandardTextSize('chat_send_btn_text_size')" style="width: 100px; text-align: center; padding: 4px; height: 25px; font-size: 0.9em; position: relative; left: 11px; margin-left: 10px;" >標準に戻す</span></span>
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
            <div style="display: flex; flex-direction: column;">
              <div>
                <?php if($coreSettings[C_COMPANY_USE_ICON_SETTINGS]): ?>
                <span>メイン画像</span>
                <div style="margin-top: 10px" ng-init="mainImageToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_main_image'))?>'">
                <?php else: ?>
                <div ng-init="mainImageToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_main_image'))?>'">
                <?php endif; ?>
                <?= $this->ngForm->hidden('main_image') ?>
                <label class="pointer" for="showMainImage1"><input type="radio" name="data[MWidgetSetting][show_main_image]" ng-model="mainImageToggle" id="showMainImage1" value="1" >画像を表示する</label><br>
                <div id="imageSelectBtns" ng-class="{chooseImg: showChooseImg()}">

                  <div id="picDiv">
                    <img ng-if="isPictureImage(main_image)" ng-src="{{main_image}}" err-src="<?=C_PATH_WIDGET_GALLERY_IMG?>chat_sample_picture.png" ng-style="{'background-color': main_color}" width="62" height="70" alt="チャットに設定している画像">
                    <i ng-if="isIconImage(main_image)" class="fal {{main_image}}" alt="チャット画像" ng-style="getIconColor(main_image)"></i>
                  </div>
                  <div id="picChooseDiv">
                    <div class="greenBtn btn-shadow" ng-click="showGallary(<?=WIDGET_GALLERY_TYPE_MAIN ?>)">ギャラリーから選択</div>
                    <div class="greenBtn btn-shadow" id="fileTagWrap"><?php echo $this->Form->file('uploadImage', array('accept' => '.png,.jpeg,.jpg')); ?>画像をアップロード</div>
                    <input type="hidden" name="data[Trimming][info]" ng-model="trimmingInfo" id="TrimmingInfo" />
                  </div>
                </div>
                <?php if ($this->Form->isFieldError('main_image')) echo $this->Form->error('main_image', null, ['ng-if'=>'mainImageToggle=="1"']); ?>
                <?php if ($this->Form->isFieldError('uploadImage')) echo $this->Form->error('uploadImage', null, ['ng-if'=>'mainImageToggle=="1"']); ?>
                <span ng-if="uploadImageError">{{uploadImageError}}</span>
                <label class="pointer" for="showMainImage2"><input type="radio" name="data[MWidgetSetting][show_main_image]" ng-change="forceIconOriginal()" ng-model="mainImageToggle" id="showMainImage2" value="2" >画像を表示しない</label>
              </div>
              </div>
              <?php if($coreSettings[C_COMPANY_USE_ICON_SETTINGS]): ?>
              <hr class="separator" style="margin-top: 1em">
              <div>
                <span>アイコン（チャットボット）</span>
                <div style="margin-top: 10px" ng-init="chatbotIconToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_chatbot_icon'))?>'">
                  <?= $this->Form->hidden('chatbot_icon') ?>
                  <label class="pointer" for="showChatbotIcon1"><input type="radio" name="data[MWidgetSetting][show_chatbot_icon]" ng-change="resetChatbotIconTypeToMain()"  ng-model="chatbotIconToggle" id="showChatbotIcon1" value="1" >アイコンを表示する</label><br>
                  <div ng-show="chatbotIconToggle == 1" class="icon_picker" ng-init="chatbotIconType='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'chatbot_icon_type'))?>'">
                    <label class="pointer" for="chatbotIconType1"><input type="radio" name="data[MWidgetSetting][chatbot_icon_type]" ng-model="chatbotIconType" ng-change="changeIconToMainImage('bot')" ng-disabled="mainImageToggle == 2"  id="chatbotIconType1" value="1">メイン画像と同じ画像を利用する</label><br>
                    <label class="pointer" for="chatbotIconType2"><input type="radio" name="data[MWidgetSetting][chatbot_icon_type]" ng-model="chatbotIconType" ng-change="changeIconToNoImage('bot')"  id="chatbotIconType2" value="2" >個別に設定する</label><br>
                    <div ng-show="chatbotIconType == 2" style="display: flex;" >
                      <div id="iconDivWrapper">
                        <div id="iconDiv" ng-style="iconBorderSetting(checkWhiteColor() && isIconImage(chatbot_icon))">
                          <img ng-if="!isPictureImage(chatbot_icon) && !isIconImage(chatbot_icon)" ng-src="<?=C_PATH_WIDGET_GALLERY_IMG?>icon_sample_picture.png" alt="NO IMAGE">
                          <img ng-if="isPictureImage(chatbot_icon)" ng-src="{{chatbot_icon}}" err-src="<?=C_PATH_WIDGET_GALLERY_IMG?>chat_sample_picture.png" alt="無人対応アイコンに設定している画像">
                          <i ng-if="isIconImage(chatbot_icon)" class="fal {{chatbot_icon}}" alt="チャット画像" ng-style="getIconColor(chatbot_icon)" style="margin-left: 1px;" ></i>
                        </div>
                      </div>
                      <div id="iconChooseDiv">
                        <div class="greenBtn btn-shadow" ng-click="showGallary(<?=WIDGET_GALLERY_TYPE_CHATBOT ?>)">ギャラリーから選択</div>
                        <div class="greenBtn btn-shadow" id="fileTagWrap"><?php echo $this->Form->file('uploadBotIcon', array('accept' => '.png,.jpeg,.jpg')); ?>画像をアップロード</div>
                        <input type="hidden" name="data[Trimming][botIconInfo]" ng-model="trimmingBotIconInfo" id="TrimmingBotIconInfo" />
                      </div>
                    </div>
                  </div>
                  <?php if ($this->Form->isFieldError('chatbot_icon')) echo $this->Form->error('chatbot_icon', null, ['ng-if'=>'mainImageToggle=="1"']); ?>
                  <label class="pointer" for="showChatbotIcon2"><input type="radio" name="data[MWidgetSetting][show_chatbot_icon]" ng-model="chatbotIconToggle" id="showChatbotIcon2" value="2" >アイコンを表示しない</label>
                </div>
              </div>
              <hr class="separator" style="margin-top: 1em">
              <div>
                <span>アイコン（オペレータ）</span>
                <div style="margin-top: 10px" ng-init="operatorIconToggle='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'show_operator_icon'))?>'">
                  <?= $this->Form->hidden('operator_icon') ?>
                  <label class="pointer" for="showOperatorIcon1"><input type="radio" name="data[MWidgetSetting][show_operator_icon]" ng-change="resetOperatorIconTypeToMain()" ng-model="operatorIconToggle" id="showOperatorIcon1" value="1" >アイコンを表示する</label><br>
                  <div ng-show="operatorIconToggle == 1" class="icon_picker" ng-init="operatorIconType='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'operator_icon_type'))?>'">
                    <label class="pointer" for="operatorIconType1"><input type="radio" name="data[MWidgetSetting][operator_icon_type]" ng-model="operatorIconType" ng-change="changeIconToMainImage('op')" ng-disabled="mainImageToggle == 2" id="operatorIconType1" value="1" >メイン画像と同じ画像を利用する</label><br>
                    <label class="pointer" for="operatorIconType2"><input type="radio" name="data[MWidgetSetting][operator_icon_type]" ng-model="operatorIconType" ng-change="changeIconToNoImage('op')" id="operatorIconType2" value="2" >個別に設定する</label><br>
                    <div ng-show="operatorIconType == 2" style="display: flex;">
                      <div>
                        <div id="iconDivWrapper">
                          <div id="iconDiv" ng-style="iconBorderSetting(checkWhiteColor() && isIconImage(chatbot_icon))">
                            <img ng-if="!isPictureImage(operator_icon) && !isIconImage(operator_icon)" ng-src="<?=C_PATH_WIDGET_GALLERY_IMG?>icon_sample_picture.png" alt="NO IMAGE">
                            <img ng-if="isPictureImage(operator_icon)" ng-src="{{operator_icon}}" err-src="<?=C_PATH_WIDGET_GALLERY_IMG?>chat_sample_picture.png" alt="有人対応アイコンに設定している画像">
                            <i ng-if="isIconImage(operator_icon)" class="fal {{operator_icon}}" alt="チャット画像" ng-style="getIconColor(operator_icon)" style="margin-left: 1px;" ></i>
                          </div>
                        </div>
                      </div>
                      <div id="iconChooseDiv">
                        <div class="greenBtn btn-shadow" ng-click="showGallary(<?=WIDGET_GALLERY_TYPE_OPERATOR ?>)">ギャラリーから選択</div>
                        <div class="greenBtn btn-shadow" id="fileTagWrap"><?php echo $this->Form->file('uploadOpIcon', array('accept' => '.png,.jpeg,.jpg')); ?>画像をアップロード</div>
                        <input type="hidden" name="data[Trimming][opIconInfo]" ng-model="trimmingOpIconInfo" id="TrimmingOpIconInfo" />
                      </div>
                    </div>
                    <label class="pointer" for="operatorIconType3"><input type="radio" name="data[MWidgetSetting][operator_icon_type]" ng-init="getProfileIconForOperatorIcon(operatorIconType)"  ng-model="operatorIconType" ng-change="getProfileIconForOperatorIcon(operatorIconType)" id="operatorIconType3" value="3" >オペレーター毎に個別のアイコンを利用する</label><br>
                  </div>
                  <?php if ($this->Form->isFieldError('operator_icon')) echo $this->Form->error('operator_icon', null, ['ng-if'=>'mainImageToggle=="1"']); ?>
                  <label class="pointer" for="showOperatorIcon2"><input type="radio" name="data[MWidgetSetting][show_operator_icon]" ng-model="operatorIconToggle" id="showOperatorIcon2" value="2" >アイコンを表示しない</label>
                </div>
              </div>
              <?php else: ?>
              <input type="hidden" name="data[MWidgetSetting][uploadBotIcon]" value="" />
              <input type="hidden" name="data[Trimming][botIconInfo]" ng-model="trimmingBotIconInfo" id="TrimmingBotIconInfo" value="" />
              <input type="hidden" name="data[MWidgetSetting][uploadOpIcon]" value="" />
              <input type="hidden" name="data[Trimming][opIconInfo]" ng-model="trimmingOpIconInfo" id="TrimmingOpIconInfo" value="" />
              <?php endif; ?>
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
                    'class' => 'ignore-click-event',
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
                  <label class="pointer choose" for="closeButtonModeType2" style="margin:10px 0 10px 20px;"><input type="radio" name="data[MWidgetSetting][close_button_mode_type]" ng-model="closeButtonModeTypeToggle" id="closeButtonModeType2" class="showHeader" value="2" ng-click="hideWidget()">非表示<br><s style="margin:0px 0px 0px 3.2em; display: inline-block;">※再アクセス時までウィジェットが表示されなくなります</s></label>
                </div>
              </div>
            </div>
          </li>
          <!-- 閉じるボタン -->
          <?php /*スマホ隠しパラメータ*/ ?>
          <?= $this->ngForm->input('sp_banner_vertical_position_from_top', [
            'type' => 'hidden',
            'div' => false,
            'label' => false,
            'default' => "50%"
          ],
          [
            'entity' => 'MWidgetSetting.sp_banner_vertical_position_from_top'
          ]) ?>
          <?= $this->ngForm->input('sp_banner_vertical_position_from_bottom', [
            'type' => 'hidden',
            'div' => false,
            'label' => false,
            'default' => "5px"
          ],
          [
            'entity' => 'MWidgetSetting.sp_banner_vertical_position_from_bottom'
          ]) ?>
          <?= $this->ngForm->input('sp_banner_horizontal_position', [
            'type' => 'hidden',
            'div' => false,
            'label' => false,
            'default' => "5px"
          ],
          [
            'entity' => 'MWidgetSetting.sp_banner_horizontal_position'
          ]) ?>
        </ul>
      </section>

      <?php if($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <h3><?php echo mb_convert_kana($headerNo, "N", "utf-8"); $headerNo++ ?>．チャット設定</h3>
      <section>
        <ul class="settingList">
          <!-- 初期表示時の自由入力エリア -->
          <li>
            <span class="require"><label>初期表示時の自由入力エリア</label></span>
            <?php if($coreSettings[C_COMPANY_USE_FREE_INPUT]): ?>
              <div ng-init="chat_init_show_textarea='<?=h($this->formEx->val($this->data['MWidgetSetting'], 'chat_init_show_textarea'))?>'">
                <label class="pointer choose" for="MWidgetSettingChatInitShowTextarea1"><input type="radio" name="data[MWidgetSetting][chat_init_show_textarea]" ng-model="chat_init_show_textarea" id="MWidgetSettingChatInitShowTextarea1" class="showHeader" value="1" >表示する</label><br>
                <label class="pointer choose" for="MWidgetSettingChatInitShowTextarea2"><input type="radio" name="data[MWidgetSetting][chat_init_show_textarea]" ng-model="chat_init_show_textarea" id="MWidgetSettingChatInitShowTextarea2" class="showHeader" value="2" >表示しない</label><br>
              </div>
            <?php else: ?>
              <div ng-init="chat_init_show_textarea='1'">
                <label class="pointer choose" for="MWidgetSettingChatInitShowTextarea1"><input type="radio" name="data[MWidgetSetting][chat_init_show_textarea]" ng-model="chat_init_show_textarea" id="MWidgetSettingChatInitShowTextarea1" class="showHeader" value="1">表示する</label><br>
                <label class="pointer choose commontooltip" for="MWidgetSettingChatInitShowTextarea2" style="color: rgb(204, 204, 204);" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。"><input type="radio" name="data[MWidgetSetting][chat_init_show_textarea]" ng-model="chat_init_show_textarea" id="MWidgetSettingChatInitShowTextarea2" class="showHeader" value="2" disabled>表示しない</label><br>
              </div>
            <?php endif; ?>
          </li>
          <?php if ( $this->Form->isFieldError('chat_init_show_textarea') ) echo $this->Form->error('chat_init_show_textarea', null, ['wrap' => 'li']); ?>
          <!--  -->
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
            <span class="require"><label>入室・退室時の表示</label></span>
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
          <!-- オートメッセージ企業名表示 -->
          <li>
            <span class="require"><label>自動メッセージの見出し</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('show_automessage_name', [
                  'type' => 'radio',
                  'options' => $widgetShowAutomessageNameType,
                  'legend' => false,
                  'separator' => '</label><br><label class="pointer">',
                  'class' => 'showChat',
                  'div' => false,
                  'label' => false,
                  'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.show_automessage_name'
                  ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('show_automessage_name') ) echo $this->Form->error('show_automessage_name', null, ['wrap' => 'li']); ?>
          <!-- オートメッセージ企業名表示 -->
          <!-- ウィジェットの表示   -->
          <li>
            <span class="require"><label>有人メッセージの見出し</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('show_op_name', [
                  'type' => 'radio',
                  'options' => $widgetShowOpNameType,
                  'legend' => false,
                  'separator' => '</label><br><label class="pointer">',
                  'div' => false,
                  'label' => false,
                  'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.show_op_name'
                  ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('sp_show_flg') ) echo $this->Form->error('sp_show_flg', null, ['wrap' => 'li']); ?>
          <!-- ウィジェットの表示   -->
          <!-- 吹き出しデザイン -->
          <li>
            <span class="require"><label>吹き出しデザイン</label></span>
            <div class="chatMessageDesignBlock">
              <label class="pointer"><input type="radio" name="data[MWidgetSetting][chat_message_design_type]" id="MWidgetSettingChatMessageDesignType1" value="1" checked="checked" class="chatMessageDesignType" ng-model="chat_message_design_type" ng-init="chat_message_design_type='1'">BOX型</label>
              <div ng-if="chat_message_design_type === '1'" class="chatMessageArrowPositionBlock">
                <span class="subMenu">吹き出しのカド</span>
                <div class="chatMessageArrowPosition">
                  <label class="pointer first"><input type="radio" name="data[MWidgetSetting][chat_message_arrow_position]" id="MWidgetSettingChatMessageArrowPosition1" value="1" class="chatMessageArrowPositionType" ng-model="chat_message_arrow_position" ng-init="chat_message_arrow_position='2'">上</label>
                  <label class="pointer last"><input type="radio" name="data[MWidgetSetting][chat_message_arrow_position]" id="MWidgetSettingChatMessageArrowPosition2" value="2" class="chatMessageArrowPositionType" ng-model="chat_message_arrow_position" ng-init="chat_message_arrow_position='2'">下</label>
                </div>
              </div>
              <label class="pointer last"><input type="radio" name="data[MWidgetSetting][chat_message_design_type]" id="MWidgetSettingChatMessageDesignType2" value="2" class="chatMessageDesignType" ng-model="chat_message_design_type" ng-init="chat_message_design_type='1'">吹き出し型</label>
            </div>
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
          <!-- スクロール中の表示制御 -->
           <li>
            <span class="require"><label>ウィジェットの表示制御</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('sp_scroll_view_setting', [
                'type' => 'checkbox',
                'ng-disabled' => 'sp_show_flg !== "'.C_SELECT_CAN.'"',
                'ng-checked' => 'sp_scroll_view_setting ==="1"',
                'legend' => false,
                'separator' => '</label><br><label class="pointer">',
                'div' => false,
                'class' => 'showSp',
                'label' => "スクロール中は非表示にする",
                'error' => false
              ],
              [
                'entity' => 'MWidgetSetting.sp_scroll_view_setting'
              ]) ?></label></pre>
          </li>
          <!-- スクロール中の表示制御 -->
          <!-- ウィジェットの状態 -->
          <li>
            <span class="require"><label>ウィジェットの状態</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('sp_widget_view_pattern', [
              'type' => 'radio',
              'options' => $widgetSpViewPattern,
              'ng-change' => 'resetSpView()',
              'ng-disabled' => 'sp_show_flg !== "'.C_SELECT_CAN.'" || closeButtonModeTypeToggle == "2" || closeButtonSettingToggle == "1"',
              'legend' => false,
              'separator' => '</label><br><label class="pointer">',
              'class' => 'showSp',
              'div' => false,
              'label' => false,
              'string-to-number' => true,
              'default' => 1
            ],
            [
              'entity' => 'MWidgetSetting.sp_widget_view_pattern'
            ]) ?></label></pre>
          </li>
          <!-- ウィジェットの状態 -->
          <!-- 小さなバナーの表示位置 -->
          <li>
            <span class="require"><label>小さなバナー表示位置</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('sp_banner_position', [
              'type' => 'radio',
              'options' => $widgetSpPositionType,
              'ng-disabled' => 'sp_show_flg !== "'.C_SELECT_CAN.'" || closeButtonModeTypeToggle == "2" || closeButtonSettingToggle == "1"',
              'legend' => false,
              'ng-change' => 'forceSpCloseWidget()',
              'separator' => '</label><br><label class="pointer">',
              'class' => 'showSp',
              'div' => false,
              'label' => false,
              'string-to-number' => true,
              'default' => 1
            ],
            [
              'entity' => 'MWidgetSetting.sp_banner_position'
            ]) ?></label></pre>
          </li>
          <!-- 小さなバナーの表示位置 -->
          <!-- 小さなバナーのタイトル -->
          <li>
            <span class="require"><label>小さなバナーのタイトル</label></span>
            <pre><label><?= $this->ngForm->input('sp_banner_text', [
              'type' => 'text',
              'placeholder' => 'バナーテキスト',
              'class' => 'ignore-click-event',
              'ng-disabled' => 'sp_show_flg !== "'.C_SELECT_CAN.'" || closeButtonModeTypeToggle == "2" || closeButtonSettingToggle == "1"',
              'div' => false,
              'label' => false,
              'maxlength' => 15,
              'error' => false,
              'ng-focus' => 'bannerEditClick(2)',
              'ng-maxlength' => "false"
            ],
            [
              'entity' => 'MWidgetSetting.sp_banner_text'
            ]) ?></label></pre>
          </li>
          <!-- 小さなバナーのタイトル -->
          <!-- シンプル表示 -->
          <li>
            <span class="require"><label>最大時のシンプル表示</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('sp_header_light_flg', [
                'type' => 'radio',
                'options' => $normalChoices,
                'class' => 'ignore-click-event',
                'ng-change' => 'resetSpView()',
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

          <!-- シンプル表示 -->
          <li>
            <span class="require"><label>最大化表示サイズ</label></span>
            <pre><label class="pointer"><?= $this->ngForm->input('sp_maximize_size_type', [
                  'type' => 'radio',
                  'options' => $spMiximizeSizeType,
                  'ng-disabled' => 'sp_show_flg !== "'.C_SELECT_CAN.'"',
                  'legend' => false,
                  'ng-change' => 'switchWidget(3); resetSpView(); ',
                  'separator' => '</label><br><label class="pointer">',
                  'class' => 'showSp',
                  'div' => false,
                  'label' => false,
                  'error' => false
                ],
                  [
                    'entity' => 'MWidgetSetting.sp_maximize_size_type'
                  ]) ?></label></pre>
          </li>
          <?php if ( $this->Form->isFieldError('sp_maximize_size_type') ) echo $this->Form->error('sp_maximize_size_type', null, ['wrap' => 'li']); ?>
          <!-- シンプル表示 -->
        </ul>
      </section>
      <?php endif; ?>

      <?php
        // 以下はシェアリングプランのみ表示する
        if(!$coreSettings[C_COMPANY_USE_CHAT] && ($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]))):
      ?>
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
      <?= $this->Html->link('元に戻す', 'javascript:void(0)', ['ng-click' => 'reloadAct()','class' => 'whiteBtn btn-shadow']) ?>
      <?= $this->Html->link('更新', 'javascript:void(0)', ['ng-click' => 'saveAct()', 'class' => 'greenBtn btn-shadow']) ?>
      <?= $this->Html->link('dummy', 'javascript:void(0)', ['ng-click' => '', 'class' => 'whiteBtn btn-shadow', 'style' => 'visibility: hidden;']) ?>
    </div>
  </section>
