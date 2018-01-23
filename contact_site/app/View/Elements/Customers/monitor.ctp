<!-- タイトル -->
<div id='customer_title'>
    <div class="fLeft"><?= $this->Html->image('monitor_g.png', array('alt' => 'リアルタイムモニタ', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>リアルタイムモニタ
<?php if ( $widgetCheck ){ ?>
    <span>（待機中の人数：{{oprCnt}}人／離席中の人数：{{oprWaitCnt-oprCnt}}人）</span>
<?php } else { ?>
    <span>（待機中のオペレータ人数：{{oprWaitCnt}}人）</span>
<?php } ?>
    </h1>
</div>
<!-- タイトル -->
<div id='customer_menu'>
    <div>
        <!-- 検索窓 -->
        <?php if ($coreSettings[C_COMPANY_USE_CHAT] || $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) ) : ?>
          <div class="form01 fLeft">
            <?php if ($coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT])) : ?>
            <ul class="switch" ng-init="fillterTypeId=1">
              <li ng-class="{on:fillterTypeId===1}" ng-click="fillterTypeId = 1">
                <svg width="15" height="15">
                  <path d="M 4 9 C 4 8 3 2 9 4" stroke-width="1" fill="none"></path>
                  <circle cx="7" cy="7" r="6" fill="none" stroke-width="2"></circle>
                  <line x1="11" y1="11" x2="15" y2="15" stroke-width="2"></line>
                </svg>ID
              </li>
              <li ng-class="{on:fillterTypeId===2}" ng-click="fillterTypeId = 2">
                <svg width="15" height="15">
                  <path d="M 4 9 C 4 8 3 2 9 4" stroke-width="1" fill="none"></path>
                  <circle cx="7" cy="7" r="6" fill="none" stroke-width="2"></circle>
                  <line x1="11" y1="11" x2="15" y2="15" stroke-width="2"></line>
                </svg>訪問ユーザ
              </li>
            </ul>
            <?= $this->Form->input('searchText', array('type'=>'text', 'label' => false, 'ng-model' => 'searchText', 'ng-attr-placeholder' => '{{searchTextPlaceholder()}}')); ?>
            <div id="userFilter" style="display: flex;">
            </div>
            <?php endif; ?>
            <?php if ( $coreSettings[C_COMPANY_USE_CHAT] && strcmp($displayType, C_WIDGET_DISPLAY_CODE_HIDE) !== 0 && strcmp($scFlg, C_SC_ENABLED) === 0) : ?>
            <div id="scInfo">
              チャット対応数上限 <span><?=$scNum?></span> 件 （残り対応可能数 <span>{{scInfo.remain}}</span> 件）
            </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <!-- 検索窓 -->
        <!-- 機能 -->
        <div class="w50 fRight tRight p20r">
            <?= $this->Html->link(
                    $this->Html->image('menu.png', array('alt' => 'メニュー', 'width'=>20, 'height'=>20)),
                    'javascript:void(0)',
                    array('escape' => false, 'ng-click'=>'openSetting()', 'class'=>'btn-shadow greenBtn')); ?>
        </div>
        <!-- 機能 -->
    </div>
    <div>
        <div id="statusMenuWrap">
        <ul id="color-bar-left" class="fLeft">
          <?php
          /*
           * リアルタイムモニタ画面にて、ウィジェットの表示方法を「オペレーターが待機中の時のみ表示する」に
           * している場合にのみ表示します。
           */
          $nowCntClass = "";
          if ( $widgetCheck && strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0 ) {
            $nowCntClass = "m20t";
            echo '<li id="opStatus">現在のステータス</li>';
            if ( $opStatus ) {
              echo "<li id='operatorStatus' class='opWait'><span>待機中</span></li>";
              echo "<li id='changeOpStatus' ng-click='chgOpStatus()' data-status='" . $opStatus. "' class='redBtn btn-shadow'>離席中にする</li>";
            }
            else {
              echo "<li id='operatorStatus' class='opStop'><span>離席中</span></li>";
              echo "<li id='changeOpStatus' ng-click='chgOpStatus()' data-status='" . $opStatus. "' class='blueBtn btn-shadow'>待機中にする</li>";
            }
          }
          ?>
        </ul>
        <?php if($widgetCheck && strcmp($userInfo['permission_level'], C_AUTHORITY_NORMAL) !== 0): ?>
          <div id="presenceMenuWrap">
            <ul>
              <li id='showOperatorPresenceBtn' class='blueBtn btn-shadow' ng-click='showOperatorPresence()'>オペレータステータス一覧を開く</li>
            </ul>
          </div>
        </div>
        <?php endif; ?>
        <?php if(empty($coreSettings[C_COMPANY_USE_HIDE_REALTIME_MONITOR]) || !$coreSettings[C_COMPANY_USE_HIDE_REALTIME_MONITOR] ): ?>
        <p class="tRight <?=$nowCntClass?>" ng-cloak>現在 <b>{{objCnt(monitorList)}}</b>名がサイト訪問中</p>
        <?php endif; ?>
    </div>
