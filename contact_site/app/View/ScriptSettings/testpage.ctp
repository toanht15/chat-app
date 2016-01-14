<div id="testpage_bg">

  <?php if ( $layoutNumber === 1 ) { ?>
    <div id="testpage_idx">
      <div id="title">
        <span class="bold">サンプルページ：１ページ目</span>：<a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage', '?' => array('page' => 2)))?>"><span>サンプルページ：２ページ目</span></a>
      </div>
      <div id="detail">
        <p>モニタリングでは以下のことが可能です</p>
        <ul>
          <li>ページ同期（ページ遷移にも対応）</li>
          <li>スクロールの共有</li>
          <li>顧客から企業へのウィンドウサイズの反映</li>
          <li>マウス位置の共有</li>
          <li>フォームの入力内容の共有</li>
        </ul>
      </div>
      <div class="form01">
        <form method="post" action="./list.html">
          <ul>
            <li class="lb">
              テキストエリアの入力内容が共有できます。
            </li>
            <li>
              <input type="text" id="name" placeholder="名前">
            </li>
            <li class="lb">
              クリックの同期にも対応していますので、ラジオボタンの選択も反映されます。
            </li>
            <li>
              <p>性別：
                <label><input type="radio" name="man" value="1">男性</label>
                <label><input type="radio" name="woman" value="2">女性</label>
              </p>
            </li>
            <li>
              <input type="number" min="0" id="old" placeholder="年齢">
            </li>
            <li>
              <input type="text" id="office" placeholder="会社">
            </li>
            <li class="lb">
              同じく、プルダウンの選択も反映されます。
            </li>
            <li>
              <label>職種
                <select name="work">
                  <option value="1">会社員</option>
                  <option value="2">パート</option>
                  <option value="3">役　員</option>
                  <option value="4">学　生</option>
                  <option value="5">その他</option>
                </select>
              </label>
            </li>
            <li>
              <input type="text" id="favorite" placeholder="趣味">
            </li>
            <li>
              <textarea id="other" placeholder="その他"></textarea>
            </li>
            <li>
              <button type="button" onclick="alert('こんにちは')">send!!</button>
            </li>
          </ul>
        </form>
      </div>
    </div>

  <?php } else { ?>
    <div id="testpage_idx">
      <div id="title">
        <a href="<?=$this->Html->url(array('controller' => 'ScriptSettings', 'action' => 'testpage', '?' => array('page' => 1)))?>"><span class="bold">サンプルページ：１ページ目</span></a>：<span>サンプルページ：２ページ目</span>
      </div>
      <div id="detail">
        <p>モニタリングでは以下のことが可能です</p>
        <ul>
          <li>ページ同期（ページ遷移にも対応）</li>
          <li>スクロールの共有</li>
          <li>顧客から企業へのウィンドウサイズの反映</li>
          <li>マウス位置の共有</li>
          <li>フォームの入力内容の共有</li>
        </ul>
      </div>
      <div class="form01">
        <pre class="p15l">１ページ目にて上記動作を試してみてください。</pre>
      </div>
    </div>
  <?php } ?>
  <script type='text/javascript' src='//socket.localhost:8080/client/medialink.js'></script>

</div>
