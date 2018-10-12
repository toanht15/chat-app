<?php /* ヒアリング */ ?>

<div ng-if="setItem.actionType == <?= C_SCENARIO_ACTION_HEARING ?>" class="set_action_item_body action_hearing" ng-init="main.controllHearingSettingView(setActionId)">
  <ul>
      <li>
          <div class='grid-container grid-container-header'>
              <div class='area-require'>必須<span class="questionBalloon"><icon class="questionBtn"
                                                                              data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の回答内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{{showExpression('名前')}}様からのお問い合わせを受付いたしました。">?</icon></span>
              </div>
              <div class='area-name'>変数名<span class="questionBalloon"><icon class="questionBtn"
                                                                            data-tooltip="変数名を設定します。<br>ここで設定した変数名にサイト訪問者の回答内容が保存されます。<br>変数に保存された値（内容）は後続の処理（アクション）で、{{showExpression('変数名')}}と指定することで利用することが可能です。<br><br>例）変数名：名前　⇒　{{showExpression('名前')}}様からのお問い合わせを受付いたしました。">?</icon></span>
              </div>
              <div class='area-type'>タイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                            data-tooltip="サイト訪問者が入力した回答が適切か、整合性チェックを行うことができます。入力内容が不適切だった場合（整合性チェックNGだった場合）は、「入力エラー時の返信メッセージ」に設定されたメッセージを自動送信後、再度ヒアリングを実施します。<br><br>＜タイプ＞<br>@text　　　　：制限なし<br>@number　　：数字のみ<br>@email　　　：メールアドレス形式のみ<br>@tel_number：数字とハイフンのみ"
                                                                            data-tooltip-width="30em">?</icon></span>
              </div>
              <div class='area-message'>質問内容<span class="questionBalloon"><icon class="questionBtn"
                                                                                data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。">?</icon></span>
              </div>
          </div>

          <div class="input-one-row hearing-input-type" id="hearing_input_type_1">
              <hr style="margin: 8px">
              <div class='grid-container grid-container-body itemListGroup'
                   ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
                  <div class="area-drag-symbol">
                      <i class="fas fa-arrows-alt-v fa-2x"></i>
                  </div>
                  <div class="area-require">
                      <label class="require-checkbox">
                          <input type="checkbox">
                          <span class="checkmark"></span>
                      </label>

                  </div>
                  <div class='area-name'><input type="text" ng-model="hearingItem.variableName"></div>
                  <div class='area-type'>
                      <!--          <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType.toString()" ng-options="index as type.label for (index, type) in inputTypeList"></select>-->
                      <select name="hearing-input-option" id="hearing-input-option">
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
                  <div class='area-detail' style="margin-top: 10px">
            <span>
                <label>テキストタイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                  data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。">?</icon></span></label>
                <label class="pointer"><input type="radio" name="hearing-one-row-type" value="1" checked>text</label>
                <label class="pointer"><input type="radio" name="hearing-one-row-type" value="2">number</label>
                <label class="pointer"><input type="radio" name="hearing-one-row-type" value="3">email</label>
                <label class="pointer"><input type="radio" name="hearing-one-row-type" value="4">tel</label>
            </span>
                      <div class="styleFlexbox" style="margin-left: 20px; margin-top: 15px; display: none;" id="one_row_error_message">
                <span class="fb11em"><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ<span
                                class="questionBalloon"><icon class="questionBtn"
                                                              data-tooltip="サイト訪問者の発言内容がタイプに当てはまらなかった場合（整合性チェックエラーの場合）に自動返信するメッセージを設定します。">?</icon></span></label></span>
                          <div>
                              <resize-textarea name="errorMessage" maxlength="4000" ng-model="setItem.errorMessage"
                                               cols="48"
                                               rows="1" placeholder="入力エラー時の返信メッセージを入力してください"
                                               data-maxRow="10"></resize-textarea>
                          </div>
                      </div>
                  </div>

              </div>
          </div>


          <!-- テキスト（複数行）-->
          <div class="text-multiple-row hearing-input-type" id="hearing_input_type_2" style="display: none">
              <hr style="margin: 8px">
              <div class='grid-container grid-container-body itemListGroup'
                   ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
                  <div class="area-drag-symbol">
                      <i class="fas fa-arrows-alt-v fa-2x"></i>
                  </div>
                  <div class="area-require">
                      <label class="require-checkbox">
                          <input type="checkbox">
                          <span class="checkmark"></span>
                      </label>

                  </div>
                  <div class='area-name'><input type="text" ng-model="hearingItem.variableName"></div>
                  <div class='area-type'>
                      <!--          <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType.toString()" ng-options="index as type.label for (index, type) in inputTypeList"></select>-->
                      <select name="hearing-input-option" id="hearing-input-option">
                          <option value="2">テキスト（複数行）</option>
                          <option value="1">テキスト（１行）</option>
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
                  <div class='area-detail' style="margin-top: 10px">
            <span>
                <label>テキストタイプ<span class="questionBalloon"><icon class="questionBtn"
                                                                  data-tooltip="チャットボットが自動送信する質問内容を設定します。<br><br>例）お名前を入力して下さい。">?</icon></span></label>
                <label class="pointer"><input type="radio" name="hearing-multiple-input-type" value="1" checked>text</label>
                <label class="pointer"><input type="radio" name="hearing-multiple-input-type" value="2">number</label>
            </span>
                  </div>
              </div>

              <div class="styleFlexbox" style="margin-left: 20px" id="multiple_row_error_message">
                <span class="fb11em"><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ<span
                                class="questionBalloon"><icon class="questionBtn"
                                                              data-tooltip="サイト訪問者の発言内容がタイプに当てはまらなかった場合（整合性チェックエラーの場合）に自動返信するメッセージを設定します。">?</icon></span></label></span>
                  <div>
                      <resize-textarea name="errorMessage" maxlength="4000" ng-model="setItem.errorMessage" cols="48"
                                       rows="1" placeholder="入力エラー時の返信メッセージを入力してください"
                                       data-maxRow="10"></resize-textarea>
                  </div>
              </div>
          </div>


          <!-- ラジオボタン-->
          <div class="radio-button-tyepe hearing-input-type" id="hearing_input_type_3" style="display: none">
              <hr style="margin: 8px">
              <div class='grid-container grid-container-body itemListGroup'
                   ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
                  <div class="area-drag-symbol">
                      <i class="fas fa-arrows-alt-v fa-2x"></i>
                  </div>
                  <div class="area-require">
                      <label class="require-checkbox">
                          <input type="checkbox">
                          <span class="checkmark"></span>
                      </label>

                  </div>
                  <div class='area-name'><input type="text" ng-model="hearingItem.variableName"></div>
                  <div class='area-type'>
                      <!--          <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType.toString()" ng-options="index as type.label for (index, type) in inputTypeList"></select>-->
                      <select name="hearing-input-option" id="hearing-input-option">
                          <option value="3">ラジオボタン</option>
                          <option value="1">テキスト（１行）</option>
                          <option value="2">テキスト（複数行）</option>
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
              </div>
              <div class="select-option">
                    <span><label class="">選択肢１<span
                                    class="questionBalloon"><icon class="questionBtn"
                                                                  data-tooltip="サイト訪問者の発言内容がタイプに当てはまらなかった場合（整合性チェックエラーの場合）に自動返信するメッセージを設定します。">?</icon></span></label></span>
                  <input type="text" style="width: 200px; margin-left: 20px">
                  <div class="btnBlock">
                      <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
                  </div>
                  <a href="" class="greenBtn btn-shadow" style="display: inline-flex">選択肢を一括登録</a>
              </div>
          </div>

          <!-- プルダウン-->
          <div class="dropdown-type hearing-input-type" id="hearing_input_type_4" style="display: none">
              <hr style="margin: 8px">
              <div class='grid-container grid-container-body itemListGroup'
                   ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
                  <div class="area-drag-symbol">
                      <i class="fas fa-arrows-alt-v fa-2x"></i>
                  </div>
                  <div class="area-require">
                      <label class="require-checkbox">
                          <input type="checkbox">
                          <span class="checkmark"></span>
                      </label>

                  </div>
                  <div class='area-name'><input type="text" ng-model="hearingItem.variableName"></div>
                  <div class='area-type'>
                      <!--          <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType.toString()" ng-options="index as type.label for (index, type) in inputTypeList"></select>-->
                      <select name="hearing-input-option" id="hearing-input-option">
                          <option value="4">プルダウン</option>
                          <option value="1">テキスト（１行）</option>
                          <option value="2">テキスト（複数行）</option>
                          <option value="3">ラジオボタン</option>
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
              </div>
              <div class="select-option" style="margin-left: 20px">
                    <span><label class="">選択肢１<span
                                    class="questionBalloon"><icon class="questionBtn"
                                                                  data-tooltip="サイト訪問者の発言内容がタイプに当てはまらなかった場合（整合性チェックエラーの場合）に自動返信するメッセージを設定します。">?</icon></span></label></span>
                  <input type="text" style="width: 200px; margin-left: 20px">
                  <div class="btnBlock">
                      <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.addActionItemList($event, listId)')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px', 'ng-click' => 'main.removeActionItemList($event, listId)')) ?></a>
                  </div>
                  <a href="" class="greenBtn btn-shadow" style="display: inline-flex">選択肢を一括登録</a>
              </div>

              <label class="pointer" style="margin-left: 20px;">
                  <input type="checkbox" style="margin-top: 15px" id="dropdown_custom_design">デザインをカスタマイズする
                  <span class="questionBalloon"><icon class="questionBtn" data-tooltip="質問内容を全て聞き終えた後に、サイト訪問者に確認メッセージを送ることが出来ます。">?</icon></span>
              </label>

              <div id="dropdown_custom_design_area" style="display: none">
                <span style="height: 40px; margin-left: 40px">
                    <label for="">背景色</label>
                    <input type="text" class="jscolor" style="margin-left: 20px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">文字色</label>
                    <input type="text" class="jscolor" style="margin-left: 20px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">枠線色</label>
                    <input type="text" class="jscolor" style="margin-left: 20px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">▼マーク</label>
                    <input type="text" class="jscolor" style="margin-left: 8px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>
              </div>

          </div>

          <!-- カレンダー-->
          <div class="calendar-type hearing-input-type" id="hearing_input_type_5" style="display: none">
              <hr style="margin: 8px">
              <div class='grid-container grid-container-body itemListGroup'
                   ng-repeat="(listId, hearingItem) in setItem.hearings track by $index">
                  <div class="area-drag-symbol">
                      <i class="fas fa-arrows-alt-v fa-2x"></i>
                  </div>
                  <div class="area-require">
                      <label class="require-checkbox">
                          <input type="checkbox">
                          <span class="checkmark"></span>
                      </label>

                  </div>
                  <div class='area-name'><input type="text" ng-model="hearingItem.variableName"></div>
                  <div class='area-type'>
                      <!--          <select ng-model="hearingItem.inputType" ng-init="hearingItem.inputType = hearingItem.inputType.toString()" ng-options="index as type.label for (index, type) in inputTypeList"></select>-->
                      <select name="hearing-input-option" id="hearing-input-option">
                          <option value="5">カレンダー</option>
                          <option value="1">テキスト（１行）</option>
                          <option value="2">テキスト（複数行）</option>
                          <option value="3">ラジオボタン</option>
                          <option value="4">プルダウン</option>
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
              </div>

              <label class="pointer" style="margin-left: 20px;">
                  <input type="checkbox" style="margin-top: 15px" id="">過去日を選択できなくする
                  <span class="questionBalloon"><icon class="questionBtn" data-tooltip="質問内容を全て聞き終えた後に、サイト訪問者に確認メッセージを送ることが出来ます。">?</icon></span>
              </label>

              <label class="pointer" style="margin-left: 20px;">
                  <input type="checkbox" style="margin-top: 15px" id="calendar_custom_design">デザインをカスタマイズする
                  <span class="questionBalloon"><icon class="questionBtn" data-tooltip="質問内容を全て聞き終えた後に、サイト訪問者に確認メッセージを送ることが出来ます。">?</icon></span>
              </label>

              <div id="calendar_custom_design_area" style="display: none">
                <span style="height: 40px; margin-left: 40px">
                    <label for="">ヘッダー背景色</label>
                    <input type="text" class="jscolor" style="margin-left: 20px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">ヘッダー文字色</label>
                    <input type="text" class="jscolor" style="margin-left: 20px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">枠線色</label>
                    <input type="text" class="jscolor" style="margin-left: 20px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">カレンダ背景色</label>
                    <input type="text" class="jscolor" style="margin-left: 8px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">カレンダ文字色</label>
                    <input type="text" class="jscolor" style="margin-left: 8px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">土曜日文字色</label>
                    <input type="text" class="jscolor" style="margin-left: 8px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">日曜日文字色</label>
                    <input type="text" class="jscolor" style="margin-left: 8px; width: 150px" value="ab2567">
                    <span class="greenBtn btn-shadow" style="margin-left: 20px">標準に戻る</span>
                  </span>

                  <span style="height: 40px; margin-left: 40px">
                    <label for="">言語</label>
                    <label class="pointer"><input type="radio" name="language">日本語表記</label>
                    <label class="pointer"><input type="radio" name="language">英語表記</label>
                  </span>

              </div>

          </div>
      </li>



      <hr style="margin: 8px">
