<?php
$monitorSelected = "";
$settingSelected = "";
$chatSettingSelected = "";
$docSettingSelected = "";
$statisticsSelected = "";
$chatbotSelected = "";
$otherSettingSelected = "";
switch ($this->name) {
    case 'Customers':
        $monitorSelected = "selected";
        break;
    case 'MUsers':
    case 'MWidgetSettings':
    case 'ScriptSettings':
        $settingSelected = "selected";
        break;
    case 'TAutoMessages':
    case 'TChatbotScenario':
        $chatbotSelected = "selected";
        break;
    case 'MChatSettings':
    case 'MOperatingHours':
    case 'TDictionaries':
    case 'MFileTransferSetting':
    case 'MChatNotifications':
        $chatSettingSelected = "selected";
        break;
    case 'TDocuments':
        $docSettingSelected = "selected";
        break;
    case 'ChatHistories':
    case 'Histories':
    case 'Statistics':
        $statisticsSelected = "selected";
        break;
    case 'TCustomerInformationSettings':
    case 'TCustomVariables':
    case 'TCampaigns':
    case 'DisplayExclusions':
    case 'MSecuritySettings':
        $otherSettingSelected = "selected";
        break;
};
$codeAndDemoTitle = ( $adminFlg ) ? "コード設置・デモサイト" : "デモサイト" ;
?>
<!-- /* 上部カラーバー(ここから) */ -->
<div id="color-bar">
  <ul id="color-bar-right" class="fRight">
    <?php if(!empty($trialTime)) { ?>
      <li class="fLeft"><p style = "color: #c00000; font-weight:bold;margin-left: -265px !important;margin: 14px 0;"><?= 'トライアル期間終了まであと ' ?><span style = "color: #c00000; font-size: 19px;"><?= h($trialTime) ?></span><?= ' 日です'?></p></li>
    <?php } ?>
      <li class="fLeft" id = "menu-bar-right"><p><i class="fal fa-user-circle fa-2x"></i><?= h($userInfo['display_name']) ?>さん<i class='fal fa-angle-down fa-2x'></i></p></li>
  </ul>
</div>
<div id="colorBarMenu">
  <ul>
    <li class="t-link" onclick="editPersonalInfo()">
      <i class="fal fa-user-circle fa-2x smallFal"></i>
      <a href="javascript:void(0)">
        プロフィール
      </a>
    </li>
    <hr class="separator">
    <li class="t-link" onclick="window.open('https://info.sinclo.jp/manual/',target = '_blank')">
      <i class="fal fa-book-open fa-2x smallFal"></i>
      <a href="javascript:void(0)">
        ヘルプ
      </a>
    </li>
    <hr class="separator">
    <li class="t-link" onclick = 'location.href = "/Login/logout"'>
      <i class="fal fa-sign-out-alt fa-2x smallFal"></i>
      <a href="javascript:void(0)">
        ログアウト
      </a>
    </li>
  </ul>
</div>
<!-- /* 上部カラーバー(ここまで) */ -->

<!-- /* システムアイコン（ここから） */ -->
<div id="sys-icon"><?= $this->Html->image('logo_sinclo_square.png', array('alt' => 'アイコン', 'width' => 48, 'height' => 48, 'style'=>'margin-left:4px; display: block'))?></div>
<!-- /* システムアイコン（ここまで） */ -->

