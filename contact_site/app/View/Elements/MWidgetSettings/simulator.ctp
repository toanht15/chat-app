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
<p ng-if="widgetSizeTypeToggle==='4' && showWidgetType === 1" style="width:400px;">表示されているウィジェットは「大」サイズになります。実際に「最大」サイズのウィジェットをご確認いただくにはデモサイトをご覧ください。</p>
<section id="switch_widget" ng-cloak ng-class="{showBanner:closeButtonModeTypeToggle === '1' && closeButtonSettingToggle === '2' && showWidgetType === 4}">
  <ul class="ulTab" data-col=3 ng-hide="closeButtonSettingToggle === '2'">
    <li ng-class="{choose: showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    <li ng-class="{choose: showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
    <li ng-class="{choose: showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
  </ul>
  <ul class="ulTab showType4" data-col=3 ng-hide="closeButtonSettingToggle !== '2'" ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && (widgetSizeTypeToggle === '3' || widgetSizeTypeToggle === '4')}">
    <li ng-class="{choose: showWidgetType === 1}" ng-click="switchWidget(1)">通常</li>
    <li ng-class="{choose: showWidgetType === 3}" ng-click="switchWidget(3)">ｽﾏｰﾄﾌｫﾝ(縦)</li>
    <li ng-class="{choose: showWidgetType === 2}" ng-click="switchWidget(2)">ｽﾏｰﾄﾌｫﾝ(横)</li>
    <li ng-class="{choose: showWidgetType === 4}" ng-click="switchWidget(4)">非表示</li>
  </ul>
  <input type="hidden" id="switch_widget" value="">
</section>
<?php } else { ?>
<section id="switch_widget" ng-cloak ng-hide="closeButtonSettingToggle !== '2'" ng-class="{showBanner:closeButtonModeTypeToggle === '1' && closeButtonSettingToggle === '2' && showWidgetType === 4}">
  <ul class="ulTab showType4" data-col=3  ng-class="{middleSize: showWidgetType === 1 && widgetSizeTypeToggle === '2',largeSize: showWidgetType === 1 && (widgetSizeTypeToggle === '3' || widgetSizeTypeToggle === '4')}">
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

    #sincloBox.sp-preview.fullSize {
      position: absolute;
      width: auto;
      bottom: 0;
      left: 0;
      right: 0;
    }

    #sincloBox.sp-preview.fullSize #chatTalk {
      height: 258px;
      padding: 0px 5px 41.4px 5px;
    }

    #sincloBox.sp-preview.fullSize.simpleHeader #chatTalk {
      height: 310px;
      padding: 5px 5px 49.7px 5px;
    }

    #sincloBox.sp-preview.fullSize.simpleHeader.noTextarea #chatTalk {
      height: 385px;
      padding: 5px 5px 49.7px 5px;
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
    #sincloBox.sp-preview.landscape.noTextarea #chatTalk {
      height: 254px;
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
      src: url('/fonts/fontawesome-webfont.eot?v=4.0.3');
      src: url('/fonts/fontawesome-webfont.eot?#iefix&v=4.0.3') format('embedded-opentype'), url('/fonts/fontawesome-webfont.woff?v=4.0.3') format('woff'), url('/fonts/fontawesome-webfont.ttf?v=4.0.3') format('truetype'), url('/fonts/fontawesome-webfont.svg?v=4.0.3#fontawesomeregular') format('svg');
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
    <div id="wrapper">
      <?= $this->element('MWidgetSettings/widget', ['isSpPreview' => true]); ?>
    </div>
    <div id="button"></div>
  </div>
  <?= $this->element('MWidgetSettings/widget'); ?>
  <!-- バナー -->
  <style>
    #sincloBannerBox {
      background-color: rgb(255, 255, 255);
      border-radius: {{radius_ratio}}px {{radius_ratio}}px {{radius_ratio}}px {{radius_ratio}}px;
    }
    #sincloBanner:hover {
      opacity: 0.75 !important;
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
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      width: auto!important;
      margin: 0 5px;
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
      color: {{string_color}};
      font-size: 12.5px;
      cursor: pointer;
      vertical-align: middle;
      margin-right: 5px;
    }
  </style>
  <div id = "sincloBannerBox">
    <div id="sincloBanner" ng-click="bannerSwitchWidget()" ng-if="closeButtonModeTypeToggle === '1' && closeButtonSettingToggle === '2' && showWidgetType === 4">
      <div id="sincloBannerText" ng-click="bannerSwitchWidget()">
        <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="width: 24px; height: 24px; opacity: 1; margin: 0px 5px; flex-basis: 24px;" xml:space="preserve">
          <style type="text/css">
            .st0{fill:{{string_color}};}
          </style>
          <g>
            <path class="st0" d="M257.135,19.179C103.967,19.179,0,97.273,0,218.763c0,74.744,31.075,134.641,91.108,173.176 c4.004,2.572,8.728,2.962,6.955,10.365c-7.16,29.935-19.608,83.276-19.608,83.276c-0.527,2.26,0.321,4.618,2.162,6.03 c1.84,1.402,4.334,1.607,6.38,0.507c0,0,87.864-52.066,99.583-58.573c27.333-15.625,50.878-18.654,68.558-18.654 C376.619,414.89,512,366.282,512,217.458C512,102.036,418.974,19.179,257.135,19.179z" style="fill:{{string_color}}"></path>
          </g>
        </svg>
        <span id="bannertext">{{bannertext}}</span>
      </div>
    </div>
  </div>
  <!-- バナー -->

<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
  <div id="device" class="landscape" ng-if="showWidgetType === 2">
    <div id="wrapper">
      <?= $this->element("MWidgetSettings/widget_sp_landscape", ['isSpPreview' => true]) ?>
    </div>
    <div id="button"></div>
  </div>
<?php endif; ?>
<!-- スマホ版 -->

</section>
