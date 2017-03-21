<?= $this->element('TDocuments/script'); ?>
<?php
  $params = $this->Paginator->params();
  $prevCnt = ($params['page'] - 1);
?>
<div id='tdocument_idx' class="card-shadow">
  <div id='tdocument_add_title'>
    <div class="fLeft"><?= $this->Html->image('document_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>資料設定<span id="sortMessage"></span></h1>
  </div>

  <div id='tdocument_menu' class="p20trl">
    <div class="fLeft" >
      <?= $this->Html->image('add.png', ['url' => ['controller'=>'TDocuments', 'action' => 'add'], 'alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30]) ?>
    </div>
  </div>

  <div id='tdocument_list' class="p20x">

    <table>
      <thead>
        <tr>
          <th width="5%">No</th>
          <th width="15%">資料</th>
          <th width="25%">資料名</th>
          <th width="40%">概要</th>
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
          <td class="tCenter">
            <?php
            $settings = (!empty($val['TDocument']['settings'])) ? (array)json_decode($val['TDocument']['settings']) : [];
            $rotation = (!empty($settings['rotation'])) ? $settings['rotation'] : 0;
            $matrix = "transform: matrix( 1, 0, 0, 1, 0, 0);";
            switch ((int)$rotation) {
              case 90:
                 $matrix = "transform: matrix( 0, 1, -1, 0, 0, 0);";
                 break;
              case 180:
                 $matrix = "transform: matrix(1, 0, 0, -1, 0, 0);";
                 break;
              case 270:
                 $matrix = "transform: matrix( 0, -1, 1, 0, 0, 0);";
                 break;
            }
            ?>
            <?= $this->Html->image(C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/".C_PREFIX_DOCUMENT.pathinfo(h($val['TDocument']['file_name']), PATHINFO_FILENAME).".jpg", ['style' => $matrix]);?>
          </td>
          <td class="tCenter"><?=h($val['TDocument']['name'])?></td>
          <td class="tCenter"><?=h($val['TDocument']['overview'])?></td>
          <!-- <td class="tCenter"><span><?=implode("</span>、<span>",$val['TDocument']['tag'])?></span></td> -->
          <td class="p10x noClick lineCtrl">
            <div>
              <a href="<?=$this->Html->url(['controller'=>'TDocuments', 'action'=>'edit', $id])?>" class="btn-shadow greenBtn fLeft"><img src="/img/edit.png" alt="更新" width="30" height="30"></a>
              <a href="javascript:void(0)" class="btn-shadow redBtn m10r10l fRight" onclick="removeAct('<?=$id?>')"><img src="/img/trash.png" alt="削除" width="30" height="30"></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if ( count($documentList) === 0 ) :?>
          <td class="tCenter" colspan="5">保存された資料がありません</td>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>