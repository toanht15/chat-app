<style>
#tchatbotscenario_form_preview_body { background-color: {{widget.settings['chat_talk_background_color']}}; }
#tchatbotscenario_form_preview_body .actionTitle { margin: 0; background-color: #FFFFFF;}
#tchatbotscenario_form_preview_body .actionTitle.active { color: {{widget.settings['string_color']}}; background-color: {{widget.settings['main_color']}};}

#tchatbotscenario_form_preview_body .chatTalk { width: 100%; padding: 5px 5px 10px 5px; list-style-type: none; margin: 0px}
#tchatbotscenario_form_preview_body .chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0 0; padding: 10px; font-size: 12px; line-height: 1.4; white-space: pre; color: {{widget.settings['message_text_color']}}; }
#tchatbotscenario_form_preview_body .chatTalk li { word-break: break-all; white-space: pre-wrap; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType { word-wrap: break-word; word-break: break-all; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType { display: block; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left { border-radius: 12px 12px 12px 0; margin-left: 10px; width: 80%; margin-right: auto; }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re {  background-color: {{widget.makeFaintColor()}}; font-size: {{widget.settings['re_text_size']}}px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re span.details{ color: {{widget.settings['re_text_color']}}; font-size: {{widget.settings['re_text_size']}}px;}
#tchatbotscenario_form_preview_body .sinclo_re a { color: {{widget.settings['re_text_color']}}; background-color: {{widget.makeFaintColor()}};}
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.notNone { border: 1px solid {{widget.getTalkBorderColor('re')}}; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; z-index: 2; border: 5px solid transparent; border-right: 5px solid {{widget.makeFaintColor()}}; border-bottom: 5px solid {{widget.makeFaintColor()}}; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; z-index: 1; border: 5px solid transparent; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{widget.getTalkBorderColor('re')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('re')}}; }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType.chat_left { margin-right: 10px }

#tchatbotscenario_form_preview_body .chatTalk li span.cName { display: block; color: {{widget.settings['main_color']}}!important; font-weight: bold; font-size: 13px; margin: 0 0 5px 0; }
#tchatbotscenario_form_preview_body .chatTalk li span.cName.details{ color: {{widget.settings['c_name_text_color']}}!important;}

#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; padding: 0 0 0 {{widget.re_text_size+4}}px; color:{{widget.settings['re_text_color']}}; min-height: 12px; font-size: {{widget.re_text_size}}px; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] + label:before { content: ""; vertical-align: middle; position: absolute; top: {{widget.radioButtonBeforeTop}}px; left: 0px; margin-top: -{{widget.radioButtonBeforeTop}}px; width: {{widget.re_text_size}}px; height: {{widget.re_text_size}}px; border: 1px solid {{widget.settings['re_text_color']}}; border-radius: 50%; background-color: widget.getTalkBorderColor('re'); }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"]:checked + label:after { content: ""; position: absolute; top: {{widget.radioButtonAfterTop}}px; left: {{widget.radioButtonAfterLeft}}px;; margin-top: -{{widget.radioButtonAfterMarginTop}}px; width: {{widget.re_text_size-6}}px; height: {{widget.re_text_size-6}}px; background: {{widget.settings['main_color']}}; border-radius: 50%; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re span.telno { color: {{widget.settings['re_text_color']}};}
#tchatbotscenario_form_preview_body a:hover { color: {{widget.settings['main_color']}}; }

#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent { display: table; table-layout: fixed; width: 100%; height: 64px; white-space: pre-line; margin-bottom: 0; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileThumbnailArea { display: table-cell; width: 64px; height: 64px; border: 1px solid #d9d9d9; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileThumbnailArea::before { content: ""; height: 100%; vertical-align: middle; width: 0px; display: inline-block; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileThumbnailArea .sendFileThumbnail { text-align: center; vertical-align: middle; display: inline-block; width: 100%; height: auto; margin-left: 0; margin-bottom: 0px; margin-right: auto; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileMetaArea { display: table-cell; vertical-align: middle; margin-left: 10px; margin-bottom: 0px; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileMetaArea .data { margin-left: 1em; margin-bottom: 5px; display: block; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileMetaArea .data.sendFileSize { margin-bottom: 0px; }

</style>
<section ng-repeat="(setActionId, setItem) in setActionList" id="action{{setActionId}}_preview">
  <h4 class="actionTitle">{{setActionId + 1}}．{{actionList[setItem.actionType].label}}</h4>
  <ul class="chatTalk details">
    <!-- メッセージ・選択肢 -->
    <div>
      <li ng-show="setItem.message || main.visibleSelectOptionSetting(setItem)" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_message" class="details"></span></li>
    </div>
    <!-- ヒアリング -->
    <div ng-repeat="(index, hearings) in setItem.hearings">
      <li ng-show="hearings.message" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="details">{{hearings.message}}</span></li>
    </div>
    <!-- エラーメッセージ -->
    <div>
      <li ng-show="setItem.errorMessage" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_error_message" class="details"></span></li>
    </div>
    <!-- 確認メッセージ -->
    <div>
      <li ng-show="setItem.isConfirm && (setItem.confirmMessage || setItem.success || setItem.cancel)" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_confirm_message" class="details"></span></li>
    </div>
    <!-- ファイル送信 -->
    <div>
      <li ng-if="setItem.file.download_url && setItem.file.file_name && setItem.file.file_size" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">ファイルが送信されました</span><div class="sendFileContent"><div class="sendFileThumbnailArea"><img ng-if="widget.isImage(setItem.file.extension)" ng-src="{{setItem.file.download_url}}" class="sendFileThumbnail" width="64" height="64"><i ng-if="!widget.isImage(setItem.file.extension)" ng-class="widget.selectIconClassFromExtension(setItem.file.extension)" class="fa fa-4x sendFileThumbnail" aria-hidden="true"></i></div><div class="sendFileMetaArea"><span class="data sendFileName details">{{setItem.file.file_name}}</span><span class="data sendFileSize details">{{setItem.file.file_size}}</span></div></div></li>
    </div>
  </ul>
</section>
