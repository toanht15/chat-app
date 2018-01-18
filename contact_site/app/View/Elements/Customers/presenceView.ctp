<div id="presenceView">
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
          for ($i=0; $i<50; $i++) {
            echo '<tr class="tableRow" >';
            echo '  <td class="tableData" style="width: 70%">' . $user['display_name'] . '</td>';
            echo '  <td class="tableData" style="width: 30%">';
            echo '    <span class="presence-active" id="active' . $user['id'] . '" style="display:none">待機中</span>';
            echo '    <span class="presence-inactive" id="inactive' . $user['id'] . '" style="display:none">離席中</span>';
            echo '    <span class="presence-offline" id="offline' . $user['id'] . '">オフライン</span>';
            echo '  </td>';
            echo '</tr>';
          }
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>