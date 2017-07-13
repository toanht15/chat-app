<?= $this->element('Statistics/datepicker') ?>

<style type="text/css">

.questionBalloon icon {
    border-radius: 20px;
    padding: 0.1em;
    width: 1.5em;
    height: 1.5em;
    font-size: 0.9em !important;
}

.questionBalloon.questionBalloonPosition6 {
    float: right;
    margin-right: 89px;
}

.questionBalloon.questionBalloonPosition7 {
    float: right;
    margin-right: 77px;
}

.questionBalloon.questionBalloonPosition8 {
    float: right;
    margin-right: 65px;
}

.questionBalloon.questionBalloonPosition9 {
    float: right;
    margin-right: 53px;
}

.questionBalloon.questionBalloonPosition11 {
    float: right;
    margin-right: 29px;
}

.questionBalloon.questionBalloonPosition13 {
    float: right;
    margin-right: 5px;
}


form#THistoryForChatForm {
    margin-bottom: 0;
}

.thMinWidth {
  min-width: 170px;
}

.thMinWidthDayly {
  min-width: 70px;
}

.thMinWidthTimely {
  min-width: 50px;
}

tr.odd {
  height: 40px !important;
}

tr.even {
  height: 40px !important;
}

.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody > table > tbody > tr > td {
  vertical-align: middle;
  text-align: center;
}

div#statistics-table_filter {
  padding-bottom: 20px;
}

div#statistics-table_wrapper {
  /*padding: 4em;*/
}

td.tooltip {
    cursor: pointer;
}

  </style>
<script type="text/javascript">


