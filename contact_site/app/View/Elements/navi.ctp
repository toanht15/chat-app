<?php
$monitorSelected = "";
$historySelected = "";
$settingSelected = "";
$chatSettingSelected = "";
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
    case 'MChatNotifications':
    case 'TAutoMessages':
    case 'TDictionaries':
        $chatSettingSelected = "selected";
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
            <?= $this->htmlEx->naviLink('履歴一覧', 'history.png', ['href' => ['controller' => 'Histories', 'action' => 'index']]) ?>
        </div>
        <div class="icon <?=$settingSelected?> setting-icon" data-type="common">
            <?= $this->htmlEx->naviLink('設定', 'setting.png') ?>
        </div>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
        <div class="icon <?=$chatSettingSelected?> setting-icon" data-type="chat">
            <?= $this->htmlEx->naviLink('ﾁｬｯﾄ設定', 'chat_setting.png') ?>
        </div>
      <?php endif; ?>
    </div>
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
            <?= $this->htmlEx->naviLink('ウィジェット', 'widget.png', ['href' => ['controller' => 'MWidgetSettings', 'action' => 'index']]) ?>
        </div>
    <?php endif; ?>
    <?php if ( $adminFlg ): ?>
      <div class="icon">
        <?= $this->htmlEx->naviLink('キャンペーン', 'campaign.png', ['href' => ['controller' => 'TCampaigns', 'action' => 'index']]) ?>
      </div>
    <?php endif; ?>
    <div class="icon">
        <?= $this->htmlEx->naviLink('表示除外設定', 'exclusion.png', ['href' => ['controller' => 'DisplayExclusions', 'action' => 'index']]) ?>
      </div>
    </div>
    <!-- /* 共通 */ -->

    <!-- /* チャット */ -->
    <?php if ($coreSettings[C_COMPANY_USE_CHAT] || strcmp($userInfo['MCompany']['company_key'], "medialink") === 0): ?>
      <div data-sidebar-type="chat" class="hide">
      <?php if ( $adminFlg ): ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('メッセージ', 'auto_message.png', ['href' => ['controller' => 'TAutoMessages', 'action' => 'index']]) ?>
        </div>
      <?php endif; ?>
      <?php if ($coreSettings[C_COMPANY_USE_CHAT] || strcmp($userInfo['MCompany']['company_key'], "medialink") === 0): ?>
        <div class="icon">
          <?= $this->htmlEx->naviLink('簡易入力', 'dictionary.png', ['href' => ['controller' => 'TDictionaries', 'action' => 'index']]) ?>
        </div>
        <div class="icon">
          <?= $this->htmlEx->naviLink('チャット通知', 'notification.png', ['href' => ['controller' => 'MChatNotifications', 'action' => 'index']]) ?>
        </div>
      <?php endif; ?>
      </div>
    <?php endif; ?>
    <!-- /* チャット */ -->
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
