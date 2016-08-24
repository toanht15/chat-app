<div class="card-shadow m10b">
  <div id='customer_subtitle'>
    <h1 class="fLeft">詳細情報</h1>
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
<div id="customer_detail" class="card-shadow m10b p10x close">
  <h2>基本情報</h2>
  <ul class="p20l">
    <li>
      <span>アクセスID：</span><br>
      {{monitorList[detailId].accessId}}
    </li>
    <li>
      <span>IPアドレス：</span><br>
      {{monitorList[detailId].ipAddress}}
    </li>
    <li>
      <span>ユーザーエージェント：</span><br>
      {{monitorList[detailId].userAgent}}
    </li>
    <li>
      <span>閲覧中ページ：</span><br>
      <a ng-href='{{monitorList[detailId].url}}' target='showBlack'>{{monitorList[detailId].title}}</a>
    </li>
  </ul>
  <p ng-click="openDetail()">
    <svg ng-if="openDetailFlg !== true" class="svg1" height="16" width="180">
      <g stroke="#919191" stroke-width=1 >
        <!-- 左上 --><line x1="0" y1="2" x2="90" y2="12" /><!-- 左上 -->
        <!-- 左下 --><line x1="0" y1="6" x2="90" y2="16" /><!-- 左下 -->
        <!-- 右上 --><line x1="90" y1="12" x2="180" y2="2" /><!-- 右上 -->
        <!-- 右下 --><line x1="90" y1="16" x2="180" y2="6" /><!-- 右下 -->
      </g>
      Sorry, your browser does not support inline SVG.
    </svg>
    <svg ng-if="openDetailFlg !== false" class="svg1" height="16" width="180">
      <g stroke="#595959" stroke-width=1 >
        左上<line x1="0" y1="16" x2="90" y2="6" />左上
        左下<line x1="0" y1="12" x2="90" y2="2" />左下
        右上<line x1="90" y1="2" x2="180" y2="12" />右上
        右下<line x1="90" y1="6" x2="180" y2="16" />右下
      </g>
      Sorry, your browser does not support inline SVG.
    </svg>
  </p>
</div>

<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
<div id="chat-area" class="card-shadow p10x">
  <h2>チャット</h2>
  <!-- <ul class="naviBtn p0">
    <li class="w50 tCenter on">チャット</li>
    <li class="w50 tCenter">メモ</li>
  </ul> -->
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
        <textarea rows="5" id="sendMessage" ng-focus="sendMessageConnectConfirm(detailId)" maxlength="300" placeholder="ここにメッセージ入力してください。
・{{chatPs()}}で改行されます
・下矢印キー(↓)で簡易入力が開きます"></textarea>
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
  <audio id="sinclo-sound">
    <source src="<?=C_PATH_NODE_FILE_SERVER?>/sounds/decision.mp3" type="audio/mp3">';
  </audio>
</div>
<?php endif; ?>
