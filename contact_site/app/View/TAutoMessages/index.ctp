<?php echo $this->element('TAutoMessages/script'); ?>

<div id='muser_idx' class="card-shadow">

<div id='muser_add_title'>
	<div class="fLeft"><?= $this->Html->image('auto_message_g.png', array('alt' => 'オートメッセージ設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>オートメッセージ設定</h1>
</div>

<div id='muser_menu' class="p20trl">
	<div class="fLeft" >
		<?= $this->Html->image('add.png', ['url' => ['controller'=>'TAutoMessages', 'action' => 'add'], 'alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30]) ?>
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

<div id='muser_list' class="p20x">
	<table>
		<thead>
			<tr>
				<th>No</th>
				<th>名称</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach((array)$settingList as $key => $val): ?>
			<tr>
				<td class="tCenter"><?=($key + 1)?></td>
				<td class="tCenter"><?=$val['TAutoMessage']['name']?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

</div>
