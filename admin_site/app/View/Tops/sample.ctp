<section>
  <h1>◆ 通常系</h1>
  <h1><i class="fa fa-bell" aria-hidden="true"></i> ｈ１</h1>
  <h2>ｈ２</h2>
  <ul class="formArea">
    <li>
      <label for="">テキストボックス</label>
      <input type="text" />
    </li>
    <li>
      <label for="">テキストエリア</label>
      <textarea name="" id="" cols="30" rows="10"></textarea>
    </li>
  </ul>
  <a class="underLine" href="javascript:void(0)">リンク</a>
</section>

<section>
  <h1>◆ テーブル</h1>
  <table>
    <thead>
      <th>カラム１</th>
      <th>カラム２</th>
      <th>カラム３</th>
    </thead>
    <tbody>
      <tr>
        <td>データ１</td>
        <td>データ２</td>
        <td>データ３</td>
      </tr>
      <tr>
        <td>データ１</td>
        <td>データ２</td>
        <td>データ３</td>
      </tr>
      <tr>
        <td>データ１</td>
        <td>データ２</td>
        <td>データ３</td>
      </tr>
    </tbody>
  </table>
</section>

<section>
  <h1>◆ メッセージ系</h1>
  <p class="notification_message">通知メッセージ</p>
  <p class="error_message">エラーメッセージ</p>
</section>

<section>
  <h1>◆ ボタンの使い方</h1>
  <a href="javascript:void(0)" style="width: 6em" class="normal_btn">normal</a>
  <a href="javascript:void(0)" style="width: 6em" class="action_btn">action</a>
  <a href="javascript:void(0)" style="width: 6em" class="success_btn">success</a>
  <a href="javascript:void(0)" style="width: 6em" class="notification_btn">notification</a>
</section>



<section>
  <h1>◆ モーダルの使い方</h1>
  <script type="text/javascript">
  'use strict';
  function openConfirm(){
    var html  = '<p>テストになります</p>';
        html += '<ul class="formArea">';
        html += '  <li><label>テキスト</label><input type="text"></li>';
        html += '</ul>';
    modalOpen.call(window, html, 'p-confirm', '確認');
  }
  function openAlert(){
    var html  = "テストになります";
    modalOpen.call(window, html, 'p-alert', 'アラート');
  }
  </script>
  <ul>
    <li><a href="javascript:void(0)" style="width: 10em" onclick="openConfirm()" class="normal_btn">確認ダイアログ</a></li>
    <li><a href="javascript:void(0)" style="width: 6em" onclick="openAlert()" class="normal_btn">アラート</a></li>
  </ul>
</section>
