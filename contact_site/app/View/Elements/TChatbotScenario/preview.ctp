<style>
#tchatbotscenario_form_preview_body { background-color: {{simulator.get('chat_talk_background_color')}}; }
#tchatbotscenario_form_preview_body .actionTitle { margin: 0; background-color: #FFFFFF;}

#tchatbotscenario_form_preview_body .chatTalk { width: 100%; padding: 5px 5px 10px 5px; list-style-type: none; margin: 0px}
#tchatbotscenario_form_preview_body .chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0 0; padding: 3px; font-size: 11px; line-height: 1.4; white-space: pre; color: {{simulator.get('message_text_color')}}; }
#tchatbotscenario_form_preview_body .chatTalk li { word-break: break-all; white-space: pre-wrap; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType { word-wrap: break-word; word-break: break-all; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType { display: block; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType { display: inline-block; position: relative; padding: 5px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re {  background-color: {{simulator.makeFaintColor()}}; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re span.details{ color: {{simulator.get('re_text_color')}};}
#tchatbotscenario_form_preview_body .sinclo_re a { color: {{simulator.get('re_text_color')}}; background-color: {{simulator.makeFaintColor()}};}
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.notNone { border: 1px solid {{simulator.getTalkBorderColor('re')}}; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; z-index: 2; border: 5px solid transparent; border-right: 5px solid {{simulator.makeFaintColor()}}; border-bottom: 5px solid {{simulator.makeFaintColor()}}; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; z-index: 1; border: 5px solid transparent; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{simulator.getTalkBorderColor('re')}}; border-bottom: 5px solid {{simulator.getTalkBorderColor('re')}}; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType.chat_left { margin-right: 10px }

#tchatbotscenario_form_preview_body .chatTalk li span.cName { display: block; color: {{simulator.get('main_color')}}!important; font-weight: bold; font-size: 13px; margin: 0 0 5px 0; }
#tchatbotscenario_form_preview_body .chatTalk li span.cName.details{ color: {{simulator.get('c_name_text_color')}}!important;}

#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; padding: 0 0 0 15px; color:{{simulator.get('re_text_color')}}; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] + label:before { content: ""; display: block; position: absolute; top: 1px; left: 0px; width: 11px; height: 11px; border: 1px solid #999; border-radius: 50%; background-color: #FFF; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"]:checked + label:after { content: ""; display: block; position: absolute; top: 4px; left: 3px; width: 7px; height: 7px; background: {{simulator.get('main_color')}}; border-radius: 50%; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re span.telno { color: {{simulator.get('re_text_color')}};}
#tchatbotscenario_form_preview_body a:hover { color: {{simulator.get('main_color')}}; }

</style>
<section ng-repeat="(setActionId, setItem) in setActionList" id="action{{setActionId}}_preview">
  <h4 class="actionTitle"><a href="#action{{setActionId}}_setting">{{setActionId + 1}}．{{setItem.label}}</a></h4>
  <ul class="chatTalk details">
    <!-- メッセージ・選択肢 -->
    <div>
      <li ng-show="setItem.message || main.visibleSelectOptionSetting(setItem)" class="sinclo_re chat_left details" ng-class="{boxType: simulator.get('chat_message_design_type') == 1, balloonType: simulator.get('chat_message_design_type') == 2}"><span class="cName details">{{simulator.get('sub_title')}}</span><span id="action{{setActionId}}_message" class="details"></span></li>
    </div>
    <!-- ヒアリング -->
    <div ng-repeat="(index, hearings) in setItem.hearings">
      <li ng-show="hearings.message" class="sinclo_re chat_left details" ng-class="{boxType: simulator.get('chat_message_design_type') == 1, balloonType: simulator.get('chat_message_design_type') == 2}"><span class="cName details">{{simulator.get('sub_title')}}</span><span class="details">{{hearings.message}}</span></li>
    </div>
    <!-- エラーメッセージ -->
    <div>
      <li ng-show="setItem.errorMessage" class="sinclo_re chat_left details" ng-class="{boxType: simulator.get('chat_message_design_type') == 1, balloonType: simulator.get('chat_message_design_type') == 2}"><span class="cName details">{{simulator.get('sub_title')}}</span><span id="action{{setActionId}}_error_message" class="details"></span></li>
    </div>
    <!-- 確認メッセージ -->
    <div>
      <li ng-show="setItem.isConfirm && (setItem.confirmMessage || setItem.success || setItem.cancel)" class="sinclo_re chat_left details" ng-class="{boxType: simulator.get('chat_message_design_type') == 1, balloonType: simulator.get('chat_message_design_type') == 2}"><span class="cName details">{{simulator.get('sub_title')}}</span><span id="action{{setActionId}}_confirm_message" class="details"></span></li>
    </div>
  </ul>
</section>
