<?= $this->element('Statistics/datepicker') ?>
<?= $this->element('Statistics/script') ?>

<div id="sincloApp">

  <div id='statistic_menu' class="p20trl">
    <!-- /* 対象期間選択エリア */ -->
    <condition-bar>
      <left-parts>
        <span id = "searchPeriod">対象期間：</span>
        <?= $this->Form->create('Statistics'); ?>

          <?= $this->Form->input('dateType', array('type'=>'select','name' => 'dateFormat','onChange' => 'timeChangeForOperator()',
          'div'=>false, 'style' => 'vertical-align:middle;','label'=>false,'options'=>array('月別'=>'月別','日別'=>'日別','時別'=>'時別'), 'selected' => $date,
          'autocomplete'=> 'off')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'monthlyName','id' => 'monthlyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeYear, 'selected' => $type,'style' => 'display:none;vertical-align:middle','empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'daylyName','id' => 'daylyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeDate,
          'style' => 'display:none;vertical-align:middle;','selected' => $type,'empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'text','name' => 'datefilter','id' => 'hourlyForm',
          'div'=>false, 'label'=>false,'style' => 'width:11em;cursor:pointer;display:none','value' => substr($type,0,10),'placeholder' => date("Y/m/d "),'autocomplete'=>'off')); ?>
          <b id = 'triangle'></b>

        <?= $this->Form->end(); ?>
      </left-parts>
      <right-parts>
        <a href="#" id="outputOperatorCSV" class="btn-shadow blueBtn">CSV出力</a>
      </right-parts>
      </condition-bar>
      <!-- /* 対象期間選択エリア */ -->
    </div><!-- #statistic_menu -->

    <div id='statistics_content' class="p20trl" style="visibility:hidden;">
    <!-- /* テーブル表示エリア */ -->

    <table id="statistics_table" class="display opList" cellspacing="0" width = "100%">
      <thead>
        <tr>
          <th class="thMinWidthOp" onclick = 'example()'>オペレータ / <?= $date; ?></th>
          <th class = "thMinWidthTimelyForOperator">
            <a href="<?=$this->Html->url(array('controller' => 'Statistics',
            'action' => 'loadingHtml','?'=>array('item'=>'login','type'=>$time,'target'=>$type)))?>"
            onclick="window.open(this.href, 'mywindow1.<?= $time ?>', 'width=1000, height=700' ); return false;">ログイン件数</a>
          <th class = "thMinWidthTimelyForOperator tooltip" id="opChatRequestLabel">
            <a href="<?=$this->Html->url(array('controller' => 'Statistics',
            'action' => 'loadingHtml','?'=>array('item'=>'requestChat','type'=>$time,'target'=>$type)))?>"
            onclick="window.open(this.href, 'mywindow2.<?= $time ?>', 'width=1000, height=700'); return false;">チャットリクエスト件数</a>
          <div class="opQuestionBalloon commontooltip" data-text="サイト訪問者が送信したチャットを待機中のオペレーターが受信した件数(※初回メッセージのみカウント)">
            <icon class="opQuestionBtn">？</icon>
          </div></th>
          <th class = "thMinWidthTimelyForOperator tooltip" id="opChatResponseLabel">
            <a href="<?=$this->Html->url(array('controller' => 'Statistics',
            'action' => 'loadingHtml','?'=>array('item'=>'responseChat','type'=>$time,'target'=>$type)))?>"
            onclick="window.open(this.href, 'mywindow3.<?= $time ?>', 'width=1000, height=700'); return false;">チャット応対件数</a>
          <div class="opQuestionBalloon commontooltip" data-text="オペレータが入室した件数">
            <icon class="opQuestionBtn">？</icon>
          </div></th>
          <th class = "thMinWidthTimelyForOperator tooltip" id="opChatEffectivenessLabel">
            <a href="<?=$this->Html->url(array('controller' => 'Statistics',
            'action' => 'loadingHtml','?'=>array('item'=>'effectiveness','type'=>$time,'target'=>$type)))?>"
            onclick="window.open(this.href, 'mywindow4.<?= $time ?>', 'width=1000, height=700'); return false;">チャット有効件数</a>
          <div class="opQuestionBalloon commontooltip" data-text="成果が「有効」として登録された件数">
            <icon class="opQuestionBtn">？</icon>
          </div></th>
          <th class = "thMinWidthTimelyForOperator tooltip" id="opChatConsumerWaitAverageTimeLabel">
            <a href="<?=$this->Html->url(array('controller' => 'Statistics',
            'action' => 'loadingHtml','?'=>array('item'=>'avgConsumersWaitTime','type'=>$time,'target'=>$type)))?>"
            onclick="window.open(this.href, 'mywindow5.<?= $time ?>', 'width=1000, height=700'); return false;">平均消費者待機時間</a>
          <div class="opQuestionBalloon commontooltip" data-text="サイト訪問者の初回メッセージを受信してから、オペレータがチャットに入室するまでの平均時間">
            <icon class="opQuestionBtn">？</icon>
          </div></th>
          <th class = "thMinWidthTimelyForOperator tooltip" id="opChatResponseAverageTimeLabel">
            <a href="<?=$this->Html->url(array('controller' => 'Statistics',
            'action' => 'loadingHtml','?'=>array('item'=>'avgResponseTime','type'=>$time,'target'=>$type)))?>"
            onclick="window.open(this.href, 'mywindow6.<?= $time ?>', 'width=1000, height=700'); return false;">平均応答時間</a>
          <div class="opQuestionBalloon commontooltip" data-text="サイト訪問者の初回メッセージを受信してから、オペレータが初回メッセージを送信するまでの平均時間">
            <icon class="opQuestionBtn">？</icon>
          </div></th>
          <th class = "thMinWidthTimelyForOperator opLastTooltip" id="opChatEffectivenessResponseRateLabel">
            <a href="<?=$this->Html->url(array('controller' => 'Statistics',
            'action' => 'loadingHtml','?'=>array('item'=>'effectivenessRate','type'=>$time,'target'=>$type)))?>"
            onclick="window.open(this.href, 'mywindow7.<?= $time ?>', 'width=1000, height=700'); return false;">チャット有効率</a>
          <div class="opQuestionBalloon commontooltip" data-text="チャット有効件数／チャット応対件数">
            <icon class="opQuestionBtn">？</icon>
          </div></th>
        </tr>
      </thead>
      <?php
      foreach($data['users'] as $k => $v) {
       ?>
        <tr>
          <td class = 'userName'>
            <a href="<?=$this->Html->url(array('controller' => 'Statistics',
            'action' => 'loadingHtml','?'=>array('id'=>$v['m_users']['id'],'type'=>$time,'target'=>$type)))?>"
            onclick="window.open(this.href, '<?= $k.$type ?>', 'width=1000, height=700'); return false;">
          <?= $v['m_users']['display_name'] ?></a></td>
          <td><?php if(empty($v['loginNumber'])) {
            echo  0;
          }
          else {
            echo $v['loginNumber'];
          } ?></td>
          <td><?php if(empty($v['requestNumber'])) {
            echo 0;
          }
          else {
            echo $v['requestNumber'];
          } ?></td>
          <td><?php if(empty($v['responseNumber'])) {
            echo 0;
          }
          else {
            echo $v['responseNumber'];
            } ?></td>
          <td><?php if(!empty($v['effectivenessNumber'])) {
            echo $v['effectivenessNumber'];
            }
            else {
            echo 0;
            } ?></td>
          <td><?= $v['avgEnteringRommTime']?></td>
          <td><?= $v['responseTime'] ?></td>
          <td class = "tooltip"><?php
          if(empty($v['effectivenessRate'])) {
            echo '0%';
          }
          else {
           if(is_numeric($v['effectivenessRate'])) {
              $checkData = ' %';
            }
            else {
              $checkData = '';
            }
            echo $v['effectivenessRate'].$checkData;
          } ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <?=$this->Form->create('statistics', ['action' => 'forChat']);?>
      <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
  </div>
</div>

