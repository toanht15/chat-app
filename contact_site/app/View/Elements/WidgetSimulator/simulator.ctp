<?php echo $this->element('WidgetSimulator/angularjs'); ?>
<?php
$gallaryPath = C_PATH_NODE_FILE_SERVER.'/img/widget/';
?>
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
    #widget_simulator_wrapper section .ulTab li {
      display: block;
    }
    #widget_simulator_wrapper section ul#chatTalk li {
      word-break: break-all;
      white-space: pre-wrap;
    }
    #widget_simulator_wrapper section ul#chatTalk li.boxType {
      display: block;
    }
</style>

<div ng-controller="SimulatorController as simulator" ng-cloak id="widget_simulator_wrapper" style="height: 100%;">

  <div ng-if='isTabDisplay'>
    <!-- シナリオ設定ではタブ表示を行わないため、いったん削除 -->
  </div>

  <section ng-cloak>
    <style>
      #sincloBox {
        position: relative;
        z-index: 1;
        width: 285px;
        background-color: rgb(255, 255, 255);
        box-shadow: 0px 0px {{widget.settings['box_shadow']}}px {{widget.settings['box_shadow']}}px rgba(0,0,0,0.1);
        /* z風 */
        /*box-shadow: 0px -2px 3px 2px rgba(0,0,0,calc(({{widget.settings['box_shadow']}} * 0.1) / 2));*/
        /* d風 */
        /* box-shadow: 0 10px 20px rgba(0,0,0,calc(({{widget.settings['box_shadow']}} * 0.1) / 2)); */
        border-radius: {{widget.settings['radius_ratio']}}px {{widget.settings['radius_ratio']}}px 0 0;
      }
      #sincloBox.middleSize{
        width: 342.5px;
      }
      #sincloBox.largeSize{
        width: 400px;
      }
      /* タブアイコンフォント化対応start */
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
      /* タブアイコンフォント化対応end */
    </style>
    <div id="sincloBox" ng-if="widget.showWidgetType !== 2" ng-hide="widget.showWidgetType === 4" ng-class="{middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}">
      <style>
        #sincloBox * { font-size: 12px; }
        #sincloBox.middleSize  * { font-size: 13px; }
        #sincloBox.largeSize * { font-size: 13px; }
        #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
        #sincloBox span#mainImage { cursor: pointer; z-index: 2; position: absolute; top: 7px; left: 10px; }
        #sincloBox span#mainImage img { background-color: {{widget.settings['main_color']}}; }
        #sincloBox .pb07 { padding-bottom: 7px }
        #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
        #sincloBox .sinclo_re a { color: {{widget.settings['re_text_color']}};}
        #sincloBox .sinclo_se a { color: {{widget.settings['se_text_color']}};}
        #sincloBox ul#chatTalk li.sinclo_re span.telno { color: {{widget.settings['re_text_color']}};}
        #sincloBox a:hover { color: {{widget.settings['main_color']}}; }
        #sincloBox .center { text-align: center!important; padding: 7px 30px!important }
        #sincloBox div#descriptionSet { cursor: pointer; }
        #sincloBox p#widgetTitle { position:relative; z-index: 1; cursor:pointer; border-radius: {{widget.settings['radius_ratio']}}px {{widget.settings['radius_ratio']}}px 0 0; border: 1px solid {{widget.settings['main_color']}}; border-bottom:none; background-color: {{widget.settings['main_color']}}; text-align: center; font-size: 14px; padding: 7px 30px 7px 70px; margin: 0; color: {{widget.settings['string_color']}}; height: 32px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
        #sincloBox p#widgetTitle.spText{ text-indent: 1em; }
        #sincloBox p#widgetTitle.middleSize { font-size: 15px; }
        #sincloBox p#widgetTitle.largeSize { font-size: 15px; }
        #sincloBox div#minimizeBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>minimize.png'); background-position-y: 0px; top: 6px; right: 6px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
  /*
        #sincloBox div#addBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>add.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
        #sincloBox div#addBtn.closeButtonSetting { right: 25px; }
  */
        #sincloBox div#closeBtn { display: none; cursor: pointer; background-image: url('<?=$gallaryPath?>close.png'); background-position-y: -1.5px; top: 7px; right: 6px; bottom: 6px; content: " "; width: 18px; height: 18px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
        #sincloBox div#closeBtn.closeButtonSetting {display: inline-block; right: 5px; }
  /*
        #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
        #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
        #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
  */
        #sincloBox p#widgetSubTitle { background-color: {{widget.settings['header_background_color']}}; margin: 0; padding: 7px 0; text-align: left; border-width: 0 1px 0 1px; border-color: {{widget.settings['widget_border_color']}}; border-style: solid; padding-left: 77px; font-weight: bold; color: {{widget.settings['main_color']}}; height: 29px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #sincloBox p#widgetSubTitle:not(.notNoneWidgetOutsideBorder) { border:none; }
        #sincloBox p#widgetSubTitle.details { color: {{widget.settings['sub_title_text_color']}}; }
        #sincloBox p#widgetDescription { background-color: {{widget.settings['header_background_color']}}; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: {{widget.settings['widget_border_color']}}; border-style: solid; padding-left: 77px; height: 23px; color: {{widget.settings['other_text_color']}}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #sincloBox p#widgetDescription:not(.notNoneWidgetOutsideBorder) { border-left:none; border-right:none; }
        #sincloBox p#widgetDescription.details { color: {{widget.settings['description_text_color']}}; border-bottom-color: {{widget.settings['widget_inside_border_color']}}; }
        #sincloBox p#widgetDescription.details:not(.notNone) { border-bottom:none }
        #sincloBox section { display: inline-block; width: 285px; border: 1px solid {{widget.settings['widget_border_color']}}; border-top: none; }
        #sincloBox section:not(.notNoneWidgetOutsideBorder) { border: 1px solid {{widget.settings['widget_inside_border_color']}};  border-top: none; border-left:none; border-right:none; }
        #sincloBox section.details { border-bottom-color: {{widget.settings['widget_inside_border_color']}}; }
        #sincloBox section.details:not(.notNone) { border-bottom:none }
        #sincloBox section.middleSize { width: 342.5px; }
        #sincloBox section.largeSize { width: 400px; }
        #sincloBox section.noDisplay { display: none }
        #sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
        @keyframes rightEffect { 0% { transform :translate3d(20px, 0px, 0px); opacity :0; } 70% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }
        @keyframes leftEffect { 0% { transform :translate3d(-20px, 0px, 0px) scale(0.8); opacity :0; } 69% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }
        #sincloBox ul#chatTalk { width: 100%; height: 194px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
        #sincloBox ul#chatTalk.middleSize { height: 284px; }
        #sincloBox ul#chatTalk.largeSize { height: 374px; }
        #sincloBox ul#chatTalk.details { background-color: {{widget.settings['chat_talk_background_color']}}; }
        #sincloBox ul#chatTalk div.liLeft { text-align: left; }
        #sincloBox ul#chatTalk div.liRight { text-align: right; }
        #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0 0; padding: 8px; font-size: 12px; line-height: 1.4; white-space: pre; }
        #sincloBox ul#chatTalk li.middleSize { font-size: 13px; }
        #sincloBox ul#chatTalk li.largeSize { font-size: 13px; }
        #sincloBox ul#chatTalk li.boxType { word-wrap: break-word; word-break: break-all; }
        #sincloBox ul#chatTalk li.balloonType { display: inline-block; position: relative; padding: 5px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
        #sincloBox ul#chatTalk li.sinclo_se.details { background-color: {{widget.getSeBackgroundColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_se.notNone { border: 1px solid {{widget.getTalkBorderColor('se')}}; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType { margin-right: 15px; border-bottom-right-radius: 0px; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; z-index: 2; border: 5px solid transparent; border-left: 5px solid {{widget.getSeBackgroundColor()}}; border-bottom: 5px solid {{widget.getSeBackgroundColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType.notNone:after { border-left: 5px solid {{widget.getTalkBorderColor('se')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('se')}}; }
        /* 二等辺三角形バージョン */
        /*#sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 5px; left: calc(100% - 2.5px); margin-top: -10px; border: 10px solid transparent; border-left: 10px solid #FFF; z-index: 2; }*/
        /*#sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: 6px; left: 100%; margin-top: -9px; border: 9px solid transparent; border-left: 9px solid #C9C9C9; z-index: 1; }*/
        #sincloBox ul#chatTalk li.sinclo_re.notNone { border: 1px solid {{widget.getTalkBorderColor('re')}}; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; z-index: 2; border: 5px solid transparent; border-right: 5px solid {{widget.makeFaintColor()}}; border-bottom: 5px solid {{widget.makeFaintColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; z-index: 1; border: 5px solid transparent; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{widget.getTalkBorderColor('re')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('re')}}; }
        /* 二等辺三角形バージョン */
        /*#sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 5px; left: -19px; margin-top: -10px; border: 10px solid transparent; border-right: 10px solid {{makeBalloonTriangleColor()}}; z-index: 2; }*/
        /*#sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: 6px; left: -19px; margin-top: -9px; border: 9px solid transparent; border-right: 9px solid #C9C9C9; z-index: 1; }*/
        #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
        #sincloBox ul#chatTalk li.effect_right { -webkit-animation-name:rightEffect; -moz-animation-name:rightEffect; -o-animation-name:rightEffect; -ms-animation-name:rightEffect; animation-name:rightEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
        #sincloBox ul#chatTalk li.boxType.chat_right { border-bottom-right-radius: 0; margin-left: 10px }
        #sincloBox ul#chatTalk li.boxType.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
        #sincloBox ul#chatTalk li.balloonType.chat_right { margin-left: 15px }
        #sincloBox ul#chatTalk li.balloonType.chat_left { margin-right: 10px }
        #sincloBox ul#chatTalk li span.cName { display: block; color: {{widget.settings['main_color']}}!important; font-weight: bold; font-size: 13px; margin: 0 0 5px 0; }
        #sincloBox ul#chatTalk li span.cName.middleSize { font-size: 14px }
        #sincloBox ul#chatTalk li span.cName.largeSize { font-size: 14px }
        #sincloBox ul#chatTalk li span.cName.details{ color: {{widget.settings['c_name_text_color']}}!important;}
        #sincloBox ul#chatTalk li span.cName:not(.details){ color: {{widget.settings['main_color']}}!important;}
        #sincloBox ul#chatTalk li.sinclo_re span.details{ color: {{widget.settings['re_text_color']}};}
        #sincloBox ul#chatTalk li.sinclo_se span.details{ color: {{widget.settings['se_text_color']}};}
        #sincloBox ul#chatTalk li span.sinclo-radio { display: block; margin-top: 0.1em; margin-bottom: -1.25em; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; padding: 0 0 0 15px; color:{{widget.settings['re_text_color']}}; min-height: 12px; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: 1px; left: 0px; width: 11px; height: 11px; border: 1px solid #999; border-radius: 50%; background-color: #FFF; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: 4px; left: 3px; width: 7px; height: 7px; background: {{widget.settings['main_color']}}; border-radius: 50%; }
        #sincloBox section#chatTab div { height: 75px!important; padding: 5px; }
        #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; color: {{widget.settings['other_text_color']}}; margin: 0; resize: none; padding: 5px; }
        #sincloBox section#chatTab textarea#sincloChatMessage.details { color: {{widget.settings['message_box_text_color']}}; background-color: {{widget.settings['message_box_background_color']}}; }
        #sincloBox section#chatTab textarea#sincloChatMessage.details.notNone { border: 1px solid {{widget.settings['message_box_border_color']}}!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage.notNone { border-radius: 5px 0 0 5px!important; border: 1px solid {{widget.settings['chat_talk_border_color']}}!important; border-right-color: transparent!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage.notNone:focus { border-color: {{widget.settings['main_color']}}!important; outline: none!important; border-right-color: transparent!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage:not(.notNone){ border: none!important }
        #sincloBox section#chatTab #sincloChatSendBtn{ width: 20%; height: 100%; padding: 20px 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{widget.settings['main_color']}}!important; color: {{widget.settings['string_color']}}; font-weight: bold; font-size: 1.2em;}
        #sincloBox section#chatTab #sincloChatSendBtn.details{ background-color: {{widget.settings['chat_send_btn_background_color']}}!important; }
        #sincloBox section#chatTab #sincloChatSendBtn.middleSize{ padding: 20px 0; margin: 0 auto; font-size: 1.2em;}
        #sincloBox section#chatTab #sincloChatSendBtn.largeSize{ padding: 20px 0; margin: 0 auto; font-size: 1.2em;}
        #sincloBox section#chatTab #sincloChatSendBtn span { color: {{widget.settings['string_color']}} }
        #sincloBox section#chatTab #sincloChatSendBtn span.details { color: {{widget.settings['chat_send_btn_text_color']}} }
        #sincloBox section#chatTab #messageBox.messageBox{border-top: 1px solid {{widget.settings['widget_border_color']}}; padding: 0.5em;}
        #sincloBox section#chatTab #messageBox.messageBox:not(.notNoneWidgetOutsideBorder) { border-top:none; }
        #sincloBox section#chatTab #messageBox.messageBox.details{ background-color: {{widget.settings['chat_message_background_color']}}; border-top: 1px solid {{widget.settings['widget_inside_border_color']}}; }
        #sincloBox section#chatTab #messageBox.messageBox.details:not(.notNone){ border-top: none; }
      <?php endif; ?>
      <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
        #sincloBox section#callTab{height: 296.5px;}
        #sincloBox section#callTab.middleSize {height: 387px;}
        #sincloBox section#callTab.largeSize {height: 476.5px;}
        #sincloBox section#callTab.details { background-color: {{widget.settings['chat_talk_background_color']}}; }
        #sincloBox section#callTab #telNumber { overflow: hidden; color: {{widget.settings['main_color']}}; font-weight: bold; margin: 0 auto; text-align: center }
        #sincloBox section#callTab #telNumber:not(.notUseTime) { font-size: 18px; padding: 5px 0px 0px; height: 30px }
        #sincloBox section#callTab #telNumber.middleSize:not(.notUseTime) { font-size: 19px; padding: 5px 0px 0px; height: 30px }
        #sincloBox section#callTab #telNumber.largeSize:not(.notUseTime) { font-size: 19px; padding: 5px 0px 0px; height: 30px }
        #sincloBox section#callTab #telNumber.notUseTime { font-size: 20px; padding: 10px 0px 0px; height: 45px }
        #sincloBox section#callTab #telNumber.notUseTime.middleSize { font-size: 21px; padding: 10px 0px 0px; height: 45px }
        #sincloBox section#callTab #telNumber.notUseTime.largeSize { font-size: 21px; padding: 10px 0px 0px; height: 45px }
        #sincloBox section#callTab #telIcon { background-color: {{widget.settings['main_color']}}; display: block; width: 50px; height: 50px; float: left; border-radius: 25px; padding: 3px }
        #sincloBox section#callTab #telTime { font-weight: bold; color: {{widget.settings['main_color']}}; margin: 0 auto; white-space: pre-line; font-size: 11px; text-align: center; padding: 0 0 5px; height: 20px }
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
        #sincloBox section#navigation ul li { position: relative; overflow: hidden; cursor: pointer; color: {{widget.settings['other_text_color']}}; width: 50%; text-align: center; display: table-cell; padding: 10px 0; height: 40px }
        #sincloBox section#navigation ul li:not(.details) { border-left: 1px solid {{widget.settings['widget_border_color']}}; }
        #sincloBox section#navigation ul li:not(.details):not(.notNoneWidgetOutsideBorder) { border-left:none; }
        #sincloBox section#navigation ul li:last-child { border-right: 1px solid {{widget.settings['widget_border_color']}}; }
        #sincloBox section#navigation ul li:last-child:not(.notNoneWidgetOutsideBorder) { border-right:none; }
        #sincloBox section#navigation ul li.selected { background-color: #FFFFFF; z-index: -1; }
        #sincloBox section#navigation ul li:not(.selected) { border-bottom: 1px solid {{widget.settings['widget_border_color']}}; }
        #sincloBox section#navigation ul li:not(.selected):not(.notNoneWidgetOutsideBorder) { border-bottom:none; }

        #sincloBox section#navigation ul li.selected.details { background-color: {{widget.settings['chat_talk_background_color']}}; }
        #sincloBox section#navigation ul li:not(.selected).details {  }

        #sincloBox section#navigation ul li.details:not(.selected) { border-bottom: 1px solid {{widget.settings['widget_inside_border_color']}} }
        #sincloBox section#navigation ul li.details:not(.selected):not(.notNone) { border-bottom: none }
        #sincloBox section#navigation ul li[data-tab='call'].details:not(.selected){ border-left: 1px solid {{widget.settings['widget_inside_border_color']}}; }
        #sincloBox section#navigation ul li[data-tab='call'].details:not(.selected):not(.notNone){ border-left: none }
        #sincloBox section#navigation ul li[data-tab='chat'].details:not(.selected){ border-right: 1px solid {{widget.settings['widget_inside_border_color']}}; }
        #sincloBox section#navigation ul li[data-tab='chat'].details:not(.selected):not(.notNone){ border-right: none }
        #sincloBox section#navigation ul li.selected::after{ content: " "; border-bottom: 2px solid {{widget.settings['main_color']}}; position: absolute; bottom: 0px; left: 5px; right: 5px;}
        #sincloBox section#navigation ul li::before{ margin-right: 5px; color: #BCBCBC; content: " "; display: inline-block; width: 18px; height: 18px; position: relative; background-size: contain; vertical-align: middle; background-repeat: no-repeat }
  /*
        #sincloBox section#navigation ul li[data-tab='call']::before{ background-image: url('/img/widget/icon_tel.png'); }
        #sincloBox section#navigation ul li[data-tab='chat']::before{ background-image: url('/img/widget/icon_chat.png'); }
  */
        #sincloBox section#navigation ul li[data-tab='call']::before{ content: "\f095"; font-family: SincloFont; font-size: 17px; margin: -5px 7px 0 0; font-weight: bold;}
        #sincloBox section#navigation ul li[data-tab='chat']::before{ content: "\f075"; font-family: SincloFont; font-size: 17px; margin: -5px 7px 0 0; transform: scale( 1 , 1.1 ); }

        #sincloBox section#navigation ul li.selected::before{ color: {{widget.settings['main_color']}}; }
      <?php endif; ?>
      <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
        #sincloBox span#sincloAccessInfo{ height: 26.5px; display: block; padding-left: 0.5em; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid {{widget.settings['widget_border_color']}}; font-size: 0.9em; }
        #sincloBox span#sincloAccessInfo:not(.notNoneWidgetOutsideBorder) { border-top:none; }
        #sincloBox span#sincloAccessInfo.details{ border-top: 1px solid {{widget.settings['widget_inside_border_color']}}; }
        #sincloBox span#sincloAccessInfo.details:not(.notNone){ border-top: none; }
      <?php endif; ?>
        #sincloBox #footer{ height: 26.5px; padding: 5px 0; text-align: center; border: 1px solid {{widget.settings['widget_border_color']}}; color:#A1A1A1!important; font-size: 11px;margin: 0; border-top: none; }
        #sincloBox #footer:not(.notNoneWidgetOutsideBorder) { border:none; }
      </style>
      <!-- 画像 -->
      <span id="mainImage" class="widgetOpener" ng-hide="widget.spHeaderLightToggle() || widget.mainImageToggle !== '1'">
        <img ng-src="{{widget.settings['main_image']}}" err-src="<?=$gallaryPath?>chat_sample_picture.png" width="62" height="70" alt="チャット画像">
      </span>
      <!-- 画像 -->
      <div>
        <!-- タイトル -->
        <p id="widgetTitle" class="widgetOpener notSelect" ng-class="{center: widget.spHeaderLightToggle() || widget.mainImageToggle !== '1',middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3', spText:widget.showWidgetType === 3}">{{widget.settings['title']}}</p>
        <!-- タイトル -->
      </div>
      <div id="minimizeBtn" class="widgetOpener" ng-class="" style="display: block;"></div>
  <!--
      <div id="addBtn" class="widgetOpener" ng-class="{closeButtonSetting: widget.settings['close_button_mode_type'] === '2'}" style="display: none;"></div>
   -->
      <div id="closeBtn" ng-class="{closeButtonSetting: widget.settings['close_button_mode_type'] === '2'}"></div>
      <div id='descriptionSet' class="widgetOpener notSelect" ng-hide="widget.spHeaderLightToggle() || widget.mainImageToggle == '2' && widget.subTitleToggle == '2' && widget.descriptionToggle == '2'">
        <!-- サブタイトル -->
  <!-- 仕様変更、常に高度な設定が当たっている状態とする -->
  <!--
        <p ng-if="subTitleToggle == '1' && color_setting_type === '0' || color_setting_type === false"" id="widgetSubTitle" >{{widget.settings['sub_title']}}</p>
        <p ng-if="subTitleToggle == '1' && color_setting_type === '1' || color_setting_type === true" id="widgetSubTitle" class="details">{{widget.settings['sub_title']}}</p>
   -->
        <p ng-if="widget.subTitleToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details">{{widget.settings['sub_title']}}</p>
        <p ng-if="widget.subTitleToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}"></p>
        <!-- サブタイトル -->

        <!-- 説明文 -->
  <!-- 仕様変更、常に高度な設定が当たっている状態とする -->
  <!--
        <p ng-if="descriptionToggle == '1' && color_setting_type === '0' || color_setting_type === false" id="widgetDescription" >{{widget.settings['description']}}</p>
        <p ng-if="descriptionToggle == '1' && color_setting_type === '1' || color_setting_type === true" id="widgetDescription" class="details" ng-class="{ notNone:widget_inside_border_none === ''||widget_inside_border_none === false}">{{widget.settings['description']}}</p>
   -->
        <p ng-if="widget.descriptionToggle == '1'" id="widgetDescription" class="details" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.settings['description']}}</p>
        <p ng-if="widget.descriptionToggle == '2'" id="widgetDescription" class="details" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false }"></p>
        <!-- 説明文 -->
      </div>
      <div id="miniTarget">
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
        <section id="chatTab" ng-hide="widget.showTab !== 'chat'" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false, middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}">

  <!-- chat_message_copy 0 stayt -->
          <ul id="chatTalk" class="details" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}" ng-if="widget.settings['chat_message_copy'] == '0'">
            <div style="height: auto!important; padding:0; display: none;" ng-class="{liLeft: widget.settings['chat_message_design_type'] == 1, liRight: widget.settings['chat_message_design_type'] == 2}" >
            <li class="sinclo_se chat_right details" ng-class="{ notNone:wiget.se_border_none === '' || widget.se_border_none === false, middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3',boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_right: widget.settings['chat_message_with_animation']}" ><span class="details">サイト訪問者側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li id="sample_widget_re_message" class="sinclo_re chat_left" ng-style="{backgroundColor:widget.makeFaintColor()}" ng-class="{ notNone:widget.re_border_none === '' || widget.re_border_none === false, middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3',boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_left: widget.settings['chat_message_with_animation']}"><span class="cName details" ng-if="widget.settings['show_name'] == 1" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}"><?=$userInfo['display_name']?></span><span class="cName details" ng-if="widget.settings['show_name'] == 2" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}">{{widget.settings['sub_title']}}</span><span class="details">企業側メッセージ</span></li>
            </div>
            <!-- <div style="height: auto!important; padding:0;">
              <li class="showAnimationSample sinclo_re chat_left" ng-style="{backgroundColor:widget.makeFaintColor()}" ng-class="{ notNone:widget.re_border_none === '' || widget.re_border_none === false, middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3',boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}"><span class="cName details" ng-if="widget.settings['show_name'] == 1" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}"><?=$userInfo['display_name']?></span><span class="cName details" ng-if="widget.settings['show_name'] == 2" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}">{{widget.settings['sub_title']}}</span><span class="details">○○についてですね<br>どのようなご質問でしょうか？</span></li>
            </div> -->
          </ul>
  <!-- chat_message_copy 0 end -->

  <!-- chat_message_copy 1 stayt -->
          <ul id="chatTalk" class="details" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}" ng-if="widget.settings['chat_message_copy']== '1'" style = "user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;">
            <div style="height: auto!important; padding:0; display: none;" ng-class="{liLeft: widget.settings['chat_message_design_type'] == 1, liRight: widget.settings['chat_message_design_type'] == 2}" >
              <li class="sinclo_se chat_right details" ng-class="{ notNone:se_border_none === '' || se_border_none === false, middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3',boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_right: widget.settings['chat_message_with_animation']}" ><span class="details">サイト訪問者側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li id="sample_widget_re_message" class="sinclo_re chat_left" ng-style="{backgroundColor:widget.makeFaintColor()}" ng-class="{ notNone:widget.re_border_none === '' || widget.re_border_none === false, middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3',boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_left: widget.settings['chat_message_with_animation']}"><span class="cName details" ng-if="widget.settings['how_name'] == 1" ng-class="{middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}"><?=$userInfo['display_name']?></span><span class="cName details" ng-if="widget.settings['show_name'] == 2" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}">{{widget.settings['sub_title']}}</span><span class="details">企業側メッセージ</span></li>
            </div>
            <!-- <div style="height: auto!important; padding:0;">
              <li class="showAnimationSample sinclo_re chat_left" ng-style="{backgroundColor:widget.makeFaintColor()}" ng-class="{ notNone:re_border_none === '' || re_border_none === false, middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3',boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}"><span class="cName details" ng-if="widget.settings['show_name'] == 1" ng-class="{middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}"><?=$userInfo['display_name']?></span><span class="cName details" ng-if="widget.settings['show_name'] == 2" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}">{{widget.settings['sub_title']}}</span><span class="details">○○についてですね<br>どのようなご質問でしょうか？</span></li>
            </div> -->
          </ul>
  <!-- chat_message_copy 1 end -->

  <!-- chat_message_copy 0 stayt -->
          <div id="messageBox" class="messageBox details" ng-if="widget.settings['chat_message_copy'] == '1'" ng-class="{ notNoneWidgetOutsideBorder:widget.settings['widget_outside_border_none'] === ''||widget.settings['widget_outside_border_none'] === false, notNone:widget.settings['widget_inside_border_none'] === ''||widget.settings['widget_inside_border_none'] === false }" style="user-select: none; -moz-user-select: none; -webkit-user-select: none; -ms-user-select: none;">
            <textarea name="sincloChat" id="sincloChatMessage" class="details" ng-class="{ notNone:widget.message_box_border_none === ''||widget.message_box_border_none === false}" placeholder="メッセージを入力してください&#13;&#10;{{widget.chat_area_placeholder_pc}}"></textarea>
            <a id="sincloChatSendBtn" class="notSelect details" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}" ng-click="canVisitorSendMessage && visitorSendMessage()"><span class="details">送信</span></a>
          </div>
  <!-- chat_message_copy 0 end -->

  <!-- chat_message_copy 1 stayt -->
          <div id="messageBox" class="messageBox details" ng-if="widget.settings['chat_message_copy'] == '0'" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false }">
            <textarea name="sincloChat" id="sincloChatMessage" class="details" ng-class="{ notNone:widget.message_box_border_none === ''||widget.message_box_border_none === false}" placeholder="メッセージを入力してください&#13;&#10;{{widget.chat_area_placeholder_pc}}"></textarea>
            <a id="sincloChatSendBtn" class="notSelect details" ng-class="{ middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}" ng-click="canVisitorSendMessage && visitorSendMessage()"><span class="details">送信</span></a>
          </div>
  <!-- chat_message_copy 1 end -->

        <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>

  <!-- chat_message_copy 0 stayt -->
          <span id="sincloAccessInfo" ng-if="widget.settings['chat_message_copy'] == '1'" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false }" style="user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;" ng-hide="widget.showWidgetType === 3">ウェブ接客コード：●●●●</span>
  <!-- chat_message_copy 0 end -->

  <!-- chat_message_copy 1 stayt -->
          <span id="sincloAccessInfo" ng-if="widget.settings['chat_message_copy'] == '0'" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false }" ng-hide="widget.showWidgetType === 3">ウェブ接客コード：●●●●</span>
  <!-- chat_message_copy 1 end -->

          <?php endif; ?>
        </section>
      <?php endif; ?>
      <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) :?>
  <!-- 仕様変更、常に高度な設定が当たっている状態とする -->
  <!--
        <section id="callTab" ng-hide="widget.showTab !== 'call'" ng-class="{details: color_setting_type === '1' || color_setting_type === true, middleSize: widget.widgetSizeTypeToggle === '2',largeSize: widget.widgetSizeTypeToggle === '3'}">
   -->
        <section id="callTab" ng-hide="widget.showTab !== 'call'" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false, middleSize: widget.widgetSizeTypeToggle === '2',largeSize: widget.widgetSizeTypeToggle === '3'}">
          <div style="height: 50px;margin: 15px 25px;">
          <!-- アイコン -->
          <span id="telIcon"><img width="19.5" height="33" src="<?=C_PATH_NODE_FILE_SERVER?>/img/call.png" style="margin: 6px 12px"></span>
          <!-- アイコン -->

          <!-- 受付電話番号 -->
          <pre id="telNumber" ng-class="{notUseTime: widget.timeTextToggle !== '1',middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}" >{{widget.settings['tel']}}</pre>
          <!-- 受付電話番号 -->

          <!-- 受付時間 -->
          <pre id="telTime" ng-if="widget.timeTextToggle == '1'" ng-class="{middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}">受付時間： {{widget.settings['time_text']}}</pre>
          <!-- 受付時間 -->

          </div>

          <!-- テキスト -->
          <div id="telContent" ng-class="{middleSize: widget.widgetSizeTypeToggle === '2',largeSize: widget.widgetSizeTypeToggle === '3'}"><div class="tblBlock"><span ng-class="{middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '3'}">{{widget.settings['content']}}</span></div></div>
          <!-- テキスト -->

          <span id="accessIdArea" ng-class="{middleSize: widget.showWidgetType === 1 && widget.widgetSizeTypeToggle === '2',largeSize: widget.showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
          ●●●●
          </span>
        </section>
      <?php endif; ?>
  <!-- chat_message_copy 0 stayt -->
        <p id="footer" ng-if="widget.settings['chat_message_copy'] == '1'" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" style="user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;">Powered by <a target="sinclo" href="https://sinclo.medialink-ml.co.jp/lp/?utm_medium=web-widget&utm_campaign=widget-referral">sinclo</a></p>
  <!-- chat_message_copy 0 end -->

  <!-- chat_message_copy 1 stayt -->
        <p id="footer" ng-if="widget.settings['chat_message_copy'] == '0'" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" >Powered by <a target="sinclo" href="https://sinclo.medialink-ml.co.jp/lp/?utm_medium=web-widget&utm_campaign=widget-referral">sinclo</a></p>
  <!-- chat_message_copy 1 end -->
      </div>
    </div>

  <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
  <!-- スマホ版 -->
    <div id="sincloBox" ng-if="widget.showWidgetType === 2">
      <style>
        #sincloBox * { font-size: 12px; }
        #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
        #sincloBox .pb07 { padding-bottom: 7px }
        #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
        #sincloBox .sinclo_re a { color: {{widget.settings['re_text_color']}};}
        #sincloBox .sinclo_se a { color: {{widget.settings['se_text_color']}};}
        #sincloBox ul#chatTalk li.sinclo_re span.telno { color: {{widget.settings['re_text_color']}};}
        #sincloBox a:hover { color: {{widget.settings['main_color']}}; }
        #sincloBox p#widgetTitle { text-align: center!important; padding: 7px 30px!important; position:relative; z-index: 1; cursor:pointer; border-radius: 0; border: 1px solid {{widget.settings['main_color']}}; border-bottom:none; background-color: {{widget.settings['main_color']}};text-align: center; font-size: 14px; margin: 0;color: {{widget.settings['string_color']}}; height: 32px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
        #sincloBox.open #minimizeBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>minimize.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
  /*
        #sincloBox:not(.open) #addBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>add.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
  */
  /*
        #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
        #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
        #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
  */
        #sincloBox section { display: inline-block; width: 285px; border: 1px solid {{widget.settings['widget_border_color']}}; border-top: none; }
        #sincloBox section:not(.notNoneWidgetOutsideBorder) { border:none }
        #sincloBox section.noDisplay { display: none }
        #sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
        @keyframes leftEffect { 0% { transform :translate3d(-20px, 0px, 0px) scale(0.8); opacity :0; } 69% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }
        #sincloBox ul#chatTalk { width: 100%; height: 100px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
        #sincloBox ul#chatTalk.details { background-color: {{widget.settings['chat_talk_background_color']}}; }
        #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0 0; padding: 3px; font-size: 11px; line-height: 1.4; white-space: pre; color: {{widget.settings['message_text_color']}}; }
        #sincloBox ul#chatTalk div.liLeft { text-align: left; }
        #sincloBox ul#chatTalk div.liRight { text-align: right; }
        #sincloBox ul#chatTalk li.boxType { word-wrap: break-word; word-break: break-all; }
        #sincloBox ul#chatTalk li.balloonType { display: inline-block; position: relative; padding: 5px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
        #sincloBox ul#chatTalk li.sinclo_se.details { background-color: {{widget.getSeBackgroundColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_se.notNone { border: 1px solid {{widget.getTalkBorderColor('se')}}; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType { margin-right: 15px; border-bottom-right-radius: 0px; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; border: 5px solid transparent; border-left: 5px solid {{widget.getSeBackgroundColor()}}; border-bottom: 5px solid {{widget.getSeBackgroundColor()}}; z-index: 2; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType.notNone:after { border-left: 5px solid {{widget.getTalkBorderColor('se')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('se')}}; }
        #sincloBox ul#chatTalk li.sinclo_re.notNone { border: 1px solid {{widget.getTalkBorderColor('re')}}; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; border: 5px solid transparent; border-right: 5px solid {{widget.makeFaintColor()}}; border-bottom: 5px solid {{widget.makeFaintColor()}}; z-index: 2; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{widget.getTalkBorderColor('re')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('re')}}; }
        #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
        #sincloBox ul#chatTalk li.boxType.chat_right { border-bottom-right-radius: 0; margin-left: 10px }
        #sincloBox ul#chatTalk li.boxType.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
        #sincloBox ul#chatTalk li.balloonType.chat_right { margin-left: 15px }
        #sincloBox ul#chatTalk li.balloonType.chat_left { margin-right: 10px }
        #sincloBox ul#chatTalk li span.cName { display: block; color: {{widget.settings['main_color']}}!important; font-weight: bold; font-size: 12px; margin: 0 0 5px 0; }
        #sincloBox ul#chatTalk li span.cName.details{ color: {{widget.settings['c_name_text_color']}}!important;}
        #sincloBox ul#chatTalk li span.cName:not(.details){ color: {{widget.settings['main_color']}}!important;}
        #sincloBox ul#chatTalk li span:not(.details){  color: {{widget.settings['message_text_color']}}!important; }
        #sincloBox ul#chatTalk li.sinclo_re span.details{ color: {{widget.settings['re_text_color']}};}
        #sincloBox ul#chatTalk li.sinclo_se span.details{ color: {{widget.settings['se_text_color']}};}
        #sincloBox ul#chatTalk li span.sinclo-radio { display: block; margin-top: 0.1em; margin-bottom: -1.25em; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; padding: 0 0 0 15px; color:{{widget.settings['re_text_color']}}; min-height: 12px; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: 1px; left: 0px; width: 11px; height: 11px; border: 1px solid #999; border-radius: 50%; background-color: #FFF; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: 4px; left: 3px; width: 7px; height: 7px; background: {{widget.settings['main_color']}}; border-radius: 50%; }
        #sincloBox section#chatTab div { height: 65px!important;  padding: 10px; }
        #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; color: {{widget.settings['other_text_color']}}; margin: 0; resize: none; padding: 5px; }
        #sincloBox section#chatTab textarea#sincloChatMessage.details { color: {{widget.settings['message_box_text_color']}}; background-color: {{widget.settings['message_box_background_color']}}; }
        #sincloBox section#chatTab textarea#sincloChatMessage.details.notNone { border: 1px solid {{widget.settings['message_box_border_color']}}!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage.notNone { border-radius: 5px 0 0 5px!important; border: 1px solid {{widget.settings['chat_talk_border_color']}}!important; border-right-color: transparent!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage.notNone:focus { border-color: {{widget.settings['main_color']}}!important; outline: none!important; border-right-color: transparent!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage:not(.notNone){ border: none!important }
        #sincloBox section#chatTab #sincloChatSendBtn{ width: 20%; height: 100%; padding: 1em 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{widget.settings['main_color']}}!important; color: {{widget.settings['string_color']}}; font-weight: bold; font-size: 1.2em;}
        #sincloBox section#chatTab #sincloChatSendBtn.details{ background-color: {{widget.settings['chat_send_btn_background_color']}}!important; }
        #sincloBox section#chatTab #sincloChatSendBtn span { color: {{widget.settings['string_color']}} }
        #sincloBox section#chatTab #sincloChatSendBtn span.details { color: {{widget.settings['chat_send_btn_text_color']}} }
        #sincloBox section#chatTab #messageBox.messageBox{border-top: 1px solid {{widget.settings['widget_border_color']}}; padding: 0.5em;}
        #sincloBox section#chatTab #messageBox.messageBox:not(.notNoneWidgetOutsideBorder) { border:none }
        #sincloBox section#chatTab #messageBox.messageBox.details{ background-color: {{widget.settings['chat_message_background_color']}}; border-top: 1px solid {{widget.settings['widget_inside_border_color']}}; }
        #sincloBox section#chatTab #messageBox.messageBox.details:not(.notNone){ border-top: none; }
      </style>
  <!-- chat_message_copy 0 stayt -->
      <div ng-if="widget.settings['chat_message_copy']== '0'">
        <!-- タイトル -->
        <p id="widgetTitle" class="widgetOpener" ng-class="{center: widget.mainImageToggle == '2'}" >{{widget.settings['title']}}</p>
        <!-- タイトル -->
      </div>
  <!-- chat_message_copy 0 end -->

  <!-- chat_message_copy 1 stayt -->
      <div ng-if="widget.settings['chat_message_copy'] == '1'">
        <!-- タイトル -->
        <p id="widgetTitle" class="widgetOpener" ng-class="{center: widget.mainImageToggle == '2'}" style = "user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;">{{widget.settings['title']}}</p>
        <!-- タイトル -->
      </div>
  <!-- chat_message_copy 1 end -->

      <div id="minimizeBtn" class="widgetOpener"></div>
  <!--
      <div id="addBtn" class="widgetOpener"></div>
   -->
      <div id="miniTarget">
        <section id="chatTab" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}">

  <!-- chat_message_copy 0 stayt -->
          <ul id="chatTalk" class="details" ng-if="widget.settings['chat_message_copy'] == '0'">
            <div style="height: auto!important; padding:0; display: none;" ng-class="{liLeft: widget.settings['chat_message_design_type'] == 1, liRight: widget.settings['chat_message_design_type'] == 2}">
              <li class="sinclo_se chat_right details" ng-class="{notNone:se_border_none === '' || se_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_right: widget.settings['chat_message_with_animation']}"><span class="details" >サイト訪問者側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li id="sample_widget_re_message" class="sinclo_re chat_left" ng-class="{notNone:widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_left: widget.settings['chat_message_with_animation']}" ng-style="{backgroundColor:widget.makeFaintColor()}"><span class="cName details" >{{widget.settings['sub_title']}}</span><span class="details">企業側メッセージ</span></li>
            </div>
            <!-- <div style="height: auto!important; padding:0;">
              <li class="showAnimationSample sinclo_re chat_left" ng-class="{notNone:re_border_none === '' || re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}" ng-style="{backgroundColor:widget.makeFaintColor()}"><span class="cName details" >{{widget.settings['sub_title']}}</span><span class="details">○○についてですね<br>どのようなご質問でしょうか？</span></li>
            </div> -->
          </ul>
  <!-- chat_message_copy 0 end -->

  <!-- chat_message_copy 1 stayt -->
          <ul id="chatTalk" class="details" ng-if="widget.settings['chat_message_copy'] == '1'" style = "user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;" >
            <div style="height: auto!important; padding:0; display: none;" ng-class="{liLeft: widget.settings['chat_message_design_type'] == 1, liRight: widget.settings['chat_message_design_type'] == 2}">
              <li class="sinclo_se chat_right details" ng-class="{notNone:se_border_none === '' || se_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_right: widget.settings['chat_message_with_animation']}"><span class="details">サイト訪問者側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li id="sample_widget_re_message" class="sinclo_re chat_left effect_left" ng-class="{notNone:widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}" ng-style="{backgroundColor:widget.makeFaintColor()}"><span class="cName details" >{{widget.settings['sub_title']}}</span><span class="details">企業側メッセージ</span></li>
            </div>
            <!-- <div style="height: auto!important; padding:0;">
              <li class="showAnimationSample sinclo_re chat_left" ng-class="{notNone:re_border_none === '' || re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}" ng-style="{backgroundColor:widget.makeFaintColor()}"><span class="cName details" >{{widget.settings['sub_title']}}</span><span class="details">○○についてですね<br>どのようなご質問でしょうか？</span></li>
            </div> -->
          </ul>
  <!-- chat_message_copy 1 end -->

          <div id="messageBox" class="messageBox details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false }">
            <textarea name="sincloChat" id="sincloChatMessage" class="details" ng-class="{ notNone:widget.message_box_border_none === ''||widget.message_box_border_none === false}" placeholder="メッセージを入力してください{{widget.chat_area_placeholder_sp}}"></textarea>
            <a id="sincloChatSendBtn" class="notSelect details" ng-click="canVisitorSendMessage && visitorSendMessage()"><span class="details">送信</span></a>
          </div>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <!-- スマホ版 -->

  </section>

  <?= $this->Form->input('isTabDisplay', ['type' => 'hidden', 'value' => $isTabDisplay]); ?>
  <?= $this->Form->input('canVisitorSendMessage', ['type' => 'hidden', 'value' => $canVisitorSendMessage]); ?>

</div>
