<?php echo $this->element('Customers/documentLists') ?>

<section id="document_share" ng-app="sincloApp" ng-controller="MainCtrl" class="ng-scope">

  <!-- /* サイドバー */ -->
  <ul id="document_share_tools">
    <li-top>
      <!-- <p>ID:1234</p> -->
      <!-- <li>
        <span><img src="https://ap1.sinclo.jp/img/sync/icon_user.png" width="40" height="40" alt=""></span>
        <p>顧客情報</p>
      </li> -->
    </li-top>
    <li-bottom>
     <li ng-click="openDocumentList()">
        <span><img src="https://ap1.sinclo.jp/img/sync/icon_document.png" width="40" height="40" alt=""></span>
        <p>資料切り替え</p>
      </li>
      <li>
        <span onclick="closePopup()"><img src="https://ap1.sinclo.jp/img/sync/icon_disconnect.png" width="40" height="40" alt=""></span>
        <p>閉じる</p>
      </li>
    </li-bottom>
  </ul>
  <!-- /* サイドバー */ -->

  <!-- /* ツールバー */ -->
  <ul id="document_ctrl_tools">
    <li-left>
      <li class="showDescriptionBottom" data-description="前のページへ" onclick="pdfjsApi.prevPage(); return false;">
        <span class="btn"><img src="https://ap1.sinclo.jp/img/sync/icon_back.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="次のページへ" onclick="pdfjsApi.nextPage(); return false;">
        <span class="btn" ng-class=""><img src="https://ap1.sinclo.jp/img/sync/icon_next.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="原稿の表示/非表示" onclick="pdfjsApi.toggleManuScript(); return false;">
        <span id="scriptToggleBtn" class="btn"><img src="https://ap1.sinclo.jp/img/sync/icon_talkscript.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="ページ再描画" onclick="pdfjsApi.render(); return false;">
        <span class="btn"><img src="https://ap1.sinclo.jp/img/sync/icon_reconnect.png" width="30" height="30" alt=""></span>
      </li>
    </li-left>
    <li-center>
      <li>
        <span id="pages">2/ 54</span>
      </li>
    </li-center>
    <li-right>
      <li id="scaleChoose">
        <label dir="scaleType">拡大率</label>
        <select name="scale_type" id="scaleType" onchange="pdfjsApi.cngScale(); return false;">
          <option value=""> - </option>
          <option value="0.5">50%</option>
          <option value="0.75">75%</option>
          <option value="1" selected="">100%</option>
          <option value="1.5">150%</option>
          <option value="2">200%</option>
          <option value="2.5">250%</option>
          <option value="3">300%</option>
          <option value="4">400%</option>
        </select>
      </li>
      <li class="showDescriptionBottom" data-description="拡大する" onclick="pdfjsApi.zoomIn(0.25); return false;">
        <span class="btn"><img src="https://ap1.sinclo.jp/img/sync/icon_plus.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="縮小する" onclick="pdfjsApi.zoomOut(0.25); return false;">
        <span class="btn"><img src="https://ap1.sinclo.jp/img/sync/icon_minus.png" width="30" height="30" alt=""></span>
      </li>
    </li-right>
  </ul>
  <!-- /* ツールバー */ -->
  <div id="manuscriptArea" style="display: none; position: relative; width: calc(100% - 150px); left: 125px; top: 65px;" class="ui-draggable ui-draggable-handle">
    <span id="manuscript"></span>
    <span id="manuscriptCloseBtn" onclick="pdfjsApi.toggleManuScript(); return false;"></span>
  </div>

  <div id="tabStatusMessage">別の作業をしています</div>

  <div id="ang-popup">
    <div id="ang-base">
      <div id="ang-popup-background"></div>
      <div id="ang-popup-frame">
        <div id="ang-popup-content" class="document_list">
          <div id="title_area">資料一覧</div>
          <div id="search_area">
            <div class="input text"><label for="name">フィルター：</label><input name="data[name]" ng-model="searchName" type="text" id="name" class="ng-pristine ng-untouched ng-valid"></div>            <!-- <ng-multi-selector></ng-multi-selector> -->
          </div>
          <div id="list_area">
            <ol>
              <!-- ngRepeat: document in searchFunc(documentList) -->
            </ol>
          </div>
        </div>
      </div>
      <div id="ang-ballons">
      </div>
    </div>
  </div>

  <div id="desc-balloon" style="top: 50px; left: 863.984px; display: block;">拡大する</div>
</section>

 <?=  $this->Form->create('History',['id' => 'historySearch','type' => 'post','url' => ['controller' => 'Histories','action' => 'index']]); ?>
  <ul>
    <?= $this->Form->hidden('start_day',['label'=> false,'div' => false]); ?>
    <?= $this->Form->hidden('finish_day',['label'=> false,'div' => false]); ?>
    <?= $this->Form->hidden('period',['label'=> false,'div' => false]); ?>
    <li>
      <p><span>ipアドレス</span></p>
      <span><?= $this->Form->input('ip_address',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>会社名</span></p>
      <span><?= $this->Form->input('company_name',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
    <p><span>名前</span></p>
      <span><?= $this->Form->input('customer_name',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>電話番号</span></p>
      <span><?= $this->Form->input('telephone_number',['label'=>false,'div' => false]) ?></span>
    </li>
    <li>
      <p><span>メールアドレス</span></p>
      <span><?= $this->Form->input('mail_address',['label'=>false,'div' => false]) ?></span>
    </li>
    <?php if ($coreSettings[C_COMPANY_USE_CHAT]): ?>
      <li>
        <p><span>チャット担当者</span></p>
        <span><?= $this->Form->input('THistoryChatLog.responsible_name',['label'=>false,'div' => false]) ?></span>
      </li>
      <li>
        <p><span>成果</span></p>
        <span><label><?= $this->Form->input('THistoryChatLog.achievement_flg',['type' => 'select', 'empty' => ' ', 'options' => $achievementType, 'legend' => false, 'separator' => '</label><br><label>', 'label'=>false,'div' => false]) ?></label></span>
      </li>
      <li>
        <p><span>チャット内容</span></p>
        <span><?= $this->Form->input('THistoryChatLog.message',['label'=>false,'div' => false]) ?></span>
      </li>
    <?php endif; ?>
  </ul>
<?= $this->Form->end(); ?>