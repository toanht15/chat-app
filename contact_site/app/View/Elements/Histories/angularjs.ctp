<script type="text/javascript">
'use strict';
  var historySearchConditions = <?php echo json_encode($historySearchConditions);?>;
  var thistoryChatLogSearchConditions = <?php echo json_encode($thistoryChatLogSearchConditions);?>;

  var sincloApp = angular.module('sincloApp', ['ngSanitize']);
  sincloApp.controller('MainCtrl', function($scope) {
    $scope.ua = function(str){
      return userAgentChk.pre(str);
    };

    $scope.ui = function(ip, customerList){
      var showData = [];

      if ( customerList !== "" && customerList != null && customerList !== undefined ) {
        var c = JSON.parse(customerList);
        if ( ('company' in c) && c.company.length > 0 ) {
          showData.push(c.company); // 会社名
        }
        if ( ('name' in c) && c.name.length > 0 ) {
          showData.push(c.name); // 名前
        }
      }
      showData.push(ip); // IPアドレス
      return showData.join("\n");
    };

  <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
    angular.element('label[for="g_chat"]').on('change', function(e){
      var url = "<?=$this->Html->url(['controller' => 'Histories', 'action'=>'index'])?>?isChat=" + e.target.checked;
      location.href = url;
    });
  <?php endif; ?>

      /* パラメーターを取り除く */
      var targetParams = <?php echo json_encode(array_flip($excludeList['params']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
      $scope.trimToURL = function (url){
        if ( typeof(url) !== 'string' ) return "";
        return trimToURL(targetParams, url);
      };
  });

  sincloApp.directive('ngShowDetail', function(){
    return {
      restrict: 'E',
      scope: {
        visitorId: '='
      },
      template: '<a href="javascript:void(0)" ng-click="showDetail(historyId)" class="detailBtn blueBtn btn-shadow">詳細</a>',
      link: function(scope, elem, attr) {
        scope.historyId = attr['id'];
        scope.showDetail = function(id){
          $.ajax({
            type: 'GET',
            url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetCustomerInfo')) ?>",
            data: {
              historyId: id
            },
            dataType: 'html',
            success: function(html){
              modalOpen.call(window, html, 'p-history-cus', '顧客情報');
            }
          });
        };
      }
    }
  });

(function(){

  window.openHistoryById = function(id){
    $.ajax({
      type: 'GET',
      url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetStayLogs')) ?>",
      data: {
        historyId: id
      },
      dataType: 'html',
      success: function(html){
        modalOpen.call(window, html, 'p-history-logs', 'ページ移動履歴');
      }
    });
  };

  <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
    window.openChatById = function(id){
      $.ajax({
        type: 'GET',
        url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteGetChatLogs')) ?>",
        cache: false,
        data: {
          historyId: id
        },
        dataType: 'html',
        success: function(html){
          modalOpen.call(window, html, 'p-chat-logs', 'チャット履歴');
        }
      });
    };
  <?php endif; ?>

}());

$(document).ready(function(){
  var outputCSVBtn = document.getElementById('outputCSV');
  outputCSVBtn.addEventListener('click', function(){
    var thead = document.querySelector('#history_list thead');
    var tbody = document.querySelector('#history_list tbody');
    var data = [];
    // CSVに不要な列が追加されたら空をセット
<?php if ($coreSettings[C_COMPANY_USE_CHAT]) { ?>
    var label = ["date","","ip","useragent","campaign","referrer","pageCnt","visitTime","achievement","status", "user"];
<?php } else { ?>
    var label = ["date","","ip","useragent","campaign","referrer","pageCnt","visitTime"];
<?php } ?>

    var noCsvData = {};

    for (var a = 0; a < thead.children[0].children.length; a++) {
      var th = thead.children[0].children[a];
      if ( th.className.match(/noOutCsv/) !== null ) {
        noCsvData[a] = "";
      }
    }

    for(var i = 0; i < tbody.children.length; i++){
      var tr = tbody.children[i];
      var tdList = tr.children;
      var row = {};
      for(var u = 0; u < tdList.length; u++){
        if (!(u in noCsvData)) {
                  var td = tdList[u];
                  if ( td.children.length === 0 ) {
                    row[label[u]] = td.textContent;
                  }
                  else {
                    row[label[u]] = td.children[0].textContent;
                  }
                  if ( u === (label.length - 1) ) {
                    data.push(row);
                  }

        }
      }
    }
    document.getElementById('HistoryOutputData').value = JSON.stringify(data);
    document.getElementById('HistoryIndexForm').action = '<?=$this->Html->url(["controller"=>"Histories", "action" => "outputCSVOfHistory"])?>';
    document.getElementById('HistoryIndexForm').submit();
  });

<?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>

  var outputChatCSVBtn = document.getElementById('outputChat');
  outputChatCSVBtn.addEventListener('click', function(){
    var thead = document.querySelector('#history_list thead');
    var tbody = document.querySelector('#history_list tbody');
    var data = [];
    // CSVに不要な列が追加されたら空をセット
    var label = ["date","","ip","useragent","campaign","referrer","pageCnt","visitTime","status","","user"];
    var noCsvData = {};

    for (var a = 0; a < thead.children[0].children.length; a++) {
      var th = thead.children[0].children[a];
      if ( th.className.match(/noOutCsv/) !== null ) {
        noCsvData[a] = "";
      }
    }

    for(var i = 0; i < tbody.children.length; i++){
      var tr = tbody.children[i];
      var tdList = tr.children;
      var row = {};
      for(var u = 0; u < tdList.length; u++){
        var td = tdList[u];
        if (!(u in noCsvData)) {
          if ( td.children.length === 0 ) {
            row[label[u]] = td.textContent;
          }
          else {
            row[label[u]] = td.children[0].textContent;
          }
          if ( u === (label.length - 1) ) {
            data.push(row);
          }
        }
        else {
          var id = $(td.children[0]).data('id');
          if ( id !== null && id !== undefined ) {
            row['id'] = id;
          }
        }
      }
    }
    document.getElementById('HistoryOutputData').value = JSON.stringify(data);
    document.getElementById('HistoryIndexForm').action = '<?=$this->Html->url(["controller"=>"Histories", "action" => "outputCSVOfContents"])?>';
    document.getElementById('HistoryIndexForm').submit();
  });

<?php endif; ?>

  onload = function(){
    $('#mainDatePeriod').daterangepicker({
      "ranges": {
        '今日': [moment(), moment()],
        '昨日': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        '過去一週間': [moment().subtract(6, 'days'), moment()],
        '過去一ヶ月間': [moment().subtract(30, 'days'), moment()],
        '今月': [moment().startOf('month'), moment().endOf('month')],
        '先月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        '全期間': [historySearchConditions.company_start_day, moment()]
      },
      "locale": {
        "format": "YYYY/MM/DD",
        "separator": " - ",
        "applyLabel": "検索",
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
      "startDate": historySearchConditions.start_day,
      "endDate": historySearchConditions.finish_day,
      "opens": "left"
    });

    //閉じるボタン
    $('.cancelBtn').on('click', function() {
      $('#mainDatePeriod').html(historySearchConditions.period + ' : ' + historySearchConditions.start_day + '-' + historySearchConditions.finish_day);
    });

    //設定ボタン
    $('#mainDatePeriod').on('apply.daterangepicker', function(ev, picker) {
      var search_day  = $('.active').val();
      //開始日
      var startDay =  $("input[name=daterangepicker_start]").val();
      //終了日
      var endDay = $("input[name=daterangepicker_end]").val();
      //今日
      var today = moment();
      today = today.format("YYYY/MM/DD");
      //昨日
      var yesterday = moment().subtract(1, 'days');
      yesterday = yesterday.format("YYYY/MM/DD");
      //過去一週間
      var oneWeekAgo = moment().subtract(6, 'days');
      oneWeekAgo = oneWeekAgo.format("YYYY/MM/DD");
      //過去一か月間
      var oneMonthAgo = moment().subtract(30, 'days');
      oneMonthAgo = oneMonthAgo.format("YYYY/MM/DD");
      //過去一ヵ月間
      var thisMonth = moment().startOf('month');
      thisMonth = thisMonth.format("YYYY/MM/DD");
      //今月の初め
      var thisMonthStart = moment().startOf('month');
      thisMonthStart = thisMonthStart.format("YYYY/MM/DD");
      //今月の終わり
      var thisMonthEnd = moment().endOf('month');
      thisMonthEnd = thisMonthEnd.format("YYYY/MM/DD");
      //先月の初め
      var lastMonthStart = moment().subtract(1, 'month').startOf('month');
      lastMonthStart = lastMonthStart.format("YYYY/MM/DD");
      //先月の終わり
      var lastMonthEnd = moment().subtract(1, 'month').endOf('month');
      lastMonthEnd = lastMonthEnd.format("YYYY/MM/DD");
      //全期間
      var allDay = historySearchConditions.company_start_day;

      //今日
      if(startDay  == today && endDay == today){
         search_day  = "今日";
       }
       //昨日
       else if(startDay  == yesterday && endDay == yesterday){
         search_day  = "昨日";
       }
       //過去一週間
       else if(startDay  == oneWeekAgo && endDay == today){
         search_day  = "過去一週間";
       }
       //過去一か月間
       else if(startDay  == oneMonthAgo && endDay == today){
         search_day  = "過去一ヵ月間";
       }
       //今月
       else if(startDay  == thisMonthStart && endDay == thisMonthEnd){
         search_day  = "今月";
       }
       //先月
       else if(startDay  == lastMonthStart && endDay == lastMonthEnd ){
         search_day  = "先月";
       }
       //全期間
       else if(startDay  == allDay){
         search_day  = "全期間";
       }
       //カスタム
       else {
         search_day  = "カスタム";
       }

      historySearchConditions.start_day = $("input[name=daterangepicker_start]").val();
      historySearchConditions.finish_day = $("input[name=daterangepicker_end]").val();
      historySearchConditions.period = search_day;

      $.ajax({
        type: 'post',
        dataType: 'html',
        data:{
          History:historySearchConditions,
          THistoryChatLog:thistoryChatLogSearchConditions
        },
        cache: false,
        url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'searchConditionsRecord']) ?>",
        success: function(html){
          location.href = location.href;
        }
      });
    });
  }
});

</script>
