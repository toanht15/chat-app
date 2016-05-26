<section id="sample_widget_area" ng-cloak>
	<div id="sincloBox" style="position: relative; z-index: 1; width: 270px; overflow: hidden; background-color: rgb(255, 255, 255);">
	<style>
		#sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
		#sincloBox span#mainImage { z-index: 2 }
		#sincloBox span#mainImage img { background-color: {{main_color}} }
		#sincloBox .pb07 { padding-bottom: 7px }
		#sincloBox .center { text-align: center!important; padding: 7px 0!important }
		#sincloBox div#descriptionSet { height: 52px; padding-top: 7px; border-width: 0 1px 1px 1px; border-style: solid; border-color: #E8E7E0 }
		#sincloBox div#descriptionSet p:not(.ng-hide) { padding-bottom: 7px }
		#sincloBox p#widgetTitle { position:relative; z-index: 1; cursor:pointer; border-radius: {{radius_ratio}}px {{radius_ratio}}px 0 0; border: 1px solid {{main-color}}; border-bottom:none; background-color: {{main_color}};text-align: left; font-size: 14px;padding: 7px 11px 7px 73px; margin: 0;color: #FFF; height: 32px }
		#sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 6px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
		#sincloBox p#widgetSubTitle { margin: 0; text-align: left; padding-left: 77px; font-weight: bold; color: {{main_color}} }
		#sincloBox p#widgetDescription { margin: 0; text-align: left; padding-left: 77px; color: #8A8A8A; }
		#sincloBox section { display: inline-block; width: 270px; border: 1px solid #E8E7E0; border-top: none; }
		#sincloBox ul#chatTalk { width: 100%; height: 250px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
		#sincloBox ul#chatTalk li { margin: 5px 0; padding: 5px; font-size: 12px; border: 1px solid #C9C9C9; line-height: 1.8; white-space: pre; color: #5E614E; }
		#sincloBox ul#chatTalk li.sinclo_se { border-radius: 5px 5px 0; margin-left: 10px; background-color: #FFF; }
		#sincloBox ul#chatTalk li.sinclo_re { margin-right: 10px; border-radius: 5px 5px 5px 0; }
		#sincloBox section#chatTab textarea { padding: 5px; resize: none; width: 100%; height: 50px; border: 1px solid #E4E4E4; border-radius: 5px; color: #8A8A8A; }
	<?php if ( $coreSettings[C_COMPANY_USE_CHAT] && $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
		#sincloBox section#callTab { display: none }
		#sincloBox section#callTab #telNumber { color: {{main_color}}; font-weight: bold; margin: 0 auto; text-align: center }
	<?php endif; ?>
	<?php if ( $coreSettings[C_COMPANY_USE_CHAT] && $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
		#sincloBox section#navigation { border-width: 0 1px; height: 40px; position: relative; display: block; }
		#sincloBox section#navigation ul { margin: 0 0 0 -1px; display: table; padding: 0; position: absolute; top: 0; left: 0; height: 40px; width: 270px }
		#sincloBox section#navigation ul li { position: relative; overflow: hidden; cursor: pointer; color: #8A8A8A; width: 50%; text-align: center; display: table-cell; padding: 10px 0; border-left: 1px solid #E8E7E0; height: 40px }
		#sincloBox section#navigation ul li:last-child { border-right: 1px solid #E8E7E0; }
		#sincloBox section#navigation ul li.selected { background-color: #FFFFFF; }
		#sincloBox section#navigation ul li:not(.selected) { border-bottom: 1px solid #E8E7E0 }
		#sincloBox section#navigation ul li.selected::after{ content: " "; border-bottom: 2px solid {{main_color}}; position: absolute; bottom: 0px; left: 5px; right: 5px;}
		#sincloBox section#navigation ul li::before{ margin-right: 5px; background-color: #BCBCBC; content: " "; display: inline-block; width: 18px; height: 18px; position: relative; background-size: contain; vertical-align: middle; background-repeat: no-repeat }
		#sincloBox section#navigation ul li[data-tab='call']::before{ background-image: url('<?=C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT?>/img/widget/icon_tel.png'); }
		#sincloBox section#navigation ul li[data-tab='chat']::before{ background-image: url('<?=C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT?>/img/widget/icon_chat.png'); }
		#sincloBox section#navigation ul li.selected::before{ background-color: {{main_color}}; }
	<?php endif; ?>
		#sincloBox[data-openflg='true'] p#widgetTitle:after { transform: rotate(0deg); }
		#sincloBox[data-openflg='false'] p#widgetTitle:after { transform: rotate(180deg); }
		#sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
	</style>
	<!-- 画像 -->
	<span id="mainImage" ng-if="mainImageToggle == '1'" style="position: absolute; top: 7px; left: 7px;">
		<img ng-src="<?=$gallaryPath?>{{main_image}}" err-src="<?=$gallaryPath?>chat_sample_picture.png" width="62" height="70" alt="チャット画像">
	</span>
	<!-- 画像 -->
	<div>
		<!-- タイトル -->
		<p id="widgetTitle" ng-class="{center: mainImageToggle == '2'}">{{title}}</p>
		<!-- タイトル -->
	</div>
	<div id='descriptionSet' ng-hide="mainImageToggle == '2' && subTitleToggle == '2' && descriptionToggle == '2'">
		<!-- サブタイトル -->
		<p ng-hide="mainImageToggle == '2' && subTitleToggle == '2'" id="widgetSubTitle" ng-class="{pb07: headerpd()}"><span ng-if="subTitleToggle == '1'">{{sub_title}}</span></p>
		<!-- サブタイトル -->

		<!-- 説明文 -->
		<p ng-hide="mainImageToggle == '2' && descriptionToggle == '2'" id="widgetDescription"><span ng-if="descriptionToggle == '1'">{{description}}</span></p>
		<!-- 説明文 -->
	</div>
	<div id="miniTarget" ng-style="miniTargetCss">
	<?php if ( $coreSettings[C_COMPANY_USE_CHAT] && $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
		<section id="navigation">
			<ul>
				<li data-tab="chat" class="widgetCtrl selected">チャットでの受付</li>
				<li data-tab="call" class="widgetCtrl" >電話での受付</li>
			</ul>
		</section>
	<?php endif; ?>
	<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
		<section id="chatTab">
			<ul id="chatTalk">
			<li class="sinclo_se">○○について質問したいのですが</li>
			<li class="sinclo_re" style="background-color:{{makeFaintColor()}}">こんにちは</li>
			<li class="sinclo_re" style="background-color:{{makeFaintColor()}}">○○についてですね<br>どのようなご質問でしょうか？</li>
			</ul>
			<div style="border-top: 1px solid #E8E7E0; padding: 10px;">
			<textarea name="sincloChat" id="sincloChatMessage" placeholder="メッセージ入力後、Enterで送信"></textarea>
			</div>
		</section>
	<?php endif; ?>
	<?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
		<section id="callTab">
			<div style="height: 50px;margin: 15px 25px;">
			<!-- アイコン -->
			<span style="display: block; width: 50px; height: 50px; float: left; background-color: {{main_color}}; border-radius: 25px; padding: 3px;"><img width="19.5" height="33" src="<?=C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT?>/img/call.png" style="margin: 6px 12px"></span>
			<!-- アイコン -->

			<!-- 受付電話番号 -->
			<pre id="telNumber" style="font-weight: bold; font-size: 18px; padding: 5px 0px 0px; height: 30px">{{tel}}</pre>
			<!-- 受付電話番号 -->

			<!-- 受付時間 -->
			<pre ng-if="display_time_flg == '1'" style="font-weight: bold; color: {{main_color}}; margin: 0 auto; font-size: 10px; text-align: center; padding: 0 0 5px; height: 20px">受付時間： {{time_text}}</pre>
			<!-- 受付時間 -->

			</div>

			<!-- テキスト -->
			<pre style="display: block; word-wrap: break-word; font-size: 11px; text-align: center; margin: auto; line-height: 1.5; color: #6B6B6B; width: 20em;">{{content}}</pre>
			<!-- テキスト -->

			<span style="display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border: 1px solid rgb(188, 188, 188); border-radius: 15px">
			●●●●
			</span>
		</section>
	<?php endif; ?>
		<p style="padding: 5px 0;text-align: center;border: 1px solid #E8E7E0;color: #A1A1A1!important;font-size: 11px;margin: 0;border-top: none;">Powered by <a target="sinclo" href="http://medialink-ml.co.jp/index.html">sinclo</a></p>

	</div>

	</div>
</section>
