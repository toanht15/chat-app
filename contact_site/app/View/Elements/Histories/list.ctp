<table>
  <thead>
  <tr>
    <th width=" 5.5%">日時</th>
    <th width=" 4%" class="noOutCsv">詳細</th>
    <th width="10%">IPアドレス</th>
    <th width="10%">訪問ユーザ</th>
    <th width="10%">プラットフォーム<br>ブラウザ</th>
    <th width=" 6%">キャンペーン</th>
    <th width=" 13%">流入ページタイトル</th>
    <th width=" 3.5%">閲覧<br>ページ数</th>
    <th width="10%">参照元URL</th>
    <th width=" 5%">滞在時間</th>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
      <th id="lastSpeechLabel" width=" 6%">最終発言後<br>離脱時間
        <div class="questionBalloon commontooltip" data-text="サイト訪問者が最後に発言してからページを離脱するまでの時間">
          <icon class="questionBtn">？</icon>
        </div>
      </th>
      <th width=" 4%">成果</th>
      <th width="7%">チャット</th>
      <th width="10%">担当者</th>
    <?php endif; ?>
  </tr>
  </thead>
  <tbody ng-cloak>
  <?php foreach ($historyList as $key => $history): ?>
    <?php
    /* キャンペーン名の取得 */
    $campaignParam = "";
    if (!empty($stayList[$history['THistory']['id']])) {
      $tmp = mb_strstr($stayList[$history['THistory']['id']]['THistoryStayLog']['firstURL'], '?');
      if ($tmp !== "") {
        foreach ($campaignList as $k => $v) {
          if (strpos($tmp, $k) !== false) {
            if ($campaignParam !== "") {
              $campaignParam .= "\n";
            }
            $campaignParam .= h($v);
          }
        }
      }
    }
    $visitorsId = "";
    if (isset($history['THistory']['visitors_id'])) {
      $visitorsId = $history['THistory']['visitors_id'];
    }
    ?>
    <tr>
      <td class="tRight pre"><?= date_format(date_create($history['THistory']['access_date']), "Y/m/d\nH:i:s") ?></td>
      <td class="tCenter">
        <ng-show-detail data-id="<?= h($history['THistory']['id']) ?>"></ng-show-detail>
      </td>
      <td class="tLeft">
        <?php if (isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]): ?>
          <?php if (!empty($history['LandscapeData']['org_name']) && !empty($history['LandscapeData']['lbc_code'])): ?>
            <a href="javascript:void(0)" class="underL"
               onclick="openCompanyDetailInfo('<?= $history['LandscapeData']['lbc_code'] ?>')"><?= h($history['LandscapeData']['org_name']) ?></a>
            <br>
          <?php elseif (!empty($history['LandscapeData']['org_name'])): ?>
            <p><?= h($history['LandscapeData']['org_name']) ?></p><?= '\n' ?>
          <?php endif; ?>
        <?php endif; ?>
        {{ ip('<?= h(h($history['THistory']['ip_address'])) ?>
        ', <?php echo !empty($history['LandscapeData']['org_name']) ? 'true' : 'false' ?>) }}
      </td>
      <td class="tLeft pre">{{ ui('<?= h(h($history['THistory']['ip_address'])) ?>', '<?= $visitorsId ?>') }}</td>
      <td class="tLeft pre">{{ ua('<?= h(h($history['THistory']['user_agent'])) ?>') }}</td>
      <td class="tCenter pre"><?= $campaignParam ?></td>
      <td class="pre" style="font-size:11px;padding:8px 5px !important"><a
            href="<?= h((!empty($stayList[$history['THistory']['id']])) ? $stayList[$history['THistory']['id']]['THistoryStayLog']['firstURL'] : '') ?> "
            target="landing"><?= (!empty($stayList[$history['THistory']['id']]) ? $stayList[$history['THistory']['id']]['THistoryStayLog']['title'] : '') ?></a>
      </td>
      <td class="tCenter">
        <?php if (!empty($stayList[$history['THistory']['id']]) && is_numeric($stayList[$history['THistory']['id']]['THistoryStayLog']['count'])): ?>
          <a class="underL" href="javascript:void(0)"
             onclick="openHistoryById('<?= h($history['THistory']['id']) ?>')"><?= h($stayList[$history['THistory']['id']]['THistoryStayLog']['count']) ?></a>
        <?php endif; ?>
      </td>
      <td class="tLeft omit"><a href="{{::trimToURL('<?= h(h($history['THistory']['referrer_url'])) ?>',1)}}"
                                target="history">{{::trimToURL("<?= h(h($history['THistory']['referrer_url'])) ?>",2)}}</a>
      </td>
      <td class="tCenter"><?= $this->htmlEx->calcTime($history['THistory']['access_date'],
          $history['THistory']['out_date']) ?></td>
      <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
        <td class="tCenter"><?php
          if ($history['LastSpeechTime']['lastSpeechTime']
            && $history['THistory']['access_date'] !== $history['THistory']['out_date']
            && strtotime($history['LastSpeechTime']['lastSpeechTime']) <= strtotime($history['THistory']['out_date'])) {
            echo $this->htmlEx->calcTime($history['LastSpeechTime']['lastSpeechTime'],
              $history['THistory']['out_date']);
          }
          ?></td>
        <td class="tCenter"><?php
          if (!empty($history['THistoryChatLog']['eff']) && $history['THistoryChatLog']['eff'] != 0) {
            echo $achievementType[2];
          } else {
            if (!empty($history['THistoryChatLog']['deny']) && $history['THistoryChatLog']['deny'] != 0) {
              echo $achievementType[1];
            }
          }
          if (isset($history['THistoryChatLog']['terminate']) && $history['THistoryChatLog']['terminate'] != 0 && $history['THistoryChatLog']['cv'] == 0) {
            echo $achievementType[3];
          } else {
            if (isset($history['THistoryChatLog']) && $history['THistoryChatLog']['cv'] != 0) {
              echo $achievementType[0];
            }
          }
          ?></td>
        <td class="tCenter">
          <?php if (isset($history['THistoryChatLog']) && is_numeric($history['THistoryChatLog']['count'])): ?>
            <a class="underL showBold" href="javascript:void(0)"
               onclick="openChatById('<?= h($history['THistory']['id']) ?>')">履歴<?php if (isset($history['THistoryChatLog']) && !empty($history['THistoryChatLog']['type'])) {
                echo "（" . h($history['THistoryChatLog']['type']) . "）";
              } ?></a>
          <?php endif; ?>
          <?php if (isset($history['THistoryChatLog']) && !is_numeric($history['THistoryChatLog']['count'])): ?>（未対応）<?php endif; ?>
        </td>
        <td class="tCenter pre"><?php if (isset($chatUserList[$history['THistory']['id']])) {
            echo $chatUserList[$history['THistory']['id']];
          } ?></td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
<?php endif; ?>
