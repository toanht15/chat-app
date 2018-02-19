<div id='agreementList_idx'>
  <div id='agreementList_add_title'>
    <div class="fLeft"><i class="fa fa-home fa-2x" aria-hidden="true"></i></div>
    <h1>メール設定</h1>
  </div>
  <?= $this->Html->link('登録',['controller'=>'MailTemplateSettings', 'action' => 'add'],['escape' => false, 'id' => 'searchRefine','class' => 'action_btn']); ?>
  <div id='agreementList_list' class="p20trl">
    <table>
      <thead>
        <tr>
          <th style="width:7em;">名称</th>
          <th style="width:2em;">何日後</th>
          <th style="width:2em;">何時</th>
          <th style="width:20em;">メールタイトル</th>
        </tr>
      </thead>
      <?php foreach((array)$mailInfo as $key => $val): ?>
        <tbody>
          <tr ondblclick= "location.href = '<?=$this->Html->url(array('controller' => 'MailTemplateSettings', 'action' => 'edit', $val['MJobMailTemplate']['id']))?>';">
            <td><?=h($val['MJobMailTemplate']['mail_type_cd'])?></td>
            <td><?=h($val['MJobMailTemplate']['days_after'])?>日後</td>
            <td><?= h($val['MJobMailTemplate']['time'])?>時</td>
            <td><?= h($val['MJobMailTemplate']['subject'])?></td>
          </tr>
      </tbody>
    <?php endforeach; ?>
  </table>
  </div>
</div>
