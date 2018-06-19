<?php
$monitorSelected = "";
$historySelected = "";
$settingSelected = "";
$chatSettingSelected = "";
$docSettingSelected = "";
$statisticsSelected = "";
$chatbotSelected = "";
switch ($this->name) {
    case 'Customers':
        $monitorSelected = "selected";
        break;
    case 'Histories':
        $historySelected = "selected";
        break;
    case 'MUsers':
    case 'PersonalSettings':
    case 'MWidgetSettings':
    case 'ScriptSettings':
    case 'TCampaigns':
    case 'DisplayExclusions':
        $settingSelected = "selected";
        break;
    case 'TAutoMessages':
    case 'TChatbotScenario':
        $chatbotSelected = "selected";
        break;
    case 'MChatSettings':
    case 'MOperatingHours':
    case 'MChatNotifications':
    case 'TDictionaries':
        $chatSettingSelected = "selected";
        break;
    case 'TDocuments':
        $docSettingSelected = "selected";
        break;
    case 'Statistics':
        $statisticsSelected = "selected";
        break;
};
$codeAndDemoTitle = ( $adminFlg ) ? "コード・デモ" : "デモサイト" ;
?>
<!-- /* 上部カラーバー(ここから) */ -->
<div id="color-bar" class="card-shadow">
    <ul id="color-bar-right" class="fRight">
      <?php if(!empty($trialTime)) { ?>
        <li class="fLeft"><p style = "color: #c00000; font-weight:bold;margin-left: -265px !important;margin: 14px 0;"><?= 'トライアル期間終了まであと ' ?><span style = "color: #c00000; font-size: 19px;"><?= h($trialTime) ?></span><?= ' 日です'?></p></li>
      <?php } ?>
        <li class="fLeft" id = "menu-bar-right"><p style = "cursor:pointer;display:flex;"><?= h($userInfo['display_name']) ?>さん<i class='fal fa-angle-up fa-2x' style = "color:#fff;line-height: 0.6;margin-left: 6px;"></i></p></li>
    </ul>
</div>
<div id="colorBarMenu" style = "display:none;">
  <ul>
    <li class="t-link" onclick="editPersonalInfo()">
      <?= $this->Html->image('personal_g.png', array('alt' => 'プロフィール', 'width' => 30, 'height' => 30)) ?>
      <a href="javascript:void(0)">
        プロフィール
      </a>
    </li>
    <hr class="separator">
    <li class="t-link" onclick="window.open('https://info.sinclo.jp/manual/',target = '_blank')">
      <?= $this->Html->image('manual_g.png', array('alt' => 'ヘルプ', 'width' => 30, 'height' => 30)) ?>
      <a href="javascript:void(0)">
        ヘルプ
      </a>
    </li>
    <hr class="separator">
    <li class="t-link" onclick="window.open('<?= $this->Html->url(['controller' => 'Login', 'action' => 'logout']) ?>')">
      <?= $this->Html->image('logout_g.png', array('alt' => 'ログアウト', 'width' => 30, 'height' => 30)) ?>
      <a href="javascript:void(0)">
        ログアウト
      </a>
    </li>
  </ul>
</div>
<!-- /* 上部カラーバー(ここまで) */ -->

<!-- /* システムアイコン（ここから） */ -->
<div id="sys-icon" class="card-shadow"><?= $this->Html->image('sinclo_square_logo.png', array('alt' => 'アイコン', 'width' => 54, 'height' => 48, 'style'=>'margin: 6px 13px; display: block'))?></div>
<!-- /* システムアイコン（ここまで） */ -->