<!--    <li class="styleFlexbox">-->
<!--      <span class="fb11em"><label class="hearingErrorMessageLabel">入力エラー時の<br>返信メッセージ<span class="questionBalloon"><icon class="questionBtn" data-tooltip="サイト訪問者の発言内容がタイプに当てはまらなかった場合（整合性チェックエラーの場合）に自動返信するメッセージを設定します。">?</icon></span></label></span>-->
<!--      <div>-->
<!--        <resize-textarea name="errorMessage" maxlength="4000" ng-model="setItem.errorMessage" cols="48" rows="1" placeholder="入力エラー時の返信メッセージを入力してください" data-maxRow="10"></resize-textarea>-->
<!--      </div>-->
<!--    </li>-->
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.isConfirm" ng-init="setItem.isConfirm = setItem.isConfirm == 1">入力内容の確認を行う<span class="questionBalloon"><icon class="questionBtn" data-tooltip="質問内容を全て聞き終えた後に、サイト訪問者に確認メッセージを送ることが出来ます。">?</icon></span></label>
      <ul ng-if="setItem.isConfirm == true" class="indentDown">
        <li class="styleFlexbox">
          <span class="fb9em"><label>確認内容<span class="questionBalloon"><icon class="questionBtn" data-tooltip="確認メッセージとして送信するメッセージを設定します。<br><br>＜設定例＞<br>お名前　　　　：{{showExpression('名前')}}<br>電話番号　　　：{{showExpression('電話番号')}}<br>メールアドレス：{{showExpression('メールアドレス')}}<br>でよろしいでしょうか？">?</icon></span></label></span>

          <div>
            <resize-textarea name="confirmMessage" ng-model="setItem.confirmMessage" cols="48" rows="1" placeholder="確認内容のメッセージを入力してください" data-maxRow="10"></resize-textarea>
          </div>
        </li>
        <li class="styleFlexbox">
          <span class="fb9em"><label>選択肢（OK）<span class="questionBalloon"><icon class="questionBtn" data-tooltip="OK（次のアクションを実行）の場合の選択肢の名称を設定します。">?</icon></span></label></span>
          <div>
            <input type="text" name="success" ng-model="setItem.success">
          </div>
        </li>
        <li class="styleFlexbox">
          <span class="fb9em"><label>選択肢（NG）<span class="questionBalloon"><icon class="questionBtn" data-tooltip="NG（再入力）の場合の選択肢の名称を設定します。">?</icon></span></label></span>
          <div>
            <input type="text" name="cancel" ng-model="setItem.cancel">
          </div>
        </li>
      </ul>
    </li>
    <li>
      <label class="pointer"><input type="checkbox" ng-model="setItem.cv" ng-init="setItem.cv = setItem.cv == 1">成果にCVとして登録する<span class="questionBalloon"><icon class="questionBtn" data-tooltip="チャット履歴の「成果」に「途中離脱」または「CV」として自動登録します。<br><br>【途中離脱】ヒアリング途中で終了した場合<br>【CV】全項目のヒアリングが完了した場合（入力内容の確認を行う場合は「OK」が選択された場合）">?</icon></span></label>
    </li>
  </ul>
</div>
<script>
    $(document).on('change', '#dropdown_custom_design', function () {
        if ($('#dropdown_custom_design').is(':checked')) {
            $('#dropdown_custom_design_area').show();
        } else {
            $('#dropdown_custom_design_area').hide();
        }
    });

    $(document).on('change', '#calendar_custom_design', function () {
        if ($('#calendar_custom_design').is(':checked')) {
            $('#calendar_custom_design_area').show();
        } else {
            $('#calendar_custom_design_area').hide();
        }
    });

    $(document).on('change', '[name="hearing-input-option"]', function () {
        $('.hearing-input-type').hide();
        // var index = $('#hearing-input-option').val();
        $('#hearing_input_type_' + $(this).val()).show();
    });

    $(document).on('change', '[name="hearing-multiple-input-type"]', function () {
        if ($(this).val() != 1) {
            $('#multiple_row_error_message').show();
        } else {
            $('#multiple_row_error_message').hide();
        }
    });

    $(document).on('change', '[name="hearing-one-row-type"]', function () {
        if ($(this).val() != 1) {
            $('#one_row_error_message').show();
        } else {
            $('#one_row_error_message').hide();
        }
    })

</script>




