<meta charset="utf-8" />
<script type="text/javascript">

$( function() {
  document.getElementById("categoryTabs").style.display="";
  document.getElementById("popup-bg").className="category-tab-binding";
  document.getElementById("popup-title").className="category-tab-binding";
  document.getElementById("popup-main").className="category-tab-binding";
  document.getElementById("popup-frame").className="category-tab-binding";
  $( "#categoryTabs" ).tabs();
  $(".popup-frame").css('height', '');
  //インデックスの初期値を挿入暫定的に0
  document.getElementById("select_tab_index").value = 0;
  document.getElementById("mode_flg").value = 0;
  document.getElementById("keytime").value = 0;
  document.getElementById("searchkeytime").value = 0;
  $("#categoryTabs").bind('tabsactivate', function(event, ui) {
      var index = ui.newTab.index();
      // クリックされたタブのインデックスをhiddenに持っておく
      document.getElementById("select_tab_index").value = index;
  });
  //ページ読み込み時は通常モード
  document.getElementById("serect_tab_mode").style.display="";
  document.getElementById("word_search_mode").style.display="none";
});

</script>



<div id="categoryTabs">
  <input type="search" ng-model="searchWord" id="wordSearchCond" size="35" placeholder="検索する文字を入力してください"/>
  <input type="hidden" id="mode_flg" value="">
  <input type="hidden" id="keytime" value="">
  <input type="hidden" id="searchkeytime" value="">
  <div id="serect_tab_mode">
    <!-- 通常モード -->
    <input type="hidden" id="select_tab_index" value="">
    <ul class="categoryTabStyle">
      <?php for ($i = 0; $i < count((array)$dictionaryCategoriesList); $i++) { ?>
        <li class="tabStyle"><a data-id="<?=$dictionaryCategoriesList[$i]['id']?>" href="#categoryTabs-<?=$i?>"><?= h($dictionaryCategoriesList[$i]['label'])?></a></li>
      <?php } ?>
    </ul>
    <div id="chatCategory" class="chatCategory">
      <?php for ($i = 0; $i < count((array)$dictionaryCategoriesList); $i++) { ?>
      <div class="onTabs" id="categoryTabs-<?=$i?>">
            <?php if ( count((array)$dictionaryList[$i]) !== 0) {?>
            <ul class="fRight" id="wordList<?=$i?>">
              <div class="wordBorder">
              <?php foreach ( (array)$dictionaryList[$i]as $key => $val ) {?>
                <li ng-repeat="item in entryWordSearch(entryWordList)" id="item<?=$val['id']?>" class="dictionaryWord ng-binding ng-scope <?php if($key == 0){ echo "dictionarySelected".$i;}?>">
                  <?php echo nl2br(h($val['label']))?>
                </li>
              <?php }?>
              </div>
            </ul>
            <?php }else{?>
            <ul class="fRight" id="wordListoff">
              <!--
              <li style="color:#ff7b7b">[設定] > [定型文] から<br>メッセージを登録してください</li>
               -->
            </ul>
            <?php }?>
      </div>
      <?php } ?>
    </div>
    <!-- 通常モード -->
  </div>
  <div id="word_search_mode">
    <!-- 検索モード -->
    <div id="categoryTabs-ALL">
        <ul class="fRight" id="allWordList">
          <div class="wordBorder">
            <div id="allScroll">
              <?php for ($i = 0; $i < count((array)$dictionaryCategoriesList); $i++) { ?>
                <?php foreach ( (array)$dictionaryList[$i]as $key => $val ) {?>
                  <li ng-repeat="item in entryWordSearch(entryWordList)" id="searchItem<?=$val['id']?>" class="dictionaryWord ng-binding ng-scope">
                    <?php echo nl2br(h($val['label']))?>
                  </li>
                <?php }?>
              <?php } ?>
            </div>
          </div>
        </ul>
    </div>
    <!-- 検索モード -->
  </div>
  <div>
    <span class="pre">
      ＊左右の矢印キー(←)(→)でカテゴリの切り替えが可能です。
      ＊上下の矢印キー(↑)(↓)で定型文の切り替えが可能です。
    </span>
  </div>
</div>