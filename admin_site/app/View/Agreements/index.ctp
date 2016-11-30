<script type = "text/javascript">

function saveFile(){
  location.href = "<?=$this->Html->url(array('controller' => 'Agreements', 'action' => 'add'))?>";
}

function createPassword(){
  var str = random();
  $('#AgreementPassword').val(str);
}

</script>

<?= $this->Form->create('Agreement', array('type' => 'post', 'url' => array('controller' => 'Agreements', 'action' => 'index'))); ?>
  <div class="form01">
    <section>
      <?= $this->Form->input('id', array('type' => 'hidden')); ?>
      <ul class='formArea'>
        <!-- /* パスワード自動生成 */ -->
        <li>
          <div class="labelArea fLeft"><span class="require"><label>パスワード</label></span></div>
          <?= $this->Form->input('password', array('type' => 'textbox', 'placeholder' => 'パスワード', 'div' => false, 'label' => false, 'maxlength' => 50)) ?>
          <?php echo $this->Html->link(
            '自動生成',
            'javascript:void(0)',
            array('escape' => false, 'id' => 'searchRefine','class' => 'action_btn','onclick' => 'createPassword()'));
          ?>
        </li>
        <!-- /* jsファイル作成 */ -->
        <li>
          <section>
            <div>
              <?= $this->Html->link('更新', 'javascript:void(0)', ['onclick' => 'saveFile()','class' => 'action_btn','id'=>'button']) ?>
            </div>
          </section>
        </li>
      </ul>
    </section>
  </div>
<?= $this->Form->end(); ?>