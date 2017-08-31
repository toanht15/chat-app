<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TDictionaries/script'); ?>

<input type="hidden" id="stint_flg" value="<?= $stint_flg ?>">
<div id='tdictionaries_idx' class="card-shadow">

<div id='tdictionaries_add_title'>
  <div class="fLeft"><?= $this->Html->image('dictionary_g.png', array('alt' => '定型文メッセージ管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
  <h1>定型文管理</h1>
</div>
<!-- #451 定型文カテゴリ対応 start -->
<!-- カテゴリ名入力登録 start -->

<section class="texboxArea">
    <ul>
    <li>
    <div>
    <p>
      <span><input type="text" class="" size="35" id="input_category_value" placeholder="追加するカテゴリ名を入力してください" onKeyUp="inputValue(this)"></span>
      <span>
        <input type="button" class="disOffgrayBtn btn-shadow" id="input_category_btn" value="カテゴリを追加" disabled="disabled" onClick="saveCategoryAddDialog()">
      </span>
    </p>
    </div>
    </li>
    </ul>
    <ul>
      <!-- カテゴリの並び替えモード -->
        <li>
          <label class="pointer">
            <?= $this->Form->checkbox('tabsort', array('onchange' => 'tabSort()')) ?>カテゴリの並び替え
            <!--
            <span id="stintMessage" style="display:none; color:#E91E63; font-weight:normal; font-size:0.9em;">　※カテゴリ登録およびＣＳＶ関連はスタンダードプランからご利用いただけます。</span>
             -->
            <span id="stintMessage" style="display:none; color:#E91E63; font-weight:normal; font-size:0.9em;">　※カテゴリ登録はスタンダードプランからご利用いただけます。</span>
          </label>
          <span id="tabSortMessage" style="display:none; color:#E91E63; font-weight:normal; font-size:0.9em;">　（！）カテゴリを並び替え中（保存する場合は画面下部の「保存」ボタンをクリック／キャンセルする場合はチェックを外してください）</span>
        </li>
      <!-- カテゴリの並び替えモード -->
    </ul>
</section>
<section class="listArea">
<div id="soteTabs" class="soteTabs">
  <input type="hidden" id="select_tab_index" value="">
  <input type="hidden" id="select_soto" value="">
  <ul class="tablist">
  <?php for ($i = 0; $i < count((array)$nameList); $i++) { ?>
    <li><a onfocus="this.blur();" data-id="<?=$nameList[$i]['id']?>" href="#tabs-<?=$i?>"><?=h($nameList[$i]['name'])?></a></li>
  <?php } ?>
  </ul>
  <?php for ($i = 0; $i < count((array)$nameList); $i++) { ?>
  <div id="tabs-<?=$i?>" class="tabBox">
      <div id='tdictionaries_menu' class="p20trl">
        <ul class="fLeft" >
          <li>
            <?php $tab_id = $nameList[$i]['id']; ?>
            <span>
              <a>
                <?= $this->Html->image('add.png', array(
                    'alt' => '登録',
                    'id'=>'tdictionaries_add_btn'."$i",
                    'class' => 'btn-shadow disOffgreenBtn commontooltip',
                    'data-text' => '新規追加',
                    'data-balloon-position' => '36',
                    'width' => 40,
                    'height' => 40,
                    'onclick' => 'openAddDialog('.$tab_id.')',
                )) ?>
              </a>
            </span>
            <span>
              <a>
                <?= $this->Html->image('copy.png', array(
                    'alt' => 'コピー',
                    'id'=>'tdictionaries_copy_btn'."$i",
                    'class' => 'btn-shadow disOffgrayBtn commontooltip',
                    'data-text' => 'コピー（複製）',
                    'data-balloon-position' => '41',
                    'width' => 40,
                    'height' => 40)) ?>
              </a>
            </span>
            <span>
              <?php if($stint_flg){?>
              <a>
                <?= $this->Html->image('move.png', array(
                    'alt' => '移動',
                    'id'=>'tdictionaries_move_btn'."$i",
                    'class' => 'btn-shadow disOffgrayBtn commontooltip',
                    'data-text' => '移動する',
                    'data-balloon-position' => '36',
                    'width' => 40,
                    'height' => 40)) ?>
              </a>
              <?php }else{?>
              <a>
                <?= $this->Html->image('move.png', array(
                    'alt' => '移動',
                    'id'=>'tdictionaries_no_move_btn'."$i",
                    'class' => 'btn-shadow disOffgrayBtn commontooltip',
                    'data-text' => "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
                    'data-balloon-position' => '43.5',
                    'width' => 40,
                    'height' => 40)) ?>
              </a>
              <?php }?>
            </span>
            <span>
              <a>
                <?= $this->Html->image('dustbox.png', array(
                    'alt' => '削除',
                    'id'=>'tdictionaries_dustbox_btn'."$i",
                    'class' => 'btn-shadow disOffgrayBtn commontooltip',
                    'data-text' => '削除する',
                    'data-balloon-position' => '35',
                    'width' => 40,
                    'height' => 40)) ?>
              </a>
            </span>
          </li>
          <!-- 定型文の並び替えモード -->
          <li>
            <label class="pointer" for="sort">
              <?= $this->Form->checkbox('sort'.$i, array('onchange' => 'toggleSort()')) ?>定型文の並び替え
            </label>
            <span id="sortMessage<?=$i?>" style="display:none; color:#E91E63; font-weight:normal; font-size:0.9em;">　（！）定型文を並び替え中（保存する場合は画面下部の「保存」ボタンをクリック／キャンセルする場合はチェックを外してください）</span>
          </li>
          <!-- 定型文の並び替えモード -->
        </ul>
        <ul class="fRight">
          <li>
            <?php if($stint_flg){?>
              <div id="menuheader">
                <p id="openMenu<?=$i?>">
                    <?= $this->Html->image('list.png', array(
                        'alt' => 'メニュー',
                        'id'=>'tdictionaries_manu_btn'."$i",
                        'class' => 'btn-shadow disOffgreenBtn commontooltip',
                        'data-text' => 'その他編集',
                        'data-balloon-position' => '84',
                        'width' => 32,
                        'height' => 32)) ?>
                </p>
              </div>
            <?php }else{?>
              <div id="menuheader">
                <p id="openMenu<?=$i?>">
                    <?= $this->Html->image('list.png', array(
                        'alt' => 'メニュー',
                        'id'=>'tdictionaries_manu_btn'."$i",
                        'class' => 'btn-shadow disOffgrayBtn commontooltip',
                        'data-text' => "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
                        'data-balloon-position' => '84',
                        'width' => 32,
                        'height' => 32)) ?>
                </p>
              </div>
            <?php }?>
            <div id="layerMenu<?=$i?>">
             <ul>
                <li class="t-link">
                  <a href="javascript:void(0)" onclick="openCategoryEditDialog(<?=$nameList[$i]['id']?>)">
                    カテゴリ名を変更する
                  </a>
                </li>
                <li class="t-link">
                  <a href="javascript:void(0)" onclick="openCategoryDeleteDialog(<?=$nameList[$i]['id']?>)">
                    カテゴリを削除する
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
      <div id='tdictionaries_list' class="p20x">
        <table>
          <thead>
            <tr>
              <!-- #451 定型文カテゴリ対応 start -->
              <th width=" 5%">
                <input type="checkbox" name="allCheck" id="allCheck<?=$i?>" >
                <label for="allCheck<?=$i?>"></label>
              </th>
              <!-- #451 定型文カテゴリ対応 end -->
              <th>No</th>
              <th>使用範囲</th>
              <th>ワード</th>
            </tr>
          </thead>
          <tbody class="sortable">
            <?php if (!empty($dictionaryList[$i])) {?>
              <?php foreach((array)$dictionaryList[$i]as $key => $val): ?>
              <tr data-id="<?=$val['TDictionary']['id']?>" data-sort="<?=$val['TDictionary']['sort']?>">
                <!-- #451 定型文カテゴリ対応 start -->
                <td class="tCenter">
                  <input type="hidden" id="dictionary_list_flg<?=$i?>" value="1">
                  <input type="checkbox" name="selectTab<?=$i.'-'.$key?>" id="selectTab<?=$i.'-'.$key?>" value="<?=$val['TDictionary']['id']?>">
                  <label for="selectTab<?=$i.'-'.$key?>"></label>
                </td>
                <!-- #451 定型文カテゴリ対応 end -->
                <td width="8%" class="tCenter pre">
                  <a class="t-link" id="t-link" href="javascript:void(0)" onclick="openEditDialog(<?=$val['TDictionary']['id']?>,<?=$nameList[$i]['id']?>)">
                    <?=$key+1?>
                  </a>
                </td>
                <td style="width:8em;" class="tCenter pre">
                    <a class="t-link" id="t-link" href="javascript:void(0)" onclick="openEditDialog(<?=$val['TDictionary']['id']?>,<?=$nameList[$i]['id']?>)">
                      <?=$dictionaryTypeList[$val['TDictionary']['type']]?>
                    </a>
                </td>
                <td class="tLeft pre">
                    <a class="t-link" id="t-link" href="javascript:void(0)" onclick="openEditDialog(<?=$val['TDictionary']['id']?>,<?=$nameList[$i]['id']?>)">
                      <?=h($val['TDictionary']['word'])?>
                    </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php } else {?>
            <td class="tCenter" colspan="4">
              <input type="hidden" id="dictionary_list_flg<?=$i?>" value="0">
              定型文メッセージが設定されていません
            </td>
          <?php } ?>
          </tbody>
        </table>
      </div>
  </div>
  <?php } ?>
</div>
</section>
<!-- カテゴリの並べ替え更新ボタン -->
<section id="tabsort_btn" style="display:none;">
    <div id="m_widget_setting_action" class="fotterBtnArea" style="bottom: 0px; right: 17px; left: 60px;">
    <a href="javascript:void(0)" onclick="saveTabSort()" class="greenBtn btn-shadow">更新</a>
    </div>
</section>

<!-- 定型文の並べ替え更新ボタン -->
<section id="tdictionaries_sort_btn" style="display:none;">
    <div id="m_widget_setting_action" class="fotterBtnArea" style="bottom: 0px; right: 17px; left: 60px;">
    <a href="javascript:void(0)" onclick="saveToggleSort()" class="greenBtn btn-shadow">更新</a>
    </div>
</section>
<!-- カテゴリ名入力登録 end -->
<!-- #451 定型文カテゴリ対応 end -->


</div>
