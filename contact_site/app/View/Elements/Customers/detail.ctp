<div id="customer_sub" ng-customer>
  <div id="sub_contents">
    <div id="cus_info_header" class="noSelect">
      <h2>{{detail.accessId}}</h2>
      <div>
        <!-- 閉じる -->
        <a href="javascript:void(0)" ng-click="showDetail(detailId)" class="fRight customer_detail_btn redBtn btn-shadow">
          <?= $this->Html->image('close.png', ['alt'=>'チャットを終了する', 'width'=>20, 'height' => 20, 'ng-if="chatList.indexOf(detailId) < 0"']); ?>
          <?= $this->Html->image('minimize.png', ['alt'=>'詳細を閉じる', 'width'=>20, 'height' => 20, 'ng-if="chatList.indexOf(detailId) >= 0"']); ?>
        </a>
        <!-- 閉じる -->
      </div>
    </div>
    <div id="cus_info_contents" class="flexBoxCol">
      <?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
        <div id="leftContents">
          <ul id="showChatTab" class="tabStyle flexBoxCol noSelect">
            <li class="on" data-type="currentChat">現在のチャット</li>
            <li data-type="oldChat">過去のチャット</li>
          </ul>
          <div id="chatContent">

          <!-- 現在のチャット -->
            <section class="on" id="currentChat">
              <ul id="chatTalk" class="chatView">
                <chat-notificate>{{tabStatusNotificationMessage(detailId)}}</chat-notificate>
                <message-list>
                  <ng-create-message ng-repeat="chat in messageList | orderBy: 'sort'"></ng-create-message>
                </message-list>
                <typing-message>
                  <li class="sinclo_se typeing_message" ng-if="typingMessageSe !== ''">{{typingMessageSe}}</li>
                  <li class="sinclo_re typeing_message" ng-if="typingMessageRe[detailId] && typingMessageRe[detailId] !== ''">{{typingMessageRe[detailId]}}</li>
                </typing-message>
              </ul>
              <chat-detail ng-class="{showOption: showAchievement()}">
                <span>成果</span>
                <label class="pointer">
                  <?= $this->ngForm->input('achievement',
                        [
                          'type' => 'select',
                          'empty' => ' - ',
                          'options' => $achievementType,
                          'legend' => false,
                          'separator' => '</label><br><label class="pointer">',
                          'label' => false,
                          'error' => false,
                          'div' => false
                        ],[
                          'entity' => 'achievement',
                          'change' => 'changeAchievement()'
                        ]) ?>
                </label>
              </chat-detail>
              <chat-menu ng-class="{showOption: chatOptionDisabled(detailId)}">
                <chat-menu-child id="sendMenu" class="p05tb">
                  <label class="pointer" for="settingsSendPattarn"><?=$this->ngForm->input('settings.sendPattarn',
                      [
                        'type' => 'checkbox',
                        'div' => false,
                        'label' => false
                      ],
                      [
                        'default' => false,
                        'entity' => 'settings.sendPattarn',
                        'change' => 'changeSetting("sendPattarn")'
                      ])?>Enterキーで送信する</label>
                </chat-menu-child>
                <chat-menu-child id="chatMenu" class="p05tb" >
                  <span class="greenBtn btn-shadow" onclick="chatApi.addOption(1)">選択肢を追加する</span>
                </chat-menu-child>
              </chat-menu>
              <div id="sendMessageArea" ng-hide="chatAreaShowFlg !== true" style="position: relative">
                <?php if ( strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0) :?>
                  <textarea rows="5" id="sendMessage" ng-focus="sendMessageConnectConfirm(detailId)" maxlength="300" ng-attr-placeholder="{{chatPs()}}"></textarea>
                  <div id="wordListArea" ng-keydown="searchKeydown($event)">
                    <input type="text" ng-model="searchWord" id="wordSearchCond" />
                    <ul id="wordList">
                      <li ng-repeat="item in entryWordSearch(entryWordList)" id="item{{$index}}" ng-class="{selected: $index === entryWord}">{{item.label}}</li>
                      <li style="border:none; color:#ff7b7b" ng-if="entryWordList.length === 0">[設定] > [簡易入力] から<br>メッセージを登録してください</li>
                    </ul>
                  </div>
                  <div id="sendMessageAreaBtn" ng-class="{showOption: chatOptionDisabled(detailId)}">
                    <span id="sinclo_chatEndBtn" class="btn-shadow redBtn ng-binding" ng-click="confirmDisConnect(detailId)">退室</span>
                    <span id="sinclo_sendbtn" class="btn-shadow" onclick="chatApi.pushMessage()">{{chatSendBtnName()}}</span>
                  </div>
                <?php endif; ?>
              </div>
            </section>
            <!-- 現在のチャット -->

            <!-- 過去のチャット -->
            <section id="oldChat">
              <ul class="historyList">
                <li ng-click="getOldChat(historyId, true)" ng-repeat="(historyId, firstDate) in chatLogList"><span>{{firstDate | date:'yyyy年M月d日（EEE）a hh時mm分ss秒' }}</span></li>
              </ul>
              <div class="chatList">
                <ul>
                  <message-list class="chatView">
                    <message-list-descript>上から、表示したいチャット対応日時をクリックしてください</message-list-descript>
                    <ng-create-message ng-repeat="chat in chatLogMessageList | orderBy: 'sort'">
                    </ng-create-message>
                  </message-list>
                </ul>
              </div>
            </section>
            <!-- 過去のチャット -->

          </div>
        </div>
      <?php endif; ?>
      <div id="rightContents">
        <div class="detailForm card">
          <ul>
            <li>
              <label for="ng-customer-company">会社名</label>
              <input type="text" id="ng-customer-company" ng-blur="saveCusInfo('company', customData)" ng-model="customData.company" placeholder="会社名を追加" />
            </li>
            <li>
              <label for="ng-customer-name">名前</label>
              <input type="text" id="ng-customer-name" ng-blur="saveCusInfo('name', customData)" ng-model="customData.name" placeholder="名前を追加">
            </li>
            <li>
              <label for="ng-customer-tel">電話番号</label>
              <input type="text" id="ng-customer-tel" ng-blur="saveCusInfo('tel', customData)" ng-model="customData.tel" placeholder="電話番号を追加" />
            </li>
            <li>
              <label for="ng-customer-mail">メールアドレス</label>
              <input type="text" id="ng-customer-mail" ng-blur="saveCusInfo('mail', customData)" ng-model="customData.mail" placeholder="メールアドレスを追加" />
            </li>
            <li>
              <label for="ng-customer-memo">メモ</label>
              <textarea rows="7" id="ng-customer-memo" ng-blur="saveCusInfo('memo', customData)" ng-model="customData.memo" placeholder="メモを追加"></textarea>
            </li>
          </ul>
        </div>
        <div class="nowInfo card">
          <dl>
            <dt>状態</dt>
              <dd>{{tabStatusStr(detailId)}}</dd>
            <dt>閲覧中ページ</dt>
              <dd class="w100"><a href={{trimToURL(detail.url)}} target="_blank" class="underL" ng-if="detail.title">{{detail.title}}</a><span ng-if="!detail.title">{{trimToURL(detail.url)}}</span></dd>
            <dt>訪問回数</dt>
              <dd>{{detail.stayCount}} 回</dd>
            <dt>閲覧ページ数</dt>
              <dd>{{detail.prev.length}}（<a href="javascript:void(0)" ng-click="openHistory(detail)">移動履歴</a>）</dd>
          </dl>
        </div>
        <?php if ( $coreSettings[C_COMPANY_USE_SYNCLO] && strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0 ) :?>

        <div class="tCenter" ng-if="detail.connectToken">
          <span class="monitorOn" ng-if="!detail.responderId">対応中...</span>
          <span class="monitorOn" ng-if="detail.responderId"><span class="bold">対応中</span>（{{setName(detail.responderId)}}）</span>
        </div>

        <div class="connectionBtn" ng-if="showConnectionBtn()">
          <a href="javascript:void(0)" ng-click="windowOpen(detailId, detail.accessId)">接続する</a>
        </div>
        <?php endif; ?>
        <div class="hardInfo card">
          <dl>
            <dt>プラットフォーム</dt><dd>{{os(detail.userAgent)}}</dd>
            <dt>ブラウザ</dt><dd>{{browser(detail.userAgent)}}</dd>
            <dt>IPアドレス</dt><dd>{{detail.ipAddress}}</dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</div>
<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
  <audio id="sinclo-sound">
    <source src="<?=C_PATH_NODE_FILE_SERVER?>/sounds/decision.mp3" type="audio/mp3">';
  </audio>
<?php endif; ?>