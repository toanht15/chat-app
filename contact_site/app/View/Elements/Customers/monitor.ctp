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
        <div class="form01 fLeft">
            <?php if ($coreSettings[C_COMPANY_USE_SYNCLO]) : ?>
                <i>
                    <?= $this->Html->image('search_g.png', array('alt' => 'ID', 'width'=>15, 'height'=>15, 'class'=>'fLeft')); ?>
                </i>
                <?= $this->Form->input('searchText', array('type'=>'text', 'label' => false, 'ng-model' => 'searchText', 'placeholder' => 'ID')); ?>
            <?php endif; ?>
        </div>
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
              echo "<li id='changeOpStatus' onclick='chgOpStatus()' data-status='" . $opStatus. "' class='redBtn btn-shadow'>離席中にする</li>";
            }
            else {
              echo "<li id='operatorStatus' class='opStop'><span>離席中</span></li>";
              echo "<li id='changeOpStatus' onclick='chgOpStatus()' data-status='" . $opStatus. "' class='blueBtn btn-shadow'>待機中にする</li>";
            }
          }
          ?>

        </ul>
        <p class="tRight <?=$nowCntClass?>" ng-cloak>現在 <b>{{objCnt(monitorList)}}</b>名がサイト訪問中</p>
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
                        <li><?=$this->Html->image('tab_status_open.png', ['alt'=>'ウィジェットが開いている'])?>&emsp;<span>{{tabStatusStr(jsConst.tabInfo.open)}}</span></li>
                        <li><?=$this->Html->image('tab_status_close.png', ['alt'=>'ウィジェットが閉じている'])?>&emsp;<span>{{tabStatusStr(jsConst.tabInfo.close)}}</span></li>
                        <li><?=$this->Html->image('tab_status_none.png', ['alt'=>'ウィジェット非表示'])?>&emsp;<span>{{tabStatusStr(jsConst.tabInfo.none)}}</span></li>
                        <li><?=$this->Html->image('tab_status_disable.png', ['alt'=>'非アクティブ'])?>&emsp;<span>{{tabStatusStr(jsConst.tabInfo.disable)}}</span></li>
                      </ul>
                    </icon-annotation>
                  </div>
                </th>
                <th style="width: 3em" ng-hide="labelHideList.accessId">ID</th>
        <?php if (  $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
                <th style="width: 7em">モニター</th>
        <?php endif ; ?>
                <th style="width: 7em">詳細</th>
                <th style="width: 8em" ng-hide="labelHideList.ipAddress">訪問ユーザ</th>
                <th style="width: 9em" ng-hide="labelHideList.ua">プラットフォーム<br>ブラウザ</th>
                <th style="width: 5em" ng-hide="labelHideList.stayCount">訪問回数</th>
                <th style="width: 6em" ng-hide="labelHideList.time">アクセス<br>日時</th>
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
        <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
                <th style="width: 7em">モニター</th>
        <?php endif; ?>
                <th style="width: 7em">詳細</th>
                <th ng-hide="labelHideList.ipAddress" style="width: 8em">訪問ユーザ</th>
                <th ng-hide="labelHideList.ua" style="width: 9em">プラットフォーム<br>ブラウザ</th>
                <th ng-hide="labelHideList.stayCount" style="width: 5em">訪問回数</th>
                <th ng-hide="labelHideList.time" style="width: 6em">アクセス日時</th>
                <th ng-hide="labelHideList.stayTime" style="width: 5em">滞在時間</th>
                <th ng-hide="labelHideList.page" style="width: 7em">閲覧<br>ページ数</th>
                <th ng-hide="labelHideList.title">閲覧中ページ</th>
                <th ng-hide="labelHideList.referrer">参照元URL</th>
        </tr>
      </thead>
      <tbody ng-cloak>
        <tr ng-repeat="monitor in search(monitorList) | orderObjectBy : '-chatUnreadId'" ng-dblclick="showDetail(monitor.tabId)" id="monitor_{{monitor.tabId}}">
          <!-- /* 状態 */ -->
          <td class="tCenter">
            <span ng-if="monitor.status === jsConst.tabInfo.open"><?=$this->Html->image('tab_status_open.png', ['alt'=>'', 'width'=>20, 'height'=>20])?></span>
            <span ng-if="monitor.status === jsConst.tabInfo.close"><?=$this->Html->image('tab_status_close.png', ['alt'=>'', 'width'=>20, 'height'=>20])?></span>
            <span ng-if="monitor.status === jsConst.tabInfo.none"><?=$this->Html->image('tab_status_none.png', ['alt'=>'', 'width'=>20, 'height'=>20])?></span>
            <span ng-if="monitor.status === jsConst.tabInfo.disable"><?=$this->Html->image('tab_status_disable.png', ['alt'=>'', 'width'=>20, 'height'=>20])?></span>
          </td>
          <!-- /* ID */ -->
          <td ng-hide="labelHideList.accessId" class="tCenter">{{monitor.accessId}}</td>
        <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
          <!-- /* モニター */ -->
          <td class='tCenter'>
            <?php if ( strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0) :?>
              <span ng-if="monitor.widget">
                <span ng-if="!monitor.connectToken">
                  <a class='monitorBtn blueBtn btn-shadow' href='javascript:void(0)' ng-click='windowOpen(monitor.tabId, monitor.accessId)' ng-confirm-click='ID【{{monitor.accessId}}】のユーザーに接続しますか？'>接続する</a>
                </span>
              </span>
              <span ng-if="monitor.connectToken">
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

              <span ng-if="monitor.tabId != detailId" ng-click="showDetail(monitor.tabId)" class="btn-shadow blueBtn ">
                詳細を開く
                <div class="unread" ng-if="monitor.chatUnreadCnt > 0">{{monitor.chatUnreadCnt}}</div>
              </span>
              <span ng-if="monitor.tabId == detailId" ng-click="showDetail(monitor.tabId)" class="btn-shadow redBtn ">
                詳細を閉じる
                <div class="unread" ng-if="monitor.chatUnreadCnt > 0">{{monitor.chatUnreadCnt}}</div>
              </span>
            <?php endif; ?>
          </td>

          <!-- /* 訪問ユーザ */ -->
          <td ng-hide="labelHideList.ipAddress" class="tCenter pre">{{ui(monitor)}}</td>
          <!-- /* ユーザー環境 */ -->
          <td ng-hide="labelHideList.ua" class="tCenter pre">{{ua(monitor.userAgent)}}</td>
          <!-- /* 訪問回数 */ -->
          <td ng-hide="labelHideList.stayCount" class="tCenter">{{monitor.stayCount}}</td>
          <!-- /* アクセス日時 */ -->
          <td ng-hide="labelHideList.time" class="tCenter">{{monitor.time | customDate}}</td>
          <!-- /* 滞在時間 */ -->
          <td ng-hide="labelHideList.stayTime" class="tCenter" cal-stay-time></td>
          <!-- /* 閲覧ページ数 */ -->
          <td ng-hide="labelHideList.page" class="tCenter">{{monitor.prev.length}}（<a href="javascript:void(0)" class="underL" ng-click="openHistory(monitor)" >移動履歴</a>）</td>
          <!-- /* 閲覧中ページ */ -->
          <td ng-hide="labelHideList.title" class="tLeft omit"><a href={{monitor.url}} target="_blank" class="underL" ng-if="monitor.title">{{monitor.title}}</a><span ng-if="!monitor.title">{{monitor.url}}</span></td>
          <!-- /* 参照元URL */ -->
          <td ng-hide="labelHideList.referrer" class="tLeft omit"><a href={{trimToURL(monitor.referrer)}} target="_blank" class="underL" ng-if="monitor.referrer">{{trimToURL(monitor.referrer)}}</a></td>
        </tr>
      </tbody>
    </table>
  </div>
  <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>

</div>
<!-- リスト -->
