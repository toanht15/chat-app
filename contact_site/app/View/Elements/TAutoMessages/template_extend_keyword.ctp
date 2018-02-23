<li>
  <span style="padding-top:5px;"><label><?= $label ?></label></span>
  <div class="keywordWrapper">
    <label class="checkboxLabel"><input type="checkbox" ng-model="setItem.keyword_contains_enabled" ng-init="setItem.keyword_contains_enabled ? setItem.keyword_contains_enabled = setItem.keyword_contains_enabled : setItem.keyword_contains_enabled = (setItem.keyword_contains !== '')"/>対象とするキーワードを設定する</label>
    <div class="wrap pb" ng-if="setItem.keyword_contains_enabled">
      <div class="containsSetting">
        <input type="text" ng-model="setItem.keyword_contains" name="keyword_contains" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_contains_type" class="searchKeywordContainsTypeSelect">
          <option value="1">をすべて含む</option>
          <option value="2">のいずれかを含む</option>
        </select>
      </div>
      <s style="margin-top:0.4em">※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
      <?php if(strcmp($label, 'URL') !== 0): ?>
        <s style="color:#F57E7E;" >※スペース（空白）を含むキーワードを設定する場合はキーワード全体を半角のダブルクォーテーション（"）で囲んでください。</s>
        <s style="color:#F57E7E;" >（例："山田 花子"）</s>
      <?php endif; ?>
    </div>
    <label class="checkboxLabel"><input type="checkbox" class="last" ng-model="setItem.keyword_exclusions_enabled" ng-init="setItem.keyword_exclusions_enabled ? setItem.keyword_exclusions_enabled = setItem.keyword_exclusions_enabled : setItem.keyword_exclusions_enabled = (setItem.keyword_exclusions !== '')"/>対象外とするキーワードを設定する</label>
    <div class="wrap" ng-if="setItem.keyword_exclusions_enabled">
      <div class="exclusionSetting">
        <span class="andLabel" ng-if="setItem.keyword_contains_enabled && setItem.keyword_exclusions_enabled">かつ</span>
        <input type="text" ng-model="setItem.keyword_exclusions" name="keyword_exclusions" maxlength="100" ng-required="!((setItem.keyword_contains.length > 0) || (setItem.keyword_exclusions.length > 0))">
        <select ng-model="setItem.keyword_exclusions_type" class="searchKeywordExclusionsTypeSelect">
          <option value="1">をすべて含む</option>
          <option value="2">のいずれかを含む</option>
        </select>
      </div>
      <s style="margin-top:0.4em" ng-class="{bothKeyword:setItem.keyword_contains_enabled && setItem.keyword_exclusions_enabled}">※複数キーワードを指定する場合はスペースで区切って入力して下さい。</s>
      <?php if(strcmp($label, 'URL') !== 0): ?>
        <s style="color:#F57E7E;" ng-class="{bothKeyword:setItem.keyword_contains_enabled && setItem.keyword_exclusions_enabled}">※スペース（空白）を含むキーワードを設定する場合はキーワード全体を半角のダブルクォーテーション（"）で囲んでください。</s>
        <s style="color:#F57E7E;" ng-class="{bothKeyword:setItem.keyword_contains_enabled && setItem.keyword_exclusions_enabled}">（例："山田 花子"）</s>
      <?php endif; ?>
    </div>
  </div>
</li>