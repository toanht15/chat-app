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

var popupEventOverlap = {
        id: null,
        title: null,
        contents: null,
        closePopup: null,
        customizeBtn: null,
        moveType: null, // スクロール
        closeNoPopupOverlap: function(){
          return popupEventOverlap.close();
        },
        initOverlap: function() {
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
        _setEventOverlap: function(){
          this.elm.popup = document.getElementById('popup-frame-overlap');
          this.elm.close = document.getElementById('popupCloseOverlapBtn');
          this.elm.btnArea = document.getElementById('popup-button-overlap');
/*
          this.elm.help = document.getElementById('popupHelpBtn');
          var help = this.elm.help;
          help.addEventListener('click', function(){ return popupEvent.help(); });
*/
          var close = this.elm.close;
          close.addEventListener('click', function(){ return popupEventOverlap.closeNoPopupOverlap(); });
        },
        help: function(){},
        create: function () {
            var area = popupEventOverlap.elm.btnArea;
            for (var i =area.childNodes.length-1; i>=0; i--) {
                area.removeChild(area.childNodes[i]);
            }
            switch ( popupEventOverlap.id ) {
                case 'p-history-del':
                    var closeBtn = _button("はい");
                    closeBtn.onclick = function(){
                      popupEventOverlap.closeNoPopupOverlap();
                      return popupEventOverlap.closePopup();
                    };
                    var cancelBtn = _button("いいえ");
                    cancelBtn.onclick = function(){
                      return popupEventOverlap.closeNoPopupOverlap();
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
            $('#popup-main-overlap').html(this.contents);
            // タイトルをセット
            $('#popup-title-overlap').text(this.title);
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
            $("#popup-frame-overlap").removeAttr('style').removeClass();
            // コンテンツを作成
            this._popupCreate();

            if ( this.moveType === 'moment' ) {
                // ポップアップを表示状態にする
                $(".popup-off-overlap").addClass('popup-on-overlap').removeClass('popup-off-overlap');
                var contHeight = $('#popup-content-overlap').height();
                $('#popup-frame-overlap').css('top', 0).css('height', contHeight);
            }
            else {
                // 一時的にスクロール非表示に
                $('body').css('overflow', 'hidden');
                // ポップアップを表示状態にする
                $(".popup-off-overlap").addClass('popup-on-overlap').removeClass('popup-off-overlap');
                var contHeight = $('#popup-content-overlap').height();
                $('#popup-frame-overlap').css('height', contHeight);
                this.elm.popup.style.top = (window.innerHeight) + "px";

                $('#popup-frame-overlap').animate(
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
                $('.popup-on-overlap').addClass('popup-off-overlap').removeClass('popup-on-overlap');
            }
            else {
                $('body').css('overflow', 'hidden');
                $('#popup-frame-overlap').animate(
                    {
                        top: (window.innerHeight + $('#popup-frame-overlap').height())
                    },
                    500,
                    function(){
                        $('body').css('overflow', 'auto');
                        $('.popup-on-overlap').addClass('popup-off-overlap').removeClass('popup-on-overlap');
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
                    //alert(message);
                    //return false;
                    //break;
            }
            $("#shortMessageOverlap").text(message).attr('style', '').addClass(className);
            $("#shortMessageOverlap").removeClass('popup-off-overlap');
            window.setTimeout(function(){
                shortMessage.close();
            }, 1500);
        },
        close: function(){
            $("#shortMessageOverlap").animate(
                {
                    opacity: 0
                },
                500,
                function(){
                    window.setTimeout(function(){
                        $('#shortMessageOverlap').prop('class', 'popup-off-overlap');
                    }, 500);
                }
            );
        }
    };

!function(pe, se){
    window.modalOpenOverlap = function(contents, id, title, type){
        if (typeof(type) !== 'undefined') {
            pe.moveType = type;
        } else {
            pe.moveType = 'moveup';
        }
        pe.initOverlap();
        return pe.open(contents, id, title);
    };
    window.modalCloseOverlap = function(){
        return pe.close();
    };
    window.showMessageOverlap = function(type, message){
        // type 1:success, 2:error, 3:notice
        return se.open(type, message);
    };

    $(document).ready(function(){
<?php
if ( isset($alertMessage) && !empty($alertMessage) ) {
      echo "showMessage(". $alertMessage['type'] .", '" . $alertMessage['text'] . "');";
}
?>
      popupEventOverlap._setEventOverlap();
    });
}(popupEventOverlap, shortMessage);

</script>
<div id="popup-overlap" class="popup-off-overlap" style="">
  <div id="popup-frame-base-overlap">
    <div id="popup-bg-overlap"></div>
    <div id="popup-frame-overlap">
      <div id="popup-content-overlap">
            <div id="popup-ctrl-btn-overlap">
<!--
                <?php echo $this->Html->link(
                    $this->Html->image('question.png', array('alt' => 'ヘルプ', 'width'=>20, 'height'=>20)),
                    'javascript:void(0)',
                    array('escape' => false, 'class'=>'greenBtn btn-shadow', 'id' => 'popupHelpOverlapBtn'));
                ?>
-->
                <?php echo $this->Html->link(
                    $this->Html->image('close.png', array('alt' => '閉じる', 'width'=>20, 'height'=>20)),
                    'javascript:void(0)',
                    array('escape' => false, 'class'=>'redBtn btn-shadow', 'id' => 'popupCloseOverlapBtn'));
                ?>
            </div>
            <div id="popup-title-overlap"></div>
            <div id="popup-main-overlap"></div>
            <div id="popup-button-overlap">
            </div>
        </div>
    </div>
  </div>

</div>
<div id="shortMessage-overlap" class="popup-off-overlap">
</div>