<?php
$params = $excludeList['params'];
?>

<div id="tHeadDiv">
  <table>
    <thead>
      <th class="popupHistoryNo">No</th>
      <th class="popupHistoryUrl">ページタイトル</th>
      <th class="popupHistoryUrl">ページURL</th>
      <th class="popupHistoryTimer">滞在時間</th>
    </thead>
  </table>
</div>

<div id="tBodyDiv">
  <table>
    <tbody>
      <?php foreach($THistoryStayLog as $key => $val): ?>
        <?php
          $url = $this->App->trimToURL($params, $val['THistoryStayLog']['url']);
        ?>
        <tr>
          <td class="popupHistoryNo tCenter"><?=$key + 1?></td>
          <td class="popupHistoryUrl tLeft pre"><?=h($val['THistoryStayLog']['title']);?></td>
          <td class="popupHistoryUrl tLeft pre"><?=$this->Html->link($url, $url, array('target' => 'monitor', 'class' => 'underL'));?></td>
          <td class="popupHistoryTimer tRight"><?=h($val['THistoryStayLog']['stay_time'])?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>