<?php echo $this->element('MUsers/script'); ?>

<div id='muser_idx' class="card-shadow">

<div id='muser_add_title'>
	<div class="fLeft"><?= $this->Html->image('users_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
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

<div id='muser_list' class="p20x">
	<table>
		<thead>
			<tr>
<!--
				<th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck" ><label for="allCheck"></label></th>
 -->
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
			<?php
			$params = $this->Paginator->params();
			$prevCnt = ($params['page'] - 1) * $params['limit'];
			$no = $prevCnt + h($key+1);
			?>
			<tr>
<!--
				<td class="tCenter">
					<input type="checkbox" name="selectTab" id="selectTab<?=$key?>" value="<?=$val['MUser']['id']?>">
					<label for="selectTab<?=$val['MUser']['id']?>"></label>
				</td>
 -->
				<td class="tCenter">
					<a class="t-link" id="t-link" href="javascript:void(0)" onclick="openEditDialog(<?=$val['MUser']['id']?>)">
						<?=$no?>
					</a>
				</td>
				<td class="tCenter">
					<a class="t-link" id="t-link" href="javascript:void(0)" onclick="openEditDialog(<?=$val['MUser']['id']?>)">
						<?=$val['MUser']['user_name']?>
					</a>
				</td>
				<td class="tCenter">
					<a class="t-link" id="t-link" href="javascript:void(0)" onclick="openEditDialog(<?=$val['MUser']['id']?>)">
						<?=$val['MUser']['display_name']?>
					</a>
				</td>
				<td class="tCenter">
					<a class="t-link" id="t-link" href="javascript:void(0)" onclick="openEditDialog(<?=$val['MUser']['id']?>)">
						<?=$authorityList[$val['MUser']['permission_level']]?>
					</a>
				</td>
				<td class="tCenter">
					<a class="t-link" id="t-link" href="javascript:void(0)" onclick="openEditDialog(<?=$val['MUser']['id']?>)">
						<?=$val['MUser']['mail_address']?>
					</a>
				</td>
				<td class="tCenter">
<!--
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
 -->
					<?php
						if ( $userInfo['id'] === $val['MUser']['id'] ) {
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
									'class' => 'grayBtn fRight',
									'escape' => false
								)
							);
						}
						else {
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
						}
					?>
				</td>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

</div>
