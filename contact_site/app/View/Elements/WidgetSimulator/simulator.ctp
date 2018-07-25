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
      display: inline-block;
    }
</style>

<div ng-controller="SimulatorController as simulator" ng-cloak id="widget_simulator_wrapper">

  <!-- 通常/スマートフォン(縦)/スマートフォン(横)のタブ表示 -->
  <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ){?>
  <section id="switch_widget" ng-cloak ng-class="{showBanner:widget.closeButtonModeTypeToggle === '1' && widget.closeButtonSettingToggle === '2' && widget.showWidgetType === 4}" ng-if="isTabDisplay">
    <ul class="ulTab" data-col=3 ng-hide="widget.closeButtonSettingToggle === '2'">
      <li ng-class="{choose: widget.showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
      <li ng-class="{choose: widget.showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
      <li ng-class="{choose: widget.showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
    </ul>
    <ul class="ulTab showType4" data-col=3 ng-hide="widget.closeButtonSettingToggle !== '2'" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">
      <li ng-class="{choose: widget.showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
      <li ng-class="{choose: widget.showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
      <li ng-class="{choose: widget.showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
    </ul>
    <input type="hidden" id="switch_widget" value="">
  </section>
  <?php } else { ?>
  <section id="switch_widget" ng-cloak ng-hide="widget.closeButtonSettingToggle !== '2'" ng-class="{showBanner:closeButtonModeTypeToggle === '1' && widget.closeButtonSettingToggle === '2' && widget.showWidgetType === 4}" ng-if="isTabDisplay">
    <ul class="ulTab showType4" data-col=3  ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">
      <li ng-class="{choose: widget.showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    </ul>
    <input type="hidden" id="switch_widget" value="">
  </section>
  <?php }?>

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
        font-family: 'Font Awesome 5 Pro';
        font-style: normal;
        font-weight: 300;
        src: url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-light-300.eot");
        src: url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-light-300.eot?#iefix") format("embedded-opentype"), url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-light-300.woff2") format("woff2"), url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-light-300.woff") format("woff"), url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-light-300.ttf") format("truetype"), url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-light-300.svg#fontawesome") format("svg"); }

      @font-face {
        font-family: SincloFont;
        font-style: normal;
        font-weight: 900;
        src: url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-solid-900.eot");
        src: url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-solid-900.eot?#iefix") format("embedded-opentype"), url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-solid-900.woff2") format("woff2"), url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-solid-900.woff") format("woff"), url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-solid-900.ttf") format("truetype"), url("<?= C_NODE_SERVER_ADDR ?>/webfonts/fa-solid-900.svg#fontawesome") format("svg"); }

      #sincloBox .sinclo-fal {
        font-family: 'Font Awesome 5 Pro';
        display: inline-block;
        font-style: normal;
        font-weight: 300;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }

      #sincloBox .sinclo-fal.fa-4x {
        font-size: 4em;
      }

      /* タブアイコンフォント化対応end */
    </style>
    <div id="sincloBox" ng-if="widget.showWidgetType !== 2" ng-hide="widget.showWidgetType === 4" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">
      <style>
        /* http://meyerweb.com/eric/tools/css/reset/
       v2.0 | 20110126
       License: none (public domain)
     */
        #sincloBox div,
        #sincloBox span,
        #sincloBox applet,
        #sincloBox object,
        #sincloBox iframe,
        #sincloBox h1,
        #sincloBox h2,
        #sincloBox h3,
        #sincloBox h4,
        #sincloBox h5,
        #sincloBox h6,
        #sincloBox p,
        #sincloBox blockquote,
        #sincloBox pre,
        #sincloBox a,
        #sincloBox abbr,
        #sincloBox acronym,
        #sincloBox address,
        #sincloBox big,
        #sincloBox cite,
        #sincloBox code,
        #sincloBox del,
        #sincloBox dfn,
        #sincloBox em,
        #sincloBox img,
        #sincloBox ins,
        #sincloBox kbd,
        #sincloBox q,
        #sincloBox s,
        #sincloBox samp,
        #sincloBox small,
        #sincloBox strike,
        #sincloBox strong,
        #sincloBox sub,
        #sincloBox sup,
        #sincloBox tt,
        #sincloBox var,
        #sincloBox b,
        #sincloBox u,
        #sincloBox i,
        #sincloBox center,
        #sincloBox dl,
        #sincloBox dt,
        #sincloBox dd,
        #sincloBox ol,
        #sincloBox ul,
        #sincloBox li,
        #sincloBox fieldset,
        #sincloBox form,
        #sincloBox label,
        #sincloBox legend,
        #sincloBox table,
        #sincloBox caption,
        #sincloBox tbody,
        #sincloBox tfoot,
        #sincloBox thead,
        #sincloBox tr,
        #sincloBox th,
        #sincloBox td,
        #sincloBox article,
        #sincloBox aside,
        #sincloBox canvas,
        #sincloBox details,
        #sincloBox embed,
        #sincloBox figure,
        #sincloBox figcaption,
        #sincloBox footer,
        #sincloBox header,
        #sincloBox hgroup,
        #sincloBox menu,
        #sincloBox nav,
        #sincloBox output,
        #sincloBox ruby,
        #sincloBox section,
        #sincloBox summary,
        #sincloBox time,
        #sincloBox mark,
        #sincloBox audio,
        #sincloBox video
        {
          font-family: 'Helvetica Neue', Helvetica, Arial, Verdana, 'ヒラギノ角ゴ ProN W3', 'Hiragino Kaku Gothic ProN', 'メイリオ', Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;
          font-weight: normal;
          font-variant: normal;
          position: static;
          top: auto;
          right: auto;
          bottom: auto;
          left: auto;
          float: none;
          box-sizing: border-box;
          width: auto;
          min-width: 0;
          max-width: none;
          height: auto;
          min-height: 0;
          max-height: none;
          margin: 0;
          padding: 0;
          text-align: start;
          vertical-align: baseline;
          text-decoration: none;
          text-indent: 0;
          letter-spacing: normal;
          word-spacing: normal;
          color: #333; /* sinclo-particular value */
          border: 0;
          background: initial;
          box-shadow: none;
          text-shadow: none;
          -webkit-font-smoothing: subpixel-antialiased;
          direction: ltr;
        }
        /* HTML5 display-role reset for older browsers */
        #sincloBox article, #sincloBox aside, #sincloBox details, #sincloBox figcaption, #sincloBox figure, #sincloBox footer, #sincloBox header, #sincloBox hgroup, #sincloBox menu, #sincloBox nav, #sincloBox section { display: block; }
        #sincloBox ol, #sincloBox ul { list-style: none; }
        #sincloBox blockquote, #sincloBox q { quotes: none; }
        #sincloBox blockquote:before, #sincloBox blockquote:after, #sincloBox q:before, #sincloBox q:after { content: ''; content: none; }
        #sincloBox table { border-collapse: collapse; border-spacing: 0; }
        /* END OF reset-css */
        #sincloBox * { font-size: 12px; }
        #sincloBox.middleSize  * { font-size: 13px; }
        #sincloBox.largeSize * { font-size: 13px; }
        #sincloBox .sinclo-hide { display:none!important; }
        #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
        #sincloBox span#mainImage { cursor: pointer; z-index: 2; position: absolute; top: 7px; left: 8px; }
        #sincloBox span#mainImage img { background-color: {{widget.settings['main_color']}}; width: 62px; height: 70px }
        #sincloBox span#mainImage i {display: flex; justify-content: center; align-items: center; width: 62px; height: 70px; font-size: 43px; border: 1px solid; }
        #sincloBox span#mainImage i.normal { color: {{widget.settings['string_color']}}; background-color: {{widget.settings['main_color']}}; }
        #sincloBox span#mainImage i.fa-robot { padding-bottom: 3px }
        #sincloBox .pb07 { padding-bottom: 7px }
        #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
        #sincloBox .sinclo_re a { color: {{widget.settings['re_text_color']}}; font-size: {{widget.re_text_size}}px; text-decoration: underline;}
        #sincloBox .sinclo_se a { color: {{widget.settings['se_text_color']}}; font-size: {{widget.settings['se_text_size']}}px; }
        #sincloBox ul#chatTalk li.sinclo_re span.telno { color: {{widget.settings['re_text_color']}}; font-size: {{widget.re_text_size}}px; }
        #sincloBox ul#chatTalk li.sinclo_re span.link { color: {{widget.settings['re_text_color']}}; font-size: {{widget.re_text_size}}px; }
        #sincloBox a:hover { color: {{widget.settings['main_color']}}; }
        #sincloBox .center { text-align: center!important; padding: 7px 30px!important }
        #sincloBox #titleWrap { position: relative; }
        #sincloBox div#descriptionSet { cursor: pointer; }
        #sincloBox p#widgetTitle { position:relative; z-index: 1; cursor:pointer; border-radius: {{widget.settings['radius_ratio']}}px {{widget.settings['radius_ratio']}}px 0 0; border: 1px solid {{widget.settings['main_color']}}; border-bottom:none; background-color: {{widget.settings['main_color']}}; text-align: center; padding: 7px 0px 7px 70px; margin: 0; color: {{widget.settings['string_color']}}; height: auto; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: {{widget.settings['header_text_size']}}px; line-height: {{widget.settings['header_text_size']}}px; }
        #sincloBox p#widgetTitle.sp {font-size: 14px;}
        /*#sincloBox p#widgetTitle.spText{ text-indent: 1em; }*/
        #sincloBox div#minimizeBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>minimize.png'); background-position-y: 0px; position: absolute; top: calc(50% - 10px); right: 6px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
  /*
        #sincloBox div#addBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>add.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
        #sincloBox div#addBtn.closeButtonSetting { right: 25px; }
  */
        #sincloBox div#closeBtn { display: none; cursor: pointer; background-image: url('<?=$gallaryPath?>close.png'); background-position-y: -1.5px; position: absolute; top: calc(50%-9px); right: 6px; content: " "; width: 18px; height: 18px; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
        #sincloBox div#closeBtn.closeButtonSetting {display: inline-block; right: 5px; }
  /*
        #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
        #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
        #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
  */
        #sincloBox p#widgetSubTitle { background-color: {{widget.settings['header_background_color']}}; margin: 0; padding: 3px 0; text-align: left; border-width: 0 1px 0 1px; border-color: {{widget.settings['widget_border_color']}}; border-style: solid; padding-left: 74px; font-weight: bold; color: {{widget.settings['main_color']}}; height: auto; line-height: 24px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: {{widget.settings['header_text_size']-2}}px; }
        #sincloBox p#wdigetSubTitle.sp { font-size: 12px;}
        #sincloBox p#widgetSubTitle:not(.notNoneWidgetOutsideBorder) { border:none; }
        #sincloBox p#widgetSubTitle.details { color: {{widget.settings['sub_title_text_color']}}; }
        #sincloBox p#widgetDescription { background-color: {{widget.settings['header_background_color']}}; margin: 0; padding-bottom: 7px; text-align: left; border-width: 0 1px 1px 1px; border-color: {{widget.settings['widget_border_color']}}; border-style: solid; padding-left: 74px; height: auto; line-height: 15px; color: {{widget.settings['other_text_color']}}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: {{widget.settings['header_text_size']-2}}px; }
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
        @keyframes dotScale { 0%,80%,100%{transform: scale(0);opacity:0.3;} 40% {transform: scale(1);opacity:1.0; } }
        #sincloBox ul#chatTalk { width: 100%; height: 194px; padding: 0px 5px 30.8px 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
        #sincloBox ul#chatTalk.middleSize { height: 284px; padding: 0px 5px 45.6px 5px;}
        #sincloBox ul#chatTalk.largeSize { height: 374px; padding: 0px 5px 60px 5px;}
        #sincloBox ul#chatTalk.details { background-color: {{widget.settings['chat_talk_background_color']}}; }
        #sincloBox ul#chatTalk div.liLeft { text-align: left; }
        #sincloBox ul#chatTalk div.liBoxRight { text-align: right; }
        #sincloBox ul#chatTalk div.liRight { text-align: right; }
        #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 10px 0 0; padding: 10px 10px; font-size: 12px; line-height: 1.4; white-space: pre-wrap; }
        #sincloBox ul#chatTalk li.botNowTyping div[class^='reload_dot'] { min-width: 15px;width: 15px;min-height: 15px; height: 15px; border-radius: 100%; background-color: {{widget.settings['re_text_color']}};}
        #sincloBox ul#chatTalk li.botNowTyping div[class$='left'] {animation:dotScale 1.4s ease-in-out -0.32s infinite both}
        #sincloBox ul#chatTalk li.botNowTyping div[class$='center'] {animation:dotScale 1.4s ease-in-out -0.16s infinite both}
        #sincloBox ul#chatTalk li.botNowTyping div[class$='right'] {animation:dotScale 1.4s ease-in-out 0s infinite both}
        #sincloBox ul#chatTalk li.botNowTyping {display:flex;justify-content:space-around;align-items:center;width:100px;height:40px;padding:0 22px;border-radius:12px;margin-left:10px;}
        #sincloBox ul#chatTalk li.middleSize { font-size: 13px; }
        #sincloBox ul#chatTalk li.largeSize { font-size: 13px; }
        #sincloBox ul#chatTalk li.boxType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; }
        #sincloBox ul#chatTalk li.balloonType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
        #sincloBox ul#chatTalk li.sinclo_se.details { background-color: {{widget.getSeBackgroundColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_se.notNone { border: 1px solid {{widget.getTalkBorderColor('se')}}; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType { margin-right: 15px; border-bottom-right-radius: 0px; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; z-index: 2; border: 5px solid transparent; border-left: 5px solid {{widget.getSeBackgroundColor()}}; border-bottom: 5px solid {{widget.getSeBackgroundColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType.notNone:after { border-left: 5px solid {{widget.getTalkBorderColor('se')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('se')}}; }
        /* 二等辺三角形バージョン */
        /*#sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 5px; left: calc(100% - 2.5px); margin-top: -10px; border: 10px solid transparent; border-left: 10px solid #FFF; z-index: 2; }*/
        /*#sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: 6px; left: 100%; margin-top: -9px; border: 9px solid transparent; border-left: 9px solid #C9C9C9; z-index: 1; }*/
        #sincloBox ul#chatTalk li.sinclo_re { background-color: {{widget.makeFaintColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_re.notNone { border: 1px solid {{widget.getTalkBorderColor('re')}}; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 15px; border-bottom-left-radius: 0px; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; z-index: 2; border: 5px solid transparent; border-right: 5px solid {{widget.makeFaintColor()}}; border-bottom: 5px solid {{widget.makeFaintColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; z-index: 1; border: 5px solid transparent; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{widget.getTalkBorderColor('re')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('re')}}; }
        /* 二等辺三角形バージョン */
        /*#sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 5px; left: -19px; margin-top: -10px; border: 10px solid transparent; border-right: 10px solid {{makeBalloonTriangleColor()}}; z-index: 2; }*/
        /*#sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: 6px; left: -19px; margin-top: -9px; border: 9px solid transparent; border-right: 9px solid #C9C9C9; z-index: 1; }*/
        #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; -moz-animation-fill-mode:both; -o-animation-fill-mode:both; -ms-animation-fill-mode:both; animation-fill-mode:both; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
        #sincloBox ul#chatTalk li.effect_right { -webkit-animation-name:rightEffect; -moz-animation-name:rightEffect; -o-animation-name:rightEffect; -ms-animation-name:rightEffect; animation-name:rightEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; -moz-animation-fill-mode:both; -o-animation-fill-mode:both; -ms-animation-fill-mode:both; animation-fill-mode:both; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
        #sincloBox ul#chatTalk li.boxType.chat_right { border-radius: 12px 12px 0 12px; margin-left: 37.5px; margin-right:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_left { border-radius: 12px 12px 12px 0; margin-left: 10px; margin-right: 17.5px; }
        #sincloBox ul#chatTalk li.boxType.chat_right.middleSize { border-radius: 12px 12px 0 12px; margin-left: 45px; margin-right:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_left.middleSize { border-radius: 12px 12px 12px 0; margin-right: 21px; margin-left:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_right.largeSize { border-radius: 12px 12px 0 12px; margin-left: 52.7px; margin-right:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_left.largeSize { border-radius: 12px 12px 12px 0; margin-right: 24.6px; margin-left:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_right .smallSizeImg {max-width: 165px; max-height: 120px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.boxType.chat_left .smallSizeImg { max-width: 165px; max-height: 120px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.boxType.chat_right .middleSizeImg {max-width: 215px; max-height: 188px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.boxType.chat_left .middleSizeImg { max-width: 215px; max-height: 188px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.boxType.chat_right .largeSizeImg { max-width: 265px; max-height: 285px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.boxType.chat_left .largeSizeImg { max-width: 265px; max-height: 285px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.balloonType.chat_right { margin-left: 37.5px }
        #sincloBox ul#chatTalk li.balloonType.chat_left { margin-right: 17.5px }
        #sincloBox ul#chatTalk li.balloonType.chat_right.middleSize { margin-left: 45px }
        #sincloBox ul#chatTalk li.balloonType.chat_left.middleSize { margin-right: 21px }
        #sincloBox ul#chatTalk li.balloonType.chat_right.largeSize { margin-left: 52.7px }
        #sincloBox ul#chatTalk li.balloonType.chat_left.largeSize { margin-right: 24.6px }
        #sincloBox ul#chatTalk li.balloonType.chat_right .smallSizeImg {max-width: 165px; max-height: 120px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.balloonType.chat_left .smallSizeImg { max-width: 165px; max-height: 120px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.balloonType.chat_right .middleSizeImg {max-width: 215px; max-height: 188px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.balloonType.chat_left .middleSizeImg { max-width: 215px; max-height: 188px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.balloonType.chat_right .largeSizeImg { max-width: 265px; max-height: 285px; transform:none; display:block;}
        #sincloBox ul#chatTalk li.balloonType.chat_left .largeSizeImg { max-width: 265px; max-height: 285px; transform:none; display:block;}
        #sincloBox ul#chatTalk li span.cName { display: block; color: {{widget.settings['main_color']}}!important; font-weight: bold; font-size: {{widget.re_text_size}}px; margin: 0 0 5px 0; }
        #sincloBox ul#chatTalk li span.cName.middleSize { font-size: {{widget.re_text_size}}px }
        #sincloBox ul#chatTalk li span.cName.largeSize { font-size: {{widget.re_text_size}}px }
        #sincloBox ul#chatTalk li span.cName.details{ color: {{widget.settings['c_name_text_color']}}!important;}
        #sincloBox ul#chatTalk li span.cName:not(.details){ color: {{widget.settings['main_color']}}!important;}
        #sincloBox ul#chatTalk li.sinclo_re span.details{ color: {{widget.settings['re_text_color']}}; font-size: {{widget.re_text_size}}px; }
        #sincloBox ul#chatTalk li.sinclo_se span.details{ color: {{widget.settings['se_text_color']}}; font-size: {{widget.settings['se_text_size']}}px; }
        #sincloBox ul#chatTalk li span.sinclo-radio { display: block; margin-top: 0.2em; margin-bottom: -1.25em; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; margin: 0; padding: 0 0 0 {{widget.re_text_size+7}}px; color:{{widget.settings['re_text_color']}}; min-height: 12px; font-size: {{widget.re_text_size}}px; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] + label:before { content: ""; vertical-align: middle; position: absolute; top: {{widget.radioButtonBeforeTop}}px; left: 0px; margin-top: -{{widget.radioButtonBeforeTop}}px; width: {{widget.re_text_size}}px; height: {{widget.re_text_size}}px; border: 0.5px solid #999; border-radius: 50%; background-color: #FFF; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"]:checked + label:after { content: ""; position: absolute; top: {{widget.radioButtonAfterTop}}px; left: {{widget.radioButtonAfterLeft}}px;; margin-top: -{{widget.radioButtonAfterMarginTop}}px; width: {{widget.re_text_size-7}}px; height: {{widget.re_text_size-7}}px; background: {{widget.settings['main_color']}}; border-radius: 50%; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"]:disabled + label { opacity: 0.5;}

        /* ファイル送信 */
        #sincloBox ul#chatTalk li .sendFileContent { display: table; table-layout: fixed; width: 100%; height: 64px; white-space: pre-line; margin-bottom: 0; }
        #sincloBox ul#chatTalk li .sendFileContent .sendFileThumbnailArea { display: table-cell; width: 64px; height: 64px; border: 1px solid #d9d9d9; }
        #sincloBox ul#chatTalk li .sendFileContent .sendFileThumbnailArea::before { content: ""; height: 100%; vertical-align: middle; width: 0px; display: inline-block; }
        #sincloBox ul#chatTalk li .sendFileContent .sendFileThumbnailArea .sendFileThumbnail { text-align: center; vertical-align: middle; display: inline-block; width: 100%; height: auto; margin-left: 0; margin-bottom: 0px; margin-right: auto; }
        #sincloBox ul#chatTalk li .sendFileContent .sendFileThumbnailArea i.sendFileThumbnail { font-size: 4em; }
        #sincloBox ul#chatTalk li .sendFileContent .sendFileMetaArea { display: table-cell; vertical-align: middle; margin-left: 10px; margin-bottom: 0px; }
        #sincloBox ul#chatTalk li .sendFileContent .sendFileMetaArea .data { margin-left: 1em; margin-bottom: 5px; display: block; }
        #sincloBox ul#chatTalk li .sendFileContent .sendFileMetaArea .data.sendFileSize { margin-bottom: 0px; }

        /* ファイル受信 */
        #sincloBox ul#chatTalk li.sinclo_re.recv_file_left, #sincloBox #chatTalk li.sinclo_se.recv_file_right { display: block; padding: 10px; }
        #sincloBox ul#chatTalk.middleSize li.sinclo_re.recv_file_left, #sincloBox #chatTalk.middleSize li.sinclo_se.recv_file_right { display: block; padding: 12px; }
        #sincloBox ul#chatTalk.largeSize li.sinclo_re.recv_file_left, #sincloBox #chatTalk.largeSize li.sinclo_se.recv_file_right { display: block; padding: 14px; }
        #sincloBox #chatTalk li.sinclo_se.uploaded { display: inline-block; padding: 12px!important; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent { position: relative; border: 1px dashed {{widget.settings['re_text_color']}}; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent a.select-file-button { display: inline-block; width: 75%; padding: 5px 35px; border-radius: 0; text-decoration: none; cursor: pointer; margin: 0 auto; text-align: center; background-color: {{widget.settings['re_text_color']}}!important; color: {{widget.settings['re_background_color']}}; font-weight: normal; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent a.select-file-button:hover { opacity: .8; }
        #sincloBox #chatTalk li.sinclo_re div.cancelReceiveFileArea { margin-top: 5px; }
        #sincloBox #chatTalk li.sinclo_re div.cancelReceiveFileArea a { font-size: {{widget.re_text_size-1}}px; cursor: pointer; text-decoration: underline; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p { margin: 9px 0; text-align: center; color: {{widget.settings['re_text_color']}}; font-weight: bold; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.middleSize { margin: 13px 0; text-align: center; color: {{widget.settings['re_text_color']}}; font-weight: bold; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.largeSize { margin: 13px 0; text-align: center; color: {{widget.settings['re_text_color']}}; font-weight: bold; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.drop-area-message { line-height: 20px; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.middleSize.drop-area-message { line-height: 24px; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.largeSize.drop-area-message { line-height: 24px; }
        #sincloBox #chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.drop-area-icon i { line-height: 1; font-size: 3em; color: {{widget.settings['re_text_color']}}; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent { position: relative; background-color: #FFF; padding: 5px; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.preview { text-align: center; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.preview img { max-width: 215px; max-height: 160px; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.preview img.small { max-width: 165px; max-height: 120px; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.preview img.middle { max-width: 215px; max-height: 188px; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.preview img.large { max-width: 265px; max-height: 285px; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.commentarea { text-align: center; width: 100%; font-size: 0px; padding-bottom: 3px; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.commentarea textarea { border-radius: 0px; width: 97%; height: 40px; resize: none; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.selectFileArea p.commentarea textarea:focus { outline: none!important; border-color: {{widget.settings['chat_send_btn_background_color']}}!important;}
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.actionButtonWrap { display: flex; justify-content: space-between; width: 97%; margin: 0 auto; font-size: 0px; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.actionButtonWrap a:hover { opacity: .8; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.actionButtonWrap a.cancel-file-button { margin-right: 2px; width: 49%; height: auto; padding: 5px 10px; border-radius: 0; text-decoration: none; cursor: pointer; margin: 0 auto; text-align: center; background-color: #7F7F7F!important; color: #FFF; font-weight: normal; word-break: keep-all; }
        #sincloBox #chatTalk li.sinclo_se div.receiveFileContent div.actionButtonWrap a.send-file-button { margin-left: 2px; width: 49%; height: auto; padding: 5px 10px; border-radius: 0; text-decoration: none; cursor: pointer; margin: 0 auto; text-align: center; background-color: {{widget.settings['chat_send_btn_background_color']}}; color: {{widget.settings['chat_send_btn_text_color']}};  font-weight: normal; word-break: keep-all; }
        #sincloBox #chatTalk li div div.loadingPopup { display: flex; flex-flow: column nowrap; justify-content: center; align-items: center; text-align:center; vertical-align: middle; color: #FFF; background-color: rgba(0, 0, 0, 0.7); position: absolute; top:0; right: 0; bottom: 0; left: 0; }
        #sincloBox #chatTalk li div div.loadingPopup.hide { display: none; }
        #sincloBox #chatTalk li div div.loadingPopup i { font-size: 6em; text-align:center; color: #FFF; }
        #sincloBox #chatTalk li div div.loadingPopup p.progressMessage { text-align:center; color: #FFF; }
        #sincloBox #chatTalk li div div.loadingPopup i.load { -webkit-animation: spin 1.5s linear infinite; -moz-animation: spin 1.5s linear infinite; -ms-animation: spin 1.5s linear infinite; -o-animation: spin 1.5s linear infinite; animation: spin 1.5s linear infinite; }
        @-webkit-keyframes spin { 0% {-webkit-transform: rotate(0deg);} 100% {-webkit-transform: rotate(360deg);} }
        @-moz-keyframes spin { 0% {-moz-transform: rotate(0deg);} 100% {-moz-transform: rotate(360deg);} }
        @-ms-keyframes spin { 0% {-ms-transform: rotate(0deg);} 100% {-ms-transform: rotate(360deg);} }
        @-o-keyframes spin { 0% {-o-transform: rotate(0deg);} 100% {-o-transform: rotate(360deg);} }
        @keyframes spin { 0% {transform: rotate(0deg);} 100% {transform: rotate(360deg);} }

        #sincloBox section#chatTab #flexBoxWrap div#messageBox { height: 75px!important; padding: 5px; }
        #sincloBox section#chatTab #flexBoxWrap div#miniFlexBoxHeight { height: 48px!important; padding: 5px; }
        #sincloBox section#chatTab textarea#sincloChatMessage, #sincloBox section#chatTab input#miniSincloChatMessage { width: 80%; height: 100%; color: {{widget.settings['other_text_color']}}; margin: 0; resize: none; padding: 5px; }
        #sincloBox section#chatTab input#miniSincloChatMessage { height: 36px; }
        #sincloBox section#chatTab textarea#sincloChatMessage.details, #sincloBox section#chatTab input#miniSincloChatMessage.details { color: {{widget.settings['message_box_text_color']}}; background-color: {{widget.settings['message_box_background_color']}}; }
        #sincloBox section#chatTab textarea#sincloChatMessage.details.notNone, #sincloBox section#chatTab input#miniSincloChatMessage.details.notNone { border: 1px solid {{widget.settings['message_box_border_color']}}!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage.notNone, #sincloBox section#chatTab input#miniSincloChatMessage.notNone { border-radius: 5px 0 0 5px!important; border: 1px solid {{widget.settings['chat_talk_border_color']}}!important; border-right-color: transparent!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage.notNone:focus, #sincloBox section#chatTab input#miniSincloChatMessage.notNone:focus { border-color: {{widget.settings['main_color']}}!important; outline: none!important; border-right-color: transparent!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage:not(.notNone), #sincloBox section#chatTab input#miniSincloChatMessage:not(.notNone) { border: none!important; text-align: left; }
        #sincloBox section#chatTab #sincloChatSendBtn, #sincloBox section#chatTab #miniSincloChatSendBtn { width: 20%; height: 100%; padding: 25px 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{widget.settings['main_color']}}!important; color: {{widget.settings['string_color']}}; font-weight: bold; font-size: 1.2em; display: flex; justify-content: center; align-items: center; }
        #sincloBox section#chatTab #miniSincloChatSendBtn { height: 36px; padding: 8px 0; }
        #sincloBox section#chatTab #sincloChatSendBtn.details, #sincloBox section#chatTab #miniSincloChatSendBtn.details{ background-color: {{widget.settings['chat_send_btn_background_color']}}!important; }
        #sincloBox section#chatTab #sincloChatSendBtn.middleSize, #sincloBox section#chatTab #miniSincloChatSendBtn.middleSize { padding: 20px 0; margin: 0 auto; font-size: 1.2em;}
        #sincloBox section#chatTab #sincloChatSendBtn.largeSize, #sincloBox section#chatTab #miniSincloChatSendBtn.largeSize { padding: 20px 0; margin: 0 auto; font-size: 1.2em;}
        #sincloBox section#chatTab #sincloChatSendBtn span, #sincloBox section#chatTab #miniSincloChatSendBtn span { color: {{widget.settings['string_color']}} }
        #sincloBox section#chatTab #sincloChatSendBtn span.details, #sincloBox section#chatTab #miniSincloChatSendBtn span.details { color: {{widget.settings['chat_send_btn_text_color']}}; font-weight: bold; }
        #sincloBox section#chatTab #messageBox.messageBox, #sincloBox section#chatTab #miniFlexBoxHeight.messageBox {border-top: 1px solid {{widget.settings['widget_border_color']}}; padding: 0.5em;}
        #sincloBox section#chatTab #messageBox.messageBox:not(.notNoneWidgetOutsideBorder), #sincloBox section#chatTab #miniFlexBoxHeight.messageBox:not(.notNoneWidgetOutsideBorder) { border-top:none; }
        #sincloBox section#chatTab #messageBox.messageBox.details, #sincloBox section#chatTab #miniFlexBoxHeight.messageBox.details { background-color: {{widget.settings['chat_message_background_color']}}; border-top: 1px solid {{widget.settings['widget_inside_border_color']}}; }
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
        .disableCopy{ user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none; }
      </style>
      <!-- 画像 -->
      <span id="mainImage" class="widgetOpener" ng-hide="widget.spHeaderLightToggle() || widget.mainImageToggle !== '1'">
        <img ng-if="widget.isPictureImage()" ng-src="{{widget.settings['main_image']}}" err-src="<?=$gallaryPath?>chat_sample_picture.png" width="62" height="70" alt="チャット画像"/>
        <i ng-if="widget.isIconImage()" class="sinclo-fal {{widget.settings['main_image']}}" alt="チャット画像"></i>
      </span>
      <!-- 画像 -->
      <div id="titleWrap">
        <!-- タイトル -->
        <p ng-if="widget.settings['widget_title_top_type'] === '1' && widget.mainImageToggle == '1'" id="widgetTitle" class="widgetOpener notSelect leftPositionImageTitle" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize, spText:widget.showWidgetType === 3}">{{widget.settings['title']}}</p>
        <p ng-if="widget.settings['widget_title_top_type'] === '2' && widget.mainImageToggle == '1'" id="widgetTitle" class="widgetOpener notSelect centerPositionImageTitle" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize, spText:widget.showWidgetType === 3}">{{widget.settings['title']}}</p>
        <p ng-if="widget.settings['widget_title_top_type'] === '1' && widget.mainImageToggle == '2'" id="widgetTitle" class="widgetOpener notSelect leftPositionNoImageTitle" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize, spText:widget.showWidgetType === 3}">{{widget.settings['title']}}</p>
        <p ng-if="widget.settings['widget_title_top_type'] === '2' && widget.mainImageToggle == '2'" id="widgetTitle" class="widgetOpener notSelect centerPositionNoImageTitle" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize, spText:widget.showWidgetType === 3}">{{widget.settings['title']}}</p>
        <p ng-if="widget.settings['widget_title_top_type'] !== '1' && widget.settings['widget_title_top_type'] !== '2' && widget.mainImageToggle == '1'" id="widgetTitle" class="widgetOpener notSelect centerPositionImageTitle" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize, spText:widget.showWidgetType === 3}">{{widget.settings['title']}}</p>
        <p ng-if="widget.settings['widget_title_top_type'] !== '1' && widget.settings['widget_title_top_type'] !== '2' && widget.mainImageToggle == '2'" id="widgetTitle" class="widgetOpener notSelect centerPositionNoImageTitle" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize, spText:widget.showWidgetType === 3}">{{widget.settings['title']}}</p>
        <!-- タイトル -->
        <div id="minimizeBtn" class="widgetOpener" ng-class="" style="display: block;"></div>
        <div id="closeBtn" ng-class="{closeButtonSetting: widget.settings['close_button_mode_type'] === '2'}"></div>
      </div>

  <!--
      <div id="addBtn" class="widgetOpener" ng-class="{closeButtonSetting: widget.settings['close_button_mode_type'] === '2'}" style="display: none;"></div>
   -->
      <div id='descriptionSet' class="widgetOpener notSelect" ng-hide="widget.spHeaderLightToggle() || widget.mainImageToggle == '2' && widget.subTitleToggle == '2' && widget.descriptionToggle == '2'">

        <!-- サブタイトル -->

        <p ng-if="widget.settings['widget_title_name_type'] === '1' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details leftPositionImage">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] === '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details centerPositionImage">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] === '1' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details leftPositionNoImage">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] === '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details centerPositionNoImage">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] === '1' && widget.descriptionToggle == '2' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details leftPositionImageCompany">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] === '2' && widget.descriptionToggle == '2' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details centerPositionImageCompany">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] === '1' && widget.descriptionToggle == '2' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details leftPositionNoImageCompany">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] === '2' && widget.descriptionToggle == '2' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details centerPositionNoImageCompany">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.descriptionToggle == '1' && widget.subTitleToggle == '2' && widget.mainImageToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details noCompanyImageExplain">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.descriptionToggle == '1' && widget.subTitleToggle == '2' && widget.mainImageToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details noCompanyImageExplain">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.descriptionToggle == '2' && widget.subTitleToggle == '2' && widget.mainImageToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.descriptionToggle == '2' && widget.subTitleToggle == '2' && widget.mainImageToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] !== '1' && widget.settings['widget_title_name_type'] !== '2' && widget.descriptionToggle == '2' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details leftPositionImageCompany">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] !== '1' && widget.settings['widget_title_name_type'] !== '2' && widget.descriptionToggle == '2' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details leftPositionNoImageCompany">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] !== '1' && widget.settings['widget_title_name_type'] !== '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details leftPositionImage">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_name_type'] !== '1' && widget.settings['widget_title_name_type'] !== '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetSubTitle" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}" class="details leftPositionNoImage">{{widget.subTitleToggle == '1' ? widget.settings['sub_title'] : '&thinsp;'}}</p>

        <!-- 説明文 -->
        <p ng-if="widget.settings['widget_title_explain_type'] === '1' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetDescription" class="details leftPositionImage" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] === '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetDescription" class="details centerPositionImage" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] === '1' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetDescription" class="details leftPositionNoImage" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] === '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetDescription" class="details centerPositionNoImage" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] === '1' && widget.descriptionToggle == '1' && widget.subTitleToggle == '2' && widget.mainImageToggle == '1'" id="widgetDescription" class="details leftPositionImageCompany" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] === '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '2' && widget.mainImageToggle == '1'" id="widgetDescription" class="details centerPositionImageCompany" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] === '1' && widget.descriptionToggle == '1' && widget.subTitleToggle == '2' && widget.mainImageToggle == '2'" id="widgetDescription" class="details leftPositionNoImageCompany" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] === '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '2' && widget.mainImageToggle == '2'" id="widgetDescription" class="details centerPositionNoImageCompany" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.descriptionToggle == '2' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetDescription" class="details noCompanyImageExplain" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.descriptionToggle == '2' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetDescription" class="details noCompanyImageExplain" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.descriptionToggle == '2' && widget.subTitleToggle == '2' && widget.mainImageToggle == '1'" id="widgetDescription" class="details" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.descriptionToggle == '2' && widget.subTitleToggle == '2' && widget.mainImageToggle == '2'" id="widgetDescription" class="details" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] !== '1' && widget.settings['widget_title_explain_type'] !== '2' && widget.descriptionToggle == '2' && widget.subTitleToggle == '1'" id="widgetDescription" class="details noCompanyImageExplain" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] !== '1' && widget.settings['widget_title_explain_type'] !== '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '1'" id="widgetDescription" class="details leftPositionImage" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
        <p ng-if="widget.settings['widget_title_explain_type'] !== '1' && widget.settings['widget_title_explain_type'] !== '2' && widget.descriptionToggle == '1' && widget.subTitleToggle == '1' && widget.mainImageToggle == '2'" id="widgetDescription" class="details leftPositionNoImage" ng-class="{notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false}">{{widget.descriptionToggle == '1' ? widget.settings['description'] : '&thinsp;'}}</p>
      </div>
      <div id="miniTarget">
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
        <section id="chatTab" ng-hide="widget.showTab !== 'chat'" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false, middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">

  <!-- chat_message_copy 0 stayt -->
          <ul id="chatTalk" class="details" ng-class="{ middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize, disableCopy: widget.settings['chat_message_copy']== '1'}">
            <div style="height: auto!important; padding:0; display: none;" ng-class="{liBoxRight: widget.settings['chat_message_design_type'] == 1, liRight: widget.settings['chat_message_design_type'] == 2}" >
            <li class="sinclo_se chat_right details" ng-class="{ notNone:widget.se_border_none === '' || widget.se_border_none === false, middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize,boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_right: widget.settings['chat_message_with_animation'] === '1'}" ><span class="details">サイト訪問者側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li class="sinclo_re chat_left" ng-class="{ notNone:widget.re_border_none === '' || widget.re_border_none === false, middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize,boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_left: widget.settings['chat_message_with_animation'] === '1'}"><span class="cName details" ng-if="widget.settings['show_automessage_name'] === '1'" ng-class="{ middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">{{widget.settings['sub_title']}}</span><span class="details">企業側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li class="sinclo_re file_left" ng-class="{ notNone:widget.re_border_none === '' || widget.re_border_none === false, middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_left: widget.settings['chat_message_with_animation'] === '1'}"><span class="cName details" ng-if="widget.settings['show_automessage_name'] === '1'" ng-class="{ middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">{{widget.settings['sub_title']}}</span><span class="details">企業側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li class="sinclo_re chat_left recv_file_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_left: widget.settings['chat_message_with_animation'] === '1', middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><div class="receiveFileContent"><div class="selectFileArea"><p class="drop-area-message" ng-class="{ middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}"></p><p class="drop-area-icon" ng-class="{ middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}"><i class="sinclo-fal fa-cloud-upload"></i></p><p ng-class="{ middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">または</p><p class="drop-area-button" ng-class="{ middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}"><a class="select-file-button">ファイルを選択</a></p><input type="file" class="receiveFileInput" name="receiveFileInput" style="display:none"></div><div class='loadingPopup hide'><i class='sinclo-fal fa-spinner load'></i><p class='progressMessage'>読み込み中です。<br>しばらくお待ち下さい。</p></div></div><div class="cancelReceiveFileArea"><a>{{setItem.cancelLabel}}</a></div></li>
            </div>
            <div style="height: auto!important; padding:0; display: none; text-align: right;">
              <li class="sinclo_se chat_right recv_file_right details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_right: widget.settings['chat_message_with_animation'] === '1', middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3}"><div class="receiveFileContent"><div class="selectFileArea"><p class="preview"></p><p class="commentLabel">コメント</p><p class="commentarea"><textarea style="font-size: 13px; border-width: 1px; padding: 5px; line-height: 1.5;"></textarea></p><div class="actionButtonWrap"><a class="cancel-file-button">選択し直す</a><a class="send-file-button">送信する</a></div></div><div class='loadingPopup hide'><i class='sinclo-fal fa-spinner load'></i><p class='progressMessage'>読み込み中です。<br>しばらくお待ち下さい。</p></div></div></li>
            </div>
          </ul>
  <!-- chat_message_copy 0 end -->

  <!-- chat_message_copy 0 stayt -->
          <div id="flexBoxWrap">
            <div id="messageBox" class="messageBox details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false }">
              <textarea name="sincloChat" id="sincloChatMessage" class="details" ng-class="{ notNone:widget.message_box_border_none === ''||widget.message_box_border_none === false}" ng-attr-placeholder="メッセージを入力してください{{widget.chat_area_placeholder_sp}}"></textarea>
              <a id="sincloChatSendBtn" class="notSelect details" ng-click="canVisitorSendMessage && visitorSendMessage()"><span class="details">送信</span></a>
            </div>
            <div id="miniFlexBoxHeight" class="messageBox details sinclo-hide">
              <input type="text" name="miniSincloChat" id="miniSincloChatMessage" class="details" ng-class="{ notNone:widget.message_box_border_none === ''||widget.message_box_border_none === false}" maxlength="1000" ng-attr-placeholder="メッセージを入力してください{{widget.chat_area_placeholder_sp}}"></input>
              <a id="miniSincloChatSendBtn" class="notSelect details" ng-click="canVisitorSendMessage && visitorSendMessage()">送信</a>
            </div>
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
        <section id="callTab" ng-hide="widget.showTab !== 'call'" class="details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false, middleSize: widget.widgetSizeTypeToggle === '2'}">
          <div style="height: 50px;margin: 15px 25px;">
          <!-- アイコン -->
          <span id="telIcon"><img width="19.5" height="33" src="<?=C_PATH_NODE_FILE_SERVER?>/img/call.png" style="margin: 6px 12px"></span>
          <!-- アイコン -->

          <!-- 受付電話番号 -->
          <pre id="telNumber" ng-class="{notUseTime: widget.timeTextToggle !== '1',middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}" >{{widget.settings['tel']}}</pre>
          <!-- 受付電話番号 -->

          <!-- 受付時間 -->
          <pre id="telTime" ng-if="widget.timeTextToggle == '1'" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">受付時間： {{widget.settings['time_text']}}</pre>
          <!-- 受付時間 -->

          </div>

          <!-- テキスト -->
          <div id="telContent" ng-class="{middleSize: widget.widgetSizeTypeToggle === '2',largeSize: widget.widgetSizeTypeToggle === '3'}"><div class="tblBlock"><span ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.isLargeSize}">{{widget.settings['content']}}</span></div></div>
          <!-- テキスト -->

          <span id="accessIdArea" ng-class="{middleSize: widget.isMiddleSize,largeSize: widget.showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
          ●●●●
          </span>
        </section>
      <?php endif; ?>
        <p id="footer" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, disableCopy: widget.settings['chat_message_copy']== '1'}">Powered by <a target="sinclo" href="https://sinclo.medialink-ml.co.jp/lp/?utm_medium=web-widget&utm_campaign=widget-referral">sinclo</a></p>
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
        #sincloBox .sinclo_re a { color: {{widget.settings['re_text_color']}}; font-size: {{widget.re_text_size}}px; text-decoration: underline;}
        #sincloBox .sinclo_se a { color: {{widget.settings['se_text_color']}}; font-size: {{widget.settings['se_text_size']}}px; }
        #sincloBox ul#chatTalk li.sinclo_re span.telno { color: {{widget.settings['re_text_color']}};}
        #sincloBox ul#chatTalk li.sinclo_re span.link { color: {{widget.settings['re_text_color']}} !important;}
        #sincloBox a:hover { color: {{widget.settings['main_color']}}; }
        #sincloBox p#widgetTitle { text-align: center!important; padding: 7px 30px!important; position:relative; z-index: 1; cursor:pointer; border-radius: 0; border: 1px solid {{widget.settings['main_color']}}; border-bottom: none; background-color: {{widget.settings['main_color']}};text-align: center; font-size: 14px; margin: 0;color: {{widget.settings['string_color']}}; height: 32px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; line-height: {{widget.settings['header_text_size']}}px;}
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
        #sincloBox ul#chatTalk { width: 100%; height: 100px; padding: 0px 5px 16px 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
        #sincloBox ul#chatTalk.details { background-color: {{widget.settings['chat_talk_background_color']}}; }
        #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 10px 0 0; padding: 10px 10px; font-size: 11px; line-height: 1.4; white-space: pre; color: {{widget.settings['message_text_color']}}; }
        #sincloBox ul#chatTalk div.liLeft { text-align: left; }
        #sincloBox ul#chatTalk div.liBoxRight { text-align: right; }
        #sincloBox ul#chatTalk div.liRight { text-align: right; }
        #sincloBox ul#chatTalk li.botNowTyping div[class^='reload_dot'] { min-width: 15px;width: 15px; min-height: 15px;height: 15px; border-radius: 100%; background-color: {{widget.settings['re_text_color']}};}
        #sincloBox ul#chatTalk li.boxType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; }
        #sincloBox ul#chatTalk li.balloonType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
        #sincloBox ul#chatTalk li.sinclo_se.details { background-color: {{widget.getSeBackgroundColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_se.notNone { border: 1px solid {{widget.getTalkBorderColor('se')}}; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType { margin-right: 15px; border-bottom-right-radius: 0px; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; border: 5px solid transparent; border-left: 5px solid {{widget.getSeBackgroundColor()}}; border-bottom: 5px solid {{widget.getSeBackgroundColor()}}; z-index: 2; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
        #sincloBox ul#chatTalk li.sinclo_se.balloonType.notNone:after { border-left: 5px solid {{widget.getTalkBorderColor('se')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('se')}}; }
        #sincloBox ul#chatTalk li.sinclo_re { background-color: {{widget.makeFaintColor()}}; }
        #sincloBox ul#chatTalk li.sinclo_re.notNone { border: 1px solid {{widget.getTalkBorderColor('re')}}; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 15px; border-bottom-left-radius: 0px; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; border: 5px solid transparent; border-right: 5px solid {{widget.makeFaintColor()}}; border-bottom: 5px solid {{widget.makeFaintColor()}}; z-index: 2; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
        #sincloBox ul#chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{widget.getTalkBorderColor('re')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('re')}}; }
        #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:both; -moz-animation-fill-mode:both; -o-animation-fill-mode:both; -ms-animation-fill-mode:both; animation-fill-mode:both; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
        #sincloBox ul#chatTalk li.boxType.chat_right { border-radius: 12px 12px 0 12px; margin-left: 37.5px; margin-right:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_left { border-radius: 12px 12px 12px 0; margin-left: 10px; margin-right: 17.5px; }
        #sincloBox ul#chatTalk li.boxType.chat_right.middleSize { border-radius: 12px 12px 0 12px; margin-left: 45px; margin-right:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_left.middleSize { border-radius: 12px 12px 12px 0; margin-right: 21px; margin-left:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_right.largeSize { border-radius: 12px 12px 0 12px; margin-left: 52.7px; margin-right:10px; }
        #sincloBox ul#chatTalk li.boxType.chat_left.largeSize { border-radius: 12px 12px 12px 0; margin-right: 24.6px; margin-left:10px; }
        #sincloBox ul#chatTalk li.balloonType.chat_right { margin-left: 37.5px }
        #sincloBox ul#chatTalk li.balloonType.chat_left { margin-right: 17.5px }
        #sincloBox ul#chatTalk li.balloonType.chat_right.middleSize { margin-left: 45px }
        #sincloBox ul#chatTalk li.balloonType.chat_left.middleSize { margin-right: 21px }
        #sincloBox ul#chatTalk li.balloonType.chat_right.largeSize { margin-left: 52.7px }
        #sincloBox ul#chatTalk li.balloonType.chat_left.largeSize { margin-right: 24.6px }
        #sincloBox ul#chatTalk li span.cName { display: block; color: {{widget.settings['main_color']}}!important; font-weight: bold; font-size: 12px; margin: 0 0 5px 0; }
        #sincloBox ul#chatTalk li span.cName.details{ color: {{widget.settings['c_name_text_color']}}!important;}
        #sincloBox ul#chatTalk li span.cName:not(.details){ color: {{widget.settings['main_color']}}!important;}
        #sincloBox ul#chatTalk li span:not(.details){  color: {{widget.settings['message_text_color']}}!important; }
        #sincloBox ul#chatTalk li.sinclo_re span.details{ color: {{widget.settings['re_text_color']}};}
        #sincloBox ul#chatTalk li.sinclo_se span.details{ color: {{widget.settings['se_text_color']}};}
        #sincloBox ul#chatTalk li span.sinclo-radio { display: block; margin-top: 0.2em; margin-bottom: -1.25em; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; padding: 0 0 0 15px; color:{{widget.settings['re_text_color']}}; min-height: 12px; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: 1px; left: 0px; width: 12px; height: 12px; border: 0.5px solid #999; border-radius: 50%; background-color: #FFF; }
        #sincloBox ul#chatTalk li span.sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: 4px; left: 3px; width: 7px; height: 7px; background: {{widget.settings['main_color']}}; border-radius: 50%; }
        #sincloBox section#chatTab > div { height: 65px!important;  padding: 10px; }
        #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; color: {{widget.settings['other_text_color']}}; margin: 0; resize: none; padding: 5px; }
        #sincloBox section#chatTab textarea#sincloChatMessage.details { color: {{widget.settings['message_box_text_color']}}; background-color: {{widget.settings['message_box_background_color']}}; }
        #sincloBox section#chatTab textarea#sincloChatMessage.details.notNone { border: 1px solid {{widget.settings['message_box_border_color']}}!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage.notNone { border-radius: 5px 0 0 5px!important; border: 1px solid {{widget.settings['chat_talk_border_color']}}!important; border-right-color: transparent!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage.notNone:focus { border-color: {{widget.settings['main_color']}}!important; outline: none!important; border-right-color: transparent!important; }
        #sincloBox section#chatTab textarea#sincloChatMessage:not(.notNone){ border: none!important }
        #sincloBox section#chatTab #sincloChatSendBtn, #sincloBox section#chatTab #miniSincloChatSendBtn { width: 20%; height: 100%; padding: 1em 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{widget.settings['main_color']}}!important; color: {{widget.settings['string_color']}}; font-weight: bold; font-size: 1.2em;}
        #sincloBox section#chatTab #miniSincloChatSendBtn { width: 20%; height: 100%; padding: 1em 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{widget.settings['main_color']}}!important; color: {{widget.settings['string_color']}}; font-weight: bold; font-size: 1.2em;}
        #sincloBox section#chatTab #sincloChatSendBtn.details, #sincloBox section#chatTab #miniSincloChatSendBtn.details { background-color: {{widget.settings['chat_send_btn_background_color']}}!important; }
        #sincloBox section#chatTab #sincloChatSendBtn span, #sincloBox section#chatTab #miniSincloChatSendBtn span { color: {{widget.settings['string_color']}} }
        #sincloBox section#chatTab #sincloChatSendBtn span.details, #sincloBox section#chatTab #miniSincloChatSendBtn span.details { color: {{widget.settings['chat_send_btn_text_color']}}; font-weight: bold; }
        #sincloBox section#chatTab #messageBox.messageBox, #sincloBox section#chatTab #miniFlexBoxHeight.messageBox {border-top: 1px solid {{widget.settings['widget_border_color']}}; padding: 0.5em;}
        #sincloBox section#chatTab #messageBox.messageBox:not(.notNoneWidgetOutsideBorder), #sincloBox section#chatTab #miniFlexBoxHeight.messageBox:not(.notNoneWidgetOutsideBorder) { border:none }
        #sincloBox section#chatTab #messageBox.messageBox.details, #sincloBox section#chatTab #miniFlexBoxHeight.messageBox.details { background-color: {{widget.settings['chat_message_background_color']}}; border-top: 1px solid {{widget.settings['widget_inside_border_color']}}; }
        #sincloBox section#chatTab #messageBox.messageBox.details:not(.notNone), #sincloBox section#chatTab #miniFlexBoxHeight.messageBox.details:not(.notNone) { border-top: none; }
        .disableCopy{ user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none; }
      </style>
  <!-- chat_message_copy 0 stayt -->
      <div>
        <!-- タイトル -->
        <p id="widgetTitle" class="widgetOpener" ng-class="{center: widget.mainImageToggle == '2', disableCopy: widget.settings['chat_message_copy'] == '1'}" >{{widget.settings['title']}}</p>
        <!-- タイトル -->
      </div>
  <!-- chat_message_copy 0 end -->
      <div id="minimizeBtn" class="widgetOpener"></div>
  <!--
      <div id="addBtn" class="widgetOpener"></div>
   -->
      <div id="miniTarget">
        <section id="chatTab" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false}">
          <ul id="chatTalk" class="details"  ng-class="{disableCopy: widget.settings['chat_message_copy'] == '1'}">
            <div style="height: auto!important; padding:0; display: none;" ng-class="{liBoxRight: widget.settings['chat_message_design_type'] == 1, liRight: widget.settings['chat_message_design_type'] == 2}">
              <li class="sinclo_se chat_right details" ng-class="{notNone:widget.se_border_none === '' || widget.se_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_right: widget.settings['chat_message_with_animation'] === '1'}"><span class="details" >サイト訪問者側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li class="sinclo_re chat_left" ng-class="{notNone:widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_left: widget.settings['chat_message_with_animation'] === '1'}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details" >{{widget.settings['sub_title']}}</span><span class="details">企業側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li class="sinclo_re file_left" ng-class="{notNone:widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, effect_left: widget.settings['chat_message_with_animation'] === '1'}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details" >{{widget.settings['sub_title']}}</span><span class="details">企業側メッセージ</span></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li class="sinclo_re chat_left recv_file_left" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><div class="receiveFileContent"><div class="selectFileArea"><p class="drop-area-message"></p><p class="drop-area-icon"><i class="fa fa-5x fa-cloud-upload"></i></p><p>または</p><p class="drop-area-button"><a class="select-file-button">ファイルを選択</a></p><input type="file" class="receiveFileInput" name="receiveFileInput"></div></div><div class="cancelReceiveFileArea"><a>{{setItem.cancelLabel}}</a></div></li>
            </div>
            <div style="height: auto!important; padding:0; display: none;">
              <li class="sinclo_se chat_right recv_file_right" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><div class="receiveFileContent"><div class="selectFileArea"><p class="preview"></p><p class="commentLabel">コメント</p><p class="commentarea"><textarea style="width:90%; height: auto;"></textarea></p><div class="actionButtonWrap"><a class="cancel-file-button">選択し直す</a><a class="send-file-button">送信する</a></div><input type="file" class="receiveFileInput" name="receiveFileInput"></div></div></li>
            </div>
            <!-- <div style="height: auto!important; padding:0;">
              <li class="showAnimationSample sinclo_re chat_left" ng-class="{notNone:re_border_none === '' || re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}"><span class="cName details" >{{widget.settings['sub_title']}}</span><span class="details">○○についてですね<br>どのようなご質問でしょうか？</span></li>
            </div> -->
          </ul>




          <div id="messageBox" class="messageBox details" ng-class="{ notNoneWidgetOutsideBorder:widget.widget_outside_border_none === ''||widget.widget_outside_border_none === false, notNone:widget.widget_inside_border_none === ''||widget.widget_inside_border_none === false }">
            <textarea name="sincloChat" id="sincloChatMessage" class="details" ng-class="{ notNone:widget.message_box_border_none === ''||widget.message_box_border_none === false}" ng-attr-placeholder="メッセージを入力してください{{widget.chat_area_placeholder_sp}}"></textarea>
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
