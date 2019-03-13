<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/25
 * Time: 20:28
 */
?>
<style ng-if="widget.settings['widget_size_type'] == 1">
  #popup #popup-frame-base #popup-frame.p_diagrams_text {width: 785px}
  #popup #popup-frame-base #popup-frame.p_diagrams_text #text_modal #text_modal_preview {width: 285px}
</style>
<style ng-if="widget.settings['widget_size_type'] == 2">
  #popup #popup-frame-base #popup-frame.p_diagrams_text {width: 842px}
  #popup #popup-frame-base #popup-frame.p_diagrams_text #text_modal #text_modal_preview {width: 342px}
</style>
<style ng-if="widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4">
  #popup #popup-frame-base #popup-frame.p_diagrams_text {width: 898px}
  #popup #popup-frame-base #popup-frame.p_diagrams_text #text_modal #text_modal_preview {width: 398px}
</style>
<style ng-if="widget.settings['widget_size_type'] == 5">
  #popup #popup-frame-base #popup-frame.p_diagrams_text {min-width: 785px; max-width: 898px; width: calc(500px + {{widget.settings['widget_custom_width']}}px)}
  #popup #popup-frame-base #popup-frame.p_diagrams_text #text_modal #text_modal_preview {width: {{widget.settings['widget_custom_width']}}px}
