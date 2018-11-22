<?= $this->element('Statistics/datepicker') ?>
<?= $this->element('Statistics/script') ?>

<div id="sincloApp">

  <div id='statistic_menu' class="p20x">
    <!-- /* 対象期間選択エリア */ -->
    <condition-bar>
      <left-parts>
        <span id = "searchPeriod">対象期間：</span>
        <?= $this->Form->create('Statistics'); ?>

        <?= $this->Form->input('dateType', array('type'=>'select','name' => 'dateFormat','onChange' => 'timeChangeForMessageRanking()',
          'div'=>false, 'style' => 'vertical-align:middle;','label'=>false,'options'=>array('月別'=>'月別','日別'=>'日別','時別'=>'時別'), 'selected' => $date)); ?>

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
        <a href="#" id="outputCSV" class="btn-shadow blueBtn">CSV出力</a>
      </right-parts>
    </condition-bar>
    <!-- /* 対象期間選択エリア */ -->
  </div><!-- #statistic_menu -->

  <div id='statistics_content' class="p20x" style="visibility:hidden;">
    <div id='chatRequestTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>サイト訪問者がチャットを利用（発言／選択肢を選択／リンククリック）した件数（※初回のみカウント）</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatResponseTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>有人チャットリクエストに対してオペレータが入室した件数（※初回入室のみカウント）</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatAutomaticResponseTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>オートリプライまたはシナリオが利用された件数（※初回のみカウント）</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatDenialTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>有人チャットリクエストに対してSorryメッセージを返却した件数（※初回のみカウント）</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatEffectivenessTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>成果が「有効」として登録された件数</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatCVTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>成果が「CV」として登録された件数</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatRequestAverageTimeTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>サイト訪問者がサイトアクセスしてから初回メッセージを送信するまでの平均時間</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatConsumerWaitAverageTimeTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>初回有人チャットリクエストを受信してからオペレータが入室するまでの平均時間</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatResponseAverageTimeTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>初回有人チャットリクエストを受信してからオペレータが初回メッセージを送信するまでの平均時間</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatResponseRateTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>有人チャット応対率：チャット応対件数／有人チャットリクエスト件数</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatAutomaticResponseRateTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>自動返信応対件数／チャットリクエスト件数</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatEffectivenessResponseRateTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>チャット有効件数／有人チャットリクエスト件数</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatLinkClickTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>サイト訪問者がリンクをクリックした件数（※複数回リンクをクリックした場合、クリックした件数分カウント）</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatRequestAbandonTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>有人チャットリクエストに対してオペレータが入室せず放棄した件数（※初回のみカウント）</span></li>
        </ul>
      </icon-annotation>
    </div>
    <div id='chatRequestMannedTooltip' class="explainTooltip">
      <icon-annotation>
        <ul>
          <li><span>有人チャットリクエストの対象となる件数（※初回のみカウント）</span></li>
        </ul>
      </icon-annotation>
    </div>

    <!-- /* テーブル表示エリア */ -->

    <table id="statistics_table" class="display" cellspacing="0" width="100%">
      <thead>
      <?php if($date == '月別') {
        $start = 1;
        $end = 12; ?>
        <tr>
          <th class="thMinWidthMessageRanking">メッセージ</th>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <th class="thMinWidthDaylyMessageRanking" style="padding-left: 13px; padding-right: 13px"><?= $i.'月' ?></th>
          <?php } ?>
          <th class="thMinWidthDaylyMessageRanking" style="padding-left: 16px; padding-right: 16px">合計</th>
        </tr>
      <?php } ?>
      <?php if($date == '日別') {
        $start = 1;
        $end = $daylyEndDate; ?>
        <tr>
          <th class="thMinWidthMessageRanking">メッセージ</th>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <th class="thMinWidthDaylyMessageRanking" style="padding-left: 5px; padding-right: 5px"><?= $i.'日' ?></th>
          <?php } ?>
          <th class="thMinWidthDaylyMessageRanking">合計</th>
        </tr>
      <?php } ?>
      <?php if($date == '時別') {
        $start = 0;
        $end = 23; ?>
        <tr>
          <th class="thMinWidthMessageRanking">メッセージ</th>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <th class = "thMinWidthDaylyMessageRanking" style="padding-left: 5px; padding-right: 5px"><?= sprintf("%02d",$i).'-'.sprintf("%02d",$i+1) ?></th>
          <?php } ?>
          <th class="thMinWidthDaylyMessageRanking">合計</th>
        </tr>
      <?php } ?>
      </thead>
      <tbody>
      <?php if($date == '日別' or $date == '月別') { ?>
        <tr>
          <td class = 'autoMessage tooltip'>sincloを利用することで、このようなチャットボットを簡単に自社サイトに導入することができます。</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <?php if(is_int($data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)]);?></td>
            <?php }
            else { ?>
              <td><?php echo $data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
            <?php } ?>
          <?php } ?>
          <td><?php echo number_format($data['accessDatas']['allAccessNumberData']) ?></td>
        </tr>
        <tr>
          <td class = 'autoMessage tooltip'>機能について</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <?php if(is_int($data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
          <?php } ?>
          <td><?php echo number_format($data['widgetDatas']['allWidgetNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >当サイトに対してご意見、ご要望などがございましたらご自由にご記入ください。アンケートは以上です。ご協力ありがとうございました。</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <?php if(is_int($data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
          <?php } ?>
          <td><?php echo number_format($data['requestDatas']['allRequestNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >sinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <?php if(is_int($data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
          <?php } ?>
          <td><?php echo number_format($data['automaticResponseData']['allAutomaticResponseNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >おかえりなさいませ。
            sinclo（シンクロ）に興味を持っていただきありがとうございます。</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <?php if(is_int($data['linkDatas']['linkNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['linkDatas']['linkNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['linkDatas']['linkNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
          <?php } ?>
          <td><?php echo number_format($data['linkDatas']['allLinkNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >資料請求</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php
            if($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)] !== "" && $data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)] !== ""
              && $data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)] !== "") {
              echo number_format($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)]+$data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)]+$data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } } ?>
          <td><?php echo number_format($data['responseDatas']['allResponseNumberData']+$data['abandonRequestDatas']['allAbandonRequestNumberData']+$data['coherentDatas']['allDenialNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >このようにチャット形式でアンケートを自由に作成することが可能です。料金について</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <?php if(is_int($data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
          <?php } ?>
          <td><?php echo number_format($data['responseDatas']['allResponseNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >「売上にインパクトを与えるコミュニケーションのあり方」を熟知している当社だからこそ、本当に効果のあるチャットボットツールを自信をもってご提供いたします。</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <?php if(is_int($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['abandonRequestDatas']['abandonRequestNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
          <?php } ?>
          <td><?php echo number_format($data['abandonRequestDatas']['allAbandonRequestNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >合計</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <?php if(is_int($data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)]) == 'true') { ?>
              <td><?php echo number_format($data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php }
            else { ?>
              <td><?php echo ($data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)]) ?></td>
            <?php } ?>
          <?php } ?>
          <td><?php echo number_format($data['coherentDatas']['allDenialNumberData']) ?></td>
        </tr>
      <?php }

      else if($date == '時別') { ?>
        <tr>
          <td class = 'autoMessage tooltip'>資料請求</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['accessDatas']['accessNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['accessDatas']['allAccessNumberData']) ?></td>
        </tr>
        <tr>
          <td class = 'autoMessage tooltip'>導入事例が知りたい</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['widgetDatas']['widgetNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['widgetDatas']['allWidgetNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip'>sinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['requestDatas']['requestNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['requestDatas']['allRequestNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseLabel' class = 'autoMessage tooltip'>無料トライアルに申し込む
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['automaticResponseData']['automaticResponseNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['automaticResponseData']['allAutomaticResponseNumberData']) ?></td>
        </tr>
        <tr>
          <td id = 'chatLinkClickTooltip' class = 'autoMessage tooltip'>sinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['linkDatas']['linkNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['linkDatas']['allLinkNumberData']) ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'autoMessage tooltip' >合計</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo number_format($data['linkDatas']['linkNumberData'][sprintf("%02d",$i).':00']) ?></td>
          <?php } ?>
          <td><?php echo number_format($data['linkDatas']['allLinkNumberData']) ?></td>
        </tr>

      <?php } ?>
      </tbody>
    </table>
    <?=$this->Form->create('statistics', ['action' => 'forMessageRanking']);?>
    <?=$this->Form->hidden('outputData')?>
    <?=$this->Form->end();?>
  </div>
</div>

