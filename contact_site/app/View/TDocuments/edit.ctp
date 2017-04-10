<?php echo $this->Html->script("jquery-ui.min.js"); ?>
<?php echo $this->element('TDocuments/script'); ?>
<script type = text/javascript>

$(document).on("keydown", "#scaleType", function(e){ return false; });
var sincloApp = angular.module('sincloApp', []);
sincloApp.controller('MainCtrl', function($scope){
  $scope.documentList = [];
  $scope.tagList = {};
  $scope.searchName = "";
  $scope.selectList = {};
  $scope.searchFunc = function(documentList){
    var targetTagNum = Object.keys($scope.selectList).length;

    function check(elem, index, array){
      var flg = true;
      if ( elem.tag !== "" && elem.tag !== null ) {
        elem.tags = $scope.jParse(elem.tag);
      }
      if ( $scope.searchName === "" && targetTagNum === 0 ) {
        return elem;
      }

      if ( $scope.searchName !== "" && (elem.name + elem.overview).indexOf($scope.searchName) < 0 ) {
        flg = false;
      }

      if ( flg && targetTagNum > 0 ) {
        var selectList = Object.keys($scope.selectList);
        flg = true;
        for ( var i = 0; selectList.length > i; i++ ) {
          if ( elem.tags.indexOf(Number(selectList[i])) === -1 ) {
            flg = false;
          }
        }
      }

      return ( flg ) ? elem : false;

    }

    return documentList.filter(check);
  };

  /**
   * openDocumentList
   *  ドキュメントリストの取得
   * @return void(0)
   */
  $scope.openDocumentList3 = function(id) {
    $.ajax({
      type: 'post',
      data: {
        id:id
      },
      url: '<?=$this->Html->url(["controller" => "TDocuments", "action" => "remoteOpenDocumentLists"])?>',
      dataType: 'json',
      success: function(json) {
        doc = JSON.parse(json.documentList)[0];
        console.log(doc);
        $("#ang-popup3").addClass("show");
        $scope.searchName = "";
        var contHeight = $('#ang-popup-content3').height();
        $('#ang-popup-frame3').css('height', contHeight);
        $scope.tagList = ( json.hasOwnProperty('tagList') ) ? JSON.parse(json.tagList) : {};
        $scope.documentList = ( json.hasOwnProperty('documentList') ) ? JSON.parse(json.documentList) : {};
        $scope.$apply();
        slideJsApi2.readFile(doc,"document_canvas",function(err) {
          if (err) return false;
          var settings = JSON.parse(doc.settings);
        });
      }
    });
  };

  $scope.openDocumentList2 = function() {
    $.ajax({
      type: 'GET',
      url: '<?=$this->Html->url(["controller" => "Customers", "action" => "remoteOpenDocumentLists"])?>',
      dataType: 'json',
      success: function(json) {
        doc = JSON.parse(json.documentList)[0];
        $("#ang-popup2").addClass("show");
        $scope.searchName = "";
        var contHeight = $('#ang-popup-content2').height();
        $('#ang-popup-frame2').css('height', contHeight);
        $scope.tagList = ( json.hasOwnProperty('tagList') ) ? JSON.parse(json.tagList) : {};
        $scope.documentList = ( json.hasOwnProperty('documentList') ) ? JSON.parse(json.documentList) : {};
        $scope.$apply();
      }
    });
  };
  /**
   * [shareDocument description]
   * @param  {object} doc documentInfo
   * @return {void}     open new Window.
   */
  $scope.shareDocument = function(doc) {
    var targetTabId = tabId.replace("_frame", "");
    sessionStorage.removeItem('doc');
    window.open(
      "<?= $this->Html->url(['controller' => 'Customers', 'action' => 'docFrame']) ?>?tabInfo=" + encodeURIComponent(targetTabId) + "&docId=" + doc.id,
      "doc_monitor_" + targetTabId,
      "width=480,height=400,dialog=no,toolbar=no,location=no,status=no,menubar=no,directories=no,resizable=no, scrollbars=no"
    );
    $scope.closeDocumentList();
  };

  /**
   * [changeDocument description]
   * @param  {object} doc document's info
   * @return {void}     send new docURL
   */
    $scope.changeDocument = function(doc){
    console.log('change');
    console.log(doc);
    sessionStorage.setItem('page', 1);
    sessionStorage.setItem('scale', 1);
    slideJsApi2.readFile(doc, function(err) {
      if (err) return false;
      var settings = JSON.parse(doc.settings);
    });
    $scope.closeDocumentList2();
  };

  $scope.closeDocumentList = function() {
    var scroll_event = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';
    $(document).off(scroll_event);
    $("#ang-popup3").removeClass("show");
  };

  $scope.closeDocumentList2 = function() {
    $("#ang-popup2").removeClass("show");
  };
});

window.onload = function(){
  $("#manuscriptArea").draggable({
    scroll: false,
    containment: "slideframea",
    cancel: "#document_canvas"
  })
  .css({
    'display': 'block',
    'position': 'relative',
    'width': "calc(100% - 150px)",
    'left': "125px",
    'top': "4em"
  });
};
</script>

<section id="document_share" ng-app="sincloApp" ng-controller="MainCtrl">
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