<!-- /* サイドバー１（ここから） */ -->
<div id="sidebar-main" class="card-shadow">
    <div>
        <div class="icon <?=$monitorSelected?>">
            <?= $this->htmlEx->naviLink('ﾘｱﾙﾀｲﾑﾓﾆﾀ', 'monitor.png', ['href' => ['controller' => 'Customers', 'action' => 'index']]) ?>
        </div>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
          <div class="icon <?=$historySelected?> setting-icon" data-type="history">
            <?= $this->htmlEx->naviLink('履歴一覧', 'history.png') ?>
          </div>
        <?php endif; ?>
        <?php if (!$coreSettings[C_COMPANY_USE_CHAT]) : ?>
          <div class="icon <?=$historySelected?>">
            <?= $this->htmlEx->naviLink('履歴一覧', 'history.png', ['href' => ['controller' => 'Histories', 'action' => 'clearSession']]) ?>
          </div>
        <?php endif; ?>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
        <div class="icon <?=$statisticsSelected?> setting-icon" data-type="statistics" >
            <?= $this->htmlEx->naviLink('統計', 'graph.png') ?>
        </div>
        <?php endif; ?>
        <div class="icon <?=$settingSelected?> setting-icon" data-type="common">
            <?= $this->htmlEx->naviLink('基本設定', 'setting.png') ?>
        </div>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
          <?php if ( $adminFlg ): ?>
            <div class="icon <?=$chatbotSelected?> setting-icon new-line" data-type="chatbot">
              <?= $this->htmlEx->naviLink('ﾁｬｯﾄﾎﾞｯﾄ設定', 'scenario_setting.png') ?>
            </div>
          <?php endif; ?>
          <div class="icon <?=$chatSettingSelected?> setting-icon new-line" data-type="chat">
              <?= $this->htmlEx->naviLink('有人ﾁｬｯﾄ設定', 'chat_setting.png') ?>
          </div>
        <?php endif; ?>
      <?php if ($adminFlg && isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]): ?>
        <div class="icon <?=$docSettingSelected?>">
          <?= $this->htmlEx->naviLink('資料設定', 'document.png', ['href' => ['controller' => 'TDocuments', 'action' => 'index']]) ?>
        </div>
      <?php endif; ?>
      <div class="bottom-area">
        <hr class="separator"/>
        <div class="icon">
          <?= $this->htmlEx->naviLink('お知らせ', 'info.png', ['href' => 'https://info.sinclo.jp/news/', 'target' => '_blank']) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('ヘルプ', 'manual.png', ['href' => 'https://info.sinclo.jp/manual/', 'target' => '_blank']) ?>
        </div>
      </div>
    </div>
  <div id="supportNumberArea" style="width:100%; color:#FFF; position: absolute; bottom: 5px; text-align: center;"></div>
</div>
<!-- /* サイドバー１（ここまで） */ -->

<!-- /* サイドバー２（ここから） */ -->
<div id="sidebar-sub" class="card-shadow">
    <!-- /* 共通 */ -->
    <div data-sidebar-type="common" class="hide">
        <?php if ( $adminFlg ): ?>
            <div class="icon" style="display:none">
                <?= $this->htmlEx->naviLink('企業設定', 'company.png', ['href' => ['controller' => 'Customers', 'action' => 'index']]) ?>
            </div>
            <div class="icon">
                <?= $this->htmlEx->naviLink('ユーザー管理', 'users.png', ['href' => ['controller' => 'MUsers', 'action' => 'index']]) ?>
            </div>
            <div class="icon">
                <?= $this->htmlEx->naviLink('ウィジェット', 'widget.png', ['href' => ['controller' => 'MWidgetSettings', 'action' => 'index']]) ?>
            </div>
        <?php endif; ?>
        <div class="icon">
            <?= $this->htmlEx->naviLink($codeAndDemoTitle, 'script.png', ['href' => ['controller' => 'ScriptSettings', 'action' => 'index']]) ?>
        </div>
    <?php if ( $adminFlg ): ?>
      <?php //シェアリングプランの場合
        if(!$coreSettings[C_COMPANY_USE_CHAT] && ($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]))): ?>
          <div class="icon">
            <?= $this->htmlEx->naviLink('営業時間設定', 'operating_hour.png', ['href' => ['controller' => 'MOperatingHours', 'action' => 'index']]) ?>
          </div>
        <?php endif; ?>
      <div class="icon">
        <?= $this->htmlEx->naviLink('キャンペーン', 'campaign.png', ['href' => ['controller' => 'TCampaigns', 'action' => 'index']]) ?>
      </div>
      <div class="icon">
        <?= $this->htmlEx->naviLink('表示除外設定', 'exclusion.png', ['href' => ['controller' => 'DisplayExclusions', 'action' => 'index']]) ?>
      </div>
      <div class="icon">
        <?= $this->htmlEx->naviLink('セキュリティ', 'security_settings_menu.png', ['href' => ['controller' => 'MSecuritySettings', 'action' => 'edit']]) ?>
      </div>
    <?php endif; ?>
    </div>
    <!-- /* 共通 */ -->

    <!-- /* チャット */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <div data-sidebar-type="chat" class="hide">
      <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('基本設定', 'gear.png', ['href' => ['controller' => 'MChatSettings', 'action' => 'index']]) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('営業時間設定', 'operating_hour.png', ['href' => ['controller' => 'MOperatingHours', 'action' => 'index']]) ?>
        </div>
      <?php endif; ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('定型文', 'dictionary.png', ['href' => ['controller' => 'TDictionaries', 'action' => 'index']]) ?>
        </div>
      <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('ファイル送信', 'file_transfer_setting_menu.png', ['href' => ['controller' => 'MFileTransferSetting', 'action' => 'edit']]) ?>
        </div>
      <?php endif; ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('チャット通知', 'notification.png', ['href' => ['controller' => 'MChatNotifications', 'action' => 'index']]) ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- /* チャット */ -->
    <!-- /* シナリオ */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <div data-sidebar-type="chatbot" class="hide">
      <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('ｵｰﾄﾒｯｾｰｼﾞ', 'auto_message.png', ['href' => ['controller' => 'TAutoMessages', 'action' => 'index']]) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('シナリオ設定', 'flow.png', ['href' => ['controller' => 'TChatbotScenario', 'action' => 'index']]) ?>
        </div>
      <?php endif; ?>
      </div>
    <?php endif; ?>
    <!-- /* シナリオ */ -->
    <!-- /* 履歴 */ -->
    <div data-sidebar-type="history" class="hide">
      <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('チャット履歴', 'chat_setting.png', ['href' => ['controller' => 'ChatHistories', 'action' => 'clearSession'], 'onclick' => 'window.loading.load.start()']) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('アクセス履歴', 'personal.png', ['href' => ['controller' => 'Histories', 'action' => 'clearSession'], 'onclick' => 'window.loading.load.start()']) ?>
        </div>
      <?php endif; ?>
      </div>
    <!-- /* 履歴 */ -->
    <!-- /* 統計 */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
      <div data-sidebar-type="statistics" class="hide">
        <div class="icon">
          <?= $this->htmlEx->naviLink('チャット', 'chat_setting.png', ['href' => ['controller' => 'Statistics', 'action' => 'forChat'], 'onclick' => 'window.loading.load.start()']) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('オペレータ', 'personal.png', ['href' => ['controller' => 'Statistics', 'action' => 'forOperator'], 'onclick' => 'window.loading.load.start()']) ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- /*  統計 */ -->
