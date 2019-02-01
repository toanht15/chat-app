<style>
  #tchatbotscenario_form_preview.customSize { flex-basis: {{widget_custom_width}}px; max-width: 400px; min-width:285px;  }
  #tchatbotscenario_form_preview_body { background-color: {{widget.settings['chat_talk_background_color']}}; }
  #tchatbotscenario_form_preview_body .actionTitle { margin: 0; background-color: #FFFFFF;}
  #tchatbotscenario_form_preview_body .actionTitle.active { color: {{widget.settings['string_color']}}; background-color: {{widget.settings['main_color']}};}

#tchatbotscenario_form_preview_body .chatTalk { width: 100%; padding: 5px 5px 10px 5px; list-style-type: none; margin: 0px}
#tchatbotscenario_form_preview_body .chatTalk .iconDiv > i { display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; font-size: 23px; color: {{widget.settings['string_color']}}; border-radius: 50%; background-color: {{widget.settings['main_color']}}}
#tchatbotscenario_form_preview_body .chatTalk .iconDiv > i.icon_border { border: 1px solid {{widget.settings['string_color']}};}
#tchatbotscenario_form_preview_body .chatTalk li { border-radius: 5px; background-color: #FFF; margin: 10px 0 0; padding: 12px; font-size: 12px; line-height: 1.4; white-space: pre; color: {{widget.settings['message_text_color']}}; }
#tchatbotscenario_form_preview_body .chatTalk li { word-break: break-all; white-space: pre-wrap; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left { border-radius: 12px 12px 12px 0; margin-left: 10px; margin-right: 17.5px; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left.middleSize { border-radius: 12px 12px 12px 0; margin-left: 10px; margin-right: 21px; }
#tchatbotscenario_form_preview_body .chatTalk li.boxType.chat_left.largeSize { border-radius: 12px 12px 12px 0; margin-left: 10px; margin-right: 24.6px; }
#tchatbotscenario_form_preview_body .chatTalk li.balloonType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
#tchatbotscenario_form_preview_body .chatTalk li.no-wrap { display: block!important; padding: 10px 0 0 0!important; }
#tchatbotscenario_form_preview_body .chatTalk li.all-round { border-radius: 12px!important; }
#tchatbotscenario_form_preview_body .chatTalk li.sinclo_re.no-wrap span.sinclo-text-line { display: block; padding: 0 15px; }
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
  #tchatbotscenario_form_preview_body .chatTalk li span.sinclo-radio [type="radio"] + label.radio-zoom:before { content: ""; vertical-align: middle; position: absolute; top: {{widget.radioButtonBeforeTop}}px; left: 0px; margin-top: -{{widget.radioButtonBeforeTop}}px; width: {{widget.re_text_size}}px; height: {{widget.re_text_size}}px; border: 1px solid #999; border-radius: 50%; background-color: #FFF; }
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

#tchatbotscenario_form_preview_body .chatTalk li .sinclo-button-wrap { display: flex; justify-content: center; margin-top: 10px; flex-wrap: wrap; }
#tchatbotscenario_form_preview_body .chatTalk li .sinclo-button-wrap.sideBySide { flex-flow: row nowrap; }
#tchatbotscenario_form_preview_body .chatTalk li .sinclo-button-wrap .sinclo-button { display: flex; cursor: pointer; justify-content: center; align-items: center; width: 100%; padding: 10px 15px; }
#tchatbotscenario_form_preview_body .chatTalk li .sinclo-button-wrap .sinclo-button.noneBorder { border-top-style: none!important; border-left-style: none!important; border-right-style: none!important; border-bottom-style: none!important; }
#tchatbotscenario_form_preview_body .chatTalk li .sinclo-button-wrap.sideBySide .sinclo-button { width: 50%; display: flex; align-items: center; padding: 12px; }
#tchatbotscenario_form_preview_body .chatTalk li .sinclo-button-wrap.sideBySide .sinclo-button:first-child { border-bottom-left-radius: 12px; }
#tchatbotscenario_form_preview_body .chatTalk li .sinclo-button-wrap.sideBySide .sinclo-button:last-child { border-bottom-right-radius: 12px; }
#tchatbotscenario_form_preview_body .chatTalk li .sinclo-button-wrap:not(.sideBySide) .sinclo-button:last-child { border-bottom-left-radius: 12px; border-bottom-right-radius: 12px };

