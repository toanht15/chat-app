<?php /* ヒアリング */ ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                                                                              data-tooltip="ヒアリングの回答を入力する形式を指定します。<br>＜タイプ＞<br>テキスト(1行)　 　 ：フリーテキスト入力（改行不可）<br>テキスト(複数行)　 ：フリーテキスト入力（改行可）<br>ラジオボタン　　　：ラジオボタン形式の択一選択<br>プルダウン　　　　：プルダウン形式の択一選択<br>カレンダー　　　　：カレンダーから日付を選択"
            data-tooltip-width="30em">?</icon></span>
</div>
<div class='area-message'>質問内容<span class="questionBalloon"><icon class="questionBtn"
                                                                  data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。">?</icon></span>
</div>

</div>
            <hr>
            <ul ui-sortable="sortableOptionsHearing" class="input-one-row hearing-input-type sortable">
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
                    <div class='area-name'><input type="text" ng-model="hearingItem.variableName"></div>
                    <div class='area-type'>
                        <select name="hearing-input-option" ng-model="hearingItem.uiType" ng-init="hearingItem.uiType = '1'" ng-change="main.controllSelectionView(<?= C_SCENARIO_ACTION_HEARING ?>, setActionId, listId, hearingItem.uiType)">
                            <option value="1">テキスト（１行）</option>
                            <option value="2">テキスト（複数行）</option>
                            <option value="3">ラジオボタン</option>
                            <option value="4">プルダウン</option>
                            <option value="5">カレンダー</option>
                        </select>
                    </div>
                    <div class='area-message'>
                        <resize-textarea maxlength="4000" ng-model="hearingItem.message" rows="1"
                                         data-maxRow="10"></resize-textarea>
                    </div>
                    <div class='area-btn'>
                        <div class="btnBlock">
                            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
                        </div>
                    </div>
                    <div class='area-detail'>
                        <span ng-if="hearingItem.uiType === '1'">
                            <label>テキストタイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                              data-tooltip="サイト訪問者が入力した回答が適切か、整合性チェックを行うことができます。<br>入力内容が不適切だった場合（整合性チェックNGだった場合）は、「入力エラー時の返信メッセージ」に設定されたメッセージを自動送信後、再度ヒアリングを実施します。<br><br>＜タイプ＞<br>text　　　　：制限なし<br>number　　 ：数字のみ<br>email　　　：メールアドレス形式のみ<br>tel_number：0から始まる10桁以上の数字とハイフンのみ">?</icon></span></label>
                            <label class="pointer"><input type="radio" name="hearing-one-row-type" value="1"
                                                          ng-model="hearingItem.inputType">text</label>
                            <label class="pointer"><input type="radio" name="hearing-one-row-type"
                                                          value="2" ng-model="hearingItem.inputType">number</label>
                            <label class="pointer"><input type="radio" name="hearing-one-row-type"
                                                          value="3" ng-model="hearingItem.inputType">email</label>
                            <label class="pointer"><input type="radio" name="hearing-one-row-type" value="4"
                                                          ng-model="hearingItem.inputType">tel</label>
                        </span>
                        <span ng-if="hearingItem.uiType === '2'">
                            <label>テキストタイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                              data-tooltip="サイト訪問者が入力した回答が適切か、整合性チェックを行うことができます。<br>入力内容が不適切だった場合（整合性チェックNGだった場合）は、「入力エラー時の返信メッセージ」に設定されたメッセージを自動送信後、再度ヒアリングを実施します。<br><br>＜タイプ＞<br>text　　　　：制限なし<br>number　　 ：数字のみ">?</icon></span></label>
                            <label class="pointer"><input type="radio" name="hearing-multiple-input-type" value="1"
                                                          ng-model="hearingItem.inputType">text</label>
                            <label class="pointer"><input type="radio" name="hearing-multiple-input-type"
                                                          value="2" ng-model="hearingItem.inputType">number</label>
                        </span>

                        <div ng-if="(hearingItem.uiType === '1' && hearingItem.inputType != 1) || (hearingItem.uiType === '2' &&  hearingItem.inputType != 1)" class="styleFlexbox m15t">
                            <span class="fb11em"><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ
                                    <span class="questionBalloon">
                                        <icon class="questionBtn"
                                              data-tooltip="サイト訪問者が入力した回答が不適切だった場合（各テキストタイプの整合性チェックNGだった場合）に自動返信するメッセージを設定します。">?</icon>
                                    </span>
                                </label>
                            </span>
                            <div>
                                <resize-textarea name="errorMessage" maxlength="4000" ng-model="setItem.errorMessage"
                                                 cols="48"
                                                 rows="1" placeholder="入力エラー時の返信メッセージを入力してください"
                                                 data-maxRow="10"></resize-textarea>
                            </div>
                        </div>

                        <div ng-if="hearingItem.uiType === '3' || hearingItem.uiType === '4'" ng-repeat="(optionIndex, option) in hearingItem.options[hearingItem.uiType]  track by $index" class="select-option-input action{{setActionId}}_option{{listId}}">
                            <span><label class="">選択肢 {{optionIndex + 1}}<span class="questionBalloon"><icon class="questionBtn"
                                                                                         data-tooltip="選択肢を1つずつ設定します。<br>例）選択肢１：男性<br>選択肢２：女性">?</icon></span></label></span>
                            <input type="text" class="m20l" ng-model="hearingItem.options[hearingItem.uiType][optionIndex]"  style="width: 200px;">
                            <div class="btnBlock">
                                <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addHearingOption($event, hearingItem.uiType, optionIndex, listId)')) ?></a>
                                <a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px; display: none;', 'ng-click' => 'main.removeHearingOption($event, hearingItem.uiType, optionIndex, listId)')) ?></a>
                            </div>
                            <a ng-if="!optionIndex" href="" class="greenBtn btn-shadow bulk-button"
                               style="display: inline-flex; margin-top: 5px" ng-click="main.showBulkSelectionPopup(setActionId, listId, hearingItem.uiType);"> 選択肢を一括登録</a>
                        </div>

                        <label ng-if="hearingItem.uiType === '4'" class="pointer">
                            <input type="checkbox" class="m15t" id="dropdown_custom_design" ng-model="hearingItem.options.pulldownCustomDesign">デザインをカスタマイズする
                            <span class="questionBalloon"><icon class="questionBtn"
                                                                data-tooltip="プルダウンのデザイン（配色）を自由にカスタマイズすることができます。">?</icon></span>
                        </label>

                        <div class="dropdown-custom-design-area" ng-if="hearingItem.uiType === '4' && hearingItem.options.pulldownCustomDesign">
                           <span>
                               <label for="">背景色</label>
                               <input type="text" class="jscolor{hash:true} ignore-click-event" id="action{{setActionId}}_pulldown{{listId}}_backgroundColor"  ng-model="hearingItem.customDesign[4].backgroundColor">
                               <span class="greenBtn btn-shadow revert-button"  ng-click="main.revertPulldownColor(setActionId, listId, 'backgroundColor')">標準に戻る</span>
                           </span>

                           <span>
                               <label for="">文字色</label>
                               <input type="text" class="jscolor{hash:true} ignore-click-event" id="action{{setActionId}}_pulldown{{listId}}_textColor"  ng-model="hearingItem.customDesign[4].textColor">
                               <span class="greenBtn btn-shadow revert-button"  ng-click="main.revertPulldownColor(setActionId, listId, 'textColor')">標準に戻る</span>
                           </span>

                            <span>
                                <label for="">枠線色</label>
                                <input type="text" class="jscolor{hash:true} ignore-click-event" id="action{{setActionId}}_pulldown{{listId}}_borderColor" ng-model="hearingItem.customDesign[4].borderColor">
                                <span class="greenBtn btn-shadow revert-button"  ng-click="main.revertPulldownColor(setActionId, listId, 'borderColor')">標準に戻る</span>
                              </span>

                            <span>
                                <label for="">▼マーク色</label>
                                <input type="text" class="jscolor{hash:true} ignore-click-event" id="action{{setActionId}}_pulldown{{listId}}_markColor" ng-model="hearingItem.customDesign[4].markColor">
                                <span class="greenBtn btn-shadow revert-button"  ng-click="main.revertPulldownColor(setActionId, listId, 'markColor')">標準に戻る</span>
                              </span>
                        </div>
                        <div ng-if="hearingItem.uiType === '5'" class="calendar-design-custom-area">
                            <label class="pointer">
                                <input type="checkbox" ng-model="hearingItem.options[hearingItem.uiType].disablePastDate">過去日を選択できなくする
                                <span class="questionBalloon"><icon class="questionBtn"
                                                                    data-tooltip="サイト訪問日より過去の日付を選択できなくします。（過去の日付を選択できるようにする場合はチェックを外します）">?</icon></span>
                            </label>
                            <br>
                            <label class="pointer">
                                <input type="checkbox" ng-model="hearingItem.options[hearingItem.uiType].isEnableAfterDate" >当日から<input style="width: 6em"
                                        type="number" min="1" ng-disabled="!hearingItem.options[hearingItem.uiType].isEnableAfterDate" ng-model="hearingItem.options[hearingItem.uiType].enableAfterDate">日以降を選択できるようにする
                                <span class="questionBalloon"><icon class="questionBtn"
                                                                    data-tooltip="サイト訪問日より過去の日付を選択できなくします。（過去の日付を選択できるようにする場合はチェックを外します）">?</icon></span>
                            </label>
                            <br>
                            <div class="cannot-select-date-setting-area">
                                <label class="pointer">
                                    <input type="checkbox" id="set_cannot_select_date" ng-model="hearingItem.options[hearingItem.uiType].isSetDisableDate">選択できない日付を設定する（定休日など）
                                    <span class="questionBalloon"><icon class="questionBtn"
                                                                        data-tooltip="選択できない曜日や日付（または選択できる日付）を設定することができます。">?</icon></span>
                                </label>
                                <br>
                                <div class="cannot-select-date" ng-if="hearingItem.options[hearingItem.uiType].isSetDisableDate">
                                    <label class="pointer specific-day-of-week" style="margin-left: 20px;">
                                        <input type="checkbox" id="cannot_select_day_of_week" ng-model="hearingItem.options[hearingItem.uiType].isDisableDayOfWeek">特定の曜日を選択できなくする
                                        <span class="questionBalloon"><icon class="questionBtn"
                                                                            data-tooltip="チェックが付いた曜日は選択できなくなります。">?</icon></span>
                                    </label>
                                    <br>
                                    <div class="weekday-list m40l" ng-if="hearingItem.options[hearingItem.uiType].isDisableDayOfWeek">
                                        <label class="pointer specific-date" ng-repeat="(index, day) in hearingItem.options[hearingItem.uiType].dayOfWeekSetting" style="margin-left: 5px;">
                                            <input type="checkbox" ng-model="hearingItem.options[hearingItem.uiType].dayOfWeekSetting[index].value">{{day.label}}
                                        </label>
                                    </div>
                                    <div class="cannot-select-specific-date-area">
                                        <label class="pointer specific-date m20l">
                                            <input type="checkbox" ng-model="hearingItem.options[hearingItem.uiType].isSetSpecificDate">特定の日付を選択できなくする
                                            <span class="questionBalloon"><icon class="questionBtn"
                                                                                data-tooltip="下記いずれかの日付の設定が可能です。<br>・選択できない日付の設定<br>・選択できる日付の設定">?</icon></span>
                                        </label>
                                        <div class="cannot-select-specific-date" ng-if="hearingItem.options[hearingItem.uiType].isSetSpecificDate">
                                            <label class="pointer m40l"><input type="radio" name="set-specific-date" value="1" ng-model="hearingItem.options[hearingItem.uiType].setSpecificDateType">選択できない日付を指定する</label>
                                            <br>
                                            <div class="select-option-input action{{setActionId}}_option{{listId}}"
                                                 style="margin-left: 60px;" ng-repeat="(dateIndex, date) in hearingItem.options[hearingItem.uiType].specificDateData[1] track by $index" ng-if="hearingItem.options[hearingItem.uiType].setSpecificDateType == 1">
                                                <input class="mock-calendar" type="text" ng-model="hearingItem.options[hearingItem.uiType].specificDateData[1][dateIndex]" id="action{{setActionId}}_option{{listId}}_datepicker{{dateIndex}}">
                                                <div class="btnBlock">
                                                    <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addHearingOption($event, hearingItem.uiType, dateIndex, listId)')) ?></a>
                                                    <a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px; display: none;', 'ng-click' => 'main.removeHearingOption($event, hearingItem.uiType, dateIndex, listId)')) ?></a>
                                                </div>
                                                <a href="" class="greenBtn btn-shadow bulk-button"
                                                    ng-click="main.showBulkSelectionPopup(setActionId, listId, hearingItem.uiType);" ng-if="!dateIndex">日付を一括登録</a>
                                            </div>

                                            <label class="pointer m40l">
                                                <input type="radio"
                                                       name="set-specific-date"
                                                       value="2" ng-model="hearingItem.options[hearingItem.uiType].setSpecificDateType">
                                                選択できる日付を指定する
                                            </label>
                                            <br>
                                            <div class="select-option-input action{{setActionId}}_option{{listId}} m60l"
                                                  ng-repeat="(dateIndex, date) in hearingItem.options[hearingItem.uiType].specificDateData[2] track by $index" ng-if="hearingItem.options[hearingItem.uiType].setSpecificDateType == 2">
                                                <input class="mock-calendar" ng-model="hearingItem.options[hearingItem.uiType].specificDateData[2][dateIndex]" id="action{{setActionId}}_option{{listId}}_datepicker{{dateIndex}}" type="text">
                                                <div class="btnBlock">
                                                    <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addHearingOption($event, hearingItem.uiType, dateIndex, listId)')) ?></a>
                                                    <a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px; display: none;', 'ng-click' => 'main.removeHearingOption($event, hearingItem.uiType, dateIndex, listId)')) ?></a>
                                                </div>
                                                <a href="" class="greenBtn btn-shadow bulk-button" ng-click="main.showBulkSelectionPopup(setActionId, listId, hearingItem.uiType);" ng-if="!dateIndex">日付を一括登録</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <label class="pointer"">
                                    <input type="checkbox" ng-model="hearingItem.options[hearingItem.uiType].isCustomDesign">デザインをカスタマイズする
                                    <span class="questionBalloon"><icon class="questionBtn"
                                                                data-tooltip="カレンダーのデザイン（配色）を自由にカスタマイズすることができます。">?</icon></span>
                                </label>

                                <div class="calendar-design-custom" ng-if="hearingItem.options[hearingItem.uiType].isCustomDesign" style="margin-left: 20px">
                                    <span class="calendar-custom-items" ng-repeat="(optionIndex, option) in hearingItem.customDesign[hearingItem.uiType]" >
                                        <label>{{hearingItem.customDesign[hearingItem.uiType][optionIndex].label}}</label>
                                        <input type="text" id="action{{setActionId}}_option{{listId}}_{{optionIndex}}" class="jscolor{hash:true} ignore-click-event" ng-model="hearingItem.customDesign[hearingItem.uiType][optionIndex].value">
                                        <span class="greenBtn btn-shadow revert-button" ng-click="main.revertCalendarColor(setActionId, listId, optionIndex)">標準に戻る</span>
                                    </span>

                                  <span  class="language-setting">
                                    <label>言語</label>
                                    <label class="pointer"><input type="radio" name="language" value="1" ng-model="hearingItem.options[hearingItem.uiType].language" style="margin-left: 90px;">日本語表記</label>
                                    <label class="pointer m20l"><input type="radio" name="language" value="2" ng-model="hearingItem.options[hearingItem.uiType].language">英語表記</label>
                                  </span>
                                </div>
                    </div>
                </div>
                    <hr class="separator">
            </li>
        </li>
        <li>
            <label class="pointer"><input type="checkbox" checked>
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
                        <resize-textarea name="confirmMessage" ng-model="setItem.confirmMessage" cols="48" rows="1"
                                         placeholder="確認内容のメッセージを入力してください" data-maxRow="10"></resize-textarea>
                    </div>
                </li>
                <li class="styleFlexbox">
                    <span class="fb9em"><label>選択肢（OK）<span class="questionBalloon"><icon class="questionBtn"
                                                                                          data-tooltip="OK（次のアクションを実行）の場合の選択肢の名称を設定します。">?</icon></span></label></span>
                    <div>
                        <input type="text" name="success" ng-model="setItem.success">
                    </div>
                </li>
                <li class="styleFlexbox">
                    <span class="fb9em"><label>選択肢（NG）<span class="questionBalloon"><icon class="questionBtn"
                                                                                          data-tooltip="NG（再入力）の場合の選択肢の名称を設定します。">?</icon></span></label></span>
                    <div>
                        <input type="text" name="cancel" ng-model="setItem.cancel">
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





