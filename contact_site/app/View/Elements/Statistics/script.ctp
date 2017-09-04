<script type="text/javascript">
<?= $this->element('TDocuments/loadScreen'); ?>

function timeChange()　{
  var chosenDateFormat = document.forms.StatisticsForChatForm.dateFormat;

  //  selectで月別を選択した場合
  if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "月別")
  {
    document.getElementById("monthlyForm").style.display="";
    document.getElementById("daylyForm").style.display="none";
    document.getElementById("hourlyForm").style.display="none";
    document.getElementById("monthlyForm").value = "";
    document.getElementById("triangle").style.borderTop = "0px";
  }
  //selectで日別を選択した場合
  else if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "日別")
  {
    document.getElementById("monthlyForm").style.display="none";
    document.getElementById("daylyForm").style.display="";
    document.getElementById("hourlyForm").style.display="none";
    document.getElementById("hourlyForm").value = "";
    document.getElementById("triangle").style.borderTop = "0px";
  }
  //selectで時別を選択した場合
  else if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "時別")
  {
    var value = new Date().getFullYear() + "/" + ("0" + (new Date().getMonth() + 1)).slice(-2) + "/01";
    document.getElementById("monthlyForm").style.display="none";
    document.getElementById("daylyForm").style.display="none";
    document.getElementById("hourlyForm").style.display="";
    document.getElementById("hourlyForm").value = '選択してください';
    document.getElementById("hourlyForm").options = value;
    document.getElementById("triangle").style.borderTop = "6px solid";
  }
}

function timeChangeForOperator()　{
  var chosenDateFormat = document.forms.StatisticsForOperatorForm.dateFormat;

  //selectで月別を選択した場合
  if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "月別")
  {
    document.getElementById("monthlyForm").style.display="";
    document.getElementById("daylyForm").style.display="none";
    document.getElementById("hourlyForm").style.display="none";
    document.getElementById("monthlyForm").value = "";
    document.getElementById("triangle").style.borderTop = "0px";
  }
  //selectで日別を選択した場合
  else if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "日別")
  {
    document.getElementById("monthlyForm").style.display="none";
    document.getElementById("daylyForm").style.display="";
    document.getElementById("hourlyForm").style.display="none";
    document.getElementById("hourlyForm").value = "";
    document.getElementById("triangle").style.borderTop = "0px";
  }
  //selectで時別を選択した場合
  else if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "時別")
  {
    var value = new Date().getFullYear() + "/" + ("0" + (new Date().getMonth() + 1)).slice(-2) + "/01";
    document.getElementById("monthlyForm").style.display="none";
    document.getElementById("daylyForm").style.display="none";
    document.getElementById("hourlyForm").style.display="";
    document.getElementById("hourlyForm").value = '選択してください';
    document.getElementById("hourlyForm").options = value;
    document.getElementById("triangle").style.borderTop = "6px solid";
  }
}