$(function() {
  var timeType = {
    monthly: '月別',
    dayly: '日別',
    timely: '時別'
  }

  if('<?= $date ?>' == '月別'){
    document.getElementById("monthlyForm").style.display="";
  }
  if('<?= $date ?>' == '日別'){
    document.getElementById("daylyForm").style.display="";
  }
  if('<?= $date ?>' == '時別'){
    document.getElementById("hourlyForm").style.display="";
  }

  $("#monthlyForm").change(function(){
    var searchInfo = $("select[name=selectName1]").val();
    console.log(searchInfo);

    if(searchInfo == timeType.monthly) {
      document.getElementById('THistoryForChatForm').submit();
    }
  });

  $("#daylyForm").change(function(){
    var searchInfo = $("select[name=selectName1]").val();
    console.log(searchInfo);

    if(searchInfo == timeType.dayly) {
      document.getElementById('THistoryForChatForm').submit();
    }
  });

  $('input[name="datefilter"]').daterangepicker({
    "locale": {
      "format": "YYYY-MM-DD",
      "daysOfWeek": [
        "日",
        "月",
        "火",
        "水",
        "木",
        "金",
        "土"
      ],
      "monthNames": [
        "1月",
        "2月",
        "3月",
        "4月",
        "5月",
        "6月",
        "7月",
        "8月",
        "9月",
        "10月",
        "11月",
        "12月"
      ],
    },
    singleDatePicker: true,
  },
  function(start, end, label) {
    searchInfo = $("select[name=selectName1]").val();
    $('input[name="datefilter"]').val(start.format('YYYY-MM-DD'));
    if(searchInfo == timeType.timely){
      document.getElementById('THistoryForChatForm').submit();
    }
  });
});

  function functionName()　{
  var select1 = document.forms.THistoryForChatForm.selectName1; //変数select1を宣言

  if (select1.options[select1.selectedIndex].value == "月別")
  {
    console.log('月別選んだ');
    document.getElementById("monthlyForm").style.display="";
    document.getElementById("daylyForm").style.display="none";
    document.getElementById("hourlyForm").style.display="none";
    document.getElementById("monthlyForm").value = "";
  }

  else if (select1.options[select1.selectedIndex].value == "日別")
  {
    console.log('日別選んだ');
    document.getElementById("monthlyForm").style.display="none";
    document.getElementById("daylyForm").style.display="";
    document.getElementById("hourlyForm").style.display="none";
    document.getElementById("daylyForm").value = "";
  }

  else if (select1.options[select1.selectedIndex].value == "時別")
    {
      console.log('日選んだ');
    document.getElementById("monthlyForm").style.display="none";
    document.getElementById("daylyForm").style.display="none";
    document.getElementById("hourlyForm").style.display="";
    document.getElementById("hourlyForm").value = "";
    }
  }

  $(document).ready(function(){

    $('#statistics-table tbody tr td div.questionBalloon ').each( function() {
      var description;
      var td = $(this).parent();
      var tooltipName = td.attr("id");

      switch (tooltipName){
        case 'chatRequestLabel':
          description = 'サイト訪問者がチャットを送信した件数(※初回メッセージのみカウント)';
          break;
        case 'chatResponseLabel':
          description = 'チャットリクエストに対してオペレータが入室した件数（※初回入室のみカウント）';
        break;
        case 'chatAutomaticResponseLabel':
         description = 'サイト訪問者からのチャットを企業側が自動返信で応対した件数(※初回メッセージのみカウント)';
        break;
        case 'chatDenialLabel':
          description = 'Sorryメッセージが消費者に送信された件数';
        break;
        case 'chatEffectivenessLabel':
          description = '成果が「有効」として登録された件数';
        break;
        case 'chatRequestAverageTimeLabel':
          description = 'サイト訪問者がサイトアクセスしてから初回メッセージを送信するまでの平均時間';
        break;
        case 'chatConsumerWaitAverageTimeLabel':
          description = 'サイト訪問者の初回メッセージを受信してから、オペレータがチャットに入室するまでの平均時間';
        break;
        case 'chatResponseAverageTimeLabel':
          description = 'サイト訪問者の初回メッセージを受信してから、オペレータが初回メッセージを送信するまでの平均時間';
        break;
        case 'chatResponseRateLabel':
          description = 'チャット応対件数／チャットリクエスト件数';
        break;
        case 'chatAutomaticResponseRateLabel':
          description = '自動返信応対件数／チャットリクエスト件数';
        break;
        case 'chatEffectivenessResponseRateLabel':
          description = 'チャット有効件数／チャットリクエスト件数';
        break;

      }
      this.setAttribute( 'title', description );
    });

    var tableObj = $("#statistics-table").DataTable({
      //searching: false,
      scroller:true,
      responsive:true,
      scrollX: true,
      scrollY: '64vh',
      responsive: true,
      scrollCollapse: true,
      paging: false,
      info: false,
      ordering: false,
      columnDefs: [
          { width: 120, targets: 0 }
      ],
      fixedColumns: {
          leftColumns: 1
      }
    });

    var resizeDataTable = function() {
      $('.dataTables_scrollBody').css('max-height',$('#statistics_content').outerHeight() - 120 + 'px');
      tableObj.draw();
    }

    $(window).on('resize', function(event){
      console.log("resize");
      resizeDataTable();
    });

    // ページ読み込み時にもリサイズ処理を実行
    resizeDataTable();

    $('#statistics_content').css('visibility','visible');

    var outputCSVBtn = document.getElementById('outputCSV');
    outputCSVBtn.addEventListener('click', function(){
      var dateFormat = $("select[name=selectName1]").val();
      if(dateFormat == '月別') {
        var date = $("#monthlyForm").val();
      }
      if(dateFormat == '日別') {
         date = $("#daylyForm").val();
      }
      if(dateFormat == '時別') {
         date = $("#hourlyForm").val();
      }

      document.getElementById('statisticsOutputData').value = JSON.stringify({dateFormat:dateFormat,date:date});
      console.log(document.getElementById('statisticsOutputData').value.date);
      document.getElementById('statisticsForChatForm').action = '<?=$this->Html->url(["controller"=>"Statistics", "action" => "outputCsv"])?>';
      document.getElementById('statisticsForChatForm').submit();
    });

    $.extend( $.fn.dataTable.defaults, {
      language: { url: "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" }
    });


    /* Apply the tooltips */
    /*tableObj.$('.dataTable').tooltip( {
        //"delay": 0,
        //"track": true,
        //"fade": 250
    } );*/
  });

  </script>
  <div id="sincloApp">

    <div id='statistic_menu' class="p20trl">
      <!-- /* 対象期間選択エリア */ -->
      <condition-bar>
        <left-parts>
          <span id = "searchPeriod">対象期間：</span>
          <?= $this->Form->create(); ?>

          <?= $this->Form->input('dateType', array('type'=>'select','name' => 'selectName1','onChange' => 'functionName()',
          'div'=>false, 'style' => 'vertical-align:middle;','label'=>false,'options'=>array('月別'=>'月別','日別'=>'日別','時別'=>'時別'), 'selected' => $date)); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'selectName2','id' => 'monthlyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeYear, 'selected' => $type,'style' => 'display:none;vertical-align:middle','empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'selectName3','id' => 'daylyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeDate,
          'style' => 'display:none;vertical-align:middle;','selected' => $type,'empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'text','name' => 'datefilter','id' => 'hourlyForm',
          'div'=>false, 'label'=>false,'options'=>array(substr($type,0,10) => substr($type,0,10)),
          'style' => 'width:10em;cursor:pointer;display:none','value' => substr($type,0,10),'placeholder' => '選択してください'));
          ?>
          <?= $this->Form->end(); ?>
        </left-parts>
        <right-parts>
          <a href="#" id="outputCSV" class="btn-shadow blueBtn">CSV出力</a>
        </right-parts>
      </condition-bar>
      <!-- /* 対象期間選択エリア */ -->
    </div><!-- #statistic_menu -->

    <div id='statistics_content' class="p20trl" style="visibility:hidden;">

    <!-- /* テーブル表示エリア */ -->


    <table id="statistics-table" class="display" cellspacing="0" width = "100%">
      <thead>
        <?php if($date == '月別') {
          $start = 1;
          $end = 12; ?>
          <tr>
            <th class="thMinWidth">統計項目 / 月別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th class="thMinWidthDayly"><?= $i.'月' ?></th>
            <?php } ?>
            <th class="thMinWidthDayly">合計・平均</th>
          </tr>
        <?php } ?>
        <?php if($date == '日別') {
          $start = 1;
          $end = $daylyEndDate; ?>
          <tr>
            <th class="thMinWidth">統計項目 / 日別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th class="thMinWidthDayly"><?= $i.'日' ?></th>
            <?php } ?>
            <th class="thMinWidthDayly">合計・平均</th>
          </tr>
        <?php } ?>
        <?php if($date == '時別') {
          $start = 0;
          $end = 23; ?>
          <tr>
            <th class="thMinWidth">統計項目 / 時別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th class = "thMinWidthTimely"><?= sprintf("%02d",$i).'-'.sprintf("%02d",$i+1) ?></th>
            <?php } ?>
            <th class="thMinWidthDayly">合計・平均</th>
          </tr>
        <?php } ?>
      </thead>
      <tbody>
      <?php if($date == '日別' or $date == '月別') { ?>
        <tr>
          <td class = 'tooltip'>合計アクセス件数</td>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <td><?php echo $data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
            <?php } ?>
             <td><?php echo $data['accessDatas']['allAccessNumberData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>ウィジェット表示件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['widgetDatas']['allWidgetNumberData'] ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'tooltip'>チャットリクエスト件数
            <div class="questionBalloon questionBalloonPosition11">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['requestDatas']['allRequestNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatResponseLabel'  class = 'tooltip'>チャット応対件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseLabel' class = 'tooltip'>自動返信応対件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatDenialLabel' class = 'tooltip'>チャット拒否件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allDenialNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessLabel' class = 'tooltip'>チャット有効件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatRequestAverageTimeLabel' class = 'tooltip'>平均チャットリクエスト時間
            <div class="questionBalloon questionBalloonPosition13">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['avgRequestTimeDatas']['requestAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['avgRequestTimeDatas']['allRequestAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id ='chatConsumerWaitAverageTimeLabel' class = 'tooltip'>平均消費者待機時間
            <div class="questionBalloon questionBalloonPosition9">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['consumerWatingAvgTimeDatas']['allConsumerWatingAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id ='chatResponseAverageTimeLabel' class = 'tooltip'>平均応答時間
            <div class="questionBalloon questionBalloonPosition6">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseAvgTimeData']['responseAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['responseAvgTimeData']['allResponseAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatResponseRateLabel' class = 'tooltip'>チャット応対率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseDatas']['responseRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseRate'] ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseRateLabel' class = 'tooltip'>自動返信応対率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['automaticResponseData']['automaticResponseRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseRate'] ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessResponseRateLabel' class = 'tooltip'>チャット有効率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessRate'] ?></td>
        </tr>

      <?php }
      else if($date == '時別') { ?>
        <tr>
          <td class = 'tooltip'>合計アクセス件数</td>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <td><?php echo $data['accessDatas']['accessNumberData'][sprintf("%02d",$i).':00'] ?></td>
            <?php } ?>
            <td><?php echo $data['accessDatas']['allAccessNumberData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>ウィジェット表示件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['widgetDatas']['widgetNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['widgetDatas']['allWidgetNumberData'] ?></td>
        </tr>
        <tr>
          <td id="chatRequestLabel" class = 'tooltip'>チャットリクエスト件数
            <div class="questionBalloon questionBalloonPosition11">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['requestDatas']['requestNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['requestDatas']['allRequestNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatResponseLabel'  class = 'tooltip'>チャット応対件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['responseDatas']['responseNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseLabel' class = 'tooltip'>自動返信応対件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['automaticResponseData']['automaticResponseNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatDenialLabel' class = 'tooltip'>チャット拒否件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['denialNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allDenialNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessLabel' class = 'tooltip'>チャット有効件数
            <div class="questionBalloon questionBalloonPosition8">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessNumberData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatRequestAverageTimeLabel' class = 'tooltip'>平均チャットリクエスト時間
            <div class="questionBalloon questionBalloonPosition13">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['avgRequestTimeDatas']['requestAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['avgRequestTimeDatas']['allRequestAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id ='chatConsumerWaitAverageTimeLabel' class = 'tooltip'>平均消費者待機時間
            <div class="questionBalloon questionBalloonPosition9">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['consumerWatingAvgTimeDatas']['allConsumerWatingAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id ='chatResponseAverageTimeLabel' class = 'tooltip'>平均応答時間
            <div class="questionBalloon questionBalloonPosition6">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseAvgTimeData']['responseAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseAvgTimeData']['allResponseAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td id = 'chatResponseRateLabel' class = 'tooltip'>チャット応対率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseDatas']['responseRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseRate'] ?></td>
        </tr>
        <tr>
          <td id = 'chatAutomaticResponseRateLabel' class = 'tooltip'>自動返信応対率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['automaticResponseData']['automaticResponseRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseRate'] ?></td>
        </tr>
        <tr>
          <td id = 'chatEffectivenessResponseRateLabel' class = 'tooltip'>チャット有効率
            <div class="questionBalloon questionBalloonPosition7">
              <icon class="questionBtn">？</icon>
            </div>
          </td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessRate'] ?></td>
        </tr>
        <?php } ?>
      </tbody>
  </table>
        <?=$this->Form->create('statistics', ['action' => 'forChat']);?>
          <?=$this->Form->hidden('outputData')?>
          <?=$this->Form->end();?>
</div>
</div>

    <!-- /* テーブル表示エリア */ -->
  </div><!-- #statistics_content -->
  </div><!-- #sincloApp -->