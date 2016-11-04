<script type="text/javascript">
function sendadd()    {
  console.log('eeee');
 document.getElementById('MAdministratorIndexForm').submit();
}
</script>
<?= $this->Form->create('MAdministrator'); ?>
<?= $this->Form->input('mail_address',[
  'label' => 'ﾒｰﾙｱﾄﾞﾚｽ',
]); ?>
<?= $this->Form->input('password', [
  'label' => 'パスワード',
]); ?>

<?= $this->Form->button('追加する', [
  'class'=>'add-submit',
  'type'=> 'button',
  'onClick'=> 'sendadd()',
  'name' => 'add'
]); ?>