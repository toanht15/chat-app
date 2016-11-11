<?php
/**
 * MAdministratorsController controller.
 * ユーザーマスタ
 */

App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class MAdministrator extends AppModel {

  public $name = "MAdministrator";

  /**
  * Validation rules
  *
  * @var array
  */
  //登録処理の場合
  public $validate = [
    'user_name' => [
      'maxlength' => [
        'rule' => ['maxLength', 50],
        'allowEmpty' => false,
        'message' => 'ユーザー名は50文字以内で設定してください'
      ]
    ],
    'mail_address' => [
      'email' => [
        'rule' => 'email',
        'message' => 'メールアドレスの形式が不正です。'
      ],
      'isUniqueChk' => [
        'rule' => 'isUniqueChk',
        'message' => '既に登録されているアドレスです。'
      ]
    ],
    'new_password' => [
      'minLength' => [
        'rule' => ['between', 6, 12],
        'allowEmpty' => false,
        'message' => 'パスワードは６～１２文字の間で設定してください。'
      ],
      'alphaNumeric' => [
        'rule' => 'alphaNumeric',
        'message' => 'パスワードは英数字で設定してください。'
      ]
    ],
  ];

  //更新処理の場合
  public $updateValidate = [
    'user_name' => [
      'maxlength' => [
        'rule' => ['maxLength', 50],
        'allowEmpty' => false,
        'message' => 'ユーザー名は50文字以内で設定してください'
      ]
    ],
    'mail_address' => [
      'email' => [
        'rule' => 'email',
        'message' => 'メールアドレスの形式が不正です。'
      ]
    ],
    'new_password' => [
      'minLength' => [
        'rule' => ['between', 6, 12],
        'allowEmpty' => false,
        'message' => 'パスワードは６～１２文字の間で設定してください。'
      ],
      'alphaNumeric' => [
        'rule' => 'alphaNumeric',
        'message' => 'パスワードは英数字で設定してください。'
      ]
    ],
  ];

  //パスワードHASH化
  public function beforeSave($options = []) {
    if ( empty($this->data['MAdministrator']) ) return true;
    $data = $this->data['MAdministrator'];
    if ( !empty($data['new_password']) ) {
      $data['password'] = $this->makePassword($data['new_password']);
    }
    $this->data['MAdministrator'] = $data;
    return true;
  }

  public function makePassword($str){
    $passwordHasher = new SimplePasswordHasher();
    return $passwordHasher->hash($str);
  }

  //メールアドレスチェック
  public function isUniqueChk($str){
    $str['MAdministrator' . '.del_flg'] = 0;
    if ( !empty($this->id) ) {
      $str['MAdministrator' . '.id !='] = $this->id;
    }
    $ret = $this->find('all', ['fields' => 'MAdministrator' . '.*', 'conditions' => $str]);
    if ( !empty($ret) ) {
      return false;
    }
    else {
      return true;
    }
  }
}
?>