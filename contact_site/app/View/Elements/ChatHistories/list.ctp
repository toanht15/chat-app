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
    array('escape' => false,'class'=>'btn-shadow skyBlueBtn','id' => 'searchRefine','onclick' => 'openSearchRefine()','style' => 'position: absolute;
  top: 15px;
  left: 32em;
  width: 8em;
  padding: 0.25em 0.5em;
  text-align: center;'));
  ?>
  <span id="searchPeriod">検索期間</span>
    <div class = "form01 fRight"  style = "margin-top: -19px; display:inline-block; height: 40px; margin-bottom:22px; margin-right: 24px;">
    <?php
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
                 'style' => 'display: inline-block; height: 30px; margin-top:-5px;')); ?>
        </span>
          <span class = 'vertical' ng-if = "fillterTypeId == 2">
          <?= $this->Html->link(
              $this->Html->image('dock_bottom_color.png', array('alt' => 'メニュー', 'width'=>50, 'height'=>50)),
              'javascript:void(0)',
              array('escape' => false,
                  'style' => 'display: inline-block; height: 30px;margin-top:-5px;')); ?>
        </span>
        </li>
        <li ng-class="{on:fillterTypeId===2}" ng-click="fillterTypeId = 2" style = "margin-top:0; width:5em !important;">
      <span class = 'side' ng-if = "fillterTypeId == 1">
        <?= $this->Html->link(
            $this->Html->image('dock_right_color.png', array('alt' => 'メニュー', 'width'=>50, 'height'=>50)),
            'javascript:void(0)',
            array('escape' => false,
                'style' => 'display: inline-block; height: 30px;margin-top:-5px;')); ?>
        </span>
          <span class = 'side' ng-if = "fillterTypeId == 2">
        <?= $this->Html->link(
            $this->Html->image('dock_right.png', array('alt' => 'メニュー', 'width'=>50, 'height'=>50)),
            'javascript:void(0)',
            array('escape' => false,
                'style' => 'display: inline-block; height: 30px;margin-top:-5px;')); ?>
        </span>
        </li>
      </ul>
  </div>
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
    <div class='fLeft' style="width:100%;margin-bottom: 10px;margin-top: -10px;">
      <div id="btnSet">
       <span id = "outputCsv">
           <?= $this->Html->image('csv.png', array(
               'alt' => 'CSV出力',
               'id'=>$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "history_csv_btn" : "disabled_history_csv_btn",
               'class' => 'btn-shadow disOffgrayBtn commontooltip',
               'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
               'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "CSV出力" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
               'data-balloon-position' => '36',
               'width' => 45,
               'height' => 45,
               'onclick' => 'selectCsv()',
               'url'=>array('controller'=>'ChatHistories','action'=>'outputCSVOfChat'),
               'style' => 'margin-left:-4px;'
           )) ?>
       </span>
       <?php if($permission_level == 1) { ?>
        <span>
          <a>
            <?= $this->Html->image('dustbox.png', array(
                'alt' => '削除',
                'id'=>$coreSettings[C_COMPANY_USE_HISTORY_DELETE] ? "history_dustbox_btn" : "disabled_history_dustbox_btn",
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_DELETE],
                'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_DELETE] ? "削除する" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
                'data-balloon-position' => '36',
                'onclick' => 'selectDeleteChat()',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
      <?php } ?>
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
  <table class = "scroll" id = "chatTable">
      <thead>
        <tr>
          <th style = "width:2%" id = "check" width = "3%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
          <th width = "3%" id = "info" style = "width:20%">情報</th>
          <th width = "3%" id = "kind" style = "width:5%; display:none;">種別</th>
          <th id = "firstTimeReceivingLabel" style = "width:5%;display:none;min-width:79px;">初回チャット<br>受信日時<div class="questionBalloon questionBalloonPosition13">
            <icon class="questionBtn">？</icon>
          </div></th>
          <th style = "width:5%;display:none;" id = "ip">IPアドレス</th>
          <th style = "width:5%;display:none;" id = "visitor">訪問ユーザ</th>
          <th id = "campaign" style = "width:3%" >キャンペーン</th>
          <th id = "sendChatPageLabel" style = "width:13%">チャット送信ページ<div class="questionBalloon questionBalloonPosition8">
            <icon class="questionBtn">？</icon>
          </div></th>
          <th style = "min-width:33px;" id = "achievement">成果</th>
          <th style = "min-width:79px;" id = "manualReceivingLabel">有人チャット<br>受信日時<div class="questionBalloon questionBalloonPosition13">
            <icon class="questionBtn">？</icon>
            </div></th>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
          <th style = "min-width:71px;" id="lastSpeechLabel">最終発言後<br>離脱時間<div class="questionBalloon questionBalloonPosition13">
              <icon class="questionBtn">？</icon>
            </div></th>
           <th style = "width:6%;display:none;" id = "responsible">担当者</th>
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
        if($historyId == $history['THistory']['id']) {
          $userCampaignParam = "";
          $tmp = mb_strstr($stayList[$history['THistory']['id']]['THistoryStayLog']['firstURL'], '?');
          if ( $tmp !== "" ) {
            foreach($campaignList as $k => $v){
              if ( strpos($tmp, $k) !== false ) {
                if ( $userCampaignParam !== "" ) {
                  $userCampaignParam .= "\n";
                }
                $userCampaignParam .= h($v);
              }
            }
          }
        }
        ?>
        <?php
        if ((isset($history['THistoryChatLog']['type']) && isset($data['History']['chat_type']) && isset($chatType) &&
        $history['THistoryChatLog']['type'] === $chatType[$data['History']['chat_type']]) || empty($chatType)) { ?>
          <tr id = "<?=h($history['THistory']['id'])?>" ng-click="getOldChat('<?=h($history['THistory']['id'])?>', false)" onclick="openChatById('<?=h($history['THistory']['id'])?>');" class = "showBold trHeight" style="height:72px;">
            <td class="tCenter checkBox" onclick="event.stopPropagation();" style = "width:6%">
              <input type="checkbox" name="selectTab" id="selectTab<?=h($history['THistory']['id'])?>" value="<?=h($history['THistory']['id'])?>">
              <label for="selectTab<?=h($history['THistory']['id'])?>"></label>
            </td>
            <td style = "width:35%; padding-left:10px;" class = "eachInfo">
            <div class = "info">
              <?php if( is_numeric($history['THistoryChatLog']['count']) ): ?>
                <div class = "firstChatTime" style = "height:60px;">
                  <?php if (!empty($history['SpeechTime']['firstSpeechTime']) && date('Y/m/d') == date_format(date_create($history['SpeechTime']['firstSpeechTime']), "Y/m/d")){ ?>
                   <?=date_format(date_create($history['SpeechTime']['firstSpeechTime']), "H:i")?>
                  <?php }
                  else if(!empty($history['SpeechTime']['firstSpeechTime']) && date('Y/m/d') != date_format(date_create($history['SpeechTime']['firstSpeechTime']), "Y/m/d")) {
                    $firstSpeechTimeMonth = date_format(date_create($history['SpeechTime']['firstSpeechTime']), "m月");
                    $firstSpeechTimeDay = date_format(date_create($history['SpeechTime']['firstSpeechTime']), "d日"); ?>
                    <?=ltrim($firstSpeechTimeMonth, '0').ltrim($firstSpeechTimeDay, '0');?>
                  <?php } ?>
                </div>
                  <?php
                   if ((!empty($history['THistoryChatLog']['type']) && $history['THistoryChatLog']['type'] == "自動返信")
                    || ($history['THistoryChatLog']['cmp'] == 0 && $history['THistoryChatLog']['sry'] == 0 && $history['THistoryChatLog']['cus'] == 0)) { ?>
                    <li class = "largeCharacters" style = "color:#4bacc6; font-weight:bold;display: flex;overflow: hidden;white-space: nowrap;"><div class ="chatTypeName" style = "border: 1px solid #4bacc6;background-color:#4bacc6;border-radius:4px;padding:1px 3px;">Auto</div><div class = "largeCharacters enter" style ="margin-left:3px;color:#4bacc6"><?php if (isset($chatUserList[$history['THistory']['id']])) { echo '('.$chatUserList[$history['THistory']['id']].')'; } ?></div></li>
                  <?php
                  }
                  else if(!empty($history['THistoryChatLog']['type']) && $history['THistoryChatLog']['type'] == "拒否") { ?>
                    <li class = "largeCharacters" style = "color:#a6a6a6; font-weight:bold;display: flex;overflow: hidden;white-space: nowrap;"><div class ="chatTypeName" style = "border: 1px solid #a6a6a6;background-color:#a6a6a6;border-radius:4px;padding:1px 3px;">Sorry</div></li>
                  <?php
                  }
                  else if($history['THistoryChatLog']['type'] == "") { ?>
                    <li class = "largeCharacters" style = "color:#9bbb59; font-weight:bold;display: flex;overflow: hidden;white-space: nowrap;"><div class ="chatTypeName" style = "border: 1px solid #9bbb59;background-color:#9bbb59;border-radius:4px;padding:1px 3px;width: 52px !important;">Manual</div><div class = "largeCharacters enter" style ="margin-left:3px;color:#9bbb59;">(<?php if (isset($chatUserList[$history['THistory']['id']])) { echo $chatUserList[$history['THistory']['id']]; } ?>)</span></div></span>
                  <?php
                  }
                  else if($history['THistoryChatLog']['type'] == "未入室") { ?>
                    <li class = "largeCharacters" style = "color:#f79646; font-weight:bold;display: flex;overflow: hidden;white-space: nowrap;"><div class ="chatTypeName" style = "border: 1px solid #f79646;background-color:#f79646;border-radius:4px;padding:1px 3px;">NoEntry</div><div class = "largeCharacters" style ="margin-left:3px;color:#f79646">(＊未入室)</div></li>
                  <?php
                    }
                 endif; ?>
              <?php if(isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]) { ?>
                <?php if(!empty($history['LandscapeData']['org_name']) && !empty($history['LandscapeData']['lbc_code'])): ?>
                    <li style = "white-space: nowrap;overflow: hidden;"><div style = "padding-top:1px;margin-top:3px;"><a href="javascript:void(0)" style ="font-weight:bold;" class="underL largeCharacters" onclick="openCompanyDetailInfo('<?=$history['LandscapeData']['lbc_code']?>')"><?=h($history['LandscapeData']['org_name'])?></a></div></li>
                <?php elseif(!empty($history['LandscapeData']['org_name'])): ?>
                   <li style = "white-space: nowrap;overflow: hidden;"> <p><?=h($history['LandscapeData']['org_name'])?></p></li><?='\n'?>
                <?php elseif(empty($history['LandscapeData']['org_name'])): ?>
                <li style = "white-space: nowrap;overflow: hidden;"><div class = "largeCharacters" style = "padding-top:1px;font-weight:bold;margin-top:3px;">{{ ip('<?=h($history['THistory']['ip_address'])?>', <?php echo !empty($history['LandscapeData']['org_name']) ? 'true' : 'false' ?>) }}</div></li>
                <?php endif; ?>
              <?php } else { ?>
              <li style = "white-space: nowrap;overflow: hidden;"><div class = "largeCharacters" style = "padding-top:1px;font-weight:bold;margin-top:3px;">
              {{ ip('<?=h($history['THistory']['ip_address'])?>', <?php echo !empty($history['LandscapeData']['org_name']) ? 'true' : 'false' ?>) }}</div></li>
              <?php } ?>
              <li style = "white-space: nowrap;overflow: hidden;height:20px;"><div style = "padding-top:1px;" class = "largeCharacters">{{ ui('<?=h($history['THistory']['ip_address'])?>','<?=$visitorsId?>') }}</div></li></td>
              <td class="tCenter eachKind" style = "width:5%;display:none;">
              <?php if( is_numeric($history['THistoryChatLog']['count']) ): ?>
                <?php
                 if ((!empty($history['THistoryChatLog']['type']) && $history['THistoryChatLog']['type'] == "自動返信")
                  || ($history['THistoryChatLog']['cmp'] == 0 && $history['THistoryChatLog']['sry'] == 0 && $history['THistoryChatLog']['cus'] == 0)) { ?>
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
            <td class="tRight pre eachFirstSpeechTime" style = "width:5%;display:none;"><?php if (!empty($history['SpeechTime']['firstSpeechTime'])){ ?><?=date_format(date_create($history['SpeechTime']['firstSpeechTime']), "Y/m/d\nH:i:s")?><?php } ?></td>
            <td class="tLeft ip-address eachIpAddress" style = "width:10%;display:none;">
              <?php if(isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]): ?>
                <?php if(!empty($history['LandscapeData']['org_name']) && !empty($history['LandscapeData']['lbc_code'])): ?>
                    <a href="javascript:void(0)" class="underL" onclick="openCompanyDetailInfo('<?=$history['LandscapeData']['lbc_code']?>')"><?=h($history['LandscapeData']['org_name'])?></a><br>
                <?php elseif(!empty($history['LandscapeData']['org_name'])): ?>
                    <p><?=h($history['LandscapeData']['org_name'])?></p><?='\n'?>
                <?php endif; ?>
              <?php endif; ?>
              {{ ip('<?=h($history['THistory']['ip_address'])?>', <?php echo !empty($history['LandscapeData']['org_name']) ? 'true' : 'false' ?>) }}
            </td>
            <td class="tLeft pre eachVisitor" style = "width:10%;display:none;">{{ ui('<?=h($history['THistory']['ip_address'])?>', '<?=$visitorsId?>') }}</td>
            <?php ?>
            <td class="tCenter pre" style = "width:10%"><div class = "campaignInfo"><?=$campaignParam?></div></td>
            <td class="pre" style = "font-size:11px;padding:8px 5px !important;width:32%;"><a href = "<?=h($forChatSendingPageList[$history['THistoryChatLog']['t_history_stay_logs_id']]['THistoryStayLog']['url'])?>" target = "landing"><?= $forChatSendingPageList[$history['THistoryChatLog']['t_history_stay_logs_id']]['THistoryStayLog']['title'] ?></a></td>
            <td class="tCenter" style = "width:5%"><?php
              if($history['THistoryChatLog']['eff'] == 0 || $history['THistoryChatLog']['cv'] == 0 ) {
                if (isset($history['THistoryChatLog']['achievementFlg'])){
                  echo !empty($achievementType[h($history['THistoryChatLog']['achievementFlg'])]) ? $achievementType[h($history['THistoryChatLog']['achievementFlg'])] : "";
                }
              }
              else if ($history['THistoryChatLog']['eff'] != 0 && $history['THistoryChatLog']['cv'] != 0) {
                if (isset($history['THistoryChatLog']['achievementFlg'])){
                  echo $achievementType[2].nl2br("\n").$achievementType[0];
                }
              }
            ?></td>
            <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
              <td class="tRight pre" style = "width:5%;"><?php if (!empty($history['NoticeChatTime']['created'])){ ?><?=date_format(date_create($history['NoticeChatTime']['created']), "Y/m/d\nH:i:s")?><?php } ?>
              </td>
              <td class="tCenter" style = "width:4%;"><?php
              if ($history['SpeechTime']['SpeechTime']
                && $history['THistory']['access_date'] !== $history['THistory']['out_date']
                && strtotime($history['SpeechTime']['SpeechTime']) <= strtotime($history['THistory']['out_date'])){
                echo $this->htmlEx->calcTime($history['SpeechTime']['SpeechTime'], $history['THistory']['out_date']);
              }
            ?></td>
            <td class="tCenter pre responsible" style = "width:10%;display:none;"><?php if (isset($chatUserList[$history['THistory']['id']])) { echo $chatUserList[$history['THistory']['id']]; } ?></td>
          <?php endif; ?>
          </tr>
        <?php } ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</div>

