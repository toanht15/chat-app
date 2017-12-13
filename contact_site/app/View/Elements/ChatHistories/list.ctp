<div id='history_menu' class="p20trl">
  <div id="paging" class="fRight">
    <?=
        $this->Paginator->prev(
        $this->Html->image('paging.png', array('alt' => '前のページへ', 'width'=>25, 'height'=>25)),
        array('escape' => false, 'class' => 'btn-shadow greenBtn tr180'),
        null,
        array('class' => 'grayBtn tr180')
      );
    ?>
    <span style="width: auto!important;padding: 10px 0 0;"> <?= $this->Paginator->counter('{:page} / {:pages}'); ?> </span>
    <?=
        $this->Paginator->next(
        $this->Html->image('paging.png', array('alt' => '次のページへ', 'width'=>25, 'height'=>25)),
        array('escape' => false, 'class' => 'btn-shadow greenBtn'),
        null,
        array('escape' => false, 'class' => 'grayBtn')
      );
    ?>
  </div>

  <?= $this->Html->link(
    '高度な検索',
    'javascript:void(0)',
    array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchRefine','onclick' => 'openSearchRefine()','style' => 'position: absolute;
  top: 15px;
  left: 32em;
  width: 8em;
  padding: 0.25em 0.5em;
  text-align: center;'));
  ?>
  <span id="searchPeriod">検索期間</span>
  <?php
    //検索条件表示：非表示
    $noseach_menu = '';
    $seach_menu = 'seach_menu';
  ?>
  <?php //検索をした時の表示
    if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
      <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['period']) ?> : <?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
  <?php } ?>
  <?php //セッションをクリアしたときの表示(履歴一覧ボタンを押下した時)
    if(empty($data['History']['start_day'])&&empty($data['History ']['finish_day'])) { ?>
      <span id ='mainDatePeriod' name = 'datefilter' class='date'>過去一ヵ月間 : <?= h($historySearchConditions['start_day']) ?>-<?= h($historySearchConditions['finish_day']) ?></span>
  <?php } ?>
  <?php
      if(
        empty($data['History']['ip_address'])&&empty($data['History']['company_name'])
        &&empty($data['History']['customer_name'])&&empty($data['History']['telephone_number'])
        &&empty($data['History']['mail_address'])&&empty($data['THistoryChatLog']['responsible_name'])
        &&($data['THistoryChatLog']['achievement_flg'] === "")
        &&empty($data['THistoryChatLog']['message'])){
        $noseach_menu = 'noseach_menu';
        $seach_menu='　';
      }
  ?>

  <div class=<?= $seach_menu; ?> id=<?= $noseach_menu ?> style = "height: 37px !important;">
    <label class='searchConditions'>検索条件</label>
    <ul ng-non-bindable>
      <?php if(!empty($data['History']['ip_address'])) { ?>
        <li>
          <label>IPｱﾄﾞﾚｽ</label>
          <span class="value"><?= h($data['History']['ip_address']) ?></span>
        </li>
      <?php } ?>
      <?php if(!empty($data['History']['company_name'])) { ?>
        <li>
          <label>会社名</label>
          <span class="value"><?= h($data['History']['company_name']) ?></span>
        </li>
      <?php } ?>
      <?php if(!empty($data['History']['customer_name'])) { ?>
        <li>
          <label class="label">名前</label>
          <span class="value"><?= h($data['History']['customer_name']) ?></span>
        </li>
      <?php } ?>
      <?php if(!empty($data['History']['telephone_number'])) { ?>
        <li>
          <label>電話番号</label>
          <span class="value"><?= h($data['History']['telephone_number']) ?></span>
        </li>
      <?php } ?>
      <?php if(!empty($data['History']['mail_address'])) { ?>
        <li>
          <label>ﾒｰﾙｱﾄﾞﾚｽ</label>
          <span class="value"><?= h($data['History']['mail_address']) ?></span>
        </li>
      <?php } ?>
      <?php if(!empty($data['THistoryChatLog']['responsible_name'])) { ?>
        <li>
          <label>担当者</label>
          <span class="value"><?= h($data['THistoryChatLog']['responsible_name']) ?></span>
        </li>
      <?php } ?>
      <?php if(isset($data['THistoryChatLog']['achievement_flg']) && ($data['THistoryChatLog']['achievement_flg'] !== "" || $data['THistoryChatLog']['achievement_flg'] === 0)) { ?>
        <li>
          <label>成果</label>
          <span class="value"><?= $achievementType[h($data['THistoryChatLog']['achievement_flg'])] ?></span>
        </li>
      <?php } ?>
      <?php if(!empty($data['THistoryChatLog']['message'])) { ?>
        <li>
          <label>ﾁｬｯﾄ内容</label>
          <span class="value"><?= h($data['THistoryChatLog']['message']) ?></span>
        </li>
      <?php } ?>

      <?= $this->Html->link(
        '条件クリア',
        'javascript:void(0)',
        ['escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'sessionClear','onclick' => 'sessionClear()']);
      ?>
    </ul>
  </div>
    <!-- 検索窓 -->
    <div class='fLeft'>
      <?php
        if ($coreSettings[C_COMPANY_USE_CHAT]) :
        $checked = "";
        $class = "";
        if (strcmp($groupByChatChecked, 'false') !== 0) {
          $class = "";
          $checked = "checked=\"\"";
        }
      ?>
        <label for="g_chat" class="pointer <?=$class?>">
          <input type="checkbox" id="g_chat" name="group_by_chat" <?=$checked?> />
          CV(コンバージョン)のみ表示する
        </label>
      <?php endif; ?>
      <?=$this->Form->create('History', ['action' => 'index']);?>
        <?=$this->Form->hidden('outputData')?>
      <?=$this->Form->end();?>
      <?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => '/Histories']); ?>
    </div>
  </div>
  <div class="btnSet">
       <span>
         <a>
           <?= $this->Html->image('csv.png', array(
               'alt' => 'CSV出力',
               'id'=>'history_csv_btn',
               'class' => 'btn-shadow disOffgrayBtn commontooltip',
               'data-text' => 'CSV出力',
               'data-balloon-position' => '36',
               'width' => 45,
               'height' => 45,
               'onclick' => 'openAdd()',
               'style' => 'margin-left: -17em;margin-top:6px;'
           )) ?>
         </a>
       </span>
       <span>
         <a>
           <?= $this->Html->image('dustbox.png', array(
               'alt' => '削除',
               'id'=>' height:100%;ory_dustbox_btn',
               'class' => 'btn-shadow disOffgrayBtn commontooltip',
               'data-text' => '削除する',
               'data-balloon-position' => '36',
               'width' => 45,
               'height' => 45)) ?>
         </a>
       </span>
  </div>

