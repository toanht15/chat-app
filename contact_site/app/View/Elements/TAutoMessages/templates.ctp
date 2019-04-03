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
      <div style="border-top: none; display: flex; height: 30px; padding: 0; margin-top: -8px;">
        <p>訪問回数 </p>
        <input type="number" ng-model="setItem.visitCnt" name="visitCnt" ng-pattern="<?=C_MATCH_RULE_NUM_2?>" ui-validate-watch="'setItem.visitCntCond'" ui-validate="{isVisitCntRule : 'main.isVisitCntRule($value, setItem.visitCntCond)'}" style="width: 6em; margin-left: 10px; height: 32px; padding-bottom: 0;" min="1" max="100" required><p>回</p>
        <select style="margin-left: 10px; height: 24px; margin-top: 8px; font-size: 12px; padding-bottom: 3px;" ng-model="setItem.visitCntCond">
          <option value="4">以上</option>
          <option value="1">に一致する場合</option>
          <option value="2">以上の場合</option>
          <option value="3">未満の場合</option>
        </select>
        <input ng-if="setItem.visitCntCond == '4'" ng-model="setItem.visitCntMax" type="number" name="visitCntMax" ng-pattern="<?=C_MATCH_RULE_NUM_2?>" style="width: 6em; margin-left: 18px; height: 32px; padding-bottom: 0;" min="{{setItem.visitCnt + 1}}" max="100" required>　<p ng-if="setItem.visitCntCond == '4'" style="margin-left: -10px;">回 未満の場合</p>
      </div>
  </li>
  </ul>
<?php /* ページ｜C_AUTO_TRIGGER_STAY_PAGE */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_STAY_PAGE?>'"  class="setStayPage">
  <li>
    <?=$this->AutoMessage->radio('targetName')?>
  </li>
  <?=$this->element('TAutoMessages/template_extend_keyword', ['label' => 'キーワード'])?>
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
  <?=$this->element('TAutoMessages/template_extend_keyword', ['label' => 'URL'])?>
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
    <input type="text" ng-model="setItem.keyword" name="keyword" maxlength="300" required="">
  </li>
  <li>
    <?=$this->AutoMessage->radio('searchCond')?>
  </li>
</ul>

<?php /* 発言内容｜C_AUTO_TRIGGER_SPEECH_CONTENT */ ?>
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_SPEECH_CONTENT?>'" class="setSpeechKeyword">
  <?=$this->element('TAutoMessages/template_extend_keyword', ['label' => '発言内容'])?>
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
  <?=$this->element('TAutoMessages/template_extend_keyword', ['label' => 'キーワード'])?>
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
  <?=$this->element('TAutoMessages/template_extend_keyword', ['label' => 'キーワード'])?>
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

<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_VISITOR_DEVICE?>'" class="setStayPage" >
  <li>
    <span style="margin-top: 3px;"> 端末</span>
    <label class="pointer">
      <input ng-model="setItem.pc" name="device" type="checkbox" ng-required="!setItem.pc && !setItem.smartphone && !setItem.tablet">PC
    </label>
    <label style="margin-left: 20px;">
      <input ng-model="setItem.smartphone" name="device" type="checkbox">スマートフォン
    </label>
    <label style="margin-left: 20px;">
      <input ng-model="setItem.tablet" name="device" type="checkbox">タブレット
    </label>
  </li>
</ul>