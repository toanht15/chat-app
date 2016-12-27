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
        'message' => 'パスワードは半角英数字で設定してください。'
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
        'message' => 'パスワードは半角英数字で設定してください。'
      ]
    ],
    'current_password' => [
      'checkCurrentPw' => [
        'rule' => 'isCurrentPw',
        'allowEmpty' => false,
        'message' => '現在のパスワードが一致しません。'
      ]
    ],
    'confirm_password' => [
      'checkConfirmPw' => [
        'rule' => 'canMatchConfirmPw',
        'allowEmpty' => false,
        'message' => '新しいパスワードが一致しません。'
      ]
    ]
  ];

  //現在のパスワードチェック
  public function isCurrentPw($currentPw){
    $data = $this->data['MAdministrator'];
    if ( empty($currentPw['current_password']) ) return false;

    $params = [
      'fields' => '*',
      'conditions' => [
        'id' => $data['id'],
        'del_flg' => 0,
        'password' => $this->makePassword($currentPw['current_password'])
      ],
      'limit' => 1
    ];
    $ret = $this->find('all', $params);
    if ( !empty($ret) ) {
      return true;
    }
    return false;
  }

  //新しいパスワードチェック
  public function canMatchConfirmPw(){
    $data = $this->data['MAdministrator'];
    if ( !empty($data['new_password']) && !empty($data['confirm_password']) ) {
      if ( strcmp($data['new_password'], $data['confirm_password']) === 0 ) {
        return true;
      }
    }
    return false;
  }

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