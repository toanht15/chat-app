<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize']);
sincloApp.controller('WidgetCtrl', function($scope){
    $scope.main_image = "<?=$this->formEx->val($this->data['MWidgetSetting'], 'main_image')?>";
    $scope.isDisplayTime = function(){
        // 表示しない
        if ( Number(this.display_time_flg) === 0 ) {
            $('#MWidgetSettingTimeText').prop('disabled', true);
            $('#timeTextLabel').removeClass('require');
        }
        // 表示する
        else {
            $('#MWidgetSettingTimeText').prop('disabled', false);
            $('#timeTextLabel').addClass('require');
        }
    };

    $scope.headerpd = function(){
      return $scope.descriptionToggle == '1';
    }

    $scope.showChooseImg = function(){
      return $scope.mainImageToggle == '1';
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

    $scope.showGallary = function(){
      $.ajax({
        type: 'post',
        data: {
          color: $scope.main_color,
        },
        cache: false,
        dataType: 'html',
        url: "<?= $this->Html->url('/MWidgetSettings/remoteShowGallary') ?>",
        success: function(html){
          modalOpen.call(window, html, 'p-show-gallary', 'ギャラリー');
          popupEvent.customizeBtn = function(name){
            $scope.main_image = "<?=$gallaryPath?>" + name;
            $("#MWidgetSettingUploadImage").val("");
            $scope.$apply();
            popupEvent.close();
          };
        },
        error: function(){

        }
      });

    }


    angular.element('#MWidgetSettingUploadImage').change(function(e){
        var files = e.target.files;
        if ( window.URL && files.length > 0 ) {
            var file = files[files.length-1];
            // 2MB以下である
            if (file.size > 2000000) {
                $("#MWidgetSettingUploadImage").val("");
                return false;
            }
            // jpeg/jpg/png
            var reg = new  RegExp(/image\/(png|jpeg|jpg)/i);
            if ( !reg.exec(file.type) ) {
                $("#MWidgetSettingUploadImage").val("");
                return false;
            }
            var url = window.URL.createObjectURL(file);
            $scope.main_image = url;
            $scope.$apply();
        }
    });

        var sincloBox = document.getElementById("sincloBox");
        sincloBox.setAttribute("data-openflg", true);
        var widgetTitle = document.getElementById("widgetTitle");
        widgetTitle.addEventListener("click", function(e){
          var target = document.getElementById("sincloBox");
          var main = document.getElementById("miniTarget");
          var flg = target.getAttribute("data-openflg");
          var nextFlg = true;
          if ( String(flg) === "true" ) {
            nextFlg = false;
            main.style.height = 0;
          }
          else {
            var height = 0;
            for(var i = 0; main.children.length > i; i++){
                height += main.children[i].offsetHeight;
            }
            main.style.height = height + "px";
          }
          sincloBox.setAttribute("data-openflg", nextFlg);
        });

        $scope.saveAct = function (){
            $('#MWidgetSettingMainImage').val($scope.main_image);
            $('#MWidgetSettingIndexForm').submit();
        }

});

sincloApp.directive('errSrc', function(){
	return {
		link: function(scope,elements, attrs) {
			if ( attrs.ngSrc === "" ) {
				attrs.$set('src', attrs.errSrc);
			}
			elements.bind("error", function(){
				if ( attrs.ngSrc != attrs.errSrc ) {
					attrs.$set('src', attrs.errSrc);
				}
			});
		}
	};
});


$(document).ready(function(){
    var scrollTimer = null;

    var content = document.getElementById('content');
    var widget = document.getElementById('m_widget_simulator');
    var defaultTop = (content.offsetHeight - $("#m_widget_setting_form").offset().top - widget.offsetHeight) / 2;
    widget.style.top = defaultTop + "px";

    $("#content").scroll(function(e){
        var scrollTop = this.scrollTop;
        if (scrollTimer) {
          clearTimeout(scrollTimer);
        };
        var position = (scrollTop < defaultTop ) ? defaultTop : scrollTop + defaultTop;
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
