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
				<th width="30%">条件</th>
				<th width="30%">アクション</th>
			</tr>
		</thead>
		<tbody>
		<?php $allCondList = []; ?>
		<?php $allActionList = []; ?>
		<?php foreach((array)$settingList as $key => $val): ?>
			<?php
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
						$allActionList[$val['TAutoMessage']['id']] = [
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
				$allCondList[$val['TAutoMessage']['id']] = $condList;
				$conditions = implode($condList, ", ");
			}
			?>
			<tr class="<?=$class?>" data-id="<?=h($val['TAutoMessage']['id'])?>">
				<td class="tCenter noClick">
					<input type="checkbox" name="selectTab" id="selectTab<?=h($val['TAutoMessage']['id'])?>" value="<?=h($val['TAutoMessage']['id'])?>">
					<label for="selectTab<?=h($val['TAutoMessage']['id'])?>"></label>
				</td>
				<td class="tCenter"><?=$prevCnt + h($key+1)?></td>
				<td class="tCenter"><?=$this->Html->link(h($val['TAutoMessage']['name']), ['controller'=>'TAutoMessages', 'action'=>'edit', $val['TAutoMessage']['id']])?></td>
				<td class="targetBalloon">
					<span class="conditionTypeLabel m10b">条件</span><span class="m10b actionValue"><?=h($conditionType)?></span>
					<span class="conditionValueLabel m10b">設定</span><span class="m10b actionValue"><?=$conditions?></span>
				</td>
				<td class="p10x">
					<span class="actionTypeLabel m10b">対象</span><span class="m10b actionValue"><?=h($outMessageActionType[$val['TAutoMessage']['action_type']])?></span>
					<?=$activity_detail?>
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
