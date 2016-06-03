<table>
    <thead>
        <tr>
            <th>訪問ユーザ</th>
            <th>ユーザー環境</th>
            <th>アクセス日時</th>
            <th>滞在時間</th>
            <th>チャット</th>
            <th>閲覧ページ数</th>
            <th>参照元URL</th>
        </tr>
    </thead>
    <tbody>
<?php foreach($historyList as $key => $history): ?>
        <tr>
            <td class="tCenter"><?=h($history['THistory']['ip_address'])?></td>
            <td class="tCenter">{{ ua('<?=h($history['THistory']['user_agent'])?>') }}</td>
            <td class="tCenter"><?=h($history['THistory']['access_date'])?></td>
            <td class="tCenter"><?=$this->htmlEx->calcTime($history['THistory']['access_date'], $history['THistory']['out_date']) ?></td>
            <td class="tCenter">
                <?php if( is_numeric($history['THistoryChatLog']['count']) ): ?>
                    <?=h($history['THistoryChatLog']['count'])?>（<a href="javascript:void(0)" onclick="openChatById('<?=h($history['THistory']['id'])?>')" >履歴</a>）
                <?php endif; ?>
            </td>
            <td class="tCenter">
                <?php if( is_numeric($history['THistoryStayLog']['count']) ): ?>
                    <?=h($history['THistoryStayLog']['count'])?>（<a href="javascript:void(0)" onclick="openHistoryById('<?=h($history['THistory']['id'])?>')" >移動履歴</a>）
                <?php endif; ?>
            </td>
            <td class="tCenter omit"><span><?=h($history['THistory']['referrer_url'])?></span></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
