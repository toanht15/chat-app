<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TChatbotScenario/script'); ?>
<?php
// $params = $this->Paginator->params();
// $prevCnt = ($params['page'] - 1) * $params['limit'];
?>
<div id='tchatbotscenario_idx' class="card-shadow">

  <div id='tchatbotscenario_title'>
    <!-- TODO: チャットボット用のアイコンに差し替える -->
    <div class="fLeft"><?= $this->Html->image('dictionary_g.png', array('alt' => 'シナリオ一覧', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>シナリオ一覧</h1>
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
        if ($val['TAutoMessage']['id']) {
          $id = $val['TAutoMessage']['id'];
        }
        $class = "";
        if ($val['TAutoMessage']['active_flg']) {
          $class = "bgGrey";
        }
        $activity = "";
        if ($val['TAutoMessage']['activity']) {
          $activity = json_decode($val['TAutoMessage']['activity'],true);
        }
        $activity_detail = "";
        switch($val['TAutoMessage']['action_type']) {
          case C_AUTO_ACTION_TYPE_SENDMESSAGE:
            if ( !empty($activity['message']) ) {
              $allActionList[$id] = [
                'type' => $val['TAutoMessage']['action_type'],
                'detail' => $activity['message']
              ];
              $activity_detail = "<span class='actionValueLabel'>メッセージ</span><span class='actionValue'>" . h($activity['message']) . "</span>";
            }
            break;
        }
        $conditionType = "";
        if (!empty($activity['conditionType'])) {
          if(!empty($outMessageIfType[$activity['conditionType']])){
            $conditionType = $outMessageIfType[$activity['conditionType']];
          }
        }

        $conditions = "";
        if (!empty($activity['conditions'])) {
          $condList = $this->AutoMessage->setAutoMessage($activity['conditions']);
          $allCondList[$id] = $condList;
          $conditions = implode($condList, ", ");
        }
        $no = $prevCnt + h($key+1);
        ?>
<!--
        <tr class="<?=$class?>" data-id="<?=h($id)?>" onclick="openEdit(<?= $id ?>)">
 -->
        <tr class="pointer <?=$class?>" data-sort="<?=$val['TAutoMessage']['sort']?>" data-id="<?=h($id)?>" onclick="openEdit(<?= $id ?>)">
          <td class="tCenter" onclick="event.stopPropagation();" width=" 5%">
            <input type="checkbox" name="selectTab" id="selectTab<?=h($id)?>" value="<?=h($id)?>">
            <label for="selectTab<?=h($id)?>"></label>
          </td>
          <td class="tCenter" width=" 5%"><?=$no?></td>
          <td class="tCenter" width="20%"><?= $val['TAutoMessage']['name']; ?></td>
          <td class="targetBalloon" width="29%">
            <span class="conditionTypeLabel m10b">条件</span><span class="m10b actionValue"><?=h($conditionType)?></span>
            <span class="conditionValueLabel m10b">設定</span><span class="m10b actionValue"><?=h($conditions)?></span>
          </td>
          <td class="p10x" width="29%">
            <span class="actionTypeLabel m10b">対象</span><span class="m10b actionValue"><?=h($outMessageActionType[$val['TAutoMessage']['action_type']])?></span>
            <?=$activity_detail?>
          </td>
          <td class="p10x tCenter" style="font-size: 1em; font-weight: bold;" width=" 5%">
            <?php
              if(isset($activity['chatTextarea']) && $activity['chatTextarea'] === 2) {
                echo '<span class="m10b">OFF</span>';
              } else {
                echo '<span class="m10b">ON</span>';
              }
            ?>
          </td>
          <td class="p10x tCenter" style="font-size: 2em;" width=" 4%">
            <?php
            if(isset($activity['cv']) && $activity['cv'] === 1) {
              echo '<span class="m10b"><i class="fa fa-check" aria-hidden="true" style="color:#9BD6D1;"></i></span>';
            } else {
              echo '<span class="m10b"></span>';
            }
            ?>
          </td>
          <td class="p10x tCenter" style="font-size: 2em;" width=" 4%">
            <?php
            if(isset($val['TAutoMessage']['send_mail_flg']) && $val['TAutoMessage']['send_mail_flg']) {
              echo '<span class="m10b"><i class="fa fa-check" aria-hidden="true" style="color:#9BD6D1;"></i></span>';
            } else {
              echo '<span class="m10b"></span>';
            }
            ?>
          </td>
<!--
          <td class="p10x lineCtrl">
            <div>
              <?php if ($val['TAutoMessage']['active_flg']) { ?>
                <a href="javascript:void(0)" class="btn-shadow redBtn fLeft m10r10l" onclick="event.stopPropagation(); isActive(true, '<?=$id?>')"><img src="/img/inactive.png" alt="無効" width="30" height="30"></a>
              <?php } else { ?>
                <a href="javascript:void(0)" class="btn-shadow greenBtn fLeft m10r10l" onclick="event.stopPropagation(); isActive(false, '<?=$id?>')"><img src="/img/check.png" alt="有効" width="30" height="30"></a>
              <?php } ?>
              <a href="javascript:void(0)" class="btn-shadow redBtn fRight m10r" onclick="event.stopPropagation(); removeAct('<?=$no?>', '<?=$id?>')"><img src="/img/trash.png" alt="削除" width="30" height="30"></a>
            </div>
          </td>
 -->
        </tr>
      <?php endforeach; ?>
      <?php if ( count($settingList) === 0 ) : ?>
        <tr><td colspan="6" class="tCenter" style="letter-spacing: 2px">シナリオ設定がありません</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
    <div id="balloons">
      <?php foreach((array)$allCondList as $id => $condList): ?>
        <ul id="balloon_cond_<?=h($id)?>">
          <?php foreach((array)$condList as $val): ?>
            <li><?=h($val)?></li>
          <?php endforeach;?>
        </ul>
        <ul id="balloon_act_<?=h($id)?>">
          <li><?=$this->htmlEx->makeChatView($allActionList[$id]['detail'])?></li>
        </ul>
      <?php endforeach;?>
    </div>
  </div>
</div>
