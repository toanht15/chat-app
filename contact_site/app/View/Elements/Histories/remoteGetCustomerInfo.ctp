<dl>
  <dt>ユーザーID</dt>
  <dd><?=$data['THistory']['visitors_id']?></dd>
  <dt>訪問回数</dt>
  <dd><?=$data['THistoryCount']['cnt']?> 回</dd>
  <dt>ユーザーエージェント</dt>
  <dd><?=$data['THistory']['user_agent']?></dd>
  <?php foreach($data['informations'] as $key => $val): ?>
    <?php if( isset($notification[$key]) ): ?>
      <dt><?=$notification[$key]?></dt>
      <dd class="pre"><?=$val?></dd>
    <?php endif; ?>
  <?php endforeach; ?>
</dl>
