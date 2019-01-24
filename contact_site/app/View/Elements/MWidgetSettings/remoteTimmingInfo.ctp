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

  function beforeTrimmingInit(img, viewTag) {
    targetImgTag.attr('src', img);
    viewImgTag = viewTag;
  }

  function trimmingInit($scope, trimInfoTag, aspectRatio) {
    ngScope = $scope;
    trimmingInfoTag = trimInfoTag;
    $image = $('.cropper-example-1 > img');
    targetImgTag.cropper({
      aspectRatio: aspectRatio // ここでアスペクト比の調整 ワイド画面にしたい場合は 16 / 9
    });
    popupEvent.resize();
  }

  popupEvent.trimCarouselImage = function() {
    targetImgTag.cropper('getCroppedCanvas').toBlob((blob) => {
      const formData = new FormData();
      formData.append('file', blob);

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
          return XHR;
        }
      }).done(function(data, textStatus, jqXHR) {
        console.log(data.url);
        if (ngScope) {
          var numbers = trimmingInfoTag.match(/\d+/g).map(Number);
          ngScope.setActionList[numbers[0]].hearings[numbers[1]].settings.images[numbers[2]].url = data.url;
          ngScope.$apply();
        }
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
      ngScope.main_image = imgDataUrl;
      console.log(trimmingData);
      ngScope.trimmingInfo = JSON.stringify(trimmingData);
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