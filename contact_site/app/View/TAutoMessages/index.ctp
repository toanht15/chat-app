<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TAutoMessages/script'); ?>
<?php
$params = $this->Paginator->params();
$prevCnt = ($params['page'] - 1) * $params['limit'];
?>
<div id='tautomessages_idx' class="card-shadow">

  <div id='tautomessages_title'>
    <div class="fLeft"><?= $this->Html->image('auto_message_g.png', array('alt' => 'オートメッセージ設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>オートメッセージ設定</h1>
  </div>

  <div id='tautomessages_menu' class="p20trl">
    <div class="fLeft ctrlBtnArea" >
      <div class="btnSet">
        <span>
          <a>
            <?= $this->Html->image('add.png', array(
                'alt' => '登録',
                'id'=>'tautomessages_add_btn',
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
                'id'=>'tautomessages_copy_btn',
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
            <?= $this->Html->image('check.png', array(
                'alt' => '有効',
                'id'=>'tautomessages_check_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => '有効にする',
                'data-balloon-position' => '38',
                'width' => 45,
                'height' => 45,
                'onclick'=>'toActive(true)'
            )) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('inactive.png', array(
                'alt' => '無効',
                'id'=>'tautomessages_inactive_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => '無効にする',
                'data-balloon-position' => '38',
                'width' => 45,
                'height' => 45,
                'onclick'=>'toActive(false)'
            )) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('dustbox.png', array(
                'alt' => '削除',
                'id'=>'tautomessages_dustbox_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => '削除する',
                'data-balloon-position' => '36',
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
    <div id="rightContentWrap" class="fRight">
      <div id="importBtnAreaWrap">
        <div id="importBtnArea">
        <?= $this->Html->link(
          '発言内容をエクセルで編集する',
          'javascript:void(0)',
          array('escape' => false,
            'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE] ? " skyBlueBtn  commontooltip" : " grayBtn disabled commontooltip"),
            'id' => 'importExcelBtn',
            'disabled' => !$coreSettings[C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE],
            'data-text' => $coreSettings[C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE] ? "発言内容の設定をエクセルファイルにて編集しインポートすることが可能です。<br>インポートしたデータは現在の設定に追加されます。<br>（上書きや洗い替えではないため、現在登録されている設定は残ります。）" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
            'data-balloon-position' => '50',
            'data-balloon-width' => $coreSettings[C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE] ? '320' : ''
          ));
        ?>
        </div>
      </div>
      <div id="paging">
        <?php
        echo $this->Paginator->prev(
          $this->Html->image('paging.png', array('alt' => '前のページへ', 'width' => 25, 'height' => 25)),
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
  </div>
  <?php if(isset($coreSettings[C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE]) && $coreSettings[C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE]): ?>
  <div id="autoMessageLayerMenu">
    <ul>
      <li class="t-link">
        <a href="javascript:void(0)" onclick="openSelectFile()">
          編集したファイルをインポートする
        </a>
      </li>
      <li class="t-link">
        <a href="javascript:void(0)" onclick="window.open('<?= $this->Html->url(['controller' => 'TAutoMessages', 'action' => 'downloadTemplate']) ?>')">
          テンプレートをダウンロードする
        </a>
      </li>
    </ul>
  </div>
  <?php endif; ?>
  <input type="file" id="selectFileInput" name="uploadFile" accept=".xlsm" style="display:none "/>

  <div id='tautomessages_list' class="p20x">
    <table style="table-layout: fixed;">
      <thead>
      <tr>
        <th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
        <th width=" 5%">No</th>
        <th width="20%">名称</th>
        <th width="28%">条件</th>
        <th width="29%">アクション</th>
        <th width=" 5%">自由入力<br>エリア</th>
        <th width=" 4%">CV</th>
        <th width=" 4%">メール<br>送信</th>
<!--
        <th width="15%">操作</th>
 -->
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
        <tr><td colspan="6" class="tCenter" style="letter-spacing: 2px">オートメッセージ設定がありません</td></tr>
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
