<div id="customer_sub" ng-customer>
  <div id="sub_contents">
    <div id="cus_info_header" class="noSelect">
      <h2>{{detail.accessId}}</h2>
      <div>
        <!-- 閉じる -->
        <a href="javascript:void(0)" ng-click="showDetail(detailId, sincloSessionId)" class="fRight customer_detail_btn redBtn btn-shadow">
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
                  <div style="text-align:right; height: auto!important; padding:0;">
                    <li class="sinclo_se typeing_message" ng-if="typingMessageSe !== ''">{{typingMessageSe}}</li>
                  </div>
                  <div style="text-align:left; height: auto!important; padding:0;">
                    <li class="sinclo_re typeing_message" ng-if="typingMessageRe[sincloSessionId] && typingMessageRe[sincloSessionId] !== ''">{{typingMessageRe[sincloSessionId]}}</li>
                  </div>
                </typing-message>
                <chat-receiver>
                  <span id="receiveMessage">テストメッセージです</span>
                </chat-receiver>
                <upload-notification>
                  <span class="message-area" id="uploadMessage">アップロード中... {{uploadProgress}}％</span>
                  <span class="message-area" id="processingMessage">サーバー処理中です。</span>
                </upload-notification>
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
                  <?php if(isset($coreSettings[C_COMPANY_USE_SEND_FILE]) && $coreSettings[C_COMPANY_USE_SEND_FILE]): ?>
                  <span class="greenBtn btn-shadow" id="selectFileBtn">ファイル送信</span>
                  <?php else: ?>
                  <span class="grayBtn btn-shadow commontooltip" data-text="こちらの機能はスタンダードプラン<br>からご利用いただけます。">ファイル送信</span>
                  <?php endif; ?>
                  <span class="greenBtn btn-shadow" onclick="chatApi.addOption(1)">選択肢を追加する</span>
                </chat-menu-child>
              </chat-menu>
             <div id="sendMessageArea" style="position: relative">
                <?php if ( strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0) :?>
                  <textarea rows="5" id="sendMessage" ng-focus="sendMessageConnectConfirm(detailId)" maxlength="4000" ng-attr-placeholder="{{chatPs()}}"></textarea>

                  <div id="wordListArea" ng-keydown="searchKeydown($event)">

                    <input type="text" ng-model="searchWord" id="wordSearchCond" />
                    <ul id="wordList">
                      <li ng-repeat="item in entryWordSearch(entryWordList)" id="item{{$index}}" ng-class="{selected: $index === entryWord}">{{item.label}}</li>
                      <li style="border:none; color:#ff7b7b" ng-if="entryWordList.length === 0">[設定] > [定型文] から<br>メッセージを登録してください</li>
                    </ul>
                  </div>

                  <div id="sendMessageAreaBtn" ng-class="{showOption: chatOptionDisabled(detailId)}">
                    <span id="sinclo_chatEndBtn" class="btn-shadow redBtn ng-binding" ng-click="confirmDisConnect(detailId, detail.sincloSessionId)">退室</span>
                    <span id="sinclo_sendbtn" class="btn-shadow" onclick="chatApi.pushMessage()">{{chatSendBtnName()}}</span>
                  </div>
                <?php endif; ?>
              </div>
              <?php if(isset($coreSettings[C_COMPANY_USE_SEND_FILE]) && $coreSettings[C_COMPANY_USE_SEND_FILE]): ?>
              <div id="fileUploadDropArea">
                <?= $this->Html->image('file.png', array('alt' => 'CakePHP', 'width' => '250', 'height' => '250')); ?>
                <span>送信するファイルをここにドロップしてください</span>
              </div>
              <input type="file" id="selectFileInput" name="uploadFile" style="display:none "/>
              <?php endif; ?>
            </section>
            <!-- 現在のチャット -->

            <!-- 過去のチャット -->
            <section id="oldChat">
              <ul class="historyList">
                <li ng-click="getOldChat(historyId, true, $event)" ng-repeat="(historyId, firstDate) in chatLogList" class = "pastChatShowBold"><span>{{firstDate | date:'yyyy年M月d日（EEE）a hh時mm分ss秒' }}</span></li>
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
            <?php
            for($i = 0; $i < count($customerInformationList); $i++) {
              if(strcmp($customerInformationList[$i]['TCustomerInformationSetting']['input_type'], "2") === 0) {
                echo '<li class="auto-height">';
                echo '  <label class="no-float" for="ng-customer-custom-'.$customerInformationList[$i]['TCustomerInformationSetting']['id'].'">'.$customerInformationList[$i]['TCustomerInformationSetting']['item_name'].'</label>';
              } else {
                echo '<li>';
                echo '  <label for="ng-customer-custom-' . $customerInformationList[$i]['TCustomerInformationSetting']['id'] . '">' . $customerInformationList[$i]['TCustomerInformationSetting']['item_name'] . '</label>';
              }
              echo $this->htmlEx->visitorInput($customerInformationList[$i]['TCustomerInformationSetting']);
              echo '</li>';
            }
            ?>
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
          <a href="javascript:void(0)" ng-click="confirmSharingWindowOpen(detailId, detail.accessId)">接続する</a>
        </div>
        <?php endif; ?>
        <div class="hardInfo card">
          <dl>
            <dt>IPアドレス</dt>
            <?php if ( $coreSettings[C_COMPANY_REF_COMPANY_DATA] ) :?>
              <dd ng-if="detail.lbcCode && detail.orgName" style="height:auto;">
                <a href="#" ng-click="openCompanyDetailInfo(detail)">{{detail.orgName}}</a>（{{detail.ipAddress}}）
              </dd>
              <dd ng-if="!detail.lbcCode || !detail.orgName">{{detail.ipAddress}}</dd>
            <?php else: ?>
              <dd>{{detail.ipAddress}}</dd>
            <?php endif; ?>
            <dt>プラットフォーム</dt><dd>{{os(detail.userAgent)}}</dd>
            <dt>ブラウザ</dt><dd>{{browser(detail.userAgent)}}</dd>
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