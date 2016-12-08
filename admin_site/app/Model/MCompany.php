<?php
App::uses('AppModel', 'Model');
/**
 * MCompany Model
 *
 */
class MCompany extends AppModel {

  public $name = 'MCompany';
  public $validate = [
  'company_name' => [
      'maxlength' => [
        'rule' => ['maxLength', 200],
        'allowEmpty' => false,
        'message' => '会社名を200文字以内で設定してください'
      ]
    ],
    'company_key' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => 'サイトキーを100文字以内で設定してください'
      ],
      'isUniqueChkKey' => [
        'rule' => 'isUniqueChkKey',
        'message' => '既に登録されているサイトキーです。'
      ]
    ],
    'limit_users' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '契約ID数を設定してください'
      ]
    ],
    ];

    //サイトキーチェック
  public function isUniqueChkKey($str){
    $str['MCompany' . '.del_flg'] = 0;
    if ( !empty($this->id) ) {
      $str['MCompany' . '.id !='] = $this->id;
    }
    $ret = $this->find('all', ['fields' => 'MCompany' . '.*', 'conditions' => $str]);
    if ( !empty($ret) ) {
      return false;
    }
    else {
      return true;
    }
  }

}
