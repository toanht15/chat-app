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
  #popup #popup-frame-base #popup-frame.p_diagrams_branch {width: 785px}
  #popup #popup-frame-base #popup-frame.p_diagrams_text #text_modal #text_modal_preview {width: 285px}
  #popup #popup-frame-base #popup-frame.p_diagrams_branch #branch_modal #branch_modal_preview {width: 285px}
</style>
<style ng-if="widget.settings['widget_size_type'] == 2">
  #popup #popup-frame-base #popup-frame.p_diagrams_text {width: 842px}
  #popup #popup-frame-base #popup-frame.p_diagrams_branch {width: 842px}
  #popup #popup-frame-base #popup-frame.p_diagrams_text #text_modal #text_modal_preview {width: 342px}
  #popup #popup-frame-base #popup-frame.p_diagrams_branch #branch_modal #branch_modal_preview {width: 342px}
</style>
<style ng-if="widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4">
  #popup #popup-frame-base #popup-frame.p_diagrams_text {width: 898px}
  #popup #popup-frame-base #popup-frame.p_diagrams_branch {width: 898px}
  #popup #popup-frame-base #popup-frame.p_diagrams_text #text_modal #text_modal_preview {width: 398px}
  #popup #popup-frame-base #popup-frame.p_diagrams_branch #branch_modal #branch_modal_preview {width: 398px}
</style>
<style ng-if="widget.settings['widget_size_type'] == 5">
  #popup #popup-frame-base #popup-frame.p_diagrams_text {min-width: 785px; max-width: 898px; width: calc(500px + {{widget.settings['widget_custom_width']}}px)}
  #popup #popup-frame-base #popup-frame.p_diagrams_branch {min-width: 785px; max-width: 898px; width: calc(500px + {{widget.settings['widget_custom_width']}}px)}
  #popup #popup-frame-base #popup-frame.p_diagrams_text #text_modal #text_modal_preview {width: {{widget.settings['widget_custom_width']}}px}
  #popup #popup-frame-base #popup-frame.p_diagrams_branch #branch_modal #branch_modal_preview {width: {{widget.settings['widget_custom_width']}}px}