</style>
<style>
  .diagram_preview_area { width: 100%; list-style-type: none; margin: 0; max-height: 275px; overflow-y: auto;}
  .diagram_preview_area { background-color: {{widget.settings['chat_talk_background_color']}}; height: calc(100% - 66px)}
  .diagram_preview_area .iconDiv.arrowBottom { align-items: flex-end;}
  .diagram_preview_area .iconDiv.arrowUp {align-items: flex-start;}
  .diagram_preview_area .iconDiv.arrowUp div.img_wrapper { margin-top: 10px; }
  .diagram_preview_area .iconDiv.arrowUp i { margin-top: 10px; }
  .diagram_preview_area .iconDiv > i { display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; font-size: 23px; color: {{widget.settings['string_color']}}; border-radius: 50%; background-color: {{widget.settings['main_color']}}}
  .diagram_preview_area .iconDiv > i.icon_border { border: 1px solid {{widget.settings['string_color']}};}
  .diagram_preview_area li { border-radius: 5px; background-color: #FFF; margin: 10px 0 0; padding: 12px; font-size: 12px; line-height: 1.4; white-space: pre; color: {{widget.settings['message_text_color']}}; }
  .diagram_preview_area li { word-break: break-all; white-space: pre-wrap; justify-self: start; }
  .diagram_preview_area li.boxType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; }
  .diagram_preview_area div.arrowUp li.boxType.chat_left { border-radius: 0 12px 12px 12px ; }
  .diagram_preview_area div.arrowBottom li.boxType.chat_left { border-radius: 12px 12px 12px 0; }
  .diagram_preview_area li.boxType.chat_left { margin-left: 10px; margin-right: 17.5px; }
  .diagram_preview_area li.boxType.chat_left.middleSize { margin-left: 10px; margin-right: 21px; }
  .diagram_preview_area li.boxType.chat_left.largeSize { margin-left: 10px; margin-right: 24.6px; }
  .diagram_preview_area li.balloonType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
  .diagram_preview_area li.no-wrap { display: block!important; padding: 10px 0 0 0; justify-self: stretch; }
  .diagram_preview_area li.all-round { border-radius: 12px!important; }
  .diagram_preview_area li.sinclo_re.no-wrap span.sinclo-text-line { display: block; padding: 0 15px; }
  .diagram_preview_area li.sinclo_re {  background-color: {{widget.makeFaintColor()}}; font-size: {{widget.settings['re_text_size']}}px; }
  .diagram_preview_area li.sinclo_re span.details{ color: {{widget.settings['re_text_color']}}; font-size: {{widget.settings['re_text_size']}}px;}
  .diagram_preview_area li.sinclo_re span.sinclo-text-line{ display: inline-block; color: {{widget.settings['re_text_color']}}; font-size: {{widget.settings['re_text_size']}}px;}
  .diagram_preview_area li.sinclo_re a { color: {{widget.settings['re_text_color']}}; background-color: {{widget.makeFaintColor()}};}
  .diagram_preview_area li.sinclo_re.notNone { border: 1px solid {{widget.getTalkBorderColor('re')}}; }
  .diagram_preview_area li.sinclo_re.balloonType:not(.no-wrap) { margin-left: 10px; padding-right: 15px; border-bottom-left-radius: 0px; }
  .diagram_preview_area li.sinclo_re.balloonType:not(.no-wrap):before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; z-index: 2; border: 5px solid transparent; border-right: 5px solid {{widget.makeFaintColor()}}; border-bottom: 5px solid {{widget.makeFaintColor()}}; }
  .diagram_preview_area li.sinclo_re.balloonType:not(.no-wrap):after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; z-index: 1; border: 5px solid transparent; }
  .diagram_preview_area li.sinclo_re.balloonType.notNone:not(.no-wrap):after { border-right: 5px solid {{widget.getTalkBorderColor('re')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('re')}}; }
  .diagram_preview_area li.balloonType.chat_left { margin-right: 17.5px; }
  .diagram_preview_area li.balloonType.chat_left.middleSize { margin-right: 21px; }
  .diagram_preview_area li.balloonType.chat_left.largeSize { margin-right: 24.6px; }

  .diagram_preview_area li span.cName { display: block; color: {{widget.settings['main_color']}}!important; font-weight: bold; font-size: {{widget.settings['re_text_size']}}px; margin: 0 0 5px 0; }
  .diagram_preview_area li span.cName.details{ color: {{widget.settings['c_name_text_color']}}!important;}

  .diagram_preview_area li span.sinclo-radio { display: inline-block; margin-top: {{widget.settings['btw_button_margin']}}px;}
  .diagram_preview_area li span.sinclo-text-line + span.sinclo-radio { margin-top: {{widget.settings['line_button_margin']}}px; }
  .diagram_preview_area li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; margin: 0; padding: 0 0 0 {{widget.re_text_size+7}}px; color:{{widget.settings['re_text_color']}}; min-height: 12px; font-size: {{widget.re_text_size}}px; }
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label:before { content: ""; vertical-align: middle; position: absolute; top: 2px; left: 0px; width: {{widget.re_text_size}}px; height: {{widget.re_text_size}}px; border: 1px solid #999; border-radius: 50%; background-color: #FFF; }
  .diagram_preview_area li span.sinclo-radio [type="radio"]:checked + label:after { content: ""; position: absolute; top: 6px; left: {{widget.radioButtonAfterLeft}}px; width: {{widget.re_text_size-6}}px; height: {{widget.re_text_size-6}}px; background: {{widget.settings['main_color']}}; border-radius: 50%; }
  .diagram_preview_area li.sinclo_re span.telno { color: {{widget.settings['re_text_color']}};}
  #tchatbotscenario_form_preview_body a:hover { color: {{widget.settings['main_color']}}; }

  .diagram_preview_area li.sinclo_re.noText { padding-top: 0px; }
  .diagram_preview_area li .sinclo-button-wrap { display: flex; justify-content: center; flex-flow: column nowrap; }
  .diagram_preview_area li .sinclo-button-wrap.noText { margin-top: 0px; height: 100%; }
  .diagram_preview_area li .sinclo-button-wrap.sideBySide { flex-flow: row nowrap; }

  .diagram_preview_area li .sinclo-button-wrap .sinclo-button { display: flex; cursor: pointer; justify-content: center; align-items: center; width: 100%; padding: 10px 15px; text-align: center;  }
  .diagram_preview_area li .sinclo-button-wrap .sinclo-button.alignLeft { text-align: left; justify-content: flex-start; }
  .diagram_preview_area li .sinclo-button-wrap .sinclo-button.alignRight { text-align: right; justify-content: flex-end; }
  .diagram_preview_area li .sinclo-button-wrap .sinclo-button.noneBorder { border-top-style: none!important; border-left-style: none!important; border-right-style: none!important; border-bottom-style: none!important; }
  .diagram_preview_area li .sinclo-button-wrap.sideBySide .sinclo-button { display: flex; align-items: center; padding: 12px; flex-basis: 0; flex-grow: 1; }

  .diagram_preview_area li .sinclo-button-wrap.sideBySide .sinclo-button:first-child { border-bottom-left-radius: 12px; }
  .diagram_preview_area li .sinclo-button-wrap.sideBySide .sinclo-button:last-child { border-bottom-right-radius: 12px; }
  .diagram_preview_area li .sinclo-button-wrap.sideBySide .sinclo-button.noText:first-child { border-radius: 12px 0 0 12px; }

  .diagram_preview_area li .sinclo-button-wrap.sideBySide .sinclo-button.noText:last-child { border-radius: 0 12px 12px 0; }

  .diagram_preview_area li .sinclo-button-wrap:not(.sideBySide) .sinclo-button.noText { height: 100%; }
  .diagram_preview_area li .sinclo-button-wrap:not(.sideBySide) .sinclo-button.noText:first-child { border-radius: 12px 12px 0 0; }
  .diagram_preview_area li .sinclo-button-wrap:not(.sideBySide) .sinclo-button.noText:only-child { border-radius: 12px }
  .diagram_preview_area li .sinclo-button-wrap:not(.sideBySide) .sinclo-button:last-child { border-bottom-left-radius: 12px; border-bottom-right-radius: 12px }

  .diagram_preview_area li select { border: 1px solid #909090; border-radius: 0; padding: 5px; height: 30px; margin-top: 9px; margin-bottom: -2px; width: 100%; word-break: break-all}
  .diagram_preview_area .grid_preview li.smallSize select { min-width: 183px;}
  .diagram_preview_area .grid_preview {display: grid; grid-template-columns: minmax(max-content, max-content) 1fr; margin-bottom: 5px; }

  /* icon css */
  .diagram_preview_area .img_wrapper {display: inline-block; width: 40px; height: 40px; padding: 0; text-align: center; border-radius: 50%; overflow: hidden; position: relative;}
  .diagram_preview_area .img_wrapper img {position: absolute; max-width: 40px; left: -100%; right: -100%; margin: auto; }
</style>
<script>
  //ここでプレビュー用ディレクティブを定義
  sincloApp.directive('previewText', function() {
    return {
      restrict: 'E',
      replace: true,
      template: '<div ng-show="text" ng-class="{grid_preview: ,arrowUp: ,}   checkClass.handler(\'grid_preview,arrowUp,arrowBottom\')">' +
          '<div ng-if="widget.settings[\'show_chatbot_icon\'] == 1" class="iconDiv" ng-class="checkClass.handler(\'arrowUp,arrowBottom\')">' +
          '<div ng-if="widget.isBotIconImg" class="img_wrapper">' +
          '<img ng-src="{{widget.settings[\'chatbot_icon\']}}" alt="無人対応アイコンに設定している画像">' +
          '</div>' +
          '<i ng-if="widget.isBotIconIcon" ng-class=checkClass(\'icon_border\');" class="fal {{widget.settings[\'chatbot_icon\']}}"></i>' +
          '</div>' +
          //'<li class="sinclo_re chat_left details" ng-class="checkClass.handler(\'notNone,boxType,balloonType,middleSize,largeSize,customSize\')">' +
          '<li class="sinclo_re chat_left details" ng-class="{notNone: false,boxType: true,balloonType: false,middleSize: widget.isMiddleSize(),largeSize: false,customSize: false}">' +
          '<span ng-if="widget.settings[\'show_automessage_name\'] == 1" class="cName details">{{widget.settings["sub_title"]}}</span>' +
          '<span class="details">{{text}}</span>' +
          '</li>' +
          '</div>'
    }
  }).directive('previewBranch', function() {
    return {
      restrict: 'E',
      replace: true,
      template: '<div ng-show="branchText" ng-class="checkClass.handler(\'grid_preview,arrowUp,arrowBottom\')">' +
          '<div ng-if="widget.settings[\'show_chatbot_icon\'] == 1" class="iconDiv" ng-class="checkClass.handler(\'arrowUp,arrowBottom\')">' +
          '<div ng-if="widget.isBotIconImg" class="img_wrapper">' +
          '<img ng-src="{{widget.settings[\'chatbot_icon\']}}" alt="無人対応アイコンに設定している画像">' +
          '</div>' +
          '<i ng-if="widget.isBotIconIcon" ng-class=checkClass(\'icon_border\');" class="fal {{widget.settings[\'chatbot_icon\']}}"></i>' +
          '</div>' +
          '<li ng-show="branchText" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster(\'notNone,boxType,balloonType,middleSize,largeSize,customSize\')">' +
          '<span ng-if="widget.settings[\'show_automessage_name\'] === \'1\'" class="cName details">{{widget.settings[\'sub_title\']}}</span>' +
          '<span id="action{{setActionId}}-{{index}}_message" class="details">' +
          '<span class="sinclo-text-line" ng-if="!hearings.message"></span>' +
          '</span>' +
          '<div ng-class="{noneText: !branchText, hasText: branchText}">' +
          '<button ng-repeat="value in branchSelectionList track by $index" class="sinclo-button-ui" ng-show="value"' +
          'ng-class="checkClass.handler(\'tal,tac,tar,noneBorder,hasBorder\')" onclick="return false;" >{{value}}</button>' +
          '</div>' +
          '</li>' +
          '</div>'
    }
  });

</script>

