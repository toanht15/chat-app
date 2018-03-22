<style>
  img.img-responsive {
    max-width: 100%; /* This rule is very important, please do not ignore this! */
  }
</style>
<script type="text/javascript">
  var $image = null;
  var replaced = null;
  var targetImgTag = $('#img');
  var croppedData = {};
  var ngScope = null;

  function beforeTrimmingInit(img) {
    targetImgTag.attr('src', img);
  }

  function trimmingInit($scope) {
    ngScope = $scope;
    $image = $('.cropper-example-1 > img');
    targetImgTag.cropper({
      aspectRatio: 62 / 70 // ここでアスペクト比の調整 ワイド画面にしたい場合は 16 / 9
    });
    popupEvent.resize();
    $('#popup-frame').css('width', '840px');
  }

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

    var imgDataUrl = $('#img').cropper('getCroppedCanvas').toDataURL();
    $('#trim').attr('src', imgDataUrl);
    if(ngScope) {
      ngScope.main_image = imgDataUrl;
      console.log(trimmingData);
      ngScope.trimmingInfo = JSON.stringify(trimmingData);
      ngScope.$apply();
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