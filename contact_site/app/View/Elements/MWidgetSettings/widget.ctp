<div id="sincloBox" <?= !empty($isSpPreview) ? 'class="sp-preview" ng-class=\'{fullSize:sp_maximize_size_type==="2", simpleHeader:sp_header_light_flg==="1"}\' ng-if="showWidgetType === 3"' : "ng-if=\"showWidgetType !== 2 && showWidgetType !== 3\"" ?>  ng-hide="showWidgetType === 4" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
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
    #sincloBox p#widgetTitle { position:relative; z-index: 1; cursor:pointer; border-radius: {{radius_ratio}}px {{radius_ratio}}px 0 0; border: 1px solid {{main_color}}; border-bottom:none; background-color: {{main_color}};text-align: center; font-size: {{header_text_size}}px; padding: 7px 30px 7px 70px; margin: 0; color: {{string_color}}; height: auto; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: {{header_text_size}}px;}
    #sincloBox p#widgetTitle.sp { font-size: 14px;}
    #sincloBox p#widgetTitle.spText{ text-indent: 1em; }
    #sincloBox div#minimizeBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>minimize.png'); background-position-y: 0px; position: absolute; top: calc(50% - 10px); right: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
    #sincloBox div.widgetHeaderWrap:hover #widgetTitle { opacity: 0.75; }
    #sincloBox div.widgetHeaderWrap #titleWrap { position: relative; }
    /*
          #sincloBox div#addBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>add.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
      #sincloBox div#addBtn.closeButtonSetting { right: 25px; }
*/
    #sincloBox div#closeBtn { display: none; cursor: pointer; background-image: url('<?=$gallaryPath?>close.png'); background-position-y: -1.5px; position: absolute; top: calc(50% - 9px); right: 6px; content: " "; width: 18px; height: 18px; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
    #sincloBox div#closeBtn.closeButtonSetting {display: inline-block; right: 5px; }
    /*
          #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
      #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
      #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
*/
    #sincloBox p#widgetSubTitle { background-color: {{header_background_color}}; margin: 0; padding: 3px 0; text-align: left; border-width: 0 1px 0 1px; border-color: {{widget_border_color}}; border-style: solid; padding-left: 77px; font-weight: bold; color: {{main_color}}; height: auto; line-height: 24px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: {{header_text_size-2}}px; }
    #sincloBox p#widgetSubTitle.sp { font-size: 12px; }
    #sincloBox p#widgetSubTitle:not(.notNoneWidgetOutsideBorder) { border:none; }
    #sincloBox p#widgetSubTitle.details { color: {{sub_title_text_color}}; }
    #sincloBox p#widgetDescription { background-color: {{header_background_color}}; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: {{widget_border_color}}; border-style: solid; padding-left: 77px; height: auto; line-height: 15px; color: {{other_text_color}}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: {{header_text_size-2}}px; }
    #sincloBox p#widgetDescription.sp { font-size: 12px; }
    #sincloBox p#widgetDescription:not(.notNoneWidgetOutsideBorder) { border-left:none; border-right:none; }
    #sincloBox p#widgetDescription.details { color: {{description_text_color}}; border-bottom-color: {{widget_inside_border_color}}; }
    #sincloBox p#widgetDescription.details:not(.notNone) { border-bottom:none }
    #sincloBox section { display: inline-block; width: 100%; border: 1px solid {{widget_border_color}}; border-top: none; }
    #sincloBox section:not(.notNoneWidgetOutsideBorder) { border: 1px solid {{widget_inside_border_color}};  border-top: none; border-left:none; border-right:none; }
    #sincloBox section.details { border-bottom-color: {{widget_inside_border_color}}; }
    #sincloBox section.details:not(.notNone) { border-bottom:none }
    #sincloBox section.middleSize { width: 342.5px; }
    #sincloBox section.largeSize { width: 400px; }
    #sincloBox section.noDisplay { display: none }
    #sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
    @keyframes leftEffect { 0% { transform :translate3d(-20px, 0px, 0px) scale(0.8); opacity :0; } 69% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }
    #sincloBox ul#chatTalk { width: 100%; height: 194px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
    #sincloBox ul#chatTalk.middleSize { height: 284px; }
    #sincloBox ul#chatTalk.largeSize { height: 374px; }
    #sincloBox ul#chatTalk.details { background-color: {{chat_talk_background_color}}; }
    #sincloBox ul#chatTalk div.liLeft { text-align: left; }
    #sincloBox ul#chatTalk div.liRight { text-align: right; }
    #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0 0; padding: 10px; font-size: 12px; line-height: 1.4; white-space: pre; }
    #sincloBox ul#chatTalk li.middleSize { font-size: 13px; }
    #sincloBox ul#chatTalk li.largeSize { font-size: 13px; }
    #sincloBox ul#chatTalk li.boxType { word-wrap: break-word; word-break: break-all; }
    #sincloBox ul#chatTalk li.balloonType { display: inline-block; position: relative; padding: 5px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
    #sincloBox ul#chatTalk li.sinclo_se.details { background-color: {{getSeBackgroundColor()}}; }
    #sincloBox ul#chatTalk li.sinclo_se.notNone { border: 1px solid {{getTalkBorderColor('se')}}; }
    #sincloBox ul#chatTalk li.sinclo_se.balloonType { margin-right: 15px; border-bottom-right-radius: 0px; }
    #sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; z-index: 2; border: 5px solid transparent; border-left: 5px solid {{getSeBackgroundColor()}}; border-bottom: 5px solid {{getSeBackgroundColor()}}; }
    #sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
    #sincloBox ul#chatTalk li.sinclo_se.balloonType.notNone:after { border-left: 5px solid {{getTalkBorderColor('se')}}; border-bottom: 5px solid {{getTalkBorderColor('se')}}; }
    /* 二等辺三角形バージョン */
    /*#sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 5px; left: calc(100% - 2.5px); margin-top: -10px; border: 10px solid transparent; border-left: 10px solid #FFF; z-index: 2; }*/
    /*#sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: 6px; left: 100%; margin-top: -9px; border: 9px solid transparent; border-left: 9px solid #C9C9C9; z-index: 1; }*/
    #sincloBox ul#chatTalk li.sinclo_re.notNone { border: 1px solid {{getTalkBorderColor('re')}}; }
    #sincloBox ul#chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px; }
    #sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; z-index: 2; border: 5px solid transparent; border-right: 5px solid {{makeFaintColor()}}; border-bottom: 5px solid {{makeFaintColor()}}; }
    #sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; z-index: 1; border: 5px solid transparent; }
    #sincloBox ul#chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{getTalkBorderColor('re')}}; border-bottom: 5px solid {{getTalkBorderColor('re')}}; }
    /* 二等辺三角形バージョン */
    /*#sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 5px; left: -19px; margin-top: -10px; border: 10px solid transparent; border-right: 10px solid {{makeBalloonTriangleColor()}}; z-index: 2; }*/
    /*#sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: 6px; left: -19px; margin-top: -9px; border: 9px solid transparent; border-right: 9px solid #C9C9C9; z-index: 1; }*/
    #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
    #sincloBox ul#chatTalk li.boxType.chat_right { border-radius: 12px 12px 0 12px; margin-left: auto; margin-right:10px; width: 80%; }
    #sincloBox ul#chatTalk li.boxType.chat_left { border-radius: 12px 12px 12px 0; margin-right: auto; margin-left:10px; width: 80%; }
    #sincloBox ul#chatTalk li.balloonType.chat_right { margin-left: 15px }
    #sincloBox ul#chatTalk li.balloonType.chat_left { margin-right: 10px }
    #sincloBox ul#chatTalk li span.cName { display: block; color: {{main_color}}!important; font-weight: bold; font-size: {{re_text_size+1}}px; margin: 0 0 5px 0; }
    #sincloBox ul#chatTalk li span.cName.middleSize { font-size: {{re_text_size+1}}px; }
    #sincloBox ul#chatTalk li span.cName.largeSize { font-size: {{re_text_size+1}}px; }
    #sincloBox ul#chatTalk li span.cName.details{ color: {{c_name_text_color}}!important;}
    #sincloBox ul#chatTalk li span.cName:not(.details){ color: {{main_color}}!important;}
    #sincloBox ul#chatTalk li span:not(.details){  color: {{message_text_color}}!important; }
    #sincloBox ul#chatTalk li.sinclo_etc { border: none; text-align: center!important; margin: 0 auto; font-weight: bold; background-color: transparent!important; }
    #sincloBox ul#chatTalk li.sinclo_re span.details{ color: {{re_text_color}}; font-size: {{re_text_size}}px; }
    #sincloBox ul#chatTalk li.sinclo_re span.details.sp{ font-size: 12px; }
    #sincloBox ul#chatTalk li.sinclo_se span.details{ color: {{se_text_color}}; font-size: {{se_text_size}}px; }
    #sincloBox ul#chatTalk li.sinclo_se span.details.sp{ font-size: 12px; }
    #sincloBox section#chatTab div { height: 75px!important; padding: 5px; }
    #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; color: {{other_text_color}}; margin: 0; resize: none; padding: 5px; }
    #sincloBox section#chatTab textarea#sincloChatMessage.details { color: {{message_box_text_color}}; background-color: {{message_box_background_color}}; }
    #sincloBox section#chatTab textarea#sincloChatMessage.details.notNone { border: 1px solid {{message_box_border_color}}!important; }
    #sincloBox section#chatTab textarea#sincloChatMessage.notNone { border-radius: 5px 0 0 5px!important; border: 1px solid {{chat_talk_border_color}}!important; border-right-color: transparent!important; }
    #sincloBox section#chatTab textarea#sincloChatMessage.notNone:focus { border-color: {{main_color}}!important; outline: none!important; border-right-color: transparent!important; }
    #sincloBox section#chatTab textarea#sincloChatMessage:not(.notNone){ border: none!important }
    #sincloBox section#chatTab #sincloChatSendBtn{ width: 20%; height: 100%; padding: 20px 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{main_color}}!important; color: {{string_color}}; font-weight: bold; font-size: 1.2em;}
    #sincloBox section#chatTab #sincloChatSendBtn.details{ background-color: {{chat_send_btn_background_color}}!important; }
    #sincloBox section#chatTab #sincloChatSendBtn.middleSize{ padding: 20px 0; margin: 0 auto; font-size: 1.2em;}
    #sincloBox section#chatTab #sincloChatSendBtn.largeSize{ padding: 20px 0; margin: 0 auto; font-size: 1.2em;}
    #sincloBox section#chatTab #sincloChatSendBtn span { color: {{string_color}} }
    #sincloBox section#chatTab #sincloChatSendBtn span.details { color: {{chat_send_btn_text_color}} }
    #sincloBox section#chatTab #messageBox.messageBox{border-top: 1px solid {{widget_border_color}}; padding: 0.5em;}
    #sincloBox section#chatTab #messageBox.messageBox:not(.notNoneWidgetOutsideBorder) { border-top:none; }
    #sincloBox section#chatTab #messageBox.messageBox.details{ background-color: {{chat_message_background_color}}; border-top: 1px solid {{widget_inside_border_color}}; }
    #sincloBox section#chatTab #messageBox.messageBox.details:not(.notNone){ border-top: none; }
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
    #sincloBox section#callTab{height: 296.5px;}
    #sincloBox section#callTab.middleSize {height: 387px;}
    #sincloBox section#callTab.largeSize {height: 476.5px;}
    #sincloBox section#callTab.details { background-color: {{chat_talk_background_color}}; }
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
    #sincloBox section#navigation ul li { position: relative; overflow: hidden; cursor: pointer; color: {{other_text_color}}; width: 50%; text-align: center; display: table-cell; padding: 10px 0; height: 40px }
    #sincloBox section#navigation ul li:not(.details) { border-left: 1px solid {{widget_border_color}}; }
    #sincloBox section#navigation ul li:not(.details):not(.notNoneWidgetOutsideBorder) { border-left:none; }
    #sincloBox section#navigation ul li:last-child { border-right: 1px solid {{widget_border_color}}; }
    #sincloBox section#navigation ul li:last-child:not(.notNoneWidgetOutsideBorder) { border-right:none; }
    #sincloBox section#navigation ul li.selected { background-color: #FFFFFF; z-index: -1; }
    #sincloBox section#navigation ul li:not(.selected) { border-bottom: 1px solid {{widget_border_color}}; }
    #sincloBox section#navigation ul li:not(.selected):not(.notNoneWidgetOutsideBorder) { border-bottom:none; }

    #sincloBox section#navigation ul li.selected.details { background-color: {{chat_talk_background_color}}; }
    #sincloBox section#navigation ul li:not(.selected).details {  }

    #sincloBox section#navigation ul li.details:not(.selected) { border-bottom: 1px solid {{widget_inside_border_color}} }
    #sincloBox section#navigation ul li.details:not(.selected):not(.notNone) { border-bottom: none }
    #sincloBox section#navigation ul li[data-tab='call'].details:not(.selected){ border-left: 1px solid {{widget_inside_border_color}}; }
    #sincloBox section#navigation ul li[data-tab='call'].details:not(.selected):not(.notNone){ border-left: none }
    #sincloBox section#navigation ul li[data-tab='chat'].details:not(.selected){ border-right: 1px solid {{widget_inside_border_color}}; }
    #sincloBox section#navigation ul li[data-tab='chat'].details:not(.selected):not(.notNone){ border-right: none }
    #sincloBox section#navigation ul li.selected::after{ content: " "; border-bottom: 2px solid {{main_color}}; position: absolute; bottom: 0px; left: 5px; right: 5px;}
    #sincloBox section#navigation ul li::before{ margin-right: 5px; color: #BCBCBC; content: " "; display: inline-block; width: 18px; height: 18px; position: relative; background-size: contain; vertical-align: middle; background-repeat: no-repeat }
    /*
          #sincloBox section#navigation ul li[data-tab='call']::before{ background-image: url('/img/widget/icon_tel.png'); }
          #sincloBox section#navigation ul li[data-tab='chat']::before{ background-image: url('/img/widget/icon_chat.png'); }
    */
    #sincloBox section#navigation ul li[data-tab='call']::before{ content: "\f095"; font-family: SincloFont; font-size: 17px; margin: -5px 7px 0 0; font-weight: bold;}
    #sincloBox section#navigation ul li[data-tab='chat']::before{ content: "\f075"; font-family: SincloFont; font-size: 17px; margin: -5px 7px 0 0; transform: scale( 1 , 1.1 ); }

    #sincloBox section#navigation ul li.selected::before{ color: {{main_color}}; }
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
    #sincloBox span#sincloAccessInfo{ height: 26.5px; display: block; padding-left: 0.5em; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid {{widget_border_color}}; font-size: 0.9em; }
    #sincloBox span#sincloAccessInfo:not(.notNoneWidgetOutsideBorder) { border-top:none; }
    #sincloBox span#sincloAccessInfo.details{ border-top: 1px solid {{widget_inside_border_color}}; }
    #sincloBox span#sincloAccessInfo.details:not(.notNone){ border-top: none; }
    <?php endif; ?>
    #sincloBox #footer{ height: 26.5px; padding: 5px 0; text-align: center; border: 1px solid {{widget_border_color}}; color:#A1A1A1!important; font-size: 11px;margin: 0; border-top: none; }
    #sincloBox #footer:not(.notNoneWidgetOutsideBorder) { border:none; }
    .disableCopy{ user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none; }
  </style>
    <!-- 画像 -->
    <span id="mainImage" class="widgetOpener" ng-hide="spHeaderLightToggle() || mainImageToggle !== '1'">
      <img ng-src="{{main_image}}" err-src="<?=$gallaryPath?>chat_sample_picture.png" width="62" height="70" alt="チャット画像">
    </span>
  <div class="widgetHeaderWrap">
    <!-- 画像 -->
    <div id="titleWrap">
      <!-- タイトル -->
      <p id="widgetTitle" class="widgetOpener notSelect" ng-class="{center: spHeaderLightToggle() || mainImageToggle !== '1',middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3', spText:showWidgetType === 3, sp:showWidgetType === 3}">{{title}}</p>
      <!-- タイトル -->
      <div id="minimizeBtn" class="widgetOpener" ng-class="" style="display: block;"></div>
      <div id="closeBtn" ng-click="switchWidget(4)" ng-class="{closeButtonSetting: closeButtonSettingToggle === '2'}"></div>
    </div>
    <!--
        <div id="addBtn" class="widgetOpener" ng-class="{closeButtonSetting: closeButtonSettingToggle === '2'}" style="display: none;"></div>
     -->
    <div id='descriptionSet' class="widgetOpener notSelect" ng-hide=" spHeaderLightToggle() || mainImageToggle == '2' && subTitleToggle == '2' && descriptionToggle == '2'">
      <!-- サブタイトル -->
      <!-- 仕様変更、常に高度な設定が当たっている状態とする -->
      <!--
            <p ng-if="subTitleToggle == '1' && color_setting_type === '0' || color_setting_type === false"" id="widgetSubTitle" >{{sub_title}}</p>
            <p ng-if="subTitleToggle == '1' && color_setting_type === '1' || color_setting_type === true" id="widgetSubTitle" class="details">{{sub_title}}</p>
       -->
      <p ng-if="subTitleToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, sp:showWidgetType === 3}" class="details">{{sub_title}}</p>
      <p ng-if="subTitleToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, sp:showWidgetType === 3}">&thinsp;</p>
      <!-- サブタイトル -->

      <!-- 説明文 -->
      <!-- 仕様変更、常に高度な設定が当たっている状態とする -->
      <!--
            <p ng-if="descriptionToggle == '1' && color_setting_type === '0' || color_setting_type === false" id="widgetDescription" >{{description}}</p>
            <p ng-if="descriptionToggle == '1' && color_setting_type === '1' || color_setting_type === true" id="widgetDescription" class="details" ng-class="{ notNone:widget_inside_border_none === ''||widget_inside_border_none === false}">{{description}}</p>
       -->
      <p ng-if="descriptionToggle == '1'" id="widgetDescription" class="details" ng-class="{notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, notNone:widget_inside_border_none === ''||widget_inside_border_none === false, sp:showWidgetType === 3}">{{description}}</p>
      <p ng-if="descriptionToggle == '2'" id="widgetDescription" class="details" ng-class="{notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, notNone:widget_inside_border_none === ''||widget_inside_border_none === false, sp:showWidgetType === 3}">&thinsp;</p>
      <!-- 説明文 -->
    </div>
  </div>
  <div id="miniTarget">
    <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
      <section id="chatTab" ng-hide="widget.showTab !== 'chat'" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, notNone:widget_inside_border_none === ''||widget_inside_border_none === false, middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">

        <!-- chat_message_copy 0 stayt -->
        <ul id="chatTalk" class="details" ng-class="{ middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3', disableCopy: chat_message_copy == '1'}">
          <div style="height: auto!important; padding:0;">
            <li class="sinclo_re chat_left" ng-style="{backgroundColor:makeFaintColor()}" ng-class="{ notNone:re_border_none === '' || re_border_none === false, middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}"><span class="cName details" ng-if="show_automessage_name == 1" ng-class="{ middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3', sp:showWidgetType === 3}">{{sub_title}}</span><span class="details" ng-class="{sp:showWidgetType === 3}">これはオートメッセージです。<br>チャットで質問して下さい。</span></li>
          </div>
          <div style="height: auto!important; padding:0;" ng-class="{liLeft: chat_message_design_type == 1, liRight: chat_message_design_type == 2}" >
            <li class="sinclo_se chat_right details" ng-class="{ notNone:se_border_none === '' || se_border_none === false, middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}" ><span class="details" ng-class="{sp:showWidgetType === 3}">○○について質問したいのですが</span></li>
          </div>
          <li class="sinclo_etc" ng-if="show_name == 1">－ <?=$userInfo['display_name']?>が入室しました －</li>
          <li class="sinclo_etc" ng-if="show_name == 2">－ オペレーターが入室しました －</li>
          <div style="height: auto!important; padding:0;">
            <li class="sinclo_re chat_left" ng-style="{backgroundColor:makeFaintColor()}" ng-class="{ notNone:re_border_none === '' || re_border_none === false, middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}"><span class="cName details" ng-if="show_op_name == 1" ng-class="{ middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',sp:showWidgetType === 3}"><?=$userInfo['display_name']?></span><span class="cName details" ng-if="show_op_name == 2" ng-class="{ middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3', sp:showWidgetType === 3}">{{sub_title}}</span><span class="details" ng-class="{sp:showWidgetType === 3}">こんにちは</span></li>
          </div>
          <div style="height: auto!important; padding:0;">
            <li class="showAnimationSample sinclo_re chat_left" ng-style="{backgroundColor:makeFaintColor()}" ng-class="{ notNone:re_border_none === '' || re_border_none === false, middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}"><span class="cName details" ng-if="show_op_name == 1" ng-class="{ middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',sp:showWidgetType === 3}"><?=$userInfo['display_name']?></span><span class="cName details" ng-if="show_op_name == 2" ng-class="{ middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3',sp:showWidgetType === 3}">{{sub_title}}</span><span class="details" ng-class="{sp:showWidgetType === 3}">○○についてですね<br>どのようなご質問でしょうか？</span></li>
          </div>
        </ul>
        <!-- chat_message_copy 0 end -->

        <!-- chat_message_copy 1 stayt -->
        <div id="messageBox" class="messageBox details" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, notNone:widget_inside_border_none === ''||widget_inside_border_none === false, disableCopy: chat_message_copy == '1' }">
          <textarea name="sincloChat" id="sincloChatMessage" class="details" ng-class="{ notNone:message_box_border_none === ''||message_box_border_none === false}" placeholder="メッセージを入力してください&#13;&#10;{{chat_area_placeholder_pc}}"></textarea>
          <a id="sincloChatSendBtn" class="notSelect details" ng-class="{ middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}"><span class="details">送信</span></a>
        </div>
        <!-- chat_message_copy 1 end -->

          <!-- chat_message_copy 1 stayt -->
          <span id="sincloAccessInfo" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, notNone:widget_inside_border_none === ''||widget_inside_border_none === false, disableCopy: chat_message_copy == '1' }" ng-if="!show_access_id || show_access_id === '1'" ng-hide="showWidgetType === 3">ウェブ接客コード：●●●●</span>
          <!-- chat_message_copy 1 end -->
      </section>
    <?php endif; ?>
    <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
      <!-- 仕様変更、常に高度な設定が当たっている状態とする -->
      <!--
            <section id="callTab" ng-hide="widget.showTab !== 'call'" ng-class="{details: color_setting_type === '1' || color_setting_type === true, middleSize: widgetSizeTypeToggle === '2',largeSize: widgetSizeTypeToggle === '3'}">
       -->
      <section id="callTab" ng-hide="widget.showTab !== 'call'" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, notNone:widget_inside_border_none === ''||widget_inside_border_none === false, middleSize: widgetSizeTypeToggle === '2',largeSize: widgetSizeTypeToggle === '3'}">
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
    <!-- chat_message_copy 0 stayt -->
    <p id="footer" ng-if="chat_message_copy == '1'" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false}" style="user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;">Powered by <a target="sinclo" href="https://sinclo.medialink-ml.co.jp/lp/?utm_medium=web-widget&utm_campaign=widget-referral">sinclo</a></p>
    <!-- chat_message_copy 0 end -->

    <!-- chat_message_copy 1 stayt -->
    <p id="footer" ng-if="chat_message_copy == '0'" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false}" >Powered by <a target="sinclo" href="https://sinclo.medialink-ml.co.jp/lp/?utm_medium=web-widget&utm_campaign=widget-referral">sinclo</a></p>
    <!-- chat_message_copy 1 end -->
  </div>
</div>