<script type="text/javascript">
  var getDateTime = function(){
    var now = new Date();
    var year = now.getFullYear(); // 年
    var month = now.getMonth() + 1; // 月
    if(month < 10) { month = "0" + month; }
    var day = now.getDate(); // 日
    if(day < 10) { day = "0" + day; }
    var hour = now.getHours(); // 時
    if(hour < 10) { hour = "0" + hour; }
    var min = now.getMinutes(); // 分
    if(min < 10) { min = "0" + min; }
    var sec = now.getSeconds(); // 秒
    if(sec < 10) { sec = "0" + sec; }
    return data = "" + year + month + day + hour + min + sec;
  };

  var historySearchConditions = <?php echo json_encode($data);?>;

  var dataTrim = function(){
    var data = $('#mainDatePeriod')[0].innerText;
    dataArray = data.split(":")[1].split("-");
    document.getElementById("startDateForm").value = dataArray[0];
    document.getElementById("endDateForm").value = dataArray[1];
  };



  $(function() {
    $('#selectList').change(function () {
      var val = $(this).val();
      var target = $('#outputCSV');
      if(val === "none"){
        target.removeClass("skyBlueBtn");
        target.addClass("grayBtn");
        target.addClass("disabled");
      } else {
        target.addClass("skyBlueBtn");
        target.removeClass("grayBtn");
        target.removeClass("disabled");
      }
    });

    $('#mainDatePeriod').daterangepicker({
      "ranges": {
        '今日': [moment(), moment()],
        '昨日': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        '過去一週間': [moment().subtract(6, 'days'), moment()],
        '過去一ヶ月間': [moment().subtract(30, 'days'), moment()],
        '今月': [moment().startOf('month'), moment().endOf('month')],
        '先月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        '全期間': [historySearchConditions.History.company_start_day, moment()]
      },
      "locale": {
        "format": "YYYY/MM/DD",
        "separator": " - ",
        "applyLabel": "適用",
        "cancelLabel": "キャンセル",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "カスタム",
        "weekLabel": "W",
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
        "firstDay": 1
      },
      "alwaysShowCalendars": true,
      "startDate": historySearchConditions.History.start_day,
      "endDate": historySearchConditions.History.finish_day,
      "opens": "left"
    });

    var CSVbutton = $('#outputCSV');
    CSVbutton.on('click', function(e){
      if(CSVbutton.hasClass("grayBtn")) return;
      window.loading.load.start();
      dataTrim();
      $.ajax({
        type: "POST",
        url: "<?=$this->Html->url([
          'controller' => 'TLeadLists',
          'action' => 'index'
        ])?>",
        data: $("#listForm").serialize(),
        dataType: "binary",
        responseType: "blob"
      })
      .done(function(response, textStatus, jqXHR){
        console.log(response);
        let userAgent = window.navigator.userAgent.toLowerCase();
        let responseHeader = jqXHR.getResponseHeader('Content-Type');
        let extension;
        if(responseHeader.match(/csv/)){
          extension = "csv";
        } else {
          extension = "zip"
        }
        let dateTime = getDateTime();
        let filename = dateTime + "_sinclo-lead-lists." + extension;
        let data = response;

        if(userAgent.indexOf('msie') != -1 || userAgent.indexOf('trident') != -1 || userAgent.indexOf('edge') != -1) {
          window.navigator.msSaveBlob(data, filename);
        } else {
          let link = document.createElement('a');
          let downloadUrl = (window.URL || window.webkitURL).createObjectURL(data);
          link.href = downloadUrl;
          link.download = filename;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          (window.URL || window.webkitURL).revokeObjectURL(downloadUrl);
        }
        window.loading.load.finish();
      });
    });
  });
</script>
