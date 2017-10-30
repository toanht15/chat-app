<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TDocuments/script'); ?>

<?php
$params = $this->Paginator->params();
$prevCnt = ($params['page'] - 1);
?>

<section id="document_preview" ng-app="sincloApp" ng-controller="MainController">
  <div id='tdocument_idx' class="card-shadow">
    <div id='tdocument_add_title'>
      <div class="fLeft"><?= $this->Html->image('document_g.png', array('alt' => 'ユーザー管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
      <h1>資料設定<span id="sortMessage"></span></h1>
    </div>

    <div id='tdocument_menu' class="p20trl">
      <div class="fLeft" >
<!--
        <?= $this->Html->image('add.png', ['url' => ['controller'=>'TDocuments', 'action' => 'add'], 'alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 30, 'height' => 30]) ?>
 -->
        <div class="btnSet">
          <span style="display:inline; padding: 0;">
            <a style="text-decoration: none;">
              <?= $this->Html->image('add.png', array(
                  'alt' => '登録',
                  'id'=>'tdocument_add_btn',
                  'class' => 'btn-shadow disOffgreenBtn commontooltip',
                  'data-text' => '新規追加',
                  'data-balloon-position' => '36',
                  'width' => 45,
                  'height' => 45)) ?>
            </a>
          </span>
          <span style="display:inline; padding: 0;">
            <a>
              <?= $this->Html->image('dustbox.png', array(
                  'alt' => '削除',
                  'id'=>'tdocument_dustbox_btn',
                  'class' => 'btn-shadow disOffgrayBtn commontooltip',
                  'data-text' => '削除する',
                  'data-balloon-position' => '35',
                  'width' => 45,
                  'height' => 45)) ?>
            </a>
          </span>
        </div>
        <!-- 資料設定の並び替えモード -->
        <div class="tabpointer">
          <label class="pointer">
            <?= $this->Form->checkbox('sort', array('onchange' => 'toggleSort()')); ?><span id="sortText">並び替え</span><span id="sortTextMessage" style="display: none; font-size: 1.1em; color: rgb(192, 0, 0); font-weight: bold; ">（！）並び替え中（保存する場合はチェックを外してください）</span>
          </label>
        </div>
        <!-- 資料設定の並び替えモード -->
      </div>
    </div>

    <div id='tdocument_list' class="p20x">

      <table>
        <thead>
        <tr>
<!-- UI/UX統合対応start -->
          <th width=" 5%">
            <input type="checkbox" name="allCheck" id="allCheck" >
            <label for="allCheck"></label>
          </th>
<!-- UI/UX統合対応end -->
          <th width="5%">No</th>
          <th width="15%">資料</th>
          <th width="25%">資料名</th>
          <th width="40%">概要</th>
<!--
          <th width="15%">操作</th>
 -->
        </tr>
        </thead>
        <tbody class="sortable">
        <?php
        foreach((array)$documentList as $key => $val):
          $id = "";
          if ($val['TDocument']['id']) {
            $id = $val['TDocument']['id'];
          }
          $no = $prevCnt + h($key+1);
          ?>
          <tr class="pointer" data-id="<?=h($id)?>" data-sort="<?=$val['TDocument']['sort']?>" onclick="openEdit(<?=$id;?>)">
<!-- UI/UX統合対応start -->
            <td class="tCenter" onclick="event.stopPropagation();" width=" 5%">
              <input type="checkbox" name="selectTab" id="selectTab<?=$key?>" value="<?=$val['TDocument']['id']?>">
              <label for="selectTab<?=$key?>"></label>
            </td>
<!-- UI/UX統合対応end -->
            <td class="tCenter" width=" 5%"><?=$no?></td>
            <td class="tCenter" width="15%">
              <div class = "document_image" ng-click="$event.stopPropagation(); openDocumentList3(<?=$id?>)">
                <?php
                $settings = (!empty($val['TDocument']['settings'])) ? (array)json_decode($val['TDocument']['settings']) : [];
                $rotation = (!empty($settings['rotation'])) ? $settings['rotation'] : 0;
                $matrix = "transform: matrix( 1, 0, 0, 1, 0, 0);";
                switch ((int)$rotation) {
                  case 90:
                    $matrix = "transform: matrix( 0, 0.75, -1, 0, 0, 0);";
                    break;
                  case 180:
                    $matrix = "transform: matrix(1, 0, 0, -1, 0, 0);";
                    break;
                  case 270:
                    $matrix = "transform: matrix( 0, -0.75, 1, 0, 0, 0);";
                    break;
                }
                ?>
                <?= $this->Html->image(C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/".C_PREFIX_DOCUMENT.pathinfo(h($val['TDocument']['file_name']), PATHINFO_FILENAME).".jpg", ['style' => $matrix]);?>
              </div>
            </td>
            <td class="tCenter" width="25%"><?=h($val['TDocument']['name'])?></td>
            <td class="tCenter" width="40%"><?=h($val['TDocument']['overview'])?></td>
            <!-- <td class="tCenter"><span><?=implode("</span>、<span>",$val['TDocument']['tag'])?></span></td> -->
<!--
            <td class="p10x noClick lineCtrl">
              <div>
                <a href="javascript:void(0)" class="btn-shadow redBtn m10r10l fRight" onclick="event.stopPropagation(); removeAct('<?=$id?>')"><img src="/img/trash.png" alt="削除" width="30" height="30"></a>
              </div>
            </td>
 -->
          </tr>
        <?php endforeach; ?>
        <?php if ( count($documentList) === 0 ) :?>
          <td class="tCenter" colspan="5">保存された資料がありません</td>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div id="document-preview">
    <div id="document-base">
      <div id="document-preview-background"></div>
      <div id="document-preview-frame">
        <div id="document-preview-content" class="document_list">
          <!-- /* サイドバー */ -->
          <ul id="document_share_tools">
            <li-top></li-top>
            <li-bottom>
              <li ng-click="openDocumentList2()">
                <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_document.png" width="40" height="40" alt=""></span>
                <p>資料切り替え</p>
              </li>
              <li ng-click="closeDocumentList()">
                <span><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_disconnect.png" width="40" height="40" alt=""></span>
                <p>閉じる</p>
              </li>
            </li-bottom>
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
                <span id="scriptToggleBtn" class="btn on"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_talkscript.png" width="30" height="30" alt=""></span>
              </li>
              <li>
                <span id="pages"></span>
              </li>
            </li-left>
            <li-center>
              <li class="showDescriptionBottom" data-description="目次を開く" onclick="return false;">
                <span id="pageListToggleBtn" class="btn"><?=$this->Html->image("list.png", ['width'=>30, 'height'=>30, 'alt' => '目次']);?></span>
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
          <!-- /* 目次*/ -->
          <div id="slidesArea" style="top: -140px">
            <div id="slideList" style="">
            </div>
          </div>
          <!-- /* 目次*/ -->

          <!-- /* 原稿*/ -->
          <div id="manuscriptArea">
            <span id="manuscript"></span>
            <span id="manuscriptCloseBtn" onclick="slideJsApi2.toggleManuScript(); return false;"></span>
          </div>
          <!-- /* 原稿*/ -->

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
                          <img ng-src="{{::document.thumnail}}" ng-class="::setDocThumnailStyle(document)">
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
