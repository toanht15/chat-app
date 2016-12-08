<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * MCompany Model
 *
 */
class MUser extends AppModel {

  public $name = 'MUser';
   /**
  * Validation rules
  *
  * @var array
  */
  //バリデーション
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
  ];

    //パスワードHASH化
  public function beforeSave($options = []) {
    if ( empty($this->data['MUser']) ) return true;

    $data = $this->data['MUser'];
    if ( !empty($data['password']) ) {
      $data['password'] = $this->makePassword($data['password']);
    }
    $this->data['MUser']['password'] = $data['password'];
    return true;
  }

  public function makePassword($str){
    $passwordHasher = new SimplePasswordHasher();
    return $passwordHasher->hash($str);
  }

    //メールアドレスチェック
  public function isUniqueChk($str){
    $str['MUser' . '.del_flg'] = 0;
    if ( !empty($this->id) ) {
      $str['MUser' . '.id !='] = $this->id;
    }


    $ret = $this->find('all', ['fields' => 'MUser' . '.*', 'conditions' => $str]);
    if ( !empty($ret) ) {
      return false;
    }
    else {
      return true;
    }
  }
}
