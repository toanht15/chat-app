<?php /* テキスト発言｜C_SCENARIO_ACTION_TEXT */ ?>
<div ng-if="setItem.actionType == 1">
  <ul>
    <li>
      <span><label>発言内容</label></span>
      <textarea></textarea>
    </li>
  </ul>
</div>

<div ng-if="setItem.actionType == 2">
  <ul>
    <li>
      <table>
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
          <td><input type="button" value="add"> <input type="button" value="remove"></td>
        </tr>
      </table>
    </li>
    <li>
      <span><label>入力エラー時の<wbr>返信メッセージ</label></span>
      <textarea></textarea>
    </li>
    <li>
      <label><input type="checkbox">入力内容の確認を行う</label>
    </li>
    <li>
      <label><input type="checkbox">成果にCVとして登録する</label>
    </li>
  </ul>
</div>

<div ng-if="setItem.actionType == 3">
  <ul>
    <li>
      <span><label>変数名</label></span>
      <input type="text">
    </li>
    <li>
      <span><label>質問内容</label></span>
      <textarea></textarea>
    </li>
    <li>
    </li>
  </ul>
</div>

<div ng-if="setItem.actionType == 4">
  <p>メール送信のテンプレート</p>
</div>
