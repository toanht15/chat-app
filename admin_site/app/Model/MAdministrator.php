<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class MAdministrator extends AppModel {
   public $name = "MAdministrator";

   public $validate = [
    'mail_address' => [
      'rule' => 'isUnique',
      'message' => 'すでに使用されている名前です。'
    ],
    'password' => [
      [
        'rule' => 'alphaNumeric',
        'message' => 'パスワードは半角英数字にしてください'
      ],
    ]
  ];

  //データーベース保存前に自動的にパスワードを暗号化(BlowfishPasswordHasherクラス)
  public function beforeSave($options = []) {
    if (isset($this->data[$this->alias]['password'])) {
      $passwordHasher = new SimplePasswordHasher();
      $this->data[$this->alias]['password'] = $passwordHasher->hash(
      $this->data[$this->alias]['password']
      );
    }
    return true;
  }
}
?>