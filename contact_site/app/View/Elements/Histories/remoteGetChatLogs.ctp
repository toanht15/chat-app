<ul>
<?php foreach($THistoryChatLog as $key => $val): ?>
  <?php
    $className = "sinclo_se";
    $name = "";
    if ( strcmp($val['THistoryChatLog']['message_type'], 1) === 0 ) {
        $className = "sinclo_re";
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 2) === 0 && !empty($val['MUser']['display_name'])) {
        $name = $val['MUser']['display_name'] . "さん";
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 3) === 0 ) {
      $className = "sinclo_auto";
      $name = "自動応答";
    }
  ?>
  <li class="<?=$className?>"><span><?=$name?></span><?=$this->htmlEx->makeChatView(h($val['THistoryChatLog']['message']))?></li>
<?php endforeach; ?>
</ul>
