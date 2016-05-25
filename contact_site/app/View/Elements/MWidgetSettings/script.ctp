<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']);
sincloApp.controller('WidgetCtrl', function($scope){
    $scope.isDisplayTime = function(){
        // 表示しない
        if ( Number(this.display_time_flg) === 0 ) {
            $('#receiptArea').attr('style', 'background-image: url(//sinclows.dip.jp/img/call_circle.png); background-repeat: no-repeat; background-position: 5px, 0px;height: 45px; margin: 15px 10px;background-size: 45px auto, 45px auto;padding-left: 45px;');
            $('#telNumber').attr('style', 'font-weight: bold; color: #ABCD05; margin: 0 auto;font-size: 20px; text-align: center;padding: 10px 0px 0px;height: 45px;');
            $('#MWidgetSettingTimeText').prop('disabled', true);
            $('#timeTextLabel').removeClass('require');
        }
        // 表示する
        else {
            $('#receiptArea').attr('style', 'background-image: url(//sinclows.dip.jp/img/call_circle.png); background-repeat: no-repeat; background-position: 5px, 0px; height: 50px; margin: 15px 10px; background-size: 55px auto, 55px auto; padding-left: 55px;');
            $('#telNumber').attr('style', 'font-weight: bold; color: #ABCD05; margin: 0 auto; font-size: 18px; text-align: center; padding: 5px 0px 0px; height: 30px');
            $('#MWidgetSettingTimeText').prop('disabled', false);
            $('#timeTextLabel').addClass('require');
        }
    };

    $scope.headerpd = function(){
      return $scope.descriptionToggle == '1';
    }

    $scope.makeFaintColor = function(){
      var defColor = "#F1F5C8";
      if ( $scope.main_color.indexOf("#") >= 0 ) {
        var code = $scope.main_color.substr(1), r,g,b;
        if (code.length === 3) {
          r = String(code.substr(0,1)) + String(code.substr(0,1));
          g = String(code.substr(1,1)) + String(code.substr(1,1));
          b = String(code.substr(2)) + String(code.substr(2));
        }
        else {
          r = String(code.substr(0,2));
          g = String(code.substr(2,2));
          b = String(code.substr(4));
        }
        defColor = "rgba(" + parseInt(r,16) + ", " + parseInt(g,16) + ", " + parseInt(b,16) + ", 0.1)";
      }
      return defColor;
    };

    $scope.inputInitToggle = function(item){
      return (item) ? 1 : 2;
    };

});

function saveAct(){
    $('#MWidgetSettingIndexForm').submit();
}

$(document).ready(function(){
    var scrollTimer = null;
    $(window).scroll(function(e){
        var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
        if (scrollTimer) {
          clearTimeout(scrollTimer);
        };
        var position = (scrollTop < 180 ) ? 20 : scrollTop-160;
        scrollTimer = setTimeout(function(){
          $("#m_widget_simulator").animate({
            "top": position
          }, 'slow');
        }, 200);
    });
    $(".widgetCtrl").click(function(){
        var target = $(".widgetCtrl.selected"), clickTab = $(this).data('tab');
        target.removeClass('selected');
        $(this).addClass('selected');
        if ( clickTab === "call" ) {
          $("#callTab").css('display', 'inline-block');
          $("#chatTab").hide();
        }
        else {
          $("#callTab").hide();
          $("#chatTab").css('display', 'inline-block');
        }
    });

});

</script>