<!-- /* サイドバー１（ここから） */ -->
<div id="sidebar-main">
    <div>
        <div class="icon <?=$monitorSelected?>">
            <?= $this->htmlEx->naviFaIconLink('ﾘｱﾙﾀｲﾑﾓﾆﾀ', 'fa-home', ['href' => ['controller' => 'Customers', 'action' => 'index']]) ?>
        </div>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
          <div class="icon <?=$statisticsSelected?> setting-icon" data-type="statistics" >
            <?= $this->htmlEx->naviFaIconLink('履歴・統計', 'fa-chart-pie') ?>
          </div>
        <?php endif; ?>
        <?php if (!$coreSettings[C_COMPANY_USE_CHAT]) : ?>
          <div class="icon <?=$statisticsSelected?>">
            <?= $this->htmlEx->naviFaIconLink('履歴・統計', 'fa-chart-pie', ['href' => ['controller' => 'Histories', 'action' => 'clearSession']]) ?>
          </div>
        <?php endif; ?>
        <div class="icon <?=$settingSelected?> setting-icon" data-type="common">
          <?= $this->htmlEx->naviFaIconLink('基本設定', 'fa-wrench') ?>
        </div>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
          <?php if ( $adminFlg ): ?>
            <div class="icon <?=$chatbotSelected?> setting-icon new-line" data-type="chatbot">
              <?= $this->htmlEx->naviFaIconLink('ﾁｬｯﾄﾎﾞｯﾄ</br>設定', 'fa-robot') ?>
            </div>
          <?php endif; ?>
          <div class="icon <?=$chatSettingSelected?> setting-icon new-line" data-type="chat">
            <?= $this->htmlEx->naviFaIconLink('有人ﾁｬｯﾄ</br>設定', 'fa-comment') ?>
          </div>
        <?php endif; ?>
        <?php if ($adminFlg && isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]): ?>
          <div class="icon <?=$docSettingSelected?>">
            <?= $this->htmlEx->naviFaIconLink('資料設定', 'fa-file-alt', ['href' => ['controller' => 'TDocuments', 'action' => 'index']]) ?>
          </div>
        <?php endif; ?>
          <div class="icon <?=$otherSettingSelected?> setting-icon new-line" data-type="other">
            <?= $this->htmlEx->naviFaIconLink('その他設定', 'fa-cogs') ?>
          </div>
      <div class="bottom-area">
        <hr class="separator"/>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('お知らせ', 'fa-bell', ['href' => 'https://info.sinclo.jp/news/', 'target' => '_blank']) ?>
        </div>
      </div>
    </div>
  <div id="supportNumberArea" style="width:100%; color:#FFF; position: absolute; bottom: 5px; text-align: center;"></div>
</div>
<!-- /* サイドバー１（ここまで） */ -->

<!-- /* サイドバー２（ここから） */ -->
<div data-sidebar-type="common" class="sidebar-sub hide">
    <!-- /* 共通 */ -->
    <div >
        <?php if ( $adminFlg ): ?>
            <div class="icon" style="display:none">
                <?= $this->htmlEx->naviFaIconLink('企業設定', 'company.png', ['href' => ['controller' => 'Customers', 'action' => 'index']], true) ?>
            </div>
            <div class="icon">
                <?= $this->htmlEx->naviFaIconLink('ユーザー管理', 'fa-user-friends', ['href' => ['controller' => 'MUsers', 'action' => 'index']], true) ?>
            </div>
            <div class="icon">
                <?= $this->htmlEx->naviFaIconLink('ウィジェット設定', 'fa-window-maximize', ['href' => ['controller' => 'MWidgetSettings', 'action' => 'index']], true) ?>
            </div>
        <?php endif; ?>
        <div class="icon">
            <?= $this->htmlEx->naviFaIconLink($codeAndDemoTitle, 'fa-code', ['href' => ['controller' => 'ScriptSettings', 'action' => 'index']], true) ?>
        </div>
    <?php if ( $adminFlg ): ?>
      <?php //シェアリングプランの場合
      if(!$coreSettings[C_COMPANY_USE_CHAT] && ($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]))): ?>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('営業時間設定', 'fa-calendar-alt', ['href' => ['controller' => 'MOperatingHours', 'action' => 'index']], true) ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
    </div>
    <!-- /* 共通 */ -->
</div>

