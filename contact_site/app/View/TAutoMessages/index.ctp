<?php echo $this->element('TAutoMessages/script'); ?>

<div id='tautomessages_idx' class="card-shadow">

<div id='tautomessages_title'>
	<div class="fLeft"><?= $this->Html->image('auto_message_g.png', array('alt' => 'オートメッセージ設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>オートメッセージ設定</h1>
</div>

<div id='tautomessages_menu' class="p20trl">
	<div class="fLeft ctrlBtnArea" >
		<?= $this->Html->image('add.png', ['url' => ['controller'=>'TAutoMessages', 'action' => 'add'], 'alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30]) ?>
		<?= $this->Html->image('check.png', ['url' => 'javascript:void(0)', 'onclick'=>'toActive(true)', 'alt' => '有効にする', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30]) ?>
		<?= $this->Html->image('inactive.png', ['url' => 'javascript:void(0)', 'onclick'=>'toActive(false)', 'alt' => '無効にする', 'class' => 'btn-shadow redBtn', 'width' => 30, 'height' => 30]) ?>
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
				<th width="10%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
				<th width="40%">No</th>
				<th width="50%">名称</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach((array)$settingList as $key => $val): ?>
			<?php
			$class = "";
			if ($val['TAutoMessage']['active_flg']) {
				$class = "bgGrey";
			}
			 ?>
			<tr class="<?=$class?>">
				<td class="tCenter">
					<input type="checkbox" name="selectTab" id="selectTab<?=h($val['TAutoMessage']['id'])?>" value="<?=h($val['TAutoMessage']['id'])?>">
					<label for="selectTab<?=h($val['TAutoMessage']['id'])?>"></label>
				</td>
				<td class="tCenter" onclick="location.href='/TAutoMessages/edit/<?=h($val['TAutoMessage']['id'])?>';return false;"><?=h($val['TAutoMessage']['id'])?></td>
				<td class="tCenter" onclick="location.href='/TAutoMessages/edit/<?=h($val['TAutoMessage']['id'])?>';return false;"><?=$this->Html->link(h($val['TAutoMessage']['name']), ['controller'=>'TAutoMessages', 'action'=>'edit', $val['TAutoMessage']['id']])?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

</div>
