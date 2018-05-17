<?= $this->element('ScriptSettings/menuBox'); ?>
<!--/menubox-->

<div id="contents">

<div class="inner">

<div id="main">

<section>
<div id="detail" style="margin:15px 10px">
<section class="list">
   <p style = "margin-left:0px;">フォーム内容が表示されます。<br>このページは、同期対象外の為、専用タグを設置しておりません。<br><br>フォームの入力以降のページには専用タグを設置しないようにお願いします。</p>
  <br>
</section>
  <br>
<section class="list">
  <ul>
    <li><label>お名前</label>：<?php if ( isset($data['name']) ) { echo h($data['name']); } ?></li>
    <li><label>メールアドレス</label>：<?php if ( isset($data['mail']) ) { echo h($data['mail']); } ?></li>
    <li><label>性別</label>：<?php if ( isset($data['sexes']) && isset($optList['sexes'][$data['sexes']]) ) { echo h($optList['sexes'][$data['sexes']]); } ?></li>
    <li><label>年齢</label>：<?php if ( isset($data['old']) ) { echo h($data['old']); } ?></li>
    <li><label>ご住所（都道府県）</label>：<?php if ( isset($data['prefectures']) ) { echo h($data['prefectures']); } ?></li>
    <li><label>ご住所（市区町村以下）</label>：<?php if ( isset($data['address']) ) { echo h($data['address']); } ?></li>
    <li><label>お問い合わせ項目</label>： <?php
      if ( !empty($data['inquiry']) ) {
        foreach((array)$data['inquiry'] as $key => $val) {
          if ( $key > 0 ) { echo " / "; }
          if ( isset($optList['inquiry'][$val]) ) { echo h($optList['inquiry'][$val]); }
        }
      }
    ?></li>
    <li><label>お問い合わせ詳細</label>：<?php if ( isset($data['detail']) ) { echo h($data['detail']); } ?></li>
  </ul>
</section>
</div>

</section>

</div>
<!--/main-->

<?= $this->element('ScriptSettings/subMenu'); ?>

</div>
<!--/inner-->

</div>
<!--/contents-->

<?= $this->element('ScriptSettings/footer'); ?>