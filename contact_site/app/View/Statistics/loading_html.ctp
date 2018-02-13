<script type="text/javascript">

loading.load.start();

<?php
  //各項目別ウィンドウの場合
  if(!empty($item)) { ?>
    window.onload = function() {
      location.href = "<?= htmlspecialchars_decode($this->Html->url(array('controller'=>'Statistics', 'action' => 'baseForAnotherWindow',
      '?'=>array('item'=>$item,'type'=>$timeType,'target'=>$dateType))),ENT_QUOTES) ?>";
    }
  <?php
  }
  //各オペレータ別ウィンドウの場合
  else if(!empty($userId)) { ?>
    window.onload = function() {
      location.href = "<?= htmlspecialchars_decode($this->Html->url(array('controller'=>'Statistics', 'action' => 'baseForAnotherWindow',
      '?'=>array('id'=>$userId,'type'=>$timeType,'target'=>$dateType))),ENT_QUOTES) ?>";
    }
  <?php
  }
?>
</script>