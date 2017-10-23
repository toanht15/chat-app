<table>
    <thead>
        <tr>
            <th width=" 9%">日時</th>
            <th width=" 6%" class="noOutCsv">詳細</th>
            <th width="10%">IPアドレス</th>
            <th width="10%">訪問ユーザ</th>
            <th width="10%">プラットフォーム<br>ブラウザ</th>
            <th width=" 7%">キャンペーン</th>
            <th width="10%">参照元URL</th>
            <th width=" 5%">閲覧<br>ページ数</th>
            <th width=" 8%">滞在時間</th>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
            <th width=" 5%">成果</th>
            <th width="10%">チャット</th>
            <th width="10%">担当者</th>
        <?php endif; ?>
        </tr>
    </thead>
    <tbody ng-cloak>
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
        <tr>
            <td class="tRight pre"><?=date_format(date_create($history['THistory']['access_date']), "Y/m/d\nH:i:s")?></td>
            <td class="tCenter"><ng-show-detail data-id="<?=h($history['THistory']['id'])?>"></ng-show-detail></td>
            <td class="tLeft pre">{{ ip('<?=h($history['THistory']['ip_address'])?>') }}</td>
            <td class="tLeft pre">{{ ui('<?=h($history['THistory']['ip_address'])?>', '<?=$visitorsId?>') }}</td>
            <td class="tLeft pre">{{ ua('<?=h($history['THistory']['user_agent'])?>') }}</td>
            <td class="tCenter pre"><?=$campaignParam?></td>
            <td class="tLeft omit"><a href="{{::trimToURL('<?=h($history['THistory']['referrer_url'])?>')}}" target="history">{{::trimToURL("<?=h($history['THistory']['referrer_url'])?>")}}</a></td>
            <td class="tCenter">
                <?php if( is_numeric($stayList[$history['THistory']['id']]['THistoryStayLog']['count']) ): ?>
                    <a class="underL" href="javascript:void(0)" onclick="openHistoryById('<?=h($history['THistory']['id'])?>')" ><?=h($stayList[$history['THistory']['id']]['THistoryStayLog']['count'])?></a>
                <?php endif; ?>
            </td>
            <td class="tRight"><?=$this->htmlEx->calcTime($history['THistory']['access_date'], $history['THistory']['out_date']) ?></td>
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
            <td class="tCenter"><?php
              if ($history['THistoryChatLog']['achievementFlg']){
                echo $achievementType[h($history['THistoryChatLog']['achievementFlg'])];
              }
            ?></td>
            <td class="tCenter">
              <?php if( is_numeric($history['THistoryChatLog']['count']) ): ?>
                  <a class="underL" href="javascript:void(0)" onclick="openChatById('<?=h($history['THistory']['id'])?>')" >履歴<?php if (!empty($history['THistoryChatLog']['type'])) { echo "（".h($history['THistoryChatLog']['type'])."）"; } ?></a>
              <?php endif; ?>
              <?php if( !is_numeric($history['THistoryChatLog']['count']) ): ?>（未対応）<?php endif; ?>
            </td>
            <td class="tCenter pre"><?php if (isset($chatUserList[$history['THistory']['id']])) { echo $chatUserList[$history['THistory']['id']]; } ?></td>
        <?php endif; ?>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
