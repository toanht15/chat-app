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
      <div>
        <p>訪問回数 </p>
        <input type="number" ng-model="setItem.visitCnt" name="visitCnt" class="visitCnt" ng-pattern="<?=C_MATCH_RULE_NUM_2?>" ui-validate-watch="'setItem.visitCntCond'" ui-validate="{isVisitCntRule : 'main.isVisitCntRule($value, setItem.visitCntCond)'}" min="1" max="100" required><p>回</p>
        <select style="" ng-model="setItem.visitCntCond">
          <option value="4">以上</option>
          <option value="1">に一致する場合</option>
          <option value="2">以上の場合</option>
          <option value="3">未満の場合</option>
        </select>
        <input ng-if="setItem.visitCntCond == '4'" ng-model="setItem.visitCntMax" type="number" name="visitCntMax" class="visitCntMax" ng-pattern="<?=C_MATCH_RULE_NUM_2?>" min="{{setItem.visitCnt + 1}}" max="100" required>　<p ng-if="setItem.visitCntCond == '4'" style="margin-left: -10px;">回 未満の場合</p>
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
<!--訪問者の端末-->
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_VISITOR_DEVICE?>'" class="setVisitorDevice" >
  <li>
    <span style="margin-top: 3px;"> 端末</span>
    <label class="pointer pc">
      <input ng-model="setItem.pc" name="device" type="checkbox" ng-required="!setItem.pc && !setItem.smartphone && !setItem.tablet">PC
    </label>
    <label class="pointer smartphone">
      <input ng-model="setItem.smartphone" name="device" type="checkbox">スマートフォン
    </label>
    <label class="pointer tablet">
      <input ng-model="setItem.tablet" name="device" type="checkbox">タブレット
    </label>
  </li>
</ul>
<!--企業情報-->
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_COMPANY_INFORMATION?>'" class="setCompanyInformation" >
  <li>
    <span style="margin-top: 3px;"> 条件</span>
    <select name="companyInfoGetInfoStatus" ng-model="setItem.getInfoStatus">
      <option value="1">企業情報が取得できた場合</option>
      <option value="2">企業情報が取得できなかった場合</option>
      <option value="3">特定の企業情報が取得できた場合</option>
    </select>
  </li>
  <li ng-if="setItem.getInfoStatus === '3'">
    <span></span>
    <label class="pointer">
      <input type="radio" name="companyInfoSettingCondition" ng-model="setItem.settingCondition" value="1">全て一致
    </label>
    <label class="pointer">
      <input type="radio" name="companyInfoSettingCondition" ng-model="setItem.settingCondition" value="2">いずれかが一致
    </label>
  </li>

  <li ng-if="setItem.getInfoStatus === '3'" ng-repeat="(index, setting) in setItem.settings track by $index">
    <select class="infoList" ng-model="setting.type">
      <option value="1">法人番号</option>
      <option value="2">企業名</option>
      <option value="3">代表者</option>
      <option value="4">設立</option>
      <option value="5">売上高</option>
      <option value="6">資本金</option>
      <option value="7">従業員数</option>
      <option value="8">業種</option>
      <option value="9">上場区分</option>
      <option value="10">企業URL</option>
      <option value="11">LBCコード</option>
      <option value="12">本社住所</option>
      <option value="13">電話番号</option>
      <option value="14">FAX番号</option>
      <option value="15">IPアドレス</option>
    </select>

    <input ng-if="main.usePulldownList.indexOf(setting.type) === -1" type="text" class="company-info-input" ng-model="setting.content">

    <select ng-if="setting.type === '5'" class="company-info-input" ng-model="setting.content" ng-init="setting.content = '1億未満'">
      <option ng-repeat="(index, gross) in main.companyInfoMap.grossList track by $index" value="{{gross}}">{{gross}}</option>
    </select>

    <select ng-if="setting.type === '6'" class="company-info-input" ng-model="setting.content" ng-init="setting.content = '1千万円未満'">
      <option ng-repeat="(index, capital) in main.companyInfoMap.capitalList track by $index" value="{{capital}}">{{capital}}</option>
    </select>

    <select ng-if="setting.type === '7'" class="company-info-input" ng-model="setting.content" ng-init="setting.content = '1人以上5人未満'">
      <option ng-repeat="(index, employees) in main.companyInfoMap.employeesList track by $index" value="{{employees}}">{{employees}}</option>
    </select>

    <select ng-if="setting.type === '8'" class="company-info-input" ng-model="setting.content" ng-init="setting.content = '農業'">
      <option ng-repeat="(index, category) in main.companyInfoMap.industrialCategoryList track by $index" value="{{category}}">{{category}}</option>
    </select>

    <select ng-if="setting.type === '9'" class="company-info-input" ng-model="setting.content" ng-init="setting.content = '東証一部'">
      <option ng-repeat="(index, ipoType) in main.companyInfoMap.ipoTypeList track by $index" value="{{ipoType}}">{{ipoType}}</option>
    </select>


    <select ng-if="main.usePulldownList.indexOf(setting.type) === -1" class="match-condition" ng-model="setting.matchCondition">
      <option value="1">完全一致</option>
      <option value="2">部分一致</option>
      <option value="3">不一致</option>
    </select>

    <div class="btnBlock">
      <a><?= $this->Html->image('add.png', array(
          'alt' => '追加',
          'width' => 25,
          'height' => 25,
          'class' => 'btn-shadow disOffgreenBtn hearingBtn',
          'style' => 'padding: 2px',
          'ng-click' => 'main.addCompanyInfoSetting()'
        )) ?></a><a ng-if="index != 0"><?= $this->Html->image('dustbox.png', array(
          'alt' => '削除',
          'width' => 25,
          'height' => 25,
          'class' => 'btn-shadow redBtn deleteBtn hearingBtn',
          'style' => 'padding: 2px',
          'ng-click' => 'main.removeCompanyInfoSetting(index)'
        )) ?></a>
    </div>
  </li>
