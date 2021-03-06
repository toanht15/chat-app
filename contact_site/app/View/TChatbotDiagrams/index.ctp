<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TChatbotDiagrams/script'); ?>
<?php
  $params = $this->Paginator->params();
  $prevCnt = ($params['page'] - 1) * $params['limit'];
?>
<div id='TChatbotDiagram_idx' class="card-shadow">

  <div id='TChatbotDiagram_title'>
    <div class="fLeft"><i class="fal fa-sitemap fa-rotate-270 fa-2x"></i></div>
    <h1>チャットツリー設定</h1>
  </div>

  <div id='TChatbotDiagram_menu' class="p20trl">
    <div class="fLeft ctrlBtnArea" >
      <div class="btnSet">
        <span>
          <a>
            <?= $this->Html->image('add.png', array(
              'alt' => '登録',
              'id' => 'TChatbotDiagram_add_btn',
              'class' => 'btn-shadow disOffgreenBtn commontooltip',
              'data-text' => '新規追加',
              'data-balloon-position' => '36',
              'width' => 45,
              'height' => 45,
            )) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('copy.png', array(
              'alt' => 'コピー',
              'id' => 'TChatbotDiagram_copy_btn',
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
              'id' => 'TChatbotDiagram_dustbox_btn',
              'class' => 'btn-shadow disOffgrayBtn commontooltip',
              'data-text' => '削除する',
              'data-balloon-position' => '36',
              'width' => 45,
              'height' => 45)) ?>
          </a>
        </span>
      </div>
      <!-- チャットツリー設定の並び替えモード -->
      <div class="tabpointer">
        <label class="pointer">
          <?= $this->Form->checkbox('sort'); ?><span id="sortText"> 並び替え</span><span id="sortTextMessage" style="display: none; font-size: 1.1em; color: rgb(192, 0, 0); font-weight: bold; float: right; position: relative; top: 0px; left: 0px;">（！）並び替え中（保存する場合はチェックを外してください）</span>
        </label>
      </div>
      <!-- チャットツリー設定の並び替えモード -->
    </div>


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

  <div id='TChatbotDiagram_list' class="p20x">
    <table style="table-layout: fixed;">
      <thead>
      <tr>
        <th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
        <th width=" 5%">No</th>
        <th width="25%">名称</th>
        <th width="65%">呼び出し元<div class="questionBalloon"><icon class="questionBtn commontooltip" data-text="トリガー設定やシナリオ設定のアクションから、呼び出し設定を利用できます">?</icon></div></th>
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
        if ($val['TChatbotDiagram']['id']) {
          $id = $val['TChatbotDiagram']['id'];
        }

        // 呼び出し元情報
        $callerAutoMessage = (array_key_exists('TAutoMessage', $val['callerInfo']) && count($val['callerInfo']['TAutoMessage']) > 0) ? implode(', ', $val['callerInfo']['TAutoMessage']) : '';
        $callerScenario = (array_key_exists('TChatbotDiagram', $val['callerInfo']) && count($val['callerInfo']['TChatbotDiagram']) > 0) ? implode(', ', $val['callerInfo']['TChatbotDiagram']) : '';

        $no = $prevCnt + h($key+1);
        ?>
        <tr class="pointer diagram_column" data-sort="<?= $val['TChatbotDiagram']['sort'] ?>" data-id="<?= $id ? h($id) : 0 ?>">
          <td class="tCenter" onclick="event.stopPropagation();" width=" 5%">
            <input type="checkbox" name="selectTab" id="selectTab<?=h($id)?>" value="<?= $id ? h($id) : 0?>">
            <label for="selectTab<?=h($id)?>"></label>
          </td>
          <td class="tCenter" width=" 5%"><?=$no?></td>
          <td class="tCenter scenarioTitle" width="25%"><?= $val['TChatbotDiagram']['name']; ?></td>
          <td class="p10x" width="65%">
            <?php if ($callerAutoMessage === '' && $callerScenario === ''): ?>
              <p>（未設定）</p>
            <?php else: ?>
              <?php if ($callerAutoMessage !== ''): ?>
                <p><span class="callerTypeLabel typeAutoMessage">トリガー</span><span><?= $callerAutoMessage; ?></span></p>
              <?php endif; ?>
              <?php if ($callerScenario !== ''): ?>
                <p><span class="callerTypeLabel typeScenario">シナリオ</span><span><?= $callerScenario; ?></span></p>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( count($settingList) === 0 ) : ?>
        <tr class="cancel"><td colspan="6" class="tCenter" style="letter-spacing: 2px">チャットツリー設定がありません</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
    <div class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span class="detail"></span></li>
        </ul>
      </icon-annotation>
    </div>
  </div>
</div>
