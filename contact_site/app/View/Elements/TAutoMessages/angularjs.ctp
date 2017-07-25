<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate']);

sincloApp.controller('MainController', function($scope) {
    //thisを変数にいれておく
    var self = this;

    var setActivity = <?=( !empty($this->data['TAutoMessage']['activity']) ) ? json_encode($this->data['TAutoMessage']['activity'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : "{}" ?>;
    this.setItemList = {};
    var setItemListTmp = (typeof(setActivity) === "string") ? JSON.parse(setActivity) : setActivity;
    if ( 'conditions' in setItemListTmp ) {
        this.setItemList = setItemListTmp['conditions'];
    }
    this.keys = function(obj){
        if (angular.isObject(obj)) {
            return Object.keys(obj).length;
        }
        else {
            return obj.length;
        }
    };

    this.tmpList = <?php echo json_encode($outMessageTriggerList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;

    $scope.$watch(function(){
      return self.setItemList;
    });

    this.checkDisabled = function(itemId){
        return (itemId in this.setItemList && this.setItemList[itemId].length >= this.tmpList[itemId].createLimit[this.condition_type]);
    };

    this.addItem = function(tmpId){
        if ( tmpId in this.tmpList ) {
            if ( !(tmpId in this.setItemList) ) {
                this.setItemList[tmpId] = [];
            }
            else if (tmpId in this.setItemList && this.setItemList[tmpId].length >= this.tmpList[tmpId].createLimit[this.condition_type]) {
                return false;
            }
            this.setItemList[tmpId].push(angular.copy(this.tmpList[tmpId].default));
        }
    };

    this.openList = function(elm){
        var target = null;
        target = $(String(elm));
        if (!target.is(".selected")) {
            target.css('height', target.children("ng-form").children("div").prop("offsetHeight") + 34 + "px").addClass("selected");
        }
        else {
            target.css('height', "34px").removeClass("selected");
        }
    };

    this.requireCheckBox = function(form){
        if (form === undefined) return false;
        var ret = Object.keys(form).filter(function(k) {
            return form[k] == true;
        })[0];
        return ( ret === undefined || ret.length === 0 );
    };

    this.removeItem = function(itemType, itemId){
        if ( itemType in this.setItemList ) {
            if ( itemId in this.setItemList[itemType] ) {
                if ( Object.keys(this.setItemList[itemType]).length === 1 ) {
                    delete this.setItemList[itemType];
                }
                else {
                    this.setItemList[itemType].splice(itemId, 1);
                }
                angular.bind(this, function() {
                    this.setItemList = self.setItemList;
                    $scope.$apply();
                })
                $("div.balloon").hide();
            }
        }
    };

    this.saveAct = function(){
        var setList = {
                'conditionType': Number(this.condition_type),
                'conditions': angular.copy(this.setItemList),
                'widgetOpen': Number(this.widget_open),
                 // TODO 後々動的に
                'message': angular.element("#TAutoMessageAction").val()
        };
        var keys = Object.keys(setList['conditions']);
        if ("<?=C_AUTO_TRIGGER_DAY_TIME?>" in setList['conditions']) {
            for(var i = 0; setList['conditions']["<?=C_AUTO_TRIGGER_DAY_TIME?>"].length > i; i++){
                if ( 'timeSetting' in setList['conditions']["<?=C_AUTO_TRIGGER_DAY_TIME?>"][i] && Number(setList['conditions']["<?=C_AUTO_TRIGGER_DAY_TIME?>"][i].timeSetting) === 2 ) {
                    delete setList['conditions']["<?=C_AUTO_TRIGGER_DAY_TIME?>"][i]['startTime'];
                    delete setList['conditions']["<?=C_AUTO_TRIGGER_DAY_TIME?>"][i]['endTime'];
                }
            }
        }

        $('#TAutoMessageActivity').val(JSON.stringify(setList));
        submitAct();
    };

    this.isVisitCntRule = function(cnt, cond){
        if ( Number(cond) === 3 && Number(cnt) === 1) {
            return false;
        }
        return true;
    };
});

// http://stackoverflow.com/questions/17035621/what-is-the-angular-way-of-displaying-a-tooltip-lightbox
sincloApp.directive('ngShowonhover',function() {
    return {
        controller: 'MainController',
        controllerAs: 'main',
        link : function(scope, element, attrs) {
            var balloon = $("div.balloon");
            var itemsTag = element.closest("li");
            element.parent().bind('mouseenter', function(e) {
                if ( scope.$parent === null || !('itemForm' in scope.$parent) ) { return false; }
                if (Object.keys(scope.$parent.itemForm.$error).length === 0) { return false; }
                createBalloon(attrs['ngShowonhover'], scope.$parent.itemForm);
                var top = itemsTag.prop('offsetTop');
                var left = itemsTag.prop('offsetLeft');
                balloon.css({
                    "top": top + 10
                }).show();
            });
            element.parent().bind('mouseleave', function() {
                balloon.hide();
            });

            var createBalloon = function(key, form){
                var messageList = [];
                $("div.balloonContent").children().remove();

                /* 滞在時間 */
                if ( 'stayTimeRange' in form ) {
                    if ( 'required' in form.stayTimeRange.$error ) {
                        messageList.push("時間が未入力です");
                    }
                    if ( 'number' in form.stayTimeRange.$error ) {
                        messageList.push("時間は数値で入力してください");
                    }
                    if ('pattern' in form.stayTimeRange.$error) {
                        messageList.push("時間は0～100までの半角数字で指定できます");
                    }
                }
                /* 訪問回数 */
                if ( 'visitCnt' in form ) {
                    if ('required' in form.visitCnt.$error) {
                        messageList.push("訪問回数が未入力です");
                    }
                    if ( 'number' in form.visitCnt.$error ) {
                        messageList.push("訪問回数は数値で入力してください");
                    }
                    if ('pattern' in form.visitCnt.$error) {
                        messageList.push("訪問回数は1～100回までの半角数字で指定できます");
                    }
                    if ('isVisitCntRule' in form.visitCnt.$error) {
                        messageList.push("訪問回数は「1回未満」という設定はできません");
                    }
                }
                /* ページ・リファラー・検索キーワード・最初に訪れたページ・前のページ */
                if ( 'keyword' in form ) {
                    if (String(key) === '<?=h(C_AUTO_TRIGGER_REFERRER)?>' && 'required' in form.keyword.$error) {
                        messageList.push("URLが未入力です");
                    }
                    else if (String(key) !== '<?=h(C_AUTO_TRIGGER_REFERRER)?>' && 'required' in form.keyword.$error) {
                        messageList.push("キーワードが未入力です");
                    }
                }
                /* 曜日・日時 */
                if ( 'day' in form ) {
                    if ('required' in form.day.$error) {
                        messageList.push("曜日が未選択です");
                    }
                }
                if ( 'startTime' in form ) {
                    if ('required' in form.startTime.$error) {
                        messageList.push("開始時間が未入力です");
                    }
                    if ('pattern' in form.startTime.$error) {
                        messageList.push("開始時間は「00:00」の形で入力してください");
                    }
                }
                if ( 'endTime' in form ) {
                    if ('required' in form.endTime.$error) {
                        messageList.push("終了時間が未入力です");
                    }
                    if ('pattern' in form.endTime.$error) {
                        messageList.push("終了時間は「00:00」の形で入力してください");
                    }
                }

                /* 発言内容 */
                if ( 'speechContent' in form ) {
                    if ('required' in form.speechContent.$error) {
                      messageList.push("発言内容が未入力です");
                    }
                }

                /* 自動返信までの間隔 */
                if ( 'triggerTimeSec' in form ) {
                    if ('required' in form.triggerTimeSec.$error) {
                        messageList.push("自動返信までの間隔が未指定です。");
                    }
                    if ('pattern' in form.triggerTimeSec.$error) {
                      messageList.push("時間は1～60までの半角数字で指定できます");
                    }
                }

                for( var i = 0; i <  messageList.length; i++ ){
                    var element = document.createElement("p");
                    element.textContent = "● " + messageList[i];
                    $("div.balloonContent").append(element);
                }
            };
        }
    };
});

function addOption(type){
    var sendMessage = document.getElementById('TAutoMessageAction');
    switch(type){
        case 1:
            if (sendMessage.value.length > 0) {
                sendMessage.value += "\n";
            }
            sendMessage.value += "[] ";
            sendMessage.focus();
    }
}


function removeAct(){
    modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
    popupEvent.closePopup = function(){
        $.ajax({
            type: 'post',
            data: {
                id: document.getElementById('TAutoMessageId').value
            },
            cache: false,
            url: "<?= $this->Html->url('/TAutoMessages/remoteDelete') ?>",
            success: function(){
                location.href = "<?= $this->Html->url('/TAutoMessages/index') ?>";
            }
        });
    };
}

function submitAct(){
  $('#TAutoMessageEntryForm').submit();
}

</script>
