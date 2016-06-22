<?php echo $this->element('TAutoMessages/script'); ?>
<?php
$params = $this->Paginator->params();
$prevCnt = ($params['page'] - 1) * $params['limit'];
?>

<div id='tautomessages_idx' class="card-shadow">

<div id='tautomessages_title'>
	<div class="fLeft"><?= $this->Html->image('auto_message_g.png', array('alt' => 'オートメッセージ設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>オートメッセージ設定</h1>
</div>

<div id='tautomessages_menu' class="p20trl">
	<div class="fLeft ctrlBtnArea" >
		<?= $this->Html->image('add.png', ['url' => ['controller'=>'TAutoMessages', 'action' => 'add'], 'alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30]) ?>
		<?= $this->Html->image('check.png', ['url' => 'javascript:void(0)', 'onclick'=>'toActive(true)', 'alt' => '有効にする', 'class' => 'btn-shadow greenBtn actCtrlBtn', 'width' => 30, 'height' => 30]) ?>
		<?= $this->Html->image('inactive.png', ['url' => 'javascript:void(0)', 'onclick'=>'toActive(false)', 'alt' => '無効にする', 'class' => 'btn-shadow redBtn actCtrlBtn', 'width' => 30, 'height' => 30]) ?>
	</div>
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

<div id='tautomessages_list' class="p20x">
	<table>
		<thead>
			<tr>
				<th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
				<th width="10%">No</th>
				<th width="20%">名称</th>
				<th width="25%">条件</th>
				<th width="25%">アクション</th>
				<th width="15%">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php $allCondList = []; ?>
		<?php $allActionList = []; ?>
		<?php foreach((array)$settingList as $key => $val): ?>
			<?php
			$id = "";
			if ($val['TAutoMessage']['id']) {
				$id = $val['TAutoMessage']['id'];
			}
			$class = "";
			if ($val['TAutoMessage']['active_flg']) {
				$class = "bgGrey";
			}
			$activity = "";
			if ($val['TAutoMessage']['activity']) {
				$activity = json_decode($val['TAutoMessage']['activity'],true);
			}
			$activity_detail = "";
			switch($val['TAutoMessage']['action_type']) {
				case C_AUTO_ACTION_TYPE_SENDMESSAGE:
					if ( !empty($activity['message']) ) {
						$allActionList[$id] = [
							'type' => $val['TAutoMessage']['action_type'],
							'detail' => $activity['message']
						];
						$activity_detail = "<span class='actionValueLabel'>メッセージ</span><span class='actionValue'>" . $activity['message'] . "</span>";
					}
					break;
			}
			$conditionType = "";
			if (!empty($activity['conditionType'])) {
				if(!empty($outMessageIfType[$activity['conditionType']])){
					$conditionType = $outMessageIfType[$activity['conditionType']];
				}
			}

			$conditions = "";
			if (!empty($activity['conditions'])) {
				$condList = $this->AutoMessage->setAutoMessage($activity['conditions']);
				$allCondList[$id] = $condList;
				$conditions = implode($condList, ", ");
			}
			$no = $prevCnt + h($key+1);
			?>
			<tr class="<?=$class?>" data-id="<?=h($id)?>">
				<td class="tCenter">
					<input type="checkbox" name="selectTab" id="selectTab<?=h($id)?>" value="<?=h($id)?>">
					<label for="selectTab<?=h($id)?>"></label>
				</td>
				<td class="tCenter"><?=$no?></td>
				<td class="tCenter noClick"><?=$this->Html->link(h($val['TAutoMessage']['name']), ['controller'=>'TAutoMessages', 'action'=>'edit', $id])?></td>
				<td class="targetBalloon">
					<span class="conditionTypeLabel m10b">条件</span><span class="m10b actionValue"><?=h($conditionType)?></span>
					<span class="conditionValueLabel m10b">設定</span><span class="m10b actionValue"><?=$conditions?></span>
				</td>
				<td class="p10x">
					<span class="actionTypeLabel m10b">対象</span><span class="m10b actionValue"><?=h($outMessageActionType[$val['TAutoMessage']['action_type']])?></span>
					<?=$activity_detail?>
				</td>
				<td class="p10x noClick lineCtrl">
					<div>
						<a href="<?=$this->Html->url(['controller'=>'TAutoMessages', 'action'=>'edit', $id])?>" class="btn-shadow greenBtn fLeft"><img src="/img/edit.png" alt="更新" width="30" height="30"></a>
						<?php if ($val['TAutoMessage']['active_flg']) { ?>
							<a href="javascript:void(0)" class="btn-shadow redBtn fLeft" onclick="isActive(true, '<?=$id?>')"><img src="/img/inactive.png" alt="無効" width="30" height="30"></a>
						<?php } else { ?>
							<a href="javascript:void(0)" class="btn-shadow greenBtn fLeft" onclick="isActive(false, '<?=$id?>')"><img src="/img/check.png" alt="有効" width="30" height="30"></a>
						<?php } ?>
						<a href="javascript:void(0)" class="btn-shadow redBtn fRight" onclick="removeAct('<?=$no?>', '<?=$id?>')"><img src="/img/trash.png" alt="削除" width="30" height="30"></a>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php if ( count($settingList) === 0 ) : ?>
			<tr><td colspan="5" class="tCenter" style="letter-spacing: 2px">オートメッセージ設定がありません</td></tr>
		<?php endif; ?>
		</tbody>
	</table>
	<div id="balloons">
		<?php foreach((array)$allCondList as $id => $condList): ?>
		<ul id="balloon_cond_<?=h($id)?>">
			<?php foreach((array)$condList as $val): ?>
				<li><?=h($val)?></li>
			<?php endforeach;?>
		</ul>
		<ul id="balloon_act_<?=h($id)?>">
			<li><?=$this->htmlEx->makeChatView($allActionList[$id]['detail'])?></li>
		</ul>
		<?php endforeach;?>
	</div>
</div>
</div>
