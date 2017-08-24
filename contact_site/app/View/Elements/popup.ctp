<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<script type="text/javascript">
'use strict';

var popupEvent = {
        id: null,
        title: null,
        contents: null,
        closePopup: null,
        customizeBtn: null,
        moveType: null, // スクロール
        closeNoPopup: function(){
          return popupEvent.close();
        },
        init: function() {
            this.closePopup = '';
            if ( !this.moveType ) {
                this.moveType = 'moveup';
            }
        },
        elm: {
            popup: null,
            help: null,
            close: null,
            btnArea: null
        },
        _setEvent: function(){
          this.elm.popup = document.getElementById('popup-frame');
          this.elm.close = document.getElementById('popupCloseBtn');
          this.elm.btnArea = document.getElementById('popup-button');
/*
          this.elm.help = document.getElementById('popupHelpBtn');
          var help = this.elm.help;
          help.addEventListener('click', function(){ return popupEvent.help(); });
*/
          var close = this.elm.close;
          close.addEventListener('click', function(){ return popupEvent.closeNoPopup(); });
        },
        help: function(){},
        create: function () {
            var area = popupEvent.elm.btnArea;
            for (var i =area.childNodes.length-1; i>=0; i--) {
                area.removeChild(area.childNodes[i]);
            }
            switch ( popupEvent.id ) {
                case 'p-confirm':
                    var closeBtn = _button("はい");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    var closeBtn = _button("いいえ");
                    closeBtn.onclick = function(){
                        return popupEvent.closeNoPopup();
                    };
                    break;
                case 'p-cus-connection':
                    var closeBtn = _button("接続");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup(1);
                    };
                    var closeBtn = _button("接続しない");
                    closeBtn.onclick = function(){
                        return popupEvent.closeNoPopup();
                    };
                    break;
                case 'p-cus-detail':
                    var closeBtn = _button("チャットを終了する");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    var closeBtn = _button("閉じる");
                    closeBtn.onclick = function(){
                        return popupEvent.closeNoPopup();
                    };
                    break;
                case 'p-show-gallary':
                    var closeBtn = _button("閉じる");
                    closeBtn.onclick = function(){
                        return popupEvent.closeNoPopup();
                    };
                    break;
                case 'p-cus-menu':
                    var closeBtn = _button("設定");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    break;
                case 'p-thistory-entry':
                    var entryBtn = _button("検索する");
                    entryBtn.onclick = function(){
                      return popupEvent.closePopup();
                    };
                    var closeBtn = _button("閉じる");
                    closeBtn.onclick = function(){
                      return popupEvent.close();
                    };
                    break;
                case 'p-history-cus':
                case 'p-muser-entry':
                case 'p-tcampaign-entry':
                case 'p-tdictionary-entry':
                    var entryBtn = _button("保存");
                    entryBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    var closeBtn = _button("閉じる");
                    closeBtn.onclick = function(){
                        return popupEvent.close();
                    };
                    break;
                case 'p-copy':
                    var closeBtn = _button("コピーする");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    break;
                case 'p-move':
                    var closeBtn = _button("移動する");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    break;
                case 'p-category-edit':
                    var closeBtn = _button("カテゴリ名を変更");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    break;
                case 'p-category-del':
                    var closeBtn = _button("カテゴリの削除");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    break;
                case 'p-category-dictionary-edit':
//                     var closeBtn = _button("カテゴリの削除");
//                     closeBtn.onclick = function(){
//                         return popupEvent.closePopup();
//                     };
//                     break;
                case 'p-alert':
                    var closeBtn = _button("閉じる");
                    closeBtn.onclick = function(){
                        return popupEvent.closeNoPopup();
                    };
                    break;
                default:
                    var closeBtn = _button("閉じる");
                    closeBtn.onclick = function(){
                        return popupEvent.close();
                    };
                    break;
            }
            function _button(text){
                var a = document.createElement('a');
                a.classList.add("textBtn");
                a.classList.add("greenBtn");
                a.classList.add("btn-shadow");
                a.href = "javascript:void(0)";
                a.textContent = text;
                area.appendChild(a);
                return a;
            }
        },
        _popupCreate: function(){
            // コンテンツにHTMLをセット
            $('#popup-main').html(this.contents);
            // タイトルをセット
            $('#popup-title').text(this.title);
            this.create();
            // 出現初期位置をセット
            this.elm.popup.classList.add(this.id);
        },
        open: function(contents, id, title){
            // データをセット
            this.contents = contents;
            this.id = (id) ? id : 'popup-normal';
            this.title = title;

            // スタイルのリセット
            $("#popup-frame").removeAttr('style').removeClass();
            // コンテンツを作成
            this._popupCreate();

            if ( this.moveType === 'moment' ) {
                // ポップアップを表示状態にする
                $(".popup-off").addClass('popup-on').removeClass('popup-off');
                var contHeight = $('#popup-content').height();
                $('#popup-frame').css('top', 0).css('height', contHeight);
            }
            else {
                // 一時的にスクロール非表示に
                $('body').css('overflow', 'hidden');
                // ポップアップを表示状態にする
                $(".popup-off").addClass('popup-on').removeClass('popup-off');
                var contHeight = $('#popup-content').height();
                $('#popup-frame').css('height', contHeight);
                this.elm.popup.style.top = (window.innerHeight) + "px";

                $('#popup-frame').animate(
                    {
                        top: 0
                    },
                    500,
                    function(){
                        $('body').css('overflow', 'auto');
                    }
                );
            }

        },
        close: function(){
            if ( this.moveType === 'moment' ) {
                $('.popup-on').addClass('popup-off').removeClass('popup-on');
            }
            else {
                $('body').css('overflow', 'hidden');
                $('#popup-frame').animate(
                    {
                        top: (window.innerHeight + $('#popup-frame').height())
                    },
                    500,
                    function(){
                        $('body').css('overflow', 'auto');
                        $('.popup-on').addClass('popup-off').removeClass('popup-on');
                    }
                );
            }
        }
    },
    shortMessage = {
        elm: {
            content: document.getElementById('shortMessage')
        },
        open: function(type, message){
            var className = "";
            switch(type){
                case 1: // success
                    className = "success";
                    break;
                case 2: // failure
                    className = "failure";
                    break;
                case 3: // alert
                    alert(message);
                    return false;
                    break;
            }
            $("#shortMessage").text(message).attr('style', '').addClass(className);
            $("#shortMessage").removeClass('popup-off');
            window.setTimeout(function(){
                shortMessage.close();
            }, 1500);
        },
        close: function(){
            $("#shortMessage").animate(
                {
                    opacity: 0
                },
                500,
                function(){
                    window.setTimeout(function(){
                        $('#shortMessage').prop('class', 'popup-off');
                    }, 500);
                }
            );
        }
    };

