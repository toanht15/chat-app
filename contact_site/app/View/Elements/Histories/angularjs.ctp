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

</script>
