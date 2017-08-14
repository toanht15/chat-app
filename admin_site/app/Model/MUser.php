<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * MCompany Model
 *
 */
class MUser extends AppModel {

  public $name = 'MUser';

   //アソシエーション
  public $belongsTo = ['MCompany' =>
    ['className' => 'M_company',
      'conditions' => '',
      'order' => '',
      'dependent' => true,
      'foreignKey' => 'm_companies_id'
    ]
  ];
   /**
  * Validation rules
  *
  * @var array
  */
 public $validate = [
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
    'admin_password' => [
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

  public function beforeSave($options = []) {
    if ( empty($this->data['MUser']) ) return true;
    $data = $this->data['MUser'];
    if ( !empty($data['new_password']) ) {
      $data['password'] = $this->makePassword($data['new_password']);
    }
    $this->data['MUser'] = $data;
    return true;
  }

  public function passwordHash($pass) {
    if ( !empty($pass) ) {
      $data = $this->makePassword($pass);
    }
    $password = $data;
    return $password;
  }

  public function makePassword($str){
    $passwordHasher = new SimplePasswordHasher();
    return $passwordHasher->hash($str);
  }

   //メールアドレスチェック
  public function isUniqueChk($mail){
    $conditions['mail_address'] = $mail['mail_address'];
    $conditions['MUser' . '.del_flg'] = 0;
    if ( !empty($this->data['MAgreement']['m_users_id']) ) {
      $conditions['MUser' . '.id !='] = $this->data['MAgreement']['m_users_id'];
    }
    $ret = $this->find('all', ['fields' => 'MUser' . '.*', 'conditions' => $conditions]);
    if ( !empty($ret) ) {
      return false;
    }
    else {
      return true;
    }
  }
}
