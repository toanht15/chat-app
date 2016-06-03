<div id="tHeadDiv">
	<table>
		<thead>
			<th class="popupHistoryNo">No</th>
			<th class="popupHistoryUrl">タイトル</th>
			<th class="popupHistoryTimer">滞在時間</th>
		</thead>
	</table>
</div>

<div id="tBodyDiv">
	<table>
		<tbody>
			<?php foreach($THistoryStayLog as $key => $val): ?>
				<tr>
					<td class="popupHistoryNo tRight">
						<span><?=$key + 1?></span>
					</td>
					<td class="tCenter">
						<span class="popupHistoryUrl"><?=$this->Html->link(h($val['THistoryStayLog']['title']), h($val['THistoryStayLog']['url']), array('target' => 'monitor'));?></span>
					</td>
					<td class="popupHistoryTimer tCenter">
						<span><?=h($val['THistoryStayLog']['stay_time'])?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
