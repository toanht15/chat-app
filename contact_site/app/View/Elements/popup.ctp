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
var closePopup,
	popupEvent = {
		id: null,
		title: null,
		contents: null,
		init: function() {
			this.elm.popup = document.getElementById('popup');
			this.elm.help = document.getElementById('popupHelpBtn');
			this.elm.close = document.getElementById('popupCloseBtn');
			this.elm.btnArea = document.getElementById('popup-button');
			closePopup = '';
			// イベントのセット
			this._setEvent();
		},
		elm: {
			popup: null,
			help: null,
			close: null,
			btnArea: null
		},
		_setEvent: function(){
			var help = this.elm.help;
			help.addEventListener('click', function(){ return popupEvent.help(); });
			var close = this.elm.close;
			close.addEventListener('click', function(){ return popupEvent.close(); });
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
						return closePopup();
					};
					var closeBtn = _button("いいえ");
					closeBtn.onclick = function(){
						return popupEvent.close();
					};
					break;
				case 'p-cus-menu':
					var closeBtn = _button("設定");
					closeBtn.onclick = function(){
						return closePopup();
					};
					break;
				case 'p-muser-entry':
					var entryBtn = _button("登録");
					entryBtn.onclick = function(){
						return closePopup();
					};
					var closeBtn = _button("閉じる");
					closeBtn.onclick = function(){
						return popupEvent.close();
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
			// 出現初期位置をセット
			var popup = this.elm.popup.classList.add(this.id);
			this.elm.popup.style.bottom = -(window.innerHeight + $('#popup').height()) + "px";
			this.create();
		},
		open: function(contents, id, title){
			// データをセット
			this.contents = contents;
			this.id = id;
			this.title = title;

			// スタイルのリセット
			$("#popup").removeAttr('style');
			// コンテンツを作成
			this._popupCreate();
			// 一時的にスクロール非表示に
			$('body').css('overflow', 'hidden');
			// ポップアップを表示状態にする
			$('.popup-off').removeClass('popup-off');
			$('#popup').animate(
				{
					bottom: 0
				},
				500,
				function(){
					$('body').css('overflow', 'auto');
				}
			);
		},
		close: function(){
			$('body').css('overflow', 'hidden');
			$('#popup').animate(
				{
					bottom: -(window.innerHeight + $('#popup').height())
				},
				500,
				function(){
					$('body').css('overflow', 'auto');
					$('#popup-bg, #popup').attr('class', 'popup-off');
				}
			);
		}
	};

!function(pe){
	$(document).ready(function(){
		// pe.init();
	});
	window.modalOpen = function(contents, id, title){
		pe.init();
		return pe.open(contents, id, title);
	};
	window.modalClose = function(){
		return pe.close();
	};
}(popupEvent);

</script>
<div id="popup-bg" class="popup-off"></div>
<div id="popup" class="popup-off">
	<div id="popup-content">
		<div id="popup-ctrl-btn">
			<?php echo $this->Html->link(
				$this->Html->image('question.png', array('alt' => 'ヘルプ', 'width'=>20, 'height'=>20)),
				'javascript:void(0)',
				array('escape' => false, 'class'=>'greenBtn btn-shadow', 'id' => 'popupHelpBtn'));
			?>
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