    <div id="bk-ground"></div>
    <div id="customer_sub">
      <div id="sub_contents">
        <div id="cus_info_header">
          <h2>header</h2>
          <div>
            <!-- 閉じる -->
            <a ng-if="chatList.indexOf(detailId) < 0" href="javascript:void(0)" ng-click="showDetail(detailId)" class="fRight customer_detail_btn redBtn btn-shadow">
              <?= $this->Html->image('close.png', ['alt'=>'詳細を閉じる', 'width'=>20, 'height' => 20]); ?>
            </a>
            <a ng-if="chatList.indexOf(detailId) >= 0" href="javascript:void(0)" ng-click="confirmDisConnect(detailId)" class="fRight customer_detail_btn redBtn btn-shadow">
              <?= $this->Html->image('close.png', ['alt'=>'チャットを終了する', 'width'=>20, 'height' => 20]); ?>
            </a>
            <!-- 閉じる -->
            <!-- 最小化 -->
            <a href="javascript:void(0)" ng-click="showDetail(detailId)" class="fRight customer_detail_btn redBtn btn-shadow">
              <?= $this->Html->image('minimize.png', ['alt'=>'詳細を閉じる', 'width'=>20, 'height' => 20]); ?>
            </a>
            <!-- 最小化 -->
          </div>
        </div>
        <div class="flexBoxCol">
          <div id="leftContents">
            <ul class="tabStyle flexBoxCol">
              <li class="on">現在のチャット</li>
              <li>過去のチャット</li>
            </ul>
            <div id="chatContent">
              <ul id="chatTalk" >
                <message-list>
                  <ng-create-message ng-repeat="chat in messageList | orderBy: 'sort'"></ng-create-message>
                </message-list>
                <typing-message>
                  <li class="sinclo_se typeing_message" ng-if="typingMessageSe !== ''">{{typingMessageSe}}</li>
                  <li class="sinclo_re typeing_message" ng-if="typingMessageRe[detailId] !== ''">{{typingMessageRe[detailId]}}</li>
                </typing-message>
              </ul>
              <div id="chatMenu" class="p05tb" ng-class="{showOption: chatOptionDisabled(detailId)}">
                <span class="greenBtn btn-shadow" onclick="chatApi.addOption(1)">選択肢を追加する</span>
              </div>
              <div class="p05tb">
                <?=$this->ngForm->input('settings.sendPattarn',
                    [
                      'type' => 'checkbox',
                      'div' => false,
                      'label' => false
                    ],
                    [
                      'default' => false,
                      'entity' => 'settings.sendPattarn',
                      'change' => 'changeSetting("sendPattarn")'
                    ])?><label for="settingsSendPattarn">Enterキーで送信する</label>
              </div>
              <div style="position: relative">
                <?php if ( strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) !== 0) :?>
                  <textarea rows="5" id="sendMessage" ng-focus="sendMessageConnectConfirm(detailId)" maxlength="300" placeholder="{{chatPs()}}"></textarea>
                  <div id="wordListArea" ng-keydown="searchKeydown($event)">
                    <input type="text" ng-model="searchWord" id="wordSearchCond" />
                    <ul id="wordList">
                      <li ng-repeat="item in entryWordSearch(entryWordList)" id="item{{$index}}" ng-class="{selected: $index === entryWord}">{{item.label}}</li>
                      <li style="border:none; color:#ff7b7b" ng-if="entryWordList.length === 0">[設定] > [簡易入力] から<br>メッセージを登録してください</li>
                    </ul>
                  </div>
                  <span id="sinclo_sendbtn" class="btn-shadow" onclick="chatApi.pushMessage()">{{chatSendBtnName()}}</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div id="rightContents">
            <div class="detailForm card">
              <ul>
                <li>
                  <label for="">会社名</label>
                  <input type="text" />
                </li>
                <li>
                  <label for="">名前</label>
                  <input type="text" />
                </li>
                <li>
                  <label for="">電話番号</label>
                  <input type="text" />
                </li>
                <li>
                  <label for="">メールアドレス</label>
                  <input type="text" />
                </li>
                <li>
                  <label for="">メモ</label>
                  <textarea></textarea>
                </li>
              </ul>
            </div>
            <div class="nowInfo card">
              <dl>
                <dt>閲覧中ページ</dt>
                  <dd><a href="javascript:void(0)">XXページ</a></dd>
                <dt>閲覧ページ数</dt>
                  <dd>３（<a href="javascript:void(0)" ng-click="openHistory(monitorList[detailId])">移動履歴</a>）</dd>
              </dl>
            </div>
            <div class="connectionBtn">
              <a href="javascript:void(0)" ng-click="windowOpen(detailId, monitorList[detailId].accessId)">接続する</a>
            </div>
            <div class="hardInfo card">
              <dl>
                <dt>プラットフォーム</dt><dd>iPhone</dd>
                <dt>ブラウザ</dt><dd>Safari</dd>
                <dt>IPアドレス</dt><dd>111.111.111.111</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
