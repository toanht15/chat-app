<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TDocuments/script'); ?>

<section id="document_preview" ng-app="sincloApp" ng-controller="MainCtrl">
  <div id='tdocument_idx' class="card-shadow">
    <div id='tdocument_add_title'>
      <div class="fLeft"><?= $this->Html->image('document_g.png', array('alt' => '資料設定', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
      <h1>資料設定</h1>
    </div>
    <div id='tdocument_form' class="p20x">
    <!-- 更新フォーム -->
      <?=$this->Form->create('TDocument', ['id'=>'TDocumentEntryForm', 'type' => 'file'])?>
      <?php echo $this->element('TDocuments/entry'); ?>
      <?=$this->Form->end();?>
      <!-- タグ登録フォーム -->
      <?=$this->Form->create('MDocumentTag', ['url'=>['controller' =>'TDocuments', 'action'=>'addTag'], 'id' => 'MDocumentTagAddForm']) ?>
      <?= $this->Form->input('name', ['type' => 'hidden']) ?>
      <?=$this->Form->end();?>
    </div>
  </div>
  <div id="document-preview">
    <div id="document-base">
      <div id="document-preview-background"></div>
      <div id="document-preview-frame">
        <div id="document-preview-content" class="document_list">
        <!-- /* サイドバー */ -->
        <ul id="document_share_tools">
          <li-bottom2>
            <li ng-click="openDocumentList2()">
              <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_document.png" width="40" height="40" alt=""></span>
              <p>資料切り替え</p>
            </li>
            <li ng-click="closeDocumentList()">
              <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_disconnect.png" width="40" height="40" alt=""></span>
              <p>閉じる</p>
            </li>
          </li-bottom2>
        </ul>
        <!-- /* サイドバー */ -->
        <!-- /* ツールバー */ -->
        <ul id="document_ctrl_tools">
          <li-left>
            <li class="showDescriptionBottom" data-description="前のページへ" onclick="slideJsApi2.prevPage(); return false;">
              <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_back.png" width="30" height="30" alt=""></span>
            </li>
            <li class="showDescriptionBottom" data-description="次のページへ" onclick="slideJsApi2.nextPage(); return false;">
              <span class="btn" ng-class="{{manuscriptType}}" ><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_next.png" width="30" height="30" alt=""></span>
            </li>
            <li class="showDescriptionBottom" data-description="原稿の表示/非表示" onclick="slideJsApi2.toggleManuScript(); return false;">
              <span id="scriptToggleBtn" class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_talkscript.png" width="30" height="30" alt=""></span>
            </li>
          </li-left>
        <li-center>
        <li>
          <span id="pages"></span>
        </li>
      </li-center>
      <li-right>
        <li id="scaleChoose">
          <label dir="scaleType">拡大率</label>
          <select name="scale_type" id="scaleType" onchange="slideJsApi2.cngScale(); return false;">
          <option value=""   > - </option>
          <option value="0.5"   >50%</option>
          <option value="0.75"  >75%</option>
          <option value="1"     selected>100%</option>
          <option value="1.5"   >150%</option>
          <option value="2"     >200%</option>
          <option value="2.5"   >250%</option>
          <option value="3"     >300%</option>
          <option value="4"     >400%</option>
        </select>
      </li>
      <li class="showDescriptionBottom" data-description="拡大する" onclick="slideJsApi2.zoomIn(0.25); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_plus.png" width="30" height="30" alt=""></span>
      </li>
      <li class="showDescriptionBottom" data-description="縮小する" onclick="slideJsApi2.zoomOut(0.25); return false;">
        <span class="btn"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_minus.png" width="30" height="30" alt=""></span>
      </li>
    </li-right>
  </ul>

  <!-- /* ツールバー */ -->
  <div id="manuscriptArea" style="display:none;">
    <span id="manuscript"></span>
    <span id="manuscriptCloseBtn" onclick="slideJsApi2.toggleManuScript(); return false;"></span>
  </div>

  <slideFrame2>
    <div id="document_canvas2"></div>
  </slideFrame2>

  <div id="switching-preview">
    <div id="switching-base">
      <div id="switching-preview-background"></div>
      <div id="switching-preview-frame">
        <div id="switching-preview-content" class="document_list">
          <div id="title_area2">資料一覧</div>
          <div id="search_area2">
            <?=$this->Form->input('name', ['label' => 'フィルター：', 'ng-model' => 'searchName']);?>
            <!-- <ng-multi-selector></ng-multi-selector> -->
          </div>
          <div id="list_area2">
            <ol>
              <li ng-repeat="document in searchFunc(documentList)" ng-click="changeDocument(document)">
                <div class="document_image">
                  <img ng-src="{{::document.thumnail}}" style="width:5em;height:4em">
                </div>
                <div class="document_content">
                  <h3>{{::document.name}}</h3>
                  <ng-over-view docid="{{::document.id}}" text="{{::document.overview}}" ></ng-over-view>
                  <ul><li ng-repeat="tagId in document.tags">{{::tagList[tagId]}}</li></ul>
                </div>
              </li>
            </ol>
          </div>
          <div id="btn_area2">
            <a class="btn-shadow greenBtn" ng-click="closeDocumentList2()" href="javascript:void(0)">閉じる</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>