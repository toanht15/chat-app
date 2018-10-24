<style>
#tchatbotscenario_form_preview_body { background-color: {{widget.settings['chat_talk_background_color']}}; }
#tchatbotscenario_form_preview_body .actionTitle { margin: 0; background-color: #FFFFFF;}
#tchatbotscenario_form_preview_body .actionTitle.active { color: {{widget.settings['string_color']}}; background-color: {{widget.settings['main_color']}};}

#tchatbotscenario_form_preview_body .chatTalk { width: 100%; padding: 5px 5px 10px 5px; list-style-type: none; margin: 0px}
#tchatbotscenario_form_preview_body .chatTalk li { border-radius: 5px; background-color: #FFF; margin: 10px 0 0; padding: 12px; font-size: 12px; line-height: 1.4; white-space: pre; color: {{widget.settings['message_text_color']}}; }
#tchatbotscenario_form_preview_body .chatTalk li { word-break: break-all; white-space: pre-wrap; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left { border-radius: 12px 12px 12px 0; margin-left: 10px; margin-right: 17.5px; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left.middleSize { border-radius: 12px 12px 12px 0; margin-left: 10px; margin-right: 21px; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left.largeSize { border-radius: 12px 12px 12px 0; margin-left: 10px; margin-right: 24.6px; }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re {  background-color: {{widget.makeFaintColor()}}; font-size: {{widget.settings['re_text_size']}}px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re span.details{ color: {{widget.settings['re_text_color']}}; font-size: {{widget.settings['re_text_size']}}px;}
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re span.sinclo-text-line{ display: inline-block; color: {{widget.settings['re_text_color']}}; font-size: {{widget.settings['re_text_size']}}px;}
#tchatbotscenario_form_preview_body .sinclo_re a { color: {{widget.settings['re_text_color']}}; background-color: {{widget.makeFaintColor()}};}
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.notNone { border: 1px solid {{widget.getTalkBorderColor('re')}}; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 15px; border-bottom-left-radius: 0px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; z-index: 2; border: 5px solid transparent; border-right: 5px solid {{widget.makeFaintColor()}}; border-bottom: 5px solid {{widget.makeFaintColor()}}; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; z-index: 1; border: 5px solid transparent; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{widget.getTalkBorderColor('re')}}; border-bottom: 5px solid {{widget.getTalkBorderColor('re')}}; }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType.chat_left { margin-right: 17.5px; }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType.chat_left.middleSize { margin-right: 21px; }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType.chat_left.largeSize { margin-right: 24.6px; }

#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.recv_file_left, #tchatbotscenario_form_preview_body .chatTalk li.sinclo_se.recv_file_right { display: block; padding: 10px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.recv_file_left.middleSize, #tchatbotscenario_form_preview_body .chatTalk li.sinclo_se.recv_file_right.middleSize { display: block; padding: 12px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.recv_file_left.largeSize, #tchatbotscenario_form_preview_body .chatTalk li.sinclo_se.recv_file_right.largeSize { display: block; padding: 14px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent { border: 1px dashed {{widget.settings['re_text_color']}}; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent a.select-file-button { display: inline-block; width: 75%; padding: 5px 35px; border-radius: 0; text-decoration: none; cursor: pointer; margin: 0 auto; text-align: center; background-color: {{widget.settings['re_text_color']}}!important; color: {{widget.settings['re_background_color']}}; font-weight: normal; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent a.select-file-button:hover { opacity: .8; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.cancelReceiveFileArea { margin-top: 5px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.cancelReceiveFileArea a { font-size: {{widget.re_text_size-1}}px; cursor: pointer; text-decoration: underline; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p { margin: 9px 0; text-align: center; color: {{widget.settings['re_text_color']}}; font-weight: bold; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.middleSize { margin: 13px 0; text-align: center; color: {{widget.settings['re_text_color']}}; font-weight: bold; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.largeSize { margin: 13px 0; text-align: center; color: {{widget.settings['re_text_color']}}; font-weight: bold; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.dropFileMessage { line-height: 20px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.middleSize.dropFileMessage { line-height: 24px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.largeSize.dropFileMessage { line-height: 24px; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re div.receiveFileContent div.selectFileArea p.drop-area-icon i { line-height: 1; font-size: 3em; color: {{widget.settings['re_text_color']}}; }

#tchatbotscenario_form_preview_body .chatTalk li span.cName { display: block; color: {{widget.settings['main_color']}}!important; font-weight: bold; font-size: {{widget.settings['re_text_size']}}px; margin: 0 0 5px 0; }
#tchatbotscenario_form_preview_body .chatTalk li span.cName.details{ color: {{widget.settings['c_name_text_color']}}!important;}

