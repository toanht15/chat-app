<?php echo $this->element('Customers/userAgentCheck') ?>
<?php echo $this->element('Histories/angularjs') ?>

<div id='history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainCtrl">

<div id='history_title'>
	<div class="fLeft"><?= $this->Html->image('monitor_g.png', array('alt' => '履歴一覧', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>履歴一覧</h1>
</div>

<div id='history_menu' class="p20trl">
	<!-- 検索窓 -->
	<div id="paging" class="fRight">
		<?php
			echo $this->Paginator->prev(
				$this->Html->image('paging.png', array('alt' => '前のページへ', 'width'=>25, 'height'=>25)),
				array('escape' => false, 'class' => 'btn-shadow greenBtn tr180'),
				null,
				array('class' => 'grayBtn tr180')
			);
		?>
		<span style="width: auto!important"> <?php echo $this->Paginator->counter('{:page} / {:pages}'); ?> </span>
		<?php
			echo $this->Paginator->next(
				$this->Html->image('paging.png', array('alt' => '次のページへ', 'width'=>25, 'height'=>25)),
				array('escape' => false, 'class' => 'btn-shadow greenBtn'),
				null,
				array('escape' => false, 'class' => 'grayBtn')
			);
		?>
	</div>
</div>

<div id='history_list' class="p20x">
	<table>
		<thead>
			<tr>
				<th>訪問ユーザ</th>
				<th>ユーザー環境</th>
				<th>アクセス日時</th>
				<th>滞在時間</th>
				<th>閲覧ページ数</th>
				<th>参照元URL</th>
				<!-- <th>モニタリング</th> -->
			</tr>
		</thead>
		<tbody>
<?php foreach($historyList as $key => $history): ?>
			<tr>
				<td class="tCenter"><?=h($history['THistory']['ip_address'])?></td>
				<td class="tCenter">{{ ua('<?=h($history['THistory']['user_agent'])?>') }}</td>
				<td class="tCenter"><?=h($history['THistory']['access_date'])?></td>
				<td class="tCenter">{{ calcTime('<?=h($history['THistory']['access_date'])?>', '<?=h($history['THistory']['out_date'])?>') }}</td>
				<td class="tCenter"><?=h($history['THistoryStayLog']['count'])?>（<a href="javascript:void(0)" onclick="openHistoryById('<?=h($history['THistory']['id'])?>')" >移動履歴</a>）</td>
				<td class="tCenter omit"><span><?=h($history['THistory']['referrer_url'])?></span></td>
				<!-- <td class="tCenter"></td> -->
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
	<a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>
</div>

</div>
