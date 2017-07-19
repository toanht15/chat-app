<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
<section id="switch_widget" ng-cloak>
  <ul class="ulTab" data-col=3>
    <li ng-class="{choose: showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    <li ng-class="{choose: showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
    <li ng-class="{choose: showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
  </ul>
</section>
<?php endif; ?>

<section id="sample_widget_area" ng-cloak>
  <div id="sincloBox" ng-if="showWidgetType !== 2" style="position: relative; z-index: 1; width: 285px; background-color: rgb(255, 255, 255);">
    <style>
      #sincloBox * { font-size: 12px; }
      #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
      #sincloBox span#mainImage { cursor: pointer; z-index: 2; position: absolute; top: 7px; left: 10px; }
      #sincloBox span#mainImage img { background-color: {{main_color}}; }
      #sincloBox .pb07 { padding-bottom: 7px }
      #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
      #sincloBox .center { text-align: center!important; padding: 7px 30px!important }
      #sincloBox div#descriptionSet { cursor: pointer; }
      #sincloBox p#widgetTitle { position:relative; z-index: 1; cursor:pointer; border-radius: {{radius_ratio}}px {{radius_ratio}}px 0 0; border: 1px solid {{main_color}}; border-bottom:none; background-color: {{main_color}};text-align: center; font-size: 14px;padding: 7px 30px 7px 70px; margin: 0;color: {{string_color}}; height: 32px }
      #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
      #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
      #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
      #sincloBox p#widgetSubTitle { background-color: #FFF; margin: 0; padding: 7px 0; text-align: left; border-width: 0 1px 0 1px; border-color: #E8E7E0; border-style: solid; padding-left: 77px; font-weight: bold; color: {{main_color}}; height: 29px; }
      #sincloBox p#widgetDescription { background-color: #FFF; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: #E8E7E0; border-style: solid; padding-left: 77px; height: 23px; color: #8A8A8A; }
      #sincloBox section { display: inline-block; width: 285px; border: 1px solid #E8E7E0; border-top: none; }
      #sincloBox section.noDisplay { display: none }
      #sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
      #sincloBox ul#chatTalk { width: 100%; height: 194px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
      #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0; padding: 5px; font-size: 12px; border: 1px solid #C9C9C9; line-height: 1.8; white-space: pre; color: #333333; }
      #sincloBox ul#chatTalk li.chat_right { border-bottom-right-radius: 0; margin-left: 10px }
      #sincloBox ul#chatTalk li.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
      #sincloBox ul#chatTalk li span.cName { display: block; color: {{main_color}}!important; font-weight: bold; font-size: 13px }
      #sincloBox section#chatTab div { height: 75px!important; padding: 5px; }
      #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; color: #8A8A8A; margin: 0; border-radius: 5px 0 0 5px!important; resize: none; color: #8A8A8A; padding: 5px; border: 1px solid #C9C9C9!important; border-right-color: transparent!important; }
      #sincloBox section#chatTab textarea#sincloChatMessage:focus { border-color: {{main_color}}!important; outline: none!important; border-right-color: transparent!important; }
      #sincloBox section#chatTab #sincloChatSendBtn{ width: 20%; height: 100%; padding: 20px 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{main_color}}!important; color: {{string_color}}; font-weight: bold; font-size: 1.2em;}
      #sincloBox section#chatTab #sincloChatSendBtn span { color: {{string_color}} }
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
      #sincloBox section#callTab #telNumber { overflow: hidden; color: {{main_color}}; font-weight: bold; margin: 0 auto; text-align: center }
      #sincloBox section#callTab #telNumber:not(.notUseTime) { font-size: 18px; padding: 5px 0px 0px; height: 30px }
      #sincloBox section#callTab #telNumber.notUseTime { font-size: 20px; padding: 10px 0px 0px; height: 45px }
      #sincloBox section#callTab #telIcon { background-color: {{main_color}}; display: block; width: 50px; height: 50px; float: left; border-radius: 25px; padding: 3px }
      #sincloBox section#callTab #telTime { font-weight: bold; color: {{main_color}}; margin: 0 auto; white-space: pre-line; font-size: 11px; text-align: center; padding: 0 0 5px; height: 20px }
      #sincloBox section#callTab #telContent { display: block; overflow-y: auto; overflow-x: hidden; max-height: 119px }
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
        #sincloBox section#callTab #telContent .tblBlock {  text-align: center;  margin: 0 auto;  width: 240px;  display: table;  flex-direction: column;  align-content: center;  height: 119px!important;  justify-content: center; }
      <?php else: ?>
        #sincloBox section#callTab #telContent .tblBlock { text-align: center; margin: 0 auto; width: 240px; display: table; flex-direction: column; align-content: center; justify-content: center; }
      <?php endif; ?>
      #sincloBox section#callTab #telContent span { word-wrap: break-word; word-break: break-all; font-size: 11px; line-height: 1.5!important; color: #6B6B6B; white-space: pre-wrap; display: table-cell; vertical-align: middle; text-align: center }
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ): ?>
      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 18px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border-radius: 15px }
      <?php else: ?>
      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border-radius: 15px }
      <?php endif; ?>
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && ($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])) ) :?>
      #sincloBox section#navigation { border-width: 0 1px; height: 40px; position: relative; display: block; }
      #sincloBox section#navigation ul { margin: 0 0 0 -1px; display: table; padding: 0; position: absolute; top: 0; left: 0; height: 40px; width: 285px }
      #sincloBox section#navigation ul li { position: relative; overflow: hidden; cursor: pointer; color: #8A8A8A; width: 50%; text-align: center; display: table-cell; padding: 10px 0; border-left: 1px solid #E8E7E0; height: 40px }
      #sincloBox section#navigation ul li:last-child { border-right: 1px solid #E8E7E0; }
      #sincloBox section#navigation ul li.selected { background-color: #FFFFFF; }
      #sincloBox section#navigation ul li:not(.selected) { border-bottom: 1px solid #E8E7E0 }
      #sincloBox section#navigation ul li.selected::after{ content: " "; border-bottom: 2px solid {{main_color}}; position: absolute; bottom: 0px; left: 5px; right: 5px;}
      #sincloBox section#navigation ul li::before{ margin-right: 5px; background-color: #BCBCBC; content: " "; display: inline-block; width: 18px; height: 18px; position: relative; background-size: contain; vertical-align: middle; background-repeat: no-repeat }
      #sincloBox section#navigation ul li[data-tab='call']::before{ background-image: url('<?=C_PATH_NODE_FILE_SERVER?>/img/widget/icon_tel.png'); }
      #sincloBox section#navigation ul li[data-tab='chat']::before{ background-image: url('<?=C_PATH_NODE_FILE_SERVER?>/img/widget/icon_chat.png'); }
      #sincloBox section#navigation ul li.selected::before{ background-color: {{main_color}}; }
    <?php endif; ?>
    </style>
    <!-- 画像 -->
    <span id="mainImage" class="widgetOpener" ng-hide="spHeaderLightToggle() || mainImageToggle !== '1'">
      <img ng-src="{{main_image}}" err-src="<?=$gallaryPath?>chat_sample_picture.png" width="62" height="70" alt="チャット画像">
    </span>
    <!-- 画像 -->
    <div>
      <!-- タイトル -->
      <p id="widgetTitle" class="widgetOpener notSelect" ng-class="{center: spHeaderLightToggle() || mainImageToggle !== '1'}">{{title}}</p>
      <!-- タイトル -->
    </div>
    <div id='descriptionSet' class="widgetOpener notSelect" ng-hide=" spHeaderLightToggle() || mainImageToggle == '2' && subTitleToggle == '2' && descriptionToggle == '2'">
      <!-- サブタイトル -->
      <p ng-if="subTitleToggle == '1'" id="widgetSubTitle">{{sub_title}}</p>
      <p ng-if="subTitleToggle == '2'" id="widgetSubTitle"></p>
      <!-- サブタイトル -->

      <!-- 説明文 -->
      <p ng-if="descriptionToggle == '1'" id="widgetDescription">{{description}}</p>
      <p ng-if="descriptionToggle == '2'" id="widgetDescription"></p>
      <!-- 説明文 -->
    </div>
    <div id="miniTarget">
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && ($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])) ) :?>
      <section id="navigation" ng-hide="showWidgetType === 3">
        <ul>
          <li data-tab="chat" class="widgetCtrl notSelect" ng-class="{selected: widget.showTab == 'chat'}">チャットでの受付</li>
          <li data-tab="call" class="widgetCtrl notSelect" ng-class="{selected: widget.showTab == 'call'}" >電話での受付</li>
        </ul>
      </section>
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
      <section id="chatTab" ng-hide="widget.showTab !== 'chat'">
        <ul id="chatTalk">
          <li class="sinclo_se" ng-class="{chat_right: show_position == 2, chat_left: show_position == 1 }">○○について質問したいのですが</li>
          <li class="sinclo_re" ng-class="{chat_right: show_position == 1, chat_left: show_position == 2 }" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName" ng-if="show_name == 1"><?=$userInfo['display_name']?></span><span class="cName" ng-if="show_name == 2">{{sub_title}}</span>こんにちは</li>
          <li class="sinclo_re" ng-class="{chat_right: show_position == 1, chat_left: show_position == 2 }" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName" ng-if="show_name == 1"><?=$userInfo['display_name']?></span><span class="cName" ng-if="show_name == 2">{{sub_title}}</span>○○についてですね<br>どのようなご質問でしょうか？</li>
        </ul>
        <div style="border-top: 1px solid #E8E7E0; padding: 0.5em;">
          <textarea name="sincloChat" id="sincloChatMessage" placeholder="メッセージを入力してください{{chat_area_placeholder_pc}}"></textarea>
          <a id="sincloChatSendBtn" class="notSelect"><span>送信</span></a>
        </div>
      <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
        <span id="sincloAccessInfo" style="padding-left: 0.5em; padding-bottom: 0.4em;">ウェブ接客コード：●●●●</span>
        <?php endif; ?>
      </section>
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
      <section id="callTab" ng-hide="widget.showTab !== 'call'">
        <div style="height: 50px;margin: 15px 25px;">
        <!-- アイコン -->
        <span id="telIcon"><img width="19.5" height="33" src="<?=C_PATH_NODE_FILE_SERVER?>/img/call.png" style="margin: 6px 12px"></span>
        <!-- アイコン -->

        <!-- 受付電話番号 -->
        <pre id="telNumber" ng-class="{notUseTime: timeTextToggle !== '1'}" >{{tel}}</pre>
        <!-- 受付電話番号 -->

        <!-- 受付時間 -->
        <pre id="telTime" ng-if="timeTextToggle == '1'">受付時間： {{time_text}}</pre>
        <!-- 受付時間 -->

        </div>

        <!-- テキスト -->
        <div id="telContent"><div class="tblBlock"><span>{{content}}</span></div></div>
        <!-- テキスト -->

        <span id="accessIdArea">
        ●●●●
        </span>
      </section>
    <?php endif; ?>
      <p style="padding: 5px 0;text-align: center;border: 1px solid #E8E7E0;color: #A1A1A1!important;font-size: 11px;margin: 0;border-top: none;">Powered by <a target="sinclo" href="http://medialink-ml.co.jp/index.html">sinclo</a></p>
    </div>
  </div>


