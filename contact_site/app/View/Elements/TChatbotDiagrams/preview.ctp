<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/25
 * Time: 20:28
 */
?>
<style>
  .chatTalk li { border-radius: 5px; background-color: #FFF; margin: 10px 0 0; padding: 12px; font-size: 12px; line-height: 1.4; white-space: pre; color: {{widget.settings['message_text_color']}}; }
  .chatTalk li { background-color: {{widget.makeFaintColor()}} }
  .chatTalk li { word-break: break-all; white-space: pre-wrap; }
  .chatTalk li.boxType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; }
  .chatTalk div.arrowUp li.boxType.chat_left { border-radius: 0 12px 12px 12px ; }
  .chatTalk div.arrowBottom li.boxType.chat_left { border-radius: 12px 12px 12px 0; }
  .chatTalk li.boxType.chat_left { margin-left: 10px; margin-right: 17.5px; }
  .chatTalk li.boxType.chat_left.middleSize { margin-left: 10px; margin-right: 21px; }
  .chatTalk li.boxType.chat_left.largeSize { margin-left: 10px; margin-right: 24.6px; }
  .chatTalk li.balloonType { display: inline-block; position: relative; padding: 10px 15px; text-align: left!important; word-wrap: break-word; word-break: break-all; border-radius: 12px; }
  .chatTalk li.no-wrap { display: block!important; padding: 10px 0 0 0; justify-self: stretch; }
  .chatTalk li.all-round { border-radius: 12px!important; }
</style>
<script>
  //ここでプレビュー用ディレクティブを定義
  sincloApp.directive('previewText', function() {
    return {
      restrict: 'E',
      replace: true,
      template: '<div ng-show="text" ng-class="{grid_preview : widget.chatbotIconToggle == 1}">' +
          '<div>' +
          '<div ng-if="widget.isBotIconImg" class="img_wrapper">' +
          '<img ng-src="{{widget.settings[\'chatbot_icon\']}}" alt="無人対応アイコンに設定している画像">' +
          '</div>' +
          '<i ng-if="widget.isBotIconIcon"></i>' +
          '</div>' +
          '<li class="sinclo_re"><span>{{text}}</span></li>' +
          '</div>'
    }

  });

</script>

