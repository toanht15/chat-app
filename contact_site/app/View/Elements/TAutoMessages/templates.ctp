<?php /* 滞在時間｜C_AUTO_TRIGGER_STAY_TIME */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_STAY_TIME?>'" class="setStayTime">
	<li>
		<?=$this->AutoMessage->radio('stayTimeCheckType')?>
	</li>
	<li>
		<?=$this->AutoMessage->select('stayTimeType')?>
	</li>
	<li>
		<span><label>時間</label></span>
		<input type="text" class="tRight" ng-pattern="<?=C_MATCH_RULE_NUM_1?>" ng-model="setItem.stayTimeRange" name="stayTimeRange" required="">
	</li>
</ul>

<?php /* 訪問回数｜C_AUTO_TRIGGER_VISIT_CNT */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_VISIT_CNT?>'" class="setVisitCnt">
		<li>
			<span><label>訪問回数</label></span>
			<input type="text" class="tRight" ng-pattern="<?=C_MATCH_RULE_NUM_2?>" ui-validate-watch=" 'setItem.visitCntCond' " ui-validate="{isVisitCntRule : 'main.isVisitCntRule($value, setItem.visitCntCond)' }" ng-model="setItem.visitCnt" name="visitCnt" required="">&nbsp;回
		</li>
		<li>
			<?=$this->AutoMessage->radio('visitCntCond')?>
		</li>
	</ul>
<?php /* ページ｜C_AUTO_TRIGGER_STAY_PAGE */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_STAY_PAGE?>'"  class="setStayPage">
	<li>
		<?=$this->AutoMessage->radio('targetName')?>
	</li>
	<li>
		<span><label>キーワード</label></span>
		<input type="text" ng-model="setItem.keyword" maxlength="50" name="keyword" required="">
	</li>
	<li>
		<?=$this->AutoMessage->radio('stayPageCond')?>
	</li>
</ul>

<?php /* 曜日・時間｜C_AUTO_TRIGGER_DAY_TIME */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_DAY_TIME?>'" class="setDayTime">
	<li>
		<?=$this->AutoMessage->checkbox('day')?>
	</li>
	<li>
		<?=$this->AutoMessage->radio('timeSetting')?>
	</li>
	<li>
		<div class="input-group clockpicker bt0">
			<input type="text" class="form-control" name="startTime" ng-pattern="<?=C_MATCH_RULE_TIME?>" ng-model="setItem.startTime" ng-disabled="setItem.timeSetting == '2'" ng-required="setItem.timeSetting == '1'">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-time"></span>
			</span>
		</div>
		<div class="bt0"><span>～</span></div>
		<div class="input-group clockpicker bt0">
			<input type="text" class="form-control" name="endTime" ng-pattern="<?=C_MATCH_RULE_TIME?>" ng-model="setItem.endTime" ng-disabled="setItem.timeSetting == '2'" ng-required="setItem.timeSetting == '1'">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-time"></span>
			</span>
		</div>
	</li>
	<script type="text/javascript">
		$('.clockpicker').clockpicker({
			donetext:'設定',
			placement: 'original',
			align: 'original'
		});
	</script>
</ul>

<?php /* 参照元URL（リファラー）｜C_AUTO_TRIGGER_REFERRER */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_REFERRER?>'" class="setReferrer">
	<li>
		<span><label>URL</label></span>
		<input type="text" ng-model="setItem.keyword" maxlength="100" name="keyword" required="">
	</li>
	<li>
		<?=$this->AutoMessage->radio('referrerCond')?>
	</li>
</ul>

<?php /* 検索キーワード｜C_AUTO_TRIGGER_SEARCH_KEY */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_SEARCH_KEY?>'" class="setSearchKeyword">
	<li>
		<span><label>キーワード</label></span>
		<input type="text" ng-model="setItem.keyword" name="keyword" maxlength="20" required="">
	</li>
	<li>
		<?=$this->AutoMessage->radio('searchCond')?>
	</li>
</ul>