</style>
<style>
  .diagram_preview_area { width: 100%; list-style-type: none; margin: 0; overflow-y: auto; font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica,Helvetica Neue,Arial,Verdana;}
  .diagram_preview_area { background-color: {{widget.settings['chat_talk_background_color']}}; height: calc(100% - 66px); padding: 0px 5px 0px 5px; }
  .diagram_preview_area .iconDiv {display: flex}
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
  .diagram_preview_area li.sinclo_re.customWidth { width: 90%; }
  .diagram_preview_area li.sinclo_re span.details{ color: {{widget.settings['re_text_color']}}; font-size: {{widget.settings['re_text_size']}}px;}
  .diagram_preview_area li.sinclo_re span.sinclo-text-line{ display: inline-block; color: {{widget.settings['re_text_color']}}; font-size: {{widget.settings['re_text_size']}}px;}
  .diagram_preview_area li.sinclo_re span.sinclo-text-line.between{ margin-top:{{radioSelectionDistance}}px }
  .diagram_preview_area li.sinclo_re a { color: {{widget.settings['re_text_color']}}; background-color: {{widget.makeFaintColor()}};}
  .diagram_preview_area li.sinclo_re.notNone { border: 1px solid {{widget.getTalkBorderColor('re')}}; }
  .diagram_preview_area li.sinclo_re.balloonType:not(.no-wrap) { margin-left: 10px; padding-right: 15px; border-bottom-left-radius: 0px; }
  .diagram_preview_area li.sinclo_re.balloonType:not(.no-wrap):before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; z-index: 2; border: 5px solid transparent; border-right: 5px solid {{widget.makeFaintColor()}}; border-bottom: 5px solid {{widget.makeFaintColor()}}; }
  .diagram_preview_area li.sinclo_re.balloonType:not(.no-wrap):after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; z-index: 1; border: 5px solid transparent; }
  .diagram_preview_area li.sinclo_re.balloonType.notNone:not(.no-wrap):after { border-right: 5px solid {{widget.getTalkBorderColor('re')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('re')}}; }
  .diagram_preview_area li.balloonType.chat_left { margin-right: 17.5px; }
  .diagram_preview_area li.balloonType.chat_left.middleSize { margin-right: 21px; }
  .diagram_preview_area li.balloonType.chat_left.largeSize { margin-right: 24.6px; }

  .diagram_previwe_area li img { display: block; }
  .diagram_preview_area li img.smallSizeImg { max-width:165px; max-height:120px; }
  .diagram_preview_area li img.middleSizeImg { max-width:215px; max-height:188px; }
  .diagram_preview_area li img.largeSizeImg { max-width:265px; max-height:285px; }


  .diagram_preview_area li span.cName { display: block; color: {{widget.settings['main_color']}}!important; font-weight: bold; font-size: {{widget.settings['re_text_size']}}px; margin: 0 0 5px 0; }
  .diagram_preview_area li span.cName.details{ color: {{widget.settings['c_name_text_color']}}!important;}

  .diagram_preview_area li span.sinclo-radio { display: inline-block; margin-top: {{widget.settings['btw_button_margin']}}px;}
  .diagram_preview_area li span.sinclo-text-line + span.sinclo-radio { margin-top: {{widget.settings['line_button_margin']}}px; }
  .diagram_preview_area li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; margin: 0; padding: 0 0 0 {{widget.re_text_size+7}}px; color:{{widget.settings['re_text_color']}}; min-height: 12px; font-size: {{widget.re_text_size}}px; }
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label:before { content: ""; vertical-align: middle; position: absolute; top: 2px; left: 0px; width: {{widget.re_text_size}}px; height: {{widget.re_text_size}}px; border: 1px solid {{widget.settings['main_color']}}; border-radius: 50%; background-color: #FFF; }
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

  .diagram_preview_area li button.btn_top_radius {border-top-left-radius: 8px; border-top-right-radius: 8px}
  .diagram_preview_area li button.btn_bottom_radius {border-bottom-left-radius: 8px; border-bottom-right-radius: 8px}

  .diagram_preview_area li select { border: 1px solid #909090; border-radius: 0; padding: 5px; height: 30px; margin-top: 9px; margin-bottom: -2px; width: 100%; word-break: break-all}
  .diagram_preview_area .grid_preview li.smallSize select { min-width: 183px;}
  .diagram_preview_area .grid_preview {display: grid; grid-template-columns: minmax(max-content, max-content) 1fr 7px; margin-bottom: 5px; }

  /* icon css */
  .diagram_preview_area .img_wrapper {display: inline-block; width: 40px; height: 40px; padding: 0; text-align: center; border-radius: 50%; overflow: hidden; position: relative;}
  .diagram_preview_area .img_wrapper img {position: absolute; max-width: 40px; left: -100%; right: -100%; margin: auto; }
</style>


<style ng-if="!isCustomize">
  /* Default selection UI */
  .diagram_preview_area li button {width: 188px; background-color: {{widget.settings.re_text_color}};  color: {{widget.settings.re_background_color}}; cursor: pointer;  min-height: 35px; margin-bottom: 1px;  padding: 10px 15px; border: none; text-align: center; }
  .diagram_preview_area li.middleSize button { width: 240px;}
  .diagram_preview_area li.largeSize button { width: 280px;}
  .diagram_preview_area li button:active{background-color: {{getRawColor(widget.settings.main_color, 0.5)}};}
  .diagram_preview_area li button:focus{outline: none}
  .diagram_preview_area li button:hover{background-color: {{getRawColor(widget.settings.main_color, 0.5)}};}
  .diagram_preview_area li div.hasText {margin-top: 8px}
</style>

<style ng-if="radioStyle == 1">
  /* custom radio (type button) design */
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label.hasBackground {background-color: {{radioEntireBackgroundColor}}; padding: 8px 8px 8px 28px; color: {{radioTextColor}}}
  .diagram_preview_area li span.sinclo-radio [type="radio"]:checked ~ label {background-color:{{radioEntireActiveColor}}; color:{{radioActiveTextColor}};}
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label:before {top: 9px!important; left: 8px!important;}
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label:after {top: 13px!important; left: 12px!important;}
</style>
<style ng-if="isCustomize">
  /* Custom selection UI */

  /* button UI */
  .diagram_preview_area li button {background-color: {{buttonUIBackgroundColor}};  color: {{buttonUITextColor}};  cursor: pointer;  min-height: 35px;  margin-bottom: 1px;  padding: 10px 15px;  }
  .diagram_preview_area li button {width: 188px;}
  .diagram_preview_area li.middleSize button { width: 240px;}
  .diagram_preview_area li.largeSize button { width: 280px;}
  .diagram_preview_area li button:active{background-color: {{buttonUIActiveColor}};}
  .diagram_preview_area li button:focus{outline: none}
  .diagram_preview_area li button:hover{background-color: {{buttonUIActiveColor}};}
  .diagram_preview_area li button.noneBorder {border: none;}
  .diagram_preview_area li button.hasBorder {border: 1px solid {{buttonUIBorderColor}};}
  .diagram_preview_area li button.tal {text-align: left;}
  .diagram_preview_area li button.tac {text-align: center;}
  .diagram_preview_area li button.tar {text-align: right;}
  .diagram_preview_area li div.hasText {margin-top: 8px}

  /* radio button UI */
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label:before { background-color: {{radioBackgroundColor}}!important; border-color: {{radioNoneBorder ? 'transparent' : radioBorderColor}}!important}
  .diagram_preview_area li span.sinclo-radio [type="radio"] + label:after { background-color: {{radioActiveColor}}!important}
  .diagram_preview_area li span.sinclo-radio {margin-top: {{radioSelectionDistance}}px}
  .diagram_preview_area li span.sinclo-radio:first-child {margin-top: 4px;}

</style>
<script>
  //ここでプレビュー用ディレクティブを定義
  sincloApp.directive('previewText', function() {
    return {
      restrict: 'E',
      replace: true,
      template: '<div ng-show="text" ng-class="{' +
          'grid_preview: widget.settings[\'show_chatbot_icon\'] == 1,' +
          'arrowUp: widget.settings[\'chat_message_design_type\'] == 1 &&  widget.settings[\'chat_message_arrow_position\'] == 1,' +
          'arrowBottom: widget.settings[\'chat_message_design_type\'] == 2 || widget.settings[\'chat_message_arrow_position\'] == 2' +
          '}">' +
          '<div ng-if="widget.settings[\'show_chatbot_icon\'] == 1" class="iconDiv" ng-class="{' +
          'arrowUp: widget.settings[\'chat_message_design_type\'] == 1 &&  widget.settings[\'chat_message_arrow_position\'] == 1,' +
          'arrowBottom: widget.settings[\'chat_message_design_type\'] == 2 || widget.settings[\'chat_message_arrow_position\'] == 2' +
          '}">' +
          '<div ng-if="widget.isBotIconImg" class="img_wrapper">' +
          '<img ng-src="{{widget.settings[\'chatbot_icon\']}}" alt="無人対応アイコンに設定している画像">' +
          '</div>' +
          '<i ng-if="widget.isBotIconIcon" class="fal {{widget.settings[\'chatbot_icon\']}}" ng-class="{' +
          'icon_border: false' +
          '}"></i>' +
          '</div>' +
          '<li class="sinclo_re chat_left details" ng-class="{' +
          'notNone: widget.re_border_none === \'\' || widget.re_border_none === false,' +
          'boxType: widget.settings[\'chat_message_design_type\'] == 1,' +
          'balloonType: widget.settings[\'chat_message_design_type\'] == 2,' +
          'middleSize: widget.settings[\'widget_size_type\'] == 2,' +
          'largeSize: widget.settings[\'widget_size_type\'] == 3 || widget.settings[\'widget_size_type\'] == 4,' +
          'customSize: widget.settings[\'widget_size_type\'] == 5' +
          '}">' +
          '<span ng-if="widget.settings[\'show_automessage_name\'] == 1" class="cName details">{{widget.settings["sub_title"]}}</span>' +
          '<span class="details preview_text_span_{{$index}}"></span>' +
          '</li>' +
          '</div>'
    }
  }).directive('previewBranch', function() {
    return {
      restrict: 'E',
      replace: true,
      template: '<div ng-show="branchText || branchSelectionList[0].value" ng-class="{' +
          'grid_preview: widget.settings[\'show_chatbot_icon\'] == 1,' +
          'arrowUp: widget.settings[\'chat_message_design_type\'] == 1 &&  widget.settings[\'chat_message_arrow_position\'] == 1,' +
          'arrowBottom: widget.settings[\'chat_message_design_type\'] == 2 || widget.settings[\'chat_message_arrow_position\'] == 2' +
          '}">' +
          '<div ng-if="widget.settings[\'show_chatbot_icon\'] == 1" class="iconDiv" ng-class="{' +
          'arrowUp: widget.settings[\'chat_message_design_type\'] == 1 &&  widget.settings[\'chat_message_arrow_position\'] == 1,' +
          'arrowBottom: widget.settings[\'chat_message_design_type\'] == 2 || widget.settings[\'chat_message_arrow_position\'] == 2' +
          '}">' +
          '<div ng-if="widget.isBotIconImg" class="img_wrapper">' +
          '<img ng-src="{{widget.settings[\'chatbot_icon\']}}" alt="無人対応アイコンに設定している画像">' +
          '</div>' +
          '<i ng-if="widget.isBotIconIcon" class="fal {{widget.settings[\'chatbot_icon\']}}" ng-class="{' +
          'icon_border: false' +
          '}"></i>' +
          '</div>' +
          '<li ng-show="branchText || branchSelectionList[0]" class="sinclo_re chat_left details" ng-class="{' +
          'notNone: widget.re_border_none === \'\' || widget.re_border_none === false,' +
          'boxType: widget.settings[\'chat_message_design_type\'] == 1,' +
          'balloonType: widget.settings[\'chat_message_design_type\'] == 2,' +
          'middleSize: widget.settings[\'widget_size_type\'] == 2,' +
          'largeSize: widget.settings[\'widget_size_type\'] == 3 || widget.settings[\'widget_size_type\'] == 4,' +
          'customSize: widget.settings[\'widget_size_type\'] == 5,' +
          'customWidth: radioStyle == \'1\'' +
          '}">' +
          '<span ng-if="widget.settings[\'show_automessage_name\'] === \'1\'" class="cName details">{{widget.settings[\'sub_title\']}}</span>' +
          '<span class="details preview_text_span_branch">' +
          '</span>' +
          '<div ng-if="branchType == 1">' +
          '' +
          '<span ng-repeat="content in branchSelectionList track by $index" style="display: block" ng-if="content.value"' +
          'ng-class="{' +
          '\'sinclo-radio\': content.type == \'1\'' +
          '}" finisher>' +
          '<div ng-if="content.type == 1">' +
          '<input name="radio_button" id="radio_{{$index}}" type="radio">' +
          '<label for="radio_{{$index}}" ng-class="{' +
          'noneBackground: radioStyle === \'2\',' +
          'hasBackground: radioStyle === \'1\'' +
          '}">{{content.value}}</label>' +
          '</div>' +
          '<span class="sinclo-text-line between" ng-if="content.type == 2">' +
          '{{content.value}}' +
          '</span>' +
          '</span>' +
          '' +
          '</div>' +
          '<div id="button_component" ng-class="{noneText: !branchText, hasText: branchText}" ng-if="branchType == 2">' +
          '<div ng-repeat="content in branchSelectionList track by $index" ng-if="content.value">' +
          '<button class="sinclo-button-ui" ng-if="content.type == 1"' +
          'ng-class="{' +
          'tal: buttonUITextAlign == 1,' +
          'tac: buttonUITextAlign == 2,' +
          'tar: buttonUITextAlign == 3,' +
          'noneBorder: outButtonUINoneBorder,' +
          'hasBorder: !outButtonUINoneBorder' +
          '}" onclick="return false;" finisher>{{content.value}}</button>' +
          '<span class="sinclo-text-line between" ng-if="content.type == 2">' +
          '{{content.value}}' +
          '</span>' +
          '</div>' +
          '</div>' +
          '</li>' +
          '</div>'
    }
  });

</script>

