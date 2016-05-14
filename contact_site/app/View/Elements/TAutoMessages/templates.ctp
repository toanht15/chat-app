<?php echo $this->Html->script('handlebars-v4.0.5.js'); ?>
<?php /* 滞在時間｜C_AUTO_TRIGGER_STAY_TIME */ ?>
<script id="tmp_stay_time" type="text/x-handlebars-template">
<li class="triggerItem">
	<h4>滞在時間</h4>
	<div>
		<ul class="setStayTime">
			<li>
				<span><label>単位</label></span>
				<select name="stayTimeType">
					<option value="1">秒</option>
					<option value="2">分</option>
					<option value="3">時</option>
				</select>
			</li>
			<li>
				<span><label>時間</label></span>
				<input type="range" name="stayTimeRange">
			</li>
		</ul>
	</div>
</li>
</script>

<?php /* 訪問回数｜C_AUTO_TRIGGER_VISIT_CNT */ ?>
<script id="tmp_visit_cnt" type="text/x-handlebars-template">
<li class="triggerItem">
	<h4>訪問回数</h4>
	<div>
		<ul class="setVisitCnt">
			<li>
				<span><label>訪問回数</label></span>
				<input type="number" min="0" max="99" step="1" name="visitCnt">&nbsp;回
			</li>
			<li>
				<span><label>条件</label></span>
				<label><input type="radio" name="visitCntCond" value="1" checked="checked">一致</label>&nbsp;
				<label><input type="radio" name="visitCntCond" value="2">以上</label>&nbsp;
				<label><input type="radio" name="visitCntCond" value="3">未満</label>
			</li>
		</ul>
	</div>
</li>
</script>
<?php /* ページ｜C_AUTO_TRIGGER_STAY_PAGE */ ?>
<script id="tmp_stay_page" type="text/x-handlebars-template">
<li class="triggerItem">
	<h4>ページ</h4>
	<div>
		<ul class="setStayPage">
			<li>
				<span><label>対象</label></span>
				<label><input type="radio" name="targetName" value="1" checked="checked">タイトル</label>&nbsp;
				<label><input type="radio" name="targetName" value="2">URL</label>
			</li>
			<li>
				<span><label>キーワード</label></span>
				<input type="text" name="keyword">
			</li>
			<li>
				<span><label>条件</label></span>
				<label><input type="radio" name="stayPageCond" value="1" checked="checked">完全一致</label>&nbsp;
				<label><input type="radio" name="stayPageCond" value="2">部分一致</label>&nbsp;
				<label><input type="radio" name="stayPageCond" value="3">不一致</label>
			</li>
		</ul>
	</div>
</li>
</script>

<?php /* 曜日・時間｜C_AUTO_TRIGGER_DAY_TIME */ ?>
<script id="tmp_day_time" type="text/x-handlebars-template">
<li class="triggerItem">
	<h4>曜日・時間</h4>
	<div>
		<ul class="setDayTime">
			<li>
				<span><label>曜日</label></span>
				<boxes>
					<label><input type="checkbox" name="day" value="1">月</label>
					<label><input type="checkbox" name="day" value="2">火</label>
					<label><input type="checkbox" name="day" value="3">水</label>
					<label><input type="checkbox" name="day" value="4">木</label>
					<label><input type="checkbox" name="day" value="5">金</label>
					<label><input type="checkbox" name="day" value="6">土</label>
					<label><input type="checkbox" name="day" value="7">日</label>
				</boxes>
			</li>
			<li>
				<span><label>時間指定</label></span>
				<label><input type="radio" name="referrerCond" value="1" checked="checked">有効</label>&nbsp;
				<label><input type="radio" name="referrerCond" value="2">無効</label>
			</li>
			<li>
				<div class="input-group clockpicker bt0">
					<input type="text" class="form-control" value="09:00">
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-time"></span>
					</span>
				</div>
				<div class="bt0"><span>～</span></div>
				<div class="input-group clockpicker bt0">
					<input type="text" class="form-control" value="18:00">
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-time"></span>
					</span>
				</div>
			</li>
		</ul>
	</div>
</li>
</script>

<?php /* 参照元URL（リファラー）｜C_AUTO_TRIGGER_REFERRER */ ?>
<script id="tmp_referrer" type="text/x-handlebars-template">
<li class="triggerItem">
	<h4>参照元URL（リファラー）</h4>
	<div>
		<ul class="setReferrer">
			<li>
				<span><label>対象</label></span>
				<label><input type="radio" name="targetName" value="1" checked="checked">タイトル</label>&nbsp;
				<label><input type="radio" name="targetName" value="2">URL</label>
			</li>
			<li>
				<span><label>キーワード</label></span>
				<input type="text" name="keyword">
			</li>
			<li>
				<span><label>条件</label></span>
				<label><input type="radio" name="referrerCond" value="1" checked="checked">完全一致</label>&nbsp;
				<label><input type="radio" name="referrerCond" value="2">部分一致</label>&nbsp;
				<label><input type="radio" name="referrerCond" value="3">不一致</label>
			</li>
		</ul>
	</div>
</li>
</script>

<?php /* 検索キーワード｜C_AUTO_TRIGGER_SEARCH_KEY */ ?>
<script id="tmp_search_keyword" type="text/x-handlebars-template">
<li class="triggerItem">
	<h4>検索キーワード</h4>
	<div>
		<ul class="setSearchKeyword">
			<li>
				<span><label>キーワード</label></span>
				<input type="text" name="keyword">
			</li>
			<li>
				<span><label>条件</label></span>
				<radios><label><input type="radio" name="searchCond" value="1" checked="checked">完全一致</label>
				<label><input type="radio" name="searchCond" value="2">部分一致</label><br/>
				<label><input type="radio" name="searchCond" value="3">不一致（若しくは取得できなかった場合）</label></radios>
			</li>

		</ul>
	</div>
</li>
</script>
