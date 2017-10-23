<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TCampaigns/script'); ?>

<div id='tcampaigns_idx' class="card-shadow">

  <div id='tcampaigns_add_title'>
    <div class="fLeft"><?= $this->Html->image('campaign_g.png', array('alt' => 'キャンペーン管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>キャンペーン設定<span id="sortMessage"></span></h1>
  </div>
  <div id='tcampaigns_description'>
      <span class="pre">サイトアクセス時のURL（ランディングURL）に特定のパラメータを含む場合にリアルタイムモニタ（および履歴）に任意のキャンペーン名を表示させることができます。&#10;どの広告からの流入かを可視化したい場合などに本設定をお使いください。
      </span>
  </div>

  <div id='tcampaigns_menu' style= 'padding-left: 20px;'>
    <ul class="fLeft" >
      <div class="btnSet">
        <span>
          <a>
            <?= $this->Html->image('add.png', array(
                'alt' => '登録',
                'id'=>'tcampaigns_add_btn',
                'class' => 'btn-shadow disOffgreenBtn commontooltip',
                'data-text' => '新規追加',
                'data-balloon-position' => '36',
                'width' => 45,
                'height' => 45,
                'onclick' => 'openAddDialog()',
            )) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('copy.png', array(
                'alt' => 'コピー',
                'id'=>'tcampaigns_copy_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => 'コピー（複製）',
                'data-balloon-position' => '41',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('dustbox.png', array(
                'alt' => '削除',
                'id'=>'tcampaigns_dustbox_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => '削除する',
                'data-balloon-position' => '35',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
      </div>
      <!-- キャンペーン設定の並び替えモード -->
      <div class="tabpointer">
        <label class="pointer">
          <?= $this->Form->checkbox('sort', array('onchange' => 'toggleSort()')); ?><span id="sortText">並び替え</span><span id="sortTextMessage" style="display: none; font-size: 1.1em; color: rgb(192, 0, 0); font-weight: bold; ">（！）並び替え中（保存する場合はチェックを外してください）</span>
        </label>
      </div>
      <!-- キャンペーン設定の並び替えモード -->
    </ul>
  </div>

  <div id='tcampaigns_list' style = 'padding: 5px 20px 20px 20px;'>
    <table>
      <thead>
      <tr>
<!-- UI/UX統合対応start -->
        <th width=" 5%">
          <input type="checkbox" name="allCheck" id="allCheck" >
          <label for="allCheck"></label>
        </th>
<!-- UI/UX統合対応end -->
        <th style="width:30em;">キャンペーン名</th>
        <th style="width:20em;">URLパラメータ</th>
        <th>コメント</th>
<!--
        <th>操作</th>
 -->
      </tr>
      </thead>
      <tbody class="sortable">
      <?php foreach((array)$tCampaignList as $key => $val): ?>
        <tr class="pointer" data-id="<?=$val['TCampaign']['id']?>" data-sort="<?=$val['TCampaign']['sort']?>" onclick="openEditDialog('<?=h($val['TCampaign']['id'])?>')">
<!-- UI/UX統合対応start -->
          <td class="tCenter" onclick="event.stopPropagation();">
            <input type="checkbox" name="selectTab" id="selectTab<?=$key?>" value="<?=$val['TCampaign']['id']?>">
            <label for="selectTab<?=$key?>"></label>
          </td>
<!-- UI/UX統合対応end -->
          <td class="tCenter"><?=h($val['TCampaign']['name'])?></td>
          <td class="tCenter"><?=h($val['TCampaign']['parameter'])?></td>
          <td class="tLeft pre"><?=h($val['TCampaign']['comment'])?></td>
<!--
          <td class="tCenter ctrlBtnArea">
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
                'class' => 'btn-shadow redBtn blockCenter',
                'onclick' => 'event.stopPropagation(); openConfirmDialog('.$val['TCampaign']['id'].')',
                'escape' => false
              )
            );
            ?>
          </td>
 -->
        </tr>
      <?php endforeach; ?>
      <?php if ( count($tCampaignList) === 0 ) :?>
        <td class="tCenter" colspan="4">キャンペーンが設定されていません</td>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
