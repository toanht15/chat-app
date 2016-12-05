<?= $this->element('TDictionaries/script'); ?>

<?php
  $params = $this->Paginator->params();
  $prevCnt = ($params['page'] - 1);
?>

<div id='tdictionaries_idx'>
  <div id='tdictionaries_add_title'>
    <div class="fLeft"><i class="fa fa-home fa-2x" aria-hidden="true"></i></div>
    <h1>簡易入力メッセージ設定<span id="sortMessage"></span></h1>
  </div>

  <div id='tdictionaries_menu' class="p20trl">
    <ul class="fLeft" >
      <li>
        <?= $this->Html->link('登録','javascript:void(0)',['escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'openConfirm()']);?>
      </li>
      <!-- 並び替えモード -->
      <li>
        <?= $this->Form->checkbox('sort', ['onchange' => 'toggleSort()']) ?>
        <label for="sort">並び替え</label>
      </li>
      <!-- 並び替えモード -->
    </ul>
  </div>

  <div id='tdictionaries_list' class="p20x">
    <table>
      <thead>
        <tr>
          <th style="width:5em;" class='tCenter'>No</th>
          <th style="width:30em;">ワード</th>
          <th width="1%"></th>
        </tr>
      </thead>
      <tbody class="sortable">
        <?php foreach((array)$dictionaryList as $key => $val): ?>
          <?php
            $id = "";
            if ($val['TDictionary']['id']) {
              $id = $val['TDictionary']['id'];
            }
            $no = $prevCnt + h($key+1);
          ?>
          <tr data-id="<?=$val['TDictionary']['id']?>" ondblclick = <?= 'openEditDialog('.$val['TDictionary']['id'].')'?>>
            <td class="tCenter"><?=$no?></td>
            <td><?=h($val['TDictionary']['word'])?></td>
            <td>
            <i class="fa fa-times fa-2x" aria-hidden="true" a href="javascript:void(0)" id="delete" onclick="removeAct('<?=$id?>')"></i>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

