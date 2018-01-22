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
    <span style="padding-top:5px;"><label>キーワード</label></span>
    <div class="keywordWrapper">
      <div class="containsSetting">
        <input type="text" ng-model="setItem.keyword_contains" name="keyword_contains" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_contains_type" class="searchKeywordContainsTypeSelect">
          <option value="1">をすべて含む</option>
          <option value="2">のいずれかを含む</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
      <p>かつ</p>
      <div class="exclusionSetting">
        <input type="text" ng-model="setItem.keyword_exclusions" name="keyword_exclusions" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_exclusions_type" class="searchKeywordExclusionsTypeSelect">
          <option value="1">をすべて含まない</option>
          <option value="2">のいずれかを含まない</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
    </div>
  </li>
  <li>
    <?=$this->AutoMessage->radio('stayPageCond')?>
  </li>
  <div class="explainWrapper">
    <s>※キーワードに入力した内容を完全一致で扱うか、部分一致で扱うかを選択します。</s>
    <s>　（完全一致を選択した場合、ワイルドカードとして「*」の使用が可能です）</s>
  </div>
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
    <span style="padding-top:5px;"><label>URL</label></span>
    <div class="keywordWrapper">
      <div class="containsSetting">
        <input type="text" ng-model="setItem.keyword_contains" name="keyword_contains" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_contains_type" class="searchKeywordContainsTypeSelect">
          <option value="1">をすべて含む</option>
          <option value="2">のいずれかを含む</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
      <p>かつ</p>
      <div class="exclusionSetting">
        <input type="text" ng-model="setItem.keyword_exclusions" name="keyword_exclusions" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_exclusions_type" class="searchKeywordExclusionsTypeSelect">
          <option value="1">をすべて含まない</option>
          <option value="2">のいずれかを含まない</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
    </div>
  </li>
  <li>
    <?=$this->AutoMessage->radio('referrerCond')?>
  </li>
  <div class="explainWrapper">
    <s>※キーワードに入力した内容を完全一致で扱うか、部分一致で扱うかを選択します。</s>
    <s>　（完全一致を選択した場合、ワイルドカードとして「*」の使用が可能です）</s>
  </div>
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

<?php /* 発言内容｜C_AUTO_TRIGGER_SPEECH_CONTENT */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_SPEECH_CONTENT?>'" class="setSpeechKeyword">
  <li>
    <span style="padding-top:5px;"><label>発言内容</label></span>
    <div class="keywordWrapper">
      <div class="containsSetting">
        <input type="text" ng-model="setItem.keyword_contains" name="keyword_contains" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_contains_type" class="searchKeywordContainsTypeSelect">
          <option value="1">をすべて含む</option>
          <option value="2">のいずれかを含む</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
      <p>かつ</p>
      <div class="exclusionSetting">
        <input type="text" ng-model="setItem.keyword_exclusions" name="keyword_exclusions" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_exclusions_type" class="searchKeywordExclusionsTypeSelect">
          <option value="1">をすべて含まない</option>
          <option value="2">のいずれかを含まない</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
    </div>
  </li>
  <li>
    <?=$this->AutoMessage->radio('speechContentCond')?>
  </li>
  <div class="explainWrapper">
    <s>※キーワードに入力した内容を完全一致で扱うか、部分一致で扱うかを選択します。</s>
    <s>　（完全一致を選択した場合、ワイルドカードとして「*」の使用が可能です）</s>
  </div>
  <li>
    <span><label>自動返信までの間隔</label></span>
    <label for="triggerTimeSec">
      <input type="number" ng-model="setItem.triggerTimeSec" ng-pattern="<?=C_MATCH_RULE_NUM_3?>" name="triggerTimeSec" min="1" max="60" maxlength="2" required="" style="width:6em">
      秒後
    </label>
  </li>
  <li>
    <?=$this->AutoMessage->radio('speechTriggerCond')?>
  </li>
</ul>

<?php /* 最初に訪れたページ｜C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST?>'"  class="setStayPage">
  <li>
    <?=$this->AutoMessage->radio('targetName')?>
  </li>
  <li>
    <span style="padding-top:5px;"><label>キーワード</label></span>
    <div class="keywordWrapper">
      <div class="containsSetting">
        <input type="text" ng-model="setItem.keyword_contains" name="keyword_contains" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_contains_type" class="searchKeywordContainsTypeSelect">
          <option value="1">をすべて含む</option>
          <option value="2">のいずれかを含む</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
      <p>かつ</p>
      <div class="exclusionSetting">
        <input type="text" ng-model="setItem.keyword_exclusions" name="keyword_exclusions" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_exclusions_type" class="searchKeywordExclusionsTypeSelect">
          <option value="1">をすべて含まない</option>
          <option value="2">のいずれかを含まない</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
    </div>
  </li>
  <li>
    <?=$this->AutoMessage->radio('stayPageCond')?>
  </li>
  <div class="explainWrapper">
    <s>※キーワードに入力した内容を完全一致で扱うか、部分一致で扱うかを選択します。</s>
    <s>　（完全一致を選択した場合、ワイルドカードとして「*」の使用が可能です）</s>
  </div>
</ul>

<?php /* 前のページ｜C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS?>'"  class="setStayPage">
  <li>
    <?=$this->AutoMessage->radio('targetName')?>
  </li>
  <li>
    <span style="padding-top:5px;"><label>キーワード</label></span>
    <div class="keywordWrapper">
      <div class="containsSetting">
        <input type="text" ng-model="setItem.keyword_contains" name="keyword_contains" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_contains_type" class="searchKeywordContainsTypeSelect">
          <option value="1">をすべて含む</option>
          <option value="2">のいずれかを含む</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
      <p>かつ</p>
      <div class="exclusionSetting">
        <input type="text" ng-model="setItem.keyword_exclusions" name="keyword_exclusions" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_exclusions_type" class="searchKeywordExclusionsTypeSelect">
          <option value="1">をすべて含まない</option>
          <option value="2">のいずれかを含まない</option>
        </select>
      </div>
      <s>※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
    </div>
  </li>
  <li>
    <?=$this->AutoMessage->radio('stayPageCond')?>
  </li>
  <div class="explainWrapper">
    <s>※キーワードに入力した内容を完全一致で扱うか、部分一致で扱うかを選択します。</s>
    <s>　（完全一致を選択した場合、ワイルドカードとして「*」の使用が可能です）</s>
  </div>
</ul>

<?php /* 営業時間｜C_AUTO_TRIGGER_OPERATING_HOURS */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_OPERATING_HOURS?>'" class="setStayPage" >
  <li>
    <?=$this->AutoMessage->radio('operatingHoursTime');?>
    <input type = "hidden" name = "operatingHour">
    <?php if($operatingHourData == 2) { ?>
      <input type="hidden" name = "notOperatingHour" ng-model="setItem.operatingHour" ng-required="setItem.operatingHoursTime != '3'">
    <?php } ?>
  </li>
</ul>
