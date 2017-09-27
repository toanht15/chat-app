<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
<style>
    .showType4{
      width: 285px;
    }
    .showType4.middleSize{
      width: 342.5px;
    }
    .showType4.largeSize{
      width: 400px;
    }
    #m_widget_setting_idx #m_widget_setting_form #m_widget_simulator section#switch_widget.showBanner{
      margin-bottom: 3em;
    }
</style>
<section id="switch_widget" ng-cloak ng-class="{showBanner:closeButtonModeTypeToggle === '1' && closeButtonSettingToggle === '2' && showWidgetType === 4}">
  <ul class="ulTab" data-col=3 ng-hide="closeButtonSettingToggle === '2'">
    <li ng-class="{choose: showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    <li ng-class="{choose: showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
    <li ng-class="{choose: showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
  </ul>
  <ul class="ulTab showType4" data-col=3 ng-hide="closeButtonSettingToggle !== '2'" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
    <li ng-class="{choose: showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    <li ng-class="{choose: showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
    <li ng-class="{choose: showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
    <li ng-class="{choose: showWidgetType === 4}" ng-click="switchWidget(4)">非表示</li>
  </ul>
  <input type="hidden" id="switch_widget" value="">
</section>
<?php endif; ?>

<section id="sample_widget_area" ng-cloak>
  <style>
    #sincloBox {
      position: relative;
      z-index: 1;
      width: 285px;
      background-color: rgb(255, 255, 255);
      box-shadow: 0px 0px {{box_shadow}}px {{box_shadow}}px rgba(0,0,0,0.1);
      /* z風 */
      /*box-shadow: 0px -2px 3px 2px rgba(0,0,0,calc(({{box_shadow}} * 0.1) / 2));*/
      /* d風 */
      /* box-shadow: 0 10px 20px rgba(0,0,0,calc(({{box_shadow}} * 0.1) / 2)); */
      border-radius: {{radius_ratio}}px {{radius_ratio}}px 0 0;
    }
    #sincloBox.middleSize{
      width: 342.5px;
    }
    #sincloBox.largeSize{
      width: 400px;
    }
  </style>
  <div id="sincloBox" ng-if="showWidgetType !== 2" ng-hide="showWidgetType === 4" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
    <style>
      #sincloBox * { font-size: 12px; }
      #sincloBox.middleSize  * { font-size: 13px; }
      #sincloBox.largeSize * { font-size: 13px; }
      #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
      #sincloBox span#mainImage { cursor: pointer; z-index: 2; position: absolute; top: 7px; left: 10px; }
      #sincloBox span#mainImage img { background-color: {{main_color}}; }
      #sincloBox .pb07 { padding-bottom: 7px }
      #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
      #sincloBox .center { text-align: center!important; padding: 7px 30px!important }
      #sincloBox div#descriptionSet { cursor: pointer; }
      #sincloBox p#widgetTitle { position:relative; z-index: 1; cursor:pointer; border-radius: {{radius_ratio}}px {{radius_ratio}}px 0 0; border: 1px solid {{main_color}}; border-bottom:none; background-color: {{main_color}};text-align: center; font-size: 14px; padding: 7px 30px 7px 70px; margin: 0; color: {{string_color}}; height: 32px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
      #sincloBox p#widgetTitle.spText{ text-indent: 1em; }
      #sincloBox p#widgetTitle.middleSize { font-size: 15px; }
      #sincloBox p#widgetTitle.largeSize { font-size: 15px; }
      #sincloBox div#minimizeBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>minimize.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
      #sincloBox div#addBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>add.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
      #sincloBox div#addBtn.closeButtonSetting { right: 25px; }
      #sincloBox div#closeBtn { display: none; cursor: pointer; background-image: url('<?=$gallaryPath?>close.png'); background-position-y: -1.5px; top: 6px; right: 10px; bottom: 6px; content: " "; width: 23px; height: 23px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
      #sincloBox div#closeBtn.closeButtonSetting {display: inline-block; right: 5px; }
