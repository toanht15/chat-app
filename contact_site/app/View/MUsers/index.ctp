<?php echo $this->element('MUsers/script'); ?>

<div id='muser_idx' class="card-shadow">

<div id='muser_add_title'>
	<div class="fLeft"><?= $this->Html->image('setting_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
	<h1>ユーザー管理</h1>
</div>

<div id='muser_menu' class="p20tl">
<?= $this->Html->image('add.png', array('alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30, 'onclick' => 'openAddDialog()')) ?>
</div>

<div id='muser_list' class="p20x">
	<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>氏名</th>
				<th>表示名</th>
				<th>権限</th>
				<th>メールアドレス</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($userList as $val): ?>
			<tr>
				<td class="tCenter"><?=$val['MUser']['id']?></td>
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
									'onclick' => 'openEditDialog('.$val['MUser']['id'].')',
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