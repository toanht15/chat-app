<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TDictionaries/script'); ?>

<div id='tdictionaries_idx' class="card-shadow">

<div id='tdictionaries_add_title'>
  <div class="fLeft"><?= $this->Html->image('dictionary_g.png', array('alt' => '簡易入力メッセージ管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>簡易入力メッセージ管理<span id="sortMessage"></span></h1>
</div>

<div id='tdictionaries_menu' class="p20trl">
  <ul class="fLeft" >
    <li>
      <?= $this->Html->image('add.png', array('alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30, 'onclick' => 'openAddDialog()')) ?>
    </li>
    <!-- 並び替えモード -->
    <li>
      <label class="pointer" for="sort">
        <?= $this->Form->checkbox('sort', array('onchange' => 'toggleSort()')) ?>並び替え
      </label>
    </li>
    <!-- 並び替えモード -->
  </ul>
</div>

<div id='tdictionaries_list' class="p20x">
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>使用範囲</th>
        <th>ワード</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody class="sortable">
    <?php foreach((array)$dictionaryList as $key => $val): ?>
      <tr data-id="<?=$val['TDictionary']['id']?>" data-sort="<?=$val['TDictionary']['sort']?>">
        <td width="8%" class="tCenter"><?=$key+1?></td>
        <td style="width:8em;" class="tCenter"><?=$dictionaryTypeList[$val['TDictionary']['type']]?></td>
        <td class="tLeft pre"><?=h($val['TDictionary']['word'])?></td>
        <td class="tCenter ctrlBtnArea">
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
    <?php if ( count($dictionaryList) === 0 ) :?>
      <td class="tCenter" colspan="4">簡易入力メッセージが設定されていません</td>
    <?php endif; ?>
    </tbody>
  </table>
</div>

</div>