.flatpickr-calendar{background:transparent;opacity:0;display:none;text-align:center;visibility:hidden;padding:0;-webkit-animation:none;animation:none;direction:ltr;border:0;font-size:14px;line-height:24px;border-radius:5px;position:absolute;width:307.875px;-webkit-box-sizing:border-box;box-sizing:border-box;-ms-touch-action:manipulation;touch-action:manipulation;background:#fff;-webkit-box-shadow:1px 0 0 #e6e6e6,-1px 0 0 #e6e6e6,0 1px 0 #e6e6e6,0 -1px 0 #e6e6e6,0 3px 13px rgba(0,0,0,0.08);box-shadow:1px 0 0 #e6e6e6,-1px 0 0 #e6e6e6,0 1px 0 #e6e6e6,0 -1px 0 #e6e6e6,0 3px 13px rgba(0,0,0,0.08);}.flatpickr-calendar.open,.flatpickr-calendar.inline{opacity:1;max-height:640px;visibility:visible}.flatpickr-calendar.open{display:inline-block;z-index:99999}.flatpickr-calendar.animate.open{-webkit-animation:fpFadeInDown 300ms cubic-bezier(.23,1,.32,1);animation:fpFadeInDown 300ms cubic-bezier(.23,1,.32,1)}.flatpickr-calendar.inline{display:block;position:relative;top:2px}.flatpickr-calendar.static{position:absolute;top:calc(100% + 2px);}.flatpickr-calendar.static.open{z-index:999;display:block}.flatpickr-calendar.multiMonth .flatpickr-days .dayContainer:nth-child(n+1) .flatpickr-day.inRange:nth-child(7n+7){-webkit-box-shadow:none !important;box-shadow:none !important}.flatpickr-calendar.multiMonth .flatpickr-days .dayContainer:nth-child(n+2) .flatpickr-day.inRange:nth-child(7n+1){-webkit-box-shadow:-2px 0 0 #e6e6e6,5px 0 0 #e6e6e6;box-shadow:-2px 0 0 #e6e6e6,5px 0 0 #e6e6e6}.flatpickr-calendar .hasWeeks .dayContainer,.flatpickr-calendar .hasTime .dayContainer{border-bottom:0;border-bottom-right-radius:0;border-bottom-left-radius:0}.flatpickr-calendar .hasWeeks .dayContainer{border-left:0}.flatpickr-calendar.showTimeInput.hasTime .flatpickr-time{height:40px;border-top:1px solid #e6e6e6}.flatpickr-calendar.noCalendar.hasTime .flatpickr-time{height:auto}.flatpickr-calendar:before,.flatpickr-calendar:after{position:absolute;display:block;pointer-events:none;border:solid transparent;content:'';height:0;width:0;left:22px}.flatpickr-calendar.rightMost:before,.flatpickr-calendar.rightMost:after{left:auto;right:22px}.flatpickr-calendar:before{border-width:5px;margin:0 -5px}.flatpickr-calendar:after{border-width:4px;margin:0 -4px}.flatpickr-calendar.arrowTop:before,.flatpickr-calendar.arrowTop:after{bottom:100%}.flatpickr-calendar.arrowTop:before{border-bottom-color:#e6e6e6}.flatpickr-calendar.arrowTop:after{border-bottom-color:#fff}.flatpickr-calendar.arrowBottom:before,.flatpickr-calendar.arrowBottom:after{top:100%}.flatpickr-calendar.arrowBottom:before{border-top-color:#e6e6e6}.flatpickr-calendar.arrowBottom:after{border-top-color:#fff}.flatpickr-calendar:focus{outline:0}.flatpickr-wrapper{position:relative;display:inline-block}.flatpickr-months{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;}.flatpickr-months .flatpickr-month{background:transparent;color:rgba(0,0,0,0.9);fill:rgba(0,0,0,0.9);height:28px;line-height:1;text-align:center;position:relative;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;overflow:hidden;-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1}.flatpickr-months .flatpickr-prev-month,.flatpickr-months .flatpickr-next-month{text-decoration:none;cursor:pointer;position:absolute;top:0;line-height:16px;height:28px;padding:10px;z-index:3;color:rgba(0,0,0,0.9);fill:rgba(0,0,0,0.9);}.flatpickr-months .flatpickr-prev-month.disabled,.flatpickr-months .flatpickr-next-month.disabled{display:none}.flatpickr-months .flatpickr-prev-month i,.flatpickr-months .flatpickr-next-month i{position:relative}.flatpickr-months .flatpickr-prev-month.flatpickr-prev-month,.flatpickr-months .flatpickr-next-month.flatpickr-prev-month{/*
      /*rtl:begin:ignore*/left:0;/*
      /*rtl:end:ignore*/}/*
      /*rtl:begin:ignore*/
/*
      /*rtl:end:ignore*/
