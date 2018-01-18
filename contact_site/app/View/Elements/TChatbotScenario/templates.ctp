<?php /* テキスト発言 | C_SCENARIO_ACTION_TEXT */ ?>
<div ng-if="setItem.actionType == 1" class="set_action_item_body">
  <ul>
    <li class="styleFlexbox">
      <span><label>発言内容</label></span>
      <textarea name="message" ng-model="setItem.message" cols="48" rows="4" placeholder="メッセージを入力してください"></textarea>
    </li>
  </ul>
</div>

<?php /* ヒアリング | C_SCENARIO_ACTION_HEARING */ ?>
<div ng-if="setItem.actionType == 2" class="set_action_item_body">
  <ul>
    <li>
      <table cellspacing="5">
        <tr>
          <th>変数名</th>
          <th>タイプ</th>
          <th>質問内容</th>
          <th></th>
        </tr>
        <tr>
          <td><input type="text"></td>
          <td>
            <select>
              <option value="@text">@text</option>
              <option value="@number">@number</option>
              <option value="@email">@email</option>
              <option value="@tel_number">@tel_number</option>
            </select>
          </td>
          <td><input type="text"></td>
          <td class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px')) ?></a>
          </td>
        </tr>
      </table>
    </li>
    <li class="styleFlexbox">
      <span><label>入力エラー時の<wbr>返信メッセージ</label></span>
      <textarea name="errorMessage" ng-model="setItem.errorMessage" cols="48" rows="4" placeholder="入力エラー時の返信メッセージを入力してください"></textarea>
    </li>
    <li>
      <label><input type="checkbox" ng-model="setItem.isConfirm">入力内容の確認を行う</label>
      <ul ng-if="setItem.isConfirm == true" class="indentDown">
        <li class="styleFlexbox">
          <span><label>確認内容</label></span>
          <textarea name="confirmMessage" ng-model="setItem.confirmMessage" cols="48" rows="4" placeholder="確認内容のメッセージを入力してください"></textarea>
        </li>
        <li class="styleFlexbox">
          <span><label>選択肢（OK）</label></span>
          <input type="text" name="success" ng-model="setItem.success">
        </li>
        <li class="styleFlexbox">
          <span><label>選択肢（NG）</label></span>
          <input type="text" name="cancel" ng-model="setItem.cancel">
        </li>
      </ul>
    </li>
    <li>
      <label><input type="checkbox" ng-model="setItem.cv">成果にCVとして登録する</label>
      <div ng-if="setItem.cv == true" class="indentDown">
        <label class="styleBlock"><input type="radio" name="action_{{setActionId}}_cv_condition" value="1" ng-model="setItem.cvCondition">一部の項目でも正常に入力されたらCVとして登録する</label>
        <label class="styleBlock"><input type="radio" name="action_{{setActionId}}_cv_condition" value="2" ng-model="setItem.cvCondition">すべての項目が正常に入力された場合のみCVとして登録する</label>
        <label class="styleBlock"><input type="radio" name="action_{{setActionId}}_cv_condition" value="3" ng-model="setItem.cvCondition">入力確認にて選択肢（OK）が選択された場合のみCVとして登録する</label>
      </div>
    </li>
  </ul>
</div>

<?php /* 選択肢 | C_SCENARIO_ACTION_SELECT_OPTION */ ?>
<div ng-if="setItem.actionType == 3" class="set_action_item_body">
  <ul>
    <li class="styleFlexbox">
      <span><label>変数名</label></span>
      <input type="text">
    </li>
    <li class="styleFlexbox">
      <span><label>質問内容</label></span>
      <textarea name="message" ng-model="setItem.message" cols="48" rows="4" placeholder="質問内容のメッセージを入力してください"></textarea>
    </li>
    <li>
      <ul>
        <li class="styleFlexbox">
          <span><label>選択肢１</label></span>
          <input type="text">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px')) ?></a>
          </div>
        </li>
      </ul>
    </li>
  </ul>
</div>

<?php /* メール送信 | C_SCENARIO_ACTION_SEND_MAIL */ ?>
<div ng-if="setItem.actionType == 4" class="set_action_item_body">
  <ul>
    <li class="styleFlexbox">
      <span><label>送信先メールアドレス</label></span>
      <ul>
        <li>
          <input type="text">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px')) ?></a>
          </div>
        </li>
        <li>
          <input type="text">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px')) ?></a>
          </div>
        </li>
        <li>
          <input type="text">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px')) ?></a>
          </div>
        </li>
        <li>
          <input type="text">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px')) ?></a>
          </div>
        </li>
        <li>
          <input type="text">
          <div class="btnBlock">
            <a><?= $this->Html->image('add.png', array('alt' => '追加', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow disOffgreenBtn', 'style' => 'padding: 2px')) ?></a><a><?= $this->Html->image('dustbox.png', array('alt' => '削除', 'width' => 25, 'height' => 25, 'class' => 'btn-shadow redBtn deleteBtn', 'style' => 'padding: 2px')) ?></a>
          </div>
        </li>
      </ul>
    </li>
    <li class="styleFlexbox">
      <span><label>メールタイトル</label></span>
      <input type="text">
    </li>
    <li class="styleFlexbox">
      <span><label>差出人名</label></span>
      <input type="text">
    </li>
    <li class="styleFlexbox">
      <span><label>メール本文タイプ</label></span>
      <div>
        <label class="styleBlock"><input type="radio" name="action_{{setActionId}}_mail_type" value="1" ng-model="setItem.mailType">メール内容をすべてメールする</label>
        <label class="styleBlock"><input type="radio" name="action_{{setActionId}}_mail_type" value="2" ng-model="setItem.mailType">変数の値のみメールする</label>
        <label class="styleBlock"><input type="radio" name="action_{{setActionId}}_mail_type" value="3" ng-model="setItem.mailType">メール本文をカスタマイズする</label>
        <textarea ng-if="setItem.mailType == 3" cols="48" rows="4" placeholder="メール本文を入力してください"></textarea>
      </div>
    </li>
  </ul>
</div>
