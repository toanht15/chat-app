<script type="text/javascript">
<?= $this->element('TDocuments/loadScreen'); ?>

function timeChange()　{
  var chosenDateFormat = document.forms.THistoryForChatForm.dateFormat;

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
    language: { url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" }
  });

  var tableObj = $("#statistics_table").DataTable({
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

  //CSV処理
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
        document.getElementById('THistoryForChatForm').submit();
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
          document.getElementById('THistoryForChatForm').submit();
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
    if(searchInfo == timeType.timely){
      document.getElementById('THistoryForChatForm').submit();
    }
  });

  // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 65) + 'px',
      left: '50px'
    });
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });

  // DataTablesの検索時にツールチップを非表示にする
  tableObj.on('search',function(event){
    $('icon-annotation').css('display', 'none');
  });

  $(document).on({
    mouseenter: function () {
      trIndex = $(this).index()+1;
      $("table.dataTable").each(function(index) {
        $(this).find("tr:eq("+trIndex+")").addClass("highlight")
      });
    },
    mouseleave: function () {
      trIndex = $(this).index()+1;
      $("table.dataTable").each(function(index) {
        $(this).find("tr:eq("+trIndex+")").removeClass("highlight")
      });
    }
  }, ".dataTables_wrapper tr");
});
</script>