</div>

<!-- リスト -->
<div id='customer_list'>
  <div id="list_header">
    <table>
      <thead>
        <tr>
                <th style="width: 5em">状態
                  <div class="questionBalloon fRight">
                    <icon class="questionBtn">？</icon>
                    <icon-annotation>
                      <ul>
                        <li><?=$this->Html->image('tab_status_open.png', ['alt'=>'ウィジェットが開いている'])?>&emsp;<span>{{jsConst.tabInfoStr[jsConst.tabInfo.open]}}</span></li>
                        <li><?=$this->Html->image('tab_status_close.png', ['alt'=>'ウィジェットが閉じている'])?>&emsp;<span>{{jsConst.tabInfoStr[jsConst.tabInfo.close]}}</span></li>
                        <li><?=$this->Html->image('tab_status_none.png', ['alt'=>'ウィジェット非表示'])?>&emsp;<span>{{jsConst.tabInfoStr[jsConst.tabInfo.none]}}</span></li>
                        <li><?=$this->Html->image('tab_status_disable.png', ['alt'=>'非アクティブ'])?>&emsp;<span>{{jsConst.tabInfoStr[jsConst.tabInfo.disable]}}</span></li>
                      </ul>
                    </icon-annotation>
                  </div>
                </th>
                <th style="width: 3em" ng-hide="labelHideList.accessId">ID</th>
        <?php if (  $coreSettings[C_COMPANY_USE_SYNCLO] || $coreSettings[C_COMPANY_USE_DOCUMENT] || $coreSettings[C_COMPANY_USE_LA_CO_BROWSE] ) :?>
                <th style="width: 7em">操作</th>
        <?php endif ; ?>
                <th style="width: 7em">詳細</th>
                <?php if((isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA])) { ?>
                  <th style="width: 12em" ng-hide="labelHideList.ipAddress">IPアドレス</th>
                <?php } else { ?>
                   <th style="width: 8em" ng-hide="labelHideList.ipAddress">IPアドレス</th>
                <?php } ?>
                <th style="width: 8em" ng-hide="labelHideList.customer">訪問ユーザ</th>
                <th style="width: 9em" ng-hide="labelHideList.ua">プラットフォーム<br>ブラウザ</th>
                <th style="width: 5em" ng-hide="labelHideList.stayCount">訪問回数</th>
                <th style="width: 6em" ng-hide="labelHideList.time">アクセス日時</th>
                <th style="width: 5em" ng-hide="labelHideList.campaign">キャンペーン</th>
                <th style="width: 5em" ng-hide="labelHideList.stayTime">滞在時間</th>
                <th style="width: 7em" ng-hide="labelHideList.page">閲覧<br>ページ数</th>
                <th ng-hide="labelHideList.title">閲覧中ページ</th>
                <th ng-hide="labelHideList.referrer">参照元URL</th>
        </tr>
      </thead>
    </table>
  </div>
  <div id="list_body">
    <table fixed-header>
      <thead>
        <tr>
                <th style="width: 5em">状態</th>
                <th ng-hide="labelHideList.accessId" style="width: 3em">ID</th>
        <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) || (isset($coreSettings[C_COMPANY_USE_LA_CO_BROWSE]) && $coreSettings[C_COMPANY_USE_LA_CO_BROWSE]) ) :?>
                <th style="width: 7em">操作</th>
        <?php endif; ?>
                <th style="width: 7em">詳細</th>
                <?php if((isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA])) { ?>
                  <th ng-hide="labelHideList.ipAddress" style="width: 12em">IPアドレス</th>
                <?php } else { ?>
                  <th ng-hide="labelHideList.ipAddress" style="width: 8em">IPアドレス</th>
                <?php } ?>
                <th ng-hide="labelHideList.customer" style="width: 8em">訪問ユーザ</th>
                <th ng-hide="labelHideList.ua" style="width: 9em">プラットフォーム<br>ブラウザ</th>
                <th ng-hide="labelHideList.stayCount" style="width: 5em">訪問回数</th>
                <th ng-hide="labelHideList.time" style="width: 6em">アクセス日時</th>
                <th ng-hide="labelHideList.campaign" style="width: 5em">キャンペーン</th>
                <th ng-hide="labelHideList.stayTime" style="width: 5em">滞在時間</th>
                <th ng-hide="labelHideList.page" style="width: 7em">閲覧<br>ページ数</th>
                <th ng-hide="labelHideList.title">閲覧中ページ</th>
                <th ng-hide="labelHideList.referrer">参照元URL</th>
        </tr>
      </thead>
      <tbody ng-cloak>
        <tr ng-repeat="monitor in search(monitorList) | orderObjectBy : '-chatUnreadId-chat' | limitTo : 300:this" ng-dblclick="showDetail(monitor.tabId, monitor.sincloSessionId)" id="monitor_{{monitor.tabId}}">
          <!-- /* 状態 */ -->
          <td class="tCenter">
            <span ng-if="monitor.status === jsConst.tabInfo.open"><?=$this->Html->image('tab_status_open.png', ['alt'=>'', 'width'=>20, 'height'=>20])?></span>
            <span ng-if="monitor.status === jsConst.tabInfo.close"><?=$this->Html->image('tab_status_close.png', ['alt'=>'', 'width'=>20, 'height'=>20])?></span>
            <span ng-if="monitor.status === jsConst.tabInfo.none"><?=$this->Html->image('tab_status_none.png', ['alt'=>'', 'width'=>20, 'height'=>20])?></span>
            <span ng-if="monitor.status === jsConst.tabInfo.disable"><?=$this->Html->image('tab_status_disable.png', ['alt'=>'', 'width'=>20, 'height'=>20])?></span>
          </td>
          <!-- /* ID */ -->
          <td ng-hide="labelHideList.accessId" class="tCenter">{{monitor.accessId}}</td>
        <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] || (isset($coreSettings[C_COMPANY_USE_DOCUMENT]) && $coreSettings[C_COMPANY_USE_DOCUMENT]) || (isset($coreSettings[C_COMPANY_USE_LA_CO_BROWSE]) && $coreSettings[C_COMPANY_USE_LA_CO_BROWSE]) ) :?>
          <!-- /* 操作 */ -->
          <td class='tCenter'>
            <?php if ( strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0) :?>
              <span>
                <span ng-if="!monitor.connectToken&&!monitor.docShare&&!monitor.coBrowseConnectToken" id="shareToolBtn">
                  <a class='monitorBtn blueBtn btn-shadow' href='javascript:void(0)' ng-click='confirmSharingWindowOpen(monitor.tabId, monitor.accessId)' >共有</a>
                </span>
              </span>
              <span ng-if="monitor.connectToken||monitor.docShare||monitor.coBrowseConnectToken">
                <span class="monitorOn" ng-if="!monitor.responderId">対応中...</span>
                <span class="monitorOn" ng-if="monitor.responderId"><span class="bold">対応中</span><br>（{{setName(monitor.responderId)}}）</span>
              </span>
            <?php endif; ?>
          </td>
        <?php endif; ?>


          <!-- /* チャット */ -->
          <td class="tCenter" id="chatTypeBtn">
            <?php if ( strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0) :?>

              <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
              <span class="monitorOn" ng-if="monitor.chat === <?= h($muserId)?>"><span class="bold">対応中</span><br>（あなた）</span>
              <span class="monitorOn" ng-if="isset(monitor.chat) && monitor.chat !== <?= h($muserId)?>"><span class="bold">対応中</span><br>（{{setName(monitor.chat)}}）</span>
              <?php endif; ?>

              <span ng-if="monitor.tabId != detailId" ng-click="showDetail(monitor.tabId, monitor.sincloSessionId)" class="btn-shadow blueBtn ">
                開く
                <div class="unread" ng-if="monitor.chatUnreadCnt > 0">{{monitor.chatUnreadCnt}}</div>
              </span>
              <span ng-if="monitor.tabId == detailId" ng-click="showDetail(monitor.tabId, monitor.sincloSessionId)" class="btn-shadow redBtn ">
                閉じる
                <div class="unread" ng-if="monitor.chatUnreadCnt > 0">{{monitor.chatUnreadCnt}}</div>
              </span>
            <?php endif; ?>
          </td>
          <!-- /* 訪問ユーザ */ -->
          <td ng-hide="labelHideList.ipAddress" class="tCenter ref"><?php if ( isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA] ) :?><a href="javascript:void(0)"  class="underL" ng-click="openCompanyDetailInfo(monitor)" ng-if="monitor.orgName && monitor.lbcCode">{{monitor.orgName}}</a><span ng-if="monitor.orgName && !monitor.lbcCode">{{monitor.orgName}}</span><br ng-if="monitor.orgName"><?php endif; ?>{{ip(monitor)}}</td>
          <!-- /* 訪問ユーザ */ -->
          <td ng-hide="labelHideList.customer" class="tCenter pre">{{ui(monitor)}}</td>
          <!-- /* ユーザー環境 */ -->
          <td ng-hide="labelHideList.ua" class="tCenter pre">{{ua(monitor.userAgent)}}</td>
          <!-- /* 訪問回数 */ -->
          <td ng-hide="labelHideList.stayCount" class="tCenter">{{nn(monitor.tabId)}}</td>
          <!-- /* アクセス日時 */ -->
          <td ng-hide="labelHideList.time" class="tCenter">{{monitor.time | customDate}}</td>
          <!-- /* キャンペーン */ -->
          <td ng-hide="labelHideList.campaign" class="tCenter pre">{{::getCampaign(monitor.prev)}}</td>
          <!-- /* 滞在時間 */ -->
          <td ng-hide="labelHideList.stayTime" class="tCenter" cal-stay-time></td>
          <!-- /* 閲覧ページ数 */ -->
          <td ng-hide="labelHideList.page" class="tCenter">{{monitor.prev.length}}（<a href="javascript:void(0)" class="underL" ng-click="openHistory(monitor)" >移動履歴</a>）</td>
          <!-- /* 閲覧中ページ */ -->
          <td ng-hide="labelHideList.title" class="tLeft omit"><a href={{trimToURL(monitor.url)}} target="_blank" class="underL" ng-if="monitor.title">{{monitor.title}}</a><span ng-if="!monitor.title">{{trimToURL(monitor.url)}}</span></td>
          <!-- /* 参照元URL */ -->
          <td ng-hide="labelHideList.referrer" class="tLeft omit"><a href="{{::monitor.ref}}" target="_blank" class="underL" ng-if="monitor.processedRef">{{::monitor.processedRef}}</a></td>
        </tr>
      </tbody>
    </table>
  </div>
  <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>

