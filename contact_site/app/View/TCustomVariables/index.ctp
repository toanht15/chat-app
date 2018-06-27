<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?= $this->element('TCustomVariables/script') ?>

<?php
$params = $this->Paginator->params();
$prevCnt = ($params['page'] - 1) * $params['limit'];
?>
<div id='tcustomvariables_idx' class="card-shadow">
  <?php if(!$coreSettings[C_COMPANY_USE_CUSTOMVARIABLES]): ?>
    <div id="modal" style="display: table; position: absolute; top:15px; left:15px; width: calc(100% - 30px); height: calc(100% - 30px); z-index: 4; background-color: rgba(0, 0, 0, 0.8);">
      <p style="font-size: 15px; color: #FFF; display: table-cell; vertical-align: middle; text-align: center;">こちらの機能はスタンダードプランからご利用いただけます。</p>
    </div>
  <?php endif; ?>
  <div id='tcustomvariables_title'>
    <div class="fLeft"><i class="fal fa-percent fa-2x"></i></div>
    <h1>カスタム変数設定<span id="sortMessage"></span></h1>
  </div>

  <div id='tcustomvariables_description'>
    <span class="pre">お客様のサイト（sincloタグが埋め込まれたページ）内にて、CSSセレクタで指定された項目の値を取得することが可能です。（ECサイトや会員制サイトで会員番号や会員名などを取得可）
    取得した値はオートメッセージの条件で利用したり、リアルタイムモニターや履歴に自動で付与することが可能です。</span>
  </div>

  <div id='tcustomvaliables_menu' style= 'padding-left: 20px;'>
    <ul class="fLeft" >
      <div class="btnSet">
        <span>
          <a>
            <?= $this->Html->image('add.png', array(
                'alt' => '登録',
                'id'=>'tcustomvariables_add_btn',
                'class' => 'btn-shadow disOffgreenBtn commontooltip',
                'data-text' => '新規追加',
                'width' => 45,
                'height' => 45,
            	'onclick' => 'openAddDialog()'
            )) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('copy.png', array(
                'alt' => 'コピー',
                'id'=>'tcustomvariables_copy_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => 'コピー（複製）',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
        <span>
          <a>
            <?= $this->Html->image('dustbox.png', array(
                'alt' => '削除',
                'id'=>'tcustomvariables_dustbox_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => '削除する',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
      </div>
      <!-- カスタム変数の並び替えモード -->
      <div class="tabpointer">
        <label class="pointer">
          <?= $this->Form->checkbox('sort', array('onchange' => 'toggleSort()')); ?><span id="sortText">並び替え</span><span id="sortTextMessage" style="display: none; font-size: 1.1em; color: rgb(192, 0, 0); font-weight: bold; ">（！）並び替え中（保存する場合はチェックを外してください）</span>
        </label>
      </div>
      <!-- カスタム変数の並び替えモード -->
    </ul>
    <div id="paging" class="fRight" style= 'padding-right: 20px;'>
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

  <div id='tcustomvariables_list' class="p20x">
    <table>
      <thead>
      <tr>
        <th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
        <th width=" 5%">No</th>
        <th width="30%" class="tCenter">カスタム変数名</th>
        <th width="30%" class="tCenter">CSSセレクタ</th>
        <th width="30%" class="tCenter">コメント</th>
      </tr>
      </thead>
    <tbody class="sortable">
      <?php $allCondList = []; ?>
      <?php $allActionList = []; ?>
      <?php foreach((array)$tCustomVariableList as $key => $val):?>
      <tr class="pointer" data-id="<?=$val['TCustomVariable']['id']?>" data-sort="<?=$val['TCustomVariable']['sort']?>" onclick="openEditDialog('<?=$val['TCustomVariable']['id']?>')">
        <td class="tCenter" onclick="event.stopPropagation()">
          <input type="checkbox" name="selectTab" id="selectTab<?=$key?>" value="<?=$val['TCustomVariable']['id']?>">
          <label for="selectTab<?=$key?>"></label>
        </td>
        <td width="5%" class="tCenter"><?=$prevCnt + h($key+1)?></td>
        <td width="30%" class="tCenter"><?=$val['TCustomVariable']['variable_name']?></td>
        <td width="30%" class="tCenter"><?=$val['TCustomVariable']['attribute_value']?></td>
        <td width="30%" class="tCenter"><?=$val['TCustomVariable']['comment']?></td>
      </tr>
      <?php endforeach; ?>
      <?php if ( count($tCustomVariableList) === 0 ) :?>
        <tr class="cancel"><td class="tCenter" colspan="5">カスタム変数が設定されていません</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>