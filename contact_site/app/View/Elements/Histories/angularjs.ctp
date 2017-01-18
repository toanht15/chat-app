<script type="text/javascript">
'use strict';

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
    var label = ["date","","ip","useragent","campaign","referrer","pageCnt","visitTime","status", "user"];
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

  var outputChatCSVBtn = document.getElementById('outputChat');
  outputChatCSVBtn.addEventListener('click', function(){
    var thead = document.querySelector('#history_list thead');
    var tbody = document.querySelector('#history_list tbody');
    var data = [];
    // CSVに不要な列が追加されたら空をセット
    var label = ["date","","ip","useragent","campaign","referrer","pageCnt","visitTime","status", "user"];
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
    document.getElementById('HistoryIndexForm').action = '<?=$this->Html->url(["controller"=>"Histories", "action" => "outputCSVOfContents"])?>';
    document.getElementById('HistoryIndexForm').submit();
  });

  $('#dateperiod').daterangepicker({
    "ranges": {
      '今日': [moment(), moment()],
      '昨日': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      '過去一週間': [moment().subtract(6, 'days'), moment()],
      '過去一ヶ月間': [moment().subtract(29, 'days'), moment()],
      '今月': [moment().startOf('month'), moment().endOf('month')],
      '先月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "locale": {
      "format": "YYYY/MM/DD",
      "separator": " - ",
      "applyLabel": "設定",
      "cancelLabel": "閉じる",
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
    "startDate": $('#HistoryStartDay').val(),
    "endDate": $('#HistoryFinishDay').val(),
    "opens": "left"
  });

    $('#mainDatePeriod').daterangepicker({
    "ranges": {
      '今日': [moment(), moment()],
      '昨日': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      '過去一週間': [moment().subtract(6, 'days'), moment()],
      '過去一ヶ月間': [moment().subtract(29, 'days'), moment()],
      '今月': [moment().startOf('month'), moment().endOf('month')],
      '先月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "locale": {
      "format": "YYYY/MM/DD",
      "separator": " - ",
      "applyLabel": "検索",
      "cancelLabel": "閉じる",
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
    "startDate": $('#HistoryStartDay').val(),
    "endDate": $('#HistoryFinishDay').val(),
  });

<<<<<<< HEAD
  //モーダルのカレンダーの設定ボタン
=======
>>>>>>> origin/develop
  $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
    $('#HistoryStartDay').val(picker.startDate.format('YYYY/MM/DD'));
    $('#HistoryFinishDay').val(picker.endDate.format('YYYY/MM/DD'));
  });

  //view側のカレンダーの検索ボタン
  $('#mainDatePeriod').on('apply.daterangepicker', function(ev, picker) {
    //開始日と終了日取得
    $('#startDay').text(picker.startDate.format('YYYY/MM/DD'));
    $('#finishDay').text(picker.endDate.format('YYYY/MM/DD'));
    //モーダルの検索ボタンと被らないようにする
    if ( !$("#popup.popup-on #popup-frame ").is(".p-thistory-entry") ) {
      //form作成
      $('<form/>', {action: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>", method: 'post'})
      .append($('<input/>', {type: 'hidden', name: "data[datefilter]", value: $('#startDay').text()+ '-' +$('#finishDay').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][start_day]", value: $('#startDay').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][finish_day]", value: $('#finishDay').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][ip_address]", value:  $('#ip').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][company_name]", value: $('#company').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][customer_name]", value: $('#customer').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][telephone_number]", value: $('#telephone').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][mail_address]", value: $('#mail').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][responsible_name]", value: $('#responsible').text()}))
      .append($('<input/>', {type: 'hidden', name: "data[History][message]", value: $('#message').text()}))
      .appendTo(document.body)
      .submit()
    }
  });

  $('#day_search').on('click', function() {
    if ($(this).prop('checked')) {
      $("#dateperiod").prop("disabled", false);
      var d = new Date($('#dateperiod').data('daterangepicker').startDate);
      var startDate = d.getFullYear() + '/' + (d.getMonth() + 1) + '/' + d.getDate();
      var d2 = new Date($('#dateperiod').data('daterangepicker').endDate);
      var endDate = d2.getFullYear() + '/' + (d2.getMonth() + 1) + '/' + d2.getDate();
      $('#HistoryStartDay').val(startDate);
      $('#HistoryFinishDay').val(endDate);
      $("#dateperiod").removeClass('extinguish');
    }
    else {
      $("#dateperiod").prop("disabled", true);
      $('#HistoryStartDay').val("");
      $('#HistoryFinishDay').val("");
      $("#dateperiod").addClass('extinguish');
    }
  });
});

</script>
