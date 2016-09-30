<script type="text/javascript">
$(document).ready(function(){
  function customerInfoSave() {
    var dataList = {},
        customerId = document.getElementById('customerId').value;
        visitorsId = document.getElementById('visitorsId').value;
    $(".infoData").each(function(){
      dataList[$(this).data('key')] = this.value;
    });
    $.ajax({
      type: 'GET',
      url: "<?= $this->Html->url(array('controller' => 'Histories', 'action' => 'remoteSaveCustomerInfo')) ?>",
      data: {
        customerId: customerId,
        visitorsId: visitorsId,
        saveData: dataList
      },
      dataType: 'json',
      success: function(ret){
        location.href = location.href;
      }
    });
  }
  popupEvent.closePopup = function(){
    customerInfoSave();
    // popupEvent.close();
  };
});
</script>
<ul>
  <li>
    <p><span>ユーザーID</span></p>
    <span><?=$data['THistory']['visitors_id']?></span>
  </li>
  <li>
    <p><span>訪問回数</span></p>
    <span><?=$data['THistoryCount']['cnt']?> 回</span>
  </li>
  <li>
    <p><span>ユーザーエージェント</span></p>
    <span><?=$data['THistory']['user_agent']?></span>
  </li>
  <?php foreach($infoList as $key => $val): ?>
    <li>
      <p><span><?=$val?></span></p>
      <span>
      <?php
        $val = "";
        if( isset($data['informations'][$key]) ) {
          $val = $data['informations'][$key];
        }

        if ( strcmp($key, 'memo') === 0 ) {
          echo $this->Form->textarea($key, ['rows'=>5, 'cols'=>25, 'class'=>'infoData', 'value'=>$val, 'data-key'=>$key]);
        } else {
          echo $this->Form->input($key, ['type'=>'text', 'class'=>'infoData', 'value'=>$val, 'data-key'=>$key, 'label' => false, 'div'=> false]);
        }
      ?>
      </span>
    </li>
  <?php endforeach; ?>
</ul>
<?= $this->Form->input('visitorsId', ['type'=>'hidden', 'value'=>$data['THistory']['visitors_id'], 'label' => false, 'div'=> false]); ?>
<?php
$customerId = "";
if ( isset($data['MCustomer']['id']) ) {
  $customerId = $data['MCustomer']['id'];
}
echo $this->Form->input('customerId', ['type'=>'hidden', 'value' => $customerId, 'label' => false, 'div'=> false]);
?>
