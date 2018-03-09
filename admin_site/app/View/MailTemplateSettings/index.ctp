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
          <th style="width:2em;">いつ</th>
          <th style="width:20em;">メールタイトル</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach((array)$jobMailData as $key => $val): ?>
        <tr ondblclick= "location.href = '<?=$this->Html->url(array('controller' => 'MailTemplateSettings', 'action' => 'edit', $val['MJobMailTemplate']['id'],1,1))?>';">
          <td><?=h($val['MJobMailTemplate']['mail_type_cd'])?></td>
          <td><?=h($val['MJobMailTemplate']['value'])?>日後</td>
          <td><?= h($val['MJobMailTemplate']['subject'])?></td>
        </tr>
    <?php endforeach; ?>
    <?php foreach((array)$systemMailData as $key => $val): ?>
      <tr ondblclick= "location.href = '<?=$this->Html->url(array('controller' => 'MailTemplateSettings', 'action' => 'edit', $val['MSystemMailTemplate']['id'],2,2))?>';">
        <td><?=h($val['MSystemMailTemplate']['mail_type_cd'])?></td>
        <?php if($val['MSystemMailTemplate']['id'] == 1) { ?>
          <td>無料トライアル申込み後</td>
        <?php } else if($val['MSystemMailTemplate']['id'] == 4) { ?>
          <td>初期パスワード変更後</td>
        <?php } else if($val['MSystemMailTemplate']['id'] == 5) { ?>
          <td>契約申込み後</td>
        <?php } ?>
        <td><?= h($val['MSystemMailTemplate']['sender'])?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
