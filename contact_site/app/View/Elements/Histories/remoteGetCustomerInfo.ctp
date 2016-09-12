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
  <?php foreach($notification as $key => $val): ?>
    <li>
      <p><span><?=$val?></span></p>
      <span class="pre"><?php if( isset($data['informations'][$key]) ) { echo $data['informations'][$key]; } ?></span>
    </li>
  <?php endforeach; ?>
</ul>
