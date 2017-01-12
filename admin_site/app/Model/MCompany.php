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
      ],
      'wheels' => [
        'rule'    => 'naturalNumber',
        'message' => '数値を入力してください',
        'allowEmpty' => true             // 空白許可
      ]
    ]
  ];

    //サイトキーチェック
  public function isUniqueChkKey($siteKey){
    $conditions['company_key'] = $siteKey['company_key'];
    $conditions['MCompany' . '.del_flg'] = 0;
    if ( !empty($this->data['MAgreement']['m_companies_id']) ) {
      $conditions['MCompany' . '.id !='] = $this->data['MAgreement']['m_companies_id'];
    }
    $ret = $this->find('all', ['fields' => 'MCompany' . '.*', 'conditions' => $conditions]);
    if ( !empty($ret) ) {
      return false;
    }
    else {
      return true;
    }
  }

}
