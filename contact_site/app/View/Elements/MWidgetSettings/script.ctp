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
});

function saveAct(){
    $('#MWidgetSettingIndexForm').submit();
}

</script>
