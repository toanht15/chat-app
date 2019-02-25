<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/20
 * Time: 18:26
 */
?>
<script type="text/javascript">
  'use strict';

  var sincloApp = angular.module('sincloApp', ['ngSanitize']);
  sincloApp.config(function($controllerProvider){
    sincloApp.controllerProvider = $controllerProvider;
  });
  sincloApp.controller('DiagramController', [
    '$scope', '$timeout', function($scope, $timeout) {

      // 保存ボタン
      $('#submitBtn').on('click', function(e) {
        // データをJSONにして送信
        $('#TChatbotDiagramActivity').val(exportJSON());
        $('#TChatbotDiagramsEntryForm').submit();
      });

    }]).controller('ModalController', [
      '$scope', '$timeout', '$compile', function($dialogScope, $timeout, $compile) {
        $(document).on('diagram.openModal', function(e, elm) {
          $timeout(function() {
            $dialogScope.$apply();
          });
        });
    }]).directive('resizeTextarea', function() {
    return {
      restrict: 'E',
      replace: true,
      template: '<textarea style="font-size: 13px; border-width: 1px; padding: 5px; line-height: 1.5;"></textarea>',
      link: function(scope, element, attrs) {
        var maxRow = element[0].dataset.maxRow || 10;                       // 表示可能な最大行数
        var fontSize = parseFloat(element[0].style.fontSize, 10);           // 行数計算のため、templateにて設定したフォントサイズを取得
        var borderSize = parseFloat(element[0].style.borderWidth, 10) * 2;  // 行数計算のため、templateにて設定したボーダーサイズを取得(上下/左右)
        var paddingSize = parseFloat(element[0].style.padding, 10) * 2;     // 表示高さの計算のため、templateにて設定したテキストエリア内の余白を取得(上下/左右)
        var lineHeight = parseFloat(element[0].style.lineHeight, 10);       // 表示高さの計算のため、templateにて設定した行の高さを取得
        var elm = angular.element(element[0]);

        function autoResize() {
          // テキストエリアの要素のサイズから、borderとpaddingを引いて文字入力可能なサイズを取得する
          var areaWidth = elm[0].getBoundingClientRect().width - borderSize - paddingSize;

          // フォントサイズとテキストエリアのサイズを基に、行数を計算する
          var textRow = 0;
          elm[0].value.split('\n').forEach(function(string) {
            var stringWidth = string.length * fontSize;
            textRow += Math.max(Math.ceil(stringWidth / areaWidth), 1);
          });

          // 表示する行数に応じて、テキストエリアの高さを調整する
          if (textRow > maxRow) {
            elm[0].style.height = (maxRow * (fontSize * lineHeight)) + paddingSize + 'px';
            elm[0].style.overflow = 'auto';
          } else {
            elm[0].style.height = (textRow * (fontSize * lineHeight)) + paddingSize + 'px';
            elm[0].style.overflow = 'hidden';
          }
        }

        autoResize();
        scope.$watch(attrs.ngModel, autoResize);
        $(window).on('load', autoResize);
        $(window).on('resize', autoResize);
        elm[0].addEventListener('input', autoResize);
      }
    };
  });
</script>
