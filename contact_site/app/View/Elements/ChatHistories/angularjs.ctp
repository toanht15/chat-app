<script type="text/javascript">
'use strict';
  var historySearchConditions = <?php echo json_encode($data);?>;
  var mCustomerInfoList = <?php echo json_encode($mCustomerList);?>;
  var sincloApp = angular.module('sincloApp', ['ngSanitize']);
  sincloApp.controller('MainController', function($scope) {
    console.log('まずチェック！');
    console.log($scope);
    console.log($scope.fillterTypeId);

    $scope.ua = function(str){
      return userAgentChk.pre(str);
    };

    $(document).ready(function(){
      console.log('まず入ってるか確認しましょう！');
      $scope.messageList = [];
      $.ajax({
        type: "GET",
        url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'remoteGetOldChat'])?>",
        data: {
          historyId: '<?=$historyList[0]['THistory']['id']?>'
        },
        dataType: "json",
        success: function(json){
          console.log('jsonData');
          console.log(json);
          //if ( oldFlg ) { // 過去チャットの場合
            angular.element("message-list-descript").attr("class", "off");
            $scope.messageList = json;
            $scope.$apply();
          }
        });
      //}
      /*else {
        className = "currentChat";
      }
      $("#showChatTab > li[data-type='" + className + "']").addClass("on");
      $("#chatContent > section").removeClass("on");
      $("#chatContent > #" + className).addClass("on");*/
    });

    // 過去チャットと現行チャット
    $(document).on("click", "#showChatTab > li", function(e){
      var className = $(this).data('type');
      angular.element("#showChatTab > li").removeClass("on");

      if ( className === "oldChat" ) {
        $scope.chatLogList = [];
        $scope.chatLogMessageList = [];
        angular.element("message-list-descript").attr("class", "off");
        $.ajax({
          type: 'GET',
          url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteGetChatList')) ?>",
          cache: false,
          data: {
            userId: $('#visitorsId').text()
          },
          dataType: 'json',
          success: function(json){
            $scope.chatLogList = json;
            angular.element("message-list-descript").attr("class", "on");
            $scope.$apply();
          }
        });
      }
      else {
        className = "currentChat";
      }
      $("#showChatTab > li[data-type='" + className + "']").addClass("on");
      $("#chatContent > section").removeClass("on");
      $("#chatContent > #" + className).addClass("on");
    });

    // 顧客の詳細情報を取得する
    $scope.getOldChat = function(historyId, oldFlg){
      console.log('履歴変更！');
      console.log(historyId);
      console.log(oldFlg);
      //console.log(hisotryId);
      //console.log(oldFlg);
      $scope.chatLogMessageList = [];
      $.ajax({
        type: "GET",
        url: "<?=$this->Html->url(['controller'=>'Customers', 'action' => 'remoteGetOldChat'])?>",
        data: {
          historyId:  historyId
        },
        dataType: "json",
        success: function(json){
          console.log('jsonData');
          console.log(json);
          if ( oldFlg ) { // 過去チャットの場合
            angular.element("message-list-descript").attr("class", "off");
            $scope.chatLogMessageList = json;
            $scope.$apply();
          }
          else {
            $scope.messageList = json;

            $scope.chatLogList = [];
            $scope.chatLogMessageList = [];
            angular.element("message-list-descript").attr("class", "off");
            $.ajax({
              type: 'GET',
              url: "<?= $this->Html->url(array('controller' => 'Customers', 'action' => 'remoteGetChatList')) ?>",
              cache: false,
              data: {
                userId: $('#visitorsId').text()
              },
              dataType: 'json',
              success: function(json){
                $scope.chatLogList = json;
                angular.element("message-list-descript").attr("class", "on");
                $scope.$apply();
              }
            });
          }
        }
      });
    };

    $scope.ip = function(ip, issetCompanyName){
      var showData = [];
      if(issetCompanyName) {
        showData.push('(' + ip + ')'); // IPアドレス
      } else {
        showData.push(ip); // IPアドレス
      }
      return showData.join("\n");
    };

        // 【チャット】テキストの構築
    $scope.createTextOfMessage = function(chat, message, opt) {
        var strings = message.split('\n');
        var custom = "";
        var linkReg = RegExp(/http(s)?:\/\/[!-~.a-z]*/);
        var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
        var radioName = "sinclo-radio" + Object.keys(chat).length;
        var option = ( typeof(opt) !== 'object' ) ? { radio: true } : opt;
        for (var i = 0; strings.length > i; i++) {
            var str = escape_html(strings[i]);
            // ラジオボタン
            var radio = str.indexOf('[]');
            if ( option.radio && radio > -1 ) {
                var val = str.slice(radio+2);
                str = "<input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + val + "' disabled=''>";
                str += "<label class='pointer' for='" + radioName + "-" + i + "'>" + val + "</label>";
            }
            // リンク
            var link = str.match(linkReg);
            if ( link !== null ) {
                var url = link[0];
                var a = "<a href='" + url + "' target='_blank'>"  + url + "</a>";
                str = str.replace(url, a);
            }
            // 電話番号（スマホのみリンク化）
            var tel = str.match(telnoTagReg);
            if( tel !== null ) {
              var telno = tel[1];
              // ただの文字列にする
              var span = "<span class='telno'>" + telno + "</span>";
              str = str.replace(tel[0], span);
            }
            custom += str + "\n";

        }
        return custom;
      };

    // 【チャット】チャット枠の構築
    $scope.createMessage = function(elem, chat){
    var chatApi = {
      connect: false,
      tabId: null,
      sincloSessionId: null,
      userId: null,
      token: null,
      getMessageToken: null,
      messageType: {
        customer: 1,
        company: 2,
        auto: 3,
        sorry: 4,
        autoSpeech: 5,
        start: 98,
        end: 99,
      }
    }
      console.log('ここで作ってるよ！');
      var cn = "";
      var div = document.createElement('div');
      var li = document.createElement('li');
      var content = "";

      var type = Number(chat.messageType);
      var message = chat.message;
      var userId = Number(chat.userId);
      // 消費者からのメッセージの場合
      if ( type === chatApi.messageType.customer) {
        cn = "sinclo_re";
        div.style.textAlign = 'left';
        div.style.height = 'auto';
        div.style.padding = '0';
        li.className = cn;
        content = $scope.createTextOfMessage(chat, message, {radio: false});
      }
      // オートメッセージの場合
      else if ( type === chatApi.messageType.company) {
        cn = "sinclo_se";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        var chatName = widget.subTitle;
        if ( Number(widget.showName) === <?=C_WIDGET_SHOW_NAME?> ) {
          chatName = userList[Number(userId)];
        }
        content = "<span class='cName'>" + chatName + "</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.auto || type === chatApi.messageType.sorry) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>自動応答</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else if ( type === chatApi.messageType.autoSpeech ) {
        cn = "sinclo_auto";
        div.style.textAlign = 'right';
        div.style.height = 'auto';
        div.style.padding = '0';
        content = "<span class='cName'>自動返信</span>";
        content += $scope.createTextOfMessage(chat, message);
      }
      else  {
        cn = "sinclo_etc";
        var userName = "オペレーター";
        if ( Number(widget.showName) === <?=C_WIDGET_SHOW_NAME?> && userList.hasOwnProperty(Number(userId)) ) {
          userName = userList[Number(userId)];
        }
        if ( type === chatApi.messageType.start ) {
          content = "－　" + userName + "が入室しました　－";
        }
        if ( type === chatApi.messageType.end ) {
          content = "－　" + userName + "が退室しました　－";
        }
      }
      li.className = cn;
      li.innerHTML = content;
      div.appendChild(li);
      $(elem).append(div);
    };

    $scope.ui = function(ip, id){
      var showData = [];

      if ( mCustomerInfoList.hasOwnProperty(id) && mCustomerInfoList[id] !== "" && mCustomerInfoList[id] != null && mCustomerInfoList[id] !== undefined ) {
        var c = JSON.parse(mCustomerInfoList[id]);
        if ( ('company' in c) && c.company.length > 0 ) {
          showData.push(c.company); // 会社名
        }
        if ( ('name' in c) && c.name.length > 0 ) {
          showData.push(c.name); // 名前
        }
      }
      return showData.join("\n");
    };

  <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
    angular.element('label[for="g_chat"]').on('change', function(e){
      var url = "<?=$this->Html->url(['controller' => 'ChatHistories', 'action'=>'index'])?>?isChat=" + e.target.checked;
      location.href = url;
    });
  <?php endif; ?>

      /* パラメーターを取り除く */
      var targetParams = <?php echo json_encode(array_flip($excludeList['params']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;
      $scope.trimToURL = function (url,type){
        if ( typeof(url) !== 'string' ) return "";
        //表示するURLの場合
        if(type == 2) {
          //メッセージが30文字以上の場合3点リーダー表示
          if(url.length > 30) {
            url = url.substr(0,30)　+ '...';
          }
        };
        return trimToURL(targetParams, url);
      };
  });


  sincloApp.directive('ngCreateMessage', [function(){
    return {
      restrict: 'E',
      link: function(scope, elem, attr) {
        scope.createMessage(elem, scope.chat);

      }
    };
  }]);

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

  <?php if(isset($coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]): ?>
  window.openCompanyDetailInfo = function(lbc){
    var retList = {};
    $.ajax({
      type: 'POST',
      cache: false,
      url: "<?= $this->Html->url(array('controller' => 'CompanyData', 'action' => 'getDetailInfo')) ?>",
      data: JSON.stringify({
        accessToken: "<?=$token?>",
        lbc: lbc,
        format: 'popupElement'
      }),
      dataType: 'html',
      success: function(html){
        modalOpen.call(window, html, 'p-cus-company-detail', '企業詳細情報');
      }
    });
  };
  <?php endif; ?>

  <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
    window.openChatById = function(id){
      $.ajax({
        type: 'GET',
        url: "<?= $this->Html->url(array('controller' => 'ChatHistories', 'action' => 'remoteGetChatLogs')) ?>",
        cache: false,
        data: {
          historyId: id
        },
        dataType: 'html',
        success: function(html){
          //modalOpen.call(window, html, 'p-chat-logs', 'チャット履歴');
          //$(".p-chat-logs #popup-main ul").scrollTop(0);
          console.log('成功');
          console.log(html);
        }
      });
    };
  <?php endif; ?>

}());

