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
                    <?= $this->Html->image('search_g.png', array('alt' => 'アクセスID', 'width'=>20, 'height'=>20, 'class'=>'fLeft')); ?>
                </i>
                <?= $this->Form->input('searchText', array('type'=>'text', 'label' => false, 'ng-model' => 'searchText', 'placeholder' => 'アクセスID')); ?>
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
            $nowCntClass = "m40t";
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

    <table>
        <thead>
                <tr>
                        <th ng-hide="labelHideList.accessId" >アクセスID</th>
                <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
                        <th>モニター</th>
                <?php endif; ?>
                <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
                        <th>チャット</th>
                <?php endif; ?>
                        <th ng-hide="labelHideList.ipAddress" >訪問ユーザ</th>
                        <th ng-hide="labelHideList.ua" >ユーザー環境</th>
                        <th ng-hide="labelHideList.time" >アクセス日時</th>
                        <th ng-hide="labelHideList.stayTime" >滞在時間</th>
                        <th ng-hide="labelHideList.page" >閲覧ページ数</th>
                        <th ng-hide="labelHideList.title" >閲覧中ページ</th>
                        <th ng-hide="labelHideList.referrer" >参照元URL</th>
                </tr>
        </thead>
        <tbody ng-cloak>
                <tr ng-repeat="monitor in search(monitorList) | orderObjectBy : '-chatUnreadId'" ng-dblclick="showDetail(monitor.tabId)" id="monitor_{{monitor.tabId}}">
                    <!-- /* アクセスID */ -->
                    <td ng-hide="labelHideList.accessId" class="tCenter">{{monitor.accessId}}</td>
                <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] ) :?>
                    <!-- /* モニター */ -->
                    <td class='w10 tCenter'>
                        <?php if ( strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0) :?>
                            <span ng-if="monitor.widget">
                                <span ng-if="!monitor.connectToken">
                                    <a class='monitorBtn blueBtn btn-shadow' href='javascript:void(0)' ng-click='windowOpen(monitor.tabId, monitor.accessId)' ng-confirm-click='アクセスID【{{monitor.accessId}}】のユーザーに接続しますか？'>接続する</a>
                                </span>
                            </span>
                            <span ng-if="monitor.connectToken">
                                <span class="monitorOn" ng-if="!monitor.responderId">対応中...</span>
                                <span class="monitorOn" ng-if="monitor.responderId"><span class="bold">対応中</span><br>（{{setName(monitor.responderId)}}）</span>
                            </span>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>

                <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>

                <!-- /* チャット */ -->
                <td class="w10 tCenter" id="chatTypeBtn">
                    <?php if ( strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0) :?>

                        <span class="monitorOn" ng-if="monitor.chat === <?= h($muserId)?>"><span class="bold">対応中</span><br>（あなた）</span>
                        <span class="monitorOn" ng-if="isset(monitor.chat) && monitor.chat !== <?= h($muserId)?>"><span class="bold">対応中</span><br>（{{setName(monitor.chat)}}）</span>

                        <span ng-if="monitor.widget">
                          <span ng-if="monitor.tabId != detailId" ng-click="showDetail(monitor.tabId)" class="btn-shadow blueBtn ">
                            詳細を開く
                            <div class="unread" ng-if="monitor.chatUnreadCnt > 0">{{monitor.chatUnreadCnt}}</div>
                          </span>
                          <span ng-if="monitor.tabId == detailId" ng-click="showDetail(monitor.tabId)" class="btn-shadow redBtn ">
                            詳細を閉じる
                            <div class="unread" ng-if="monitor.chatUnreadCnt > 0">{{monitor.chatUnreadCnt}}</div>
                          </span>
                        </span>
                    <?php endif; ?>
                </td>
                <?php endif; ?>

                    <!-- /* 訪問ユーザ */ -->
                    <td ng-hide="labelHideList.ipAddress" class="tCenter">{{monitor.ipAddress}}</td>
                    <!-- /* ユーザー環境 */ -->
                    <td ng-hide="labelHideList.ua" class="tCenter">{{ua(monitor.userAgent)}}</td>
                    <!-- /* アクセス日時 */ -->
                    <td ng-hide="labelHideList.time" class="tCenter">{{monitor.time | customDate}}</td>
                    <!-- /* 滞在時間 */ -->
                    <td ng-hide="labelHideList.stayTime" class="tCenter" cal-stay-time></td>
                    <!-- /* 閲覧ページ数 */ -->
                    <td ng-hide="labelHideList.page" class="tCenter">{{monitor.prev.length}}（<a href="javascript:void(0)" class="underL" ng-click="openHistory(monitor)" >移動履歴</a>）</td>
                    <!-- /* 閲覧中ページ */ -->
                    <td ng-hide="labelHideList.title" class="tCenter omit"><a href={{monitor.url}} target="monitor" class="underL" ng-if="monitor.title">{{monitor.title}}</a><span ng-if="!monitor.title">{{monitor.url}}</span></td>
                    <!-- /* 参照元URL */ -->
                    <td ng-hide="labelHideList.referrer" class="tCenter omit"><span>{{monitor.referrer}}</span></td>
            </tr>
        </tbody>
    </table>
    <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>

</div>
<!-- リスト -->
