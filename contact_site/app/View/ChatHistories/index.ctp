<?= $this->element('Customers/userAgentCheck') ?>
<?= $this->element('ChatHistories/angularjs') ?>
<?= $this->element('ChatHistories/script') ?>
<div id='chat_history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainController"  style = "/*height:860px;*/">

  <div id='history_title'>
    <div class="fLeft"><?= $this->Html->image('history_g.png', array('alt' => '履歴一覧', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
      <h1>履歴一覧</h1>
      <?= $this->Html->link(
        '履歴一覧ＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false,
              'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? " skyBlueBtn commontooltip" : " grayBtn disabled"),
              'id' => 'outputCSV',
              'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
              'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "検索条件に該当するチャット履歴<br>一覧をCSV出力します。" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
              'data-balloon-position' => '75'
          ));
      ?>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>


      <?= $this->Html->link(
        'チャット内容ＣＳＶ出力',
        'javascript:void(0)',
        array('escape' => false,
              'class'=>'btn-shadow'.($coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? " skyBlueBtn commontooltip" : " grayBtn disabled"),
              'id' => 'outputChat',
              'disabled' => !$coreSettings[C_COMPANY_USE_HISTORY_EXPORTING],
              'data-text' => $coreSettings[C_COMPANY_USE_HISTORY_EXPORTING] ? "検索条件に該当するチャット内容を<br>すべてCSV出力します。" : "こちらの機能はスタンダードプラン<br>からご利用いただけます。",
        ));
      ?>
    <?php endif; ?>
  </div>



  <div id = "history_list2" style ="height:810px !important;">
    <div id = "historyBody" style = "width:100%;">
      <div id='history_menu2' class="p20trl">
        <div id="paging" class="fRight">
          <?=
              $this->Paginator->prev(
              $this->Html->image('paging.png', array('alt' => '前のページへ', 'width'=>25, 'height'=>25)),
              array('escape' => false, 'class' => 'btn-shadow greenBtn tr180'),
              null,
              array('class' => 'grayBtn tr180')
            );
          ?>
          <span style="width: auto!important;padding: 10px 0 0;"> <?= $this->Paginator->counter('{:page} / {:pages}'); ?> </span>
          <?=
              $this->Paginator->next(
              $this->Html->image('paging.png', array('alt' => '次のページへ', 'width'=>25, 'height'=>25)),
              array('escape' => false, 'class' => 'btn-shadow greenBtn'),
              null,
              array('escape' => false, 'class' => 'grayBtn')
            );
          ?>
        </div>

        <?= $this->Html->link(
          '高度な検索',
          'javascript:void(0)',
          array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchRefine','onclick' => 'openSearchRefine()','style' => 'top:15px !important; left:30.6em !important;'));
        ?>
        <span id="searchPeriod">検索期間</span>
        <?php
          //検索条件表示：非表示
          $noseach_menu = '';
          $seach_menu = 'seach_menu';
        ?>
        <?php //検索をした時の表示
          if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
            <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['period']) ?> : <?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
        <?php } ?>
        <?php //セッションをクリアしたときの表示(履歴一覧ボタンを押下した時)
          if(empty($data['History']['start_day'])&&empty($data['History ']['finish_day'])) { ?>
            <span id ='mainDatePeriod' name = 'datefilter' class='date'>過去一ヵ月間 : <?= h($historySearchConditions['start_day']) ?>-<?= h($historySearchConditions['finish_day']) ?></span>
        <?php } ?>
        <?php
            if(
              empty($data['History']['ip_address'])&&empty($data['History']['company_name'])
              &&empty($data['History']['customer_name'])&&empty($data['History']['telephone_number'])
              &&empty($data['History']['mail_address'])&&empty($data['THistoryChatLog']['responsible_name'])
              &&($data['THistoryChatLog']['achievement_flg'] === "")
              &&empty($data['THistoryChatLog']['message'])){
              $noseach_menu = 'noseach_menu';
              $seach_menu='　';
            }
        ?>

        <div class=<?= $seach_menu; ?> id=<?= $noseach_menu ?> style = "height: 37px !important;">
          <label class='searchConditions'>検索条件</label>
          <ul ng-non-bindable>
            <?php if(!empty($data['History']['ip_address'])) { ?>
              <li>
                <label>IPｱﾄﾞﾚｽ</label>
                <span class="value"><?= h($data['History']['ip_address']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['History']['company_name'])) { ?>
              <li>
                <label>会社名</label>
                <span class="value"><?= h($data['History']['company_name']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['History']['customer_name'])) { ?>
              <li>
                <label class="label">名前</label>
                <span class="value"><?= h($data['History']['customer_name']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['History']['telephone_number'])) { ?>
              <li>
                <label>電話番号</label>
                <span class="value"><?= h($data['History']['telephone_number']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['History']['mail_address'])) { ?>
              <li>
                <label>ﾒｰﾙｱﾄﾞﾚｽ</label>
                <span class="value"><?= h($data['History']['mail_address']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['THistoryChatLog']['responsible_name'])) { ?>
              <li>
                <label>担当者</label>
                <span class="value"><?= h($data['THistoryChatLog']['responsible_name']) ?></span>
              </li>
            <?php } ?>
            <?php if(isset($data['THistoryChatLog']['achievement_flg']) && ($data['THistoryChatLog']['achievement_flg'] !== "" || $data['THistoryChatLog']['achievement_flg'] === 0)) { ?>
              <li>
                <label>成果</label>
                <span class="value"><?= $achievementType[h($data['THistoryChatLog']['achievement_flg'])] ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['THistoryChatLog']['message'])) { ?>
              <li>
                <label>ﾁｬｯﾄ内容</label>
                <span class="value"><?= h($data['THistoryChatLog']['message']) ?></span>
              </li>
            <?php } ?>

            <?= $this->Html->link(
              '条件クリア',
              'javascript:void(0)',
              ['escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'sessionClear','onclick' => 'sessionClear()']);
            ?>
          </ul>
        </div>
        <!-- 検索窓 -->
        <div class='fLeft'>
          <?php
            if ($coreSettings[C_COMPANY_USE_CHAT]) :
            $checked = "";
            $class = "";
            if (strcmp($groupByChatChecked, 'false') !== 0) {
              $class = "";
              $checked = "checked=\"\"";
            }
          ?>
            <label for="g_chat" class="pointer <?=$class?>">
              <input type="checkbox" id="g_chat" name="group_by_chat" <?=$checked?> />
              CV(コンバージョン)のみ表示する
            </label>
          <?php endif; ?>
          <?=$this->Form->create('History', ['action' => 'index']);?>
            <?=$this->Form->hidden('outputData')?>
          <?=$this->Form->end();?>
          <?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => '/Histories']); ?>
        </div>
      </div>
      <div class="btnSet" style = "height: 85px;">
           <span>
             <a>
               <?= $this->Html->image('csv.png', array(
                   'alt' => 'CSV出力',
                   'id'=>'history_csv_btn',
                   'class' => 'btn-shadow disOffgrayBtn commontooltip',
                   'data-text' => 'CSV出力',
                   'data-balloon-position' => '36',
                   'width' => 45,
                   'height' => 45,
                   'onclick' => 'openAdd()',
                   'style' => 'margin-left: -17em;margin-top:6px;'
               )) ?>
             </a>
           </span>
           <span>
             <a>
               <?= $this->Html->image('dustbox.png', array(
                   'alt' => '削除',
                   'id'=>'hisory_dustbox_btn',
                   'class' => 'btn-shadow disOffgrayBtn commontooltip',
                   'data-text' => '削除する',
                   'data-balloon-position' => '36',
                   'width' => 45,
                   'height' => 45)) ?>
             </a>
           </span>
        </div>
        <?=$this->element('ChatHistories/list')?>
        <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>
      </div>





      <div id='history_menu' class="p20trl" style = "display:none;">
        <div id="paging" class="fRight">
          <?=
              $this->Paginator->prev(
              $this->Html->image('paging.png', array('alt' => '前のページへ', 'width'=>25, 'height'=>25)),
              array('escape' => false, 'class' => 'btn-shadow greenBtn tr180'),
              null,
              array('class' => 'grayBtn tr180')
            );
          ?>
          <span style="width: auto!important;padding: 10px 0 0;"> <?= $this->Paginator->counter('{:page} / {:pages}'); ?> </span>
          <?=
              $this->Paginator->next(
              $this->Html->image('paging.png', array('alt' => '次のページへ', 'width'=>25, 'height'=>25)),
              array('escape' => false, 'class' => 'btn-shadow greenBtn'),
              null,
              array('escape' => false, 'class' => 'grayBtn')
            );
          ?>
        </div>

        <?= $this->Html->link(
          '高度な検索',
          'javascript:void(0)',
          array('escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'searchRefine','onclick' => 'openSearchRefine()','style' => 'position: absolute;
        top: 80px;
        left: 32em;
        width: 8em;
        padding: 0.25em 0.5em;
        text-align: center;'));
        ?>
        <span id="searchPeriod">検索期間</span>
        <?php
          //検索条件表示：非表示
          $noseach_menu = '';
          $seach_menu = 'seach_menu';
        ?>
        <?php //検索をした時の表示
          if(!empty($data['History']['start_day'])||!empty($data['History ']['finish_day'])) { ?>
            <span id ='mainDatePeriod' name = 'datefilter'><?= h($data['History']['period']) ?> : <?= h($data['History']['start_day']) ?>-<?= h($data['History']['finish_day']) ?></span>
        <?php } ?>
        <?php //セッションをクリアしたときの表示(履歴一覧ボタンを押下した時)
          if(empty($data['History']['start_day'])&&empty($data['History ']['finish_day'])) { ?>
            <span id ='mainDatePeriod' name = 'datefilter' class='date'>過去一ヵ月間 : <?= h($historySearchConditions['start_day']) ?>-<?= h($historySearchConditions['finish_day']) ?></span>
        <?php } ?>
        <?php
            if(
              empty($data['History']['ip_address'])&&empty($data['History']['company_name'])
              &&empty($data['History']['customer_name'])&&empty($data['History']['telephone_number'])
              &&empty($data['History']['mail_address'])&&empty($data['THistoryChatLog']['responsible_name'])
              &&($data['THistoryChatLog']['achievement_flg'] === "")
              &&empty($data['THistoryChatLog']['message'])){
              $noseach_menu = 'noseach_menu';
              $seach_menu='　';
            }
        ?>

        <div class=<?= $seach_menu; ?> id=<?= $noseach_menu ?> style = "height: 37px !important;">
          <label class='searchConditions'>検索条件</label>
          <ul ng-non-bindable>
            <?php if(!empty($data['History']['ip_address'])) { ?>
              <li>
                <label>IPｱﾄﾞﾚｽ</label>
                <span class="value"><?= h($data['History']['ip_address']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['History']['company_name'])) { ?>
              <li>
                <label>会社名</label>
                <span class="value"><?= h($data['History']['company_name']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['History']['customer_name'])) { ?>
              <li>
                <label class="label">名前</label>
                <span class="value"><?= h($data['History']['customer_name']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['History']['telephone_number'])) { ?>
              <li>
                <label>電話番号</label>
                <span class="value"><?= h($data['History']['telephone_number']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['History']['mail_address'])) { ?>
              <li>
                <label>ﾒｰﾙｱﾄﾞﾚｽ</label>
                <span class="value"><?= h($data['History']['mail_address']) ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['THistoryChatLog']['responsible_name'])) { ?>
              <li>
                <label>担当者</label>
                <span class="value"><?= h($data['THistoryChatLog']['responsible_name']) ?></span>
              </li>
            <?php } ?>
            <?php if(isset($data['THistoryChatLog']['achievement_flg']) && ($data['THistoryChatLog']['achievement_flg'] !== "" || $data['THistoryChatLog']['achievement_flg'] === 0)) { ?>
              <li>
                <label>成果</label>
                <span class="value"><?= $achievementType[h($data['THistoryChatLog']['achievement_flg'])] ?></span>
              </li>
            <?php } ?>
            <?php if(!empty($data['THistoryChatLog']['message'])) { ?>
              <li>
                <label>ﾁｬｯﾄ内容</label>
                <span class="value"><?= h($data['THistoryChatLog']['message']) ?></span>
              </li>
            <?php } ?>

                <?= $this->Html->link(
                  '条件クリア',
                  'javascript:void(0)',
                  ['escape' => false, 'class'=>'skyBlueBtn btn-shadow','id' => 'sessionClear','onclick' => 'sessionClear()']);
                ?>
              </ul>
        </div>
        <!-- 検索窓 -->
        <div class='fLeft'>
          <?php
            if ($coreSettings[C_COMPANY_USE_CHAT]) :
            $checked = "";
            $class = "";
            if (strcmp($groupByChatChecked, 'false') !== 0) {
              $class = "";
              $checked = "checked=\"\"";
            }
          ?>
            <label for="g_chat" class="pointer <?=$class?>">
              <input type="checkbox" id="g_chat" name="group_by_chat" <?=$checked?> />
              CV(コンバージョン)のみ表示する
            </label>
          <?php endif; ?>
          <?=$this->Form->create('History', ['action' => 'index']);?>
            <?=$this->Form->hidden('outputData')?>
          <?=$this->Form->end();?>
          <?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => '/Histories']); ?>
        </div>
      </div>
      <div class="btnSet" style = "height: 85px;">
           <span>
             <a>
               <?= $this->Html->image('csv.png', array(
                   'alt' => 'CSV出力',
                   'id'=>'history_csv_btn',
                   'class' => 'btn-shadow disOffgrayBtn commontooltip',
                   'data-text' => 'CSV出力',
                   'data-balloon-position' => '36',
                   'width' => 45,
                   'height' => 45,
                   'onclick' => 'openAdd()',
                   'style' => 'margin-left: -17em;margin-top:6px;'
               )) ?>
             </a>
           </span>
           <span>
             <a>
               <?= $this->Html->image('dustbox.png', array(
                   'alt' => '削除',
                   'id'=>' height:100%;ory_dustbox_btn',
                   'class' => 'btn-shadow disOffgrayBtn commontooltip',
                   'data-text' => '削除する',
                   'data-balloon-position' => '36',
                   'width' => 45,
                   'height' => 45)) ?>
             </a>
           </span>
         </div>

          <div id='history_list' class="p20x" style = "position:relative; height:710px; display:none;" >
            <?=$this->element('ChatHistories/list2')?>
            <a href="javascript:void(0)" style="display:none" id="modalCtrl"></a>
          </div>
    </div>
</div>