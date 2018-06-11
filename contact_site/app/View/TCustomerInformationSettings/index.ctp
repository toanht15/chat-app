<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?= $this->element('TCustomerInformationSettings/script') ?>

<?php
//DB作成後復元
//$params = $this->Paginator->params();
//$prevCnt = ($params['page'] - 1) * $params['limit'];
?>
<div id='tcustomerinformationsettings_idx' class="card-shadow">

  <div id='tcustomerinformationsettings_title'>
    <div class="fLeft"><?= $this->Html->image('dictionary_g.png', array('alt' => 'カスタム変数', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>訪問ユーザー情報設定<span id="sortMessage"></span></h1>
  </div>

  <div id='tcustomerinformationsettings_description'>
    <span class="pre">訪問ユーザ情報として記録する項目（会社名、氏名、連絡先など）を自由に設定することができます。記録された情報はリアルタイムモニターや履歴から確認可能です。
    また、<a href="/TCustomVariables">カスタム変数の値</a>（会員番号や会員名などページから取得した値）を訪問ユーザ情報として自動登録する設定も当画面から行います。</span>
  </div>

  <div id='tcustomvaliables_menu' style= 'padding-left: 20px;'>
    <ul class="fLeft" >
      <div class="btnSet">
        <span>
          <a>
            <?= $this->Html->image('add.png', array(
                'alt' => '登録',
                'id'=>'tcustomerinformationsettings_add_btn',
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
                'id'=>'tcustomerinformationsettings_copy_btn',
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
                'id'=>'tcustomerinformationsettings_dustbox_btn',
                'class' => 'btn-shadow disOffgrayBtn commontooltip',
                'data-text' => '削除する',
                'data-balloon-position' => '35',
                'width' => 45,
                'height' => 45)) ?>
          </a>
        </span>
      </div>
      <!-- 訪問ユーザー情報の並び替えモード -->
      <div class="tabpointer">
        <label class="pointer">
          <?= $this->Form->checkbox('sort', array('onchange' => 'toggleSort()')); ?><span id="sortText">並び替え</span><span id="sortTextMessage" style="display: none; font-size: 1.1em; color: rgb(192, 0, 0); font-weight: bold; ">（！）並び替え中（保存する場合はチェックを外してください）</span>
        </label>
      </div>
      <!-- 訪問ユーザー情報の並び替えモード -->
    </ul>
    <!--************データベース作成後、paginateを有効にする*************
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
      </div> -->
    </div>

  <div id='tcustomerinformationsettings_list' class="p20x">
    <table>
      <thead>
      <tr>
        <th width=" 5%"><input type="checkbox" name="allCheck" id="allCheck"><label for="allCheck"></label></th>
        <th width=" 5%">No</th>
        <th width =" 25%" class="tCenter">項目名</th>
        <th width =" 10%" class="tCenter">タイプ</th>
        <th width =" 6%" class="tCenter">一覧表示</th>
        <th width =" 6%" class="tCenter">メール掲載</th>
        <th width =" 15%" class="tCenter">カスタム変数</th>
        <th width =" 15%" class="tCenter">コメント</th>
      </tr>
      </thead>
    <tbody class="sortable">
      <!--データを登録しだいコメントアウトタグ削除、下記の仮td群を削除 <?php $allCondList = []; ?>
      <?php $allActionList = []; ?>
      <?php foreach((array)$tCustomVariableList as $key => $val):?>
      <tr class="pointer" data-id="<?=$val['TCustomVariable']['id']?>" data-sort="<?=$val['TCustomVariable']['sort']?>" onclick="openEditDialog('<?=$val['TCustomVariable']['id']?>')">
        <td class="tCenter" onclick="event.stopPropagation()">
          <input type="checkbox" name="selectTab" id="selectTab<?=$key?>" value="<?=$val['TCustomVariable']['id']?>">
          <label for="selectTab<?=$key?>"></label>
        </td>
        <td width="5%" class="tCenter"><?=$prevCnt + h($key+1)?></td>
        <td class="tCenter"><?=$val['TCustomVariable']['variable_name']?></td>
        <td class="tCenter"><?=$val['TCustomVariable']['attribute_value']?></td>
        <td class="tCenter"><?=$val['TCustomVariable']['comment']?></td>
      </tr>
      <?php endforeach; ?>
      <?php if ( count($tCustomVariableList) === 0 ) :?>
        <tr class="cancel"><td class="tCenter" colspan="5">カスタム変数が設定されていません</td></tr>
      <?php endif; ?>-->
      <!-- Editが使用できない為一時的にAddを採用 -->
      <tr class="pointer" onclick="openAddDialog()">
      <!-- この記述が無いとチェックボックスをクリックしてもedit画面が開いてしまう -->
        <td class="tCenter" onclick="event.stopPropagation()">
          <input type="checkbox" name="selectTab" id="selectTab0" value="">
          <label for="selectTab0"></label>
        </td>
        <td class="tCenter">1</td>
        <td class="tCenter">会社名</th>
        <td class="tCenter">テキストボックス</th>
        <td class="tCenter">チェックマーク</th>
        <td class="tCenter">チェックマーク</th>
        <td class="tCenter">カスタム変数が入ります</th>
        <td class="tCenter">会社名</th>
      </tbody>
    </table>
  </div>

</div>