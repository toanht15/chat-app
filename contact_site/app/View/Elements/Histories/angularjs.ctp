<script type="text/javascript">
'use strict';

    var sincloApp = angular.module('sincloApp', ['ngSanitize']);
    sincloApp.controller('MainCtrl', function($scope) {
        $scope.ua = function(str){
            return userAgentChk.init(str);
        };
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
            },
            error: function(){
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
                },
                error: function(){
                }
            });
        };

        document.body.onload = function(){
            var listToggle = document.getElementById('g_chat');
            var listToggleLabel = document.querySelector('label[for="g_chat"]');
            listToggle.addEventListener("change", function(e){
                var url = "<?=$this->Html->url(['controller' => 'Histories', 'action'=>'index'])?>?isChat=" + e.target.checked;
                location.href = url;
            });
        };
    <?php endif; ?>
}());



</script>