/*
      #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
      #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
      #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
*/
      #sincloBox p#widgetSubTitle { background-color: #FFF; margin: 0; padding: 7px 0; text-align: left; border-width: 0 1px 0 1px; border-color: #E8E7E0; border-style: solid; padding-left: 77px; font-weight: bold; color: {{main_color}}; height: 29px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
      #sincloBox p#widgetDescription { background-color: #FFF; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: #E8E7E0; border-style: solid; padding-left: 77px; height: 23px; color: #666666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
      #sincloBox section { display: inline-block; width: 285px; border: 1px solid #E8E7E0; border-top: none; }
      #sincloBox section.middleSize { width: 342.5px; }
      #sincloBox section.largeSize { width: 400px; }
      #sincloBox section.noDisplay { display: none }
      #sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
      @keyframes leftEffect { 0% { transform :translate3d(-20px, 0px, 0px) scale(0.8); opacity :0; } 69% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }
      #sincloBox ul#chatTalk { width: 100%; height: 194px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
      #sincloBox ul#chatTalk.middleSize { height: 284px; }
      #sincloBox ul#chatTalk.largeSize { height: 374px; }
      #sincloBox ul#chatTalk div.liLeft { text-align: left; }
      #sincloBox ul#chatTalk div.liRight { text-align: right; }
      #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0 0; padding: 8px; font-size: 12px; border: 1px solid #C9C9C9; line-height: 1.4; white-space: pre; color: #333333; }
      #sincloBox ul#chatTalk li.middleSize { font-size: 13px; }
      #sincloBox ul#chatTalk li.largeSize { font-size: 13px; }
      #sincloBox ul#chatTalk li.boxType { word-wrap: break-word; word-break: break-all; }
      #sincloBox ul#chatTalk li.balloonType { display: inline-block; position: relative; padding: 5px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
      #sincloBox ul#chatTalk li.sinclo_se.balloonType { margin-right: 15px; border-bottom-right-radius: 0px; }
      #sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; border: 5px solid transparent; border-left: 5px solid #FFF; border-bottom: 5px solid #FFF; z-index: 2; }
      #sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; border-left: 5px solid #C9C9C9; border-bottom: 5px solid #C9C9C9; z-index: 1; }
      /* 二等辺三角形バージョン */
      /*#sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 5px; left: calc(100% - 2.5px); margin-top: -10px; border: 10px solid transparent; border-left: 10px solid #FFF; z-index: 2; }*/
      /*#sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: 6px; left: 100%; margin-top: -9px; border: 9px solid transparent; border-left: 9px solid #C9C9C9; z-index: 1; }*/
      #sincloBox ul#chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px; }
      #sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; border: 5px solid transparent; border-right: 5px solid {{makeBalloonTriangleColor()}}; border-bottom: 5px solid {{makeBalloonTriangleColor()}}; z-index: 2; }
      #sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; border: 5px solid transparent; border-right: 5px solid #C9C9C9; border-bottom: 5px solid #C9C9C9; z-index: 1; }
      /* 二等辺三角形バージョン */
      /*#sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 5px; left: -19px; margin-top: -10px; border: 10px solid transparent; border-right: 10px solid {{makeBalloonTriangleColor()}}; z-index: 2; }*/
      /*#sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: 6px; left: -19px; margin-top: -9px; border: 9px solid transparent; border-right: 9px solid #C9C9C9; z-index: 1; }*/
      #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
      #sincloBox ul#chatTalk li.boxType.chat_right { border-bottom-right-radius: 0; margin-left: 10px }
      #sincloBox ul#chatTalk li.boxType.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
      #sincloBox ul#chatTalk li.balloonType.chat_right { margin-left: 15px }
      #sincloBox ul#chatTalk li.balloonType.chat_left { margin-right: 10px }
      #sincloBox ul#chatTalk li span.cName { display: block; color: {{main_color}}!important; font-weight: bold; font-size: 13px; margin: 0 0 5px 0; }
      #sincloBox ul#chatTalk li span.cName.middleSize { font-size: 14px }
      #sincloBox ul#chatTalk li span.cName.largeSize { font-size: 14px }
      #sincloBox section#chatTab div { height: 75px!important; padding: 5px; }
      #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; color: #666666; margin: 0; border-radius: 5px 0 0 5px!important; resize: none; color: #666666; padding: 5px; border: 1px solid #C9C9C9!important; border-right-color: transparent!important; }
      #sincloBox section#chatTab textarea#sincloChatMessage:focus { border-color: {{main_color}}!important; outline: none!important; border-right-color: transparent!important; }
      #sincloBox section#chatTab #sincloChatSendBtn{ width: 20%; height: 100%; padding: 20px 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{main_color}}!important; color: {{string_color}}; font-weight: bold; font-size: 1.2em;}
      #sincloBox section#chatTab #sincloChatSendBtn.middleSize{ padding: 20px 0; margin: 0 auto; font-size: 1.2em;}
      #sincloBox section#chatTab #sincloChatSendBtn.largeSize{ padding: 20px 0; margin: 0 auto; font-size: 1.2em;}
      #sincloBox section#chatTab #sincloChatSendBtn span { color: {{string_color}} }
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
      #sincloBox section#callTab{height: 296.5px;}
      #sincloBox section#callTab.middleSize {height: 387px;}
      #sincloBox section#callTab.largeSize {height: 476.5px;}
      #sincloBox section#callTab #telNumber { overflow: hidden; color: {{main_color}}; font-weight: bold; margin: 0 auto; text-align: center }
      #sincloBox section#callTab #telNumber:not(.notUseTime) { font-size: 18px; padding: 5px 0px 0px; height: 30px }
      #sincloBox section#callTab #telNumber.middleSize:not(.notUseTime) { font-size: 19px; padding: 5px 0px 0px; height: 30px }
      #sincloBox section#callTab #telNumber.largeSize:not(.notUseTime) { font-size: 19px; padding: 5px 0px 0px; height: 30px }
      #sincloBox section#callTab #telNumber.notUseTime { font-size: 20px; padding: 10px 0px 0px; height: 45px }
      #sincloBox section#callTab #telNumber.notUseTime.middleSize { font-size: 21px; padding: 10px 0px 0px; height: 45px }
      #sincloBox section#callTab #telNumber.notUseTime.largeSize { font-size: 21px; padding: 10px 0px 0px; height: 45px }
      #sincloBox section#callTab #telIcon { background-color: {{main_color}}; display: block; width: 50px; height: 50px; float: left; border-radius: 25px; padding: 3px }
      #sincloBox section#callTab #telTime { font-weight: bold; color: {{main_color}}; margin: 0 auto; white-space: pre-line; font-size: 11px; text-align: center; padding: 0 0 5px; height: 20px }
      #sincloBox section#callTab #telTime.middleSize { font-size: 12px; padding: 0 0 5px; height: 20px }
      #sincloBox section#callTab #telTime.largeSize { font-size: 12px; padding: 0 0 5px; height: 20px }
      #sincloBox section#callTab #telContent { display: block; overflow-y: auto; overflow-x: hidden; max-height: 119px }
      #sincloBox section#callTab #telContent.middleSize{ max-height: 202px; height: 202px}
      #sincloBox section#callTab #telContent.largeSize{ max-height: 280px; height: 280px}
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
        #sincloBox section#callTab #telContent .tblBlock {  text-align: center;  margin: 0 auto;  width: 240px;  display: table;  flex-direction: column;  align-content: center;  height: 119px!important;  justify-content: center; }
      <?php else: ?>
        #sincloBox section#callTab #telContent .tblBlock { text-align: center; margin: 0 auto; width: 240px; display: table; flex-direction: column; align-content: center; justify-content: center; }
      <?php endif; ?>
      #sincloBox section#callTab #telContent span { word-wrap: break-word; word-break: break-all; font-size: 11px; line-height: 1.5!important; color: #6B6B6B; white-space: pre-wrap; display: table-cell; vertical-align: middle; text-align: center }
      #sincloBox section#callTab #telContent span.middleSize { font-size: 12px; line-height: 1.5!important; }
      #sincloBox section#callTab #telContent span.largeSize { font-size: 12px; line-height: 1.5!important; }
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ): ?>
      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 18px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border-radius: 15px }
      #sincloBox section#callTab #accessIdArea.middleSize { margin: 18px auto; padding: 7px; font-size: 26px; }
      #sincloBox section#callTab #accessIdArea.largeSize { margin: 18px auto; padding: 7px; font-size: 26px; }
      <?php else: ?>
      #sincloBox section#callTab #accessIdArea { height: 50px; display: block; margin: 10px auto; width: 80%; padding: 7px;  color: #FFF; background-color: rgb(188, 188, 188); font-size: 25px; font-weight: bold; text-align: center; border-radius: 15px }
      #sincloBox section#callTab #accessIdArea.middleSize { margin: 10px auto; padding: 7px; font-size: 26px; }
      #sincloBox section#callTab #accessIdArea.largeSize { margin: 10px auto; padding: 7px; font-size: 26px; }
      <?php endif; ?>
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && ($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])) ) :?>
      #sincloBox section#navigation { border-width: 0 1px; height: 40px; position: relative; display: block; }
      #sincloBox section#navigation ul { margin: 0 0 0 -1px; display: table; padding: 0; position: absolute; top: 0; left: 0; height: 40px; width: 285px }
      #sincloBox section#navigation ul.middleSize { width: 343.5px }
      #sincloBox section#navigation ul.largeSize { width: 400px }
      #sincloBox section#navigation ul li { position: relative; overflow: hidden; cursor: pointer; color: #666666; width: 50%; text-align: center; display: table-cell; padding: 10px 0; border-left: 1px solid #E8E7E0; height: 40px }
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
      <p id="widgetTitle" class="widgetOpener notSelect" ng-class="{center: spHeaderLightToggle() || mainImageToggle !== '1',middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3', spText:showWidgetType === 3}">{{title}}</p>
      <!-- タイトル -->
    </div>
    <div id="minimizeBtn" class="widgetOpener" ng-class="" style="display: block;"></div>
    <div id="addBtn" class="widgetOpener" ng-class="{closeButtonSetting: closeButtonSettingToggle === '2'}" style="display: none;"></div>
    <div id="closeBtn" ng-click="switchWidget(4)" ng-class="{closeButtonSetting: closeButtonSettingToggle === '2'}"></div>
    <div id='descriptionSet' class="widgetOpener notSelect" ng-hide=" spHeaderLightToggle() || mainImageToggle == '2' && subTitleToggle == '2' && descriptionToggle == '2'">
      <!-- サブタイトル -->
      <p ng-if="subTitleToggle == '1'" id="widgetSubTitle" >{{sub_title}}</p>
      <p ng-if="subTitleToggle == '2'" id="widgetSubTitle"></p>
      <!-- サブタイトル -->

      <!-- 説明文 -->
      <p ng-if="descriptionToggle == '1'" id="widgetDescription" >{{description}}</p>
      <p ng-if="descriptionToggle == '2'" id="widgetDescription"></p>
      <!-- 説明文 -->
    </div>
    <div id="miniTarget">
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && ($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])) ) :?>
      <section id="navigation" ng-hide="showWidgetType === 3" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
        <ul ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
          <li data-tab="chat" class="widgetCtrl notSelect" ng-class="{selected: widget.showTab == 'chat'}">チャットでの受付</li>
          <li data-tab="call" class="widgetCtrl notSelect" ng-class="{selected: widget.showTab == 'call'}">電話での受付</li>
        </ul>
      </section>
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
      <section id="chatTab" ng-hide="widget.showTab !== 'chat'" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
        <ul id="chatTalk" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
          <div style="height: auto!important; padding:0;" ng-class="{liLeft: chat_message_design_type == 1, liRight: chat_message_design_type == 2}" >
          <li class="sinclo_se chat_right" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}" >○○について質問したいのですが</li>
          </div>
          <div style="height: auto!important; padding:0;">
            <li class="sinclo_re chat_left" ng-style="{backgroundColor:makeFaintColor()}" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}"><span class="cName" ng-if="show_name == 1" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}"><?=$userInfo['display_name']?></span><span class="cName" ng-if="show_name == 2" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">{{sub_title}}</span>こんにちは</li>
          </div>
          <div style="height: auto!important; padding:0;">
            <li class="showAnimationSample sinclo_re chat_left" ng-style="{backgroundColor:makeFaintColor()}" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}"><span class="cName" ng-if="show_name == 1" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}"><?=$userInfo['display_name']?></span><span class="cName" ng-if="show_name == 2" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">{{sub_title}}</span>○○についてですね<br>どのようなご質問でしょうか？</li>
          </div>
        </ul>
        <div style="border-top: 1px solid #E8E7E0; padding: 0.5em;">
          <textarea name="sincloChat" id="sincloChatMessage" placeholder="メッセージを入力してください{{chat_area_placeholder_pc}}"></textarea>
          <a id="sincloChatSendBtn" class="notSelect" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}"><span>送信</span></a>
        </div>
      <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
        <span id="sincloAccessInfo" style="height: 26.5px; display: block; padding-left: 0.5em; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #E8E7E0; font-size: 0.9em;" ng-hide="showWidgetType === 3">ウェブ接客コード：●●●●</span>
        <?php endif; ?>
      </section>
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
      <section id="callTab" ng-hide="widget.showTab !== 'call'" ng-class="{middleSize: widgetSizeTypeToggle === '2',largeSize: widgetSizeTypeToggle === '3'}">
        <div style="height: 50px;margin: 15px 25px;">
        <!-- アイコン -->
        <span id="telIcon"><img width="19.5" height="33" src="<?=C_PATH_NODE_FILE_SERVER?>/img/call.png" style="margin: 6px 12px"></span>
        <!-- アイコン -->

        <!-- 受付電話番号 -->
        <pre id="telNumber" ng-class="{notUseTime: timeTextToggle !== '1',middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}" >{{tel}}</pre>
        <!-- 受付電話番号 -->

        <!-- 受付時間 -->
        <pre id="telTime" ng-if="timeTextToggle == '1'" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">受付時間： {{time_text}}</pre>
        <!-- 受付時間 -->

        </div>

        <!-- テキスト -->
        <div id="telContent" ng-class="{middleSize: widgetSizeTypeToggle === '2',largeSize: widgetSizeTypeToggle === '3'}"><div class="tblBlock"><span ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">{{content}}</span></div></div>
        <!-- テキスト -->

        <span id="accessIdArea" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
        ●●●●
        </span>
      </section>
    <?php endif; ?>
      <p style="height: 26.5px; padding: 5px 0;text-align: center;border: 1px solid #E8E7E0;color: #A1A1A1!important;font-size: 11px;margin: 0;border-top: none;">Powered by <a target="sinclo" href="https://sinclo.medialink-ml.co.jp/lp/?utm_medium=web-widget&utm_campaign=widget-referral">sinclo</a></p>
    </div>
  </div>
  <!-- バナー -->
  <style>
    @font-face {
      font-family: 'SincloFont';
      src: url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.eot?v=4.0.3');
      src: url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.eot?#iefix&v=4.0.3') format('embedded-opentype'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.woff?v=4.0.3') format('woff'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.ttf?v=4.0.3') format('truetype'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.svg?v=4.0.3#fontawesomeregular') format('svg');
      font-weight: normal;
      font-style: normal
    }

    .sinclo-fa {
      display: inline-block;
      font-family: SincloFont;
      font-style: normal;
      font-weight: normal;
      line-height: 1;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      cursor: pointer;
    }

    .sinclo-fa .fa-comment:before {
      content: "\f075"
    }
    #sincloBanner {
      position: relative;
      z-index: 1;
      height: 60px;
      width : fit-content !important;
      background-color: {{main_color}};
      box-shadow: 0px 0px calc( {{box_shadow}}px + 1px) {{box_shadow}}px rgba(0,0,0,0.1);
      border-radius: {{radius_ratio}}px {{radius_ratio}}px {{radius_ratio}}px {{radius_ratio}}px;
      color: {{string_color}};
      margin: auto;
      filter:alpha(opacity=80);
      -moz-opacity: 0.8;
      opacity: 0.8;
      top: -30px;
      cursor: pointer;
      zoom: 70%;
    }
    #sincloBannerText{
      line-height: 60px;
      height: auto!important;
      width: auto!important;
      padding:0;
    }
    #sincloBanner i{
      color: {{string_color}};
    }
    #sincloBanner #sinclo-comment{
      transform: scale( 1 , 1.4 );
      font-size: 25px;
      padding: 0 15px 0 15px;
      cursor: pointer;
    }
    #sincloBanner #bannertext{
      font-size: 18px;
      padding: 0 15px 0 0;
      cursor: pointer;
    }
  </style>
  <div id="sincloBanner" ng-click="bannerSwitchWidget()" ng-if="closeButtonModeTypeToggle === '1' && closeButtonSettingToggle === '2' && showWidgetType === 4">
    <div id="sincloBannerText" ng-click="bannerSwitchWidget()">
      <span ng-click="bannerSwitchWidget()"><i id="sinclo-comment" class="sinclo-fa fa-comment">&#xf075;</i><i id="bannertext">{{bannertext}}</i></span>
    </div>
  </div>
  <!-- バナー -->

