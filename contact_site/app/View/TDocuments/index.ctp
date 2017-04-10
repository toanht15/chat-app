<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TDocuments/script'); ?>

<?php
  $params = $this->Paginator->params();
  $prevCnt = ($params['page'] - 1);
?>

<section id="document_share" ng-app="sincloApp" ng-controller="MainCtrl">
<div id='tdocument_idx' class="card-shadow">
  <div id='tdocument_add_title'>
    <div class="fLeft"><?= $this->Html->image('document_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
    <h1>資料設定<span id="sortMessage"></span></h1>
  </div>

  <div id='tdocument_menu' class="p20trl">
    <div class="fLeft" >
      <?= $this->Html->image('add.png', ['url' => ['controller'=>'TDocuments', 'action' => 'add'], 'alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30]) ?>
    </div>
  </div>

  <div id='tdocument_list' class="p20x">

    <table>
      <thead>
        <tr>
          <th width="5%">No</th>
          <th width="15%">資料</th>
          <th width="25%">資料名</th>
          <th width="40%">概要</th>
          <th width="15%">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php
          foreach((array)$documentList as $key => $val):
          $id = "";
          if ($val['TDocument']['id']) {
            $id = $val['TDocument']['id'];
          }
          $no = $prevCnt + h($key+1);
        ?>
        <tr data-id="<?=h($id)?>">
          <td class="tCenter"><?=$no?></td>
          <td class="tCenter">
            <div class = "document_image">
              <?= $this->Html->image(C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/".C_PREFIX_DOCUMENT.pathinfo(h($val['TDocument']['file_name']), PATHINFO_FILENAME).".jpg", ["width" => 210, "height" => 180,"ng-click"=>"openDocumentList3($id)"]);?>
            </div>
          </td>
          <td class="tCenter"><?=h($val['TDocument']['name'])?></td>
          <td class="tCenter"><?=h($val['TDocument']['overview'])?></td>
          <!-- <td class="tCenter"><span><?=implode("</span>、<span>",$val['TDocument']['tag'])?></span></td> -->
          <td class="p10x noClick lineCtrl">
            <div>
              <a href="<?=$this->Html->url(['controller'=>'TDocuments', 'action'=>'edit', $id])?>" class="btn-shadow greenBtn fLeft"><img src="/img/edit.png" alt="更新" width="30" height="30"></a>
              <a href="javascript:void(0)" class="btn-shadow redBtn m10r10l fRight" onclick="removeAct('<?=$id?>')"><img src="/img/trash.png" alt="削除" width="30" height="30"></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if ( count($documentList) === 0 ) :?>
          <td class="tCenter" colspan="5">保存された資料がありません</td>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

  <div id="ang-popup3">
    <div id="ang-base3">
      <div id="ang-popup-background3"></div>
      <div id="ang-popup-frame3">
        <div id="ang-popup-content3" class="document_list">
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

  <slideFramea>
    <div id="document_canvas"></div>
  </slideFramea>

  <div id="ang-popup2">
    <div id="ang-base2">
      <div id="ang-popup-background2"></div>
      <div id="ang-popup-frame2">
        <div id="ang-popup-content2" class="document_list">
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
      <div id="ang-ballons2">
      </div>
    </div>
  </div>
</section>
