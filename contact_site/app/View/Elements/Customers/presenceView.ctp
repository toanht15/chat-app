<div id="presenceView">
  <div id="presenceViewPopHeader" class="noSelect">
    <h2>オペレータステータス一覧</h2>
    <div>
      <!-- 閉じる -->
      <a href="javascript:void(0)" ng-click="closeOperatorPresence()" class="fRight customer_detail_btn redBtn btn-shadow">
        <?= $this->Html->image('close.png', ['alt'=>'チャットを終了する', 'width'=>20, 'height' => 20, 'ng-if="chatList.indexOf(detailId) < 0"']); ?>
        <?= $this->Html->image('minimize.png', ['alt'=>'詳細を閉じる', 'width'=>20, 'height' => 20, 'ng-if="chatList.indexOf(detailId) >= 0"']); ?>
      </a>
      <!-- 閉じる -->
    </div>
  </div>
  <div id="presenceViewContents">
    <div id="presenceTableWrap">
      <div id="presenceViewheader" fixed-header>
        <table>
          <thead>
          <tr>
            <th id="statusHeader" ng-click="changeStatusSortMode()">
              状態
              <i class="fa fa-sort" aria-hidden="true" ng-if="operatorListSortMode === 'displayName'"></i>
              <i class="fa fa-sort-asc" aria-hidden="true" ng-if="operatorListSortMode === 'status' && operatorListSortOrder === 'asc'"></i>
              <i class="fa fa-sort-desc" aria-hidden="true" ng-if="operatorListSortMode === 'status' && operatorListSortOrder === 'desc'"></i>
            </th>
            <th id="displayNameHeader" ng-click="changeDisplaySortMode()">
              表示名
              <i class="fa fa-sort" aria-hidden="true" ng-if="operatorListSortMode === 'status'"></i>
              <i class="fa fa-sort-asc" aria-hidden="true" ng-if="operatorListSortMode === 'displayName' && operatorListSortOrder === 'asc'"></i>
              <i class="fa fa-sort-desc" aria-hidden="true" ng-if="operatorListSortMode === 'displayName' && operatorListSortOrder === 'desc'"></i>
            </th>
          </tr>
          </thead>
        </table>
      </div>
      <div id="presenceViewBodyScroll">
        <div id="presenceViewBody">
          <table>
            <tbody ng-cloak>
            <tr class="tableRow" ng-repeat="operator in operatorList | orderOperatorStatus : this">
              <td class="tableData operatorStatus">
                <div class="statusWrap" ng-if="operator.status === 1">
                  <i class="fa fa-check-circle presence-active icon" aria-hidden="true"></i>
                  <span class="presence-active">待機中</span>
                </div>
                <div class="statusWrap" ng-if="operator.status === 0">
                  <i class="fa fa-minus-circle presence-inactive icon" aria-hidden="true"></i>
                  <span class="presence-inactive">離席中</span>
                </div>
                <div class="statusWrap" ng-if="isUndefined(operator.status)">
                  <i class="fa fa-times-circle presence-offline icon" aria-hidden="true"></i>
                  <span class="presence-offline">オフライン</span>
                </div>
              </td>
              <td class="tableData displayName"><span class="displayNameSpace">{{operator.display_name}}</td>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  <!--
  // OS判定
  $(function() {
    if (navigator.userAgent.indexOf('Mac') > 0){
      $('#statusHeader').addClass('mac');
    }
    if (navigator.userAgent.indexOf('Windows NT') > 0){
      $('.displayNameSpace').css('padding-left', '1em');
    }
  });
  //-->
</script>