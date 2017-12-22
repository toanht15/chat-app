<div id='history_menu' style = "padding: 20px 0 0 0;">
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
        empty($data['History']['chat_type_name'])&&empty($data['History']['campaign'])
        &&empty($data['History']['ip_address'])&&empty($data['History']['company_name'])
        &&empty($data['History']['customer_name'])&&empty($data['History']['telephone_number'])
        &&empty($data['History']['mail_address'])&&empty($data['THistoryChatLog']['responsible_name'])
        &&($data['THistoryChatLog']['achievement_flg'] === "")&&empty($data['THistoryChatLog']['send_chat_page'])
        &&empty($data['THistoryChatLog']['message'])){
        $noseach_menu = 'noseach_menu';
        $seach_menu='　';
      }
  ?>

  <div class=<?= $seach_menu; ?> id=<?= $noseach_menu ?> style = "height: 37px !important;">
    <label class='searchConditions'>検索条件</label>
    <ul ng-non-bindable>
     <?php  if(!empty($data['History']['chat_type'])) { ?>
        <li>
          <label>種別</label>
          <span class="value"><?= h($data['History']['chat_type_name']) ?></span>
        </li>
      <?php } ?>
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
      <?php if(!empty($data['History']['campaign'])) { ?>
        <li>
          <label>ｷｬﾝﾍﾟｰﾝ</label>
          <span class="value"><?= h($data['History']['campaign']) ?></span>
        </li>
      <?php } ?>
      <?php if(!empty($data['THistoryChatLog']['send_chat_page'])) { ?>
        <li>
          <label>ﾁｬｯﾄ送信ﾍﾟｰｼﾞ</label>
          <span class="value"><?= h($data['THistoryChatLog']['send_chat_page']) ?></span>
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
      <div class="btnSet">
       <span id = "outputCsv">
           <?= $this->Html->image('csv.png', array(
               'alt' => 'CSV出力',
               'id'=>'history_csv_btn',
               'class' => 'btn-shadow disOffgrayBtn commontooltip',
               'data-text' => 'CSV出力',
               'data-balloon-position' => '36',
               'width' => 45,
               'height' => 45,
               'onclick' => 'selectedOutputCSV()',
               'url'=>array('controller'=>'ChatHistories','action'=>'outputCSVOfChat')
           )) ?>
       </span>
       <span>
         <a>
           <?= $this->Html->image('dustbox.png', array(
               'alt' => '削除',
               'id'=>'history_dustbox_btn',
               'class' => 'btn-shadow disOffgrayBtn commontooltip',
               'data-text' => '削除する',
               'data-balloon-position' => '36',
               'onclick' => 'selectDeleteChat()',
               'width' => 45,
               'height' => 45)) ?>
         </a>
       </span>
  </div>

      <?php
        if ($coreSettings[C_COMPANY_USE_CHAT]) :
        $checked = "";
        $class = "checked";
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
<div id = "list_body" style = "overflow-y: auto; overflow-x: hidden;">
  <div id = "list_header">
  <table  style = "width:100%;">
    <thead>
      <tr>
        <th width=" 4%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
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
  <table class = "scroll" id = "chatTable">
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
      <tbody ng-cloak id = "chatHistory" >
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
            <?php
            if ((isset($history['THistoryChatLog']['type']) && isset($data['History']['chat_type']) && isset($chatType) &&
              $history['THistoryChatLog']['type'] === $chatType[$data['History']['chat_type']]) || empty($chatType)) {

              if((!empty($campaignParam) && !empty($data['History']['campaign']) && $data['History']['campaign'] == $campaignParam) || empty($data['History']['campaign'])) { ?>
          <tr id = "<?=h($history['THistory']['id'])?>" ng-click="getOldChat('<?=h($history['THistory']['id'])?>', false)" onclick="openChatById('<?=h($history['THistory']['id'])?>');" class = "showBold">
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
              <td class="tRight pre"><?=date_format(date_create($history['LastSpeechTime']['firstSpeechTime']), "Y/m/d\nH:i:s")?></td>
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
              <td class="pre" style = "font-size:11px;padding:8px 5px !important"><a href = "<?=h($history['THistoryStayLog']['url'])?>" target = "landing"><?= $history['THistoryStayLog']['title'] ?></a></td>
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
              <td class="tRight pre"><?php if (!empty($history['NoticeChatTime']['NoticeChatTime'])){ ?><?=date_format(date_create($history['NoticeChatTime']['NoticeChatTime']), "Y/m/d\nH:i:s")?><?php } ?>
              </td>
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
          <?php } } ?>
  <?php endforeach; ?>
      </tbody>
  </table>
</div>

</div>


<div ng-aa id = "detail" class = "detail" style = "width: 100%; background-color: #f2f2f2;">
  <div id="cus_info_contents"  class="flexBoxCol">
    <div id="leftContents" style = "width: 100%;padding: 1em 1.5em 1em 1.5em;">
      <ul id="showChatTab" class="tabStyle flexBoxCol noSelect" style = "width:100%">
        <li class="on" data-type="currentChat" style = "margin-left:-40px;">チャット内容</li>
        <li data-type="oldChat">過去のチャット</li>
      </ul>
      <div id="chatContent" style = "width:100%; ">


      <!-- 現在のチャット -->
      <section class="on" id="currentChat" style = "height:100%;">
        <ul id="chatTalk" class="chatView" style = "height:100%;">
          <message-list>
            <ng-create-message ng-repeat="chat in messageList | orderBy: 'sort'"></ng-create-message>
          </message-list>
        </ul>
      </section>
      <!-- 現在のチャット -->

      <!-- 過去のチャット -->
      <section id="oldChat" style = "height:100%">
        <ul class="historyList">
          <li class = "pastChatShowBold" ng-click="getOldChat(historyId, true)" ng-repeat="(historyId, firstDate) in chatLogList"><span>{{firstDate | date:'yyyy年M月d日（EEE）a hh時mm分ss秒' }}</span></li>
        </ul>
          <ul class="chatView" id = "pastChatTalk" >
            <message-list>
              <message-list-descript>上から、表示したいチャット対応日時をクリックしてください</message-list-descript>
              <ng-create-message ng-repeat="chat in chatLogMessageList | orderBy: 'sort'"></ng-create-message>
            </message-list>
          </ul>
      </section>
      <!-- 過去のチャット -->

          </div>
        </div>
        <div id="rightContents" style = "width:100% !important;">
        <div class = "form01 fRight" style = "right:20px;">
        <?php
        $this->log('screenFLg!',LOG_DEBUG);
        $this->log($screenFlg,LOG_DEBUG);
        if($screenFlg == C_CHAT_HISTORY_SIDE) { ?>
          <ul class="switch" ng-init = "fillterTypeId = 2" style = "box-shadow:none;">
        <?php }
        if($screenFlg == C_CHAT_HISTORY_VERTICAL) { ?>
          <ul class="switch" ng-init = "fillterTypeId = 1" style = "box-shadow:none;">
        <?php } ?>
              <li ng-class="{on:fillterTypeId===1}" ng-click="fillterTypeId = 1" style = "margin-top:0; width:5em !important;">
                <span class = 'vertical' ng-if = "fillterTypeId == 1">
                 <?= $this->Html->link(
                    $this->Html->image('dock_bottom.png', array('alt' => 'メニュー', 'width'=>50, 'height'=>50)),
                    'javascript:void(0)',
                    array('escape' => false,
                      'style' => 'display: inline-block; height: 30px; margin-top:-9px;')); ?>
                </span>
                <span class = 'vertical' ng-if = "fillterTypeId == 2">
                  <?= $this->Html->link(
                    $this->Html->image('dock_bottom_color.png', array('alt' => 'メニュー', 'width'=>50, 'height'=>50)),
                    'javascript:void(0)',
                    array('escape' => false,
                      'style' => 'display: inline-block; height: 30px;margin-top:-9px;')); ?>
                </span>
              </li>
              <li ng-class="{on:fillterTypeId===2}" ng-click="fillterTypeId = 2" style = "margin-top:0; width:5em !important;">
              <span class = 'side' ng-if = "fillterTypeId == 1">
                <?= $this->Html->link(
                    $this->Html->image('dock_right_color.png', array('alt' => 'メニュー', 'width'=>50, 'height'=>50)),
                    'javascript:void(0)',
                    array('escape' => false,
                      'style' => 'display: inline-block; height: 30px;margin-top:-9px;')); ?>
                </span>
               <span class = 'side' ng-if = "fillterTypeId == 2">
                <?= $this->Html->link(
                    $this->Html->image('dock_right.png', array('alt' => 'メニュー', 'width'=>50, 'height'=>50)),
                    'javascript:void(0)',
                    array('escape' => false,
                      'style' => 'display: inline-block; height: 30px;margin-top:-9px;')); ?>
                </span>
              </li>
            </ul>
        </div>
          <div class="nowInfo card" style = "border-bottom: 1px solid #bfbfbf; width:100%; margin-top: 25px;">
          <dl>
            <dt>ユーザID</dt>
            <dd id = "visitorsId"><?= $defaultHistoryList['THistory']['visitors_id'] ?></dd>
            <dt>IPアドレス</dt>
            <dd id = "LandscapeData">
            <?php if(isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]): ?>
                  <?php if(!empty($defaultHistoryList['LandscapeData']['org_name']) && !empty($defaultHistoryList['LandscapeData']['lbc_code'])): ?>
                      <a href="javascript:void(0)" class="underL" onclick="openCompanyDetailInfo('<?=$defaultHistoryList['LandscapeData']['lbc_code']?>')">
                      <span id = "Landscape"><?=h($defaultHistoryList['LandscapeData']['org_name'])?></span></a><br>
                  <?php elseif(!empty($defaultHistoryList['LandscapeData']['org_name'])): ?>
                      <p><?=h($defaultHistoryList['LandscapeData']['org_name'])?></p><?='\n'?>
                  <?php endif; ?>
                <?php endif; ?>
                <span id= "ipAddress">{{ ip('<?=h($defaultHistoryList['THistory']['ip_address'])?>', <?php echo !empty($defaultHistoryList['LandscapeData']['org_name']) ? 'true' : 'false' ?>) }}</span></dd>
            <dt>訪問回数</dt>
            <dd id = "visitCounts"><?= $tHistoryCountData.'回' ?></dd>
            <dt>プラットフォーム</dt>
            <dd id = "platform">
              {{ ua('<?=h($defaultHistoryList['THistory']['user_agent'])?>') }}
            </dd>
          </dl>
        </div>
        <div class="hardInfo card" style = "width:100%;">
          <dl>
            <dt>キャンペーン</dt>
            <dd><?=$campaignParam?></dd>
            <dt>ランディングページ</dt>
            <dd id = "landing">
            <a href = "<?=h($stayList[$defaultHistoryList['THistory']['id']]['THistoryStayLog']['firstURL'])?>" target = "landing">
            <span id = "landingPage"><?= $stayList[$defaultHistoryList['THistory']['id']]['THistoryStayLog']['title'] ?></span></a></dd>
            <dt>チャット送信ページ</dt>
            <dd id = "chatSending">
            <a href = "<?=h($defaultHistoryList['THistoryStayLog']['url'])?>" target = "landing">
            <span id = "chatSendingPage"><?= $defaultHistoryList['THistoryStayLog']['title'] ?></span></a></dd>
            <dt>離脱ページ</dt>
            <dd id = "separation">
            <a href = "<?=h($defaultHistoryList['LastSpeechSendPage']['url'])?>" target = "landing">
            <span id = "separationPage"><?= $defaultHistoryList['LastSpeechSendPage']['title'] ?></span></a></dd></dd>
            <dt>閲覧ページ数</dt>
            <dd>
            <?php if( is_numeric($stayList[$defaultHistoryList['THistory']['id']]['THistoryStayLog']['count']) ): ?>
              <span id = "pageCount"><?=h($stayList[$defaultHistoryList['THistory']['id']]['THistoryStayLog']['count'])?></span>
              <a id = "moveHistory" class="underL" href="javascript:void(0)" onclick="openHistoryById('<?=h($defaultHistoryList['THistory']['id'])?>')" >(移動履歴)</a>
            <?php endif; ?></dd>
          </dl>
        </div>
        <div class="detailForm card">
          <ul>
            <li>
              <label for="ng-customer-company">会社名</label>
              <input type="text"  data-key='company' class="infoData" id="ng-customer-company" value ="<?= $mCusData['informations']['company'] ?>" ng-blur="saveCusInfo('company', customData)"  placeholder="会社名を追加" />
            </li>
            <li>
              <label for="ng-customer-name">名前</label>
              <input type="text" data-key='name' class = "infoData" id="ng-customer-name" value ="<?= $mCusData['informations']['name'] ?>" ng-blur="saveCusInfo('name', customData)" placeholder="名前を追加">
            </li>
            <li>
              <label for="ng-customer-tel">電話番号</label>
              <input type="text" data-key='tel' class = "infoData" id="ng-customer-tel" value ="<?= $mCusData['informations']['tel'] ?>" ng-blur="saveCusInfo('tel', customData)"  placeholder="電話番号を追加" />
            </li>
            <li>
              <label for="ng-customer-mail">メールアドレス</label>
              <input type="text" data-key='mail' class = "infoData" id="ng-customer-mail" value ="<?= $mCusData['informations']['mail'] ?>" ng-blur="saveCusInfo('mail', customData)" placeholder="メールアドレスを追加" />
            </li>
            <li>
              <label for="ng-customer-memo" style = "width:60% !important">メモ</label>
              <textarea rows="7" data-key='memo' class = "infoData" id="ng-customer-memo" placeholder="メモを追加"><?= $mCusData['informations']['memo'] ?></textarea>
            </li>
          </ul>
          <div id="personal_action">
              <?= $this->Html->link('元に戻す', 'javascript:void(0)', ['onclick' => 'reloadAct()', 'class' => 'whiteBtn btn-shadow lineUpSaveBtn historyReturnButton']) ?>
              <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'customerInfoSave('.$historyId.')','id' => 'customerInfo', 'class' => 'greenBtn btn-shadow lineUpSaveBtn hitoryUpdateButton']) ?>
          </div>
        </div>
      </div>
</div>
</div>
<?php
echo $this->Form->input('customerId', ['type'=>'hidden', 'id' => 'customerId', 'value' => "", 'label' => false, 'div'=> false]);
?>

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