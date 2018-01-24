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
            <th style="width: 70%">表示名</th>
            <th style="width: 30%">状態</th>
          </tr>
          </thead>
        </table>
      </div>
      <div id="presenceViewBodyScroll">
        <div id="presenceViewBody">
          <table>
            <tbody>
            <?php
            foreach($userList as $index => $user) {
  //            for($i = 0; $i<100; $i++) { for design fix
                echo '<tr class="tableRow" >';
                echo '  <td class="tableData" style="width: 70%">' . $user['display_name'] . '</td>';
                echo '  <td class="tableData" style="width: 30%">';
                echo '    <span class="presence-active" id="active' . $user['id'] . '" style="display:none">待機中</span>';
                echo '    <span class="presence-inactive" id="inactive' . $user['id'] . '" style="display:none">離席中</span>';
                echo '    <span class="presence-offline" id="offline' . $user['id'] . '">オフライン</span>';
                echo '  </td>';
                echo '</tr>';
              }
  //          } for design fix
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>