#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio { display: inline-block; margin-top: {{widget.settings['btw_button_margin']}}px; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-text-line + span.sinclo-radio { margin-top: {{widget.settings['line_button_margin']}}px; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] { display: none; -webkit-appearance: radio!important; -moz-appearance: radio!important; appearance: radio!important; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] + label { position: relative; display: inline-block; width: 100%; cursor: pointer; margin: 0; padding: 0 0 0 {{widget.re_text_size+7}}px; color:{{widget.settings['re_text_color']}}; min-height: 12px; font-size: {{widget.re_text_size}}px; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] + label:before { content: ""; vertical-align: middle; position: absolute; top: {{widget.radioButtonBeforeTop}}px; left: 0px; margin-top: -{{widget.radioButtonBeforeTop}}px; width: {{widget.re_text_size}}px; height: {{widget.re_text_size}}px; border: 0.5px solid #999; border-radius: 50%; background-color: #FFF; }
#tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"]:checked + label:after { content: ""; position: absolute; top: {{widget.radioButtonAfterTop}}px; left: {{widget.radioButtonAfterLeft}}px;; margin-top: -{{widget.radioButtonAfterMarginTop}}px; width: {{widget.re_text_size-7}}px; height: {{widget.re_text_size-7}}px; background: {{widget.settings['main_color']}}; border-radius: 50%; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re span.telno { color: {{widget.settings['re_text_color']}};}
#tchatbotscenario_form_preview_body a:hover { color: {{widget.settings['main_color']}}; }

#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent { display: table; table-layout: fixed; width: 100%; height: 64px; white-space: pre-line; margin-bottom: 0; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileThumbnailArea { display: table-cell; width: 64px; height: 64px; border: 1px solid #d9d9d9; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileThumbnailArea::before { content: ""; height: 100%; vertical-align: middle; width: 0px; display: inline-block; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileThumbnailArea .sendFileThumbnail { text-align: center; vertical-align: middle; display: inline-block; width: 100%; height: auto; margin-left: 0; margin-bottom: 0px; margin-right: auto; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileMetaArea { display: table-cell; vertical-align: middle; margin-left: 10px; margin-bottom: 0px; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileMetaArea .data { margin-left: 1em; margin-bottom: 5px; display: block; }
#tchatbotscenario_form_preview_body .chatTalk li .sendFileContent .sendFileMetaArea .data.sendFileSize { margin-bottom: 0px; }

#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar { width: 210px; height: 250px; border-radius: 0; box-shadow: none; -webkit-box-shadow: none;}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-current-month { font-size: 14px; padding-top: 8px }
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-weekdays { width: 210px; height: 21px}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-weekdaycontainer { padding-top: 8px;}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-weekdaycontainer .flatpickr-weekday { font-size: 11px; line-height: 11px}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer { max-width: 206px; min-width: 200px;}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months { height: 32px;}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months .flatpickr-prev-month { height: 24px; padding: 7px}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months .flatpickr-next-month { height: 24px; padding: 7px}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months .flatpickr-current-month .numInputWrapper { display: none}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months .flatpickr-current-month input.cur-year { display: none}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer .flatpickr-day.disabled { color: rgba(57,57,57,0.1);}
#tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer .flatpickr-day { height: 32px; line-height: 32px; border-radius: 0; font-weight: bolder;}

#tchatbotscenario_form_preview_body .chatTalk li select { border: 1px solid #909090; border-radius: 0; padding: 5px; height: 30px; margin-top: 12px; margin-bottom: -2px; min-width: 210px; max-width: 220px}

