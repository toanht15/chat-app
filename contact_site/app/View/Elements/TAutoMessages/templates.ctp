<?php /* 滞在時間｜C_AUTO_TRIGGER_STAY_TIME */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_STAY_TIME?>'" class="setStayTime">
	<li>
		<span><label>単位</label></span>
		<select name="stayTimeType" ng-model="setItem.stayTimeType">
			<option value="1">秒</option>
			<option value="2">分</option>
			<option value="3">時</option>
		</select>
	</li>
	<li>
		<span><label>時間</label></span>
		<input type="number" min="0" max="100" step="1" ng-model="setItem.stayTimeRange" name="stayTimeRange" required="">
	</li>
</ul>

<?php /* 訪問回数｜C_AUTO_TRIGGER_VISIT_CNT */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_VISIT_CNT?>'"  class="setVisitCnt">
		<li>
			<span><label>訪問回数</label></span>
			<input type="number" min="0" max="99" step="1" ng-model="setItem.visitCnt" name="visitCnt" required="">&nbsp;回
		</li>
		<li>
			<span><label>条件</label></span>
			<label><input type="radio" ng-model="setItem.visitCntCond" name="visitCntCond{{itemId}}_{{$id}}" value="1">一致</label>&nbsp;
			<label><input type="radio" ng-model="setItem.visitCntCond" name="visitCntCond{{itemId}}_{{$id}}" value="2">以上</label>&nbsp;
			<label><input type="radio" ng-model="setItem.visitCntCond" name="visitCntCond{{itemId}}_{{$id}}" value="3">未満</label>&nbsp;
		</li>
	</ul>
<?php /* ページ｜C_AUTO_TRIGGER_STAY_PAGE */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_STAY_PAGE?>'"  class="setStayPage">
	<li>
		<span><label>対象</label></span>
		<label><input type="radio" ng-model="setItem.targetName" name="targetName{{itemId}}_{{$id}}" value="1">タイトル</label>&nbsp;
		<label><input type="radio" ng-model="setItem.targetName" name="targetName{{itemId}}_{{$id}}" value="2">URL</label>

	</li>
	<li>
		<span><label>キーワード</label></span>
		<input type="text" ng-model="setItem.keyword" name="keyword" required="">
	</li>
	<li>
		<span><label>条件</label></span>
		<label><input type="radio" ng-model="setItem.stayPageCond" name="stayPageCond{{itemId}}_{{$id}}" value="1">部分一致</label>&nbsp;
		<label><input type="radio" ng-model="setItem.stayPageCond" name="stayPageCond{{itemId}}_{{$id}}" value="2">不一致</label>
		<label><input type="radio" ng-model="setItem.stayPageCond" name="stayPageCond{{itemId}}_{{$id}}" value="2">不一致</label>
	</li>
</ul>

<?php /* 曜日・時間｜C_AUTO_TRIGGER_DAY_TIME */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_DAY_TIME?>'" class="setDayTime">
	<li>
		<span><label>曜日</label></span>
		<boxes>
			<label><input type="checkbox" ng-model="setItem.day.mon" name="day" value="1">月</label>
			<label><input type="checkbox" ng-model="setItem.day.tue" name="day" value="2">火</label>
			<label><input type="checkbox" ng-model="setItem.day.wed" name="day" value="3">水</label>
			<label><input type="checkbox" ng-model="setItem.day.thu" name="day" value="4">木</label>
			<label><input type="checkbox" ng-model="setItem.day.fri" name="day" value="5">金</label>
			<label><input type="checkbox" ng-model="setItem.day.sat" name="day" value="6">土</label>
			<label><input type="checkbox" ng-model="setItem.day.sun" name="day" value="7" ng-required="main.requireCheckBox(setItem.day)">日</label>
		</boxes>
	</li>
	<li>
		<span><label>時間指定</label></span>
		<label><input type="radio" ng-model="setItem.timeSetting" name="timeSetting{{itemId}}_{{$id}}" value="1">する</label>&nbsp;
		<label><input type="radio" ng-model="setItem.timeSetting" name="timeSetting{{itemId}}_{{$id}}" value="2">しない</label>
	</li>
	<li>
		<div class="input-group clockpicker bt0">
			<input type="text" class="form-control" ng-model="setItem.startTime" ng-disabled="setItem.referrerCond == '2'" required="">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-time"></span>
			</span>
		</div>
		<div class="bt0"><span>～</span></div>
		<div class="input-group clockpicker bt0">
			<input type="text" class="form-control" ng-model="setItem.endTime" ng-disabled="setItem.referrerCond == '2'" required="">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-time"></span>
			</span>
		</div>
	</li>
	{{setItem|json}}
	<script type="text/javascript">$('.clockpicker').clockpicker({'donetext':'設定'});</script>
</ul>

<?php /* 参照元URL（リファラー）｜C_AUTO_TRIGGER_REFERRER */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_REFERRER?>'" class="setReferrer">
	<li>
		<span><label>対象</label></span>
		<label><input type="radio" ng-model="setItem.targetName" name="targetName{{itemId}}_{{$id}}" value="1">タイトル</label>&nbsp;
		<label><input type="radio" ng-model="setItem.targetName" name="targetName{{itemId}}_{{$id}}" value="2">URL</label>
	</li>
	<li>
		<span><label>キーワード</label></span>
		<input type="text" ng-model="setItem.keyword" name="keyword" required="">
	</li>
	<li>
		<span><label>条件</label></span>
		<label><input type="radio" ng-model="setItem.referrerCond" name="referrerCond{{itemId}}_{{$id}}" value="1">完全一致</label>&nbsp;
		<label><input type="radio" ng-model="setItem.referrerCond" name="referrerCond{{itemId}}_{{$id}}" value="2">部分一致</label>&nbsp;
		<label><input type="radio" ng-model="setItem.referrerCond" name="referrerCond{{itemId}}_{{$id}}" value="3">不一致</label>
	</li>
</ul>

<?php /* 検索キーワード｜C_AUTO_TRIGGER_SEARCH_KEY */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_SEARCH_KEY?>'" class="setSearchKeyword">
	<li>
		<span><label>キーワード</label></span>
		<input type="text" ng-model="setItem.keyword" name="keyword" required="">
	</li>
	<li>
		<span><label>条件</label></span>
		<radios>
			<label><input type="radio" ng-model="setItem.searchCond" name="searchCond{{itemId}}_{{$id}}" value="1">完全一致</label>
			<label><input type="radio" ng-model="setItem.searchCond" name="searchCond{{itemId}}_{{$id}}" value="2">部分一致</label><br/>
			<label><input type="radio" ng-model="setItem.searchCond" name="searchCond{{itemId}}_{{$id}}" value="3">不一致（若しくは取得できなかった場合）</label>
		</radios>
	</li>
</ul>
