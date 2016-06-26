<table>
    <thead>
        <tr>
            <th width="10%">日時</th>
            <th width="10%">IPアドレス</th>
            <th width="20%">OS<br>ブラウザ</th>
            <th width="25%">参照元URL</th>
            <th width=" 5%">閲覧<br>ページ数</th>
            <th width="10%">滞在時間</th>
            <th width="10%">ステータス</th>
            <th width="10%">担当者</th>
        </tr>
    </thead>
    <tbody ng-cloak>
<?php foreach($historyList as $key => $history): ?>
        <tr>
            <td class="tRight pre"><?=date_format(date_create($history['THistory']['access_date']), "Y/m/d\nH:i:s")?></td>
            <td class="tLeft"><?=h($history['THistory']['ip_address'])?></td>
            <td class="tLeft pre">{{ ua('<?=h($history['THistory']['user_agent'])?>') }}</td>
            <td class="tLeft omit"><span><?=h($history['THistory']['referrer_url'])?></span></td>
            <td class="tCenter">
                <?php if( is_numeric($history['THistoryStayLog']['count']) ): ?>
                    <a class="underL" href="javascript:void(0)" onclick="openHistoryById('<?=h($history['THistory']['id'])?>')" ><?=h($history['THistoryStayLog']['count'])?></a>
                <?php endif; ?>
            </td>
            <td class="tRight"><?=$this->htmlEx->calcTime($history['THistory']['access_date'], $history['THistory']['out_date']) ?></td>
            <td class="tCenter">
        <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
                <?php if( is_numeric($history['THistoryChatLog']['count']) ): ?>
                    <a class="underL" href="javascript:void(0)" onclick="openChatById('<?=h($history['THistory']['id'])?>')" >チャット</a>
                <?php endif; ?>
                <?php if( !is_numeric($history['THistoryChatLog']['count']) ): ?>（未対応）<?php endif; ?>
        <?php endif; ?>
        <?php if (!$coreSettings[C_COMPANY_USE_CHAT]) : ?>
            （未対応）
        <?php endif; ?>
            </td>
            <td class="tCenter pre"><?php if (isset($chatUserList[$history['THistory']['id']])) { echo $chatUserList[$history['THistory']['id']]; } ?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
