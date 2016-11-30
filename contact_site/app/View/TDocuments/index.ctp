<?= $this->element('TDocuments/script'); ?>
<?php
  $params = $this->Paginator->params();
  $prevCnt = ($params['page'] - 1);
?>
<div id='tdocument_idx' class="card-shadow">
  <div id='tdocument_add_title'>
    <div class="fLeft"><?= $this->Html->image('users_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>資料設定<span id="sortMessage"></span></h1>
  </div>

  <div id='tdocument_menu' class="p20trl">
    <div class="fLeft" >
      <?= $this->Html->image('add.png', array('alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30, 'onclick' => "location.href = 'http://sinclo.localhost/TDocuments/add';")) ?>
    </div>
  </div>

  <div id='tdocument_list' class="p20x">

    <table>
      <thead>
        <tr>
          <th width="10%">No</th>
          <th width="20%">資料名</th>
          <th width="30%">概要</th>
          <th width="25%">タグ</th>
          <th width="15%">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php
          foreach((array)$documentList as $key => $val):
          $id = "";
          if ($val['TDocument']['id']) {
            $id = $val['TDocument']['id'];
          }
          $no = $prevCnt + h($key+1);
        ?>
        <tr data-id="<?=h($id)?>">
          <td class="tCenter"><?=$no?></td>
          <td class="tCenter"><?=h($val['TDocument']['name'])?></td>
          <td class="tCenter"><?=h($val['TDocument']['overview'])?></td>
          <td class="tCenter"><span><?=implode("</span>、<span>",$val['TDocument']['tag'])?></span></td>
          <td class="p10x noClick lineCtrl">
            <div>
              <a href="<?=$this->Html->url(['controller'=>'TDocuments', 'action'=>'edit', $id])?>" class="btn-shadow greenBtn fLeft"><img src="/img/edit.png" alt="更新" width="30" height="30"></a>
              <a href="javascript:void(0)" class="btn-shadow redBtn m10r10l fRight" onclick="removeAct('<?=$id?>')"><img src="/img/trash.png" alt="削除" width="30" height="30"></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>