<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
<!-- スマホ版 -->
  <div id="sincloBox" ng-if="showWidgetType === 2" style="position: relative; z-index: 1; width: 285px; background-color: rgb(255, 255, 255);">
    <style>
      #sincloBox * { font-size: 12px; }
      #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
      #sincloBox .pb07 { padding-bottom: 7px }
      #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
      #sincloBox p#widgetTitle { text-align: center!important; padding: 7px 30px!important; position:relative; z-index: 1; cursor:pointer; border-radius: 0; border: 1px solid {{main_color}}; border-bottom:none; background-color: {{main_color}};text-align: center; font-size: 14px; margin: 0;color: {{string_color}}; height: 32px }
      #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
      #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
      #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
      #sincloBox section { display: inline-block; width: 285px; border: 1px solid #E8E7E0; border-top: none; }
      #sincloBox section.noDisplay { display: none }
      #sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
      #sincloBox ul#chatTalk { width: 100%; height: 100px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
      #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0; padding: 3px; font-size: 11px; border: 1px solid #C9C9C9; line-height: 1.8; white-space: pre; color: #333333; }
      #sincloBox ul#chatTalk li.chat_right { border-bottom-right-radius: 0; margin-left: 10px }
      #sincloBox ul#chatTalk li.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
      #sincloBox ul#chatTalk li span.cName { display: block; color: {{main_color}}!important; font-weight: bold; font-size: 12px }
      #sincloBox section#chatTab div { height: 65px!important;  padding: 10px; }
      #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; margin: 0; font-size: 11px; color: #8A8A8A; border-radius: 5px 0 0 5px!important; resize: none; color: #8A8A8A; padding: 5px; border: 1px solid #C9C9C9!important; border-right-color: transparent!important; }
      #sincloBox section#chatTab textarea#sincloChatMessage:focus { border-color: {{main_color}}!important; outline: none!important; border-right-color: transparent!important; }
      #sincloBox section#chatTab #sincloChatSendBtn{ width: 20%; height: 100%; padding: 1em 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{main_color}}!important; color: {{string_color}}; font-weight: bold; font-size: 1.2em;}
      #sincloBox section#chatTab #sincloChatSendBtn span { color: {{string_color}} }
    </style>
    <div>
      <!-- タイトル -->
      <p id="widgetTitle" class="widgetOpener" ng-class="{center: mainImageToggle == '2'}">{{title}}</p>
      <!-- タイトル -->
    </div>
    <div id="miniTarget">
      <section id="chatTab">
        <ul id="chatTalk">
        <li class="sinclo_se" ng-class="{chat_right: show_position == 2, chat_left: show_position == 1 }">○○について質問したいのですが</li>
        <li class="sinclo_re" ng-class="{chat_right: show_position == 1, chat_left: show_position == 2 }" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName">{{sub_title}}</span>こんにちは</li>
        <li class="sinclo_re" ng-class="{chat_right: show_position == 1, chat_left: show_position == 2 }" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName">{{sub_title}}</span>○○についてですね<br>どのようなご質問でしょうか？</li>
        </ul>
        <div style="border-top: 1px solid #E8E7E0; padding: 0.5em;">
          <textarea name="sincloChat" id="sincloChatMessage" placeholder="メッセージを入力してください{{chat_area_placeholder_sp}}"></textarea>
          <a id="sincloChatSendBtn" class="notSelect"><span>送信</span></a>
        </div>
      </section>
    </div>
  </div>
<?php endif; ?>
<!-- スマホ版 -->

</section>
