<div class="form01">
  <section>
    <ul class="settingList pl30">
      <!-- 資料名 -->
      <li>
        <span class="require"><label>資料名</label></span>
        <?= $this->Form->input('name', ['type' => 'text','placeholder' => '資料名','maxlength' => 30,'label' => false,'div' => false,]) ?>
      </li>
      <!-- 概要 -->
      <li>
        <span>
          <label class="require">概要</label>
        </span>
        <?=$this->Form->input('overview', ['type'=>'textarea','placeholder' => '概要','label' => false,'div' => false,'maxlength'=>300,'cols' => 25,'rows' => 5])?>
      </li>
      <!-- タグ -->
      <li>
        <span>
          <label>タグ</label>
        </span>
        <?= $this->Form->input('new_tag', ['type' => 'text','placeholder' => '新しいタグ','maxlength' => 30,'label' => false,'div' => false]) ?>
        <?= $this->Html->image('add.png', array('alt' => '登録', 'class' => 'btn-shadow greenBtn', 'width' => 22, 'height' => 22, 'onclick' => 'tagAdd()')) ?>
      </li>

      <li>
        <span></span>
        <?= $this->Form->input('tag', array('type' => 'select','multiple' => true, 'label' => false,'id' => 'labelHideList','options' => $labelHideList)); ?>
      </li>
      <!-- ダウンロードフラグ -->
      <li>
        <span class="require"><label>ダウンロード</label></span>
        <?=
        $this->Form->input('download_flg', ['legend' => false,'type' => 'radio','options' => $radio,'default' => C_YES]); ?>
      </li>
      <!-- ページ数フラグ -->
      <li>
        <span class="require"><label>ページ数表示</label></span>
        <?=
        $this->Form->input('pagenation_flg', ['legend' => false,'type' => 'radio','options' => $radio2,'default' => C_SELECT_CAN]); ?>
      </li>
      <?php if ($this->params->action = 'add') { ?>
        <input type="text" style="display:block; position: fixed; top: -500px; left: -500px; z-index: 0;">
        <input type="password" style="display:block; position: fixed; top: -500px; left: -500px; z-index: 0;">
      <?php } ?>
      <li>
        <span><label>パスワード</label></span>
        <?= $this->Form->input('password', ['type' => 'password','placeholder' => 'パスワード','maxlength' => 30,'label' => false,'div' => false,'autocomplete' => 'off']) ?>
      </li>
    </ul>
  </section>

  <section>
    <?=$this->Form->hidden('id')?>
    <div id="tautomessages_actions" class="fotterBtnArea">
      <?=$this->Html->link('戻る','/TDocuments/index', ['class'=>'whiteBtn btn-shadow'])?>
      <a href="javascript:void(0)"  onclick="saveAct()" class="greenBtn btn-shadow">保存</a>
      <?php
        $class = "";
        if ( empty($this->data['TDocument']['id']) ) {
          $class = "vHidden";
        }
      ?>
      <a href="javascript:void(0)" onclick="removeActEdit()" class="redBtn btn-shadow <?=$class?>">削除</a>
    </div>
  </section>
</div>