.flatpickr-months .flatpickr-prev-month.flatpickr-next-month,.flatpickr-months .flatpickr-next-month.flatpickr-next-month{/*
      /*rtl:begin:ignore*/right:0;/*
      /*rtl:end:ignore*/}/*
      /*rtl:begin:ignore*/
/*
      /*rtl:end:ignore*/
.flatpickr-months .flatpickr-prev-month:hover,.flatpickr-months .flatpickr-next-month:hover{color:#959ea9;}.flatpickr-months .flatpickr-prev-month:hover svg,.flatpickr-months .flatpickr-next-month:hover svg{fill:#f64747}.flatpickr-months .flatpickr-prev-month svg,.flatpickr-months .flatpickr-next-month svg{width:14px;height:14px;}.flatpickr-months .flatpickr-prev-month svg path,.flatpickr-months .flatpickr-next-month svg path{-webkit-transition:fill .1s;transition:fill .1s;fill:inherit}.numInputWrapper{position:relative;height:auto;}.numInputWrapper input,.numInputWrapper span{display:inline-block}.numInputWrapper input{width:100%;}.numInputWrapper input::-ms-clear{display:none}.numInputWrapper span{position:absolute;right:0;width:14px;padding:0 4px 0 2px;height:50%;line-height:50%;opacity:0;cursor:pointer;border:1px solid rgba(57,57,57,0.15);-webkit-box-sizing:border-box;box-sizing:border-box;}.numInputWrapper span:hover{background:rgba(0,0,0,0.1)}.numInputWrapper span:active{background:rgba(0,0,0,0.2)}.numInputWrapper span:after{display:block;content:"";position:absolute}.numInputWrapper span.arrowUp{top:0;border-bottom:0;}.numInputWrapper span.arrowUp:after{border-left:4px solid transparent;border-right:4px solid transparent;border-bottom:4px solid rgba(57,57,57,0.6);top:26%}.numInputWrapper span.arrowDown{top:50%;}.numInputWrapper span.arrowDown:after{border-left:4px solid transparent;border-right:4px solid transparent;border-top:4px solid rgba(57,57,57,0.6);top:40%}.numInputWrapper span svg{width:inherit;height:auto;}.numInputWrapper span svg path{fill:rgba(0,0,0,0.5)}.numInputWrapper:hover{background:rgba(0,0,0,0.05);}.numInputWrapper:hover span{opacity:1}.flatpickr-current-month{font-size:135%;line-height:inherit;font-weight:300;color:inherit;position:absolute;width:75%;left:12.5%;padding:6.16px 0 0 0;line-height:1;height:28px;display:inline-block;text-align:center;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0);}.flatpickr-current-month span.cur-month{font-family:inherit;font-weight:700;color:inherit;display:inline-block;margin-left:.5ch;padding:0;}.flatpickr-current-month span.cur-month:hover{background:rgba(0,0,0,0.05)}.flatpickr-current-month .numInputWrapper{width:6ch;width:7ch\0;display:inline-block;}.flatpickr-current-month .numInputWrapper span.arrowUp:after{border-bottom-color:rgba(0,0,0,0.9)}.flatpickr-current-month .numInputWrapper span.arrowDown:after{border-top-color:rgba(0,0,0,0.9)}.flatpickr-current-month input.cur-year{background:transparent;-webkit-box-sizing:border-box;box-sizing:border-box;color:inherit;cursor:text;padding:0 0 0 .5ch;margin:0;display:inline-block;font-size:inherit;font-family:inherit;font-weight:300;line-height:inherit;height:auto;border:0;border-radius:0;vertical-align:initial;}.flatpickr-current-month input.cur-year:focus{outline:0}.flatpickr-current-month input.cur-year[disabled],.flatpickr-current-month input.cur-year[disabled]:hover{font-size:100%;color:rgba(0,0,0,0.5);background:transparent;pointer-events:none}.flatpickr-weekdays{background:transparent;text-align:center;overflow:hidden;width:100%;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center;height:28px;}.flatpickr-weekdays .flatpickr-weekdaycontainer{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1}span.flatpickr-weekday{cursor:default;font-size:90%;background:transparent;color:rgba(0,0,0,0.54);line-height:1;margin:0;text-align:center;display:block;-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1;font-weight:bolder}.dayContainer,.flatpickr-weeks{padding:1px 0 0 0}.flatpickr-days{position:relative;overflow:hidden;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-align:start;-webkit-align-items:flex-start;-ms-flex-align:start;align-items:flex-start;width:307.875px;}.flatpickr-days:focus{outline:0}.dayContainer{padding:0;outline:0;text-align:left;width:307.875px;min-width:307.875px;max-width:307.875px;-webkit-box-sizing:border-box;box-sizing:border-box;display:inline-block;display:-ms-flexbox;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-flex-wrap:wrap;flex-wrap:wrap;-ms-flex-wrap:wrap;-ms-flex-pack:justify;-webkit-justify-content:space-around;justify-content:space-around;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0);opacity:1;}.dayContainer + .dayContainer{-webkit-box-shadow:-1px 0 0 #e6e6e6;box-shadow:-1px 0 0 #e6e6e6}.flatpickr-day{background:none;border:1px solid transparent;border-radius:150px;-webkit-box-sizing:border-box;box-sizing:border-box;color:#393939;cursor:pointer;font-weight:400;width:14.2857143%;-webkit-flex-basis:14.2857143%;-ms-flex-preferred-size:14.2857143%;flex-basis:14.2857143%;max-width:39px;height:39px;line-height:39px;margin:0;display:inline-block;position:relative;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;text-align:center;}.flatpickr-day.inRange,.flatpickr-day.prevMonthDay.inRange,.flatpickr-day.nextMonthDay.inRange,.flatpickr-day.today.inRange,.flatpickr-day.prevMonthDay.today.inRange,.flatpickr-day.nextMonthDay.today.inRange,.flatpickr-day:hover,.flatpickr-day.prevMonthDay:hover,.flatpickr-day.nextMonthDay:hover,.flatpickr-day:focus,.flatpickr-day.prevMonthDay:focus,.flatpickr-day.nextMonthDay:focus{cursor:pointer;outline:0;background:#e6e6e6;border-color:#e6e6e6}.flatpickr-day.today{border-color:#959ea9;}.flatpickr-day.today:hover,.flatpickr-day.today:focus{border-color:#959ea9;background:#959ea9;color:#fff}.flatpickr-day.selected,.flatpickr-day.startRange,.flatpickr-day.endRange,.flatpickr-day.selected.inRange,.flatpickr-day.startRange.inRange,.flatpickr-day.endRange.inRange,.flatpickr-day.selected:focus,.flatpickr-day.startRange:focus,.flatpickr-day.endRange:focus,.flatpickr-day.selected:hover,.flatpickr-day.startRange:hover,.flatpickr-day.endRange:hover,.flatpickr-day.selected.prevMonthDay,.flatpickr-day.startRange.prevMonthDay,.flatpickr-day.endRange.prevMonthDay,.flatpickr-day.selected.nextMonthDay,.flatpickr-day.startRange.nextMonthDay,.flatpickr-day.endRange.nextMonthDay{-webkit-box-shadow:none;box-shadow:none;color:#fff;}.flatpickr-day.selected.startRange,.flatpickr-day.startRange.startRange,.flatpickr-day.endRange.startRange{border-radius:50px 0 0 50px}.flatpickr-day.selected.endRange,.flatpickr-day.startRange.endRange,.flatpickr-day.endRange.endRange{border-radius:0 50px 50px 0}.flatpickr-day.selected.startRange + .endRange:not(:nth-child(7n+1)),.flatpickr-day.startRange.startRange + .endRange:not(:nth-child(7n+1)),.flatpickr-day.endRange.startRange + .endRange:not(:nth-child(7n+1)){-webkit-box-shadow:-10px 0 0 #569ff7;box-shadow:-10px 0 0 #569ff7}.flatpickr-day.selected.startRange.endRange,.flatpickr-day.startRange.startRange.endRange,.flatpickr-day.endRange.startRange.endRange{border-radius:50px}.flatpickr-day.inRange{border-radius:0;-webkit-box-shadow:-5px 0 0 #e6e6e6,5px 0 0 #e6e6e6;box-shadow:-5px 0 0 #e6e6e6,5px 0 0 #e6e6e6}.flatpickr-day.disabled,.flatpickr-day.disabled:hover,.flatpickr-day.prevMonthDay,.flatpickr-day.nextMonthDay,.flatpickr-day.notAllowed,.flatpickr-day.notAllowed.prevMonthDay,.flatpickr-day.notAllowed.nextMonthDay{color:rgba(57,57,57,0.3);background:transparent;border-color:transparent;cursor:default}.flatpickr-day.disabled,.flatpickr-day.disabled:hover{cursor:not-allowed;color:rgba(57,57,57,0.1)}.flatpickr-day.week.selected{border-radius:0;-webkit-box-shadow:-5px 0 0 #569ff7,5px 0 0 #569ff7;box-shadow:-5px 0 0 #569ff7,5px 0 0 #569ff7}.flatpickr-day.hidden{visibility:hidden}.rangeMode .flatpickr-day{margin-top:1px}.flatpickr-weekwrapper{display:inline-block;float:left;}.flatpickr-weekwrapper .flatpickr-weeks{padding:0 12px;-webkit-box-shadow:1px 0 0 #e6e6e6;box-shadow:1px 0 0 #e6e6e6}.flatpickr-weekwrapper .flatpickr-weekday{float:none;width:100%;line-height:28px}.flatpickr-weekwrapper span.flatpickr-day,.flatpickr-weekwrapper span.flatpickr-day:hover{display:block;width:100%;max-width:none;color:rgba(57,57,57,0.3);background:transparent;cursor:default;border:none}.flatpickr-innerContainer{display:block;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-sizing:border-box;box-sizing:border-box;overflow:hidden;}.flatpickr-rContainer{display:inline-block;padding:0;-webkit-box-sizing:border-box;box-sizing:border-box}.flatpickr-time{text-align:center;outline:0;display:block;height:0;line-height:40px;max-height:40px;-webkit-box-sizing:border-box;box-sizing:border-box;overflow:hidden;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;}.flatpickr-time:after{content:"";display:table;clear:both}.flatpickr-time .numInputWrapper{-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1;width:40%;height:40px;float:left;}.flatpickr-time .numInputWrapper span.arrowUp:after{border-bottom-color:#393939}.flatpickr-time .numInputWrapper span.arrowDown:after{border-top-color:#393939}.flatpickr-time.hasSeconds .numInputWrapper{width:26%}.flatpickr-time.time24hr .numInputWrapper{width:49%}.flatpickr-time input{background:transparent;-webkit-box-shadow:none;box-shadow:none;border:0;border-radius:0;text-align:center;margin:0;padding:0;height:inherit;line-height:inherit;color:#393939;font-size:14px;position:relative;-webkit-box-sizing:border-box;box-sizing:border-box;}.flatpickr-time input.flatpickr-hour{font-weight:bold}.flatpickr-time input.flatpickr-minute,.flatpickr-time input.flatpickr-second{font-weight:400}.flatpickr-time input:focus{outline:0;border:0}.flatpickr-time .flatpickr-time-separator,.flatpickr-time .flatpickr-am-pm{height:inherit;display:inline-block;float:left;line-height:inherit;color:#393939;font-weight:bold;width:2%;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-align-self:center;-ms-flex-item-align:center;align-self:center}.flatpickr-time .flatpickr-am-pm{outline:0;width:18%;cursor:pointer;text-align:center;font-weight:400}.flatpickr-time input:hover,.flatpickr-time .flatpickr-am-pm:hover,.flatpickr-time input:focus,.flatpickr-time .flatpickr-am-pm:focus{background:#f3f3f3}.flatpickr-input[readonly]{cursor:pointer}@-webkit-keyframes fpFadeInDown{from{opacity:0;-webkit-transform:translate3d(0,-20px,0);transform:translate3d(0,-20px,0)}to{opacity:1;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}}@keyframes fpFadeInDown{from{opacity:0;-webkit-transform:translate3d(0,-20px,0);transform:translate3d(0,-20px,0)}to{opacity:1;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}}

  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar { width: 210px; height: 249px; border-radius: 0; box-shadow: none; -webkit-box-shadow: none;}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-current-month { font-size: 14px; padding-top: 8px }
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-weekdays { width: 206px; height: 21px}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-weekdaycontainer { height: 21px; padding-top: 4px; padding-right: 2px; white-space: normal}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-weekdaycontainer .flatpickr-weekday { font-size: 11px; line-height: 10px}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer { max-width: 206px; min-width: 200px;}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months { height: 32px;}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months .flatpickr-prev-month { height: 24px; padding: 7px}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months .flatpickr-next-month { height: 24px; padding: 7px}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months .flatpickr-current-month .numInputWrapper { display: none}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .flatpickr-months .flatpickr-current-month input.cur-year { display: none}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer .flatpickr-day.disabled { color: rgba(57,57,57,0.18);}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer .flatpickr-day {flex-basis: 29.42px; height: 32px; line-height: 32px; border-radius: 0; font-weight: bolder;}
  #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer .flatpickr-day.today:after { content: "";position: absolute;top: 0px;left: 0px;width: 27px;height: 29px;display: inline-block;}
  @media screen and (-ms-high-contrast: active), screen and (-ms-high-contrast: none) { /* ie */
    #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer .flatpickr-day {flex-basis: 28.42px;}
    #tchatbotscenario_form_preview_body .chatTalk li .flatpickr-calendar .dayContainer .flatpickr-day.today:after { content: "";position: absolute;top: 0px;left: -1px;width: 28px;height: 29px;display: inline-block;}
  }
  #tchatbotscenario_form_preview_body .chatTalk li select { border: 1px solid #909090; border-radius: 0; padding: 5px; height: 30px; margin-top: 9px; margin-bottom: -2px; min-width: 210px; max-width: 220px}
  #tchatbotscenario_form_preview_body .chatTalk .grid_preview li.smallSize select { min-width: 183px;}

</style>
<section ng-repeat="(setActionId, setItem) in setActionList" id="action{{setActionId}}_preview">
  <h4 class="actionTitle">{{setActionId + 1}}．{{actionList[setItem.actionType].label}}</h4>
  <ul class="chatTalk details">
    <!-- メッセージ・選択肢 -->
    <div ng-class="{grid_preview : widget.settings['show_chatbot_icon'] == 1 }">
      <div ng-if="(setItem.message || main.visibleSelectOptionSetting(setItem)) && widget.settings['show_chatbot_icon'] == 1" class="iconDiv" >
        <div ng-if="!chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" class="img_wrapper">
          <img ng-src="{{widget.settings['chatbot_icon']}}" alt="無人対応アイコンに設定している画像">
        </div>
        <i ng-if="chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" ng-class="{icon_border:isMainColorWhite()}" class="fal {{widget.settings['chatbot_icon']}}"></i>
      </div>
      <li ng-show="setItem.message || main.visibleSelectOptionSetting(setItem)" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_message" class="details"></span></li>
    </div>
    <!-- ヒアリング -->
    <div ng-repeat="(index, hearings) in setItem.hearings">
      <style ng-if="hearings.uiType === '5'">
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar { border: 2px solid {{hearings.settings.customDesign.borderColor}}}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .flatpickr-months { background: {{hearings.settings.customDesign.headerBackgroundColor}}}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .flatpickr-months .flatpickr-month { color: {{hearings.settings.customDesign.headerTextColor}}}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .flatpickr-weekdays { background: {{hearings.settings.customDesign.headerWeekdayBackgroundColor}}}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .flatpickr-months .flatpickr-prev-month{ fill: {{hearings.settings.customDesign.headerTextColor}}}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .flatpickr-months .flatpickr-next-month { fill: {{hearings.settings.customDesign.headerTextColor}}}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .dayContainer { background-color: {{hearings.settings.customDesign.calendarBackgroundColor}}; }
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .dayContainer .flatpickr-day.selected { background-color: {{hearings.settings.customDesign.headerBackgroundColor}}; color: {{hearings.selectedTextColor}} !important;}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .dayContainer .flatpickr-day.today:after {border: 1px solid {{hearings.settings.customDesign.headerBackgroundColor}};  outline: 1px solid {{hearings.settings.customDesign.headerWeekdayBackgroundColor}}}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .dayContainer .flatpickr-day {border-top: none;  border-left:none; border-bottom: 1px solid  {{hearings.settings.customDesign.headerWeekdayBackgroundColor}}; border-right: 1px solid  {{hearings.settings.customDesign.headerWeekdayBackgroundColor}};}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar .dayContainer span:nth-child(7n+7) { border-right: none;}
        #action{{setActionId}}_calendar{{index}} .flatpickr-calendar span.flatpickr-weekday { color: {{hearings.weekdayTextColor}} !important;}
      </style>
      <style ng-if="hearings.uiType === '7'">
        .tal { text-align: left; }
        .tac { text-align: center; }
        .tar { text-align: right; }
        li.action{{setActionId}}_button{{index}} div#sinclo-button-wrap{{index}} span.sinclo-button {
          border-top: 1px solid {{hearings.settings.customDesign.buttonBorderColor}}!important;
          color:{{hearings.settings.customDesign.buttonTextColor}}!important;
          background-color:{{hearings.settings.customDesign.buttonBackgroundColor}}!important;
        }
        li.action{{setActionId}}_button{{index}} div#sinclo-button-wrap{{index}}.sideBySide span.sinclo-button:first-child {
          border-right: 1px solid {{hearings.settings.customDesign.buttonBorderColor}};
        }
        li.action{{setActionId}}_button{{index}} div#sinclo-button-wrap{{index}} span.sinclo-button:active
        {
          background-color:{{hearings.settings.customDesign.buttonActiveColor}}!important;
        }
      </style>
      <div ng-class="{grid_preview : widget.settings['show_chatbot_icon'] == 1 }">
        <div ng-if="hearings.message && widget.settings['show_chatbot_icon'] == 1" class="iconDiv" >
          <div ng-if="!chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" class="img_wrapper">
            <img ng-src="{{widget.settings['chatbot_icon']}}" alt="無人対応アイコンに設定している画像">
          </div>
          <i ng-if="chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" ng-class="{icon_border:isMainColorWhite()}" class="fal {{widget.settings['chatbot_icon']}}"></i>
        </div>
        <!-- テキスト（複数） -->
        <li ng-show="hearings.message" ng-if="hearings.uiType === '1' || hearings.uiType === '2'" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="details">{{hearings.message}}</span></li>
        <li ng-if="hearings.uiType === '5'" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="details">{{hearings.message}} <div id="action{{setActionId}}_calendar{{index}}" style="margin-top: 7px; margin-bottom: 6px"></div></span></li>
        <!-- プルダウン -->
        <li ng-show="hearings.message || hearings.settings.options[0]" ng-if="hearings.uiType === '4'" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')" style="padding-bottom: 0"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="sinclo-text-line">{{hearings.message}}</span><br><select id="action{{setActionId}}_selection{{index}}"><option class="action{{setActionId}}_selection{{index}}_option" value="">選択してください</option><option class="action{{setActionId}}_selection{{index}}_option" ng-repeat="item in hearings.settings.options track by $index" ng-if="item" value="{{item}}" ng-bind="item""></option></select>
        </li>

        <!-- ラジオボタン -->
        <li ng-show="hearings.message || hearings.settings.options[0]" ng-if="hearings.uiType === '3'" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="sinclo-text-line" ng-bind="hearings.message"></span><div style="margin-top: -32px">
                <span ng-repeat="(optionIndex, option) in hearings.settings.options track by $index" class="sinclo-radio" style="display: block" ng-if="option"><input name="action{{setActionId}}_index{{index}}" id="action{{setActionId}}_index{{index}}_option{{optionIndex}}" type="radio" value="{{option}}"><label for="action{{setActionId}}_index{{index}}_option{{optionIndex}}" ng-bind="option"></label></span></div></li>

        <!-- ボタン -->
        <li ng-show="hearings.message || hearings.settings.options[0]" ng-if="hearings.uiType === '7'" class="sinclo_re chat_left details no-wrap all-round action{{setActionId}}_button{{index}}" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span class="sinclo-text-line" ng-class="{tal: hearings.settings.customDesign.messageAlign == '1', tac: hearings.settings.customDesign.messageAlign == '2', tar: hearings.settings.customDesign.messageAlign == '3'}" ng-bind="hearings.message"></span><div id="sinclo-button-wrap{{index}}" class="sinclo-button-wrap" ng-class="{sideBySide: hearings.settings.options.length == 2}">
                <span ng-repeat="(optionIndex, option) in hearings.settings.options track by $index" class="sinclo-button" ng-class="{noneBorder: hearings.settings.customDesign.outButtonNoneBorder}" ng-if="option !== null">{{option}}</span></div></li>

      <!-- エラーメッセージ -->
      <br><li ng-show="hearings.errorMessage" class="sinclo_re chat_left details" ng-class="{notNone: widget.re_border_none === '' || widget.re_border_none === false, boxType: widget.settings['chat_message_design_type'] == 1, balloonType: widget.settings['chat_message_design_type'] == 2, middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_error_message" class="details">{{hearings.errorMessage}}</span></li>
      </div>
    </div>
    <!-- 確認メッセージ -->
    <div ng-class="{grid_preview : widget.settings['show_chatbot_icon'] == 1 }">
      <!-- アイコン設定START -->
      <div ng-if="(setItem.isConfirm && (setItem.confirmMessage || setItem.success || setItem.cancel)) && widget.settings['show_chatbot_icon'] == 1" class="iconDiv" >
        <div ng-if="!chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" class="img_wrapper">
          <img ng-src="{{widget.settings['chatbot_icon']}}" alt="無人対応アイコンに設定している画像">
        </div>
        <i ng-if="chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" ng-class="{icon_border:isMainColorWhite()}" class="fal {{widget.settings['chatbot_icon']}}"></i>
      </div>
      <!-- アイコン設定END -->
      <li ng-show="setItem.isConfirm && (setItem.confirmMessage || setItem.success || setItem.cancel)" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_confirm_message" class="details"></span></li>
    </div>
    <!-- ファイル送信 -->
    <div ng-class="{grid_preview : widget.settings['show_chatbot_icon'] == 1 }">
      <!-- アイコン設定START -->
      <div ng-if="setItem.file.download_url && setItem.file.file_name && setItem.file.file_size && widget.settings['show_chatbot_icon'] == 1" class="iconDiv" >
        <div ng-if="!chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" class="img_wrapper">
          <img ng-src="{{widget.settings['chatbot_icon']}}" alt="無人対応アイコンに設定している画像">
        </div>
        <i ng-if="chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" ng-class="{icon_border:isMainColorWhite()}" class="fal {{widget.settings['chatbot_icon']}}"></i>
      </div>
      <!-- アイコン設定END -->
      <li ng-if="setItem.file.download_url && setItem.file.file_name && setItem.file.file_size" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">ファイルが送信されました</span><div class="sendFileContent"><div class="sendFileThumbnailArea"><img ng-if="widget.isImage(setItem.file.extension)" ng-src="{{setItem.file.download_url}}" class="sendFileThumbnail" width="64" height="64"><i ng-if="!widget.isImage(setItem.file.extension)" ng-class="widget.selectIconClassFromExtension(setItem.file.extension)" class="fa fa-4x sendFileThumbnail" aria-hidden="true"></i></div><div class="sendFileMetaArea"><span class="data sendFileName details">{{setItem.file.file_name}}</span><span class="data sendFileSize details">{{setItem.file.file_size}}</span></div></div></li>
    </div>
    <!-- ファイル受信 -->
    <div ng-class="{grid_preview : widget.settings['show_chatbot_icon'] == 1 }">
      <!-- アイコン設定START -->
      <div ng-if="setItem.dropAreaMessage && widget.settings['show_chatbot_icon'] == 1" class="iconDiv" >
        <div ng-if="!chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" class="img_wrapper">
          <img ng-src="{{widget.settings['chatbot_icon']}}" alt="無人対応アイコンに設定している画像">
        </div>
        <i ng-if="chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" ng-class="{icon_border:isMainColorWhite()}" class="fal {{widget.settings['chatbot_icon']}}"></i>
      </div>
      <!-- アイコン設定END -->
      <li ng-show="setItem.dropAreaMessage" class="sinclo_re chat_left recv_file_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><div class="receiveFileContent"><div class="selectFileArea"><p class="dropFileMessage" ng-class="classNameChecker.checkMaster('middleSize,largeSize,customSize')">{{setItem.dropAreaMessage}}</p><p class="drop-area-icon" ng-class="{middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><i class="fal fa-3x fa-cloud-upload"></i></p><p ng-class="{middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}">または</p><p ng-class="{middleSize: widget.settings['widget_size_type'] == 2, largeSize: widget.settings['widget_size_type'] == 3 || widget.settings['widget_size_type'] == 4}"><a class="select-file-button">ファイルを選択</a></p></div></div><div ng-if="setItem.cancelEnabled" class="cancelReceiveFileArea"><a>{{setItem.cancelLabel}}</a></div></li>
    </div>
    <!-- 条件分岐アクション・テキスト発言 -->
    <div ng-repeat="(index, condition) in setItem.conditionList" ng-class="{grid_preview : widget.settings['show_chatbot_icon'] == 1 }">
      <div ng-if="condition.actionType == '1' && condition.action.message && widget.settings['show_chatbot_icon'] == 1" class="iconDiv" >
        <div ng-if="!chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" class="img_wrapper">
          <img ng-src="{{widget.settings['chatbot_icon']}}" alt="無人対応アイコンに設定している画像">
        </div>
        <i ng-if="chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" ng-class="{icon_border:isMainColorWhite()}" class="fal {{widget.settings['chatbot_icon']}}"></i>
      </div>
      <li ng-show="condition.actionType == '1' && condition.action.message" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}-{{index}}_message" class="details"></span></li>
    </div>
    <!-- 条件分岐アクション・どの条件にも該当しないテキスト発言 -->
    <div ng-class="{grid_preview : widget.settings['show_chatbot_icon'] == 1 }">
      <!-- アイコン設定START -->
      <div ng-if="setItem.elseEnabled && setItem.elseAction.actionType == '1' && setItem.elseAction.action.message && widget.settings['show_chatbot_icon'] == 1" class="iconDiv" >
        <div ng-if="!chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" class="img_wrapper">
          <img ng-src="{{widget.settings['chatbot_icon']}}" alt="無人対応アイコンに設定している画像">
        </div>
        <i ng-if="chatbotIconIsFontIcon(widget.settings['chatbot_icon'])" ng-class="{icon_border:isMainColorWhite()}" class="fal {{widget.settings['chatbot_icon']}}"></i>
      </div>
      <!-- アイコン設定END -->
      <li ng-show="setItem.elseEnabled && setItem.elseAction.actionType == '1' && setItem.elseAction.action.message" class="sinclo_re chat_left details" ng-class="classNameChecker.checkMaster('notNone,boxType,balloonType,middleSize,largeSize,customSize')"><span ng-if="widget.settings['show_automessage_name'] === '1'" class="cName details">{{widget.settings['sub_title']}}</span><span id="action{{setActionId}}_else-message" class="details"></span></li>
    </div>
  </ul>
</section>