</ul>

<!--訪問ユーザ情報-->
<ul ng-if="itemType == '<?=C_AUTO_TRIGGER_VISITOR_INFORMATION?>'" class="setCompanyInformation" >
  <li>
    <span style="margin-top: 3px;"> 条件</span>
    <select name="companyInfoGetInfoStatus" ng-model="setItem.getInfoStatus">
      <option value="1">訪問ユーザ情報が取得できた場合</option>
      <option value="2">訪問ユーザ情報が取得できなかった場合</option>
      <option value="3">特定の訪問ユーザ情報が取得できた場合</option>
    </select>
  </li>
  <li ng-if="setItem.getInfoStatus === '3'">
    <span></span>
    <label class="pointer">
      <input type="radio" name="visitorInfoSettingCondition" ng-model="setItem.settingCondition" value="1">全て一致
    </label>
    <label class="pointer">
      <input type="radio" name="visitorInfoSettingCondition" ng-model="setItem.settingCondition" value="2">いずれかが一致
    </label>
  </li>
  <li ng-if="setItem.getInfoStatus === '3'" ng-repeat="(index, setting) in setItem.settings track by $index">
    <select class="infoList" ng-model="setting.name">
      <option ng-repeat="(index, option) in main.visitorInfoList track by $index" value="{{option}}">{{option}}</option>
    </select>
      <input type="text" class="company-info-input"  ng-model="setting.content">

      <select class="match-condition" ng-model="setting.matchCondition">
        <option value="1">完全一致</option>
        <option value="2">部分一致</option>
        <option value="3">不一致</option>
      </select>

      <div class="btnBlock">
        <a><?= $this->Html->image('add.png', array(
            'alt' => '追加',
            'width' => 25,
            'height' => 25,
            'class' => 'btn-shadow disOffgreenBtn hearingBtn',
            'style' => 'padding: 2px',
            'ng-click' => 'main.addVisitorInfoSetting()'
          )) ?></a><a ng-if="index != 0"><?= $this->Html->image('dustbox.png', array(
            'alt' => '削除',
            'width' => 25,
            'height' => 25,
            'class' => 'btn-shadow redBtn deleteBtn hearingBtn',
            'style' => 'padding: 2px',
            'ng-click' => 'main.removeVisitorInfoSetting(index)'
          )) ?></a>
      </div>
  </li>
</ul>