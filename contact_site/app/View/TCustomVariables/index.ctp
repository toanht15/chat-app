<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?= $this->element('TCustomVariables/script') ?>

<div id='tcustomvariables_idx' class="card-shadow">

  <div id='tcustomvariables_title'>
    <div class="fLeft"><?= $this->Html->image('dictionary_g.png', array('alt' => 'カスタム変数', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>カスタム変数<span id="sortMessage"></span></h1>
  </div>

  <div id='tcustomvariables_description'>
    <span class="pre">お客様のサイト（sincloタグが埋め込まれたページ）内にて、id属性またはname属性で指定された項目の値を取得することが可能です。（ECサイトや会員制サイトで会員番号や会員名などを取得可）
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
                'data-balloon-position' => '36',
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
                'data-balloon-position' => '41',
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
                'data-balloon-position' => '35',
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
  </div>

  <div id='tcustomvariables_list' class="p20x">
    <table>
      <thead>
      <tr>
        <th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
        <th width=" 5%">No</th>
        <th class="tCenter">カスタム変数名</th>
        <th width=" 7%">属性種別</th>
        <th class="tCenter">属性値</th>
        <th class="tCenter">コメント</th>
      </tr>
      </thead>
      <tbody class="sortable">
      <tr class="pointer" data-id="" data-sort="" onclick="openEditDialog()">
        <td class="tCenter" onclick="event.stopPropagation();">
          <input type="checkbox" name="selectTab" id="selectTab0" value="1">
          <label for="selectTab0"></label>
        </td>
        <td width="5%" class="tCenter">1</td>
        <td class="tCenter">会員番号</td>
        <td class="tCenter">id</td>
        <td class="tCenter">lblMemberID</td>
        <td class="tCenter">会員番号を表します</td>
      </tr>
      <?php if ( count($settingList) === 0 ) :?>
        <td class="tCenter" colspan="4">チャット通知設定が設定されていません</td>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
