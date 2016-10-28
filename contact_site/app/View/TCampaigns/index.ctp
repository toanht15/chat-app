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
      <li>
        <?= $this->Html->image('add.png', array('alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30, 'onclick' => 'openAddDialog()')) ?>
      </li>
    </ul>
  </div>

  <div id='tcampaigns_list' style = 'padding: 5px 20px 20px 20px;'>
    <table>
      <thead>
        <tr>
          <th style="width:30em;">キャンペーン名</th>
          <th style="width:20em;">URLパラメータ</th>
          <th>コメント</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody class="sortable">
      <?php foreach((array)$tCampaignList as $key => $val): ?>
        <tr data-id="<?=$val['TCampaign']['id']?>">
          <td class="tCenter"><?=h($val['TCampaign']['name'])?></td>
          <td class="tCenter"><?=h($val['TCampaign']['parameter'])?></td>
          <td class="tLeft pre"><?=h($val['TCampaign']['comment'])?></td>
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
                'onclick' => 'openEditDialog('.$val['TCampaign']['id'].')',
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
                  'onclick' => 'openConfirmDialog('.$val['TCampaign']['id'].')',
                  'escape' => false
                )
            );
          ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( count($tCampaignList) === 0 ) :?>
        <td class="tCenter" colspan="4">キャンペーンが設定されていません</td>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
