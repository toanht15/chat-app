<?php echo $this->element('Customers/userAgentCheck') ?>
<?php echo $this->element('Customers/script') ?>
<?php echo $this->element('Customers/angularjs') ?>

<section id='customer_idx' class="{{customerMainClass}}" ng-app="sincloApp" ng-controller="MainCtrl" ng-cloak>

    <div id='customer_main' class="card-shadow">

        <div id='customer_title'>
            <div class="fLeft"><?= $this->Html->image('monitor_g.png', array('alt' => 'リアルタイムモニタ', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
            <h1>リアルタイムモニタ
        <?php if ( $widgetCheck ): ?>
            <span ng-show="oprCnt">（待機中のオペレーター人数：{{oprCnt}}人）</span><span ng-hide="oprCnt">（待機中のオペレーターが居ません）</span>
        <?php endif; ?>
            </h1>
        </div>

        <div id='customer_menu' class="p20tl">
            <!-- 検索窓 -->
            <div class="form01 fLeft">
                <span>
                    <?= $this->Html->image('search_g.png', array('alt' => 'アクセスID', 'width'=>20, 'height'=>20, 'class'=>'fLeft')); ?>
                </span>
                <?= $this->Form->input('searchText', array('type'=>'text', 'label' => false, 'ng-model' => 'searchText', 'placeholder' => 'アクセスID')); ?>
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

        <div id='customer_list' class="p20x">
            <table>
                <thead>
                    <tr>
                        <th ng-hide="labelHideList.accessId" >アクセスID</th>
                        <th ng-hide="labelHideList.ipAddress" >訪問ユーザ</th>
                        <th ng-hide="labelHideList.ua" >ユーザー環境</th>
                        <th ng-hide="labelHideList.time" >アクセス日時</th>
                        <th ng-hide="labelHideList.stayTime" >滞在時間</th>
                        <th ng-hide="labelHideList.page" >閲覧ページ数</th>
                        <th ng-hide="labelHideList.title" >閲覧中ページ</th>
                        <th ng-hide="labelHideList.referrer" >参照元URL</th>
                        <th>チャット</th>
                        <th>モニター</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="monitor in search(monitorList)" ng-dblclick="showDetail(monitor.tabId)" id="monitor_{{monitor.tabId}}">
                        <td ng-hide="labelHideList.accessId" class="tCenter">{{monitor.accessId}}</td>
                        <td ng-hide="labelHideList.ipAddress" class="tCenter">{{monitor.ipAddress}}</td>
                        <td ng-hide="labelHideList.ua" class="tCenter">{{ua(monitor.userAgent)}}</td>
                        <td ng-hide="labelHideList.time" class="tCenter">{{monitor.time | customDate}}</td>
                        <td ng-hide="labelHideList.stayTime" class="tCenter" cal-stay-time></td>
                        <td ng-hide="labelHideList.page" class="tCenter">{{monitor.prev.length}}（<a href="javascript:void(0)" ng-click="openHistory(monitor)" >移動履歴</a>）</td>
                        <td ng-hide="labelHideList.title" class="tCenter"><a href={{monitor.url}} target="monitor" ng-if="monitor.title">{{monitor.title}}</a><span ng-if="!monitor.title">{{monitor.url}}</span></td>
                        <td ng-hide="labelHideList.referrer" class="tCenter omit"><span>{{monitor.referrer}}</span></td>
                        <td class="w10 tCenter" id="chatTypeBtn">
                            <ng-show="monitor.widget">
                              <span ng-click="ngChatApi.connect(monitor)" class="btn-shadow blueBtn " ng-if="!isset(monitor.chat)">対応する
                                <div class="unread" ng-if="monitor.chatUnread.cnt > 0">{{monitor.chatUnread.cnt}}</div>
                              </span>
                              <span ng-click="ngChatApi.disConnect(monitor)" class="btn-shadow redBtn " ng-if="monitor.chat === <?= h($muserId)?>">対応を終わる
                                <div class="unread" ng-if="monitor.chatUnread.cnt > 0">{{monitor.chatUnread.cnt}}</div>
                              </span>
                              <span ng-if="isset(monitor.chat) && monitor.chat !== <?= h($muserId)?>">{{userList[monitor.chat]}}さん対応中</span>
                            </ng-show>
                        </td>
                        <td class='w10 tCenter'>
                            <span ng-show="monitor.widget">
                              <a   ng-if="!monitor.connectToken" class='monitorBtn blueBtn btn-shadow' href='javascript:void(0)' ng-click="windowOpen(monitor.tabId)" ng-confirm-click="アクセスID【{{monitor.accessId}}】のユーザーに接続しますか？">接続する</a>
                            </span>
                            <ng-hide="monitor.widget">
                              <span ng-if=" monitor.connectToken">モニタリング中</span>
                            </ng-hide>
                        </td>
                    </tr>
                </tbody>
            </table>
            <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>
        </div>

    </div>

    <div id='customer_sub'>
        <div class="card-shadow m10b">
            <div id='customer_subtitle'>
                <div class="fLeft"><?= $this->Html->image('sub_icon.png', array('alt' => '詳細情報', 'width' => 25, 'height' => 25, 'style' => 'margin: 0 15px 0 auto;transform: rotate(45deg);')) ?></div>
                <h1>詳細情報</h1>
            </div>
        </div>
        <div id="customer_detail" class="card-shadow m10b p10x">
            <h2>基本情報</h2>
            <ul class="p20l">
                <li>
                  <span>アクセスID：</span><br>
                    {{detailData.accessId}}
                </li>
                <li>
                  <span>IPアドレス：</span><br>
                    {{detailData.ipAddress}}
                </li>
                <li>
                  <span>ユーザーエージェント：</span><br>
                    {{detailData.userAgent}}
                </li>
                <li>
                  <span>閲覧中ページ：</span><br>
                  <a ng-href='{{detailData.url}}' target='showBlack'>{{detailData.title}}</a>
                </li>
            </ul>
        </div>
        <div id="chat-area" class="card-shadow p10x">
            <h2>チャット</h2>
            <ul class="naviBtn p0">
                <li class="w50 tCenter on">チャット</li>
                <li class="w50 tCenter">メモ</li>
            </ul>
            <div id="chatContent">
                <ul id="chatTalk" >
                </ul>
                <div style="position: relative" ng-if="detailData.chat === <?=h($muserId)?>">
                  <textarea rows="5" id="sendMessage" placeholder="問いかけ内容"></textarea>
                  <span id="sinclo_sendbtn" onclick="chatApi.pushMessage()">＋</span>
                </div>
            </div>
        </div>
    </div>
</section>


