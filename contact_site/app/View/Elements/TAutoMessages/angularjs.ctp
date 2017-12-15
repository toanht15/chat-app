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
      //営業時間を利用しない場合
      if(<?= $operatingHourData ?> == 2) {
        delete obj[4];
      }
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
        //営業時間設定を利用しない場合
        if(<?= $operatingHourData ?> == 2 && itemId == 4) {
          //ツールチップ表示
          $('#triggerList div ul li').each(function(i){
            if(i == 3) {
              $(this).addClass("commontooltip");
              $(this).attr('data-text', 'こちらの機能は営業時間設定で「利<br>用する」を選択すると、ご利用いただけます。');
              $(this).attr('data-balloon-position', '14');
              $(this).attr('operatingHours', 'widgetHoursPage');
            }
          });
          return true;
        }
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
            //営業時間設定を利用しない場合
            if(<?= $operatingHourData ?> == 2 && tmpId == 4) {
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
                'message': angular.element("#TAutoMessageAction").val(),
                'chatTextarea': Number(this.chat_textarea),
                'cv': Number(this.cv),
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

                /* 営業時間 */
                if( 'notOperatingHour' in form) {
                    messageList.push("営業時間設定を利用していません");
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
            break;
        case 2:
          if (sendMessage.value.length > 0) {
            sendMessage.value += "\n";
          }
          sendMessage.value += "<telno></telno>";
          sendMessage.focus();
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', sendMessage.value.length-8);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, sendMessage.value.length-8);
          }
          break;
    }
}


function removeAct(lastPage){
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
                var url = "<?= $this->Html->url('/TAutoMessages/index') ?>";
                location.href = url + "/page:" + lastPage;
            }
        });
    };
}

function submitAct(){
  $('#TAutoMessageEntryForm').submit();
}

//スクロール位置把握
var topPosition = 0;
window.onload = function() {
  document.querySelector('#content').onscroll = function() {
    topPosition = this.scrollTop;
  };
};

$(document).ready(function(){
  // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    console.log(parentTdId);
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    console.log(targetObj);
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 170 + topPosition) + 'px',
      left: $(this).offset().left - 101 + 'px'
    });
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });

  // これまでのチャット内容をメールで送信する
  var initializeFromMailAddressArea = function() {
    var checked = $('#mainSendMailFlg').prop('checked');
    if(checked) {
      $('.sendMailSettings').css('display', '');
      $('#sendMailSettingCheckBox').css("padding","15px 0 0 0");
    } else {
      $('.sendMailSettings').css('display', 'none');
      $('#sendMailSettingCheckBox').css("padding","15px 0 15px 0");
    }
    var atFirst = true;
    var prevObj = undefined;
    $('.mailAddressBlock').each(function(index){
      var mailAddress = $(this).find('input[type="text"]').val();
      if(mailAddress !== "") {
        $(this).css('display', 'inline-flex').addClass('show');
        $(this).find('.disOffgreenBtn').css('display', 'none');
        $(this).find('.deleteBtn').css('display', 'block');
        if(index !== 0 && index < 4) {
          prevObj.find('.disOffgreenBtn').css('display', 'none');
          $(this).find('.disOffgreenBtn').css('display', 'block');
        } else if(prevObj) {
          prevObj.find('.disOffgreenBtn').css('display', 'none');
        }
      } else {
        if(index === 0) {
          $(this).css('display', 'inline-flex').addClass('show');
          $(this).find('.disOffgreenBtn').css('display', 'block');
          $(this).find('.deleteBtn').css('display', 'none');
        } else if(index === 1) {
          $(this).css('display', 'none').removeClass('show');
          prevObj.find('.disOffgreenBtn').css('display', 'block');
          prevObj.find('.deleteBtn').css('display', 'none');
        } else {
          $(this).css('display', 'none').removeClass('show');
        }
      }
      prevObj = $(this);
    });

  };

  $('#mainSendMailFlg').on('change', function(event){
    initializeFromMailAddressArea();
  });

  $('.disOffgreenBtn').on('click', function(ev){
    $(this).parents('.mailAddressBlock').next('span').css('display', 'inline-flex').addClass('show').find('input[type="text"]').val('');
    $(this).css('display','none').parents('.btnBlock').find('.redBtn').css('display', 'block');
    if($('#fromMailAddressSettings').find('.show').length === 5) {
      $(this).parents('.mailAddressBlock').next('span').find('.disOffgreenBtn').css('display', 'none');
    }
  });

  $('.deleteBtn').on('click', function(ev){
    $('#mailAddressSetting').find('span.show').last().css('display','none').removeClass('show');
    var targetObj = $(this).parents('.mailAddressBlock').find('input[type="text"]');
    targetObj.val('');
    var nextAllObj = $(this).parents('.mailAddressBlock').nextAll('span');
    nextAllObj.each(function(idx){
      targetObj.val($(this).find('input[type="text"]').val());
      targetObj = $(this).find('input[type="text"]');
      if(nextAllObj.length-1 === idx) {
        targetObj.val('');
      }
    });

    $('#mailAddressSetting').find('span.show').last().find('.disOffgreenBtn').css('display', 'block');
    if($('#fromMailAddressSettings').find('.show').length === 1) {
      $('#mailAddressSetting').find('span.show').first().find('.disOffgreenBtn').css('display', 'block');
      $('#mailAddressSetting').find('span.show').first().find('.redBtn').css('display', 'none');
    }
  });
  initializeFromMailAddressArea();
});
</script>
