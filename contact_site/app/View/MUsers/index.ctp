<?php echo $this->element('MUsers/script'); ?>

<div id='muser_idx' class="card-shadow">

<div id='muser_add_title'>
	<div class="fLeft"><?= $this->Html->image('setting_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>ユーザー管理<span>（未使用アカウント数：<?=$limitUserNum - $userListCnt?>）</span></h1>
</div>

<div id='muser_menu' class="p20trl">
<?php if( $limitUserNum > $userListCnt ): ?>
	<div class="fLeft" >
		<?= $this->Html->image('add.png', array('alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30, 'onclick' => 'openAddDialog()')) ?>
	</div>
<?php endif;?>
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
				<th>氏名</th>
				<th>表示名</th>
				<th>権限</th>
				<th>メールアドレス</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach((array)$userList as $key => $val): ?>
			<tr>
				<td class="tCenter"><?=($key + 1)?></td>
				<td class="tCenter"><?=$val['MUser']['user_name']?></td>
				<td class="tCenter"><?=$val['MUser']['display_name']?></td>
				<td class="tCenter"><?=$authorityList[$val['MUser']['permission_level']]?></td>
				<td class="tCenter"><?=$val['MUser']['mail_address']?></td>
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
								'onclick' => 'openEditDialog('.$val['MUser']['id'].')',
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
									'onclick' => 'openConfirmDialog('.$val['MUser']['id'].')',
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
