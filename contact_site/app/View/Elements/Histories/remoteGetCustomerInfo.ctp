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
  <?php foreach($data['informations'] as $key => $val): ?>
    <?php if( isset($notification[$key]) ): ?>
      <li>
        <p><span><?=$notification[$key]?></span></p>
        <span class="pre"><?=$val?></span>
      </li>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>
