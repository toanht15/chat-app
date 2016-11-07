
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
        customizeBtn: null,
        moveType: null, // スクロール
        closePopup: function(){
          return popupEvent.close();
        },
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
          this.elm.close = document.getElementById('popup-close-btn');
          this.elm.btnArea = document.getElementById('popup-button');
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
                    closeBtn.classList.add("normal_btn");
                    closeBtn.onclick = function(){
                        return popupEvent.closePopup();
                    };
                    var closeBtn = _button("いいえ");
                    closeBtn.classList.add("normal_btn");
                    closeBtn.onclick = function(){
                        return popupEvent.closeNoPopup();
                    };
                    break;
                case 'p-alert':
                    var closeBtn = _button("閉じる");
                    closeBtn.classList.add("normal_btn");
                    closeBtn.onclick = function(){
                        return popupEvent.closeNoPopup();
                    };
                    break;
                default:
                    var closeBtn = _button("閉じる");
                    closeBtn.classList.add("normal_btn");
                    closeBtn.onclick = function(){
                        return popupEvent.close();
                    };
                    break;
            }
            function _button(text){
                var a = document.createElement('a');
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
            $('#popup-title h2').text(this.title);
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

<div id="popup" class="popup-off">
  <div id="popup-frame-base">
    <div id="popup-bg"></div>
    <div id="popup-frame" style="width: 400px; height: 170px">
      <div id="popup-content">
        <div id	="popup-title">
          <h2></h2>
          <a id="popup-close-btn" href="javascript:void(0)">×</a>
        </div>
        <div id	="popup-main"></div>
        <div id	="popup-button"></div>
      </div>
    </div>
  </div>
</div><!-- modal -->

<div id="shortMessage" class="popup-off">
</div>
