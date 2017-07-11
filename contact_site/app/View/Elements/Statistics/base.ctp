  <?= $this->element('Statistics/datepicker') ?>
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
  <script src="https://cdn.datatables.net/fixedcolumns/3.2.1/js/dataTables.fixedColumns.min.js"></script>


  <style type="text/css">
  /* 基本のテーブル定義 */
  /*table.tableFrame {border:1px solid   #595959;border-collapse:collapse;table-layout:fixed;}
  table.tableFrame td{border:1px solid #595959;}
  table.tableFrame th{border:1px solid #595959; height: 35px;}
  table.tableFrame th{background-color:#C3D69B;color:#595959;}

  .tableData{width: 100%;}
  .tableData th{width:100px;}
  .tableData td{width:100px;}

  div#foo-table_wrapper {
    width:100%;
    height:100%;
  }

  .dataTables_scrollHeadInner {
      width: 100% !important;
  }

  table.table.table-bordered.dataTable.no-footer {
      width: 100% !important;
      height:100%;
  }

  .dataTables_scrollBody {
      height: 100% !important;
  }

  .dataTables_scroll {
    height: 100% !important;
  }*/
  /*th, td { white-space: nowrap; }
  div.dataTables_wrapper {
      margin: 0 auto;
      width: 100%;
  }

  div.container {
      width: 80%;
  }

  table.display.dataTable.no-footer {
      width: 100% !important;
  }

  .dataTables_scrollHeadInner {
      width: 100% !important;
  }
*/

  td {
    text-align:center;
  }

  .thMinWidth {
    min-width: 150px;
  }

  .thMinWidthDayly {
    min-width: 70px;
  }

  .thMinWidthTimely {
    min-width: 50px;
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
  //var select2 = document.forms.THistoryForChatForm.selectName2; //変数select2を宣言
  var searchInfo;

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
      var searchInfo = $("select[name=selectName1]").val();
      var date = $("select[name=selectName2]").val();
      console.log('date');
      console.log(date);

      $.ajax({
        type: 'post',
        dataType: 'html',
        data:{
          data:searchInfo,
          date:date,
          //allData:allData
        },
        cache: false,
        url: "<?= $this->Html->url(['controller' => 'Statistics', 'action' => 'outputCsv']) ?>",
        success: function(html){
           location.href = "<?=$this->Html->url(array('controller' => 'Statistics', 'action' => 'forChat'))?>";
        }
      });
    });
        $.extend( $.fn.dataTable.defaults, {
      language: { url: "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" }
    });

    $("#foo-table").DataTable({
        scrollY: "400px",
        searching: false,
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        info: false,
        columnDefs: [
            { width: 120, targets: 0 }
        ],
        fixedColumns: {
            leftColumns: 1
        }
    });

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
          'div'=>false, 'label'=>false,'options'=>array('2016' => '2016','2017' => '2017'), 'selected' => $type,'style' => 'display:none')); ?>

          <?= $this->Form->input('dateForm', array('type'=>'select','name' => 'selectName3','id' => 'daylyForm',
          'div'=>false, 'label'=>false,'options'=>array('2017-04' =>'2017/04','2017-05' =>'2017/05','2017-06' =>'2017/06','2017-07' =>'2017/07'),'style' => 'display:none','selected' => $type)); ?>

          <?= $this->Form->input('dateForm', array('type'=>'text','name' => 'datefilter','id' => 'hourlyForm',
          'div'=>false, 'label'=>false,'options'=>array(substr($type,0,10) => substr($type,0,10)),'style' => 'width:8em;cursor:pointer;display:none','value' => substr($type,0,10)));
          ?>
          <?= $this->Form->end(); ?>
        </left-parts>
        <right-parts>
          <a href="#" id="outputCSV" class="btn-shadow blueBtn">CSV出力</a>
        </right-parts>
      </condition-bar>
      <!-- /* 対象期間選択エリア */ -->
    </div><!-- #statistic_menu -->

    <div id='statistics_content' class="p20trl">

    <!-- /* テーブル表示エリア */ -->


    <table id="foo-table" class="display" cellspacing="0" width="100%">
      <thead>
        <?php if($date == '月別') {
          $start = 1;
          $end = 12; ?>
          <tr>
            <th>統計項目 / 月別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th><?= $type.'-'.sprintf("%02d",$i) ?></th>
            <?php } ?>
          </tr>
        <?php } ?>
        <?php if($date == '日別') {
          $start = 1;
          $end = $daylyEndDate; ?>
          <tr>
            <th class="thMinWidth">統計項目 / 日別</th>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <th class="thMinWidthDayly"><?= $type.'-'.sprintf("%02d",$i) ?></th>
            <?php } ?>
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
          </tr>
        <?php } ?>
      </thead>
      <tbody>
      <?php if($date == '日別' or $date == '月別') { ?>
        <tr>
          <td>合計アクセス件数</td>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <td><?php echo $data['accessDatas']['accessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
            <?php } ?>
        </tr>
        <tr>
          <td>ウィジェット表示件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['widgetDatas']['widgetNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャットリクエスト件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['requestDatas']['requestNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット応対件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['responseDatas']['responseNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット拒否件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['denialNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット有効件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessNumberData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>平均チャットリクエスト時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['avgRequestTimeDatas']['requestAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>平均消費者待機時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>平均応答時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseAvgTimeData']['responseAvgTimeData'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット応対率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseDatas']['responseRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット有効率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessRate'][$type.'-'.sprintf("%02d",$i)] ?></td>
          <?php } ?>
        </tr>
      <?php }
      else if($date == '時別') { ?>
        <tr>
          <td>合計アクセス件数</td>
            <?php for ($i = $start; $i <= $end; $i++) { ?>
              <td><?php echo $data['accessDatas']['accessNumberData'][sprintf("%02d",$i).':00'] ?></td>
            <?php } ?>
        </tr>
        <tr>
          <td>ウィジェット表示件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['widgetDatas']['widgetNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャットリクエスト件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['requestDatas']['requestNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット応対件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
                <td><?php echo $data['responseDatas']['responseNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット拒否件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['denialNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット有効件数</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessNumberData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>平均チャットリクエスト時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['avgRequestTimeDatas']['requestAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>平均消費者待機時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>平均応答時間</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseAvgTimeData']['responseAvgTimeData'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット応対率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['responseDatas']['responseRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <tr>
          <td>チャット有効率</td>
          <?php for ($i = $start; $i <= $end; $i++) { ?>
            <td><?php echo $data['coherentDatas']['effectivenessRate'][sprintf("%02d",$i).':00'] ?></td>
          <?php } ?>
        </tr>
        <?php } ?>
      </tbody>
  </table>

</div>
</div>

    <!-- /* テーブル表示エリア */ -->
  </div><!-- #statistics_content -->
  </div><!-- #sincloApp -->