<div id="presenceView">
  <div id="presenceViewheader">
    <table>
      <thead>
      <tr>
        <th style="width: 50%">表示名</th>
        <th style="width: 50%">状態</th>
      </tr>
      </thead>
    </table>
  </div>
  <div id="presenceViewBody">
    <table>
      <thead>
      <tr>
        <th style="width: 50%">表示名</th>
        <th style="width: 50%">状態</th>
      </tr>
      </thead>
      <tbody>
      <?php
      foreach($userList as $index => $user) {
        echo '<tr class="tableRow">';
        echo '  <td class="tableData">' . $user['display_name'] . '</td>';
        echo '  <td class="tableData">';
        echo '    <span class="presence-active" id="active'. $user['id'] .'" style="display:none">待機中</span>';
        echo '    <span class="presence-inactive" id="inactive'. $user['id'] .'" style="display:none">離席中</span>';
        echo '    <span class="presence-offline" id="offline'. $user['id'] .'">オフライン</span>';
        echo '  </td>';
        echo '</tr>';
      }
      ?>
      </tbody>
    </table>
  </div>
</div>