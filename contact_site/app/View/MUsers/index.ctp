<?php echo $this->element('MUsers/script'); ?>

<div id='muser_idx' class="card-shadow">

  <div id='muser_add_title'>
    <div class="fLeft"><?= $this->Html->image('users_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>ユーザー管理<span>（未使用アカウント数：<?=$limitUserNum - $userListCnt?>）</span></h1>
  </div>

  <div id='muser_menu' class="p20trl">
      <div class="fLeft" >
        <div class="btnSet" >
          <?php if( $limitUserNum > $userListCnt ): ?>
          <span>
            <a>
              <?= $this->Html->image('add.png', array(
                  'alt' => '登録',
                  'id'=>'m_users_add_btn',
                  'class' => 'btn-shadow disOffgreenBtn commontooltip',
                  'data-text' => '新規追加',
                  'data-balloon-position' => '36',
                  'width' => 45,
                  'height' => 45,
                  'onclick' => 'openAddDialog()',
              )) ?>
            </a>
          </span>
          <?php endif;?>
          <span>
            <a>
              <?= $this->Html->image('dustbox.png', array(
                  'alt' => '削除',
                  'id'=>'m_users_dustbox_btn',
                  'class' => 'btn-shadow disOffgrayBtn commontooltip',
                  'data-text' => '削除する',
                  'data-balloon-position' => '35',
                  'width' => 45,
                  'height' => 45)) ?>
            </a>
          </span>
        </div>
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

  <div id='muser_list' class="p20x">
    <table>
      <thead>
      <tr>
<!-- UI/UX統合対応start -->
        <th width=" 5%">
          <input type="checkbox" name="allCheck" id="allCheck" >
          <label for="allCheck"></label>
        </th>
<!-- UI/UX統合対応end -->
        <th width=" 5%">No</th>
        <th width=" 15%">氏名</th>
        <th width=" 20%">表示名</th>
        <th width=" 10%">権限</th>
        <th>メールアドレス</th>
<!--
        <th>操作</th>
 -->
      </tr>
      </thead>
      <tbody>
      <?php foreach((array)$userList as $key => $val): ?>
        <?php
        $params = $this->Paginator->params();
        $prevCnt = ($params['page'] - 1) * $params['limit'];
        $no = $prevCnt + h($key+1);
        ?>
        <tr class="pointer" onclick="<?="openEditDialog('".h($val['MUser']['id'])."')"?>">
<!-- UI/UX統合対応start -->
          <td class="tCenter" onclick="event.stopPropagation();">
            <input type="checkbox" name="selectTab" id="selectTab<?=$key?>" value="<?=$val['MUser']['id']?>">
            <label for="selectTab<?=$key?>"></label>
          </td>
<!-- UI/UX統合対応end -->
          <td class="tCenter"><?=$no?></td>
          <td class="tCenter"><?=$val['MUser']['user_name']?></td>
          <td class="tCenter"><?=$val['MUser']['display_name']?></td>
          <td class="tCenter"><?=$authorityList[$val['MUser']['permission_level']]?></td>
          <td class="tCenter"><?=$val['MUser']['mail_address']?></td>
<!--
          <td class="tCenter ctrlBtnArea">
            <?php
            if ( $userInfo['id'] === $val['MUser']['id'] ) {
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
                  'class' => 'grayBtn blockCenter',
                  'escape' => false
                )
              );
            }
            else {
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
                  'onclick' => 'event.stopPropagation(); openConfirmDialog('.$val['MUser']['id'].')',
                  'escape' => false
                )
              );
            }
            ?>
          </td>
 -->
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
