<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('Campaigns/script'); ?>

<div id='campaigns_idx' class="card-shadow">

<div id='campaigns_add_title'>
  <div class="fLeft"><?= $this->Html->image('dictionary_g.png', array('alt' => 'キャンペーン管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>キャンペーン設定<span id="sortMessage"></span></h1>
</div>

<p>サイトアクセス時のURL（ランディングURL）に特定のパラメータを含む場合にリアルタイムモニタ（および履歴）に<br />
任意のキャンペーン名を表示させることができます。どの広告からの流入かを可視化したい場合などに本設定をお使い<br />
ください。</p>

<div id='campaigns_menu' class="p20trl">
  <ul class="fLeft" >
    <li>
      <?= $this->Html->image('add.png', array('alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30, 'onclick' => 'openAddDialog()')) ?>
    </li>

  </ul>
</div>

<div id='campaigns_list' class="p20x">
  <table>
    <thead>
      <tr>
        <th>キャンペーン名</th>
        <th>URLパラメータ</th>
        <th>コメント</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody class="sortable">
    <?php foreach((array)$campaignList as $key => $val): ?>
      <tr data-id="<?=$val['Campaign']['id']?>">
        <td width="8%" class="tCenter"><?=h($val['Campaign']['name'])?></td>
        <td style="width:8em;" class="tCenter"><?=h($val['Campaign']['parameter'])?></td>
        <td class="tLeft pre"><?=h($val['Campaign']['comment'])?></td>
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
              'onclick' => 'openEditDialog('.$val['Campaign']['id'].')',
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
                'onclick' => 'openConfirmDialog('.$val['Campaign']['id'].')',
                'escape' => false
              )
          );
        ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if ( count($campaignList) === 0 ) :?>
      <td class="tCenter" colspan="4">キャンペーンが設定されていません</td>
    <?php endif; ?>
    </tbody>
  </table>
</div>

</div>