</style>
<section ng-repeat="(setActionId, setItem) in setActionList" id="action{{setActionId}}_preview">
  <h4 class="actionTitle">{{setActionId + 1}}．{{actionList[setItem.actionType].label}}</h4>
  <ul class="chatTalk details">
    <!-- メッセージ・選択肢 -->
    <div>
      <li ng-show="setItem.message || main.visibleSelectOptionSetting(setItem)" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_message" class="details"></span></li>
    </div>
    <!-- ヒアリング -->
    <div ng-repeat="(index, hearings) in setItem.hearings">
      <style ng-if="hearings.uiType === '5'">
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month { fill: {{hearings.customDesign[5].headerTextColor.value}}}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .dayContainer .flatpickr-day.selected { background-color: {{hearings.customDesign[5].headerBackgroundColor.value}}; border: 1px solid {{hearings.customDesign[5].headerBackgroundColor.value}} !important; color: {{calendarSelectedColor}} !important;}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .dayContainer .flatpickr-day.today { border: 1px solid  {{hearings.customDesign[5].headerBackgroundColor.value}} !important;}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .dayContainer .flatpickr-day { border: 0.5px solid  {{hearings.customDesign[5].headerWeekdayBackgroundColor.value}} !important;}
      </style>

      <li ng-show="hearings.message" ng-if="hearings.uiType === '1' || hearings.uiType === '2'" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="details">{{hearings.message}}</span></li>
      <li ng-if="hearings.uiType === '5'" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="details">{{hearings.message}} <div id="action{{setActionId}}_calendar{{index}}" style="margin-top: 10px; margin-bottom: 6px"></div></span></li>
        <li ng-show="hearings.message || hearings.options[hearings.uiType][0]" ng-if="hearings.uiType === '4'" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}" style="padding-bottom: 0"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="sinclo-text-line">{{hearings.message}}</span><br><select id="action{{setActionId}}_selection{{index}}"><option class="action{{setActionId}}_selection{{index}}_option" value="">選択してください</option><option class="action{{setActionId}}_selection{{index}}_option" ng-repeat="item in hearings.options[hearings.uiType] track by $index" value="{{item}}" ng-bind="item""></option></select>
      </li>
        <li ng-show="hearings.message || hearings.options[hearings.uiType][0]" ng-if="hearings.uiType === '3'" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="sinclo-text-line" ng-bind="hearings.message"></span><div style="margin-top: -28px">
                <span ng-repeat="(optionIndex, option) in hearings.options[hearings.uiType] track by $index" class="sinclo-radio" style="display: block" ng-if="option"><input name="action{{setActionId}}_index{{index}}" id="action{{setActionId}}_index{{index}}_option{{optionIndex}}" type="radio" value="{{option}}"><label for="action{{setActionId}}_index{{index}}_option{{optionIndex}}" ng-bind="option"></label></span></div></li>
      <br><li ng-show="hearings.errorMessage" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_error_message" class="details">{{hearings.errorMessage}}</span></li>
    </div>
    <!-- 確認メッセージ -->
    <div>
      <li ng-show="setItem.isConfirm && (setItem.confirmMessage || setItem.success || setItem.cancel)" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_confirm_message" class="details"></span></li>
    </div>
    <!-- ファイル送信 -->
    <div>
      <li ng-if="setItem.file.download_url && setItem.file.file_name && setItem.file.file_size" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">ファイルが送信されました</span><div class="sendFileContent"><div class="sendFileThumbnailArea"><img ng-if="widget.isImage(setItem.file.extension)" ng-src="{{setItem.file.download_url}}" class="sendFileThumbnail" width="64" height="64"><i ng-if="!widget.isImage(setItem.file.extension)" ng-class="widget.selectIconClassFromExtension(setItem.file.extension)" class="fa fa-4x sendFileThumbnail" aria-hidden="true"></i></div><div class="sendFileMetaArea"><span class="data sendFileName details">{{setItem.file.file_name}}</span><span class="data sendFileSize details">{{setItem.file.file_size}}</span></div></div></li>
    </div>
    <!-- ファイル受信 -->
    <div>
      <li ng-show="setItem.dropAreaMessage" class="sinclo_re chat_left recv_file_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><div class="receiveFileContent"><div class="selectFileArea"><p class="dropFileMessage" ng-class="{middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}">{{setItem.dropAreaMessage}}</p><p class="drop-area-icon" ng-class="{middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><i class="fal fa-3x fa-cloud-upload"></i></p><p ng-class="{middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}">または</p><p ng-class="{middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><a class="select-file-button">ファイルを選択</a></p></div></div><div ng-if="setItem.cancelEnabled" class="cancelReceiveFileArea"><a>{{setItem.cancelLabel}}</a></div></li>
    </div>
    <!-- 条件分岐アクション・テキスト発言 -->
    <div ng-repeat="(index, condition) in setItem.conditionList">
      <li ng-show="condition.actionType == '1' && condition.action.message" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}-{{index}}_message" class="details"></span></li>
    </div>
    <!-- 条件分岐アクション・どの条件にも該当しないテキスト発言 -->
    <div>
      <li ng-show="setItem.elseEnabled && setItem.elseAction.actionType == '1' && setItem.elseAction.action.message" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_else-message" class="details"></span></li>
    </div>
  </ul>
</section>