!function(pe, se){
    window.modalOpen = function(contents, id, title, type){
        if (typeof(type) !== 'undefined') {
            pe.moveType = type;
        } else {
            pe.moveType = 'moveup';
        }
        pe.init();
        return pe.open(contents, id, title);
    };
    window.modalClose = function(){
        return pe.close();
    };
    window.showMessage = function(type, message){
        // type 1:success, 2:error, 3:notice
        return se.open(type, message);
    };

    $(document).ready(function(){
<?php
if ( isset($alertMessage) && !empty($alertMessage) ) {
      echo "showMessage(". $alertMessage['type'] .", '" . $alertMessage['text'] . "');";
}
?>
      popupEvent._setEvent();
    });
}(popupEvent, shortMessage);

</script>
<div id="popup" class="popup-off" style="">
  <div id="popup-frame-base">
    <div id="popup-bg"></div>
    <div id="popup-frame">
        <div id="popup-content">
            <div id="popup-ctrl-btn">
<!--
                <?php echo $this->Html->link(
                    $this->Html->image('question.png', array('alt' => 'ヘルプ', 'width'=>20, 'height'=>20)),
                    'javascript:void(0)',
                    array('escape' => false, 'class'=>'greenBtn btn-shadow', 'id' => 'popupHelpBtn'));
                ?>
-->
                <?php echo $this->Html->link(
                    $this->Html->image('close.png', array('alt' => '閉じる', 'width'=>20, 'height'=>20)),
                    'javascript:void(0)',
                    array('escape' => false, 'class'=>'redBtn btn-shadow', 'id' => 'popupCloseBtn'));
                ?>
            </div>
            <div id="popup-title"></div>
            <div id="popup-main"></div>
            <div id="popup-button">
            </div>
        </div>
    </div>
  </div>

</div>
<div id="shortMessage" class="popup-off">
</div>