</div>
<!-- /* サイドバー２（ここまで） */ -->
<script type="text/javascript">
  var nowOpenType = "";
  var clickMenu = false;

  var hideTimer = null;
  $("#sidebar-main .icon:not(.setting-icon)").mouseenter(function(){
    console.log("#sidebar-main .icon:not(.setting-icon)");
    if(hideTimer) {
      clearTimeout(hideTimer);
      hideTimer = null;
    }
    setTimeout(function(){
      $("#sidebar-sub").removeClass('open');
      $("#sidebar-sub > div").addClass("hide");
      nowOpenType = "";
    }, 100);
  });

  $(".setting-icon").mouseenter(function(){
    var type = $(this).data("type");
    if ( $("#sidebar-sub").is(".open") ) {
      $("#sidebar-sub").removeClass('open');
      $("#sidebar-sub > div").addClass("hide");
      setTimeout(function(){
        $("#sidebar-sub > div").addClass("hide");
        $("#sidebar-sub div[data-sidebar-type='"+type+"']").removeClass("hide");
        $("#sidebar-sub").addClass('open');
        nowOpenType = type;
      }, 100);
    }
    else {
      $("#sidebar-sub > div").addClass("hide");
      $("#sidebar-sub div[data-sidebar-type='"+type+"']").removeClass("hide");
      $("#sidebar-sub").addClass('open');
      nowOpenType = type;
    }
  });
  $('#header').mouseleave(function(){
    if(nowOpenType !== "") {
      $("#sidebar-sub").removeClass('open');
      $("#sidebar-sub > div").addClass("hide");
      nowOpenType = "";
    }
  });

  var fadeOutLayerMenu = function() {
    $("#colorBarMenu").fadeOut("fast");
  };

  var fadeInLayerMenu = function() {
    console.log('fadein');
    $("#colorBarMenu").fadeIn("fast");
  };

  $('#menu-bar-right').on('click', function(e) {
    e.stopPropagation();
    //矢印下向きに変更
    $('.fal').toggleClass('downArrow');
    var menu = document.getElementById("colorBarMenu").style.display;
    if(menu == "block"){
      //メニュー非表示
      fadeOutLayerMenu();
      $("#menu-bar-right").css('background-color', '#C3D69B');
    }
    else{
      //メニュー表示
      fadeInLayerMenu();
      $("#menu-bar-right").css('background-color', '#D6E8B0');
    }
    clickMenu = true;
    $("#menu-bar-right").mouseenter(function(){
      $("#menu-bar-right").css('background-color', '#D6E8B0');
    });
    $('#menu-bar-right').mouseleave(function(){
      if(clickMenu === false) {
        $("#menu-bar-right").css('background-color', '#C3D69B');
      }
    });
  });


  $(document).on('click',function(){
    //メニュー非表示
    fadeOutLayerMenu();
    $('.fal').removeClass('downArrow');
    $("#menu-bar-right").css('background-color', '#C3D69B');
    clickMenu = false;
  });

  function editPersonalInfo(){
    $.ajax({
      type: 'post',
      dataType: 'html',
      cache: false,
      url: "<?= $this->Html->url('/PersonalSettings/remoteOpenEntryForm') ?>",
      success: function(html){
        modalOpen.call(window, html, 'p-personal-update', '個人設定', 'moment');
      },
      error: function(html) {
        console.log('error');
      }
    });
  }
</script>