$(window).load(function(){

  $.extend( $.fn.dataTable.defaults, {
    language: { url: "/lib/datatables/Japanese.JSON" }
  });

  var tableObj = $("#statistics_table").DataTable({
    searching: false,
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

  //リサイズ処理
  var resizeDataTable = function() {
    $('.dataTables_scrollBody').css('max-height',$('#statistics_content').outerHeight() - 120 + 'px');
  }
  // ページ読み込み時にもリサイズ処理を実行
  tableObj.on( 'draw', function () {
    resizeDataTable();
    $('#statistics_content').css('visibility','visible');
  } );

  $(window).on('resize', function(event){
    console.log("resize");
    resizeDataTable();
  });

  //CSV処理(チャット統計)
  if(document.getElementById('outputCSV') != null) {
    var outputCSVBtn = document.getElementById('outputCSV');
    outputCSVBtn.addEventListener('click', function(){
      var dateFormat = $("select[name=dateFormat]").val();
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
  }
  //CSV処理(オペレータ統計一覧画面)
  else if(document.getElementById('outputOperatorCSV') != null) {
    var outputOperatorCSVBtn = document.getElementById('outputOperatorCSV');
    outputOperatorCSVBtn.addEventListener('click', function(){
      var dateFormat = $("select[name=dateFormat]").val();
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
      document.getElementById('statisticsForChatForm').action = '<?=$this->Html->url(["controller"=>"Statistics", "action" => "outputOperatorCsv","List"])?>';
      console.log(document.getElementById('statisticsForChatForm').action);
      document.getElementById('statisticsForChatForm').submit();
    });
  }

  //CSV処理(オペレータ統計別ウィンドウ各項目画面)
  else if(document.getElementById('outputEachItemOperatorCSV') != null) {
    var outputEachItemOperatorCSVBtn = document.getElementById('outputEachItemOperatorCSV');
    outputEachItemOperatorCSVBtn.addEventListener('click', function(){
      var item = location.search.match(/item=(.*?)(&|$)/)[1];
      var dateFormat = location.search.match(/type=(.*?)(&|$)/)[1];
      var date = location.search.match(/target=(.*?)(&|$)/)[1];

      document.getElementById('statisticsOutputData').value = JSON.stringify({item:item,dateFormat:dateFormat,date:date});
      document.getElementById('statisticsForChatForm').action = '<?=$this->Html->url(["controller"=>"Statistics", "action" => "outputEachOperatorCsv"])?>';
      document.getElementById('statisticsForChatForm').submit();
    });
    var closeWindowBtn = document.getElementById('closeWindow');
    closeWindowBtn.addEventListener('click', function(){
      window.close();
    });
  }

  //CSV処理(オペレータ統計別ウィンドウ個人画面)
  else if(document.getElementById('outputPrivateOperatorCSV') != null) {
    var outputPrivateOperatorCSVBtn = document.getElementById('outputPrivateOperatorCSV');
    outputPrivateOperatorCSVBtn.addEventListener('click', function(){
      var id = location.search.match(/id=(.*?)(&|$)/)[1];
      var dateFormat = location.search.match(/type=(.*?)(&|$)/)[1];
      var date = location.search.match(/target=(.*?)(&|$)/)[1];

      document.getElementById('statisticsOutputData').value = JSON.stringify({id:id,dateFormat:dateFormat,date:date});
      document.getElementById('statisticsForChatForm').action = '<?=$this->Html->url(["controller"=>"Statistics", "action" => "outputEachOperatorCsv"])?>';
      document.getElementById('statisticsForChatForm').submit();
    });
    var closeWindowBtn = document.getElementById('closeWindow');
    closeWindowBtn.addEventListener('click', function(){
      window.close();
    });
  }

  var timeType = {
    monthly: '月別',
    dayly: '日別',
    timely: '時別'
  }

  //月別で検索した場合
  if('<?= $date ?>' == '月別'){
    document.getElementById("monthlyForm").style.display="";
    document.getElementById("triangle").style.borderTop = "0px";
  }
  //日別で検索した場合
  if('<?= $date ?>' == '日別'){
    document.getElementById("daylyForm").style.display="";
    document.getElementById("triangle").style.borderTop = "0px";
  }
  //時別で検索した場合
  if('<?= $date ?>' == '時別'){
    document.getElementById("hourlyForm").style.display="";
    document.getElementById("triangle").style.borderTop = "6px solid";
  }

  //月別の年を選択
  $("#monthlyForm").change(function(){
    var monthlyForm = $("#monthlyForm").val();
    if(monthlyForm != '') {
      loading.load.start();
      var dateFormat = $("select[name=dateFormat]").val();

      if(dateFormat == timeType.monthly) {
        // Safariでローディングのイメージが表示されない問題の解決方法としてsetTimeoutを挿入
        // @see https://stackoverflow.com/questions/28586393/safari-not-updating-ui-during-form-submission
        if(document.getElementById("StatisticsForChatForm") != null) {

          setTimeout(function(){
            document.getElementById('StatisticsForChatForm').submit();
          },0);
        }
        else if(document.getElementById("StatisticsForOperatorForm") != null) {
          setTimeout(function(){
            document.getElementById('StatisticsForOperatorForm').submit();
          },0);
        }
      }
    }
  });

  //日別の月を選択
  $("#daylyForm").change(function(){
    var daylyForm = $("#daylyForm").val();
    if(daylyForm != '') {
      loading.load.start();
      var dateFormat = $("select[name=dateFormat]").val();

      if(dateFormat == timeType.dayly) {
        // Safariでローディングのイメージが表示されない問題の解決方法としてsetTimeoutを挿入
        // @see https://stackoverflow.com/questions/28586393/safari-not-updating-ui-during-form-submission
        if(document.getElementById("StatisticsForChatForm") != null) {
          setTimeout(function() {
            document.getElementById('StatisticsForChatForm').submit();
          },0);
        }
        else if(document.getElementById("StatisticsForOperatorForm") != null) {
          setTimeout(function(){
            document.getElementById('StatisticsForOperatorForm').submit();
          },0);
        }
      }
    }
  });

  //datepicke
  $('input[name="datefilter"]').daterangepicker({
    "locale": {
      "format": "YYYY/MM/DD",
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
    minDate: '2017-07-01'
  },
  function(start, end, label) {
    loading.load.start();
    searchInfo = $("select[name=dateFormat]").val();
    $('input[name="datefilter"]').val(start.format('YYYY/MM/DD'));
    if(searchInfo == timeType.timely) {
      // Safariでローディングのイメージが表示されない問題の解決方法としてsetTimeoutを挿入
      // @see https://stackoverflow.com/questions/28586393/safari-not-updating-ui-during-form-submission
      if(document.getElementById("StatisticsForChatForm") != null) {
        setTimeout(function() {
          document.getElementById('StatisticsForChatForm').submit();
        },0);
      }
      else if(document.getElementById("StatisticsForOperatorForm") != null) {
        setTimeout(function(){
          document.getElementById('StatisticsForOperatorForm').submit();
        },0);
      }
    }
  });

  // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','block');
    if ( parentTdId.match(/op/)) {
      targetObj.css({
        top: ($(this).offset().top - targetObj.find('ul').outerHeight() -35) + 'px',
        left: '50px'
      });
    }
    else {
      targetObj.css({
        top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 65) + 'px',
        left: '50px'
      });
    }
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });

  // ツールチップの表示制御(オペレータ統計画面)
  $('.opQuestionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','block');
    console.log(parentTdId);
    if( parentTdId == 'opChatResponseLabel') {
      targetObj.css({
        left: ($(this).offset().left - 170) + 'px'
      });
    }
    else if ( parentTdId.match(/op/)) {
      console.log($(this).offset().left);
      targetObj.css({
        left: ($(this).offset().left - 207) + 'px'
      });
    }
  });

  $('.opQuestionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
    console.log($(this).offset().left);
  });

  // DataTablesの検索時にツールチップを非表示にする
  tableObj.on('search',function(event){
    $('icon-annotation').css('display', 'none');
  });

  $(document).on({
    mouseenter: function () {
      trIndex = $(this).index();
      if($(this).attr('class') == 'odd' || $(this).attr('class') == 'even') {
        $("table.dataTable tbody").each(function(index) {
          $(this).find("tr:eq("+trIndex+")").addClass("highlight")
        });
      }
    },
    mouseleave: function () {
      trIndex = $(this).index();
      $("table.dataTable tbody").each(function(index) {
        $(this).find("tr:eq("+trIndex+")").removeClass("highlight")
      });
    }
  }, ".dataTables_wrapper tr");
});
</script>
