<div class="form01">
  <section>
    <ul class="settingList pl30">
      <!-- 資料名 -->
      <li>
        <span class="require"><label>資料名</label></span>
        <?= $this->Form->input('name', ['type' => 'text','placeholder' => '資料名','maxlength' => 30,'label' => false,'div' => false, 'error'=>false]) ?>
      </li>
      <?php if ( $this->Form->isFieldError('name') ) echo $this->Form->error('name', null, ['wrap' => 'li']); ?>
      <!-- 資料名 -->

      <!-- 概要 -->
      <li>
        <span><label>概要</label></span>
        <?=$this->Form->input('overview', ['type'=>'textarea','placeholder' => '概要','label' => false,'div' => false,'maxlength'=>300,'cols' => 25,'rows' => 5])?>
      </li>

      <!-- 資料 -->
      <li>
        <span class="require"><label>資料</label></span>
        <documentArea>
          <content>
            <preview>
              <?php if(empty($this->params['pass'][0])) { ?>
                <slideframe style="pointer-events: none;" >
                  <div id="document_canvas"></div>
                </slideframe>
              <?php }
              else { ?>
                <slideframe ng-click="openDocumentList3(<?=$this->params['pass'][0]?>)">
                  <div id="document_canvas"></div>
                </slideframe>
              <?php } ?>
              <paging>
                <span onclick="slideJsApi.prevPage(); return false;"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_back.png" width="30" height="30" alt=""></span>
                <span class="pages"></span>
                <span onclick="slideJsApi.nextPage(); return false;"><img src="<?=C_PATH_SYNC_TOOL_IMG?>icon_next.png" width="30" height="30" alt=""></span>
              </paging>
            </preview>
            <controller>
              <upload class="btn-shadow greenBtn">
                <?=$this->Form->input('files', ['type'=>'file','placeholder' => '資料', 'error' => false, 'label' => false,'div' => false])?>
                <?=$this->Form->input('file_name', ['type'=>'hidden'])?>
                <?=$this->Form->input('settings', ['type'=>'hidden'])?>
                <?=$this->Form->input('rotation', ['type'=>'hidden'])?>
              </upload>
              <rotate class="btn-shadow greenBtn" onclick="slideJsApi.rotate(); return false;">
                <?=$this->Html->image('rotate90_w.png', ['alt' => '90度資料を回転する']);?>
              </rotate>
            </controller>
          </content>
          <content>
            <p>原稿<span class="pages"></span></p>
            <textarea id="pages-text"></textarea>
            <?=$this->Form->hidden('manuscript')?>
          </content>
        </documentArea>
      </li>
      <?php if ( $this->Form->isFieldError('files') ) echo $this->Form->error('files', null, ['wrap' => 'li']); ?>
      <!-- 資料 -->

      <!-- ダウンロードフラグ -->
      <li>
        <span class="require"><label>ダウンロード</label></span>
        <label class="pointer">
          <?=$this->Form->input('download_flg',
          ['legend' => false,'type' => 'radio',
          'class' => 'pointer',
          'options' => $download,
          'separator' => '</label>&nbsp;<label class="pointer">',
          'default' => C_YES]); ?>
        </label>
      </li>
      <!-- ページ数フラグ -->
      <li>
        <span class="require"><label>ページ数表示</label></span>
        <label class="pointer">
          <?=$this->Form->input('pagenation_flg',
          ['legend' => false,'type' => 'radio',
          'class' => 'pointer',
          'options' => $pagenation,
          'separator' => '</label>&nbsp;<label class="pointer">',
          'default' => C_SELECT_CAN]); ?>
        </label>
      </li>
      <?php if ($this->params->action = 'add') { ?>
        <input type="text" style="display:block; position: fixed; top: -500px; left: -500px; z-index: 0;">
        <input type="password" style="display:block; position: fixed; top: -500px; left: -500px; z-index: 0;">
      <?php } ?>
      <!-- <li>
        <span><label>パスワード</label></span>
        <?= $this->Form->input('password', ['type' => 'password','placeholder' => 'パスワード','maxlength' => 30,'label' => false,'div' => false,'autocomplete' => 'off']) ?>
      </li> -->
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