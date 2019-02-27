<?php /* ヒアリング */ ?>
<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_HEARING ?>" class="set_action_item_body action_hearing"
     ng-init="main.controllHearingSettingView(setActionId)">
  <ul>
    <li>
      <div class='grid-container-hearing grid-container-header'>
        <div class='area-require'>必須<span class="questionBalloon"><icon class="questionBtn"
                                                                        data-tooltip="必須項目とする場合はチェックを付けます。（スキップ可能とする場合はチェックを外します）">?</icon></span>
        </div>
        <div class='area-name'>変数名<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の回答内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{{showExpression('名前')}}様からのお問い合わせを受付いたしました。">?</icon></span>
        </div>
        <div class='area-type'>タイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                      data-tooltip="ヒアリングの回答を入力する形式を指定します。<br>＜タイプ＞<br>テキスト(1行)　　 　　 ：フリーテキスト入力（改行不可）<br>テキスト(複数行)　　　 ：フリーテキスト入力（改行可）<br>ラジオボタン　　　　　：ラジオボタン形式の択一選択<br>プルダウン　　　　　　：プルダウン形式の択一選択<br>カルーセル（画像表示）：画像表示による択一選択<br>コンファーム　　　　　：ボタン形式の択一選択<br>カレンダー　　　　　　：カレンダーから日付を選択"
                                                                      data-tooltip-width="30em">?</icon></span>
        </div>
        <div class='area-message'>質問内容<span class="questionBalloon"><icon class="questionBtn"
                                                                          data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。">?</icon></span>
        </div>

      </div>
      <hr>
      <ul ui-sortable="sortableOptionsHearing" class="input-one-row hearing-input-type sortable"
          ng-model="setItem.hearings">
        <li class='grid-container-hearing grid-container-body itemListGroup'
            ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
          <div class="area-drag-symbol handleOption" style="cursor: move;">
            <i class="fas fa-arrows-alt-v fa-2x"></i>
          </div>
          <div class="area-require">
            <label class="require-checkbox">
              <input type="checkbox" ng-model="hearingItem.required">
              <span class="checkmark"></span>
            </label>
          </div>
          <div class='area-name'><input type="text" class="raw-variable-suggest" ng-model="hearingItem.variableName"></div>
          <div class='area-type'>
            <select name="hearing-input-option" ng-model="hearingItem.uiType"
                    ng-change="main.handleChangeUitype(<?= C_SCENARIO_ACTION_HEARING ?>, setActionId, listId, hearingItem.uiType)">
              <option value="1">テキスト（１行）</option>
              <option value="2">テキスト（複数行）</option>
              <option value="8">ボタン</option>
              <option value="3">ラジオボタン</option>
              <option value="4">プルダウン</option>
              <option value="6">カルーセル（画像表示）</option>
              <option value="7">コンファーム</option>
              <option value="9">チェックボックス</option>
              <option value="5">カレンダー</option>
            </select>
          </div>
          <div class='area-message' >
            <resize-textarea class="variable-suggest" ng-class="{disabledArea: hearingItem.settings.balloonStyle === '2'}" maxlength="4000" ng-model="hearingItem.message" rows="1"
                             data-maxRow="10" ></resize-textarea>
          </div>
          <div class='area-btn'>
            <div class="btnBlock">
              <a><?= $this->Html->image('add.png', array(
                  'alt' => '追加',
                  'width' => 25,
                  'height' => 25,
                  'class' => 'btn-shadow disOffgreenBtn hearingBtn',
                  'style' => 'padding: 2px',
                  'ng-click' => 'main.addActionItemList($event, listId)'
                )) ?></a><a><?= $this->Html->image('dustbox.png', array(
                  'alt' => '削除',
                  'width' => 25,
                  'height' => 25,
                  'class' => 'btn-shadow redBtn deleteBtn hearingBtn',
                  'style' => 'padding: 2px',
                  'ng-click' => 'main.removeActionItemList($event, listId)'
                )) ?></a>
            </div>
          </div>
          <div class='area-detail'>
                        <span ng-if="hearingItem.uiType === '1'" style="padding: 0;">
                            <label>テキストタイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                              data-tooltip="サイト訪問者が入力した回答が適切か、整合性チェックを行うことができます。<br>入力内容が不適切だった場合（整合性チェックNGだった場合）は、「入力エラー時の返信メッセージ」に設定されたメッセージを自動送信後、再度ヒアリングを実施します。<br><br>＜タイプ＞<br>text　　　　：制限なし<br>number　　 ：数字のみ<br>email　　　：メールアドレス形式のみ<br>tel_number：0から始まる10桁以上の数字とハイフンのみ">?</icon></span></label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-one-row-type"
                                                          value="1"
                                                          ng-model="hearingItem.inputType">text</label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-one-row-type"
                                                          value="2"
                                                          ng-model="hearingItem.inputType">number</label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-one-row-type"
                                                          value="3"
                                                          ng-model="hearingItem.inputType">email</label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-one-row-type"
                                                          value="4"
                                                          ng-model="hearingItem.inputType">tel</label>
                        </span>
            <span ng-if="hearingItem.uiType === '2'" style="padding: 0;">
                            <label>テキストタイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                              data-tooltip="サイト訪問者が入力した回答が適切か、整合性チェックを行うことができます。<br>入力内容が不適切だった場合（整合性チェックNGだった場合）は、「入力エラー時の返信メッセージ」に設定されたメッセージを自動送信後、再度ヒアリングを実施します。<br><br>＜タイプ＞<br>text　　　　：制限なし<br>number　　 ：数字のみ">?</icon></span></label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-multiple-row-type"
                                                          value="1" ng-model="hearingItem.inputType">text</label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-multiple-row-type"
                                                          value="2" ng-model="hearingItem.inputType">number</label>
                        </span>

            <div
                ng-if="(hearingItem.uiType === '1' && hearingItem.inputType != 1) || (hearingItem.uiType === '2' &&  hearingItem.inputType != 1)"
                class="styleFlexbox m15t">
                            <span class="fb11em"><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ
                                    <span class="questionBalloon">
                                        <icon class="questionBtn"
                                              data-tooltip="サイト訪問者が入力した回答が不適切だった場合（各テキストタイプの整合性チェックNGだった場合）に自動返信するメッセージを設定します。">?</icon>
                                    </span>
                                </label>
                            </span>
              <div>
                <resize-textarea name="errorMessage" class="variable-suggest" maxlength="4000" ng-model="hearingItem.errorMessage"
                                 cols="48"
                                 rows="1" placeholder="入力エラー時の返信メッセージを入力してください"
                                 data-maxRow="10"></resize-textarea>
              </div>
            </div>

            <div ng-if="hearingItem.uiType === '3' || hearingItem.uiType === '4' || hearingItem.uiType === '7' || hearingItem.uiType === '8' || hearingItem.uiType === '9'"
                 ng-repeat="(optionIndex, option) in hearingItem.settings.options  track by $index"
                 class="select-option-input action{{setActionId}}_option{{listId}}"
                 ng-init="main.controllHearingOptionView(setActionId, listId)">
                            <span><label class="">選択肢 {{optionIndex + 1}}<span class="questionBalloon"><icon
                                      class="questionBtn"
                                      data-tooltip="選択肢を1つずつ設定します。<br>例）選択肢１：男性<br>　　選択肢２：女性">?</icon></span></label></span>
              <input type="text" class="variable-suggest m20lt" ng-model="hearingItem.settings.options[optionIndex]"
                     style="width: 200px;">
              <div class="btnBlock">
                <a><?= $this->Html->image('add.png', array(
                    'alt' => '追加',
                    'width' => 25,
                    'height' => 25,
                    'class' => 'btn-shadow disOffgreenBtn',
                    'style' => 'padding: 2px',
                    'ng-click' => 'main.addHearingOption($event, hearingItem.uiType, optionIndex, listId)'
                  )) ?></a>
                <a><?= $this->Html->image('dustbox.png', array(
                    'alt' => '削除',
                    'width' => 25,
                    'height' => 25,
                    'class' => 'btn-shadow redBtn deleteBtn',
                    'style' => 'padding: 2px;',
                    'ng-click' => 'main.removeHearingOption($event, hearingItem.uiType, optionIndex, listId)'
                  )) ?></a>
              </div>
              <a ng-if="!optionIndex" href="" class="greenBtn btn-shadow bulk-button commontooltip"
                 style="display: inline; margin-top: 5px" data-text="選択肢として登録する内容をテキストエリア内で改行して一括で登録することができます。"
                 ng-click="main.showBulkSelectionPopup(setActionId, listId, hearingItem.uiType);"> 選択肢を一括登録</a>
            </div>

            <div ng-if=" hearingItem.uiType === '9'" class="checkbox-separator" style="display: flex; margin-top: 6px">
              <span><label class="">複数選択された際の区切り文字</label></span>
              <select name="checkbox-separator" ng-model="hearingItem.settings.checkboxSeparator" style="width: 150px; margin-left: 20px; height: 29.5px;">
                <option value="1"> ,（カンマ）</option>
                <option value="2">/（スラッシュ）</option>
                <option value="3">|（パイプ）</option>
              </select>
            </div>

            <label ng-if="hearingItem.uiType === '4'" class="pointer">
              <input type="checkbox" class="m15t" id="dropdown_custom_design"
                     ng-model="hearingItem.settings.pulldownCustomDesign">デザインをカスタマイズする
              <span class="questionBalloon"><icon class="questionBtn"
                                                  data-tooltip="プルダウンのデザイン（配色）を自由にカスタマイズすることができます。">?</icon></span>
            </label>

            <span ng-if="hearingItem.uiType === '6'" style="padding: 0;">
                            <label>表示形式<span class="questionBalloon"><icon class="questionBtn"
                                                                           data-tooltip="吹き出しの表示有無を選択できます。">?</icon></span></label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-balloon-style"
                                                          value="1"
                                                          ng-model="hearingItem.settings.balloonStyle">吹き出しあり</label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-balloon-style"
                                                          value="2"
                                                          ng-model="hearingItem.settings.balloonStyle">吹き出しなし</label>
            </span></br>

            <span ng-if="hearingItem.uiType === '6'" style="padding: 0;">
                            <label>スタイル<span class="questionBalloon"><icon class="questionBtn"
                                                                           data-tooltip="画像をウィジェット内に全画面表示するか、並べて表示するかを選択できます。">?</icon></span></label>
                            <label class="pointer"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-lineup-style"
                                                          value="1"
                                                          ng-model="hearingItem.settings.lineUpStyle">1つずつ表示</label>
                            <label class="pointer" style="margin-left: 15px;"><input type="radio"
                                                          name="action{{setActionId}}-hearing{{listId}}-lineup-style"
                                                          value="2"
                                                          ng-model="hearingItem.settings.lineUpStyle">並べて表示</label>
            </span>

            <div ng-if="hearingItem.uiType === '6'">
              <ul ui-sortable="sortableOptionsCarousel" ng-model="hearingItem.settings.images">
                <li class="action{{setActionId}}_option{{listId}}" ng-repeat="(imageIndex, image) in hearingItem.settings.images  track by $index"
                     ng-init="main.controllHearingOptionView(setActionId, listId)">
                  <div class="carousel-item">
                  <div class="carousel-item-header">
                    <div class="area-drag-symbol handleOption" style="cursor: move; display: inline-block; margin-left: 3px;">
                      <i class="fas fa-arrows-alt-v fa-2x" style="font-size: 16px;"></i>
                    </div>
                    <p><strong>画像 {{imageIndex + 1}}</strong></p>
                  </div>
                  <div class="carousel-item-body">
                    <div class="carousel-image styleFlexbox">
                      <span class="carousel-label"><label class="">画像 <span class="questionBalloon"><icon
                                class="questionBtn"
                                data-tooltip="カルーセルに表示する画像を設定します。</br>※ファイル形式：jpg, jpeg, png, gif">?</icon></span></label></span>
                      <p style="display: inline-block" class="m20l" ng-show="!hearingItem.settings.images[imageIndex].url && !hearingItem.settings.images[imageIndex].isUploading">画像が選択されていません</p>
                      <div class="uploadProgress" style="margin-left: 10px" ng-show="hearingItem.settings.images[imageIndex].isUploading">
                        <div class="uploadProgressArea" style="width: 20em;"><span>アップロード中 ...</span><div class="uploadProgressRate progressbar_action{{setActionId}}_hearing{{listId}}_image{{imageIndex}}"><span>アップロード中 ...</span></div></div>
                      </div>
                      <img ng-show="hearingItem.settings.images[imageIndex].url && !hearingItem.settings.images[imageIndex].isUploading" ng-src="{{hearingItem.settings.images[imageIndex].url}}" alt="プレビュー" style="margin: 8px 12px;" id="image_preview_action{{setActionId}}_hearing{{listId}}_image{{imageIndex}}" width="100" height="{{hearingItem.settings.aspectRatio ? 100 / hearingItem.settings.aspectRatio : 100}}">
                    </div>
                    <div class="carousel-button styleFlexbox">
                      <input type="file" class="hide image_upload_btn" id="upload_action{{setActionId}}_hearing{{listId}}_image{{imageIndex}}">
                      <span class="greenBtn btn-shadow" ng-click="main.carouselSelectFile($event, setActionId, listId, imageIndex)">ファイル選択</span>
                      <span class="btn-shadow"
                            ng-class="{disOffgrayBtn: !hearingItem.settings.images[imageIndex].url, redBtn: !!hearingItem.settings.images[imageIndex].url}"
                            ng-click="main.removeCarouselImage($event, setActionId, listId, imageIndex)">
                        ファイル削除
                      </span>
                    </div>
                    <div class="carousel-title styleFlexbox">
                      <span class="carousel-label"><label class="">タイトル <span class="questionBalloon"><icon
                                class="questionBtn"
                                data-tooltip="説明文のタイトルの設定を行います。">?</icon></span></label></span>
                      <input type="text" ng-model="hearingItem.settings.images[imageIndex].title" class="variable-suggest m20l m10r">
                    </div>
                    <div class="carousel-sub-title styleFlexbox">
                      <span class="carousel-label"><label class="">本文 <span class="questionBalloon"><icon
                            class="questionBtn"
                            data-tooltip="説明文の設定を行います。">?</icon></span></label></span>
                      <resize-textarea class="variable-suggest m20l m10r" style="height: 27px" maxlength="4000" rows="1"
                                       data-maxRow="10" ng-model="hearingItem.settings.images[imageIndex].subTitle"></resize-textarea>
                    </div>
                    <div class="carousel-answer styleFlexbox">
                      <span class="carousel-label"><label class="">選択時の内容 <span class="questionBalloon"><icon
                            class="questionBtn"
                            data-tooltip="サイト訪問者（チャット利用者）が画像を選択した際に変数にセットする文言を設定します。">?</icon></span></label></span>
                      <input type="text" class="variable-suggest m20l m10r" ng-model="hearingItem.settings.images[imageIndex].answer">
                    </div>
                  </div>
                  </div>
                  <div class="area-btn" style="display: inline-block; position: absolute; right: 14px; margin-top: -25px;">
                    <div class="btnBlock">
                      <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addHearingOption($event, hearingItem.uiType, imageIndex, listId)')) ?></a>
                      <a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px;', 'ng-click' => 'main.removeHearingOption($event, hearingItem.uiType, imageIndex, listId)')) ?></a>
                    </div>
                  </div>
                </li>
              </ul>
            </div>

            <label ng-if="hearingItem.uiType === '6'" class="pointer">
              <input type="checkbox" class="m15t"
                     ng-model="hearingItem.settings.carouselCustomDesign">デザインをカスタマイズする
              <span class="questionBalloon"><icon class="questionBtn"
                                                  data-tooltip="カルーセルのデザイン（文字色、文字サイズ、矢印のデザイン、枠線色）を変更できます。">?</icon></span>
            </label>

            <div class="dropdown-custom-design-area"
                 ng-if="hearingItem.uiType === '6' && hearingItem.settings.carouselCustomDesign">
              <span>
                <label for="">タイトル文字色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_carousel{{listId}}_titleColor"
                       ng-model="hearingItem.settings.customDesign.titleColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertCarouselDesign(setActionId, listId, 'titleColor')">標準に戻す</span>
              </span>
              <span>
                <label for="">タイトル文字サイズ</label>
                <input type="number" class="" min="5" max="100"
                       id="action{{setActionId}}_carousel{{listId}}_titleFontSize"
                       ng-model="hearingItem.settings.customDesign.titleFontSize"><p>px</p>
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertCarouselDesign(setActionId, listId, 'titleFontSize')">標準に戻す</span>
              </span>

              <span class="language-setting carousel-arrow-type">
                  <label for="">タイトル位置</label>
                  <label class="pointer"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-title-position"
                                                value="1"
                                                ng-model="hearingItem.settings.titlePosition"
                                                style="margin-left: 40px;">左寄せ</label>
                  <label class="pointer m20l"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-title-position"
                                                     value="2" style="margin-left: 20px"
                                                     ng-model="hearingItem.settings.titlePosition">中央寄せ</label>
                <label class="pointer m20l"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-title-position"
                                                   value="3" style="margin-left: 20px"
                                                   ng-model="hearingItem.settings.titlePosition">右寄せ</i></label>
                </span>
                <span>
                  <label for="">本文文字色</label>
                  <input type="text" class="jscolor{hash:true} ignore-click-event"
                         id="action{{setActionId}}_carousel{{listId}}_subTitleColor"
                         ng-model="hearingItem.settings.customDesign.subTitleColor">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCarouselDesign(setActionId, listId, 'subTitleColor')">標準に戻す</span>
                </span>

                <span>
                  <label for="">本文文字サイズ</label>
                  <input type="number" class="" min="5" max="100"
                         id="action{{setActionId}}_carousel{{listId}}_subTitleFontSize"
                         ng-model="hearingItem.settings.customDesign.subTitleFontSize"><p>px</p>
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCarouselDesign(setActionId, listId, 'subTitleFontSize')">標準に戻す</span>
                </span>

              <span class="language-setting carousel-arrow-type">
                  <label for="">本文文字位置</label>
                  <label class="pointer"><input type="radio"
                                                name="action{{setActionId}}-hearing{{listId}}-subTitle-position"
                                                value="1"
                                                ng-model="hearingItem.settings.subTitlePosition"
                                                style="margin-left: 40px;">左寄せ</label>
                  <label class="pointer m20l"><input type="radio"
                                                     name="action{{setActionId}}-hearing{{listId}}-subTitle-position"
                                                     value="2" style="margin-left: 20px"
                                                     ng-model="hearingItem.settings.subTitlePosition">中央寄せ</label>
                <label class="pointer m20l"><input type="radio"
                                                   name="action{{setActionId}}-hearing{{listId}}-subTitle-postiion"
                                                   value="3" style="margin-left: 20px"
                                                   ng-model="hearingItem.settings.subTitlePosition">右寄せ</i></label>
                </span>

              <span>
                <label for="">矢印色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_carousel{{listId}}_arrowColor"
                       ng-model="hearingItem.settings.customDesign.arrowColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertCarouselDesign(setActionId, listId, 'arrowColor')">標準に戻す</span>
              </span>


              <span class="language-setting carousel-pattern">
                  <label for="">矢印の位置</label>
                  <label class="pointer"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-pattern"
                                                     value="2" style="margin-left: 52px"
                                                     ng-model="hearingItem.settings.carouselPattern">画像の外側</label>
                  <label class="pointer m20l"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-pattern"
                                                value="1"
                                                ng-model="hearingItem.settings.carouselPattern"
                                                >画像の内側</label>
                </span>

              <span class="language-setting carousel-arrow-type" style="width: 38em">
                  <label>矢印スタイル</label>
                  <label class="pointer m20l"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-arrow"
                                                   value="4" style="margin-left: 40px"
                                                   ng-model="hearingItem.settings.arrowType"><i class="fas fa-chevron-square-right fa-2x"></i></i></label>
                  <label class="pointer"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-arrow"
                                                value="1"
                                                ng-model="hearingItem.settings.arrowType"
                                                style="margin-left: 20px;"><i class="fas fa-chevron-circle-right fa-2x"></i></label>
                  <label class="pointer m20l"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-arrow"
                                                     value="2" style="margin-left: 20px"
                                                     ng-model="hearingItem.settings.arrowType"><i class="fal fa-chevron-circle-right fa-2x"></i></label>
                <label class="pointer m20l"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-arrow"
                                                   value="3" style="margin-left: 20px"
                                                   ng-model="hearingItem.settings.arrowType"><i class="fas fa-chevron-right fa-2x"></i></label>

                </span>

              <span>
                <label for="">外枠線色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_carousel{{listId}}_outBorderColor"
                       ng-model="hearingItem.settings.customDesign.outBorderColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertCarouselDesign(setActionId, listId, 'outBorderColor')">標準に戻す</span>
              </span>

              <label class="pointer" style="margin-left: 145px">
                <input type="checkbox" style="margin-top: 5px; margin-bottom: 10px;"
                       ng-model="hearingItem.settings.outCarouselNoneBorder">枠線なしにする
              </label>

              <span>
                <label for="">内枠線色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_carousel{{listId}}_inBorderColor"
                       ng-model="hearingItem.settings.customDesign.inBorderColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertCarouselDesign(setActionId, listId, 'inBorderColor')">標準に戻す</span>
              </span>

              <label class="pointer" style="margin-left: 145px">
                <input type="checkbox" style="margin-top: 5px; margin-bottom: 10px;"
                       ng-model="hearingItem.settings.inCarouselNoneBorder">枠線なしにする
              </label>
            </div>


            <div class="dropdown-custom-design-area"
                 ng-if="hearingItem.uiType === '4' && hearingItem.settings.pulldownCustomDesign">
              <span>
                <label for="">背景色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_pulldown{{listId}}_backgroundColor"
                       ng-model="hearingItem.settings.customDesign.backgroundColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertPulldownColor(setActionId, listId, 'backgroundColor')">標準に戻す</span>
              </span>
              <span>
                <label for="">文字色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_pulldown{{listId}}_textColor"
                       ng-model="hearingItem.settings.customDesign.textColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertPulldownColor(setActionId, listId, 'textColor')">標準に戻す</span>
              </span>

              <span>
                <label for="">枠線色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_pulldown{{listId}}_borderColor"
                       ng-model="hearingItem.settings.customDesign.borderColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertPulldownColor(setActionId, listId, 'borderColor')">標準に戻す</span>
              </span>
            </div>
            <div ng-if="hearingItem.uiType === '5'" class="calendar-design-custom-area">
              <label class="pointer">
                <input type="checkbox" ng-model="hearingItem.settings.disablePastDate">過去日を選択できなくする
                <span class="questionBalloon"><icon class="questionBtn"
                                                    data-tooltip="サイト訪問日より過去の日付を選択できなくします。（過去の日付を選択できるようにする場合はチェックを外します）">?</icon></span>
              </label>
              <br>
              <label class="pointer">
                <input type="checkbox" ng-model="hearingItem.settings.isEnableAfterDate">当日から<input
                    style="width: 6em"
                    type="number" min="1" ng-disabled="!hearingItem.settings.isEnableAfterDate"
                    ng-model="hearingItem.settings.enableAfterDate">日以降を選択できるようにする
                <span class="questionBalloon"><icon class="questionBtn"
                                                    data-tooltip="サイト訪問日から設定した日数以降を選択できるようにします。例えば、当日は選択できなくし翌日以降から選択可能とする場合は「1」と設定します。">?</icon></span>
              </label>
              <br>
              <label class="pointer">
                <input type="checkbox" ng-model="hearingItem.settings.isDisableAfterDate">当日から<input
                    style="width: 6em"
                    type="number" min="0" ng-disabled="!hearingItem.settings.isDisableAfterDate"
                    ng-model="hearingItem.settings.disableAfterDate">日以降を選択できないようにする
                <span class="questionBalloon"><icon class="questionBtn"
                                                    data-tooltip="サイト訪問日から設定した日数以降を選択できないようにします。">?</icon></span>
              </label>
              <br>
              <div class="cannot-select-date-setting-area">
                <label class="pointer">
                  <input type="checkbox" id="set_cannot_select_date"
                         ng-model="hearingItem.settings.isSetDisableDate">選択できない日付を設定する（定休日など）
                  <span class="questionBalloon"><icon class="questionBtn"
                                                      data-tooltip="選択できない曜日や日付（または選択できる日付）を設定することができます。">?</icon></span>
                </label>
                <br>
                <div class="cannot-select-date" ng-if="hearingItem.settings.isSetDisableDate">
                  <label class="pointer specific-day-of-week" style="margin-left: 20px;">
                    <input type="checkbox" id="cannot_select_day_of_week"
                           ng-model="hearingItem.settings.isDisableDayOfWeek">特定の曜日を選択できなくする
                    <span class="questionBalloon"><icon class="questionBtn"
                                                        data-tooltip="チェックが付いた曜日は選択できなくなります。">?</icon></span>
                  </label>
                  <br>
                  <div class="weekday-list m40l" ng-if="hearingItem.settings.isDisableDayOfWeek">
                    <label class="pointer specific-date" style="margin-left: 5px;">
                      <input type="checkbox"
                             ng-model="hearingItem.settings.dayOfWeekSetting[0]">日
                    </label>
                    <label class="pointer specific-date" style="margin-left: 5px;">
                      <input type="checkbox"
                             ng-model="hearingItem.settings.dayOfWeekSetting[1]">月
                    </label>
                    <label class="pointer specific-date" style="margin-left: 5px;">
                      <input type="checkbox"
                             ng-model="hearingItem.settings.dayOfWeekSetting[2]">火
                    </label>
                    <label class="pointer specific-date" style="margin-left: 5px;">
                      <input type="checkbox"
                             ng-model="hearingItem.settings.dayOfWeekSetting[3]">水
                    </label>
                    <label class="pointer specific-date" style="margin-left: 5px;">
                      <input type="checkbox"
                             ng-model="hearingItem.settings.dayOfWeekSetting[4]">木
                    </label>
                    <label class="pointer specific-date" style="margin-left: 5px;">
                      <input type="checkbox"
                             ng-model="hearingItem.settings.dayOfWeekSetting[5]">金
                    </label>
                    <label class="pointer specific-date" style="margin-left: 5px;">
                      <input type="checkbox"
                             ng-model="hearingItem.settings.dayOfWeekSetting[6]">土
                    </label>
                  </div>
                  <div class="cannot-select-specific-date-area">
                    <label class="pointer specific-date m20l">
                      <input type="checkbox" ng-model="hearingItem.settings.isSetSpecificDate">特定の日付を選択できなくする
                      <span class="questionBalloon"><icon class="questionBtn"
                                                          data-tooltip="下記いずれかの日付の設定が可能です。<br>・選択できない日付の設定<br>・選択できる日付の設定">?</icon></span>
                    </label>
                    <div class="cannot-select-specific-date"
                         ng-if="hearingItem.settings.isSetSpecificDate">
                      <label class="pointer m40l">
                        <input type="radio" name="set-specific-date" value="1"
                               ng-model="hearingItem.settings.setSpecificDateType">選択できない日付を指定する
                      </label>
                      <br>
                      <div class="select-option-input action{{setActionId}}_option{{listId}}"
                           style="margin-left: 60px;"
                           ng-repeat="(dateIndex, date) in hearingItem.settings.specificDateData track by $index"
                           ng-if="hearingItem.settings.setSpecificDateType == 1">
                        <input type="text" ng-model="hearingItem.settings.specificDateData[dateIndex]"
                               id="action{{setActionId}}_option{{listId}}_datepicker{{dateIndex}}">
                        <div class="btnBlock">
                          <a><?= $this->Html->image('add.png', array(
                              'alt' => '追加',
                              'width' => 25,
                              'height' => 25,
                              'class' => 'btn-shadow disOffgreenBtn',
                              'style' => 'padding: 2px',
                              'ng-click' => 'main.addHearingOption($event, hearingItem.uiType, dateIndex, listId)'
                            )) ?></a>
                          <a><?= $this->Html->image('dustbox.png', array(
                              'alt' => '削除',
                              'width' => 25,
                              'height' => 25,
                              'class' => 'btn-shadow redBtn deleteBtn',
                              'style' => 'padding: 2px; display: none;',
                              'ng-click' => 'main.removeHearingOption($event, hearingItem.uiType, dateIndex, listId)'
                            )) ?></a>
                        </div>
                        <a href="" class="greenBtn btn-shadow bulk-button commontooltip"
                           data-text="設定する日付をテキストエリア内で改行して一括で登録することができます。"
                           ng-click="main.showBulkSelectionPopup(setActionId, listId, hearingItem.uiType);"
                           ng-if="!dateIndex">日付を一括登録</a>
                      </div>

                      <label class="pointer m40l">
                        <input type="radio" name="set-specific-date" value="2"
                               ng-model="hearingItem.settings.setSpecificDateType">選択できる日付を指定する
                      </label>
                      <br>
                      <div class="select-option-input action{{setActionId}}_option{{listId}} m60l"
                           ng-repeat="(dateIndex, date) in hearingItem.settings.specificDateData track by $index"
                           ng-if="hearingItem.settings.setSpecificDateType == 2">
                        <input class="mock-calendar"
                               ng-model="hearingItem.settings.specificDateData[dateIndex]"
                               id="action{{setActionId}}_option{{listId}}_datepicker{{dateIndex}}" type="text">
                        <div class="btnBlock">
                          <a><?= $this->Html->image('add.png', array(
                              'alt' => '追加',
                              'width' => 25,
                              'height' => 25,
                              'class' => 'btn-shadow disOffgreenBtn',
                              'style' => 'padding: 2px',
                              'ng-click' => 'main.addHearingOption($event, hearingItem.uiType, dateIndex, listId)'
                            )) ?></a>
                          <a><?= $this->Html->image('dustbox.png', array(
                              'alt' => '削除',
                              'width' => 25,
                              'height' => 25,
                              'class' => 'btn-shadow redBtn deleteBtn',
                              'style' => 'padding: 2px; display: none;',
                              'ng-click' => 'main.removeHearingOption($event, hearingItem.uiType, dateIndex, listId)'
                            )) ?></a>
                        </div>
                        <a href="" class="greenBtn btn-shadow bulk-button commontooltip"
                           data-text="設定する日付をテキストエリア内で改行して一括で登録することができます。"
                           ng-click="main.showBulkSelectionPopup(setActionId, listId, hearingItem.uiType);"
                           ng-if="!dateIndex">日付を一括登録</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <label class="pointer"">
              <input type="checkbox" ng-model="hearingItem.settings.isCustomDesign">デザインをカスタマイズする
              <span class="questionBalloon"><icon class="questionBtn"
                                                  data-tooltip="カレンダーのデザイン（配色）を自由にカスタマイズすることができます。">?</icon></span>
              </label>

              <div class="calendar-design-custom"
                   ng-if="hearingItem.settings.isCustomDesign" style="margin-left: 20px">
                <span class="calendar-custom-items">
                  <label>ヘッダー背景色</label>
                  <input type="text" id="action{{setActionId}}_option{{listId}}_headerBackgroundColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.headerBackgroundColor"
                         ng-change="main.changeCalendarHeaderColor(setActionId, listId, 'headerBackgroundColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCalendarColor(setActionId, listId, 'headerBackgroundColor')">標準に戻す</span>
                </span>
                <span class="calendar-custom-items">
                  <label>ヘッダー文字色</label>
                  <input type="text" id="action{{setActionId}}_option{{listId}}_headerTextColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.headerTextColor"
                         ng-change="main.changeCalendarHeaderColor(setActionId, listId, 'headerTextColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCalendarColor(setActionId, listId, 'headerTextColor')">標準に戻す</span>
                </span>
                <span class="calendar-custom-items">
                  <label>曜日背景色</label>
                  <input type="text" id="action{{setActionId}}_option{{listId}}_headerWeekdayBackgroundColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.headerWeekdayBackgroundColor"
                         ng-change="main.changeCalendarHeaderColor(setActionId, listId, 'headerWeekdayBackgroundColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCalendarColor(setActionId, listId, 'headerWeekdayBackgroundColor')">標準に戻す</span>
                </span>
                <span class="calendar-custom-items">
                  <label>枠線色</label>
                  <input type="text" id="action{{setActionId}}_option{{listId}}_borderColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.borderColor"
                         ng-change="main.changeCalendarHeaderColor(setActionId, listId, 'borderColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCalendarColor(setActionId, listId, 'borderColor')">標準に戻す</span>
                </span>
                <span class="calendar-custom-items">
                  <label>カレンダ背景色</label>
                  <input type="text" id="action{{setActionId}}_option{{listId}}_calendarBackgroundColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.calendarBackgroundColor"
                         ng-change="main.changeCalendarHeaderColor(setActionId, listId, 'calendarBackgroundColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCalendarColor(setActionId, listId, 'calendarBackgroundColor')">標準に戻す</span>
                </span>
                <span class="calendar-custom-items">
                  <label>カレンダ文字色</label>
                  <input type="text" id="action{{setActionId}}_option{{listId}}_calendarTextColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.calendarTextColor"
                         ng-change="main.changeCalendarHeaderColor(setActionId, listId, 'calendarTextColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCalendarColor(setActionId, listId, 'calendarTextColor')">標準に戻す</span>
                </span>
                <span class="calendar-custom-items">
                  <label>土曜日文字色</label>
                  <input type="text" id="action{{setActionId}}_option{{listId}}_saturdayColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.saturdayColor"
                         ng-change="main.changeCalendarHeaderColor(setActionId, listId, 'saturdayColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCalendarColor(setActionId, listId, 'saturdayColor')">標準に戻す</span>
                </span>
                <span class="calendar-custom-items">
                  <label>日曜日文字色</label>
                  <input type="text" id="action{{setActionId}}_option{{listId}}_sundayColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.sundayColor"
                         ng-change="main.changeCalendarHeaderColor(setActionId, listId, 'sundayColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCalendarColor(setActionId, listId, 'sundayColor')">標準に戻す</span>
                </span>

                <span class="language-setting">
                  <label>言語</label>
                  <label class="pointer"><input type="radio" name="action{{setActionId}}-hearing{{listId}}-language"
                                                value="1"
                                                ng-model="hearingItem.settings.language"
                                                style="margin-left: 60px;">日本語表記</label>
                  <label class="pointer m20l"><input type="radio"
                                                     name="action{{setActionId}}-hearing{{listId}}-language" value="2"
                                                     ng-model="hearingItem.settings.language">英語表記</label>
                </span>
              </div>
            </div>

            <label ng-if="hearingItem.uiType === '7'" class="pointer">
              <input type="checkbox" ng-model="hearingItem.settings.isCustomDesign">デザインをカスタマイズする
              <span class="questionBalloon"><icon class="questionBtn"
                                                  data-tooltip="ボタンのデザイン（配色）を自由にカスタマイズすることができます。">?</icon></span>
            </label>
            <div ng-if="hearingItem.uiType === '7' && hearingItem.settings.isCustomDesign"
                 class="button-design-custom-area">
                  <span class="button-custom-items">
                  <label style="width: 100px;">質問内容位置</label>
                      <div class="radio-buttons">
                        <label class="radio-label text3 pointer"
                               for="action{{setActionId}}_button{{listId}}_messageAlign1">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_messageAlign1"
                                 ng-model="hearingItem.settings.customDesign.messageAlign"
                                 value="1">左寄せ</label>
                        <label class="radio-label text4 pointer"
                               for="action{{setActionId}}_button{{listId}}_messageAlign2">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_messageAlign2"
                                 ng-model="hearingItem.settings.customDesign.messageAlign"
                                 value="2">中央寄せ</label>
                        <label class="radio-label text3 pointer"
                               for="action{{setActionId}}_button{{listId}}_messageAlign3">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_messageAlign3"
                                 ng-model="hearingItem.settings.customDesign.messageAlign"
                                 value="3">右寄せ</label>
                      </div>
                </span>
              <span class="button-custom-items">
                  <label>ボタン背景色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_buttonBackgroundColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.buttonBackgroundColor"
                         ng-change="main.changeButtonColor(setActionId, listId, 'buttonBackgroundColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertButtonColor(setActionId, listId, 'buttonBackgroundColor')">標準に戻す</span>
                </span>
              <span class="button-custom-items">
                  <label>ボタン文字色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_buttonTextColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.buttonTextColor"
                         ng-change="main.changeButtonColor(setActionId, listId, 'buttonTextColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertButtonColor(setActionId, listId, 'buttonTextColor')">標準に戻す</span>
                </span>
              <span class="button-custom-items">
                  <label style="width: 100px;">ボタン文字位置</label>
                      <div class="radio-buttons">
                        <label class="radio-label text3 pointer"
                               for="action{{setActionId}}_button{{listId}}_buttonAlign1">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_buttonAlign1"
                                 ng-model="hearingItem.settings.customDesign.buttonAlign"
                                 value="1">左寄せ</label>
                        <label class="radio-label text4 pointer"
                               for="action{{setActionId}}_button{{listId}}_buttonAlign2">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_buttonAlign2"
                                 ng-model="hearingItem.settings.customDesign.buttonAlign"
                                 value="2">中央寄せ</label>
                        <label class="radio-label text3 pointer"
                               for="action{{setActionId}}_button{{listId}}_buttonAlign3">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_buttonAlign3"
                                 ng-model="hearingItem.settings.customDesign.buttonAlign"
                                 value="3">右寄せ</label>
                      </div>
                </span>
              <span class="button-custom-items">
                  <label>ボタン選択色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_buttonActiveColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.buttonActiveColor"
                         ng-change="main.changeButtonColor(setActionId, listId, 'buttonActiveColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertButtonColor(setActionId, listId, 'buttonActiveColor')">標準に戻す</span>
                </span>
              <span class="button-custom-items">
                <label>ボタン枠線色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_button{{listId}}_buttonBorderColor"
                       ng-model="hearingItem.settings.customDesign.buttonBorderColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertButtonColor(setActionId, listId, 'buttonBorderColor')">標準に戻す</span>
              </span>

              <label class="pointer" style="margin-left: 116px">
                <input type="checkbox" style="margin-top: 5px; margin-bottom: 10px;"
                       ng-model="hearingItem.settings.customDesign.outButtonNoneBorder" value="1">枠線なしにする
              </label>
            </div>

            <label ng-if="hearingItem.uiType === '8'" class="pointer">
              <input type="checkbox" ng-model="hearingItem.settings.buttonUICustomDesign">デザインをカスタマイズする
              <span class="questionBalloon"><icon class="questionBtn"
                                                  data-tooltip="ボタンのデザイン（配色）を自由にカスタマイズすることができます。">?</icon></span>
            </label>
            <div ng-if="hearingItem.uiType === '8' && hearingItem.settings.buttonUICustomDesign"
                 class="button-design-custom-area">
              <span class="button-custom-items">
                  <label>ボタン背景色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_buttonUIBackgroundColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.buttonUIBackgroundColor">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertButtonUIColor(setActionId, listId, 'buttonUIBackgroundColor')">標準に戻す</span>
                </span>
              <span class="button-custom-items">
                  <label>ボタン文字色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_buttonUITextColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.buttonUITextColor"
                         ng-change="main.changeButtonColor(setActionId, listId, 'buttonUITextColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertButtonUIColor(setActionId, listId, 'buttonUITextColor')">標準に戻す</span>
                </span>
              <span class="button-custom-items">
                  <label style="width: 100px;">ボタン文字位置</label>
                      <div class="radio-buttons">
                        <label class="radio-label text3 pointer"
                               for="action{{setActionId}}_button{{listId}}_buttonAlign1">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_buttonAlign1"
                                 ng-model="hearingItem.settings.customDesign.buttonUITextAlign"
                                 value="1">左寄せ</label>
                        <label class="radio-label text4 pointer"
                               for="action{{setActionId}}_button{{listId}}_buttonAlign2">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_buttonAlign2"
                                 ng-model="hearingItem.settings.customDesign.buttonUITextAlign"
                                 value="2">中央寄せ</label>
                        <label class="radio-label text3 pointer"
                               for="action{{setActionId}}_button{{listId}}_buttonAlign3">
                          <input type="radio"
                                 id="action{{setActionId}}_button{{listId}}_buttonAlign3"
                                 ng-model="hearingItem.settings.customDesign.buttonUITextAlign"
                                 value="3">右寄せ</label>
                      </div>
                </span>
              <span class="button-custom-items">
                  <label>ボタン選択色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_buttonUIActiveColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.buttonUIActiveColor"
                         ng-change="main.changeButtonColor(setActionId, listId, 'buttonUIActiveColor')">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertButtonUIColor(setActionId, listId, 'buttonUIActiveColor')">標準に戻す</span>
                </span>
              <span class="button-custom-items">
                <label>ボタン枠線色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_button{{listId}}_buttonUIBorderColor"
                       ng-model="hearingItem.settings.customDesign.buttonUIBorderColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertButtonUIColor(setActionId, listId, 'buttonUIBorderColor')">標準に戻す</span>
              </span>

              <label class="pointer" style="margin-left: 116px">
                <input type="checkbox" style="margin-top: 5px; margin-bottom: 10px;"
                       ng-model="hearingItem.settings.outButtonUINoneBorder">枠線なしにする
              </label>
            </div>

            <label ng-if="hearingItem.uiType === '9'" class="pointer">
              <input type="checkbox" ng-model="hearingItem.settings.checkboxCustomDesign">デザインをカスタマイズする
              <span class="questionBalloon"><icon class="questionBtn"
                                                  data-tooltip="ボタンのデザイン（配色）を自由にカスタマイズすることができます。">?</icon></span>
            </label>
            <div ng-if="hearingItem.uiType === '9' && hearingItem.settings.checkboxCustomDesign"
                 class="checkbox-design-custom-area">
              <span class="checkbox-custom-items">
                  <label>チェックボックス背景色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_checkboxBackgroundColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.checkboxBackgroundColor">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCheckboxColor(setActionId, listId, 'checkboxBackgroundColor')">標準に戻す</span>
                </span>
              <span class="checkbox-custom-items">
                  <label>チェックON時の背景色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_checkboxActiveColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.checkboxActiveColor">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCheckboxColor(setActionId, listId, 'checkboxActiveColor')">標準に戻す</span>
              </span>
              <span class="checkbox-custom-items">
                  <label>チェック色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_checkboxCheckmarkColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.checkboxCheckmarkColor">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertCheckboxColor(setActionId, listId, 'checkboxCheckmarkColor')">標準に戻す</span>
              </span>
              <span class="checkbox-custom-items">
                <label>チェックボックス枠線色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_button{{listId}}_checkboxBorderColor"
                       ng-model="hearingItem.settings.customDesign.checkboxBorderColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertCheckboxColor(setActionId, listId, 'checkboxBorderColor')">標準に戻す</span>
              </span>

              <label class="pointer" style="margin-left: 165px">
                <input type="checkbox" style="margin-top: 5px; margin-bottom: 10px;"
                       ng-model="hearingItem.settings.checkboxNoneBorder">枠線なしにする
              </label>
            </div>

            <label ng-if="hearingItem.uiType === '3'" class="pointer">
              <input type="checkbox" ng-model="hearingItem.settings.radioCustomDesign">デザインをカスタマイズする
              <span class="questionBalloon"><icon class="questionBtn"
                                                  data-tooltip="ボタンのデザイン（配色）を自由にカスタマイズすることができます。">?</icon></span>
            </label>
            <div ng-if="hearingItem.uiType === '3' && hearingItem.settings.radioCustomDesign"
                 class="checkbox-design-custom-area">
              <span class="checkbox-custom-items">
                  <label>ラジオボタン背景色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_radioBackgroundColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.radioBackgroundColor">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertRadioButtonColor(setActionId, listId, 'radioBackgroundColor')">標準に戻す</span>
                </span>
              <span class="checkbox-custom-items">
                  <label>ラジオボタンの色</label>
                  <input type="text" id="action{{setActionId}}_button{{listId}}_radioActiveColor"
                         class="jscolor{hash:true} ignore-click-event"
                         ng-model="hearingItem.settings.customDesign.radioActiveColor">
                  <span class="greenBtn btn-shadow revert-button"
                        ng-click="main.revertRadioButtonColor(setActionId, listId, 'radioActiveColor')">標準に戻す</span>
              </span>

              <span class="checkbox-custom-items">
                <label>ラジオボタン枠線色</label>
                <input type="text" class="jscolor{hash:true} ignore-click-event"
                       id="action{{setActionId}}_button{{listId}}_radioBorderColor"
                       ng-model="hearingItem.settings.customDesign.radioBorderColor">
                <span class="greenBtn btn-shadow revert-button"
                      ng-click="main.revertRadioButtonColor(setActionId, listId, 'radioBorderColor')">標準に戻す</span>
              </span>

              <label class="pointer" style="margin-left: 165px">
                <input type="checkbox" style="margin-top: 5px; margin-bottom: 10px;"
                       ng-model="hearingItem.settings.radioNoneBorder">枠線なしにする
              </label>
            </div>
          </div>
          <hr class="separator">
        </li>

        <li>
          <label class="pointer"><input type="checkbox" ng-model="setItem.restore">
            入力内容の復元機能を有効にする
            <span class="questionBalloon">
                    <icon class="questionBtn" data-tooltip="再入力時や再訪問時に回答済みの内容を復元することができます。（復元させない場合はチェックを外します）">?</icon>
                </span>
          </label>
        </li>
        <li>
          <label class="pointer"><input type="checkbox" ng-model="setItem.isConfirm"
                                        ng-init="setItem.isConfirm = setItem.isConfirm == 1">入力内容の確認を行う<span
                class="questionBalloon"><icon class="questionBtn"
                                              data-tooltip="質問内容を全て聞き終えた後に、サイト訪問者に確認メッセージを送ることが出来ます。">?</icon></span></label>
          <ul ng-if="setItem.isConfirm == true" class="indentDown">
            <li class="styleFlexbox">
                    <span class="fb9em"><label>確認内容<span class="questionBalloon"><icon class="questionBtn"
                                                                                       data-tooltip="確認メッセージとして送信するメッセージを設定します。<br><br>＜設定例＞<br>お名前　　　　：{{showExpression('名前')}}<br>電話番号　　　：{{showExpression('電話番号')}}<br>メールアドレス：{{showExpression('メールアドレス')}}<br>でよろしいでしょうか？">?</icon></span></label></span>

              <div>
                <resize-textarea class="variable-suggest" name="confirmMessage" ng-model="setItem.confirmMessage" cols="48" rows="1"
                                 placeholder="確認内容のメッセージを入力してください" data-maxRow="10"></resize-textarea>
              </div>
            </li>
            <li class="styleFlexbox">
                    <span class="fb9em"><label>選択肢（OK）<span class="questionBalloon"><icon class="questionBtn"
                                                                                          data-tooltip="OK（次のアクションを実行）の場合の選択肢の名称を設定します。">?</icon></span></label></span>
              <div>
                <input type="text" name="success" class="variable-suggest" ng-model="setItem.success">
              </div>
            </li>
            <li class="styleFlexbox">
                    <span class="fb9em"><label>選択肢（NG）<span class="questionBalloon"><icon class="questionBtn"
                                                                                          data-tooltip="NG（再入力）の場合の選択肢の名称を設定します。">?</icon></span></label></span>
              <div>
                <input type="text" name="cancel" class="variable-suggest" ng-model="setItem.cancel">
              </div>
            </li>
          </ul>
        </li>
        <li>
          <label class="pointer"><input type="checkbox" ng-model="setItem.cv" ng-init="setItem.cv = setItem.cv == 1">成果にCVとして登録する<span
                class="questionBalloon"><icon class="questionBtn"
                                              data-tooltip="チャット履歴の「成果」に「途中離脱」または「CV」として自動登録します。<br><br>【途中離脱】ヒアリング途中で終了した場合<br>【CV】全項目のヒアリングが完了した場合（入力内容の確認を行う場合は「OK」が選択された場合）">?</icon></span></label>
        </li>
      </ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>