<div id = "detail" class = "detail" style = "width: 100%; background-color: #f2f2f2; display:none;">

    <div id="verticalToggleMenu" ng-init = "setDetailMode(1)" ng-if="fillterTypeId === 1" class = "form01" style = "">
    <ul class="switch" style = "box-shadow:none; padding-left: 17px; margin-bottom: 0;">
      <li ng-class="{on:switchDetailMode===1}" ng-click="setDetailMode(1)" style = "margin-top:0; margin-bottom:0; width:9em !important;">
        <span ng-if="switchDetailMode===1" style="margin: 0; padding: 5px 0; color: #FFFFFF;">チャット内容</span>
        <span ng-if="switchDetailMode===2" style="margin: 0; padding: 5px 0; color: #c3d69b;">チャット内容</span>
      </li>
      <li ng-class="{on:switchDetailMode===2}" ng-click="setDetailMode(2)" style = "margin-top:0; margin-bottom:0; width:9em !important;">
        <span ng-if="switchDetailMode===1" style="margin: 0; padding: 5px 0; color: #c3d69b;">詳細情報</span>
        <span ng-if="switchDetailMode===2" style="margin: 0; padding: 5px 0; color: #FFFFFF;">詳細情報</span>
      </li>
    </ul>
  </div>

  <?php if(!empty($edit)) { ?>
    <div id="verticalToggleMenu" ng-init = "setDetailMode(2)" ng-if="fillterTypeId === 2" class = "form01" style = "">
  <?php }
    else { ?>
    <div id="verticalToggleMenu" ng-init = "setDetailMode(1)" ng-if="fillterTypeId === 2" class = "form01" style = "">
  <?php } ?>
    <ul class="switch" style = "box-shadow:none; padding-left: 17px; margin-bottom: 0;">
      <li ng-class="{on:switchDetailMode===1}" ng-click="setDetailMode(1)" style = "margin-top:0; margin-bottom:0; width:9em !important;">
        <span ng-if="switchDetailMode===1" style="margin: 0; padding: 5px 0; color: #FFFFFF;">チャット内容</span>
        <span ng-if="switchDetailMode===2" style="margin: 0; padding: 5px 0; color: #c3d69b;">チャット内容</span>
      </li>
      <li ng-class="{on:switchDetailMode===2}" ng-click="setDetailMode(2  )" style = "margin-top:0; margin-bottom:0; width:9em !important;">
        <span ng-if="switchDetailMode===1" style="margin: 0; padding: 5px 0; color: #c3d69b;">詳細情報</span>
        <span ng-if="switchDetailMode===2" style="margin: 0; padding: 5px 0; color: #FFFFFF;">詳細情報</span>
      </li>
    </ul>
  </div>

  <div id="cus_info_contents"  class="flexBoxCol">
    <div id="leftContents" ng-show="judgeShowChatContent()" style = "width: 100%;padding: 1em 1.5em 1em 1.5em;">
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
        <ul class="historyList" style = "margin-top: 0;">
          <li class = "pastChatShowBold" id = "oldChatList" ng-click="getOldChat(historyId, true)" ng-repeat="(historyId, firstDate) in chatLogList"><span>{{firstDate | date:'yyyy年M月d日（EEE）a hh時mm分ss秒' }}</span></li>
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
        <div id="customerInfoScrollArea" ng-show="judgeShowCustomerContent()" style = "width:100% !important;">
          <div id="rightContents" style = "width:100% !important; margin-bottom: 4em;">
        <?php if(!empty($defaultHistoryList) && !empty($tHistoryCountData)) { ?>
          <div class="nowInfo card" style = "border-bottom: 1px solid #bfbfbf; width:100%; margin-top: 20px;">
            <dl>
            <li>
              <dt>ユーザID</dt>
              <dd id = "visitorsId"><?= $defaultHistoryList['THistory']['visitors_id'] ?></dd>
            </li>
            <li>
              <dt>IPアドレス</dt>
              <dd id = "LandscapeData">
              <?php if(isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]): ?>
                <?php if(!empty($defaultHistoryList['LandscapeData']['org_name']) && !empty($defaultHistoryList['LandscapeData']['lbc_code'])): ?>
                    <a href="javascript:void(0)" class="underL" onclick="openCompanyDetailInfo('<?=$defaultHistoryList['LandscapeData']['lbc_code']?>')">
                    <span id = "Landscape"><?=h($defaultHistoryList['LandscapeData']['org_name'])?></span></a>
                <?php elseif(!empty($defaultHistoryList['LandscapeData']['org_name'])): ?>
                    <p><?=h($defaultHistoryList['LandscapeData']['org_name'])?></p><?='\n'?>
                <?php elseif(empty($defaultHistoryList['LandscapeData']['org_name'])): ?>
                  <span id = "Landscape"></span>
                  <?php $defaultHistoryList['THistory']['ip_address'] = '('.$defaultHistoryList['THistory']['ip_address'].')' ?>
                <?php endif; ?>
              <?php else: ?>
                <?php $defaultHistoryList['THistory']['ip_address'] = '('.$defaultHistoryList['THistory']['ip_address'].')' ?>
              <?php endif; ?>
              <span id= "ipAddress">{{ ip('<?=h($defaultHistoryList['THistory']['ip_address'])?>', <?php echo !empty($defaultHistoryList['LandscapeData']['org_name']) ? 'true' : 'false' ?>) }}</span></dd>
            </li>
            <li>
              <dt>訪問回数</dt>
              <dd id = "visitCounts"><?= $tHistoryCountData.'回' ?></dd>
            </li>
            <li>
              <dt>プラットフォーム</dt>
              <dd id = "platform">
                {{ ua('<?=h($defaultHistoryList['THistory']['user_agent'])?>') }}
              </dd>
            </li>
            </dl>
          </div>
        <?php } ?>
        <div class="hardInfo card" style = "width:100%;">
        <?php if(!empty($defaultHistoryList) && !empty($tHistoryCountData)) { ?>
          <dl>
          <li>
            <dt>キャンペーン</dt>
            <dd id = "campaignParam"><?=$userCampaignParam?></dd>
          </li>
          <li>
          <li>
            <dt>参照元URL</dt>
            <dd id = "referrer">
            <a href="<?=h($history['THistory']['referrer_url'])?>" target="history">
            <span id = "referrerUrl"><?=h($history['THistory']['referrer_url']) ?></span></a></dd>
          </li>
          <li>
            <dt>ランディングページ</dt>
            <dd id = "landing">
            <a href = "<?=h($stayList[$defaultHistoryList['THistory']['id']]['THistoryStayLog']['firstURL'])?>" target = "landing">
            <span id = "landingPage"><?= $stayList[$defaultHistoryList['THistory']['id']]['THistoryStayLog']['title'] ?></span></a></dd>
          </li>
          <li>
            <dt>チャット送信ページ</dt>
            <dd id = "chatSending">
            <a href = "<?=h($forChatSendingPageList[$defaultHistoryList['THistoryChatLog']['t_history_stay_logs_id']]['THistoryStayLog']['url'])?>" target = "landing">
            <span id = "chatSendingPage"><?= $forChatSendingPageList[$defaultHistoryList['THistoryChatLog']['t_history_stay_logs_id']]['THistoryStayLog']['title'] ?></span></a></dd>
          </li>
          <li>
            <dt>離脱ページ</dt>
            <dd id = "separation">
            <a href = "<?=h($detailChatPagesData[0]['THistoryStayLog']['url'])?>" target = "landing">
            <span id = "separationPage"><?= $detailChatPagesData[0]['THistoryStayLog']['title'] ?></span></a></dd></dd>
          </li>
          <li>
            <dt>閲覧ページ数</dt>
            <dd>
            <?php if( is_numeric($stayList[$defaultHistoryList['THistory']['id']]['THistoryStayLog']['count']) ): ?>
              <span id = "pageCount"><?=h($stayList[$defaultHistoryList['THistory']['id']]['THistoryStayLog']['count'])?></span>
              <a id = "moveHistory" class="underL" href="javascript:void(0)" onclick="openHistoryById('<?=h($defaultHistoryList['THistory']['id'])?>')" >(移動履歴)</a>
            <?php endif; ?></dd>
          </li>
          </dl>
           <?php } ?>
        </div>
        <div class="detailForm card">
        <?php if(!empty($defaultHistoryList) && !empty($tHistoryCountData)) { ?>
          <ul>
            <li>
            <?php $this->log('mCusData',LOG_DEBUG); $this->log($mCusData,LOG_DEBUG); ?>
              <label for="ng-customer-company">会社名</label>
              <input type="text"  data-key='company' class="infoData" id="ng-customer-company" value ="<?= !empty($mCusData['informations']['company']) ? $mCusData['informations']['company'] : "" ?>" ng-blur="saveCusInfo('company', customData)"  placeholder="会社名を追加" />
            </li>
            <li>
              <label for="ng-customer-name">名前</label>
              <input type="text" data-key='name' class = "infoData" id="ng-customer-name" value ="<?= !empty($mCusData['informations']['name']) ? $mCusData['informations']['name'] : "" ?>" ng-blur="saveCusInfo('name', customData)" placeholder="名前を追加">
            </li>
            <li>
              <label for="ng-customer-tel">電話番号</label>
              <input type="text" data-key='tel' class = "infoData" id="ng-customer-tel" value ="<?= !empty($mCusData['informations']['tel']) ? $mCusData['informations']['tel'] : "" ?>" ng-blur="saveCusInfo('tel', customData)"  placeholder="電話番号を追加" />
            </li>
            <li>
              <label for="ng-customer-mail">メールアドレス</label>
              <input type="text" data-key='mail' class = "infoData" id="ng-customer-mail" value ="<?= !empty($mCusData['informations']['mail']) ? $mCusData['informations']['mail'] : "" ?>" ng-blur="saveCusInfo('mail', customData)" placeholder="メールアドレスを追加" />
            </li>
            <li>
              <label for="ng-customer-memo" style = "width:60% !important">メモ</label>
              <textarea rows="7" data-key='memo' class = "infoData" id="ng-customer-memo" placeholder="メモを追加"><?= !empty($mCusData) ? $mCusData['informations']['memo'] : "" ?></textarea>
            </li>
          </ul>
          <div id="personal_action">
              <?= $this->Html->link('元に戻す', 'javascript:void(0)', ['onclick' => 'reloadAct('.$historyId.')', 'id' => 'restore','class' => 'whiteBtn btn-shadow lineUpSaveBtn historyReturnButton']) ?>
              <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'customerInfoSave('.$historyId.')','id' => 'customerInfo', 'class' => 'greenBtn btn-shadow lineUpSaveBtn hitoryUpdateButton']) ?>
          </div>
          <?php } ?>
        </div>
      </div>
        </div>
</div>
</div>
<?php
$customerId = "";
if ( isset($mCusData['MCustomer']['id']) ) {
  $customerId = $mCusData['MCustomer']['id'];
}
echo $this->Form->input('customerId', ['type'=>'hidden','id' => 'customerId', 'value' => $customerId, 'label' => false, 'div'=> false]);
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