</div>
<!-- リスト -->

<!-- 資料 -->
<div id="ang-popup">
  <div id="ang-base">
    <div id="ang-popup-background"></div>
    <div id="ang-popup-frame">
      <div id="ang-popup-content" class="document_list">
        <div id="title_area">資料一覧</div>
        <pre id="description_area" style="margin-bottom: 0">{{message}}</pre>
        <div id="search_area">
          <?=$this->Form->input('name', ['label' => 'フィルター：', 'ng-model' => 'searchName']);?>
          <!-- <ng-multi-selector></ng-multi-selector> -->
        </div>
        <div id="list_area">
          <ol>
            <li ng-repeat="document in docSearchFunc(documentList)" ng-click="shareDocument(document)">
              <div class="document_image">
                <img ng-src="{{::document.thumnail}}" ng-class="::setDocThumnailStyle(document)">
              </div>
              <div class="document_content">
                <h3>{{::document.name}}</h3>
                <ng-over-view docid="{{::document.id}}" text="{{::document.overview}}" ></ng-over-view>
                <ul><li ng-repeat="tagId in document.tags">{{::tagList[tagId]}}</li></ul>
              </div>
            </li>
          </ol>
        </div>
        <div id="btn_area">
          <a class="btn-shadow greenBtn" ng-click="closeDocumentList()" href="javascript:void(0)">閉じる</a>
        </div>
      </div>
    </div>
    <div id="ang-ballons">
    </div>
  </div>
</div>
<!-- 資料 -->

