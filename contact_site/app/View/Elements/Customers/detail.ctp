<div class="card-shadow m10b">
	<div id='customer_subtitle'>
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
	<!-- <ul class="naviBtn p0">
		<li class="w50 tCenter on">チャット</li>
		<li class="w50 tCenter">メモ</li>
	</ul> -->
	<div id="chatContent">
		<ul id="chatTalk" >
		</ul>
		<div id="chatMenu" ng-class="{showOption: chatOptionDisabled(detailId)}">
			<span class="greenBtn btn-shadow" onclick="chatApi.addOption(1)">選択肢を追加する</span>
		</div>
		<div style="position: relative">
			<textarea rows="5" id="sendMessage" ng-focus="sendMessageConnectConfirm(detailId)" maxlength="300" placeholder="ここにメッセージ入力してください。
(Shift + Enterで改行)"></textarea>
			<div id="wordListArea">
				<select ng-init="entryWord=''" ng-model="entryWord" ng-options="v.id as v.label for (k, v) in entryWordSearch(entryWordList)" id="entryWordList"></select>
				<input type="text" ng-model="searchWord" id="wordSearchCond" />
				<ul id="wordList">
					<li ng-repeat="item in entryWordSearch(entryWordList)" id="item{{item.id}}" ng-class="{selected: item.id === entryWord}">{{item.label}}</li>
				</ul>
			</div>
			<span id="sinclo_sendbtn" class="btn-shadow" onclick="chatApi.pushMessage()">送信（Enter）</span>
		</div>
	</div>
	<audio id="sinclo-sound">
		<source src="<?=C_PATH_NODE_FILE_SERVER?>/sounds/decision.mp3" type="audio/mp3">';
	</audio>
</div>
<?php endif; ?>
