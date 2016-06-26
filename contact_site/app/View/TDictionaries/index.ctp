<?php echo $this->element('TDictionaries/script'); ?>

<div id='tdictionaries_idx' class="card-shadow">

<div id='tdictionaries_add_title'>
	<div class="fLeft"><?= $this->Html->image('dictionary_g.png', array('alt' => '簡易入力メッセージ管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>簡易入力メッセージ管理</h1>
</div>

<div id='tdictionaries_menu' class="p20trl">
	<div class="fLeft" >
		<?= $this->Html->image('add.png', array('alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30, 'onclick' => 'openAddDialog()')) ?>
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
		<span style="width: auto!important;padding: 10px 0 0;"> <?php echo $this->Paginator->counter('{:page} / {:pages}'); ?> </span>
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

<div id='tdictionaries_list' class="p20x">
	<table>
		<thead>
			<tr>
				<th width="5%">No</th>
				<th width="15%">種類</th>
				<th width="70%">ワード</th>
				<th width="10%">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach((array)$dictionaryList as $key => $val): ?>
			<?php
			$params = $this->Paginator->params();
			$prevCnt = ($params['page'] - 1) * $params['limit'];
			$no = $prevCnt + h($key+1);

			$kind = "企業";
			if ( strcmp(C_DICTIONARY_TYPE_COMP, $val['TDictionary']['type']) === 0 ) {
				$kind = "個人";
			}
			?>
			<tr>
				<td class="tCenter"><?=$no?></td>
				<td class="tCenter"><?=$dictionaryTypeList[$val['TDictionary']['type']]?></td>
				<td class="tCenter"><?=$val['TDictionary']['word']?></td>
				<td class="tCenter">
					<?php
						echo $this->Html->link(
							$this->Html->image(
								'edit.png',
								array(
									'alt' => '更新',
									'width' => 30,
									'height' => 30,
								)
							),
							'javascript:void(0)',
							array(
								'class' => 'btn-shadow greenBtn fLeft',
								'onclick' => 'openEditDialog('.$val['TDictionary']['id'].')',
								'escape' => false
							)
						);
					?>
					<?php
						echo $this->Html->link(
								$this->Html->image(
									'trash.png',
									array(
										'alt' => '削除',
										'width' => 30,
										'height' => 30
									)
								),
								'javascript:void(0)',
								array(
									'class' => 'btn-shadow redBtn fRight',
									'onclick' => 'openConfirmDialog('.$val['TDictionary']['id'].')',
									'escape' => false
								)
						);
					?>

				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

</div>
