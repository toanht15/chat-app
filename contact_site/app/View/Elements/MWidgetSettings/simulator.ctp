<script type="text/javascript">
  <!--
  // OS判定
  $(function() {
    if (navigator.userAgent.indexOf('Mac') > 0){
      $('body').addClass('mac');
    }
  });
  //-->
</script>
<style>
    .showType4{
      width: 285px;
    }
    .showType4.middleSize{
      width: 342.5px;
    }
    .showType4.largeSize{
      width: 400px;
    }
    #m_widget_setting_idx #m_widget_setting_form #m_widget_simulator section#switch_widget.showBanner{
      margin-bottom: 3em;
    }
</style>
<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ){?>
<section id="switch_widget" ng-cloak ng-class="{showBanner:closeButtonModeTypeToggle === '1' && closeButtonSettingToggle === '2' && showWidgetType === 4}">
  <ul class="ulTab" data-col=3 ng-hide="closeButtonSettingToggle === '2'">
    <li ng-class="{choose: showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    <li ng-class="{choose: showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
    <li ng-class="{choose: showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
  </ul>
  <ul class="ulTab showType4" data-col=3 ng-hide="closeButtonSettingToggle !== '2'" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
    <li ng-class="{choose: showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    <li ng-class="{choose: showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
    <li ng-class="{choose: showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
    <li ng-class="{choose: showWidgetType === 4}" ng-click="switchWidget(4)">非表示</li>
  </ul>
  <input type="hidden" id="switch_widget" value="">
</section>
<?php } else { ?>
<section id="switch_widget" ng-cloak ng-hide="closeButtonSettingToggle !== '2'" ng-class="{showBanner:closeButtonModeTypeToggle === '1' && closeButtonSettingToggle === '2' && showWidgetType === 4}">
  <ul class="ulTab showType4" data-col=3  ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && widgetSizeTypeToggle === '3'}">
    <li ng-class="{choose: showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    <li ng-class="{choose: showWidgetType === 4}" ng-click="switchWidget(4)">非表示</li>
  </ul>
  <input type="hidden" id="switch_widget" value="">
</section>
<?php }?>

<section id="sample_widget_area" ng-cloak>
  <style>
    #sincloBox {
      position: relative;
      z-index: 1;
      width: 285px;
      background-color: rgb(255, 255, 255);
      box-shadow: 0px 0px {{box_shadow}}px {{box_shadow}}px rgba(0,0,0,0.1);
      /* z風 */
      /*box-shadow: 0px -2px 3px 2px rgba(0,0,0,calc(({{box_shadow}} * 0.1) / 2));*/
      /* d風 */
      /* box-shadow: 0 10px 20px rgba(0,0,0,calc(({{box_shadow}} * 0.1) / 2)); */
      border-radius: {{radius_ratio}}px {{radius_ratio}}px 0 0;
    }
    #sincloBox.sp-preview {
      position: absolute;
      width: auto;
      bottom: 0;
      left: 5px;
      right: 5px;
    }
    #sincloBox.sp-preview.landscape {
      position: absolute;
      width: auto;
      bottom: 0;
      left: 0;
      right: 0;
    }
    #sincloBox.sp-preview.landscape #chatTalk {
      height: 189px;
    }
    #sincloBox.middleSize{
      width: 342.5px;
    }
    #sincloBox.largeSize{
      width: 400px;
    }
    /* タブアイコンフォント化対応start */
    @font-face {
      font-family: 'SincloFont';
      src: url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.eot?v=4.0.3');
      src: url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.eot?#iefix&v=4.0.3') format('embedded-opentype'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.woff?v=4.0.3') format('woff'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.ttf?v=4.0.3') format('truetype'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.svg?v=4.0.3#fontawesomeregular') format('svg');
      font-weight: normal;
      font-style: normal
    }

    .sinclo-fa {
      display: inline-block;
      font-family: SincloFont;
      font-style: normal;
      font-weight: normal;
      line-height: 1;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      cursor: pointer;
    }

    #device {
      position:relative;
      background-color: #fff;
    }

    #device.portrait {
       width:344px;		/* 横スクロールバー+ボーダー */
       height: 552px;
       padding:17px 17px 15px;
       border:4px solid #c9c9c9;
       border-top: 0;
       border-radius:50px;
       border-top-left-radius: 0;
       border-top-right-radius: 0;
       box-shadow:0 1px 24px #111;
    }

    #device.landscape {
      width:567px;		/* 横スクロールバー+ボーダー */
      height: 334px;
      padding:17px 17px 15px;
      border:4px solid #c9c9c9;
      border-left: 0px;
      border-radius:50px;
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      box-shadow:0 1px 24px #111;
    }

    .mac #device.portrait {
      width: 328px;
    }

    .mac #device.landscape {
      width:551px;
    }

    #device.portrait #wrapper {
      position: relative;
      overflow: hidden;
      height:450px;		/* ★iphone5 */
      border:3px solid #333;
      border-radius:2px;
    }
    #device.portrait #wrapper img#previewBack {
      position: absolute;
      width:100%;
      border:0;
    }
    #device.portrait #button {
      width:58px;
      height:58px;
      margin:15px auto 0;
    }

    #device.landscape #wrapper {
      position: relative;
      overflow: hidden;
      width: 450px;
      height:293px;		/* ★iphone5 */
      border:3px solid #333;
      border-radius:2px;
    }
    #device.landscape #wrapper img#previewBack {
      position: absolute;
      width:100%;
      border:0;
    }
    #device.landscape #button {
      width:58px;
      height:58px;
      position: absolute;
      top: 135px;
      right: 10px;
    }

    @media screen and ( min-width : 321px ){
      #device.portrait #button, #device.landscape #button {
        border:3px solid #ccc;
        border-radius:31px;
        background:linear-gradient(to bottom, #fcfcfc, #fff);
      }
    }
    /* タブアイコンフォント化対応end */
  </style>
  <div id="device" class="portrait" ng-if="showWidgetType === 3">
    <div id="wrapper"><?= $this->Html->image('sp-preview_page.png', ['id' => 'previewBack']) ?>
      <?= $this->element('MWidgetSettings/widget', ['isSpPreview' => true]); ?>
    </div>
    <div id="button"></div>
  </div>
  <?= $this->element('MWidgetSettings/widget'); ?>
  <!-- バナー -->
  <style>
    @font-face {
      font-family: 'SincloFont';
      src: url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.eot?v=4.0.3');
      src: url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.eot?#iefix&v=4.0.3') format('embedded-opentype'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.woff?v=4.0.3') format('woff'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.ttf?v=4.0.3') format('truetype'), url('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/fonts/fontawesome-webfont.svg?v=4.0.3#fontawesomeregular') format('svg');
      font-weight: normal;
      font-style: normal
    }

    .sinclo-fa {
      display: inline-block;
      font-family: SincloFont;
      font-style: normal;
      font-weight: normal;
      line-height: 1;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      cursor: pointer;
    }

    .sinclo-fa .fa-comment:before {
      content: "\f075"
    }
    #sincloBanner {
      position: relative;
      z-index: 1;
      height: 42px;
      width : {{getBannerWidth()}};
      background-color: {{main_color}};
      box-shadow: 0px 0px {{box_shadow}}px {{box_shadow}}px rgba(0,0,0,0.1);
      border-radius: {{radius_ratio}}px {{radius_ratio}}px {{radius_ratio}}px {{radius_ratio}}px;
      color: {{string_color}};
      margin: auto;
      filter:alpha(opacity=90);
      -moz-opacity: 0.9;
      opacity: 0.9;
      top: -30px;
      cursor: pointer;
    }
    #sincloBannerText{
      line-height: 42px;
      height: auto!important;
      width: auto!important;
      padding:0;
    }
    #sincloBanner i{
      color: {{string_color}};
    }
    #sincloBanner #sinclo-comment{
      transform: scale( 1 , 1.4 );
      font-size: 17.5px;
      padding: 0 2px 0 7px;
      cursor: pointer;
    }
    #sincloBanner, #bannertext{
      font-size: 12.5px;
      padding: 0 0 0 3px;
      cursor: pointer;
      color: {{string_color}};
    }
  </style>
  <div id="sincloBanner" ng-click="bannerSwitchWidget()" ng-if="closeButtonModeTypeToggle === '1' && closeButtonSettingToggle === '2' && showWidgetType === 4">
    <div id="sincloBannerText" ng-click="bannerSwitchWidget()">
      <div ng-click="bannerSwitchWidget()"><i id="sinclo-comment" class="sinclo-fa fa-comment"></i><span id="bannertext">{{bannertext}}</span></div>
    </div>
  </div>
  <!-- バナー -->

<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
  <div id="device" class="landscape" ng-if="showWidgetType === 2">
    <div id="wrapper"><?= $this->Html->image('sp-preview_page.png', ['id' => 'previewBack']) ?>
      <?= $this->element("MWidgetSettings/widget_sp_landscape", ['isSpPreview' => true]) ?>
    </div>
    <div id="button"></div>
  </div>
<?php endif; ?>
<!-- スマホ版 -->

</section>
