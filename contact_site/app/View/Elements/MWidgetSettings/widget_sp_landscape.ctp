<!-- スマホ版 -->
<div id="sincloBox" <?= !empty($isSpPreview) ? 'class="sp-preview landscape"' : '' ?> ng-if="showWidgetType === 2">
  <style>
    #sincloBox * { font-size: 12px; }
    #sincloBox span, #sincloBox pre { font-family: "ヒラギノ角ゴ ProN W3","HiraKakuProN-W3","ヒラギノ角ゴ Pro W3","HiraKakuPro-W3","メイリオ","Meiryo","ＭＳ Ｐゴシック","MS Pgothic",sans-serif,Helvetica, Helvetica Neue, Arial, Verdana!important }
    #sincloBox .pb07 { padding-bottom: 7px }
    #sincloBox .notSelect { -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; }
    #sincloBox p#widgetTitle { text-align: center!important; padding: 7px 30px!important; position:relative; z-index: 1; cursor:pointer; border-radius: 0; border: 1px solid {{main_color}}; border-bottom:none; background-color: {{main_color}};text-align: center; font-size: 14px; margin: 0;color: {{string_color}}; height: 32px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
    #sincloBox.open #minimizeBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>minimize.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
    /*
          #sincloBox:not(.open) #addBtn { cursor: pointer; background-image: url('<?=$gallaryPath?>add.png'); background-position-y: 0px; top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear; z-index: 2; }
*/
    /*
          #sincloBox p#widgetTitle:after { background-position-y: 3px; background-image: url('<?=$gallaryPath?>yajirushi.png'); top: 6px; right: 10px; bottom: 6px; content: " "; display: inline-block; width: 20px; height: 20px; position: absolute; background-size: contain; vertical-align: middle; background-repeat: no-repeat; transition: transform 200ms linear}
      #sincloBox.open p#widgetTitle:after { transform: rotate(0deg); }
      #sincloBox:not(.open) p#widgetTitle:after { transform: rotate(180deg); }
*/
    #sincloBox section { display: inline-block; width: 100%; border: 1px solid {{widget_border_color}}; border-top: none; }
    #sincloBox section:not(.notNoneWidgetOutsideBorder) { border:none }
    #sincloBox section.noDisplay { display: none }
    #sincloBox div#miniTarget { overflow: hidden; transition: height 200ms linear; }
    @keyframes leftEffect { 0% { transform :translate3d(-20px, 0px, 0px) scale(0.8); opacity :0; } 69% {} 100% { transform :translate3d(0px, 0px, 0px); opacity :1; } }
    #sincloBox ul#chatTalk { width: 100%; height: 100px; padding: 5px; list-style-type: none; overflow-y: scroll; overflow-x: hidden; margin: 0}
    #sincloBox ul#chatTalk.details { background-color: {{chat_talk_background_color}}; }
    #sincloBox ul#chatTalk li { border-radius: 5px; background-color: #FFF; margin: 5px 0 0; padding: 3px; font-size: 11px; line-height: 1.4; white-space: pre; color: {{message_text_color}}; }
    #sincloBox ul#chatTalk div.liLeft { text-align: left; }
    #sincloBox ul#chatTalk div.liRight { text-align: right; }
    #sincloBox ul#chatTalk li.boxType { word-wrap: break-word; word-break: break-all; }
    #sincloBox ul#chatTalk li.balloonType { display: inline-block; position: relative; padding: 5px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
    #sincloBox ul#chatTalk li.sinclo_se.details { background-color: {{getSeBackgroundColor()}}; }
    #sincloBox ul#chatTalk li.sinclo_se.notNone { border: 1px solid {{getTalkBorderColor('se')}}; }
    #sincloBox ul#chatTalk li.sinclo_se.balloonType { margin-right: 15px; border-bottom-right-radius: 0px; }
    #sincloBox ul#chatTalk li.sinclo_se.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: calc(100% - 3px); margin-top: -10px; border: 5px solid transparent; border-left: 5px solid {{getSeBackgroundColor()}}; border-bottom: 5px solid {{getSeBackgroundColor()}}; z-index: 2; }
    #sincloBox ul#chatTalk li.sinclo_se.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: 100%; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
    #sincloBox ul#chatTalk li.sinclo_se.balloonType.notNone:after { border-left: 5px solid {{getTalkBorderColor('se')}}; border-bottom: 5px solid {{getTalkBorderColor('se')}}; }
    #sincloBox ul#chatTalk li.sinclo_re.notNone { border: 1px solid {{getTalkBorderColor('re')}}; }
    #sincloBox ul#chatTalk li.sinclo_re.balloonType { margin-left: 10px; padding-right: 20px; border-bottom-left-radius: 0px; }
    #sincloBox ul#chatTalk li.sinclo_re.balloonType:before { height: 0px; content: ""; position: absolute; bottom: 0px; left: -7px; margin-top: -10px; border: 5px solid transparent; border-right: 5px solid {{makeFaintColor()}}; border-bottom: 5px solid {{makeFaintColor()}}; z-index: 2; }
    #sincloBox ul#chatTalk li.sinclo_re.balloonType:after { height: 0px; content: ""; position: absolute; bottom: -1px; left: -10px; margin-top: -9px; border: 5px solid transparent; z-index: 1; }
    #sincloBox ul#chatTalk li.sinclo_re.balloonType.notNone:after { border-right: 5px solid {{getTalkBorderColor('re')}}; border-bottom: 5px solid {{getTalkBorderColor('re')}}; }
    #sincloBox ul#chatTalk li.effect_left { -webkit-animation-name:leftEffect; -moz-animation-name:leftEffect; -o-animation-name:leftEffect; -ms-animation-name:leftEffect; animation-name:leftEffect; -webkit-animation-duration:0.5s; -moz-animation-duration:0.5s; -o-animation-duration:0.5s; -ms-animation-duration:0.5s; animation-duration:0.5s; -webkit-animation-iteration-count:1; -moz-animation-iteration-count:1; -o-animation-iteration-count:1; -ms-animation-iteration-count:1; animation-iteration-count:1; -webkit-animation-fill-mode:forwards; -moz-animation-fill-mode:forwards; -o-animation-fill-mode:forwards; -ms-animation-fill-mode:forwards; animation-fill-mode:forwards; -webkit-transform-origin:left bottom; -moz-transform-origin:left bottom; -o-transform-origin:left bottom; -ms-transform-origin:left bottom; transform-origin:left bottom; opacity:0; -webkit-animation-delay:0.6s; -moz-animation-delay:0.6s; -o-animation-delay:0.6s; -ms-animation-delay:0.6s; animation-delay:0.6s; }
    #sincloBox ul#chatTalk li.boxType.chat_right { border-bottom-right-radius: 0; margin-left: 10px }
    #sincloBox ul#chatTalk li.boxType.chat_left { border-bottom-left-radius: 0; margin-right: 10px }
    #sincloBox ul#chatTalk li.balloonType.chat_right { margin-left: 15px }
    #sincloBox ul#chatTalk li.balloonType.chat_left { margin-right: 10px }
    #sincloBox ul#chatTalk li span.cName { display: block; color: {{main_color}}!important; font-weight: bold; font-size: 12px; margin: 0 0 5px 0; }
    #sincloBox ul#chatTalk li span.cName.details{ color: {{c_name_text_color}}!important;}
    #sincloBox ul#chatTalk li span.cName:not(.details){ color: {{main_color}}!important;}
    #sincloBox ul#chatTalk li span:not(.details){  color: {{message_text_color}}!important; }
    #sincloBox ul#chatTalk li.sinclo_re span.details{ color: {{re_text_color}};}
    #sincloBox ul#chatTalk li.sinclo_se span.details{ color: {{se_text_color}};}
    #sincloBox section#chatTab div { height: 65px!important;  padding: 10px; }
    #sincloBox section#chatTab textarea#sincloChatMessage { width: 80%; height: 100%; color: {{other_text_color}}; margin: 0; resize: none; padding: 5px; }
    #sincloBox section#chatTab textarea#sincloChatMessage.details { color: {{message_box_text_color}}; background-color: {{message_box_background_color}}; }
    #sincloBox section#chatTab textarea#sincloChatMessage.details.notNone { border: 1px solid {{message_box_border_color}}!important; }
    #sincloBox section#chatTab textarea#sincloChatMessage.notNone { border-radius: 5px 0 0 5px!important; border: 1px solid {{chat_talk_border_color}}!important; border-right-color: transparent!important; }
    #sincloBox section#chatTab textarea#sincloChatMessage.notNone:focus { border-color: {{main_color}}!important; outline: none!important; border-right-color: transparent!important; }
    #sincloBox section#chatTab textarea#sincloChatMessage:not(.notNone){ border: none!important }
    #sincloBox section#chatTab #sincloChatSendBtn{ width: 20%; height: 100%; padding: 1em 0; border-radius: 0 5px 5px 0; cursor: pointer; margin: 0 auto; float: right; text-align: center; background-color: {{main_color}}!important; color: {{string_color}}; font-weight: bold; font-size: 1.2em;}
    #sincloBox section#chatTab #sincloChatSendBtn.details{ background-color: {{chat_send_btn_background_color}}!important; }
    #sincloBox section#chatTab #sincloChatSendBtn span { color: {{string_color}} }
    #sincloBox section#chatTab #sincloChatSendBtn span.details { color: {{chat_send_btn_text_color}} }
    #sincloBox section#chatTab #messageBox.messageBox{border-top: 1px solid {{widget_border_color}}; padding: 0.5em;}
    #sincloBox section#chatTab #messageBox.messageBox:not(.notNoneWidgetOutsideBorder) { border:none }
    #sincloBox section#chatTab #messageBox.messageBox.details{ background-color: {{chat_message_background_color}}; border-top: 1px solid {{widget_inside_border_color}}; }
    #sincloBox section#chatTab #messageBox.messageBox.details:not(.notNone){ border-top: none; }
  </style>
  <!-- chat_message_copy 0 stayt -->
  <div ng-if="chat_message_copy == '0'">
    <!-- タイトル -->
    <p id="widgetTitle" class="widgetOpener" ng-class="{center: mainImageToggle == '2'}" >{{title}}</p>
    <!-- タイトル -->
  </div>
  <!-- chat_message_copy 0 end -->

  <!-- chat_message_copy 1 stayt -->
  <div ng-if="chat_message_copy == '1'">
    <!-- タイトル -->
    <p id="widgetTitle" class="widgetOpener" ng-class="{center: mainImageToggle == '2'}" style = "user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;">{{title}}</p>
    <!-- タイトル -->
  </div>
  <!-- chat_message_copy 1 end -->

  <div id="minimizeBtn" class="widgetOpener"></div>
  <!--
      <div id="addBtn" class="widgetOpener"></div>
   -->
  <div id="miniTarget">
    <section id="chatTab" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false}">

      <!-- chat_message_copy 0 stayt -->
      <ul id="chatTalk" class="details" ng-if="chat_message_copy == '0'">
        <div style="height: auto!important; padding:0;" ng-class="{liLeft: chat_message_design_type == 1, liRight: chat_message_design_type == 2}">
          <li class="sinclo_se chat_right details" ng-class="{notNone:se_border_none === '' || se_border_none === false, boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}"><span class="details" >○○について質問したいのですが</span></li>
        </div>
        <div style="height: auto!important; padding:0;">
          <li class="sinclo_re chat_left" ng-class="{notNone:re_border_none === '' || re_border_none === false, boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName details" >{{sub_title}}</span><span class="details">こんにちは</span></li>
        </div>
        <div style="height: auto!important; padding:0;">
          <li class="showAnimationSample sinclo_re chat_left" ng-class="{notNone:re_border_none === '' || re_border_none === false, boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName details" >{{sub_title}}</span><span class="details">○○についてですね<br>どのようなご質問でしょうか？</span></li>
        </div>
      </ul>
      <!-- chat_message_copy 0 end -->

      <!-- chat_message_copy 1 stayt -->
      <ul id="chatTalk" class="details" ng-if="chat_message_copy == '1'" style = "user-select: none;-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;" >
        <div style="height: auto!important; padding:0;" ng-class="{liLeft: chat_message_design_type == 1, liRight: chat_message_design_type == 2}">
          <li class="sinclo_se chat_right details" ng-class="{notNone:se_border_none === '' || se_border_none === false, boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}"><span class="details">○○について質問したいのですが</span></li>
        </div>
        <div style="height: auto!important; padding:0;">
          <li class="sinclo_re chat_left" ng-class="{notNone:re_border_none === '' || re_border_none === false, boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName details" >{{sub_title}}</span><span class="details">こんにちは</span></li>
        </div>
        <div style="height: auto!important; padding:0;">
          <li class="showAnimationSample sinclo_re chat_left" ng-class="{notNone:re_border_none === '' || re_border_none === false, boxType: chat_message_design_type == 1, balloonType: chat_message_design_type == 2}" ng-style="{backgroundColor:makeFaintColor()}"><span class="cName details" >{{sub_title}}</span><span class="details">○○についてですね<br>どのようなご質問でしょうか？</span></li>
        </div>
      </ul>
      <!-- chat_message_copy 1 end -->

      <div id="messageBox" class="messageBox details" ng-class="{ notNoneWidgetOutsideBorder:widget_outside_border_none === ''||widget_outside_border_none === false, notNone:widget_inside_border_none === ''||widget_inside_border_none === false }">
        <textarea name="sincloChat" id="sincloChatMessage" class="details" ng-class="{ notNone:message_box_border_none === ''||message_box_border_none === false}" placeholder="メッセージを入力してください{{chat_area_placeholder_sp}}"></textarea>
        <a id="sincloChatSendBtn" class="notSelect details" ><span class="details">送信</span></a>
      </div>
    </section>
  </div>
</div>