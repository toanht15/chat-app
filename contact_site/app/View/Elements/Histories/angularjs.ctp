<script type="text/javascript">
'use strict';

  var sincloApp = angular.module('sincloApp', ['ngSanitize']);
  sincloApp.controller('MainCtrl', function($scope) {
    $scope.ua = function(str){
      return userAgentChk.pre(str);
    };

  <?php if ($coreSettings[C_COMPANY_USE_CHAT]) : ?>
    angular.element('label[for="g_chat"]').on('change', function(e){
      var url = "<?=$this->Html->url(['controller' => 'Histories', 'action'=>'index'])?>?isChat=" + e.target.checked;
      location.href = url;
    });
  <?php endif; ?>
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
    var tbody = document.querySelector('#history_list tbody');
    var data = [];
    var label = ["date","ip","useragent","referrer","pageCnt","visitTime","status", "user"];

    for(var i = 0; i < tbody.children.length; i++){
      var tr = tbody.children[i];
      var tdList = tr.children;
      var row = {};
      for(var u = 0; u < 8; u++){
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
    document.getElementById('HistoryOutputData').value = JSON.stringify(data);
    document.getElementById('HistoryIndexForm').action = '<?=$this->Html->url(["controller"=>"Histories", "action" => "outputCSVOfHistory"])?>';
    document.getElementById('HistoryIndexForm').submit();
  });

});

</script>
