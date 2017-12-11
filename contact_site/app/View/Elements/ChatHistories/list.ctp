<div id = "list_body">
<div id = "list_height" style = "height:500px !important;">
  <table>
      <thead>
          <tr>
              <th width=" 3%"></th>
              <th width=" 6%">種別</th>
              <th width=" 6%">初回チャット<br>受信日時</th>
              <th width="10%">IPアドレス</th>
              <th width="10%">訪問ユーザ</th>
              <th width=" 8%">キャンペーン</th>
              <th width=" 17%">チャット送信ページ<div class="questionBalloon questionBalloonPosition8"></th>
              <th width=" 5%">成果</th>
              <th width="6%">有人チャット<br>受信日時</th>
          <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
              <th width=" 6%">最終発言後<br>離脱時間</th>
              <th width="10%">担当者</th>
          <?php endif; ?>
          </tr>
      </thead>
      <tbody ng-cloak id = "chatHistory">
  <?php foreach($historyList as $key => $history): ?>
  <?php
  /* キャンペーン名の取得 */
  $campaignParam = "";
  $tmp = mb_strstr($stayList[$history['THistory']['id']]['THistoryStayLog']['firstURL'], '?');
  if ( $tmp !== "" ) {
    foreach($campaignList as $k => $v){
      if ( strpos($tmp, $k) !== false ) {
        if ( $campaignParam !== "" ) {
          $campaignParam .= "\n";
        }
        $campaignParam .= h($v);
      }
    }
  }
  $visitorsId = "";
  if ( isset($history['THistory']['visitors_id']) ) {
    $visitorsId = $history['THistory']['visitors_id'];
  }
  ?>
          <tr onclick="openChatById('<?=h($history['THistory']['id'])?>')">
              <td class="tCenter" onclick="event.stopPropagation();" width=" 3%">
                <input type="checkbox" name="selectTab" id="selectTab<?=h($history['THistory']['id'])?>" value="<?=h($history['THistory']['id'])?>">
                <label for="selectTab<?=h($history['THistory']['id'])?>"></label>
              </td>
              <td class="tCenter">
                <?php if( is_numeric($history['THistoryChatLog']['count']) ): ?>
                    <?php
                     if (!empty($history['THistoryChatLog']['type']) && $history['THistoryChatLog']['type'] == "自動返信") { ?>
                      <span style = "color:#4bacc6; font-weight:bold;">Auto</span>
                    <?php
                    }
                    else if(!empty($history['THistoryChatLog']['type']) && $history['THistoryChatLog']['type'] == "拒否") { ?>
                      <span style = "color:#a6a6a6; font-weight:bold;">Sorry</span>
                    <?php
                    }
                    else if($history['THistoryChatLog']['type'] == "") { ?>
                      <span style = "color:#9bbb59; font-weight:bold;">Manual</span>
                    <?php
                    }
                    else if($history['THistoryChatLog']['type'] == "未入室") { ?>
                      <span style = "color:#f79646; font-weight:bold;">NoEntry</span>
                    <?php
                      }
                   endif; ?>
              </td>
              <td class="tRight pre"><?=date_format(date_create($history['THistory']['access_date']), "Y/m/d\nH:i:s")?></td>
              <td class="tLeft">
                <?php if(isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]): ?>
                  <?php if(!empty($history['LandscapeData']['org_name']) && !empty($history['LandscapeData']['lbc_code'])): ?>
                      <a href="javascript:void(0)" class="underL" onclick="openCompanyDetailInfo('<?=$history['LandscapeData']['lbc_code']?>')"><?=h($history['LandscapeData']['org_name'])?></a><br>
                  <?php elseif(!empty($history['LandscapeData']['org_name'])): ?>
                      <p><?=h($history['LandscapeData']['org_name'])?></p><?='\n'?>
                  <?php endif; ?>
                <?php endif; ?>
                {{ ip('<?=h($history['THistory']['ip_address'])?>', <?php echo !empty($history['LandscapeData']['org_name']) ? 'true' : 'false' ?>) }}
              </td>
              <td class="tLeft pre">{{ ui('<?=h($history['THistory']['ip_address'])?>', '<?=$visitorsId?>') }}</td>
              <td class="tCenter pre"><?=$campaignParam?></td>
              <td class="pre" style = "font-size:11px;padding:8px 5px !important"><a href = "<?=h($stayList[$history['THistory']['id']]['THistoryStayLog']['firstURL'])?>" target = "landing"><?= $stayList[$history['THistory']['id']]['THistoryStayLog']['title'] ?></a></td>
              <td class="tCenter"><?php
                if($history['THistoryChatLog']['eff'] == 0 || $history['THistoryChatLog']['cv'] == 0 ) {
                  if (isset($history['THistoryChatLog']['achievementFlg'])){
                    echo $achievementType[h($history['THistoryChatLog']['achievementFlg'])];
                  }
                }
                else if ($history['THistoryChatLog']['eff'] != 0 && $history['THistoryChatLog']['cv'] != 0) {
                  if (isset($history['THistoryChatLog']['achievementFlg'])){
                    echo $achievementType[2].nl2br("\n").$achievementType[0];
                  }
                }
              ?></td>
          <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
              <td class="tRight pre"><?=date_format(date_create($history['THistory']['access_date']), "Y/m/d\nH:i:s")?></td>
              <td class="tCenter"><?php
              if ($history['LastSpeechTime']['lastSpeechTime']
                && $history['THistory']['access_date'] !== $history['THistory']['out_date']
                && strtotime($history['LastSpeechTime']['lastSpeechTime']) <= strtotime($history['THistory']['out_date'])){
                echo $this->htmlEx->calcTime($history['LastSpeechTime']['lastSpeechTime'], $history['THistory']['out_date']);
              }
              ?></td>
              <td class="tCenter pre"><?php if (isset($chatUserList[$history['THistory']['id']])) { echo $chatUserList[$history['THistory']['id']]; } ?></td>
          <?php endif; ?>
          </tr>
  <?php endforeach; ?>
      </tbody>
  </table>
