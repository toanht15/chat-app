<ul>
<?php foreach($THistoryChatLog as $key => $val): ?>
  <?php
    $className = "";
    $name = "";
    $message = "";
    if ( strcmp($val['THistoryChatLog']['message_type'], 1) === 0 ) {
      $className = "sinclo_re";
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 2) === 0 && !empty($val['MUser']['display_name'])) {
      $className = "sinclo_se";
      $name = $val['MUser']['display_name'];
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 3) === 0 ) {
      $className = "sinclo_auto";
      $name = "自動応答";
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 98) === 0 ) {
      $className = "sinclo_etc";
      $message = "- ". $val['MUser']['display_name'] . "が入室しました -";
    }
    else if ( strcmp($val['THistoryChatLog']['message_type'], 99) === 0 ) {
      $className = "sinclo_etc";
      $message = "- ". $val['MUser']['display_name'] . "が退室しました -";
    }

  ?>
  <?php if ( intval($val['THistoryChatLog']['message_type']) < 90 ) { ?>
    <li class="<?=$className?>"><span><?= $this->Time->format($val['THistoryChatLog']['created'], "%Y/%m/%d %H:%M:%S")?></span><span><?=$name?></span><?=$this->htmlEx->makeChatView(h($val['THistoryChatLog']['message']))?></li>
  <?php } else { ?>
    <li class="<?=$className?>"><span><?= $this->Time->format($val['THistoryChatLog']['created'], "%Y/%m/%d %H:%M:%S")?></span><?=$message?></li>
  <?php } ?>
<?php endforeach; ?>
</ul>
