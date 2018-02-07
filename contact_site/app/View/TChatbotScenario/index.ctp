<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TChatbotScenario/script'); ?>
<?php
$params = $this->Paginator->params();
$prevCnt = ($params['page'] - 1) * $params['limit'];
?>
<div id='tchatbotscenario_idx' class="card-shadow">

  <div id='tchatbotscenario_title'>
    <div class="fLeft"><?= $this->Html->image('scenario_setting_g.png', array('alt' => 'シナリオ設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>シナリオ設定</h1>
  </div>

  <div id='tchatbotscenario_menu' class="p20trl">
    <div class="fLeft ctrlBtnArea" >
      <div class="btnSet">
        <span>
          <a>
            <?= $this->Html->image('add.png', array(
                'alt' => '登録',
                'id'=>'tchatbotscenario_add_btn',
                'class' => 'btn-shadow disOffgreenBtn commontooltip',
                'data-text' => '新規追加',
                'data-balloon-position' => '36',
                'width' => 45,
                'height' => 45,
                'onclick' => 'openAdd()',
            )) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('copy.png', array(
                'alt' => 'コピー',
                'id'=>'tchatbotscenario_copy_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => 'コピー（複製）',
                'data-balloon-position' => '41',
                'width' => 45,
                'height' => 45
            )) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('dustbox.png', array(
                'alt' => '削除',
                'id'=>'tchatbotscenario_dustbox_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => '削除する',
                'data-balloon-position' => '35',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
      </div>
      <!-- オートメッセージ設定の並び替えモード -->
      <div class="tabpointer">
        <label class="pointer">
          <?= $this->Form->checkbox('sort', array('onchange' => 'toggleSort()')); ?><span id="sortText"> 並び替え</span><span id="sortTextMessage" style="display: none; font-size: 1.1em; color: rgb(192, 0, 0); font-weight: bold; float: right; position: relative; top: 0px; left: 0px;">（！）並び替え中（保存する場合はチェックを外してください）</span>
        </label>
      </div>
      <!-- オートメッセージ設定の並び替えモード -->
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
      <span style="width: auto!important;padding: 10px 0 0;"> <!--<?php echo $this->Paginator->counter('{:page} / {:pages}'); ?>--> 1 / 1 </span>
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

  <div id='tchatbotscenario_list' class="p20x">
    <table style="table-layout: fixed;">
      <thead>
      <tr>
        <th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
        <th width=" 5%">No</th>
        <th width="45%">名称</th>
        <th width="45%">呼び出し元</th>
      </tr>
      </thead>
<!--
      <tbody>
 -->
      <tbody class="sortable">
      <?php $allCondList = []; ?>
      <?php $allActionList = []; ?>
      <?php foreach((array)$settingList as $key => $val): ?>
        <?php
        $id = "";
        if ($val['TChatbotScenario']['id']) {
          $id = $val['TChatbotScenario']['id'];
        }

        // 呼び出し元情報
        $callerInfo = count($val['TAutoMessage']) > 0 ? implode(', ', $val['TAutoMessage']) : '（未設定）';

        $no = $prevCnt + h($key+1);
        ?>
        <tr class="pointer" data-sort="<?=$val['TChatbotScenario']['sort']?>" data-id="<?=h($id)?>" onclick="openEdit(<?= $id ?>)">
          <td class="tCenter" onclick="event.stopPropagation();" width=" 5%">
            <input type="checkbox" name="selectTab" id="selectTab<?=h($id)?>" value="<?=h($id)?>">
            <label for="selectTab<?=h($id)?>"></label>
          </td>
          <td class="tCenter" width=" 5%"><?=$no?></td>
          <td class="tCenter" width="45%"><?= $val['TChatbotScenario']['name']; ?></td>
          <td class="p10x" width="45%">
            <?= $callerInfo; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( count($settingList) === 0 ) : ?>
        <tr><td colspan="6" class="tCenter" style="letter-spacing: 2px">シナリオ設定がありません</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
