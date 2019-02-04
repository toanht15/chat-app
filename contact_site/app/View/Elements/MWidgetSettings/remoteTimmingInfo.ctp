<style>
  img.img-responsive {
    max-width: 100%; /* This rule is very important, please do not ignore this! */
  }
</style>
<script type="text/javascript">
  var $image = null;
  var replaced = null;
  var targetImgTag = $('#img');
  var viewImgTag = null;
  var trimmingInfoTag = null;
  var croppedData = {};
  var ngScope = null;
  var targetImageName = null;

  function beforeTrimmingInit(img, viewTag) {
    targetImgTag.attr('src', img);
    viewImgTag = viewTag;
  }

  function trimmingInit($scope, trimInfoTag, aspectRatio, target) {
    ngScope = $scope;
    trimmingInfoTag = trimInfoTag;
    targetImageName = target;
    $image = $('.cropper-example-1 > img');
    targetImgTag.cropper({
      aspectRatio: aspectRatio, // ここでアスペクト比の調整 ワイド画面にしたい場合は 16 / 9
    });
    if( target === "chatbot_icon" || target === "operator_icon" || target === "profile_icon") {
      //アイコンに関する設定の場合はクラスを付ける
      $('.cropper-example-1').addClass( "icon_trimming" );
    }
    if( $('#popup-frame')[0] != null ) {
      popupEvent.resize();
    }
    if ( $('#popup-frame-overlap')[0] != null ) {
      popupEventOverlap.resize();
    }
  }

  var isBotIconNeedChangeToMainImage = function(ngScope){
    return Number(ngScope.chatbotIconToggle) === 1
        && Number(ngScope.chatbotIconType) === 1;
  };

  var isOpIconNeedChangeToMainImage = function(ngScope){
    return Number(ngScope.operatorIconToggle) === 1
        && Number(ngScope.operatorIconType) === 1;
  };

  popupEventOverlap.doTrimming = function(){
    data = $('#img').cropper('getData');
    // 切り抜きした画像のデータ
    // このデータを元にして画像の切り抜きが行われます
    var trimmingData = {
      width: Math.round(data.width),
      height: Math.round(data.height),
      x: Math.round(data.x),
      y: Math.round(data.y),
      _token: 'jf89ajtr234534829057835wjLA-SF_d8Z' // csrf用
    };

    var imgDataUrl = targetImgTag.cropper('getCroppedCanvas').toDataURL();
    viewImgTag.attr('src', imgDataUrl);
    console.log(trimmingInfoTag);
    if(trimmingInfoTag) {
      trimmingInfoTag.val(JSON.stringify(trimmingData));
    }
    return popupEventOverlap.close();
  };

  function carouselTrimmingInit($scope, trimInfoTag) {
    ngScope = $scope;
    trimmingInfoTag = trimInfoTag;
    var numbers = trimmingInfoTag.match(/\d+/g).map(Number);
    var aspectRatio = ngScope.setActionList[numbers[0]].hearings[numbers[1]].settings.aspectRatio;
    $image = $('.cropper-example-1 > img');
    if (aspectRatio) {
      var style = '<style>';
      style += '#popup #popup-frame-base #popup-frame.p-widget-carousel-trimming .point-e {\n' +
          '  display: none;\n' +
          '}\n' +
          '\n' +
          '#popup #popup-frame-base #popup-frame.p-widget-carousel-trimming .point-s {\n' +
          '  display: none;\n' +
          '}\n' +
          '\n' +
          '#popup #popup-frame-base #popup-frame.p-widget-carousel-trimming .point-n {\n' +
          '  display: none;\n' +
          '}\n' +
          '\n' +
          '#popup #popup-frame-base #popup-frame.p-widget-carousel-trimming .point-w {\n' +
          '  display: none;\n' +
          '}';
      style += '</style>';
      $('.cropper-example-1').after(style);
    }

    var html = '<i style="margin-top: 10px; cursor: pointer" id="cropper_zoom_btn" onclick="zoomIn();" class="fas fa-2x fa-search-plus"></i>';
    html += '<i style="margin-top: 10px; margin-left: 10px; cursor: pointer" onclick="zoomOut();" class="fas fa-2x fa-search-minus"></i>';
    $('.cropper-example-1').after(html);

    targetImgTag.cropper({
      viewMode: 0,
      aspectRatio: aspectRatio, // ここでアスペクト比の調整 ワイド画面にしたい場合は 16 / 9
    });
    popupEvent.resize();
  }

  function zoomIn() {
    targetImgTag.cropper('zoom', 0.1);
  }

  function zoomOut() {
    targetImgTag.cropper('zoom', -0.1);
  }

  popupEvent.trimCarouselImage = function() {
    var aspectRatio = targetImgTag.cropper('getCroppedCanvas').width / targetImgTag.cropper('getCroppedCanvas').height;
    var numbers = trimmingInfoTag.match(/\d+/g).map(Number);
    if (!ngScope.setActionList[numbers[0]].hearings[numbers[1]].settings.aspectRatio) {
      ngScope.setActionList[numbers[0]].hearings[numbers[1]].settings.aspectRatio = aspectRatio;
    }

    targetImgTag.cropper('getCroppedCanvas').toBlob((blob) => {
      const formData = new FormData();
      formData.append('file', blob);
      var numbers = trimmingInfoTag.match(/\d+/g).map(Number);
      $.ajax({
        url: "<?= $this->Html->url('/TChatbotScenario/remoteUploadCarouselImage') ?>",
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        xhr: function() {
          var XHR = $.ajaxSettings.xhr();
          if (XHR.upload) {
            XHR.upload.addEventListener('progress', function(e) {
              var progress = parseInt(e.loaded / e.total * 10000) / 100;
              ngScope.setActionList[numbers[0]].hearings[numbers[1]].settings.images[numbers[2]].isUploading = true;
              $('.progressbar_action' + numbers[0] + '_hearing' + numbers[1] + '_image' + numbers[2]).css('width', progress + '%');
              ngScope.$apply();
            }, false);
          }

          return XHR;
        }
      }).done(function(data, textStatus, jqXHR) {
        console.log(data.url);
        if (ngScope) {
          ngScope.setActionList[numbers[0]].hearings[numbers[1]].settings.images[numbers[2]].url = data.url;
          ngScope.$apply();
        }
      }).always(function() {
        ngScope.setActionList[numbers[0]].hearings[numbers[1]].settings.images[numbers[2]].isUploading = false;
      });

      return popupEvent.close();
    });
  };

  popupEvent.doTrimming = function(){
    data = $('#img').cropper('getData');
    // 切り抜きした画像のデータ
    // このデータを元にして画像の切り抜きが行われます
    var trimmingData = {
      width: Math.round(data.width),
      height: Math.round(data.height),
      x: Math.round(data.x),
      y: Math.round(data.y),
      _token: 'jf89ajtr234534829057835wjLA-SF_d8Z' // csrf用
    };

    var imgDataUrl = targetImgTag.cropper('getCroppedCanvas').toDataURL();
    viewImgTag.attr('src', imgDataUrl);

    if(ngScope) {
      if(targetImageName) {
        switch(targetImageName) {
          case "main_image":
            ngScope.main_image = imgDataUrl;
            ngScope.trimmingInfo = JSON.stringify(trimmingData);
            if ( isBotIconNeedChangeToMainImage(ngScope) ) {
              ngScope.chatbot_icon = ngScope.main_image;
            }
            if ( isOpIconNeedChangeToMainImage(ngScope) ) {
              ngScope.operator_icon = ngScope.main_image;
            }
            break;
          case "chatbot_icon":
            ngScope.chatbot_icon = imgDataUrl;
            ngScope.trimmingBotIconInfo = JSON.stringify(trimmingData);
            break;
          case "operator_icon":
            ngScope.operator_icon = imgDataUrl;
            ngScope.trimmingOpIconInfo = JSON.stringify(trimmingData);
            break;
          default:
            //default is main_image
            ngScope.main_image = imgDataUrl;
            ngScope.trimmingInfo = JSON.stringify(trimmingData);
            break;
        }
      }
      ngScope.$apply();
    }
    if(trimmingInfoTag) {
      trimmingInfoTag.val(JSON.stringify(trimmingData));
    }

    return popupEvent.close();
  };
  //var $image = $('.cropper-example-1 > img'),replaced;
  $(function(){
// getDataボタンが押された時の処理
    $('#getData3').on('click', function(){
      console.log('チェｋｋｋック');
      // crop のデータを取得
      croppedData = targetImgTag.cropper('getData');
    });
  });
</script>
<div class="cropper-example-1" style='max-width: 800px'>
  <!-- bladeテンプレートを使用していれば asset()や url() 関数が使えます -->
  <img src="" width="640" height="480" id='img' class='img-responsive' style='margin:0 auto;' alt="チャットに設定している画像">
</div>

<div>
  <div class="container" id="trimmed-image"></div>
</div>
