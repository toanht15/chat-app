<?= $this->element('ScriptSettings/menuBox'); ?>
<!--/menubox-->

<div id="contents">

<div class="inner">

<div id="main">

<section>

<h2>会社概要</h2>

<table class="ta1">
<tr>
<th>所在地</th>
<td>東京都XX区XXXX</td>
</tr>
<tr>
<th>地図</th>
<td><a href="http://template-party.com/file/pickup_googlemap.html">Google Mapを使いたい人はこちらの解説をご覧下さい。</a></td>
</tr>
<tr>
<th>見出し</th>
<td>ここに説明など入れて下さい。サンプルテキスト。</td>
</tr>
<tr>
<th>見出し</th>
<td>ここに説明など入れて下さい。サンプルテキスト。</td>
</tr>
<tr>
<th>見出し</th>
<td>ここに説明など入れて下さい。サンプルテキスト。</td>
</tr>
<tr>
<th>見出し</th>
<td>ここに説明など入れて下さい。サンプルテキスト。</td>
</tr>
<tr>
<th>見出し</th>
<td>ここに説明など入れて下さい。サンプルテキスト。</td>
</tr>
</table>

</section>

<section id="about">

<h2>当テンプレートについて</h2>

<h3>当テンプレートはhtml5+CSS3(レスポンシブWEBデザイン)です</h3>
<p>当テンプレートは、パソコン、スマホ、タブレットでhtml共通のレスポンシブWEBデザインになっております。<br>
古いブラウザ（※特にIE8以下）で閲覧した場合にCSSの一部が適用されない（各を丸くする加工やグラデーションなどの加工等）のでご注意下さい。</p>

<h3>各デバイスごとのレイアウトチェックは</h3>
<p>最終的なチェックは実際のタブレットやスマホで行うのがおすすめですが、臨時チェックは最新のブラウザ(IEならIE10以降)で行う事もできます。ブラウザの幅を狭くしていくと、各端末サイズに合わせたレイアウトになります。</p>

<h3>各デバイス用のスタイル変更は</h3>
<p>cssフォルダのstyle.cssファイルで行って下さい。詳しい説明も入っています。<br>
前半はパソコン環境を含めた全端末の共通設定になります。中盤以降、各端末向けのスタイルが追加設定されています。<br>
media=&quot; (～)&quot;の「～」部分でcssを切り替えるディスプレイのサイズを設定しています。ここは必要に応じて変更も可能です。</p>

<h3>小さい端末（※幅480px以下）の環境でのみ</h3>
<p>メインメニューが折りたたみ式（３本バーアイコン化）になります。バーのスタイル設定もstyle.cssで行う事ができます。</p>

<h3>画像ベースは</h3>
<p>「base」フォルダに入っていますのでご自由にご活用下さい。<br>
トップページのmainimgの３端末画像は１枚のままだとお手持ち画像に入れ替えにくいので別々に分けました。レイヤーを使える画像ソフトをおもちなら、「mainimg_pc.png」「mainimg_sh.png」「mainimg_ta.png」にそれぞれお手持ち画像をはめこんで全て同じ位置を保って重ねるとmainimgと同じ形になります。<br>
尚、写真の元素材を当社運営の<a href="http://photo-chips.com/">PHOTO-CHIPS</a>や<a href="http://decoruto.com/">DECORUTO</a>で配布している場合もございます。</p>

</section>

<section>

<h2>当テンプレートの使い方</h2>

<h3 class="color1">注意：当テンプレートにはメインメニューが「２箇所」ずつ入っています</h3>
<p>パソコンなどの大きな端末「menubar（幅481px以上）」向けと、スマホなどの小さな端末「menubar-s（幅480px以下）」向けがそれぞれ入っています。大きな端末向けは編集ソフトで見れると思いますが、小さな端末向けは見えないと思いますのでhtml側で編集して下さい。</p>

