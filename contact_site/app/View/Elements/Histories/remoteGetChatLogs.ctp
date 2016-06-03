<ul>
<?php foreach($THistoryChatLog as $key => $val): ?>
  <?php
    $className = "sinclo_se";
    if ( strcmp($val['THistoryChatLog']['message_type'], 1) === 0 ) {
        $className = "sinclo_re";
    }
  ?>
  <li class="<?=$className?>"><?=h($val['THistoryChatLog']['message'])?></li>
<?php endforeach; ?>
</ul>