$(document).ready(function(){

  // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 70) + 'px',
      left: $(this).offset().left - 65 + 'px'
    });
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });

  var outputCSVBtn = document.getElementById('outputCSV');
  outputCSVBtn.addEventListener('click', function(){
    if($(outputCSVBtn).hasClass('disabled')) return false;
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
    if($(outputChatCSVBtn).hasClass('disabled')) return false;
    var thead = document.querySelector('#history_list thead');
    var tbody = document.querySelector('#history_list tbody');
    var data = [];
    // CSVに不要な列が追加されたら空をセット
    var label = ["date","","ip","useragent","campaign","sourcePage","pageCnt","visitTime","status","","user"];
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
    document.getElementById('HistoryIndexForm').action = '<?=$this->Html->url(["controller"=>"Histories", "action" => "outputCSVOfChatHistory"])?>';
    document.getElementById('HistoryIndexForm').submit();
  });

<?php endif; ?>
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
    "startDate": historySearchConditions.History.start_day,
    "endDate": historySearchConditions.History.finish_day,
    "opens": "left"
  });

  //キャンセルボタン
  $('.cancelBtn').on('click', function() {
    $('#mainDatePeriod').html(historySearchConditions.History.period + ' : ' + historySearchConditions.History.start_day + '-' + historySearchConditions.History.finish_day);
  });

  //検索期間欄をクリックした場合
  $('#mainDatePeriod').on('click', function() {
    $('#mainDatePeriod').html(historySearchConditions.History.period + ' : ' + historySearchConditions.History.start_day + '-' + historySearchConditions.History.finish_day);
  });

  var prevBoldTarget = null;
  $('.underL.showBold').on('click', function(e){
    $(this).parents('tr').find('td').each(function(index){
      $(this).css("font-weight", "bold");
    });
    if(prevBoldTarget) {
      prevBoldTarget.parents('tr').find('td').each(function(index){
        $(this).css("font-weight", "normal");
      });
    }
    prevBoldTarget = $(this);
  });

  //検索ボタン
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
    var allDay = historySearchConditions.History.company_start_day;

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
     else if(startDay  == allDay && endDay == today){
       search_day  = "全期間";
     }
     //カスタム
     else {
       search_day  = "カスタム";
     }
    historySearchConditions.History.start_day = $("input[name=daterangepicker_start]").val();
    historySearchConditions.History.finish_day = $("input[name=daterangepicker_end]").val();
    historySearchConditions.History.period = search_day;

    $.ajax({
      type: 'post',
      dataType: 'html',
      data:historySearchConditions,
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>",
      success: function(html){
        location.href ="<?= $this->Html->url(['controller' => 'Histories', 'action' => 'index']) ?>";
      }
    });
  });
});

</script>