<h3>titleタグ、copyright、metaタグ、他の設定</h3>
<p><strong class="color1">■titleタグの設定はとても重要です。念入りにワードを選んで適切に入力しましょう。</strong><br>
まず、htmlソースが見れる状態にして、<br>
<span class="look">&lt;title&gt;ビジネスサイト向け 無料ホームページテンプレート tp_biz40&lt;/title&gt;</span><br>
を編集しましょう。<br>
あなたのホームページ名が「Sample Company」だとすれば、<br>
<span class="look">&lt;title&gt;Sample Company&lt;/title&gt;</span><br>
とすればＯＫです。SEO対策もするなら冒頭に重要なワードを入れておきましょう。</p>
<p><strong class="color1">■copyrightを変更しましょう。</strong><br>
続いてhtmlの下の方にある、<br>
<span class="look">Copyright&copy; Sample Company All Rights Reserved.</span><br>
の「Sample Company」部分もあなたのサイト名に変更します。</p>
<p><strong class="color1">■metaタグを変更しましょう。</strong><br>
htmlソースが見える状態にしてmetaタグを変更しましょう。</p>
<p>ソースの上の方に、<br>
<span class="look">content=&quot;ここにサイト説明を入れます&quot;</span><br>
という部分がありますので、テキストをサイトの説明文に入れ替えます。検索結果の文面に使われる場合もありますので、見た人が来訪したくなるような説明文を簡潔に書きましょう。</p>
<p>続いて、その下の行の<br>
<span class="look">content=&quot;キーワード１,キーワード２,～～～&quot;</span><br>
も設定します。ここはサイトに関係のあるキーワードを入れる箇所です。10個前後ぐらいあれば充分です。キーワード間はカンマ「,」で区切ります。</p>
<p><strong class="color1">■h1ロゴのaltタグも変更しましょう。</strong><br>
html側に<br>
<span class="look">alt=&quot;Sample Company&quot;</span><br>
となっている箇所があるので、この部分もあなたのサイト名に変更しましょう。</p>

<h3>画面右上のCampaignマークは</h3>
<p>htmlの下の方に画像タグで入っています。マークを使わない場合はhtml側から削除して下さい。</p>

<h3>１カラムで使いたい場合は</h3>
<p><a href="c1.html">こちらをご覧下さい。</a></p>

<h3>テーマカラーを変更したい場合</h3>
<ol>
<li>cssフォルダのstyle.cssを開き、上の方にある「header」の中にある「background」の「２行目（古いブラウザ向けでない方）」にある２つのカラーコード（#で始まる６桁の英数字）の左側のコードを希望のテーマカラーのコードに一括変換。同じコードが数カ所あるので一括変更できるソフトを使うと便利です。</li>
<li>続いて右側のコードも変更しますが、ここはヘッダーの光って見える部分「以外」の暗い色になります。１箇所しかないので直接入れ替えて下さい。</li>
<li>あとは必要に応じてimagesフォルダの「arrow.png」も変えて下さい。</li>
</ol>
<p>以上、簡単です。</p>

<h3>スマホなどの小さな端末からボタンクリックでPC画面を表示させたい方へ</h3>
<p>レスポンシブデザインだと、スマホやタブレットなどの小さな端末から見た場合はそれ専用のレイアウトに変わりますが、あえてPC画面も見せたいユーザーの為に<a href="http://template-party.com/tips/tips20160916viewport.html">tipsを公開</a>しました。</p>

<h3>プレビューでチェックすると警告メッセージが出る場合(一部ブラウザ対象)</h3>
<p>主にjavascript（jsファイル）ファイルによって出る警告ですが、WEB上では出ません。また、この警告が出ている間は効果を見る事ができないので、警告メッセージ内でクリックして解除してあげて下さい。これにより効果がちゃんと見れるようになります。</p>

</section>

</div>
<!--/main-->

<?= $this->element('ScriptSettings/subMenu'); ?>
</div>
<!--/inner-->

</div>
<!--/contents-->

<?= $this->element('ScriptSettings/footer'); ?>