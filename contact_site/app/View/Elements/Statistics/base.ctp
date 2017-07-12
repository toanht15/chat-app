<?= $this->element('Statistics/datepicker') ?>

<style type="text/css">
.thMinWidth {
  min-width: 150px;
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

div#statistics-table_wrapper {
  top: 25px;
}

.dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody > table > tbody > tr > td {
  vertical-align: middle;
  text-align: center;
}

div#statistics-table_filter {
  margin-bottom: 15px;
}

div#statistics-table_wrapper {
  padding: 4em;
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

    /*var tableObj = $("#statistics-table").DataTable({
    //searching: false,
    scroller:true,
    responsive:true,
    scrollX: true,
    scrollY: '50vh',
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
    });*/


    $('#statistics-table tbody tr td.tooltip').each( function() {
      var description;
      var td = $('td', this);
      console.log(td);
      var tooltipName = $(td['context']).text();

      if ( tooltipName == "合計アクセス件数" )
          description =  tooltipName+' : ページにアクセスされた件数';

      else if ( tooltipName == "ウィジェット表示件数" )
          description = tooltipName+' : ウィジェットが表示された件数';

      else if ( tooltipName == "チャットリクエスト件数" )
          description = tooltipName+' : 消費者からのチャットを企業側が受信した件数';

      else if ( tooltipName == "チャット応対件数" )
          description = tooltipName+' : 消費者からのチャットを企業側が応対した件数';

      else if ( tooltipName == "自動返信応対件数" )
          description = tooltipName+' : 消費者からのチャットを企業側が自動返信で応対した件数';

      else if ( tooltipName == "チャット拒否件数" )
          description = tooltipName+' : Sorryメッセージが消費者に送信された件数';

      else if ( tooltipName == "チャット有効件数" )
          description = tooltipName+' : 成果に有効を登録した件数';

      else if ( tooltipName == "平均チャットリクエスト時間" )
          description = tooltipName+' : 消費者がサイトアクセスしてからチャットを送信するまでの平均時間';

      else if ( tooltipName == "平均消費者待機時間" )
          description = tooltipName+' : 消費者からのチャットに対して、オペレーターがチャットに入室するまでの平均時間';

      else if ( tooltipName == "平均応答時間" )
          description = tooltipName+' : 消費者からのチャットに対して、オペレーターが最初のメッセージを送信するまでの平均時間';

      else if ( tooltipName == "チャット応対率" )
          description = tooltipName+' : チャット応対件数／チャットリクエスト件数*100';

      else if ( tooltipName == "自動返信応対率" )
          description = tooltipName+' : 自動返信応対件数／チャットリクエスト件数*100';

      else if ( tooltipName == "チャット有効率" )
          description = tooltipName+' :   チャット有効件数／チャットリクエスト件数*100';

      this.setAttribute( 'title', description );
    });

    var tableObj = $("#statistics-table").DataTable({
    //searching: false,
    scroller:true,
    responsive:true,
    scrollX: true,
    scrollY: '50vh',
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
          対象期間：
          <?= $this->Form->create(); ?>
          <?= $this->Form->input('dateType', array('type'=>'select','name' => 'selectName1','onChange' => 'functionName()',
          'div'=>false, 'label'=>false,'options'=>array('月別'=>'月別','日別'=>'日別','時別'=>'時別'), 'selected' => $date)); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'selectName2','id' => 'monthlyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeYear, 'selected' => $type,'style' => 'display:none','empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'selectName3','id' => 'daylyForm',
          'div'=>false, 'label'=>false,'options'=>$companyRangeDate,
          'style' => 'display:none;','selected' => $type,'empty' => '選択してください')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'text','name' => 'datefilter','id' => 'hourlyForm',
          'div'=>false, 'label'=>false,'options'=>array(substr($type,0,10) => substr($type,0,10)),
          'style' => 'width:10em;cursor:pointer;display:none','value' => substr($type,0,10),'placeholder' => '選択してください'));
          ?>
          <?= $this->Form->end(); ?>
        </left-parts>
        <right-parts>
        <?=$this->Form->create('statistics', ['action' => 'forChat']);?>
          <?=$this->Form->hidden('outputData')?>
          <?=$this->Form->end();?>
          <a href="#" id="outputCSV" class="btn-shadow blueBtn">CSV出力</a>
        </right-parts>
      </condition-bar>
      <!-- /* 対象期間選択エリア */ -->
    </div><!-- #statistic_menu -->

    <div id='statistics_content' class="p20trl">

    <!-- /* テーブル表示エリア */ -->


    <table id="statistics-table" class="display" cellspacing="0">
      <thead>
        <?php if($date == '月別') {
          $start = 1;
          $end = 12; ?>
          <tr>
            <th>統計項目 / 月別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th><?= $i?></th>
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
              <th class="thMinWidthDayly"><?= $i ?></th>
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
          <td class = 'tooltip'>チャットリクエスト件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['requestDatas']['allRequestNumberData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>チャット応対件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>自動返信応対件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['automaticResponseData']['automaticResponseNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>チャット拒否件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allDenialNumberData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>チャット有効件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessNumberData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>平均チャットリクエスト時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['avgRequestTimeDatas']['requestAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['avgRequestTimeDatas']['allRequestAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>平均消費者待機時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['consumerWatingAvgTimeDatas']['allConsumerWatingAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>平均応答時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseAvgTimeData']['responseAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['responseAvgTimeData']['allResponseAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>チャット応対率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseDatas']['responseRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseRate'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>自動返信応対率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['automaticResponseData']['automaticResponseRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseRate'] ?></td>
        </tr>
        <tr>
          <td class = 'tooltip'>チャット有効率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessRate'] ?></td>
        </tr>

      <?php }
      else if($date == '時別') { ?>
        <tr>
          <td>合計アクセス件数</td>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <td><?php echo $data['accessDatas']['accessNumberData'][sprintf("%02d",$i).':00'] ?></td>
            <?php } ?>
            <td><?php echo $data['accessDatas']['allAccessNumberData'] ?></td>
        </tr>
        <tr>
          <td>ウィジェット表示件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['widgetDatas']['widgetNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['widgetDatas']['allWidgetNumberData'] ?></td>
        </tr>
        <tr>
          <td>チャットリクエスト件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['requestDatas']['requestNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['requestDatas']['allRequestNumberData'] ?></td>
        </tr>
        <tr>
          <td>チャット応対件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['responseDatas']['responseNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td>自動返信応対件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['automaticResponseData']['automaticResponseNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseNumberData'] ?></td>
        </tr>
        <tr>
          <td>チャット拒否件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['denialNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allDenialNumberData'] ?></td>
        </tr>
        <tr>
          <td>チャット有効件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessNumberData'] ?></td>
        </tr>
        <tr>
          <td>平均チャットリクエスト時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['avgRequestTimeDatas']['requestAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['avgRequestTimeDatas']['allRequestAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td>平均消費者待機時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['consumerWatingAvgTimeDatas']['allConsumerWatingAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td>平均応答時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseAvgTimeData']['responseAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseAvgTimeData']['allResponseAvgTimeData'] ?></td>
        </tr>
        <tr>
          <td>チャット応対率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseDatas']['responseRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['responseDatas']['allResponseRate'] ?></td>
        </tr>
        <tr>
          <td>自動返信応対率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['automaticResponseData']['automaticResponseRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['automaticResponseData']['allAutomaticResponseRate'] ?></td>
        </tr>
        <tr>
          <td>チャット有効率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
          <td><?php echo $data['coherentDatas']['allEffectivenessRate'] ?></td>
        </tr>
        <?php } ?>
      </tbody>
  </table>

</div>
</div>

    <!-- /* テーブル表示エリア */ -->
  </div><!-- #statistics_content -->
  </div><!-- #sincloApp -->