<?php if ($notSupportBrowser) { ?>
<pre style="font-size: 13px">
<b>対応していないブラウザです。
下記を参考に、ブラウザを選定してください。</b>


<b>対応ブラウザ</b>
- Google Chrome 49.0以上
- Mozila FireFox 45.0以上
- Internet Explorer 10.0以上
</pre>
<?php
} else {
echo $this->Form->create('MUser', array('url' => '/Login/login', 'id' => 'MUserIndexForm'));
echo $this->Form->input('mail_address', array('label' => false, 'placeholder' => 'Mail Address'));
echo $this->Form->input('password', array('label' => false, 'placeholder' => 'Password', 'id' => 'MUserPasswordInput'));
echo $this->Html->link('Sign In','javascript:void(0)', array('id' => 'MUserFormButton'));
echo $this->Form->end();
}
?>