<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
<!-- スマホ版 -->
  <div id="sincloBox" ng-if="showWidgetType === 2">
    <style>
      #sincloBox * { font-size: 12px; }
      #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
      #sincloBox .pb07 { padding-bottom: 7px }
      #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
      #sincloBox p#widgetTitle { text-align: center!important; padding: 7px 30px!important; position:relative; z-index: 1; cursor:pointer; border-radius: 0; border: 1px solid {{main_color}}; border-bottom:none; background-color: {{main_color}};text-align: center; font-size: 14px; margin: 0;color: {{string_color}}; height: 32px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
      #sincloBox.open #minimizeBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>minimize.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
      #sincloBox:not(.open) #addBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>add.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }

/*
      #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
      #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
      #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
*/
      #sincloBox section { display: inline-block; width: 285px; border: 1px solid #E8E7E0; border-top: none; }
      #sincloBox section.noDisplay { display: none }
      #sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
      @keyframes leftEffect { 0% { transform :translate3d(-20px, 0px, 0px) scale(0.8); opacity :0; } 69% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }
      #sincloBox ul#chatTalk { width: 100%; height: 100px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
      #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0 0; padding: 3px; font-size: 11px; border: 1px solid #C9C9C9; line-height: 1.4; white-space: pre; color: #333333; }
      #sincloBox ul#chatTalk div.liLeft { text-align: left; }
      #sincloBox ul#chatTalk div.liRight { text-align: right; }
      #sincloBox ul#chatTalk li.boxType { word-wrap: break-word; word-break: break-all; }
      #sincloBox ul#chatTalk li.balloonType { display: inline-block; position: relative; padding: 5px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
      #sincloBox ul#chatTalk li.sinclo_se.balloonType { margin-right: 15px; border-bottom-right-radius: 0px; }
      #sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; border: 5px solid transparent; border-left: 5px solid #FFF; border-bottom: 5px solid #FFF; z-index: 2; }
      #sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; border-left: 5px solid #C9C9C9; border-bottom: 5px solid #C9C9C9; z-index: 1; }
      #sincloBox ul#chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px; }
      #sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; border: 5px solid transparent; border-right: 5px solid {{makeBalloonTriangleColor()}}; border-bottom: 5px solid {{makeBalloonTriangleColor()}}; z-index: 2; }
      #sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; border: 5px solid transparent; border-right: 5px solid #C9C9C9; border-bottom: 5px solid #C9C9C9; z-index: 1; }
      #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
      #sincloBox ul#chatTalk li.boxType.chat_right { border-bottom-right-radius: 0; margin-left: 10px }
      #sincloBox ul#chatTalk li.boxType.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
      #sincloBox ul#chatTalk li.balloonType.chat_right { margin-left: 15px }
      #sincloBox ul#chatTalk li.balloonType.chat_left { margin-right: 10px }
      #sincloBox ul#chatTalk li span.cName { display: block; color: {{main_color}}!important; font-weight: bold; font-size: 12px; margin: 0 0 5px 0; }
      #sincloBox section#chatTab div { height: 65px!important;  padding: 10px; }
      #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; margin: 0; font-size: 11px; color: #666666; border-radius: 5px 0 0 5px!important; resize: none; color: #666666; padding: 5px; border: 1px solid #C9C9C9!important; border-right-color: transparent!important; }
      #sincloBox section#chatTab textarea#sincloChatMessage:focus { border-color: {{main_color}}!important; outline: none!important; border-right-color: transparent!important; }
      #sincloBox section#chatTab #sincloChatSendBtn{ width: 20%; height: 100%; padding: 1em 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{main_color}}!important; color: {{string_color}}; font-weight: bold; font-size: 1.2em;}
      #sincloBox section#chatTab #sincloChatSendBtn span { color: {{string_color}} }
    </style>
    <div>
      <!-- タイトル -->
      <p id="widgetTitle" class="widgetOpener" ng-class="{center: mainImageToggle == '2'}">{{title}}</p>
      <!-- タイトル -->
    </div>
    <div id="minimizeBtn" class="widgetOpener"></div>
    <div id="addBtn" class="widgetOpener"></div>
    <div id="miniTarget">
      <section id="chatTab">
        <ul id="chatTalk">
          <div style="height: auto!important; padding:0;" ng-class="{liLeft: chat_message_design_type == 1, liRight: chat_message_design_type == 2}">
            <li class="sinclo_se chat_right" ng-class="{boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}">○○について質問したいのですが</li>
          </div>
          <div style="height: auto!important; padding:0;">
            <li class="sinclo_re chat_left" ng-class="{boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName">{{sub_title}}</span>こんにちは</li>
          </div>
          <div style="height: auto!important; padding:0;">
            <li class="showAnimationSample sinclo_re chat_left" ng-class="{boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName">{{sub_title}}</span>○○についてですね<br>どのようなご質問でしょうか？</li>
          </div>
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