<div id = "list_body" style = "padding: 0px 20px 0 20px; height:53em; overflow-y: auto; overflow-x: hidden;">
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
          <th width=" 17%" id = "sendChatPageLabel">チャット送信ページ<div class="questionBalloon questionBalloonPosition8">
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

<div id = "list_header">
  <table  style = "width:100%;">
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
</div>


<div id = "detail" class = "detail" style = "width: 100%; background-color: #f2f2f2;">
  <div id="cus_info_contents"  class="flexBoxCol">
    <div id="leftContents" style = "width: 100%;padding: 1em 1.5em 1em 1.5em;">
      <ul id="showChatTab" class="tabStyle flexBoxCol noSelect" style = "width:100%">
        <li class="on" data-type="currentChat">チャット内容</li>
        <li data-type="oldChat">過去のチャット</li>
      </ul>
      <div id="chatContent" style = "width:100%; height:100% ">

      <!-- 現在のチャット -->
      <section class="on" id="currentChat" style = "height:100%;">
        <ul id="chatTalk" class="chatView" style = "height:100%;">
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
        <div id="rightContents" style = "width:100% !important;">
        <div class = "form01 fRight">
          <ul class="switch" ng-init = "fillterTypeId = 2" style = "box-shadow:none;">
              <li id = "uuu" ng-class="{on:fillterTypeId===1}" ng-click="fillterTypeId = 1" style = "margin-top:0; width:6em !important;">
                <span ng-if = "fillterTypeId == 1">
                 <?= $this->Html->link(
                    $this->Html->image('dock_bottom.png', array('alt' => 'メニュー', 'width'=>40, 'height'=>40)),
                    'javascript:void(0)',
                    array('escape' => false,
                      'style' => 'display: inline-block; height: 30px;')); ?>
                </span>
                <span ng-if = "fillterTypeId == 2">
                  <?= $this->Html->link(
                    $this->Html->image('dock_bottom_color.png', array('alt' => 'メニュー', 'width'=>40, 'height'=>40)),
                    'javascript:void(0)',
                    array('escape' => false,'id' => 'vertical',
                      'style' => 'display: inline-block; height: 30px;')); ?>
                </span>
              </li>
              <li id = "eee" ng-class="{on:fillterTypeId===2}" ng-click="fillterTypeId = 2" style = "margin-top:0; width:6em !important;">
              <span ng-if = "fillterTypeId == 1">
                <?= $this->Html->link(
                    $this->Html->image('dock_right_color.png', array('alt' => 'メニュー', 'width'=>40, 'height'=>40)),
                    'javascript:void(0)',
                    array('escape' => false,'id' => 'side',
                      'style' => 'display: inline-block; height: 30px;')); ?>
                </span>
               <span ng-if = "fillterTypeId == 2">
                <?= $this->Html->link(
                    $this->Html->image('dock_right.png', array('alt' => 'メニュー', 'width'=>40, 'height'=>40)),
                    'javascript:void(0)',
                    array('escape' => false,
                      'style' => 'display: inline-block; height: 30px;')); ?>
                </span>
              </li>
            </ul>
        </div>
          <div class="nowInfo card" style = "border-bottom: 1px solid #bfbfbf; width:100%; margin-top: 58px;">
          <dl>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">ユーザID</dt>
            <dd style = "width: 30%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">20171122141125995</dd>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">IPアドレス</dt>
            <dd style = "width: 30%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">ABC商事（49.98.153.80）</dd>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">訪問回数</dt>
            <dd style = "width: 30%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">5回</dd>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">プラットフォーム</dt>
            <dd style = "width: 30%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">Windows 8.1 | IE（ver.11.0）</dd>
          </dl>
        </div>
        <div class="hardInfo card" style = "width:100%;">
          <dl>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">キャンペーン</dt>
            <dd style = "width: 70%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">GSN | 広告_MC</dd>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">ランディングページ</dt>
            <dd style = "width: 70%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">MediaSeries | 企業の成長を加速させる次世代型コミュニケーションサービス</dd>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">チャット送信ページ</dt>
            <dd style = "width: 70%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">驚異のコストで導入可能 - 選ばれる理由 | MediaVoice</dd>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">離脱ページ</dt>
            <dd style = "width: 70%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">オフィスのフリーアドレス化を実現 - 導入事例 | MediaSeries</dd>
            <dt style = "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">閲覧ページ数</dt>
            <dd style = "width: 70%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">6（移動履歴）</dd>
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