</div>
<div id = "detail" style = "width: 101.6%; margin-left:-34px; background-color: #f2f2f2;">
  <div id="cus_info_contents" class="flexBoxCol">
    <div id="leftContents" style = "padding:1em 1.8em 1em 0em;">
      <ul id="showChatTab" class="tabStyle flexBoxCol noSelect">
        <li class="on" data-type="currentChat">チャット内容</li>
        <li data-type="oldChat">過去のチャット</li>
      </ul>
      <div id="chatContent">

      <!-- 現在のチャット -->
      <section class="on" id="currentChat">
        <ul id="chatTalk" class="chatView">
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
        </ul>
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
        <div id="rightContents">
          <div class="nowInfo card" style = "border-bottom: 1px solid #bfbfbf;">
          <dl >
            <dt>ユーザID</dt><dd>20171122141125995</dd>
            <dt>IPアドレス</dt><dd>ABC商事（49.98.153.80）</dd>
            <dt>訪問回数</dt><dd>5回</dd>
            <dt>プラットフォーム</dt><dd>Windows 8.1 | IE（ver.11.0）</dd>
          </dl>
        </div>
        <div class="hardInfo card">
          <dl>
            <dt>キャンペーン</dt><dd>GSN | 広告_MC</dd>
            <dt>ランディングページ</dt><dd>MediaSeries | 企業の成長を加速させる次世代型コミュニケーションサービス</dd>
            <dt>チャット送信ページ</dt><dd>驚異のコストで導入可能 - 選ばれる理由 | MediaVoice</dd>
            <dt>離脱ページ</dt><dd>オフィスのフリーアドレス化を実現 - 導入事例 | MediaSeries</dd>
            <dt>閲覧ページ数</dt><dd>6（移動履歴）</dd>
          </dl>
        </div>
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
              <label for="ng-customer-memo" style = "width:60% !important">メモ</label>
              <textarea rows="7" id="ng-customer-memo" ng-blur="saveCusInfo('memo', customData)" ng-model="customData.memo" placeholder="メモを追加"></textarea>
            </li>
          </ul>
          <div id="personal_action">
              <?= $this->Html->link('元に戻す', 'javascript:void(0)', ['onclick' => 'loading.ev(saveAct)', 'class' => 'whiteBtn btn-shadow lineUpSaveBtn historyReturnButton']) ?>
              <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'loading.ev(saveAct)', 'class' => 'greenBtn btn-shadow lineUpSaveBtn hitoryUpdateButton']) ?>
          </div>
        </div>
      </div>
      </div>
</div>
</div>
<div id = "list_header">
  <table>
    <thead>
      <tr>
        <th width=" 3%"></th>
        <th width=" 6%">種別</th>
        <th id = "firstTimeReceivingLabel" width=" 6%">初回チャット<br>受信日時<div class="questionBalloon questionBalloonPosition13">
          <icon class="questionBtn">？</icon>
        </div></th>
        <th width="10%">IPアドレス</th>
        <th width="10%">訪問ユーザ</th>
        <th width=" 8%">キャンペーン</th>
        <th id = "sendChatPageLabel" width=" 17%">チャット送信ページ<div class="questionBalloon questionBalloonPosition8">
          <icon class="questionBtn">？</icon>
        </div></th>
        <th width=" 5%">成果</th>
        <th id = "manualReceivingLabel" width="6%">有人チャット<br>受信日時<div class="questionBalloon questionBalloonPosition13">
          <icon class="questionBtn">？</icon>
          </div></th>
      <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
        <th id="lastSpeechLabel" width=" 6%">最終発言後<br>離脱時間<div class="questionBalloon questionBalloonPosition13">
            <icon class="questionBtn">？</icon>
          </div></th>
        <th width="10%">担当者</th>
      <?php endif; ?>
      </tr>
    </thead>
  </table>
</div>
<?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
<div id='lastSpeechTooltip' class="explainTooltip">
  <icon-annotation>
    <ul>
      <li><span>サイト訪問者が最後に発言してからページを離脱するまでの時間</span></li>
    </ul>
  </icon-annotation>
</div>
<div id='sendChatPageTooltip' class="opThreeLineExplainTooltip">
  <icon-annotation>
    <ul>
      <li><span>サイト訪問者が一番最初にチャット送信（ラジオボタン操作を含む）したページ</span></li>
    </ul>
  </icon-annotation>
</div>
<div id='firstTimeReceivingTooltip' class="opThreeLineExplainTooltip">
  <icon-annotation>
    <ul>
      <li><span>サイト訪問者が一番最初にチャット送信（ラジオボタン操作を含む）した日時</span></li>
    </ul>
  </icon-annotation>
</div>
<div id='manualReceivingTooltip' class="opThreeLineExplainTooltip">
  <icon-annotation>
    <ul>
      <li><span>サイト訪問者が送信したチャットが、最初にオペレータに通知された日時</span></li>
    </ul>
  </icon-annotation>
</div>
<?php endif; ?>