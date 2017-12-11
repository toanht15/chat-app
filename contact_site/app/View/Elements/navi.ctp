<?php
$monitorSelected = "";
$historySelected = "";
$settingSelected = "";
$chatSettingSelected = "";
$docSettingSelected = "";
$statisticsSelected = "";
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
    case 'MOperatingHours':
        $settingSelected = "selected";
        break;
    case 'MChatSettings':
    case 'MChatNotifications':
    case 'TAutoMessages':
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
        <li class="fLeft"><p><?= h($userInfo['display_name']) ?>さん</p></li>
        <li class="fRight" id="logout" onclick='location.href = "/Login/logout"'><p>ログアウト</p></li>
    </ul>
</div>
<!-- /* 上部カラーバー(ここまで) */ -->

<!-- /* システムアイコン（ここから） */ -->
<div id="sys-icon" class="card-shadow"><?= $this->Html->image('sinclo_square_logo.png', array('alt' => 'アイコン', 'width' => 54, 'height' => 48, 'style'=>'margin: 6px 3px; display: block'))?></div>
<!-- /* システムアイコン（ここまで） */ -->

<!-- /* サイドバー１（ここから） */ -->
<div id="sidebar-main" class="card-shadow">
    <div>
        <div class="icon <?=$monitorSelected?>">
            <?= $this->htmlEx->naviLink('ﾘｱﾙﾀｲﾑﾓﾆﾀ', 'monitor.png', ['href' => ['controller' => 'Customers', 'action' => 'index']]) ?>
        </div>
        <div class="icon <?=$historySelected?>">
            <?= $this->htmlEx->naviLink('履歴一覧', 'history.png', ['href' => ['controller' => 'Histories', 'action' => 'clearSession']]) ?>
        </div>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
        <div class="icon <?=$statisticsSelected?> setting-icon" data-type="statistics" >
            <?= $this->htmlEx->naviLink('統計', 'graph.png') ?>
        </div>
        <?php endif; ?>
        <div class="icon <?=$settingSelected?> setting-icon" data-type="common">
            <?= $this->htmlEx->naviLink('設定', 'setting.png') ?>
        </div>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
        <div class="icon <?=$chatSettingSelected?> setting-icon" data-type="chat">
            <?= $this->htmlEx->naviLink('ﾁｬｯﾄ設定', 'chat_setting.png') ?>
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
        <div class="icon">
            <?= $this->htmlEx->naviLink('個人設定', 'personal.png', ['href' => ['controller' => 'PersonalSettings', 'action' => 'index']]) ?>
        </div>
    <?php if ( $adminFlg ): ?>
        <div class="icon" style="display:none">
            <?= $this->htmlEx->naviLink('企業設定', 'company.png', ['href' => ['controller' => 'Customers', 'action' => 'index']]) ?>
        </div>
        <div class="icon">
            <?= $this->htmlEx->naviLink('ユーザー管理', 'users.png', ['href' => ['controller' => 'MUsers', 'action' => 'index']]) ?>
        </div>
    <?php endif; ?>
        <div class="icon">
            <?= $this->htmlEx->naviLink($codeAndDemoTitle, 'script.png', ['href' => ['controller' => 'ScriptSettings', 'action' => 'index']]) ?>
        </div>
    <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('営業時間設定', 'operating_hour.png', ['href' => ['controller' => 'MOperatingHours', 'action' => 'index']]) ?>
        </div>
        <div class="icon">
            <?= $this->htmlEx->naviLink('ウィジェット', 'widget.png', ['href' => ['controller' => 'MWidgetSettings', 'action' => 'index']]) ?>
        </div>
      <div class="icon">
        <?= $this->htmlEx->naviLink('キャンペーン', 'campaign.png', ['href' => ['controller' => 'TCampaigns', 'action' => 'index']]) ?>
      </div>
      <div class="icon">
        <?= $this->htmlEx->naviLink('表示除外設定', 'exclusion.png', ['href' => ['controller' => 'DisplayExclusions', 'action' => 'index']]) ?>
      </div>
    <?php endif; ?>
    </div>
    <!-- /* 共通 */ -->

    <!-- /* チャット */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <div data-sidebar-type="chat" class="hide">
      <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('基本設定', 'chat_setting.png', ['href' => ['controller' => 'MChatSettings', 'action' => 'index']]) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('ｵｰﾄﾒｯｾｰｼﾞ', 'auto_message.png', ['href' => ['controller' => 'TAutoMessages', 'action' => 'index']]) ?>
        </div>
      <?php endif; ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('定型文', 'dictionary.png', ['href' => ['controller' => 'TDictionaries', 'action' => 'index']]) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('チャット通知', 'notification.png', ['href' => ['controller' => 'MChatNotifications', 'action' => 'index']]) ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- /* チャット */ -->
    <!-- /* 統計 */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
      <div data-sidebar-type="statistics" class="hide">
        <div class="icon">
          <?= $this->htmlEx->naviLink('チャット', 'chat_setting.png', ['href' => ['controller' => 'Statistics', 'action' => 'forChat']]) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('オペレータ', 'personal.png', ['href' => ['controller' => 'Statistics', 'action' => 'forOperator']]) ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- /*  統計 */ -->
</div>
<!-- /* サイドバー２（ここまで） */ -->

<script type="text/javascript">
  var nowOpenType = "";
    $(".setting-icon").click(function(){
      var type = $(this).data("type");
      if (nowOpenType === type) {
        $("#sidebar-sub").removeClass('open');
        $("#sidebar-sub > div").addClass("hide");
        nowOpenType = "";
      }
      else {
        if ( $("#sidebar-sub").is(".open") ) {
          $("#sidebar-sub").removeClass('open');
          $("#sidebar-sub > div").addClass("hide");
          setTimeout(function(){
            $("#sidebar-sub div[data-sidebar-type='"+type+"']").removeClass("hide");
            $("#sidebar-sub").addClass('open');
            nowOpenType = type;
          }, 100);
        }
        else {
          $("#sidebar-sub div[data-sidebar-type='"+type+"']").removeClass("hide");
          $("#sidebar-sub").addClass('open');
          nowOpenType = type;
        }

      }

  });
</script>
<?php if(strcmp($_SERVER['SERVER_NAME'], '	sinclo.jp') === 0): ?>
<script type='text/javascript' src='https://ws1.sinclo.jp/client/5a2e2a75cb7e3.js' data-hide='1'></script>
<script>
  document.addEventListener('sinclo:connected', function(evt) {
// この部分が動作したタイミングは
// sincloの接続処理が完了して番号を取得できる状態となっている
    var accessId = window.sinclo.api.getAccessId();
// 上記のaccessIdの値を表示したい箇所に挿入
    $('#supportNumberArea').text(accessId);
  });
</script>
<?php endif; ?>