<div data-sidebar-type="chat" class="sidebar-sub hide">
  <!-- /* チャット */ -->
  <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
    <div>
      <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('基本設定', 'fa-cog', ['href' => ['controller' => 'MChatSettings', 'action' => 'index']], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('営業時間設定', 'fa-calendar-alt', ['href' => ['controller' => 'MOperatingHours', 'action' => 'index']], true) ?>
        </div>
      <?php endif; ?>
      <div class="icon">
        <?= $this->htmlEx->naviFaIconLink('定型文管理', 'fa-book', ['href' => ['controller' => 'TDictionaries', 'action' => 'index']], true) ?>
      </div>
      <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('ファイル送信設定', 'fa-paperclip', ['href' => ['controller' => 'MFileTransferSetting', 'action' => 'edit']], true) ?>
        </div>
      <?php endif; ?>
      <div class="icon">
        <?= $this->htmlEx->naviFaIconLink('チャット通知設定', 'fa-info-circle', ['href' => ['controller' => 'MChatNotifications', 'action' => 'index']], true) ?>
      </div>
    </div>
  <?php endif; ?>
  <!-- /* チャット */ -->
</div>

<div data-sidebar-type="chatbot" class="sidebar-sub hide">
    <!-- /* シナリオ */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <div >
      <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('オートメッセージ設定', 'fa-comments', ['href' => ['controller' => 'TAutoMessages', 'action' => 'index']], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('シナリオ設定', 'fa-code-merge', ['href' => ['controller' => 'TChatbotScenario', 'action' => 'index']], true) ?>
        </div>
      <?php endif; ?>
      </div>
    <?php endif; ?>
    <!-- /* シナリオ */ -->
</div>
<div data-sidebar-type="statistics" class="sidebar-sub hide">
    <!-- /* 履歴・統計 */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
      <div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('チャット履歴', 'fa-history', ['href' => ['controller' => 'ChatHistories', 'action' => 'clearSession'], 'onclick' => 'window.loading.load.start()'], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('アクセス履歴', 'fa-history', ['href' => ['controller' => 'Histories', 'action' => 'clearSession'], 'onclick' => 'window.loading.load.start()'], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('チャット統計レポート', 'fa-chart-line', ['href' => ['controller' => 'Statistics', 'action' => 'forChat'], 'onclick' => 'window.loading.load.start()'], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('オペレータ統計レポート', 'fa-chart-line', ['href' => ['controller' => 'Statistics', 'action' => 'forOperator'], 'onclick' => 'window.loading.load.start()'], true) ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- /*  履歴・統計 */ -->
</div>
<div data-sidebar-type="other" class="sidebar-sub hide">
    <!-- /* その他設定 */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <div >
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('訪問ユーザ情報設定', 'fa-address-card', ['href' => ['controller' => 'TCustomerInformationSettings', 'action' => 'index']], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('カスタム変数設定', 'fa-percent', ['href' => ['controller' => 'TCustomVariables', 'action' => 'index']], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('キャンペーン設定', 'fa-trophy', ['href' => ['controller' => 'TCampaigns', 'action' => 'index']], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('表示除外設定', 'fa-minus-circle', ['href' => ['controller' => 'DisplayExclusions', 'action' => 'index']], true) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviFaIconLink('セキュリティ設定', 'fa-shield-alt', ['href' => ['controller' => 'MSecuritySettings', 'action' => 'edit']], true) ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- /* その他設定 */ -->
</div>
<!-- /* サイドバー２（ここまで） */ -->
<script type="text/javascript">
  var clickMenu = false;

  $(function(){
    var property = window.getComputedStyle($('a > i.icon')[0], '::before').getPropertyValue('width');
    console.log(property);  // 疑似要素取得
  });


  var pointtimes = 0;
  var duration_time = 260;
  $(".setting-icon").mouseenter(function(){
    pointtimes += 1;
    var type = $(this).data("type");
    var self = $(this);
    $('.sidebar-sub').stop(true,false).animate;
    $.when(
      $('.sidebar-sub').animate({left: -200}, duration_time)
    ).done(function(){
      $('.sidebar-sub').addClass('hide');
      $('[data-sidebar-type="' + type + '"]').removeClass('hide').offset({top: self.offset().top}).animate({left: 81}, duration_time);
    });
  });

  $("#sidebar-main div.icon:not(.setting-icon)").mouseenter(function(){
    $.when(
      $('.sidebar-sub').animate({left: -200}, duration_time)
    ).done(function(){
      $('.sidebar-sub').addClass('hide');
    });
  });

  $('#header').mouseleave(function(){
    $.when(
      $('.sidebar-sub').animate({left: -200},duration_time)
    ).done(function(){
      $('.sidebar-sub').addClass('hide');
    });
  });

  var fadeOutLayerMenu = function() {
    $("#colorBarMenu").slideUp(260);
  };

  var fadeInLayerMenu = function() {
    $("#colorBarMenu").slideToggle(260);
  };

  $("#menu-bar-right").mouseenter(function(){
    if($("#colorBarMenu").css('display') == 'none') {
      fadeInLayerMenu();
    }
    $("#menu-bar-right").css('background-color', '#D6E8B0');
  });
  $('#menu-bar-right').mouseleave(function(e){
      if(e.toElement.id !== 'colorBarMenu') {
        //メニュー非表示
        fadeOutLayerMenu();
        $("#menu-bar-right").css('background-color', '#C3D69B');
      }
  });
  $('#colorBarMenu').mouseleave(function(e){
    if(e.toElement.id !== 'menu-bar-right') {
      //メニュー非表示
      fadeOutLayerMenu();
      $("#menu-bar-right").css('background-color', '#C3D69B');
    }
  });
  $('.t-link').click(function(){
    $("#colorBarMenu").css('display', 'none');
    $("#menu-bar-right").css('background-color', '#C3D69B');
  });

  function editPersonalInfo(){
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/PersonalSettings/remoteOpenEntryForm') ?>",
      success: function(html){
        modalOpen.call(window, html, 'p-personal-update', 'プロフィール', 'moment');
      },
      error: function(html) {
        console.log('error');
      }
    });
  }
</script>