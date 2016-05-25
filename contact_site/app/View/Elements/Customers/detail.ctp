<div class="card-shadow m10b">
	<div id='customer_subtitle'>
		<?= $this->Html->image('sub_icon.png', ['alt' => '詳細情報', 'width' => 25, 'height' => 25, 'style' => 'margin: 0 15px 0 auto;transform: rotate(45deg);', 'class'=>'fLeft']) ?>
		<h1 class="fLeft">詳細情報</h1>
		<!-- 閉じる -->
		<a ng-if="chatList.indexOf(detailId) < 0" href="javascript:void(0)" ng-click="showDetail(detailId)" class="fRight customer_detail_btn redBtn btn-shadow">
			<?= $this->Html->image('close.png', ['alt'=>'詳細を閉じる', 'width'=>20, 'height' => 20]); ?>
		</a>
		<a ng-if="chatList.indexOf(detailId) >= 0" href="javascript:void(0)" ng-click="confirmDisConnect(detailId)" class="fRight customer_detail_btn redBtn btn-shadow">
			<?= $this->Html->image('close.png', ['alt'=>'チャットを終了する', 'width'=>20, 'height' => 20]); ?>
		</a>
		<!-- 閉じる -->
		<!-- 最小化 -->
		<a href="javascript:void(0)" ng-click="showDetail(detailId)" class="fRight customer_detail_btn redBtn btn-shadow">
			<?= $this->Html->image('minimize.png', ['alt'=>'詳細を閉じる', 'width'=>20, 'height' => 20]); ?>
		</a>
		<!-- 最小化 -->
	</div>
</div>
<div id="customer_detail" class="card-shadow m10b p10x">
	<h2>基本情報</h2>
	<ul class="p20l">
		<li>
		  <span>アクセスID：</span><br>
			{{monitorList[detailId].accessId}}
		</li>
		<li>
		  <span>IPアドレス：</span><br>
			{{monitorList[detailId].ipAddress}}
		</li>
		<li>
		  <span>ユーザーエージェント：</span><br>
			{{monitorList[detailId].userAgent}}
		</li>
		<li>
		  <span>閲覧中ページ：</span><br>
		  <a ng-href='{{monitorList[detailId].url}}' target='showBlack'>{{monitorList[detailId].title}}</a>
		</li>
	</ul>
</div>

<?php if ( $coreSettings[C_COMPANY_USE_CHAT] ) :?>
<div id="chat-area" class="card-shadow p10x">
	<h2>チャット</h2>
	<ul class="naviBtn p0">
		<li class="w50 tCenter on">チャット</li>
		<li class="w50 tCenter">メモ</li>
	</ul>
	<div id="chatContent">
		<ul id="chatTalk" >
		</ul>
		<div style="position: relative">
			<textarea rows="5" id="sendMessage" maxlength="300" placeholder="メッセージ入力後、Enterで送信"></textarea>
			<span id="sinclo_sendbtn" class="btn-shadow" onclick="chatApi.pushMessage()">送信</span>
		</div>
	</div>
</div>
<?php endif; ?>
